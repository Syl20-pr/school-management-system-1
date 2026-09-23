<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table d'affectation des matières aux classes.
 *
 * Cette table fait le lien entre une matière et une classe scolaire en
 * définissant les paramètres d'évaluation propres à cette association :
 * la note maximale, la note de passage et la part de note subjective.
 *
 * Relations :
 *   school_subjects → assign_subjects ← student_classes
 *   assign_subjects → student_marks (notes des élèves)
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table assign_subjects.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : assign_subjects
        // Affecte une matière à une classe avec ses paramètres de notation.
        // Permet de configurer un barème différent par classe et par matière.
        // ---------------------------------------------------------------
        Schema::create('assign_subjects', function (Blueprint $table) {
            $table->id();                               // Clé primaire auto-incrémentée

            // --- Relations ---
            $table->integer('class_id');                // Classe concernée (référence student_classes.id)
            $table->integer('subject_id');              // Matière enseignée (référence school_subjects.id)

            // --- Paramètres de notation ---
            $table->double('full_mark');                // Note maximale attribuable (barème total)
            $table->double('pass_mark');                // Note minimale pour valider la matière (seuil de passage)
            $table->double('subjective_mark');          // Part de la note issue de l'évaluation subjective (ex : participation)

            $table->timestamps();                       // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table assign_subjects.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_subjects'); // Suppression de la table d'affectation des matières
    }
};
