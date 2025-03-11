<!--
 DEV APP
 Created date : 14-5-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="qtyHistoryModal"  tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
		     <div class="p-0 modal-header txt-bg">
				 <div class="container-fluid text-white">
					<div class="row">  
						  <div class="col-md-10 col-8">
							   <h5>{{__('Inventory qty history')}}</h5>
						  </div>
						 
					</div>	 
				</div>	 
						<button type="button" class="float-right btn btn-action" data-dismiss="modal" aria-label="Close">
						  <i class="fa fa-times"></i>
						</button>
					  
				  
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body">
               <div class="mt-1 container">
					<div class="card"> 
						 <div class="row">
						    <div id="current_qty_div" class="form-group col-md-12"></div>
						   <div id="qty_history_div" class="form-group col-md-12"></div>
						 </div>
                    </div> 						 
			   </div>
			 
            </div>
        </div>
    </div>
</div>	
