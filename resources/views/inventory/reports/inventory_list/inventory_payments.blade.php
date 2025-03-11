<!--
   DEV APP
   Created date : 2-2-2023
-->
@extends('gui.main_gui')
@section('content')
 <div class="container-fluid bg-light">
   @include('inventory.reports.menu.report_menu')
   
   <div class="card  p-1">
      <div class="card-header report-menu">
	     <h5>{{__('Sales per Pay')}}</h5>
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
			
			 <div class="col-md-3">
			   <label class="label-size">{{__('Type')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_payment_type" id="filter_payment_type" style="width:100%;">
				 <option value="0">{{__('Choose a payment type')}}</option>
				@foreach($payment_types as $pt)
				  <option value="{{$pt->id}}">{{(app()->getLocale()=='en')?$pt->name_eng:$pt->name_fr}}</option>
				@endforeach  
			   </select>
			 </div>
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
        
		//custom calculation for amount
		var amntCalc = function(values, data, calcParams){
		var calc=0;
		
		 data.forEach(function(arr){
              
			  if(arr["payment_type"]=="Payment")  
				calc+=parseFloat(arr["payment_amount"]);
              else
				calc-=parseFloat(arr["payment_amount"]);
        });

    return parseFloat(calc).toFixed(2);
		
	}
	
	//custom calculation for amount
		var amntFormat = function(values, data, calcParams){
		
		 data.forEach(function(arr){
              
			  if(arr["payment_type"]=="Payment")  
				return arr["payment_amount"];
              else
				return '-'+arr["payment_amount"];
        });

   
	}
	
		var table = new Tabulator("#example-table", {
    ajaxURL:"{{route('inventory.reports.inventory_sales_per_pay',app()->getLocale())}}", //ajax URL
    ajaxParams: function(){
         var filter_patient=$('#filter_patient').val();
		 //var filter_payment=$('#filter_payment').val();
		 var filter_payment_type=$('#filter_payment_type').val();
		 var filter_fromdate=$('#filter_fromdate').val();
		 var filter_todate=$('#filter_todate').val();
		 var filter_due_amount=$('#filter_due_amount').val();
		 var filter_inv_type = $('#filter_inv_type').val();
		return {filter_payment_type:filter_payment_type,filter_patient:filter_patient,
		        filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_due_amount:filter_due_amount,filter_inv_type:filter_inv_type};
           },
	height:"400px",
	placeholder:"{{__('No data is found')}}",
	pagination:true, //enable pagination.
	paginationSize:50,
    paginationSizeSelector:[50,75,100,200,true],
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	groupBy:"payment_name",
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
        "en-us":{ 
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
        {title:"{{__('Invoice')}}",field:"bill_nb",formatter:function(cell, formatterParams, onRendered){
			var data=cell.getValue();
			var arr = data.split(';');
			if(arr[1] !='' && arr[1]!=null){
				return arr[0]+'<br/>'+'{{__("Ref. Invoice")}}'+' : '+arr[1];
			}else{
				return arr[0];
			}
		}},
		{title:"{{__('Payment date')}}", field:"payment_date"},
		{title:"Type", field:"payment_name"},
		{title:"{{__('Pay/Ref')}}", field:"payment_type",formatter:function(cell, formatterParams, onRendered){
				
				var data = cell.getValue();
				if(data=='Payment') { return '{{__("Payment")}}'; }
				else{ 
				 if(data=='Refund') { return '{{__("Refund")}}'; }	
				 else{	return ''; }
				}
        }},
		{title:"{{__('Total after tax')}}", field:"total_pay",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,}},
		{title:"{{__('Paid Amount')}}", field:"payment_amount",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,},bottomCalc:"sum",bottomCalcParams: {precision: 2},bottomCalcFormatter:"money",bottomCalcFormatterParams:{decimal:".",thousand:false,symbol:"$",symbolAfter:"p",precision:2,}},
        {title:"{{__('Due Amount')}}", field:"solde_du",formatter:"money",formatterParams:{decimal:".",symbol:"$",symbolAfter:"p",precision:2,}},
		{title:"{{__('Patient')}}", field:"patName",formatter:function(cell, formatterParams, onRendered){
				var data = cell.getValue();
				if(data !=null && data!='') { 
				return data; 
				}else{ 
				 return ''; 
				}
        }},
		{title:"{{__('Branch')}}", field:"branch_name",formatter:function(cell, formatterParams, onRendered){
				var data = cell.getValue();
				if(data !=null && data!='') { 
				return data; 
				}else{ 
				 return ''; 
				}
        }},
		{title:"{{__('Date of invoice')}}", field:"bill_date"}	
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
				orientation:"landacape", //set page orientation to portrait
				title:"{{__('Sales per Item')}}", //add title to report
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
		//var filter_payment=$('#filter_payment').val();
		var filter_payment_type=$('#filter_payment_type').val();
		var filter_patient=$('#filter_patient').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_due_amount=$('#filter_due_amount').val();
		var filter_inv_type = $('#filter_inv_type').val();
		table.setData("{{route('inventory.reports.inventory_sales_per_pay',app()->getLocale())}}", 
					  {filter_payment_type:filter_payment_type,filter_patient:filter_patient,
					   filter_fromdate:filter_fromdate,filter_todate:filter_todate,filter_due_amount:filter_due_amount,filter_inv_type:filter_inv_type});
		
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