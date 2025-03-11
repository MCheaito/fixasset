<!--
 DEV APP
 Created date : 26-3-2023  
 -->
	
	 @if($presc->count())
		 <div  class="col-md-4">
		   <div class="card card-outline ">
			 <div class="card-body p-1">
				<select name="presc_select" id="presc_select" class="label-size custom-select rounded-0"  size="5">
				@foreach($presc as $c)
				 @php $cancelled_state =($c->active=='N')?' - '.__('Cancelled'):''; @endphp
				 <option value="{{$c->id}}">{{'#'.$c->id.' , '.Carbon\Carbon::parse($c->presc_date)->format('Y-m-d H:i').$cancelled_state}}</option>
				@endforeach
				</select>		
			 </div>
		   </div>
		 </div>  
		 @foreach($presc as $c)	
			@php
			   
			   $presc_id = ' , #'.$c->id;
			   $presc_details=UserHelper::get_presc_details($c->id);
			   $doctor = UserHelper::get_doctor_info($c->doctor_num);
			@endphp		
			<div id="MEDPRES-{{$c->id}}" class="card-presc col-md-8" style="display:none;">
			   <div class="card card-outline ">
					<div class="card-header">
						<div class="card-title">
						
						<u><b>{{__('Medical Prescription').$presc_id}}</b></u>@if($c->active=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif
						</div>
						<div class="card-tools">
						   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
							 <i class="fas fa-minus"></i>
						   </button>
						</div> 
					</div>	
					<div class="card-body p-1">
						<div class="row">
						  
							   <div class="table-responsive col-md-12">
								   <table class="table-bordered table-sm">
									 <thead>
										  <tr>
											<th>{{__('Date')}}</th>
											<th>{{__('Resource')}}</th>
											@if($c->livrer=='Y')
											<th>{{__('Delivery')}}</th>
											@endif
										  </tr>
									 </thead>
									 <tbody>
										
											<tr>
											<td>{{Carbon\Carbon::parse($c->date_presc)->format('Y-m-d H:i')}}</td>
											<td>{{isset($doctor)?$doctor->first_name.' '.$doctor->last_name:__("Undefined")}}</td>
											@if($c->livrer=='Y')
											<td>{{__('By').' : '.$c->livrer_user}}<br/>{{__('Date').' : '.Carbon\Carbon::parse($c->livrer_date)->format('Y-m-d H:i')}}</td>
											@endif
											</tr>
									 </tbody>
								   </table>
								 </div>
								  
								   @if($presc_details->count())
								   <div class="mt-2 table-responsive col-md-12">
								   <table id="myTable" class="table table-bordered table-sm" style="width:100%;text-align:center;">
										<thead>
											<tr class="text-center">
												<th scope="col">{{__('Renew')}}</th>	
												<th class="info" scope="col">{{__('Eye')}}</th>
												<th scope="col">{{__('Section')}}</th>
												<th scope="col">{{__('Medicine Name')}}</th>
												<th scope="col">{{__('Route')}}</th>
												<th scope="col">{{__('Dosage')}}</th>
												<th scope="col">{{__('Freq.')}}</th>
												<th scope="col">{{__('Duration')}}</th>
												<th class="info" scope="col">{{__('Date')}}</th>
												<th scope="col">{{__('Remarks')}}</th>
                                               											
											</tr>
										</thead>
										   <tbody>
											  @php 
												$lang = app()->getLocale();
																						
											  @endphp
											  @foreach($presc_details as $d)
											  
											  <tr>
												<td>{{isset($d->renew)?__($d->renew):__('No')}}</td>
												<td>{{__($d->eye_type)}}</td>
												<td>{{$lang=='en'?$d->section_en:$d->section_fr}}</td>
												<td>{{$lang=='en'?$d->medicine_en:$d->medicine_fr}}</td>
												<td>{{$lang=='en'?$d->voie_en:$d->voie_fr}}</td>
												<td>{{$lang=='en'?$d->dosage.' '.$d->dt_en.' '.$d->dp_en:$d->dosage.' '.$d->dt_fr.' '.$d->dp_fr}}</td>
												<td>{{$lang=='en'?$d->freq_en:$d->freq_fr}}</td>
												<td>{{$d->duration.' '.__($d->duration_unit)}}</td>
												<td>{{$d->expiry_date}}</td>
												<td>{{$d->remarks}}</td>
												
											  </tr>
											  @endforeach
										   </tbody>
							         </table> 
							   </div>
							   @endif
							   
								  
						   
							   
						   
						 </div>								
					</div>
									
								
				</div>
			</div>					
			@endforeach
	@else
	<div id="MEDPRES" class="col-md-6 mt-3">
					<div class="card card-outline ">
								   
						   <div class="card-header">
								<div class="card-title"><u><b>{{__('Medical prescription')}}</b></u></div>
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
