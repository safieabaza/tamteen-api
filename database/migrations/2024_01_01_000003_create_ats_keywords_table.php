<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ats_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('keyword');
            $table->float('weight')->default(1.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ats_keywords');
    }
};
