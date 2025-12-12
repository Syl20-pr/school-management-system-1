<?php

namespace App\Http\Controllers\Backend\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
//use App\Models\StudentClass; 
//use App\Models\AssignSubject;
use App\Models\Designation;
use App\Models\AssignDesignation;
class AssignDesignationController extends Controller
{
    //
  public function ViewAssignDesignation(){
       //$data['allData'] = AssignSubject::all();
        $data['allData'] = AssignDesignation::select('designation_id')->groupBy('designation_id')->get();
        return view('backend.setup.assign_designation.view_assign_designation',$data);
    }


     public function AddAssignDesignation(){
        $data['designations'] = Designation::all();
        $data['teachers'] = User::where('usertype', 'Employé')->get();
        return view('backend.setup.assign_designation.add_assign_designation',$data);
    }


    public function StoreAssignDesignation(Request $request){

            $teacherCount = count($request->teacher_id);
            if ($teacherCount !=NULL) {
                for ($i=0; $i <$teacherCount ; $i++) { 
                    $assign_designation = new AssignDesignation();
                    $assign_designation->designation_id = $request->designation_id;
                    $assign_designation->teacher_id = $request->teacher_id[$i];
                    $assign_designation->comment = $request->comment[$i];
                    $assign_designation->save();

                } // End For Loop
            }// End If Condition

            $notification = array(
                'message' => 'Prof. Ajoutée à la Désignation avec Succès',
                'alert-type' => 'success'
            );

            return redirect()->route('assign.designation.view')->with($notification);

        }  // End Method 


     public function EditAssignDesignation($designation_id){
            $data['editData'] = AssignDesignation::where('designation_id',$designation_id)->orderBy('teacher_id','asc')->get();
            // dd($data['editData']->toArray());
        $data['designations'] = Designation::all();
        $data['teachers'] = User::where('usertype', 'Employé')->get();
        return view('backend.setup.assign_designation.edit_assign_designation',$data);

        }


public function UpdateAssignDesignation(Request $request,$designation_id){
        if ($request->teacher_id == NULL) {
       
        $notification = array(
            'message' => 'Désolé, vous n\'avez sélectionné aucun(e) Prof.',
            'alert-type' => 'error'
        );

        return redirect()->route('assign.designation.edit',$class_id)->with($notification);
             
        }else{
             
    $countDesignation = count($request->teacher_id);
    AssignDesignation::where('designation_id',$designation_id)->delete(); 
            for ($i=0; $i <$countDesignation ; $i++) { 
                $assign_designation = new AssignDesignation();
                    $assign_designation->designation_id = $request->designation_id;
                    $assign_designation->teacher_id = $request->teacher_id[$i];
                    $assign_designation->comment = $request->comment[$i];
                    $assign_designation->save();

            } // End For Loop    

        }// end Else

       $notification = array(
            'message' => 'Données Mises à Jour avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('assign.designation.view')->with($notification);
    } // end Method 


    public function DetailsAssignDesignation($designation_id){
   $data['detailsData'] = AssignDesignation::where('designation_id',$designation_id)->orderBy('teacher_id','asc')->get();

   return view('backend.setup.assign_designation.details_assign_designation',$data);


    }




}


