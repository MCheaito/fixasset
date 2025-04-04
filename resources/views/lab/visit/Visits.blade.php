@extends('gui.main_gui')
@section('styles')
<!-- Bootstrap file input css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('dist/bootstrap-fileinput/css/fileinput.min.css') }}">
<style>
.bginfo{
	color: #000 !important;
}
.bgblue{
	color: #0275d8 !important; 
}
.bgorange{
	color: #fd7e14  !important; 
}
.bggreen{
	color: #5cb85c !important; 
}
.bgred{
	color: #d9534f !important; 
}
@media print {
		body * {
			visibility: hidden;
		}
		.modal-content * {
			visibility: visible;
			overflow: visible;
		}
		.main-page * {
			display: none;
		}
		.modal {
			position: absolute;
			left: 0;
			top: 0;
			margin: 0;
			padding: 0;
			min-height: 550px;
			visibility: visible;
			overflow: visible !important; /* Remove scrollbar for printing. */
		}
		
		.noPrint{
			  display: none;
		}
				
		.modal-dialog {
			visibility: visible !important;
			overflow: visible !important; /* Remove scrollbar for printing. */
		}
	}
	


.close-icon{
    	border-radius: 50%;
        position: absolute;
        right: 5px;
        top: -10px;
        padding: 5px 8px;
    }


.fancybox__slide {
  padding: 0px;
 
}

.fancybox__carousel .fancybox__slide.has-pdf .fancybox__content {
 width:95%;
 height:85%
}

.file-drop-zone {
	min-height:auto;
	
}
.file-drop-zone-title {
	font-size : 1.4em;
	padding : 25px 10px;
}

.text-nowrap{
	overflow:hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.billcss{
	border:0px !important;
	padding:0 !important;
	background-color:#fff !important;
	font-size: 14px;
	font-weight: bold;
}
.tabulator{
	font-size: 14px;
}
</style>
 
@endsection
@section('content')

<div class="container-fluid" style="font-size:0.9rem;">	
     <div class="row mt-1 mb-1">  
	         @php $disabled = isset($order) && ($order->status=='F' || $order->status=='V')?'disabled':'';@endphp
			 <div class="form-group col-md-5" style="padding-bottom:5px">
					<label class="label-size">{{__('Guarantors')}}</label>
					<select class="select2_data custom-select rounded-0" name="selectLab"   id="selectLab" style="width:100%;" {{$disabled}}>
							<option value="">{{__('Choose a guarantor')}}</option>
							@foreach($ext_labs as $lab)
							 <option value="{{$lab->id}}" {{(!isset($order) && Session::has('order_ext_lab') && Session::get('order_ext_lab')==$lab->id )||(isset($order) && $lab->id==$order->ext_lab)? 'selected' : ''}}>{{$lab->full_name}}</option>
							@endforeach
                    </select>
			  </div>
			  <div class="form-group col-md-5" style="padding-bottom:5px">
					<label class="label-size">{{__('Doctors')}}</label>
					<select class="select2_data custom-select rounded-0" name="selectDoc"  id="selectDoc" style="width:100%;" {{$disabled}}>
							<option value="">{{__('Choose a doctor')}}</option>
							@foreach($doctors as $d)
							 <option value="{{$d->id}}">{{$d->full_name}}</option>
						    @endforeach
                    </select>
			  </div>
			  @if(auth()->user()->type==2)
			  <div class="form-group col-md-1" style="padding-bottom:5px">
			    <label class="label-size">{{__('SMS')}}</label>
				<input type="text" class="form-control text-center" disabled name="sms_pack" id="sms_pack" value="{{$sms_pack}}"/>
			  </div>
			  @endif
			  <div class="mt-md-4 form-group col-md-1 text-right" style="padding-bottom:5px">
				<a href="{{route('lab.visit.index',app()->getLocale())}}" class="btn btn-back">{{__('Back')}}</a>  
			  </div>
			 <div class="form-group col-md-3" style="padding-bottom:5px">
					<label class="label-size p-0 m-0">{{__('Request')}}</label>
					<input type="text" name="dateConsult" readonly="true" value="{{isset($order)?'Nb. : '.$order->request_nb:__('New Request')}}" class="form-control form-control-border border-width-2"/>
             </div>     
			 <div class="form-group col-md-6" style="padding-bottom:5px">
				 <label class="label-size p-0 m-0">{{__('Patient')}}</label>
				 <input type="text" name="txtPatient" readonly="true" value="{{$patient_data}}" class="form-control form-control-border border-width-2"/>
			</div>
			<div class="form-group col-md-3">
			   <label class="label-size p-0 m-0">{{__('Age')}}</label>
			   <input type="text" name="txtDOB" readonly="true" value="{{$age_data}}" class="form-control form-control-border border-width-2"/>
			</div>
			
			
	      <div class="col-md-12"> 
			<div class="card">
				<div class="card-header card-menu"> 
				   <div class="card-title">
						<ul class="nav nav-pills" style="font-size:1rem;">
						  
						   <li class="nav-item">
							<a class="visits_tab nav-link active"  href="#orders" data-toggle="tab">{{__('Orders')}}</a>
						  </li>
						  @if(isset($order))
							  
							  @if($view_bills)
							  <li class="nav-item">
								<a class="visits_tab nav-link"  href="#bill" data-toggle="tab">{{__('Billing')}}</a>
							  </li>
							  @endif
							  @if($view_results)
							  <li class="nav-item">
								<a class="visits_tab nav-link"  href="#results" data-toggle="tab">{{__('Results')}}</a>
							  </li>
							  @endif
							  @if($view_culture && $culture_test->count())
								 <li class="nav-item">
									<a class="visits_tab nav-link"  href="#culture" data-toggle="tab">{{__('Culture')}}</a>
								  </li>
							  @endif	  
							  @if($view_attachments)
								 <li class="nav-item">
									<a class="visits_tab nav-link"  href="#docs" data-toggle="tab">{{__('Attachments')}}</a>
								  </li>
							  @endif
							   @if($view_templates && $template_reports->count())
						         @foreach($template_reports as $tr) 
						          <li class="nav-item">
									<a class="visits_tab nav-link"  href="#template{{$tr->id}}" data-toggle="tab">{{'Template-'.$tr->test_name}}</a>
								  </li>
							     @endforeach
						       @endif
							   
						  @endif
						 
						</ul>
				   </div>
                 </div>
             
			  <div class="card-body pr-0 pb-0 pt-1 pl-1"> 				 
					
					<div class="tab-content">
					   <input type="hidden" id="order_id" name="order_id" value="{{isset($order)?$order->id:'0'}}"/>
					   <input type="hidden" id="order_status" name="order_status" value="{{isset($order)?$order->status:'P'}}"/>
					   <div id="orders" class="tab-pane active">
						     @include('lab.visit.orders.index')
					   </div>
					   @if(isset($order))
						   
						   @if($view_bills) 
						   <div id="bill" class="tab-pane">
								 @include('lab.visit.billing.index')
						   </div>					   
						   @endif
						   @if($view_results)
						   <div id="results" class="tab-pane">
								  @include('lab.visit.results.index')
						   </div>
						   @endif
						   @if($view_attachments)
						   <div id="docs" class="tab-pane">
								 @include('lab.visit.documents.index')
						   </div>
						   @endif
						   @if($view_culture && $culture_test->count())
						   <div id="culture" class="tab-pane">
								 @include('lab.visit.culture.index')
						   </div>
						   @endif
						   @if($view_templates && $template_reports->count())
						    @foreach($template_reports as $tr) 
						     <div id="template{{$tr->id}}" class="tab-pane">
							  <div class="container-fluid">	
                                 <div class="mt-1 row form-group">	
							 	   <div class="col-md-12 text-right">
								    <button type="button" data-id="{{$tr->id}}" class="editTemplate m-1 btn btn-action" {{isset($order) && ($order->status=='V')?'disabled':''}}>{{__('Edit')}}</button>
				                    <button type="button" data-id="{{$tr->id}}" class="cancelTemplate m-1 btn btn-reset" disabled>{{__('Cancel')}}</button>
									<button type="button" data-id="{{$tr->id}}" class="updateTemplate m-1 btn btn-action" disabled >{{__('Update')}}</button>
								   	<button type="button" data-id="{{$tr->id}}" class="printTemplate m-1 btn btn-action">{{__('Print')}}</button>
								   </div>
								   <div class="mb-2 col-md-12">
									<textarea  id="description{{$tr->id}}" class="summernote form-control" name="description">{!!$tr->description!!}</textarea>
					              </div>
								 </div>
							  </div>	 
						   </div>
							@endforeach
						  @endif
					   @endif					
					</div>
			   </div>
            </div>
           </div>			
     </div>								
</div>		
@if(isset($order) && isset($ReqPatient))
  @include('lab.visit.billing.paymentModal')
  @include('lab.visit.billing.refundModal')
  @include('lab.visit.billing.discountModal')
@endif
@if(isset($order) && $view_culture && $culture_test->count())
  @include('lab.visit.culture.resultTextModal')
  @include('lab.visit.culture.bacteriaModal')
@endif
@if(isset($order))
  @include('lab.visit.results.SMSEmailModal')
  @include('lab.visit.results.SMSEmailHistoryModal')
  @if($valid_tests_cnt>0)
	   @include('lab.visit.results.validateTSTSModal')
  @endif	
@endif

@include('lab.visit.orders.orderTestsModal')	 
@endsection	
@section("scripts")
<script src="{{ asset('dist/Tafqeet.js')}}"></script>
<!-- Bootstrap file input js-->
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/filetype.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/buffer.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/piexif.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/sortable.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/locales/fr.js') }}"></script>
<script>
 $(document).ready(function(){
	var t,table,table1;
	var defaultLab = $('#selectLab').val();
	//if status is valid then show invalidate result if it has access to validate Results
	var tovalidation = '{{$validate_results?"Y":"N"}}';
	if(tovalidation=='Y' && $('#order_status').val()=='V'){
		$('#invalidateAllTests').show();
	}else{
		$('#invalidateAllTests').hide();
	}
	
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
	
	var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	}).length;
	
	$('#selected_tests').text('Selected tests are : '+nb);
	
	var valid_tests_cnt=$('#valid_tests_cnt').val();
	var tbl_valid_tsts;
	if(valid_tests_cnt>0){
	 tbl_valid_tsts=$('#validate-tsts-table').DataTable({
		paging:false,
		columns:[
		 {data:'action',titke:'action',orderable: false,searchable: false},
		 {data:'type',title:'{{__("Type")}}'},
		 {data:'group',title:'{{__("Group")}}'},
		 {data:'test',title:'{{__("Test")}}'},
		 {data:'result',title:'{{__("Result")}}'}
		]	  
		});
	
	$('#validate-tsts-table thead th').eq(0).html(
                '<input type="checkbox" id="validate_all_tsts" class="mr-2"/>'+'{{__("Validate All")}}'
            );
	
	
	// Event when search is performed
    function updateCheckAll() {
        var allChecked = $('#validate-tsts-table tbody tr').find('.validate_one_tst').length === $('#validate-tsts-table tbody tr').find('.validate_one_tst:checked').length;
        $('#validate_all_tsts').prop('checked', allChecked);
            }
	
	updateCheckAll();
	
	tbl_valid_tsts.on('search.dt', function() {
         $('#validate_all_tsts').prop('disabled', true).prop('checked', false);
		});

    // Event when the search is cleared or DataTable state is restored
    tbl_valid_tsts.on('draw.dt', function() {
         if (tbl_valid_tsts.search() === '') {
                 $('#validate_all_tsts').prop('disabled', false);
				 updateCheckAll();
               }
            });
	
	$('#validate_all_tsts').change(function(){
	   var checked = $(this).is(':checked');
	   $('#validate-tsts-table tbody tr').find('.validate_one_tst').prop('checked',checked);
	   });
	 
	
	tbl_valid_tsts.on('change', '.validate_one_tst', function() {
         //console.log("HI");
		 updateCheckAll();
            });
	}
	
	//function that checks for import button to open it if file exists
	var chk_fn ='{{isset($order) && $order->is_trial=="N"?"Y":"N"}}';
	
	if(chk_fn=='Y'){
		//Initial check on document ready
		checkFile();
		// Set interval to check every 5 minutes (300,000 milliseconds)
		var interval = 300000; // Check every 5 minutes
        setInterval(checkFile, interval);
		//do timer function
		function checkFile() {
			$.ajax({
		        url: '{{route("lab.visit.chkReceiveFile",app()->getLocale())}}',
				data:{id:$('#order_id').val(),_token:'{{csrf_token()}}'},
				type: 'POST',
				success: function(response) {
					if (response.fileExists) {
						$('#importResults').prop('disabled', false);
						
					}else{
						$('#importResults').prop('disabled', true);
						
					}
				},
				error: function(xhr, status, error) {
					console.error('Error checking file:', error);
				}
			});
         }

       
	}
	
	
	// turn into a datatable
   t = $('#tsts_tbl').DataTable({
		serverSide:true,
		processing: false,
        searching: true,
		paging: false,
        ordering: false,
		scrollY: "500px",
	    scrollX: true,
	    scrollCollapse:true,
		autoWidth: false,
		rowGroup: {
						dataSrc: 'category_name'
					},
	    ajax: {
			url: "{{ route('lab.visit.filterGroup',app()->getLocale()) }}",
			type: "POST",
			data: function (d) {
					
					d._token ='{{csrf_token()}}';
					d.order_id = $('#order_id').val();
					d.type='datatable';
							}
					},
					
					columns: [

						{data: 'id',visible:false,},
						{data:'category_name',visible:false},
						{data:'test_name',width:'30%'},
						{data:'referred_test',orderable:false,searchable:false,width:'30%'},
						{data:'insert_date',width:'15%'},
						{data: 'user_name',width:'25%'}
					   ],
	              });
				  
		t.columns.adjust();		  
	
 
 //pay and refund fillCurrency
	$('#valdollar').val('1.00');
	$('#vallira').val('0.00');
	$('#rvaldollar').val('1.00');
	$('#rvallira').val('0.00');
	$("#dvaldollar").val("1.00");
	//Define variables for input elements
 $('#profile_tests').select2({dropdownParent:'#orderTestsModal'});
 $('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
 $('.select2_data_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent:'#orderTestsModal'});
 $('.select2_pay_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent:'#paymentModal'});
 $('.select2_modal_email').select2({dropdownParent:'#SMSEmailModal',tags:true,tokenSeparators: [';'],placeholder:"{{__('Enter email addresses separated by ; (maximum : 10)')}}",maximumSelectionLength: 10 });		
 $('.select2_modal_sms').select2({dropdownParent:'#SMSEmailModal',tags:true,tokenSeparators: [';'],placeholder:"{{__('Enter telephone numbers separated by ; (maximum: 10)')}}",maximumSelectionLength: 10 });		
 $('.email_add').inputmask("email");
 
 
 var phones = [{ "mask": "########"}, { "mask": "########"}];
 $('.phone_nb').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
		});
		
 
 $('.summernote_sms_email').summernote({
								height: 250,
								toolbar: [
									['style', ['bold', 'italic', 'underline', 'clear']],
									['font', ['strikethrough']],
									['fontsize', ['fontsize']],
									['color', ['color']],
									['para', ['ul', 'ol', 'paragraph']],
									['height', ['height']]
								]
							});    
				 
				 $('.visits_tab').click(function (e) {
								e.preventDefault();
								$(this).tab('show');
							
							});

				$('.visits_tab').on("shown.bs.tab", function (e) {
					var id = $(e.target).attr("href");
					
					localStorage.setItem('visitsTab', id)
				});

				var visitsTab = localStorage.getItem('visitsTab');
				
				if (visitsTab != null) {
					$('a[data-toggle="tab"][href="' + visitsTab + '"]').tab('show');
				}else{
					$('a[data-toggle="tab"][href="#orders"]').tab('show');
				}
				
				
  
  $('#tsts_tbl').on('change', 'select.referred-test-select', function() {
        var referred_lab = $(this).val();
		var order_id=$('#order_id').val();
		var custom_id=$(this).data("id");
        $.ajax({
			url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			type:'POST',
			data:{_token:'{{csrf_token()}}',custom_id:custom_id,order_id:order_id,referred_lab:referred_lab,type:'chgRefferedLAB'},
			dataType:'JSON',
			success: function(data){
				if(data.success){
					t.ajax.reload();
					Swal.fire({toast:true,title:data.msg,icon:'success',position:'bottom-right',showConfirmButton:false,timer:1500});
				}
			}
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
		 arr = $.parseJSON('{{json_encode($profile_ids)}}');
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
		
		var is_trial = '{{isset($order) && $order->is_trial=="Y"?"Y":"N"}}';
		if(is_trial=='Y'){
			$('#is_trial').prop('checked',false);
		}else{
			$('#is_trial').prop('checked',true);
		}
	  
  });				
    
 
 $culture_exist = '{{$culture_test->count()?"Y":"N"}}';
 
 if(  $culture_exist=='Y' ){
	
	 $("#bacteriaModal").find(".sbacteria-multiple").select2({dropdownParent:'#bacteriaModal'});
	 
	 $('#tst_results_tbl').DataTable({paging:false});
	 
	 $('#bacteriaModal').on('show.bs.modal',function(){
		 var row_id = $(this).find('input[name="bact_cult_id"]').val();
		 var row = $('#culture_tbl tbody').find('#row'+row_id);
		 var bacteria_ids_txt = row.find('td.bactIDs').text().trim();
		 var bacteria_ids = bacteria_ids_txt.split(',');
		 var antibiotics_data = row.find('td:nth-child(8) .antibiotic').val();
		 
		 if(bacteria_ids.length>0){
			  			  
			  $.ajax({
			   url:'{{route("lab.visit.getBacteriaAntibiotics",app()->getLocale())}}',
			   type:'POST',
			   data:{culture_id: $('input[name="bact_cult_id"]').val(),antibiotics_data:antibiotics_data,bacteria:bacteria_ids,_token:'{{csrf_token()}}'},
			   dataType:'JSON',
			   success:function(data){
				   $('#bacteriaModal').find('.sbacteria-multiple').val(bacteria_ids).change();
				   $('#bact_ant_tbl').empty();
				   $('#bact_ant_tbl').html(data.html);
			   }
			 });
		 }
		 
		 /*$("#searchBactInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#bact_ant_tbl tbody tr").filter(function() {
			  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		  });*/
				 
		 $('.sbacteria-multiple').on('select2:select', function (e) { 
           
		   var row_id = $('#bacteriaModal').find('input[name="bact_cult_id"]').val();
		   var row = $('#culture_tbl tbody').find('#row'+row_id);
		   var antibiotics_data = row.find('td:nth-child(8) .antibiotic').val();
		 
		   
		   $.ajax({
			   url:'{{route("lab.visit.getBacteriaAntibiotics",app()->getLocale())}}',
			   type:'POST',
			   data:{culture_id: $('input[name="bact_cult_id"]').val(),antibiotics_data:antibiotics_data,bacteria:$(this).val(),_token:'{{csrf_token()}}'},
			   dataType:'JSON',
			   success:function(data){
				   $('#bact_ant_tbl').empty();
				   $('#bact_ant_tbl').html(data.html);
			   }
			   
		   });
	  
      });
		 
	  $('.sbacteria-multiple').on('select2:unselect', function (e) { 
           
		   if($(this).val().length==0){
			 $('#bact_ant_tbl tbody').empty(); 
		   }else{
		   
		   var row_id = $('#bacteriaModal').find('input[name="bact_cult_id"]').val();
		   var row = $('#culture_tbl tbody').find('#row'+row_id);
		   var antibiotics_data = row.find('td:nth-child(8) .antibiotic').val();
		   
		   $.ajax({
			   url:'{{route("lab.visit.getBacteriaAntibiotics",app()->getLocale())}}',
			   type:'POST',
			   data:{culture_id: $('input[name="bact_cult_id"]').val(),antibiotics_data:antibiotics_data,bacteria:$(this).val(),_token:'{{csrf_token()}}'},
			   dataType:'JSON',
			   success:function(data){
				   $('#bact_ant_tbl').empty();
				   $('#bact_ant_tbl').html(data.html);
			   }
			   
		   });
		   }
     });
	 
	 
	 });
	 
	 $('#bacteriaModal').on('hide.bs.modal',function(){
		 var row_id = $(this).find('input[name="bact_cult_id"]').val();
		 var row = $('#culture_tbl tbody').find('#row'+row_id);
		 var bacteria_ids = $(this).find('.sbacteria-multiple').val().join();
		 var bacteria_names = $(this).find('.sbacteria-multiple').select2("data");
		 bacteria_names = bacteria_names.map(x => x.text).join();
         row.find('td.bactNames').text(bacteria_names);
		 row.find('td.bactIDs').text(bacteria_ids);
		 var $data= [];
		 $('table').each(function(){
		  $(this).find('tbody tr').each(function(){
			 
             var x = $(this);
             var result_ant = x.find('td:nth-child(3) .cult_result').val();
             var bact_id =    Number(x.find('td:nth-child(4)').text());
			 var ant_id =     Number(x.find('td:nth-child(5)').text());
			 if(result_ant!=null && result_ant!=''){
				 $data.push([bact_id,ant_id,result_ant]);
			 }
			
		   });
		});
	   row.find('td:nth-child(8) .antibiotic').val(JSON.stringify($data));
	   var antibiotic_data = row.find('td:nth-child(8) input.antibiotic').val();
	   var culture_id = row.find('td:nth-child(1)').text();
       //update data 
       $.ajax({
			url:'{{route("lab.visit.saveCultureData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           culture_id:culture_id,
				   antibiotic_data: antibiotic_data,
				   type: 'antibiotic'
				},
			dataType:'JSON',
			success:function(data){
				//Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });	   
	 
	 });
	 
	
	$("#searchCultInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#culture_tbl tbody tr").filter(function() {
			  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		  });
	 
	 
	 function toggleTableElements(disabled) {
        $('#culture_tbl input,#culture_tbl button').prop('disabled', disabled);
      }
	 
	 $('#cancel_culture').click(function(e){
		 e.preventDefault();
		 toggleTableElements(true);
		 $(this).prop('disabled',true);
		 $('#edit_culture').prop('disabled',false);
		 $('#print_culture').prop('disabled',false);
	 });
	 $('#edit_culture').click(function(e){
		 e.preventDefault();
		 toggleTableElements(false);
		 $(this).prop('disabled',true);
		 $('#cancel_culture').prop('disabled',false);
		 $('#print_culture').prop('disabled',true);
		 /*var saveData = [];
		 $('#culture_tbl tbody tr').each(function(i){
			 var row = $(this);
			 var order_id = $('#order_id').val();
			 var culture_id = row.find('td:nth-child(1)').text();
			 var test_id = row.find('td:nth-child(2)').text();
			 var gram_staim = row.find('td:nth-child(4) input.gram_staim').val();
             var culture_result = row.find('td:nth-child(5) input.culture_result').val();
             var antibiotic_data = row.find('td:nth-child(8) input.antibiotic').val();
			 var rowData = {
              culture_id:culture_id,
			  test_id:test_id,
			  order_id: order_id,
			  gram_staim: gram_staim,
              culture_result: culture_result,
              antibiotic_data: antibiotic_data
              };
			  saveData.push(rowData);
		 });
		 
		 
		 		 
		 $.ajax({
			url:'{{route("lab.visit.saveCultureData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           culture_data: JSON.stringify(saveData),
				   type: 'all'
				},
			dataType:'JSON',
			success:function(data){
				Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });*/
	 });
	 
	 $('input.gram_staim').on('input',function(e){
		 e.preventDefault();
		 var saveData = [];
		 var row = $(this).closest('tr');
		 var culture_id = row.find('td:nth-child(1)').text();
		 var gram_staim = row.find('td:nth-child(4) input.gram_staim').val();
		 $.ajax({
			url:'{{route("lab.visit.saveCultureData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           culture_id:culture_id,
				   gram_staim: gram_staim,
				   type: 'gram_stain'
				},
			dataType:'JSON',
			success:function(data){
				//Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });
         
	 });
	 
	  $('input.culture_result').on('input',function(e){
		 e.preventDefault();
		 var saveData = [];
		 var row = $(this).closest('tr');
		 var culture_id = row.find('td:nth-child(1)').text();
		 var culture_result = row.find('td:nth-child(5) input.culture_result').val();
		 $.ajax({
			url:'{{route("lab.visit.saveCultureData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           culture_id:culture_id,
				   culture_result: culture_result,
				   type: 'culture_result'
				},
			dataType:'JSON',
			success:function(data){
				//Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });
        
	 });
 
   
	$('.openBacteriaModal').click(function(e){
		e.preventDefault();
		var row = $(this).closest('tr');
		var culture_id = row.find('td:first').text();
		$('#bacteriaModal').find('input[name="bact_cult_id"]').val(culture_id);
		$('#bacteriaModal').modal('show');
	});
	
	
	 
	 $('#print_culture').click(function(e){
		 e.preventDefault();
	     var order_id = $('#order_id').val();
		 if(order_id=='0'){
			 Swal.fire({icon:'warning',html:'{{__("Please save the order before printing")}}',customClass:'w-auto'});
			 return false;
		 }
		 
		 $.ajax({
			url:'{{route("lab.visit.printCultureData",app()->getLocale())}}',
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
					
					link.download='Culture_Order'+order_id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
	 });
 
 
 }
 
 var view_templates = '{{$view_templates && $template_reports->count()?"Y":"N"}}';
	if(view_templates=='Y'){
		
		$('.summernote').summernote({
                height: 500, 
                toolbar: [
					['style', ['bold', 'italic', 'underline', 'clear']],
					['font', ['strikethrough', 'superscript', 'subscript','fontname']],
					['fontsize', ['fontsize']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['picture']]
				  ]
              });
		
		
		  
		  var summernoteInstances = $('.summernote');
		  
		  summernoteInstances.each(function() {
            // Get the Summernote instance
            var summernoteInstance = $(this).summernote();
            summernoteInstance.summernote('disable');
           });
		  
	
	$('.editTemplate').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var tab = $(this).closest("div[id^='template']");
		tab.find('.summernote').summernote('enable');
		$(this).prop('disabled',true);
		tab.find('.cancelTemplate').prop('disabled',false);
		tab.find('.updateTemplate').prop('disabled',false);
		tab.find('.printTemplate').prop('disabled',true);
	});
	
	$('.printTemplate').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
	    $.ajax({
			url:'{{route("lab.visit.opTemplateData",app()->getLocale())}}',
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
			data:{'_token': '{{ csrf_token() }}',id:id,type:'print'},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download='Template_'+id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });		 
		
	});
	
	$('.cancelTemplate').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var tab = $(this).closest("div[id^='template']");
		$.ajax({
			url:'{{route("lab.visit.opTemplateData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           id:id,
				   type:'cancel'
				},
			dataType:'JSON',
			success:function(data){
                 tab.find('.summernote').summernote('code',data.description);
				 tab.find('.summernote').summernote('disable');
		         tab.find('.editTemplate').prop('disabled',false);
				 tab.find('.printTemplate').prop('disabled',false);
		         tab.find('.updateTemplate').prop('disabled',true);
		         $(this).prop('disabled',true);			
				 }
			 
		 });	   
		
	});
	
	$('.updateTemplate').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var tab = $(this).closest("div[id^='template']");
		var description = tab.find('.summernote').val();
		$.ajax({
			url:'{{route("lab.visit.opTemplateData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		           id:id,
				   type:'update',
				   description:description
				},
			dataType:'JSON',
			success:function(data){
				Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });	   
		
	});
	
	
	}
 
 
 
 
 
 if($('#example-table').length){	
	
	

  /*table = $('#example-table').DataTable({
				paging: false,
				ordering: false,
				searching: true
	               });*/
				  
		//table.columns.adjust();		        
 
  function filterTable(searchText) {
                searchText = searchText.toLowerCase();

                // First pass: Determine visibility of rows
                $('#example-table tbody tr').each(function() {
                    const $row = $(this);
                    const isGroupHeader = $row.find('th').attr('colspan');

                    if (isGroupHeader) {
                        // For group headers, initially hide them
                        $row.hide();
                    } else {
                        // For data rows, check if they should be visible
                        const rowText = $row.text().toLowerCase();
                        const isVisible = rowText.includes(searchText) || searchText === '';

                        $row.toggle(isVisible);
                    }
                });

                // Second pass: Show group headers based on visible rows
                $('#example-table tbody tr').each(function() {
                    const $row = $(this);
                    const isGroupHeader = $row.find('th').attr('colspan');

                    if (isGroupHeader) {
                        // Get the group header and its related rows
                        const $groupHeader = $row;
                        let groupRowsVisible = false;

                        // Check if any following rows are visible
                        $row.nextUntil('tr:has(th[colspan])').each(function() {
                            if ($(this).is(':visible')) {
                                groupRowsVisible = true;
                                return false; // Break out of each loop
                            }
                        });

                        // Show or hide the group header based on the visibility of its rows
                        $groupHeader.toggle(groupRowsVisible);
                    }
                });
            }  
 $('#search_results').on('input',function(e){
	 filterTable($(this).val());
});
// Initial filter to ensure correct state
            //filterTable('');
 
 var isTabOrEnterPressed = false;
 var inputValue = '';
 
 $('#example-table').on('input','input.resultVal',function(e) {
	 inputValue =$(this).val();
 });


$('#example-table').on('keydown', 'input.resultVal', function(e) {
	  var $clickedRow = $(this).closest('tr');
	 if((e.key === 'Enter' || e.key==='Tab' || e.key=='ArrowDown' || e.key=='ArrowUp') 
		 &&  inputValue!=='' && inputValue !== $(this).val()){
       	getStatus($clickedRow,e.key);
		isTabOrEnterPressed = true;    
	 }
	 
	 if(e.key === 'Enter' || e.key=='ArrowDown' || e.key=='ArrowUp'){	
				
			  	var $nextRow;
				if (e.key === 'ArrowUp') {
						$nextRow = $clickedRow.prev();
					}else{
						$nextRow = $clickedRow.next();
					} 
				  // Skip over column group rows
					  while ($nextRow.find('th[colspan]').length > 0) {
						if (e.key === 'ArrowUp') {
								$nextRow = $nextRow.prev();
							}else{
								$nextRow = $nextRow.next();
							}
					   }
					   
					   while ($nextRow.find('td[colspan]').length > 0) {
						if (e.key === 'ArrowUp') {
								$nextRow = $nextRow.prev();
							}else{
								$nextRow = $nextRow.next();
							}
					   }
					
					 var $resultValInput = $nextRow.find('input.resultVal');
					  //console.log($resultValInput);
					 if ($resultValInput.length > 0) {
						$resultValInput.focus();
					  } else {
						
						return;
						
					  } 
		}
   
   });
   
   $('#example-table').on('change', 'input.resultVal', function(e) {
	
    if (isTabOrEnterPressed) {
        //console.log("Change triggered by Tab or Enter");
        isTabOrEnterPressed = false;
    } else {
       
		var $clickedRow = $(this).closest('tr');
		getStatus($clickedRow,null);
        //console.log("Change not triggered by Tab or Enter");
    }
   
});
	   
   
  function getStatus($clickedRow,key){
	  
	  var ResultVal = $clickedRow.find('.resultVal').val();
           
	if($clickedRow.find('.test_type').text()!='F' && !isNaN(ResultVal)){
		     var CodeId = $clickedRow.data('test-id');
		     
			 var formulaResult = '';
		     //check for formula or calculate for this row in order to update their values
			 var $formulaRows = $('#example-table tbody tr').filter(function() {
                var $row = $(this);
                var code1 = $row.data('code1');
                var code2 = $row.data('code2');
                var code3 = $row.data('code3');
                var code4 = $row.data('code4');
			    return code1 == CodeId || code2 == CodeId || code3 == CodeId || code4 == CodeId;
              });
		                 
		     
		     if ($formulaRows.length > 0) {
			  
			   $formulaRows.each(function(){
				 var $formulaRow = $(this);
				 var code1 = $formulaRow.data('code1');
                 var code2 = $formulaRow.data('code2');
                 var code3 = $formulaRow.data('code3');
                 var code4 = $formulaRow.data('code4');
			   
			     var dec_pts = $formulaRow.find('.dec_pts').text();
			   
            
                 var code1Value = parseFloat($('tr[data-test-id="'+code1+'"] .resultVal').val());
                 var code2Value = parseFloat($('tr[data-test-id="'+code2+'"] .resultVal').val());
                 var code3Value = parseFloat($('tr[data-test-id="'+code3+'"] .resultVal').val());
                 var code4Value = parseFloat($('tr[data-test-id="'+code4+'"] .resultVal').val()) ;
            
			     var formula = $formulaRow.data('formula');
                 
				  function codeExistsAndNotEmpty(code, value) {
                     return formula.includes(code) && !isNaN(value);
                   } 
			      if (codeExistsAndNotEmpty('code1', code1Value)) {
                       formula = formula.replace('code1', code1Value);
                    } 
                  if (codeExistsAndNotEmpty('code2', code2Value)) {
                      formula = formula.replace('code2', code2Value);
                      }

                 if (codeExistsAndNotEmpty('code3', code3Value)) {
                     formula = formula.replace('code3', code3Value);
                    }
				 if (codeExistsAndNotEmpty('code4', code4Value)) {
                      formula = formula.replace('code4', code4Value);
                      }	
                 
				 if (!formula.includes('code1') && 
				     !formula.includes('code2') && 
					 !formula.includes('code3') && 
					 !formula.includes('code4') ) {
                     var result = eval(formula);
			    	   if(dec_pts !=null && dec_pts!='' && !isNaN(dec_pts)){
					     result = parseFloat(result.toFixed(dec_pts));
				      }else{
					    if(Number.isFinite(result) && Math.abs(result % 1) > 0.01) {
						result = parseFloat(result.toFixed(2));
						} 
				       }
				    
   
					   var test_type = $formulaRow.find('.test_type').text();
					   	   if(test_type=='F'){       
							   $formulaRow.find('.resultVal').val(result);
							   formulaResult=result;
							   getFormulaData($formulaRow,formulaResult,'F');
						   }else{
							   if(test_type=='C'){
								  $formulaRow.find('.calc_result').text(result);
								  formulaResult=result;
								  getFormulaData($formulaRow,formulaResult,'C');
							   }
						   }
				        }else{
							//do nothing
						}
					  });
			       
				  getCellData($clickedRow,ResultVal,'N');
			   }else{
				 getCellData($clickedRow,ResultVal,'N');    
			   }
		     }else{
				 if($clickedRow.find('.test_type').text()!='F' && isNaN(ResultVal)){
					getCellData($clickedRow,ResultVal,'T');   
				 }
			 }

            
  }
  
  function getFormulaData(formulaRow,formulaResult,type) {
	var formula_type = formulaRow.find('.test_type').text();
	var formula_id = parseInt(formulaRow.find('.result_id').text());
	var formula_result = formulaResult;
	
	$.ajax({
        url: '{{route("lab.visit.checkResultVal",app()->getLocale())}}',
        method: "post",
        data: {
     		formula_id:formula_id,
			formula_type:formula_type,
			formula_result:formula_result,
			type:type,
            _token: '{{csrf_token()}}'
        },
        dataType: "JSON",
        success: function(data) {
              formulaRow.find('.result_status').text(data.state);
			  formulaRow.find('.sign').text(data.sign);
			  formulaRow.find('.resultVal').removeClass("bginfo bgorange bgred bgblue");
			 switch (data.state) {
					case 'H':
						formulaRow.find('.resultVal').addClass("bgred");
						break;
					case 'L':
						formulaRow.find('.resultVal').addClass("bgblue");
						break;
					case 'PL':
					case 'PH':
						formulaRow.find('.resultVal').addClass("bgorange");
						break;
					default:
						formulaRow.find('.resultVal').addClass("bginfo");
				   }
		      
		
		
		}
    });
}
    
  function getCellData(row,value,type) {
   
	var result_value = value;
	var result_id = row.find('.result_id').text();
    var test_id = row.find('.test_id').text();
	
	
	$.ajax({
        url: '{{route("lab.visit.checkResultVal",app()->getLocale())}}',
        method: "post",
        data: {
            id: result_id,
            result: result_value,
            test_id: test_id,
           	type:type,
			_token: '{{csrf_token()}}'
        },
        dataType: "JSON",
        success: function(data) {
           if(type=='N'){ 	
			row.find('.result_status').text(data.state);
			row.find('.sign').text(data.sign);
			row.find('.resultVal').removeClass("bginfo bgorange bgred bgblue");
		    var msg='';
			switch (data.state) {
					case 'H':
					   if(data.sign=='{{__("High")}}'){
						   row.find('.resultVal').addClass("bgred");	
						   msg = 'Test Result is High';
						}else{	
						  row.find('.resultVal').addClass("bginfo");    
						  msg='';
						}
					break;
					case 'L':
						
						if(data.sign=='{{__("Low")}}'){
						   row.find('.resultVal').addClass("bgblue");
							msg = 'Test Result is Low';
						}else{	
						  row.find('.resultVal').addClass("bginfo");  
						  msg='';
						}
						break;
					case 'PL':
						row.find('.resultVal').addClass("bgorange");
						msg = 'Test Result is Panic Low !';
						break;
					case 'PH':
						row.find('.resultVal').addClass("bgorange");
						msg = 'Test Result is Panic High !';
						break;
					default:
						row.find('.resultVal').addClass("bginfo");
						msg='';
				   }
			   if(msg!=''){
			    Swal.fire({toast:true,title:msg,icon:'warning',timer:1500,position:'bottom-right',showConfirmButton:false});
		       }
			 
			 }
			
		
		}
    });
}

function moveToNextInput(input) {
    var $cell = $(input).closest('td');
    var $nextRow = $cell.closest('tr').nextAll(':visible').first();
    var $nextCell = $nextRow.find('td').eq($cell.index());

    if ($nextCell.length > 0) {
        var $nextInput = $nextCell.find('input').first(); 
        if ($nextInput.length === 0) {
            $nextInput = $nextCell.find('select').first(); 
        }

        if ($nextInput.length > 0) {
            $nextInput.focus();
        } else {
            moveToNextInput($nextCell); 
        }
    } else {
        return;
    }
}
  
 $('#importResults').click(function(e){
	e.preventDefault();
    var order_id = $('#order_id').val();
    $.ajax({
		url: '{{route("lab.visit.importData",app()->getLocale())}}',
		type: 'POST',
		data: {_token:'{{csrf_token()}}',order_id:order_id},
		dataType: 'JSON',
		success: function(data){
			if(data.error){
				Swal.fire({icon:'warning',html:data.error,customClass:'w-auto'});
				return false;
			}else{
				if(data.success){
			 	Swal.fire({toast:true,icon:'success',title:data.success,timer:800,position:'bottom-right',showConfirmButton:false}).then(function(){
					window.location.href = data.location;
				});
			    }
			}
		}
	});	
 });
 
 
 
 $('#editResults').click(function(e){
	 e.preventDefault();
	 $(this).prop('disabled',true);
	 
	 $('#cancelResults').prop('disabled',false);
	 
     
	 var status = $('#order_status').val();
	 var valid = status=='V'?'Y':'N';
	 //if(status=='F'){$('#validateAllTests').prop('disabled',false);}
	 //else{$('#validateAllTests').prop('disabled',true);}
	  $('#doneResults').prop('disabled',true);
	  
	  if($('#validateAllTests').is(':visible')){
	   $('#validateAllTests').prop('disabled',true);
	  }
	  
	  $('#printResults').prop('disabled',true);
	  
	  if(valid=='Y'){
	   $('#fixed_comment').prop('disabled',true);
       //$('#saveResults').prop('disabled',true);
	   //$('#importResults').prop('disabled',true);
	   //$('#sendResults').prop('disabled',false);
	   //$('#sendResultsInfo').prop('disabled',false);
       $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
	   
	  }else{
	   $('#fixed_comment').prop('disabled',false);
	   //$('#saveResults').prop('disabled',false);
	   //$('#importResults').prop('disabled',false);
	   //$('#sendResults').prop('disabled',true);
	   //$('#sendResultsInfo').prop('disabled',true);
	   $('#example-table tbody tr').find('input.resultVal').prop('disabled',false);
	  
	  }
     var valid_tests_cnt = $('#valid_tests_cnt').val();
	 //added
	  if(valid_tests_cnt>0){ 
	   $('#example-table').find('#validateAllRslts').prop('disabled',true);
	   $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
	  }
 });

 $('#cancelResults').click(function(e){
	 e.preventDefault();
	 var status = $('#order_status').val();
	 $('#editResults').prop('disabled',false);
	 $(this).prop('disabled',true);
	  $('#doneResults').prop('disabled',false);
	 //$('#saveResults').prop('disabled',true);
	 var valid_tests_cnt = $('#valid_tests_cnt').val(); 
	  if(valid_tests_cnt>0){
	  if(status=='F'){
	   	//added
	    $('#example-table').find('#validateAllRslts').prop('disabled',false);
	    $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',false);
	   }else{
		
		//added
	    $('#example-table').find('#validateAllRslts').prop('disabled',true);
	    $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
	   }
	 } 
	  
	 $('#printResults').prop('disabled',false);
	  
	 //$('#importResults').prop('disabled',true);
	 //$('#doneResults').prop('disabled',true);
	 //if($('#validateAllTests').is(':visible')){$('#validateAllTests').prop('disabled',true);}
	
	 $('#fixed_comment').prop('disabled',true);
	 $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
	 
 });   
 
 var originalStates = {};
 $('.validateOneRslt').each(function(index) {
       originalStates[index] = $(this).prop('checked');
        });
  
function restoreStates() {
                $('.validateOneRslt').each(function(index) {
                    $(this).prop('checked', originalStates[index] || false);
                });
                $('#validateAllRslts').prop('checked', $('.validateOneRslt:checked').length === $('.validateOneRslt').length);
            }			
 
 $('#validateAllRslts').change(function(){
	  var order_id = $('#order_id').val();
	  var has_validation='{{$validate_results?"Y":"N"}}';
		 $.ajax({
			 url:'{{route("lab.visit.newValidation",app()->getLocale())}}',
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
			 data:{_token:'{{csrf_token()}}',order_id:order_id,req_type:'AllTSTs'},
			 type:'POST',
			 dataType:'JSON',
			 success: function(data){
				if(data.success){
					//results part
					//added
					$('#example-table').find('#validateAllRslts').prop('disabled',true);
					$('#example-table').find('#validateAllRslts').prop('checked',true);
					$('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
					$('#example-table tbody tr').find('input.validateOneRslt').prop('checked',true);
					
					$('#editResults').prop('disabled',true);
					$('#cancelResults').prop('disabled',true);
					//end added
					$('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
					//change all tests to checked 
					$('#order_status_name').val('{{__("Validated")}}');
					$('#order_status').val('V');
					$('#sendResults').prop('disabled',false);
					$('#sendResultsInfo').prop('disabled',false);
					$('#fixed_comment').prop('disabled',true);
					$('#doneResults').prop('disabled',true);
					if(has_validation=='Y'){ $('#invalidateAllTests').show(); }
					
					//bill part
					$('#finalizeBill').prop('disabled',true);
					$('#saveBill').prop('disabled',true);
					$('#btnpayment').prop('disabled',true);
					$('#btnrefund').prop('disabled',true);
					$('#btndiscount').prop('disabled',true);
					
					//order
					$('#printOrder').prop('disabled',true);
					$('#testsListBtn').prop('disabled',true);
					$('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',true);
					
					//documents
					$('#input_files').fileinput('disable');
					$('#docs_descrip').prop('disabled',true);
					$('#documents_upload').hide();
					$('.attachIMG').hide();
					
					//disable doctor and GUARANTOR
					$('#selectLab').prop('disabled',true);
					$('#selectDoc').prop('disabled',true);
					
					//Culture
					var $cultureDivs = $('.tab-pane > [id="culture"]');
					if($cultureDivs.length){ 
					  $('#edit_culture').prop('disabled',false);
					  $('#cancel_culture').prop('disabled',true);
					  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
						 }
					//templates
					var $templateDivs = $('.tab-pane > [id^="template"]');
					if($templateDivs.length){ 
					  $('.editTemplate').prop('disabled',false);
					  $('.cancelTemplate').prop('disabled',true);
					  $('.summernote').each(function() {
							$(this).summernote('disable');
							});
					 }
					//update sms package
					$('#sms_pack').val(data.sms_pack);
					
					Swal.fire({toast:true,title:data.msg,timer:3000,icon:'success',position:'bottom-right',showConfirmButton:false}); 
				}else{
					//procedure an error occured in sending data
					$('#example-table').find('#validateAllRslts').prop('checked',true);
					$('#example-table').find('#validateOneRslt').prop('checked',false);
					Swal.fire({toast:true,html:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
				}
			 }
		});
 });


 $('#example-table').on('change','input.validateOneRslt',function(e){
	 
	 var checked = $(this).is(':checked');
	 var len1 = $('#example-table').find('input.validateOneRslt:checked').length;
	 var len2 = $('#example-table').find('input.validateOneRslt').length;
	
	 if(len1==len2){
		
		 //validate all procedure send email and sms
		 var id = $(this).closest('tr').data('id');
		 var type = $(this).data('type');
		 var need_validation = 'N';
		 var has_validation = '{{$validate_results?"Y":"N"}}';
		 var order_id = $('#order_id').val();
		 $.ajax({
			 url:'{{route("lab.visit.newValidation",app()->getLocale())}}',
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
			 data:{_token:'{{csrf_token()}}',order_id:order_id,id:id,type:type,req_type:'AllTSTs'},
			 type:'POST',
			 dataType:'JSON',
			 success: function(data){
				if(data.success){
					//results part
					//added
					$('#example-table').find('#validateAllRslts').prop('disabled',true);
					$('#example-table').find('#validateAllRslts').prop('checked',true);
					$('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
					$('#example-table tbody tr').find('input.validateOneRslt').prop('checked',true);
					
					$('#editResults').prop('disabled',true);
					$('#cancelResults').prop('disabled',true);
					//end added
					$('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
					//change all tests to checked 
					$('#order_status_name').val('{{__("Validated")}}');
					$('#order_status').val('V');
					$('#sendResults').prop('disabled',false);
					$('#sendResultsInfo').prop('disabled',false);
					$('#fixed_comment').prop('disabled',true);
					$('#doneResults').prop('disabled',true);
					if(has_validation=='Y'){ $('#invalidateAllTests').show(); }
					//bill part
					$('#finalizeBill').prop('disabled',true);
					$('#saveBill').prop('disabled',true);
					$('#btnpayment').prop('disabled',true);
					$('#btnrefund').prop('disabled',true);
					$('#btndiscount').prop('disabled',true);
					
					//order
					$('#printOrder').prop('disabled',true);
					$('#testsListBtn').prop('disabled',true);
					$('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',true);
					
					//documents
					$('#input_files').fileinput('disable');
					$('#docs_descrip').prop('disabled',true);
					$('#documents_upload').hide();
					$('.attachIMG').hide();
					
					//disable doctor and GUARANTOR
					$('#selectLab').prop('disabled',true);
					$('#selectDoc').prop('disabled',true);
					
					//Culture
					var $cultureDivs = $('.tab-pane > [id="culture"]');
					if($cultureDivs.length){ 
					  $('#edit_culture').prop('disabled',false);
					  $('#cancel_culture').prop('disabled',true);
					  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
						 }
					//templates
					var $templateDivs = $('.tab-pane > [id^="template"]');
					if($templateDivs.length){ 
					  $('.editTemplate').prop('disabled',false);
					  $('.cancelTemplate').prop('disabled',true);
					  $('.summernote').each(function() {
							$(this).summernote('disable');
							});
					 }
					//update sms package
					$('#sms_pack').val(data.sms_pack);
					
					Swal.fire({toast:true,title:data.msg,timer:3000,icon:'success',position:'bottom-right',showConfirmButton:false}); 
				}else{
					//procedure an error occured in sending data
					$('#example-table').find('#validateAllRslts').prop('checked',true);
					$('#example-table').find('#validateOneRslt').prop('checked',false);
					Swal.fire({toast:true,html:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
				}
			 }
			 });
	 }else{
		 var id = $(this).closest('tr').data('id');
		 var type = $(this).data('type');
		 var need_validation = $(this).is(':checked')?'N':'Y';
		 $.ajax({
			 url:'{{route("lab.visit.newValidation",app()->getLocale())}}',
			 data:{_token:'{{csrf_token()}}',id:id,type:type,req_type:'OneTST',need_validation:need_validation},
			 type:'POST',
			 dataType:'JSON',
			 success: function(data){
				if(data.success){//do nothing
				}else{
				Swal.fire({toast:true,title:data.msg,timer:1000,icon:'success',position:'bottom-right',showConfirmButton:false}); 
			    }
			 }
			 });
	 } 
 });

}
  
 if($('#bill-table').length){ 
 
 var TotalDT = function(values, data, calcParams){
    var data = "Total :";
    return data;
}

$('#discountModal').on('show.bs.modal',function(){ 
 var txt = $('#selectmethoddiscount option:selected').text().trim();
	if(txt=='Percentage'){
		$('#selectcurrencyd').prop('disabled',true);
	}else{
		$('#selectcurrencyd').prop('disabled',false);
	}
});
 
 table1 = new Tabulator("#bill-table", {
    
	ajaxURL:"{{route('lab.visit.getBill',[app()->getLocale()])}}", //ajax URL
    ajaxParams: function(){
        var filter_order=$('#order_id').val();
		return {filter_order:filter_order};
           },
	height:"400px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:false, //enable pagination.
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	
	downloadConfig:{
        columnHeaders:true, //do not include column headers in downloaded table
        columnGroups:true, //do not include column groups in column headers for downloaded table
        rowGroups:true, //do not include row groups in downloaded table
        columnCalcs:true, //do not include column calcs in downloaded table
        dataTree:false, //do not include data tree in downloaded table
    },
	printConfig:{
        columnHeaders:true, //do not include column headers in printed table
        columnGroups:true, //do not include column groups in column headers for printed table
        rowGroups:true, //do not include row groups in printed table
        columnCalcs:true, //do not include column calcs in printed table
        dataTree:false, //do not include data tree in printed table
        formatCells:true, //show raw cell values without formatter
    },
	
	columns:[
		{title:"{{__('#')}}", field:"bill_num",visible:false},
		{title:"{{__('Test Name')}}",field:"bill_name",topCalc:TotalDT,headerFilter:"input"},
		{title:"{{__('Bill')}}", field:"clinic_bill_num"},
		{title:"{{__('Date')}}", field:"bill_datein",formatter:function(cell, formatterParams, onRendered){
			var val = moment(cell.getValue()).format('DD/MM/YYYY');
			return val;
		}},
		{title:"{{__('Test')}}", field:"bill_code",headerFilter:"input",visible:false},
		{title:"{{__('CNSS')}}", field:"cnss",headerFilter:"input"},
		{title:"{{__('Cost USD')}}",field:"test_cost",visible:false,formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"$",
                symbolAfter:"p",
				},headerFilter:"input"}, 
		{title:"{{__('Nb L')}}",field:"bill_quantity",visible:false,headerFilter:"input"},
		{title:"{{__('Price USD')}}",field:"bill_price",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
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
		{title:"{{__('Price LBP')}}",field:"lbill_price",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
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
       {title:"{{__('Price EURO')}}",field:"ebill_price",visible:false,topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"€",
				symbolAfter:"p",
				negativeSign:false,
				},formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"€",
                symbolAfter:"p",
				},headerFilter:"input"}, 
	   {formatter:"buttonCross",  title:"Action", headerSort:false, cellClick:function(e, cell){
	                  var status=$('#order_status').val();
					  
					  if(status=='V')
					  {return false;}
					  else{
					  if(confirm('Are you sure you want to delete this entry?'))
		                  cell.getRow().delete();
					    }
						},frozen:true
               },
	      ],
    });
 }
	
	
	$("#input_files").fileinput({
		'language': 'en',
		'maxFileCount': 10,
		'maxFileSize':'3072',
		'hideThumbnailContent':true,
		'showPreview':true,
		'showUpload': true,
		'showRemove ':true,
		'showBrowse':true,
		'browseOnZoneClick' : true,
		'allowedFileExtensions': ["jpg","jpeg","png","gif","bmp","tiff","svg","webp","pdf"]
	});
	
	if($('#order_status').val()=='V'){
		$("#input_files").fileinput('disable');
		$('#docs_descrip').prop('disabled',true);
		$('.attachIMG').hide();
	}else{
		$("#input_files").fileinput('enable');
		$('#docs_descrip').prop('disabled',false);
		$('.attachIMG').show();
	}
	  
	$("#selectLab").change(function()
        {
			var ext_lab = $('#selectLab').val();
			if(ext_lab==null || ext_lab==''){
				$(this).val(defaultLab).trigger('change');
				Swal.fire({text:'{{__("Please choose a guarantor")}}',icon:'warning',customClass:'w-auto'});
				return false;
			}
			
			var order_id = $('#order_id').val();
			var bill_id = $('#bill_id').val();
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("UpdateLab",app()->getLocale())}}',
			   data:{"order_id":order_id,"ext_lab":ext_lab,bill_id:bill_id,"type":"guarantor"},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){	
			    if(data.change_bill){
				  /*table1.setData("{{route('lab.visit.getBill',app()->getLocale())}}", {filter_order:order_id});
				  $("#payamountrefund").val(data.sumpay);
			      $("#refamountrefund").val(data.sumref);
			      $("#balancerefund").val(data.nbalance);
			      $("#payamount").val(data.sumpay);
			      $("#refamount").val(data.sumref);
			      $("#balancepay").val(data.nbalance);
			  	  $("#payamountdiscount").val(data.sumpay);
			      $("#refamountdiscount").val(data.sumref);
			      $("#balancediscount").val(data.nbalance);
			 	  $("#tpay").val(data.sumpay);
			      $("#trefund").val(data.sumref);
			      $("#balance").val(data.nbalance);
			      $("#tdiscount").val(data.tdiscount);
			  	  $('#tpayd').val(data.sumpayd);
			      $('#trefd').val(data.sumrefd);
			      $('#balanced').val(data.balanced);
			      $('#tdiscountd').val(data.tdiscountd);
				 if(data.balanced==0){
					 $('#saveBill').prop('disabled',true);
					 $('#btnrefund').prop('disabled',true); 
					 $('#btnpayment').prop('disabled',true); 	
					 $('#btndiscount').prop('disabled',true);
					 $('#finalizeBill').prop('checked',true);				 
				  }else{
					 $('#saveBill').prop('disabled',false);
					 $('#btnrefund').prop('disabled',false); 
					 $('#btnpayment').prop('disabled',false); 	
					 $('#btndiscount').prop('disabled',false);
					 $('#finalizeBill').prop('checked',false);	 				 
				  }*/
				  Swal.fire({icon:'success',toast:true,timer:1500,position:'bottom-right',
				             title:data.msg,showConfirmButton:false}).then(function() {
                                  window.location.reload(); 
                                  });
				}else{
				 Swal.fire({icon:'success',toast:true,timer:1500,position:'bottom-right',title:data.msg,showConfirmButton:false});			   
			    }
			 }
            });
		});	
		
	$("#selectDoc").change(function()
        {
			var doctor_num = $('#selectDoc').val();
			var order_id = $('#order_id').val();
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("UpdateLab",app()->getLocale())}}',
			   data:{"order_id":order_id,"doctor_num":doctor_num,"type":"doctor"},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){	
                Swal.fire({icon:'success',toast:true,timer:1500,position:'bottom-right',title:data.msg,showConfirmButton:false});			   
			 }
            });
		});		
  
   
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
		 arr = $.parseJSON('{{json_encode($profile_ids)}}');
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
	  var is_trial = '{{isset($order) && $order->is_trial=="Y"?"Y":"N"}}';
		if(is_trial=='Y'){
			$('#is_trial').prop('checked',false);
		}else{
			$('#is_trial').prop('checked',true);
		}
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
				  url:'{{route("lab.visit.saveOrder",app()->getLocale())}}',
				   data:{
					   order_id:order_id,
					   clinic_num:'{{$clinic->id}}',
					   patient_num:'{{$patient->id}}',
					   ext_lab: $('#selectLab').val(),
					   doctor_num:$('#selectDoc').val(),
					   is_trial: $('#orderTestsModal').find('#is_trial').is(':checked')?'N':'Y',
					   tests: JSON.stringify(dataToSave)
					     },
				   type: 'post',
				   dataType: 'json',
				   success: function(data){
					
					 
					 Swal.fire({ toast:true,title: data.msg,icon: "success",position:'bottom-right',timer:1000,
					 showConfirmButton:false }).then(function(){
						  $('#orderTestsModal').modal('hide');
						 window.location.href = data.location;
					 });
					 
				 
				 }
				});  
			 

		
   });
   
   $('#go_results').click(function(e){
	   e.preventDefault();
	   $('a[data-toggle="tab"][href="#bill"]').tab('show');
   });
   
   $('#saveResults').click(function(e){
	    e.preventDefault();
		var data = table.rows().data().toArray();
		var result_data = data.map(function(row) {
               var id = row[0];
			   var result = row[16];
			   var sign = row[5];
			   var result_status=row[9];
			   var calc_unit=row[14];
			   var calc_result=row[13];
			   var field_num = row[15];
			   var ref_range=row[6];
			   var test_type = row[8];
              return {id: id,test_type:test_type,result: result,result_status:result_status,sign:sign,calc_unit:calc_unit,calc_result:calc_result,ref_range:ref_range,field_num:field_num};
            });
        var order_id = $('#order_id').val();
	    
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("lab.visit.saveResults",app()->getLocale())}}',
			data:{order_id:order_id,result_data:result_data,fixed_comment:$('#fixed_comment').val()},
			type: 'post',
			dataType: 'json',
			success: function(data){
				 //$('#validateResults').removeAttr('disabled'); 
				 Swal.fire({toast:true,title:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
			     
			}
		});
   });
   
  
  $('#finalizeBill').change(function(e){
	  e.preventDefault();
	  var checked = $(this).is(':checked');
	  var balance = parseFloat($('#balanced').val());
	  if(checked){
		 if(balance!=0){
			 Swal.fire({text:'{{__("There is")}}'+' '+balance+'$ '+'{{__("remaining balance, please check it before finalizing the bill!")}}',icon:'warning',customClass:'w-auto'});
			 $(this).prop('checked',false); 
			 return;
		 }
		 $('#saveBill').prop('disabled',true);
         $('#btnrefund').prop('disabled',true); 
         $('#btnpayment').prop('disabled',true); 	
         $('#btndiscount').prop('disabled',true);
         		 
	  }else{
		 $('#saveBill').prop('disabled',false);
         $('#btnrefund').prop('disabled',false); 
         $('#btnpayment').prop('disabled',false); 	
         $('#btndiscount').prop('disabled',false); 	
	  }
  });
  
  
  $('#saveBill').click(function(e){
	e.preventDefault();
	var bill_id=document.getElementById("bill_id").value;
	var data = table1.getData();
	var order_id = $('#order_id').val();
	//console.log(bill_id);
	$.ajax({
		type:'POST',
		data:{_token: '{{ csrf_token() }}',bill_id:bill_id,data:JSON.stringify(data),order_id:order_id},
		url: '{{route("lab.visit.saveBill",app()->getLocale())}}',
		success: function(data){
			var order_id = $('#order_id').val();
			table1.setData("{{route('lab.visit.getBill',app()->getLocale())}}", {filter_order:order_id});
			
			 $("#payamountrefund").val(data.sumpay);
			 $("#refamountrefund").val(data.sumref);
			 $("#balancerefund").val(data.nbalance);
			 
			 $("#payamount").val(data.sumpay);
			 $("#refamount").val(data.sumref);
			 $("#balancepay").val(data.nbalance);
			 
			 $("#payamountdiscount").val(data.sumpay);
			 $("#refamountdiscount").val(data.sumref);
			 $("#balancediscount").val(data.nbalance);
			 
			 $("#tpay").val(data.sumpay);
			 $("#trefund").val(data.sumref);
			 $("#balance").val(data.nbalance);
			 $("#tdiscount").val(data.tdiscount);
			 
			 $('#tpayd').val(data.sumpayd);
			 $('#trefd').val(data.sumrefd);
			 $('#balanced').val(data.balanced);
			 $('#tdiscountd').val(data.tdiscountd);

             if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
			
			Swal.fire({toast:true,title:data.msg,icon:'success',position:'bottom-right',showConfirmButton:false,timer:3000});
		}
		});	
      });	
   
   $('#invalidateAllTests').click(function(e){
	   var order_id = $('#order_id').val();
	   var valid_tests_cnt = $('#valid_tests_cnt').val();
	   var msg = (valid_tests_cnt>0)?'<div>This action will invalidate all your results and returns its status to <b>Finished</b>.</div>':'<div>This action will invalidate all your results and returns its status to <b>Pending</b>.</div>';
	   var is_valid = valid_tests_cnt>0?'IF':'IP';
	   var has_validation = '{{$validate_results?"Y":"N"}}';
	   Swal.fire({
            title: 'Are you sure?',
            html: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, invalidate it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
               performAjaxRequest1();
            }
        });
		
		// Perform your AJAX request
			function performAjaxRequest1(){
			$.ajax({
				url:'{{route("lab.visit.validateResults",app()->getLocale())}}',
				data:{_token:'{{csrf_token()}}',order_id:order_id,is_valid:is_valid,type:'finishProcess'},
			    type: 'post',
			    dataType: 'json',
				success: function(data) {
					switch(data.return_status){
						 case 'P':
						    $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
							$('#doneResults').prop('disabled',false);
							$('#doneResults').prop('checked',false);
							$('#editResults').prop('disabled',false);
							$('#cancelResults').prop('disabled',true);
							$('#sendResultsInfo').prop('disabled',true);
							//added
							if(has_validation=='Y' && valid_tests_cnt>0){
							 $('#example-table').find('#validateAllRslts').prop('checked',false);
							 $('#example-table').find('#validateAllRslts').prop('disabled',true);
							 $('#example-table tbody tr').find('input.validateOneRslt').prop('checked',false);
							 $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
							 }
							$('#invalidateAllTests').hide();
							
							$('#order_status_name').val('{{__("Pending")}}');
					        $('#order_status').val('P');
							$('#sendResults').prop('disabled',true);
							$('#fixed_comment').prop('disabled',false);
							
							//bill part
							  $('#finalizeBill').prop('disabled',false);
							  $('#saveBill').prop('disabled',false);
							  $('#btnpayment').prop('disabled',false);
							  $('#btnrefund').prop('disabled',false);
							  $('#btndiscount').prop('disabled',false);
							  
							 
							  //order
							  $('#printOrder').prop('disabled',false);
							  $('#testsListBtn').prop('disabled',false);
							  $('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',false);
							  
							  //disable doctor and GUARANTOR
							  $('#selectLab').prop('disabled',false);
							  $('#selectDoc').prop('disabled',false);
							  
							  //documents
							  $('#input_files').fileinput('enable');
							  $('#docs_descrip').prop('disabled',false);
							  $('#documents_upload').show();
							  $('.attachIMG').show();
							  
							 //Culture
								 var $cultureDivs = $('.tab-pane > [id="culture"]');
								 if($cultureDivs.length){ 
								  $('#edit_culture').prop('disabled',false);
								  $('#cancel_culture').prop('disabled',true);
								  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
								 }
								 
								  //templates
								 var $templateDivs = $('.tab-pane > [id^="template"]');
								 if($templateDivs.length){ 
								  $('.editTemplate').prop('disabled',false);
								  $('.cancelTemplate').prop('disabled',true);
								  $('.summernote').each(function() {
										$(this).summernote('disable');
									});
								 }
						 break;
						 case 'F':
								$('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
								$('#doneResults').prop('disabled',false);
								$('#doneResults').prop('checked',true);
								$('#editResults').prop('disabled',false);
							    $('#cancelResults').prop('disabled',true);
								$('#sendResultsInfo').prop('disabled',false);
								
								//added
								if(has_validation=='Y' && valid_tests_cnt>0){
								 $('#example-table').find('#validateAllRslts').prop('checked',false);
								 $('#example-table').find('#validateAllRslts').prop('disabled',false);
								 $('#example-table tbody tr').find('input.validateOneRslt').prop('checked',false);
								 $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',false);
								 }
								$('#invalidateAllTests').hide();
								
								$('#order_status_name').val('{{__("Finished")}}');
								$('#order_status').val('F');
								$('#sendResults').prop('disabled',true);
								$('#fixed_comment').prop('disabled',false);
								
								//bill part
								  $('#finalizeBill').prop('disabled',false);
								  $('#saveBill').prop('disabled',false);
								  $('#btnpayment').prop('disabled',false);
								  $('#btnrefund').prop('disabled',false);
								  $('#btndiscount').prop('disabled',false);
								 
								  //order
								  $('#printOrder').prop('disabled',false);
								  $('#testsListBtn').prop('disabled',false);
								  $('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',false);
								  
								  //disable doctor and GUARANTOR
								  $('#selectLab').prop('disabled',true);
								  $('#selectDoc').prop('disabled',true);
								  
								  //documents
								  $('#input_files').fileinput('enable');
								  $('#docs_descrip').prop('disabled',false);
								  $('#documents_upload').show();
								  $('.attachIMG').show();
								  
								 //Culture
								 var $cultureDivs = $('.tab-pane > [id="culture"]');
								 if($cultureDivs.length){ 
								  $('#edit_culture').prop('disabled',false);
								  $('#cancel_culture').prop('disabled',true);
								  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
								 }
								 
								  //templates
								 var $templateDivs = $('.tab-pane > [id^="template"]');
								 if($templateDivs.length){ 
								  $('.editTemplate').prop('disabled',false);
								  $('.cancelTemplate').prop('disabled',true);
								  $('.summernote').each(function() {
										$(this).summernote('disable');
									});
								 }
						 break;
						 
					   }
					 
	
					 Swal.fire({toast:true,html:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
				}
				
			});
			}
   });
   
   $('#doneResults').change(function(e){
	    e.preventDefault();
		var order_id = $('#order_id').val();
		var checked = $(this).is(':checked');
		
		var valid_tests_cnt = $('#valid_tests_cnt').val();
		
		var has_validation = '{{$validate_results?"Y":"N"}}';
		
		var is_valid = checked?(valid_tests_cnt>0?'F':'V'):'P';
	
        var valid_msg = (is_valid=='V')?'{{__("Validating...")}}':(is_valid=='F'?'{{__("Finishing...")}}':'{{__("Cancelling...")}}');   
		
	    Swal.fire({
				title: '',
				html: valid_msg,
				showCancelButton:true,
				timer: 1000,
				timerProgressBar:true,
				didOpen: () => {
					Swal.showLoading();
				}				
			}).then((result) => {
				if (result.dismiss === Swal.DismissReason.cancel) {
					
					if($('#order_status').val()=='P'){
						  $('#doneResults').prop('checked',false);
					}else{
						 $('#doneResults').prop('checked',true); 
					  }			   
			   
					Swal.fire({
						icon: 'info',
						text: 'The action is cancelled.',
						customClass:'w-auto'
					});
					return;
				}else{
					performAjaxRequest();
				}
             });
            
			// Perform your AJAX request
			function performAjaxRequest(){
			$.ajax({
				url:'{{route("lab.visit.validateResults",app()->getLocale())}}',
				data:{_token:'{{csrf_token()}}',order_id:order_id,is_valid:is_valid,type:'finishProcess'},
			    type: 'post',
			    dataType: 'json',
				success: function(data) {
					
					 switch(data.return_status){
						 case 'P':
						    $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
							$('#doneResults').prop('disabled',false);
							$('#doneResults').prop('checked',false);
							$('#editResults').prop('disabled',false);
							$('#cancelResults').prop('disabled',true);
							$('#sendResultsInfo').prop('disabled',true);
							//added
							if(has_validation=='Y' && valid_tests_cnt>0){
							 $('#example-table').find('#validateAllRslts').prop('checked',false);
							 $('#example-table').find('#validateAllRslts').prop('disabled',true);
							 $('#example-table tbody tr').find('input.validateOneRslt').prop('checked',false);
							 $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',true);
							 }
							$('#invalidateAllTests').hide();
							
							$('#order_status_name').val('{{__("Pending")}}');
					        $('#order_status').val('P');
							$('#sendResults').prop('disabled',true);
							$('#fixed_comment').prop('disabled',false);
							
							//bill part
							  $('#finalizeBill').prop('disabled',false);
							  $('#saveBill').prop('disabled',false);
							  $('#btnpayment').prop('disabled',false);
							  $('#btnrefund').prop('disabled',false);
							  $('#btndiscount').prop('disabled',false);
							  
							 
							  //order
							  $('#printOrder').prop('disabled',false);
							  $('#testsListBtn').prop('disabled',false);
							  $('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',false);
							  
							  //disable doctor and GUARANTOR
							  $('#selectLab').prop('disabled',false);
							  $('#selectDoc').prop('disabled',false);
							  
							  //documents
							  $('#input_files').fileinput('enable');
							  $('#docs_descrip').prop('disabled',false);
							  $('#documents_upload').show();
							  $('.attachIMG').show();
							  
							 //Culture
								 var $cultureDivs = $('.tab-pane > [id="culture"]');
								 if($cultureDivs.length){ 
								  $('#edit_culture').prop('disabled',false);
								  $('#cancel_culture').prop('disabled',true);
								  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
								 }
								 
								  //templates
								 var $templateDivs = $('.tab-pane > [id^="template"]');
								 if($templateDivs.length){ 
								  $('.editTemplate').prop('disabled',false);
								  $('.cancelTemplate').prop('disabled',true);
								  $('.summernote').each(function() {
										$(this).summernote('disable');
									});
								 }
						 break;
						 case 'F':
								$('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
								$('#doneResults').prop('disabled',false);
								$('#doneResults').prop('checked',true); 
								$('#editResults').prop('disabled',false);
							    $('#cancelResults').prop('disabled',true);
								$('#sendResultsInfo').prop('disabled',false);
								
								//added
								if(has_validation=='Y' && valid_tests_cnt>0){
								 $('#example-table').find('#validateAllRslts').prop('checked',false);
								 $('#example-table').find('#validateAllRslts').prop('disabled',false);
								 $('#example-table tbody tr').find('input.validateOneRslt').prop('checked',false);
								 $('#example-table tbody tr').find('input.validateOneRslt').prop('disabled',false);
								 }
								$('#invalidateAllTests').hide();
								
								$('#order_status_name').val('{{__("Finished")}}');
								$('#order_status').val('F');
								$('#sendResults').prop('disabled',true);
								$('#fixed_comment').prop('disabled',false);
								
								//bill part
								  $('#finalizeBill').prop('disabled',false);
								  $('#saveBill').prop('disabled',false);
								  $('#btnpayment').prop('disabled',false);
								  $('#btnrefund').prop('disabled',false);
								  $('#btndiscount').prop('disabled',false);
								 
								  //order
								  $('#printOrder').prop('disabled',false);
								  $('#testsListBtn').prop('disabled',false);
								  $('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',false);
								  
								  //disable doctor and GUARANTOR
								  $('#selectLab').prop('disabled',true);
								  $('#selectDoc').prop('disabled',true);
								  
								  //documents
								  $('#input_files').fileinput('enable');
								  $('#docs_descrip').prop('disabled',false);
								  $('#documents_upload').show();
								  $('.attachIMG').show();
								  
								 //Culture
								 var $cultureDivs = $('.tab-pane > [id="culture"]');
								 if($cultureDivs.length){ 
								  $('#edit_culture').prop('disabled',false);
								  $('#cancel_culture').prop('disabled',true);
								  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
								 }
								 
								  //templates
								 var $templateDivs = $('.tab-pane > [id^="template"]');
								 if($templateDivs.length){ 
								  $('.editTemplate').prop('disabled',false);
								  $('.cancelTemplate').prop('disabled',true);
								  $('.summernote').each(function() {
										$(this).summernote('disable');
									});
								 }
						 break;
						 //only happens when no need for validation checkbox
						 case 'NV':
						  //keep page as it is since no email and sms are sent and uncheck done
						     $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
							 $('#doneResults').prop('disabled',false);
							 $('#editResults').prop('disabled',false);
							 $('#cancelResults').prop('disabled',true);
							 $('#sendResultsInfo').prop('disabled',true);
							 $('#invalidateAllTests').hide();
							 $('#doneResults').prop('checked',false);
							 $('#doneResults').prop('disabled',false);
						 break;
						 //only happens when no need for validation checkbox
						 case 'V':
						     $('#example-table tbody tr').find('input.resultVal').prop('disabled',true);
							 $('#order_status_name').val('{{__("Validated")}}');
					         $('#order_status').val('V');
					         $('#doneResults').prop('disabled',true);
						     $('#sendResults').prop('disabled',false);
							 $('#sendResultsInfo').prop('disabled',false);
							 $('#fixed_comment').prop('disabled',true);
							 $('#editResults').prop('disabled',true);
							 $('#cancelResults').prop('disabled',true);
							 
							 if(has_validation=='Y'){ $('#invalidateAllTests').show(); }
							  
							  //bill part
							  $('#finalizeBill').prop('disabled',true);
							  $('#saveBill').prop('disabled',true);
							  $('#btnpayment').prop('disabled',true);
							  $('#btnrefund').prop('disabled',true);
							  $('#btndiscount').prop('disabled',true);
							 
							  //order
							  $('#printOrder').prop('disabled',true);
							  $('#testsListBtn').prop('disabled',true);
							  $('#tsts_tbl tbody tr').find('select.referred-test-select').prop('disabled',true);
							  
							  //disable doctor and GUARANTOR
							  $('#selectLab').prop('disabled',true);
							  $('#selectDoc').prop('disabled',true);
							  
							  //documents
							  $('#input_files').fileinput('disable');
							  $('#docs_descrip').prop('disabled',true);
							  $('#documents_upload').hide();
							  $('.attachIMG').hide();
							  
							  //Culture
								 var $cultureDivs = $('.tab-pane > [id="culture"]');
								 if($cultureDivs.length){ 
								  $('#edit_culture').prop('disabled',false);
								  $('#cancel_culture').prop('disabled',true);
								  $('#culture_tbl tbody tr').find('input').prop('disabled',false);
								 }
								 
								  //templates
								 var $templateDivs = $('.tab-pane > [id^="template"]');
								 if($templateDivs.length){ 
								  $('.editTemplate').prop('disabled',false);
								  $('.cancelTemplate').prop('disabled',true);
								  $('.summernote').each(function() {
										$(this).summernote('disable');
									});
								 }
							  
							  //update sms package
							  $('#sms_pack').val(data.sms_pack);
						 break;
					   }
					 
	
					 Swal.fire({toast:true,html:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
				}
				
			});
			}

		
   });
   
	   
   
   $('#printResults').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
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
					
					link.download=('result.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
   });
   
   $('#printOrder').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
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
					
					link.download='Request#'+order_id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
   });
   
   $('#sendResults').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
	   var doctor_num = $('#selectDoc').val();
	   var ext_lab = $('#selectLab').val();
	   $.ajax({
		   url: '{{route("lab.visit.SMS_EMAIL",app()->getLocale())}}',
		   data:{_token: '{{ csrf_token() }}',order_id:order_id,ext_lab:ext_lab,doctor_num:doctor_num},
		   type: 'POST',
		   dataType:'JSON',
		   success: function(data){
			   $('#SMSEmailModal').find('#pat_email').val(data.pat_email);
			   $('#SMSEmailModal').find('#pat_tel').val(data.pat_tel);
			   $('#SMSEmailModal').find('#doc_email').val(data.doc_email);
			   $('#SMSEmailModal').find('#doc_tel').val(data.doc_tel);
			   $('#SMSEmailModal').find('#guarantor_email').val(data.guarantor_email);
			   $('#SMSEmailModal').find('#guarantor_tel').val(data.guarantor_tel);
			   $('#SMSEmailModal').find('#email_subject').val(data.email_subject);
			   $('#SMSEmailModal').find('#email_body').summernote('code', data.email_body);
               $('#SMSEmailModal').find('#sms_body').val(data.sms_body);
			   $('#SMSEmailModal').modal('show');
		   }
	   });
		
   });
   
   $('#sendResultsBtn').click(function(e){
	   e.preventDefault();
	   var pat_email = $('#SMSEmailModal').find('#pat_email').val();
	   if(pat_email!='' && !isValidEmailAddress(pat_email)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid email address for patient")}}',customClass:'w-auto'});
		   return false;
	   }
	   var doc_email = $('#SMSEmailModal').find('#doc_email').val();
	   if(doc_email!='' && !isValidEmailAddress(doc_email)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid email address for doctor")}}',customClass:'w-auto'});
		   return false;
	   }
	   var guarantor_email = $('#SMSEmailModal').find('#guarantor_email').val();
	   if(guarantor_email!='' && !isValidEmailAddress(guarantor_email)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid email address for guarantor")}}',customClass:'w-auto'});
		   return false;
	   }
	   
	   var pat_tel = $('#SMSEmailModal').find('#pat_tel').val();
	   if(pat_tel!='' && !isValidPhoneNumber(pat_tel)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid phone number for patient (only 8 digits)")}}',customClass:'w-auto'});
		   return false;
	   }
	   var doc_tel = $('#SMSEmailModal').find('#doc_tel').val();
	   if(doc_tel!='' && !isValidPhoneNumber(doc_tel)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid phone number for doctor (only 8 digits)")}}',customClass:'w-auto'});
		   return false;
	   }
	   var guarantor_tel = $('#SMSEmailModal').find('#guarantor_tel').val();
	   if(guarantor_email!='' && !isValidPhoneNumber(guarantor_tel)){
		   Swal.fire({icon:'warning',text:'{{__("Please input a valid phone number for guarantor")}}',customClass:'w-auto'});
		   return false;
	   }
	   
	   
	   
	   var sms_body = $('#SMSEmailModal').find('#sms_body').val();
	   var email_subject = $('#SMSEmailModal').find('#email_subject').val();
	   var email_body = $('#SMSEmailModal').find('#email_body').summernote('code');
	   var other_emails = $('#SMSEmailModal').find('#email_senders').val();
	   var other_phones = $('#SMSEmailModal').find('#tel_senders').val();
	   //console.log(other_emails);
	   if(pat_email=='' && doc_email=='' && guarantor_email=='' && other_emails.length==0
	      && pat_tel=='' && doc_tel=='' && guarantor_tel=='' && other_phones.length==0){
		   Swal.fire({icon:'warning',text:'{{__("Please input at least one email address or phone number in order to send the lab results")}}',customClass:'w-auto'});
		   return false; 
	   }
	   
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
		   data:{'_token': '{{ csrf_token() }}',order_id:$('#order_id').val(),pat_tel:pat_tel,pat_email:pat_email,guarantor_email:guarantor_email,
		         other_emails:other_emails,other_phones:other_phones,guarantor_tel:guarantor_tel,doc_tel:doc_tel,doc_email:doc_email,sms_body:sms_body,email_body:email_body,email_subject:email_subject},
		   type: 'post',
		   dataType:'json',
		   success: function(data){
					 if(data.success && data.success!=''){
					  Swal.fire({toast:true,html:data.success,icon:'success',timer:3000,position:'bottom-right',showConfirmButton:false});
					 }
					 //update sms package
					 $('#sms_pack').val(data.sms_pack);
					 $('#SMSEmailModal').modal('hide');
					 $('#sendResultsInfo').prop('disabled',false);
					
				}
		    });
	   
   });
   
  
	
	//SMS EMAIL EVENTS
	 $('#email_senders').on('select2:select', function(e) {
      var selectedEmails = $(this).val();
	  var validEmails = [];
	  $.each(selectedEmails, function(index, email) {
        if (isValidEmailAddress(email)) {
            validEmails.push(email);
        } else {
            Swal.fire({icon:'warning',text:'{{__("Please input a valid email address")}}',customClass:'w-auto'});
        }
     });
	
	$(this).val(validEmails).trigger('change'); 
    });

    // Validate telephone numbers
    $('#tel_senders').on('select2:select', function(e) {
       var selectedTels = $(this).val();
	   var validTels = [];
	  $.each(selectedTels, function(index, tel) {
        if (isValidPhoneNumber(tel)) {
            validTels.push(tel);
        } else {
           Swal.fire({icon:'warning',text:'{{__("Please input a valid phone number (only 8 digits)")}}',customClass:'w-auto'});
        }
     });
	
	$(this).val(validTels).trigger('change'); 
	  
    });

    // Validate email address
    function isValidEmailAddress(emailAddress) {
      var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return pattern.test(emailAddress);
    }

    // Validate phone number
    function isValidPhoneNumber(phoneNumber) {
      var pattern = /^\d{8}$/;
      return pattern.test(phoneNumber);
    }

    // Handle send message
  
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
  

//new discount methods
$('#selectcurrencyd').on('change', function(e){
    e.preventDefault();
	current_val=$(this).val();
	switch(current_val){
		case '1':
		    $("#dvaldollar").val('1.0');
			$("#valamountdiscount").val('');
			$('#dis_percent').val('');
			$("#dvallira").val('0');
		break;
		case '2':
		    $("#dvaldollar").val($('#exchange_rate').val());
			$("#valamountdiscount").val('');
			$('#dis_percent').val('');
			$("#dvallira").val('0');
		break;
	}
  	
});



$('#valamountdiscount').on('input', function(e){
e.preventDefault();
valamount=$(this).val();
valdollar=$("#dvaldollar").val();
vallira=valamount*valdollar;
if(valamount=='' || isNaN(valamount)){
	 $('#dis_percent').val('');
 }else{
	 var currency = parseInt($('#selectcurrencyd').val());
	 var total;
	switch(currency){
		  //LBP
		  case 1:
		    total = document.getElementById('totalf').value;
			total = total.replace(/,/g, '');
			$('#dis_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('stotal').value;
		    $('#dis_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 } 	
$("#dvallira").val(Math.round(vallira));
});

$('#dis_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();
	if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
		
    if (val === '') {
            $(this).val('');
			$('#valamountdiscount').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyd').val());
		var total;
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamountdiscount').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamountdiscount').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamountdiscount").val()==''?0:$("#valamountdiscount").val();
 valdollar=$("#dvaldollar").val();
 vallira=valamount*valdollar;
 $('#dvallira').val(Math.round(vallira)); 
});


//new refund methods
$('#selectcurrencyr').on('change', function(e)
{
    e.preventDefault();
  	current_val=$(this).val();
	switch(current_val){
		case '1':
		    $("#rvaldollar").val('1.0');
			$("#valamountrefund").val('');
			$('#ref_percent').val('');
			$("#rvallira").val('0');
		break;
		case '2':
		    $("#rvaldollar").val($('#exchange_rate').val());
			$("#valamountrefund").val('');
			$('#ref_percent').val('');
			$("#rvallira").val('0');
		break;
	}
	

});

$('#valamountrefund').on('input', function(e)
{
 e.preventDefault();
 valamount=$("#valamountrefund").val();
 valdollar=$("#rvaldollar").val();
 vallira=valamount*valdollar;
 if(valamount=='' || isNaN(valamount)){
	 $('#ref_percent').val('');
 }else{
	 var currency = parseInt($('#selectcurrencyr').val());
	 var total;
	switch(currency){
		  //LBP
		  case 1:
		    total = document.getElementById('totalf').value;
			total = total.replace(/,/g, '');
			$('#ref_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('stotal').value;
		    $('#ref_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 } 
 $("#rvallira").val(Math.round(vallira));
});

$('#selectmethodrefund').change(function(e){
	e.preventDefault();
	var method = $('#selectmethodrefund option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#refundModal').find('#refGuarantor').val($('#selectLab').val());
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',false);
	}else{
		$('#refundModal').find('#refGuarantor').val('');
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',true);
	}
});

$('#ref_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();
	if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
		
    if (val === '') {
            $(this).val('');
			$('#valamountrefund').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyp').val());
		var total;
		
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamountrefund').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamountrefund').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamountrefund").val()==''?0:$("#valamountrefund").val();
 valdollar=$("#rvaldollar").val();
 vallira=valamount*valdollar;
 $('#rvallira').val(Math.round(vallira)); 
});


//new payment methods
$('#selectcurrencyp').on('change', function(e){
	e.preventDefault();
    current_val=$(this).val();
	switch(current_val){
		case '1':
		    $("#valdollar").val('1.0');
			$("#valamount").val('');
			$('#pay_percent').val('');
			$("#vallira").val('0');
		break;
		case '2':
		    $("#valdollar").val($('#exchange_rate').val());
			$("#valamount").val('');
			$('#pay_percent').val('');
			$("#vallira").val('0');
		break;
	}
});

$('#valamount').on('input', function(e){
 e.preventDefault();
 valamount=$("#valamount").val();
 valdollar=$("#valdollar").val();
 vallira=valamount*valdollar;	
 
 if(valamount=='' || isNaN(valamount)){
	 $('#pay_percent').val('');
 }else{
	 var currency = parseInt($('#selectcurrencyp').val());
	 var total;
	switch(currency){
		  //LBP
		  case 1:
		    total = document.getElementById('totalf').value;
			total = total.replace(/,/g, '');
			$('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('stotal').value;
		    $('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 }
 
 $("#vallira").val(Math.round(vallira));
});

$('#selectmethod').change(function(e){
	e.preventDefault();
	var method = $('#selectmethod option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#paymentModal').find('#payGuarantor').val($('#selectLab').val());
        $('#paymentModal').find('#payGuarantor').trigger('change.select2');
		$('#paymentModal').find('#payGuarantor').prop('disabled',false);
	}else{
		$('#paymentModal').find('#payGuarantor').val('');
        $('#paymentModal').find('#payGuarantor').trigger('change.select2');
		$('#paymentModal').find('#payGuarantor').prop('disabled',true);
	}
});


$('#pay_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();

    if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
	
	
	
	if (val === '') {
            $(this).val('');
			$('#valamount').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyp').val());
		var total;
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamount').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamount').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamount").val()==''?0:$("#valamount").val();
 valdollar=$("#valdollar").val();
 vallira=valamount*valdollar;
 $('#vallira').val(Math.round(vallira)); 
});


 
 });
</script>
<script>
 /*$(function(){ 	
  
	
 
	$('#chosen_tests').empty();
   	$('.test').each(function(){ 
		if($(this).is(':checked')){
		  $('#chosen_tests').append('<div class="label-size col-12" id="'+$(this).val()+'"><b>'+$(this).attr('data-name')+'</b></div>');
		  }
       });
	
	
	
   
  
	
	  //$('#filter_category').val('');
	  //$('#filter_category').trigger('change.select2');
	  //$('#filter_group').val('');
	  //$('#filter_group').trigger('change.select2');
	 // Search text
      //var text = $('#search_test').val().toLowerCase();
	  // Search 
	 // Hide all content class element
     //$('.content-chk').hide();
	 //$('.content-chk  .content-lbl').each(function(){
          //if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
              // $(this).closest('.content-chk').show();
                //}else{
				 //$(this).closest('.content-chk').hide();	
				//}
           //});
				
   
   
   
 
 
 });*/
 
 
  
</script>
<script>
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


function nbCheck(elem,value) {
     var v = parseFloat(value);
    if (isNaN(v)) {
        $(elem).val('');
		
    } else {
       if(v>0) { $(elem).val('+'+v.toFixed(2)); }
	   if(v==0) { $(elem).val('0.00'); }
	   if(v<0) { $(elem).val(v.toFixed(2));}
    }
}
// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

function paymentbill(){
var bill_id=$('#bill_id').val();
$("#valamount").val("");
$("#pay_percent").val("");
$.ajax({
	url:'{{route("lab.visit.GetPay",app()->getLocale())}}',
    type:'POST',
    data:{_token: '{{ csrf_token() }}',bill_id:bill_id,type:'P'},
     success: function(data){
	 //$("#totalpay").val(data.totall);
	 $("#payamount").val(data.sumpay);
     $("#refamount").val(data.sumref);
	 $("#balancepay").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#paydiscount').val(data.discount);
	 
	 //$("#totalpayd").val(data.totald);
	 $("#payamountd").val(data.sumpayd);
     $("#refamountd").val(data.sumrefd);
	 $("#balancepayd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
     $('#paydiscountd').val(data.discountd);

	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
	 }
    });	
 
 var method = $('#paymentModal').find('#selectmethod option:selected').text().trim().toLowerCase();
 if(method=='guarantor'){
		$('#paymentModal').find('#payGuarantor').val($('#selectLab').val());
        $('#paymentModal').find('#payGuarantor').trigger('change.select2');
		$('#paymentModal').find('#payGuarantor').prop('disabled',false);
	}else{
		$('#paymentModal').find('#payGuarantor').val('');
        $('#paymentModal').find('#payGuarantor').trigger('change.select2');
		$('#paymentModal').find('#payGuarantor').prop('disabled',true);
	}
 
 
 $('#paymentModal').modal('show');
 
}

function savepay(){
var x = document.getElementById("myTablePay").rows[0].cells.length;
var arr1 = new Array();
for(ii=0;ii<document.getElementById("myTablePay").rows.length;ii++){
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,
          "DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,
		  "GUARANTOR":document.getElementById("myTablePay").rows[ii].cells[9].innerHTML,
		  "TYPE":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,
		  "CURRENCY":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,
		  "PRICE":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML,
		  "RATE":document.getElementById("myTablePay").rows[ii].cells[6].innerHTML,
		  "TOTAL":document.getElementById("myTablePay").rows[ii].cells[7].innerHTML};	
}
	
var id_lab ='{{$clinic->id}}';
var bill_id = $('#bill_id').val();
var order_id='{{isset($order)?$order->id:0}}';
var balance=(document.getElementById("balancepay").value).replaceAll(",", "");;	
var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_lab,"bill_id":bill_id,data:myjson1,"balance":balance,"order_id":order_id},
   url: '{{route("lab.visit.save_pay",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
         //refund modal update totals
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 $("#refdiscount").val(data.tdiscount);
		 
		 $("#payamountrefundd").val(data.sumpayd);
         $("#refamountrefundd").val(data.sumrefd);
		 $("#balancerefundd").val(data.balanced);
		 $('#refdiscountd').val(data.tdiscountd);
		 
		 //payment modal update totals
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 $("#paydiscount").val(data.tdiscount);
		 
		 $("#payamountd").val(data.sumpayd);
         $("#refamountd").val(data.sumrefd);
		 $("#balancepayd").val(data.balanced);
		 $('#paydiscountd').val(data.tdiscountd);
		 
		 //update discount totals
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 $('#disdiscount').val(data.tdiscount);
		 
		 $("#payamountdiscountd").val(data.sumpayd);
         $("#refamountdiscountd").val(data.sumrefd);
		 $("#balancediscountd").val(data.balanced);
		 $('#disdiscountd').val(data.tdiscountd);
		 
		 //update bill table totals
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
	
		  $('#paymentModal').modal('hide');

		  if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
		  
		  }
	 
	 if(data.warning){
				   Swal.fire({ 
				  "text":data.warning,
				  "icon":"warning",
				  "customClass": "w-auto"});
			       return;
				} 

	}
 });
}

function refundbill(){
var bill_id=document.getElementById("bill_id").value;
$("#valamountrefund").val("");
$("#ref_percent").val("");
$.ajax({
	url:'{{route("lab.visit.GetPay",app()->getLocale())}}',
    type:'POST',
    data:{_token: '{{ csrf_token() }}',bill_id:bill_id,type:'R'},
     success: function(data){
	 //$("#totalref").val(data.totall);
	 $("#payamountrefund").val(data.sumpay);
     $("#refamountrefund").val(data.sumref);
 	 $("#balancerefund").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#refdiscount").val(data.discount);
	 
	 //$("#totalrefd").val(data.totald);
	 $("#payamountrefundd").val(data.sumpayd);
     $("#refamountrefundd").val(data.sumrefd);
 	 $("#balancerefundd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#refdiscountd").val(data.discountd);
	 
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html1);
	
}
 });	


var method = $('#selectmethodrefund option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#refundModal').find('#refGuarantor').val($('#selectLab').val());
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',false);
	}else{
		$('#refundModal').find('#refGuarantor').val('');
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',true);
	}

$('#refundModal').modal('show');

}


function saverefund(){
var x = document.getElementById("myTableRef").rows[0].cells.length;
var arr2 = new Array();
for(iii=0;iii<document.getElementById("myTableRef").rows.length;iii++){
   arr2[iii]={"CODE":document.getElementById("myTableRef").rows[iii].cells[0].innerHTML,
              "DATE":document.getElementById("myTableRef").rows[iii].cells[1].innerHTML,
			  "GUARANTOR":document.getElementById("myTableRef").rows[iii].cells[9].innerHTML,
			  "TYPE":document.getElementById("myTableRef").rows[iii].cells[3].innerHTML,
			  "CURRENCY":document.getElementById("myTableRef").rows[iii].cells[4].innerHTML,
			  "PRICE":document.getElementById("myTableRef").rows[iii].cells[5].innerHTML,
			  "RATE":document.getElementById("myTableRef").rows[iii].cells[6].innerHTML,
			  "TOTAL":document.getElementById("myTableRef").rows[iii].cells[7].innerHTML};	
}
	
var id_facility='{{$clinic->id}}';
var bill_id=document.getElementById("bill_id").value;
var order_id='{{isset($order)?$order->id:0}}';
var balance=document.getElementById("balancerefund").value;	
var myjson2=JSON.stringify(arr2);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson2,"balance":balance,"order_id":order_id},
    url: '{{route("lab.visit.save_refund",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
        
		  //refund modal update totals
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 $("#refdiscount").val(data.tdiscount);
		 
		 $("#payamountrefundd").val(data.sumpayd);
         $("#refamountrefundd").val(data.sumrefd);
		 $("#balancerefundd").val(data.balanced);
		 $('#refdiscountd').val(data.tdiscountd);
		 
		 //payment modal update totals
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 $("#paydiscount").val(data.tdiscount);
		 
		 $("#payamountd").val(data.sumpayd);
         $("#refamountd").val(data.sumrefd);
		 $("#balancepayd").val(data.balanced);
		 $('#paydiscountd').val(data.tdiscountd);
		 
		 //update discount totals
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 $('#disdiscount').val(data.tdiscount);
		 
		 $("#payamountdiscountd").val(data.sumpayd);
         $("#refamountdiscountd").val(data.sumrefd);
		 $("#balancediscountd").val(data.balanced);
		 $('#disdiscountd').val(data.tdiscountd);
		 
		 //update bill table totals
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
		 	     
		 
          $('#refundModal').modal('hide');

        if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
			  
		  }
        if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		      return;
			} 

	}
 });
 
}

function savediscount()
{
	
var x = document.getElementById("myTableDis").rows[0].cells.length;
var arr2 = new Array();
for(iii=0;iii<document.getElementById("myTableDis").rows.length;iii++){
   arr2[iii]={"CODE":document.getElementById("myTableDis").rows[iii].cells[0].innerHTML,
              "DATE":document.getElementById("myTableDis").rows[iii].cells[1].innerHTML,
			  "GUARANTOR":document.getElementById("myTableDis").rows[iii].cells[9].innerHTML,
			  "TYPE":document.getElementById("myTableDis").rows[iii].cells[3].innerHTML,
			  "CURRENCY":document.getElementById("myTableDis").rows[iii].cells[4].innerHTML,
			  "PRICE":document.getElementById("myTableDis").rows[iii].cells[5].innerHTML,
			  "RATE":document.getElementById("myTableDis").rows[iii].cells[6].innerHTML,
			  "TOTAL":document.getElementById("myTableDis").rows[iii].cells[7].innerHTML};	
}
	
var id_facility='{{$clinic->id}}';
var bill_id=document.getElementById("bill_id").value;
var order_id='{{isset($order)?$order->id:0}}';
var balance=document.getElementById("balancediscount").value;	
var myjson2=JSON.stringify(arr2);
  
   $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson2,"balance":balance,"order_id":order_id},
    url: '{{route("lab.visit.save_discount",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });   
		  //refund modal update totals
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 $("#refdiscount").val(data.tdiscount);
		 
		 $("#payamountrefundd").val(data.sumpayd);
         $("#refamountrefundd").val(data.sumrefd);
		 $("#balancerefundd").val(data.balanced);
		 $('#refdiscountd').val(data.tdiscountd);
		 
		 //payment modal update totals
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 $("#paydiscount").val(data.tdiscount);
		 
		 $("#payamountd").val(data.sumpayd);
         $("#refamountd").val(data.sumrefd);
		 $("#balancepayd").val(data.balanced);
		 $('#paydiscountd').val(data.tdiscountd);
		 
		 //update discount totals
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 $('#disdiscount').val(data.tdiscount);
		 
		 $("#payamountdiscountd").val(data.sumpayd);
         $("#refamountdiscountd").val(data.sumrefd);
		 $("#balancediscountd").val(data.balanced);
		 $('#disdiscountd').val(data.tdiscountd);
		 
		 //update bill table totals
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
		 
		 if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
}
 if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		     return;
			} 

	}
 });

}


function insRowPay(){
  current_val=$("#selectmethod").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
	 
  if(parseFloat(current_val)==0 || current_val==''){
	   Swal.fire({ 
              "text":"Please input an amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
var x=document.getElementById('myTablePay').insertRow(document.getElementById('myTablePay').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
var i= x.insertCell(8);
var j= x.insertCell(9);

document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#payGuarantor").val()==''?'':$("#payGuarantor option:selected").text().trim();

d.innerHTML=$("#selectmethod option:selected").text().trim();
e.innerHTML=$("#selectcurrencyp option:selected").text().trim();
f.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("valdollar").value).toFixed(2);
h.innerHTML=parseFloat(document.getElementById("vallira").value).toFixed(2);
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';	
j.style.display="none";
j.innerHTML=$("#payGuarantor").val();

}
function deleteRowPay(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[8].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
}

function insRowRef()
{
	  current_val=$("#selectmethodrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseFloat(current_val)==0 || current_val==''){
	   Swal.fire({ 
              "text":"Please input an amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableRef').insertRow(document.getElementById('myTableRef').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
var i= x.insertCell(8);
var j= x.insertCell(9);
document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#refGuarantor").val()==''?'':$("#refGuarantor option:selected").text().trim();

d.innerHTML=$("#selectmethodrefund option:selected").text().trim();
e.innerHTML=$("#selectcurrencyr option:selected").text().trim();
f.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("rvaldollar").value).toFixed(2);
h.innerHTML=parseFloat(document.getElementById("rvallira").value).toFixed(2);
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
j.style.display="none";
j.innerHTML=$("#refGuarantor").val();


}

function deleteRowRef(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[6].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
}

function discountbill()
{
$("#valamountdiscount").val("");
$("#dis_percent").val("");
var bill_id=document.getElementById("bill_id").value;
$.ajax({
	url:'{{route("lab.visit.GetPay",app()->getLocale())}}',
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"D"},
     success: function(data){
	 $("#payamountdiscount").val(data.sumpay);
     $("#refamountdiscount").val(data.sumref);
 	 $("#balancediscount").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#disdiscount").val(data.discount);
	 
	 $("#payamountdiscountd").val(data.sumpayd);
     $("#refamountdiscountd").val(data.sumrefd);
 	 $("#balancediscountd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#disdiscountd").val(data.discountd);
	 $('#myTableDis').empty();
	 $('#myTableDis').html(data.html1);
	 
}
    });	
var method = $('#selectmethoddiscount option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#discountModal').find('#disGuarantor').val($('#selectLab').val());
        $('#discountModal').find('#disGuarantor').trigger('change.select2');
		$('#discountModal').find('#disGuarantor').prop('disabled',false);
	}else{
		$('#discountModal').find('#disGuarantor').val('');
        $('#discountModal').find('#disGuarantor').trigger('change.select2');
		$('#discountModal').find('#disGuarantor').prop('disabled',true);
	}
$('#discountModal').modal('show');
//var totaldiscount=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);



}


function insRowDis()
{
	  current_val=$("#selectmethoddiscount").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountdiscount").val();
  if(parseFloat(current_val)==0 || current_val==''){
	   Swal.fire({ 
              "text":"Please input an amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableDis').insertRow(document.getElementById('myTableDis').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
var i= x.insertCell(8);
var j= x.insertCell(9);
document.getElementById("cptd").value=parseInt(document.getElementById("cptd").value)+1;
a.innerHTML=document.getElementById("cptd").value;
b.innerHTML=document.getElementById("date_discount").value;
c.innerHTML=$("#disGuarantor").val()==''?'':$("#disGuarantor option:selected").text().trim();

d.innerHTML=$("#selectmethoddiscount option:selected").text().trim();
e.innerHTML=$("#selectcurrencyd option:selected").text().trim();
f.innerHTML=parseFloat(document.getElementById("valamountdiscount").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("dvaldollar").value).toFixed(2);
h.innerHTML=parseFloat(document.getElementById("dvallira").value).toFixed(2);
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletedis'+document.getElementById("cptd").value+'" value="{{__('Delete')}}" onclick="deleteRowDis(this)"/>';
j.style.display="none";
j.innerHTML=$("#disGuarantor").val();


}

function deleteRowDis(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valdis=parseFloat(document.getElementById("myTableDis").rows[ii].cells[6].innerHTML);
 document.getElementById('myTableDis').deleteRow(ii);
}



function downloadPDF(){	 
   var id=document.getElementById("bill_id").value;	
     
      $.ajax({
           url: '{{route("lab.visit.GetPDFBill",app()->getLocale())}}',
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
			link.download='Bill#'+id+'.pdf';
			link.click();
			Swal.fire({title:'{{__("Bill Downloaded")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
	
		
			    });
	
	
	
	}
	
function isNumberKey(evt,el)
       {
           var charCode = (event.which) ? event.which : event.keyCode;
			// Allow digits, period (.), and backspace
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
				return false;
			}
			// Allow only one decimal point
			var currentValue = $(el).val();
			if (charCode == 46 && currentValue.indexOf('.') !== -1) {
				return false;
			}
			return true;
       }		
	
function getSMSEmailInfo(){
	var order_id = $('#order_id').val();
	if(order_id!=0){
		 $.ajax({
			url: "{{ route('lab.visit.filterGroup',app()->getLocale()) }}",
			type: "POST",
			data:{_token :'{{csrf_token()}}',order_id :order_id,type:'Info'},
			dataType:"JSON",
			success: function(data){
				  $('#SMSEmailHistoryModal').find('#emailSMS_table1').DataTable().destroy();
				  $('#SMSEmailHistoryModal').find('#emailSMS_table1').DataTable({
						responsive: true,
						data: data.table1,
						columns: [
						  { data: 'to_patient' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						  { data: 'to_doctor' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						 { data: 'to_guarantor' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						  { data: 'email_date',render: function(data, type, row) {
							if(data!='' && data!=null){
                                return moment(data).format('DD/MM/YYYY HH:mm');
							}else{
								return data;
							}
						  }}
						 ],
						pageLength: 10
					  });
				  
				  $('#SMSEmailHistoryModal').find('#emailSMS_table3').DataTable().destroy();
				  $('#SMSEmailHistoryModal').find('#emailSMS_table3').DataTable({
						responsive: true,
						data: data.table3,
						columns: [
						  { data: 'to_patient' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						  { data: 'to_doctor' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						 { data: 'to_guarantor' ,render: function(data, type, row) {
							if (data === 'Y') {
							  return '<i class="fa fa-check text-success"></i>';
							} else {
							  return '<i class="fa fa-times text-danger"></i>';
							}
						  }},
						 { data: 'sms_date',render: function(data, type, row) {
							if(data!='' && data!=null){
                                return moment(data).format('DD/MM/YYYY HH:mm');
							}else{
								return data;
							}
						  }}
						 ],
						pageLength: 10
					  });	  

          	    $('#SMSEmailHistoryModal').find('#emailSMS_table2').DataTable().destroy();
				$('#SMSEmailHistoryModal').find('#emailSMS_table2').DataTable({
						responsive: true,
						data: data.table2,
						columns: [
						  { data: 'error_msg' },
						  { data: 'created_at',render: function(data, type, row) {
							if(data!='' && data!=null){
                                return moment(data).format('DD/MM/YYYY');
							}else{
								return data;
							}
						  } }
						],
						pageLength: 10
					  });
					  
				$('#SMSEmailHistoryModal').modal('show');	  
			  }
			});
	}
}
</script>
<script>
$(function() {
    Fancybox.bind("[data-fancybox]", {
         fitToView: true, // if you require exact size (400x300)
        closeClick: true,
        openEffect: 'elastic',
        closeEffect: 'elastic'
       
        });
});
</script>
<script>
        flatpickr('#date_pay', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#date_refund', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
</script>
@endsection