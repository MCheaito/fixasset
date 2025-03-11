@extends('gui.main_gui')
@section('styles')
<style>
  .label-size1{
	  font-size:0.8rem;
  }

</style>
@endsection
@section('content')
        
<div class="container-fluid">
   <div class="card card-outline">	  
	  <div class="card-header card-menu">
					<div class="card-tools">
									<button type="button" class="btn btn-resize btn-sm" title="{{__('Show/Collapse')}}" data-card-widget="collapse">
										<i class="fas fa-minus"></i>
								    </button>
				     </div>
			       					
					<div class="row">
						<div class="col-md-12">
						  <h3 class="text-dark">{{__("Lab Codes Fields")}}</h3>
					   </div> 
						<div class="form-group col-md-4">
						  <select  id="filter_lab" name="filter_lab" class="select2_data custom-select rounded-0" style="width:100%;">
							@foreach($my_labs as $lab)
							  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
							@endforeach
						  </select>
						</div>
                        <div class="form-group col-md-4">
						  <select  id="filter_test" name="filter_test" class="select2_data custom-select rounded-0" style="width:100%;">		
							@if(auth()->user()->type==2)
								<option value="">{{__("Choose a code")}}</option>
							    @foreach($tests as $t)
								  @if(isset($t->test_code) && $t->test_code!='')
								      <option value="{{$t->id}}">{{$t->test_name.' ( '.$t->test_code.' )'}}</option>
							      @else
									  <option value="{{$t->id}}">{{$t->test_name}}</option>
								  @endif	  
								@endforeach
							@endif	
						  </select>
						</div>							
						<div class="form-group col-md-2">
						  <select  id="filter_status" name="filter_status" class="custom-select rounded-0">
							<option value="Y">{{__('Active')}}</option>
							<option value="N">{{__('InActive')}}</option>
						  </select>
						</div>
                        <div class="form-group col-md-2">
					         <a class="m-1 float-right btn btn-action" href="javascript:void(0)" id="create_test"><i class="fa fa-plus"></i> Create</a>
					    </div>						
					</div>
                  </div>
        <div class="card-body p-0">
           <div class="container-fluid">  
			 
			 <div class="m-1 row">
			   <div class="col-md-12">
                   
                        
                            <table id="fields_dt" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
										<th>{{__('Order')}}</th>
										<th>{{__('Test Name')}}</th>
										<th>{{__('Group')}}</th>
										<th>{{__('Gender')}}</th>
										<th>{{__('Age Range')}}</th>
										<th>{{__('Min')}}</th>
										<th>{{__('Max')}}</th>
										<th>{{__('Panic Range')}}</th>
										<th>{{__('Description')}}</th>
										<th>{{__('Actions')}}</th>
                                    </tr>
                                </thead>
                                 <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
	</div>		
</div>
@include('tests.fields.fieldLABModal')
@endsection
@section('scripts')
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$('.select2_modal').select2({theme:'bootstrap4',width:'resolve',dropdownParent: $('#fieldLABModal')});
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
     });
	 
	
	var table = $('#fields_dt').DataTable({
		stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[0,'desc']],
		info: true,
		pageLength: 50,
		lengthMenu: [
        [50, 75, 100, -1],
        [50, 75, 100, 'All']
         ],
	    scrollY:"350px",
	    scrollX:true,
	    scrollCollapse: true,
        ajax: {
            url: "{{ route('lab.tests_fields.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#filter_status').val();
				d.filter_lab=$('#filter_lab').val();
				d.filter_test=$('#filter_test').val();
                    }
              },
        columns: [
            {data: 'id',name:'f.id'},
			{data: 'field_order',name:'f.field_order'},
			{data: 'test_name'},
			{data: 'group_name',searchable:false},
            {data: 'gender',render: function(data, type, row){
				if(data=='M'){
					return 'Male';
				}
				if(data=='F'){
					return 'Female';
				}
				if(data=='B'){
					return 'Both';
				}
				return data;
			}
			},
			{data: 'age_range'},
			{data: 'min'},
			{data: 'max'},
			{data: 'panic_range'},
			{data: 'descrip',name:'f.descrip'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		fixedColumns:   {
				left : 0,
				right: 1
				
				}
		
	});
	
	
	var table1 = new Tabulator("#fields_dt1", {
	 ajaxURL:"{{ route('lab.tests_fields.filter_code',app()->getLocale())}}", //ajax URL
     ajaxConfig:"POST",
	 ajaxParams: function(){
        var filter_lab=$('#fieldLABModal').find('#lab_num').val();
		var filter_test=$('#fieldLABModal').find('#test_id').val();
	   	return {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test};
           },
	height:280,
	placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:"local",
    paginationSize:5,
    paginationSizeSelector:[5,10, 25, 50, 100, true],
	paginationCounter:"rows",
	layout:"fitDataStretch",
	layoutColumnsOnNewData:true,
	movableRows:false,
    columns:[
		{title:"{{__('#')}}", field:"id"},
		{title:"{{__('Order')}}", field:"field_order"},
		{title:"{{__('Code')}}", field:"test_name",headerFilter:"input"},
        {title:"{{__('Description')}}",field:"descrip",headerFilter:"input"},
		{title:"{{__('Gender')}}",field: "gender",headerFilter:"input"},
		{title:"{{__('Age range')}}",field: "age_range",headerFilter:"input"},
		{title:"{{__('Min')}}",field: "min",headerFilter:"input"},
		{title:"{{__('Max')}}",field: "max",headerFilter:"input"},
		{title:"{{__('Unit')}}",field: "unit",headerFilter:"input"},
		{title:"{{__('Panic range')}}",field: "panic_range"},
		{title:"{{__('Remark')}}",field: "remark"},
		],
    });


	
	$('#create_test').click(function () {
		var lab_num = $('#filter_lab').val();
		$('#extLabForm').trigger("reset");
		$('.select2_modal').trigger('change.select2'); 
		$('#extLabForm').find('#field_type').val("new");   	 
        $('#extLabForm').find('#id').val('0');
		$('#extLabForm').find('#lab_num').val(lab_num);
		$('#modelHeading').html("{{__('Create a new field')}}");
		$('#inactiveBtn').hide();
		$('#test_id').prop('disabled',false);
        $('#fieldLABModal').modal('show');
		
    });
	
		
	$("#filter_status,#filter_test,#filter_lab").change(function() {
	  table.ajax.reload(); 
    });

    $('#test_id').change(function(){
		var filter_lab=$('#fieldLABModal').find('#lab_num').val();
		var filter_test=$('#fieldLABModal').find('#test_id').val();
		table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
		
	});	
	
	$('#inactiveBtn').click(function(e){
		e.preventDefault();
		var test_id = $('#extLabForm').find('#test_id').val();
		
		$.ajax({

            type: "POST",
            url: "{{ route('lab.tests_fields.inactiveFields',app()->getLocale()) }}",
            data:{test_id:test_id,type:'inactive'},
			dataType:"JSON",
            success: function (data) {
                Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
				$('#fieldLABModal').modal('hide');
            }
           });
	});
	
	
	
	$('#saveBtn').click(function (e) {

        e.preventDefault();
        var test = $('#extLabForm').find('#test_id').val();
		if(test==''){
			Swal.fire({text:"Please choose a code",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var name = $('#extLabForm').find('#descrip').val();
		/*if(name==''){
			Swal.fire({text:"Please input a description",icon:"error",customClass:"w-auto"});
			return false;
		}*/
		var is_comparison=$('#is_comparison').is(':checked');
		
		  var nv1 = $('#extLabForm').find('#normal_value1').val();
		  var nv2 = $('#extLabForm').find('#normal_value2').val();
		  if(nv1!='' &&  nv2!='' && parseFloat(nv2)<=parseFloat(nv1)){
	        Swal.fire({text:"Please input a second normal value greater than first normal value",icon:"error",customClass:"w-auto"});
		  return false;
	     } 
		var plv = $('#extLabForm').find('#panic_low_value').val();
		var phv = $('#extLabForm').find('#panic_high_value').val();
		if(plv !='' && phv !='' && parseFloat(phv)<=parseFloat(plv)){
			Swal.fire({text:"Please input a panic high value greater than panic low value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var fage = $('#extLabForm').find('#fage').val();
		if(fage!='' && fage!=0 && parseFloat(fage)<0){
			Swal.fire({text:"Please input a valid from_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		var tage = $('#extLabForm').find('#tage').val();	
		if(tage!='' && tage!=0 && parseFloat(tage)<0){
			Swal.fire({text:"Please input a valid to_age range",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		if(tage!='' && tage!=0 && fage!='' && fage!=0 && parseFloat(tage)<=parseFloat(fage)){
			Swal.fire({text:"Please input a to_age value greater than from_age value",icon:"error",customClass:"w-auto"});
			return false;
		}
		
		
        $.ajax({

          data: $('#extLabForm').serialize(),
          url: "{{ route('lab.tests_fields.store',app()->getLocale()) }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
               if($('#extLabForm').find('#field_type').val()=='edit'){
			       $('#extLabForm').trigger("reset");
                   $('#fieldLABModal').modal('hide');
				   
			     }
				 
			   if($('#extLabForm').find('#field_type').val()=='new'){
				  //$('#extLabForm').trigger("reset");
				  $('#extLabForm').find('.rdata').val('');
				  $('#extLabForm').find('.chkbox').prop('checked',false);
		          //$('.select2_modal').trigger('change.select2');
				  var lab_num = $('#filter_lab').val();
                  $('#extLabForm').find('#id').val('0');
		          $('#extLabForm').find('#lab_num').val(lab_num);
		          $('#extLabForm').find('#modalHeading').val('{{__("Create a new field")}}');
				  var filter_lab=$('#fieldLABModal').find('#lab_num').val();
		          var filter_test=$('#fieldLABModal').find('#test_id').val();
		          table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
			   }	 
			 
			  Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
              
			  
           }

      });

    });
	
	 $('#fieldLABModal').on('show.bs.modal',function(){
		var filter_lab=$('#fieldLABModal').find('#lab_num').val();
		var filter_test=$('#fieldLABModal').find('#test_id').val();
		table1.setData("{{route('lab.tests_fields.filter_code',app()->getLocale())}}", {_token:'{{csrf_token()}}',filter_lab:filter_lab,filter_test:filter_test},"POST");	
		
	 });
	 
	 $('#fieldLABModal').on('hide.bs.modal',function(){
		 table.ajax.reload();
	 });
	
	$('body').on('click', '.toggle-chk', function (e) {
        e.preventDefault();
		var id = $(this).data("id");
		var checked = $(this).is(':checked')?'O':'N';
	
        $.ajax({

            type: "DELETE",
            url: "{{ route('lab.tests_fields.delete',app()->getLocale()) }}",
            data:{id:id,checked:checked},
			dataType:"JSON",
            success: function (data) {
                Swal.fire({title:data.success,toast:true,icon:'success',showConfirmButton: false,timer:1500,position:'bottom-right'});
				table.ajax.reload();
            }
           });
	
    });
	

});
</script>
<script>
function editData(id) {
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
		  $('#id').val(res.data.id);
		  $('#lab_num').val(res.data.clinic_num);
		  
		  $('#descrip').val(res.data.descrip);
		  $('#gender').val(res.data.gender);
		  $('#panic_low_value').val(res.data.panic_low_value);
		  $('#panic_high_value').val(res.data.panic_high_value);
		  $('#normal_value1').val(res.data.normal_value1);
		  $('#normal_value2').val(res.data.normal_value2);
		  $('#tage').val(res.data.tage);
		  $('#fage').val(res.data.fage);
		  $('#field_order').val(res.data.field_order);
		  $('#sign_min').val(res.data.sign_min);
		  $('#sign_max').val(res.data.sign_max);
		  $('#sign').val(res.data.sign);
		  
		  /*if(res.data.is_comparison=='Y'){
		    $('#is_comparison').prop('checked',true);
		  }else{
			$('#is_comparison').prop('checked',false);  
		  }
		  if(res.data.desirable_low=='Y'){
		    $('#desirable_low').prop('checked',true);
		  }else{
			$('#desirable_low').prop('checked',false);  
		  }
		  if(res.data.desirable_high=='Y'){
		    $('#desirable_high').prop('checked',true);
		  }else{
			$('#desirable_high').prop('checked',false);  
		  }
		 		 
		  if(res.data.rtype=='' || res.data.rtype==null){
			$('#rtype').val('');  
		  }else{
		    $('#rtype').val(res.data.rtype);
		  }*/
		  
		  if(res.data.mytype=='' || res.data.mytype==null){
			$('#mytype').val('');  
		  }else{
		  $('#mytype').val(res.data.mytype);
		  }
		  
		  if(res.data.test_id=='' || res.data.test_id==null){
			$('#test_id').val('');  
		  }else{
		    $('#test_id').val(res.data.test_id);
		  }
		  
		  $('#test_id').prop('disabled',true);
		  $('#remark').val(res.data.remark);
		  $('#unit').val(res.data.unit);
		  //$('#criteria').val(res.data.criteria);
		  $('.select2_modal').trigger('change.select2');
		  
		  $('#modelHeading').html("{{__('Edit field')}}"+":#"+res.data.id);
          $('#extLabForm').find('#field_type').val("edit");
		  $('#inactiveBtn').show();
          $('#fieldLABModal').modal('show');
         
		 
		  
          }
      });

    }

function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }	
</script>
@endsection