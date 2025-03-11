<div class="container-fluid">	
   <div class="mt-1 row form-group">
	  <div class="col-md-4">
		<input type="text" id="searchCultInput" class="form-control mb-3" placeholder="Search...">
	  </div>
	  <div class="col-md-8 text-right">
	     @if($edit_culture)
		   <button type="button" class="btn btn-action" id="edit_culture" {{isset($order) && ($order->status=='V')?'disabled':''}}>{{__("Edit")}}</button>
		   <button type="button" id="cancel_culture" class="m-1 btn btn-reset" disabled >{{__('Cancel')}}</button>
		 @endif  
		<button type="button" class="btn btn-action" id="print_culture">{{__("Print")}}</button>
	  </div>
	</div>
  
    <div class="row form-group">
	    <div class="table-responsive col-md-12">   
		  <table id="culture_tbl" class="table-bordered table-sm" style="width:100%;">
		   <thead class="text-center">
		     <tr>
			   <th style="display:none;">{{__('#')}}</th>
			   <th style="display:none;">{{__('test id')}}</th>
			   <th>{{__('Test Name')}}</th>
			   <th>{{__('Gram Stain')}}</th>
			   <th>{{__('Culture Result')}}</th>
			   <th>{{__('Germ')}}</th>
			   <th>{{__('Antibiotics')}}</th>
			   <th style="display:none;">{{__('Choosen Antibiotics')}}</th>
			   <th style="display:none;">{{__('Choosen Bacteria')}}</th>
			  </tr>
		   </thead>
		   <tbody>
		   @foreach($culture_test as $t)
			 @php 
			 $Results = $test_textResults->where('test_id',$t->test_id)->all();
			 $Results1 = $gram_staim_results->where('test_id',$t->test_id)->all();
			 $culture_test_det = DB::table('tbl_order_culture_results_detail')->where('culture_id',$t->id)->where('active','Y')->get(); 
		     $savedBacterias = $savedBacteriaIDs = $savedAntibiotics = array();
			 foreach($culture_test_det as $d){
			  $bact = DB::table('tbl_lab_sbacteria')->where('id',$d->bacteria_id)->value('descrip');
              if(!in_array($bact,$savedBacterias)){
			   array_push($savedBacterias,$bact);
			  }
             if(!in_array($d->bacteria_id,$savedBacteriaIDs)){
			   array_push($savedBacteriaIDs,$d->bacteria_id);
			  }
             array_push($savedAntibiotics,array($d->bacteria_id,$d->antibiotic_id,$d->result));			  
		     }
			 @endphp
			 <tr id="{{'row'.$t->id}}">
			   <td style="width:0%;display:none;">{{$t->id}}</td>
			   <td style="width:0%;display:none;">{{$t->test_id}}</td>
			   <th style="font-size:14px;width:10%;">{{$t->test_name}}</th>
			   <td style="font-size:14px;width:35%;">
			      <input type="text" class="gram_staim form-control" list="all_gram_stain_results{{$t->id}}" value="{{$t->gram_staim}}" disabled />
			      <datalist id="all_gram_stain_results{{$t->id}}">
				    @foreach($Results1 as $r)
				     <option value="{{$r->name}}"></option>
				    @endforeach
				  </datalist>
			   </td>
			   <td style="font-size:14px;width:30%;">
				   <input type="text" class="form-control culture_result" list="all_culture_results{{$t->id}}" value="{{$t->culture_result}}" disabled />
				   <datalist id="all_culture_results{{$t->id}}">
					  @foreach($Results as $r)
					   <option value="{{$r->name}}"></option>
					  @endforeach
				   </datalist>   
			   </td>
			   <td class="bactNames" style="font-size:14px;width:20%;">{{implode(',',$savedBacterias)}}</td>
			   <td class="text-center" style="width:5%;"><button type="button" class="openBacteriaModal btn btn-action btn-icon btn-sm" disabled >{{__('Choose')}}</button></td>
               <td  style="width:0%;display:none;"><input class="antibiotic" type="hidden" value="{{json_encode($savedAntibiotics)}}"/></td>
			   <td class="bactIDs" style="width:0%;display:none;">{{implode(',',$savedBacteriaIDs)}}</td> 
             </tr>
		   @endforeach
           </tbody>
		 </table>
        </div>
	</div>	
</div>  