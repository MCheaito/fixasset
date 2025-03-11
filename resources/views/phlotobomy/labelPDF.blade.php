<!DOCTYPE html>
<html>
  <head>
	 <meta name="viewport" content="width=device-width, initial-scale=1">
     
	 <title>{{__('Label')}}</title>
	 <style>
	 
	   @page { margin: 5px 5px 5px 5px; }
	 </style>
  <head>
  <body>
   
    @php 
	  $order_id = strval($label->request_nb);
	  $request_nb = 'No'.' : '.strval($order_id);
	  $patient_nb = $label->file_nb;
	  $patient_name = $label->patient_name;
	  $patient_age = isset($label->patient_dob) && $label->patient_dob!=''?UserHelper::getPatAgeLBL($label->patient_dob):"";
	  $patient_gender = "";
	  if(isset($label->patient_gender) && $label->patient_gender!=""){  $patient_gender=$label->patient_gender."-";  }
	  $coll_date = isset($label->coll_datetime) && $label->coll_datetime!=""?'Collection'.' : '.Carbon\Carbon::parse($label->coll_datetime)->format('d/m/Y H:i'):"";
	  $guarantor = "";
	  if(isset($ext_lab)){  $guarantor=$ext_lab->full_name;  }
	  $doctor_name = "";
	  if(isset($doctor)){
		   $mname = isset($doctor->middle_name)?' '.$doctor->middle_name:'';
		   $doctor_name=$doctor->first_name.$mname.$doctor->last_name;
	  }
	  $order_date_time = Carbon\Carbon::parse($label->order_datetime)->format('d/m/Y H:i');
	  
	  $cnt=0;
	@endphp
    @foreach($test_codes as $tc)
	     @if($cnt>=1)<div style="page-break-before:always;"></div>@endif
		<div style="width:100%;"> 
		       <div style="font-size:12px;">{{__('File Nb.')}}&nbsp;:&nbsp;{{$patient_nb}}</div>
			   <div style="font-size:12px;">{{$patient_name}}&nbsp;&nbsp;{{$patient_gender.$patient_age}}</div> 
			   <div style="font-size:12px;">{{$coll_date}}</div>
			    @if($guarantor!="")<div style="font-size:12px;">{{$guarantor}}</div>@endif
			    @if($doctor_name!="")<div style="font-size:12px;">{!!str_replace(' ','&nbsp;',$doctor_name)!!}</div>@endif
			   <div style="font-size:12px;">{!!str_replace(' ','&nbsp;',$tc['specimen_name'])!!}&nbsp;:&nbsp;{{implode(',',$tc['codes'])}}</div>
			   <div style="font-size:12px;">{{$order_id}}</div>
			   <div>{!! DNS1D::getBarcodeHTML($order_id, 'C128',2,22) !!}</div>
		 </div>
		 @php $cnt++; @endphp
     @endforeach  
  </body>
</html>  	