@extends('gui.main_gui')
@section('styles')
<style>
 .select2-container .select2-selection--multiple .select2-selection__rendered {
    overflow-y: auto;
    max-height: 200px;
  }
</style>
@endsection
@section('content')
<div class="container-fluid">
   <!--begin group,code card-->
   <div class="card card-outline">	  
	  <div class="card-header card-menu">
		 <div class="container-fluid">
			<div class="row">
			   <div class="col-md-4 col-6">
			     <h4 class="text-dark">{{isset($test)?__("Edit code"):__("New code")}}</h4>
			   </div> 	 
			   <div class="col-md-8 text-right col-6">
						@if(isset($test))
						<a class="m-1 btn btn-action" href="{{route('lab.tests.new',app()->getLocale())}}"><i class="mr-1 fa fa-plus"></i>{{__("New Code")}}</a>
						@endif
						<a class="m-1 btn btn-action" href="{{route('lab.tests.index',app()->getLocale())}}">{{__("Back")}}</a>
			   			<button type="button" class="m-1 btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
			   </div>
			  
			</div>
		  </div>	
	  </div>
      <div class="card-body p-0">
	      <div class="container-fluid" style="padding-right:1px;padding-left:1px;">
		     <form id="extLabForm">
                <div class="m-1 row"> 
				      <input type="hidden" name="tst_id" id="tst_id" value="{{isset($test)?$test->id:0}}"/>
				      <div class="col-md-5">
					    <table style="width:100%;">
						   <thead class="text-center">	
							 <tr>
							  <td>
								<label for="name" class="label-size">{{__("Group")}}</label>
							  </td>
							  <td>
								<label for="name" class="label-size">{{__("Culture")}}</label>
							  </td>
							  <td>
								<label for="name" class="label-size">{{__("Custom Test")}}</label>
							  </td>
							  <td>
								<label for="name" class="label-size">{{__("Print All Normal Values")}}</label>
							  </td>
							  <td id="is_valid_th">
								<label for="name" class="label-size">{{__("to Check?")}}</label>
							  </td>
							 </tr>
							</thead>
							<tbody class="text-center">
							<tr>
							<td>
								<label class="slideon slideon-xs slideon-success">
								  <input type="checkbox" name="is_group" id="is_group" {{isset($test) && isset($test->is_group) && $test->is_group=='Y'?'checked':''}}/>
								  <span class="slideon-slider"></span>
								</label>
							</td>
							<td>
							  <label class="slideon slideon-xs slideon-success">
							   <input type="checkbox" name="is_culture" id="is_culture" {{isset($test) && isset($test->is_culture) && $test->is_culture=='Y'?'checked':''}} />
							   <span class="slideon-slider"></span>
							  </label>
							</td>
							<td>
							 <label class="slideon slideon-xs slideon-success">
							  <input  type="checkbox" name="custom_test" id="custom_test" {{isset($test) && isset($test->custom_test) && $test->custom_test=='Y'?'checked':''}} />
							  <span class="slideon-slider"></span>
							 </label>
							</td>
							<td>
							 <label class="slideon slideon-xs slideon-success">
							  <input  type="checkbox" name="is_code_printed" id="is_code_printed" {{isset($test) && isset($test->is_printed) && $test->is_printed=='Y'?'checked':''}} />
							  <span class="slideon-slider"></span>
							 </label>
							</td>
							<td id="is_valid_td">
							 <label class="slideon slideon-xs slideon-success">
							  <input  type="checkbox" name="is_valid" id="is_valid" {{isset($test) && isset($test->is_valid) && $test->is_valid=='Y'?'checked':''}} />
							  <span class="slideon-slider"></span>
							 </label>
							</td>
							</tr>
							</tbody>
						</table>
					  </div>
					 
					  <div class="col-md-3">
						  <label  for="name" class="label-size">{{__('Category')}}</label>
						  <select  id="category_num" name="category_num" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a group category")}}</option>
							@foreach($categories as $g)
							  @php $select_cat=isset($test) && isset($test->category_num) && $test->category_num==$g->id?'selected':''; @endphp
							  <option {{$select_cat}} value="{{$g->id}}">{{$g->descrip}}</option>
							@endforeach
						  </select>
					  </div>
					  <div class="col-md-4">
					    <label  for="name" class="label-size">{{__('Name').' *'}}</label>
						<input type="text" id="test_name" class="form-control" name="test_name" value="{{isset($test) && isset($test->test_name)?$test->test_name:old('test_name')}}"/>
					  </div>
					   <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('CNSS').'*'}}</label>
						<input type="text" id="cnss" name="cnss"  class="form-control" value="{{isset($test) && isset($test->cnss)?$test->cnss:old('cnss')}}"/> 
           			  </div>
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('NBL')}}</label>
						<input type="number" id="nbl" name="nbl"  class="form-control" onkeypress="return isNumberKey(event)" value="{{isset($test) && isset($test->nbl)?$test->nbl:old('nbl')}}"/> 
           		     </div>
					 
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Code')}}</label>
						<input type="text" id="test_code" name="test_code"  class="form-control" value="{{isset($test) && isset($test->test_code)?$test->test_code:old('test_code')}}"/> 
           			  </div>
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Price')}}</label>
						<input type="number" step="0.01" id="price" name="price"  class="form-control" onkeypress="return isNumberKey(event)" value="{{isset($test) && isset($test->price)?$test->price:old('price')}}"/> 
           			  </div>	  
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Normal Value')}}</label>
						<input type="text" id="normal_value" name="normal_value"  class="form-control" value="{{isset($test) && isset($test->normal_value)?$test->normal_value:old('normal_value')}}"/> 
           			  </div>
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('LisCode')}}</label>
						<input type="text" id="listcode" name="listcode"  class="form-control" value="{{isset($test) && isset($test->listcode)?$test->listcode:old('listcode')}}"/> 
           		      </div>
					  <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Order')}}</label>
						<input type="number" id="testord" min="0" name="testord"  class="form-control"  onkeypress="return isNumberKey(event)" value="{{isset($test) && isset($test->testord)?$test->testord:old('testord')}}"/> 
					  </div>
					
					 
					 <div class="col-md-4 col-6">
					    <label  for="name" class="label-size">{{__('Referred labs')}}</label>
						<select  id="referred_tests" name="referred_tests" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a referred test")}}</option>
							@foreach($ext_labs as $g)
							  @php $select_rf = isset($test) && isset($test->referred_tests) && $test->referred_tests==$g->id?'selected':''; @endphp
							  <option {{$select_rf}} value="{{$g->id}}">{{$g->full_name}}</option>
							@endforeach
						</select>
					 </div>
					 <div class="col-md-2 col-6">
					    <label  for="name" class="label-size">{{__('Storage T°')}}</label>
						<select id="storage" name="storage" class="form-control">
						  <option value="">{{__("Choose")}}</option>
						   <option  value="Refrigerated" {{isset($test) && isset($test->storage) && $test->storage=="Refrigerated"?"selected":""}}>{{__("Refrigerated")}}</option>
						   <option value="Frozen" {{isset($test) && isset($test->storage) && $test->storage=="Frozen"?"selected":""}}>{{__("Frozen")}}</option>
						   <option value="Storage Temp." {{isset($test) && isset($test->storage) && $test->storage=="Storage Temp."?"selected":""}}>{{__("Storage Temp.")}}</option>
						</select>
					  </div>
					   <div class="col-md-2 col-6">
					    <label for="name" class="label-size">{{__('TAT hrs')}}</label>
						<input type="number" id="tat_hrs" name="tat_hrs"  class="form-control" onkeypress="return isNumberKey(event)" value="{{isset($test) && isset($test->tat_hrs)?$test->tat_hrs:old('tat_hrs')}}"/> 
           			  </div>
					 <div class="col-md-2 col-6">
					    <label  for="name" class="label-size">{{__('Transport T°')}}</label>
						<select id="transport" name="transport" class="form-control">
						   <option value="">{{__("Choose")}}</option>
						   <option value="Room Temp." {{isset($test) && isset($test->transport) && $test->transport=="Room Temp."?"selected":""}}>{{__("Room Temp.")}}</option>
						   <option value="On ice" {{isset($test) && isset($test->transport) && $test->transport=="On ice"?"selected":""}}>{{__("On ice")}}</option>
						   <option value="Transport Temp." {{isset($test) && isset($test->transport) && $test->transport=="Transport Temp."?"selected":""}}>{{__("Transport Temp.")}}</option>
						</select>
					  </div>
					   <div class="col-md-2 col-4">
					    <label for="name" class="label-size">{{__('Type')}}</label>
					    <select name="test_type" id="test_type" class="custom-select rounded-0">
						   <option value="">{{__('Normal')}}</option>
						   <option value="F" {{isset($test) && isset($test->test_type) && $test->test_type=="F"?"selected":""}}>{{__('Formula')}}</option>
						</select>
					  </div>
					 
					 
					  <div class="col-md-2">
					    <label for="name" class="label-size">{{__('Numbers after comma')}}</label>
						<input type="number" min="0" id="dec_pts" name="dec_pts" class="form-control" value="{{isset($test) && isset($test->dec_pts)?$test->dec_pts:old('dec_pts')}}" onkeypress="return isNumberKey(event)"/>
					  </div>
					  <div class="col-md-4">
					   <label for="name" class="label-size">{{__('Specimen')}}
					   <button class="pl-2 p-0 btn btn-sm text-muted" title="{{__('Add specimen')}}" onclick="event.preventDefault();openNewSpecimen();"><i class="fa fa-plus"></i></button></span>
					   </label>
					   <select  id="specimen" name="specimen" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a specimen")}}</option>
							@foreach($specimen as $g)
							  @php $select_specimen = isset($test) && isset($test->specimen) && $test->specimen==$g->id?'selected':''; @endphp
							  <option {{$select_specimen}} value="{{$g->id}}">{{$g->name}}</option>
							@endforeach
						</select>
					  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Special consideration')}}
						<button class="pl-2 p-0 btn btn-sm text-muted" title="{{__('Add special consideration')}}" onclick="event.preventDefault();openNewSpec();"><i class="fa fa-plus"></i></button></span>
						</label>
						<select  id="special_considerations" name="special_considerations" class="select2_data custom-select rounded-0" style="width:100%;">
							<option value="">{{__("Choose a special consideration")}}</option>
							@foreach($spec_cons as $g)
							  @php $select_spec = isset($test) && isset($test->special_considerations) && $test->special_considerations==$g->id?'selected':''; @endphp
							  <option {{$select_spec}} value="{{$g->id}}">{{$g->name}}</option>
							@endforeach
						</select>
					  </div>
					 
					   <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Method instruction')}}</label>
						<input type="text" id="method_instruction" name="method_instruction" class="form-control"  value="{{isset($test) && isset($test->descrip)?$test->descrip:old('method_instruction')}}"/>
					  </div>
					  
					  <div class="col-md-4">
					    <label  for="name" class="label-size">{{__('Preanalytical')}}</label>
						<textarea  id="preanalytical" class="form-control" name="preanalytical" rows="3">{{isset($test) && isset($test->preanalytical)?$test->preanalytical:old('preanalytical')}}</textarea>
					  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Remark for reference')}}</label>
						<textarea id="test_rq" name="test_rq"  rows="3" class="form-control">{{isset($test) && isset($test->test_rq)?$test->test_rq:old('test_rq')}}</textarea> 
           			  </div>
					  <div class="col-md-4">
					    <label for="name" class="label-size">{{__('Clinical remark')}}</label>
						<textarea id="clinical_remark" name="clinical_remark" class="form-control"  rows="3">{{isset($test) && isset($test->clinical_remark)?$test->clinical_remark:old('clinical_remark')}}</textarea>
					  </div>
					   
					  <div class="form-group col-md-4 select2-primary" id="result_text_div" style="display:none;">
					    
						 <label for="name" class="label-size">{{__('Result Text')}}</label>
					     <select  id="result_text" name="result_text" class="select2_multiple_data custom-select rounded-0" data-dropdown-css-class="select2-primary"  data-placeholder="{{__('Choose a text result')}}" multiple="multiple" style="width:100%;">
							@foreach($text_results as $g)
							  <option value="{{$g->id}}" selected>{{$g->name}}</option>
							@endforeach
						</select>
						
					  </div>
					  <div class="form-group col-md-4 select2-primary" id="gram_stain_results_div" style="display:none;">
					     <label for="name" class="label-size">{{__('Gram Stain')}}</label>
					     <select  id="gram_stain_results" name="gram_stain_results" class="select2_multiple_data custom-select rounded-0" data-dropdown-css-class="select2-primary"  data-placeholder="{{__('Choose a gram_stain')}}" multiple="multiple" style="width:100%;">
							@foreach($gram_stain_results as $g)
							  <option value="{{$g->id}}" selected>{{$g->name}}</option>
							@endforeach
						 </select>
					  </div>
					  <div class="form-group mt-md-4 col-md-4">
						<button class="m-1 btn btn-action btn-sm" name="saveBtn" id="saveBtn">{{isset($test)?__("Update"):__("Save")}}</button>
						<button class="m-1 btn btn-reset btn-sm" name="cancelBtn" id="cancelBtn">{{__("Cancel")}}</button>
						 @if(isset($test))
						  <button class="m-1 btn btn-delete btn-sm" name="deleteBtn" id="deleteBtn">{{__("Inactivate")}}</button>
						  <button id="insertNGField" class="m-1 btn btn-action btn-sm" data-id="{{$test->id}}" data-name="{{$test->test_name}}"><i class="mr-2 fa fa-plus"></i>{{__('Fields')}}</button>
						 @endif
			          </div>
				</div>
			</form>
						
		  </div>
      </div>	  
    </div>	 
	<!--end group,code card-->
    <!--begin sub-group card-->
    <div id="sub_group" class="card card-outline" style="display:none;">
	   <div class="card-header card-menu">
		    <div class="container-fluid">
			<div class="row">
			   <div class="col-md-4 col-6">  
			      <h4 class="text-dark">{{__("Sub-Group codes")}}</h4>
			   </div> 	 
			   <div class="col-md-8 text-right col-6">
			           <button class="m-1 btn btn-action btn-sm" name="insSubGrp" id="insSubGrp"><i class="mr-1 fa fa-plus"></i>{{__("Sub-Group")}}</a>
			   		   <button type="button" class="m-1 btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
			   </div>
			  
			</div>
		  </div>	
	   </div> 	 
	   <div class="card-body" style="padding-right:7.5px;padding-left:7.5px;"> 
			
				<div class="row">
					<div class="col-md-12 table-responsive" style="overflow-y:auto;max-height:350px;">
						<table id="myTable" class="table table-sm table-striped table-bordered" style="width:100%;">
							<thead>
								<tr>
									<th scope="col" style="display:none;font-size:13px;">{{__('#')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Order')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Name')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Code')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Normal Value')}}</th>
									<th scope="col" style="font-size:13px;">{{__('LisCode')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Result Text')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Type')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Print All')}}</th>
									<th scope="col" style="font-size:13px;">{{__('to Check?')}}</th>
									<th scope="col" style="font-size:13px;">{{__('Title?')}}</th>
									<th scope="col"></th>
									<th scope="col" style="display:none;">{{__('ID')}}</th>
								</tr>
							</thead>
							<tbody id="myData" style="font-size:14px;">
								@if($sub_groups->count())
									@php $cnt =0; @endphp
									@foreach($sub_groups as $s)
										@php $cnt++; @endphp
										<tr>
											<td style="display:none;">{{$cnt}}</td>
											<td><input type="number" min="0" class="form-control" value="{{$s->testord}}" onkeypress="return isNumberKey(event)" style="min-width:70px;max-width:100px;"/></td>
											<td><input type="text" class="form-control" value="{{$s->test_name}}" style="min-width:250px;"/></td>
											<td><input type="text" class="form-control" value="{{$s->test_code}}" style="min-width:80px;"/></td>
											<td><input type="text" class="form-control" value="{{$s->normal_value}}" style="min-width:80px;"/></td>
											<td><input type="text" class="form-control" value="{{$s->listcode}}" style="min-width:80px;"/></td>
											<td class="select2-primary">
											   @php $subgroup_result = UserHelper::getSubgroupResults($s->id);@endphp
											   <select  name="subgroup_result_text" class="subgroup_rslt  custom-select rounded-0" data-dropdown-css-class="select2-primary" data-placeholder="{{__('Choose a text result')}}" multiple="multiple">
												@foreach($subgroup_result as $g)
												  <option value="{{$g->id}}" selected>{{$g->name}}</option>
												@endforeach
						                       </select>
											</td>
											<td>
											   <select name="test_type" id="test_type" class="form-control" style="min-width:100px;">
						                          <option value="">{{__('Normal')}}</option>
						                          <option value="F" {{$s->test_type=="F"?"selected":""}}>{{__('Formula')}}</option>
						                       </select>
											</td>
											<td>
											  <label class="mt-2 slideon slideon-xs slideon-success">
						                      <input  type="checkbox" {{$s->is_printed=='Y'?'checked':''}}/>
						                      <span class="slideon-slider"></span>
						                      </label>
											</td>
											<td>
											  <label class="mt-2 slideon slideon-xs slideon-success">
						                      <input  type="checkbox" {{$s->is_valid=='Y'?'checked':''}}/>
						                      <span class="slideon-slider"></span>
						                      </label>
											</td>
											<td>
											  <label class="mt-2 slideon slideon-xs slideon-success">
						                      <input  type="checkbox" onchange="event.preventDefault();disableIns(this);" {{$s->is_title=='Y'?'checked':''}}/>
						                      <span class="slideon-slider"></span>
						                      </label>
											</td>
											<td style="white-space:nowrap;">
												<button class="btn btn-action btn-xs" title="{{__('Insert Fields')}}" onclick="event.preventDefault();insField(this,{{$s->id}});" {{$s->is_title=='Y'?'disabled':''}}><i class="fa fa-plus"></i></button>
												<button class="ml-1 btn btn-delete btn-xs" title="{{__('Delete')}}" onclick="event.preventDefault();deleteRow(this,{{$s->id}});"><i class="fa fa-trash"></i></button>
											</td>
											
											<td style="display:none;">{{$s->id}}</td>
										</tr>
									@endforeach
								@endif
							</tbody>
						</table> 
					</div>
				</div>
			
		</div>

   </div>	
	<!--end sub-group card-->	 
	
</div><!--end container-->
@include('tests.fieldsModal')
@include('tests.editfieldModal')
@include('tests.newfieldModal')
@include('tests.specConsModal')
@include('tests.specimenModal')
@include('tests.cultresultModal')
@endsection
@section('scripts')
<script>
var  table1;
var  table2;
var  table3;
var  table4;
var  subgroupHtml;
$(document).ready(function(){
  $('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
  $('#result_text').select2({tags:true});
  $('#gram_stain_results').select2({tags:true});
  $('#myTable tbody tr').find('.subgroup_rslt').select2({tags:true,'width':'200px'});
  subgroupHtml = $('#myData').html();	
  
  var chk = $('#is_group').is(':checked');
  var culture = $('#is_culture').is(':checked');
 
  if(culture){
	  $('#gram_stain_results_div').css('display','block');
  }else{
	  $('#gram_stain_results_div').css('display','none');
	  $('#gram_stain_results').val(null).trigger('change');
  }
  
  if(chk){
			$('#insertNGField').hide();
			
			$('#is_valid_th').hide();
			$('#is_valid_td').hide();
			$('#is_valid').prop('checked',false);
			
			$('#sub_group').css('display','block');
            $('#result_text_div').css('display','none');
		    $('#result_text').val(null).trigger('change');
		 }else{
			$('#insertNGField').show();
			
			$('#is_valid_th').show();
			$('#is_valid_td').show();
			
			$('#myData').empty();
			$('#sub_group').css('display','none');
            $('#result_text_div').css('display','block');
		}
	
	
  
});


$(function(){
		
	
	table1 = new Tabulator("#fields_dt1", {
	 ajaxURL:"{{ route('lab.tests_fields.filter_code',app()->getLocale())}}", //ajax URL
     ajaxConfig:"POST",
	 ajaxParams: function(){
        var filter_lab=$('#fieldsModal').find('#lab_num').val();
		var filter_test=$('#fieldsModal').find('#field_test_id').val();
	   	return {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test};
           },
	height:500,
	placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:"local",
    paginationSize:50,
    paginationSizeSelector:[50,75, 100, true],
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
    columns:[
		{title:"{{__('#')}}", field:"id",visible:false},
		{title:"{{__('test_id')}}", field:"test_id",visible:false},
		{title:"{{__('Order')}}", field:"field_order",editor:"number", editorParams: {type:"number", min: 1, step: 1},
		 cellEdited: function(cell){
			var newValue = cell.getValue();
			var oldValue = cell.getData().oldValue;
			if(newValue !== oldValue) {
				var rowData = cell.getRow().getData();
				var id = rowData.id;
				var test_id = rowData.test_id;
					$.ajax({
						url: '{{route("lab.tests_fields.chkFieldOrder",app()->getLocale())}}',
						method: 'POST',
						data: { _token:'{{csrf_token()}}',id:id,field_order: newValue,test_id:test_id },
						success: function(response) {
							if(response.success) {
								Swal.fire({title:response.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
								
							} else {
							cell.restoreOldValue();
							Swal.fire({
								icon: 'error',
								text: response.error,
								customClass:'w-auto'
							});
						  }
					  },
					 error: function(xhr, status, error) {
							cell.restoreOldValue();
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: 'An error occurred while processing your request. Please try again later.',
								customClass:'w-auto'
							});
						  }
					});
			 }	
		}
		},
		{title:"{{__('Code')}}", field:"test_name",headerFilter:"input"},
        {title:"{{__('Gender')}}",field: 'gender',headerFilter:"input"},
		{title:"{{__('Age range')}}",field: "age_range",headerFilter:"input"},
		{title:"{{__('Min')}}",field: "min",headerFilter:"input"},
		{title:"{{__('Max')}}",field: "max",headerFilter:"input"},
		{title:"{{__('Unit')}}",field: "unit",headerFilter:"input"},
		{title:"{{__('Panic range')}}",field: "panic_range"},
		{title:"{{__('Description')}}",field:"descrip",headerFilter:"input"},
		{title:"{{__('Remark')}}",field: "remark"},
		{title:"{{__('Actions')}}", field:"status",frozen:true,
		 formatter:function(cell, formatterParams, onRendered){
			 var row=cell.getRow().getData();
			 var btn='<a href="javascript:void(0)"  class="btn btn-md btn-clean btn-icon" onclick="event.preventDefault();editData('+row.id+',\''+row.test_name+'\')"><i  class="far fa-edit text-primary" title="{{__("edit")}}"></i></a>';
             btn+='<a href="javascript:void(0)"  class="btn btn-md btn-clean btn-icon" onclick="event.preventDefault();delField('+row.id+')"><i  class="fa fa-trash text-danger" title="{{__("delete")}}"></i></a>';
			 return btn;
		 }
		},
		],
    });	
	
	$('#is_culture').change(function(e){
		e.preventDefault();
		var chk = $(this).is(':checked');
		if(chk){
			$('#gram_stain_results_div').css('display','block');
		}else{
			$('#gram_stain_results_div').css('display','none');
			$('#gram_stain_results').val(null).trigger('change');
		}
	});
	
	
	
	$('#is_group').change(function(e){
		e.preventDefault();
		var chk = $(this).is(':checked');
		var tst_id = $('#tst_id').val();
		var is_group = '{{isset($test)?$test->is_group:"N"}}';
		  
		
		  if(chk){
			$('#insertNGField').hide();
			$('#sub_group').css('display','block');
			$('#result_text_div').css('display','none');
			$('#result_text').val(null).trigger('change');
			
			$('#is_valid_th').hide();
			$('#is_valid_td').hide();
			$('#is_valid').prop('checked',false);
			
			
			if(tst_id!=0 && is_group=='Y'){  
		        $('#myData').empty();
				$('#myData').html(subgroupHtml);
				
				/*$.ajax({
				 type:'POST',
				 data:{_token: "{{ csrf_token() }}",id:tst_id},
				 url:'{{route("lab.tests.cancel",app()->getLocale())}}',
				 dataType:'JSON',
				 success: function(data){
					 $('#myData').empty();
					 $('#myData').html(data.html);
					
				 }
	           }); */
		    }
		  
		   }else{
			
			$('#insertNGField').show();
			$('#is_valid_th').show();
			$('#is_valid_td').show();
			
			$('#myData').empty();
			$('#sub_group').css('display','none');
		    $('#result_text_div').css('display','block');
		   }
		
		
	});
	
	
	
	$('#insSubGrp').click(function(e){
		e.preventDefault();
		var test_name=$('#test_name').val();
		if(test_name==''){
			Swal.fire({ 
              "text":"{{__('Please input the group name before inserting a sub-group')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
			  return false;
		  
		}
		
		
		var x=document.getElementById('myData').insertRow(document.getElementById('myData').rows.length);
		var len = x.insertCell(0);
		var order=x.insertCell(1);
		var name = x.insertCell(2);
		var code = x.insertCell(3);
		var normal_value=x.insertCell(4);
		var liscode = x.insertCell(5);
		var rslt = x.insertCell(6);
		var type = x.insertCell(7);
		var is_printed=x.insertCell(8);
		var is_valid = x.insertCell(9);
		var is_title = x.insertCell(10);
		var action = x.insertCell(11);
		var id = x.insertCell(12);
		
		//len.style.minWidth="5px";
		len.style.display="none";
		len.innerHTML=document.getElementById('myData').rows.length;
		//order.style.width="70px";
		order.innerHTML='<input type="number" min="0" style="min-width:70px;max-width:100px;" name="sub_order" class="form-control"  value="" onkeypress="return isNumberKey(event)"/>';
		//name.style.width="220px";
		name.innerHTML='<input type="text" name="sub_name" style="min-width:250px;" class="form-control"  value=""/>';
		//code.style.width="100px";
		code.innerHTML='<input type="text" name="sub_code" style="min-width:80px;" class="form-control"  value=""/>';
		//normal_value.style.width="150px";
		normal_value.innerHTML='<input type="text" name="sub_normal_value" style="min-width:80px;" class="form-control"  value=""/>';
		//liscode.style.width="100px";
		liscode.innerHTML='<input type="text" name="sub_liscode" class="form-control"  style="min-width:80px;" value=""/>';
		//rslt.style.width = "220px";
		rslt.classList.add("select2-primary");
		rslt.innerHTML='<select  name="subgroup_result_text" class="subgroup_rslt  custom-select rounded-0" data-dropdown-css-class="select2-primary"  data-placeholder="{{__("Choose a text result")}}" multiple="multiple"></select>';
		//type.style.width="100px";
		type.innerHTML='<select name="test_type" id="test_type" class="form-control" style="min-width:100px;"><option value="" selected>{{__("Normal")}}</option><option value="F">{{__("Formula")}}</option></select>';
		//is_printed.style.minWidth="100px";
        is_printed.innerHTML='<label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" style="width:50px;"/><span class="slideon-slider"></span></label>';		
		is_valid.innerHTML='<label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" style="width:50px;"/><span class="slideon-slider"></span></label>';		
	    is_title.innerHTML='<label class="mt-2 slideon slideon-xs slideon-success"><input  type="checkbox" style="width:50px;"/><span class="slideon-slider"></span></label>';		
		//action.style.minWidth="100px";
		action.style.whiteSpace = "nowrap";
		id_delete= 'onclick=event.preventDefault();deleteRow(this,0)';
		action.innerHTML='<button class="m-1 btn btn-action btn-xs" title="{{__("Insert Fields")}}" disabled><i class="fa fa-plus"></i></button><button  title="{{__("Delete")}}" class="m-1 btn btn-delete btn-xs" '+id_delete+'><i class="fa fa-trash"></i></button>';
		id.style.display="none";
		id.innerHTML="";
		$(rslt).find('.subgroup_rslt').select2({tags:true,width: '200px'});
	});
	
	
	$('#deleteBtn').click(function(e){
		e.preventDefault();
		id=$('#tst_id').val();
		Swal.fire({
		  title: '{{__("Are you sure?")}}',
		  html:'{{__("Please note, this operation will affect the related fields for this test code")}}',
		  showDenyButton: true,
		  confirmButtonText: '{{__("OK")}}',
		  denyButtonText: '{{__("Cancel")}}',
		  customClass: 'w-auto'
		}).then((result) => {
			  if (result.isConfirmed) {
				  $.ajax({
					type: "POST",
					url: "{{ route('lab.tests.remove',app()->getLocale()) }}",
					data:{_token: "{{ csrf_token() }}",id:id},
					dataType:"JSON",
					success: function (data) {
						Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
						window.location.href=data.location;
					}
				   });
				  
			  }else if (result.isDenied) {
				return false;
			  }
            })
	});
	
	
	
	
	
	$('#cancelBtn').click(function(e){
		e.preventDefault();
		var tst_id = $('#tst_id').val();
		var chk = $('#is_group').is(':checked');
		
		if(tst_id==0){
		 
		 if(!chk){
			$('#is_group').prop('checked',true);
			$('#sub_group').css('display','block');
			
			$('#is_valid_td').show();
			$('#is_valid_th').show();
		   
		   }else{
			$('#is_group').prop('checked',false);
			
			$('#is_valid_td').hide();
			$('#is_valid_th').hide();
			$('#is_valid').prop('checked',false);
			
			$('#myData').empty();
			$('#sub_group').css('display','none');
		   }
		
		}else{
		  var is_group = '{{isset($test)?$test->is_group:"N"}}';
		  if(is_group=='Y'){
			 $('#extLabForm').trigger("reset");
			 $('.select2_data').trigger("change.select2");
			 $('#insertNGField').hide();
				 
			 $('#is_valid_td').hide();
			 $('#is_valid_th').hide();
			 $('#is_valid').prop('checked',false);
			 $('#sub_group').css('display','block');
			 $('#myData').empty();
			 $('#myData').html(subgroupHtml);
			
			 /*$.ajax({
			 type:'POST',
			 data:{_token: "{{ csrf_token() }}",id:tst_id},
			 url:'{{route("lab.tests.cancel",app()->getLocale())}}',
			 dataType:'JSON',
			 success: function(data){
				 $('#extLabForm').trigger("reset");
				 $('.select2_data').trigger("change.select2");
				 $('#insertNGField').hide();
				 
				 $('#is_valid_td').hide();
			     $('#is_valid_th').hide();
				 $('#is_valid').prop('checked',false);
			
				 $('#sub_group').css('display','block');
				 $('#myData').empty();
				 $('#myData').html(data.html);
			 }
	        });*/ 
		  }else{
			  $('#extLabForm').trigger("reset");
		      $('.select2_data').trigger("change.select2");
			  
			  $('#is_valid_td').show();
			  $('#is_valid_th').show();
			
			  $('#insertNGField').show();
		      $('#myData').empty();
		      $('#sub_group').css('display','none');
			 }

		}
	});
	
	$('#saveBtn').click(function(e){
		e.preventDefault();
		var test_name=$('#test_name').val();
		var is_group = $('#is_group').is(':checked')?'Y':'N';
		var is_culture = $('#is_culture').is(':checked')?'Y':'N';
		
		if(test_name==''){
			Swal.fire({ 
              "text":(is_group=='Y')?"{{__('Please input a group name')}}":"{{__('Please input a code name')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
			  return false;
		  
		}
		var cnss=$('#cnss').val();
		if(cnss==''){
			Swal.fire({ 
              "text":"{{__('Please input a cnss')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
			  return false;
		  
		}
		
		var arr = new Array();
		var arr_ids = new Array();
		if(is_group=='Y'){
			var cnt=document.getElementById('myData').rows.length;
			if(cnt==0){
				Swal.fire({ 
				  "text":"{{__('Please input at least one sub-group')}}",
				  "icon":"warning",
				  "customClass": "w-auto"});
				  return false;
			}
		   
		    var cnt_errors = 0;
		   for(i=0;i<document.getElementById("myData").rows.length;i++){
			  if(document.getElementById("myData").rows[i].cells[2].getElementsByTagName("input")[0].value==""){
				 cnt_errors++;
			  }  
		    }
			if(cnt_errors>0){
				Swal.fire({ 
				  "text":"{{__('Please fill the names of all sub_groups')}}",
				  "icon":"warning",
				  "customClass": "w-auto"});
				  return false; 
			}  
			
			for(i=0;i<document.getElementById("myData").rows.length;i++){  
			  var is_printed = (document.getElementById("myData").rows[i].cells[8].getElementsByTagName("input")[0].checked==true)?'Y':'N';
			  var is_valid = (document.getElementById("myData").rows[i].cells[9].getElementsByTagName("input")[0].checked==true)?'Y':'N';
			  var is_title = (document.getElementById("myData").rows[i].cells[10].getElementsByTagName("input")[0].checked==true)?'Y':'N';

			  var selectElement = document.getElementById("myData").rows[i].cells[6].getElementsByTagName("select")[0];
              var selectedOptions = Array.from(selectElement.selectedOptions);
              var selectedTexts = selectedOptions.map(function(option) {
						 return option.text.trim();
                     });
			 
			 
			  arr[i]={
				     "order":document.getElementById("myData").rows[i].cells[1].getElementsByTagName("input")[0].value,
					 "name":document.getElementById("myData").rows[i].cells[2].getElementsByTagName("input")[0].value,
					 "code":document.getElementById("myData").rows[i].cells[3].getElementsByTagName("input")[0].value,
				     "normalvalue":document.getElementById("myData").rows[i].cells[4].getElementsByTagName("input")[0].value,
				     "liscode":document.getElementById("myData").rows[i].cells[5].getElementsByTagName("input")[0].value,
					 "rslt":selectedTexts,
					 "type":document.getElementById("myData").rows[i].cells[7].getElementsByTagName("select")[0].value,
					 "isPrinted":is_printed,
					 "isValid":is_valid,
					 "isTitle":is_title,
					 "subgroupID":document.getElementById("myData").rows[i].cells[12].innerHTML,
					 };	
  		       if(document.getElementById("myData").rows[i].cells[12].innerHTML!= null 
			      && document.getElementById("myData").rows[i].cells[12].innerHTML!=''){
			       arr_ids.push(document.getElementById("myData").rows[i].cells[12].innerHTML);
			      }
			   }
	
		    }	
	 
	  //console.log(arr);
	  var sub_codes=arr.length==0?null:JSON.stringify(arr);
	  var sub_ids = arr_ids.length==0?null:JSON.stringify(arr_ids);
	  var tst_id = $('#tst_id').val();
	  var is_printed = $('#is_code_printed').is(':checked')?'Y':'N';
	  var is_valid = $('#is_valid_td').is(':visible') && $('#is_valid').is(':checked')?'Y':'N';
	  var custom_test = $('#custom_test').is(':checked')?'Y':'N';
	  var url = tst_id==0?"{{route('lab.tests.store',app()->getLocale())}}":"{{route('lab.tests.update',app()->getLocale())}}";
	  
	  var result_txts =  null;
	  if($('#result_text').is(':visible')){
		 
		  var tags = $('#result_text').select2('data').map(function(tag) {
                return tag.text;
            });
		   
		   // Combine selected values and tags
           
		   result_txts = tags;
		   
	  }
	  
	  var gram_stain_results =  null;
	  if($('#gram_stain_results').is(':visible')){
		 
		  var tags = $('#gram_stain_results').select2('data').map(function(tag) {
                return tag.text;
            });
		   
		   // Combine selected values and tags
           
		    gram_stain_results = tags;
		   
	  }


   
		  
	 
	  
	  $.ajax({
          type:'POST',
		  url: url,
          data:{_token: "{{ csrf_token() }}",
		        id:tst_id,
				data:sub_codes,
				sub_ids:sub_ids,
				is_printed:is_printed,
				is_valid:is_valid,
		        is_group:is_group,
				custom_test:custom_test,
				is_culture:is_culture,
				test_name:$('#test_name').val(),
				category_num:$('#category_num').val(),
				cnss:$('#cnss').val(),
				test_code:$('#test_code').val(),
				price:$('#price').val(),
				nbl:$('#nbl').val(),
				normal_value:$('#normal_value').val(),
				listcode:$('#listcode').val(),
				testord:$('#testord').val(),
				preanalytical:$('#preanalytical').val(),
				storage:$('#storage').val(),
				tat_hrs:$('#tat_hrs').val(),
				referred_tests:$('#referred_tests').val(),
				transport:$('#transport').val(),
				test_rq:$('#test_rq').val(),
				test_type:$('#test_type').val(),
				description:$('#method_instruction').val(),
				clinical_remark:$('#clinical_remark').val(),
				specimen:$('#specimen').val(),
				special_considerations:$('#special_considerations').val(),
				result_text:  result_txts,
				gram_stain_results:gram_stain_results,
				dec_pts:$('#dec_pts').val()
				},
	      success: function(data){
			  if(data.warning){	
			  Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
			  return false;
		     }
			if(data.success){	
				 Swal.fire({ 
				  "title":data.success,
				  "icon":"success",
				  "timer":3000,
				  "position":"bottom-right",
				  "showConfirmButton":false,
				  "toast": true});
			  	 window.location.href=data.location
			    }
		  }
	  
	  });
	
	});
	
	


$('#fieldsModal').on('show.bs.modal',function(){
	var filter_lab=$('#fieldsModal').find('#lab_num').val();
    var filter_test=$('#fieldsModal').find('#field_test_id').val();
	table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
});

//edit field
$('#editfieldModal').on('hide.bs.modal',function(){
     $('#fieldsModal').css('display','block');
});

//new field
$('#newfieldModal').on('hide.bs.modal',function(){
     $('#fieldsModal').css('display','block');
});

$('#saveFieldBtn').click(function(e){
	e.preventDefault();
   var test = $('#newfieldForm').find('#field_test_id').val();
   var name = $('#newfieldForm').find('#descrip').val();
   var nv1 = $('#newfieldForm').find('#normal_value1').val();
   var nv2 = $('#newfieldForm').find('#normal_value2').val();
    
	  if(nv1!='' &&  nv2!='' && parseFloat(nv2)<=parseFloat(nv1)){
		Swal.fire({text:"Please input a second normal value greater than first normal value",icon:"error",customClass:"w-auto"});
			return false;
		} 
	 
		var plv = $('#newfieldForm').find('#panic_low_value').val();
		var phv = $('#newfieldForm').find('#panic_high_value').val();
		
		if(plv !='' && phv !='' && parseFloat(phv)<=parseFloat(plv)){
			Swal.fire({text:"Please input a panic high value greater than panic low value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var fage = $('#newfieldForm').find('#fage').val();
		
		if(fage!='' && fage!=0 && parseFloat(fage)<0){
			Swal.fire({text:"Please input a valid from_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var tage = $('#newfieldForm').find('#tage').val();	
		
		if(tage!='' && tage!=0 && parseFloat(tage)<0){
			Swal.fire({text:"Please input a valid to_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		
		if(tage!='' && tage!=0 && fage!='' && fage!=0 && parseFloat(tage)<=parseFloat(fage)){
			Swal.fire({text:"Please input a to_age value greater than from_age value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
        $.ajax({

          data: $('#newfieldForm').serialize(),
          url: "{{ route('lab.tests_fields.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
               
				  if(data.error){
					  Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
					  return false;
				  }else{
					  var filter_lab=$('#newfieldForm').find('#lab_num').val();
					  var filter_test=$('#newfieldForm').find('#test_id').val();
					  table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
					  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
					  $('#newfieldForm').trigger("reset");
					  $('#newfieldModal').modal('hide');
					  $('#fieldsModal').css('display','block');
				  }
			  
           }

      });

	
});


$('#updateFieldBtn').click(function(e){
	e.preventDefault();
   var test = $('#editfieldForm').find('#test_id').val();
   var name = $('#editfieldForm').find('#descrip').val();
   var nv1 = $('#editfieldForm').find('#normal_value1').val();
   var nv2 = $('#editfieldForm').find('#normal_value2').val();
    
	  if(nv1!='' &&  nv2!='' && parseFloat(nv2)<=parseFloat(nv1)){
		Swal.fire({text:"Please input a second normal value greater than first normal value",icon:"error",customClass:"w-auto"});
			return false;
		} 
	 
		var plv = $('#editfieldForm').find('#panic_low_value').val();
		var phv = $('#editfieldForm').find('#panic_high_value').val();
		
		if(plv !='' && phv !='' && parseFloat(phv)<=parseFloat(plv)){
			Swal.fire({text:"Please input a panic high value greater than panic low value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var fage = $('#editfieldForm').find('#fage').val();
		
		if(fage!='' && fage!=0 && parseFloat(fage)<0){
			Swal.fire({text:"Please input a valid from_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var tage = $('#editfieldForm').find('#tage').val();	
		
		if(tage!='' && tage!=0 && parseFloat(tage)<0){
			Swal.fire({text:"Please input a valid to_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		
		if(tage!='' && tage!=0 && fage!='' && fage!=0 && parseFloat(tage)<=parseFloat(fage)){
			Swal.fire({text:"Please input a to_age value greater than from_age value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		
        $.ajax({

          data: $('#editfieldForm').serialize(),
          url: "{{ route('lab.tests_fields.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            	  if(data.error){
					  Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
					  return false;
				  }else{
					  var filter_lab=$('#editfieldForm').find('#lab_num').val();
					  var filter_test=$('#editfieldForm').find('#test_id').val();
					  table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
					  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
					  $('#editfieldForm').trigger("reset");
					  $('#editfieldModal').modal('hide');
					  $('#fieldsModal').css('display','block');
				  }
				}
      });

	
});

$('#insertNGField').click(function(e){
	e.preventDefault();
	var id = $(this).data('id');
	var name= $(this).data('name');
	var lab_num = '{{auth()->user()->clinic_num}}';
	
	$('#fieldsModal').find('#field_test_id').val(id);
	$('#fieldsModal').find('#field_test_name').val(name);
	$('#fieldsModal').find('#lab_num').val(lab_num);
	$('#fieldsModal').modal('show');
});	


$('#specConsModal').on('show.bs.modal',function(){
	table2 = new Tabulator("#spec_cons_div", {
	 ajaxURL:"{{ route('lab.tests.specConsideration',app()->getLocale())}}", //ajax URL
     ajaxConfig:"POST",
	  ajaxParams: function(){
        	return {_token:'{{csrf_token()}}',type:'table'};
           },
	 height:350,
	 placeholder:"No Data Available", //display message to user on empty table
	 placeholderHeaderFilter:"No Matching Data",
	 pagination:"local",
     paginationSize:50,
     paginationSizeSelector:[50,75, 100, true],
	 layout:"fitDataStretch",
	 layoutColumnsOnNewData:true,
	 movableRows:false,
     columns:[
		{title:"{{__('#')}}", field:"id",headerFilter:"input"},
		{title:"{{__('Name')}}", field:"name",editor:"input",headerFilter:"input",cellEdited: function(cell) {
         var rowData = cell.getRow().getData();
		 var oldValue = cell.getOldValue();
		 var newValue = cell.getValue();
		 if (newValue.trim() === '') {
             cell.restoreOldValue();
         }else if (newValue !== oldValue){
			 var newData = {
				id: rowData.id,
				name: rowData.name,
			   };
           saveSpecCons(cell,newData);
		 }
         }},
		{title:"{{__('status')}}", field:"status",visible:false},
		{title:"{{__('Action')}}", field:"actions",
		 formatter:function(cell, formatterParams, onRendered){
			 var row=cell.getRow().getData();
			 var checked = (row.status=='Y')?'checked':'';
             var btn ='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onchange="toggleSpecCons('+row.id+',\''+row.name+'\',this)" class="toggle-specCons" '+checked+'><span class="slideon-slider"></span></label>';
			 return btn;
		 }
		},
		],
    });
  
   
  	
});

$('#specimenModal').on('show.bs.modal',function(){
	table3 = new Tabulator("#specimens_div", {
	 ajaxURL:"{{ route('lab.tests.Specimen',app()->getLocale())}}", //ajax URL
     ajaxConfig:"POST",
	  ajaxParams: function(){
        	return {_token:'{{csrf_token()}}',type:'table'};
           },
	 height:350,
	 placeholder:"No Data Available", //display message to user on empty table
	 placeholderHeaderFilter:"No Matching Data",
	 pagination:"local",
     paginationSize:50,
     paginationSizeSelector:[50,75, 100, true],
	 layout:"fitDataStretch",
	 layoutColumnsOnNewData:true,
	 movableRows:false,
     columns:[
		{title:"{{__('#')}}", field:"id",visible:false},
		{title:"{{__('Order')}}",field:"specimen_order",headerFilter:"input"},
		{title:"{{__('Name')}}", field:"name",editor:"input",headerFilter:"input",cellEdited: function(cell) {
         var rowData = cell.getRow().getData();
		 var oldValue = cell.getOldValue();
		 var newValue = cell.getValue();
		 if (newValue.trim() === '') {
             cell.restoreOldValue();
         }else if (newValue !== oldValue){
			 var newData = {
				id: rowData.id,
				name: rowData.name,
			   };
           saveSpecimens(cell,newData);
		 }
         }},
		{title:"{{__('status')}}", field:"status",visible:false},
		{title:"{{__('Action')}}", field:"actions",
		 formatter:function(cell, formatterParams, onRendered){
			 var row=cell.getRow().getData();
			 var checked = (row.status=='Y')?'checked':'';
             var btn ='<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" onchange="toggleSpecimens('+row.id+',\''+row.name+'\',this)" class="toggle-specimens" '+checked+'><span class="slideon-slider"></span></label>';
			 return btn;
		 }
		},
		],
    });
  
   
  	
});


$('#cultresultModal').on('show.bs.modal',function(){
	table4 = new Tabulator("#result_text_div", {
	 ajaxURL:"{{ route('lab.tests.textResult',app()->getLocale())}}", //ajax URL
     ajaxConfig:"POST",
	  ajaxParams: function(){
        	return {_token:'{{csrf_token()}}','test_id':$('#tst_id').val(),type:'table'};
           },
	 height:350,
	 placeholder:"No Data Available", //display message to user on empty table
	 placeholderHeaderFilter:"No Matching Data",
	 pagination:"local",
     paginationSize:50,
     paginationSizeSelector:[50,75, 100, true],
	 layout:"fitData",
	 layoutColumnsOnNewData:true,
	 movableRows:false,
     columns:[
		{title:"{{__('#')}}", field:"id",headerFilter:"input"},
		{title:"{{__('Name')}}", field:"name",headerFilter:"input"},
		{title:"{{__('status')}}", field:"status",visible:false},
		{title:"{{__('Action')}}", field:"actions",formatter:function(cell, formatterParams, onRendered){
			 var row=cell.getRow().getData();
			 var btn ='<button type="button"  onclick="delTextResult('+row.id+')" class="btn btn-sm btn-icon"><i class="fa fa-trash-alt text-danger"></i></button>';
			 return btn;
		 }
		},
		],
    });
  
   
  	
});


	
});

function getLastRowValue(column) {
    var data = table1.getData();  // Retrieve all data from table1
    if(data.length > 0) {
        var lastRow = data[data.length - 1];  // Get the last row
        return lastRow[column];  // Return the value of the specified column
    }
    return null;  // Return null if the table1 is empty
}

function saveSpecCons(cell,row){
	  var id = row.id;
	  var name=row.name;
	  
	  
	  $.ajax({
		   url:"{{ route('lab.tests.specConsideration',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,name:name,type:'update'},
		   dataType:"JSON",
		   success:function(data){
			   if(data.error){
				  var oldValue = cell.getOldValue();
				  cell.restoreOldValue();
				  Swal.fire({text:data.error,icon:"error",customClass:"w-auto"}); 
			   } 
			   if(data.success){
		  
				$('#special_considerations option:not(:first)').remove();
					  $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#special_considerations').append(newOption).trigger('change');
					 });
				
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		       }
		   }
	  });
 }

function saveSpecimens(cell,row){
	  var id = row.id;
	  var name=row.name;
	  var order = row.specimen_order;
	  $.ajax({
		   url:"{{ route('lab.tests.Specimen',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,order:order,name:name,type:'update'},
		   dataType:"JSON",
		   success:function(data){
			   if(data.error){
				  var oldValue = cell.getOldValue();
				  cell.restoreOldValue();
				  Swal.fire({text:data.error,icon:"error",customClass:"w-auto"}); 
			   } 
			   if(data.success){
		  
				$('#specimen option:not(:first)').remove();
					  $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#specimen').append(newOption).trigger('change');
					 });
				
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		       }
		   }
	  });
 }	 


function toggleSpecCons(id,name,el){
		var checked = $(el).is(':checked')?'Y':'N';
		$.ajax({
		   url:"{{ route('lab.tests.specConsideration',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,checked:checked,type:'delete'},
		   dataType:"JSON",
		   success:function(data){
			    table2.setData("{{route('lab.tests.specConsideration',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	if(data.inactive){
				  $("#special_considerations option[value='"+id+"']").remove();
				  $('#special_considerations').trigger('change');
				}
				if(data.active){
					 
					 $('#special_considerations option:not(:first)').remove();
					  $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#special_considerations').append(newOption).trigger('change');
					 });
				  
				}
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		   }
	  });
    }
	
function toggleSpecimens(id,name,el){
		var checked = $(el).is(':checked')?'Y':'N';
		$.ajax({
		   url:"{{ route('lab.tests.Specimen',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,checked:checked,type:'delete'},
		   dataType:"JSON",
		   success:function(data){
			    table3.setData("{{route('lab.tests.Specimen',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	if(data.inactive){
				  $("#specimen option[value='"+id+"']").remove();
				  $('#specimen').trigger('change');
				}
				if(data.active){
					 
					 $('#specimen option:not(:first)').remove();
					  $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#specimen').append(newOption).trigger('change');
					 });
				  
				}
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		   }
	  });
    }	


function newSpecCons(){
	  
	  var name = $('#specConsModal').find('#spec_cons_name').val();
	  if(name==''){
		  Swal.fire({text:"Please fill a name",icon:"error",customClass:"w-auto"});
		  return false;
	  }
	  
	  $.ajax({
		   url:"{{ route('lab.tests.specConsideration',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',name:name,type:'new'},
		   dataType:"JSON",
		   success:function(data){
			   if(data.error){
				  Swal.fire({text:data.error,icon:"error",customClass:"w-auto"}); 
			   } 
			   if(data.success){
				table2.setData("{{route('lab.tests.specConsideration',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	$('#specConsModal').find('#specConsForm').trigger("reset");
				
				$('#special_considerations option:not(:first)').remove();
					 
					 $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#special_considerations').append(newOption).trigger('change');
					 });
				
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		       }
		   }
	  });
  }
  
function newSpecimen(){
	  var order = $('#specimenModal').find('#specimen_order').val();
	   if(order==''){
		  Swal.fire({text:"Please fill an order",icon:"error",customClass:"w-auto"});
		  return false;
	  }
	  
	  
	  var name = $('#specimenModal').find('#specimen_name').val();
	  if(name==''){
		  Swal.fire({text:"Please fill a name",icon:"error",customClass:"w-auto"});
		  return false;
	  }
	  
	  
	  $.ajax({
		   url:"{{ route('lab.tests.Specimen',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',name:name,order:order,type:'new'},
		   dataType:"JSON",
		   success:function(data){
			   if(data.error){
				  Swal.fire({text:data.error,icon:"error",customClass:"w-auto"}); 
			   } 
			   if(data.success){
				table3.setData("{{route('lab.tests.Specimen',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	$('#specimenModal').find('#specimensForm').trigger("reset");
				
				$('#specimen option:not(:first)').remove();
					 
					 $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#specimen').append(newOption).trigger('change');
					 });
				
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		       }
		   }
	  });
  }  
  
  
function newTextResult(){
	  var name = $('#cultresultModal').find('#cult_text_name').val();
	  if(name==''){
		  Swal.fire({text:"Please fill a name",icon:"error",customClass:"w-auto"});
		  return false;
	  }
	  $.ajax({
		   url:"{{ route('lab.tests.textResult',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',name:name,test_id:$('#tst_id').val(),type:'new'},
		   dataType:"JSON",
		   success:function(data){
			   if(data.error){
				  Swal.fire({text:data.error,icon:"error",customClass:"w-auto"}); 
			   } 
			   if(data.success){
				table4.setData("{{route('lab.tests.textResult',app()->getLocale())}}", {_token:'{{csrf_token()}}',test_id:$('#tst_id').val(),type:'table'},"POST");	
			   	$('#cultresultModal').find('#cultResultForm').trigger("reset");
				
				     $('#result_text').empty();
					 //console.log(data.values);
					 $.each(data.keys, function(i, key) {
						 // Create a DOM Option and pre-select by default
                         var newOption = new Option(data.values[i], key, false, false);
                        // Append it to the select
                        $('#result_text').append(newOption).trigger('change');
					 });
				
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		       }
		   }
	  });
  }  

/*function delSpecCons(id){
	 $.ajax({
		   url:"{{ route('lab.tests.specConsideration',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,type:'delete'},
		   dataType:"JSON",
		   success:function(data){
			    table3.setData("{{route('lab.tests.specConsideration',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	$("#special_considerations option[value='"+id+"']").remove();
				$('#special_considerations').trigger('change');
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		   }
	  });
  }*/
  
function delTextResult(id){
	 $.ajax({
		   url:"{{ route('lab.tests.textResult',app()->getLocale())}}", //ajax URL
           type:"POST",
	       data: {_token:'{{csrf_token()}}',id:id,type:'delete'},
		   dataType:"JSON",
		   success:function(data){
			    table4.setData("{{route('lab.tests.textResult',app()->getLocale())}}", {_token:'{{csrf_token()}}',type:'table'},"POST");	
			   	$("#result_text option[value='"+id+"']").remove();
				$('#result_text').trigger('change');
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
		   }
	  });
  }  


function delField(id){
	 $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
     $.ajax({

            type: "POST",
            url: "{{ route('lab.tests_fields.inactiveFields',app()->getLocale()) }}",
            data:{id:id,type:'inactive_field'},
			dataType:"JSON",
            success: function (data) {
                var filter_lab=$('#fieldsForm').find('#lab_num').val();
		        var filter_test=$('#fieldsForm').find('#field_test_id').val();
		        table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
				Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:3000,position:'bottom-right'});
				
            }
           });

}
	
function disableIns(el){
	var checked = $(el).is(':checked');
	$(el).closest('tr').find('td:eq(11)>.btn-action').prop('disabled',checked);
}

function insField(el,id){
	var lab_num = '{{auth()->user()->clinic_num}}';
	var test_name = $(el).closest('tr').find('td:eq(2)>input').val().trim();
	if(test_name==null || test_name==''){
		Swal.fire({icon:'warning',text:'{{__("Please fill a name for the sub-group")}}',customClass:'w-auto'});
		return false;
	}
	$('#fieldsModal').find('#field_test_id').val(id);
	$('#fieldsModal').find('#field_test_name').val(test_name);
	$('#fieldsModal').find('#test_title').text(test_name);
	$('#fieldsModal').find('#lab_num').val(lab_num);
	$('#fieldsModal').modal('show');
}
	
function deleteRow(r,id){
	var ii=r.parentNode.parentNode.rowIndex;
	if(id==0){
		document.getElementById('myTable').deleteRow(ii);
	    for(i=0;i<document.getElementById("myData").rows.length;i++){
	      document.getElementById("myData").rows[i].cells[0].innerHTML=i+1;
		}
	}else{
		//remove fields related to this code
	 $.ajax({
          data: {_token:'{{csrf_token()}}',id:id},
		  url: '{{route("lab.tests.deleteRow",app()->getLocale())}}',
		  type:'POST',
		  dataType:'JSON',
		  success: function(data){
			  document.getElementById('myTable').deleteRow(ii);
	          for(i=0;i<document.getElementById("myData").rows.length;i++){
	          document.getElementById("myData").rows[i].cells[0].innerHTML=i+1;
		     }
		  }
	    });
	}
	
}

function isNumberKey(evt){
  var charCode = (evt.which) ? evt.which : evt.keyCode;
   if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
}


function newData(){
	var lab_num = $('#fieldsModal').find('#lab_num').val();
	var id = $('#fieldsModal').find('#field_test_id').val();
	var name = $('#fieldsModal').find('#field_test_name').val();
	var lastOrderValue = getLastRowValue("field_order");
	$('#newfieldForm').trigger("reset");
	$('#newfieldForm').find('#field_order').val(lastOrderValue+1);
	$('#newfieldForm').find('#test_id').val(id);
	$('#newfieldForm').find('#test_name').val(name);
	$('#newfieldForm').find('#field_type').val("new");   	 
    $('#newfieldForm').find('#id').val('0');
	$('#newfieldForm').find('#lab_num').val(lab_num);
	$('#newfieldModal').find('#modelHeading').html("{{__('New field')}}");
	$('#newfieldModal').modal('show');
	$('#fieldsModal').css('display','none');
}

function editData(id,test_name) {
      $.ajaxSetup({

          headers: {

              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    
	  $.ajax({
          data: {id:id},
          url: "{{ route('lab.tests_fields.get_info',app()->getLocale()) }}",
		  type: "POST",
          dataType: 'json',
          success: function (res) {
		  $('#editfieldModal').find('#id').val(res.data.id);
		  $('#editfieldModal').find('#lab_num').val(res.data.clinic_num);
		  $('#editfieldModal').find('#descrip').val(res.data.descrip);
		  $('#editfieldModal').find('#gender').val(res.data.gender);
		  $('#editfieldModal').find('#panic_low_value').val(res.data.panic_low_value);
		  $('#editfieldModal').find('#panic_high_value').val(res.data.panic_high_value);
		  $('#editfieldModal').find('#normal_value1').val(res.data.normal_value1);
		  $('#editfieldModal').find('#normal_value2').val(res.data.normal_value2);
		  $('#editfieldModal').find('#tage').val(res.data.tage);
		  $('#editfieldModal').find('#fage').val(res.data.fage);
		  $('#editfieldModal').find('#field_order').val(res.data.field_order);
		  $('#editfieldModal').find('#sign_min').val(res.data.sign_min);
		  $('#editfieldModal').find('#sign_max').val(res.data.sign_max);
		  $('#editfieldModal').find('#sign').val(res.data.sign);
		  /*if(res.data.is_comparison=='Y'){
		    $('#editfieldModal').find('#is_comparison').prop('checked',true);
		  }else{
			$('#editfieldModal').find('#is_comparison').prop('checked',false);  
		  }
		  if(res.data.desirable_low=='Y'){
		    $('#editfieldModal').find('#desirable_low').prop('checked',true);
		  }else{
			$('#editfieldModal').find('#desirable_low').prop('checked',false);  
		  }
		  if(res.data.desirable_high=='Y'){
		    $('#editfieldModal').find('#desirable_high').prop('checked',true);
		  }else{
			$('#editfieldModal').find('#desirable_high').prop('checked',false);  
		  }*/
		  
		 
		  /*if(res.data.rtype=='' || res.data.rtype==null){
			$('#editfieldModal').find('#rtype').val('');  
		  }else{
		    $('#editfieldModal').find('#rtype').val(res.data.rtype);
		  }*/
		  
		  if(res.data.mytype=='' || res.data.mytype==null){
			$('#editfieldModal').find('#mytype').val('');  
		  }else{
		  $('#editfieldModal').find('#mytype').val(res.data.mytype);
		  }
		  
		  $('#editfieldModal').find('#test_id').val(res.data.test_id);
		  $('#editfieldModal').find('#test_name').val(test_name);
		  $('#editfieldModal').find('#remark').val(res.data.remark);
		  $('#editfieldModal').find('#unit').val(res.data.unit);
		  //$('#editfieldModal').find('#criteria').val(res.data.criteria);
		  $('#editfieldModal').find('#modelHeading').html("{{__('Edit field')}}"+":#"+res.data.id);
          $('#editfieldModal').find('#field_type').val("edit");
		  $('#fieldsModal').css('display','none');
          $('#editfieldModal').modal('show');
          }
      });

}
</script>
<script>
function openNewSpec(){
 
  $('#specConsModal').modal({
    backdrop: false,
    show: true
  });

  $('.modal-dialog').draggable({
    handle: ".modal-header"
  });
 
}

function openNewSpecimen(){
 
  $('#specimenModal').modal({
    backdrop: false,
    show: true
  });

  $('.modal-dialog').draggable({
    handle: ".modal-header"
  });
 
}

function openCultResult(){

  $('#cultresultModal').modal({
    backdrop: false,
    show: true
  });

  $('.modal-dialog').draggable({
    handle: ".modal-header"
  });
}



</script>

@endsection