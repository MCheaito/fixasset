<!--
    DEV APP
    Created date : 19-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
					<table id="dash_docs_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
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
									  @foreach($medical_documents as $p)
									   <tr>
									     <td><a onclick="localStorage.setItem('visitsTab','#MedicalDocuments');PopupCenter('{{route('patient_dash.visit.view',[app()->getLocale(),$p->visit_num])}} ', '{{__('Medical Documents')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
										 <td>{{$p->id}}</td>
										 <td>{{isset($p->ProName)?$p->ProName:__("Undefined")}}</td>
										 <td>{{$p->visit_num}}</td>
									     <td>{{$p->visit_date_time}}</td>
									     <td>{{$p->ClinicName}}</td>
										 <td>{{$p->patDetail}}</td>
									     <td>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):'+1 '.$p->Tel }}</td>
										</td>

									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

