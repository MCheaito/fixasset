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
										  <label class="label-size">{{__('Referred Lab')}}</label>
										  <input type="text" disabled class="form-control" name="ins_name" value="{{$pr->full_name}}"/>
										  <input type="hidden" value="{{$pr->id}}" id="pr_id"/>
										  <input type="hidden" id="choosen_ins_codes"/>
						            </div>	
									<div class="form-group col-md-1">
										  <label class="label-size">{{__('Price$')}}</label>
										  <input type="text" name="priced" id="priced" value="{{$pr->priced}}" onkeypress="return isNumberKey(event)" oninput="event.preventDefault();calcPrices();" class="form-control" />
									</div>
									<div class="form-group col-md-2">
										  <label class="label-size">{{__('PriceLBP')}}</label>
										  <input type="text" name="pricel" id="pricel" value="{{$pr->pricel}}" onkeypress="return isNumberKey(event)"  oninput="event.preventDefault();calcLBPrices();" class="form-control" />
									</div>
									
									
									<div class="form-group col-md-5">
 									   <button type="button" class="m-1 mt-4 btn btn-action" id="saveBtn">{{__('Update')}}</button>
									   <button type="button" class="mt-2 m-1 mt-4 btn btn-action" onclick="event.preventDefault();openCodesModal()">{{__('Add Code')}}</button>
									   <button type="button" class="m-1 mt-4 btn btn-action" data-toggle="modal" data-target="#copyPricesModal">{{__('Copy prices')}}</button>
									    @if(Session::has('INS'))
									     <a class="m-1 mt-4  float-right btn btn-back" href="{{route('external_insurance.index',app()->getLocale())}}">{{__('Back')}}</a>
 								        @else
 									     <a class="m-1 mt-4  float-right btn btn-back" href="{{route('prices.index',app()->getLocale())}}" onclick="localStorage.setItem('pricesTab', '#ins');">{{__('Back')}}</a>
									    @endif
									    <a class="m-1 mt-4  float-right btn btn-action" href="{{route('ins_prices.create',app()->getLocale())}}">{{__('Create')}}</a>
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
@include('prices.insurance.copyPricesModal')
@include('prices.insurance.addCodeModal')
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
      $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
	  var table = $('#dataTable_codes').DataTable({
      	searching: true,
		paging: false,
		order: [[1,'asc']],
        info: true,
	    scrollY:"450px",
	    scrollX:true,
	    scrollCollapse: true,
		ajax: {
            url: "{{ route('ins_prices.get',app()->getLocale()) }}",
			type: "POST",
			data: function ( d ) {
				d.pr_id = $('#pr_id').val();
				d.get_codes = $('#choosen_ins_codes').val();
				d.manual_auto = $('#manual_auto').val();
				d.type= 'edit';
				  }
			  },
        columns: [
		    {data: 'chk_one', name: 'chk_one', orderable: false, searchable: false},
			{data: 'testord'},
			{data: 'test_name'},
			{data: 'cnss'},
			{data: 'nbl'},
			{data: 'manual', name: 'manual', orderable: false, searchable: false},
			{data: 'totald', name: 'totald', orderable: false, searchable: false},
			{data: 'totall', name: 'totall', orderable: false, searchable: false},
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
	
	var table_codes = $('#dataTable_add_codes').DataTable({
      	processing:false,
		serverSide:true,
		searching: true,
		paging: false,
		ordering: false,
		info: true,
		ajax: {
            url: "{{ route('ins_prices.addcodes',app()->getLocale()) }}",
			type: "POST",
			data: function ( d ) {
				d.referred_lab = $('#pr_id').val();
				d.chosen_codes = $('#choosen_ins_codes').val();
				  }
			  },
	    columns: [
            {data: 'choose', orderable: false, searchable: false},
			{data: 'testord',name:'t.testord'},
			{data: 'cnss',name:'t.cnss'},
			{data: 'test_name',name:'t.test_name'},
			{data: 'referred_lab',name:'t.full_name'}
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
	
	$('#manual_auto').change(function(){
		  table.ajax.reload();
	});
  
  $('#addCodeModal').find('.choose_ins_allcodes').change(function(e){
		e.preventDefault();
		var checked = $(this).is(':checked');
		if(checked){
		     $('#dataTable_add_codes tbody tr').find('.add_ins_code').prop('checked',true);
		 }else{
			 $('#dataTable_add_codes tbody tr').find('.add_ins_code').prop('checked',false); 
		 }
	});
	
  $('#addCodeModal').on('show.bs.modal',function(){
	  table_codes.columns.adjust();
	  table_codes.ajax.reload(null, false);
  }); 

  $('#addCodeModal').on('hide.bs.modal',function(){
	  var choosen_codes = [];
	  $('#dataTable_add_codes tbody tr').find('.add_ins_code:checked').each(function(){
		  choosen_codes.push($(this).val());
		});
	 
	 $('#choosen_ins_codes').val(choosen_codes);
	 table.ajax.reload();
   });  
  
  
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
			 var totall =  nbl*pricel;
			 var is_manual = 'N';
		 }
         arr[i]={'test_id':Number(test_id),'is_manual':is_manual,'totald':Number(parseFloat(totald).toFixed(2)),'totall':Number(parseFloat(totall).toFixed(0))};	 
	     i++;
	  });
	  
	  
   $.ajax({
   url:'{{route("ins_prices.update",app()->getLocale())}}',
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

var checkboxes = $('#copyPricesModal').find('.extINS');
var selectAllCheckbox = $('#copyPricesModal').find('.choose10');
var limit = 10;


$(document).on('keyup', '#copyPricesModal #search-input', function(e) {
      e.preventDefault();
	  var searchText = $(this).val().toLowerCase();
	  
	  $('.extINS').each(function() {
        var labelText = $(this).next('label').text().toLowerCase();
        var checkboxContainer = $(this).closest('.checkbox-container');
        if (searchText === '' || labelText.includes(searchText)) {
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
                    html: "Oops...You can only choose up to " + limit + " referred labs.",
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

function calcOnePrice(el){
	var rowElement = $(el).closest('tr');
    var price = $(el).val();
    //console.log(price);	
	if(price!='' && price!=0){
	  var rated = '{{$lbl_usd}}'; 
	  var pricel = price*rated; 
	   rowElement.find('#totall').val(parseFloat(pricel).toFixed(0));
	}else{
	   rowElement.find('#totall').val('');
	}
}

function calcLBPrice(el){
	var rowElement = $(el).closest('tr');
    var pricel = $(el).val();
    //console.log(price);	
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
	var len = $('#copyPricesModal').find('.extINS:checked').length;
	if(len==0){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Please choose at least one Referred Lab")}}'});
		return false;
	}
	
	if(len>10){
		Swal.fire({icon:'error',customClass:'w-auto',html:'{{__("Note: please choose no more than 10 Referred Labs at a time")}}'});
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
		url:'{{route("ins_prices.copy",app()->getLocale())}}',
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
function resolvePercentage(el){
	var val=$(el).val();
	if(!Number(val)){
		$(el).val(0);
	}else{
		if(val<0){
			$(el).val(0);
		}
		if(val>100){
			$(el).val(100)
		}
	}
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

function openCodesModal(){
	 $('#addCodeModal').modal('show');
}		   	   	   
</script>

@endsection