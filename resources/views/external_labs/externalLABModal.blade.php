<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="externalLABModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="padding-top:1px;padding-bottom:1px;">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-top:5px;padding-bottom:5px;">
                <form id="extLabForm">
                  <div class="row">
				       <div class="mb-1 col-md-12 text-center">
						 <button  class="m-1 btn btn-action" id="saveBtn">Save changes</button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					  <input type="hidden" name="id" id="id"/>
					  <input type="hidden" name="lab_num" id="lab_num"/>
					  <div class="col-md-6">
					    <label for="name" class="label-size">{{__('Name').' *'}}</label>
						<input type="text" class="form-control" name="full_name" style="text-transform:uppercase"/>
					  </div>
					  
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Code')}}</label>
						<input type="text" name="code"  class="form-control"/> 
           
					  </div>
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('Rate')}}</label>
						<select name="rate" id="rate" class="form-control">
						  <option value="USD">US Dollar</option>
						  <option value="LBP">Lebanese Pound</option>
						</select>
					  </div>
					  <div class="col-md-1">
					    <label class="label-size">{{__('to Check?')}}</label>
						<label class="slideon slideon-xs slideon-success">
							  <input  type="checkbox" name="is_valid" id="is_valid"/>
							  <span class="slideon-slider"></span>
						</label>
					  </div>
					  
					  <div class="form-group col-md-2">
							<label for="pricel" class="label-size">{{__('PriceLBP')}}</label>
							<input id="pricel" class="form-control" type="text" name="pricel" 
						           onkeypress="return isNumberKey(event)" 
								   oninput="var rated = '{{$lbl_usd}}'; var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){$('#priced').val(parseFloat(price/rated).toFixed(2));$('#pricee').val(parseFloat(price/ratee).toFixed(2));}else{  $('#priced').val(''); $('#pricee').val(''); }"/>
														  
						</div>		
						<div class="form-group col-md-2">
							<label for="priced" class="label-size">{{__('Price$')}}</label>
						    <input id="priced" class="form-control" type="text" name="priced"  
							oninput="var rated = '{{$lbl_usd}}';var ratee = '{{$lbl_euro}}'; var price =$(this).val(); if(price!=''){ $('#pricel').val(price*rated);$('#pricee').val(parseFloat((price*rated)/ratee).toFixed(2));} else{ $('#pricel').val(''); $('#pricee').val(''); }"
							onkeypress="return isNumberKey(event)"/>
						</div>	
						
						<div class="form-group col-md-2">
						    <label for="pricee" class="label-size">{{__('Price€')}}</label>
							 <input id="pricee" class="form-control" type="text" name="pricee"  onkeypress="return isNumberKey(event)"/>
						</div>
						
					   <div class="col-md-3">
						  <label for="category" class="label-size">{{__('Category')}}</label>
						  <select  id="category" name="category" class="form-control">
							<option value="">{{__('Choose a category')}}</option>
							@foreach($cats as $c)
							<option value="{{$c->id}}">{{app()->getLocale()=='fr'?$c->name_fr:$c->name_en}}</option>
							@endforeach
						  </select>
						</div>	
					   
					   <!--<div class="col-md-3">
					    <label for="name" class="label-size">{{__('Percentage')}}</label>
						<input type="text" name="percentage"  class="form-control" onkeypress="return isNumberKey(event)"/> 
           
					  </div>-->
					  
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Email')}}</label>
						<input type="text" class="mail form-control" name="email"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Alternate Email1')}}</label>
						<input type="text" class="mail form-control" name="email2"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Alternate Email2')}}</label>
						<input type="text" class="mail form-control" name="email3"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Fax Nb')}}</label>		
						<input type="text" class="phone form-control" name="fax"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Contact Phone')}}</label>
						<input type="text" class="phone form-control" name="telephone"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Alternate Phone1')}}</label>
						<input type="text" class="form-control" name="alternate_phone1"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Alternate Phone2')}}</label>
						<input type="text" class="form-control" name="alternate_phone2"/>
					  </div>
					  <!--<div class="col-md-3">
					    <label for="name" class="label-size">{{__('Region Name')}}</label>
						<input type="text" class="form-control" name="region_name"/>
					  </div>-->
					  <div class="col-md-6">
					    <label for="name" class="label-size">{{__('Address')}}</label>
						<input type="text" class="form-control" name="full_address"/>
					  </div>
					  <!--<div class="col-md-3">
					    <label for="name" class="label-size">{{__('Appt.#')}}</label>
						<input type="text" class="form-control" name="appt_nb"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('City Name')}}</label>
						<input type="text" class="form-control" name="city"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('State')}}</label>
						<input type="text" class="form-control" name="state"/>
					  </div>
					  <div class="col-md-3">
					    <label for="name" class="label-size">{{__('Zip code')}}</label>
						<input type="text" class="form-control" name="zip_code"/>
					  </div>-->
					  
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