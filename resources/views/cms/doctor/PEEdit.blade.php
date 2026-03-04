<!--@extends('app')-->
@section('style')
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">
<style>
.panel-warning{
	color: #d19b3d;
}
.form-control
{
    font-weight: bolder;
}

.cms-font
{
    font-weight: bolder;
}

/* .table
{
 font-weight: bolder;
} */

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


#PatientName .tt-menu {
  max-height: 250px;
  overflow-y: auto;
}
.tt-input.loading {
    background: transparent url('/images/ajax-loader.gif') no-repeat scroll right center content-box !important;
}
.textarea-container {
            padding-left: 3rem;
            padding-right: 3rem;
        }
.textarea-container textarea {
	width: 100%;
	max-width: 100%;
	resize: none;
}
.nav-button {
            margin-right: 10px;
            margin-bottom: 5px; /* If you want vertical spacing as well */
        }
.medical-history-table {
	width: 100%;
	border-collapse: collapse;
}
.medical-history-table, .medical-history-table th, .medical-history-table td {
	border: 1px solid black;
}
.medical-history-table th, .medical-history-table td {
	padding: 8px;
	text-align: left;
}
.medical-history-table th {
	background-color: #f2f2f2;
}
.table_input, .PE_normal{
	border: none; 
	outline: none;
	background: none;
}
.form-section {
            background-color: #f7f7f7;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
}
.form-section h4 {
	margin-top: 0;
	margin-bottom: 15px;
}
.form-section .checkbox-inline, .form-section .radio-inline {
	margin-left: 0;
}
.underline-input {
	border: none;
	border-bottom: 1px solid black;
	outline: none;
	border-radius: 0;
	width: 100%;
	padding-left: 2px;
}
.form-group-inline {
	display: flex;
	align-items: center;
}
.form-group-inline > * {
	margin-right: 10px;
}
.checkbox-inline {
    display: flex;
    align-items: center; /* Aligns checkbox and text vertically */
    gap: 5px; /* Adds spacing between checkbox and label */
}
.table-bordered > tbody > tr > td {
    vertical-align: middle;
}
textarea[readonly] {
    cursor: not-allowed;
}

/* Change cursor for disabled inputs */
input:disabled {
    cursor: not-allowed;
}
table {
	/* border: 1px solid black; */
	border-collapse: collapse;
	width: 100%;
}
th, td {
	/* border: 1px solid black; */
	border-collapse: collapse;
	text-align: center;
	padding: 0px;
}
th {
	background-color: #f2f2f2;
}



@media (min-width: 767px){
	.m-top100 {
		margin-top: -100px !important;
	}
	.m-top170 {
		margin-top: -170px !important;
	}
	.m-top90 {
		margin-top: -90px !important;
	}
	.m-top20 {
		margin-top: -20px !important;
	}
}
.webcam { cursor:pointer; }
 /* Hide the spinners in Chrome, Safari, Edge, and Opera */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
	-webkit-appearance: none;
	margin: 0;
}

    /* Hide the spinners in Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
#TransactionListTable tbody tr.selected {
    background-color: #d1ecf1; /* Light blue background */
    color: #0c5460; /* Dark text color for better contrast */
}
.bordered-icon {
display: inline-block;
padding: 5px; /* Adjust the padding as needed */
border: 1px solid transparent; /* Adjust the border color and style as needed */
border-radius: 5px; /* Adjust the border radius as needed */
margin: 2px; /* Adjust the margin as needed */
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
.pdfResult{
	background-color: #cfe2f3;
}
.pdfResult:hover{
	background-color: #3d85c6;
}

</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb hide">
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ '/kiosk/consultationqueue' }}">Doctor's Consultation <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
                    <li class="active"><a href="#">Edit <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
    	<div class="col-menu-15 create-queue">
         <form id="formQueueEdit" class="form-horizontal" role="form" method="POST"  autocomplete="off">
		<input type="hidden" name="reUpdate" value="reUpdate">
		<input type="hidden" name="_queueid" value="{{$datas->Id}}">
		<input type="hidden" name="_queueCode" value="{{$datas->QCode}}">
		<input type="hidden" name="_idDoctor" value="{{$datas->IdDoctor}}">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
        	<div class="panel panel-primary">
		<div class="col-menu-15">
			@if (count($errors) > 0)
				<div class="alert alert-danger">
					<strong>Whoops!</strong> There were some problems with your input.<br><br>
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
		</div>
				<div class="panel-heading cms-font" style="line-height:12px;"> Info </div>
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
							<div class="col-sm-2 col-md-2 pad-left-1-md text-right-md">
								<label class="bold ">PID</label>                            
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->PatientCode }}" placeholder="System Generated" readonly="readonly">
							</div>
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
								<label class="bold">Queue No.</label>
                            </div>
                            <div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" placeholder="System Generated" value="{{ $datas->QCode }}" readonly="readonly">
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
								<label class="bold ">Status</label>
                            </div>
                            <div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 text-right-md">
								<label class="bold">Company</label>
                            </div>
                            <div class="col-sm-10 col-md-10 pad-0-md">
								<input type="text" class="form-control" name="" value="{{$datas->NameCompany ?? ''}}" placeholder="Company" readonly="readonly" required="required">
							</div>
						</div>
                    </div>
				</div>
			</div>
			
				
				<div class="panel panel-primary"  style="margin-bottom: 50px">
					<div class="panel-heading " style="line-height:12px;">
						<input type="button" data-target="section-1" name="pastmed" class="btn btn-primary nav-button pastmed active" value="I. PAST MEDICAL & SURGICAL">
						<input type="button" data-target="section-2" name="peronal_social" class="btn btn-primary nav-button peronal_social" value="II. PERSONAL/SOCIAL">
						
						<input type="button" data-target="section-3" name="obstetrics" class="btn btn-primary nav-button obstetrics" value="III. OBSTETRICS & GYNECOLOGICAL">
						
						<input type="button" data-target="section-4" name="family" class="btn btn-primary nav-button family" value="IV. FAMILY HISTORY">
						<input type="button" data-target="section-5" name="physical" class="btn btn-primary nav-button physical" value="V. PHYSICAL EXAMINATION"> 
						<input type="button" data-target="section-6" name="laboraroty" class="btn btn-primary nav-button laboraroty hide" value="VI. LABORATORY TEST AND DIAGNOSTICS PROCEDURES" >
						<input type="button" data-target="section-7" name="assessment" class="btn btn-primary nav-button assessment hide" value="VII. ASSESSMENT AND RECOMMENDATION" >
					</div>
					
					<div class="panel-body">
						<div class="col-lg-12 ">
							<input type="hidden" placeholder="Required feild on PAST MEDICAL & SURGICAL HISTORY" required name="pastmedical">
							<div id="section-1" class="form-section pastmed">
								<div style="text-align: justify">
								<h4>I. PAST MEDICAL & SURGICAL HISTORY (current medications, past diseases, hospitalizations, operations) <span style="color:red">*</span> 
									<label style="float: right;"><input type="checkbox" name="unremarkable"><i style="color:blue" class="small"> Unremarkable</i></label>
								</h4>
								</div>
								<table class="medical-history-table">
									<thead>
										<tr>
											<th>ILLNESS</th>
											<th>DATE OF DIAGNOSIS / REMARKS</th>
											<th>ILLNESS</th>
											<th>DATE OF DIAGNOSIS / REMARKS</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											
											<td>Liver / Gallbladder disease</td>
											<td>
												<textarea class="form-control table_input" name="liverglad" id="" cols="20" rows="1" maxlength="160" placeholder="Liver / Gallbladder disease" required>{{$PEdata[0]->LiverGallbladderDisease ?? ''}}</textarea>
											</td>
											<td>Diabetes Mellitus</td>
											<td>
												<textarea class="form-control table_input" name="diabetisM" id="" cols="20" rows="1" maxlength="160" placeholder="Diabetes Mellitus" required>{{$PEdata[0]->DiabetesMellitus ?? ''}}</textarea>

											</td>
										</tr>
										<tr>
											<td>Heart Disease</td>
											<td>
												<textarea class="form-control table_input" name="heartDisease" id="" cols="20" rows="1" maxlength="160" placeholder="Heart Disease" required>{{$PEdata[0]->Heartdisease ?? ''}}</textarea>
											</td>
											<td>Chronic Headache/Migraine</td>
											<td>
												<textarea class="form-control table_input" name="ChronicHeadache" id="" cols="20" rows="1" maxlength="160" placeholder="Chronic Headache/Migraine" required>{{$PEdata[0]->ChronicHeadacheMigraine ?? ''}}</textarea>
											</td>
										</tr>
										<tr>
											<td>Asthma / Allergy</td>
											<td>
												<textarea class="form-control table_input" name="asthmaAllergy" id="" cols="20" rows="1" maxlength="160" placeholder="Asthma / Allergy" required>{{$PEdata[0]->AsthmaAllergy ?? ''}}</textarea>
											</td>
											<td>Hypertension</td>
											<td>
												<textarea class="form-control table_input" name="Hypertension" id="" cols="20" rows="1" maxlength="160" placeholder="Hypertension" required>{{$PEdata[0]->Hypertension ?? ''}}</textarea>
											</td>
										</tr>
										<tr>
											<td>Tuberculosis</td>
											<td>
												<textarea class="form-control table_input" name="Tuberculosis" id="" cols="20" rows="1" maxlength="160" placeholder="Tuberculosis" required>{{$PEdata[0]->Tuberculosis ?? ''}}</textarea>
											</td>
											<td>Kidney Disease</td>
											<td>
												<textarea class="form-control table_input" name="KidneyDisease" id="" cols="20" rows="1" maxlength="160" placeholder="Kidney Disease" required>{{$PEdata[0]->KidneyDisease ?? ''}}</textarea>
											</td>
										</tr>
										<tr>
											<td>Ear/Nose/Throat Disorder</td>
											<td>
												<textarea class="form-control table_input" name="EarNoseThroat" id="" cols="20" rows="1" maxlength="160" placeholder="Ear/Nose/Throat Disorder" required>{{$PEdata[0]->EarNoseThroatDisorder ?? ''}}</textarea>
											</td>
											<td>Cancer</td>
											<td>
												<textarea class="form-control table_input" name="Cancer" id="" cols="20" rows="1" maxlength="160" placeholder="Cancer" required>{{$PEdata[0]->Cancer ?? ''}}</textarea>
											</td>
										</tr>
										<tr>
											<td>Eye Disorder</td>
											<td>
												<textarea class="form-control table_input" name="EyeDisorder" id="" cols="20" rows="1" maxlength="160" placeholder="Eye Disorder" required>{{$PEdata[0]->EyeDisorder ?? ''}}</textarea>
											</td>
											<td>Sexually Transmitted Disease</td>
											<td>
												<textarea class="form-control table_input" name="SexuallyTransmitted" id="" cols="20" rows="1" maxlength="160" placeholder="Sexually Transmitted Disease" required>{{$PEdata[0]->SexuallyTransmittedDisease ?? ''}}</textarea>
											</td>
										</tr>
									</tbody>
								</table><br>
								<div class="row form-group row-md-flex-center">
									<div class="col-sm-1 col-md-1 pad-right-0-md text-right-md">
										<label class="bold ">Others: </label>
									</div>
									<div class="col-sm-7 col-md-7 pad-1-md">
										<input type="text" class="form-control" name="pastMedOthers" value="{{$PEdata[0]->PastMedOthers ?? ''}}"style="border: none; border-bottom: solid black 1px; outline: none;">
									</div>
								</div>
							</div>
							<!-- II. PERSONAL / SOCIAL HISTORY -->
							<div id="section-2" class="form-section  peronal_social" hidden>
								<h4>II. PERSONAL / SOCIAL HISTORY</h4>
								<div class="row">
									<div class="col-xs-12 col-md-6">
										<input type="hidden" placeholder="Required feild on PERSONAL / SOCIAL HISTORY" name="personalsocial" required>
										<div class="form-group-inline">
											<label>Present Smoker?<span style="color:red">*</span></label>
											
												<label class="checkbox-inline">
													<input type="checkbox" name="presentSmokerY" @if(isset($PEdata[0]) && $PEdata[0]->PresentSmoker === "Y") checked @endif> Yes
												</label>
												<label class="checkbox-inline">
													<input type="checkbox" name="presentSmokerN" @if(isset($PEdata[0]) && $PEdata[0]->PresentSmoker === "N") checked @endif> No
												</label>
											<div class="input-group col-xs-12 col-md-6">
												<input type="number" class="form-control" name="presentSmokerSD" readonly="readonly" value="{{isset($PEdata[0]) && $PEdata[0]->PresentSmokerSticksPerDay}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
												<span class="input-group-addon">stick(s)/day</span>
												<input type="number" class="form-control" name="presentSmokerYears" readonly="readonly" value="{{isset($PEdata[0]) && $PEdata[0]->PresentSmokerYears}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
												<span class="input-group-addon">Year(s)</span>
											</div>
										</div>
										<br>
										<div class="form-group-inline">
											<label>Previous Smoker?<span style="color:red">*</span></label>
											<label class="checkbox-inline">
												<input type="checkbox" name="prevY" @if(isset($PEdata[0]) && $PEdata[0]->PreviousSmoker === "Y") checked @endif> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="prevN" @if(isset($PEdata[0]) && $PEdata[0]->PreviousSmoker === "N") checked @endif> No
											</label>
											<div class="input-group col-xs-12 col-md-6">
												<input type="number" class="form-control" name="previousSmokerSD" readonly="readonly" value="{{ $PEdata[0]->PreviousSmokerSticksPerDay ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
												<span class="input-group-addon">stick(s)/day</span>
												<input type="number" class="form-control" name="previousSmokerYears" readonly="readonly" value="{{ $PEdata[0]->PreviousSmokerYears ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
												<span class="input-group-addon">Year(s)</span>
											</div>
										</div>
									</div>
									<div class="col-xs-12 col-md-6">
										<div class="form-group-inline">
											<label>Present Alcoholic Drinker?<span style="color:red">*</span></label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PresDrinkerY" @if(isset($PEdata[0]) && (!empty($PEdata[0]->PresentAlcoholDrinker) || $PEdata[0]->PresentAlcoholDrinker !== null)) checked @endif> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" style="margin-bottom: 1px" name="PresDrinkerN" @if(isset($PEdata[0]) && empty($PEdata[0]->PresentAlcoholDrinker)) checked @endif> No
											</label>
											
											<div class="input-group col-xs-12 col-md-4">
												<input type="number" class="form-control" name="PresBottle" readonly="readonly" value="{{$PEdata[0]->PresentAlcoholDrinker ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
												<span class="input-group-addon">Bottle(s)/Week</span>
											</div>
										</div>
										<br>
										<div class="form-group-inline">
											<label>Previous Alcoholic Drinker?<span style="color:red">*</span></label>
											<label class="checkbox-inline ">
												<input type="checkbox" name="PrevDrinkerY" @if(isset($PEdata[0]) && (!empty($PEdata[0]->PrevAlcoholDrinker) || $PEdata[0]->PrevAlcoholDrinker !== null)) checked @endif> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PrevDrinkerN" @if(isset($PEdata[0]) && $PEdata[0]->PrevAlcoholDrinker == '') checked @endif> No
											</label>
											<div class="input-group col-xs-12 col-md-4">
												<input type="number" class="form-control" name="PrevDrinkerYear" readonly="readonly" value="{{$PEdata[0]->PrevAlcoholDrinker ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
												<span class="input-group-addon">Year(s)</span>
											</div>
											
										</div>
									</div>
								</div>
								<br>
								<div class="row form-group row-md-flex-center" >
									<div class="col-sm-1 col-md-1 pad-right-0-md text-right-md">
										<label class="bold ">Others: </label>
									</div>
									<div class="col-sm-12 col-md-12 pad-1-md">
										<input type="text" class="form-control" name="PersonalOthers" value="{{$PEdata[0]->PersonalSocialOther ?? ''}}" style="border: none; border-bottom: solid black 1px; outline: none;">
									</div>
								</div>
							</div>

							<!-- III. OBSTETRICS & GYNECOLOGICAL HISTORY (if applicable) -->
							<div id="section-3" class="form-section  obstetrics" hidden>
								<input type="hidden" placeholder="Required feild on OBSTETRICS & GYNECOLOGICAL HISTORY"  name="obstetricsrequired">
								<h4>III. OBSTETRICS & GYNECOLOGICAL HISTORY @if($datas->Gender == "M")(Not applicable) @endif</h4>
								<div class="row">
									<div class="form-group-inline">
										<label class="bold">First Day of Last Menstruation<span style="color:red">*</span></label>
										<div class="input-group">
											<input type="date" class="form-control" name="fDayofMendtruation" value="{{$PEdata[0]->FirstDayofLastMenstruation ?? ''}}" required placeholder="First Day of Last Menstruation">
										</div>
										<label class="bold">Regular?<span style="color:red">*</span></label>
										&nbsp; 
										<input type="hidden" name="mensregular" placeholder="Regular" required>
											<label class="checkbox-inline">
												<input type="checkbox"name="Regular" @if(isset($PEdata[0]) && $PEdata[0]->Regular == "Yes") checked @endif
												> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="NRegular" @if(isset($PEdata[0]) && $PEdata[0]->Regular == "No") checked @endif
												> No
											</label>
											&nbsp;
											<label class="bold">Menarche</label>
										<div class="input-group">
											<input type="number" class="form-control" style="border: none; border-bottom: solid black 1px; outline: none;" name="menarch" value="{{$PEdata[0]->MenopausalAge ?? ''}}" >
										</div>
										<label class="bold">Menopausal_Age</label>
										<div class="input-group">
											<input type="number" class="form-control col-xs-1" style="border: none; border-bottom: solid black 1px; outline: none;" name="MenopausalAge" value="{{$PEdata[0]->MenopausalAge ?? ''}}" >
										</div>
										<div class="col-sm-2 col-md-2 pad-1-md">
											<input type="text" class="form-control hide" name="OBGYNEOthers" value="{{$PEdata[0]->OBGYNEOthers ?? ''}}" style="border: none; border-bottom: solid black 1px; outline: none;">
										</div>
									</div>
								</div>
								<div class="row">
									 <br>
									<div class="col-xs-3 col-sm-3 col-md-">
										<div class="form-group">
											<div class="input-group" style=" align-items: center;">
												<span class="input-group-addon">G</span>
												<input type="number" class="form-control" id="g_value" name="g_value" value="{{$g_value}}" style="max-width: 80px" maxlength="1" oninput="if(this.value.length > 1) this.value = this.value.slice(0, 1);">
												<span class="input-group-addon">P</span>
												<input type="number" class="form-control" id="p_value" name="p_value" value="{{$p_value}}" placeholder="" style="max-width: 80px" maxlength="1" oninput="if(this.value.length > 1) this.value = this.value.slice(0, 1);">
												<span class="input-group-addon"></span>
												<input type="text" class="form-control" id="p1_value" name="p1_value" value="{{$p1_value}}" placeholder="(_-_-_-_)">
											</div>
										</div>
										
								
									</div>
									<div class="col-xs-9 col-sm-9 col-md-9">
										<div class="row form-group row-md-flex-center">
											<div class="col-sm-1 col-md-1 pad-right-0-md text-right-md">
												<label class="bold ">Others: </label>
											</div>
											<div class="col-sm-12 col-md-12 pad-1-md">
												<input type="text" class="form-control" name="OBGYNEOthers" value="{{$PEdata[0]->OBGYNEOthers ?? ''}}" style="border: none; border-bottom: solid black 1px; outline: none;">
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- IV. FAMILY HISTORY -->
							<div id="section-4" class="form-section family " hidden> 
								<input type="hidden" placeholder="Required feild on FAMILY HISTORY" required name="familyhistory">
								<h4>IV. FAMILY HISTORY
									<label style="float: right;"><input type="checkbox" name="family_None"><i style="color:blue" class="small"> None</i></label>
								</h4>
								<div class="form-group-inline">
									<label class="checkbox-inline">
										 Bronchial Asthma
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Bronchial Asthma" value="{{$PEdata[0]->BronchialAsthma ?? ''}}" name="BronchialAsthma">
									
									<label class="checkbox-inline">
										 Goiter
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder=" Goiter" value="{{$PEdata[0]->Goiter ?? ''}}" name="Goiter">
									
									<label class="checkbox-inline">
										 Heart Disease
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Heart Disease" value="{{$PEdata[0]->FHeartDisease ?? ''}}" name="HeartDiseaseF">
									
									<label class="checkbox-inline">
										 Kidney Disease
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Kidney Disease" value="{{$PEdata[0]->KedneyDisease ?? ''}}" name="KidneyDiseaseF">
								</div>
								<div class="form-group-inline">
									<label class="checkbox-inline">
										 Diabetes Mellitus
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Diabetes Mellitus" value="{{$PEdata[0]->FDiabetesMellitus ?? ''}}" name="DiabetesMellitusF">
									
									<label class="checkbox-inline">
										 PTB
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="PTB" value="{{$PEdata[0]->PTB ?? ''}}" name="PTB">
									
									<label class="checkbox-inline">
										Hypertension
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Hypertension" value="{{$PEdata[0]->FHypertension ?? ''}}" name="HypertensionF">
									
									<label class="checkbox-inline">
										 Others
									</label>
									<input type="text" class="underline-input fnone form-control" required placeholder="Others" value="{{$PEdata[0]->FamilyOthers ?? ''}}" name="FamilyOthers">
								</div>
							</div>
							
							 <!-- V. Physical Examination -->
							<div id="section-5" class="form-section physical" hidden>
								<div class="panel panel-primary">
									<div class="panel-heading" style="line-height:12px;">Vital Signs </div>
										<div class="panel-body" >
											<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
												<div class="row">
													<div class="col-md-4 ">
														<label class="bold">Pulse Rate<span style="color:red">*</span></label>
														<div class="input-group">
															<input type="number" class="form-control" placeholder="Pulse Rate" name="pulserate" value="{{$vitals->PulseRate ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>

															<span class="input-group-addon" id="basic-addon2">bpm</span>
														</div>
													</div>
													<div class="col-md-4">
														<label class="bold">Respiratory Rate<span style="color:red">*</span></label>
														<div class="input-group">
															<input type="number" class="form-control" placeholder="Respiratory Rate" name="respiratory"   value="{{$vitals->RespiratoryRate ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
															<span class="input-group-addon" id="basic-addon2">cpm</span>
														</div>
													</div>
													<div class="col-md-4">
														<label class="bold">Temperature<span style="color:red">*</span></label>
														<div class="input-group">
															<input type="number" class="form-control" placeholder="Temperature" name="temperature"  value="{{$vitals->Temperature ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
															<span class="input-group-addon" id="basic-addon2">°C</span>
														</div> 
													</div> 
												</div>
											
												<div class="row" style="padding-top: 8px">
													<div class="col-md-4">
														<label class="bold">Height<span style="color:red">*</span></label>
														<div class="input-group">
															<input type="number" type="text" class="form-control" id="height" placeholder="Height"  name="height"   value="{{$vitals->Height ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
															<span class="input-group-addon" id="basic-addon2">cm</span>
														</div>
													</div>
													<div class="col-md-4">
														<label class="bold">Weight<span style="color:red">*</span></label>
														<div class="input-group">
															<input type="number" class="form-control" id="weight" placeholder="Weight" name="weight"  value="{{$vitals->Weight ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
															<span class="input-group-addon" id="basic-addon2">kg</span>
														</div>
													</div>
													<div class="col-md-4">
														<label class="bold">BMI:</label>
														<div class="input-group">
															<input type="text" class="form-control" id="bmi" style="z-index: -0;" name="bmi"value="{{$vitals->BMI ?? ''}}" readonly="readonly">
															<span class="input-group-addon" id="bmi-category">Category</span>
															<input type="hidden" class="form-control" name="BMICategory">
														</div>
													</div>
												</div> 
											</div> 
											<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
												<div class="row">
													<div class="col-md-9">
														<label class="bold">Blood Pressure<span style="color:red">*</span></label>
														
														<div class="input-group">
															<span class="input-group-addon" id="basic-addon2">1</span>
															<input type="number" class="form-control" name="bloodpresure" value="{{$vitals->BloodPresure ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;" required placeholder="Blood Presure">
															<span class="input-group-addon" id="basic-addon2">/</span>
															<input type="number" class="form-control" name="bloodpresureover"  value="{{$vitals->BloodPresureOver ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;" required placeholder="Blood Presure">
															<span class="input-group-addon" id="basic-addon2">mmHg</span>
														</div>
														<div class="input-group">
															<span class="input-group-addon" id="basic-addon2">2</span>
															<input type="number" class="form-control" name="bloodpresure2"  value="{{$vitals->BloodPresure2 ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;">
															<span class="input-group-addon" id="basic-addon2">/</span>
															<input type="number" class="form-control" name="bloodpresureover2"   value="{{$vitals->BloodPresureOver2 ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;">
															<span class="input-group-addon" id="basic-addon2">mmHg</span>
														</div>
														<div class="input-group">
															<span class="input-group-addon" id="basic-addon2">3</span>
															<input type="number" class="form-control" name="bloodpresure3" value="{{$vitals->BloodPresure3 ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;">
															<span class="input-group-addon" id="basic-addon2">/</span>
															<input type="number" class="form-control" name="bloodpresureover3"  value="{{$vitals->BloodPresureOver3 ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;">
															<span class="input-group-addon" id="basic-addon2">mmHg</span>
														</div>
													</div>
												</div> 
											</div>
										</div>
									</div>
									<div class="panel panel-primary" style="margin-bottom: 50px">
										<div class="panel-heading" style="line-height:12px;">Visual Acuity</div>
											<div class="panel-body" >
											 
												<div class="row">
													<div class="col-md-12">
														<table class="table table-bordered">
															<thead>
																<tr>
																	<th style="width:40%"></th>
																	<th>Far Vision</th>
																	<th>Near Vision</th>
																</tr>
															</thead>
															<tbody>
																<!-- Row for Uncorrected Vision -->
																<tr>
																	<td><label class="text-left bold" style="display: block;">Uncorrected</label></td>
																	<td>
																		<div class="row">
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="uncorectedSpan" style="color: red">*</span>OD 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OD" name="uncorectedOd" value="{{$vitals->UcorrectedOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="uncorectedSpan" style="color: red">*</span>OS 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OS"  name="uncorectedOs" value="{{$vitals->UcorrectedOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
																				</div>
																			</div>
																		</div>
																	</td>
																	<td>
																		<!-- Add Near Vision Uncorrected Fields Here -->
																	   
																		<div class="row">
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2">OD J</span>
																					<input type="number" class="form-control number"  placeholder="Near Vision OD" name="uncorectedNearOd" value="{{$vitals->UncorrectedNearOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2">OS J</span>
																					<input type="number" class="form-control number" placeholder="Near Vision OS" name="uncorectedNearOs" value="{{$vitals->UncorrectedNearOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																		</div>
																	</td>
																</tr>
														
																<!-- Row for Corrected Vision -->
																<tr>
																	<td >
																		<div class="row">
																			<div class="col-xs-12 text-left bold" style="display: block;">
																				<label class="bold">Corrected</label>
																				&nbsp;
																				<label class="bold">
																					<input type="checkbox" name="contactLenses" 
																					@if(isset($vitals->WithContactLens) && $vitals->WithContactLens === "Y") checked @endif > 
																					with Contact Lenses
																				</label> 
																				<label class="bold">
																					<input type="checkbox" name="eyeglasses" 
																				   @if(isset($vitals->WithEyeGlass) && $vitals->WithEyeGlass === "Y") checked @endif > 
																					with Eyeglasses
																				</label>
																			</div>
																		</div>
																	</td>
																	<td>
																		<div class="row">
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="hide corectedSpan" style="color: red">*</span>OD 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OD" name="corectedOd" readonly="readonly" value="{{$vitals->CorrectedOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="hide corectedSpan" style="color: red">*</span>OS 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OS" name="corectedOs" readonly="readonly" value="{{$vitals->CorrectedOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																		</div>
																	</td>
																	<td>
																		<!-- Add Near Vision Corrected Fields Here -->
																		<div class="row">
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2">OD J</span>
																					<input type="number" class="form-control number" placeholder="Near Vision OD J" name="corectedNearOd" readonly="readonly" value="{{$vitals->CorrectedNearOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2">OS J</span>
																					<input type="number" class="form-control number" placeholder="Near Vision OS J" name="corectedNearOs" readonly="readonly" value="{{$vitals->CorrectedNearOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
																				</div>
																			</div>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>                          
												</div>
											</div>
										</div>
										<h4>V. PHYSICAL EXAMINATION
											<input type="hidden" placeholder="Required field on PHYSICAL EXAMINATION" name="physicalexamnination" required>
											<label style="float: right;"><input type="checkbox" name="PE_normal"><i style="color:blue" class="small"> Normal</i></label>
										</h4>
										<table class="table table-bordered">
									<thead>

									</thead>
									<tbody>
									<tr>
										<td class="bold">Skin</td>
										<td>
											<textarea class="form-control  PE_normal" name="Skin" id="" cols="20" rows="1" maxlength="160" placeholder="Skin" required>{{$PEdata[0]->Skin ?? ''}}</textarea>
										</td>
										<td class="bold">Lungs</td>
										<td>
											<textarea class="form-control  PE_normal" name="Lungs" id="" cols="20" rows="1" maxlength="160" placeholder="Lungs" required>{{$PEdata[0]->Lungs ?? ''}}</textarea>
									</tr>
									<tr>
										<td class="bold">Head/Scalp</td>
										<td>
											<textarea class="form-control PE_normal" name="HeadScalp" id="" cols="20" rows="1" maxlength="160" placeholder="Head/Scalp" required>{{$PEdata[0]->HeadScalp ?? ''}}</textarea>
										</td>
										<td class="bold">Heart</td>
										<td>
											<textarea class="form-control PE_normal" name="Heart" id="" cols="20" rows="1" maxlength="160" placeholder="Heart" required>{{$PEdata[0]->Heart ?? ''}}</textarea>
										</td>
									</tr>
									<tr>
										<td class="bold">Eyes</td>
										<td>
											<textarea class="form-control PE_normal" name="Eyes" id="" cols="20" rows="1" maxlength="160" placeholder="Eyes" required>{{$PEdata[0]->Eyes ?? ''}}</textarea>
										</td>
										<td class="bold">Abdomen</td>
										<td>
											<textarea class="form-control PE_normal" name="Abdomen" id="" cols="20" rows="1" maxlength="160" placeholder="Abdomen" required>{{$PEdata[0]->Abdomen ?? ''}}</textarea>
										</td>
									</tr>
									<tr>
										<td class="bold">Ears/Hearing</td>
										<td>
											<textarea class="form-control  PE_normal" name="EarsHearing" id="" cols="20" rows="1" maxlength="160" placeholder="Ears/Hearing" required>{{$PEdata[0]->EarsHearing ?? ''}}</textarea>
										</td>
										<td class="bold">Back/Flanks</td>
										<td>
											<textarea class="form-control  PE_normal" name="BackFlanks" id="" cols="20" rows="1" maxlength="160" placeholder="Back/Flanks" required>{{$PEdata[0]->BlackFlanks ?? ''}}</textarea>
										</td>
									</tr>
									<tr>
										<td class="bold">Nose/Sinuses</td>
										<td>
											<textarea class="form-control  PE_normal" name="NoseSinuses" id="" cols="20" rows="1" maxlength="160" placeholder="Nose/Sinuses" required>{{$PEdata[0]->NoseSinuses ?? ''}}</textarea>
										</td>
										<td class="bold">Extremities</td>
										<td>
											<textarea class="form-control  PE_normal" name="Extremities" id="" cols="20" rows="1" maxlength="160" placeholder="Extremities" required>{{$PEdata[0]->Extremities ?? ''}}</textarea>
										</td>
									</tr>
									<tr>
										<td class="bold">Mouth/Throat</td>
										<td>
											<textarea class="form-control  PE_normal" name="MouthThroat" id="" cols="20" rows="1" maxlength="160" placeholder="Mouth/Throat" required>{{$PEdata[0]->MouthThroat ?? ''}}</textarea>
										</td>
										<td class="bold">Neurological</td>
										<td>
											<textarea class="form-control  PE_normal" name="Neurological" id="" cols="20" rows="1" maxlength="160" placeholder="Neurological" required>{{$PEdata[0]->Neurological ?? ''}}</textarea>
										</td>
									</tr>
									<tr>
										<td class="bold">Neck/Thyroid</td>
										<td>
											<textarea class="form-control  PE_normal" name="NeckThyroid" id="" cols="20" rows="1" maxlength="160" placeholder="Neck/Thyroid" required>{{$PEdata[0]->NeckThyroid ?? ''}}</textarea>
										</td>
										<td class="bold">Genitals/Urinary</td>
										<td>
											<input type="text" list="CBA" class="form-control PE_normal" value="{{$PEdata[0]->GenitalsUrinary ?? ''}}" name="GenitalsUrinary" placeholder="Genitals/Urinary" required>
											<datalist id="CBA">
												<option value="Normal">Normal</option>
												<option value="Not Done">Not Done</option>
												<option value="Refused">Refused</option>
												<option value="Waived">Waived</option>
											</datalist>
										</td>
									</tr>
									<tr>
										<td class="bold">Chest/Breast/Axilla</td>
										<td>
											<textarea class="form-control  PE_normal" name="ChestBreastAxilla" id="" cols="20" rows="1" maxlength="160" placeholder="Chest/Breast/Axilla" required>{{$PEdata[0]->ChestBreastAxilla ?? ''}}</textarea>
										</td>
										<td class="bold">Anus/Rectum</td>
										<td>	
											<input type="text" list="CBA" class="form-control PE_normal" value="{{$PEdata[0]->AnusRectum ?? ''}}" name="AnusRectum" placeholder="Anus/Rectum" required>
											<datalist id="CBA">
												<option value="Normal">Normal</option>
												<option value="Not Done">Not Done</option>
												<option value="Refused">Refused</option>
												<option value="Waived">Waived</option>
											</datalist>
											
										</td>
									</tr>
									</tbody>
								</table>
								<div class="row form-group row-md-flex-center">
									<div class="col-sm-2 col-md-2 pad-right-0-md text-right-md">
										<label class="bold ">Other Findings: </label>
									</div>
									<div class="col-sm-12 col-md-12 pad-12-md">
										<input type="text" class="form-control" name="OtherFindings" value="{{$PEdata[0]->PhysicalExamOther ?? ''}}" style="border: none; border-bottom: solid black 1px; outline: none;">
									</div>
								</div>
							</div>
						
							
				
				</div>
				
          	</div>
		</form>		        
        
		</div>
    </div>
    <div class="navbar-fixed-bottom">
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
		<div class="col-xs-6">
				<button class="viewbtn btn btn-info col-xs-4 col-sm-4 col-md-4 col-lg-4"  style="border-radius:0px; line-height:29px; visibility:hidden;" type="button"> View - Deleted </button>
		</div>
		<div class="col-xs-6">
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false && $datas->AStatus === 280 )
			
				<button class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="visibility:hidden;"></button>
				<a class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save</a>					
			@endif	
		</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('/js/webcam.js') }}"></script>
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
var webCamModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="webcam-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_SUCCESS,
	data: {
		'pageToLoad': "{{ '/webcam' }}"
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
		label: 'Submit',
		action: function (modalRef){
			$('.webcam').attr('src', $('input[name="image"]').val());
			$('input[name="myimage"]').val($('input[name="image"]').val());
			modalRef.close();
		}
	}]
});
function autoSave(field, value) {
		var formData = {};
		formData[field] = value;
		formData['draft'] = true;  // Optional: Set this to true to indicate it's a draft
		formData['_queueid'] = $('input[name="_queueid"]').val();
		formData['_queueCode'] = $('input[name="_queueCode"]').val();
		formData['IdPatient'] = $('input[name="IdPatient"]').val();
		$.ajax({
			url: "/doctor/queue/draft/temp",  // Change this to your route for saving drafts
			type: "POST",
			data: formData,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
			success: function(response) {
				console.log('Draft saved successfully for ' + field);
			},
			error: function(xhr, status, error) {
				console.log('Error saving draft for ' + field);
			}
		});
	}

$(document).ready(function(e) {


if($('input[name="contactLenses"], input[name="eyeglasses"]').is(':checked')) 
{
	$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', false);
	$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', false);
	$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', true);
	$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', true);
	$('.uncorectedSpan').hide();
	$('.corectedSpan').removeClass('hide');
}else{
	$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', false);
	$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', true);
	$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', false);
	$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', true);
	$('.corectedSpan').addClass('hide');
	$('.uncorectedSpan').show();
}

$('input[name="contactLenses"], input[name="eyeglasses"]').on('change', function() {
	if ($(this)
.is(':checked')) 
	{
		$('input[name="contactLenses"], input[name="eyeglasses"]').not(this).prop('checked', false);
		$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', false);
		$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', false);
		$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', true).val('');
		$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', true);
		$('.uncorectedSpan').hide();
		$('.corectedSpan').removeClass('hide');
	}else{
		$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', false).val('');
		$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', true).val('');
		$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', false);
		$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', true);
		$('.corectedSpan').addClass('hide');
		$('.uncorectedSpan').show();
	}
});

	$('#subjective').on('input', function() {
		autoSave('subjective', $(this).val());
	});

	$('#objective').on('input', function() {
		autoSave('objective', $(this).val());
	});

	$('#assessment').on('input', function() {
		autoSave('assessment', $(this).val());
	});

	$('#plan').on('input', function() {
		autoSave('plan', $(this).val());
	});

		
		/*---------------BMI Computation Start-----------*/
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
			} else {
				return 'Obese II';
			}
		}
		$('#height, #weight').on('input', function() {
			calculateBMI();
		});
		// Calculate BMI on page load if values are already filled
		calculateBMI();
		/*---------------BMI Computation End-----------*/
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
			//'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/patient/create') }}"
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
		var PastResultModal = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="pastResult-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_WIDE,
		type: BootstrapDialog.TYPE_WARNING,
		data: {
			//'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/patient/create') }}"
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
		var pdfResultsModal = new BootstrapDialog({
			message: function(dialog) {
				var $message = $('<div class="hpdf-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
				var pageToLoad = dialog.getData('pageToLoad')+"/"+dialog.getData('Queueid')+"/edit/?transid="+dialog.getData('transid');
				$message.load(pageToLoad);
				console.log(pageToLoad);
				return $message;
			},
			size: BootstrapDialog.SIZE_WIDE,
			type: BootstrapDialog.TYPE_SUCCESS,
			data: {
				'pageToLoad': "{{ '/cms/api/hpdf' }}"
			},
			animate: false,
			closable: false,
			buttons: [{
				cssClass: 'btn-default modal-closebtn',
				label: 'Close',
				action: function (modalRef) {
					modalRef.close();
				}
			}]
		});
		var TestRecommendation = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_WIDE,
		type: BootstrapDialog.TYPE_WARNING,
		data: {
			//'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/patient/create') }}"
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
				parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
				var itemSelected = [];
			$("input:checked", $('#ItemListTable').dataTable().fnGetNodes()).each(function(){
				itemSelected.push({"ItemCode":$(this).val()});
			});
			console.log(itemSelected);
				parent.postData(
				"{{ '/doctor/queue/order/orderModal?idQueue=' }}" + $('input[name=_queueid]').val(),
				{
					'ItemCode':itemSelected,
					'IdQueue':$('input[name=_queueid]').val(),
					'IdPatient':$('input[name=IdPatient]').val(),
					'IdDoctor':$('input[name=_idDoctor]').val(),
					'Id':0,
					'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 
					$('#ItemListTable').dataTable().fnClearTable();
					$('#ItemListTable').DataTable().rows.add( $data ).draw();
					TestRecommendation.close();
					parent.waitingDialog.hide();
				}
			);
			}
		}
			
		
		]
		});

	
	





	$('.history').attr('hidden', true);
	$('.result').attr('hidden', true);
	
	
	
	$('input.nav-button').click(function() {
        // Remove 'active' class from all buttons
        $('input.nav-button').removeClass('active');
        // Add 'active' class to the clicked button
        $(this).addClass('active');
        
        // Hide all sections
        $('.form-section').attr('hidden', true);
        
        // Show the relevant section based on the clicked button's name
        var sectionClass = '.' + $(this).attr('name');
        $(sectionClass).attr('hidden', false);
    });			
						
	$('.editbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - View");
		patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
	$('#ResultListTable').on('click', '.pdfResult', function (e) {
		e.preventDefault();

		var transid = $(this).closest('tr').data('toggle-idtransaction');
		var queueid = $(this).closest('tr').data('toggle-queueid');
	    
		if (!transid || !queueid) {
			console.error("Missing required data: transid or queueid");
			return;
		}

		// Construct the URL
		var pageToLoad = "{{ '/cms/api/hpdf' }}/" + queueid + "/edit/?transid=" + transid;
		$.ajax({
			url: pageToLoad,
			type: 'GET',
			dataType: 'html',
			success: function (response) {

				var parser = new DOMParser();
				var doc = parser.parseFromString(response, 'text/html');
				var iframe = doc.querySelector('iframe');

				if (iframe) {
					var pdfLink = iframe.getAttribute('src');
					var windowFeatures = "location=yes,height=800,width=6000,scrollbars=yes,status=yes";
					window.open(pdfLink, '_blank', 'location=yes,height=800,width=5000,scrollbars=yes,status=yes');
				} else {
					console.error("No iframe found in the response.");
					alert("Unable to extract PDF link.");
				}
			},
				error: function (xhr, status, error) {
				console.error("AJAX Request Failed:", status, error);
				alert("An error occurred while fetching the content. Please try again.");
			}
		});
	});
	
	$('.reccomendbtn').on('click',function(e){
		TestRecommendation.setTitle("Test - Recommendation");
		TestRecommendation.setType(BootstrapDialog.TYPE_SUCCESS);
		TestRecommendation.setData("pageToLoad", "{{ '/doctor/queue/order/orderModal' }}/"+ $('input[name="_queueid"]').val()+"/edit");
		TestRecommendation.realize();
		TestRecommendation.open();
		e.preventDefault();
	});
	//$('textarea').on('keypress', function (event) {
   // if (event.which === 13) { // 13 is the Enter key
     //   event.preventDefault(); // Prevents new line
    //}
	//});
	//------------ I PAST MEDICAL & SURGICAL HISTORY --------------//
	function updatePastMedical() {
            let hasValue = false; // Assume no input unless found
            
            // Check if any textarea is not empty
            $("textarea").each(function () {
                if ($(this).val().trim() !== "") {
                    hasValue = true; // Found a filled textarea
                }
            });

            // Check if "Unremarkable" is checked
            if ($("input[name='unremarkable']").is(":checked")) {
                hasValue = true;
            }

            // Set value of pastmedical
            $("input[name='pastmedical']").val(hasValue ? "1" : "");
        }

        // Trigger when textareas or checkbox change
        $("textarea, input[name='unremarkable']").on("input change", updatePastMedical);
	if ($('.table_input').filter(function() {
		return $(this).val() === 'Unremarkable';
	}).length > 0) {
		$('input[name="unremarkable"]').prop('checked', true);
	} else {
		$('input[name="unremarkable"]').prop('checked', false);
	}
	$('input[name="unremarkable"]').on('change', function(e) {
    if ($(this).is(':checked')) {
        // Only set "Unremarkable" in empty input fields
        $('.table_input').filter(function() {
            return $(this).val() === '';
        }).val('Unremarkable');
    } else {
        // Clear all input fields previously set to "Unremarkable"
        $('.table_input').filter(function() {
            return $(this).val() === 'Unremarkable';
        }).val('');
    }
	});

	//------------ END I PAST MEDICAL & SURGICAL HISTORY --------------//
	//------------ II Personal/Social Start --------------//
	function updatePersonalSocial() {
		let allChecked = false; // Assume all are checked unless we find an unchecked pair

		// Loop through each required pair of checkboxes
		let requiredGroups = [
			["input[name='presentSmokerY']", "input[name='presentSmokerN']"],
			["input[name='prevY']", "input[name='prevN']"],
			["input[name='PresDrinkerY']", "input[name='PresDrinkerN']"],
			["input[name='PrevDrinkerY']", "input[name='PrevDrinkerN']"]
		];

		let $presentSmokerY = false;
		let $presentSmokerN = false;
		let $prevY  = false;
		let $prevN = false;
		let $PresDrinkerY = false;
		let $PresDrinkerN = false;
		let $PrevDrinkerY = false;
		let $PrevDrinkerN = false;
		
		requiredGroups.forEach(pair => {
			//let isChecked = ($(pair[0]).is(":checked") || $(pair[1]).is(":checked")); 
			if($(pair[0]).is(":checked") && pair[0] === "input[name='presentSmokerY']" )
			{
				$presentSmokerY = true;
			}
			else if($(pair[1]).is(":checked") && pair[1] === "input[name='presentSmokerN']" )
			{
				$presentSmokerN = true;
			}
			
			if($(pair[0]).is(":checked") && pair[0] === "input[name='prevY']" )
			{
				$prevY = true;
			}
			else if($(pair[1]).is(":checked") && pair[1] === "input[name='prevN']" )
			{
				$prevN = true;
			}
			
			if($(pair[0]).is(":checked") && pair[0] === "input[name='PresDrinkerY']" )
			{
				$PresDrinkerY = true;
			}
			else if($(pair[1]).is(":checked") && pair[1] === "input[name='PresDrinkerN']" )
			{
				$PresDrinkerN = true;
			}
			
			if($(pair[0]).is(":checked") && pair[0] === "input[name='PrevDrinkerY']" )
			{
				$PrevDrinkerY = true;
			}
			else if($(pair[1]).is(":checked") && pair[1] === "input[name='PrevDrinkerN']" )
			{
				$PrevDrinkerN = true;
			}
		});
		let $left = false;
		let $right = false;
		
		if( ($presentSmokerY) || ($presentSmokerN && ($prevY || $prevN )) )
		{
			$left = true;
		}
		if( ($PresDrinkerY)  || ($PresDrinkerN && ($PrevDrinkerY || $PrevDrinkerN )) )
		{
			$right = true;
		}
		
		if( $left && $right)
		{
			allChecked = true;
		}
		//alert(allChecked);
		$("input[name='personalsocial']").val(allChecked ? "1" : "");
	}
	// Trigger function when checkboxes change
	$("input[type='checkbox']").change(updatePersonalSocial);

	if($('input[name="presentSmokerY"]').is(':checked') ){
		$('input[name="presentSmokerSD"]').attr('placeholder','sticks/day').attr('required', true).attr('readonly', false);
		$('input[name="presentSmokerYears"]').attr('placeholder','Year').attr('required', true).attr('readonly', false);
	}
	$('input[name="presentSmokerY"], input[name="presentSmokerN"]').on('click', function() {
		$('input[name="presentSmokerY"], input[name="presentSmokerN"]').not(this).prop('checked', false);
		if($('input[name="presentSmokerY"]').is(':checked') ){
			$('input[name="presentSmokerSD"]').attr('placeholder','sticks/day').attr('required', true).attr('readonly', false);
			$('input[name="presentSmokerYears"]').attr('placeholder','Year').attr('required', true).attr('readonly', false);
			$('input[name="prevY"], input[name="prevN"]').attr('disabled', true).prop('checked', false);
		}else{
			$('input[name="presentSmokerSD"]').attr('placeholder', '').attr('required', false).attr('readonly', true).val(null);
			$('input[name="presentSmokerYears"]').attr('placeholder','').attr('required', false).attr('readonly', true).val(null);
			$('input[name="prevY"], input[name="prevN"]').attr('disabled', false);
		}
	});
	$('input[name="prevY"], input[name="prevN"]').on('click', function() {
        $('input[name="prevY"], input[name="prevN"]').not(this).prop('checked', false);
		if($('input[name="prevY"]').is(':checked') ){
			$('input[name="previousSmokerSD"]').attr('placeholder','sticks/day').attr('required', true).attr('readonly', false);
			$('input[name="previousSmokerYears"]').attr('placeholder','Year').attr('required', true).attr('readonly', false);
		}else{
			$('input[name="previousSmokerSD"]').attr('placeholder', '').attr('required', false).attr('readonly', true)
			$('input[name="previousSmokerYears"]').attr('placeholder','').attr('required', false).attr('readonly', true);
		}
    });
	$('input[name="PresDrinkerY"], input[name="PresDrinkerN"]').on('click', function() {
        $('input[name="PresDrinkerY"], input[name="PresDrinkerN"]').not(this).prop('checked', false);
		if($('input[name="PresDrinkerY"]').is(':checked')){
			$('input[name="PresBottle"]').attr('placeholder','Bottles').attr('required', true).attr('readonly', false);
			$('input[name="PrevDrinkerY"], input[name="PrevDrinkerN"]').attr('disabled', true).prop('checked', false);
		}else{
			$('input[name="PresBottle"]').attr('placeholder', '').attr('required', false).attr('readonly', true);
			$('input[name="PrevDrinkerY"], input[name="PrevDrinkerN"]').attr('disabled', false);
		}
    });
	$('input[name="PrevDrinkerY"], input[name="PrevDrinkerN"]').on('click', function() {
        $('input[name="PrevDrinkerY"], input[name="PrevDrinkerN"]').not(this).prop('checked', false);
		if($('input[name="PrevDrinkerY"]').is(':checked')){
			$('input[name="PrevDrinkerYear"]').attr('placeholder','Year').attr('required', true).attr('readonly', false);
		}else{
			$('input[name="PrevDrinkerYear"]').attr('placeholder', '').attr('required', false).attr('readonly', true);
		}
    });
	//------------ II Personal/Social Start End ------------//

	//------------ III Obstetrics & Gynecological start ------------//
	function updateObstetrics() {
            let hasValue = false;

            // Check if at least one input field has a value
            $("input[name='fDayofMendtruation'], input[name='menarch'], input[name='MenopausalAge'], input[name='OBGYNEOthers'], input[name='g_value'], input[name='p_value'], input[name='p1_value']").each(function () {
                if ($(this).val().trim() !== "") {
                    hasValue = true;
                }
            });

            // Check if either "Regular" or "NRegular" is checked
            if ($("input[name='Regular']").is(":checked") || $("input[name='NRegular']").is(":checked")) {
                hasValue = true;
            }

            // Set value of obstetrics
            $("input[name='obstetricsrequired']").val(hasValue ? "1" : "");
        }

        // Trigger when any input or checkbox changes\
	
    $("input").on("input change", updateObstetrics);
	$("input[name='Regular'], input[name='NRegular']").on("change", function () {
        if ($("input[name='Regular']").is(":checked") || $("input[name='NRegular']").is(":checked")) {
            $("input[name='mensregular']").val("1"); // Set value to "1" when checked
        } else {
            $("input[name='mensregular']").val(""); // Clear if none are checked
        }
    });

	$('input[name=menarch]').on('input', function() {
    let value = parseFloat($(this).val());

    if (value > 20) {
        $(this).val(20); // Set to max 50 if exceeded
    } else if (value < 0 || isNaN(value)) {
        $(this).val(); // Prevent negative values
    }
	});
	$('input[name=MenopausalAge]').on('input', function() {
    let value = parseFloat($(this).val());

    if (value > 100) {
        $(this).val(100); // Set to max 50 if exceeded
    } else if (value < 0 || isNaN(value)) {
        $(this).val(); // Prevent negative values
    }
	});
	$('input[name="Regular"], input[name="NRegular"]').on('change', function() {
    if ($(this).is(':checked')) {
        $('input[name="Regular"], input[name="NRegular"]').not(this).prop('checked', false);
    }
	});
	if($('input[name=Gender]').val() == 'M'){
		$('input[name=fDayofMendtruation] , input[name=mensregular] , input[name=Regular], input[name=NRegular], input[name=MenopausalAge], input[name=menarch], input[name=OBGYNEOthers], input[name=g_value], input[name=p_value], input[name=p1_value]').attr('disabled', true).attr('required', false);
	}
	let format = '(_-_-_-_)';
    
	// Function to set the cursor position at the first occurrence of "_"
	function setCaretPosition(input, pos) {
	   input.setSelectionRange(pos, pos); // Set the caret position
   }
   // Event listener for keyup on input field
   $('#p1_value').on('input', function(e) {
	   let inputVal = $(this).val().replace(/\D/g, ''); // Keep only digits
	   let formattedVal = format;
	   
	   // Replace underscores with typed numbers
	   for (let i = 0; i < inputVal.length; i++) {
		   formattedVal = formattedVal.replace('_', inputVal[i]);
	   }

	   // Replace remaining underscores
	   formattedVal = formattedVal.replace(/_/g, '_');
	   $(this).val(formattedVal);

	   // Find the next "_" and move the cursor there
	   let nextPos = formattedVal.indexOf('_');
	   if (nextPos !== -1) {
		   setCaretPosition(this, nextPos);  // Move cursor to the next "_"
	   }
   });
	//------------ III Obstetrics & Gynecological End ------------//
	
	//------------ IV Family History  --------------//
	$("input[name='BronchialAsthma'], input[name='Goiter'], input[name='HeartDiseaseF'], input[name='KidneyDiseaseF'], input[name='DiabetesMellitusF'], input[name='PTB'], input[name='HypertensionF'], input[name='FamilyOthers']").on("input", function () {
    let allEmpty = true;

    $("input[name='BronchialAsthma'], input[name='Goiter'], input[name='HeartDiseaseF'], input[name='KidneyDiseaseF'], input[name='DiabetesMellitusF'], input[name='PTB'], input[name='HypertensionF'], input[name='FamilyOthers']").each(function () {
        if ($(this).val() !== "") {
            allEmpty = false;
        }
    });

    $("input[name='familyhistory']").val(allEmpty ? "" : "1");
	});

	$('input[name="contactLenses"], input[name="eyeglasses"]').on('change', function() {
    if ($(this).is(':checked')) {
        $('input[name="contactLenses"], input[name="eyeglasses"]').not(this).prop('checked', false);
		}
	});
	// $('input[name="family_None"]').on('change', function(){
	// 	if($(this).is(':checked')){
	// 		$('.fnone').val('None').addClass('bold');

	// 	}else{
	// 		$('.fnone').val('');
	// 	}
	// });
	if ($('.fnone').filter(function() {
        return $(this).val() === 'None';
    }).length > 0) {
        $('input[name="family_None"]').prop('checked', true);
    } else {
        $('input[name="family_None"]').prop('checked', false);
    }
	$('input[name="family_None"]').on('change', function(e) {
    if ($(this).is(':checked')) {
        // Only set "Unremarkable" in empty input fields
        $('.fnone').filter(function() {
            return $(this).val() === '';
        }).val('None');
		$("input[name='familyhistory']").val("1");
    } else {
        // Clear all input fields previously set to "Unremarkable"
        $('.fnone').filter(function() {
            return $(this).val() === 'None';
        }).val('');
		$("input[name='familyhistory']").val("");
    }
	});

	$('.toggle-input').change(function() {
    var inputField = $(this).closest('label').next('input');
    var labelText = $(this).closest('label').text().trim(); // Get the label's text
	//inputField.attr('placeholder', 'Normal');
    if ($(this).is(':checked')) {
        inputField.removeAttr('readonly').attr({
            'required': true,
            'placeholder': labelText // Set the placeholder to the label's text
        });
    } else {
        inputField.attr('readonly', 'readonly').removeAttr('required');
    }
	});
	//------------End IV Family History ------------//

	//------------ V Physical Examination  --------------//
	$('textarea[name="Skin"], textarea[name="Lungs"], textarea[name="HeadScalp"], textarea[name="Heart"], textarea[name="Eyes"], textarea[name="Abdomen"], textarea[name="EarsHearing"], textarea[name="BackFlanks"], textarea[name="NoseSinuses"], textarea[name="Extremities"], textarea[name="MouthThroat"], textarea[name="Neurological"], textarea[name="NeckThyroid"], textarea[name="GenitalsUrinary"], textarea[name="ChestBreastAxilla"], textarea[name="AnusRectum"]').on("input", function () {
    let allEmpty = true;

    $('textarea[name="Skin"], textarea[name="Lungs"], textarea[name="HeadScalp"], textarea[name="Heart"], textarea[name="Eyes"], textarea[name="Abdomen"], textarea[name="EarsHearing"], textarea[name="BackFlanks"], textarea[name="NoseSinuses"], textarea[name="Extremities"], textarea[name="MouthThroat"], textarea[name="Neurological"], textarea[name="NeckThyroid"], textarea[name="GenitalsUrinary"], textarea[name="ChestBreastAxilla"], textarea[name="AnusRectum"]').each(function () {
        if ($(this).val().trim() !== "") {
            allEmpty = false;
        }
    });

    $("input[name='physicalexamnination']").val(allEmpty ? "" : "1");
});

// Listen for changes on the checkbox
	if ($('.PE_normal').filter(function() {
		return $(this).val() === 'Normal';
	}).length > 0) {
		$('input[name="PE_normal"]').prop('checked', true);
	} else {
		$('input[name="PE_normal"]').prop('checked', false);
	}
	$('input[name="PE_normal"]').on('change', function(e) {
		if ($(this).is(':checked')) {
		// Only set "Unremarkable" in empty input fields
			$('.PE_normal').filter(function() {
				return $(this).val() === '';
			}).val('Normal');
			$("input[name='physicalexamnination']").val("1");
		} else {
		// Clear all input fields previously set to "Unremarkable"
			$('.PE_normal').filter(function() {
			return $(this).val() === 'Normal';
			}).val('');
			$("input[name='physicalexamnination']").val("");
		}
	});
	
	$('select[name="AnusRectum"]').selectize({
            create: true,  // Allows custom entries
            persist: false // This prevents the custom option from being added permanently
        });
	$('select[name="GenitalsUrinary"]').selectize({
            create: true,  // Allows custom entries
            persist: false // This prevents the custom option from being added permanently
        });
	//------------ End V Physical Examination  --------------//

	$('.webcam').on('click', function(){
		webCamModal.setTitle("WebCam");
		webCamModal.realize();
		webCamModal.open();
		//e.preventDefault();
			
	});

	$('.savebtn').on('click',function(e){

		

		if($('input[name="contactLenses"]').is(':checked'))
		{
		    var WithLense = "Y"
		} else{
		    var WithLense = "N"
		}
		
		if($('input[name="eyeglasses"]').is(':checked'))
		{
		    var WithEyeglass = "Y"
		} else{
		    var WithEyeglass = "N"
		}
		
		if($('input[name="presentSmokerY"]').is(':checked'))
		{
		    var presentSmoker = "Y"
		}
		
		if($('input[name="presentSmokerN"]').is(':checked'))
		{
		    var presentSmoker = "N"
		}
		
		if($('input[name="prevY"]').is(':checked'))
		{
		    var prevSmoker = "Y"
		}
		
		if($('input[name="prevN"]').is(':checked'))
		{
		    var prevSmoker = "N"
		}
		if($('input[name="Regular"]').is(':checked'))
		{
			var regular = "Yes"
		}
		if($('input[name="NRegular"]').is(':checked'))
		{
			var regular = "No"
		}
		if( parent.required($('form')) ) return false;
		var userConfirmed = window.confirm('Are you sure you want to proceed?');
		if (!userConfirmed) {
			return false;
		}

		parent.waitingDialog.show('Saving...', {dialogSize: 'sm', progressType: 'warning'});
		var gp = $('input[name="g_value"]').val() + $('input[name="p_value"]').val() + $('input[name="p1_value"]').val();
		
		

		//$('#formQueueCreate').submit();
		parent.postData(
				"{{ '/doctor/pe/'.$datas->Id }}",
				{
					'IdDoctor':$('input[name=_idDoctor]').val(),
					'IdPatient':$('input[name=IdPatient]').val(),
					'_method'				:'PUT'
					//*****VA******//
					,'PulseRate'             : $('input[name=pulserate]').val()
					,'Respiraroty'          : $('input[name=respiratory]').val()
					,'BloodPresure'         : $('input[name=bloodpresure]').val()
					,'BloodPresureOver'     : $('input[name=bloodpresureover]').val()
					,'BloodPresure2'        : $('input[name=bloodpresure2]').val()
					,'BloodPresureOver2'    : $('input[name=bloodpresureover2]').val()
					,'BloodPresure3'        : $('input[name=bloodpresure3]').val()
					,'BloodPresureOver3'    : $('input[name=bloodpresureover3]').val()
					,'Temperature'          : $('input[name=temperature]').val()
					,'Height'               : $('input[name=height]').val()
					,'Weight'               : $('input[name=weight]').val()
					,'BMI'                  : $('input[name=bmi]').val()
					,'unCorOD'              : $('input[name=uncorectedOd]').val()
					,'unCorOS'              : $('input[name=uncorectedOs]').val()
					,'CorOD'                : $('input[name=corectedOd]').val()
					,'CorOS'                : $('input[name=corectedOs]').val()
					,'UncorrectedNearOD'    : $('input[name=uncorectedNearOd]').val()
					,'UncorrectedNearOS'    : $('input[name=uncorectedNearOs]').val()
					,'CorrectedNearOD'      : $('input[name=corectedNearOd]').val()
					,'CorrectedNearOS'      : $('input[name=corectedNearOs]').val()
					,'WithLense'            : WithLense
					,'WithEyeglass'         : WithEyeglass
					,'BMIcategory'			: $('input[name=BMICategory]').val()
					// ******** PE ************//
					,'liverglad'				: $('textarea[name=liverglad]').val()
					,'diabetisM'			: $('textarea[name=diabetisM]').val()
					,'heartDisease'			: $('textarea[name=heartDisease]').val()
					,'ChronicHeadache'		: $('textarea[name=ChronicHeadache]').val()
					,'asthmaAllergy'			: $('textarea[name=asthmaAllergy]').val()
					,'Hypertension'			: $('textarea[name=Hypertension]').val()
					,'Tuberculosis'			: $('textarea[name=Tuberculosis]').val()
					,'KidneyDisease'			: $('textarea[name=KidneyDisease]').val()
					,'EarNoseThroat'			: $('textarea[name=EarNoseThroat]').val()
					,'Cancer'				: $('textarea[name=Cancer]').val()
					,'EyeDisorder'			: $('textarea[name=EyeDisorder]').val()
					,'SexuallyTransmitted'		: $('textarea[name=SexuallyTransmitted]').val()
					,'pastMedOthers'			: $('input[name=pastMedOthers]').val()
					,'presentSmoker'			: presentSmoker
					,'presentSmokerSD'		: $('input[name=presentSmokerSD]').val()
					,'presentSmokerYears'		: $('input[name=presentSmokerYears]').val()
					,'prevSmoker'			: prevSmoker
					,'prevSD'				: $('input[name=previousSmokerSD]').val()
					,'prevYears'			: $('input[name=previousSmokerYears]').val()
					,'PresentAlcoholDrinker'	: $('input[name=PresBottle]').val()
					,'PresDrinkerN'			: $('input[name=PrevDrinkerYear]').val()
					,'PersonalOthers'			: $('input[name=PersonalOthers]').val()
					,'fDayofMendtruation'		: $('input[name=fDayofMendtruation]').val()
					,'MenopausalAge'		: $('input[name=MenopausalAge]').val()	
					,'Menarche'			: $('input[name=menarch]').val()
					,'Regular'				: regular
					,'gp'					: gp
					,'OBGYNEOthers'		: $('input[name=OBGYNEOthers]').val()
					,'BronchialAsthma'		: $('input[name=BronchialAsthma]').val()
					,'Goiter'				: $('input[name=Goiter]').val()
					,'HeartDiseaseF'			: $('input[name=HeartDiseaseF]').val()
					,'KidneyDiseaseF'		: $('input[name=KidneyDiseaseF]').val()
					,'DiabetesMellitusF'		: $('input[name=DiabetesMellitusF]').val()
					,'PTB'				: $('input[name=PTB]').val()
					,'HypertensionF'			: $('input[name=HypertensionF]').val()
					,'FamilyOthers'			: $('input[name=FamilyOthers]').val()
					,'Skin'				: $('textarea[name=Skin]').val()
					,'Lungs'				: $('textarea[name=Lungs]').val()
					,'HeadScalp'			: $('textarea[name=HeadScalp]').val()
					,'Heart'				: $('textarea[name=Heart]').val()
					,'Eyes'				: $('textarea[name=Eyes]').val()
					,'Abdomen'				: $('textarea[name=Abdomen]').val()
					,'EarsHearing'			: $('textarea[name=EarsHearing]').val()
					,'BackFlanks'			: $('textarea[name=BackFlanks]').val()
					,'NoseSinuses'			: $('textarea[name=NoseSinuses]').val()
					,'Extremities'			: $('textarea[name=Extremities]').val()
					,'MouthThroat'			: $('textarea[name=MouthThroat]').val()
					,'Neurological'			: $('textarea[name=Neurological]').val()
					,'NeckThyroid'			: $('textarea[name=NeckThyroid]').val()
					,'GenitalsUrinary'		: $('input[name=GenitalsUrinary]').val()
					,'ChestBreastAxilla'		: $('textarea[name=ChestBreastAxilla]').val()
					,'AnusRectum'			: $('input[name=AnusRectum]').val()
					,'OtherFindings'			: $('input[name=OtherFindings]').val()
					

				},
				function($data)
				{  
					var hyperlink = document.createElement('a');
					hyperlink.href = '/kiosk/consultationqueue';
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
			var csrfToken = $('meta[name="_token"]').attr('content');
				var queueID = $('input[name="_queueid"]').val();
				$.ajax({
					type: 'POST',
					url: '{{ route('exitstation') }}',
					headers: { 'X-CSRF-TOKEN': csrfToken },
					data: { queueID: queueID, station: 'CONSULTATION'},
					success: function (response) {
						console.log('Exit room successfully');
					},
					error: function (error) {
						console.error('Error updating status:', error);
					}
				});
		e.preventDefault();
	});

	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
});
</script>
@endsection