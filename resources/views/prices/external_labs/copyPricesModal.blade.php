<!-- 
 DEV APP
 Created date : 23-3-2023
-->
<div class="modal fade" id="copyPricesModal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Copy prices to Category/Guarantors')}}
				<span class="ml-3 badge badge-info">{{__('Note: The limited number of Guarantors to copy prices is 10')}}</span>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
				</button>
            </div>
            <div class="modal-body">
                <form id="copypricesForm">
                  @csrf
				 		  
				<div class="row">	 
				      <div class="form-group col-md-3">
					    <label for="name" class="label-size">{{__('Copy from')}}</label>
					    <input type="text" disabled class="form-control" name="lab_name" value="{{$pr->full_name}}"/>
						<input type="hidden" name="modal_price_id" value="{{$pr->id}}"/>
						<input type="hidden" name="testsCopy" id="testsCopy"/>
					  </div>
					  <div class="form-group col-md-4">
					    <label for="name" class="label-size">{{__('Category')}}</label>
						<select  id="modal_cat" name="modal_cat" class="form-control">
						   <option value="">{{__('Choose a category')}}</option>
							@foreach($cats as $c)
							  <option value="{{$c->id}}">{{app()->getLocale()=='en'?$c->name_en:$c->name_fr}}</option>
							@endforeach
						</select>
					  </div>
					  <div class="form-group col-md-3">
					      <label for="name" class="label-size">{{__('Search')}}</label>
						  <input type="text" class="form-control" id="search-input"/>
					  </div>
					  <div class="form-group  col-md-2 text-right">
						 <button  class="m-1 btn btn-action" onclick="event.preventDefault();copyPrices();">{{__('Copy')}}</button>
						 <button type="reset" class="m-1 btn btn-reset" data-dismiss="modal">{{__('Close')}}</button>
                      </div>
					 </div>
					  <div class="row">
						   <div class="form-group col-md-3">
							<label class="label-size" style="font-size:1rem;">{{__('Select All')}}</label>
							 <input type="checkbox" class="choose10" style="width:20px;height:20px;" />
						  </div>
					  </div>		
					   <div class="row">
						  @foreach($other_ext_labs as $o)
							
							<div class="form-group col-md-4 col-6 checkbox-container">
							  <input type="checkbox" name="ext_lab[]" class="ml-2 extLAB" data-category="{{$o->category}}" value="{{$o->id}}"/><label class="label-size ml-2">{{$o->full_name}}</label>
							</div>
						  @endforeach
					  </div>
				   
               </form>

            </div>

        </div>

    </div>

</div>
