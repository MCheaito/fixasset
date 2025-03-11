<!-- 
 DEV APP
 Created date : 11-1-2023
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
				  
				  <div class="col-md-2">
					  <select id="selectStatus" name="status" class="form-control">
					   <option value="O">{{__('Active')}}</option>
					   <option value="N">{{__('InActive')}}</option>
					  </select>
			     </div>
				  <div class="col-md-4">
					  <select id="filter_speciality" name="filter_speciality" class="form-control">
					   <option value="">{{__('Choose a speciality')}}</option>
					   @foreach($specialities as $s)
					   <option value="{{$s->id}}">{{(app()->getLocale()=='fr')?$s->name_fr:$s->name_en}}</option>
					   @endforeach
					  </select>
			     </div>
				 @if(auth()->user()->admin_perm=='O' || (auth()->user()->permission=='S' && auth()->user()->type==2))
				 <div class="col-md-6">
				 <a href="{{ route('resources.create',app()->getLocale()) }}" class="btn btn-action btn-sm float-right">{{__('Create')}}<i class="ml-1 fa fa-plus"></i></a>
                 </div>
				 @endif 
				  
				</div>
                <div class="card row mt-2">                   
				   <div class="m-1 col-md-12">
				   <!--begin: Datatable -->
                    <table id="doctors_table" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
                        <thead>
                        <tr>
                           
							<th>{{__('Speciality')}}</th>
							<th>{{__('Complete Name')}}</th>
							<th>{{__('Phone')}}</th>
							<th>{{__('Phone2')}}</th>
							<th>{{__('Phone3')}}</th>
							<th>{{__('Email')}}</th>
							<th>{{__('Fax Nb')}}</th>
							<th>{{__('Address')}}</th>
							<th>{{__('Actions')}}</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        
                    </table>
					</div>
                </div>
				
  
@endsection

@section('scripts')
<script>
  $(function () { 
    
	var table= $('#doctors_table').DataTable({
		   stateSave: true,
	       stateDuration: -1,
		   processing: false,
           serverSide: true,
		   paging: true,
           searching: true,
           ordering: true,
           info: true,
		   scrollY:450,
	       scrollX:true,
	       scrollCollapse: true,
	       pageLength: 50,
		  lengthMenu: [
           [50, 75, 100, -1],
           [50, 75, 100, 'All']
            ],
		   ajax: {
            url: "{{ route('resources.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_status=$('#selectStatus').val();
				d.filter_speciality=$('#filter_speciality').val();
				
                    }
			
              },
        columns: [
			{data: 'speciality_name'},
			{data: 'doctor_name'},
			{data: 'tel'},
			{data: 'tel2'},
			{data: 'tel3'},
			{data: 'email'},
			{data: 'fax'},
			{data: 'address'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
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
	   
	   table.columns.adjust();
		
		$('#selectStatus,#filter_speciality').change(function(){
			table.ajax.reload();
		});
		
		$('body').on('change','.toggle-chk',function(e){
			var id = $(this).data("id");
			var url = '{{route("resources.destroy",[app()->getLocale(),":id"])}}';
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

@endsection
