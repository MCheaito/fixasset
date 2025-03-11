<!-- 
 DEV APP
 Created date : 11-4-2024
-->
<div class="modal fade" id="addCodeModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Add codes to Referred Lab')}}
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
				  <table id="dataTable_add_codes" class="table table-bordered table-sm nowrap" style="width:100%;">
				    <thead>
					   <tr>
					   <th>{{__('Choose All')}}
					    <label class="mt-2 slideon slideon-xs  slideon-success">
					      <input type="checkbox" class="choose_ins_allcodes"/>
						  <span class="slideon-slider"></span>
					    </label>
					   </th>
					   <th>{{__('Order')}}</th>
					   <th>{{__('CNSS')}}</th>
					   <th>{{__('Name')}}</th>
					   <th>{{__('Referred Lab')}}</th>
					   </tr>
					</thead>
					<tbody id="ins_codes_list">
					   
					</tbody>
				  </table>
				</div>

            </div>

        </div>

    </div>

</div>