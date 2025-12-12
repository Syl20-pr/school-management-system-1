<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assign_subject_teaches', function (Blueprint $table) {
            $table->id();
            $table->integer('year_id');
            $table->integer('designation_id');
            $table->integer('teacher_id');
            $table->integer('subject_id');
            $table->double('total_hours');
            $table->double('total_sessions');
            $table->string('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_subject_teaches');
    }
};
