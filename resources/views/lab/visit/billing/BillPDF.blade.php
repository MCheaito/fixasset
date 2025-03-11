<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Billing')}}</title>
			<style>
			 @page { margin: 180px 20px 60px 20px; }
             header { position: fixed; top: -160px; left: 0px; right: 0px; height: 50px; }
             footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 90px; }
			 .page:after { content: counter(page, decimal); }
             body {
               font-family: 'DejaVu Serif', serif;
               line-height: 1;
			   font-size: 11px !important;
			  }
			 .center {
                text-align: center;
              }
             .center img {
                display: block;
              }
			
			
			table {
                  border-spacing: 0px;
                  table-layout: auto;
                  margin-left: auto;
                  margin-right: auto;
                  width: 100%;
			      text-align: left;
				  font-size: 10px !important;
			     
			   }
			.table.table-bordered tr {
				margin-bottom: 10px;
				padding: 0;
			}
			
			
			.table.table-bordered th {
				padding: 0px;
				
			}

			.table.table-bordered td
			{
			  padding: 0px 0px 6px 0px;
			}
         </style>
			
	</head>
	<body style="font-size:12px;">	
               <header>
			     <div  style="border:0 px;padding:1px;">
                            @if(isset($logo))
							   <div style="float:left;width: 12%;height:70px;padding-bottom:5px;">
							   <img src="{{config('app.url').'/storage/app/7mJ~33/'.$logo->logo_path}}" style="width:100%;height:100%;"/>
							  </div>
							  <div class="text-center" style="width:88%;padding-top:10px;padding-bottom:5px;">
                                  <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
                              </div>
							 <div style="clear:both;"></div>
						    @else
								<div class="text-center" style="padding-bottom:5px;">
                                  <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
								  <!--<div style="position:absolute;left:30%;font-size:16px;font-family:DejaVu Sans, sans-serif;direction: rtl;">مركز الهادي للخصوبة والتشخيص</div>-->
                                 </div>
							@endif	
							<div style="float:left;width:45%;border-top:1px solid;border-left:1px solid;border-right:3px solid;border-bottom:3px solid;padding-left:5px;margin-left:5px;">
								<div>
								  <b>{{__('Name').' : '}}</b>
								  @if($patient->middle_name !='' && isset($patient->middle_name))
									{{$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name}}
								  @else
									{{$patient->first_name.' '.$patient->last_name}}	  
								  @endif	  
							    </div>
								<div>
								  <b>{{__('Gender').' : '}}</b> {{($patient->sex=='F')?'Female':( ($patient->sex=='M')?'Male':'Undefined')}}
								</div>
								 @if(isset($patient->birthdate) && $patient->birthdate!='')
									 <div><b>{{__('Age/DOB').' : '}}</b> {{UserHelper::getPatAge($patient->birthdate)}}
								     <b style="margin-left: 20px;"></b> {{Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y')}}</div>
								@endif
								<div><b>{{__('File Nb').' : '}}</b>{{$patient->file_nb}}</div>
							    <div><b>{{__('Request Nb').' : '}}</b>{{$order->request_nb}}</div>
	                       </div>
                           <div style="float:left;width:5%;"></div>
                           <div style="float:left;width:45%;margin-left:5px;border-top:1px solid;border-left:1px solid;border-right:3px solid;border-bottom:3px solid;padding-left:5px;">           
                                <div>
									  <b>{{__('Ref By Dr.').' : '}}</b>
									  @if(isset($doctor)) 
										  @if($doctor->middle_name !='' && isset($doctor->middle_name))
											{{$doctor->first_name.' '.$doctor->middle_name.' '.$doctor->last_name}}
										  @else
											{{$doctor->first_name.' '.$doctor->last_name}}	  
										  @endif
									  @else
									     {{__('-')}}
									  @endif	  
								  </div>
                                 @if(isset($ext_lab) && isset($ext_lab->full_name) && $ext_lab->full_name!='')
								  <div><b>{{__('Guarantor').' : '}}</b>{{$ext_lab->full_name}}</div>	
							     @else	 
								  <div><b>{{__('Guarantor').' : '}}</b>{{__('Private')}}</div>	
							     @endif
								<div><b>{{__('Bill Nb').' : '}}</b>{{$Bill->clinic_bill_num}}</div>
								<div><b>{{__('Bill Date').' : '}}</b>{{Carbon\Carbon::parse($Bill->bill_datein)->format('d/m/Y').' at '.Carbon\Carbon::parse($Bill->bill_datein)->format('H:i')}}</div>								
								<div><b>{{__('Reg. Date').' : '}}</b>{{Carbon\Carbon::parse($order->order_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->order_datetime)->format('H:i')}}</div>
							</div>
                            <div style="clear:both;"></div>
                      </div> 
			   </header>
			   <footer>
			     @include('lab.visit.footer')
			   </footer>
			   <main>
			   <div  style="width:100%;">
	       		   	   <div style="margin-top:10px;margin-bottom:10px;">
							  <div class="row">
								  <div class="col-md-12">
									<table class="table table-bordered" style="width:100%;">
										   <tbody>
												<tr>
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
																	<td style="text-align: center;">{{$results->bill_price}}</td>
																	<td style="text-align: center;">{{number_format($results->lbill_price,2,'.',',')}}</td>
																	
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
											<tr>
												<th  scope="col" ></th>
												<th  scope="col" style="text-align: center;">{{__('Total')}}</th>
												<th  scope="col" style="text-align: center;">{{__('Remaining')}}</th>
												<th  scope="col" style="text-align: center;">{{__('Total Payment')}}</th>
												<th  scope="col" style="text-align: center;">{{__('Total Discount')}}</th>
												<th  scope="col" style="text-align: center;">{{__('Total Donate')}}</th>
											</tr>					
											<tr> 
												<td style="text-align: center;">LBP</td>
												<td style="text-align: center;">{{number_format($Bill->lbill_total,2,'.',',')}}</td>
												<td style="text-align: center;">{{$balancel}}</td>
												<td style="text-align: center;">{{$sumpayl}}</td>
												<td style="text-align: center;">{{$tdiscountl}}</td>
												<td style="text-align: center;">{{$sumrefl}}</td>
										   </tr>
										   <tr> 
												<td style="text-align: center;">USD</td>
												<td style="text-align: center;">{{$Bill->bill_total}}</td>
												<td style="text-align: center;">{{$balanced}}</td>
												<td style="text-align: center;">{{$sumpayd}}</td>
												<td style="text-align: center;">{{$tdiscountd}}</td>
												<td style="text-align: center;">{{$sumrefd}}</td>
										   </tr>
										  </tbody>
									 </table> 
								</div>
							</div>
						  </div>
				   <!--@if(isset($general_sig))
				  <div style="float:left;"></div>
				  <div style="float:right;margin-top:5px;margin-bottom:5px;">
					<img src="{{config('app.url').'/storage/app/7mJ~33/'.$general_sig->path}}" style="width:120px;height:60px;" alt="SIgnature">				  
				  </div>
				  <div style="clear: both;"></div>
	              @endif-->
			       
			    </div>
			</main>
		    
	</body>
</html>
