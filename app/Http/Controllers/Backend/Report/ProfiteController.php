<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AccountEmployeeSalary;
use App\Models\AccountOtherCost;
use App\Models\AccountStudentFee;

//use DB;
//use PDF;
use Barryvdh\DomPDF\Facade\Pdf;


class ProfiteController extends Controller
{
    public function MonthlyProfitView(){
        return view('backend.report.profit.profit_view');

    }



    public function MonthlyProfitDatewais(Request $request){

        $start_date = date('Y-m',strtotime($request->start_date));
        $end_date = date('Y-m',strtotime($request->end_date));
        $sdate = date('Y-m-d',strtotime($request->start_date));
        $edate = date('Y-m-d',strtotime($request->end_date));
         
        $student_fee = AccountStudentFee::whereBetween('date',[$start_date,$end_date])->sum('amount');

        $other_cost = AccountOtherCost::whereBetween('date',[$sdate,$edate])->sum('amount'); 

        $emp_salary = AccountEmployeeSalary::whereBetween('date',[$start_date,$end_date])->sum('amount');

        $total_cost = $other_cost+$emp_salary;
        $profit = $student_fee-$total_cost;  
          
         $html['thsource']  = '<th>Frais de Scolarité</th>';
         $html['thsource'] .= '<th>Autres Frais</th>';
         $html['thsource'] .= '<th>Salaires Des Employés</th>';
         $html['thsource'] .= '<th>Coût TOTAL</th>';
         $html['thsource'] .= '<th>PROFIT </th>';
         $html['thsource'] .= '<th>Action</th>';

         $color = 'success';
         $html['tdsource']  = '<td>'.$student_fee.'</td>';
         $html['tdsource']  .= '<td>'.$other_cost.'</td>';
         $html['tdsource']  .= '<td>'.$emp_salary.'</td>';
         $html['tdsource']  .= '<td>'.$total_cost.'</td>';
         $html['tdsource']  .= '<td>'.$profit.'</td>';
         $html['tdsource'] .='<td>';
            $html['tdsource'] .='<a class="btn btn-sm btn-'.$color.'" title="PDF" target="_blanks" href="'.route("report.profit.pdf").'?start_date='.$sdate.'&end_date='.$edate.'">Justificatif</a>';
            $html['tdsource'] .= '</td>';
        
        return response()->json(@$html); 

    } // end method 


    public function MonthlyProfitPdf(Request $request){

         $data['start_date'] = date('Y-m',strtotime($request->start_date));
         $data['end_date'] = date('Y-m',strtotime($request->end_date));
         $data['sdate'] = date('Y-m-d',strtotime($request->start_date));
         $data['edate'] = date('Y-m-d',strtotime($request->end_date));

    $pdf = PDF::loadView('backend.report.profit.profit_pdf', $data)->setPaper('a4')
        ->setOptions([
            'tempDir' => public_path(),
            'chroot' => public_path(),
        ]);
    //$pdf->SetProtection(['copy', 'print'], '', 'pass');
    return $pdf->download('Profit.pdf');

    }
}
