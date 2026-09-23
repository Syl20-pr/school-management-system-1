<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration : Création de la table des barèmes de notation (grilles de notes).
 *
 * Définit la correspondance entre une plage de notes numériques et un grade
 * (mention) accompagné de sa valeur en points GPA. Utilisée pour la
 * génération des bulletins et le calcul des moyennes pondérées.
 *
 * CORRECTION appliquée :
 *   Les colonnes numériques (grade_point, start_marks, end_marks,
 *   start_point, end_point) sont désormais en float au lieu de string.
 */
return new class extends Migration
{
    /**
     * Exécution de la migration : création de la table marks_grades.
     */
    public function up(): void
    {
        // ---------------------------------------------------------------
        // TABLE : marks_grades
        // Grille de correspondance note ↔ mention ↔ points GPA.
        // Exemple : 16-20 → "Très Bien" → 4.0 points
        // Colonnes numériques corrigées de string vers float.
        // ---------------------------------------------------------------
        Schema::create('marks_grades', function (Blueprint $table) {
            $table->id();                               // Clé primaire auto-incrémentée

            // --- Libellé du grade ---
            $table->string('grade_name');              // Nom de la mention (ex : "Excellent", "Très Bien", "Passable")

            // --- Valeurs numériques (float pour permettre les décimales) ---
            $table->float('grade_point', 5, 2);        // Valeur en points GPA correspondant à ce grade (ex : 4.00)
            $table->float('start_marks', 8, 2);        // Note minimale de la plage pour ce grade (ex : 16.00)
            $table->float('end_marks', 8, 2);          // Note maximale de la plage pour ce grade (ex : 20.00)
            $table->float('start_point', 5, 2);        // Point GPA minimal de la plage (ex : 3.50)
            $table->float('end_point', 5, 2);          // Point GPA maximal de la plage (ex : 4.00)

            // --- Commentaire ---
            $table->string('remarks');                  // Remarque ou appréciation associée au grade (ex : "Félicitations")

            $table->timestamps();                       // created_at et updated_at
        });
    }

    /**
     * Annulation de la migration : suppression de la table marks_grades.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks_grades'); // Suppression de la grille de notation
    }
};
