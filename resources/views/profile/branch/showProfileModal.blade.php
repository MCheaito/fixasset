<!-- 
 DEV APP
 Created date : 3-3-2023
-->
<div class="modal fade" id="showProfileModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
	   <div class="modal-content movableDialog">
                <div class="modal-header">
                     <h5>{{__('Show')}}</h5>
					 <button type="button" class="pull-right btn btn-sm" data-dismiss="modal" aria-label="Close">
								 <i class="fas fa-times"></i> 
					 </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						   <div class="col-md-12">	
								<div class="card card-outline">
									<div class="card-header card-menu">
										<ul class="nav nav-pills">
										  <li class="nav-item"><a class="nav-link active" href="#persos" data-toggle="tab">{{__('General Information')}}</a></li>
										  <li class="nav-item"><a class="nav-link" href="#schedule" data-toggle="tab">{{__('Schedule')}}</a></li>
										</ul>
									</div>
									<div class="card-body p-0">
									   <div class="tab-content">
										   <div id="persos" class="active tab-pane" style="overflow-x:auto;">
													
														
										   </div>
										   <div id="schedule" class="tab-pane" style="overflow-x:auto;">
												
												
										   </div>
										  
										 </div>
									</div>
								</div>
							</div>  
						 </div>
				
				</div> 
        </div>
	 </div>
</div>	 
