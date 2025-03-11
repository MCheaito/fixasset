<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="bacLABModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content movableDialog">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <form id="extLabForm">
                  <div class="row">
				      <input type="hidden" name="id" id="id"/>
					  <input type="hidden" name="lab_num" id="lab_num"/>
					  
					   <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Order')}}</label>
						<input type="number" min="0" id="testord" name="testord"  class="form-control"  onkeypress="return isNumberKey(event)"/> 
					  </div>
					  <div class="col-md-4">
					    <label  for="name" class="label-size">{{__('Code')}}</label>
						<input type="text" id="code" class="form-control" name="code" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-4">
					    <label  for="name" class="label-size">{{__('Description').' *'}}</label>
						<input type="text" id="descrip" class="form-control" name="descrip"/>
					  </div>
					  
					
					  <div class="mt-1 col-md-12 text-center">
						 <button  class="m-1 btn btn-action" id="saveBtn">Save changes</button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
				   </div>
               </form>

            </div>

        </div>

    </div>

</div>