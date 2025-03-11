<div class="container-fluid">	
	 <div class="row mb-1">
		  	 
			
			  <div class="form-group col-md-12 text-right">
					
					<button type="button" class="btn btn-action" id="testsListBtn" data-target="#orderTestsModal" data-toggle="modal" {{isset($order) && $order->status=='V'?'disabled':''}}><i class="mr-1 fa fa-plus"></i>{{__('Tests')}}</button>
					
					@if(isset($order))
					<button type="button" class="btn btn-action" id="printOrder" {{isset($order) && $order->status=='V'?'disabled':''}}>Print Order</button>
					@endif
					@if($view_bills)
					<button type="button" class="btn btn-action {{isset($order)?'':'d-none'}}" id="go_results">{{__('Go to Billing')}}</button>
					@endif
					<!--<input type="hidden" id="chk_tsts" value="{{implode(',',$order_tests)}}"/>-->
			  </div>	  
			  
			    
			  <div id="chosen_tests" class="form-group col-md-12">	 
				  <table id="tsts_tbl" class="table-bordered display compact" style="width:100%">
				     <thead>
					    <tr>
						   <th>{{__('#')}}</th>
						   <th>{{__('Category')}}</th>
						   <th>{{__('Test Name')}}</th>
						   <th>{{__('Referred Labs')}}</th>
						   <th>{{__('Insert Date')}}</th>
						   <th>{{__('User')}}</th>
						</tr>
					 </thead>
					 <tbody>
					 </tbody>
				  </table>
				
			  </div>
				
		  
	  </div>
        
	
	
</div>
						
