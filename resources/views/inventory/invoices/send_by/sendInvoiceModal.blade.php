<!--
   DEV APP
   Created date : 26-4-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="sendInvoiceModal"  tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
		     <div class="modal-header card-menu">
			    <h5>{{__('Patient')}}</h5>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body">
				  <input type="hidden" id="ext_inv_id"/>
				  <input type="hidden" id="ext_pat_id"/>
				  <div id="patient_data"></div> 
            </div>
        </div>
    </div>
</div>	