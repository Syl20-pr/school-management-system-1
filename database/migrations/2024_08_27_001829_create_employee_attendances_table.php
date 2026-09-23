<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des présences du personnel.
 *
 * Enregistre quotidiennement la présence ou l'absence de chaque employé.
 * Le statut de présence (attend_status) peut prendre des valeurs telles que
 * "present", "absent", "late", "excused", etc.
 *
 * Relations :
 *   users (employee) → employee_attendances
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table employee_attendances.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : employee_attendances
        // Suivi quotidien de la présence des employés.
        // Chaque enregistrement correspond à un employé pour une date
        // donnée, avec son statut de présence.
        // ---------------------------------------------------------------
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();                                                      // Clé primaire auto-incrémentée

            // --- Relation employé ---
            $table->integer('employee_id')->comment('Référence à users.id (l\'employé)'); // Employé concerné

            // --- Suivi de présence ---
            $table->date('date');                                              // Date du relevé de présence
            $table->string('attend_status');                                   // Statut : "present", "absent", "retard", "excusé"…

            $table->timestamps();                                              // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table employee_attendances.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances'); // Suppression de la table des présences du personnel
    }
};
