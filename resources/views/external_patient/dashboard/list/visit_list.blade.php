<!--
    DEV APP
    Created date : 19-3-2023
	
 -->
<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_visit_table" class="table table-bordered table-striped  data-table nowrap" style="cursor:pointer;width:100%;">
									<thead>
										<tr>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Status')}}</th>
											<th>{{__('External Doctor')}}</th>
											<th>{{__('External Lab')}}</th>
											<th>{{__('Lab')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Landline Phone')}}</th>
											<th>{{__('Cell Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($visits as $v)
									   <tr>
										  <td>{{$v->visit_date_time}}</td>
										 <td>
										   @switch($v->order_status)
											 @case('V') {{__('Validated').' '}}<a onclick="generatePDFOrder($v->id)" class="btn btn-action btn-sm">{{__('PDF')}}<span class="ml-1"><i class="fa fa-file-pdf"></i></span></a>@break
										   @endswitch
										 </td>
										 <td>{{isset($v->ProName)?$v->ProName:''}}</td>
										 <td>{{isset($v->ExtLabName)?$v->ExtLabName:''}}</td>
										 <td>{{$v->ClinicName}}</td>
										 <td>{{$v->patDetail}}</td>
									     <td>{{isset($v->Tel) && $v->Tel !=''?$v->Tel:''}}</td>
										 <td>{{isset($v->Cell) && $v->Cell !=''?$v->Cell:''}}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										
