<!--
    DEV APP
    Created date : 13-7-2022
	Updated date : 14-9-2022, changed interface
	Auto created from laravel auth package
	
 -->

@extends('gui.login_gui')
@section('content')
<div class="container-fluid">
    <div class="row d-flex align-items-center">
        <section class="col-md-12 text-center">
		    <img src="{{asset('storage/images/main_img.png')}}" class="img-fluid"   alt="Sample image" style="border: 1px solid;border-radius:50%;width:150px;height:150px;">
		</section>
		<section class="col-md-12 text-center">

			<div class="offset-md-2 col-md-8 card p-0" style="border: 2px solid;">
                <div class="card-header text-white"><h4 class="login-box-msg"><b><i>{{__("Log in to begin your session")}}</i></b></h4></div>
                <div class="card-body login-card-body">
                    
					<form method="POST" action="{{ route('login',app()->getLocale()) }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="username" class="label-size col-md-3 col-form-label text-md-end">{{ __('User Name') }}</label>

                            <div class="input-group col-md-7">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
								<div class="input-group-append">
								<div class="input-group-text">
								<span class="fas fa-user"></span>
								</div>
								</div>
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="label-size col-md-3 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="input-group  col-md-7">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
								<div class="input-group-append">
								<div class="input-group-text">
								<span class="fas fa-lock"></span>
								</div>
								</div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
						
                       <div class="row mb-0">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-action">
                                    {{ __('Login In Here') }}
                                </button>
				                <a class="btn btn-action" href="{{ route('newregister',app()->getLocale()) }}">{{ __('New Account') }}</a>	
                            </div>
							<div class="col-md-12 text-center">
                               
                               @if (Route::has('password.request'))
                               <a class="btn btn-link btn-active" href="{{ route('password.request',app()->getLocale()) }}">
                                        {{ __('Reset your password') }}
                                    </a>
                               @endif
                                    
                               
                            </div>
							<div class="col-md-10 offset-md-2">
						    <span class="text-danger text-left"><strong>{{$errors->first('status')}}</strong></span>
							</div>
                        </div>
                    </form>
                </div>
				
            </section>
			
        </div>
		
    </div>
	<footer>
	  	              <div class="row m-1">
						 
					 	  <div class="mt-4" style="font-size:12px;">{{__("Copyright")}}&#xA0;&#xA0;&copy;{{Carbon\Carbon::now()->format('Y')}} </div>
			         </div>	
	</footer>
</div>
@endsection
