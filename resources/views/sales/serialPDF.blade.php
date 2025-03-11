<!DOCTYPE html>
<html>
  <head>
	 <meta name="viewport" content="width=device-width, initial-scale=1">
     
	 <title>{{__('Patient Serial')}}</title>
	 <style>
	 
	   @page { margin: 10px 20px 5px 20px; }
	 </style>
  <head>
  <body>
   
    @php 
	  $request_nb = strval($data->order_id);
	  $patient_name = $data->patient_name;
	  $doctor_name = $data->doctor_name;
	  
	@endphp
   
	     <div style="width:100%;"> 
			 <div style="margin-top:15px;text-align:center;">
				<span>
				   <span style="font-size:14px;">
					 <div>{{$patient_name}}</div>
					 <div>{{Carbon\Carbon::parse($data->order_datetime)->format('d/m/Y H:i')}}</div>
					 <div style="font-size:22px;"><b>{{$serial_nb}}</b></div>
					 @if($doctor_name!="")<div style="font-size:14px;"><b>{{$doctor_name}}</b></div>@endif
				   </span>
				</span>
			 </div>
		 </div>
       
  </body>
</html>  	