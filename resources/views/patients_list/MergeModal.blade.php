<!-- 
 DEV APP
 Created date : 15-4-2023
-->
<div class="modal fade" id="MergeModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
	   <div class="modal-content movableDialog">
                <div class="modal-header">
                     <h5>{{__('New Patient Merge')}}</h5>
					 <button type="button" class="pull-right btn btn-sm" data-dismiss="modal" aria-label="Close">
								 <i class="fas fa-times"></i> 
					 </button>
                </div>
				<div class="modal-body">
				       <div class="row m-1">
											
																				
											    <div id="NewPay" class="container-fluid  border  border-radius">
												  <div  class="mt-1 col-sm-12 col-md-12" >
														<div class="row">
															  
																<div class="mb-2 col-md-6">
																<label for="patient" class="label-size">{{__('Patient')}}</label>
																<select class="select2_merge_modal custom-select rounded-0" name="newMergeNameEN" id="newMergeNameEN" style="width:100%;">
																	
																</select>
																</div>
															 
															   <div class="m-1 col-md-12 text-center">							    
																<button  id="save_new_merge" class="btn btn-action">{{__("Save")}}</button>						
																<button  id="cancel_new_merge"  data-dismiss="modal" class="btn btn-delete">{{__("Cancel")}}</button>						
															   </div>
																						   
														</div>
													</div>
				                                </div>
												
                                        </div>
				</div>
		</div>
    </div>
</div>		 