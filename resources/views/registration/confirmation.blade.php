<!--
    DEV APP
    Created date : 13-7-2022
	Auto created from laravel auth package
 -->
@extends('gui.login_gui')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-menu">
				  <div class="card-title text-dark">
				   <h5> {{ __('Account Verification') }}</h5>
				  </div>
				</div>
                <div class="card-body">
                 <h5>{{ __('To activate your account, please check your email for a verification link.') }}</h5>
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
