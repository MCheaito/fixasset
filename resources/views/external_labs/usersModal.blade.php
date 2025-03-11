<!-- 
 DEV APP
 Created date : 23-6-2024
-->
<div class="modal fade" id="usersModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="padding-top:1px;padding-bottom:1px;">
                <h5 class="modal-title" id="modelHeading"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-top:5px;padding-bottom:5px;">
                <form id="usersForm">
                  <div class="row">
				       <div class="mb-1 col-md-5">
					    <input type="hidden" name="guarantor_id" id="guarantor_id"/>
						<input type="hidden" name="guarantor_lab_num" id="guarantor_lab_num"/>
						<input type="text" class="form-control" id="guarantor_name" name="guarantor_name" style="text-transform:uppercase" disabled />
					  </div>
					  
					   <div class="mb-1 col-md-7 text-right">
						 <button  class="m-1 btn btn-action" id="saveBtn" onclick="event.preventDefault();saveUserInfo();"></button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					 
					  
					  <div class="mb-1 col-md-4">
						<label for="name" class="m-0 label-size">{{__('First Name').' *'}}</label>
						<input type="text" class="form-control" id="guarantor_fname" name="guarantor_fname"/>
					  </div>
					  <div class="mb-1 col-md-4">
						<label for="name" class="m-0 label-size">{{__('Last Name').' *'}}</label>
						<input type="text" class="form-control" id="guarantor_lname" name="guarantor_lname"/>
					  </div>
					  <div class="mb-1 col-md-4">
					    <label for="name" class="m-0 label-size">{{__('Email').' *'}}</label>
						<input type="text" class="mail form-control" id="guarantor_email" name="guarantor_email"/>
					  </div>
					  <div class="mb-1 col-md-4">
					    <label for="guarantor_username" class="m-0 label-size">{{__('Username').' *'}}</label>
                        <input type="text" class="form-control" id="guarantor_username" name="guarantor_username" required />
					  </div>
					  <div class="mb-1 col-md-3">
                          <label for="permission" class="m-0 form-label label-size">{{__('Profile Permission')}}</label>
                          <select name="permission" class="form-control" disabled>
			                  <option value="L" selected>{{__('Guarantor')}}</option>
                          </select>					   
				      </div>
					 <div class="mb-1 card col-md-5 border border-success">
						   
						   <div class="card-body p-0">
								
							   <div id="div_menus" class="row m-1">
								    @foreach($user_menus as $m)
										 @if($m=='profile' || $m=='dashboard' || $m=='all_patients' || $m=='lab_requests' || $m=='medical_billings' || $m=='custom_reports'
										 || $m=='general_reports' || $m=='tests_settings' || $m=='all_resources' || $m=='users' || $m=='send_feedback' || $m=='inventory' || $m=='phlebotomy') 
											 <div class="m-1 col-md-12 border border-info"></div>
											  <div class="col-md-12">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" checked style="width:1em;height:1em;"/>&#xA0;<b>{{__($m)}}</b>
											  </div>
										 @else
											   
											   <div class="col-md-auto" style="margin-left:5px;">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" checked style="width:1em;height:1em;"/>&#xA0;{{__($m)}}
											   </div>
											     
										 @endif
							        @endforeach
								</div>
						   </div>						
						</div>
					 </div>
					 
				   </div>
               </form>

            </div>

        </div>

    </div>

</div>