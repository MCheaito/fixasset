<!-- The Modal -->
<div class="modal hide fade in" id="patientCardModal"  tabindex="-1" data-keyboard="false" data-backdrop="false" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl movableDialog">
        <div class="modal-content">
		     <div class="modal-header card-menu">
			    <ul class="nav nav-pills" style="font-size:1rem;">
					<li class="nav-item"><a class="nav-link active" href="#patient_data" data-toggle="tab">{{__('Patient')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_events" data-toggle="tab">{{__('Appointments')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_visits" data-toggle="tab">{{__('Visits')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_hist" data-toggle="tab">{{__('History')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_treatment_plans" data-toggle="tab">{{__('Treatment plan')}}</a></li>
				    <li class="nav-item"><a class="nav-link" href="#patient_medical_documents" data-toggle="tab">{{__('General documents')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_rx" data-toggle="tab">{{__('RX')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_cmds" data-toggle="tab">{{__('Orders')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_bills" data-toggle="tab">{{__('Medical Billings')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_inventory_bills" data-toggle="tab">{{__('Inventory Billings')}}</a></li>
					<li class="nav-item"><a class="nav-link" href="#patient_med_presc" data-toggle="tab">{{__('Medicines')}}</a></li>
				</ul>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body">
               <div class="tab-content">
						  <div class="tab-pane active" id="patient_data">
						  </div>
						  <div class="tab-pane" id="patient_events">
						  </div>
						  <div class="tab-pane" id="patient_visits">
						  </div>
						  <div class="tab-pane" id="patient_hist">
						  </div>
						  <div class="tab-pane" id="patient_treatment_plans">
						  </div>
						  <div class="tab-pane" id="patient_medical_documents">
						  </div>
						  <div class="tab-pane" id="patient_rx">
						  </div>
						  <div class="tab-pane" id="patient_cmds">
						  </div>
						  <div class="tab-pane" id="patient_bills">
						  </div>
						   <div class="tab-pane" id="patient_inventory_bills">
						  </div>
						  <div class="tab-pane" id="patient_med_presc">
						  </div>
				</div>		  
            </div>
        </div>
    </div>
</div>	
