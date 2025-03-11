<!--
    DEV APP
    created date : 30-8-2023
 -->

<nav class="main-header navbar text-sm navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
		  <li class="nav-item">
			<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
		  </li>
		 
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto"> 
      
	  	   
	   <li class="nav-item dropdown navBarStyle">
          <a  data-toggle="dropdown" href="#" role="button" aria-expanded="false" style="display:flex;">
                     @php 
					   	  $user_fname = strlen(strtoupper(auth()->user()->fname))>5?mb_substr(strtoupper(auth()->user()->fname),0,3,'utf-8').'.' : strtoupper(auth()->user()->fname);
				  	      $user_lname = strlen(strtoupper(auth()->user()->lname))>5?mb_substr(strtoupper(auth()->user()->lname),0,3,'utf-8').'.' : strtoupper(auth()->user()->lname);
					 @endphp
					
				     <div style="width: 40px;height: 40px;position: relative;">
				      <img class="elevation-2" src="{{url('futgpr/default_profile_photo/noimage.png')}}" style="height: 100%;width: 100%;border-radius: 50%;border: 2px solid #adb5bd;"/>
		              <div style=" width: 15px;height: 15px;border-radius: 50%;background-color:#1bbc9b;border: 2px solid white;bottom: 0;right: 0;position: absolute;"></div>
					</div>
					<div class="p-0 pl-1 pt-2">{{$user_fname.' '.$user_lname}}</div>
					
					
		  </a>
         <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
		        <a href="#" class="text-center dropdown-item">
				  <img  class="img-bordered-sm img-circle elevation-2" src="{{ isset($imgProfile) && $imgProfile != 'none' ? UserHelper::rp_txt($imgProfile->path) :url('futgpr/default_profile_photo/noimage.png')}}" style="width:32px; height:32px;top:10px; left:10px;"/>
                  
				  @php 
				    $user_fname = strlen(auth()->user()->fname)>5?mb_substr(strtoupper(auth()->user()->fname),0,3,'utf-8').'.' : strtoupper(auth()->user()->fname);
				  	$user_lname = strlen(auth()->user()->lname)>5?mb_substr(strtoupper(auth()->user()->lname),0,3,'utf-8').'.' : strtoupper(auth()->user()->lname);
				  @endphp
				  <div><b>{{ $user_fname.' , '.$user_lname}}</b></div>
				  @if(isset(auth()->user()->email))<div> {{auth()->user()->email}}</div>@endif
				</a>
						 
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