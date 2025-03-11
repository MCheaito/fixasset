<!--
    DEV APP
    Created date :15-9-2023
 -->
  <div class="modal fade" id="zoomlinkModal" tabindex="-1" role="dialog" aria-labelledby="zoomlinkModalModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                      <div class="modal-header txt-bg text-white p-1">
						<h5 class="m-1 modal-title" id="feedbackModalLabel">{{__('Remote connection')}}</h5>
                        <button type="button" class="text-white close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">x</span>
                        </button>
                      </div>
                      
					  <div class="modal-body">
                         <div class="row table-responsive"> 
						  <table class="table table-bordered table-sm table-striped" style="width:100%">
						     <thead>
							   <tr>
							   <th>#</th>
							   <th class="text-center">{{__("Click here to open the session")}}</th>
							   </tr>
							 </thead>
							 <tbody id="links_table">
							   @php 
							   $cnt=0; 
							   $zoom_links = UserHelper::get_zoom_links();
							   @endphp
							   @if($zoom_links->count()>0)
								   @foreach($zoom_links as $l)
								   <tr>
									 <td>{{++$cnt}}</td>
									 <td class="text-center"><a href="{{$l->link}}" target="_blank">{{$l->link}}</a></td>
								   </tr>
								   @endforeach
							   @else
								   <tr>
							         <td colspan="3" class="text-center">{{__("No data is found")}}</td>
							       </tr>
                               @endif								   
							 </tbody>
						  </table>
                         </div>
						 <hr/>
						<form id="zoomLinkForm" action="" method="">
					   	 <div class="row">
						   <div class="col-md-8">
						     <label class="label-size" for="name">{{__("Copy your link here")}}</label>
							 <textarea class="form-control" name="link_name" id="link_name"></textarea>
						   </div>
						   <div class="col-md-4">
						      <button  id="saveLink" type="button" class="mt-4 float-right btn btn-action">{{__('Save')}}</button>
						   </div>
						 </div>
						</form>
					  </div>
                      <div class="modal-footer">
						<button type="button" type="button" class="m-1 float-right btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					 
                    </div>
                  </div>
    </div>
<script>
$(function(){
	$('#saveLink').off().on('click',function (e) {
		e.preventDefault();
		var txtComment = $("#link_name").val();
		if(txtComment==''){
		swal.fire({text:'{{__("Please input a link")}}',icon:'error',customClass:'w-auto'});
		return false;
		}
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
          url: "{{ route('insert_remote_link',app()->getLocale()) }}",
		  data: $('#zoomLinkForm').serialize(),
	      type: "POST",
          dataType: 'json',
		success: function (data) {
		 $('#links_table').empty();	
		 $('#links_table').html(data.html);
		 $("#link_name").val('');
		 Swal.fire({toast:true,title:'{{__("Saved successfully")}}',icon:'success',showConfirmButton:false,timer:1500,position:'bottom-right'});
		}
		});
	});
});
</script>