<!doctype html>
<!--
    DEV APP
    created date : 10-7-2022 
 -->
<html lang="{{app()->getLocale()}}">
<head>
    <meta charset="utf-8">
	<title>{{ config('app.name') }}</title>
	<meta content="{{ asset('storage/images/logo-200-200.jpg') }}" property="og:image">
	<meta property="og:type" content="website">
	<meta content="width=device-width, initial-scale=1" name="viewport">
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Scripts -->
	 
    <link href="{{ asset('storage/images/logo-200-200.jpg') }}" rel="shortcut icon" type="image/x-icon">
	<!-- jQuery -->
    <script src="{{ asset('dist/jquery/jquery.min.js')}}"></script>
    <!-- Bootstrap 4 -->
	<script src="{{ asset('dist/bootstrap/js/bootstrap.min.js') }}"></script>
	<!--Flatpickr-->
	<script src="{{asset('dist/flatpickr/dist/flatpickr.min.js')}}"></script>
	<script src="{{asset('dist/moment/moment.min.js')}}"></script>
	<!--Input mask-->  
	<script src="{{ asset('dist/inputmask/jquery.inputmask.min.js')}}"></script>
	@yield('scripts')
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('dist/fontawesome-free/css/all.min.css') }}">
  
 <!--Flatpickr-->
  <link rel="stylesheet" href="{{ asset('dist/flatpickr/dist/flatpickr.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('dist/flatpickr/dist/themes/material_green.css')}}">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	 <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/adminlte/css/adminlte.min.css') }}">
	<!-- Other Styles -->
	<link href="{{ asset('dist/custom/stylish.min.css') }}" rel="stylesheet">  
	{!! RecaptchaV3::initJs() !!}
	 @yield('styles')
</head>
<body>
 <div id="app">
  <nav class="navbar navbar-expand-md navbar-light  shadow-md" style="background-color:#FDFCFA;">
            <div class="offset-md-2 col-md-8">
                
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
               </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                      <div class="d-none d-md-block">
					   <img src="{{asset('storage/images/logo_viva.jfif')}}" class="img-fluid"   alt="Sample image" style="height:80px;width:224px;">
                       </div>
					  <!-- Language Dropdown Menu -->
							  <!--<li class="nav-item dropdown">
							  		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
									  <b class="label-size">{{__('Site language').' : '}}{{(strtoupper(app()->getLocale())=='EN')? __('English'):__('French')}}</b><span class="caret"></span>
									</a>
									
									<div class="dropdown-menu dropdown-menu-right">
									@foreach (config('app.available_locales') as $locale)
									  @php 
									  $url = \URL::full(); 
									  $url = str_replace('en',$locale,$url);  
									  $url = str_replace('fr',$locale,$url);
									  @endphp
									 
									 <a href="{{$url}}" 
									 class="dropdown-item {{(app()->getLocale()==$locale)?'active':''}}">
										{{( strtoupper($locale)=='EN')?__('English'):('French') }}
									   </a>
									@endforeach
									</div>
							  </li>-->
                            
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                       
				   </ul>
                </div>
            </div>
        </nav>
        <main class="py-2" style="background-color:#FDFCFA;">
            @yield('content')
        </main>
    </div>
	 
</body>

</html>		