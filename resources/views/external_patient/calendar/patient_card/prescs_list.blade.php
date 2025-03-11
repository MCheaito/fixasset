<!--
    DEV APP
    Created date : 28-3-2023
 -->
<div class="container-fluid">	
			<div class="row  m-1" style="overflow-x:auto;height:350px;overflow-y: auto;">
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
											<th>{{__('Resource')}}</th>
											<th>{{__('Branch')}}</th>
											<th>{{__('Patient')}}</th>
										</tr>
									</thead>
									<tbody>
									  @foreach($prescs as $p)
									   <tr>
									     <td>
										 {{$p->presc_id}}<a onclick="PopupCenter('{{route('emr.visit.med_prescription.index',[app()->getLocale(),$p->presc_id])}} ', '{{__('Medical Prescription')}}','1000','500')" class="ml-1 btn btn-icon btn-sm" title="{{__('View')}}"><i class="fa fa-eye" style="color:#1bbc9b"></i></a>
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

<script>
$(document).ready(function(){
			
	
    
	$('#dash_presc_table').DataTable({
           
		   retrieve: true,
		   paging: true,
           searching: true,
           ordering: true,
		   order: [['1','desc'],['2','desc']],
           info: true,
		   language: {
			    search:         "{{__('Search')}}&nbsp;:",
				lengthMenu:    "{{__('Show')}} _MENU_ {{__('entries')}}",
				info:          "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
				infoEmpty: "{{__('Showing')}}  0 {{__('to')}} 0 {{__('of')}} 0 entrées",
				emptyTable:  "{{__('No data is found')}}",
				zeroRecords: "{{__('No data is found')}}",
				buttons: {
                                   "colvis": "{{__('Column visibility')}}",
                                   "copy": "{{__('Copy')}}",
								   "print": "{{__('Print')}}",
								   "pageLength": {
                                                   _: "{{__('Show')}} %d {{__('entries')}}",
												   '-1': "__('Show all')}}"
                                                 }
						
								},
				paginate: {
								first:      "{{__('First')}}",
								previous:   "{{__('Previous')}}",
								next:       "{{__('Next')}}",
								last:       "{{__('Last')}}"
							}
				}
				});	
	
	
	
	
});


</script>
<script>
    function PopupCenter(url, title, w, h) {  
        // Fixes dual-screen position                         Most browsers      Firefox  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;  
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;  
                  
        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;  
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;  
                  
        var left = ((width / 2) - (w / 2)) + dualScreenLeft;  
        var top = ((height / 2) - (h / 2)) + dualScreenTop;  
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);  
      
        // Puts focus on the newWindow  
        if (window.focus) {  
            newWindow.focus();  
        }  
    }  
</script>

