<!--
  DEV APP
  Created date : 6-1-2023
-->
@extends('gui.main_gui')
@section("styles")
<style>
   .modal-header {
	     cursor: move;
        }
    
	.modal-content{
    -webkit-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -moz-box-shadow: 0 5px 15px rgba(0,0,0,0);
    -o-box-shadow: 0 5px 15px rgba(0,0,0,0);
    box-shadow: 0 5px 15px rgba(0,0,0,0);
     }
</style>
@endsection
@section('content')

  

    <!-- begin:: Container -->
        <div class="container-fluid">
              
				<div class="row m-1">
				   
				   <div class="col-md-3 col-6">
					  <select id="selectStatus" name="status" class="custom-select rounded-0">
					   <option value="O">{{__('Active')}}</option>
					   <option value="N">{{__('InActive')}}</option>
					  </select>
			       </div>
				   @if($user_admin_perm=='O' || ($user_type=='2' && ($user_perm=='S' || $user_perm=='A')))  	
				   <div class="col-md-9 col-6">			   
					  <a href="{{ route('branches.create',app()->getLocale()) }}" class="btn btn-action btn-sm float-right">{{__('Create new lab')}}</a>
				  </div>
				  @endif
				   <div class="mt-2 col-md-12">
						@include('layouts.partials.messages')
				   </div>
				</div>
                <div class="card row mt-2">                   
				   <div class="m-1 col-md-12">
				   <!--begin: Datatable -->
                    <table id="clinics_table" class="display compact stripe cell-border nowrap" style="width:100%;">
                        <thead>
                        <tr>                       
							<th>{{__('#')}}</th>
							<th class="my_type">{{__('Lab Type')}}</th>
							<th>{{__('Complete Name')}}</th>
							<th>{{__('Price$')}}</th>
							<th>{{__('PriceLBP')}}</th>
                            <th>{{__('Address')}}</th>
							<th>{{__('Contact Phone')}}</th>
							<th>{{__('Alternate Phone1')}}</th>
							<th>{{__('Alternate Phone2')}}</th>
							<th>{{__('Email')}}</th>
							<th>{{__('Fax Nb')}}</th>
							<th class="my_status">{{__('Status')}}</th>
							<th>{{__('Actions')}}</th>
                        </tr>
                        </thead>
                    </table>
					</div>
                </div> 
		  @include('profile.branch.showProfileModal')
@endsection

@section('scripts')
<script>
 $(document).ready(function() {  
	   $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 
	   
	   var table= $('#clinics_table').DataTable({
           stateSave: true,
		   stateDuration: -1,
		   paging: true,
           searching: true,
           ordering: true,
           info: true,
		   pageLength: 50,
		   lengthMenu: [
			[50, 75, 100, -1],
			[50, 75, 100, 'All']
			 ],
		   scrollY:450,
	       scrollX:true,
	       scrollCollapse: true,
	       processing: true,
		   serverSide: false, // Use false for client-side processing
		   ajax: {
					  url: '{{route("get_branches",app()->getLocale())}}',
					  type: 'POST',
					  data: function ( d ) {
						d.branch_status= $("#selectStatus").val();
					  },
					  dataSrc: 'data' 
					},
		    columns:[
					   {data:'cnt'},
					   {data:'kind'},
					   {data:'name'},
					   {data:'priced'},
					   {data:'pricel'},
					   {data:'address'},
					   {data:'tel'},
					   {data:'tel1'},
					   {data:'tel2'},
					   {data:'email'},
					   {data:'fax'},
					   {data:'status'},
					   {data:'actions',orderable:false,searchable:false}
				   ],
		   language: {
			    search:         "{{__('Search')}}&nbsp;:",
				lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
				info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
				infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
				emptyTable:  "{{__('No data is found')}}",
				zeroRecords: "{{__('No data is found')}}",
				buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
						
								},
				paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				},
		   fixedColumns:   {
							left : 0,
							right: 1
						
						},
			fixedHeader:{
							header: true,
							footer: true
						}
       });
	   
	    //var selectedValue=$("#selectStatus").val();
		
		//table.columns('.my_status').search("^" + selectedValue + "$", true, false).draw();
	   
	   
	   $('#selectStatus').on('change', function(e){
		
		//var selectedValue=$("#selectStatus").val();
		
		//table.columns('.my_status').search("^" + selectedValue + "$", true, false).draw();
          table.ajax.reload();
		});
		
		
		
		$('body').on('change','.toggle-chk',function(e){
			var id = $(this).data("id");
			var url = '{{route("branches.destroy",[app()->getLocale(),":id"])}}';
			url = url.replace(":id",id);
			//alert(id);
			e.preventDefault();
			var checked = ($(this).is(':checked'))?'O':'N';
			//alert(checked);
			$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
				$.ajax({
						 url: url,
						 data: {checked:checked},
						 type: 'delete',
						 dataType: 'json',
						 success: function(data){
								//window.location.href=data.url;
								table.ajax.reload();
							 }
						});
		});
	 
	 });
	 
</script>
<script>
     $('.movableDialog').draggable({
       handle: ".modal-header"
       });
</script>
<script>
function showProfile(clinic_num){
		$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						   }
					   });
			  $.ajax({
					type : 'POST',
					url : '{{route("profiles.clinic.show_profile",app()->getLocale())}}',
					data : { clinic_num :clinic_num},
					dataType: 'JSON',
					success: function(data){
						  
						  $('#showProfileModal div.modal-body').find('#persos').html(data.html_persos);
						  $('#showProfileModal div.modal-body').find('#schedule').html(data.html_schedule);
						  //$('#showProfileModal div.modal-body').find('#doctors').html(data.html_doctors);
						  //$('#showProfileModal div.modal-body').find('#exams').html(data.html_exams);
						 


					}
		          });
	
}
</script>
 
@endsection
