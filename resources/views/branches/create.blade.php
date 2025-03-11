<!--
  DEV APP
  Created date : 9-1-2023
-->
@extends('gui.main_gui')
@section('styles')
<style>
.badge{
	font-size:0.9rem;
}
</style>
@endsection
@section('content')
<!-- begin:: Content Container-->
<div class="container-fluid">
					<div class="m-1 row">
							<div class="col-md-6">
							  <h3>{{isset($clinic) ? __('Edit Lab Info') : __('Add New Lab')}}</h3>
                            </div>
							<div class="col-md-6 text-right">
							   <a href="{{ route('branches.index',app()->getLocale()) }}" class="btn btn-back" >{{__('Back')}}</a>
							</div>
							   
					</div>	
            <div class="card mt-2">
					    <div class="card-header card-menu">
						   <ul class="nav nav-pills">
					          <li class="nav-item"><a class="nav-link" href="#persos" data-toggle="tab">{{__('General Information')}}</a></li>
					          <li class="nav-item"><a class="nav-link" href="#account" data-toggle="tab">{{__('Account Information')}}</a></li>
					          <li class="nav-item"><a class="nav-link" href="#schedule" data-toggle="tab">{{__('Schedule')}}</a></li>
					       </ul>
						</div>
				<div class="card-body"> 
                   <div class="tab-content">
						   <div id="persos" class="tab-pane">
							<!--begin::Form-->
                             <form 
                              action="{{isset($clinic) ? route('branches.update',[app()->getLocale(),$clinic->id]) : route('branches.store',app()->getLocale())}}"
                              method="POST"
                              enctype="multipart/form-data">
                           
                                
                                @csrf
                                @if(isset($clinic))
                                    @method('PUT')
                                @endif
                           <!--begin clinic profile-->                  
                            <div class="row">
								<div class="col-md-6">
								 <span class="badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</span>
								</div>
								<div class="col-md-6 text-right">
                                        <input type="submit" 
										       name="update_clinic"
											   value="{{isset($clinic) ? __('Update') : __('Save')}}"
                                               class="btn btn-action">
                                    </div>
								<div class="col-md-12 m-1">
                                  
								  <div class="row">
										<div class="form-group col-md-4">
												 <label for="name" class="label-size">{{__('Name').'*'}}</label>
												 <input id="name" class="form-control" type="text" name="name"
												   placeholder="NEW"
												   value="{{isset($clinic) ? $clinic->full_name : old('name')}}">
											  
										</div>
										<div class="form-group col-md-2">
												<label for="type"  class="label-size">{{__('Type').'*'}}</label>
                                                @if(auth()->user()->admin_perm!='O')
												   @if(isset($clinic))
													   @php 
												         $type_clin ='';
														 switch($clinic->kind){
															 case 'lab' :  $type_clin =  __('Lab'); break;
															 case 'hospl' :  $type_clin = __('Hospital Lab'); break;
															
														    }
												       @endphp
													    <select class="custom-select rounded-0" name="type">
													      <option value="{{$clinic->kind}}">{{$type_clin}}</option>
													    </select>
											        @else	  
													 <select class="custom-select rounded-0" name="type">
													  <option value="lab">{{__('Lab')}}</option>
													  <option value="hospl">{{__('Hospital Lab')}}</option>
													  
													</select>
 												   @endif
												@endif
												@if(auth()->user()->admin_perm=='O')
												<select class="custom-select rounded-0" name="type">
												  @php $selected_value=(isset($clinic))?$clinic->kind:old('type');@endphp
												  <option value="clin" {{($selected_value=='lab')?'selected':''}}>{{__('Lab')}}</option>
  												  <option value="hosp" {{($selected_value=='hospl')?'selected':''}}>{{__('Hospital Lab')}}</option>
												  
												</select>
												@endif
											
										</div>
										<div class="form-group col-md-2">
											<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
											<input id="pricel" class="form-control" type="text" name="pricel" 
															    value="{{isset($clinic) ? $clinic->pricel : old('pricel')}}" onkeypress="return isNumberKey(event)" 
																oninput="var rated = '{{$lbl_usd}}'; var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){$('#priced').val(parseFloat(price/rated).toFixed(2));$('#pricee').val(parseFloat(price/ratee).toFixed(2));}else{  $('#priced').val(''); $('#pricee').val(''); }"/>

														  
										</div>		
										<div class="form-group col-md-2">
															<label for="priced" class="label-size">{{__('Price$')}}</label>
															 <input id="priced" class="form-control" type="text" name="priced" 
															    value="{{isset($clinic) ? $clinic->priced : old('priced')}}" 
																oninput="var rated = '{{$lbl_usd}}';var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){ $('#pricel').val(price*rated);$('#pricee').val(parseFloat((price*rated)/ratee).toFixed(2));} else{ $('#pricel').val(''); $('#pricee').val(''); }"
							                                    onkeypress="return isNumberKey(event)"/>
														  
											</div>	
													
										<div class="form-group col-md-2">
															<label for="pricee" class="label-size">{{__('Price€')}}</label>
															 <input id="pricee" class="form-control" type="text" name="pricee" 
															    value="{{isset($clinic) ? $clinic->pricee : old('pricee')}}" onkeypress="return isNumberKey(event)"/>
														  
											</div>	
										
										<div class="form-group col-md-3">
												<label for="email" class="label-size">{{__('Email')}}</label>
                                                <input class="form-control" type="text" name="email"
												   value="{{isset($clinic) ? $clinic->email : old('email')}}">											
										</div>
										<div class="form-group col-md-3">
												<label for="fax" class="label-size">{{__('Fax Nb')}}</label>
                                             
												<input class="telephone form-control" type="text" name="fax"
												   value="{{isset($clinic) ? $clinic->fax : old('fax')}}">											
										
										</div>
										<div class="form-group col-md-3">
												<label for="telephone" class="label-size">{{__('Contact Phone')}}</label>
                                                
												<input class="telephone form-control" type="text" name="telephone"
												   value="{{isset($clinic) ? $clinic->telephone : old('telephone')}}">											
											   
										</div>
										<div class="form-group col-md-3">
												<label for="alternate_phone1 " class="label-size">{{__('Alternate Phone1')}}</label>
                                                
												<input class="form-control" type="text" name="alternate_phone1"
												   value="{{isset($clinic) ? $clinic->alternate_phone1 : old('alternate_phone1')}}">											
										</div>
										<div class="form-group col-md-3">
												<label for="alternate_phone2" class="label-size">{{__('Alternate Phone2')}}</label>
                                               
												<input class="form-control" type="text" name="alternate_phone2"
												   value="{{isset($clinic) ? $clinic->alternate_phone2 : old('alternate_phone2')}}">											
										</div>
                                       	
										<div class="form-group col-md-6">
												<label for="address" class="label-size">{{__('Address')}}</label>
                                                <input class="form-control" type="text" name="address"
												   value="{{isset($clinic) ? $clinic->full_address :  old('address') }}">											
										</div>
										<div class="form-group col-md-2">
												<label for="appt_nb" class="label-size" >{{__('Appt.#')}}</label>
                                                <input  class="form-control" name="appt_nb" type="text" value="{{isset($clinic) ? $clinic->appt_nb : old('appt_nb')}}"/>
									
										</div>
										<div class="form-group col-md-3">
												<label for="city_name" class="label-size" >{{__('City Name')}}</label>
                                                <input  class="form-control" name="city_name" type="text" value="{{isset($clinic) ? $clinic->city_name : old('city_name')}}"/>
									
										</div>
										<div class="form-group col-md-3">
												<label for="region_name" class="label-size">{{__('Region Name')}}</label>
												<input  class="form-control" name="region_name" type="text" value="{{isset($clinic) ? $clinic->region_name : old('region_name')}}"/>
										</div>
																				
										<div class="form-group col-md-3">
												<label for="state" class="label-size" >{{__('State')}}</label>
                                                <input  class="form-control" name="state" type="text" value="{{isset($clinic) ? $clinic->state : old('state')}}"/>
									
										</div>
										<div class="form-group col-md-3">
												<label for="zip_code" class="label-size">{{__('Zip code')}}</label>
                                                <input class="form-control" type="text" name="zip_code"
												   value="{{isset($clinic) ? $clinic->zip_code : old('zip_code')}}">											
										</div>
										<div class="form-group col-md-2">
												<label for="province_code" class="label-size">{{__('Province Code')}}</label>
                                                <input class="form-control" type="text" name="province_code"
												   value="{{isset($clinic) ? $clinic->province_code : old('province_code')}}">											
										</div>
										<div class="form-group col-md-2">
												<label for="country_code" class="label-size">{{__('Country Code')}}</label>
                                                <input class="form-control" type="text" name="country_code"
												   value="{{isset($clinic) ? $clinic->country_code : old('country_code')}}">											
										</div>									
										
							           
										<div class="form-group col-md-8">
												<label for="remarks" class="label-size">{{__('Remarks')}}</label>
											    <textarea class="form-control" name="remarks">{{isset($clinic)?$clinic->remarks:old('remarks')}}</textarea>
										</div>
										
                                   </div>
                               </div>
                       
						       
						 </div>
						 </form>
                         <!--end::Form-->
						</div><!--end pane-->
					
               <!--end Clinic profile-->			
                           <div id="account" class="tab-pane">
						     <div class="card">
							   <div class="card-body">
						      
						       <form action="{{isset($clinic) ? route('branches.update',[app()->getLocale(),$clinic->id]):''}}" method="POST"  enctype="multipart/form-data">
							   @csrf
                                @if(isset($clinic))
                                    @method('PUT')
                                @endif
							    <div class="row">
									  @if(!isset($user))
									    <div class="mb-2 col-md-12">
										   <input type="submit" name="update_account" value="{{__('Create account')}}"
                                               class="btn btn-action" {{isset($clinic)? '':'disabled'}}>
									    </div>
										@endif
											<div class="form-group col-md-6">
													<label for="username" class="label-size">{{__('Username').'*'}}</label>
													<div class="input-group">
														<div class="input-group-prepend">
      			                                          <input type="text" autoComplete="false"  name="Prefix_Username" value="{{isset($clinic)?$clinic->id.'_':''}}" class="col-2 form-control" readonly="true"/>
                                                          <input type="text" class="form-control" name="username" value="{{isset($clinic) && isset($user)?substr(strstr($user->username, '_'), strlen('_')):old('username')}}" {{isset($clinic) && !isset($user)? '':'disabled'}}>
													     </div>
													</div>
												
											</div>
											@if(!isset($user))
											<div class="form-group col-md-6">
													<label for="password" class="label-size">{{__('Password').'*'}}</label>
													<input id="password" type="password" class="form-control" name="password"  {{isset($clinic)? '':'disabled'}}>
                                        	    
											</div>
											
											<div class="form-group col-md-6">
														<label for="user_email" class="label-size">{{__('Email')}}</label>
                                                        <input id="user_email" type="text" class="form-control" name="user_email" value="{{isset($clinic)?$clinic->public_email:old('email')}}" {{isset($clinic)? '':'disabled'}}>
											</div>
                                           	@endif									
											<div class="form-group col-md-3">
										      <label for="permission" class="form-label label-size">{{__('Access Permission')}}</label>
											 			<select name="permission" class="form-control" readonly>
														   <option value="S" selected>{{__('Super user')}}</option>
														</select>
												
									        </div>
											<div class="form-group col-md-3">
										      <label for="profile_permission" class="form-label label-size">{{__('Profile Permission')}}</label>
											 			<select name="profile_permission" class="form-control" readonly>
														   <option value="" selected>{{__('Super Admin')}}</option>
														</select>
												
									        </div>
											<div class="col-md-12 text-center form-group">
							                   <h5>{{__('All profile permissions')}}</h5>
						                    </div>
											@foreach($menus as $m)
											<div class="form-group col-md-4">
									          <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;" checked disabled />&#xA0;{{__($m)}}
											</div>
										    @endforeach
									   </div>
									  
							
					               </form>
                                   <!--end::Form-->
								  
								
								</div>
							   </div>	
							</div>
                              
				      <div id="schedule" class="tab-pane">
								  
						   <div class="container-fluid">
							  	<div class="row m-1">
									<div class="col-md-6">
									@if(isset($clinic) && isset($clinic->open_hours))
											   <form class="" action="{{route('branches.destroy',[app()->getLocale(),$clinic->id])}}" method="post" onsubmit="return confirm('{{__('Are you sure?')}}');">
																	@method('DELETE')
																	@csrf
													<button type="submit" class="btn btn-delete" name="destroy_openingHours" >{{__('Delete schedule')}}</button>				
												</form>
										   @endif
									</div>
								 
							  
								
									<div class="col-md-6">
									   <form    id="schedule_form" action="{{isset($clinic) ? route('branches.update',[app()->getLocale(),$clinic->id]):''}}"
									  method="POST"  enctype="multipart/form-data">
									  @csrf
										@if(isset($clinic))
											@method('PUT')
										@endif
									  <button id="clear_schedule" class="m-1 float-right btn btn-reset" {{isset($clinic)?'':'disabled'}}>{{__('Reset')}}</button>
									  <button type="submit" id="save_schedule" name="update_hours"  class="m-1 float-right btn btn-action" {{isset($clinic) && isset($clinic->open_hours)?'':'disabled'}}>{{isset($clinic) && isset($clinic->open_hours)?__('Update schedule'):__('Save schedule')}}</button>
									   										 	
									</div>
								</div>	 
								@php 
								  $week_days=array(0 =>'Monday',1=>'Tuesday',2=>'Wednesday',3=>'Thursday',4=>'Friday',5=>'Saturday',6=>'Sunday');
								@endphp 
                            
							 <div class="m-1 row"> 
							  <div class="col-md-12" style="overflow-x:auto;">
						      <table class="table table-bordered table-sm" style="width:100%;">
							     <tbody>
									  @foreach($week_days as $key => $week_day)
										@if(isset($clinic->open_hours))
										  @php
										  $open_days = json_decode($clinic->open_hours,true);
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
											 <input  class="border-top-0 border-left-0 border-right-0"  id="start_time{{$key}}" name="start_time{{$key}}" 
													   placeholder="{{__('Start Time')}}" type="text" value="@if(isset($clinic->open_hours)){{$from_time}}@endif" style="width:auto;" {{isset($clinic)?'':'disabled'}} data-inpu />

										   
										</div>
										</td>
										<td>
										<div class="col-3">
											<input class="border-top-0 border-left-0 border-right-0" id="end_time{{$key}}" name="end_time{{$key}}" 
													   placeholder="{{__('End Time')}}" type="text" value="@if(isset($clinic->open_hours)){{$to_time}}@endif" style="width:auto;" {{isset($clinic)?'':'disabled'}} data-input />
										  
										</div>
										</td>
									</tr>
                                   @endforeach
										</tbody>
										</table>
									</div>	
							   </div>
                            </form>
					     </div>				    				         
                    </div>
               </div>
			</div>
        </div>
</div>		
    <!-- end:: Content -->
	
			
@endsection

@section('scripts')
 @if ($errors->has('name'))
  <script>
   Swal.fire({"title":"Error", 
              "text":"{{$errors->first('name')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('pricel'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('pricel')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('priced'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('priced')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('pricee'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('pricee')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>	  
 @elseif ($errors->has('telephone'))
  <script>
  Swal.fire({
			 "text":"{{$errors->first('telephone')}}",
			 "icon":"error",
			 "customClass": "w-auto"});
  </script>
 @elseif ($errors->has('alternate_phone1'))
  <script>
  Swal.fire({
			 "text":"{{$errors->first('alternate_phone1')}}",
			 "icon":"error",
			 "customClass": "w-auto"});
  </script>
@elseif ($errors->has('alternate_phone2'))
  <script>
  Swal.fire({
			 "text":"{{$errors->first('alternate_phone2')}}",
			 "icon":"error",
			 "customClass": "w-auto"});
  </script>  
 @elseif ($errors->has('fax'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('fax')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('email'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('email')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script> 
 @elseif ($errors->has('region_name'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('region_name')}}",
              "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('city_name'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('city_name')}}",
              "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('category'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('category')}}",
              "icon":"error",
			  "customClass": "w-auto"});
  </script>
  
 @endif
 
 
 @if ($errors->has('username'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('username')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('password'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('password')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('user_email'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('user_email')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @endif
  
<script>
$(document).ready(function() {
   $('[name=user_email]').inputmask("email");
   $('[name=email]').inputmask("email");
   var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
    $('.telephone').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
		});
   
   $('.doc-select2').select2();
   
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
		
	for(let i=0;i<=6;i++){
		flatpickr('#start_time'+i, {
				
				enableTime: true,
				time_24hr: true,
				minTime: '08:00',
				noCalendar: true,
				dateFormat: "H:i",
				
			});
		
		flatpickr('#end_time'+i, {
				
				enableTime: true,
				time_24hr: true,
				minTime: '08:00',
				noCalendar: true,
				dateFormat: "H:i"
				
			});
	       }
			
   
   
 });
</script>
<script>
$(function(){
		
		
		for(let i=0;i<=6;i++){
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
     
	 $('#clear_schedule').off().on('click',function(e){
		e.preventDefault();
		$('#schedule_form').trigger("reset");
		$('#save_schedule').prop("disabled", true);
		/*for(let i=0;i<=6;i++){
			$('#start_time'+i).val('');
			$('#end_time'+i).val('');
		}*/
	 });
	 
	 
});  
	  
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

