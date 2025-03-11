<!--
 DEV APP
 Created date : 10-4-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="openBranchModal"  tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
		     <div class="modal-header txt-bg">
				 <div class="row text-white">
					  <div class="col-md-6">
						   <h3>{{__('Branches')}}</h3>
					  </div> 
				</div>	 
						<button type="button" class="float-right btn btn-action" data-dismiss="modal" aria-label="Close">
						  <i class="fa fa-times"></i>
						</button>
					  
				  
			 </div>
	         <!-- Modal body -->
            <div class="modal-body">
              
			  <div class="row">
			    <div class="col-md-8">
				   <select name="branches" id="branches" class="select2_modal form-control">
				   </select>
				</div>
				 <div class="col-md-4">
				   <button class="btn btn-action float-right" onclick="choose_branch()">{{__('Choose')}}</button>
				</div>
			  
			  </div>
			 
            </div>
        </div>
    </div>
</div>	

 
