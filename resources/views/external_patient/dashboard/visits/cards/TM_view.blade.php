<!--
   DEV APP
   Created date : 2-3-2023
 
-->
<div id="TM" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Tonometer test')}}</b></u></div>
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
												<th scope="col" class="border ">{{__('Tension')}}</th>
												<th scope="col" class="border ">{{__('Pachymetry')}}</th>

											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('Right')}}</th>
												<td id="TMRMMHG" class="border">{{isset($TM) && isset($TM->right_iop_mmhg)?$TM->right_iop_mmhg:__('Undefined')}}</td>
												<td id="TMRPA" class="border">{{isset($TM) && isset($TM->right_iop_pa)?$TM->right_iop_pa:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Left')}}</th>
												<td id="TMLMMHG" class="border">{{isset($TM) && isset($TM->left_iop_mmhg)?$TM->left_iop_mmhg:__('Undefined')}}</td>
												<td id="TMLPA" class="border">{{isset($TM) && isset($TM->left_iop_pa)?$TM->left_iop_pa:__('Undefined')}}</td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
                            <div id="TM_outside_preview" class="row spotlight-group justify-content-center">
							
								@if(isset($TM) && isset($TM->image_path))
							       <div class="spotlight-group col-md-2 col-4 mb-1 txt-border">
											<a class="spotlight" data-title="{{$TM->image_name }}" 
												 href="{{ UserHelper::rp_txt($TM->image_path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($TM->thumb_image_path) }}" style="width:100%;height:100%;"/>
												</a>
											
									</div>
							    
							    @endif  
							</div>							
						</div>
						
					
				</div>
            </div>								