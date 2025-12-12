<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_time_table_slots_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeTableSlotsTable extends Migration
{
    public function up()
    {
        Schema::create('time_table_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timetable_id')->constrained('time_tables')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->foreignId('period_id')->constrained('periods');
            $table->foreignId('subject_id')->constrained('school_subjects');
            //$table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms');
            $table->timestamps();
            
            $table->unique(['timetable_id', 'day_of_week', 'period_id']);
            $table->unique(['day_of_week', 'period_id', 'teacher_id'], 'teacher_time_slot_unique');
            $table->unique(['day_of_week', 'period_id', 'classroom_id'], 'classroom_time_slot_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_table_slots');
    }
}