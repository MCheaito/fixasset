<!--
 DEV APP
 Created date : 12-3-2023
-->
@extends('gui.main_gui')

@section('content')	
 
<div class="container-fluid">
            <div class="row m-1">  
								
					<div class="mb-1 col-md-12">	
					<h3>{{__('Sub Asset')}}</h3>
					 </div>	
					
									<!--<div class="mb-1 col-md-3">
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$clinic->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$clinic->id}}"/>
								    </div>-->
									
                               						
							
							 			 
					          <div class="mb-1 col-md-2">
								  <select name="filter_status" id="filter_status" class="custom-select rounded-0">
									  <option value="O">{{__('Active')}}</option>
									  <option value="N">{{__('InActive')}}</option>
								  </select>	  
								</div>
								  <div class="mb-1 col-md-5">
										<!--<label for="types" class="label-size">{{__('Fournisseur')}}</label>-->
										<select class="select2_data custom-select rounded-0" name="selectfournisseur" autocomplete="off"   id="selectfournisseur" style="width:100%;">
											<option value="">{{__('All suppliers')}}</option>
											@foreach($Fournisseur as $Fournisseurs)
												<option value="{{$Fournisseurs->id}}">{{$Fournisseurs->name}}</option>
											@endforeach 
										</select>
								    </div>		
					
					<div class="mb-1 col-md-5">		
				
					<button class="btn btn-action" id="btnAddCollection" name="AddCollection" onclick="addcollection()">{{__('Add Sub Asset')}}</button>
					<input  type="hidden" class="form-control"   id="id" />   
					</div> 	 
			    
			  					
			</div>
	 
	 	
	<div class="card-body p-0">
	
				
   <section class="col-md-12">
				    <div class="card"> 
						<div class="card-header text-white">
							 <div class="card-title"><h5>{{__("All Sub Asset")}}</h5></div>
							 <div class="card-tools">
								<button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse">
									<i class="fas fa-minus"></i>
								</button>
							 </div> 
						</div>										
						<div class="card-body">	
							<div class="row m-1">
							   <div class="col-lg-12">
								 <table id="collection_table" class="table table-bordered stripe cell-border order-column nowrap"  style="width:100%;">
									<thead>
									<tr>
									<th class="all">{{__('Sub Asset')}}</th>
									<th class="all">{{__('Main Asset')}}</th>
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
<!--collection modal-->
			<div class="modal fade" id="collectiontModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg">
								<h4 class="modal-title">{{__('Add New Sub Asset')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body">
						        <div class="container">
									<div class="row">   
									   <div class="col-md-4">
											<label class="label-size" for="name">{{__('Main Asset')}}</label>
											<input  class="form-control"   id="name_fournisseur"  disabled /> 
											<input  class="form-control"   id="id_fournisseur"  type="hidden" />   
									   </div>
									  
										<div class="col-md-4">
											<label class="label-size" for="name">{{__('Name fr')}}</label>
											 <input  class="form-control"  id="valcollection"  /> 
										</div>
										<div class="col-md-4">
											<label class="label-size" for="name">{{__('Name en')}}</label>
											 <input  class="form-control"  id="valcollectioneng"  /> 
										</div>
										</div>
									</div>
										 
								</div>					  
							
							  <div class="modal-footer justify-content-center">
								<input type="button" id="btnSaveCollectionModal" value="{{__('Save')}}"  class="btn btn-action">
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> </div> 
					 <!--end collectionModal-->
@endsection	
@section('scripts')

<script>
function addcollection()
{
 //var e = document.getElementById("selectfournisseur");
	//var str = e.options[e.selectedIndex].text;
  
	//alert(fname);
	if($('#selectfournisseur').val() == ""){
		Swal.fire({text:'{{__("Please choose a supplier")}}',icon:"error",customClass:'w-auto'});
		return false;
	}
	  var fname=$('#selectfournisseur :selected').text();	
	//document.getElementById("id_fournisseur").value =str;
    $("#name_fournisseur").val(fname);
	$("#id_fournisseur").val($('#selectfournisseur').val());
	$("#valcollection").val("");
	$("#valcollectioneng").val("");
	$("#id").val("0");
	$('#collectiontModal').modal('show');
}	



$(document).ready(function(){
	
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
	var table = $('#collection_table').DataTable({

					processing: false,
                    searching: true,
					serverSide: true,
                    order : [[0,'asc']],
					scrollY: "400px",
			        scrollX: true,
			        scrollCollapse:true,
					ajax: {
						url: "{{ route('inventory.collection.index',app()->getLocale()) }}",
					    data: function (d) {
								d.filter_status=$('#filter_status').val();
								d.selectfournisseur = $('#selectfournisseur').val();
							}
					},
						 
					columns: [
						{data: 'col_name'},
						{data: 'fname'},
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
	
 $('#selectfournisseur,#filter_status').change(function(){
	$('#collection_table').DataTable().ajax.reload();
});	

$('#btnSaveCollectionModal').click(function(event){
 //var fournisseur_id = document.getElementById("selectfournisseur");
 fournisseur_id=$("#id_fournisseur").val();
 collection_name=$("#valcollection").val();
 collection_name_eng=$("#valcollectioneng").val();
  clinic_id=$("#clinic_id").val();
  //var collection_name = document.getElementById("valcollection");
  id=$("#id").val();
  //alert(fournisseur_id+'-'+collection_name+'-'+clinic_id+'-'+id);
  //return false;
  if(collection_name==""){
  Swal.fire({text:'{{__("Please input a model name in french")}}',icon:'error',customClass:'w-auto'});
  return false;
  }
  if(collection_name_eng==""){
  Swal.fire({text:'{{__("Please input a model name in english")}}',icon:'error',customClass:'w-auto'});
  return false;
  }
$.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"fournisseur_id":fournisseur_id,"collection_name":collection_name,"collection_name_eng":collection_name_eng,"id":id},
   url: '{{route("SaveCollection",app()->getLocale())}}',
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
			  
              $('#collection_table').DataTable().ajax.reload();
			  $('#collectiontModal').modal('hide');
			}
		   }
 });
 
 });
 
 
 $('body').on('change','.toggle-chk',function(){
	var type =  ($(this).is(':checked'))?'activate':'inactivate';
	var id  = $(this).data("id");
	$.ajax({
            url: '{{route("inventory.collection.destroy",app()->getLocale())}}',
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
			  $('#collection_table').DataTable().ajax.reload();

		      
			 }
       });	
    });

});
</script>
<script>
function editcollection(id,name,fid,fname,name_eng){
//	alert($id);
//	alert($name);
//var e = document.getElementById("selectfournisseur");
//var str = e.options[e.selectedIndex].text;
//document.getElementById("id_fournisseur").value =str;
$('#name_fournisseur').val(fname);
$('#id_fournisseur').val(fid);
$("#valcollection").val(name);
$("#valcollectioneng").val(name_eng);

$("#id").val(id);
$('#collectiontModal').modal('show');
}

/*function delCollection(id){
	var url='{{route("inventory.collection.destroy",[app()->getLocale(),":id"])}}';
	url=url.replace(":id",id);
	$.ajax({
		url:url,
		method:'post',
		dataType:'json',
		success:function(data){
			Swal.fire({title:data.msg,icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false});
			$('#collection_table').DataTable().ajax.reload();
		}
	});
}*/
</script>
@endsection	
