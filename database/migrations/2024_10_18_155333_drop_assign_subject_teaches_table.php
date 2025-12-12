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
        //
        Schema::dropIfExists('assign_subject_teaches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
