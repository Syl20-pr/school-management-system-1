<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne class_id si non présente.
     * Niveau 4 : Intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('assign_subject_teaches', function (Blueprint $table) {
            if (!Schema::hasColumn('assign_subject_teaches', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('year_id')
                      ->comment('Référence à student_classes.id');
                $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('set null');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne class_id si présente.
     */
    public function down(): void
    {
        Schema::table('assign_subject_teaches', function (Blueprint $table) {
            if (Schema::hasColumn('assign_subject_teaches', 'class_id')) {
                $table->dropColumn('class_id');
            }
        });
    }
};
