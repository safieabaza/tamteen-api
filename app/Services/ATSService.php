<?php

namespace App\Services;

use App\Models\ATSKeyword;
use App\Models\CV;

class ATSService
{
    public function analyze(CV $cv): array
    {
        $cvText = $this->extractText($cv);
        $keywords = ATSKeyword::all();

        $keywordResult   = $this->scoreKeywords($cvText, $keywords);
        $formattingScore = $this->scoreFormatting($cv);
        $qualityScore    = $this->scoreContentQuality($cv, $cvText);
        $completenessScore = $this->scoreCompleteness($cv);

        $totalScore = round(
            $keywordResult['score'] + $formattingScore['score'] +
            $qualityScore['score'] + $completenessScore['score'],
            1
        );

        return [
            'ats_score'        => min(100, $totalScore),
            'matched_keywords' => $keywordResult['matched'],
            'missing_keywords' => $keywordResult['missing'],
            'recommendations'  => $this->buildRecommendations(
                $keywordResult,
                $formattingScore,
                $qualityScore,
                $completenessScore
            ),
            'score_breakdown'  => [
                'keyword_match'   => ['score' => $keywordResult['score'], 'max' => 40],
                'formatting'      => ['score' => $formattingScore['score'], 'max' => 20],
                'content_quality' => ['score' => $qualityScore['score'], 'max' => 20],
                'completeness'    => ['score' => $completenessScore['score'], 'max' => 20],
            ],
        ];
    }

    private function extractText(CV $cv): string
    {
        $parts = [];

        if ($cv->summary) {
            $parts[] = $cv->summary;
        }

        if ($cv->experience) {
            foreach ($cv->experience as $exp) {
                foreach (['title', 'company', 'description', 'responsibilities'] as $field) {
                    if (!empty($exp[$field])) {
                        $parts[] = is_array($exp[$field])
                            ? implode(' ', $exp[$field])
                            : $exp[$field];
                    }
                }
            }
        }

        if ($cv->education) {
            foreach ($cv->education as $edu) {
                foreach (['degree', 'institution', 'field', 'description'] as $field) {
                    if (!empty($edu[$field])) {
                        $parts[] = $edu[$field];
                    }
                }
            }
        }

        if ($cv->skills) {
            foreach ($cv->skills as $skill) {
                $parts[] = is_string($skill) ? $skill : ($skill['name'] ?? '');
            }
        }

        return strtolower(implode(' ', $parts));
    }

    private function scoreKeywords(string $text, $keywords): array
    {
        $matched = [];
        $missing = [];
        $matchedWeight = 0;
        $totalWeight = 0;

        foreach ($keywords as $kw) {
            $totalWeight += $kw->weight;
            $keyword = strtolower($kw->keyword);

            // Use word-boundary matching to avoid "java" matching inside "javascript"
            $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
            $occurrences = preg_match_all($pattern, $text);

            if ($occurrences > 0) {
                $multiplier = min($occurrences, 2) / 2;
                $matchedWeight += $kw->weight * $multiplier;
                $matched[] = [
                    'keyword'     => $kw->keyword,
                    'category'    => $kw->category,
                    'occurrences' => $occurrences,
                ];
            } else {
                $missing[] = [
                    'keyword'  => $kw->keyword,
                    'category' => $kw->category,
                    'weight'   => $kw->weight,
                ];
            }
        }

        $score = $totalWeight > 0
            ? ($matchedWeight / $totalWeight) * 40
            : 0;

        // Sort missing by weight descending so most impactful show first
        usort($missing, fn($a, $b) => $b['weight'] <=> $a['weight']);

        return [
            'score'   => round($score, 2),
            'matched' => $matched,
            'missing' => array_slice($missing, 0, 20),
        ];
    }

    private function scoreFormatting(CV $cv): array
    {
        $score = 0;
        $issues = [];

        // Has all main sections (+5)
        $hasSections = !empty($cv->experience) && !empty($cv->education) && !empty($cv->skills);
        if ($hasSections) {
            $score += 5;
        } else {
            $issues[] = 'Add all sections: experience, education, and skills';
        }

        // Bullet points in experience descriptions (+3)
        $hasBullets = false;
        if ($cv->experience) {
            foreach ($cv->experience as $exp) {
                $desc = $exp['description'] ?? $exp['responsibilities'] ?? '';
                if (is_array($desc) && count($desc) > 1) {
                    $hasBullets = true;
                    break;
                }
                if (is_string($desc) && (str_contains($desc, '•') || str_contains($desc, '-') || str_contains($desc, '*'))) {
                    $hasBullets = true;
                    break;
                }
            }
        }
        if ($hasBullets) {
            $score += 3;
        } else {
            $issues[] = 'Use bullet points in experience descriptions for better readability';
        }

        // Consistent formatting: experience entries have required fields (+3)
        $consistentFormat = true;
        if ($cv->experience) {
            foreach ($cv->experience as $exp) {
                if (empty($exp['title']) || empty($exp['company'])) {
                    $consistentFormat = false;
                    break;
                }
            }
        }
        if ($consistentFormat && !empty($cv->experience)) {
            $score += 3;
        } else {
            $issues[] = 'Ensure all experience entries have job title and company name';
        }

        // Reasonable word count (200-1000 words) (+4)
        $wordCount = str_word_count($this->extractText($cv));
        if ($wordCount >= 200 && $wordCount <= 1000) {
            $score += 4;
        } elseif ($wordCount < 200) {
            $issues[] = "CV is too short ({$wordCount} words). Aim for 200-1000 words";
        } else {
            $issues[] = "CV is too long ({$wordCount} words). Aim for 200-1000 words";
        }

        // Complete contact info (+5)
        $hasContact = !empty($cv->full_name) && !empty($cv->email) && !empty($cv->phone);
        if ($hasContact) {
            $score += 5;
        } else {
            $issues[] = 'Add complete contact information: full name, email, and phone';
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function scoreContentQuality(CV $cv, string $fullText): array
    {
        $score = 0;
        $issues = [];

        // Quantifiable achievements: %, $, numbers (+8)
        $hasQuantifiables = preg_match('/\d+\s*(%|percent|\$|million|billion|k\b|thousand|users|customers|projects?|years?|months?)/i', $fullText);
        if ($hasQuantifiables) {
            $score += 8;
        } else {
            $issues[] = 'Add quantifiable achievements (e.g., "increased revenue by 30%", "managed 5 engineers")';
        }

        // Detailed experience descriptions (>50 chars average) (+6)
        $detailedDescriptions = true;
        if ($cv->experience) {
            foreach ($cv->experience as $exp) {
                $desc = $exp['description'] ?? $exp['responsibilities'] ?? '';
                $descText = is_array($desc) ? implode(' ', $desc) : $desc;
                if (strlen($descText) < 50) {
                    $detailedDescriptions = false;
                    break;
                }
            }
        }
        if ($detailedDescriptions && !empty($cv->experience)) {
            $score += 6;
        } else {
            $issues[] = 'Add detailed descriptions to each work experience entry (at least 50 characters)';
        }

        // Professional summary (+3)
        if (!empty($cv->summary) && str_word_count($cv->summary) >= 20) {
            $score += 3;
        } else {
            $issues[] = 'Add a professional summary of at least 20 words';
        }

        // Complete contact details (+3)
        if (!empty($cv->email) && !empty($cv->phone)) {
            $score += 3;
        } else {
            $issues[] = 'Provide complete contact details including email and phone';
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function scoreCompleteness(CV $cv): array
    {
        $score = 0;
        $issues = [];

        if (!empty($cv->full_name)) {
            $score += 3;
        } else {
            $issues[] = 'Add your full name';
        }

        if (!empty($cv->email)) {
            $score += 3;
        } else {
            $issues[] = 'Add your email address';
        }

        if (!empty($cv->phone)) {
            $score += 2;
        } else {
            $issues[] = 'Add your phone number';
        }

        if (!empty($cv->summary)) {
            $score += 4;
        } else {
            $issues[] = 'Add a professional summary';
        }

        if (!empty($cv->experience) && count($cv->experience) > 0) {
            $score += 5;
        } else {
            $issues[] = 'Add at least one work experience entry';
        }

        if (!empty($cv->education) && count($cv->education) > 0) {
            $score += 3;
        } else {
            $issues[] = 'Add at least one education entry';
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function buildRecommendations(
        array $keywordResult,
        array $formattingScore,
        array $qualityScore,
        array $completenessScore
    ): array {
        $recommendations = [];

        foreach ([$completenessScore['issues'], $formattingScore['issues'], $qualityScore['issues']] as $issues) {
            foreach ($issues as $issue) {
                $recommendations[] = $issue;
            }
        }

        // Add top missing keywords as a recommendation
        if (!empty($keywordResult['missing'])) {
            $topMissing = array_slice(array_column($keywordResult['missing'], 'keyword'), 0, 5);
            $recommendations[] = 'Consider adding these high-impact keywords: ' . implode(', ', $topMissing);
        }

        return $recommendations;
    }
}
