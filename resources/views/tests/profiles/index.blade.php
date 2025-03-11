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
						  <h3 class="text-dark">{{__('Lab Profiles List')}}</h3>
					   </div> 
						<div class="form-group col-md-4">
						  <select  id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0" style="width:100%;">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>	
						
                        <div class="form-group col-md-4">
						  <select  id="filter_name" name="filter_name" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__('Choose a profile')}}</option>
							@foreach($profiles as $p)
							  <option value="{{$p->id}}">{{$p->profile_name}}</option>
							@endforeach
						  </select>
						</div>		
                        <div class="form-group input-group col-md-4">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						  <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_profile"><i class="fa fa-plus"></i> Create</a>
						</div>
                      						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="profiles_dt" class="table table-bordered table-striped data-table nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="all">#</th>
										<th class="all">{{__('Name')}}</th>
										<th>{{__('Codes')}}</th>
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
@include('tests.profiles.profileModal')
@endsection
@section('scripts')
<script>
$(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$('.select2_modal').select2({dropdownParent: $('#profileModal')});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
     });
	 
	 
	var table = $('#profiles_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [['0','desc']],
        info: true,
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
	    responsive:true,
        ajax: {
            url: "{{ route('lab.tests_profiles.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_name=$('#filter_name').val();
                    }
              },
        columns: [
            {data: 'id'},
			{data: 'profile_name'},
			{data: 'tests_names'},
           	{data: 'action', name: 'action', orderable: false, searchable: false},
        ]
		
	});
	
	table.columns.adjust();
	
	$('#create_profile').click(function () {
		var lab_num = $('#filter_lab').val();
      	$('#saveBtn').text("{{__('Save')}}");
        $('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#extLabForm').trigger("reset");
		$('.select2_modal').trigger('change.select2');   
        $('#modelHeading').html("Create lab profile");
        $('#profileModal').modal('show');
    });
	
	$("#filter_status,#filter_lab,#filter_name").change(function() {
	  table.ajax.reload(); 
    });  		
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        var name=$('#extLabForm').find('#profile_name').val();
		if(name==''){
			Swal.fire({text:"Please input a profile name",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var cnt=$('#extLabForm').find('#profile_tests').length;
		if(cnt==0){
			Swal.fire({text:"Please choose at least one code",icon:"error",customClass:"w-auto"});
			return false;
		}
		
			      
        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('lab.tests_profiles.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
               if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  if(data.success){
			  $('#extLabForm').trigger("reset");
              $('#profileModal').modal('hide');
			  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
              $('#filter_name').empty();
			  $('#filter_name').html(data.html);
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
		  html: (checked=='N')?'{{__("Are you sure you want to delete this profile?")}}':'{{__("Are you sure you want to activate this profile?")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('lab.tests_profiles.delete',app()->getLocale()) }}",
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
          url: "{{ route('lab.tests_profiles.get_info',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#profile_name').val(res.data.profile_name);
		  var arr = $.parseJSON(res.data.profile_tests);
		  $('#profile_tests').val(arr).change();
		  $('.select2_modal').trigger('change.select2');
		  $('#modelHeading').html("{{__('Edit lab profile')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#profileModal').modal('show');
          }
      });

    }


</script>
@endsection