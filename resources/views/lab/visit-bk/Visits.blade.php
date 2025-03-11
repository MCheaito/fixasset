@extends('gui.main_gui')
@section('styles')
<!-- Bootstrap file input css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('dist/bootstrap-fileinput/css/fileinput.min.css') }}">
<style>
.bginfo{
	color: #000 !important;
}
.bgblue{
	color: #0275d8 !important; 
}
.bgorange{
	color: #fd7e14  !important; 
}
.bggreen{
	color: #5cb85c !important; 
}
.bgred{
	color: #d9534f !important; 
}
@media print {
		body * {
			visibility: hidden;
		}
		.modal-content * {
			visibility: visible;
			overflow: visible;
		}
		.main-page * {
			display: none;
		}
		.modal {
			position: absolute;
			left: 0;
			top: 0;
			margin: 0;
			padding: 0;
			min-height: 550px;
			visibility: visible;
			overflow: visible !important; /* Remove scrollbar for printing. */
		}
		
		.noPrint{
			  display: none;
		}
				
		.modal-dialog {
			visibility: visible !important;
			overflow: visible !important; /* Remove scrollbar for printing. */
		}
	}
	


.close-icon{
    	border-radius: 50%;
        position: absolute;
        right: 5px;
        top: -10px;
        padding: 5px 8px;
    }


.fancybox__slide {
  padding: 0px;
 
}

.fancybox__carousel .fancybox__slide.has-pdf .fancybox__content {
 width:95%;
 height:85%
}

.file-drop-zone {
	min-height:auto;
	
}
.file-drop-zone-title {
	font-size : 1.4em;
	padding : 25px 10px;
}

.text-nowrap{
	overflow:hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}


</style>
 
@endsection
@section('content')
@php 
   $access_validate_results = UserHelper::can_access(auth()->user(),'validate_results'); 
   $access_order_bills = UserHelper::can_access(auth()->user(),'order_bills');
   $access_order_results = UserHelper::can_access(auth()->user(),'order_results');
   $access_order_culture = UserHelper::can_access(auth()->user(),'order_culture');   
   $access_order_attachments = UserHelper::can_access(auth()->user(),'order_attachments');   
@endphp
<div class="container-fluid" style="font-size:0.9rem;">	
    <div class="row mt-1 mb-1">  
	          <div class="col-md-3 pl-1 pr-1">
	                <label for="name" class="label-size">{{__('Order')}}</label>
					<input type="text" name="dateConsult" readonly="true" value="{{isset($order)?'#'.$order->id.' , '.Carbon\Carbon::parse($order->order_datetime)->format('Y-m-d H:i'):__('New Order')}}" class="form-control form-control-border border-width-2"/>
                   
					<div class="mt-2">
					 <label for="name" class="label-size">{{__('Patient')}}</label>
					 <input type="text" name="txtPatient" readonly="true" value="{{$patient->first_name.' '.(isset($patient->middle_name)?$patient->middle_name.' ':'').$patient->last_name}}" class="form-control form-control-border border-width-2"/>
					</div>
					<div class="mt-2 select2-teal">
					  <label for="name" class="label-size">{{__('Profiles')}}</label>
					  @php 
					  $state = isset($order) && (auth()->user()->type !=2 || $order->status=='V')?'disabled':'';
					  @endphp
					  <select name="profile_tests[]" id="profile_tests" class="select2_multiple form-control" multiple="multiple"  data-placeholder="{{__('Choose a profile')}}" data-dropdown-css-class="select2-teal" {{$state}}   style="width:100%;">
					    @foreach($profiles as $p)
						  <option value="{{$p->id}}" {{in_array($p->id,$profile_ids)?'selected':''}}>{{$p->profile_name}}</option>
						@endforeach
					  </select>
					</div>
					<div class="mt-2">
					<a href="{{route('lab.visit.index',app()->getLocale())}}" class="btn btn-back">{{__('Back')}}</a>  
			       	
					</div>
					<div class="mt-2">	 
						 <div class="ml-1 row txt-border">
						  <legend class="border text-center label-size"><b>{{__('Selected Codes')}}</b></legend>
						  <div  class="col-md-12">
							<div id="chosen_tests" class="row" style="max-height:350px;overflow-y:auto;">
							</div>		
						  </div>
						 </div>
	                </div>
				</div>
	 <div class="col-md-9 pl-1 pr-1"> 
			<div class="card">
				<div class="card-header card-menu"> 
				   <div class="card-title">
						<ul class="nav nav-pills" style="font-size:1rem;">
						  
						   <li class="nav-item">
							<a class="visits_tab nav-link active"  href="#orders" data-toggle="tab">{{__('Orders')}}</a>
						  </li>
						  @if(isset($order))
							  
							  @if($access_order_bills)
							  <li class="nav-item">
								<a class="visits_tab nav-link"  href="#bill" data-toggle="tab">{{__('Billing')}}</a>
							  </li>
							  @endif
							  @if($access_order_results)
							  <li class="nav-item">
								<a class="visits_tab nav-link"  href="#results" data-toggle="tab">{{__('Results')}}</a>
							  </li>
							  @endif
							  @if($access_order_culture && isset($culture_test))
								 <li class="nav-item">
									<a class="visits_tab nav-link"  href="#culture" data-toggle="tab">{{__('Culture')}}</a>
								  </li>
							  @endif	  
							  @if($access_order_attachments)
								 <li class="nav-item">
									<a class="visits_tab nav-link"  href="#docs" data-toggle="tab">{{__('Attachments')}}</a>
								  </li>
							  @endif
							   
						  @endif
						 
						</ul>
				   </div>
                 </div>
             
			  <div class="card-body pr-0 pb-0 pt-1 pl-1"> 				 
					
					<div class="tab-content">
					   <input type="hidden" id="order_id" name="order_id" value="{{isset($order)?$order->id:'0'}}"/>
					   <input type="hidden" id="order_status" name="order_status" value="{{isset($order) && $order->status=='V'?'V':''}}"/>
					   <div id="orders" class="tab-pane active">
						     @include('lab.visit.orders.index')
					   </div>
					   @if(isset($order))
						   
						   @if($access_order_bills) 
						   <div id="bill" class="tab-pane">
								 @include('lab.visit.billing.index')
						   </div>					   
						   @endif
						   @if($access_order_results)
						   <div id="results" class="tab-pane">
								  @include('lab.visit.results.index')
						   </div>
						   @endif
						   @if($access_order_attachments)
						   <div id="docs" class="tab-pane">
								 @include('lab.visit.documents.index')
						   </div>
						   @endif
						   @if($access_order_culture && isset($culture_test))
						   <div id="culture" class="tab-pane">
								 @include('lab.visit.culture.index')
						   </div>
						   @endif
					   @endif					
					</div>
			   </div>
            </div>
           </div>			
       </div>								
</div>		
@if(isset($order) && isset($ReqPatient))
  @include('lab.visit.billing.paymentModal')
  @include('lab.visit.billing.refundModal')
  @include('lab.visit.billing.discountModal')
@endif
@if(isset($order) && $access_order_culture && isset($culture_test))
  @include('lab.visit.culture.resultTextModal')
@endif	 

@endsection	
@section("scripts")
<script src="{{ asset('dist/Tafqeet.js')}}"></script>
<!-- Bootstrap file input js-->
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/filetype.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/buffer.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/piexif.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/plugins/sortable.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/fileinput.min.js') }}"></script>
<script src="{{ asset('dist/bootstrap-fileinput/js/locales/fr.js') }}"></script>
<script>
 $(document).ready(function(){
	// turn into a datatable
    $('#tsts_tbl').dataTable({
		ordering:false,
		searching:false,
		paging:true
	});
 
 
 $culture_exist = '{{isset($culture_test)?"Y":"N"}}';
 
 if(  $culture_exist=='Y' && $(".select2-teal").is(":visible") ){
	
	 $(".sbacteria-multiple").select2();
	 
	 $('#tst_results_tbl').DataTable({paging:false});
	 
	 $('.sbacteria-multiple').on('select2:select', function (e) { 
      
	   $.ajax({
	       url:'{{route("lab.visit.getBacteriaAntibiotics",app()->getLocale())}}',
		   type:'POST',
		   data:{culture_id: $('#culture_id').val(),bacteria:$(this).val(),_token:'{{csrf_token()}}'},
		   dataType:'JSON',
		   success:function(data){
			   $('#antibioticsList').empty();
			   $('#antibioticsList').html(data.html);
		   }
		   
	   });
	  
     });
	 
	 $('.sbacteria-multiple').on('select2:unselect', function (e) { 
      // console.log($(this).val());
	   if($(this).val().length==0){
		  $('#antibioticsList').empty(); 
	   }else{
	   $.ajax({
	       url:'{{route("lab.visit.getBacteriaAntibiotics",app()->getLocale())}}',
		   type:'POST',
		   data:{culture_id: $('#culture_id').val(),bacteria:$(this).val(),_token:'{{csrf_token()}}'},
		   dataType:'JSON',
		   success:function(data){
			   $('#antibioticsList').empty();
			   $('#antibioticsList').html(data.html);
		   }
		   
	   });
	   }
     });
	 
	 $('#save_culture').click(function(e){
		 e.preventDefault();
		 var len = document.getElementById("antibioticsList").rows.length;
		 if(len==0){
			 Swal.fire({icon:'error',html:'{{__("Please choose at least one bacteria")}}',customClass:'w-auto'});
			 return false;
		 }
		 
		 var arr = new Array();
		 for(i=0;i<len;i++){
			 var bacteria_id = document.getElementById("antibioticsList").rows[i].cells[3].getElementsByTagName("input")[0].value;
			 var antibiotic_id = document.getElementById("antibioticsList").rows[i].cells[4].getElementsByTagName("input")[0].value;
			 var antibiotic_result = document.getElementById("antibioticsList").rows[i].cells[2].getElementsByTagName("select")[0].value;
		     arr[i]={"bacteria_id":bacteria_id,"antibiotic_id":antibiotic_id,"antibiotic_result":antibiotic_result};
		 }
		 
		 var culture_data=arr.length==0?null:JSON.stringify(arr);
		 
		 $.ajax({
			url:'{{route("lab.visit.saveCultureData",app()->getLocale())}}',
			type:'POST',
		    data: {_token:'{{csrf_token()}}',
		            culture_id: $('#culture_id').val(),
					order_id: $('#order_id').val(),
					test_id: $('#culture_test_id').val(),
					gram_staim:$('#gram_staim').val(),
				    culture_urine:$('#culture_urine').val(),
				    culture_data: culture_data
				},
			dataType:'JSON',
			success:function(data){
				$('#culture_id').val(data.culture_id);
				Swal.fire({icon:'success',toast:true,timer:3000,position:'bottom-right',showConfirmButton:false,title:data.msg});
			}
			 
		 });
	 });
 
    $('#resultTextModal').on('show.bs.modal',function(){
		$('.chk-all-text').change(function(e){
			e.preventDefault();
			var checked = $(this).is(':checked');
			if(checked){
				$('.chk-one-text').prop('checked',true);
			}else{
				$('.chk-one-text').prop('checked',false);
			}
		});
		
	});
	
	 $('#resultTextModal').on('hide.bs.modal',function(){
		 var culture_urine = $('#culture_urine').val();
		 var texts = [];
		 $('#tst_results_tbl .chk-one-text:checked').each(function (index, obj) {
		    var txt = $(this).closest("tr").find("td:eq(1)").text();
			texts.push(txt);
		 });
		 //console.log(texts);
		 
		 if (texts.length > 0) {
              if (culture_urine !== "") {
                   $('#culture_urine').val(culture_urine + "\n" + texts.join("\n"));
               } else {
                  $('#culture_urine').val(texts.join("\n"));
              }
           }
		 
	 });
	 
	 $('#print_culture').click(function(e){
		 e.preventDefault();
	     var culture_id = $('#culture_id').val();
		 if(culture_id=='0'){
			 Swal.fire({icon:'warning',html:'{{__("Please save culture data before printing")}}',customClass:'w-auto'});
			 return false;
		 }
		 
		 $.ajax({
			url:'{{route("lab.visit.printCultureData",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',culture_id:culture_id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download='Culture#'+culture_id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
	 });
 
 
 }
 
 
 });
</script>
<script>
 $(function(){ 	
	
	//pay and refund fillCurrency
	$('#valdollar').val('1.00');
	$('#vallira').val('0.00');
	$('#rvaldollar').val('1.00');
	$('#rvallira').val('0.00');
	$("#dvaldollar").val("1.00");
	//Define variables for input elements
	
	$('#profile_tests').select2();
  if($('#example-table').length){	
	
	
	
	var editCheck = function(cell){
    //cell - the cell component for the editable cell
    //get row data
	var status = '{{isset($order)?$order->status:''}}';
    var data = cell.getRow().getData();
	var user_type='{{auth()->user()->type}}';
   
    return status !='V' &&  user_type=='2'; // only allow the name cell to be edited 
    }
	
	
	function getCellData(cell,value) {
    var result_value = value;
	var row = cell.getRow();
	var dt = row.getData();
    var result_id = dt.id;
    var test_id = dt.test_id;
    var type = dt.test_type;
	var field_num=dt.field_num;
	var is_formula = 'N';
    var all_tests = '';

    if (type === 'F' || type=='C') {
        is_formula = type;
        all_tests = table.getData().map(function (row) {
            return {
                test_id: row.test_id,
                result: row.result
            };
        });
    }

    $.ajax({
        url: '{{route("lab.visit.checkResultVal",app()->getLocale())}}',
        method: "post",
        data: {
            id: result_id,
            result: result_value,
            test_id: test_id,
            is_formula: is_formula,
			field_num: field_num,
            all_tests: all_tests,
            _token: '{{csrf_token()}}'
        },
        dataType: "JSON",
        success: function(data) {
            if (type === 'F') {
                row.update({ "result": data.formula_result });
            }
            row.update({ "result_status": data.state });
            row.update({ "sign": data.sign });
                        
			var ref_range = row.getData().ref_range;
			//var is_printed = (row.getData().is_printed=='Y')?'Y':'N';
			var field_nb = row.getData().field_num;
            
			
			if(field_nb == null || field_nb==''){
			 row.update({ "field_num": data.field_num });
			}
			if(ref_range == null || ref_range==''){
				if (data.min !== "" && data.max !== "") {
					row.update({ "ref_range": data.min + "-" + data.max });
				}
				if (data.min === "" && data.max !== "") {
					row.update({ "ref_range": "<" + data.max });
				}
				if (data.min !== '' && data.max === '') {
					row.update({ "ref_range": ">=" + data.max });
				}
				if (data.min === "" && data.max === "") {
					row.update({ "ref_range": "" });
				}
			}
            var dec_pts = dt.dec_pts;
            if (dec_pts != null && dec_pts !== '' && Number(result_value) && result_value!=null && result_value!='') {
                row.update({ "result": parseFloat(cell.getValue()).toFixed(dec_pts) });
            }

           
			cell.getElement().classList.remove("bginfo");
            cell.getElement().classList.remove("bgorange");
            cell.getElement().classList.remove("bgred");
            cell.getElement().classList.remove("bgblue");

            switch (data.state) {
                case 'H':
                    cell.getElement().classList.add("bgred");
                    break;
                case 'L':
                    cell.getElement().classList.add("bgblue");
                    break;
                case 'N':
                    cell.getElement().classList.add("bginfo");
                    break;
                case 'PL':
                case 'PH':
                    cell.getElement().classList.add("bgorange");
                    break;
                default:
                    cell.getElement().classList.add("bginfo");
            }
        }
    });
}
	
	
	var inputEditor = function(cell, onRendered, success, cancel, editorParams) {
    // Create and style editor
    var editor = document.createElement("input");
    editor.setAttribute("type", "text");
    editor.style.padding = "5px"; // Adjust padding
    editor.style.width = "calc(100% - 10px)"; // Adjust width
    editor.style.border = "1px solid #ccc"; // Add border
    editor.style.borderRadius = "5px"; // Add border radius
    editor.style.backgroundColor = "#f9f9f9"; // Add background color
    editor.style.color = "#333"; // Add text color
    editor.style.fontFamily = "Arial, sans-serif"; // Add font family
    editor.style.fontSize = "14px"; // Add font size
    editor.style.boxSizing = "border-box"; // Ensure box sizing includes border

    // Set value of editor to the current value of the cell
    editor.value = cell.getValue();
    
	editor.addEventListener("focus", function(e) {
        editor.select();
    });
    // Prevent click events on the editor from propagating to the cell
    editor.addEventListener("click", function(e) {
        e.stopPropagation();
    });

    // Prevent editor from closing when clicked
    editor.addEventListener("mousedown", function(e) {
        e.stopPropagation();
    });

    // Attach editor to cell element
    cell.getElement().appendChild(editor);

    // Focus editor after attaching it to cell element
    editor.focus();
   
   editor.addEventListener("mousemove", function(e) {
        e.stopPropagation();
    });
    
	function handleSuccess(value) {
        // Assuming getCellData is a placeholder for your operation
        getCellData(cell, value); // You need to implement getCellData
        success(value);
        // Remove focus from editor after success
        editor.blur();
    }

    editor.addEventListener("change", function() {
        handleSuccess(editor.value);
    });

    // Handle Enter key press
    editor.addEventListener("keydown", function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSuccess(editor.value);
        }
    });

    return editor;
};

      

var table = new Tabulator("#example-table", {
    
	ajaxURL:"{{route('lab.visit.getResult',[app()->getLocale()])}}", //ajax URL
    ajaxParams: function(){
        var filter_order=$('#order_id').val();
	   
		return {filter_order:filter_order};
           },
	height:"540px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:false, //enable pagination.
    layout:"fitData" ,
	layoutColumnsOnNewData:true,
	movableRows:false,
	printHeader:"<h3 style='text-align:center;'>Clinical Laboratory And Blood Bank Departement</h3>",
 	groupBy:"group_name",
	groupHeader: function(value, count, data, group){
        return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
    groupHeaderPrint: function(value, count, data, group){
        return value + "<span style='color:#d00; margin-left:10px;'>(" + count + ")</span>";
    },
	groupClosedShowCalcs:true,
	downloadConfig:{
        columnHeaders:true, //do not include column headers in downloaded table
        columnGroups:true, //do not include column groups in column headers for downloaded table
        rowGroups:true, //do not include row groups in downloaded table
        columnCalcs:true, //do not include column calcs in downloaded table
        dataTree:false, //do not include data tree in downloaded table
    },
	printConfig:{
        columnHeaders:true, //do not include column headers in printed table
        columnGroups:true, //do not include column groups in column headers for printed table
        rowGroups:true, //do not include row groups in printed table
        columnCalcs:true, //do not include column calcs in printed table
        dataTree:false, //do not include data tree in printed table
        formatCells:true, //show raw cell values without formatter
    },
	
	columns:[
		{title:"{{__('Order')}}", field:"position"},
		{title:"{{__('#')}}", field:"id",visible:false},
		{title:"{{__('Group')}}", field:"group_name",headerFilter:"input",visible:false},
		{title:"{{__('Name')}}", field:"test_name",headerFilter:"input"},
		{title:"{{__('Result')}}",width:200, field:"result",editor: inputEditor,formatter:function(cell, formatterParams, onRendered){
			       var row = cell.getRow();
				   var data = row.getData();
				   var state = data.result_status; //get state of cell
			       var dec_pts = data.dec_pts;
			  
						cell.getElement().style.fontSize = "18px";
						cell.getElement().style.fontWeight = "bold";
						cell.getElement().classList.remove("bginfo");
						cell.getElement().classList.remove("bgorange");
						cell.getElement().classList.remove("bgred");
						cell.getElement().classList.remove("bgblue");
					   
					   switch(state){
						      case 'H':  cell.getElement().classList.add("bgred"); break;
							  case 'L':  cell.getElement().classList.add("bgblue"); break;
							  case 'N':  cell.getElement().classList.add("bginfo"); break;
							  case 'PL': case 'PH':  cell.getElement().classList.add("bgorange"); break;
							  default:   cell.getElement().classList.add("bginfo"); 
					   }
				
				if(dec_pts !='' && dec_pts!=null && Number(cell.getValue()) && cell.getValue()!=null && cell.getValue()!=''){
					return parseFloat(cell.getValue()).toFixed(dec_pts);
				}else{
					return cell.getValue();
				}
			},headerFilter:"input"
			},
		    
		{title:"{{__('Sign')}}", field:"sign",headerFilter:"input"},
		{title:"{{__('Reference Range')}}", field:"ref_range",headerFilter:"input"},
		{title:"{{__('Previous Result')}}",field:"prev_result",headerFilter:"input",visible:false},
		{title:"{{__('Status')}}", field:"status",visible:false},
		{title:"{{__('Test type')}}", field:"test_type",visible:false},	
		{title:"{{__('Result Status')}}",field:"result_status",visible:false},
		{title:"{{__('Test id')}}",field:"test_id",visible:false},
		{title:"{{__('Formula id')}}",field:"formula_num",visible:false},
		{title:"{{__('Is printed')}}",field:"is_printed",visible:false},
		{title:"{{__('Dec Pts')}}",field:"dec_pts",visible:false},
		{title:"{{__('Calc Result')}}",field:"calc_result",visible:false},
		{title:"{{__('Calc Unit')}}",field:"calc_unit",visible:false}

      ],
    });
	

  


  
  
  
  
  
  
  
  
  }
  
 if($('#bill-table').length){ 
 
 var table1 = new Tabulator("#bill-table", {
    
	ajaxURL:"{{route('lab.visit.getBill',[app()->getLocale()])}}", //ajax URL
    ajaxParams: function(){
        var filter_order=$('#order_id').val();
		return {filter_order:filter_order};
           },
	height:"400px",
    placeholder:"No Data Available", //display message to user on empty table
	placeholderHeaderFilter:"No Matching Data",
	pagination:false, //enable pagination.
    layout:"fitData",
	layoutColumnsOnNewData:true,
	movableRows:false,
	
	downloadConfig:{
        columnHeaders:true, //do not include column headers in downloaded table
        columnGroups:true, //do not include column groups in column headers for downloaded table
        rowGroups:true, //do not include row groups in downloaded table
        columnCalcs:true, //do not include column calcs in downloaded table
        dataTree:false, //do not include data tree in downloaded table
    },
	printConfig:{
        columnHeaders:true, //do not include column headers in printed table
        columnGroups:true, //do not include column groups in column headers for printed table
        rowGroups:true, //do not include row groups in printed table
        columnCalcs:true, //do not include column calcs in printed table
        dataTree:false, //do not include data tree in printed table
        formatCells:true, //show raw cell values without formatter
    },
	
	columns:[
		{title:"{{__('#')}}", field:"bill_num",visible:false},
		{title:"{{__('Test Name')}}",field:"bill_name",headerFilter:"input"},
		{title:"{{__('Bill')}}", field:"clinic_bill_num"},
		{title:"{{__('Date')}}", field:"bill_datein"},
		{title:"{{__('Test')}}", field:"bill_code",headerFilter:"input",visible:false},
		{title:"{{__('CNSS')}}", field:"cnss",headerFilter:"input"},
		{title:"{{__('Cost USD')}}",field:"test_cost",visible:false,formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"$",
                symbolAfter:"p",
				},headerFilter:"input"}, 
		{title:"{{__('Nb L')}}",field:"bill_quantity",visible:false,headerFilter:"input"},
		{title:"{{__('Price USD')}}",field:"bill_price",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"$",
				symbolAfter:"p",
				negativeSign:false,
				},formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"$",
                symbolAfter:"p",
				},headerFilter:"input"}, 
		{title:"{{__('Price LBP')}}",field:"lbill_price",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"ل.ل",
				symbolAfter:"p",
				negativeSign:false,
				},
		        formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				symbol:"ل.ل",
				symbolAfter:"p",
				negativeSign:false,
				},headerFilter:"input"},
       {title:"{{__('Price EURO')}}",field:"ebill_price",topCalc:"sum",topCalcParams:{precision:2},topCalcFormatter:"money",topCalcFormatterParams:{
				decimal:".",
				thousand:",",
				symbol:"€",
				symbolAfter:"p",
				negativeSign:false,
				},formatter:"money", formatterParams:{
				decimal:".",
				thousand:",",
				negativeSign:false,
				symbol:"€",
                symbolAfter:"p",
				},headerFilter:"input"}, 
	   {formatter:"buttonCross",  title:"Action", headerSort:false, cellClick:function(e, cell){
	                  var status=$('#order_status').val();
					  
					  if(status=='V')
					  {return false;}
					  else{
					  if(confirm('Are you sure you want to delete this entry?'))
		                  cell.getRow().delete();
					    }
						},frozen:true
               },
	      ],
    });
 }
	$("#input_files").fileinput({
		'language': 'en',
		'maxFileCount': 10,
		'maxFileSize':'3072',
		'hideThumbnailContent':true,
		'showPreview':true,
		'showUpload': true,
		'showRemove ':true,
		'showBrowse':true,
		'browseOnZoneClick' : (status!='V')?true:false,
		'allowedFileExtensions': ["jpg","jpeg","png","gif","bmp","tiff","svg","webp","pdf"]
	});
	  
	var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	}).length;
	
	$('#selected_tests').text('Selected tests are : '+nb);
	$('#chosen_tests').empty();
   	$('.test').each(function(){ 
		if($(this).is(':checked')){
		  $('#chosen_tests').append('<div class="label-size col-12" id="'+$(this).val()+'"><b>'+$(this).attr('data-name')+'</b></div>');
		  }
       });
	
	
	$('.select2_data').select2({theme:'bootstrap4',width:'resolve'});
			
     
				 $('.visits_tab').click(function (e) {
								e.preventDefault();
								$(this).tab('show');
							
							});

				$('.visits_tab').on("shown.bs.tab", function (e) {
					var id = $(e.target).attr("href");
					
					localStorage.setItem('visitsTab', id)
				});

				var visitsTab = localStorage.getItem('visitsTab');
				
				if (visitsTab != null) {
					$('a[data-toggle="tab"][href="' + visitsTab + '"]').tab('show');
				}else{
					$('a[data-toggle="tab"][href="#orders"]').tab('show');
				}
    
   
  
	$("#selectLab").change(function()
        {
			var ext_lab = $('#selectLab').val();
			var order_id = $('#order_id').val();
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("UpdateLab",app()->getLocale())}}',
			   data:{"order_id":order_id,"ext_lab":ext_lab},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){		   
			 }
            });
		});	
  
   
   $('#search_test').keyup(function(){
       var group_num = $('#filter_group').val();
	   var category_num = $('#filter_category').val();
	   var text = $(this).val().trim().toLowerCase();
		
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{group_num:group_num,category_num:category_num,search:text,type:'search'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                const arr = data.tests;
				//console.log(arr);
				 // Hide all content class element
                 // Hide all content class element
                $('.content-chk ').hide();
				$.each(arr,function(index,val){
				 	 //var text = val.toLowerCase();	
					 $('.content-chk  .content-lbl').each(function(){
					  if($(this).find('.test').val() == val ){
						 //console.log(text);
						 $(this).closest('.content-chk').show();
						
						 }
					  });
				 
			         });					 
                  
					
			 }
            });
	  //$('#filter_category').val('');
	  //$('#filter_category').trigger('change.select2');
	  //$('#filter_group').val('');
	  //$('#filter_group').trigger('change.select2');
	 // Search text
      //var text = $('#search_test').val().toLowerCase();
	  // Search 
	 // Hide all content class element
     //$('.content-chk').hide();
	 //$('.content-chk  .content-lbl').each(function(){
          //if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
              // $(this).closest('.content-chk').show();
                //}else{
				 //$(this).closest('.content-chk').hide();	
				//}
           //});
				
   });
   
   $('#cancel_order').click(function(e){
	  e.preventDefault();
	     $('#tests_form').trigger("reset");
	     $('#search_test').val('');
		 $('#filter_category').val('');
		 $('#filter_category').trigger('change.select2');
		 $('#filter_group').val('');
		 $('#filter_group').trigger('change.select2');
		 
	     let arr =[];
		 arr = $.parseJSON('{{json_encode($profile_ids)}}');
		 //console.log(arr);
		 $('#profile_tests').val(arr).change();
		 $('#profile_tests').trigger('change.select2');
		 $('#chosen_tests').empty();
		 
		 $('.test').each(function(){ 
			
			if($(this).is(':checked')){
			  $(this).parent().removeClass('btn-light'); 
			  $(this).parent().addClass('btn-success');
			 
			    $('#chosen_tests').append('<div class="label-size col-12" id="'+$(this).val()+'"><b>'+$(this).attr('data-name')+'</b></div>');
			  
			  }else{
				$(this).parent().addClass('btn-light'); 
			    $(this).parent().removeClass('btn-success');
				$('#chosen_tests').find('#'+$(this).val()).remove();
								 
			  }
		 
		 });	
	  
	 var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	    }).length;
	
	  $('#selected_tests').text('Selected tests are : '+nb);
      
   });
   
   $('#save_order').click(function(e){
	  e.preventDefault();
	  var nb = $('.test').filter(':checked').length;
	  if(nb==0){
		  Swal.fire({icon:'error',text:'Please choose at least one test',customClass:'w-auto'});
		  return false;
	  }else{
		Swal.fire({
           text: 'Do you want to save the changes?',
           showCancelButton: true,
           confirmButtonText: 'OK',
         }).then((result) => {
			  if (result.isConfirmed) {
				
				const  arr= [];
				const  arr_bill= [];
				$('.test').each(function(){
					if( $(this).is(':checked') ){
						arr.push($(this).val());
					   if($(this).attr('data-group') == 'Y')	
						arr_bill.push($(this).val()+'GR');
					   else
						arr_bill.push($(this).val()+'NG');
					}
				});
				
				
				
				
				$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
				$.ajax({
				  url:'{{route("lab.visit.saveOrder",app()->getLocale())}}',
				   data:{
					   order_id:$('#order_id').val(),
					   clinic_num:'{{$clinic->id}}',
					   patient_num:'{{$patient->id}}',
					   ext_lab: $('#selectLab').val(),
					   tests: arr,
					   tests_bill:arr_bill
					     },
				   type: 'post',
				   dataType: 'json',
				   success: function(data){
					 
					 Swal.fire({ toast:true,title: data.msg,icon: "success",position:'bottom-right',timer:1000,
					 showConfirmButton:false }).then(function(){
						 window.location.href=data.location;
						 });
					 
				 }
				});  
			  } else{
				
				return false;
			  }

		 })
		
		
		
	  }
   });
   
   $('#go_results').click(function(e){
	   e.preventDefault();
	   $('a[data-toggle="tab"][href="#bill"]').tab('show');
   });
   
   $('#saveResults').click(function(e){
	    e.preventDefault();
		var data = table.getData();
		var all_tests = table.getData().map(function (row) {
							return {
								test_id: row.test_id,
								result: row.result
							};
							});
    
		var order_id = $('#order_id').val();
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("lab.visit.saveResults",app()->getLocale())}}',
			data:{order_id:order_id,all_tests:all_tests,data:JSON.stringify(data),fixed_comment:$('#fixed_comment').val()},
			type: 'post',
			dataType: 'json',
			success: function(data){
				 var order_id = $('#order_id').val();
				 table.setData("{{route('lab.visit.getResult',app()->getLocale())}}", {filter_order:order_id});
				 $('#validateResults').removeAttr('disabled'); 
				 Swal.fire({toast:true,title:data.msg,timer:3000,position:"bottom-right",icon:"success",showConfirmButton:false});
			     
			}
		});
   });
   
   $('#saveBill').click(function(e){
	e.preventDefault();
	var bill_id=document.getElementById("bill_id").value;
	var data = table1.getData();
	var order_id = $('#order_id').val();
	//console.log(bill_id);
	$.ajax({
		type:'POST',
		data:{_token: '{{ csrf_token() }}',bill_id:bill_id,data:JSON.stringify(data),order_id:order_id},
		url: '{{route("lab.visit.saveBill",app()->getLocale())}}',
		success: function(data){
			var order_id = $('#order_id').val();
			table1.setData("{{route('lab.visit.getBill',app()->getLocale())}}", {filter_order:order_id});
			$('#balance').val(data.balance);
			$('#tpay').val(data.tpay);
			$('#trefund').val(data.trefund);
			Swal.fire({toast:true,title:data.msg,icon:'success',position:'bottom-right',showConfirmButton:false,timer:3000});
		}
		});	
      });	
   
   $('#doneResults').click(function(e){
	   e.preventDefault();
		var order_id = $('#order_id').val();
		$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("lab.visit.validateResults",app()->getLocale())}}',
			data:{order_id:order_id,type:'done'},
			type: 'post',
			dataType: 'json',
			success: function(data){
				Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 window.location.href = data.location;
				 });
		     }
		});
   });
   
   $('#validateResults').change(function(e){
	    e.preventDefault();
		var order_id = $('#order_id').val();
		var is_valid= $(this).is(':checked')?'Y':'N';
	    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
		$.ajax({
			url:'{{route("lab.visit.validateResults",app()->getLocale())}}',
			data:{order_id:order_id,is_valid:is_valid,type:'validate'},
			type: 'post',
			dataType: 'json',
			success: function(data){
				 Swal.fire({toast:true,title:data.msg,timer:1000,position:"bottom-right",icon:"success",showConfirmButton:false}).then(function(){
					 window.location.href = data.location;
				 });
				 
			}
		});
   });
   
   $('#printResults').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
		$.ajax({
			url:'{{route("lab.visit.printResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download=('result.pdf');
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
   });
   
   $('#printOrder').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
		$.ajax({
			url:'{{route("lab.visit.printOrder",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		    xhrFields: { responseType: 'blob'},
		   }).then(function(data){					
					var blob = new Blob([data]);
					var link=document.createElement('a');
					link.href=window.URL.createObjectURL(blob);
					
					link.download='Request#'+order_id+'.pdf';
					link.click();
					Swal.fire({title:'{{__("Downloaded successfully")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
			    });
   });
   
   $('#sendResults').click(function(e){
	   e.preventDefault();
	   var order_id = $('#order_id').val();
		$.ajax({
			url:'{{route("lab.visit.sendResults",app()->getLocale())}}',
			beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); 
							},
			data:{'_token': '{{ csrf_token() }}',order_id:order_id},
			type: 'post',
		   dataType:'json',
		   success: function(data){
					if(data.success){	
						Swal.fire({title:data.success,icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.warn){	
						Swal.fire({title:data.warn,icon:"warning",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});					
					   }
					 if(data.error){
						 Swal.fire({text:data.error,icon:'error',customClass:'w-auto'});
					 }				   
				}
		    });
   });
  
	
  
  $('#filter_category').change(function(){
	  
	       var category_num = $(this).val();
		   var group_num = $('#filter_group').val();
		   var text = $('#search_test').val().trim().toLowerCase();
		   
		   /*if(category_num=='' && group_num=='' && text==''){
				
				 $('.content-chk  .content-lbl').each(function(){
                      $(this).closest('.content-chk').show();
				
                  });
				return false;
			}*/
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{category_num:category_num,search:text,type:'cat'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                 $('#filter_group').empty();
				 $('#filter_group').html(data.html);
				 const arr = data.tests;
				 $('.content-chk ').hide();
				 
				 $.each(arr,function(index,val){
				 	 //var text = val.toLowerCase();	
					 $('.content-chk  .content-lbl').each(function(){
					  if($(this).find('.test').val() == val ){
						 //console.log(text);
						 $(this).closest('.content-chk').show();
						
						 }
					  });
				 
			         });					 
			   }
            });
	       
      });
	  


$('#profile_tests').on('select2:select', function (e) {
     // Do something
     var data = e.params.data;
     var id = data.id;
	  $.ajax({
			
			url: '{{route("lab.visit.getProfileTests",app()->getLocale())}}',
			type: 'post',
			dataType: 'json',
			data:{_token:'{{csrf_token()}}',id:id},
			success: function(data){
				//console.log(data.tests);
				var arr = data.tests;
				$('.test').each(function(){
					if(arr.includes($(this).val()) && !$(this).is(':checked')){
						$(this).prop('checked',true);
						$(this).parent().removeClass('btn-light'); 
			            $(this).parent().addClass('btn-success');
				        $('#chosen_tests').append('<div class="label-size col-12" id="'+$(this).val()+'"><b>'+$(this).attr('data-name')+'</b></div>');
					}
				});
				
				var nb = $('.test').filter(function(){
					return $(this).is(':checked') 
				}).length;
				
				$('#selected_tests').text('Selected tests are : '+nb);
			 }
		  });
	  
		  });

$('#profile_tests').on('select2:unselect', function (e) {
     // Do something
	 var data = e.params.data;
     var id = data.id;
	  $.ajax({
			
			url: '{{route("lab.visit.getProfileTests",app()->getLocale())}}',
			type: 'post',
			dataType: 'json',
			data:{_token:'{{csrf_token()}}',id:id},
			success: function(data){
				//console.log(data.tests);
				
				var arr = data.tests;
				$('.test').each(function(){
					if(arr.includes($(this).val()) && $(this).is(':checked')){
						$(this).prop('checked',false);
						$(this).parent().removeClass('btn-success'); 
			            $(this).parent().addClass('btn-light');
                        $('#chosen_tests').find('#'+$(this).val()).remove();					
						}
				});
				
				var nb = $('.test').filter(function(){
					return $(this).is(':checked') 
				}).length;
				
				$('#selected_tests').text('Selected tests are : '+nb);
			 }
		  });
	  
		  });
		  
	  
	  $('#filter_group').change(function(){
	  
	       var group_num = $(this).val();
		   var category_num = $('#filter_category').val();
		   var text = $('#search_test').val().trim().toLowerCase();
		  
		  /*if(group_num == '' && category_num=='' && text==''){
				 $('.content-chk  .content-lbl').each(function(){
                      $(this).closest('.content-chk').show();
				
                  });
				return false;
			}*/
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
				 });
			$.ajax({
			  url:'{{route("lab.visit.filterGroup",app()->getLocale())}}',
			   data:{group_num:group_num,category_num:category_num,search:text,type:'grp'},
			   type: 'post',
			   dataType: 'json',
			   success: function(data){
                const arr = data.tests;
				//console.log(arr);
				 // Hide all content class element
                 // Hide all content class element
                $('.content-chk ').hide();
				$.each(arr,function(index,val){
				 	 //var text = val.toLowerCase();	
					 $('.content-chk  .content-lbl').each(function(){
					  if($(this).find('.test').val() == val ){
						 //console.log(text);
						 $(this).closest('.content-chk').show();
						
						 }
					  });
				 
			         });					 
                  
					
			 }
            });
	       
      });
  
 $('#selectcurrencyp').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyp").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#valdollar").val(data.pdolar);
			$("#valamount").val('0.00');
			$("#vallira").val('0.00');
			
		 }
      });
});



$('#selectcurrencyr').on('change', function()
{
    //alert($("#selectcode").val()); //or alert($(this).val());
    current_val=$("#selectcurrencyr").val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#rvaldollar").val(data.pdolar);
			$("#valamountrefund").val('0.00');
			$("#rvallira").val('0.00');
			
		 }
      });
});

$('#selectcurrencyd').on('change', function()
{
    current_val=$(this).val();
  	$.ajax({
		
		url: '{{route("fillCurrency",app()->getLocale())}}',
		   type: 'get',
		  dataType: 'json',
		  data:{"selectcurrency":current_val},
		  success: function(data){
			 $("#dvaldollar").val(data.pdolar);
			$("#valamountdiscount").val('0.00');
			$("#dvallira").val('0.00');
			
		 }
      });
});

$('#valamount').on('change input', function()
{
valamount=$("#valamount").val();
valdollar=$("#valdollar").val();
vallira=valamount*valdollar;	
$("#vallira").val(vallira);
});

$('#valamountrefund').on('change input', function()
{
valamount=$("#valamountrefund").val();
valdollar=$("#rvaldollar").val();
vallira=valamount*valdollar;	
$("#rvallira").val(vallira);
});

$('#valamountdiscount').on('change input', function()
{
valamount=$("#valamountdiscount").val();
valdollar=$("#dvaldollar").val();
vallira=valamount*valdollar;	
$("#dvallira").val(vallira);
}); 
 
 
 
 
 
 });
 
 
  
</script>
<script>
function chkbx_fn(el){
	 
	 if($(el).is(':checked')){
			  		  
				//el is a test then add it
				$(el).parent().removeClass('btn-light'); 
			    $(el).parent().addClass('btn-success');
				$('#chosen_tests').append('<div class="label-size col-12" id="'+$(el).val()+'"><b>'+$(el).attr('data-name')+'</b></div>');
				  
		  }else{
			   $(el).parent().addClass('btn-light'); 
			   $(el).parent().removeClass('btn-success');
			   $('#chosen_tests').find('#'+$(el).val()).remove();
			   
				}	

			 
			  
		
	  
	  var nb = $('.test').filter(function(){
		return $(this).is(':checked') 
	}).length;
	  
	  $('#selected_tests').text('Selected tests are : '+nb);
}


function nbCheck(elem,value) {
     var v = parseFloat(value);
    if (isNaN(v)) {
        $(elem).val('');
		
    } else {
       if(v>0) { $(elem).val('+'+v.toFixed(2)); }
	   if(v==0) { $(elem).val('0.00'); }
	   if(v<0) { $(elem).val(v.toFixed(2));}
    }
}
// THE SCRIPT THAT CHECKS IF THE KEY PRESSED IS A NUMERIC OR DECIMAL VALUE.
function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

function paymentbill(){
var bill_id=$('#bill_id').val();
$("#valamount").val("0.00");
$.ajax({
	url:'{{route("GetPay",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id},
     success: function(data){
	 $("#payamount").val(data.sumpay);
     $("#refamount").val(data.sumref);
	 $("#balancepay").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#myTablePay').empty();
	 $('#myTablePay').html(data.html1);
	 }
    });	
 $('#paymentModal').modal('show');
 var totalpay=(document.getElementById("totalf").value);
 $("#totalpay").val(totalpay);
}

function savepay(){
var x = document.getElementById("myTablePay").rows[0].cells.length;
var arr1 = new Array();
for(ii=0;ii<document.getElementById("myTablePay").rows.length;ii++){
arr1[ii]={"CODE":document.getElementById("myTablePay").rows[ii].cells[0].innerHTML,"DATE":document.getElementById("myTablePay").rows[ii].cells[1].innerHTML,"TYPE":document.getElementById("myTablePay").rows[ii].cells[2].innerHTML,"CURRENCY":document.getElementById("myTablePay").rows[ii].cells[3].innerHTML,"PRICE":document.getElementById("myTablePay").rows[ii].cells[4].innerHTML,"RATE":document.getElementById("myTablePay").rows[ii].cells[5].innerHTML,"TOTAL":document.getElementById("myTablePay").rows[ii].cells[6].innerHTML};	
}
	
var id_lab ='{{$clinic->id}}';
var bill_id = $('#bill_id').val();
var order_id='{{isset($order)?$order->id:0}}';
var balance=(document.getElementById("balancepay").value).replaceAll(",", "");;	
var myjson1=JSON.stringify(arr1);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_lab,"bill_id":bill_id,data:myjson1,"balance":balance,"order_id":order_id},
   url: '{{route("lab.visit.save_pay",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			  Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
         $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $('#paymentModal').modal('hide');	
	     $("#balance").val(data.nbalance);
	     $("#balancepay").val(data.nbalance);
          }
	 
	 if(data.warning){
				   Swal.fire({ 
				  "text":data.warning,
				  "icon":"warning",
				  "customClass": "w-auto"});
			  
				} 

	}
 });
}
function refundbill(){
var bill_id=document.getElementById("bill_id").value;
$("#valamountrefund").val("0.00");
$.ajax({
	url:'{{route("GetRef",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"REF"},
     success: function(data){
	 $("#payamountrefund").val(data.sumpay);
     $("#refamountrefund").val(data.sumref);
 	 $("#balancerefund").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	 $('#myTableRef').empty();
	 $('#myTableRef').html(data.html);
	
}
    });	
$('#refundModal').modal('show');
var totalrefund=(document.getElementById("totalf").value);
$("#totalref").val(totalrefund);
}

function saverefund(){
var x = document.getElementById("myTableRef").rows[0].cells.length;
var arr2 = new Array();
for(iii=0;iii<document.getElementById("myTableRef").rows.length;iii++){
   arr2[iii]={"CODE":document.getElementById("myTableRef").rows[iii].cells[0].innerHTML,"DATE":document.getElementById("myTableRef").rows[iii].cells[1].innerHTML,"TYPE":document.getElementById("myTableRef").rows[iii].cells[2].innerHTML,"CURRENCY":document.getElementById("myTableRef").rows[iii].cells[3].innerHTML,"PRICE":document.getElementById("myTableRef").rows[iii].cells[4].innerHTML,"RATE":document.getElementById("myTableRef").rows[iii].cells[5].innerHTML,"TOTAL":document.getElementById("myTableRef").rows[iii].cells[6].innerHTML};	
}
	
var id_facility='{{$clinic->id}}';
var bill_id=document.getElementById("bill_id").value;
var order_id='{{isset($order)?$order->id:0}}';
var balance=document.getElementById("balancerefund").value;	
var myjson2=JSON.stringify(arr2);
 $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,data:myjson2,"balance":balance,"order_id":order_id},
    url: '{{route("lab.visit.save_refund",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
         $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
         $('#refundModal').modal('hide');	
		 var b1=$("#balance").val();
		 var p1=$("#tpay").val();
		 $("#balance").val(data.nbalance);
	     $("#balancerefund").val(data.nbalance);
          }
        if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		  
			} 

	}
 });
 
}
function insRowPay(){
  current_val=$("#selectmethod").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	 current_val=$("#valamount").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input an amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
var x=document.getElementById('myTablePay').insertRow(document.getElementById('myTablePay').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
document.getElementById("cptp").value=parseInt(document.getElementById("cptp").value)+1;
a.innerHTML=document.getElementById("cptp").value;
b.innerHTML=document.getElementById("date_pay").value;
c.innerHTML=$("#selectmethod option:selected").text().trim();
d.innerHTML=$("#selectcurrencyp option:selected").text().trim();
e.innerHTML=parseFloat(document.getElementById("valamount").value).toFixed(2);
f.innerHTML=parseFloat(document.getElementById("valdollar").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("vallira").value).toFixed(2);
h.innerHTML='<input type="button" class="btn btn-delete" id="rowdeletepay'+document.getElementById("cptp").value+'" value="{{__('Delete')}}" onclick="deleteRowPay(this)"/>';	
}
function deleteRowPay(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valpay=parseFloat(document.getElementById("myTablePay").rows[ii].cells[6].innerHTML);
 document.getElementById('myTablePay').deleteRow(ii);
}

function insRowRef()
{
	  current_val=$("#selectmethodrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input a type",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	
	
	 current_val=$("#valamountrefund").val();
  if(parseInt(current_val)==0){
	   Swal.fire({ 
              "text":"Please input an amount",
              "icon":"warning",
			  "customClass": "w-auto"});
		  
		  return ;	
		
			 }	

var x=document.getElementById('myTableRef').insertRow(document.getElementById('myTableRef').rows.length);
var a= x.insertCell(0);
var b= x.insertCell(1);
var c= x.insertCell(2);
var d= x.insertCell(3);
var e= x.insertCell(4);
var f= x.insertCell(5);
var g= x.insertCell(6);
var h= x.insertCell(7);
document.getElementById("cptr").value=parseInt(document.getElementById("cptr").value)+1;
a.innerHTML=document.getElementById("cptr").value;
b.innerHTML=document.getElementById("date_refund").value;
c.innerHTML=$("#selectmethodrefund option:selected").text().trim();
d.innerHTML=$("#selectcurrencyr option:selected").text().trim();
e.innerHTML=parseFloat(document.getElementById("valamountrefund").value).toFixed(2);
f.innerHTML=parseFloat(document.getElementById("rvaldollar").value).toFixed(2);
g.innerHTML=parseFloat(document.getElementById("rvallira").value).toFixed(2);
h.innerHTML='<input type="button" class="btn btn-delete" id="rowdeleteref'+document.getElementById("cptr").value+'" value="{{__('Delete')}}" onclick="deleteRowRef(this)"/>';
}

function deleteRowRef(r){
 var ii=r.parentNode.parentNode.rowIndex;
 var  valref=parseFloat(document.getElementById("myTableRef").rows[ii].cells[6].innerHTML);
 document.getElementById('myTableRef').deleteRow(ii);
}

function discountbill()
{
$("#valamountdiscount").val("0.00");
var bill_id=document.getElementById("bill_id").value;
$.ajax({
	url:'{{route("GetRef",app()->getLocale())}}',
    type:'GET',
    data:{"_token": "{{ csrf_token() }}","bill_id":bill_id,"type":"DIS"},
     success: function(data){
	 $("#payamountdiscount").val(data.sumpay);
    $("#refamountdiscount").val(data.sumref);
 	 $("#balancediscount").val(data.balance.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	$('#discountModal').modal('show');
}
    });	
var totaldiscount=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totaldiscount").val(totaldiscount);
var totald=(document.getElementById("totalf").value);
//var balancerefund=parseFloat(document.getElementById("balance").value);
$("#totald").val(totald);

}

function savediscount()
{
	

 
//var id_facility=document.getElementById("id_facility").value;
//var date_pay=document.getElementById("date_pay").value;	
//var valamount=document.getElementById("valamount").value;
//var selectmethod=document.getElementById("selectmethod").value;	
var id_facility ='{{$clinic->id}}';
var bill_id = $('#bill_id').val();
var order_id='{{isset($order)?$order->id:0}}';
 // var bill_id=document.getElementById("bill_id").value;
  var balance=document.getElementById("balancerefund").value;	
  var valamountdiscount=document.getElementById("dvallira").value;
  $.ajax({
    type:'POST',
    data:{"_token": "{{ csrf_token() }}","id_facility":id_facility,"bill_id":bill_id,"balance":balance,'valamountdiscount':valamountdiscount},
   url: '{{route("SaveDiscount",app()->getLocale())}}',
	success: function(data){
		if(data.success){	
			 Swal.fire({ 
              title:data.success,
			  toast : true,
			  position: 'bottom-right',
			  showConfirmButton:false,
			  timer: 1500,
			  icon:'success'
			  });
         $("#payamountrefund").val(data.sumpay);
         $("#refamountrefund").val(data.sumref);
		 $("#payamount").val(data.sumpay);
         $("#refamount").val(data.sumref);
		 $("#tpay").val(data.sumpay);
         $("#trefund").val(data.sumref);
		  $("#tdiscount").val(data.totaldiscount);
		var b1=$("#balance").val();
		 var p1=$("#tpay").val();

	$("#btnsaverefund").prop("disabled", true);  	
	$("#balance").val(data.nbalance);
	$("#balancerefund").val(data.nbalance);
	$("#discountModal").modal("hide");
}
 if(data.warning){
			   Swal.fire({ 
              "text":data.warning,
              "icon":"warning",
			  "customClass": "w-auto"});
		     return;
			} 

	}
 });

}

function downloadPDF(){	 
   var id=document.getElementById("bill_id").value;	
      var fraction = document.getElementById("totalf").value.split(".");

                if (fraction.length == 2){
                  var tafqeetTotal=tafqeet (fraction[0]) + " فاصلة " + tafqeet (fraction[1]);
                }
                else if (fraction.length == 1){
                    var tafqeetTotal=  tafqeet (fraction[0]);
                }
      $.ajax({
           url: '{{route("downloadPDFBilling",app()->getLocale())}}',
		   beforeSend: function() { 
		                 Swal.fire({
							title: '',
							html: '{{__("Please wait, downloading...")}}',
							timerProgressBar: true,
							  didOpen: () => {
								Swal.showLoading()
							   }
		                    }); },
		   data: {'_token': '{{ csrf_token() }}','id':id,'tafqeetTotal':tafqeetTotal},
           type: 'post',
		   xhrFields: { responseType: 'blob'},
           }).then(function(data){
					
				//pdf is downloaded return back
 			var blob = new Blob([data]);
			var link=document.createElement('a');
			link.href=window.URL.createObjectURL(blob);
			link.download='Bill#'+id+'.pdf';
			link.click();
			Swal.fire({title:'{{__("Bill Downloaded")}}',icon:"success",toast:true,showConfirmButton: false,timer:3000,position :"bottom-right"});
	//window.location.href="{{route('lab.billing.index',app()->getLocale())}}";
		
			    });
	
	
	
	}


   
</script>
<script>
$(function() {
    Fancybox.bind("[data-fancybox]", {
         fitToView: true, // if you require exact size (400x300)
        closeClick: true,
        openEffect: 'elastic',
        closeEffect: 'elastic'
       
        });
});
</script>
<script>
        flatpickr('#date_pay', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
		flatpickr('#date_refund', {
            allowInput : true,
			altInput: true,
			enableTime: true,
            altFormat: "Y-m-d H:i",
            dateFormat: "Y-m-d H:i"
        });
    </script>
@endsection