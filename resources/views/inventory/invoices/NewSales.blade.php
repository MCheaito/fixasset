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
				<div class="card-header text-white">
			      <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('New Invoice')}} - {{__($type1->name)}}</h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				  
				</div>
				
				<div class="card-body p-0">
					<div  class="row m-1"> 
						<div class="col-md-4">	
						 <button class="m-1 btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentinvoice()">{{__('Pay')}}</button>
						 <button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="refundinvoice()">{{__('Reimburse')}}</button>
					   </div>
					      <div class="col-md-4 col-4" hidden >
                                          	<div class="form-group">
												<label class="label-size">{{__('Stock')}}</label>
												<div class="input-group">
													<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-venus-mars"></i></span></div>
													<select class="custom-select rounded-0" name="fromstock" id="fromstock">
														<option value="">{{__('Chosse Stock')}}</option>
														
														<option
															value="N" >
																{{__('From Lab To G.Stock')}}
														</option>
														<option
															value="Y">
																{{__('From G.Stock To Lab')}}
														</option>
														
													</select>
												</div>
										</div>			  
						   </div>
					   <div class="col-md-4">
						@if(Session::has('dashboard_pid'))
				          <a href="{{ route('patientslist.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						@else
					    <a href="{{ route('inventory.invoices.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						@endif
						<!--<button class="m-1 float-right btn btn-action"  id="btnprintLabel" name="btnprintLabel" onClick="downloadPDFLabel()">{{__('Print label')}}</button>-->
						<button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>
						<button type="button" id="btnsend" class="m-1 float-right btn btn-action" onclick="send_email_pat()">{{__('Send')}}</button>

					   </div>
				    </div>
					<div  class="row m-1">  
					
									<div class="col-md-3">
										<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                               							
						  <div class="col-md-3">
							 <label for="reqID_val"><b>{{__('Invoice Nb')}}</b></label>
							 <input  type="text" class="form-control" name="reqID_val"  value="" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="invoice_id"   value=""/>
						  </div>
						  <div class="col-md-3">
							 <label for="invoice_date_val"><b>{{__('Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_val" id="invoice_date_val" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
					  </div>
					   <div class="form-group col-md-3 select2-teal">
					    <label for="selectpatient"><b>{{__('User')}}</b></label>
						<input type="text" class="form-control" name="selectpatient1" readonly="true" value="{{$myName}}"/>
						<input type="hidden" id="selectpatient" name="selectpatient" value="{{$myId}}"/>
						</div>
				</div>
						
					
					<div class="row m-1">
					
						<div class="col-md-4 col-12">
							<label for="selectcode"><b>{{__('Code')}}</b>
							<span class="ml-1" id="NbItemStock" style="color:red;"></span>
							<span class="ml-2"><button class="btn btn-xs text-white txt-bg"  id="AddNewItem"  data-toggle="modal" data-target="#NewItemModal" style="border-radius:50%;"><i class="fa fa-plus fa-lg"></i>{{__('')}}</button></span>
							</label>
							<select class="select2_allitems custom-select rounded-0" name="selectcode" id="selectcode" style="width:100%;" >
									
							</select>
							<input  type="hidden" id="typeCode"/>
							</div>	
						  <div class="col-md-2 col-8">
							<label for="name"><b>{{__('BarCode')}}&#xA0;&#xA0;</b></label>
							<div class="form-group">
							<input type="text" class="form-control" name="barcode" id="barcode"  >
							</div> 	
					     </div>	
						<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Quantity')}}</b></label>
								<input  class="form-control" value="" id="valqty"/>
								<input  type="hidden" id="cpt"/>
								<input  type="hidden" id="tbl"/>
							</div>
						</div>
					
					
											<div class="col-md-2 col-6">
														<label for="name"><b>{{__('Sel Price')}}&#xA0;&#xA0;</b></label>
															<div class="form-group">
															<input type="text" class="form-control" name="sel_price" id="sel_price"  >
															<input type="hidden" class="form-control" name="sel_price1" id="sel_price1" value="" >	
												</div> 	
											</div>
					
					
					<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Disc%')}}</b></label>
								<input  class="form-control" id="valdiscountP" value=""/>
							</div>
						</div>
					<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Discount')}}</b></label>
								<input  class="form-control" id="valdiscount" value=""/>
							</div>
						</div>
					
				
						<div class="col-md-1 col-6">
						<div class="d-flex flex-column">					
					<label for="selecttax"><b>{{__("Tax")}}</b></label>					
						<input id="taxable" type="checkbox"  style="height:22px;width:22px;" name="taxable"/>
					</div> 	
				</div> 
					<div class="col-md-2 col-6">
							<label for="selectcode"><b>{{__('Offer Type')}}</b></label>
							<select class="select2_data custom-select rounded-0" name="selectdiscounttype" id="selectdiscounttype" style="width:100%;" >
									@foreach($typediscount as $typediscounts)
									<option value="{{$typediscounts->id}}">{{__($typediscounts->name)}}</option>
									@endforeach 
							</select>
							
							</div>
					<div class="col-md-4 col-8">
							<label for="name"><b>{{__('Description')}}&#xA0;&#xA0;</b></label>
							<div class="form-group">
							<input type="text" class="form-control" name="description" id="description"  >
							</div> 	
					</div>				
					<div class="col-md-2 col-4">
					<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-sm btn-action"  id="btnadd" onclick="insRow()">{{__('Insert')}}</button>
						</div>
					</div>
                   
                    <div class="col-md-4">
							<label for="name"><b>{{__('General Comment')}}&#xA0;&#xA0;</b></label>
							<div class="form-group">
							<textarea class="form-control" name="comment" id="comment" rows="1">{{old('comment')}}</textarea>
							</div> 	
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
											<th scope="col" style="font-size:16px;">{{__('Item')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item name')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item Qty')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Item Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Discount')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Total')}}</th>
											<th scope="col" style="font-size:16px;">{{__('T.Disc')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Tax')}}</th>
											<th scope="col"></th>
											<th scope="col" style="font-size:16px;display:none;">{{__('TVQ')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('TPS')}}</th>
										</tr>
									</thead>
								   <tbody></tbody>
								</table> 
							</div>
						</div>
						<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
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
							<div class="mb-1 d-none d-md-block col-md-2">
							
							</div>
							<div class="mb-1 d-none d-md-block col-md-4">
							
							</div>
							
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
<!--include patient card modal-->
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
@include('inventory.invoices.send_by.sendInvoiceModal')
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
			url: '{{route("inventory.items.loadItems",app()->getLocale())}}', // Replace with the actual route URL
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
}
</script>
<script>
$(document).ready(function(){
			
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$('.select2_patient').select2({
			theme: 'bootstrap4',
			width: 'resolve',
			language:'{{app()->getLocale()}}',
			placeholder: "{{__('Choose a patient')}}",
			ajax: {
				url: '{{route("patient.loadPat",app()->getLocale())}}', // Replace with the actual route URL
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						 _token: "{{ csrf_token() }}",
						clinic_num:$('#clinic_id').val(),
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
		  
   $('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadItems",app()->getLocale())}}', // Replace with the actual route URL
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
			$("#valprice").val("0.00");
			$("#initprice").val("0.00");
			//$("#sel_price").val("0.00");
			$("#sel_price1").val("0.00");
			//$("#valdiscount").val("0.00");
			//$("#valdiscountP").val("0.00");
			$("#sel_price").val("");
			$("#valdiscount").val("");
			$("#valdiscountP").val("");
			$("#totalf").val("0.00");
			$("#tdiscount").val("0.00");
			$("#qst").val("0.00");
			$("#gst").val("0.00");
			$("#balance").val("0.00");
			$("#payamount").val("0.00");
			$("#refamount").val("0.00");
			$("#valamount").val("0.00");
			$("#stotal").val("0.00");
			$("#tbl").val("");
			$("#description").val("");
			$("#comment").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnsend").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#balancerefund").val("0.00");	
			$("#totalrefund").val("0.00");
			$("#valamountrefund").val("0.00");
			$("#balancerefund").val("0.00");
			$("#tpay").val("0.00");
			$("#trefund").val("0.00");			
			$("#btndelete").prop("disabled", true);
			//$("#selectpro").prop("disabled", true);
		$("#selectpatient").removeAttr('disabled');	
	
$('#valqty,#sel_price,#valdiscountP').on('change', function()
{
 var discp=	($("#valqty").val()*$("#sel_price").val()*$("#valdiscountP").val())/100;
  $("#valdiscount").val(discp);
});


	
$('#barcode').on('change', function()
{
    current_val=$("#barcode").val();
	fromstock=$("#fromstock").val();
  	if(current_val==''){
		clearSelect2();
		return false;
	}
	
	$.ajax({
		url:'{{route("fillBarcode",app()->getLocale())}}',
		   type: 'get',
		    data:{"_token": "{{ csrf_token() }}","barcode":current_val,"fromstock":fromstock,inv_type:'sale'},
		  dataType: 'json',
		  success: function(data){
			  if(data.error){
					  $("#barcode").val('');
					  clearSelect2();
					  Swal.fire({html:data.error,icon:'error',customClass:'w-auto'});
					  
				  }else{
			  $("#sel_price").val(data.Price);
			  $("#typeCode").val(data.gen_types);
			 
             $('.select2_allitems').empty();
			 var itm=[{id:data.item_id,text:data.item_description}];
			  $('.select2_allitems').select2({theme:'bootstrap4',width:'resolve',data:itm});
			 
			 $("#NbItemStock").text("In Stock:"+" "+data.NbItemStock);
			 if (data.tax=='Y'){
			document.getElementById("taxable").checked = true;
			 }
			 $("#valdiscount").val('0.00');
			 $("#valdiscountP").val('0.00');	
			 if (data.gen_types=='2'){
				$("#valdiscount").prop("disabled", true);
				$("#valdiscountP").prop("disabled", true);  				
			  }else{
				 $("#valdiscount").removeAttr('disabled');
                 $("#valdiscountP").removeAttr('disabled'); 				 
			  }
			}
}

      });
});	
			
$('#selectcode').on('change', function()
{
   // alert(this.value); //or alert($(this).val());
   document.getElementById("taxable").checked = false;
   current_val=$("#selectcode").val();
   fromstock=$("#fromstock").val();
   if(current_val=='0' || current_val==null){
//		 $("#sel_price").val('0.00');
         $("#NbItemStock").text('');
	//	 $("#valdiscount").val('0.00');
	//	 $("#valdiscountP").val('0.00');
        $("#sel_price").val('');
		 $("#valdiscount").val('');
		 $("#valdiscountP").val('');		 
		return false;
	}
	$.ajax({
		url: 'fillPriceInv',
		data:{"selectcode":current_val,"fromstock":fromstock},
		   type: 'get',
		  dataType: 'json',
		  success: function(data){
			   $("#sel_price").val(data.Price);
			  $("#typeCode").val(data.gen_types);
             $("#NbItemStock").text("In Stock:"+" "+data.NbItemStock);
			 if (data.tax=='Y'){
			document.getElementById("taxable").checked = true;
			 }
			 if (data.Price=="0.00"){
				 $("#sel_price").val('')
			 }
			// $("#valdiscount").val('0.00');
			// $("#valdiscountP").val('0.00');	
			  $("#valdiscount").val('');
			 $("#valdiscountP").val('');	
			 if (data.gen_types=='2'){
				$("#valdiscount").prop("disabled", true);
                $("#valdiscountP").prop("disabled", true);  				
			  }else{
				 $("#valdiscount").removeAttr('disabled');
                 $("#valdiscountP").removeAttr('disabled');				 
			  }
			 
			 
			// if (data.Category=='3'){ $("#description").removeAttr('disabled');	
			// }else{
			//	$("#description").prop("disabled", true); 
			// }
		 }
      });
});	

$('#selecttype').change(function(e){
	
	 current_val=$("#selecttype").val();
	 if(current_val=="4"){
		$("#selectpro").removeAttr('disabled');	
		$("#selectpatient").prop("disabled", true);			
		}
	if(current_val=="3"){
		$("#selectpatient").removeAttr('disabled');	
		$("#selectpro").prop("disabled", true);			
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

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
       });
		



 function paymentinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$("#valamount").val("0.00");
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
$("#valamountrefund").val("0.00");
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
var q = $('#valqty').val(),sp=$('#sel_price').val(),dis=$('#valdiscount').val(),disp=$('#valdiscountP').val(); 
  
  if(sp==''){
	  sp="0.00";
	  document.getElementById("sel_price").value="0.00";
  }
  if(dis==''){
	  dis="0.00";
	  document.getElementById("valdiscount").value="0.00";
  }
  if(disp==''){
	  disp="0.00";
	  document.getElementById("valdiscountP").value="0.00";
  }
   if (!$.isNumeric(q)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
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

if (!$.isNumeric(dis)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Discount')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
if (!$.isNumeric(disp)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Discount')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}		
var typecode=$('#typeCode').val();
var discval=$('#sel_price').val();	
if ((typecode=="2") && (discval>=0))
{
discval=-1*discval;
$('#sel_price').val(discval);	
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
document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripVerre = document.getElementById("description").value;
 //if (descripVerre==''){
 var descripcode=$("#selectcode option:selected").text();
 var lendescripcode=descripcode.indexOf("-(");
var descripcode = descripcode.substring(0,lendescripcode)+" "+document.getElementById("description").value;
 //}else{
 //var descripcode=document.getElementById("description").value;
// }
 var descriptypediscount=$("#selectdiscounttype option:selected").text();
 var taxable=document.getElementById("taxable").checked;
 //alert(taxable);
 if (taxable==true){taxablet='Y';}else{taxablet='N';}
 
 var total=(parseFloat(document.getElementById("valqty").value)*parseFloat(document.getElementById("sel_price").value))-parseFloat(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("selectcode").value;
b.innerHTML=descripcode;
c.innerHTML=document.getElementById("valqty").value;
d.innerHTML=document.getElementById("sel_price").value;
//e.innerHTML=document.getElementById("valdiscount").value;
id_discount= ' onchange=discRow(this,"discount'+document.getElementById("cpt").value+'") oninput=discRow(this,"discount'+document.getElementById("cpt").value+'")';
e.innerHTML='<input type="text" class="form-control" size="5" id="discount'+document.getElementById("cpt").value+'" value="'+document.getElementById("valdiscount").value+'"'+id_discount+'>';

f.innerHTML=total;
g.innerHTML=descriptypediscount;
h.innerHTML=taxablet;
id_delete= ' onclick=deleteRow(this,"discount'+document.getElementById("cpt").value+'")';
i.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}"'+id_delete+'>';
j.style.display = 'none';
k.style.display = 'none';
   selcode=document.getElementById("selectcode").value;
    selecttype="2";
   valqty=document.getElementById("valqty").value;
   valdiscount=document.getElementById("valdiscount").value;
   cpt=document.getElementById("cpt").value;
   selecttax=document.getElementById("selecttax").value;
   selectqst=document.getElementById("qst").value;
   selectgst=document.getElementById("gst").value;
   typediscount=document.getElementById("selectdiscounttype").value;   
   taxable=document.getElementById("taxable").value;
	valprice=document.getElementById("sel_price").value;
  // tbl=$("#tbl").val();
   totalf=document.getElementById("totalf").value;
   tdiscount=document.getElementById("tdiscount").value;
	$.ajax({
	url: 'addInvoiceRow',
		   type: 'get',
		   data:{"cpt":cpt,"selecttype":selecttype,"valprice":valprice,"typediscount":typediscount,"taxable":taxablet,"selectcode":selcode,"valqty":valqty,"valdiscount":valdiscount,"selecttax":selecttax,"totalf":totalf,"tdiscount":tdiscount,"selectqst":selectqst,"selectgst":selectgst},
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
			 // Swal.fire({ 
              //"text":data.success,
              //"icon":"success",
			  //"customClass": "w-auto"});
		   //	$('#htmlTable').empty();
			// $('#htmlTable').html(data.htmltable);	
			//$("#tbl").val(data.tbl);	
			document.getElementById("totalf").value=data.totalf;
			document.getElementById("tdiscount").value=data.tdiscount;
			document.getElementById("stotal").value=data.stotal;
			//document.getElementById("balance").value=data.balance;
			document.getElementById("gst").value=data.gst;
			document.getElementById("qst").value=data.qst;
			j.innerHTML=data.uqst;
			k.innerHTML=data.ugst;
			//document.getElementById("valdiscount").value="0.00";
			//document.getElementById("valdiscountP").value="0.00";
			document.getElementById("valdiscount").value="";
			document.getElementById("valdiscountP").value="";
			if (document.getElementById("sel_price").value=="0.00"){
			document.getElementById("sel_price").value="";
			}
			document.getElementById("description").value="";
		    $("#btnsave").removeAttr('disabled');
			
			} 
			 
		 }
      }); 

}
function deleteRow(r,rd)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valqty=parseInt(document.getElementById("myTable").rows[ii].cells[2].innerHTML);
//var rdisc=rd;
//alert(rd);
 //if (document.getElementById(rdisc)!=null){
 // var  valdiscount=parseFloat(document.getElementById(rdisc).value);
 
 // }
 var  valdiscount=parseFloat(document.getElementById("myTable").rows[ii].cells[4].getElementsByTagName("input")[0].value);
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[3].innerHTML);
 var  code=parseInt(document.getElementById("myTable").rows[ii].cells[0].innerHTML);
   var  taxable=document.getElementById("myTable").rows[ii].cells[7].innerHTML;

//alert(document.getElementById("myTable").rows[ii].cells[1].innerHTML);
 var selecttax=document.getElementById("selecttax").value;
var totalf=parseFloat(document.getElementById("totalf").value);
var tdiscount=parseFloat(document.getElementById("tdiscount").value);

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
 		    var qstf=parseFloat((total*taxqst)/100).toFixed(2);
			//alert(qstf);
	        var gstf=parseFloat((total*taxgst)/100).toFixed(2);
			document.getElementById("qst").value=parseFloat(parseFloat(document.getElementById("qst").value)-parseFloat(qstf)).toFixed(2);
			document.getElementById("gst").value=parseFloat(parseFloat(document.getElementById("gst").value)-parseFloat(gstf)).toFixed(2);
			
			 totalf=parseFloat(parseFloat(totalf)-parseFloat(total)).toFixed(2);
			 tdiscount=parseFloat(parseFloat(tdiscount)-parseFloat(valdiscount)).toFixed(2);
		     tpay=parseFloat(document.getElementById("tpay").value);
			trefund=parseFloat(document.getElementById("trefund").value);
			// balance=parseFloat(parseFloat(totalf)+parseFloat(document.getElementById("gst").value)+parseFloat(document.getElementById("qst").value)).toFixed(2);
			 stotal=parseFloat(parseFloat(totalf)+parseFloat(document.getElementById("gst").value)+parseFloat(document.getElementById("qst").value)).toFixed(2);
			$("#stotal").val(stotal);
			// $("#balance").val(balance);
			 $("#totalf").val(totalf);
			 $("#tdiscount").val(tdiscount);
			 document.getElementById('myTable').deleteRow(ii);
		if(document.getElementById('myTable').rows.length==1){
			//$("#balance").val("0.00");
			//$("#balance").val(tpay-trefund);
			$("#totalf").val("0.00");
			$("#stotal").val("0.00");
			$("#tdiscount").val("0.00");
			$("#gst").val("0.00");
			$("#qst").val("0.00");
			
			//$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnsend").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);	
			$("#btnadd").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			//nbalance=$("#tpay").val()-$("#trefund").val();
			//$("#balance").val(nbalance);
			$("#cpt").val("0");
}
		 }
      });

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
d.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(2);
  var deposit=document.getElementById("deposit").checked;

 if (deposit==true){tdeposit='Y';}else{tdeposit='N';}
e.innerHTML=tdeposit;
f.innerHTML=(document.getElementById("pay_rq").value);

g.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';
var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)+parseFloat(document.getElementById("valamount").value)).toFixed(2);
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
 var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)-parseFloat(valpay)).toFixed(2);
document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)+parseFloat(valpay)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
if(document.getElementById('myTablePay').rows.length==1){
			$("#payamount").val("0.00");
			
			
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
d.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(2);
e.innerHTML=(document.getElementById("ref_rq").value);

f.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(2);
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
 var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)-parseFloat(valref)).toFixed(2);
document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)-parseFloat(valref)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
if(document.getElementById('myTableRef').rows.length==1){
			$("#refamount").val("0.00");
			
			
}
$("#btnsaverefund").prop("disabled", false);		
}




function saveinventory()
{
var fref=$('#selectpatient').val();
		  //if(fref=='0' || fref==null){
		//  Swal.fire({text:"{{__('The Patient  field is required')}}",icon:"error",customClass:"w-auto"});
		//  return false;
		// }		
//for <td>
var x = document.getElementById("myTable").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//,"INITPRICE":document.getElementById("myTable").rows[i].cells[10].innerHTML
//alert(document.getElementById("myTable").rows[1].cells[10].innerHTML);
for(i=0;i<document.getElementById("myTable").rows.length;i++){
	disc="0.00";
	var s="discount"+i;
	if (document.getElementById(s)!=null){

//	 disc =document.getElementById(s).value;
     disc =document.getElementById("myTable").rows[i].cells[4].getElementsByTagName("input")[0].value;
	 

	}
//arr[i]={"CODE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"DESCRIP":document.getElementById("myTable").rows[i].cells[1].innerHTML,"QTY":document.getElementById("myTable").rows[i].cells[2].innerHTML,"PRICE":document.getElementById("myTable").rows[i].cells[3].innerHTML,"DISCOUNT":document.getElementById("myTable").rows[i].cells[4].innerHTML,"TOTAL":document.getElementById("myTable").rows[i].cells[5].innerHTML,"TYPEDISCOUNT":document.getElementById("myTable").rows[i].cells[6].innerHTML,"TAXABLE":document.getElementById("myTable").rows[i].cells[7].innerHTML,"EXPIRY_DATE":document.getElementById("invoice_date_val").value,"FORMULAID":'0',"SELPRICE":'0.00',"INITPRICE":'0.00'};	
arr[i]={"CODE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"DESCRIP":document.getElementById("myTable").rows[i].cells[1].innerHTML,"QTY":document.getElementById("myTable").rows[i].cells[2].innerHTML,"PRICE":document.getElementById("myTable").rows[i].cells[3].innerHTML,"DISCOUNT":disc,"TOTAL":document.getElementById("myTable").rows[i].cells[5].innerHTML,"TYPEDISCOUNT":document.getElementById("myTable").rows[i].cells[6].innerHTML,"TAXABLE":document.getElementById("myTable").rows[i].cells[7].innerHTML,"TVQ":document.getElementById("myTable").rows[i].cells[9].innerHTML,"TPS":document.getElementById("myTable").rows[i].cells[10].innerHTML,"EXPIRY_DATE":document.getElementById("invoice_date_val").value,"FORMULAID":'0',"SELPRICE":'0.00',"INITPRICE":'0.00'};	

}
  selectcode=document.getElementById("selectcode").value;
   valgst=document.getElementById("gst").value;
   valqst=document.getElementById("qst").value;
   selecttax=document.getElementById("selecttax").value;
   totalf=document.getElementById("totalf").value;
   tdiscount=document.getElementById("tdiscount").value;
	//tdiscount=$('#tdiscount').val();   
   balance=document.getElementById("balance").value;
   selectpatient=document.getElementById("selectpatient").value;
   invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_id=document.getElementById("invoice_id").value;
   reqID=document.getElementById("reqID").value;
    clinic_id=document.getElementById("clinic_id").value;
	stotal=document.getElementById("stotal").value;
    tpay=document.getElementById("tpay").value;
	 trefund=document.getElementById("trefund").value;
	 comment=document.getElementById("comment").value;
	   selectpro="0";
      selecttype="2";
	   invoice_date_due=document.getElementById("invoice_date_val").value;
	    fromstock=document.getElementById("fromstock").value;
   Mastatus='N';
   if (invoice_id!='')
   {
	 Mastatus='M';   
   }
   
var answer = confirm ("{{__('Are you sure?')}}")
   
if (answer){
var myjson=JSON.stringify(arr);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}",data:myjson,"invoice_date_due":invoice_date_due,"fromstock":fromstock,
	      "selecttype":selecttype,"selectpro":selectpro,"selcode":selcode,"valgst":valgst,"valqst":valqst,
		  "selecttax":selecttax,"totalf":totalf,"tdiscount":tdiscount,"selectpatient":selectpatient,
		  "balance":balance,"status":Mastatus,"invoice_id":invoice_id,"reqID":reqID,
		  "invoice_date_val":invoice_date_val,"clinic_id":clinic_id,"tpay":tpay,"trefund":trefund,"stotal":stotal,"invoice_rq":comment},
    url:'SaveInventory',
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
			document.getElementById("balance").value=data.balance;*/
			
			/*document.getElementById("qst").value=data.total_qst;
			document.getElementById("gst").value=data.total_gst;
			document.getElementById("tdiscount").value=data.total_discount;
			document.getElementById("stotal").value=data.total_without_tax;
			document.getElementById("totalf").value=data.total_with_tax;*/
			
			/*$("#btnadd").prop("disabled", true);
		   	 $("#btnpayment").removeAttr('disabled');
			$("#btnmodify").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#btnsend").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');
			$("#selecttax").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#selectpatient").prop("disabled", true);
			$("#invoice_date_val").prop("disabled", true);
			$("#comment").prop("disabled", true);
    		$("#btndelete").removeAttr('disabled');
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
			//alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			var rdisc="discount"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			document.getElementById(rdisc).setAttribute('disabled',false);
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
			$("#btnsend").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);			
		    $("#btnadd").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			$("#selectpatient").removeAttr('disabled');
			$("#invoice_date_val").removeAttr('disabled');
			$("#comment").removeAttr('disabled');
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			 var rdiscount="discount"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//	$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = false;
				document.getElementById(rdiscount).disabled = false;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
}
function discRow(r,rd)
{	
//alert(r);
var ii=r.parentNode.parentNode.rowIndex;
//var ii=rd.replace('discount', '');
//alert(ii);
var nb=document.getElementById("cpt").value;
//alert(nb);
// change discount and total in row
 var  valqty=parseInt(document.getElementById("myTable").rows[ii].cells[2].innerHTML);
 //var rdisc="discount"+rd;
 rdisc=rd;
  //var  valdiscount=parseInt(document.getElementById(rdisc).value);
  var valdiscount= parseFloat(document.getElementById("myTable").rows[ii].cells[4].getElementsByTagName('input')[0].value);
  
  if (!$.isNumeric(valdiscount)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Discount')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		 document.getElementById("myTable").rows[ii].cells[4].getElementsByTagName('input')[0].value="0.00";
		 valdiscount="0.00";
		//  return false;		
		}				 
	 
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[3].innerHTML);
 var newtotal=(valqty*valprice)-valdiscount;
 document.getElementById("myTable").rows[ii].cells[5].innerHTML=newtotal;
// end

// get tax
var selecttax=document.getElementById("selecttax").value;
$.ajax({
		url: 'fillInvoiceDel',
		   type: 'get',
		  dataType: 'json',
		 data:{"code":'',"selecttax":selecttax,"taxable":'Y'},
		  success: function(data){
			var taxqst=parseFloat(data.qst).toFixed(3);
			var taxgst=parseFloat(data.gst).toFixed(3);	
			var j=1;
			var newtdiscount=0;
			var newqstf=0.00;
			var newgstf=0.00;
			var newtotalf=0.00;
			
			/*for(i=1;i<=nb;i++){
			var rdisc="discount"+j;
			if (document.getElementById(rdisc)!=null){
				newtdiscount +=parseInt(document.getElementById(rdisc).value);
				}
			j++;
			}*/
			
			//alert(ii);
			for(i=1;i<=nb;i++){
			
			if(document.getElementById("myTable").rows[i] !=null){
				var total=document.getElementById("myTable").rows[i].cells[5].innerHTML;
				var taxable=document.getElementById("myTable").rows[i].cells[7].innerHTML;
				var discount = document.getElementById("myTable").rows[i].cells[4].getElementsByTagName('input')[0].value;
				if (taxable=='Y'){
				 newqstf +=((total*taxqst)/100);
				 newgstf +=((total*taxgst)/100);	
					}
				newtotalf +=parseFloat(total);
				newtdiscount +=parseFloat(discount);
			}
			}
			document.getElementById("qst").value=parseFloat(newqstf).toFixed(2);
			document.getElementById("gst").value=parseFloat(newgstf).toFixed(2);
            document.getElementById("tdiscount").value=parseFloat(newtdiscount).toFixed(2);
			document.getElementById("totalf").value=parseFloat(newtotalf).toFixed(2);
			stotal=parseFloat(parseFloat(document.getElementById("totalf").value)+parseFloat(document.getElementById("gst").value)+parseFloat(document.getElementById("qst").value)).toFixed(2);
			$("#stotal").val(stotal);
		  }
		});
			
		
			}

function cancelinventory()
{			
			$("#btncancel").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btnprint").removeAttr('disabled');
			$("#btnsend").removeAttr('disabled');
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
			var rdiscount="discount"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = true;
				document.getElementById(rdiscount).disabled = true;
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
	var nbalance=parseFloat(data.nbalance).toFixed(2);
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
	var nbalance=parseFloat(data.nbalance).toFixed(2);
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
 	


$('#formula_id').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#valprice').val(); 
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
			  // $('#sel_price').val(data.sel_price); 
			  //   $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
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

function send_email_pat(){
		 
		 var patient_num = $('#selectpatient').val();
		 if(patient_num==null || patient_num=='0'){
			 Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
			 return false;
		 }
		 
		 var inv_id =  $('#invoice_id').val();
		 
				  $.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							   }
						   });
				  $.ajax({
						type : 'POST',
						url : '{{route("inventory.invoices.inv_pat_list",app()->getLocale())}}',
						data : { inv_id: inv_id,patient_num:patient_num},
						dataType: 'JSON',
						success: function(data){
						  $('#sendInvoiceModal').find('#ext_pat_id').val(patient_num);
						  $('#sendInvoiceModal').find('#ext_inv_id').val(inv_id);
						  $('#sendInvoiceModal').find('#patient_data').html(data.html_patient);
						  $('#sendInvoiceModal').modal('show');
						}
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
function openPatientCardModal(){
  var patient_num = $('#selectpatient').val();
		 if(patient_num==null || patient_num=='0'){
			 Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
			 return false;
		 }
	     
			 $.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						   }
					   });
			  $.ajax({
					type : 'POST',
					url : '',
					data : { patient_num :patient_num},
					dataType: 'JSON',
					success: function(data){
						  
						  $('#patientCardModal').find('.modal-dialog').removeClass('modal-xl');
						  $('#patientCardModal').find('.modal-dialog').addClass('modal-lg');
						  $('#patientCardModal div.modal-body').find('#patient_data').html(data.html_patient);
						  $('#patientCardModal div.modal-body').find('#patient_events').html(data.html_rdvs);
						  $('#patientCardModal div.modal-body').find('#patient_visits').html(data.html_visit);
						  $('#patientCardModal div.modal-body').find('#patient_hist').html(data.html_hist);
						  $('#patientCardModal div.modal-body').find('#patient_treatment_plans').html(data.html_treatment_plans);
						  $('#patientCardModal div.modal-body').find('#patient_medical_documents').html(data.html_medical_documents);
						  $('#patientCardModal div.modal-body').find('#patient_rx').html(data.html_rx);
						  $('#patientCardModal div.modal-body').find('#patient_cmds').html(data.html_cmds);
						  $('#patientCardModal div.modal-body').find('#patient_bills').html(data.html_bills);
						  $('#patientCardModal div.modal-body').find('#patient_inventory_bills').html(data.html_inventory_bills);
                          $('#patientCardModal div.modal-body').find('#patient_med_presc').html(data.html_prescs);
						  $('#patientCardModal div.modal-body').find('#patient_stripe').html(data.html_stripe);
						  //$('#patientCardModal').find('#update_patient').hide();
						  $('#patientCardModal').modal('show');
					}
		          });
	
} 
function downloadStripePDF(id){
   $.ajax({
           url: '',
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
			link.download=('Stripe.pdf');
			link.click();
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
  
			    });
	}  	
</script>
<script>
        flatpickr('#invoice_date_due', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#invoice_date_val', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
 flatpickr('#date_pay', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#date_refund', {
            allowInput : true,
			altInput: true,
			enableTime: false,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });		
 $("#NewItemModal").on('hidden.bs.modal', function(){
  //  alert('The modal is about to be hidden.');
	$('.select2_allitems').val(null).trigger('change');
    $("#NewItemModal").find('#fournisseur').trigger('change.select2');
	$("#NewItemModal").find('#brand').trigger('change.select2');
	
  });		




</script>
	
@endsection	
