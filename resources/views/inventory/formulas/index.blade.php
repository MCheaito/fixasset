<!--
 DEV APP
 Created date : 18-6-2023
-->
@extends('gui.main_gui')

@section('content')	
 
<div class="container-fluid">
            <div class="row m-1">  
								
					<div class="mb-1 col-md-12">	
					<h3>{{__('Formula')}}</h3>
					 </div>
						
                               
									<div class="mb-1 col-md-3">
										<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                                						
					 
					               
								 <div class="mb-1 col-md-3">
									  <select name="filter_formula" id="filter_formula" class="select2_data form-control" style="width:100%;">
										<option value="">{{__('All Formula')}}</option>
										@foreach($all_formula as $s)
										  <option value="{{$s->id}}">{{$s->name}}</option> 
										@endforeach
									  </select>
								 </div>
								<div class="mb-1 col-md-3">
									  <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									  <select name="filter_status" id="filter_status" class="custom-select rounded-0">
										  <option value="O">{{__('Active')}}</option>
										  <option value="N">{{__('InActive')}}</option>
									  </select>	  
								    </div>
								<div class="mb-1 col-md-3">		
							
											<a id="solItem" href="{{route('NewFormula',[app()->getLocale()])}}" class="btn btn-action  float-right">{{__('Add Formula')}}</a>
								
								</div> 	 
			    
			  					
			</div>
	 
	 	
	<div class="card-body p-0">
	
				
   <section class="col-md-12">
				    <div class="card"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5>{{__("All Formula")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body">	
							<div class="row m-1">
							   <div class="col-lg-12">
								 <table id="formula_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th>{{__('#')}}</th>
									<th>{{__('ID')}}</th>
									<th>{{__('Name')}}</th>
									<th>{{__('Actions')}}</th>
									</tr>
									</thead>
									<tbody>
									  
									</tbody>
								</table>
							</div>
						   </div>
						</div>
                    </div>				
                </section> 													
				</div>						  
  </div>	  

@endsection	
@section('scripts')

<script>
$(function(){
	 

	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	var table = $('#formula_table').DataTable({
					stateSave: true,
                    stateDuration: -1,
					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[0,'desc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.formulas.index',app()->getLocale()) }}",
					    data: function (d) {
								
								//d.selecttype = $('#selecttype').val();
								//d.filter_clinic = $('#filter_clinic').val();
								d.filter_status=$('#filter_status').val();
								d.filter_formula = $('#filter_formula').val();
							}
					},
						 
					columns: [
						{data: 'DT_RowIndex'},
						{data: 'id'},
						{data: 'name'},
						{data: 'action',orderable: false, searchable: false},

					],
					language :{
					       
							search:         "{{__('Search')}}&nbsp;:",
							lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
							info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
							infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
							zeroRecords:    "{{__('No data is found')}}",
							emptyTable:     "{{__('No data is found')}}",
							buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
						
								},
							paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				         },
					
					fixedColumns:   {
				            left: 0,
				            right: 1
				        }
					

				});	
	
	$('#filter_formula,#filter_status').change(function(){
		table.ajax.reload();
	});
	
$('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajax({
            url: '{{route("inventory.formulas.destroy",app()->getLocale())}}',
		   data: {'id':id,'type':type},
           type: 'post',
           dataType: 'json',
           success: function(data){
           
			    Swal.fire({ 
              "title":data.msg,
			  "toast":true,
              "icon":"success",
			  "timer":3000,
			  "position":"bottom-right",
			  "showConfirmButton":false
			  });
			  table.ajax.reload();

		      
			 }
       });	
    });

});

</script>
<script>

 	

</script>

@endsection	
