<div class="container-fluid">	
			
			<div class="row m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
			  <div class="col-md-12">
			        <table id="rx_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Type')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Date Rx')}}</th>
											<th>{{__('Expiration Date')}}</th>
											<th>{{__('Reason')}}</th>
											<th>{{__('Optician')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0; @endphp
									  @foreach($rx as $r)
									   <tr>
									     <td><a onclick="PopupCenter('{{ route('emr.visit.RX.index',[app()->getLocale(),$r->id]) }} ', '{{__('Rx')}}','1000','500')"   class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$r->id}}</td>
										 <td>{{__('Rx').'-'.(isset($r->rx_type)?$r->rx_type:__("Undefined"))}}</td>
										 <td>{{$r->visit_num}}</td>
										 <td>{{isset($r->ProName)?$r->ProName:__("Undefined")}}</td>
										 <td>{{$r->rx_date}}</td>
										 <td>{{$r->rx_expiry_date}}</td>
										 <td>{{isset($r->rx_reason)?$r->rx_reason:__("Undefined")}}</td>
										 <td>{{isset($r->rx_optician)?$r->rx_optician:__("Undefined")}}</td>
										 <td>{{$r->ClinicName}}</td>
										 <td>{{$r->patDetail}}</td>
										 <td>{{($r->Tel=='' || $r->Tel==NULL)?__("Undefined"):'+1 '.$r->Tel }}</td>
									   </tr>
									  @endforeach
									   @foreach($rx_cl as $r)
									   <tr>
									     <td><a onclick="PopupCenter('{{ route('emr.visit.RXCL.index',[app()->getLocale(),$r->id]) }} ', '{{__('CL Rx')}}','1000','500')"   class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$r->id}}</td>
										 <td>{{__('CL Rx').'-'.(isset($r->rx_type)?$r->rx_type:__("Undefined"))}}</td>
										 <td>{{$r->visit_num}}</td>
										 <td>{{isset($r->ProName)?$r->ProName:__("Undefined")}}</td>
										 <td>{{$r->rx_date}}</td>
										 <td>{{$r->rx_expiry_date}}</td>
										 <td>{{isset($r->rx_reason)?$r->rx_reason:__("Undefined")}}</td>
										 <td>{{isset($r->rx_optician)?$r->rx_optician:__("Undefined")}}</td>
										 <td>{{$r->ClinicName}}</td>
										 <td>{{$r->patDetail}}</td>
										 <td>{{($r->Tel=='' || $r->Tel==NULL)?__("Undefined"):'+1 '.$r->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#rx_table').DataTable({
           paging: true,
           searching: true,
           ordering: true,
		   order: [['4','desc'],['1','desc']],
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
