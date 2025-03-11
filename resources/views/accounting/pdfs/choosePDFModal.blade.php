<div class="modal fade" id="choosePDFModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__("Print")}}</h5>
       
      </div>
      <div class="modal-body">
            <form id="form_pdf_choice">
			 <div class="m-1"> 
			  <input id="pdf_id" type="hidden"/>
			  @if($ReqPatient->type != '1')
			  <select class="custom-select rounded-0" name="pdf_desc" id="pdf_desc">
			     <option value="O">{{__("With description")}}</option>
				 <option value="N">{{__("Without description")}}</option>
			  </select>
			  @endif
		     </div>
			 <div  class="m-1 form-check form-check-inline">
				  <input class="form-check-input" type="radio" name="pdf_type" id="payment" value="Payment" checked>
				  <label class="form-check-label" for="payment">
				  @switch($ReqPatient->type)
				   @case('1') {{__("Payment receipt")}} @break
				   @case('2') {{__("Payment receipt")}} @break
				   @case('3') {{__("Return Patient")}} @break
				   @case('99') {{__("Warranty")}} @break
				  @endswitch
				  </label>
			</div>
			<div id="all_pdf" class="m-1 form-check form-check-inline" style="display:none;">
			  <input class="form-check-input" type="radio" name="pdf_type" id="all" value="All">
			  <label class="form-check-label" for="all">{{__("All")}}</label>
			</div>
		  </form>
      </div>
      <div class="modal-footer justify-content-center">
        <button id="choose_pdf" type="button" class="btn btn-action">{{__("OK")}}</button>
        <button type="button" class="btn btn-delete" data-dismiss="modal">{{__("Close")}}</button>
      </div>
    </div>
  </div>
</div>