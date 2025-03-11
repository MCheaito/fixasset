<!--
    DEV APP
    Created date : 25-9-2023
 -->
<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_stripe_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('Receipt')}}</th>
											<th>{{__('Price')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Appointment')}}</th>
											<th>{{__('Bill')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
											<th>{{__('Cell Phone')}}</th>
											<th>{{__('Email')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($pat_stripe as $rdv)
									   <tr>
									     <td><a onclick="event.preventDefault();downloadStripePDF({{$rdv->id}});"  class="btn btn-action btn-sm">{{__('Print')}}<span class="ml-1"><i class="fa fa-file-pdf"></i></span></a></td>
										 <td>{{$rdv->id}}</td>
										 <td>{{$rdv->price}}</td>
										 <td>{{Carbon\Carbon::parse($rdv->pay_date)->format('Y-m-d H:i')}}</td>
										 <td>{{__('Exam').' : '.$rdv->exam_name}}<br/>
										     {{__('Date/Time').' : '.Carbon\Carbon::parse($rdv->rdv_date)->format('Y-m-d H:i')}}
										 </td>
										 <td>
										 @if($rdv->clinic_bill_num !='' && $rdv->clinic_bill_num !=NULL)
										 {{__('Nb.').' : '.$rdv->clinic_bill_num}} <br/> {{__('Date').' : '.Carbon\Carbon::parse($rdv->bill_date)->format('Y-m-d')}} 
									     @else
										  {{__(' ')}}
									     @endif	 
										 </td>
										 <td>{{isset($rdv->ProName)?$rdv->ProName:__("Undefined")}}</td>
										 <td>{{$rdv->ClinicName}}</td>
										 <td>{{$rdv->patName}}</td>
									     <td>{{($rdv->Tel=='' || $rdv->Tel==NULL)?__("Undefined"):'+1 '.$rdv->Tel }}</td>
										 <td>{{($rdv->cell_phone=='' || $rdv->cell_phone==NULL)?__("Undefined"):'+1 '.$rdv->cell_phone }}</td>
										 <td>{{($rdv->email=='' || $rdv->email==NULL)?__("Undefined"):$rdv->email }}</td>
									   </tr>
									  @endforeach
									</tbody>
									<tfoot><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tfoot>
								</table>
			             </div>
			          </div>  									
</div>										

<script>
$(document).ready(function(){
			
	
    
	var table=$('#dash_stripe_table').DataTable({
           retrieve: true,
		   paging: true,
		   lengthMenu: [[10, 25, 50,100, -1], [10, 25, 50,100, "{{__('All')}}"]], 
           searching: true,
           ordering: true,		   
		   order: [['1','desc'],['3','desc']],
           scrollY:350,
		   scrollX:true,
		   scrollCollapse: true,
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
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "__('All')}}"
                                                 }
						
								},
				paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				},
				 footerCallback: function (row, data, start, end, display) {
						let api = this.api();
				 
						// Remove the formatting to get integer data for summation
						let intVal = function (i) {
							return typeof i === 'string'
								? i.replace(/[\$,]/g, '') * 1
								: typeof i === 'number'
								? i
								: 0;
						};
				 
						api.column(0).footer().innerHTML ='Total :';
						// Total over this page
						pageTotal = api
							.column(2)
							.data()
							.reduce((a, b) => intVal(a) + intVal(b), 0);
				 
						// Update footer
						api.column(2).footer().innerHTML =
							'$' +  parseFloat(pageTotal).toFixed(2);
											
					}
				});	
	
	
	
	
});


</script>