<form id="medEval" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
   <div class="medEval">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
        <div class="row">
            <div class="col-md-12 ">
                <label for="" class="bold">Assessment</label>
                <input type="text" name="Assessment" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <label for="" class="bold">Recommendation</label>
                <textarea name="Recommendation" id="" cols="30" rows="18" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
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
    </div>
</form>
<script>
$(document).ready(function(e) {
    $('.medEval').height($(window).height()-120);
});

</script>