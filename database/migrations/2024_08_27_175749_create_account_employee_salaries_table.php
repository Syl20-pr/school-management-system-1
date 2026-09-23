<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des paiements de salaires des employés.
 *
 * Enregistre chaque paiement de salaire effectué à un employé.
 * Permet de suivre l'historique des versements mensuels de salaires.
 *
 * CORRECTION appliquée :
 *   La colonne 'date' était de type string ; elle est désormais de type date.
 *
 * Relations :
 *   users (employee) → account_employee_salaries
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table account_employee_salaries.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : account_employee_salaries
        // Journal des versements de salaires aux employés.
        // Chaque ligne représente un paiement effectué à un employé
        // à une date donnée pour un montant précis.
        // ---------------------------------------------------------------
        Schema::create('account_employee_salaries', function (Blueprint $table) {
            $table->id();                                                           // Clé primaire auto-incrémentée

            // --- Relation employé ---
            $table->integer('employee_id')->comment('Référence à users.id (l\'employé)'); // Employé bénéficiaire du salaire

            // --- Détails du paiement ---
            $table->date('date')->nullable();                                       // Date du versement (corrigé : anciennement de type string)
            $table->double('amount')->nullable();                                   // Montant du salaire versé

            $table->timestamps();                                                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table account_employee_salaries.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_employee_salaries'); // Suppression de la table des paiements de salaires
    }
};
