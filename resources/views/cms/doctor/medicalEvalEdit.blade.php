<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>

.form-control
{
    font-weight: bolder;
}

.cms-font
{
    font-weight: bolder;
}

.table
{
 font-weight: bolder;
}

img{border:none;}
#preview{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:5px;
	display:none;
	color:#fff;
	z-index:100;
	}




.table-result{ margin-top:-10px; margin-bottom:20px; z-index:0;}	
#TransactionListTable_filter{ width:100%; padding-left:5px; padding-right:5px;}
td.group {
    background-color: #D1CFD0;
    border-bottom: 2px solid #A19B9E;
    border-top: 2px solid #A19B9E;
}
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
.row-package:not(.header-package) {
    color: #337AB7;
    text-decoration: none;
    cursor: pointer;
    position: relative;
}
.row-package:not(.header-package)::after {
    content: "View Package Composition";
    position: absolute;
    top: -40%;
    left: 50%;
    transform: translateX(-50%);
	color: white;
    background-color: rgba(169, 169, 169, 0.8);
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
	white-space: nowrap;
    max-width: none;
}

.row-package:not(.header-package):hover::after {
    opacity: 1;
}

.row-cis:not(.header-company) {
    color: #337AB7;
    text-decoration: none;
    cursor: pointer;
    position: relative;
}

.row-cis:not(.header-company)::after {
    content: "View Client Information Sheet";
    position: absolute;
    top: -40%;
    left: 50%;
    transform: translateX(-50%);
	color: white;
    background-color: rgba(169, 169, 169, 0.8);
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
	white-space: nowrap;
    max-width: none;
}

.row-cis:not(.header-company):hover::after {
    opacity: 1;
}


#PatientName .tt-menu {
  max-height: 250px;
  overflow-y: auto;
}
.tt-input.loading {
    background: transparent url('/images/ajax-loader.gif') no-repeat scroll right center content-box !important;
}
table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            border-collapse: collapse;
            /* text-align: center; */
            padding: 0px;
        }
        th {
            background-color: #f2f2f2;
        }.
        @media (min-width: 992px) {
            .modal-dialog {
    width: 100%;
  }
}
.custom-size-modal .modal-dialog {
    width: 80vw; /* Custom width: 80% of the viewport */
    height: 80vh; /* Custom height: 80% of the viewport */
    max-width: none; /* Override any max-width constraints */
    margin: 0 auto; /* Center the dialog */
}

</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
		<li class="active"><a href="{{ url(session('userBUCode').'/doctor/evaluation') }}" class="no-waiting">Company Evaluation <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                   <li class="active"><a  href="{{ url(session('userBUCode').'/doctor/evaluation/company').'/'.$_GET['companyid'].'/edit' }}" class="no-waiting">{{ $compaName}}  <span class="badge" style="top:-9px; position:relative;"></span></a></li>
		<li class="active"><a href="#">Edit <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
         <form id="formQueueEdit" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}" autocomplete="off">
		<input type="hidden" name="reUpdate" value="reUpdate">
		<input type="hidden" name="_queueid" value="{{$datas->Id}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="panel panel-primary">
            <div class="panel-heading cms-font" style="line-height:12px;">Patient Info </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
                            <label class="bold ">Patient's Name<font style="color:red;">*</font></label>
                        </div>
                        <div class="col-sm-10 col-md-10">
                            <div class="input-group">
                                <input type="hidden" name="IdPatient" value="{{ $datas->IdPatient }}" />
                                <div id="PatientName">
                                <input type="text" class="typeahead form-control cms-font" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" required="required" readonly="readonly">
                                   </div>
                                <div class="input-group-btn">
                                    @if(strpos(session('userRole'), '"ldap_role":"[DOCTOR]"') !== false)
                                        <button class="newbtn btn btn-warning hide" type="button" disabled="disabled"> New </button>
                                    @endif
                                        <button class="editbtn btn btn-success " type="button"> View </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 pad-0-md text-right-md ">
                            <label class="bold ">Date of Birth<font style="color:red;">*</font></label>
                        </div>
                        <div class="col-sm-3 col-md-3">
                            <input type="text" class="form-control" name="DOB" value="{{ $datas->DOB && $datas->DOB !== '0000-00-00' && preg_match('/\d/', $datas->DOB) ? date('d-M-Y', strtotime($datas->DOB)) : '' }}" placeholder="Date of Birth" readonly="readonly" required="required">
                        </div>
                        <div class="col-sm-1 col-md-1 pad-0-md text-right-md">
                            <label class="bold">Gender<font style="color:red;">*</font></label>
                        </div>
                        <div class="col-sm-3 col-md-3">
                            <input type="text" class="form-control" name="Gender" value="{{ $datas->Gender }}" placeholder="Gender" readonly="readonly" required="required">
                        </div>
                        <div class="col-sm-1 col-md-1 text-right-md">
                            <label class="bold">Age</label>
                        </div>
                        <div class="col-sm-2 col-md-2">
                            <input type="text" class="form-control" name="Age" value="{{ $datas->AgePatient }}" placeholder="Age" readonly="readonly" required="required">
                        </div>
                    </div>
                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 text-right-md">
                            <label class="bold">Notes</label>
                        </div>
                        <div class="col-sm-10 col-md-10">
                            <textarea class="form-control" name="Notes" value="" placeholder="Notes" readonly="readonly"></textarea>
                        </div>
                    </div>          
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                    <!-- START -->
                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
                            <label class="bold ">PID</label>                            
                        </div>
                        <div class="col-sm-4 col-md-4 pad-0-md">
                            <input type="text" class="form-control" value="{{ $datas->PatientCode }}" placeholder="System Generated" readonly="readonly">
                        </div>
                        <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
                            <label class="bold">Queue No.</label>
                        </div>
                        <div class="col-sm-4 col-md-4 pad-0-md">
                            <input type="text" class="form-control" placeholder="System Generated" value="{{ $datas->Code }}" readonly="readonly">
                        </div>
                    </div>

                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md ">
                            <label class="bold ">Date Time</label>
                        </div>
                        <div class="col-sm-4 col-md-4 pad-0-md">
                            <input type="text" class="form-control " name="Date" value="{{ date('d-M-Y H:i:s',strtotime($datas->DateTime)) }}" placeholder="Date" readonly="readonly">
                        </div>
                        <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
                            <label class="bold ">Queue Status.</label>
                        </div>
                        <div class="col-sm-4 col-md-4 pad-0-md">
                            <input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
                        </div>
                    </div>
                    <div class="row form-group row-md-flex-center">
                        <div class="col-sm-2 col-md-2 text-right-md">
                            <label class="bold">Company</label>
                        </div>
                        <div class="col-sm-12 col-md-10 pad-0-md">
                            <input type="text" class="form-control" name="" value="{{$transactionType[0]->NameCompany ?? ''}}" placeholder="Company" readonly="readonly" required="required">
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">ASSESMENT AND RECOMMENDATION</div>
					<div class="panel-body" >
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:70%; text-align: center;">Tests</th>
                                            <th>Status</th>
                                            <th>Class</th>
                                            <th style="width:10%;">PDF Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>                       
                                        <!-- First row for VITAL SIGNS -->
                                        <tr>
                                            <td>
                                                <input type="hidden" class="count" name="count" value="">
                                                 VS, VA, Medical History and PE
                                            </td>
                                            <td>
                                                @if(!empty($AssesAndRec[0]) && $AssesAndRec[0]->ItemCode == 'VITALS') Evaluated  @elseif( isset($VitalSigns[0]->IdQueue)) For Eval @else For Vitals @endif
                                            </td>
                                            <td>
						@foreach($AssesAndRec as $item)
							@if($item->ItemCode == "VITALS")
								@foreach(json_decode($item->Class ?? '{}', true) as $class)
									{{ $class }}
								@endforeach
						    @endif
						@endforeach
                                            </td>
                                            <td>
                                                <button name="viewVital" style="color: black" class="form-control viewVital btn btn-warning"> View Vital Sign </button>
                                            </td>
                                        </tr>
                                        <!-- Rows for transactionData -->
                                        @foreach($transactionData as $index => $test)
                                        <tr>
                                            <td>
                                                <input type="hidden" class="count1" name="count1" value="">
                                                <input type="hidden" class="ItemDescription" name="ItemDescription" value="{{ $test->ItemDescription }}">
                                                {{ $test->ItemDescription }}
                                            </td>
                                            <td>@php
                                                $found = false;
                                                @endphp
                                                @foreach($AssesAndRec as $item)
                                                    @if($test->ItemCode == $item->ItemCode)
                                                        @php $found = true; @endphp
                                                        Evaluated
                                                    @endif
                                                @endforeach
                                                
                                                @if(!$found)
                                                    {{ $test->Status }}
                                                @endif
                                            </td>
					<td>
					 @foreach($AssesAndRec as $item)
						@if($test->ItemCode == $item->ItemCode)
							@foreach(json_decode($item->Class ?? '{}', true) as $class)
								{{ $class }}
							@endforeach
						@endif
					@endforeach
					</td>
                                            <td>
                                                <input type="hidden" class="ItemCode" value="{{ $test->ItemCode }}" name="ItemCode"> 
                                                    <input type="hidden" name="transid" class="transid" value="{{ $test->IdTransaction }}"> 
                                                <button class="btn btn-warning _link" style="color: black; font-weight: bold;" @if( $test->Status == 'Completed' || $test->Status == 'Released') @else disabled  @endif>
                                                    {{ $test->ItemDescription == 'VITAL SIGNS' ? 'View Vital Sign' : 'View PDF Result' }}
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    
                                   
                                </table>
                                <div class="panel panel-success">
                                <div class="panel-heading" style="line-height:12px;">Summary</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <textarea class="form-control" rows="15" readonly>
                                                    @php 
                                                        $summary = []; 
                                                    @endphp
                                                    @foreach($AssesAndRec as $test)
                                                        @php 
                                                            $assessments = json_decode($test->Assessment, true); 
                                                            $recommendations = json_decode($test->Recommendation, true); 
                                                        @endphp
                                                        @if(!empty($assessments) && !empty($recommendations))
                                                            @foreach($assessments as $key => $assessment)
                                                                @php  
                                                                    $num = preg_replace('/[^0-9]/', '', $key); 
                                                                    $recKey = "Recommendation" . $num; 
                                                                    $recommendation = $recommendations[$recKey] ?? 'No Recommendation'; 
                                                                    $summary[] = trim($assessment) . ' - ' . trim($recommendation);
                                                                @endphp
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                    {{ implode("\n\n", $summary) }}
                                                    </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>                          
                            </div>
                        </div>
                    </div> 
                <div class="panel panel-primary" style="margin-bottom: 50px">
                    <div class="panel-heading" style="line-height:12px; text-align:center" >MEDICAL EXAMINATION RATING</div>
                        <div class="panel-body" >
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="text-align:center">
                                        <h5>MEDICAL EXAMINATION RATING SYSTEM</h5>
                                        Occupational Safety and Health Standards <br>
                                        Department of Labor and Employment
                                    </div>
                                    <label class="bold">
                                        <input type="checkbox" class="check" name="classA" id="" @if(in_array('Class A', $selectedClasses)) checked="checked" @else disabled="disabled" @endif>
                                        Class A - Physically fit for any work.
                                    </label>
                                    <br>
                                     <label class="bold">
                                        <input type="checkbox" class="check" name="classB" id="" @if(in_array('Class B', $selectedClasses)) checked="checked" @else disabled="disabled" @endif>
                                        Class B - Physically under-develop or with correctible defects, (error of refraction, dental caries, defective hearing, and other similar defects) but otherwise fit to work.
                                    </label>
                                    <label class="bold">
                                        <input type="checkbox" class="check" name="classC" id="" @if(in_array('Class C', $selectedClasses)) checked="checked" @else disabled="disabled" @endif>
                                        Class C - Employable but owning certain impairments or conditions (heart disease, hypertension, anatomical defects) requires special placement or limited duty in specified or selected assignment requiring follow-up treatment/periodic evaluation.
                                    </label>
                                    <label class="bold">
                                        <input type="checkbox" class="check" name="classD" id="" @if(in_array('Class D', $selectedClasses)) checked="checked" @else disabled="disabled" @endif>
                                        Class D - Unfit or unsafe for any type of employment (active PTB, advanced heart disease with threatened failure, malignant hypertension, and other similar illnesses).
                                    </label>
                                    <label class="bold">
                                        <input type="checkbox" class="check" name="pending" id="" @if(in_array('Pending', $selectedClasses)) checked="checked" @else disabled="disabled" @endif>
                                        Pending - Incomplete test/s and/or result/s that need further evaluation. These cases may be re-classified fit or unfit after completion or futher evaluation by the Company Physician.
                                    </label>
                                 </div>                          
                            </div>
                        </div>
                    </div>       
          	</div>
		</form>		        
		</div>
    </div>
    <div class="navbar-fixed-bottom" >
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
		<div class="col-xs-6">
			@if( $datas->QueueStatus  === "Evaluated")	
				<button class="viewPrintEvalbtn btn btn-info col-xs-4 col-sm-4 col-md-4 col-lg-4"  style="border-radius:0px; line-height:29px;" type="button"> Print Evaluation </button>
			@else
				<button class=" btn btn-info col-xs-4 col-sm-4 col-md-4 col-lg-4 disabled" disabled="disabled"  style="border-radius:0px; line-height:29px;" type="button"> Print Evaluation </button>
			@endif
			@if( $datas->QueueStatus  === "Evaluation Draft")	
				<button class="evaluatedlbtn btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4"  style="border-radius:0px; line-height:29px;" type="button"> Tag as Evaluated </button>
			@else
				<button class=" btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4 disabled" disabled="disabled"  style="border-radius:0px; line-height:29px;" type="button"> Tag as Evaluated </button>
			@endif
			@if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') !== false)
				<button class="reGeneratebtn btn btn-danger col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" type="button"> Get Latest Lab Result </button>				
			@endif
		</div>
		<div class="col-xs-6">
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
			<button class="col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="visibility:hidden;"></button>
			<a href="{{ url(session('userBU').'/cms/payment/'.$datas->Id.'/edit') }}" class="btn btn-warning col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="  border-radius:0px; line-height:29px; visibility:hidden;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> </a>
				@if( $datas->QueueStatus  !== "Evaluated")
					<a class="savebtn saving btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save as Draft</a>					
				@else
					<a class="btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4 disabled" disabled="disabled" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save as Draft</a>					
				@endif
			@endif	
		</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }
var calculateAge = function(birthday) {
    dob = new Date(birthday);
   var today = new Date();
   var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));

    return age;
};
var pdfResultsModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="hpdf-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('pageId')+"/edit/?transid="+dialog.getData('transid')+"&ItemCode="+dialog.getData('ItemCode');
		$message.load(pageToLoad);
		console.log(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
    cssClass: 'custom-size-modal',
	data: {
		'pageToLoad': "{{ '/doctor/evaluation/evalModal/evalModalView' }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn',
		label: 'Save',
		action: function (modalRef){
            if( parent.required($('form')) ) return false;
            var assessments = [];
            var recommendations = [];
            var findings    = [];
            var Class    = [];
            // Loop through all Assessment inputs
            $('input[name="Assessment[]"]').each(function() {
                assessments.push($(this).val());
            });
            console.log(assessments);
            // Loop through all Recommendation textareas
            $('textarea[name="Recommendation[]"]').each(function() {
                recommendations.push($(this).val());
            });
            $('input[name="findings[]"]').each(function() {
                findings.push($(this).val());
            });
            $('input[name="class[]"]').each(function() {
                Class.push($(this).val());
            });
            parent.postData("{{'/doctor/evaluation/evalModal/evalModalView/'.$datas->Id.'/?ItemCode=' }}"+modalRef.getData('ItemCode'),
            {
                'Assessment'            : assessments
                ,'Recommendation'       : recommendations
                ,'Findings'             : findings
                ,'Class'                : Class
                ,'_token'				: $('input[name=_token]').val()
				,'_method'				:'PUT'
            },
			function($data)
			{ 
                location.reload();
            });  
        }
    }]
});
var patientAddModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		pageToLoad: ''
	},
	animate: false,
	closable: false,
	buttons: [
	{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	}
	]
	});


var patientEvalModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
    cssClass: 'custom-size-modal',
	data: {

		pageToLoad: ''
	},
	animate: false,
	closable: false,
	buttons: [
	{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},
	{
		id: 'btnsave',
		cssClass: 'btn-success actionbtn',
		label: 'Save',
		action: function (modalRef){
            if( parent.required($('form')) ) return false;
            var assessments = [];
            var recommendations = [];
            var findings    = [];
            var Class    = [];
            // Loop through all Assessment inputs
            $('input[name="Assessment[]"]').each(function() {
                assessments.push($(this).val());
            });
            console.log(assessments);
            // Loop through all Recommendation textareas
            $('textarea[name="Recommendation[]"]').each(function() {
                recommendations.push($(this).val());
            });
            $('input[name="findings[]"]').each(function() {
                findings.push($(this).val());
            });
            $('input[name="class[]"]').each(function() {
                Class.push($(this).val());
            });
            parent.postData("{{'/doctor/evaluation/evalModal/evalModalView/'.$datas->Id.'/?ItemCode=VITALS' }}",
            {
                
                'Assessment'            : assessments
                ,'Recommendation'       : recommendations
                ,'Findings'             : findings
                ,'Class'                : Class
                ,'_token'				: $('input[name=_token]').val()
				,'_method'				:'PUT'
            },
			function($data)
			{ 
                location.reload();
            }); 
        }
    }
	]
	});


$(document).ready(function(e) {

    function calculateBMI() {
    var height = parseFloat($('#height').val());
    var weight = parseFloat($('#weight').val());

    if (height > 0 && weight > 0) {
        var heightInMeters = height / 100;
        var bmi = weight / (heightInMeters * heightInMeters);
        var bmiCategory = getBMICategory(bmi);

        $('#bmi').val(bmi.toFixed(2));
        $('#bmi-category').text(bmiCategory);
    } else {
        $('#bmi').val('');
        $('#bmi-category').text('Category');
    }
}

function getBMICategory(bmi) {
    if (bmi < 18.5) {
        return 'Underweight';
    } else if (bmi >= 18.5 && bmi < 22.99) {
        return 'Normal';
    } else if (bmi >= 23 && bmi < 24.99) {
        return 'Overweight';
    } else if (bmi >= 25 && bmi < 29.99) {
        return 'Obese I';
    }else {
        return 'Obesity II';
    }
}

$('#height, #weight').on('input', function() {
    calculateBMI();
});
$(".classification").each(function () {
        let classificationData = $(this).data("classification"); // Get data attribute

        if (classificationData) {
            try {
                let parsedData = JSON.parse(classificationData); // Parse JSON
                let formattedText = Object.values(parsedData).join(", "); // Extract values & join

                $(this).text(formattedText); // Insert into div
            } catch (error) {
                console.error("Invalid JSON format:", error);
            }
        }
    });
// Calculate BMI on page load if values are already filled
calculateBMI();
						

    $('.check').on('change', function() {
    if ($(this).is(':checked')) {
        $('.check').not(this).prop('checked', false);
		}
	});
	
	$('.editbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - View");
		patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
    $('.viewVital').on('click', function(e){
        patientEvalModal.setTitle('Assessment And Recommendation (Vital Signs And PE)');
		patientEvalModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientEvalModal.setData("pageToLoad", "{{ '/doctor/evaluation/evalModal/evalModalView' }}/"+$('input[name="_queueid"]').val()+"/?ItemCode=VITALS");
		patientEvalModal.realize();
		patientEvalModal.open();
		e.preventDefault();
    });
    $('._link').on('click', function (e) {
    e.preventDefault();

    const transid = $(this).closest('tr').find('.transid').val();
    const ItemCode = $(this).closest('tr').find('.ItemCode').val();     
    const count = $(this).closest('tr').find('.count1').val();
    pdfResultsModal.setTitle("Assessment And Recommendation" + ' ' + "( "+$(this).closest('tr').find('.ItemDescription').val()+" )");
    pdfResultsModal.setData("pageId", $('input[name="_queueid"]').val());
    pdfResultsModal.setData("transid", transid);
    pdfResultsModal.setData("ItemCode", ItemCode);
    pdfResultsModal.realize();
    pdfResultsModal.open();
});
	$('.savebtn').on('click',function(e){
		var selectedClasses = [];
		$('input[name="classA"]:checked').each(function() {
			selectedClasses.push("Class A");
		});
		$('input[name="classB"]:checked').each(function() {
			selectedClasses.push("Class B");
		});
		$('input[name="classC"]:checked').each(function() {
			selectedClasses.push("Class C");
		});
		$('input[name="classD"]:checked').each(function() {
			selectedClasses.push("Class D");
		});
		$('input[name="pending"]:checked').each(function() {
			selectedClasses.push("Pending");
		});

		var Class = selectedClasses.join(', ');

		var Bloodpresure1 = $('input[name=bloodpresure]').val()+" / "+$('input[name=overbloodpresure]').val()

		if( parent.required($('form')) ) return false;
		parent.postData(
			"{{ '/doctor/evaluation/company/patient/'.$datas->Id }}",
			{  
				'Class'                 : Class
				,'_token'               : $('input[name=_token]').val()
				,'_method'              :'PUT'
			},
			function($data)
			{  
				var hyperlink = document.createElement('a');
				hyperlink.href = '/doctor/evaluation/company/patient/'+$data+'/edit/?companyid='+{{ $_GET['companyid']}};
				var mouseEvent = new MouseEvent('click', {
					view: window,
					bubbles: true,
					cancelable: true
				});

				hyperlink.dispatchEvent(mouseEvent);
				(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);

				e.preventDefault();
			}
		);
		e.preventDefault();
	});
	
	$('.evaluatedlbtn').on('click',function(e)
	{
		if ("{{ $datas->QueueStatus }}" == "Evaluation Draft")
		{
			parent.postData(
				"{{ '/doctor/evaluation/company/patient?id='.$datas->Id }}",
				{  
					'_token'               : $('input[name=_token]').val(),
					'idQueue'		: '{{ $datas->Id }}',
					'_method'              :'POST'
				},
				function($data)
				{  
					var hyperlink = document.createElement('a');
					hyperlink.href = '/doctor/evaluation/company/patient/'+$data+'/edit/?companyid='+{{ $_GET['companyid']}};
					var mouseEvent = new MouseEvent('click', {
						view: window,
						bubbles: true,
						cancelable: true
					});

					hyperlink.dispatchEvent(mouseEvent);
					(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);

					e.preventDefault();
				}
			);
		
		}
		else
		{
			alert("You must save as draft first!");
		}
	
	});
	
	$('.viewPrintEvalbtn').on('click',function(e){
		if($(this).attr('disabled') !== 'disabled' )
		{
			var id = '{{ $datas->Id }}';
			var userId = '{{ Auth::user()->id }}';

			var hyperlink = document.createElement('a');
			hyperlink.href = "http://{{$_SERVER['SERVER_ADDR']}}:8080/jasperserver/flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FDoctors&reportUnit=%2Freports%2FDoctors%2FPE_From_v1&j_username=report&j_password=DnCMSReport&output=pdf&appointmentID="+id+"&myID="+userId;
								
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			//hyperlink.dispatchEvent(mouseEvent);
			//window.open(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			window.open(hyperlink, '_blank', 'location=yes,height=800,width=6000,scrollbars=yes,status=yes');
			//e.preventDefault();
			waitingDialog.hide();
		}
	});
	
	$('.reGeneratebtn').on('click', function(e){

		var queueId=$('input[name="_queueid"]').val();
		var csrfToken = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
		    type: 'POST',
		    url: '{{ route('regeneratepdf') }}',
		    headers: {
			'X-CSRF-TOKEN': csrfToken,
		    },
		    data: { idQueue : queueId },
		    success: function (response) {
					//alert(response);
					alert('Re Generate pdf has been submitted');
					location.reload(true);
		    },
		    error: function (error) { 
			console.error('Please check you connection');
		    }
		});
	});
	

	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	
	
	
});
</script>
@endsection