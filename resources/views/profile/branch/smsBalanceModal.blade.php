<!-- 
 DEV APP
 Created date : 15-4-2023
-->
<div class="modal fade" id="smsBalanceModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
	   <div class="modal-content movableDialog">
                <div class="modal-header">
                     <h5>{{__('New SMS balance')}}</h5>
					 <button type="button" class="pull-right btn btn-sm" data-dismiss="modal" aria-label="Close">
								 <i class="fas fa-times"></i> 
					 </button>
                </div>
				<div class="modal-body">
				       <div class="row m-1">
											
																				
											    <div class="container-fluid  border  border-radius">
												  <div  class="mt-1 col-sm-12 col-md-12" >
														<div class="row">
															   <div class="col-md-12">	
												                  <label class="label-size" for="newPack">{{__("New balance")}}</label>
												                   <input type="number" id="newPack" name="newPack"  value="0" class="form-control" onkeypress="return isNumberKey(event)"/>
											                    </div>
											                    <div class="col-md-12">
											                      <button id="add_new_sms_balance" class="m-1 btn btn-sm btn-icon btn-action">{{__('Save')}}</button>
											                      <button data-dismiss="modal" class="m-1 btn btn-sm btn-icon btn-delete">{{__('Cancel')}}</button>
																 </div>		   
														</div>
													</div>
				                                </div>
												
                                        </div>
				</div>
		</div>
    </div>
</div>		 