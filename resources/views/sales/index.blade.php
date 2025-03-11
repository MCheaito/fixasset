<!--
 DEV APP
 Created date : 21-12-2022
-->
@extends('gui.main_gui')
@section('content')	
<div class="container-fluid">	
		<div class="row m-1">  	
			<div class="col-md-8">
			     <h3>{{__('Sales')}}</h3>
			</div>
			<div class="col-md-4">
			  <input type="text" readonly="true" class="form-control form-control-border border-width-2" value="{{$branch->full_name}}"/>
			</div>
			
        </div>								
			    
	 <div class="card card-menu">
	  
	     <div class="card-body p-0">
		    							
			         
							
							<div id="Command"  class="tab-pane">
								<div class="row m-1">
									<div class="col-md-2 mb-2">
										<select name="filter_status" id="filter_status_com" class="custom-select rounded-0">
											  <option value="O" {{ session('filter_status_com') == 'O' ? 'selected' : '' }}>{{__('Active')}}</option>
											  <option value="N" {{ session('filter_status_com') == 'N' ? 'selected' : '' }}>{{__('InActive')}}</option>
										 </select>	  
									</div>
										<div class="form-group col-md-3 select2-primary">
											<select class="select2_data custom-select rounded-0" name="selectpro" id="selectpro" style="width:100%;" >
											<option value="">{{__('Select Clients')}}</option>		
											@foreach($clients as $c)
											<option value="{{$c->id}}" {{ session('selectpro') == $c->id ? 'selected' : '' }}>{{$c->name}}</option>
											@endforeach 
											</select>
															
                                        </div>
											
										<div class="form-group col-md-3 select2-primary">
											<select class="select2_data custom-select rounded-0" name="filter_user" id="filter_user" style="width:100%;" >
											@if(auth()->user()->permission=='S')
											<option value="">{{__('Select Users')}}</option>
											@endif
											@foreach($users as $u)
											<option value="{{$u->id}}" {{ session('selectuser') == $u->id ? 'selected' : '' }}>{{$u->name}}</option>
											@endforeach 
											</select>
															
                                        </div>
												
									<div class="col-md-2 mb-2">	
										  <a id="btncommand" href="" class="float-right btn btn-action">{{__('New Visite')}}</a>
										 
									</div> 
									<div class="col-md-2 mb-2">	
										  <a id="btncommando" href="" class="float-right btn btn-action">{{__('New Order')}}</a>
										 
									</div> 
								 </div>
								 <div class="row m-1">
								 	<div class="col-md-2 mb-2">
										<select name="filter_rp_com" id="filter_rp_com" class="custom-select rounded-0">
											  <option value="">{{__('Sent/Not Sent')}}</option>
											  <option value="Y" {{ session('filter_rp_com') == 'Y' ? 'selected' : '' }}>{{__('Sent')}}</option>
											  <option value="N" {{ session('filter_rp_com') == 'N' ? 'selected' : '' }}>{{__('Not Sent')}}</option>
										 </select>	  
									</div>
								 <div class="col-md-2 mb-2">
										<select name="filter_v_com" id="filter_v_com" class="custom-select rounded-0">
											  <option value="">{{__('All/Valid/Facturation')}}</option>
											  <option value="Y"  {{ session('filter_v_com') == 'Y' ? 'selected' : '' }}>{{__('Valid')}}</option>
											  <option value="N"  {{ session('filter_v_com') == 'N' ? 'selected' : '' }}>{{__('Facturation')}}</option>
											   <option value="A"  {{ session('filter_v_com') == 'A' ? 'selected' : '' }}>{{__('valid/Facturation')}}</option>
										 </select>	  
									</div>
									<div class="col-md-2 mb-2">
										<select name="filter_d_com" id="filter_d_com" class="custom-select rounded-0">
											  <option value="">{{__('All/Quotation')}}</option>
											  <option value="Y"  {{ session('filter_d_com') == 'Y' ? 'selected' : '' }}>{{__('Quotation')}}</option>
										 </select>	  
									</div>
										<div class="col-md-2 mb-2">
										<select name="filter_typein" id="filter_typein" class="custom-select rounded-0">
											  <option value="">{{__('Order/Visit')}}</option>
											  <option value="O"  {{ session('filter_typein') == 'O' ? 'selected' : '' }}>{{__('Order')}}</option>
											  <option value="V" {{ session('filter_typein') == 'V' ? 'selected' : '' }}>{{__('Visit')}}</option>
										 </select>	  
									</div>
								
								 </div>
								<div class="row m-1">
								   <div class="col-lg-12">
										 <table id="Command_table" class="table table-hover nowrap responsive"  style="width:100%;">
										<thead>
										<tr>
										<th class="all">{{__('#')}}</th>
										<th class="all">{{__('ID')}}</th>
										<th class="all">{{__('Clients')}}</th>
										<th class="all">{{__('User')}}</th>
										<th class="all">{{__('Date')}}</th>
										<th class="all">{{__('Due Date')}}</th>
										<th class="all">{{__('Notes')}}</th>
										<th class="all">{{__('Delivery')}}</th>
										<th class="all">{{__('Total')}}</th>
										<th class="all">{{__('Received')}}</th>
										<th class="all">{{__('Balance')}}</th>
										<th class="all">{{__('Email')}}</th>
										<th class="all">{{__('Validated')}}</th>
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
                    <!--</div>-->				
                <!--</section>--> 													
		    </div>
        <!--</div>-->
 		
  </div>	  

@endsection	
@section('scripts')
<script>
function clearSelect2(){
	var supplier=$('#selectpro').val()
	var supplier=$('#selectuser').val()
	$('.select2_allitems').val(null).trigger('change');
	$('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("loadOtherItemsCMD",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
					supplier:supplier,
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
}
</script>   
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	             $('.invoices_tab').click(function (e) {
				   e.preventDefault();
				   $($.fn.dataTable.tables(true)).DataTable()
                       .columns.adjust();
				   
				   if($('#Purchasing_table').length){
					  $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }
				   
				   if($('#Sales_table').length){
					 $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }								
				   //$('#active_tab').val($(this).data('id'));
				   var data=$(this).attr('href').split('#');
				   
				   $(this).tab('show');
				   
				  		}); 

				$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
					  $($.fn.dataTable.tables(true)).DataTable()
                       .columns.adjust();
					 
					 if($('#Purchasing_table').length){
					  $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }
				   
				   if($('#Sales_table').length){
					 $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }			
					  		
				 if($('#Command_table').length){
					  $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }	
				 if($('#Accounting_table').length){
					  $($.fn.dataTable.tables(true)).DataTable()
						.columns.adjust()
						.responsive.recalc();
					  }	
					  
					var id = $(e.target).attr("href");
					
					localStorage.setItem('invoicesTab', id)
				});
				
				

				var invTab = localStorage.getItem('invoicesTab');
				
				if (invTab != null) {
					$('a[data-toggle="tab"][href="' + invTab + '"]').tab('show');
					
				}else{
					$('a[data-toggle="tab"][href="#Sales"]').tab('show');
					
				}							
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
	
    
 
	$('#Purchasing_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[4,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_p').val();
								d.filter_free=$('#filter_free').val();
								d.selecttype = '1';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',orderable:true,searchable:false},
						{data: 'id_invoice',name:'tbl_inventory_invoices_request.clinic_inv_num'},
						{data:'nbaccount',searchable:false},
						{data:'reference'},
						{data: 'name',name:'tbl_fournisseur.name',render: function(data, type, row) {
							if(data!=null && data!=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'date_invoice',name:'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'sqty', searchable: false},
						{data: 'total'},
						{data: 'total_pay'},
						{data: 'inv_balance'},
						{data: 'sub_total',name:'total'},
						{data: 'qst'},
						{data: 'gst'},
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
					// Customize row creation
						createdRow: function(row, data, dataIndex) {
						if (data.sqty === '' || data.sqty ===null) {
							$(row).find('td:eq(6)').css('background-color', 'red'); // Change background color of the sqty column
							}
							},
					fixedColumns:   {
				            left: 0,
				            right: 1
				        }
					

				});
				
				

	    $('#Sales_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[4,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_s').val();
								d.selecttype = '2';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',orderable:false,searchable:false},
						{data: 'id_invoice',render: function(data, type, row) {
							var invoice_data = data.split(';');
							if(invoice_data[1] !='nul' && invoice_data[3]=='Y'){
							 if(invoice_data[2] == 'Y')
							   return invoice_data[0]+'<br/>'+'{{__("Cl. Order").",".__("Warranty")." #"}}'+invoice_data[1];	
							 else
							  return invoice_data[0]+'<br/>'+'{{__("GF. Order").",".__("Warranty")." #"}}'+invoice_data[1];	
	 
							}
							if(invoice_data[1] !='nul' && invoice_data[3]!='Y'){
							  if(invoice_data[2] == 'Y')
							   return invoice_data[0]+'<br/>'+'{{__("Cl. Order")." #"}}'+invoice_data[1];	  
							  else
						      return invoice_data[0]+'<br/>'+'{{__("GF. Order")." #"}}'+invoice_data[1];
							}
						   
						   return invoice_data[0];
							
						}},
						{data: 'date_invoice',name:'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						
						{data: 'total'},
						{data: 'total_pay'},
						{data: 'inv_balance'},
						{data: 'sub_total',name:'total'},
						{data: 'qst'},
						{data: 'gst'},
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
				
				$('#ReturnPatient_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[3,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_rp').val();
								d.filter_rp=$('#filter_rp').val();
								d.selecttype = '3';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',searchable:false,orderable:false},
						{data: 'id_invoice',render: function(data, type, row) {
						  var data_invoice = data.split(';');
						  if(data_invoice[1] != '' && data_invoice[2] !=''){
							 return data_invoice[0]+'<br/>'+data_invoice[1]+'<br/>'+'{{__("Ref. Invoice")." : "}}'+data_invoice[2];  
						  }else{
							  if(data_invoice[1] != ''){
								  return data_invoice[0]+'<br/>'+data_invoice[1]; 
							  }else{
							   if(data_invoice[2]!=''){
								 return data_invoice[0]+'<br/>'+'{{__("Ref. Invoice")." : "}}'+data_invoice[2];  
  							   }else{
							   return data_invoice[0];
							   }
							  }
						  }
						}
						},
						{data: 'patDetail',render: function(data, type, row) {
							if(data !=null && data !=""){
							var pat = data.split(',');
							return '{{__("Name")}}'+': '+pat[0]+'<br/>'+'{{__("HIN")}}'+': '+pat[1];
						    }else{
								return '{{__("Undefined")}}';
							}
						 }
						 },
						{data: 'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'sub_total',name:'total'},
						{data: 'qst'},
						{data: 'gst'},
						{data: 'total_pay'},
						{data: 'inv_balance'},
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
				
				$('#ReturnSupplier_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[3,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_rs').val();
								d.filter_rs=$('#filter_rs').val();
								d.selecttype = '4';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',searchable:false,orderable:false},
						{data: 'id_invoice',render: function(data, type, row) {
							data_invoice=data.split(';');
							
							if(data_invoice[1] != '' && data_invoice[2] !=''){
							 return data_invoice[0]+'<br/>'+data_invoice[1]+'<br/>'+'{{__("Ref. Invoice")." : "}}'+data_invoice[2];  
						  }else{
							  if(data_invoice[1] != ''){
								  return data_invoice[0]+'<br/>'+data_invoice[1]; 
							  }else{
							   if(data_invoice[2]!=''){
								 return data_invoice[0]+'<br/>'+'{{__("Ref. Invoice")." : "}}'+data_invoice[2];  
  							   }else{
							   return data_invoice[0];
							   }
							  }
						  }
						}},
						{data: 'name',name:'tbl_fournisseur.name',render: function(data, type, row) {
							if(data!=null && data!=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'invoice_sup'},
						{data: 'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'sub_total',name:'total'},
						{data: 'total_pay'},
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
				
				$('#Remise_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[3,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_rm').val();
								d.selecttype = '99';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',searchable:false,orderable:false},
						{data: 'id_invoice',name:'tbl_inventory_invoices_request.clinic_inv_num'},
						{data: 'patDetail',render: function(data, type, row) {
							if(data !=null && data !=""){
							var pat = data.split(',');
							return '{{__("Name")}}'+': '+pat[0]+'<br/>'+'{{__("HIN")}}'+': '+pat[1];
						    }else{
								return '{{__("Undefined")}}';
							}
						 }
						 },
						{data: 'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data:'remise_invoice',name:'remise.clinic_inv_num',render: function(data, type, row) {
							if(data !=null && data !=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'sub_total',name:'total'},
						{data: 'qst'},
						{data: 'gst'},
						{data: 'total_pay'},
						{data: 'inv_balance'},
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
		$('#Adjacement_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[3,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_ad').val();
								d.selecttype = '5';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',searchable:false,orderable:false},
						{data: 'id_invoice',name:'clinic_inv_num'},
						{data: 'item_code',name:'tbl_details.item_code'},
						{data: 'item_name',name:'tbl_details.item_name'},
						{data:'typeadjacement',searchable:false,render: function(data, type, row) {
							if(data !=null && data =="1"){
								return '+';
							}else{
								return '-';
							}
						}},						
						{data: 'qty',name:'tbl_details.qty'},
						{data: 'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'gstock',name:'gstock'},
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

  
$('#Command_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[4,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('sales.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_com').val();
								d.filter_rp_com=$('#filter_rp_com').val();
								d.filter_v_com=$('#filter_v_com').val();
								d.filter_d_com=$('#filter_d_com').val();
								d.filter_p_com=$('#filter_p_com').val();
								d.selectpro=$('#selectpro').val();
								d.filter_typein=$('#filter_typein').val();
								
								d.selectcode=$('#selectcode').val();
								d.selecttype = '6';
								d.filter_user=$('#filter_user').val();
								}
					},
						 
					columns: [
						{data: 'DT_RowIndex',orderable:false,searchable:false},
						{data: 'id_invoice',name:'tbl_inventory_invoices_request.clinic_inv_num'},
						{data: 'name',orderable: true,name:'tbl_clients.name',render: function(data, type, row) {
							if(data!=null && data!=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'nameuser',name:'nameuser',render: function(data, type, row) {
							if(data!=null && data!=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'date_invoice',name:'date_invoice',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'date_due',name:'date_due',render: function(data, type, row) {
							if(data!=null && data!=""){
								res1= data.split(' ');
							return res1[0]+'<br/>'+res1[1]
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'notes', 
						name: 'notes',
						createdCell: function (td) {
							$(td).css('white-space', 'normal');
							$(td).css('word-wrap', 'break-word');
						$(td).css('overflow-wrap', 'break-word');}},
						{data: 'date_delivery',name:'date_delivery',render: function(data, type, row) {
							if(data!=null && data!=""){
								return data;
							}else{
								return '{{__("Undefined")}}';
							}
						}},
						{data: 'sub_total',name:'total'},
						{data: 'inv_balance'},
						{
							data: 'inv_balance',
							render: function(data, type, row) {
								  var total = parseFloat(row.sub_total) || 0;
									var invoiceBalance = parseFloat(row.inv_balance) || 0;
									var cbalance = total - invoiceBalance;
									return cbalance.toFixed(2);
							}
						},
						{data: 'Email',orderable: false, searchable: false},
						{data: 'validate',orderable: false, searchable: false},
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
 
 $('#Accounting_table').DataTable({
                    stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[3,'desc'],[1,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.invoices.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status_acc').val();
								d.selecttype = '7';
								
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex',orderable:false,searchable:false},
						{data: 'serial'},
						{data: 'refer'},
						{data: 'datein',name:'datein',render: function(data, type, row) {
							res= data.split(' ');
							return res[0]+'<br/>'+res[1];
						}},
						{data: 'ffrom'},
						{data: 'ltamount1'},
						{data: 'dtamount1'},
						{data: 'etamount1'},
						{data: 'fto'},
						{data: 'ltamount2'},
						{data: 'dtamount2'},
						{data: 'etamount2'},
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
 
$('#filter_status_p,#filter_free').change(function(){
	  
	   $('#Purchasing_table').DataTable().ajax.reload();
     });	

$('#filter_status_s').change(function(){
	  
	   $('#Sales_table').DataTable().ajax.reload();
     });	

$('#filter_rs,#filter_status_rs').change(function(){
	  
	   $('#ReturnSupplier_table').DataTable().ajax.reload();
     });	

$('#filter_rp,#filter_status_rp').change(function(){
	  
	   $('#ReturnPatient_table').DataTable().ajax.reload();
     });	

$('#filter_status_rm').change(function(){
	  
	   $('#Remise_table').DataTable().ajax.reload();
     });		 
	
$('#filter_status_ad').change(function(){
	  
	   $('#Adjacement_table').DataTable().ajax.reload();
     });
$('#filter_status_com,#filter_rp_com,#filter_v_com,#filter_p_com,#filter_d_com,#selectcode,#selectpro,#filter_user,#filter_user,#filter_typein').change(function(){
	  
	   $('#Command_table').DataTable().ajax.reload();
     });
	 
$('#filter_status_acc').change(function(){
	  
	   $('#Accounting_table').DataTable().ajax.reload();
     });
$('#filter_cmd,#filter_status_cmd').change(function(){
	  
	   $('#Commands_table').DataTable().ajax.reload();
     });		 
});




function deleteRemise(id,state){
if (state=='activate')
{
MaTitle='{{__("Are you sure to reactivate this bill?")}}';	
}else{
MaTitle='{{__("Are you sure to delete the guaranty bill?")}}';
}
	
Swal.fire({
  title: MaTitle,
  html:'',
  showDenyButton: true,
  confirmButtonText: '{{__("OK")}}',
  denyButtonText: '{{__("Cancel")}}',
  customClass: 'w-auto'
}).then((result) => {
  if (result.isConfirmed) {
	  $.ajax({
            url: '{{route("DeleteInventory",app()->getLocale())}}',
		   data: {'id':id,state:state},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
                title:data.success,
			    toast:true,
                icon:"success",
			    timer:3000,
				position:"bottom-right",
			    showConfirmButton:false
			  });
			  var name=data.type;
			  //alert(name);
			  $('#'+name+'_table').DataTable().ajax.reload();

		       } 
			 }
       });	
	  
  }else if (result.isDenied) {
    return false;
  }
})

}
function getPDF(id,supplier_id,id_invoice,name){
	   $.ajax({
           url: '{{route("salesgenerate_cmd_pdf",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id,'supplier_id':supplier_id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			File_name='Order_'+id_invoice+'_'+name+'.pdf';
			link.download=(File_name);
			link.click();
			Swal.fire({title:'{{__("Order Downloaded")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
		
			    });
}

function sendPDF(id){
	    
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	    $.ajax({
			type : 'POST',
			url : '{{route("salessend_supplier_email",app()->getLocale())}}',
			data : { id: id},
			dataType: 'JSON',
			success: function(data){
					  if(data.error){
						  Swal.fire({html:data.error,icon:'error',customClass:'w-auto'});
					  }
                      
                      if(data.success){
						Swal.fire({toast:true,title:data.success,position:'bottom-right',icon:'success',showConfirmButton:false,timer:3000});
						  $('#Command_table').DataTable().ajax.reload(null, false);

					 
					 }					  
						
						}
					});	 
}

function deleteaccounting(id,state){
Swal.fire({
  title: '{{__("Are you sure?")}}',
  html:'',
  showDenyButton: true,
  confirmButtonText: '{{__("OK")}}',
  denyButtonText: '{{__("Cancel")}}',
  customClass: 'w-auto'
}).then((result) => {
  if (result.isConfirmed) {
	  $.ajax({
            url: '{{route("DeleteAccounting",app()->getLocale())}}',
		   data: {'id':id,state:state},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
                title:data.success,
			    toast:true,
                icon:"success",
			    timer:3000,
				position:"bottom-right",
			    showConfirmButton:false
			  });
			  //var name=data.type;
			  //alert(name);
			  $('#Accounting_table').DataTable().ajax.reload();

		       } 
			 }
       });	
	  
  }else if (result.isDenied) {
    return false;
  }
})

}

function validateCmd1(id,is_valid1){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("SalesValidate1",app()->getLocale())}}',
			data:{id:id,is_valid1:is_valid1},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			  			  $('#Command_table').DataTable().ajax.reload(null, false);


				 });
				 
			}
		});
}

function validateCmd2(id,clientid,is_valid2){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("SalesValidate2",app()->getLocale())}}',
			data:{id:id,is_valid2:is_valid2,cleintid:clientid},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			 			  $('#Command_table').DataTable().ajax.reload(null, false);


				 });
				 
			}
		});
}

function cdoneCmd1(id,cdone){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("SalesDone",app()->getLocale())}}',
			data:{id:id,cdone:cdone},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			  			  $('#Command_table').DataTable().ajax.reload(null, false);


				 });
				 
			}
		});
}

function downloadPDF(id, lang) {
    $.ajax({
        url: '{{route("file.download",app()->getLocale())}}', // Use template literals to include the lang parameter
        data: { id: id },
        type: 'POST',
        xhrFields: { responseType: 'blob' }, // Set the response type to blob
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
       success: function(data, status, xhr) {
            // Create a new Blob object using the response data
            var blob = new Blob([data], { type: 'application/pdf' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.target = '_blank'; // Open in a new tab
            
            var contentDisposition = xhr.getResponseHeader('Content-Disposition');
            var fileName = 'view.pdf'; // Default file name if header is not set
            
            if (contentDisposition) {
                var matches = /filename="([^"]*)"/.exec(contentDisposition);
                if (matches != null && matches[1]) { 
                    fileName = matches[1]; 
                }
            }
            
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Optionally, show a success message
            Swal.fire({
                title: '{{ __("Order Viewed") }}',
                icon: "success",
                toast: true,
                showConfirmButton: false,
                timer: 3000,
                position: "bottom-right"
            });
        },
        error: function(xhr, status, error) {
            // Handle AJAX errors
            Swal.fire({
                title: '{{ __("Error") }}',
                text: '{{ __("File Not Found.") }}',
                icon: "error",
                showConfirmButton: true
            });
        }
    });
}


function cpaidCmd1(id,cpaid){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("SalesPaid",app()->getLocale())}}',
			data:{id:id,cpaid:cpaid},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
				  
					  
				  }else{
					
					  
					  
				  }
				

			 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			  $('#Command_table').DataTable().ajax.reload(null, false);

				 });
				 
			}
		});
}

function EmailSent(id,state){
		//var is_valid1= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("EmailSentValidate",app()->getLocale())}}',
			data:{id:id,state:state},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(data.type=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
			 			  $('#Command_table').DataTable().ajax.reload(null, false);


				 });
				 
			}
		});
}

$('#selectpro').change(function(){
	clearSelect2();
 });
 $('#selectuser').change(function(){
	clearSelect2();
 });
function freeCmd1(id,is_free){

   
       // var id=$('#invoice_id').val();
		//var is_free= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("CmdFree",app()->getLocale())}}',
			data:{id:id,is_free:is_free},
			type: 'post',
			dataType: 'json',
			success: function(data){
				  if(is_free=='Y'){
					  
					  
				  }else{
					
					  
					  
				  }
				  
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 //window.location.href = data.location;
					  $('#Purchasing_table').DataTable().ajax.reload(null, false);
				 });
				 
			}
		});
   }


 
function deleteInventory(id,state){
Swal.fire({
  title: '{{__("Are you sure?")}}',
  html:'{{__("Please note, this operation will affects the number of items in the stock")}}',
  showDenyButton: true,
  confirmButtonText: '{{__("OK")}}',
  denyButtonText: '{{__("Cancel")}}',
  customClass: 'w-auto'
}).then((result) => {
  if (result.isConfirmed) {
	  $.ajax({
            url: '{{route("DeleteInventory",app()->getLocale())}}',
		   data: {'id':id,state:state},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
                title:data.success,
			    toast:true,
                icon:"success",
			    timer:3000,
				position:"bottom-right",
			    showConfirmButton:false
			  });
			  var name=data.type;
			  $('#'+name+'_table').DataTable().ajax.reload();

		       } 
			 }
       });	
	  
  }else if (result.isDenied) {
    return false;
  }
})

}
 	
$(function(){
	$('#btnpurchase').click(function() {
		
     $("a#btnpurchase").attr("href","{{route('NewInvoices',[app()->getLocale(),'1'])}}");
	
    });	
	
	$('#btnsale').click(function() {
		
     $("a#btnsale").attr("href","{{route('NewSales',[app()->getLocale(),'2'])}}");
	
    });
	
	$('#btnrpatient').click(function() {
		
     $("a#btnrpatient").attr("href","{{route('NewRSales',[app()->getLocale(),'3'])}}");
	
    });	
	$('#btnrsupplier').click(function() {
		
     $("a#btnrsupplier").attr("href","{{route('NewRInvoices',[app()->getLocale(),'4'])}}");
	
    });	

	$('#btncommand').click(function() {
		
     $("a#btncommand").attr("href","{{route('NewCommandSales',[app()->getLocale(),'6','V'])}}");
	
    });	
	$('#btncommando').click(function() {
		
     $("a#btncommando").attr("href","{{route('NewCommandSales',[app()->getLocale(),'6','O'])}}");
	
    });	
	
	$('#btnaccounting').click(function() {
		
     $("a#btnaccounting").attr("href","{{route('NewAccounting',[app()->getLocale()])}}");
	
    });	
	
});	
</script>
<script>
function PopupCenter(url, title, w, h) {  
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
                  
        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
                  
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
        var top = ((height / 2) - (h / 2)) + dualScreenTop;  
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
      
        // Puts focus on the newWindow  
        if (window.focus) {  
            newWindow.focus();  
        }  
    }  
</script>
@endsection	
