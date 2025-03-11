<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
	 
	   <p>{!!$details['msg1']!!}</p>
	   <br/>
	   <p>{!!$details['msg2']!!}</p>
	   <br/>
	   <p style="text-align:center;"><a href="{{$details['link']}}"><u>{{__("Confirm Account")}}</u></a></p>
	
	  <br/>
	  
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

