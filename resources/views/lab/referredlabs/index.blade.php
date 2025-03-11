<!--
 DEV APP
 Created date : 16-08-2024
-->
@extends('gui.main_gui')

@section('content')	
<div class="container-fluid" id="content_bill">	
			
			<div class="row mt-1">
				<section class="col-md-12 p-0"> 
					<div  class="card card-outline m-0 mb-2">
						<div class="card-header text-white p-0">
							<div class="row"> 
								 <div class="col-md-9 col-8">
								 <h5 class="ml-2 mb-1 mt-1">{{__('Referred Labs').'-'.$FromFacility->full_name}}</h5>
								 </div>
								 <div class="col-md-3 col-4">
									   <button type="button" class="float-right btn btn-sm btn-resize" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
									  </button>
								 </div> 
							</div> 
						</div> 
						<div class="card-body p-1">
						
						</div>
					</div>	
				</section>				
				<section class="col-md-12 p-0"> 
				   <div class="card card-outline m-0 mr-2">
						<div class="card-header">
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__('Filter Referred_lab history')}}</h5></div>
							 <div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								  </button>
							 </div> 
						</div> 
						<div class="card-body p-0">
						  
							<div class="form-row m-1">			  
							 
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('From date')}}"
									value="{{(request()->session()->has('from_date_bill'))?request()->session()->get('from_date_bill'):Carbon\Carbon::now()->subDays(30)->format('Y-m-d')}}"/>
								</div>
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('To date')}}"
									value="{{(request()->session()->has('to_date_bill'))?request()->session()->get('to_date_bill'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
								<div class="col-md-2 mb-2">
										<select name="filter_status" id="filter_status" class="custom-select rounded-0">
											  <option value="">{{__('Paid/Not Paid')}}</option>
											  <option value="Y" {{ session('filter_status') == 'Y' ? 'selected' : '' }} >{{__('Paid')}}</option>
											  <option value="N" {{ session('filter_status') == 'N' ? 'selected' : '' }}>{{__('Not Paid')}}</option>
										 </select>	  
									</div>
								<div class="col-md-6">
                                    <!--<label for="patient" class="label-size">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
									</select>									
								</div>
								  <div class="mt-1 mb-1 col-md-4">
										<!--<label for="pro" class="label-size">{{__('Guarantor')}}</label>-->
										<select name="filter_g" id="filter_g" class="custom-select rounded-0">
										<option value="">{{__('Select Referred_lab')}}</option>		
											@foreach($ext_labs as $ref_labs)
											<option value="{{$ref_labs->id}}" {{ session('filter_g') == $ref_labs->id ? 'selected' : '' }}>{{$ref_labs->full_name}}</option>
											@endforeach 
										</select>
							        </div>
								
                               		<div class="col-md-4">
										
										<input type="hidden" id="filter_branch" name="filter_branch" value="{{$FromFacility->id}}"/>
								    </div>							
								
							</div>
						  
						</div>
					</div>
				</section>
                
                <section class="col-md-12 p-0">
				    <div class="card card-outline m-0"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__("Referred_lab history")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body p-0">	
							<div class="row  mt-1">
							   <div class="col-md-12">
								 <table id="referredlabs_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('#')}}</th>
									<th class="all ">{{__('Bill')}}</th>
									<th class="all ">{{__('Request')}}</th>
									<th class="all">{{__('Guarantor')}}</th>
									<th class="all">{{__('Patient')}}</th>
									<th class="all">{{__('Test Name')}}</th>
									<th class="all">{{__('Total $')}}</th>
									<th class="all">{{__('Total €')}}</th>
									<th class="all">{{__('Pay $')}}</th>
									<th class="all">{{__('Pay €')}}</th>
									<th class="all">{{__('Paid')}}</th>
									</tr>
									</thead>
									<tbody>
									  
									</tbody>
								</table>
							</div>
						   </div>
						   	<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Total $')}}</label>
							   <input  class="text-center form-control"  id="tdollar"  value="{{$tdollar}}" disabled  />
							</div>
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Total €')}}</label>
							   <input  class="text-center form-control"  id="teuro" value="{{$teuro}}" disabled  />
							</div>
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Pay $')}}</label>
							   <input  class="text-center form-control"  id="paydollar" value="{{$paydollar}}" disabled  />
							</div>
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Pay €')}}</label>
							   <input  class="text-center form-control"  id="payeuro" value="{{$payeuro}}" disabled  />
							</div>
							
							</div>
						   
						</div>
                    </div>				
                </section> 				
        </div>   				
							
</div>										
										
@endsection	
@section('scripts')

<script>
$(document).ready(function(){
	$('#filter_fromdate,#filter_todate,#filter_patient,#filter_status,#filter_g').change(function(){
	var filter_fromdate = $('#filter_fromdate').val();
	var filter_todate = $('#filter_todate').val();
	var filter_patient = $('#filter_patient').val();
	var filter_status = $('#filter_status').val();
	var filter_g = $('#filter_g').val();
	$('#referredlabs_table').DataTable().ajax.reload();
	$.ajax({
            url: '{{route("refgen_sumprice",app()->getLocale())}}',
		   data: {'filter_fromdate':filter_fromdate,'filter_todate':filter_todate,'filter_patient':filter_patient,'filter_status':filter_status,'filter_g':filter_g},
           type: 'get',
           dataType: 'json',
           success: function(data){
			$('#tdollar').val(data.tdollar); 
			$('#teuro').val(data.teuro); 		
			$('#paydollar').val(data.paydollar); 
			$('#payeuro').val(data.payeuro); 					
		   }
});
	
});
	$('body').addClass('sidebar-collapse');
	$('.select2_filter_patient').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('Please choose a patient from the list')}}",
		ajax: {
			url: '{{route("patient.loadPat",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    clinic_num:'{{$FromFacility->id}}',
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
				    clinic_num:'{{$FromFacility->id}}',
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
	
		$('#date_bill').flatpickr({
			allowInput: true,
			enableTime: true,
			dateFormat: "Y-m-d H:i",
			time_24hr: true,
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
	
		
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	$('#referredlabs_table').DataTable({
					stateSave: true,
					stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
			        ajax: {
						url: "{{ route('lab.referredlabs.index',app()->getLocale()) }}",
					    data: function (d) {
								
								 d.id_facility = $('#filter_branch').val();
								 d.id_pro = $('#filter_g').val();
								 d.filter_doc=$('#filter_doc').val();
								 d.filter_patient=$('#filter_patient').val();
								 d.filter_fromdate=$('#filter_fromdate').val();
								 d.filter_todate=$('#filter_todate').val();
								 d.filter_status=$('#filter_status').val();
							}
					},
						 
					columns: [

						{data: 'id'},
						{data: 'fac_bill',render: function(data, type, row) {
							var bill = data.split(',');
							return '{{__("Num")}}'+': '+bill[0]+'<br/>'+'{{__("Date")}}'+': '+bill[1];
						 
						 }
						},
						{data:'request_nb'},
						{data: 'ext_lab_name'},
						{data: 'patDetail',render: function(data, type, row) {
							var pat = data.split(',');
							var name = '{{__("Name")}}'+': '+pat[0]
							if(pat[1]!=null && pat[1]!=''){
							 name+='<br/>'+'{{__("DOB")}}'+': '+pat[1];
							}
							if(pat[2]!=null && pat[2]!=''){
							 name+='<br/>'+'{{__("Cell#")}}'+': '+pat[2];
							}
						    return name;
						 }
						 },
						{data: 'bill_name'},
						{data: 'bill_price'},
						{data: 'ebill_price'},
						{data: 'ref_dolarprice'},
						{data: 'ref_ebill_price'},
						{data: 'Paid',orderable: false, searchable: false},

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
	$('#referredlabs_table').DataTable().ajax.reload();
});	





$('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajax({
            url: '{{route("deleteBilllab",app()->getLocale())}}',
		   data: {'id':id,'type':type},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
              "title":data.success,
			  "toast":true,
              "icon":"success",
			  "timer":3000,
			  "position":"bottom-right",
			  "showConfirmButton":false
			  });
			  $('#bill_table').DataTable().ajax.reload();

		       } 
			 }
       });	
    });
	
	

});//end scripts functions	

function Paid(id,state){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("PaidTest",app()->getLocale())}}',
			data:{id:id,state:state},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			 			  $('#referredlabs_table').DataTable().ajax.reload(null, false);


				 });
				 
			}
		});
}

</script>

@endsection	