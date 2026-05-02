<?php

namespace Database\Seeders;

use App\Models\ATSKeyword;
use Illuminate\Database\Seeder;

class ATSKeywordSeeder extends Seeder
{
    public function run(): void
    {
        ATSKeyword::truncate();

        $keywords = [
            // Action Verbs (weight: 1.2)
            ['category' => 'action_verb', 'keyword' => 'achieved',     'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'led',          'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'implemented',  'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'designed',     'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'developed',    'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'managed',      'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'created',      'weight' => 1.0],
            ['category' => 'action_verb', 'keyword' => 'optimized',    'weight' => 1.3],
            ['category' => 'action_verb', 'keyword' => 'improved',     'weight' => 1.3],
            ['category' => 'action_verb', 'keyword' => 'increased',    'weight' => 1.3],
            ['category' => 'action_verb', 'keyword' => 'reduced',      'weight' => 1.3],
            ['category' => 'action_verb', 'keyword' => 'established',  'weight' => 1.1],
            ['category' => 'action_verb', 'keyword' => 'coordinated',  'weight' => 1.0],
            ['category' => 'action_verb', 'keyword' => 'executed',     'weight' => 1.1],
            ['category' => 'action_verb', 'keyword' => 'launched',     'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'delivered',    'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'built',        'weight' => 1.1],
            ['category' => 'action_verb', 'keyword' => 'analyzed',     'weight' => 1.1],
            ['category' => 'action_verb', 'keyword' => 'collaborated', 'weight' => 1.0],
            ['category' => 'action_verb', 'keyword' => 'mentored',     'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'streamlined',  'weight' => 1.2],
            ['category' => 'action_verb', 'keyword' => 'spearheaded',  'weight' => 1.3],
            ['category' => 'action_verb', 'keyword' => 'revamped',     'weight' => 1.1],
            ['category' => 'action_verb', 'keyword' => 'engineered',   'weight' => 1.2],

            // Tech Skills (weight: 1.0 - 1.5)
            ['category' => 'tech_skill', 'keyword' => 'javascript',  'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'python',      'weight' => 1.4],
            ['category' => 'tech_skill', 'keyword' => 'java',        'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'react',       'weight' => 1.4],
            ['category' => 'tech_skill', 'keyword' => 'node.js',     'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'aws',         'weight' => 1.5],
            ['category' => 'tech_skill', 'keyword' => 'docker',      'weight' => 1.4],
            ['category' => 'tech_skill', 'keyword' => 'kubernetes',  'weight' => 1.5],
            ['category' => 'tech_skill', 'keyword' => 'sql',         'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'mongodb',     'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'git',         'weight' => 1.0],
            ['category' => 'tech_skill', 'keyword' => 'api',         'weight' => 1.1],
            ['category' => 'tech_skill', 'keyword' => 'rest',        'weight' => 1.1],
            ['category' => 'tech_skill', 'keyword' => 'graphql',     'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'typescript',  'weight' => 1.4],
            ['category' => 'tech_skill', 'keyword' => 'laravel',     'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'vue.js',      'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'angular',     'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'php',         'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'ruby',        'weight' => 1.1],
            ['category' => 'tech_skill', 'keyword' => 'go',          'weight' => 1.3],
            ['category' => 'tech_skill', 'keyword' => 'redis',       'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'postgresql',  'weight' => 1.2],
            ['category' => 'tech_skill', 'keyword' => 'linux',       'weight' => 1.1],
            ['category' => 'tech_skill', 'keyword' => 'ci/cd',       'weight' => 1.4],
            ['category' => 'tech_skill', 'keyword' => 'machine learning', 'weight' => 1.5],
            ['category' => 'tech_skill', 'keyword' => 'data science', 'weight' => 1.4],

            // Soft Skills (weight: 0.8 - 1.1)
            ['category' => 'soft_skill', 'keyword' => 'leadership',       'weight' => 1.1],
            ['category' => 'soft_skill', 'keyword' => 'communication',    'weight' => 1.0],
            ['category' => 'soft_skill', 'keyword' => 'teamwork',         'weight' => 0.9],
            ['category' => 'soft_skill', 'keyword' => 'problem-solving',  'weight' => 1.1],
            ['category' => 'soft_skill', 'keyword' => 'analytical',       'weight' => 1.0],
            ['category' => 'soft_skill', 'keyword' => 'creative',         'weight' => 0.9],
            ['category' => 'soft_skill', 'keyword' => 'detail-oriented',  'weight' => 0.9],
            ['category' => 'soft_skill', 'keyword' => 'organized',        'weight' => 0.8],
            ['category' => 'soft_skill', 'keyword' => 'adaptable',        'weight' => 0.9],
            ['category' => 'soft_skill', 'keyword' => 'motivated',        'weight' => 0.8],
            ['category' => 'soft_skill', 'keyword' => 'collaborative',    'weight' => 1.0],
            ['category' => 'soft_skill', 'keyword' => 'innovative',       'weight' => 1.0],

            // Industry Terms (weight: 1.1 - 1.4)
            ['category' => 'industry_term', 'keyword' => 'agile',          'weight' => 1.2],
            ['category' => 'industry_term', 'keyword' => 'scrum',          'weight' => 1.2],
            ['category' => 'industry_term', 'keyword' => 'kanban',         'weight' => 1.1],
            ['category' => 'industry_term', 'keyword' => 'devops',         'weight' => 1.4],
            ['category' => 'industry_term', 'keyword' => 'microservices',  'weight' => 1.4],
            ['category' => 'industry_term', 'keyword' => 'scalability',    'weight' => 1.3],
            ['category' => 'industry_term', 'keyword' => 'performance',    'weight' => 1.1],
            ['category' => 'industry_term', 'keyword' => 'security',       'weight' => 1.2],
            ['category' => 'industry_term', 'keyword' => 'testing',        'weight' => 1.1],
            ['category' => 'industry_term', 'keyword' => 'automation',     'weight' => 1.3],
            ['category' => 'industry_term', 'keyword' => 'cloud',          'weight' => 1.3],
            ['category' => 'industry_term', 'keyword' => 'architecture',   'weight' => 1.3],
        ];

        foreach ($keywords as $keyword) {
            ATSKeyword::create($keyword);
        }

        $this->command->info('Seeded ' . count($keywords) . ' ATS keywords');
    }
}
