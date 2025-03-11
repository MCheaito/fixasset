<!--
    DEV APP
    created date : 12-7-2022 
 -->
@php	
     $access_dash = UserHelper::can_access(auth()->user(),'dashboard');
	 $access_patients_list = UserHelper::can_access(auth()->user(),'all_patients');
	 $access_resources = UserHelper::can_access(auth()->user(),'all_resources');
	 $access_visits = UserHelper::can_access(auth()->user(),'lab_requests');
	 $access_profile = UserHelper::can_access(auth()->user(),'profile');
	 $access_tests_settings = UserHelper::can_access(auth()->user(),'tests_settings');
	 $access_inventory = UserHelper::can_access(auth()->user(),'inventory');
	 $access_billings = UserHelper::can_access(auth()->user(),'medical_billings');
	 $access_general_reports = UserHelper::can_access(auth()->user(),'general_reports');
	 $access_custom_reports = UserHelper::can_access(auth()->user(),'custom_reports');
@endphp
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
		  <li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		  </li>
		  @if($access_visits)
		  <li class="nav-item navBarStyle">
		    <a class="nav-link" href="{{ route('lab.visit.index',app()->getLocale()) }}">{{ __('Requests') }}</a>
		  </li>
		  @if($access_visits && auth()->user()->type==2)
		  <li class="d-none d-md-block nav-item navBarStyle">
		    <a class="nav-link" href="{{ route('lab.visit.waiting_list',app()->getLocale()) }}">{{ __('OUTREACH Requests') }}</a>
		  </li>
          @endif
		  @endif
		  @if($access_dash)
		  <li class="nav-item navBarStyle">
			<a class="nav-link" href="{{ route('dashboard.index',app()->getLocale()) }}">{{ __('Dashboard') }}</a>
		 </li>
		 @endif
		 @if($access_patients_list)
		 <li class="nav-item navBarStyle">
			<a class="nav-link" href="{{ route('patientslist.index',app()->getLocale()) }}">{{ __('Patients') }}</a>
		 </li>
		 @endif
		 @if($access_inventory || $access_billings)
		  <li class="d-none d-md-block nav-item dropdown navBarStyle">
				<a class="nav-link  dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Invoices</a>
				<div class="dropdown-menu">	
					@if($access_inventory)
						@switch(auth()->user()->type)
							@case(2)
								<a class="dropdown-item" href="{{ route('inventory.invoices.index',app()->getLocale()) }}" onclick="{{ Session::forget('dashboard_pid')}} localStorage.clear();">{{ __('Inventory') }}</a>
							@break
							@case(1)
							  @if(Session::has('inventory_branch_num'))
								 <a class="dropdown-item" href="{{ route('inventory.invoices.index',app()->getLocale()) }}" onclick="{{ Session::forget('dashboard_pid')}} localStorage.clear();">{{ __('Inventory') }}</a>
							  @else
								 <a class="dropdown-item" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});">{{ __('Inventory') }}</a>
							  @endif					  
							@break
					  @endswitch
				   @endif
				   <div class="dropdown-divider"></div>
					@if($access_billings)
					<a class="dropdown-item" href="{{ route('lab.billing.index',app()->getLocale()) }}">{{ __('Lab') }}</a>
				    <a class="dropdown-item" href="{{ route('lab.referredlabs.index',app()->getLocale()) }}">{{ __('Referred Labs') }}</a>
				   @endif
				</div>	
			</li>		
		@endif
		
	   @if($access_resources)
			<li class="d-none d-md-block nav-item dropdown navBarStyle">
				<a class="nav-link  dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">All Resources</a>
				<div class="dropdown-menu">	
					<a class="dropdown-item" href="{{route('branches.index',app()->getLocale())}}" onclick="localStorage.clear();">{{ __('My Labs') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('resources.index',app()->getLocale())}}" onclick="localStorage.clear();">{{ __('Doctors') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('external_labs.index',app()->getLocale())}}">{{ __('Guarantors') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('external_insurance.index',app()->getLocale())}}">{{ __('Referred Labs') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('prices.index',app()->getLocale())}}" onclick="localStorage.clear();">{{ __('Prices') }}</a>
                    
				</div>	
			</li>				
		@endif
		@if($access_tests_settings)
			<li class="d-none d-md-block nav-item dropdown navBarStyle">
				<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tests Settings</a>
				<div class="dropdown-menu">	
					<a class="dropdown-item" href="{{route('lab.tests_cat.index',app()->getLocale())}}">{{ __('Categories') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests.index',app()->getLocale())}}">{{ __('Codes') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_fields.index',app()->getLocale())}}">{{ __('Fields') }}</a>
                    <div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_formulas.index',app()->getLocale())}}">{{ __('Formulas') }}</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_profiles.index',app()->getLocale())}}">{{ __('Profiles') }}</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_bacteria.index',app()->getLocale())}}">{{ __('Family Bacteria') }}</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_sbacteria.index',app()->getLocale())}}">{{ __('Bacteria') }}</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_gantibiotic.index',app()->getLocale())}}">{{ __('Group Antibiotic') }}</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="{{route('lab.tests_antibiotic.index',app()->getLocale())}}">{{ __('Antibiotic') }}</a>

				</div>	
			</li>				
		@endif			
	  @if($access_inventory || $access_general_reports || $access_custom_reports)
	  <li class="nav-item dropdown d-none d-md-inline-block navBarStyle">
        <a class="nav-link  dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__('Reports')}}</a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		  @if($access_inventory)
				@switch(auth()->user()->type)
					@case(2)
						<a class="dropdown-item" href="{{ route('inventory.reports.index',app()->getLocale()) }}" onclick="localStorage.clear();">{{ __('Inventory') }}</a>
					@break
					@case(1)
					  @if(Session::has('inventory_branch_num'))
						   <a class="dropdown-item" href="{{ route('inventory.reports.index',app()->getLocale()) }}" onclick="localStorage.clear();">{{ __('Inventory') }}</a>
					  @else
						   <a class="dropdown-item" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});">{{ __('Inventory')}}</a>
					  @endif	
					@break
			  @endswitch
		   @endif
           <div class="dropdown-divider"></div>
		   @if($access_general_reports)
				   <a class="dropdown-item" href="{{ route('reports.index',app()->getLocale()) }}">{{ __('General') }}</a>
		   @endif
		    <div class="dropdown-divider"></div>
		   @if($access_custom_reports)
				   <a class="dropdown-item" href="{{ route('custom_reports.index',app()->getLocale()) }}">{{ __('Template') }}</a>
		   @endif
          </div>    
       </li>
	  @endif
	</ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto"> 
      
	  	   
	   <li class="nav-item dropdown">
          <a  data-toggle="dropdown" href="#" role="button" aria-expanded="false" style="display:flex;">
                    @php $imgProfile = App\Models\TblBillLogo::where('clinic_num',auth()->user()->clinic_num)->where('status','O')->value('logo_path');@endphp

					<div style="width: 40px;height: 40px;position: relative;">
					<img class="elevation-2" src="{{ isset($imgProfile) && $imgProfile != '' ? url('furl/'.$imgProfile) :url('furl/default_profile_photo/noimage.png')}}" style="height: 100%;width: 100%;border-radius: 50%;border: 2px solid #adb5bd;"/>
		            <div style=" width: 15px;height: 15px;border-radius: 50%;background-color:#1bbc9b;border: 2px solid white;bottom: 0;right: 0;position: absolute;"></div>
					</div>
					
		  </a>
         <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
		        <a href="#" class="text-center dropdown-item">
				  <img  class="img-bordered-sm img-circle elevation-2" src="{{ isset($imgProfile) && $imgProfile != '' ? url('furl/'.$imgProfile) :url('furl/default_profile_photo/noimage.png')}}" style="width:32px; height:32px;top:10px; left:10px;"/>
                  
				  @php 
				    $user_fname = strlen(auth()->user()->fname)>5?mb_substr(strtoupper(auth()->user()->fname),0,3,'utf-8').'.' : strtoupper(auth()->user()->fname);
				  	$user_lname = strlen(auth()->user()->lname)>5?mb_substr(strtoupper(auth()->user()->lname),0,3,'utf-8').'.' : strtoupper(auth()->user()->lname);
				  @endphp
				  <div><b>{{ $user_fname.' , '.$user_lname}}</b></div>
				  @if(isset(auth()->user()->email))<div> {{auth()->user()->email}}</div>@endif
				</a>
				<div class="dropdown-divider"></div>				  
				 @if($access_profile)
					 @if(auth()->user()->type==1 && UserHelper::can_access(auth()->user(),'profile'))
							<a class="dropdown-item" href="{{route('profiles.doctor',app()->getLocale())}}"><i class="mr-1 text-sidebar fas fa-circle"></i>{{ __('Doctor Profile') }}</a>
					@endif					  
					@if( (auth()->user()->type==2 || auth()->user()->type==3) && UserHelper::can_access(auth()->user(),'profile'))	
					  <a class="dropdown-item" href="{{route('profiles.clinic',app()->getLocale())}}"><i class="mr-1 text-sidebar fas fa-circle"></i>{{ __('Lab Profile') }}</a>
					@endif 
				 @endif				 
				<div class="dropdown-divider"></div>				  
								  
			      <a href="{{ route('logout',app()->getLocale()) }}" class="dropdown-item"
							  onclick="event.preventDefault();
							  document.getElementById('logout-form').submit();">
							 <i class="mr-1 text-sidebar fa fa-fw fa-sign-out-alt"></i>{{ __('Logout') }}
				  </a>
                  <form id="logout-form" action="{{ route('logout',app()->getLocale()) }}" method="POST" class="d-none">
                             @csrf
                   </form>
				   
          </div>
       </li>
    </ul>
  </nav>