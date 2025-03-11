<!--
    DEV APP
    created date : 30-8-2023
 -->
<div class="sidebar">
   
      <!-- SidebarSearch Form -->
      <div class="mt-1 form-inline">
        <div class="input-group" data-widget="sidebar-search" data-highlight-class="text-dark">
          <input class="form-control form-control-sidebar" type="search" placeholder="{{__('Search')}}" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
   
   
   
      <!-- Sidebar Menu -->
      <nav class="mt-1">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          		
				<li class="nav-item">
					<a class="nav-link {{ (request()->segment(3) == 'calendar' and request()->segment(2) == 'external_patient') ? 'active' : '' }}" href="{{ route('external_patient.calendar.index',app()->getLocale()) }}"><i class="text-sidebar far fa-calendar-alt nav-icon"></i><p>{{__('Calendar')}}</p></a>
				</li>
				<li class="nav-item">
					<a class="nav-link {{ (request()->segment(3) == 'info' and request()->segment(2) == 'patient_dash') ? 'active' : '' }}" href="{{ route('patient_dash.index',app()->getLocale()) }}"><i class="text-sidebar far fa-address-card nav-icon"></i><p>{{__('Patient Dashboard')}}</p></a>
				</li>
				<li class="nav-item">
		        <a href="{{ route('logout',app()->getLocale()) }}" class="nav-link"  onclick="event.preventDefault();  document.getElementById('logout-form-2').submit();"onClick="dispatch(\''.$myHtmlId.'\',\'logOutFromD\');'.$closeBar.'"><i class="text-sidebar fa fa-fw fa-sign-out-alt nav-icon"></i><p>{{__('Log out')}}</p></a>
		       <form id="logout-form-2" action="{{ route('logout',app()->getLocale()) }}" method="POST" class="d-none">
                 @csrf
               </form>
		       </li>
           
		   
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>