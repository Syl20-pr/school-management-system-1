<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne address si elle n'existe pas déjà.
     * Niveau 4 : Garantie d'intégrité du schéma et compatibilité migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->comment('Adresse postale ou géographique');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne si présente.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
