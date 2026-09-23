<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des catégories de frais scolaires.
 *
 * Référentiel des différents types de frais perçus par l'établissement
 * (ex : frais d'inscription, frais de scolarité, frais d'examen).
 * Ces catégories sont ensuite liées à des montants par classe et à des
 * paiements effectués par les élèves.
 */
return new class extends Migration {
    /**
     * Exécution de la migration : création de la table fee_categories.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : fee_categories
        // Référentiel des types de frais scolaires.
        // Chaque catégorie peut avoir un montant différent selon la classe
        // (voir la table fee_category_amounts).
        // ---------------------------------------------------------------
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique de la catégorie (ex : "Inscription", "Scolarité")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table fee_categories.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_categories'); // Suppression de la table des catégories de frais
    }
};
