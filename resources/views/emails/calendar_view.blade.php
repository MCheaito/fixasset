<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="{{asset('dist/bootstrap5/css/bootstrap.min.css')}}">
</head>
<body>
<div class="container">
	
	  <p>{!!$details['msg']!!}</p>
	  <p><a href="{{$details['link']}}">{{__('Link')}}</a></p>
	  
	  <br/><br/>
	  
	  <footer>
		 <small>
		 <br/>
		 <div><b>{{isset($details['branch_name'])?$details['branch_name']:__('Undefined')}}</b></div>
		 <div><b>{{__("Address")}} :</b> {{isset($details['branch_address'])?$details['branch_address']:__('Undefined')}}</div>
		 @if(isset($details['branch_tel']))<div><b>Tel. :</b> {{$details['branch_tel']}}</div>@endif
         @if(isset($details['branch_fax']))<div><b>Fax :</b> {{ $details['branch_fax']}}</div>@endif
		 @if(isset($details['branch_email']))<div><b>{{__("Email")}} :</b> {{$details['branch_email']}}</div>@endif
		 </small>
	  </footer>
   
 </div>   
</body>						
</html>			