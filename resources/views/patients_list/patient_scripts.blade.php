<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('patient_form');
  form.addEventListener('keypress', function(e) {
    const target = e.target;
    if (e.key === 'Enter') {
      e.preventDefault(); // Prevent form submission
      
      const focusableElements = Array.from(form.querySelectorAll('input, textarea, select, button, [tabindex]:not([tabindex="-1"])'));
      const currentIndex = focusableElements.indexOf(target);
      const nextIndex = (currentIndex + 1) % focusableElements.length;
      const nextElement = focusableElements[nextIndex];
      if (nextElement) {
        nextElement.focus();
      }
    }
  });
});
</script>
<script> 
$(function()
{
   
   $('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
    $('.select2_title').select2({theme:'bootstrap4',width:'resolve', 
	                             tags: true,
								 createTag: function (params) {
									var term = $.trim(params.term);

									if (term === '') {
									  return null;
									}
								    return {
											id: term,
											text: term,
											newTag: true 
										}
										
								  }
								 });
								 


  
								 
   $('[name=email]').inputmask("email");
   
   var phones = [{ "mask": "##-######"}, { "mask": "##-######"}];
    $('#cell_phone').inputmask({ 
        mask: phones, 
        greedy: false, 
        definitions: { '#': { validator: "[0-9]", cardinality: 1}} });
		
    var currentYear = new Date().getFullYear();
    var maxDate = new Date(currentYear, 11, 31); // Month is zero-based, so 11 represents December
    var maxDateString = maxDate.toISOString().slice(0, 10);

  flatpickr('#birthdate', {
             allowInput : true,
			 altInput: true,
             maxDate: maxDateString,
			 altFormat: "d/m/Y",
             dateFormat: "Y-m-d",
			 disableMobile: true
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

			   var access_send_results = '{{UserHelper::can_access(auth()->user(),"send_results")}}';
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
			  //window.location.href=data.location;
		  }		  
		});
}


</script>


