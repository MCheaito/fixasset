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
			<title>{{__('Stripe Payment')}}</title>
			
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
							  <b>{{$clinic->full_name}}</b>
						      @endif
							 </div>
                            </div>
					        <div class="top_center"><b>{{__('Payment Receipt')}}</b></div>
						   <div class="top_right">
							   <div><b>{{__('Receipt')}}:</b> {{'#'.$Stripe->id}}</div>
							   <div><b>{{__('Date')}}:</b> {{Carbon\Carbon::parse($Stripe->pay_date)->format('Y-m-d')}}</div>
							   @if(isset($bill) && isset($bill->clinic_bill_num))<div><b>{{__('Bill')}}:</b> {{$bill->clinic_bill_num}}</div>@endif

							</div>
                            <div style="clear:both;"></div>
                      </div>
					  
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
                                    @if(isset($patient))
									<div><b>Patient :  {{$patient->first_name.' '.$patient->last_name.' ( '.$patient->id.' )'}}</b></div>
                                    <div>{{__("Address")}} : {{$patient->addresse.' '.$patient->city.' '.$patient->codepostale}}</div>
                                    @if(isset($patient->first_phone))<div>{{__("Landline")}} :  {{$patient->first_phone}}</div>@endif
									@if(isset($patient->cell_phone))<div>{{__("Cell")}} :  {{$patient->cell_phone}}</div>@endif
                                    @endif
								</div>
								
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                      </div>
					  
                    
                    
			<div style="overflow-x:auto;margin-top:1em;">
			  <div class="row">
			  <div class="col-md-12">
				<table class="table table-bordered" style="width:100%;">
					<tbody>
                         <tr class="tbl_head">
								<th style="text-align:center;">{{__('Exam')}}</th>
								<th style="text-align:center;">{{__('Price')}}</th>
							    <th style="text-align:center;">{{__('Currency')}}</th>
						        <th style="text-align:center;">{{__('Date')}}</th>
							
					    	</tr>
  
								  
									 <tr>
							
												<td>
												@if(app()->getLocale() == 'fr')
													{{ $result->am_code }} - {{ $result->name_fr }}
												@else
													{{ $result->am_code }} - {{ $result->name_eng }}
												@endif
												
												</td>
											
												<td style="text-align:right;">{{$result->price}}</td>
												<td style="text-align:right;">CAD</td>
												<td style="text-align:right;">{{Carbon\Carbon::parse($result->pay_date)->format('Y-m-d H:i')}}</td>
												
												</tr>
										

									 
						</tbody>
				</table>
				</div>
			</div>
          </div>			
			
             
				
			<footer id="footer">
				              
						 
						 
			</footer>
		
  </body>
</html>
