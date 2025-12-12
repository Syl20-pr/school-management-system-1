<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSubject;
use App\Models\StudentClass;
use App\Models\User;
use App\Models\StudentYear;
use App\Models\Designation;
use App\Models\AssignDesignation;
use App\Models\AssignSubject;
use App\Models\AssignSubjectTeach;

class AssignSubjectTeachController extends Controller
{
    //
  // public function ViewAssignSubjectTeacher(){
  //      //$data['allData'] = AssignSubject::all();
  //       $data['allData'] = AssignSubjectTeach::select('year_id')->groupBy('year_id')->get();
  //       return view('backend.setup.assign_teacher.view_assign_teacher',$data);
  //   }
    public function ViewAssignSubjectTeacher()
{
    // Example of fetching data
    $allData = AssignSubjectTeach::with('year')->get(); // Adjust as needed
    
    return view('backend.setup.assign_teacher.view_assign_teacher', compact('allData'));
}



    // Method to return subjects based on the selected class
    public function getSubjectsByClass(Request $request)
    {
        $classId = $request->class_id;

        // Fetch subjects assigned to the selected class from `assign_subjects`
        $subjects = AssignSubject::where('class_id', $classId)
                    ->with('school_subject')  // Load the school_subject relationship
                    ->get()
                    ->map(function($assignSubject) {
                        return [
                            'id' => $assignSubject->school_subject->id,
                            'name' => $assignSubject->school_subject->name,
                        ];
                    });

        return response()->json($subjects);
    }


     public function AddAssignTeacher(){
        $data['years'] = StudentYear::all();
        $data['classes'] = StudentClass::all();
        $data['designations'] = Designation::all();
        //$data['teachers'] = AssignDesignation::with('designation')->get();
        $data['subjects'] = AssignSubject::all();
        return view('backend.setup.assign_teacher.add_assign_teacher',$data);
    }


    // Fetch teachers based on selected designation
    public function getTeachersByDesignation(Request $request)
    {
        $designationId = $request->designation_id;

        $teachers = AssignDesignation::where('designation_id', $designationId)
                    ->with(['teacher' => function ($query) {
                        $query->where('usertype', 'Employé');  // Only employees
                    }])
                    ->get()
                    ->map(function ($assignDesignation) {
                        return [
                            'id' => $assignDesignation->teacher->id,
                            'name' => $assignDesignation->teacher->name,
                        ];
                    });

        return response()->json($teachers);
    }


public function StoreAssignTeachers(Request $request)
{
    // Log request data to debug
    //\Log::info('Request data:', $request->all());

    // Ensure subject_id is an array even if only one subject is selected
    $subjectIds = is_array($request->subject_id) ? $request->subject_id : [$request->subject_id];

    // Use a transaction to ensure data integrity
    \DB::transaction(function () use ($request, $subjectIds) {
        foreach ($subjectIds as $key => $subjectId) {
            $assignSubjectTeach = new AssignSubjectTeach();
            $assignSubjectTeach->year_id = $request->year_id;
            $assignSubjectTeach->class_id = $request->class_id;
            $assignSubjectTeach->teacher_id = $request->teacher_id;
            $assignSubjectTeach->designation_id = $request->designation_id;
            $assignSubjectTeach->subject_id = $subjectId;

            // Ensure total_hours is numeric (strip "H" and other non-numeric characters)
            $assignSubjectTeach->total_hours = (int) preg_replace('/\D/', '', $request->total_hours[$key]);

            // Total sessions should already be numeric, so no need for cleanup
            $assignSubjectTeach->total_sessions = (int) $request->total_sessions[$key];

            // Handle nullable comments
            $assignSubjectTeach->comments = $request->comments[$key] ?? null;

            $assignSubjectTeach->save();

            // Log saved data
            \Log::info('Saved subject teach assignment:', $assignSubjectTeach->toArray());
        }
    });

    // Return success message
    $notification = [
        'message' => 'Affectation de Matière Ajoutée avec Succès',
        'alert-type' => 'success'
    ];

    return redirect()->route('assign.teacher.add')->with($notification);
}





        public function EditAssignSubject($class_id){
            $data['editData'] = AssignSubject::where('class_id',$class_id)->orderBy('subject_id','asc')->get();
            // dd($data['editData']->toArray());
        $data['subjects'] = SchoolSubject::all();
        $data['classes'] = StudentClass::all();
        return view('backend.setup.assign_subject.edit_assign_subject',$data);

        }


public function UpdateAssignSubject(Request $request,$class_id){
        if ($request->subject_id == NULL) {
       
        $notification = array(
            'message' => 'Désolé, vous n\'avez sélectionné aucune matière',
            'alert-type' => 'error'
        );

        return redirect()->route('assign.subject.edit',$class_id)->with($notification);
             
        }else{
             
    $countClass = count($request->subject_id);
    AssignSubject::where('class_id',$class_id)->delete(); 
            for ($i=0; $i <$countClass ; $i++) { 
                $assign_subject = new AssignSubject();
                    $assign_subject->class_id = $request->class_id;
                    $assign_subject->subject_id = $request->subject_id[$i];
                    $assign_subject->full_mark = $request->full_mark[$i];
                    $assign_subject->pass_mark = $request->pass_mark[$i];
                    $assign_subject->subjective_mark = $request->subjective_mark[$i];
                    $assign_subject->save();

            } // End For Loop    

        }// end Else

       $notification = array(
            'message' => 'Données Mises à Jour avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.subject.view')->with($notification);
    } // end Method 


    public function DetailsAssignSubject($class_id){
   $data['detailsData'] = AssignSubject::where('class_id',$class_id)->orderBy('subject_id','asc')->get();

   return view('backend.setup.assign_subject.details_assign_subject',$data);


    }




}

