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
                      <div   style="border:2px #1bbc9b solid;padding:1px;margin-bottom:1em;">
                            <div style="float:left;">
							 <div>
							  @if(isset($logo))
							  <img src="{{config('app.url').'/storage/app/7mJ~33/'.$logo->logo_path}}" style="width:60px;height:60px;"/>
						      @else
							  <b>{{__('Bill')}}</b>
						      @endif
							 </div>
                            </div>
                            <div style="float:right;">
                               @if(isset($request_nb))
								<div><b>{{__('Date/Time')}}:</b> {{Carbon\Carbon::parse($Bill->bill_datein)->format('d/m/Y H:i')}}</div>
                                <div><b>{{__('Bill Nb.').' : '}}</b> {{$Bill->clinic_bill_num}}</div>
							    <div><b>{{__('Request Nb.').' : '}}</b>{{$request_nb}}</div>
							   @else 
								<div><b>{{__('Date/Time')}}:</b> {{Carbon\Carbon::parse($Bill->bill_datein)->format('d/m/Y H:i')}}</div>
                                <div><b>{{__('Bill Nb.').' : '}}</b> {{$Bill->clinic_bill_num}}</div>
							   @endif
                            </div>
                            <div style="clear:both;"></div>
                      </div>
					  <div style="float:left;width:60%">
                           
                            <br/>
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						       <div><b>{{(isset($clinic))?$clinic->full_name:__('Undefined')}}</b></div>
                               @if(isset($clinic) && isset($clinic->full_address) && $clinic->full_address!='' )<div>{{$clinic->full_address}}</div>@endif
							   @if(isset($clinic) && isset($clinic->telephone) && $clinic->telephone!='' )<div>Tel.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax) && $clinic->fax!='')<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email) && $clinic->email!='')<div>{{__("Email")}}: {{$clinic->email}}</div>@endif
                               @if(isset($pro))
							    @if(isset($pro->middle_name) && $pro->middle_name!='')
								 <div><b>{{('External Physician').': '}} {{$pro->first_name.' '.$pro->middle_name.' '.$pro->last_name}}</b></div>
							    @else	
						         <div><b>{{('External Physician').': '}} {{$pro->first_name.' '.$pro->last_name}}</b></div>
							    @endif
							   @endif
							   @if(isset($ext_lab))
							   <div><b>{{('External Lab').': '}} {{$ext_lab->full_name}}</b></div>
							   @endif
							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">

                                <br/>
                                <div style="float:left;margin-left:10px;margin-top:0px;">
                                    @if(isset($patient))
									 @if(isset($patient->middle_name) && $patient->middle_name!='')
								        <div><b>Patient :  </b>{{$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name}}</div>
                                     @else
										<div><b>Patient :  </b>{{$patient->first_name.' '.$patient->last_name}}</div> 
									 @endif	 
									<div><b>File Nb. :  </b>{{$patient->file_nb}}</div>
									@if(isset($patient->addresse) && $patient->addresse!='')<div>{{__("Address")}} : {{$patient->addresse}}</div>@endif
                                    @if(isset($patient->first_phone) && $patient->first_phone!='')<div>{{__("Landline Phone")}} :  {{$patient->first_phone}}</div>@endif
									@if(isset($patient->cell_phone) && $patient->cell_phone!='')<div>{{__("Cell Phone")}} :  {{$patient->cell_phone}}</div>@endif
                                    @if(isset($patient->email) && $patient->email!='')<div>{{__("Email")}} :  {{$patient->email}}</div>@endif
									@endif
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
                    </div>	
					
			<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
			      <div class="col-md-12">
					<table class="table-bordered" style="width:100%;">
						   <tbody>
								<tr style="background-color:#1bbc9b;color:#fff;">
								    <th   scope="col" style="text-align: center;">{{__('CNSS')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Item Name')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Nb of L')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Price USD')}}</th>
									<th   scope="col" style="text-align: center;">{{__('Price LBP')}}</th>
									
								</tr>
	  
							@foreach($result as $results)
									
								
										         <tr>
													<td  style="text-align: center;" >{{$results->cnss}}</td>
													<td  style="text-align: center;" >{{$results->bill_name}}</td>
													<td style="text-align: center;">{{$results->bill_quantity}}</td>
													<td style="text-align: center;">{{isset($results->bill_price) && $results->bill_price!=0.00?number_format((float)$results->bill_price,2,'.',''):0}}</td>
    												<td style="text-align: center;">{{isset($results->lbill_price) && $results->lbill_price!=0.00?number_format((float)$results->lbill_price,0,'.',','):0}}</td>
													
													</tr>
											

							@endforeach
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
								<th  scope="col" style="text-align: center;">{{__('Total')}}</th>
						        <th  scope="col" style="text-align: center;">{{__('Remianing')}}</th>
						        <th  scope="col" style="text-align: center;">{{__('Total Payment')}}</th>
								<th  scope="col" style="text-align: center;">{{__('Total Discount')}}</th>
								<th  scope="col" style="text-align: center;">{{__('Total Donate')}}</th>
					    	</tr>
					   <tbody>
                           <tr> 
								<td style="text-align: center;">LBP</td>
								<td style="text-align: center;">{{number_format((float)$Bill->lbill_total,0,'.',',')}}</td>
								<td style="text-align: center;">{{number_format((float)$Bill->bill_balance,0,'.',',')}}</td>
								<td style="text-align: center;">{{number_format((float)$pay,0,'.',',')}}</td>
								<td style="text-align: center;">{{isset($Bill->bill_discount) && $Bill->bill_discount!=0.00?number_format((float)$Bill->bill_discount,0,'.',','):0}}</td>
								<td style="text-align: center;">{{$ref}}</td>
			               </tr>
						   <tr> 
								<td style="text-align: center;">USD</td>
								<td style="text-align: center;">{{$Bill->bill_total}}</td>
								<td style="text-align: center;">{{$Bill->bill_balance_us}}</td>
								<td style="text-align: center;">{{$payd}}</td>
								<td style="text-align: center;">{{isset($Bill->bill_discount_us) && $Bill->bill_discount_us!=0.00?$Bill->bill_discount_us:0}}</td>
								<td style="text-align: center;">{{$refd}}</td>
			               </tr>
						  </tbody>
					 </table> 
				</div>
			</div>
		  </div>
		  
		    @if(isset($Bill) && isset($Bill->notes) && $Bill->notes!='')
			<div style="overflow-x:auto;margin-top:15px;">
			  <div class="row">
			      <div class="col-md-12">
				     <label for="name">{{__('Remark')}}</label>
					 <textarea class="form-control" name="comments">{{$Bill->notes}}</textarea>
				  </div>
			  </div>
			</div> 
            @endif			
			<footer id="footer">
				          <div style="text-align: right;font-size:12px;">
							
								 @if(isset($signature_path)) 
								  <img   style="border-bottom:2px solid #000;" src="data:image/png;base64,{{base64_encode(file_get_contents($signature_path))}}"/>
								 @else
								   <div>
									 <div style="float:left;width:70%"></div>
									 <div style="float:left;width:30%;border-bottom:2px solid #000;"></div>
									 <div style="clear:both;"></div>	
									</div> 
								 @endif 
							   @if(isset($pro))
							     @if(isset($pro->middle_name) && $pro->middle_name!='')
							      <div style="text-align: right;">{{$pro->first_name.' '.$pro->middle_name.' '.$pro->last_name}} {{isset($pro->license_num) && $pro->license_num!=''?' ( #'.$pro->license_num.' )':''}}</div>
							     @else
								  <div style="text-align: right;">{{$pro->first_name.' '.$pro->last_name}} {{isset($pro->license_num) && $pro->license_num!=''?' ( #'.$pro->license_num.' )':''}}</div>
								 @endif	 
							   @endif
							  <div  style="text-align: right;">{{__('Date').' : '.Carbon\Carbon::now()->format('d/m/Y').' '.__('at').' '.Carbon\Carbon::now()->format('H:i')}}</div> 	  
							
							</div>
				          
						   									
			</footer>
			</body>
</html>
  