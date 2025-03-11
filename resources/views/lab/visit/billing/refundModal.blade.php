<!--refund modal-->
			<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header txt-bg text-white" style="padding-top:2px;padding-bottom:1px;">
								<h4 class="modal-title">{{__('Donate')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body" style="padding-top:2px;padding-bottom:1px;">
								    <div class="container-fluid">
						                 <div class="row">   
						                    
											<div class="col-md-6">
												<label for="name" class="label-size">{{__('Patient name')}}</label>
												<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Bill Nb')}}</label>
												<input  class="form-control"  value="{{$ReqPatient->clinic_bill_num}}" id="reqIDRef"  disabled />
												<input  type="hidden" id="cptr" value="{{$cptrCount}}"/>
										    </div>	
											<div class="col-md-3">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_refund" id="date_refund" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
											</div>
										  </div>	
										  <div class="row">   
											
											
									        <div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
								               <select class="form-control"   id="selectmethodrefund" name="selectmethodrefund">
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
												<select class="select2_ref_modal custom-select rounded-0" name="refGuarantor"   id="refGuarantor" style="width:100%;">
														<option value="">{{__('Guarantors')}}</option>
														@foreach($ext_labs as $lab)
														 <option value="{{$lab->id}}">{{$lab->full_name}}</option>
														@endforeach
												</select>
											</div>	
											<div class="col-md-1">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="form-control" name="selectcurrencyr"  id="selectcurrencyr" style="width:100%;">
																			@foreach($currencys as $currencyrefs)
																						<option value="{{$currencyrefs->id}}">
																						{{$currencyrefs->abreviation}}
																						</option>
																						@endforeach 
												</select>				
											</div>
											<div class="col-md-2">
											  <label class="label-size" for="name">{{__('Percentage%')}}</label>
											  <input  class="form-control"  value="" id="ref_percent"  onkeypress="return isNumberKey(event,this)"/> 
										    </div>
						  				   <div class="col-md-2">
												<label class="label-size" for="name">{{__('Amount')}}</label>
								                <input  class="form-control"  value="" id="valamountrefund" onkeypress="return isNumberKey(event,this)"/> 
										   </div>
										   <div class="col-md-1">
											<div class="d-flex flex-column">
											 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
											 <button class="btn btn-action"  id="btnaddref" onclick="insRowRef()">{{__('Add')}}</button>
											</div>
										   </div>											
										
										  </div>
										<div class="row">  
											 <div class="col-md-2" style="display:none;">
												<label class="label-size" for="name">{{__('ٌRate')}}</label>
												 <input  class="form-control"  value="" id="rvaldollar" value="1.0"  disabled /> 
											 </div>
											 <div class="col-md-2" style="display:none;">
												<label class="label-size" for="name">{{__('Amount LBP')}}</label>
												 <input  class="form-control"  value="" id="rvallira" value="0" disabled /> 
											</div>
									    </div>
										
										<div id="htmlTableRef" class="row mt-2 m-1">
											<div class="table-responsive">
												<table id="myTableRef" class="table table-striped table-bordered table-hover" style="text-align:center;">
																					   
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
														  <td><input  class="billcss form-control" value="{{$totalf}}" id="totalref"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balance}}" id="balancerefund"  disabled /></td>
														  <td> <input  class="billcss form-control" value=""  id="payamountrefund"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refdiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamountrefund"  disabled /></td>
														 </tr>
														 <tr>
														  <th>USD</th>
														  <td><input  class="billcss form-control" value="{{$stotal}}" id="totalrefd"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balanced}}" id="balancerefundd"  disabled /></td>
														  <td><input  class="billcss form-control" value=""  id="payamountrefundd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refdiscountd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamountrefundd"  disabled /></td>
 														 </tr>
													   </tbody>
													 </table>
													 </div>
													
										
										</div>
                                    </div>
							    </div>		
							<div class="modal-footer justify-content-center" style="padding-top:2px;padding-bottom:1px;">
							    <button class="btn btn-action" id="btnsaverefund" name="btnsaverefund" onClick="event.preventDefault();saverefund()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
				
		    </div>
           </div>
         </div>		   
			<!--end RefundModal-->	  	  