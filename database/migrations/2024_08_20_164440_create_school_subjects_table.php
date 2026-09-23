<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des matières scolaires.
 *
 * Référentiel de toutes les matières enseignées dans l'établissement
 * (ex : Mathématiques, Français, Histoire-Géographie, Sciences Physiques).
 * Les matières sont ensuite affectées aux classes via la table assign_subjects.
 */
return new class extends Migration {
    /**
     * Exécution de la migration : création de la table school_subjects.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : school_subjects
        // Référentiel des matières ou disciplines enseignées.
        // Une matière peut être enseignée dans plusieurs classes avec des
        // paramètres différents (barème, note de passage) via assign_subjects.
        // ---------------------------------------------------------------
        Schema::create('school_subjects', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique de la matière (ex : "Mathématiques", "Français")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table school_subjects.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_subjects'); // Suppression de la table des matières scolaires
    }
};
