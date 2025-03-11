<!--
   DEV APP
   Created date : 8-2-2023

-->
<div id="AR" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Auto refraction test')}}</b></u></div>
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
												<th scope="col" class="border ">{{__('Sphere')}}</th>
												<th scope="col" class="border ">{{__('Cylinder')}}</th>
												<th scope="col" class="border ">{{__('Axis')}}</th>
											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('Right')}}</th>
												<td id="tdRSPH" class="border">{{isset($AR) && isset($AR->right_sphere)?$AR->right_sphere:__('Undefined')}}</td>
												<td id="tdRCYL" class="border">{{isset($AR) && isset($AR->right_cylinder)?$AR->right_cylinder:__('Undefined')}}</td>
												<td id="tdRAXE" class="border">{{isset($AR) && isset($AR->right_axis)?$AR->right_axis:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Left')}}</th>
												<td id="tdLSPH" class="border">{{isset($AR) && isset($AR->left_sphere)?$AR->left_sphere:__('Undefined')}}</td>
												<td id="tdLCYL" class="border">{{isset($AR) && isset($AR->left_cylinder)?$AR->left_cylinder:__('Undefined')}}</td>
												<td id="tdLAXE" class="border">{{isset($AR) && isset($AR->left_axis)?$AR->left_axis:__('Undefined')}}</td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
							
							<div id="AR_outside_preview" class="row spotlight-group justify-content-center">
							
								@if(isset($AR) && isset($AR->image_path))
							       <div class="col-md-2 col-4 mb-1 txt-border">
											<a class="spotlight" data-title="{{$AR->image_name }}" 
												 href="{{ UserHelper::rp_txt($AR->image_path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($AR->thumb_image_path) }}" style="width:100%;height:100%;"/>
												</a>
											
									</div>
							    
							    @endif  
							</div>
							
						</div>
						
					
				</div>
            </div>