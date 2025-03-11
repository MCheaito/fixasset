<!--
   DEV APP
   Created date : 11-11-2022
-->
@extends('gui.main_gui')
@section('content')
 <div class="container-fluid">
  <div class="card">
    @include('inventory.reports.menu.report_menu')
    <div class="row m-1">
	   <div class="col-md-10">
		<table id="inventory_reports_table" class="display compact nowrap" style="font-size:1em;width:100%;"></table>
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
	               ['1','<a  href="{{route("inventory.reports.inventory_accounting",app()->getLocale())}}">{{__("Accounting")}}</a>'],
				   ['2','<a  href="{{route("inventory.reports.inventory_orders",app()->getLocale())}}">{{__("Orders")}}</a>'],
				   ['3','<a  href="{{route("inventory.reports.inventory_sales",app()->getLocale())}}">{{__("Sales")}}</a>'],
	               ['4','<a  href="{{route("inventory.reports.inventory_sales_per_item",app()->getLocale())}}">{{__("Sales per Item")}}</a>'],
				   ['5','<a  href="{{route("inventory.reports.inventory_sales_per_supplier",app()->getLocale())}}">{{__("Sales per Supplier")}}</a>'],
				   ['6','<a  href="{{route("inventory.reports.inventory_sales_per_pay",app()->getLocale())}}">{{__("Sales per Pay")}}</a>'],
				   ['7','<a  href="{{route("inventory.reports.inventory_purchases",app()->getLocale())}}">{{__("Purchases")}}</a>'],
				   ['8','<a  href="{{route("inventory.reports.inventory_purchases_per_item",app()->getLocale())}}">{{__("Purchases per Item")}}</a>'],
				   ['9','<a  href="{{route("inventory.reports.inventory_purchases_per_supplier",app()->getLocale())}}">{{__("Purchases per Supplier")}}</a>'],
				   ['10','<a  href="{{route("inventory.reports.inventory_purchases_per_pay",app()->getLocale())}}">{{__("Purchases per Pay")}}</a>'],
				   ['11','<a  href="{{route("inventory.reports.inventory_items_per_qty",app()->getLocale())}}">{{__("Quantities per Item")}}</a>']
				   ];
	
   $('#inventory_reports_table').DataTable({
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