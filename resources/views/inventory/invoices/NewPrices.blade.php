<!--
 DEV APP
 Created date : 24-12-2022
-->
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
			
				<div class="card-header">
				  <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('New Prices')}}}}</h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				
				
				 
				</div>
				<div class="card-body p-0">
				    <div  class="row m-1"> 
						<div class="col-md-6" hidden>	
						 <button class="m-1 btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentinvoice()">{{__('Pay')}}</button>
						 <button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="refundinvoice()">{{__('Reimburse')}}</button>
					   </div>
					   <div class="col-md-6">
					    	
						<a href="{{ route('inventory.invoices.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						<button class="m-1 float-right btn btn-action"  id="btnprintLabel" name="btnprintLabel" onClick="downloadPDFLabel()" hidden >{{__('Print label')}}</button>
						<button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>

					   </div>
				   </div>
					<div  class="row m-1">  
					
									<div class="col-md-3">
										<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
										<input type="hidden" id="reference" name="reference" />

								    </div>
															
						  <div class="col-md-3">
							 <label for="reqID_val"><b>{{__('Prices Nb')}}</b></label>
							 <input  type="text" class="form-control" name="reqID_val"  value="" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="invoice_id"   value=""/>
						  </div>
						  <div class="col-md-3">
							 <label for="invoice_date_val"><b>{{__('Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_val" id="invoice_date_val" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
					  </div>
					    <div class="col-md-3" hidden >
							 <label for="invoice_date_due"><b>{{__('Due Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_due" id="invoice_date_due" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
					  </div>
						  </div>
						<div  class="row m-1"  > 
							
										<div class="form-group col-md-3 select2-teal" hidden >
										<label for="pro"><b>{{__('Fournisseur').'*'}}</b></label>
											<select class="select2_data custom-select rounded-0" name="selectpro" id="selectpro" style="width:100%;" >
											<option value="0">{{__('Choose a supplier')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}">{{$fournisseurs->name}}</option>
											@endforeach 
											</select>
															
                                        </div>
										
				
						  <div class="col-md-2" hidden >
							 <label for="invoice_ref"><b>{{__('Supplier Invoice Nb').'*'}}</b></label>
							 <input type="text" class="form-control"  name="invoice_ref" id="invoice_ref" value="" />
					  </div>
					  	<div class="form-group col-md-3 select2-teal">
										<label for="pro"><b>{{__('Clients')}}</b></label>
											<select class="select2_data custom-select rounded-0" name="selectclient" id="selectclient" style="width:100%;" >
											<option value="0">{{__('Choose a Client')}}</option>		
											@foreach($clients as $sclients)
											<option value="{{$sclients->id}}">{{$sclients->name}}</option>
											@endforeach 
											</select>
															
                                        </div>
					    <div class="col-md-4">
							 <label for="invoice_rq"><b>{{__('Remark')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_rq" id="invoice_rq" value="" />
					  </div>
						  </div>
					<div class="row m-1">
					
						<div class="col-md-3 col-6">
							<label for="selectcode"><b>{{__('Code')}}</b>
							<span class="ml-1" id="NbItemStock" style="color:red;"></span>
							</label>
							<select class="select2_allitems custom-select rounded-0" name="selectcode" id="selectcode" style="width:100%;" >
									
							</select>
						<input  type="hidden" id="typeCode"/>	
							</div>	
					<div class="d-none col-md-1 col-6">
					<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
					</div>
					</div>				
						<div class="col-md-1 col-6" hidden >
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Quantity')}}</b></label>
								<input  class="form-control" value="" id="valqty"/>
								<input  type="hidden" id="cpt"/>
								<input  type="hidden" id="tbl"/>
							</div>
						</div>
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Unit Price')}}</b></label>
								<input  class="form-control" value="" id="initprice"/>
							</div>
						</div>
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Kit Price')}}</b></label>
								<input  class="form-control" value="" id="valprice" name="valprice"/>
							</div>
						</div>
						<div class="col-md-2 col-6" hidden >
														<label for="name"><b>{{__('Formula')}}&#xA0;&#xA0;</b></label>
															<div class="form-group">
																<select name="formula_id" id="formula_id" class="custom-select rounded-0">
													<option value="0">{{__('Undefined')}}</option>
														@foreach($Formula as $f)
														<option value="{{$f->id}}" {{($f->id==1 ? 'selected' : '')}} >{{__($f->name)}}</option>
														@endforeach
													</select>																			
												</div> 	
											</div> 
											<div class="col-md-2 col-6">
														<label for="name"><b>{{__('Box Price')}}&#xA0;&#xA0;</b></label>
															<div class="form-group">
															<input type="text" class="form-control" name="sel_price" id="sel_price"   />
															<input type="hidden" class="form-control" name="sel_price1" id="sel_price1" value="" />	
												</div> 	
											</div>
							
		          
					<div class="col-md-1 col-6" hidden >
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Disc%')}}</b></label>
								<input  class="form-control" id="valdiscountP" value=""/>
							</div>
						</div>
					    <div class="col-md-2 col-6" hidden >
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Discount')}}</b></label>
								<input  class="form-control" id="valdiscount" value=""/>
							</div>
						</div>
						<div class="col-md-3 col-6" hidden >
							 <label for="invoice_date_dexpiry"><b>{{__('Expiry Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_dexpiry" id="invoice_date_dexpiry" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" />
					  </div>
					  <div class="col-md-3 col-6" hidden >
							 <label for="invoice_nblot"><b>{{__('Lot Nb')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_nblot" id="invoice_nblot" value="" />
					  </div>
					 <!-- <div class="col-md-3 col-6">
							<label for="selectcode"><b>{{__('Offer Type')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectdiscounttype" id="selectdiscounttype" style="width:100%;" >
									@foreach($typediscount as $typediscounts)
									<option value="{{$typediscounts->id}}">{{__($typediscounts->name)}}</option>
									@endforeach 
							</select>
							
							</div>-->
							<div class="col-md-1 col-6" hidden >
									<div class="d-flex flex-column">					
								<label for="selecttax"><b>{{__("Tax")}}</b></label>					
									<input id="taxable" type="checkbox"  style="height:22px;width:22px;" name="taxable"/>
								</div> 	
				            </div> 			
					<div class="col-md-2 col-4">
							<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-sm btn-action"  id="btnadd" onclick="insRow()">{{__('Insert')}}</button>
							</div>
						</div>		   
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
											<th scope="col" style="font-size:16px;">{{__('Reference')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item name')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Unit Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Kit Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Box Price')}}</th>
											<th scope="col"></th>
										</tr>
									</thead>
								   <tbody></tbody>
								</table> 
							</div>
						</div>
						<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;" hidden >
								<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('Discount')}}</label>
							   <input  class="text-center form-control"  id="tdiscount"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('SubTotal')}}</label>
							   <input  class="text-center form-control"  id="totalf"   disabled  />
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
							   <input  class="text-center form-control"  id="qst"   disabled  />
							</div>
							<div class="mb-1 col-md-1 col-4">
							   <label for="name">{{__('GST')}}</label>
							   <input  class="text-center form-control"  id="gst"   disabled  />
							</div>
							
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Total')}}</label>
							   <input  class="text-center form-control"  id="stotal"   disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('Balance')}}</label>
							   <input  class="text-center form-control"  id="balance"   disabled  />
							</div>
							<div class="mb-1 d-none d-md-block col-md-2"></div>
							<div class="mb-1 d-none d-md-block col-md-6"></div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Payment')}}</label>
							   <input  class="text-center form-control"  id="tpay"  disabled  />
							</div>
							<div class="mb-1 col-md-2 col-4">
							   <label for="name">{{__('T.Refund')}}</label>
							   <input  class="text-center form-control"  id="trefund"  disabled  />
							</div>
						</div>
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1 btn btn-action" id="btnsave" name="btnsave" onClick="saveinventory()">{{__('Save')}}</button>
									 <button class="m-1 btn btn-action" id="btnmodify" name="btnmodify" onClick="modifyinventory()">{{__('Modify')}}</button>
									 <button class="m-1 btn btn-delete" id="btndelete" name="btndelete" onClick="deleteinventory()">{{__('Delete')}}</button>
									 <button class="m-1 btn btn-reset" id="btncancel" name="btncancel" onClick="cancelinventory()">{{__('Cancel')}}</button>
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
							<div class="p-0 modal-header txt-bg">
								<h4 class="modal-title">{{__('Pay')}}</h4>
								<button type="button" class="float-right btn btn-action" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
						    <div class="p-0 modal-body">
						        <div class="container">
									<div class="row m-1">   
									   <div class="col-md-6">
											<label class="label-size" for="name">{{__('Invoice Nb')}}</label>
											<input  class="form-control"   id="reqIDPay" value="" disabled />   
											<input  type="hidden" id="cptp" value=""/>
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
												<input  class="form-control" value="" id="totalpay"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="" id="balancepay"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Paid amount')}}</label>
													 <input  class="form-control" value=""  id="payamount"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimbursed amount')}}</label>
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
												<input  class="form-control"  value="" id="reqIDRef"  disabled />
												<input  type="hidden" id="cptr" value=""/>
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
												<input  class="form-control" value="" id="totalrefund"   disabled />   
										</div>
										<div class="col-md-3">
											<label class="label-size" for="name">{{__('Balance')}}</label>
											<input  class="form-control"  value="" id="balancerefund"  disabled />   
										</div>
										
										<div class="col-md-3">
													<label class="label-size" for="name">{{__('Paid amount')}}</label>
													 <input  class="form-control" value=""  id="payamountrefund"  disabled /> 
											</div>
											<div class="col-md-3">
												<label class="label-size" for="name">{{__('Reimbursed amount')}}</label>
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
			<!--end RefundModal-->	  	  		
		  
    </div>
</div>
@endsection
@section('scripts')
@include('inventory.items.lunettescript_modal')
<script>
function clearSelect2(){
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
					supplier: $('#selectpro').val(),
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
	  
			$("#cpt").val("0");
			$("#cptp").val("0");
			$("#cptr").val("0");
			$("#valqty").val("1");
			$("#valprice").val("");
			$("#initprice").val("");
			$("#sel_price").val("");
			$("#sel_price1").val("0.000");
			$("#valdiscount").val("");

			$("#valdiscountP").val("");
			$("#totalf").val("0.000");
			$("#tdiscount").val("0.000");
			$("#qst").val("0.000");
			$("#gst").val("0.000");
			$("#balance").val("0.000");
			$("#payamount").val("0.000");
			$("#refamount").val("0.000");
			$("#valamount").val("0.000");
			$("#tbl").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#balancerefund").val("0.000");	
			$("#totalrefund").val("0.000");
			$("#valamountrefund").val("0.000");
			$("#balancerefund").val("0.000");
			$("#tpay").val("0.000");
			$("#trefund").val("0.000");	
			
			//$("#selectpatient").prop("disabled", true);
			//$("#selectpro").prop("disabled", true);
		$("#btndelete").prop("disabled", true);
		$("#selectpro").removeAttr('disabled');	
		$("#selectclient").removeAttr('disabled');	

	
			
$('#selectcode').on('change', function()
{
   // alert(this.value); //or alert($(this).val());
   document.getElementById("taxable").checked = false;
   current_val=$("#selectcode").val();
   if(current_val=='0' || current_val==null){
		  $("#sel_price").val('');
		  $("#valprice").val('');
		  $("#initprice").val('');
		  $("#formula_id").val('0');
          $("#NbItemStock").text('');
		  $("#valdiscountP").val('');
		  $("#valdiscount").val('');
		return false;
	}
	$.ajax({
		url: 'fillPriceInv?selectcode='+current_val,
		   type: 'get',
		  dataType: 'json',
		  success: function(data){
			 $("#valprice").val(data.cost_price);
			   $("#sel_price").val(data.Price);
			    $("#initprice").val(data.initprice);
				 $("#formula_id").val(data.formula_id);
				   $("#typeCode").val(data.gen_types);
				     $("#reference").val(data.reference);
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
	 if(current_val=="4"){
		$("#selectpro").removeAttr('disabled');	
		$("#selectclient").removeAttr('disabled');	
		$("#selectpatient").prop("disabled", true);			
		}
	if(current_val=="3"){
		$("#selectpatient").removeAttr('disabled');	
		$("#selectpro").prop("disabled", true);		
		$("#selectclient").prop("disabled", true);				
		}	
	//$.ajax({
	//	url: 'fillInvoiceType?selecttype='+current_val,
	//	   type: 'get',
	//	  dataType: 'json',
		//  success: function(data){
	//	if(data.type=='P'){
	//	$("#selectpatient").removeAttr('disabled');	
	//	$("#selectpro").prop("disabled", true);		
	//	}
	//	if(data.type=='F'){
	//	$("#selectpro").removeAttr('disabled');	
	//	$("#selectpatient").prop("disabled", true);			
	//	}
	//	if(data.type=='G'){
	//	$("#selectpro").prop("disabled", true);		
	//	$("#selectpatient").prop("disabled", true);			
	//	}
	//	 }
     // });
	
	});	


  
});

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
    $("#pay_rq").val("");
document.getElementById("deposit").checked = false;
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
$("#totalrefund").val(totalrefund);
$("#balancerefund").val(balancerefund);
 $("#ref_rq").val("");

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

var ip=$('#initprice').val(),sp=$('#sel_price').val(),vp=$('#valprice').val();
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
  
   
if (!$.isNumeric(ip)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Unit price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(sp)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Box price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(vp)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Kit price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}

var typecode=$('#typeCode').val();
var discval=$('#valprice').val();	

var x=document.getElementById('myTable').insertRow(document.getElementById('myTable').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);


document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;
 var descripcode=$("#selectcode option:selected").text();

a.innerHTML=document.getElementById("selectcode").value;
c.innerHTML=descripcode;
b.innerHTML=document.getElementById("reference").value;

d.innerHTML = '<input type="text" class="form-control" size="3" id="unitprice' + 
                  document.getElementById("cpt").value + '" value="' + 
                  document.getElementById("initprice").value + 
                  '" oninput="RetRow1(this, \'unitprice' + document.getElementById("cpt").value + '\')" ' + 
                  'onchange="RetRow1(this, \'unitprice' + document.getElementById("cpt").value + '\')" />';
e.innerHTML = '<input type="text" class="form-control" size="3" id="kitprice' + 
                  document.getElementById("cpt").value + '" value="' + 
                  document.getElementById("valprice").value + 
                  '" oninput="RetRow1(this, \'kitprice' + document.getElementById("cpt").value + '\')" ' + 
                  'onchange="RetRow1(this, \'kitprice' + document.getElementById("cpt").value + '\')" />';
f.innerHTML = '<input type="text" class="form-control" size="3" id="boxprice' + 
                  document.getElementById("cpt").value + '" value="' + 
                  document.getElementById("sel_price").value + 
                  '" oninput="RetRow1(this, \'boxprice' + document.getElementById("cpt").value + '\')" ' + 
                  'onchange="RetRow1(this, \'boxprice' + document.getElementById("cpt").value + '\')" />';


g.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}" onclick="deleteRow(this)"/>';
$("#btnadd").removeAttr('disabled');
$("#btnsave").removeAttr('disabled');
   
}
function deleteRow(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 			 document.getElementById('myTable').deleteRow(ii);
		if(document.getElementById('myTable').rows.length==1){
			//$("#balance").val("0.00");
			//$("#balance").val(tpay-trefund);
			$("#totalf").val("0.000");
			$("#stotal").val("0.000");
			$("#tdiscount").val("0.000");
			$("#gst").val("0.000");
			$("#qst").val("0.000");
			
			//$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
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
              "text":"{{__('Please input a type')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"{{__('Please input an amount')}}",
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
              "text":"{{__('Please input a type')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"{{__('Please input an amount')}}",
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

function RetRow(r,rd)
{	
var ii=r.parentNode.parentNode.rowIndex;

var nb=document.getElementById("cpt").value;

 var  valqty=parseInt(document.getElementById("myTable").rows[ii].cells[2].getElementsByTagName('input')[0].value);
 rdisc=rd;
  var valdiscount= parseFloat(document.getElementById("myTable").rows[ii].cells[4].innerHTML);
  if (valdiscount==''){
	  valdiscount="0.00";
  }
  if (!$.isNumeric(valqty)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}				 
 	 	 
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[3].innerHTML);
 var newtotal=parseFloat((valqty*valprice)-valdiscount);
 document.getElementById("myTable").rows[ii].cells[5].innerHTML=newtotal;
 
 
}



function saveinventory()
{
	
//var fref=$('#invoice_ref').val();
//		  if(fref==''){
//		  Swal.fire({text:"{{__('The Supplier Invoice Nb field is required')}}",icon:"error",customClass:"w-auto"});
//		  return false;
//		 }	
var fprof=$('#selectpro').val();
var fclient=$('#selectclient').val();
		  if(fclient==''){
		  Swal.fire({text:"{{__('The client field is required')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }			 


let table = document.getElementById("myTable");
    let arr = [];

    for (let i = 0; i < table.rows.length; i++) {
        let row = table.rows[i];
		let valqty="0";
	
 arr.push({
            
			CODE:row.cells[0].innerHTML,
			REFERENCE:row.cells[1].innerHTML,
			DESCRIP:row.cells[2].innerHTML,
			UNITPRICE: row.cells[3].querySelector('input') 
                   ? row.cells[3].querySelector('input').value 
                   : row.cells[3].textContent.trim(),
			KITPRICE: row.cells[4].querySelector('input') 
                   ? row.cells[4].querySelector('input').value 
                   : row.cells[4].textContent.trim(),
			BOXPRICE: row.cells[5].querySelector('input') 
                   ? row.cells[5].querySelector('input').value 
                   : row.cells[5].textContent.trim()
			});	

}



var answer = confirm ("{{__('Are you sure?')}}")
   selectcode=document.getElementById("selectcode").value;
   selectpro=document.getElementById("selectpro").value;
   selectclient=document.getElementById("selectclient").value;

   valgst=document.getElementById("gst").value;
   valqst=document.getElementById("qst").value;
   selecttax=document.getElementById("selecttax").value;
   totalf=document.getElementById("totalf").value;
   tdiscount=document.getElementById("tdiscount").value;  
   balance=document.getElementById("balance").value;
   selectpatient="0";
   selecttype="8";
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
     is_free= $('#freeCmd1').is(':checked')?'Y':'N';
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
	      "selectcode":selectcode,"selectpro":selectpro,"selectclient":selectclient,"valgst":valgst,"valqst":valqst,
		  "selecttax":selecttax,"totalf":totalf,"tdiscount":tdiscount,"selectpatient":selectpatient,
		  "selecttype":selecttype,"balance":balance,"status":Mastatus,"invoice_id":invoice_id,"reqID":reqID,
		  "invoice_date_val":invoice_date_val,"clinic_id":clinic_id,"invoice_date_due":invoice_date_due,"invoice_ref":invoice_ref,"invoice_rq":invoice_rq,"tpay":tpay,"trefund":trefund,"stotal":stotal},
    url:'SavePrices',
    success: function(data){
		if(data.warning){	
			  Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		}
		if(data.success){	
			 Swal.fire({ 
              "title":data.success,
              "icon":"success",
			  "timer":3000,
			  "position":"bottom-right",
			  "showConfirmButton":false,
			  "toast": true});
			  
			window.location.href=data.location;  
		   //	$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);	
			/*document.getElementById("reqID").value=data.reqID;
			document.getElementById("reqIDRef").value=data.reqID;
			document.getElementById("reqIDPay").value=data.reqID;
			document.getElementById("invoice_id").value=data.last_id;
			document.getElementById("balance").value=data.balance;
			$("#btnadd").prop("disabled", true);
		   	 $("#btnpayment").removeAttr('disabled');
			 $("#btnmodify").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');
			$("#selecttax").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#selectpro").prop("disabled", true);
			$("#invoice_date_val").prop("disabled", true);
			$("#invoice_date_due").prop("disabled", true);
			$("#invoice_ref").prop("disabled", true);
			$("#invoice_rq").prop("disabled", true);
			$("#btndelete").removeAttr('disabled');*/
			//disable button delete in table 
			//var nb=document.getElementById("cpt").value;
			//alert(nb);
			/*var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;*/
			//alert(rdel);
			//j++;
			//alert(document.getElementById(rdel));
			/*if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
			}*/
			
			} 
		
    }
 });
}
else{

}
}

function modifyinventory()
{			$("#btncancel").removeAttr('disabled');
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);			
		    $("#btnadd").removeAttr('disabled');
			$("#selectpro").removeAttr('disabled');
			$("#invoice_date_val").removeAttr('disabled');
			$("#invoice_date_due").removeAttr('disabled');
			$("#invoice_ref").removeAttr('disabled');
			$("#invoice_rq").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			
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


function cancelinventory()
{			
			$("#btncancel").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btnprint").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');			
			$("#btnpayment").removeAttr('disabled');			
		    $("#btnmodify").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			
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

$('#formula_id,#valprice').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#valprice').val(); 
	  current_val=$("#selectcode").val();
	//if (id==1){
//		$("#sel_price").removeAttr('disabled');
	//	$("#sel_price").prop("readonly",false);
	//}else{
		// $("#sel_price").prop("disabled", true);
	//	$("#sel_price").prop("readonly",true);
	//	$.ajax({
      //      url: '{{route('generation_price',app()->getLocale())}}',
     //      type: 'get',
      //     dataType: 'json',
		//   data: {'id':id,'cost_price':cost_price,'selectcode':current_val},
        //   success: function(data){
		//	      $('#sel_price').val(data.sel_price); 
		//	      $('#sel_price1').val(data.sel_price); 
		//   }
		//	});
//	} 
	});

function downloadPDF(){	 
   var id=document.getElementById("reqID").value;	

      $.ajax({
           url: '{{route("downloadPDFInvoice",app()->getLocale())}}',
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
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
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

</script>
<script>
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
 flatpickr('#invoice_date_dexpiry', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d ",
            dateFormat: "Y-m-d "
        });		
 $('#selectpro').change(function(){
	 var id= $('#selectcode').val();
	 //alert($(this).val());
	//if (id=='0'){	 
		 $.ajax({
			   url: '{{route("Refresh_code",app()->getLocale())}}',
			   type: 'get',
			   data: { filter_supplier: $(this).val() },
			   dataType: 'json',
			 success: function(data){
			  $('#selectcode').html(data.html);
		}
		});
	//}
	clearSelect2();
 });
 
 
 
 $('#formula_idxxx').on('change', function() {
	var id= $('#formula_id').val(); 
	var cost_price= $('#cost_price').val(); 
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
		   data: {'id':id,'cost_price':cost_price},
           success: function(data){
			   $('#sel_price').val(data.sel_price); 
			   //  $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
	});		
 
 $("#NewItemModal").on('hidden.bs.modal', function(){
  //  alert('The modal is about to be hidden.');
	  $('.select2_allitems').val(null).trigger('change');
	  $("#NewItemModal").find('#fournisseur').trigger('change.select2');
	  $("#NewItemModal").find('#brand').trigger('change.select2');
	
	/*$.ajax({
           url: '{{route("Refresh_code",app()->getLocale())}}',
           type: 'get',
           data: { filter_supplier: $('#selectpro').val() },
		   dataType: 'json',
         success: function(data){
		   $("#NewItemModal").find('#fournisseur').trigger('change.select2');
		  $("#NewItemModal").find('#brand').trigger('change.select2');
		  $('#selectcode').html(data.html);
	 }
	});*/
	
	
  });		
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
@endsection	
