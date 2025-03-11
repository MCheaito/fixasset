<!--
   DEV APP
   Created date : 11-11-2022
-->
@extends('gui.main_gui')
@section('content')
<div class="container-fluid">
   <div class="card">
      @include('reports.menu.report_menu')
      <div class="row m-1">
	    <div class="col-md-10">
			<table id="reports_table" class="display compact nowrap" style="font-size:1em;width:100%;"></table>
		</div>
	  </div>	 
   </div>			
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('#reports_menu').hide();
	
	var dataSet = [
	               ['1','<a  href="{{route("reports.daily_requests",app()->getLocale())}}">{{__("Requests")}}</a>'],
	               ['2','<a  href="{{route("reports.daily_outreach_requests",app()->getLocale())}}">{{__("OUTREACH Requests")}}</a>'],
				   ['3','<a  href="{{route("reports.daily_tests_per_request",app()->getLocale())}}">{{__("Tests per Request")}}</a>'],
				   ['4','<a  href="{{route("reports.daily_invoices",app()->getLocale())}}">{{__("Invoices")}}</a>'],
				   ['5','<a  href="{{route("reports.daily_invoices_per_tests",app()->getLocale())}}">{{__("Invoices per Test")}}</a>'],
				   ['6','<a  href="{{route("reports.daily_invoices_per_payments",app()->getLocale())}}">{{__("Invoices per Pay")}}</a>']
				  ];
	
	$('#reports_table').DataTable({
                    ordering: true,
					paging: true,
					lengthMenu: [[20,30,50,100,-1],[20,30,50,100,'All']],  
                    searching: true,
					ordering: true,
					scrollY: 450,
			        scrollX: true,
			        scrollCollapse:true,
					data: dataSet,
					columns: [
							{ title: '#'},
							{ title: 'Reports'}
							],
			        language :{
					       
							search:         "{{__('Search')}}&nbsp;:",
							lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
							info:          "_START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
							infoEmpty: "0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
							zeroRecords:    "{{__('No data is found')}}",
							emptyTable:     "{{__('No data is found')}}",
							buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "{{__('Show all')}}"
                                                 }
						
								},
							paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				         }
				
				});
});
</script>

@endsection