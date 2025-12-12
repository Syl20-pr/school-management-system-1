<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_types', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas déjà
            if (!Schema::hasColumn('exam_types', 'term_type_id')) {
                $table->unsignedBigInteger('term_type_id')->after('name');
            }

            // Ajoute la contrainte de clé étrangère uniquement si elle n’existe pas
            $table->foreign('term_type_id')
                  ->references('id')
                  ->on('term_types')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropForeign(['term_type_id']);
            // $table->dropColumn('term_type_id'); // Décommente si tu veux supprimer aussi la colonne
        });
    }
};
