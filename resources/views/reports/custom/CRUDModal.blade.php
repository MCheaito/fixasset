<div class="modal fade" id="CRUDModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="padding-top:1px;padding-bottm:1px;">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-top:1px;padding-bottm:1px;">
                <form id="crudForm">
                  <div class="row">
				      <input type="hidden" name="id" id="id"/>
					   <div class="col-md-12 text-right">
						 <button  class="m-1 btn btn-action" id="saveBtn">Save changes</button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					  
					  <div class="mb-1 col-md-6">
						  <label  for="name" class="m-0 label-size">{{__('Report Name').' *'}}</label>
						  <input type="text" id="report_name" class="form-control" name="report_name"/>
					  </div>
					  
					  <div class="mb-1 col-md-6">
						  <label  for="name" class="m-0 label-size">{{__('Code').' *'}}</label>
						  <select name="test_id" id="test_id" class="form-control" style="width: 100%;">
						     <option value="">{{__('Choose a code')}}</option>
							@foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="mb-2 col-md-12">
					     <textarea  id="description" class="summernote form-control" name="description"></textarea>
					  </div>
					  
					 
				   </div>
               </form>

            </div>

        </div>

    </div>

</div>