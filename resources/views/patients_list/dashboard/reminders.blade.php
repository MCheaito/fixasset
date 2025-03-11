<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			<div class="row m-1">
				<div class="col-md-12">
					
					<a  class="m-1 float-right btn btn-action btn-sm" onclick="event.preventDefault();new_sch()">{{__('New reminder')}}</a>
				</div>
			</div>
			<div class="row m-1">
			  <div id="reminder_data" class="col-md-12">
			        <table id="dash_reminder_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Actions')}}</th>
											<th>{{__('Branch')}}</th>
									        <th>{{__('Type')}}</th>
											<th>{{__('Date')}}</th>
											<th>{{__('Reminder')}}</th>
											<th>{{__('Authorize Email')}}</th>
											<th>{{__('Authorize SMS')}}</th>
											<th>{{__('Patient')}}</th>
										</tr>
									</thead>
									<tbody>
									   
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										
@include('patients_list.dashboard.reminderModal')
<script>
$(document).ready(function(){
			
	
    
	var table=$('#dash_reminder_table').DataTable({
           serverSide: true,
		   paging: true,
           searching: true,
           ordering: true,		   
		   order: [['3','desc']],
           scrollY:350,
		   scrollX:true,
		   scrollCollapse: true,
           info: true,
		    ajax: {
			 url: "{{ route('patient.get_reminder_data',app()->getLocale())}}",
			 type: 'POST',
			 data: function (d) {
				     d.action_type= 'data_table';
					 d.clinic_num = $('#branch_id').val();
					 d.patient_num = $('#pat_id').val();
					}
			 },
			 columns: [
			    {data: 'action',orderable: false, searchable: false},
				{data:'branch_name'},
				{data:'type'},
				{data:'date_range',render: function(data, type, row) {
					if(data != '' && data != null){ 
						 
					     return data;
			        }else{
						return '{{__("Undefined")}}';
					   }
				}},
				{data:'remind_before',render: function(data, type, row) {
					if(data != '0' && data != null){ 
						 
					     return '{{__("Before")}}'+' '+data+'h';
			        }else{
						return '{{__("Undefined")}}';
					   }
				}},
				{data:'remind_email'},
				{data:'remind_sms'},
				{data:'pat_name'},
			  
			  ],
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
$('#reminderModal').on('hidden.bs.modal',function(){
	$('#remind_form').trigger("reset");
	
});

$('#reminderModal').on('shown.bs.modal',function(){
	   
	   
	
	$('.date_data').flatpickr({
		allowInput: true,
		dateFormat: "Y-m-d"	
		});
	
	$('#remind_type').change(function(){
		var action_type = 'new';
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax({
		  url: '{{route("patient.get_reminder_data",app()->getLocale())}}',
          method: 'post',
          data: { rem_id:$('#rem_id').val(), type: $(this).val(),pat_id:$('#pat_id').val() ,branch_id:$('#branch_id').val(),action_type:action_type},
          dataType:'json',
          success: function(data){
			 if(data.rem !=null){
			  $('#selectSubjEN').val(data.rem.email_head_en);
			  $('#selectMailEN').val(data.rem.email_body_en);
			  $('#selectSmsEN').val(data.rem.sms_body_en);
			  }else{
			  $('#selectSubjEN').val('');
			  $('#selectMailEN').val('');
			  $('#selectSmsEN').val('');  
			  }
			  if(data.date !=null && data.date !=''){
				  $('input[name="dateSpick"]').val(data.date);
			  }else{
				  $('input[name="dateSpick"]').val('');
			  }
		  }		  
		});
	  });
	
	
	
	$('#save_rem').off().on('click',function(e){
	e.preventDefault();
      var sms_size = $('#chksms').is(":checked")? $('#selectSmsEN').val().length:0;
	  var sms_cnt = Math.ceil(sms_size/300);
      var sms_msg = (sms_size==0)?'':'<p><b>'+'{{__("Please note that the SMS units are")}}'+' : '+sms_cnt+'</b></p>';
	  var msg ='<p>'+'{{__("Do you want to save the changes?")}}'+'</p>'+sms_msg; 	  
     Swal.fire({
     html: msg,
     showDenyButton: true,
     confirmButtonText: '{{__("Ok")}}',
     denyButtonText: '{{__("Cancel")}}',
     }).then((result) => {
	  /* Read more about isConfirmed, isDenied below */
	  if (result.isConfirmed) {	      
		  
		  
		  
		  if ($('#chkmail').is(":checked"))
			{
			  if($('#selectSubjEN').val()==""){
				  Swal.fire({text: '{{__("Please write the email title")}}',icon: 'error', customClass : 'w-auto'});
					return false;
			  }

			  if($('#selectMailEN').val()==""){
				  Swal.fire({text: '{{__("Please write the email message content")}}',icon: 'error', customClass : 'w-auto'});
					return false;
			  }  	  
			
			}
			
			if ($('#chksms').is(":checked"))
			{
			 if($('#selectSmsEN').val()==""){
				  Swal.fire({text: '{{__("Please write the sms message content")}}',icon: 'error', customClass : 'w-auto'});
					return false;
			  }  	  
			
			}

            if(!$('#chksms').is(":checked") && !$('#chkmail').is(":checked")){
				Swal.fire({text: '{{__("Please choose at least one option : sms , email")}}',icon: 'error', customClass : 'w-auto'});
					return false;
			}
		
		var remind_before = $('#remind_before').val();
		if(remind_before=='0'){
			Swal.fire({text:'{{__("Please choose a reminder alert")}}',icon:'error',customClass:'w-auto'});
			return false;
		}
       var start_date = $('input[name="dateSpick"]').val();
							 
							 if(start_date==""){
								Swal.fire({text: '{{__("Please choose a valid start date")}}',icon: 'error', customClass : 'w-auto'});
								return false;  
							  }
							  
							
   
      $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax({
		  url: '{{route("patient.save_reminder_data",app()->getLocale())}}',
          method: 'post',
          data: $('#remind_form').serialize(),
          dataType:'json',
          success: function(data){
			  $('#reminderModal').modal('hide');
			  $('#dash_reminder_table').DataTable().ajax.reload();
			  Swal.fire({toast:true,title:data.msg,icon:'success',timer:3000,position:'bottom-right',showConfirmButton:false});			 
		  }		  
		});
      }else if (result.isDenied) {
		return false;
	  }	
	 })	  
   
   });
	

});
</script>
<script>
    function new_sch(){
		$('#rem_id').val('0');
		$('#remind_type').prop('disabled',false);
		
		var type = $('#remind_type').val();
		
			$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
			$.ajax({
			  url: '{{route("patient.get_reminder_data",app()->getLocale())}}',
			  method: 'post',
			  data: { rem_id:$('#rem_id').val() ,type: type,pat_id:$('#pat_id').val(),branch_id: $('#branch_id').val(),'action_type':'new'},
			  dataType:'json',
			  success: function(data){
				  $('#remind_form').trigger("reset");
				  if(data.rem !=null){
				  $('#selectSubjEN').val(data.rem.email_head_en);
				  $('#selectMailEN').val(data.rem.email_body_en);
				  $('#selectSmsEN').val(data.rem.sms_body_en);
				  }
				  if(data.date !=null && data.date !=''){
				  $('input[name="dateSpick"]').val(data.date);
				  }else{
					  $('input[name="dateSpick"]').val('');
				  }
				  
		          $('#reminderModalLabel').text('{{__("New reminder")}}');
		          $('#save_rem').text('{{__("Save")}}');
				  $('#reminderModal').modal('show');
			  }		  
			});
		
	}
	
	function edit_sch(id){
		$('#rem_id').val(id);
		
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		$.ajax({
		  url: '{{route("patient.get_reminder_data",app()->getLocale())}}',
          method: 'post',
          data: { rem_id:id ,'action_type':'edit'},
          dataType:'json',
          success: function(data){
			  
			  $('#reminderModalLabel').text('{{__("Edit reminder")}}');
			  $('#remind_type').val(data.rem.type);
			  $('#remind_type').prop('disabled',true);
			  if(data.rem.remind_by_email=='O'){
			   $('input[name="remind_by_email"]').prop("checked",true);
			  }else{
			   $('input[name="remind_by_email"]').prop("checked",false);  
			  }
			  
			  if(data.rem.remind_by_sms=='O'){
			   $('input[name="remind_by_sms"]').prop("checked",true);
			  }else{
			   $('input[name="remind_by_sms"]').prop("checked",false);  
			  }
			  $('#remind_before').val(data.rem.remind_before);
			  $('input[name="dateSpick"]').val(data.rem.start);
			  $('#selectSubjEN').val(data.rem.email_head_en);
			  $('#selectMailEN').val(data.rem.email_body_en);
			  $('#selectSmsEN').val(data.rem.sms_body_en);
			  $('#save_rem').text('{{__("Update")}}');
			  $('#reminderModal').modal('show');
		  }		  
		});
	}
	
	function destroy_sch(id){
		Swal.fire({
	       text: '{{__("Are you sure?")}}',
           showCancelButton: true,
		   confirmButtonText: '{{__("Yes")}}',
           cancelButtonText: '{{__("No")}}',
		   customClass: 'w-auto',
        
      }).then((result) =>  {
         if (result.isConfirmed) {
			$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
			$.ajax({
			  url: '{{route("patient.get_reminder_data",app()->getLocale())}}',
			  method: 'post',
			  data: { rem_id:id ,'action_type':'delete'},
			  dataType:'json',
			  success: function(data){
			    $('#dash_reminder_table').DataTable().ajax.reload();
				Swal.fire({toast:true,title:data.msg,icon:'success',timer:3000,position:'bottom-right',showConfirmButton:false});			
			  }
			});
	      }
	     })
}


</script>
