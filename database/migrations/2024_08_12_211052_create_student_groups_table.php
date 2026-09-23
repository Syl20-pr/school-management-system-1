<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des groupes d'élèves.
 *
 * Un groupe permet de subdiviser une classe en sous-ensembles
 * (ex : Groupe A, Groupe B, Groupe Sciences, etc.).
 * Cette subdivision est utilisée pour l'organisation des travaux
 * pratiques, des activités ou des séquences pédagogiques.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table student_groups.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : student_groups
        // Référentiel des groupes au sein d'une classe.
        // Utilisé dans l'inscription des élèves (assign_students)
        // pour affiner la répartition pédagogique.
        // ---------------------------------------------------------------
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique du groupe (ex : "Groupe A", "Groupe Sciences")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table student_groups.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_groups'); // Suppression de la table des groupes
    }
};
