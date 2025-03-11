<!--
    DEV APP
    Created date : 18-3-2023
 -->
<div class="container-fluid">	
			
			<div class="row m-1">
			  
			  @if(isset($inv_balance) && $inv_balance !=0)
			  <div class="col-md-8"></div>
		      <div  id="div_inv_balance" class="form-group col-md-4">
					<label class="text-dark" style="font-size:0.75em;">{{__('Inv. due Amnt')}}</label>
					<input type="text" disabled id="inv_balance" class="form-control label-size bg-gradient-whitesmoke" value="{{$inv_balance}}"/>
			  </div>
			  @endif
			  <div class="col-md-12">
			        <table id="dash_inv_bills_table" class="table table-bordered table-striped  data-table nowrap" style="width:100%;">
									<thead>
										<tr>
											<th>{{__('Action')}}</th>
											<th>{{__('#')}}</th>
									        <th>{{__('Bill')}}</th>
											<th>{{__('Total')}}</th>
										    <th>{{__('Due Balance')}}</th>
											<th>{{__('Paid')}}</th>
											<th>{{__('Patient')}}</th>
									        <th>{{__('Sub-total')}}</th>
									        <th>{{__('QST')}}</th>
									        <th>{{__('GST')}}</th>
									        <th>{{__('Branch')}}</th>      
									        
										</tr>
									</thead>
									<tbody>
									  @php $count=0 @endphp
									  @foreach($inv_bills as $b)
									   <tr>
									     <td><a onclick="event.preventDefault();downloadInvoicePDF({{$b->id}});"  class="btn btn-action btn-sm">{{__('Print')}}<span class="ml-1"><i class="fa fa-file-pdf"></i></span></a></td>
										 <td>{{$b->id}}</td>
									     <td>
										  {{$b->bill_id}} <br/>{{$b->bill_date}}
										 </td>									 
										 <td>{{$b->total}}</td>
										 <td>{{$b->sold}}</td>
										 <td>{{$b->total_pay}}</td>
										 <td>{{isset($b->patDetail)?$b->patDetail:__("Undefined")}}</td>
										 <td>{{$b->subtotal}}</td>
										 <td>{{$b->qst}}</td>
										 <td>{{$b->gst}}</td>
										 <td>{{$b->ClinicName}}</td>
									   </tr>
									  @endforeach
									</tbody>
									<tfoot>
					                 <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
					                </tfoot>
								</table>
			             </div>
			          </div>  									
</div>										
