<div class="modal fade" id="SMSEmailHistoryModal" tabindex="-1" role="dialog" aria-labelledby="SMSEmailHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:1px; padding-bottom:1px;">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Email/SMS History')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top:1px; padding-bottom:1px;">
        <ul class="nav nav-pills" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">{{__('Sent Email')}}</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link" id="tab1-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab1" aria-selected="true">{{__('Sent SMS')}}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">{{__('Fail')}}</a>
          </li>
        </ul>
		<div class="tab-content mt-1" id="myTabContent">
		   <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">	 
			 <div class="row table-responsive">
			  <div class="form-group col-md-12">
				<table id="emailSMS_table1" class="table table-striped" style="width:100%">
				  <thead>
					<tr>
					  <th>{{__('Patient')}}</th>
					  <th>{{__('Doctor')}}</th>
					  <th>{{__('Guarantor')}}</th>
					  <th>{{__('Email')}}</th>
					</tr>
				  </thead>
				  <tbody>
				   
				  </tbody>
				</table>
			  </div>
			  </div>
			</div>
			<div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab1-tab">	 
			 <div class="row table-responsive">
			  <div class="form-group col-md-12">
				<table id="emailSMS_table3" class="table table-striped" style="width:100%">
				  <thead>
					<tr>
					  <th>{{__('Patient')}}</th>
					  <th>{{__('Doctor')}}</th>
					  <th>{{__('Guarantor')}}</th>
					  <th>{{__('SMS')}}</th>
					</tr>
				  </thead>
				  <tbody>
				   
				  </tbody>
				</table>
			  </div>
			  </div>
			</div>
			<div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
			  <div class="row table-responsive">
			  <div class="form-group col-md-12">
				<table id="emailSMS_table2" class="table table-striped" style="width:100%">
				  <thead>
					<tr>
					  <th>{{__('Message')}}</th>
					  <th>{{__('Date')}}</th>
					</tr>
				  </thead>
				  <tbody>
				   
				  </tbody>
				 </table>
			    </div>
              </div>
		   </div>	
		</div>	
      </div>
    </div>
  </div>
</div>