<!--
 DEV APP
 Created date : 30-3-2023
-->
<!DOCTYPE html>
<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
			<title>{{__('Items')}}</title>
			<style>
			
			table {
                  border-spacing: 0px;
                  table-layout: auto;
                  margin-left: auto;
                  margin-right: auto;
                  width: 100%;
			      font-size: 13px ! important;
			   }
			table th,table td{
				word-break: break-all;
				}
			
            #tarea {
			  border: 2px inset #ccc;
			  background-color: white;
			  
			}			
            
			#footer {
				padding-top: 10px;
				padding-bottom: 0px;
				position:fixed;
				bottom:0;
				width:100%;
                 }      						
			
            </style>
		</head>
<body style="font-size:14px;">
            <div style="width:100%;">
                    <div> 
					 <div   style="border:2px #1bbc9b solid;padding:1px;margin-bottom:1em;">
                            <div style="float:left;">
							 <div>
							  <b>{{__('Item').'-'.$item->id}}</b>
						     </div>
                            </div>
                            <div style="float:right;">
                                <div><b>{{__('Code Invoice')}}:</b> {{isset($item->num_invoice)?$item->num_invoice:__('Undefined')}}</div>
                                <div><b>{{__('Facture')}}:</b> {{isset($item->num_facture)?$item->num_facture:__('Undefined')}}</div>
							</div>
                            <div style="clear:both;"></div>
                      </div>
					  <div style="float:left;width:60%">
                           
                            <br/>
                            <div style="float:left;margin-left:10px;margin-top:0px;">
						       
								<div><b>{{$clinic->full_name}}</b></div>   
							   <div>{{(isset($clinic))?$clinic->full_address.' , '.$clinic->zip_code:__('Undefined')}}</div>
							   @if(isset($clinic) && isset($clinic->telephone))<div>T.:  {{$clinic->telephone}}</div>@endif
                               @if(isset($clinic) && isset($clinic->fax))<div>Fax: {{$clinic->fax}}</div>@endif
							   @if(isset($clinic) && isset($clinic->email))<div>{{__("Email")}}: {{$clinic->email}}</div>@endif
							</div>
                            <div style="clear:both;"></div>
                       </div>
					   <div style="float:left;width:40%">
					   <div style="clear:both;"></div>
					   </div>
					   <div style="clear:both;" />
					 </div> 
              </div>
			  <div style="margin-top:1.5em;">
			       <div class="row">
					  <div class="col-md-12">
						<table class="table table-bordered" style="width:100%;">
							<tbody>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Type')}}</th>
							   <th style="text-align:center;" scope="col">{{__('Category')}}</th>
							   <th style="text-align:center;" scope="col">{{__('Fournisseur')}}</th>
							   <th style="text-align:center;" scope="col">{{__('Collection')}}</th>
							   
							  </tr>
							  <tr>
							   <th style="text-align:center;" scope="col">{{$item->ft_name}}</th>
							   <th style="text-align:center;" scope="col">{{$item->cat_name}}</th>
                               <th style="text-align:center;" scope="col">{{$item->fournisseur}}</th>
							   <th style="text-align:center;" scope="col">{{$item->brand}}</th>
							  </tr>
							 </tbody>
                           </table>
                          </div>
                        </div>
                 </div>	
                 <div style="margin-top:0.5em;">
			       <div class="row">
					  <div class="col-md-12">
						<table class="table table-bordered" style="width:100%;">
							<tbody>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Cost Price')}}</th>
							   <th style="text-align:center;" scope="col">{{__('Formula')}}</th>
							    <th style="text-align:center;" scope="col">{{__('Sel Price')}}</th>
							    <th style="text-align:center;" scope="col">{{__('Taxable')}}</th>
							   
							  </tr>
							  <tr>
							    <th style="text-align:right;" scope="col">{{$item->cost_price}}</th>
							    <th style="text-align:center;" scope="col">{{$item->formula_name}}</th>
							    <th style="text-align:right;" scope="col">{{$item->sel_price}}</th>
							    <th style="text-align:center;" scope="col">{{$item->taxable=='Y'?__('Yes'):__('No')}}</th>
							  </tr>
							 </tbody>
                           </table>
                          </div>
                        </div>
                 </div>
                 <div style="margin-top:0.5em;">
			       <div class="row">
					  <div class="col-md-12">
						<table class="table table-bordered" style="width:100%;">
							<tbody>
							  <tr>
							    <th style="text-align:center;" scope="col">{{__('Qty')}}</th>
							    <th style="text-align:center;" scope="col">{{__('Min')}}</th>
							    <th style="text-align:center;" scope="col">{{__('Max')}}</th>
							    <th style="text-align:center;" scope="col">{{__('Garanty')}}</th>
							  </tr>
							  <tr>
							   <th style="text-align:center;" scope="col">{{$item->qty}}</th>
							   <th style="text-align:center;" scope="col">{{$item->min}}</th>
							   <th style="text-align:center;" scope="col">{{$item->max}}</th>
							   <th style="text-align:center;" scope="col">{{$item->garanty}}</th>
							  </tr>
							 </tbody>
                           </table>
                          </div>
                        </div>
                 </div>				 
				<div style="margin-top:0.5em;">
			       <div class="row">
					  <div class="col-md-12">
						<table class="table table-bordered" style="width:60%;">
							<tbody>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Sku')}}</th>
							   <th scope="col">{{$item->sku}}</th>
							  </tr>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Material')}}</th>
							   <th scope="col">
							   @php
							    $material='';
								switch($item->materiel){
								 case '1': $material=__('Metal'); break;
								 case '2': $material=__('Plastic'); break;
								 case '3': $material=__('Nylon'); break;
								 case '4': $material=__('Metal Groove'); break;
								 case '5': $material=__('Drill'); break;
								 default: $material=__('Undefined');
								}
							   @endphp
							   {{$material}}
							   </th>
							  </tr>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Description')}}</th>
							   <th scope="col">{{$item->description}}</th>
							  </tr>
							  <tr>
							   <th style="text-align:center;" scope="col">{{__('Bar Code')}}</th>
							   <th scope="col" style="font-size:10px;">{!! DNS1D::getBarcodeHTML($item->barcode, 'C128',2,22) !!}</th>
							  </tr>
							 </tbody>
                           </table>
                          </div>
                        </div>
                 </div>				  
				@if(isset($item) && isset($item->notes))
				<div style="page-break-before:always;overflow-x:auto;margin-top:1em;">
				  <div class="row">
					  <div class="col-md-12">
						  <div id="tarea">{{$item->notes}}</div>
					  </div>
			       </div>
		       </div>			  
		       @endif
   </body>
</html>  	