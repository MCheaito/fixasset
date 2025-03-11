<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="fieldLABModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content movableDialog">
            <div class="modal-header" style="padding-bottom:0.1rem;padding-top:0.1rem;">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-bottom:0.1rem;padding-top:0.1rem;">
             <div class="row"> 
			  <div class="col-md-12">  
				<form id="extLabForm">
                  <div class="row">
				      <input type="hidden" name="id" id="id"/>
					  <input type="hidden" name="field_type" id="field_type"/>
					  <input type="hidden" name="lab_num" id="lab_num"/>
					  <div class="col-md-5 col-8">
					    <label  for="name" class="label-size">{{__('Code').' *'}}</label>
						<select id="test_id" name="test_id" class="select2_modal custom-select rounded-0" style="width:100%;">
						 @if(auth()->user()->type==2)
							  <option value="">{{__("Choose a code")}}</option>
						      @foreach($tests as $t)
							      @if(isset($t->test_code) && $t->test_code!='')
								      <option value="{{$t->id}}">{{$t->test_name.' ( '.$t->test_code.' )'}}</option>
							      @else
									  <option value="{{$t->id}}">{{$t->test_name}}</option>
								  @endif
							  @endforeach
						  @endif	  
						</select>
					  </div>
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Gender').' *'}}</label>
                        <select class="rdata form-control" name="gender" id="gender">
						  <option value="">Choose a gender</option>
						  <option value="M">Male</option>
						  <option value="F">Female</option>
						  <option value="B">Both</option>
						</select>
					  </div>
					  <div class="col-md-1 col-6">
					    <label for="name" class="label-size">{{__('Panic low')}}</label>
						<input type="number" id="panic_low_value" name="panic_low_value"   class="rdata form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <div class="col-md-1 col-6">
					    <label for="name" class="label-size">{{__('Panic high')}}</label>
						<input type="number" id="panic_high_value" name="panic_high_value"  class="rdata form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <!--<div class="col-md-4 col-12 text-center">
					    <label for="name" class="label-size">{{__("For comparison")}}</label>
						<label class="m-1 slideon slideon-xs slideon-success">
						  <input  type="checkbox" name="is_comparison" id="is_comparison" checked />
						  <span class="slideon-slider"></span>
						</label>
						<div>
						<label for="name" class="label-size">{{__("Desirable low")}}</label>
						<label class="m-1 slideon slideon-xs slideon-success">
						  <input class="chkbox" type="checkbox" name="desirable_low" id="desirable_low"/>
						  <span class="slideon-slider"></span>
						</label>
						<label for="name" class="label-size">{{__("Desirable high")}}</label>
						<label class="m-1 slideon slideon-xs slideon-success">
						  <input class="chkbox" type="checkbox" name="desirable_high" id="desirable_high"/>
						  <span class="slideon-slider"></span>
						</label>
						</div>
					  </div>-->
					  <div class="col-md-1 col-4">
					    <label for="name" class="label-size">{{__('From age >=')}}</label>
						<input type="number" id="fage" min="0" name="fage"  class="rdata form-control" onkeypress="return isNumberKey(event)" value="0"/> 
           			  </div>
					  <div class="col-md-1 col-4">
					    <label for="name" class="label-size">{{__('To age <')}}</label>
						<input type="number" id="tage" min="0" name="tage"  class="rdata form-control" onkeypress="return isNumberKey(event)" value="0"/> 
           			  </div>
					   <div class="col-md-1 col-4">
					    <label for="name" class="label-size">{{__('Age type')}}</label>
                        <select class="form-control" name="mytype" id="mytype">
							  <option value="Y">Year</option>
							  <option value="M">Month</option>
							  <option value="W">Week</option>
							  <option value="D">Day</option>
						</select>
					  </div>
					  
					  <div class="col-md-1 col-6">
					    <label for="name" class="label-size">{{__('Min')}}</label>
						<input type="number" id="normal_value1" name="normal_value1"   class="rdata form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__("Min Sign")}}</label>
						<select name="sign_min" id="sign_min" class="rdata form-control">
						  <option value="">{{__("Choose")}}</option>
						  <option value="Neg">{{__("Neg")}}</option>
						  <option value="Pos">{{__("Pos")}}</option>
						  <option value="Bord">{{__("Bord")}}</option>
						  <option value="High">{{__("High")}}</option>
						  <option value="Low">{{__("Low")}}</option>
						  <option value="Reactive">{{__("Reactive")}}</option>
						  <option value="No Reactive">{{__("No Reactive")}}</option>
						</select>
					  </div>
					  <div class="col-md-1 col-6">
					    <label for="name" class="label-size">{{__('Max')}}</label>
						<input type="number" id="normal_value2" name="normal_value2"  class="rdata form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__("Max Sign")}}</label>
						<select name="sign_max" id="sign_max" class="rdata form-control">
						  <option value="">{{__("Choose")}}</option>
						  <option value="Neg">{{__("Neg")}}</option>
						  <option value="Pos">{{__("Pos")}}</option>
						  <option value="Bord">{{__("Bord")}}</option>
						  <option value="High">{{__("High")}}</option>
						  <option value="Low">{{__("Low")}}</option>
						  <option value="Reactive">{{__("Reactive")}}</option>
						  <option value="No Reactive">{{__("No Reactive")}}</option>
						</select>
					  </div>
					  <div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__("Other Sign")}}</label>
						<input type="text" name="sign" id="sign" class="rdata form-control"/>
					  </div>
					  <div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__('Unit')}}</label>
						<input type="text" id="unit" name="unit"  class="rdata form-control"/> 
           			  </div>
					   <div class="col-md-1 col-6">
					    <label for="name" class="label-size">{{__('Order')}}</label>
						<input type="number" id="field_order" min="0" name="field_order"  class="rdata form-control" onkeypress="return isNumberKey(event)"/> 
           			  </div>
					  <div class="col-md-5">
					    <label  for="name" class="label-size">{{__('Description')}}</label>
						<textarea id="descrip" class="rdata form-control" name="descrip" rows="1"  style="height:50px;"></textarea>
					  </div>
					   <div class="col-md-5">
					     <label for="name" class="label-size">{{__('Remark')}}</label>
						 <textarea id="remark" name="remark" class="rdata form-control" rows="1" style="height:50px;"></textarea>
					  </div>
					 
					 
					  <!--<div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__('Comparison criteria')}}</label>
                        <select class="rdata form-control" name="criteria" id="criteria">
						  <option value="">None</option>
						  <option value="A">Age</option>
						  <option value="G">Gender</option>
						  <option value="AG">Age and Gender</option>
						</select>
					  </div>-->
					  <div class="mt-4 col-md-2">
						 <button  class="m-1 btn btn-action btn-sm" id="saveBtn">{{__("Save")}}</button>
						 <button type="reset" class="m-1 btn btn-reset btn-sm" data-dismiss="modal">{{__('Close')}}</button>
						 <button  class="m-1 btn btn-delete btn-sm" id="inactiveBtn" style="display:none;">{{__("Inactivate")}}</button>
                     
					 </div>
					 
				   </div>
               </form>
			   </div>
			   <div class="col-md-12">
			   <div class="row">
			       <div class="col-md-12">
                          <div id="fields_dt1" class="table-bordered table-sm" style="font-size:0.9rem;"></div>  
							
							
                        </div>
                  </div>
                </div>
				</div>
            </div>

        </div>

    </div>

</div>