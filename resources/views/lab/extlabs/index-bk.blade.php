<!--
 DEV APP
 Created date : 16-08-2024
-->
@extends('gui.main_gui')

@section('content')	
<div class="container-fluid" id="content_bill">	
			
			<div class="row mt-1">
				<section class="col-md-12 p-0"> 
					<div  class="card card-outline">
						<div class="card-header text-white">
							<div class="row"> 
								 <div class="col-md-9 col-8">
								 <h5 class="ml-2 mb-1 mt-1">{{__('Guarantor Labs').'-'.$FromFacility->full_name}}</h5>
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
				   <div class="card card-outline">
						<div class="card-header">
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__('Filter Guarantor history')}}</h5></div>
							 <div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								  </button>
							 </div> 
						</div> 
						<div class="card-body p-0">
						  
							<div class="row m-1">			  
							 
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('From date')}}"
									value="{{(request()->session()->has('from_date_bill'))?request()->session()->get('from_date_bill'):Carbon\Carbon::now()->subDays(7)->format('Y-m-d')}}"/>
								</div>
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('To date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('To date')}}"
									value="{{(request()->session()->has('to_date_bill'))?request()->session()->get('to_date_bill'):Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
								
								<div class="col-md-2 mb-2" hidden >
										<select name="filter_status" id="filter_status" class="custom-select rounded-0">
											  <option value="">{{__('Paid/Not Paid')}}</option>
											  <option value="Y" {{ session('filter_status') == 'Y' ? 'selected' : '' }} >{{__('Paid')}}</option>
											  <option value="N" {{ session('filter_status') == 'N' ? 'selected' : '' }}>{{__('Not Paid')}}</option>
										 </select>	  
									</div>
									<div class="col-md-2 mb-2">
										<select name="filter_facture" id="filter_facture" class="custom-select rounded-0">
											  <option value="">{{__('Facture/Not Facture')}}</option>
											  <option value="Y" {{ session('filter_facture') == 'Y' ? 'selected' : '' }} >{{__('Facture')}}</option>
											  <option value="N" {{ session('filter_facture') == 'N' ? 'selected' : '' }}>{{__('Not Facture')}}</option>
										 </select>	  
									</div>
								<div class="col-md-2 mb-2">
										<select name="filter_sent" id="filter_sent" class="custom-select rounded-0">
											  <option value="">{{__('Cash/Guarntor')}}</option>
											  <option value="1" {{ session('filter_sent') == '1' ? 'selected' : '' }} >{{__('Cash')}}</option>
											  <option value="5" {{ session('filter_sent') == '5' ? 'selected' : '' }}>{{__('Guarantor')}}</option>
										 </select>	  
									</div>	
							
								<div class="col-md-4">
                                    <!--<label for="patient" class="label-size">{{__('Patient')}}</label>-->
									<select class="select2_filter_patient1 custom-select rounded-0" name="filter_patient"  id="filter_patient" style="width:100%;">
									</select>									
								</div>
								
								
								  <div class="mt-1 mb-1 col-md-4">
										<!--<label for="pro" class="label-size">{{__('Guarantor')}}</label>-->
										<select name="filter_g" id="filter_g" class="select2_data_grntr custom-select rounded-0">
										
										</select>
							        </div>
								
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_fromdatefacture" id="filter_fromdatefacture"  placeholder="{{__('From date Facture')}}"
									value=""/>
								</div>
								<div class="col-md-2 col-6">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_todatefacture" id="filter_todatefacture"  placeholder="{{__('To date Facture')}}"
									value=""/>
								</div>
								<div class="col-md-2 col-6" hidden >
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_fromdatepaid" id="filter_fromdatepaid"  placeholder="{{__('From date Paid')}}"
									value=""/>
								</div>
								<div class="col-md-2 col-6" hidden >
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="filter_todatepaid" id="filter_todatepaid"  placeholder="{{__('To date Paid')}}"
									value=""/>
								</div>
								<div class="col-md-4 col-6">
								</div>
									<div class="col-md-2 col-4">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="datein" id="datein"  placeholder="{{__('date Facture')}}"
									value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
								</div>
									<div class="col-md-2 col-4">
									<!--<label for="name" class="label-size">{{__('From date')}}</label>-->
									<input autocomplete="false" type="text" class="form-control" name="datedue" id="datedue"  placeholder="{{__('due Date Facture')}}"
									value=""/>
								</div>
								<div class="col-md-2">
								 <label for="name">{{__('All Facture')}}</label>
							   <input type="checkbox" id="select-all">
							   </div>
							   <div class="col-md-1" hidden >
							    <label for="name">{{__('Remove Facture')}}</label>
							   <input type="checkbox" id="unselect-all">
							   </div>
							   <div class="col-md-1">
									<button class="btn btn-action" id="save-btn">{{__('Save Facture')}}</button>	
										<input type="hidden" id="filter_branch" name="filter_branch" value="{{$FromFacility->id}}"/>
							</div>
							   <div class="col-md-1">					 
						   <button class="btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentbill()">{{__('Payments')}}</button>
							</div>	 
									<div class="col-md-1" hidden >
							    <label for="name">{{__('All Paid')}}</label>
							   <input type="checkbox" id="select-allp">
								</div>
                               						
							<div class="col-md-1" hidden >
									<button class="btn btn-action" id="save-btnp">{{__('Save Paid')}}</button>	
								 </div>			
							<div class="col-md-1">
							<div class="d-flex flex-column">
							 <button class="btn btn-action"  id="btndetails" onclick="printdetails()">{{__('Details')}}</button>
							</div>
							</div>
							<div class="col-md-1">
							<div class="d-flex flex-column">
							 <button class="btn btn-action"  id="btntotal" onclick="printtotal()">{{__('Total')}}</button>
							</div>
							</div>		   		
							<div class="col-md-1">
							<div class="d-flex flex-column">
							 <button class="btn btn-action"  id="btntotal" onclick="printstatment()">{{__('Statment')}}</button>
							</div>
							</div>		   			
							</div>
						  
						</div>
					</div>
				</section>
                
                <section class="col-md-12 p-0">
				    <div class="card card-outline"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5 class="ml-2 mb-1 mt-1">{{__("Guarantor history")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body p-0">	
							<div class="row  mt-1">
							   <div class="col-md-12">
							   
								 <table id="guarlabs_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('#')}}</th>
									<th class="all ">{{__('Bill')}}</th>
									<th class="all ">{{__('Request')}}</th>
									<th class="all">{{__('Guarantor')}}</th>
									<th class="all">{{__('Patient')}}</th>
									<th class="all" style="display:none;">{{__('Bill Code')}}</th>
									<th class="all">{{__('Test Name')}}</th>
									<th class="all">{{__('Total $')}}</th>
									<th class="all">{{__('Facture')}}</th>
									<th class="all">{{__('Date Facture')}}</th>
																	

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
<!--payment modal-->
				<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg text-white" style="padding-top:2px;padding-bottom:1px;">
								<h4 class="modal-title">{{__('Payment')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body" style="padding-top:2px;padding-bottom:1px;">
						        <div class="container-fluid">
									<div class="row">   
									    <div class="col-md-6">
										<label class="label-size" for="name">{{__('Gurantor name')}}</label>
										<input type="text" id="guarant_name" class="form-control"	value="" disabled />
									   </div>
									   <div class="col-md-3">
											<label class="label-size" for="name">{{__('Facture Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="" disabled />   
											<input  type="hidden" id="cptp" value=""/>
									   </div>
									  <div class="col-md-3">	 
										  <label class="label-size" for="name">{{__('Date/Time')}}</label>
										  <input type="text" class="form-control" name="date_pay" id="date_pay" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
										</div>
									  </div> 
									   <div class="row">   
									    
										 <div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
											   <select class="form-control"   id="selectmethod" name="selectmethod">
													<option value="P">{{__('Paid')}}</option>
													<option value="D">{{__('Discount')}}</option>
													<option value="R">{{__('Donate')}}</option>
																				
												</select>
										   </div>
										  											
											<div class="col-md-1">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="form-control" name="selectcurrencyp"  id="selectcurrencyp" style="width:100%;">
																				@foreach($currencys as $currencypays)
																						<option value="{{$currencypays->id}}">
																						{{$currencypays->abreviation}}
																						</option>
																						@endforeach 
												</select>				
											</div>
										 			
										<div class="col-md-2">
											<label class="label-size" for="name">{{__('Percentage%')}}</label>
											 <input  class="form-control"  value="" id="pay_percent"  onkeypress="return isNumberKey(event,this)"/> 
										</div>
										<div class="col-md-2">
											<label class="label-size" for="name">{{__('Amount')}}</label>
											 <input  class="form-control"  value="" id="valamount"  onkeypress="return isNumberKey(event,this)"/> 
										</div>
										<div class="col-md-1" hidden >
											<div class="d-flex flex-column">
											 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
											 <button class="btn btn-action"  id="btnaddpay" onclick="insRowPay()">{{__('Add')}}</button>
											</div>
										</div>		 
										</div>			
										<div class="row">  
																
										<div class="col-md-2 col-6" style="display:none;">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="" id="valdollar" value="1.0" disabled /> 
										</div>
										 			
										<div class="col-md-2 col-6" style="display:none;">
											<label class="label-size" for="name">{{__('Amount LBP')}}</label>
											 <input  class="form-control"  value="" id="vallira"  value="0" disabled /> 
										</div>
										
									</div>
									
									  
							
							
								<div id="htmlTablePay" class="row mt-2 m-1">
									<div class="table-responsive">
										<table id="myTablePay" class="table table-striped table-bordered table-hover" style="text-align:center;">
										
										   
										</table> 
									</div>
							   </div>		
						        <div class="row m-1">
							            <div class="table-responsive col-md-12">
													 <table class="table-bordered table-sm" style="font-size:14px;">
													  <thead>
														  <tr class="text-center">
															<th></th>
															<th>Total</th>
															<th>Remaining</th>
															<th>Total Payment</th>
															<th>Total Discount</th>
															<th>Total Donate</th>
														   </tr>
													   </thead>
													   <tbody>
													   	 <tr>
														  <th>USD</th>
														  <td><input  class="billcss form-control" value="" id="totalpayd"   disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="balancepayd"  disabled /></td>
														  <td><input  class="billcss form-control" value=""  id="payamountd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="paydiscountd"  disabled /></td>
 														  <td><input  class="billcss form-control"  value="" id="refamountd"  disabled /></td>
														 </tr>
													   </tbody>
													 </table>
													 </div>
										
										
						
						         </div>
                              </div>
							</div>
						    <div class="modal-footer justify-content-center" style="padding-top:2px;padding-bottom:1px;">
								 <button class="btn btn-action" id="btnsavepay" name="btnsavepay" onClick="event.preventDefault();savepayfacture()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> 
			</div> 
					 <!--end paymentModal-->				
				
				
				
				
        </div>   				
							
</div>										
										
@endsection	
@section('scripts')

<script>
$(document).ready(function(){
	 $("#valdiscount").val("0.00");
			$("#vallira").val("0.00");
			$("#dvallira").val("0.00");
			$("#valdollar").val("1.00");
			$("#rvallira").val("0.00");
			$("#rvaldollar").val("1.00");
			$("#dvaldollar").val("1.00");
			$("#valamount").val("0.00");
	$('#select-all').on('click', function() 
	{ 
	var table = $('#guarlabs_table').DataTable();
	
	var rows = table.rows({ 'search': 'applied' }).nodes(); 
	$('input[class="row-facture"]', rows).prop('checked', true); 

	});
	
	$('#unselect-all').on('click', function() 
	{ 
	var table = $('#guarlabs_table').DataTable();
	
	var rows = table.rows({ 'search': 'applied' }).nodes(); 
	$('input[class="row-facture"]', rows).prop('checked', false); 

	});
	
	$('#select-allp').on('click', function() 
	{ 
	var table = $('#guarlabs_table').DataTable(); 
	var rows = table.rows({ 'search': 'applied' }).nodes(); 
	$('input[class="row-paid"]', rows).prop('checked', this.checked); 
	});
	
	$('body').addClass('sidebar-collapse');
	
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
		
		$('#filter_fromdate,#datein,#datedue').flatpickr({
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
		
		$('#filter_fromdatefacture').flatpickr({
			allowInput: true,
			enableTime: false,
			dateFormat: "Y-m-d",
			disableMobile: true
		});
		var min_date = $('#filter_fromdatefacture').val();
		$('#filter_todatefacture').flatpickr({
			allowInput: true,
			enableTime: false,
			minDate: $('#filter_fromdatefacture').val(),
			dateFormat: "Y-m-d",
			disableMobile: true
		});
		$('#filter_fromdatepaid').flatpickr({
			allowInput: true,
			enableTime: false,
			dateFormat: "Y-m-d",
			disableMobile: true
		});
		var min_date = $('#filter_fromdatepaid').val();
		$('#filter_todatepaid').flatpickr({
			allowInput: true,
			enableTime: false,
			minDate: $('#filter_fromdatepaid').val(),
			dateFormat: "Y-m-d",
			disableMobile: true
		});
		
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
		
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	$('#guarlabs_table').DataTable({
					stateSave: true,
					stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					pageLength: 50,
					lengthMenu: [
					[50, 75, 100, -1],
					[50, 75, 100, 'All']
					 ],
			        ajax: {
						url: "{{ route('lab.extlabs.index',app()->getLocale()) }}",
					    data: function (d) {
								
								 d.id_facility = $('#filter_branch').val();
								 d.filter_g = $('#filter_g').val();
								 d.filter_doc=$('#filter_doc').val();
								 d.filter_patient=$('#filter_patient').val();
								 d.filter_fromdate=$('#filter_fromdate').val();
								 d.filter_todate=$('#filter_todate').val();
								 d.filter_status=$('#filter_status').val();
								 d.filter_facture=$('#filter_facture').val();
								 d.filter_fromdatefacture=$('#filter_fromdatefacture').val();
								 d.filter_todatefacture=$('#filter_todatefacture').val();
								 d.filter_fromdatepaid=$('#filter_fromdatepaid').val();
								 d.filter_todatepaid=$('#filter_todatepaid').val();
								 d.filter_sent=$('#filter_sent').val();
							}
					},
						 
					columns: [

						{data: 'id'},
						{data: 'reference'},
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
						{data: 'bill_code',visible: false},
						{data: 'bill_name'},
						{data: 'bill_price'},
						{data: 'GFacture',orderable: false, searchable: false},
						{data: 'ref_date_facture'},
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

    
		var filter_fromdate = $('#filter_fromdate').val();
		var filter_todate = $('#filter_todate').val();
		var filter_patient = $('#filter_patient').val();
		var filter_status = $('#filter_status').val();
		var filter_facture = $('#filter_facture').val();
		var filter_fromdatefacture=$('#filter_fromdatefacture').val();
		var filter_todatefacture=$('#filter_todatefacture').val();
		var filter_fromdatepaid=$('#filter_fromdatepaid').val();
		var filter_todatepaid=$('#filter_todatepaid').val();
		var filter_sent = $('#filter_sent').val();
		var filter_g = $('#filter_g').val();
	    $.ajax({
				url: '{{route("guargen_sumprice",app()->getLocale())}}',
			   data: {'filter_fromdatefacture':filter_fromdatefacture,'filter_todatefacture':filter_todatefacture,'filter_fromdatepaid':filter_fromdatepaid,'filter_todatepaid':filter_todatepaid,'filter_sent':filter_sent,'filter_fromdate':filter_fromdate,'filter_todate':filter_todate,'filter_patient':filter_patient,'filter_facture':filter_facture,'filter_status':filter_status,'filter_g':filter_g},
			   type: 'get',
			   dataType: 'json',
			   success: function(data){
				$('#tdollar').val(data.tdollar); 
				$('#teuro').val(data.teuro); 		
				$('#paydollar').val(data.paydollar); 
				$('#payeuro').val(data.payeuro); 					
			   }
	      });
	

$('#filter_fromdate').change(function(){
	    var filter_fromdate = $('#filter_fromdate').val();
		var filter_todate = $('#filter_todate').val();
		var filter_patient = $('#filter_patient').val();
		var filter_status = $('#filter_status').val();
		var filter_facture = $('#filter_facture').val();
		var filter_fromdatefacture=$('#filter_fromdatefacture').val();
		var filter_todatefacture=$('#filter_todatefacture').val();
		var filter_fromdatepaid=$('#filter_fromdatepaid').val();
		var filter_todatepaid=$('#filter_todatepaid').val();
		var filter_sent = $('#filter_sent').val();
		var filter_g = $('#filter_g').val();
	
	if(from_date!=null && from_date!=''){
		$('#filter_todate').flatpickr().destroy();
		$('#filter_todate').flatpickr({
			allowInput: true,
			enableTime: false,
			dateFormat: "Y-m-d",
			minDate: from_date ,
			disableMobile: true
		});
	}else{
	   $('#filter_todate').flatpickr().destroy();
       $('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		disableMobile: true
	});	   
	}
	$('#guarlabs_table').DataTable().ajax.reload();
	$.ajax({
				url: '{{route("refgen_sumprice",app()->getLocale())}}',
			   data: {'filter_fromdatefacture':filter_fromdatefacture,'filter_todatefacture':filter_todatefacture,'filter_fromdatepaid':filter_fromdatepaid,'filter_todatepaid':filter_todatepaid,'filter_sent':filter_sent,'filter_fromdate':filter_fromdate,'filter_todate':filter_todate,'filter_patient':filter_patient,'filter_facture':filter_facture,'filter_status':filter_status,'filter_g':filter_g},
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
				    total = document.getElementById('totalpayd').value;
					total = total.replace(/,/g, '');
					$('#valamount').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('totalpayd').value;
					$('#valamount').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 valamount=$("#valamount").val()==''?0:$("#valamount").val();
 valdollar=$("#valdollar").val();
 vallira=valamount*valdollar;
 $('#dvallira').val(Math.round(vallira)); 
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
		    total = document.getElementById('totalpayd').value;
			total = total.replace(/,/g, '');
			$('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('totalpayd').value;
		    $('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 }
 
 $("#vallira").val(Math.round(vallira));
});



$('#selectcurrencyd').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyd").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#dvaldollar").val(data.pdolar);
			$("#valamountdiscount").val('0.00');
			$("#dvallira").val('0.00');
			
		 }
      });
});
$('#filter_todatefacture,#filter_todatepaid,#filter_fromdatefacture,#filter_fromdatepaid,#filter_todate,#filter_patient,#filter_facture,#filter_status,#filter_sent,#filter_g').change(function(){
		var filter_fromdate = $('#filter_fromdate').val();
		var filter_todate = $('#filter_todate').val();
		var filter_patient = $('#filter_patient').val();
		var filter_status = $('#filter_status').val();
		var filter_sent = $('#filter_sent').val();
		var filter_facture = $('#filter_facture').val();
		var filter_g = $('#filter_g').val();
		var filter_fromdatefacture=$('#filter_fromdatefacture').val();
		var filter_todatefacture=$('#filter_todatefacture').val();
		var filter_fromdatepaid=$('#filter_fromdatepaid').val();
		var filter_todatepaid=$('#filter_todatepaid').val();
		$('#guarlabs_table').DataTable().ajax.reload();
		$.ajax({
				url: '{{route("refgen_sumprice",app()->getLocale())}}',
			   data: {'filter_fromdatefacture':filter_fromdatefacture,'filter_todatefacture':filter_todatefacture,'filter_fromdatepaid':filter_fromdatepaid,'filter_todatepaid':filter_todatepaid,'filter_facture':filter_facture,'filter_sent':filter_sent,'filter_fromdate':filter_fromdate,'filter_todate':filter_todate,'filter_patient':filter_patient,'filter_status':filter_status,'filter_g':filter_g},
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
	
	

//$('#filter_patient').on('change',function(){
	//$('#guarlabs_table').DataTable().ajax.reload(null, false);
//});


$('#save-btn').on('click', function() { 
var selectedValues = [];
var selectedValuesN = [];
var datein = $('#datein').val();
var datedue = $('#datedue').val();
var selectall = $('#select-all').prop('checked');
var table = $('#guarlabs_table').DataTable();
//var unselectall = $('#unselect-all').prop('checked');
var filter_g = $('#filter_g').val();

//if (selectall=='true'){

$('input[class="row-facture"]:checked', table.rows().nodes()).each(function() 
{
 selectedValues.push($(this).closest('tr').find('td').eq(0).text()); 
 });
//}
 
 //if (unselectall=='true'){
 //$('#select-all').prop('checked', false);
//}else{
//var rows = table.rows({ 'search': 'applied' }).nodes(); $('input[class="row-facture"]:not(:checked)', rows).each(function() 
//{ selectedValues.push($(this).closest('tr').find('td').eq(0).text().trim()); 
//});
	//}
$.ajax({
  url:'{{route("FactureTest",app()->getLocale())}}',
   type: 'POST', 
  data:{selectedValues: selectedValues, _token: '{{ csrf_token() }}',filter_g:filter_g,datein:datein,datedue:datedue,selectall:selectall}, 
  success: function(data) 
 { 
 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
			$('#guarlabs_table').DataTable().ajax.reload(null, false);
			//$('#select-all').prop('checked', false);
 
 }); 
 }
 }); 
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

function Paid(id,bill_code,state){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	var datein = $('#datein').val();
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("GuarPaidTest",app()->getLocale())}}',
			data:{id:id,state:state,bill_code:bill_code,datein:datein},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
			//		 //window.location.href = data.location;
			  $('#guarlabs_table').DataTable().ajax.reload(null, false);


				 });
				 
		}
	});
}

function GuarFacture(id,bill_code,state1){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	var datein = $('#datein').val();
	   $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("FactureTest_old",app()->getLocale())}}',
			data:{id:id,state1:state1,bill_code:bill_code,datein:datein},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
			//		 //window.location.href = data.location;
			$('#guarlabs_table').DataTable().ajax.reload(null, false);


				 });
				 
		}
		});
}


function printdetails(){	 
   var filter_fromdatefacture=document.getElementById("filter_fromdatefacture").value;	
   var filter_todatefacture=document.getElementById("filter_todatefacture").value;	
   var filter_fromdatepaid=document.getElementById("filter_fromdatepaid").value;	
   var filter_todatepaid=document.getElementById("filter_todatepaid").value;	
   var filter_g=document.getElementById("filter_g").value;	
   var filter_status=document.getElementById("filter_status").value;	
   var filter_facture=document.getElementById("filter_facture").value;	
   var filter_fromdate=document.getElementById("filter_fromdate").value;	
   var filter_todate=document.getElementById("filter_todate").value;	
   var filter_sent=document.getElementById("filter_sent").value;	

      $.ajax({
           url: '{{route("downloadPDFFacture",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}',
		   'filter_fromdatefacture':filter_fromdatefacture,
		   'filter_todatefacture':filter_todatefacture,
		   'filter_fromdatepaid':filter_fromdatepaid,
		   'filter_todatepaid':filter_todatepaid,
		   'filter_status':filter_status,
		   'filter_facture':filter_facture,
		   'filter_fromdate':filter_fromdate,
		   'filter_todate':filter_todate,
		   'filter_sent':filter_sent,
		   'filter_g':filter_g,'type':'D'},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
	}

function printtotal(){	 
   var filter_fromdatefacture=document.getElementById("filter_fromdatefacture").value;	
   var filter_todatefacture=document.getElementById("filter_todatefacture").value;	
   var filter_fromdatepaid=document.getElementById("filter_fromdatepaid").value;	
   var filter_todatepaid=document.getElementById("filter_todatepaid").value;	
   var filter_g=document.getElementById("filter_g").value;	
   var filter_status=document.getElementById("filter_status").value;	
   var filter_facture=document.getElementById("filter_facture").value;	
   var filter_fromdate=document.getElementById("filter_fromdate").value;	
   var filter_todate=document.getElementById("filter_todate").value;	
    var filter_sent=document.getElementById("filter_sent").value;	
      $.ajax({
           url: '{{route("downloadPDFFacture",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}',
		   'filter_fromdatefacture':filter_fromdatefacture,
		   'filter_todatefacture':filter_todatefacture,
		   'filter_fromdatepaid':filter_fromdatepaid,
		   'filter_todatepaid':filter_todatepaid,
		   'filter_status':filter_status,
		   'filter_facture':filter_facture,
		   'filter_fromdate':filter_fromdate,
		   'filter_todate':filter_todate,
   		   'filter_sent':filter_sent,
		   'filter_g':filter_g,'type':'T'},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
	}
	
function printstatment(){	 
 
   var filter_g=document.getElementById("filter_g").value;	
   var filter_fromdate=document.getElementById("filter_fromdate").value;	
   var filter_todate=document.getElementById("filter_todate").value;	
      $.ajax({
           url: '{{route("downloadPDFStatment",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}',
		  'filter_fromdate':filter_fromdate,
		   'filter_todate':filter_todate,
		   'filter_g':filter_g,'type':'T'},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
	}	
function paymentbill()
{
//var bill_id=document.getElementById("bill_id").value;
var comboBox = document.getElementById("filter_g");
var selectedOption = comboBox.options[comboBox.selectedIndex];
var guarant_name = selectedOption.text;
$("#guarant_name").val(guarant_name);

$("#valamount").val("0.00");

 var filter_fromdatefacture=document.getElementById("filter_fromdatefacture").value;	
   var filter_todatefacture=document.getElementById("filter_todatefacture").value;	
   var filter_fromdatepaid=document.getElementById("filter_fromdatepaid").value;	
   var filter_todatepaid=document.getElementById("filter_todatepaid").value;	
   var filter_g=document.getElementById("filter_g").value;	
   var filter_status=document.getElementById("filter_status").value;	
   var filter_facture=document.getElementById("filter_facture").value;	
   var filter_fromdate=document.getElementById("filter_fromdate").value;	
   var filter_todate=document.getElementById("filter_todate").value;	
    var filter_sent=document.getElementById("filter_sent").value;	
      $.ajax({
           url: '{{route("GetPayFacture",app()->getLocale())}}',
		    data: {'_token': '{{ csrf_token() }}',
		   'filter_fromdatefacture':filter_fromdatefacture,
		   'filter_todatefacture':filter_todatefacture,
		   'filter_fromdatepaid':filter_fromdatepaid,
		   'filter_todatepaid':filter_todatepaid,
		   'filter_status':filter_status,
		   'filter_facture':filter_facture,
		   'filter_fromdate':filter_fromdate,
		   'filter_todate':filter_todate,
   		   'filter_sent':filter_sent,
		   'filter_g':filter_g,'type':'T'},
           type: 'post',
		  success: function(data){
			$("#totalpayd").val(data.totalBillPrice);
			$("#reqIDPay").val(data.reference);
		  }
			    });


//$.ajax({
//	url:'{{route("GetPay",app()->getLocale())}}',
  //  type:'POST',
  //  data:{_token: '{{ csrf_token() }}',bill_id:bill_id,type:'P'},
  //   success: function(data){
//	 $("#payamount").val(data.sumpay);
  //   $("#refamount").val(data.sumref);
//	 $("#balancepay").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
//	 $('#paydiscount').val(data.discount);
	 
//	 $("#payamountd").val(data.sumpayd);
  //   $("#refamountd").val(data.sumrefd);
//	 $("#balancepayd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
   //  $('#paydiscountd').val(data.discountd);

	// $('#myTablePay').empty();
	// $('#myTablePay').html(data.html1);
//}
  //  });	
$('#paymentModal').modal('show');
//var totalpay=(document.getElementById("totalf").value);
//var balancepay=(document.getElementById("balance").value);
 //$("#totalpay").val(totalpay);
 // $("#balancepay").val(balancepay);
}


function savepayfacture()
{
var filter_g=document.getElementById("filter_g").value;
var bill_id=document.getElementById("reqIDPay").value;
var valamount=document.getElementById("valamount").value;
var currency=document.getElementById("selectcurrencyp").value;
var selectmethod=document.getElementById("selectmethod").value;
var datepay=document.getElementById("date_pay").value;

 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","filter_g":filter_g,"bill_id":bill_id,
	"valamount":valamount,"currency":currency,"selectmethod":selectmethod,"datepay":datepay},
   url: '{{route("SavePayFacture",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
      		  
}
 if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
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
	
</script>

@endsection	