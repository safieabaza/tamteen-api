<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_id')->constrained()->cascadeOnDelete();
            $table->float('ats_score');
            $table->json('matched_keywords');
            $table->json('missing_keywords');
            $table->json('recommendations');
            $table->json('score_breakdown')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_analyses');
    }
};
