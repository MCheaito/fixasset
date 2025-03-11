<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\ExtLab;
use App\Models\ExtIns;
use DB;

class DashboardController extends Controller
{
    
	public function __construct()
    {
        $this->middleware('auth');
    }
	
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($lang)
    {
       $user_type = auth()->user()->type;
	    $user_num = auth()->user()->id;
	   switch($user_type){
		   //external doctor account
		   /*case 1:
		   $doc_info = Doctor::where('doctor_user_num',$user_num)->first();
		   $patient_nb = Patient::where('status','O')->where('ext_lab',$user_num)->get()->count();
		   $orders_nb = DB::table('tbl_visits_orders')->where('active','Y')->whereIn('status',['P','V'])->where('ext_lab',$user_num)->get()->count();
		   return view('dashboard.index')->with(['doc_info'=>$doc_info,'patient_nb'=>$patient_nb,
		                                        'orders_nb'=>$orders_nb]);
		   break;*/
		   //external lab account
		   case 3:
		   $lab_info = ExtLab::where('lab_user_num',$user_num)->first();
		   $patient_nb = Patient::where('status','O')->where('ext_lab',$lab_info->id)->get()->count();
		   $orders_nb = DB::table('tbl_visits_orders')->where('active','Y')->whereIn('status',['P','F','V'])->where('ext_lab',$lab_info->id)->get()->count();
		   return view('dashboard.index')->with(['lab_info'=>$lab_info,'patient_nb'=>$patient_nb,'orders_nb'=>$orders_nb]);
		   break;
		   case 2:
		   $clinic_num = auth()->user()->clinic_num;
		   $lab_info = DB::table('tbl_clinics')->where('id',$clinic_num)->first();
		   $patient_nb = DB::table('tbl_patients')->where('status','O')->where('clinic_num',$clinic_num)->get()->count();
		   $doc_nb = Doctor::where('active','O')->get()->count();
		   $extlabs_nb = ExtLab::where('clinic_num',$clinic_num)->where('status','A')->get()->count();			 
		   $extins_nb = ExtIns::where('clinic_num',$clinic_num)->where('status','Y')->get()->count();			 
		   $orders_nb = DB::table('tbl_visits_orders')->where('active','Y')->where('clinic_num',$clinic_num)->get()->count();
		   return view('dashboard.index')->with(['lab_info'=>$lab_info,'patient_nb'=>$patient_nb,
		                                         'doc_nb'=>$doc_nb,'extlabs_nb'=>$extlabs_nb,'extins_nb'=>$extins_nb,
												 'orders_nb'=>$orders_nb]);

		   break;
	   }
	   
	     
	   
	   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
