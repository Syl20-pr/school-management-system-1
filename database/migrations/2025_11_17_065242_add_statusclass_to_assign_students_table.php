<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assign_students', function (Blueprint $table) {
            $table->enum('statusclass', ['N', 'D'])
                ->default('N')
                ->after('class_id')
                ->comment('N=Nouveau, D=Doublant pour cette année');
        });
    }

    public function down(): void
    {
        Schema::table('assign_students', function (Blueprint $table) {
            $table->dropColumn('statusclass');
        });
    }
};