<!--
    DEV APP
    Created date : 28-7-2022
 -->
<!--add new event modal-->
	<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg movableDialog">
        <div class="modal-content">
            <div class="modal-header p-1">
                <h4 class="modal-title">{{__('Reservation')}}</h4>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{__('Close')}}</span></button>

            </div>
			<div class="modal-body">
                <form id="event-form">
					<input type="hidden" id="exam_duration"/>
					<div class="row">
					  <div class="col-md-12 text-right">
					     <button id="save-event" class="btn btn-action" onclick="event.preventDefault();">{{__('Save')}}</button>
						 <button type="reset" class="btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
						 <button  id="delete-event" class="btn btn-delete" onclick="event.preventDefault();">{{__('Delete')}}</button>
						 
					  </div>
					</div>
					<div class="row">					
						<div class="col-md-6 form-group">
							<label>{{__('Doctor')}}</label>
							<input type="text" readonly="true" name="pro_name" class="form-control" />
						</div>
						
						<div class="col-md-6 form-group">
							<label>{{__('Exam Details')}}</label>
								<select id="exam_list" name="exam_id" class="custom-select rounded-0" style="width:100%;">
								   
									 <option value="" selected>{{__('Choose an exam')}}</option>
								   
								  @foreach($exams as $exam)
								   @php $name = app()->getLocale()=='en'? $exam->name_eng:$exam->name_fr; @endphp
								   <option value="{{$exam->id}}">
								   @if($exam->duration != NULL && $exam->duration != '' )
								    @php 
								         $mins =$exam->duration;
										 $h = floor($mins/60);
										 $m =  $mins - $h*60;
										 $h = ($h<10)? '0'.$h : $h;
								         $m = ($m<10)? '0'.$m : $m;
										 $d = $h.':'.$m;
									@endphp	
									{{($exam->taxable=='Y')?$name." ( ".$exam->am_code.",".__("Taxable").",".$d." ) " :	$name." ( ".$exam->am_code.",".$d." ) " }}							
							       @else
							       	{{($exam->taxable=='Y')?$name." ( ".$exam->am_code.",".__("Taxable")." ) " :	$name." ( ".$exam->am_code." ) " }}							
							       @endif
							      </option>
								  @endforeach
								</select>
							
						</div>
						
						
					</div>
					
                    <div class="row">					
						
						<div class="col-md-6 form-group">
							<label>{{__('Patient')}}</label>
								<select id="patient_list" name="patient" class="custom-select rounded-0" style="width:100%;" disabled>  
									 
					 
									   <option value="{{$patients->id}}" selected>{{$patients->patName}}</option>
								</select>
								
							 
						</div>
						<div class="col-md-6 form-group">
							<label>{{__('Title')}}</label>
							<input type="text" id="event_title" name="title" class="form-control"/>

						</div>
						
					</div>
                    <div class="row">
						<div class="col-md-6 form-group">
							<label>{{__('Branch Name')}}</label>
							<input type="text" readonly="true" name="clinic_name" class="form-control" />
						</div>
						
						<div class="col-md-6 form-group">
							<label>{{__('Appointment Status')}}</label>
							<select id="status_list" name="status" class="custom-select rounded-0" style="width:100%;" disabled>
							  @foreach($status as $stat)
							   <option value="{{$stat->id}}">{{(app()->getLocale()=='en')?$stat->state_en:$stat->state_fr}}</option>
							  @endforeach
							</select>
						</div>
						
					</div>
                    <div class="row">
                        <div class="col-md-4 form-group">
							<label>{{__('Appointment Date')}}</label>
							<input type="text" id="event_date"  name="event_date" class="form-control" disabled />
						</div>					
						<div class="col-md-4 form-group">
							<label>{{__('Appointment start time')}}</label>
							<div class="input-group">
							<span class="input-group-text">
								<i class="fa fa-clock"></i>
							</span>
							<input type="text" id="event_start_time" name="event_start_time" class="timepicker form-control" disabled />
							</div>
						</div>
						<div class="col-md-4 form-group">
							<label>{{__('Appointment end time')}}</label>
							<div class="input-group">
							<span class="input-group-text">
								<i class="fa fa-clock"></i>
							</span>
							<input type="text" id="event_end_time" name="event_end_time" class="timepicker form-control" disabled />
							</div>
						</div>
					</div>
					</form>
                    <!--<div class="row">
					    <div class="col-md-8 form-group">
							<label>{{__('Appointment note')}}</label>
							<textarea id="event_note" name="note" class="form-control" rows="1"></textarea>
							<div class="mt-1 form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_exceptional" id="exception_event">
                                <label class="form-check-label" for="exception_event">{{__('Exceptional event')}}</label>
                            </div>
						</div>
						
                    </div>-->					
                
            </div>
           
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
