<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentMarks;
use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\ExamType;
use App\Models\AssignSubject;
use App\Models\AssignStudent;
use App\Models\SchoolSubject;
use App\Models\AssignExamType;
use App\Models\TermType;

class AssignExamTypeController extends Controller
{
      //
  public function ViewAssignExamType(){
       //$data['allData'] = AssignSubject::all();
        $data['allData'] = AssignExamType::select('term_type_id')->groupBy('term_type_id')->get();
        return view('backend.setup.assign_exam_type.view_assign_exam_type',$data);
    }


     public function AddAssignExamType(){
        $data['term_types'] = TermType::all();
        $data['exam_types'] = ExamType::all();
        return view('backend.setup.assign_exam_type.add_assign_exam_type',$data);
    }


    public function StoreAssignExamType(Request $request){

            $examTypeCount = count($request->exam_type_id);
            if ($examTypeCount !=NULL) {
                for ($i=0; $i <$examTypeCount ; $i++) { 
                    $assign_exam_type = new AssignExamType();
                    $assign_exam_type->term_type_id = $request->term_type_id;
                    $assign_exam_type->exam_type_id = $request->exam_type_id[$i];
                    $assign_exam_type->comment = $request->comment[$i];
                    $assign_exam_type->save();

                } // End For Loop
            }// End If Condition

            $notification = array(
                'message' => 'Type d\'Evaluation ajouté avec Succès',
                'alert-type' => 'success'
            );

            return redirect()->route('assign.exam.view')->with($notification);

        }  // End Method 


     public function EditAssignExamType($term_type_id){
            $data['editData'] = AssignExamType::where('term_type_id',$term_type_id)->orderBy('exam_type_id','asc')->get();
            // dd($data['editData']->toArray());
        $data['term_types'] = TermType::all();
        $data['exam_types'] = ExamType::all();
        return view('backend.setup.assign_exam_type.edit_assign_exam_type',$data);

        }


public function UpdateAssignExamType(Request $request,$term_type_id){
        if ($request->exam_type_id == NULL) {
       
        $notification = array(
            'message' => 'Désolé, vous n\'avez sélectionné aucun Type d\'Evaluation',
            'alert-type' => 'error'
        );

        return redirect()->route('assign.exam.edit',$term_type_id)->with($notification);
             
        }else{
             
    $countTermType = count($request->exam_type_id);
    AssignExamType::where('term_type_id',$term_type_id)->delete(); 
            for ($i=0; $i <$countTermType ; $i++) { 
                $assign_exam_type = new AssignExamType();
                    $assign_exam_type->term_type_id = $request->term_type_id;
                    $assign_exam_type->exam_type_id = $request->exam_type_id[$i];
                    $assign_exam_type->comment = $request->comment[$i];
                    $assign_exam_type->save();

            } // End For Loop    

        }// end Else

       $notification = array(
            'message' => 'Données Mises à Jour avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.exam.view')->with($notification);
    } // end Method 


    public function DetailsAssignExamType($term_type_id){
   $data['detailsData'] = AssignExamType::where('term_type_id',$term_type_id)->orderBy('exam_type_id','asc')->get();

   return view('backend.setup.assign_exam_type.details_assign_exam_type',$data);


    }

}
