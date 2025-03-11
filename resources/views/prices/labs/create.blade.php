<!-- 
 DEV APP
 Created date : 16-2-2024
-->
@extends('gui.main_gui')
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
						<div class="col-md-4">
						  <h3 class="text-dark">{{__('Create prices')}}</h3>
					   </div> 
						
					</div>
                  </div>
			   
			  
			   <div class="card-body p-0">
			       	
				  
						    <div class="container-fluid">    
								<div class="m-1 row">
									 <div class="form-group col-md-4">
										  <label class="label-size">{{__('Lab')}}</label>
										  <input type="text" disabled class="form-control" value="{{$lab->full_name}}"/>
										  <input type="hidden" id="lab_id" value="{{$lab->id}}"/>
						            </div>	
									<div class="form-group col-md-1">
										  <label class="label-size">{{__('Price$')}}</label>
										  <input type="text" name="priced" id="priced" value="{{isset($lab->priced)?$lab->priced:old('priced')}}" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcPrices();" class="form-control" />
									</div>
									<div class="form-group col-md-2">
										  <label class="label-size">{{__('PriceLBP')}}</label>
										  <input type="text" name="pricel" id="pricel" value="{{isset($lab->pricel)?$lab->pricel:old('pricel')}}" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcLBPrices();" class="form-control" />
									</div>
									
									<div class="form-group col-md-4">
 									   <button type="button" class="m-1 mt-4 btn btn-action" id="saveBtn">{{__('Save')}}</button>
								       <a class="m-1 mt-4  float-right btn btn-back" href="{{route('branches.index',app()->getLocale())}}">{{__('Back')}}</a>
									</div> 
								</div> 
								<div class="m-1 row">
								    <div class="col-md-12">
										<table  id="dataTable_codes" class="table table-bordered table-striped data-table display compact" style="width:100%;">
											<thead>
												<tr>
												 <th>{{__('Order')}}</th>
												 <th>{{__('Code')}}</th>
												 <th>{{__('CNSS')}}</th>
							                     <th>{{__('NBL')}}</th>
							                     <th nowrap>{{__('Manual')}}
												 <label class="mt-2 slideon slideon-xs  slideon-success">
												 <input type="checkbox" class="ml-1 chk-all" onclick="chkAllManual(this)"/>
												 <span class="slideon-slider"></span>
												 </label>
												 </th>
												 <th>{{__('Total$')}}</th>
												 <th>{{__('TotalLBP')}}</th>
												</tr>
											</thead>
											<tbody id="myRows">
											 @foreach($codes as $c)
											   <tr>
												   <td>{{$c->testord}}</td>
												   <td>{{$c->test_name}}</td>
												   <td>{{$c->cnss}}</td>
												   <td>{{$c->nbl}}</td>
												   <td>
													 <label class="mt-2 slideon slideon-xs  slideon-success">
													 <input type="checkbox" value="{{$c->id}}" class="chk_manual" onchange="event.preventDefault();chg_state(this);"/>
													 <span class="slideon-slider"></span></label>
												   </td>
												   <td><input type="text" size="7" id="totald" oninput="event.preventDefault();calcOnePrice(this);" onkeypress="return isNumberKey(event)" disabled="true" /></td>
												   <td><input type="text" size="10" id="totall" oninput="event.preventDefault();calcLBPrice(this);" onkeypress="return isNumberKey(event)" disabled="true" /></td>
												</tr>   
											 @endforeach
											</tbody>
										</table>
									</div> 
								</div>	
							</div>  
						</div>
						
						  
				   </div>
			 </div>
     </div>			   
	
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
      var table = $('#dataTable_codes').DataTable({
      	searching: true,
		paging: false,
		order: [[0,'asc']],
        info: true,
	    scrollY:"450px",
	    scrollX:true,
	    scrollCollapse: true,
        language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
					paginate: {
									first:      "{{__('First')}}",
									previous:   "{{__('Previous')}}",
									next:       "{{__('Next')}}",
									last:       "{{__('Last')}}"
								}
					}
    });
	
	table.columns.adjust();
  $('#saveBtn').click(function(e){
	  if (table.search() !== '') { 
		 Swal.fire({
					html: '{{__("To save your prices, please clear your search?")}}',
					icon: 'warning',
					customClass:'w-auto',
					showCancelButton: true,
					confirmButtonText: 'Yes, clear it',
					cancelButtonText: 'No, cancel',
				}).then((result) => {
					if (result.isConfirmed) {
						table.search('').draw(); // Clear search filter and redraw the table
						// Save data after a short delay
						setTimeout(saveData, 1000);
					}
				});
			} else {
				// Save data directly if search filter is not active
				saveData();
			}
	 
  
  });
   
 function saveData(){
	  
	  if($('#priced').val()==''){
		  Swal.fire({icon:'error',html:'{{__("Please input the external lab price in DOLLAR")}}',customClass:'w-auto'});
		  return false;
	  }
	  if($('#pricel').val()==''){
		  Swal.fire({icon:'error',html:'{{__("Please input the external lab price in LBP")}}',customClass:'w-auto'});
		  return false;
	  }
	  
	  var arr=new Array();
	  var priced=$.isNumeric($('#priced').val()) && $('#priced').val()!=''?parseFloat($('#priced').val()).toFixed(2):0; 
	  var pricel=$.isNumeric($('#pricel').val()) && $('#pricel').val()!=''?parseFloat($('#pricel').val()).toFixed(0):0; 
	  
	  
	  for(i=0;i<document.getElementById("myRows").rows.length;i++){
		 var checked = document.getElementById("myRows").rows[i].cells[4].getElementsByTagName("input")[0].checked;
         var test_id = document.getElementById("myRows").rows[i].cells[4].getElementsByTagName("input")[0].value; 
		 if(checked){
			 var totald =  document.getElementById("myRows").rows[i].cells[5].getElementsByTagName("input")[0].value;
			 totald = $.isNumeric(totald) && totald!='' && totald!=null?totald:0;
			 var totall =  document.getElementById("myRows").rows[i].cells[6].getElementsByTagName("input")[0].value;
			 totall = $.isNumeric(totall) && totall!='' && totall!=null?totall:0;
			 var is_manual = 'Y';
		}else{
			 var nbl = document.getElementById("myRows").rows[i].cells[3].innerHTML;
			 var nbl = $.isNumeric(nbl) && nbl!='' && nbl!=null?parseFloat(document.getElementById("myRows").rows[i].cells[3].innerHTML).toFixed(2):0;
			 var totald = nbl*priced;
			 var totall = nbl*pricel;
			 var is_manual = 'N';
		 }
         arr[i]={'test_id':Number(test_id),'is_manual':is_manual,'totald':Number(parseFloat(totald).toFixed(2)),'totall':Number(parseFloat(totall).toFixed(0))};	 
	  }

  
   $.ajax({
   url:'{{route("lab_prices.store",app()->getLocale())}}',
   data:{_token:'{{csrf_token()}}',lab_id:$('#lab_id').val(),manual:JSON.stringify(arr),
         priced:priced,pricel:pricel},
   type:'POST',
   dataType:'JSON',
   success:function(data){
	   Swal.fire({title:data.success,icon:'success',showConfirmButton:false,
	             toast:true,position:'bottom-right',timer:1500}).then(function(){
			window.location.href=data.location;		 
				 }); 
   }});
  
 }
	
});
</script>
<script>
function isNumberKey(evt){
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
	   
function calcPrices(){
	var price = $('#priced').val();
	if(price!='' && price!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var pricel = price*rated; 
	  $('#pricel').val(parseFloat(pricel).toFixed(0));
	}else{
	  $('#pricel').val('');
	}
}

function calcOnePrice(el){
	var rowElement = $(el).closest('tr');
    var price = $(el).val();
    console.log(price);	
	if(price!='' && price!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var pricel = price*rated; 
	   rowElement.find('#totall').val(parseFloat(pricel).toFixed(0));
	}else{
	   rowElement.find('#totall').val('');
	}
}

function calcLBPrices(){
	var pricel = $('#pricel').val();
	if(pricel!='' && pricel!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var ratee = '{{$lbl_euro}}'; 
	  var priced = pricel/rated; 
	  $('#priced').val(parseFloat(priced).toFixed(2));
	}else{
	  $('#priced').val('');
	}
}

function calcLBPrice(el){
	var rowElement = $(el).closest('tr');
    var pricel = $(el).val();
    console.log(pricel);	
	if(pricel!='' && pricel!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var priced = pricel/rated; 
	   rowElement.find('#totald').val(parseFloat(priced).toFixed(2));
	}else{
	   rowElement.find('#totald').val('');
	}
}

function chg_state(el){
	var checked = $(el).is(':checked');
	var rowElement = $(el).closest('tr');
	rowElement.find('#totald').val('');
	rowElement.find('#totall').val('');
	if(checked){
	 rowElement.find('#totald').prop('disabled',false);
	 rowElement.find('#totall').prop('disabled',false);
	}else{
	 rowElement.find('#totald').prop('disabled',true);
	 rowElement.find('#totall').prop('disabled',true);
	}
}

function chkAllManual(el){
	var checked = $(el).is(':checked');
	if(checked){
		$('.chk_manual').prop('checked',true);
		var rowElement = $('.chk_manual').closest('tr');
	    rowElement.find('#totald').val('');
	    rowElement.find('#totall').val('');
	    rowElement.find('#totald').prop('disabled',false);
	    rowElement.find('#totall').prop('disabled',false);
	}else{
		$('.chk_manual').prop('checked',false);
		var rowElement = $('.chk_manual').closest('tr');
	    rowElement.find('#totald').val('');
	    rowElement.find('#totall').val('');
	    rowElement.find('#totald').prop('disabled',true);
	    rowElement.find('#totall').prop('disabled',true);
		}
}	   
</script>

@endsection