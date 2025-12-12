<?php

namespace App\Livewire\Backend\Marks;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\AssignStudent;
use App\Models\ExamType;
use App\Models\TermType;
use App\Models\AssignSubject;
use App\Models\SchoolSubject;
use App\Models\AssignExamType;

class MarksEntry extends Component
{
    public $year_id, $class_id, $assign_subject_id, $term_type_id;
    public $years = [], $classes = [], $subjects = [], $term_types = [];
    public $students = [];
    public $examTypes = [];
    public $marks = [];
    public $inapte = [];
    public $isEPS = false;
    public $loading = false;

    public function mount()
    {
        $user = Auth::user();

        if ($user->role == 'Admin' || $user->role == 'Operator') {
            $this->years = StudentYear::all();
            $this->classes = StudentClass::all();
        } elseif ($user->usertype == 'Employé') {
            $assignedClasses = DB::table('assign_subject_teaches')
                ->where('teacher_id', $user->id)
                ->distinct()
                ->pluck('class_id')
                ->toArray();

            $this->years = StudentYear::all();
            $this->classes = StudentClass::whereIn('id', $assignedClasses)->get();
        }
    }

    public function updatedClassId()
    {
        $this->assign_subject_id = null;
        $this->term_type_id = null;
        $this->students = [];
        $this->examTypes = [];

        if ($this->class_id) {
            $this->subjects = AssignSubject::with('school_subject')
                ->where('class_id', $this->class_id)
                ->get();

            $this->term_types = $this->getTermTypesForClass($this->class_id);
        }
    }

    private function getTermTypesForClass($class_id)
    {
        $studentClass = StudentClass::find($class_id);

        if (!$studentClass) {
            return [];
        }

        $trimesterClasses = [
            '6ème A', '6ème B', '6ème C', '6ème D', 
            '5ème A', '5ème B', '5ème C', '5ème D', 
            '4ème A', '4ème B', '4ème C', '4ème D', 
            '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'
        ];

        if (in_array($studentClass->name, $trimesterClasses)) {
            return TermType::whereIn('name', ['Trimestre 1', 'Trimestre 2', 'Trimestre 3'])->get();
        } else {
            return TermType::whereIn('name', ['Semestre 1', 'Semestre 2'])->get();
        }
    }

    public function loadStudents()
    {
        if (!$this->year_id || !$this->class_id || !$this->assign_subject_id || !$this->term_type_id) {
            return;
        }

        $this->students = AssignStudent::with(['student'])
            ->where('year_id', $this->year_id)
            ->where('class_id', $this->class_id)
            ->orderBy('student_id')
            ->get();

        $this->examTypes = AssignExamType::join('exam_types', 'assign_exam_types.exam_type_id', '=', 'exam_types.id')
            ->where('assign_exam_types.term_type_id', $this->term_type_id)
            ->pluck('exam_types.name')
            ->toArray();

        $this->isEPS = SchoolSubject::where('id', $this->assign_subject_id)->value('name') === 'E.P.S';

        $existingMarks = DB::table('student_marks')
            ->where('year_id', $this->year_id)
            ->where('class_id', $this->class_id)
            ->where('assign_subject_id', $this->assign_subject_id)
            ->where('term_type_id', $this->term_type_id)
            ->get()
            ->groupBy('student_id');

        foreach ($this->students as $student) {
            foreach ($this->examTypes as $examType) {
                $mark = $existingMarks[$student->student_id]->firstWhere('exam_type_id', DB::table('exam_types')->where('name', $examType)->value('id'));
                $this->marks[$student->student_id][$examType] = $mark ? $mark->marks : null;
            }

            if ($this->isEPS) {
                $this->inapte[$student->student_id] = $existingMarks[$student->student_id]->first()?->inapte ?? 0;
            }
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['year_id', 'class_id', 'assign_subject_id', 'term_type_id'])) {
            $this->loadStudents();
        }
    }


    public function updatedMarks()
    {
        $this->saveMarks(true);
    }

    public function saveMarks($autoSave = false)
    {
        $this->loading = true;

        foreach ($this->marks as $studentId => $examScores) {
            foreach ($examScores as $examType => $score) {
                if (is_null($score)) {
                    continue;
                }

                DB::table('student_marks')->updateOrInsert(
                    [
                        'student_id' => $studentId,
                        'year_id' => $this->year_id,
                        'class_id' => $this->class_id,
                        'assign_subject_id' => $this->assign_subject_id,
                        'term_type_id' => $this->term_type_id,
                        'exam_type_id' => DB::table('exam_types')->where('name', $examType)->value('id'),
                    ],
                    [
                        'marks' => $score,
                        'inapte' => $this->isEPS ? $this->inapte[$studentId] : 0,
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->loading = false;
        if ($autoSave) {
            $this->dispatch('notify', 'Notes sauvegardées automatiquement !');
        } else {
            session()->flash('success', 'Notes sauvegardées avec succès !');
        }
    }

    public function render()
    {
        return view('livewire.backend.marks.marks-entry');
    }
}
