<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne statusclass si elle n'existe pas déjà.
     * Niveau 4 : Intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'statusclass')) {
                $table->string('statusclass')->nullable()->after('gender')->comment('Statut de l\'élève dans la classe (Redoublant, Passant, etc.)');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne si présente.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'statusclass')) {
                $table->dropColumn('statusclass');
            }
        });
    }
};
