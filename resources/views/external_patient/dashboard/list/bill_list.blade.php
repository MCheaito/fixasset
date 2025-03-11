<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row m-1">
			  @if(isset($bill_balance) && $bill_balance !=0)
						  <div class="col-md-8"></div>
						   <div id="div_bill_balance" class="form-group col-md-4">
							  <label class="text-dark" style="font-size:0.75em;">{{__('Med. due Amnt')}}</label>
							  <input type="text" disabled id="bill_balance" class="form-control label-size bg-gradient-whitesmoke" value="{{ $bill_balance}}"/>
							</div>
				@endif
			  <div class="col-md-12">
			        <table id="dash_bills_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
									        <th>{{__('Bill')}}</th>
											<th>{{__('Total')}}</th>
											<th>{{__('Due Balance')}}</th>
											<th>{{__('Paid')}}</th>
									        <th>{{__('Doctor')}}</th> 
									        <th>{{__('Sub-total')}}</th>
											<th>{{__('QST')}}</th>
									        <th>{{__('GST')}}</th>       
											<th>{{__('Branch')}}</th>
									        <th>{{__('Patient')}}</th>
									        <th>{{__('Phone')}}</th>
									        
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($bills as $b)
									   <tr>
									     <td><a onclick="event.preventDefault();downloadBillPDF({{$b->id}});"  class="btn btn-action btn-sm">{{__('Print')}}<span class="ml-1"><i class="fa fa-file-pdf"></i></span></a></td>
										 <td>{{$b->id}}</td>
									     <td>@php $bill= explode(',',$b->fac_bill); @endphp
										 {{__("Num").' : '.$bill[0]}}<br/>{{__("Date").' : '.$bill[1]}}
										 </td>
										 <td>{{$b->total}}</td>
										 <td>{{$b->sold}}</td>
										 <td>{{$b->total_pay}}</td>
										 <td>{{isset($b->ProName)?$b->ProName:__("Undefined")}}</td>
										 <td>{{$b->sub_total}}</td>
										 <td>{{$b->tvq}}</td>
										 <td>{{$b->tvs}}</td>
										 <td>{{$b->ClinicName}}</td>
										 <td>{{$b->patDetail}}</td>
									     <td>{{($b->Tel=='' || $b->Tel==NULL)?__("Undefined"):'+1 '.$b->Tel }}</td>
									   </tr>
									  @endforeach
									</tbody>
									<tfoot>
					                 <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
					                </tfoot>
								</table>
			             </div>
			          </div>  									
</div>										

