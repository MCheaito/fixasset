<!--
   DEV APP
   Created date : 26-7-2023
-->
<!DOCTYPE html>
<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Inventory Audit')}}</title>
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
                      <div style="background-color:#1bbc9b;color:#fff;padding:1px;margin-bottom:1em;">
                            <div style="float:left;">
							    <div><b>{{__('Inventory Audit').' '.'#'.$mat->id}}</b></div>
                            </div>
                            <div style="float:right;">
                                <div><b>{{__('Date')}}:</b> {{Carbon\Carbon::parse($mat->date_invoice)->format('Y-m-d')}}</div>
                            </div>
                            <div style="clear:both;"></div>
                      </div>
                      <div>
                          
						  <div style="float:left;width:60%">
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						      
								<div><b>{{$clinic->full_name}}</b></div>   
							   <div>{{(isset($clinic))?$clinic->full_address.' , '.$clinic->zip_code:__('Undefined')}}</div>
							   @if(isset($clinic) && isset($clinic->telephone))<div>T.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax))<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email))<div>{{__("Email")}}: {{$clinic->email}}</div>@endif

							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">
                               <div style="float:left;margin-left:10px;margin-top:0px;">
                                    @if($mat->approuve=='Y')
									   <div><b>{{__("Approuved")}} :</b>{{Carbon\Carbon::parse($mat->date_approve)->format('Y-m-d')}}</div>
									@endif	
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                         
					  </div>	  
					  
                    		
                </div>	
			    
				<div style="margin-top:10;margin-bottom:2px;">
 				        <div style="width:100%">
				            		 <div style="overflow-x:auto;margin-top:1px;">						
											<div class="row">
												<div class="col-md-12">
												   <table class="table table-bordered table-sm" style="width:100%;">
														<thead class="text-center">
															<tr>
															 <th scope="col" colspan='4' class="text-center border">{{__("Totals")}}</th>
															</tr>
															<tr>
																<th scope="col" class="text-center border">{{__("Total Stock Qty")}}</th>
																<th scope="col" class="text-center border">{{__('Total Phys Qty')}}</th>
																<th scope="col" class="text-center border">{{__('Total Diff Qty')}}</th>
																<th scope="col" class="text-center border">{{__('Total Amount')}}</th>
																
															</tr>
														</thead>
															<tbody class="text-center">
																<tr>
																	<td  class="border">{{$tsq}}</td>
																	<td  class="border">{{$tpq}}</td>
																	<td  class="border">{{$tdq}}</td>
																	<td  class="border">{{$tamount}}</td>
																	
																</tr>
																
															</tbody>
														</table>
												</div>
											</div>
										</div>
							
									<div style="overflow-x:auto;margin-top:1px;">						
										<div class="row">
											<div class="col-md-12"> 
												<table  class="table table-bordered table-sm" style="width:100%;">
													<thead class="text-center">
														<tr>
															<th scope="col" class="text-center border">{{__("#")}}</th>
															<th scope="col" class="text-center border">{{__('Barcode')}}</th>
															<th scope="col" class="text-center border">{{__('Item name')}}</th>
															<th scope="col" class="text-center border">{{__('Qty Stock')}}</th>
															<th scope="col" class="text-center border">{{__('Phys Qty')}}</th>
															<th scope="col" class="text-center border">{{__('Diff Qty')}}</th>
															
													</tr>
													</thead>
													<tbody class="text-center">
														@php $cpt=0 @endphp
														@foreach($mat_det as $m)
														<tr>
															<th class="border">{{++$cpt}}</th>
															<td class="border">{{$m->item_code}}</td>
															<td class="border">{{$m->item_name}}</td>
															<td class="border">{{$m->qty}}</td>
															<td class="border">{{$m->rqty}}</td>
															<td class="border">{{$m->dqty}}</td>
															
														</tr>
														@endforeach
														
													</tbody>
												</table>
											</div>
										</div>
									</div>	
								   
				                   		
							
						</div>		
						
				</div>
                 
               
			</div>		
			<footer id="footer">
				            
							<!--<div style="text-align: right;font-size:12px;">
							
							   <div>
						         <div style="float:left;width:70%"></div>
						         <div style="float:left;width:30%;border-bottom:2px solid #000;"></div>
								 <div style="clear:both;"></div>	
								</div> 
							
							   <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('Y-m-d').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							   
							</div>-->
				          
									
			</footer>
	    </body>
</html>
