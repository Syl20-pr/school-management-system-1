<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\Setup\StudentClassController; 
use App\Http\Controllers\Backend\Setup\StudentYearController; 
use App\Http\Controllers\Backend\Setup\StudentGroupController; 
use App\Http\Controllers\Backend\Setup\StudentShiftController;
use App\Http\Controllers\Backend\Setup\FeeCategoryController; 
use App\Http\Controllers\Backend\Setup\FeeAmountController; 
use App\Http\Controllers\Backend\Setup\ExamTypeController; 
use App\Http\Controllers\Backend\Setup\TermTypeController;
use App\Http\Controllers\Backend\Setup\SchoolSubjectController; 
use App\Http\Controllers\Backend\Setup\AssignSubjectController;
use App\Http\Controllers\Backend\Setup\AssignDesignationController;
use App\Http\Controllers\Backend\Setup\DesignationController; 
use App\Http\Controllers\Backend\Setup\AssignSubjectTeachController;
use App\Http\Controllers\Backend\Setup\AssignExamTypeController;

use App\Http\Controllers\Backend\Student\StudentRegController;
use App\Http\Controllers\Backend\Student\StudentRollController;
use App\Http\Controllers\Backend\Student\RegistrationFeeController;
use App\Http\Controllers\Backend\Student\MonthlyFeeController;
use App\Http\Controllers\Backend\Student\ExamFeeController;
use App\Http\Controllers\Backend\Student\StudentAbsController;
use App\Http\Controllers\Backend\Student\StudentPromotionController;

use App\Http\Controllers\Backend\Employee\EmployeeRegController;
use App\Http\Controllers\Backend\Employee\EmployeeSalaryController;
use App\Http\Controllers\Backend\Employee\EmployeeLeaveController;
use App\Http\Controllers\Backend\Employee\EmployeeAttendanceController;
use App\Http\Controllers\Backend\Employee\MonthlySalaryController;

use App\Http\Controllers\Backend\Marks\MarksController;
use App\Http\Controllers\Backend\DefaultController;
use App\Http\Controllers\Backend\Marks\GradeController;

use App\Http\Controllers\Backend\Account\StudentFeeController;
use App\Http\Controllers\Backend\Account\AccountSalaryController;
use App\Http\Controllers\Backend\Account\OtherCostController;

use App\Http\Controllers\Backend\Report\ProfiteController;
use App\Http\Controllers\Backend\Report\MarkSheetController;
use App\Http\Controllers\Backend\Report\BulletinSheetController;
use App\Http\Controllers\Backend\Report\TermStatisticsController;
use App\Http\Controllers\Backend\Report\AttenReportController;
use App\Http\Controllers\Backend\Report\ResultReportController;

use App\Http\Controllers\Backend\Report\Bulletin\BulletinController;
use App\Http\Controllers\Backend\Report\Bulletin\StatisticsController;

use App\Http\Controllers\TimeTableController;

use App\Http\Middleware\PreventBackHistory;

use App\Livewire\AbsenceManager;



Route::get('/', function () {
    return view('auth.login');
});

// Protecting routes with 'auth' and 'PreventBackHistory' middleware
Route::middleware(['auth', PreventBackHistory::class])->group(function () {


    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');
    
Route::get('/admin/logout', [AdminController::class, 'Logout'])->name('admin.logout');
    
    Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/dashboard', [UserController::class, 'TeacherDashboard'])->name('teacher.dashboard');
});


//Route::middleware(['auth'])->group(function () {

// User Management All Routes 

Route::prefix('users')->group(function(){

Route::get('/view', [UserController::class, 'UserView'])->name('user.view');

Route::get('/add', [UserController::class, 'UserAdd'])->name('users.add');

Route::post('/store', [UserController::class, 'UserStore'])->name('users.store');

Route::get('/edit/{id}', [UserController::class, 'UserEdit'])->name('users.edit');
Route::post('/update/{id}', [UserController::class, 'UserUpdate'])->name('users.update');

Route::get('/delete/{id}', [UserController::class, 'UserDelete'])->name('users.delete');


});

/// User Profile and Change Password 
Route::prefix('profile')->group(function(){

Route::get('/view', [ProfileController::class, 'ProfileView'])->name('profile.view');

Route::get('/edit', [ProfileController::class, 'ProfileEdit'])->name('profile.edit');

Route::post('/store', [ProfileController::class, 'ProfileStore'])->name('profile.store');

Route::get('/password/view', [ProfileController::class, 'PasswordView'])->name('profile.password.view');

Route::post('/password/update', [ProfileController::class, 'PasswordUpdate'])->name('profile.password.update');

});


/// User Profile and Change Password 
Route::prefix('setups')->group(function(){

// Student Class Routes 
Route::get('student/class/view', [StudentClassController::class, 'ViewStudent'])->name('student.class.view');

Route::get('student/class/add', [StudentClassController::class, 'StudentClassAdd'])->name('student.class.add');

Route::post('student/class/store', [StudentClassController::class, 'StudentClassStore'])->name('store.student.class');

Route::get('student/class/edit/{id}', [StudentClassController::class, 'StudentClassEdit'])->name('student.class.edit');

Route::post('student/class/update/{id}', [StudentClassController::class, 'StudentClassUpdate'])->name('update.student.class');

Route::get('student/class/delete/{id}', [StudentClassController::class, 'StudentClassDelete'])->name('student.class.delete');

// Student Year Routes 

Route::get('student/year/view', [StudentYearController::class, 'ViewYear'])->name('student.year.view');

Route::get('student/year/add', [StudentYearController::class, 'StudentYearAdd'])->name('student.year.add');

Route::post('student/year/store', [StudentYearController::class, 'StudentYearStore'])->name('store.student.year');

Route::get('student/year/edit/{id}', [StudentYearController::class, 'StudentYearEdit'])->name('student.year.edit');

Route::post('student/year/update/{id}', [StudentYearController::class, 'StudentYearUpdate'])->name('update.student.year');

Route::get('student/year/delete/{id}', [StudentYearController::class, 'StudentYearDelete'])->name('student.year.delete');


// Student Group Routes 

Route::get('student/group/view', [StudentGroupController::class, 'ViewGroup'])->name('student.group.view');

Route::get('student/group/add', [StudentGroupController::class, 'StudentGroupAdd'])->name('student.group.add');

Route::post('student/group/store', [StudentGroupController::class, 'StudentGroupStore'])->name('store.student.group');

Route::get('student/group/edit/{id}', [StudentGroupController::class, 'StudentGroupEdit'])->name('student.group.edit');

Route::post('student/group/update/{id}', [StudentGroupController::class, 'StudentGroupUpdate'])->name('update.student.group');

Route::get('student/group/delete/{id}', [StudentGroupController::class, 'StudentGroupDelete'])->name('student.group.delete');


// Student Shift Routes 

Route::get('student/shift/view', [StudentShiftController::class, 'ViewShift'])->name('student.shift.view');

Route::get('student/shift/add', [StudentShiftController::class, 'StudentShiftAdd'])->name('student.shift.add');

Route::post('student/shift/store', [StudentShiftController::class, 'StudentShiftStore'])->name('store.student.shift');

Route::get('student/shift/edit/{id}', [StudentShiftController::class, 'StudentShiftEdit'])->name('student.shift.edit');

Route::post('student/shift/update/{id}', [StudentShiftController::class, 'StudentShiftUpdate'])->name('update.student.shift');

Route::get('student/shift/delete/{id}', [StudentShiftController::class, 'StudentShiftDelete'])->name('student.shift.delete');



// Fee Category Routes 

Route::get('fee/category/view', [FeeCategoryController::class, 'ViewFeeCat'])->name('fee.category.view');

Route::get('fee/category/add', [FeeCategoryController::class, 'FeeCatAdd'])->name('fee.category.add');

Route::post('fee/category/store', [FeeCategoryController::class, 'FeeCatStore'])->name('store.fee.category');

Route::get('fee/category/edit/{id}', [FeeCategoryController::class, 'FeeCatEdit'])->name('fee.category.edit');

Route::post('fee/category/update/{id}', [FeeCategoryController::class, 'FeeCategoryUpdate'])->name('update.fee.category');

Route::get('fee/category/delete/{id}', [FeeCategoryController::class, 'FeeCategoryDelete'])->name('fee.category.delete');

// Fee Category Amount Routes 

Route::get('fee/amount/view', [FeeAmountController::class, 'ViewFeeAmount'])->name('fee.amount.view');

Route::get('fee/amount/add', [FeeAmountController::class, 'AddFeeAmount'])->name('fee.amount.add');

Route::post('fee/amount/store', [FeeAmountController::class, 'StoreFeeAmount'])->name('store.fee.amount');

Route::get('fee/amount/edit/{fee_category_id}', [FeeAmountController::class, 'EditFeeAmount'])->name('fee.amount.edit');

Route::post('fee/amount/update/{fee_category_id}', [FeeAmountController::class, 'UpdateFeeAmount'])->name('update.fee.amount');

Route::get('fee/amount/details/{fee_category_id}', [FeeAmountController::class, 'DetailsFeeAmount'])->name('fee.amount.details');


// Terms Type Routes 

Route::get('term/type/view', [TermTypeController::class, 'ViewTermType'])->name('term.type.view');

Route::get('term/type/add', [TermTypeController::class, 'TermTypeAdd'])->name('term.type.add');

Route::post('term/type/store', [TermTypeController::class, 'TermTypeStore'])->name('store.term.type');

Route::get('term/type/edit/{id}', [TermTypeController::class, 'TermTypeEdit'])->name('term.type.edit');

Route::post('term/type/update/{id}', [TermTypeController::class, 'TermTypeUpdate'])->name('update.term.type');

Route::get('term/type/delete/{id}', [TermTypeController::class, 'TermTypeDelete'])->name('term.type.delete');

// Exam Type Routes 

Route::get('exam/type/view', [ExamTypeController::class, 'ViewExamType'])->name('exam.type.view');

Route::get('exam/type/add', [ExamTypeController::class, 'ExamTypeAdd'])->name('exam.type.add');

Route::post('exam/type/store', [ExamTypeController::class, 'ExamTypeStore'])->name('store.exam.type');

Route::get('exam/type/edit/{id}', [ExamTypeController::class, 'ExamTypeEdit'])->name('exam.type.edit');

Route::post('exam/type/update/{id}', [ExamTypeController::class, 'ExamTypeUpdate'])->name('update.exam.type');

Route::get('exam/type/delete/{id}', [ExamTypeController::class, 'ExamTypeDelete'])->name('exam.type.delete');

// Assign Exam Types to Terms

 Route::get('assign/exam/view', [AssignExamTypeController::class, 'ViewAssignExamType'])->name('assign.exam.view');

 Route::get('assign/exam/add', [AssignExamTypeController::class, 'AddAssignExamType'])->name('assign.exam.add');

 Route::post('assign/exam/store', [AssignExamTypeController::class, 'StoreAssignExamType'])->name('store.assign.exam');

 Route::get('assign/exam/edit/{term_type_id}', [AssignExamTypeController::class, 'EditAssignExamType'])->name('assign.exam.edit');

 Route::post('assign/exam/update/{term_type_id}', [AssignExamTypeController::class, 'UpdateAssignExamType'])->name('update.assign.exam');

 Route::get('assign/exam/details/{term_type_id}', [AssignExamTypeController::class, 'DetailsAssignExamType'])->name('assign.exam.details');




// School Subject All Routes 

Route::get('school/subject/view', [SchoolSubjectController::class, 'ViewSubject'])->name('school.subject.view');

Route::get('school/subject/add', [SchoolSubjectController::class, 'SubjectAdd'])->name('school.subject.add');

Route::post('school/subject/store', [SchoolSubjectController::class, 'SubjectStore'])->name('store.school.subject');

Route::get('school/subject/edit/{id}', [SchoolSubjectController::class, 'SubjectEdit'])->name('school.subject.edit');

Route::post('school/subject/update/{id}', [SchoolSubjectController::class, 'SubjectUpdate'])->name('update.school.subject');

Route::get('school/subject/delete/{id}', [SchoolSubjectController::class, 'SubjectDelete'])->name('school.subject.delete');



// Assign Subject Routes 

Route::get('assign/subject/view', [AssignSubjectController::class, 'ViewAssignSubject'])->name('assign.subject.view');

Route::get('assign/subject/add', [AssignSubjectController::class, 'AddAssignSubject'])->name('assign.subject.add');

Route::post('assign/subject/store', [AssignSubjectController::class, 'StoreAssignSubject'])->name('store.assign.subject');

Route::get('assign/subject/edit/{class_id}', [AssignSubjectController::class, 'EditAssignSubject'])->name('assign.subject.edit');

Route::post('assign/subject/update/{class_id}', [AssignSubjectController::class, 'UpdateAssignSubject'])->name('update.assign.subject');

Route::get('assign/subject/details/{class_id}', [AssignSubjectController::class, 'DetailsAssignSubject'])->name('assign.subject.details');


// Designation All Routes 

Route::get('designation/view', [DesignationController::class, 'ViewDesignation'])->name('designation.view');

Route::get('designation/add', [DesignationController::class, 'DesignationAdd'])->name('designation.add');

Route::post('designation/store', [DesignationController::class, 'DesignationStore'])->name('store.designation');

Route::get('designation/edit/{id}', [DesignationController::class, 'DesignationEdit'])->name('designation.edit');

Route::post('designation/update/{id}', [DesignationController::class, 'DesignationUpdate'])->name('update.designation');

Route::get('designation/delete/{id}', [DesignationController::class, 'DesignationDelete'])->name('designation.delete');


// Assign Designation (Teacher)Routes 

 Route::get('assign/designation/view', [AssignDesignationController::class, 'ViewAssignDesignation'])->name('assign.designation.view');

 Route::get('assign/designation/add', [AssignDesignationController::class, 'AddAssignDesignation'])->name('assign.designation.add');

 Route::post('assign/designation/store', [AssignDesignationController::class, 'StoreAssignDesignation'])->name('store.assign.designation');

 Route::get('assign/designation/edit/{designation_id}', [AssignDesignationController::class, 'EditAssignDesignation'])->name('assign.designation.edit');

 Route::post('assign/designation/update/{designation_id}', [AssignDesignationController::class, 'UpdateAssignDesignation'])->name('update.assign.designation');

 Route::get('assign/designation/details/{designation_id}', [AssignDesignationController::class, 'DetailsAssignDesignation'])->name('assign.designation.details');


// Assign  Teacher to Subject of each Class Routes 

 Route::get('assign/subject/teacher/view', [AssignSubjectTeachController::class, 'ViewAssignSubjectTeacher'])->name('assign.subject.teacher.view');

 Route::get('subject/classes/get', [AssignSubjectTeachController::class, 'getSubjectsByClass'])->name('get.subject.byclass');

 Route::get('assign/teacher/add', [AssignSubjectTeachController::class, 'AddAssignTeacher'])->name('assign.teacher.add');

 Route::get('assign/teachers/get', [AssignSubjectTeachController::class, 'getTeachersByDesignation'])->name('get.assign.teachers');

 Route::post('assign/teachers/store', [AssignSubjectTeachController::class, 'StoreAssignTeachers'])->name('store.assign.teachers');

// Route::get('assign/subject/edit/{class_id}', [AssignSubjectController::class, 'EditAssignSubject'])->name('assign.subject.edit');

// Route::post('assign/subject/update/{class_id}', [AssignSubjectController::class, 'UpdateAssignSubject'])->name('update.assign.subject');

// Route::get('assign/subject/details/{class_id}', [AssignSubjectController::class, 'DetailsAssignSubject'])->name('assign.subject.details');





}); 


/// Student Registration Routes   
Route::prefix('students')->group(function(){

Route::get('/reg/view', [StudentRegController::class, 'StudentRegView'])->name('student.registration.view');

Route::get('/reg/Add', [StudentRegController::class, 'StudentRegAdd'])->name('student.registration.add');

Route::post('/reg/store', [StudentRegController::class, 'StudentRegStore'])->name('store.student.registration');
 
Route::get('/year/class/wise', [StudentRegController::class, 'StudentClassYearWise'])->name('student.year.class.wise');
Route::get('/reg/search', [StudentRegController::class, 'StudentClassYearWise'])->name('student.registration.search');

Route::get('/students/download-pdf', [StudentRegController::class, 'GenerateClassListPDF'])->name('students.download.pdf');

Route::get('/reg/edit/{student_id}', [StudentRegController::class, 'StudentRegEdit'])->name('student.registration.edit');

Route::post('/reg/update/{student_id}', [StudentRegController::class, 'StudentRegUpdate'])->name('update.student.registration');
// Ajoutez ces routes manquantes :
Route::get('/reg/promotion/{student_id}', [StudentRegController::class, 'StudentRegPromotion'])->name('student.registration.promotion');
Route::post('/reg/promotion-update/{student_id}', [StudentRegController::class, 'StudentUpdatePromotion'])->name('promotion.student.registration');
    
Route::get('/reg/details/{student_id}', [StudentRegController::class, 'StudentRegDetails'])->name('student.registration.details');
Route::delete('/reg/delete/{student_id}', [StudentRegController::class, 'StudentRegDelete'])->name('student.registration.delete');
/////////// Recherche intelligente de doublons/////////////////////////////////////////////
Route::get('/students/search-name', [StudentRegController::class, 'searchByName'])->name('students.searchByName');


///////////////////////////////////////////////////////////////////////////////////////
// STudents Absences///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
Route::get('/absence/view', [StudentAbsController::class, 'StudentAbsenceView'])->name('student.absence.view');
// Fetch students and absences
Route::get('/absences/get', [StudentAbsController::class, 'getStudentAbsences'])->name('student.absences.getstudent');
// Store absences
Route::post('/absences/store', [StudentAbsController::class, 'storeStudentAbsences'])->name('absences.entry.store');
Route::get('/absence/view-livewire', AbsenceManager::class)->name('absence.livewire');

// Auto-save absence
Route::post('/absences/auto-save', [StudentAbsController::class, 'autoSaveStudentAbsence'])->name('absences.auto.save');
/////////////////////////////////////////////////////////////////////////////////////////

//Route::get('/reg/promotion/{student_id}', [StudentRegController::class, 'StudentRegPromotion'])->name('student.registration.promotion');

//Route::post('/reg/update/promotion/{student_id}', [StudentRegController::class, 'StudentUpdatePromotion'])->name('promotion.student.registration');

//Route::get('/reg/details/{student_id}', [StudentRegController::class, 'StudentRegDetails'])->name('student.registration.details');

//Route::get('student/registration/delete/{student_id}', [StudentRegController::class, 'StudentRegDelete'])->name('student.registration.delete');


// Student Roll Generate Routes 
Route::get('/roll/generate/view', [StudentRollController::class, 'StudentRollView'])->name('roll.generate.view');

Route::get('/reg/getstudents', [StudentRollController::class, 'GetStudents'])->name('student.registration.getstudents');

//Route::post('/roll/generate/store', [StudentRollController::class, 'StudentRollStore'])->name('roll.generate.store');
Route::get('/roll/get/classes', [StudentRollController::class, 'GetClassesByYear'])->name('roll.get.classes');
Route::get('/roll/generate/pdf', [StudentRollController::class, 'GenerateRegisterPDF'])->name('roll.generate.pdf');
Route::get('/roll/generate/all', [StudentRollController::class, 'GenerateAllRegisters'])->name('roll.generate.all');


// Registration Fee Routes 
Route::get('/reg/fee/view', [RegistrationFeeController::class, 'RegFeeView'])->name('registration.fee.view');

Route::get('/reg/fee/classwisedata', [RegistrationFeeController::class, 'RegFeeClassData'])->name('student.registration.fee.classwise.get');

Route::get('/reg/fee/payslip', [RegistrationFeeController::class, 'RegFeePayslip'])->name('student.registration.fee.payslip');


// Monthly Fee Routes 
Route::get('/monthly/fee/view', [MonthlyFeeController::class, 'MonthlyFeeView'])->name('monthly.fee.view');

Route::get('/monthly/fee/classwisedata', [MonthlyFeeController::class, 'MonthlyFeeClassData'])->name('student.monthly.fee.classwise.get');

Route::get('/monthly/fee/payslip', [MonthlyFeeController::class, 'MonthlyFeePayslip'])->name('student.monthly.fee.payslip');

// Exam Fee Routes 
Route::get('/exam/fee/view', [ExamFeeController::class, 'ExamFeeView'])->name('exam.fee.view');

Route::get('/exam/fee/classwisedata', [ExamFeeController::class, 'ExamFeeClassData'])->name('student.exam.fee.classwise.get');

Route::get('/exam/fee/payslip', [ExamFeeController::class, 'ExamFeePayslip'])->name('student.exam.fee.payslip');

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// MISE A JOUR PROMOTION /REDOUBLE/EXCUSION DES ELEVES/////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Routes pour la promotion
//Route::get('/promotion/bulk', [StudentPromotionController::class, 'bulkPromotionView'])->name('student.promotion.bulk');
//Route::post('/promotion/bulk/process', [StudentPromotionController::class, 'processBulkPromotion'])->name('student.promotion.bulk.process');
//Route::get('/promotion/statistics/{year_id?}', [StudentPromotionController::class, 'statistics'])->name('student.promotion.statistics');

//Route::get('/students/promotion/class-statistics/{year_id}', [StudentPromotionController::class, 'classStatisticsAjax'])->name('student.promotion.statistics.class');

//Route::get('/promotion/{student_id}', [StudentPromotionController::class, 'promotionView'])->name('student.promotion.view');
//Route::post('/promotion/process/{student_id}', [StudentPromotionController::class, 'processPromotion'])->name('student.promotion.process');

//
// Route pour l'annulation
//Route::delete('/promotion/cancel/{history_id}', [StudentPromotionController::class, 'cancelPromotionDecision'])
//    ->name('student.promotion.cancel');
Route::get('promotion/bulk', [StudentPromotionController::class, 'bulkPromotionView'])->name('student.promotion.bulk');
    Route::post('promotion/bulk/process', [StudentPromotionController::class, 'processBulkPromotion'])->name('student.promotion.bulk.process');

    Route::get('promotion/statistics', [StudentPromotionController::class, 'statistics'])->name('student.promotion.statistics');

    Route::post('bulk/promotion/cancel', [StudentPromotionController::class, 'cancelPromotion'])
    ->name('student.promotion.cancel');

    Route::get('promotion/statistics/ajax/{year_id}', [StudentPromotionController::class, 'classStatisticsAjax'])->name('student.promotion.statistics.ajax');

    Route::get('promotion/{student_id}', [StudentPromotionController::class, 'promotionView'])->name('student.promotion.view');
    Route::post('promotion/{student_id}', [StudentPromotionController::class, 'processPromotion'])->name('student.promotion.process');
    // Historique des statuts d'un élève
    Route::get('/student/status-history/{student_id}', [StudentPromotionController::class, 'statusHistoryView'])
        ->name('student.status.history');
    //Route::delete('promotion/cancel/{history_id}', [\App\Http\Controllers\Backend\Student\StudentPromotionController::class, 'cancelPromotionDecision'])->name('student.promotion.cancel');


//////////// FIN DE MISE A JOUR//////////////////////////////////////////////////////////////////////////////

}); 


/// Employee Registration Routes
Route::prefix('employees')->group(function(){

Route::get('reg/employee/view', [EmployeeRegController::class, 'EmployeeView'])->name('employee.registration.view');

Route::get('reg/employee/list', [EmployeeRegController::class, 'EmployeeList'])->name('employee.registration.list');

Route::get('reg/employee/add', [EmployeeRegController::class, 'EmployeeAdd'])->name('employee.registration.add');

Route::post('reg/employee/store', [EmployeeRegController::class, 'EmployeeStore'])->name('store.employee.registration');
 
Route::get('reg/employee/edit/{id}', [EmployeeRegController::class, 'EmployeeEdit'])->name('employee.registration.edit');

Route::post('reg/employee/update/{id}', [EmployeeRegController::class, 'EmployeeUpdate'])->name('update.employee.registration');

Route::get('reg/employee/details/{id}', [EmployeeRegController::class, 'EmployeeDetails'])->name('employee.registration.details');



// Employee Salary All Routes 
Route::get('salary/employee/view', [EmployeeSalaryController::class, 'SalaryView'])->name('employee.salary.view');

Route::get('salary/employee/increment/{id}', [EmployeeSalaryController::class, 'SalaryIncrement'])->name('employee.salary.increment');

Route::post('salary/employee/store/{id}', [EmployeeSalaryController::class, 'SalaryStore'])->name('update.increment.store');

Route::get('salary/employee/details/{id}', [EmployeeSalaryController::class, 'SalaryDetails'])->name('employee.salary.details');


// Employee Leave All Routes 
Route::get('leave/employee/view', [EmployeeLeaveController::class, 'LeaveView'])->name('employee.leave.view');

Route::get('leave/employee/add', [EmployeeLeaveController::class, 'LeaveAdd'])->name('employee.leave.add');

Route::post('leave/employee/store', [EmployeeLeaveController::class, 'LeaveStore'])->name('store.employee.leave');

Route::get('leave/employee/edit/{id}', [EmployeeLeaveController::class, 'LeaveEdit'])->name('employee.leave.edit');

Route::post('leave/employee/update/{id}', [EmployeeLeaveController::class, 'LeaveUpdate'])->name('update.employee.leave');

Route::get('leave/employee/delete/{id}', [EmployeeLeaveController::class, 'LeaveDelete'])->name('employee.leave.delete');



// Employee Attendance All Routes 
Route::get('attendance/employee/view', [EmployeeAttendanceController::class, 'AttendanceView'])->name('employee.attendance.view');

Route::get('attendance/employee/add', [EmployeeAttendanceController::class, 'AttendanceAdd'])->name('employee.attendance.add');

Route::post('attendance/employee/store', [EmployeeAttendanceController::class, 'AttendanceStore'])->name('store.employee.attendance');

Route::get('attendance/employee/edit/{date}', [EmployeeAttendanceController::class, 'AttendanceEdit'])->name('employee.attendance.edit');

Route::get('attendance/employee/details/{date}', [EmployeeAttendanceController::class, 'AttendanceDetails'])->name('employee.attendance.details');


// Employee Monthly Salary All Routes 
Route::get('monthly/salary/view', [MonthlySalaryController::class, 'MonthlySalaryView'])->name('employee.monthly.salary');

Route::get('monthly/salary/get', [MonthlySalaryController::class, 'MonthlySalaryGet'])->name('employee.monthly.salary.get');

Route::get('monthly/salary/payslip/{employee_id}', [MonthlySalaryController::class, 'MonthlySalaryPayslip'])->name('employee.monthly.salary.payslip');




});


/// Marks Management Routes  
Route::prefix('marks')->group(function(){


// Route principale pour l'interface unifiée
    Route::get('manage', [MarksController::class, 'MarksManage'])->name('marks.manage');
    
    // Routes pour les opérations
    Route::post('store', [MarksController::class, 'MarksStore'])->name('marks.store');
    Route::post('update', [MarksController::class, 'MarksUpdate'])->name('marks.update');
    Route::post('auto-save', [MarksController::class, 'MarksAutoSave'])->name('marks.auto.save');
    Route::post('update-single', [MarksController::class, 'UpdateSingleMark'])->name('marks.update.single');
    
    // Routes pour les données
    Route::get('get-students', [MarksController::class, 'MarksGetStudents'])->name('marks.getstudents');
    Route::get('get-subjects', [MarksController::class, 'GetSubjects'])->name('marks.getsubjects');
    Route::get('get-termtypes', [MarksController::class, 'GetTermTypes'])->name('marks.gettermtypes');
    Route::get('get-students-edit', [MarksController::class, 'MarksEditGetStudents'])->name('student.edit.getstudents');
/* Route::get('marks/entry/add', [MarksController::class, 'MarksAdd'])->name('marks.entry.add');

Route::post('marks/entry/store', [MarksController::class, 'MarksStore'])->name('marks.entry.store'); 

Route::post('marks/auto/save', [MarksController::class, 'MarksStore'])->name('marks.auto.save');

Route::get('marks/entry/edit', [MarksController::class, 'MarksEdit'])->name('marks.entry.edit'); 
Route::get('marks/get-subjects', [MarksController::class, 'GetSubjects'])->name('marks.getsubjects');
Route::get('marks/get-termtype', [MarksController::class, 'GetTermTypes'])->name('marks.gettermtypes');

Route::get('marks/getstudents/edit', [MarksController::class, 'MarksEditGetStudents'])->name('student.edit.getstudents');

Route::post('marks/entry/update', [MarksController::class, 'MarksUpdate'])->name('marks.entry.update');

Route::post('marks/autoupdate/save', [MarksController::class, 'MarksUpdate'])->name('marks.autoupdate.save');

//Route::get('marks/getsubjects', [MarksController::class, 'GetSubjects'])->name('marks.getsubjects');
    
//Route::get('marks/getstudents', [MarksController::class, 'MarksGetStudents'])->name('student.getstudents');

Route::get('marks/getstudent', [MarksController::class, 'MarksGetStudent'])->name('student.marks.getstudent');
 */
// Marks Entry Grade 

Route::get('marks/grade/view', [GradeController::class, 'MarksGradeView'])->name('marks.entry.grade');

Route::get('marks/grade/add', [GradeController::class, 'MarksGradeAdd'])->name('marks.grade.add');

Route::post('marks/grade/store', [GradeController::class, 'MarksGradeStore'])->name('store.marks.grade');

Route::get('marks/grade/edit/{id}', [GradeController::class, 'MarksGradeEdit'])->name('marks.grade.edit');

Route::post('marks/grade/update/{id}', [GradeController::class, 'MarksGradeUpdate'])->name('update.marks.grade');



});

Route::get('marks/getsubject', [DefaultController::class, 'GetSubject'])->name('marks.getsubject');

Route::get('student/marks/getstudents', [DefaultController::class, 'GetStudents'])->name('student.marks.getstudents');


 
 /// Account Management Routes  
Route::prefix('accounts')->group(function(){

Route::get('student/fee/view', [StudentFeeController::class, 'StudentFeeView'])->name('student.fee.view');

Route::get('student/fee/add', [StudentFeeController::class, 'StudentFeeAdd'])->name('student.fee.add');

Route::get('student/fee/getstudent', [StudentFeeController::class, 'StudentFeeGetStudent'])->name('account.fee.getstudent'); 

Route::post('student/fee/store', [StudentFeeController::class, 'StudentFeeStore'])->name('account.fee.store'); 


// Employee Salary Routes
Route::get('account/salary/view', [AccountSalaryController::class, 'AccountSalaryView'])->name('account.salary.view');

Route::get('account/salary/add', [AccountSalaryController::class, 'AccountSalaryAdd'])->name('account.salary.add');

Route::get('account/salary/getemployee', [AccountSalaryController::class, 'AccountSalaryGetEmployee'])->name('account.salary.getemployee');

Route::post('account/salary/store', [AccountSalaryController::class, 'AccountSalaryStore'])->name('account.salary.store');


// Other Cost Rotues 

Route::get('other/cost/view', [OtherCostController::class, 'OtherCostView'])->name('other.cost.view');

Route::get('other/cost/add', [OtherCostController::class, 'OtherCostAdd'])->name('other.cost.add');

Route::post('other/cost/store', [OtherCostController::class, 'OtherCostStore'])->name('store.other.cost');

Route::get('other/cost/edit/{id}', [OtherCostController::class, 'OtherCostEdit'])->name('edit.other.cost');

Route::post('other/cost/update/{id}', [OtherCostController::class, 'OtherCostUpdate'])->name('update.other.cost');



}); 


/// Report Management All Routes  
Route::prefix('reports')->group(function(){

Route::get('monthly/profit/view', [ProfiteController::class, 'MonthlyProfitView'])->name('monthly.profit.view');

Route::get('monthly/profit/datewais', [ProfiteController::class, 'MonthlyProfitDatewais'])->name('report.profit.datewais.get');

Route::get('monthly/profit/pdf', [ProfiteController::class, 'MonthlyProfitPdf'])->name('report.profit.pdf');


// MarkSheet Generate Routes 
//Route::prefix('marksheet')->group(function () {
    Route::get('/marksheet/generate/view', [MarkSheetController::class, 'MarkSheetView'])->name('marksheet.generate.view');
    Route::get('/marksheet/display', [MarkSheetController::class, 'DisplayMarksheet'])->name('marksheet.display');
Route::get('/marksheet/download', [MarkSheetController::class, 'DownloadMarksheet'])->name('marksheet.download');

// BulletinSheet Generate Routes 
//--Route::get('/bulletin/generate/view', [BulletinSheetController::class, 'BulletinView'])->name('bulletinsheet.generate.view');
Route::get('/bulletin/display/display', [BulletinSheetController::class, 'GenerateBulletin'])->name('bulletin.generate.display');
Route::get('/bulletin/display/report/download', [BulletinSheetController::class, 'DownloadBulletinReport'])->name('report.bulletin.download');// Download Bilan Bulletin
Route::get('/report/bulletin/marksheets/download', [BulletinSheetController::class, 'DownloadStudentMarksheets'])->name('report.bulletin.marksheets.download');// Download Students Marksheets
// Route to generate individual marksheets for each student in a single PDF
Route::get('/report/class-marksheets/download', [BulletinSheetController::class, 'generateClassMarksheets'])->name('report.class.marksheets.download');
// Classement Par Moyenne et Rang

Route::get('/report/class_rank_management/download', [BulletinSheetController::class, 'generateClassMeanStatistics'])->name('report.bulletin.class_rank_management.download');
// Classement Par Moyenne et Rang 
// Classement Par Moyenne et Rang

Route::get('/report/class_mean_management/download', [BulletinSheetController::class, 'generateClassMeanStatisticsDetails'])->name('report.bulletin.mean_statistics.download');

// Statistiques par Matieres

Route::get('/report/class_subjects_statistics/download', [BulletinSheetController::class, 'generateClassMeanStatisticsSubjects'])->name('report.bulletin.subject_mean_statistics.download');
//////////////////////////////////////////////////////MISE A JOUR/////////////////////////////////////////////////
Route::prefix('bulletins')->name('bulletins.')->group(function() {
        Route::get('/', [BulletinController::class, 'index'])->name('index');
        Route::post('generate', [BulletinController::class, 'generate'])->name('generate');
        Route::get('download', [BulletinController::class, 'download'])->name('download');
    });


//}); 

// Attendance Report Routes 
Route::get('attendance/report/view', [AttenReportController::class, 'AttenReportView'])->name('attendance.report.view');

Route::get('report/attendance/get', [AttenReportController::class, 'AttenReportGet'])->name('report.attendance.get');


// Student Result Report Routes 
Route::get('student/result/view', [ResultReportController::class, 'ResultView'])->name('student.result.view');

Route::get('student/result/get', [ResultReportController::class, 'ResultGet'])->name('report.student.result.get');


// Student ID Card Routes 
Route::get('student/idcard/view', [ResultReportController::class, 'IdcardView'])->name('student.idcard.view');

Route::get('student/idcard/get', [ResultReportController::class, 'IdcardGet'])->name('report.student.idcard.get');



});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ici nous définissons toutes les routes liées aux bulletins et statistiques.
| Les controllers sont organisés dans le namespace App\Http\Controllers\Reports.
|
*/

Route::prefix('report')->name('report.')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour la gestion des bulletins
    |--------------------------------------------------------------------------
    */
    Route::prefix('bulletins')->name('bulletins.')->group(function () {
        // Affichage de la page principale (sélection classe/année/période)
        Route::get('/', [BulletinController::class, 'index'])->name('index');

        // Afficher le bulletin d’un élève
        Route::get('/{student}/show', [BulletinController::class, 'show'])
            ->whereNumber('student')
            ->name('show');

        // Télécharger le bulletin PDF d’un élève
        Route::get('/{student}/download', [BulletinController::class, 'downloadBulletinPDF'])
            ->whereNumber('student')
            ->name('download');

        // Générer les bulletins d’une classe (vue collective)
        Route::post('/class', [BulletinController::class, 'generateClassMarksheets'])
            ->name('class');
    });

    /*
    |--------------------------------------------------------------------------
    | Routes pour les statistiques
    |--------------------------------------------------------------------------
    */
    Route::prefix('statistics')->name('statistics.')->group(function () {
        // Statistiques globales par classe
        Route::post('/class', [StatisticsController::class, 'classStatistics'])
            ->name('class');

        // Statistiques détaillées par matière
        Route::post('/subjects', [StatisticsController::class, 'subjectStatistics'])
            ->name('subjects');

        // Gestion des classements (par élève, par matière)
        Route::post('/ranks', [StatisticsController::class, 'classRanks'])
            ->name('ranks');

        // Télécharger version PDF des statistiques
        Route::post('/class/pdf', [StatisticsController::class, 'downloadClassStatisticsPDF'])
            ->name('class.pdf');
        Route::post('/subjects/pdf', [StatisticsController::class, 'downloadSubjectStatisticsPDF'])
            ->name('subjects.pdf');
    });

});
///-----------------------------------------------------------------------------------------------------
//--------------------TIME TABLE ROUTES---------------------------------------------------------------
//------------------------------------------------------------------------------------------------------
// Routes pour les emplois du temps
Route::prefix('timetable')->group(function () {
    Route::get('/', [TimeTableController::class, 'index'])->name('timetable.index');
    Route::get('/create', [TimeTableController::class, 'create'])->name('timetable.create');
    Route::post('/', [TimeTableController::class, 'store'])->name('timetable.store');
    Route::get('/{timetable}', [TimeTableController::class, 'show'])->name('timetable.show');
    Route::get('/{timetable}/edit', [TimeTableController::class, 'edit'])->name('timetable.edit');
    Route::put('/{timetable}', [TimeTableController::class, 'update'])->name('timetable.update');
    Route::delete('/{timetable}', [TimeTableController::class, 'destroy'])->name('timetable.destroy');
    Route::patch('/{timetable}/toggle-active', [TimeTableController::class, 'toggleActive'])->name('timetable.toggle-active');
    Route::post('/generate', [TimeTableController::class, 'generate'])->name('timetable.generate');
    Route::get('/teacher/{teacher}', [TimeTableController::class, 'teacherView'])->name('timetable.teacher-view');
    Route::get('/class/{class}', [TimeTableController::class, 'classView'])->name('timetable.class-view');
});

//----------------------------------------------------------------------------------------------------------
//==========================================================================================================
//==========================================================================================================
// Routes pour le module de bulletins
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('bulletin', [BulletinSheetController::class, 'index'])->name('bulletin.view');
        
        // ✅ ANCIENNE méthode synchrone (garder pour compatibilité)
        Route::post('bulletin/generate', [BulletinSheetController::class, 'generate'])->name('bulletin.generate');
        
        Route::post('bulletin/preview', [BulletinSheetController::class, 'preview'])->name('bulletin.preview');
        
        // API pour une interface moderne
        Route::post('bulletin/api/generate', [BulletinSheetController::class, 'apiGenerate'])->name('bulletin.api.generate');

        // ✅ CORRECTION : Routes pour le système asynchrone avec la NOUVELLE méthode
        Route::post('bulletin/generate-async', [BulletinSheetController::class, 'generateAsync'])->name('bulletin.generate.async');
        Route::get('bulletin/progress/{job_id}', [BulletinSheetController::class, 'checkProgress'])->name('bulletin.progress');
        Route::get('bulletin/download/{job_id}', [BulletinSheetController::class, 'downloadResult'])->name('bulletin.download');
    });
});

//Routes pour les statistiques de classes et par metieres
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('statistics', [TermStatisticsController::class, 'index'])->name('statistics.index');
        // Sync generation (immediate download)
        Route::post('generate/class', [TermStatisticsController::class, 'generateClassPdf'])->name('statistics.generate.class');
        Route::post('generate/subject', [TermStatisticsController::class, 'generateSubjectPdf'])->name('statistics.generate.subject');
        Route::post('generate/all', [TermStatisticsController::class, 'generateAllZip'])->name('statistics.generate.all');

        // Async endpoints (optional)
        Route::post('generate/async', [TermStatisticsController::class, 'generateAsync'])->name('statistics.generate.async');
        Route::get('progress/{jobId}', [TermStatisticsController::class, 'checkProgress'])->name('statistics.progress');
        Route::get('download/{jobId}', [TermStatisticsController::class, 'downloadResult'])->name('statistics.download');
    });
});


// 🔍 ROUTE TEMPORAIRE DE DÉBOGAGE - À SUPPRIMER APRÈS RÉSOLUTION








});//End Middleware Ath Route





