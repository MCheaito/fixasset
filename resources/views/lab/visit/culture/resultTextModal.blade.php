<!--Discount modal-->
			<div class="modal fade" id="resultTextModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">{{__('Choose a text for culture urine')}}</h4>
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<input type="hidden" name="modal_cult_id"/>
							</div>
							<div class="modal-body">
								    <form id="tst_result_form">
									<div class="container">
						               
									   <div class="row">
									     <div class="col-md-12">
										   <table id="tst_results_tbl" class="table-bordered" style="width:100%;">
										       <thead>
											    <tr>
												  <th>{{__('Choose All')}}<span class="ml-1"><input type="checkbox" class="chk-all-text"/></th>
												  <th>{{__('Name')}}</th>
												</tr>
												</thead>
												<tbody>
												@foreach($test_textResults as $r)
												 <tr>
												   <td><input type="checkbox" class="chk-one-text"/></td>
												   <td>{{$r->name}}</td>
												 </tr>
												@endforeach
											    </tbody>
										   </table>
										 </div>
									   </div> 
								 </div>
								 </form>
							</div>
		</div>		
    </div>		
</div>					
<!--end DISCOUNTModal-->	  	  