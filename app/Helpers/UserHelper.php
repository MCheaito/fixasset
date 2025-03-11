<?php
namespace App\Helpers; // Your helpers namespace 
use App\Models\User;
use App\Models\Patient;
use App\Models\Permission;
use App\Models\TblBillRate;
use App\Models\LabTestFields;
use Illuminate\Support\HtmlString;
use DB;
use Session;
use Carbon\Carbon;


class UserHelper
{
    
 
//function to escapt html chars in sms and email templates
	public static function escape_newline($str)
	{
		
		$str = str_replace("\r\n", "*BR*", $str);
		return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
	}


//get accessability of each user to access menu
	public static function can_access($user,$menu){
	   
	   $user_type = $user->type;
	   switch($user_type){
		   //pro account
		   case 1 :
			//admin access all menus
			if($user->admin_perm=='O'){ return true; }
			
			if($user->admin_perm !='O'){
				$perms = Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)
									   ->where('active','O')->first();
				$user_profile = isset($perms->profile_permissions)?explode(",",$perms->profile_permissions):[];
				if(in_array($menu,$user_profile)){
					return true;
				}else{
					return false;
				}
			}
			break;
			//external lab account
		   case 3 :
			//admin access all menus
			if($user->admin_perm=='O'){ return true; }
			
			if($user->admin_perm !='O'){
				$perms = Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)
									   ->where('active','O')->first();
				$user_profile = isset($perms->profile_permissions)?explode(",",$perms->profile_permissions):[];
				if(in_array($menu,$user_profile)){
					return true;
				}else{
					return false;
				}
			}
			break;
			//internal lab account
			case 2:
			//admin access all menus
			if($user->admin_perm=='O'){ return true; }
			
			$perms = Permission::where('uid',$user->id)->where('clinic_num',$user->clinic_num)
									   ->where('active','O')->first();
			$user_profile= isset($perms->profile_permissions)?explode(",",$perms->profile_permissions):[];					   
			if(in_array($menu,$user_profile)){
					return true;
				}else{
					return false;
				}
				
			break;
		   
	   }
	   
		
	}
	
	
public static function drop_session_keys($arr){
		foreach($arr as $key=>$value){
			Session::forget($key);
		}
	}
	
public static function generate_session_keys($arr){
		foreach($arr as $key=>$value){
			Session::put($key,$value);
		}
	}	
	

public static function rp_txt($str){
   	 
	 if (str_contains($str, "custom")) {
         $str = str_replace("custom","index.php/furl",$str);
       }else{
		 $str = "/pubic/index.php/furl".$str;  
	   }

	 
	 if (!str_contains($str, config('app.url'))) {
         $str = config('app.url').$str;
       }
	 
	 
     return $str;
	
	}
	
public static function add_url($str){
	if (!str_contains($str, config('app.url'))) {
         $str = config('app.url').$str;
       }
	 return $str;  
}	

public static function get_article($id){
	$item = DB::table('tbl_inventory_items as i')->select('i.id','i.description as name','f.name as namefournisseur')
	        ->join('tbl_inventory_items_fournisseur as f','f.id','i.fournisseur')
			->where('f.active','O')
			->where('i.id',$id)
			->first();
	$name = isset($item) ? $item->name.'-'.'('.__('Supplier').':'.$item->namefournisseur.')' : __("Undefined");
    return $name;	
}

public static function get_doctor_info($id){
	$data= Doctor::find($id);
	return $data;
}

public static function getPatVisits($id){
	$pat_visits = DB::table('tbl_visits')
	              ->where('patient_num',$id)
				  ->where('active','O')
				  ->ordrBy('visit_date_time','desc')->get();
  return $pat_visits;
}

public static function getRingCentralFaxState($id){
	 require base_path("vendor/autoload.php");
    $rcsdk = new SDK($client_id, $secrect_code, $server);
    $platform = $rcsdk->platform();
	$platform->login($user_name, $ext, $pass);
	
      $endpoint = "/restapi/v1.0/account/~/extension/~/message-store/".$id;
      $resp = $platform->get($endpoint);
      $jsonObj = $resp->json();
      if ($jsonObj->messageStatus == "Queued"){
        sleep(10);
        getRingCentralFaxState($jsonObj->id);
      }
	  
	  return $jsonObj->messageStatus;
	  
    
}

public static function get_zoom_links(){
	$links = DB::table('zoom_links')->orderBy('id','desc')->get();
	return $links;
}

public static function dashboard_pat_name($id){
	$patient = Patient::find($id);
	return $patient->first_name.' '.$patient->last_name;
}

public static function getMainBranchName(){
	$b = DB::table('tbl_clinics')->where('active','O')->where('main_branch','Y')->first();
	return isset($b) && isset($b->full_name)?$b->full_name:'Branch Name';
}

public static function getRefUnit($gender,$test_id,$is_printed,$field_num){
   $ref_range = NULL;
   $one_ref_range = NULL;
   
   $unit = NULL;
   $remark='';
   $range = '';
   
     
 //if($is_printed=='Y'){ 
	
	$field = LabTestFields::whereIn('id',$field_num)->orderBy('field_order')->get();  
	
	
	$field_cnt = $field->count();
	
	if($field_cnt){
			$ref_range = '';
            $one_ref_range = '';
			
			foreach($field as $f){
			 
			
			  //get only one unit
			 if($f->unit!='' && isset($f->unit) && $unit==''){
				$unit =  $f->unit;
			  }
			 //gender
				
				$gender = $f->gender;
				switch($gender){
				 case 'F': $gender=__('Female'); break;
				 case 'M': $gender=__('Male'); break;
				 case 'B': $gender=__('Male or Female'); break;
				 default: $gender = '';
				}
			  
			 
			 
			 $fage = isset($f->fage) && $f->fage!='' && $f->fage!='0'?$f->fage:'';
			 $tage = isset($f->tage) && $f->tage!='' && $f->tage!='0'?$f->tage:'';
			 
			 $age_type = __('yrs');
			 if($f->mytype=='D'){
				$age_type = __('days'); 
			 }
			 if($f->mytype=='M'){
				$age_type = __('months'); 
			 }
			 if($f->mytype=='W'){
				$age_type = __('weeks'); 
			 }
			 
			 $age_range ='';
			 if($fage !='' && $tage !=''){
				 $age_range = $fage.'-'.$tage.' '.$age_type;
				 if($f->mytype=='D' && $tage==365){
					 $age_range = $fage.' '.__('days').'-1'.' '.__('year'); 
				 }
			 }else{
				if($fage =='' && $tage !=''){
					$age_range = '<'.$tage.' '.$age_type;
				}else{
				  if($fage !='' && $tage ==''){
					$age_range = '≥'.$fage.' '.$age_type;
				  }
				}
			 }
			 
			
			 //normal ranges
			 $min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
			 $max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
			 $range='';
			 if($min !='' && $max !=''){
				
				$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min.'-'.$max:$min.'-'.$max;
				//$one_ref_range .= '<div>'.$range.'</div>';   
				}else{
					if($min=='' && $max!=''){
						$range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$max:"<".$max;
						//$one_ref_range .= '<div>'.$range.'</div>';
					}else{
					  if($max=='' && $min!=''){
					    $range=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min:"≥".$min;
					    //$one_ref_range .= '<div>'.$range.'</div>';
					  }else{
					   if($min=='' && $max==''){
						   
						   if(isset($f->descrip)&& $f->descrip!=''){
							  $descrip = $f->descrip;
							  $range = $descrip;
							  //$range='<div style="white-space:pre-line;">'.$descrip.'</div>'; 
						   }
						 }
					  }
				   } 	
				
				}
				
				
				if($gender!=''){
					if($age_range!=''){
						 $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
						 
						 if(strpos($ref_range, $gender) !== false){
						   $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	 
						 }else{
						   $ref_range .= '<div>'.$gender.' ( '.$age_range.' ) '.': '.$range.'</div>';
						 }
					}else{
						 $one_ref_range .= '<div>'.$range.'</div>';	
						 if(strpos($ref_range, $gender) !== false){
							$ref_range .= '<div>'.$range.'</div>'; 
						 }else{
						    $ref_range .= '<div>'.$gender.' : '.$range.'</div>';
						 }
					}
				  }else{
					if($age_range!=''){
					  $ref_range .= '<div>'.$age_range.' : '.$range.'</div>';
					  $one_ref_range .= '<div>'.$age_range.' : '.$range.'</div>';	
					}else{
					  $ref_range .= '<div>'.$range.'</div>';
					  $one_ref_range .= '<div>'.$range.'</div>';	
					 }	
				 }
				 
			
						
				if(isset($f->remark) && $f->remark!=''){
					$remark = $f->remark;
					$remark = '<div style="white-space:pre-line;">'.$remark.'</div>';
					$ref_range.= $remark;
					
				}
				
				
			  }
	
	}
	
   /*}else{
	 
	 $field_id_data = count($field_num);
	 $f = LabTestFields::find($field_id_data); 
	 
	 if(isset($f)){
		if($f->unit!='' && isset($f->unit)){ $unit =  $f->unit;	}
		 
		 $gender = $f->gender;
		
		 switch($gender){
			 case 'F': $gender=__('Female'); break;
			 case 'M': $gender=__('Male'); break;
			 case 'B': $gender=__('Male or Female'); break;
			 default: $gender = '';
			}
		 
		 $fage = isset($f->fage) && $f->fage!='' && $f->fage!='0'?$f->fage:'';
		 $tage = isset($f->tage) && $f->tage!='' && $f->tage!='0'?$f->tage:'';
		 
		 $age_type = __('yrs');
		 if($f->mytype=='D'){
			$age_type = __('days'); 
		 }
		 if($f->mytype=='M'){
			$age_type = __('months'); 
		 }
		 if($f->mytype=='W'){
			$age_type = __('weeks'); 
		 }
		 
		 $age_range ='';
		 if($fage !='' && $tage !=''){
			 $age_range = $fage.'-'.$tage.' '.$age_type;
			 if($f->mytype=='D' && $tage==365){
				 $age_range = $fage.' '.__('days').'-1'.' '.__('year'); 
			 }
		 }else{
			if($fage =='' && $tage !=''){
				$age_range = '<'.$tage.' '.$age_type;
			}
			if($fage !='' && $tage ==''){
				$age_range = '≥'.$fage.' '.$age_type;
			}
		 }
		 
		 
		//normal ranges
		$min = isset($f->normal_value1) && $f->normal_value1!=''?$f->normal_value1:'';
		$max = isset($f->normal_value2) && $f->normal_value2!=''?$f->normal_value2:'';
		if($min !='' && $max !=''){
			$r = isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min.'-'.$max:$min.'-'.$max;
			$range=$r;
			$one_ref_range = $range;
		}else{
	    	if($min=='' && $max!=''){
				$r=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$max:"<".$max;
				$range=$r;
				$one_ref_range = $range;
			}
		    if($max=='' && $min!=''){
	 		   $r=isset($f->descrip)&& $f->descrip!=''?$f->descrip.' '.$min:"≥".$min;
			   $range=$r;
			   $one_ref_range = $range;
			}
		   if($min=='' && $max==''){
			   
			   if(isset($f->descrip)&& $f->descrip!=''){
				  $descrip = $f->descrip;
				  //$range='<div style="white-space:pre-line;">'.$descrip.'</div>';
				  $range = $descrip;
			   }
		   } 	
		}
		
		if($gender!=''){
			if($age_range!=''){
				 $ref_range = '<div>'.$gender.' ( '.$age_range.' )'.' : '.$range.'</div>';
			}else{
				 $ref_range = '<div>'.$gender.' : '.$range.'</div>';	
			}
		  }else{
		    if($age_range!=''){
			  $ref_range = '<div>'.$age_range.' : '.$range.'</div>';
		    }else{
			  $ref_range = '<div>'.$range.'</div>';
		     }	
		 }
		
		if(isset($f->remark) && $f->remark!=''){
					$remark = $f->remark;
					$remark = '<div style="white-space:pre-line;">'.$remark.'</div>';
					$ref_range.= $remark;
					
				}
	
	
	}
																    
   }*/
   
  $unit = self::replaceGreekSymbols($unit);
  return array('ref_range'=>$ref_range,'unit'=>$unit,'one_ref_range' => $one_ref_range);
}

public static function replaceMathSymbols($ref_remark){
	$symbols = array(
	      '≤' => '<=',
		  '≥' => '>=',
		  );
	return str_replace(array_keys($symbols), array_values($symbols), $ref_remark);	  
}

public static function replaceGreekSymbols($unit){
	$greekSymbols = array(
        'α' => '&alpha;',
        'β' => '&beta;',
        'γ' => '&gamma;',
        'δ' => '&delta;',
        'ε' => '&epsilon;',
        'ζ' => '&zeta;',
        'η' => '&eta;',
        'θ' => '&theta;',
        'ι' => '&iota;',
        'κ' => '&kappa;',
        'λ' => '&lambda;',
        'μ' => '&micro;',
        'ν' => '&nu;',
        'ξ' => '&xi;',
        'ο' => '&omicron;',
        'π' => '&pi;',
        'ρ' => '&rho;',
        'σ' => '&sigma;',
        'τ' => '&tau;',
        'υ' => '&upsilon;',
        'φ' => '&phi;',
        'χ' => '&chi;',
        'ψ' => '&psi;',
        'ω' => '&omega;',
        'Α' => '&Alpha;',
        'Β' => '&Beta;',
        'Γ' => '&Gamma;',
        'Δ' => '&Delta;',
        'Ε' => '&Epsilon;',
        'Ζ' => '&Zeta;',
        'Η' => '&Eta;',
        'Θ' => '&Theta;',
        'Ι' => '&Iota;',
        'Κ' => '&Kappa;',
        'Λ' => '&Lambda;',
        'Μ' => '&Mu;',
        'Ν' => '&Nu;',
        'Ξ' => '&Xi;',
        'Ο' => '&Omicron;',
        'Π' => '&Pi;',
        'Ρ' => '&Rho;',
        'Σ' => '&Sigma;',
        'Τ' => '&Tau;',
        'Υ' => '&Upsilon;',
        'Φ' => '&Phi;',
        'Χ' => '&Chi;',
        'Ψ' => '&Psi;',
        'Ω' => '&Omega;'
        
    );

    return str_replace(array_keys($greekSymbols), array_values($greekSymbols), $unit);

}

public static function getSpecimenImg(){
	$specimen = DB::table('tbl_lab_specimen')->get();
	$arr = array();
	
	foreach($specimen as $s){
		$name =strtolower(trim(str_replace(' ','_',$s->name)));
		$id = $s->id;
		switch($name){
			case 'serum':
		     $img = asset('storage/images/specimens/serum.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'plasma_heparine':
		     $img = asset('storage/images/specimens/plasma_heparine.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'plasma_citrate':
		     $img = asset('storage/images/specimens/plasma_citrate.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'whole_blood_edta':
		     $img = asset('storage/images/specimens/whole_blood_edta.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'plasma_edta':
		     $img = asset('storage/images/specimens/plasma_edta.png');
			  array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'swab':
		     $img = asset('storage/images/specimens/swab.png');
			  array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'fluoride_oxalate':
		     $img = asset('storage/images/specimens/fluoride_oxalate.png');
			  array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'csf':
		     $img = asset('storage/images/specimens/csf.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'urine_24hrs':
		     $img = asset('storage/images/specimens/urine_24hrs.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'trace_metal':
		     $img = asset('storage/images/specimens/trace_metal.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'whole_blood_heparine':
		     $img = asset('storage/images/specimens/whole_blood_heparine.png');
			  array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			case 'urine_spot':
		     $img = asset('storage/images/specimens/urine_spot.png');
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
			default:
		     $img = $s->name;
			 array_push($arr,array('id'=>$id,'name'=>$name,'src'=>$img));
		    break;
		}
	}
	
	return $arr;
}

public static function getTextResults($order_id,$test_id){
	
	$culture = DB::table('tbl_order_culture_results')->where('order_id',$order_id)->where('test_id',$test_id)->first();
	$data = NULL;
	
	if(isset($culture)){
		$data = array('culture_id'=>'','gram_staim'=>'','culture_urine'=>'','sbacteria'=>array(),'antibiotics'=>array());
		
		$data['culture_id'] = $culture->id;
		$data['gram_staim']=$culture->gram_staim;
		$data['culture_urine']=$culture->culture_urine;
		$bact_arr = $bact_ant = array();
		$details = DB::table('tbl_order_culture_results_detail')->where('culture_id',$culture->id)->where('active','Y')->get();
		foreach($details as $d){
			if (!in_array($d->bacteria_id, $bact_arr)) {
			 array_push($bact_arr,$d->bacteria_id);
			}
			
			$bacteria_name = DB::table('tbl_lab_sbacteria')->where('id',$d->bacteria_id)->value('descrip');
			$ant_name = DB::table('tbl_lab_antibiotic')->where('id',$d->antibiotic_id )->value('descrip');
			array_push($bact_ant,array($bacteria_name,$ant_name,$d->bacteria_id,$d->antibiotic_id,$d->result));
		}
		$data['sbacteria']=$bact_arr;
		$data['antibiotics']=$bact_ant;
	}
	
	//dd($data);
  return $data;	
}

public static function getTestTextResults($test_id){
	$tst = DB::table('tbl_lab_tests')->find($test_id);
	$txt_area = '';
	if(isset($tst) && isset($tst->result_text)){
		$arr = json_decode($tst->result_text,true);
		if(count($arr)==1){
			$txt_area=DB::table('tbl_lab_text_results')->where('id',$r)->value('name');
		}else{
			foreach($arr as $r){
			$txt_area.=DB::table('tbl_lab_text_results')->where('id',$r)->value('name');
			$txt_area.=PHP_EOL;
			}
		}
		
	} 

return $txt_area;
}

public static function getSign($res_sign){
switch(strtolower($res_sign)){
		case 'high': $sign='H'; break;
		case 'low': $sign='L'; break;
		case 'borderline': $sign='Bord.'; break;
		case 'positive': $sign='Pos'; break;
		case 'negative': $sign='Neg'; break;
		case 'indeterminate': $sign='Ind.'; break;
		default: $sign = $res_sign;
		}

return $sign;
}

public static function getFormula($test_id,$age,$gender){
	$f = DB::table('tbl_lab_tests_formulas')->where('test_id',$test_id)->where('active','Y')->first();
	$data = array();
	if(isset($f) && isset($f->formula)){
		$formula = $f->formula;
		$formula = str_replace('age',$age,$formula);
		$formula = str_replace('^','**',$formula);
		$formula = str_replace('factor1',$f->factor1,$formula);
		$formula = str_replace('factor2',$f->factor2,$formula);
		$formula = str_replace('factor3',$f->factor3,$formula);
		//eGFR test
		if($f->test_id = 1056){
			if($gender=='F'){
				$formula = str_replace('factor4',$f->factor4,$formula);
			}else{
				$formula = str_replace('factor4',1,$formula);
			}
		}else{
		$formula = str_replace('factor4',$f->factor4,$formula);
		}
		
		$data= array('formula'=>$formula,'code1'=>$f->test1,'code2'=>$f->test2,'code3'=>$f->test3,'code4'=>$f->test4);
	    $data = $data;
	}
	return $data;
  	
}

public static function CalcPatientBirthdate($dob){
	$birthdate = Carbon::parse($dob);
	$age = $birthdate->diff(Carbon::now());
	$years = $age->y;
    $months = $age->m;
    $days = $age->d;
	
	$age_data = $age1=$age2=$age3='';
	
	
	if($years>0){
		$age1 = $years.' '.__('years');
	}
	
	if($months>0){
		$age2 = ' '.$months.' '.'months';
	}
	
	if($days>0){
		$age3 = ' '.$days.' '.__('days');
	}
	
	if($age1!='' || $age2!='' || $age3!=''){
	  $age_data = $age1.$age2.$age3;
	}

return $age_data;

}

public static function getSubgroupResults($test_id){
	$results = DB::table('tbl_lab_text_results')->where('test_id',$test_id)->where('status','Y')->orderBy('name')->get();
   return $results;
}

public static function getPatAge($dob){
	
	//age in years
	$age = Carbon::parse($dob)->diffInYears(Carbon::now());
	if($age==0){
		//age in months
		$age=Carbon::parse($dob)->diffInMonths(Carbon::now());
		
		if($age==0){
		  //age in days
		  $age=Carbon::parse($dob)->diffInDays(Carbon::now());
			  $age_label = ($age<=1)?__('day'):__('days');
			  $age_data = $age.' '.$age_label;
		  
		}else{
			
			$age_label = ($age<=1)?__('month'):__('months');
			$age_data = $age.' '.$age_label;
		}
	}else{
		   $age_label = ($age<=1)?__('year'):__('years');
		   $age_data = $age.' '.$age_label;
	}
	return $age_data;

}

public static function getPatExactAge($dob){
	$now = Carbon::now();
    $diff = $now->diff(Carbon::parse($dob));
    $years = $diff->y;
    $months = $diff->m;
    $days = $diff->d;
	$age_data = array();
	if($years!=0){
		$age_label = $years<=1?__('year'):__('years');
		array_push($age_data,$years.' '.$age_label);
	}
	
	if($months!=0){
		$age_label = $months<=1?__('month'):__('months');
		array_push($age_data,$months.' '.$age_label);
	}
	
	if($days!=0){
	    $age_label = $days<=1?__('day'):__('days');	
		array_push($age_data,$days.' '.$age_label);
		
	}
	
	return implode(',',$age_data);
	
}

public static function getPatAgeLBL($dob){
	//age in years
	$age = Carbon::parse($dob)->diffInYears(Carbon::now());
	if($age==0){
		//age in months
		$age=Carbon::parse($dob)->diffInMonths(Carbon::now());
		
		if($age==0){
		  //age in days
		  $age=Carbon::parse($dob)->diffInDays(Carbon::now());
		    $age_data = $age.' '.__('d');
		}else{
			$age_data = $age.' '.__('m');
		}
	}else{
		$age_data = $age.' '.__('y');
	}
	
	return $age_data;
}

public static function getTestSuggestions($test_id){
	$data = DB::table('tbl_lab_text_results')->select('id','name','name_fr')->where('test_id',$test_id)->orderBy('id','desc')->get();
	return $data;

}

public static function getPreviousResult($id){
	$rid = DB::table('tbl_visits_order_results')->find($id);
	$value='';
	if(isset($rid)){
		$date = DB::table('tbl_visits_orders')->where('id',$rid->order_id)->value('order_datetime');
	
    	if(isset($rid->result) && $rid->result!='' && trim($rid->result)!='-'){
			$value = $rid->result;
			$value.=' , ';
			$value.=__('Date').' : '.Carbon::parse($date)->format('d/m/Y');
		}else{
			if(isset($rid->result_txt) && $rid->result_txt!='' && trim($rid->result_txt)!='-'){
				$value = $rid->result_txt;
				$value.=' , ';
			    $value.=__('Date').' : '.Carbon::parse($date)->format('d/m/Y');
			}
		}
	}
	return $value;
}

public static function sendSMS($tels,$msg,$sms_id){
	 //Lebanon test send sms
	 // API endpoint
	 $apiUrl = config('app.apiSMSLB');
	 // API credentials
	 $username = config('app.userSMSLB');
	 $password = config('app.passSMSLB');
	 $msg = mb_convert_encoding($msg, 'UTF-8', 'UTF-8');
	 $senderId = config('app.senderidSMSLB');
	
	 // Prepare data to be sent
			$data = [
				'message' => $msg,
				'senderid' => $senderId,
				'destination' => $tels,
				'username' => $username,
				'password' => $password
				
			];

			// Send the request using cURL
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $apiUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => http_build_query($data),
				CURLOPT_HTTPHEADER => array(
					"content-type: application/x-www-form-urlencoded",
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return false;
				
			} else {
				
			   if(str_contains($response, ';')){	
				 $arr = explode(';',$response);
				 //success then response = [MESSAGE ID];[ PHONE NUMBER];[NUMBER OF UNITS SPENT]
				 if(isset($arr[2])){
				  	   DB::table('tbl_clinic_sms_pack')->where('id',$sms_id)->update(['nb_spent_units'=>intval($arr[2])]);
				       return true;
				   }else{
					return false;
				   }
			  }else{
				  return false;
			  }
			}
}


}