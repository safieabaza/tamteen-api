<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ATSKeyword extends Model
{
    protected $table = 'ats_keywords';

    protected $fillable = ['category', 'keyword', 'weight'];

    protected $casts = [
        'weight' => 'float',
    ];
}
