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
						  <h3 class="text-dark">{{__('Lab groups list')}}</h3>
					   </div> 
						<div class="form-group col-md-4">
						  <select   id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0" style="width:100%;">
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
						<div class="form-group col-md-4">
						  <select   name="filter_cat" id="filter_cat" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">Choose a group category</option>
							@foreach($categories as $c)
							  <option value="{{$c->id}}">{{$c->descrip}}</option>
							@endforeach
						  </select>
						</div>
                        <div class="form-group col-md-2">
					         <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_test"><i class="fa fa-plus"></i> Create</a>
					    </div>						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="groups_dt" class="table table-bordered table-striped data-table display compact nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
										<th>{{__('Ordering')}}</th>
										<th>{{__('Name')}}</th>
										<th>{{__('Code')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Referred test')}}</th>
										<th>{{__('Price')}}</th>
										<th>{{__('Unit')}}</th>
										<th>{{__('Normal Value')}}</th>
										<th>{{__('CNSS')}}</th>
										<th>{{__('Remarks')}}</th>
										<th>{{__('NBL')}}</th>
										<th>{{__('LisCode')}}</th>
										<th> {{__('Instructions')}} </th>
										<th class="all">{{__('Actions')}}</th>
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
@include('tests.groups.groupLABModal')
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
	$('.select2_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent: $('#groupLABModal')});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
     });
	 
	
	var table = $('#groups_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[0,'desc'],[1,'asc']],
        info: true,
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
	    scrollY:"350px",
	    scrollX:true,
	    scrollCollapse: true,
        ajax: {
            url: "{{ route('lab.tests_groups.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_cat=$('#filter_cat').val();
                    }
              },
        columns: [
            {data: 'id',name:'g.id'},
		    {data:'order',name:'g.testord'},
			{data: 'descrip',name:'g.test_name'},
			{data: 'code',name:'g.test_code'},
			{data: 'cat_name',name:'cat.descrip'},
			{data: 'referred_lab',name:'ext.full_name'},
            {data: 'price',name:'g.price',render: function(data, type, row){
						 if(data==null || data=='') return '0.00';
						else  return  data;						 
						}
						},						
			{data: 'unit',name:'g.unit'},
			{data: 'normal_value',name:'g.normal_value'},
            {data: 'cnss',name:'g.cnss'},
            {data: 'test_rq',name:'g.test_rq'},
            {data: 'nbl',name:'g.nbl'},
            {data: 'listcode',name:'g.listcode'},			
			{data: 'instruction',name:'g.descrip'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		fixedColumns:   {
				left : 0,
				right: 1
				
				}
		
		
	});
	
	$('#create_test').click(function () {
		var lab_num = $('#filter_lab').val();
        $('#extLabForm').trigger("reset");
		$('.select2_modal').trigger('change.select2');
		$('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#testLABModal').find('#modalHeading').text('{{__("Create new group")}}');
		$('#extLabForm').find('#code_type').val("new");
        $('#groupLABModal').modal('show');
		
    });
	
	
	$("#filter_status,#filter_cat").change(function() {
	  table.ajax.reload(); 
    });  		
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        
		var name = $('#extLabForm').find('#descrip').val();
		//var code = $('#extLabForm').find('#code').val();
		//var cat = $('#extLabForm').find('#category_num').val();
		
		/*if(cat==''){
			Swal.fire({text:"Please input a category",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		if(code==''){
			Swal.fire({text:"Please input a code",icon:"error",customClass:"w-auto"});
			return false;
		}
		*/
		if(name==''){
			Swal.fire({text:"Please input a name",icon:"error",customClass:"w-auto"});
			return false;
		}
		
				
        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('lab.tests_groups.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
               if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			  }
			  if(data.success){
			     if($('#extLabForm').find('#code_type').val()=='edit'){
			      $('#extLabForm').trigger("reset");
				  $('#groupLABModal').modal('hide');
			     }
				if($('#extLabForm').find('#code_type').val()=='new'){
				  $('#extLabForm').trigger("reset");
		          $('.select2_modal').trigger('change.select2');
				  var lab_num = $('#filter_lab').val();
                  $('#extLabForm').find('#id').val('0');
		          $('#extLabForm').find('#lab_num').val(lab_num);
		          $('#extLabForm').find('#modalHeading').val('{{__("Create new group")}}');
				} 
			 
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
		  html:'{{__("Please note, this operation will affect the related codes for this group")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('lab.tests_groups.delete',app()->getLocale()) }}",
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
          url: "{{ route('lab.tests_groups.get_info',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		   $('#extLabForm').find('#id').val(res.data.id);
		  $('#extLabForm').find('#lab_num').val(res.data.clinic_num);
		  $('#extLabForm').find('#code').val(res.data.test_code);
		  $('#extLabForm').find('#descrip').val(res.data.test_name);
		  $('#extLabForm').find('#description').val(res.data.descrip);
		  $('#extLabForm').find('#price').val(res.data.price);
		  
		   if(res.data.referred_tests=='' || res.data.referred_tests==null){
			$('#extLabForm').find('#referred_tests').val('');  
		  }else{
		    $('#extLabForm').find('#referred_tests').val(res.data.referred_tests);
		  }
		  
		  if(res.data.category_num=='' || res.data.category_num==null){
			$('#extLabForm').find('#category_num').val('');  
		  }else{
		    $('#extLabForm').find('#category_num').val(res.data.category_num);
		  }
		  
		  
		  $('.select2_modal').trigger('change.select2');
		  
		  $('#extLabForm').find('#unit').val(res.data.unit);
		  $('#extLabForm').find('#normal_value').val(res.data.normal_value);
		  $('#extLabForm').find('#cnss').val(res.data.cnss);
		  $('#extLabForm').find('#test_rq').val(res.data.test_rq);
		  $('#extLabForm').find('#listcode').val(res.data.listcode);
		  $('#extLabForm').find('#testord').val(res.data.testord);
		  $('#extLabForm').find('#nbl').val(res.data.nbl);
		  $('#extLabForm').find('#preanalytical').val(res.data.preanalytical);
		  $('#extLabForm').find('#storage').val(res.data.storage);
		  $('#extLabForm').find('#transport').val(res.data.transport);
		  $('#extLabForm').find('#tat_hrs').val(res.data.tat_hrs);
		  $('#testLABModal').find('#modalHeading').text('{{__("Edit group")}}'+':#'+res.data.id);
		  $('#extLabForm').find('#code_type').val("edit");
          $('#groupLABModal').modal('show');
         
		 
		  
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