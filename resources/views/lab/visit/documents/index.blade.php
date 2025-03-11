<div class="container-fluid main-page">
     @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)

                        <li><button type="button" class="close" data-dismiss="alert">×</button>{{ $error }}</li>

                    @endforeach
                </ul>
            </div>
        @endif
		@if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        </div>
        @endif
        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        </div>
        @endif
       	
		@if(isset($order) && ($order->status=='V'))
		@else	
		<div  id="documents_upload" class="card  m-1">
			<form id="documents_form" class="m-1 form-row" action="{{route('lab.visit.uploadAttach',app()->getLocale())}}" method="POST" enctype="multipart/form-data">
              @csrf 
			 
			<div class="col-md-4">
					<input type="hidden" id="doc_order_id" name="doc_order_id" value="{{isset($order)?$order->id:'0'}}"/>
					<label for="name" class="label-size">{{__('Description')}}</label>
					<textarea id="docs_descrip" name="description" class="form-control" rows="2"></textarea>
			</div>
			
			<div class="col-md-8">
			    
					<input id="input_files" name='files[]' type='file' accept="image/*,application/pdf" multiple />
				
               </div>
		    
		   </form>
		</div>
        @endif	
				  
    
	   
       <!--display all documents in a gallery-->
		<div class="row mt-2">
		  <div class="col-md-12">
			 <div class="card card-outline card-teal">
               <div class="card-header">
							<div class="card-title"><b>{{__('All medical documents')}}</b></div>
							<div class="card-tools">
							   <button type="button" class="btn btn-sm btn-resize" data-card-widget="collapse"><i class="fas fa-minus"></i></button>	 
							</div> 
				</div>	
			   <div class="card-body"> 			 
				<div class="row mt-1">
					@if($documents->count())
					  @foreach($documents as $image)
						@php $ext = explode(".",$image->name)[1]; @endphp
						<div  class="text-center col-md-2 col-4 mb-2">
							@if($ext=='pdf')
								<a  class="fancybox" data-fancybox data-type="pdf"  href="{{url('furl/'.$image->path)}}">
								   <i class="fa fa-file-pdf fa-lg" style="color:#3574AA;"></i><br/>{{$image->name}}
								</a>
								<div style="font-size:0.65rem;">{{Carbon\Carbon::parse($image->created_at)->format('d-m-Y')}}</div>
							@else
								<a class="spotlight" data-title="{{$image->name }}" 
							     data-description="{{$image->notes}}" href="{{ url('furl/'.$image->path) }}" data-download="true"
							     data-button="{{__('Print')}}" data-button-href="javascript:window.print()">
									<img  class="img-fluid" alt="" src="{{ url('furl/'.$image->path) }}" style="width:100%;height:60px;"/>
								</a>
								<div style="font-size:0.65rem;">{{Carbon\Carbon::parse($image->created_at)->format('d-m-Y')}}</div>
							@endif 
							<form action="{{route('lab.visit.destroyAttach',app()->getLocale())}}" method="POST">
							<input type="hidden" name="_method" value="delete">
							{!! csrf_field() !!}
							<input type="hidden" name="image_id" value="{{$image->id}}"/>
							<button type="submit" id="destroy_attach" class="attachIMG close-icon btn btn-icon btn-delete btn-sm" title="{{__('delete')}}" {{isset($order) && $order->status=='V'?'disabled':''}}><i class="fa fa-times-circle"></i></button>
							</form>
						</div>
					 @endforeach
					@endif
				</div>	
			   </div>
			 </div>  
          </div>
	   </div> 

    
</div> <!-- container / end -->

