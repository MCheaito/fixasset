<div class="container-fluid">	
   <div class="row form-group">
	  <div class="col-md-12 text-right">
	    <button type="button" class="btn btn-action btn-sm" id="save_culture">{{__("Save")}}</button>
		<button type="button" class="btn btn-action btn-sm" id="print_culture">{{__("Print")}}</button>
	  </div>
	</div>
   <form id="culture_form">
   
   @if(isset($culture_test))
	      @php 
		    $text_result = UserHelper::getTextResults($order->id,$culture_test->id);
			
		  @endphp
	  <div class="row form-group">
	    <div class="col-md-5">
		  <label class="label-size">{{__("Test Name")}}</label>
		  <input type="text" name="culture_test_name" id="culture_test_name" class="form-control" readonly="true" value="{{$culture_test->test_name}}"/>
		  <input type="hidden" name="culture_test_id" id="culture_test_id" value="{{$culture_test->id}}"/>
		  <input type="hidden" name="culture_id" id="culture_id" value="{{isset($text_result) && isset($text_result['culture_id'])?$text_result['culture_id']:'0'}}"/>

		</div>
		<div class="col-md-7">
		   <label class="label-size">{{__("Gram Staim")}}</label>
		   <input type="text" name="gram_staim" id="gram_staim"  class="form-control" value="{{isset($text_result) && isset($text_result['gram_staim']) && $text_result['gram_staim']!=''?$text_result['gram_staim']:old('gram_staim')}}"/>
		</div>
		<div class="mt-2 mb-2 col-md-5 select2-teal">
		  <label class="label_size">{{__("Bacteria")}}</label>
		  <select class="sbacteria-multiple" name="sbacteria[]" data-dropdown-css-class="select2-teal" data-placeholder="{{__('Choose a bacteria')}}" multiple="multiple" style="width:100%">
		    @foreach($sbacteria as $b)
			 <option value="{{$b->id}}" {{isset($text_result) && isset($text_result['sbacteria']) && in_array($b->id,$text_result['sbacteria'])?'selected':''}}>{{$b->descrip}}</option>
			@endforeach
          </select> 		  
		</div>
		
		<div class="mt-2 mb-2 col-md-7">
		   <label class="label-size">{{__("Culture Urine")}}<span class="ml-1"><button type="button" data-target="#resultTextModal" data-toggle="modal" class="btn btn-icon btn-sm" title="{{__('Add a text for culture urine')}}"><i class="fa fa-plus"></i></button></label>
		   <textarea class="form-control" name="culture_urine" id="culture_urine">{{isset($text_result) && isset($text_result['culture_urine']) && $text_result['culture_urine']!=''?$text_result['culture_urine']:old('culture_urine')}}</textarea>
		</div>
		
		<div class="mt-2 mb-2 col-md-12">
		   <table  class="table-bordered table-sm" style="width:100%;">
		     <thead>
			    <tr>
				  <th>{{__("Bacteria")}}</th>
				  <th>{{__("Antibiotic")}}</th>
				  <th>{{__("Result")}}</th>
				  <th style="display:none"></th>
				  <th style="display:none"></th>
				</tr>
			 </thead>
			 <tbody id="antibioticsList">
			   @if(isset($text_result) && isset($text_result['antibiotics']))
				   @foreach($text_result['antibiotics'] as $ant)
			         <tr>
					   <td>{{$ant[0]}}</td>
					   <td>{{$ant[1]}}</td>
					   <td>
					    <select name="ant_res" class="form-control" style="width:100%">
						  <option value="S" {{$ant[4]=='S'?'selected': ''}}>S</option>
						  <option value="R" {{$ant[4]=='R'?'selected': ''}}>R</option>
						  <option value="I" {{$ant[4]=='I'?'selected': ''}}>I</option>
                        </select>
					   </td>
					   <td style="display:none"><input type="hidden" name="antibiotic_id" value="{{$ant[2]}}"/></td>
					   <td style="display:none;"><input type="hidden" name="bacteria_id" value="{{$ant[3]}}"/></td>
					 </tr>
			       @endforeach
			   @endif 	   
			 </tbody>
		   </table>
		</div>
	  </div>
   @endif
   </form>
</div>  