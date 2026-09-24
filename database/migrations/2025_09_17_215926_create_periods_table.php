<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_periods_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('name')->nullable();
            $table->integer('order');
            $table->boolean('is_break')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};