<div class="container-fluid">	
  	
  <div class="row mt-1">	
		   
			
			<div class="form-group  col-md-1">  
				  <label class="label-size m-0">{{__("Bill Done")}}</label>
				  <label class="slideon slideon-xs  slideon-success">
				  <input type="checkbox" id="finalizeBill" class="toggle-bill" {{(isset($order) && $order->status=='V')?'disabled':''}} {{(isset($order) && $order->status=='V')|| floatval($balanced)==0?'checked':''}} />
				  <span class="slideon-slider"></span>
				</label>
			</div>
			<div class="form-group  col-md-5">	
				
				<button id="saveBill" class="m-1 btn btn-action" {{(isset($order) && $order->status=='V') || floatval($balance)==0?'disabled':''}} >Update</button>
				<button id="printBill" class="m-1 btn btn-action" onClick="event.preventDefault();downloadPDF()" {{isset($order)?'':'disabled'}}>Print</button>
				<input type="hidden" name="bill_id" id="bill_id" value="{{isset($ReqPatient)?$ReqPatient->id:0}}"/>
					
			</div>
			<div class="form-group col-md-6 text-right">	
				<button class="m-1 btn btn-action" id="btnpayment" name="btnpayment" onClick="event.preventDefault();paymentbill()" {{(isset($order) && $order->status=='V') || floatval($balanced)==0?'disabled':''}}>{{__('Pay')}}</button>
				<button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="event.preventDefault();refundbill()" {{(isset($order) && $order->status=='V') || floatval($balanced)==0?'disabled':''}}>{{__('Donate')}}</button>
                <button class="btn btn-action" id="btndiscount" name="btndiscount" onClick="event.preventDefault();discountbill()" {{(isset($order) && $order->status=='V')|| floatval($balanced)==0?'disabled':''}}>{{__('Discount')}}</button>
		    </div>
			
			
			
	   <div class="form-group col-md-7">
		   <div id="bill-table" class="table-bordered table-sm"></div>
	   </div>
	   <div class="table-responsive form-group col-md-5">
			 <table class="table-bordered table-sm">
			   <thead>
			      <tr class="text-center">
				    <th></th>
					<th style="display:none;">Total</th>
					<th>Remianing</th>
					<th>Total Payment</th>
					<th>Total Discount</th>
					<th>Total Donate</th>
					<th>Exchange Rate</th>
				  </tr>
			   </thead>
			   <tbody>
			       <tr>
				      <th>LBP</th>
					  <td style="display:none;"><input  value="{{$totalf}}" class="form-control"  id="totalf"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="balance"  value="{{$balance==0?0:$balance}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpay"  value="{{ $pay==0?0:number_format($pay, 2, '.', ',') }}" disabled  /></td>
					  <td><input  class="billcss form-control"  value="{{isset($ReqPatient->bill_discount) && $ReqPatient->bill_discount!=''?number_format($ReqPatient->bill_discount,2,'.',','):0}}" id="tdiscount"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefund"  value="{{ $refund==0?0:number_format($refund, 2, '.', ',') }}" disabled  /></td>
				      <td rowspan="2"><input  class="billcss form-control"  id="exchange_rate"  value="{{$ReqPatient->exchange_rate}}" disabled  /></td>
				   </tr>
				   <tr>
				      <th>USD</th>
					  <td style="display:none;"><input  class="form-control"  id="stotal"  value="{{$stotal}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="balanced"  value="{{$balanced==0?0:number_format($balanced, 2,'.','')}}" disabled  /></td>
					  <td><input  class="billcss form-control"  id="tpayd"  value="{{$payd==0?0:number_format($payd, 2,'.','')}}" disabled  /></td>
					  <td><input  class="billcss form-control"  value="{{isset($ReqPatient->bill_discount_us) && $ReqPatient->bill_discount_us!=''?number_format($ReqPatient->bill_discount_us,2,'.',''):0}}" id="tdiscountd"   disabled  /></td>
					  <td><input  class="billcss form-control"  id="trefd"  value="{{$refundd==0?0:number_format($refundd,2,'.','')}}" disabled  /></td>
				   </tr>
			   </tbody>
			 </table>
			</div>
	   
   </div>
   
</div>										
