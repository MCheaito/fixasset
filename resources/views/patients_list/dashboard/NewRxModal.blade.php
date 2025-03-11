<!-- Modal -->
<div class="modal fade" id="NewRxModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="NewRxModal">{{__('New Rx')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="newrx_form">
			<div class="m-1 row">
			   <div class="form-group col-md-6">
				<label  class="label-size">{{__('Patient')}}</label>
				<input type="text" class="form-control" disabled value="{{$rx_patient->first_name.' '.$rx_patient->last_name}}"/>
			   </div>
			   <div class="form-group col-md-6">
				<label  class="label-size">{{__('Visits')}}</label>
				<select id="rx_visit" name="rx_visit" class="label-size custom-select rounded-0"  size="{{count($pat_visits)==0?1:(count($pat_visits)>=7?7:count($pat_visits)+1)}}">
				     <option value="0" selected>{{__("New visit")}}</option>
				   @if(count($pat_visits)>0)
					 @foreach($pat_visits as $visit)
				       <option value="{{$visit->id}}">{{__("Visit")." : ".$visit->id." , ".__("Date")." : ".$visit->visit_date_time}}</option>
                     @endforeach				 
				   @endif	   
				</select>
			   </div>
			 </div>  
		   
          </form>						
      </div>
      <div class="justify-content-center modal-footer">
        <button type="button" class="btn btn-action" onclick="event.preventDefault();new_rx('Rx')">{{__('Rx')}}</button>
		<button type="button" class="btn btn-action" onclick="event.preventDefault();new_rx('CLRx')">{{__('CL Rx')}}</button>
        <button type="button" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>
  </div>
</div>