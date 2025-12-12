<?php

namespace App\Http\Controllers\Backend\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Requests\PromotionStudentRequest;
use App\Models\AssignStudent;
use App\Models\StudentClass;
use App\Models\StudentGroup;
use App\Models\StudentShift;
use App\Models\StudentYear;
use App\Services\StudentRegistrationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentRegController extends Controller
{
    public function __construct(private StudentRegistrationService $service) {}

    // 👇 Vue initiale (affiche par défaut les derniers year/class si aucun filtre)
    /*public function StudentRegView(Request $request)
    {
        $data['years']   = StudentYear::all();
        $data['classes'] = StudentClass::all();

        // ⚠️ prend les valeurs envoyées par GET si elles existent, sinon defaults
        $data['year_id']  = $request->get('year_id', StudentYear::orderBy('id','desc')->first()->id);
        $data['class_id'] = $request->get('class_id', StudentClass::orderBy('id','desc')->first()->id);

        $data['allData'] = AssignStudent::with(['student','student_year','student_class'])
            ->where('year_id', $data['year_id'])
            ->where('class_id', $data['class_id'])
            ->get();

        return view('backend.student.student_reg.student_view', $data);
    }*/
    public function StudentRegView(Request $request)
    {
        $data['years'] = StudentYear::orderBy('name', 'desc')->get();
        $data['classes'] = StudentClass::orderBy('name')->get();

        // Gestion des valeurs par défaut avec vérification d'existence
        $defaultYear = StudentYear::orderBy('is_current', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        
        $defaultClass = StudentClass::orderBy('name')->first();

        // Récupération des paramètres avec fallback sécurisé
        $data['year_id'] = $request->get('year_id', $defaultYear ? $defaultYear->id : null);
        $data['class_id'] = $request->get('class_id', $defaultClass ? $defaultClass->id : null);

        // Vérification que les IDs existent vraiment dans la base
        if (!StudentYear::where('id', $data['year_id'])->exists()) {
            $data['year_id'] = $defaultYear ? $defaultYear->id : null;
        }
        
        if (!StudentClass::where('id', $data['class_id'])->exists()) {
            $data['class_id'] = $defaultClass ? $defaultClass->id : null;
        }

        // Récupération des données seulement si les filtres sont valides
        if ($data['year_id'] && $data['class_id']) {
            $data['allData'] = AssignStudent::with(['student','student_year','student_class'])
                ->where('year_id', $data['year_id'])
                ->where('class_id', $data['class_id'])
                ->get();
        } else {
            $data['allData'] = collect(); // Collection vide
        }

        return view('backend.student.student_reg.student_view', $data);
    }


    // 👇 Action de recherche (formulaire bouton "Rechercher")
    public function StudentClassYearWise(Request $request)
    {
        $request->validate([
            'year_id'  => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
        ]);

        $data['years']   = StudentYear::all();
        $data['classes'] = StudentClass::all();
        $data['year_id'] = $request->year_id;
        $data['class_id'] = $request->class_id;

        $data['allData'] = AssignStudent::with(['student','student_year','student_class'])
            ->where('year_id', $request->year_id)
            ->where('class_id', $request->class_id)
            ->get();

        return view('backend.student.student_reg.student_view', $data);
    }

    // PDF Liste
    public function GenerateClassListPDF(Request $request)
    {
        $students = AssignStudent::where('year_id', $request->year_id)
            ->where('class_id', $request->class_id)
            ->with(['student','student_year','student_class'])
            ->get()
            ->sortBy(fn($as) => $as->student->name);

        $femaleCount     = $students->filter(fn($s) => $s->student->gender === 'Féminin')->count();
        $statusclassCount= $students->filter(fn($s) => $s->student->statusclass === 'D')->count();

        $className    = optional($students->first()->student_class)->name ?? 'classe';
        $safeClass    = strtoupper(Str::slug($className, '_'));
        $filename     = "Liste_des_élèves_{$safeClass}.pdf";

        $pdf = Pdf::loadView('backend.student.student_reg.student_list_pdf', compact('students','femaleCount','statusclassCount'));
        return $pdf->setPaper('A4')->download($filename);
    }

    // Ajout élève
    public function StudentRegAdd()
    {
        $data['years']  = StudentYear::all();
        $data['classes']= StudentClass::all();
        $data['groups'] = StudentGroup::all();
        $data['shifts'] = StudentShift::all();
        return view('backend.student.student_reg.student_add', $data);
    }

    public function StudentRegStore(StoreStudentRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('student.registration.add')
            ->with(['message'=>"Inscription d'Élève Ajoutée avec Succès",'alert-type'=>'success']);
    }

    public function StudentRegEdit($student_id)
    {
        $data['years']  = StudentYear::all();
        $data['classes']= StudentClass::all();
        $data['groups'] = StudentGroup::all();
        $data['shifts'] = StudentShift::all();

        $data['editData'] = AssignStudent::with(['student','discount'])
            ->where('student_id', $student_id)->firstOrFail();

        return view('backend.student.student_reg.student_edit', $data);
    }

    public function StudentRegUpdate(UpdateStudentRequest $request, $student_id)
    {
        $this->service->update((int)$student_id, $request->validated());

        return redirect()
            ->route('student.registration.view')
            ->with(['message'=>"Inscription de l'Élève Mise à Jour avec Succès",'alert-type'=>'success']);
    }

    public function StudentRegPromotion($student_id)
    {
        $data['years']  = StudentYear::all();
        $data['classes']= StudentClass::all();
        $data['groups'] = StudentGroup::all();
        $data['shifts'] = StudentShift::all();

        $data['editData'] = AssignStudent::with(['student','discount'])
            ->where('student_id',$student_id)->firstOrFail();

        return view('backend.student.student_reg.student_promotion', $data);
    }

    public function StudentUpdatePromotion(PromotionStudentRequest $request, $student_id)
    {
        $this->service->promote((int)$student_id, $request->validated());

        return redirect()
            ->route('student.registration.view')
            ->with(['message'=>"Promotion de l'Élève Mise à Jour avec Succès",'alert-type'=>'success']);
    }

    public function StudentRegDetails($student_id)
    {
        $data['details'] = AssignStudent::with(['student','discount','student_year','student_class','group','shift'])
            ->where('student_id', $student_id)->firstOrFail();

        $pdf = Pdf::loadView('backend.student.student_reg.student_details_pdf', $data)
            ->setPaper('a4')
            ->setOptions(['tempDir' => public_path(),'chroot' => public_path()]);

        return $pdf->download('document.pdf');
    }

    public function StudentRegDelete($student_id)
    {
        try {
            $this->service->deleteCompletely((int)$student_id);
            return redirect()->back()->with([
                'message' => "Élève supprimé avec succès",
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => "Erreur lors de la suppression : " . $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
    }

    ////////////////////////////// Method pour la recherche de doublons//////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function searchByName(Request $request)
    {
        $query = trim($request->get('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $students = AssignStudent::with(['student', 'student_class', 'student_year'])
            ->whereHas('student', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get();

        return response()->json($students->map(function($s) {
            return [
                'id'        => $s->student->id,
                'name'      => $s->student->name,
                'gender'    => $s->student->gender,
                'dob'       => $s->student->dob,
                'status'    => $s->student->statusclass == 'N' ? 'Nouveau' : 'Doublant',
                'class'     => $s->student_class->name ?? null,
                'year'      => $s->student_year->name ?? null,
                'image'     => $s->student->image
                    ? asset('upload/student_images/' . $s->student->image)
                    : asset('upload/no_image.jpg')
            ];
        }));
    }

}
