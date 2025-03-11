<!--
   DEV APP
   Created date : 6-3-2023
-->
<!DOCTYPE html>
<html>
	<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<link rel="stylesheet"   href="{{asset('dist/custom/stylish.min.css')}}"/>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			
			<title>{{__('Treatment Plan')}}</title>
			<style>
			#tarea {
			  border: 2px inset #ccc;
			  background-color: white;
			  
			}

			#footer {
				padding-top: 10px;
				padding-bottom: 0px;
				position:fixed;
				bottom:0;
				width:100%;
                 }      						
			
            </style>
			
	</head>
	<body style="font-size:14px;">	
   
            <div style="width:100%;">
	               
					<div>
                      <div   class="txt-border" style="padding:1px;margin-bottom:1em;">
                            <div style="float:left;">
							 <div>
							   <b>{{__('Treatment Plan')}}</b>
						      
							 </div>
                            </div>
                            <div style="float:right;">
                                <div><b>{{__('Date/Time')}}:</b> {{Carbon\Carbon::parse($visit->visit_date_time)->format('Y-m-d H:i')}}</div>
                                <div><b>{{__('Visit Nb')}}:</b> {{$visit->id}}</div>
                            </div>
                            <div style="clear:both;"></div>
                      </div>
					  <div style="float:left;width:60%">
                           
                            <br/>
                            <div  style="float:left;margin-left:10px;margin-top:0px;">
						       <div><b>{{(isset($clinic))?$clinic->full_name:__('Undefined')}}</b></div>
                               <div><b>{{__('Resource').' : '}}</b> {{isset($doctor)?$doctor->first_name.' '.$doctor->last_name:__('Undefined')}}</div>
                               <div>{{__('Address').': '}} {{(isset($clinic) && isset($clinic->full_address))?$clinic->full_address:__('Undefined')}}</div>
							   <div>Tel.:  {{(isset($clinic) && isset($clinic->telephone))?'+1 '.$clinic->telephone:__('Undefined')}}</div>
                               <div>Fax: {{(isset($clinic) && isset($clinic->fax))?'+1 '.$clinic->fax:__('Undefined')}}</div>
							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

                                <br/>
                                <div  style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>Patient :  {{$patient->first_name.' '.$patient->last_name}}</b></div>
                                    <div>{{__("Address")}} : {{$patient->addresse.' '.$patient->city.' '.$patient->codepostale}}</div>
                                    <div>{{__("Landline Phone")}} :  {{isset($patient->first_phone)?'+1 '.$patient->first_phone:__("Undefined")}}</div>
									<div>{{__("Cell Phone")}} :  {{isset($patient->cell_phone)?'+1 '.$patient->cell_phone:__("Undefined")}}</div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
                    </div>	
					
		<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
			  <div class="col-md-12">
				 @if($patient_plan->count()>0)
				    @php $n=1; @endphp
			        @foreach($patient_plan as $p)
			          @if ( $n % 5 == 0 )
                        <div style="page-break-before:always;"> </div>
                      @endif
					  <div id="tarea" style="margin-bottom:1em;">{!!$p->description!!}</div>
					  
					  @php $n++; @endphp
				   @endforeach
				@else
				   <textarea name="description">{{__('Undefined')}}</textarea>
				@endif	
				</div>
			</div>
		  </div>
			<footer id="footer">
				          <div style="text-align: right;font-size:12px;">
							 
								 @if(isset($signature_path)) 
								  <img   style="border-bottom:2px solid #000;" src="data:image/png;base64,{{base64_encode(file_get_contents($signature_path))}}"/>
								 @else
								   <div>
									 <div style="float:left;width:70%"></div>
									 <div style="float:left;width:30%;border-bottom:2px solid #000;"></div>
									 <div style="clear:both;"></div>	
									</div> 
								 @endif 
							   @if(isset($doctor))
							  <div style="text-align: right;">{{$doctor->first_name.' '.$doctor->last_name}} {{isset($doctor->license_num)?' ( #'.$doctor->license_num.' )':''}}</div>
							   @endif
							  <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('Y-m-d').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div>
				            
						   									
			</footer>
	</body>
</html>
