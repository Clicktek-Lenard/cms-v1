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

.selectize-dropdown .description {
	display: block;
	font-size: medium;
	color: #888; /* Light color for additional details */
}

.selectize-dropdown .Code {
	display: block;
	font-size: smaller;
	color: #888; /* Light color for Code */
}
:not(:focus)::-webkit-input-placeholder {
    /* WebKit browsers */
    color: transparent;
}
:not(:focus):-moz-placeholder {
    /* Mozilla Firefox 4 to 18 */
    color: transparent;
}
:not(:focus)::-moz-placeholder {
    /* Mozilla Firefox 19+ */
    color: transparent;
}
:not(:focus):-ms-input-placeholder {
    /* Internet Explorer 10+ */
    color: transparent;
}
/* fix dropdown for physician */
.selectize-dropdown, .selectize-dropdown.single {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    width: 100% !important;
    z-index: 9999 !important;
    background: #fff !important;
    border: 1px solid #ccc !important;
    box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
}
</style>
@endsection

@section('content')
<div class="container-fluid"> 
	<div class="navbar-fixed-top crumb hide">
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li><a href="{{ '/cms/vitals' }}">Doctor <span class="badge cms-font" style="top:-9px; position:relative;"></span></a></li>
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
		<input type="hidden" name="_transactionType" value="{{$datas->PriceGroupItemPrice}}">			
		<div class="panel panel-primary">
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
		
				<div class="panel-heading cms-font" style="line-height:12px;">Info </div>
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
                                    <input type="text" class="typeahead form-control cms-font" name="PatientName" value="{{ $datas->FullName }}" placeholder="Patient Name" required="required" >
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
								<textarea class="form-control" name="Notes" placeholder="Notes"></textarea>
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
							<div class="hide col-sm-2 col-md-2 pad-left-1-md text-left-md">
								<label class="bold">Patient Type</label>
							</div>
							<div class="hide col-sm-4 col-md-4 pad-0-md">
								<select name="PatientType" class="form-control" placeholder="Patient Type" data-placeholder="Patient Type" required="required" >
									<option></option>
								</select>
							</div>

							<div class="col-sm-2 col-md-2 pad-left-1-md text-left-md">
								<label class="bold ">Clinic</label>
                            </div>
							<div class="col-sm-4 col-md-4 pad-0-md">
                            	<input type="hidden" name="ClinicCode" value="{{ $datas->IdClinic }}" />
								<select name="Clinic" class="form-control disabled" placeholder="Clinic" required="required" disabled="disabled">
                                    <option value=""></option>
                                    @foreach ($clinics as $clinic) 
			       						<option value="{{ $clinic->Code }}"  @if($datas->IdBU == $clinic->Code  ) selected @else '' @endif  >{{ $clinic->Description }}</option>
                                    @endforeach
                                </select>
							</div>
                            <div class="col-sm-2 col-md-2 pad-left-0-md text-right-md  ">
								<label class="bold ">Queue Status.</label>
                            </div>
                            <div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->QueueStatus }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>

						@if (session('userClinicCode') == 'ICT')
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
								<label class="bold ">Ante-Date</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control anteDate" name="anteDate" value="{{$datas->Date}}" placeholder="Ante Date" readonly="readonly" >
							</div>
						</div>
						@endif
						<!-- END -->

						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
								<label class="bold ">Input By</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control" value="{{ $datas->InputBy }}" placeholder="System Generated" readonly="readonly">
							</div>
						</div>
						<div class="row form-group row-md-flex-center">
							<div class="col-sm-2 col-md-2 col-md-offset-6 pad-left-0-md  text-right-md ">
								<label class="bold ">Date Time</label>
							</div>
							<div class="col-sm-4 col-md-4 pad-0-md">
								<input type="text" class="form-control " name="Date" value="{{ date('d-M-Y H:i:s',strtotime($datas->DateTime)) }}" placeholder="Date" readonly="readonly">
							</div>
						</div>
                        
                    </div>
					
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading" style="line-height:12px;">Attending Physician</div>
					<div class="panel-body" >
                        <div class="col-md-12">
                            <label class="bold"></label>
                            <div class="row">
                                <div class="col-xs-12">
				<input type="hidden" name="AttendingId" value="{{ $datas->IdDoctor }}" class="form-control" >
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon2" style="color:red;"><input type="button" style="border:none" class="defaultDoctor" value="Select Attending Physician"></span>
										<input type="hidden" name="AttendingName" class="form-control" placeholder="Select a physician">
            							
                                        <select name="Attending" class="form-control" required placeholder="Select Attending Physician">
								
											<option value=""  selected> @if(isset($vitals->PcpId)) {{$vitals->PcpId}}@else @endif</option>
										</select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div> 
            <div class="panel panel-primary ChiefComplaint">
				<div class="panel-heading" style="line-height:12px;">Chief Complaint</div>
					<div class="panel-body" >
                        <div class="col-md-12">
                            <label class="bold"></label>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon2">Chief Complaint<span style="color:red">*</span></span>
                                        <input type="text" class="form-control" name="ChiefComplain" style="z-index: -0" value="{{$vitals->ChiefComplaint ?? ''}}" required placeholder="Chief Complaint">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>   
			<div class="panel panel-primary VitalSigns">
				<div class="panel-heading" style="line-height:12px; ">Vital Signs </div>
					<div class="panel-body" >
						<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
							<div class="row">
								<div class="col-md-4 ">
									<label class="bold">Pulse Rate<span style="color:red">*</span> </label>
									<div class="input-group">
										<input type="number" class="form-control" style="z-index: -0;" placeholder="Pulse Rate" name="pulserate" value="{{$vitals->PulseRate ?? ''}}" min="0" max="220" required> 

										<span class="input-group-addon" id="basic-addon2">bpm</span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="bold">Respiratory Rate<span style="color:red">*</span></label>
									<div class="input-group">
										<input type="number" class="form-control" style="z-index: -0;" name="respiratory" placeholder="Respiratory Rate" value="{{$vitals->RespiratoryRate ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" required>
										<span class="input-group-addon" id="basic-addon2">cpm</span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="bold">Temperature<span style="color:red">*</span> </label>
									<div class="input-group">
										<input type="number" class="form-control" style="z-index: -0;" name="temperature" placeholder="Temperature" value="{{$vitals->Temperature ?? ''}}" maxlength="4" required>
										<span class="input-group-addon" id="basic-addon2">°C</span>
									</div> 
								</div> 
							</div>
						
							<div class="row" style="padding-top: 8px">
								<div class="col-md-4">
									<label class="bold" >Height<span id="Height" style="color:red">*</span> </label>
									<div class="input-group">
										<input type="number" type="text" class="form-control" id="height" placeholder="Height" style="z-index: -0;" name="height" value="{{$vitals->Height ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
										<span class="input-group-addon" id="basic-addon2">cm</span>
									</div>
								</div>
								<div class="col-md-4">
									<label class="bold" >Weight<span id="Weight" style="color:red">*</span> </label>
									<div class="input-group">
										<input type="number" class="form-control" id="weight" placeholder="Weight" style="z-index: -0;" name="weight" value="{{$vitals->Weight ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
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
										<span class="input-group-addon" id="basic-addon2">1 </span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresure" value="{{$vitals->BloodPresure ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;" required placeholder="Blood Presure">
										<span class="input-group-addon" id="basic-addon2">/</span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresureover" value="{{$vitals->BloodPresureOver ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;" required placeholder="Blood Presure">
										<span class="input-group-addon" id="basic-addon2">mmHg</span>
									</div>
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon2">2</span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresure2" value="{{$vitals->BloodPresure2 ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;">
										<span class="input-group-addon" id="basic-addon2">/</span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresureover2" value="{{$vitals->BloodPresureOver2 ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;">
										<span class="input-group-addon" id="basic-addon2">mmHg</span>
									</div>
									<div class="input-group">
										<span class="input-group-addon" id="basic-addon2">3</span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresure3" value="{{$vitals->BloodPresure3 ?? ''}}" oninput="if(this.value > 300) this.value = 300; if(this.value < 0) this.value = 0;">
										<span class="input-group-addon" id="basic-addon2">/</span>
										<input type="number" class="form-control" style="z-index: -0;" name="bloodpresureover3" value="{{$vitals->BloodPresureOver3 ?? ''}}" oninput="if(this.value > 200) this.value = 200; if(this.value < 0) this.value = 0;">
										<span class="input-group-addon" id="basic-addon2">mmHg</span>
									</div>
								</div>
							</div> 
						</div>
					</div>
				</div>

									<div class="panel panel-primary visualAcuity" style="margin-bottom: 50px">
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
																					<span class="input-group-addon " id="basic-addon2"><span class="uncorectedSpan" style="color: red"></span>OD 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OD" name="uncorectedOd" value="{{$vitals->UcorrectedOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" >
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="uncorectedSpan" style="color: red"></span>OS 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OS" name="uncorectedOs" value="{{$vitals->UcorrectedOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
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
																					<input type="number" class="form-control number" placeholder="Near Vision OD" name="uncorectedNearOd" value="{{$vitals->UncorrectedNearOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
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
																					@if(isset($vitals->WithContactLens) && $vitals->WithContactLens === "Y") checked @endif> 
																					with Contact Lenses
																				</label> 
																				<label class="bold">
																					<input type="checkbox" name="eyeglasses" 
																				   @if(isset($vitals->WithEyeGlass) && $vitals->WithEyeGlass === "Y") checked @endif> 
																					with Eyeglasses
																				</label>
																			</div>
																		</div>
																	</td>
																	<td>
																		<div class="row">
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="hide corectedSpan" style="color: red"></span>OD 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OD" name="corectedOd" value="{{$vitals->CorrectedOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" readonly >
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2"><span class="hide corectedSpan" style="color: red"></span>OS 20/</span>
																					<input type="number" class="form-control number" placeholder="Far Vision OS" name="corectedOs" value="{{$vitals->CorrectedOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" readonly>
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
																					<input type="number" class="form-control number" placeholder="Near Vision OD J" name="corectedNearOd" value="{{$vitals->CorrectedNearOD ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" readonly>
																				</div>
																			</div>
																			<div class="col-xs-6">
																				<div class="input-group">
																					<span class="input-group-addon" id="basic-addon2">OS J</span>
																					<input type="number" class="form-control number"  placeholder="Near Vision OS J" name="corectedNearOs" value="{{$vitals->CorrectedNearOS ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" readonly>
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
			@if(strpos(session('userRole'), '"ldap_role":"[CMS-CSR-VIEW]"') === false)
			<button class="col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="visibility:hidden;"></button>
			<a class="pcpbtn btn btn-success col-xs-4 col-sm-4 col-md-4 col-lg-4 " style="  border-radius:0px; line-height:29px; visibility:hidden;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Select PCP Doctor</a>
			<a class="savebtn  btn btn-primary col-xs-4 col-sm-4 col-md-4 col-lg-4" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save</a>					
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
var historyModal = new BootstrapDialog({
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

	var pcpDoctorsModal = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="patientAdd-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_LARGE,
		type: BootstrapDialog.TYPE_WARNING,
		data: {
			//'pageToLoad': "{{ url(session('userBUCode').'/cms/pages/patient/create') }}"
			pageToLoad: ''
		},
		animate: false,
		closable: false,
		buttons: [

		{
			id: 'btnsave',
			cssClass: 'btn-success actionbtn btn-sm',
			label: 'Save',
			action: function (modalRef){
		
				parent.postData(
				"{{ '/doctor/vitals/doctordecking/pcpdoctor?IdQueue=' }}"+ $('input[name="_queueid"]').val(),
				{
					
					'Id':0
					,'_token': $('input[name=_token]').val()
				},
				function($data)
				{ 
					$('#ItemListTable').dataTable().fnClearTable();
					$('#ItemListTable').DataTable().rows.add( $data ).draw();
					pcpDoctorsModal.close();
					parent.waitingDialog.hide();
				}
			);
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
		$('input[name="BMICategory"]').val(bmiCategory);
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
        return 'Obese II';
    }
}

$('#height, #weight').on('input', function() {
    calculateBMI();
});

// Calculate BMI on page load if values are already filled
calculateBMI();


var select = $('select[name="Attending"]').selectize({
    valueField: 'Code', 
    labelField: 'FullName', 
    searchField: ['FullName', 'Id'], 
    create: false,
    options: @json($Physician),
    load: function (query, callback) {
        if (!query.length) return callback();
        $.ajax({
            url: '/api/physicians',
            type: 'GET',
            dataType: 'json',
            data: { q: query },
            error: function (xhr) {
                console.error("Failed to fetch data:", xhr.responseText || xhr.statusText);
                callback();
            },
            success: function (res) {
                callback(res);
            }
        });
    },
    render: {
        option: function (item, escape) {
            return (
                '<div>' +
                    '<span class="name">' + escape(item.FullName) + '</span>' +
                    '<span class="description"><medium>' + escape(item.Description || '') + '</medium></span>' +
                    '<span class="Code"><small>' + escape(item.Code) + '</small></span>' +
                '</div>'
            );
        }
    },
    onChange: function (value) {
        var selected = this.options[value];
        $('input[name="AttendingName"]').val(selected ? selected.FullName : '');
        $('input[name="AttendingId"]').val(selected ? selected.Id : '');
    }
});

var selectInstance = select[0].selectize;
var defaultCode = {!! json_encode($datas->NameDoctor ?? '') !!};

if (defaultCode && !selectInstance.options[defaultCode]) {
    selectInstance.addOption({
        FullName: {!! json_encode($vitals->PcpName ?? $datas->NameDoctor) !!},
        Description: "",
        Code: defaultCode
    });
    selectInstance.refreshOptions(false);
}

if (selectInstance && defaultCode) {
    selectInstance.setValue(defaultCode);
    $('input[name="AttendingId"]').val('{{$datas->IdDoctor}}');
}

$(document).on('click', '.defaultDoctor', function(){
    $('input[name="AttendingId"]').val('{{$datas->IdDoctor}}');
    if (selectInstance) {
        selectInstance.setValue(defaultCode);
    }
});
$('input[name="pulserate"]').on('input', function() {
        let value = parseInt($(this).val()); // Get input value as an integer
        
        if (value > 220) {
            $(this).val(220); // Set to max 220 if exceeded
        } else if (value < 0 || isNaN(value)) {
            $(this).val(); // Prevent negative values
        }
});
$('input[name="respiratory"]').on('input', function() {
        let value = parseInt($(this).val());
        
        if (value > 60) {
            $(this).val(60); // Set to max 60 if exceeded
        } else if (value < 0 || isNaN(value)) {
            $(this).val(); // Prevent negative values
        }
});
$('input[name="temperature"]').on('input', function() {
    let value = parseFloat($(this).val());

    if (value > 45) {
        $(this).val(45); // Set to max 50 if exceeded
    } else if (value < 0 || isNaN(value)) {
        $(this).val(); // Prevent negative values
    }
});
$('input[name="height"]').on('input', function() {
        let value = parseInt($(this).val());
        
        if (value > 250) {
            $(this).val(250);
        } else if (value < 0 || isNaN(value)) {
            $(this).val(); // Prevent negative values
        }
});
$('input[name="weight"]').on('input', function() {
        let value = parseInt($(this).val());
        
        if (value > 600) {
            $(this).val(600);
        } else if (value < 0 || isNaN(value)) {
            $(this).val(); // Prevent negative values
        }
});

$('input[name="contactLenses"], input[name="eyeglasses"]').on('change', function() {
    if ($(this).is(':checked')) {
        $('input[name="contactLenses"], input[name="eyeglasses"]').not(this).prop('checked', false);
		$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', false);
		$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', false);
		$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', true).val('');
		//$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', true);
		$('.uncorectedSpan').hide();
		$('.corectedSpan').removeClass('hide');
	}else{
		$('input[name="uncorectedOd"], input[name="uncorectedOs"], input[name="uncorectedNearOd"], input[name="uncorectedNearOs"]').attr('readonly', false).val('');
		$('input[name="corectedOd"], input[name="corectedOs"], input[name="corectedNearOd"], input[name="corectedNearOs"]').attr('readonly', true).val('');
		$('input[name="corectedOd"], input[name="corectedOs"]').attr('required', false);
		//$('input[name="uncorectedOd"], input[name="uncorectedOs"]').attr('required', true);
		$('.corectedSpan').addClass('hide');
		$('.uncorectedSpan').show();
	}
});

	parent.getData("{{ asset('/json/PatientType.json') }}",null,
		function($data){
			$.each($data.patient, function(key,val){
			var selected =  false;
			$('select[name="PatientType"]').append($("<option></option>").attr({"value":val.id,"selected":selected}).text(val.id)); 
		});
		$('select[name="PatientType"]').selectize();
		var PatientType  = $('select[name="PatientType"]')[0].selectize;
		PatientType.setValue('{{ $datas->PatientType }}');
	});							
	$clinicSelect = $('select[name="Clinic"]').selectize({
		onChange: function(value) {
			if (!value.length )
			{
				$('input[name="ClinicCode"]').val('');
				return;	
			}
			$('input[name="ClinicCode"]').val( $('select[name="Clinic"] option:selected').text() );
			
		}
	});
	$clinic = $clinicSelect[0].selectize;
	var Trans = '{{ isset($Transactions[0]) ? e($Transactions[0]->CodeItemPrice) : "" }}';
	if(Trans){
		$('.visualAcuity').hide();
		$('.VitalSigns').css('margin-bottom', '50px');
		$('input[name="uncorectedOd"] , input[name="uncorectedOs"]').attr('required', false);
		$('#Height, #Weight').hide();
	}else{
		$('input[name=height], input[name=weight]').attr('required', true);
		$('.visualAcuity').show();
		//$('input[name="uncorectedOd"] , input[name="uncorectedOs"]').attr('required', true);
		$('.ChiefComplaint').hide();
		$('input[name=ChiefComplain]').attr('required', false);
	}
	$('.editbtn').on('click',function(e){
		patientAddModal.setTitle("Patient - View");
		patientAddModal.setType(BootstrapDialog.TYPE_SUCCESS);
		patientAddModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		patientAddModal.realize();
		patientAddModal.open();
		e.preventDefault();
	});
	$('.vitalhistory').on('click',function(e){
		historyModal.setTitle("Vital Sign History");
		historyModal.setType(BootstrapDialog.TYPE_SUCCESS);
		historyModal.setData("pageToLoad", "{{ '/cms/doctor/past/vitalhistory' }}");
		historyModal.realize();
		historyModal.open();
		e.preventDefault();
	});
	$('._Add').on('click',function(e){
		addVitalModal.setTitle("Patient - V0.ew");
		addVitalModal.setType(BootstrapDialog.TYPE_SUCCESS);
		addVitalModal.setData("pageToLoad", "{{ '/cms/queue/pages/queuePatient' }}/"+$('input[name="IdPatient"]').val()+'/edit');
		addVitalModal.realize();
		addVitalModal.open();
		e.preventDefault();
	});
	$('.savebtn').on('click',function(e){
		
		var Bloodpresure1 = $('input[name=bloodpresure]').val()+" / "+$('input[name=overbloodpresure]').val()
		var csrfToken = $('meta[name="_token"]').attr('content');
		var queueID = $('input[name="_queueid"]').val();
		$.ajax({
			type: 'POST',
			url: '{{ route('vitaltoconsult') }}',
			headers: { 'X-CSRF-TOKEN': csrfToken },
			data: { queueID: queueID},
			success: function (response) {
				console.log('Exit room successfully');
			},
			error: function (error) {
				console.error('Error updating status:', error);
			}
		});
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
    	if( parent.required($('form')) ) return false;
		var userConfirmed = window.confirm('Are you sure you want to proceed?');	
		if (!userConfirmed) {
			return false;
		}
            parent.postData(
                    "{{ '/doctor/vitals/'.$datas->Id }}",
                    {  
						'PcpId':  $('input[name="AttendingId"]').val()
						,'PcpName' : $('input[name="AttendingName"]').val()
						,'PulseRate'             : $('input[name=pulserate]').val()
						,'ChiefComplaint'       : $('input[name=ChiefComplain]').val()
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
						,'ColorVision'          : $('input[name=colorVision]').val()
						,'Deficient'            : $('input[name=deficient]').val()
						,'WithLense'            : WithLense
						,'WithEyeglass'         : WithEyeglass
						,'BMIcategory'			: $('input[name=BMICategory]').val()
						,'_transactionType'		: $('input[name="_transactionType"]').val()
                        ,'_token'               : $('input[name=_token]').val()
                        ,'_method'              :'PUT'
                    },
                    function($data)
                    {  	
		    
			var hyperlink = document.createElement('a');
			hyperlink.href = '/kiosk/vitalsignsqueue';
			var mouseEvent = new MouseEvent('click', {
			    view: window,
			    bubbles: true,
			    cancelable: true
			});
			
			hyperlink.dispatchEvent(mouseEvent);
			(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			
                    }
                );
		e.preventDefault();
	});

	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});
	
	
	
});
</script>
@endsection