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
    Schema::table('student_promotion_histories', function (Blueprint $table) {
        $table->text('cancellation_reason')->nullable()->after('cancelled_by');
    });
}

public function down()
{
    Schema::table('student_promotion_histories', function (Blueprint $table) {
        $table->dropColumn('cancellation_reason');
    });
}
};
