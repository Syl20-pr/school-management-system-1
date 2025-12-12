<?php

namespace App\Http\Controllers\Backend\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignStudent;
use App\Models\User;
use App\Models\DiscountStudent;
use App\Models\FeeCategoryAmount;

use App\Models\StudentYear;
use App\Models\StudentClass;
use App\Models\StudentGroup;
use App\Models\StudentShift;
use DB;
//use PDF;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Designation;
use App\Models\EmployeeSallaryLog;

class EmployeeRegController extends Controller
{
     public function EmployeeView(){

        $data['allData'] = User::where('usertype','Employé')->get();
        return view('backend.employee.employee_reg.employee_view',$data);
    }

    public function EmployeeList(){

        //$data['allData'] = User::where('usertype','Employé')->get();

        // Fetch and sort employees
        $employees = User::where('usertype', 'Employé')
            ->orderBy('name', 'asc') // Sort alphabetically
            ->get();

        // Count females
        $femaleCount = $employees->where('gender', 'Féminin')->count();

        // Generate PDF
        $pdf = PDF::loadView('backend.employee.employee_reg.employee_list', compact('employees', 'femaleCount'));

        // Return PDF for download
        return $pdf->download('employee_list.pdf');
    }


    public function EmployeeAdd(){
        $data['designation'] = Designation::all();
        return view('backend.employee.employee_reg.employee_add',$data);
    }




    public function EmployeeStore(Request $request){
        DB::transaction(function() use($request){
        $checkYear = date('Ym',strtotime($request->join_date));
        //dd($checkYear);
        $employee = User::where('usertype','Employé')->orderBy('id','DESC')->first();

        if ($employee == null) {
            $employeeId = 1;
        } else {
            $employeeId = $employee->id + 1;
        }

        // Apply the condition to format the ID with leading zeros
        if ($employeeId < 10) {
            $id_no = '000' . $employeeId;
        } elseif ($employeeId < 100) {
            $id_no = '00' . $employeeId;
        } elseif ($employeeId < 1000) {
            $id_no = '0' . $employeeId;
        } else {
            $id_no = $employeeId;
        }///////////////////////////////////////


        $final_id_no = $checkYear . $id_no;

        // Create and save the User
        $user = new User();
        $code = rand(0000,9999);
        $user->id_no = $final_id_no;
        $user->password = bcrypt($code);
        $user->usertype = 'Employé';
        $user->code = $code;
        $user->role = $request->role;
        $user->name = $request->name;
        $user->fname = $request->fname;
        $user->mname = $request->mname;
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->religion = $request->religion;
        $user->salary = $request->salary;
        $user->lob = $request->lob;
        $user->f_no = $request->f_no;
        $user->designation_id = $request->designation_id;
        $user->dob = date('Y-m-d',strtotime($request->dob));
        $user->join_date = date('Y-m-d',strtotime($request->join_date));

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/employee_images'),$filename);
            $user->image = $filename;
        }
        $user->save();

        // Create and save AssignStudent
          $employee_salary = new EmployeeSallaryLog();
          $employee_salary->employee_id = $user->id;
          $employee_salary->effected_salary = date('Y-m-d',strtotime($request->join_date));
          $employee_salary->previous_salary = $request->salary;
          $employee_salary->present_salary = $request->salary;
          $employee_salary->increment_salary = '0';
          $employee_salary->save();

           
        });


        $notification = array(
            'message' => 'Nouveau Employé Ajouté(e) Avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('employee.registration.view')->with($notification);

    } // END Method


    public function EmployeeEdit($id){
        $data['editData'] = User::find($id);
        $data['designation'] = Designation::all();
        return view('backend.employee.employee_reg.employee_edit',$data);

    }


    public function EmployeeUpdate(Request $request, $id){
    
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->fname = $request->fname;
        $user->mname = $request->mname;
        $user->mobile = $request->mobile;
        $user->role = $request->role;
        $user->address = $request->address;
        $user->gender = $request->gender;
        $user->lob = $request->lob;
        $user->f_no = $request->f_no;
        $user->religion = $request->religion;
         
        $user->designation_id = $request->designation_id;
        $user->dob = date('Y-m-d',strtotime($request->dob));
         

        if ($request->file('image')) {
            $file = $request->file('image');
            @unlink(public_path('upload/employee_images/'.$user->image));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/employee_images'),$filename);
            $user['image'] = $filename;
        }
        $user->save();

         

        $notification = array(
            'message' => 'Modification des Données de l\'Employé(e) fait Avec Succès',
            'alert-type' => 'success'
        );

        return redirect()->route('employee.registration.view')->with($notification);


    }// END METHOD



    public function EmployeeDetails($id){
        $data['details'] = User::find($id);

    $pdf = PDF::loadView('backend.employee.employee_reg.employee_details_pdf', $data)->setPaper('a4')
        ->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
    return $pdf->stream('Employe.pdf');

    }


    

}
