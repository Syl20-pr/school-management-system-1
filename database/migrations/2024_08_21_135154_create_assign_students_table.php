<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table d'inscription des élèves dans les classes.
 *
 * Table pivot centrale du module élèves. Elle matérialise l'affectation
 * d'un élève à une classe pour une année scolaire donnée. Elle regroupe
 * également le groupe, le créneau horaire et le numéro de liste (roll).
 *
 * Relation principale :
 *   users (student) ↔ student_classes ↔ student_years
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table assign_students.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : assign_students
        // Enregistre l'inscription d'un élève dans une classe pour une
        // année scolaire précise. Un élève peut changer de classe d'une
        // année à l'autre ; l'historique est conservé via cette table.
        // ---------------------------------------------------------------
        Schema::create('assign_students', function (Blueprint $table) {

            // --- Identification de l'inscription ---
            $table->integer('student_id')->comment('Référence à users.id (l\'élève)'); // Élève concerné
            $table->integer('roll')->nullable();                // Numéro de liste de l'élève dans la classe

            // --- Affectation pédagogique ---
            $table->integer('class_id');                        // Classe affectée (référence student_classes)
            $table->integer('year_id');                         // Année scolaire (référence student_years)
            $table->integer('group_id')->nullable();            // Groupe optionnel (référence student_groups)
            $table->integer('shift_id')->nullable();            // Créneau horaire optionnel (référence student_shifts)

            $table->timestamps();                               // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table assign_students.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_students'); // Suppression de la table d'inscription des élèves
    }
};
