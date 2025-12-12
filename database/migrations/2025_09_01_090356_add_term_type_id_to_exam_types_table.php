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

    public function down()
    {
        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropForeign(['term_type_id']);
            $table->dropColumn('term_type_id');
        });
    }
};
