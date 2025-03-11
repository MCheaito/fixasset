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
                    <form method="POST" action="{{ route('registerPassword',app()->getLocale()) }}">
                        @csrf
						@if($errors->has('msg'))
							<div class="alert alert-danger alert-block">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<strong>{{ $errors->first('msg') }}</strong>
							</div>	
							
					@endif
							
					<div class="row m-1 mb-2">			
					<div class="col-md-3">
					<p class="badge txt-bg" style="font-size:20px;">{{__("Confirmation")}}</p>
					</div>
					
                    <div class="mt-1 col-md-3 text-right">
					 <p class="badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</p>
					</div>
							                    
					</div>    
				
					
                        <div class="row m-1 mb-2">

                            <div class="col-md-6">
                                <label for="email" class="label-size">{{ __('User Name').'*' }}</label>

								<input id="email" type="text" class="form-control" name="email" value="{{$username}}"  autocomplete="fname" disabled>
								<input id="username" type="hidden" class="form-control" name="username" value="{{$username}}"  autocomplete="fname" >

                               
                            </div>
                            </div>
						<div class="row m-1 mb-2">
                            <div class="col-md-6">
                               <label for="code" class="label-size">{{ __('Code').'*' }}</label>
								<input id="code" type="text" class="form-control " name="code" value=""  autocomplete="lname" autofocus>

                              
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
							</div>
							<div class="row m-1 mb-2">
                            <div class="col-md-6">
                               <label for="password-confirm" class="label-size">{{ __('Confirm Password').'*' }}</label>
								<input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                            </div>
                        </div>
                      
						<div class="row mb-1">
                            <div class="col-md-6 text-center">
                                <button type="submit" class="btn btn-action">
                                    {{ __('Save') }}
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
 

    });

</script>
@endsection	