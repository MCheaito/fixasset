<div class="modal fade" id="changeLoginPassModal" tabindex="-1" role="dialog" aria-labelledby="changePassModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{{__("Change Password")}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
     	<div class="form-group">
		   <label class="label-size" for="name">{{__("New Password").'*'}}</label>
		   <input type="password" class="form-control" name="new_password" id="new_password" value="{{old('new_password')}}"/>
		</div>
		<div class="form-group">
		    <label class="label-size" for="name">{{__("Confirm New Password").'*'}}</label>
		    <input type="password" class="form-control" name="cfrm_password" id="cfrm_password" value="{{old('cfrm_password')}}"/>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" id="chg_pass" class="btn btn-action">{{__("Save")}}</button>
		<button type="button" class="btn btn-delete" data-dismiss="modal">{{__("Close")}}</button>
      </div>
    </div>
  </div>
</div>