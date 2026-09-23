<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécution de la migration : ajout de la colonne mobile si elle n'existe pas déjà.
     * Niveau 4 : Garantie d'intégrité et compatibilité avec migrate:fresh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérification de présence préalable pour éviter les erreurs de duplication de colonne
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable()->comment('Numéro de téléphone mobile');
            }
        });
    }

    /**
     * Annulation de la migration : suppression de la colonne si elle existe.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mobile')) {
                $table->dropColumn('mobile');
            }
        });
    }
};
