<!--
   DEV APP
   Created date : 27-3-2023
-->
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
			  <div class="col-md-12">
					<table id="patient_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											
											<th>{{__('Name')}}</th>
											<th>{{__('Email')}}</th>
											<!--<th>{{__('Fax')}}</th>-->
											
											<th>{{__('Actions')}}</th>
										</tr>
									</thead>
									<tbody>
									 
									   <tr>
									    
										 <td>{{$patient->first_name.' '.$patient->last_name}}</td>
										 <td>{{isset($patient->email)?$patient->email:__('Undefined')}}</td>
									     <!--<td>{{isset($patient->fax)?$patient->fax:__('Undefined')}}</td>-->
										 
										 <td>
										   
										   <input type="checkbox"  class="chk_pat_email" name="send_email"   {{$patient->receive_mail && isset($patient->email)?'':'disabled'}} /><label class="ml-1 mr-1 label-size">{{__('Email')}}</label>
                                           <!--<input type="checkbox"  class="chk_pat_fax" name="send_fax"  {{isset($patient->fax)?'':'disabled'}} /><label class="ml-1 mr-1 label-size">{{__('Fax')}}</label>-->
                                           <button type="button"  class="pat_send_mail_fax btn btn-sm btn-action">{{__('Send')}}</button>   
										</td>

									   </tr>
									 
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#patient_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['0','desc']],
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
	
	
    

    $('.pat_send_mail_fax').click(function(e){
		e.preventDefault();
		var is_checked_email = $('.chk_pat_email').is(':checked');
		if(!is_checked_email){
			Swal.fire({text:'{{__("Please choose an option : email")}}',icon:'error',customClass:'w-auto'});
		    return false;
		}
		//var is_checked_fax = $('.chk_pat_fax').is(':checked');
		//if(!is_checked_email && !is_checked_fax){
			//Swal.fire({text:'{{__("Please choose at least one option or both options : email,fax")}}',icon:'error',customCalss:'w-auto'});
		    //return false;
		//}
		var id = '{{$patient->id}}';
		
		var url='';
		var type = $('#code_type').val();
		switch(type){
			case 'documents':
			url = '{{route("emr.visit.documents.send_email_fax",app()->getLocale())}}';
			break;
			case 'visual_field':
			url = '{{route("emr.visit.visual_fields.send_email_fax",app()->getLocale())}}';
			break;
			case 'biomicroscopy':
			url = '{{route("emr.visit.biomicroscopy.send_email_fax",app()->getLocale())}}';
			break;
			case 'other_microscopic_data':
			url = '{{route("emr.visit.other_microscopic_data.send_email_fax",app()->getLocale())}}';
			break;
		}
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
            });
		$.ajax({
				url: url ,
				beforeSend: function() { 
								 Swal.fire({
									title: '',
									html: '{{__("Please wait...")}}',
									timerProgressBar: true,
									  didOpen: () => {
										Swal.showLoading()
									   }
									}); },
				data: {'patient_num':id,
					   'is_checked_email':is_checked_email,
					   
					   'visit_num':$('#ext_visit_id').val(),
					   'type':'patient'
					   },
				type: 'post',
				dataType:'json',
				success: function(data){
					if(data.error){
					Swal.fire({title:data.error,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'error'});
						}
					if(data.success){
					Swal.fire({title:data.success,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'success'});
					
					}
					$('#'+data.modal_name).modal('hide');
				}
			 });
	});	   
	
});


</script>

