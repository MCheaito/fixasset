<!--
 DEV APP
 Created date : 28-12-2022
-->
@extends('gui.main_gui')
@section('content')		
<style>
    .from-section h5 {
        text-decoration: underline;
    }
</style>
				
<div class="container-fluid"> 
	<div class="row mt-2">
        <section class="col-md-12">	
			<div class="card">	  
				<div class="card-header">
				  <div class="row">
					  <div class="col-md-9 col-7"><h5>{{__('Edit Entry')}}</h5></div>
				      <div class="col-md-3 col-5">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
					    <span class="m-1 float-right badge label-size bg-gradient-danger text-white">{{'* : '.__('Mandatory')}}</span>
                      </div>
                    </div>
				
				
				 
				</div>
				
				<div class="card-body p-0">
				    <div  class="row m-1"> 
						<!--<div class="col-md-6">	

						 <button class="m-1 btn btn-action" id="btnpayment" name="btnpayment" onClick="paymentinvoice()">{{__('Pay')}}</button>
						 <button class="m-1 btn btn-action" id="btnrefund" name="btnrefund" onClick="refundinvoice()">{{__('Reimburse')}}</button>
						 
					   </div>-->
					   <div class="col-md-6">
						 <a href="{{ route('inventory.invoices.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						 <button class="m-1 float-right btn btn-action"  id="btnprintLabel" name="btnprintLabel" onClick="downloadPDFLabel()" hidden >{{__('Print label')}}</button>
						 <button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>

					   </div>
				   
				    </div>
					<div  class="row m-1"> 
						
									<div class="col-md-3">
										<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
										<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
										<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
										<input  type="hidden" id="cpt" value="{{$cptCount}}"/>
										<input  type="hidden" id="tbl"/>
									</div>
									
                               									
						  <div class="col-md-3">
							 <label for="reqID_val"><b>{{__('Invoice Nb')}}</b></label>
							 <input  type="text" class="form-control" name="reqID_val"  value="{{$ReqAccount->serial}}" id="reqID"  disabled /> 
							 <input  type="hidden" autoComplete="false"   id="invoice_id"   value="{{$ReqAccount->id}}"/>
						  </div>
						  <div class="col-md-3">
							 <label for="invoice_date_val"><b>{{__('Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_val" id="invoice_date_val" value="{{Carbon\Carbon::parse($ReqAccount->datein)->format('Y-m-d H:i')}}"  disabled />
					  </div>
					    <div class="col-md-3"  >
							 <label for="invoice_date_due"><b>{{__('Due Date')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_date_due" id="invoice_date_due" value="{{Carbon\Carbon::parse($ReqAccount->dateout)->format('Y-m-d H:i')}}"  disabled />
					  </div>
						  </div>
															
					  <div  class="row m-1">
							  <div class="form-group col-md-2 select2-teal">
												<label for="pro"><b>{{__('Type').'*'}}</b></label>
													<select class="select2_data custom-select rounded-0" name="stype" id="stype" style="width:100%;" disabled >
													<option value="0" {{isset($ReqAccount) && $ReqAccount->type == '0' ? 'selected' : ''}}>{{__('Receipts')}}</option>		
													<option value="1" {{isset($ReqAccount) && $ReqAccount->type == '1' ? 'selected' : ''}}>{{__('Cashing')}}</option>
													<option value="2" {{isset($ReqAccount) && $ReqAccount->type == '2' ? 'selected' : ''}}>{{__('On the account')}}</option>
													<option value="3" {{isset($ReqAccount) && $ReqAccount->type == '3' ? 'selected' : ''}}>{{__('Invoices')}}</option>
													<option value="4" {{isset($ReqAccount) && $ReqAccount->type == '4' ? 'selected' : ''}}>{{__('Returned')}}</option>
													<option value="5" {{isset($ReqAccount) && $ReqAccount->type == '5' ? 'selected' : ''}}  >{{__('Discount')}}</option>
													</select>
																	
							 </div>
							 <div class="col-md-2">
							 <label for="invoice_ref"><b>{{__('Reference')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_ref" id="invoice_ref" value="{{$ReqAccount->refer}}" disabled />
							 </div>
							    <div class="form-group col-md-2 select2-teal">
												<label for="pro"><b>{{__('Source').'*'}}</b></label>
											<select class="select2_data custom-select rounded-0" name="selectsource" id="selectsource" style="width:100%;" disabled >
											<option value="0">{{__('Choose a source')}}</option>		
											@foreach($source as $ssource)
											<option value="{{$ssource->id}}" {{isset($ReqAccount) && $ReqAccount->source == $ssource->id ? 'selected' : ''}}>{{$ssource->name}}</option>
											@endforeach 
											</select>
																	
							 </div>
							 <div class="col-md-6">
							 <label for="invoice_rq"><b>{{__('Remark')}}</b></label>
							 <input type="text" class="form-control"  name="invoice_rq" id="invoice_rq" value="{{$ReqAccount->rq}}" disabled />
							 </div>	 
						 </div> 
						<div  class="row m-1"> 
									<div class="form-group col-md-3 select2-teal">
										<label for="pro"><b>{{__('From').'*'}}</b></label>
											<select class="select2_data custom-select rounded-0" name="fselectpro" id="fselectpro" style="width:100%;" disabled >
											<option value="0">{{__('Choose a supplier')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}" >{{$fournisseurs->name}}</option>
											@endforeach 
											</select>
															
                                    </div>
							 <div class="form-group col-md-3 select2-teal">
												<label for="pro"><b>{{__('Currency')}}</b></label>
													<select class="select2_data custom-select rounded-0" name="fcurrency" id="fcurrency" style="width:100%;" disabled >
													<option value="LBP" >{{__('LBP')}}</option>		
													<option value="USD" >{{__('USD')}}</option>
													<option value="EUR" >{{__('EUR')}}</option>
													</select>
																	
							 </div>	
									<div class="col-md-2 col-3">
									<div class="d-flex flex-column">
									<label for="name"><b>{{__('Rate')}}</b></label>
									<input  class="form-control" value="" id="fvalrate" name="fvalrate" disabled />
									</div>	
									</div>							 
									<div class="col-md-2 col-3">
									<div class="d-flex flex-column">
									<label for="name"><b>{{__('Amount')}}</b></label>
									<input  class="form-control" value="" id="fvalamount" name="fvalamount" disabled />
									</div>
									</div>
						 
								
					</div>
					<div class="row m-1">
					
					<div class="form-group col-md-3 select2-teal">
										<label for="pro"><b>{{__('To').'*'}}</b></label>
											<select class="select2_data custom-select rounded-0" name="tselectpro" id="tselectpro" style="width:100%;" disabled >
											<option value="0">{{__('Choose a supplier')}}</option>		
											@foreach($fournisseur as $fournisseurs)
											<option value="{{$fournisseurs->id}}" >{{$fournisseurs->name}}</option>
											@endforeach 
											</select>
															
                                    </div>
							 <div class="form-group col-md-3 select2-teal">
												<label for="pro"><b>{{__('Currency')}}</b></label>
													<select class="select2_data custom-select rounded-0" name="tcurrency" id="tcurrency" style="width:100%;" disabled >
													<option value="LBP" >{{__('LBP')}}</option>		
													<option value="USD" >{{__('USD')}}</option>
													<option value="EUR" >{{__('EUR')}}</option>

													</select>
																	
							 </div>	
									<div class="col-md-2 col-3">
									<div class="d-flex flex-column">
									<label for="name"><b>{{__('Rate')}}</b></label>
									<input  class="form-control" value="" id="tvalrate" name="tvalrate" disabled />
									</div>	
									</div>							 
									<div class="col-md-2 col-3">
									<div class="d-flex flex-column">
									<label for="name"><b>{{__('Amount')}}</b></label>
									<input  class="form-control" value="" id="tvalamount" name="tvalamount" disabled />
									</div>
									</div>
						<div class="col-md-2 col-4">
							<div class="d-flex flex-column">
							 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
							 <button class="btn btn-sm btn-action"  id="btnadd" onclick="insRow()" disabled >{{__('Insert')}}</button>
							</div>
						</div>	 	   
					</div>


					
		        </div>
			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card">
				<div class="card-body">
						<div class="m-1 row">
						   <div class="col-md-6"></div>
						   <div class="col-md-6">
							 <input  class="text-center form-control"  id="myInput"  onkeyup="myFunction()" placeholder="{{__('Search for item name...')}}">
						   </div>
						  </div>
						<div id="htmlTable" class="row mt-2 m-1">
							<div class="table-responsive">
								<table id="myTable" class="table table-striped table-bordered table-hover" style="text-align:center;"  >
									<thead>
										<tr class="txt-bg text-white text-center">
											<th scope="col" style="font-size:16px;">{{__('#')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('fcode')}}</th>
											<th scope="col" style="font-size:16px;">{{__('From')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Currency')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Rate')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Amount')}}</th>
											<th scope="col" style="font-size:16px;display:none;">{{__('tcode')}}</th>
											<th scope="col" style="font-size:16px;">{{__('To')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Currency')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Rate')}}</th>
											<th scope="col" style="font-size:16px;">{{__('Amount')}}</th>
											<th scope="col"></th>
											
										</tr>
									</thead>
								   <tbody>
								    @php
										$cpt = 1;
									@endphp
									@foreach ($ReqDeails as $sReqDeails) 
													<tr>
													<td>{{$cpt}}</td>
													<td  style="display:none;">{{$sReqDeails->admi1}}</td>
													<td>{{$sReqDeails->name1}}</td>
													<td>
													<select class="form-control" id="fcurrency{{$cpt}}">
														<option value="USD" {{ ($sReqDeails->curency1 === "USD" ? 'selected' : '') }}>USD</option> 
														<option value="LBP" {{ ($sReqDeails->curency1 === "LBP" ? 'selected' : '') }}>LBP</option> 
														<option value="EUR" {{ ($sReqDeails->curency1 === "EUR" ? 'selected' : '') }}>EUR</option>
													</select>
													</td>
													<td><input type="text" class="form-control" size="3"  id="fvalrate{{$cpt}}" value="{{$sReqDeails->prcurren1}}"  /></td>											
													<td><input type="text" class="form-control" size="3"  id="fvalamount{{$cpt}}" value="{{$sReqDeails->amount1}}" /></td>																						    
													<td style="display:none;">{{($sReqDeails->admi2)}}</td>
													<td>{{($sReqDeails->name2)}}</td>
													<td>
													<select class="form-control" id="tcurrency{{$cpt}}" >
														<option value="USD" {{ ($sReqDeails->curency2 === "USD" ? 'selected' : '') }}>USD</option> 
														<option value="LBP" {{ ($sReqDeails->curency2 === "LBP" ? 'selected' : '') }}>LBP</option> 
														<option value="EUR" {{ ($sReqDeails->curency2 === "EUR" ? 'selected' : '') }}>EUR</option>
													</select>
													</td>
													<td><input type="text" class="form-control" size="3"  id="tvalrate{{$cpt}}" value="{{$sReqDeails->prcurren2}}"  /></td>											
													<td><input type="text" class="form-control" size="3"  id="tvalamount{{$cpt}}" value="{{$sReqDeails->amount2}}" /></td>																						    
													<td><input type="button" class="btn btn-delete" id="rowdelete{{$cpt}}" value="{{__('Delete')}}" onclick="deleteRow(this)" disabled /></td>
											        </tr>
											@php
											$cpt++;
											@endphp	 
									@endforeach
								   
								   </tbody>
								</table> 
							</div>
						</div>
						
						<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
						<!-- From Section -->
						<div class="col-md-6 from-section">
							<div class="mb-1">
								<h5>From</h5>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label for="name">{{__('Total LBP')}}</label>
									<input class="text-center form-control" id="lstotal1" value="{{($ReqAccount->ltamount1)}}" disabled />
								</div>
								<div class="col-md-4">
									<label for="name">{{__('Total EUR')}}</label>
									<input class="text-center form-control" id="estotal1" value="{{($ReqAccount->etamount1)}}" disabled />
								</div>
								<div class="col-md-4">
									<label for="name">{{__('Total USD')}}</label>
									<input class="text-center form-control" id="ustotal1" value="{{($ReqAccount->dtamount1)}}" disabled />
								</div>
							</div>
						</div>

						<!-- To Section -->
						<div class="col-md-6 from-section">
							<div class="mb-1">
								<h5>To</h5>
							</div>
							<div class="row">
								<div class="col-md-4">
									<label for="name">{{__('Total LBP')}}</label>
									<input class="text-center form-control" id="lstotal2" value="{{($ReqAccount->ltamount2)}}" disabled />
								</div>
								<div class="col-md-4">
									<label for="name">{{__('Total EUR')}}</label>
									<input class="text-center form-control" id="estotal2" value="{{($ReqAccount->etamount2)}}" disabled />
								</div>
								<div class="col-md-4">
									<label for="name">{{__('Total USD')}}</label>
									<input class="text-center form-control" id="ustotal2" value="{{($ReqAccount->dtamount2)}}" disabled />
								</div>
							</div>
						</div>
					</div>
						<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1  btn btn-action" id="btnsave" name="btnsave" onClick="saveaccount()">{{__('Save')}}</button>
									 <button class="m-1  btn btn-action" id="btnmodify" name="btnmodify" onClick="modifyaccount()">{{__('Modify')}}</button>
 									 <button class="m-1 btn btn-delete" id="btndelete" name="btndelete" onClick="deleteaccounting()">{{__('Delete')}}</button>
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
function clearSelect2(){
	var supplier=$('#selectpro').val()
	$('.select2_allitems').val(null).trigger('change');
	$('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadOtherItems",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
					supplier:supplier,
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
}
</script>   
<script>
  $(document).ready(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});

 //$("#cpt").val("0");
			$("#valqty").val("1");
			$("#valqtyremise").val("1");
			$("#valprice").val("");
			$("#initprice").val("");
			$("#sel_price").val("");
			$("#valdiscount").val("");
			$("#valdiscountP").val("");
			$("#formula_id").val("0");
			$("#tbl").val("");
			$("#fvalamount").val("0.000");
			$("#tvalamount").val("0.000");
			$("#fvalrate").val("1.000");
			$("#tvalrate").val("1.000");
			$("#btnsave").prop("disabled", true);
			$("#btnadd").prop("disabled", true);
			$("#selectpatient").prop("disabled", true);
			$("#selectpro").prop("disabled", true);	
			$("#selecttype").prop("disabled", true);
			$("#invoice_date_val").prop("disabled", true);
			$("#invoice_date_due").prop("disabled", true);
			$("#invoice_ref").prop("disabled", true);
			$("#invoice_rq").prop("disabled", true);
			//$("#btnreturn").prop("disabled", true);
			//$("#selectreturn").prop("disabled", true);
			var nb=document.getElementById("cpt").value;
		//  	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var fcurency="fcurrency"+j;
			var fvalrate="fvalrate"+j;
			var fvalamount="fvalamount"+j;
			var tcurency="tcurrency"+j;
			var tvalrate="tvalrate"+j;
			var tvalamount="tvalamount"+j;
			var rdel="rowdelete"+j;
			j++;
			document.getElementById(fcurency).disabled = true;
			document.getElementById(fvalrate).disabled = true;
			document.getElementById(fvalamount).disabled = true;
			document.getElementById(tcurency).disabled = true;
			document.getElementById(tvalrate).disabled = true;
			document.getElementById(tvalamount).disabled = true;
			}
   var supplier=$('#selectpro').val();
   $('.select2_allitems').select2({
        theme: 'bootstrap4',
		width: 'resolve',
		language:'{{app()->getLocale()}}',
		placeholder: "{{__('All Code')}}",
		ajax: {
			url: '{{route("inventory.items.loadOtherItems",app()->getLocale())}}', // Replace with the actual route URL
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
				    _token: "{{ csrf_token() }}",
					item_type: 'all_item',
					supplier:supplier,
					q: params.term, // user's input
					page: params.page || 1 // current page number
				};
			},
			processResults: function (data) {
				return {
					results: data.results,
					pagination: {
						more: data.pagination.more
					}
				};
			}
			
		},
		allowClear: true
	  });
   
   
			

})

 function returnpatient(){
	  var valreturn=$("#selectreturn").val();
	 // var uu='<option value="'.vid.'">'
	if(valreturn!='0'){
    var url = "{{ route('inventory.invoices.EditRinvoices',[app()->getLocale(),'vid'])}}";
	url=url.replace('vid',valreturn);
    PopupCenter(url, '{{__("Return Fournisseur")}}','1000','500');
//     window.location.href=url; 
	 	 //window.open(url,target="_blank");

	}else{
     Swal.fire({text:'{{__("Please choose a return")}}',icon:'error',customClass:'w-auto'});
     return false;
	}
 }
 
 function insRow()
{
	
	 current_val=$("#fselectpro").val();
  if(current_val=="0" || current_val==null){
	   Swal.fire({ 
              "text":"{{__('Please choose an From Fournisseur')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
 current_val1=$("#tselectpro").val();
  if(current_val1=="0" || current_val1==null){
	   Swal.fire({ 
              "text":"{{__('Please choose an From Fournisseur')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
var q = $('#tvalamount').val(),vp=$('#fvalamount').val(),vp=$('#fvalrate').val(),vp=$('#tvalrate').val(); 
  
 // if(vp==''){
//	  vp="0.000";
//	  document.getElementById("valprice").value="0.000";
 // }
  
   
   if (!$.isNumeric(q)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Rate and Amount')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}


var tcurrency=$('#tcurrency').val();
var fcurrency=$('#fcurrency').val();	

var x=document.getElementById('myTable').insertRow(document.getElementById('myTable').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
var i= x.insertCell(8);
var j= x.insertCell(9);
var k= x.insertCell(10);
var l= x.insertCell(11)

document.getElementById("cpt").value=parseInt(document.getElementById("cpt").value)+1;

var fdescrip=$("#fselectpro option:selected").text();
var tdescrip=$("#tselectpro option:selected").text();
var fcurrencyValue=document.getElementById("fcurrency").value;
var tcurrencyValue=document.getElementById("tcurrency").value;

a.innerHTML=document.getElementById("cpt").value;
b.innerHTML=document.getElementById("fselectpro").value;
c.innerHTML=fdescrip;
d.innerHTML='<select class="form-control" id="fcurrency' + document.getElementById("cpt").value + '">' +
                '<option value="USD" ' + (fcurrencyValue === "USD" ? 'selected' : '') + '>USD</option>' +
                '<option value="LBP" ' + (fcurrencyValue === "LBP" ? 'selected' : '') + '>LBP</option>' +
                '<option value="EUR" ' + (fcurrencyValue === "EUR" ? 'selected' : '') + '>EUR</option>' +
              '</select>';
e.innerHTML='<input type="text" class="form-control" size="3"  id="fvalrate'+document.getElementById("cpt").value+'" value="'+document.getElementById("fvalrate").value+'" />';
f.innerHTML='<input type="text" class="form-control" size="3"  id="fvalamount'+document.getElementById("cpt").value+'" value="'+document.getElementById("fvalamount").value+'" />';
g.innerHTML=document.getElementById("tselectpro").value;
h.innerHTML=tdescrip;
i.innerHTML='<select class="form-control" id="tcurrency' + document.getElementById("cpt").value + '">' +
                '<option value="USD" ' + (tcurrencyValue === "USD" ? 'selected' : '') + '>USD</option>' +
                '<option value="LBP" ' + (tcurrencyValue === "LBP" ? 'selected' : '') + '>LBP</option>' +
                '<option value="EUR" ' + (tcurrencyValue === "EUR" ? 'selected' : '') + '>EUR</option>' +
              '</select>';
j.innerHTML='<input type="text" class="form-control" size="3"  id="tvalrate'+document.getElementById("cpt").value+'" value="'+document.getElementById("tvalrate").value+'" />';
k.innerHTML='<input type="text" class="form-control" size="3"  id="tvalamount'+document.getElementById("cpt").value+'" value="'+document.getElementById("tvalamount").value+'" />';
l.innerHTML='<input type="button" class="btn btn-delete" id="rowdelete'+document.getElementById("cpt").value+'" value="{{__("Delete")}}" onclick="deleteRow(this)"/>';
b.style.display = 'none';
g.style.display = 'none';

   
   cpt=document.getElementById("cpt").value;
   invoice_date_due=document.getElementById("invoice_date_due").value;
   invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_ref=document.getElementById("invoice_ref").value;
   invoice_rq=document.getElementById("invoice_rq").value;
   stype=document.getElementById("stype").value;
   selectsource=document.getElementById("selectsource").value;
 
	//$.ajax({
	//url: 'addRowAccount',
		//   type: 'get',
		 //  data:{"cpt":cpt},
		 // dataType: 'json',
		 // success: function(data){
			//  $('#htmlTable').empty();
		//	 $('#htmlTable').html(data.htmltable);
	//		 if(data.warning){
			// $('#htmlTable').empty();
		//	   Swal.fire({ 
          //    "text":data.warning,
            //  "icon":"warning",
			//  "customClass": "w-auto"});
		  
		//	} 
			// if(data.success){	
			
				
			   $("#btnsave").removeAttr('disabled');
			
		//	} 
			 
	//	 }
     // }); 

}
function deleteRow(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valqty=parseInt(document.getElementById("myTable").rows[ii].cells[2].innerHTML);
 var  valdiscount=parseFloat(document.getElementById("myTable").rows[ii].cells[4].innerHTML);
 var  valprice=parseFloat(document.getElementById("myTable").rows[ii].cells[3].innerHTML);
 var  code=parseInt(document.getElementById("myTable").rows[ii].cells[0].innerHTML);
  var  taxable=document.getElementById("myTable").rows[ii].cells[8].innerHTML;

//alert(document.getElementById("myTable").rows[ii].cells[1].innerHTML);
 var selecttax=document.getElementById("selecttax").value;
var totalf=parseFloat(document.getElementById("totalf").value);
var tdiscount=parseFloat(document.getElementById("tdiscount").value);
var invoice_date_dexpiry=document.getElementById("invoice_date_dexpiry").value;

$.ajax({
		url: 'fillInvoiceDel',
		   type: 'get',
		  dataType: 'json',
		 data:{"code":code,"selecttax":selecttax,"taxable":taxable},
		  success: function(data){
			//  alert(data);
			var taxqst=parseFloat(data.qst).toFixed(3);
			//alert(taxqst);
			var taxgst=parseFloat(data.gst).toFixed(3);
			var total=(valprice*valqty)-valdiscount;
 		    var qstf=parseFloat((total*taxqst)/100).toFixed(3);
			//alert(qstf);
	        var gstf=parseFloat((total*taxgst)/100).toFixed(3);
			document.getElementById("qst").value=parseFloat(parseFloat(document.getElementById("qst").value)-parseFloat(qstf)).toFixed(3);
			document.getElementById("gst").value=parseFloat(parseFloat(document.getElementById("gst").value)-parseFloat(gstf)).toFixed(3);
			
			 totalf=parseFloat(parseFloat(totalf)-parseFloat(total)).toFixed(3);
			 tdiscount=parseFloat(parseFloat(tdiscount)-parseFloat(valdiscount)).toFixed(3);
			 stotal=totalf+parseFloat(document.getElementById("qst").value)+parseFloat(document.getElementById("gst").value);
			  $("#stotal").val(stotal);
		  //   balance=parseFloat(parseFloat(totalf)+parseFloat(document.getElementById("gst").value)+parseFloat(document.getElementById("qst").value)).toFixed(2);
		//	 $("#balance").val(balance);
			 $("#totalf").val(totalf);
			 $("#tdiscount").val(tdiscount);
			 document.getElementById('myTable').deleteRow(ii);

		 }
      });



if(ii==1){
	
		//	$("#btnsave").prop("disabled", true);
			$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btnprintLabel").prop("disabled", true);
			$("#btnrefund").prop("disabled", true);	
			$("#btnadd").removeAttr('disabled');
}

}

function deleteaccounting(){
	var id=$('#invoice_id').val();

	  $.ajax({
            url: '{{route("DeleteAccounting",app()->getLocale())}}',
		   data: {"_token": "{{ csrf_token() }}",'id':id,state:'inactivate'},
           type: 'post',
           dataType: 'json',
           success: function(data){
           if(data.success){
			    Swal.fire({ 
                title:data.success,
			    toast:true,
                icon:"success",
			    timer:3000,
				position:"bottom-right",
			    showConfirmButton:false
			  });
			  //alert(name);
		  window.location.href="{{route('inventory.invoices.index',app()->getLocale())}}";

		       } 
			 }
       });	
	 
}


function saveaccount()
{
	
//var fprof=$('#selectpro').val();
	//	  if(fprof==''){
		//  Swal.fire({text:"{{__('The Supplier field is required')}}",icon:"error",customClass:"w-auto"});
		 // return false;
	//	 }			 
//for <td>
var x = document.getElementById("myTable").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//,"INITPRICE":document.getElementById("myTable").rows[i].cells[10].innerHTML
//alert(document.getElementById("myTable").rows[1].cells[10].innerHTML);
for(i=0;i<document.getElementById("myTable").rows.length;i++){
	var fc="fcurrency"+i;
	var tc="tcurrency"+i;
	var fr="fvalrate"+i;
	var tr="tvalrate"+i;
	var fa="fvalamount"+i;
	var ta="tvalamount"+i;
	frate="0.00";
	famount="0.00";
	fcurrency="";
	trate="0.00";
	tamount="0.00";
	tcurrency="";
if (document.getElementById(fc)!=null){

     fcurrency =document.getElementById("myTable").rows[i].cells[3].getElementsByTagName("select")[0].value;
	 

	}
if (document.getElementById(fr)!=null){

     frate =document.getElementById("myTable").rows[i].cells[4].getElementsByTagName("input")[0].value;

	}		
if (document.getElementById(fa)!=null){

     famount =document.getElementById("myTable").rows[i].cells[5].getElementsByTagName("input")[0].value;
	 

	}		
if (document.getElementById(tc)!=null){

     tcurrency =document.getElementById("myTable").rows[i].cells[8].getElementsByTagName("select")[0].value;
	 

	}		
if (document.getElementById(tr)!=null){

     trate =document.getElementById("myTable").rows[i].cells[9].getElementsByTagName("input")[0].value;
	 

	}		
if (document.getElementById(ta)!=null){

     tamount =document.getElementById("myTable").rows[i].cells[10].getElementsByTagName("input")[0].value;
	 

	}			

arr[i]={"FCODE":document.getElementById("myTable").rows[i].cells[1].innerHTML,"FNAME":document.getElementById("myTable").rows[i].cells[2].innerHTML,"FCURRENCY":fcurrency,"FRATE":frate,"FAMOUNT":famount,
        "TCODE":document.getElementById("myTable").rows[i].cells[6].innerHTML,"TNAME":document.getElementById("myTable").rows[i].cells[7].innerHTML,"TCURRENCY":tcurrency,"TRATE":trate,"TAMOUNT":tamount};	

}

var answer = confirm ("{{__('Are you sure?')}}")
   stype=document.getElementById("stype").value;
   selectsource=document.getElementById("selectsource").value;

   invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_date_due=document.getElementById("invoice_date_due").value;
   invoice_rq=document.getElementById("invoice_rq").value;
   invoice_id=document.getElementById("invoice_id").value;
    refer=document.getElementById("invoice_ref").value;
   reqID=document.getElementById("reqID").value;
    clinic_id=document.getElementById("clinic_id").value;
	
   Mastatus='N';
   if (invoice_id!='')
   {
	 Mastatus='M';   
   }
if (answer){
var myjson=JSON.stringify(arr);
 $.ajax({  
    type:'POST',	  
    data:{"_token": "{{csrf_token()}}",data:myjson,"type":stype,"selectsource":selectsource,"dateout":invoice_date_due,
		  "status":Mastatus,"invoice_id":invoice_id,"reqID":reqID,"refer":refer,
		  "datein":invoice_date_val,"clinic_id":clinic_id,"rq":invoice_rq},
    url:'{{route("SaveAccount",app()->getLocale())}}',
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
			  
			
			$("#btnadd").prop("disabled", true);
		   	 $("#btnmodify").removeAttr('disabled');
			$("#btnprint").removeAttr('disabled');
			$("#btnsave").prop("disabled", true);
			$("#selectpro").removeAttr("disabled");
			$("#btnsave").removeAttr("disabled");
			//$("#invoice_date_due").prop("disabled", true);
			//$("#invoice_date_val").prop("disabled", true);
			$("#invoice_ref").prop("disabled", true);
			$("#invoice_rq").prop("disabled", true);
			$("#stype").prop("disabled", true);
			$("#selectsource").prop("disabled", true);
			$("#tselectpro").prop("disabled", true);
			$("#fselectpro").prop("disabled", true);
			$("#fvalrate").prop("disabled", true);
			$("#fvalamount").prop("disabled", true);
			$("#fcurrency").prop("disabled", true);
			$("#tvalrate").prop("disabled", true);
			$("#tvalamount").prop("disabled", true);
			$("#tcurrency").prop("disabled", true);
			$('#invoice_date_due').prop('disabled', true); 
			document.getElementById("lstotal1").value = data.sltamount1;
			document.getElementById("estotal1").value = data.eltamount1;
			document.getElementById("ustotal1").value = data.dltamount1;
			document.getElementById("lstotal2").value = data.sltamount2;
			document.getElementById("estotal2").value = data.eltamount2;
			document.getElementById("ustotal2").value = data.dltamount2;
			var nb=document.getElementById("cpt").value;
		//  	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var fcurency="fcurrency"+j;
			var fvalrate="fvalrate"+j;
			var fvalamount="fvalamount"+j;
			var tcurency="tcurrency"+j;
			var tvalrate="tvalrate"+j;
			var tvalamount="tvalamount"+j;
			var rdel="rowdelete"+j;
			j++;
			document.getElementById(fcurency).disabled = true;
			document.getElementById(fvalrate).disabled = true;
			document.getElementById(fvalamount).disabled = true;
			document.getElementById(tcurency).disabled = true;
			document.getElementById(tvalrate).disabled = true;
			document.getElementById(tvalamount).disabled = true;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
			document.getElementById(rdel).disabled = true;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
						} 
		
    }
 });
}
else{

}
}

function modifyaccount()
{
			//$("#btnpayment").prop("disabled", true);
			$("#btnmodify").prop("disabled", true);
			//$("#btnrefund").prop("disabled", true);
			//$("#btnprint").prop("disabled", true);				
		    $("#btnadd").removeAttr('disabled');
			//$("#selecttype").removeAttr('disabled');
			$("#selectpro").removeAttr('disabled');
			$("#btnsave").removeAttr('disabled');
			$("#invoice_date_due").removeAttr('disabled');
			$("#invoice_date_val").removeAttr('disabled');
			$("#invoice_ref").removeAttr('disabled');
			$("#invoice_rq").removeAttr('disabled');
			$("#stype").removeAttr("disabled", true);
			$("#selectsource").removeAttr("disabled", true);
			$("#tselectpro").removeAttr("disabled", true);
			$("#fselectpro").removeAttr("disabled", true);
			$("#fvalrate").removeAttr("disabled", true);
			$("#fvalamount").removeAttr("disabled", true);
			$("#fcurrency").removeAttr("disabled", true);
			$("#tvalrate").removeAttr("disabled", true);
			$("#tvalamount").removeAttr("disabled", true);
			$("#tcurrency").removeAttr("disabled", true);

//			current_val=$("#selecttype").val();
		
		
			//disable button delete in table 
			var nb=document.getElementById("cpt").value;
		//  	alert(nb);
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var fcurency="fcurrency"+j;
			var fvalrate="fvalrate"+j;
			var fvalamount="fvalamount"+j;
			var tcurency="tcurrency"+j;
			var tvalrate="tvalrate"+j;
			var tvalamount="tvalamount"+j;
			var rdel="rowdelete"+j;
			j++;
			document.getElementById(fcurency).disabled = false;
			document.getElementById(fvalrate).disabled = false;
			document.getElementById(fvalamount).disabled = false;
			document.getElementById(tcurency).disabled = false;
			document.getElementById(tvalrate).disabled = false;
			document.getElementById(tvalamount).disabled = false;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
			//$("#rdel").removeAttr('disabled');
			document.getElementById(rdel).disabled = false;
          //  document.getElementById(rdel).setAttribute('disabled',false);
			}
			}
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
}

function paymentinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$("#valamount").val("0.000");
$.ajax({
	url:'{{route("GetPayInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $("#payamount").val(data.sumpay);
    $("#refamount").val(data.sumref);
	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
	
}
    });	
$('#paymentModal').modal('show');
var totalpay=parseFloat(document.getElementById("stotal").value);
var balancepay=parseFloat(document.getElementById("balance").value);
  $("#totalpay").val(totalpay);
  $("#balancepay").val(balancepay);
}

function refundinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$("#valamountrefund").val("0.000");
$.ajax({
	url:'{{route("GetRefInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $("#payamountrefund").val(data.sumpay);
    $("#refamountrefund").val(data.sumref);
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html1);
	
}
    });	
$('#refundModal').modal('show');
var totalrefund=parseFloat(document.getElementById("stotal").value);
var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totalref").val(totalrefund);
$("#balancerefund").val(balancerefund);
 $("#ref_rq").val("");

}

function remiseinvoice()
{
var invoice_id=document.getElementById("invoice_id").value;
$.ajax({
	url:'{{route("GetRemiseInventory",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","invoice_id":invoice_id},
     success: function(data){
	 $('#myTableRemise').empty();
	 $('#myTableRemise').html(data.html1);
     $("#NreqIDRemis").val(data.IdInvRemise);
	 if (data.IdInvRemise!=''){
		$("#btnaddremise").prop("disabled", true);
		$("#btnsaveremise").prop("disabled", true); 
	var nb=document.getElementById("cpts").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdeleteremise"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
			}	
		
		
	 }
	
}
    });	
$('#remiseModal').modal('show');
}

function insRowPay()
{
	 current_val=$("#selectmethod").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTablePay').insertRow(document.getElementById('myTablePay').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#selectmethod option:selected").text().trim();
d.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(3);
var deposit=document.getElementById("deposit").checked;

 if (deposit==true){tdeposit='Y';}else{tdeposit='N';}
e.innerHTML=tdeposit;
f.innerHTML=(document.getElementById("pay_rq").value);
g.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';
var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)+parseFloat(document.getElementById("valamount").value)).toFixed(3);
document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)-parseFloat(document.getElementById("valamount").value)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
	$("#btnsavepay").prop("disabled", false);	
}

function deleteRowPay(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[3].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
 var totalpay=parseFloat(parseFloat(document.getElementById("payamount").value)-parseFloat(valpay)).toFixed(3);
document.getElementById("payamount").value=totalpay;
//var balancepay=parseFloat(parseFloat(document.getElementById("balancepay").value)+parseFloat(valpay)).toFixed(2);
//document.getElementById("balancepay").value=balancepay;
if(document.getElementById('myTablePay').rows.length==1){
			$("#payamount").val("0.000");
			
			
}
$("#btnsavepay").prop("disabled", false);		
}

function insRowRef()
{
	
	 current_val=$("#selectmethodrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableRef').insertRow(document.getElementById('myTableRef').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);

document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#selectmethodrefund option:selected").text().trim();
d.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(3);
e.innerHTML=(document.getElementById("ref_rq").value);

f.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(3);
document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)+parseFloat(document.getElementById("valamountrefund").value)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
	$("#btnsaverefund").prop("disabled", false);	
  	
}
function deleteRowRef(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[3].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
 var totalref=parseFloat(parseFloat(document.getElementById("refamountrefund").value)-parseFloat(valref)).toFixed(3);
document.getElementById("refamountrefund").value=totalref;
//var balanceref=parseFloat(parseFloat(document.getElementById("balancerefund").value)-parseFloat(valref)).toFixed(2);
//document.getElementById("balancerefund").value=balanceref;
if(document.getElementById('myTableRef').rows.length==1){
			$("#refamount").val("0.000");
			
			
}
$("#btnsaverefund").prop("disabled", false);		
}


function insRowRemise()
{
	 current_val=$("#selectcoderemise").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un code",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valqtyremise").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Veuillez Entrer un Qty",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableRemise').insertRow(document.getElementById('myTableRemise').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);

document.getElementById("cpts").value=parseInt(document.getElementById("cpts").value)+1;
//var selectcode = document.getElementById("selectcode");
//var descripcode = e.options[e.selectedIndex].text;
 //var descripcode=$("#selectcode option:selected").text();
// var total=(parseInt(document.getElementById("valqty").value)*parseInt(document.getElementById("valprice").value))-parseInt(document.getElementById("valdiscount").value);
a.innerHTML=document.getElementById("cpts").value;
b.innerHTML=document.getElementById("date_remise").value;
c.innerHTML=document.getElementById("selectcoderemise").value;
d.innerHTML=$("#selectcoderemise option:selected").text().trim();
e.innerHTML=document.getElementById("valqtyremise").value;
f.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteremise'+document.getElementById("cpts").value+'" value="{{__('Delete')}}" onclick="deleteRowRemise(this)"/>';
	$("#btnsaveremise").prop("disabled", false);	
}

function deleteRowRemise(r)
{
var ii=r.parentNode.parentNode.rowIndex;

 var  valpay=parseFloat(document.getElementById("myTableRemise").rows[ii].cells[3].innerHTML);
 document.getElementById('myTableRemise').deleteRow(ii);
 
$("#btnsaveremise").prop("disabled", false);		
}

function saveremise()
{
var x = document.getElementById("myTableRemise").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTableRemise").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTableRemise").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTableRemise").rows[ii].cells[1].innerHTML,"CODE":document.getElementById("myTableRemise").rows[ii].cells[2].innerHTML,"NAME":document.getElementById("myTableRemise").rows[ii].cells[3].innerHTML,"QTY":document.getElementById("myTableRemise").rows[ii].cells[4].innerHTML};	

}
	
var invoice_id=document.getElementById("invoice_id").value;
  invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_date_due=document.getElementById("invoice_date_due").value;
     clinic_id=document.getElementById("clinic_id").value;
	  NreqIDRemis=document.getElementById("NreqIDRemis").value;
var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","NreqIDRemis":NreqIDRemis,"invoice_id":invoice_id,"clinic_id":clinic_id,"invoice_date_val":invoice_date_val,"invoice_date_due":invoice_date_due,data:myjson1},
   url: '{{route("SaveRemiseInventory",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
       
$("#btnsaveremise").prop("disabled", true);  	
$("#NreqIDRemis").val(data.NreqID);	
$("#btnaddremise").prop("disabled", true);

var nb=document.getElementById("cpts").value;
			var j=1;
			for(i=1;i<=nb;i++){
				
		    var rdel="rowdeleteremise"+j;
			//alert(rdel);
			j++;
			//alert(document.getElementById(rdel));
			if (document.getElementById(rdel)!=null){
            document.getElementById(rdel).setAttribute('disabled',false);
			}
			}	

}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
}

function savepay()
{
var x = document.getElementById("myTablePay").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTablePay").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTablePay").rows[ii].cells[2].innerHTML,"PRICE":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,"DEPOSIT":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,"RQPAY":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML};	

}
	
var clinic_id=document.getElementById("clinic_id").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var invoice_id=document.getElementById("invoice_id").value;
  var balance=document.getElementById("balancepay").value;	
 // if(selectmethod=="0"){
	//   Swal.fire({ 
      //        "text":"Veuillez choisir une méthode",
        //      "icon":"warning",
		//	  "customClass": "w-auto"});
		  
		//  return ;	
		
	//		 }	

var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"invoice_id":invoice_id,data:myjson1,"balance":balance},
   url: '{{route("SavePayInventory",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
         $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		   $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
      //   $('#paymentModal').modal('hide');	
		var b1=$("#balance").val();
		 var p1=$("#tpay").val();
$("#btnsavepay").prop("disabled", true);  	
	//	 alert(b1);
	//	 alert(p1);
	var nbalance=parseFloat(data.nbalance).toFixed(3);
	//alert(nbalance);
    $("#balance").val(nbalance);
	 $("#balancepay").val(nbalance);
}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
}

function saverefund()
{
var x = document.getElementById("myTableRef").rows[0].cells.length;
//document.getElementById("myTable").innerHTML="Found " + x + " cells in the first td element.";
var arr1 = new Array();
//for <tr>
//var x = document.getElementById("myTable").rows.length;
//document.getElementById("demo").innerHTML=document.getElementById("myTable").rows[0].cells[1].innerHTML;
//alert(document.getElementById("myTable").rows[0].cells[1].innerHTML);
for(ii=0;ii<document.getElementById("myTableRef").rows.length;ii++){
	//s = "test-"+i;
//arr[i]={"cell1":document.getElementById("myTable").rows[i].cells[0].innerHTML,"cell2":document.getElementById("myTable").rows[i].cells[1].innerHTML};	
arr1[ii]={"CODE":document.getElementById("myTableRef").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTableRef").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTableRef").rows[ii].cells[2].innerHTML,"PRICE":document.getElementById("myTableRef").rows[ii].cells[3].innerHTML,"RQREF":document.getElementById("myTableRef").rows[ii].cells[4].innerHTML};	

}
	
var clinic_id=document.getElementById("clinic_id").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var invoice_id=document.getElementById("invoice_id").value;
  var balance=document.getElementById("balancerefund").value;	
 // if(selectmethod=="0"){
	//   Swal.fire({ 
      //        "text":"Veuillez choisir une méthode",
        //      "icon":"warning",
		//	  "customClass": "w-auto"});
		  
		//  return ;	
		
	//		 }	

var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,"invoice_id":invoice_id,data:myjson1,"balance":balance},
   url: '{{route("SaveRefundInventory",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
         $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		   $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
      //   $('#paymentModal').modal('hide');	
		var b1=$("#balance").val();
		 var p1=$("#trefund").val();
$("#btnsaverefund").prop("disabled", true);  	
	//	 alert(b1);
	//	 alert(p1);
	var nbalance=parseFloat(data.nbalance).toFixed(3);
	//alert(nbalance);
$("#balance").val(nbalance);
$("#balancerefund").val(nbalance);
}
 if(data.warning){
			// $('#htmlTable').empty();
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
}

$('#selectpro').change(function(){
	clearSelect2();
 });
 
 $("#NewItemModal").on('hidden.bs.modal', function(){
  //  alert('The modal is about to be hidden.');
	  $('.select2_allitems').val(null).trigger('change');
	  $("#NewItemModal").find('#fournisseur').trigger('change.select2');
	  $("#NewItemModal").find('#brand').trigger('change.select2');
	
	
  });		

$('#choose_pdf').click(function(e){
	e.preventDefault();
	var desc = 'O';
	var print = $("input[name='pdf_type']:checked").val();
	var id = $('#pdf_id').val();
	if(print==null){
		Swal.fire({html:'{{__("Please choose at least one option")}}',icon:'error',customClass:'w-auto'});
		return false;
	}
	$.ajax({
           url: '{{route("downloadPDFInvoice",app()->getLocale())}}',
		   beforeSend: function() { 
		                 $('#choosePDFModal').modal('hide');
						 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id,'print_type':print,'desc':desc},
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
	
	
});

function downloadPDF(){	 
   var id=document.getElementById("reqID").value;	
   $('#choosePDFModal').find('#pdf_id').val(id);
   $('#choosePDFModal').find('#all_pdf').show();
   $('#choosePDFModal').modal('show');
      
	
	
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

$('#formula_id,#valprice').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#valprice').val(); 
	  current_val=$("#selectcode").val();
	if (id==1){
//		$("#sel_price").removeAttr('disabled');
		$("#sel_price").prop("readonly",false);
	}else{
		// $("#sel_price").prop("disabled", true);
		$("#sel_price").prop("readonly",true);
		$.ajax({
            url: '{{route('generation_price',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'cost_price':cost_price,'selectcode':current_val},
           success: function(data){
			      $('#sel_price').val(data.sel_price); 
			      $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
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
</script>
<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>
@endsection	
