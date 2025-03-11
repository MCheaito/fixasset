@component('mail::message')
       <p>{{__('Online inquiry done in').' : '}}<b>{{Carbon\Carbon::now()->format("Y-m-d H:i")}}</b></p> 
	   <p>{{__('Name').' : '}}<b>{{$details['full_name']}}</b></p>
	   <p>{{__('User').' : '}}<b>{{$details['user_name']}}</b></p>
	   <p>{{__('Email').' : '}}<b>{{$details['email']}}</b></p>
	   <p>{{__('Type').' : '}}<b>{{$details['type']}}</b></p>
	   <hr/>
	   <p>{{__('Message').' : '}}</p>
	   <p><b>{!!$details['msg']!!}</b></p>
@endcomponent