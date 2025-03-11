<!--
   DEV APP
   Created date : 8-10-2022
-->
 
 @if($rxs->count())
  <div  class="col-md-4">
	   <div class="card card-outline ">
		 <div class="card-body p-1">
			<select name="rx_select" id="rx_select" class="label-size custom-select rounded-0"  size="5">
			@foreach($rxs as $rx)
			 @php $cancelled_state =($rx->active=='N')?' - '.__('Cancelled'):''; @endphp
			 <option value="{{$rx->id}}">{{'#'.$rx->id.' - '.$rx->rx_type.' , '.Carbon\Carbon::parse($rx->datein)->format('Y-m-d H:i').$cancelled_state}}</option>
			@endforeach
			</select>		
		 </div>
	   </div>
	 </div>  
  @foreach($rxs as $rx)	
        @php
		   
		   $rx_id = ' , #'.$rx->id;
		  
		@endphp		
  <div id="RX-{{$rx->id}}" class="card-rx col-md-8" style="display:none;">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Prescription Rx').$rx_id}}</b></u>@if($rx->active=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif</div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
						   	</div> 
						</div>	
						<div class="card-body">
			                @if($rx->odsph !='' || $rx->odcyl !='' ||  $rx->odaxe !='' || $rx->oddva !='' || $rx->odav !='' ||
				                $rx->ogsph !='' || $rx->ogcyl !='' ||  $rx->ogaxe !='' || $rx->ogdva !='' || $rx->ogav !='')
							<div class="row table-responsive">
  							  <div class="col-md-12">
								 <table class="table table-borderless" style="width:100%;">
										<thead class="text-center">
											<tr>
												<th scope="col" class="border-left-0 border-top-0"></th>
												<th scope="col" class="border ">{{__('Sphere')}}</th>
												<th scope="col" class="border ">{{__('Cylinder')}}</th>
												<th scope="col" class="border ">{{__('Axis')}}</th>
												<th scope="col" class="border ">{{__('ADD')}}</th>
												<th scope="col" class="border">{{__('V.A.')}}</th>

											</tr>
										</thead>
										<tbody class="text-center">
											<tr>
												<th scope="row" class="border">{{__('OD')}}</th>
												<td id="odsph" class="border">{{isset($rx)?$rx->odsph:__('Undefined')}}</td>
												<td id="odcyl" class="border">{{isset($rx)?$rx->odcyl:__('Undefined')}}</td>
												<td id="odaxe" class="border">{{isset($rx)?$rx->odaxe:__('Undefined')}}</td>
												<td id="oddva" class="border">{{isset($rx)?$rx->oddva:__('Undefined')}}</td>
												<td id="odav" class="border">{{isset($rx)?$rx->odav:__('Undefined')}}</td>

											</tr>
											<tr>
												<th scope="row" class="border">{{__('OS')}}</th>
												<td id="ogsph" class="border">{{isset($rx)?$rx->ogsph:__('Undefined')}}</td>
												<td id="ogcyl" class="border">{{isset($rx)?$rx->ogcyl:__('Undefined')}}</td>
												<td id="ogaxe" class="border">{{isset($rx)?$rx->ogaxe:__('Undefined')}}</td>
												<td id="ogdva" class="border">{{isset($rx)?$rx->ogdva:__('Undefined')}}</td>
												<td id="ogav" class="border">{{isset($rx)?$rx->ogav:__('Undefined')}}</td>

											</tr>
										</tbody>
									</table>
								</div>
                            </div>
							@endif
							@if($rx->odvertex !='' || $rx->odprism !='' || $rx->oddeloin !='' || $rx->oddepres !='' || $rx->odbase !='' ||
							    $rx->ogvertex !='' || $rx->ogprism !='' || $rx->ogdeloin !='' || $rx->ogdepres !='' || $rx->ogbase !='')
							<div  class="row table-responsive">   
											<table class="table table-borderless" style="width:100%;">
												<thead class="text-center">
													<tr>
														<th scope="col" class="border-right-bottom"></th>
														<th scope="col" class="border">{{__('Vertex')}}</th>
														<th scope="col" class="border">{{__('Prism')}}</th>
														<th scope="col" class="border">{{__('Base')}}</th>
														<th scope="col" class="border">{{__('Far P.D.')}}</th>
														<th scope="col" class="border">{{__('Near P.D.')}}</th>
												</tr>
												</thead>
												<tbody class="text-center">
													<tr>
														<th scope="row" class="border">{{__('OD')}}</th>
														<td class="border">{{isset($rx)?$rx->odvertex:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->odprism:__('Undefined')}}</td>
														<td class="border">{{isset($rx) ?$rx->odbase:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->oddeloin:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->oddepres:__('Undefined')}}</td>
													</tr>
													<tr>
														<th scope="row" class="border">{{__('OS')}}</th>
														<td class="border">{{isset($rx)?$rx->ogvertex:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->ogprism:__('Undefined')}}</td>
														<td class="border">{{isset($rx) ?$rx->ogbase:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->ogdeloin:__('Undefined')}}</td>
														<td class="border">{{isset($rx)?$rx->ogdepres:__('Undefined')}}</td>
													</tr>
													
												</tbody>
											</table>
										  </div>
										@endif  
                            @if($rx_docs->count())
							<div class="mt-2 row" style="overflow-x:hidden;">
  							  
							      
									   @foreach($rx_docs as $image)
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
								   
								</div>
                               @endif								
						</div>
						
					
				</div>
            </div>
			@endforeach
@else

<div id="RX" class="col-md-6 mt-1">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Prescription Rx')}}</b></u></div>
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