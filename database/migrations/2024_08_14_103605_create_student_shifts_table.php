<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des horaires (shifts) d'élèves.
 *
 * Certains établissements fonctionnent en plusieurs créneaux horaires
 * (ex : matin, après-midi). Cette table référence ces créneaux pour
 * permettre d'affecter chaque élève à un horaire précis.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table student_shifts.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : student_shifts
        // Référentiel des créneaux horaires (shifts) de l'établissement.
        // Utilisé lors de l'inscription d'un élève dans une classe pour
        // définir s'il suit les cours le matin, l'après-midi, etc.
        // ---------------------------------------------------------------
        Schema::create('student_shifts', function (Blueprint $table) {
            $table->id();                           // Clé primaire auto-incrémentée
            $table->string('name')->unique();       // Nom unique du créneau (ex : "Matin", "Après-midi")
            $table->timestamps();                   // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table student_shifts.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_shifts'); // Suppression de la table des créneaux horaires
    }
};
