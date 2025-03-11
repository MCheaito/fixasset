<!-- 
 DEV APP
 Created date : 15-12-2022
-->
@extends('gui.main_gui')

@section('content')
    
      <div class="container-fluid">
 	    <div class="row m-1">
		    <div class="col-md-12">
				
					@include('layouts.partials.messages')
				
			</div>
			@if( ($user_type==2 && $user_perm=='U') || 
			     ($user_type==3 && $user_perm=='L') || 
				 ($user_type==1 && $user_perm=='D') || 
				 ($user_type==4 && $user_perm=='P'))
			<div class="col-md-4 m-1"></div>
			@else
			<div class="col-md-4">
				  <select id="selectStatus" name="status" class="custom-select rounded-0">
				   <option value="">{{__('Active\InActive')}}</option>
				   <option value="O">{{__('Active')}}</option>
				   <option value="N">{{__('InActive')}}</option>
				  </select>
			</div>
			<div class="col-md-4">
			       <select id="selectType" name="type" class="custom-select rounded-0">
				   <option value="">{{__('Account types')}}</option>
				   <option value="2">{{__('Internal Lab')}}</option>
				   <option value="3">{{__('Guarantor')}}</option>
				  </select>
		
			</div>
			@if( ($user_type==2 && $user_perm=='S') || $user_admin_perm=='O')
			<div class="col-md-4">
			 <button data-toggle="modal" data-target="#accountTypeModal" class="float-right btn btn-sm btn-action">{{__('Create')}}<i class="ml-1 fa fa-plus"></i></button>
			</div>
			@endif
		  @endif
           
		 </div>
   
   <div class="card">
	 <div class="row m-1">
	 <div class="col-md-12">
		<table id="my_table" class="display compact stripe cell-border nowrap" style="width:100%;">
            <thead>
            <tr>
                
				<th>{{__('#')}}</th>
				<th>{{__('Account ID')}}</th>
				<th>{{__('Complete Name')}}</th>
				<th class="my_type">{{__('Account Type')}}</th>
				<th>{{__('Email')}}</th>
				<th>{{__('Access Permission')}}</th>
				<th  class="my_status">{{__('Status')}}</th>
		        <th>{{__('Actions')}}</th>
				                		
            </tr>
            </thead>
         
        </table>
       </div>
	   </div>
	  </div> 
        
     </div>
@include('users_list.accountTypeModal')	 
@endsection

@section('scripts')
<script>
$(document).ready(function() { 
	   var selectedType = localStorage.getItem("selectedType");
		 $("#selectType").val(selectedType);
	   
	   $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 
	   
	   var table=$("#my_table").DataTable({
		          
				  order: [[0, "desc"]],
				  scrollY: "400px",
			      scrollX: true,
			      scrollCollapse:true,
				  processing: true,
				  serverSide: false, // Use false for client-side processing
				  ajax: {
					  url: '{{route("get_users",app()->getLocale())}}',
					  type: 'POST',
					  data: function ( d ) {
						d.user_type= $("#selectType").val();
						d.user_status= $("#selectStatus").val();
					  },
					  dataSrc: 'data' 
					},
				  columns:[
					   {data:'cnt'},
					   {data:'username'},
					   {data:'name'},
					   {data:'usertype'},
					   {data:'email'},
					   {data:'userpermission'},
					   {data:'status'},
					   {data:'actions',orderable:false,searchable:false}
				   ],
		          language :{
					       
							search:         "{{__('Search')}}&nbsp;:",
							lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
							info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
							infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
							zeroRecords:    "{{__('No data is found')}}",
							emptyTable:     "{{__('No data is found')}}",
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
			
			fixedHeader: {
						header: true,
						footer: true
					}		 
										 
                         
				});
				
	     
		 
		 
		 /*if(selectedType!=null && selectedType!=''){
		     $("#selectType").val(selectedType);
			 table.columns('.my_type').search("^" + selectedType + "$", true, false).draw();
         }else{
			 table.columns('.my_status').search('').draw();
		 }*/
		
		$('#selectStatus').on('change', function(e){
			/*var selectedValue=$("#selectStatus").val();
		    if(selectedValue!='')
		     table.columns('.my_status').search("^" + selectedValue + "$", true, false).draw();
            else
             table.columns('.my_status').search(selectedValue).draw();*/
             table.ajax.reload();  		 
		});
		
		$('#selectType').on('change', function(e){
		 var selectedValue=$(this).val();
		 localStorage.setItem("selectedType",selectedValue);		
		/*if(selectedValue!=''){
		  table.columns('.my_type').search("^" + selectedValue + "$", true, false).draw();
		 }else{
         table.columns('.my_type').search(selectedValue).draw();
		 }*/
		 table.ajax.reload();
		});
		
		$('body').on('change','.toggle-chk',function(e){
			var id = $(this).data("id");
			var url = '{{route("userslist.destroy",[app()->getLocale(),":id"])}}';
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
	
	$('#accountTypeModal').on('shown.bs.modal',function(){
		$('#create_new_user').click(function(e){
			e.preventDefault();
			var type = $('#accountTypeModal').find('#acc_type').val();
			
			switch(type){
				case '1':
				window.location.href="{{route('userslist.create',app()->getLocale())}}";
				 $('#accountTypeModal').modal("hide");
				break;
				case '2':
				 window.location.href="{{route('userslist.create_lab',app()->getLocale())}}";
				 $('#accountTypeModal').modal("hide");
				break;
			}
		});
	});
	
	});
</script>


@endsection