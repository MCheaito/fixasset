<!-- Modal -->
<div class="modal fade" id="NewVisitModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:0.1rem;padding-bottom:0.1rem;">
        <h5 class="modal-title" id="NewVisitModal">{{__('All Lab Requests')}}</h5>
        <input type="hidden" class="form-control" name="pat_id" id="pat_id"/> 
		<input type="hidden" class="form-control" name="pat_branch" id="pat_branch"/> 
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"  style="padding-top:0.1rem;padding-bottom:0.1rem;">
        <form id="newrx_form">
			<div class="m-1 row">
			   <div class="mb-1 col-md-6">
				<input type="text" class="form-control" name="patient_name" id="patient_name" disabled /> 
			   </div>
			   <div class="mb-1 col-md-6 text-right">
			  	 <a id="new_pat_order" target="_blank" href="{{route('lab.visit.edit',app()->getLocale())}}" class="btn-sm btn btn-action" onclick="new_visit();">{{__('New lab request')}}</a>
 			     <button type="button" class="btn btn-delete btn-sm" data-dismiss="modal">{{__('Close')}}</button>
			   </div>
			   <div class="mb-1 col-md-6">
				 <label for="name" class="label-size">{{__('From date')}}</label>
				 <input autocomplete="false" type="text" class="form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}" value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
			  </div>
			  <div class="mb-1 col-md-6">
				 <label for="name" class="label-size">{{__('To date')}}</label>
				 <input autocomplete="false" type="text" class="form-control" name="filter_todate" id="filter_todate" placeholder="{{__('Choose a date')}}"  value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
			   </div>
			   <div  class="col-md-12">
				  <div class="all_visit table-bordered table-sm"></div>
			   </div>
			 </div>  
		 </form>
          		 
      </div>
      
    </div>
  </div>
</div>