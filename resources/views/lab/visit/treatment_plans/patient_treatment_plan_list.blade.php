<!--
   DEV APP
   Created date : 11-3-2023
-->
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
			  <div class="col-md-12">
					<table id="plan_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Actions')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Visit ID')}}</th>
											<th>{{__('Visit Date/Time')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($treatment_plan as $p)
									   <tr>
									     <td>
										     <a   onclick="PopupCenter('{{route('emr.visit.treatment_plans.index',[app()->getLocale(),$p->visit_num])}} ', '{{__('Treatment Plan')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
									         <button onclick="importData({{$p->visit_num}})" class="btn btn-action btn-sm">{{__('Upload')}}<span class="ml-1"><i class="fa fa-upload"></i></span></button>
										</td>
										 <td>{{++$count}}</td>
										 <td>{{isset($p->ProName)?$p->ProName:__("Undefined")}}</td>
										 <td>{{$p->visit_num}}</td>
									     <td>{{$p->visit_date_time}}</td>
									     <td>{{$p->ClinicName}}</td>
										 <td>{{$p->patDetail}}</td>
									     <td>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):$p->Tel }}</td>
										 

									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#plan_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['4','desc'],['3','desc']],
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
<script>
function importData(id){
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
    });
	$.ajax({
			url: '{{route("emr.visit.treatment_plans.import",app()->getLocale())}}',
		    data: {'id':id,
			       'current_visit_num': $('input[name="current_visit_num"]').val()
				   },
			type: 'post',
			dataType:'json',
			success: function(data){
				$('#importTreatmentPlanModal').modal('hide');
				window.location.href=data.location;
			}
	     });
}
</script>
