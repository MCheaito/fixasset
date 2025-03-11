<!-- 
 DEV APP
 Created date : 10-2-2024
-->
<div class="modal fade" id="codesModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content movableDialog">
            <div class="modal-header" style="padding-bottom:0.1rem;padding-top:0.1rem;">
                <h5 class="modal-title">{{__('Codes list')}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-bottom:0.1rem;padding-top:0.1rem;">
             <div class="container-fluid p-0"> 
			  <div class="row"> 
			   <div class="col-md-6 mt-1 mb-2">
				  <textarea name="coll_notes" class="form-control" placeholder="{{__('Please write your general note here')}}" id="coll_notes" rows="1"></textarea>
			   </div>
			   <div class="col-cmd-3 mt-1 mb-2">
			      
				  <button type="button"   class="btn btn-action" onclick="event.preventDefault();printPDF()">{{__('Request')}}<i title="{{_('Print')}}" class="ml-1 far fa-file-pdf text-primary"></i></button>
			   	  
				  <button type="button" id="print_lbl" class="btn btn-action" onclick="event.preventDefault();printLBL()">{{__('Label')}}<i title="{{_('Print')}}" class="ml-1 far fa-file-pdf text-primary"></i></button>

			   </div>
			   <div class="col-md-3 mt-1 mb-2 text-right">
			    <input type="hidden" id="modal_order_id"/>
				<input type="hidden" id="modal_daily_serial_nb"/>
				<!--only for lab users-->
				@if(auth()->user()->type==2)
				<button type="button" class="btn btn-action" onclick="event.preventDefault();save_data();">{{__('Save')}}</button>
				@endif
				<button type="button" data-dismiss="modal" class="btn btn-delete">{{__('Close')}}</button>
			   </div>
			   <div class="col-md-12 mb-1">
			    <div id="codes_list_table" style="font-size:0.9rem;"></div>  
			   </div>
               </div>
			  </div> 
           </div>

    </div>
   </div>
</div>