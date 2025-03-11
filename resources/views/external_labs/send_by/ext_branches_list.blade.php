<!--
   DEV APP
   Created date : 24-3-2023
-->
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
			  <div class="col-md-12">
					<table id="external_branches_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('#')}}</th>
											<th>{{__('Name')}}</th>
											<th>{{__('Type')}}</th>
											<th>{{__('Email')}}</th>
											<th>{{__('Fax')}}</th>
											<th>{{__('Actions')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($external_branches as $ext)
									   <tr>
									     <td>{{++$count}}</td>
										 <td>{{$ext->full_name}}</td>
										 <td>
										  @php
										   $type=__('Undefined');
											switch($ext->kind){
												case 'clinic': $type=__('Clinic'); break;
												case 'pharm': $type=__('Pharmacy'); break;
											}
										   @endphp
										   {{$type}}
										 </td>
									     <td>{{isset($ext->email)?$ext->email:__('Undefined')}}</td>
									     <td>{{isset($ext->fax)?'+1 '.$ext->fax:__('Undefined')}}</td>
										 <td>
										   
										   <input type="checkbox"  class="chk_email" name="send_email" value="{{$ext->id}}"  {{isset($ext->email)?'':'disabled'}} /><label class="ml-1 mr-1 label-size">{{__('Email')}}</label>
                                           <input type="checkbox"  class="chk_fax" name="send_fax" value="{{$ext->id}}" {{isset($ext->fax)?'':'disabled'}} /><label class="ml-1 mr-1 label-size">{{__('Fax')}}</label>
                                           <button type="button"  class="send_mail_fax btn btn-sm btn-action">{{__('Send')}}</button>   
										</td>

									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	$('#external_branches_table').DataTable({
           
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
	
	$('.chk_email').click(function () {
          $('.chk_email').not(this).prop('checked',false);
		  var chk_mail_val = $(this).val();
		  $('.chk_fax:checkbox:checked').each(function(){
			   var value = $(this).val();
			   if(value != chk_mail_val ){
			     $(this).prop('checked',false); 
		        }	  
		  });
          
       });
	   
	$('.chk_fax').click(function () {

          $('.chk_fax').not(this).prop('checked',false);
		  
		  var chk_fax_val = $(this).val();
		  $('.chk_email:checkbox:checked').each(function(){
			   var value = $(this).val();
			   if(value != chk_fax_val ){
			     $(this).prop('checked',false); 
		        }	  
		  });
	   });


    $('.send_mail_fax').click(function(e){
		e.preventDefault();
		const tr = $(this).closest('tr');
		var is_checked_email = tr.find('.chk_email').is(':checked');
		var is_checked_fax = tr.find('.chk_fax').is(':checked');
		if(!is_checked_email && !is_checked_fax){
			Swal.fire({text:'{{__("Please choose at least one option or both options : email,fax")}}',icon:'error',customClass:'w-auto'});
		    return false;
		}
		var id = (is_checked_email)?tr.find('.chk_email').val():tr.find('.chk_fax').val();
		var url='';
		var type = $('#code_type').val();
		
		switch(type){
			case 'documents':
			url = '{{route("emr.visit.documents.send_email_fax",app()->getLocale())}}';
			break;
			case 'treatment_plans':
			url = '{{route("emr.visit.treatment_plans.send_email_fax",app()->getLocale())}}';
			break;
			
			case 'history':
			url = '{{route("emr.visit.history.send_email_fax",app()->getLocale())}}';
			break;
			case 'visual_field':
			url = '{{route("emr.visit.visual_fields.send_email_fax",app()->getLocale())}}';
			break;
			case 'biomicroscopy':
			url = '{{route("emr.visit.biomicroscopy.send_email_fax",app()->getLocale())}}';
			break;
			
		}
		
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
            });
		$.ajax({
				url: url,
				beforeSend: function() { 
								 Swal.fire({
									title: '',
									html: '{{__("Please wait...")}}',
									timerProgressBar: true,
									  didOpen: () => {
										Swal.showLoading()
									   }
									}); },
				data: {'ext_branch_id':id,
					   'is_checked_email':is_checked_email,
					   'is_checked_fax':is_checked_fax,
					   'visit_num':$('#ext_visit_id').val(),
					   'type':'ext_branch'
					   },
				type: 'post',
				dataType:'json',
				success: function(data){
					if(data.error){
					Swal.fire({title:data.error,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'error'});
						}
					if(data.success){
					Swal.fire({title:data.success,toast:true,showConfirmButton:false,timer:3000,position:'bottom-right',icon:'success'});
					$('#'+data.modal_name).modal('hide');
					}
				}
			 });
	});	   
	
});


</script>

