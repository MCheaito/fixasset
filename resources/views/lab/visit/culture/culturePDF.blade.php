<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Culture')}}</title>
			<style>
			 @page { margin: 180px 20px 60px 20px; }
             header { position: fixed; top: -160px; left: 0px; right: 0px; height: 50px; }
             footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 90px; }
			.page:after { content: counter(page, decimal); }
             body {
               font-family: 'DejaVu Serif', serif;
               line-height: 1;
			   font-size: 11px !important;
			  }
			 .center {
                text-align: center;
              }
             .center img {
                display: block;
              }
			
			
         </style>
			
	</head>
	<body style="font-size:12px;">	
               <header>
			     <div  style="border:0 px;padding:1px;">
                            @if(isset($logo))
							   <div style="float:left;width: 12%;height:70px;padding-bottom:5px;">
							   <img src="{{config('app.url').'/storage/app/7mJ~33/'.$logo->logo_path}}" style="width:100%;height:100%;"/>
							  </div>
							  <div class="text-center" style="width:88%;padding-top:10px;padding-bottom:5px;">
                                  <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
                              </div>
							 <div style="clear:both;"></div> 
						    @else
								<div class="text-center" style="padding-bottom:5px;">
                                  <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
								  <!--<div style="position:absolute;left:30%;font-size:16px;font-family:DejaVu Sans, sans-serif;direction: rtl;">مركز الهادي للخصوبة والتشخيص</div>-->
                                 </div>
							@endif	
							<div style="float:left;width:45%;border-top:1px solid;border-left:1px solid;border-right:3px solid;border-bottom:3px solid;padding-left:5px;margin-left:5px;">
							    <div>
								  <b>{{__('Name').' : '}}</b>
								  @if($patient->middle_name !='' && isset($patient->middle_name))
									{{$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name}}
								  @else
									{{$patient->first_name.' '.$patient->last_name}}	  
								  @endif	  
							    </div>
								<div>
								  <b>{{__('Gender').' : '}}</b> {{($patient->sex=='F')?'Female':( ($patient->sex=='M')?'Male':'Undefined')}}
								</div>
								 @if(isset($patient->birthdate) && $patient->birthdate!='')
									 <div><b>{{__('Age/DOB').' : '}}</b> {{UserHelper::getPatAge($patient->birthdate)}}
								     <b style="margin-left: 20px;"></b> {{Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y')}}</div>
								@endif
								<div><b>{{__('File Nb').' : '}}</b>{{$patient->file_nb}}</div>
								<div><b>{{__('Request Nb').' : '}}</b>{{$order->request_nb}}</div>
                            </div>
							<div style="float:left;width:5%;"></div>
                            <div style="float:left;width:45%;margin-left:5px;border-top:1px solid;border-left:1px solid;border-right:3px solid;border-bottom:3px solid;padding-left:5px;">           
                                <div>
									  <b>{{__('Ref By Dr.').' : '}}</b>
									  @if(isset($doctor)) 
										  @if($doctor->middle_name !='' && isset($doctor->middle_name))
											{{$doctor->first_name.' '.$doctor->middle_name.' '.$doctor->last_name}}
										  @else
											{{$doctor->first_name.' '.$doctor->last_name}}	  
										  @endif
									  @else
									     {{__('-')}}
									  @endif	  
								  </div>
                                  @if(isset($ext_lab) && isset($ext_lab->full_name) && $ext_lab->full_name!='')
								  <div><b>{{__('Guarantor').' : '.$ext_lab->full_name}}</b></div>	
							     @else	 
								  <div><b>{{__('Guarantor').' : '.__('Private')}}</b></div>	
							     @endif
								
								<div><b>{{__('Reg. Date').' : '}}</b>{{Carbon\Carbon::parse($order->order_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->order_datetime)->format('H:i')}}</div>
                               				                	
							</div>
                            <div style="clear:both;"></div>
                      </div>
			   </header>
			   <footer>
			     
				 @include('lab.visit.footer')
			   </footer>
			   <main>
			   @php $cnt=0; @endphp
			   @foreach($culture_data as $culture)
			      @php $page_break = ($cnt==0)?'width:100%;':'width:100%;page-break-before:always'; @endphp  
			       <div  style="{{$page_break}}">
					    <div style="margin-top:20px;margin-bottom:10px;margin-left:10px; width: 90%; float: left;box-sizing: border-box;">
								<div style="border: 1px solid #ccc;margin-bottom: 10px;">
									<div style="background-color: #eeeeee;border:0px;text-align:center;padding: 5px;">
										<b>{{__('Bacteriology')}}</b>
									</div>
									 <div style="padding: 10px;">
											<div style="display: inline-block; width: 100%;"><b>{{__('Test Name').' : '}}</b>{{$culture->test_name}}</div>
											<div style="display: inline-block; width: 100%;"><b>{{__('Gram Stain').' : '}}</b>{{$culture->gram_staim}}</b></div>
                                            <div style="display: inline-block; width: 100%;"><b>{{__('Culture Result').' : '}}</b>{{$culture->need_validation=='Y'?__('Pending Validation'):$culture->culture_result}}</div>
									</div>
								</div>
					          
							  
						</div>
					   <div style="clear: both;"></div>
					
					@if(count($bacteria))
				      <div style="width:100%">
				      @php $counter = 0; @endphp
				      @foreach($bacteria as $b)
					      @php 
						   $details = $culture_details->where('bacteria_id',$b->id)->where('culture_id',$culture->culture_id)->all();
						   $bacterias = $culture_details->where('bacteria_id',$b->id)->where('culture_id',$culture->culture_id)->pluck('bacteria_id')->toArray();
						  @endphp
						  @if(in_array($b->id,$bacterias))	  
						  <div style="margin-left:5px; width: 45%; float: left;box-sizing: border-box;">
							<div style="border: 1px solid #ccc; margin-bottom: 10px;">
								<div style="background-color: #00548E; color: #fff; padding: 5px;">
									<b>{{__('Germ')}}: {{$b->descrip}}</b>
								</div>
								  <div style="padding: 10px;">
									<div style="display: inline-block; width: 70%;"><b><u>{{__('Antibiotics')}}</u></b></div>
									<div style="display: inline-block; width: 25%;"><b><u>{{__('Results')}}</u></b></div>
									
										@foreach($details as $d)
										<div style="padding-bottom: 1px;padding-top: 2px;border-bottom: 1px solid #ccc;">
										 <div style="display: inline-block; width: 70%;">{{$d->antibiotic_name}}</div>
										 <div style="display: inline-block; width: 25%;">{{$culture->need_validation=='Y'?__('-'):$d->result}}</div>
										</div>
										@endforeach
									
								</div>
							</div>
							
						  </div>
						 @php $counter++;@endphp
                         @if ($counter %2 ==0) 
                            <div style="clear: both;"></div>
                         @endif  
					@endif
				  
				  @endforeach
				  @if ($counter %2 !=0) 
                         <div style="clear: both;"></div>
                  @endif  
				@endif
			    </div>
			  
			   <div  style="font-size:10px;margin-left:10px;margin-top:30px;text-algin:center;width:100%;">
				   <p><b>S:Sensitive</b></p>
				   <p><b>R:Resistant</b></p>
				   <p><b>I:Intermediate</b></p>
			   </div>
			  </div>
			  @php $cnt++; @endphp
             @endforeach
			 <!--@if(isset($general_sig))
			  <div style="float:left;"></div>
			  <div style="float:right;margin-top:5px;margin-bottom:5px;">
				<img src="{{config('app.url').'/storage/app/7mJ~33/'.$general_sig->path}}" style="width:120px;height:60px;" alt="SIgnature">				  
			  </div>
			  <div style="clear: both;"></div>
	         @endif-->
			</main>
		    
	</body>
</html>
