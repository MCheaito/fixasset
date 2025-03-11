<!-- 
 DEV APP
 Created date : 30-9-2022
-->
@extends('gui.main_gui')

@section('content')
        
<div class="mt-1 container-fluid">
  <div class="row">
       <div class="col-md-12">	
            <div class="card card-outline">
			    <div class="card-header card-menu p-1">
				  <div class="row">
					   <div class="col-md-9 text-left">	
						<ul class="nav nav-pills">
						  <li class="nav-item"><a class="nav-link" href="#persos" data-toggle="tab">{{__('General Information')}}</a></li>
						  <li class="nav-item"><a class="nav-link" href="#schedule" data-toggle="tab">{{__('Schedule')}}</a></li>
						  <li class="nav-item"><a class="nav-link" href="#bill" data-toggle="tab">{{__('Billing')}}</a></li>
						  <li class="nav-item"><a class="nav-link" href="#email_sms" data-toggle="tab">{{__('Email/SMS Setting')}}</a></li>
						</ul>
						</div>
						@if(isset($show_profile) && $show_profile)
							<div class="col-md-3 text-right">
						        <a href="{{ route('branches.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>

                            </div> 
						 @endif
						@if(isset($edit_profile) && $edit_profile)
						 <div class="col-md-3  text-right">
							 
                        </div>
						@endif
				   </div>	
				</div>
				<div class="card-body p-0">
				   <div class="tab-content">
				       <div id="persos" class="tab-pane">
					               <form id="clinic_info">
                                    	
                                    <div class="m-1 row">
										<div class="col-md-12 text-right">
													<button type="button" id="save_clinic_info" class="btn btn-action">{{__('Save')}}</button>
													<button  type="reset" class="btn btn-reset">{{__("Reset")}}</button>
													
										</div>
										<div class="form-group col-md-6">
											   <label for="full_name" class="label-size">{{__("Name")}}</label>
											   <input id="full_name" class="form-control" type="text" name="full_name"
													   readonly="(auth()->user()->permission!='U')?'':true" value="{{isset($clinUser) ? $clinUser->full_name : old('full_name')}}"/>					  	   
										</div>
										<div class="form-group col-md-2">
											<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
											<input id="pricel" class="form-control" type="text" name="pricel" 
															    value="{{isset($clinUser) ? $clinUser->pricel : old('pricel')}}" onkeypress="return isNumberKey(event)"/>
														  
										</div>		
										<div class="form-group col-md-2">
															<label for="priced" class="label-size">{{__('Price$')}}</label>
															 <input id="priced" class="form-control" type="text" name="priced" 
															    value="{{isset($clinUser) ? $clinUser->priced : old('priced')}}" onkeypress="return isNumberKey(event)"/>
														  
											</div>	
													
										<div class="form-group col-md-2">
															<label for="pricee" class="label-size">{{__('Price€')}}</label>
															 <input id="pricee" class="form-control" type="text" name="pricee" 
															    value="{{isset($clinUser) ? $clinUser->pricee : old('pricee')}}" onkeypress="return isNumberKey(event)"/>
														  
											</div>	
										<div class="form-group col-md-3">
											  <label for="email" class="label-size">{{__("Email")}}</label>
											  <input type="text" id="email" name="email" class="form-control"
													   value="{{isset($clinUser->email)?$clinUser->email:(isset($user_email)? $user_email:old('email'))}}"/>
										   
										</div>
										
										<div class="form-group  col-md-3">
											  <label for="fax" class="label-size">{{__("Fax Nb")}}</label>
											  
											  <input type="text" id="fax" name="fax"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->fax : old('fax')}}" {{auth()->user()->permission=='U'?'disabled':''}} />
										</div> 
                                        	
                                       <div class="form-group  col-md-3">
											  <label for="telephone" class="label-size">{{__("Contact Phone")}}</label>
											  
											  <input type="text" id="telephone" name="telephone"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->telephone : old('telephone')}}"/>
										      
										</div>
      									<div class="form-group  col-md-3">
											  <label for="alternate_phone1" class="label-size">{{__("Alternate Phone1")}}</label>
											  
											  <input type="text" id="alternate_phone1" name="alternate_phone1"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->alternate_phone1 : old('alternate_phone1')}}"/>
										      
										</div> 
										<div class="form-group  col-md-3">
											  <label for="alternate_phone2" class="label-size">{{__("Alternate Phone2")}}</label>
											 
											  <input type="text" id="alternate_phone2" name="alternate_phone2"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->alternate_phone2 : old('alternate_phone2')}}"/>
										       
										</div> 
											
										
																
                                        <div class="form-group col-md-6">
											  <label for="address" class="label-size">{{__("Address")}}</label>
											  <input type="text" id="full_address" name="full_address" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->full_address : old('full_address')}}"/>
										</div>
										
                                        <!--<div class="form-group  col-md-2">
											  <label for="appt_nb" class="label-size">{{__("Appt.#")}}</label>
											  <input type="text" id="appt_nb" name="appt_nb" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->appt_nb : old('appt_nb')}}"/>
										</div>
										<div class="form-group  col-md-4">
											  <label for="city_name" class="label-size">{{__("City Name")}}</label>
											  <input  name="city_name" type="text" class="form-control" value="{{isset($clinUser) ? $clinUser->city_name : old('city_name')}}"/>
 
										</div>
										<div class="form-group  col-md-4">
												<label for="region_name" class="label-size">{{__('Region Name')}}</label>
                                                <input  class="form-control" name="region_name" type="text" value="{{isset($clinUser) ? $clinUser->region_name : old('region_name')}}"/>
												
										</div>
										<div class="form-group  col-md-4">
											  <label for="state" class="label-size">{{__("State")}}</label>
											  <input type="text" id="state" name="state" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->state : old('state')}}"/>
										</div>
                                        <div class="form-group  col-md-2">
											  <label for="zip_code" class="label-size">{{__("Zip code")}}</label>
											  <input type="text" id="zip_code" name="zip_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->zip_code : old('zip_code')}}"/>
										</div>
                                      	
                                        <div class="form-group col-md-2">
											  <label for="province_code" class="label-size">{{__("Province Code")}}</label>
											  <input type="text" id="province_code" name="province_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->province_code : old('province_code')}}"/>
										</div>-->
                                        <div class="form-group col-md-2">
											  <label for="country_code" class="label-size">{{__("Country Code")}}</label>
											  <input type="text" id="country_code" name="country_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->country_code : old('country_code')}}"/>
										</div> 
										<div class="form-group col-md-3">
											  <label for="whatsapp" class="label-size">{{__("Whatsapp")}}</label>
											  <input type="text" id="whatsapp" name="whatsapp" class="form-control"
													   value="{{isset($clinUser->whatsapp)?$clinUser->whatsapp:old('whatsapp')}}"/>
										   
										</div>
                                        <div class="form-group col-md-4">
											  <label for="website" class="label-size">{{__("Website")}}</label>
											  <input type="text" id="website" name="website" class="form-control"
													   value="{{isset($clinUser->website)?$clinUser->website:old('website')}}"/>
										   
										</div>
                                        <div class="form-group  col-md-5">
											<label for="remarks" class="label-size">{{__("Remarks")}}</label>
											<textarea  class="form-control" id="remarks" name="remarks">{{isset($clinUser)?$clinUser->remarks : old('remarks')}}</textarea>
													
									     </div>
										 
									</div>
									 
									</form>	
                                  
					   </div>
					   <div id="schedule" class="tab-pane">
					       <form id="schedule_form">
									  
								   <div class="row m-1">
										
										<div class="col-md-12">
									      <button type="button" id="save_schedule"  class="m-1 float-left btn btn-action" {{isset($clinUser->open_hours)?'':'disabled'}} >{{isset($clinUser->open_hours)?__('Update schedule'):__('Save schedule')}}</button>
							              <button  type="button" id="reset_schedule" class="m-1 float-right btn btn-reset">{{__('Reset')}}</button>
									      <button type="button" id="delete_schedule" class="m-1 float-right btn btn-delete" {{isset($clinUser->open_hours)?'':'disabled'}}>{{__('Delete schedule')}}</button>
	                                      <input type="hidden" name="action_type"/>
										  <input type="hidden" name="clinic_num" value="{{Crypt::encryptString($clinUser->id)}}"/>
									   </div>
									</div>
									@php 
									  $week_days=array(0 =>'Monday',1=>'Tuesday',2=>'Wednesday',3=>'Thursday',4=>'Friday',5=>'Saturday',6=>'Sunday');
									@endphp 
									
									 <div class="table-responsive m-1 row"> 
									  <table class="table-bordered table-hover table-sm" style="whitespace:nowrap;width:100%">
									  <tbody>
									  @foreach($week_days as $key => $week_day)
										@if(isset($clinUser->open_hours))
										  @php
										  $open_days = json_decode($clinUser->open_hours,true);
										   $day="";
										   $from_time = "";
										   $to_time= ""; 
										   if(isset($open_days[$week_day])){	  
											  $day=explode("-",$open_days[$week_day]);
											  $from_time = $day[0];
											  $to_time= $day[1];
											  }
										 @endphp
										 @endif
									  <tr>
									  <th>{{__($week_day)}}</th>
									  <td>
									   <div class="col-3">
												<input class="border-top-0 border-left-0 border-right-0" id="start_time{{$key}}" name="start_time{{$key}}" 
													   placeholder="{{__('Start Time')}}" type="text" value="@if(isset($clinUser->open_hours)){{$from_time}}@endif" style="width:auto;"/>

										</div>
										</td>
										<td>
										<div class="col-3">
																						
												<input class="border-top-0 border-left-0 border-right-0" id="end_time{{$key}}" name="end_time{{$key}}" 
													   placeholder="{{__('End Time')}}" type="text" value="@if(isset($clinUser->open_hours)){{$to_time}}@endif" style="width:auto;"/>
											
										</div>
										</td>
										</tr>
										@endforeach
										</tbody>
										</table>
									</div>
															
									
															
								</form>
					   </div>
					   					   
					   <div id="bill" class="tab-pane">
					     
                                    <form id="billingForm">
                                        
                                        <div class="row m-1">    
											<div class="col-md-12">
											     <button type="button" id="update_billing"  class="float-right btn btn-action">{{__('Save')}}</button>
											</div>
										</div>	
										<div class="row m-1">
										   <div class="col-md-2 col-6">
										     <label class="label-size" for="prefix">{{__("Serial#")}}</label>
										     <input type="text" id="bill_serial_code" name="prefix" value="{{$clinUser->bill_serial_code}}" class="form-control" readonly="true"/>
										   </div>
										   <div class="col-md-2 col-6">
										       <label class="label-size" for="prefix">{{__("Sequence#")}}</label>
											   <input type="text" id="bill_sequence_num" name="seq" value="{{$clinUser->bill_sequence_num}}" class="form-control" readonly="true"/>
										   </div>
										   <div class="col-md-2 col-6">
												@php
												 $pack = 0;
												 $fixed_pack = 0;
												 $pay_pack = 0;
												 if(isset($sms_package)){
													 $fixed_pack = isset($sms_package->current_sms_pack) && $sms_package->current_sms_pack>0?$sms_package->current_sms_pack:0;
												     $pay_pack = isset($sms_package->pay_pack) && $sms_package->pay_pack>0?$sms_package->pay_pack:0;
                                                     $pack = $fixed_pack+$pay_pack;
												 }
												@endphp
												<label class="label-size" for="smsPack">{{__("SMS#")}}
												 @if(UserHelper::can_access(auth()->user(),'lab_sms_balance'))
											 	 <span id="paid_pack"  style="font-size:0.75rem;">{{'('.__('Paid Pack').' : '.$pay_pack.')'}}</span>
											     <span class="ml-1">
												  <button  onclick="event.preventDefault();$('#smsBalanceModal').modal('show');" class="btn btn-xs btn-icon btn-action" title="{{__('add new balance')}}"><i class="fa fa-plus"></i></button>
												 </span>
												 @endif
												</label>
												
												<input type="text" id="smsPack" name="smsPack" readOnly="true" value="{{$pack}}" class="form-control"/>
											</div>
 										   
											 
											   
										   <div class="col-md-6 select2-teal"> 
												 <label class="label-size" for="prefix">{{__("Payment method")}}
												   @if(UserHelper::can_access(auth()->user(),'lab_payment_methods'))
											        <span class="ml-2"> 
											         <button id="NewPayBtn" class="btn btn-xs btn-icon btn-action" title="{{__('add new pay method')}}"><i class="fa fa-plus"></i></button>
											        </span> 
											       @endif
												 </label>
												 <select id="all_pay_methods" class="select2" data-dropdown-css-class="select2-teal" multiple="multiple" data-placeholder="{{__('Select a payment method')}}" name="pay_methods[]" style="width: 100%;" >
												   @foreach($pay_methods as $m)
												     <option value="{{$m->id}}" {{($clinUser->id==$m->clinic_num)?'selected':''}} >{{(app()->getLocale()=='en')?$m->name_eng:$m->name_fr}}</option>
												   @endforeach
												 </select>
											 </div>
											 <div class="col-md-2 col-6">
												   <label class="label-size" for="lbl_usd">{{__("USD to LBP")}}</label>
												   <input type="number" min="0" id="lbl_usd" name="lbl_usd" value="{{isset($lbl_usd)?intVal($lbl_usd):0}}" class="form-control" onkeypress="return isNumberKey(event)"/>
											  </div>
											  <!--<div class="col-md-2 col-6">
												   <label class="label-size" for="lbl_euro">{{__("EUR to LBP")}}</label>
												   <input type="number" min="0" id="lbl_euro" name="lbl_euro" value="{{isset($lbl_euro)?intVal($lbl_euro):0}}" class="form-control" onkeypress="return isNumberKey(event)"/>
											   </div>-->
                                        </div>
										
										
										</form>
                                        <div class="mt-2 card card-outline">
												<div class="card-body">	
												   <div class="row">	
													 <div class="text-center col-md-9"><h3>{{__("Logo")}}</h3></div>
													 <div class="text-right col-md-3"> 
														<button id="myAttachbtn" name="manualImport" class="m-1 btn" title="{{__('click to attach')}}"  style="width:32px;height:32px;position:relative;padding:0;cursor:pointer;">
																	<div  style="position:absolute;top:50%;left:50%;-ms-transform: translate(-50%, -50%);transform: translate(-50%, -50%);cursor:pointer;">
																	   <a class="btn btn-action"><i class="fa fa-upload"></i></a>
																	   
																	</div>
																	<input id="logo_attach" type="file" name="myFile" 
																		  class="logoUpload" accept="image/*" 
																		style="top: 0;opacity:00;width:100%;height:100%;z-index:2;display:inline-block;cursor:pointer;font-size:0;-ms-transform: translateY(-50%);transform: translateY(-50%);"/>
														</button>
														<!--<img src="{{asset('storage/images/attach-icon.jpg')}}" style="border-radius:20%;width:32px;height:32px;"/>-->
													 </div>
													 <div class="col-md-1"> </div>
													 <div  class="text-center col-md-10" style="border: 1px #1bbc9b;">
													  <div id="getAttachments">
													  @if(isset($bill_logo))
														
															<div  id="attach-{{$bill_logo->id}}" class="mb-1" style="border-radius: 4px;border:solid gray 1px;padding:2px 7px;margin:5px 8px 0 0;display:inline-block;">
																<input type="hidden" name="attachID" id="attachID" value="{{$bill_logo->id}}"/>
																<a  data-fancybox="gallery" data-type="image"  data-caption="{{$bill_logo->clinic_name}}"  data-src="{{url('furl/'.$bill_logo->logo_path)}}" href="javascript:;">
																  <img  class="img-fluid mb-2" alt="" src="{{url('furl/'.$bill_logo->logo_path)}}" />
																</a>
																<i  onclick="getAttach('{{$bill_logo->id}}')" class="fa fa-times" style="color:rgb(220,53,69); margin-left:2px;cursor:pointer;"></i>
															</div>
													  
													  @else
														<h5 class="text-center">{{__('No bill logo')}}</h5>
													  @endif 
													   </div> 
													 </div>
												   </div>
                                              </div>
										</div>
					   </div>
					   <div id="email_sms" class="tab-pane">
					       <form id="emailsmsForm">
                                <div class="row m-1">    
								   <div class="col-md-12">
									   <button id="update_email_sms" type="button" class="float-right btn btn-action">{{__('Save')}}</button>
									   <input type="hidden" id="pass_id" name="profile_id" value="{{Crypt::encryptString($clinUser->id)}}"/>
									</div>
								</div>
								<div class="row m-1">
								  <div class="table-responsive form-group col-md-12">	
								     <table class="table table-sm table-bordered" style="font-size:small;width:100%"> 
									   <thead><tr><th colspan="11" style="text-align:center;"><h5>{{__('Variables')}}</h5></th></tr></thead>
									   <tbody>
										   <td  style="padding:0;border-block-width:medium"><b>*LabName*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*LabAddress*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*LabPhone*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*LabEmail*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*LabWhatsapp*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*LabSite*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*PatientName*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*DoctorName*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*ResultsPDF*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*RequestDate*</b></td>
										   <td  style="padding:0;border-block-width:medium"><b>*RequestTime*</b></td>
									   </tbody>
									  </table> 
									</div>  
								
								</div>
								<div class="row m-1 border">	
					              <div class="form-group col-md-12 text-center"><h5>{{__("Email setting")}}</h5></div>
							      <div class="form-group col-md-8">
								   <label class="label-size" for="email_head">{{__("Title")}}</label>
								   <input type="text" class="form-control" id="email_head" name="email_head" value="{{$email_head}}" class="form-control"/>
							     </div>
							     <div class="form-group col-md-12">	
									   <label class="label-size" for="email_body">{{__("Write your message")}}</label>
									   <textarea id="email_body" name="email_body" rows="10"  style="width:100%;" class="form-control">{{$email_body}}</textarea>
								 </div>
							  </div><!--end mail tab pane-->
							  <div  class="m-1 row border">
						         <div class="form-group col-md-12 text-center"><h5>{{__("SMS setting")}}</h5></div>
								 <div class="form-group col-md-12">	
										   <label class="label-size" for="sms_body">{{__("Write your message")}}</label>
										   <textarea id="sms_body" name="sms_body" rows="10"  style="width:100%;" class="form-control">{{$sms_body}}</textarea>
								 </div>				 
						      </div>
                            </form>   										
					   </div>
				   </div>
				</div>
			</div>
		</div>  
     </div>
</div>	
 @if(UserHelper::can_access(auth()->user(),'lab_payment_methods'))
  @include('profile.branch.payMethodModal')                                  
 @endif
 @if(UserHelper::can_access(auth()->user(),'lab_sms_balance'))
	 @include('profile.branch.smsBalanceModal') 
 @endif
 @if(auth()->user()->admin_perm=='O')
	 @include('profile.branch.offerTypesModal') 
 @endif
@endsection

@section('scripts')
 
<script>
        $(document).ready(function() {
            
			
				$('.select2').select2();
				$('[name=email]').inputmask("email");
				var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
					$('[name=telephone],[name=fax],[name=whatsapp]').inputmask({ 
						mask: phones, 
						greedy: false, 
						definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
					});

					
            $('a[data-toggle="tab"]').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				});

				$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
					var id = $(e.target).attr("href");
					localStorage.setItem('selectedTab', id)
				});

				var selectedTab = localStorage.getItem('selectedTab');
				//alert(selectedTab);
				if (selectedTab != null) {
					$('a[data-toggle="tab"][href="' + selectedTab + '"]').tab('show');
				}else{
					$('a[data-toggle="tab"][href="#persos"]').tab('show');
				}

			
			
			
			
        });
</script>                  
<script>
$(function(){

								
	   
	  //script for each day	   
	 for(let i=0;i<=6;i++){
		flatpickr('#start_time'+i, {
				enableTime: true,
				time_24hr: true,
				noCalendar: true,
				dateFormat: "H:i"
				
			});
		
		flatpickr('#end_time'+i, {
				enableTime: true,
				time_24hr: true,
				noCalendar: true,
				dateFormat: "H:i"
				
			});
			
        $("#start_time"+i).off().on('change',function (e) {
            e.preventDefault();
			if($("#start_time"+i).val() != null  && $('#save_schedule').prop("disabled")){
			  
			  $('#save_schedule').prop("disabled", false);
			 }
            flatpickr('#end_time'+i, {
                enableTime: true,
				time_24hr: true,
				minTime: $("#start_time"+i).val(),
                noCalendar: true,
                dateFormat: "H:i"
            });
        });
		
		$("#end_time"+i).off().on('change',function (e) {
            e.preventDefault();
		    if($("#end_time"+i).val() != null  && $('#save_schedule').prop("disabled")){
			 
			  $('#save_schedule').prop("disabled", false);
			 }
		});
		}
		
		$('#reset_schedule').off().on('click',function(e){
			e.preventDefault();
			$('#schedule_form').trigger("reset");
			var count=0;
			for(let i=0;i<=6;i++){
			  if($('#start_time'+i).val()=='') {count++;}
			  if($('#end_time'+i).val()=='') {count++;}
		    }
			
			if(count==14){
				 $('#save_schedule').prop("disabled", true);
			}
		});
		
	 $('#delete_schedule').off().on('click',function(e){
		e.preventDefault();
		 var cfrm = confirm("{{__('Are you sure?')}}");
		 if(cfrm){
			 $('input[name="action_type"]').val('delete_schedule');
			 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
			   
			   $.ajax({
				type: 'POST',
				url: '{{route("profiles.clinic.schedule",app()->getLocale())}}',
				dataType: 'JSON',
				data: $('#schedule_form').serialize(),
				success: function (data) {
					
					//Swal.fire({title:data.msg,icon:'error',toast:true,timer:5000,position:'bottom-right',showConfirmButton:false});
					//location.reload();
					window.location.href="{{route('profiles.clinic',app()->getLocale())}}";
					//clear schedule
					/*for(let i=0;i<=6;i++){
						$('#start_time'+i).val('');
						$('#end_time'+i).val('');
					   }*/
					}
			    });
		        }
	         });
		 
		 $('#save_schedule').off().on('click',function(e){
			e.preventDefault();
			 $('input[name="action_type"]').val('save_schedule');
			 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
			   
			   $.ajax({
				type: 'POST',
				url: '{{route("profiles.clinic.schedule",app()->getLocale())}}',
				dataType: 'JSON',
				data: $('#schedule_form').serialize(),
				success: function (data) {
					//Swal.fire({title:data.msg,icon:'success',toast:true,timer:5000,position:'bottom-right',showConfirmButton:false});
					//location.reload();
					window.location.href="{{route('profiles.clinic',app()->getLocale())}}";
					}
			   });
	       });
		 
});
	   
</script>
<script>
	$(function(){
		
		
		$('#save_clinic_info').off().on('click',function(e){
               e.preventDefault();
			    $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
			   $.ajax({
				 url: "{{route('profiles.clinic.info',[app()->getlocale(),$clinUser->id])}}",
                 method: "PUT",
				 dataType: "JSON",
                  data: $('#clinic_info').serialize(),
				  success: function(result){
                   
					 if(result.errors){

						Swal.fire({"text":result.errors[0],"icon":"error","customClass": "w-auto"});
					 }
					 if(result.error_useremail){

						Swal.fire({"text":result.error_useremail,"icon":"error","customClass": "w-auto"});
					 }
					 
					 if(result.success){
						   
						   Swal.fire({
								icon: 'success',
								toast: true,
								position: 'bottom-end',
								timer: 5000,
								showConfirmButton: false,
								title: result.success
								});
								//window.location.href="{{route('profiles.clinic',app()->getLocale())}}";
								//location.reload();
								
                           }

                       }

			   });
			});
	});
  </script>	
 
<script>
  $(function(){
	  var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content"); 

	  $('#NewPayBtn').off().on('click',function(e){
		 e.preventDefault();
		 //$('#NewPay').show(); 
		  $('#payMethodModal').modal("show");
	  });
	  
	  
	  
	  $('#cancel_new_pay').off().on('click',function(e){
		 e.preventDefault();
		
            //reset new pay fields
					$('#newPayMethodEN').val('');
		            $('#newPayMethodFR').val('');
		            $('#haveInsurance').val('N');
		            //$('#NewPay').hide(); 
		  
	  });
	  
	  /*$('#selecttax').change(function(e){
			 e.preventDefault();
			 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
		    $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: {bill_rate_id:$(this).val(),type:'get_bill_rates',_token:CSRF_TOKEN},
			  dataType: 'json',
			  success: function(data){
				  //update rates
				  $('#tva_rate').val(data.tva_rate);
				  $('#lbl_rate').val(data.lbl_rate);
				 // $('#gst_rate').val(data.gst_rate);
			    }
			  });
		});*/
	  
	  
	  
	  $('#save_new_pay').off().on('click',function(e){
		 e.preventDefault();
		 var method_en= $('#newPayMethodEN').val();
		 
		 if( method_en==''){ 
		 Swal.fire({text:'{{__("Please input the pay method name in english")}}', icon:'error',customClass:'w-auto'});
		 return false;
		 }
		 var method_fr=  $('#newPayMethodFR').val();
		 if( method_fr==''){ 
		 Swal.fire({text:'{{__("Please input the pay method name in french")}}', icon:'error',customClass:'w-auto'});
		 return false;
		 }
		 var insurance=  $('#haveInsurance').val();
		  $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: {clinic_id:'{{$clinUser->id}}',name_eng: method_en,name_fr:method_fr,insurance:insurance,type:'new_pay_method',_token:CSRF_TOKEN},
			  dataType: 'json',
			  success: function(data){
				    //reset new pay fields
					$('#newPayMethodEN').val('');
		            $('#newPayMethodFR').val('');
		            $('#haveInsurance').val('N');
				    //$('#NewPay').hide();
					$('#payMethodModal').modal("hide");
					$('#all_pay_methods').empty();
					$('#all_pay_methods').html(data.html);
					Swal.fire({title:data.success,icon:'success',toast:true,timer:1500,showConfirmButton:false,position:'bottom-end'});
			  }
		  });
		  
	  });
	  
	  $('#update_email_sms').off().on('click',function(e){
		   e.preventDefault();
		       $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
			   $.ajax({
				 url: "{{route('profiles.clinic.sms_email_setting',[app()->getlocale()])}}",
                 method: "POST",
				 dataType: "JSON",
                 data: $('#emailsmsForm').serialize(),
				 success: function(result){
					Swal.fire({title:result.success,icon:'success',toast:true,timer:1500,showConfirmButton:false,position:'bottom-end'});
  				  }
			   });
	  });
	  
	  $('#update_billing').off().on('click',function(e){
		  e.preventDefault();
		  
		  var bill_serial_code = $('#bill_serial_code').val();
		  if( bill_serial_code==''){ 
		    Swal.fire({text:'{{__("Please input a bill serial")}}', icon:'error',customClass:'w-auto'});
		    return false;
		   }
		  var bill_sequence_num = $('#bill_sequence_num').val();
		  if( bill_sequence_num==''){ 
		    Swal.fire({text:'{{__("Please input a bill sequence")}}', icon:'error',customClass:'w-auto'});
		    return false;
		   }
		   
		   var lbl_usd = $('#lbl_usd').val();
		   if(!$.isNumeric(lbl_usd)){
			 Swal.fire({text:'{{__("Please input a numeric value in USD to LBP rate")}}', icon:'error',customClass:'w-auto'});
		    return false;  
		   }
		   
		   var lbl_euro = $('#lbl_euro').val();
		   /*if(!$.isNumeric(lbl_euro)){
			 Swal.fire({text:'{{__("Please input a numeric value in EUR to LBP rate")}}', icon:'error',customClass:'w-auto'});
		    return false;  
		   }*/
		   /*var lbl_rate = $('#lbl_rate').val();
		   if(!$.isNumeric(lbl_rate)){
			 Swal.fire({text:'{{__("Please input a numeric value in LBL rate")}}', icon:'error',customClass:'w-auto'});
		    return false;  
		   }*/
		   
		   
		  $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: {clinic_id:'{{$clinUser->id}}',data:$('#billingForm').serialize(),type:'update_bill',_token:CSRF_TOKEN},
			  dataType: 'json',
			  success: function(data){
				 //Swal.fire({title:data.success,icon:'success',toast:true,timer:3000,showConfirmButton:false,position:'bottom-end'});
				  window.location.href="{{route('profiles.clinic',app()->getLocale())}}";
			  }
		  });
		  
	  });
	  
	  $('.logoUpload').change(function(e){
		  e.preventDefault();
		  var fd=new FormData();
			fd.append('logoImage',$('.logoUpload')[0].files[0]);
			fd.append('clinic_id','{{$clinUser->id}}');
			fd.append('type','new_bill_logo');
			fd.append('_token',CSRF_TOKEN);
		  $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: fd,
			  contentType: false,
              cache: false,
              processData: false,
			  dataType: 'json',
			  success: function(data){
				   if(data.success){
					$('#getAttachments').empty();
					$('#getAttachments').html(data.html);
			        $('#logo_attach').val('');
					Swal.fire({title:data.success,icon:'success',toast:true,timer:1500,showConfirmButton:false,position:'bottom-end'});
				   }
				   if(data.error){
				     Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
  
				   }
			  }
		  });
	  });
	  
	  $('#add_new_sms_balance').off().on('click',function(e){
		   e.preventDefault();
		    var oldPack = parseInt($('#smsPack').val());
			 var newPack = parseInt($('#newPack').val());
			   if(newPack<0){
					Swal.fire({text:'{{__("Sms package must be a positive number")}}', icon:'error',customClass:'w-auto'});
				return false;
			   }
		  
		    var sms_package = oldPack + newPack;
		   
		   
           $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: {clinic_id:'{{$clinUser->id}}',new_pay:newPack,type:'new_sms_balance',_token:CSRF_TOKEN},
			  dataType: 'json',
			  success: function(data){
				 Swal.fire({title:data.success,icon:'success',toast:true,timer:3000,showConfirmButton:false,position:'bottom-end'});
				 $('#newPack').val(0);
		         $('#smsPack').val(data.sms_package);
				 $('#paid_pack').text('{{"(".__("Paid Pack")." : "}}'+data.paid+")");
			     $('#smsBalanceModal').modal("hide");
			  }
		     });
	   });
	  
  });
</script>
<script>
function getAttach(id){
	 var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content"); 
	 var attach_id = id;
	 $.ajax({
			  url: '{{route("save_bill",app()->getLocale())}}',
			  type: 'post',
			  data: {attach_id:attach_id,clinic_id:'{{$clinUser->id}}',type:'delete_bill_logo',_token:CSRF_TOKEN},
			  dataType: 'json',
			  success: function(data){
				   if(data.success){
					$('#getAttachments').empty();
					$('#getAttachments').html('<h5 class="text-center">{{__("No bill logo")}}</h5>');
					Swal.fire({title:data.success,icon:'success',toast:true,timer:1500,showConfirmButton:false,position:'bottom-end'});
				   }
				   
			  }
		  });
}
</script>
<script>
function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
</script>
@endsection