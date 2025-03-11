<!--
 DEV APP
 Created date : 14-12-2022
-->
@extends('gui.main_gui')

@section('content')	

<div class="container">	
    <div class="row mt-2">
			<div class="card">    
				
				<div class="card-header text-white">
										
											<div class="row">
											  <div class="col-md-10 col-8">
											   <h3>{{__('Asset Main Category')}}</h3>
											  </div> 
											 <div class="col-md-2 col-4">
											   <label class="m-1 label-size float-right badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</label>
											  </div>
																	
										</div>

									</div>			
                        <div class="card-body p-0">
										<form id="Clientsform" action="{{route('store_assetmaincategory',app()->getLocale())}}"
											method="post"	onsubmit="return confirm('{{__('Do you want to save the following results?')}}');">								
											@csrf
									<div class="m-1 row">
										
									<div class="col-6">
										<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$clinic->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$clinic->id}}"/>
									<input type="hidden" name="id_assets" id="id_assets" value="{{$id_assets}}"/>


								    </div>
									
                              
 								   <div class="col-md-3">
								         <a href="{{route('inventory.assetmaincategory.index',[app()->getLocale(),''])}}" class="m-1 float-right btn btn-back">{{__('Back')}}</a> 
										 <!--<button type="button" id="btnPrintClients" class="m-1 float-right btn btn-action  text-center" onClick="downloadPDF()">{{__('Print')}}</button>-->
								   </div>
								</div>												
										<div class="row m-1">
									
										
												<div class="col-md-5">
														<label class="label-size" style="font-size:16px;">{{__('Name')}}&#xA0;*</label>
															<div class="form-group">
															<input type="text" class="form-control" name="name" id="name" value="{{isset($assets)?$assets->name:old('name')}}">												
												@error('name')
                                                <div class="alert alert-danger">{{__('The Name field is required')}}</div>
                                                @enderror     
												
												</div> 
													</div> 
												<div class="col-md-5">
														<label class="label-size" style="font-size:16px;">{{__('Account Nb')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="accountnb" id="accountnb" value="{{isset($assets)?$assets->accountnb:old('accountnb')}}">												
												</div> 													
												</div> 
										
													<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Depreciation')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="depreciation" id="depreciation" value="{{isset($assets)?$assets->depreciation:old('depreciation')}}" >												
												          </div> 	
											        </div> 	
											
													
														
											       <div class="col-md-6">
															<label class="label-size" style="font-size:16px;">{{__('Asset Life')}}&#xA0;&#xA0;</label>
																<div class="form-group">
																<input type="text" class="form-control" name="asset_life" id="asset_life" value="{{isset($assets)?$assets->asset_life:old('asset_life')}}">												
													            </div> 	
													</div>
											        <div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Asset Age')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="asset_age" id="asset_age" value="{{isset($assets)?$assets->asset_age:old('asset_age')}}" >												
												            </div> 	
											         </div> 	
												
										    
											</div> 
										
																	
																			
										
									 <div class="row m-1">
									  
									   <div class="text-center col-md-12">
										  <input type="submit" id="btnSaveClients" value="{{__('Save') }}"  class="btn btn-action">
										   <input type="button" id="btnModifyClients" value="{{__('Modify')}}"  class="btn btn-action">
										
									   </div>
									 
									</div>
								  </form> 
				</div>
				</div>
			</div>
			</div><!--end card ref-->	
	

@endsection	
@section('scripts')
<script>
$(document).ready(function() {
	$('.select2').select2({theme:'bootstrap4',resolve:'resolve'});

   $('.data-select2').select2();
 //  $('#email').inputmask("email");
 
});

$(function(){
	 var id=$('#id_assets').val(); 
	 if (id!='0'){
	$("#Clientsform :input").prop("disabled", true);
	//$("#btnadd").prop("disabled", true);
	$("#btnModifyClients").removeAttr('disabled');
	$("#btnPrintClients").removeAttr('disabled');
	$("#docButton").removeAttr('disabled');
	
	 }else{
	$('#depreciation').val("0.00"); 
	$('#asset_life').val("0.00"); 
	$('#asset_age').val("0.00"); 
	$("#btnModifyClients").prop("disabled", true);
	$("#btnPrintClients").prop("disabled", true);
	$("#btnSaveClients").removeAttr('disabled');
	$("#docButton").prop("disabled", true);
	 }
	  
});

$('#name').change(function(e){
	var code1=$("#code").val();
	if (code1==''){
	$("#code").removeAttr('disabled');
	var inputString = $("#name").val().substr(0,3);
	$("#code").val(inputString);
	}
	});	


$('#btnModifyClients').click(function(e){
	$("#Clientsform :input").prop("disabled", false);
    $("#btnModifyClients").prop("disabled", true);	
	$("#userp_code").prop("disabled", true);	
	});	
$('#btnSaveClients').click(function(){
	var form = '#clientsform';
	var id_assets=$('#id_assets').val(); 
	 $('#Itemsform'). attr('data-action','{{route('store_clients',app()->getLocale())}}');
	 $(form).off().on('submit', function(event){
        event.preventDefault();
        var url = $(this).attr('data-action');
		
		$.ajax({
           url: url,
           type: 'post',
           data: $("#Clientsform").serialize(),
           dataType: 'JSON',
           success: function(data){
            if(data.warn){
				alert(data.warn);
			}else{
				if(data.success){
				
				   
				   }
			}
		   }	
		 }); 
	 });
      });

	

 function downloadPDF(){	 
   var id=document.getElementById("id_lunette").value;	
   if (id!='0'){
    $.ajax({
           url: '',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('clients.pdf');
					link.click();	
			window.location.href="";
					
			    });
 }
 }
 
  
 
 
</script>

<script>
$(function () {
 	 $('.data-select2').trigger('change.select2');										
  });
</script>
@endsection