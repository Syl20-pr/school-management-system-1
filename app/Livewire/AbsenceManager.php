<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AssignStudent;
use App\Models\StudentAbsence;
use App\Models\StudentYear;
use App\Models\StudentClass;

class AbsenceManager extends Component
{
    public $yearId;
    public $classId;
    public $typeId;
    public $students = [];
    public $years = [];
    public $classes = [];
    public $types = [];

    protected $listeners = ['classSelected' => 'updateTypesBasedOnClass'];

    public function mount()
    {
        $this->years = StudentYear::orderBy('is_current', 'desc')
            ->orderBy('name', 'desc')
            ->get();
            
        $this->classes = StudentClass::orderBy('name')->get();
        
        // Sélectionner automatiquement l'année en cours
        $currentYear = StudentYear::where('is_current', true)->first();
        if ($currentYear) {
            $this->yearId = $currentYear->id;
        }
        
        $this->types = [];
    }

    public function updatedYearId($value)
    {
        $this->validate([
            'yearId' => 'required|exists:student_years,id'
        ]);

        if ($this->classId) {
            $this->loadStudents();
        }
    }

    public function updatedClassId($value)
    {
        $this->validate([
            'classId' => 'required|exists:student_classes,id'
        ]);

        $this->updateTypesBasedOnClass();

        if ($this->yearId) {
            $this->loadStudents();
        }
    }

    public function updatedTypeId($value)
    {
        if ($this->yearId && $this->classId) {
            $this->loadStudents();
        }
    }

    public function updateTypesBasedOnClass()
    {
        if (!$this->classId) {
            $this->types = [];
            $this->typeId = null;
            return;
        }

        // Récupérer le nom de la classe
        $class = StudentClass::find($this->classId);
        
        if (!$class) {
            $this->types = [];
            $this->typeId = null;
            return;
        }

        $className = $class->name;

        // Debug dans les logs
        \Log::info('Class selected for types', [
            'class_id' => $this->classId,
            'class_name' => $className
        ]);

        $trimestreClasses = [
            '6ème A', '5ème A', '4ème A', '3ème A',
            '6ème B', '5ème B', '4ème B', '3ème B',
            '6ème C', '5ème C', '4ème C', '3ème C',
            '6ème D', '5ème D', '4ème D', '3ème E',
        ];

        if (in_array($className, $trimestreClasses)) {
            $this->types = [
                1 => '1er Trimestre',
                2 => '2ème Trimestre',
                3 => '3ème Trimestre',
            ];
        } else {
            $this->types = [
                1 => '1er Semestre',
                2 => '2ème Semestre',
            ];
        }

        $this->typeId = null;

        \Log::info('Types generated', [
            'types' => $this->types,
            'class_name' => $className
        ]);
    }

    public function loadStudents()
    {
        if (!$this->yearId || !$this->classId) {
            $this->students = [];
            return;
        }

        // Tri alphabétique par nom
        $assignments = AssignStudent::with(['student'])
            ->where('year_id', $this->yearId)
            ->where('class_id', $this->classId)
            ->get()
            ->sortBy(function($assign) {
                return $assign->student->name;
            });

        $this->students = $assignments->map(function ($assign) {
            $absence = StudentAbsence::where('student_id', $assign->student->id)
                ->where('year_id', $this->yearId)
                ->where('class_id', $this->classId)
                ->first();

            return [
                'id'       => $assign->student->id,
                'name'     => $assign->student->name,
                'gender'   => $assign->student->gender,
                'absences' => $absence->absences ?? 0,
                'absence_id' => $absence->id ?? null,
            ];
        })->values()->toArray();
    }

    public function autoSave($index)
    {
        if (!isset($this->students[$index])) return;
        
        $student = $this->students[$index];
        $absences = (int)($student['absences'] ?? 0);

        if ($absences < 0 || $absences > 40) {
            $this->dispatch('notify', 
                type: 'error', 
                message: "Les absences doivent être entre 0 et 40 heures."
            );
            return;
        }

        try {
            StudentAbsence::updateOrCreate(
                [
                    'student_id' => $student['id'],
                    'year_id'    => $this->yearId,
                    'class_id'   => $this->classId,
                ],
                [
                    'type_tri_sem' => $this->typeId,
                    'absences'     => $absences,
                ]
            );

            $this->dispatch('notify', 
                type: 'success', 
                message: "Absence sauvegardée pour {$student['name']}."
            );

        } catch (\Exception $e) {
            \Log::error('Error saving absence', ['error' => $e->getMessage()]);
            $this->dispatch('notify', 
                type: 'error', 
                message: "Erreur lors de la sauvegarde."
            );
        }
    }

    public function saveAll()
    {
        foreach ($this->students as $index => $student) {
            $this->autoSave($index);
        }

        $this->dispatch('notify', 
            type: 'success', 
            message: "Toutes les absences ont été sauvegardées."
        );
    }

    public function render()
    {
        return view('livewire.absence-manager')
            ->layout('admin.admin_master');
    }
}