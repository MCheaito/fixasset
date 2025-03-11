<!--
   DEV APP
   Created date : 26-2-2023
-->
@extends('gui.main_gui')
@section('styles')
<style>
.read-more-show{
      cursor:pointer;
      
    }
    .read-more-hide{
      cursor:pointer;
     
    }

    .hide_content{
      display: none;
    }
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
<div class="container">	
    <div class="row m-1">
	  <div class="col-md-12">
	   <a href="{{route('emr.visit.edit',[app()->getLocale(),$visit_id])}}" class="m-1 float-right btn btn-back">{{__('Back')}}</a>  

	   <h3>{{__('Treatment Plan')}}</h3>
	  </div>
     
    </div>
	
	  <form action="{{$patient_plans->count()>0? route('emr.visit.treatment_plans.update',app()->getLocale()):route('emr.visit.treatment_plans.store',app()->getLocale())}}"
	        class="form-row" method="POST" enctype="multipart/form-data">
			  @csrf
			   <div class="col-md-12">
				 <button type="submit" id="save_data" class="m-1 btn btn-action">{{$patient_plans->count()>0?__('Update'):__('Save')}}</button>
	             <button class="m-1 btn btn-action"  id="btnview" name="btnview">{{__('Import')}}</button>
      	         <button id="add" type="button" class="m-1  btn btn-action">{{__('Insert')}}&#xA0;<i class="fa fa-plus"></i></button>
                 <button class="float-right m-1 btn btn-action"  id="btnprint" name="btnprint"  {{$patient_plans->count()>0?'':'disabled'}}>{{__('Print')}}</button>
			   	 <button class="float-right m-1 btn btn-action"  id="btnsend" name="btnsend"  {{$patient_plans->count()>0?'':'disabled'}}>{{__('Send')}}</button>
	          </div>	  
			  <input type="hidden" name="visit_num" value="{{$visit_id}}"/>
			  <div class="table-responsive col-md-12">
				<table class="table table-sm table-bordered nowrap" id="dynamic_field">  
				  @if($patient_plans->count()>0)
					  @php $row_cnt=1; @endphp
				      @foreach($patient_plans as $p)
						  <tr id="row{{$row_cnt}}" class="dynamic-added">
							<td><textarea  id="row{{$row_cnt}}" class="summernote form-control" name="description[]">{{$p->description}}</textarea></td>
						  <td>
						   <a href="#planslistModal" id="{{$row_cnt}}" class="m-1 btn btn-icon btn-sm btn-action btn-list" data-target="#planslistModal" data-toggle="modal">{{__('Choose')}}</a>  
						   <button type="button" name="remove" id="{{$row_cnt}}" class="m-1 btn btn-icon btn-sm btn-delete btn-remove"><i class="fa fa-trash" title="{{__('delete')}}"></i></button>
						  </td>
						 </tr>
						 @php $row_cnt++; @endphp
					 @endforeach
				  @else
					   <tr id="row1" class="dynamic-added">
					   <td><textarea  id="row1" class="summernote form-control" name="description[]"></textarea></td>
					   <td>
						<a href="#planslistModal" id="1" class="m-1 btn btn-icon btn-sm btn-action btn-list" data-target="#planslistModal" data-toggle="modal">{{__('Choose')}}</a>  
						<button type="button" name="remove" id="1" class="m-1 btn btn-icon btn-sm btn-delete btn-remove"><i class="fa fa-trash" title="{{__('delete')}}"></i></button>
					   </td>
					  </tr>
				  @endif
				</table>
			   </div>
			   
	   </form>
     
</div>
@include('emr.visit.treatment_plans.planslistModal')
@include('emr.visit.treatment_plans.importTreatmentPlanModal')
@include('emr.visit.treatment_plans.sendTreatmentPlanModal')

@endsection
@section('scripts')
<script>
 $('.movableDialog').draggable({
       handle: ".modal-header"
       });
	 
</script>
<script>
$(function() {
           // Hide the extra content initially, using JS so that if JS is disabled, no problemo:
            $('.read-more-content').addClass('hide_content')
            $('.read-more-show, .read-more-hide').removeClass('hide_content')

            // Set up the toggle effect:
            $('.read-more-show').on('click', function(e) {
              $(this).next('.read-more-content').removeClass('hide_content');
              $(this).addClass('hide_content');
              e.preventDefault();
            });

            // Changes contributed by @diego-rzg
            $('.read-more-hide').on('click', function(e) {
              var p = $(this).parent('.read-more-content');
              p.addClass('hide_content');
              p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
              e.preventDefault();
            });
  
  var language='{{app()->getLocale()=="fr"?"fr-FR":"en-EN"}}';
  $('.summernote').summernote({
	  toolbar: [
  ['style', ['style']],
  ['font', ['bold', 'underline', 'clear']],
  ['fontname', ['fontname']],
  ['color', ['color']],
  ['para', ['ul', 'ol', 'paragraph']],
  ['table', ['table']]
  
 
  ],
  lang: language,
  height: 'auto',
  width: 'auto'
  });
  
  $('#plans_table').DataTable({
           paging: true,
           searching: true,
           ordering: true,
		   info: true,
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
				}
		  
       });
	   
	   
	   
	   
	   var cnt = '{{$patient_plans->count()>0?$patient_plans->count():1}}';
	   var i=cnt;  
      
	  $('#add').click(function(e){  
           e.preventDefault();
           i++;  

           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><textarea  id="summernote_'+i+'"  class="summernote form-control" name="description[]"></textarea></td><td><a href="#planslistModal" id="'+i+'" class="m-1 btn btn-icon btn-sm btn-action btn-list" data-target="#planslistModal" data-toggle="modal">{{__("Choose")}}</a><button type="button" name="remove" id="'+i+'" class="m-1 btn btn-sm btn-icon btn-delete btn-remove"><i class="fa fa-trash" title="{{__("delete")}}"></i></button></td></tr>');  
           $('.summernote').summernote({
					  toolbar: [
				  ['style', ['style']],
				  ['font', ['bold', 'underline', 'clear']],
				  ['fontname', ['fontname']],
				  ['color', ['color']],
				  ['para', ['ul', 'ol', 'paragraph']],
				  ['table', ['table']]
				  
				 
				  ],
				  lang: language,
				  height: 'auto',
				  width: 'auto'
				  });
				  
		}); 
	  
	  $('body').on('click','.btn-remove',function(){  

           var button_id = $(this).attr("id");   
           //alert(button_id);
           $('#row'+button_id+'').remove();  

      });
      
      $('body').on('click','.btn-list',function(){  
	   $('#row_id').val("row"+$(this).attr("id"));
	  
	  }); 	  
  
});
</script>
<script>
$('#planslistModal').on('hidden.bs.modal',function(){
	 var numberOfChecked = $('.chk:checked').length;
	 //var data = $('.summernote').eq(0).val();
	 var rowid =$('#row_id').val();
	 var data = $('#'+rowid).find('textarea').val();
     //alert(rowid);
	
	 if(numberOfChecked>0){
	 var result=(data!=null && data!='')?data:'';
	 $('.chk:checked').each(function () {
		 var id = $(this).val();
		 var category = $('#plan_category_'+id).text();
		 var title = $('#plan_title_'+id).text();
		 var remark = $('#plan_remark_'+id).text();
		 result+= '<div><b>'+category+' : </b>'+title+' - '+remark+'</div><br/>';
	 });
	 
	 $('#planslist_form').trigger("reset");
	 $('#'+rowid).find('.summernote').summernote('code', result);

	 }
});
</script>
<script>
$(function(){
   $('.ext_branches_tab').click(function (e) {
		//clear pat checkboxes
		$('.chk_pat_email').prop('checked',false);
		$('.chk_pat_fax').prop('checked',false);
	});
	
	 $('.pat_tab').click(function (e) {
		//clear pat checkboxes
		$('.chk_email').prop('checked',false);
		$('.chk_fax').prop('checked',false);
	});
   
   $('#btnsend').off().on('click',function(e){
         e.preventDefault();
         var visit_num =  $('input[name="visit_num"]').val();
				 
				  $.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							   }
						   });
				  $.ajax({
						type : 'POST',
						url : '{{route("emr.visit.treatment_plans.pat_externalBranches",app()->getLocale())}}',
						data : { visit_num :visit_num},
						dataType: 'JSON',
						success: function(data){
						  $('#sendTreatmentPlanModal').find('#ext_visit_id').val(visit_num);
						  $('#sendTreatmentPlanModal').find('#patient_data').html(data.html_patient);
						  $('#sendTreatmentPlanModal').find('#external_branches').html(data.html_external_branches);
						  $('#sendTreatmentPlanModal').modal('show');
						}
					  });	 
   });
   $('#btnprint').off().on('click',function(e){
         e.preventDefault();	   
		   var cnt='{{$patient_plans->count()}}';	
		   var visit_num = $('input[name="visit_num"]').val();
			  if(cnt>0){
				  
			   $.ajax({
				   url: '{{route("emr.visit.treatment_plans.generatepdf",app()->getLocale())}}',
				   beforeSend: function() { 
								 Swal.fire({
									title: '',
									html: '{{__("Please wait...")}}',
									timerProgressBar: true,
									  didOpen: () => {
										Swal.showLoading()
									   }
									}); },
				   data: {'_token': '{{ csrf_token() }}','visit_num':visit_num},
				   type: 'post',
				   xhrFields: { responseType: 'blob'},
				   }).then(function(data){
							
						//pdf is downloaded return back
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					link.download=('treatment_plan.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
			
						});
			  }
           });
		   
		   
	$('#btnview').off().on('click',function(e){
 
	 e.preventDefault();
	 var visit_num =  $('input[name="visit_num"]').val();
				 
				  $.ajaxSetup({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							   }
						   });
				  $.ajax({
						type : 'POST',
						url : '{{route("emr.visit.treatment_plans.list",app()->getLocale())}}',
						data : { visit_num :visit_num},
						dataType: 'JSON',
						success: function(data){
						  $('#importTreatmentPlanModal').find('#modal_title').text('{{__("Treatment plan")}}');
						  $('#importTreatmentPlanModal').find('#current_visit_num').val(visit_num);
						  $('#importTreatmentPlanModal').find('#patient_treatment_plans').html(data.html_treatment_plans);
						  $('#importTreatmentPlanModal').modal('show');
						}
					  });

});	   
	
	});
</script>
@endsection
