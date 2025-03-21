<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Custom auth forget and reset password routes
// User Password Reset Routes
Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');




Route::get('/', function () {
	if(Auth::check()){
	  if(Auth::user()->type==4)
		  return  redirect(app()->getLocale().'/patient_dash/info');
	  else
		  return  redirect(app()->getLocale().'/dashboard');
     }else{
	     return  redirect(app()->getLocale().'/login');
	}
});

Route::get('/login', function () {
	if(Auth::check()){
	  if(Auth::user()->type==4)
		  return  redirect(app()->getLocale().'/patient_dash/info');
	  else
		  return  redirect(app()->getLocale().'/dashboard');
     }else{
	     return  redirect(app()->getLocale().'/login');
	   }
});

Route::get('/dashboard', function () {
	if(Auth::check()){
	  if(Auth::user()->type==4)
		  return  redirect(app()->getLocale().'/patient_dash/info');
	  else
		  return  redirect(app()->getLocale().'/dashboard');
     }else{
	     return  redirect(app()->getLocale().'/login');
	   }
});

   //Custom auth forget and reset password routes
  //outside routes
  // User Password Reset Routes
 Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
 Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
 Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

 //files get
 Route::get('/furl/{file?}','App\Http\Controllers\FileController@get')->where('file','(.*)')->middleware('auth');
 //qrCode path
 Route::get('/qrndmcde','App\Http\Controllers\FileController@getQrCodeFile')->name('qrCode.getFile');


Route::group(['prefix' => '/{locale}',
              'where' => ['locale' => '[a-zA-Z]{2}'],
              'middleware' => 'setlocale'], function ($locale) {
                //authentication routes
				Route::get('/', function () {
					return view('auth.login');
				});

				//Auth::routes();
		        //Custom auth forget and reset password routes
				// User Password Reset Routes
				Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');

				// User Authentication Routes
				Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
				Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
				Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

				// User Registration Routes
				//Route::get('register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
				//Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
                //registeration patient
			    Route::get('inforegister', 'App\Http\Controllers\registration\RegistrationPatientController@showRegistration')->name('inforegister');
		        Route::post('registerB', [App\Http\Controllers\registration\RegistrationPatientController::class, 'registerB'])->name('registerB');
				Route::get('inforegister/verify_mail', [App\Http\Controllers\registration\RegistrationPatientController::class, 'confirmB'])->name('confirmB');
                Route::get('inforegister/confirm_account', 'App\Http\Controllers\registration\RegistrationPatientController@inforegister_confirm')->name('inforegister_confirm');

				// User Verification Routes
				Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
				Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
				Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

				Route::get('userinforegister', 'App\Http\Controllers\PatientController@showUserRegistration')->name('userinforegister');
		        Route::post('registerPassword', [App\Http\Controllers\PatientController::class, 'registerPassword'])->name('registerPassword');

				//sms link
				Route::get('sms/link', 'App\Http\Controllers\lab\visit\VisitController@sms_link')->name('open_sms_link');
			    //activate guarantor User
			    Route::get('guarantor/activate/{hash}', [App\Http\Controllers\external_sections\ExtLabsController::class,'activate'])->name('external_labs.activate');

});

//main routes for authenticated users as patient with locales
Route::group(['prefix' => '/{locale}',
              'where' => ['locale' => '[a-zA-Z]{2}'],
              'middleware' => ['setlocale','auth','PatientRules']], function ($locale) {
     //dashboard
	 Route::get('patient_dash/info', [App\Http\Controllers\external_patient\DashboardController::class, 'index'])->name('patient_dash.index');
     Route::get('patient_dash/visit/{id}/view', [App\Http\Controllers\external_patient\DashboardController::class, 'view_visits'])->name('patient_dash.visit.view');
     Route::post('patient_dash_chgpass', [App\Http\Controllers\external_patient\DashboardController::class, 'chg_pass'])->name('patient_dash.chg_pass');
	 Route::post('patient_dash_generatePDFOrder', [App\Http\Controllers\external_patient\DashboardController::class, 'pat_pdf_order'])->name('patient_dash.generatePDFOrder');
	 Route::post('changePatUserlanguage', [App\Http\Controllers\external_patient\DashboardController::class, 'changePatUserlanguage'])->name('patient_dash.changePatUserlanguage');
});

//main routes for authenticated  users other than patient
//group routes with languange and auth
Route::group(['prefix' => '/{locale}',
              'where' => ['locale' => '[a-zA-Z]{2}'],
              'middleware' => ['setlocale','auth','GeneralRules']], function ($locale) {


				//lab tests routes
				Route::get('tests/all', [App\Http\Controllers\tests\TestsController::class, 'index'])->name('lab.tests.index');
				Route::get('tests/new_code', [App\Http\Controllers\tests\TestsController::class, 'new'])->name('lab.tests.new');
				Route::post('store_lab_test', [App\Http\Controllers\tests\TestsController::class, 'store'])->name('lab.tests.store');
				Route::get('tests/{id}/edit_code', [App\Http\Controllers\tests\TestsController::class, 'edit'])->name('lab.tests.edit');
				Route::post('update_lab_test', [App\Http\Controllers\tests\TestsController::class, 'update'])->name('lab.tests.update');
				Route::post('delete_lab_test', [App\Http\Controllers\tests\TestsController::class, 'remove'])->name('lab.tests.remove');
				Route::post('cancel_lab_test', [App\Http\Controllers\tests\TestsController::class, 'cancel'])->name('lab.tests.cancel');
				Route::delete('toggle_lab_test', [App\Http\Controllers\tests\TestsController::class, 'destroy'])->name('lab.tests.delete');
				Route::post('get_lab_test_info', [App\Http\Controllers\tests\TestsController::class, 'get_info'])->name('lab.tests.get_info');
                Route::post('deleteRow_lab_test', [App\Http\Controllers\tests\TestsController::class, 'deleteRow'])->name('lab.tests.deleteRow');
                Route::post('special_considerations', [App\Http\Controllers\tests\TestsController::class, 'specConsideration'])->name('lab.tests.specConsideration');
                Route::post('get_specimens', [App\Http\Controllers\tests\TestsController::class, 'get_specimens'])->name('lab.tests.Specimen');
				Route::post('textResult', [App\Http\Controllers\tests\TestsController::class, 'textResult'])->name('lab.tests.textResult');
				Route::post('updateOrder', [App\Http\Controllers\tests\TestsController::class, 'updateOrder'])->name('lab.tests.updateOrder');
				Route::post('updateTestCode', [App\Http\Controllers\tests\TestsController::class, 'updateTestCode'])->name('lab.tests.updateTestCode');

				//lab tests fields routes
				Route::get('tests_fields/all', [App\Http\Controllers\tests\TestsFieldsController::class, 'index'])->name('lab.tests_fields.index');
				Route::post('store_test_field', [App\Http\Controllers\tests\TestsFieldsController::class, 'store'])->name('lab.tests_fields.store');
				Route::delete('toggle_test_field', [App\Http\Controllers\tests\TestsFieldsController::class, 'destroy'])->name('lab.tests_fields.delete');
				Route::post('get_test_field', [App\Http\Controllers\tests\TestsFieldsController::class, 'get_info'])->name('lab.tests_fields.get_info');
				Route::post('get_tests_info', [App\Http\Controllers\tests\TestsFieldsController::class, 'get_tests_info'])->name('lab.tests_fields.get_tests');
				Route::post('filter_code', [App\Http\Controllers\tests\TestsFieldsController::class, 'filter_code'])->name('lab.tests_fields.filter_code');
				Route::post('inactiveFields', [App\Http\Controllers\tests\TestsFieldsController::class, 'inactiveFields'])->name('lab.tests_fields.inactiveFields');
				Route::post('chkFieldOrder', [App\Http\Controllers\tests\TestsFieldsController::class, 'chkFieldOrder'])->name('lab.tests_fields.chkFieldOrder');

				//lab tests profiles routes
				Route::get('tests_profiles/all', [App\Http\Controllers\tests\TestsProfilesController::class, 'index'])->name('lab.tests_profiles.index');
				Route::post('store_lab_testP', [App\Http\Controllers\tests\TestsProfilesController::class, 'store'])->name('lab.tests_profiles.store');
				Route::delete('toggle_lab_testP', [App\Http\Controllers\tests\TestsProfilesController::class, 'destroy'])->name('lab.tests_profiles.delete');
				Route::post('get_lab_testP', [App\Http\Controllers\tests\TestsProfilesController::class, 'get_info'])->name('lab.tests_profiles.get_info');

				//lab tests formulas routes
				Route::get('tests_formulas/all', [App\Http\Controllers\tests\TestsFormulasController::class, 'index'])->name('lab.tests_formulas.index');
				Route::post('store_lab_testF', [App\Http\Controllers\tests\TestsFormulasController::class, 'store'])->name('lab.tests_formulas.store');
				Route::delete('toggle_lab_testF', [App\Http\Controllers\tests\TestsFormulasController::class, 'destroy'])->name('lab.tests_formulas.delete');
				Route::post('get_lab_testF', [App\Http\Controllers\tests\TestsFormulasController::class, 'get_info'])->name('lab.tests_formulas.get_info');

                //lab tests groups routes
				Route::get('tests_groups/all', [App\Http\Controllers\tests\TestsGroupsController::class, 'index'])->name('lab.tests_groups.index');
				Route::post('store_test_group', [App\Http\Controllers\tests\TestsGroupsController::class, 'store'])->name('lab.tests_groups.store');
				Route::delete('toggle_test_group', [App\Http\Controllers\tests\TestsGroupsController::class, 'destroy'])->name('lab.tests_groups.delete');
				Route::post('get_test_group', [App\Http\Controllers\tests\TestsGroupsController::class, 'get_info'])->name('lab.tests_groups.get_info');

				//lab tests categories routes
				Route::get('tests_categories/all', [App\Http\Controllers\tests\TestsCatController::class, 'index'])->name('lab.tests_cat.index');
				Route::post('store_test_cat', [App\Http\Controllers\tests\TestsCatController::class, 'store'])->name('lab.tests_cat.store');
				Route::delete('toggle_test_cat', [App\Http\Controllers\tests\TestsCatController::class, 'destroy'])->name('lab.tests_cat.delete');
				Route::post('get_test_cat', [App\Http\Controllers\tests\TestsCatController::class, 'get_info'])->name('lab.tests_cat.get_info');

				//lab tests S bacteria route
                Route::get('tests_sbacteria/all', [App\Http\Controllers\tests\TestsSBacteriaController::class, 'index'])->name('lab.tests_sbacteria.index');
				Route::post('store_test_sbacteria', [App\Http\Controllers\tests\TestsSBacteriaController::class, 'store'])->name('lab.tests_sbacteria.store');
				Route::delete('toggle_test_sbacteria', [App\Http\Controllers\tests\TestsSBacteriaController::class, 'destroy'])->name('lab.tests_sbacteria.delete');
				Route::post('get_test_sbacteria', [App\Http\Controllers\tests\TestsSBacteriaController::class, 'get_info'])->name('lab.tests_sbacteria.get_info');

				//lab tests Group antibiotic route
                Route::get('tests_gantibiotic/all', [App\Http\Controllers\tests\TestsGAntibioticController::class, 'index'])->name('lab.tests_gantibiotic.index');
				Route::post('store_test_gantibiotic', [App\Http\Controllers\tests\TestsGAntibioticController::class, 'store'])->name('lab.tests_gantibiotic.store');
				Route::delete('toggle_test_gantibiotic', [App\Http\Controllers\tests\TestsGAntibioticController::class, 'destroy'])->name('lab.tests_gantibiotic.delete');
				Route::post('get_test_gantibiotic', [App\Http\Controllers\tests\TestsGAntibioticController::class, 'get_info'])->name('lab.tests_gantibiotic.get_info');

				//lab tests bacteria route
                Route::get('tests_bacteria/all', [App\Http\Controllers\tests\TestsBacteriaController::class, 'index'])->name('lab.tests_bacteria.index');
				Route::post('store_test_bacteria', [App\Http\Controllers\tests\TestsBacteriaController::class, 'store'])->name('lab.tests_bacteria.store');
				Route::delete('toggle_test_bacteria', [App\Http\Controllers\tests\TestsBacteriaController::class, 'destroy'])->name('lab.tests_bacteria.delete');
				Route::post('get_test_bacteria', [App\Http\Controllers\tests\TestsBacteriaController::class, 'get_info'])->name('lab.tests_bacteria.get_info');

				//lab tests antibiotic route
                Route::get('tests_antibiotic/all', [App\Http\Controllers\tests\TestsAntibioticController::class, 'index'])->name('lab.tests_antibiotic.index');
				Route::post('store_test_antibiotic', [App\Http\Controllers\tests\TestsAntibioticController::class, 'store'])->name('lab.tests_antibiotic.store');
				Route::delete('toggle_test_antibiotic', [App\Http\Controllers\tests\TestsAntibioticController::class, 'destroy'])->name('lab.tests_antibiotic.delete');
				Route::post('get_test_antibiotic', [App\Http\Controllers\tests\TestsAntibioticController::class, 'get_info'])->name('lab.tests_antibiotic.get_info');

			   //succursale profile routes
				Route::get('profile/branch', [App\Http\Controllers\profile\BranchProfileController::class, 'index'])->name('profiles.clinic');
                Route::get('profile/branch/{id}/view', [App\Http\Controllers\profile\BranchProfileController::class, 'show'])->name('profiles.clinic.show');
				Route::put('profile/branch/{id}/info', [App\Http\Controllers\profile\BranchProfileController::class, 'update_info'])->name('profiles.clinic.info');
				Route::post('profile/branch/schedule', [App\Http\Controllers\profile\BranchProfileController::class, 'update_schedule'])->name('profiles.clinic.schedule');
                Route::put('profile/branch/{id}/doctors', [App\Http\Controllers\profile\BranchProfileController::class, 'update_professionals'])->name('profiles.clinic.doctors');
                Route::get('profiles/branch/language', [App\Http\Controllers\profile\BranchProfileController::class, 'changeUserClinic_language'])->name('profiles.clinic.changeUserCliniclanguage');
                Route::put('profiles/branch/exams', [App\Http\Controllers\profile\BranchProfileController::class, 'update_exams'])->name('profiles.clinic.exams');
                Route::post('save_bill', [App\Http\Controllers\profile\BranchProfileController::class, 'save_bill'])->name('save_bill');
                Route::post('profiles/branch/view_profile', [App\Http\Controllers\profile\BranchProfileController::class, 'show_profile'])->name('profiles.clinic.show_profile');
                Route::post('sms_email_setting', [App\Http\Controllers\profile\BranchProfileController::class, 'sms_email_setting'])->name('profiles.clinic.sms_email_setting');

				//doctor profile routes
                Route::get('profile/doctor', [App\Http\Controllers\profile\DoctorProfileController::class, 'index'])->name('profiles.doctor');
				Route::post('profile/doctor/view', [App\Http\Controllers\profile\DoctorProfileController::class, 'show_profile'])->name('profiles.doctor.show');
                Route::post('profile/doctor/signature', [App\Http\Controllers\profile\DoctorProfileController::class, 'upload_signature'])->name('profiles.doctor.upload_signature');
                Route::post('profile/doctor/photo', [App\Http\Controllers\profile\DoctorProfileController::class, 'upload_photo'])->name('profiles.doctor.profile_photo');
                Route::post('profile/doctor/password', [App\Http\Controllers\profile\DoctorProfileController::class, 'update_password'])->name('profiles.doctor.password_change');
				Route::put('profile/doctor/{id}/update', [App\Http\Controllers\profile\DoctorProfileController::class, 'update_info'])->name('profiles.doctor.update');
                Route::get('profile/doctor/language', [App\Http\Controllers\profile\DoctorProfileController::class, 'changeUserPro_language'])->name('profiles.doctor.changeUserProlanguage');

                //support routes
				Route::post('support', [App\Http\Controllers\SupportController::class, 'inquiry_message'])->name('support');
				Route::post('insert_remote_link', [App\Http\Controllers\SupportController::class, 'insert_remote_link'])->name('insert_remote_link');

				//dashboard routes
				Route::resource('dashboard', App\Http\Controllers\DashboardController::class);
				//sales
				Route::get('sales', [App\Http\Controllers\SalesController::class, 'index'])->name('sales.index');
				Route::get('sales/{id}/{typein}/NewCommandSales', [App\Http\Controllers\SalesController::class, 'NewCommandSales'])->name('NewCommandSales');
				Route::get('sales/{id}/{typein}/editcommandsales', [App\Http\Controllers\SalesController::class, 'editcommandsales'])->name('sales.editcommandsales');
				Route::post('sales/{id}/{typein}/SaveCommandSales', [App\Http\Controllers\SalesController::class, 'SaveCommandSales'])->name('SaveCommandSales');
				Route::get('fill_code_categoty', [App\Http\Controllers\SalesController::class, 'fill_code_categoty'])->name('fill_code_categoty');
				Route::get('fillSalesDel', [App\Http\Controllers\SalesController::class, 'fillSalesDel'])->name('fillSalesDel');
				Route::get('sales/{id}/fillSalesDel', [App\Http\Controllers\SalesController::class, 'fillSalesDel'])->name('fillSalesDel_ID');
				Route::post('SalesValidate1', [App\Http\Controllers\SalesController::class, 'SalesValidate1'])->name('SalesValidate1');
				Route::post('SalesValidate2', [App\Http\Controllers\SalesController::class, 'SalesValidate2'])->name('SalesValidate2');
				Route::post('SalesDone', [App\Http\Controllers\SalesControllerr::class, 'SalesDone'])->name('SalesDone');
				Route::post('SalesPaid', [App\Http\Controllers\SalesController::class, 'SalesPaid'])->name('SalesPaid');
				Route::get('sales/{id}/{typein}/fillPriceInvSales', [App\Http\Controllers\SalesController::class, 'fillPriceInvSales'])->name('fillPriceInvSales');
				Route::get('EfillPriceInvSales', [App\Http\Controllers\SalesController::class, 'EfillPriceInvSales'])->name('EfillPriceInvSales');
				Route::post('salesgenerate_cmd_pdf', [App\Http\Controllers\SalesController::class, 'salesgenerate_cmd_pdf'])->name('salesgenerate_cmd_pdf');
				Route::post('salessend_supplier_email', [App\Http\Controllers\SalesController::class, 'salessend_supplier_email'])->name('salessend_supplier_email');
				Route::post('salesdownloadPDFInvoice', [App\Http\Controllers\SalesController::class, 'salesdownloadPDFInvoice'])->name('salesdownloadPDFInvoice');

				//users route
				Route::get('userslist', [App\Http\Controllers\UsersListController::class, 'index'])->name('userslist.index');
				Route::get('userslist/create/lab', [App\Http\Controllers\UsersListController::class, 'create'])->name('userslist.create');
				Route::get('userslist/create/extdoctor', [App\Http\Controllers\UsersListController::class, 'create_doctor'])->name('userslist.create_doctor');
				Route::get('userslist/create/extlab', [App\Http\Controllers\UsersListController::class, 'create_lab'])->name('userslist.create_lab');
				Route::get('userslist/{id}/edit', [App\Http\Controllers\UsersListController::class, 'edit'])->name('userslist.edit');
				Route::post('userslist/store', [App\Http\Controllers\UsersListController::class, 'store'])->name('userslist.store');
				Route::post('userslist/update', [App\Http\Controllers\UsersListController::class, 'update'])->name('userslist.update');
				Route::delete('userslist/{id}/destroy', [App\Http\Controllers\UsersListController::class, 'destroy'])->name('userslist.destroy');
				Route::post('edit_user_password', [App\Http\Controllers\UsersListController::class, 'update_password'])->name('user.edit.password_change');
				Route::post('get_defined_profile', [App\Http\Controllers\UsersListController::class, 'get_defined_profile'])->name('get_defined_profile');
				Route::post('get_users', [App\Http\Controllers\UsersListController::class, 'get_users'])->name('get_users');

				//patients list route
				Route::resource('patientslist', App\Http\Controllers\PatientController::class);
				Route::post('patientslist/update_modal', [App\Http\Controllers\PatientController::class, 'patient_update_modal'])->name('patientslist.update_modal');
				Route::post('patientslist/inactiavte', [App\Http\Controllers\PatientController::class, 'inactivate_patient'])->name('patient.inactivate');
				Route::post('patientslist/activate', [App\Http\Controllers\PatientController::class, 'activate_patient'])->name('patient.activate');
				Route::post('patientslist/dashboard', [App\Http\Controllers\PatientController::class, 'dashboard_patient'])->name('patient.dashboard');
				Route::post('patientslist/get_reminder_data', [App\Http\Controllers\PatientController::class, 'get_reminder_data'])->name('patient.get_reminder_data');
				Route::post('patientslist/save_reminder_data', [App\Http\Controllers\PatientController::class, 'save_reminder_data'])->name('patient.save_reminder_data');
				Route::post('patientslist/new_cmd', [App\Http\Controllers\PatientController::class, 'new_cmd'])->name('patient.new_cmd');
				Route::post('patientslist/getPatResults', [App\Http\Controllers\PatientController::class, 'getPatResults'])->name('patient.getPatResults');
				Route::post('patientslist/validatePatResults', [App\Http\Controllers\PatientController::class, 'validatePatResults'])->name('patient.validatePatResults');
				Route::post('patientslist/getPatVisits', [App\Http\Controllers\PatientController::class, 'getPatVisits'])->name('patient.getPatVisits');
				Route::post('patientslist/new_visit', [App\Http\Controllers\PatientController::class, 'new_visit'])->name('patient.new_visit');
				Route::get('loadPat', [App\Http\Controllers\PatientController::class, 'loadPat'])->name('patient.loadPat');
             	Route::get('loadGuarantors', [App\Http\Controllers\PatientController::class, 'loadGuarantors'])->name('loadGuarantors');
				Route::get('loadDoctors', [App\Http\Controllers\PatientController::class, 'loadDoctors'])->name('loadDoctors');
				Route::post('createTitleTag', [App\Http\Controllers\PatientController::class, 'createTitleTag'])->name('createTitleTag');


				//lab reports route
				Route::get('lab/reports', [App\Http\Controllers\reports\ReportsController::class, 'index'])->name('reports.index');
                Route::get('lab/reports/sales_per_pay', [App\Http\Controllers\reports\ReportsController::class, 'pay_refund'])->name('reports.inventory_sales_per_pay');
				Route::get('lab/reports/all_requests', [App\Http\Controllers\reports\ReportsController::class, 'results'])->name('reports.daily_requests');
                Route::get('lab/reports/outreach_requests', [App\Http\Controllers\reports\ReportsController::class, 'outreachRequests'])->name('reports.daily_outreach_requests');
  			    Route::get('lab/reports/all_invoices', [App\Http\Controllers\reports\ReportsController::class, 'invoices'])->name('reports.daily_invoices');
				Route::get('lab/reports/invoices_per_pay', [App\Http\Controllers\reports\ReportsController::class, 'invoices_per_pay'])->name('reports.daily_invoices_per_payments');
  			    Route::get('lab/reports/invoices_per_test', [App\Http\Controllers\reports\ReportsController::class, 'invoices_per_test'])->name('reports.daily_invoices_per_tests');
				Route::get('lab/reports/tests_per_request', [App\Http\Controllers\reports\ReportsController::class, 'tests_per_request'])->name('reports.daily_tests_per_request');

				//old lab reports route
				Route::get('lab/reports/all_purchases', [App\Http\Controllers\reports\ReportsController::class, 'purchases'])->name('reports.inventory_purchases');
			    Route::get('lab/reports/sales_per_item', [App\Http\Controllers\reports\ReportsController::class, 'sales_per_item'])->name('reports.inventory_sales_per_item');
			    Route::get('lab/reports/sales_per_supplier', [App\Http\Controllers\reports\ReportsController::class, 'inventory_sales_per_supplier'])->name('reports.inventory_sales_per_supplier');
                Route::get('lab/reports/purchases_per_item', [App\Http\Controllers\reports\ReportsController::class, 'inventory_purchases_per_item'])->name('reports.inventory_purchases_per_item');
			    Route::get('lab/reports/purchases_per_supplier', [App\Http\Controllers\reports\ReportsController::class, 'inventory_purchases_per_supplier'])->name('reports.inventory_purchases_per_supplier');
                Route::get('lab/reports/purchases_per_pay', [App\Http\Controllers\reports\ReportsController::class, 'inventory_purchases_per_pay'])->name('reports.inventory_purchases_per_pay');

				//lab custom_reports route
				Route::get('lab/custom_reports', [App\Http\Controllers\reports\CustomReportsController::class, 'index'])->name('custom_reports.index');
				Route::post('custom_reports_getInfo', [App\Http\Controllers\reports\CustomReportsController::class, 'getInfo'])->name('custom_reports.getInfo');
				Route::post('custom_reports_create', [App\Http\Controllers\reports\CustomReportsController::class, 'create'])->name('custom_reports.create');
				Route::post('custom_reports_edit', [App\Http\Controllers\reports\CustomReportsController::class, 'edit'])->name('custom_reports.edit');
				Route::delete('custom_reports_delete', [App\Http\Controllers\reports\CustomReportsController::class, 'delete'])->name('custom_reports.delete');
				Route::post('custom_reports_print', [App\Http\Controllers\reports\CustomReportsController::class, 'printPDF'])->name('custom_reports.printPDF');

				//patients visit route
                Route::get('lab/requests', [App\Http\Controllers\lab\visit\VisitController::class, 'index'])->name('lab.visit.index');
				Route::get('fillPatientlab', [App\Http\Controllers\lab\visit\VisitController::class, 'fillPatientlab'])->name('fillPatientlab');
				Route::get('fillPatientDatalab', [App\Http\Controllers\lab\visit\VisitController::class, 'fillPatientDatalab'])->name('fillPatientDatalab');
				Route::get('lab/requests/edit/{id?}', [App\Http\Controllers\lab\visit\VisitController::class, 'edit'])->name('lab.visit.edit');
   				Route::post('lab/requests/delete', [App\Http\Controllers\lab\visit\VisitController::class, 'destroy'])->name('lab.visit.destroy');
				Route::post('lab/requests/create', [App\Http\Controllers\lab\visit\VisitController::class, 'create'])->name('lab.visit.create');
                Route::post('fill_instruments', [App\Http\Controllers\lab\visit\VisitController::class, 'fillInstruments'])->name('fill_instruments');
				Route::post('UpdateProf', [App\Http\Controllers\lab\visit\VisitController::class, 'UpdateProf'])->name('UpdateProf');
				Route::post('UpdateLab', [App\Http\Controllers\lab\visit\VisitController::class, 'UpdateLab'])->name('UpdateLab');
				Route::post('filterLabGroup', [App\Http\Controllers\lab\visit\VisitController::class, 'filterGroup'])->name('lab.visit.filterGroup');
				Route::post('filterLabTests', [App\Http\Controllers\lab\visit\VisitController::class, 'filterTests'])->name('lab.visit.filterTests');
				Route::post('saveLabOrder', [App\Http\Controllers\lab\visit\VisitController::class, 'saveOrder'])->name('lab.visit.saveOrder');
				Route::get('getLabResults', [App\Http\Controllers\lab\visit\VisitController::class, 'getResult'])->name('lab.visit.getResult');
				Route::get('getLabBill', [App\Http\Controllers\lab\visit\VisitController::class, 'getBill'])->name('lab.visit.getBill');
				Route::post('checkResultVal', [App\Http\Controllers\lab\visit\VisitController::class, 'checkResultVal'])->name('lab.visit.checkResultVal');
				Route::post('saveLabResults', [App\Http\Controllers\lab\visit\VisitController::class, 'saveResults'])->name('lab.visit.saveResults');
				Route::post('printLabResults', [App\Http\Controllers\lab\visit\VisitController::class, 'printResults'])->name('lab.visit.printResults');
				Route::post('sendLabResults', [App\Http\Controllers\lab\visit\VisitController::class, 'sendResults'])->name('lab.visit.sendResults');
				Route::post('validateLabResults', [App\Http\Controllers\lab\visit\VisitController::class, 'validateResults'])->name('lab.visit.validateResults');
				Route::post('uploadAttach', [App\Http\Controllers\lab\visit\VisitController::class, 'uploadAttach'])->name('lab.visit.uploadAttach');
				Route::delete('destroyAttach', [App\Http\Controllers\lab\visit\VisitController::class, 'destroyAttach'])->name('lab.visit.destroyAttach');
				Route::post('saveBill', [App\Http\Controllers\lab\visit\VisitController::class, 'saveBill'])->name('lab.visit.saveBill');
				Route::post('getProfileTests', [App\Http\Controllers\lab\visit\VisitController::class, 'getProfileTests'])->name('lab.visit.getProfileTests');
				Route::post('visit_save_pay', [App\Http\Controllers\lab\visit\VisitController::class, 'SavePay'])->name('lab.visit.save_pay');
				Route::post('visit_save_refund', [App\Http\Controllers\lab\visit\VisitController::class, 'SaveRefund'])->name('lab.visit.save_refund');
                Route::post('visit_save_discount', [App\Http\Controllers\lab\visit\VisitController::class, 'SaveDiscount'])->name('lab.visit.save_discount');
				Route::get('lab/requests/get_referredtests', [App\Http\Controllers\lab\visit\VisitController::class, 'get_referredtests'])->name('lab.visit.get_referredtests');
				Route::post('printLabOrder', [App\Http\Controllers\lab\visit\VisitController::class, 'printOrder'])->name('lab.visit.printOrder');
				Route::post('getBacteriaAntibiotics', [App\Http\Controllers\lab\visit\VisitController::class, 'getBacteriaAntibiotics'])->name('lab.visit.getBacteriaAntibiotics');
				Route::post('saveCultureData', [App\Http\Controllers\lab\visit\VisitController::class, 'saveCultureData'])->name('lab.visit.saveCultureData');
				Route::post('printCultureData', [App\Http\Controllers\lab\visit\VisitController::class, 'printCultureData'])->name('lab.visit.printCultureData');
				Route::post('patData', [App\Http\Controllers\lab\visit\VisitController::class, 'patData'])->name('lab.visit.pat_data');
				Route::post('addResultTag', [App\Http\Controllers\lab\visit\VisitController::class, 'addResultTag'])->name('lab.visit.addResultTag');
				Route::post('GetPDFBill', [App\Http\Controllers\lab\visit\VisitController::class, 'GetPDFBill'])->name('lab.visit.GetPDFBill');
				Route::post('opTemplateData', [App\Http\Controllers\lab\visit\VisitController::class, 'opTemplateData'])->name('lab.visit.opTemplateData');
				Route::post('labGetPay', [App\Http\Controllers\lab\visit\VisitController::class, 'GetPay'])->name('lab.visit.GetPay');
				Route::post('SMS_EMAIL', [App\Http\Controllers\lab\visit\VisitController::class, 'SMS_EMAIL'])->name('lab.visit.SMS_EMAIL');
				Route::post('import_machine_results', [App\Http\Controllers\lab\visit\VisitController::class, 'import_machine_results'])->name('lab.visit.importData');
				Route::post('request_codes', [App\Http\Controllers\lab\visit\VisitController::class, 'request_codes'])->name('lab.visit.request_codes');
				Route::post('saveGuarantorOrder', [App\Http\Controllers\lab\visit\VisitController::class, 'saveGuarantorOrder'])->name('lab.visit.saveGuarantorOrder');
				Route::post('acceptRequest', [App\Http\Controllers\lab\visit\VisitController::class, 'acceptRequest'])->name('lab.visit.acceptRequest');
				Route::post('rejectRequest', [App\Http\Controllers\lab\visit\VisitController::class, 'rejectRequest'])->name('lab.visit.rejectRequest');
				Route::post('chkReceiveFile', [App\Http\Controllers\lab\visit\VisitController::class, 'chkReceiveFile'])->name('lab.visit.chkReceiveFile');
                Route::get('lab/requests/in_process', [App\Http\Controllers\lab\visit\VisitController::class, 'waiting_list'])->name('lab.visit.waiting_list');
				Route::post('newLabValidation', [App\Http\Controllers\lab\visit\VisitController::class, 'newValidation'])->name('lab.visit.newValidation');

				//patients billing route
                Route::get('lab/billing', [App\Http\Controllers\lab\billing\BillingController::class, 'index'])->name('lab.billing.index');
				Route::get('fillPatientBilllab', [App\Http\Controllers\lab\billing\BillingController::class, 'fillPatientBilllab'])->name('fillPatientBilllab');
				Route::get('fillPatientDataBilllab', [App\Http\Controllers\lab\billing\BillingController::class, 'fillPatientDataBilllab'])->name('fillPatientDataBilllab');
				Route::post('NewBillPatient', [App\Http\Controllers\lab\billing\BillingController::class, 'NewBillPatient'])->name('NewBillPatient');
				Route::get('fillPrice', [App\Http\Controllers\lab\billing\BillingController::class, 'fillPrice'])->name('fillPrice');
				Route::get('addTaping', [App\Http\Controllers\lab\billing\BillingController::class, 'addTaping'])->name('addTaping');
				Route::get('fillTax', [App\Http\Controllers\lab\billing\BillingController::class, 'fillTax'])->name('fillTax');
				Route::post('SaveBill', [App\Http\Controllers\lab\billing\BillingController::class, 'SaveBill'])->name('SaveBill');
				Route::post('SavePay', [App\Http\Controllers\lab\billing\BillingController::class, 'SavePay'])->name('SavePay');
				Route::post('SaveRefund', [App\Http\Controllers\lab\billing\BillingController::class, 'SaveRefund'])->name('SaveRefund');
				Route::post('SaveDiscount', [App\Http\Controllers\lab\billing\BillingController::class, 'SaveDiscount'])->name('SaveDiscount');
				Route::post('GetSumPayRef', [App\Http\Controllers\lab\billing\BillingController::class, 'GetSumPayRef'])->name('GetSumPayRef');
				Route::post('downloadPDFBilling', [App\Http\Controllers\lab\billing\BillingController::class, 'downloadPDFBilling'])->name('downloadPDFBilling');
				Route::post('generatePDFBilling', [App\Http\Controllers\lab\billing\BillingController::class, 'generatePDFBilling'])->name('generatePDFBilling');
				Route::post('deleteBilllab', [App\Http\Controllers\lab\billing\BillingController::class, 'deleteBilllab'])->name('deleteBilllab');
				Route::get('lab/billing/{id}/edit', [App\Http\Controllers\lab\billing\BillingController::class, 'edit'])->name('lab.billing.edit');
				Route::get('fillTaxDel', [App\Http\Controllers\lab\billing\BillingController::class, 'fillTaxDel'])->name('fillTaxDel');
				Route::get('lab/billing/{id}/fillTax', [App\Http\Controllers\lab\billing\BillingController::class, 'fillTax'])->name('fillTax_ID');
				Route::get('lab/billing/{id}/fillTaxDel', [App\Http\Controllers\lab\billing\BillingController::class, 'fillTaxDel'])->name('fillTaxDel_ID');
				Route::get('lab/billing/{id}/addTaping', [App\Http\Controllers\lab\billing\BillingController::class, 'addTaping'])->name('addTaping_ID');
				Route::get('GetRef', [App\Http\Controllers\lab\billing\BillingController::class, 'GetRef'])->name('GetRef');
				Route::post('GetPay', [App\Http\Controllers\lab\billing\BillingController::class, 'GetPay'])->name('GetPay');
				Route::get('fillCurrency', [App\Http\Controllers\lab\billing\BillingController::class, 'fillCurrency'])->name('fillCurrency');
				Route::post('BillsendPatient', [App\Http\Controllers\lab\billing\BillingController::class, 'send_patient'])->name('lab.billing.send_patient');

				//patients referred labs route
                Route::get('lab/referredlabs', [App\Http\Controllers\lab\referredlabs\ReferredLabsController::class, 'index'])->name('lab.referredlabs.index');
				Route::post('PaidTest', [App\Http\Controllers\lab\referredlabs\ReferredLabsController::class, 'PaidTest'])->name('PaidTest');
				Route::get('refgen_sumprice', [App\Http\Controllers\lab\referredlabs\ReferredLabsController::class, 'refgen_sumprice'])->name('refgen_sumprice');

				//external guarantors route
			    Route::get('external/guarantors', [App\Http\Controllers\external_sections\ExtLabsController::class,'index'])->name('external_labs.index');
                Route::post('get_external_guarantors', [App\Http\Controllers\external_sections\ExtLabsController::class,'get'])->name('external_labs.get');
                Route::post('store_external_guarantors', [App\Http\Controllers\external_sections\ExtLabsController::class,'store'])->name('external_labs.store');
                Route::delete('delete_external_guarantors', [App\Http\Controllers\external_sections\ExtLabsController::class,'delete'])->name('external_labs.delete');
                Route::post('guarantors_getUserInfo', [App\Http\Controllers\external_sections\ExtLabsController::class,'getUserInfo'])->name('external_labs.getUserInfo');
                Route::post('guarantors_saveUserInfo', [App\Http\Controllers\external_sections\ExtLabsController::class,'saveUserInfo'])->name('external_labs.saveUserInfo');

				//external insurance route
			    Route::get('external/referred_labs', [App\Http\Controllers\external_sections\ExtInsuranceController::class,'index'])->name('external_insurance.index');
                Route::post('get_referred_lab', [App\Http\Controllers\external_sections\ExtInsuranceController::class,'get'])->name('external_insurance.get');
                Route::post('store_referred_lab', [App\Http\Controllers\external_sections\ExtInsuranceController::class,'store'])->name('external_insurance.store');
                Route::delete('delete_referred_lab', [App\Http\Controllers\external_sections\ExtInsuranceController::class,'delete'])->name('external_insurance.delete');


				//external labs,insurance prices route
			    Route::get('prices/all', [App\Http\Controllers\PricesController::class,'index'])->name('prices.index');
				Route::get('prices/guarantor/create/{id?}', [App\Http\Controllers\PricesController::class,'extlab_create'])->name('extlab_prices.create');
				Route::post('guarantor_prices_store', [App\Http\Controllers\PricesController::class,'extlab_store'])->name('extlab_prices.store');
				Route::get('prices/guarantor/edit/{id}', [App\Http\Controllers\PricesController::class,'extlab_edit'])->name('extlab_prices.edit');
				Route::post('guarantor_prices_update', [App\Http\Controllers\PricesController::class,'extlab_update'])->name('extlab_prices.update');
				Route::post('guarantor_prices_copy', [App\Http\Controllers\PricesController::class,'extlab_copy'])->name('extlab_prices.copy');
                Route::post('guarantor_prices_get', [App\Http\Controllers\PricesController::class,'extlab_getPrices'])->name('extlab_prices.get');
				Route::post('guarantor_prices_getCats', [App\Http\Controllers\PricesController::class,'extlab_getCats'])->name('extlab_prices.getCats');
				Route::get('prices/referred_lab/create/{id?}', [App\Http\Controllers\PricesController::class,'ins_create'])->name('ins_prices.create');
				Route::post('referred_lab_prices_store', [App\Http\Controllers\PricesController::class,'ins_store'])->name('ins_prices.store');
				Route::get('prices/referred_lab/edit/{id}', [App\Http\Controllers\PricesController::class,'ins_edit'])->name('ins_prices.edit');
				Route::post('referred_lab_prices_update', [App\Http\Controllers\PricesController::class,'ins_update'])->name('ins_prices.update');
				Route::post('referred_lab_prices_copy', [App\Http\Controllers\PricesController::class,'ins_copy'])->name('ins_prices.copy');
				Route::post('referred_lab_prices_get', [App\Http\Controllers\PricesController::class,'extins_getPrices'])->name('ins_prices.get');
                Route::post('referred_lab_add_codes', [App\Http\Controllers\PricesController::class,'ins_addcodes'])->name('ins_prices.addcodes');
				Route::get('prices/lab/create/{id}', [App\Http\Controllers\PricesController::class,'lab_create'])->name('lab_prices.create');
				Route::post('lab_prices_store', [App\Http\Controllers\PricesController::class,'lab_store'])->name('lab_prices.store');
				Route::get('prices/lab/edit/{id}', [App\Http\Controllers\PricesController::class,'lab_edit'])->name('lab_prices.edit');
				Route::post('lab_prices_update', [App\Http\Controllers\PricesController::class,'lab_update'])->name('lab_prices.update');
				Route::get('prices/doctor/create/{id?}', [App\Http\Controllers\PricesController::class,'doctor_create'])->name('doctor_prices.create');
				Route::post('doctor_prices_store', [App\Http\Controllers\PricesController::class,'doctor_store'])->name('doctor_prices.store');
				Route::get('prices/doctor/edit/{id}', [App\Http\Controllers\PricesController::class,'doctor_edit'])->name('doctor_prices.edit');
				Route::post('doctor_prices_update', [App\Http\Controllers\PricesController::class,'doctor_update'])->name('doctor_prices.update');
				Route::post('doctor_prices_copy', [App\Http\Controllers\PricesController::class,'doctor_copy'])->name('doctor_prices.copy');
				Route::post('doctor_prices_get', [App\Http\Controllers\PricesController::class,'doctor_getPrices'])->name('doctor_prices.get');
				Route::post('extlab_prices_export', [App\Http\Controllers\PricesController::class,'extlab_export'])->name('extlab_prices.export');

				//branches directory route
				Route::resource('branches', App\Http\Controllers\resources\BranchesController::class);
				Route::post('get_branches', [App\Http\Controllers\resources\BranchesController::class, 'get_branches'])->name('get_branches');

               //doctor directory routes
				Route::resource('resources', App\Http\Controllers\resources\DoctorsController::class);
				Route::get('getProfessions', [App\Http\Controllers\resources\DoctorsController::class, 'getProfessions'])->name('getProfessions');
                Route::post('inventory_modal', [App\Http\Controllers\resources\DoctorsController::class, 'getDocBranches'])->name('inventory.modal');

				 //inventory route
                Route::get('inventory/items', [App\Http\Controllers\inventory\items\ItemsController::class, 'index'])->name('inventory.items.index');
				Route::get('inventory/items/{id}/edit', [App\Http\Controllers\inventory\items\ItemsController::class, 'edit'])->name('inventory.items.edit');
   				Route::post('inventory/items/delete', [App\Http\Controllers\inventory\items\ItemsController::class, 'destroy'])->name('inventory.items.destroy');
   				Route::get('inventory/items/{type}/{id}/lunette/', [App\Http\Controllers\inventory\items\ItemsController::class, 'lunette'])->name('inventory.items.lunette');
   				Route::get('inventory/items/{type}/{id}/produits/', [App\Http\Controllers\inventory\items\ItemsController::class, 'produits'])->name('inventory.items.produits');
				Route::post('inventory/items/lenses', [App\Http\Controllers\inventory\items\ItemsController::class, 'lenses'])->name('inventory.items.lenses');
				Route::post('store_lunette', [App\Http\Controllers\inventory\items\ItemsController::class, 'store_lunette'])->name('store_lunette');
				Route::get('generation_code', [App\Http\Controllers\inventory\items\ItemsController::class, 'generation_code'])->name('generation_code');
				Route::get('generation_price', [App\Http\Controllers\inventory\items\ItemsController::class, 'generation_price'])->name('generation_price');
				Route::get('specstype', [App\Http\Controllers\inventory\items\ItemsController::class, 'specstype'])->name('specstype');
				Route::get('collection_code', [App\Http\Controllers\inventory\items\ItemsController::class, 'collection_code'])->name('collection_code');
				Route::post('generate_pdf_item', [App\Http\Controllers\inventory\items\ItemsController::class, 'generate_pdf'])->name('inventory.items.generate_pdf');
				Route::post('item_label_pdf', [App\Http\Controllers\inventory\items\ItemsController::class, 'generate_item_label'])->name('inventory.items.item_label_pdf');
				Route::post('item_qty_history', [App\Http\Controllers\inventory\items\ItemsController::class, 'item_qty_history'])->name('inventory.items.item_qty_history');
				Route::post('SaveAdjacement', [App\Http\Controllers\inventory\items\ItemsController::class, 'SaveAdjacement'])->name('SaveAdjacement');
				Route::get('generation_sumqtyprice', [App\Http\Controllers\inventory\items\ItemsController::class, 'generation_sumqtyprice'])->name('generation_sumqtyprice');
				Route::get('loadItems', [App\Http\Controllers\inventory\items\ItemsController::class, 'loadItems'])->name('inventory.items.loadItems');
				Route::get('loadOtherItems', [App\Http\Controllers\inventory\items\ItemsController::class, 'loadOtherItems'])->name('inventory.items.loadOtherItems');

				//suppliers route
                Route::get('inventory/suppliers', [App\Http\Controllers\inventory\suppliers\SuppliersController::class, 'index'])->name('inventory.suppliers.index');
				Route::get('inventory/suppliers/{id}/edit', [App\Http\Controllers\inventory\suppliers\SuppliersController::class, 'edit'])->name('inventory.suppliers.edit');
   				Route::post('inventory/suppliers/delete', [App\Http\Controllers\inventory\suppliers\SuppliersController::class, 'destroy'])->name('inventory.suppliers.destroy');
   				Route::get('inventory/suppliers/{type}/{id}/suppliers/', [App\Http\Controllers\inventory\suppliers\SuppliersController::class, 'suppliers'])->name('inventory.suppliers.suppliers');
				Route::post('store_suppliers', [App\Http\Controllers\inventory\suppliers\SuppliersController::class, 'store_suppliers'])->name('store_suppliers');
				//clients route
                Route::get('inventory/clients', [App\Http\Controllers\inventory\Clients\ClientsController::class, 'index'])->name('inventory.clients.index');
				Route::get('inventory/clients/{id}/edit', [App\Http\Controllers\inventory\Clients\ClientsController::class, 'edit'])->name('inventory.clients.edit');
   				Route::post('inventory/clients/delete', [App\Http\Controllers\inventory\Clients\ClientsController::class, 'destroy'])->name('inventory.clients.destroy');
   				Route::get('inventory/clients/{type}/{id}/clients/', [App\Http\Controllers\inventory\Clients\ClientsController::class, 'clients'])->name('inventory.clients.clients');
				Route::post('store_clients', [App\Http\Controllers\inventory\Clients\ClientsController::class, 'store_clients'])->name('store_clients');
							//collection route
                Route::get('inventory/collection', [App\Http\Controllers\inventory\collection\CollectionController::class, 'index'])->name('inventory.collection.index');
				Route::post('SaveCollection', [App\Http\Controllers\inventory\collection\CollectionController::class, 'SaveCollection'])->name('SaveCollection');
				Route::get('inventory/collection/{id}/edit', [App\Http\Controllers\inventory\collection\CollectionController::class, 'edit'])->name('inventory.collection.edit');
   				Route::post('inventory/collection/delete', [App\Http\Controllers\inventory\collection\CollectionController::class, 'destroy'])->name('inventory.collection.destroy');
				//AssetMainCategory route
                Route::get('inventory/assetmaincategory', [App\Http\Controllers\inventory\assetmaincategory\AssetMainCategoryController::class, 'index'])->name('inventory.assetmaincategory.index');
				Route::get('inventory/assetmaincategory/{id}/edit', [App\Http\Controllers\inventory\assetmaincategory\AssetMainCategoryController::class, 'edit'])->name('inventory.assetmaincategory.edit');
   				Route::post('inventory/assetmaincategory/delete', [App\Http\Controllers\inventory\assetmaincategory\AssetMainCategoryController::class, 'destroy'])->name('inventory.assetmaincategory.destroy');
   				Route::get('inventory/assetmaincategory/{type}/{id}/assetmaincategory/', [App\Http\Controllers\inventory\assetmaincategory\AssetMainCategoryController::class, 'assetmaincategory'])->name('inventory.assetmaincategory.assetmaincategory');
				Route::post('store_assetmaincategory', [App\Http\Controllers\inventory\assetmaincategory\AssetMainCategoryController::class, 'store_assetmaincategory'])->name('store_assetmaincategory');
				//category route
                Route::get('inventory/category', [App\Http\Controllers\inventory\category\CategoryController::class, 'index'])->name('inventory.category.index');
				Route::post('SaveCategory', [App\Http\Controllers\inventory\category\CategoryController::class, 'SaveCategory'])->name('SaveCategory');
				Route::get('inventory/category/{id}/edit', [App\Http\Controllers\inventory\category\CategoryController::class, 'edit'])->name('inventory.category.edit');
   				Route::post('inventory/category/delete', [App\Http\Controllers\inventory\category\CategoryController::class, 'destroy'])->name('inventory.category.destroy');
				//location route
                Route::get('inventory/location', [App\Http\Controllers\inventory\location\LocationController::class, 'index'])->name('inventory.location.index');
				Route::post('SaveLocation', [App\Http\Controllers\inventory\location\LocationController::class, 'SaveLocation'])->name('SaveLocation');
				Route::get('inventory/location/{id}/edit', [App\Http\Controllers\inventory\location\LocationController::class, 'edit'])->name('inventory.location.edit');
   				Route::post('inventory/location/delete', [App\Http\Controllers\inventory\location\LocationController::class, 'destroy'])->name('inventory.location.destroy');
				//Invoices route
                Route::get('inventory/invoices', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'index'])->name('inventory.invoices.index');
				Route::get('inventory/invoices/{id}/NewInvoices/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewInvoices'])->name('NewInvoices');
				Route::get('inventory/invoices/{id}/fillPriceInv', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillPriceInv'])->name('fillPriceInv');
				Route::get('fillInvoiceType', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillInvoiceType'])->name('fillInvoiceType');
				Route::get('inventory/invoices/{id}/addInvoiceRowCommand', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'addInvoiceRowCommand'])->name('addInvoiceRowCommand');
				Route::get('addInvoiceRow', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'addInvoiceRow'])->name('addInvoiceRow');
				Route::get('fillInvoiceDel', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillInvoiceDel'])->name('fillInvoiceDel');
				Route::get('inventory/invoices/{id}/fillInvoiceDel', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillInvoiceDel'])->name('fillInvoiceDel_ID');
				Route::get('inventory/invoices/{id}/addInvoiceRow', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'addInvoiceRow'])->name('addInvoiceRow_ID');
				Route::get('Refresh_code', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'Refresh_code'])->name('Refresh_code');
				Route::post('inventory/invoices/{id}/SaveInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SaveInventory'])->name('SaveInventory');
				Route::post('inventory/invoices/{id}/SaveCommand', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SaveCommand'])->name('SaveCommand');
				Route::post('DeleteInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'DeleteInventory'])->name('DeleteInventory');
				Route::get('inventory/invoices/{id}/edit', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'edit'])->name('inventory.invoices.edit');
				Route::post('downloadPDFInvoice', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'downloadPDFInvoice'])->name('downloadPDFInvoice');
				Route::post('generatePDFInvoice', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'generatePDFInvoice'])->name('generatePDFInvoice');
				Route::get('fillPriceInv', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillPriceInv'])->name('EfillPriceInv');
				Route::post('SaveInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SaveInventory'])->name('ESaveInventory');
				Route::get('GetRefInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetRefInventory'])->name('GetRefInventory');
				Route::get('GetPayInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetPayInventory'])->name('GetPayInventory');
				Route::post('SavePayInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SavePayInventory'])->name('SavePayInventory');
				Route::post('SaveRefundInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SaveRefundInventory'])->name('SaveRefundInventory');
				Route::get('GetRemiseInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetRemiseInventory'])->name('GetRemiseInventory');
				Route::post('SaveRemiseInventory', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SaveRemiseInventory'])->name('SaveRemiseInventory');
				Route::post('invoice_pdf_labels', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'invoice_pdf_labels'])->name('inventory.download_pdf_label');
				Route::get('inventory/invoices/{id}/NewSales/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewSales'])->name('NewSales');
				Route::get('inventory/invoices/{id}/editsales', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'editsales'])->name('inventory.invoices.editsales');
				Route::get('inventory/invoices/{id}/NewRSales/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewRSales'])->name('NewRSales');
				Route::get('inventory/invoices/{id}/EditRSales', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'EditRSales'])->name('inventory.invoices.EditRSales');
				Route::get('inventory/invoices/{id}/NewRInvoices/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewRInvoices'])->name('NewRInvoices');
				Route::get('inventory/invoices/{id}/EditRinvoices', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'EditRinvoices'])->name('inventory.invoices.EditRinvoices');
				Route::get('GetInvoiceSalesNb', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetInvoiceSalesNb'])->name('GetInvoiceSalesNb');
				Route::get('GetInvoiceSalesDetails', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetInvoiceSalesDetails'])->name('GetInvoiceSalesDetails');
				Route::get('GetInvoicesNb', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetInvoicesNb'])->name('GetInvoicesNb');
				Route::get('GetInvoicesDetails', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'GetInvoicesDetails'])->name('GetInvoicesDetails');
                Route::post('inv_pat_list', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'inv_pat_list'])->name('inventory.invoices.inv_pat_list');
				Route::post('inv_send_email_pat', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'send_email_pat'])->name('inventory.invoices.send_email_pat');
				Route::get('fillBarcode', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'fillBarcode'])->name('fillBarcode');
				Route::post('save_cmd', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'save_cmd'])->name('inventory.invoices.save_cmd');
				Route::get('inventory/invoices/{id}/editadj', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'editadj'])->name('inventory.invoices.editadj');
				Route::get('inventory/invoices/{id}/NewCommand/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewCommand'])->name('NewCommand');
				Route::get('inventory/invoices/{id}/editcommand', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'editcommand'])->name('inventory.invoices.editcommand');
				Route::post('sendPDFInvoice', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'sendPDFInvoice'])->name('sendPDFInvoice');
				Route::post('sendgeneratePDFInvoice', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'sendgeneratePDFInvoice'])->name('sendgeneratePDFInvoice');
				Route::post('send_supplier_email', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'send_supplier_email'])->name('send_supplier_email');
				Route::post('generate_cmd_pdf', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'generate_cmd_pdf'])->name('generate_cmd_pdf');
				Route::post('CommandValidate1', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CommandValidate1'])->name('CommandValidate1');
				Route::post('CommandValidate2', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CommandValidate2'])->name('CommandValidate2');
				Route::get('loadOtherItemsCMD', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'loadOtherItemsCMD'])->name('loadOtherItemsCMD');
				Route::post('EmailSentValidate', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'EmailSentValidate'])->name('EmailSentValidate');
				Route::post('CommandDone', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CommandDone'])->name('CommandDone');
				Route::post('CommandPaid', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CommandPaid'])->name('CommandPaid');
				Route::post('cuploadAttach', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'cuploadAttach'])->name('invoice.cmd.uploadAttach');
				Route::delete('cdestroyAttach', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'cdestroyAttach'])->name('invoice.cmd.destroyAttach');
				Route::post('download', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'download'])->name('file.download');
				Route::post('CmdFree', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CmdFree'])->name('CmdFree');
				Route::post('SavePayCommand', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SavePayCommand'])->name('SavePayCommand');
				Route::post('CommandQuote', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'CommandQuote'])->name('CommandQuote');
				Route::get('inventory/invoices/{id}/NewPrices/', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'NewPrices'])->name('NewPrices');
				Route::get('inventory/invoices/{id}/editprices', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'editprices'])->name('inventory.invoices.editprices');
				Route::post('inventory/invoices/{id}/SavePrices', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SavePrices'])->name('SavePrices');
				Route::post('SavePrices', [App\Http\Controllers\inventory\invoices\InvoicesController::class, 'SavePrices'])->name('ESavePrices');

					//accounting
				Route::get('accounting/NewAccounting', [App\Http\Controllers\accounting\AccountingController::class, 'NewAccounting'])->name('NewAccounting');
				Route::get('accounting/{id}/EditAccounting/', [App\Http\Controllers\accounting\AccountingController::class, 'EditAccounting'])->name('EditAccounting');
				Route::post('accounting_SaveAccount', [App\Http\Controllers\accounting\AccountingController::class, 'SaveAccount'])->name('SaveAccount');
				Route::post('DeleteAccounting', [App\Http\Controllers\accounting\AccountingController::class, 'DeleteAccounting'])->name('DeleteAccounting');
				Route::post('DeleteAccounting', [App\Http\Controllers\accounting\AccountingController::class, 'DeleteAccounting'])->name('DeleteAccounting');
				Route::post('accounting_to_account', [App\Http\Controllers\accounting\AccountingController::class, 'to_account'])->name('accounting.to_account');

                //inventory reports routes
				Route::get('inventory/reports', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'index'])->name('inventory.reports.index');
                Route::get('inventory/report/sales_per_pay', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_pay_refund'])->name('inventory.reports.inventory_sales_per_pay');
				Route::get('inventory/report/all_sales', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_sales'])->name('inventory.reports.inventory_sales');
                Route::get('inventory/report/all_purchases', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_purchases'])->name('inventory.reports.inventory_purchases');
			    Route::get('inventory/report/sales_per_item', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_sales_per_item'])->name('inventory.reports.inventory_sales_per_item');
			    Route::get('inventory/report/sales_per_supplier', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_sales_per_supplier'])->name('inventory.reports.inventory_sales_per_supplier');
                Route::get('inventory/report/purchases_per_item', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_purchases_per_item'])->name('inventory.reports.inventory_purchases_per_item');
			    Route::get('inventory/report/purchases_per_supplier', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_purchases_per_supplier'])->name('inventory.reports.inventory_purchases_per_supplier');
                Route::get('inventory/report/purchases_per_pay', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_purchases_per_pay'])->name('inventory.reports.inventory_purchases_per_pay');
                Route::get('inventory/report/inventory_items_per_qty', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_items_per_qty'])->name('inventory.reports.inventory_items_per_qty');
				Route::get('Filter_cat', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'Filter_cat'])->name('Filter_cat');
				Route::get('Filter_item', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'Filter_item'])->name('Filter_item');
                Route::get('inventory/report/inventory_accounting', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_accounting'])->name('inventory.reports.inventory_accounting');
                Route::get('inventory/report/inventory_orders', [App\Http\Controllers\inventory\reports\InventoryReportsController::class, 'inventory_orders'])->name('inventory.reports.inventory_orders');

                //inventory formulas routes
				Route::get('inventory/formulas', [App\Http\Controllers\inventory\formulas\FormulasController::class, 'index'])->name('inventory.formulas.index');
				Route::get('inventory/formulas/NewFormula/', [App\Http\Controllers\inventory\formulas\FormulasController::class, 'NewFormula'])->name('NewFormula');
				Route::post('SaveFormula', [App\Http\Controllers\inventory\formulas\FormulasController::class, 'SaveFormula'])->name('SaveFormula');
				Route::get('inventory/formulas/{id}/editformula', [App\Http\Controllers\inventory\formulas\FormulasController::class, 'editformula'])->name('inventory.formulas.editformula');
   				Route::post('inventory/formulas/delete', [App\Http\Controllers\inventory\formulas\FormulasController::class, 'destroy'])->name('inventory.formulas.destroy');

			   //inventory materials routes
				Route::get('inventory/materials', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'index'])->name('inventory.materials.index');
                Route::get('inventory/materials/NewMaterials/', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'NewMaterials'])->name('NewMaterials');
				Route::get('inventory/materials/edit/{id}/{edit?}', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'editmaterials'])->name('inventory.materials.editmaterials');
   				Route::post('inventory/materials/delete', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'destroy'])->name('inventory.materials.destroy');
				Route::get('GetItemsDetails', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'GetItemsDetails'])->name('GetItemsDetails');
				Route::get('fillStockNb', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'fillStockNb'])->name('fillStockNb');
				Route::get('updateQtyRow', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'updateQtyRow'])->name('updateQtyRow');
				Route::get('updateQtyByPlusMinus', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'updateQtyByPlusMinus'])->name('updateQtyByPlusMinus');
				Route::post('ApproveInventory', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'ApproveInventory'])->name('ApproveInventory');
				Route::get('getDatatableMaterials', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'getDatatableMaterials'])->name('getDatatableMaterials');
				Route::post('DeleteMaterials', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'DeleteMaterials'])->name('DeleteMaterials');
				Route::post('InventoryAuditPDF', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'AuditPDF'])->name('AuditPDF');
                Route::get('inventory/materials/NewMaterialsAdj/', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'NewMaterialsAdj'])->name('NewMaterialsAdj');
				Route::get('GetItemsDetailsAdj', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'GetItemsDetailsAdj'])->name('GetItemsDetailsAdj');
				Route::post('ApproveInventoryAdj', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'ApproveInventoryAdj'])->name('ApproveInventoryAdj');
				Route::get('loadMaterials', [App\Http\Controllers\inventory\materials\MaterialsController::class, 'loadMaterials'])->name('inventory.materials.loadMaterials');
                //phlotobomy routes
				Route::get('phlebotomy/all', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'index'])->name('phlebotomy.index');
				Route::post('save_phlebotomy', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'save_phlebotomy'])->name('phlebotomy.save');
				Route::post('phlebotomy_codes', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'phlebotomy_codes'])->name('phlebotomy.codes');
				Route::post('phlebotomy_label', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'phlebotomy_label'])->name('phlebotomy.label');
				Route::post('phlebotomy_serial', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'phlebotomy_serial'])->name('phlebotomy.serial');
				Route::get('loadTests', [App\Http\Controllers\phlotobomy\PhlotobomyController::class, 'loadTests'])->name('loadTests');




});





