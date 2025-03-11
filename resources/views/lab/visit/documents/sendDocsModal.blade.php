<!--
   DEV APP
   Created date : 25-3-2023
-->
<!-- The Modal -->
<div class="modal hide fade in" id="sendDocsModal"  tabindex="-1" data-keyboard="false" data-backdrop="false" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg movableDialog">
        <div class="modal-content">
		     <div class="modal-header card-menu">
			    <ul class="nav nav-pills">
					 <li class="nav-item pat_tab">
						 <a class="nav-link active"  href="#patient_data" data-toggle="tab">{{__('Patient')}}</a>
					</li>
					 <li class="nav-item ext_branches_tab">
					 <a class="nav-link"  href="#external_branches" data-toggle="tab">{{__('External branches')}}</a>
				    </li>
				</ul>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			 </div>
	         <!-- Modal body -->
            <div class="p-0 modal-body">
				  <input type="hidden" id="ext_visit_id"/>
				  <input type="hidden" id="code_type" value="documents"/>
				  <div class="tab-content">
					  <div class="tab-pane active" id="patient_data">
					  </div>
					  <div class="tab-pane" id="external_branches">
					  </div>
				 </div>
				 
						  
            </div>
        </div>
    </div>
</div>	