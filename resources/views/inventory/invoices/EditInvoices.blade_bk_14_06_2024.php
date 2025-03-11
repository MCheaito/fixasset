<!--
 DEV APP
 Created date : 28-12-2022
-->
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header">
				  <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('Edit Invoice')}}</h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				
				
				 
				</div>
				
				<div class="card-body p-0">
				    <div  class="row m-1"> 
						<div class="col-md-6">	

						 <button class="m-1 btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentinvoice()">{{__('Pay')}}</button>
						 <button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="refundinvoice()">{{__('Reimburse')}}</button>
						 
					   </div>
					   <div class="col-md-6">
						 <a href="{{ route('inventory.invoices.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						 <button class="m-1 float-right btn btn-action"  id="btnprintLabel" name="btnprintLabel" onClick="downloadPDFLabel()">{{__('Print label')}}</button>
						 <button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>
						 <button class="m-1 float-right btn btn-action"  id="btnaccount" name="btnaccount" onclick="event.preventDefault();TransAcount({{$ReqPatient->id}})">{{__('Account')}}</button>
					   </div>
				   
				    </div>
					<div  class="row m-1"> 
						
									<div class="col-md-3">
										<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                               									
						  <div class="col-md-2">
							 <label for="reqID_val"><b>{{__('Invoice Nb')}}</b></label>
							 <input  type="text" class="form-control" name="reqID_val"  value="{{$ReqPatient->clinic_inv_num}}" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="invoice_id"   value="{{$ReqPatient->id}}"/>
						  </div>
						  <div class="col-md-2">
							 <label for="invoice_date_val"><b>{{__('Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_val" id="invoice_date_val" value="{{Carbon\Carbon::parse($ReqPatient->date_invoice)->format('Y-m-d H:i')}}"  />
					  </div>
					    <div class="col-md-2">
							 <label for="invoice_date_due"><b>{{__('Due Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_due" id="invoice_date_due" value="{{Carbon\Carbon::parse($ReqPatient->date_due)->format('Y-m-d H:i')}}"  />
					  </div>
					    <div class="col-md-3">
							 <label for="invoice_date_due"><b>{{__('Nb in Account')}}</b></label>
							 <input type="text" class="form-control"  name="nbaccount" id="nbaccount" value="{{$ReqPatient->nbaccount}}"  disabled />
					  </div>
						  </div>
						<div  class="row m-1"> 
							
										<div class="form-group col-md-3 select2-primary">
											<label for="pro"><b>{{__('Fournisseur')}}</b></label>
											<select class="select2_data custom-select rounded-0" name="selectpro" id="selectpro" style="width:100%;" >
											<option value="0">{{__('Select Suppliers')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}" {{isset($ReqPatient) && $fournisseurs->id==$ReqPatient->fournisseur_id ? 'selected' : ''}}>{{$fournisseurs->name}}</option>
											@endforeach 
											</select>
															
                                        </div>
											
					
						  <div class="col-md-3">
							 <label for="invoice_ref"><b>{{__('Supplier Invoice Nb').'*'}}</b></label>
							 <input type="text" class="form-control"  name="invoice_ref" id="invoice_ref" value="{{$ref}}" disabled />
					  </div>
					    <div class="col-md-6">
							 <label for="invoice_rq"><b>{{__('Remark')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_rq" id="invoice_rq" value="{{$ReqPatient->notes}}" disabled />
					  </div>
						  </div>
					<div class="row m-1">
					
						<div class="col-md-3 col-6">
							<label for="selectcode"><b>{{__('Code')}}</b>
							<span class="ml-1" id="NbItemStock" style="color:red;"></span>
							<span><button class="ml-2  btn btn-sm btn-action"  id="AddNewItem"  data-toggle="modal" data-target="#NewItemModal" title="{{app()->getLocale()=='en'?__('Add'):__('Ajouter')}}"><i class="fa fa-plus"></i></button></span>
							</label>
							<select class="select2_allitems custom-select rounded-0" name="selectcode" id="selectcode" style="width:100%;" >
									
							</select>
							<input  type="hidden" id="typeCode"/>
							</div>	
								
						<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Quantity')}}</b></label>
								<input  class="form-control" value="" id="valqty"/>
								<input  type="hidden" id="cpt" value="{{$cptCount}}"/>
								<input  type="hidden" id="tbl"/>
							</div>
						</div>
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Initial Price')}}</b></label>
								<input  class="form-control" value="" id="initprice"/>
							</div>
						</div>
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Cost Price')}}</b></label>
								<input  class="form-control" value="" id="valprice"/>
							</div>
						</div>
						<div class="col-md-2 col-6">
														<label for="name">{{__('Formula')}}&#xA0;&#xA0;</label>
															<div class="form-group">
																<select name="formula_id" id="formula_id" class="custom-select rounded-0">
													<option value="0">{{__('Undefined')}}</option>
														@foreach($Formula as $f)
														<option value="{{$f->id}}" >{{__($f->name)}}</option>
														@endforeach
													</select>																			
												</div> 	
											</div> 
											<div class="col-md-2 col-6">
														<label for="name">{{__('Sel Price')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="sel_price" id="sel_price" value=""  />
															<input type="hidden" class="form-control" name="sel_price1" id="sel_price1" value="" />																							
												</div> 	
											</div>
					  				
				</div> 	
					<div class="row m-1">
					<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Disc%')}}</b></label>
								<input  class="form-control" id="valdiscountP" value=""/>
							</div>
						</div>
					<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Discount')}}</b></label>
								<input  class="form-control" id="valdiscount" value=""/>
							</div>
						</div>
						<div class="col-md-2 col-6">
							 <label for="invoice_date_dexpiry"><b>{{__('Expiry Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_dexpiry" id="invoice_date_dexpiry" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
					  </div>
					<!--<div class="col-md-3 col-6">
							<label for="selectcode"><b>{{__('Offer Type')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectdiscounttype" id="selectdiscounttype" style="width:100%;" >
									@foreach($typediscount as $typediscounts)
									<option value="{{$typediscounts->id}}">{{__($typediscounts->name)}}</option>
									@endforeach 
							</select>
							
							</div>-->
						<div class="col-md-1 col-6">
							<div class="d-flex flex-column">					
						<label for="selecttax"><b>{{__("Tax")}}</b></label>					
							<input id="taxable" type="checkbox"  style="height:22px;width:22px;" name="taxable"/>
						</div> 	
				      </div> 	
						<div class="col-md-2 col-4">
							<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-sm btn-action"  id="btnadd" onclick="insRow()" >{{__('Insert')}}</button>
							</div>
						</div>	
						@if($ReqPatientRetCount>0)
					<div class="form-group col-md-3 select2-primary">
											<label for="patient"><b>{{__('Return Patient')}}</b></label>
											<select class="select2_data custom-select rounded-0" name="selectreturn" id="selectreturn" style="width:100%;" >
											<!-- <option value="0">{{__('Select Return')}}</option>		-->
											@foreach($ReqPatientRet as $ReqPatientRets)
											<option value="{{$ReqPatientRets->id}}" >{{$ReqPatientRets->clinic_inv_num}},{{__('Date')}}:{{$ReqPatientRets->date_invoice}},{{__('total')}}:{{$ReqPatientRets->total}}</option>
											@endforeach 
											</select>
															
                                        </div> 					 
						<div class="form-inline col-md-2 col-4 ">
							<button class="m-1 btn btn-action" id="btnreturn" name="btnreturn" onClick="returnpatient()">{{__('View Return')}}  </button> 
		
						</div>	
						@endif						
		            </div>
						
		        </div>
			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card">
				<div class="card-body">
						<div class="m-1 row">
						   <div class="col-md-6"></div>
						   <div class="col-md-6">
							 <input  class="text-center form-control"  id="myInput"  onkeyup="myFunction()" placeholder="{{__('Search for item name...')}}">
						   </div>
						  </div>
						<div id="htmlTable" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTable" class="table table-striped table-bordered table-hover" style="text-align:center;">
									<thead>
										<tr class="txt-bg text-white text-center">
											<th scope="col" style="font-size:16px;">{{__('Item')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item name')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item Qty')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Discount')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Total')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Expiry Date')}}</th>
											<th scope="col" style="font-size:16px;">{{__('T.Disc')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Tax')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('Formula ID')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('Sel Price')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('Initial Price')}}</th>
											<th scope="col"></th>
											<th scope="col" style="font-size:16px;display:none;">{{__('TVQ')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('TPS')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('Ret Qty')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('Ret ID')}}</th>
										</tr>
									</thead>
								   <tbody>
								    @php
										$cpt = 1;
									@endphp
									@foreach ($ReqDeails as $sReqDeails) 
													<tr>
													<td>{{$sReqDeails->item_code}}</td>
													<td>{{$sReqDeails->item_name}}</td>
													<td>{{$sReqDeails->qty}}</td>
													<td>{{$sReqDeails->price}}</td>
													<td>{{$sReqDeails->discount}}</td>
													<td>{{(($sReqDeails->qty)*($sReqDeails->price))-($sReqDeails->discount)}}</td>
													<td>{{$sReqDeails->date_exp}}</td>
													<td>{{$sReqDeails->tdiscount}}</td>
													<td>{{$sReqDeails->tax}}</td>
													<td style="display:none;">{{$sReqDeails->formula_id}}</td>
													<td  style="display:none;">{{$sReqDeails->sel_price}}</td>
													<td  style="display:none;">{{$sReqDeails->initprice}}</td>
													<td><input type="button" class="btn btn-delete" id="rowdelete{{$cpt}}" value="{{__('Delete')}}" onclick="deleteRow(this)" disabled /></td>
											        <td style="display:none;">{{$sReqDeails->qst}}</td>
													<td  style="display:none;">{{$sReqDeails->gst}}</td>
													<td  style="display:none;">{{$sReqDeails->rqty}}</td>
													<td style="display:none;">{{$sReqDeails->rlid}}</td>
												   </tr>
											@php
											$cpt++;
											@endphp	 
									@endforeach
								   
								   </tbody>
								</table> 
							</div>
						</div>
						
						<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Discount')}}</label>
							   <input  class="text-center form-control"  id="tdiscount"  value="{{$ReqPatient->discount}}" disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('SubTotal')}}</label>
							   <input  class="text-center form-control"  id="totalf"  value="{{$ReqPatient->total}}" disabled  />
							</div>
							
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Tax')}}</label>
							   <select class="custom-select rounded-0"   id="selecttax" name="selecttax" disabled >
										@foreach($rates as $r)
									     <option value="{{$r->id}}" {{$FromFacility->bill_tax==$r->id?'selected':''}}>{{$r->name}}</option>	
										@endforeach
											
											<!--@if($FromFacility->bill_tax==1)
											<option value="1" >{{__('QC')}}</option>
											@else	
											<option value="2">{{__('ON')}}</option>
											@endif-->
							   </select>
							</div>
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('QST')}}</label>
							   <input  class="text-center form-control"  id="qst" value="{{$ReqPatient->qst}}"  disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('GST')}}</label>
							   <input  class="text-center form-control"  id="gst"  value="{{$ReqPatient->gst}}" disabled  />
							</div>
							
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Total')}}</label>
							   <input  class="text-center form-control"  id="stotal"  value="{{$stotal}}" disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Balance')}}</label>
							   <input  class="text-center form-control"  id="balance" value="{{$ReqPatient->inv_balance}}"  disabled  />
							</div>
							<div class="mb-1 d-none d-md-block col-md-2"></div>
							<div class="mb-1 d-none d-md-block col-md-6"></div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Payment')}}</label>
							   <input  class="text-center form-control"  id="tpay" value="{{$pay}}" disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Refund')}}</label>
							   <input  class="text-center form-control"  id="trefund" value="{{$refund}}" disabled  />
							</div>
						</div>
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1  btn btn-action" id="btnsave" name="btnsave" onClick="saveinventory()">{{__('Save')}}</button>
									 <button class="m-1  btn btn-action" id="btnmodify" name="btnmodify" onClick="modifyinventory()">{{__('Modify')}}</button>
 									 <button class="m-1 btn btn-delete" id="btndelete" name="btndelete" onClick="deleteinventory()">{{__('Delete')}}</button>
									</div>	
									
						</div>					  
				</div>
            </div>				
		</section>
@include('inventory.items.NewItemModal',['modal'=>true,'Fournisseur'=>$fournisseur,'Formula'=>$Formula,'iType'=>$iType,'lunette_specs'=>$lunette_specs,'lunette_id'=>'0'])	


<!--payment modal-->
			<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
				  <!-- Modal content-->
    				<div class="modal-content">
							<div class="p-0 modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Pay')}}</h4>
								<button type="button" class="float-right btn btn-action" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						    <div class="p-0 modal-body">
						        <div class="container">
									<div class="row m-1">   
									   <div class="col-md-6">
											<label class="label-size" for="name">{{__('Invoice Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="{{$ReqPatient->clinic_inv_num}}" disabled />   
											<input  type="hidden" id="cptp" value="{{$cptpCount}}"/>
									   </div>
									  
									   <div class="row m-1">   
									   <div class="col-md-3">	 
										  <label class="label-size" for="name">{{__('Date/Time')}}</label>
										  <input type="text" class="form-control" name="date_pay" id="date_pay" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
										</div>
										<div class="col-md-3">
											   <label class="label-size" for="name">{{__('Type')}}</label>
											   <select class="custom-select rounded-0"   id="selectmethod" name="selectmethod"  ">
																					<option value="0">{{__('All types')}}</option>																
																						@foreach($methodepay as $methodepays)
																						<option value="{{$methodepays->id}}">
																						{{$methodepays->name}}
																						</option>
																						@endforeach 
												</select>
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Amount')}}</label>
											 <input  class="form-control"  value="" id="valamount"  /> 
										</div>
										<div class="col-md-3">
										<div class="d-flex flex-column">
										 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
										 <button class="btn btn-action"  id="btnaddpay" onclick="insRowPay()">{{__('Insert')}}</button>
										</div>
										</div>	
										<div class="col-md-1 col-6">
											<div class="d-flex flex-column">					
										<label for="selecttax"><b>{{__("Deposit")}}</b></label>					
											<input id="deposit" type="checkbox"  style="height:22px;width:22px;" name="deposit"/>
										</div> 											
										</div>
										<div class="col-md-6">
										 <label for="invoice_rq"><b>{{__('Remark')}}</b></label>
										 <input type="text" class="form-control"  name="pay_rq" id="pay_rq" value=""  />
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
												<input  class="form-control" value="{{$ReqPatient->total}}" id="totalpay"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="{{$ReqPatient->balance}}" id="balancepay"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Paid amount')}}</label>
													 <input  class="form-control" value="{{$pay}}"  id="payamount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimbursed amount')}}</label>
												<input  class="form-control"  value="{{$refund}}" id="refamount"  disabled /> 
											</div>
						</div>			
						    <div class="modal-footer justify-content-center">
								 <button class="btn btn-action" id="btnsavepay" name="btnsavepay" onClick="savepay()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
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
							<div class="p-0 modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Reimburse')}}</h4>
								<button type="button" class="float-right btn btn-action" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
							<div class="p-0 modal-body">
								    <div class="container">
						                <div class="row m-1">   
						                    <div class="col-md-6">
												<label class="label-size" for="name">{{__('Invoice Nb')}}</label>
												<input  class="form-control"  value="{{$ReqPatient->clinic_inv_num}}" id="reqIDRef"  disabled />
												<input  type="hidden" id="cptr" value="{{$cptrCount}}"/>
										    </div>	
										
											<div class="row m-1">   
											<div class="col-md-3">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_refund" id="date_refund" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
											</div>
									        <div class="col-md-3">
											   <label class="label-size" for="name">{{__('Type')}}</label>
								               <select class="custom-select rounded-0"   id="selectmethodrefund" name="selectmethodrefund">
													<option value="0">{{__('All types')}}</option>																
														@foreach($methodepay as $methodepays)
														<option value="{{$methodepays->id}}">
														{{$methodepays->name}}
														</option>
														@endforeach 
												</select>
								            </div>
											
						  				   <div class="col-md-3">
												<label class="label-size" for="name">{{__('Amount')}}</label>
								                <input  class="form-control"  value="" id="valamountrefund" /> 
										   </div>
										   <div class="col-md-3">
										<div class="d-flex flex-column">
										 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
										 <button class="btn btn-action"  id="btnaddref" onclick="insRowRef()">{{__('Insert')}}</button>
										</div>
										</div>											
											<div class="col-md-6">
										 <label for="invoice_rq"><b>{{__('Remark')}}</b></label>
										 <input type="text" class="form-control"  name="ref_rq" id="ref_rq" value=""  />
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
						</div>	
					<div class="row m-1">
							<div class="col-md-3">
												<label class="label-size" for="name">{{__('Total')}}</label>
												<input  class="form-control" value="{{$ReqPatient->total}}" id="totalref"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="{{$ReqPatient->balance}}" id="balancerefund"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Paid amount')}}</label>
													 <input  class="form-control" value="{{$pay}}"  id="payamountrefund"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimbursed amount')}}</label>
												<input  class="form-control"  value="{{$refund}}" id="refamountrefund"  disabled /> 
											</div>
						</div>										
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsaverefund" name="btnsaverefund" onClick="saverefund()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
			 </div>		
		    </div>
			</div>				
			<!--end RefundModal-->	
			
			<!--remnise modal-->
			<div class="modal fade" id="remiseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Remise')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								    <div class="container">
						                <div class="row">
										<div class="col-md-3">
												<label class="label-size" for="name">{{__('Remise Nb')}}</label>
												<input  class="form-control"  value="" id="NreqIDRemis"  disabled />
										    </div>											
						                    <div class="col-md-3">
												<label class="label-size" for="name">{{__('Invoice Nb')}}</label>
												<input  class="form-control"  value="{{$ReqPatient->clinic_inv_num}}" id="reqIDRemis"  disabled />
												<input  type="hidden" id="cpts" value="{{$cptsCount}}"/>
										    </div>	
										
											<div class="row">   
											<div class="col-md-3">	 
												<label class="label-size" for="name">{{__('Date/Time')}}</label>
									            <input type="text" class="form-control" name="date_remise" id="date_remise" value="{{Carbon\Carbon::now()->format('Y-m-d H:i:s A')}}" />
											</div>
									       <div class="col-md-3">
										<label for="selectcode"><b>{{__('Code')}}</b></label><label id="NbItemStock" style="color:red;"></label>
										<select class="select2_data custom-select rounded-0" name="selectcoderemise" id="selectcoderemise" style="width:100%;" >
											<option value="0">{{__('All Code')}}</option>																
											@foreach($coderemise as $coderemises)
											<option value="{{$coderemises->id}}">{{$coderemises->name}}</option>
											@endforeach 
										</select>
							
										</div>	
											
						  				   <div class="col-md-3">
												<label class="label-size" for="name">{{__('Qty')}}</label>
								                <input  class="form-control"  value="" id="valqtyremise" /> 
										   </div>
										   <div class="col-md-3">
										<div class="d-flex flex-column">
										 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
										 <button class="btn btn-action"  id="btnaddremise" onclick="insRowRemise()">{{__('Insert')}}</button>
										</div>
										</div>											
										
										</div>
										</div> 
										</div>
										</div>
										<div id="htmlTableRemise" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTableRemise" class="table table-striped table-bordered table-hover" style="text-align:center;">
																	   
								</table> 
						</div>		
						</div>			
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsaveremise" name="btnsaveremise" onClick="saveremise()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
					    </div>
		    </div>					   					   	
		    </div>					   
			<!--end RemiseModal-->	  	  		  			
    </div>
</div>
@include('inventory.invoices.pdfs.choosePDFModal')
@endsection	
@section('scripts')
 @include('inventory.items.lunettescript_modal')			
<script>
function clearSelect2(){
	var supplier=$('#selectpro').val()
	$('.select2_allitems').val(null).trigger('change');
	$('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadOtherItems",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
					supplier:supplier,
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
}
</script>   
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});

 //$("#cpt").val("0");
			$("#valqty").val("1");
			$("#valqtyremise").val("1");
			$("#valprice").val("");
			$("#initprice").val("");
			$("#sel_price").val("");
			$("#valdiscount").val("");
			$("#valdiscountP").val("");
			$("#formula_id").val("0");
			$("#tbl").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#selectpatient").prop("disabled", true);
			$("#selectpro").prop("disabled", true);	
			$("#selecttype").prop("disabled", true);
			$("#invoice_date_val").prop("disabled", true);
			$("#invoice_date_due").prop("disabled", true);
			$("#invoice_ref").prop("disabled", true);
			$("#invoice_rq").prop("disabled", true);
			if ($("#nbaccount").val()=='' || $("#nbaccount").val()==null){
				 $("#valdiscount").removeAttr('disabled');
			}else{
				$("#btnaccount").prop("disabled", true);
			}
			//$("#btnreturn").prop("disabled", true);
			//$("#selectreturn").prop("disabled", true);
   var supplier=$('#selectpro').val();
   $('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadOtherItems",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
					supplier:supplier,
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
   
   
   
			
$('#selectcode').on('change', function()
{
   // alert(this.value); //or alert($(this).val());
   current_val=$("#selectcode").val();
   if(current_val=='0' || current_val==null){
		 $("#sel_price").val('0.000');
		  $("#valprice").val('0.000');
		  $("#initprice").val('0.000');
		  $("#formula_id").val('0');
          $("#NbItemStock").text('');
		  $("#valdiscount").val('');
    	  $("#valdiscountP").val('');

		return false;
	}
    document.getElementById("taxable").checked = false;
	$.ajax({
		url: '{{route("EfillPriceInv",app()->getLocale())}}',
		 data:{"_token": "{{ csrf_token() }}","selectcode":current_val},
		   type: 'get',
		  dataType: 'json',
		  success: function(data){
			  $("#valprice").val(data.cost_price);
			   $("#sel_price").val(data.Price);
			    $("#initprice").val(data.initprice);
				 $("#formula_id").val(data.formula_id);
				 $("#typeCode").val(data.gen_types);
              $("#NbItemStock").text("In Stock:"+" "+data.NbItemStock);
			   if (data.tax=='Y'){
			document.getElementById("taxable").checked = true;
			 }
			 if (data.initprice=="0.000"){
				 $("#initprice").val('')
			 }
			 if (data.Price=="0.000"){
				 $("#sel_price").val('')
			 }
			 if (data.cost_price=="0.000"){
				 $("#valprice").val('')
			 }
			// $("#valdiscount").val('0.00');
			// $("#valdiscountP").val('0.00');	
			  $("#valdiscount").val('');
			 if (data.gen_types=='2'){
				$("#valdiscount").prop("disabled", true);
				$("#initprice").prop("disabled", true);
				$("#sel_price").prop("disabled", true);
				$("#formula_id").prop("disabled", true);				
			  }else{
				 $("#valdiscount").removeAttr('disabled');
				 $("#initprice").removeAttr('disabled');
				 $("#sel_price").removeAttr('disabled');
				 $("#formula_id").removeAttr('disabled');				 
			  } 
			 
		 }
      });
});	

$('#valqty,#valprice,#valdiscountP').on('change', function()
{
 var discp=	($("#valqty").val()*$("#valprice").val()*$("#valdiscountP").val())/100;
  $("#valdiscount").val(discp);
});

$('#selecttype').change(function(e){
	
	 current_val=$("#selecttype").val();
	$.ajax({
		url: '{{route("fillInvoiceType",app()->getLocale())}}',
		 data:{"_token": "{{ csrf_token() }}","selecttype":current_val},
		   type: 'get',
		  dataType: 'json',
		  success: function(data){
		if(data.type=='P'){
		$("#selectpatient").removeAttr('disabled');	
		$("#selectpro").prop("disabled", true);		
		}
		if(data.type=='F'){
		$("#selectpro").removeAttr('disabled');	
		$("#selectpatient").prop("disabled", true);			
		}
		if(data.type=='G'){
		$("#selectpro").prop("disabled", true);		
		$("#selectpatient").prop("disabled", true);			
		}
		 }
      });
	
	});	


  
});

 function returnpatient(){
	  var valreturn=$("#selectreturn").val();
	 // var uu='<option value="'.vid.'">'
	if(valreturn!='0'){
    var url = "{{ route('inventory.invoices.EditRinvoices',[app()->getLocale(),'vid'])}}";
	url=url.replace('vid',valreturn);
    PopupCenter(url, '{{__("Return Fournisseur")}}','1000','500');
//     window.location.href=url; 
	 	 //window.open(url,target="_blank");

	}else{
     Swal.fire({text:'{{__("Please choose a return")}}',icon:'error',customClass:'w-auto'});
     return false;
	}
 }
 
 function insRow()
{
	
	 current_val=$("#selectcode").val();
  if(current_val=="0" || current_val==null){
	   Swal.fire({ 
              "text":"{{__('Please choose an Code')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	


var q = $('#valqty').val(),ip=$('#initprice').val(),sp=$('#sel_price').val(),vp=$('#valprice').val(),dis=$('#valdiscount').val(),disp=$('#valdiscountP').val(); 
   if(ip==''){
	  ip="0.000";
	  document.getElementById("initprice").value="0.000";
  }
  if(sp==''){
	  sp="0.000";
	  document.getElementById("sel_price").value="0.000";
  }
  if(vp==''){
	  vp="0.000";
	  document.getElementById("valprice").value="0.000";
  }
   if(dis==''){
	  dis="0.000";
	  document.getElementById("valdiscount").value="0.000";
  }
   if(disp==''){
	  disp="0.000";
	  document.getElementById("valdiscountP").value="0.000";
  }
   if (!$.isNumeric(q)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(ip)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in initial price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(sp)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in sale price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(vp)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in cost price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(dis)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Discount')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
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
var i= x.insertCell(8);
var j= x.insertCell(9);
var k= x.insertCell(10);
var l= x.insertCell(11);
var m= x.insertCell(12);
var o= x.insertCell(13);
var p= x.insertCell(14);
var q= x.insertCell(15);
var r= x.insertCell(16);
document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 var descripcode=$("#selectcode option:selected").text();
 var descriptypediscount="0";
 var taxable=document.getElementById("taxable").checked;
 if (taxable==true){taxablet='Y';}else{taxablet='N';}
  var total=(parseFloat(document.getElementById("valqty").value)*parseFloat(document.getElementById("valprice").value))-parseFloat(document.getElementById("valdiscount").value);

  a.innerHTML=document.getElementById("selectcode").value;
b.innerHTML=descripcode;
c.innerHTML=document.getElementById("valqty").value;
d.innerHTML=document.getElementById("valprice").value;


e.innerHTML=document.getElementById("valdiscount").value;
f.innerHTML=total;
g.innerHTML=document.getElementById("invoice_date_dexpiry").value;
h.innerHTML=descriptypediscount;
i.innerHTML=taxablet;
j.innerHTML=document.getElementById("formula_id").value;
k.innerHTML=document.getElementById("sel_price").value;
l.innerHTML=document.getElementById("initprice").value;
m.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}" onclick="deleteRow(this)"/>';

j.style.display = 'none';
k.style.display = 'none';
l.style.display = 'none';
o.style.display = 'none';
p.style.display = 'none';
q.style.display = 'none';
r.style.display = 'none';
   selcode=document.getElementById("selectcode").value;
   selectpro=document.getElementById("selectpro").value;
   valqty=document.getElementById("valqty").value;
   valdiscount=document.getElementById("valdiscount").value;
 
   valprice=document.getElementById("valprice").value;
   
   cpt=document.getElementById("cpt").value;
   selecttax=document.getElementById("selecttax").value;
   selectqst=document.getElementById("qst").value;
   selectgst=document.getElementById("gst").value;
   invoice_date_dexpiry=document.getElementById("invoice_date_dexpiry").value;
    typediscount="0";   
   taxable=document.getElementById("taxable").value;
      selecttype="1";
sel_price=document.getElementById("sel_price").value;
	initprice=document.getElementById("initprice").value;
     formula_id=document.getElementById("formula_id").value;
  // tbl=$("#tbl").val();
   totalf=document.getElementById("totalf").value;
   tdiscount=document.getElementById("tdiscount").value;
	$.ajax({
	url: 'addInvoiceRow',
		   type: 'get',
		   data:{"cpt":cpt,"initprice":initprice,"sel_price":sel_price,"formula_id":formula_id,"selecttype":selecttype,"typediscount":typediscount,"taxable":taxablet,"selectcode":selcode,"selectpro":selectpro,"valqty":valqty,"valdiscount":valdiscount,"valprice":valprice,"selecttax":selecttax,"totalf":totalf,"tdiscount":tdiscount,"selectqst":selectqst,"selectgst":selectgst,"invoice_date_dexpiry":invoice_date_dexpiry},
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
			  /*Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});*/
		   
		   //	$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);	
			document.getElementById("totalf").value=data.totalf;
			document.getElementById("tdiscount").value=data.tdiscount;
			//document.getElementById("balance").value=data.balance;
			o.innerHTML=data.uqst;
			p.innerHTML=data.ugst;
			q.innerHTML="0";
			r.innerHTML="0";
			document.getElementById("gst").value=data.gst;
			document.getElementById("qst").value=data.qst;
			document.getElementById("stotal").value=data.stotal;
			document.getElementById("valdiscount").value="";
			document.getElementById("valdiscountP").value="";
			if (document.getElementById("sel_price").value=="0.000"){
			document.getElementById("sel_price").value="";
			}
			if (document.getElementById("initprice").value=="0.000"){
			document.getElementById("initprice").value="";
			}
			if (document.getElementById("valprice").value=="0.000"){
			document.getElementById("valprice").value="";
			}
			document.getElementById("valqty").value="1";
		    $("#btnsave").removeAttr('disabled');
			
			} 
			 
		 }
      }); 

}
function deleteRow(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valqty=parseInt(document.getElementById("myTable").rows[ii].cells[2].innerHTML);
 var  valdiscount=parseFloat(document.getElementById("myTable").rows[ii].cells[4].innerHTML);
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[3].innerHTML);
 var  code=parseInt(document.getElementById("myTable").rows[ii].cells[0].innerHTML);
  var  taxable=document.getElementById("myTable").rows[ii].cells[8].innerHTML;

//alert(document.getElementById("myTable").rows[ii].cells[1].innerHTML);
 var selecttax=document.getElementById("selecttax").value;
var totalf=parseFloat(document.getElementById("totalf").value);
var tdiscount=parseFloat(document.getElementById("tdiscount").value);
var invoice_date_dexpiry=document.getElementById("invoice_date_dexpiry").value;

$.ajax({
		url: 'fillInvoiceDel',
		   type: 'get',
		  dataType: 'json',
		 data:{"code":code,"selecttax":selecttax,"taxable":taxable},
		  success: function(data){
			//  alert(data);
			var taxqst=parseFloat(data.qst).toFixed(3);
			//alert(taxqst);
			var taxgst=parseFloat(data.gst).toFixed(3);
			var total=(valprice*valqty)-valdiscount;
 		    var qstf=parseFloat((total*taxqst)/100).toFixed(3);
			//alert(qstf);
	        var gstf=parseFloat((total*taxgst)/100).toFixed(3);
			document.getElementById("qst").value=parseFloat(parseFloat(document.getElementById("qst").value)-parseFloat(qstf)).toFixed(3);
			document.getElementById("gst").value=parseFloat(parseFloat(document.getElementById("gst").value)-parseFloat(gstf)).toFixed(3);
			
			 totalf=parseFloat(parseFloat(totalf)-parseFloat(total)).toFixed(3);
			 tdiscount=parseFloat(parseFloat(tdiscount)-parseFloat(valdiscount)).toFixed(3);
			 stotal=totalf+parseFloat(document.getElementById("qst").value)+parseFloat(document.getElementById("gst").value);
			  $("#stotal").val(stotal);
		  //   balance=parseFloat(parseFloat(totalf)+parseFloat(document.getElementById("gst").value)+parseFloat(document.getElementById("qst").value)).toFixed(2);
		//	 $("#balance").val(balance);
			 $("#totalf").val(totalf);
			 $("#tdiscount").val(tdiscount);
			 document.getElementById('myTable').deleteRow(ii);

		 }
      });



if(ii==1){
	
		//	$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);	
			$("#btnadd").removeAttr('disabled');
}

}

function deleteinventory(){
	var id=$('#invoice_id').val();
Swal.fire({
  title: '{{__("Are you sure?")}}',
  html:'{{__("Please note, that operation effects the number of items in the stock")}}',
  showDenyButton: true,
  confirmButtonText: '{{__("OK")}}',
  denyButtonText: '{{__("Cancel")}}',
  customClass: 'w-auto'
}).then((result) => {
  if (result.isConfirmed) {
	  $.ajax({
            url: '{{route("DeleteInventory",app()->getLocale())}}',
		   data: {"_token": "{{ csrf_token() }}",'id':id,state:'inactivate'},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
                title:data.success,
			    toast:true,
                icon:"success",
			    timer:3000,
				position:"bottom-right",
			    showConfirmButton:false
			  });
			  //alert(name);
		  window.location.href="{{route('inventory.invoices.index',app()->getLocale())}}";

		       } 
			 }
       });	
	  
  }else if (result.isDenied) {
    return false;
  }
})

}


function saveinventory()
{
	var fref=$('#invoice_ref').val();
		  if(fref==''){
		  Swal.fire({text:"{{__('The Supplier Invoice Nb field is required')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }	
//for <td>
var x = document.getElementById("myTable").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(i=0;i<document.getElementById("myTable").rows.length;i++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr[i]={"CODE":document.getElementById("myTable").rows[i].cells[0].innerHTML,
        "DESCRIP":document.getElementById("myTable").rows[i].cells[1].innerHTML,
		"QTY":document.getElementById("myTable").rows[i].cells[2].innerHTML,
		"PRICE":document.getElementById("myTable").rows[i].cells[3].innerHTML,
		"DISCOUNT":document.getElementById("myTable").rows[i].cells[4].innerHTML,
		"TOTAL":document.getElementById("myTable").rows[i].cells[5].innerHTML,
		"TVQ":document.getElementById("myTable").rows[i].cells[13].innerHTML,
		"TPS":document.getElementById("myTable").rows[i].cells[14].innerHTML,
		"RQTY":document.getElementById("myTable").rows[i].cells[15].innerHTML,
		"RLID":document.getElementById("myTable").rows[i].cells[16].innerHTML,
		"EXPIRY_DATE":document.getElementById("myTable").rows[i].cells[6].innerHTML,
		"TYPEDISCOUNT":document.getElementById("myTable").rows[i].cells[7].innerHTML,
		"TAXABLE":document.getElementById("myTable").rows[i].cells[8].innerHTML,
		"FORMULAID":document.getElementById("myTable").rows[i].cells[9].innerHTML,
		"SELPRICE":document.getElementById("myTable").rows[i].cells[10].innerHTML,
		"INITPRICE":document.getElementById("myTable").rows[i].cells[11].innerHTML};	

}

var answer = confirm ("{{__('Are you sure?')}}")
   selectcode=document.getElementById("selectcode").value;
   selectpro=document.getElementById("selectpro").value;
   valgst=document.getElementById("gst").value;
   valqst=document.getElementById("qst").value;
   selecttax=document.getElementById("selecttax").value;
   totalf=document.getElementById("totalf").value;
   tdiscount=document.getElementById("tdiscount").value;  
  // balance=document.getElementById("balance").value;
   selectpatient="0";
   selecttype="1";
   invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_date_due=document.getElementById("invoice_date_due").value;
   invoice_ref=document.getElementById("invoice_ref").value;
   invoice_rq=document.getElementById("invoice_rq").value;
   invoice_id=document.getElementById("invoice_id").value;
   reqID=document.getElementById("reqID").value;
    clinic_id=document.getElementById("clinic_id").value;
		stotal=document.getElementById("stotal").value;
		 tpay=document.getElementById("tpay").value;
	 trefund=document.getElementById("trefund").value;
   Mastatus='N';
   if (invoice_id!='')
   {
	 Mastatus='M';   
   }
if (answer){
var myjson=JSON.stringify(arr);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}",data:myjson,
	      "tpay":tpay,"trefund":trefund,"stotal":stotal,"selectpro":selectpro,"valgst":valgst,"valqst":valqst,
		  "selecttax":selecttax,"totalf":totalf,"tdiscount":tdiscount,"selectpatient":selectpatient,
		  "selecttype":selecttype,"status":Mastatus,"invoice_id":invoice_id,"reqID":reqID,
		  "clinic_id":clinic_id,"invoice_date_val":invoice_date_val,"invoice_date_due":invoice_date_due,"invoice_ref":invoice_ref,"invoice_rq":invoice_rq},
    url:'{{route("ESaveInventory",app()->getLocale())}}',
    success: function(data){
		if(data.success){	
			 Swal.fire({ 
              "title":data.success,
              "icon":"success",
			  "timer":3000,
			  "position":"bottom-right",
			  "showConfirmButton":false,
			  "toast": true});
		   	//$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);	
			document.getElementById("reqID").value=data.reqID;
			document.getElementById("invoice_id").value=data.last_id;
			document.getElementById("balance").value=data.balance;
			
			document.getElementById("tdiscount").value=parseFloat(data.tdiscount).toFixed(3);
			document.getElementById("totalf").value=parseFloat(data.totalf).toFixed(3);
			document.getElementById("qst").value=parseFloat(data.qst).toFixed(3);
			document.getElementById("gst").value=parseFloat(data.gst).toFixed(3);
			document.getElementById("stotal").value=parseFloat(data.stotal).toFixed(3);
			
			$("#btnadd").prop("disabled", true);
		   	 $("#btnmodify").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');
			$("#selecttax").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#selectpatient").prop("disabled", true);
			$("#selectpro").prop("disabled", true);	
			$("#selecttype").prop("disabled", true);	
			$("#invoice_date_val").prop("disabled", true);
            $("#invoice_date_due").prop("disabled", true);	
			$('.form-control.input').prop('disabled', true);
			$("#invoice_ref").prop("disabled", true);	
			$("#invoice_rq").prop("disabled", true);	
			$("#btnreturn").prop("disabled", true);
			$("#selectreturn").prop("disabled", true);
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


function modifyinventory()
{
			//$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			//$("#btnrefund").prop("disabled", true);
			//$("#btnprint").prop("disabled", true);				
		    $("#btnadd").removeAttr('disabled');
			//$("#selecttype").removeAttr('disabled');
			$("#selectpro").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			$("#invoice_date_due").removeAttr('disabled');
			$("#invoice_date_val").removeAttr('disabled');
			$("#invoice_ref").removeAttr('disabled');
			$("#invoice_rq").removeAttr('disabled');
			$("#btnreturn").removeAttr("disabled", true);
			$("#selectreturn").removeAttr("disabled", true);
			current_val=$("#selecttype").val();
		
		$("#selectpro").removeAttr('disabled');	
		
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//  	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
		//	alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
			document.getElementById(rdel).disabled = false;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
flatpickr('#invoice_date_due', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
flatpickr('#invoice_date_val', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });	
}

function paymentinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$("#valamount").val("0.000");
$.ajax({
	url:'{{route("GetPayInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $("#payamount").val(data.sumpay);
    $("#refamount").val(data.sumref);
	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
	
}
    });	
$('#paymentModal').modal('show');
var totalpay=parseFloat(document.getElementById("stotal").value);
var balancepay=parseFloat(document.getElementById("balance").value);
  $("#totalpay").val(totalpay);
  $("#balancepay").val(balancepay);
}

function refundinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$("#valamountrefund").val("0.000");
$.ajax({
	url:'{{route("GetRefInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $("#payamountrefund").val(data.sumpay);
    $("#refamountrefund").val(data.sumref);
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html1);
	
}
    });	
$('#refundModal').modal('show');
var totalrefund=parseFloat(document.getElementById("stotal").value);
var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totalref").val(totalrefund);
$("#balancerefund").val(balancerefund);
 $("#ref_rq").val("");

}

function TransAcount(account_id){
	var account_num =$('#nbaccount').val();
	if(account_id !=0){
	  var invoice_num =$('#invoice_id').val();
	  if(account_num != null && account_num !=''){
		   $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		   $.ajax({
				type : 'POST',
				url : '{{route("accounting.to_account",app()->getLocale())}}',
				data : { account_id:account_id},
				dataType: 'JSON',
				success: function(data){
							//window.location.href = data.location;
							$('#invoice_num').val(data.clinic_inv_num);
							$('#btndel').show();
							$('#btndel').removeAttr('disabled');
							PopupCenter(data.location, '{{__("Order")}}','1000','500');
							}
						  });	 	
		  
	  }else{
	  
	  
	  Swal.fire({
		 html: '{{__("Are you sure you want to generate your invoice to Account?")}}',
         showDenyButton: true,
         confirmButtonText: '{{__("Yes")}}',
         denyButtonText: '{{__("No")}}',
         customClass: 'w-auto'
       }).then((result) => {
         if (result.isConfirmed) {	
	  
		  $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }});
		  $.ajax({
				type : 'POST',
				url : '{{route("accounting.to_account",app()->getLocale())}}',
				data : { account_id:account_id},
				dataType: 'JSON',
				success: function(data){
							//window.location.href = data.location;
							$('#nbaccount').val(data.nbaccount);
							$('#btndel').show();
							$('#btnaccount').prop('disabled',true);
							PopupCenter(data.location, '{{__("Order")}}','1000','500');
							}
						  });	 	
		 }else if(result.isDenied){
			 return false;
		 }
	   })
	  }
	
	}else{
		return false;
	}
	
}


function remiseinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$.ajax({
	url:'{{route("GetRemiseInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $('#myTableRemise').empty();
	 $('#myTableRemise').html(data.html1);
     $("#NreqIDRemis").val(data.IdInvRemise);
	 if (data.IdInvRemise!=''){
		$("#btnaddremise").prop("disabled", true);
		$("#btnsaveremise").prop("disabled", true); 
	var nb=document.getElementById("cpts").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdeleteremise"+j;
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
$('#remiseModal').modal('show');
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
document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#selectmethod option:selected").text().trim();
d.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(3);
var deposit=document.getElementById("deposit").checked;

 if (deposit==true){tdeposit='Y';}else{tdeposit='N';}
e.innerHTML=tdeposit;
f.innerHTML=(document.getElementById("pay_rq").value);
g.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';
var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)+parseFloat(document.getElementById("valamount").value)).toFixed(3);
document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)-parseFloat(document.getElementById("valamount").value)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
	$("#btnsavepay").prop("disabled", false);	
}

function deleteRowPay(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[3].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
 var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)-parseFloat(valpay)).toFixed(3);
document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)+parseFloat(valpay)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
if(document.getElementById('myTablePay').rows.length==1){
			$("#payamount").val("0.000");
			
			
}
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

document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#selectmethodrefund option:selected").text().trim();
d.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(3);
e.innerHTML=(document.getElementById("ref_rq").value);

f.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(3);
document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
	$("#btnsaverefund").prop("disabled", false);	
  	
}
function deleteRowRef(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[3].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
 var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)-parseFloat(valref)).toFixed(3);
document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)-parseFloat(valref)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
if(document.getElementById('myTableRef').rows.length==1){
			$("#refamount").val("0.000");
			
			
}
$("#btnsaverefund").prop("disabled", false);		
}


function insRowRemise()
{
	 current_val=$("#selectcoderemise").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un code",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valqtyremise").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Qty",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableRemise').insertRow(document.getElementById('myTableRemise').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);

document.getElementById("cpts").value=parseInt(document.getElementById("cpts").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cpts").value;
b.innerHTML=document.getElementById("date_remise").value;
c.innerHTML=document.getElementById("selectcoderemise").value;
d.innerHTML=$("#selectcoderemise option:selected").text().trim();
e.innerHTML=document.getElementById("valqtyremise").value;
f.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteremise'+document.getElementById("cpts").value+'" value="{{__('Delete')}}" onclick="deleteRowRemise(this)"/>';
	$("#btnsaveremise").prop("disabled", false);	
}

function deleteRowRemise(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valpay=parseFloat(document.getElementById("myTableRemise").rows[ii].cells[3].innerHTML);
 document.getElementById('myTableRemise').deleteRow(ii);
 
$("#btnsaveremise").prop("disabled", false);		
}

function saveremise()
{
var x = document.getElementById("myTableRemise").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTableRemise").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTableRemise").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTableRemise").rows[ii].cells[1].innerHTML,"CODE":document.getElementById("myTableRemise").rows[ii].cells[2].innerHTML,"NAME":document.getElementById("myTableRemise").rows[ii].cells[3].innerHTML,"QTY":document.getElementById("myTableRemise").rows[ii].cells[4].innerHTML};	

}
	
var invoice_id=document.getElementById("invoice_id").value;
  invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_date_due=document.getElementById("invoice_date_due").value;
     clinic_id=document.getElementById("clinic_id").value;
	  NreqIDRemis=document.getElementById("NreqIDRemis").value;
var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","NreqIDRemis":NreqIDRemis,"invoice_id":invoice_id,"clinic_id":clinic_id,"invoice_date_val":invoice_date_val,"invoice_date_due":invoice_date_due,data:myjson1},
   url: '{{route("SaveRemiseInventory",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
       
$("#btnsaveremise").prop("disabled", true);  	
$("#NreqIDRemis").val(data.NreqID);	
$("#btnaddremise").prop("disabled", true);

var nb=document.getElementById("cpts").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdeleteremise"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
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
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTablePay").rows[ii].cells[2].innerHTML,"PRICE":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,"DEPOSIT":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,"RQPAY":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML};	

}
	
var clinic_id=document.getElementById("clinic_id").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var invoice_id=document.getElementById("invoice_id").value;
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
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"invoice_id":invoice_id,data:myjson1,"balance":balance},
   url: '{{route("SavePayInventory",app()->getLocale())}}',
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
	var nbalance=parseFloat(data.nbalance).toFixed(3);
	//alert(nbalance);
    $("#balance").val(nbalance);
	 $("#balancepay").val(nbalance);
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

function saverefund()
{
var x = document.getElementById("myTableRef").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTableRef").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTableRef").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTableRef").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTableRef").rows[ii].cells[2].innerHTML,"PRICE":document.getElementById("myTableRef").rows[ii].cells[3].innerHTML,"RQREF":document.getElementById("myTableRef").rows[ii].cells[4].innerHTML};	

}
	
var clinic_id=document.getElementById("clinic_id").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var invoice_id=document.getElementById("invoice_id").value;
  var balance=document.getElementById("balancerefund").value;	
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
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"invoice_id":invoice_id,data:myjson1,"balance":balance},
   url: '{{route("SaveRefundInventory",app()->getLocale())}}',
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
		 var p1=$("#trefund").val();
$("#btnsaverefund").prop("disabled", true);  	
	//	 alert(b1);
	//	 alert(p1);
	var nbalance=parseFloat(data.nbalance).toFixed(3);
	//alert(nbalance);
$("#balance").val(nbalance);
$("#balancerefund").val(nbalance);
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

$('#selectpro').change(function(){
	clearSelect2();
 });
 
 $("#NewItemModal").on('hidden.bs.modal', function(){
  //  alert('The modal is about to be hidden.');
	  $('.select2_allitems').val(null).trigger('change');
	  $("#NewItemModal").find('#fournisseur').trigger('change.select2');
	  $("#NewItemModal").find('#brand').trigger('change.select2');
	
	
  });		

$('#choose_pdf').click(function(e){
	e.preventDefault();
	var desc = 'O';
	var print = $("input[name='pdf_type']:checked").val();
	var id = $('#pdf_id').val();
	if(print==null){
		Swal.fire({html:'{{__("Please choose at least one option")}}',icon:'error',customClass:'w-auto'});
		return false;
	}
	$.ajax({
           url: '{{route("downloadPDFInvoice",app()->getLocale())}}',
		   beforeSend: function() { 
		                 $('#choosePDFModal').modal('hide');
						 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id,'print_type':print,'desc':desc},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
	
});

function downloadPDF(){	 
   var id=document.getElementById("reqID").value;	
   $('#choosePDFModal').find('#pdf_id').val(id);
   $('#choosePDFModal').find('#all_pdf').show();
   $('#choosePDFModal').modal('show');
      
	
	
	}
	
function downloadPDFLabel(){	
var id=document.getElementById("reqID").value;
 $.ajax({
           url: '{{route("inventory.download_pdf_label",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
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
			link.download=('Invoice_labels.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
            
		
			    });

}

$('#formula_id,#valprice').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#valprice').val(); 
	  current_val=$("#selectcode").val();
	if (id==1){
//		$("#sel_price").removeAttr('disabled');
		$("#sel_price").prop("readonly",false);
	}else{
		// $("#sel_price").prop("disabled", true);
		$("#sel_price").prop("readonly",true);
		$.ajax({
            url: '{{route('generation_price',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'cost_price':cost_price,'selectcode':current_val},
           success: function(data){
			      $('#sel_price').val(data.sel_price); 
			      $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
	});
	

</script>
<script>
function PopupCenter(url, title, w, h) {  
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
                  
        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
                  
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
        var top = ((height / 2) - (h / 2)) + dualScreenTop;  
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
      
        // Puts focus on the newWindow  
        if (window.focus) {  
            newWindow.focus();  
        }  
    }
</script>
<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
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
flatpickr('#date_remise', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });		     				
 
    </script>

@endsection	
