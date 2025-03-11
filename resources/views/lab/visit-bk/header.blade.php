                 <div  style="border:2px solid padding:1px;">
                            <div class="text-center" style="padding-bottom:10px;">
                                 <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
								 <!--<div style="position:absolute;left:30%;font-size:16px;font-family:DejaVu Sans, sans-serif;direction: rtl;">مركز الهادي للخصوبة والتشخيص</div>-->
                            </div>
							<div style="float:left;margin-left:5px;">
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
									 <div><b>{{__('Age/DOB').' : '}}</b> {{Carbon\Carbon::parse($patient->birthdate)->age.' '.__('year(s)')}}
								     <b style="margin-left: 20px;"></b> {{Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y')}}</div>
								@endif
								@if(isset($doctor)) 
								  <div>
									  <b>{{__('Ref By Dr.').' : '}}</b>
									  @if($doctor->middle_name !='' && isset($doctor->middle_name))
										{{$doctor->first_name.' '.$doctor->middle_name.' '.$doctor->last_name}}
									  @else
										{{$doctor->first_name.' '.$doctor->last_name}}	  
									  @endif
								  </div>
								 @endif
                                 <!--@if(isset($ext_lab)) 
								   <div><b>{{__('Ref By Lab').' : '}}</b>{{$ext_lab->full_name}}</div>
								 @endif-->									 
							     @if(isset($first_ins) || isset($second_ins))
								   @if(isset($first_ins)) <div><b>{{__('Guarantor').' : '}}</b>{{$first_ins->full_name}}</div>
							       @else	
							         @if(isset($second_ins)) <div><b>{{__('Guarantor').' : '}}</b>{{$second_ins->full_name}}</div>@endif	
							       @endif 
								@else
								  <div><b>{{__('Guarantor').' : '.__('Private')}}</b></div>	
							    @endif	
                            </div>
                            <div style="float:right;margin-right:5px;">
                                <div><b>{{__('File Nb').' : '}}</b>{{$patient->id}}</div>
								<div><b>{{__('Request Nb').' : '}}</b>{{$order->id}}</div>
								<div><b>{{__('Reg. Date').' : '}}</b>{{Carbon\Carbon::parse($order->order_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->order_datetime)->format('H:i')}}</div>
					            <div><b>{{__('Collection Date').' : '}}</b> {{isset($order->collection_date) && $order->collection_date!=''?Carbon\Carbon::parse($order->collection_date)->format('d/m/Y'):''}}</div>
                               	<div><b>{{__('Reporting Date').' : '}}</b> {{isset($order->report_datetime) && $order->report_datetime!=''?Carbon\Carbon::parse($order->report_datetime)->format('d/m/Y'):''}}</div>
                               				                	
							</div>
                            <div style="clear:both;"></div>
                      </div> 