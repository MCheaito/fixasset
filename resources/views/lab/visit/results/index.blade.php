
<div class="container-fluid">	
  <div class="row">			
	      	
		    <div class="col-md-3" style="font-size:1.3em;">
					<span class="badge text-white bgblue">{{__("Low")}}</span>
					<span class="ml-2 badge text-white bginfo">{{__("Normal")}}</span>
					<span class="ml-2 badge text-white bgred">{{__("High")}}</span>
					<span class="ml-2 badge text-white bgorange">{{__("Panic")}}</span>	   
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
			    <div>
				  
				  @if($edit_results)
				    <button type="button" id="editResults" class="m-1 btn btn-action" {{isset($order) && ($order->status=='V')?'disabled':''}}>{{__('Edit')}}</button>
				    <button type="button" id="cancelResults" class="m-1 btn btn-reset" disabled>{{__('Cancel')}}</button>
			        @if(isset($order) && $order->is_trial=='N')
					<button type="button" id="importResults" class="m-1 btn btn-action" disabled>{{__('Import')}}</button>
					@endif
				  @endif
				</div>
			 </div>
			 <div class="col-md-4">
		        <label class="label-size">{{__("Global comment")}}</label>
			    <textarea name="fixed_comment" class="form-control" rows="1" id="fixed_comment" disabled>{{isset($order)&& isset($order->fixed_comment) && $order->fixed_comment!=''?$order->fixed_comment:old('fixed_comment')}}</textarea>
		     </div>
			     
			  
			  <div class="col-md-4 text-right">
			   	   
				   <button type="button" id="printResults" class="m-1 btn btn-action">{{__('Print')}}</button>
				    <!--make results done when they need check and only for lab users-->
			       <!--only user that has validate result can do it-->
				    <!--@if($validate_results && $valid_tests_cnt>0)-->
				    <!--open modal to validate tests only if there is tests to validate-->
				    <!--<button type="button" id="validateAllTests" class="m-1 btn btn-action btn-sm" {{isset($order) && ($order->status=='F')?'':'disabled'}}>{{__('Validate')}}</button>
					@endif-->	 
				   <button type="button" id="sendResults" class="m-1 btn btn-action" {{isset($order) && ($order->status=='V')?'':'disabled'}}>{{__('Email/SMS')}}</button>
				   <button type="button" id="sendResultsInfo" title="{{__('Email/SMS history')}}" class="m-1 btn btn-action" onclick="event.preventDefault();getSMSEmailInfo()" {{isset($order) && ($order->status!='P')?'':'disabled'}}><i class="fa fa-exclamation-circle"></i></button>
				   <input type="hidden" id="valid_tests_cnt" value="{{$valid_tests_cnt>0?$valid_tests_cnt:0}}"/>
				   <button type="button" id="invalidateAllTests" style="display:none;" class="m-1 btn btn-action">{{__('Invalidate')}}</button>
			       
			  </div>
			   <div class="col-md-1">
			       <label style="width:60px;">
				       {{__('Done')}}
					   <label class="slideon slideon-xs  slideon-success">
					   <input type="checkbox" id="doneResults" class="toggle-chk"  {{isset($order) && ($order->status=='V')?'disabled':''}} {{isset($order) && ($order->status=='F' || $order->status=='V')?'checked':''}} />
					   <span class="slideon-slider"></span>
					   </label>
				   </label> 
			  </div>
			  
		 <div class="col-md-6"></div>
          <div class="col-md-6 text-right">
			<label class="mr-1 label-size">{{__("Search").' : '}}</label>
			<input type="text" id="search_results"/>
		  </div>		 
		 <div class="col-md-12" style="max-height: 500px;overflow-y: auto;overflow-x: auto;display: block;">
		   <table id="example-table" class="table table-bordered table-sm" style="border-collapse: collapse;width:100%;">
		     <thead>
			  
			   <tr>
				 <th style="display:none;">{{__('#')}}</th>
				 <th style="display:none;">{{__('Order')}}</th>
				 <th>{{__('Test Name')}}</th>
				 <th>{{__('Result')}}</th>
				 @if($validate_results && $valid_tests_cnt>0)
			     <th><input type="checkbox" class="mr-1" id="validateAllRslts" {{isset($order) && ($order->status=='V')?'checked':''}} {{isset($order) && ($order->status=='F')?'':'disabled'}}/>{{__('Validate All')}}</th>
				 @endif
				 <th>{{__('Sign')}}</th>
				 <th>{{__('Ref. Range')}}</th>
				 <th>{{__('Previous result')}}</th>
				 <td style="display:none;">{{__('Status')}}</td>
				 <td style="display:none;">{{__('Type')}}</td>
				 <td style="display:none;">{{__('Result Status')}}</td>
				 <td style="display:none;">{{__('Test id')}}</td>
				 <td style="display:none;">{{__('Is printed')}}</td>
				 <td style="display:none;">{{__('Dec. Pts')}}</td>
				 <td style="display:none;">{{__('Calc result')}}</td>
				 <td style="display:none;">{{__('Calc unit')}}</td>
				 <td style="display:none;">{{__('Field#')}}</td>
			   </tr>
			 </thead>
			 <tbody>
			 @foreach($results as $group=>$result)
			   <tr>
			   <th colspan="{{$validate_results && $valid_tests_cnt>0?17:16}}" style="background-color:#0000001a;">{{$group}}</th>
			   </tr>
			  @foreach($result as $r)
			   @php
			     $cl="bginfo";
				 switch($r->result_status){
					 case 'H': $cl="bgred"; break;
					 case 'L': $cl="bgblue"; break;
					 case 'PL': case 'PH': $cl="bgorange"; break;
				    }
			     $formula = UserHelper::getFormula($r->test_id,$patient_age,$patient_gender);
				 $suggestions = UserHelper::getTestSuggestions($r->test_id);
			   @endphp
			    @if($r->is_title=='Y')
				   <tr><td colspan="{{$validate_results && $valid_tests_cnt>0?17:16}}"><b>{{$r->test_name}}</b></td></tr>
			    @else
			   
				   @if( ($r->test_type=='F'|| $r->test_type=='C') && !empty($formula))
					   <tr data-id="{{$r->id}}" data-test-id="{{$r->test_id}}" data-formula="{{$formula['formula']}}" 
						   data-code1="{{$formula['code1']}}" data-code2="{{$formula['code2']}}"
						   data-code3="{{$formula['code3']}}" data-code4="{{$formula['code4']}}">
						 <td class="result_id" style="display:none;">{{$r->id}}</td>
						 <td style="display:none;">{{$r->position}}</td>
						 <td>{{$r->test_name}}</td>
						 <td><input type="text" class="resultVal form-control {{$cl}}" value="{{$r->result}}" disabled /></td>
						 @if($validate_results && $valid_tests_cnt>0)
						 <td><input type="checkbox" data-type="result" class="validateOneRslt" {{$r->need_validation=='Y'?'':'checked'}} {{isset($order) && ($order->status=='F')?'':'disabled'}}/></td>
						 @endif
						 <td class="sign">{{$r->result_sign}}</td>
						 <td class="ref_range">{!!$r->one_ref_range!!}</td>
						 <td>{{UserHelper::getPreviousResult($r->prev_result_num)}}</td>
						 <td style="display:none;">{{$r->status}}</td>
						 <td class="test_type" style="display:none;">{{$r->test_type}}</td>
						 <td class="result_status" style="display:none;">{{$r->result_status}}</td>
						 <td class="test_id" style="display:none;">{{$r->test_id}}</td>
						 <td style="display:none;">{{$r->is_printed}}</td>
						 <td class="dec_pts" style="display:none;">{{$r->dec_pts}}</td>
						 <td class="calc_result" style="display:none;">{{$r->calc_result}}</td>
						 <td style="display:none;">{{$r->calc_unit}}</td>
						 <td class="field_num" style="display:none;">{{$r->field_num}}</td>
						</tr> 
				   @else
						<tr data-id="{{$r->id}}" data-test-id="{{$r->test_id}}">
						 <td class="result_id" style="display:none;">{{$r->id}}</td>
						 <td style="display:none;">{{$r->group_name}}</td>
						 <td style="display:none;">{{$r->position}}</td>
						 <td>{{$r->test_name}}</td>
						 <td>
							@if( (isset($r->result_txt)&& $r->result_txt!='') ||  $suggestions->count()>0) 
							  <input type="text" class="resultVal form-control" list="myList{{$r->id}}"  value="{{$r->result_txt}}" disabled />
							  <datalist id="myList{{$r->id}}">
								@foreach($suggestions as $s)
								<option value="{{$s->name}}"></option>
								@endforeach
							  </datalist>
							 @else					 
							  <input type="text" class="resultVal form-control {{$cl}}" value="{{$r->result}}" disabled />
							 @endif	 
							
						 </td>
						
						 @if($validate_results && $valid_tests_cnt>0)
						  <td><input type="checkbox" data-type="result" class="validateOneRslt" {{$r->need_validation=='Y'?'':'checked'}} {{isset($order) && ($order->status=='F')?'':'disabled'}}/></td>
						 @endif
							 
						 <td class="sign">{{$r->result_sign}}</td>
						 <td class="ref_range">{!!$r->one_ref_range!!}</td>
						 <td>{{UserHelper::getPreviousResult($r->prev_result_num)}}</td>
						 <td style="display:none;">{{$r->status}}</td>
						 <td class="test_type" style="display:none;">{{$r->test_type}}</td>
						 <td class="result_status" style="display:none;">{{$r->result_status}}</td>
						 <td class="test_id" style="display:none;">{{$r->test_id}}</td>
						 <td style="display:none;">{{$r->is_printed}}</td>
						 <td class="dec_pts" style="display:none;">{{$r->dec_pts}}</td>
						 <td class="calc_result" style="display:none;">{{$r->calc_result}}</td>
						 <td style="display:none;">{{$r->calc_unit}}</td>
						 <td class="field_num" style="display:none;">{{$r->field_num}}</td>
						</tr> 
				   
				   @endif	   
			     @endif
			   @endforeach
			  @endforeach
			  @if($view_culture && $culture_test->count())
			     <tr>
			      <th colspan="{{$validate_results && $valid_tests_cnt>0?17:16}}" style="background-color:#0000001a;">{{__('Culture')}}</th>
			     </tr>
				  @foreach($culture_test as $t)
					   <tr data-id="{{$t->id}}">
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td>{{$t->test_name}}</td>
						  @if($validate_results && $valid_tests_cnt>0)
							 <td></td>
							 <td><input type="checkbox" data-type="culture" class="validateOneRslt" {{$r->need_validation=='Y'?'':'checked'}} {{isset($order) && ($order->status=='F')?'':'disabled'}}/></td>
						  @endif
							 <td colspan="3"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
					   </tr>
				  @endforeach
			  @endif
			  @if($view_templates && $template_reports->count())
			     <tr>
			      <th colspan="{{$validate_results && $valid_tests_cnt>0?17:16}}" style="background-color:#0000001a;">{{__('Template')}}</th>
			     </tr>
				  @foreach($template_reports as $t)
					   <tr data-id="{{$t->id}}">
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td>{{$t->test_name}}</td>
							 <td></td>
						  @if($validate_results && $valid_tests_cnt>0)
							 <td><input type="checkbox" data-type="template" class="validateOneRslt" {{$r->need_validation=='Y'?'':'checked'}} {{isset($order) && ($order->status=='F')?'':'disabled'}}/></td>
						  @endif
							 <td colspan="3"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
							 <td style="display:none;"></td>
					   </tr>
				  @endforeach
			  @endif
			 </tbody>
		   </table>
		   
	   </div>
   </div>
</div>										
