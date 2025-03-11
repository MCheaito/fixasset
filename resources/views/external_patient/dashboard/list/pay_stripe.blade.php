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

