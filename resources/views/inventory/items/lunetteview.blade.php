<!--
 DEV APP
 Created date : 9-3-2023
 Created date : 30-3-2023 (print pdf)
 Created date : 31-3-2023 (print pdf for label)
-->
<div class="container-fluid">	
    <div class="row">
			<div class="card">    
				          @if(isset($modal) && $modal)
									@else
				                    <div class="card-header p-0 text-white">
										
											<div class="row">
											  <div class="col-md-10 col-8">
											   <h3>{{__('Items')}}</h3>
											   
											  </div> 
											  <div class="col-md-2 col-4">
											   <label class="m-1 label-size float-right badge bg-gradient-danger">{{'* : '.__('Mandatory')}}</label>
											  </div>	
											</div>

									 </div>
                                     @endif									
								<div class="card-body p-0">
																		
										
										<form id="Itemsform" action="{{route('store_lunette',app()->getLocale())}}"
											method="post"	onsubmit="return confirm('{{__('Do you want to save the following results?')}}');">								
											@csrf
										<div class="row m-1">
										
										
										<div class="col-md-4">
											<!--<label for="branch_name" class="label-size ">{{__('Branch')}}</label>-->
											<input type="text" class="form-control" name="branch_name" readonly="true" value="{{$FromFacility->full_name}}"/>
											<input type="hidden" id="clinic_id" name="clinic_id" value="{{$FromFacility->id}}"/>
											<label for="branch_name" class="label-size ">{{__('Asset Code')}}</label>
											<input type="text" class="form-control" id="item_id" name="item_id" value="{{isset($item)?$item->id:''}}"/>

										</div>
									
                                
										 @if(isset($modal) && $modal)
										<div class="col-md-8">
											  <input type="hidden" id="modal_value" value="{{$modal}}"/>
											  <button type="reset" class="m-1 float-right btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
											  <input type="button" id="btnSaveItemsModal" value="{{__('Save')}}"  class="m-1 float-right btn btn-action">
										</div> 
										@else
										<div class="col-md-8">
												   <a href="{{route('inventory.items.index',[app()->getLocale(),''])}}" class="m-1 float-right btn btn-back">{{__('Back')}}</a> 
												   <button type="button" id="btnPrintItems" class="m-1 float-right btn btn-action" onClick="downloadPDF()">{{__('Print')}}</button>
												   <button type="button" id="btnPrintItemsLabel" class="m-1 float-right btn btn-action" onClick="itemLBLPDF()">{{__('Print label')}}</button>
												   <a id="btnAddItems" href="" class="m-1 float-right btn btn-action">{{__('New Article')}}</a>
												   <button type="button" id="btnCopyItems" class="m-1 float-right btn btn-action" >{{__('Copy Item')}}</button>

										</div>	
									   @endif
								</div>	
											
										<div class="m-1 row">
										   <div class="col-md-3 col-6" hidden >
												<label class="label-size" style="font-size:14px;">{{__('Type')}}&#xA0;&#xA0;</label>
													<select name="gen_type" id="gen_type" class="custom-select rounded-0">
														@if(isset($modal) && $modal)
															@foreach($iType as $iTypes)
															<option value="{{$iTypes->id}}">{{__($iTypes->name)}}</option>
															@endforeach
														@else	
															@foreach($iType as $iTypes)
															<option value="{{$iTypes->id}}" {{$iTypes->id==$type ? 'selected' : ''}}>{{__($iTypes->name)}}</option>
															@endforeach
													    @endif
													</select>
												</div>
													<div class="col-md-3 col-12">
												       <label class="label-size" style="font-size:14px;">{{__('Location').' * '}}&#xA0;&#xA0;</label>
															<select name="location_id" id="location_id" class="custom-select rounded-0" style="width:100%;">
															<option value="">{{__('Undefined')}}</option>
																@foreach($location as $slocation)
																<option value="{{$slocation->id}}" {{isset($item) && $slocation->id==$item->location_id ? 'selected' : ''}}>{{$slocation->code}}</option>
																@endforeach
															</select>	
														<!--@error('fournisseur')
														<div class="alert alert-danger">{{__('The Supplier field is required')}}</div>
														@enderror -->      													
												</div>
												<div class="col-md-4 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Location Details')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="locationd" id="locationd" value="{{isset($item)?$item->locationd:''}}" disabled >												
												</div>
												
												</div>
												
												<div class="m-1 row">
														<div class="col-md-3 col-12">
												       <label class="label-size" style="font-size:14px;">{{__('Asset Main Category').' * '}}&#xA0;&#xA0;</label>
															<select name="main_id" id="main_id" class="custom-select rounded-0" style="width:100%;">
															<option value="">{{__('Undefined')}}</option>
																@foreach($assetm as $sassetm)
																<option value="{{$sassetm->id}}" {{isset($item) && $sassetm->id==$item->main_id ? 'selected' : ''}}>{{$sassetm->name}}</option>
																@endforeach
															</select>	
														<!--@error('fournisseur')
														<div class="alert alert-danger">{{__('The Supplier field is required')}}</div>
														@enderror -->      													
												</div>
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Main Account Nb')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="mainacct" id="mainacct" value="{{isset($item)?$item->mainacct:''}}" disabled>												
												</div>
												<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Dep.Rate')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="deprate" id="deprate" value="{{isset($item)?$item->deprate:''}}" disabled>												
												</div>
												<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Asset Life Year')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="assetlifey" id="assetlifey" value="{{isset($item)?$item->assetlifey:''}}" disabled>												
												</div>
												<div class="col-md-2 col-6" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Asset Age Year')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="assetlifea" id="assetlifea" value="{{isset($item)?$item->assetlifea:''}}">												
												</div>
												
												</div>
												<div class="m-1 row">
														<div class="col-md-3 col-12">
												       <label class="label-size" style="font-size:14px;">{{__('Asset Sub Category').' * '}}&#xA0;&#xA0;</label>
															<select name="sub_id" id="sub_id" class="custom-select rounded-0" style="width:100%;">
															<option value="">{{__('Undefined')}}</option>
																@foreach($subcategry as $ssubcategry)
																<option value="{{$ssubcategry->id}}" {{isset($item) && $ssubcategry->id==$item->sub_id ? 'selected' : ''}}>{{$ssubcategry->name}}</option>
																@endforeach
															</select>	
														<!--@error('fournisseur')
														<div class="alert alert-danger">{{__('The Supplier field is required')}}</div>
														@enderror -->      													
												</div>
													<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Asset Details')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="detailsasset" id="detailsasset" value="{{isset($item)?$item->detailsasset:''}}">												
												</div>
												<div class="col-md-1 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Quantity')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="qty" id="qty" value="{{isset($item)?$item->qty:old('qty')}}"  >												
												</div>
												</div>
												
											<div class="m-1 row">
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Manufacturer')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="manufacturer" id="manufacturer" value="{{isset($item)?$item->manufacturer:old('manufacturer')}}">												
												</div>
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Model/Brand')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="modele" id="modele" value="{{isset($item)?$item->modele:old('modele')}}">												
												</div> 					 												
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Serial')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="serial" id="serial" value="{{isset($item)?$item->serial:old('serial')}}">												
												</div> 
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Detailed Specification')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="specdetails" id="specdetails" value="{{isset($item)?$item->specdetails:old('specdetails')}}">												
												</div> 																
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Made Of')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="madeof" id="madeof" value="{{isset($item)?$item->madeof:old('madeof')}}">												
												</div> 					 												
												<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Color')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="dcolor" id="dcolor" value="{{isset($item)?$item->dcolor:old('dcolor')}}">												
												</div>
												</div>												
												
												<div class="m-1 row">
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Invoice#')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="num_invoice" id="num_invoice" value="{{isset($item)?$item->num_invoice:old('num_invoice')}}">												
												</div> 																
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Invoice Date')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="invdate" id="invdate" value="{{isset($item)?$item->invdate:old('invdate')}}">												
												</div> 					 												
												<div class="col-md-3 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Capitalized Date')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="capdate" id="capdate" value="{{isset($item)?$item->capdate:old('capdate')}}">												
												</div> 					 												
													 												

													<div class="col-md-3 col-12">
												       <label class="label-size" style="font-size:14px;">{{__('Supplier').' * '}}&#xA0;&#xA0;</label>
															<select name="fournisseur" id="fournisseur" class="custom-select rounded-0" style="width:100%;">
															<option value="">{{__('Undefined')}}</option>
																@foreach($Fournisseur as $s)
																<option value="{{$s->id}}" {{isset($item) && $s->id==$item->fournisseur ? 'selected' : ''}}>{{$s->name}}</option>
																@endforeach
															</select>	
														<!--@error('fournisseur')
														<div class="alert alert-danger">{{__('The Supplier field is required')}}</div>
														@enderror -->      													
												</div>
												</div>
												
											<div class="m-1 row">
												<div  class="col-md-2 col-6">
												<label class="label-size" style="font-size:14px;">{{__('Currency')}}&#xA0;&#xA0;</label>
											<select class="custom-select rounded-0" name="currency" id="currency">
													<option
														value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }} @if(isset($item)) {{$item->currency == 'USD' ? 'selected' : ''}} @endif>
															{{__('USD')}}
													</option>
													<option
														value="LBP" {{ old('currency') == 'LBP' ? 'selected' : '' }} @if(isset($item)) {{$item->currency == 'LBP' ? 'selected' : ''}} @endif>
															{{__('LBP')}}
													</option>
												
												</select>
									         </div>
											<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Price LBP')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="pricelbp" id="pricelbp" value="{{isset($item)?$item->pricelbp:old('pricelbp')}}" >												
											</div> 
											<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Price USD')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="priceusd" id="priceusd" value="{{isset($item)?$item->priceusd:old('priceusd')}}" >												
											</div> 		 						
											<div class="col-md-2 col-2" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Depreciation LBP')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="deplbp" id="deplbp" value="{{isset($item)?$item->deplbp:old('deplbp')}}" >												
											</div> 		 						
											<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Exchange Rate')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="rate" id="rate" value="{{isset($item)?$item->rate:old('rate')}}" >												
											</div> 		 						
											<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Gross Value LBP')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="grosslbp" id="grosslbp" value="{{isset($item)?$item->grosslbp:old('grosslbp')}}" >												
											</div> 		 						
											<div class="col-md-2 col-2" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Net Asset LBP')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="netlbp" id="netlbp" value="{{isset($item)?$item->netlbp:old('netlbp')}}" >												
											</div> 		 						
											<div class="col-md-2 col-2" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Accumulate Dep. LBP')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="acclbp" id="acclbp" value="{{isset($item)?$item->acclbp:old('acclbp')}}" >												
											</div>
											</div>
											
											<div class="m-1 row">											
											<div class="col-md-2 col-2" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Last Dep. Date')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="lastdatedep" id="lastdatedep" value="{{isset($item)?$item->lastdatedep:old('lastdatedep')}}" >												
											</div>
											
										<div class="col-md-3 col-12">
											<label class="label-size" style="font-size:14px;">
												{{ __('Depreciation Acct.') }} <span style="color:red;">*</span>&#xA0;&#xA0;
											</label>
											<select name="descriptionacct" id="descriptionacct" class="custom-select rounded-0" style="width:100%;">
												@foreach($Depreciation as $sda)
													<option value="{{ $sda->id }}" {{ isset($item) && $sda->id == $item->descriptionacct ? 'selected' : '' }}>
														{{ $sda->num_compte }}
													</option>
												@endforeach
											</select>  
										</div>

										<div class="col-md-3 col-12">
											<label class="label-size" style="font-size:14px;">
												{{ __('Accumulated Acct.') }} <span style="color:red;">*</span>&#xA0;&#xA0;
											</label>
											<select name="accumlateacct" id="accumlateacct" class="custom-select rounded-0" style="width:100%;">
												<option value="">{{ __('Undefined') }}</option>
												@foreach($Fixed as $saa)
													<option value="{{ $saa->id }}" {{ isset($item) && $saa->id == $item->accumlateacct ? 'selected' : '' }}>
														{{ $saa->num_compte }}
													</option>
												@endforeach
											</select>  
										</div>
										<div class="col-md-3 col-12">
											<label class="label-size" style="font-size:14px;">
												{{ __('Depreciation Type') }} <span style="color:red;">*</span>&#xA0;&#xA0;
											</label>
											<select name="descriptiontype" id="descriptiontype" class="custom-select rounded-0" style="width:100%;">
												@foreach($DepreciationType as $taa)
													<option value="{{ $taa->id }}" {{ isset($item) && $taa->id == $item->descriptiontype ? 'selected' : '' }}>
														{{ $taa->name }}
													</option>
												@endforeach
											</select>  
										</div>
										</div>
										
										<div class="m-1 row">											
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Status')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="status1" id="status1" value="{{isset($item)?$item->status:old('status')}}" >												
											</div>
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Insurance')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="insurance" id="insurance" value="{{isset($item)?$item->insurance:old('insurance')}}" >												
											</div>
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Last Maintenance')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="lastmaintenance" id="lastmaintenance" value="{{isset($item)?$item->lastmaintenance:old('lastmaintenance')}}" >												
											</div>
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Insurance Exp.Date')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="inexpdate" id="inexpdate" value="{{isset($item)?$item->inexpdate:old('inexpdate')}}" >												
											</div>
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Software Licence')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="softlicence" id="softlicence" value="{{isset($item)?$item->softlicence:old('softlicence')}}" >												
											</div>
											</div>
											
										<div class="m-1 row" >
										<div class="col-md-6 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Remark')}}&#xA0;&#xA0;</label>
															<textarea class="form-control" name="remark" id="remark" rows="4">{{ isset($item) ? $item->remark : old('remark') }}</textarea>
									
											</div>
										<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('User')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="username1" id="username1" value="{{isset($item)?$item->username:old('username')}}" >												
											</div>											
										</div> 	
											<div class="m-1 row" hidden>
												<div  class="col-md-3 col-6" hidden >
													<label class="label-size" style="font-size:14px;">{{__('Category')}}&#xA0;&#xA0;</label>
													<div id="Category">
														<select name="category" id="category" class="custom-select rounded-0">
														@foreach($iCategory as $iCategorys)
															<option value="{{$iCategorys->id}}" {{isset($item) && $iCategorys->id==$item->category ? 'selected' : ''}}>{{$iCategorys->name}}</option>
														    
														@endforeach
														</select>
													</div>	
													</div>
												
										     <div class="col-md-3 col-12" hidden >
												<label class="label-size" style="font-size:14px;">{{__('Analyzer')}}&#xA0;&#xA0;</label>
													<select name="brand" id="brand" class="custom-select rounded-0" style="width:100%;">
													<option value="">{{__('Undefined')}}</option>	
														
																@foreach($collection as $brands)
																<option value="{{$brands->id}}" {{isset($item) && $brands->id==$item->brand ? 'selected' : ''}}>{{$brands->name}}</option>
																@endforeach
													</select>
												<!--@error('brand')
                                                <div class="alert alert-danger">{{__('The Collection field is required')}}</div>
                                                @enderror -->      														
												</div>
											
												<div class="col-md-3 col-6" hidden>
														<label class="label-size" style="font-size:14px;">{{__('Reference')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="sku" id="sku" value="{{isset($item)?$item->sku:''}}">												
												</div> 
												<div class="col-md-3 col-6" hidden>
														<label class="label-size" style="font-size:14px;">{{__('BarCode')}}&#xA0;&#xA0;
													<!--	<span><button class="mr-4 float-right btn btn-xs btn-action"  id="AddCode" >{{__('Generate Barcode')}}</button></span> -->

														</label>
														<input type="text" class="form-control" name="barcode" id="barcode" value="{{isset($item)?$item->barcode:''}}" >												
											   </div> 		
											<div  class="col-md-3 col-6" hidden>
												<label class="label-size" style="font-size:14px;">{{__('Type')}}&#xA0;&#xA0;</label>
											<select class="custom-select rounded-0" name="materiel" id="materiel">
													<option value="">{{__('Select Type')}}</option>
													<option
														value="1" {{ old('materiel') == '1' ? 'selected' : '' }} @if(isset($item)) {{$item->materiel == '1' ? 'selected' : ''}} @endif>
															{{__('Reagent')}}
													</option>
													<option
														value="2" {{ old('materiel') == '2' ? 'selected' : '' }} @if(isset($item)) {{$item->materiel == '2' ? 'selected' : ''}} @endif>
															{{__('QC')}}
													</option>
													<option
														value="3" {{ old('materiel') == '3' ? 'selected' : '' }} @if(isset($item)) {{$item->materiel == '3' ? 'selected' : ''}} @endif>
															{{__('Cal')}}
													</option>
													<option
														value="4" {{ old('materiel') == '4' ? 'selected' : '' }} @if(isset($item)) {{$item->materiel == '4' ? 'selected' : ''}} @endif>
															{{__('Cons')}}
													</option>
													
												</select>
									</div>	
												
												</div>
												<div class="m-1 row" hidden>
												<div class="col-md-3 col-6" hidden>
														<label class="label-size" style="font-size:14px;">{{__('Facture')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="num_facture" id="num_facture" value="{{isset($item)?$item->num_facture:old('num_facture')}}">												
												</div> 
												<div class="col-md-3 col-6" hidden>
														<label class="label-size" style="font-size:14px;">{{__('Date Reception')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="date_recpetion" id="date_recpetion" value="{{isset($item)?$item->date_recpetion:old('date_recpetion')}}">												
												</div>
												<div class="col-md-6" hidden>
														<label class="label-size" style="font-size:14px;">{{__('Description').' * '}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="description" id="description" value="{{isset($item)?$item->description:old('description')}}" >												
															<!--@error('description')
															<div class="alert alert-danger">{{__('The Description field is required')}}</div>
															@enderror-->     
												</div> 	
												
											</div> 	
											
										<div id="prospec" class="row m-1" hidden>
										  @if(isset($modal) && $modal)
										  @else	  
											@php 
											 $all_lunette_type= isset($item->items_specs)?json_decode($item->items_specs ,true):[];  
											 if(!empty( $all_lunette_type)){
											   $all_lunette_type = array_map('strval', $all_lunette_type);
											 }
											 $count=0;
											@endphp
												  @foreach($lunette_specs as $t) 
													  
														 <div class="mb-1 col-md-2 col-5">
														   <label class="label-size">{{(app()->getLocale()=='en')?$t->english:$t->french}}</label>
															<input type="text" id="{{$t->english}}" name="lunette_type[]" class="form-control" value="{{(!empty($all_lunette_type) && isset($all_lunette_type[$count]))? $all_lunette_type[$count] : '' }} "/>
													       </div>
													  @php $count++; @endphp
												   @endforeach
											@endif	   
									    </div>
										<div class="row m-1" hidden >
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Nb of Test')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="nbtest" id="nbtest" value="{{isset($item)?$item->nbtest:old('nbtest')}}" >												
											</div> 		
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Nb in Box')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="frac" id="frac" value="{{isset($item)?$item->frac:old('frac')}}" >												
											</div> 	
											
											 <div  class="col-md-2 col-6">
												<label class="label-size" style="font-size:14px;">{{__('Used')}}&#xA0;&#xA0;</label>
											<select class="custom-select rounded-0" name="used" id="used">
													<option
														value="O" {{ old('used') == 'O' ? 'selected' : '' }} @if(isset($item)) {{$item->used == 'O' ? 'selected' : ''}} @endif>
															{{__('Used')}}
													</option>
													<option
														value="N" {{ old('used') == 'N' ? 'selected' : '' }} @if(isset($item)) {{$item->used == 'N' ? 'selected' : ''}} @endif>
															{{__('Not Used')}}
													</option>
													
												</select>
									         </div>	
												<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Offer')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="offre" id="offre" value="{{isset($item)?$item->offre:old('offre')}}" >												
											</div> 		 
											<div class="col-md-2 col-2">
														<label class="label-size" style="font-size:14px;">{{__('Discount')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="discount" id="discount" value="{{isset($item)?$item->discount:old('discount')}}" >												
											</div> 		 
										 </div>
										<div class="row m-1" hidden >
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Initial Price')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="initprice" id="initprice" value="{{isset($item)?$item->initprice:old('initprice')}}" >												
											</div> 		
										<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Cost Price')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="cost_price" id="cost_price" value="{{isset($item)?$item->cost_price:old('cost_price')}}" >												
											</div> 		
											
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Formula')}}&#xA0;&#xA0;</label>
														<select name="formula_id" id="formula_id" class="custom-select rounded-0">
													       <option value="0">{{__('Undefined')}}</option>
															@foreach($Formula as $f)
															<option value="{{$f->id}}" {{isset($item) && $f->id==$item->formula_id ? 'selected' : ($f->id==1 ? 'selected' : '')}}>{{$f->name}}</option>
															@endforeach
													   </select>																			
											</div> 
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Sel Price')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="sel_price" id="sel_price" value="{{isset($item)?$item->sel_price:old('sel_price')}}" >
															<input type="hidden" class="form-control" name="sel_price1" id="sel_price1" value="" >																							
											</div>
											<div  class="col-md-2 col-6">
												<label class="label-size" style="font-size:14px;">{{__('Bill').' +/-'}}&#xA0;&#xA0;</label>
											<select class="custom-select rounded-0" name="typecode" id="typecode">
													<option
														value="1" {{ old('typecode') == '1' ? 'selected' : '' }} @if(isset($item)) {{$item->typecode == '1' ? 'selected' : ''}} @endif>
															{{__('Normal').' +'}}
													</option>
													<option
														value="2" {{ old('typecode') == '2' ? 'selected' : '' }} @if(isset($item)) {{$item->typecode == '2' ? 'selected' : ''}} @endif>
															{{__('Discount').' -'}}
													</option>
													
												</select>
									         </div>	
											<div class="col-md-1 col-6">
											<label class="label-size" style="font-size:14px;">{{__("Tax")}}</label>
											<div class="m-1 form-group">											
											 <input id="taxable" type="checkbox"  @if(isset($item)){{($item->taxable=='Y')?'checked':''}} @endif style="height:22px;width:22px;" name="taxable"/>
											  
											</div> 	
											</div> 	
											</div> 	
											<div class="row m-1" hidden >	
										
										<div class="col-md-1 col-6" hidden >
														<label class="label-size" style="font-size:14px;">{{__('Qty G.Stock')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="gqty" id="gqty" value="{{isset($item)?$item->gqty:old('gqty')}}" disabled >												
											</div> 														
											<div class="col-md-1 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Min')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="min" id="min" value="{{isset($item)?$item->min:old('min')}}" >												
											</div> 
											<div class="col-md-1 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Max')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="max" id="max" value="{{isset($item)?$item->max:old('max')}}" >												
											</div>
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Garanty')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="garanty" id="garanty" value="{{isset($item)?$item->garanty:old('garanty')}}" >												
											</div> 
											
											<div class="col-md-4">
												<label class="label-size" for="notes" style="font-size:14px;">{{__("Notes")}}</label>
												<textarea name="notes" id="notes"  class="form-control" rows="1">{{isset($item)?$item->notes:old('notes')}}</textarea>
											</div>
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Lot Nb')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="nblot" id="nblot" value="{{isset($item)?$item->nblot:old('nblot')}}" >												
											</div> 
											<div class="col-md-2 col-6">
														<label class="label-size" style="font-size:14px;">{{__('Expiray Date')}}&#xA0;&#xA0;</label>
															<input type="text" class="form-control" name="dexpiry" id="dexpiry" value="{{isset($item)?$item->dexpiry:old('dexpiry')}}" >												
											</div> 
											 <input type="hidden" name="id_lunette" id="id_lunette" value="{{$lunette_id}}"/>
										   <input type="hidden" name="id_sku" id="id_sku" value=""/>
										   <input type="hidden" name="typesave" id="typesave" value="F"/>
										
										</div>
										 
									 @if(isset($modal) && $modal)
										 
									 @else	
									 <div class="row m-1">
															
									   <div class="text-center col-md-12">
										 
										  
										   <input type="submit" id="btnSaveItems" value="{{__('Save') }}"  class="m-1 btn btn-action">
										   <input type="button" id="btnModifyItems" value="{{__('Modify')}}"  class="m-1 btn btn-action">
										   <input type="button" id="btnadjacement" value="{{__('Adjustment')}}"  class="m-1 btn btn-action" hidden>
								           
										 
									   </div>
									   
									</div>
									@endif
								  </form> 
				</div>
				</div>
			</div>
			
<!--Adjacement modal-->
			<div class="modal fade" id="adjacementModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
							  <!-- Modal content-->
					<div class="modal-content">
							<div class="p-0 modal-header txt-bg text-white">
								<h4 class="modal-title">{{__('Adjustment')}}</h4>
								<button type="button" class="float-right btn btn-action" data-dismiss="modal"><i class="fa fa-times"></i></button>
							</div>
							<div class="p-0 modal-body">
								    <div class="container">
						                 
											
											<div class="row m-1"> 
						<div class="col-md-3">
							 <label for="adjacement_date_val"><b>{{__('Date')}}</b></label>
							 <input type="text" class="form-control"  name="adjacement_date_val" id="adjacement_date_val" value="{{Carbon\Carbon::now()->format('Y-m-d H:i')}}" />
						</div>											
											  <div  class="col-md-2 col-6">
												<label class="label-size" style="font-size:14px;">{{__('Bill').' +/-'}}&#xA0;&#xA0;</label>
											<select class="custom-select rounded-0" name="typeadjacement" id="typeadjacement">
													<option
														value="1" {{ old('typeadjacement') == '1' ? 'selected' : '' }} >
															{{__('Add +')}}
													</option>
													<option
														value="2" {{ old('typeadjacement') == '2' ? 'selected' : '' }} >
															{{__('Minus -')}}
													</option>
													
												</select>
									         </div>	
											
						  				   <div class="col-md-3">
												<label class="label-size" for="name">{{__('Qty')}}</label>
								                <input  class="form-control"  value="0" id="valamountadjacement" /> 
										   </div>
										  <div class="col-md-3" hidden >
												<label class="label-size" for="name">{{__('Qty G.Stock')}}</label>
								                <input  class="form-control"  value="0" id="valamountadjacementG" /> 
										   </div>				
										
										</div> 
										</div>
										</div>
										
														
							<div class="modal-footer justify-content-center">
							    <button class="btn btn-action" id="btnsaveadjacement" name="btnsaveadjacement" onClick="saveadjacement()">{{__('Save')}}</button>
								<button type="reset" class="btn btn-delete" data-dismiss="modal">{{__('Close')}}</button>
							</div>
				
		    </div>
			</div>
			</div>			
			<!--end AdjacementModal-->	  	  					
			
			
			
</div><!--end card ref-->	
