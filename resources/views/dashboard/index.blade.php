<!--
    DEV APP
    Created date : 14-7-2022
 -->
@extends('gui.main_gui')

@section('content')
      <div class="mt-2 container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              
                @if(auth()->user()->type==3)
					<div class="inner">
				    <h5><b>{{$lab_info->full_name}}</b></h5>
				    @if(isset($lab_info->full_address) && $lab_info->full_address!='')<div>{{'Address'.' : '.$lab_info->full_address}}</div>@endif
					@if(isset($lab_info->email) && $lab_info->email!='')<div>{{'Email'.' : '.$lab_info->email}}</div>@endif
					@if(isset($lab_info->telephone) && $lab_info->telephone!='')<div>{{'Phone'.' : '.$lab_info->telephone}}</div>@endif
					@if(isset($lab_info->fax) && $lab_info->fax!='')<div>{{'Fax'.' : '.$lab_info->fax}}</div>@endif
					</div>
					@if(UserHelper::can_access(auth()->user(),'profile'))
					<a href="{{route('profiles.clinic',app()->getLocale())}}" onclick="localStorage.clear();" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
			        @endif
				@endif	
				@if(auth()->user()->type==2)
					<div class="inner">
				    <h5><b>{{$lab_info->full_name}}</b></h5>
				    @if(isset($lab_info->full_address) && $lab_info->full_address!='')<div>{{'Address'.' : '.$lab_info->full_address}}</div>@endif
					@if(isset($lab_info->email) && $lab_info->email!='')<div>{{'Email'.' : '.$lab_info->email}}</div>@endif
					@if(isset($lab_info->telephone) && $lab_info->telephone!='')<div>{{'Phone'.' : '.$lab_info->telephone}}</div>@endif
					@if(isset($lab_info->whatsapp) && $lab_info->whatsapp!='')<div>{{'Watsapp'.' : '.$lab_info->whatsapp}}</div>@endif
					@if(isset($lab_info->fax) && $lab_info->fax!='')<div>{{'Fax'.' : '.$lab_info->fax}}</div>@endif
					</div>
					@if(UserHelper::can_access(auth()->user(),'profile'))
					<a href="{{route('profiles.clinic',app()->getLocale())}}" onclick="localStorage.clear();" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
			        @endif
				@endif	
              
              
            </div>
          </div>
          <!-- ./col -->
         
		  <!-- ./col -->
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
               
					<h3>{{$patient_nb}}</h3>
				    <h5>Active Patients</h5>
			    				
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              @if(UserHelper::can_access(auth()->user(),'all_patients'))
			  <a href="{{ route('patientslist.index',app()->getLocale()) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              @endif
			</div>
          </div>
		   <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
               
					<h3>{{$orders_nb}}</h3>
				    <h5>Active Requests</h5>
			    				
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              @if(UserHelper::can_access(auth()->user(),'lab_requests'))
			  <a href="{{ route('lab.visit.index',app()->getLocale()) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              @endif
			</div>
          </div>
		   @if(auth()->user()->type==2)
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                
               
					<h3>{{$doc_nb}}</h3>
				    <h5>Active Doctors</h5>
			   			
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              
			  @if(UserHelper::can_access(auth()->user(),'all_resources'))
			  <a href="{{ route('resources.index',app()->getLocale()) }}" onclick="localStorage.clear();" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              @endif
			</div>
          </div>
		  @endif
		  <!-- ./col -->
		  @if(auth()->user()->type==2)
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
               
					<h3>{{$extlabs_nb}}</h3>
				    <h5>Active Guarantors</h5>
			    				
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              @if(UserHelper::can_access(auth()->user(),'all_resources'))
			  <a href="{{ route('external_labs.index',app()->getLocale()) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              @endif
			</div>
          </div>
		  <!-- ./col -->
		  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
               
					<h3>{{$extins_nb}}</h3>
				    <h5>Active Referred Labs</h5>
			    				
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              @if(UserHelper::can_access(auth()->user(),'all_resources'))
			  <a href="{{ route('external_insurance.index',app()->getLocale()) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              @endif
			</div>
          </div>
		  @endif
		  <!-- ./col -->
        </div>
        <!-- /.row -->
       
      </div><!-- /.container-fluid -->
	
@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('body').find('.main-footer').show();
});
</script>
@endsection
 

