<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la clé primaire id si elle n'existe pas déjà.
     * Niveau 4 : Garantie d'intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('assign_students', function (Blueprint $table) {
            if (!Schema::hasColumn('assign_students', 'id')) {
                $table->id()->first();
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la clé primaire id.
     */
    public function down(): void
    {
        Schema::table('assign_students', function (Blueprint $table) {
            if (Schema::hasColumn('assign_students', 'id')) {
                $table->dropColumn('id');
            }
        });
    }
};
