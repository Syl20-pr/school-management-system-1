<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AssignSubjectTeach;
use App\Models\AssignStudent;

class UserController extends Controller
{
    
    // Methods for Teachers ...

    public function TeacherDashboard() {
        // Get the currently logged-in teacher (assume user is authenticated)
        $teacher = auth()->user();

        // Check if the logged-in user is a teacher
        if ($teacher->usertype !== 'Employé') {
            return redirect()->back()->with('error', 'Accès Non Authorisé');
        }

        // Get the subjects assigned to the teacher
        $assignedSubjects = AssignSubjectTeach::where('teacher_id', $teacher->id)
            ->with(['class', 'subject'])
            ->get();

        // Get the students for each class the teacher is assigned to
        foreach ($assignedSubjects as $assigned) {
            $assigned->students = AssignStudent::where('class_id', $assigned->class_id)
                                               ->with('student') // get student details
                                               ->get();
        }

        // Pass the data to the teacher's dashboard view
        return view('backend.teacher.dashboard', compact('teacher', 'assignedSubjects'));
    }

///////////////////////////////// END METHODS TEACHERS  ////////////////////////////

    public function UserView(){
        // $allData = User::all();
        $data['allData'] = User::where('usertype','Admin')->get();
        return view('backend.user.view_user',$data);

    }


     public function UserAdd(){
        return view('backend.user.add_user');
    }


    public function UserStore(Request $request){

        $validatedData = $request->validate([
            'email' => 'required|unique:users',
            'name' => 'required',
        ]);

        $data = new User();
        $code = rand(0000,9999);
        $data->usertype = 'Admin';
        //$data->usertype = $request->usertype;
        $data->role = $request->role;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = bcrypt($code);
        //$data->password = bcrypt($request->password);
        $data->code = $code;
        $data->save();

        $notification = array(
            'message' => 'Utilisateur Ajouté avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('user.view')->with($notification);

    }



    public function UserEdit($id){
        $editData = User::find($id);
        return view('backend.user.edit_user',compact('editData'));

    }



    public function UserUpdate(Request $request, $id){

        $data = User::find($id);
        //$data->usertype = $request->usertype;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->role = $request->role;
        $data->save();

        $notification = array(
            'message' => 'Utilisateur Mis à Jour avec Succès',
            'alert-type' => 'info'
        );

        return redirect()->route('user.view')->with($notification);

    }



    public function UserDelete($id){
        $user = User::find($id);
        $user->delete();

        $notification = array(
            'message' => 'Utilisateur Supprimé Avec Succès',
            'alert-type' => 'info'
        );

        return redirect()->route('user.view')->with($notification);

    }


}
