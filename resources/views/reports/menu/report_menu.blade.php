<!--
   DEV APP
   Created date : 7-11-2022
-->
<nav class="navbar navbar-expand-lg navbar-light txt-bg">
  <h5 class="text-white navbar-brand">{{__('All lab reports')}}</h5>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="nav navbar-nav">
      
      <li class="nav-item"> 
       
      </li>
	  </ul>
	  <ul class="navbar-nav ml-auto">
	   <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		{{__('Choose a report')}}
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
       	   <p class="m-0 ml-2"><b>{{ __('Lab') }}</b></p>
		   <div class="dropdown-divider"></div>
		    <a class="dropdown-item" href="{{route('reports.daily_requests',app()->getLocale())}}">{{__('Requests')}}</a>
		    <a class="dropdown-item" href="{{route('reports.daily_outreach_requests',app()->getLocale())}}">{{__('OUTREACH Requests')}}</a>
		  	<a class="dropdown-item" href="{{route('reports.daily_tests_per_request',app()->getLocale())}}">{{__('Tests per Request')}}</a>
 		   <div class="dropdown-divider"></div>
		    <a class="dropdown-item" href="{{route('reports.daily_invoices',app()->getLocale())}}">{{__('Invoices')}}</a>
		    <a class="dropdown-item" href="{{route('reports.daily_invoices_per_tests',app()->getLocale())}}">{{__('Invoices per Test')}}</a>
		    <a class="dropdown-item" href="{{route('reports.daily_invoices_per_payments',app()->getLocale())}}">{{__('Invoices per Pay')}}</a>

		</div>
      </li>
	   <li class="nav-item "> 
		<a  id="reports_menu" class="btn btn-back" href="{{route('reports.index',app()->getLocale())}}">{{__('Back to menu')}}</a>
	  </li>
    </ul>
  </div>
</nav>