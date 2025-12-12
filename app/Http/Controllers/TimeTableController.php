<?php
// app/Http/Controllers/TimeTableController.php
namespace App\Http\Controllers;

use App\Models\TimeTable;
use App\Models\TimeTableSlot;
use App\Models\Period;
use App\Models\Classroom;
use App\Models\User;
use App\Models\StudentClass;
use App\Models\StudentYear;
use App\Models\SchoolSubject;
use App\Models\TeacherUnavailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Timetable\TimeTableGeneratorService;
use App\Http\Requests\Timetable\StoreTimeTableRequest;
use App\Http\Requests\Timetable\UpdateTimeTableRequest;
use App\Http\Requests\Timetable\GenerateTimeTableRequest;

class TimeTableController extends Controller
{
    protected $generatorService;

    public function __construct(TimeTableGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    public function index(Request $request)
    {
        $years = StudentYear::orderBy('name', 'desc')->get();
        $classes = StudentClass::all();
        
        $query = TimeTable::with(['academicYear', 'class', 'slots']);
        
        if ($request->has('year_id') && $request->year_id) {
            $query->where('academic_year_id', $request->year_id);
        }
        
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        $timetables = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('backend.timetable.index', compact('timetables', 'years', 'classes'));
    }

    /* public function create()
    {
        $years = StudentYear::orderBy('name', 'desc')->get();
        $classes = StudentClass::all();
        $periods = Period::orderBy('order')->get();
        $teachers = Teacher::with('user')->get();
        $classrooms = Classroom::where('is_available', true)->get();
        $subjects = SchoolSubject::all();
        
        return view('backend.timetable.create', compact(
            'years', 'classes', 'periods', 'teachers', 'classrooms', 'subjects'
        ));
    } */

    public function create()
    {
        $years = StudentYear::orderBy('name', 'desc')->get();
        $classes = StudentClass::all();
        $periods = Period::orderBy('order')->get();

        // Correct teacher query
        $teachers = User::where('usertype', 'Employé')
                        ->where('role', 'enseignant')
                        ->get(); // ✅

        $classrooms = Classroom::where('is_available', true)->get();
        $subjects = SchoolSubject::all();
        
        return view('backend.timetable.create', compact(
            'years', 'classes', 'periods', 'teachers', 'classrooms', 'subjects'
        ));
    }

    public function store(StoreTimeTableRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $timetable = TimeTable::create([
                'name' => $request->name,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $request->class_id,
                'term' => $request->term,
                'is_active' => $request->has('is_active')
            ]);
            
            if ($request->has('slots')) {
                foreach ($request->slots as $slot) {
                    TimeTableSlot::create([
                        'timetable_id' => $timetable->id,
                        'day_of_week' => $slot['day_of_week'],
                        'period_id' => $slot['period_id'],
                        'subject_id' => $slot['subject_id'],
                        'teacher_id' => $slot['teacher_id'],
                        'classroom_id' => $slot['classroom_id']
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('timetable.index')
                ->with('success', 'Emploi du temps créé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(TimeTable $timetable)
    {
        $timetable->load(['slots.period', 'slots.subject', 'slots.teacher', 'slots.classroom']);
        
        return view('backend.timetable.show', compact('timetable'));
    }

    /* public function edit(TimeTable $timetable)
    {
        $timetable->load('slots');
        $years = StudentYear::orderBy('name', 'desc')->get();
        $classes = StudentClass::all();
        $periods = Period::orderBy('order')->get();
        $teachers = Teacher::with('user')->get();
        $classrooms = Classroom::where('is_available', true)->get();
        $subjects = SchoolSubject::all();
        
        return view('backend.timetable.edit', compact(
            'timetable', 'years', 'classes', 'periods', 'teachers', 'classrooms', 'subjects'
        ));
    } */

    public function edit(TimeTable $timetable)
    {
        $timetable->load('slots');
        $years = StudentYear::orderBy('name', 'desc')->get();
        $classes = StudentClass::all();
        $periods = Period::orderBy('order')->get();

        // Filter users who are teachers
        $teachers = User::where('usertype', 'Employé')->where('role', 'enseignant')->get(); // ✅

        $classrooms = Classroom::where('is_available', true)->get();
        $subjects = SchoolSubject::all();
        
        return view('backend.timetable.edit', compact(
            'timetable', 'years', 'classes', 'periods', 'teachers', 'classrooms', 'subjects'
        ));
    }

    public function update(UpdateTimeTableRequest $request, TimeTable $timetable)
    {
        DB::beginTransaction();
        
        try {
            $timetable->update([
                'name' => $request->name,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $request->class_id,
                'term' => $request->term,
                'is_active' => $request->has('is_active')
            ]);
            
            // Supprimer les anciens slots
            $timetable->slots()->delete();
            
            // Ajouter les nouveaux slots
            if ($request->has('slots')) {
                foreach ($request->slots as $slot) {
                    TimeTableSlot::create([
                        'timetable_id' => $timetable->id,
                        'day_of_week' => $slot['day_of_week'],
                        'period_id' => $slot['period_id'],
                        'subject_id' => $slot['subject_id'],
                        'teacher_id' => $slot['teacher_id'],
                        'classroom_id' => $slot['classroom_id']
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('timetable.index')
                ->with('success', 'Emploi du temps mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(TimeTable $timetable)
    {
        DB::beginTransaction();
        
        try {
            $timetable->slots()->delete();
            $timetable->delete();
            
            DB::commit();
            
            return redirect()->route('timetable.index')
                ->with('success', 'Emploi du temps supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function generate(GenerateTimeTableRequest $request)
    {
        try {
            $timetable = $this->generatorService->generate($request->validated());
            
            return redirect()->route('timetable.show', $timetable->id)
                ->with('success', 'Emploi du temps généré avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la génération: ' . $e->getMessage())
                ->withInput();
        }
    }

    /* public function teacherView($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);
        $slots = TimeTableSlot::with(['timetable.class', 'period', 'subject', 'classroom'])
            ->where('teacher_id', $teacherId)
            ->orderBy('day_of_week')
            ->orderBy('period_id')
            ->get()
            ->groupBy('day_of_week');
        
        return view('backend.timetable.teacher-view', compact('teacher', 'slots'));
    } */

    public function teacherView($teacherId)
    {
        $teacher = User::findOrFail($teacherId);

        // Optional check: confirm this is a teacher
        if ($teacher->usertype !== 'Employé' || $teacher->role !== 'enseignant') {
            abort(403, 'Vous n\'êtes pas un Enseignant');
        }

        $slots = TimeTableSlot::with(['timetable.class', 'period', 'subject', 'classroom'])
            ->where('teacher_id', $teacherId)
            ->orderBy('day_of_week')
            ->orderBy('period_id')
            ->get()
            ->groupBy('day_of_week');

        return view('backend.timetable.teacher-view', compact('teacher', 'slots'));
    }


    public function classView($classId)
    {
        $class = StudentClass::findOrFail($classId);
        $timetable = TimeTable::with(['slots.period', 'slots.subject', 'slots.teacher', 'slots.classroom'])
            ->where('class_id', $classId)
            ->active()
            ->first();
        
        if (!$timetable) {
            return redirect()->back()
                ->with('warning', 'Aucun emploi du temps actif trouvé pour cette classe.');
        }
        
        return view('backend.timetable.class-view', compact('class', 'timetable'));
    }

    public function toggleActive(TimeTable $timetable)
    {
        DB::beginTransaction();
        
        try {
            // Désactiver tous les autres emplois du temps pour cette classe et année
            TimeTable::where('academic_year_id', $timetable->academic_year_id)
                ->where('class_id', $timetable->class_id)
                ->where('term', $timetable->term)
                ->update(['is_active' => false]);
            
            // Activer cet emploi du temps
            $timetable->update(['is_active' => !$timetable->is_active]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Statut d\'activation modifié avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }
}