<!--
    DEV APP
    Created date : 19-3-2023
 -->

<div class="container-fluid">	
			
			<div class="row  m-1">
			  <div class="col-md-12">
					<table id="dash_cmd_table" class="table table-bordered table-striped  data-table nowrap" style="cursor:pointer;width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
											<th>{{__('Type')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Invoice Nb')}}</th>
											<th>{{__('Visit Nb')}}</th>
											<th class="my_delivery">{{__('Delivery')}}</th>
											<th>{{__('Supplier')}}</th>
											<th>{{__('Descriptions')}}</th>
											<th>{{__('Total')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
											<th>{{__('Tel')}}</th>
											
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($commands as $p)
									   <tr>
										  <td>
											  @if(isset($p->cmd_cl) && $p->cmd_cl=='Y')
												<a onclick="localStorage.setItem('visitsTab','#GFOrders');PopupCenter('{{route('patient_dash.visit.view',[app()->getLocale(),$p->visit_num])}} ', '{{__('Contact Lens Order')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
											  @else 
												<a onclick="localStorage.setItem('visitsTab','#CLOrders');PopupCenter('{{route('patient_dash.visit.view',[app()->getLocale(),$p->visit_num])}} ', '{{__('Glass Frame Order')}}','1000','500')" class="btn btn-action btn-sm">{{__('View')}}<span class="ml-1"><i class="fa fa-eye"></i></span></a>
											   @endif										
										   </td>
										 <td>
											 @if($p->active=='O') 
											 {{$p->id}}
											 @else
											 {{$p->id}}<br/>{{__('Cancelled')}}	 
											 @endif	 
										 </td>
										 <td>{{isset($p->cmd_cl) && $p->cmd_cl=='Y'?__('Contact Lens'):__('Glass Frame')}}</td>
									     <td>{{$p->datecmd}}</td>
										 <td>{{isset($p->clinic_inv_num)?$p->clinic_inv_num:__("Undefined")}}</td>
										 <td>{{$p->visit_num}}</td>
										 <td>
											 @if($p->livrer=='Y')
											   {{__("Delivered by").' : '.$p->livrer_user}}<br/>{{__('Date').' : '.Carbon\Carbon::parse($p->livrer_date)->format('Y-m-d H:i')}}
											@else
											  {{__("Not delivered")}}
											@endif
										 </td>
										 <td>{{isset($p->supplier_name)?$p->supplier_name:__("Undefined")}}</td>
									     <td>
										 {{$p->descriptions}}
										 </td>
										 <td>{{$p->total}}</td>
										 <td>{{$p->branch_name}}</td>
										 <td>{{$p->patDetail}}</td>
									     <td>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):'+1 '.$p->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										



