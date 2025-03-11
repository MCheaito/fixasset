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
						<div class="col-md-8">
						  <h3 class="text-dark">{{__("Lab Tests List")}}</h3>
					   </div> 
						<div class="form-group col-md-4">
						  <select  id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0" style="width:100%;">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>	
						
                        <div class="form-group col-md-3">
						  <select  id="filter_group" name="filter_group" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a test")}}</option>
							@foreach($groups as $g)
							  <option value="{{$g->id}}">{{$g->test_name}}</option>
							@endforeach
						  </select>
						</div>		
                        <div class="form-group col-md-3">
						  <select  id="filter_cat" name="filter_cat" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a category")}}</option>
							@foreach($categories as $g)
							  <option value="{{$g->id}}">{{$g->descrip}}</option>
							@endforeach
						  </select>
						</div>
						 <div class="form-group col-md-auto">
						  <select  id="filter_type" name="filter_type" class="custom-select rounded-0">
							<option value="">{{__('Group/Not_Group/Culture')}}</option>
							<option value="G">{{__('Group')}}</option>
							<option value="NG">{{__('Not_Group')}}</option>
							<option value="CULT">{{__('Culture')}}</option>
						  </select>
						</div>
                        <div class="form-group col-md-auto">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						</div>
						<div class="form-group col-md-2">
					       <a class="ml-1 float-right btn btn-action" href="{{route('lab.tests.new',app()->getLocale())}}"><i class="mr-1 fa fa-plus"></i>{{__("Create")}}</a>
						</div>
                      						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="tests_dt" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
										<th>{{__('Group/Not_Group')}}</th>
									    <th>{{__('Order')}}</th>
										<th>{{__('Code')}}</th>
										<th>{{__('Name')}}</th>
										<th>{{__('Category')}}</th>
										<th>{{__('Referred LAB')}}</th>
										<th>{{__('Type')}}</th>
										<th>{{__('Price')}}</th>
										<th>{{__('Normal Value')}}</th>
										<th>{{__('CNSS')}}</th>
										<th>{{__('Remarks')}}</th>
										<th>{{__('NBL')}}</th>
										<th>{{__('LisCode')}}</th>
										<th> {{__('Instructions')}} </th>
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
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	function saveFilterState() {
        var v1 = $('#filter_lab').val();
        var v2 = $('#filter_group').val();
		var v3 = $('#filter_cat').val();
		var v4 = $('#filter_type').val();
        
		localStorage.setItem('testLab', v1);
        localStorage.setItem('testCode', v2);
		localStorage.setItem('testCat', v3);
		localStorage.setItem('testType', v4);
     }
	 
	function retrieveFilterState() {
        var v1 = localStorage.getItem('testLab');
        var v2 = localStorage.getItem('testCode');
		var v3 = localStorage.getItem('testCat');
		var v4 = localStorage.getItem('testType');
        
        if(v1){ $('#filter_lab').val(v1).trigger('change'); }
        if(v2){ $('#filter_group').val(v2).trigger('change'); }
		if(v3){ $('#filter_cat').val(v3).trigger('change'); }
		if(v4){ $('#filter_type').val(v4); }
        
    }
	
	retrieveFilterState();
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	// Custom sorting function for the 'order' column
    $.fn.dataTable.ext.order['dom-text-numeric'] = function(settings, col) {
        return this.api().column(col, { order: 'index' }).nodes().map(function(td, i) {
            return $('input', td).val() * 1; // Convert to numeric value
        });
    };
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')	}});
	var table = $('#tests_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		ordering: true,
		order: [[0,'desc'],[2,'asc']],
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
            url: "{{ route('lab.tests.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_group=$('#filter_group').val();
				d.filter_cat=$('#filter_cat').val();
				d.filter_type=$('#filter_type').val();
                    }
              },
        columns: [
            {data: 'id',name:'t.id'},
			{data: 'is_group',render: function(data, type, row){
				if(data=='Y') return '{{__("Group")}}';
				else  return '{{__("Not_Group")}}';
			}},
			{data: 'testord',name:'t.testord'},	
			{data: 'test_code',name:'t.test_code'},
			{data: 'test_name',name:'t.test_name'},
			{data: 'cat_name',name:'cat.descrip'},	
			{data: 'referred_lab',name:'ext.full_name'},
			{data:'test_type',name:'t.test_type',render: function(data, type, row){
						 if(data=='F') return '{{__("Formula")}}';
						 else{
							if(data=='C') return '{{__("Calculate")}}';
							else return '{{__("Normal")}}';
						 }
			}},
			{data: 'price',name:'t.price',render: function(data, type, row){
						 if(data==null || data=='') return '0.00';
						else  return  data;						 
						}
						},						
			{data: 'normal_value',name:'t.normal_value'},
            {data: 'cnss',name:'t.cnss'},
            {data: 'test_rq',name:'t.test_rq'},
            {data: 'nbl',name:'t.nbl'},
            {data: 'listcode',name:'t.listcode'},			
			{data: 'descrip',name:'t.descrip'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		fixedColumns:   {
				left : 0,
				right: 1
				
				}
		
	});
	table.columns.adjust();
	$('#tests_dt').on('change', '.order-input', function() {
        const id = $(this).data('id');
        const order = $(this).val();
        
		if (order < 1 || !Number.isInteger(Number(order))) {
            Swal.fire({icon:'error',customClass:'w-auto',text:'{{__("Order must be an integer greater than 0")}}'});
            return;
        }

        $.ajax({
            url: '{{ route("lab.tests.updateOrder",app()->getLocale()) }}',
            type: 'POST',
            data: {
                id: id,
                order: order,
                _token: '{{csrf_token()}}'
            },
            success: function(response) {
                table.ajax.reload(null, false); 
				if (response.success) {
                     Swal.fire({icon:'success',title:response.success,toast:true,timer:1500,position:'bottom-right',showConfirmButton:false});
                      
				} else {
                    if( response.error){ 
					 Swal.fire({icon:'error',customClass:'w-auto',text:response.error});
					}
					//do nothing for other orders
                }
            },
            error: function() {
                Swal.fire({icon:'error',customClass:'w-auto',text:'{{__("Error updating order")}}'});
				
            }
        });
    });
	
	$('#tests_dt').on('change', '.test-code-input', function() {
        const id = $(this).data('id');
        const test_code = $(this).val();

        $.ajax({
            url: '{{ route("lab.tests.updateTestCode",app()->getLocale()) }}',
            type: 'POST',
            data: {
                id: id,
                test_code: test_code,
                _token: '{{csrf_token()}}'
            },
            success: function(response) {
                table.ajax.reload(null, false); 
				if (response.success) {
                     Swal.fire({icon:'success',title:response.success,toast:true,timer:1500,position:'bottom-right',showConfirmButton:false});
                      
				} else {
                    if( response.error){ 
					 Swal.fire({icon:'error',customClass:'w-auto',text:response.error});
					}
					//do nothing for other orders
                }
            },
            error: function() {
                Swal.fire({icon:'error',customClass:'w-auto',text:'{{__("Error updating order")}}'});
				
            }
        });
    });
	
	
	
	

	$('body').on('click', '.toggle-chk', function (e) {
        e.preventDefault();
		var id = $(this).data("id");
		var checked = $(this).is(':checked')?'O':'N';
			Swal.fire({
		  title: '{{__("Are you sure?")}}',
		  html:'{{__("Please note, this operation will affect the related fields for this test code")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({

					type: "DELETE",
					url: "{{ route('lab.tests.delete',app()->getLocale()) }}",
					data:{id:id,checked:checked},
					dataType:"JSON",
					success: function (data) {
						Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
						 table.ajax.reload(null, false);
					}
				   });
				  
			  }else if (result.isDenied) {
				return false;
			  }
            })
     });
	
	$("#filter_status,#filter_lab,#filter_group,#filter_cat,#filter_type").change(function() {
	  saveFilterState();
	   table.ajax.reload(null, false);
    });  		

});
</script>

@endsection