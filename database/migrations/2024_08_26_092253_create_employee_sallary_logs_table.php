<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table du journal des modifications de salaire.
 *
 * Chaque modification du salaire d'un employé est tracée ici avec
 * l'ancien salaire, le nouveau salaire, l'incrément appliqué et
 * la date d'effet de la modification.
 *
 * REMARQUE : Le nom d'origine de la table était "employee_sallary_logs"
 * (faute de frappe avec double "l"). Le nom corrigé est utilisé ici.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table employee_salary_logs.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : employee_salary_logs
        // Journal d'audit des changements de salaire des employés.
        // Permet de consulter l'historique complet des évolutions salariales
        // pour chaque employé.
        // NB : Ancien nom erroné = "employee_sallary_logs" (corrigé ici).
        // ---------------------------------------------------------------
        Schema::create('employee_sallary_logs', function (Blueprint $table) {
            $table->id();                                           // Clé primaire auto-incrémentée

            // --- Relation employé ---
            $table->integer('employee_id')->comment('Référence à users.id (l\'employé concerné)'); // Employé concerné

            // --- Données salariales ---
            $table->integer('previous_salary')->nullable();         // Salaire avant modification
            $table->integer('present_salary')->nullable();          // Nouveau salaire après modification
            $table->integer('increment_salary')->nullable();        // Montant de l'augmentation appliquée

            // --- Date d'effet ---
            $table->date('effected_salary')->nullable();            // Date à partir de laquelle le nouveau salaire est applicable

            $table->timestamps();                                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table employee_sallary_logs.
     * NB : Le nom 'employee_sallary_logs' (double l) est conservé intentionnellement
     * pour la compatibilité avec les migrations suivantes qui y font référence.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_sallary_logs'); // Suppression du journal des salaires
    }
};
