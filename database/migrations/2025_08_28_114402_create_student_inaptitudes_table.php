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
            $table->unsignedBigInteger('assign_subject_id'); // = school_subjects.id dans ton flux actuel
            $table->unsignedBigInteger('term_type_id');
            $table->boolean('inapte')->default(false);
            $table->timestamps();

            $table->unique(['student_id','year_id','class_id','assign_subject_id','term_type_id'], 'inaptitudes_unique');
        });
    }
    public function down(): void {
        Schema::dropIfExists('student_inaptitudes');
    }
};
