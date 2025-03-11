<!--
   DEV APP
   Created date : 9-5-2023
-->
@extends('gui.main_gui')
@section('content')
 <div class="container-fluid bg-light">
  @include('inventory.reports.menu.report_menu')
   <div class="card  p-1">
      <div class="card-header report-menu">
	     <h5>{{__('Sales')}}</h5>
		 <div class="row m-1">
			 <div class="col-md-3">
			   <label class="label-size">
			    @switch(auth()->user()->type)
				 @case(1) {{__('Resource')}} @break
				 @case(2) {{__('Branch')}} @break
				@endswitch 
			   </label>
			   <input type="text" class="form-control form-control-border" value="{{$resource->full_name}}" disabled />
			 </div>
			 <div class="col-md-3 col-6">
				<label for="name" class="label-size">{{__('From date')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			<div class="col-md-3 col-6">
			    <label for="name" class="label-size">{{__('To date')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			 <div class="col-md-3">
			   <label class="label-size">{{__('Patient')}}</label>
			   <select class="select2_patient custom-select rounded-0" name="filter_patient" id="filter_patient">
				 
			   </select>
			 </div>
			 
          </div>
		  <div class="row m-1">
		     <div class="col-md-3">
			   <label class="label-size">{{__('Due Amount')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_due_amount" id="filter_due_amount" style="width:100%;">
				 <option value="0">{{__('Due amount')}}</option>
				 <option value="-1">{{__('>0')}}</option>
				
			   </select>
			 </div>
			 <div class="col-md-3">
			   <label class="label-size">{{__('Invoice')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_inv_type" id="filter_inv_type" style="width:100%;">
				 <option value="0">{{__('All Invoices')}}</option>
				 <option value="RP">{{__('Return Patient')}}</option>
				 <option value="CN">{{__('Credit Note')}}</option>
				
			   </select>
			 </div>
			 <div class="col-md-3">
			    <button id="generate" class="mt-4 m-1 btn btn-action">{{__("Generate")}}</button>
			 </div>
		  </div>
	  
	  </div>
      <div class="card-body">
	    <div class="row">
		   <div class="col-md-12">
			   <table id="sales_table" class="table table-striped table-bordered display nowrap"  style="font-size:0.9rem;width:100%;">
						<thead>
							<tr>
								<th>{{__('Invoice')}}</th>
								<th>{{__('Patient')}}</th>
								<th>{{__('Date')}}</th>
								<th>{{__('SubTotal')}}</th>
								<th>{{__('QST')}}</th>
								<th>{{__('GST')}}</th>
								<th>{{__('Total')}}</th>
								<th>{{__('Paid Amount')}}</th>
								<th>{{__('Due Amount')}}</th>
							    <th>{{__('Branch')}}</th>
							</tr>
						</thead>
				       <tbody></tbody>
					   <tfoot>
					     <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
					   </tfoot>
				</table>		
		   </div>
		 </div>	 
      </div>
   </div>			
 </div>

@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('body').addClass('sidebar-collapse');
	
    $('#reports_menu').show();
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});

	
	$('#filter_fromdate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
        disableMobile: true
	});
	
	$('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
        disableMobile: true
	});
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
	
	var clinic_num,user_type='{{auth()->user()->type}}';
	if(user_type==1){
		clinic_num='{{Session::get("inventory_branch_num")}}';
	}
	if(user_type==2){
		clinic_num='{{auth()->user()->clinic_num}}';
	}
	
	$('.select2_patient').select2({
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
						clinic_num:clinic_num,
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
    
   $($.fn.dataTable.tables(true)).DataTable()
			  .columns.adjust();	
   
	$('#sales_table').DataTable({
                    dom: 'Bfrtip',
					footerCallback: function ( row, data, start, end, display) {
                    var api = this.api(), data;
					 // Remove the formatting to get integer data for summation
					var intVal = function (i) {
						return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
					};
                   var current_total_am=0,current_total_gst=0,current_total_qst=0,current_total_st=0,current_total_due=0,current_total_pay=0;  
				  
				api.column(3,{ page: 'current' }).data().each(function(value,idx) {
                             
					        
							current_total_st+=parseFloat(value);
						
						
					   })
					   
				api.column(4,{ page: 'current' }).data().each(function(value,idx) {
                       
					   
							current_total_qst+=parseFloat(value);
						
						
					   })

				api.column(5,{ page: 'current' }).data().each(function(value,idx) {
									   
									   
											current_total_gst+=parseFloat(value);
										
										
									   })					   
					
				  api.column(6,{ page: 'current' }).data().each(function(value,idx) {
                       
					   
							current_total_am+=parseFloat(value);
						
						
					   })
					
					 api.column(7,{ page: 'current' }).data().each(function(value,idx) {
                       
					   
							current_total_pay+=parseFloat(value);
						
						
					   })
					   
					   
					   
					   api.column(8,{ page: 'current' }).data().each(function(value,idx) {
                       
					   
							current_total_due+=parseFloat(value);
						
						
					   })
					
					
					// Update footer
					$(api.column(0).footer()).html('<b>Total :</b>');
					$(api.column(3).footer()).html(parseFloat(current_total_st).toFixed(2)+'$');
					$(api.column(4).footer()).html(parseFloat(current_total_qst).toFixed(2)+'$');
					$(api.column(5).footer()).html(parseFloat(current_total_gst).toFixed(2)+'$');
					$(api.column(6).footer()).html(parseFloat(current_total_am).toFixed(2)+'$');
					$(api.column(7).footer()).html(parseFloat(current_total_pay).toFixed(2)+'$');
					$(api.column(8).footer()).html(parseFloat(current_total_due).toFixed(2)+'$');
					},	
					processing: false,
					paging: true,
					lengthMenu: [[50, 75, 100,200, -1], [50, 75, 100,200, "{{__('All')}}"]], 
                    searching: true,
					serverSide: true,
                    order : [[2,'desc'],[0,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
			        ajax: {
						url: "{{ route('inventory.reports.inventory_sales',app()->getLocale()) }}",
					    data: function (d) {
									
									 d.filter_patient=$('#filter_patient').val();								 
									 d.filter_fromdate=$('#filter_fromdate').val();
									 d.filter_todate=$('#filter_todate').val();
									 d.filter_due_amount=$('#filter_due_amount').val();
									 d.filter_inv_type=$('#filter_inv_type').val();
							}
					},
						 
					columns: [

						{data: 'id_invoice',render: function(data, type, row) {
							var invoice_data = data.split(';');
							if(invoice_data[1] !='nul' && invoice_data[2] !='nul'){
							return invoice_data[0]+'<br/>'+'{{__("Order")." #"}}'+invoice_data[1]+'<br/>'+'{{__("Ref. Invoice")}}'+' : '+invoice_data[2];
							}else{
							  if(invoice_data[2] !='nul')
								return invoice_data[0]+'<br/>'+'{{__("Ref. Invoice")}}'+' : '+invoice_data[2];
                              else{
							   if(invoice_data[1] !='nul')
								  return invoice_data[0]+'<br/>'+'{{__("Order")." #"}}'+invoice_data[1];
                               else
							      return invoice_data[0];
							  }
							}
						}},
						{data: 'patDetail',render: function(data, type, row) {
							if(data !=null && data !=""){
							var pat = data.split(',');
							return '{{__("Name")}}'+': '+pat[0]+'<br/>'+'{{__("HIN")}}'+': '+pat[1];
						    }else{
								return '{{__("Undefined")}}';
							}
						 
						 }
						 },
						{data: 'date_invoice'},
						{data: 'sub_total',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'qst',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'gst',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'total',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'montant_payer',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'solde_du',render: DataTable.render.number( null, null, 2,'','$' )},
						{data: 'branch_name'},
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
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "{{__('All')}}"
                                                 }
						
								},
							paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				         },
					
					buttons: [
						'pageLength',
						{
						extend:    'print',
						text:      '<i class="fas fa-print"></i>',
						titleAttr: '{{__("Print")}}',
						footer: true
                        },
						
						

						{
							extend:    'excelHtml5',
							text:      '<i class="fas fa-file-excel"></i>',
							titleAttr: 'Excel',
							footer: true
						},
						{
							extend:    'csvHtml5',
							text:      '<i class="fas fa-file-alt"></i>',
							titleAttr: 'CSV',
							footer: true
						},
						{
							extend:    'pdfHtml5',
							text:      '<i class="fas fa-file-pdf"></i>',
							orientation: 'portrait',
                            pageSize: 'A4',
							titleAttr: 'PDF',
							footer: true
						}
					
					],
					
					
				
				});
				
	$('#navbarNavDropdown').on('show.bs.dropdown', function () {
		  //recalculate widths
			$($.fn.dataTable.tables(true)).DataTable()
			  .columns.adjust();	
		})		
				
});
</script>
<script>
$(function(){
  
  
  $('#filter_fromdate').change(function(){
	var from_date = $('#filter_fromdate').val();
	
	if(from_date!=null && from_date!=''){
	//$('#filter_todate').removeAttr('disabled');
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
	//$('#sales_table').DataTable().ajax.reload();
});	
  
  $('#generate').click(function(e){
	  e.preventDefault();
	  $('#sales_table').DataTable().ajax.reload();
  });
  //$('#filter_todate,#filter_patient').change(function(){
	//$('#sales_table').DataTable().ajax.reload();
   //});
});
</script>
@endsection