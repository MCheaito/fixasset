<?php
/*
* DEV APP
* Created date : 1-9-2022
*  
* update functions from time to time till today
*
*/

namespace App\Http\Controllers\lab\visit;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

use App\Models\DOCSResults;
use App\Models\TblVisits;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\DoctorSignature;

use App\Models\ExtBranch;

use Illuminate\Support\Facades\Mail;
use App\Mail\SettingMailAttach;
use RingCentral\SDK\SDK;

use File;
use Image;
use DB;
use Alert;
use PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class DocumentsController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**

     * Listing Of images gallery

     *

     * @return \Illuminate\Http\Response

     */

    public function index($lang,$id)

    {
       
	   $visit=TblVisits::find($id);
	   $patient=Patient::find($visit->patient_num);
	   $id_clinic = $visit->clinic_num;
	   $documents = DOCSResults::where('visit_num',$id)->where('active','O')->get();
	  return view('emr.visit.documents.index',compact('documents'))->with(['visit_id'=>$id,'patient'=>$patient]);
		 

    }


    /**

     * Upload documetns function

     *

     * @return \Illuminate\Http\Response

     */

    public function upload($lang,Request $request)

    {
        
		$this->validate($request, [

				'files' => 'required',
				'files.*'=>'mimes:jpg,jpeg,png,gif,bmp,tiff,svg,webp,pdf|max:3072'
			  ],[
				 'files.required' => __('Please choose a document of type image/pdf'),
				 'files.*.mimes' => __('Please insert a document of type image/pdf'),
				 'files.*.max'   => __('Document should be less than 3 MB')
			
			]);
        $type= $request->type;
		$uid = auth()->user()->id;
		$image_path = public_path('custom/uploads/Documents');
		if (!file_exists($image_path)){
				mkdir($image_path, 0775, true);
			}
		$visit = TblVisits::where('id',$request->visit_id)->first();
	    $clinicID = $visit->clinic_num;
		$patientID = $visit->patient_num;
		
		
		if($request->hasfile('files')) {
			
			  foreach($request->file('files') as $file){
			  
			  $image_name =  date('Ymd').'_'.$uid.'_'.$clinicID.'_'.uniqid().'.'.$file->getClientOriginalExtension();
             
			  //dd($image_name);
			  
			  $file->move($image_path, $image_name);
			  

										
			
			  //$title = $request->full_name;
			  $notes = $request->description;
			  $path = str_replace(config('app.src_url'),config('app.url'),$image_path);
			  
				DOCSResults::create([
					'visit_num' => $request->visit_id,
					'equip_type' => $type,
					'notes' => $notes,
					'name' => $file->getClientOriginalName(),
					'path' => $path.'/'.$image_name,
					'clinic_num' => $visit->clinic_num,
					'patient_num' => $visit->patient_num,
					'user_num' => $uid,
					'active' => 'O'
				]);
			  }
           
			return back()->with('success',__('Documents Uploaded successfully'));
			//return response()->json(['success',__('Document(s) Uploaded successfully')]);
			//return response()->json(['success'=>$image_name]);
			 }
			
		
		
		
    
	}

    /**

     * Remove Document function

     *

     * @return \Illuminate\Http\Response

     */

    public function destroy($lang,$id)

    {

    	DOCSResults::find($id)->update(['active'=>'N','user_num'=>auth()->user()->id]);

    	return back()->with('success','Document removed successfully.');	

    }
	
	
	//get patient documents for old visit
public function get_patient_docs($lang,Request $request){
	$visit_num = $request->visit_num;
	//dd($visit_num);
	$visit = TblVisits::find($visit_num);
	$patient_num = $visit->patient_num;
	$clinic_num = $visit->clinic_num;
	
	$sql="select DISTINCT(p.id) as id,p.visit_num,DATE_FORMAT(visit.visit_date_time,'%Y-%m-%d %H:%i') as visit_date_time,
		          clin.full_name as ClinicName,CONCAT(doc.first_name,' ',doc.last_name) AS ProName,
		          CONCAT(patConsult.first_name,' ',patConsult.last_name) AS patDetail,patConsult.first_phone as Tel
				  from tbl_docs_data as p
				  INNER JOIN tbl_visits as visit on visit.id=p.visit_num and  visit.active='O'
				  INNER JOIN tbl_clinics as clin on p.clinic_num=clin.id and  clin.active='O'
				  LEFT JOIN tbl_doctors as doc on visit.doctor_num=doc.id and doc.active='O'
				  INNER JOIN tbl_patients as patConsult on p.patient_num=patConsult.id and   patConsult.status='O'
				  where p.active='O' and p.visit_num<>".$visit_num." and p.patient_num=".$patient_num." and p.clinic_num=".$clinic_num." 
                  ORDER BY visit_date_time desc		  
				  ";
	$documents = DB::select(DB::raw("$sql"));
	$html_documents= view('emr.visit.documents.patient_docs_list')->with(['documents'=>$documents])->render();
	return response()->json(['html_documents'=>$html_documents]);
	
}	

//import patient documents to current visit
public function import_patient_docs($lang,Request $request){
	$current_visit_num= $request->current_visit_num;
	
	$import_id = $request->id;
	$docs = DOCSResults::where('id',$import_id)->get();
	//dd($docs);
	if($docs->count()>0){
		$visit = TblVisits::find($current_visit_num);
		foreach($docs as $d){
			//Add document data for current visit
		  DOCSResults::create([
		            'visit_num' => $current_visit_num,
					'equip_type' => "DOCS",
					'notes' => isset($d->notes)?$d->notes:NULL,
					'name' => $d->name,
					'path' =>$d->path,
					'clinic_num' => $visit->clinic_num,
					'patient_num' => $visit->patient_num,
					'user_num' => auth()->user()->id,
					'active' => 'O'
		        ]);
		
		}
	}
	$location = route('emr.visit.documents.index',[app()->getLocale(),$current_visit_num]);
	$msg = __('Imported successfully');
	Alert::toast($msg,"success");
	return response()->json(['location'=>$location]);
}

//get external branches list created date : 24-3-2023
public function pat_externalBranches_list($lang,Request $request){
 $visit_num = $request->visit_num;
 $clinic_num = TblVisits::where('id',$visit_num)->pluck('clinic_num')[0];
 $patient_num= TblVisits::where('id',$visit_num)->pluck('patient_num')[0];
 $external_branches = ExtBranch::where('clinic_num',$clinic_num)->where('status','A')->get();
 $patient=Patient::select('id','first_name','last_name','fax','email','receive_mail')->where('id',$patient_num)->first();
 $html_ext_branch= view('external_branches.send_by.ext_branches_list')->with(['external_branches'=>$external_branches])->render();
 $html_patient= view('patients_list.send_by.patient_data')->with(['patient'=>$patient])->render();

 return response()->json(['html_external_branches'=>$html_ext_branch,'html_patient'=>$html_patient]);	
}

//send treatment plan by email , fax created date : 25-3-2023
public function send_email_fax($lang,Request $request){
  switch($request->type){
 case 'patient': 
	   $id = $request->patient_num;
	   $patient = Patient::find($id);
	   $email = $patient->email;
	   $fax = $patient->fax;
	   $name = $patient->first_name.' '.$patient->last_name;
 break;
 case 'ext_branch':
	 //external branch id
	 $id = $request->ext_branch_id;
	 //get external branch details
	 $external_branch = ExtBranch::find($id);
	 $email = $external_branch->email;
	 $fax = $external_branch->fax;
	 $name = $external_branch->full_name;
   break;
 }
 //visit num
 $visit_num  = $request->visit_num;
 
 //generate pdf to send by email or fax or both
 $pdf = $this->getPDF($visit_num);
  //is checked email
 $is_checked_email = $request->is_checked_email; 
 $resp_email='';
 if($is_checked_email=="true"){
	
	
	 $visit = TblVisits::select(DB::raw("DATE(tbl_visits.visit_date_time) as visit_date"),
	                            DB::raw("CONCAT(pat.first_name,' ',pat.last_name) as pat_name"),
	                     'clin.full_name as branch_name','clin.telephone as branch_tel','clin.fax as branch_fax',
						 'clin.email as branch_email','clin.full_address as branch_address')
						->join('tbl_patients as pat','pat.id','tbl_visits.patient_num')
	                    ->join('tbl_clinics as clin','clin.id','tbl_visits.clinic_num')
						->where('tbl_visits.id',$visit_num)->first();
	
	 $logo=public_path("storage/images/logo-120-120.jpg");
	 $logo = str_replace(config('app.src_url'),config("app.url"),$logo);
	 $title=__('Hello').' '.$name.' , ';
	 if($request->type=='ext_branch'){
	 $msg1=__('You will find attached the documents report for the patient').' : '.$visit->pat_name.' . ';
	 }
	 if($request->type=='patient'){
	 $msg1=__('You will find attached your documents report').' . ';
	 }
	 $msg2=__('This report is sent from branch').' : '.$visit->branch_name.' . ';
	 
	 $from = $visit->branch_name;
	 $reply_to_name = __("No reply").','.$visit->branch_name;
	 $reply_to_address = isset($visit->branch_email)? $visit->branch_email:'noreply@email.com';
	 	 $subject = __('Patient documents report');

	 $details = ['title'=>$title,'msg1'=>$msg1,'msg2'=>$msg2,
	             'branch_name'=>$visit->branch_name,'branch_address'=>$visit->branch_address,'branch_tel'=>$visit->branch_tel,
				 'branch_fax'=>$visit->branch_fax,'branch_email'=>$visit->branch_email,
				'from'=>$from,'reply_to_name'=>$reply_to_name,'reply_to_address'=>$reply_to_address,'subject'=>$subject];
	 $to  = $email;
	 $pdf_name = 'Document-'.$visit->visit_date.'.pdf';
	 Mail::to($to)->send(new SettingMailAttach($details,$pdf,$pdf_name));
	
    if (Mail::failures()) {
				$resp_email.= __("Email: fail");
			}else{
				$resp_email.= __("Email : success");
			}	 
	}
 
  //is checked fax
  $is_checked_fax = $request->is_checked_fax;
  //send by fax procedure
  $resp_fax='';
    if($is_checked_fax=="true"){
	//dd("hello fax");
	//send through ring central
	
	$fax = str_replace("-","",$fax);
    $fax = str_replace("(","",$fax);
    $fax = str_replace(")","",$fax);
    $faxNo = str_replace(" ","",$fax);
    if(strlen($fax)==10){
      $fax = "+1".$fax;
      }
	  //dd($fax);
	$path = public_path('/custom/fax/docs/tmp/');  
	if (!file_exists($path)) {
                    mkdir($path, 0775, true);
            }
	
	$name = $visit_id.'_'.uniqid().'.pdf';
	$pdf_file=$path.$name;
	file_put_contents($pdf_file,$pdf);	
			
	$client_id = config('app.ringcentral_clientid');
    $secrect_code = config('app.ringcentral_clientsecret');
    $server = config('app.ringcentral_server');
    $user_name = config('app.ringcentral_username');
    $pass = config('app.ringcentral_pass');
    $ext = config('app.ringcentral_ext');
	
    require base_path("vendor/autoload.php");
    $rcsdk = new SDK($client_id, $secrect_code, $server);

    $platform = $rcsdk->platform();
    $platform->login($user_name, $ext, $pass);

	$request = $rcsdk->createMultipartBuilder()
	->setBody(array(
	  'to' => array(array('phoneNumber' => $fax)),
	  'faxResolution' => 'High',
	  'coverIndex' =>0
	))
	->add(fopen($pdf_file, 'r'))
	->request('/account/~/extension/~/fax');

       $response = $platform->sendRequest($request);
      //dd($response->json()->id);
	   if(isset($response->json()->id)){
		   $resp_fax.= __("Fax: success");
	   }else{
		   $resp_fax.= __("Fax: fail");

	   }
    //delete all files after sending fax success or fail
        $files = glob($path.'*'); // get all file names
           foreach($files as $file){ // iterate files
                 if(is_file($file)) {
                   unlink($file); // delete file
                 }
                }
  
  
       }
  
	  if($resp_fax=='' && $resp_email!=''){
	  $msg = $resp_email;
      }
  
	  if($resp_email=='' && $resp_fax!=''){
		  $msg = $resp_fax;
	  }
	  
	  
	  if($resp_email!='' && $resp_fax!=''){
		$msg = $resp_email." , ".$resp_fax;
	  }
  
 return response()->json(["success"=>$msg,'modal_name'=>'sendDocsModal']);   
	

}

//generate documents pdf document for email and fax
function getPDF($id)
{
  
  $docs = DOCSResults::where('active','O')->where('visit_num',$id)->where('name','not like','%.pdf')->get();
  $visit = TblVisits::where('id',$id)->where('active','O')->first();
  $clinic=Clinic::where('active','O')->where('id',$visit->clinic_num)->first();
  $doctor=Doctor::where('active','O')->where('id',$visit->doctor_num)->first();
  $patient=Patient::where('status','O')->where('id',$visit->patient_num)->first();
  $signature_path = NULL;
			  //doctor account check if signature is allowed
			  //clinical account check both doctor signature and authorize clinic are allowed 
			 
	          if(isset($doctor)){
					 if( (auth()->user()->type==1 && $doctor->show_sign=='true')
						  || (auth()->user()->type==2 && $doctor->show_sign=='true' && $doctor->show_sign_for_clinic=='true')){
						   $user_signature = DoctorSignature::where('id',$doctor->sign_num)->where('active','O')->first();
						   
						   $mainPath = public_path('/custom/profile_signatures/tmp/');
						   $uid=auth()->user()->id;
													
							 if (!file_exists($mainPath)) {
									mkdir($mainPath, 0775, true);
							  }
							 $image = Image::make(file_get_contents(public_path(substr($user_signature->path,7))))->orientate();
							 
							 //delete old existing files
							 $files = glob($mainPath.'*'); // get all file names
								foreach($files as $file){ // iterate files
								  if(is_file($file)) {
									unlink($file); // delete file
								  }
								}
							 $filename= date('Y').date('m').date('d').'_'.$uid.'_'.uniqid().'.png';
							             
										if(($image->width() <= 200) and ($image->height() <= 200)) {
										}else{
											
												  $image->resize(200, 200, function ($constraint) {
												  $constraint->aspectRatio();

													// if you need to prevent upsizing, you can add:
												  $constraint->upsize();
												 
												  })->save($mainPath.$filename);

										}
						 
						 	$signature_path = $mainPath.$filename;				 
						   }//end sig chk
						   
					}	
			 
		
            $data = [
    
                'title' => __('Documents'),
                'date' => date('m/d/Y'),
				'signature_path' => $signature_path,
                'docs' => $docs,
				'visit'=>$visit,
                'clinic' => $clinic,
                'doctor' =>$doctor,
                'patient' => $patient,
                 ]; 
    
            $pdf = PDF::setOptions(['defaultFont' => 'sans-serif','isHtml5ParserEnabled' => true,'isRemoteEnabled' => true,'isJavascriptEnabled'=>true])
                        -> loadView('emr.visit.documents.DocsPDF', $data);
           
		    $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();
            $canvas->page_text(250, 820, "Page {PAGE_NUM} ".__('of')." {PAGE_COUNT}", null, 10, array(0, 0, 0));
			
             $pdf_merge = PDFMerger::init();
			 $path = public_path('/custom/docs_pdf/');
				if (!file_exists($path)) {
						mkdir($path, 0775, true);
				}
            
                        
             
             //create new pdf in directory
             $uid = auth()->user()->id;
			 $name=date("Y-m-d")."_".$uid."_".uniqid() . ".pdf";
             $pdf_file = $path . $name;
			 
			 //delete old existing files
             $files = glob($path.'*'); // get all file names
                foreach($files as $file){ // iterate files
                  if(is_file($file)) {
                    unlink($file); // delete file
                  }
                }
      		 file_put_contents($pdf_file, $pdf->output());
			 
			 $pdf_merge->addPDF( $pdf_file, 'all');
			
			 $pdf_docs = DOCSResults::where('active','O')->where('visit_num',$id)->where('name','like','%.pdf')->get();
				//dd($pdf_docs);
				if( $pdf_docs->count()>0){
					
					foreach($pdf_docs as $d){
						$pdf_path = str_replace(config("app.url"),config("app.src_url"),$d->path);
						$pdf_merge->addPDF($pdf_path, 'all');
					}
					
				}
             $pdf_merge->merge();
			 
			 
             //$pdf_merge->save('documents.pdf');			 
			 return $pdf_merge->output();
   
    }			
	
	
}