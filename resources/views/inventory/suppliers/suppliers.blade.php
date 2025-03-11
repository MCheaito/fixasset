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
											   <h3>{{__('Chart of Account')}}</h3>
											  </div> 
											 <div class="col-md-2 col-4">
											   <label class="m-1 label-size float-right badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</label>
											  </div>
																	
										</div>

									</div>			
                        <div class="card-body p-0">
										<form id="Suplliersform" action="{{route('store_suppliers',app()->getLocale())}}"
											method="post"	onsubmit="return confirm('{{__('Do you want to save the following results?')}}');">								
											@csrf
									<div class="m-1 row">
										
									<div class="col-6">
										<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$clinic->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$clinic->id}}"/>
								    </div>
									
                                 <div class="col-md-3">
												   <div class="form-group">
													<select name="type_fournisseur" id="type_fournisseur" class="custom-select rounded-0">
													<option value="1" {{isset($suppliers) && $suppliers->fournisseur=='1' ? 'selected' : ''}}>{{'General'}}</option>
													<option value="2" {{isset($suppliers) && $suppliers->fournisseur=='2' ? 'selected' : ''}}>{{'Suppliers'}}</option>
																										
													<option value="3" {{isset($suppliers) && $suppliers->fournisseur=='3' ? 'selected' : ''}}>{{'Depreciation'}}</option>
													<option value="4" {{isset($suppliers) && $suppliers->fournisseur=='4' ? 'selected' : ''}}>{{'Fixed Asset'}}</option>
													</select>
													</div> 	
												</div>
 								   <div class="col-md-3">
								         <a href="{{route('inventory.suppliers.index',[app()->getLocale(),''])}}" class="m-1 float-right btn btn-back">{{__('Back')}}</a> 
										 <!--<button type="button" id="btnPrintSuplliers" class="m-1 float-right btn btn-action  text-center" onClick="downloadPDF()">{{__('Print')}}</button>-->
								   </div>
								</div>												
										<div class="row m-1">
									
											<div class="col-md-2">
														<label class="label-size" style="font-size:16px;">{{__('Code')}}&#xA0;*</label>
															<div class="form-group">
															<input type="text" class="form-control" name="code" id="code" value="{{isset($suppliers)?$suppliers->code:old('code')}}"  readonly >												
											<!--	@error('code')
                                                <div class="alert alert-danger">{{__('The Code field is required')}}</div>
                                                @enderror    --> 
												</div> 	
												</div> 
												<div class="col-md-5">
														<label class="label-size" style="font-size:16px;">{{__('Name')}}&#xA0;*</label>
															<div class="form-group">
															<input type="text" class="form-control" name="name" id="name" value="{{isset($suppliers)?$suppliers->name:old('name')}}">												
												@error('name')
                                                <div class="alert alert-danger">{{__('The Name field is required')}}</div>
                                                @enderror     
												
												</div> 
													</div> 
												<div class="col-md-5">
														<label class="label-size" style="font-size:16px;">{{__('Contact')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="contact" id="contact" value="{{isset($suppliers)?$suppliers->contact:old('contact')}}">												
												</div> 													
												</div> 
										</div> 	
											<div class="row m-1">
										
													<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Email')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="email" id="email" value="{{isset($suppliers)?$suppliers->email:old('email')}}" >												
												          </div> 	
											        </div> 	
													<div class="col-md-4">
															<label class="label-size" style="font-size:16px;">{{__('Tel')}}&#xA0;&#xA0;</label>
																<div class="input-group">
																  <div class="input-group-prepend">
																	<span class="input-group-text" id="basic-addon1">+1</span>
																  </div>
																<input type="text" class="form-control" name="tel" id="tel" value="{{isset($suppliers)?$suppliers->tel:old('tel')}}" >												
															  </div> 	
													</div> 	
													 <div class="col-md-4">
															<label class="label-size" style="font-size:16px;">{{__('Fax')}}&#xA0;&#xA0;</label>
																<div class="input-group">
																  <div class="input-group-prepend">
																	<span class="input-group-text" id="basic-addon1">+1</span>
																  </div>
																  <input type="text" class="form-control" name="fax" id="fax" value="{{isset($suppliers)?$suppliers->fax:old('fax')}}" >												
															   </div> 	
													  </div> 		
												</div>
														
											<div class="row m-1">	
											       <div class="col-md-6">
															<label class="label-size" style="font-size:16px;">{{__('Adresse')}}&#xA0;&#xA0;</label>
																<div class="form-group">
																<input type="text" class="form-control" name="adresse" id="adresse" value="{{isset($suppliers)?$suppliers->adresse:old('adresse')}}">												
													            </div> 	
													</div>
											        <div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Ville')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="ville" id="ville" value="{{isset($suppliers)?$suppliers->ville:old('ville')}}" >												
												            </div> 	
											         </div> 	
													<div class="col-md-3">
																	<label class="label-size" style="font-size:16px;">{{__('Province')}}&#xA0;&#xA0;</label>
																		<div class="form-group">
																		<input type="text" class="form-control" name="province" id="province" value="{{isset($suppliers)?$suppliers->province:old('province')}}" >												
															           </div> 	
													</div> 	
										    
											</div> 
											<div class="row m-1">	
											    <div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Pays')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="pays" id="pays" value="{{isset($suppliers)?$suppliers->pays:old('pays')}}" >												
												           </div> 	
											    </div> 												
												<div class="col-md-3">
															<label class="label-size" style="font-size:16px;">{{__('Code Postal')}}&#xA0;&#xA0;</label>
																<div class="form-group">
																<input type="text" class="form-control" name="codepostal" id="codepostal" value="{{isset($suppliers)?$suppliers->codepostal:old('codepostal')}}" >												
													           </div> 	
												</div> 
												<div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Account Nb')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="num_compte" id="num_compte" value="{{isset($suppliers)?$suppliers->num_compte:old('num_compte')}}" >												
												         </div> 
												</div> 
												<div class="col-md-3" hidden >
														<label class="label-size" style="font-size:16px;">{{__('Sequence')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="sequence" id="sequence" value="{{isset($suppliers)?$suppliers->sequence:old('sequence')}}" >												
												           </div> 													
											    </div> 
											   <div class="col-md-3" hidden >
												   <label class="label-size" style="font-size:16px;">{{__('Code Method')}}&#xA0;&#xA0;</label>
												   <div class="form-group">
													<select name="code_method" id="code_method" class="custom-select rounded-0">
														<option value="1" {{isset($suppliers) && $suppliers->code_method=='1' ? 'selected' : ''}}>{{'Sequence'}}</option>
														<option value="2" {{isset($suppliers) && $suppliers->code_method=='2' ? 'selected' : ''}}  >{{'Auto'}}</option>
													</select>
													</div> 	
												</div>
												<div class="col-md-3" hidden >
															<label class="label-size" style="font-size:16px;">{{__('item Sequence')}}&#xA0;&#xA0;</label>
																<div class="form-group">
																<input type="text" class="form-control" name="item_seq" id="item_seq" value="{{isset($suppliers)?$suppliers->item_seq:'0'}}" >												
																</div> 													
												</div> 
												<div class="col-md-6 select2-teal" hidden >
											                <label for="od" class="label-size" style="font-size:16px;">{{__('Analyzer')}} </label>
															<select id="CollectionSuplliers" class="data-select2" name="collection[]" style="width:100%;" data-dropdown-css-class="select2-teal" data-placeholder="{{__('Select Collection')}}"  multiple="multiple" disabled >
																	  @foreach($supplierCollection as $sc)
																	  <option value="{{ $sc->id.';'.$sc->name }}" 
																	   {{(isset($CollIds) && count($CollIds)>0 && in_array($sc->id,$CollIds))?'selected':''}}>{{(app()->getLocale()=='fr')?$sc->name:$sc->name_eng}}</option>
																	 @endforeach
                                                                </select>
															
                                                </div>
										  
											
										   </div>
										<div class="row m-1 txt-border">
										
									@php $all_suppliers_type= isset($suppliers->types)?json_decode($suppliers->types ,true):[]; @endphp
									   
											
											  @foreach($types as $t) 
											  <div class="m-1 col-md-2" hidden >	
												
												<input type="checkbox" name="suppliers_type[]" value="{{$t->id}}"  {{(isset($suppliers->types) && $all_suppliers_type != NULL && in_array($t->id,$all_suppliers_type))? 'checked' : '' }} style="height:20px;width:20px;"/>
												<label class="m-1 label-size">{{$t->name}}</label>
												
											  </div>
											   @endforeach
																	
										</div>
										 
											
								
										<input type="hidden" name="id_suppliers" id="id_suppliers" value="{{$id_suppliers}}"/>
										<div class="row m-1">
											<div class="form-group col-md-9">
												<label class="label-size" for="notes" style="font-size:16px;">{{__("Notes")}}</label>
												<textarea name="notes" id="notes"  class="form-control"  rows="3">{{isset($suppliers)?$suppliers->notes:old('notes')}}</textarea>
											</div>
										</div>
					
										
										
									 <div class="row m-1">
									  
									   <div class="text-center col-md-12">
										  <input type="submit" id="btnSaveSuplliers" value="{{__('Save') }}"  class="btn btn-action">
										   <input type="button" id="btnModifySuplliers" value="{{__('Modify')}}"  class="btn btn-action">
										
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
   var phones = [{ "mask": "###-###-####"}, { "mask": "###-###-####"}];
			$('#tel,#fax').inputmask({ 
				mask: phones, 
				greedy: false, 
				definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
});

$(function(){
	 var id=$('#id_suppliers').val(); 
	 if (id!='0'){
	$("#Suplliersform :input").prop("disabled", true);
	//$("#btnadd").prop("disabled", true);
	$("#btnModifySuplliers").removeAttr('disabled');
	$("#btnPrintSuplliers").removeAttr('disabled');
	$("#docButton").removeAttr('disabled');
	
	 }else{
	
	$("#btnModifySuplliers").prop("disabled", true);
	$("#btnPrintSuplliers").prop("disabled", true);
	$("#btnSaveSuplliers").removeAttr('disabled');
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


$('#btnModifySuplliers').click(function(e){
	$("#Suplliersform :input").prop("disabled", false);
    $("#btnModifySuplliers").prop("disabled", true);	
    $("#CollectionSuplliers").prop("disabled", true);	

	//$("#code").prop("disabled", true);	
	});	
$('#btnSaveSuplliers').click(function(){
	var form = '#Suppliersform';
	var clinic_id=$('#clinic_id').val(); 
	 $('#Itemsform'). attr('data-action','{{route('store_suppliers',app()->getLocale())}}');
	 $(form).off().on('submit', function(event){
        event.preventDefault();
        var url = $(this).attr('data-action');
		
		$.ajax({
           url: url,
           type: 'post',
           data: $("#Suplliersform").serialize(),
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
					link.download=('suppliers.pdf');
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