<?php

namespace App\Http\Controllers\Backend\Marks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\StudentMarks;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\ExamType;
use App\Models\AssignSubject;
use App\Models\AssignStudent;
use App\Models\TermType;

class MarksController extends Controller
{
    public function MarksManage()
    {
        $user = auth()->user();
        $isEPS = false;

        $currentYear = StudentYear::orderBy('name', 'desc')->first();

        if ($user->role == 'Admin' || $user->role == 'Operator') {
            $data['years'] = StudentYear::all();
            $data['current_year'] = $currentYear;
            $data['classes'] = StudentClass::orderBy('name')->get();
            $data['exam_types'] = ExamType::where('name', 'not like', 'bulletin%')->get();
            $data['subjects'] = AssignSubject::with('school_subject')->get();
            $data['term_types'] = TermType::all();
        } elseif ($user->usertype == 'Employé') {
            $assignedClasses = DB::table('assign_subject_teaches')
                ->where('teacher_id', $user->id)
                ->distinct()->pluck('class_id')->toArray();

            $assignedSubjects = DB::table('assign_subject_teaches')
                ->where('teacher_id', $user->id)
                ->distinct()->pluck('subject_id')->toArray();

            $data['years'] = StudentYear::all();
            $data['current_year'] = $currentYear;
            $data['classes'] = StudentClass::whereIn('id', $assignedClasses)->orderBy('name')->get();
            $data['exam_types'] = ExamType::where('name', 'not like', 'bulletin%')->get();
            $data['subjects'] = AssignSubject::whereIn('subject_id', $assignedSubjects)->with('school_subject')->get();
            $data['term_types'] = TermType::all();

            $isEPS = AssignSubject::whereIn('subject_id', $assignedSubjects)
                ->whereHas('school_subject', function ($q) { $q->where('name', 'E.P.S'); })
                ->exists();
        }

        $data['isEPS'] = $isEPS;
        return view('backend.marks.marks_manage', $data);
    }

    /* public function MarksStore(Request $request): JsonResponse
    {
        $request->validate([
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'assign_subject_id' => 'required|exists:assign_subjects,subject_id', // tu envoies l’ID du school_subject
            'term_type_id' => 'required|exists:term_types,id',
            'student_id' => 'required|array',
            'student_id.*' => 'exists:users,id',
            'Interro' => 'nullable|array',
            'Devoir' => 'nullable|array',
            'Compo' => 'nullable|array',
            'inapte' => 'nullable|array',
            'is_auto_save' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $year_id = $request->year_id;
            $class_id = $request->class_id;
            $assign_subject_id = $request->assign_subject_id; // = school_subjects.id
            $term_type_id = $request->term_type_id;
            $student_ids = $request->student_id;
            $isAutoSave = (bool)($request->is_auto_save ?? false);

            // Est-ce l'EPS ?
            $isEPS = DB::table('school_subjects')->where('id', $assign_subject_id)->value('name') === 'E.P.S';

            // Dériver les noms/ids d'examens
            $termType = TermType::find($term_type_id);
            $examNames = $this->getExamNamesByTerm($termType->name ?? '');
            $examTypes = ExamType::whereIn('name', array_values($examNames))->get();

            $examTypeIds = [
                'Interro' => optional($examTypes->firstWhere('name', $examNames['Interro']))->id,
                'Devoir'  => optional($examTypes->firstWhere('name', $examNames['Devoir']))->id,
                'Compo'   => optional($examTypes->firstWhere('name', $examNames['Compo']))->id,
            ];

            $interroMarks = $request->input('Interro', []);
            $devoirMarks  = $request->input('Devoir', []);
            $compoMarks   = $request->input('Compo', []);
            $inapteValues = $request->input('inapte', []);

            $now = now();

            foreach ($student_ids as $index => $student_id) {
                $currentInapte = (int)($inapteValues[$index] ?? 0);

                // ——— 1) Upsert dans la table "globale" d’inaptitude (source officielle) ———
                DB::table('student_inaptitudes')->updateOrInsert(
                    [
                        'student_id' => $student_id,
                        'year_id' => $year_id,
                        'class_id' => $class_id,
                        'assign_subject_id' => $assign_subject_id,
                        'term_type_id' => $term_type_id,
                    ],
                    [
                        'inapte' => $currentInapte,
                        'updated_at' => $now,
                        'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                    ]
                );

                // ——— 2) Si EPS & inapte → on force les notes à null (comportement actuel conservé) ———
                $interroVal = $isEPS && $currentInapte ? null : ($interroMarks[$index] ?? null);
                $devoirVal  = $isEPS && $currentInapte ? null : ($devoirMarks[$index] ?? null);
                $compoVal   = $isEPS && $currentInapte ? null : ($compoMarks[$index] ?? null);

                // ——— 3) Upsert dans student_marks (compat + inapte copié) ———
                if ($examTypeIds['Interro']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Interro'],
                        ],
                        [
                            'marks' => $interroVal,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }
                if ($examTypeIds['Devoir']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Devoir'],
                        ],
                        [
                            'marks' => $devoirVal,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }
                if ($examTypeIds['Compo']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Compo'],
                        ],
                        [
                            'marks' => $compoVal,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isAutoSave ? 'Sauvegarde automatique effectuée' : 'Notes enregistrées avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur sauvegarde notes: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    } */
    public function MarksStore(Request $request): JsonResponse
    {
        // Validation assouplie si auto-save
        if ($request->is_auto_save) {
            $request->validate([
                'year_id' => 'required|exists:student_years,id',
                'class_id' => 'required|exists:student_classes,id',
                'assign_subject_id' => 'required|exists:assign_subjects,subject_id',
                'term_type_id' => 'required|exists:term_types,id',
                'student_id' => 'required|array',
                'student_id.*' => 'exists:users,id',
            ]);
        } else {
            // Validation stricte pour sauvegarde globale
            $request->validate([
                'year_id' => 'required|exists:student_years,id',
                'class_id' => 'required|exists:student_classes,id',
                'assign_subject_id' => 'required|exists:assign_subjects,subject_id',
                'term_type_id' => 'required|exists:term_types,id',
                'student_id' => 'required|array',
                'student_id.*' => 'exists:users,id',
                'Interro' => 'nullable|array',
                'Devoir' => 'nullable|array',
                'Compo' => 'nullable|array',
                'inapte' => 'nullable|array',
                'is_auto_save' => 'nullable|boolean',
            ]);
        }

        try {
            DB::beginTransaction();

            $year_id = $request->year_id;
            $class_id = $request->class_id;
            $assign_subject_id = $request->assign_subject_id;
            $term_type_id = $request->term_type_id;
            $student_ids = $request->student_id;
            $isAutoSave = $request->is_auto_save ?? false;

            // Déterminer si matière = EPS
            $isEPS = DB::table('school_subjects')
                ->where('id', $assign_subject_id)
                ->value('name') === 'E.P.S';

            // Exam types
            $termType = TermType::find($term_type_id);
            $termName = $termType->name ?? '';
            $examNames = $this->getExamNamesByTerm($termName);

            $examTypes = ExamType::whereIn('name', array_values($examNames))->get();
            $examTypeIds = [
                'Interro' => $examTypes->firstWhere('name', $examNames['Interro'])->id ?? null,
                'Devoir' => $examTypes->firstWhere('name', $examNames['Devoir'])->id ?? null,
                'Compo' => $examTypes->firstWhere('name', $examNames['Compo'])->id ?? null,
            ];

            $interroMarks = $request->input('Interro', []);
            $devoirMarks  = $request->input('Devoir', []);
            $compoMarks   = $request->input('Compo', []);
            $inapteValues = $request->input('inapte', []);

            $now = now();

            foreach ($student_ids as $index => $student_id) {
                $currentInapte = $inapteValues[$index] ?? 0;

                if ($isEPS && $currentInapte) {
                    $interroMarks[$index] = null;
                    $devoirMarks[$index]  = null;
                    $compoMarks[$index]   = null;
                }

                // Interro
                if ($examTypeIds['Interro']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Interro'],
                        ],
                        [
                            'marks' => $interroMarks[$index] ?? null,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }

                // Devoir
                if ($examTypeIds['Devoir']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Devoir'],
                        ],
                        [
                            'marks' => $devoirMarks[$index] ?? null,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }

                // Compo
                if ($examTypeIds['Compo']) {
                    DB::table('student_marks')->updateOrInsert(
                        [
                            'student_id' => $student_id,
                            'year_id' => $year_id,
                            'class_id' => $class_id,
                            'assign_subject_id' => $assign_subject_id,
                            'term_type_id' => $term_type_id,
                            'exam_type_id' => $examTypeIds['Compo'],
                        ],
                        [
                            'marks' => $compoMarks[$index] ?? null,
                            'inapte' => $currentInapte,
                            'updated_at' => $now,
                            'created_at' => DB::raw('IFNULL(created_at, NOW())'),
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isAutoSave ? 'Sauvegardé automatiquement' : 'Notes enregistrées avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur sauvegarde notes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /* public function MarksGetStudents(Request $request): JsonResponse
    {
        $request->validate([
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'assign_subject_id' => 'required|exists:assign_subjects,subject_id',
            'term_type_id' => 'required|exists:term_types,id',
        ]);

        $year_id = $request->year_id;
        $class_id = $request->class_id;
        $assign_subject_id = $request->assign_subject_id;
        $term_type_id = $request->term_type_id;

        // Tous les élèves de la classe/période
        $students = AssignStudent::with(['student'])
            ->join('users', 'users.id', '=', 'assign_students.student_id')
            ->where('assign_students.year_id', $year_id)
            ->where('assign_students.class_id', $class_id)
            ->orderBy('users.name')
            ->get(['assign_students.*']); // evite de polluer le JSON

        // Noms examens
        $termType = TermType::find($term_type_id);
        $examNames = $this->getExamNamesByTerm($termType->name ?? '');

        // IDs Exam
        $examTypes = ExamType::whereIn('name', array_values($examNames))->get();
        $examTypeIds = [
            'Interro' => optional($examTypes->firstWhere('name', $examNames['Interro']))->id,
            'Devoir'  => optional($examTypes->firstWhere('name', $examNames['Devoir']))->id,
            'Compo'   => optional($examTypes->firstWhere('name', $examNames['Compo']))->id,
        ];

        // Toutes les notes existantes (groupées par student)
        $allMarks = StudentMarks::where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('assign_subject_id', $assign_subject_id)
            ->where('term_type_id', $term_type_id)
            ->get()
            ->groupBy('student_id');

        // États globaux inapte depuis la nouvelle table
        $globalInaptitudes = DB::table('student_inaptitudes')
            ->where('year_id', $year_id)
            ->where('class_id', $class_id)
            ->where('assign_subject_id', $assign_subject_id)
            ->where('term_type_id', $term_type_id)
            ->pluck('inapte', 'student_id'); // [student_id => inapte]

        foreach ($students as $student) {
            $sid = $student->student_id;
            $studentMarks = $allMarks->get($sid, collect([]));

            $marksData = ['Interro' => '', 'Devoir' => '', 'Compo' => ''];
            $inapteValue = null;

            foreach ($studentMarks as $mark) {
                if ($mark->exam_type_id == $examTypeIds['Interro']) $marksData['Interro'] = $mark->marks;
                if ($mark->exam_type_id == $examTypeIds['Devoir'])  $marksData['Devoir']  = $mark->marks;
                if ($mark->exam_type_id == $examTypeIds['Compo'])   $marksData['Compo']   = $mark->marks;

                // fallback compat si nécessaire (si pas d’état global)
                if ($inapteValue === null && $mark->inapte !== null) {
                    $inapteValue = (int)$mark->inapte;
                }
            }

            // priorité à la table globale
            if ($globalInaptitudes->has($sid)) {
                $inapteValue = (int)$globalInaptitudes[$sid];
            }
            $student->setAttribute('marksData', $marksData);
            $student->setAttribute('inapte', (int)($inapteValue ?? 0));
        }

        $isEPS = DB::table('school_subjects')->where('id', $assign_subject_id)->value('name') === 'E.P.S';

        return response()->json([
            'students' => $students,
            'isEPS' => $isEPS,
        ]);
    }
 */
    public function MarksGetStudents(Request $request): JsonResponse
{
    $request->validate([
        'year_id' => 'required|exists:student_years,id',
        'class_id' => 'required|exists:student_classes,id',
        'assign_subject_id' => 'required|exists:assign_subjects,subject_id',
        'term_type_id' => 'required|exists:term_types,id',
    ]);

    $year_id = $request->year_id;
    $class_id = $request->class_id;
    $assign_subject_id = $request->assign_subject_id;
    $term_type_id = $request->term_type_id;

    // Tous les élèves
    $students = AssignStudent::with(['student'])
        ->join('users', 'users.id', '=', 'assign_students.student_id')
        ->where('assign_students.year_id', $year_id)
        ->where('assign_students.class_id', $class_id)
        ->orderBy('users.name')
        ->get(['assign_students.*']);

    // Exam types
    $termType = TermType::find($term_type_id);
    $examNames = $this->getExamNamesByTerm($termType->name ?? '');
    $examTypes = ExamType::whereIn('name', array_values($examNames))->get();

    $examTypeIds = [
        'Interro' => optional($examTypes->firstWhere('name', $examNames['Interro']))->id,
        'Devoir'  => optional($examTypes->firstWhere('name', $examNames['Devoir']))->id,
        'Compo'   => optional($examTypes->firstWhere('name', $examNames['Compo']))->id,
    ];

    // Notes existantes
    $allMarks = StudentMarks::where('year_id', $year_id)
        ->where('class_id', $class_id)
        ->where('assign_subject_id', $assign_subject_id)
        ->where('term_type_id', $term_type_id)
        ->get()
        ->groupBy('student_id');

    // Inaptitudes globales
    $globalInaptitudes = DB::table('student_inaptitudes')
        ->where('year_id', $year_id)
        ->where('class_id', $class_id)
        ->where('assign_subject_id', $assign_subject_id)
        ->where('term_type_id', $term_type_id)
        ->pluck('inapte', 'student_id');

    foreach ($students as $student) {
        $sid = $student->student_id;
        $studentMarks = $allMarks->get($sid, collect([]));

        $marksData = [
            'Interro' => ['id' => null, 'val' => ''],
            'Devoir'  => ['id' => null, 'val' => ''],
            'Compo'   => ['id' => null, 'val' => ''],
        ];
        $inapteValue = null;

        foreach ($studentMarks as $mark) {
            if ($mark->exam_type_id == $examTypeIds['Interro']) {
                $marksData['Interro'] = ['id' => $mark->id, 'val' => $mark->marks];
            }
            if ($mark->exam_type_id == $examTypeIds['Devoir']) {
                $marksData['Devoir'] = ['id' => $mark->id, 'val' => $mark->marks];
            }
            if ($mark->exam_type_id == $examTypeIds['Compo']) {
                $marksData['Compo'] = ['id' => $mark->id, 'val' => $mark->marks];
            }

            if ($inapteValue === null && $mark->inapte !== null) {
                $inapteValue = (int)$mark->inapte;
            }
        }

        if ($globalInaptitudes->has($sid)) {
            $inapteValue = (int)$globalInaptitudes[$sid];
        }

        $student->setAttribute('marksData', $marksData);
        $student->setAttribute('inapte', (int)($inapteValue ?? 0));
    }

    $isEPS = DB::table('school_subjects')->where('id', $assign_subject_id)->value('name') === 'E.P.S';

    return response()->json([
        'students' => $students,
        'isEPS' => $isEPS,
    ]);
}

    private function getExamNamesByTerm($termName)
    {
        switch ($termName) {
            case 'Semestre 1':
                return ['Interro'=>'Interrogation 1Sem','Devoir'=>'Devoir 1er Sem','Compo'=>'Compo 1er Sem'];
            case 'Semestre 2':
                return ['Interro'=>'Interrogation 2Sem','Devoir'=>'Devoir 2em Sem','Compo'=>'Compo 2em Sem'];
            case 'Trimestre 1':
                return ['Interro'=>'Interrogation 1Trim','Devoir'=>'Devoir 1er Trim','Compo'=>'Compo 1er Trim'];
            case 'Trimestre 2':
                return ['Interro'=>'Interrogation 2Trim','Devoir'=>'Devoir 2em Trim','Compo'=>'Compo 2em Trim'];
            case 'Trimestre 3':
                return ['Interro'=>'Interrogation 3Trim','Devoir'=>'Devoir 3em Trim','Compo'=>'Compo 3em Trim'];
            default:
                return ['Interro'=>'Interrogation 1Sem','Devoir'=>'Devoir 1er Sem','Compo'=>'Compo 1er Sem'];
        }
    }

    public function MarksUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'marks_id' => 'required|exists:student_marks,id',
            'marks' => 'required|numeric|between:0,20',
            'year_id' => 'required|exists:student_years,id',
            'class_id' => 'required|exists:student_classes,id',
            'assign_subject_id' => 'required|exists:assign_subjects,subject_id',
            'exam_type_id' => 'required|exists:exam_types,id',
        ]);

        try {
            $mark = StudentMarks::findOrFail($request->marks_id);
            $mark->marks = $request->marks;
            $mark->save();

            return response()->json(['success' => true, 'message' => 'Note mise à jour avec succès']);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour note: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour'], 500);
        }
    }

    public function UpdateSingleMark(Request $request): JsonResponse
{
    $request->validate([
        'mark_id' => 'required|exists:student_marks,id',
        'marks'   => 'nullable|numeric|between:0,20',
        'inapte'  => 'sometimes|boolean'
    ]);

    try {
        $mark = StudentMarks::findOrFail($request->mark_id);

        if ($request->has('marks')) {
            $mark->marks = $request->marks;
        }

        if ($request->has('inapte')) {
            $inapte = (int)$request->inapte;
            $mark->inapte = $inapte;

            $isEPS = DB::table('school_subjects')
                ->where('id', $mark->assign_subject_id)
                ->value('name') === 'E.P.S';

            if ($isEPS) {
                if ($inapte === 1) {
                    // deviens Inapte → on efface la note
                    $mark->marks = null;
                } else {
                    // deviens Apte → laisser la possibilité de ressaisir
                    // (on ne restaure pas automatiquement)
                }
            }
        }

        $mark->save();

        return response()->json([
            'success' => true,
            'message' => 'Note / statut modifié avec succès'
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur modification note: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification'
        ], 500);
    }
}


    public function MarksAutoSave(Request $request): JsonResponse
    {
        return $this->MarksStore($request);
    }

    public function GetSubjects(Request $request): JsonResponse
    {
        $request->validate(['class_id' => 'required|exists:student_classes,id']);

        $class_id = $request->class_id;
        $user = auth()->user();

        if ($user->role == 'Admin' || $user->role == 'Operator') {
            $subjects = AssignSubject::with('school_subject')->where('class_id', $class_id)->get();
        } elseif ($user->usertype == 'Employé') {
            $assignedSubjectIds = DB::table('assign_subject_teaches')
                ->where('teacher_id', $user->id)
                ->where('class_id', $class_id)
                ->pluck('subject_id');

            $subjects = AssignSubject::with('school_subject')
                ->where('class_id', $class_id)
                ->whereIn('subject_id', $assignedSubjectIds)
                ->get();
        }

        return response()->json($subjects);
    }

    public function GetTermTypes(Request $request): JsonResponse
    {
        $request->validate(['class_id' => 'required|exists:student_classes,id']);

        $class_id = $request->class_id;
        $studentClass = StudentClass::find($class_id);
        if (!$studentClass) return response()->json([]);

        $trimesterClasses = StudentClass::whereIn('name', [
            '6ème A','6ème B','6ème C','6ème D',
            '5ème A','5ème B','5ème C','5ème D',
            '4ème A','4ème B','4ème C','4ème D',
            '3ème A','3ème B','3ème C','3ème D','3ème E'
        ])->pluck('id')->toArray();

        $termTypes = in_array($studentClass->id, $trimesterClasses)
            ? TermType::whereIn('name', ['Trimestre 1','Trimestre 2','Trimestre 3'])->get()
            : TermType::whereIn('name', ['Semestre 1','Semestre 2'])->get();

        return response()->json($termTypes);
    }
}
