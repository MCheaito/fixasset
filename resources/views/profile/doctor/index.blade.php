<!-- 
 DEV APP
 Created date : 6-10-2022
-->
@extends("gui.main_gui")
@section("styles")
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
    <link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet"> 
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
   

	<style>
	   
.kbw-signature { 
	margin:0;
	float: left; 
	width: 100%; 
	height: 200px; 
    border: 2px solid #1bbc9b !important;
	}



    </style>

@endsection
@section("content")
   
   <div class="container-fluid">
		 <div class="row">   
		   
         <div class="col-md-12"> 
			   <div class="card">
			      <div class="card-header card-menu">
					      <div class="card-tools">
									<button type="button" class="m-1 btn btn-resize btn-sm" title="{{__('Show/Collapse')}}" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
								    </button>
									
				            </div>
						<div class="card-title">
							
							<ul class="nav nav-pills nav-pills">
							<li class="nav-item"><a class="nav-link" href="#persos" data-toggle="tab">{{__("General Information")}}</a></li>
							<li class="nav-item"><a class="nav-link" href="#account" data-toggle="tab">{{__("Account Information")}}</a></li>
							<li class="nav-item"><a class="nav-link" href="#signature" data-toggle="tab">{{__("Signature Information")}}</a></li>

							</ul>
						</div>
                  </div>
			   
			  
			   <div class="card-body p-0">
			       <div class="tab-content">
						  <div class="tab-pane" id="persos">
                                <form class="form-horizontal" action="{{route('profiles.doctor.update',[app()->getLocale(),$docUser->id])}}"  method="POST">
										@csrf
										@method('PUT')	 
							   <!--update profile information-->
								  <div class="row m-1">
									<div class="col-md-12">	
								     <input type="hidden" name="update_persos"/>
									 <button type="submit" class="m-1 float-right btn  btn-action">{{__('Save')}}</button>
								   </div>
								   
									<div class="form-group col-md-4">
										<label for="first_name" class="label-size"> {{__("First Name").'*'}}</label>
										<input type="text" class="form-control" autoComplete="false" style=" text-transform: uppercase;" name="first_name" value="{{$docUser->first_name}}" />
									</div>
									<div class="form-group col-md-4">
											<label for="middle_name" class="label-size">{{__('Middle Name')}}</label>
											<input id="middle_name" class="form-control" type="text" name="middle_name" style=" text-transform: uppercase;"
															   value="{{$docUser->middle_name}}">
														  
									</div>
									<div class="form-group col-md-4">
										<label  for="last_name" class="label-size">{{__("Last Name").'*'}}</label>
										 <input type="text" class="form-control" autoComplete="false" style=" text-transform: uppercase;" name="last_name" value="{{$docUser->last_name}}" />
									</div>
									<div class="form-group col-md-2">
															<label for="code" class="label-size">{{__('Code').'*'}}</label>
															 <input id="code" class="form-control" type="text" name="code"
															   value="{{$docUser->code}}">
														  
													</div>
													
													
													
													<div class="form-group col-md-4">
															<label for="specia" class="label-size">{{__('Speciality')}}</label>
															 <input id="specia" class="form-control" type="text" name="specia" style=" text-transform: uppercase;"
															   value="{{$docUser->specia}}">
														  
													</div>
									<div class="form-group col-md-2">
															<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
															 <input id="pricel" class="form-control" type="text" name="pricel" 
															    value="{{$docUser->pricel}}">
									</div>		
									<div class="form-group col-md-2">
															<label for="priced" class="label-size">{{__('Price$')}}</label>
															 <input id="priced" class="form-control" type="text" name="priced" 
															    value="{{$docUser->priced}}">
									</div>				
									<div class="form-group col-md-2">
															<label for="pricee" class="label-size">{{__('Price€')}}</label>
															 <input id="pricee" class="form-control" type="text" name="pricee" 
															    value="{{$docUser->pricee}}">
									</div>
									<div class="form-group col-md-3">
														<label for="gender" class="label-size">{{__("Gender")}}</label>
														<select name="gender" class="custom-select rounded-0">
														<option value="" {{($docUser->gender =='' )?   'selected'  : '' }}>{{__("Undefined")}}</option>
														<option value="H" {{($docUser->gender =='H' )? 'selected' : '' }}>{{__("Male")}}</option>
														<option value="F" {{($docUser->gender =='F' )? 'selected' : '' }}>{{__("Female")}}</option>
														</select>
												    </div>
									<div class="form-group col-md-3">
										<label for="tel" class="label-size">{{__("Phone")}}</label>
										
										<input type="text" class="form-control" name="tel" value="{{$docUser->tel}}" />
									   
									</div>
									<div class="form-group col-md-3">
										<label for="tel2" class="label-size">{{__("Phone2")}}</label>
										
										<input type="text" class="form-control" name="tel2" value="{{$docUser->tel2}}" />
									  
									</div>
									<div class="form-group col-md-3">
										<label for="tel3" class="label-size">{{__("Phone3")}}</label>
										
										<input type="text" class="form-control" name="tel3" value="{{$docUser->tel3}}" />
									    
									</div>
									<div class="form-group col-md-3">
										<label  for="fax" class="label-size">{{__("Fax Nb")}}</label>
										
										<input type="text" class="form-control" name="fax" value="{{$docUser->fax}}" />
									  
									</div>
									<div class="form-group col-md-3">
										<label  for="email" class="label-size">{{__("Email")}}</label>
										<input type="text" class="form-control" name="email" value="{{isset($docUser->email)?$docUser->email:$docUser->user_email}}" />
									</div>
									
									<div class="form-group col-md-6">
															<label for="address" class="label-size">{{__('Address')}}</label>
															 <input id="address" class="form-control" type="text" name="address"
															   value="{{$docUser->address}}">
														  
													</div>
									<div class="form-group col-md-3">
															<label for="city" class="label-size">{{__('City')}}</label>
															 <input id="city" class="form-control" type="text" name="city"
															   value="{{$docUser->city}}">
														  
													</div>
									
									               
													<div class="form-group col-md-3">
															<label for="appt_nb" class="label-size">{{__('Appt.#')}}</label>
															 <input id="appt_nb" class="form-control" type="text" name="appt_nb"
															   value="{{$docUser->appt_nb}}">
														  
													</div>
													
													<div class="form-group col-md-3">
															<label for="state" class="label-size">{{__('State')}}</label>
															 <input id="state" class="form-control" type="text" name="state"
															   value="{{$docUser->state}}">
														  
													</div>
													<div class="form-group col-md-3">
															<label for="zip_code" class="label-size">{{__('Zip Code')}}</label>
															 <input id="zip_code" class="form-control" type="text" name="zip_code"
															   value="{{$docUser->zip_code}}">
														  
													</div>
									
									 <div class="form-group col-md-7">
										<label  for="remarks" class="label-size">{{__("Remarks")}}</label>
										<textarea class="form-control" name="remarks">{{$docUser->remarks}}</textarea>
									</div>
									
								   </div>
								   
                                  </form>
						  </div>
						   <div class="tab-pane" id="account">
						          <form action="{{ route('profiles.doctor.password_change',app()->getLocale())}}" method="post" enctype="multipart/form-data"> 
								   @csrf
								   <div class="row m-1">
								     	<div class="form-group col-md-4"></div>
										<div class="form-group col-md-4">
										 <label class="label-size" for="name">{{__('Profile image')}}</label>
											<div>
											   <img id="profileImg" class="profile-user-img img-circle img-fluid  elevation-3" src="{{ isset($imgProfile) ? $imgProfile->path : '/public/custom/default_profile_photo/noimage.png' }}" style="width:80px;height:80px;"/> 
											   <label class="mt-2 ml-2 btn btn-sm btn-action" title="{{__('Choose an image')}}">{{__('Upload')}}<i class="ml-1 fa fa-upload"></i>
											    <input type="file" name="image" accept="image/*" placeholder="Choose image" id="profile" style="display:none;">
											   </label> 
											</div>  
											
									   </div>
										<div class="col-md-4 text-right">
											<button type="submit"  class="m-1 btn btn-action">{{__("Save")}}</button>
										</div>
							       </div>
                                   							   
								  	 
									  <div class="row m-1">						  					   
									  <div class="form-group col-md-4">
											<label  for="profileProfession" class="label-size">
												{{__("Speciality")}}
											</label>
											<input type="text" class="form-control" autoComplete="false" name="profileProfession" readonly="true" value="{{$docUser->specia}}" />
										</div>
										<div class="form-group col-md-4">
											<label for="profileUser" class="label-size">
												{{__("First Name")}}
											</label>
											<input type="text" class="form-control" autoComplete="false" name="user_fname"  value="{{$docUser->user_fname}}" />
										</div>
										<div class="form-group col-md-4">
											<label for="profileUser" class="label-size">
												{{__("Last Name")}}
											</label>
											<input type="text" class="form-control" autoComplete="false" name="user_lname"  value="{{$docUser->user_lname}}" />
										</div>

										<div class="form-group col-md-4">
											<label for="profileUser" class="label-size">
												{{__("User Name")}}
											</label>
											<input type="text" class="form-control" autoComplete="false" name="profileUser"  value="{{$docUser->username}}" />
										</div>
										
										<div class="form-group col-md-4">
										  <label for="password" class="label-size">{{ __('Password') }}</label>
										  <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
										</div>
										<div class="form-group col-md-4">
									       <label for="password-confirm" class="label-size">{{ __('Confirm Password') }}</label>
									        <input id="password-confirm" type="password" class="form-control"  name="password_confirmation"  autocomplete="new-password">
									    </div>
									</div>
                                  </form>			
						   </div>
   
						  
						   <div class="tab-pane" id="signature">
						       <form method="POST" action="{{ route('profiles.doctor.upload_signature',app()->getLocale()) }}">
									@csrf
									   <div class="row m-1">
										 <div class="col-md-12 text-right">
										  <button type="submit" class="m-1  btn btn-action">{{__('Save')}}</button>
                                        </div> 
									   </div>
									   <div class="row m-1">
									     <div class="form-group  col-md-4 col-10">
											 <label class="col-12 label-size" for="name">{{__('Draw your signature')}}: <span class="ml-2"> <button id="clear" class="float-right btn-sm btn btn-delete">{{__('Clear')}}</button></span> </label>
												<br/>
												<div id="sig" class="kbw-signature"></div>
												<br/>
												<div class="mt-2 mb-2">
										            <textarea id="doc_sig" name="signed" style="display: none"></textarea>
										        </div>
										 </div>
										 @if(isset($docSignature))
									   	
										<div class="mt-5 form-group  col-md-5">
										  <table class="table table-bordered table-sm w-auto txt-border">
										    <tbody>
											   <tr>
											     <th>{{__("Add my signature on the prescriptions")}}</th>
												 <th>{{__("Authorize the secretary to add my signature")}}</th>
											   </tr>
											   <tr>
											     <td><label class="slideon slideon-xs  slideon-success"><input  type="checkbox"   class="form-check-input" name="chkSignature" style="width:1em;height:1em;" {{($docUser->show_sign=='false')? '' : 'checked' }}/><span class="slideon-slider"></span></label></td>
											  	 <td><label class="ml-2 slideon slideon-xs  slideon-success"><input  type="checkbox"   class="form-check-input" name="chkClinAut" style="width:1em;height:1em;" {{($docUser->show_sign_for_clinic=='false')? '' : 'checked' }} {{($docUser->show_sign=='false')? 'disabled' : '' }}/><span class="slideon-slider"></span></label></td>
											  </tr>
											</tbody>
										  </table>
										</div>
									@endif
										 
										 <div class="form-group col-md-3">
										  <label class="label-size" for="name">{{__('Signature display')}}:</label>
										  <img id="signature_num" class="mt-2 txt-border img-fluid" style="width:20em;height:8em;txt-border" src="@if(isset($docSignature)){{$docSignature->path}}@endif"/>
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

@section("scripts")

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
  @if ($errors->has('image'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('image')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('user_fname'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('user_fname')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('user_lname'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('user_lname')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('profileUser'))
  <script>
   Swal.fire({ 
              "html":"{{$errors->first('profileUser')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @elseif ($errors->has('password'))
  <script>
   Swal.fire({ 
              "text":"{{$errors->first('password')}}",
			  "icon":"error",
			  "customClass": "w-auto"});
  </script>
  @endif
 
  <script type="text/javascript">
        $(document).ready(function() {
			
			 $('a[data-toggle="tab"]').click(function (e) {
					e.preventDefault();
					$(this).tab('show');
				});

				$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
					var id = $(e.target).attr("href");
					localStorage.setItem('selectedTab', id)
				});

				var selectedTab = localStorage.getItem('selectedTab');
				//alert(selectedTab);
				if (selectedTab != null) {
					$('a[data-toggle="tab"][href="' + selectedTab + '"]').tab('show');
				}else{
					$('a[data-toggle="tab"][href="#persos"]').tab('show');
				}
			
			$('#PasswordModal-{{$docUser->id}}').on('hidden.bs.modal', function(e) {
			  $('.alert-danger-password').hide();
			
            });

			
			 $('input[name="chkSignature"]').click(function(){
            if($(this).prop("checked") == true){
                 $('input[name="chkClinAut"]').prop('disabled',false);
            }
            else if($(this).prop("checked") == false){
              $('input[name="chkClinAut"]').prop('disabled',true);
			  $('input[name="chkClinAut"]').prop('checked',false);
            }
            });
			
        });
</script>                  
 
    <script type="text/javascript" src="{{asset('dist/jquery.signature/touch/jquery.ui.touch-punch.min.js')}}"></script>
    <link type="text/css" href="{{asset('dist/jquery.signature/css/jquery.signature.css')}}" rel="stylesheet"> 
    <script type="text/javascript" src="{{asset('dist/jquery.signature/js/jquery.signature.js')}}"></script>
  
  <script type="text/javascript">

    	
	var sig = $('#sig').signature({syncField: '#doc_sig', syncFormat: 'PNG'});

    
	$('#clear').click(function(e) {

        e.preventDefault();

        sig.signature('clear');

        $("#doc_sig").val('');

        });
     
	 $('#profile').change(function(){
		 
		 let reader = new FileReader();
 
		 reader.onload = (e) => { 
	 
		  $('#profileImg').attr('src', e.target.result); 
		  }
 
        reader.readAsDataURL(this.files[0]); 
		 
	 });
  </script>
  <script>
	$(function(){
		/*flatpickr('#practice_date', {
					allowInput: true,
					altInput: true,
					altFormat: "Y-m-d",
					dateFormat: "Y-m-d",
					
				});*/
	$('[name=email]').inputmask("email");
	var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
    $('[name=tel],[name=tel2],[name=tel3],[name=fax]').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });

	});
 </script>
  <script>
	$(function(){
		$('#pricel').change(function(e){
			e.preventDefault();
			var price = $(this).val();
			var lbl_usd = '{{$lbl_usd}}';
			var lbl_euro='{{$lbl_euro}}';
			$('#priced').val(parseFloat(price/lbl_usd).toFixed(2));
			$('#pricee').val(parseFloat(price/lbl_euro).toFixed(2));
		});
		 
		$('#password_modal').click(function(e){
               e.preventDefault();
			   var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");   
			   $.ajax({
				 url: "{{ route('profiles.doctor.password_change',app()->getLocale())}}",
                 method: "POST",
				 dataType: "JSON",
                  data: {
                     password: $('#password').val(),
					 password_confirmation: $('#password-confirm').val(),
					 _token: CSRF_TOKEN
			
                    },
				  success: function(result){
                   if(result.errors)
                      {
	                   $('.alert-danger-password').html('');
						$.each(result.errors, function(key, value){
							$('.alert-danger-password').show();
							$('.alert-danger-password').append('<li>'+value+'</li>');
						});
                     }else{
	                       $('.alert-danger-password').hide();
						   $('#PasswordModal-{{$docUser->id}}').modal('hide');
						    Swal.fire({
								icon: 'success',
								toast: true,
								position: 'bottom-end',
								timer: 5000,
								showConfirmButton: false,
								title: result.success
								});
                           }

                       }

			   });
			});
	});
  </script>	
  
 
@endsection