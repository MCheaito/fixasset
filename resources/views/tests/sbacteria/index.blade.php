@extends('gui.main_gui')

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
						  <h3 class="text-dark">Lab Tests S Bacteria</h3>
					   </div> 
						<div class="form-group col-md-4">
						  <select   class="select2_data custom-select rounded-0" style="width:100%;">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>
                        					
						<div class="form-group col-md-2">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						</div>
                        <div class="form-group col-md-6">
					         <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_test"><i class="fa fa-plus"></i> Create</a>
					    </div>						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="antibiotic_dt" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="all">#</th>
										<th>{{__('Order')}}</th>
										<th>{{__('Code')}}</th>
										<th class="all">{{__('Description')}}</th>				
										<th class="all">Actions</th>
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
@include('tests.sbacteria.sbacLABModal')
@endsection
@section('scripts')
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
     });
	 
	
			
	var table = $('#antibiotic_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [['0','desc'],['1','asc']],
        info: true,
	    responsive:true,
		
        ajax: {
            url: "{{ route('lab.tests_sbacteria.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				
                    }
              },
        columns: [
            {data: 'id'},
			{data: 'testord'},
			{data: 'code'},
            {data: 'descrip'},
			
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
		
	});
	
	
	
	$('#create_test').click(function () {
		var lab_num = $('#filter_lab').val();
		   	 
		$('#saveBtn').text("{{__('Save')}}");
        $('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#extLabForm').trigger("reset");
        $('#modelHeading').html("Create test antibiotic");
        $('#sbacLABModal').modal('show');
		
    });
	
	
	$("#filter_status").change(function() {
	  table.ajax.reload(); 
    });  		
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        
		var name = $('#extLabForm').find('#descrip').val();
		//var code = $('#extLabForm').find('#code').val();
		
		/*if(code==''){
			Swal.fire({text:"Please input a code",icon:"error",customClass:"w-auto"});
			return false;
		}*/
		
		if(name==''){
			Swal.fire({text:"Please input a description",icon:"error",customClass:"w-auto"});
			return false;
		}
		
				
        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('lab.tests_sbacteria.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
              if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  
			  if(data.success){
			  $('#extLabForm').trigger("reset");
              $('#sbacLABModal').modal('hide');
			  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
              table.ajax.reload();
			  }
           }

      });

    });
	
	$('body').on('click', '.toggle-chk', function (e) {
        e.preventDefault();
		var id = $(this).data("id");
		var checked = $(this).is(':checked')?'O':'N';
		Swal.fire({
		  title: '{{__("Are you sure?")}}',
		  html:'{{__("Please note, this operation will affect the related groups for this antibiotic")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('lab.tests_sbacteria.delete',app()->getLocale()) }}",
					data:{id:id,checked:checked},
					dataType:"JSON",
					success: function (data) {
						Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
						table.ajax.reload();
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
function editData(id) {
      $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    
	  $.ajax({
          data: {id:id},
          url: "{{ route('lab.tests_sbacteria.get_info',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#lab_num').val(res.data.clinic_num);
		  $('#testord').val(res.data.testord);
		  $('#descrip').val(res.data.descrip);
		  $('#code').val(res.data.code);
		 
		  $('#modelHeading').html("{{__('Edit test antibiotic')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#sbacLABModal').modal('show');
         
		 
		  
          }
      });

    }

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