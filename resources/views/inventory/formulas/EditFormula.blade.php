<!--
 DEV APP
 Created date : 28-12-2022
-->
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header">
				  
				    <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('Edit Formula')}}  </h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				  
					
				</div>
				
				<div class="card-body p-0">
					<div  class="row m-1"> 
                       
						<div class="col-md-6">	
						
					   </div>
					   <div class="col-md-6">
						 <a href="{{ route('inventory.formulas.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>					 
						 <!--<button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>-->
                        
					   </div>
				   
				    </div>
					<div  class="row m-1"> 
						
									<div class="col-md-4">
										<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
								    </div>
									
                       
						 <div class="col-md-3">
							 <label for="reqID_val"><b>{{__('Formula ID')}}</b></label>
							 <input  type="text" class="form-control" name="reqID_val"  value="{{$ReqFormula->id}}" id="reqID"  disabled /> 
							  <input  type="hidden" autoComplete="false"   id="formula_id"   value="{{$ReqFormula->id}}"/>
						  </div>	
						
							 <div class="col-md-5">
								<label for="name"><b>{{__('Description')}}&#xA0;&#xA0;</b></label>
									<div class="form-group">
									<input type="text" class="form-control" name="description" id="description"  value="{{$ReqFormula->name}}" >
									</div> 	
						</div>				
						</div>
					
						<div class="row m-1">
					
						
						 
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('From Price')}}</b></label>
								<input  class="form-control" value="" id="fprice"/>
								<input  type="hidden" id="cpt" value="{{$cptCount}}"/>
								<input  type="hidden" id="tbl"/>
							</div>
						</div>
					
					
											<div class="col-md-2 col-6">
														<label for="name"><b>{{__('To Price')}}&#xA0;&#xA0;</b></label>
															<div class="form-group">
															<input type="text" class="form-control" name="tprice" id="tprice"  >
												</div> 	
											</div>
					
					
					<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Multiple')}}</b></label>
								<input  class="form-control" id="multiple" value=""/>
							</div>
						</div>
					<div class="col-md-1 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('Division')}}</b></label>
								<input  class="form-control" id="division" value=""/>
							</div>
						</div>
					
				
						<div class="col-md-1 col-6">
						<div class="d-flex flex-column">					
					<label for="selecttax"><b>{{__("Plus")}}</b></label>					
						<input  class="form-control" id="plus" value=""/>
					</div> 	
				</div> 
					<div class="col-md-1 col-6">
						<div class="d-flex flex-column">					
					<label for="selecttax"><b>{{__("Minus")}}</b></label>					
						<input  class="form-control" id="minus" value=""/>
					</div> 	
				</div> 
				
					<div class="col-md-2 col-4">
					<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-sm btn-action"  id="btnadd" onclick="insRow()">{{__('Insert')}}</button>
						</div>
					</div>
                   
             
		        </div>
					
						
		        </div>
			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card">
				<div class="card-body">
						<div id="htmlTable" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTable" class="table table-striped table-bordered table-hover table-sm" style="text-align:center;">
									<thead>
										<tr class="txt-bg text-white text-center">
											
											<th scope="col" style="font-size:16px;">{{__('From Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('To Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Division')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Multiple')}}</th>										
											<th scope="col" style="font-size:16px;">{{__('Plus')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Minus')}}</th>
											<th scope="col"></th>
										</tr>
									</thead>
								   <tbody>
								    @php
										$cpt = 1;
									@endphp
									@foreach ($FormulaDeails as $sFormulaDeails) 
													<tr>
													<td>{{$sFormulaDeails->from_price}}</td>
													<td>{{$sFormulaDeails->to_price}}</td>
													<td>{{$sFormulaDeails->divise}}</td>
													<td>{{$sFormulaDeails->multiple}}</td>
													<td>{{$sFormulaDeails->plus}}</td>
													<td>{{$sFormulaDeails->minus}}</td>
													<td><input type="button" class="btn btn-delete" id="rowdelete{{$cpt}}" value="{{__('Delete')}}" onclick="deleteRow(this,'discount{{$cpt}}')" disabled /></td>
											        </tr>
											@php
											$cpt++;
											@endphp	 
									@endforeach
								   
								   </tbody>
								</table> 
							</div>
						</div>
						
						
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1  btn btn-action" id="btnsave" name="btnsave" onClick="saveformula()">{{__('Save')}}</button>
									 <button class="m-1  btn btn-action" id="btnmodify" name="btnmodify" onClick="modifyformula()" >{{__('Modify')}}</button>
									</div>	
									
						</div>					  
				</div>
            </div>				
		</section>


    </div>
</div>

@endsection	
@section('scripts')
   

<script>
$(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
    $('.select2_multiple').select2();
	
	$('.date_data').flatpickr({
		allowInput: true,
		enableTime: true,
		 time_24hr: true,
        dateFormat: "Y-m-d H:i",
        disableMobile: true
	});
			$("#fprice").val("0.00");
			$("#tprice").val("0.00");
			$("#plus").val("0.00");
			$("#minus").val("0.00");
			$("#division").val("0.00");
			$("#multiple").val("0.00");
			$("#tbl").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			


});				




 
  function insRow()
{
	
	
var v1 = $('#fprice').val(),v2=$('#tprice').val(),v3=$('#division').val(),v4=$('#multiple').val(),v5=$('#plus').val(),v6=$('#minus').val(); 
  
   if (!$.isNumeric(v1)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in From Price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
 if (!$.isNumeric(v2)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in To Price')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
 if (!$.isNumeric(v3)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in Division')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
 if (!$.isNumeric(v4)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in Multiple')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
 if (!$.isNumeric(v5)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in Plus')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
 if (!$.isNumeric(v6)){
			 Swal.fire({ 
            "text":"{{__('Please enter numeric value in Minus')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
		


var x=document.getElementById('myTable').insertRow(document.getElementById('myTable').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;
a.innerHTML=document.getElementById("fprice").value;
b.innerHTML=document.getElementById("tprice").value;
c.innerHTML=document.getElementById("multiple").value;
d.innerHTML=document.getElementById("division").value;
e.innerHTML=document.getElementById("plus").value;
f.innerHTML=document.getElementById("minus").value;
id_delete= ' onclick=deleteRow(this,"discount'+document.getElementById("cpt").value+'")';
g.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}"'+id_delete+'>';

document.getElementById("fprice").value="0.00";
document.getElementById("tprice").value="0.00";
document.getElementById("division").value="0.00";
document.getElementById("multiple").value="0.00";
document.getElementById("plus").value="0.00";
document.getElementById("minus").value="0.00";
$("#btnsave").removeAttr('disabled');
			
			

}
function deleteRow(r,rd)
{
var ii=r.parentNode.parentNode.rowIndex;
 document.getElementById('myTable').deleteRow(ii);
		if(document.getElementById('myTable').rows.length==1){
			
			$("#cpt").val("0");
}
	

}

function saveformula()
{
var fref=$('#description').val();
		  if(fref==''){
		  Swal.fire({text:"{{__('The Description  field is required')}}",icon:"error",customClass:"w-auto"});
		  return false;
		 }		
//for <td>
var x = document.getElementById("myTable").rows[0].cells.length;
var arr = new Array();

for(i=0;i<document.getElementById("myTable").rows.length;i++){
arr[i]={"FPRICE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"TPRICE":document.getElementById("myTable").rows[i].cells[1].innerHTML,"DIVISION":document.getElementById("myTable").rows[i].cells[3].innerHTML,"MULTIPLE":document.getElementById("myTable").rows[i].cells[2].innerHTML,"PLUS":document.getElementById("myTable").rows[i].cells[4].innerHTML,"MINUS":document.getElementById("myTable").rows[i].cells[5].innerHTML};	

}

var answer = confirm ("{{__('Are you sure?')}}")
   description=document.getElementById("description").value;
   reqID=document.getElementById("reqID").value;
    clinic_id=document.getElementById("clinic_id").value;
	 formula_id=reqID;
   Mastatus='N';
   if (formula_id!='')
   {
	 Mastatus='M';   
   }
if (answer){
var myjson=JSON.stringify(arr);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}",data:myjson,"status":Mastatus,"clinic_id":clinic_id,"formula_id":formula_id,"reqID":reqID,"description":description},
    url:'{{route("SaveFormula",app()->getLocale())}}',
    success: function(data){
		if(data.warning){	
			  Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		}
		if(data.success){	
			 Swal.fire({ 
              "title":data.success,
              "icon":"success",
			  "timer":3000,
			  "position":"bottom-right",
			  "showConfirmButton":false,
			  "toast": true});
		 
			document.getElementById("reqID").value=data.reqID;
			document.getElementById("formula_id").value=data.last_id;
			
			$("#btnadd").prop("disabled", true);
		   	$("#btnmodify").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');
			$("#btnsave").prop("disabled", true);
			$("#btndelete").removeAttr('disabled');
			var nb=document.getElementById("cpt").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			j++;
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
			
			} 
		
    }
 });
}
else{

}
}

function modifyformula()
{			$("#btncancel").removeAttr('disabled');
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);			
		    $("#btnadd").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			$("#selectpatient").removeAttr('disabled');
			$("#invoice_date_val").removeAttr('disabled');
			$("#comment").removeAttr('disabled');
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//	$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = false;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
}



</script>
<script>
  flatpickr('#date_pay', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#date_refund', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });	
flatpickr('#date_remise', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });		     				
 $("#NewItemModal").on('hidden.bs.modal', function(){
  //  alert('The modal is about to be hidden.');
	$.ajax({
           url: '{{route('Refresh_code',app()->getLocale())}}',
          type: 'get',
          dataType: 'json',
         success: function(data){
		   $("#NewItemModal").find('#fournisseur').trigger('change.select2');
		  $("#NewItemModal").find('#brand').trigger('change.select2');
		  $('#selectcode').html(data.html);
	 }
	});
	
	
  });		
</script>
<script>
function PopupCenter(url, title, w, h) {  
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
                  
        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
                  
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
        var top = ((height / 2) - (h / 2)) + dualScreenTop;  
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
      
        // Puts focus on the newWindow  
        if (window.focus) {  
            newWindow.focus();  
        }  
    }

function send_email_pat(){
		 
		 var patient_num = $('#selectpatient').val();
		 if(patient_num==null || patient_num=='0'){
			 Swal.fire({text:'{{__("Please choose a patient")}}',icon:'error',customClass:'w-auto'});
			 return false;
		 }
		 
		 var inv_id =  $('#invoice_id').val();
		 
				  $.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							   }
						   });
				  $.ajax({
						type : 'POST',
						url : '{{route("inventory.invoices.inv_pat_list",app()->getLocale())}}',
						data : { inv_id: inv_id,patient_num:patient_num},
						dataType: 'JSON',
						success: function(data){
						  $('#sendInvoiceModal').find('#ext_pat_id').val(patient_num);
						  $('#sendInvoiceModal').find('#ext_inv_id').val(inv_id);
						  $('#sendInvoiceModal').find('#patient_data').html(data.html_patient);
						  $('#sendInvoiceModal').modal('show');
						}
					  });	 
   }	
</script>

@endsection	
