<!--
 DEV APP
 Created date : 9-2-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="NewItemModal"  tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="overflow-y: initial !important">
        <div class="modal-content">
		     <div class="p-0 modal-header txt-bg">
				 <div class="container-fluid text-white">
					<div class="row">  
						  <div class="col-md-10 col-8">
							   <h3>{{__('Items')}}</h3>
						  </div>
						  <div class="col-md-2 col-4">
							<label class="mt-2 label-size float-right badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</label>
						 </div>	
					</div>	 
				</div>	 
						<button type="button" class="float-right btn btn-action" data-dismiss="modal" aria-label="Close">
						  <i class="fa fa-times"></i>
						</button>
					  
				  
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body" style="height: 85vh;overflow-y: auto;">
              
			  @include('inventory.items.lunetteview',['modal'=>true])
			 
            </div>
        </div>
    </div>
</div>	

 
