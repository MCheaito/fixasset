<div class="modal fade" id="patientCardModal" tabindex="-1" role="dialog" aria-labelledby="patientCardModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
           <h5 class="modal-title" id="modal_pat_name"></h5>
		   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
	  </div>
      
      <div class="modal-body">
		<div class="row">
		   <div class="col-12 mb-1">
			<label class="badge badge-info label-size"><b>{{__('Number of non-valid results').' : '}}</b><span class="ml-2" id="cnt_nonvalid"></span></label>
		  </div>
		   <div class="col-md-12">
		     <div id="pat_results_table" class="table-bordered table-sm"></div>
		   </div>
		 </div>
      </div>
      
    </div>
  </div>
</div>