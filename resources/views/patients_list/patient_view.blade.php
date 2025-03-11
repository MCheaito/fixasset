
  <!-- begin:: Content -->
    <div class="container-fluid">
        
        
		<!-- begin:: Content Container-->
           <div class="card card-outline">
                        
						<div class="card-header  p-1">
							 <div class="row"> 
								<div class="col-md-10">
							      <h4>{{isset($patient) ? __('Edit Patient') : __('New Patient')}}</h4>
								</div>
							 </div>
						</div>	
					 
                     <div class="card-body p-0">
                       
                         	 
								 <form  id="patient_form" action="{{isset($patient) ? route('patientslist.update',[app()->getLocale(),$patient->id]) : route('patientslist.store',app()->getLocale())}}" method="POST"> 
									@csrf
									@if(isset($patient))
										@method('PUT')
									@endif
							
								@if($errors->has('msg'))
								<div class="alert alert-danger">
									{{ $errors->first('msg') }}
								</div>
							   @endif
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
									   	<div class="col-md-2">	   
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
									
									
									<div class="col-md-4 text-right">
										 @if(isset($patient))
										   @php 
									        $pat_name = $patient->first_name;
									            if(isset($patient->middle_name) && $patient->middle_name!=''){
												$pat_name.=' '.$patient->middle_name;	
												}
												$pat_name.=' '.$patient->last_name;
										   @endphp		
									     <a href="{{ route('patientslist.create',app()->getLocale()) }}"  class="btn btn-sm btn-action"><i class="mr-2 fa fa-plus text-muted"></i>{{__('Create')}}</a>
									     <!--<a href="javascript:void(0)" onclick="event.preventDefault();openVisitModal({{$patient->id}},{{$patient->clinic_num}},'{{$pat_name}}');" class="btn btn-action btn-sm" title="{{__('All requests')}}"><i  class="mr-2 fas fa-clipboard-list text-muted"></i></a>-->
										 @endif
										 <input type="submit" value="{{isset($patient) ? __('Update') : __('Save') }}"  class="btn  btn-action btn-sm">
									     <input  type="reset" class="btn   btn-reset btn-sm" value="{{__('Reset')}}"/>
										 <a href="{{ route('patientslist.index',app()->getLocale()) }}" class="btn btn-back btn-sm">{{__('Back')}}</a>
										
									</div>
                                </div>
								<div class="row mt-2 m-1" style="border:2px;border-style: double;border-color: #d9e2ef;">
								       <div class="col-md-2">
									     <label class="label-size">{{__('Title')}}</label>
										 <select name="title" id="title" class="select2_title form-control"> 
										  <option value="">{{__('Choose')}}</option>
										  @foreach($titles as $t)
										   <option  value="{{$t->id}}" {{isset($patient)&& isset($patient->title) && $patient->title==$t->id?'selected':''}}>{{$t->name}}</option>
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
													@error('first_name')
													<div class="alert alert-danger">{{__('Please input patient first name')}}</div>
													@enderror        
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
													@error('middle_name')
													<div class="alert alert-danger">{{__('Please input patient middle name')}}</div>
													@enderror        
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
												@error('last_name')
												<div class="alert alert-danger">{{__('Please input patient last name')}}</div>
												@enderror        
											</div>
                                      </div>
									    <div class="col-md-2 col-6">
										   <div class="form-group">
												<label class="label-size">{{__('Birthdate').'*'}}</label>
													<input type="text" class="form-control" readonly="" name="birthdate" id="birthdate"
														   value="{{isset($patient) ? $patient->birthdate : old('birthdate')}}">
												
												@error('birthdate')
												<div class="alert alert-danger">{{__('Please input patient birthdate')}}</div>
												@enderror 
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
												@error('gender')
												<div class="alert alert-danger">{{__('Please input patient gender')}}</div>
												@enderror
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
												     <option value="">{{__('Choose')}}</option>
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
												
												<textarea class="form-control" name="address" id="address">{{isset($patient)? $patient->addresse : old('address')}}</textarea>
										</div>
                                     </div>
									 
									  <!--<div class="col-md-4">
											<div class="form-group">
												<label class="label-size">{{__('Referred by')}}</label>
												<div class="input-group">
												  <input type="text"  name="referred_by" class="form-control" value="{{isset($patient) ? $patient->referred_by : old('referred_by')}}"/>
												</div>
												   
											</div>
                                      </div>
									  
									  <div class="col-md-2 col-6">
                                          	<div class="form-group">
												<label class="label-size">{{__('Blood group')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-venus-mars"></i></span></div>
													<select class="form-control" name="blood_group" id="blood_group">
														<option value="">{{__('')}}</option>
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
									  <div class="col-md-2 col-6">
										   <div class="form-group">
												<label class="label-size" style="white-space:break-spaces;">{{__('Marital Status')}}</label>
												<div class="input-group date">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-heading"></i></span></div>
													<select name="marital_status" class="form-control">
													  <option value="0">{{__('')}}</option>
													  <option value="S" {{(isset($patient) && $patient->marital_status=='S')?'selected':''}}>{{__('Single')}}</option>
													  <option value="M" {{(isset($patient) && $patient->marital_status=='M')?'selected':''}}>{{__('Married')}}</option>
													  <option value="D" {{(isset($patient) && $patient->marital_status=='D')?'selected':''}}>{{__('Divorced')}}</option>
													  <option value="W" {{(isset($patient) && $patient->marital_status=='W')?'selected':''}}>{{__('Widowed')}}</option>
													</select>
												</div>
											</div>
                                      </div>-->
									
									<div class="form-group text-center col-md-12">
				                      <a   class="btn btn-back"  onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">{{__('Go to top')}}</a>
				                    </div>
									
                            </div>  
                           
							
                        </form>
                        <!--end::Form-->
                    </div>
        </div>
        <!-- end:: Content Container-->
    </div>
    <!-- begin:: Content -->
	<table id="suggestions-table">
    <!-- Suggestions will be displayed here dynamically -->
   </table>