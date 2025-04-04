<!--
    DEV APP
    Created date : 19-3-2023
 -->
 @php
   $access_gf_order = UserHelper::can_access(auth()->user(),'gf_orders');
   $access_cl_order = UserHelper::can_access(auth()->user(),'cl_orders');
 @endphp  
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
			  <div class="col-md-12">
					<table id="dash_cmd_table" class="table table-bordered table-striped  data-table nowrap" style="cursor:pointer;width:100%;">
									<thead>
										<tr>
										    <th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
										    <th>{{__('Type')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Invoice Nb')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th class="my_delivery">{{__('Delivery')}}</th>
											<th>{{__('Supplier')}}</th>
											<th>{{__('Descriptions')}}</th>
											<th>{{__('Total')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Tel')}}</th>
											
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($commands as $p)
									   <tr>
									      <td>
										   @if(isset($p->cmd_cl) && $p->cmd_cl=='Y')
										    <a onclick="PopupCenter('{{route('emr.visit.command_cl.index',[app()->getLocale(),$p->id])}} ', '{{__('Contact Lens Order')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
 									      @else 
										    <a onclick="PopupCenter('{{route('emr.visit.command.index',[app()->getLocale(),$p->id])}} ', '{{__('Glass Frame Order')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
										   @endif
										  </td>
										 <td>
										  @if($p->active=='O') 
										   {{$p->id}}
										 @else
										   {{$p->id}}<br/>{{__('Cancelled')}}	 
										 @endif	 
										 </td>
										 <td>{{isset($p->cmd_cl) && $p->cmd_cl=='Y'?__('Contact Lens'):__('Glass Frame')}}</td>
									     <td>{{$p->datecmd}}</td>
										 <td>{{isset($p->clinic_inv_num)?$p->clinic_inv_num:__("Undefined")}}</td>
										 <td>{{$p->visit_num}}</td>
										  <td>
											 @if($p->livrer=='Y')
											   {{__("Delivered by").' : '.$p->livrer_user}}<br/>{{__('Date').' : '.Carbon\Carbon::parse($p->livrer_date)->format('Y-m-d H:i')}}
											@else
											  {{__("Not delivered")}}
											@endif
										 </td>
										 <td>{{isset($p->supplier_name)?$p->supplier_name:__("Undefined")}}</td>
									     <td>
										 {{$p->descriptions}}
										 </td>
										 <td>{{$p->total}}</td>
										 <td>{{$p->branch_name}}</td>
										 <td>{{$p->patDetail}}</td>
									     <td>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):'+1 '.$p->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#dash_cmd_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['2','desc']],
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
												   '-1': "__('Show all')}}"
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

