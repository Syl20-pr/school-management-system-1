<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des classes scolaires.
 *
 * Chaque enregistrement représente une classe (ex : 6ème, 5ème, Terminale).
 * Les colonnes 'level' et 'level_order' permettent d'ordonner les classes
 * dans une hiérarchie pédagogique (cycle, niveau, etc.).
 */
return new class extends Migration {
    /**
     * Exécution de la migration : création de la table student_classes.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : student_classes
        // Référentiel des classes de l'établissement scolaire.
        // Utilisée pour affecter les élèves, attribuer les matières
        // et générer les bulletins de notes.
        // ---------------------------------------------------------------
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();                                          // Clé primaire auto-incrémentée
            $table->string('name')->unique();                      // Nom unique de la classe (ex : "6ème A", "Terminale S")
            $table->timestamps();                                  // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table student_classes.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_classes'); // Suppression de la table des classes
    }
};
