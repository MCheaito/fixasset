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
			<title>{{__('Order')}}</title>
			<style>
			  .txt{
					width: 3em;
				}
			</style>
		</head>
	    <body style="font-size:14px;">	
            <div style="width:100%;">
	            <div>
                      <div class="top_line_cmd">
                            <div style="float:left;margin:20px;margin-top:1px;margin-bottom:1px;">
							 <div><b>
							  @php 
							   $type = __('Order').' ';
							   if($cmd->is_warranty=='Y'){ $type = __('Warranty Order');}
							   $type.='#'.$cmd->id;
							  @endphp
							   @if(isset($from_warranty))
								   {{$from_warranty.','.$type}}
							   @else
								   {{$type}}
							   @endif
							 </b></div>
							 <div>{!! DNS1D::getBarcodeHTML(strval($cmd->id), 'C128',2,22) !!}</div>
							 <div><b>{{__("Date")}}:</b>&#xA0;{{Carbon\Carbon::parse($cmd->date_cmd)->format('Y-m-d')}}</div>
							 @if($cmd->is_warranty=='Y')
							     @if(isset($cmd) && isset($cmd->reference))
								 <div><b>{{__('Ref. Invoice')}}:</b> {{$cmd->reference}}</div>
								 @endif
							 @endif
							 @if(isset($cmd->cmd_cabaret_num) && $cmd->cmd_cabaret_num !='')
								 <div><b>{{__("Cabaret Nb.")}}:</b>&#xA0;{{$cmd->cmd_cabaret_num}}</div>
							 @endif
							 
                            </div>
                            <div style="float:right;margin:20px;margin-top:1px;margin-bottom:1px;">
							   <div><b>{{__('Visit').' : '}}</b> {{'#'.$cmd->visit_num}}</div>
							   @if(isset($clinic_inv_num) && $clinic_inv_num!='')
							   <div><b>{{__('Invoice')}}:</b> {{$clinic_inv_num}}</div>	  
							   @endif
							   @if(isset($cmd->expected_delivery))
							   <div><b>{{__('Expected Delivery')}}:</b>&#xA0;{{$cmd->expected_delivery}}</div>
						       @endif	   
							   @if(isset($cmd->representant) && $cmd->representant!='')
							   <div><b>{{__('Representant')}}:</b> {{UserHelper::getRepresentant($cmd->representant)}}</div>	
						       @endif
							   @if(isset($cmd->statuslab) && $cmd->statuslab!='')
								@php 
							     $lab_status=UserHelper::getStatusLab($cmd->statuslab,app()->getLocale()); 
								 if(isset($cmd->sentlab) && $cmd->sentlab!=''){
								  $lab_status_det=UserHelper::getAllStatusLab($cmd->sentlab,$cmd->datesent);
								  $lab_status.='-'.$lab_status_det; 
							     }
								@endphp
							    <div><b>{{__("Status")}}:</b>&#xA0;{{$lab_status}}</div>
							   @endif
                            </div>
                            <div style="clear:both;"></div>
                      </div>
                      <div>
                          
						  <div style="float:left;width:60%">
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						       @if(isset($doctor))<div><b>{{$doctor->first_name.' '.$doctor->last_name.(isset($doctor->doc_title)?' '.$doctor->doc_title:'')}}</b></div>@endif
							   <div><b>{{$clinic->full_name}}</b></div>   
							   @php 
								   $address_details = '';
								   if(isset($clinic) && isset($clinic->full_address) && $clinic->full_address!=''){
									  $address_details = $clinic->full_address; 
								   }
								   if(isset($clinic) && isset($clinic->city_name) && $clinic->city_name!=''){
									  $address_details.=' , '.$clinic->city_name; 
								   }
								   if(isset($clinic) && isset($clinic->province_code) && $clinic->province_code!=''){
									  $address_details.=' , '.$clinic->province_code;  
								   }
								   if(isset($clinic) && isset($clinic->country_code) && $clinic->country_code!=''){
									  $address_details.=' , '.$clinic->country_code;  
								   }
								   if(isset($clinic) && isset($clinic->zip_code) && $clinic->zip_code!=''){
									  $address_details.=' , '.$clinic->zip_code;  
								   }
							   @endphp
							   @if($address_details!='')<div>{{$address_details}}</div>@endif
							   @if(isset($clinic) && isset($clinic->telephone) && $clinic->telephone!='')<div>T.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax) && $clinic->fax!='')<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email) && $clinic->email!='')<div>{{__("Email")}}: {{$clinic->email}}</div>@endif

							</div>
                            <div style="clear:both;"></div>
                          </div>
                          <div style="float:left;width:40%">
                               <div style="float:left;margin-left:10px;margin-top:0px;">
                                    <div><b>Patient :  {{$patient->first_name.' '.$patient->last_name.' ( '.$patient->id.' )'}}</b></div>
                                     @if(isset($patient->addresse) && $patient->addresse!='')<div>{{__("Address")}} : {{$patient->addresse.' '.$patient->city.' '.$patient->codepostale}}</div>@endif
                                     @if(isset($patient->first_phone) && $patient->first_phone!='')<div>{{__("Landline")}} :  {{$patient->first_phone}}</div>@endif
									 @if(isset($patient->cell_phone) && $patient->cell_phone!='')<div>{{__("Cell")}} :  {{$patient->cell_phone}}</div>@endif
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                         
					  </div>	  
					  
                </div>	
			    @if(count($right_rx_data) || count($left_rx_data))
				   @if(isset($right_rx_data['rsph']) || isset($right_rx_data['rbalance'])
					 || isset($right_rx_data['rcyl']) || isset($right_rx_data['raxe'])
					 || isset($right_rx_data['radd']) || isset($right_rx_data['rav'])
					 || isset($right_rx_data['rbc'])  || isset($left_rx_data['lsph']) 
					 || isset($left_rx_data['lbalance']) || isset($left_rx_data['lcyl']) 
					 || isset($left_rx_data['laxe']) || isset($left_rx_data['ladd']) 
					 || isset($left_rx_data['lav'])  || isset($left_rx_data['lbc'])
					 || isset($right_rx_data['rvertex']) || isset($right_rx_data['rdeloin'])
					 || isset($right_rx_data['rdepres']) || isset($right_rx_data['rprism'])
					 || isset($right_rx_data['rprism2']) || isset($right_rx_data['rbase'])
					 || isset($right_rx_data['rheight'])  || isset($left_rx_data['lvertex']) 
					 || isset($left_rx_data['ldeloin']) || isset($left_rx_data['ldepres']) 
					 || isset($left_rx_data['lprism']) || isset($left_rx_data['lprism2']) 
					 || isset($left_rx_data['lbase'])  || isset($left_rx_data['lheight'])
					 || isset($right_rx_data['rvertexlun']) || isset($right_rx_data['lvertexlun'])
					 || isset($right_rx_data['corr']) || isset($right_rx_data['galbe'])
					 || isset($right_rx_data['panto']) || isset($right_rx_data['sensvl'])
					 || isset($right_rx_data['sensvp']) || isset($right_rx_data['ap'])
					 || isset($right_rx_data['contraste']))
			      <div style="margin-top:10px;">
 				        
				            			<table class="table-bordered table-sm" style="margin-bottom:2px;width:100%;">
												 <thead>
													<tr>
														<th></th>
														@if((isset($right_rx_data['rbalance']) && $right_rx_data['rbalance']=='Y')
															|| (isset($left_rx_data['lbalance']) && $left_rx_data['lbalance']=='Y'))
														 <th>{{__('Balance')}}</th>
														@endif 
														<th>{{__('Sph')}}</th>
														<th>{{__('Cyl')}}</th>
														<th>{{__('Axe')}}</th>
														<th>ADD</th>
														<th>{{__('V.A.')}}</th>
														<th>{{__('BC')}}</th>
														
														<th>{{__('Vertex')}}</th>
														<th>{{__('Vertex Lun')}}</th>
														<th>{{__('Far P.D.')}}</th>
														<th>{{__('Near P.D.')}}</th>
														<th>{{__('Prism')}}</th>
														<th>{{__('Prism2')}}</th>
														<th>{{__('Base')}}</th>
														<th>{{__('Height')}}</th>
													</tr>
												 </thead>
												 <tbody>
													<tr>
													  <td>{{__('OD')}}</td>
  													  @if((isset($right_rx_data['rbalance']) && $right_rx_data['rbalance']=='Y')
															|| (isset($left_rx_data['lbalance']) && $left_rx_data['lbalance']=='Y'))
													  <td class="text-center"><input type="checkbox" style="width:20px;height:20px;" {{count($right_rx_data) && isset($right_rx_data['rbalance']) && $right_rx_data['rbalance']=='Y'? 'checked': ''}}/></td>
													  @endif
													  <td>{{count($right_rx_data) && isset($right_rx_data['rsph'])?$right_rx_data['rsph']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rcyl'])?$right_rx_data['rcyl']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['raxe'])?$right_rx_data['raxe']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['radd'])?$right_rx_data['radd']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rav'])?$right_rx_data['rav']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rbc'])?$right_rx_data['rbc']:''}}</td>
                                                      
													  <td>{{count($right_rx_data) && isset($right_rx_data['rvertex'])?$right_rx_data['rvertex']:''}}</td>
												      <td>{{count($right_rx_data) && isset($right_rx_data['rvertexlun'])?$right_rx_data['rvertexlun']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rdeloin'])?$right_rx_data['rdeloin']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rdepres'])?$right_rx_data['rdepres']:''}}</td>
												      <td>{{count($right_rx_data) && isset($right_rx_data['rprism'])?$right_rx_data['rprism']:''}}</td>
												      <td>{{count($right_rx_data) && isset($right_rx_data['rprism2'])?$right_rx_data['rprism2']:''}}</td>									  
												      <td>
														   @if(count($right_rx_data) && isset($right_rx_data['rbase']))
															@switch($right_rx_data['rbase'])
																@case('U') {{__('Up')}} @break
																@case('D') {{__('Down')}} @break
																@case('E') {{__('External')}} @break
																@case('I') {{__('Internal')}} @break
																@default {{__('')}} @break
															@endswitch
														  @else
														  {{__('')}}
														  @endif	  
												      </td>
												      <td>{{count($right_rx_data) && isset($right_rx_data['rheight'])?$right_rx_data['rheight']:''}}</td>

													</tr>
													<tr>
													  <td>{{__('OS')}}</td>
   													  @if((isset($right_rx_data['rbalance']) && $right_rx_data['rbalance']=='Y')
															|| (isset($left_rx_data['lbalance']) && $left_rx_data['lbalance']=='Y'))
													  <td class="text-center"><input type="checkbox" style="width:20px;height:20px;" {{count($left_rx_data) && isset($left_rx_data['lbalance']) && $left_rx_data['lbalance']=='Y'? 'checked': ''}}/></td>
													  @endif
													  <td>{{count($left_rx_data) && isset($left_rx_data['lsph'])?$left_rx_data['lsph']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lcyl'])?$left_rx_data['lcyl']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['laxe'])?$left_rx_data['laxe']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['ladd'])?$left_rx_data['ladd']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lav'])?$left_rx_data['lav']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lbc'])?$left_rx_data['lbc']:''}}</td>
													 
													  <td>{{count($left_rx_data) && isset($left_rx_data['lvertex'])?$left_rx_data['lvertex']:''}}</td>
												      <td>{{count($left_rx_data) && isset($left_rx_data['lvertexlun'])?$left_rx_data['lvertexlun']:''}}</td>
 													  <td>{{count($left_rx_data) && isset($left_rx_data['ldeloin'])?$left_rx_data['ldeloin']:''}}</td>
												      <td>{{count($left_rx_data) && isset($left_rx_data['ldepres'])?$left_rx_data['ldepres']:''}}</td>
												      <td>{{count($left_rx_data) && isset($left_rx_data['lprism'])?$left_rx_data['lprism']:''}}</td>
 												      <td>{{count($left_rx_data) && isset($left_rx_data['lprism2'])?$left_rx_data['lprism2']:''}}</td>
												      <td>
														@if(count($left_rx_data) && isset($left_rx_data['lbase']))
															@switch($left_rx_data['lbase'])
																@case('U') {{__('Up')}} @break
																@case('D') {{__('Down')}} @break
																@case('E') {{__('External')}} @break
																@case('I') {{__('Internal')}} @break
																@default {{__('')}} @break
															@endswitch
														  @else
														  {{__('')}}
														  @endif	  
												     </td>
												     <td>{{count($left_rx_data) && isset($left_rx_data['lheight'])?$left_rx_data['lheight']:''}}</td>
													
													</tr>
													
												 </tbody>
											   </table>
										 <table class="table-bordered table-sm" style="width:100%;">
										   <thead>
										     <th style="text-align:center;width: 97px;">Corr.</th>
											 <th style="text-align:center;width: 97px;">Galbe</th>
											 <th style="text-align:center;width: 97px;">Panto</th>
											 <th style="text-align:center;width: 97px;">Sens. VL</th>
											 <th style="text-align:center;width: 97px;">Sens. VP</th>
											 <th style="text-align:center;width: 97px;">AP</th>
											 <th style="text-align:center;width: 97px;">Cont.</th>
										   </thead>
										   <tbody>
										   <tr>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['corr'])){{$right_rx_data['corr']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['galbe'])){{$right_rx_data['galbe']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['panto'])){{$right_rx_data['panto']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['sensvl'])){{$right_rx_data['sensvl']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['sensvp'])){{$right_rx_data['sensvp']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['ap'])){{$right_rx_data['ap']}} @else &nbsp;@endif</td>
											 <td style="width: 97px;">@if(count($right_rx_data) && isset($right_rx_data['contraste'])){{$right_rx_data['contraste']}} @else &nbsp;@endif</td>
										   </tr>
										   </tbody>
										 </table>
								</div>
								@endif
					@endif
					@php $external_monture = isset($cmd->external_monture)?json_decode($cmd->external_monture):NULL;@endphp
					@if(isset($external_monture))
					 <div style="margin-top:10px;">
                          <p><u><b>{{__('External Item')}}</b></u></p>
					    <table class="table-bordered table-sm" style="width:100%;">
						 <thead >
						   <tr>
							 <th>{{__('Model')}}</th>
							 <th>{{__('Color')}}</th>
							 <th>{{__('Chassis-A')}}</th>
							 <th>{{__('Height-B')}}</th>
							 <th>{{__('Diag-ED')}}</th>
							 <th>{{__('Point-DBL')}}</th>
							 <th>{{__('Material')}}</th>
						   </tr>
						 </thead>
						 <tbody>
						    <tr>
							 <td>{{isset($external_monture[1])?$external_monture[1]:'' }}</td>
							 <td>{{isset($external_monture[2])?$external_monture[2]:''}}</td>
							 <td>{{isset($external_monture[3])?$external_monture[3]:''}}</td>
							 <td>{{isset($external_monture[4])?$external_monture[4]:''}}</td>
							 <td>{{isset($external_monture[5])?$external_monture[5]:''}}</td>
							 <td>{{isset($external_monture[6])?$external_monture[6]:''}}</td>
							 <td>{{isset($external_monture[0])?$external_monture[0]:''}}</td>
							 </tr>
                         </tbody>
                       </table>
                     </div>   
				    @endif
					
			        @if($cmd_details->count())
					 @php $cnt_lunette = $cmd_details->where('cmd_type','lunette')->all();@endphp
				      @if(count($cnt_lunette)>0)
				      <div style="margin-top:10px;">
							<p><u><b>{{__('GlassFrame Properties')}}</b></u></p>
							<table class="table-bordered table-sm" style="width:100%;">
							 <thead >
							   <tr>
								 <th>{{__('Name')}}</th>
								 <th>{{__('Model')}}</th>
								 <th>{{__('Color')}}</th>
								 <th>{{__('Chassis-A')}}</th>
								 <th>{{__('Height-B')}}</th>
								 <th>{{__('Diag-ED')}}</th>
								 <th>{{__('Point-DBL')}}</th>
								 <th>{{__('Material')}}</th>
							   </tr>
							 </thead>
							 <tbody>
								@foreach ($cmd_details as $c) 
								  @if($c->cmd_type=='lunette')	
									@php $lunette_specs = json_decode($c->item_specs,true);@endphp
									 
											<tr>
												<td>{{$c->item_name}}</td>
												<td>{{(isset($lunette_specs[1]) && $lunette_specs[1]!=null)?$lunette_specs[1]:''}}</td>
												<td>{{(isset($lunette_specs[2]) && $lunette_specs[2]!=null)?$lunette_specs[2]:''}}</td>
												<td>{{(isset($lunette_specs[3]) && $lunette_specs[3]!=null)?$lunette_specs[3]:''}}</td>
												<td>{{(isset($lunette_specs[4]) && $lunette_specs[4]!=null)?$lunette_specs[4]:''}}</td>
												<td>{{(isset($lunette_specs[5]) && $lunette_specs[5]!=null)?$lunette_specs[5]:''}}</td>
												<td>{{isset($lunette_specs[6])&&($lunette_specs[6]!=null)?$lunette_specs[6]:''}}</td>
												<td>{{isset($lunette_specs[0])&&($lunette_specs[0]!=null)?$lunette_specs[0]:'' }}</td>
											 </tr>
								   @endif
								@endforeach
				             </tbody>
						  </table>
					  </div> 
					  @endif
				     <div style="margin-top:10px;">
				        <table class="table-bordered table-sm" style="width:100%;">
									<thead >
										<tr>
											<th>{{__('Type')}}</th>
											<th>{{__('SKu')}}</th>
											<th>{{__('Name')}}</th>
											<th>{{__('Qty')}}</th>
											<th>{{__('Price')}}</th>
											<th>{{__('Discount')}}</th>
											<th>{{__('Total')}}</th>
											<th>{{__('T.Disc')}}</th>
											<th>{{__('Tax')}}</th>
										</tr>
									</thead>
								   <tbody>
								     @php
										$cpt = 1;
									@endphp
									@foreach ($cmd_details as $c) 
													<tr>
													<td style="white-space:nowrap;">
													@switch($c->cmd_type)
													  @case('rlens') {{__('Right lens')}} @break
													  @case('llens') {{__('Left lens')}} @break
													  @case('blens') {{__('Both lens')}} @break
													  @case('discount') {{__('Discount')}} @break
													  @case('lunette') {{__('Glass Frame')}} @break
													  @default {{__('')}}
													@endswitch
													</td>
													<td>
													@php $item = App\Models\TblInventoryItems::find($c->item_code);@endphp 
													{{$item->sku}}
													</td>
													<td>{{$c->item_name}}</td>
													<td style="text-align:right;">{{$c->qty}}</td>
													<td style="text-align:right;">{{number_format($c->price,2,'.',',')}}</td>
													<td style="text-align:right;">{{$c->discount}}</td>
													<td style="text-align:right;">{{number_format( (($c->qty)*($c->price))-($c->discount),2,'.',',')}}</td>
													<td>{{$c->tdiscount==__('undefined')?'':$c->tdiscount}}</td>
													<td>{{($c->tax=='Y')?__('Yes'):__('No')}}</td>
											       </tr>
											@php
											$cpt++;
											@endphp	 
									@endforeach
								   </tbody>
							</table> 					   
					</div>
					@endif
					
			<div style="overflow-x:auto;margin-top:15px;">
			  <div class="row">
				  <div class="col-md-12">
					<table class="table-bordered table-sm" style="margin-bottom:5px;width:100%;">
						<tbody>
					      <tr>
						    <th style="text-align:center;"><b>{{__('Balance')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Discount')}}</b></th>
						    <th style="text-align:center;"><b>{{__('Sub-total')}}</b></th>
						    <th style="text-align:center;"><b>{{__('QST')}}</b></th>
						    <th style="text-align:center;"><b>{{__('GST')}}</b></th>
							<th style="text-align:center;"><b>{{__('Total')}}</b></th>
							
						  </tr>
					      <tr>
						   <td style="text-align:right;">{{$cmd->total+$cmd->discount}}</td>
							<td style="text-align:right;">{{$cmd->discount}}</td>
							<td style="text-align:right;">{{$cmd->total}}</td>
							<td style="text-align:right;">{{$cmd->qst}}</td>
							<td style="text-align:right;">{{$cmd->gst}}</td>
							<td style="text-align:center;">{{$cmd->total+$cmd->qst+$cmd->gst}}</td>
						  </tr>
						</tbody>
					</table>
				  </div>
       			</div>
             </div>
				<div style="overflow-x:auto;margin-top:1px;margin-bottom:10px;">
					        <div style="margin-top:2px;float:left;width:61%;">
							    @if(isset($cmd->monture_note) && $cmd->monture_note!='')
									  <div class="row">
										<div class="col-md-12">
										  <label for="name" style="margin-bottom:2px;"><u><b>{{__('Note').'-'.__('GF.')}}</b></u></label>
										  <textarea class="no-brdr">{{$cmd->monture_note}}</textarea>
										</div>  
									  </div> 
						      @endif	
							  @if(isset($cmd->lens_note) && $cmd->lens_note!='')
										  <div class="row">
											<div class="col-md-12">
											  <label for="name" style="margin-bottom:2px;"><u><b>{{__('Note').'-'.__('Lens')}}</b></u></label>
											  <textarea class="no-brdr">{{$cmd->lens_note}}</textarea>
											</div>  
										  </div> 
							  @endif
								@if(isset($cmd->cmd_comment) && $cmd->cmd_comment!='')
								  <div class="row">
									<div class="col-md-12">
									  <label for="name" style="margin-bottom:2px;"><u><b>{{__('General Comment')}}</b></u></label>
									  <textarea class="no-brdr">{{$cmd->cmd_comment}}</textarea>
									</div>  
								  </div> 
					            @endif	
							   
							  <div style="clear:both;"></div>
							 
							</div>
							<div style="float:right;width:35%;">
								
								 <div>
								   @if($pay->count()>0)
								   <table class="table-bordered" style="margin-bottom:2px;width:100%;">
									 <thead>
									   <tr><th  colspan="2" scope="col" style="width:200px;text-align: center;">{{__('Paid amount')}}</th></tr>
									  </thead>
									  <tbody>
									  @php $sum_pay=0.00; @endphp	
								     @foreach($pay as $p)
										 <tr>
										 <td>{{$p->pay_name}}
										     @if(isset($p->pay_date))
											   <div style="font-size:10px;text-align: left;">{{$p->pay_date}}</div>
										     @endif
											 @if(isset($p->deposit) && $p->deposit=='Y')
											   <div style="font-size:10px;text-align: left;">{{__('Deposit')}}</div>
										     @endif
											 @if(isset($p->remark) && $p->remark!='')
											   <div style="font-size:10px;text-align: left;">{{__('Remark').' : '.$p->remark}}</div>
										     @endif
										 </td>
										  <td style="text-align: right;">{{$p->pay_amount}}</td>	 
										 </tr>
										 @php  $sum_pay+=floatval($p->pay_amount); @endphp	
										@endforeach
										<tr>
									      <td>{{__('Total')}}</td>
									      <td style="text-align: right;">{{number_format($sum_pay, 2, '.', '')}}</td>
										 
									    </tr>
									    
									</tbody>
									
								</table>
								@endif
								@if($ref->count()>0)
								  <table class="table-bordered" style="margin-bottom:2px;width:100%;">
									
									  <thead>
									  <tr><th  colspan="2" scope="col" style="width:200px;text-align: center;">{{__('Reimburse amount')}}</th></tr>
									  </thead>
									  <tbody>
											@php $sum_ref=0.00;@endphp
										    @foreach($ref as $r)
											 <tr>
											 <td>
											 {{$r->pay_name}}
											 @if(isset($r->pay_date))
											   <div style="font-size:10px;text-align: left;">{{$r->pay_date}}</div>
										     @endif
											 @if(isset($r->deposit) && $r->deposit=='Y')
											   <div style="font-size:10px;text-align: left;">{{__('Deposit')}}</div>
										     @endif
											 @if(isset($r->remark) && $r->remark!='')
											   <div style="font-size:10px;text-align: left;">{{__('Remark').' : '.$r->remark}}</div>
										     @endif
											 </td>
											 <td style="text-align: right;">{{$r->pay_amount}}</td>
											 
											 </tr>
											 @php  $sum_ref+=floatval($r->pay_amount); @endphp	
											@endforeach
											 <tr>
											  <td>{{__('Total')}}</td>
											  <td style="text-align: right;">{{number_format($sum_ref, 2, '.', '')}}</td>
											  
											 </tr>							    
									</tbody>
								</table>
								@endif
								<table class="table-bordered" style="margin-bottom:2px;width:100%;">
									<tbody>
									  <tr><th style="width:200px;text-align:center;"><b>{{__('Due Balance')}}</b></th></tr>
									  <tr>
									  	<td style="text-align:right;">{{$cmd->inv_balance}}</td>
									  </tr>
									</tbody>
								</table>
								 </div>
								
								<div style="clear:both;"></div>
							</div>
							<div style="clear:both;"></div>
					</div>
					
				@if($cmd_docs->count())			
			      @php $cnt=0; @endphp
			      @foreach($cmd_docs as $image)
				     <div style="page-break-before:always;margin-top:1px"> 
				      <img  class="img-fluid" alt="pic" src="{{$image->path}}" style="width:95%;"/>
				    </div>
		          @endforeach				  
				@endif
						
			</div>					
			<footer id="footer">
				            
							<div style="margin-bottom:15px;text-align: center;font-size:20px;">
							  <div><b>Patient :  {{$patient->first_name.' '.$patient->last_name}}</b></div>

							</div>
			</footer>
	    </body>
</html>
