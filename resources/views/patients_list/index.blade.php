<!--
    DEV APP
    Created date : 14-7-2022
 -->
@extends('gui.main_gui')
@section("styles")
<style>
   html, body {
	  margin: 0;
	  padding: 0;
	 
    }
	.dt-button {
            background-color: #f0f0f0; 
            position: relative;
            display: inline-block;
            box-sizing: border-box;
		    margin-left: 0.167em;
		    margin-right: 0.167em;
		    margin-bottom: 0.333em;
		    padding: 0.5em 1em;
		    border: 1px solid rgba(0, 0, 0, 0.3);
		    border-radius: 2px;
		    cursor: pointer;
		    font-size: 0.88em;
		    line-height: 1.6em;
		    color: inherit;
		    white-space: nowrap;
		    overflow: hidden;
		    background-color: rgba(0, 0, 0, 0.1);
		    background: linear-gradient(to bottom, rgba(230, 230, 230, 0.1) 0%, rgba(0, 0, 0, 0.1) 100%);
		   -webkit-user-select: none;
		   -moz-user-select: none;
		   -ms-user-select: none;
		   user-select: none;
		   text-decoration: none;
		   outline: none;
           text-overflow: ellipsis;
        }
		
		
</style>

@endsection
@section('content')
@php 
$access_send_results = UserHelper::can_access(auth()->user(),'send_results');
$access_validate_results = UserHelper::can_access(auth()->user(),'validate_results');
@endphp

    <!-- begin:: Container -->
        <div class="container-fluid mt-1">
				  
				   
				   <div class='card'>
					<div class="card-header card-menu">
					  <div class="row m-1">
					  <div class="col-md-12 mb-2">
					       @include('layouts.partials.messages')
				       </div>
					  <div class="col-md-4">
					    		<input type="text" class="form-control" value="{{$clinic->full_name}}" disabled/>
						</div>
						<div class="col-md-4">
						  @php $disabled = auth()->user()->type!=3?'':'disabled'; @endphp
					    		<select class="select2_data custom-select rounded-0" id="ext_lab" name="ext_lab" style="width:100%;" {{$disabled}}>
									@if(auth()->user()->type!=3)
									<option value="0">Guarantors</option>
									@endif
									@foreach ($ext_labs as $d)
									<option value="{{$d->id}}">{{$d->full_name}}</option>
									@endforeach
						
								</select>
						</div>
						<div class="col-md-4">
						  @php $disabled = auth()->user()->type!=1?'':'disabled'; @endphp
					    		<select class="select2_data custom-select rounded-0" id="doctor_num" name="doctor_num" style="width:100%;" {{$disabled}}>
									@if(auth()->user()->type!=1)
									<option value="0">Doctors</option>
									@endif
									@foreach ($doctors as $d)
									<option value="{{$d->id}}">{{$d->full_name}}</option>
									@endforeach
						
								</select>
						</div>
						 
						</div>
					</div>
					<div class="card-body p-0">
					   <div class="row m-1">
			  		       <div class="col-md-4">
								<select id="filter_patient" name="filter_patient" class="select2_data custom-select rounded-0" style="width:100%;">
								  <option value="0">{{__('Choose a patient')}}</option>
								  @foreach($patients_list as $p)
								   @php $mname = isset($p->middle_name) && $p->middle_name!=''?' '.$p->middle_name:''; @endphp
								   <option value="{{$p->id}}">{{$p->first_name.$mname.' '.$p->last_name}}</option>
								  @endforeach
								</select>
						  </div>
						 <div class="col-md-2">
								<select id="selectStatus" name="status" class="custom-select rounded-0">
								<option value="O">{{__('Active')}}</option>
								<option value="N">{{__('InActive')}}</option>
								</select>
						  </div>
						  <div class="col-md-3">
								<button type="button" id="download-xlsx" class="btn btn-secondary dt-button">Excel</button>
								<button type="button" id="download-csv" class="btn btn-secondary  dt-button">CSV</button>
								<button type="button" id="print-table" class="btn btn-secondary dt-button">Print</button>
							</div>

						   <div class="col-md-3">
						    <a href="{{ route('patientslist.create',app()->getLocale()) }}" class="m-1 btn btn-action btn-sm float-right">{{__('Create')}}<i class="ml-1 fa fa-plus"></i></a>
				           </div>
					   </div>
					   <div class="row m-1">
							
							<div class="col-md-12" style="font-size:0.9em;">
							  <div id="patients_table" class="table-bordered table-sm"></div>
							
						   </div>
                       </div>
					</div> 
                 </div>
              
    </div>
               
   <!--include patient card modal-->
@include('patients_list.patientCardModal')	
@include('patients_list.dashboard.NewVisitModal')		
	
@endsection
 

@section('scripts')
  
<script>
$(function(){
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
    
    var table = new Tabulator("#patients_table", {
	 persistenceMode:"cookie",
	 persistence:{
        sort: true, 
        filter: true, 
        headerFilter: true, 
        group: false, 
        page: true, 
        columns: false, 
      },
	 ajaxURL:"{{ route('patientslist.index',app()->getLocale())}}", //ajax URL
     ajaxParams: function(){
        
		var filter_patient=$('#filter_patient').val();
		var status = $('#selectStatus').val();
		var ext_lab = $('#ext_lab').val();
		var doctor_num = $('#doctor_num').val();
	   	return {filter_patient:filter_patient,status:status,ext_lab:ext_lab,doctor_num:doctor_num};
           },
	height:"400px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:true, //enable pagination.
	paginationSize:10,
    paginationSizeSelector:[50,75, 100, true],
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
    columns:[
		{title:"{{__('File Nb.')}}", field:"file_nb",headerFilter:"input"},
		{title:"{{__('First Name')}}", field:"pat_fname",headerFilter:"input"},
		{title:"{{__('Middle Name')}}", field:"middle_name",headerFilter:"input"},
		{title:"{{__('Last Name')}}", field:"last_name",headerFilter:"input"},
		{title:"{{__('DOB')}}", field:"birthdate",headerFilter:"input"},
		{title:"{{__('Guarantor')}}", field:"ext_lab",headerFilter:"input"},
		{title:"{{__('Doctor')}}", field:"doctor_name",headerFilter:"input"},
		{title:"{{__('Email')}}", field:"email",headerFilter:"input"},
		{title:"{{__('Cellular')}}", field:"cell_phone",headerFilter:"input"},
	    {title:"{{__('Actions')}}", field:"status",frozen:true,
	       formatter:function(cell, formatterParams, onRendered){
		       var data = cell.getValue();
			   var row = cell.getRow().getData();
			   var url = '{{route("patientslist.edit",[app()->getLocale(),":id"])}}';
			   url = url.replace(":id",row.id);
			   var checked = (data=='O')? 'checked':'';
			   var disabled = (data=='O')?'':'disabled';
			   var btn1 = '<a href="'+url+'"  class="btn btn-md btn-clean btn-icon '+disabled+'"><i  class="far fa-edit text-primary" title="{{__("edit")}}"></i></a>';
		       var btn2 = '<label class="mt-2 slideon slideon-xs  slideon-success"><input type="checkbox" data-id="'+row.id+'" class="toggle-chk" '+checked+'><span class="slideon-slider"></span></label>';
               //var btn3 = '<a href="javascript:void(0)"  data-name="'+row.patName+'" class="pat-card btn btn-md btn-clean btn-icon '+disabled+'" data-id="'+row.id+'"><i  class="far fa-address-card text-primary" title="{{__("view results")}}"></i></a>';
			   //var btn4 = '<a href="javascript:void(0)" onclick="event.preventDefault();openVisitModal('+row.id+','+row.lab_num+',\''+row.patName+'\');" class="pat-visit btn btn-md btn-clean btn-icon '+disabled+'"><i  class="far fa-eye text-primary" title="{{__("view lab requests")}}"></i></a>';
			   //var btn5='<a id="new_pat_order" target="_blank" href="{{route("lab.visit.edit",app()->getLocale())}}" class="btn btn-md btn-clean btn-icon '+disabled+'" onclick="new_request('+row.id+','+row.clinic_num+');" title="{{__("New lab request")}}"><i class="fa fa-plus text-primary"></i></a>';
			   return '<div class="inline-block">'+btn1+btn2+'</div>';
		}
	   },

		
      ],
    });
	
	 document.getElementById("download-xlsx").addEventListener("click", function(){
       table.download("xlsx", "patient_list.xlsx", {sheetName:"Patients"});
    });


	document.getElementById("download-csv").addEventListener("click", function(){
       table.download("csv", "patient_list.csv");
     });

  
   document.getElementById("print-table").addEventListener("click", function(){
              table.print(false, true); 
      });
	

   function showProgress(id) {
        document.getElementById(id).style.display = "block";
    }

    function hideProgress(id) {
        document.getElementById(id).style.display = "none";
    }
   

	
    $('#selectStatus,#ext_lab,#filter_patient,#doctor_num').change(function(){
		
		var filter_patient=$('#filter_patient').val();
		var status = $('#selectStatus').val();
		var ext_lab = $('#ext_lab').val();
		var doctor_num = $('#doctor_num').val();
        table.setData("{{route('patientslist.index',app()->getLocale())}}", {filter_patient:filter_patient,status:status,ext_lab:ext_lab,doctor_num:doctor_num});		
		});
	
	
	$('body').on('change','.toggle-chk',function(e){
		e.preventDefault();
		var id =$(this).data("id");
		
		var checked = $(this).is(':checked');
		var url = (!checked)?'{{route("patient.inactivate",app()->getLocale())}}':'{{route("patient.activate",app()->getLocale())}}';
	    //alert(url);
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });
				$.ajax({
						 url: url,
						 data: {id:id},
						 type: 'post',
						 dataType: 'json',
						 success: function(data){
								
								Swal.fire({title:data.msg,icon:"success",toast:true,showConfirmButton:false,position:"bottom-right",timer: 3000});
								
								var filter_patient=$('#filter_patient').val();
								var status = $('#selectStatus').val();
								var ext_lab = $('#ext_lab').val();
								table.setData("{{route('patientslist.index',app()->getLocale())}}", {filter_patient:filter_patient,status:status,ext_lab:ext_lab});	
							 }
						});
	            });
 
 var table_visits='';	       
 $('#NewVisitModal').on('show.bs.modal',function(){  
       
	   $('#NewVisitModal').find('#filter_fromdate').flatpickr({
			allowInput: true,
			enableTime: false,
			dateFormat: "Y-m-d",
			disableMobile: true
		});
		
		var min_date = $('#NewVisitModal').find('#filter_fromdate').val();
		$('#NewVisitModal').find('#filter_todate').flatpickr({
			allowInput: true,
			enableTime: false,
			minDate: min_date,
			dateFormat: "Y-m-d",
			disableMobile: true
		});
         		
	table_visits = new Tabulator(".all_visit", {
		 
	 ajaxURL:"{{ route('patient.getPatVisits',app()->getLocale())}}", //ajax URL
     ajaxParams: function(){
       return {
		       _token:'{{csrf_token()}}',
	            id:$('#NewVisitModal').find('#pat_id').val(),
	            filter_fromdate:$('#filter_fromdate').val(),
			    filter_todate:$('#filter_todate').val()
			   };
           },
	 ajaxConfig:"POST", 	   
	 height:320,
	 placeholder:"No Data Available", //display message to user on empty table
	 placeholderHeaderFilter:"No Matching Data",
	 pagination:true, //enable pagination.
	 paginationSize:5,
     paginationSizeSelector:[5,10,25, 50, 100, true],
	 paginationCounter:"rows",
	 layout:"fitData",
	 layoutColumnsOnNewData:true,
	 movableRows:false,
     columns:[
		{title:"#", field:"id"},
		{title:"{{__('Date')}}", field:"visit_date",headerFilter:"input"},
		{title:"{{__('Time')}}", field:"visit_time",headerFilter:"input"},
		{title:"{{__('Order Status')}}", field:"order_status",headerFilter:"input"},
		{title:"{{__('Action')}}", field:"status",frozen:true,
	     formatter:function(cell, formatterParams, onRendered){
		       var data = cell.getValue();
			    var row = cell.getRow().getData();
			   var access_edit = '{{UserHelper::can_access(auth()->user(),"edit_request")}}';
			   var btn= '<button   class="btn btn-md btn-clean btn-icon" title="Download Result" onclick="event.preventDefault();printPDF('+row.id+')"><i class="fa fa-file-pdf text-primary"></i></button>';

			   var access_send_results = '{{$access_send_results}}';
			   if(access_send_results && row.order_status=='{{__("Validated")}}'){
		         btn+= '<button  class="btn btn-md btn-clean btn-icon" title="Email Result" onclick="event.preventDefault();sendPDF('+row.id+')"><i class="fa fa-envelope text-primary"></i></button>';
		          }
				  
			   if(access_edit){
			   var url = '{{route("lab.visit.edit",[app()->getLocale(),":id"])}}';
			   url = url.replace(":id",data);
			    btn+='<a href="'+url+'"  target="_blank"  onclick="$(\'#NewVisitModal\').modal(\'hide\');" class="btn btn-md btn-clean btn-icon"><i  class="far fa-edit text-primary" title="{{__("edit")}}"></i></a>';
			   }
			   
			   
			   return btn;
		}
	   },

      ],
    });
		
});


$('#filter_fromdate').change(function(){
	var from_date = $(this).val();
	if(from_date!=null && from_date!=''){
	$('#NewVisitModal').find('#filter_todate').flatpickr().destroy();	
	$('#NewVisitModal').find('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		minDate: from_date ,
        disableMobile: true
	});
	}else{
       $('#NewVisitModal').find('#filter_todate').flatpickr().destroy();	
	   $('#NewVisitModal').find('#filter_todate').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		disableMobile: true
	   });
	}
 table_visits.setData("{{route('patient.getPatVisits',app()->getLocale())}}",{_token:'{{csrf_token()}}',id:$('#NewVisitModal').find('#pat_id').val(),filter_fromdate:$('#filter_fromdate').val(),filter_todate:$('#filter_todate').val()});	

});

$('#filter_todate').change(function(){
	 table_visits.setData("{{route('patient.getPatVisits',app()->getLocale())}}",{_token:'{{csrf_token()}}',id:$('#NewVisitModal').find('#pat_id').val(),filter_fromdate:$('#filter_fromdate').val(),filter_todate:$('#filter_todate').val()});	

});	


/*var table1='';	
$('body').on('click','.pat-card',function(e){
  e.preventDefault();
  var cnt=0;
  var patient_id = $(this).data("id");
  var patient_data = $(this).data("name");
  
  //table for patient results
     table1 = new Tabulator("#pat_results_table", {
     
	 ajaxURL:"{{ route('patient.getPatResults',app()->getLocale())}}", //ajax URL
     ajaxParams: function(){
       return {_token:'{{csrf_token()}}',patient_id:patient_id};
           },
	ajaxConfig:"POST", 	   
	height:"400px",
	groupBy:"visit_data",
	groupStartOpen:function(value, count, data, group){
    //value - the value all members of this group share
    //count - the number of rows in this group
    //data - an array of all the row data objects in this group
    //group - the group component for the group
      if(count && data[0].order_status!='V'){  cnt++;  }
	  $('#cnt_nonvalid').text(cnt);
     return false; //all groups with more than three rows start open, any with three or less start closed
      },
	groupHeader: function(value, count, data, group){
		var btn1 = '<span style="margin-left:10px;"><button   class="btn btn-md btn-clean btn-icon" title="Download Result" onclick="printPDF('+data[0].order_id+')"><i class="fa fa-file-pdf text-primary"></i></button></span>';
        var btn2 = '';
		var access_send_results = '{{$access_send_results}}';
		var access_validate_results = '{{$access_validate_results}}';
		if(data[0].order_status=='V' && access_send_results ){
		  btn2 = '<span style="margin-left:10px;"><button  class="btn btn-md btn-clean btn-icon" title="Email Result" onclick="sendPDF('+data[0].order_id+')"><i class="fa fa-envelope text-primary"></i></button></span>';
		}
		
		var btn3 ='';
		if(access_validate_results){
			if(data[0].order_status!='V'){
			 btn3 = '<span style="margin-left:40px;"><input type="radio" class="validate" data-id="'+data[0].order_id+'" data-patid="'+patient_id+'"/><label class="ml-2 form-check-label">Not Validated</label></span>';
			}else{
			 btn3 = '<span style="margin-left:40px;"><input type="radio" checked /><label class="ml-2 form-check-label">Validated</label></span>';
			}
		}
		
		return value + '<span style="color:#d00; margin-left:10px;">(' + count + ')</span>'+btn1+btn2+btn3;
    },
	placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:true, //enable pagination.
	paginationSize:25,
    paginationSizeSelector:[25, 50, 100, true],
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
    columns:[
		{title:"#", field:"order_id",visible:false},
		{title:"status", field:"order_status",visible:false},
		{title:"Visit", field:"visit_data",visible:false},
		{title:"{{__('Group')}}", field:"group_name",headerFilter:"input"},
		{title:"{{__('Name')}}", field:"test_name",headerFilter:"input"},
		{title:"{{__('Result')}}", field:"result",headerFilter:"input"},
		{title:"{{__('Unit')}}", field:"unit",headerFilter:"input"},
		{title:"{{__('Normal Range')}}", field:"range_val",headerFilter:"input"},
      ],
    });
  
  if(cnt==0) $('#cnt_nonvalid').text(cnt);	
    
  $('#patientCardModal').modal("show");
});*/  
  
/*$('#patientCardModal').on('click','.validate',function(e){
	e.preventDefault();
	var order_id = $(this).data("id");
	var patient_id = $(this).data("patid");
	$.ajax({
	     url: '{{route("patient.validatePatResults",app()->getLocale())}}',
		 data: {_token:'{{csrf_token()}}',order_id:order_id},
		 type: 'post',
		 dataType: 'json',
		 success: function(data){
			Swal.fire({title:data.msg,icon:"success",toast:true,showConfirmButton:false,position:"bottom-right",timer: 3000});
			table1.setData("{{route('patient.getPatResults',app()->getLocale())}}", {_token:'{{csrf_token()}}',patient_id:patient_id},"POST");	
			}
	     });	
});*/  
  
  
  
});
  
</script>
<script>
function printPDF(order_id){
	$.ajax({
			url:'{{route("lab.visit.printResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download=('result.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
}

function sendPDF(order_id){
	$.ajax({
			url:'{{route("lab.visit.sendResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		   dataType:'json',
		   success: function(data){
					if(data.success){	
						Swal.fire({title:data.success,icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.warn){	
						Swal.fire({title:data.warn,icon:"warning",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.error){
						 Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
					 }				   
				}
		    });
}


function openVisitModal(id,branch,name){
	$('#NewVisitModal').find('#pat_id').val(id);
	$('#NewVisitModal').find('#pat_branch').val(branch);
	$('#NewVisitModal').find('#patient_name').val(name);	  
	$('#NewVisitModal').modal('show');
	
	
}

function new_request(patient_num,clinic_num){
	
	
	$.ajax({
		  url: '{{route("patient.new_visit",app()->getLocale())}}',
		  method:'POST',
		  data: {_token: '{{ csrf_token() }}',patient_num:patient_num,clinic_num:clinic_num},
          dataType:'JSON',
          success: function(data){
			  //$('#NewVisitModal').modal('hide');
			  //$('#NewVisitModal').find('#new_pat_order').prop('href',data.location);
			  //window.location.href=data.location;
		  }		  
		});
}

function new_visit(){
	
	var patient_num = $('#NewVisitModal').find('#pat_id').val();
	var clinic_num = $('#NewVisitModal').find('#pat_branch').val();
	
	$.ajax({
		  url: '{{route("patient.new_visit",app()->getLocale())}}',
		  method:'POST',
		  data: {_token: '{{ csrf_token() }}',patient_num:patient_num,clinic_num:clinic_num},
          dataType:'JSON',
          success: function(data){
			  $('#NewVisitModal').modal('hide');
			  //$('#NewVisitModal').find('#new_pat_order').prop('href',data.location);
			  //window.location.href=data.location;
		  }		  
		});
}

</script>

@endsection
