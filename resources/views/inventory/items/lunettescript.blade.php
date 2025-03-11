<!--
 DEV APP
 Created date : 9-3-2023
 Created date : 30-3-2023 (print pdf)
 Created date : 31-3-2023 (print pdf for label)
-->
@if ($errors->has('fournisseur'))
	  <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('fournisseur')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
@elseif ($errors->has('brand'))
      <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('brand')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
@elseif ($errors->has('description'))	
       <script>
	   Swal.fire({ 
				  "html":"{{$errors->first('description')}}",
				  "icon":"error",
				  "customClass": "w-auto"});
	  </script>
@endif	  
<script>
$(function(){
	//var id=0;
	//	$.ajax({
     //      url: '{{route('generation_code',app()->getLocale())}}',
      //    type: 'get',
       //   dataType: 'json',
		//  data: {'id':id},
        //  success: function(data){
	//		  $('#brand').html(data.html); 
	//	  }
	//		});
	flatpickr('#date_recpetion', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i",
			defaultDate: ["{{Carbon\Carbon::now()->format('Y-m-d H:i')}}"]
        });
		flatpickr('#dexpiry', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });
	flatpickr('#invdate', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });
flatpickr('#capdate', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });		
flatpickr('#lastdatedep', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });		
flatpickr('#insurance', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });		
flatpickr('#lastmaintenance', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });		
flatpickr('#inexpdate', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d"
        });				
	//$("#sku").prop("readonly", true);
	//$("#barcode").prop("readonly", true);
	
	var gen_type = $('#gen_type').val();
	var id=$('#id_lunette').val(); 
  
   if (id!='0'){
	$("#Itemsform :input").prop("disabled", true);
	//$("#btnadd").prop("disabled", true);
	$("#btnModifyItems").removeAttr('disabled');
	$("#btnPrintItems").removeAttr('disabled');
	$('#btnAddItems').removeClass('disabled');
	$('#btnAddItems').removeClass('disabled');
	$('#btnCopyItems').removeAttr('disabled');
	//if(gen_type=='1'){
	$("#btnPrintItemsLabel").removeAttr('disabled');	
	//}else{
	 //$("#btnPrintItemsLabel").prop("disabled",true);
	//}
	
	 }
   
   if (id=='0'){
	$('#cost_price').val(""); 
	$('#sel_price').val(""); 
	$('#initprice').val(""); 
	$('#nbtest').val(""); 
	$('#frac').val("1"); 
	$('#qty').val("1"); 
	$('#gqty').val("0"); 
	$('#offer').val("0"); 
	$('#discount').val("0.00"); 
	$('#pricelbp').val("0.00"); 
	$('#priceusd').val("0.00"); 
	$('#deplbp').val("0.00"); 
	$('#rate').val("1.00"); 
	$('#grosslbp').val("0.00"); 
	$('#netlbp').val("0.00"); 
	$('#acclbp').val("0.00"); 
	$('#min').val("1"); 
	$('#max').val("10"); 	 
	$("#btnModifyItems").prop("disabled", true);
	$("#btnPrintItems").prop("disabled", true);
	$("#btnPrintItemsLabel").prop("disabled",true);
	$("#btnSaveItems").removeAttr('disabled');
	$('#btnAddItems').addClass('disabled');
	$("#btnadjacement").prop("disabled",true);
	$("#AddCode").prop("disabled",true);
	
	 }
	
	
	if (id>0){
		$.ajax({
           url: '{{route('collection_code',app()->getLocale())}}',
          type: 'get',
          dataType: 'json',
		  data: {'id':id},
         success: function(data){
			  $('#brand').html(data.html); 
	  }
			});
	}
	$('#fournisseur,#location_id,#main_id,#sub_id,#descriptionacct,#accumlateacct,#descriptiontype').select2( {theme: 'bootstrap4',width: 'resolve'});
    $('#brand').select2( {theme: 'bootstrap4',width: 'resolve'});
	
	$('#main_id').on('change', function() {   
	var id=$('#main_id').val(); 
	
		$.ajax({
           url: '{{route('generation_code',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'type':'main'},
           success: function(data){
		  //if (data.sequence!=""){
           $('#mainacct').val(data.main_accctnb); 
           $('#deprate').val(data.main_depreciation); 
           $('#assetlifey').val(data.main_assetlife); 
           $('#assetlifea').val(data.main_assetage); 

	
		   }
			});
	});

$('#brand1').on('change', function() {  
   var type = $('#gen_type').val();
   if(type==2){
	   var brand = $('#brand').val()
	   if(brand !="" && brand !='0' && brand!=null){
	     $('#description').val($('#brand :selected').text());
	   }
   }
});

$('#location_id').on('change', function() {  
	var id=$('#location_id').val(); 
	
		$.ajax({
           url: '{{route('generation_code',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'type':'location'},
           success: function(data){
		  //if (data.sequence!=""){
           $('#locationd').val(data.namelocation); 
         

	
		   }
			});	 
});

$('#btnadjacement').click(function(e){

$("#valamountadjacement").val("0");
$("#valamountadjacementG").val("0");
$("#valamount").val("0.00");

$('#adjacementModal').modal('show');

	});

	
$('#gen_type').on('change', function() {
	var id=$('#gen_type').val(); 
	var id_lunette=$('#id_lunette').val(); 	
		//if(id=='1' && id_lunette!='0'){
			$('#btnPrintItemsLabel').removeAttr('disabled');
		//}else{
			//$('#btnPrintItemsLabel').prop('disabled',true);
		//}
	$.ajax({
           url: '{{route('specstype',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id},
           success: function(data){
			    $('#prospec').html("");
			    $('#prospec').html(data.html);
				$('#category').html("");
				$('#category').html(data.html1);
			    
		   }
			});	
	});
	


$('#Model1').on('change', function() {
	
	$('#description').val('');
	var Brand=$('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#Model').val();
	var Color=$('#Color').val();
	var Chassis=$('#Chassis-A').val();
    var Height=$('#Height-B').val();
	var Diag=$('#Diag-ED').val();
	var Bridge=$('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	//+' '+Diag+' '+Bridge;
	$('#description').val(Ndescription);
	
	});
	
$('#Color1').on('change', function() {
	$('#description').val('');
	var Brand=$('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#Model').val();
	var Color=$('#Color').val();
	var Chassis=$('#Chassis-A').val();
    var Height=$('#Height-B').val();
	var Diag=$('#Diag-ED').val();
	var Bridge=$('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	//+' '+Diag+' '+Bridge;
	$('#description').val(Ndescription);
	
	});
$('#Height-B1').on('change', function() {
	$('#description').val('');
	var Brand=$('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#Model').val();
	var Color=$('#Color').val();
	var Chassis=$('#Chassis-A').val();
    var Height=$('#Height-B').val();
	var Diag=$('#Diag-ED').val();
	var Bridge=$('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	//+' '+Diag+' '+Bridge;
	$('#description').val(Ndescription);
	
	});
//$('#Diag-ED').on('change', function() {
	//$('#description').val('');
	//var Brand=$('select[name=brand] option:selected').text();
	//var Model=$('#Model').val();
	//var Color=$('#Color').val();
	//var Chassis=$('#Chassis-A').val();
    //var Height=$('#Height-B').val();
	//var Diag=$('#Diag-ED').val();
	//var Bridge=$('#Bridge-DBL').val();
	//var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height+' '+Diag+' '+Bridge;
	//$('#description').val(Ndescription);
	
	//});
//$('#Bridge-DBL').on('change', function() {
//	$('#description').val('');
//	var Brand=$('select[name=brand] option:selected').text();
//	var Model=$('#Model').val();
//	var Color=$('#Color').val();
//	var Chassis=$('#Chassis-A').val();
 //   var Height=$('#Height-B').val();
//	var Diag=$('#Diag-ED').val();
//	var Bridge=$('#Bridge-DBL').val();
//	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height+' '+Diag+' '+Bridge;
//	$('#description').val(Ndescription);
	
//	});

$('#Chassis-A1').on('change', function() {
	$('#description').val('');
	var Brand=$('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#Model').val();
	var Color=$('#Color').val();
	var Chassis=$('#Chassis-A').val();
    var Height=$('#Height-B').val();
	var Diag=$('#Diag-ED').val();
	var Bridge=$('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	//+' '+Diag+' '+Bridge;
	$('#description').val(Ndescription);
	
	});

	
	$('#formula_id,#cost_price').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#cost_price').val(); 
	if (id==1){
//		$("#sel_price").removeAttr('disabled');
		//$("#sel_price").prop("readonly",false);
	}else{
		
		// $("#sel_price").prop("disabled", true);
	//	$("#sel_price").prop("readonly",true);
		//$.ajax({
        //   url: '{{route('generation_price',app()->getLocale())}}',
        //   type: 'get',
        //   dataType: 'json',
		 //  data: {'id':id,'cost_price':cost_price},
         //  success: function(data){
		//	   $('#sel_price').val(data.sel_price); 
			   //  $('#sel_price1').val(data.sel_price); 
		//   }
		//	});
	}
	});

	$('#btnClearREF').click(function(e){
		e.preventDefault();
		$('#Itemsform').trigger("reset");
	});
	
	

$('#btnAddItems').click(function() {
		var selecttype = '1';
		if(selecttype==""){
		Swal.fire({text:'{{__("Please choose a type")}}',icon:'error',customClass:'w-auto'});
			return false;
		}
		var id_lunette='0';
		
		switch (selecttype) { 
		
		case '1': 	
		 $("a#btnAddItems").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'1','0'])}}");
		 break;
		case '2': 	
		 $("a#btnAddItems").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'2','0'])}}");
		 break;
		case '3':
		 $("a#btnAddItems").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'3','0'])}}");
		break;	
		case '4':
		 $("a#btnAddItems").attr("href","{{route('inventory.items.lunette',[app()->getLocale(),'4','0'])}}");
		break;	
		}
    });
 	
$('#btnCopyItems').click(function() {
	$("#Itemsform :input").prop("disabled", false);
    $("#btnModifyItems").prop("disabled", true);	
	$("#barcode").removeAttr('disabled');
	$("#sku").removeAttr('disabled');
	$("#sku").prop("readonly", true);
	$("#barcode").prop("readonly", true);
	$("#qty").prop("readonly", true);
	$("#gqty").prop("readonly", true);
	$("#sel_price").prop("readonly", false);	
		var id=$('#formula_id').val();
		var vbarcode=$('#barcode').val();
		//$('#sku').val("");
		$('#barcode').val("");
		$('#id_lunette').val("0");
		$('#id_sku').val("0");
		if (vbarcode>0){
			$("#AddCode").prop("disabled",true);
		}else{
			$("#AddCode").removeAttr('disabled');
			
		}
			if (id>1){
			$("#sel_price").prop("readonly",true);
			}
		
     var gen_type=$('#gen_type').val();
	    $("#btnPrintItemsLabel").prop('disabled',true);	
	});	


$('#btnModifyItems').click(function(e){
	if($('#sel_price').val()=="0.00"){
		$('#sel_price').val("");
	}
	if($('#cost_price').val()=="0.00"){
		$('#cost_price').val("");
	}
	if($('#initprice').val()=="0.00"){
		$('#initprice').val("");
	}
	$("#Itemsform :input").prop("disabled", false);
    $("#btnModifyItems").prop("disabled", true);	
	//$("#fournisseur").prop("disabled", true);
	//$("#barcode").removeAttr('disabled');
	//$("#sku").removeAttr('disabled');
	//$("#sku").prop("readonly", true);
	//$("#num_invoice").prop("disabled", true);
	//$("#barcode").prop("readonly", true);
	//$("#qty").prop("readonly", true);
	$("#gqty").prop("readonly", true);
	//$("#sel_price").prop("readonly", false);	
		var id=$('#formula_id').val();
		var vbarcode=$('#barcode').val();
		if (vbarcode>0){
			$("#AddCode").prop("disabled",true);
		}else{
			$("#AddCode").removeAttr('disabled');
			
		}
			if (id>1){
			//$("#sel_price").prop("disabled", true);	
			$("#sel_price").prop("readonly",true);
			}
		
     var gen_type=$('#gen_type').val();
     //if(gen_type!='1'){
	    $("#btnPrintItemsLabel").prop('disabled',true);	
	   // }
	});	
	});
 

function generate_barcode()
{
var id=$('#fournisseur').val(); 
	
		$.ajax({
           url: '{{route('generation_code',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id},
           success: function(data){
		  if (data.sequence!=""){
           	$('#sku').val(data.sequence); 
			$('#id_sku').val(data.sequence); 
			$('#barcode').val(data.sequence); 
		
			 }else{
			$('#sku').val(""); 
			$('#barcode').val(""); 
		    $("#num_invoice").removeAttr('disabled');			 
			 }
		//	  $('#brand').html(""); 
		//	  $('#brand').html(data.html); 
		   }
			});	

}


function saveadjacement()
{
	
//var answer = confirm ("Are you sure?")
  var qtyA = $('#valamountadjacement').val();
  var qtyAG = $('#valamountadjacementG').val();
		if((qtyA<=0) && (qtyAG<=0)){
		Swal.fire({text:'{{__("Please fill a quantity")}}',icon:'error',customClass:'w-auto'});
			return false;
		}
if (!$.isNumeric(qtyA)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}		
	if (!$.isNumeric(qtyAG)){
			 Swal.fire({ 
              "text":"{{__('Please enter numeric value in G.Qty')}}",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return false;		
		}			
   selecttype="5";
   adjacement_date_val=document.getElementById("adjacement_date_val").value;
    qty=document.getElementById("valamountadjacement").value;
	gqty=document.getElementById("valamountadjacementG").value;
	if (gqty>0){
		var gstock="Y";
	}
	if (qty>0){
		var gstock="N";
	}
	id=document.getElementById("item_id").value;
	descrip=document.getElementById("description").value;
   selectadj=document.getElementById("typeadjacement").value;
//if (answer){
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","gstock":gstock,"selectadj":selectadj,"selecttype":selecttype,"adjacement_date_val":adjacement_date_val,"qty":qty,"gqty":gqty,"id":id,"descrip":descrip},
    url:'{{route('SaveAdjacement',app()->getLocale())}}',
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
			  $('#adjacementModal').modal('hide');
		if (data.genstock=='N'){			  
		document.getElementById("qty").value=data.qty;
		}else{
		document.getElementById("gqty").value=data.gqty;		
		}			
				} 
		
    }
 });
//}
//else{

//}
}



 function downloadPDF(){	 
   var id=$('input[name="id_lunette"]').val();
   //alert(id);   
   if (id!='0'){
    $.ajax({
           url: '{{route("inventory.items.generate_pdf",app()->getLocale())}}',
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
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('item.pdf');
					link.click();	
			       Swal.fire({title:"{{__('Downloaded successfully')}}",toast:true,showConfirmButton:false,timer:3000,position:"bottom-right"});
					
			    });
 }
 }
 
 
function itemLBLPDF(){	 
   var id=$('input[name="id_lunette"]').val();
   //alert(id);   
   if (id!='0'){
    $.ajax({
           url: '{{route("inventory.items.item_label_pdf",app()->getLocale())}}',
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
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('item_label.pdf');
					link.click();	
			       Swal.fire({title:"{{__('Downloaded successfully')}}",toast:true,showConfirmButton:false,timer:3000,position:"bottom-right"});
					
			    });
       }
 } 
 
 $('#AddCode').on('click', function (event) {
  event.preventDefault();
 generate_barcode();
});
 
// $('#btnSaveItems').on('click',function() {
 //generate_barcode();
 
//}); 
</script>


