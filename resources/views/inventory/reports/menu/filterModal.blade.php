<!--
    DEV APP
    Created date : 13-5-2023
 -->
<!--add new event modal-->
	<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('Filter data')}}</h4>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{__('Close')}}</span></button>

            </div>
			<div class="modal-body">
                <form id="filter-form">
					<div class="row m-1">
					   <div class=" text-right  col-md-12">
					      <input type="hidden" id="report_type" name="type"/>
						  <button id="go" class="btn btn-action">{{__("OK")}}</button>
						  <button type="reset" data-dismiss="modal" class="btn btn-delete">{{__("Cancel")}}</button>
						</div>
					</div>		 
					<div class="row m-1">
							 <div class="form-group col-md-12">
							   <label class="label-size">
								@switch(auth()->user()->type)
								 @case(1) {{__('Resource')}} @break
								 @case(2) {{__('Branch')}} @break
								@endswitch 
							   </label>
							   <input type="text" class="form-control form-control-border" value="{{$resource->full_name}}" disabled />
							 </div>
							 <div class="form-group col-md-6">
								<label for="name" class="label-size">{{__('From date')}}</label>
								<input autocomplete="false" type="text" class="date_range form-control" name="filter_fromdate" id="filter_fromdate"  placeholder="{{__('Choose a date')}}"/>
							</div>
							<div class="form-group col-md-6">
								<label for="name" class="label-size">{{__('To date')}}</label>
								<input autocomplete="false" type="text" class="date_range form-control" name="filter_todate" id="filter_todate"  placeholder="{{__('Choose a date')}}"/>
							</div>
							<div id="patients" class="form-group col-md-6">
							   <label class="label-size">{{__('Patient')}}</label>
							   <select class="select2_modal custom-select rounded-0" name="filter_patient" id="filter_patient" style="width:100%">
								 <option value="0">{{__('Choose a patient')}}</option>
								 @foreach($patients as $p)
								   <option value="{{$p->id}}">{{$p->first_name.' '.$p->last_name}}</option>
								 @endforeach
							   </select>
							 </div>
						   <div id="items" class="form-group col-md-6">
						    <label class="label-size">{{__('Items')}}</label>
							   <select class="select2_modal custom-select rounded-0" name="filter_item" id="filter_item">
								 <option value="0">{{__('Choose an item')}}</option>
								 @foreach($items as $i)
								   <option value="{{$i->id}}">{{$i->name}}</option>
								 @endforeach
							   </select>
						   </div>
						 <div id="suppliers" class="form-group col-md-6">
						   <label class="label-size">{{__('Supplier')}}</label>
						   <select class="select2_modal custom-select rounded-0" name="filter_supplier" id="filter_supplier" style="width:100%;">
							 <option value="0">{{__('Choose a supplier')}}</option>
							 @foreach($suppliers as $s)
							   <option value="{{$s->id}}">{{$s->name}}</option>
							 @endforeach
						   </select>
						 </div>
			              <div id="pay_refund" class="form-group col-md-6">
							   <label class="label-size">{{__('Payment/Refund')}}</label>
							   <select class="custom-select rounded-0" name="filter_payment" id="filter_payment">
								 <option value="0">{{__('Choose a payment reference')}}</option>
								 <option value="P">{{__('Payment')}}</option>
								 <option value="R">{{__('Refund')}}</option>
							   </select>
							 </div>
						 <div id="pay_types" class="form-group col-md-6">
						   <label class="label-size">{{__('Type')}}</label>
						   <select class="select2_modal custom-select rounded-0" name="filter_payment_type" id="filter_payment_type" style="width:100%;">
							 <option value="0">{{__('Choose a payment type')}}</option>
							@foreach($payment_types as $pt)
							  <option value="{{$pt->id}}">{{(app()->getLocale()=='en')?$pt->name_eng:$pt->name_fr}}</option>
							@endforeach  
						   </select>
						 </div>
                 </div>	   
                </form>
            </div>
           
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
