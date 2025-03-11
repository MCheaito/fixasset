<!--
 DEV APP
 Created date : 4-11-2022
-->
 
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-1">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header">
				  <div class="card-title"><h5>{{__('Bill').'-'.$clinic->full_name}}</h5></div>
				  <div class="card-tools">
				   <button type="button" class="btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
				  </div>	
				</div>
				<div class="card-body">
					<div class="row m-1">
					    <div class="mb-1 col-md-2 col-6">
							 <input  type="text" class="form-control" name="reqID_val"  placeholder="{{__('Bill Nb')}}" value="{{$ReqPatient->clinic_bill_num}}" id="reqID"  disabled /> 
							 <input  type="hidden"  id="bill_id"   value="{{$ReqPatient->id}}"/>
                             <input  type="hidden"  id="id_facility"   value="{{$clinic->id}}"  />   
						  </div>
						  <div class="mb-1 col-md-2 col-6">
							 <input type="text" name="bill_date_val" placeholder="{{__('Bill date')}}" class="form-control form-control-border" value="{{$ReqPatient->bill_datein}}" disabled />
							 <input  type="hidden"  id="bill_date"   value="{{$ReqPatient->bill_datein}}"/>  
						  </div>
						<div class="mb-1 col-md-8 text-right">
						   <button class="btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>
						   <button class="btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentbill()">{{__('Pay')}}</button>
						   <button class="btn btn-action" id="btnrefund" name="btnrefund" onClick="refundbill()">{{__('Donate')}}</button>
						   <button class="btn btn-action" id="btndiscount" name="btndiscount" onClick="discountbill()">{{__('Discount')}}</button>
						   <a href="{{ route('lab.billing.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>
						</div>	
						
					</div>
					<div  class="row m-1">  
						  
						  <div  class="col-md-4">
							<label class="label-size" for="patname"><b>{{__('Patient')}}</b></label>
							<input type="text" name="patname" class="form-control form-control-border" value="{{isset($patient->middle_name) && $patient->middle_name!=''?$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name:$patient->first_name.' '.$patient->last_name}}" disabled />
							<input  type="hidden" id="id_patient"   value="{{$patient->id}}"/>  
						</div>
                        <div class="col-md-4">
							<label class="label-size" for="pro"><b>{{__('Guarantor')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectpro"   id="selectpro" style="width:100%;" disabled >
										<option value="0">{{__('Undefined')}}</option>
										@foreach($ext_labs as $l)
										<option value="{{$l->id}}" {{isset($ReqPatient->ext_lab) && $l->id==$ReqPatient->ext_lab ? 'selected' : ''}}>{{$l->full_name}}</option>
										@endforeach
							</select>
						</div>
						<div class="col-md-4">
							<label class="label-size" for="pro"><b>{{__('Doctor')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectdc"   id="selectdc" style="width:100%;"  disabled>
										<option value="0">{{__('Choose a doctor')}}</option>
												
												@foreach($doctors as $l)
													<option value="{{$l->id}}" {{isset($ReqPatient->doctor_num) && $l->id==$ReqPatient->doctor_num ? 'selected' : ''}}>{{$l->name}}</option>
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
						<div class="col-md-2" style="display: none;">
								<label class="label-size" for="name"><b>{{__('Quantity')}}</b></label>
								<input  class="form-control" value="" id="valqty"/>
								<input  type="hidden" id="cpt" value="{{$cptCount}}"/>
								<input  type="hidden" id="tbl"/>
								<input  type="hidden" id="cnss"/>
						</div>
						<div class="col-md-2" style="display: none;">
								<label class="label-size" for="name"><b>{{__('Nb of L')}}</b></label>
								<input  class="form-control" value="" id="valnbl"/>
						</div>
												
						<div class="col-md-2">
								<label class="label-size" for="name"><b>{{__('Price USD')}}</b></label>
								<input  class="form-control" value="" id="valpriced" disabled />
						</div>
						<div class="col-md-2"  style="display: none;">
								<label class="label-size" for="name"><b>{{__('Price EUR')}}</b></label>
								<input  class="form-control" value="" id="valpricee" disabled />
						</div>
						<div class="col-md-2">
								<label class="label-size" for="name"><b>{{__('Price LBP')}}</b></label>
								<input  class="form-control" value="" id="valprice" disabled />
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
								<textarea class="form-control" id="notes" name="notes" disabled>{{$ReqPatient->notes}}</textarea>
						</div>
					 </div>
		        </div>
			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card">
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
								   <tbody>
								     @php
										$cpt = 1;
									@endphp
									@foreach ($ReqDeails as $sReqDeails) 
													<tr>
													<td>{{$cpt}}</td>
													<td style="display:none;">{{$sReqDeails->bill_code}}</td>
													<td>{{$sReqDeails->cnss}}</td>
													<td>{{$sReqDeails->bill_name}}</td>
													<td style="display:none;">{{$sReqDeails->bill_quantity}}</td>
													<td>{{ $sReqDeails->bill_price}}</td>
													<td>{{ $sReqDeails->lbill_price}}</td>
													<td><input type="button" class="btn btn-delete" id="rowdelete{{$cpt}}" value="{{__('Delete')}}" onclick="deleteRow(this)" disabled /></td>
											       </tr>
											@php
											$cpt++;
											@endphp	 
									@endforeach
								   </tbody>
								</table> 
							</div>
						</div>
					<!--	<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
						<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Discount')}}</label>
							   <input  class="text-center form-control"  value="{{$ReqPatient->bill_discount}}" id="tdiscount"   disabled  />
							</div>
							
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Total USD')}}</label>
							   <input  class="text-center form-control"  id="stotal"  value="{{$stotal}}" disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4">
							    <label for="name">{{__('Total EUR')}}</label>
							   <input  class="text-center form-control"  id="etotal" value="{{$etotal}}"  disabled  />   
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Total LBP')}}</label>
							   <input  value="{{$totalf}}" class="text-center form-control"  id="totalf"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Remaining')}}</label>
							   <input  class="text-center form-control"  id="balance"  value="{{$balance}}" disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Payment')}}</label>
							   <input  class="text-center form-control"  id="tpay"  value="{{ number_format($pay, 2, '.', ',') }}" disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Refund')}}</label>
							   <input  class="text-center form-control"  id="trefund"  value="{{ number_format($refund, 2, '.', ',') }}" disabled  />
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
							   <input  class="text-center form-control"  value="{{$ReqPatient->tvq}}" id="qst"   disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4" style="display: none;">
							   <label for="name">{{__('GST')}}</label>
							   <input  class="text-center form-control"  id="gst"  value="{{$ReqPatient->tvs}}" disabled  />
							</div>
						
							
							<div class="mb-1 d-none d-md-block col-md-2"></div>
							<div class="mb-1 d-none d-md-block col-md-6"></div>
							
						</div> -->
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
					  <td><input  value="{{$totalf}}" class="form-control"  id="totalf"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="balance"  value="{{$balance}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpay"  value="{{ number_format($pay, 2, '.', ',') }}" disabled  /></td>
					  <td><input  class="billcss form-control"  value="{{isset($ReqPatient->bill_discount) && $ReqPatient->bill_discount!=''?number_format($ReqPatient->bill_discount,2,'.',','):'0.00'}}" id="tdiscount"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefund"  value="{{ number_format($refund, 2, '.', ',') }}" disabled  /></td>
				   </tr>
				   <tr>
				      <th>$</th>
					  <td ><input  class="form-control"  id="stotal"  value="{{$stotal}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="balanced"  value="{{($balanced!='' && isset($balanced))?number_format($balanced,2,'.',''):'0.00'}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpayd"  value="{{ $sumpayd!=''?number_format($sumpayd, 2, '.', ','):'0.00' }}" disabled  /></td>
					  <td><input  class="billcss form-control"  value="{{($tdiscountd!='')?number_format((float)$tdiscountd,2,'.',''):'0.00'}}" id="tdiscountd"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefd"  value="{{ $sumrefd!=''?number_format($sumrefd, 2, '.', ','):'0.00' }}" disabled  /></td>
				   </tr>
			   </tbody>
			 </table>
			</div>
			</div>
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									  <button class="btn btn-action" id="btnsave" name="btnsave" onClick="savebill()" disabled >{{__('Save')}}</button>
									  <button class="btn btn-action" id="btnmodify" name="btnmodify" onClick="modifybill()">{{__('Modify')}}</button>
									  <button class="btn btn-action" id="btncancel" name="btncancel" onClick="cancelbill()">{{__('Cancel')}}</button>
									</div>	
									
						</div>					  
				</div>
            </div>				
		</section>
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
								    <div class="container-fluid">
						                   <div class="row">   
												
												<div class="col-md-8">
													<label for="name" class="label-size">{{__('Patient name')}}</label>
													<input class="form-control" type="text" value="{{$patient->first_name.' '.$patient->last_name}}"  disabled />
												</div>
												<div class="col-md-4">
													<label class="label-size" for="name">{{__('Bill Nb')}}</label>
													<input  class="form-control"  value="{{$ReqPatient->clinic_bill_num}}" id="reqIDDisc"  disabled />
													
												</div>	
											</div>
										    <div class="row">   
												<div class="col-md-4">	 
													<label class="label-size" for="name">{{__('Date/Time')}}</label>
													<input type="text" class="form-control" name="date_discount" id="date_discount" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
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
												   <label class="label-size" for="name">{{__('Currency')}}</label>
													<select class="form-control" name="selectcurrencyd"  id="selectcurrencyd" style="width:100%;">
																				@foreach($currencys as $currencydisc)
																							<option value="{{$currencydisc->id}}">
																							{{$currencydisc->abreviation}}
																							</option>
																							@endforeach 
												     </select>				
												</div>
											    <div class="col-md-3">
											      <label class="label-size" for="name">{{__('Percentage%')}}</label>
											      <input  class="form-control"  value="" id="dis_percent"  onkeypress="return isNumberKey(event,this)"/> 
										        </div>
												<div class="col-md-3">
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
														  <td><input  class="billcss form-control" value="{{$totalf}}" id="totald"   disabled /></td>
														  <td><input  class="billcss form-control"  value="{{$balance}}" id="balancediscount"  disabled /></td>
														  <td> <input  class="billcss form-control" value=""  id="payamountdiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="disdiscount"  disabled /></td>
														  <td><input  class="billcss form-control"  value="" id="refamountdiscount"  disabled /></td>
														 </tr>
														 <tr>
														  <th>USD</th>
														  <td><input  class="billcss form-control" value="{{$stotal}}" id="totaldd"   disabled /></td>
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
</div> 
</div><!--end of container-->  
@endsection	
@section('scripts')
<script>
$(document).ready(function(){
	
	        //$("#cpt").val("0");
			$("#valnbl").val("0.00");
			$("#valsom").val("0.00");
			$("#valqty").val("1");
			$("#valprice").val("0.00");
			$("#valpriced").val("0.00");
			$("#valpricee").val("0.00");
			$("#valdiscount").val("0.00");
			$("#vallira").val("0.00");
			$("#dvallira").val("0.00");
			$("#valdollar").val("1.00");
			$("#rvallira").val("0.00");
			$("#rvaldollar").val("1.00");
			$("#dvaldollar").val("1.00");
			//$("#qst").val("0.00");
			//$("#gst").val("0.00");
			//$("#payamount").val("0.00");
			//$("#refamount").val("0.00");
			$("#valamount").val("0.00");
			$("#tbl").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#selectpro").prop("disabled", true);	
			$("#btncancel").prop("disabled", true);
			//$("#btnpayment").prop("disabled", true);
			//$("#btnprint").prop("disabled", true);
			//$("#btnrefund").prop("disabled", true);
			//$("#totalrefund").val("0.00");
			//$("#valamountrefund").val("0.00");
			//$("#balancerefund").val("0.00");
			//alert($("#valqty").val());
		$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
		
function nbCheck(elem,value) {
     var v = parseFloat(value);
    if (isNaN(v)) {
        $(elem).val('');
		
    } else {
       if(v>0) { $(elem).val('+'+v.toFixed(2)); }
	   if(v==0) { $(elem).val('0.00'); }
	   if(v<0) { $(elem).val(v.toFixed(2));}
    }
}

// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

$('#pay_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();

    if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
	
	
	
	if (val === '') {
            $(this).val('');
			$('#valamount').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyp').val());
		var total;
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamount').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamount').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamount").val()==''?0:$("#valamount").val();
 valdollar=$("#valdollar").val();
 vallira=valamount*valdollar;
 $('#vallira').val(Math.round(vallira)); 
});

$('#selectmethodrefund').change(function(e){
	e.preventDefault();
	var method = $('#selectmethodrefund option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#refundModal').find('#refGuarantor').val($('#selectLab').val());
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',false);
	}else{
		$('#refundModal').find('#refGuarantor').val('');
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',true);
	}
});

$('#ref_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();
	if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
		
    if (val === '') {
            $(this).val('');
			$('#valamountrefund').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyp').val());
		var total;
		
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamountrefund').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamountrefund').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamountrefund").val()==''?0:$("#valamountrefund").val();
 valdollar=$("#rvaldollar").val();
 vallira=valamount*valdollar;
 $('#rvallira').val(Math.round(vallira)); 
});




$('#selectcode').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcode").val();
   id_facility=$("#id_facility").val();
    selectpro=$("#selectpro").val();
  // alert(current_val);
	$.ajax({
		
		url: '{{route("fillPrice",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"id_patient":$('#id_patient').val(),"selectcode":current_val,"id_facility":id_facility,"selectpro":selectpro},
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


$('#valamount').on('input', function(e){
 e.preventDefault();
 valamount=$("#valamount").val();
 valdollar=$("#valdollar").val();
 vallira=valamount*valdollar;	
 
 if(valamount=='' || isNaN(valamount)){
	 $('#pay_percent').val('');
 }else{
	 var currency = parseInt($('#selectcurrencyp').val());
	 var total;
	switch(currency){
		  //LBP
		  case 1:
		    total = document.getElementById('totalf').value;
			total = total.replace(/,/g, '');
			$('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('stotal').value;
		    $('#pay_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 }
 
 $("#vallira").val(Math.round(vallira));
});




$('#valamountdiscount').on('input', function(e){
e.preventDefault();
valamount=$(this).val();
valdollar=$("#dvaldollar").val();
vallira=valamount*valdollar;
if(valamount=='' || isNaN(valamount)){
	 $('#dis_percent').val('');
 }else{
	 var currency = parseInt($('#selectcurrencyd').val());
	 var total;
	switch(currency){
		  //LBP
		  case 1:
		    total = document.getElementById('totalf').value;
			total = total.replace(/,/g, '');
			$('#dis_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
	     //USD
		  case 2:
		    total = document.getElementById('stotal').value;
		    $('#dis_percent').val(parseFloat((valamount*100)/total).toFixed(2));
		  break;
		}
 } 	
$("#dvallira").val(Math.round(vallira));
});

$('#dis_percent').on('input',function(e){
	e.preventDefault();
	var val = $(this).val();
	if (!isNaN(val)) {
            // If the float value is greater than 100, set it to 100
            if (val > 100) {
                $(this).val('');
            } else if (val < 0) {
                $(this).val('');
            }
        } else {
            $(this).val('');
        }
		
    if (val === '') {
            $(this).val('');
			$('#valamountdiscount').val('');
        } else {
		var percent = parseFloat(val);
		var currency = parseInt($('#selectcurrencyd').val());
		var total;
			  switch(currency){
				  //LBP
				  case 1:
				    total = document.getElementById('totalf').value;
					total = total.replace(/,/g, '');
					$('#valamountdiscount').val(Math.round((total*percent)/100));
				  break;
				  //USD
				  case 2:
				    total = document.getElementById('stotal').value;
					$('#valamountdiscount').val(parseFloat( (total*percent)/100).toFixed(2));
				  break;
			  }
			  
			
	   }
 
 valamount=$("#valamountdiscount").val()==''?0:$("#valamountdiscount").val();
 valdollar=$("#dvaldollar").val();
 vallira=valamount*valdollar;
 $('#dvallira').val(Math.round(vallira)); 
});

	  
$('#selecttax').on('change', function()
{
	//alert("ok");
   selecttax=$("#selecttax").val();
   totalf=document.getElementById("totalf").value;
   var valamount=$("#valamount").val();
   var x = document.getElementById("myTable").rows[0].cells.length;
var arr = new Array();
for(i=0;i<document.getElementById("myTable").rows.length;i++){
arr[i]={"CODE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"TOTAL":document.getElementById("myTable").rows[i].cells[5].innerHTML};	

}
var myjson=JSON.stringify(arr);
//alert(myjson);
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
//var totalf=totalf+(parseInt(document.getElementById("valnbl").value)*parseFloat(document.getElementById("valprice").value));
//var stotal=stotal+(parseInt(document.getElementById("valnbl").value)*parseFloat(document.getElementById("valpriced").value));

a.innerHTML=document.getElementById("cpt").value;
b.style.display="none";
b.innerHTML=document.getElementById("selectcode").value;
c.innerHTML=document.getElementById("cnss").value;
d.innerHTML=descripcode;
e.style.display="none";
e.innerHTML=document.getElementById("valnbl").value;
f.innerHTML=parseFloat(document.getElementById("valpriced").value).toFixed(2);
//g.innerHTML=parseFloat(document.getElementById("valpricee").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("valprice").value).toFixed(2);
h.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}" onclick="deleteRow(this)"/>';

   selcode=document.getElementById("selectcode").value;
   cnss=document.getElementById("cnss").value;
   selectpro=document.getElementById("selectpro").value;
   valnbl=document.getElementById("valnbl").value;
   valprice=(document.getElementById("valprice").value).replaceAll(",", "");
   valpriced=(document.getElementById("valpriced").value).replaceAll(",", "");
  //  valpricee=(document.getElementById("valpricee").value).replaceAll(",", "");
   cpt=document.getElementById("cpt").value;
     tpay=document.getElementById("tpay").value;
	 trefund=document.getElementById("trefund").value;
  // tbl=$("//////9#tbl").val();
   totalf=(document.getElementById("totalf").value).replaceAll(",", "");
    stotal=(document.getElementById("stotal").value).replaceAll(",", "");
	// etotal=(document.getElementById("etotal").value).replaceAll(",", "");
   tdiscount=document.getElementById("tdiscount").value;
 //  balance=document.getElementById("balance").value;
  // alert(totalf);
	$.ajax({
	url: 'addTaping',
		   type: 'get',
		   data:{"cnss":cnss,"descrip":descripcode,"totalf":totalf,"stotal":stotal,"cpt":cpt,"selectcode":selcode,"selectpro":selectpro,"valnbl":valnbl,"valpriced":valpriced,"valprice":valprice,"tdiscount":tdiscount,"tpay":tpay,"trefund":trefund},
		  dataType: 'json',
		  success: function(data){
			//  $('#htmlTable').empty();
		//	 $('#htmlTable').html(data.htmltable);
			 if(data.warning){
			// $('#htmlTable').empty();
		   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 
			 if(data.success){	
			/*  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});*/
		   //	$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);
			
		//	document.getElementById("totalf").value=data.totalf;
		//	document.getElementById("stotal").value=data.stotal;
		//	document.getElementById("tdiscount").value=data.tdiscount;
			//document.getElementById("balance").value=data.balance;
			//document.getElementById("gst").value=data.gst;
			//document.getElementById("qst").value=data.qst;
			//document.getElementById("valdiscount").value="0.00";
		    $("#btnsave").removeAttr('disabled');
			
			} 
			 
		 }
      }); 

}
function deleteRow(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valnbl=parseInt(document.getElementById("myTable").rows[ii].cells[4].innerHTML);
 //var  valpricee=parseFloat(document.getElementById("myTable").rows[ii].cells[6].innerHTML);
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[6].innerHTML);
 var  valpriced=parseFloat(document.getElementById("myTable").rows[ii].cells[5].innerHTML);
 var  code=parseInt(document.getElementById("myTable").rows[ii].cells[1].innerHTML);
//var  totalf=(document.getElementById("totalf").value).replaceAll(",", "");
//var  stotal=(document.getElementById("stotal").value).replaceAll(",", "");
//var  etotal=(document.getElementById("etotal").value).replaceAll(",", "");

var tdiscount=parseFloat(document.getElementById("tdiscount").value);
	
			// totalf=parseFloat((totalf)-(valprice)).toFixed(2);
 			// stotal=parseFloat((stotal)-(valpriced)).toFixed(2);
			//  etotal=parseFloat((etotal)-(valpricee)).toFixed(2);
			// tdiscount=parseFloat(parseFloat(tdiscount)-parseFloat(valdiscount)).toFixed(2);
		//	tpay=parseFloat(document.getElementById("tpay").value);
			//trefund=parseFloat(document.getElementById("trefund").value);
			//$("#stotal").val(stotal);
			// $("#balance").val(balance);
			// $("#totalf").val(totalf);
		//	  $("#tdiscount").val(tdiscount);
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

 function insRowPay(){
  current_val=$("#selectmethod").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
	 
  if(parseFloat(current_val)==0 || current_val==''){
	   Swal.fire({ 
              "text":"Please input an amount",
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
var i= x.insertCell(8);
var j= x.insertCell(9);

document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#payGuarantor").val()==''?'':$("#payGuarantor option:selected").text().trim();

d.innerHTML=$("#selectmethod option:selected").text().trim();
e.innerHTML=$("#selectcurrencyp option:selected").text().trim();
f.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("valdollar").value).toFixed(2);
h.innerHTML=parseFloat(document.getElementById("vallira").value).toFixed(2);
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';	
j.style.display="none";
j.innerHTML=$("#payGuarantor").val();

}
function deleteRowPay(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[8].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
}
function insRowRef()
{
	  current_val=$("#selectmethodrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseFloat(current_val)==0 || current_val==''){
	   Swal.fire({ 
              "text":"Please input an amount",
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
var i= x.insertCell(8);
var j= x.insertCell(9);
document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#refGuarantor").val()==''?'':$("#refGuarantor option:selected").text().trim();

d.innerHTML=$("#selectmethodrefund option:selected").text().trim();
e.innerHTML=$("#selectcurrencyr option:selected").text().trim();
f.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("rvaldollar").value).toFixed(2);
h.innerHTML=parseFloat(document.getElementById("rvallira").value).toFixed(2);
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
j.style.display="none";
j.innerHTML=$("#refGuarantor").val();


}

function deleteRowRef(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[6].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
}

function modifybill()
{
			//$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			//$("#btnrefund").prop("disabled", true);
			//$("#btnprint").prop("disabled", true);				
		    $("#btnadd").removeAttr('disabled');
		//	$("#selecttax").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			 $("#selectpro").removeAttr('disabled');
			 $("#selectdc").removeAttr('disabled');
			 $("#patname").removeAttr('disabled');
			 $("#bill_date").removeAttr('disabled');
			 $("#btnpayment").prop("disabled", true);
			 $("#btndiscount").prop("disabled", true);
			 $("#btnrefund").prop("disabled", true);
			 $("#btncancel").removeAttr('disabled');
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
			$("#btndiscount").removeAttr('disabled');			
		    $("#btnmodify").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			$("#selectpro").prop("disabled", true);
			$("#selectdc").prop("disabled", true);
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

   selcode=document.getElementById("selectcode").value;
   selectpro=document.getElementById("selectpro").value;
   selectdc=document.getElementById("selectdc").value;
     totalf=(document.getElementById("totalf").value).replaceAll(",", "");
   // tdiscount=document.getElementById("tdiscount").value;
	tdiscount=(document.getElementById("tdiscount").value).replaceAll(",", "");
   // tdiscount=0.00;
  // tdiscount=document.getElementById("totaldiscount").value;
   id_patient=document.getElementById("id_patient").value;
   id_facility=document.getElementById("id_facility").value;
   bill_date=document.getElementById("bill_date").value;
   balance=document.getElementById("balance").value;
   bill_id=document.getElementById("bill_id").value;
   reqID=document.getElementById("reqID").value;
   stotal=(document.getElementById("stotal").value).replaceAll(",", "");
    tpay=document.getElementById("tpay").value;
	 trefund=document.getElementById("trefund").value;
	 notes=document.getElementById("notes").value;
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
			  "timer":3000,
			  "position":"bottom-right"
			  
			  });
		   //	$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);	
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
		 $('#totalf').val(data.totalf);
	     $('#stotal').val(data.stotal);	     
			$("#btnadd").prop("disabled", true);
		    $("#btnpayment").removeAttr('disabled');
			$("#btndiscount").removeAttr('disabled');
			 $("#btnmodify").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#selecttax").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btncancel").prop("disabled", true);
			$("#selectpro").prop("disabled", true);
			$("#notes").prop("disabled", true);
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
			//alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
			
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
    type:'POST',
    data:{_token: '{{ csrf_token() }}',bill_id:bill_id,type:'P'},
     success: function(data){
	 $("#payamount").val(data.sumpay);
     $("#refamount").val(data.sumref);
	 $("#balancepay").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#paydiscount').val(data.discount);
	 
	 //$("#totalpayd").val(data.totald);
	 $("#payamountd").val(data.sumpayd);
     $("#refamountd").val(data.sumrefd);
	 $("#balancepayd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
     $('#paydiscountd').val(data.discountd);

	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
}
    });	
$('#paymentModal').modal('show');
var totalpay=(document.getElementById("totalf").value);
//var balancepay=(document.getElementById("balance").value);
 $("#totalpay").val(totalpay);
 // $("#balancepay").val(balancepay);
}


function savepay()
{
var x = document.getElementById("myTablePay").rows[0].cells.length;
var arr1 = new Array();
for(ii=0;ii<document.getElementById("myTablePay").rows.length;ii++){
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,
          "DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,
		  "GUARANTOR":document.getElementById("myTablePay").rows[ii].cells[9].innerHTML,
		  "TYPE":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,
		  "CURRENCY":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,
		  "PRICE":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML,
		  "RATE":document.getElementById("myTablePay").rows[ii].cells[6].innerHTML,
		  "TOTAL":document.getElementById("myTablePay").rows[ii].cells[7].innerHTML};	
}
	
var id_facility=document.getElementById("id_facility").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var bill_id=document.getElementById("bill_id").value;
  var balance=(document.getElementById("balancepay").value).replaceAll(",", "");;	
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
         $('#paymentModal').modal('hide');	
		//var b1=$("#balance").val();
	//	 var p1=$("#tpay").val();
	//	 alert(b1);
	//	 alert(p1);
	//var nbalance=parseFloat(data.nbalance).toFixed(2);
	//alert(data.nbalance);
 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
	
		  $('#paymentModal').modal('hide');
		    if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                // $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
              //   $('#finalizeBill').prop('checked',false);	 				 
			  }
		  
		  
		  
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

function refundbill(){
var bill_id=document.getElementById("bill_id").value;
$("#valamountrefund").val("");
$("#ref_percent").val("");
$.ajax({
	url:'{{route("GetPay",app()->getLocale())}}',
    type:'POST',
    data:{_token: '{{ csrf_token() }}',bill_id:bill_id,type:'R'},
     success: function(data){
	 //$("#totalref").val(data.totall);
	 $("#payamountrefund").val(data.sumpay);
     $("#refamountrefund").val(data.sumref);
 	 $("#balancerefund").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#refdiscount").val(data.discount);
	 
	 //$("#totalrefd").val(data.totald);
	 $("#payamountrefundd").val(data.sumpayd);
     $("#refamountrefundd").val(data.sumrefd);
 	 $("#balancerefundd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#refdiscountd").val(data.discountd);
	 
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html1);
	
}
 });	


var method = $('#selectmethodrefund option:selected').text().trim().toLowerCase();
	if(method=='guarantor'){
		$('#refundModal').find('#refGuarantor').val($('#selectLab').val());
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',false);
	}else{
		$('#refundModal').find('#refGuarantor').val('');
        $('#refundModal').find('#refGuarantor').trigger('change.select2');
		$('#refundModal').find('#refGuarantor').prop('disabled',true);
	}

$('#refundModal').modal('show');

}


function saverefund(){
var x = document.getElementById("myTableRef").rows[0].cells.length;
var arr2 = new Array();
for(iii=0;iii<document.getElementById("myTableRef").rows.length;iii++){
   arr2[iii]={"CODE":document.getElementById("myTableRef").rows[iii].cells[0].innerHTML,
              "DATE":document.getElementById("myTableRef").rows[iii].cells[1].innerHTML,
			  "GUARANTOR":document.getElementById("myTableRef").rows[iii].cells[9].innerHTML,
			  "TYPE":document.getElementById("myTableRef").rows[iii].cells[3].innerHTML,
			  "CURRENCY":document.getElementById("myTableRef").rows[iii].cells[4].innerHTML,
			  "PRICE":document.getElementById("myTableRef").rows[iii].cells[5].innerHTML,
			  "RATE":document.getElementById("myTableRef").rows[iii].cells[6].innerHTML,
			  "TOTAL":document.getElementById("myTableRef").rows[iii].cells[7].innerHTML};	
}
	
var id_facility='{{$clinic->id}}';
var bill_id=document.getElementById("bill_id").value;
var order_id='{{isset($order)?$order->id:0}}';
var balance=document.getElementById("balancerefund").value;	
var myjson2=JSON.stringify(arr2);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson2,"balance":balance,"order_id":order_id},
    url: '{{route("SaveRefund",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
        
		  //refund modal update totals
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 $("#refdiscount").val(data.tdiscount);
		 
		 $("#payamountrefundd").val(data.sumpayd);
         $("#refamountrefundd").val(data.sumrefd);
		 $("#balancerefundd").val(data.balanced);
		 $('#refdiscountd').val(data.tdiscountd);
		 
		 //payment modal update totals
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 $("#paydiscount").val(data.tdiscount);
		 
		 $("#payamountd").val(data.sumpayd);
         $("#refamountd").val(data.sumrefd);
		 $("#balancepayd").val(data.balanced);
		 $('#paydiscountd').val(data.tdiscountd);
		 
		 //update discount totals
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 $('#disdiscount').val(data.tdiscount);
		 
		 $("#payamountdiscountd").val(data.sumpayd);
         $("#refamountdiscountd").val(data.sumrefd);
		 $("#balancediscountd").val(data.balanced);
		 $('#disdiscountd').val(data.tdiscountd);
		 
		 //update bill table totals
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
		 	     
		 
          $('#refundModal').modal('hide');

        if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
			  
		  }
        if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		      return;
			} 

	}
 });
 
}

function downloadPDF(){	 
   var id=document.getElementById("bill_id").value;	
      /*var fraction = document.getElementById("totalf").value.split(".");

                if (fraction.length == 2){
                  var tafqeetTotal=tafqeet (fraction[0]) + " فاصلة " + tafqeet (fraction[1]);
                }
                else if (fraction.length == 1){
                    var tafqeetTotal=  tafqeet (fraction[0]);
                }*/
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
$("#valamountdiscount").val("");
$("#dis_percent").val("");
var bill_id=document.getElementById("bill_id").value;
$.ajax({
	url:'{{route("GetPay",app()->getLocale())}}',
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"DIS"},
     success: function(data){
	 $("#payamountdiscount").val(data.sumpay);
     $("#refamountdiscount").val(data.sumref);
 	 $("#balancediscount").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#disdiscount").val(data.discount);
	 
	 $("#payamountdiscountd").val(data.sumpayd);
     $("#refamountdiscountd").val(data.sumrefd);
 	 $("#balancediscountd").val(data.balanced.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $("#disdiscountd").val(data.discountd);
}
    });	

$('#discountModal').modal('show');
var totaldiscount=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);



}


function isNumberKey(evt,el)
       {
           var charCode = (event.which) ? event.which : event.keyCode;
			// Allow digits, period (.), and backspace
			if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
				return false;
			}
			// Allow only one decimal point
			var currentValue = $(el).val();
			if (charCode == 46 && currentValue.indexOf('.') !== -1) {
				return false;
			}
			return true;
       }	
	   
function savediscount()
{
	

 
  var id_facility='{{$clinic->id}}';
  var bill_id=document.getElementById("bill_id").value;
  var balance=document.getElementById("balancediscount").value;	
  var valamountdiscount=document.getElementById("valamountdiscount").value;
  
  if(valamountdiscount=='' || !$.isNumeric(valamountdiscount)){
	  Swal.fire({ 
              text:'{{__("Please input a valid value for amount")}}',
			  icon:'warning',
			  customClass:'w-auto'
			  });
  }
  
  $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,"balance":balance,'valamountdiscount':valamountdiscount,'currency':$('#selectcurrencyd').val()},
   url: '{{route("SaveDiscount",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
         
		  //refund modal update totals
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#balancerefund").val(data.nbalance);
		 $("#refdiscount").val(data.tdiscount);
		 
		 $("#payamountrefundd").val(data.sumpayd);
         $("#refamountrefundd").val(data.sumrefd);
		 $("#balancerefundd").val(data.balanced);
		 $('#refdiscountd').val(data.tdiscountd);
		 
		 //payment modal update totals
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#balancepay").val(data.nbalance);
		 $("#paydiscount").val(data.tdiscount);
		 
		 $("#payamountd").val(data.sumpayd);
         $("#refamountd").val(data.sumrefd);
		 $("#balancepayd").val(data.balanced);
		 $('#paydiscountd').val(data.tdiscountd);
		 
		 //update discount totals
		 $("#payamountdiscount").val(data.sumpay);
         $("#refamountdiscount").val(data.sumref);
		 $("#balancediscount").val(data.nbalance);
		 $('#disdiscount').val(data.tdiscount);
		 
		 $("#payamountdiscountd").val(data.sumpayd);
         $("#refamountdiscountd").val(data.sumrefd);
		 $("#balancediscountd").val(data.balanced);
		 $('#disdiscountd').val(data.tdiscountd);
		 
		 //update bill table totals
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $("#balance").val(data.nbalance);
		 $("#tdiscount").val(data.tdiscount);
		 
		 $('#tpayd').val(data.sumpayd);
		 $('#trefd').val(data.sumrefd);
		 $('#balanced').val(data.balanced);
	     $('#tdiscountd').val(data.tdiscountd);
		 
		 if(data.balanced==0){
				 $('#saveBill').prop('disabled',true);
				 $('#btnrefund').prop('disabled',true); 
				 $('#btnpayment').prop('disabled',true); 	
				 $('#btndiscount').prop('disabled',true);
                 $('#finalizeBill').prop('checked',true);				 
			  }else{
				 $('#saveBill').prop('disabled',false);
				 $('#btnrefund').prop('disabled',false); 
				 $('#btnpayment').prop('disabled',false); 	
				 $('#btndiscount').prop('disabled',false);
                 $('#finalizeBill').prop('checked',false);	 				 
			  }
}
 if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		     return;
			} 

	}
 });

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