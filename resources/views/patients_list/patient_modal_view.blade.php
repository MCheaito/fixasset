  <!-- begin:: Content -->
                    <div class="container-fluid">
        		    <!-- begin:: Content Container-->
          			 <form id="patient_form">
								<div class="row m-1">
									<div class="col-md-2"><span class="badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span></div>
									<div class="col-md-4"> 
                                                           
                                         <div class="form-group">
                                                <div class="input-group">
                                                    <input type="hidden" name="clinic_num" value="{{$clinic->id}}"/>
													<div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="fa fa-heading"></i></span></div>
                                                    <input type="text" class="form-control" value="{{$clinic->full_name}}" disabled/>
                                                </div>
                                                @error('clinic_num')
                                                <div class="alert alert-danger">{{__('Please choose a lab')}}</div>
                                                @enderror                                
                                           </div>   
                                    </div>
									
									
									  @if(isset($patient)) 
									   	<div class="col-md-3">	   
										     <div class="form-group">
														<div class="input-group">
															<div class="input-group-prepend" style="font-size:1rem;"><span class="input-group-text"><b>{{__('File Nb.')}}</b></span></div>
															<input type="text" id="file_nb" class="form-control" name="file_nb" readonly="true" value="{{isset($patient) ? $patient->file_nb: old('id_patient')}}"/>
															<input id="id_patient" class="form-control" type="hidden" name="id_patient"
																value="{{isset($patient) ? $patient->id : old('id_patient')}}" readonly="true">

														</div>
																					
												</div>
										</div>									
										@endif					
																		
									<div class="col-md-3 text-right">
										 <button type="button" id="update_patient" class="btn  btn-action ">{{__('Save')}}</button>
										 <input type="hidden" id="req_type" name="req_type"/> 
										 <button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
									</div>
                                </div>
								<div class="row mt-2 m-1" style="border:2px;border-style: double;border-color: #d9e2ef;">
								     <div class="col-md-2">
									     <label class="label-size">{{__('Title')}}</label>
										 <select name="title" id="title" class="select2_title form-control"> 
										  <option value="">{{__('Choose')}}</option>
										  @foreach($titles as $t)
										   <option value="{{$t->id}}" {{isset($patient)&& isset($patient->title) && $patient->title==$t->id?'selected':''}}>{{$t->name}}</option>
										  @endforeach
									     </select>
									  </div>
									  <div class="col-md-4" >     
											<div class="form-group">
												<label class="label-size">{{__('First Name').'*'}}</label>
												<div class="input-group">
													<input id="first_name" class="form-control" type="text" name="first_name"  
														   style="text-transform:uppercase"
														   value="{{isset($patient) ? $patient->first_name : old('first_name')}}">
														 
												</div>
												
											</div>       
                                      </div>
									  <div class="col-md-3" >     
											<div class="form-group">
												<label class="label-size">{{__('Middle Name')}}</label>
												<div class="input-group">
													<input id="middle_name" class="form-control" type="text" name="middle_name"  
														   style="text-transform:uppercase"
														   value="{{isset($patient) ? $patient->middle_name : old('middle_name')}}">
														 
												</div>
													     
											</div>       
                                      </div>
									  
									  <div class="col-md-3">
											<div class="form-group">
												<label class="label-size">{{__('Last Name').'*'}}</label>
												<div class="input-group">
													<input id="last_name" class="form-control" type="text" name="last_name"  
														   style="text-transform:uppercase"
														   value="{{isset($patient) ? $patient->last_name : old('last_name')}}">
												</div>
												   
											</div>
                                      </div>
									
									            
				                       <div class="col-md-2 col-6">
                                          	<div class="form-group">
												<label class="label-size">{{__('Gender').'*'}}</label>
												<div class="input-group">
													<select class="custom-select rounded-0" name="gender" id="gender">
														<option value="">{{__('Choose')}}</option>
														
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
										<div class="col-md-2 col-6">
										   <div class="form-group">
												<label class="label-size" style="white-space:break-spaces;">{{__('Birthdate').'*'}}</label>
												<div class="input-group date">
													<input type="text" class="form-control" readonly="" name="birthdate" id="birthdate"
														   value="{{isset($patient) ? $patient->birthdate : old('birthdate')}}">
												</div>
												
											</div>
                                      </div>
									  <div class="col-md-4">
											<div class="form-group">
												<label class="label-size">{{__('Guarantor')}}</label>
												<div class="input-group">
													@php $class=(auth()->user()->type==2)?'select2_data':'';@endphp
													<select name="ext_lab" class="{{$class}} custom-select rounded-0" style="width:100%;">
												     @if(auth()->user()->type==2)
								                           <option value="">{{__('Choose')}}</option>
							                         @endif 
													 @foreach($ext_labs as $lab)
													   @php 
													    $selected = '';
														if(isset($patient) && isset($patient->ext_lab) && $patient->ext_lab==$lab->id){
															$selected = 'selected';
														}
													   @endphp
													   <option value="{{$lab->id}}" {{$selected}}>{{$lab->full_name}}</option>
													 @endforeach
												</select>
												</div>
												   
											</div>
                                      </div> 
									  <div class="col-md-4">
											<div class="form-group">
												<label class="label-size">{{__('Doctor')}}</label>
												<div class="input-group">
													<select name="doctor_num" class="select2_data custom-select rounded-0" style="width:100%;">
												     @if(auth()->user()->type!=1)
												     <option value="">{{__('Choose a doctor')}}</option>
												     @endif
													 @foreach($doctors as $doc)
													   <option value="{{$doc->id}}" {{isset($patient) && isset($patient->doctor_num) && $patient->doctor_num==$lab->id?'selected':''}}>{{$doc->full_name}}</option>
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
													   value="{{isset($patient) ? $patient->email : old('email')}}">
											</div>
										</div>
                    
                                       </div> 
									   <div class="col-md-4">
											
												<label class="label-size" style="white-space:break-spaces;">{{__('Cellular Phone')}}</label>
												
													<input id="cell_phone" class="form-control" type="text" name="cell_phone" 
														   value="{{isset($patient) ? $patient->cell_phone : old('cell_phone')}}">
												
									  </div> 
									
									<div class="col-md-4">           
										
											<label class="label-size" style="white-space:break-spaces;">{{__('Other Phone')}}</span></label>
											
												<input id="home_phone" class="form-control" type="text" name="home_phone"  
													   value="{{isset($patient) ? $patient->first_phone : old('home_phone')}}">
											
											
										
                                      </div>
									   <div class="col-md-3">
											<div class="form-group">
												<label class="label-size">{{__('Passport Number')}}</label>
												<div class="input-group">
												  <input type="text"  name="passport_nb" class="form-control" value="{{isset($patient) ? $patient->passport_nb : old('passport_nb')}}"/>
												</div>
												   
											</div>
                                      </div> 
									   
									   <div class="col-md-4">
											<div class="form-group">
												<label class="label-size">{{__('Husband/Partner Name')}}</label>
												<div class="input-group">
												  <input type="text"  name="husband_name" class="form-control" value="{{isset($patient) ? $patient->husband_name : old('husband_name')}}"/>
												</div>
												   
											</div>
                                      </div>
									 			 
									 <div class="col-md-5">       
										<div class="form-group">
											<label class="label-size">{{__('Address')}}</label>
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i
															class="fa fa-location-arrow"></i></span></div>
												<textarea class="form-control" name="address" id="address">{{isset($patient)? $patient->addresse : old('address')}}</textarea>
											</div>
										</div>
                                     </div>
									 
									
								
                            </div>  
                           
							
                        </form><!--end::Form-->
                   
                  </div>