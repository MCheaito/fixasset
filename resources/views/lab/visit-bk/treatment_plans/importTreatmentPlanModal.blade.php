<!--
   DEV APP
   Created date : 10-3-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="importTreatmentPlanModal"  tabindex="-1" data-keyboard="false" data-backdrop="false" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl movableDialog">
        <div class="modal-content">
		     <div class="modal-header card-menu">
			    <h5 id="modal_title"></h5>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body">
						  <input type="hidden" id="current_visit_num" name="current_visit_num"/>
						  
						  <div id="patient_treatment_plans">
						  </div>
						  
            </div>
        </div>
    </div>
</div>	
