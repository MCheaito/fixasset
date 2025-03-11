<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			<div class="row m-1">
				<div class="col-md-12">
					
					<a id="btnsale" class="m-1 float-right btn btn-action btn-sm">{{__('New Sale')}}</a>
				</div>
			</div>
			<div class="row m-1">
			  <div class="col-md-12">
			        <table id="dash_inv_bills_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
									        <th>{{__('Bill')}}</th>
											<th>{{__('Total')}}</th>
										    <th>{{__('Due Balance')}}</th>
											<th>{{__('Paid')}}</th>
											<th>{{__('Patient')}}</th>
									        <th>{{__('Sub-total')}}</th>
									        <th>{{__('QST')}}</th>
									        <th>{{__('GST')}}</th>
									        <th>{{__('Branch')}}</th>      
									        
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($bills as $b)
									   <tr>
									     <td>
										 @switch($b->bill_type)
										   @case (1) <a href="{{ route('inventory.invoices.edit',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a> @break
										   @case (2) <a href="{{ route('inventory.invoices.editsales',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>@break
										   @case (3) <a href="{{ route('inventory.invoices.EditRSales',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>@break
										   @case (4) <a href="{{ route('inventory.invoices.EditRinvoices',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>@break
										   @case (5) <a href="{{ route('inventory.invoices.editadj',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>@break
										   @case (99) <a href="{{ route('inventory.invoices.editsales',[app()->getLocale(),$b->id]) }}" target="_blank"  class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>@break
										 @endswitch
										 </td>
										 <td>{{$b->id}}</td>
									     <td>
										  @if($b->is_warranty=='Y')
										   {{$b->bill_id.','.__('Warranty')}} <br/>{{$b->bill_date}}
										  @else
										   {{$b->bill_id}} <br/>{{$b->bill_date}}
										  @endif
										 </td>									 
										 <td>{{$b->total}}</td>
										 <td>{{$b->sold}}</td>
										 <td>{{$b->total_pay}}</td>
										 <td>{{isset($b->patDetail)?$b->patDetail:__("Undefined")}}</td>
										 <td>{{$b->subtotal}}</td>
										 <td>{{$b->qst}}</td>
										 <td>{{$b->gst}}</td>
										 <td>{{$b->ClinicName}}</td>
									   </tr>
									  @endforeach
									</tbody>
									<tfoot>
					                 <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
					                </tfoot>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	var table=$('#dash_inv_bills_table').DataTable({
           
		   paging: true,
		   lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "{{__('All')}}"]], 
		   searching: true,
           ordering: true,		   
		   order: [['2','desc']],
           scrollY:350,
		   scrollX:true,
		   scrollCollapse: true,
           info: true,
		   language: {
			    search:         "{{__('Search')}}&nbsp;:",
				lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
				info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
				infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
				emptyTable:  "{{__('No data is found')}}",
				zeroRecords: "{{__('No data is found')}}",
				buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "__('All')}}"
                                                 }
						
								},
				paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				},
				 footerCallback: function (row, data, start, end, display) {
						let api = this.api();
				 
						// Remove the formatting to get integer data for summation
						let intVal = function (i) {
							return typeof i === 'string'
								? i.replace(/[\$,]/g, '') * 1
								: typeof i === 'number'
								? i
								: 0;
						};
				 
						api.column(0).footer().innerHTML ='Total :';
						// Total over this page
						pageTotal = api
							.column(3)
							.data()
							.reduce((a, b) => intVal(a) + intVal(b), 0);
				 
						// Update footer
						api.column(3).footer().innerHTML =
							'$' +  parseFloat(pageTotal).toFixed(2);
						// Total over this page
						pageTotal = api
							.column(4)
							.data()
							.reduce((a, b) => intVal(a) + intVal(b), 0);
				 
						// Update footer
						api.column(4).footer().innerHTML =
							'$' + parseFloat(pageTotal).toFixed(2);
                       
					   // Total over this page
						pageTotal = api
							.column(5)
							.data()
							.reduce((a, b) => intVal(a) + intVal(b), 0);
				 
						// Update footer
						api.column(5).footer().innerHTML =
							'$' +  parseFloat(pageTotal).toFixed(2);							
					}
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
