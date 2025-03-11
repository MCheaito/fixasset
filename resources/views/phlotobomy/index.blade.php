@extends('gui.main_gui')
@section('content')
<div class="container-fluid p-0">
  <div class="row">
    <div class="col-md-2 col-6">
	   <label class="label-size">{{__("From Date")}}</label>
	   <input type="text" id="from_date" name="from_date" class="form-control" value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
	</div>
	<div class="col-md-2 col-6">
		<label for="name" class="label-size">{{__('To date')}}</label>
		<input type="text" class="form-control" name="to_date" id="to_date"  value="{{Carbon\Carbon::now()->format('Y-m-d')}}"/>
	</div>
	<div class="col-md-2 col-6">
	   <label class="label-size">{{__("Has collection")}}</label>
       @if(auth()->user()->type==2)
	   <select class="form-control" name="is_phletobomy"  id="is_phletobomy">
	      <option value="">{{__('Yes/No')}}</option>
		  <option value="N">{{__('No')}}</option>
		  <option value="Y">{{__('Yes')}}</option>
	   </select>
	  @else
		  <select class="form-control" name="is_phletobomy"  id="is_phletobomy" disabled >
		  <option value="Y" selected>{{__('Yes')}}</option>
	   </select>
	  @endif	  
	</div>
	<div class="col-md-3 col-6">
       <label for="select_test" class="label-size ">{{__('Codes')}}</label>
	   <select class="select2_tests form-control" name="select_test"  id="select_test" style="width:100%;">
	   </select>									
	</div>
	<div class="col-md-3 col-6">
       <label for="select_patient" class="label-size ">{{__('Patient')}}</label>
	   <select class="select2_patient form-control" name="select_patient"  id="select_pateint" style="width:100%;">
	   </select>									
	</div>
	
  </div>
  <div class="row mt-1">
     <div class="col-md-12">
	    <div id="phlotobomy_table" class="table-bordered table-sm"></div>
	 </div>
  </div>

</div>
@include('phlotobomy.codesModal')
@endsection
@section('scripts')
<script>
let table,table_codes;
$(function(){
	var currentYear = new Date().getFullYear();
    var maxDate = new Date(currentYear, 11, 31); // Month is zero-based, so 11 represents December
    var maxDateString = maxDate.toISOString().slice(0, 10);
	$('#from_date').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		maxDate: maxDateString,
        disableMobile: true
	});
	var min_date = $('#from_date').val();
	
	$('#to_date').flatpickr({
		allowInput: true,
		enableTime: false,
		minDate: $('#from_date').val(),
		maxDate: maxDateString,
        dateFormat: "Y-m-d",
        disableMobile: true
	});
	
	$('.select2_patient').select2({
			theme: 'bootstrap4',
			width: 'resolve',
			language:'{{app()->getLocale()}}',
			placeholder: "{{__('Choose a patient')}}",
			ajax: {
				url: '{{route("patient.loadPat",app()->getLocale())}}', // Replace with the actual route URL
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						_token: '{{csrf_token()}}',
						clinic_num:'{{$clinic_num}}',
						q: params.term, // user's input
						page: params.page || 1 // current page number
					};
				},
				processResults: function (data) {
					return {
						results: data.results,
						pagination: {
							more: data.pagination.more
						}
					};
				}
				
			},
			allowClear: true
		  });
		  
	 $('.select2_tests').select2({
			theme: 'bootstrap4',
			width: 'resolve',
			language:'{{app()->getLocale()}}',
			placeholder: "{{__('Choose a code')}}",
			ajax: {
				url: '{{route("loadTests",app()->getLocale())}}', // Replace with the actual route URL
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						_token: '{{csrf_token()}}',
						q: params.term, // user's input
						page: params.page || 1 // current page number
					};
				},
				processResults: function (data) {
					return {
						results: data.results,
						pagination: {
							more: data.pagination.more
						}
					};
				}
				
			},
			allowClear: true
		  });
		  
  table = new Tabulator("#phlotobomy_table", {
	 ajaxURL:"{{ route('phlebotomy.index',app()->getLocale())}}", //ajax URL
     ajaxParams: function(){
        var patient=$('#select_pateint').val();
		var code=$('#select_test').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var is_phletobomy = $('#is_phletobomy').val();
		var token = '{{csrf_token()}}';
		return {_token:token,code:code,patient:patient,from_date:from_date,to_date:to_date,is_phletobomy:is_phletobomy};
        },
	height:"500px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:true, //enable pagination.
	paginationSize:50,
    paginationSizeSelector:[50, 75, 100, true],
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	resizableRows:true,
    columns:[
		{title:"{{__('pid')}}", field:"patient_num",visible:false},
		{title:"{{__('#')}}", field:"order_id",visible:false},
		{title:"{{__('Serial#')}}",field:"daily_serial_nb",formatter:function(cell, formatterParams, onRendered){
			var data = cell.getValue();
			var row = cell.getRow().getData();
			var arr = {!!$serialNumbersByDate!!};
			var serialNumbers = arr[data];
			var order_id = cell.getRow().getData().order_id;
			var btn = '';
			 for (var i = 0; i < serialNumbers.length; i++) {
				 if (serialNumbers[i].id === order_id) {
                   	var serial_nb =serialNumbers[i].serial;
					row.hidden_serial_nb = serial_nb;
					var btn='<button type="button" class="p-1 ml-1 btn btn-md btn-clean btn-icon" name="print_serial" id="print_serial" onclick="event.preventDefault();printSerial('+order_id+','+serial_nb+')"><i title="{{__("Print daily serial")}}" class="far fa-file-pdf text-primary"></i></button>';	
					return '<div><b>'+serial_nb+'</b>'+btn+'</div>';
				}
			 }
			
			
			return '';
		}},
		{title:"HideSerial",field:"hidden_serial_nb",visible:false},
		{title:"{{__('Name')}}", field:"patient_name",headerFilter:"input"},
		{title:"{{__('Age')}}", field:"patient_age",headerFilter:"input",formatter:function(cell, formatterParams, onRendered){
			var dob = new Date(cell.getValue());
            var today = new Date();
			var ageInMilliseconds = today - dob;
			var ageInYears = Math.floor(ageInMilliseconds / (1000 * 60 * 60 * 24 * 365));
            var ageInMonths = Math.floor(ageInMilliseconds / (1000 * 60 * 60 * 24 * 30));
            var ageInDays = Math.floor(ageInMilliseconds / (1000 * 60 * 60 * 24));

           if (ageInYears >= 1) {
                return ageInYears + " years";
          }else{ 
		  if (ageInMonths >= 1) {
                return ageInMonths + " months";
          }else {
             return ageInDays + " days";
           }
		  }
			
		}},
		{title:"{{__('Request')}}", field:"order_data",headerFilter:"input",formatter:function(cell, formatterParams, onRendered){
			var data = cell.getValue().split(';');
			var row = cell.getRow().getData();
			var btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon" onclick="openCodesModal('+row.order_id+','+row.hidden_serial_nb+');"><i title="{{__("Add")}}" class="fas fa-plus text-primary"></i></button>';
			var result = '<div>'+data[0]+btn+'</div><div>'+data[1]+'</div>';
			return result;
		}},
		{title:"{{__('Paid')}}",field:"billed",formatter:function(cell, formatterParams, onRendered){
			var data = cell.getValue();
			if(data=='N'){
				    return '<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="{{_("Not Billed")}}" class="fa fa-times text-danger"></i></button>';
			}else{
				if(data=='Y'){
				    return '<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="{{_("Done")}}" class="fa fa-check text-success"></i></button>';	
				}else{
					return '<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="{{_("Not Done")}}" class="fa fa-exclamation-triangle text-warning"></i></button>';
				}
			}
		}},
		{title:"{{__('Collected')}}", field:"blood_collection",formatter:function(cell, formatterParams, onRendered){
			var collection_date = cell.getRow().getData().collection_date;
			var order_id = cell.getRow().getData().order_id;
			var checked = (collection_date!=null &&  collection_date!='')?'checked':'';
            if(collection_date!=null &&  collection_date!=''){
				btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="{{_("Done")}}" class="fa fa-check text-success"></i></button>';
			}else{
			    btn='<button type="button" class="p-1 btn btn-md btn-clean btn-icon"><i title="{{_("Not Done")}}" class="fa fa-times text-danger"></i></button>';
			}
			return btn;
			
		}},
		{title:"{{__('Collection Date')}}", field:"collection_date",headerFilter:"input"},
		{title:"{{__('CheckedSpecimens')}}", field:"chk_specimens",visible:false},
		{title:"{{__('Specimens')}}", field:"tests_specimens", formatter:function(cell, formatterParams, onRendered){
			var row = cell.getRow().getData();
			var data = cell.getValue();
			var btn='';
			if(data!=null){
			var specimen = @json($specimen);
			var chk_specimens = row.chk_specimens;
			var img_src='',title='';
			function isImageURL(url) {
               var imageExtensions = /\.(jpg|jpeg|png|gif)$/i;
               return imageExtensions.test(url);
              }

				if(data.includes(',')){
					var arr =data.split(',');
					var cnt=1;
					$.each( arr, function( index, value ) {
					  var hasValue='N';
					if(chk_specimens!=null && chk_specimens!=''){
					  hasValue = Object.values(chk_specimens).includes(value)?'Y':'N';
					}
					
					var checked = (hasValue=='Y')?'checked':'';
					
					var myspecimen = specimen.find(function(obj) {
                           return obj.id === parseInt(value);
                        });

					  
					  if(myspecimen){
					  img_src = myspecimen.src;
					  title =   myspecimen.name;
					  }
					  
					  
					  
					  if(!isImageURL(img_src)){
					   btn+='<input type="checkbox" '+checked+' value="'+value+'" class="ml-2 specimens" disabled /><label class="ml-1">'+img_src+'</label>';
  					  }else{
					   btn+='<input type="checkbox" '+checked+' value="'+value+'" class="ml-2 specimens" disabled /><label class="ml-1"><img src="'+img_src+'" title="'+title+'" style="height:20px;width:20px;"/></label>';
					  }
					  if(cnt%4==0){ btn+='<br/>';}
					  cnt++;
					});
				}else{
					var hasValue='N';
					if(chk_specimens!=null && chk_specimens!=''){
					  hasValue = Object.values(chk_specimens).includes(data)?'Y':'N';
					}
					
					var checked = (hasValue=='Y')?'checked':'';
                    
					var myspecimen = specimen.find(function(obj) {
                          return obj.id === parseInt(data);
                        });

					 if(myspecimen){ 
					  img_src = myspecimen.src;
					  title =   myspecimen.name;
					   }
					
					  if(!isImageURL(img_src)){
					   btn='<div><input type="checkbox" '+checked+' value="'+data+'" class="ml-2 specimens" disabled /><label class="ml-1">'+img_src+'</label></div>';
  					  }else{
					   btn='<div><input type="checkbox" '+checked+' value="'+data+'" class="ml-2 specimens" disabled /><label class="ml-1"><img src="'+img_src+'" title="'+title+'" style="height:20px;width:20px;"/></label></div>';
					  }				
				 }
			
		       return btn;
			}else{
				return data;
			}
		 }},
		{title:"{{__('CheckedSpecCons')}}", field:"chk_specialcons",visible:false},
		
	   ],
    });




$('#from_date').change(function(){
	var from_date = $('#from_date').val();
	
	if(from_date!=null && from_date!=''){
	$('#to_date').flatpickr().destroy();	
	$('#to_date').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		minDate: from_date ,
        disableMobile: true
	});
	}else{
	   $('#to_date').flatpickr().destroy();	
	   $('#to_date').flatpickr({
		allowInput: true,
		enableTime: false,
        dateFormat: "Y-m-d",
		disableMobile: true
	   });
	}
	
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var patient=$('#select_pateint').val();
    var code=$('#select_test').val();
    var is_phletobomy = $('#is_phletobomy').val();
	var token='{{csrf_token()}}';
    table.setData("{{ route('phlebotomy.index',app()->getLocale())}}",  {_token:token,code:code,patient:patient,from_date:from_date,to_date:to_date,is_phletobomy:is_phletobomy});
	
});		
		  	
$('#to_date,#select_pateint,#select_test,#is_phletobomy').change(function(){
	
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var patient=$('#select_pateint').val();
    var code=$('#select_test').val();
    var is_phletobomy = $('#is_phletobomy').val();
	var token='{{csrf_token()}}';
    table.setData("{{ route('phlebotomy.index',app()->getLocale())}}",  {_token:token,code:code,patient:patient,from_date:from_date,to_date:to_date,is_phletobomy:is_phletobomy});
   
   });
   
$('#codesModal').on('hide.bs.modal',function(){
	var patient=$('#select_pateint').val();
	var code=$('#select_test').val();
	var from_date = $('#from_date').val();
	var to_date=$('#to_date').val();
	var is_phletobomy = $('#is_phletobomy').val();
	var token='{{csrf_token()}}';
	table.setData("{{ route('phlebotomy.index',app()->getLocale())}}",  {_token:token,code:code,patient:patient,from_date:from_date,to_date:to_date,is_phletobomy:is_phletobomy});

});


});

function save_data(){
		var collection_date =coll_notes=null;
	    var order_id =$('#codesModal').find('#modal_order_id').val();
		var checkedSpecimens = [];
		var checkedSpecCons = [];
		var rows = table_codes.getRows();
		rows.forEach(function(row) {
			var rowData = row.getData();	
			var checkbox = row.getElement().querySelector('.chkcodes');
			var checkbox2 = row.getElement().querySelector('.chkspeccons');
			if(checkbox){
			  var isChecked = checkbox.checked;
			  var value = parseInt(checkbox.value);
			  
			  if(isChecked && !checkedSpecimens.includes(value)){
				checkedSpecimens.push(value);
			  }
			}
			
			if(checkbox2){
			  var isChecked = checkbox2.checked;
			  var value = parseInt(checkbox2.value);
			  
			  if(isChecked && !checkedSpecCons.includes(value)){
				checkedSpecCons.push(value);
			  }
			}
			
			
		});
			
        
		
		var coll_notes = $('#codesModal').find('#coll_notes').val().trim();  
		
		
		
			$.ajax({
					url:'{{route("phlebotomy.save",app()->getLocale())}}',
					type: 'POST',
					dataType: 'JSON',
					data: {_token:'{{csrf_token()}}',
					        order_id:order_id,
							checkedSpecimens:checkedSpecimens,
							checkedSpecCons:checkedSpecCons,
							coll_notes: coll_notes
							},
					success: function(data){
						if(data.ok=='Y'){
							$('#codesModal').find('#print_lbl').prop('disabled',false);
						}else{
							$('#codesModal').find('#print_lbl').prop('disabled',true);
						}
						var token='{{csrf_token()}}';
	                    table_codes.setData("{{ route('phlebotomy.codes',app()->getLocale())}}",  {_token:token,order_id:order_id});

						
					}
				});
 
}

function openCodesModal(order_id,daily_serial_nb){

function testFormatter(cell, formatterParams, onRendered) {
     var container = document.createElement("div");
    
    // Create checkbox element
    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";
	checkbox.classList.add("mr-2");

    // Handle click event on checkbox
    checkbox.addEventListener("change", function() {
        // Get all rows in the table
        var rows = cell.getTable().getRows();

        // Set selected state of all checkboxes in the column based on the state of the checkbox in the header
        rows.forEach(function(row) {
            var cellValue = row.getCell(cell.getColumn().getField()).getValue();
			var element = row.getElement().querySelector('.chkcodes');
            if(element){
				if (checkbox.checked) {
					element.checked = true;
				} else {
					element.checked = false;
				}
			}
        });
    });

   var label = document.createElement("label");
    label.textContent = "Specimen";
    container.appendChild(checkbox);
	container.appendChild(label);

    return container;
}

function testFormatter2(cell, formatterParams, onRendered) {
     var container = document.createElement("div");
    
    // Create checkbox element
    var checkbox = document.createElement("input");
    checkbox.type = "checkbox";
	checkbox.classList.add("mr-2");

    // Handle click event on checkbox
    checkbox.addEventListener("change", function() {
        // Get all rows in the table
        var rows = cell.getTable().getRows();

        // Set selected state of all checkboxes in the column based on the state of the checkbox in the header
        rows.forEach(function(row) {
            var cellValue = row.getCell(cell.getColumn().getField()).getValue();
			var element = row.getElement().querySelector('.chkspeccons');
            if(element){
				if (checkbox.checked) {
					element.checked = true;
				} else {
					element.checked = false;
				}
			}
        });
    });

   var label = document.createElement("label");
    label.textContent = "Special considerations";
    container.appendChild(checkbox);
	container.appendChild(label);

    return container;
}
	
	
	table_codes = new Tabulator("#codes_list_table", {
	 ajaxURL:"{{ route('phlebotomy.codes',app()->getLocale())}}", //ajax URL
     ajaxConfig:'POST',
	 ajaxParams: function(){
        var token = '{{csrf_token()}}';
		return {_token:token,order_id:order_id};
        },
	ajaxResponse:function(url, params, response){
         var coll_notes = response.coll_notes;
		 //set notes in modal
		 $('#codesModal').find('#coll_notes').val(coll_notes);
		 
        return response.table_data; 
    },	
	height:"420px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:false, 
	paginationCounter:"rows",
	layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	resizableRows:true,
    columns:[
	  {title:"{{__('Test ID')}}", field:"test_id",visible:false},
	  {title: "{{__('Code')}}",field:"test_code",headerFilter:"input"},
	  {title: "{{__('Test')}}", headerSort: false, field:"test_name",headerFilter:"input"},
	  {title:"{{__('Date/Time')}}",field:"collected_test_date",headerFilter:"input"},
	  {title:"{{__('Is Collected')}}",field:"is_test_collected",visible:false},
	  {title:"{{__('CNSS')}}", field:"cnss",headerFilter:"input"},
	  {title:"{{__('Group')}}", field:"is_group",headerFilter:"input",formatter:function(cell, formatterParams, onRendered){
		  var data=cell.getValue();
		  if(data=='Y'){
			  return "{{__('Yes')}}";
		  }else{
			 return "{{__('No')}}"; 
		  }
	  }},
	  
	  {title:"{{__('CheckedSpecCons')}}", field:"chk_specialcons",visible:false},
	  {title:"{{__('CheckedSpecimens')}}", field:"chk_specimens",visible:false},
	  {titleFormatter: testFormatter, headerSort: false,field:"specimen", formatter:function(cell, formatterParams, onRendered){
			var data = cell.getValue();
			var btn='';
			var row = cell.getRow().getData();
			var test_id = row.test_id;
			var chked = row.chk_specimens!='' && row.chk_specimens!=null?JSON.parse(row.chk_specimens):[];
		    var checked =  (chked.length!=0 && chked[test_id]!=null)?'checked':'';
		    
			if(data!=null && data!=''){
			  var specimen = @json($specimen);
			  var img_src='',title='';
			  function isImageURL(url) {
               var imageExtensions = /\.(jpg|jpeg|png|gif)$/i;
               return imageExtensions.test(url);
              }
			  
			 var myspecimen = specimen.find(function(obj) {
                          return obj.id === parseInt(data);
                        });

			 if(myspecimen){ 
					  img_src = myspecimen.src;
					  title =   myspecimen.name;
					   }
			 
		 
		  
		  var btn='';
			 if(!isImageURL(img_src)){
					   btn='<div><input type="checkbox" '+checked+' class="mr-2 chkcodes" value="'+test_id+'" /><label class="ml-1">'+img_src+'</label></div>';
  					  }else{
					   btn='<div><input type="checkbox" '+checked+' class="mr-2 chkcodes" value="'+test_id+'" /><label class="ml-1"><img src="'+img_src+'" title="'+title+'" style="height:20px;width:20px;"/></label></div>';
					  }				
				
		       return btn;
			}else{
				return data;
			}
		 }},
	  
	  {titleFormatter: testFormatter2, headerSort: false,field:"special_considerations",
		 formatter:function(cell, formatterParams, onRendered){
			var row = cell.getRow().getData();
			var data = cell.getValue();
			var test_id = row.test_id;
			var chked = row.chk_specialcons!='' && row.chk_specialcons!=null?JSON.parse(row.chk_specialcons):[];
		    var checked =  (chked.length!=0 && chked[test_id]!=null)?'checked':'';
			var btn='';
			if(data!=null && data!=''){
			 var spec_cons = @json($spec_cons);
			 btn='<div><input type="checkbox" '+checked+' class="mr-2 chkspeccons" value="'+test_id+'" /><label class="ml-1">'+spec_cons[data]+'</label></div>';
			 return btn;
			}else{
				return data;
			}
		 }
		},
	  {title:"{{__('PreAnalytical')}}",field:"preanalytical"}	
	  
	 ],
	});

$('#codesModal').find('#modal_order_id').val(order_id);
$('#codesModal').find('#modal_daily_serial_nb').val(daily_serial_nb);
$('#codesModal').modal('show');
}

function loadMore(el) {
            var contentContainer = $(el).closest('.content-container');
            contentContainer.find('.full-content').show();
            contentContainer.find('.truncated-content').hide();
            contentContainer.find('.load-more-btn').hide();
            contentContainer.find('.load-less-btn').show();
        }

 function loadLess(el) {
 		    var contentContainer = $(el).closest('.content-container');
            contentContainer.find('.full-content').hide();
            contentContainer.find('.truncated-content').show();
            contentContainer.find('.load-more-btn').show();
            contentContainer.find('.load-less-btn').hide();
        }
	
	
function printLBL(){	 
    var order_id = $('#codesModal').find('#modal_order_id').val();
	var daily_serial_nb = $('#codesModal').find('#modal_daily_serial_nb').val();
    $.ajax({
           url: '{{route("phlebotomy.label",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','order_id':order_id,daily_serial_nb:daily_serial_nb},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('label.pdf');
					link.click();	
			       Swal.fire({title:"{{__('Downloaded successfully')}}",toast:true,showConfirmButton:false,timer:3000,position:"bottom-right"});
					
			    });
       
 }
 

function printPDF(){
	    var order_id = $('#codesModal').find('#modal_order_id').val();
		$.ajax({
			url:'{{route("lab.visit.printOrder",app()->getLocale())}}',
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
					
					link.download=('Request#'+order_id+'.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
}

function printSerial(order_id,serial_nb){
	
		$.ajax({
			url:'{{route("phlebotomy.serial",app()->getLocale())}}',
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
			data:{'_token': '{{ csrf_token() }}',order_id:order_id,serial_nb:serial_nb},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download=('Request#'+order_id+'.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
}


</script>
@endsection 
 