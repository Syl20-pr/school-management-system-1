<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des congés du personnel.
 *
 * Enregistre les demandes et attributions de congés pour chaque employé.
 * Chaque congé est caractérisé par sa date de début, sa date de fin
 * et son motif (référence à la table leave_purposes).
 *
 * Relations :
 *   users (employee) → employee_leaves ← leave_purposes
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table employee_leaves.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : employee_leaves
        // Enregistre les congés accordés aux employés.
        // La durée est calculée entre start_date et end_date.
        // Le motif est défini dans la table leave_purposes.
        // ---------------------------------------------------------------
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();                                                   // Clé primaire auto-incrémentée

            // --- Relations ---
            $table->integer('employee_id')->comment('Référence à users.id (l\'employé en congé)'); // Employé bénéficiaire
            $table->integer('leave_purpose_id');                            // Motif du congé (référence leave_purposes.id)

            // --- Période du congé ---
            $table->date('start_date');                                     // Date de début du congé
            $table->date('end_date');                                       // Date de fin du congé

            $table->timestamps();                                           // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table employee_leaves.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_leaves'); // Suppression de la table des congés
    }
};
