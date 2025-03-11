<!--
   DEV APP
   Created date : 17-2-2023

-->
<div id="LM" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Lensometer test')}}</b></u></div>
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
												<th scope="col" class="border ">{{__('Add2')}}</th>
												<th scope="col" class="border ">{{__('Add1')}}</th>
											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('Right')}}</th>
												<td id="LMRSPH" class="border">{{isset($LM) && isset($LM->right_sphere)?$LM->right_sphere:__('Undefined')}}</td>
												<td id="LMRCYL" class="border">{{isset($LM) && isset($LM->right_cylinder)?$LM->right_cylinder:__('Undefined')}}</td>
												<td id="LMRAXE" class="border">{{isset($LM) && isset($LM->right_axis)?$LM->right_axis:__('Undefined')}}</td>
												<td id="LMRADD2" class="border">{{isset($LM) && isset($LM->right_add2)?$LM->right_add2:__('Undefined')}}</td>
												<td id="LMRADD1" class="border">{{isset($LM) && isset($LM->right_add1)?$LM->right_add1:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Left')}}</th>
												<td id="LMLSPH" class="border">{{isset($LM) && isset($LM->left_sphere)?$LM->left_sphere:__('Undefined')}}</td>
												<td id="LMLCYL" class="border">{{isset($LM) && isset($LM->left_cylinder)?$LM->left_cylinder:__('Undefined')}}</td>
												<td id="LMLAXE" class="border">{{isset($LM) && isset($LM->left_axis)?$LM->left_axis:__('Undefined')}}</td>
												<td id="LMLADD2" class="border">{{isset($LM) && isset($LM->left_add2)?$LM->left_add2:__('Undefined')}}</td>
												<td id="LMLADD1" class="border">{{isset($LM) && isset($LM->left_add1)?$LM->left_add1:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('BD')}}</th>
												<td id="LMBD" class="border">{{isset($LM) && isset($LM->binocular_distance)?$LM->binocular_distance:__('Undefined')}}</td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
							<div id="LM_outside_preview" class="row spotlight-group justify-content-center">
							
								@if(isset($LM) && isset($LM->image_path))
							       <div class="spotlight-group col-md-2 col-4 mb-1 txt-border">
											<a class="spotlight" data-title="{{$LM->image_name }}" 
												 href="{{ UserHelper::rp_txt($LM->image_path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($LM->thumb_image_path) }}" style="width:100%;height:100%;"/>
												</a>
											
									</div>
							    
							    @endif  
							</div>
						</div>
						
					
				</div>
            </div>