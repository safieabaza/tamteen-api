<?php

namespace App\Http\Controllers;

use App\Models\CV;
use App\Models\CVAnalysis;
use App\Services\ATSService;
use Illuminate\Http\JsonResponse;

class ATSController extends Controller
{
    public function __construct(private ATSService $atsService) {}

    public function analyze(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $cv = CV::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found',
                'data'    => [],
            ], 404);
        }

        $result = $this->atsService->analyze($cv);

        // Persist the analysis result
        $analysis = CVAnalysis::create([
            'cv_id'            => $cv->id,
            'ats_score'        => $result['ats_score'],
            'matched_keywords' => $result['matched_keywords'],
            'missing_keywords' => $result['missing_keywords'],
            'recommendations'  => $result['recommendations'],
            'score_breakdown'  => $result['score_breakdown'],
        ]);

        // Update the ATS score on the CV itself
        $cv->update(['ats_score' => $result['ats_score']]);

        return response()->json([
            'success' => true,
            'message' => 'ATS analysis completed',
            'data'    => [
                'analysis_id'      => $analysis->id,
                'cv_id'            => $cv->id,
                'ats_score'        => $result['ats_score'],
                'score_breakdown'  => $result['score_breakdown'],
                'matched_keywords' => $result['matched_keywords'],
                'missing_keywords' => $result['missing_keywords'],
                'recommendations'  => $result['recommendations'],
            ],
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $cv = CV::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found',
                'data'    => [],
            ], 404);
        }

        $analyses = CVAnalysis::where('cv_id', $cv->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Analysis history retrieved',
            'data'    => ['analyses' => $analyses],
        ]);
    }
}
