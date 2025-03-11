<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="fieldsModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom:0.1rem;padding-top:0.1rem;">
                <div class="modal-title">
				 <h5 id="test_title"></h5>
				</div>
				<button type="button" class="ml-2 btn btn-action btn-sm" onclick="event.preventDefault();newData()">{{__('Create')}}<i class="ml-1 fa fa-plus"></i></button>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-bottom:0.1rem;padding-top:0.1rem;">
             <div class="row"> 
			  
			   <div class="col-md-12">
			   <div class="row">
			      <div class="col-md-12">
                           <input type="hidden" name="lab_num" id="lab_num"/>
					       <input type="hidden" id="field_test_id" name="test_id"/>
					       <input   type="hidden" id="field_test_name" name="field_test_name" />
						  <div id="fields_dt1" class="table-bordered table-sm" style="font-size:0.8rem;"></div>  
						  </div>
                  </div>
                </div>
				</div>
            </div>

        </div>

    </div>

</div>