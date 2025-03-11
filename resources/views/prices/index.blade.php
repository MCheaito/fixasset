<!-- 
 DEV APP
 Created date : 16-2-2024
-->
@extends('gui.main_gui')
@section('content')
<div class="container-fluid">
      <div class="row m-1">  	
			<div class="col-md-8">
			     <h3>{{__('All prices')}}</h3>
			</div>
			<div class="col-md-4">
			  <input type="text" readonly="true" class="form-control form-control-border border-width-2" value="{{$lab->full_name}}"/>
			</div>
			
        </div>	
	  
	  <div class="card card-outline">
			      <div class="card-header card-menu">
					 <div class="card-title">
						<ul class="nav nav-pills">
						  
						  <li class="nav-item">
							<a class="all_tab nav-link" href="#extlabs" data-toggle="tab">{{__('Guarantors')}}</a>
						  </li>
						 
						  <li class="nav-item">
							<a class="all_tab nav-link" href="#ins" data-toggle="tab">{{__('Referred Labs')}}</a>
						  </li>
						 
						</ul>
				   </div>
                  </div>
			   
			  
			   <div class="card-body p-0 pt-1">
			       	<div class="tab-content">
				         <div id="extlabs"  class="tab-pane">
						    <div class="container-fluid">    
								<div class="m-1 row">
									<div class="form-group col-md-4">
										  <select  id="filter_extlab" name="filter_extlab" class="select2_data custom-select rounded-0" style="width:100%;">
											<option value="">{{__('Choose a guarantor')}}</option>
											@foreach($ext_labs as $lab)
											  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
											@endforeach
										  </select>
						            </div>	
									
									<div class="form-group col-md-8">
 									   <a class="m-1 float-right btn btn-action" href="{{route('extlab_prices.create',app()->getLocale())}}">{{__('Create')}}</a>
									</div> 
								</div> 
								<div class="m-1 row">
								    <div class="col-md-12">
										<table  id="dataTable_extlabs" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
											<thead>
												<tr>
												 <th>#</th>
												 <th>{{__('Category')}}</th>
												 <th>{{__('Guarantor')}}</th>
												 <th>{{__('Create date')}}</th>
							                     <th>{{__('Update date')}}</th>
												 <th>Actions</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div> 
								</div>	
							</div>
                          </div>
						  
						  <div id="ins"  class="tab-pane">
						    <div class="container-fluid">    
								<div class="m-1 row">
									
								    <div class="form-group col-md-4">
										  <select  id="filter_ins" name="filter_ins" class="select2_data custom-select rounded-0" style="width:100%;">
											<option value="">{{__('Choose a Referred Lab')}}</option>
											@foreach($insurance as $lab)
											  <option value="{{$lab->id}}">{{$lab->full_name}}</option>
											@endforeach
										  </select>
						             </div>	
									
									<div class="form-group col-md-8">
 									   <a class="m-1 float-right btn btn-action" href="{{route('ins_prices.create',app()->getLocale())}}">{{__('Create')}}</a>
									</div> 
								</div> 
								<div class="m-1 row">
								    <div class="col-md-12">
										<table  id="dataTable_extins" class="table table-bordered table-striped data-table display compact nowrap" style="width:100%;">
											<thead>
												<tr>
												 <th>#</th>
												 <th>{{__('Referred Lab')}}</th>
												 <th>{{__('Create date')}}</th>
							                     <th>{{__('Update date')}}</th>
												 <th>Actions</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div> 
								</div>	
							</div>
                          </div>
					   </div><!--end of tab content-->
						
				</div><!--end of card body-->
								  
			</div><!--end of card-->
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function(){
	$('.all_tab').click(function (e) {
			e.preventDefault();
			$($.fn.dataTable.tables(true)).DataTable()
                       .columns.adjust();
	        $(this).tab('show');
				   }); 

	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
					  $($.fn.dataTable.tables(true)).DataTable()
                       .columns.adjust();
	  var id = $(e.target).attr("href");
	  localStorage.setItem('pricesTab', id)
				});
				
		var priceTab = localStorage.getItem('pricesTab');
		if (priceTab != null) {
			$('a[data-toggle="tab"][href="' + priceTab + '"]').tab('show');
		}else{
			$('a[data-toggle="tab"][href="#extlabs"]').tab('show');
		}							
	
	
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
	  $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
      var table = $('#dataTable_extlabs').DataTable({
      	stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[0,'desc']],
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
            url: "{{ route('prices.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_extlab=$('#filter_extlab').val();
				d.type='extlab';
                    }
			
              },
        columns: [
            {data: 'id', name: 'p.id'},
			{data: 'category_name'},
			{data: 'lab_name'},
			{data: 'created_on'},
			{data: 'updated_on'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
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
				
				}
    });
	
	table.columns.adjust();
	
	var table_ins = $('#dataTable_extins').DataTable({
      	stateSave: true,
	    stateDuration: -1,
		processing: false,
        serverSide: true,
		searching: true,
		order: [[0,'desc']],
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
            url: "{{ route('prices.index',app()->getLocale()) }}",
			data: function ( d ) {
				d.filter_ins=$('#filter_ins').val();
				d.type='insurance';
                    }
              },
        columns: [
            {data: 'id', name: 'p.id'},
			{data: 'lab_name'},
			{data: 'created_on'},
			{data: 'updated_on'},
			{data: 'action', name: 'action', orderable: false, searchable: false},
        ],
		language: {
					search:         "{{__('Search')}}&nbsp;:",
					lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
					info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
					infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
					emptyTable:  "{{__('No data is found')}}",
					zeroRecords: "{{__('No data is found')}}",
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
				
				}
    });

   table_ins.columns.adjust();
   
   
   $("#filter_extlab").change(function() {
	  table.ajax.reload(); 
    });
	
   $("#filter_ins").change(function() {
	  table_ins.ajax.reload(); 
    });	
	
   


	
});
</script>

@endsection