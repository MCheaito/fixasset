@php 
$access_save_results = UserHelper::can_access(auth()->user(),'save_results');
$access_send_results = UserHelper::can_access(auth()->user(),'send_results');
@endphp
<div class="container-fluid">	
 
  <div class="row">	
		   
		  
			
		
	   <div class="col-md-3">	
				<div style="font-size:1.2em;">
					<span class="badge text-white bgblue">{{__("Low")}}</span>
					<span class="ml-2 badge text-white bginfo">{{__("Normal")}}</span>
				 </div>
				 <div style="font-size:1.2em;">
					<span class="badge text-white bgred">{{__("High")}}</span>
					<span class="ml-2 badge text-white bgorange">{{__("Panic")}}</span>	   
				 </div>
			   @if(isset($order))
				@php 
			        $order_status = '';
					switch($order->status){
						case 'P': $order_status=__('Pending');   break;
						case 'F': $order_status=__('Finished');  break;
						case 'V': $order_status=__('Validated'); break;
					}
			    @endphp
			    <div class="mt-1" style="font-size:1em;">
				 <div class="input-group">
				 <div class="input-group-prepend"><label class="input-group-text label-size">{{__("Status")}}</label></div>
				 <input type="text" readonly="true" id="order_status_name" name="order_status_name" class="form-control" value="{{$order_status}}"/>
				 </div>	   
				</div>
              @endif
			  @if($access_validate_results)
			     <div class="mt-1">	
				  <label class="label-size">{{__("Validate")}}</label>
				  <label class="mt-2 slideon slideon-xs  slideon-success">
				  <input type="checkbox" id="validateResults" class="toggle-chk" {{isset($order) && $order->status=='V'?'checked':''}} {{isset($order) && $order->status!='V' && $order->status!='F'?'disabled':''}}/>
				  <span class="slideon-slider"></span>
				  </label>              
				</div>
			  @endif
	          <div class="mt-1">
				@if($access_save_results)
				<button id="saveResults" class="m-1 btn btn-action btn-sm" {{isset($order) && $order->status!='V'?'':'disabled'}} >{{__('Save')}}</button>
				@endif
				
				@if(auth()->user()->type==2)
				<button id="doneResults" class="m-1 btn btn-action btn-sm" {{isset($order) && $order->status!='F' && $order->status!='V'?'':'disabled'}}>{{__('Result Done')}}</button>
				@endif
				
				@if($access_send_results)
				<button id="sendResults" class="m-1 btn btn-action btn-sm" {{isset($order) && $order->status=='V'?'':'disabled'}}>{{__('Email')}}</button>
				@endif
				
				<button id="printResults" class="m-1 btn btn-action btn-sm">{{__('Print')}}</button>
				
	          </div>
			  <div class="mt-2">
		      <label class="label-size">{{__("Global comment")}}</label>
			  <textarea name="fixed_comment" class="form-control" rows="3" id="fixed_comment">{{isset($order)&& isset($order->fixed_comment) && $order->fixed_comment!=''?$order->fixed_comment:old('fixed_comment')}}</textarea>
		   </div>
	     </div>
		 	
		 <div class="col-md-9">
		   <div id="example-table" class="table-bordered table-sm" style="font-size:0.85rem;"></div>
		   
	   </div>
   </div>
</div>										
