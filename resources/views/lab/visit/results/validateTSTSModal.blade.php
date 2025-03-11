<!-- Modal -->
<div class="modal fade" id="validateTSTSModal" tabindex="-1" aria-labelledby="validateTSTSModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:3px;padding-bottom:1px;">
        <!-- Nav tabs -->
        <div class="container-fluid">
		 <div class="row"> 
		  <div class="col-10">	
			<h5>{{__('Validate tests')}}</h5>
		  </div>
          <div class="col-2">		  
			<button type="button" class="float-right btn btn-delete btn-sm" data-dismiss="modal">{{__('Close')}}</button>
		 </div>
         </div>		 
		</div>
		
      </div>
      <div class="modal-body">
        <div class="container-fluid">
		  <div class="row">
			<div class="table-responsive col-md-12">
			   <form>
			   <table id="validate-tsts-table" class="table-bordered table-sm nowrap" style="width:100%;">
		        <thead>
				   <tr>
					   <th>{{__('Validate All')}}</th>
					   <th>{{__('Type')}}</th>
					   <th>{{__('Group')}}</th>
					   <th>{{__('Test Name')}}</th>
					   <th>{{__('Result')}}</th>
					   
				   </tr>
				</thead>
				<tbody>
				</tbody>
			   </table>
			   </form>
            </div>
		  </div>
		</div>	  
      </div>
    </div>
  </div>
</div>