<!DOCTYPE html>
<!--
    DEV APP
    created date : 11-7-2022 
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
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('dist/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('dist/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/adminlte/css/adminlte.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('dist/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('dist/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('dist/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('dist/datatables/datatables.min.css')}}">
  <!--Flatpickr-->
  <link rel="stylesheet" href="{{ asset('dist/flatpickr/dist/flatpickr.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('dist/flatpickr/dist/themes/material_green.css')}}">
  <!--Spotlight galllery box-->
  <link rel="stylesheet" href="{{asset('dist/spotlight/css/spotlight.min.css')}}">
  <!-- Fancybox -->
  <link rel="stylesheet"   href="{{asset('dist/fancybox/fancybox.css')}}"/>
  <!--summernote-->
  <link rel="stylesheet" href="{{asset('dist/summernote/summernote-bs4.min.css')}}"/>
  <!--custom styles-->
  <link rel="stylesheet"   href="{{asset('dist/custom/stylish.min.css')}}"/>
   <!--toggle switch-->
  <link rel="stylesheet" href="{{ asset('dist/toggle-switch/slideon.min.css')}}"/>	   
   <!--tabulator-->
   <link href="{{asset('dist/tabulator/dist/css/tabulator.min.css')}}" rel="stylesheet">
   <link href="{{asset('dist/tabulator/dist/css/tabulator_bootstrap4.min.css')}}" rel="stylesheet">
   
     {!! RecaptchaV3::initJs() !!}
	
	<style>
	 .grecaptcha-badge { visibility: hidden !important; }
	</style>
   <!--begin::Custom Styles -->
  
   <style>
   .navBarStyle{
	   border: 1px solid #ddd;
	   border-radius: 30px;
	   margin-right: 5px !important;
   }
   </style>
   @yield('styles')
  <!--end::Custom Styles -->
</head>
<body class="sidebar-mini layout-fixed bg-light sidebar-collapse"  data-scrollbar-theme="os-theme-dark">
 @auth	
 <div class="wrapper">
 
	<!-- Navbar -->
	 @if(auth()->user()->type==4)
		@include('gui.patient_navbar')
	   @else	  
		@include('gui.main_nav')
	   @endif
  <!-- /.navbar -->
  <!-- sidebar.blade.php -->

 <!-- Main Sidebar Container -->
  <aside  class="main-sidebar elevation-4 sidebar-light-teal">
    <!-- Brand Logo -->
    <a href="#" class="brand-link pt-2 pb-2">
       @php $imgProfile = App\Models\TblBillLogo::where('clinic_num',auth()->user()->clinic_num)->where('status','O')->value('logo_path');@endphp
	  <img src="{{ isset($imgProfile) && $imgProfile!=''?url('furl/'.$imgProfile):asset('storage/images/logo-128-128.jpg') }}" alt="Logo" class="brand-image img-circle elevation-3" style="background:white;opacity: .8">
      <span class="brand-text font-weight-light text-white">&nbsp;</span> 
    </a>

    <!-- Sidebar -->
	  @if(auth()->user()->type==4)
		@include('gui.patient_sidebar')
	  @else
	    @include('gui.main_sidebar')
	  @endif
    <!-- /.sidebar -->
  </aside>
      <!-- End page header content -->    
		     <!-- Content Wrapper. Contains page content -->
             <div class="content-wrapper" data-widget="iframe">
                <section class="content bg-light">
			     
			     @yield('content')
			     
		        </section>
		     </div>
		<footer class="main-footer" style="display:none;">
			<strong>Copyright © 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
			All rights reserved.
			
         </footer>
        <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
		
		<!--jQuery3.7.1-->
		<script src="{{ asset('dist/jquery3.7.1/jquery.min.js') }}"></script>
		<!--<script src="{{ asset('dist/jquery/jquery.min.js') }}"></script>-->
		<!-- jQuery UI 1.11.4 -->
		<script src="{{ asset('dist/jquery-ui/jquery-ui.min.js') }}"></script>
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
		<script>
		  $.widget.bridge('uibutton', $.ui.button) 
		</script>	
		<!-- Bootstrap 4 -->
		<script src="{{ asset('dist/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
		<!-- Select2 -->
		<script src="{{ asset('dist/select2/js/select2.full.min.js')}}"></script>
		<script src="{{ asset('dist/select2/js/i18n/fr.js')}}"></script>
		<!-- overlayScrollbars -->
		<script src="{{ asset('dist/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
		<!--summernote-->
		<script src="{{ asset('dist/summernote/summernote-bs4.min.js') }}"></script>
		<script src="{{ asset('dist/summernote/lang/summernote-fr-FR.min.js') }}"></script>
		<!-- DataTables  & dist -->
		<script src="{{ asset('dist/datatables/datatables.min.js')}}"></script>
		<!-- AdminLTE App -->
		<script src="{{ asset('dist/adminlte/js/adminlte.min.js') }}"></script>
		<!--Flatpickr-->
		<script src="{{asset('dist/flatpickr/dist/flatpickr.min.js')}}"></script>
		<script src="{{asset('dist/moment/moment.min.js')}}"></script>
        <!--Input mask-->  
		 <script src="{{ asset('dist/inputmask/jquery.inputmask.min.js')}}"></script>
		<!--Spotlight box-->
		<script src="{{asset('dist/spotlight/js/spotlight.min.js')}}"></script>	
		<!-- Fancybox -->
		<script src="{{asset('dist/fancybox/fancybox.umd.js')}}"></script>
		<script src="{{ asset('dist/toggle-switch/slideon.min.js')}}"></script>	
        <!--Tabulator-->
        <script type="text/javascript" src="{{asset('dist/tabulator/dist/js/tabulator.min.js')}}"></script>
		<script src="{{asset('dist/jspdf/dist/jspdf.umd.min.js')}}"></script>
		<script src="{{asset('dist/jspdf-autotable/dist/jspdf.plugin.autotable.min.js')}}"></script>
		<script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script> 		
	    <script>
          $(document).ready(function() {
           
			//reset all bootstrap modal to their original content
                  $('.modal').on('hidden.bs.modal', function () {
                      $(this).find('form').trigger('reset');// will clear all element inside modal
                            });
			// Bind to AdminLTE events for sidebar
				$(document).on('shown.lte.pushmenu collapsed.lte.pushmenu', function() {
					setTimeout(function() {
						$($.fn.dataTable.tables(true)).each(function() {
							var table = $(this).DataTable();
							table.columns.adjust(); // Adjust column widths
							if (table.responsive) {
								table.responsive.recalc(); // Recalculate for responsive tables
							}
						});
					   }, 300); // Adjust delay as needed
				});				
				 
                  });
        </script>			
		<!-- SweetAlert2 -->
		@include('sweetalert::alert')
		<!--begin::Custom Scipts -->
		@yield('scripts')
		<!--end::Custom Scipts -->
		@include('support.onlinesupportModal',['id'=>auth()->user()->id])
		
 @endauth
 
</body>
</html>