<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CV extends Model
{
    protected $table = 'cvs';

    protected $fillable = [
        'user_id', 'title', 'full_name', 'email', 'phone',
        'summary', 'experience', 'education', 'skills', 'ats_score',
    ];

    protected $casts = [
        'experience' => 'array',
        'education'  => 'array',
        'skills'     => 'array',
        'ats_score'  => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function analyses()
    {
        return $this->hasMany(CVAnalysis::class, 'cv_id');
    }

    public function latestAnalysis()
    {
        return $this->hasOne(CVAnalysis::class, 'cv_id')->latestOfMany();
    }
}
