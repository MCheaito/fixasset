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
						<div class="col-md-7">
						  <h3 class="text-dark">{{__("Template Reports List")}}</h3>
					   </div> 
						<div class="form-group col-md-5">
						  <select  id="filter_lab" name="filter_lab" class="custom-select rounded-0" style="width:100%;">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>	
						
                        <div class="form-group col-md-5">
						  <select  id="filter_test" name="filter_test" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a code")}}</option>
							@foreach($tests as $t)
							  <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
						</div>		
                        <div class="form-group col-md-3">
						  <select  id="filter_cat" name="filter_cat" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a category")}}</option>
							@foreach($categories as $c)
							  <option value="{{$c->id}}">{{$c->descrip}}</option>
							@endforeach
						  </select>
						</div>
						<div class="form-group col-md-2">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						</div>
						<div class="form-group col-md-2">
					       <button type="button" class="ml-1 float-right btn btn-action" id="create_custom_report"><i class="mr-1 fa fa-plus"></i>{{__("Create")}}</button>
						</div>
                      						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="customreports_dt" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
										<th>{{__('Report Name')}}</th>
										<th>{{__('Test Name')}}</th>
										<th>{{__('Test Category')}}</th>
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
@include('reports.custom.CRUDModal')
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')	}});
	var table = $('#customreports_dt').DataTable({
		stateSave: true,
	   	processing: true,
        serverSide: false,
		searching: true,
		ordering: true,
		order: [[0,'desc']],
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
        info: true,
	    scrollY: 480,
	    scrollX:true,
	    scrollCollapse: true,
        ajax: {
            url: "{{ route('custom_reports.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_test=$('#filter_test').val();
				d.filter_cat=$('#filter_cat').val();
                    },
			dataSrc: 'data'		
              },
        columns: [
            {data: 'id'},
			{data: 'report_name'},
			{data: 'test_name'},
			{data: 'cat_name'},
			{data: 'action',orderable: false, searchable: false}
        ],
		fixedColumns:   {
				left : 0,
				right: 1
				
				}
		
	});
	table.columns.adjust();

	$('body').on('click', '.toggle-chk', function (e) {
        e.preventDefault();
		var id = $(this).data("id");
		var checked = $(this).is(':checked')?'O':'N';
			Swal.fire({
		  title: '{{__("Are you sure?")}}',
		  html: checked=='O'?'{{__("Please note, this operation will activate this custom report")}}':'{{__("Please note, this operation will inactivate this custom report")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('custom_reports.delete',app()->getLocale()) }}",
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
	 
	$('#CRUDModal').on('show.bs.modal',function(){
		
		$('#test_id').select2({theme:'bootstrap4',width:'resolve',dropdownParent: $('#CRUDModal')});
		
		
	   
      $('#saveBtn').off().on('click',function (e) {
        e.preventDefault();
        var report_name=$('#crudForm').find('#report_name').val();
		
		if(report_name==''){
			Swal.fire({text:"Please input a report name",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var code=$('#crudForm').find('#test_id').val();
		
		if(code==''){
			Swal.fire({text:"Please choose a code",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var id = $('#crudForm').find('#id').val();
		var url = (id=='0')?"{{route('custom_reports.create',app()->getLocale())}}":"{{route('custom_reports.edit',app()->getLocale())}}";
		
		var description = $('#description').summernote('code');
        
		$.ajax({

          data: {id:id,report_name:report_name,test_id:code,description:description},
          url: url,
          type: "POST",
          dataType: "JSON",
          success: function (data) {
               if(data.error){
				Swal.fire({text:data.error,icon:"error",customClass:"w-auto"});  
			   }
			  if(data.success){
			  $('#crudForm').trigger("reset");
              $('#CRUDModal').modal('hide');
			  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
              table.ajax.reload();
			  }
           }

      });

    });
	
   });
   
    $('#CRUDModal').on('hidden.bs.modal', function (event) {
        $('#description').summernote('destroy');
      });

    $('#create_custom_report').click(function () {
		$('#saveBtn').text("{{__('Save')}}");
        $('#crudForm').find('#id').val('0');
		$('#crudForm').trigger("reset");
		$('#test_id').trigger('change.select2');   
        $('#modelHeading').html("Create custom report");
		$('#description').summernote({
		  
		  toolbar: [
		    ['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript','fontname']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
            ['insert', ['picture']]
		  ],
		  dialogsInBody: true,
		  height: '500px'
		  });
	    $('#description').summernote('reset');
        $('#CRUDModal').modal('show');
    });

	
	
	$("#filter_status,#filter_lab,#filter_test,#filter_cat").change(function() {
	  table.ajax.reload(); 
    });  		

});
</script>
<script>
function printData(id) {
	$.ajax({
			url:'{{route("custom_reports.printPDF",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',id:id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download= 'Report#'+id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
}
function editData(id) {
      $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    
	  $.ajax({
          data: {id:id},
          url: "{{ route('custom_reports.getInfo',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#id').val(res.data.id);
		  $('#test_id').val(res.data.test_id);
		  $('#test_id').trigger('change.select2');
		  $('#report_name').val(res.data.report_name);
		  $('#description').summernote({
		  
		  toolbar: [
		    ['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript','fontname']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
            ['insert', ['picture']]
		  ],
		  dialogsInBody: true,
		  height: '500px'
		  });
		  $('#description').summernote('reset');
		  $('#description').summernote('code', res.data.description);
		  $('#modelHeading').html("{{__('Edit custom report')}}");
          $('#saveBtn').text("{{__('Update')}}");
          $('#CRUDModal').modal('show');
          }
      });

    }


</script>

@endsection