<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Culture')}}</title>
			<style>
			 @page { margin: 130px 25px 60px 25px; }
             header { position: fixed; top: -110px; left: 0px; right: 0px; height: 50px; }
             footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 50px; }
			 .page:after { content: counter(page, decimal); }
             body {
               font-family: 'DejaVu Sans';
               line-height: 1;
			  }
			 .center {
                text-align: center;
              }
             .center img {
                display: block;
              }
			
			
			.table.table-borderless {
				border-collapse: collapse;
				border-spacing: 0;
				table-layout: auto;
				width: 100%;
				max-width: 100%;
				text-align: left;
				font-size: 12px !important;
				
			}
	
			.table.table-borderless tr {
				margin-bottom: 10px;
				padding: 0;
			}
			
			
			.table.table-borderless th {
				padding: 0px;
			}

			.table.table-borderless td
			{
			  padding: 0px 0px 6px 0px;
			}
         </style>
			
	</head>
	<body style="font-size:12px;">	
               <header>
			     <div  style="border:2px solid padding:1px;">
                            <div class="text-center" style="padding-bottom:10px;">
                                 <div style="font-size:16px;">AL HADI Fertility & Diagnostic Center</div>
                            </div>
							<div style="float:left;margin-left:5px;">
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
									 <div><b>{{__('Age/DOB').' : '}}</b> {{Carbon\Carbon::parse($patient->birthdate)->age.' '.__('year(s)')}}
								     <b style="margin-left: 20px;"></b> {{Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y')}}</div>
								@endif
								@if(isset($doctor)) 
								  <div>
									  <b>{{__('Ref By Dr.').' : '}}</b>
									  @if($doctor->middle_name !='' && isset($doctor->middle_name))
										{{$doctor->first_name.' '.$doctor->middle_name.' '.$doctor->last_name}}
									  @else
										{{$doctor->first_name.' '.$doctor->last_name}}	  
									  @endif
								  </div>
								 @endif
                                								 
							     @if(isset($first_ins) || isset($second_ins))
								   @if(isset($first_ins)) <div><b>{{__('Guarantor').' : '}}</b>{{$first_ins->full_name}}</div>
							       @else	
							         @if(isset($second_ins)) <div><b>{{__('Guarantor').' : '}}</b>{{$second_ins->full_name}}</div>@endif	
							       @endif 
								@else
								  <div><b>{{__('Guarantor').' : '.__('Private')}}</b></div>	
							    @endif	
                            </div>
                            <div style="float:right;margin-right:5px;">
                                <div><b>{{__('File Nb').' : '}}</b>{{$patient->id}}</div>
								<div><b>{{__('Request Nb').' : '}}</b>{{$order->id}}</div>
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
					<div style="margin-top:20px;margin-bottom:10px;">
							<div style="width:100%">
							  <div class="row">
									   <div class="col-md-12">
									     <p><b>{{__('Test Name').' : '}}</b>{{$culture->test_name}}</p>
										 <p><b>{{__('Gram Staim').' : '}}</b>{{$culture->gram_staim}}</p>
										 <p><b>{{__('Culture Urine').' : '}}</b>{{$culture->culture_urine}}</p>
											
									   </div>
								</div>	   
							</div>
				   
					</div>
					<div style="page-break-inside:avoid;margin-bottom:10px;">
							<div style="width:100%">
							   <div class="row">
									   <div class="col-md-12">
									      <table  class="table table-borderless" style="width:100%;">
											    <thead>
												  <tr>
												    <th>{{__('Bacteria')}}</th>
													<th>{{__('Antibiotic')}}</th>
													<th>{{__('Result')}}</th>
												  </tr>
												</thead>
												<tbody>
												  @foreach($details as $d)
												    <tr>
													 <td style="width:40%;">{{$d->bacteria_name}}</td>
													 <td style="width:40%;">{{$d->antibiotic_name}}</td>
													 <td style="width:20%;">{{$d->result}}</td>
													</tr> 
												  @endforeach
												</tbody>
											</table>	
									   </div>
							</div>
				   
					</div>
				</div>
			   </div>
			</main>
		    
	</body>
</html>
