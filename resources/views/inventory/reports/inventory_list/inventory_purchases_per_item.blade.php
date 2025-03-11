<!--
   DEV APP
   Created date : 9-5-2023
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
	      <h5>{{__('Purchases per Item Type')}}</h5>
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
			   <label class="label-size">{{__('Supplier')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_supplier" id="filter_supplier" style="width:100%;">
				 <option value="0">{{__('Choose a supplier')}}</option>
				 @foreach($suppliers as $s)
				   <option value="{{$s->id}}">{{$s->name}}</option>
				 @endforeach
			   </select>
			 </div>
			   <div class="col-md-3" hidden >
			   <label class="label-size">{{__('Item Type')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_item" id="filter_item" style="width:100%;">
				 <option value="0">{{__('Choose an item type')}}</option>
				 @foreach($items as $i)
				   <option value="{{$i->id}}">{{__($i->name)}}</option>
				 @endforeach
			   </select>
			 </div>
			  <div class="col-md-3">
			   <label class="label-size">{{__('Item Categry')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_category" id="filter_category" style="width:100%;">
				 <option value="0">{{__('Choose an item Categry')}}</option>
				 @foreach($categorys as $i)
				   <option value="{{$i->id}}">{{__($i->name)}}</option>
				 @endforeach
			   </select>
			 </div>
			  <div class="col-md-3">
			   <label class="label-size">{{__('Item')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_allitem" id="filter_allitem" style="width:100%;">
				 <option value="0">{{__('Choose an item')}}</option>
				 @foreach($allitems as $i)
				   <option value="{{$i->id}}">{{__($i->description)}}</option>
				 @endforeach
			   </select>
			 </div>
			  <div class="col-md-3">
			   <label class="label-size">{{__('Invoice')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_inv_type" id="filter_inv_type" style="width:100%;">
				 <option value="0">{{__('All Invoices')}}</option>
				 <option value="RS">{{__('Return Supplier')}}</option>			
			   </select>
			 </div>
			
			 <div class="col-md-3">
			  <button id="generate" class="mt-4 m-1 btn btn-action">{{__("Generate")}}</button>
			</div>
          </div>
	      
	  </div>
      <div class="card-body">
	    
		<div class="row">
		    <div class="m-1 col-md-5">
                 <button id="print-table" class="btn btn-secondary rounded-pill"><i class="fas fa-print" title="{{__('Print')}}"></i></button>
				 <button id="download-xlsx" class="btn btn-secondary rounded-pill"><i class="fas fa-file-excel" title="{{__('Excel')}}"></i></button>
				 <button id="download-csv" class="btn btn-secondary rounded-pill"><i class="fas fa-file-alt" title="{{__('CSV')}}"></i></button>
				 <button id="download-pdf" class="btn btn-secondary rounded-pill"><i class="fas fa-file-pdf" title="{{__('PDF')}}"></i></button>
            </div>
			<div class="m-1 input-group col-md-6">
			    <label class="col-sm-2 col-form-label" style="font-size:0.8rem;">{{__('Search')}}</label>
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
    ajaxURL:"{{route('inventory.reports.inventory_purchases_per_item',app()->getLocale())}}", //ajax URL
    ajaxParams: function(){
        var filter_supplier=$('#filter_supplier').val();
	    var filter_item=$('#filter_item').val();
		var filter_category=$('#filter_category').val();
		var filter_allitem=$('#filter_allitem').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
	    var filter_inv_type=$('#filter_inv_type').val();
		return {filter_category:filter_category,filter_supplier:filter_supplier, filter_item:filter_item, filter_allitem:filter_allitem,
		        filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_inv_type:filter_inv_type};
           },
	height:"400px",
	placeholder:"{{__('No data is found')}}",
	pagination:true, //enable pagination.
	paginationSize:50,
    paginationSizeSelector:[50,75,100,200,true],
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	groupBy:"item_type",
	 groupHeader: function(value, count, data, group){
        return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
    groupHeaderPrint: function(value, count, data, group){
        return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
	groupClosedShowCalcs:true,
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
        {title:"{{__('Invoice')}}", field:"invoice_num",formatter:function(cell, formatterParams, onRendered){
			var data=cell.getValue();
			var arr = data.split(';');
			if(arr[1] !='' && arr[1]!=null){
				return arr[0]+'<br/>'+'{{__("Ref. Invoice")}}'+' : '+arr[1];
			}else{
				return arr[0];
			}
		}},
		{title:"Date", field:"date_invoice"},
		{title:"{{__('Invoice Supplier')}}", field:"supplier_name_invoice"},
		{title:"{{__('Branch')}}", field:"branch_name"},
		 {title:"{{__('Type')}}", field:"item_type"},
	    {title:"{{__('Name')}}", field:"item_name"},
        {title:"{{__('Supplier')}}", field:"supplier_name"},
		{title:"{{__('$Cost')}}", field:"cost_price",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
	    {title:"{{__('Qty')}}", field:"qty",bottomCalc:"sum",bottomCalcParams: {precision: 2}},
		{title:"{{__('Discount')}}", field:"discount",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},},
		{title:"{{__('Sub-total')}}", field:"subtotal",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
		{title:"{{__('QST')}}", field:"qst",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
        {title:"{{__('GST')}}", field:"gst",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
        {title:"{{__('Total')}}", field:"total",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
		{title:"{{__('Profit')}}", field:"profit",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
	    {title:"{{__('Profit%')}}",field:"profit_percent",formatter:function(cell, formatterParams, onRendered){
			var val=cell.getValue();
			return parseFloat(val*100).toFixed(2)+'%';
		}},
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
    table.download("pdf", "data.pdf", {
        orientation:"landscape", //set page orientation to portrait
        title:"{{__('Purchases per Item Type')}}", //add title to report
    });
});	
	
//print button
document.getElementById("print-table").addEventListener("click", function(){
   table.print("active", true, {columnGroups:false});
});	
	
//trigger download of data.csv file
document.getElementById("download-csv").addEventListener("click", function(){
    table.download("csv", "data.csv");
});

//trigger download of data.xlsx file
document.getElementById("download-xlsx").addEventListener("click", function(){
    table.download("xlsx", "data.xlsx", {sheetName:"{{__('Sales per Item')}}"});
});


$('#generate').click(function(e){
	e.preventDefault();
	var filter_supplier=$('#filter_supplier').val();
	var filter_item=$('#filter_item').val();
	var filter_category=$('#filter_category').val();
	var filter_fromdate=$('#filter_fromdate').val();
	var filter_todate=$('#filter_todate').val();
	var filter_inv_type=$('#filter_inv_type').val();
	table.setData("{{route('inventory.reports.inventory_purchases_per_item',app()->getLocale())}}", 
	              {filter_category:filter_category,filter_supplier:filter_supplier, filter_item:filter_item,
		           filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_inv_type:filter_inv_type});
	
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
  
  $('#filter_supplier').change(function(){
	 var id= $('#filter_allitem').val();
	 //alert($(this).val());
	//if (id=='0'){	 
		 $.ajax({
			   url: '{{route("Filter_cat",app()->getLocale())}}',
			   type: 'get',
			   data: { filter_supplier: $(this).val() },
			   dataType: 'json',
			 success: function(data){
			//  $('#filter_allitem').html(data.html);
			$('#filter_category').html(data.html);
			$('#filter_allitem').html(data.html1);
		}
		});
		});
$('#filter_category').change(function(){
	 var filter_supplier= $('#filter_supplier').val();
	  var id= $('#filter_allitem').val();
	 //alert($(this).val());
	//if (id=='0'){	 
		 $.ajax({
			   url: '{{route("Filter_item",app()->getLocale())}}',
			   type: 'get',
			   data: { filter_category: $(this).val(),filter_supplier:filter_supplier },
			   dataType: 'json',
			 success: function(data){
			//  $('#filter_allitem').html(data.html);
			$('#filter_allitem').html(data.html);
		}
		});		
	//}
	//clearSelect2();
 });
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