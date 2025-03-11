<!--
   DEV APP
   Created date : 16-6-2023
-->
<div id="VISF" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Biomicroscopy document')}}</b></u></div>
							<div class="card-tools">
							  <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							  </button>
							 
					     	</div> 
						</div>	
						<div class="card-body">
			               <div class="row m-1" style="overflow-x:hidden;">
							      @if(isset($biomicro_doc))
											<div class="col-md-4 col-6  border">
													<a class="spotlight" data-title="{{$biomicro_doc->real_name }}" 
													 href="{{ UserHelper::rp_txt($biomicro_doc->path) }}" 
													 data-download="true"
													 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
														<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($biomicro_doc->thumb_path) }}" style="width:100%;height:100%;"/>
													</a>
												
												
											</div>
											
										@if(isset($biomicro_doc->notes) && $biomicro_doc->notes !='') 
											<div class="table-responsive col-md-12 border">
											   <table class="table-borderless" style="width:100%;">
											   <tr><th style="text-align:center;">{{__("Description")}}</th></tr>
											   <tr><td>{!!nl2br($biomicro_doc->notes)!!}</td></tr>
											  </table>
											 </div>
										  
										@endif
                                      								   
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
	