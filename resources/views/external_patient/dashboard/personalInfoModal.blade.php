<!-- Modal -->
<div class="modal fade" id="personalInfoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header p-1">
        <h5 class="modal-title" id="personalInfoModal">{{__('Patient Info')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-1">
           
  <!-- begin:: Content -->
    <div class="container-fluid">
        
        
		<!-- begin:: Content Container-->
           <div class="card card-outline">
                       
                     <div class="card-body p-0">
                       
                         	 
								<div class="row  m-1" style="border:2px;border-style: double;border-color: #d9e2ef;">
								    <div class="col-md-4"> 
                                                           
                                         <div class="form-group">
												<label class="label-size">{{__('Lab Name')}}</label>
                                                <div class="input-group">
                                                    <input type="hidden" name="clinic_num" value="{{$clinic->id}}"/>
													<div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="fa fa-heading"></i></span></div>
                                                    <input type="text" class="form-control" value="{{$clinic->full_name}}" disabled />
                                                </div>
                                                                             
                                           </div>    
                                      </div>
									            
				                       <div class="col-md-4 col-6">
                                          	<div class="form-group">
												<label class="label-size">{{__('Gender')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-venus-mars"></i></span></div>
													<select class="custom-select rounded-0" name="gender" id="gender" disabled>
														<option value="">{{__('Select a gender')}}</option>
														
														<option
															value="M" {{isset($patient) && $patient->sex == 'M' ? 'selected' : ''}}>
																{{__('Male')}}
														</option>
														<option
															value="F" {{isset($patient) && $patient->sex == 'F' ? 'selected' : ''}}>
																{{__('Female')}}
														</option>
														
													</select>
												</div>
												
											</div>			  
										</div>
										<div class="col-md-4 col-6">
										   <div class="form-group">
												<label class="label-size" style="white-space:break-spaces;">{{__('Birthdate')}}</label>
												<div class="input-group date">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
													<input type="text" class="form-control" readonly="" name="birthdate" id="birthdate"
														   value="{{isset($patient) ? $patient->birthdate : old('birthdate')}}" disabled >
												</div>
												
											</div>
                                      </div>
									   <div class="col-md-4" >     
											<div class="form-group">
												<label class="label-size">{{__('First Name')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<input id="first_name" class="form-control" type="text" name="first_name"  
														   value="{{isset($patient) ? $patient->first_name : old('first_name')}}" disabled>
														 
												</div>
													     
											</div>       
                                      </div>
									  <div class="col-md-4" >     
											<div class="form-group">
												<label class="label-size">{{__('Middle Name')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<input id="middle_name" class="form-control" type="text" name="middle_name"  
														   value="{{isset($patient) ? $patient->middle_name : old('middle_name')}}" disabled>
														 
												</div>
													    
											</div>       
                                      </div>
									  
									  <div class="col-md-4">
											<div class="form-group">
												<label class="label-size">{{__('Last Name')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<input id="last_name" class="form-control" type="text" name="last_name"  
														   value="{{isset($patient) ? $patient->last_name : old('last_name')}}" disabled>
												</div>
												       
											</div>
                                      </div>
									   
									  
									  <div class="col-md-4 col-6">
											<div class="form-group">
												<label class="label-size">{{__('Mother Name')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<input id="mother_name" class="form-control" type="text" name="mother_name"  
														   value="{{isset($patient) ? $patient->mother_name : old('mother_name')}}" disabled>
												</div>
												   
											</div>
                                      </div>
									   <div class="col-md-4 col-6">
											<div class="form-group">
												<label class="label-size">{{__('Father Name')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<input id="father_name" class="form-control" type="text" name="father_name"  
														   value="{{isset($patient) ? $patient->father_name : old('father_name')}}" disabled>
												</div>
												   
											</div>
                                      </div>
									 
									  <div class="col-md-4 col-6">
											<div class="form-group">
												<label class="label-size">{{__('Referred by')}}</label>
												<div class="input-group">
												  <input type="text"  name="referred_by" class="form-control" value="{{isset($patient) ? $patient->referred_by : old('referred_by')}}" disabled />
												</div>
												   
											</div>
                                      </div>
									  <div class="col-md-4 col-6">
											<div class="form-group">
												<label class="label-size">{{__('External lab/doctor')}}</label>
												<div class="input-group">
													<select name="ext_lab" class="select2_data custom-select rounded-0" style="width:100%;" disabled>
												     @foreach($ext_labs as $lab)
													   <option value="{{$lab->user_num}}" {{isset($patient) && isset($patient->ext_lab) && $patient->ext_lab==$lab->user_num?'selected':''}}>{{$lab->full_name}}</option>
													 @endforeach
												</select>
												</div>
												   
											</div>
                                      </div>                      
									  <div class="col-md-4 col-6">       
										<div class="form-group">
											<label class="label-size">{{__('Primary insurance')}}</label>
											<div class="input-group">
												<select name="first_ins" class="select2_data custom-select rounded-0" style="width:100%;" disabled >
												  <option value="">Choose an insurance</option>
												  @foreach($insurance as $ins)
												   <option value="{{$ins->id}}" {{isset($patient) && isset($patient->first_ins) && $patient->first_ins==$ins->id?'selected':''}}>{{$ins->full_name}}</option>
												  @endforeach
												</select>
											</div>
										</div>
                                    </div>
									
									<div class="col-md-4 col-6">       
										<div class="form-group">
											<label class="label-size">{{__('Secondary insurance')}}</label>
											<div class="input-group">
												<select name="second_ins" class="select2_data custom-select rounded-0" style="width:100%;" disabled>
												  <option value="">Choose an insurance</option>
												  @foreach($insurance as $ins)
												   <option value="{{$ins->id}}" {{isset($patient) && isset($patient->second_ins) && $patient->second_ins==$ins->id?'selected':''}}>{{$ins->full_name}}</option>
												  @endforeach
												</select>
											</div>
										</div>
                                    </div>
									   <div class="col-md-4">       
										<div class="form-group">
											<label class="label-size">{{__('Email')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text">@</span></div>
												<input id="email" class="form-control" type="text" name="email"
													   value="{{isset($patient) ? $patient->email : old('email')}}" disabled>
											</div>
										</div>
                    
                                       </div> 									   
									   
									 <div class="col-md-4 col-6">           
										
											<label class="label-size" style="white-space:break-spaces;">{{__('Landline Phone')}}</span></label>
											
												<input id="home_phone" class="form-control" type="text" name="home_phone"  
													   value="{{isset($patient) ? $patient->first_phone : old('home_phone')}}" disabled>
											
											
										
                                      </div>
																	  								     
									<div class="col-md-4 col-6">
											
												<label class="label-size" style="white-space:break-spaces;">{{__('Cell phone')}}</label>
												
													<input id="cell_phone" class="form-control" type="text" name="cell_phone" 
														   value="{{isset($patient) ? $patient->cell_phone : old('cell_phone')}}" disabled>
												
									</div>
								   
									  <div class="col-md-4 col-6">
										 <div class="d-flex flex-column">
											 <label for="name"><span class="label-size inputLabel" style="white-space:break-spaces;">{{__("Authorize sending by")}}</span></label>
																				 
											 <div class="form-inline">
												 <label class="ml-1 mr-1" style="font-size:16px;">{{__("E-mail")}}</label>
												 <label class="slideon slideon-xs  slideon-success">
												 <input type="checkbox"  disabled {{(isset($patient) && $patient->receive_mail)?'checked':''}}  style="height:22px;width:22px;" name="chkEmail"/>
												 <span class="slideon-slider"></span></label>
												 
												 <label class="ml-1" style="font-size:16px;">{{__("SMS")}}</label>
												 <label class="slideon slideon-xs  slideon-success">
												 <input type="checkbox"  disabled {{(isset($patient) && $patient->receive_sms)?'checked':''}}  style="height:22px;width:22px;" name="chkSms"/>
												  <span class="slideon-slider"></span></label>
												 
											 </div>
										 </div>
								      </div>
									  
										<div class="col-md-4 col-6">
                                          	<div class="form-group">
												<label class="label-size">{{__('Blood group')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-venus-mars"></i></span></div>
													<select class="custom-select rounded-0" name="blood_group" id="blood_group" disabled>
														<option value="">{{__('Select a blood group')}}</option>
														<option value="A+" {{isset($patient) && $patient->blood_group == 'A+' ? 'selected' : ''}}>A+</option>
														<option value="A-" {{isset($patient) && $patient->blood_group == 'A-' ? 'selected' : ''}}>A-</option>
														<option value="B+" {{isset($patient) && $patient->blood_group == 'B+' ? 'selected' : ''}}>B+</option>
														<option value="B-" {{isset($patient) && $patient->blood_group == 'B-' ? 'selected' : ''}}>B-</option>
														<option value="AB+" {{isset($patient) && $patient->blood_group == 'AB+' ? 'selected' : ''}}>AB+</option>
														<option value="AB-" {{isset($patient) && $patient->blood_group == 'AB-' ? 'selected' : ''}}>AB-</option>
														<option value="O+" {{isset($patient) && $patient->blood_group == 'O+' ? 'selected' : ''}}>O+</option>
														<option value="O-" {{isset($patient) && $patient->blood_group == 'O-' ? 'selected' : ''}}>O-</option>
													</select>
												</div>
											</div>			  
										</div>
									  <div class="col-md-4 col-6">
										   <div class="form-group">
												<label class="label-size" style="white-space:break-spaces;">{{__('Marital Status')}}</label>
												<div class="input-group date">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<select name="marital_status" class="custom-select rounded-0" disabled>
													  <option value="0">{{__('Undefined')}}</option>
													  <option value="S" {{(isset($patient) && $patient->marital_status=='S')?'selected':''}}>{{__('Single')}}</option>
													  <option value="M" {{(isset($patient) && $patient->marital_status=='M')?'selected':''}}>{{__('Married')}}</option>
													  <option value="D" {{(isset($patient) && $patient->marital_status=='D')?'selected':''}}>{{__('Divorced')}}</option>
													  <option value="W" {{(isset($patient) && $patient->marital_status=='W')?'selected':''}}>{{__('Widowed')}}</option>
													</select>
												</div>
											</div>
                                      </div>
									  
									  <div class="col-md-5">       
										<div class="form-group">
											<label class="label-size">{{__('Address')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i
															class="fa fa-location-arrow"></i></span></div>
												<textarea class="form-control" name="address" id="address" cols="10" rows="1" readonly="true">{{isset($patient)? $patient->addresse : old('address')}}</textarea>
											</div>
										</div>
                                     </div>
									 <div class="col-md-3 col-6">       
										<div class="form-group">
											<label class="label-size">{{__('Appt.#')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
												<input id="appt_nb" class="form-control" type="text" name="appt_nb"
													   value="{{isset($patient) ? $patient->appt_nb : old('appt_nb')}}" disabled>
											</div>
										</div>
                                    </div>
									
									<div class="col-md-4 col-6">       
                                         <div class="form-group">
											<label class="label-size">{{__('City')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
												<input id="city" class="form-control" type="text" name="city"
													   value="{{isset($patient) ? $patient->city : old('city')}}" disabled>
											</div>
												   
										 </div>
                                     </div>
									 <div class="col-md-4 col-6">       
										<div class="form-group">
											<label class="label-size">{{__('State')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
												<input id="state" class="form-control" type="text" name="state"
													   value="{{isset($patient) ? $patient->state : old('state')}}" disabled>
											</div>
										</div>
                                    </div>
									
									 <div class="col-md-4 col-6">       
										<div class="form-group">
											<label class="label-size">{{__('Zip code')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
												<input id="codepostale" class="form-control" type="text" name="codepostale"
													   value="{{isset($patient) ? $patient->codepostale : old('codepostale')}}" disabled>
											</div>
										</div>
                                    </div>
								
                                   
                            </div>  
                           
							
                        </form>
                        <!--end::Form-->
                    </div>
        </div>
        <!-- end:: Content Container-->
    </div>
    <!-- begin:: Content -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-delete" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>