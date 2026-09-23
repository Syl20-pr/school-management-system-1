<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des années scolaires.
 *
 * Chaque enregistrement représente une année académique (ex : "2023-2024").
 * Le champ 'is_current' permet d'identifier rapidement l'année en cours
 * sans avoir à comparer des dates.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table student_years.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : student_years
        // Référentiel des années scolaires de l'établissement.
        // Toutes les inscriptions, notes et présences sont liées à une
        // année scolaire pour faciliter le suivi longitudinal des élèves.
        // ---------------------------------------------------------------
        Schema::create('student_years', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique de l'année (ex : "2023-2024")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table student_years.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_years'); // Suppression de la table des années scolaires
    }
};
