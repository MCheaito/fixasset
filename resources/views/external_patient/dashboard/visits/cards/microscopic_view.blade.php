<!--
   DEV APP
   Created date : 8-7-2023
-->
<div  class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><u><b>{{__('Other Microscopy data')}}</b></u></div>
							<div class="card-tools">
						  	   <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								 <i class="fas fa-minus"></i>
							   </button>
							 
					     	</div> 
						</div>	
						<div class="card-body ">
			                <div class="row table-responsive">
							   <div class="col-md-12"> 
								  @if($other_microscopic_data->count()>0)
								  <table class="table-bordered table-sm" style="width:100%;">
							       <thead>
								    <tr><th>{{__('Description')}}</th></tr>
								   </thead>
								   <tbody>
								     @foreach($other_microscopic_data as $tp)
									 <tr>
									 <td>
											 
											 @php
											 $allowed_tags = array('<b>','<strong>','<i>','<u>','<br>','<ul>','<ol>','<li>','<em>','<font>');
											 $str = strip_tags($tp->description,$allowed_tags); 
											 @endphp
										 
										      @if(strlen($str) > 100)
														{!!substr($str,0,100)!!}
														<span class="read-more-show hide_content">...<i class="fa fa-plus icon-sm btn-active"></i></span>
														<span class="read-more-content"> {!!substr($str,100,strlen($str))!!} 
														<span class="read-more-hide hide_content"><i class="fa fa-minus btn-active"></i></span> </span>
											   @else
									             {!!$str!!}
									           @endif
										
									 </td>
									 </tr>
									 @endforeach
								   </tbody>
								  </table>
								  @else							   
								   <h5>{{__('Undefined')}}</h5>
								  @endif
								</div>
                            </div>														
						</div>
						
					
				</div>
            </div>
          
           