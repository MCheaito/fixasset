<!--
   DEV APP
   Created date : 9-12-2023
-->
@extends('gui.main_gui')
@section('styles')
<style>
/*Horizontally center header and footer*/
.tabulator-print-header, tabulator-print-footer{
    text-align:center;
}
</style>
@endsection
@section('content')
 <div class="container-fluid bg-light">
   @include('inventory.reports.menu.report_menu')
   <div class="card">
      <div class="card-header report-menu">
	      <h5>{{__('Orders Reports')}}</h5>
		 <div class="row m-1">
			 <div class="col-md-4">
			   <label class="label-size">{{__('Lab')}}</label>
			   <input type="text" class="form-control form-control-border" value="{{$resource->full_name}}" disabled />
			 </div>
			 <div class="col-md-2 col-6">
				<label for="name" class="label-size">{{__('From date')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			<div class="col-md-2 col-6">
			    <label for="name" class="label-size">{{__('To date')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			<div class="col-md-4">
			   <label class="label-size">{{__('Supplier')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_supplier" id="filter_supplier">
				 <option value="0">{{__('Choose a supplier')}}</option>
				 @foreach($fournisseur as $s)
				   <option value="{{$s->id}}">{{$s->name}}</option>
				 @endforeach
			   </select>
			 </div>
			 	<div class="col-md-4 col-6">
				 <label class="label-size">{{__('Item')}}</label>
										<select class="select2_allitems custom-select rounded-0" name="selectcode" id="selectcode" style="width:100%;" >
												
										</select>
										<input  type="hidden" id="typeCode"/>
										</div>	
			<div class="col-md-2 mb-2">
			 <label class="label-size">{{__('Sent Email')}}</label>
										<select name="filter_rp_com" id="filter_rp_com" class="custom-select rounded-0">
											  <option value="">{{__('Sent/Not Sent')}}</option>
											  <option value="Y" >{{__('Sent')}}</option>
											  <option value="N" >{{__('Not Sent')}}</option>
										 </select>	  
									</div>
								 <div class="col-md-2 mb-2">
								  <label class="label-size">{{__('Validated')}}</label>
										<select name="filter_v_com" id="filter_v_com" class="custom-select rounded-0">
											  <option value="">{{__('Valid/Not Valid')}}</option>
											  <option value="Y"  >{{__('Valid1')}}</option>
											  <option value="N" >{{__('Valid2')}}</option>
											   <option value="A"  >{{__('valid1/Valid2')}}</option>
										 </select>	  
									</div>
									<div class="col-md-2 mb-2">
									 <label class="label-size">{{__('Status')}}</label>
										<select name="filter_d_com" id="filter_d_com" class="custom-select rounded-0">
											  <option value="">{{__('Done/Pending')}}</option>
											  <option value="Y"  >{{__('Done')}}</option>
											  <option value="N" >{{__('Pending')}}</option>
										 </select>	  
									</div>
									<div class="col-md-2 mb-2">
									 <label class="label-size">{{__('Paid')}}</label>
										<select name="filter_p_com" id="filter_p_com" class="custom-select rounded-0">
											  <option value="">{{__('Paid/Not Paid')}}</option>
											  <option value="Y"  >{{__('Paid')}}</option>
											  <option value="N" >{{__('Not Paid')}}</option>
										 </select>	  
									</div>
			  	<div class="col-md-2 mb-2">
				 <label class="label-size">{{__('Active')}}</label>
										<select name="filter_status" id="filter_status_com" class="custom-select rounded-0">
											  <option value="O">{{__('Active')}}</option>
											  <option value="N" >{{__('InActive')}}</option>
										 </select>	  
				</div>
			 <div class="col-md-3">
			  <button id="generate" class="mt-4 m-1 btn btn-action">{{__("Generate")}}</button>
			</div>
          </div>
	      
	  </div>
      <div class="card-body">
	    <div class="row">
		    <div class="mb-1 col-md-7">
                 <button id="print-table" class="btn btn-secondary rounded-pill"><i class="fas fa-print" title="{{__('Print')}}"></i></button>
				 <button id="download-xlsx" class="btn btn-secondary rounded-pill"><i class="fas fa-file-excel" title="{{__('Excel')}}"></i></button>
				 <button id="download-csv" class="btn btn-secondary rounded-pill"><i class="fas fa-file-alt" title="{{__('CSV')}}"></i></button>
				 <button id="download-pdf" class="btn btn-secondary rounded-pill"><i class="fas fa-file-pdf" title="{{__('PDF')}}"></i></button>
            </div>
			<div class="mb-1 input-group col-md-5">
			    <label class="col-sm-2 col-form-label">{{__('Search')}}</label>
			    <input id="search_data" type="text" class="col-sm-10 form-control" placeholder="">
			</div>
		   <div class="col-md-12">
			   
			   
			   <div id="example-table" class="table-bordered"></div>
			   
			 
		   </div>
		 </div>	 
      </div>
   </div>			
 </div>

@endsection
@section('scripts')

<script>
function clearSelect2(){
	var supplier=$('#selectpro').val()
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
	$('body').addClass('sidebar-collapse');
    $('#reports_menu').show();
    $('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadItems",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
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
	
	var lang='{{app()->getLocale()}}';
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
	
	var table = new Tabulator("#example-table", {
    ajaxURL:"{{route('inventory.reports.inventory_orders',app()->getLocale())}}", //ajax URL
    ajaxParams: function(){

        var filter_status_com=$('#filter_status_com').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_rp_com=$('#filter_rp_com').val();
		var filter_d_com=$('#filter_d_com').val();
		var filter_p_com=$('#filter_p_com').val();
		var selectpro=$('#filter_supplier').val();
		var selectcode=$('#selectcode').val();
		return {filter_status_com:filter_status_com,filter_rp_com:filter_rp_com,
				filter_fromdate:filter_fromdate,
				filter_todate:filter_todate,filter_d_com:filter_d_com,
				filter_p_com:filter_p_com,selectpro:selectpro,selectcode:selectcode};
           },
	height:"400px",
	placeholder:"{{__('No data is found')}}",
	pagination:true, //enable pagination.
	paginationSize:50,
    paginationSizeSelector:[50,75,100,200,true],
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	downloadConfig:{
        columnHeaders:true, //do not include column headers in downloaded table
        columnGroups:true, //do not include column groups in column headers for downloaded table
        rowGroups:true, //do not include row groups in downloaded table
        columnCalcs:true, //do not include column calcs in downloaded table
        dataTree:true, //do not include data tree in downloaded table
    },
	locale:true,
	langs:{
        "en":{ 
            "data":{
                "loading":"{{__('Loading')}}", //data loader text
                "error":"{{__('Error')}}", //data error text
             },
            "pagination":{
                "page_size":"{{__('Page')}}",
				"first":"{{__('First')}}",
                "first_title":"{{__('First Page')}}",
                "last":"{{__('Last')}}",
                "last_title":"{{__('Last Page')}}",
                "prev":"{{__('Previous')}}",
                "prev_title":"{{__('Previous Page')}}",
                "next":"{{__('Next')}}",
                "next_title":"{{__('Next Page')}}",
                "all":"{{__('All')}}",
            },
        },
	},
	
   columns: [
    {title: "{{__('#')}}", field: "id"},
    {title: "{{__('Order Nb')}}", field: "clinic_inv_num"},
    {title: "{{__('Supplier#')}}", field: "name"},
    {title: "{{__('Date ')}}", field: "date_invoice"},
    {
        title: "{{__('Total')}}",
        field: "total",
        bottomCalc: "sum",
        formatter: "money",
        formatterParams: {thousand: ",", precision: 2},
        bottomCalcFormatter: "money",
        bottomCalcFormatterParams: {thousand: ",", precision: 2}
    },
    {
        title: "{{__('Received')}}",
        field: "inv_balance",
        bottomCalc: "sum",
        formatter: "money",
        formatterParams: {thousand: ",", precision: 2},
        bottomCalcFormatter: "money",
        bottomCalcFormatterParams: {thousand: ",", precision: 2}
    },
    {
        title: "{{__('Remain')}}",
        field: "total-inv_balance",
        bottomCalc: "sum",
        mutator: function(value, data) {
            return data.total - data.inv_balance;
        },
        formatter: "money",
        formatterParams: {thousand: ",", precision: 2},
        bottomCalcFormatter: "money",
        bottomCalcFormatterParams: {thousand: ",", precision: 2}
    },
    {
        title: "{{__('Payment')}}",
        field: "total_pay",
        bottomCalc: "sum",
        formatter: "money",
        formatterParams: {thousand: ",", precision: 2},
        bottomCalcFormatter: "money",
        bottomCalcFormatterParams: {thousand: ",", precision: 2}
    },
    {title: "{{__('Email')}}", field: "email_sent"},
    {title: "{{__('Done')}}", field: "cdone"},
    {title: "{{__('Paid')}}", field: "cpaid"},
    {title: "{{__('Validated 1')}}", field: "is_valid1"},
    {title: "{{__('Validated 2')}}", field: "is_valid2"}
]


    });
	


	
$("#search_data").keyup(function () {
            table.setFilter(matchAny, { value: $("#search_data").val() });
            if ($("#search_data").val() == " "){
              table.clearFilter()
            }
   });
 
//trigger download of data.pdf file
document.getElementById("download-pdf").addEventListener("click", function(){
    table.download("pdf", "orders.pdf", {
        orientation:"portrait", //set page orientation to portrait
        title:"{{__('Orders')}}", //add title to report
    });
});	
	
//print button
document.getElementById("print-table").addEventListener("click", function(){
   table.print("active", true, {columnGroups:false});
});	
	
//trigger download of data.csv file
document.getElementById("download-csv").addEventListener("click", function(){
    table.download("csv", "orders.csv");
});

//trigger download of data.xlsx file
document.getElementById("download-xlsx").addEventListener("click", function(){
    table.download("xlsx", "orders.xlsx", {sheetName:"{{__('Orders')}}"});
});


$('#generate').click(function(e){
	e.preventDefault();
	  var filter_status_com=$('#filter_status_com').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_rp_com=$('#filter_rp_com').val();
		var filter_d_com=$('#filter_d_com').val();
		var filter_p_com=$('#filter_p_com').val();
		var selectpro=$('#filter_supplier').val();
		var selectcode=$('#selectcode').val();
		
	table.setData("{{route('inventory.reports.inventory_orders',app()->getLocale())}}", 
	              {filter_status_com:filter_status_com,filter_rp_com:filter_rp_com,
				filter_fromdate:filter_fromdate,
				filter_todate:filter_todate,filter_d_com:filter_d_com,
				filter_p_com:filter_p_com,selectpro:selectpro,selectcode:selectcode});
	
});
	
//custom filter function
//custom filter function
function matchAny(data, filterParams){
    //data - the data for the row being filtered
    //filterParams - params object passed to the filter
    var filterValue = filterParams.value.toLowerCase();
    var match = false;

    for(var key in data){
      if (JSON.stringify(data[key]).toLowerCase().search(filterValue) != -1) {
            match = true;
        }
    }

    return match;
}

	
	
  
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
});	
   
});
</script>
@endsection