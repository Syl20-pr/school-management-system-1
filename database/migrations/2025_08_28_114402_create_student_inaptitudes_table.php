<?php

// database/migrations/2025_08_28_000000_create_student_inaptitudes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_inaptitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('assign_subject_id');
            $table->unsignedBigInteger('term_type_id');
            $table->boolean('inapte')->default(false);
            $table->timestamps();

            // Contrainte d'unicité : un seul enregistrement par élève/année/classe/matière/trimestre
            $table->unique(['student_id', 'year_id', 'class_id', 'assign_subject_id', 'term_type_id'], 'inaptitudes_unique');

            // Clés étrangères pour garantir l'intégrité référentielle
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('assign_subject_id')->references('id')->on('assign_subjects')->onDelete('cascade');
            $table->foreign('term_type_id')->references('id')->on('term_types')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_inaptitudes');
    }
};
