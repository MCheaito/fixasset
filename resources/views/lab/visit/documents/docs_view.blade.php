<!--
   DEV APP
   Created date : 7-9-2022
-->
<div id="DOCS" class="col-md-6 mt-3">
            	<div class="card card-outline ">
				    		   
					   <div class="card-header">
							<div class="card-title"><a class="text-white" href="{{route('emr.visit.documents.index',[app()->getLocale(),$visit->id])}}" ><u><b>{{__('Medical documents')}}</b></u></a></div>
							<div class="card-tools">
							   <a href="{{route('emr.visit.documents.index',[app()->getLocale(),$visit->id])}}" class="btn btn-sm btn-resize border border-radius rounded-circle" id="editDOC" title="{{__('edit')}}"><i class="far fa-edit"></i></a>
							  <button type="button" class="btn btn-sm btn-resize border border-radius rounded-circle" data-card-widget="collapse">
								<i class="fas fa-minus"></i>
							  </button>
							 
					     	</div> 
						</div>	
						<div class="card-body">
			                 <div class="row" style="overflow-x:hidden;">
  							  
							      @if(count($med_docs)>0)
									   @foreach($med_docs as $image)
								        @php $ext = explode(".",$image->name)[1]; @endphp
											@if($ext=='pdf')
											<div class="spotlight-group text-center col-md-auto col-4 m-1 border">
	
											   <a  class="fancybox" data-fancybox data-type="pdf"  href="{{$image->path}}">
												   <i class="fa fa-file-pdf fa-lg" style="color:#3574AA;"></i>
												   <br/>{{$image->name}}
												</a>
											</div>	
											@else
											 <div class="spotlight-group text-center col-md-2 col-4 m-1 border">	
											   <a class="spotlight" data-title="{{$image->name }}" 
												 data-description="{{$image->notes}}" href="{{ $image->path }}" data-download="true"
												 data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
													<img  class="img-fluid" alt="" src="{{ $image->path }}" style="width:100%;height:100%;"/>
												</a>
												</div>
											@endif 
											
										
                                       @endforeach									   
								   @else
								    <div class="col-md-12">
								     <div class="mb-2"><i class="far fa-images fa-2x btn-active"></i></div>  
								     <div><h5>{{__('No documents')}}</h5></div>
									 </div>
								   @endif  
								 
								</div>
                            </div>
                            				
						</div>
					
		    </div>
	