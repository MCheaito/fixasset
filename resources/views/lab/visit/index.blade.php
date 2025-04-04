@extends('gui.main_gui')
@section('styles')
<style>
.dt-button {
            background-color: #f0f0f0; 
            position: relative;
            display: inline-block;
            box-sizing: border-box;
		    margin-left: 0.167em;
		    margin-right: 0.167em;
		    margin-bottom: 0.333em;
		    padding: 0.5em 1em;
		    border: 1px solid rgba(0, 0, 0, 0.3);
		    border-radius: 2px;
		    cursor: pointer;
		    font-size: 0.88em;
		    line-height: 1.6em;
		    color: inherit;
		    white-space: nowrap;
		    overflow: hidden;
		    background-color: rgba(0, 0, 0, 0.1);
		    background: linear-gradient(to bottom, rgba(230, 230, 230, 0.1) 0%, rgba(0, 0, 0, 0.1) 100%);
		   -webkit-user-select: none;
		   -moz-user-select: none;
		   -ms-user-select: none;
		   user-select: none;
		   text-decoration: none;
		   outline: none;
           text-overflow: ellipsis;
        }

      
</style>
@endsection
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
								
								
							   <div class="col-md-9">
									<label for="patient" class="m-0">{{__('Patient')}}
									 <input type="hidden" id="clinic_req" name="clinic_req" value="{{$clinics->id}}"/>
									 <span><button type="button" id="create_pat_data"  class="p-1 btn btn-icon btn-md"  title="{{__('Create new patient')}}"><i class="fa fa-plus text-muted"></i></button></span>
									 <span><button type="button" id="edit_pat_data" class="p-1 btn btn-icon btn-md"  title="{{__('Edit patient')}}"><i class="fa fa-edit text-muted"></i></button></span>
									</label>
									<select class="select2_filter_patient custom-select rounded-0" name="patient"   id="selectpatient" style="width:100%;">
										 
									</select>
								</div>
								 
								<div class="mt-2 col-md-3">
									    <label class="label-size m-0">{{__('Date/Time')}}</label>
										<div class="input-group">
											<input  autocomplete="false" placeholder="{{__('Date/Time')}}" type="text" class="form-control"  name="date_visit" id="date_visit" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
											<button  id="NewVisit" class="ml-1 btn btn-action btn-md btn-icon" title="{{__('Create')}}" style="font-size:0.9em;border-radius:50%;">{{__('Request')}}<i class="ml-1 fa fa-plus"></i></button>
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
							    <div class="mb-1 col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('From date')}}" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"
									value="{{(request()->session()->has('from_date'))?request()->session()->get('from_date'):Carbon\Carbon::now()->subDays(60)->format('Y-m-d')}}"/>
								</div>
								<div class="mb-1 col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('To date')}}" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('Choose a date')}}"
									value="{{(request()->session()->has('to_date'))?request()->session()->get('to_date'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
							   <div class="mb-1 col-md-2 col-6">
                                    <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									<select class="custom-select rounded-0" name="filter_status"  id="filter_status" style="width:100%;">
										<option value="Y" {{request()->session()->has('status') && request()->session()->get('status')=='Y'?'selected':'' }}>{{__("Active")}}</option>
										<option value="N"  {{request()->session()->has('status') && request()->session()->get('status')=='N'?'selected':'' }}>{{__("Cancelled")}}</option>
									</select>									
								</div>
								<div class="col-md-2 col-6">
									<!--<label for="filter_patient_tel" class="label-size ">{{__('Phone')}}</label>-->
									<input type="tel" class="form-control" placeholder="{{__('Fill a phone')}}" name="filter_patient_tel" id="filter_patient_tel" value="{{request()->session()->has('filter_patient_tel') && request()->session()->get('filter_patient_tel')!=''?request()->session()->get('filter_patient_tel'):old('filter_patient_tel')}}" onkeypress="return isNumberKey(event)"/>
								</div>
							  
								<div class="mb-1 col-md-4 col-6">
                                    <!--<label for="patient" class="label-size ">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
											
									</select>									
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
										<select class="select2_data_grntr custom-select rounded-0" name="filter_ext_lab"  id="filter_ext_lab" style="width:100%;">
											
										</select>
							        </div>
									<div class="col-md-3 col-6">
										<select class="select2_data_doc custom-select rounded-0" name="filter_doctor_num"  id="filter_doctor_num" style="width:100%;">
											
										</select>
							        </div>
                                	<div class="col-md-3 col-6">
										<!--<label for="filter_order" class="label-size ">{{__('Result status')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_order"  id="filter_order" style="width:100%;">
								                 <option value="0" >{{__('Choose a status')}}</option>
												 <!--<option value="I"  {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='I'?'selected':'' }}>{{__('In Process(Guarantor)')}}</option>-->
												 <option value="NV" {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='NV'?'selected':'' }}>{{__('Results to Check')}}</option>
												 <option value="NVF" {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='NVF'?'selected':'' }}>{{__('Finished Results to Check')}}</option>
												 <option value="P"  {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='P'?'selected':'' }}>{{__('Pending')}}</option>
											     <option value="F"  {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='F'?'selected':'' }}>{{__('Finished')}}</option>
											     <option value="V"  {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='V'?'selected':'' }}>{{__('Validated')}}</option>
											  	 <option value="NF" {{request()->session()->has('filter_result_state') && request()->session()->get('filter_result_state')=='NF'?'selected':'' }}>{{__('Not Filled')}}</option>
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
								<button type="button" id="refresh_button" class="btn btn-sm btn-resize"><i class="fas fa-sync-alt"></i></button>
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
									<th class="all" style="display:none;">{{__('#')}}</th>
									<th class="all">{{__('Request Nb')}}</th>
							        <th class="all">{{__('Patient')}}</th>
									<th class="all">{{__('Guarantor')}}</th>
									<th class="all">{{__('Status')}}</th>
									<th class="all">{{__('Appointment')}}</th>
									<th class="all">{{__('Sent Status')}}</th>
									<th class="all">{{__('Codes')}}</th>
									<th class="all">{{__('Doctor')}}</th>
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
@include('lab.visit.orders.codesModal')										
@endsection	
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('patientModal');
  $('#patientModal').on('shown.bs.modal', function () {
    const form = modal.querySelector('#patient_form');
    modal.addEventListener('keypress', function(e) {
      const target = e.target;
      if (e.key === 'Enter') {
        e.preventDefault(); // Prevent form submission
        
        // Find next focusable element
        const focusableElements = Array.from(form.querySelectorAll('input, textarea, select, button, [tabindex]:not([tabindex="-1"])'));
        const currentIndex = focusableElements.indexOf(target);
        const nextIndex = (currentIndex + 1) % focusableElements.length;
        const nextElement = focusableElements[nextIndex];
        if (nextElement) {
          nextElement.focus();
        }
      }
    });
  });
});
</script>
<script>
$(document).ready(function(){
	var phones = [{ "mask": "##-###-###"}, { "mask": "##-###-###"}];
    $('#filter_patient_tel').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
	
	var currentYear = new Date().getFullYear();
    var maxDate = new Date(currentYear, 11, 31); // Month is zero-based, so 11 represents December
    var maxDateString = maxDate.toISOString().slice(0, 10);
	
	$('#date_visit').flatpickr({
		allowInput: true,
		enableTime: true,
		maxDate: maxDateString,
        altInput: true,
	    altFormat: "d/m/Y H:i",
		dateFormat: "Y-m-d H:i",
		time_24hrs: false,
        disableMobile: true
	});
    
	$('#filter_fromdate').flatpickr({
		allowInput: true,
		enableTime: false,
        maxDate: maxDateString,
		altInput: true,
	    altFormat: "d/m/Y",
		dateFormat: "Y-m-d",
		disableMobile: true
	});
	var min_date = $('#filter_fromdate').val();
	$('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
		minDate: $('#filter_fromdate').val(),
		maxDate: maxDateString,
		altInput: true,
	    altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        disableMobile: true
	});
		
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	$('.select2_filter_patient').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Please choose a patient from the list or Create a new one')}}",
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
		minimumInputLength: 0,
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
	  
	  var selectedPatId = localStorage.getItem('selectedPatId');
      var selectedPatText = localStorage.getItem('selectedPatText');
      
	  if (selectedPatId && selectedPatText) {
        // Create the option manually and select it
        var newOption = new Option(selectedPatText, selectedPatId, true, true);
        $('.select2_filter_patient1').append(newOption).trigger('change');
       }
	  
	  $('.select2_filter_patient1').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('selectedPatId', selectedData.id);
        localStorage.setItem('selectedPatText', selectedData.text);
        });
		
	  $('.select2_filter_patient1').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('selectedPatId');
          localStorage.removeItem('selectedPatText');
           });	
	  
	  
	  $('.select2_data_doc').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Choose a doctor')}}",
		minimumInputLength: 0,
		ajax: {
			url: '{{route("loadDoctors",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
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
	  
	  var selectedDocId = localStorage.getItem('selectedDocId');
      var selectedDocText = localStorage.getItem('selectedDocText');
      
	  if (selectedDocId && selectedDocText) {
        // Create the option manually and select it
        var newOption = new Option(selectedDocText, selectedDocId, true, true);
        $('.select2_data_doc').append(newOption).trigger('change');
       }
	  
	  $('.select2_data_doc').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('selectedDocId', selectedData.id);
        localStorage.setItem('selectedDocText', selectedData.text);
        });
		
	  $('.select2_data_doc').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('selectedDocId');
          localStorage.removeItem('selectedDocText');
           });	
	  
	  
	  
	  
	  $('.select2_data_grntr').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Choose a guarantor')}}",
		ajax: {
			url: '{{route("loadGuarantors",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    status:'A',
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
		
	  var selectedGRNTRId = localStorage.getItem('selectedGRNTRId');
      var selectedGRNTRText = localStorage.getItem('selectedGRNTRText');
      
	  if (selectedGRNTRId && selectedGRNTRText) {
        // Create the option manually and select it
        var newOption = new Option(selectedGRNTRText, selectedGRNTRId, true, true);
        $('.select2_data_grntr').append(newOption).trigger('change');
       }
	  
	  $('.select2_data_grntr').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('selectedGRNTRId', selectedData.id);
        localStorage.setItem('selectedGRNTRText', selectedData.text);
        });
		
	  $('.select2_data_grntr').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('selectedGRNTRId');
          localStorage.removeItem('selectedGRNTRText');
           });	
	
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	$('#visit_table').DataTable({
					
					processing: false,
                    searching: true,
					serverSide: true,
					dom: "<'row'<'col-sm-3'l><'col-sm-3'B><'text-right col-sm-6'f>>" + 
						 "<'row'<'col-sm-12'tr>>" + 
						 "<'row'<'col-sm-5'i><'col-sm-7'p>>",
					buttons: [
						{ extend: 'excel', className: 'dt-button' },
						{ extend: 'csv', className: 'ml-1 dt-button' },
						{ extend: 'print', className: 'ml-1 dt-button' }
					],
                    order : [[1,'desc']],
					pageLength: 50,
					lengthMenu: [
					[50, 75, 100, -1],
					[50, 75, 100, 'All']
					 ],
					info: true,
					scrollY: 480,
			        scrollX: true,
			        scrollCollapse:true,
			        ajax: {
						url: "{{ route('lab.visit.index',app()->getLocale()) }}",
					    data: function (d) {
								
								 d.clinic_num = $('#filter_branch').val();
								 d.ext_lab = $('#filter_ext_lab').val();
								 d.doctor_num = $('#filter_doctor_num').val();
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

						{data: 'order_id',visible:false},
						{data: 'request_nb'},
						{data: 'pat_details'},
						{data:'ext_lab_name'},
						{data:'color_status'},
						{data: 'visit_date_time'},
						{data: 'sent_result_details',orderable: false, searchable: false},
					    {data:'test_names'},
						{data:'doctor_name'},
						{data: 'ClinicName'},
						{data: 'action',orderable: false, searchable: false}

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

/*$('#patient_tel').change(function(){
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
});*/

$('#filter_fromdate').change(function(){
	var from_date = $('#filter_fromdate').val();
	var currentYear = new Date().getFullYear();
    var maxDate = new Date(currentYear, 11, 31); // Month is zero-based, so 11 represents December
    var maxDateString = maxDate.toISOString().slice(0, 10);
	
	if(from_date!=null && from_date!=''){
	
	$('#filter_todate').flatpickr().destroy();	
	$('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
		altInput: true,
	    altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
		minDate: from_date ,
		maxDate: maxDateString,
        disableMobile: true
	});
	}else{
	
       $('#filter_todate').flatpickr().destroy();	
	   $('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
		maxDate: maxDateString,
		altInput: true,
	    altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
		disableMobile: true
	   });
	}
	$('#visit_table').DataTable().ajax.reload(null, false);
});	

$('#filter_patient_tel').on('input',function(){
	$('#visit_table').DataTable().ajax.reload(null, false);
});

$('#filter_todate,#filter_patient,#filter_status,#filter_ext_lab,#filter_order,#filter_test_codes,#filter_doctor_num').change(function(){
	$('#visit_table').DataTable().ajax.reload(null, false);
});

$('#refresh_button').click(function(e){
	 e.preventDefault();
	 $('#visit_table').DataTable().ajax.reload(null,false);
	 Swal.fire({toast:true,title:'{{__("Refreshed successfully")}}',icon:'success',position:'bottom-right',showConfirmButton:false,timer:1500});
	 
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
						$('#visit_table').DataTable().ajax.reload(null, false);
					 }
				});
});


$('#patientModal').on('show.bs.modal',function(){
	 $('#patientModal').find('.select2_data').select2({theme:'bootstrap4',width:'resolve',dropdownParent:'#patientModal'});
      $('.select2_title').select2({theme:'bootstrap4',
	                              width:'resolve',
								  dropdownParent:'#patientModal',
	                             tags: true,
								 createTag: function (params) {
									var term = $.trim(params.term);

									if (term === '') {
									  return null;
									}
								    return {
											id: term,
											text: term,
											newTag: true 
										}
										
								  }
								 });
	 $('#patientModal').find('[name=email]').inputmask("email");
   
    var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
  
   
   $('#patientModal').find('#cell_phone').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });		
	
    var currentYear = new Date().getFullYear();
    var maxDate = new Date(currentYear, 11, 31); // Month is zero-based, so 11 represents December
    var maxDateString = maxDate.toISOString().slice(0, 10);
  
  flatpickr('#birthdate', {
             static: true,
			 allowInput : true,
			 altInput: true,
             maxDate: maxDateString,
			 altFormat: "d/m/Y",
             dateFormat: "Y-m-d",
			 disableMobile: true
        });
  
  

$('#patientModal').find('#update_patient').off().on('click',function(e){
	 e.preventDefault();
	 
		 var clinic = $('#patientModal').find('#clinic_num').val();
		 if(clinic==''){
		  Swal.fire({text:"{{__('Please choose a lab')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		  var fname=$('#patientModal').find('#first_name').val();
		  if(fname==''){
		  Swal.fire({text:"{{__('Please input patient first name')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }
		 
		 
		 
		 var lname=$('#patientModal').find('#last_name').val();
		 if(lname==''){
		  Swal.fire({text:"{{__('Please input patient last name')}}",icon:"error",customClass:"w-auto"});
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
	
		 
		  var cell_phone=$('#patientModal').find('#cell_phone').val();
		  if(cell_phone==''){
		    
		  }else{
			 if(!cell_phone.match(/^[0-9]{2}-[0-9]{6}$/)){
				 Swal.fire({text:"{{__('Enter a cellular phone number in this format: xx-xxxxxx')}}",icon:"error",customClass:"w-auto"});
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
					selectPatient.empty();
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
					var blob = new Blob([data], { type: 'application/pdf' });
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download= 'Result-'+order_id+'.pdf';
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
		
function openSwalMSG(msg){
	Swal.fire({
            html: msg,
            icon: 'info',
            customClass:'w-auto'
        });
}



function openCodesModal(order_id){
	
	
	table_codes = new Tabulator("#codes_list_table", {
	 ajaxURL:"{{ route('lab.visit.request_codes',app()->getLocale())}}", //ajax URL
     ajaxConfig:'POST',
	 ajaxParams: function(){
        var token = '{{csrf_token()}}';
		return {_token:token,order_id:order_id};
        },
	ajaxResponse:function(url, params, response){
         var coll_notes = response.coll_notes;
		 //set notes in modal
		 $('#codesModal').find('#coll_notes').val(coll_notes);
		 
        return response.table_data; 
    },	
	height:"400px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:false, 
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	resizableRows:true,
    columns:[
	  {title:"{{__('Test ID')}}", field:"test_id",visible:false},
	  {title:"{{__('Group')}}", field:"is_group",headerFilter:"input",formatter:function(cell, formatterParams, onRendered){
		  var data=cell.getValue();
		  if(data=='Y'){
			  return "{{__('Yes')}}";
		  }else{
			 return "{{__('No')}}"; 
		  }
	  }},
	  {title: "{{__('Code')}}",field:"test_code",headerFilter:"input"},
	  {title:"{{__('CNSS')}}", field:"cnss",headerFilter:"input"},
	  {title: "{{__('Test')}}", headerSort: false, field:"test_name",headerFilter:"input"},
	  {title:"{{__('Collection Date/Time')}}",field:"collected_test_date",headerFilter:"input"},
	  {title:"{{__('Is Collected')}}",field:"is_test_collected",visible:false},
	  {title:"{{__('Price USD')}}",field:"price_usd",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"$",
				symbolAfter:"p",
				negativeSign:false,
				},formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"$",
                symbolAfter:"p",
				},headerFilter:"input"}, 
		{title:"{{__('Price LBP')}}",field:"price_lbl",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"ل.ل",
				symbolAfter:"p",
				negativeSign:false,
				},
		        formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				symbol:"ل.ل",
				symbolAfter:"p",
				negativeSign:false,
				},headerFilter:"input"},
	  
	  {title:"{{__('CheckedSpecCons')}}", field:"chk_specialcons",visible:false},
	  {title:"{{__('CheckedSpecimens')}}", field:"chk_specimens",visible:false},
	  {title: "{{__('Specimen')}}", headerSort: false,field:"specimen", formatter:function(cell, formatterParams, onRendered){
			var data = cell.getValue();
			var btn='';
			var row = cell.getRow().getData();
			var test_id = row.test_id;
			var chked = row.chk_specimens!='' && row.chk_specimens!=null?JSON.parse(row.chk_specimens):[];
		    var checked =  (chked.length!=0 && chked[test_id]!=null)?'checked':'';
		    
			if(data!=null && data!=''){
			  var specimen = @json($specimen);
			  var img_src='',title='';
			  function isImageURL(url) {
               var imageExtensions = /\.(jpg|jpeg|png|gif)$/i;
               return imageExtensions.test(url);
              }
			  
			 var myspecimen = specimen.find(function(obj) {
                          return obj.id === parseInt(data);
                        });

			 if(myspecimen){ 
					  img_src = myspecimen.src;
					  title =   myspecimen.name;
					   }
			 
		 
		  
		  var btn='';
			 if(!isImageURL(img_src)){
					   btn='<div><input type="checkbox" '+checked+' disabled class="mr-2 chkcodes" value="'+test_id+'" /><label class="ml-1">'+img_src+'</label></div>';
  					  }else{
					   btn='<div><input type="checkbox" '+checked+' disabled class="mr-2 chkcodes" value="'+test_id+'" /><label class="ml-1"><img src="'+img_src+'" title="'+title+'" style="height:20px;width:20px;"/></label></div>';
					  }				
				
		       return btn;
			}else{
				return data;
			}
		 }},
	  
	  {title: "{{__('Special Considerations')}}", headerSort: false,field:"special_considerations",
		 formatter:function(cell, formatterParams, onRendered){
			var row = cell.getRow().getData();
			var data = cell.getValue();
			var test_id = row.test_id;
			var chked = row.chk_specialcons!='' && row.chk_specialcons!=null?JSON.parse(row.chk_specialcons):[];
		    var checked =  (chked.length!=0 && chked[test_id]!=null)?'checked':'';
			var btn='';
			if(data!=null && data!=''){
			 var spec_cons = @json($spec_cons);
			 btn='<div><input type="checkbox" '+checked+' disabled class="mr-2 chkspeccons" value="'+test_id+'" /><label class="ml-1">'+spec_cons[data]+'</label></div>';
			 return btn;
			}else{
				return data;
			}
		 }
		},
	  {title:"{{__('PreAnalytical')}}",field:"preanalytical"}	
	  
	 ],
	});

$('#codesModal').find('#modal_order_id').val(order_id);

$('#codesModal').modal('show');
}
function printLBL(){	 
    var order_id = $('#codesModal').find('#modal_order_id').val();
    $.ajax({
           url: '{{route("phlebotomy.label",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','order_id':order_id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('label.pdf');
					link.click();	
			       Swal.fire({title:"{{__('Downloaded successfully')}}",toast:true,showConfirmButton:false,timer:3000,position:"bottom-right"});
					
			    });
       
 }
function printRequestPDF(){
	    var order_id = $('#codesModal').find('#modal_order_id').val();
		$.ajax({
			url:'{{route("lab.visit.printOrder",app()->getLocale())}}',
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
					
					link.download=('Request#'+order_id+'.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
} 
</script>
@endsection	