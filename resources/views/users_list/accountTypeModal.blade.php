<!-- Modal -->
<div class="modal fade" id="accountTypeModal" tabindex="-1" role="dialog" aria-labelledby="accountTypeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Choose an account type')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form id="acc_type_form">
		 <div class="form-group">
				<label for="acc_type" class="form-label label-size">{{__('Account type')}}</label>
				<select  id="acc_type" name="acc_type" class="form-control">
				    <option value="1">{{__('Internal Lab')}}</option>
					<option value="2">{{__('Guarantor')}}</option>
				 </select>						   
		 </div>
		 </form>
      </div>
      <div class="modal-footer">
        <button id="create_new_user" type="button" class="btn btn-action">{{__('Go')}}</button>
		<button type="button" class="btn btn-delete" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>