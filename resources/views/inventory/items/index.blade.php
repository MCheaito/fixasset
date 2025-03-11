<!--
 DEV APP
 Created date : 22-12-2022
-->
@extends('gui.main_gui')
@section('styles')
<style>
.data-header:not(.collapsed) .rotate-icon{
  transform: rotate(180deg);
}
</style>
@endsection
@section('content')	
 
                 <div class="container-fluid">	
					<div class="row  m-1">  
									<div class="mb-2 col-md-6">
										<h3>{{__('Items')}}</h3>
									</div>
									<div class="mb-2 col-md-3" hidden>
										<!--<label for="types" class="label-size">{{__('Types')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="selecttype" autocomplete="off"   id="selecttype" style="width:100%;">
											<option value="">{{__('Choose a type')}}</option>
											@foreach($Type as $Types)
												<option value="{{$Types->id}}">{{__($Types->name)}}</option>
											@endforeach 
										</select>
								    </div>
                                    <div class="mb-2 col-md-3">		
				
								       <a id="solItem" href="" class="btn btn-action">{{__('Add Items')}}</a>
						           </div> 	 
			    									
									
									<div class="mb-1 col-md-2">
										<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                               
								   <div class="mb-1 col-md-2" >
										<!--<label for="types" class="label-size">{{__('Types')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_type" autocomplete="off"   id="filter_type" style="width:100%;">
											<option value="">{{__('Select Type')}}</option>
													<option
														value="1" >
															{{__('Reagent')}}
													</option>
													<option
														value="2" >
															{{__('QC')}}
													</option>
													<option
														value="3" >
															{{__('Cal')}}
													</option>
													<option
														value="4" >
															{{__('Cons')}}
													</option>
										</select>
								    </div>
									<div class="mb-1 col-md-3">
										<!--<label for="types" class="label-size">{{__('Types')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_fournisseur" autocomplete="off"   id="filter_fournisseur" style="width:100%;">
											<option value="">{{__('Choose a Supplier')}}</option>
											@foreach($iSupplier as $sup)
												<option value="{{$sup->id}}">{{$sup->name}}</option>
											@endforeach 
										</select>
								    </div>
									<div class="mb-1 col-md-2">
										<!--<label for="types" class="label-size">{{__('Types')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="filter_category" autocomplete="off"   id="filter_category" style="width:100%;">
											<option value="">{{__('All categories')}}</option>
											@foreach($iCategory as $cat)
												<option value="{{$cat->id}}">{{$cat->name}}</option>
											@endforeach 
										</select>
								    </div>
									
									<div class="mb-1 col-md-2">
									  <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									  <select name="filter_status" id="filter_status" class="custom-select rounded-0">
										  <option value="O">{{__('Active')}}</option>
										  <option value="N">{{__('InActive')}}</option>
									  </select>	  
								    </div>
										<div class="mb-1 col-md-2">
									  <!--<label for="filter_status" class="label-size ">{{__('Status')}}</label>-->
									  <select name="filter_used" id="filter_used" class="custom-select rounded-0">
									   	<option value="">{{__('All Analyzer')}}</option>
											@foreach($Analyzer as $ana)
												<option value="{{$ana->id}}">{{$ana->name}}</option>
											@endforeach 
									  </select>	  
								    </div>
					    
			  					
				 </div>
	 				
									
	<div class="card-body p-0">
	
				
	 
   <section class="col-md-12">
				    <div class="card"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5>{{__("All Items")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body">	
							<div class="row m-1">
							   <div class="col-lg-12">
								 <table id="items_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('#')}}</th>
									<th class="all">{{__('SKU')}}</th>
									<th class="all">{{__('Supplier')}}</th>
									<th class="all ">{{__('Category')}}</th>
									<th class="all ">{{__('Analyzer')}}</th>
									<th class="all">{{__('Description')}}</th>
									<th class="all">{{__('Qty')}}</th>
									<th class="all">{{__('$Cost')}}</th>
									<th class="all">{{__('NbTest')}}</th>
									<th class="all">{{__('Actions')}}</th>
									</tr>
									</thead>
									<tbody>
									  
									</tbody>
								</table>
							</div>
						   </div>
						   	<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Total Stock Qty')}}</label>
							   <input  class="text-center form-control"  id="tsq"  value="{{$tsq}}" disabled  />
							</div>
							<div class="mb-1 col-md-3 col-4">
							   <label for="name">{{__('Total Amount')}}</label>
							   <input  class="text-center form-control"  id="tamount" value="{{$tamount}}" disabled  />
							</div>
							
							
							</div>
						</div>
                    </div>				
                </section> 													
				</div>
        </div>				
  @include('inventory.items.qtyHistoryModal')
@endsection	
@section('scripts')

<script>
$(document).ready(function(){
	
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	//console.log(localStorage.getItem("item_type"));
	
	if(localStorage.getItem("item_type") != null){
		var type = localStorage.getItem("item_type");
		$('#filter_type').val(type);
		$('#filter_type').trigger('change.select2');
	}
	
	
	var table = $('#items_table').DataTable({
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
						url: "{{ route('inventory.items.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_category=$('#filter_category').val();
								d.filter_status=$('#filter_status').val();
								d.filter_used=$('#filter_used').val();
								d.selecttype = $('#filter_type').val();
								d.filter_fournisseur = $('#filter_fournisseur').val();
							}
					},
						 
					columns: [
						{data: 'id',name:'tbl_inventory_items.id'},
						{data: 'sku'},
						{data: 'fournisseur',name:'tbl_fournisseur.name'},
						{data: 'cat_name',name:'cat.name'},
						{data: 'analyzer',name:'cat.name'},
						{data: 'description'},
						{data: 'qty',render: function(data, type, row){
								var qty = (data==null)?0:data;
								return qty+'<span class="ml-2"><button class="btn btn-xs btn-action" onclick="qty_history('+row.id+')"><i class="far fa-eye" title="{{__("History")}}"></i></button></span>';
							
						}},
						{data: 'cost_price'},
						{data: 'nbtest',name:'tbl_inventory_items.nbtest'},
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
	
	

});
$(function(){
	$('#solItem').click(function() {
		//var selecttype = $('#selecttype').val();
		var selecttype = '1';
		if(selecttype==""){
		Swal.fire({text:'{{__("Please choose a type")}}',icon:'error',customClass:'w-auto'});
			return false;
		}
		var id_lunette='0';
		//var url = "{{ route('inventory.items.lunette', [app()->getLocale(), 'REPLACE', '0']) }}";
		//url = url.replace('REPLACE', selecttype);
	//	$("a#solItem").attr("href", url);
		//switch (selecttype) { 
		
		//case '1': 	
		 $("a#solItem").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'1','0'])}}");
		// break;
		//case '2': 	
		// $("a#solItem").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'2','0'])}}");
		// break;
		//case '3':
		// $("a#solItem").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'3','0'])}}");
		//break;	
		//case '4':
		// $("a#solItem").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'4','0'])}}");
		//break;	
		//case '5':
		// $("a#solItem").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'5','0'])}}");
		//break;	
		//}
    });
 	
	
$('#filter_type,#filter_status,#filter_used,#filter_category,#filter_fournisseur').change(function(){
	var filter_type = $('#filter_type').val();
	var filter_category = $('#filter_category').val();
	var filter_status = $('#filter_status').val();
	var filter_used = $('#filter_used').val();
	var filter_fournisseur = $('#filter_fournisseur').val();
	 localStorage.setItem("item_type",filter_type);
	 
	$('#items_table').DataTable().ajax.reload();
	
	$.ajax({
            url: '{{route("generation_sumqtyprice",app()->getLocale())}}',
		   data: {'filter_type':filter_type,'filter_category':filter_category,'filter_used':filter_used,'filter_status':filter_status,'filter_fournisseur':filter_fournisseur},
           type: 'get',
           dataType: 'json',
           success: function(data){
			$('#tamount').val(data.tamount); 
			$('#tsq').val(data.tsq); 			
		   }
});
});

$('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
	$.ajax({
            url: '{{route("inventory.items.destroy",app()->getLocale())}}',
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
			  $('#items_table').DataTable().ajax.reload();

		      
			 }
       });	
    });
	
$('#qtyHistoryModal').on('shown.bs.modal',function(){
	$('#qty_history_table').DataTable({
					destroy:true,
					dom: '<"wrapper"lftp>',
					order : [],
					scrollY: "250px",
			        scrollX: true,
			        scrollCollapse:true,
					searching: true,
					paging : true,
					info: false,
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
				            left: 1,
				            right: 0
				        }
					

				});
});	
	

});
</script>
<script>
function qty_history(id){
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
	
	$.ajax({
            url: '{{route("inventory.items.item_qty_history",app()->getLocale())}}',
		   data: {'id':id},
           type: 'post',
           dataType: 'json',
           success: function(data){
              $('#qtyHistoryModal').find('#current_qty_div').html(data.html1);
			  $('#qtyHistoryModal').find('#qty_history_div').html(data.html);
			  $('#qtyHistoryModal').modal('show');
			  
			 }
       });	
	
}
</script>
@endsection	
