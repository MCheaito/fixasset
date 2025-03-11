<!--
 DEV APP
 Created date : 3-4-2023
-->
<!DOCTYPE html>
<html>
  <head>
	 <meta name="viewport" content="width=device-width, initial-scale=1">
     
	 <title>{{__('Item label')}}</title>
	 <style>
	 
	   @page { margin: 0px 0px 0px 73px; }
	 </style>
  <head>
  <body>
   
    @php 
   $item_specs = json_decode($item->items_specs,true);
   $model = isset($item_specs[0])?$item_specs[0]:'';
   $color = isset($item_specs[1])?$item_specs[1]:'';
   $chassis = isset($item_specs[2])?$item_specs[2]:'';
   $data = $model.' '.$color.' '.$chassis;
   $data = strlen($data)>16?substr($data,0,15).'...':$data;	
   $description = isset($item->description)?(strlen($item->description)>16?substr($item->description,0,15).'...':$item->description):'';
   @endphp
   <div style="width:100%;"> 
		
		
	     <div style=" transform-origin: 10px 20px; transform: rotate(90deg);">
			@switch($item->gen_types)
			  @case(1)
			  <span><span style="font-size:12px;">{{$data}}<br/>{{$item->sel_price.'$'}}</span><br/><br/><span style="font-size:12px;">{{$item->barcode}}</span><br/>{!! DNS1D::getBarcodeHTML($item->barcode, 'C128',0.95,22) !!}</span>
			   @break
			   @default
			  <span><span style="font-size:12px;">{{$description}}<br/>{{$item->sel_price.'$'}}</span><br/><br/><span style="font-size:12px;">{{$item->barcode}}</span><br/>{!! DNS1D::getBarcodeHTML($item->barcode, 'C128',0.95,22) !!}</span>
             @endswitch 
         </div>
       		
	</div>
	
  </body>
</html>  	