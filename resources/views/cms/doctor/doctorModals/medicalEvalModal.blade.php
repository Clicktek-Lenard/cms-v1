<style>
.nav-button {
    padding: 10px 15px;
    border: 1px solid #007bff;
    background-color: #fff;
    color: #007bff;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-align: center;
    min-width: 40px; /* Prevents shrinking */
}
.nav-button.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
.nav-button:hover {
    background-color: #007bff;
    color: white;
}
.family-history {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap; /* Prevents text from being cut off */
    gap: 10px; /* Adds spacing between elements */
}

.family-item {
    display: flex;
    flex-direction: column; /* Aligns label and input properly */
    align-items: center; /* Centers content */
    white-space: nowrap;
}

</style>

<form id="medEval" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
    <div class="medEval">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="col-12 text-center mt-3">
                <div class="d-flex flex-wrap justify-content-center">
                    <input type="button" name="vitalsigns" class="btn btn-outline-primary nav-button vitalsigns active" data-target="section-1" value="I. VITAL SIGNS">
                    <input type="button" name="pastmed" class="btn btn-outline-primary nav-button pastmed" data-target="section-2" value="II. PAST MEDICAL & SURGICAL">
                    <input type="button" name="personal_social" class="btn btn-outline-primary nav-button personal_social" data-target="section-3" value="II. PERSONAL/SOCIAL">
                    <input type="button" name="obstetrics" class="btn btn-outline-primary nav-button obstetrics" data-target="section-4" value="IV. OBSTETRICS & GYNECOLOGICAL">
                    <input type="button" name="family" class="btn btn-outline-primary nav-button family" data-target="section-5" value="V. FAMILY HISTORY">
                    <input type="button" name="physical" class="btn btn-outline-primary nav-button physical" data-target="section-6" value="VI. PHYSICAL EXAMINATION">  
                </div>
            </div>
        <div>
    <hr>
   
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
            <div id="formContainer">
                <!-- First set of fields -->
                <div class="fieldGroup">
                    <div class="form-header">
                        <button type="button" class="btn btn-success addMore">+</button>
                    </div>
                    <div class="form-body">
                        @php
                            $firstEntry = isset($assesAndRec[0]) ? $assesAndRec[0] : null;
                            $assessments = json_decode($firstEntry->Assessment ?? '{}', true);
                            $recommendations = json_decode($firstEntry->Recommendation ?? '{}', true);
                            $findings = json_decode($firstEntry->Findings ?? '{}', true);
                            $Assesmentclass = json_decode($firstEntry->Class ?? '{}', true);
                        @endphp
                        <div class="row">
                            <div class="col-md-9">
                                <label class="bold">Findings</label>
                                <input type="text" value="{{$findings['Findings1'] ?? ''}}" id="findings" name="findings[]" class="form-control findingsInput" list="FindingsList">
				<datalist id="FindingsList" >
					@foreach($Assesment as $data)
						<option value="||{{ $data->Code }}||{{ $data->Findings }}" 
							data-code="{{ $data->Code }}" 
							data-assessment="{{ $data->Assesment }}" 
							data-findings="{{ $data->Findings }}" 
							data-class="{{ $data->Class }}" 
							data-recommendation="{{ $data->Recommendation }}" >
						 <div>
							<span class="description"> {{ $data->Assesment }} </span>
						</div>
						</option>
					@endforeach
				</datalist>
                            </div>
                            <div class="col-md-3">
                                <label class="bold">Class</label>
                                <input type="text" value="{{$Assesmentclass['Class1'] ?? ''}}" id="class" name="class[]" class="form-control classInput" readonly="readonly">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="bold">Assessment</label>
                                <input type="text" id="assessment_1" name="Assessment[]" class="form-control assessmentInput" 
                                    list="AssessmentList" value="{{ $assessments['Assessment1'] ?? '' }}">
                                <datalist id="AssessmentList" >
                                    @foreach($Assesment as $data)
                                        <option value="||{{ $data->Code }}||{{ $data->Assesment }}" 
						data-code="{{ $data->Code }}" 
						data-assessment="{{ $data->Assesment }}" 
                                                data-findings="{{ $data->Findings }}" 
                                                data-class="{{ $data->Class }}" 
                                                data-recommendation="{{ $data->Recommendation }}" >
					 <div>
						<span class="description"> {{ $data->Findings }} </span>
					</div>
					
                                        </option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-12">
                                <label class="bold" >Recommendation</label>
                                <textarea name="Recommendation[]" cols="30" rows="5" id="recommendation_1" class="form-control recommendationInput">{{ $recommendations['Recommendation1'] ?? '' }}</textarea>
                            </div>

                        </div>

                        <div id="formContainer"></div>
                    </div>
                </div>
            </div>
            
        </div>
      
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
        <div id="section-1" class="form-section vitalsigns">
            <div class="panel panel-primary">
                <div class="panel-heading" style="line-height:12px;">Vital Signs </div>
                    <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <label class="bold">Pulse Rate:@if(isset($vitals) && $vitals->PulseRate ) <span style="font-weight:lighter">{{$vitals->PulseRate}}</span> <i>bpm</i> @else  @endif </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="bold">Respiratory Rate:@if(isset($vitals) && $vitals->RespiratoryRate ) <span style="font-weight:lighter">{{$vitals->RespiratoryRate}}</span> <i>cpm</i> @else  @endif </label>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 8px">
                                <div class="col-md-6">
                                    <label class="bold">Temperature:@if(isset($vitals) && $vitals->Temperature ) <span style="font-weight:lighter">{{$vitals->Temperature}}</span> <i>°C</i> @else  @endif </label>
                                </div> 
                                <div class="col-md-6">
                                    <label class="bold">Height:@if(isset($vitals) && $vitals->Height ) <span style="font-weight:lighter">{{$vitals->Height}}</span> <i>cm</i> @else  @endif </label>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 8px">
                                <div class="col-md-6">
                                    <label class="bold">Weight:@if(isset($vitals) && $vitals->Weight ) <span style="font-weight:lighter">{{$vitals->Weight}}</span> <i>kg</i> @else  @endif </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="bold">BMI:@if(isset($vitals) && $vitals->BMI ) <span style="font-weight:lighter">{{$vitals->BMI}}</span> @else  @endif </label>
                                    <span class="" id="bmi-category"></span>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 8px">
                                <div class="col-md-6">
                                    <label class="bold">Blood Pressure:@if(isset($vitals) && $vitals->BloodPresure ) <span style="font-weight:lighter">{{$vitals->BloodPresure}} /  {{$vitals->BloodPresureOver}}</span> <i>mmHg</i> @else  @endif  </label>
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
                                            <th style="width:20%"></th>
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
                                                    <div class="col-xs-5">
                                                            <label>OD 20/{{$vitals->UcorrectedOD ?? ''}}</label>
                                                    </div>
                                                    <div class="col-xs-5">
                                                        <label>OS 20/{{$vitals->UcorrectedOS ?? ''}}</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Add Near Vision Uncorrected Fields Here -->
                                                
                                                <div class="row">
                                                    <div class="col-xs-5">
                                                        <label>OD J/{{$vitals->UncorrectedNearOD ?? ''}}</label>
                                                    </div>
                                                    <div class="col-xs-5">
                                                        <label>OS J/{{$vitals->UncorrectedNearOS ?? ''}}</label>
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
                                                        
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-xs-5">
                                                        <label>OD 20/{{$vitals->CorrectedOD ?? ''}}</label>
                                                    </div>
                                                    <div class="col-xs-5">
                                                        <label>OS 20/{{$vitals->CorrectedOS ?? ''}}</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Add Near Vision Corrected Fields Here -->
                                                <div class="row">
                                                    <div class="col-xs-5">
                                                        <label>OD J/{{$vitals->CorrectedNearOD ?? ''}}</label>     
                                                    </div>
                                                    <div class="col-xs-5">
                                                        <label>OS J/{{$vitals->CorrectedNearOS ?? ''}}</label> 
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-xs-12 text-left bold" style="display: block;">
                                        <label class="bold">
                                            <input type="checkbox" name="contactLenses" 
                                            @if(isset($vitals->WithContactLens) && $vitals->WithContactLens === "Y") checked @endif> 
                                            with Contact Lenses
                                        </label> 
                                        &nbsp;
                                        <label class="bold">
                                            <input type="checkbox" name="eyeglasses" 
                                            @if(isset($vitals->WithEyeGlass) && $vitals->WithEyeGlass === "Y") checked @endif> 
                                            with Eyeglasses
                                        </label>
                                    </div>
                                </div>
                            </div>                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary form-section pastmed" id="section-2" style="margin-bottom: 50px" hidden>
                <div class="panel-heading" style="line-height:12px;">Past Medical & Surgical</div>
                    <div class="panel-body" >
                        <div class="row">
                            <div class="col-md-12">
                                <div style="text-align: justify">
                                <h4>I. PAST MEDICAL & SURGICAL HISTORY (current medications, past diseases, hospitalizations, operations) 
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
                                            <td><input type="text" name="liverglad" required class="form-control table_input" value="{{$PEdata[0]->LiverGallbladderDisease ?? ''}}"></td>
                                            <td>Diabetes Mellitus</td>
                                            <td><input type="text" name="diabetisM" class="form-control table_input" value="{{$PEdata[0]->DiabetesMellitus ?? ''}}"></td>
                                        </tr>
                                        <tr>
                                            <td>Heart Disease</td>
                                            <td><input type="text" name="heartDisease" class="form-control table_input" value="{{$PEdata[0]->Heartdisease ?? ''}}"></td>
                                            <td>Chronic Headache/Migraine</td>
                                            <td><input type="text" name="ChronicHeadache" class="form-control table_input" value="{{$PEdata[0]->ChronicHeadacheMigraine ?? ''}}"></td>
                                        </tr>
                                        <tr>
                                            <td>Asthma / Allergy</td>
                                            <td><input type="text" name="asthmaAllergy" class="form-control table_input" value="{{$PEdata[0]->AsthmaAllergy ?? ''}}"></td>
                                            <td>Hypertension</td>
                                            <td><input type="text" name="Hypertension" class="form-control table_input" value="{{$PEdata[0]->Hypertension ?? ''}}"></td>
                                        </tr>
                                        <tr>
                                            <td>Tuberculosis</td>
                                            <td><input type="text" name="Tuberculosis" class="form-control table_input" value="{{$PEdata[0]->Tuberculosis ?? ''}}"></td>
                                            <td>Kidney Disease</td>
                                            <td><input type="text" name="KidneyDisease" class="form-control table_input" value="{{$PEdata[0]->KidneyDisease ?? ''}}"></td>
                                        </tr>
                                        <tr>
                                            <td>Ear/Nose/Throat Disorder</td>
                                            <td><input type="text" name="EarNoseThroat" class="form-control table_input" value="{{$PEdata[0]->EarNoseThroatDisorder ?? ''}}"></td>
                                            <td>Cancer</td>
                                            <td><input type="text" name="Cancer" class="form-control table_input" value="{{$PEdata[0]->Cancer ?? ''}}"></td>
                                        </tr>
                                        <tr>
                                            <td>Eye Disorder</td>
                                            <td><input type="text" name="EyeDisorder" class="form-control table_input" value="{{$PEdata[0]->EyeDisorder ?? ''}}"></td>
                                            <td>Sexually Transmitted Disease</td>
                                            <td><input type="text" name="SexuallyTransmitted" class="form-control table_input" value="{{$PEdata[0]->SexuallyTransmittedDisease ?? ''}}"></td>
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
                        </div>
                    </div>
                </div>
                    <div class="panel panel-primary form-section personal_social" id="section-3" style="margin-bottom: 50px" hidden>
                        <div class="panel-heading" style="line-height:12px;">Personal / Social History</div>
                            <div class="panel-body" >
                                <h4>II. PERSONAL / SOCIAL HISTORY</h4>
								<div class="row">
									<div class="col-xs-12 col-md-12">
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="form-group-inline">
                                                <label>Present Smoker?</label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="presentSmokerY" @if(isset($PEdata[0]) && $PEdata[0]->PresentSmoker === "Y") checked @endif> Yes
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="presentSmokerN" @if(isset($PEdata[0]) && $PEdata[0]->PresentSmoker === "N") checked @endif> No
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="presentSmokerSD" readonly="readonly" value="{{isset($PEdata[0]) && $PEdata[0]->PresentSmokerSticksPerDay}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
                                                    <span class="input-group-addon">stick(s)/day</span>
                                                    <input type="number" class="form-control" name="presentSmokerYears" readonly="readonly" value="{{isset($PEdata[0]) && $PEdata[0]->PresentSmokerYears}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
                                                    <span class="input-group-addon">Year(s)</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="form-group-inline">
                                                <label>Previous Smoker?</label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="prevY" @if(isset($PEdata[0]) && $PEdata[0]->PreviousSmoker === "Y") checked @endif> Yes
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="prevN" @if(isset($PEdata[0]) && $PEdata[0]->PreviousSmoker === "N") checked @endif> No
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="previousSmokerSD" readonly="readonly" value="{{ $PEdata[0]->PreviousSmokerSticksPerDay ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
                                                    <span class="input-group-addon">stick(s)/day</span>
                                                    <input type="number" class="form-control" name="previousSmokerYears" readonly="readonly" value="{{ $PEdata[0]->PreviousSmokerYears ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
                                                    <span class="input-group-addon">Year(s)</span>
                                                </div>
                                            </div>
                                        </div>
									</div>
                                </div>
                                <div class="row col-xs-12 col-md-12">
									<div class="col-xs-12 col-md-6">
										<div class="form-group-inline">
											<label>Present Alcoholic Drinker?</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PresDrinkerY" @if(isset($PEdata[0]) && (!empty($PEdata[0]->PresentAlcoholDrinker) || $PEdata[0]->PresentAlcoholDrinker !== null)) checked @endif> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PresDrinkerN" @if(isset($PEdata[0]) && empty($PEdata[0]->PresentAlcoholDrinker)) checked @endif> No
											</label>
											<div class="input-group">
												<input type="number" class="form-control" name="PresBottle" readonly="readonly" value="{{$PEdata[0]->PresentAlcoholDrinker ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);" >
												<span class="input-group-addon">Bottle(s)/Week</span>
											</div>
										</div>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
										<div class="form-group-inline">
											<label>Previous Alcoholic Drinker?</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PrevDrinkerY" @if(isset($PEdata[0]) && (!empty($PEdata[0]->PrevAlcoholDrinker) || $PEdata[0]->PrevAlcoholDrinker !== null)) checked @endif> Yes
											</label>
											<label class="checkbox-inline">
												<input type="checkbox" name="PrevDrinkerN" @if(isset($PEdata[0]) && $PEdata[0]->PrevAlcoholDrinker == '') checked @endif> No
											</label>
											<div class="input-group">
												<input type="number" class="form-control" name="PrevDrinkerYear" readonly="readonly" value="{{$PEdata[0]->PrevAlcoholDrinker ?? ''}}" maxlength="3" oninput="if(this.value.length > 3) this.value = this.value.slice(0, 3);">
												<span class="input-group-addon">Year(s)</span>
											</div>
										</div>
									</div>
                                    
								</div>
                            </div>
                            <div class="row" style="margin-bottom: 10px;">
                                <div class="col-sm-1 col-md-1 pad-right-0-md text-right-md">
                                    <label class="bold ">Others: </label>
                                </div>
                                <div class="col-sm-12 col-md-10 pad-1-md">
                                    <input type="text" class="form-control" name="PersonalOthers" value="{{$PEdata[0]->PersonalSocialOther ?? ''}}" style="border: none; border-bottom: solid black 1px; outline: none;">
                                </div>
                            </div>     
                        </div>
                        <div class="panel panel-primary form-section obstetrics" id="section-4" style="margin-bottom: 50px" hidden>
                            <div class="panel-heading" style="line-height:12px;">Obstetrics & gynecological History</div>
                                <div class="panel-body" >
                                    <h4>III. OBSTETRICS & GYNECOLOGICAL HISTORY (Not applicable) </h4>
                                    <div class="col-xs-12 col-md-12">
                                        <div class="row align-items-center">
                                            <!-- First Day of Last Menstruation -->
                                            <div class="col-md-6">
                                                <label class="bold">First Day of Last Menstruation</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" name="fDayofMendtruation" value="{{$PEdata[0]->FirstDayofLastMenstruation ?? ''}}">
                                                </div>
                                            </div>
                                        
                                            <!-- Regular? -->
                                            <div class="col-md-6 d-flex align-items-center">
                                                <label class="bold me-2">Regular?</label> &nbsp;
                                                <label class="checkbox-inline me-2">
                                                    <input type="checkbox" name="Regular" @if(isset($PEdata[0]) && $PEdata[0]->Regular == "Yes") checked @endif> Yes
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" name="NRegular" @if(isset($PEdata[0]) && $PEdata[0]->Regular == "No") checked @endif> No
                                                </label>
                                            </div>
                                        </div>
                                            <br>
                                            <div class="col-xs-5 col-sm-5 col-md-5">
                                                <div class="form-group">
                                                    <div class="input-group" style=" align-items: center;">
                                                        <span class="input-group-addon">G</span>
                                                        <input type="number" class="form-control" id="g_value" name="g_value" value="{{$g_value ?? ''}}" style="max-width: 80px" maxlength="1" oninput="if(this.value.length > 1) this.value = this.value.slice(0, 1);">
                                                        <span class="input-group-addon">P</span>
                                                        <input type="number" class="form-control" id="p_value" name="p_value" value="{{$p_value ?? ''}}" placeholder="" style="max-width: 80px" maxlength="1" oninput="if(this.value.length > 1) this.value = this.value.slice(0, 1);">
                                                        <span class="input-group-addon"></span>
                                                        <input type="text" class="form-control" id="p1_value" name="p1_value" value="{{$p1_value ?? ''}}" placeholder="(_-_-_-_)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
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
                                </div>
                            </div>
                            <div class="panel panel-primary form-section family" id="section-5" style="margin-bottom: 50px" hidden>
                                <div class="panel-heading" style="line-height:12px;">Family History</div>
                                    <div class="panel-body" >
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12 family-history">
                                                <h4>IV. FAMILY HISTORY
                                                    <label style="float: right;"><input type="checkbox" name="family_None"><i style="color:blue" class="small"> None</i></label>
                                                </h4>
                                                <div class="form-group-inline">
                                                    <div class="col-xs-3 col-sm-2 col-md-3 family-item">
                                                        <label class="checkbox-inline">
                                                            Bronchial Asthma
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->BronchialAsthma ?? ''}}" name="BronchialAsthma">
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item">
                                                        <label class="checkbox-inline">
                                                            Goiter
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->Goiter ?? ''}}" name="Goiter">
                                                    </div>  
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item">
                                                        <label class="checkbox-inline">
                                                            Heart Disease
                                                        </label>
                                                        <input type="text" class="underline-input form-control" value="{{$PEdata[0]->FHeartDisease ?? ''}}" name="HeartDiseaseF">
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item">  
                                                        <label class="checkbox-inline">
                                                            Kidney Disease
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->KedneyDisease ?? ''}}" name="KidneyDiseaseF">
                                                    </div>
                                                </div>
                                                <div class="form-group-inline">
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item"> 
                                                        <label class="checkbox-inline">
                                                            Diabetes Mellitus
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->FDiabetesMellitus ?? ''}}" name="DiabetesMellitusF">
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item"> 
                                                        <label class="checkbox-inline">
                                                            PTB
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->PTB ?? ''}}" name="PTB">
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item"> 
                                                        <label class="checkbox-inline">
                                                            Hypertension
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->FHypertension ?? ''}}" name="HypertensionF">
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-3 family-item"> 
                                                        <label class="checkbox-inline">
                                                            Others
                                                        </label>
                                                        <input type="text" class="underline-input fnone form-control" value="{{$PEdata[0]->FamilyOthers ?? ''}}" name="FamilyOthers">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-primary form-section physical" id="section-6" style="margin-bottom: 50px" hidden>
                                    <div class="panel-heading" style="line-height:12px;">Physical Examination</div>
                                        <div class="panel-body" >
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4>V. PHYSICAL EXAMINATION
                                                        <label style="float: right;"><input type="checkbox" name="PE_normal"><i style="color:blue" class="small"> Normal</i></label>
                                                    </h4>
                                                    <table class="table table-bordered">
                                                <thead>
            
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="bold">Skin</td>
                                                    <td><input type="text" name="Skin" class="form-control   PE_normal skin" value="{{$PEdata[0]->Skin ?? ''}}" ></td>
                                                    <td class="bold">Lungs</td>
                                                    <td><input type="text" name="Lungs" class="form-control  PE_normal" value="{{$PEdata[0]->Lungs ?? ''}}" ></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Head/Scalp</td>
                                                    <td><input type="text" name="HeadScalp" class="form-control  PE_normal" value="{{$PEdata[0]->HeadScalp ?? ''}}"></td>
                                                    <td class="bold">Heart</td>
                                                    <td><input type="text" name="Heart" class="form-control  PE_normal" value="{{$PEdata[0]->Heart ?? ''}}"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Eyes</td>
                                                    <td><input type="text" name="Eyes" class="form-control  PE_normal" value="{{$PEdata[0]->Eyes ?? ''}}"></td>
                                                    <td class="bold">Abdomen</td>
                                                    <td><input type="text" name="Abdomen" class="form-control  PE_normal" value="{{$PEdata[0]->Abdomen ?? ''}}"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Ears/Hearing</td>
                                                    <td><input type="text" name="EarsHearing" class="form-control  PE_normal" value="{{$PEdata[0]->EarsHearing ?? ''}}"></td>
                                                    <td class="bold">Back/Flanks</td>
                                                    <td><input type="text" name="BackFlanks" class="form-control  PE_normal" value="{{$PEdata[0]->BlackFlanks ?? ''}}"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Nose/Sinuses</td>
                                                    <td><input type="text" name="NoseSinuses" class="form-control  PE_normal" value="{{$PEdata[0]->NoseSinuses ?? ''}}"></td>
                                                    <td class="bold">Extremities</td>
                                                    <td><input type="text" name="Extremities" class="form-control  PE_normal" value="{{$PEdata[0]->Extremities ?? ''}}"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Mouth/Throat</td>
                                                    <td><input type="text" name="MouthThroat" class="form-control  PE_normal" value="{{$PEdata[0]->MouthThroat ?? ''}}"></td>
                                                    <td class="bold">Neurological</td>
                                                    <td><input type="text" name="Neurological" class="form-control  PE_normal" value="{{$PEdata[0]->Neurological ?? ''}}"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Neck/Thyroid</td>
                                                    <td><input type="text" name="NeckThyroid" class="form-control  PE_normal" value="{{$PEdata[0]->NeckThyroid ?? ''}}"></td>
                                                    <td class="bold">Genitals/Urinary</td>
                                                    <td>
                                                        <input type="text" list="CBA" class="form-control" value="{{$PEdata[0]->GenitalsUrinary ?? ''}}" name="GenitalsUrinary">
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
                                                    <td><input type="text" name="ChestBreastAxilla" class="form-control  PE_normal" value="{{$PEdata[0]->ChestBreastAxilla ?? ''}}"></td>
                                                    <td class="bold">Anus/Rectum</td>
                                                    <td>
                                                        <input type="text" list="CBA" class="form-control" value="{{$PEdata[0]->AnusRectum ?? ''}}" name="AnusRectum">
                                                        <datalist id="CBA">
                                                            <option value="Normal">Normal</option>
                                                            <option value="Not Done">Not Done</option>
                                                            <option value="Refused">Refused</option>
                                                            <option value="Waived">Waived</option>
                                                        </datalist>
                                                        {{-- <select name="AnusRectum" id="" class="form-control">
                                                            <option value="Normal">Normal</option>
                                                            <option value="Not Done" @if(isset($PEdata[0]) && $PEdata[0]->AnusRectum == "Not Done" ) selected @endif>Not Done</option>
                                                            <option value="Refused"  @if(isset($PEdata[0]) && $PEdata[0]->AnusRectum == "Refused" ) selected @endif>Refused</option>
                                                            <option value="Waived"  @if(isset($PEdata[0]) && $PEdata[0]->AnusRectum == "Waived" ) selected @endif>Waived</option>
                                                        </select> --}}
                                                    </td>
                                                    {{-- <td><input type="text" name="AnusRectum" class="form-control  PEtable" value="{{$PEdata[0]->AnusRectum ?? ''}}" placeholder="Refused"></td> --}}
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
                                    </div>
            </div>
    </div>
</form>
<script>
$(document).ready(function(e) {
    $('.medEval').height($(window).height()-120);

    $('input.nav-button').click(function() {
        // Remove 'active' class from all buttons
        $('input.nav-button').removeClass('active');
        // Add 'active' class to the clicked button
        $(this).addClass('active');
        
        // Hide all sections
        $('.form-section').attr('hidden', true);
        
        // Show the relevant section based on the clicked button's name
        var sectionClass = '.' + $(this).attr('name');
        console.log(sectionClass);
        $(sectionClass).attr('hidden', false);
    });

$(document).on('input', '.findingsInput', function () {
	var selectedValue = $(this).val().trim();
	// Find the closest row (instead of .fieldGroup)
	var row = $(this).closest('.row');

	var option = $("#FindingsList option").filter(function () {
		return $(this).val() === selectedValue;
	});

	if (selectedValue === '') {
		row.next().find('.assessmentInput').val('').html('');  // Target the previous row for assessmentInput
		row.find('.classInput').val('');     // Target the previous row for Class
		row.next().find('.recommendationInput').val('');   // Recommendation is in the same row
	} else if (option.length > 0) {
		var code = option.attr("data-code") || '';
		var assessment = option.attr("data-assessment") || '';
		var findings = option.attr("data-findings") || '';
		var classVal = option.attr("data-class") || '';
		var recommendation = option.attr("data-recommendation") || '';
		row.find('.findingsInput').val(findings.replace(code+"||", ""));  // Set Findings
		row.next().find('.assessmentInput').val(assessment).html(assessment);  // Set assessmentInput
		row.find('.classInput').val(classVal);     // Set Class
		row.next().find('.recommendationInput').val(recommendation); // Set Recommendation
		$(this).val(findings.replace("||"+code+"||", ""));
	}
});

$(document).on('input', '.assessmentInput', function () {
	var selectedValue = $(this).val().trim();
	// Find the closest row (instead of .fieldGroup)
	var row = $(this).closest('.row');

	var option = $("#AssessmentList option").filter(function () {
	
		return $(this).val() === selectedValue;
	});

	if (selectedValue === '') {
		row.prev().find('.findingsInput').val('');  // Target the previous row for Findings
		row.prev().find('.classInput').val('');     // Target the previous row for Class
		row.find('.recommendationInput').val('');   // Recommendation is in the same row
	} else if (option.length > 0) {
		var code = option.attr("data-code") || '';
		var assessment = option.attr("data-assessment") || '';
		var findings = option.attr("data-findings") || '';
		var classVal = option.attr("data-class") || '';
		var recommendation = option.attr("data-recommendation") || '';
		row.prev().find('.assessmentInput').val(assessment.replace(code+"||", ""));  // Set Findings
		row.prev().find('.findingsInput').val(findings);  // Set Findings
		row.prev().find('.classInput').val(classVal);     // Set Class
		row.find('.recommendationInput').val(recommendation); // Set Recommendation
		$(this).val(assessment.replace("||"+code+"||", ""));
	}
});
if($('input[name=Gender]').val() == 'M'){
		$('input[name=fDayofMendtruation] , input[name=Regular], input[name=NRegular], input[name=MenopausalAge], input[name=menarch], input[name=OBGYNEOthers], input[name=g_value], input[name=p_value], input[name=p1_value]').attr('disabled', true);
	}
    var counter = 2; // Start at 2 because 1st is already displayed
        var assessments = @json($assessments);
        var recommendations = @json($recommendations);
        var findings = @json($findings);
        var Assessclass = @json($class);
        // Loop through additional assessments and append them
        $.each(assessments, function (key, value) {
            var num = key.replace('Assessment', ''); // Extract number
            if (num > 1) { // Skip the first one (already displayed)
                var newFields = `
                    <div class="fieldGroup">
                        <hr>
                        <div class="form-header">
                            <button type="button" class="btn btn-danger removeField">-</button>
                        </div>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <label class="bold">Findings</label>
                                    <input type="text" value="${findings['Findings' + num] || ''}" id="findings" name="findings[]" list="FindingsList"  class="form-control findingsInput">
                                </div>
                                <div class="col-md-3">
                                    <label class="bold">Class</label>
                                    <input type="text" value="${Assessclass['Class' + num] || ''}" id="class" name="class[]" class="form-control classInput" readonly="readonly">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="bold">Assessment</label>
                                    <input type="text" name="Assessment[]" list="AssessmentList" 
                                           class="form-control assessmentInput" id="assessment_${num}" 
                                           value="${value}">
                                </div>
                                <div class="col-md-12">
                                    <label class="bold">Recommendation</label>
                                    <textarea name="Recommendation[]" class="form-control recommendationInput" 
                                              id="recommendation_${num}">${recommendations['Recommendation' + num] || ''}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $("#formContainer").append(newFields);
                counter++;
            }
        });

        // Add new empty fields when clicking Add button
        $(".addMore").click(function () {
            var newFields = `
                <div class="fieldGroup">
                    <hr>
                    <div class="form-header">
                        <button type="button" class="btn btn-danger removeField">-</button>
                    </div>
                    <div class="form-body">
                         <div class="row">
                                <div class="col-md-9">
                                    <label class="bold">Findings</label>
                                    <input type="text" id="findings" name="findings[]" list="FindingsList"  class="form-control findingsInput">
                                </div>
                                <div class="col-md-3">
                                    <label class="bold">Class</label>
                                    <input type="text" id="class" name="class[]" class="form-control classInput" readonly="readonly">
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="bold">Assessment</label>
                                <input type="text" name="Assessment[]" list="AssessmentList" 
                                       class="form-control assessmentInput" id="assessment_${counter}">
                            </div>
                            <div class="col-md-12">
                                <label class="bold">Recommendation</label>
                                <textarea name="Recommendation[]" class="form-control recommendationInput" 
                                          id="recommendation_${counter}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $("#formContainer").append(newFields);
            counter++;
        });

        // Remove fields
        $(document).on("click", ".removeField", function () {
            $(this).closest(".fieldGroup").remove();
        });
});

</script>