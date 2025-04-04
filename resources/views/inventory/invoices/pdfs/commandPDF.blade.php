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
			<title>{{__('Order')}}</title>
			
		</head>
	<body style="font-size:12px;">	
   
            <div style="width:100%;">
	               
					<div>
                       <div  class="top_line">
                           
						   <div class="top_left">
							 <div>
							  @if(isset($logo))
								  <img src="{{config('app.url').'/storage/app/7mJ~33/'.$logo->logo_path}}" style="position:relative;height:70px;"/>
						      @else
							  <b>{{__('Order')}}</b>
						      @endif
							 </div>
                            </div>

						    <div class="top_center" style="font-size:16px;"><b>{{__("Fastmed s.a.r.l")}}</b></div>
						   <div class="top_right">
								@if($Invoice->quote=='Y')
							    <div><b>{{__('Quotation')}}:</b> {{$Invoice->clinic_inv_num}}</div>
								@else
							    <div><b>{{__('Order')}}:</b> {{$Invoice->clinic_inv_num}}</div>								
								@endif	
								<div><b>{{__('Date')}}:</b> {{Carbon\Carbon::parse($Invoice->date_invoice)->format('d/m/Y')}}</div>
                            </div>
                            <div style="clear:both;"></div>
                      </div>
					
					  <div style="float:left;width:60%">
                           
                            
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						      
								<div><b>{{__("Fastmed s.a.r.l")}}</b></div>   
							   @if(isset($supplier) && isset($supplier->num_compte))<div><b>{{__("MOF")}} :</b> {{__("3153813")}}</div>@endif
							   @if(isset($clinic) && isset($clinic->full_address) && $clinic->full_address!='')<div>{{$clinic->full_address}}</div>@endif
							   @if(isset($clinic) && isset($clinic->telephone) && $clinic->telephone!='')<div>T.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax) && $clinic->fax!='')<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email) && $clinic->email!='')<div>{{__("Email")}}: {{$clinic->email}}</div>@endif

							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

							  <div style="float:left;margin-left:10px;margin-top:0px;">
                                  @if(isset($supplier))  
									<div><b>{{__("Supplier")}} :  {{$supplier->name}}</b></div>
									@if(isset($supplier->addresse) && $supplier->addresse!='' )<div>{{__("Address")}} : {{$supplier->addresse}}</div>@endif
                                    @if(isset($supplier->tel) && $supplier->tel!='')<div>Tel. :  {{$supplier->tel}}</div>@endif
									@if(isset($supplier->fax) && $supplier->fax!='')<div>Fax. : {{$supplier->fax}}</div>@endif
									@if(isset($supplier->email) && $supplier->email!='')<div>{{__("Email")}}: {{$supplier->email}}</div>@endif
								  @endif	
                                </div>
								
								
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
					 
			<div style="overflow-x:auto;margin-top:10px;">
			  <div class="row">
			  <div class="col-md-12">
				<table class="table table-bordered" style="width:100%;">
					<tbody>
                         <tr class="tbl_head">
								<th style="text-align:center;">{{__('ID')}}</th>
								<th style="text-align:center;">{{__('Name')}}</th>
							    <th style="text-align:center;">{{__('Qty')}}</th>
						        <th style="text-align:center;">{{__('Price')}}</th>
								<th style="text-align:center;">{{__('Total')}}</th>
					    	</tr>
									@php $stotal=0.00; @endphp
								   @foreach($result as $results)
								
							
									 <tr style="font-size:11px;">
											   <td>
												@php $item = App\Models\TblInventoryItems::find($results->item_code);@endphp 
												@if($Invoice->active=='W' || $item->sku=='' || $item->sku==NULL)
												@else
												{{$item->sku}}
											    @endif
												</td>
												<td> @php 
														$position = strpos($results->item_name, '-('); 
														if ($position !== false) {
															$itemName = substr($results->item_name, 0, $position);
															echo $itemName;
														} else {
															echo $results->item_name;
														}
													@endphp</td>
												<td style="text-align:right;">{{$results->qty}}</td>
												<td style="text-align:right;">{{$results->price}} $</td>
												<td style="text-align:right;">{{$results->total}} $</td>
												
												</tr>
										
										@php $stotal+=$results->total;@endphp
									 @endforeach
						</tbody>
				</table>
				</div>
			</div>
          </div>			
			<div style="overflow-x:auto;margin-top:5px;">
			  <div class="row">
				  <div class="col-md-12">
					<table class="table table-bordered" style="margin-bottom:2px;width:100%;">
						<tbody>
					      <tr  class="tbl_head">
						   
							<th style="width:200px;text-align:center;"><b>{{__('Total USD')}}</b></th>
							
						  </tr>
					      <tr style="font-size:11px;">
						  
							<td style="text-align:right;">Total USD: {{ number_format($stotal, 2, '.', ',') }} $</td>
							
						  </tr>
						</tbody>
					</table>
				  </div>
       			</div>
             </div>
               <div style="overflow-x:auto;margin-top:1px;margin-bottom:10px;">
					        <div style="margin-top:20px;float:left;width:65%;">
							  @if(isset($Invoice->notes) && $Invoice->notes!='')
								<div style="margin-top:2em;">
								  <div class="row">
									<div class="col-md-12">
									  <label for="name"><b>{{__('General Comment')}}</b></label>
									  <textarea class="no-brdr">{{$Invoice->notes}}</textarea>
									</div>  
								  </div> 
								</div>
				             @endif	 
							 <div style="clear:both;"></div>
							</div>
							<div style="float:right;width:31%;">
								
								 <div>
								   
							
								
								
								<div style="clear:both;"></div>
								</div>
								<div style="clear:both;"></div>
							</div>
										            
							
			</div> 			 
              
			</div>
				
			<footer id="footer">
				
			</footer>
		
  </body>
</html>
