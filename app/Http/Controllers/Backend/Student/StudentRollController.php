<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignStudent;
use App\Models\StudentYear;
use App\Models\StudentClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

use ZipArchive;
use Illuminate\Support\Facades\Log;

class StudentRollController extends Controller
{
    public function StudentRollView(){
        // Récupérer l'année en cours par défaut
        $currentYear = StudentYear::where('is_current', true)->first();
        
        $data['years'] = StudentYear::orderBy('is_current', 'desc')
            ->orderBy('name', 'desc')
            ->get();
            
        $data['selected_year'] = $currentYear ? $currentYear->id : null;
        
        // Récupérer les classes de l'année en cours
        if ($currentYear) {
            // Méthode alternative sans utiliser whereHas
            $data['classes'] = StudentClass::whereIn('id', function($query) use ($currentYear) {
                $query->select('class_id')
                      ->from('assign_students')
                      ->where('year_id', $currentYear->id)
                      ->groupBy('class_id');
            })->orderBy('name')->get();
        } else {
            $data['classes'] = collect();
        }

        return view('backend.student.roll_generate.roll_generate_view', $data);
    }

    public function GetClassesByYear(Request $request){
        $year_id = $request->year_id;
        
        // Méthode alternative sans relation
        $classes = StudentClass::whereIn('id', function($query) use ($year_id) {
            $query->select('class_id')
                  ->from('assign_students')
                  ->where('year_id', $year_id)
                  ->groupBy('class_id');
        })->orderBy('name')->get();

        // Ajouter le nombre d'étudiants par classe
        $classes->each(function($class) use ($year_id) {
            $class->students_count = AssignStudent::where('class_id', $class->id)
                ->where('year_id', $year_id)
                ->count();
        });

        return response()->json($classes);
    }

    /* public function GenerateRegisterPDF(Request $request){
        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $type = $request->type; // 'attendance' ou 'grades'

        $year = StudentYear::findOrFail($year_id);
        $class = StudentClass::findOrFail($class_id);

        // Récupérer les étudiants avec leurs rôles
        $students = AssignStudent::with(['student'])
            ->where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->orderBy('roll')
            ->get();

        if ($type === 'attendance') {
            // PDF pour les présences
            $pdf = Pdf::loadView('backend.student.roll_generate.pdf.attendance_register', 
                compact('students', 'year', 'class'));
            
            $filename = "Registre_Presence_{$class->name}_{$year->name}.pdf";
            
        } else {
            // PDF pour les notes
            $pdf = Pdf::loadView('backend.student.roll_generate.pdf.grades_register', 
                compact('students', 'year', 'class'));
            
            $filename = "Carnet_Notes_{$class->name}_{$year->name}.pdf";
        }

        return $pdf->download($filename);
    } */

    public function GenerateRegisterPDF(Request $request)
    {
        // Log de débogage
        Log::info('GenerateRegisterPDF called', [
            'all_params' => $request->all(),
            'year_id' => $request->year_id,
            'class_id' => $request->class_id,
            'type' => $request->type,
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        // Validation des paramètres
        $validated = $request->validate([
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'type' => 'required|in:attendance,grades'
        ]);

        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $type = $request->type;

        Log::info('Params after validation', [
            'year_id' => $year_id,
            'class_id' => $class_id,
            'type' => $type
        ]);

        try {
            $year = StudentYear::findOrFail($year_id);
            $class = StudentClass::findOrFail($class_id);

            // Récupérer les étudiants avec leurs rôles
            /* $students = AssignStudent::with(['student'])
                ->where('year_id', $year_id)
                ->where('class_id', $class_id)
                ->orderBy('roll')
                ->get();
 */
            $students = AssignStudent::with(['student'])
            ->where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->join('users', 'assign_students.student_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('assign_students.*')
            ->get();

            Log::info('Students found', ['count' => $students->count()]);

            if ($students->isEmpty()) {
                Log::warning('Aucun étudiant trouvé', ['year_id' => $year_id, 'class_id' => $class_id]);
                return response()->json(['error' => 'Aucun étudiant trouvé'], 404);
            }

            if ($type === 'attendance') {
                $pdf = Pdf::loadView('backend.student.roll_generate.pdf.attendance_register', 
                    compact('students', 'year', 'class'));
                $filename = "Registre_Presence_{$class->name}_{$year->name}.pdf";
            } else {
                $pdf = Pdf::loadView('backend.student.roll_generate.pdf.grades_register', 
                    compact('students', 'year', 'class'));
                $filename = "Carnet_Notes_{$class->name}_{$year->name}.pdf";
            }

            Log::info('PDF generated successfully', ['filename' => $filename]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }

    /* public function GenerateAllRegisters(Request $request){
        $year_id = $request->year_id;
        $type = $request->type;
        
        $year = StudentYear::findOrFail($year_id);
        
        // Récupérer les classes avec des étudiants cette année
        $classes = StudentClass::whereIn('id', function($query) use ($year_id) {
            $query->select('class_id')
                  ->from('assign_students')
                  ->where('year_id', $year_id)
                  ->groupBy('class_id');
        })->get();

        // Créer un ZIP avec tous les PDFs
        $zipFileName = "Registres_{$year->name}_" . ($type === 'attendance' ? 'Presences' : 'Notes') . ".zip";
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Créer le dossier temp s'il n'existe pas
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($classes as $class) {
                $students = AssignStudent::with(['student'])
                    ->where('year_id', $year_id)
                    ->where('class_id', $class->id)
                    ->orderBy('roll')
                    ->get();

                if ($students->count() > 0) {
                    if ($type === 'attendance') {
                        $pdf = Pdf::loadView('backend.student.roll_generate.pdf.attendance_register', 
                            compact('students', 'year', 'class'));
                        $filename = "Presence_{$class->name}.pdf";
                    } else {
                        $pdf = Pdf::loadView('backend.student.roll_generate.pdf.grades_register', 
                            compact('students', 'year', 'class'));
                        $filename = "Notes_{$class->name}.pdf";
                    }

                    // Sauvegarder temporairement le PDF
                    $tempPdfPath = storage_path("app/temp/{$filename}");
                    $pdf->save($tempPdfPath);
                    
                    // Ajouter au ZIP
                    $zip->addFile($tempPdfPath, $filename);
                }
            }
            $zip->close();
            
            // Nettoyer les fichiers temporaires
            foreach (glob(storage_path('app/temp/*.pdf')) as $file) {
                unlink($file);
            }
            
            return response()->download($zipPath)->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'Erreur lors de la génération des fichiers');
    } */
   public function GenerateAllRegisters(Request $request)
    {
        // Log de débogage
        Log::info('GenerateAllRegisters called', [
            'all_params' => $request->all(),
            'year_id' => $request->year_id,
            'type' => $request->type,
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        // Validation des paramètres
        $validated = $request->validate([
            'year_id' => 'required|exists:student_years,id',
            'type' => 'required|in:attendance,grades'
        ]);

        $year_id = $request->year_id;
        $type = $request->type;

        Log::info('Params after validation', [
            'year_id' => $year_id,
            'type' => $type
        ]);

        try {
            $year = StudentYear::findOrFail($year_id);
            
            // Récupérer les classes avec des étudiants cette année
            $classes = StudentClass::whereIn('id', function($query) use ($year_id) {
                $query->select('class_id')
                      ->from('assign_students')
                      ->where('year_id', $year_id)
                      ->groupBy('class_id');
            })->get();

            Log::info('Classes found', ['count' => $classes->count()]);

            if ($classes->isEmpty()) {
                Log::warning('Aucune classe trouvée', ['year_id' => $year_id]);
                return response()->json(['error' => 'Aucune classe trouvée'], 404);
            }

            // Créer un ZIP avec tous les PDFs
            $zipFileName = "Registres_{$year->name}_" . ($type === 'attendance' ? 'Presences' : 'Notes') . ".zip";
            $zipPath = storage_path('app/temp/' . $zipFileName);
            
            // Créer le dossier temp s'il n'existe pas
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                foreach ($classes as $class) {
                    $students = AssignStudent::with(['student'])
                        ->where('year_id', $year_id)
                        ->where('class_id', $class->id)
                        ->orderBy('roll')
                        ->get();

                    if ($students->count() > 0) {
                        if ($type === 'attendance') {
                            $pdf = Pdf::loadView('backend.student.roll_generate.pdf.attendance_register', 
                                compact('students', 'year', 'class'));
                            $filename = "Presence_{$class->name}.pdf";
                        } else {
                            $pdf = Pdf::loadView('backend.student.roll_generate.pdf.grades_register', 
                                compact('students', 'year', 'class'));
                            $filename = "Notes_{$class->name}.pdf";
                        }

                        // Sauvegarder temporairement le PDF
                        $tempPdfPath = storage_path("app/temp/{$filename}");
                        $pdf->save($tempPdfPath);
                        
                        // Ajouter au ZIP
                        $zip->addFile($tempPdfPath, $filename);
                        
                        Log::info('PDF added to ZIP', ['filename' => $filename]);
                    }
                }
                $zip->close();
                
                // Nettoyer les fichiers temporaires
                foreach (glob(storage_path('app/temp/*.pdf')) as $file) {
                    unlink($file);
                }
                
                Log::info('ZIP created successfully', ['filename' => $zipFileName]);
                
                return response()->download($zipPath)->deleteFileAfterSend(true);
            }
            
            Log::error('Failed to create ZIP archive');
            return response()->json(['error' => 'Erreur lors de la création du ZIP'], 500);

        } catch (\Exception $e) {
            Log::error('Error generating ZIP: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erreur lors de la génération des fichiers: ' . $e->getMessage()], 500);
        }
    }
    
}