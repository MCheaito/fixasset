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
			
		</head>
	    <body style="font-size:14px;">	
            <div style="width:100%;">
	            <div>
                      <div class="top_line_cmd">
                            <div style="float:left;margin:20px;margin-top:1px;margin-bottom:1px;">
                              <div><b>{{isset($from_warranty)? $from_warranty.','.__('Cl. Order').' '.'#'.$cmd->id:__('Cl. Order').' '.'#'.$cmd->id}} </b></div>
							  <div>{!! DNS1D::getBarcodeHTML(strval($cmd->id), 'C128',2,22) !!}</div>
							  <div><b>{{__("Date")}}:</b>&#xA0;{{Carbon\Carbon::parse($cmd->date_cmd)->format('Y-m-d')}}</div>
							  @if(isset($cmd->rxcl_id))<div><b>{{__('CL Rx')}}:</b> {{'#'.$cmd->rxcl_id}}</div>@endif
							  
							</div>
                            <div style="float:right;margin:20px;margin-top:1px;margin-bottom:1px;">
                              <div><b>{{__('Visit').' : '}}</b> {{'#'.$cmd->visit_num}}</div>
							  @if(isset($clinic_inv_num) && $clinic_inv_num!='')
							   <div><b>{{__('Invoice')}}:</b> {{$clinic_inv_num}}</div>	  
							  @endif
							  @if(isset($cmd->expected_delivery))
							   <div><b>{{__('Expected Delivery')}}:</b>&#xA0;{{$cmd->expected_delivery}}</div>
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
							  @if(isset($cmd->representant) && $cmd->representant!='')
							   <div><b>{{__('Representant')}}:</b> {{UserHelper::getRepresentant($cmd->representant)}}</div>	
						      @endif
                            </div>
                            <div style="clear:both;"></div>
                      </div>
                      <div>
                          
						  <div style="float:left;width:60%">
                            <div style="float:left;margin-left:10px;margin-top:0px;">
                               @if(isset($doctor))<div><b>{{$doctor->first_name.' '.$doctor->last_name.(isset($doctor->doc_title)?' '.$doctor->doc_title:'')}}</b></div>@endif
								<div><b>{{$clinic->full_name}}</b></div>   
							   @if(isset($supplier->num_compte))<div><b>{{__("Account Nb")}} :</b> {{$supplier->num_compte}}</div>@endif
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
                                    @if(isset($supplier)) 
									 <div><b>{{__("Supplier")}} :  {{isset($supplier->name)?$supplier->name:__("Undefined")}}</b></div>
									 @if(isset($supplier->addresse) && $supplier->addresse!='')<div>{{__("Address")}} : {{$supplier->addresse}}</div>@endif
                                     @if(isset($supplier->tel) && $supplier->tel!='')<div>Tel. :  {{$supplier->tel}}</div>@endif
									 @if(isset($supplier->fax) && $supplier->fax!='')<div>Fax. : {{$supplier->fax}}</div>@endif
									@endif
									@php $pat = App\Models\Patient::find($cmd->patient_id);@endphp
								    @if(isset($pat)) <div style="margin-top:10px;"><b>{{__("Patient")}}:</b>&#xA0;{{$pat->first_name.' '.$pat->last_name.' ( '.$patient->id.' )'}}</div>@endif
                               </div>
							  
                                <div style="clear:both;"></div>
                            </div>
                            <div style="clear:both;" />
                         
					  </div>	  
					  			
                </div>	
			    
				@if(count($right_rx_data) || count($left_rx_data))
				
			     @if(isset($right_rx_data['rsph'])   || isset($right_rx_data['rcyl']) 
					 || isset($right_rx_data['raxe'])|| isset($right_rx_data['rbc'])
					 || isset($right_rx_data['rdia']) || isset($right_rx_data['radd'])
					 || isset($right_rx_data['rdom'])  || isset($left_rx_data['lsph']) 
					 || isset($left_rx_data['lcyl']) || isset($left_rx_data['laxe']) 
					 || isset($left_rx_data['lbc']) || isset($left_rx_data['ldia']) 
					 || isset($left_rx_data['ladd'])  || isset($left_rx_data['ldom']))
				<div style="margin-top:10px;">
 				        
				            			<table class="table-bordered" style="width:100%;">
												 <thead>
													<tr>
														<th class="tbl_head">{{__('Eye')}}</th>
														<th class="tbl_head">{{__('Sphere')}}</th>
														<th class="tbl_head">{{__('Cylinder')}}</th>
														<th class="tbl_head">{{__('Axis')}}</th>
														<th class="tbl_head">{{__('BC')}}</th>
													    <th class="tbl_head">{{__('DIA')}}</th>
														<th class="tbl_head">{{__('ADD')}}</th>
														<th class="tbl_head">{{__('Power')}}</th>
													</tr>
												 </thead>
												 <tbody>
													<tr>
													  <td>{{__('OD')}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rsph'])?$right_rx_data['rsph']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rcyl'])?$right_rx_data['rcyl']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['raxe'])?$right_rx_data['raxe']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rbc'])?$right_rx_data['rbc']:''}}</td>
												      <td>{{count($right_rx_data) && isset($right_rx_data['rdia'])?$right_rx_data['rdia']:''}}</td>
												  	  <td>{{count($right_rx_data) && isset($right_rx_data['radd'])?$right_rx_data['radd']:''}}</td>
													  <td>{{count($right_rx_data) && isset($right_rx_data['rdom'])?__($right_rx_data['rdom']):''}}</td>

													</tr>
													<tr>
													  <td>{{__('OS')}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lsph'])?$left_rx_data['lsph']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lcyl'])?$left_rx_data['lcyl']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['laxe'])?$left_rx_data['laxe']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['lbc'])?$left_rx_data['lbc']:''}}</td>
												      <td>{{count($left_rx_data) && isset($left_rx_data['ldia'])?$left_rx_data['ldia']:''}}</td>
												 	  <td>{{count($left_rx_data) && isset($left_rx_data['ladd'])?$left_rx_data['ladd']:''}}</td>
													  <td>{{count($left_rx_data) && isset($left_rx_data['ldom'])?__($left_rx_data['ldom']):''}}</td>
													</tr>
												 </tbody>
											   </table>
										 
								</div>
						 @endif		
						 @if(isset($right_rx_data['rpower'])   || isset($right_rx_data['rradius']) 
						   || isset($right_rx_data['raxe1'])|| isset($right_rx_data['is_rdom'])
						   || isset($right_rx_data['rdeloin']) || isset($right_rx_data['rdepres'])
						   || isset($right_rx_data['bdeloin'])  || isset($left_rx_data['bdepres']) 
						   || isset($left_rx_data['lpower']) || isset($left_rx_data['lradius']) 
						   || isset($left_rx_data['laxe1'])  || isset($left_rx_data['is_ldom']) 
						   || isset($left_rx_data['ldeloin'])  || isset($left_rx_data['ldepres']))	
								
								
								<div style="margin-top:10px;">
								  <table class="table-bordered" style="width:100%;">
											 <thead>
												<tr>
												  <th class="tbl_head text-center" colspan="5">{{__('Keratometer')}}</th>
												  <th class="tbl_head text-center" colspan="2">{{__('Visual Acuity')}}</th> 												  
												</tr>
												<tr>
													<th class="text-center">{{__('Eye')}}</th>
													<th class="text-center">{{__('K1')}}</th>
													<th class="text-center">{{__('K2')}}</th>
													<th class="text-center">{{__('Axis')}}</th>
													<th class="text-center">{{__('Dominant')}}</th>
													<th class="text-center">{{__('Far')}}</th>
													<th class="text-center">{{__('Near')}}</th>
													
													
												</tr>
											 </thead>
											 <tbody>
												<tr>
												  <td>{{__('OD')}}</td>
												  <td>{{count($right_rx_data) && isset($right_rx_data['rpower'])?$right_rx_data['rpower']:''}}</td>
												  <td>{{count($right_rx_data) && isset($right_rx_data['rradius'])?$right_rx_data['rradius']:''}}</td>
												  <td>{{count($right_rx_data) && isset($right_rx_data['raxe1'])?$right_rx_data['raxe1']:''}}</td>
												  <td>{{count($right_rx_data) && isset($right_rx_data['is_rdom']) && $right_rx_data['is_rdom']=='Y'?'D':''}}</td>
												 
												  <td>{{count($right_rx_data) && isset($right_rx_data['rdeloin'])?$right_rx_data['rdeloin']:''}}</td>
												  <td>{{count($right_rx_data) && isset($right_rx_data['rdepres'])?$right_rx_data['rdepres']:''}}</td>
												 
												</tr>
												<tr>
												  <td>{{__('OS')}}</td>
												   <td>{{count($left_rx_data) && isset($left_rx_data['lpower'])?$left_rx_data['lpower']:''}}</td>
												  <td>{{count($left_rx_data) && isset($left_rx_data['lradius'])?$left_rx_data['lradius']:''}}</td>
												  <td>{{count($left_rx_data) && isset($left_rx_data['laxe1'])?$left_rx_data['laxe1']:''}}</td>
												  <td>{{count($left_rx_data) && isset($left_rx_data['is_ldom']) && $left_rx_data['is_ldom']=='Y'?'D':''}}</td>
												 
												  <td>{{count($left_rx_data) && isset($left_rx_data['ldeloin'])?$left_rx_data['ldeloin']:''}}</td>
												  <td>{{count($left_rx_data) && isset($left_rx_data['ldepres'])?$left_rx_data['ldepres']:''}}</td>
												  
												</tr>
												<tr>
												   <td>{{__('OU')}}</td>
												   <td colspan="4" style="border:0px;"></td>
												   <td>{{count($left_rx_data) && isset($left_rx_data['bdeloin'])?$left_rx_data['bdeloin']:''}}</td>
												  <td>{{count($left_rx_data) && isset($left_rx_data['bdepres'])?$left_rx_data['bdepres']:''}}</td>
												  
												</tr>
											 </tbody>
										   </table>
								  
					            </div>
					           @endif
					
					@endif
					
			        @if($cmd_details->count() || (isset($cmd->cmd_comment) && $cmd->cmd_comment!=''))
					<div style="margin-top:1em;">
				        @if($cmd_details->count())
						<table class="table-bordered table-sm" style="margin-bottom:5px;width:100%">
									<thead>
										<tr>
											<th>{{__('Type')}}</th>
											<th>{{__('SKu')}}</th>
											<th>{{__('Name')}}</th>
											<th>{{__('Qty')}}</th>
											<th>{{__('Properties')}}</th>
											
										</tr>
									</thead>
								   <tbody>
								     @php
										$cpt = 1;
									@endphp
									@foreach ($cmd_details as $c) 
											@if($c->cmd_type !='discount')		
													<tr>
													<td style="white-space:nowrap;">
													@switch($c->cmd_type)
													  @case('rclens') {{__('Right')}} @break
													  @case('lclens') {{__('Left')}} @break
													  @case('bclens') {{__('Both')}} @break
													  @default {{__('')}}
													@endswitch
													</td>
													<td>
													@php $item = App\Models\TblInventoryItems::find($c->item_code);@endphp 
													{{$item->sku}}
													</td>
													<td>{{$c->item_name}}</td>
													<td style="text-align:right;">{{$c->qty}}</td>
													<td style="font-size:10px;">
													@if(isset($c->item_specs))
													  @php $specs = json_decode($c->item_specs); @endphp
												      @if($specs[1]!='')<div>{{__('Curve-X').' : '.$specs[1]}}</div>@endif
													  @if($specs[2]!='')<div>{{__('Curve-Y').' : '.$specs[2]}}</div>@endif
													  @if($specs[3]!='')<div>{{__('Diameter').' : '.$specs[3]}}</div>@endif
													  @if($specs[4]!='')<div>{{__('Cl-Color').' : '.$specs[4]}}</div>@endif
													  @if($specs[0]!='')<div>{{__('Material').' : '.$specs[0]}}</div>@endif	
													@endif	
													</td>
													
											       </tr>
											@php
											$cpt++;
											@endphp	
                                           @endif 											
									@endforeach
								   </tbody>
							</table>
							@endif
                            @if(isset($cmd->cmd_comment) && $cmd->cmd_comment!='')
								  <div class="row">
									<div class="col-md-12">
									  <label for="name" style="margin-bottom:2px;"><u><b>{{__('General Comment')}}</b></u></label>
									  <textarea class="no-brdr">{{$cmd->cmd_comment}}</textarea>
									</div>  
								  </div> 
					       @endif								
					</div>
					@endif
					
			</div>					
			<footer id="footer">
				            
				
			</footer>
	    </body>
</html>
