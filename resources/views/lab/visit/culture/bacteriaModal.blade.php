<!--Discount modal-->
			<div class="modal fade" id="bacteriaModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header" style="padding-top:0.1rem;padding-bottom:0.1rem;">
								
								<div class="form-group col-md-10">
								   <label class="m-0">{{__('Choose a bacteria')}}</label>
								   <select class="sbacteria-multiple form-control" name="sbacteria[]"  data-placeholder="{{__('Choose a bacteria')}}" multiple="multiple" style="padding:0;width:100%">
										@foreach($sbacteria as $b)
										<option value="{{$b->id}}">{{$b->descrip}}</option>
										@endforeach
									</select> 
							    </div>
								<input type="hidden" name="bact_cult_id"/>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>
							<div class="modal-body">
								   
									<div class="container">
						               <div id="bact_ant_tbl" class="row">
									     
										 
										 <!--<div class="col-md-6 mb-1">
										  <input type="text" id="searchBactInput" class="form-control mb-3" placeholder="Search...">
										 </div>-->
										
									   </div> 
								 </div>
								 
							</div>
		</div>		
    </div>		
</div>					
<!--end DISCOUNTModal-->	  	  