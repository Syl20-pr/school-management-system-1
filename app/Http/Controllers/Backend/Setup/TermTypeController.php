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
use App\Models\TermType;

class TermTypeController extends Controller
{
    public function ViewTermType(){
        $data['allData'] = TermType::all();
        return view('backend.setup.term.view_term',$data);

    }


    public function TermTypeAdd(){
        return view('backend.setup.term.add_term');
    }

    public function TermTypeStore(Request $request){

            $validatedData = $request->validate([
                'name' => 'required|unique:term_types,name',
                
            ]);

            $data = new TermType();
            $data->name = $request->name;
            $data->save();

            $notification = array(
                'message' => 'Trimestre/Semestre Ajoutée Avec Succès',
                'alert-type' => 'success'
            );

            return redirect()->route('term.type.view')->with($notification);

        }


     public function TermTypeEdit($id){
            $editData = TermType::find($id);
            return view('backend.setup.term.edit_term',compact('editData'));

        }


        public function TermTypeUpdate(Request $request,$id){

        $data = TermType::find($id);
     
     $validatedData = $request->validate([
            'name' => 'required|unique:term_types,name,'.$data->id
            
        ]);

        
        $data->name = $request->name;
        $data->save();

        $notification = array(
            'message' => 'Trimestre/Semestre Modifiée Avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('term.type.view')->with($notification);
    }



     public function TermTypeDelete($id){
            $user = TermType::find($id);
            $user->delete();

            $notification = array(
                'message' => 'Trimestre/Semestre Supprimée Avec Succès',
                'alert-type' => 'info'
            );

            return redirect()->route('term.type.view')->with($notification);

        }
}
