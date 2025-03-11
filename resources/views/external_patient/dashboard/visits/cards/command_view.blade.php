<!--
 DEV APP
 Created date : 1-9-2023  
 -->
 
 @if($cmd->count())
	 <div  class="col-md-4">
	   <div class="card card-outline ">
		 <div class="card-body p-1">
			<select name="cmd_select" id="cmd_select" class="custom-select rounded-0"  size="5" style="font-size:0.85rem;">
			@foreach($cmd as $c)
			   @php 
			   $cancelled_state = ($c->active=='N')?' - '.__('Cancelled'):'';
			   $est_name = $c->is_estimation=='Y'?' , '.__('Estimation'):'';
			   @endphp
		       <option value="{{$c->id}}">{{'#'.$c->id.' , '.Carbon\Carbon::parse($c->date_cmd)->format('Y-m-d H:i').$est_name.$cancelled_state}}</option>
			 
			@endforeach
			</select>		
		 </div>
	   </div>
	 </div>  
	 @foreach($cmd as $c)	
		@php
		  
		   $command_id = ' , #'.$c->id;
		   $right_rx_data = UserHelper::get_cmd_rx($c->right_rx_data);
		   $left_rx_data =  UserHelper::get_cmd_rx($c->left_rx_data);
		   $external_monture = isset($c->external_monture)?json_decode($c->external_monture):NULL;
		   $cmd_details= UserHelper::get_cmd_details($c->id);
	       //$data = UserHelper::get_cmd_spec($c->id);
		   //$lunette_specs =  isset($data) ? UserHelper::get_cmd_rx($data->item_specs): NULL;
		   $cmd_opto = UserHelper::get_doctor_info($c->doctor_num);
		   $gf_name = $c->is_estimation=='Y'? __('Estimation'):__('Glass Frame Order');
		@endphp		
		<div id="CMD-{{$c->id}}" class="card-cmd col-md-8" style="display:none;">
		   <div class="card card-outline ">
				<div class="card-header">
					<div class="card-title">
					  
					   <u><b>{{$gf_name.$command_id}}</b></u>@if($c->active=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif
					</div>
					<div class="card-tools">
					  
	                   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
						 <i class="fas fa-minus"></i>
					   </button>
					</div> 
				</div>	
				<div class="card-body p-1">
					<div class="row">
					  
						   <div class="table-responsive col-md-12">
							   <table class="table-bordered table-sm">
								 <thead>
								      <tr>
									    <th>{{__('Invoice Nb')}}</th>
										<th>{{__('Date')}}</th>
										<th>{{__('Resource')}}</th>
										@if($c->livrer=='Y')
										<th>{{__('Delivery')}}</th>
									    @endif
									  </tr>
								 </thead>
								 <tbody>
									
										<tr>
										<td>{{isset($c->clinic_inv_num)?$c->clinic_inv_num:__("Undefined")}}</td>
										<td>{{Carbon\Carbon::parse($c->date_cmd)->format('Y-m-d H:i')}}</td>
										<td>{{isset($cmd_opto)?$cmd_opto->first_name.' '.$cmd_opto->last_name:__("Undefined")}}</td>
										@if($c->livrer=='Y')
										<td>{{__('By').' : '.$c->livrer_user}}<br/>{{__('Date').' : '.Carbon\Carbon::parse($c->livrer_date)->format('Y-m-d H:i')}}</td>
									    @endif
										</tr>
								 </tbody>
							   </table>
							 </div>
							  @if(count($right_rx_data)>0 || count($left_rx_data)>0)
							 <div class="mt-2 table-responsive col-md-12">
							   <table class="table-bordered table-sm" style="width:100%;">
								 <thead>
									<tr>
										<th></th>
										<th>{{__('Sph.')}}</th>
										<th>{{__('Cyl.')}}</th>
										<th>{{__('Axe')}}</th>
										<th>{{__('V.A.')}}</th>
										<th>{{__('ADD')}}</th>
										<th>{{__('BC')}}</th>
										
									</tr>
								 </thead>
								 <tbody>
									<tr>
									  <td>{{__('OD')}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rsph'])?$right_rx_data['rsph']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rcyl'])?$right_rx_data['rcyl']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['raxe'])?$right_rx_data['raxe']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rav'])?$right_rx_data['rav']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['radd'])?$right_rx_data['radd']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rbc'])?$right_rx_data['rbc']:''}}</td>
									  
									</tr>
									<tr>
									  <td>{{__('OS')}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lsph'])?$left_rx_data['lsph']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lcyl'])?$left_rx_data['lcyl']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['laxe'])?$left_rx_data['laxe']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lav'])?$left_rx_data['lav']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['ladd'])?$left_rx_data['ladd']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lbc'])?$left_rx_data['lbc']:''}}</td>
									  
									</tr>
								 </tbody>
							   </table>
							   </div>
							    <div class="mt-2 table-responsive col-md-12">
							   <table class="table-bordered table-sm" style="width:100%;">
								 <thead>
									<tr>
										<th></th>
										
										<th>{{__('Vertex')}}</th>
										<th>{{__('Far P.D')}}</th>
										<th>{{__('Near P.D.')}}</th>
										<th>{{__('Prism')}}</th>
										<th>{{__('Base')}}</th>
										<th>{{__('Height')}}</th>
									</tr>
								 </thead>
								 <tbody>
									<tr>
									  <td>{{__('OD')}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rvertex'])?$right_rx_data['rvertex']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rdeloin'])?$right_rx_data['rdeloin']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rdepres'])?$right_rx_data['rdepres']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rprism'])?$right_rx_data['rprism']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rbase'])?$right_rx_data['rbase']:''}}</td>
									  <td>{{count($right_rx_data) && isset($right_rx_data['rheight'])?$right_rx_data['rheight']:''}}</td>

									</tr>
									<tr>
									  <td>{{__('OS')}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lvertex'])?$left_rx_data['lvertex']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['ldeloin'])?$left_rx_data['ldeloin']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['ldepres'])?$left_rx_data['ldepres']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lprism'])?$left_rx_data['lprism']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lbase'])?$left_rx_data['lbase']:''}}</td>
									  <td>{{count($left_rx_data) && isset($left_rx_data['lheight'])?$left_rx_data['lheight']:''}}</td>

									</tr>
								 </tbody>
							   </table>
							   </div>
							   @endif
							   @if(isset($external_monture))
								 <div class="mt-2 table-responsive col-md-12">
								  <p><u><b>{{__('External Item')}}</b></u></p>
								<table class="table-bordered table-sm">
								 <thead>
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
										<td>{{$external_monture[1]}}</td>
										<td>{{$external_monture[2]}}</td>
										<td>{{$external_monture[3]}}</td>
										<td>{{$external_monture[4]}}</td>
										<td>{{$external_monture[5]}}</td>
										<td>{{$external_monture[6]}}</td>
										<td>{{$external_monture[0]}}</td>
									 </tr>
								 </tbody>
							   </table>
							 </div>   
							   @endif		
							   
							   @if($cmd_details->count())
							   <div class="mt-2 table-responsive col-md-12">
										<p><u><b>{{__('GlassFrame Properties')}}</b></u></p>
										<table class="table-bordered table-sm">
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
															<td>{{($lunette_specs[1]!=null)?$lunette_specs[1]:''}}</td>
															<td>{{($lunette_specs[2]!=null)?$lunette_specs[2]:''}}</td>
															<td>{{($lunette_specs[3]!=null)?$lunette_specs[3]:''}}</td>
															<td>{{($lunette_specs[4]!=null)?$lunette_specs[4]:''}}</td>
															<td>{{($lunette_specs[5]!=null)?$lunette_specs[5]:''}}</td>
															<td>{{($lunette_specs[6]!=null)?$lunette_specs[6]:''}}</td>
															<td>{{$lunette_specs[0]}}</td>
														 </tr>
											   @endif
											@endforeach
										 </tbody>
									  </table>
								  </div>   
						       <div class="mt-2 table-responsive col-md-12">
							   <table class="table-bordered table-sm">
											<thead>
												<tr>
													<th>{{__('Type')}}</th>
													<th>{{__('Item')}}</th>
													<th>{{__('Item Name')}}</th>
													<th>{{__('Item Qty')}}</th>
													<th>{{__('Item Price')}}</th>
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
											@foreach ($cmd_details as $cd) 
															<tr>
															<td>
															@switch($cd->cmd_type)
															  @case('rlens') {{__('Right lens')}} @break
															  @case('llens') {{__('Left lens')}} @break
															  @case('blens') {{__('Both lens')}} @break
															  @case('discount') {{__('Discount')}} @break
															  @case('lunette') {{__('Glass Frame')}} @break
															  @default {{__('Undefined')}}
															@endswitch
															</td>
															<td>{{$cd->item_code}}</td>
															<td>{{$cd->item_name}}</td>
															<td>{{$cd->qty}}</td>
															<td>{{number_format($cd->price,2,'.',',')}}</td>
															<td>{{$cd->discount}}</td>
															<td>{{number_format( (($cd->qty)*($cd->price))-($cd->discount),2,'.',',')}}</td>
															<td>{{$cd->tdiscount}}</td>
															<td>{{$cd->tax}}</td>
														   </tr>
													@php
													$cpt++;
													@endphp	 
											@endforeach
										   </tbody>
									</table> 					   
						   </div>
						   @endif
						   
							  
					   
						   
					   
					 </div>								
				</div>
								
							
			</div>
		</div>					
        @endforeach
@else
   <!--new cmd-->
	<div id="CMD-NEW" class="card-cmd col-md-6 mt-1">
		   <div class="card card-outline ">
				<div class="card-header">
					<div class="card-title">
                        	
							<u><b>{{__('Glass Frame Order')}}</b></u>
					   
					</div>
					<div class="card-tools">
					  
					   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
						 <i class="fas fa-minus"></i>
					   </button>
					</div> 
				</div>	
				<div class="card-body p-1">
					<div class="row">
					 <div class="col-md-12">
					   <h5>{{__('Undefined')}}</h5>
					 </div>
					</div>
				</div>	
			</div>
      </div>			
@endif	
