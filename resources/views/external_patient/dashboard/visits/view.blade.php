<!--
   DEV APP
Created date : 1-9-2023  
-->
@extends('gui.main_gui')
@section('styles')
<style>
    #cmd_select>option:checked,#cmd_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #cmd_cl_select>option:checked,#cmd_cl_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
   
   #presc_select>option:checked,#presc_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #rx_select>option:checked,#rx_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #rxcl_select>option:checked,#rxcl_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #tp_select>option:checked,#tp_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #histf_select>option:checked,#histf_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
	  #ref_select>option:checked,#ref_select>option:focus {
      background: #1e71f4;
      color: white;
      outline:0;
      border:0;
      }
	  
    .read-more-show{
      cursor:pointer;
      
    }
    .read-more-hide{
      cursor:pointer;
     
    }

    .hide_content{
      display: none;
    }

.txt{
		width: 4em;
	}
@media (max-width: 767px) {
	.txt{
		width: 7em;
	}
    }
	

.fancybox__slide {
  padding: 0px;
 
}

.fancybox__carousel .fancybox__slide.has-pdf .fancybox__content {
 width:95%;
 height:85%
}

</style>

@endsection
@section('content')

<div class="container-fluid p-0 pl-1" style="font-size:0.9rem;">	
		    <div class="row mt-1 mb-1">  
			    <div class="col-md-6">
			    <input type="text" name="dateConsult" 
				value="{{__('Visit').' : '.'#'.$visit->id.' , '.__('Date/Time').' : '.Carbon\Carbon::parse($visit->visit_date_time)->format('Y-m-d H:i')}}"
                style="font-size:1.2em;font-weight:bold;"				
				class="form-control form-control-border border-width-2" disabled />

				</div>
               
			</div>
			<div class="row mt-1 mb-2">	
				
				<div class="col-md-3 col-6">
	              <label for="txtClinic" class="label-size"><b>{{__('Branch')}}</b></label>
				  <input type="text" name="txtClinic" value="{{$clinic->full_name}}" class="form-control form-control-border border-width-2" disabled />
                </div>
				<div class="col-md-3 col-6">
	              <label for="txtPatient" class="label-size"><b>{{__('Patient')}}</b></label>
				  <input type="text" name="txtClinic"  value="{{$patient->first_name.' '.$patient->last_name}}" class="form-control form-control-border border-width-2" disabled />
                </div>			
				 <div class="col-md-3 col-6">
					<label for="txDoctor" class="label-size">{{__('Doctor')}}</label>
					<input type="text" name="txDoctor" value="{{isset($doctor)?$doctor->first_name.' '.$doctor->last_name:old('txDoctor')}}" class="form-control form-control-border border-width-2" disabled />
				</div>	
				<div class="col-md-3 col-6">
							<label for="selectFrom" class="label-size">{{__('Status')}}</label>
							<select id="status_list" name="status" class="custom-select rounded-0" disabled>
							    
								 <option value="">{{__('')}}</option>
							  
							  @foreach($status as $stat)
							   <option value="{{$stat->id}}" {{isset($visit) && $stat->id==$visit->status ? 'selected' : ''}}>{{(app()->getLocale()=='en')?$stat->state_en:$stat->state_fr}}</option>
							  @endforeach
							</select>
				</div>			
	        </div>
             
		<div class="row mt-1">
           <div class="col-md-12"> 
			<div class="card">
				<div class="card-header card-menu"> 
				   <div class="card-title">
						<ul class="nav nav-pills" style="font-size:1rem;">
						  @foreach($exams_zones as $z)
						  @php 
						  $code_name = $z->english;
						  $code_name = str_replace(' ', '',$code_name);
						  
						  @endphp
						   <li class="nav-item">
							<a class="visits_tab nav-link"  href="{{'#'.$code_name}}" data-toggle="tab">{{(app()->getLocale()=='en')?$z->english:$z->french}}</a>
						  </li>
						  @endforeach
						</ul>
				   </div>
                 </div>
             
			  <div class="card-body"> 				 
					
					<div class="tab-content">
					   @php 
					        //remove all session variables
							Session::forget('presc_visit_num');
					        Session::forget('cmd_visit_num');
							Session::forget('cmd_cl_visit_num');
							Session::forget('rx_visit_num');
							Session::forget('rxcl_visit_num');
							Session::forget('tp_visit_num');
							Session::forget('histf_visit_num');
							Session::forget('rf_visit_num');
					  @endphp		
					   <div id="History" class="tab-pane">
						  <div class="row">
							@if(in_array('HISTORY',$exams_codes))
							   @php UserHelper::generate_session_keys(['histf_visit_num'=>$visit->id]); @endphp		
						      @include('external_patient.dashboard.visits.cards.history_view')
						    @endif
							
							@if(in_array('PLANS',$exams_codes))
                             <div class="border-bottom col-12"></div>						      
						      @php UserHelper::generate_session_keys(['tp_visit_num'=>$visit->id]); @endphp		
							  @include('external_patient.dashboard.visits.cards.plans_view')
						    @endif
							
							</div>
					   </div>
					   <div id="Rx" class="tab-pane">
						  <div class="row">	
						    @if(in_array('RX',$exams_codes))
							  @php UserHelper::generate_session_keys(['rx_visit_num'=>$visit->id]); @endphp	
							  @include('external_patient.dashboard.visits.cards.RX_view')
						    @endif
						  </div> 
					   </div>
					    <div id="CLRx" class="tab-pane">
						  <div class="row">	
						    @if(in_array('RXCL',$exams_codes))
						      @php UserHelper::generate_session_keys(['rxcl_visit_num'=>$visit->id]); @endphp
						      @include('external_patient.dashboard.visits.cards.RXCL_view')
						    @endif
						  </div> 
					   </div>
					   <div id="MedicalDocuments" class="tab-pane">
						 <div class="row">	
							 
							 @if(in_array('DOCS',$exams_codes))
							  @include('external_patient.dashboard.visits.cards.docs_view')
						     @endif
							 @if(in_array('OCT',$exams_codes))
								@include('external_patient.dashboard.visits.cards.OCT_view')
							 @endif
							 @if(in_array('SLIT',$exams_codes))
								@include('external_patient.dashboard.visits.cards.SLIT_view')
							 @endif
							 @if(in_array('RETINA',$exams_codes))
								@include('external_patient.dashboard.visits.cards.RETINA_view')
							 @endif
						 </div>
					   </div>
					   <div id="OcularTests" class="tab-pane">			   
						 <div class="row">	
							 @if(in_array('VISF',$exams_codes))
							  @include('external_patient.dashboard.visits.cards.visf_view')
						     @endif
							  @if(in_array('BIOMICRO',$exams_codes))
							  @include('external_patient.dashboard.visits.cards.biomicro_view')
						     @endif
							 @if(in_array('OTHERBIOMICRO',$exams_codes))
							  @include('external_patient.dashboard.visits.cards.microscopic_view')
						    @endif
							@if(in_array('AR',$exams_codes))
								@include('external_patient.dashboard.visits.cards.AR_view')
							@endif
							@if(in_array('LM',$exams_codes))
								@include('external_patient.dashboard.visits.cards.LM_view')
							@endif
							@if(in_array('SUBJREF',$exams_codes))
								@include('external_patient.dashboard.visits.cards.SUBJREF_view')
							@endif
							@if(in_array('KM',$exams_codes))
								@include('external_patient.dashboard.visits.cards.KM_view')
							@endif
							@if(in_array('TM',$exams_codes))
								@include('external_patient.dashboard.visits.cards.TM_view')
							@endif
						  </div>	
					   </div>
					   <div id="GFOrders" class="tab-pane">
						  <div class="row">	
						   @if(in_array('GFCMD',$exams_codes))
							@php UserHelper::generate_session_keys(['cmd_visit_num'=>$visit->id]); @endphp	   
							@include('external_patient.dashboard.visits.cards.command_view')
						   @endif
						  </div> 
					   </div>
					   <div id="CLOrders" class="tab-pane">
						  <div class="row">	
						   @if(in_array('CLCMD',$exams_codes))
							@php 
						     UserHelper::generate_session_keys(['cmd_cl_visit_num'=>$visit->id]); 
							 @endphp	   
							@include('external_patient.dashboard.visits.cards.commandcl_view')
						   @endif
						  </div> 
					   </div>
					   <div id="Prescription" class="tab-pane"> 
							<div class="row">
							@if(in_array('MEDPRES',$exams_codes))
								  @php UserHelper::generate_session_keys(['presc_visit_num'=>$visit->id]); @endphp			
								  @include('external_patient.dashboard.visits.cards.med_prescription')
							@endif
							</div>
						</div>
					</div>
			   </div>
            </div>
           </div>			
       </div>								
</div>		
												
@endsection	
@section("scripts")

<script>
 $(function(){ 	
			$('body').addClass('sidebar-collapse');
			// Hide the extra content initially, using JS so that if JS is disabled, no problemo:
            $('.read-more-content').addClass('hide_content')
            $('.read-more-show, .read-more-hide').removeClass('hide_content')

            // Set up the toggle effect:
            $('.read-more-show').on('click', function(e) {
              $(this).next('.read-more-content').removeClass('hide_content');
              $(this).addClass('hide_content');
              e.preventDefault();
            });

            // Changes contributed 
            $('.read-more-hide').on('click', function(e) {
              var p = $(this).parent('.read-more-content');
              p.addClass('hide_content');
              p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
              e.preventDefault();
            });
     
				 $('.visits_tab').click(function (e) {
								e.preventDefault();
								$(this).tab('show');
							});

				$('.visits_tab').on("shown.bs.tab", function (e) {
					var id = $(e.target).attr("href");
					
					localStorage.setItem('visitsTab', id)
				});

				var visitsTab = localStorage.getItem('visitsTab');
				
				if (visitsTab != null) {
					$('a[data-toggle="tab"][href="' + visitsTab + '"]').tab('show');
				}else{
					$('a[data-toggle="tab"][href="#History"]').tab('show');
				}
    
	
   // mark the first option as selected
   if($('#cmd_select').length){
	 
		 var session = localStorage.getItem('cmd-item');
		 if(session ==null){
			 $("#cmd_select option:first").attr('selected','selected');
		 }else{
			$("#cmd_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#cmd_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-cmd').hide();
		 $('#CMD-'+val).show();
		 
		$('#cmd_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('cmd-item',val);
			$('.card-cmd').hide();
			$('#CMD-'+val).show();
			
		}); 
   }
   
   // mark the first option as selected
   if($('#cmd_cl_select').length){
	 
		 var session = localStorage.getItem('cmd-cl-item');
		 //alert(session);
		 if(session ==null){
			 $("#cmd_cl_select option:first").attr('selected','selected');
		 }else{
			$("#cmd_cl_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#cmd_cl_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-cmd-cl').hide();
		 $('#CMDCL-'+val).show();
		 
		$('#cmd_cl_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('cmd-cl-item',val);
			$('.card-cmd-cl').hide();
			$('#CMDCL-'+val).show();
			
		}); 
   }
   
   if($('#presc_select').length){
	 
		 var session = localStorage.getItem('medpresc-item');
		 if(session ==null){
			 $("#presc_select option:first").attr('selected','selected');
		 }else{
			$("#presc_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#presc_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-presc').hide();
		 $('#MEDPRES-'+val).show();
		 
		$('#presc_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('medpresc-item',val);
			$('.card-presc').hide();
			$('#MEDPRES-'+val).show();
			
		}); 
   }
   
   if($('#rx_select').length){
	 
		 var session = localStorage.getItem('rx-item');
		 if(session ==null){
			 $("#rx_select option:first").attr('selected','selected');
		 }else{
			$("#rx_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#rx_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-rx').hide();
		 $('#RX-'+val).show();
		 
		$('#rx_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('rx-item',val);
			$('.card-rx').hide();
			$('#RX-'+val).show();
			
		}); 
   }
   
    if($('#rxcl_select').length){
	 
		 var session = localStorage.getItem('rxcl-item');
		 if(session ==null){
			 $("#rxcl_select option:first").attr('selected','selected');
		 }else{
			$("#rxcl_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#rxcl_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-rxcl').hide();
		 $('#RXCL-'+val).show();
		 
		$('#rxcl_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('rxcl-item',val);
			$('.card-rxcl').hide();
			$('#RXCL-'+val).show();
			
		}); 
   }
   
    if($('#tp_select').length){
	 
		 var session = localStorage.getItem('tp-item');
		 if(session ==null){
			 $("#tp_select option:first").attr('selected','selected');
		 }else{
			$("#tp_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#tp_select :selected').val();
		 //console.log(val);
		 //hide all cards other than selected one
		 $('.card-tp').hide();
		 $('#PLAN-'+val).show();
		 
		$('#tp_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('tp-item',val);
			$('.card-tp').hide();
			$('#PLAN-'+val).show();
			
		}); 
   }
   
   if($('#histf_select').length){
	 
		 var session = localStorage.getItem('histf-item');
		 if(session ==null){
			 $("#histf_select option:first").attr('selected','selected');
		 }else{
			$("#histf_select option[value="+session+"]").attr('selected','selected');
		   }
		 var val = $('#histf_select :selected').val();
		 //hide all cards other than selected one
		 $('.card-histf').hide();
		 $('#HIST-'+val).show();
		 
		$('#histf_select').change(function(e){
			e.preventDefault();
			var val = $(this).val();
			localStorage.setItem('histf-item',val);
			$('.card-histf').hide();
			$('#HIST-'+val).show();
			
		}); 
   }
   
   
  
  
  

 
 });
</script>
<script>
$(function() {
    Fancybox.bind("[data-fancybox]", {
         fitToView: true, // if you require exact size (400x300)
        closeClick: true,
        openEffect: 'elastic',
        closeEffect: 'elastic'
       
        });
});
</script>

@endsection