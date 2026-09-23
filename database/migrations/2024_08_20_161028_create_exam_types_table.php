<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des types d'examens.
 *
 * Référentiel des différents types d'épreuves ou d'évaluations de
 * l'établissement (ex : Devoir 1, Devoir 2, Examen semestriel, Examen final).
 * Chaque type d'examen est rattaché à une période (trimestre ou semestre).
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table exam_types.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : exam_types
        // Référentiel des types d'examens ou de contrôles continus.
        // Utilisée pour catégoriser les notes des élèves (student_marks)
        // et organiser les évaluations par période (term_types).
        // ---------------------------------------------------------------
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique du type d'examen (ex : "Devoir 1", "Examen")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table exam_types.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types'); // Suppression de la table des types d'examens
    }
};
