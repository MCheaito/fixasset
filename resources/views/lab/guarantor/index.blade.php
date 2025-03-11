@extends('gui.main_gui')
@section('styles')
<style>
.text-nowrap{
	overflow:hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
</style>
@section('content')	
<div class="container-fluid mt-1">	
			<div class="row mt-1">
				
				<section class="col-md-12 p-0"> 
					<div  class="card card-outline m-1">
						<div class="card-header text-white ">
							 <div class="card-title"><h5>{{__('New Request').'-'.$gname}}</h5></div>
							 <div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								  </button>
							 </div> 
						</div> 
						<div class="card-body p-1">
						 <form  id="NewVisitForm">
					    
						    <div class="row">
								
								
							   <div class="col-md-10">
									<label for="patient" class="m-0">{{__('Patient')}}
									 <input type="hidden" id="clinic_req" name="clinic_req" value="{{$clinics->id}}"/>
									 <button type="button" id="create_pat_data"  class="p-1 btn btn-icon btn-md"  title="{{__('Create new patient')}}"><i class="fa fa-plus text-muted"></i></button>
									 <button type="button" id="edit_pat_data" class="p-1 btn btn-icon btn-md" title="{{__('Edit patient')}}"><i class="fa fa-edit text-muted"></i></button>
									</label>
									<select class="select2_filter_patient custom-select rounded-0" name="patient"   id="selectpatient" style="width:100%;">
										 
									</select>
								</div>
								
								 <div class="col-md-2 align-self-end mx-auto">
									 <button  id="NewVisit" class="m-1 btn btn-action btn-md btn-icon" title="{{__('Create')}}" style="font-size:0.9em;border-radius:50%;">{{__('Request')}}<i class="ml-1 fa fa-plus"></i></button>
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
									value="{{Carbon\Carbon::now()->subDays(30)->format('Y-m-d')}}"/>
								</div>
								<div class="mb-1 col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('To date')}}" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('Choose a date')}}"
									value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
							   <div class="mb-1 col-md-3 col-6">
                                    <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									<select class="custom-select rounded-0" name="filter_status"  id="filter_status" style="width:100%;">
										<option value="">{{__("Active/Rejected/Cancelled")}}</option>
										<option value="Y">{{__("Active")}}</option>
										<option value="R">{{__("Rejected")}}</option>
										<option value="N">{{__("Cancelled")}}</option>
									</select>									
								</div>
								<div class="col-md-3 col-6">
										<!--<label for="filter_order" class="label-size ">{{__('Result status')}}</label>-->
										<select class="custom-select rounded-0" name="filter_order"  id="filter_order" style="width:100%;">
								            	 <option value="0">{{__('Choose a status')}}</option>
												 <option value="I">{{__('In Progress')}}</option>
											     <option value="V">{{__('Completed')}}</option>  
										</select>
							        </div>		
								<div class="col-md-2 col-6">
									<!--<label for="filter_patient_tel" class="label-size ">{{__('Phone')}}</label>-->
									<input type="tel" class="form-control" placeholder="{{__('Fill a phone')}}" name="filter_patient_tel" id="filter_patient_tel" value="{{old('filter_patient_tel')}}" onkeypress="return isNumberKey(event)"/>
								</div>
							   <div class="col-md-3 col-6">
										<!--<label for="filter_test_codes" class="label-size ">{{__('Lab Code')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_test_codes"  id="filter_test_codes" style="width:100%;">
								               <option value="0">{{__('Choose a code')}}</option>
											   @foreach($lab_tests as $t)
											      <option value="{{$t->id}}">{{$t->test_name}}</option>
											   @endforeach
										</select>
							        </div>			
								<div class="mb-1 col-md-5">
                                    <!--<label for="patient" class="label-size ">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
											
									</select>									
								</div>
								<div class="col-md-4">
										<select class="select2_data_doc custom-select rounded-0" name="filter_doctor_num"  id="filter_doctor_num" style="width:100%;">
											  
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
									<th class="all">{{__('Status')}}</th>
									<th class="all">{{__('Appointment')}}</th>
									<th class="all">{{__('Notes')}}</th>
									<th class="all">{{__('Doctor')}}</th>
									<th class="all">{{__('Sent Status')}}</th>
									<th class="all">{{__('Codes')}}</th>
									<th class="all">{{__('Guarantor')}}</th>
									
									<!--<th class="all ">{{__('My Lab')}}</th>-->
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
@include('lab.guarantor.codesModal')
@include('lab.guarantor.orderTestsModal')										
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
	
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	$('#visit_table').DataTable({
					
					processing: false,
                    searching: true,
					serverSide: true,
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
						{data: 'patDetail'},
						{data:'color_status'},
						{data: 'visit_date_time'},
						{data:'reject_note_list'},
						{data:'doctor_name'},
						{data: 'sent_result_details',orderable: false, searchable: false},
					    {data:'test_names'},
						{data:'ext_lab_name'},
						
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

    
	//auto reload table for new states every 10 minutes
	setInterval(function() {
      $('#visit_table').DataTable().ajax.reload(null, false); // Reload DataTable without resetting paging position
      }, 600000);				
	
	
	
	
	$('#orderTestsModal').on('show.bs.modal',function(){
		var nb = $('.test').filter(function(){return $(this).is(':checked')}).length;
		$('#orderTestsModal').find('#selected_tests').text('Selected tests are : '+nb);
	    $('#orderTestsModal').find('.select2_data_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent:'#orderTestsModal'});
        $('#orderTestsModal').find('#profile_tests').select2({dropdownParent:'#orderTestsModal'});
     
   
   $('#search_test').keyup(function(){
       var group_num = $('#filter_group').val();
	   var category_num = $('#filter_category').val();
	   var text = $(this).val().trim().toLowerCase();
		 if(group_num == '' && category_num=='' && text==''){
				 $('.content-cat').show();
				 $('.content-chk  .content-lbl').each(function(){
                      $(this).closest('.content-chk').show();
                  });
				return false;
			}
		
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{group_num:group_num,category_num:category_num,search:text,type:'search'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                const arr = data.tests;
				
                $('.content-chk').hide();
				$('.content-cat').hide();
				var categoryNums = data.category_nums;
                 categoryNums.forEach(function(categoryNum) {
				   $('#cat'+categoryNum).show();
				 });
				 
				$.each(arr,function(index,val){
				 	 $('.content-chk  .content-lbl').each(function(){
					  
					  if($(this).find('.test').val() == val ){
						 $(this).closest('.content-chk').show();
						 $(this).closest('.row').find('.content-cat').show(); 
						 }
					  });
				 
			         });					 
                  
					
			 }
            });
         });
		 
	$('#cancel_order').click(function(){
	  
	     $('#testsForm').trigger("reset");
	     
	     let arr =[];
		 
		 $('#profile_tests').val(arr).change();
		 $('#profile_tests').trigger('change.select2');
		 
		 $('.test').each(function(){ 
			
			if($(this).is(':checked')){
			  $(this).parent().removeClass('btn-light'); 
			  $(this).parent().addClass('btn-success');
			 			  
			  }else{
				$(this).parent().addClass('btn-light'); 
			    $(this).parent().removeClass('btn-success');
								 
			  }
		 
		 });	
	  
	 var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	    }).length;
	
	  $('#selected_tests').text('Selected tests are : '+nb);
   });
   
  
   
   $('#save_order').click(function(e){
	  e.preventDefault();
	  var nb = $('.test').filter(':checked').length;
	  //var nb= $('#chk_tsts').val().length;
	  //var nb = $('#tsts_tbl').find('.dataTables_empty').length ;
	  
	    
	
	  if(nb==0){
		  Swal.fire({icon:'error',text:'Please choose at least one test',customClass:'w-auto'});
		  return false;
	    }
			 
				
				var dataToSave = []; 
			   
			   $('.test').each(function(){
		          if($(this).is(':checked')){ 
				    var rowData={};
					rowData.test_id = $(this).val();
					rowData.referred_test = $(this).data("referredLAB");
					dataToSave.push(rowData);
				  }
	            });
	 
	  		    var order_id = $('#order_id').val();
				
				$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
				$.ajax({
				  url:'{{route("lab.visit.saveGuarantorOrder",app()->getLocale())}}',
				   data:{
					   clinic_num: '{{auth()->user()->clinic_num}}',
					   patient_num: $('#orderTestsModal').find('#guarantor_patient_num').val(),
					   ext_lab: '{{$gid}}',
					   tests: JSON.stringify(dataToSave)
					     },
				   type: 'post',
				   dataType: 'json',
				   success: function(data){
					
					 $('#orderTestsModal').modal('hide');
					 Swal.fire({ toast:true,title: data.msg,icon: "success",position:'bottom-right',timer:1500,showConfirmButton:false });
					 $('#visit_table').DataTable().ajax.reload(null,false);
				 
				 }
				});  
	
               });
	
	
	$('#filter_category').change(function(){
	  
	       var category_num = $(this).val();
		   var group_num = $('#filter_group').val();
		   var text = $('#search_test').val().trim().toLowerCase();
		   if(group_num == '' && category_num=='' && text==''){
				 $('.content-cat').show();
				 $('.content-chk  .content-lbl').each(function(){
                      $(this).closest('.content-chk').show();
				   });
				return false;
			}
		    
		   $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{category_num:category_num,search:text,type:'cat'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                 $('#filter_group').empty();
				 $('#filter_group').html(data.html);
				 const arr = data.tests;
				 $('.content-chk').hide();
				 $('.content-cat').hide();
				 var categoryNums = data.category_nums;
                 categoryNums.forEach(function(categoryNum) {
				   $('#cat'+categoryNum).show();
				 });
				 
				 $.each(arr,function(index,val){
				 	 //var text = val.toLowerCase();	
					 $('.content-chk  .content-lbl').each(function(){
					  if($(this).find('.test').val() == val ){
						 $(this).closest('.content-chk').show();
						
						 }
					  });
				 
			         });					 
			   }
            });
	       
      });
	  


$('#profile_tests').on('select2:select', function (e) {
     // Do something
     var data = e.params.data;
     var id = data.id;
	  $.ajax({
			
			url: '{{route("lab.visit.getProfileTests",app()->getLocale())}}',
			type: 'post',
			dataType: 'json',
			data:{_token:'{{csrf_token()}}',id:id},
			success: function(data){
				//console.log(data.tests);
				var arr = data.tests;
				$('.test').each(function(){
					if(arr.includes($(this).val()) && !$(this).is(':checked')){
						$(this).prop('checked',true);
						$(this).parent().removeClass('btn-light'); 
			            $(this).parent().addClass('btn-success');
				        //$('#chosen_tests').append('<div class="label-size col-12" id="'+$(this).val()+'"><b>'+$(this).attr('data-name')+'</b></div>');
					}
				});
				
				var nb = $('.test').filter(function(){
					return $(this).is(':checked') 
				}).length;
				
				$('#selected_tests').text('Selected tests are : '+nb);
			 }
		  });
	  
		  });

$('#profile_tests').on('select2:unselect', function (e) {
     // Do something
	 var data = e.params.data;
     var id = data.id;
	  $.ajax({
			
			url: '{{route("lab.visit.getProfileTests",app()->getLocale())}}',
			type: 'post',
			dataType: 'json',
			data:{_token:'{{csrf_token()}}',id:id},
			success: function(data){
				//console.log(data.tests);
				
				var arr = data.tests;
				$('.test').each(function(){
					if(arr.includes($(this).val()) && $(this).is(':checked')){
						$(this).prop('checked',false);
						$(this).parent().removeClass('btn-success'); 
			            $(this).parent().addClass('btn-light');
                        //$('#chosen_tests').find('#'+$(this).val()).remove();					
						}
				});
				
				var nb = $('.test').filter(function(){
					return $(this).is(':checked') 
				}).length;
				
				$('#selected_tests').text('Selected tests are : '+nb);
			 }
		  });
	  
		  });
		  
	  
	  $('#filter_group').change(function(){
	  
	       var group_num = $(this).val();
		   var category_num = $('#filter_category').val();
		   var text = $('#search_test').val().trim().toLowerCase();
		  
		  if(group_num == '' && category_num=='' && text==''){
				 $('.content-cat').show();
				 $('.content-chk  .content-lbl').each(function(){
                      $(this).closest('.content-chk').show();
                  });
				return false;
			}
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{group_num:group_num,category_num:category_num,search:text,type:'grp'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                const arr = data.tests;
				
                $('.content-chk ').hide();
				$('.content-cat').hide();
				var categoryNums = data.category_nums;
                categoryNums.forEach(function(categoryNum) {
				   $('#cat'+categoryNum).show();
				 });
				 
				$.each(arr,function(index,val){
				 	// var text = val.trim().toLowerCase();	
					 $('.content-chk  .content-lbl').each(function(){
					  if($(this).find('.test').val() == val ){
						 //console.log(text);
						 $(this).closest('.content-chk').show();
						 $(this).closest('.content-cat').show();
						 }
					  });
				 
			         });					 
                  
					
			 }
            });
	       
      });
	
	
	
	
	
	});
	
	 $('#orderTestsModal').on('hidden.bs.modal',function(){
	     $('#testsForm').trigger("reset");
		 $('#search_test').val('');
		 $('#filter_category').val('');
		 $('#filter_category').trigger('change.select2');
		 $('#filter_group').val('');
		 $('#filter_group').trigger('change.select2');
		 $('.content-chk').show();
	     let arr =[];
		 $('#profile_tests').val(arr).change();
		 $('#profile_tests').trigger('change.select2');
		 
		 $('.test').each(function(){ 
				$(this).parent().addClass('btn-light'); 
			    $(this).parent().removeClass('btn-success');
								 
			 });	
	  
	    var nb = $('.test').filter(function(){
		  return $(this).is(':checked') 
	    }).length;
	
	    $('#selected_tests').text('Selected tests are : '+nb);
	  
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
   var patient = $('#selectpatient').val();
   var date_visit=$('#date_visit').val();
   var ext_lab_num = '{{$gid}}';
   
   if(patient=="0" || patient==null){
	   Swal.fire({text:'{{__("Please choose your patient")}}',icon:"error",customClass:"w-auto"});
	   return false;
   }
   
   
    $('#orderTestsModal').find('#guarantor_patient_num').val(patient);
    $('#orderTestsModal').modal("show");
   });



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
	$('#visit_table').DataTable().ajax.reload(null,false);
});	

$('#filter_patient_tel').on('input',function(){
	$('#visit_table').DataTable().ajax.reload(null,false);
});

$('#filter_todate,#filter_patient,#filter_status,#filter_order,#filter_test_codes,#filter_doctor_num').change(function(){
	$('#visit_table').DataTable().ajax.reload(null,false);
});

$('#refresh_button').click(function(e){
	 e.preventDefault();
	 $('#visit_table').DataTable().ajax.reload(null,false);
	 Swal.fire({toast:true,title:'{{__("Refreshed successfully")}}',icon:'success',position:'bottom-right',showConfirmButton:false,timer:1500});
	 
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
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download= 'Request#'+order_id+'.pdf';
					link.click();
					$('#visit_table').DataTable().ajax.reload(null,false);
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
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
 
 function chkbx_fn(el){
	 
	 if($(el).is(':checked')){
			  		  
				//el is a test then add it
				$(el).parent().removeClass('btn-light'); 
			    $(el).parent().addClass('btn-success');
				//$('#chosen_tests').append('<div class="label-size col-12" id="'+$(el).val()+'"><b>'+$(el).attr('data-name')+'</b></div>');
				  
		  }else{
			   $(el).parent().addClass('btn-light'); 
			   $(el).parent().removeClass('btn-success');
			   //$('#chosen_tests').find('#'+$(el).val()).remove();
			   
				}	

			 
			  
		
	  
	  var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	}).length;
	  
	  $('#selected_tests').text('Selected tests are : '+nb);
}

		
</script>
@endsection	