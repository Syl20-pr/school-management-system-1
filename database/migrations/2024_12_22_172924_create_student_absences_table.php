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
        Schema::create('student_absences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->comment('References user_id in users table');
            $table->string('id_no')->nullable()->comment('Identification number');
            $table->unsignedBigInteger('year_id')->nullable()->comment('References year in years table');
            $table->unsignedBigInteger('class_id')->nullable()->comment('References class in classes table');
            $table->unsignedTinyInteger('type_tri_sem')->nullable();
            $table->float('absences', 5, 2)->nullable()->comment('Number of hours of absences'); // 999.99 max
            $table->timestamps();

            // Relationships
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('years')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');

            // Indexes
            $table->index(['student_id', 'year_id', 'class_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_absences');
    }
};
