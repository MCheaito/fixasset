<!--
   DEV APP
   Created date : 21-5-2023
-->

 @if($rxcls->count())
       <div  class="col-md-4">
	   <div class="card card-outline ">
		 <div class="card-body p-1">
			<select name="rxcl_select" id="rxcl_select" class="label-size custom-select rounded-0"  size="5">
			@foreach($rxcls as $rx)
			 @php $cancelled_state =($rx->active=='N')?' - '.__('Cancelled'):''; @endphp
			 <option value="{{$rx->id}}">{{'#'.$rx->id.' - '.$rx->rx_type.' , '.Carbon\Carbon::parse($rx->datein)->format('Y-m-d H:i').$cancelled_state}}</option>
			@endforeach
			</select>		
		 </div>
	   </div>
	 </div>  
     @foreach($rxcls as $rx)	 
	  @php
		   $rx_id = ' , #'.$rx->id;
		  
		@endphp		
	  <div id="RXCL-{{$rx->id}}" class="card-rxcl col-md-8">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Prescription CL Rx').$rx_id}}</b></u>@if($rx->active=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif</div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
						   	</div> 
						</div>	
						<div class="card-body">
			                 
							 <div class="row table-responsive">
								<div class="col-md-12">
								  <table class="table table-bordered table-sm" style="width:100%;">
									<tr>
									  <th>{{__("CL Right")}}</th>
									  <th>{{__("CL Left")}}</th>
									  @if(isset($rx->reason) && $rx->reason!='')
									  <th>{{__("Principal cause")}}</th>
									  @endif
									  @if(isset($rx->notes) && $rx->notes!='')
									  <th>{{__("General notes")}}</th>
								      @endif
									  @if(isset($rx->prop_notes) && $rx->prop_notes!='')
									  <th>{{__("Remarks")}}</th>
								      @endif
									</tr>
									<tr>
									   <td>{{UserHelper::get_article($rx->ritem)}}</td>
									   <td>{{UserHelper::get_article($rx->litem)}}</td>
									    @if(isset($rx->reason) && $rx->reason!='')
									     <td>{{$rx->reason}}
								        @endif
										@if(isset($rx->notes) && $rx->notes!='')
									     <td>{{$rx->notes}}
								        @endif
										@if(isset($rx->prop_notes) && $rx->prop_notes!='')
									     <td>{{$rx->prop_notes}}
								        @endif
									</tr>
								  </table>	
								</div>
							 </div>
							
							<div class="row table-responsive">
							<div class="col-md-12">
										  <legend class ='border text-center label-size'><b>{{__('Eye Health')}}</b></legend>
										  <div  class="table-responsive  mt-1">   
											<table class="table table-bordered table-sm" style="width:100%;">
											  <tr>
											    <th>{{__("Blood Pressure")}}</th>
												<th>{{__("MDA")}}</th>
												<th>{{__("Drugs")}}</th>
												<th>{{__("Surgery")}}</th>
												<th>{{__("Eye Injury")}}</th>
												<th>{{__('Diabetes')}}</th>
												<th>{{__('Eye Allergy')}}</th>
												<th>{{__('Glaucome')}}</th>
											  </tr>
											  <tr>
											    <td>{{isset($rx)?$rx->arterial:__("Undefined")}}</td>
												<td>{{isset($rx) && $rx->dmla=='Y'?__("Yes"):__("No")}}</td>
											    <td>{{isset($rx) && $rx->medication=='Y'?__("Yes"):__("No")}}</td>
												<td>{{isset($rx) && $rx->chirugie=='Y'?__("Yes"):__("No")}}</td>
											  	<td>{{isset($rx) && $rx->blessure=='Y'?__("Yes"):__("No")}}</td>
											  	<td>{{isset($rx) && $rx->diab=='Y'?__("Yes"):__("No")}}</td>
											    <td>{{isset($rx) && $rx->allergy=='Y'?__("Yes"):__("No")}}</td>
											  	<td>{{isset($rx) && $rx->glaucome=='Y'?__("Yes"):__("No")}}</td>
											  </tr>
											  
											  
											</table>
										  </div>	
										</div>
									</div>
							<div class="row table-responsive">
  							  <div class="col-md-12">
								 <table class="table table-borderless" style="width:100%;">
										<thead class="text-center">
											<tr>
												<th scope="col" class="border-left-0 border-top-0"></th>
												<th scope="col" class="border ">{{__('Sphere')}}</th>
												<th scope="col" class="border ">{{__('Cylinder')}}</th>
												<th scope="col" class="border ">{{__('Axis')}}</th>
												<th scope="col" class="border ">{{__('BC')}}</th>
												<th scope="col" class="border ">{{__('DIA')}}</th>
												<th scope="col" class="border ">{{__('ADD')}}</th>
												<th scope="col" class="border">{{__('Power')}}</th>

											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('OD')}}</th>
												<td id="odsph" class="border">{{isset($rx)?$rx->odsph:__('Undefined')}}</td>
												<td id="odcyl" class="border">{{isset($rx)?$rx->odcyl:__('Undefined')}}</td>
												<td id="odaxe" class="border">{{isset($rx)?$rx->odaxe:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->odbc:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->oddia:__('Undefined')}}</td>
												<td id="oddva" class="border">{{isset($rx)?$rx->odadd:__('Undefined')}}</td>
												<td id="odav" class="border">{{isset($rx)?__($rx->oddom):__('Undefined')}}</td>

											</tr>
											<tr>
												<th scope="row" class="border">{{__('OS')}}</th>
												<td id="ogsph" class="border">{{isset($rx)?$rx->ogsph:__('Undefined')}}</td>
												<td id="ogcyl" class="border">{{isset($rx)?$rx->ogcyl:__('Undefined')}}</td>
												<td id="ogaxe" class="border">{{isset($rx)?$rx->ogaxe:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->ogbc:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->ogdia:__('Undefined')}}</td>
												<td id="ogdva" class="border">{{isset($rx)?$rx->ogadd:__('Undefined')}}</td>
												<td id="ogav" class="border">{{isset($rx)?__($rx->ogdom):__('Undefined')}}</td>

											</tr>
										</tbody>
									</table>
								</div>
                            </div>
								
							<div class="row table-responsive">
  							  <div class="col-md-12">
								 <table class="table table-borderless" style="width:100%;">
										<thead class="text-center">
											<tr>
											 <th></th>
											 <th colspan='4' class="border">{{__("Keratometer")}}</th>
											 <th colspan='2' class="border border-left-0">{{__("Visual Acuity")}}</th>
											</tr>
											<tr>
												<th scope="col" class="border-left-0 border-top-0"></th>
												
												<th scope="col" class="border ">{{__('K1')}}</th>
												<th scope="col" class="border ">{{__('K2')}}</th>
												<th scope="col" class="border ">{{__('Axe')}}</th>
												<th scope="col" class="border ">{{__('Dominant')}}</th>
												<th scope="col" class="border ">{{__('Far')}}</th>
												<th scope="col" class="border ">{{__('Near')}}</th>
											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('OD')}}</th>
												
												<td  class="border">{{isset($rx)?$rx->rpower:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->rradius:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->raxe:__('Undefined')}}</td>
												<td  class="border">{{isset($rx) && $rx->rdom=='Y'?'D':''}}</td>
											    <td  class="border">{{isset($rx)?$rx->oddeloin:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->oddepres:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('OS')}}</th>
												
												<td  class="border">{{isset($rx)?$rx->lpower:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->lradius:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->laxe:__('Undefined')}}</td>
												<td  class="border">{{isset($rx) && $rx->ldom=='Y'?'D':''}}</td>
											    <td  class="border">{{isset($rx)?$rx->ogdeloin:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->ogdepres:__('Undefined')}}</td>
											</tr>
											<tr>
												<th scope="row" class="border">{{__('OU')}}</th>
												<td colspan="4"></td>
												<td  class="border">{{isset($rx)?$rx->oudeloin:__('Undefined')}}</td>
												<td  class="border">{{isset($rx)?$rx->oudepres:__('Undefined')}}</td>
											</tr>
											
										</tbody>
									</table>
								</div>
							 
                            </div>
							<div class="row table-responsive">
							  <div class="col-md-12">
							     <table class="table table-bordered table-sm" style="width:100%;">
									<thead class="text-center">
									  <tr>
									   <th class="border-0"></th>
									   <th>{{__('Vision condition')}}</th>
									   <th>{{__('Comfort condition')}}</th>
									   <th>{{__('CL Centration')}}</th>
									   <th>{{__('CL Movement')}}</th>
									   <th>{{__('CL Rotation')}}</th>
									  </tr>
									</thead>
									<tbody>
										<tr>
											<th>{{__('OD')}}</th>
											<td>
											  @switch($rx->rvision)
											     @case('good') {{__("Good")}} @break
												 @case('not_good') {{__("Not Good")}} @break
												 @default {{__("Undefined")}} 
											    @endswitch
											</td>
											<td>
											   @switch($rx->rconfort)
											     @case('good') {{__("Good")}} @break
												 @case('not_good') {{__("Not Good")}} @break
												 @default {{__("Undefined")}} 
											    @endswitch
											</td>
											<td>
											   @switch($rx->rcentration)
											     @case('cc') CC @break
												 @case('cb') CB @break
												 @case('bc') BC @break
												 @case('hc') HC @break
												 @case('ch') CH @break
												 @default __("Undefined") 
											    @endswitch
											</td>
											<td>
											   @switch($rx->rmovement)
											     @case('tight') {{__("Tight")}} @break
												 @case('good') {{__("Good")}} @break
												 @case('big') {{__("Big")}} @break
												 @default {{__("Undefined")}}  
											    @endswitch
											</td>
											<td>
											   @switch($rx->rrotation)
											     @case('0') {{__("0")}} @break
												 @case('5N') {{__("5N")}} @break
												 @case('5T') {{__("5T")}} @break
												 @case('10N') {{__("10N")}} @break
												 @case('10T') {{__("10T")}} @break
												 @case('15N') {{__("15N")}} @break
												 @case('15T') {{__("15T")}} @break
												 @case('20N') {{__("20N")}} @break
												 @case('20T') {{__("20T")}} @break
												 @default __("Undefined") 
											    @endswitch
											</td>
										</tr>
										<tr>
											<th>{{__('OS')}}</th>
											<td>
											   @switch($rx->lvision)
											     @case('good') {{__("Good")}} @break
												 @case('not_good') {{__("Not Good")}} @break
												 @default {{__("Undefined")}}  
											    @endswitch
											</td>
											<td>
											   @switch($rx->lconfort)
											     @case('good') {{__("Good")}} @break
												 @case('not_good') {{__("Not Good")}} @break
												 @default {{__("Undefined")}}  
											    @endswitch
											</td>
											<td>
											   @switch($rx->lcentration)
											      @case('cc') CC @break
												 @case('cb') CB @break
												 @case('bc') BC @break
												 @case('hc') HC @break
												 @case('ch') CH @break
												 @default __("Undefined") 
											    @endswitch
											</td>
											<td>
											   @switch($rx->lmovement)
											     @case('tight') {{__("Tight")}} @break
												 @case('good') {{__("Good")}} @break
												 @case('big') {{__("Big")}} @break
												 @default {{__("Undefined")}}  
											    @endswitch
											</td>
											<td>
											   @switch($rx->lrotation)
											     @case('0') {{__("0")}} @break
												 @case('5N') {{__("5N")}} @break
												 @case('5T') {{__("5T")}} @break
												 @case('10N') {{__("10N")}} @break
												 @case('10T') {{__("10T")}} @break
												 @case('15N') {{__("15N")}} @break
												 @case('15T') {{__("15T")}} @break
												 @case('20N') {{__("20N")}} @break
												 @case('20T') {{__("20T")}} @break
												 @default __("Undefined") 
											    @endswitch
											</td>
											
                                        </tr>											
									</tbody>
								</table>
							  </div>
							</div>
							
                            
							<div class="mt-2 row" style="overflow-x:hidden;">
  							           @if(isset($rx) && $rx->prop_image != '')
										<div class="spotlight-group text-center col-md-2 col-4 m-1 border">
											<a class="spotlight"  
											   href="{{ UserHelper::rp_txt($rx->prop_image) }}" data-download="true"
											   data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
															<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($rx->prop_image_thumb) }}" style="width:100%;height:100%;"/>
											</a>
										</div>
										
							           @endif	
							      
								  @if($rx_cl_docs->count())
									   @foreach($rx_cl_docs as $image)
											
											@if($image->rx_id==$rx->id)  
												@php $ext = explode(".",$image->real_name)[1]; @endphp
												@if($ext=='pdf')
												<div class="spotlight-group text-center col-md-auto col-4 m-1 border">	
												   <a  class="fancybox" data-fancybox data-type="pdf"  href="{{UserHelper::rp_txt($image->path)}}">
													   <i class="fa fa-file-pdf fa-lg" style="color:#3574AA;"></i>
													   <br/>{{$image->real_name}}
													</a> 
												</div>	
												@else
												<div class="spotlight-group text-center col-md-2 col-4 m-1 border">
													<a class="spotlight" data-title="{{$image->real_name }}" 
													 href="{{ UserHelper::rp_txt($image->path) }}" data-download="true"
													 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
														<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($image->thumb_path) }}" style="width:100%;height:100%;"/>
													</a>
												</div>
												@endif 
											@endif
										
                                       @endforeach									   
								   @endif
								</div>
                               										
						</div>
						
					
				</div>
            </div>
			@endforeach
@else
<div id="RXCL" class="col-md-6 mt-1">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Prescription CL Rx')}}</b></u></div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
						   	</div> 
						</div>	
						<div class="card-body">
			                <div class="row table-responsive">
  							  <div class="col-md-12">
							  <h5>{{__('Undefined')}}</h5>
								</div>
                            </div>								
						</div>
						
					
				</div>
</div>					 
@endif	