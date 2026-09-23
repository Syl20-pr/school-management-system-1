<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des paiements de frais scolaires des élèves.
 *
 * Enregistre chaque transaction de paiement effectuée par un élève
 * pour une catégorie de frais donnée. Permet de suivre les encaissements
 * et de calculer les soldes restants.
 *
 * CORRECTION appliquée :
 *   La colonne 'date' était de type string ; elle est désormais de type date.
 *
 * Relations :
 *   users (student) → account_student_fees
 *   student_years → account_student_fees
 *   student_classes → account_student_fees
 *   fee_categories → account_student_fees
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table account_student_fees.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : account_student_fees
        // Journal des paiements de frais scolaires par élève.
        // Chaque ligne correspond à un paiement (ou versement partiel)
        // pour une catégorie de frais précise.
        // ---------------------------------------------------------------
        Schema::create('account_student_fees', function (Blueprint $table) {
            $table->id();                                   // Clé primaire auto-incrémentée

            // --- Contexte scolaire ---
            $table->integer('year_id')->nullable();         // Année scolaire (référence student_years.id)
            $table->integer('class_id')->nullable();        // Classe de l'élève au moment du paiement (référence student_classes.id)
            $table->integer('student_id')->nullable();      // Élève qui effectue le paiement (référence users.id)
            $table->integer('fee_category_id')->nullable(); // Catégorie de frais payée (référence fee_categories.id)

            // --- Détails du paiement ---
            $table->date('date')->nullable();               // Date du paiement (corrigé : anciennement de type string)
            $table->double('amount')->nullable();           // Montant versé lors de cette transaction

            $table->timestamps();                           // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table account_student_fees.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_student_fees'); // Suppression de la table des paiements de frais
    }
};
