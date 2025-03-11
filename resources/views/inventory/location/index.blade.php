<!--
 DEV APP
 Created date : 13-3-2023
-->
@extends('gui.main_gui')

@section('content')	
 
<div class="container-fluid">
            <div class="row m-1">  
								
					<div class="col-md-12 mb-1">	
					<h3>{{__('Location')}}</h3>
					 </div>
					  		 	
							
									<div class="mb-1 col-md-3">
										<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                               
								<div class="mb-1 col-md-3">
								  <select name="filter_status" id="filter_status" class="custom-select rounded-0">
									  <option value="O">{{__('Active')}}</option>
									  <option value="N">{{__('InActive')}}</option>
								  </select>	  
								</div>
							<div class="mb-1 col-md-3" hidden>
										<!--<label for="types" class="label-size">{{__('Types')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="selecttypes" autocomplete="off"   id="selecttypes" style="width:100%;">
											<option value="">{{__('All types')}}</option>
											@foreach($Type as $Types)
												<option value="{{$Types->id}}">{{__($Types->name)}}</option>
											@endforeach 
										</select>
								    </div>					 
					<div class="mb-1 col-md-3">		
				
					<button class="btn btn-action" id="btnAddLocation" name="AddLocation" onclick="addLocation()">{{__('Add Location')}}</button>
					<input  type="hidden" class="form-control"   id="id" value=""  />   
					</div> 	 
				
			    
			  					
			</div>
	 
	 	
	<div class="card-body p-0">
				
   <section class="col-md-12">
				    <div class="card"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5>{{__("All Location")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body">	
							<div class="row m-1">
							   <div class="col-lg-12">
								 <table id="location_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('Code')}}</th>
									<th class="all">{{__('Location')}}</th>
									<th class="all">{{__('Actions')}}</th>
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
<!--Location modal-->
			<div class="modal fade" id="locationtModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg">
								<h4 class="modal-title">{{__('Add New Location')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body">
						        <div class="container">
									<div class="row">   
									  
									  	<div class="col-md-4">
											<label class="label-size" for="name">{{__('Code')}}</label>
											 <input  class="form-control"  value="" id="valcode"  /> 
										</div>
										</div>
									<div class="row">   

										<div class="col-md-6">
											<label class="label-size" for="name">{{__('Details')}}</label>
											 <input  class="form-control"  value="" id="vallocation"  /> 
										</div>
										<div class="col-md-6" hidden>
											<label class="label-size" for="name">{{__('Details en')}}</label>
											 <input  class="form-control"  value="" id="vallocationeng"  /> 
										</div>
										</div>
									</div>
										 
								</div>					  
							
							  <div class="modal-footer justify-content-center">
								<input type="button" id="btnSaveLocationModal" value="{{__('Save')}}"  class="btn btn-action">
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> </div> 
					 <!--end LocationModal-->
@endsection	
@section('scripts')

<script>
function addLocation()
{
$("#vallocation").val("");
$("#valcode").val("");
$("#vallocationeng").val("");

$("#id").val("0");
$('#locationtModal').modal('show');
}	



$(document).ready(function(){
	
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	var table = $('#location_table').DataTable({

					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[0,'asc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.location.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status').val();
							}
					},
						 
					columns: [
						{data: 'code'},
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
	
	

});
$(function(){
	
 $('#selecttypes,#filter_status').change(function(){
	$('#location_table').DataTable().ajax.reload();
});	

$('#btnSaveLocationModal').click(function(event){
 //var fournisseur_id = document.getElementById("selectfournisseur");
 location_name=$("#vallocation").val();
  clinic_id=$("#clinic_id").val();
 location_name_eng=$("#vallocationeng").val();
 valcode=$("#valcode").val();

  //var location_name = document.getElementById("vallocation");
  id=$("#id").val();
   if(valcode==""){
  Swal.fire({text:'{{__("Please input a location code")}}',icon:'error',customClass:'w-auto'});
  return false;
  }
  //if(location_name_eng==""){
 // Swal.fire({text:'{{__("Please input a location in english")}}',icon:'error',customClass:'w-auto'});
 // return false;
 // }
$.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"valcode":valcode,"location_name":location_name,"location_name_eng":location_name_eng,"id":id},
   url: '{{route("SaveLocation",app()->getLocale())}}',
   dataType: 'JSON',
       success: function(data){
			  if(data.success){
			  Swal.fire({ 
              "toast":true,
			  "title":data.success,
              "icon":"success",
			  "timer":3000,
			  "toast": "true",
			  "position":"bottom-right",
			  "showConfirmButton":false
			  });
			  $('#locationtModal').modal('hide');
			  $('#location_table').DataTable().ajax.reload();
			}
			
		   }
 });
 
 });
 
 $('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajax({
            url: '{{route("inventory.location.destroy",app()->getLocale())}}',
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
			  $('#location_table').DataTable().ajax.reload();

		      
			 }
       });	
    });

});
</script>
<script>
function editlocation(id,code,name,type_name,name_eng){
//	alert($id);
//	alert($name);
//var e = document.getElementById("selecttypes");
//var str = e.options[e.selectedIndex].text;
//document.getElementById("id_types").value =str;
$('#name_types').val(type_name);
$("#vallocation").val(name);
$("#vallocationeng").val(name_eng);

$("#id").val(id);
$('#locationtModal').modal('show');
}

/*function dellocation(id){
	var url='{{route("inventory.location.destroy",[app()->getLocale(),":id"])}}';
	url=url.replace(":id",id);
	$.ajax({
		url:url,
		method:'post',
		dataType:'json',
		success:function(data){
			Swal.fire({title:data.msg,icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false});
			$('#location_table').DataTable().ajax.reload();
		}
	});
}*/
</script>
@endsection	
