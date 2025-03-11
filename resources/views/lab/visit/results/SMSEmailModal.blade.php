<!-- Modal -->
<div class="modal fade" id="SMSEmailModal" tabindex="-1" aria-labelledby="communicationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="padding-top:3px;padding-bottom:1px;">
        <!-- Nav tabs -->
        <div class="container-fluid">
		 <div class="row"> 
		  <div class="col-10">	
			<ul class="nav nav-pills" role="tablist">
			  <li class="nav-item">
				<a class="nav-link active" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="true">{{__('Email')}}</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" id="sms-tab" data-toggle="tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false">{{__('SMS')}}</a>
			  </li>
			</ul>
		  </div>
          <div class="col-2">		  
			<button type="button" class="float-rigth btn btn-action btn-sm" id="sendResultsBtn">{{__('Send')}}</button>
			<button type="button" class="float-right btn btn-delete btn-sm" data-dismiss="modal">{{__('Close')}}</button>
		 </div>
         </div>		 
		</div>
		
      </div>
      <div class="modal-body">
        
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active" id="email">
            <div class="container-fluid">
			  <div class="row">
			    <div class="col-md-4 mb-1">
				  <label class="p-0 m-0" for="pat_email">{{__('Patient')}}</label>
				  <input type="text" class="email_add form-control" id="pat_email">
               </div>
				<div class="col-md-4 mb-1">
				  <label class="p-0 m-0" for="doc_email">{{__('Doctor')}}</label>
				  <input type="text" class="email_add form-control" id="doc_email">
				</div>
				<div class="col-md-4 mb-1">
				  <label class="p-0 m-0" for="guarantor_email">{{__('Guarantor')}}</label>
				  <input type="text" class="email_add form-control" id="guarantor_email">
				</div>
				<div class="col-md-12 mb-1">
                 <label class="p-0 m-0" for="email_senders">{{__('Others')}}</label>
                 <select class="form-control select2_modal_email" id="email_senders"  multiple="multiple">
                   <!-- Populate sender options dynamically -->
                 </select>
                </div>
				<div class="col-md-12 mb-1">
				 <label class="p-0 m-0" for="email_subject">{{__('Subject')}}</label>
                 <input type="text" class="form-control" id="email_subject">
               </div>
               <div class="col-md-12 form-group">
                 <label class="p-0 m-0" for="emailBody">{{__('Message')}}</label>
                 <textarea class="form-control summernote_sms_email" id="email_body" rows="10"></textarea>
               </div>
			  </div>
			</div>
          </div>
          <div class="tab-pane" id="sms">
            <div class="container-fluid">
			  <div class="row">
			     <div class="col-md-4 mb-1">
					<label class="p-0 m-0" for="pat_tel">{{__('Patient')}}</label>
					<input type="text" class="phone_nb form-control" id="pat_tel">
				</div>
				<div class="col-md-4 mb-1">
					<label class="p-0 m-0" for="doc_tel">{{__('Doctor')}}</label>
					<input type="text" class="phone_nb form-control" id="doc_tel">
				</div>
				<div class="col-md-4 mb-1">
					<label class="p-0 m-0" for="guarantor_tel">{{__('Guarantor')}}</label>
					<input type="text" class="phone_nb form-control" id="guarantor_tel">
				</div>
				<div class="col-md-12 mb-1">
					<label class="p-0 m-0" for="tel_senders">{{__('Others')}}</label>
					<select class="form-control select2_modal_sms" id="tel_senders" multiple="multiple">
						<!-- Populate telephone options dynamically -->
					</select>
				</div>
				<div class="col-md-12 form-group">
					<label class="p-0 m-0" for="sms_body">Message</label>
					<textarea class="form-control" id="sms_body" rows="5"></textarea>
				</div>
			  </div>
			</div>  
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>