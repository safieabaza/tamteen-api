<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CVAnalysis extends Model
{
    protected $table = 'cv_analyses';

    protected $fillable = [
        'cv_id', 'ats_score', 'matched_keywords',
        'missing_keywords', 'recommendations', 'score_breakdown',
    ];

    protected $casts = [
        'ats_score'         => 'float',
        'matched_keywords'  => 'array',
        'missing_keywords'  => 'array',
        'recommendations'   => 'array',
        'score_breakdown'   => 'array',
    ];

    public function cv()
    {
        return $this->belongsTo(CV::class, 'cv_id');
    }
}
