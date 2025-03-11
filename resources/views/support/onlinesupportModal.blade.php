<!--
    DEV APP
    Created date : 20-10-2022
 -->
  <div class="modal fade" id="onlinesupportModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="onlinesupportModalLabel" aria-hidden="true">
                  <div class="modal-dialog movableDialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header txt-bg text-white p-1">
						<h5 class="m-1 modal-title" id="feedbackModalLabel">{{__('Support 24/24')}}</h5>
                        <button type="button" class="text-white close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">x</span>
                        </button>
                       <!--<div><p>@php echo env('MAIL_HOST'); @endphp </p></div>-->
					   <!--<div><p>@php echo config('app.src_url'); @endphp </p></div>-->
                      </div>
                      <form id="supportForm" action="" method="">
					  
					  <div class="modal-body">
                          
						   <div class="form-group">
							<label class="badge txt-bg text-white" style="font-size:1rem;" for="inputSubject">{{__('Title')}}</label>
							<input type="text" id="inputSubject" name="inputSubject" class="form-control txt-border">
						   </div>
						   <div class="form-group">
							  <label class="badge txt-bg text-white" style="font-size:1rem;" for="txtComment"><b>{{__('Please, input your message')}}</b></label>
							  <textarea id="txtComment" name="txtComment" class="txt-border form-control" rows="4"></textarea>
							</div>
						   
						   <!--<div style="padding-top:15px;padding-bottom:5px;">
                                 <span class="badge txt-bg text-white" style="font-size:16px;"><b>{{__('Please, input your message')}}</b></span>
                           </div>
						   <div>
                              <textarea id="txtComment" name="txtComment" rows="5" class="txt-border" cols="10" style="width:100%"></textarea>
                           </div>-->	
						
                      </div>
                      <div class="modal-footer d-flex flex-row justify-content-center">
                        <div>
							
                <input  type="hidden" name="UserId"  value="{{$id}}"/>
				<input  id="sendMail" type="button" class="btn btn-action" value="{{__('Send message')}}"/>
                <button type="button" type="button" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>

						</div>
                      </div>
					  </form>
                    </div>
                  </div>
    </div>
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(function(){
	
	    $('#sendMail').off().on('click',function (e) {

        e.preventDefault();
		var txtComment = $("#txtComment").val();
		
		if(txtComment==''){
		swal.fire({text:'{{__("Please insert a message")}}',icon:'error',customClass:'w-auto'});
		return false;
		}
		
		
		$.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

          }

        });
		$.ajax({

          url: "{{ route('support',app()->getLocale()) }}",
		  
		  beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Sending email in progress...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },

          data: $('#supportForm').serialize(),
		  
		  type: "POST",

          dataType: 'json',

          success: function (data) {
			   
			                     
			  if(data.success){
			    $('#onlinesupportModal-{{$id}}').modal('hide');
			    Swal.fire({
						icon: 'success',
						toast: true,
						position: 'bottom-end',
						timer: 5000,
						showConfirmButton: false,
						title: data.success
				   });
				
			  }
			  
			  if(data.error){
			  $('#onlinesupportModal-{{$id}}').modal('hide');
               Swal.fire({
                    icon: 'error',
					toast: true,
					position: 'bottom-end',
					timer: 5000,
					showConfirmButton: false,
					title: data.error
               });
			   
			  }
			 

          },

         
      });

    });
	
	
	
	
});


</script>