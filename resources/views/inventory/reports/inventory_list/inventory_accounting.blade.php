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
	      <h5>{{__('Accounting Reports')}}</h5>
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
			 <div class="col-md-2">
			   <label class="label-size">{{__('From Account')}}</label>
										<select class="select2_data custom-select rounded-0" name="fselectpro" id="fselectpro" style="width:100%;" >
											<option value="">{{__('Choose a supplier')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}">{{$fournisseurs->name}}</option>
											@endforeach 
										</select>
			 </div>
			  <div class="col-md-2">
			   <label class="label-size">{{__('To Account')}}</label>
										</select><select class="select2_data custom-select rounded-0" name="tselectpro" id="tselectpro" style="width:100%;" >
											<option value="">{{__('Choose a supplier')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}">{{$fournisseurs->name}}</option>
											@endforeach 
										</select>
			 </div>
			   <div class="col-md-2">
			   <label class="label-size">{{__('Type')}}</label>
			    <select class="select2_data custom-select rounded-0" name="stype" id="stype" style="width:100%;" >
													<option value="">{{__('Select Type')}}</option>		
													<option value="0">{{__('Receipts')}}</option>		
													<option value="1">{{__('Cashing')}}</option>
													<option value="2">{{__('On the account')}}</option>
													<option value="3">{{__('Invoices')}}</option>
													<option value="4">{{__('Returned')}}</option>
													<option value="5">{{__('Discount')}}</option>
				</select>
			 </div>
			   <div class="col-md-2">
			   <label class="label-size">{{__('Currency')}}</label>
			  	<select class="select2_data custom-select rounded-0" name="fcurrency" id="fcurrency" style="width:100%;" >
													<option value="">{{__('Select Currency')}}</option>	
													<option value="LBP">{{__('LBP')}}</option>		
													<option value="USD">{{__('USD')}}</option>
													<option value="EUR">{{__('EUR')}}</option>
				</select>
			 </div>
			   <div class="col-md-2">
			   <label class="label-size">{{__('User')}}</label>
										</select><select class="select2_data custom-select rounded-0" name="alluser" id="alluser" style="width:100%;" >
											<option value="">{{__('Choose a User')}}</option>		
											@foreach($all_user as $Users)
											<option value="{{$Users->id}}">{{$Users->fname}}</option>
											@endforeach 
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
    ajaxURL:"{{route('inventory.reports.inventory_accounting',app()->getLocale())}}", //ajax URL
    ajaxParams: function(){
        var filter_from_acc=$('#fselectpro').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_type=$('#stype').val();
		var filter_to_acc=$('#tselectpro').val();
		var filter_currency=$('#fcurrency').val();
		var filter_user=$('#alluser').val();
		return {filter_user:filter_user,filter_from_acc:filter_from_acc,filter_to_acc:filter_to_acc,filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_type:filter_type,filter_currency:filter_currency};
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
    columns:[
        {title:"{{__('#')}}", field:"id"},
		{title:"{{__('Account_ID#')}}", field:"account_id"},
		{title:"{{__('From ')}}", field:"name1"},
		{title:"{{__('$Currency')}}", field:"curency1"},
		{title:"{{__('$Amount')}}", field:"amount1",bottomCalc:"sum"},
		{title:"{{__('To').'+'}}", field:"name2"},
		{title:"{{__('$Currency').'+'}}", field:"curency2"},
		{title:"{{__('$Amount').'-'}}", field:"amount2",bottomCalc:"sum"},
		{title:"{{__('Comment')}}", field:"rq1"},
			    ],
    });
	


	
$("#search_data").keyup(function () {
            table.setFilter(matchAny, { value: $("#search_data").val() });
            if ($("#search_data").val() == " "){
              table.clearFilter()
            }
   });
 
//trigger download of data.pdf file
document.getElementById("download-pdf").addEventListener("click", function(){
    table.download("pdf", "accounting.pdf", {
        orientation:"portrait", //set page orientation to portrait
        title:"{{__('Accounting')}}", //add title to report
    });
});	
	
//print button
document.getElementById("print-table").addEventListener("click", function(){
   table.print("active", true, {columnGroups:false});
});	
	
//trigger download of data.csv file
document.getElementById("download-csv").addEventListener("click", function(){
    table.download("csv", "accounting.csv");
});

//trigger download of data.xlsx file
document.getElementById("download-xlsx").addEventListener("click", function(){
    table.download("xlsx", "accounting.xlsx", {sheetName:"{{__('Accounting')}}"});
});


$('#generate').click(function(e){
	e.preventDefault();
	 var filter_from_acc=$('#fselectpro').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_type=$('#stype').val();
		var filter_to_acc=$('#tselectpro').val();
		var filter_currency=$('#fcurrency').val();
		var filter_user=$('#alluser').val();
	table.setData("{{route('inventory.reports.inventory_accounting',app()->getLocale())}}", 
	              {filter_user:filter_user,filter_from_acc:filter_from_acc,filter_to_acc:filter_to_acc,filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_type:filter_type,filter_currency:filter_currency});
	
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