@extends('gui.login_gui')
@section('styles')
 <style>
  .grecaptcha-badge { visibility: hidden !important; }
 </style>
@endsection
@section('content')	
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                
                <div class="card-body p-0">
                    <form method="POST" action="{{ route('registerB',app()->getLocale()) }}">
                        @csrf
						@if($errors->has('msg'))
							<div class="alert alert-danger alert-block">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<strong>{{ $errors->first('msg') }}</strong>
							</div>	
							
					@endif
							
					<div class="row m-1 mb-2">			
					<div class="col-md-3">
					<p class="badge txt-bg" style="font-size:20px;">{{__("Registration")}}</p>
					</div>
					<div class="col-md-6">        				   
								<!--<label for="fname" class="label-size">{{__('Clinic').'*'}}</label>-->
								
								<label class="label-size">{{__('Lab').'*'}}</label>
								         		  @if(isset($clinic))
                                                        <select class="custom-select rounded-0" name="clinic_num" id="clinic_num" >
                                                           @foreach($clinic as $clin)
                                                                <option  value="{{$clin->id}}" >{{$clin->full_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                   
					               @error('clinic_num')
                                      <div class="alert alert-danger">{{__('Please select a lab')}}</div>
                                   @enderror 
					</div>
                    <div class="mt-1 col-md-3 text-right">
					 <p class="badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</p>
					</div>
							                    
					</div>    
				
					
                        <div class="row m-1 mb-2">

                            <div class="col-md-6">
                                <label for="fname" class="label-size">{{ __('First name').'*' }}</label>

								<input id="fname" type="text" class="form-control @error('fname') is-invalid @enderror" name="fname" value="{{ old('fname') }}"  autocomplete="fname" autofocus>

                                @error('fname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        

                            <div class="col-md-6">
                               <label for="lname" class="label-size">{{ __('Last name').'*' }}</label>
								<input id="lname" type="text" class="form-control @error('lname') is-invalid @enderror" name="lname" value="{{ old('lname') }}"  autocomplete="lname" autofocus>

                                @error('lname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{$message}}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row m-1 mb-2">
								 <div class="col-md-6">	
									<label for="gender" class="label-size">{{ __('Birthdate').'*' }}</label>	
									<input type="text" class="form-control @error('birthdate') is-invalid @enderror"  value="{{old('birthdate')}}" name="birthdate" id="birthdate" placeholder="YYYY-MM-DD">
								    @error('birthdate')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>
								<div class="col-md-6">
								<label for="tel" class="label-size">{{ __('Cell phone').'*' }}</label>
									<input id="tel" type="text" class="form-control @error('tel') is-invalid @enderror" name="tel" value="{{ old('tel') }}"  autocomplete="tel" autofocus>
									@error('tel')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>
						</div>
						<div class="row m-1 mb-2">
								
							<div class="col-md-6">
							<label for="email" class="label-size">{{ __('Email').'*' }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">

							<!--<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text">@</span></div>
							 </div>-->
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="label-size">{{ __('Username').'*' }}</label>

								<input id="username" readonly="" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}"  autocomplete="username">

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
								 		
											
						</div>	
                        					

                        <div class="row m-1 mb-2">

                            <div class="col-md-6">
                                <label for="password" class="label-size">{{ __('Password').'*' }}</label>

								<input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                       

                            <div class="col-md-6">
                               <label for="password-confirm" class="label-size">{{ __('Confirm Password').'*' }}</label>
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                            </div>
                        </div>
						<div class="row m-1 mb-2">

                           <div class="col-md-6">	 			
											       <label for="gender" class="label-size">{{ __('Gender')}}</label>											
		
													<select class="custom-select rounded-0" name="gender" id="gender">
														<option value="">{{__('Select Gender')}}</option>
														
														<option
															value="M">
																{{__('Male')}}
														</option>
														<option
															value="F" >
																{{__('Female')}}
														</option>
														<option	value="N">
																{{__('Undefined')}}
														</option>
													</select>
								</div> 
							
                        </div>
                      
						<div class="row mb-1">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-action">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection	
@section('scripts')
<script>

    $(document).ready(function(){
 var phones = [{ "mask": "##-###-###"}, { "mask": "##-###-###"}];
    $('#email').inputmask("email");
	$('#tel').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
		
 
 flatpickr('#birthdate', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });		

$('#email').change(function(){
	 $('#username').val($('#email').val());
});


    });

</script>
@endsection	