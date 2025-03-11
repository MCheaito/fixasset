<!-- 
 DEV APP
 Created date : 18-12-2022
-->
@extends('gui.main_gui')

@section('content')
    
<div class="container-fluid">
        <div class="mt-2">
            @include('layouts.partials.messages')
        </div>
    
		<div class="card card-outline">  
            <form method="post" 
			      onsubmit="return confirm('{{__('Are you sure to save this information?')}}');"
 				  action="{{ route('userslist.update',app()->getLocale()) }}">
                
                @csrf
              <div class="row m-1">
					<input type="hidden" name="id" value="{{$user->id}}"/>
					<div class="mt-1 mb-1 col-md-3">
                    <select id="account_type" name="acc_type" class="form-control" disabled>
					  @if($user->type==1)
					  <option value="1" selected>{{__('External Doctor')}}</option>
				      @endif
					  @if($user->type==2)
					  <option value="2" selected>{{__('Internal Lab')}}</option>
				     @endif
					 @if($user->type==3)
					  <option value="3" selected>{{__('Guarantor')}}</option>
				     @endif
					
					</select>
                    </div>
					@if(isset($clinic))
					<div id="clinics_list" class="mt-1 mb-1 col-md-5">
						<input id="clinic_num" type="text" class="form-control" readonly="true" value="{{$clinic->full_name}}"/>
					</div> 
                  @endif
					<div class="mt-1 mb-1 col-md-4 text-right"> 
					  <button type="submit" class="btn btn-action">{{__('Update')}}</button>
					  <a href="{{ route('userslist.index',app()->getLocale()) }}" class="btn btn-back">{{__('Back')}}</a></button>
					</div>
              </div>
			  <div class="row m-1">	
				
				 		 
				 <div class="mt-1 col-md-4">
                    <label for="name" class="form-label label-size">{{__('First name')}}</label>
                    <input value="{{ $user->fname }}" 
                        type="text" 
                        class="form-control" 
                        name="fname" 
                        placeholder="First name">

                   
                </div>
				<div class="mt-1 col-md-4">
                    <label for="name" class="form-label label-size">{{__('Last name')}}</label>
                    <input value="{{ $user->lname }}" 
                        type="text" 
                        class="form-control" 
                        name="lname" 
                        placeholder="Last name">

                   
                </div>
                <div class="mt-1 col-md-4">
                    <label for="email" class="form-label label-size">{{__('Email')}}</label>
                    <input value="{{ $user->email }}"
                        type="text" 
                        class="form-control" 
                        name="email" 
                        placeholder="Email address">
                    
                </div>
                <div class="mt-1 col-md-4">
                    <label for="username" class="form-label label-size">{{__('User Name')}}</label>
                   	@if($user->type==1 || $user->type==3)
						<input value="{{$user->username}}"
							type="text" 
							class="form-control" 
							id="username"
							name="username" 
							placeholder="Username"/>
					@endif		
					@if($user->type==2)
					 <!--@php $first_acc = App\Models\Clinic::where('active','O')->where('clinic_user_num',$user->id)->first(); @endphp	-->
				     <input value="{{$user->username}}"
							type="text" 
							class="form-control" 
							id="username"
							name="username" 
							placeholder="Username"/>
					<!--<div class="input-group">
					  <div class="input-group-prepend">
						<input type="text" autoComplete="false"  name="Prefix_Username" value="{{$user->clinic_num.'_'}}" class="col-2 form-control" readonly="true"/>
						
						</div>
					</div>-->
					@endif
					
                   
                </div>
				<div class="mt-1 col-md-4">
                    <label for="password" class="form-label label-size">{{ __('New Password') }}</label>
                    <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">                     
                </div>
                <div class="mt-1 col-md-4">
                    <label for="password-confirm" class="form-label label-size">{{ __('Confirm New Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password">
                </div>
								  
				<div class="col-md-6 select2-teal">
					<label for="od" class="label-size" style="font-size:16px;">{{__('clients')}} </label>
					<select class="data-select2" name="clients_code[]" style="width:100%;" data-dropdown-css-class="select2-teal" data-placeholder="{{__('Select Clients')}}"  multiple="multiple" >
					@foreach($clients as $sc)
					<option value="{{ $sc->id.';'.$sc->name }}" 
					 {{(isset($clients_code) && count($clients_code)>0 && in_array($sc->id,$clients_code))?'selected':''}}>{{$sc->name}}</option>
					@endforeach
                     </select>
				</div>	  
					  
				<div class="mt-1 col-md-2">
                    <label for="permission" class="form-label label-size">{{__('Access Permission')}}</label>
                    @if($user->type==2)
					<select name="permission" class="form-control">
					    @if(auth()->user()->permission == 'S' || auth()->user()->admin_perm=='O')
							@php $select=($user->permission == 'S')? 'selected': ''; @endphp
							<option value="S" {{$select}}>{{__('Super user')}}</option>
							@php $select=($user->permission == 'A')? 'selected': ''; @endphp
							<option value="A" {{$select}}>{{__('Admin')}}</option>
							@php $select=($user->permission == 'U')? 'selected': ''; @endphp
							<option value="U" {{$select}}>{{__('User')}}</option>
					    @endif
						@if(auth()->user()->permission=='A')
							@php $select=($user->permission == 'A')? 'selected': ''; @endphp
						    <option value="A" {{$select}}>{{__('Admin')}}</option>
						    @php $select=($user->permission == 'U')? 'selected': ''; @endphp
						    <option value="U" {{$select}}>{{__('User')}}</option>
						@endif
						@if(auth()->user()->permission=='U')
							<option value="U" selected>{{__('User')}}</option>
						@endif		
					</select>
					@else
						@if($user->type==3)	
						 <select name="permission" class="form-control" disabled>
						   <option value="L" selected>{{__('Guarantor')}}</option>
						 </select>					   
						@endif
					@endif
                </div>
				 
					 @if( (auth()->user()->admin_perm=='O') || 
						 (auth()->user()->permission=='S' && auth()->user()->type==2) )
						
							@if($user->admin_perm !='O')
							<div class="mt-1 col-md-2">
								   @php
									$user_permissions = isset($permission->profile_permissions)?explode(",",$permission->profile_permissions):[];
								   @endphp
								<label for="profiles" class="form-label label-size">{{__('Profile Permission')}}</label>
								<select id="profiles" name="profiles" class="form-control" style="width:100%;">
								   <option value="">{{__('Undefined')}}</option>
									@foreach($profiles as $profile)
									  @php 
										 $access_menus = explode(",",$profile->access_menus);
									  @endphp
									  <option value="{{$profile->id}}" {{(array_diff($access_menus,$user_permissions ) === array_diff($user_permissions,$access_menus) && count($access_menus)==count($user_permissions))? 'selected': ''}}>{{(app()->getLocale()=='en')?$profile->name_en:$profile->name_fr}}
									@endforeach
								</select>							  
							</div>
							@endif
					@endif
					
					@if( (auth()->user()->admin_perm=='O') || 
				     (auth()->user()->permission=='S' && auth()->user()->type==2) )
						@if($user->admin_perm !='O')
						<div class="card  mt-3 col-md-8 border border-success">
							  
							   <div class="text-center card-title">
								 <h5>{{__('All profile permissions')}}</h5>
							   </div>
							   <div class="card-body p-1">
								   <div class="row m-1 mb-2">
									<div class="col-md-4">
									  <input type="checkbox" id="checkAll" name="checkAll" value="0" style="width:1em;height:1em;"/>&#xA0;{{__('Select all')}}
									</div>
								   </div>
								   <div class="m-1 row">
									   
									   @foreach($menus as $m)
										 @if($m=='profile' || $m=='dashboard' || $m=='all_patients' || $m=='lab_requests' || $m=='medical_billings' || $m=='custom_reports'
											 || $m=='general_reports' || $m=='tests_settings' || $m=='all_resources' || $m=='users' || $m=='send_feedback' || $m=='inventory' || $m=='phlebotomy')  
											 <div class="m-1 col-md-12 border border-info"></div>
											  <div class="master_div col-md-12">
												 <input class="menu master" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;" {{(in_array($m,$user_permissions))? 'checked' : '' }}/>&#xA0;<b>{{__($m)}}</b>
											  </div>
										 @else
											   
											   <div class="sub_div col-md-auto" style="margin-left:5px;">
												 <input class="menu sub" type="checkbox" name="menu[]" value="{{$m}}" style="width:1em;height:1em;" {{(in_array($m,$user_permissions))? 'checked' : '' }}/>&#xA0;{{__($m)}}
											   </div>
											  
										 @endif
									   @endforeach
									</div>
							   </div>						
						</div>
				      @endif
				 @endif
				 
				</div>
				  
				  
				
		    </form>
	    </div>
		
</div>


@endsection
@section('scripts')
<script>
$('document').ready(function(){
	
    $('[name=email]').inputmask("email");
	$('.select2').select2({theme:'bootstrap4',width:'resolve'});
   $('.data-select2').select2();
	
	var type=$('#account_type').val();
	//call it to make select all checked or not
	checkCheckAllCheckbox();
	
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
			data: {id:profile_id,type:'profile'},
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
<script>
$(function () {
 	 $('.data-select2').trigger('change.select2');										
  });
</script>
@endsection