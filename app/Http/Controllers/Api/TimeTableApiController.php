<?php
// app/Http/Controllers/Api/TimeTableApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeTable;
use App\Models\TimeTableSlot;
use App\Models\User;
use App\Models\StudentClass;
use Illuminate\Http\Request;

class TimeTableApiController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeTable::with(['academicYear', 'class']);
        
        if ($request->has('year_id')) {
            $query->where('academic_year_id', $request->year_id);
        }
        
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        $timetables = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($timetables);
    }

    public function show($id)
    {
        $timetable = TimeTable::with([
            'slots.period', 
            'slots.subject', 
            'slots.teacher.user', 
            'slots.classroom'
        ])->findOrFail($id);
        
        return response()->json($timetable);
    }

    /* public function teacherTimetable($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);
        
        $slots = TimeTableSlot::with([
            'timetable.class', 
            'period', 
            'subject', 
            'classroom'
        ])
        ->where('teacher_id', $teacherId)
        ->get()
        ->groupBy('day_of_week');
        
        return response()->json([
            'teacher' => $teacher,
            'timetable' => $slots
        ]);
    } */

    public function teacherTimetable($teacherId)
    {
        // Use User model directly with proper filters
        $teacher = User::where('usertype', 'Employee')
                    ->where('role', 'teacher')
                    ->findOrFail($teacherId);

        $slots = TimeTableSlot::with([
            'timetable.class', 
            'period', 
            'subject', 
            'classroom'
        ])
        ->where('teacher_id', $teacherId)
        ->get()
        ->groupBy('day_of_week');

        return response()->json([
            'teacher' => $teacher,
            'timetable' => $slots
        ]);
    }

    public function classTimetable($classId)
    {
        $class = StudentClass::findOrFail($classId);
        
        $timetable = TimeTable::with([
            'slots.period', 
            'slots.subject', 
            'slots.teacher.user', 
            'slots.classroom'
        ])
        ->where('class_id', $classId)
        ->active()
        ->first();
        
        return response()->json([
            'class' => $class,
            'timetable' => $timetable
        ]);
    }
}