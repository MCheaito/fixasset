<!DOCTYPE html>
<html>
	<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Results')}}</title>
			<style>
			 @page { margin: 140px 25px 60px 25px; }
             header { position: fixed; top: -120px; left: 0px; right: 0px; height: 50px; }
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
			     @include('lab.visit.header')
			   </header>
			   <footer>
			     @include('lab.visit.footer')
			   </footer>
			   <main>
			   <div  style="width:100%;">
	       		<div style="margin-bottom:10px;">
 				        <div style="width:100%">
						   @php $cat_cnt=0; @endphp
						   @foreach($categories as $cat)
							   @php $cat_style = ($cat_cnt==0)?'margin-bottom:15px;':'page-break-inside:avoid;margin-bottom:15px;' @endphp
							    <div style="{{$cat_style}}">	   
							  
								   <div class="row">
									   <div class="col-md-12"> 
										@php $res = $results->where('category_num',$cat->id)->groupBy('group_name')->all(); @endphp  
										<table  class="table table-borderless" style="width:100%;">
											    <thead>
											       <tr>
													  <th colspan="5" style="background-color: #eeeeee;border:0px;text-align:center;"><b style="min-width:100px;display:inline-block;margin-bottom:2px;font-size:14px;">{{isset($cat)?$cat->descrip:''}}</b></th>
												   </tr>
												   <tr>
													  <th style="border:0px;padding-bottom:5px;">{{__("Test Name")}}</th>
													  <th style="border:0px;padding-bottom:5px;">{{__("Result")}}</th>
													  <th style="border:0px;padding-bottom:5px;">{{__("Flag")}}</th>
													  <th style="border:0px;padding-bottom:5px;">{{__("Unit")}}</th>
													  <th style="border:0px;padding-bottom:5px;">{{__("Reference Range")}}</th>
												  </tr>
												  
											 	</thead>
												<tbody>
													@foreach($res as $data)	
													  @if($data[0]->group_name!=__('Other'))
														    <tr>
															  <td style="color:#fff;background-color: #00548E;border:0px;" colspan="5">
															   <b style="margin-left:5px !important;">{{$data[0]->group_name}}</b>
															  @if(isset($data[0]->group_instruction) && $data[0]->group_instruction!='')
															   <div style="margin-left:5px !important;line-height:8px;font-size: 9px !important;">{{'('.$data[0]->group_instruction.')'}}</div>
															  @endif
															  
															  </td>
															 </tr>
														    
														  @endif
														  
													
														  
												        @php $row_cnt=0;@endphp
														@foreach($data as $r)  
														  @php
														      $test_calc = NULL;
														      $test_calc_unit = NULL;
														      if(isset($r->test_type) && $r->test_type=='C'){
															       $test_calc = $r->calc_result;
																   $test_calc_unit = $r->calc_unit;
																   }
														          
																  $ref_range = '';
																  $unit = '';
																  $is_printed = $r->is_printed;
																  $dt = UserHelper::getRefUnit($patient->sex,$r->test_id,$is_printed,$r->field_num);
																  $ref_range = $dt["ref_range"];
																  $unit=$dt["unit"];
																  $res_sign = UserHelper::getSign($r->sign);
																  
																@endphp
														 
														
															
															
															@if( $r->group_name=='Other')
																
															    
																<!--<tr style="background-color: #eeeeee;">
																		  <th style="border:0px;">{{__("Test Name")}}</th>
																		  <th style="border:0px;">{{__("Flag")}}</th>
																		  <th style="border:0px;">{{__("Result")}}</th>
																		  <th style="border:0px;">{{__("Reference Range")}}</th>
																		  <th style="border:0px;">{{__("Unit")}}</th>
																</tr>-->
																<!--<tr>
																  <td style="border:0px;border-bottom:1px solid;" colspan="5">
																	  <b>{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</b>
																	  @if(isset($r->method_instruction) && $r->method_instruction!='')
																	   <div style="line-height:8px;font-size: 9px !important;">{{'('.$r->method_instruction.')'}}</div>
																	  @endif
																  </td>
																</tr>-->
															 	
														 @endif
														
															<tr>
															 <td style="width:30%;" class="border">
															  @if($data[0]->group_name=='Other')
															      <b style="margin-left:5px !important;font-size:12px !important;">{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</b>
															      @if(isset($r->method_instruction) && $r->method_instruction!='')
															       <div style="margin-left:5px !important;font-size: 9px !important;">{{'('.$r->method_instruction.')'}}</div>
															      @endif
															  @else
																  <b style="font-size:10px !important;margin-left:20px !important;">{{isset($r->test_name)&&$r->test_name!=''?$r->test_name:'-'}}</b>
															      @if(isset($r->method_instruction) && $r->method_instruction!='')
															       <div style="margin-left:20px !important;font-size: 9px !important;">{{'('.$r->method_instruction.')'}}</div>
															      @endif
															  @endif	  
															  
															 </td>
															 
															 <td class="border" style="font-size:10px;width:25%;">
															       @if($r->result!='')
																       {{$r->result}}
																   @else
																	   {{_('Pending')}}
																   @endif	   
																  
																   @if(isset($test_calc) && $test_calc!='')
																	 <div>
																      <small>{{$test_calc}}</small>&nbsp;&nbsp;&nbsp;&nbsp;
																	  @if(isset($test_calc_unit) && $test_calc_unit!='')
																		  @php $test_calc_unit= UserHelper::replaceGreekSymbols($test_calc_unit); @endphp 
															     	        @if(strpos($test_calc_unit, '^') !== false) 
																		       @php 
																		       $modifiedText = preg_replace('/(\d+)\^(\d+)\/([a-zA-Z]{1,2})(\d*)/', '$1<sup>$2</sup>/$3<sup>$4</sup>', $test_calc_unit);			  
																		       @endphp
																		      <small>{!!$modifiedText!!}</small>
																	        @else
																		      <small>{!!$test_calc_unit!!}</small>
																	        @endif
																        @endif
																	  </div>
																   @endif
															 </td>
															 <td style="width:8%;" class="border" style="text-align:center;">
															   <small><b>{{$res_sign}}</b></small>
															 </td>									
															 <td style="width:7%;" class="border">
															   
															  @if(strpos($unit, '^') !== false) 
																	@php 
																	 $modifiedText = preg_replace('/(\d+)\^(\d+)\/([a-zA-Z]{1,2})(\d*)/', '$1<sup>$2</sup>/$3<sup>$4</sup>', $unit);			  
																	@endphp
																	<small style="font-size:9px;">{!!$modifiedText!!}</small>
																@else
																	<small style="font-size:9px;">{!!$unit!!}</small>
																@endif
															 </td> 
															 <td style="width:30%;" class="border">
															    <div style="font-size:10px;">{!!$ref_range!!}</div>
																@if(isset($r->reference_remark) && $r->reference_remark!='')
																  <div><textarea style="padding:0;margin:0;border:0px;outline:0;font-size:10px;">{{$r->reference_remark}}</textarea></div>	
																@endif	
															  </td>
															  
															
															 </tr>
															
															@endforeach
															 @if($data[0]->group_name!=__('Other'))
																 @if(isset($data[0]->group_clinical_remark) && $data[0]->group_clinical_remark!='')
																 <tr>
																  <td  colspan="5" style="border:0px;">
																  <div style="padding-left:3px;margin-top:3px;line-height: 1;border:1px solid;white-space: pre-wrap;font-size:10px;">{{$data[0]->group_clinical_remark}}</div>
																  </td> 
																</tr>
																@endif
															 @else	 
																 @if(isset($data[0]->clinical_remark) && $data[0]->clinical_remark!='')
																 <tr>
																  <td  colspan="5" style="border:0px;">
																  <div><textarea class="form-control" style="max-width:100%;margin-top:3px;padding:0px;font-style: italic;font-size: 11px !important;">{{$data[0]->clinical_remark}}</textarea></div>
																  </td> 
																</tr>
																@endif
															 @endif
															@php $row_cnt++;@endphp 
														</tbody>   
													   @endforeach
                  			                            
											</table>
										</div>
									</div>	
							   </div>
							   @php $cat_cnt++; @endphp 
						   @endforeach	   
						</div>
		           </div>
				   <!--@if($order->is_phlotobomy=='Y')
					   <div style="page-break-inside:avoid;margin-bottom:15px;">
				          <div class="row">
						     <div class="col-md-12"> 
								<table  class="table table-borderless" style="width:100%;">
								   <thead>
									  <tr><th colspan="2" style="border:0px;text-align:left;"><b style="min-width:100px;display:inline-block;margin-bottom:5px;padding:2px;font-size:16px;">{{__('Phlebotomy')}}</b></th></tr>
									  <tr>
											<td style="border:0px;border-bottom:1px solid;">
											  <b>{{__('Request Nb').' : '.$order->id}}</b>
											</td>
											<td style="border:0px;border-bottom:1px solid;">
											@php 
											   $pat_name = $patient->first_name.' '.$patient->last_name;
											   if($patient->middle_name !='' && isset($patient->middle_name)){
												 $pat_name = $patient->first_name.' '.$patient->middle_name.' '.$patient->last_name;
											    }
											@endphp	
											 <b>{{__('Patient').' : '.$pat_name}}</b>
											</td>
									    </tr>
										
										<tr style="background-color: #eeeeee;">
										 <th style="border:0px;">{{__('Test')}}</th>
										 <th style="border:0px;">{{__('Preanalytical')}}</th>
										</tr>
								   </thead>
								   <tbody>
								        
									    @foreach($phlebotomy as $ph)
										  <tr>
										  <td class="border">{{$ph->test_name}}</td>
										  <td class="border">{{$ph->preanalytical}}</td>
										  </tr>
										@endforeach
								   </tbody>
								</table>
							 </div>
                          </div>							 
				       </div>
				   @endif-->	   
			       @if($documents->count())
					  @foreach($documents as $d) 
				       
					   <div style="page-break-inside:avoid;margin-bottom:15px;">
							<img class="img-fluid" src="{{config('app.url').Storage::url('app/private/'.$d->path)}}" alt="photo" style="width:auto;max-width:75%;"/>
						
					   </div>
					  @endforeach 
				   @endif
				   @if(isset($order->fixed_comment) && $order->fixed_comment!='')
					   <div style="page-break-inside:avoid;margin-bottom:15px;">
							<label>{{__("Global Comment")}}</label>
							<textarea style="margin-left:5px;width:auto;max-width:95%;">{{$order->fixed_comment}}</textarea>
					   </div>
				   @endif
                   
				          
			</div>
			</main>
		    
	</body>
</html>
