<!--
 DEV APP
 Created date : 3-3-2022  
 -->
 
 @if($cmd_cl->count())
	 <div  class="col-md-4">
	   <div class="card card-outline ">
		 <div class="card-body p-1">
			<select name="cmd_cl_select" id="cmd_cl_select" class="custom-select rounded-0"  size="5" style="font-size:0.85rem;">
			@foreach($cmd_cl as $c)
			  @php 
			  $cancelled_state = ($c->active=='N')?' - '.__('Cancelled'):''; 
			  $est_name = ($c->is_estimation=='Y')?' , '.__('Estimation'):'';
			  @endphp
			  <option value="{{$c->id}}">{{'#'.$c->id.' , '.Carbon\Carbon::parse($c->date_cmd)->format('Y-m-d H:i').$est_name.$cancelled_state}}</option>
			@endforeach
			</select>		
		 </div>
	   </div>
	 </div>  
	 @foreach($cmd_cl as $c)	
		@php
		   
		   $command_id = ' , #'.$c->id;
		   $right_rx_data = UserHelper::get_cmd_rx($c->right_rx_data);
		   $left_rx_data =  UserHelper::get_cmd_rx($c->left_rx_data);
		   $cmd_details= UserHelper::get_cmd_details($c->id);
	       $data = UserHelper::get_cmd_spec($c->id);
		   $cmd_opto = UserHelper::get_doctor_info($c->doctor_num);
		   $cl_name = ($c->is_estimation=='Y')?__('Estimation'):__('Contact Lens Order');
		@endphp		
		<div id="CMDCL-{{$c->id}}" class="card-cmd-cl col-md-8" style="display:none;">
		   <div class="card card-outline ">
				<div class="card-header">
					<div class="card-title">
					
					<u><b>{{$cl_name.$command_id}}</b></u>@if($c->active=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif
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
									    @if(isset($c->rxcl_id))
										<th>{{__('CL Rx')}}</th>
									    @endif
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
										@if(isset($c->rxcl_id))
										<td>{{'#'.$c->rxcl_id}}</td>
									    @endif
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
							  @if(count($right_rx_data) || count($left_rx_data))
							 <div class="mt-2 table-responsive col-md-12">
							   <table class="table-bordered table-sm" style="width:100%;">
								 <thead>
									<tr>
										<th></th>
										<th>{{__('Sph.')}}</th>
										<th>{{__('Cyl.')}}</th>
										<th>{{__('Axe')}}</th>
										<th>{{__('BC')}}</th>
										<th>{{__('DIA')}}</th>
										<th>{{__('ADD')}}</th>
										<th>{{__('Power')}}</th>
										
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
							    <div class="mt-2 table-responsive col-md-12">
							   <table class="table-borderless table-sm" style="width:100%;">
								 <thead>
									<tr>
									    <th></th>
										<th class='border text-center' colspan='4'>{{__('Keratometer')}}</th>
										<th class='border text-center' colspan='2'>{{__('Visual Acuity')}}</th>
									</tr>
									<tr>
										<th></th>
										<th class='border'>{{__('K1')}}</th>
										<th class='border'>{{__('K2')}}</th>
										<th class='border'>{{__('Axe')}}</th>
										<th class='border'>{{__('Dominant')}}</th>
										<th class='border'>{{__('Far')}}</th>
										<th class='border'>{{__('Near')}}</th>
										
									</tr>
								 </thead>
								 <tbody>
									<tr>
									  <td class='border'>{{__('OD')}}</td>
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['rpower'])?$right_rx_data['rpower']:''}}</td>
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['rradius'])?$right_rx_data['rradius']:''}}</td>
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['raxe1'])?$right_rx_data['raxe1']:''}}</td>
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['is_rdom']) && $right_rx_data['is_rdom']=='Y'?'D':''}}</td>
									  
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['rdeloin'])?$right_rx_data['rdeloin']:''}}</td>
									  <td class='border'>{{count($right_rx_data) && isset($right_rx_data['rdepres'])?$right_rx_data['rdepres']:''}}</td>
									  
									</tr>
									<tr>
									  <td class='border'>{{__('OS')}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['rpower'])?$left_rx_data['lpower']:''}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['rradius'])?$left_rx_data['lradius']:''}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['raxe1'])?$left_rx_data['laxe1']:''}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['is_ldom']) && $left_rx_data['is_ldom']=='Y'?'D':''}}</td>
									  
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['ldeloin'])?$left_rx_data['ldeloin']:''}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['ldepres'])?$left_rx_data['ldepres']:''}}</td>
									  
									</tr>
									<tr>
									  <td class='border'>{{__('OU')}}</td>
									  <td></td>
									  <td></td>
									  <td></td>
									  <td></td>
									  
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['bdeloin'])?$left_rx_data['bdeloin']:''}}</td>
									  <td class='border'>{{count($left_rx_data) && isset($left_rx_data['bdepres'])?$left_rx_data['bdepres']:''}}</td>
									  
									</tr>
								 </tbody>
							   </table>
							   </div>
							   @endif
								
							   
							   @if($cmd_details->count())
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
															  @case('rclens') {{__('Right')}} @break
															  @case('lclens') {{__('Left')}} @break
															  @case('bclens') {{__('Both')}} @break
															  @case('discount') {{__('Discount')}} @break
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
	<div id="CMDCL-NEW" class="mt-1 card-cmd-cl col-md-6">
		   <div class="card card-outline ">
				<div class="card-header">
					<div class="card-title">
				    	<u><b>{{__('Contact Lens Order')}}</b></u>
					   
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
