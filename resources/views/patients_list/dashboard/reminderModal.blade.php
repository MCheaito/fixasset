<!-- Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reminderModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="remind_form">
		<div class="m-1 row">
		   <div class="form-group col-md-6">
		    <label  class="label-size">{{__('Patient')}}</label>
			<input type="text" class="form-control" disabled value="{{$pat_name}}"/>
			<input type="hidden" id="pat_id" name="patient_num" value="{{$pat_id}}"/>
			<input type="hidden" id="rem_id" name="rem_id" value="0"/>
		   </div>
		   <div class="form-group col-md-6">
		    <label  class="label-size">{{__('Branch')}}</label>
			<input type="text" class="form-control" disabled value="{{$branch_name}}"/>
			<input type="hidden" id="branch_id" name="branch_num" value="{{$branch_id}}"/>
		   </div>
		   <div class="form-group col-md-3">
		     <label  class="label-size">{{__('Type')}}</label>
			 <select name="remind_type" id="remind_type" class="custom-select rounded-0">
			    <option value="dob">{{__("DOB")}}</option>
			    <option value="rx">{{__("Rx")}}</option>
			 </select>
		   </div>
		   <div class="form-group col-md-3 border">
			   <label  class="label-size">{{__('Authorize sending by')}}</label>
				   <div class="mt-2"> 
								  <label class="form-check-label" for="chkmail">{{__('Email')}}</label>
								  <label class="mt-2 ml-2 slideon slideon-xs  slideon-success">										
								  <input id="chkmail" type="checkbox" class="chk" name="remind_by_email" style="height:20px;width:20px;margin-left:7px;margin-right:7px;"/>
								  <span class="slideon-slider"></span></label>
								  <label class="form-check-label" for="chksms">{{__('SMS')}}</label>
								  <label class="mt-2 ml-2 slideon slideon-xs  slideon-success">										
								  <input id="chksms" type="checkbox" class="chk" name="remind_by_sms" style="height:20px;width:20px;margin-left:7px;margin-right:7px;"/>
								  <span class="slideon-slider"></span></label>

					</div> 
			</div>
			<div  class="form-group col-md-3">
									 <label  for="code" class="label-size">{{__('Remind before')}}</label>
									 <div class="input-group">	 
										 <select  id="remind_before" name="remind_before" class="select2_data custom-select rounded-0" style="width:100%;">
											<option value="0">{{__('Undefined')}}</option>
											<option value="48">48h</option>
											<option value="72">72h</option>
											<option value="96">96h</option>
											<option value="120">120h</option>
											<option value="144">144h</option>
											<option value="168">168h</option>
										</select>
										
									 </div>	
			</div>
            <div class="form-group col-md-3">
								 <label for="dateSpick" class="label-size">{{__('Next Date')}}</label>
								 <div class="input-group">
										<span class="input-group-text">
											<i class="fa fa-calendar-alt"></i>
										</span>
									<input type="text" name="dateSpick"  class="date_data form-control" value="{{old('dateSpick')}}"/>
								 </div>
				      </div>
				
			
		 </div>
		 <div id="variables" class="row m-1">
					       <div class=" table-responsive form-group col-md-12">
								  <table class="table table-sm table-bordered" style="font-size:small;width:100%"> 
								   <thead><tr><th colspan="5" style="text-align:center;"><h5>{{__('Variables')}}</h5></th></tr></thead>
								   <tbody>
									   <td  style="padding:0;border-block-width:medium"><b>*BranchFullName*</b></td>
									   <td  style="padding:0;border-block-width:medium"><b>*BranchFullAddress*</b></td>
									   <td  style="padding:0;border-block-width:medium"><b>*BranchContactPhone*</b></td>
									   <td  style="padding:0;border-block-width:medium"><b>*PatientFullName*</b></td>
									   <td  style="padding:0;border-block-width:medium"><b>*Date*</b></td>

								   </tbody>
								  </table> 
							  </div>
					</div>
		 <div id="mail" class="row m-1 txt-border">	
					         <div class="form-group col-md-12 text-center"><h5>{{__("Email setting")}}</h5></div>
							 <div class="form-group col-md-6">
								<label class="label-size" for="selectSubjEN">{{__("Title")}}</label>
								<input type="text" class="form-control" id="selectSubjEN" name="email_head_en" value="" class="form-control"/>
							 </div>
							 <div class="form-group col-md-10">	
									   <label class="label-size" for="selectMailEN">{{__("Write your message")}}</label>
									   <textarea id="selectMailEN" name="email_body_en" rows="5"  style="width:100%;" class="form-control"></textarea>
								
							 </div>
							 
							  
					</div><!--end mail tab pane-->	
         <div id="sms" class="m-1 row txt-border">
						        <div class="form-group col-md-12 text-center"><h5>{{__("SMS setting")}}</h5></div>
								<div class="form-group col-md-10">	
									 <div class="form-group mb-3">
										   <label class="label-size" for="selectSms">{{__("Write your message")}}</label>
										   <textarea id="selectSmsEN" name="sms_body_en" rows="5" cols="100" style="width:100%;" class="form-control"></textarea>
										   
									  </div>
								</div>

								 
						</div>
            </form>						
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
        <button id="save_rem" type="button" class="btn btn-action">{{__('Save')}}</button>
      </div>
    </div>
  </div>
</div>