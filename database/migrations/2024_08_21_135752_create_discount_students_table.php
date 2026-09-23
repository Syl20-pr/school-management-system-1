<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des remises accordées aux élèves.
 *
 * Certains élèves peuvent bénéficier d'une réduction sur leurs frais scolaires.
 * Cette table enregistre, pour chaque inscription (assign_student),
 * la catégorie de frais concernée et le montant ou pourcentage de remise accordé.
 *
 * Relations :
 *   assign_students → discount_students ← fee_categories
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table discount_students.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : discount_students
        // Enregistre les remises sur les frais accordées à des élèves
        // inscrits. Chaque remise est liée à une inscription spécifique
        // et à une catégorie de frais.
        // ---------------------------------------------------------------
        Schema::create('discount_students', function (Blueprint $table) {
            $table->id();                                   // Clé primaire auto-incrémentée

            // --- Relations ---
            $table->integer('assign_student_id');           // Inscription de l'élève (référence assign_students.id)
            $table->integer('fee_category_id')->nullable(); // Catégorie de frais concernée (référence fee_categories.id)

            // --- Montant de la remise ---
            $table->double('discount')->nullable();         // Montant ou pourcentage de réduction accordé

            $table->timestamps();                           // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table discount_students.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_students'); // Suppression de la table des remises élèves
    }
};
