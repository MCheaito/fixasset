<!--Discount modal-->
			<div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Discount')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								    <div class="container">
						                <div class="row">   
						                    <div class="col-md-6">
												<label class="label-size" for="name">{{__('Bill Nb')}}</label>
												<input  class="form-control"  value="{{$ReqPatient->clinic_bill_num}}" id="reqIDDisc"  disabled />
												
										    </div>	
											<div class="col-md-6">
												<label for="name" class="label-size">{{__('Patient name')}}</label>
												<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
											</div>
												<div class="row">   
											<div class="col-md-4">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_discount" id="date_discount" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
											</div>
									        <div class="col-md-3">
											   <label class="label-size" for="name">{{__('Type')}}</label>
								               <select class="custom-select rounded-0"   id="selectmethoddiscount" name="selectmethoddiscount">
														@foreach($methodepay as $methodepays)
														<option value="{{$methodepays->id}}">
														{{$methodepays->name}}
														</option>
														@endforeach 
												</select>
								            </div>
											<div class="col-md-2">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="custom-select rounded-0" name="selectcurrencyd"  id="selectcurrencyd" style="width:100%;">
																			@foreach($currencys as $currencydisc)
																						<option value="{{$currencydisc->id}}">
																						{{$currencydisc->abreviation}}
																						</option>
																						@endforeach 
												</select>				
													</div>
						  				   <div class="col-md-3">
												<label class="label-size" for="name">{{__('Amount')}}</label>
								                <input  class="form-control"  value="" id="valamountdiscount" /> 
										   </div>
										 							
										
										</div>
											<div class="row">  
																
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="" id="dvaldollar"  disabled /> 
										</div>
										 			
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('Amount L.L')}}</label>
											 <input  class="form-control"  value="" id="dvallira"  disabled /> 
										</div>
										
									</div>
										</div> 
										</div>
										</div>
									
						
					<div class="row m-1">
							<div class="col-md-3">
												<label class="label-size" for="name">{{__('Total')}}</label>
												<input  class="form-control" value="" id="totald"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="" id="balancediscount"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Payment amount')}}</label>
													 <input  class="form-control" value=""  id="payamountdiscount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimburse amount')}}</label>
												<input  class="form-control"  value="" id="refamountdiscount"  disabled /> 
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