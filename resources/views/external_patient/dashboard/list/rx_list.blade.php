<!--
    DEV APP
    Created date : 19-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row m-1">
			  <div class="col-md-12">
			        <table id="dash_rx_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Type')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th>{{__('Doctor')}}</th>			
											<th>{{__('Date Rx')}}</th>
											<th>{{__('Expiration Date')}}</th>
											<th>{{__('Reason')}}</th>
											<th>{{__('Optician')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Phone')}}</th>
										</tr>
									</thead>
									<tbody>
									  @php $count=0; @endphp
									  @foreach($rx as $r)
									   <tr>
									     <td><a onclick="localStorage.setItem('visitsTab','#Rx');PopupCenter('{{ route('patient_dash.visit.view',[app()->getLocale(),$r->visit_num]) }} ', '{{__('Rx')}}','1000','500')"   class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$r->id}}</td>
										 <td>{{__('Rx').'-'.(isset($r->rx_type)?$r->rx_type:__("Undefined"))}}</td>
										 <td>{{$r->visit_num}}</td>
										 <td>{{isset($r->ProName)?$r->ProName:__("Undefined")}}</td>
										 <td>{{$r->rx_date}}</td>
										 <td>{{$r->rx_expiry_date}}</td>
										 <td>{{isset($r->rx_reason)?$r->rx_reason:__("Undefined")}}</td>
										 <td>{{isset($r->rx_optician)?$r->rx_optician:__("Undefined")}}</td>
										 <td>{{$r->ClinicName}}</td>
										 <td>{{$r->patDetail}}</td>
										 <td>{{($r->Tel=='' || $r->Tel==NULL)?__("Undefined"):'+1 '.$r->Tel }}</td>
									   </tr>
									  @endforeach
									  @foreach($rx_cl as $r)
									   <tr>
									     <td><a onclick="localStorage.setItem('visitsTab','#CLRx');PopupCenter('{{ route('patient_dash.visit.view',[app()->getLocale(),$r->visit_num]) }} ', '{{__('CL Rx')}}','1000','500')"   class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a></td>
										 <td>{{$r->id}}</td>
										 <td>{{__('CL Rx').'-'.(isset($r->rx_type)?$r->rx_type:__("Undefined"))}}</td>
										 <td>{{$r->visit_num}}</td>
										 <td>{{isset($r->ProName)?$r->ProName:__("Undefined")}}</td>
										 <td>{{$r->rx_date}}</td>
										 <td>{{$r->rx_expiry_date}}</td>
										 <td>{{isset($r->rx_reason)?$r->rx_reason:__("Undefined")}}</td>
										 <td>{{isset($r->rx_optician)?$r->rx_optician:__("Undefined")}}</td>
										 <td>{{$r->ClinicName}}</td>
										 <td>{{$r->patDetail}}</td>
										 <td>{{($r->Tel=='' || $r->Tel==NULL)?__("Undefined"):'+1 '.$r->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

