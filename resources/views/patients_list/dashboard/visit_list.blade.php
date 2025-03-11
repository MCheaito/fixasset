<!--
    DEV APP
    Created date : 19-3-2023
	
 -->
<div class="container-fluid">	
			@if(Session::has('waiting_room'))
				@php Session::forget('waiting_room'); @endphp    
			@endif	
			<div class="row  m-1">
			  <div class="col-md-12">
				<input type="hidden" name="pat_id" value="{{isset($visit_patient)?$visit_patient->id:'0'}}"/>
				<input type="hidden" name="pat_clinic_num" value="{{isset($visit_patient)?$visit_patient->clinic_num:'0'}}"/>
				<button data-toggle="modal" data-target="#NewVisitModal" class="m-1 float-right btn btn-sm btn-action">{{__("New Visit")}}</button>
			  </div>
			</div>  
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_visit_table" class="table table-bordered table-striped  data-table nowrap" style="cursor:pointer;width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Last updated')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($visits as $v)
									   <tr>
									     <td><a href="{{ route('emr.visit.edit',[app()->getLocale(),$v->id]) }}" target="_blank" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$v->id}}</td>
										 <td>{{isset($v->ProName)?$v->ProName:__("Undefined")}}</td>
									     <td>{{$v->visit_date_time}}</td>
										 <td>{{$v->last_updated}}</td>
									     <td>{{$v->ClinicName}}</td>
										 <td>{{$v->patDetail}}</td>
									     <td>{{($v->Tel=='' || $v->Tel==NULL)?__("Undefined"):'+1 '.$v->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										
@include('patients_list.dashboard.NewVisitModal')
<script>
$(document).ready(function(){
			
	
    
	$('#dash_visit_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['3','desc'],['4','desc']],
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
    function new_visit(){
		
		var visit_num = $('#NewVisitModal').find('#all_visit').val();
		//new visit then insert it in database then open new command
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	    $.ajax({
		  url: '{{route("patient.new_visit",app()->getLocale())}}',
		  method:'POST',
		  data: {visit_num:visit_num,patient_num:'{{$visit_patient->id}}',clinic_num:'{{$visit_patient->clinic_num}}'},
          dataType:'JSON',
          success: function(data){
			  $('#NewVisitModal').modal('hide');
			  window.location.href=data.location;
		  }		  
		});
		
 		
	}
	
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
