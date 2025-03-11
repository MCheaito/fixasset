<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">{{__('Reject Note')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rejectId">
                    <div class="form-group">
                        <label class="label-size" for="rejectNote">{{__('Reason')}}</label>
                        <input class="form-control" id="rejectNote" name="reject_note" type="text" list="rejctNoteOptions"></textarea>
                        <datalist id="rejctNoteOptions">
						@foreach($reject_notes as $n)
						 <option value="{{$n}}"></option>
						@endforeach
					   </datalist>	
					</div>
					<div class="form-group">
                        <label class="label-size" for="otherrejectNote">{{__('Other Remarks')}}</label>
                        <textarea class="form-control" id="otherrejectNote" name="other_reject_note"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-action" onclick="event.preventDefault();saverejectNote();">{{__('Save')}}</button>
					<button type="button" class="btn btn-delete" data-dismiss="modal">{{__('Cancel')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
