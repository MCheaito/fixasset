<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_rdv_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('#')}}</th>
											<th>{{__('Exam')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Status')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($rdvs as $rdv)
									   <tr>
									     <td>{{++$count}}</td>
										 <td>{{$rdv->exam_name}}</td>
										 <td>{{isset($rdv->ProName)?$rdv->ProName:__("Undefined")}}</td>
									     <td>{{$rdv->rdv_date.' '.$rdv->rdv_time}}</td>
										 <td>{{$rdv->rdv_state}}</td>
										 <td>{{$rdv->ClinicName}}</td>
										 <td>{{$rdv->patName}}</td>
									     <td>{{($rdv->Tel=='' || $rdv->Tel==NULL)?__("Undefined"):'+1 '.$rdv->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#dash_rdv_table').DataTable({
           paging: true,
           searching: true,
           ordering: true,
		   order: [['3','desc']],
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
