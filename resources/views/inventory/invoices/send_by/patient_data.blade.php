<!--
   DEV APP
   Created date : 26-4-2023
-->
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;">
			  <div class="col-md-12">
					<table id="patient_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Name')}}</th>
											<th>{{__('Email')}}</th>
											<th>{{__('Without description')}}</th>
											<th>{{__('Status')}}</th>				
											<th>{{__('Actions')}}</th>
										</tr>
									</thead>
									<tbody>
									 
									   <tr>
									    
										 <td>{{$patient->first_name.' '.$patient->last_name}}</td>
										 <td>{{isset($patient->email)?$patient->email:__('Undefined')}}</td>								 
										 <td>
										  <div>
										     <label class="mt-2 slideon slideon-xs  slideon-success">
											 <input type="checkbox"  id="desc" name="desc"/>
											 <span class="slideon-slider"></span></label></span>
										  </div>
										 </td>
										 <td>
										 {{isset($status_patient)?$status_patient:''}}
										 </td>	
											
										 <td>
										   
										   <input type="checkbox"  class="chk_pat_email" name="send_email"   {{$patient->receive_mail && isset($patient->email)?'':'disabled'}} /><label class="ml-1 mr-1 label-size">{{__('Email')}}</label>
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
		
		
		var url = '{{route("inventory.invoices.send_email_pat",app()->getLocale())}}';
		//if desc checked means without description
		var desc = $('#desc').is(':checked')?'N':'O';
		
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
				data: {'patient_num':$('#ext_pat_id').val(),
					   'is_checked_email':is_checked_email,
					   'desc':desc,
					   'inv_id':$('#ext_inv_id').val(),
					   },
				type: 'post',
				dataType:'json',
				success: function(data){
					if(data.error){
					Swal.fire({title:data.error,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'error'});
					$('#sendInvoiceModal').modal('hide');	
						}
					if(data.success){
					Swal.fire({title:data.success,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'success'});
					$('#sendInvoiceModal').modal('hide');
					}
				}
			 });
	});	   
	
});


</script>

