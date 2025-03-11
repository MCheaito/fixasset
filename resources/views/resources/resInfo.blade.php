<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="docModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="padding-top:1px;padding-bottom:1px;">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-top:5px;padding-bottom:5px;">
                <form id="docForm">
                  <div class="row">
				       <div class="mb-1 col-md-12 text-center">
						 <button  class="m-1 btn btn-action" id="saveBtn">Save changes</button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					  <input type="hidden" name="id" id="id"/>
					  <input type="hidden" name="lab_num" id="lab_num"/>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('First Name').' *'}}</label>
						<input type="text" class="form-control" id="first_name" name="first_name" style="text-transform:uppercase"/>
					  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Middle Name')}}</label>
						<input type="text" class="form-control" id="middle_name" name="middle_name" style="text-transform:uppercase"/>
					  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Last Name').' *'}}</label>
						<input type="text" class="form-control" id="last_name" name="last_name" style="text-transform:uppercase"/>
					  </div>
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('Code')}}</label>
						<input type="text" id="code" name="code"  class="form-control"/> 
           
					  </div>
					  <div class="col-md-4">
						 <label for="specia" class="label-size">{{__('Category')}}</label>
						 <select  id="specia" name="specia" class="form-control">
							<option value="0">{{__('Choose a Speciality')}}</option>
							@foreach($specialities as $s)
								<option value="{{$s->id}}">{{(app()->getLocale()=='fr')?$s->name_fr:$s->name_en}}</option>
							@endforeach
						 </select>
					  </div>
					  <div class="col-md-3">
						  <label for="gender" class="label-size">{{__("Gender")}}</label>
						  <select name="gender" id="gender" class="custom-select rounded-0">
							<option value="">{{__("Unkown")}}</option>
							<option value="H">{{__("Male")}}</option>
							<option value="F">{{__("Female")}}</option>
						  </select>
					  </div>
					  <div class="col-md-3">
					      <label for="tel" class="label-size">{{__('Phone')}}</label>
						  <input class="phone form-control" type="text" id="tel" name="tel"/>
					  </div>
					  <div class="col-md-3">
					      <label for="tel2" class="label-size">{{__('Phone2')}}</label>
						  <input class="form-control" type="text" name="tel2" id="tel2"/>
					  </div>
					  <div class="col-md-3">
					      <label for="tel3" class="label-size">{{__('Phone3')}}</label>
						  <input class="form-control" type="text" name="tel3" id="tel3"/>
					  </div>
					  <div class="col-md-3">
					      <label for="fax" class="label-size">{{__('Fax')}}</label>
						  <input class="phone form-control" type="text" name="fax" id="fax"/>
					  </div>
					  <div class="col-md-3">
						  <label for="email" class="label-size">{{__('Email')}}</label>
						  <input id="email" class="mail form-control" type="text" name="email"/>
					  </div>
					  <div class="col-md-6">
						<label for="address" class="label-size">{{__('Address')}}</label>
						<input id="address" class="form-control" type="text" name="address"/>
					  </div>
					  <div class="col-md-3">
						<label for="city" class="label-size">{{__('City Name')}}</label>
						<input id="city" class="form-control" type="text" name="city"/>
					  </div>
					  <div class="col-md-3">
						<label for="appt_nb" class="label-size">{{__('Appt.#')}}</label>
						<input id="appt_nb" class="form-control" type="text" name="appt_nb"/>
					  </div>
					  <div class="col-md-3">
						<label for="state" class="label-size">{{__('State')}}</label>
						<input id="state" class="form-control" type="text" name="state"/>
					  </div>
					  <div class="col-md-3">
						 <label for="zip_code" class="label-size">{{__('Zip Code')}}</label>
						 <input id="zip_code" class="form-control" type="text" name="zip_code"/>
					  </div>
					  <div class="col-md-6">
					    <label for="name" class="label-size">{{__('Remarks')}}</label>
						<textarea id="remarks" name="remarks" class="form-control" name="remarks"></textarea>
					  </div>
					 
				   </div>
               </form>

            </div>

        </div>

    </div>

</div>