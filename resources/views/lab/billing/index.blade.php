<!--
 DEV APP
 Created date : 22-10-2022
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
								 <h5 class="ml-2 mb-1 mt-1">{{__('New Bill').'-'.$FromFacility->full_name}}</h5>
								 </div>
								 <div class="col-md-3 col-4">
									   <button type="button" class="float-right btn btn-sm btn-resize" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
									  </button>
								 </div> 
							</div> 
						</div> 
						<div class="card-body p-1">
						 <form  id="newBill_form" action="{{route('NewBillPatient',app()->getLocale())}}" method="POST" enctype="multipart/form-data">
					        @csrf
						    <div class="row">
								   <div class="col-md-9">
									 <input type="hidden" id="clinic" name="clinic" value="{{$FromFacility->id}}"/>
									 <label for="patient" class="label-size">{{__('Patient')}}</label>
									 <select class="select2_filter_patient custom-select rounded-0" name="patient"   id="selectpatient" style="width:100%;">
									 </select>
								   </div>
								   <div class="col-md-3">
									<label class="label-size">{{__('Date/Time')}}</label>
										<div class="input-group">
											<input type="text" class="form-control"  name="date_bill" id="date_bill" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
											<button type="submit" class="ml-1 btn btn-action btn-md btn-icon" title="{{__('Create')}}" style="font-size:0.9em;border-radius:50%;">{{__('Bill')}}<i class="ml-1 fa fa-plus"></i></button>
										</div>
								   </div>
							</div>   
                         </form>						 
						</div>
					</div>	
				</section>				
				<section class="col-md-12 p-0"> 
				   <div class="card card-outline m-0 mr-2">
						<div class="card-header">
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__('Filter bills history')}}</h5></div>
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
								<div class="col-md-2">
                                    <!--<label for="filter_status" class="label-size">{{__('Status')}}</label>-->
									<select class="form-control" name="filter_status"  id="filter_status" style="width:100%;">
										<option value="O" {{request()->session()->has('status_bill') && request()->session()->get('status_bill')=='O'?'selected':''}}>{{__('Active')}}</option>	
										<option value="N" {{request()->session()->has('status_bill') && request()->session()->get('status_bill')=='N'?'selected':''}}>{{__('Cancelled')}}</option>	
									</select>									
								</div>
								<div class="col-md-6">
                                    <!--<label for="patient" class="label-size">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
									</select>									
								</div>
								  <div class="mt-1 mb-1 col-md-4">
										<!--<label for="pro" class="label-size">{{__('Guarantor')}}</label>-->
									    <select class="select2_data_grntr custom-select rounded-0" name="filter_g"  id="filter_g" style="width:100%;">
										</select>
							        </div>
									<div class="mt-1 mb-1 col-md-4">
										<!--<label for="pro" class="label-size">{{__('Doctor')}}</label>-->
									    <select class="select2_data_doc custom-select rounded-0" name="filter_doc"  id="filter_doc" style="width:100%;">
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
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__("Bills history")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body p-0">	
							<div class="row  mt-1">
							   <div class="col-md-12">
								 <table id="bill_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('#')}}</th>
									<th class="all ">{{__('Bill')}}</th>
									<th class="all ">{{__('Request')}}</th>
									<th class="all">{{__('Guarantor')}}</th>
									<th class="all">{{__('Patient')}}</th>
									<th class="all">{{__('Doctor')}}</th>
									<th class="all">{{__('Total')}}</th>
									<th class="all">{{__('Total$')}}</th>
									<th class="all">{{__('Remaining')}}</th>
									<th class="all">{{__('Remaining$')}}</th>
									<th class="all ">{{__('Lab')}}</th>
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
										
@endsection	
@section('scripts')

<script>
$(document).ready(function(){
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
    
	$('#bill_table').DataTable({
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
						url: "{{ route('lab.billing.index',app()->getLocale()) }}",
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
						{data: 'doctor_name'},
						{data: 'total'},
						{data: 'totalUS'},
						{data: 'sold'},
						{data: 'soldUS'},
						{data: 'fromClinicName'},
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
	$('#bill_table').DataTable().ajax.reload();
});	



$('#filter_todate,#filter_patient,#filter_status,#filter_g,#filter_doc').change(function(){
	$('#bill_table').DataTable().ajax.reload();
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
	
	$('#newBill_form').submit(function(event) {
            // Prevent the form from submitting initially
            event.preventDefault();
	        var patient = $('#selectpatient').val();
			if(patient==null || patient==''){
				Swal.fire({text:'{{__("Please choose your patient")}}',icon:'warning',customClass:'w-auto'});
				return;
			}
			var date_bill=$('#date_bill').val();
			if(date_bill==null || date_bill==''){
				Swal.fire({text:'{{__("Please choose a billing date")}}',icon:'warning',customClass:'w-auto'});
				return;
			}
			this.submit();
	});

});//end scripts functions	


</script>
<script>
function getPDF(id){
	   $.ajax({
           url: '{{route("downloadPDFBilling",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Bills.pdf');
			link.click();
			Swal.fire({title:'{{__("Bill Downloaded")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
		
			    });
}


function sendPDF(id){
	    
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	    $.ajax({
			type : 'POST',
			url : '{{route("lab.billing.send_patient",app()->getLocale())}}',
			data : { bill_id: id},
			dataType: 'JSON',
			success: function(data){
					  if(data.error){
						  Swal.fire({html:data.error,icon:'error',customClass:'w-auto'});
					  }
                      
                      if(data.success){
						Swal.fire({toast:true,title:data.success,position:'bottom-right',icon:'success',showConfirmButton:false,timer:3000});
					  }					  
						
						}
					});	 
}
</script>
@endsection	