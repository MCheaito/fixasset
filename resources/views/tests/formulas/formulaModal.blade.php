<div class="modal fade" id="formulaModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
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
					  <div class="col-md-4 select2-teal">
						  <label  for="name" class="label-size">{{__('Test Code').' *'}}</label>
						  <select name="test_id" id="test_id" class="select2_modal form-control" style="width: 100%;">
						    @foreach($tests_formulas as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6">
						  <label  for="name" class="label-size">{{__('Formula').' *'}}</label>
						  <input type="text" id="formula" class="form-control" name="formula"/>
					  </div>
					  <div class="col-md-2">
						  <label  for="name" class="label-size">{{__('Unit')}}</label>
						  <input type="text" id="unit" class="form-control" name="unit"/>
					  </div>
				  </div> 
				  <div class="mt-2 row">	
					 <legend>{{__('Codes/Factors')}}</legend>
					 <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Code1')}}</label>
						  <select name="test1" id="test1" class="select2_modal form-control" style="width: 100%;">
						    <option value="">{{__('Choose a code')}}</option>
							@foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Factor1')}}</label>
						  <input type="text"  name="factor1" class="form-control" id="factor1" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Code2')}}</label>
						  <select name="test2" id="test2" class="select2_modal form-control" style="width: 100%;">
						    <option value="">{{__('Choose a code')}}</option>
							@foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Factor2')}}</label>
						  <input type="text"  name="factor2" class="form-control" id="factor2" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Code3')}}</label>
						  <select name="test3" id="test3" class="select2_modal form-control" style="width: 100%;">
						    <option value="">{{__('Choose a code')}}</option>
							@foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Factor3')}}</label>
						  <input type="text"  name="factor3" id="factor3" class="form-control" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Code4')}}</label>
						  <select name="test4" id="test4" class="select2_modal form-control" style="width: 100%;">
						    <option value="">{{__('Choose a code')}}</option>
							@foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->test_name}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Factor4')}}</label>
						  <input type="text"  name="factor4" id="factor4" class="form-control" onkeypress="return isNumberKey(event)"/>
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