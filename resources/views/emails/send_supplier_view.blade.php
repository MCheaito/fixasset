<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="{{asset('dist/bootstrap5/css/bootstrap.min.css')}}">
</head>
<body>
<div class="container">	

	  <p>{{$details['title']}}</p>
      <p>{!!$details['msg']!!}</p>
	  <br/><br/>
	  <br/><br/>
	  <footer>
		<small>
		 <br/>
		 <div><b>{{$details['branch_name1']}}</b></div>
		 <div><b>{{$details['branch_name2']}}</b></div>
		 @if(isset($details['branch_address']))<div><b>{{__("Address")}} :</b> {{$details['branch_address']}}</div>@endif
		 @if(isset($details['branch_tel']))<div><b>Tel. :</b> {{$details['branch_tel']}}</div>@endif
         @if(isset($details['branch_fax']))<div><b>Fax :</b> {{ $details['branch_fax']}}</div>@endif
		 @if(isset($details['branch_email']))<div><b>{{__("Email")}} :</b> {{$details['branch_email']}}</div>@endif
		</small>
	  </footer>
   
 </div>   
</body>						
</html>			