<!--
 DEV APP
 Created date : 12-4-2023
-->

<script>
$(function(){
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
		var id=$('#NewItemModal').find('#gen_type').val(); 
		    
			$.ajax({
				   url: '{{route('specstype',app()->getLocale())}}',
				   type: 'get',
				   dataType: 'json',
				   data: {'id':id},
				   success: function(data){
						$('#NewItemModal').find('#prospec').html("");
						$('#NewItemModal').find('#prospec').html(data.html);
						$('#NewItemModal').find('#category').html("");
						$('#NewItemModal').find('#category').html(data.html1);
						
				   }
					});	
	     
	
	$('#NewItemModal').find("#sku").prop("readonly", true);
	$('#NewItemModal').find("#barcode").prop("readonly", true);
	var id=$('#NewItemModal').find('#id_lunette').val(); 
	if (id>0){
		$.ajax({
           url: '{{route('collection_code',app()->getLocale())}}',
          type: 'get',
          dataType: 'json',
		  data: {'id':id},
         success: function(data){
			  $('#NewItemModal').find('#brand').html(data.html); 
	  }
			});
	}
	$('#NewItemModal').find('#fournisseur').select2( {theme: 'bootstrap4',width: 'resolve',dropdownParent: $('#NewItemModal')});
    $('#NewItemModal').find('#brand').select2( {theme: 'bootstrap4',width: 'resolve',dropdownParent: $('#NewItemModal')});
	
	
$('.modal').on('change', '#fournisseur', function() {
	var id=$('#NewItemModal').find('#fournisseur').val(); 
	
		$.ajax({
           url: '{{route('generation_code',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id},
           success: function(data){
		//	   if (data.sequence!=""){
         //  	$('#NewItemModal').find('#sku').val(data.sequence); 
		//	$('#NewItemModal').find('#id_sku').val(data.sequence); 
			//$('#num_invoice').val(data.sequence); 
	//		$('#NewItemModal').find('#barcode').val(data.sequence); 
	//		$('#NewItemModal').find("#barcode").removeAttr('disabled');
	//		$('#NewItemModal').find("#sku").removeAttr('disabled');
	//		$('#NewItemModal').find("#sku").prop("readonly", true);
	//		$('#NewItemModal').find("#barcode").prop("readonly", true);
	//		 }else{
	//		$('#NewItemModal').find('#sku').val(""); 
			//$('#num_invoice').val(""); 
	//		$('#NewItemModal').find('#barcode').val(""); 
		//	$("#sku").removeAttr('disabled');
	//	    $('#NewItemModal').find("#num_invoice").removeAttr('disabled');
		 //   $("#barcode").removeAttr('disabled');
				 
	//		 }
			$('#NewItemModal').find('#brand').html(""); 
			  $('#NewItemModal').find('#brand').html(data.html); 
		   }
			});
	});
	
$('.modal').on('change', '#gen_type', function() {
	var id=$('#NewItemModal').find('#gen_type').val(); 
	
	$.ajax({
           url: '{{route('specstype',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id},
           success: function(data){
			    $('#NewItemModal').find('#prospec').html("");
			    $('#NewItemModal').find('#prospec').html(data.html);
				$('#NewItemModal').find('#category').html("");
				$('#NewItemModal').find('#category').html(data.html1);
			    
		   }
			});	
	});

$('.modal').on('change', '#brand', function() {   
   var type = $('#NewItemModal').find('#gen_type').val();
   if(type==2){
	   var brand = $('#NewItemModal').find('#brand').val()
	   if(brand !="" && brand !='0' && brand!=null){
	     $('#description').val($('#NewItemModal').find('#brand :selected').text());
	   }
   }
});	


$('.modal').on('change', '#Model', function() {
	
	$('#NewItemModal').find('#description').val('');
	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#NewItemModal').find('#Model').val();
	var Color=$('#NewItemModal').find('#Color').val();
	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
    var Height=$('#NewItemModal').find('#Height-B').val();
	var Diag=$('#NewItemModal').find('#Diag-ED').val();
	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	$('#NewItemModal').find('#description').val(Ndescription);
	
	});
	
$('.modal').on('change', '#Color', function() {
	$('#NewItemModal').find('#description').val('');
	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#NewItemModal').find('#Model').val();
	var Color=$('#NewItemModal').find('#Color').val();
	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
    var Height=$('#NewItemModal').find('#Height-B').val();
	var Diag=$('#NewItemModal').find('#Diag-ED').val();
	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	$('#NewItemModal').find('#description').val(Ndescription);
	
	});
$('.modal').on('change', '#Height-B', function() {
	$('#NewItemModal').find('#description').val('');
	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#NewItemModal').find('#Model').val();
	var Color=$('#NewItemModal').find('#Color').val();
	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
    var Height=$('#NewItemModal').find('#Height-B').val();
	var Diag=$('#NewItemModal').find('#Diag-ED').val();
	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	$('#NewItemModal').find('#description').val(Ndescription);
	$('#description').val(Ndescription);
	
	});
//$('.modal').on('change', '#Diag-ED', function() {
//	$('#NewItemModal').find('#description').val('');
//	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
//	if (Brand=='undefined' || Brand=='Non défini')
//	{
//		
//		var Brand='';
//	}
//	var Model=$('#NewItemModal').find('#Model').val();
//	var Color=$('#NewItemModal').find('#Color').val();
//	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
 //   var Height=$('#NewItemModal').find('#Height-B').val();
//	var Diag=$('#NewItemModal').find('#Diag-ED').val();
//	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
//	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height+' '+Diag+' '+Bridge;
//	$('#NewItemModal').find('#description').val(Ndescription);
	
//	});
//$('.modal').on('change', '#Bridge-DBL', function() {
//	$('#NewItemModal').find('#description').val('');
//	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
//	if (Brand=='undefined' || Brand=='Non défini')
//	{
		
//		var Brand='';
//	}
//	var Model=$('#NewItemModal').find('#Model').val();
//	var Color=$('#NewItemModal').find('#Color').val();
//	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
 //   var Height=$('#NewItemModal').find('#Height-B').val();
//	var Diag=$('#NewItemModal').find('#Diag-ED').val();
//	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
//	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height+' '+Diag+' '+Bridge;
//	$('#NewItemModal').find('#description').val(Ndescription);
	
//	});

$('.modal').on('change', '#Chassis-A', function() {
	$('#NewItemModal').find('#description').val('');
	var Brand=$('#NewItemModal').find('select[name=brand] option:selected').text();
	if (Brand=='undefined' || Brand=='Non défini')
	{
		
		var Brand='';
	}
	var Model=$('#NewItemModal').find('#Model').val();
	var Color=$('#NewItemModal').find('#Color').val();
	var Chassis=$('#NewItemModal').find('#Chassis-A').val();
    var Height=$('#NewItemModal').find('#Height-B').val();
	var Diag=$('#NewItemModal').find('#Diag-ED').val();
	var Bridge=$('#NewItemModal').find('#Bridge-DBL').val();
	var Ndescription=Brand+' '+Model+' '+Color+' '+Chassis+' '+Height;
	$('#NewItemModal').find('#description').val(Ndescription);
	
	});

	
$('.modal').on('change', '#formula_id,#cost_price', function() {
	var id=$('#NewItemModal').find('#formula_id').val(); 
	var cost_price=$('#NewItemModal').find('#cost_price').val(); 
	if (id==1){
//		$("#sel_price").removeAttr('disabled');
		$('#NewItemModal').find("#sel_price").prop("readonly",false);
	}else{
		
		// $("#sel_price").prop("disabled", true);
		$('#NewItemModal').find("#sel_price").prop("readonly",true);
		$.ajax({
           url: '{{route('generation_price',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'cost_price':cost_price},
           success: function(data){
			   $('#NewItemModal').find('#sel_price').val(data.sel_price); 
			   //  $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
	});


	
$('#btnSaveItemsModal').click(function(event){
	event.preventDefault();
	//alert("Hello");
//	var supplier=$('#fournisseur').val();
	//if(supplier==""){
//	Swal.fire({text:'{{__("Please choose your supplier")}}',icon:'error',customClass:'w-auto'});
 //   return false;
//	}
	
//	var collection=$('#brand').val();
	//alert(collection);
//	if(collection==""){
//	Swal.fire({text:'{{__("Please choose your Collection")}}',icon:'error',customClass:'w-auto'});
//	return false;
//	}
	
//	var description=$('#description').val();
//	if(description==""){
//	Swal.fire({text:'{{__("Please Enter the description")}}',icon:'error',customClass:'w-auto'});
  //    return false;
//	}
	
	$('#typesave').val("M");
	var form = '#Itemsform';
	 $('#Itemsform'). attr('data-action','{{route("store_lunette",app()->getLocale())}}');
	// $(form).off().on('click', function(event){
        
        var url = $('#Itemsform').attr('data-action');
		
		$.ajax({
           url: url,
           type: 'post',
           data: $('#Itemsform').serialize(),
           dataType: 'JSON',
           success: function(data){
			    if(data.warning){
			  Swal.fire({ 
               toast:true,
			   title:data.warning,
               icon:"warning",
			   timer: 3000,
			   showConfirmButton:false,
			   position:"bottom-right"
			   });
			}
			  if(data.success){
			  Swal.fire({ 
               toast:true,
			   title:data.success,
               icon:"success",
			   timer: 3000,
			   showConfirmButton:false,
			   position:"bottom-right"
			   });
		    $('#NewItemModal').modal('hide');   
			}
		   }	
		 }); 
	// });
      });
	

 
 
}); 
 
 
</script>
