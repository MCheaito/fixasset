<!--
 DEV APP
 Created date : 8-11-2022
 to be done
-->
@extends('gui.main_gui')

@section('content')	
<style>
.text-wrap{
    white-space:normal;
}
.width-400{
    width:400px;
}
</style>
	<div class="container">
	   <div class="row">
	     <div class="col-lg-12 margin-tb">
			<div class="pull-left">
			<h2>{{__('Audit')}}</h2>
			</div>
			
	     </div>
	   </div>
    <div class='row m-1 mt-2 mb-2'>
	
	<div class='col-md-2'>
	    <label class="badge badge-info inputLabel" style="font-size:14px;">{{__('From')}}</label>
		 <input type="text" class="form-control" name="fromdate" id="fromdate"
                                               value="{{date('Y-m-01')}}">
		</div>
		<div class='col-md-2'>
	    <label class="badge badge-info inputLabel" style="font-size:14px;">{{__('To')}}</label>
		 <input type="text" class="form-control" name="todate" id="todate"
                                               value="{{date('Y-m-d')}}">
		</div>
	    <div class='col-md-3'>
		  <label class="badge badge-info inputLabel" style="font-size:14px;">{{__('File')}}</label>
          <select class="custom-select rounded-0" id="selectFile" name="selectFile">
		 	<option value="Request">{{__('Request')}}</option>
			<option value="Report">{{__('Report')}}</option>
			<option value="Patient">{{__('Patient')}}</option>
			<option value="Professional">{{__('Professional')}}</option>
			<option value="Professional_speciality">{{__('Professional_speciality')}}</option>
			<option value="Facility">{{__('Clinic')}}</option>
			<option value="Facility_speciality">{{__('Clinic_speciality')}}</option>
			<option value="Facility_access">{{__('Clinic_access')}}</option>
			<option value="Request_confirmation">{{__('Request_confirmation')}}</option>			
			<option value="Request_attachment">{{__('Request_attachment')}}</option>
			<option value="Request_notes">{{__('Request_notes')}}</option>
			<option value="Request_reports">{{__('Request_reports')}}</option>
			<option value="Reports_attachment">{{__('Reports_attachment')}}</option>
			
		   </select>
		</div>
		
	    <div class='col-md-2'>
		 <label class="badge badge-info inputLabel" style="font-size:14px;">{{__('Status')}}</label>
		   <select class="custom-select rounded-0" id="selectStatus">
		      <option value="0">{{__("All")}}</option>
			 <option value="create">{{__('Create')}}</option>
			 <option value="update">{{__('Update')}}</option>
			 <option value="delete">{{__('Delete')}}</option>
			 
  	   </select>
        </div>
		 <div class='col-md-1 mt-4 '>
		  <input type="button" value="{{__('Filter') }}"  id="btn-filter" class="btn btn-success">	
		</div>		  
	</div>
	 
	
	<div class="row m-1 mt-2">
	   <div class="col-12" id="table_audit">
	     
	</div>
   </div>
</div>  

@endsection	

@section('scripts')

<script>
	 $(function () { 
	var table= $("#audit_list_table").DataTable({
		order: [['5','desc'],['0','desc']],
		scrollY:350,
		scrollX:true,
		scrollCollapse:true,
		lengthMenu: [50,75,100],
		language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty:     "{{__('Showing')}} 0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
					paginate: {
									first:      "{{__('First')}}",
									previous:   "{{__('Previous')}}",
									next:       "{{__('Next')}}",
									last:       "{{__('Last')}}"
								}
			         }
		
                  });
	var selectedValueFile=$("#selectFile").val();
		var selectedValuefdate=$("#fromdate").val();
		var selectedValuetdate=$("#todate").val();
		var selectedValueStatus=$("#selectStatus").val();
	//	alert("ok");
		
		 $.ajax({
		        url:'fillFile?filterFile='+selectedValueFile+'&filterfdate='+selectedValuefdate+'&filtertdate='+selectedValuetdate+'&filterStatus='+selectedValueStatus,
		        type: 'get',
           dataType: 'json',
           success: function(data){
		 $('#table_audit').html(data.html); 
		var table= $("#audit_list_table").DataTable({
			order: [['5','desc'],['0','desc']],
			scrollY:400,
			scrollX:true,
			scrollCollapse:true,
			lengthMenu: [50,75,100],
			language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty:     "{{__('Showing')}} 0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
					paginate: {
									first:      "{{__('First')}}",
									previous:   "{{__('Previous')}}",
									next:       "{{__('Next')}}",
									last:       "{{__('Last')}}"
								}
			         },
		
		    columnDefs: [
                {
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-400'>" + data + "</div>";
                    },
                    targets: 3
                }
             ]
		   });
		   }
		   });
		
	 
	   $('#btn-filter').on('click', function(e){
		var selectedValueFile=$("#selectFile").val();
		var selectedValuefdate=$("#fromdate").val();
		var selectedValuetdate=$("#todate").val();
		var selectedValueStatus=$("#selectStatus").val();
	//	alert("ok");
		
		 $.ajax({
		        url:'fillFile?filterFile='+selectedValueFile+'&filterfdate='+selectedValuefdate+'&filtertdate='+selectedValuetdate+'&filterStatus='+selectedValueStatus,
		        type: 'get',
           dataType: 'json',
           success: function(data){
		 $('#table_audit').html(data.html); 
		var table= $("#audit_list_table").DataTable({
			order: [['5','desc'],['0','desc']],
			scrollY:400,
			scrollX:true,
			scrollCollapse:true,
			lengthMenu: [50,75,100],
			language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty:     "{{__('Showing')}} 0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
					paginate: {
									first:      "{{__('First')}}",
									previous:   "{{__('Previous')}}",
									next:       "{{__('Next')}}",
									last:       "{{__('Last')}}"
								}
			         },
		
		    columnDefs: [
                {
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-400'>" + data + "</div>";
                    },
                    targets: 3
                }
             ]
		   });
		   }
		   });
		
		
		table.columns('.my_file').search(selectedValueFile).draw();
	   });
       
	   $('#selectPeriod').on('change', function(e){
		var selectedValue=$("#selectPeriod").val();
		
		table.columns('.my_date').search(selectedValue).draw();
	   }); 

       $('#selectStatus').on('change', function(e){
		var selectedValue=$("#selectStatus").val();
		table.columns('.my_action').search(selectedValue).draw();
		
 }); 
	 });
</script>
 <script>
        flatpickr('#fromdate', {
            allowInput : true,
			altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d",
        });
		flatpickr('#todate', {
            allowInput : true,
			altInput: true,
            altFormat: "Y-m-d",
            dateFormat: "Y-m-d",
        });
    </script>
@endsection