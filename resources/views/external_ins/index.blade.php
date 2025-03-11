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
						<div class="col-md-4">
						  <h3 class="text-dark">Referred Labs</h3>
					   </div> 
						<div class="col-md-5">
						  <select  id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>	
						<div class="col-md-3">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
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
										<table  id="dataTable_extins" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
											<thead>
												<tr>
												 <th>#</th>
												 <th>{{__('ID')}}</th>
												 <th>{{__('Complete Name')}}</th>
												 <th>{{__('Code')}}</th>
												 <th>{{__('Rate')}}</th>
												 <th>{{__('Price$')}}</th>
							                     <th>{{__('PriceLBP')}}</th>
												 <th>{{__('Email')}}</th>
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
@include('external_ins.externalINSModal')
@endsection
@section('scripts')
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>

$(function () {
	$('.mail').inputmask("email");
   
   var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
    $('.phone').inputmask({mask: phones,greedy: false,definitions: { '#': { validator: "[0-9]", cardinality: 1}}});
	
	/*Pass Header Token*/ 

    $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
	
	var table = $('#dataTable_extins').DataTable({
      	stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[1,'desc']],
        info: true,
		scrollY:450,
	    scrollX:true,
	    scrollCollapse: true,
		 pageLength: 50,
		  lengthMenu: [
           [50, 75, 100, -1],
           [50, 75, 100, 'All']
            ],
        ajax: {
            url: "{{ route('external_insurance.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
                    }
              },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
			{data: 'id',visible:false},
			{data: 'full_name'},
			{data: 'code'},
			{data: 'rate'},
			{data: 'priced'},
			
			{data: 'pricel'},
            {data: 'email'},
			{data: 'fax'},
			{data: 'telephone'},
			{data: 'alternative_phone1'},
			{data: 'alternative_phone2'},
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
        $('#extInsForm').find('#id').val('0');
		$('#extInsForm').find('#lab_num').val(lab_num);
		$('#extInsForm').trigger("reset");
        $('#modelHeading').html("Create a referred lab");
        $('#externalINSModal').modal('show');
    });
	

   
    $("#filter_status,#filter_lab").change(function() {
	  table.ajax.reload(null, false); 
    });  		
    	
	
	
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        var name=$('#extInsForm').find('input[name="full_name"]').val();
		if(name==''){
			Swal.fire({text:"Please input a name",icon:"error",customClass:"w-auto"});
			return false;
		}
		
      /*var code=$('#extInsForm').find('input[name="code"]').val();
		if(code==''){
			Swal.fire({text:"Please input a code",icon:"error",customClass:"w-auto"});
			return false;
		}		
		
		
		var email=$('#extInsForm').find('input[name="email"]').val(); 
		
		if(email==''){
			Swal.fire({text:"{{__('Please input an email')}}",icon:"error",customClass:"w-auto"});
			return false;
		}*/
		

        $.ajax({

          data: $('#extInsForm').serialize(),
          url: "{{ route('external_insurance.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  
			  if(data.success){
			  $('#extInsForm').trigger("reset");
              $('#externalINSModal').modal('hide');
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
            url: "{{ route('external_insurance.delete',app()->getLocale()) }}",
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
          url: "{{ route('external_insurance.get',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#lab_num').val(res.data.clinic_num);
		  $('#priced').val(res.data.priced);
		  $('#pricel').val(res.data.pricel);
		  $('#pricee').val(res.data.pricee);
		  $('#rate').val(res.data.rate);
		  $('input[name="code"]').val(res.data.code);
		  //$('input[name="percentage"]').val(res.data.percentage);
		  $('input[name="full_name"]').val(res.data.full_name);
		  //$('input[name="region_name"]').val(res.data.region_name);
		  $('input[name="full_address"]').val(res.data.full_address);
		  //$('input[name="appt_nb"]').val(res.data.appt_nb);
		  //$('input[name="city"]').val(res.data.city);
		  //$('input[name="state"]').val(res.data.state);
		  //$('input[name="zip_code"]').val(res.data.zip_code);
		  $('input[name="telephone"]').val(res.data.telephone);
		  $('input[name="alternative_phone1"]').val(res.data.alternative_phone1);
		  $('input[name="alternative_phone2"]').val(res.data.alternative_phone2);
		  $('input[name="fax"]').val(res.data.fax);
		  $('input[name="email"]').val(res.data.email);
		  $('#remarks').val(res.data.remarks);
		  $('#modelHeading').html("{{__('Edit a referred lab')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#externalINSModal').modal('show');
         
		 
		  
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