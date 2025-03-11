<!-- 
 DEV APP
 Created date : 15-4-2023
-->
<div class="modal fade" id="payMethodModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
	   <div class="modal-content movableDialog">
                <div class="modal-header">
                     <h5>{{__('New payment method')}}</h5>
					 <button type="button" class="pull-right btn btn-sm" data-dismiss="modal" aria-label="Close">
								 <i class="fas fa-times"></i> 
					 </button>
                </div>
				<div class="modal-body">
				       <div class="row m-1">
											
																				
											    <div id="NewPay" class="container-fluid  border  border-radius">
												  <div  class="mt-1 col-sm-12 col-md-12" >
														<div class="row">
															   <div class="col-md-4">
																  <label for="newPayMethodEN" class="label-size">{{__("Name en")}}</label>
																  <input type="text"  id="newPayMethodEN" name="newPayMethodEN" class="form-control"/>
															   </div>
																<div class="col-md-4">
																  <label  for="newPayMethodFR" class="label-size">{{__("Name fr")}}</label>
																  <input type="text" id="newPayMethodFR" name="newPayMethodFR" class="form-control"/>
															   </div>
															   <div class="col-md-4">
																 <label for="haveInsurance" class="label-size">{{__("Assurance")}}</label>
																 <select class="custom-select rounded-0" id="haveInsurance" name="haveInsurance">
																   <option value="N">{{__('No')}}</option>
																   <option value="Y">{{__('Yes')}}</option>
																 </select>
															   </div>
															   <div class="m-1 col-md-12 text-center">							    
																<button  id="save_new_pay" class="btn btn-action">{{__("Save")}}</button>						
																<button  id="cancel_new_pay"  data-dismiss="modal" class="btn btn-delete">{{__("Cancel")}}</button>						
															   </div>
																						   
														</div>
													</div>
				                                </div>
												
                                        </div>
				</div>
		</div>
    </div>
</div>		 