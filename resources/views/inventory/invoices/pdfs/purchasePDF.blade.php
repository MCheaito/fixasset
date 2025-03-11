<!--
 DEV APP
 Created date : 15-4-2023
-->
<!DOCTYPE html>
<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <link rel="stylesheet" href="{{asset('dist/custom/pdfs_stylish.css')}}">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Bill')}}</title>
			
		</head>
	<body style="font-size:14px;">	
   
            <div style="width:100%;">
	               
					<div>
                      <div   class="top_line">
                           
						   <div class="top_left">
							 <div>
							 @if(isset($logo))
								  <img src="{{ $logo->logo_path }}" style="position:relative;height:70px;"/>
						      @else
							  <b>{{__('Purchase invoice')}}</b>
						      @endif
							 </div>
                            </div>
					       <div class="top_center"><b>{{$clinic->full_name}}</b></div>
					       <div class="top_right">
							    <div><b>{{__('Purchase')}}:</b> {{$Invoice->clinic_inv_num}}</div>
								<div><b>{{__('Date')}}:</b> {{Carbon\Carbon::parse($Invoice->date_invoice)->format('Y-m-d')}}</div>
								
                            </div>
                            <div style="clear:both;"></div>
                      </div>
					 
					  <div style="float:left;width:60%">
                           
                            
                            <div style="float:left;margin-left:10px;margin-top:0px;">
							    @if(str_contains($clinic->full_name,'Lunetterie GRAND OPTICAL'))
							    <div><b>Claude El-Khoury o.o.d</b></div>
                               @else
								<div><b>{{$clinic->full_name}}</b></div>   
							   @endif	   
							   @if(isset($supplier->num_compte))<div><b>{{__("Account Nb")}} :</b> {{$supplier->num_compte}}</div>@endif
							   <div>{{(isset($clinic))?$clinic->full_address.' , '.$clinic->zip_code:__('Undefined')}}</div>
							   @if(isset($clinic) && isset($clinic->telephone))<div>T.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax))<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email))<div>{{__("Email")}}: {{$clinic->email}}</div>@endif

							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

								<div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>{{__("Supplier")}} :  {{isset($supplier)?$supplier->name:__("Undefined")}}</b></div>
									<div>{{__("Address")}} : {{isset($supplier) && isset($supplier->addresse)?$supplier->addresse:__("Undefined")}}</div>
                                     @if(isset($supplier->tel))<div>Tel. :  {{$supplier->tel}}</div>@endif
									 @if(isset($supplier->fax))<div>Fax. : {{$supplier->fax}}</div>@endif
                                </div>
								
								
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
					  
                    
			<div style="overflow-x:auto;margin-top:1.5em;">
			  <div class="row">
			  <div class="col-md-12">
				<table class="table table-bordered" style="width:100%;">
					<tbody>
                         <tr class="tbl_head">
								<th style="text-align:center;">{{__('Sku')}}</th>
								<th style="text-align:center;">{{__('Name')}}</th>
							    <th style="text-align:center;">{{__('Qty')}}</th>
						        <th style="text-align:center;">{{__('Price')}}</th>
								<th style="text-align:center;">{{__('Discount')}}</th>
								<th style="text-align:center;">{{__('Total')}}</th>
					    	</tr>
  
								   @foreach($result as $results)
								
							
									 <tr>
											    
												<td>
												@php $item = App\Models\TblInventoryItems::find($results->item_code);@endphp 
												@if($Invoice->active=='W' || $item->sku=='' || $item->sku==NULL)
												@else
												{{$item->sku}}
											    @endif 
												</td>
												<td>{{$results->item_name}}</td>
												<td style="text-align:right;">{{$results->qty}}</td>
												<td style="text-align:right;">{{$results->price}}</td>
												<td style="text-align:right;">{{$results->discount}}</td>
												<td style="text-align:right;">{{$results->qty*$results->price-$results->discount}}</td>
												
												</tr>
										

									 @endforeach
						</tbody>
				</table>
				</div>
			</div>
          </div>			
			<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
				  <div class="col-md-12">
					<table class="table table-bordered" style="margin-bottom:2px;width:100%;">
						<tbody>
					      <tr  class="tbl_head">
						    <th style="text-align:center;"><b>{{__('Balance')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Discount')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Sub-total')}}</b></th>
						    <th style="text-align:center;"><b>{{__('QST')}}</b></th>
						    <th style="text-align:center;"><b>{{__('GST')}}</b></th>
							<th style="width:200px;text-align:center;"><b>{{__('Total')}}</b></th>
						  </tr>
					      <tr>
						    <td style="text-align:right;">{{$Invoice->total+$Invoice->discount}}</td>
							<td style="text-align:right;">{{$Invoice->discount}}</td>
							<td style="text-align:right;">{{$Invoice->total}}</td>
							<td style="text-align:right;">{{$Invoice->qst}}</td>
							<td style="text-align:right;">{{$Invoice->gst}}</td>
							<td style="text-align:center;">{{$Invoice->total+$Invoice->qst+$Invoice->gst}}</td>
						  </tr>
						</tbody>
					</table>
				  </div>
       			</div>
             </div>	
                 
					<div style="overflow-x:auto;margin-top:1px;">
					        <div style="float-left;"><div style="clear:both;"></div></div>
							<div style="float-right;">
								<div style="float:right;">
								 <div>
								   @if($pay->count()>0)
								   <table class="table table-bordered" style="margin-bottom:2px;width:100%;">
									 <thead>
									   <tr  class="tbl_head">
									   
										<th  colspan="2" scope="col" style="width:200px;text-align: center;">{{__('Paid amount')}}</th>
                                       
									  </tr>
									  </thead>
									  <tbody>
									  @php $sum_pay=0.00; @endphp	
									  
									   
									   @foreach($pay as $p)
										 <tr>
										 <td>{{$p->pay_name}}
										     @if(isset($p->deposit) && $p->deposit=='Y')
											   <div style="font-size:12px;text-align: left;">{{__('Deposit')}}</div>
										     @endif
											 @if(isset($p->remark) && $p->remark!='')
											   <div style="font-size:12px;text-align: left;">{{__('Remark').' : '.$p->remark}}</div>
										     @endif
										 </td>
										  <td style="text-align: right;">{{$p->pay_amount}}</td>	 
										 </tr>
										 @php  $sum_pay+=floatval($p->pay_amount); @endphp	
										@endforeach
										<tr>
									      <td>{{__('Total')}}</td>
									      <td style="text-align: right;">{{number_format($sum_pay, 2, '.', '')}}</td>
										 
									    </tr>
									    
									</tbody>
									
								</table>
								@endif
								@if($ref->count()>0)
								  <table class="table table-bordered" style="margin-bottom:2px;width:100%;">
									
									  <thead>
									  <tr  class="tbl_head">
									   
										<th  colspan="2" scope="col" style="width:200px;text-align: center;">{{__('Reimburse amount')}}</th>
										
									  </tr>
									  </thead>
									  <tbody>
										
											@php $sum_ref=0.00;@endphp
										    @foreach($ref as $r)
											 <tr>
											 <td>
											 {{$r->pay_name}}
											 @if(isset($p->deposit) && $p->deposit=='Y')
											   <div style="font-size:12px;text-align: left;">{{__('Deposit')}}</div>
										     @endif
											 @if(isset($p->remark) && $p->remark!='')
											   <div style="font-size:12px;text-align: left;">{{__('Remark').' : '.$p->remark}}</div>
										     @endif
											 </td>
											 <td style="text-align: right;">{{$r->pay_amount}}</td>
											 
											 </tr>
											 @php  $sum_ref+=floatval($r->pay_amount); @endphp	
											@endforeach
											 <tr>
											  <td>{{__('Total')}}</td>
											  <td style="text-align: right;">{{number_format($sum_ref, 2, '.', '')}}</td>
											  
											 </tr>
										<!--<td style="text-align: center;">{{($Invoice->invoice_balance!=NULL && $Invoice->invoice_balance!='')?$Invoice->invoice_balance:'0.00'}}</td>-->
									    
									</tbody>
								</table>
								@endif
								<table class="table table-bordered" style="margin-bottom:2px;width:100%;">
									<tbody>
									  <tr  class="tbl_head">
									   
										<th style="width:200px;text-align:center;"><b>{{__('Due Balance')}}</b></th>
										
									  </tr>
									  <tr>
									   
										<td style="text-align:right;">{{$Invoice->inv_balance}}</td>
									  </tr>
									</tbody>
								</table>
								 </div>
								<div style="clear:both;"></div>
								</div>
								<div style="clear:both;"></div>
							</div>
							
							
							
			</div>			 
			           
			             
							
			</div>
				
			<footer id="footer">
				              
						   
						   <!--<div style="text-align: right;font-size:12px;">
							
								 
								   <div>
									 <div style="float:left;width:70%">
									 </div>
									 <div style="float:left;width:30%;border-bottom:2px solid #000;"></div>
									 <div style="clear:both;"></div>	
									</div> 
								 
							  <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('Y-m-d').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div> -->        
			</footer>
		
  </body>
</html>
