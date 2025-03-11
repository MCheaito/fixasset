<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_rdv_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('#')}}</th>
											<th>{{__('Exam')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Status')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($rdvs as $rdv)
									   <tr>
									     <td>{{++$count}}</td>
										 <td>{{$rdv->exam_name}}</td>
										 <td>{{isset($rdv->ProName)?$rdv->ProName:__("Undefined")}}</td>
									     <td>{{$rdv->rdv_date.' '.$rdv->rdv_time}}</td>
										 <td>{{$rdv->rdv_state}}</td>
										 <td>{{$rdv->ClinicName}}</td>
										 <td>{{$rdv->patName}}</td>
									     <td>{{($rdv->Tel=='' || $rdv->Tel==NULL)?__("Undefined"):'+1 '.$rdv->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

