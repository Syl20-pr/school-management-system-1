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
        Schema::create('assign_designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id')
                  ->comment('Référence à designations.id');
            $table->string('comment')->nullable()
                  ->comment('Commentaire ou observation liée à cette affectation');
            $table->timestamps();

            // Clé étrangère vers la table des postes/grades
            $table->foreign('designation_id')
                  ->references('id')
                  ->on('designations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_designations');
    }
};
