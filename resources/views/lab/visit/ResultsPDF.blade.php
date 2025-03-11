<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<!-- Font Awesome -->
            <link rel="stylesheet" href="{{ asset('dist/fontawesome-free/css/all.min.css') }}">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Results')}}</title>
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
			
			
			.table.table-borderless {
				border-collapse: collapse;
				border-spacing: 0;
				table-layout: auto;
				width: 100%;
				max-width: 100%;
				text-align: left;
				font-size: 10px !important;
				
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
	<body>	
               <header>
			     @include('lab.visit.header')
			   </header>
			   <footer>
			     @include('lab.visit.footer')
			   </footer>
			   <main>
			   <div  style="width:100%;">
	       		<div style="margin-bottom:5px;">
 				        <div style="width:100%">
						   @php $cat_cnt=0; @endphp
						   @foreach($categories as $cat)
							   @php $cat_style = ($cat_cnt==0)?'margin-bottom:10px;':'page-break-inside:avoid;margin-bottom:10px;' @endphp
							    <div style="{{$cat_style}}">	   
							  
								   <div class="row">
									   <div class="col-md-12"> 
										           @php 
										            $res = $results->where('category_num',$cat->id)
										                   ->sortBy(function ($item) {
                                                             return is_null($item->group_order) ? PHP_INT_MAX : $item->group_order;
                                                             })
                                                           ->groupBy('group_order')
                                                           ->all();
													
                                                   @endphp  
										
										<table  class="table table-borderless" style="width:100%;">
											    <thead>
											       <tr>
													  <th colspan="5" style="color:#fff;background-color: #1B74B8;border:1px solid #1B74B8;text-align:center;"><b style="min-width:100px;display:inline-block;margin-top:2px;margin-bottom:2px;font-size:13px;">{{isset($cat)?$cat->descrip:''}}</b></th>
												   </tr>
												  
												   <tr>
													  <th style="padding-left:5px !important;background-color: #eeeeee;text-align:left;border-left:1px #ccc solid;border-right:1px #ccc solid;padding-bottom:5px;">{{__("Test Name")}}</th>
													  <th style="padding-left:5px !important;background-color: #eeeeee;text-align:left;border-left:1px #ccc solid;border-right:1px #ccc solid;padding-bottom:5px;padding-right:5px;">{{__("Result")}}</th>
													  <th style="background-color: #eeeeee;text-align:center;border-left:1px #ccc solid;border-right:1px #ccc solid;padding-bottom:5px;">{{__("Flag")}}</th>
													  <th style="padding-left:5px !important;background-color: #eeeeee;text-align:left;border-left:1px #ccc solid;border-right:1px #ccc solid;padding-bottom:5px;">{{__("Unit")}}</th>
													  <th style="padding-left:5px !important;background-color: #eeeeee;text-align:left;border-left:1px #ccc solid;border-right:1px #ccc solid;padding-bottom:5px;">{{__("Reference Range")}}</th>
												  </tr>
												  
											 	</thead>
												<tbody>
													
													@foreach($res as $data)	
												       
														@php 
														  $row_cnt=0;
														  $d = $data->sortBy(function ($item) {
                                                                        return is_null($item->position) ? PHP_INT_MAX : $item->position;
                                                                        })->groupBy('group_name')->all();
														@endphp
													  @foreach($d as $data)
														 @if($data[0]->group_name!=__('Other'))
														      <tr>
															    <td style="color:#1B74B8;border-bottom: 1px solid #7EC8E3;" colspan="5">
															     <b style="margin-left:5px !important;font-size:10px !important;">{{$data[0]->group_name}}</b>
															     @if(isset($data[0]->group_instruction) && $data[0]->group_instruction!='')
															      <span style="margin-left:5px !important;line-height:8px;font-size: 8px !important;">{{'('.$data[0]->group_instruction.')'}}</span>
															     @endif
															    </td>
															 </tr>
														    
														  @endif
													 @foreach($data as $r)  
														   @if($r->is_title=='Y')   
															 <tr><td colspan="5" style="font-weight: bold; margin-left:5px !important;font-size:10px !important;"><u  style="margin-left:5px !important;font-size:10px !important;">{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</u></td></tr> 
														   @else	  
															  @php
														        $test_calc = NULL;
														        $test_calc_unit = NULL;
														         if(isset($r->test_type) && $r->test_type=='C'){
															       $test_calc = $r->calc_result;
																   $test_calc_unit = $r->calc_unit;
																   }
														          
																$unit = $r->unit;
																$is_printed = $r->is_printed;
																$res_sign = UserHelper::getSign($r->sign);
														 	   @endphp
														   
														
															<tr>
															 <td style="width:36%;max-width: 36%;" class="border">
															  @if($data[0]->group_name=='Other')
															      <b style="color:#1B74B8;margin-left:5px !important;font-size:10px !important;">{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</b>
															      @if(isset($r->method_instruction) && $r->method_instruction!='')
															       <span style="color:#1B74B8;margin-left:5px !important;font-size: 8px !important;">{{'('.$r->method_instruction.')'}}</span>
															      @endif
															  @else
																  <i><b style="font-size:9px !important;margin-left:20px !important;">{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</b></i>
															      @if(isset($r->method_instruction) && $r->method_instruction!='')
															       <span style="margin-left:5px !important;font-size: 8px !important;">{{'('.$r->method_instruction.')'}}</span>
															      @endif
															  @endif	  
															  
															 </td>
															    <!--check if test is to be validated and result status is not valid then write To Validate-->
															      @if($r->need_validation=='Y')
																	   	<td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;">{{__('Pending Validation')}}</td>
                                                                  @else																	

																	   @if(isset($r->result) && $r->result!='')
																		  @if($res_sign=='H' || $res_sign=='Pos')
																		     <td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;"><span style="display: block;width: 100%;padding: 5px;box-sizing: border-box;background-color:#FCE6E9;">{{$r->result}}</span></td> 
																	      @else
																			@if($res_sign=='L' || $res_sign=='Neg')  
																	         <td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;"><span style="display: block;width: 100%;padding: 5px;box-sizing: border-box;background-color:#F5F4F9;">{{$r->result}}</span></td> 
																			@else 
																	         <td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;">{{$r->result}}</td> 
																	        @endif
																		  @endif	
																	   @else
																		 @if(isset($r->result_txt) && $r->result_txt!='')
																		   <td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;word-wrap: break-word;overflow-wrap: break-word; word-break: break-all;">{{$r->result_txt}}</td>
																		 @else 
																		   
																			@if($order->status=="P")
																			<td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;">{{__('Pending')}}</td>
																			@else
																			<td class="border" style="padding-left:5px !important;text-align:left;font-size:10px;width:24%;max-width: 24%;">{{__('')}}</td>
																		    @endif	   
																		 @endif
																	   @endif	   
																  @endif
															 <td style="font-size:9px;text-align:center;width:8%;max-width: 8%;" class="border">
															  @if($r->need_validation!='Y') 
															   @switch($res_sign)
															    @case('H') @case('h') <i class="fa fa-arrow-up fa-md" style="padding-top:5px;" aria-hidden="true"></i> @break
																@case('L') @case('l') <i class="fa fa-arrow-down fa-md" style="padding-top:5px;" aria-hidden="true"></i> @break
																@default  <small><b>{{$res_sign}}</b></small>
															   @endswitch
															  @endif
															 </td>									
															 <td style="width:8%;max-width: 8%;" class="border">
															   
															  @if(strpos($unit, '^') !== false) 
																	@php 
																	 $modifiedText = preg_replace('/(\d+)\^(\d+)\/([a-zA-Z]{1,2})(\d*)/', '$1<sup>$2</sup>/$3<sup>$4</sup>', $unit);			  
																	@endphp
																	<small style="padding-left:5px !important;font-size:8.5px;">{!!$modifiedText!!}</small>
																@else
																	<small style="padding-left:5px !important;font-size:8.5px;">{!!$unit!!}</small>
																@endif
															 </td> 
															 <td style="padding-left:5px;width:24%;max-width: 24%;font-size:9px;" class="border">
															    {!!$r->ref_range!!}
																@if(isset($r->code_remark) && $r->code_remark!='')
																  <div style="padding-left:5px !important;white-space:pre-line;">{{$r->code_remark}}</div>	
																@endif	
															  </td>
															  
															
															 </tr>
															@endif
															@endforeach
															 
															 @if($data[0]->group_name!=__('Other'))
																 @if(isset($data[0]->group_clinical_remark) && $data[0]->group_clinical_remark!='')
																 <tr>
																  <td  colspan="5" style="border:0px;">
																  <div style="padding-left:5px;margin-top:3px;line-height: 1;border:1px solid;white-space: pre-wrap;font-size:9px;">{{$data[0]->group_clinical_remark}}</div>
																  </td> 
																</tr>
																@endif
															 @else	 
																 @if(isset($data[0]->clinical_remark) && $data[0]->clinical_remark!='')
																 <tr>
																  <td  colspan="5" style="border:0px;">
																   <div style="padding-left:5px;margin-top:3px;line-height: 1;border:1px solid;white-space: pre-wrap;font-size:9px;">{{$data[0]->clinical_remark}}</div>
																  </td> 
																</tr>
																@endif
															 @endif
															@php $row_cnt++;@endphp 
														 
														 @endforeach 
														@endforeach
														</tbody>   
													   
                  			                            
											</table>
										</div>
									</div>	
							   </div>
							   @php $cat_cnt++; @endphp 
						   @endforeach	   
						</div>
		           </div>
				  	   
			       @if($documents->count())
					  @foreach($documents as $d) 
				       
					   <div style="page-break-inside:avoid;margin-bottom:15px;">
							<img class="img-fluid" src="{{config('app.url').Storage::url('app/7mJ~33/'.$d->path)}}" alt="photo" style="width:auto;max-width:75%;"/>
						
					   </div>
					  @endforeach 
				   @endif
				   @if(isset($order->fixed_comment) && $order->fixed_comment!='')
					   <div style="page-break-inside:avoid;margin-bottom:15px;">
							<label>{{__("Global Comment")}}</label>
							<textarea style="margin-left:5px;width:auto;max-width:95%;">{{$order->fixed_comment}}</textarea>
					   </div>
				   @endif
				   
				 
                    
				   <!--Culture Pages-->
				   
				   @if($culture_data->count())
				    @foreach($culture_data as $culture)
					  <div  style="width:100%;page-break-before:always;">
								<div style="margin-top:20px;margin-bottom:10px;margin-left:10px; width: 90%; float: left;box-sizing: border-box;">
										<div style="border: 1px solid #ccc;margin-bottom: 10px;">
											<div style="background-color: #eeeeee;border:0px;text-align:center;padding: 5px;">
												<b>{{__('Bacteriology')}}</b>
											</div>
											 <div style="padding: 10px;">
													<div style="display: inline-block; width: 100%;"><b>{{__('Test Name').' : '}}</b>{{$culture->test_name}}</div>
													<div style="display: inline-block; width: 100%;"><b>{{__('Gram Stain').' : '}}</b>{{$culture->gram_staim}}</b></div>
													<div style="display: inline-block; width: 100%;"><b>{{__('Culture Result').' : '}}</b>{{$culture->need_validation=='Y'?__('Pending Validation'):$culture->culture_result}}</div>
											</div>
										</div>
									  
									  
								</div>
							   <div style="clear: both;"></div>
							
							@if(count($bacteria))
							  <div style="width:100%">
							  @php $counter = 0; @endphp
							  @foreach($bacteria as $b)
								  @php 
								   $details = $culture_details->where('bacteria_id',$b->id)->where('culture_id',$culture->culture_id)->all();
								   $bacterias = $culture_details->where('bacteria_id',$b->id)->where('culture_id',$culture->culture_id)->pluck('bacteria_id')->toArray();
								  @endphp
								  @if(in_array($b->id,$bacterias))	  
								  <div style="margin-left:5px; width: 45%; float: left;box-sizing: border-box;">
									<div style="border: 1px solid #ccc; margin-bottom: 10px;">
										<div style="background-color: #00548E; color: #fff; padding: 5px;">
											<b>{{__('Germ')}}: {{$b->descrip}}</b>
										</div>
										  <div style="padding: 10px;">
											<div style="display: inline-block; width: 70%;"><b><u>{{__('Antibiotics')}}</u></b></div>
											<div style="display: inline-block; width: 25%;"><b><u>{{__('Results')}}</u></b></div>
											
												@foreach($details as $d)
												<div style="padding-bottom: 1px;padding-top: 2px;border-bottom: 1px solid #ccc;">
												 <div style="display: inline-block; width: 70%;">{{$d->antibiotic_name}}</div>
												 <div style="display: inline-block; width: 25%;">{{$culture->need_validation=='Y'?__('-'):$d->result}}</div>
										        </div>
												@endforeach
											
										</div>
									</div>
									
								  </div>
								 @php $counter++;@endphp
								 @if ($counter %2 ==0) 
									<div style="clear: both;"></div>
								 @endif  
							@endif
						  
						  @endforeach
						  @if ($counter %2 !=0) 
								 <div style="clear: both;"></div>
						  @endif  
						@endif
						</div>
					  
					   <div  style="font-size:10px;margin-left:10px;margin-top:30px;text-algin:center;width:100%;">
						   <p><b>S:Sensitive</b></p>
						   <p><b>R:Resistant</b></p>
						   <p><b>I:Intermediate</b></p>
					   </div>
					  
					  </div>
					 @endforeach			  
				  @endif
                  
				  @if(isset($qrCodeImage))
				      <div style="float:left;margin-bottom:10px;">
                         <img src="{{$qrCodeImage}}" alt="QR Code">
						 <div style="margin-top: 5px;font-size:9px;text-align: center;"><b>Note: This code is valid for only 4 days</b></div>
				      </div>
					  <div style="float:right;margin-bottom:10px;"></div>
					  <div style="clear: both;"></div>
					
				   @endif
				   <!--@if(isset($general_sig))
				    <div style="float:right;margin-bottom:10px;">
                       <img src="{{config('app.url').'/storage/app/7mJ~33/'.$general_sig->path}}" style="width:140px;height:70px;" alt="SIgnature">				  
				    </div>
					@else
 						<div style="float:right;"></div>
				   @endif
				   <div style="clear: both;"></div>
				  -->
				 </div>
			</main>
		    
	</body>
</html>
