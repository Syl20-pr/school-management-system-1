<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des montants de frais par catégorie et par classe.
 *
 * Cette table fait le lien entre une catégorie de frais et une classe scolaire
 * en précisant le montant applicable. Ainsi, les frais de scolarité peuvent
 * varier d'une classe à l'autre (ex : 6ème = 50 000 FCFA, Terminale = 80 000 FCFA).
 *
 * Relations :
 *   fee_categories → fee_category_amounts ← student_classes
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table fee_category_amounts.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : fee_category_amounts
        // Définit le montant d'une catégorie de frais pour une classe donnée.
        // Permet de personnaliser les tarifs selon le niveau scolaire.
        // ---------------------------------------------------------------
        Schema::create('fee_category_amounts', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée

            // --- Relations (sans contrainte FK formelle — cohérence applicative) ---
            $table->integer('fee_category_id');     // Catégorie de frais (référence fee_categories.id)
            $table->integer('class_id');            // Classe concernée (référence student_classes.id)

            // --- Montant ---
            $table->double('amount');               // Montant des frais (en unité monétaire locale)

            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table fee_category_amounts.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_category_amounts'); // Suppression de la table des montants de frais
    }
};
