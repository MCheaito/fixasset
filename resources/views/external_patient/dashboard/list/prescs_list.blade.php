<!--
    DEV APP
    Created date : 28-3-2023
 -->
<div class="container-fluid">	
			<div class="row  m-1">
			  <div class="col-md-12">
					<table id="dash_presc_table" class="table table-bordered table-striped  data-table nowrap" style="cursor:pointer;width:100%;">
									<thead>
										<tr>
											<th>{{__('#')}}</th>
											<th>{{__('Visit')}}</th>
											<th>{{__('Date/Time')}}</th>
											<th>{{__('Renew')}}</th>
											<th>{{__('Medicine')}}</th>
											<th>{{__('Remarks')}}</th>
											<th>{{__('Doctor')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
										
										</tr>
									</thead>
									<tbody>
									  @foreach($prescs as $p)
									   <tr>
									     <td>
										 {{$p->presc_id}}
									     <a onclick="event.preventDefault();downloadPrescPDF({{$p->presc_id}});"  class="btn btn-action btn-sm">{{__('Print')}}<span class="ml-1"><i class="fa fa-file-pdf"></i></span></a>
										 </td>
										 <td>{{$p->visit_num}}</td>
										 <td>{{Carbon\Carbon::parse($p->datepresc)->format('Y-m-d')}}<br/>{{Carbon\Carbon::parse($p->datepresc)->format('H:i')}}</td>
										 <td>{{isset($p->renew)?__($p->renew):__('No')}}</td>
                                         <td>
										   @if(app()->getLocale()=='en')
										    @php 
										     $eye = isset($p->eye_type)?' , '.'Eye'.' : '.$p->eye_type:''; 
											 $date = isset($p->expiry_date) && $p->expiry_date !=''?' , '.$p->expiry_date:'';
											@endphp
											{{$p->title_en.'-'.$p->section_en.' , '.$p->voie_en.$eye.$date}}<br/>
											{{$p->dosage.' '.$p->dt_en.' '.$p->dp_en.' , '.$p->freq_en.' , '.$p->duration.' '.$p->duration_unit}} 
										   @endif
                                           @if(app()->getLocale()=='fr')
											@php 
										     $eye = isset($p->eye_type)?' , '.__('Eye').' : '.__($p->eye_type):''; 
											 $date = isset($p->expiry_date) && $p->expiry_date !=''?' , '.$p->expiry_date:'';
											 @endphp
											{{$p->title_fr.'-'.$p->section_fr.' , '.$p->voie_fr.$eye.$date}}<br/>
											{{$p->dosage.' '.$p->dt_fr.' '.$p->dp_fr.' , '.$p->freq_fr.' , '.$p->duration.' '.__($p->duration_unit)}} 
										   @endif									   
                                         </td>										 
										 <td>{{$p->remarks}}</td>
										 <td>{{isset($p->docName)?$p->docName:__("Undefined")}}</td>
										 <td>{{$p->branch_name}}</td>
										 <td>
										  {{$p->patDetail}}<br/>{{($p->Tel=='' || $p->Tel==NULL)?__("Undefined"):'+1 '.$p->Tel }}
										 </td>
									    
									   </tr>
									  @endforeach
									</tbody>
								</table>
			             </div>
			          </div>  									
</div>										

