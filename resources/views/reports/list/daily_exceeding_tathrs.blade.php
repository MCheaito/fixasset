<!--
   DEV APP
   Created date : 9-5-2023
-->
@extends('gui.main_gui')
@section('content')
 <div class="container-fluid bg-light">
  @include('reports.menu.report_menu')
   <div class="card  p-1">
      <div class="card-header report-menu">
	     <h5>{{__('Tests exceeding TAT')}}</h5>
		 <div class="row m-1">
			 <div class="col-md-4">
			   <label class="label-size">{{__("Lab")}}</label>
			   <input type="text" class="form-control form-control-border" value="{{$resource->full_name}}" disabled />
			 </div>
			 <div class="col-md-3 col-6">
				<label for="name" class="label-size">{{__('From')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			<div class="col-md-3 col-6">
			    <label for="name" class="label-size">{{__('To')}}</label>
				<input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate"  placeholder="{{__('Choose a date')}}"/>
			</div>
			 
			 <div class="col-md-4">
			   <label class="label-size">{{__('Patient')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_patient" id="filter_patient">
				 <option value="">{{__('Choose a patient')}}</option>
				 @foreach($patients as $p)
				   <option value="{{$p->id}}">{{$p->first_name.' '.(isset($p->middle_name)?$p->middle_name.' ':'').$p->last_name}}</option>
				 @endforeach
			   </select>
			 </div>
			 <div class="col-md-4">
			   <label class="label-size">{{__('Guarantors')}}</label>
			   <select class="select2_data custom-select rounded-0" name="filter_resource" id="filter_resource">
				 @if(auth()->user()->type==2)
					<option value="">{{__('Choose a guarantor')}}</option>
				@endif
				@foreach($ext_labs as $lab)
					<option value="{{$lab->id}}">{{$lab->full_name}}</option>
				@endforeach
			   </select>
			 </div>
		    
			 <div class="col-md-2">
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
			    <label class="col-sm-2 col-form-label" style="font-size:1rem;">{{__('Search')}}</label>
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
    ajaxURL:"{{route('reports.daily_requests_tat',app()->getLocale())}}", //ajax URL
    ajaxParams: function(){
        var filter_patient=$('#filter_patient').val();
		var filter_fromdate=$('#filter_fromdate').val();
		var filter_todate=$('#filter_todate').val();
		var filter_resource=$('#filter_resource').val();
		return {
			    filter_resource:filter_resource,filter_patient:filter_patient,
		        filter_fromdate:filter_fromdate,filter_todate:filter_todate
				};
           },
	height:"450px",
	placeholder:"{{__('No data is found')}}",
	pagination:true, //enable pagination.
	paginationSize:50,
    paginationSizeSelector:[50,75,100,200,true],
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	groupBy:"request_nb",
	groupHeader: function(value, count, data, group){
       
		return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
    groupHeaderPrint: function(value, count, data, group){
       
		return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
	groupClosedShowCalcs:true,
	downloadConfig:{
        columnHeaders:true, //do not include column headers in printed table
        columnGroups:true, //do not include column groups in column headers for printed table
        rowHeaders:true, //do not include row headers in printed table
        rowGroups:true, //do not include row groups in printed table
        columnCalcs:true, //do not include column calcs in printed table
        dataTree:false, //do not include data tree in printed table
        formatCells:true, //show raw cell values without formatter
    },
	printConfig:{
        columnHeaders:true, //do not include column headers in printed table
        columnGroups:true, //do not include column groups in column headers for printed table
        rowHeaders:true, //do not include row headers in printed table
        rowGroups:true, //do not include row groups in printed table
        columnCalcs:true, //do not include column calcs in printed table
        dataTree:false, //do not include data tree in printed table
        formatCells:true, //show raw cell values without formatter
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
        {title:"{{__('Request')}}", field:"request_nb",visible:false},
		{title:"{{__('Code')}}", field:"test_name"},
		{title:"{{__('TAT Date')}}",field:"tatDATE"},
		{title:"{{__('Patient')}}", field:"patDetail"},
		{title:"{{__('Guarantor')}}", field:"ext_lab_name"}
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
    table.download("pdf", "lab_requests.pdf", {
        orientation:"landscape", //set page orientation to portrait
        title:"{{__('Requests')}}", //add title to report
    });
});	
	
//print button
document.getElementById("print-table").addEventListener("click", function(){
   table.print("active", true, {columnGroups:false});
});	
	
//trigger download of data.csv file
document.getElementById("download-csv").addEventListener("click", function(){
    table.download("csv", "lab_requests.csv");
});

//trigger download of data.xlsx file
document.getElementById("download-xlsx").addEventListener("click", function(){
    table.download("xlsx", "lab_requests.xlsx", {sheetName:"{{__('Requests')}}"});
});


$('#generate').click(function(e){
	e.preventDefault();
	var filter_patient=$('#filter_patient').val();
	var filter_fromdate=$('#filter_fromdate').val();
	var filter_todate=$('#filter_todate').val();
	var filter_resource=$('#filter_resource').val();
	var filter_status=$('#filter_status').val();
	
	table.setData("{{route('reports.daily_requests_tat',app()->getLocale())}}", 
	              {
				   filter_resource:filter_resource, filter_patient:filter_patient,
		           filter_fromdate:filter_fromdate,filter_todate:filter_todate
				   });
	
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