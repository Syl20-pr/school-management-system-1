<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne gender si elle n'existe pas déjà.
     * Niveau 4 : Intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->comment('Genre de l\'utilisateur (Masculin / Féminin)');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne si présente.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
};
