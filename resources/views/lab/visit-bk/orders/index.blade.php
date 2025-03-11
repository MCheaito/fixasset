<div class="container-fluid">	
	 <div class="row mb-1">
		  	 
             <div class="col-md-4">
					<!--<label for="selectLab" class="label-size">{{__('External Lab/Doctor')}}</label>-->
					<select class="select2_data custom-select rounded-0" name="selectLab" autocomplete="off"   id="selectLab" style="width:100%;">
							@if(auth()->user()->type==2)
							<option value="">{{__('All external lab/doctor')}}</option>
							@endif
							@foreach($ext_labs as $lab)
								<option value="{{$lab->user_num}}" {{(!isset($order) && Session::has('order_ext_lab') && Session::get('order_ext_lab')==$lab->user_num )||(isset($order) && $lab->user_num==$order->ext_lab)? 'selected' : ''}}>{{$lab->full_name}}</option>
							@endforeach
                       					
					</select>
			  </div>	
			  <div class="form-group col-md-5">
				<button class="btn btn-action btn-sm" id="save_order" {{isset($order) && $order->status=='V'?'disabled':''}}>{{isset($order)?'Update':'Save'}}</button>
				<button class="btn btn-reset btn-sm" id="cancel_order" {{isset($order) && $order->status=='V'?'disabled':''}}>Reset</button>
				@if(isset($order))
				<button class="btn btn-action btn-sm" id="printOrder" {{isset($order) && $order->status=='V'?'disabled':''}}>Print Order</button>
				@endif
				@if($access_order_bills)
				<button class="btn btn-action btn-sm {{isset($order)?'':'d-none'}}" id="go_results">{{__('Go to Billing')}}</button>
			    @endif
			  </div>	  
			  
			  <div class="form-group col-md-3">
				 <h6 class="float-md-right badge badge-success txt-bg label-size" id="selected_tests">Selected tests are : 0</h6>
			  </div>
				
		  
	  </div>
      <div class="row">
	   
		<div class="form-group col-md-4" style="width:100%;">
		  <select class="select2_data custom-select rounded-0" id="filter_category" style="width:100%;" {{isset($order) && $order->status=='V'?'disabled':''}}>
			   <option value="">{{__('Choose a category')}}</option>
			  @foreach($categories as $g)
			       @php 
				   $name = $g->descrip;
			        if($g->code !=NULL && $g->code !=''){
						$name.=' '.'('.$g->code.')';
					}
					@endphp
			   <option value="{{$g->id}}">{{$name}}</option>
			  @endforeach
		  </select>
		</div>
		<div class="form-group col-md-4" style="width:100%;">
		  <select class="select2_data custom-select rounded-0" id="filter_group" style="width:100%;" {{isset($order) && $order->status=='V'?'disabled':''}}>
			   <option value="">{{__('Choose a code')}}</option>
			  @foreach($groups as $g)
			       @php 
				     $name = $g->descrip;
			          if($g->code !=NULL && $g->code !=''){
						$name.=' '.'('.$g->code.')';
					   }
					@endphp
			   <option value="{{$g->id}}">{{$name}}</option>
			  @endforeach
		  </select>
		</div>
		<div class="form-group col-md-4">
			<input type="text" class="form-control form-control-border border-width-2" id="search_test" value="" name="search" placeholder="Serach for a test..." {{isset($order) && $order->status=='V'?'disabled':''}}/>
			
		  </div>
		  
      </div>  	  
	  <div class="container-fluid">
	   <div class="row">
	    <div class="col-md-12 txt-border" style="height:350px;max-height:450px;overflow-y:auto;">
		  <form id="tests_form">
			 
			 <div id="tests_row" class="mt-1 row">
				  @php  $disabled = isset($order) && $order->status=='V'?'disabled':''; @endphp
				  <legend class="border text-center">{{__("All Codes")}}</legend>
				  
				  @foreach($tests as $t)
					
						 @php 
							$checked = isset($order_tests) && count($order_tests)>0 && in_array($t->id,$order_tests) ?  'checked': '';
							$class =   $checked=='' ?  'btn-light' : 'btn-success';
							if( (auth()->user()->type==1 || auth()->user()->type==3) && $checked=='checked'){
					           $disabled = 'disabled';
				              }
				 	    @endphp			
					  <div class="mb-2 col-md-2 col-6 content-chk">
						   <label  class="content-lbl btn {{$class}} {{$disabled}} text-nowrap" style="border:1px solid;border-radius:25% 10%;text-align:center;width:100%;max-width:100%;font-size:0.8rem;">
						   <input type="checkbox" name="test[]" {{$checked}} {{$disabled}} data-name="{{$t->test_name}}" data-group="{{$t->is_group}}" value="{{$t->id}}" class="test form-control" onclick="chkbx_fn(this)"  hidden /> 
							 {{$t->test_name}}
						   </label>
					   </div>
					  
					
				  @endforeach
				 
			  </div>
			  
		 </form>
       </div>		 
	   
     </div>
    </div>
	
</div>
						
