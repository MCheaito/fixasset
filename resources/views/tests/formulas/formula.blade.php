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
						  <h3 class="text-dark">{{__('Codes Formulas List')}}</h3>
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
							<option value="">{{__('Choose a code')}}</option>
							@foreach($tests_formulas as $t)
							  <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
						</div>		
                        <div class="form-group input-group col-md-4">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						  <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_formula"><i class="fa fa-plus"></i> Create</a>
						</div>
                      						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="formulas_dt" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="all">#</th>
										<th class="all">{{__('Name')}}</th>
										<th>{{__('Formula')}}</th>
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
@include('tests.formulas.formulaModal')
@endsection
@section('scripts')
<script>
$(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$('.select2_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent: $('#formulaModal')});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
     });
	 //stateSave: true,
	   // stateDuration: -1,
	 
	var table = $('#formulas_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[0,'desc']],
        info: true,
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
	    responsive:true,
        ajax: {
            url: "{{ route('lab.tests_formulas.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_name=$('#filter_name').val();
                    }
              },
        columns: [
            {data: 'id',name:'f.id'},
			{data: 'test_name',name:'t.test_name'},
			{data: 'formula',name:'f.formula'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ]
		
	});
	
	$('#create_formula').click(function () {
		var lab_num = $('#filter_lab').val();
      	$('#saveBtn').text("{{__('Save')}}");
        $('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#extLabForm').trigger("reset");
		$('.select2_modal').trigger('change.select2');   
        $('#modelHeading').html("Create formula");
        $('#formulaModal').modal('show');
    });
	
	$("#filter_status,#filter_lab,#filter_name").change(function() {
	  table.ajax.reload(); 
    });  		
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        var name=$('#extLabForm').find('#test_name').val();
		
		if(name==''){
			Swal.fire({text:"Please input a test to create the formula",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var formula=$('#extLabForm').find('#formula').val();
		
		if(formula==''){
			Swal.fire({text:"Please input a formula",icon:"error",customClass:"w-auto"});
			return false;
		}
		
			      
        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('lab.tests_formulas.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
               if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  if(data.success){
			  $('#extLabForm').trigger("reset");
              $('#formulaModal').modal('hide');
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
		  html: (checked=='N')?'{{__("Are you sure you want to delete the formula for this test code?")}}':'{{__("Are you sure you want to activate the formula for this test code?")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('lab.tests_formulas.delete',app()->getLocale()) }}",
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
          url: "{{ route('lab.tests_formulas.get_info',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#test_id').val(res.data.test_id);
		  $('#formula').val(res.data.formula);
		  $('#unit').val(res.data.unit);
		  
		  $('#test1').val(res.data.test1);
		  $('#test2').val(res.data.test2);
		  $('#test3').val(res.data.test3);
		  $('#test4').val(res.data.test4);
		  
		  $('#factor1').val(res.data.factor1);
		  $('#factor2').val(res.data.factor2);
		  $('#factor3').val(res.data.factor3);
		  $('#factor4').val(res.data.factor4);
		  
		  $('.select2_modal').trigger('change.select2');
		  $('#modelHeading').html("{{__('Edit formula')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#formulaModal').modal('show');
          }
      });

    }


</script>
<script>
function isNumberKey(evt){
  var charCode = (evt.which) ? evt.which : evt.keyCode;
   if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
}
</script>
@endsection