<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Request')}}</title>
			<style>
			 body {
              margin: 0;
              padding: 0;
             
             }
			.table.table-borderless {
				border-collapse: collapse;
				border-spacing: 0;
				table-layout: auto;
				width: 100%;
				max-width: 100%;
				text-align: left;
				
			}

			.table.table-borderless tr {
				margin: 0;
				padding: 0;
			}

			.table.table-borderless td,
			.table.table-borderless th {
				padding-top: 0;
				padding-bottom: 0;
				
			}
			
            </style>
			
	</head>
	<body style="font-size:12px;">	
    
			   <div  style="width:100%;">
	       		                           
					  <div  style="border:2px solid padding:1px;">
                            <div style="float:left;width:60%;margin-left:5px;">
							     <div><b>{{__('Request Nb').' : '}}</b>{{$order->id}}</div>
								 <div>
								  <b>{{__('Name').' : '}}</b>
								  @if($patient->middle_name !='' && isset($patient->middle_name))
									{{$patient->first_name.' '.$patient->middle_name.' '.$patient->last_name}}
								  @else
									{{$patient->first_name.' '.$patient->last_name}}	  
								  @endif	  
							    </div>
								<div><b>{{__('Date').' : '}}</b>{{Carbon\Carbon::parse($order->order_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->order_datetime)->format('H:i')}}</div>

                            </div>
                            <div style="float:left;width:40%;margin-left:5px;">
                                 @if(isset($doctor)) 
								  <div>
									  <b>{{__('Ref By Doc').' : '}}</b>
									  @if($doctor->middle_name !='' && isset($doctor->middle_name))
										{{$doctor->first_name.' '.$doctor->middle_name.' '.$doctor->last_name}}
									  @else
										{{$doctor->first_name.' '.$doctor->last_name}}	  
									  @endif
								  </div>
								 @endif
                                  @if(isset($ext_lab) && isset($ext_lab->full_name) && $ext_lab->full_name!='')
								  <div><b>{{__('Guarantor').' : '.$ext_lab->full_name}}</b></div>	
							     @else	 
								  <div><b>{{__('Guarantor').' : '.__('Private')}}</b></div>	
							     @endif
								<div><b>{{__('Gender').' : '}}</b> {{($patient->sex=='F')?'Female':( ($patient->sex=='M')?'Male':'Undefined')}}</div>
                                 @if(isset($patient->birthdate) && $patient->birthdate!='')
									 <div><b>{{__('Age/DOB').' : '}}</b> {{UserHelper::getPatAge($patient->birthdate)}}
								     <b style="margin-left: 20px;"></b> {{Carbon\Carbon::parse($patient->birthdate)->format('d/m/Y')}}</div>
								@endif
										                	
							</div>
                            <div style="clear:both;"></div>
                      </div>  
					  
                                 
					<div style="margin-top:10px;margin-bottom:10px;">
 				        <div style="width:100%">
						   
						   @foreach($arr as $cat => $grp)
							   <table class="table table-borderless" style="font-size:12px;width:100%;">
							    <tr>
							     <td colspan="5" style="border:0;"><b style="min-width:200px;display:inline-block;font-size:14px;background-color: #f0f3f4;">{{$cat}}</b></td>
								</tr>
								@php $index=0;@endphp
								 <tr>
								@foreach($grp as $key => $val)
							         @if (count($grp)==1)
										<td colspan="5"><b>{{$key}}</b>
										@else
									     <td colspan="5"><b>{{$key}}</b>
							        @endif
									<!--@if($val!="NG")
								      <table class="table table-borderless">
								       <tr>
									   @php $index1=0;@endphp
									   @foreach($val as $tst)
								           
									         <td style="font-size:11px;border:0;">{{$tst}}</td>
									   
										   @php $index1++; @endphp
										   @if (count($grp)==1)
											 @if($index1%5==0)
										       </tr><tr>
									          @endif   
										   @else   
										     @if($index1%2==0)
										       </tr><tr>
									          @endif
									       @endif
									   @endforeach
									   </tr>
									   </table>
								    @endif-->	
								    </td>
								    
								 @php $index++; @endphp
								 @if ($index%5==0)
										</tr><tr>
									@endif
									
							   @endforeach
							   
							   </tr>
							   
							   </table>
						   @endforeach	   
						</div>
						
		           </div>
			       
				          
			</div>	
		
	</body>
</html>
