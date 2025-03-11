@extends('gui.main_gui')

@section('content')
<div class="container-fluid mt-1">

		<div class=" card card-outline">	
			<form action="{{route('userslist.store',app()->getLocale())}}" method="POST">
                @csrf
               <div class="form-row m-1">
						<div class="mt-1 mb-1 col-md-2">
						<input type="hidden" name="acc_type" value="3"/>
						<input type="text" name="acc_name" value="{{__('Guarantor')}}" class="form-control" disabled />						  
					   </div>
					    <div  class="mt-1 mb-1 col-md-4">
							     <select class="form-control" name="clinic_num" id="clinic_num">
									  @foreach($clinics as $clinic)
									  <option  value="{{$clinic->id}}">{{$clinic->full_name}}</option>
									  @endforeach
								</select>  
							
					    </div>
					    <div class="mt-1 mb-1 col-md-4">
						   <select class="select2 form-control" name="ext_lab_num" id="ext_lab_num" style="width:100%;">
						      @foreach($ext_labs as $d)
								 <option  value="{{$d->id}}">{{$d->full_name}}</option>
							  @endforeach
						   </select>  
					   </div>
						<div class="mt-1 mb-1 col-md-2 text-right"> 
						   <input type="submit" name="save_clinic" class="btn btn-action" value="{{__('Save')}}"/>
						   <a href="{{ route('userslist.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a>
						 </div>
				  </div>
			   <div class="row m-1">  
					 			 					  
					  
					   <div class="mt-1 col-md-4">
							<label for="fname" class="form-label label-size">{{__('First name')}}</label>
							<input value="{{old('fname')}}" 
								type="text" 
								class="form-control" 
								name="fname"
                                id="fname"								
								placeholder="{{__('First name')}}"
								>

							@if ($errors->has('fname'))
								<span class="text-danger text-left">{{ $errors->first('fname') }}</span>
							@endif
						</div>
						<div class="mt-1 col-md-4">
							<label for="lname" class="form-label label-size">{{__('Last name')}}</label>
							<input value="{{old('lname')}}" 
								type="text" 
								class="form-control" 
								id="lname"
								name="lname" 
								placeholder="{{__('Last name')}}"
								>

							@if ($errors->has('lname'))
								<span class="text-danger text-left">{{ $errors->first('lname') }}</span>
							@endif
						</div>
						<div class="mt-1 col-md-4">
							<label for="email" class="form-label label-size">{{__('Email')}}</label>
							<input value="{{old('email')}}"
								id="email"
								type="text" 
								class="form-control" 
								name="email" 
								placeholder="{{__('Email address')}}">
							@if ($errors->has('email'))
								<span class="text-danger text-left">{{ $errors->first('email') }}</span>
							@endif
						</div>
               
						<div class="mt-1 col-md-4">
							<label for="username" class="form-label label-size">{{__('Username')}}</label>
							<input value="{{old('username')}}"
									type="text" 
									class="form-control" 
									id="username"
									name="username" 
									placeholder="{{__('Username')}}"
									/>
							@if ($errors->has('username'))
								<span class="text-danger text-left">{{ $errors->first('username') }}</span>
							@endif
						 </div>
				
						  <div class="mt-1 col-md-4">
									<label for="password" class="form-label label-size">{{ __('Password') }}</label>

								   <input id="password" type="password" class="form-control" name="password"  autocomplete="new-password">

										@if ($errors->has('password'))
                                    <span class="text-danger text-left">
                                        {{ $errors->first('password') }}
                                    </span>
                                      @endif
									
							</div>

							 <div class="mt-1 col-md-4">
									<label for="password-confirm" class="form-label label-size">{{ __('Confirm Password') }}</label>
									<input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
							 </div>
					   	   
						  <div class="mt-1 col-md-2">
						      <label for="profiles" class="form-label label-size">{{__('Profile Permission')}}</label>
							  <select id="profiles" name="profiles" class="select2 form-control" style="width:100%;">
								   <option value="">{{__('Undefined')}}</option>
								  @foreach($profiles as $profile)
									<option value="{{$profile->id}}" selected>{{$profile->name_en}}
								  @endforeach
                              </select>							  
                          </div>
						  <div class="card  mt-3 col-md-10 border border-success">
						   <div class="text-center card-title">
							 <h5>{{__('All profile permissions')}}</h5>
						   </div>
						   <div class="card-body p-1">
								<div class="row m-1 mb-2">
								<div class="col-md-4">
								  <input type="checkbox" id="checkAll" name="checkAll" value="0" style="width:1em;height:1em;"/>&#xA0;{{__('Select all')}}
								</div>
							   </div>
							   <div id="div_menus" class="row m-1">
								    @foreach($menus as $m)
										 @if($m=='profile' || $m=='dashboard' || $m=='all_patients' || $m=='lab_requests' || $m=='medical_billings' || $m=='custom_reports'
										 || $m=='general_reports' || $m=='tests_settings' || $m=='all_resources' || $m=='users' || $m=='send_feedback' || $m=='inventory' || $m=='phlebotomy') 
											 <div class="m-1 col-md-12 border border-info"></div>
											  <div class="col-md-12">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;"/>&#xA0;<b>{{__($m)}}</b>
											  </div>
										 @else
											   
											   <div class="col-md-auto" style="margin-left:5px;">
												 <input class="menu" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;"/>&#xA0;{{__($m)}}
											   </div>
											     
										 @endif
							        @endforeach
								</div>
						   </div>						
						</div>
                     	  
                    </div>
					  
						
					  
					
		      </form>
         </div>

</div>
@endsection
@section('scripts')

<script>
$('document').ready(function(){
 var id = $('#ext_lab_num').val();
 $('#checkAll').prop('checked',true);
 $('.menu').prop('checked', true);
 $('.select2').select2({theme:'bootstrap4',width:'resolve'});
 $('#email').inputmask('email');
	$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
        $.ajax({
			url: '{{route("get_defined_profile",app()->getLocale())}}',
			type: 'post',
			data: {id:id,acc_type: $('#acc_type').val(),type:'extlab'},
			dataType: 'json',
			success: function(data){
			  	$('#fname').val(data.fname);
				$('#lname').val(data.lname);
				$('#username').val(data.username);
				$('#password').val(data.password);
				$('#password-confirm').val(data.password);
			}
		});
		
 $('#ext_lab_num').change(function(e){
	e.preventDefault();
	var id = $(this).val();
	$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
        $.ajax({
			url: '{{route("get_defined_profile",app()->getLocale())}}',
			type: 'post',
			data: {id:id,acc_type: $('#acc_type').val(),type:'extlab'},
			dataType: 'json',
			success: function(data){
			  	$('#fname').val(data.fname);
				$('#lname').val(data.lname);
				$('#username').val(data.username);
				$('#password').val(data.password);
				$('#password-confirm').val(data.password);
			}
		});
});	
	
$('#profiles').change(function(e){
	e.preventDefault();
	var profile_id = $('#profiles').val();
	
	if(profile_id==''){
		$('#checkAll').prop('checked',false);
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
			data: {id:profile_id,acc_type: $('#acc_type').val(),type:'profile'},
			dataType: 'json',
			success: function(data){
			  	
				$('input[type=checkbox]').each(function(){
								
					if($.inArray($(this).val(),data.access_menus)==-1){
						$(this).prop('checked',false);
					}else{
						$(this).prop('checked',true);
					}
					
                });
			   checkCheckAllCheckbox();
			}
		});
		
	}
});

$('.menu.master').change(function() {
     var isChecked = $(this).prop('checked');
	 $(this).closest('.master_div').nextUntil('.master_div').find('.menu.sub').prop('checked', isChecked);
	 checkCheckAllCheckbox();
    	
	});

$('#checkAll').change(function () {
     $('#profiles').val('');
     var isChecked = $(this).prop('checked');
     $('.menu').prop('checked', isChecked);
 });


	
function checkCheckAllCheckbox() {
        var allChecked = true;
        
        // Loop through each checkbox with class checkbox-item
        $('.menu').each(function() {
            if (!$(this).prop('checked')) {
                allChecked = false;
                return false; // Exit the loop early if any checkbox is not checked
            }
        });
        
        // Set the "Check All" checkbox based on allChecked flag
        $('#checkAll').prop('checked', allChecked); 
    }
	
	
});
</script>

@endsection