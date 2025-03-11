<!-- 
 DEV APP
 Created date : 30-9-2022
-->
@extends('gui.main_gui')

@section('content')
        
<div class="mt-1 container-fluid">
  <div class="row">
       <div class="col-md-12">	
            <div class="card card-outline">
			    <div class="card-header card-menu p-1">
				  <div class="row">
					   <div class="col-md-9 text-left">	
						<ul class="nav nav-pills">
						  <li class="nav-item"><a class="nav-link active" href="#persos" data-toggle="tab">{{__('General Information')}}</a></li>
						</ul>
						</div>
						
				   </div>	
				</div>
				<div class="card-body p-0">
				   <div class="tab-content">
				       <div id="persos" class="tab-pane active">
					               <form id="clinic_info">
                                    	
                                    <div class="m-1 row">
										<div class="col-md-12 text-right">
													<button id="save_clinic_info" class="btn btn-action">{{__('Save')}}</button>
													<button  type="reset" class="btn btn-reset">{{__("Reset")}}</button>
													
										</div>
										<div class="form-group col-md-6">
											 @if(auth()->user()->permission!='U')  
											   <label for="full_name" class="label-size">{{__("Name")}}</label>
											   <input id="full_name" class="form-control" type="text" name="full_name"
													  value="{{isset($clinUser) ? $clinUser->full_name : old('full_name')}}"/>					  	   
										     @else
												  <label for="full_name" class="label-size">{{__("Name")}}</label>
											   <input id="full_name" class="form-control" type="text" name="full_name"
													   disabled value="{{isset($clinUser) ? $clinUser->full_name : old('full_name')}}"/>
										     @endif		 
										</div>
										 <div class="col-md-3">
					                      <label for="name" class="label-size">{{__('Code')}}</label>
						                  <input type="text" name="code"  class="form-control" value="{{isset($clinUser) ? $clinUser->code : old('code')}}"/> 
           					             </div>
										<div class="col-md-2">
										 <label for="name" class="label-size">{{__('Rate')}}</label>
										 <select name="rate" id="rate" class="form-control">
										  <option value="USD">US Dollar</option>
										  <option value="LBP" {{isset($clinUser) && $clinUser->rate=='LBP' ?'selected':''}}>Lebanese Pound</option>
										 </select>
									   </div>
									   
										<div class="form-group col-md-2">
											<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
											<input id="pricel" class="form-control" type="text" name="pricel" 
										     value="{{isset($clinUser) ? $clinUser->pricel : old('pricel')}}" onkeypress="return isNumberKey(event)"
											 oninput="var rated = '{{$lbl_usd}}'; var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){$('#priced').val(parseFloat(price/rated).toFixed(2));$('#pricee').val(parseFloat(price/ratee).toFixed(2));}else{  $('#priced').val(''); $('#pricee').val(''); }"/>
														  
										</div>		
										<div class="form-group col-md-2">
															<label for="priced" class="label-size">{{__('Price$')}}</label>
															 <input id="priced" class="form-control" type="text" name="priced" 
															 oninput="var rated = '{{$lbl_usd}}';var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){ $('#pricel').val(price*rated);$('#pricee').val(parseFloat((price*rated)/ratee).toFixed(2));} else{ $('#pricel').val(''); $('#pricee').val(''); }"
															 value="{{isset($clinUser) ? $clinUser->priced : old('priced')}}" onkeypress="return isNumberKey(event)"/>  
									    </div>	
													
										<div class="form-group col-md-2">
															<label for="pricee" class="label-size">{{__('Price€')}}</label>
															 <input id="pricee" class="form-control" type="text" name="pricee" 
															    value="{{isset($clinUser) ? $clinUser->pricee : old('pricee')}}" onkeypress="return isNumberKey(event)"/> 
										</div>
										 <div class="col-md-3">
										  <label for="category" class="label-size">{{__('Category')}}</label>
										  <select  id="category" name="category" class="form-control" disabled>
											<option value="">{{__('Choose a category')}}</option>
											@foreach($cats as $c)
											<option value="{{$c->id}}" {{isset($clinUser) && isset($clinUser->category) && $clinUser->category==$c->id?'selected':''}}>{{app()->getLocale()=='fr'?$c->name_fr:$c->name_en}}</option>
											@endforeach
										  </select>
										</div>	
										<div class="form-group col-md-3">
											  <label for="email" class="label-size">{{__("Email")}}</label>
											  <input type="text" id="email" name="email" class="form-control"
													   value="{{isset($clinUser->email)?$clinUser->email:(isset($user_email)? $user_email:old('email'))}}"/>
										   
										</div>
										
										<div class="form-group  col-md-3">
											  <label for="fax" class="label-size">{{__("Fax Nb")}}</label>
											  
											  <input type="text" id="fax" name="fax"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->fax : old('fax')}}" {{auth()->user()->permission=='U'?'disabled':''}} />
										</div> 
                                        	
                                       <div class="form-group  col-md-3">
											  <label for="telephone" class="label-size">{{__("Contact Phone")}}</label>
											  
											  <input type="text" id="telephone" name="telephone"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->telephone : old('telephone')}}"/>
										      
										</div>
      									<div class="form-group  col-md-3">
											  <label for="alternate_phone1" class="label-size">{{__("Alternate Phone1")}}</label>
											  
											  <input type="text" id="alternate_phone1" name="alternate_phone1"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->alternate_phone1 : old('alternate_phone1')}}"/>
										      
										</div> 
										<div class="form-group  col-md-3">
											  <label for="alternate_phone2" class="label-size">{{__("Alternate Phone2")}}</label>
											 
											  <input type="text" id="alternate_phone2" name="alternate_phone2"  class="form-control"
													   value="{{isset($clinUser) ? $clinUser->alternate_phone2 : old('alternate_phone2')}}"/>
										       
										</div> 
											
										
																
                                        <div class="form-group col-md-6">
											  <label for="address" class="label-size">{{__("Address")}}</label>
											  <input type="text" id="full_address" name="full_address" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->full_address : old('full_address')}}"/>
										</div>
										
                                        <!--<div class="form-group  col-md-2">
											  <label for="appt_nb" class="label-size">{{__("Appt.#")}}</label>
											  <input type="text" id="appt_nb" name="appt_nb" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->appt_nb : old('appt_nb')}}"/>
										</div>
										<div class="form-group  col-md-4">
											  <label for="city_name" class="label-size">{{__("City Name")}}</label>
											  <input  name="city_name" type="text" class="form-control" value="{{isset($clinUser) ? $clinUser->city_name : old('city_name')}}"/>
 
										</div>
										<div class="form-group  col-md-4">
												<label for="region_name" class="label-size">{{__('Region Name')}}</label>
                                                <input  class="form-control" name="region_name" type="text" value="{{isset($clinUser) ? $clinUser->region_name : old('region_name')}}"/>
												
										</div>
										<div class="form-group  col-md-4">
											  <label for="state" class="label-size">{{__("State")}}</label>
											  <input type="text" id="state" name="state" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->state : old('state')}}"/>
										</div>
                                        <div class="form-group  col-md-4">
											  <label for="zip_code" class="label-size">{{__("Zip code")}}</label>
											  <input type="text" id="zip_code" name="zip_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->zip_code : old('zip_code')}}"/>
										</div>
                                      	
                                        <div class="form-group col-md-2">
											  <label for="province_code" class="label-size">{{__("Province Code")}}</label>
											  <input type="text" id="province_code" name="province_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->province_code : old('province_code')}}"/>
										</div>
                                        <div class="form-group col-md-2">
											  <label for="country_code" class="label-size">{{__("Country Code")}}</label>
											  <input type="text" id="country_code" name="country_code" class="form-control"
													   value="{{isset($clinUser) ? $clinUser->country_code : old('country_code')}}"/>
										</div>--> 											
                                        
                                        <div class="form-group  col-md-6">
											<label for="remarks" class="label-size">{{__("Remarks")}}</label>
											<textarea  class="form-control" id="remarks" name="remarks">{{isset($clinUser)?$clinUser->remarks : old('remarks')}}</textarea>
													
									     </div>
										 
									</div>
									 
									</form>	
                                  
					   </div>
					   
				   </div>
				</div>
			</div>
		</div>  
     </div>
</div>	
@endsection 
@section('scripts')
 
<script>
        $(document).ready(function() {
            
			
				$('.select2').select2();
				$('[name=email]').inputmask("email");
				var phones = [{ "mask": "##-###-###"}, { "mask": "##-###-###"}];
					$('[name=telephone],[name=fax]').inputmask({ 
						mask: phones, 
						greedy: false, 
						definitions: { '#': { validator: "[0-9]", cardinality: 1}} 
					});

					
           
        });
</script>                  

<script>
	$(function(){
		
		
		$('#save_clinic_info').off().on('click',function(e){
               e.preventDefault();
			    $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
			   $.ajax({
				 url: "{{route('profiles.clinic.info',[app()->getlocale(),$clinUser->id])}}",
                 method: "PUT",
				 dataType: "JSON",
                  data: $('#clinic_info').serialize(),
				  success: function(result){
                   
					 if(result.errors){

						Swal.fire({"text":result.errors[0],"icon":"error","customClass": "w-auto"});
					 }
					 if(result.error_useremail){

						Swal.fire({"text":result.error_useremail,"icon":"error","customClass": "w-auto"});
					 }
					 
					 if(result.success){
						   
						   Swal.fire({
								icon: 'success',
								toast: true,
								position: 'bottom-end',
								timer: 5000,
								showConfirmButton: false,
								title: result.success
								});
								//window.location.href="{{route('profiles.clinic',app()->getLocale())}}";
								//location.reload();
								
                           }

                       }

			   });
			});
	});
  </script>	
<script>
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