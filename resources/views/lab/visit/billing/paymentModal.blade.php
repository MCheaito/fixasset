<!--payment modal-->
			<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg text-white" style="padding-top:2px;padding-bottom:1px;">
								<h4 class="modal-title">{{__('Payment')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body" style="padding-top:2px;padding-bottom:1px;">
						        <div class="container-fluid">
									<div class="row">   
									    <div class="col-md-6">
										<label class="label-size" for="name">{{__('Patient name')}}</label>
										<input type="text" class="form-control"	value="{{$patient->first_name.' '.$patient->last_name}}" disabled />
									   </div>
									   <div class="col-md-3">
											<label class="label-size" for="name">{{__('Bill Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="{{$ReqPatient->clinic_bill_num}}" disabled />   
											<input  type="hidden" id="cptp" value="{{$cptpCount}}"/>
									   </div>
									  <div class="col-md-3">	 
										  <label class="label-size" for="name">{{__('Date/Time')}}</label>
										  <input type="text" class="form-control" name="date_pay" id="date_pay" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
										</div>
									  </div> 
									   <div class="row">   
									    
										 <div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
											   <select class="form-control"   id="selectmethod" name="selectmethod">
																						@foreach($methodepay as $methodepays)
																						@if(strtolower(trim($methodepays->name))!='percentage'))
																						<option value="{{$methodepays->id}}">
																						{{$methodepays->name}}
																						</option>
																						@endif
																						@endforeach 
												</select>
										   </div>
										   <div class="col-md-4">
												<label class="label-size" for="name">{{__('Guarantor')}}</label>
												<select class="select2_pay_modal custom-select rounded-0" name="payGuarantor"   id="payGuarantor" style="width:100%;">
														<option value="">{{__('Guarantors')}}</option>
														@foreach($ext_labs as $lab)
														 <option value="{{$lab->id}}">{{$lab->full_name}}</option>
														@endforeach
												</select>
											</div>	
											
											<div class="col-md-1">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="form-control" name="selectcurrencyp"  id="selectcurrencyp" style="width:100%;">
																				@foreach($currencys as $currencypays)
																						<option value="{{$currencypays->id}}">
																						{{$currencypays->abreviation}}
																						</option>
																						@endforeach 
												</select>				
											</div>
										 			
										<div class="col-md-2">
											<label class="label-size" for="name">{{__('Percentage%')}}</label>
											 <input  class="form-control"  value="" id="pay_percent"  onkeypress="return isNumberKey(event,this)"/> 
										</div>
										<div class="col-md-2">
											<label class="label-size" for="name">{{__('Amount')}}</label>
											 <input  class="form-control"  value="" id="valamount"  onkeypress="return isNumberKey(event,this)"/> 
										</div>
										<div class="col-md-1">
											<div class="d-flex flex-column">
											 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
											 <button class="btn btn-action"  id="btnaddpay" onclick="insRowPay()">{{__('Add')}}</button>
											</div>
										</div>		 
										</div>			
										<div class="row">  
																
										<div class="col-md-2 col-6" style="display:none;">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="" id="valdollar" value="1.0" disabled /> 
										</div>
										 			
										<div class="col-md-2 col-6" style="display:none;">
											<label class="label-size" for="name">{{__('Amount LBP')}}</label>
											 <input  class="form-control"  value="" id="vallira"  value="0" disabled /> 
										</div>
										
									</div>
									
									  
							
							
								<div id="htmlTablePay" class="row mt-2 m-1">
									<div class="table-responsive">
										<table id="myTablePay" class="table table-striped table-bordered table-hover" style="text-align:center;">
										
										   
										</table> 
									</div>
							   </div>		
						        <div class="row m-1">
							            <div class="table-responsive col-md-12">
													 <table class="table-bordered table-sm" style="font-size:14px;">
													  <thead>
														  <tr class="text-center">
															<th></th>
															<th>Total</th>
															<th>Remianing</th>
															<th>Total Payment</th>
															<th>Total Discount</th>
															<th>Total Donate</th>
														   </tr>
													   </thead>
													   <tbody>
													     <tr>
														  <th>LBP</th>
														  <td><input  class="billcss form-control" value="{{$totalf}}" id="totalpay"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balance}}" id="balancepay"  disabled /></td>
														  <td> <input  class="billcss form-control" value=""  id="payamount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="paydiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamount"  disabled /></td>
														 </tr>
														 <tr>
														  <th>USD</th>
														  <td><input  class="billcss form-control" value="{{$stotal}}" id="totalpayd"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balanced}}" id="balancepayd"  disabled /></td>
														  <td><input  class="billcss form-control" value=""  id="payamountd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="paydiscountd"  disabled /></td>
 														  <td><input  class="billcss form-control"  value="" id="refamountd"  disabled /></td>
														 </tr>
													   </tbody>
													 </table>
													 </div>
										
										
						
						         </div>
                              </div>
							</div>
						    <div class="modal-footer justify-content-center" style="padding-top:2px;padding-bottom:1px;">
								 <button class="btn btn-action" id="btnsavepay" name="btnsavepay" onClick="event.preventDefault();savepay()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> 
			</div> 
					 <!--end paymentModal-->