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
                         
                            <div style="float:center;">
								<div><b>{{__('Guarantor Statment of Account')}}:</b></div>
                              
								<div><b>{{__('Date/Time')}}:</b> {{Carbon\Carbon::now()->format('d/m/Y').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div>
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
								    <th   scope="col" style="text-align: center;">{{__('#')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Date')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Reference')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Transaction')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Details')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Amount USD')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Payment USD')}}</th>
								</tr>
							@php
							$xx=1;
							$total=0.00;
							$totalp=0.00;
							@endphp
							@foreach($bills as $results)
									
								
										         <tr>
													<td  style="text-align: center; font-size:11px;" >{{$xx}}</td>
													<td  style="text-align: center;font-size:11px;" >{{$results->datein}}</td>
													<td  style="text-align: center;font-size:11px;" >{{$results->bill_num}}</td>
													<td  style="text-align: center;font-size:11px;" > 
													@switch($results->payment_type)
														@case('S')
															{{ __('Schedule') }}
															@break
														@case('P')
															{{ __('Payment') }}
															@break
														@case('D')
															{{ __('Discount') }}
															@break
														@case('R')
															{{ __('Refund') }}
															@break
														@default
															{{ __('Unknown') }}
													@endswitch
													</td>
													<td style="text-align: center;font-size:11px;">{{$results->notes }}</td>
													<td style="text-align: center;">
													@if ($results->payment_type=='S')
													{{isset($results->payment_amount) && $results->payment_amount!=0.00?number_format((float)$results->payment_amount,2,'.',','):0.00}}
													@endif
													</td>
													<td style="text-align: center;">
													@if ($results->payment_type!='S')
													{{isset($results->dpay_amount) && $results->dpay_amount!=0.00?number_format((float)$results->dpay_amount,2,'.',','):0.00}}
													@endif
													</td>
													
													</tr>
											
							@php 
							$xx += 1; 
							if($results->payment_type=='S'){
							$total +=$results->payment_amount;
							}
							if($results->payment_type!='S'){
							$totalp +=$results->dpay_amount;
							}
							@endphp
							@endforeach
							</tbody>
							<tr> 
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"><b>{{__('Total').':'}}</b></td>
								<td style="text-align: center;"><b>{{ number_format((float)$total, 2, '.', ',').' USD' }}</b></td>
								<td style="text-align: center;"><b>{{ number_format((float)$totalp, 2, '.', ',').' USD' }}</b></td>
			               </tr>
							<tr> 
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"></td>
								<td style="text-align: center;"><b>{{__('Balance').':'}}</b></td>
								<td colspan="2" style="text-align: center;"><b>{{ number_format((float)$total-(float)$totalp, 2, '.', ',').' USD' }}</b></td>
			               </tr>									   
						</table>
					</div>
				</div>
              </div>
              <div style="overflow-x:auto;margin-top:15px;">
			  <div class="row">
			      <div class="col-md-12">			  
                 
				</div>
			</div>
		  </div>
		  
		   
			<footer id="footer">
				          <div style="text-align: right;font-size:12px;">
							
								
							  <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('d/m/Y').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div>
				          
						   									
			</footer>
			</body>
</html>
  