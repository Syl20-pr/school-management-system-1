<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_time_tables_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->constrained('student_years');
            $table->foreignId('class_id')->constrained('student_classes');
            $table->enum('term', ['first', 'second', 'third'])->default('first');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['academic_year_id', 'class_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_tables');
    }
};