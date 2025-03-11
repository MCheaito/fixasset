<div class="container-fluid">	
  	
  <div class="row mt-1">	
		    <div class="mb-1 col-md-2 col-6">
				<label for="name">{{__('Discount')}}</label>
				<input  class="text-center form-control"  value="{{$ReqPatient->bill_discount}}" id="tdiscount"   disabled  />
			</div>
			<div class="mb-1 col-md-2 col-4" style="display:none;">
				   <label for="name">{{__('Total USD')}}</label>
				   <input  class="text-center form-control"  id="stotal"  value="{{$stotal}}" disabled  />
			</div>
			<div class="mb-1 col-md-2 col-4" style="display:none;">
			   <label for="name">{{__('Total LBP')}}</label>
			   <input  value="{{$totalf}}" class="text-center form-control"  id="totalf"   disabled  />
			</div>
			<div class="mb-1 col-md-2 col-6">
			   <label for="name">{{__('Balance')}}</label>
			   <input  class="text-center form-control"  id="balance"  value="{{$balance}}" disabled  />
			</div>
			<div class="mb-1 col-md-2 col-6">
				   <label for="name">{{__('T.Payment')}}</label>
				   <input  class="text-center form-control"  id="tpay"  value="{{ number_format($pay, 2, '.', ',') }}" disabled  />
			</div>
			<div class="mb-1 col-md-2 col-6">
			   <label for="name">{{__('T.Refund')}}</label>
			   <input  class="text-center form-control"  id="trefund"  value="{{ number_format($refund, 2, '.', ',') }}" disabled  />
			</div>
			<div class="mb-1 col-md-4">	
				<button class="m-1 btn btn-action" id="btnpayment " name="btnpayment" onClick="event.preventDefault();paymentbill()" {{isset($order) && $order->status!='V'?'':'disabled'}}>{{__('Pay')}}</button>
				<button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="event.preventDefault();refundbill()" {{isset($order) && $order->status!='V'?'':'disabled'}}>{{__('Reimburse')}}</button>
                <button class="btn btn-action" id="btndiscount" name="btndiscount" onClick="event.preventDefault();discountbill()" {{isset($order) && $order->status!='V'?'':'disabled'}}>{{__('Discount')}}</button>
		    </div>
			<div class="mb-1 col-md-12">	
				<button id="saveBill" class="m-1 btn btn-action" {{isset($order) && $order->status!='V'?'':'disabled'}} >Update</button>
				<button id="printBill" class="m-1 btn btn-action" onClick="event.preventDefault();downloadPDF()" {{isset($order)?'':'disabled'}}>Print</button>
				<input type="hidden" name="bill_id" id="bill_id" value="{{isset($ReqPatient)?$ReqPatient->id:0}}"/>
		    </div>
		
	   <div class="form-group col-md-12">
		   <div id="bill-table" class="table-bordered"></div>
	   </div>
	   
   </div>
   
</div>										
