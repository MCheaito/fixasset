<!--
    DEV APP
    Created date : 30-8-2023
 -->
@extends('gui.main_gui')

@section('content')
<div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-md-3">
            <!-- small box -->
            <div class="small-box bg-teal">
   		        <div class="inner">
				     <div><b>{{$patient->first_name.(isset($patient->middle_name)?' '.$patient->middle_name.' ':' ').$patient->last_name}}</b></div>
					 @isset($patient->email)<div>{{__('Email').' : '.$patient->email}}</div>@endif
					 @isset($patient->first_phone)<div>{{__('Landline Phone').' : '.$patient->first_phone}}</div>@endif
					 @isset($patient->cell_phone)<div>{{__('Cell Phone').' : '.$patient->cell_phone}}</div>@endif
				</div>
				<a href="javascript:void(0)" class="small-box-footer" onclick="event.preventDefault();$('#personalInfoModal').modal('show');">{{__("More info")}} <i class="ml-1 fas fa-arrow-circle-right"></i></a>
            </div>
			<div class="mt-2 mb-2">
			  <button type="button" class="btn btn-action" onclick="event.preventDefault();$('#changePassModal').modal('show');">{{__("Change Password")}}</button>
			</div>
			
							
          </div><!-- ./col -->
         <div class="col-md-9">
		    <div class="card">
					<div class="card-header card-menu">
					     
						 <div class="col-12">
						 <ul class="nav nav-pills" style="font-size:1rem;">
					        <li class="nav-item"><a class="nav-link active" href="#dashboard_visits" data-toggle="tab">{{__('Medical Visits')}}</a></li>
					     </ul>
						 </div>
						 </div>
					
						<div class="card-body p-0">
						   <div class="tab-content" style="font-size:0.9em;">
							  <div class="tab-pane active" id="dashboard_events">
							   @include('external_patient.dashboard.list.visit_list')
							  </div>
							  
						   </div>		  
						</div>
			   </div>
		 </div>
		  
        </div>
        <!-- /.row -->
       
</div><!-- /.container-fluid -->
@include('external_patient.dashboard.personalInfoModal')
@include('external_patient.dashboard.changePassModal')	
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('body').addClass('sidebar-collapse');
	$('body').find('.main-footer').show();
	 $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
	
	
					
	$('#dash_visit_table').DataTable({
           paging: true,
           searching: true,
           ordering: true,
		   order: [['2','desc'],['3','desc']],
		    scrollY:350,
		   scrollX:true,
		   scrollCollapse: true,
           info: true,
		   language: {
			    search:         "{{__('Search')}}&nbsp;:",
				lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
				info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
				infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
				emptyTable:  "{{__('No data is found')}}",
				zeroRecords: "{{__('No data is found')}}",
				buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "__('Show all')}}"
                                                 }
						
								},
				paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				}
				});	
				
	$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
	
	
	
});
</script>
<script>
$(function(){
	$('#changePassModal').on('shown.bs.modal',function(){
		$('#chg_pass').click(function(e){
			var old_pass = $('#old_password').val();
			var new_pass = $('#new_password').val();
			var cfrm_pass = $('#cfrm_password').val();
			$('#new_password').removeClass('is-invalid');
            $('#cfrm_password').removeClass('is-invalid');
		    $('#old_password').removeClass('is-invalid');
			
			if(old_pass==''){
				$('#old_password').addClass('is-invalid');
				Swal.fire({html:'{{__("Please fill your current password")}}',icon:'error',customClass:'w-auto'});
			    return false;
			}
			
			if(old_pass.length<6){
				$('#old_password').addClass('is-invalid');
				Swal.fire({html:'{{__("Your current password must be at least 6 characters")}}',icon:'error',customClass:'w-auto'});
			    return false;
			}
			
						
			if(new_pass==''){
				$('#new_password').addClass('is-invalid');
				Swal.fire({html:'{{__("Please fill your new password")}}',icon:'error',customClass:'w-auto'});
			    return false;
			}
			
			if(new_pass.length<6){
				$('#new_password').addClass('is-invalid');
				Swal.fire({html:'{{__("Your new password must be at least 6 characters")}}',icon:'error',customClass:'w-auto'});
			    return false;
			}
			
			
			$.ajax({
				url: '{{route("patient_dash.chg_pass",app()->getLocale())}}',
				type: 'post',
				data: {old_pass:old_pass,new_pass:new_pass,cfrm_pass:cfrm_pass,_token:'{{csrf_token()}}'},
				dataType:'json',
				success: function(data){
					if(data.error_match){
						$('#old_password').addClass('is-invalid');
				        Swal.fire({html:data.error_match,icon:'error',customClass:'w-auto'});
					}
					
					if(data.error_same1){
						$('#old_password').addClass('is-invalid');
						$('#new_password').addClass('is-invalid');
				        Swal.fire({html:data.error_same1,icon:'error',customClass:'w-auto'});
					}
					
					if(data.error_same2){
						$('#new_password').addClass('is-invalid');
						$('#cfrm_password').addClass('is-invalid');
				        Swal.fire({html:data.error_same2,icon:'error',customClass:'w-auto'});
					}
					
					if(data.msg){
					$('#changePassModal').modal('hide');
					Swal.fire({icon:'success',toast:true,showConfirmButton:false,position:'bottom-right',timer:3000,title:data.msg});
				    }
				}
			});
		
		});
	});
	$('#changePassModal').on('hidden.bs.modal',function(){
		//reset fields
		$('#new_password').removeClass('is-invalid');
        $('#cfrm_password').removeClass('is-invalid');
		$('#old_password').removeClass('is-invalid');
		$('#old_password').val('');
		$('#new_password').val('');
		$('#cfrm_password').val('');
	});
});
</script>
<script>
function generatePDFOrder(id){
   $.ajax({
           url: '{{route("patient_dash.generatePDFOrder",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Stripe.pdf');
			link.click();
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
  
			    });
	}


function PopupCenter(url, title, w, h) {  
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
                  
        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
                  
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
        var top = ((height / 2) - (h / 2)) + dualScreenTop;  
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
      
        // Puts focus on the newWindow  
        if (window.focus) {  
            newWindow.focus();  
        }  
    }  
</script>
 <script>
  $('#changLang').change(function(){
	  
	  var page_lang='{{auth()->user()->user_language}}';
	  var selected_lang= $('#changLang').val();
	  //alert('{{app()->getLocale()}}');
	  if(selected_lang != page_lang){
		  var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content"); 
		  var user_id = '{{auth()->user()->id}}';
		   $.ajax({
            type: 'POST',
	        url: '{{route("patient_dash.changePatUserlanguage",app()->getLocale())}}',
			dataType: 'JSON',
            data: {user_id : user_id,lang: selected_lang,_token:CSRF_TOKEN},
            success: function (response) {
				if(response.success){
                 
		         window.location.href=response.url;
			   
				}
            }
           });
	    }
  });
  </script>
@endsection
 

