@extends('gui.login_gui')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-menu">
				  <div class="card-title">
				  {{ __('Verified account') }}
				  </div>
				</div>
                <div class="card-body justify-content-center">
                 @if($state=='Y')
				  <h5>{{ __('Your account has been activated successfully').'.' }}</h5>
				  <h5>{{ __('Please proceed to login').'.' }}</h5>
                 @else
				  <h5>{{ __('Your account is already activated').'.' }}</h5>
				  <h5>{{ __('Please proceed to login').'.' }}</h5>	 
				 @endif
				 <a href="{{route('login',app()->getLocale())}}" class="btn btn-action">{{__("Login")}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection