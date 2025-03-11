<!--
   DEV APP
   Created date : 15-11-2022
   Updated date:  9-8-2023
-->

@if($pat_visit->count())
  <div  class="col-md-4">
		   <div class="card card-outline ">
			 <div class="card-body p-1">
				<select name="histf_select" id="histf_select" class="label-size custom-select rounded-0"  size="5">
				@foreach($pat_visit as $h)
				 @php  $cancelled_state =($h->active=='N')?' - '.__('Cancelled'):'';@endphp
				 <option value="{{$h->id}}">{{'#'.$h->id.' , '.Carbon\Carbon::parse($h->created_at)->format('Y-m-d H:i').$cancelled_state}}</option>
				@endforeach
				</select>		
			 </div>
		   </div>
	</div>
  @foreach($pat_visit as $h)
  <div id="HIST-{{$h->id}}" class="card-histf col-md-8" style="display:none;">
            	@php
				
		         $hist_id = ' , #'.$h->id;
				 $name = (app()->getLocale()=='en')?'name_en':'name_fr';
				 $eye_part = isset($h->ocular_part)? UserHelper::getEyePart($h->ocular_part,$name):NULL;
				 $eye_complaint = isset($h->ocular_complaint)?UserHelper::getEyeComplaint($h->ocular_complaint,$h->clinic_num,$name):NULL;
				 $eye_pain = isset($h->ocular_pain) ? UserHelper::getEyePain($h->ocular_pain,$h->clinic_num,$name):NULL;
				 $eye_signs = isset($h->ocular_signs) ? UserHelper::getEyeSigns($h->ocular_signs,$h->clinic_num,$name):NULL;
					$right_eye_conditions = NULL;
					$left_eye_conditions = NULL;
					$both_eye_conditions = NULL;
					if(isset($h->ocular_conds)){
					  $conditions = json_decode($h->ocular_conds,true);
					  if(isset($conditions["right_eye"])){
						$right_eye = json_encode($conditions["right_eye"]);
						$right_eye_conditions = UserHelper::getEyeConditions($right_eye,$h->clinic_num,$name);  
					  }
					  if(isset($conditions["left_eye"])){
						$left_eye = json_encode($conditions["left_eye"]);
						$left_eye_conditions = UserHelper::getEyeConditions($left_eye,$h->clinic_num,$name);  
					  }
					  if(isset($conditions["both_eyes"])){
						$both_eye = json_encode($conditions["both_eyes"]);
						$both_eye_conditions = UserHelper::getEyeConditions($both_eye,$h->clinic_num,$name);  
					  }
					}
				
				$med_infos = isset($h->med_infos)?json_decode($h->med_infos,true):NULL;
				$med_conds = isset($h->med_conds)?UserHelper::getMedConds($h->med_conds,$h->clinic_num,$name):NULL;
				$med_allergies = isset($h->med_allergies)?UserHelper::getMedAllergies($h->med_allergies,$h->clinic_num,$name):NULL;
				$diab_type = isset($h->diab_type)?UserHelper::getDiabType($h->diab_type,$h->clinic_num,$name):NULL;
				$diab_control = isset($h->diab_control)?UserHelper::getDiabControl($h->diab_control,$h->clinic_num,$name):NULL;

				@endphp
				<div class="card card-outline">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('History form').$hist_id}}</b></u>@if($h->active=='N')<span class="label-size badge bg-gradient-danger ml-1">{{__('Cancelled')}}</span>@endif</div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
							 
					     	</div> 
						</div>	
						<div class="card-body" style="height:300px;overflow-y:auto;">
			                <div class="row table-responsive">
							    <div class="col-md-12"> 
								   <table class="table-bordered table-sm" style="width:auto;">
								      <thead>
									    <tr>
										<th>{{__('Principal cause')}}</th>
										@if(isset($h->second_cause))
										<th>{{__('Second cause')}}</th>
									    @endif
										@if(isset($h->exam_date))
										<th>{{__('Date')}}</th>
									    @endif
										@if(isset($h->visit_notes))
										<th>{{__('Remarks')}}</th>	
										@endif	
										</tr>
									  </thead>
									  <tbody>
									   <tr>
									   <td>{{$h->root_cause}}</td>
									   @if(isset($h->second_cause))
									   <td>{{$h->second_cause}}</td>
									   @endif
									   @if(isset($h->exam_date))
									   <td>{{$h->exam_date}}</td>
                                       @endif
									   @if(isset($h->visit_notes))
										<td>
										  
									           @if(strlen($h->visit_notes) > 45)
														{{substr($h->visit_notes,0,45)}}
														<span class="read-more-show hide_content">...<i class="fa fa-plus icon-sm btn-active"></i></span>
														<span class="read-more-content"> {{substr($h->visit_notes,45,strlen($h->visit_notes))}} 
														<span class="read-more-hide hide_content"><i class="fa fa-minus btn-active"></i></span> </span>
											   @else
									             {{$h->visit_notes}}
									           @endif
									   </td>
									 @endif 
					                  </tr>
									  </tbody>
									</table>  
 									</div>
								</div>
							
									@if(isset($eye_part) || isset($eye_complaint) || isset($eye_pain) 
										|| isset($eye_signs) && count($eye_signs)!=0)
								<div class="row mt-2 table-responsive">	   
									<div class="col-md-12"> 
									<table class="table-bordered table-sm" style="width:auto;">  
									  <thead>
									    <tr>
										@if(isset($eye_part))<th>{{__('Eye')}}</th>@endif
										@if(isset($eye_complaint))<th>{{__('Complaint')}}</th>@endif
										@if(isset($eye_pain))<th>{{__('Pain')}}</th>@endif
										@if(isset($eye_signs) && count($eye_signs)!=0)<th>{{__('Signs')}}</th>@endif
									    </tr>
									  </thead>
									  <tbody>
									  <tr>
									  @if(isset($eye_part))
									  <td>{{$eye_part}}</td>
									  @endif
									  @if(isset($eye_complaint))
									  <td>{{$eye_complaint}}</td>
									  @endif
									  @if(isset($eye_pain))
									  <td>{{$eye_pain}}</td>
									  @endif
									  @if(isset($eye_signs) && count($eye_signs)!=0)
									   <td>{{implode(',',$eye_signs)}}</td>
									  @endif
									  
									  </tr>
									  </tbody>
									  </table>
									  </div>
									 </div> 
									  @endif
									  @if(   isset($left_eye_conditions) && count($left_eye_conditions)!=0
									      || isset($right_eye_conditions) && count($right_eye_conditions)!=0
										  || isset($both_eye_conditions) && count($both_eye_conditions)!=0)
									  <div class="row mt-2 table-responsive">
									   <div class="col-md-12">
									  
									   <table class="table-sm table-bordered" style="width:auto;">
									     <caption class="border" style="text-align:center;caption-side:top">{{__('Eye conditions')}}</caption>
										 <thead>
										   	@if(isset($left_eye_conditions) && count($left_eye_conditions)!=0)<th>{{__('Left eye')}}</th>@endif
										    @if(isset($right_eye_conditions) && count($right_eye_conditions)!=0)<th>{{__('Right eye')}}</th>@endif
										    @if(isset($both_eye_conditions) && count($both_eye_conditions)!=0)<th>{{__('Both eyes')}}</th>@endif
										    </tr>
										 </thead>
										 <tbody>
										   <tr>
										   @if(isset($left_eye_conditions) && count($left_eye_conditions)!=0)
											  <td>{{implode(',',$left_eye_conditions)}}</td>
										   @endif
										   @if(isset($right_eye_conditions) && count($right_eye_conditions)!=0)
											  <td>{{implode(',',$right_eye_conditions)}}</td>
										   @endif
										   @if(isset($both_eye_conditions) && count($both_eye_conditions)!=0)
											  <td>{{implode(',',$both_eye_conditions)}}</td>
										   @endif
										   </tr>
										 </tbody>
									   </table>
									  </div>
									 </div> 
									  @endif
									  @if(isset($med_infos) && count($med_infos)!=0
									      || isset($med_conds) && count($med_conds)!=0
										  || isset($med_allergies) && count($med_allergies)!=0)
									  <div class="row mt-2 table-responsive">
									   <div class="col-md-12">
									   <table class="table-bordered table-sm" style="width:100%;">
										  @if(isset($med_infos) && count($med_infos)!=0)
											 @foreach($med_infos as $infos)
											   <tr><td colspan="2"><strong>{{(app()->getLocale()=='en')?$infos['name_en'].' ':$infos['name_fr'].' '}}</strong>
											   @if($infos['ans']=='O')
											   {{__('Yes')}}
											   @else
												   @if($infos['ans']=='N')
												   {{__('No')}}
												   @else
												   {{$infos['ans']}}
												   @endif
											   @endif
											   </td></tr>
											 @endforeach
										  @endif
										  @if(isset($med_conds) && count($med_conds)!=0)
										  <tr><td colspan="2"><strong>{{__('Medical conditions').' : '}}</strong>{{implode(',',$med_conds)}}</td></tr>
										   @endif
										  @if(isset($med_allergies) && count($med_allergies)!=0)
										  <tr><td colspan="2"><strong>{{__('Medical allergies').' : '}}</strong>{{implode(',',$med_allergies)}}</td></tr>
										  @endif
										  </table>
									    </div>
									   </div>	
									  @endif
   								       @if(isset($diab_type)
									      || isset($diab_control)
										  )
									  <div class="row mt-2 table-responsive">
										  <div class="col-md-12 form-group">
										  <table class="table-bordered table-sm" style="width:auto;">
										   <caption class="border" style="text-align:center;caption-side:top">{{__('Diabetes')}}</caption>
										   <thead>
											 @if(isset($diab_type))<th>{{__('Type')}}</th>@endif
											 @if(isset($diab_control))<th>{{__('Control')}}</th>@endif
										   </thead>
										   <tbody>
											   @if(isset($diab_type))
											  <tr><td colspan="2"><strong></strong>{{$diab_type}}</td></tr>
											  @endif
											  @if(isset($diab_control))
											   <tr><td colspan="2"><strong></strong>{{$diab_control}}</td></tr>
											  @endif
										   </tbody>
										  </table>									   
										</div>
                                    </div>
                                    @endif									
						</div>
						
					
				</div>
            </div>
		@endforeach	
@else
	  <div id="HIST" class="col-md-6 mt-1">
					<div class="card card-outline ">
								   
						   <div class="card-header">
								<div class="card-title"><u><b>{{__('History form')}}</b></u></div>
								<div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
									 <i class="fas fa-minus"></i>
								   </button>
								</div> 
							</div>	
							<div class="card-body">
								<div class="row table-responsive">
								  <div class="col-md-12">
								  <h5>{{__('Undefined')}}</h5>
									</div>
								</div>								
							</div>
							
						
					</div>
	</div>			
  @endif	  			