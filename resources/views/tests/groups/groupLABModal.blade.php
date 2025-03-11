<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="groupLABModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content movableDialog">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                 <div class="row text-center">
				     <div class="col-md-12">
						 <button  class="m-1 btn btn-action" id="saveBtn">{{__("Save")}}</button>
						 <button  id="closeBtn" type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
				</div>
				<form id="extLabForm">
                  <div class="row">
				      <input type="hidden" name="id" id="id"/>
					   <input type="hidden" name="code_type" id="code_type"/>
					  <input type="hidden" name="lab_num" id="lab_num"/>
					  <div class="col-md-6">
					    <label  for="name" class="label-size">{{__('Category')}}</label>
						<select id="category_num" name="category_num" class="select2_modal custom-select rounded-0" style="width:100%;">
					
							  <option value="">Choose a group category</option>
						      @foreach($categories as $t)
							   <option value="{{$t->id}}">{{$t->descrip}}</option>
							  @endforeach
						    
						</select>
					  </div>
					  <div class="col-md-6">
					    <label  for="name" class="label-size">{{__('Name').' *'}}</label>
						<input type="text" id="descrip" class="form-control" name="descrip"/>
					  </div>
					   <div class="col-md-3">
					    <label  for="name" class="label-size">{{__('Code')}}</label>
						<input type="text" id="code" class="form-control" name="code" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('CNSS')}}</label>
						<input type="number" id="cnss" name="cnss"  class="form-control" onkeypress="return isNumberKey(event)"/> 
           
					  </div>
					   <div class="col-md-2">
					    <label for="name" class="label-size">{{__('Price')}}</label>
						<input type="number" step="0.01" id="price" name="price"  class="form-control" onkeypress="return isNumberKey(event)"/> 
           
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Unit')}}</label>
						<input type="text" id="unit" name="unit"  class="form-control"/> 
           
					  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Normal Value')}}</label>
						<input type="text" id="normal_value" name="normal_value"  class="form-control"/> 
           
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('LisCode')}}</label>
						<input type="text" id="listcode" name="listcode"  class="form-control"/> 
           
					  </div>
					 <div class="col-md-6">
					    <label for="name" class="label-size">{{__('Remark')}}</label>
						<input type="text" id="test_rq" name="test_rq"  class="form-control"/> 
           
					  </div>
					  
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('NBL')}}</label>
						<input type="number" id="nbl" name="nbl"  class="form-control" onkeypress="return isNumberKey(event)"/> 
           
					  </div>
					  <div class="col-md-4">
					    <label  for="name" class="label-size">{{__('Referred tests')}}</label>
						<select  id="referred_tests" name="referred_tests" class="select2_modal custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a referred test")}}</option>
							@foreach($ext_labs as $g)
							  <option value="{{$g->id}}">{{$g->full_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6">
					    <label  for="name" class="label-size">{{__('Preanalytical')}}</label>
						<textarea  id="preanalytical" class="form-control" name="preanalytical" rows="1"></textarea>
					  </div>
					  <div class="col-md-3">
					    <label  for="name" class="label-size">{{__('Storage T°')}}</label>
						<select id="storage" name="storage" class="custom-select rounded-0">
						   <option value="">{{__("Choose a storage")}}</option>
						   <option value="Refrigerated">{{__("Refrigerated")}}</option>
						   <option value="Frozen">{{__("Frozen")}}</option>
						   <option value="Storage Temp.">{{__("Storage Temp.")}}</option>
						</select>
					  </div>
					  <div class="col-md-3">
					    <label  for="name" class="label-size">{{__('Transport T°')}}</label>
						<select id="transport" name="transport" class="custom-select rounded-0">
						   <option value="">{{__("Choose a transport")}}</option>
						   <option value="Room Temp.">{{__("Room Temp.")}}</option>
						   <option value="On ice">{{__("On ice")}}</option>
						   <option value="Transport Temp.">{{__("Transport Temp.")}}</option>
						</select>
					  </div>
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('TAT hrs')}}</label>
						<input type="number" id="tat_hrs" name="tat_hrs"  class="form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('Ordering')}}</label>
						<input type="number" id="testord" name="testord"  class="form-control" value="0" onkeypress="return isNumberKey(event)"/> 
					  </div>
					  
					  <div class="col-md-6">
					    <label for="name" class="label-size">{{__('Instructions')}}</label>
						<textarea id="description" name="description" class="form-control"  rows="1"></textarea>
					  </div>				  
					
					  
				   </div>
               </form>

            </div>

        </div>

    </div>

</div>