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
                $table->integer('class_id')->after('year_id');
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
