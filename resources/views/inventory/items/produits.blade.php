@extends('gui.main_gui')
@section('content')	
<div class="container">	
    <div class="row mt-2">
			<div class="card">    
				
				             <div class="card-header text-white">
										
											<div class="row">
											  <div class="col-md-4">
											   <h3>{{__('Items')}}</h3>
											  </div> 
											 
																	
										</div>

									</div>			
						<div class="card-body">
										<form id="Itemsform" action="{{route('store_lunette',app()->getLocale())}}"
											method="post"	onsubmit="return confirm('{{__('Do you want to save the following results?')}}');">								
											@csrf			
										<div class="m-1 row">
										
										<div class="col-md-4">
												<label class="label-size" style="font-size:16px;">{{__('Type')}}&#xA0;&#xA0;</label>
												<div class="form-group">
													<select name="gen_type" id="gen_type" class="custom-select rounded-0">
														
														<option value="{{$iType->id}}" >{{$iType->name}}</option>
													</select>
													</div> 	
												</div>
										
											<div class="col-md-4">
												<label class="label-size" style="font-size:16px;">{{__('Category')}}&#xA0;&#xA0;</label>
												<div class="form-group">
													<select name="category" id="category" class="custom-select rounded-0">
														<option value="0" {{isset($item) && $item->category=='0' ? 'selected' : ''}}>{{'N/D'}}</option>
														<option value="1" {{isset($item) && $item->category=='1' ? 'selected' : ''}}>{{'Regural'}}</option>
														<option value="2" {{isset($item) && $item->category=='2' ? 'selected' : ''}}>{{'Security'}}</option>
														<option value="3" {{isset($item) && $item->category=='3' ? 'selected' : ''}}>{{'Clip'}}</option>
														<option value="4" {{isset($item) && $item->category=='4' ? 'selected' : ''}}>{{'Old Fashioned'}}</option>
													</select>
													</div> 	
												</div>
													<div class="col-md-4">
												<label class="label-size" style="font-size:16px;">{{__('Fournisseur')}}&#xA0;&#xA0;</label>
												<div class="form-group">
													<select name="fournisseur" id="fournisseur" class="custom-select rounded-0">
													<option value="0">{{__('N/D')}}</option>
														@foreach($Fournisseur as $s)
														<option value="{{$s->id}}" {{isset($item) && $s->id==$item->fournisseur ? 'selected' : ''}}>{{$s->name}}</option>
														@endforeach
													</select>												
													</div> 	
												</div>
										
										</div> 	
											<div class="m-1 row">
												
												<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Code Invoice')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="num_invoice" id="num_invoice" value="{{isset($item)?$item->num_invoice:old('num_invoice')}}">												
												</div> 	
												</div> 
												<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Sku')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="sku" id="sku" value="{{isset($item)?$item->sku:old('sku')}}">												
												</div> 	
												</div> 
												<div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('BarCode')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="barcode" id="barcode" value="{{isset($item)?$item->barcode:old('barcode')}}" >												
												</div> 	
											</div> 		
												
												
												</div>
												<div class="m-1 row">
												<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Facture')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="num_facture" id="num_facture" value="{{isset($item)?$item->num_facture:old('num_facture')}}">												
												</div> 	
												</div> 
												<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Date Reception')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="date_recpetion" id="date_recpetion" value="{{isset($item)?$item->date_recpetion:old('date_recpetion')}}">												
												</div> 	
												</div> 
												
											</div> 	
											
										<div class="row m-1">
									@php 
									     $all_lunette_type= isset($item->items_specs)?json_decode($item->items_specs ,true):[];  
									     //echo json_encode(isset($item->items_specs)?json_decode($item->items_specs ,true):[] ,true); 
									     $all_lunette_type = array_map('strval', $all_lunette_type);
										 $count=0;
									@endphp
											  @foreach($lunette_specs as $t) 
											   <div class="ml-1 col-md-1">
											   <label class="label-size" style="font-size:0.8em;">{{(app()->getLocale()=='en')?$t->english:$t->french}}&#xA0;&#xA0;</label>
												<div class="form-group">
												<input type="text" name="lunette_type[]" class="form-control" value="{{(!empty($all_lunette_type))? $all_lunette_type[$count] : '' }} "/>
												</div> 	
											</div> 	
											   @php $count++; @endphp
										
											   @endforeach
									  
									</div>
									<div class="row m-1">
									
											<div class="col-md-3">
												<label class="label-size" style="font-size:16px;">{{__('Collection')}}&#xA0;&#xA0;</label>
												<div class="form-group">
													<select name="brand" id="brand" class="custom-select rounded-0">
												
													</select>												
													</div> 	
												</div>
												<!--
												<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Line')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="line" id="line" value="{{isset($item)?$item->line:old('line')}}" >												
												</div> 	
												</div> -->	
										<div class="col-md-9">
														<label class="label-size" style="font-size:16px;">{{__('Description')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="description" id="description" value="{{isset($item)?$item->description:old('description')}}" >												
												</div> 	
											</div> 	
										
										</div>
										<div class="row m-1">
									
										<div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Cost Price')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="cost_price" id="cost_price" value="{{isset($item)?$item->cost_price:old('cost_price')}}" >												
												</div> 	
											</div> 		
											
											<div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Formula')}}&#xA0;&#xA0;</label>
															<div class="form-group">
																<select name="formula_id" id="formula_id" class="custom-select rounded-0">
													<option value="0">{{__('N/D')}}</option>
														@foreach($Formula as $f)
														<option value="{{$f->id}}" {{isset($item) && $f->id==$item->formula_id ? 'selected' : ''}}>{{$f->name}}</option>
														@endforeach
													</select>																			
												</div> 	
											</div> 
											<div class="col-md-3">
														<label class="label-size" style="font-size:16px;">{{__('Sel Price')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="sel_price" id="sel_price" value="{{isset($item)?$item->sel_price:old('sel_price')}}" disabled>
															<input type="hidden" class="form-control" name="sel_price1" id="sel_price1" value="" >																							
												</div> 	
											</div>
											<div class="col-md-3">
											<label style="font-size:16px;"></label>
											<div class="form-group">											
											 <input type="checkbox"  @if(isset($item)){{($item->taxable=='Y')?'checked':''}} @else {{ old('taxable') == 'N' ? '' : 'checked'}} @endif style="height:22px;width:22px;" name="taxable"/>
											  <span style="font-size:16px;">{{__("Tax")}}</span>
											</div> 	
											</div> 	
											</div> 	
											<div class="row m-1">	
											<div class="col-md-2">
														<label class="label-size" style="font-size:16px;">{{__('Quantity')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="qty" id="qty" value="{{isset($item)?$item->qty:old('qty')}}" >												
												</div> 	
											</div> 						
											<div class="col-md-2">
														<label class="label-size" style="font-size:16px;">{{__('Min')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="min" id="min" value="{{isset($item)?$item->min:old('min')}}" >												
												</div> 	
											</div> 
											<div class="col-md-2">
														<label class="label-size" style="font-size:16px;">{{__('Max')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="max" id="max" value="{{isset($item)?$item->max:old('max')}}" >												
												</div> 	
											</div> 
											<div class="col-md-4">
														<label class="label-size" style="font-size:16px;">{{__('Garanty')}}&#xA0;&#xA0;</label>
															<div class="form-group">
															<input type="text" class="form-control" name="garanty" id="garanty" value="{{isset($item)?$item->garanty:old('garanty')}}" >												
												</div> 	
											</div> 																	
										</div>
										  <input type="hidden" name="id_lunette" id="id_lunette" value="{{$lunette_id}}"/>
										   <input type="hidden" name="id_sku" id="id_sku" value=""/>
										<div class="row m-1">
											<div class="form-group col-md-9">
												<label class="label-size" for="notes">{{__("Notes")}}</label>
												<textarea name="notes" id="notes"  class="form-control"  rows="3">{{isset($item)?$item->notes:old('notes')}}</textarea>
											</div>
										</div>
									 <div class="row m-1">
															
									   <div class="text-center col-md-12">
										  <input type="submit" id="btnSaveItems" value="{{__('Save') }}"  class="btn btn-action">
										   <input type="button" id="btnModifyItems" value="{{__('Modify')}}"  class="btn btn-action">
										 <button type="button" id="btnPrintItems" class="m-1 btn btn-action  text-center" onClick="downloadPDF()">{{__('Print')}}</button>
										 <a href="{{route('inventory.items.index',[app()->getLocale(),''])}}" class="mr-2 ml-2 btn btn-back">{{__('Close')}}</a> 
									   </div>
									   
									</div>
								  </form> 
				</div>
				</div>
			</div>
</div><!--end card ref-->	
@endsection	
@section('scripts')
<script>
$(function(){
	var id=0;
		$.ajax({
           url: '{{route('generation_code',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id},
           success: function(data){
			  $('#brand').html(data.html); 
		   }
			});
	$('#fournisseur').select2( {theme: 'bootstrap4',width: 'element'});
    $('#brand').select2( {theme: 'bootstrap4',width: 'element'});
	$('#fournisseur').on('change', function() {
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
			//$('#num_invoice').val(data.sequence); 
			$('#barcode').val(data.sequence); 
			$("#sku").prop("disabled", true);
			//$("#num_invoice").prop("disabled", true);
		    $("#barcode").prop("disabled", true);
			 }else{
			$('#sku').val(""); 
			//$('#num_invoice').val(""); 
			$('#barcode').val(""); 
		//	$("#sku").removeAttr('disabled');
		    $("#num_invoice").removeAttr('disabled');
		 //   $("#barcode").removeAttr('disabled');
				 
			 }
			  $('#brand').html(data.html); 
		   }
			});
	});

	$('#formula_id').on('change', function() {
	var id=$('#formula_id').val(); 
	var cost_price=$('#cost_price').val(); 
	if (id==1){
		$("#sel_price").removeAttr('disabled');
	}else{
		 $("#sel_price").prop("disabled", true);
		$.ajax({
           url: '{{route('generation_price',app()->getLocale())}}',
           type: 'get',
           dataType: 'json',
		   data: {'id':id,'cost_price':cost_price},
           success: function(data){
			   $('#sel_price').val(data.sel_price); 
			     $('#sel_price1').val(data.sel_price); 
		   }
			});
	}
	});

	$('#btnClearREF').click(function(e){
		e.preventDefault();
		$('#Itemsform').trigger("reset");
	});	
	 var id=$('#id_lunette').val(); 
	 if (id!='0'){
	$("#Itemsform :input").prop("disabled", true);
	//$("#btnadd").prop("disabled", true);
	$("#btnModifyItems").removeAttr('disabled');
	$("#btnPrintItems").removeAttr('disabled');
	 }else{
	$('#cost_price').val("0.00"); 
	$('#sel_price').val("0.00"); 
	$('#qty').val("1"); 
	$('#min').val("1"); 
	$('#max').val("10"); 	 
	$("#btnModifyItems").prop("disabled", true);
	$("#btnPrintItems").prop("disabled", true);
	$("#btnSaveItems").removeAttr('disabled');
		 
	 }
	  
});




$('#btnModifyItems').click(function(e){
	$("#Itemsform :input").prop("disabled", false);
    $("#btnModifyItems").prop("disabled", true);	
	//$("#fournisseur").prop("disabled", true);
	$("#sku").prop("disabled", true);
	//$("#num_invoice").prop("disabled", true);
	$("#barcode").prop("disabled", true);
		var id=$('#formula_id').val();
			if (id>1){
			$("#sel_price").prop("disabled", true);	
			}
			
	});	
	
	
$('#btnSaveItemss').click(function(){
	var form = '#Itemsform';
	 $('#Itemsform'). attr('data-action','{{route('store_lunette',app()->getLocale())}}');
	 $(form).off().on('submit', function(event){
        event.preventDefault();
        var url = $(this).attr('data-action');
		alert($('#sku').val());
		$.ajax({
           url: url,
           type: 'post',
           data: $('#Itemsform').serialize(),
           dataType: 'JSON',
           success: function(data){
            if(data.warn){
				alert(data.warn);
			}else{
				if(data.success){
				 $('#sku').val(data.id); 
				 $('#num_invoice').val(""); 
				 $('#barcode').val(""); 
				   
				   }
			}
		   }	
		 }); 
	 });
      });
	  
 function downloadPDF(){	 
   var id=document.getElementById("id_lunette").value;	
   if (id!='0'){
    $.ajax({
           url: '{{route("downloadPDFRx",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
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
			window.location.href="{{route('emr.visit.index',app()->getLocale())}}";
					
			    });
 }
 }
 
  
 
 
</script>

<script>
        flatpickr('#date_recpetion', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i",
			defaultDate: ["{{Carbon\Carbon::now()->format('Y-m-d H:i')}}"]
        });
	
    </script>
@endsection