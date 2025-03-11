<!--
 DEV APP
 Created date : 27-10-2022
-->
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header">
				  <div class="card-title">
				  <h5>{{__('New Lab Bill').'-'.$clinic->full_name}}</h5>
				  				  
				  </div>
				  <div class="card-tools">
				   <button type="buttonf" class="btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
				  </div>	
				</div>
				<div class="card-body">
					<div class="row m-1">
					   <div class="mb-1 col-md-2 col-6">
							 <input  type="text" class="form-control" name="reqID_val"  value="" placeholder="{{__('Bill Nb')}}" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="bill_id"   value=""/>
                             <input  type="hidden"  id="id_facility"   value="{{$clinic->id}}"  />   
					   </div>
					   <div class="mb-1 col-md-2 col-6">
							 <input type="text" name="bill_date_val" class="form-control form-control-border" placeholder="{{__('Bill date')}}" value="{{$date_bill}}" disabled />
							 <input  type="hidden" autoComplete="false"   id="bill_date"   value="{{$date_bill}}"/>  
					    </div>
					   <div class="mb-1 col-md-8 text-right">
						<!--<button class="btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>
						<button class="btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentbill()">{{__('Pay-up')}}</button>
				       	<button class="btn btn-action" id="btnrefund" name="btnrefund" onClick="refundbill()">{{__('Donate')}}</button>
						<button class="btn btn-action" id="btndiscount" name="btndiscount" onClick="discountbill()">{{__('Discount')}}</button>-->
					   	<a href="{{ route('lab.billing.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>
					   </div>	
					  
				   </div>
					<div  class="row m-1">  
						  
						  <div class="col-md-4">
							<label class="label-size" for="patname"><b>{{__('Patient')}}</b></label>
							<input type="text" name="patname" class="form-control form-control-border" value="{{isset($patient->middle_name) && $patient->middle_name!=''?$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name:$patient->first_name.' '.$patient->last_name}}" disabled />
							<input  type="hidden" id="id_patient"   value="{{$patient->id}}"/>  
						</div>
                        <div class="col-md-4">
							<label class="label-size" for="pro"><b>{{__('Guarantor')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectpro"   id="selectpro" style="width:100%;"  >
										<option value="0">{{__('Undefined')}}</option>
												
												@foreach($ext_labs as $l)
													<option value="{{$l->id}}" {{isset($selectgrntr) && $l->id==$selectgrntr ? 'selected' : ''}}>{{$l->full_name}}</option>
												@endforeach
								
							</select>
						</div>
						<div class="col-md-4">
							<label class="label-size" for="pro"><b>{{__('Doctor')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectdc"   id="selectdc" style="width:100%;"  >
										<option value="0">{{__('Choose a doctor')}}</option>
												
												@foreach($doctors as $l)
													<option value="{{$l->id}}">{{$l->name}}</option>
												@endforeach
								
							</select>
						</div>	
					</div>
					<div class="row m-1 mt-2">
						<div class="col-md-4">
							<label class="label-size" for="selectcode"><b>{{__('Test Name')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectcode"  id="selectcode" style="width:100%;">

									<option value="0">{{__('Choose a test')}}</option>																
									@foreach($code as $codes)
									<option value="{{$codes->id}}">{{$codes->name}}</option>
									@endforeach 
							</select>
							<input  type="hidden" id="typeCode"/>	
						</div>				
						<div class="col-md-2" style="display: none;">>
								<label class="label-size" for="name"><b>{{__('Quantity')}}</b></label>
								<input  class="form-control" value="" id="valqty"/>
								<input  type="hidden" id="cpt"/>
								<input  type="hidden" id="tbl"/>
								<input  type="hidden" id="cnss"/>
						</div>
						<div class="col-md-2" style="display:none;">
								<label class="label-size" for="name"><b>{{__('Nb of L')}}</b></label>
								<input  class="form-control" value="" id="valnbl"/>
						</div>
						
						<div class="col-md-2">
								<label class="label-size" for="name"><b>{{__('Price USD')}}</b></label>
								<input  class="form-control" value="" id="valpriced" disabled />
						</div>
						
						<div class="col-md-2">
								<label class="label-size" for="name"><b>{{__('Price LBP')}}</b></label>
								<input  class="form-control" value="" id="valprice" disabled />
						</div>
						
						<div class="col-md-2" style="display: none;">
								<label class="label-size" for="name"><b>{{__('Price EUR')}}</b></label>
								<input  class="form-control" value="" id="valpricee" disabled />
						</div>
						<div class="col-md-2" style="display: none;">
								<label class="label-size" for="name"><b>{{__('Discount')}}</b></label>
								<input  class="form-control" id="valdiscount" value=""/>
						</div>
						<div class="col-md-2" style="display: none;">
								<label class="label-size" for="name"><b>{{__('Total')}}</b></label>
								<input  class="form-control" id="valsom" value=""/>
						</div>
						<div class="col-md-2">
							<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-action"  id="btnadd" onclick="insRow()">{{__('Add')}}</button>
							</div>
						</div>		   
		            </div>
					<div class="row m-1 mt-2">
					<div class="col-md-8">
								<label class="label-size" for="name"><b>{{__('Remark')}}</b></label>
								<textarea  class="form-control" name="notes" id="notes">{{old('notes')}}</textarea>
						</div>
					 </div>
		        </div>
			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card card-outline card-info">
				<div class="card-body">
						<div id="htmlTable" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTable" class="table table-striped table-bordered table-hover" style="text-align:center;">
									<thead>
										<tr class="txt-bg text-white text-center">
										   <th scope="col" style="font-size:16px;">#</th>
											<th scope="col" style="display:none;font-size:16px;">{{__('Test')}}</th>
											<th scope="col" style="font-size:16px;">{{__('CNSS')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Test name')}}</th>
											<th scope="col" style="display:none;font-size:16px;">{{__('Nb L')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Price USD')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Price LBP')}}</th>
											
											<th scope="col"></th>
										</tr>
									</thead>
								   <tbody></tbody>
								</table> 
							</div>
						</div>
					<!--	<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
						   <div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Discount')}}</label>
							   <input  class="text-center form-control"  id="totaldiscount"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   
							</div>
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Total USD')}}</label>
							   <input  class="text-center form-control"  id="stotal"   disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4">
							     <label for="name">{{__('Total EUR')}}</label>
							   <input  class="text-center form-control"  id="etotal"   disabled  /> 
							</div>
						    
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Total LBP')}}</label>
							   <input  class="text-center form-control"  id="totalf"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Remaining')}}</label>
							   <input  class="text-center form-control"  id="balance"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Payment')}}</label>
							   <input  class="text-center form-control"  id="tpay"  disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Refund')}}</label>
							   <input  class="text-center form-control"  id="trefund"  disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4" style="display: none;">
							   <label for="name">{{__('Tax')}}</label>
							   <select class="custom-select rounded-0"   id="selecttax" name="selecttax" disabled >
											@foreach($rates as $r)
											<option value="{{$r->id}}" {{$clinic->bill_tax==$r->id?'selected':''}}>{{$r->name}}</option>
											@endforeach
							   </select>
							</div>
							<div class="mb-1 col-md-1 col-4" style="display: none;">
							   <label for="name">{{__('QST')}}</label>
							   <input  class="text-center form-control"  id="qst"   disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4" style="display: none;">
							   <label for="name">{{__('GST')}}</label>
							   <input  class="text-center form-control"  id="gst"   disabled  />
							</div>
							
						
							
							<div class="mb-1 d-none d-md-block col-md-6"></div>
							
						</div>-->
					<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
						<div class="form-group col-md-8">
		   <div id="bill-table" class="table-bordered table-sm"></div>
	   </div>
	   <div class="table-responsive form-group col-md-12">
			 <table class="table-bordered table-sm">
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
					  <td><input  value="" class="form-control"  id="totalf"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="balance"  value="" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpay"  value="" disabled  /></td>
					  <td><input  class="billcss form-control"  value="" id="tdiscount"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefund"  value="" disabled  /></td>
				   </tr>
				   <tr>
				      <th>$</th>
					  <td ><input  class="form-control"  id="stotal"  value="" disabled  /></td>
					  <td><input  class="billcss form-control"  id="balanced"  value="" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpayd"  value="" disabled  /></td>
					  <td><input  class="billcss form-control"  value="" id="tdiscountd"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefd"  value="" disabled  /></td>
				   </tr>
			   </tbody>
			 </table>
			</div>
			</div>
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									  <button class="btn btn-action" id="btnsave" name="btnsave" onClick="savebill()">{{__('Save')}}</button>
									  <button class="btn btn-action" id="btnmodify" name="btnmodify" onClick="modifybill()">{{__('Modify')}}</button>
									   <button class="btn btn-action" id="btncancel" name="btncancel" onClick="cancelbill()">{{__('Cancel')}}</button>
									</div>	
									
						</div>					  
				</div>
            </div>				
		</section>
			<!--payment modal-->
			<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="modal-header txt-bg">
								<h4 class="modal-title">{{__('Pay-up')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
						    <div class="modal-body">
						        <div class="container">
									<div class="row">   
									   <div class="col-md-6">
											<label class="label-size" for="name">{{__('Bill Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="" disabled />   
											<input  type="hidden" id="cptp" value=""/>
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
											 <input  class="form-control"  value="" id="valdollar"  disabled /> 
										</div>
										 			
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('Amount L.L')}}</label>
											 <input  class="form-control"  value="" id="vallira"  disabled /> 
										</div>
										
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
												<input  class="form-control" value="" id="totalpay"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Remaining')}}</label>
											<input  class="form-control"  value="" id="balancepay"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Pay-up amount')}}</label>
													 <input  class="form-control" value=""  id="payamount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Donate amount')}}</label>
												<input  class="form-control"  value="" id="refamount"  disabled /> 
											</div>
						</div>			
						    <div class="modal-footer justify-content-center">
								 <button class="btn btn-action" id="btnsavepay" name="btnsavepay" onClick="savepay()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					</div>
				</div> 
			</div> 
			</div> 
					 <!--end paymentModal-->
			 
			<!--refund modal-->
			<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Donate')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								    <div class="container">
						                <div class="row">   
						                    <div class="col-md-6">
												<label class="label-size" for="name">{{__('Bill Nb')}}</label>
												<input  class="form-control"  value="" id="reqIDRef"  disabled />
												<input  type="hidden" id="cptr" value=""/>
										    </div>	
											<div class="col-md-6">
												<label for="name" class="label-size">{{__('Patient name')}}</label>
												<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
											</div>
												<div class="row">   
											<div class="col-md-3">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_refund" id="date_refund" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
											</div>
									        <div class="col-md-2">
											   <label class="label-size" for="name">{{__('Type')}}</label>
								               <select class="custom-select rounded-0"   id="selectmethodrefund" name="selectmethodrefund">
														@foreach($methodepay as $methodepays)
														<option value="{{$methodepays->id}}">
														{{$methodepays->name}}
														</option>
														@endforeach 
												</select>
								            </div>
											<div class="col-md-2">
											   <label class="label-size" for="name">{{__('Currency')}}</label>
												<select class="custom-select rounded-0" name="selectcurrencyr"  id="selectcurrencyr" style="width:100%;">
																			@foreach($currencys as $currencyrefs)
																						<option value="{{$currencyrefs->id}}">
																						{{$currencyrefs->abreviation}}
																						</option>
																						@endforeach 
												</select>				
													</div>
						  				   <div class="col-md-3">
												<label class="label-size" for="name">{{__('Amount')}}</label>
								                <input  class="form-control"  value="" id="valamountrefund" /> 
										   </div>
										   <div class="col-md-2">
										<div class="d-flex flex-column">
										 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
										 <button class="btn btn-action"  id="btnaddref" onclick="insRowRef()">{{__('Add')}}</button>
										</div>
										</div>											
										
										</div>
											<div class="row">  
																
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('ٌRate')}}</label>
											 <input  class="form-control"  value="" id="rvaldollar"  disabled /> 
										</div>
										 			
										<div class="col-md-5">
											<label class="label-size" for="name">{{__('Amount L.L')}}</label>
											 <input  class="form-control"  value="" id="rvallira"  disabled /> 
										</div>
										
									</div>
										</div> 
										</div>
										</div>
										<div id="htmlTableRef" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTableRef" class="table table-striped table-bordered table-hover" style="text-align:center;">
									
								</table> 
						</div>		
						
					<div class="row m-1">
							<div class="col-md-3">
												<label class="label-size" for="name">{{__('Total')}}</label>
												<input  class="form-control" value="" id="totalrefund"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Remaining')}}</label>
											<input  class="form-control"  value="" id="balancerefund"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Pay-up amount')}}</label>
													 <input  class="form-control" value=""  id="payamountrefund"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Donate amount')}}</label>
												<input  class="form-control"  value="" id="refamountrefund"  disabled /> 
											</div>
						</div>										
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsaverefund" name="btnsaverefund" onClick="saverefund()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
				
		    </div>		
</div>		
</div>		
</div>					
			<!--end RefundModal-->	  	  
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
												<input  class="form-control"  value="" id="reqIDDisc"  disabled />
												<input  type="hidden" id="cptr" value=""/>
										    </div>	
											<div class="col-md-6">
												<label for="name" class="label-size">{{__('Patient name')}}</label>
												<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
											</div>
												<div class="row">   
											<div class="col-md-3">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_discount" id="date_discount" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
											</div>
									        <div class="col-md-2">
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
											<label class="label-size" for="name">{{__('Remaining')}}</label>
											<input  class="form-control"  value="" id="balancediscount"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Pay-up amount')}}</label>
													 <input  class="form-control" value=""  id="payamountdiscount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Donate amount')}}</label>
												<input  class="form-control"  value="" id="refamountdiscount"  disabled /> 
											</div>
						</div>										
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsavediscount" name="btnsavediscount" onClick="savediscount()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
				
</div>		
</div>		
</div>					
			<!--end DISCOUNTModal-->	  	  
    </div>
</div>	
@endsection	
@section('scripts')
<script>
$(document).ready(function(){
	        $("#etotal").val("0.00");
			$("#stotal").val("0.00");
			$("#totalf").val("0.00");
			$("#valnbl").val("0.00");
			$("#valsom").val("0.00");
	        $("#cpt").val("0");
			$("#cptp").val("0");
			$("#cptr").val("0");
			$("#valqty").val("1");
			$("#valprice").val("");
			$("#valpriced").val("");
			$("#valpricee").val("");
			$("#valdiscount").val("0.00");
			$("#totalf").val("0.00");
			$("#tdiscount").val("0.00");
			$("#totaldiscount").val("0.00");
			$("#qst").val("0.00");
			$("#gst").val("0.00");
			$("#balance").val("0.00");
			$("#payamount").val("0.00");
			$("#refamount").val("0.00");
			$("#valamount").val("0.00");
			$("#tbl").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#btndiscount").prop("disabled", true);
			$("#btncancel").prop("disabled", true);
			$("#totalrefund").val("0.00");
			$("#valamountrefund").val("0.00");
			$("#balancerefund").val("0.00");
			$("#tpay").val("0.00");
			$("#trefund").val("0.00");
			$("#vallira").val("0.00");
			$("#valdollar").val("1.00");
			$("#rvallira").val("0.00");
			$("#rvaldollar").val("1.00");
			$("#dvaldollar").val("1.00");
			$("#valeuro").val("1.00");
			$("#rvaluro").val("1.00");
			$("#stotal").val("0.00");
			$("#balanced").val("0.00");
			$("#tpayd").val("0.00");
			$("#tdiscountd").val("0.00");
			$("#trefd").val("0.00");
		//var totalf=$("#totalf").val();
	//	var valamount=$("#valamount").val();
		//var selecttax="1";
	//$.ajax({
	//	url:'fillTax',
	//	 data:{"selecttax":selecttax,"valamount":valamount,"totalf":totalf},
	//	   type: 'get',
	//	  dataType: 'json',
	//	  success: function(data){
	//		  $("#qst").val(data.qst);
	//		  $("#gst").val(data.gst);
			 
	//	 }
     // });

	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});

	  
$('#selecttax').on('change', function()
{
   selecttax=$("#selecttax").val();
   totalf=document.getElementById("totalf").value;
   var valamount=$("#valamount").val();
   var x = document.getElementById("myTable").rows[0].cells.length;
var arr = new Array();
for(i=0;i<document.getElementById("myTable").rows.length;i++){
arr[i]={"CODE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"TOTAL":document.getElementById("myTable").rows[i].cells[5].innerHTML};	

}
var myjson=JSON.stringify(arr);
	$.ajax({
		url: 'fillTax',
		   type: 'get',
		  dataType: 'json',
		 data:{"selecttax":selecttax,"valamount":valamount,"totalf":totalf,"data":myjson},
		  success: function(data){
			//  alert(data);
			  $("#qst").val(data.qst);
			  $("#gst").val(data.gst);
			//balance=parseFloat(totalf+qst+gst-pay);
		//	pay=$("#valamount").val();;
			 totalf=$("#totalf").val();
			 balance=parseFloat(totalf)+parseFloat(data.qst)+parseFloat(data.gst);
			 $("#balance").val(balance);
			
		 }
      });
});			
$('#selectcode').on('change', function()
{
   
   current_val=$("#selectcode").val();
   if( current_val=='0' ||  current_val==null){
	           $("#valnbl").val('');
			   $("#valprice").val('');
			   $("#valpriced").val('');
			   $("#valpricee").val('');
			   $("#valsom").val('');
			   $("#cnss").val('');
			   return;
   }
   
   id_facility=$("#id_facility").val();
    selectpro=$("#selectpro").val();
	$.ajax({
		url: 'fillPrice',
		data:{"id_patient":$('#id_patient').val(),"selectcode":current_val,"id_facility":id_facility,"selectpro":selectpro},
		   type: 'get',
		  dataType: 'json',
		  success: function(data){
			   $("#valnbl").val(data.nbl);
			   $("#valprice").val(data.pricel);
			   $("#valpriced").val(data.priced);
			   $("#valpricee").val(data.pricee);
			   $("#valsom").val(data.total);
			   $("#cnss").val(data.cnss);
		 }
      });
});



$('#selectcurrencyp').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyp").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#valdollar").val(data.pdolar);
			$("#valamount").val('0.00');
			$("#vallira").val('0.00');
			
		 }
      });
});

$('#selectcurrencyr').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyr").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#rvaldollar").val(data.pdolar);
			$("#valamountrefund").val('0.00');
			$("#rvallira").val('0.00');
			
		 }
      });
});

$('#selectcurrencyd').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyd").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#dvaldollar").val(data.pdolar);
			$("#valamountdiscount").val('0.00');
			$("#dvallira").val('0.00');
			
		 }
      });
});



$('#valamountdiscount').on('change input', function()
{
valamount=$("#valamountdiscount").val();
valdollar=$("#dvaldollar").val();
vallira=valamount*valdollar;	
$("#dvallira").val(vallira);
});

$('#valamount').on('change input', function()
{
valamount=$("#valamount").val();
valdollar=$("#valdollar").val();
vallira=valamount*valdollar;	
$("#vallira").val(vallira);
});

$('#valamountrefund').on('change input', function()
{
valamount=$("#valamountrefund").val();
valdollar=$("#rvaldollar").val();
vallira=valamount*valdollar;	
$("#rvallira").val(vallira);
});

});

 
 function insRow()
{
 current_val=$("#selectcode").val();
  if(current_val=="0"){
	   Swal.fire({ 
              "text":"{{__('Please choose a test')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
var typecode=$('#typeCode').val();
var discval=$('#valprice').val();	
if ((typecode=="2") && (discval>=0))
{
discval=-1*discval;
$('#valprice').val(discval);	
}	
var x=document.getElementById('myTable').insertRow(document.getElementById('myTable').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
//var i= x.insertCell(8);

document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;
var descripcode=$("#selectcode option:selected").text();
var totalf=($('#totalf').val()!=null && $('#totalf').val()!="")?parseFloat($('#totalf').val()).toFixed(2):0;
var stotal=($('#stotal').val()!=null && $('#stotal').val()!="")?parseFloat($('#stotal').val()).toFixed(2):0;
var valpriced =($("#valpriced").val()!="" && $("#valpriced").val()!=null)?parseFloat($("#valpriced").val()).toFixed(2):0;
var valprice=($("#valprice").val()!="" && $("#valprice").val()!=null)?parseFloat($("#valprice").val()).toFixed(2):0;
	
	a.innerHTML=document.getElementById("cpt").value;
	b.style.display="none";
	b.innerHTML=document.getElementById("selectcode").value;
	c.innerHTML=document.getElementById("cnss").value;
	d.innerHTML=descripcode;
	e.style.display="none";
	e.innerHTML=document.getElementById("valnbl").value;
	f.innerHTML=valpriced;
	g.innerHTML=valprice;
	h.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}" onclick="deleteRow(this)"/>';

   var selcode=$("#selectcode").val();
   var cnss=$("#cnss").val();
   var selectpro=$("#selectpro").val();
   var valnbl=$("#valnbl").val();
    //valprice=(document.getElementById("valprice").value).replaceAll(",", "");
    //valpriced=(document.getElementById("valpriced").value).replaceAll(",", "");
   //  valpricee=(document.getElementById("valpricee").value).replaceAll(",", "");
    var cpt=document.getElementById("cpt").value;
    var tpay=document.getElementById("tpay").value;
	var trefund=document.getElementById("trefund").value;
    var tdiscount=document.getElementById("tdiscount").value;
	$("#btnsave").removeAttr('disabled');
	
}
function deleteRow(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valnbl=parseInt(document.getElementById("myTable").rows[ii].cells[4].innerHTML);
 //var  valpricee=parseFloat(document.getElementById("myTable").rows[ii].cells[6].innerHTML);
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[6].innerHTML);
 var  valpriced=parseFloat(document.getElementById("myTable").rows[ii].cells[5].innerHTML);
 var  code=parseInt(document.getElementById("myTable").rows[ii].cells[1].innerHTML);
var  totalf=(document.getElementById("totalf").value).replaceAll(",", "");
var  stotal=(document.getElementById("stotal").value).replaceAll(",", "");
//var  etotal=(document.getElementById("etotal").value).replaceAll(",", "");

var tdiscount=parseFloat(document.getElementById("tdiscount").value);
	
			 totalf=parseFloat((totalf)-(valprice)).toFixed(2);
 			 stotal=parseFloat((stotal)-(valpriced)).toFixed(2);
			//  etotal=parseFloat((etotal)-(valpricee)).toFixed(2);
			// tdiscount=parseFloat(parseFloat(tdiscount)-parseFloat(valdiscount)).toFixed(2);
			tpay=parseFloat(document.getElementById("tpay").value);
			trefund=parseFloat(document.getElementById("trefund").value);
			//$("#stotal").val(stotal);
			// $("#balance").val(balance);
			// $("#totalf").val(totalf);
			  $("#tdiscount").val(tdiscount);
			 document.getElementById('myTable').deleteRow(ii);
			if(document.getElementById('myTable').rows.length==1){
			//$("#balance").val("0.00");
			//$("#balance").val(tpay-trefund);
			$("#totalf").val("0.00");
			$("#stotal").val("0.00");
     		$("#etotal").val("0.00");
			$("#tdiscount").val("0.00");
					
			//$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btndiscount").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);	
			$("#btnadd").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			//nbalance=$("#tpay").val()-$("#trefund").val();
			//$("#balance").val(nbalance);
			//$("#cpt").val("0.00");
}
}

 function insRowPay()
{
	 current_val=$("#selectmethod").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
var x=document.getElementById('myTablePay').insertRow(document.getElementById('myTablePay').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#selectmethod option:selected").text().trim();
d.innerHTML=$("#selectcurrencyp option:selected").text().trim();
e.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(2);
f.innerHTML=parseFloat(document.getElementById("valdollar").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("vallira").value).toFixed(2);

h.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';
//var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)+parseFloat(document.getElementById("vallira").value)).toFixed(2);
//document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)-parseFloat(document.getElementById("valamount").value)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
	$("#btnsavepay").prop("disabled", false);	
}

function deleteRowPay(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[6].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
// var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)-parseFloat(valpay)).toFixed(2);
//document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)+parseFloat(valpay)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
//if(document.getElementById('myTablePay').rows.length==1){
//			$("#payamount").val("0.00");
			
			
//}
$("#btnsavepay").prop("disabled", false);		
}

function insRowRef()
{
	
	 current_val=$("#selectmethodrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	


var x=document.getElementById('myTableRef').insertRow(document.getElementById('myTableRef').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#selectmethodrefund option:selected").text().trim();
d.innerHTML=$("#selectcurrencyr option:selected").text().trim();
e.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(2);
f.innerHTML=parseFloat(document.getElementById("valdollar").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("rvallira").value).toFixed(2);
h.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
//var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(2);
//document.getElementById("refamountrefund").value=totalref;
	$("#btnsaverefund").prop("disabled", false);	
  	
}
function deleteRowRef(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[6].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
 //var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)-parseFloat(valref)).toFixed(2);
//document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)-parseFloat(valref)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
//if(document.getElementById('myTableRef').rows.length==1){
	//		$("#refamount").val("0.00");
			
			
//}
$("#btnsaverefund").prop("disabled", false);		
}



function savebill()
{
//for <td>
//var x = document.getElementById("myTable").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
var len = document.getElementById("myTable").rows.length;
if(len==1){
	Swal.fire({text:'{{__("Please choose at least one test")}}',icon:'warning',customClass:'w-auto'});
	return;
}

for(i=0;i<document.getElementById("myTable").rows.length;i++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr[i]={
	    "CODE":document.getElementById("myTable").rows[i].cells[1].innerHTML,
		"CNSS":document.getElementById("myTable").rows[i].cells[2].innerHTML,
		"DESCRIP":document.getElementById("myTable").rows[i].cells[3].innerHTML,
		"NBL":document.getElementById("myTable").rows[i].cells[4].innerHTML,
		"PRICEL":document.getElementById("myTable").rows[i].cells[6].innerHTML,
		"PRICED":document.getElementById("myTable").rows[i].cells[5].innerHTML,
		};	

}

var answer = confirm ("{{__('Are you sure?')}}")

   selcode=$("#selectcode").val();
   selectpro=$("#selectpro").val();
   selectdc=$("#selectdc").val();
    totalf=(document.getElementById("totalf").value).replaceAll(",", "");
   // tdiscount=document.getElementById("tdiscount").value;
	tdiscount=(document.getElementById("tdiscount").value).replaceAll(",", "");
   // tdiscount=0.00;
  // tdiscount=document.getElementById("totaldiscount").value;
     id_patient=$("#id_patient").val();
     id_facility=$("#id_facility").val();
     bill_date=$("#bill_date").val();
     balance=$("#balance").val();
     bill_id=$("#bill_id").val();
     reqID=$("#reqID").val();
     stotal=(document.getElementById("stotal").value).replaceAll(",", "");
     tpay=$("#tpay").val();
	 trefund=$("#trefund").val();
	 notes=$("#notes").val();
   Mastatus='N';
   if (bill_id!='')
   {
	 Mastatus='M';   
   }
if (answer){
var myjson=JSON.stringify(arr);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}",data:myjson,"selectdc":selectdc,"id_facility":id_facility,"selcode":selcode,"selectpro":selectpro,"totalf":totalf,"tdiscount":tdiscount,"id_patient":id_patient,"bill_date":bill_date,"stotal":stotal,"status":Mastatus,"bill_id":bill_id,"reqID":reqID,"tpay":tpay,"trefund":trefund,"notes":notes},
    url: '{{route("SaveBill",app()->getLocale())}}',
    success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "title":data.success,
              "icon":"success",
			  "toast":true,
			  "showConfirmButton":false,
			  "timer":2500,
			  "position":"bottom-right"
			  
			  });
		  	window.location.href=data.location;  
			} 
		
    }
 });
}
else{

}
}
function paymentbill()
{
var bill_id=document.getElementById("bill_id").value;
$("#valamount").val("0.00");
$.ajax({
	url:'{{route("GetPay",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id},
     success: function(data){
	 $("#payamount").val(data.sumpay);
    $("#refamount").val(data.sumref);
	 $("#balancepay").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
	
}
    });	
$('#paymentModal').modal('show');
var totalpay=(document.getElementById("totalf").value);
//var balancepay=parseFloat(document.getElementById("balance").value);
  $("#totalpay").val(totalpay);
  //$("#balancepay").val(balancepay);
}
function modifybill()
{			$("#btncancel").removeAttr('disabled');
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#btndiscount").prop("disabled", true);
			$("#btnprint").prop("disabled", true);				
		    $("#btnadd").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
				$("#notes").removeAttr('disabled');
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//	$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = false;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
}

function cancelbill()
{			
			$("#btncancel").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btnprint").removeAttr('disabled');	
			$("#btnpayment").removeAttr('disabled');			
		    $("#btnmodify").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			$("#btndiscount").removeAttr('disabled');
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = true;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
}

function savepay()
{
var x = document.getElementById("myTablePay").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTablePay").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTablePay").rows[ii].cells[2].innerHTML,"CURRENCY":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,"PRICE":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,"RATE":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML,"TOTAL":document.getElementById("myTablePay").rows[ii].cells[6].innerHTML};	

}
	
var id_facility=document.getElementById("id_facility").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var bill_id=document.getElementById("bill_id").value;
  var balance=document.getElementById("balancepay").value;	
 // if(selectmethod=="0"){
	//   Swal.fire({ 
      //        "text":"Veuillez choisir une méthode",
        //      "icon":"warning",
		//	  "customClass": "w-auto"});
		  
		//  return ;	
		
	//		 }	

var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson1,"balance":balance},
   url: '{{route("SavePay",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
         $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		   $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
      //   $('#paymentModal').modal('hide');	
		var b1=$("#balance").val();
		 var p1=$("#tpay").val();
$("#btnsavepay").prop("disabled", true);  	
	//	 alert(b1);
	//	 alert(p1);
	//var nbalance=parseFloat(data.nbalance).toFixed(2);
	//alert(nbalance);
	$("#balance").val(data.nbalance);
	$("#balancepay").val(data.nbalance);
}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
}
function refundbill()
{
var bill_id=document.getElementById("bill_id").value;
$("#valamountrefund").val("0.00");
$.ajax({
	url:'{{route("GetRef",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"REF"},
     success: function(data){
	 $("#payamountrefund").val(data.sumpay);
    $("#refamountrefund").val(data.sumref);
 	 $("#balancerefund").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html);
	
}
    });	
$('#refundModal').modal('show');
var totalrefund=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totalrefund").val(totalrefund);
//$("#balancerefund").val(balancerefund);
}


function saverefund()
{
	

 var x = document.getElementById("myTableRef").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr2 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(iii=0;iii<document.getElementById("myTableRef").rows.length;iii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr2[iii]={"CODE":document.getElementById("myTableRef").rows[iii].cells[0].innerHTML,"DATE":document.getElementById("myTableRef").rows[iii].cells[1].innerHTML,"TYPE":document.getElementById("myTableRef").rows[iii].cells[2].innerHTML,"CURRENCY":document.getElementById("myTableRef").rows[iii].cells[3].innerHTML,"PRICE":document.getElementById("myTableRef").rows[iii].cells[4].innerHTML,"RATE":document.getElementById("myTableRef").rows[iii].cells[5].innerHTML,"TOTAL":document.getElementById("myTableRef").rows[iii].cells[6].innerHTML};	

}
	
var id_facility=document.getElementById("id_facility").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var bill_id=document.getElementById("bill_id").value;
  var balance=document.getElementById("balancerefund").value;	
 // if(selectmethod=="0"){
	//   Swal.fire({ 
      //        "text":"Veuillez choisir une méthode",
        //      "icon":"warning",
		//	  "customClass": "w-auto"});
		  
		//  return ;	
		
	//		 }	

var myjson2=JSON.stringify(arr2);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson2,"balance":balance},
   url: '{{route("SaveRefund",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
         $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
		var b1=$("#balance").val();
		 var p1=$("#tpay").val();

	//	 alert(b1);
	//	 alert(p1);
	//var nbalance=parseFloat(data.nbalance).toFixed(2);
	$("#btnsaverefund").prop("disabled", true);  	

	//alert(nbalance);
	$("#balance").val(data.nbalance);
	$("#balancerefund").val(data.nbalance);
}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
 
 
 
 
}
function savediscount()
{
	

 
var id_facility=document.getElementById("id_facility").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var bill_id=document.getElementById("bill_id").value;
  var balance=document.getElementById("balancerefund").value;	
  var valamountdiscount=document.getElementById("valamountdiscount").value;	
  $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,"balance":balance,'valamountdiscount':valamountdiscount},
   url: '{{route("SaveDiscount",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
         $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
		  $("#totaldiscount").val(data.totaldiscount);
		var b1=$("#balance").val();
		 var p1=$("#tpay").val();

	//	 alert(b1);
	//	 alert(p1);
	//var nbalance=parseFloat(data.nbalance).toFixed(2);
	$("#btnsaverefund").prop("disabled", true);  	

	//alert(nbalance);
	$("#balance").val(data.nbalance);
	$("#balancerefund").val(data.nbalance);
}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
 
 
 
 
}

function downloadPDF(){	 
   var id=document.getElementById("bill_id").value;	

      $.ajax({
           url: '{{route("downloadPDFBilling",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Bills.pdf');
			link.click();
			Swal.fire({title:'{{__("Bill Downloaded")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
			//window.location.href="{{route('lab.billing.index',app()->getLocale())}}";

		
			    });
	
	
	
	}
function discountbill()
{
$("#valamountdiscount").val("0.00");
var bill_id=document.getElementById("bill_id").value;
$.ajax({
	url:'{{route("GetRef",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"DIS"},
     success: function(data){
	 $("#payamountdiscount").val(data.sumpay);
    $("#refamountdiscount").val(data.sumref);
 	 $("#balancediscount").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	
}
    });	
$('#discountModal').modal('show');
var totaldiscount=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totaldiscount").val(totaldiscount);
var totald=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totald").val(totald);
//$("#balancerefund").val(balancerefund);
}

</script>
<script>
        flatpickr('#date_pay', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#date_refund', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
    </script>
@endsection	
