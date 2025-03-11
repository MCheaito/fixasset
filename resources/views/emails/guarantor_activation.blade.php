<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="{{asset('dist/bootstrap5/css/bootstrap.min.css')}}">
</head>
<body>
<div class="container">	
    <p>Dear {{$guarantor}},</p>
    <p>Thank you for registering with {{$lab->full_name}} .</p>
	<p>Please click the following link below to activate your account:</p>
    <p><a href="{{ $url }}">Activate Account</a></p>
	<p><b>Note: This link will expire in 4 days.</b></p>
	<p style="background-color: #f8d7da;color: #721c24;border: 1px solid #f5c6cb;padding: 15px;border-radius: 4px;">
       <b>Important:</b> Please change your default account password (123456) as soon as you log in. 
        After logging in, you can change your password by going to the users page.
    </p>
	<br/>
	<p>Should you have any questions, please feel free to contact us at :</p>
    @if(isset($lab->full_address) && $lab->full_address!='')<div><b>{{__("Address")}} :</b> {{$lab->full_address}}</div>@endif
	@if(isset($lab->telephone)&& $lab->telephone!='')<div><b>Phone :</b> {{$lab->telephone}}</div>@endif
	@if(isset($lab->whatsapp)&& $lab->whatsapp!='')<div><b>Whatsapp :</b> {{$lab->whatsapp}}</div>@endif
	@if(isset($lab->email) && $lab->email!='')<div><b>{{__("Email")}} :</b> {{$lab->email}}</div>@endif
	<br/>
	<p>Best regards,</p>
	<p>{{$lab->full_name}}</p>
	
 </div>   
</body>						
</html>			