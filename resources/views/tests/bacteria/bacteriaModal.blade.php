<div class="modal fade" id="bacteriaModal" aria-hidden="true">
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
					  <div class="col-md-6">
						  <label  for="name" class="label-size">{{__('Name').' *'}}</label>
						  <input type="text" id="bacteria_name" class="form-control" name="bacteria_name"/>
					  </div>
					   <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Bacterias').' *'}}</label>
						  <select name="bacteria_tests[]" id="bacteria_tests" class="select2 select2_modal form-control" data-dropdown-css-class="select2-teal" data-placeholder="{{__('Choose a code')}}" multiple="multiple" style="width: 100%;">
						    @foreach($sousbacteria as $t1)
							 <option value="{{$t1->id}}">{{$t1->descrip}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-6 select2-teal">
						  <label  for="name" class="label-size">{{__('Antibiotics').' *'}}</label>
						  <select name="antibiotic_tests[]" id="antibiotic_tests" class="select2 select2_modal form-control" data-dropdown-css-class="select2-teal" data-placeholder="{{__('Choose a code')}}" multiple="multiple" style="width: 100%;">
						    @foreach($tests as $t)
							 <option value="{{$t->id}}">{{$t->descrip}}</option>
							@endforeach
						  </select>
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