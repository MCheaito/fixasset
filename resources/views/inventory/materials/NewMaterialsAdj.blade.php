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
					  <div class="card-title"><h5>{{__('New Audit')}}</h5></div>
				      <div class="card-tools">
					   	<button type="button" class="m-1 float-right btn btn-resize btn-sm" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
                      </div>
                    
				</div>
				
				<div class="card-body p-0">
					<div  class="row mt-1 ml-1 mr-1"> 
						<div class="col-md-6">
					
							 <button class="btn  btn-action"  id="btnimport" onclick="impItems()">{{__('Import Items')}}</button>
							<!-- <button class="btn btn-sm btn-action"  id="btnclear" onclick="insRow()">{{__('Clear Items')}}</button> -->
												
					   </div>
					   <div class="col-md-6">
						
					    <a href="{{ route('inventory.materials.index',app()->getLocale()) }}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>
						<!--<button class="m-1 float-right btn btn-action"  id="btnprint" name="btnprint" onClick="downloadPDF()">{{__('Print')}}</button>-->

					   </div>
				    </div>
					<div  class="row ml-1 mr-1">  
							<div class="col-md-4">
								<label for="branch_name" class="label-size ">{{__('Branch')}}</label>
								<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$branch->full_name}}"/>
								<input type="hidden" id="clinic_id" name="clinic_id" value="{{$branch->id}}"/>
							</div>
							
							  <div class="col-md-3 d-none">
								 <!--<label for="reqID_val"><b>{{__('Invoice Nb')}}</b></label>-->
								 <input  type="hidden" class="form-control" name="reqID_val"  value="" id="reqID"  disabled /> 
								 <input  type="hidden" autoComplete="false"   id="invoice_id"   value=""/>
							  </div>
							  <div class="col-md-3">
								 <label for="invoice_date_val"><b>{{__('Date')}}</b></label>
								 <input type="text" class="form-control"  name="invoice_date_val" id="invoice_date_val" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}"  disabled />
							  </div>
							  <div class="col-md-5">
									<label for="name"><b>{{__('General Comment')}}</b></label>
									<div class="form-group">
									<textarea class="form-control" name="comment" id="comment" rows="1">{{old('comment')}}</textarea>
									</div> 	
							</div>					
						  </div>
						
					
					<div class="row ml-1 mr-1">
					
								<div class="col-md-4 col-12">
									<label for="selectcode"><b>{{__('Code')}}</b>
									</label>
									<select class="select2_data custom-select rounded-0" name="selectcode" id="selectcode" style="width:100%;" >
										
									</select>
									<input  type="hidden" id="typeCode"/>
									</div>	
										  <div class="col-md-2 col-6">
											<label for="name"><b>{{__('BarCode')}}</b></label>
											<div class="form-group">
											<input type="text" class="form-control" name="barcode" id="barcode"  >
											</div> 	
										 </div>	
											<div class="col-md-2 col-6">
												<div class="d-flex flex-column">
													<label for="name"><b>{{__('Stock Qty')}}</b></label>
													<input  class="form-control" value="" id="valqty" disabled />
													<input  type="hidden" id="cpt"/>
													<input  type="hidden" id="tbl"/>
												</div>
											</div>
										
					
											<div class="col-md-2 col-6">
														<label for="name"><b>{{__('Phys Qty')}}</b></label>
															<div class="form-group">
															<input type="text" class="form-control" name="jqty" id="jqty"  >
												</div> 	
											</div>
					
											<div class="d-none col-md-2 col-6">
														<label for="name"><b>{{__('Phys Qty')}}</b></label>
															<div class="form-group">
															<input type="text" class="form-control" name="rqty" id="rqty" disabled >
												</div> 	
											</div>
								
				
					
											<div class="col-md-2 col-4">
											<div class="d-flex flex-column">
													 <label for="name"><span>&#xA0;&#xA0;&#xA0;</span></label>
													 <button class="btn btn-action"  id="btnadd" onclick="updateRow()" disabled>{{__('Update')}}</button>
												</div>
											</div>
					
										   <div class="col-md-6 col-8">
										   <label for="name"><b>{{__('Last Update')}}</b></label>
											<div class="form-group">
											<input type="text" class="form-control" name="msg" id="msg" disabled >
											</div> 	
											</div>
                  	
		        </div>
				
				</div>

			</div>
        </section>			
		<section class="col-md-12">
		    <div class="card">
				<div class="card-body">
								
						<div id="htmlTable" class="row m-1">
							<div class="col-md-12">
								<table id="myTable" class="table table-striped table-bordered table-hover" style="text-align:center;width:100%;">
									<thead>
										<tr>
										    <th scope="col">{{__('#')}}</th>
											<th scope="col">{{__('BarCode')}}</th>
											<th scope="col">{{__('Item name')}}</th>
											<th scope="col">{{__('Stock Qty')}}</th>
											<th scope="col">{{__('Phys Qty')}}</th>
											<th scope="col">{{__('Diff Qty')}}</th>
											<th scope="col"></th>
											<th scope="col" style="display:none;">{{__('item id')}}</th>
										</tr>
									</thead>
								   <tbody></tbody>
								</table> 
							</div>
						</div>
					
					<div class="row m-1" style="text-align:center;background-color:#e9ecef;overflow:auto;">
							<div class="mb-1 col-md-3 col-6">
							   <label for="name">{{__('Total Stock Qty')}}</label>
							   <input  class="text-center form-control"  id="tsq"   disabled  />
							</div>
							<div class="mb-1 col-md-3 col-6">
							   <label for="name">{{__('Total Phys Qty')}}</label>
							   <input  class="text-center form-control"  id="tpq"   disabled  />
							</div>
							
						
							<div class="mb-1 col-md-3 col-6">
							   <label for="name">{{__('Total Diff Qty')}}</label>
							   <input  class="text-center form-control"  id="tdq"   disabled  />
							</div>
							<div class="mb-1 col-md-3 col-6">
							   <label for="name">{{__('Total Amount')}}</label>
							   <input  class="text-center form-control"  id="tamount"  disabled  />
							</div>
							
							
							</div>
						<!--<div class="row mt-2 m-1">
									<div class="col-md-12 text-center">
									 <button class="m-1 btn btn-action" id="btnsave" name="btnsave" onClick="approveinventory()">{{__('Approve')}}</button>
									 <button class="m-1 btn btn-delete" id="btndelete" name="btndelete" onClick="deletematerials()">{{__('Delete')}}</button>
									</div>	
									
						</div>-->					  
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
			$("#valqty").val("1");
			$("#jqty").val("1");
			$("#tbl").val("");
			$("#description").val("");
			$("#comment").val("");
			$("#msg").val("");
			$("#selectcode").prop("disabled", true);
			$("#barcode").prop("disabled", true);
			$("#jqty").prop("disabled", true);
			$("#comment").prop("disabled", true);
			$("#invoice_date_val").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			$("#btnprint").prop("disabled", true);
			$("#btndelete").prop("disabled", true);
			$("#btnclear").prop("disabled", true);
			
 var table = $('#myTable').DataTable({
             stateSave: true,
             stateDuration: -1,
			 serverSide: true,
			 paging: true, // Disable default DataTable pagination
             searching: true, // Disable search feature
			 info: true, // Disable showing information about the table
             order : [[0,'desc']],
			 scrollY: "400px",
			 scrollX: true,
			 scrollCollapse:true,
			 ajax: {
						url: "{{ route('NewMaterialsAdj',app()->getLocale()) }}",
					    data: function (d) {
						
								d.invoice_id=$('#reqID').val();
							}
					},
			 columns:[
			 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
			 {data: 'item_code'},
			 {data:'item_name'},
			 {data:'qty'},
			 {data:'rqty',render: function( data, type, full, meta){
				 var cpt = meta.row;
				 var id = 'jqty'+cpt;
				 var id_jqty ='onchange=ChangePhysQty('+cpt+',"jqty'+cpt+'")  oninput=ChangePhysQty('+cpt+',"jqty'+cpt+'")';
				 return '<input type="text" size="5" id="'+id+'" value="'+data+'" '+id_jqty+' />';
			 }},
			 {data:'dqty',render: function(data, type, full, meta){
				 var cpt = meta.row;
				 var id = 'dqty'+cpt;
				 return '<input type="text" size="5"  id="'+id+'" value="'+data+'" disabled />';
			 }},
			 {data:null,orderable:false,searchable:false,render: function(data, type, full, meta){
				var cpt = meta.row;
				var begindiv = '<div class="d-inline">';
				var btnminus = '<button class="m-1 btn btn-xs btn-icon btn-danger"  title="{{__("Minus")}}" data-type="minus" id="MinusItem'+cpt+'" style="border-radius:50%;" onclick="MinusOne('+cpt+')"><i class="fa fa-minus fa-lg text-white"></i></button>'; 
				var btnplus = '<button class="m-1  btn btn-xs btn-icon btn-success"  title="{{__("Plus")}}" data-type="plus" id="AddItem'+cpt+'" style="border-radius:50%;" onclick="AddOne('+cpt+')"><i class="fa fa-plus fa-lg text-white"></i></button>'; 
                var enddiv = '</div>';
				return begindiv+btnplus+btnminus+enddiv;
			 }},
			 {data:'item_id',visible: false}
			 
			 ],
			 language :{
					       
							search:         "{{__('Search')}}&nbsp;:",
							lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
							info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
							infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
							zeroRecords:    "{{__('No data is found')}}",
							emptyTable:     "{{__('No data is found')}}",
							buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
						
								},
							paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				         },
					
					fixedColumns:   {
				            left: 0,
				            right: 1
				        }
			});
			
$('#selectcode').on('change', function()
{
   current_val=$("#selectcode").val();
   invoice_id=$("#invoice_id").val();
   if(current_val=='0'){
	return false;
	}
	$.ajax({
		url:'{{route("fillStockNb",app()->getLocale())}}' ,
		   type: 'get',
		  dataType: 'json',
		  data:{"_token": "{{ csrf_token() }}","selectcode":current_val,"invoice_id":invoice_id},
		  success: function(data){
             $("#valqty").val(data.NbItemStock);
			 $("#rqty").val(data.rqty);
		 }
      });
});	

var barcode = document.getElementById("barcode");
barcode.addEventListener("keydown", function(event) {
    if (event.keyCode === 13) {
   current_val=$("#barcode").val();
	 invoice_id=$("#invoice_id").val();
  if(current_val=="0"){
	   Swal.fire({ 
              "text":"{{__('Please choose a barCode')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;	
		
			 }	
var q = $('#jqty').val(); 
  
    if (!$.isNumeric(q)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}
    
	
  	$.ajax({
	       url:'{{route("updateQtyRow",app()->getLocale())}}' ,
		   type: 'get',
		   data:{"code":current_val,"jqty":q,"invoice_id":invoice_id,"barcode":"Y"},
		   dataType: 'json',
		   success: function(data){
			 if(data.warning){
				   Swal.fire({ 
				  "text":data.warning,
				  "icon":"warning",
				  "customClass": "w-auto"});
			  
				} 
			 if(data.success){	
		
				$('#msg').val(data.success);
				$('#tsq').val(data.tsq);
				$('#tdq').val(data.tdq);
				$('#tpq').val(data.tpq);
				$('#tamount').val(data.tamount);
			    $('#myTable').DataTable().ajax.reload();
				
			} 
			 
		 }
      }); 
	}
});	  



  
});




function updateRow()
{
	
	 current_val=$("#selectcode").val();
	 invoice_id=$("#invoice_id").val();
  if(current_val=="0"){
	   Swal.fire({ 
              "text":"{{__('Please choose a code')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;	
		
			 }	
var q = $('#jqty').val(); 
  
    if (!$.isNumeric(q)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}

  	$.ajax({
	url:'{{route("updateQtyRow",app()->getLocale())}}' ,
		   type: 'get',
		   data:{"code":current_val,"jqty":q,"invoice_id":invoice_id},
		  dataType: 'json',
		  success: function(data){
		 if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 
			 if(data.success){	
			  Swal.fire({ 
              "text":data.success,
              "icon":"success",
			  "customClass": "w-auto"});
			$('#msg').val(data.success);
			$('#tsq').val(data.tsq);
			$('#tdq').val(data.tdq);
			$('#tpq').val(data.tpq);
			$('#tamount').val(data.tamount);
			$('#myTable').DataTable().ajax.reload();
			
			} 
			 
		 }
      }); 

}


function approveinventory()
{

var answer = confirm ("{{__('Are you sure?')}}")
  
   invoice_date_val=document.getElementById("invoice_date_val").value;
   invoice_id=document.getElementById("invoice_id").value;
   clinic_id=document.getElementById("clinic_id").value;
   comment=document.getElementById("comment").value;
   
 
if (answer){
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","clinic_id":clinic_id,
	      "invoice_id":invoice_id,"comment":comment},
    url:'{{route("ApproveInventory",app()->getLocale())}}',
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
		    $('#msg').val(data.success);
			$("#btnadd").prop("disabled", true);
		   	$("#comment").prop("disabled", true);
		   	$("#jqty").prop("disabled", true);
    		$("#btndelete").prop("disabled", true);
			$("#btnsave").prop("disabled", true);
			
			//disable button delete in table 
			$('#myTable').DataTable().ajax.reload();
			 table.rows().nodes().to$().find('.plusbtn').prop('disabled',true);
		     table.rows().nodes().to$().find('.minusbtn').prop('disabled',true);
			
			}
			} 
		
    
 });
}
else{

}
}


function deletematerials(){
	var id=$('#invoice_id').val();
Swal.fire({
  title: '{{__("Are you sure?")}}',
  html:'',
  showDenyButton: true,
  confirmButtonText: '{{__("OK")}}',
  denyButtonText: '{{__("Cancel")}}',
  customClass: 'w-auto'
}).then((result) => {
  if (result.isConfirmed) {
	  $.ajax({
            url: '{{route("DeleteMaterials",app()->getLocale())}}',
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
		  //window.location.href="{{route('inventory.materials.index',app()->getLocale())}}";
		     $('#myTable').DataTable().ajax.reload();
						
			 $("#barcode").prop('disabled',true);	
			$("#jqty").prop('disabled',true);
			$("#comment").prop('disabled',true);			
			$("#selectcode").prop('disabled',true);	
			$("#invoice_date_val").prop('disabled',true);	
			$("#btnsave").prop('disabled',true);
			$("#btnprint").prop('disabled',true);
			
			$("#comment").val("");
			$("#invoice_id").val("");
                $('#tsq').val("");
				$('#tdq').val("");
				$('#tpq').val("");
				$('#tamount').val("");			
		       } 
			 }
       });	
	  
  }else if (result.isDenied) {
    return false;
  }
})

}
 	


function downloadPDF(){	 
   var id=document.getElementById("reqID").value;	
    if(id !='' && id !='0'){
      $.ajax({
           url: '{{route("AuditPDF",app()->getLocale())}}',
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
			link.download=('Audit.pdf');
			link.click();
			
			Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});

		
			    });
	     }
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
       
		flatpickr('#invoice_date_val', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
 
function ChangePhysQty(r,rd)
{	
var table = $('#myTable').DataTable();
//var ii=r.parentNode.parentNode.rowIndex;
//var nb=document.getElementById("cpt").value;
 var invoice_id=$("#invoice_id").val();
 var code= table.row(r).data().item_code;
 var description= table.row(r).data().item_name;
 var  sqty=parseInt(table.row(r).data().qty);
 var itemid= (table.row(r).data().item_id);

 //var rdisc="discount"+rd;
 //rdisc=rd;
  //var  valdiscount=parseInt(document.getElementById(rdisc).value);
  var rqty= parseFloat($(table.row(r).node()).find('#'+rd).val());
  
  if (!$.isNumeric(jqty)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Phys Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		 $(table.row(r).node()).find('#'+rd).val('0');
		 rqty="0";
		//  return false;		
		}				 
	 
 var dqty;
 
	 dqty= rqty-sqty;
 
 //r: row index, rd: id of input physical quantity
 $(table.row(r).node()).find('#dqty'+r).val(dqty);
	$.ajax({
	url:'{{route("updateQtyByPlusMinus",app()->getLocale())}}' ,
		   type: 'get',
		   data:{"code":itemid,"description":description,"invoice_id":invoice_id,"jqty":rqty,"dqty":dqty,"type":'+2'},
		  dataType: 'json',
		  success: function(data){
			  $('#msg').val(data.success);
			  $('#tsq').val(data.tsq);
			  $('#tdq').val(data.tdq);
			  $('#tpq').val(data.tpq);
			  $('#tamount').val(data.tamount);
								 }
 });
			
}

function impItems(){
 
   invoice_date_val=document.getElementById("invoice_date_val").value;
   clinic_id=document.getElementById("clinic_id").value;
   comment=document.getElementById("comment").value;
   $("#msg").val("");
  	$.ajax({
		url: '{{route('GetItemsDetailsAdj',app()->getLocale())}}',
		beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		type: 'get',
		data: {'invoice_date_val':invoice_date_val,'comment':comment,'clinic_id':clinic_id},
		dataType: 'json',
		success: function(data){
			 window.location.href = data.loc;           
			
			 }
      });
	}
   
    
function AddOne(r)
{
 //var ii=r.parentNode.parentNode.rowIndex;
 var table = $('#myTable').DataTable();
 var invoice_id=$("#invoice_id").val();
 var code= table.row(r).data().item_code;
 var description= table.row(r).data().item_name;
 var  valnow=parseInt($(table.row(r).node()).find('#jqty'+r).val());
 var valstock= parseInt(table.row(r).data().qty);
  var itemid= table.row(r).data().item_id;

 var newval=valnow+1;
 var difval;

   difval=newval-valstock;
 
 $(table.row(r).node()).find('#jqty'+r).val(newval);
 $(table.row(r).node()).find('#dqty'+r).val(difval);
 var jqty=newval;
 var dqty=difval;
 $.ajax({
	url:'{{route("updateQtyByPlusMinus",app()->getLocale())}}' ,
		   type: 'get',
		   data:{"code":itemid,"description":description,"invoice_id":invoice_id,"jqty":jqty,"dqty":dqty,"type":'+1'},
		  dataType: 'json',
		  success: function(data){
			  $('#msg').val(data.success);
			  $('#tsq').val(data.tsq);
			$('#tdq').val(data.tdq);
			$('#tpq').val(data.tpq);
			$('#tamount').val(data.tamount);
								 }
 });
}

function MinusOne(r)
{
 //var ii=r.parentNode.parentNode.rowIndex;
 var table = $('#myTable').DataTable();
 var invoice_id=$("#invoice_id").val();
 var code= table.row(r).data().item_code;
 var description= table.row(r).data().item_name;
 var  valnow=parseInt($(table.row(r).node()).find('#jqty'+r).val());
 var valstock= parseInt(table.row(r).data().qty);
 var itemid= (table.row(r).data().item_id);
 var newval=valnow-1;
 var difval;
 
 difval=newval-valstock;
 
 $(table.row(r).node()).find('#jqty'+r).val(newval);
 $(table.row(r).node()).find('#dqty'+r).val(difval);
 var jqty=newval;
 var dqty=difval;
 $.ajax({
	url:'{{route("updateQtyByPlusMinus",app()->getLocale())}}' ,
		   type: 'get',
		   data:{"code":itemid,"description":description,"invoice_id":invoice_id,"jqty":jqty,"dqty":dqty,"type":'-1'},
		  dataType: 'json',
		  success: function(data){
			  $('#msg').val(data.success);
			  $('#tsq').val(data.tsq);
			$('#tdq').val(data.tdq);
			$('#tpq').val(data.tpq);
			$('#tamount').val(data.tamount);
								 }
 });
 }
 


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
