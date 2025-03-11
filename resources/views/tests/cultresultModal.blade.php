<div class="modal fade" id="cultresultModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content movableDialog">
            <div class="modal-header" style="padding-bottom:0.1rem;padding-top:0.1rem;">
                <h5 class="modal-title">{{__('Add a culture result')}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body" style="padding-bottom:0.1rem;padding-top:0.1rem;">
             <div class="row"> 
				  <div class="col-md-12">  
					<form id="cultResultForm">
					 @csrf
					  <div class="mb-2 row">
						  <div class="col-md-12">
							<label  for="name" class="label-size">{{__('Name').' *'}}</label>
							<input type="text" id="cult_text_name" name="cult_text_name" class="form-control"/>
						  </div>
						  <div class="col-md-12 text-center">
						     <button  type="button" class="mt-2  btn btn-action btn-sm" onclick="event.preventDefault();newTextResult();">{{__("Insert")}}</button>
						     <button  type="reset" class="mt-2  btn btn-reset btn-sm" data-dismiss="modal">{{__("Close")}}</button>
						  </div>
						 
					   </div>
				     </form>
			       </div>
				   <div class="col-md-12">
					   <div class="row">
						   <div class="col-md-12">
								  <div id="result_text_div" class="table-bordered table-sm" style="font-size:0.9rem;"></div>  
								  </div>
						  </div>
                    </div>
			  
				</div>
            </div>

        </div>

    </div>

</div>