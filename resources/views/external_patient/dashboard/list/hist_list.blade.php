<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			<div class="row  m-1">
			  <div class="col-md-12">
			        <table id="dash_hist_table" class="table table-bordered table-striped   data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th>{{__('Visit Date/Time')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($history as $h)
									   <tr>
									     <td><a onclick="localStorage.setItem('visitsTab','#History');PopupCenter('{{route('patient_dash.visit.view',[app()->getLocale(),$h->visit_num])}} ', '{{__('History form')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$h->id}}</td>
										 <td>{{isset($h->ProName)?$h->ProName:__("Undefined")}}</td>
										 <td>{{$h->visit_num}}</td>
									     <td>{{$h->visit_date_time}}</td>
									     <td>{{$h->ClinicName}}</td>
										 <td>{{$h->patDetail}}</td>
									     <td>{{($h->Tel=='' || $h->Tel==NULL)?__("Undefined"):'+1 '.$h->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

