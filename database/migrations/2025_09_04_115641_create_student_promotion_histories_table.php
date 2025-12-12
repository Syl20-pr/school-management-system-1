<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_promotion_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_year_id');
            $table->unsignedBigInteger('from_class_id');
            $table->unsignedBigInteger('to_year_id')->nullable();
            $table->unsignedBigInteger('to_class_id')->nullable();
            $table->enum('action', ['promote', 'repeat', 'exclude']);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('decision_by');
            $table->timestamp('decision_date');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_year_id')->references('id')->on('student_years')->onDelete('cascade');
            $table->foreign('to_year_id')->references('id')->on('student_years')->onDelete('cascade');
            $table->foreign('from_class_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('to_class_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('decision_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_promotion_histories');
    }
};