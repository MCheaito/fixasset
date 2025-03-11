<!-- Modal -->
<div class="modal fade" id="orderTestsModal" tabindex="-1" role="dialog" aria-labelledby="orderTestsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding-top: 0.3rem;padding-bottom:0.3rem;">
        <h5>{{__('All Tests')}}</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 0.3rem;padding-bottom:0.3rem;">
       
		<div class="container-fluid mb-1">
		   <div class="row">
			   <div class="form-group col-md-3" style="width:100%;" style="padding-bottom:3px;">
				   <select class="select2_data_modal custom-select rounded-0" id="filter_category" style="width:100%;">
					   <option value="">{{__('Choose a category')}}</option>
					  
					   <option value="-1">{{('Custom Tests')}}</option> 
					
					  @foreach($categories as $g)
					   <option value="{{$g->id}}">{{$g->descrip}}</option>
					  @endforeach
				   </select>
				 </div>
				 <div class="form-group col-md-4" style="width:100%;" style="padding-bottom:3px;">
				   <select class="select2_data_modal custom-select rounded-0" id="filter_group" style="width:100%;">
					   <option value="">{{__('Choose a group')}}</option>
					  @foreach($groups as $g)
						   @php 
							 $name = $g->test_name;
							  if($g->descrip !=NULL && $g->descrip !=''){
								$name.=' '.'['.$g->descrip.']';
							   }
							@endphp
					   <option value="{{$g->id}}">{{$name}}</option>
					  @endforeach
				   </select>
				 </div>
				  <div class="form-group col-md-5 select2-success" style="padding-bottom:3px;">
						  <select name="profile_tests[]" id="profile_tests" class="select2_multiple form-control" multiple="multiple"  data-placeholder="{{__('Choose a profile')}}" data-dropdown-css-class="select2-success"   style="width:100%;">
							@foreach($profiles as $p)
							  <option value="{{$p->id}}">{{$p->profile_name}}</option>
							@endforeach
						  </select>
				  </div>
				  <div class="form-group col-md-4" style="padding-bottom:3px;">
				       <h6 class="badge badge-success" id="selected_tests" style="font-size:1rem;">Selected tests are : 0</h6>
			      </div> 
				  <div class="form-group col-md-5" style="padding-bottom:3px;">
			        <input type="text" class="form-control form-control-border border-width-2" id="search_test" value="" name="search" placeholder="Search for a test..."/>
			      </div> 
                  <div class="form-group col-md-3 text-right" style="padding-bottom:3px;">				  
						<input type="hidden" id="guarantor_patient_num"/>
						<button type="button" class="btn btn-action btn-md" id="save_order">{{__('Save')}}</button>
						<button type="button" class="btn btn-reset btn-md" id="cancel_order">{{__('Reset')}}</button>
                   </div>
		      </div>
		</div>	  
      <form id="testsForm">
		<div class="container-fluid">
			   
			   <div class="row">
  				<div class="col-md-12">
				   
						 @foreach($categories as $c)  
						 <div id="tests_row" class="mt-1 row">
						     
						     @php 
						       $cat_tests = $tests->where('category_num',$c->id)->sortBy('testord')->all();
						     @endphp
					         <div class="col-md-12 content-cat" id="cat{{$c->id}}">
							   <Legend style="padding:0;margin:0;font-size:16px;"><b><u>{{$c->descrip}}</u></b></legend>
							 </div>
							@foreach($cat_tests as $t)
									
							   <div class="col-md-2  content-chk">
								   <label  class="content-lbl btn btn-light  text-nowrap" title="{{$t->test_name}}" style="border:1px solid;border-radius:25% 10%;text-align:center;width:100%;max-width:100%;font-size:0.7rem;">
								   <input type="checkbox" name="test[]"  data-name="{{$t->test_name}}"  data-group="{{$t->is_group}}" data-referredLAB="{{$t->referred_tests}}"  value="{{$t->id}}" class="test form-control" onclick="chkbx_fn(this)"  hidden /> 
									 {{$t->test_name}}
								   </label>
							   </div>
							
						    @endforeach
							 
						</div>
					  @endforeach
				    
			   </div>		 
			   
			 </div>
		  </div>
		  </form>
      </div>
     
    </div>
  </div>
</div>