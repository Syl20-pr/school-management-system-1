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
        Schema::table('assign_subject_teaches', function (Blueprint $table) {
            $table->integer('class_id')->after('year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_subject_teaches', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
    }
};
