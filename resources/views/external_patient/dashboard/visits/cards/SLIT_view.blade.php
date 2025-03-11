<!--
   DEV APP
   Created date : 25-12-2022  
-->
<div id="SLIT" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Medical slit lamp images')}}</b></u></div>
							<div class="card-tools">
							  <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							  </button>
							 
					     	</div> 
						</div>	
						<div class="card-body">
			                 <div class="row table-responsive">
  							   @if(count($SLIT_images)>0)
									  @foreach($SLIT_images as $image)
								        @php $ext = explode(".",$image->image_name)[1]; @endphp
										<div class="spotlight-group col-md-2 col-4 m-1 border">
											<a class="spotlight" data-title="{{$image->image_name }}" 
												 data-description="{{$image->notes}}" href="{{ UserHelper::rp_txt($image->image_path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($image->thumb_image_path) }}" style="width:100%;height:100%;"/>
												</a>
											
										</div>
                                       @endforeach				 
								   @else
									<div class="col-md-12">
							           <div class="mb-2"><i class="far fa-images fa-2x btn-active"></i></div> 
								      <div><h5>{{__('No documents')}}</h5></div>
									</div>
								   @endif  
                            </div>
                            				
						</div>
					
				</div>
            </div>