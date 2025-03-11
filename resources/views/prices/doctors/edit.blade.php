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
						
					</div>
                  </div>
			   
			  
			   <div class="card-body p-0">
			       	
				  
						    <div class="container-fluid">    
								<div class="m-1 row">
									 <div class="form-group col-md-3">
										  <label class="label-size">{{__('Doctor')}}</label>
										  <input type="text" disabled class="form-control" name="lab_name" value="{{isset($pr->middle_name) && $pr->middle_name!=''?$pr->first_name.' '.$pr->middle_name.' '.$pr->last_name:$pr->first_name.' '.$pr->last_name }}"/>
										  <input type="hidden" value="{{$pr->id}}" id="pr_id"/>
						            </div>	
									<div class="form-group col-md-1">
										  <label class="label-size">{{__('Price$')}}</label>
										  <input type="text" name="priced" id="priced" value="{{$pr->priced}}" onkeypress="return isNumberKey(event)" onfocusout="event.preventDefault();calcPrices();" class="form-control" />
									</div>
									<div class="form-group col-md-2">
										  <label class="label-size">{{__('PriceLBP')}}</label>
										  <input type="text" name="pricel" id="pricel" value="{{$pr->pricel}}" onkeypress="return isNumberKey(event)"  class="form-control" />
									</div>
									<div class="form-group col-md-1">
										  <label class="label-size">{{__('Price€')}}</label>
										  <input type="text" name="pricee" id="pricee" value="{{$pr->pricee}}" onkeypress="return isNumberKey(event)"  class="form-control" />
									</div>
									<div class="form-group col-md-5">
 									   <button type="button" class="m-1 mt-4 btn btn-action" id="saveBtn">{{__('Update')}}</button>
									   <button type="button" class="m-1 mt-4 btn btn-action" data-toggle="modal" data-target="#copyPricesModal">{{__('Copy prices')}}</button>
 									   @if(Session::has('DOCTOR'))
									     <a class="m-1 mt-4  float-right btn btn-back" href="{{route('resources.index',app()->getLocale())}}">{{__('Back')}}</a>
 								      @else
								        <a class="m-1 mt-4  float-right btn btn-back" href="{{route('prices.index',app()->getLocale())}}" onclick="localStorage.setItem('pricesTab', '#doctors');">{{__('Back')}}</a>
									   @endif
									   <a class="m-1 mt-4  float-right btn btn-action" href="{{route('doctor_prices.create',app()->getLocale())}}">{{__('Create')}}</a>
										</div> 
								</div> 
								<div class="m-1 row">
								    <div class="col-md-12">
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
							                     <th>{{__('Manual')}}</th>
												 <th>{{__('Total$')}}</th>
												 <th>{{__('TotalLBP')}}</th>
							                     <th>{{__('Total€')}}</th>
												 
												</tr>
											</thead>
											<tbody id="myRows">
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
														 <input type="text" size="5" id="totald" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][1]}}" onfocusout="event.preventDefault();calcOnePrice(this);"/>
														@else	
														 <input type="text" size="5" id="totald" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][1]}}" onfocusout="event.preventDefault();calcOnePrice(this);" disabled="true" />
														@endif
												   @else
													   <input type="text" size="5" id="totald" onkeypress="return isNumberKey(event)"  onfocusout="event.preventDefault();calcOnePrice(this);" disabled="true" />
												   @endif	
												   </td>
												   <td>
												   @if(isset($code_prices[$c->id])) 
														@if($code_prices[$c->id][0]=='Y')
														 <input type="text" size="8" id="totall" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][2]}}" />
														@else	
														 <input type="text" size="8" id="totall" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][2]}}"  disabled="true" />
														@endif
												   @else
													    <input type="text" size="8" id="totall" onkeypress="return isNumberKey(event)"   disabled="true" />
												   @endif	
												   </td>
												   <td>
												    @if(isset($code_prices[$c->id])) 
														@if($code_prices[$c->id][0]=='Y')
														 <input type="text" size="5" id="totale" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][3]}}" />
														@else	
														 <input type="text" size="5" id="totale" onkeypress="return isNumberKey(event)" value="{{$code_prices[$c->id][3]}}" disabled="true" />
														@endif
												   @else
													   <input type="text" size="5" id="totale" onkeypress="return isNumberKey(event)"  disabled="true" />
												   @endif	   
												   </td>
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
@include('prices.doctors.copyPricesModal')
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
   $('#copyPricesModal').on('show.bs.modal',function(){
	  
	  var checkboxes = document.querySelectorAll('.chk-one:checked');
	   if (checkboxes.length === 0) {
        Swal.fire({html:'{{__("Please choose at least one test in order to copy its prices")}}',customClass:'w-auto',icon:'error'});
        return false;
       }
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
	  var pricel=$.isNumeric($('#pricel').val()) && $('#pricel').val()!=''?parseFloat($('#pricel').val()):0; 
	  var pricee=$.isNumeric($('#pricee').val()) && $('#pricee').val()!=''?parseFloat($('#pricee').val()).toFixed(2):0;
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
			 var totale =  row.cells[8].getElementsByTagName("input")[0].value;
			 totale = $.isNumeric(totale) && totale!='' && totale!=null?totale:0;
			 var is_manual = 'Y';
		}else{
			 var nbl = row.cells[4].innerHTML;
			 var nbl = $.isNumeric(nbl) && nbl!='' && nbl!=null?parseFloat(row.cells[4].innerHTML).toFixed(2):0;
			 var totald = nbl*priced;
			 var totall = nbl*pricel;
			 var totale = nbl*pricee;
			 var is_manual = 'N';
		 }
         arr[i]={'test_id':Number(test_id),'is_manual':is_manual,'totald':Number(parseFloat(totald).toFixed(2)),'totall':Number(parseFloat(totall)),'totale':Number(parseFloat(totale).toFixed(2))};	 
	     i++;
	  });

  
   $.ajax({
   url:'{{route("doctor_prices.update",app()->getLocale())}}',
   data:{_token:'{{csrf_token()}}',id:$('#pr_id').val(),manual:JSON.stringify(arr),
         priced:priced,pricel:pricel,pricee:pricee},
   type:'POST',
   dataType:'JSON',
   success:function(data){
	   Swal.fire({title:data.success,icon:'success',showConfirmButton:false,
	             toast:true,position:'bottom-right',timer:1500}).then(function(){
			window.location.href=data.location;		 
				 }); 
   }});
  }

$('#copyPricesModal').find('#modal_cat').change(function(e){
	e.preventDefault();
	var filter = $(this).val();
	
      if (filter === '') {
        $('.doc').parent().show();
      } else {
        $('.doc').parent().hide();
        $('.doc[data-category="' + filter + '"]').parent().show();
      }

});

var checkboxes = $('#copyPricesModal').find('.doc');
var selectAllCheckbox = $('#copyPricesModal').find('.choose10');
var limit = 10;

$(document).on('keyup', '#copyPricesModal #search-input', function(e) {
      e.preventDefault();
	  var searchText = $(this).val().toLowerCase();
	  var filter =Number($('#copyPricesModal').find('#modal_cat').val());
	
	  $('.doc').each(function() {
        var labelText = $(this).next('label').text().toLowerCase();
        var checkboxContainer = $(this).closest('.checkbox-container');
        var category = Number($(this).data('category'));
        if ((searchText === '' || labelText.includes(searchText)) && (filter === 0 || category === filter)) {
           
		   checkboxContainer.show();
         } else {
           $(this).prop('checked', false);
		   checkboxContainer.hide();
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
                    html: "Oops...You can only choose up to " + limit + " doctors.",
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
	  var ratee = '{{$lbl_euro}}'; 
	  var pricel = price*rated; 
	  var pricee = pricel/ratee;
	  $('#pricel').val(parseFloat(pricel));
	  $('#pricee').val(parseFloat(pricee).toFixed(2));
	}else{
	  $('#pricel').val('');
	  $('#pricee').val('');	
	}
}

function calcOnePrice(el){
	var rowElement = $(el).closest('tr');
    var price = $(el).val();
    //console.log(price);	
	if(price!='' && price!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var ratee = '{{$lbl_euro}}';
	  var pricel = price*rated; 
	  var pricee = pricel/ratee;
	   rowElement.find('#totall').val(parseFloat(pricel));
	   rowElement.find('#totale').val(parseFloat(pricee).toFixed(2));
	}else{
	   rowElement.find('#totall').val('');
	   rowElement.find('#totale').val('');	
	}
}

function chg_state(el){
	var checked = $(el).is(':checked');
	var rowElement = $(el).closest('tr');
	rowElement.find('#totald').val('');
	rowElement.find('#totall').val('');
	rowElement.find('#totale').val('');	
	if(checked){
	 rowElement.find('#totald').prop('disabled',false);
	 rowElement.find('#totall').prop('disabled',false);
	 rowElement.find('#totale').prop('disabled',false);
	}else{
	 rowElement.find('#totald').prop('disabled',true);
	 rowElement.find('#totall').prop('disabled',true);
	 rowElement.find('#totale').prop('disabled',true);	
	}
}

function copyPrices(){
	var len = $('#copyPricesModal').find('.doc:checked').length;
	if(len==0){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Please choose at least one doctor")}}'});
		return false;
	}
	
	if(len>10){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Note: please choose no more than 10 external labs at a time")}}'});
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
		url:'{{route("doctor_prices.copy",app()->getLocale())}}',
		data: $('#copypricesForm').serialize(),
		type: 'POST',
		dataType:'JSON',
		success: function(data){
			Swal.fire({toast:true,timer:3000,icon:'success',title:data.success,position:'bottom-right',showConfirmButton:false});
		    $('#copyPricesModal').modal('hide');
			/*$('.chk-one:checked').each(function(){
				$(this).prop('checked',false);
			});*/	
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
</script>

@endsection