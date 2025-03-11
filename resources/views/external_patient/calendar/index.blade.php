<!--
    DEV APP
    Created date : 28-7-2022
 -->
@extends("gui.main_gui")
@section("styles")
	 <link rel="stylesheet" href="{{asset('dist/fullcalendar-scheduler-5.11.3/lib/main.css')}}" />
	 <!--<link rel="stylesheet" href="{{asset('dist/bootstrap5/css/bootstrap.min.css')}}">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">-->
	 <link rel="stylesheet" href="{{asset('dist/jquery-ui-1.13.2/jquery-ui.css')}}">
	 <link rel="stylesheet" href="{{asset('dist/jquery-contextmenu/jquery.contextMenu.min.css')}}">
     <link rel="stylesheet" href="{{asset('dist/toastr/toastr.min.css')}}" />
	<style>
	html, body {
	  margin: 0;
	  padding: 0;
	 
    }

.placeholder-event{
	background:red;
} 
.fc .fc-datePickerButton-button,.fc-prev-button,.fc-next-button{ 
   
	border-radius : 50rem !important;
}
 
.btn-primary,.btn-primary:disabled {
    
    background-color: #1bbc9b;
    border-color: #1bbc9b;
	
    
  }

ui-datepicker {
background-color: #fff;
}

.ui-datepicker-header {
background-color: #1bbc9b;
}

.ui-datepicker-title {
color: white;
}

.ui-widget-content .ui-state-default {
border: 0px;
text-align: center;
background: #fff;
font-weight: normal;
color: #000;
}

.ui-widget-content .ui-state-default:hover {
border: 0px;
text-align: center;
background: #1bbc9b;
font-weight: normal;
color: #fff;
}

.ui-widget-content .ui-state-active {
border: 0px;
background: #1bbc9b;
color: #fff;
}
 
 @media screen and (max-width: 767.98px) {
      .fc .fc-datePickerButton-button{ 
        margin-top: auto;
        }
		.fc .fc-customTxt-button{ 
        margin-top: auto;
        }
	  .fc .fc-header-toolbar{
		  text-align:center;
	  } 	
       }
      .modal-header {
	     cursor: move;
        }
    
	.modal-content{
    -webkit-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -moz-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -o-box-shadow: 0 5px 15px rgba(0,0,0,0);
    box-shadow: 0 5px 15px rgba(0,0,0,0);
     }
	 
	table.dataTable thead tr > .dtfc-fixed-right {
        background-color: #1bbc9b;
      }


.placeholder-event {
  background-color: lightgray; /* Background color for the placeholder */
  overflow: hidden;
  position: relative;
}

.placeholder-event::before {
  content: "{{__('Available')}}"; /* Text you want to display */
  top: 50%; /* Adjust vertical position as needed */
  left: 50%; /* Adjust horizontal position as needed */
  position : absolute;
  transform: translate(-50%, -50%);
  color: black; /* Text color */
  font-size: 14px; /* Adjust font size as needed */
  pointer-events: none; /* Prevent interaction with the text */
}

.fc .fc-customTxt-button{
  background: none !important;
  border: none !important;
  color: #000;
  font-size: 1.2rem;
  cursor: none !important;
  margin-left: 0 !important;
  padding: 0 !important;
}

.fc .fc-customTxt-button:before{
   content: '\f245';
   color: #1bbc9b;
   font-size: 1.5rem;
   font-family: 'Font Awesome 5 free';
   margin:0px 5px 0px 0px;
   text-decoration:none;
   font-weight: bold;
   border: 2px solid;
   padding: 1px 4px 1px 5px;
   border-radius: 10%;
} 

.fc .fc-customTxt-button:hover,.fc-customTxt-button:focus{
  background: none !important;
  border: none !important;
  color: #000;
  cursor: none !important;
  
 
} 


</style>
	
@endsection
@section("content")
    
	<div class="container-fluid" style="max-width: 100%;">	
		<input type="hidden" id="cal_date" value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
		<input type="hidden" id="cal_view" value="resourceTimeGridWeek"/>
		<input type="hidden" id="docID" value="{{$docID}}"/>
		<input type="hidden" id="clinicID" value="{{$clinicID}}"/>

		<div class="row">	
		   	<div class="col-xl-4">
			  <input type="text" class="form-control form-control-border border-width-2" value="{{$patients->patName}}" disabled />
			</div>
			<div class="col-xl-4 input-group" style="flex-wrap:nowrap;">
					  <div class="input-group-prepend">
					  <span class="input-group-text">{{__('Doctor').' : '}}</span>
					  </div>
					 
						  <select class="select2 form-control" id="selectResource" name="selectResource" style="width:auto;">
							@if(count($doctors)>1)
							 <option value="">{{__('All doctors')}}</option>
							@endif	   
							@foreach($doctors as $doc)
							  <option value="{{$doc->id}}" {{($docID==$doc->id)?'selected':''}}>{{$doc->first_name.' '.$doc->last_name}}</option>
							@endforeach		   
						  </select>
					   
             </div>
             <div class="col-xl-4 text-right" style="font-size:0.8em;">
			 <i>Licensed &copy; {{Carbon\Carbon::now()->format('Y')}} FullCalendar LLC</i>
			 
			 </div>
			 
			<div   class="col-xl-12" id="calendar-container">
				
				<div id='calendar'></div>
			</div>
		</div>	
    </div>     
	<!--include event Modal-->
	   @include('external_patient.calendar.eventModal',['status'=>$status,'patients'=>$patients,'exams'=>$exams])
     
	<!--end include event Modal-->
	<!--include patient card modal-->
	@include('external_patient.calendar.patient_card.patientCardModal')
	
@endsection
@section("scripts")
    {{-- Scripts --}}
	
	<script src="{{asset('dist/fullcalendar-scheduler-5.11.3/lib/main.js')}}"></script>
	<script src="{{asset('dist/fullcalendar-scheduler-5.11.3/lib/locales-all.js')}}"></script>
	<script src="{{asset('dist/fullcalendar-scheduler-5.11.3/rrule/rrule.js')}}"></script>
	<script src="{{asset('dist/fullcalendar-scheduler-5.11.3/rrule/rrule-tz.min.js')}}"></script>
	<script src="{{asset('dist/fullcalendar-scheduler-5.11.3/rrule/main.global.min.js')}}"></script>
	<script src="{{asset('dist/jquery-ui-1.13.2/jquery-ui.js')}}"></script>
	<script src="{{asset('dist/jquery-contextmenu/jquery.contextMenu.min.js')}}"></script>
    <script src="{{asset('dist/jquery-contextmenu/jquery.ui.position.js')}}"></script>
	<script src="{{asset('dist/toastr/toastr.min.js')}}"></script>
	
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(function () {
        
		
		$('body').addClass('sidebar-collapse');
		
		$('body').find('.main-footer').hide();
		
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
       });
		
		var decodeEntities = (function() {
			  // this prevents any overhead from creating the object each time
			  var element = document.createElement('div');

			  function decodeHTMLEntities (str) {
				if(str && typeof str === 'string') {
				  // strip script/html tags
				  str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
				  str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
				  element.innerHTML = str;
				  str = element.textContent;
				  element.textContent = '';
				}

				return str;
			  }

			  return decodeHTMLEntities;
			})();
		
		$('.select2').select2({theme:'bootstrap4',width:'resolve'});
		//$('.select2_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent:$('#eventModal')});
		
			flatpickr('#event_date',{
		allowInput: true,
		enableTime: false,
		dateFormat: "Y-m-d",
		disableMobile: true
	    });
	
	flatpickr('#event_start_time',{
		allowInput: true,
		enableTime: true,
		noCalendar: true,
		defaultHour: 8,
		dateFormat: "H:i",
		time_24hr: true,
		disableMobile: true
		
	});
	
	flatpickr('#event_end_time',{
		allowInput: true,
		enableTime: true,
		noCalendar: true,
		defaultHour: 8,
		minTime: $('#event_start_time').val(),
		dateFormat: "H:i",
		time_24hr: true,
		disableMobile: true
	});
	
	
		
		//get dropped list of patients
		 
		   $.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						   }
					   });
		
        
		//draw calendar on dcument ready
		    var EVTS = "{{ route('external_patient.calendar-events',app()->getLocale()) }}";
			var RESOURCES = "{{ route('external_patient.calendar-resources',app()->getLocale()) }}";
			var CRUDEVTS = "{{ route('external_patient.calendar-crud-ajax',app()->getLocale()) }}";
           	var height='650px';
			var initialLocaleCode = '{{app()->getLocale()}}'; 	   
		    var calendarEl = document.getElementById('calendar');
		   	var user_type='{{auth()->user()->type}}';
				  
		   
			var calendar = new FullCalendar.Calendar(calendarEl, {
                	schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
					timeZone: 'America/Montreal',
                    weekNumberCalculation: 'ISO',				
					scrollTime: '8:00:00',
					stickyFooterScrollbar : true,
                    slotDuration: '00:15:00', // very small slots will make the calendar really tall
                    dayMinWidth: 180, // will cause horizontal scrollbars					
					initialDate: $('#cal_date').val(),
					initialView: $('#cal_view').val(),
					height: height,
					locale: initialLocaleCode,
					navLinks: true, // can click day/week names to navigate views
					selectable: true,
					eventOverlap: false,
					droppable: false, // this allows things to be dropped onto the calendar
                    nowIndicator: false, 					
				   	themeSystem: 'bootstrap',
					allDaySlot: false,
					selectConstraint : 'availableSchedule',
					eventConstraint  : 'availableSchedule',
					selectOverlap: function(event) {
					  return (event.groupId == "availableSchedule");
					},
					editable: false,
					eventStartEditable: false,
					eventDurationEditable: false,
					eventResourceEditable : false,
					dayMaxEvents: true,
					expandRows : true,
					aspectRatio: 2,
					displayEventTime: false,
					
				     customButtons: {
							customTxt:{
								text: '{{__("Click on available slots to reserve")}}'
								
							},
							datePickerButton: {
								text: '{{__("Go to date")}}',
								
								click: function () {


									var btnCustom = $('.fc-datePickerButton-button'); // name of custom  button in the generated code
									
									btnCustom.after('<input type="hidden" id="hiddenDate" class="datepicker" style="position: relative; z-index:100;"/>');

									$("#hiddenDate").datepicker({
										showOn: "button",
										changeMonth: true,
                                        changeYear: true,
										dateFormat:"yy-mm-dd",
										showAnim:"drop",
										showButtonPanel: true,
										onSelect: function (dateText, inst) {
											calendar.changeView('resourceTimeGridDay',new Date(dateText));
											calendar.gotoDate(new Date(dateText));
											
										},
									});

									var btnDatepicker = $('.ui-datepicker-trigger'); // name of the generated datepicker UI 
									
									//Below are required for manipulating dynamically created datepicker on custom button click
									$('#hiddenDate').show().focus().hide();
									btnDatepicker.trigger('click'); //dynamically generated button for datepicker when clicked on input textbox
									btnDatepicker.hide();
									btnDatepicker.remove();
									$('input.datepicker').not(':first').remove();//dynamically appended every time on custom button click

								}
							}
							
						},
						
					bootstrapFontAwesome:{
						prev:'fa-chevron-left',
						next:'fa-chevron-right'					
						},	
					
					headerToolbar: {  
						  start: 'resourceTimeGridDay,resourceTimeGridWeek datePickerButton',
						  center: 'title customTxt',
						  end: 'today prev next'
						},
					titleFormat: { 
					    year: '2-digit', 
						month: 'short', 
						day: '2-digit',
						weekday: 'short'
						
					     // other view-specific options here
					   },		
					 
					
					resources:  {
								
									url: RESOURCES,
									method: 'GET',
									extraParams: function(){
									 return{
										'docID':$('#docID').val(),
										'clinicID':$('#clinicID').val()
										};
										}
						        },
					
					resourceLaneDidMount :function (arg) {
					$(arg.el).css('border', 'solid 3px #1bbc9b');
					
					},
					
					resourceLabelDidMount :  function (arg) {
					$(arg.el).css('border', 'solid 3px #1bbc9b');
					
					},
					
					events: {
						            url: EVTS,
									method: 'GET',
									extraParams: function(){
									return{
										'docID': $('#docID').val(),
										'clinicID':$('#clinicID').val()
										};
										}
					        },
							
					
					select: function (info){
                       var view = calendar.view;
						if(view.type=='dayGridMonth'  ){
										//do nothing
										info.revert();
									}else{
							       
									var clinic_name = '';
									var pro_name = '';
									var resource_title_split = info.resource.title.split(";");
									var data1 = resource_title_split[1].split(":");
									var data2 = resource_title_split[0].split(":");
									pro_name = 	data1[1];
									clinic_name = 	data2[1];
								    var startDT = info.startStr;
									var startD = moment(startDT).format("YYYY-MM-DD");
									var startT = moment(startDT).format("HH:mm");
									var endDT =  info.endStr;
									var endT =  moment(endDT).format("HH:mm");
									//fetch patients and exams  for resource facility
									var branch_num = '{{$clinicID}}';
											   
								   // set values in inputs
								   
									$('#eventModal').find('input[name=event_date]').val(
										startD
									);
									
									$('#eventModal').find('input[name=event_start_time]').val(
										startT
									);
									
									$('#eventModal').find('input[name=event_end_time]').val(
										endT
									);
									
									$('#eventModal').find('input[name=clinic_name]').val(
												clinic_name
											);
									
									$('#eventModal').find('input[name=pro_name]').val(
												pro_name
											);		
									
									// show modal dialog
									//$('#eventModal').find('#patient_list').prop('disabled',false);  
									//$('#exam_list').select2({dropdownParent: $('#eventModal'),theme:'bootstrap4',width:'resolve'});
									$('#eventModal').find('#delete-event').hide();
									$('#eventModal').modal("show");
                                    
									$('#save-event').off().on('click', function() {
										//get data from filled fields in event 
												var new_title= $('input[name=title]').val();
												var new_note= $('#event_note').val();
												var new_status= $('#status_list').val();
												var new_patient= $('#patient_list').val();
												var new_exam= $('#exam_list').val();
												var moment_start_time = moment($('input[name=event_start_time]').val(),'HH:mm');
												var moment_end_time =  moment($('input[name=event_end_time]').val(),'HH:mm');
												var diff = moment_end_time.diff(moment_start_time,'minutes');
												var new_start_time = $('input[name=event_start_time]').val();
												var new_end_time = $('input[name=event_end_time]').val();
												var resourceStartT = info.resource.extendedProps.start_time;
												var resourceEndT = info.resource.extendedProps.end_time;
																		
													if(new_patient==''){
													Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }
													
													if(new_status==''){
													Swal.fire({text:'{{__("Please choose a status")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }
												   
												   if(new_exam==''){
													Swal.fire({text:'{{__("Please choose an exam")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }
													//check time difference less than 15  minutes
													
													if(diff<15){
												     Swal.fire({text:'{{__("Please choose two times of difference at least 15 minutes")}}',icon:'error',customClass:'w-auto'});
													 return false;
													}
													
													
														if(new_start_time<resourceStartT || new_start_time>resourceEndT){
														 Swal.fire({text:'{{__("Please choose a time interval between")}}'+' '+resourceStartT+' '+'{{__("and")}}'+' '+resourceEndT,icon:'error',customClass:'w-auto'});
														 return false;
														}
														
														if(new_end_time<resourceStartT || new_end_time>resourceEndT){
														 Swal.fire({text:'{{__("Please choose a time interval between")}}'+' '+resourceStartT+' '+'{{__("and")}}'+' '+resourceEndT,icon:'error',customClass:'w-auto'});
														 return false;
														}
													   
													
													
													var evt_date =  $('input[name=event_date]').val();
													var evt_start_time = $('input[name=event_start_time]').val();
													var evt_end_time = $('input[name=event_end_time]').val();

													var new_start = moment(evt_date+" "+evt_start_time).format("YYYY-MM-DD HH:mm");
												    var new_end = moment(evt_date+" "+evt_end_time).format("YYYY-MM-DD HH:mm");
										            var pro_id = $('#docID').val();
													var clinic_id = $('#clinicID').val();
													pro_id = info.resource.id;
													
											$.ajax({

												url: CRUDEVTS ,

												data: {

                                                    title: new_title,
													status: new_status,
													patient: new_patient,
													exam: new_exam,
													start: new_start,
													end: new_end,
													pro: pro_id,
													note: new_note,
													clinic: clinic_id,
													type: 'add'

												},

												type: "POST",

												success: function (data) {
											        if(data.error){
													 Swal.fire({icon:'error',html:data.error,customClass:'w-auto'});	
													}else{
													// if saved, close modal
                                                    $("#eventModal").modal('hide');
													calendar.addEvent(data);
													
													//calendar.unselect();
													calendar.refetchEvents();
												    displayMessage("{{__('Event Created Successfully')}}");
													}
												}

											});
										
                                    });
								
									}

							},

                   

                    eventClick: function(info)  {
                      var view = calendar.view;
									
						
						if(view.type=='dayGridMonth'  ){
												//do nothing
								}else{
								
						      if(info.event.extendedProps.recurrent==1 && info.event.extendedProps.id_patient=='{{$patients->id}}'){
						
								//var modifyMsg = confirm("{{__('Do you really want to edit this event?')}}");

								//if (modifyMsg) {
									var startDT = info.event.startStr;
									var startD = moment(startDT).format("YYYY-MM-DD");
									var startT = moment(startDT).format("HH:mm");
									var endDT =  info.event.endStr;
									var endT =  moment(endDT).format("HH:mm");
									var title = info.event.title;
									var id = info.event.id;
									var note = info.event.extendedProps.event_note;
									
									//fetch patients for facility
									 	$('#eventModal').find('#patient_list').val(
												info.event.extendedProps.id_patient
											);
										  
										  $('#eventModal').find('#exam_list').val(
												info.event.extendedProps.exam_id
											);
										
											
											$('#eventModal').find('input[name=event_id]').val(
												id
											);
											
											$('#eventModal').find('input[name=clinic_name]').val(
												info.event.extendedProps.clinic
											);
											
											$('#eventModal').find('input[name=pro_name]').val(
												info.event.extendedProps.pro
											);
											
											$('#eventModal').find('input[name=title]').val(
												info.event.extendedProps.event_title
											);
											
											$('#eventModal').find('#status_list').val(
												info.event.extendedProps.id_status
											);
											
											$('#eventModal').find('input[name=event_date]').val(
												startD
											);
											
																						
											$('#eventModal').find('input[name=event_start_time]').val(
												startT
											);
											
											$('#eventModal').find('input[name=event_end_time]').val(
												endT
											);
											
											$('#eventModal').find('#event_note').val(
												note
											);
											
											// show modal dialog
											//$("#patient_list").select2().select2("destroy");
											$('#eventModal').find('#patient_list').prop('disabled',true);        
									        $('#eventModal').find('#delete-event').show();
											
									        //$('#eventModal').find('#new-consult').show();
											$('#eventModal').modal("show");
											
											$("#eventModal").find('#save-event').off().on('click', function() {
												//get data from filled fields in event 
												var new_title= $('input[name=title]').val();
												var new_status= $('#status_list').val();
												var new_note= $('#event_note').val();
												var new_patient= $('#patient_list').val();
												var new_exam= $('#exam_list').val();
												var moment_start_time = moment($('input[name=event_start_time]').val(),'HH:mm');
												var moment_end_time =  moment($('input[name=event_end_time]').val(),'HH:mm');
												var diff = moment_end_time.diff(moment_start_time,'minutes');
												
																								
												var new_start_time = $('input[name=event_start_time]').val();
												var new_end_time = $('input[name=event_end_time]').val();
												var resources = info.event.getResources();
												var resourceStartT = resources.map(function(resource) { return resource.extendedProps.start_time });
                                                resourceStartT = resourceStartT[0];
												var resourceEndT = resources.map(function(resource) { return resource.extendedProps.end_time });
												resourceEndT = resourceEndT[0];
												
													 if(new_patient==''){
													Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }												
													
																								    
													if(new_status==''){
													Swal.fire({text:'{{__("Please choose a status")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }
												   												  												
												  if(new_exam==''){
													Swal.fire({text:'{{__("Please choose an exam")}}',icon:'error',customClass:'w-auto'});
													return false;
												    }
													//check time difference less than 15  minutes
													
													if(diff<15){
												     Swal.fire({text:'{{__("Please choose two times of difference at least 15 minutes")}}',icon:'error',customClass:'w-auto'});
													 return false;
													}
													
													
													
														if(new_start_time<resourceStartT || new_start_time>resourceEndT
														   || new_end_time<resourceStartT || new_end_time>resourceEndT){
														 Swal.fire({text:'{{__("Please choose a time interval between")}}'+' '+resourceStartT+' '+'{{__("and")}}'+' '+resourceEndT,icon:'error',customClass:'w-auto'});
														 return false;
														}
													
													
													var evt_date =  $('input[name=event_date]').val();
													var evt_start_time = $('input[name=event_start_time]').val();
													var evt_end_time = $('input[name=event_end_time]').val();

													var new_start = moment(evt_date+" "+evt_start_time).format("YYYY-MM-DD HH:mm");
												    var new_end = moment(evt_date+" "+evt_end_time).format("YYYY-MM-DD HH:mm");
													
													 
													$.ajax({

														url: CRUDEVTS ,

														data: {

															title: new_title,
															status: new_status,
															patient: new_patient,
															exam: new_exam,
															start: new_start,
															end: new_end,
															note: new_note,
															id: id,
															type: 'update'

														},

														type: "POST",

														success: function (data) {
														   if(data.error){
																 Swal.fire({icon:'error',html:data.error,customClass:'w-auto'});	
																}else{
																// if saved, close modal
																$("#eventModal").modal('hide');
																
																$('#eventModal').find('#delete-event').hide();
																displayMessage("{{__('Event Updated Successfully')}}");
																calendar.refetchEvents();
																}
														}

													});
												
											});
											 $("#eventModal").find('#delete-event').off().on('click', function(e) {
                                                  e.preventDefault();
												  var delMsg = confirm("{{__('Do you really want to delete this event?')}}");
													if(	delMsg ){
														
														$.ajax({

															type: "POST",

															url: CRUDEVTS ,

															data: {

																	id: id,

																	type: 'delete'

															},

															success: function (response) {
															  if(response.success){ 
															   $("#eventModal").modal('hide');
															   $('#eventModal').find('#delete-event').hide();
																info.event.remove();
																calendar.refetchEvents();

																displayWarnMessage("{{__('Event Deleted Successfully')}}");
															  }
															if(response.error){
																Swal.fire({text:response.error,icon:"error",customClass:"w-auto"});
																 }	

															}

														});
													}
											 });	

								//}
						}//end non recurrent event
                       }
					},
				
					  
					//event exists
						
						eventDidMount : function (info) {
							       
									var view=calendar.view;
							        
									
									if(view.type=='dayGridMonth' ){
								     //do nothing
									 
									}else{
									
									
									
									if(info.event.extendedProps.recurrent !=3){ /*do nothing*/   
									 $(info.el).find(".fc-event-title").css('font-size','0.85rem');
						             $(info.el).find(".fc-event-title").css('font-weight','bold');
									 $(info.el).find(".fc-event-title").css('padding','0 1px');
									 $(info.el).find(".fc-event-title").css('white-space','pre-wrap');
									 
									}
								    
								   if(info.event.extendedProps.recurrent==3){ /*do nothing*/   
                                         //$(info.el).find('.placeholder-event').remove();
										 $(info.el).find(".fc-event-title").css('font-size','1rem');
						                 $(info.el).find(".fc-event-title").css('font-weight','bolder');
									     $(info.el).find(".fc-event-title").css('text-align','center');
									}
									 
									 
									
									if( (info.event.extendedProps.recurrent == 1 || info.event.extendedProps.recurrent == 2) && info.event.extendedProps.id_patient=='{{$patients->id}}' ){
											
											var name_edit='{{__("Edit")}}';
											var name_del='{{__("Delete")}}';
											var name_payment='{{__("Payment")}}';
											var icon_payment='fab fa-cc-stripe';
											var description = info.event.extendedProps.description;	
											
											var i = document.createElement('i');
											// Add all your other classes here that are common, for demo just 'fa'
											i.className = 'fa'; 
											i.classList.add(info.event.extendedProps.icon);
											i.classList.add('fa-lg');
											i.classList.add('m-1');
											i.classList.add('float-right');
											//i.style.cssText='color:'+info.event.color;
											
											i.style.cssText='color:#343a40'; 
											
											var x = Math.floor((Math.random() * 100000) + 1);
											
											var code = info.event.extendedProps.id_status+x;
											// If you want it inline with title
											$(info.el).prepend(i);
											if(info.event.extendedProps.event_sms_status=='O' && info.event.extendedProps.id_status != 1){
											var i1 = document.createElement('i');
											// Add all your other classes here that are common, for demo just 'fa'
											i1.className = 'fa'; 
											i1.classList.add('fa-sms');
											i1.classList.add('fa-lg');
											i1.classList.add('m-1');
											i1.classList.add('float-right');
											i1.style.cssText='color:#343a40';
											$(info.el).prepend(i1);
											code=code+1;
											}
											if(info.event.extendedProps.event_mail_status=='O' && info.event.extendedProps.id_status != 1){
											var i2 = document.createElement('i');
											// Add all your other classes here that are common, for demo just 'fa'
											i2.className = 'fa'; 
											i2.classList.add('fa-envelope');
											i2.classList.add('fa-lg');
											i2.classList.add('m-1');
											i2.classList.add('float-right');
											i2.style.cssText='color:#343a40';
											$(info.el).prepend(i2);
											code=code+1;											
											}
											
											if(info.event.extendedProps.recurrent==1 && info.event.extendedProps.id_status == 1 && (info.event.extendedProps.onlineform_sms_status=='O' || info.event.extendedProps.onlineform_email_status=='O') ){
											var i3 = document.createElement('i');
											// Add all your other classes here that are common, for demo just 'fa'
											i3.className = 'fas'; 
											i3.classList.add('fa-file-alt');
											i3.classList.add('fa-lg');
											i3.classList.add('m-1');
											i3.classList.add('float-right');
											i3.style.cssText='color:#343a40';
											$(info.el).prepend(i3);
											code=code+1;
											}
											
											var my_items ='';
											//'new' : {name : name_new,icon : 'fas fa-plus'},
														
											if(info.event.extendedProps.recurrent == 1){
											
											 my_items={
														
														'edit': {name: name_edit, icon: 'edit'},
														'sep2': '---------',
														'delete': {name: name_del, icon: 'delete'},
														'sep3': '---------',
														'payment': {name: name_payment, icon: icon_payment},
														'sep3': '---------',
													    'Decription': {type: 'html', html:description}
													  };
											     }
												 
                                            if(info.event.extendedProps.recurrent == 2){
											
											 my_items={
														'edit': {name: name_edit, icon: 'edit'},
														'sep1': '---------',
														'delete': {name: name_del, icon: 'delete'},
														'sep2': '---------',
													    'Decription': {type: 'html', html:description}
													  };
											     }
																				 
											
											  
                                             var trigger_event,trigger_delay;
                                            if(checkIOS()){
													trigger_event = "hover";
													trigger_hide = true;
												}else{
													trigger_event = "right";
													trigger_hide = false;
												}			 											   
											
											$.contextMenu({
												selector: '.context-menu-one'+info.event.id+code,
												trigger : trigger_event,
												hideOnSecondTrigger : trigger_hide ,
												autoHide:  true,
												
												callback: function(key, options) {
													var m = 'clicked: ' + key;
											    
											   
													
													//end key new
													if(key=='edit'){
													  //same as event click edit need ajax to get new values of updated event
													    
														var id = info.event.id;
														 
														//fetch patients for facility
															 
															  				  
															// set values in inputs
																	
																	
																	
																	 $.ajax({

																			type: "POST",
																			url: CRUDEVTS ,
																			data: { id: id,type:"event_details"},
																			dataType: "JSON",
																			success: function (data) {
																			   $('#eventModal').find('input[name=event_id]').val(
																					data.event_id
																				);
																	
																				$('#eventModal').find('input[name=clinic_name]').val(
																					data.clinic_name
																				);
																				$('#eventModal').find('input[name=pro_name]').val(
																					data.doctor_name
																				);
																			   
																			   $('#eventModal').find('input[name=title]').val(
																		                data.event_title
																	                   );
																	   		    $('#eventModal').find('#status_list').val(
																		                 data.id_status
																	                );
																	           
																			   $('#eventModal').find('#patient_list').val(
																							data.id_patient
																								);
																			   
																			   $('#eventModal').find('#event_note').val(
																							data.note
																						);				
																					  
																			   $('#eventModal').find('#exam_list').val(
																							data.exam_id
																						);
																						
																	          $('#eventModal').find('input[name=event_date]').val(
																		            data.startD
																	                  );
																																													
																	          $('#eventModal').find('input[name=event_start_time]').val(
																		            data.startT
																	               );
																	
																	           $('#eventModal').find('input[name=event_end_time]').val(
																		               data.endT
																	                   );
																				 
																				 // show modal dialog
														                       //$("#patient_list").select2().select2("destroy");
											                                   
																			   $('#eventModal').find('#delete-event').show();
																			  
									                                           //$('#eventModal').find('#new-consult').show();
																	           $('#eventModal').modal("show"); 
																				 } 
																				
																				});
																	
																	
																	
											
																	$("#eventModal").find('#save-event').off().on('click', function() {
																		//get data from filled fields in event 
																		var new_title= $('input[name=title]').val();
																		var new_note= $('#event_note').val();
																		var new_status= $('#status_list').val();
																		var new_patient= $('#patient_list').val();
																		var new_exam= $('#exam_list').val();
																		var moment_start_time = moment($('input[name=event_start_time]').val(),'HH:mm');
																		var moment_end_time =  moment($('input[name=event_end_time]').val(),'HH:mm');
																		var diff = moment_end_time.diff(moment_start_time,'minutes');
																		
																														
																		var new_start_time = $('input[name=event_start_time]').val();
																		var new_end_time = $('input[name=event_end_time]').val();
																		var resources = info.event.getResources();
																		var resourceStartT = resources.map(function(resource) { return resource.extendedProps.start_time });
																		resourceStartT = resourceStartT[0];
																		var resourceEndT = resources.map(function(resource) { return resource.extendedProps.end_time });
																		resourceEndT = resourceEndT[0];
																		
																			 if(new_patient==''){
																			Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
																			return false;
																			}												
																			
																			
																			if(new_status==''){
																			Swal.fire({text:'{{__("Please choose a status")}}',icon:'error',customClass:'w-auto'});
																			return false;
																			}
																																										
																		  if(new_exam==''){
																			Swal.fire({text:'{{__("Please choose an exam")}}',icon:'error',customClass:'w-auto'});
																			return false;
																			}
																			//check time difference less than 15  minutes
																			
																			if(diff<15){
																			 Swal.fire({text:'{{__("Please choose two times of difference at least 15 minutes")}}',icon:'error',customClass:'w-auto'});
																			 return false;
																			}
																			var exam_duration = $('#exam_duration').val();
																			
																			
																				if(new_start_time<resourceStartT || new_start_time>resourceEndT
																				   || new_end_time<resourceStartT || new_end_time>resourceEndT){
																				 Swal.fire({text:'{{__("Please choose a time interval between")}}'+' '+resourceStartT+' '+'{{__("and")}}'+' '+resourceEndT,icon:'error',customClass:'w-auto'});
																				 return false;
																				}
																			
																			
																			var evt_date =  $('input[name=event_date]').val();
																			var evt_start_time = $('input[name=event_start_time]').val();
																			var evt_end_time = $('input[name=event_end_time]').val();

																			var new_start = moment(evt_date+" "+evt_start_time).format("YYYY-MM-DD HH:mm");
																			var new_end = moment(evt_date+" "+evt_end_time).format("YYYY-MM-DD HH:mm");
																			
																			 
																			$.ajax({

																				url: CRUDEVTS ,

																				data: {

																					title: new_title,
																					status: new_status,
																					patient: new_patient,
																					exam: new_exam,
																					start: new_start,
																					end: new_end,
																					note: new_note,
																					id: id,
																					type: 'update'

																				},

																				type: "POST",

																				success: function (data) {
																				   if(data.error){
																					 Swal.fire({icon:'error',html:data.error,customClass:'w-auto'});	
																					}else{
																					
																						// if saved, close modal
																						$("#eventModal").modal('hide');
																						$('#eventModal').find('#delete-event').hide();
																						
																						displayMessage("{{__('Event Updated Successfully')}}");
																						calendar.refetchEvents();
																					}
																				}

																			});
																		
																	});
																	 $("#eventModal").find('#delete-event').off().on('click', function(e) {
																		  e.preventDefault();
																		  var delMsg = confirm("{{__('Do you really want to delete this event?')}}");
																			if(	delMsg ){
																				
																				$.ajax({

																					type: "POST",

																					url: CRUDEVTS ,

																					data: {

																							id: id,

																							type: 'delete'

																					},

																					success: function (response) {
																					   if(response.success){
																						   $("#eventModal").modal('hide');
																						   $('#eventModal').find('#delete-event').hide();
																							info.event.remove();
																							calendar.refetchEvents();

																							displayWarnMessage("{{__('Event Deleted Successfully')}}");
																					    }
																					  if(response.error){
																						  Swal.fire({text:response.error,icon:"error",customClass:"w-auto"});
																					  }	

																					}

																				});
																			}
																	 });	
													}//end edit action
													
													
													if(key=='delete'){
														//same as event click delete event
														var delMsg = confirm("{{__('Do you really want to delete this event?')}}");
														if(	delMsg ){
														
														$.ajax({

															type: "POST",

															url: CRUDEVTS ,

															data: {

																	id: info.event.id,

																	type: 'delete'

															},

															success: function (response) {
															   if(response.success){
																info.event.remove();
																calendar.refetchEvents();

																displayWarnMessage("{{__('Event Deleted Successfully')}}");
															   }
															    if(response.error){
																	Swal.fire({text:response.error,icon:"error",customClass:"w-auto"});
																		 }	

															}

														});
														}
													}//end delete action
										            //stripe payment right click
													if(key=='payment'){
														$.ajax({
															type: "POST",
															url: CRUDEVTS ,
															data: {id: info.event.id,type: 'payment'},
															
															success: function (response) {
															  if(response.error){
																Swal.fire({html:response.msg,icon:'error',customClass:'w-auto'});  
															  }
															  if(response.success){
															    window.location.href=response.url;
															   }
															   }

														});
													}//end of stripe payment right click
																	
												},
																			
												items: my_items
																										
											});
											
											
											$('.context-menu-one'+info.event.id+code).on('click', function(e){
												
                                              
											})    
											
											info.el.className = info.el.className + ' context-menu-one'+info.event.id+code;
											
											
											
										}	
									}			
							}
							

                });
         
		
		 
		 calendar.render();
		
  
  
  $('#selectResource').change(function(){
			  
			  var pro = $('#selectResource').val();
			  $('#docID').val(pro);
			  calendar.refetchResources();
			  calendar.refetchEvents();			  
			        		 
			  
		  });
   		  
				
 $('#eventModal').on('hidden.bs.modal', function () {
	$('#exam_list').val('');
	$('#event_title').val('');
	$('#status_list').val('2');
	//$('#event-form').trigger("reset");
	//$('.select2_modal').trigger('change.select2');
	
	    })

 
 $(document.body).on("change","#selectPatient",function(){
		
		$('#dropped_patients_table').DataTable().ajax.reload();
		 });
	

  

$("#exam_list").off().on('change',function (e) {
	e.preventDefault();	
	var exam_id = $("#exam_list").val();
	if(exam_id != null && exam_id != ''){	
		$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				});
		$.ajax({
				type: "GET",
				url: "{{route('external_patient.get_exam_duration',app()->getLocale())}}" ,
				data: {
					id_exam: $("#exam_list").val(),
				},
				success: function (response) {
				   //alert(response.duration);
				   //$('#event_end_time').val($('#event_start_time').val());
				   $('#exam_duration').val(response.duration);
				   if(response.duration !='' && response.duration !=null){
				   var duration = response.duration;
				   var start_time = $('#event_start_time').val();
				   var end_time=moment(start_time,'HH:mm').add( duration,'minutes').format('HH:mm');
				   $('#event_end_time').val(end_time);
				   }
				}
			});	 
		}
	 });


	
 $("#event_start_time").change(function () {	
	
	flatpickr('#event_end_time',{
		allowInput: true,
		enableTime: true,
		noCalendar: true,
		defaultHour: 8,
		minTime: $('#event_start_time').val(),
		dateFormat: "H:i",
		time_24hr: true,
		disableMobile: true
		
	});
 });



});//end document ready
	 
    
    /*------------------------------------------

    --------------------------------------------

    Toastr Success and Fail Code

    --------------------------------------------

    --------------------------------------------*/
    
    
	function displayMessage(message) {
         toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-bottom-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
        
		toastr.success('',message);

    } 
	
	function displayErrorMessage(message) {
         toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-bottom-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
        toastr.error('',message);

    } 
	
	function displayWarnMessage(message) {
        toastr.options = {
		  "closeButton": false,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-bottom-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
        toastr.info('',message);

    } 

</script>
<script>
function checkIOS(){
	return [
    'iPad Simulator',
    'iPhone Simulator',
    'iPod Simulator',
    'iPad',
    'iPhone',
    'iPod'
  ].includes(navigator.platform)
  // iPad on iOS 13 detection
  || (navigator.userAgent.includes("Mac") && "ontouchend" in document)
  
	/*var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (isIOS) {
	 console.log('This is a IOS device');
	
    } else {
	 return false;
	 console.log('This is Not a IOS device');
	}*/		   
											
}
</script>


@endsection