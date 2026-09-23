<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des notes des élèves.
 *
 * Enregistre les notes obtenues par chaque élève pour une matière,
 * un type d'examen et une période donnés. Cette table est au cœur
 * du module de gestion pédagogique.
 *
 * Relations :
 *   users (student) → student_marks
 *   assign_subjects → student_marks
 *   exam_types → student_marks
 *   student_years → student_marks
 *   student_classes → student_marks
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table student_marks.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : student_marks
        // Stocke les notes des élèves par matière, examen et période.
        // La colonne 'inapte' indique si l'élève était inapte (dispensé)
        // lors de l'évaluation (ajout ultérieur en 2025).
        // ---------------------------------------------------------------
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();                                                           // Clé primaire auto-incrémentée

            // --- Identification de l'élève ---
            $table->integer('student_id')->comment('Référence à users.id (l\'élève)'); // Élève concerné
            $table->string('id_no')->nullable();                                    // Numéro matricule de l'élève (redondance pour recherche rapide)

            // --- Contexte scolaire ---
            $table->integer('year_id')->nullable();                                 // Année scolaire (référence student_years.id)
            $table->integer('class_id')->nullable();                                // Classe de l'élève (référence student_classes.id)
            $table->integer('assign_subject_id')->nullable();                       // Matière assignée (référence assign_subjects.id)
            $table->integer('exam_type_id')->nullable();                            // Type d'examen (référence exam_types.id)

            // --- Note obtenue ---
            $table->double('marks')->nullable();                                    // Note numérique obtenue par l'élève

            $table->timestamps();                                                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table student_marks.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_marks'); // Suppression de la table des notes
    }
};
