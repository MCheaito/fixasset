<!--
    DEV APP
    Created date : 19-3-2023
 -->
<div class="container-fluid">	
			<div class="row  m-1">
			  <div class="col-md-12">
				<input type="hidden" name="pat_id" value="{{isset($doc_patient)?$doc_patient->id:'0'}}"/>
				<input type="hidden" name="pat_clinic_num" value="{{isset($doc_patient)?$doc_patient->clinic_num:'0'}}"/>
				<button data-toggle="modal" data-target="#NewDocModal" class="m-1 float-right btn btn-sm btn-action">{{__("New General document")}}</button>
			  </div>
			</div>  
			<div class="row  m-1">
			  <div class="col-md-12">
					<table id="dash_docs_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th>{{__('Visit Date/Time')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
											
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($medical_documents as $p)
									   <tr>
									     <td><a href="{{route('emr.visit.documents.index',[app()->getLocale(),$p->visit_num])}}" target="_blank" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
										 <td>{{$p->id}}</td>
										 <td>{{isset($p->ProName)?$p->ProName:__("Undefined")}}</td>
										 <td>{{$p->visit_num}}</td>
									     <td>{{$p->visit_date_time}}</td>
									     <td>{{$p->ClinicName}}</td>
										 <td>{{$p->patDetail}}</td>
									     <td>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):'+1 '.$p->Tel }}</td>
										</td>

									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										
@include('patients_list.dashboard.NewDocModal')
<script>
$(document).ready(function(){
			
	
    
	$('#dash_docs_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['4','desc'],['3','desc']],
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
    function new_doc(){
		
		var visit_num = $('#NewDocModal').find('#doc_visit').val();
		//new visit then insert it in database then open new command
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	    $.ajax({
		  url: '{{route("patient.new_doc",app()->getLocale())}}',
		  method:'POST',
		  data: {visit_num:visit_num,patient_num:'{{$doc_patient->id}}',clinic_num:'{{$doc_patient->clinic_num}}'},
          dataType:'JSON',
          success: function(data){
			  $('#NewDocModal').modal('hide');
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

