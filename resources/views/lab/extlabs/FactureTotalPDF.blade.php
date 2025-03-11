<!DOCTYPE html>
<!--
 DEV APP
 Created date : 5-11-2022
-->
<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
			     font-size: 13px ! important;
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
	<body style="font-size:13px;">	
   
            <div style="width:100%;">
	               
					<div>
                   
                            <div style="float:right;">
                              <div><b>{{__('Invoice Nb')}}:</b> {{$reference}}</div>
							 <div><b>{{__('Invoice Date')}}:</b> {{$datein}}</div>
							 <div><b>{{__('Invoice Due Date')}}:</b> {{$datedue}}</div>
							 <div><b>{{__('Currency')}}:</b> {{__('USD')}}</div>

                            </div>
                            <div style="clear:both;"></div>
					  <div style="float:left;width:60%">
                           
                            <br/>
                            <div style="float:left;margin-left:10px;margin-top:2px;">
						       <div><b>{{(isset($clinic))?$clinic->full_name:__('Undefined')}}</b></div>
                               @if(isset($clinic) && isset($clinic->full_address) && $clinic->full_address!='' )<div>{{$clinic->full_address}}</div>@endif
							   @if(isset($clinic) && isset($clinic->telephone) && $clinic->telephone!='' )<div>Tel.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax) && $clinic->fax!='')<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email) && $clinic->email!='')<div>{{__("Email")}}: {{$clinic->email}}</div>@endif
                               
							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

                                <br/>
                                <div style="float:left;margin-left:10px;margin-top:0px;">
                                <div><b>{{(isset($ext_lab))?$ext_lab->full_name:__('Undefined')}}</b></div>
                               @if(isset($ext_lab) && isset($ext_lab->full_address) && $ext_lab->full_address!='' )<div>{{$ext_lab->full_address}}</div>@endif
							   @if(isset($ext_lab) && isset($ext_lab->telephone) && $ext_lab->telephone!='' )<div>Tel.:  {{$ext_lab->telephone}}</div>@endif
                               @if(isset($ext_lab) && isset($ext_lab->fax) && $ext_lab->fax!='')<div>Fax: {{$ext_lab->fax}}</div>@endif
							   @if(isset($ext_lab) && isset($ext_lab->email) && $ext_lab->email!='')<div>{{__("Email")}}: {{$ext_lab->email}}</div>@endif
                               
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
                    </div>	
					
			<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
			  <div class="col-md-12"><b>
			  {{__('Between').' '.$filter_fromdatefacture.' '.__('And').' '.$filter_todatefacture }}
			  </b></div>
			      <div class="col-md-12">
					<table class="table-bordered" style="width:100%;">
						   <tbody>
								<tr style="background-color:#1bbc9b;color:#fff;">
								    <th   scope="col" style="text-align: center;">{{__('Month')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Description')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Amount')}}</th>
								
									
								</tr>
							@php
							$total=0.00;
							@endphp
							@foreach($bills as $results)
							@php
							$total +=$results->bill_price;
							@endphp
							@endforeach
							@php 
							$formattedDate = substr($filter_fromdatefacture, 0, 7);  
							@endphp	
										         <tr>
													<td  style="text-align: center;" >{{$formattedDate}}</td>
													<td style="text-align: center;">{{ __('Lab Test').' '.$formattedDate}}</td>
													<td style="text-align: center;">{{$total}}</td>
																										
													</tr>
											
							
							</tbody>
						</table>
					</div>
				</div>
              </div>
              <div style="overflow-x:auto;margin-top:15px;">
			  <div class="row">
			      <div class="col-md-12">			  
                    <table class="table-bordered" style="width:100%;">
					   <tr style="background-color:#1bbc9b;color:#fff;">
								<th></th>
								<th  scope="col" style="text-align: center;"></th>
						        <th  scope="col" style="text-align: center;"></th>
						        <th  scope="col" style="text-align: center;"></th>
								<th  scope="col" style="text-align: center;"></th>
								<th  scope="col" style="text-align: center;"></th>
					    	</tr>
					   <tbody>
                          
						   <tr> 
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;">{{__('Total')}}</td>
								<td style="text-align: center;">{{ number_format((float)$total, 2, '.', ',').' USD' }}</td>
			               </tr>
						    <tr> 
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;">{{__('Discount')}}</td>
								<td style="text-align: center;">{{ number_format((float)$discount, 2, '.', ',').' USD' }}</td>
			               </tr>
						    <tr> 
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;">{{__('Net Amount')}}</td>
								<td style="text-align: center;">{{ number_format((float)$total-$discount, 2, '.', ',').' USD' }}</td>
			               </tr>
						  </tbody>
					 </table> 
				</div>
			</div>
		  </div>
		  
		   
			<footer id="footer">
				          <div style="text-align: right;font-size:12px;">
							
								
							  <div  style="text-align: right;">{{__('Print Date').' : '.Carbon\Carbon::now()->format('d/m/Y').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div>
				          
						   									
			</footer>
			</body>
</html>
  