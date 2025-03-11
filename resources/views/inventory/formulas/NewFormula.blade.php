<!--
 DEV APP
 Created date : 24-12-2022
-->
@extends('gui.main_gui')
@section('content')						
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header text-white">
			      <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('New Formula')}}</h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				  
				</div>
				
				<div class="card-body p-0">
					<div  class="row m-1"> 
					
					   <div class="col-md-12">
						
					    <a href="{{ route('inventory.formulas.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						<!--<button class="m-1 float-right btn btn-action"  id="btnprintLabel" name="btnprintLabel" onClick="downloadPDFLabel()">{{__('Print label')}}</button>-->

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
							 <input  type="text" class="form-control" name="reqID_val"  value="" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="formula_id"   value=""/>
						  </div>
							<div class="col-md-5">
							<label for="name"><b>{{__('Description')}}{{'*'}}&#xA0;&#xA0;</b></label>
							<div class="form-group">
							<input type="text" class="form-control" name="description" id="description"  >
							</div> 	
					</div>				
					  
						  </div>
						
					
					<div class="row m-1">
					
						
						 
						<div class="col-md-2 col-6">
							<div class="d-flex flex-column">
								<label for="name"><b>{{__('From Price')}}</b></label>
								<input  class="form-control" value="" id="fprice"/>
								<input  type="hidden" id="cpt"/>
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
								<table id="myTable" class="table table-striped table-bordered table-hover" style="text-align:center;">
									<thead>
										<tr class="txt-bg text-white text-center">
											<th scope="col" style="font-size:16px;">{{__('From Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('To Price')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Multiple')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Division')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Plus')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Minus')}}</th>
											<th scope="col"></th>
											</tr>
									</thead>
								   <tbody></tbody>
								</table> 
							</div>
						</div>
						
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1 btn btn-action" id="btnsave" name="btnsave" onClick="saveformula()">{{__('Save')}}</button>
									 <button class="m-1 btn btn-action" id="btnmodify" name="btnmodify" onClick="modifyformula()">{{__('Modify')}}</button>
									 <button class="m-1 btn btn-reset" id="btncancel" name="btncancel" onClick="cancelformula()">{{__('Cancel')}}</button>
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

			$("#cpt").val("0");
			$("#cptp").val("0");
			$("#cptr").val("0");
			$("#fprice").val("0.00");
			$("#tprice").val("0.00");
			$("#plus").val("0.00");
			$("#minus").val("0.00");
			$("#division").val("0.00");
			$("#multiple").val("0.00");
			
			$("#tbl").val("");
			$("#description").val("");
			$("#comment").val("");
			$("#btnsave").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			
			$("#btndelete").prop("disabled", true);
		
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
arr[i]={"FPRICE":document.getElementById("myTable").rows[i].cells[0].innerHTML,"TPRICE":document.getElementById("myTable").rows[i].cells[1].innerHTML,"DIVISION":document.getElementById("myTable").rows[i].cells[2].innerHTML,"MULTIPLE":document.getElementById("myTable").rows[i].cells[3].innerHTML,"PLUS":document.getElementById("myTable").rows[i].cells[4].innerHTML,"MINUS":document.getElementById("myTable").rows[i].cells[5].innerHTML};	

}

var answer = confirm ("{{__('Are you sure?')}}")
   description=document.getElementById("description").value;
   reqID=document.getElementById("reqID").value;
    clinic_id=document.getElementById("clinic_id").value;
	 formula_id=document.getElementById("formula_id").value;
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
		//	alert(nb);
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

function cancelformula()
{			
			$("#btncancel").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btnprint").removeAttr('disabled');
			$("#btnprintLabel").removeAttr('disabled');			
			$("#btnpayment").removeAttr('disabled');			
		    $("#btnmodify").removeAttr('disabled');
			//$("#selecttax").removeAttr('disabled');
			$("#btnrefund").removeAttr('disabled');
			
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdelete"+j;
			var rdiscount="discount"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
				document.getElementById(rdel).disabled = true;
				document.getElementById(rdiscount).disabled = true;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
}


function downloadPDF(){	 
   var id=document.getElementById("reqID").value;	

      $.ajax({
           url: '{{route("downloadPDFInvoice",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoices.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	
	}
	
function downloadPDFLabel(){	
var id=document.getElementById("reqID").value;
 $.ajax({
           url: '{{route("inventory.download_pdf_label",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download=('Invoice_labels.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });

}	

</script>
<script>
        flatpickr('#invoice_date_due', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#invoice_date_val', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
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
     </script>
	
@endsection	
