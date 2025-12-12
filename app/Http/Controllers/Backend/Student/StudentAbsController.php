<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignStudent;
use App\Models\User;
use App\Models\DiscountStudent;

use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\StudentGroup;
use App\Models\StudentShift;
use DB;
//use PDF;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\StudentAbsence;

class StudentAbsController extends Controller
{
    /**
     * Display the Absence Entry Form.
     */
    public function StudentAbsenceView()
    {
        /*$data['years'] = StudentYear::all();
        $data['classes'] = StudentClass::all();
        return view('backend.student.student_absence.absence_view', $data);
        */
        return view('backend.student.student_absence.absence_view');
    }

    /**
     * Fetch students based on the selected year and class.
     */
    public function getStudentAbsences(Request $request)
    {
        $request->validate([
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
        ]);

        // Fetch students with their absence records
        $students = AssignStudent::with('student')
            ->where('year_id', $request->year_id)
            ->where('class_id', $request->class_id)
            ->get();

        $data = [];

        foreach ($students as $assign) {
            //$absences = StudentAbsence::where('student_id', $assign->student_id)
               // ->where('year_id', $request->year_id)
              //  ->where('class_id', $request->class_id)
               // ->get();

            //$data[] = [
            //    'student' => $assign->student,
            //    'absences' => $absences,
            //];
            $data[] = [
            'student' => [
                'id_no' => $assign->student->id_no,
                'name' => $assign->student->name,
                'gender' => $assign->student->gender,
            ],
            'student_id' => $assign->student->id, // Ensure this field is included
            'absences' => StudentAbsence::where('student_id', $assign->student->id)
                ->where('year_id', $request->year_id)
                ->where('class_id', $request->class_id)
                ->get(),
        ];
        }

        return response()->json($data);
    }

    /**
     * Store or Update Absences for Students.
     */
    public function storeStudentAbsences(Request $request)
{
    $validated = $request->validate([
        'student_id.*' => 'required|exists:users,id',
        'year_id' => 'required|exists:student_years,id',
        'class_id' => 'required|exists:student_classes,id',
        'type_tri_sem' => 'nullable|integer|min:1|max:3',
        'absences.*' => 'nullable|numeric|min:0|max:40',
    ]);

    // Check if student_id and absences arrays have the same length
    if (count($request->student_id) !== count($request->absences)) {
        return redirect()->back()->withErrors('Mismatch between students and absences entries.');
    }

    try {
        foreach ($request->student_id as $key => $studentId) {
            StudentAbsence::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'year_id' => $request->year_id,
                    'class_id' => $request->class_id,
                ],
                [
                    'type_tri_sem' => $request->type_tri_sem,
                    'absences' => $request->absences[$key] ?? 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Absences enregistrées avec succès.');
    } catch (\Exception $e) {
        //\Log::error('Error saving absences: ' . $e->getMessage());
        return redirect()->back()->withErrors('Une erreur est survenue lors de la sauvegarde des absences.');
    }
}


    /**
     * Auto-Save Absences via AJAX.
     */
    public function autoSaveStudentAbsence(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'type_tri_sem' => 'nullable|integer|min:1|max:3',
            'absences' => 'nullable|numeric|min:0|max:40',
        ]);

        try {
            StudentAbsence::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'year_id' => $request->year_id,
                    'class_id' => $request->class_id,
                ],
                [
                    'type_tri_sem' => $request->type_tri_sem,
                    'absences' => $request->absences ?? 0,
                ]
            );

            return response()->json(['success' => true, 'message' => 'Absence auto-sauvegardée avec succès.']);
        } catch (\Exception $e) {
            \Log::error('Error auto-saving absence: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'auto-sauvegarde des absences.'], 500);
        }
    } 


}
