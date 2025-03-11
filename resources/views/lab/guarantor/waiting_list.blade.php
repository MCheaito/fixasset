@extends('gui.main_gui')
@section('content')	
<div class="container-fluid mt-1">	
			<div class="row mt-1">
				<section class="col-md-12 p-0"> 
				   <div class="card card-outline m-1">
						<div class="card-header">
							 <div class="card-title"><h5>{{__('ALL OUTREACH Requests')}}</h5></div>
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
									value="{{(request()->session()->has('wl_from_date'))?request()->session()->get('wl_from_date'):Carbon\Carbon::now()->subDays(30)->format('Y-m-d')}}"/>
								</div>
								<div class="mb-1 col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" placeholder="{{__('To date')}}" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('Choose a date')}}"
									value="{{(request()->session()->has('wl_to_date'))?request()->session()->get('wl_to_date'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
							   <div class="mb-1 col-md-2 col-6">
                                    <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									<select class="custom-select rounded-0" name="filter_status"  id="filter_status" style="width:100%;">
										<option value="">{{__('All')}}</option>
										<option value="Y" {{request()->session()->has('wl_status') && request()->session()->get('status')=='Y'?'selected':'' }}>{{__("In Progress")}}</option>
										<option value="N"  {{request()->session()->has('wl_status') && request()->session()->get('status')=='N'?'selected':'' }}>{{__("Rejected")}}</option>
									</select>									
								</div>
								<div class="col-md-2 col-6">
									<!--<label for="filter_patient_tel" class="label-size ">{{__('Phone')}}</label>-->
									<input type="tel" class="form-control" placeholder="{{__('Fill a phone')}}" name="filter_patient_tel" id="filter_patient_tel" value="{{request()->session()->has('wl_filter_patient_tel') && request()->session()->get('wl_filter_patient_tel')!=''?request()->session()->get('wl_filter_patient_tel'):old('wl_filter_patient_tel')}}" onkeypress="return isNumberKey(event)"/>
								</div>
							  
								<div class="mb-1 col-md-4 col-6">
                                    <!--<label for="patient" class="label-size ">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
											
									</select>									
								</div>
								<div class="col-md-4 col-6">
										<!--<label for="filter_test_codes" class="label-size ">{{__('Lab Code')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_test_codes"  id="filter_test_codes" style="width:100%;">
								               <option value="0">{{__('Choose a code')}}</option>
											   @foreach($lab_tests as $t)
											      <option value="{{$t->id}}"  {{request()->session()->has('wl_lab_code') && request()->session()->get('wl_lab_code')==$t->id?'selected':'' }}>{{$t->test_name}}</option>
											   @endforeach
										</select>
							     </div>						
								 <div class="col-md-4 col-6">
										<select class="select2_data_grntr custom-select rounded-0" name="filter_ext_lab"  id="filter_ext_lab" style="width:100%;">
											
										</select>
							      </div>
								  <div class="col-md-4 col-6">
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
							 <div class="card-title"><h5>{{__("Requests History")}}</h5></div>
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
									<th class="all">{{__('Notes')}}</th>
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
@include('lab.guarantor.rejectModal')
@include('lab.guarantor.codesModal')										
@endsection	
@section('scripts')
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
	  
	  var selectedPatId = localStorage.getItem('wl_selectedPatId');
      var selectedPatText = localStorage.getItem('wl_selectedPatText');
      
	  if (selectedPatId && selectedPatText) {
        // Create the option manually and select it
        var newOption = new Option(selectedPatText, selectedPatId, true, true);
        $('.select2_filter_patient1').append(newOption).trigger('change');
       }
	  
	  $('.select2_filter_patient1').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('wl_selectedPatId', selectedData.id);
        localStorage.setItem('wl_selectedPatText', selectedData.text);
        });
		
	  $('.select2_filter_patient1').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('wl_selectedPatId');
          localStorage.removeItem('wl_selectedPatText');
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
	  
	  var selectedDocId = localStorage.getItem('wl_selectedDocId');
      var selectedDocText = localStorage.getItem('wl_selectedDocText');
      
	  if (selectedDocId && selectedDocText) {
        // Create the option manually and select it
        var newOption = new Option(selectedDocText, selectedDocId, true, true);
        $('.select2_data_doc').append(newOption).trigger('change');
       }
	  
	  $('.select2_data_doc').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('wl_selectedDocId', selectedData.id);
        localStorage.setItem('wl_selectedDocText', selectedData.text);
        });
		
	  $('.select2_data_doc').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('wl_selectedDocId');
          localStorage.removeItem('wl_selectedDocText');
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
		
	  var selectedGRNTRId = localStorage.getItem('wl_selectedGRNTRId');
      var selectedGRNTRText = localStorage.getItem('wl_selectedGRNTRText');
      
	  if (selectedGRNTRId && selectedGRNTRText) {
        // Create the option manually and select it
        var newOption = new Option(selectedGRNTRText, selectedGRNTRId, true, true);
        $('.select2_data_grntr').append(newOption).trigger('change');
       }
	  
	  $('.select2_data_grntr').on('select2:select', function(e) {
         var selectedData = e.params.data;
       // Store selected ID and text in localStorage
        localStorage.setItem('wl_selectedGRNTRId', selectedData.id);
        localStorage.setItem('wl_selectedGRNTRText', selectedData.text);
        });
		
	  $('.select2_data_grntr').on('select2:unselect', function() {
          // Clear stored data from localStorage
          localStorage.removeItem('wl_selectedGRNTRId');
          localStorage.removeItem('wl_selectedGRNTRText');
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
						url: "{{ route('lab.visit.waiting_list',app()->getLocale()) }}",
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
						{data: 'patDetail'},
						{data:'ext_lab_name'},
						{data:'color_status'},
						{data: 'visit_date_time'},
						{data:'reject_notes_list'},
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
	
	
});
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

function acceptRequest(id){
	$.ajax({
		url:'{{route("lab.visit.acceptRequest",app()->getLocale())}}',
		data:{_token:'{{csrf_token()}}',id:id},
		type:'POST',
		dataType:'JSON',
		success: function(data){
                     Swal.fire({title:data.msg,icon:"success",toast:true,showConfirmButton:false,position:"bottom-right",timer: 3000});
				    $('#visit_table').DataTable().ajax.reload(null, false);
		}		
	});
}

function rejectRequest(id){
	 $('#rejectModal').find('#rejectId').val(id);
     $('#rejectModal').modal('show');
}

function saverejectNote(){
	var id = $('#rejectModal').find('#rejectId').val();
	var note = $('#rejectModal').find('#rejectNote').val();
	var other_note = $('#rejectModal').find('#otherrejectNote').val()
	$.ajax({
		url:'{{route("lab.visit.rejectRequest",app()->getLocale())}}',
		data:{_token:'{{csrf_token()}}',id:id,note:note,other_note:other_note},
		type:'POST',
		dataType:'JSON',
		success: function(data){
               Swal.fire({title:data.msg,icon:"success",toast:true,showConfirmButton:false,position:"bottom-right",timer: 3000});
			   $('#rejectModal').modal('hide');
			   $('#visit_table').DataTable().ajax.reload(null, false);
		}		
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