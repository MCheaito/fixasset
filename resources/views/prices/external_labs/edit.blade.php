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
						  <h3 class="text-dark">{{__('Edit prices')}}</h3>
					   </div> 
					   <div class="col-md-2">
						  <select class=" m-1 form-control" name="manual_auto" id="manual_auto">
						    <option value="">Manual/Auto</option>
                            <option value="1">Manual</option>
							<option value="2">Auto</option>
						  </select>
					   </div> 
					</div>
                  </div>
			   
			  
			   <div class="card-body p-0">
			       	
				  
						    <div class="container-fluid">    
								<div class="m-1 row">
									 <div class="form-group col-md-3">
										  <label class="label-size">{{__('Guarantor')}}</label>
										  <input type="text" disabled class="form-control" name="lab_name" value="{{$pr->full_name}}"/>
										  <input type="hidden" value="{{$pr->id}}" id="pr_id"/>
						            </div>	
									<div class="form-group col-md-1">
										  <label class="label-size">{{__('Price$')}}</label>
										  <input type="text" name="priced" id="priced" value="{{$pr->priced}}" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcPrices();" class="form-control" />
									</div>
									<div class="form-group col-md-2">
										  <label class="label-size">{{__('PriceLBP')}}</label>
										  <input type="text" name="pricel" id="pricel" value="{{$pr->pricel}}" oninput="event.preventDefault();calcLBPrices();" onkeypress="return isNumberKey(event)"  class="form-control" />
									</div>
									
									<div class="form-group col-md-5">
 									   <button type="button" class="m-1 mt-4 btn btn-action" id="saveBtn">{{__('Update')}}</button>
									   <button type="button" class="m-1 mt-4 btn btn-action" data-toggle="modal" data-target="#copyPricesModal">{{__('Copy prices')}}</button>
									   @if(Session::has('extLAB'))
									     <a class="m-1 mt-4  float-right btn btn-back" href="{{route('external_labs.index',app()->getLocale())}}">{{__('Back')}}</a>
 								      @else
								        <a class="m-1 mt-4  float-right btn btn-back" href="{{route('prices.index',app()->getLocale())}}" onclick="localStorage.setItem('pricesTab', '#extlabs');">{{__('Back')}}</a>
									   @endif
									   <a class="m-1 mt-4  float-right btn btn-action" href="{{route('extlab_prices.create',app()->getLocale())}}">{{__('Create')}}</a>
									<!--<button type="button" class="m-1 mt-4 btn btn-action" onclick="exportData({{$pr->id}})">{{__('Export')}}</button>-->

										</div> 
								</div> 
								<div class="m-1 row">
								
								    <div class="col-md-12">
									  <!-- <label class="label-size">{{__('Select All')}}</label>
									   <input type="checkbox" onClick="toggle(this)" checked /> -->
										<table  id="dataTable_codes" class="table table-bordered table-striped data-table display compact" style="width:100%;">
											<thead>
												<tr>
												 <th nowrap>
												 {{__('Update/Copy')}}
												 <input type="checkbox" class="ml-1 chk-all" onclick="chkAllRows(this)"/>
												 </th>
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
												@php
												$cpt = 1;
												@endphp
											 @foreach($codes as $c)
											   <tr>
												   <td><input type="checkbox" class="chk-one"/></td>
												   <td>{{$c->testord}}</td>
												   <td>{{$c->test_name}}</td>
												   <td>{{$c->cnss}}</td>
												   <td>{{$c->nbl}}</td>
												   <td>
													@if(isset($code_prices[$c->id])) 
													 <label class="mt-2 slideon slideon-xs  slideon-success">
													 <input type="checkbox" value="{{$c->id}}" class="chk_manual" {{$code_prices[$c->id][0]=='Y'?'checked':''}} onchange="event.preventDefault();chg_state(this);"/>
													 <span class="slideon-slider"></span></label>
												    @else
													 <label class="mt-2 slideon slideon-xs  slideon-success">
													 <input type="checkbox" value="{{$c->id}}" class="chk_manual"  onchange="event.preventDefault();chg_state(this);"/>
													 <span class="slideon-slider"></span></label>	
													@endif	
												   </td>
												   <td>
												     @if(isset($code_prices[$c->id])) 
														@if($code_prices[$c->id][0]=='Y')
														 <input type="text" class="inputd" size="7" id="totald" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][1]}}" oninput="event.preventDefault();calcOnePrice(this);"/>
														@else	
														 <input type="text" class="inputd" size="7" id="totald" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][1]}}" oninput="event.preventDefault();calcOnePrice(this);" disabled="true" />
														@endif
												   @else
													   <input type="text" class="inputd" size="7" id="totald" onkeypress="return isNumberKey(event)"  oninput="event.preventDefault();calcOnePrice(this);" disabled="true" />
												   @endif	
												   </td>
												   <td>
												   @if(isset($code_prices[$c->id])) 
														@if($code_prices[$c->id][0]=='Y')
														 <input type="text" size="10" class="inputl" id="totall" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][2]}}" oninput="event.preventDefault();calcLBPrice(this);"/>
														@else	
														 <input type="text" class="inputl" size="10" id="totall" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][2]}}"  oninput="event.preventDefault();calcLBPrice(this);" disabled="true" />
														@endif
												   @else
													    <input type="text" class="inputl" size="10" id="totall" onkeypress="return isNumberKey(event)"  oninput="event.preventDefault();calcLBPrice(this);" disabled="true" />
												   @endif	
												   </td>
												  
												</tr> 
												@php
												$cpt++;
												@endphp	 												
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
@include('prices.external_labs.copyPricesModal')
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
      var table = $('#dataTable_codes').DataTable({
      	searching: true,
		paging: false,
		order: [[1,'asc']],
        info: true,
	    scrollY:"450px",
	    scrollX:true,
	    scrollCollapse: true,
		columnDefs:[
		{targets:0, orderable:false,searchable:false}
		],
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
	
	$('#manual_auto').change(function(){
		  var filterValue = $(this).val();
		  
		  table.rows().every(function() {
			var checkbox = $(this.node()).find('.chk_manual');
			var isChecked = checkbox.prop('checked');
			
			if (filterValue === '1') {
			  if (isChecked) {
				$(this.node()).show();
			  } else {
				$(this.node()).hide();
			  }
			} else if (filterValue === '2') {
			   if (!isChecked) {
				 $(this.node()).show();
			   } else {
				 $(this.node()).hide();
			   }
			} else if (filterValue === '') {
			  $(this.node()).show();
			} 
		  });
		//table.draw();
	});
	
  $('#copyPricesModal').on('show.bs.modal',function(){
	  
	  var checkboxes = document.querySelectorAll('.chk-one:checked');
	   if (checkboxes.length === 0) {
        Swal.fire({html:'{{__("Please choose at least one test in order to copy its prices")}}',customClass:'w-auto',icon:'error'});
        return false;
       }
	  
	 
	  
	  $.ajax({
		  url:'{{route("extlab_prices.getCats",app()->getLocale())}}',
		  type:'POST',
		  data:{_token:'{{csrf_token()}}',id:'{{$pr->id}}'},
		  dataType:'JSON',
		  success: function(data){
			 //$('#copyPricesModal').find('#modal_cat').val(data.category);
			 var filter = $('#copyPricesModal').find('#modal_cat').val();
			  if (filter === '') {
				$('.extLAB').parent().show();
			  } else {
				$('.extLAB').parent().hide();
				$('.extLAB[data-category="' + filter + '"]').parent().show();
			  }
		  }
	  });
  });	
	
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
	 
	  var checkboxes = document.querySelectorAll('.chk-one:checked');
	   if (checkboxes.length === 0) {
        Swal.fire({html:'{{__("Please choose at least one test in order to update its prices")}}',customClass:'w-auto',icon:'error'});
        return false;
       }
	
	  var arr=new Array();
	  var priced=$.isNumeric($('#priced').val()) && $('#priced').val()!=''?parseFloat($('#priced').val()).toFixed(2):0; 
	  var pricel=$.isNumeric($('#pricel').val()) && $('#pricel').val()!=''?parseFloat($('#pricel').val()).toFixed(0):0; 
	  
	  let i=0;
	  checkboxes.forEach(function(checkbox) {
		var row = checkbox.closest('tr');
		
		var test_id =row.cells[5].getElementsByTagName("input")[0].value;
		var checked = row.cells[5].getElementsByTagName("input")[0].checked;
		if(checked){
			 var totald =  row.cells[6].getElementsByTagName("input")[0].value;
			 totald = $.isNumeric(totald) && totald!='' && totald!=null?totald:0;
			 var totall =  row.cells[7].getElementsByTagName("input")[0].value;
			 totall = $.isNumeric(totall) && totall!='' && totall!=null?totall:0;
			 var is_manual = 'Y';
		}else{
			 var nbl = row.cells[4].innerHTML;
			 var nbl = $.isNumeric(nbl) && nbl!='' && nbl!=null?parseFloat(row.cells[4].innerHTML).toFixed(2):0;
			 var totald = nbl*priced;
			 var totall = nbl*pricel;
			 var is_manual = 'N';
		 }
         arr[i]={'test_id':Number(test_id),'is_manual':is_manual,'totald':Number(parseFloat(totald).toFixed(2)),'totall':Number(parseFloat(totall).toFixed(0))};	 
	     i++;
	  });

   $.ajax({
   url:'{{route("extlab_prices.update",app()->getLocale())}}',
   data:{_token:'{{csrf_token()}}',id:$('#pr_id').val(),manual:JSON.stringify(arr),
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



var checkboxes = $('#copyPricesModal').find('.extLAB');
var selectAllCheckbox = $('#copyPricesModal').find('.choose10');
var limit = 10;

$('#copyPricesModal').find('#modal_cat').change(function(e){
	e.preventDefault();
	var filter = $(this).val();
	var visibleCheckboxes = checkboxes.filter(':visible');
      if (filter === '') {
        $('.extLAB').parent().show();
      } else {
        $('.extLAB').parent().hide();
		$('.extLAB').prop('checked', false);
        $('.extLAB[data-category="' + filter + '"]').parent().show();
      }
            if (visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length) {
                selectAllCheckbox.prop('checked', true);
            } else if (visibleCheckboxes.filter(':checked').length === 0) {
                selectAllCheckbox.prop('checked', false);
            }
            if (visibleCheckboxes.filter(':checked').length >= limit) {
                selectAllCheckbox.prop('checked', true);
            }else {
                selectAllCheckbox.prop('checked', false);
            }
});

$(document).on('keyup', '#copyPricesModal #search-input', function(e) {
      e.preventDefault();
	  var searchText = $(this).val().toLowerCase();
	  var filter =Number($('#copyPricesModal').find('#modal_cat').val());
	  var visibleCheckboxes = checkboxes.filter(':visible');
	  $('.extLAB').each(function() {
        var labelText = $(this).next('label').text().toLowerCase();
        var checkboxContainer = $(this).closest('.checkbox-container');
        var category = Number($(this).data('category'));
        if ((searchText === '' || labelText.includes(searchText)) && (filter === 0 || category === filter)) {
           
		   checkboxContainer.show();
         } else {
           
		   checkboxContainer.hide();
		   $(this).prop('checked', false);
        }
		
            if (visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length) {
                selectAllCheckbox.prop('checked', true);
            } else if (visibleCheckboxes.filter(':checked').length === 0) {
                selectAllCheckbox.prop('checked', false);
            }
            if (visibleCheckboxes.filter(':checked').length >= limit) {
                selectAllCheckbox.prop('checked', true);
            }else {
                selectAllCheckbox.prop('checked', false);
            }
    });
	 
});


checkboxes.click(function () {
            var visibleCheckboxes = checkboxes.filter(':visible');
			var checkedCount = visibleCheckboxes.filter(':checked').length;
            if (checkedCount > limit) {
                Swal.fire({
                    icon: 'error',
                    html: "Oops...You can only choose up to " + limit + " guarantors.",
					customClass:'w-auto'
                });
                $(this).prop('checked', false);
            }
           
            if (visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length) {
                selectAllCheckbox.prop('checked', true);
            } else if (visibleCheckboxes.filter(':checked').length === 0) {
                selectAllCheckbox.prop('checked', false);
            }
            if (visibleCheckboxes.filter(':checked').length >= limit) {
                selectAllCheckbox.prop('checked', true);
            }else {
                selectAllCheckbox.prop('checked', false);
            }
        });
		
selectAllCheckbox.change(function(){ 
  var visibleCheckboxes = checkboxes.filter(':visible');
  var checked = $(this).prop('checked');
  visibleCheckboxes.prop('checked', checked);
  visibleCheckboxes.slice(limit).prop('checked', false);
  });
  
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
    
	if(price!='' && price!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var pricel = price*rated; 
	   rowElement.find('.inputl').val(parseFloat(pricel).toFixed(0));
	}else{
	   rowElement.find('#totall').val('');
	}
}

function calcLBPrices(){
	var pricel = $('#pricel').val();
	if(pricel!='' && pricel!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var priced = pricel/rated; 
	  $('#priced').val(parseFloat(priced).toFixed(2));
	}else{
	  $('#priced').val('');
	}
}

function calcLBPrice(el){
	var rowElement = $(el).closest('tr');
    var pricel = $(el).val();
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


function copyPrices(){
	
	var len = $('#copyPricesModal').find('.extLAB:checked').length;
	if(len==0){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Please choose at least one Guarantor")}}'});
		return false;
	}
	
	if(len>10){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Note: please choose no more than 10 Guarantors at a time")}}'});
		return false;
	}
	
	
	var dataToCopy = [];
    $('.chk-one').each(function() {
        if ($(this).is(':checked')) {
			var test_id = $(this).closest('tr').find('.chk_manual').val();
			dataToCopy.push(test_id);
			
                }
            });
	
	
	$('#testsCopy').val(dataToCopy);
	
	$.ajax({
		url:'{{route("extlab_prices.copy",app()->getLocale())}}',
		data: $('#copypricesForm').serialize(),
		type: 'POST',
		dataType:'JSON',
		success: function(data){
			       if (data.status === 'success') {
                            Swal.fire({toast:true,timer:3000,icon:'success',title:data.msg,position:'bottom-right',showConfirmButton:false});
		                    $('#copyPricesModal').modal('hide');
                        } else {
                            Swal.fire({icon:'error',text:data.msg,customClass:'w-auto'});
							return;
                        }
			
			/*$('.chk-one:checked').each(function(){
				$(this).prop('checked',false);
			});*/				
		},
		error: function(xhr, status, error) {
                    var msg='Request failed: ' + error;
					Swal.fire({icon:'error',text:msg,customClass:'w-auto'});  
				   }
	});
}	

function exportData(id){
	$.ajax({
		url:'{{route("extlab_prices.export",app()->getLocale())}}',
		type:'POST',
		data: {_token:'{{csrf_token()}}',id:id},
		dataType:'JSON',
		success: function(data){
			console.log("success");
		}
	});
}

function chkAllRows(el){
	var checked = $(el).is(':checked');
	if(checked){
		$('.chk-one').prop('checked',true);
	}else{
		$('.chk-one').prop('checked',false);
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