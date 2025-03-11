<!-- 
 DEV APP
 Created date : 14-1-2023
-->
@extends('gui.main_gui')
@section('styles')
<style>
.badge{
	font-size:0.9rem;
}
</style>
@endsection
@section('content')
<!-- begin:: Content Container-->
<div class="container-fluid">
						
						
                        <div class="card mt-1">
						     <!--begin::Form-->
										 
										 <form action="{{isset($doctor) ? route('resources.update',[app()->getLocale(),$doctor->id]) : route('resources.store',app()->getLocale())}}"
										  method="POST"   enctype="multipart/form-data">
									   
											
											@csrf
											@if(isset($doctor))
												@method('PUT')
											@endif
							<div class="card-header card-menu">
							  <div class="row m-1">
								<div class="col-md-3 text-dark col-6">
									<h3>{{isset($doctor) ? __('Edit') : __('Create')}}</h3>
									
								</div>
								<div class="col-md-3 mt-1 col-6 text-right">
								  <span class="badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</span>
								</div>
								
								<div class="col-md-6 text-right">
									    <input type="submit" 
														   name="update_professional"
														   value="{{isset($doctor) ? __('Update') : __('Save')}}"
														   class="btn btn-action"/>
									   <a href="{{ route('resources.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>
								      
								 </div>
						       </div>	
                            </div> 							
						    <div class="card-body p-0"> 
                            
										
									   <!--begin doctor profile-->                  
										<div class="row m-1">
											
											<div class="col-md-12">
											  <div class="row">
													<div class="form-group col-md-4">
															<label for="first_name" class="label-size">{{__('First Name').'*'}}</label>
															 <input id="first_name" class="form-control" type="text" name="first_name" style=" text-transform: uppercase;"
															   placeholder="{{__('NEW')}}" 
															   style="text-transform:uppercase"
															   value="{{isset($doctor) ? $doctor->first_name : old('first_name')}}">
														  
													</div>
													<div class="form-group col-md-4">
															<label for="middle_name" class="label-size">{{__('Middle Name')}}</label>
															 <input id="middle_name" class="form-control" type="text" name="middle_name" style=" text-transform: uppercase;"
															   placeholder="{{__('NEW')}}" 
															   style="text-transform:uppercase"
															   value="{{isset($doctor) ? $doctor->middle_name : old('middle_name')}}">
														  
													</div>
													<div class="form-group col-md-4">
															<label for="last_name" class="label-size">{{__('Last Name').'*'}}</label>
															 <input id="last_name" class="form-control" type="text" name="last_name" style=" text-transform: uppercase;"
															   placeholder="{{__('NEW')}}" 
															   style="text-transform:uppercase"
															   value="{{isset($doctor) ? $doctor->last_name : old('last_name')}}">
														  
													</div>
																										
													<div class="form-group col-md-2">
															<label for="code" class="label-size">{{__('Code')}}</label>
															 <input id="code" class="form-control" type="text" name="code"
															   value="{{isset($doctor) ? $doctor->code : old('code')}}">
														  
													</div>
													
													   <div class="col-md-4">
														  <label for="specia" class="label-size">{{__('Category')}}</label>
														  <select  id="specia" name="specia" class="form-control">
															<option value="0">{{__('Choose a Speciality')}}</option>
															@foreach($specias as $c)
															<option value="{{$c->id}}" {{isset($doctor) && isset($doctor->specia) && $doctor->specia==$c->id?'selected':''}}>{{$c->name}}</option>
															
															@endforeach
														  </select>
														</div>	
													<!--<div class="form-group col-md-2">
															<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
															 <input id="pricel" class="form-control" type="text" name="pricel" 
															    value="{{isset($doctor) ? $doctor->pricel : old('pricel')}}" onkeypress="return isNumberKey(event)" 
																onfocusout="var rated = '{{$lbl_usd}}'; var ratee = '{{$lbl_euro}}'; var price =$(this).val(); 
																$('#priced').val(parseFloat(price/rated).toFixed(2));$('#pricee').val(parseFloat(price/ratee).toFixed(2));"/>
														  
													</div>		
													<div class="form-group col-md-2">
															<label for="priced" class="label-size">{{__('Price$')}}</label>
															 <input id="priced" class="form-control" type="text" name="priced" 
															    value="{{isset($doctor) ? $doctor->priced : old('priced')}}" onkeypress="return isNumberKey(event)"/>
														  
													</div>	
													
													<div class="form-group col-md-2">
															<label for="pricee" class="label-size">{{__('Price€')}}</label>
															 <input id="pricee" class="form-control" type="text" name="pricee" 
															    value="{{isset($doctor) ? $doctor->pricee : old('pricee')}}" onkeypress="return isNumberKey(event)"/>
														  
													</div>-->	
													<div class="form-group col-md-3">
														<label for="gender" class="label-size">{{__("Gender")}}</label>
														<select name="gender" class="custom-select rounded-0">
														<option value="">{{__("Unkown")}}</option>
														<option value="H" {{isset($doctor) && ($doctor->gender =='H' )? 'selected' : '' }}>{{__("Male")}}</option>
														<option value="F" {{isset($doctor) && ($doctor->gender =='F' )? 'selected' : '' }}>{{__("Female")}}</option>
														</select>
												   </div>
																				
												   <div class="form-group col-md-3">
															<label for="tel" class="label-size">{{__('Phone')}}</label>
															
															<input class="telephone form-control" type="text" name="tel"
															   value="{{isset($doctor) ? $doctor->tel : old('tel')}}">											
														    
													</div>
													<div class="form-group col-md-3">
															<label for="tel2" class="label-size">{{__('Phone2')}}</label>
															
															<input class="form-control" type="text" name="tel2"
															   value="{{isset($doctor) ? $doctor->tel2 : old('tel2')}}">											
														     
													</div>
													<div class="form-group col-md-3">
															<label for="tel3" class="label-size">{{__('Phone3')}}</label>
															
															<input class="form-control" type="text" name="tel3"
															   value="{{isset($doctor) ? $doctor->tel3 : old('tel3')}}">											
														   
													</div>
												   <div class="form-group col-md-3">
															<label for="fax" class="label-size">{{__('Fax')}}</label>
															
															<input class="telephone form-control" type="text" name="fax"
															   value="{{isset($doctor) ? $doctor->fax : old('fax')}}">											
														    
													</div>
													<div class="form-group col-md-3">
															<label for="email" class="label-size">{{__('Email')}}</label>
															 <input id="email" class="form-control" type="text" name="email"
															   value="{{isset($doctor) ? $doctor->email : old('email')}}">
														  
													</div>
													<div class="form-group col-md-6">
															<label for="address" class="label-size">{{__('Address')}}</label>
															 <input id="address" class="form-control" type="text" name="address"
															   value="{{isset($doctor) ? $doctor->address : old('address')}}">
														  
													</div>
													<div class="form-group col-md-3">
															<label for="city" class="label-size">{{__('City Name')}}</label>
															 <input id="city" class="form-control" type="text" name="city"
															   value="{{isset($doctor) ? $doctor->city : old('city')}}">
														  
													</div>
													
													<div class="form-group col-md-3">
															<label for="appt_nb" class="label-size">{{__('Appt.#')}}</label>
															 <input id="appt_nb" class="form-control" type="text" name="appt_nb"
															   value="{{isset($doctor) ? $doctor->appt_nb : old('appt_nb')}}">
														  
													</div>
													
													
													<div class="form-group col-md-3">
															<label for="state" class="label-size">{{__('State')}}</label>
															 <input id="state" class="form-control" type="text" name="state"
															   value="{{isset($doctor) ? $doctor->state : old('state')}}">
														  
													</div>
													<div class="form-group col-md-3">
															<label for="zip_code" class="label-size">{{__('Zip Code')}}</label>
															 <input id="zip_code" class="form-control" type="text" name="zip_code"
															   value="{{isset($doctor) ? $doctor->zip_code : old('zip_code')}}">
														  
													</div>
																										
													<div class="form-group col-md-6">
															<label for="remarks" class="label-size">{{__('Remarks')}}</label>
															<textarea class="form-control" name="remarks">{{isset($doctor)?$doctor->remarks:old('remarks')}}</textarea>
													</div>
												</div>
										   </div>
								   
										  
									 </div>
									 </form>
								   <!--end::Form-->
								</div><!--end doctor profile-->			
                               									 
							   </div>
                              </div>
       
<!-- end:: Content Container-->
		   			
@endsection

@section('scripts')
 @if ($errors->has('code'))
	  <script>
	   Swal.fire({
				  "html":"{{$errors->first('code')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('first_name'))
	  <script>
	   Swal.fire({
				  "html":"{{$errors->first('first_name')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('last_name'))
	  <script>
	  Swal.fire({
				 "html":"{{$errors->first('last_name')}}",
				 "icon":"error",
				 "customClass": "w-auto"});
	  </script>
  @elseif ($errors->has('pricel'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('pricel')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('priced'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('priced')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
 @elseif ($errors->has('pricee'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('pricee')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>	  
 @elseif ($errors->has('email'))

	  <script>
	     Swal.fire({ 
				  "html":"{{$errors->first('email')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
   @elseif ($errors->has('tel'))

	  <script>
	     Swal.fire({ 
				  "html":"{{$errors->first('tel')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
	   @elseif ($errors->has('tel2'))

	  <script>
	     Swal.fire({ 
				  "html":"{{$errors->first('tel2')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
	   @elseif ($errors->has('tel3'))

	  <script>
	     Swal.fire({ 
				  "html":"{{$errors->first('tel3')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
  @elseif ($errors->has('fax'))

	  <script>
	     Swal.fire({ 
				  "html":"{{$errors->first('fax')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>	  
  @endif

 
<script>
 $(document).ready(function() {
     		$('.select2').select2();
			$('[name=email]').inputmask("email");
			$('[name=user_email]').inputmask("email");
			
			/*flatpickr('#date', {
                allowInput : true,
                dateFormat: "Y-m-d",
            });*/
            
			var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
			
			$('.telephone').inputmask({ 
				mask: phones, 
				greedy: false, 
				definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
				
			
			
					
						
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

