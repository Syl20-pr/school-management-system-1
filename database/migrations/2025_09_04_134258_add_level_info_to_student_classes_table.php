<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->integer('level')->nullable()->after('name')
                  ->comment('Niveau numérique: 6 pour 6ème, 5 pour 5ème, etc.');
            $table->string('level_name', 50)->nullable()->after('level')
                  ->comment('Nom du niveau: 6ème, 5ème, 4ème, etc.');
        });
    }

    public function down()
    {
        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropColumn(['level', 'level_name']);
        });
    }
};