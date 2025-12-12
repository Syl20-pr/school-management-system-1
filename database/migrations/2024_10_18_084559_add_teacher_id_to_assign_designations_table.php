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
        Schema::table('assign_designations', function (Blueprint $table) {
            // Add the teacher_id column (unsigned big integer)
            $table->unsignedBigInteger('teacher_id')->nullable()->after('designation_id');

            // Add a foreign key constraint to the users table
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_designations', function (Blueprint $table) {
            // Drop the foreign key and the column
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }
};
