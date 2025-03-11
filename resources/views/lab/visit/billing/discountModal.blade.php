<!--Discount modal-->
			<div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Discount')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								    <div class="container-fluid">
						                   <div class="row">   
												
												<div class="col-md-8">
													<label for="name" class="label-size">{{__('Patient name')}}</label>
													<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
												</div>
												<div class="col-md-4">
													<label class="label-size" for="name">{{__('Bill Nb')}}</label>
													<input  class="form-control"  value="{{$ReqPatient->clinic_bill_num}}" id="reqIDDisc"  disabled />
													<input  type="hidden" id="cptd" value="{{$cptdCount}}"/>

												</div>	
											</div>
										    <div class="row">   
												<div class="col-md-4">	 
													<label class="label-size" for="name">{{__('Date/Time')}}</label>
													<input type="text" class="form-control" name="date_discount" id="date_discount" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}"/>
												</div>
												<!--<div class="col-md-3">
												   <label class="label-size" for="name">{{__('Type')}}</label>
												   <select class="custom-select rounded-0"   id="selectmethoddiscount" name="selectmethoddiscount">
															@foreach($methodepay as $methodepays)
															<option value="{{$methodepays->id}}">
															{{$methodepays->name}}
															</option>
															@endforeach 
													</select>
												</div>-->
												  <div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
								               <select class="form-control"   id="selectmethoddiscount" name="selectmethoddiscount">
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
												<select class="select2_ref_modal custom-select rounded-0" name="disGuarantor"   id="disGuarantor" style="width:100%;">
														<option value="">{{__('Guarantors')}}</option>
														@foreach($ext_labs as $lab)
														 <option value="{{$lab->id}}">{{$lab->full_name}}</option>
														@endforeach
												</select>
											</div>	
											    <div class="col-md-1">
												   <label class="label-size" for="name">{{__('Currency')}}</label>
													<select class="form-control" name="selectcurrencyd"  id="selectcurrencyd" style="width:100%;">
																				@foreach($currencys as $currencydisc)
																							<option value="{{$currencydisc->id}}">
																							{{$currencydisc->abreviation}}
																							</option>
																							@endforeach 
												     </select>				
												</div>
											    <div class="col-md-2">
											      <label class="label-size" for="name">{{__('Percentage%')}}</label>
											      <input  class="form-control"  value="" id="dis_percent"  onkeypress="return isNumberKey(event,this)"/> 
										        </div>
												<div class="col-md-2">
													<label class="label-size" for="name">{{__('Amount')}}</label>
													<input  class="form-control"  value="" id="valamountdiscount" onkeypress="return isNumberKey(event,this)"/> 
											    </div>
																		
											<div class="col-md-1">
											<div class="d-flex flex-column">
											 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
											 <button class="btn btn-action"  id="btnadddis" onclick="insRowDis()">{{__('Add')}}</button>
											</div>
										   </div>					
										</div>
									    <!--<div class="row">  
																
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="1.0" id="dvaldollar"  disabled /> 
										</div>
										 			
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Amount LBP')}}</label>
											 <input  class="form-control"  value="0" id="dvallira"  disabled /> 
										</div>
										</div>-->
										<div class="row">  
											 <div class="col-md-2" style="display:none;">
												<label class="label-size" for="name">{{__('ٌRate')}}</label>
												 <input  class="form-control"  value="" id="dvaldollar" value="1.0"  disabled /> 
											 </div>
											 <div class="col-md-2" style="display:none;">
												<label class="label-size" for="name">{{__('Amount LBP')}}</label>
												 <input  class="form-control"  value="" id="dvallira" value="0" disabled /> 
											</div>
									    </div>
										
										<div id="htmlTableRef" class="row mt-2 m-1">
											<div class="table-responsive">
												<table id="myTableDis" class="table table-striped table-bordered table-hover" style="text-align:center;">
																					   
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
														  <td><input  class="billcss form-control"  value="{{$balance}}" id="balancediscount"  disabled /></td>
														  <td> <input  class="billcss form-control" value=""  id="payamountdiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="disdiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamountdiscount"  disabled /></td>
														 </tr>
														 <tr>
														  <th>USD</th>
														  <td><input  class="billcss form-control" value="{{$stotal}}" id="totalrefd"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balanced}}" id="balancediscountd"  disabled /></td>
														  <td><input  class="billcss form-control" value=""  id="payamountdiscountd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="disdiscountd"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamountdiscountd"  disabled /></td>
														 </tr>
													   </tbody>
													 </table>
													 </div>
													
						             </div>	
                                  </div>
							</div>	  
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsavediscount" name="btnsavediscount" onClick="event.preventDefault();savediscount()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
				
</div>		
</div>		
</div>					
<!--end DISCOUNTModal-->	  	  