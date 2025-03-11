<!-- HTML template with placeholders for page numbers -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Template</title>
	<style>
        @page { margin: 180px 20px 60px 20px; }
             header { position: fixed; top: -160px; left: 0px; right: 0px; height: 50px; 
			          font-family: 'DejaVu Serif', serif;
                      line-height: 1;
		              font-size: 11px !important;
			        }
             footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 50px; 
			          font-family: 'DejaVu Serif', serif;
                      line-height: 1;
		              font-size: 9px !important;
					}
	   .page:after { content: counter(page, decimal); }
	   
	   main {
		  font-family: Helvetica;
          line-height: 1;
		  font-size: 11px !important;
				  }
		
		
		.center {
                text-align: center;
              }
        
		.center img {
                display: block;
              }		  
	  	    
		table{
		border-collapse: collapse;
		border-spacing: 0;
		width: 100%;
		max-width: 100%;
		
	   }

	</style>
</head>
<body>
   <header>
	 <div  style="border:0px; padding:1px;">
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
								
								<div><b>{{__('Reg. Date').' : '}}</b>{{Carbon\Carbon::parse($order->order_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->order_datetime)->format('H:i')}}</div>
					            <div><b>{{__('Collection Date').' : '}}</b> {{isset($order->collection_date) && $order->collection_date!=''?Carbon\Carbon::parse($order->collection_date)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->collection_date)->format('H:i'):'-'}}</div>
                               	<div><b>{{__('Reporting Date').' : '}}</b> {{isset($order->report_datetime) && $order->report_datetime!=''?Carbon\Carbon::parse($order->report_datetime)->format('d/m/Y').' at '.Carbon\Carbon::parse($order->report_datetime)->format('H:i'):'-'}}</div>
                               				                	
							</div>
                            <div style="clear:both;"></div>
                      </div> 
   </header>
   <footer>
	 @include('lab.visit.footer')
   </footer>
   <main style="page-break-inside:auto;margin-top:10px;margin-bottom:10px;">    
	@if($template->cat_name=='')
	    <div style="margin-top:5px;margin-bottom:5px;background-color: #eeeeee;border:0px;text-align:center;"><b style="min-width:100px;display:inline-block;margin:5px;font-size:14px;">{{$template->test_name}}</b></div>
     @else 
	    <div style="margin-top:5px;margin-bottom:5px;background-color: #eeeeee;border:0px;text-align:center;"><b style="min-width:100px;display:inline-block;margin:5px;font-size:14px;">{{$template->cat_name.' : '.$template->test_name}}</b></div>
	 @endif
	<div style="margin-top:5px;margin-bottom:5px;width:100%;max-width:100%;">
	  {!! $descrip !!}
	</div>
	
    </main>
	
</body>
</html>



