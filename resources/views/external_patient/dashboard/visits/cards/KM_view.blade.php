<!--
   DEV APP
   Created date : 25-2-2023

-->
<div id="KM" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Keratometer test')}}</b></u></div>
							<div class="card-tools">
							  <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							  </button>
							 
					     	</div> 
						</div>	
						<div class="card-body">
			                <div class="row table-responsive">
  							  <div class="col-md-12">
								 <table class="table table-borderless" style="width:100%;">
										<thead class="text-center">
											<tr>
												<th scope="col" class="border-left-0 border-top-0"></th>
												<th scope="col" class="border ">{{__('K1')}}</th>
												<th scope="col" class="border ">{{__('K2')}}</th>
												<th scope="col" class="border ">{{__('Axis')}}</th>
											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('Right')}}</th>
												<td id="KMRPOWER" class="border">{{isset($KM) && isset($KM->right_power)?$KM->right_power:__('Undefined')}}</td>
												<td id="KMRRADIUS" class="border">{{isset($KM) && isset($KM->right_radius)?$KM->right_radius:__('Undefined')}}</td>
												<td id="KMRAXIS" class="border">{{isset($KM) && isset($KM->right_axis)?$KM->right_axis:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Left')}}</th>
												<td id="KMLPOWER" class="border">{{isset($KM) && isset($KM->left_power)?$KM->left_power:__('Undefined')}}</td>
												<td id="KMLRADIUS" class="border">{{isset($KM) && isset($KM->left_radius)?$KM->left_radius:__('Undefined')}}</td>
												<td id="KMLAXIS" class="border">{{isset($KM) && isset($KM->left_axis)?$KM->left_axis:__('Undefined')}}</td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
							<div id="KM_outside_preview" class="row spotlight-group justify-content-center">
							
								@if(isset($KM) && isset($KM->image_path))
							       <div class="col-md-2 col-4 mb-1 txt-border">
											<a class="spotlight" data-title="{{$KM->image_name }}" 
												 href="{{ UserHelper::rp_txt($KM->image_path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($KM->thumb_image_path) }}" style="width:100%;height:100%;"/>
												</a>
											
									</div>
							    
							    @endif  
							</div>
						</div>
						
					
				</div>
            </div>