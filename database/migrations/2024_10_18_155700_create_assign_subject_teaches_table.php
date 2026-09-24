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
            $table->unsignedBigInteger('year_id')
                  ->comment('Référence à student_years.id');
            $table->unsignedBigInteger('class_id')
                  ->nullable()
                  ->comment('Référence à student_classes.id (ajouté par migration ultérieure)');
            $table->unsignedBigInteger('designation_id')
                  ->comment('Référence à designations.id');
            $table->unsignedBigInteger('teacher_id')
                  ->comment('Référence à users.id (enseignant)');
            $table->unsignedBigInteger('subject_id')
                  ->comment('Référence à school_subjects.id');
            $table->double('total_hours')->nullable();
            $table->double('total_sessions')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();

            // Clés étrangères référentielles
            $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('set null');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('school_subjects')->onDelete('cascade');
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
