<?php
// app/Services/TimeTableGeneratorService.php
namespace App\Services\Timetable;

use App\Models\TimeTable;
use App\Models\TimeTableSlot;
use App\Models\Period;
use App\Models\User;
use App\Models\Classroom;
use App\Models\SchoolSubject;
use App\Models\TeacherUnavailability;
use Illuminate\Support\Facades\DB;
use Exception;

class TimeTableGeneratorService
{
    protected $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    
    public function generate(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Vérifier si un emploi du temps existe déjà
            $existingTimetable = TimeTable::where([
                'academic_year_id' => $data['academic_year_id'],
                'class_id' => $data['class_id'],
                'term' => $data['term']
            ])->first();
            
            if ($existingTimetable) {
                $existingTimetable->slots()->delete();
                $timetable = $existingTimetable;
            } else {
                $timetable = TimeTable::create([
                    'name' => "Emploi du temps " . $data['class_id'] . " - " . $data['term'],
                    'academic_year_id' => $data['academic_year_id'],
                    'class_id' => $data['class_id'],
                    'term' => $data['term'],
                    'is_active' => false
                ]);
            }
            
            // Récupérer les données nécessaires
            $subjects = SchoolSubject::all();
            //$teachers = Teacher::with(['unavailabilities', 'subjects'])->get();
            $teachers = User::where('role', 'teacher') // or 'usertype' depending on your logic
                ->with(['unavailabilities', 'subjects'])
                ->get();
            $classrooms = Classroom::where('is_available', true)->get();
            $periods = Period::orderBy('order')->get();
            
            // Générer l'emploi du temps
            $generatedSlots = $this->generateTimetable(
                $timetable, 
                $subjects, 
                $teachers, 
                $classrooms, 
                $periods, 
                $data
            );
            
            // Enregistrer les créneaux
            foreach ($generatedSlots as $slot) {
                TimeTableSlot::create($slot);
            }
            
            DB::commit();
            
            return $timetable->load('slots');
            
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Erreur lors de la génération: ' . $e->getMessage());
        }
    }
    
    protected function generateTimetable($timetable, $subjects, $teachers, $classrooms, $periods, $data)
    {
        $slots = [];
        $teacherSchedule = [];
        $classroomSchedule = [];
        $maxHoursPerTeacher = $data['max_hours_per_teacher'] ?? 20;
        
        // Initialiser les emplois du temps des enseignants et salles
        foreach ($teachers as $teacher) {
            foreach ($this->days as $day) {
                foreach ($periods as $period) {
                    $teacherSchedule[$teacher->id][$day][$period->id] = false;
                }
            }
        }
        
        foreach ($classrooms as $classroom) {
            foreach ($this->days as $day) {
                foreach ($periods as $period) {
                    $classroomSchedule[$classroom->id][$day][$period->id] = false;
                }
            }
        }
        
        // Générer les créneaux pour chaque matière
        foreach ($subjects as $subject) {
            $subjectTeachers = $teachers->filter(function($teacher) use ($subject) {
                return $teacher->subjects->contains($subject->id);
            });
            
            if ($subjectTeachers->isEmpty()) {
                continue;
            }
            
            $hoursPerWeek = $subject->hours_per_week ?? 4;
            $assignedHours = 0;
            
            while ($assignedHours < $hoursPerWeek) {
                foreach ($this->days as $day) {
                    foreach ($periods as $period) {
                        if ($assignedHours >= $hoursPerWeek) {
                            break 2;
                        }
                        
                        // Trouver un enseignant disponible
                        $availableTeacher = $this->findAvailableTeacher(
                            $subjectTeachers, 
                            $teacherSchedule, 
                            $day, 
                            $period->id,
                            $maxHoursPerTeacher
                        );
                        
                        if (!$availableTeacher) {
                            continue;
                        }
                        
                        // Trouver une salle disponible
                        $availableClassroom = $this->findAvailableClassroom(
                            $classrooms, 
                            $classroomSchedule, 
                            $day, 
                            $period->id
                        );
                        
                        if (!$availableClassroom) {
                            continue;
                        }
                        
                        // Créer le créneau
                        $slots[] = [
                            'timetable_id' => $timetable->id,
                            'day_of_week' => $day,
                            'period_id' => $period->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $availableTeacher->id,
                            'classroom_id' => $availableClassroom->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        
                        // Marquer comme occupé
                        $teacherSchedule[$availableTeacher->id][$day][$period->id] = true;
                        $classroomSchedule[$availableClassroom->id][$day][$period->id] = true;
                        
                        $assignedHours++;
                        
                        if ($assignedHours >= $hoursPerWeek) {
                            break 2;
                        }
                    }
                }
            }
        }
        
        return $slots;
    }
    
    protected function findAvailableTeacher($teachers, $teacherSchedule, $day, $periodId, $maxHours)
    {
        foreach ($teachers as $teacher) {
            // Vérifier si l'enseignant est disponible à ce créneau
            if (isset($teacherSchedule[$teacher->id][$day][$periodId]) && 
                !$teacherSchedule[$teacher->id][$day][$periodId]) {
                
                // Vérifier les indisponibilités
                $isUnavailable = TeacherUnavailability::where('teacher_id', $teacher->id)
                    ->where(function($query) use ($day, $periodId) {
                        $query->where(function($q) use ($day, $periodId) {
                            $q->where('day_of_week', $day)
                              ->where('period_id', $periodId);
                        })->orWhere('specific_date', now()->format('Y-m-d'));
                    })
                    ->exists();
                
                if (!$isUnavailable) {
                    // Vérifier le nombre d'heures déjà assignées
                    $assignedHours = $this->countAssignedHours($teacherSchedule, $teacher->id);
                    if ($assignedHours < $maxHours) {
                        return $teacher;
                    }
                }
            }
        }
        
        return null;
    }
    
    protected function findAvailableClassroom($classrooms, $classroomSchedule, $day, $periodId)
    {
        foreach ($classrooms as $classroom) {
            if (isset($classroomSchedule[$classroom->id][$day][$periodId]) && 
                !$classroomSchedule[$classroom->id][$day][$periodId]) {
                return $classroom;
            }
        }
        
        return null;
    }
    
    protected function countAssignedHours($teacherSchedule, $teacherId)
    {
        $count = 0;
        foreach ($teacherSchedule[$teacherId] as $day => $periods) {
            foreach ($periods as $occupied) {
                if ($occupied) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
    public function analyzeTimetable(TimeTable $timetable)
    {
        $analysis = [
            'teacher_utilization' => [],
            'classroom_utilization' => [],
            'conflicts' => [],
            'stats' => [
                'total_slots' => 0,
                'teacher_hours' => [],
                'free_periods' => 0
            ]
        ];
        
        $slots = $timetable->slots;
        $analysis['stats']['total_slots'] = $slots->count();
        
        // Analyser l'utilisation des enseignants
        foreach ($slots->groupBy('teacher_id') as $teacherId => $teacherSlots) {
            $analysis['teacher_utilization'][$teacherId] = [
                'total_hours' => $teacherSlots->count(),
                'days' => $teacherSlots->groupBy('day_of_week')->map->count()
            ];
            
            $analysis['stats']['teacher_hours'][$teacherId] = $teacherSlots->count();
        }
        
        // Analyser l'utilisation des salles
        foreach ($slots->groupBy('classroom_id') as $classroomId => $classroomSlots) {
            $analysis['classroom_utilization'][$classroomId] = [
                'total_hours' => $classroomSlots->count(),
                'utilization_rate' => ($classroomSlots->count() / (count($this->days) * Period::count())) * 100
            ];
        }
        
        // Détecter les conflits
        $analysis['conflicts'] = $this->detectConflicts($timetable);
        
        return $analysis;
    }
    
    protected function detectConflicts(TimeTable $timetable)
    {
        $conflicts = [];
        $slots = $timetable->slots;
        
        // Conflits enseignants (double réservation)
        $teacherSlots = $slots->groupBy(['teacher_id', 'day_of_week', 'period_id']);
        foreach ($teacherSlots as $teacherId => $days) {
            foreach ($days as $day => $periods) {
                foreach ($periods as $periodId => $slotGroup) {
                    if ($slotGroup->count() > 1) {
                        $conflicts[] = [
                            'type' => 'teacher_double_booking',
                            'teacher_id' => $teacherId,
                            'day' => $day,
                            'period_id' => $periodId,
                            'slots' => $slotGroup->pluck('id')
                        ];
                    }
                }
            }
        }
        
        // Conflits salles (double réservation)
        $classroomSlots = $slots->groupBy(['classroom_id', 'day_of_week', 'period_id']);
        foreach ($classroomSlots as $classroomId => $days) {
            foreach ($days as $day => $periods) {
                foreach ($periods as $periodId => $slotGroup) {
                    if ($slotGroup->count() > 1) {
                        $conflicts[] = [
                            'type' => 'classroom_double_booking',
                            'classroom_id' => $classroomId,
                            'day' => $day,
                            'period_id' => $periodId,
                            'slots' => $slotGroup->pluck('id')
                        ];
                    }
                }
            }
        }
        
        return $conflicts;
    }
}