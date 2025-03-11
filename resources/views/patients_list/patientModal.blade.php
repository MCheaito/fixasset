<!-- 
 DEV APP
 Created date : 24-3-2024
-->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"  aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
	   <div class="modal-content">
                <div class="modal-header p-1">
                     <h4>{{isset($patient) ? __('Edit Patient'): __('New Patient')}}</h4>
					 <button type="button" class="pull-right btn btn-sm" data-dismiss="modal" aria-label="Close">
								 <i class="fas fa-times"></i> 
					 </button>
                </div>
				<div class="modal-body">
				   

				</div>
		</div>
    </div>
</div>		 