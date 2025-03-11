<!--payment modal-->
			<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Payment')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body">
						        <div class="container">
									<div class="row">   
									   <div class="col-md-6">
											<label class="label-size" for="name">{{__('Bill Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="{{$ReqPatient->clinic_bill_num}}" disabled />   
											<input  type="hidden" id="cptp" value="{{$cptpCount}}"/>
									   </div>
									   <div class="col-md-6">
										<label class="label-size" for="name">{{__('Patient name')}}</label>
										<input type="text" class="form-control"	value="{{$patient->first_name.' '.$patient->last_name}}" disabled />
									   </div>
									   <div class="row">   
									   <div class="col-md-3">	 
										  <label class="label-size" for="name">{{__('Date/Time')}}</label>
										  <input type="text" class="form-control" name="date_pay" id="date_pay" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
										</div>
										<div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
											   <select class="custom-select rounded-0"   id="selectmethod" name="selectmethod"  ">
																						@foreach($methodepay as $methodepays)
																						<option value="{{$methodepays->id}}">
																						{{$methodepays->name}}
																						</option>
																						@endforeach 
												</select>
										</div>
											<div class="col-md-2">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="custom-select rounded-0" name="selectcurrencyp"  id="selectcurrencyp" style="width:100%;">
																				@foreach($currencys as $currencypays)
																						<option value="{{$currencypays->id}}">
																						{{$currencypays->abreviation}}
																						</option>
																						@endforeach 
												</select>				
													</div>
										 			
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Amount')}}</label>
											 <input  class="form-control"  value="" id="valamount"  /> 
										</div>
										<div class="col-md-2">
										<div class="d-flex flex-column">
										 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
										 <button class="btn btn-action"  id="btnaddpay" onclick="insRowPay()">{{__('Add')}}</button>
										</div>
										</div>		 
										</div>			
										<div class="row">  
																
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="" id="valdollar" value="1.00" disabled /> 
										</div>
										 			
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('Amount L.L')}}</label>
											 <input  class="form-control"  value="" id="vallira"  value="0.00" disabled /> 
										</div>
										
									</div>
									
									</div>
										 
								</div>					  
							
							
							<div id="htmlTablePay" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTablePay" class="table table-striped table-bordered table-hover" style="text-align:center;">
								
								   
								</table> 
							</div>
						</div>		
						<div class="row m-1">
							<div class="col-md-3">
												<label class="label-size" for="name">{{__('Total')}}</label>
												<input  class="form-control" value="{{$totalf}}" id="totalpay"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="{{$balance}}" id="balancepay"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Payment amount')}}</label>
													 <input  class="form-control" value=""  id="payamount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimburse amount')}}</label>
												<input  class="form-control"  value="" id="refamount"  disabled /> 
											</div>
						</div>
                       </div>							
						    <div class="modal-footer justify-content-center">
								 <button class="btn btn-action" id="btnsavepay" name="btnsavepay" onClick="event.preventDefault();savepay()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> 
			</div> 
					 <!--end paymentModal-->