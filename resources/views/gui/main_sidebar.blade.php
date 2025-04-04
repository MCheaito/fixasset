<!--
    DEV APP
    created date : 11-7-2022 
 -->
<div class="sidebar">
    @php	
   	 $access_dash = UserHelper::can_access(auth()->user(),'dashboard');
	 $access_patients_list = UserHelper::can_access(auth()->user(),'all_patients');
     $access_visits = UserHelper::can_access(auth()->user(),'lab_requests');
	 $access_billings = UserHelper::can_access(auth()->user(),'medical_billings');
	 $access_general_reports = UserHelper::can_access(auth()->user(),'general_reports');
	 $access_tests_settings = UserHelper::can_access(auth()->user(),'tests_settings');
	 $access_users = UserHelper::can_access(auth()->user(),'users');
	 $access_feedback = UserHelper::can_access(auth()->user(),'send_feedback');
	 $access_resources = UserHelper::can_access(auth()->user(),'all_resources');
	 $access_inventory = UserHelper::can_access(auth()->user(),'inventory');
	 $access_phlebotomy = UserHelper::can_access(auth()->user(),'phlebotomy');
	 $access_custom_reports = UserHelper::can_access(auth()->user(),'custom_reports');
    @endphp
	
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
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        @if( $access_dash) 
						<li class="nav-item">
								 <a class="nav-link {{ (request()->segment(2) == 'dashboard') ? 'active' : '' }}" href="{{ route('dashboard.index',app()->getLocale()) }}"><i class="text-sidebar fas fa-tachometer-alt nav-icon"></i><p>{{ __('Dashboard') }}</p></a>
						 </li>
				       @endif
					
						@if($access_inventory) 
						   <li class="nav-item {{ (request()->segment(2) == 'inventory') ? 'menu-open' : '' }}" >
							
							<a href="javascript:void(0)" class="nav-link {{ (request()->segment(2) == 'inventory' ) ? 'active' : '' }}" >
							  <i class="text-sidebar nav-icon fas fa-archive"></i>
							  <p>{{__('Fix Asset')}}<i class="text-sidebar right fas fa-angle-left"></i></p>
							</a>
							
								
								@switch(auth()->user()->type)
								 @case(2)
									<ul class="ml-2 nav nav-treeview">
									 
									 
									  <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items'  || request()->segment(3) == 'materials') ? 'menu-open' : '' }}">
										 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items' ||request()->segment(3) == 'materials') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Items') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
										 <ul class="ml-2 nav nav-treeview">
											 <li class="nav-item">
											   <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'items' ) ? 'active' : '' }}" href="{{ route('inventory.items.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Items list') }}</i></p></a>
											</li>
											<li class="nav-item" hidden>
											  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'materials' ) ? 'active' : '' }}" href="{{ route('inventory.materials.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Inventory audit') }}</p></a>
											</li>
										 </ul>
									  </li>
									  <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'menu-open' : '' }}">
										 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Tools') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
										 <ul class="ml-2 nav nav-treeview">
											<li class="nav-item">
											 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'suppliers' ) ? 'active' : '' }}" href="{{ route('inventory.suppliers.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Chart of Account') }}</p></a>
											</li>
											<li class="nav-item">
											 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'suppliers' ) ? 'active' : '' }}" href="{{ route('inventory.assetmaincategory.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Main Asset') }}</p></a>
											</li>
											<li class="nav-item">
											 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'collection' ) ? 'active' : '' }}" href="{{ route('inventory.collection.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Sub Asset') }}</p></a>
											</li>
											<li class="nav-item">
											 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'category' ) ? 'active' : '' }}" href="{{ route('inventory.location.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Location') }}</p></a>
											</li>
											
											<li class="nav-item">
											 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'category' ) ? 'active' : '' }}" href="{{ route('inventory.category.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Depreciation Type') }}</p></a>
											</li>
											<li class="nav-item">
											  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'formulas' ) ? 'active' : '' }}" href="{{ route('inventory.formulas.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon" hidden ></i><p>{{ __('Sales formula') }}</p></a>
											</li>
											
										 </ul>
									  </li>
									  
									  
									
									  <li class="nav-item" hidden >
										 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'invoices' ) ? 'active' : '' }}" href="{{ route('inventory.invoices.index',app()->getLocale()) }}" onclick="{{ Session::forget('dashboard_pid')}} localStorage.clear();"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Invoices') }}</p></a>
									  </li>
									   
									   <li class="nav-item">
											  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'reports' ) ? 'active' : '' }}" href="{{ route('inventory.reports.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('reports') }}</p></a>
										</li>
										 
									</ul>
								   @break
								   @case(1)
									 <ul id="inventory_doctor" class="ml-2 nav nav-treeview">
										 <li class="nav-item">
												  <a class="nav-link" href="javascript:void(0)" onclick="open_branch_Modal()"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Choose a branch') }}</p></a>
										  </li>
										 @if(Session::has('inventory_branch_num'))
											   <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items'  || request()->segment(3) == 'materials') ? 'menu-open' : '' }}">
												 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items' ||request()->segment(3) == 'materials') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Items') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
												 <ul class="ml-2 nav nav-treeview">
													 <li class="nav-item">
													   <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'items' ) ? 'active' : '' }}" href="{{ route('inventory.items.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Items list') }}</i></p></a>
													</li>
													<li class="nav-item" hidden>
													  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'materials' ) ? 'active' : '' }}" href="{{ route('inventory.materials.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Inventory audit') }}</p></a>
													</li>
												 </ul>
											   </li>
											   <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'menu-open' : '' }}">
												 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Suppliers') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
												 <ul class="ml-2 nav nav-treeview">
													<li class="nav-item">
													 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'suppliers' ) ? 'active' : '' }}" href="{{ route('inventory.suppliers.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Suppliers list') }}</p></a>
													</li>
													<li class="nav-item">
													 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'collection' ) ? 'active' : '' }}" href="{{ route('inventory.collection.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Collection') }}</p></a>
													</li>
													<li class="nav-item">
													 <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'category' ) ? 'active' : '' }}" href="{{ route('inventory.category.index',app()->getLocale()) }}"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Category') }}</p></a>
													</li>
													<li class="nav-item">
													  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'formulas' ) ? 'active' : '' }}" href="{{ route('inventory.formulas.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Sales formula') }}</p></a>
													</li>
													
												 </ul>
											  </li>
											   
											  
											<li class="nav-item" hidden>
											  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'invoices' ) ? 'active' : '' }}" href="{{ route('inventory.invoices.index',app()->getLocale()) }}" onclick="Session::forget('dashboard_pid')}} localStorage.clear();"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Invoices') }}</p></a>
											</li>
											
											<li class="nav-item">
											  <a class="nav-link {{ (request()->segment(2) == 'inventory' ) && (request()->segment(3) == 'reports' ) ? 'active' : '' }}" href="{{ route('inventory.reports.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('reports') }}</p></a>
											</li>
											
										 @else
											  <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items'  ||request()->segment(3) == 'materials') ? 'menu-open' : '' }}">
												 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'items'  || request()->segment(3) == 'materials') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Items') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
												 <ul class="ml-2 nav nav-treeview">
													<li class="nav-item">
													  <a class="nav-link" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Items list') }}</p></a>
													</li>
													<li class="nav-item">
													  <a class="nav-link" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Inventory audit') }}</p></a>
													</li>
												 </ul>
											  </li>
											   <li class="nav-item {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'menu-open' : '' }}">
												 <a class="nav-link {{ (request()->segment(2) == 'inventory') &&  (request()->segment(3) == 'suppliers'  || request()->segment(3) == 'formulas' || request()->segment(3) == 'collection' ||request()->segment(3) == 'category') ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Suppliers') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
												 <ul class="ml-2 nav nav-treeview">
													<li class="nav-item">
													  <a class="nav-link" href="javascript:void(0)" onclick="Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Suppliers list') }}</p></a>
													</li>
													<li class="nav-item">
													 <a class="nav-link" href="javascript:void(0)" onclick="Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Collection') }}</p></a>
													</li>
													 <li class="nav-item">
													   <a class="nav-link" href="javascript:void(0)" onclick="Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Category') }}</p></a>
													</li>
													<li class="nav-item" hidden>
													  <a class="nav-link" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-square nav-icon"></i><p>{{ __('Sales formula') }}</p></a>
													</li>
													
												 </ul>
											  </li>
											   
											  
											
											<li class="nav-item" hidden>
											  <a class="nav-link" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Invoices') }}</p></a>
											</li>
											
											<li class="nav-item">
											  <a class="nav-link" href="javascript:void(0)" onclick="localStorage.clear();Swal.fire({text:'{{__('Choose a branch')}}',icon:'error',customClass:'w-auto'});"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __(' reports') }}</p></a>
											</li>
											
										 @endif	 
									 </ul>
								   @break
								  @endswitch 
													
					          </li>
					     @endif
					   
					  
						
					
					   
					   
							<li class="nav-item" hidden>
							 <a class="nav-link {{ (request()->segment(2) == 'userslist') ? 'active' : '' }}" href="{{ route('sales.index',app()->getLocale()) }}"><i class="text-sidebar fas fa-eye nav-icon"></i><p>{{ __('Visite') }}</p></a>
                           </li>
					
						
						@if($access_users)
							<li class="nav-item">
							 <a class="nav-link {{ (request()->segment(2) == 'userslist') ? 'active' : '' }}" href="{{ route('userslist.index',app()->getLocale()) }}"><i class="text-sidebar fas fa-users nav-icon"></i><p>{{ __('Users') }}</p></a>
                           </li>
						@endif
						@if($access_tests_settings)
						  <li class="nav-item {{ (request()->segment(2) == 'tests' || request()->segment(2) == 'tests_fields' || request()->segment(2) == 'tests_groups' || request()->segment(2) == 'tests_categories' || request()->segment(2) == 'tests_profiles' || request()->segment(2) == 'tests_formulas' || request()->segment(2) == 'tests_bacteria' || request()->segment(2) == 'tests_antibiotic' ) ? 'menu-open' : '' }}" hidden>
								<a href="javascript:void(0)" class="nav-link {{ (request()->segment(2) == 'tests' || request()->segment(2) == 'tests_fields'  || request()->segment(2) == 'tests_groups' || request()->segment(2) == 'tests_categories' || request()->segment(2) == 'tests_profiles' || request()->segment(2) == 'tests_formulas' || request()->segment(2) == 'tests_bacteria' || request()->segment(2) == 'tests_antibiotic' ) ? 'active' : '' }}">
									  <i class="nav-icon text-sidebar fas fa-cog"></i>
									  <p>Tests Settings<i class="text-sidebar right fas fa-angle-left"></i></p>
								</a>
								<ul class="ml-2 nav nav-treeview" hidden>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_categories' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_cat.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Categories</p></a></li>
								  <!--<li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_groups' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_groups.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Groups</p></a></li>-->
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Codes</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_fields' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_fields.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Fields</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_formulas' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_formulas.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Formulas</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_profiles' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_profiles.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Profiles</p></a></li>
														
							 <li class="nav-item {{ (request()->segment(2) == 'tests') &&  (request()->segment(3) == 'culture'  || request()->segment(3) == 'tests_antibiotic' || request()->segment(3) == 'tests_bacteria' ) ? 'menu-open' : '' }}">
								<a class="nav-link {{ (request()->segment(2) == 'tests') &&  (request()->segment(3) == 'culture'  || request()->segment(3) == 'tests_antibiotic' || request()->segment(3) == 'tests_bacteria' ) ? 'active' : '' }}" href="javascript:void(0)"><i class="text-sidebar far fa-circle nav-icon"></i><p>{{ __('Culture') }}<i class="text-sidebar right fas fa-angle-left"></i></p></a>
								 <ul class="ml-2 nav nav-treeview">
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_bacteria' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_bacteria.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Family Bacteria</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_bacteria' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_sbacteria.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Bacteria</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_antibiotic' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_gantibiotic.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Group Antibiotic</p></a></li>
								  <li class="nav-item"><a class="nav-link {{(request()->segment(2) == 'tests_antibiotic' && request()->segment(3) == 'all'  ) ? 'active' : ''}}" href="{{ route('lab.tests_antibiotic.index',app()->getLocale()) }}"><i class="text-sidebar nav-icon far fa-circle"></i> <p>Antibiotic</p></a></li>

								 </ul>
							</li>	
						</ul>						
						</li>
						 @endif 
						 @if($access_resources)
						  <li class="nav-item {{ (request()->segment(2) == 'external' or request()->segment(2) == 'resources' or request()->segment(2) == 'branches' or request()->segment(2) == 'prices' ) ? 'menu-open' : '' }}">
					
							<a href="javascript:void(0)" class="nav-link {{ (request()->segment(2) == 'external' or request()->segment(2) == 'resources' or request()->segment(2) == 'branches' or request()->segment(2) == 'prices' ) ? 'active' : '' }}">
							  <i class="nav-icon text-sidebar far fa-folder"></i>
							  <p>All Resources
								  <i class="text-sidebar right fas fa-angle-left"></i></p>
							</a>
					
							<ul class="ml-2 nav nav-treeview">
							      <li class="nav-item">
								
								  
									   <a class="nav-link {{ (request()->segment(3) == '' and request()->segment(2) == 'branches') ? 'active' : '' }}" href="{{ route('branches.index',app()->getLocale()) }}" onclick="localStorage.clear();">
										  <i class="text-sidebar nav-icon far fa-circle"></i> <p>{{__('My Labs')}}</p>
									   </a>
										   
		                          </li>
								  <li class="nav-item" hidden>
									   <a class="nav-link {{ (request()->segment(3) == '' and request()->segment(2) == 'resources' ) ? 'active' : '' }}"  href="{{ route('resources.index',app()->getLocale()) }}" onclick="localStorage.clear();">
										  <i class=" text-sidebar nav-icon far fa-circle"></i> <p>{{__('Doctors')}}</p>
									   </a>				
							      </li>
								  <li class="nav-item" hidden>
									<a class="nav-link {{ (request()->segment(3) == 'guarantors' ) && (request()->segment(2) == 'external' ) ? 'active' : '' }}" href="{{ route('external_labs.index',app()->getLocale()) }}"><i class="text-sidebar  far fa-circle nav-icon"></i><p>{{__('Guarantors')}}</p></a>
								  </li>
								  <li class="nav-item" hidden>
									<a class="nav-link {{ (request()->segment(3) == 'referred_labs' ) && (request()->segment(2) == 'external' ) ? 'active' : '' }}" href="{{ route('external_insurance.index',app()->getLocale()) }}"><i class="text-sidebar  far fa-circle nav-icon"></i><p>{{__('Referred Labs')}}</p></a>
								  </li>
								  <li class="nav-item" hidden>
									<a class="nav-link {{ (request()->segment(3) == 'all' ) && (request()->segment(2) == 'prices' ) ? 'active' : '' }}" href="{{ route('prices.index',app()->getLocale()) }}" onclick="localStorage.clear();"><i class="text-sidebar  far fa-circle nav-icon"></i><p>{{__('Prices')}}</p></a>
								  </li>
							</ul>					
			             </li>
			            @endif 
                        
                        @if($access_feedback)
							<li class="nav-item">
								   <a class="nav-link {{ (request()->segment(2) == 'support') ? 'active' : '' }}" href="javacript:void()" data-toggle="modal" data-target="#onlinesupportModal-{{auth()->user()->id}}"> <i class="text-sidebar fas fa-question-circle nav-icon"></i><p>{{__('Contact our Support')}}</p></a>
							</li>
							<!--<li class="nav-item">
			                  <a class="nav-link" href="javacript:void()" data-toggle="modal" data-target="#zoomlinkModal"><i class="text-sidebar fa fa-link nav-icon"></i><p>{{__('Remote connection')}}</p></a>
			                </li>--> 
						@endif	
			
					   <li class="nav-item">
							<a href="{{ route('logout',app()->getLocale()) }}" class="nav-link"	  onclick="event.preventDefault();  document.getElementById('logout-form-2').submit();"onClick="dispatch(\''.$myHtmlId.'\',\'logOutFromD\');'.$closeBar.'"><i class="text-sidebar fa fa-fw fa-sign-out-alt nav-icon"></i><p>{{__('Log out')}}</p></a>
							<form id="logout-form-2" action="{{ route('logout',app()->getLocale()) }}" method="POST" class="d-none">
										 @csrf
							</form>
					  </li>
			   
		   
        </ul>
      </nav>
 </div>