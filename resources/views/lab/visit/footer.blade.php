
   <div style="font-size:9px; width: 100%;">
        @if(isset($general_sig))
		    <div style="float:right;">
                 <img src="{{config('app.url').'/storage/app/7mJ~33/'.$general_sig->path}}" style="width:140px;height:70px;" alt="SIgnature">				  
			</div>
		@else
 		<div style="float:right;"></div>
		@endif
		<div style="clear: both;"></div>
	   
	   <hr style="margin:0;padding:0;"/>
	   <div style="text-align: right;">
           {{ __('Print Date').' : '.date('d/m/Y H:i') }}
       </div>
       <div class="page" style="text-align: center;">
           Page 
       </div>
       <div style="margin-top:10px;text-align: center;">
          {{$branch_data}}
       </div>
	   
   </div>
   