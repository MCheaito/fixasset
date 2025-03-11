<!--
 DEV APP
 Created date : 14-1-2023
-->
<!DOCTYPE html>
<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Bill')}}</title>
			<style>
			input[type=checkbox]
				{
				  /* Double-sized Checkboxes */
				  -ms-transform: scale(1.5); /* IE */
				  -moz-transform: scale(1.5); /* FF */
				  -webkit-transform: scale(1.5); /* Safari and Chrome */
				  -o-transform: scale(1.5); /* Opera */
				  transform: scale(1.5);
				  padding : 2px;
				}
			table {
                  border-spacing: 0px;
                  table-layout: auto;
                  margin-left: auto;
                  margin-right: auto;
                  width: 100%;
			     text-align: left;
			     font-size: 14px ! important;
			   }
			table th,table td{
				word-break: break-all;
				}
            
			#footer {
				padding-top: 10px;
				padding-bottom: 0px;
				position:fixed;
				bottom:0;
				width:100%;
                 }      						
			
            </style>
		</head>
	<body style="font-size:14px;">	
   
            <div style="width:100%;">
	               
					<div>
                      <div   style="border:2px #1bbc9b solid;padding:1px;margin-bottom:1em;">
                            <div style="float:left;">
							 <div>
							  @if(isset($logo))
							  <img src="{{ $logo->path }}" style="position:relative;border:1px solid;border-radius:50%;"/>
						      @else
							  <b>{{__('Invoice')}}</b>
						      @endif
							 </div>
                            </div>
                            <div style="float:right;">
							    <div><b>{{__('Invoice Nb')}}:</b> {{$Invoice->clinic_inv_num}}</div>
								<div><b>{{__('Date')}}:</b> {{Carbon\Carbon::parse($Invoice->date_invoice)->format('Y-m-d H:i')}}</div>
								
                            </div>
                            <div style="clear:both;"></div>
                      </div>
					  <div style="float:left;width:60%">
                           
                            
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						       <div><b>{{(isset($clinic))?$clinic->full_name:__('Undefined')}}</b></div>
                               <!--<div><b>{{(isset($pro))?$pro->first_name.' '.$pro->last_name.' , '.'#'.$pro->license_num:__('Undefined')}}</b></div>-->
                               <div>{{(isset($clinic))?$clinic->full_address.' , '.$clinic->zip_code:__('Undefined')}}</div>
							   <div>T.:  {{(isset($clinic) && isset($clinic->telephone))?$clinic->telephone:__('Undefined')}}</div>
                               <div>Fax: {{(isset($clinic) && isset($clinic->fax))?$clinic->fax:__('Undefined')}}</div>
							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

                                
                                @switch($Invoice->type)
								@case(2) 
								<div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>  {{(isset($patient))?'Patient : '.$patient->first_name.' '.$patient->last_name:__('')}}</b></div>
                                    <div>{{(isset($patient))?$patient->city:__('')}}</div>
                                    <div>  {{(isset($patient))?'T.:'.$patient->first_phone:__('')}}</div>
                                </div>
								@break
								
								@case(3) 
								<div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>  {{(isset($patient))?'Patient : '.$patient->first_name.' '.$patient->last_name:__('')}}</b></div>
                                    <div>{{(isset($patient))?$patient->city:__('')}}</div>
                                    <div>  {{(isset($patient))?'T. : '.$patient->first_phone:__('')}}</div>
                                </div>
								@break
								@case(99) 
								<div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>  {{(isset($patient))?'Patient : '.$patient->first_name.' '.$patient->last_name:__('')}}</b></div>
                                    <div>{{(isset($patient))?$patient->city:__('')}}</div>
                                    <div>  {{(isset($patient))?'T.:'.$patient->first_phone:__('')}}</div>
                                </div>
								@break
								@default
								<div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>  {{(isset($supplier))?__('Supplier').' : '.$supplier->name:__('')}}</b></div>
                                    <div>{{(isset($supplier))?$supplier->ville:__('')}}</div>
                                    <div>  {{(isset($supplier))?'T. : '.$supplier->tel:__('')}}</div>
                                </div>
								
								@endswitch
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
					  @if(isset($serial) && ($Invoice->type=='2' || $Invoice->type=='99'))
						<div> 
						 <div style="float-left;">
							 <div style="float:left;width:60%;margin-left:10px;margin-top:0px;">
							   <div><b>{{__('QST Nb')}}:</b> {{$serial->qst_num}}</div>
							   <div style="clear:both;"></div>
							 </div>
						 </div>
						 <div style="float-left;">
						   <div style="float:left;width:40%;margin-top:0px;">
						     <div><b>{{__('GST Nb')}}:</b> {{$serial->gst_num}}</div>
						     <div style="clear:both;"></div>
						   </div>
						 </div>
						 <div style="clear:both;"></div>
						</div> 
					@endif 
                    
                    
			<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
			  <div class="col-md-12">
				<table class="table table-bordered" style="width:100%;">
					<tbody>
                         <tr style="background-color:#1bbc9b !important;color:#fff;">
								<th style="text-align:center;">{{__('Descrip')}}</th>
							    <th style="text-align:center;">{{__('Quantity')}}</th>
						        <th style="text-align:center;">{{__('Price')}}</th>
								<th style="text-align:center;">{{__('Discount')}}</th>
								<th style="text-align:center;">{{__('Total')}}</th>
					    	</tr>
  
								   @foreach($result as $results)
								
							
									 <tr>
												<td style="text-align:center;">{{$results->item_name}}</td>
												<td style="text-align:center;">{{$results->qty}}</td>
												<td style="text-align:center;">{{$results->price}}</td>
												<td style="text-align:center;">{{$results->discount}}</td>
												<td style="text-align:center;">{{$results->qty*$results->price-$results->discount}}</td>
												
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
					<table class="table table-bordered" style="width:100%;">
						<tbody>
					      <tr  style="background-color:#1bbc9b !important;color:#fff;">
						    <th style="text-align:center;"><b>{{__('Total')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Discount')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Sub-total')}}</b></th>
						    <th style="text-align:center;"><b>{{__('QST')}}</b></th>
						    <th style="text-align:center;"><b>{{__('GST')}}</b></th>
							<th style="text-align:center;"><b>{{__('Total')}}</b></th>
							
						  </tr>
					      <tr>
						    <td style="text-align:center;">{{$Invoice->total}}</td>
							<td style="text-align:center;">{{$Invoice->discount}}</td>
							<td style="text-align:center;">{{$Invoice->total-$Invoice->discount}}</td>
							<td style="text-align:center;">{{$Invoice->qst}}</td>
							<td style="text-align:center;">{{$Invoice->gst}}</td>
							<td style="text-align:center;">{{$Invoice->total-$Invoice->discount+$Invoice->qst+$Invoice->gst}}</td>
						  </tr>
						</tbody>
					</table>
				  </div>
       			</div>
             </div>	
                @if($Invoice->type=='1' || $Invoice->type=='2')    
					<div>
					        
							<div style="float-left;">
								<div style="float:left;width:50%;margin-top:1em;">
								 <div>
								   <table class="table table-bordered" style="width:100%;">
									 <thead>
									  <tr  style="background-color:#1bbc9b !important;color:#fff;">
									   
										<th  colspan="2" scope="col" style="text-align: center;">{{__('Paid amount')}}</th>

									  </tr>
									  </thead>
									  <tbody>
									  @if($pay->count()>0)
										@foreach($pay as $p)
										 <tr>
										 <td style="text-align: center;">{{$p->pay_name}}</td>
										 <td style="text-align: center;">{{$p->pay_amount}}</td>
										 </tr>
										
										@endforeach
									  @else
										 <tr>
										 <td colspan="2" style="text-align: center;">0.00</td>
										 </tr>
									  @endif	  
									  
									</tbody>
									
								</table>
								 </div>
								<div style="clear:both;"></div>
								</div>
							</div>
							
							
							<div style="float-left;">
								<div style="float:left;width:50%;margin-left:10px;margin-top:1em;">
								 <div>
								  <table class="table table-bordered" style="width:100%;">
									
									  <thead>
									  <tr  style="background-color:#1bbc9b !important;color:#fff;">
									   
										<th  colspan="2" scope="col" style="text-align: center;">{{__('Reimburse amount')}}</th>
										<!--<th  scope="col" style="text-align: center;">{{__('Remaining amount')}}</th>-->
									  </tr>
									  </thead>
									  <tbody>
										@if($ref->count()>0)
											@foreach($ref as $r)
											 <tr>
											 <td style="text-align: center;">{{$r->pay_name}}</td>
											 <td style="text-align: center;">{{$r->pay_amount}}</td>
											 </tr>
											@endforeach
										<!--<td style="text-align: center;">{{($Invoice->invoice_balance!=NULL && $Invoice->invoice_balance!='')?$Invoice->invoice_balance:'0.00'}}</td>-->
									    @else
											<tr>										 
											 <td colspan="2"  style="text-align: center;">0.00</td>
											 </tr>
									    @endif	
									</tbody>
								</table>
								 </div>
								<div style="clear:both;"></div>
								</div>
							</div>
							
							<div style="clear:both;"></div>
					     </div>			 
			           @endif
			             
							
			</div>
				
			<footer id="footer">
				              
						   @if($Invoice->type=='2' && isset($serial) && $serial->inv_note != NULL && $serial->inv_note  !='')
						         <div style="margin-bottom:10em;">
    								 <textarea class="form-control">{{$serial->inv_note}}</textarea>
						          </div>
						   @endif
						  
						   <div style="text-align: right;font-size:12px;">
							
								 
								   <div>
									 <div style="float:left;width:70%">
									 </div>
									 <div style="float:left;width:30%;border-bottom:2px solid #000;"></div>
									 <div style="clear:both;"></div>	
									</div> 
								 
							  <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('Y-m-d').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div>         
			</footer>
		
  </body>
</html>
