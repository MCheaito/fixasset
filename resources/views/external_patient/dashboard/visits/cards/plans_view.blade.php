<!--
   DEV APP
   Created date : 26-2-2023
-->

 @if($pat_treatment_plan->count())
    <div  class="col-md-4">
		   <div class="card card-outline ">
			 <div class="card-body p-1">
				<select name="tp_select" id="tp_select" class="label-size custom-select rounded-0"  size="5">
				@foreach($pat_treatment_plan as $c)
				  @php $cancelled_state =($c->status=='N')?' - '.__('Cancelled'):''; @endphp
				 <option value="{{$c->id}}">{{'#'.$c->id.' , '.Carbon\Carbon::parse($c->created_at)->format('Y-m-d H:i').$cancelled_state}}</option>
				@endforeach
				</select>		
			 </div>
		   </div>
	</div>
    @foreach($pat_treatment_plan as $c)	
    @php
		  
		   $tp_id = ' , #'.$c->id;
		   $pat_tp_desc = isset($c->description)?json_decode($c->description,true):array();
		   $pat_tp_docs = UserHelper::getTPDOCS($c->id);
		@endphp		
	<div id="PLAN-{{$c->id}}" class="card-tp col-md-8" style="display:none;">
            	<div class="card card-outline " >
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Treatment plan').$tp_id}}</b></u>@if($c->status=='N')<span class="ml-1 label-size badge bg-gradient-danger">{{__('Cancelled')}}</span>@endif</div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
							 
					     	</div> 
						</div>	
						<div class="card-body" style="height:300px;overflow-y:auto">
			               @if(count($pat_tp_desc)>0)
							<div class="row table-responsive">
							   <div class="col-md-12"> 
								 
								  <table class="table-bordered table-sm" style="width:100%;">
							       <thead>
								    <tr><th>{{__('Description')}}</th></tr>
								   </thead>
								   <tbody>
								     @foreach($pat_tp_desc as $tp)
									 <tr>
									 <td>
											 
											 @php
											 $allowed_tags = array('<b>','<strong>','<i>','<u>','<br>','<ul>','<ol>','<li>','<em>','<font>');
											 $str = strip_tags($tp,$allowed_tags); 
											 @endphp
										      @if(strlen($str) > 100)
												<span>{!!substr($str,0,100)!!}<span class="read-more-show hide_content">...<i class="fa fa-plus icon-sm btn-active"></i></span><span class="read-more-content">{!!substr($str,100,strlen($str))!!}<span class="read-more-hide hide_content"><i class="fa fa-minus btn-active"></i></span></span></span>
											   @else
									             {!!$str!!}
									           @endif
										
									 </td>
									 </tr>
									 @endforeach
								   </tbody>
								  </table>
								
								</div>
                            </div>
							@endif
                            @if($pat_tp_docs->count())
						     <div class="mt-2 row" style="overflow-x:hidden;">
  							  
							      
									   @foreach($pat_tp_docs as $image)
								       
											@php $ext = explode(".",$image->real_name)[1]; @endphp
											@if($ext=='pdf')
											<div class="spotlight-group text-center col-md-auto col-4 m-1 border">	
											  <a  class="fancybox" data-fancybox data-type="pdf"  href="{{UserHelper::rp_txt($image->path)}}">
												   <i class="fa fa-file-pdf fa-lg" style="color:#3574AA;"></i>
												   <br/>{{$image->real_name}}
												</a> 
											</div>	
											@else
											<div class="spotlight-group text-center col-md-2 col-4 m-1 border">
   										     <a class="spotlight" data-title="{{$image->real_name }}" 
												 href="{{ UserHelper::rp_txt($image->path) }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ UserHelper::rp_txt($image->thumb_path) }}" style="width:100%;height:100%;"/>
												</a>
											</div>	
											@endif 
										 
                                       @endforeach									   
								   
								</div>
                               @endif							
						</div>
						
					
				</div>
            </div>
		@endforeach	
  @else
	  <div id="PLAN" class="col-md-6 mt-1">
					<div class="card card-outline ">
								   
						   <div class="card-header">
								<div class="card-title"><u><b>{{__('Treatment Plan')}}</b></u></div>
								<div class="card-tools">
								   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
									 <i class="fas fa-minus"></i>
								   </button>
								</div> 
							</div>	
							<div class="card-body">
								<div class="row table-responsive">
								  <div class="col-md-12">
								  <h5>{{__('Undefined')}}</h5>
									</div>
								</div>								
							</div>
							
						
					</div>
	</div>			
  @endif	  
           