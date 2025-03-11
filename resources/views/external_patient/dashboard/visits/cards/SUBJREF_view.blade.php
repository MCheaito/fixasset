<!--
   DEV APP
   Created date : 19-3-2023
 
-->
<div id="SUBJREF" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Subjective refraction test')}}</b></u></div>
							<div class="card-tools">
							  <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							  </button>
							 
					     	</div> 
						</div>	
						<div class="card-body">
			                 <div class="row table-responsive">
  							  <div class="col-md-12">
								 <table class="table table-borderless table-sm" style="width:100%;">
										<thead class="text-center">
											<tr>
												<th scope="col" class="border-left-0 border-top-0"></th>
												<th scope="col" class="border ">{{__('Sphere')}}</th>
												<th scope="col" class="border ">{{__('Cylinder')}}</th>
												<th scope="col" class="border ">{{__('Axis')}}</th>
												<th scope="col" class="border ">{{__('ADD')}}</th>
												<th scope="col" class="border ">{{__('DistantVA')}}</th>
												<th scope="col" class="border ">{{__('NearVA')}}</th
											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('Right')}}</th>
												<td id="SUBJREFRSPH" class="border">{{isset($SUBJREF)?$SUBJREF->right_sphere:__('Undefined')}}</td>
												<td id="SUBJREFRCYL" class="border">{{isset($SUBJREF)?$SUBJREF->right_cylinder:__('Undefined')}}</td>
												<td id="SUBJREFRAXE" class="border">{{isset($SUBJREF)?$SUBJREF->right_axis:__('Undefined')}}</td>
												<td id="SUBJREFRADD" class="border">{{isset($SUBJREF)?$SUBJREF->right_add1:__('Undefined')}}</td>
												<td id="SUBJREFRDVA" class="border">{{isset($SUBJREF)?$SUBJREF->right_distant_va:__('Undefined')}}</td>
												<td id="SUBJREFRNVA" class="border">{{isset($SUBJREF)?$SUBJREF->right_near_va:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Left')}}</th>
												<td id="SUBJREFLSPH" class="border">{{isset($SUBJREF)?$SUBJREF->left_sphere:__('Undefined')}}</td>
												<td id="SUBJREFLCYL" class="border">{{isset($SUBJREF)?$SUBJREF->left_cylinder:__('Undefined')}}</td>
												<td id="SUBJREFLAXE" class="border">{{isset($SUBJREF)?$SUBJREF->left_axis:__('Undefined')}}</td>
												<td id="SUBJREFLADD" class="border">{{isset($SUBJREF)?$SUBJREF->left_add1:__('Undefined')}}</td>
												<td id="SUBJREFLDVA" class="border">{{isset($SUBJREF)?$SUBJREF->left_distant_va:__('Undefined')}}</td>
												<td id="SUBJREFLNVA" class="border">{{isset($SUBJREF)?$SUBJREF->left_near_va:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('Both')}}</th>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td id="SUBJREFBDVA" class="border">{{isset($SUBJREF)?$SUBJREF->both_distant_va:__('Undefined')}}</td>
												<td id="SUBJREFBNVA" class="border">{{isset($SUBJREF)?$SUBJREF->both_near_va:__('Undefined')}}</td>
											</tr>
										</tbody>
									</table>
								</div>
                            </div>
                            <div  class="row table-responsive mt-2">
							  <div   class="col-md-12">
							   
										 <table class="table table-borderless table-sm" style="width:100%;">
											<thead class="text-center">
												<tr>
												   <th colspan="7" scope="col" class="border txt-bg text-white">{{__("Final refraction")}}</th>
												</tr>
												<tr>
													<th scope="col" class="border-right-bottom"></th>
													<th scope="col" class="border">{{__('Sphere')}}</th>
													<th scope="col" class="border">{{__('Cylinder')}}</th>
													<th scope="col" class="border">{{__('Axis')}}</th>
													<th scope="col" class="border">{{__('ADD')}}</th>
													<th scope="col" class="border">{{__('DistantVA')}}</th>
													<th scope="col" class="border">{{__('NearVA')}}</th>
												</tr>
											</thead>
											<tbody class="text-center">
												<tr>
													<th scope="row" class="border">{{__('Right')}}</th>
													<td id="frefrsph" class="border">{{isset($FREF)?$FREF->right_sphere:''}}</td>
													<td id="frefrcyl" class="border">{{isset($FREF)?$FREF->right_cylinder:''}}</td>
													<td id="frefraxe" class="border">{{isset($FREF)?$FREF->right_axis:''}}</td>
													<td id="frefradd" class="border">{{isset($FREF)?$FREF->right_add1:''}}</td>
													<td id="frefrloin" class="border">{{isset($FREF)?$FREF->right_distant_va:''}}</td>
													<td id="frefrpres" class="border">{{isset($FREF)?$FREF->right_near_va:''}}</td>

												</tr>
												<tr>
													<th scope="row" class="border">{{__('Left')}}</th>
													<td id="freflsph" class="border">{{isset($FREF)?$FREF->left_sphere:''}}</td>
													<td id="freflcyl" class="border">{{isset($FREF)?$FREF->left_cylinder:''}}</td>
													<td id="freflaxe" class="border">{{isset($FREF)?$FREF->left_axis:''}}</td>
													<td id="frefladd" class="border">{{isset($FREF)?$FREF->left_add1:''}}</td>
													<td id="freflloin" class="border">{{isset($FREF)?$FREF->left_distant_va:''}}</td>
													<td id="freflpres" class="border">{{isset($FREF)?$FREF->left_near_va:''}}</td>

												</tr>
												
												<tr>
													<th scope="row" class="border">{{__('Both')}}</th>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td id="frefbloin" class="border">{{isset($FREF)?$FREF->both_distant_va:''}}</td>
													<td id="frefbpres" class="border">{{isset($FREF)?$FREF->both_near_va:''}}</td>

												</tr>
											</tbody>
										</table>
								       
							  </div>
					        </div>							
						</div>
					
				</div>
            </div>