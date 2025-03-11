@extends('gui.main_gui')

@section('content')	
<div class="container-fluid mt-1">	
			<div class="row mt-1">
				
				<section class="col-md-12 p-0"> 
					<div  class="card card-outline m-1">
						<div class="card-header text-white ">
							 <div class="card-title"><h5>{{__('New Request').'-'.$clinics->full_name}}</h5></div>
							 <div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								  </button>
							 </div> 
						</div> 
						<div class="card-body p-1">
						 <form  id="NewVisitForm">
					    
						    <div class="row">
									<!--<div class="col-md-3 col-6">
										<label for="clinic_name" class="label-size ">{{__('Lab Name')}}</label>
										<input type="text" class="form-control" name="clinic_name" readonly="true" value="{{$clinics->full_name}}"/>
								    </div>-->
								
							  
							   <!--<div class="col-md-4 col-6">
									<label for="patient_tel" class="label-size ">{{__('Landline Phone')}}</label>
									<select class="select2_data custom-select rounded-0" name="patient_tel"   id="patient_tel" style="width:100%;">
										 <option value="0">{{__('Choose a landline phone')}}</option>
										 	@foreach($Patients as $pat)
											   @if($pat->first_phone!='' && isset($pat->first_phone))	
												 <option value="{{$pat->id}}">{{'#'.$pat->id.' , '.$pat->first_phone}}</option>
											   @endif
											@endforeach
											
									</select>
								</div>-->
								<!--<div class="col-md-4">
									
									<label for="patient_cell" class="label-size m-0">{{__('Cellular Phone')}}</label>
									<select class="select2_data custom-select rounded-0" name="patient_cell"   id="patient_cell" style="width:100%;">
										 <option value="0">{{__('Choose a cellular phone')}}</option>
										 	@foreach($Patients as $pat)
												@if($pat->cell_phone!='' && isset($pat->cell_phone))
												 <option value="{{$pat->id}}">{{'#'.$pat->id.' , '.$pat->cell_phone}}</option>
											    @endif
											@endforeach
											
									</select>
								</div>-->
								
							   <div class="col-md-8">
									<label for="patient" class="m-0 label-size ">{{__('Patient')}}
									 <input type="hidden" id="clinic_req" name="clinic_req" value="{{$clinics->id}}"/>
									 <span><button type="button" id="create_pat_data"  class="btn btn-icon btn-sm" style="line-height:1;" title="{{__('new patient')}}"><i class="fa fa-plus text-muted"></i></button></span>
									 <span><button type="button" id="edit_pat_data" class="btn btn-icon btn-sm" style="line-height:1;" title="{{__('edit patient')}}"><i class="fa fa-edit text-muted"></i></button></span>
									</label>
									<select class="select2_filter_patient custom-select rounded-0" name="patient"   id="selectpatient" style="width:100%;">
										 
									</select>
								</div>
								 <!--<div class="col-md-4 col-6">
									<label for="patient" class="label-size ">{{__('External Lab/Doctor')}}</label>
									@php $disabled = auth()->user()->type==2?'':'disabled'; @endphp
									<select class="select2_data custom-select rounded-0" name="ext_lab_num"   id="ext_lab_num" style="width:100%;" {{$disabled}}>
										   @if(auth()->user()->type==2)
												<option value="0">{{__('Choose an external lab/doctor')}}</option>
											@endif
										 	@foreach($ext_labs as $l)
												<option value="{{$l->user_num}}">{{$l->full_name}}</option>
											@endforeach
											
									</select>
								</div>-->
								<div class="col-md-4">
									    <label class="label-size m-0">{{__('Date/Time')}}</label>
										<div class="input-group">
											<input  autocomplete="false" placeholder="{{__('Date/Time')}}" type="text" class="form-control"  name="date_visit" id="date_visit" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
											<button  id="NewVisit" class="ml-1 btn btn-action btn-md btn-icon" title="{{__('Create')}}" style="font-size:0.9em;border-radius:50%;">{{__('Lab Request')}}<i class="ml-1 fa fa-plus"></i></button>
										</div>
									
							   </div>
							</div>   
                         </form>						 
						</div>
					</div>	
				</section>
				<section class="col-md-12 p-0"> 
				   <div class="card card-outline m-1">
						<div class="card-header">
							 <div class="card-title"><h5>{{__('Filter Lab Requests')}}</h5></div>
							 <div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								  </button>
							 </div> 
						</div> 
						<div class="card-body p-0">
						  
							<div class="form-row m-1">			  
							    <div class="mb-1 col-md-3 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('From date')}}" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"
									value="{{(request()->session()->has('from_date'))?request()->session()->get('from_date'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								<div class="mb-1 col-md-3 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('To date')}}" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('Choose a date')}}"
									value="{{(request()->session()->has('to_date'))?request()->session()->get('to_date'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
							   <div class="mb-1 col-md-3 col-6">
                                    <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									<select class="custom-select rounded-0" name="filter_status"  id="filter_status" style="width:100%;">
										<option value="Y" {{request()->session()->has('status') && request()->session()->get('status')=='O'?'selected':'' }}>{{__("Active")}}</option>
										<option value="N"  {{request()->session()->has('status') && request()->session()->get('status')=='N'?'selected':'' }}>{{__("Cancelled")}}</option>
									</select>									
								</div>
							  
								<div class="mb-1 col-md-3 col-6">
                                    <!--<label for="patient" class="label-size ">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
											
									</select>									
								</div>
							
								<div class="col-md-3 col-6">
									<!--<label for="filter_patient_tel" class="label-size ">{{__('Phone')}}</label>-->
									<input type="tel" class="form-control" placeholder="{{__('Fill a phone')}}" name="filter_patient_tel" id="filter_patient_tel" value="{{request()->session()->has('filter_patient_tel') && request()->session()->get('filter_patient_tel')!=''?request()->session()->get('filter_patient_tel'):old('filter_patient_tel')}}" onkeypress="return isNumberKey(event)"/>
								</div>
								
									<div class="col-md-3 col-6">
										<!--<label for="filter_test_codes" class="label-size ">{{__('Lab Code')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_test_codes"  id="filter_test_codes" style="width:100%;">
								               <option value="0">{{__('Choose a code')}}</option>
											   @foreach($lab_tests as $t)
											      <option value="{{$t->id}}"  {{request()->session()->has('lab_code') && request()->session()->get('lab_code')==$t->id?'selected':'' }}>{{$t->test_name}}</option>
											   @endforeach
										</select>
							        </div>						
									<div class="col-md-3 col-6">
										<!--<label for="filter_doctor" class="label-size ">{{__('External lab/doctor')}}</label>-->
									    @php $disabled = auth()->user()->type==2?'':'disabled'; @endphp
										<select class="select2_data custom-select rounded-0" name="filter_ext_lab"  id="filter_ext_lab" style="width:100%;" {{$disabled}}>
												
												@if(auth()->user()->type==2)
												 <option value="0">{{__('Guarantor/Doctor')}}</option>
											     <option value="-1">{{$clinics->full_name}}</option>
											    @endif
												@foreach($ext_labs as $l)
													<option value="{{$l->user_num}}" {{request()->session()->has('ext_lab') && request()->session()->get('ext_lab')==$l->user_num?'selected':'' }}>{{$l->full_name}}</option>
												@endforeach
										</select>
							        </div>
                                	<div class="col-md-3 col-6">
										<!--<label for="filter_order" class="label-size ">{{__('Result status')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_order"  id="filter_order" style="width:100%;">
								               @if(auth()->user()->type==2)
											   <option value="0">{{__('Result Status')}}</option>
										       <option value="NF">{{__('Not Filled')}}</option>
											   <option value="P">{{__('Pending')}}</option>
											   <option value="F">{{__('Finished')}}</option>
											   <option value="V">{{__('Validated')}}</option>
											   @else
												 <option value="0">{{__('Pending/Validated')}}</option>
											     <option value="P">{{__('Pending')}}</option>
											     <option value="V">{{__('Validated')}}</option>  
											   @endif 	   
										</select>
							        </div>								
								
									
							</div>
						  
						</div>
					</div>
				</section>
                <section class="col-md-12 p-0">
				    <div class="card card-outline m-1"> 
						<div class="card-header text-white ">
							 <div class="card-title"><h5>{{__("Lab Requests History")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body p-0">	
							<div class="row m-1">
							   <div class="col-md-12">
								 <table id="visit_table" class="table table-bordered table-hover stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('Request Nb')}}</th>
									<th class="all">{{__('Status')}}</th>
									<th class="all">{{__('Appointment')}}</th>
							        <th class="all">{{__('Patient')}}</th>
									<th class="all">{{__('Codes')}}</th>
									<th class="all">{{__('Guarantor/Doctor')}}</th>
									<th class="all ">{{__('My Lab')}}</th>
									<th class="all">{{__('Actions')}}</th>
									</tr>
									</thead>
									<tbody>
									  
									</tbody>
								</table>
							</div>
						   </div>
						</div>
                    </div>				
                </section> 				
        </div>   				
							
</div>										
@include('patients_list.patientModal')										
@endsection	
@section('scripts')
<script>
$(document).ready(function(){
	var phones = [{ "mask": "##-###-###"}, { "mask": "##-###-###"}];
    $('#filter_patient_tel').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
	
	
	$('#date_visit').flatpickr({
		allowInput: true,
		enableTime: true,
        dateFormat: "Y-m-d H:i",
        disableMobile: true
	});
    
	$('#filter_fromdate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
        disableMobile: true
	});
	var min_date = $('#filter_fromdate').val();
	$('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
		minDate: $('#filter_fromdate').val(),
        dateFormat: "Y-m-d",
        disableMobile: true
	});
		
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	$('.select2_filter_patient').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Choose a patient')}}",
		ajax: {
			url: '{{route("patient.loadPat",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    clinic_num:'{{$clinics->id}}',
					status:'O',
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
	  
	  $('.select2_filter_patient1').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Choose a patient')}}",
		ajax: {
			url: '{{route("patient.loadPat",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    clinic_num:'{{$clinics->id}}',
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
		
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	$('#visit_table').DataTable({
					stateSave: true,
					stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[2,'desc']],
					scrollY: "450px",
			        scrollX: true,
			        scrollCollapse:true,
			        ajax: {
						url: "{{ route('lab.visit.index',app()->getLocale()) }}",
					    data: function (d) {
								
								 d.clinic_num = $('#filter_branch').val();
								 d.ext_lab = $('#filter_ext_lab').val();		
								 d.filter_patient=$('#filter_patient').val();
								 d.filter_fromdate=$('#filter_fromdate').val();
								 d.filter_todate=$('#filter_todate').val();
								 d.filter_status=$('#filter_status').val();
								 d.filter_order=$('#filter_order').val();
								 d.filter_patient_tel=$('#filter_patient_tel').val();
								 d.filter_test_codes=$('#filter_test_codes').val();
							}
					},
						 
					columns: [

						{data: 'order_id'},
						{data:'order_status'},
						{data: 'visit_date_time'},
					    {data: 'patDetail',render: function(data, type, row) {
						   if(data !='' && data != null){	
							var pat = data.split(',');
							var dob = (pat[1]==null || pat[1]=='')?'':'<br/>'+'{{__("DOB")}}'+': '+pat[1];
							var tel1 = (pat[2]==null || pat[2]=='')?'':'<br/>'+'{{__("Cellular Phone")}}'+': '+pat[2];
							return '{{__("Name")}}'+': '+pat[0]+dob+tel1;
						   }else{
							   return data;
						   }
						 }
						},
						{data:'test_names'},
						{data:'ext_lab_name'},
						{data: 'ClinicName'},
						{data: 'action',orderable: false, searchable: false},

					],
					
					language :{
					       
							search:         "{{__('Search')}}&nbsp;:",
							lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
							info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
							infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
							zeroRecords:    "{{__('No data is found')}}",
							emptyTable:     "{{__('No data is found')}}",
							buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
						
								},
							paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				         },
					fixedColumns:   {
				left: 0,
				right: 1
				}
					

				});	
	
	
	
	
});

$(function(){

$('#create_pat_data').off().on('click',function(e){
	e.preventDefault();
	var patient = $('#selectpatient').val();
	var clinic = $('#clinic_req').val();
	$.ajax({
		url:'{{route("lab.visit.pat_data",app()->getLocale())}}',
		type:'POST',
		data:{patient:patient,clinic:clinic,_token:'{{csrf_token()}}',req_type:'newData'},
		dataType:'JSON',
		success:function(data){
			$('#patientModal div.modal-body').html(data.html);
			$('#patientModal').find('#update_patient').text('Save');
			$('#patientModal').find('#req_type').val('new');
			$('#patientModal').modal('show');
		}
	});
});

$('#edit_pat_data').off().on('click',function(e){
	e.preventDefault();
	var patient = $('#selectpatient').val();
	if(patient=="0" || patient==null){
	   Swal.fire({text:'{{__("Please choose your patient")}}',icon:"error",customClass:"w-auto"});
	   return false;
   }
   
	var clinic = $('#clinic_req').val();
	$.ajax({
		url:'{{route("lab.visit.pat_data",app()->getLocale())}}',
		type:'POST',
		data:{patient:patient,clinic:clinic,_token:'{{csrf_token()}}',req_type:'editData'},
		dataType:'JSON',
		success:function(data){
			$('#patientModal div.modal-body').html(data.html);
			$('#patientModal').find('#update_patient').text('Update');
			$('#patientModal').find('#req_type').val('update');
			$('#patientModal').modal('show');
		}
	});
});


$('#NewVisit').off().on('click',function(e){
   e.preventDefault();
   localStorage.clear();
   var patient = $('#selectpatient').val();
   var date_visit=$('#date_visit').val();
   
   if(patient=="0" || patient==null){
	   Swal.fire({text:'{{__("Please choose your patient")}}',icon:"error",customClass:"w-auto"});
	   return false;
   }
   
   if(date_visit==""){
	   Swal.fire({text:'{{__("Please choose a date")}}',icon:"error",customClass:"w-auto"});
	   return false;
   }
   

$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
   $.ajax({
		 url: '{{route("lab.visit.create",app()->getLocale())}}',
		 data: {patient:patient,date_visit:date_visit,clinic:$('#clinic_req').val(),ext_lab_num:$('#ext_lab_num').val()},
		 type: 'post',
		 dataType: 'json',
		 success: function(data){
				if(data.success){
					window.location.href=data.location;
				}
			 }
		});

});

$('#patient_tel').change(function(){
	var id = $(this).val();
	$('#selectpatient').val(id);
	if($("#patient_cell option[value='"+id+"']").length > 0)
	{ 
       $('#patient_cell').val(id);
	}else{
		$('#patient_cell').val('0');
	}
	$('.select2_data').trigger('change.select2');
});

$('#patient_cell').change(function(){
	var id = $(this).val();
	$('#selectpatient').val(id);
	if($("#patient_tel option[value='"+id+"']").length > 0)
	{ 
       $('#patient_tel').val(id);
	}else{
		$('#patient_tel').val('0');
	}   	
	$('.select2_data').trigger('change.select2');
});

$('#selectpatient').change(function(){
	var id = $(this).val();
	if($("#patient_cell option[value='"+id+"']").length > 0)
	{ 
       $('#patient_cell').val(id);
	}else{
		$('#patient_cell').val('0');
	}

   if($("#patient_tel option[value='"+id+"']").length > 0)
	{ 
       $('#patient_tel').val(id);
	}else{
		$('#patient_tel').val('0');
	}   	
	
	$('.select2_data').trigger('change.select2');
});

$('#filter_fromdate').change(function(){
	var from_date = $('#filter_fromdate').val();
	
	if(from_date!=null && from_date!=''){
	
	//$('#filter_todate').removeAttr('disabled');
	//$('#filter_todate').val('');
	$('#filter_todate').flatpickr().destroy();	
	$('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		minDate: from_date ,
        disableMobile: true
	});
	}else{
	   //$('#filter_todate').val('');
	   //$('#filter_todate').attr('disabled',true);
       $('#filter_todate').flatpickr().destroy();	
	   $('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		disableMobile: true
	   });
	}
	$('#visit_table').DataTable().ajax.reload();
});	

$('#filter_patient_tel').on('input',function(){
	$('#visit_table').DataTable().ajax.reload();
});

$('#filter_todate,#filter_patient,#filter_status,#filter_ext_lab,#filter_order,#filter_test_codes').change(function(){
	$('#visit_table').DataTable().ajax.reload();
});

$('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
		   $.ajax({
				 url: '{{route("lab.visit.destroy",app()->getLocale())}}',
				 data: {id:id,type:type},
				 type: 'post',
				 dataType: 'json',
				 success: function(data){
						
						Swal.fire({title:data.msg,icon:"success",toast:true,showConfirmButton:false,position:"bottom-right",timer: 3000});
						$('#visit_table').DataTable().ajax.reload();
					 }
				});
});


$('#patientModal').on('show.bs.modal',function(){
	 $('#patientModal').find('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
     $('#patientModal').find('[name=email]').inputmask("email");
   
    var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
   $('#patientModal').find('#home_phone').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
   
   $('#patientModal').find('#cell_phone').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });		
	
  flatpickr('#patientModal #birthdate', {
            allowInput : true,
			altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d",
        });
  
  $('#patientModal').find('input[name="chkSms"]').change(function(){
		  var phone=$('#cell_phone').val();
         
		  if($('input[name="chkSms"]').is(':checked')){
			   
			  if(phone == "" || phone == null ) {
				  $('input[name="chkSms"]').prop('checked',false);
				 Swal.fire({text:'{{__("You must enter a cellular phone number")}}',icon:'error',customClass:'w-auto'});
				  }else{
		  
					  const re = /^[0-9]{2}-[0-9]{3}-[0-9]{3}$/;
					  const ok = re.exec(phone);
					  if(!ok) {
							$('input[name="chkSms"]').prop('checked',false);
							Swal.fire({text:'{{__("Enter cell phone number in this format: 11-111-111")}}',icon:'error',customClass:'w-auto'});
						}	
				  }	
		  
		       }		  
			
		  }); 
	  
  $('#patientModal').find('input[name="chkEmail"]').change(function()
        {
			var mail=$('#email').val();
         
		  if($('input[name="chkEmail"]').is(':checked')){
			   
			  if(mail == "" || mail == null ) {
				  $('input[name="chkEmail"]').prop('checked',false);
				 Swal.fire({text:'{{__("Please enter a valid e-mail")}}',icon:'error',customClass:'w-auto'});
				  }else{
		  
					  const re =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
					  const ok = re.exec(mail);
					  if(!ok) {
							$('input[name="chkEmail"]').prop('checked',false);
							Swal.fire({text:'{{_("Please enter a valid e-mail")}}',icon:'error',customClass:'w-auto'});
						}	
				  }	
		  
		       }		  
				
		}); 

$('#patientModal').find('#update_patient').off().on('click',function(e){
	 e.preventDefault();
	 
		 var clinic = $('#patientModal').find('#clinic_num').val();
		 if(clinic==''){
		  Swal.fire({text:"{{__('Please choose a lab')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		 var gender=$('#patientModal').find('#gender').val();
		 if(gender==''){
		  Swal.fire({text:"{{__('Please input patient gender')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		 var dob = $('#patientModal').find('#birthdate').val();
		 if(dob==''){
		  Swal.fire({text:"{{__('Please input patient birth date')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		 var fname=$('#patientModal').find('#first_name').val();
		  if(fname==''){
		  Swal.fire({text:"{{__('Please input patient first name')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		  var mname=$('#patientModal').find('#middle_name').val();
		  if(mname==''){
		  Swal.fire({text:"{{__('Please input patient middle name')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		 var lname=$('#patientModal').find('#last_name').val();
		 if(lname==''){
		  Swal.fire({text:"{{__('Please input patient last name')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		
		 
		 var home_phone=$('#patientModal').find('#home_phone').val();
		  if(home_phone==''){
		    
		  }else{
			 if(!home_phone.match(/^[0-9]{2}-[0-9]{6}$/)){
				 Swal.fire({text:"{{__('Enter a landline phone number in this format: 00-000000')}}",icon:"error",customClass:"w-auto"});
				 return false; 
			 }
		 }
		 
		  var cell_phone=$('#patientModal').find('#cell_phone').val();
		  if(cell_phone==''){
		    
		  }else{
			 if(!cell_phone.match(/^[0-9]{2}-[0-9]{6}$/)){
				 Swal.fire({text:"{{__('Enter a cellular phone number in this format: 00-000000')}}",icon:"error",customClass:"w-auto"});
				 return false; 
			 }
		 }
		
		 $.ajax({
			 url : "{{route('lab.visit.pat_data',app()->getLocale())}}",
			 type: "POST",
			 data : $('#patientModal').find('#patient_form').serialize(),
			 dataType: "JSON",
			 success: function(data){
				if(data.error){
					Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});
				} 
				if(data.success){
					   
					Swal.fire({title:data.success,icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
				    $('#patientModal').modal('hide');
					var selectPatient = $('.select2_filter_patient');
					// create the option and append to Select2
                    var option = new Option(data.patient_data, data.patient_id, true, true);
                    selectPatient.append(option).trigger('change');
                    // manually trigger the `select2:select` event
                    selectPatient.trigger({
                      type: 'select2:select',
                      params: {
                         data: data
                         }
                      });
					
				} 
			 }
		   });	
		 
	
   });
		
});



});//end scripts functions	


</script>
<script>
function printPDF(order_id){
	$.ajax({
			url:'{{route("lab.visit.printResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download= 'Request#'+order_id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
}
function sendPDF(order_id){
	$.ajax({
			url:'{{route("lab.visit.sendResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		   dataType:'json',
		   success: function(data){
					if(data.success){	
						Swal.fire({title:data.success,icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.warn){	
						Swal.fire({title:data.warn,icon:"warning",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.error){
						 Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
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
<script>
function loadMore(el) {
            var contentContainer = $(el).closest('.content-container');
            contentContainer.find('.full-content').show();
            contentContainer.find('.truncated-content').hide();
            contentContainer.find('.load-more-btn').hide();
            contentContainer.find('.load-less-btn').show();
        }

 function loadLess(el) {
 		    var contentContainer = $(el).closest('.content-container');
            contentContainer.find('.full-content').hide();
            contentContainer.find('.truncated-content').show();
            contentContainer.find('.load-more-btn').show();
            contentContainer.find('.load-less-btn').hide();
        }
</script>
@endsection	