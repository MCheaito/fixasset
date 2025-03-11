<!-- 
 DEV APP
 Created date : 23-3-2023
-->
@extends('gui.main_gui')
@section('styles')
<style>
.modal-header {
	     cursor: move;
        }
.modal-content{
    -webkit-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -moz-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -o-box-shadow: 0 5px 15px rgba(0,0,0,0);
    box-shadow: 0 5px 15px rgba(0,0,0,0);
     }		

</style>
@endsection
@section('content')
<div class="container-fluid">
      <div class="card card-outline">
			      <div class="card-header card-menu">
					<div class="card-tools">
									<button type="button" class="btn btn-resize btn-sm" title="{{__('Show/Collapse')}}" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
								    </button>
				     </div>
			       					
					<div class="row">
						<div class="col-md-12">
						  <h3 class="text-dark">Guarantors</h3>
					   </div> 
						<div class="col-md-4">
						  <select  id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>
						<div class="col-md-4">
						  <select  id="filter_cat" name="filter_cat" class="custom-select rounded-0">
							<option value="">{{__('All categories')}}</option>
							@foreach($cats as $c)
							<option value="{{$c->id}}">{{app()->getLocale()=='fr'?$c->name_fr:$c->name_en}}</option>
							@endforeach
						  </select>
						</div>	
						<div class="col-md-2">
						  <select  id="filter_status" name="filter_status" class="form-control">
							<option value="A">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						</div>
						<div class="col-md-2">
						  <select  id="filter_toCheck" name="filter_toCheck" class="form-control">
							<option value="">{{__('to Check?')}}</option>
							<option value="Y">{{__('Yes')}}</option>
							<option value="N">{{__('No')}}</option>
						  </select>
						</div>	
					</div>
                  </div>
			   
			  
			   <div class="card-body p-0">
			       	
				  
						    <div class="container-fluid">    
								<div class="m-1 row">
									  
									  <div class="col-md-12">
									    <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_external">Create</a>
									  </div> 
								</div> 
								<div class="m-1 row">
								    <div class="col-md-12">
										<table  id="dataTable_extlab" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
											<thead>
												<tr>
												 <th>#</th>
												 <th>{{__('ID')}}</th>
												 <th>{{__('Category')}}</th>
												 <th>{{__('Complete Name')}}</th>
												 <th>{{__('Rate')}}</th>
												 <th>{{__('Price$')}}</th>
							                     <th>{{__('PriceLBP')}}</th>
												 <th>{{__('to Check?')}}</th>
												 <th>{{__('Code')}}</th>
												 <th>{{__('Email')}}</th>
												 <th>{{__('Alternate Email1')}}</th>
												 <th>{{__('Alternate Email2')}}</th>
												 <th>{{__('Fax Nb')}}</th>
												 <th>{{__('Contact Phone')}}</th>
												 <th>{{__('Alternate Phone1')}}</th>
												 <th>{{__('Alternate Phone2')}}</th>
												 <th>{{__('Address')}}</th>
												 <th>Actions</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div> 
								</div>	
							</div>  
						</div>
						
						  
				   </div>
			 </div>
     </div>			   
	
</div>
@include('external_labs.externalLABModal')
@include('external_labs.usersModal')
@endsection
@section('scripts')
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(document).ready(function() {
	$('.mail').inputmask("email");
   
   var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
    $('.phone').inputmask({mask: phones,greedy: false,definitions: { '#': { validator: "[0-9]", cardinality: 1}}});
	
	/*Pass Header Token*/ 

    $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
	
	var table = $('#dataTable_extlab').DataTable({
      	stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[1,'desc']],
        info: true,
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
	    scrollY:450,
	    scrollX:true,
	    scrollCollapse: true,
        ajax: {
            url: "{{ route('external_labs.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_cat=$('#filter_cat').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_toCheck=$('#filter_toCheck').val();
                    }
              },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
			{data:'id',visible:false},
			{data: 'category_name'},
			{data: 'full_name'},
			{data: 'rate'},
			{data: 'priced'},
			{data: 'pricel'},
			{data:'is_valid'},
			{data: 'code'},
			{data: 'email'},
			{data: 'email2'},
			{data: 'email3'},
			{data: 'fax'},
			{data: 'telephone'},
			{data: 'alternate_phone1'},
			{data: 'alternate_phone2'},
			{data: 'full_address'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
					paginate: {
									first:      "{{__('First')}}",
									previous:   "{{__('Previous')}}",
									next:       "{{__('Next')}}",
									last:       "{{__('Last')}}"
								}
					},
		fixedColumns:   {
				left : 0,
				right: 1
				
				}
    });
    
    
	
	
	
    $('#create_external').click(function () {
		var lab_num = $('#filter_lab').val();
      	$('#saveBtn').text("{{__('Save')}}");
        $('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#extLabForm').trigger("reset");
        $('#modelHeading').html("Create a guarantor");
        $('#externalLABModal').modal('show');
    });
	

   
    $("#filter_status,#filter_lab,#filter_cat,#filter_toCheck").change(function() {
	  table.ajax.reload(null, false); 
    });  		
    	
	
	
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        var name=$('#extLabForm').find('input[name="full_name"]').val();
		if(name==''){
			Swal.fire({text:"Please input a name",icon:"error",customClass:"w-auto"});
			return false;
		}
		      
		
		/*var code=$('#extLabForm').find('input[name="code"]').val();
		if(code==''){
			Swal.fire({text:"Please input a code",icon:"error",customClass:"w-auto"});
			return false;
		}*/
		    
			
		var email=$('#extLabForm').find('input[name="email"]').val(); 
		
		/*if(email==''){
			Swal.fire({text:"{{__('Please input an email')}}",icon:"error",customClass:"w-auto"});
			return false;
		}*/
		

        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('external_labs.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  
			  if(data.success){
			  $('#extLabForm').trigger("reset");
              $('#externalLABModal').modal('hide');
			  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
              table.ajax.reload(null, false);
			  }
           }

      });

    });
	
	
	$('body').on('click', '.toggle-chk', function (e) {
        e.preventDefault();
		var id = $(this).data("id");
		var checked = $(this).is(':checked')?'O':'N';
		
        $.ajax({

            type: "DELETE",
            url: "{{ route('external_labs.delete',app()->getLocale()) }}",
            data:{id:id,checked:checked},
			dataType:"JSON",
            success: function (data) {
                Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
				table.ajax.reload(null, false); 
            }
           });

    });
	
	
	

  

  });

</script>
<script>
function editData(id) {
      $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    
	  $.ajax({
          data: {id:id},
          url: "{{ route('external_labs.get',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#lab_num').val(res.data.clinic_num);
		  $('#category').val(res.data.category);
		  $('#priced').val(res.data.priced);
		  $('#pricel').val(res.data.pricel);
		  $('#pricee').val(res.data.pricee);
		  $('input[name="code"]').val(res.data.code);
		  $('#rate').val(res.data.rate);
		  //$('input[name="percentage"]').val(res.data.percentage);
		  $('input[name="full_name"]').val(res.data.full_name);
		  //$('input[name="region_name"]').val(res.data.region_name);
		  $('input[name="full_address"]').val(res.data.full_address);
		  //$('input[name="appt_nb"]').val(res.data.appt_nb);
		  //$('input[name="city"]').val(res.data.city);
		  //$('input[name="state"]').val(res.data.state);
		  //$('input[name="zip_code"]').val(res.data.zip_code);
		  $('input[name="telephone"]').val(res.data.telephone);
		  $('input[name="alternate_phone1"]').val(res.data.alternate_phone1);
		  $('input[name="alternate_phone2"]').val(res.data.alternate_phone2);
		  $('input[name="fax"]').val(res.data.fax);
		  $('input[name="email"]').val(res.data.email);
		  if(res.data.is_valid=='Y'){
			 $('#is_valid').prop('checked',true); 
		  }else{
			 $('#is_valid').prop('checked',false); 
		  }
		  $('#remarks').val(res.data.remarks);
		  $('#modelHeading').html("{{__('Edit a guarantor')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#externalLABModal').modal('show');
         
		 
		  
          }
      });

    }
	

function deleteUser(id){
	Swal.fire({
        title: 'Are you sure ?',
        html: "<b>{{__('Note')}}:</b>&nbsp;{{__('The user account for this guarantor will be removed!')}}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{__("Yes, remove it!")}}',
        cancelButtonText: '{{__("Cancel")}}'
    }).then((result) => {
        if (result.isConfirmed) {
           $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		   $.ajax({
             data: {id:id,type:'deleteUser'},
             url: "{{ route('external_labs.getUserInfo',app()->getLocale()) }}",
		     type: "POST",
             dataType: 'json',
            success: function (data) {
				if(data.status=='success'){
					  $('#usersForm').trigger("reset");
                      $('#usersModal').modal('hide');
			          Swal.fire({html:data.msg,toast:true,icon:'success',showConfirmButton: false,timer:3000,position:'bottom-right'});
                      $('#dataTable_extlab').DataTable().ajax.reload(null, false);
				}else{
					 Swal.fire({icon:'error',text:data.msg,customClass:'w-auto'});
					 return;
				}
			   },
			 error: function(xhr, status, error) {
			   var msg = 'Errror! There was a problem in deleting this account.';
			   Swal.fire({icon:'error',text:msg,customClass:'w-auto'});
               }  
		   });
        }
    });
}




function editUser(id){
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $.ajax({
          data: {id:id,type:'editUser'},
          url: "{{ route('external_labs.getUserInfo',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (data) {
		  $('#usersModal').find('#guarantor_id').val(id);
		  $('#usersModal').find('#guarantor_lab_num').val(data.clinic_num);
		  $('#usersModal').find('#guarantor_name').val(data.name);
		  $('#usersModal').find('#guarantor_fname').val(data.fname);
		  $('#usersModal').find('#guarantor_lname').val(data.lname);
		  $('#usersModal').find('#guarantor_email').val(data.email);
		  $('#usersModal').find('#guarantor_username').val(data.user_name);
		  $('#usersModal').find('#modelHeading').text(data.header);
		  $('#usersModal').find('#saveBtn').text(data.button);
          $('#usersModal').find('#guarantor_username').prop('disabled',true);  
		  $('#usersModal').modal('show');
          },
		  error: function(xhr, status, error) {
			   var msg = 'Errror! There was a problem in editing this account.';
			   Swal.fire({icon:'error',text:msg,customClass:'w-auto'});
               }  
      });

}

function createUser(id){
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $.ajax({
          data: {id:id,type:'createUser'},
          url: "{{ route('external_labs.getUserInfo',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (data) {
		  $('#usersModal').find('#guarantor_id').val(id);
		  $('#usersModal').find('#guarantor_lab_num').val(data.clinic_num);
		  $('#usersModal').find('#guarantor_name').val(data.name);
		  $('#usersModal').find('#guarantor_fname').val(data.fname);
		  $('#usersModal').find('#guarantor_lname').val(data.lname);
		  $('#usersModal').find('#guarantor_email').val(data.email);
		  $('#usersModal').find('#guarantor_username').val(data.user_name);
		  $('#usersModal').find('#modelHeading').text(data.header);
		  $('#usersModal').find('#saveBtn').text(data.button);
          $('#usersModal').find('#guarantor_username').prop('disabled',false); 
		  $('#usersModal').modal('show');
          },
		  error: function(xhr, status, error) {
			   var msg = 'Errror! There was a problem in creating this account.';
			   Swal.fire({icon:'error',text:msg,customClass:'w-auto'});
               }  
      });

}

function resendActivationEmail(id){
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $.ajax({
          data: {id:id,type:'emailUser'},
          url: "{{ route('external_labs.getUserInfo',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (data) {
		  	 Swal.fire({html:data.msg,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});	  
          },
		  error: function(xhr, status, error) {
			   var msg = 'Errror! There was a problem in resending the activation email for this account.';
			   Swal.fire({icon:'error',text:msg,customClass:'w-auto'});
               }  
      });
	
}

function saveUserInfo(){
  var id = $('#usersModal').find('#guarantor_id').val();
  var lab_num = $('#usersModal').find('#guarantor_lab_num').val();
  
  var fname= $('#usersModal').find('#guarantor_fname').val();
   if(fname==''){
	   Swal.fire({text:'{{__("Please enter a first name")}}',icon:'error',customClass:'w-auto'});
	   return;
   }
 
 var lname= $('#usersModal').find('#guarantor_lname').val();
   if(lname==''){
	   Swal.fire({text:'{{__("Please enter a last name")}}',icon:'error',customClass:'w-auto'});
	   return;
   }     

 var email= $('#usersModal').find('#guarantor_email').val();
   if(email==''){
	   Swal.fire({text:'{{__("Please enter an email")}}',icon:'error',customClass:'w-auto'});
	   return;
   }else{
	    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
		if(!emailPattern.test(email)){
			Swal.fire({text:'{{__("Invalid email. Please enter a valid email address.")}}',icon:'error',customClass:'w-auto'});
	        return;
		}
   }
   
   var username= $('#usersModal').find('#guarantor_username').val();
   if(username==''){
	   Swal.fire({text:'{{__("Please enter a user name")}}',icon:'error',customClass:'w-auto'});
	   return;
   }

  

  var chked_menus = $('#usersForm').find('.menu:checked');
  var menus = [];
  chked_menus.each(function(){
	  menus.push($(this).val());
  });

  $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    $.ajax({
          data: {id:id,lab_num:lab_num,fname:fname,lname:lname,email:email,username:username,menus:menus},
          url: "{{ route('external_labs.saveUserInfo',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (data) {
		      $('#usersForm').trigger("reset");
              $('#usersModal').modal('hide');
			  Swal.fire({html:data.msg,toast:true,icon:'success',showConfirmButton: false,timer:3000,position:'bottom-right'});
              $('#dataTable_extlab').DataTable().ajax.reload(null, false);
          }
      });   
      

}	
</script>
<script>
function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
</script>

@endsection