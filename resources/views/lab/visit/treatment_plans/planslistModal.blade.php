<!--
   DEV APP
   Created date : 3-2-2023
-->
<div class="modal fade" id="planslistModal" tabindex="-1" role="dialog" aria- 
            labelledby="planslistModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							    <h5>{{__('All list')}}</h5>
								<input type="hidden" id="row_id"/>
								<button type="button" class="close" data-dismiss="modal" aria- 
                                label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
						</div>
						<div class="modal-body">
						   <form id="planslist_form">
						   <div class="card row">
							  <div class="col-md-12 m-1">
								<table id="plans_table" class="table table-bordered table-striped display responsive compact" style="width:100%">
									<thead>
									  <tr>
									  <th class="all">{{__('Category')}}</th>
									  <th class="all">{{__('Title')}}</th>
									  <th>{{__('Remark')}}</th>
									  <th class="all">{{__('Choose')}}</th>
									  </tr>
									</thead>
									<tbody>
									@foreach($plans as $plan)
									 <tr>
									 <td id="plan_category_{{$plan->id}}">{{(app()->getLocale()=='en')?$plan->category_en:$plan->category_fr}}</td>
									 <td id="plan_title_{{$plan->id}}">{{(app()->getLocale()=='en')?$plan->title_en:$plan->title_fr}}</td>
									 <td id="plan_remark_{{$plan->id}}">
									 @php $str=(app()->getLocale()=='en')?$plan->remark_en:$plan->remark_fr; @endphp
									 @if(strlen($str) > 100)
										{{substr($str,0,100)}}
										<span class="read-more-show hide_content">...<i class="fa fa-plus icon-sm btn-active"></i></span>
										<span class="read-more-content"> {{substr($str,100,strlen($str))}} 
										<span class="read-more-hide hide_content"><i class="fa fa-minus btn-active"></i></span> </span>
									@else
									    {{$str}}
									@endif
									 </td>
									 <td><input type="checkbox" class="chk" value="{{$plan->id}}"/></td>
									 </tr>
									@endforeach
									</tbody>
									</table>
							  </div>
							</div>
							</form>
						</div>
						
					</div>
				</div>
			</div>