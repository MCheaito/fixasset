<!-- 
 DEV APP
 Created date : 28-12-2022
-->
@extends('gui.main_gui')

@section('content')
    <div class="bg-light rounded">
       
		<div class="container mt-2">
	
				<!--clinic list-->
			 <div class=" card card-outline ">	 
				 <form action="{{route('userslist.store',app()->getLocale())}}" method="POST">
                    @csrf
				<div class="m-1 form-row" id="clinic_list">
					<div class="mt-1 col-md-6">
						<label for="acc_type" class="form-label label-size">{{__('Account type')}}</label>
						 <select id="account_type" name="acc_type" class="form-control" readonly>
							<option value="2">{{__('Branch')}}</option>
						  </select>
								 
						@if ($errors->has('acc_type'))
							<span class="text-danger text-left">{{ $errors->first('acc_type') }}</span>
						@endif
					</div>
					<div  class="mt-1 col-md-6">
						<label for="clinic_num" class="form-label label-size">{{__('Branch name')}}</label>
						 @if(count($clinics))
							
								  @php $selected_value=old('clinic_num') @endphp
								@if(count($clinics)>1)
								 <select class="select2 form-control" name="clinic_num" id="clinic_num" style="width:100%;">
								  <option value='' {{($selected_value=='')?'selected':''}}>{{__('Select Branch')}}</option>
								@foreach($clinics as $clinic)
								<option  value="{{$clinic->id}}" {{($selected_value==$clinic->id)?'selected':''}}>{{$clinic->full_name}}</option>
								@endforeach
								 </select>
								@else
								  <select class="form-control" name="clinic_num" id="clinic_num" readonly>
									  @foreach($clinics as $clinic)
									   <option  value="{{$clinic->id}}" selected>{{$clinic->full_name}}</option>
									  @endforeach
									</select>  
								@endif
							</select>
						   @if ($errors->has('clinic_num'))
							<span class="text-danger text-left">{{ $errors->first('clinic_num') }}</span>
						@endif
						 
						 @endif
					 </div>
					  <div class="mt-1 col-md-6">
						<label for="fname" class="form-label label-size">{{__('First name')}}</label>
						<input value="{{old('fname')}}" 
							type="text" 
							class="form-control" 
							name="fname" 
							placeholder="{{__('First name')}}"/>

						@if ($errors->has('fname'))
							<span class="text-danger text-left">{{ $errors->first('fname') }}</span>
						@endif
					</div>
					<div class="mt-1 col-md-6">
						<label for="lname" class="form-label label-size">{{__('Last name')}}</label>
						<input value="{{old('lname')}}" 
							type="text" 
							class="form-control" 
							name="lname" 
							placeholder="{{__('Last name')}}"/>

						@if ($errors->has('lname'))
							<span class="text-danger text-left">{{ $errors->first('lname') }}</span>
						@endif
				    </div>
					<div class="mt-1 col-md-6">
						<label for="email" class="form-label label-size">{{__('Email')}}</label>
						<input value="{{old('email')}}"
							type="text" 
							class="form-control" 
							name="email" 
							placeholder="{{__('Email address')}}"/>
						@if ($errors->has('email'))
							<span class="text-danger text-left">{{ $errors->first('email') }}</span>
						@endif
					</div>
					<div id="clin_username_list" class="mt-1 col-md-6">
						<label for="username" class="form-label label-size">{{__('User Name')}}</label>
						<div class="input-group">
						  <div class="input-group-prepend">
							<input type="text" autoComplete="false"  id="Prefix_Username" name="Prefix_Username" value="{{old('Prefix_Username')}}" class="col-2 form-control" readonly="true"/>
							<input value="{{old('username')}}"
								type="text" 
								class="form-control" 
								name="username" 
								placeholder="{{__('Username')}}"/>
							</div>
						</div>
						
						@if ($errors->has('username'))
							<span class="text-danger text-left">{{ $errors->first('username') }}</span>
						@endif
					 </div>	
                   				
                     <div class="mt-1 col-md-6">
                            <label for="password" class="form-label label-size">{{ __('Password') }}</label>
                           <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
                                @if ($errors->has('password'))
                                    <span class="text-danger text-left">
                                        {{ $errors->first('password') }}
                                    </span>
                                @endif
                            
                      </div>
                     <div class="mt-1 col-md-6">
                            <label for="password-confirm" class="form-label label-size">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                     </div>
					  
					  <div id="clin_permission_list" class="mt-1 col-md-6">
						<label for="permission" class="form-label label-size">{{__('Access Permission')}}</label>
						 @php $selected_value=old('permission') @endphp
						<select name="permission" class="select2 form-control" style="width:100%;">
						  <option value="" {{($selected_value=='')?'selected':''}}>{{__('Undefined')}}</option>
						  <option value="A" {{($selected_value=='A')?'selected':''}}>{{__('Admin')}}</option>
						  <option value="S" {{($selected_value=='S')?'selected':''}}>{{__('Super user')}}</option>
						  <option value="U" {{($selected_value=='U')?'selected':''}}>{{__('User')}}</option>
						</select>
						@if ($errors->has('permission'))
							<span class="text-danger text-left">{{ $errors->first('permission') }}</span>
						@endif
                      </div>
					   <div class="mt-1 col-md-6">
						      <label for="profiles" class="form-label label-size">{{__('Profile Permission')}}</label>
							  <select id="profiles" name="profiles" class="select2 form-control" style="width:100%;">
								   <option value="">{{__('Undefined')}}</option>
								  @foreach($profiles as $profile)
									<option value="{{$profile->id}}">{{(app()->getLocale()=='en')?$profile->name_en:$profile->name_fr}}
								  @endforeach
                              </select>							  
                        </div>					 
                 </div>
				 <div class="card  mt-2 m-1 border border-success">
					   <div class="text-center card-title">
					     <h5>{{__('All profile permissions')}}</h5>
					   </div>
					   <div class="card-body">
						   <div class="row m-1 mb-2">
						    <div class="col-md-4">
							  <input type="checkbox" id="checkAll" name="checkAll" value="0" style="width:1em;height:1em;"/>&#xA0;{{__('Select all')}}
						    </div>
						   </div>
						   <div class="row m-1">
							    @foreach($menus as $m)
										 @if($m=='patients_list' || $m=='patients_calendar' || $m=='visits' || $m=='waiting_room' || $m=='billings' || $m=='custom_reports'
										 || $m=='general_reports' || $m=='inventory' || $m=='clinical_notes' || $m=='settings' || $m=='referrals') 
											 <div class="m-1 col-md-12 border border-info"></div>
											  <div class="col-md-4">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;"/>&#xA0;<b>{{__($m)}}</b>
											  </div>
										 @else
											   @if($m=='profile' || $m=='branches' || $m=='doctors'
												  || $m=='users' || $m=='contact_support' || $m=='external_branches')	 
											   <div class="border col-md-12"></div>
											   <div class="col-auto ml-2">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;"/>&#xA0;<b>{{__($m)}}</b>
											   </div>
											   <div class="col-md-8"></div>
											   @else
											   <div class="col-auto ml-3">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;"/>&#xA0;{{__($m)}}
											   </div>
											   @endif	   
										 @endif
							     @endforeach
							</div>
                       </div>						
					</div>
				  <div class="form-row mt-2 mb-2">
				    <div class="col-md-12 text-center"> 
					   <input type="submit" name="save_clinic" class="btn btn-action" value="{{__('Save')}}"/>
					   <a href="{{ route('userslist.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>
					 </div>
				  </div>
				 </form>
			</div>	 
                <!--end clinic list-->    					
        </div>
</div>
@endsection
@section('scripts')

<script>
$('document').ready(function(){
	
	$('[name=email]').inputmask("email");
	
	var type=$('#account_type').val();
	var facid=$('#clinic_num').val();
	if(facid!=-1)
	{$('#Prefix_Username').val(facid+'_');}
    
	$('.select2').select2({theme:'bootstrap4',width:'resolve'});
	
	$('#profiles').change(function(e){
	e.preventDefault();
	var profile_id = $('#profiles').val();
	
	if(profile_id==''){
		$('.menu').prop('checked',false);
	}else{
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
        $.ajax({
			url: '{{route("get_defined_profile",app()->getLocale())}}',
			type: 'post',
			data: {profile_id:profile_id},
			dataType: 'json',
			success: function(data){
			  	
				$('input[type=checkbox]').each(function(){
								
					if($.inArray($(this).val(),data.access_menus)==-1){
						$(this).prop('checked',false);
					}else{
						$(this).prop('checked',true);
					}
					
                });
			}
		});
		
	}
});

$('#checkAll').click(function () {
     $('#profiles').val('').change();
     	 
     $('.menu').prop('checked', this.checked);    
 });
	
});
</script>
<script>

$('#clinic_num').change(function(){
	var facid=$('#clinic_num').val();
	$('#Prefix_Username').val(facid+'_');
});


</script>

@endsection