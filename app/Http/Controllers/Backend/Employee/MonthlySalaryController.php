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

use App\Models\EmployeeLeave;
use App\Models\LeavePurpose;

use App\Models\EmployeeAttendance;

class MonthlySalaryController extends Controller
{
    public function MonthlySalaryView(){
        return view('backend.employee.monthly_salary.monthly_salary_view');

    }


  public function MonthlySalaryGet(Request $request){
        
        $date = date('Y-m',strtotime($request->date));
         if ($date !='') {
            $where[] = ['date','like',$date.'%'];
         }
         
         $data = EmployeeAttendance::select('employee_id')->groupBy('employee_id')->with(['user'])->where($where)->get();
         // dd($allStudent);
         $html['thsource']  = '<th>SL</th>';
         $html['thsource'] .= '<th>Nom</th>';
         $html['thsource'] .= '<th>Salaire de Base</th>';
         $html['thsource'] .= '<th>Salaire Du Mois</th>';
         $html['thsource'] .= '<th>Action</th>';


         foreach ($data as $key => $attend) {
            $totalattend = EmployeeAttendance::with(['user'])->where($where)->where('employee_id',$attend->employee_id)->get();
            $absentcount = count($totalattend->where('attend_status','Absent'));

            $color = 'success';
            $html[$key]['tdsource']  = '<td>'.($key+1).'</td>';
            $html[$key]['tdsource'] .= '<td>'.$attend['user']['name'].'</td>';
            $html[$key]['tdsource'] .= '<td>'.$attend['user']['salary'].'</td>';
             
            
            $salary = (float)$attend['user']['salary'];
            $salaryperday = (float)$salary/30;
            $totalsalaryminus = (float)$absentcount*(float)$salaryperday;
            $totalsalary = (float)$salary-(float)$totalsalaryminus;

            $html[$key]['tdsource'] .='<td>'.$totalsalary.'FCFA'.'</td>';
            $html[$key]['tdsource'] .='<td>';
            $html[$key]['tdsource'] .='<a class="btn btn-sm btn-'.$color.'" title="PaySlip" target="_blanks" href="'.route("employee.monthly.salary.payslip",$attend->employee_id).'">Bulletin</a>';
            $html[$key]['tdsource'] .= '</td>';

         }  
        return response()->json(@$html);
 

  } // END Method 


    public function MonthlySalaryPayslip(Request $request,$employee_id){
        $id = EmployeeAttendance::where('employee_id',$employee_id)->first();
        $date = date('Y-m',strtotime($id->date));
         if ($date !='') {
            $where[] = ['date','like',$date.'%'];
         }

    $data['details'] = EmployeeAttendance::with(['user'])->where($where)->where('employee_id',$id->employee_id)->get();  

    $pdf = PDF::loadView('backend.employee.monthly_salary.monthly_salary_pdf', $data)->setPaper('a4')
        ->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);

    
    return $pdf->download('bulletin.pdf');

    }

}
