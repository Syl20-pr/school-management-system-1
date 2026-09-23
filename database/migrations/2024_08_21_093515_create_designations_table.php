<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des fonctions / postes du personnel.
 *
 * Référentiel des désignations (postes ou fonctions) attribuables
 * aux employés de l'établissement (ex : Directeur, Enseignant,
 * Surveillant, Comptable, Secrétaire, etc.).
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table designations.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : designations
        // Référentiel des postes et fonctions du personnel.
        // Utilisée pour affecter un rôle professionnel à chaque employé
        // via la table assign_designations.
        // ---------------------------------------------------------------
        Schema::create('designations', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique du poste (ex : "Directeur", "Enseignant", "Comptable")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table designations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations'); // Suppression de la table des fonctions du personnel
    }
};
