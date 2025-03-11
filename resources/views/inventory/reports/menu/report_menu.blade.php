<!--
   DEV APP
   Created date : 7-11-2022
-->
<nav class="navbar navbar-expand-lg navbar-light txt-bg">
  <h5 class="text-white navbar-brand">{{__('All inventory reports')}}</h5>
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
		{{__('Choose an inventory report')}}
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
       	  <p class="ml-2"><b>{{ __('Inventory') }}</b></p>
		   <div class="dropdown-divider"></div>
		   	 <a class="dropdown-item" href="{{route('inventory.reports.inventory_accounting',app()->getLocale())}}">{{__('Accounting')}}</a>
		     <a class="dropdown-item" href="{{route('inventory.reports.inventory_orders',app()->getLocale())}}">{{__('Orders')}}</a>
		  <div class="dropdown-divider"></div>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_sales',app()->getLocale())}}">{{__('Sales')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_sales_per_item',app()->getLocale())}}">{{__('Sales per Item')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_sales_per_supplier',app()->getLocale())}}">{{__('Sales per Supplier')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_sales_per_pay',app()->getLocale())}}">{{__('Sales per Pay')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_purchases',app()->getLocale())}}">{{__('Purchases')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_purchases_per_item',app()->getLocale())}}">{{__('Purchases per Item')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_purchases_per_supplier',app()->getLocale())}}">{{__('Purchases per Supplier')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_purchases_per_pay',app()->getLocale())}}">{{__('Purchases per Pay')}}</a>
			<a class="dropdown-item" href="{{route('inventory.reports.inventory_items_per_qty',app()->getLocale())}}">{{__('Quantities per Item')}}</a>

		</div>
      </li>
	   <li class="nav-item "> 
		<a  id="reports_menu" class="btn btn-back" href="{{route('inventory.reports.index',app()->getLocale())}}">{{__('Back to menu')}}</a>
	  </li>
    </ul>
  </div>
</nav>