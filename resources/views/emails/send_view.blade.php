<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="{{asset('dist/bootstrap5/css/bootstrap.min.css')}}">
</head>
<body>
<div class="container">	
	  
	  @if(isset($details['msg_rem']))
	   <p>{!!$details['msg_rem']!!}</p>	  
	  @else	  
	  <p>{{$details['title']}}</p>
      <p>{{$details['msg1']}}</p>
	  <p>{{$details['msg2']}}</p>
	  @endif
	  
 </div>   
</body>						
</html>			