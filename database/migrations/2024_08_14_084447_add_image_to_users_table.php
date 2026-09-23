<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne image si elle n'existe pas déjà.
     * Niveau 4 : Intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('gender')->comment('Chemin vers la photo de profil');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne si présente.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
