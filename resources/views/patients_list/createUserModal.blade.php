<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{__("Create User")}}</h5>
		<div class="col-md-3">
		<p class="badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</p>
	    </div>		       
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
		   
      </div>
      <div class="modal-body"> 
	 
     	<div class="row m-1 mb-2"> 
			
					<div class="col-md-6">        				   
					<label class="label-size">{{__('Clinic').'*'}}</label>
                    <select class="custom-select rounded-0" name="clinic_num" id="clinic_num" >
                    <option  value="{{$clin->id}}" >{{$clin->full_name}}</option>
                    </select>         
					</div>
          <div class="col-md-6 text-center">
                                     <button type="button" id="create_user" class="btn btn-action">{{ __('Register') }}</button>
                                    
                            </div>
							                    
		</div> 
		   <div class="row m-1 mb-2">

                            <div class="col-md-6">
                                <label for="fname" class="label-size">{{ __('First name').'*' }}</label>

								<input id="fname" type="text" class="form-control " name="fname" value="{{isset($patient) ? $patient->first_name : ''}}"  autocomplete="fname" autofocus>

                               
                            </div>
                        

                            <div class="col-md-6">
                               <label for="lname" class="label-size">{{ __('Last name').'*' }}</label>
								<input id="lname" type="text" class="form-control " name="lname" value="{{isset($patient) ? $patient->last_name : ''}}"  autocomplete="lname" autofocus>

                                
                            </div>
            </div>
			 <div class="row m-1 mb-2">
								 <div class="col-md-6">	
									<label for="gender" class="label-size">{{ __('Birthdate').'*' }}</label>	
									<input type="text" class="form-control "  value="{{isset($patient) ? $patient->birthdate : ''}}" name="birthdate" id="birthdate" placeholder="YYYY-MM-DD">
								    
								</div>
								<div class="col-md-6">
								<label for="tel" class="label-size">{{ __('Cell phone').'*' }}</label>
									<input id="tel" type="text" class="form-control" name="tel" value="{{isset($patient) ? $patient->first_phone : ''}}"  autocomplete="tel" autofocus>
									
								</div>
						</div>
						<div class="row m-1 mb-2">
								<div class="col-md-6">	 			
											       <label for="gender" class="label-size">{{ __('Gender')}}</label>											
		
													<select class="custom-select rounded-0" name="gender" id="gender">
														<option value="">{{__('Select Gender')}}</option>
														
														<option
															value="M" {{isset($patient) && $patient->sex == 'M' ? 'selected' : ''}}>
																{{__('Male')}}
														</option>
														<option
															value="F" {{isset($patient) && $patient->sex == 'F' ? 'selected' : ''}}>
																{{__('Female')}}
														</option>
														<option	value="N" {{isset($patient) && $patient->sex == 'N' ? 'selected' : ''}}>
																{{__('Undefined')}}
														</option>
													</select>
								</div>
								 <div class="col-md-6">
                              <label for="ramq" class="label-size">{{ __('RAMQ') }}</label>

								<input id="ramq" type="text" class="form-control" name="ramq" value="{{isset($patient) ? $patient->ramq : ''}}" >

                                
                            </div>

                            
								 		
											
						</div>	
                        <div class="row m-1 mb-2">

                            <div class="col-md-6">
							<label for="email" class="label-size">{{ __('Email').'*' }}</label>
                                <input id="email" type="email" class="form-control " name="email" value="{{isset($patient) ? $patient->email : ''}}" autocomplete="email">

							
                            </div>
							<div class="col-md-6">
                                <label for="email" class="label-size">{{ __('Username').'*' }}</label>

								<input id="username" readonly="" type="text" class="form-control " name="username" value="{{isset($patient) ? $patient->email : ''}}"  autocomplete="username">

                                
                            </div>
                        </div>
						

                        <div class="row m-1 mb-2">

                            <div class="col-md-6">
                                <label for="password" class="label-size">{{ __('Password').'*' }}</label>

								<input id="password" type="password" class="form-control " name="password"  autocomplete="new-password">

                               
                            </div>
							 <div class="col-md-6">
                               <label for="password-confirm" class="label-size">{{ __('Confirm Password').'*' }}</label>
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                            </div>
						 </div>	
					
      </div>
     
	 <div class="modal-footer">
                          
      </div>
    </div>
  </div>
</div>