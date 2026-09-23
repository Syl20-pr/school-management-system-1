<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('exam_types', function (Blueprint $table) {
            //$table->unsignedBigInteger('term_type_id')->after('name');

            // Optional: enforce foreign key constraint
            //$table->foreign('term_type_id')->references('id')->on('term_types')->onDelete('cascade');
        });
    }

    /**
     * Annulation de la migration : suppression sécurisée si la colonne existe.
     * Niveau 4 : Garantie de rollback sans erreur.
     */
    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            if (Schema::hasColumn('exam_types', 'term_type_id')) {
                // Vérifier et supprimer la contrainte si active
                try {
                    $table->dropForeign(['term_type_id']);
                } catch (\Throwable $e) {}
                $table->dropColumn('term_type_id');
            }
        });
    }
};
