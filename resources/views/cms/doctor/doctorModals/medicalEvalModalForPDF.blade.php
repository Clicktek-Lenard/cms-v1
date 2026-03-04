<form id="medEval" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
    <div class="medEval">
     <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
        @php
        $firstEntry = isset($assesAndRec[0]) ? $assesAndRec[0] : null;
        $assessments = json_decode($firstEntry->Assessment ?? '{}', true);
        $recommendations = json_decode($firstEntry->Recommendation ?? '{}', true);
        $findings = json_decode($firstEntry->Findings ?? '{}', true);
        $Assesmentclass = json_decode($firstEntry->Class ?? '{}', true);
        @endphp
        <div class="form-header">
            <button type="button" class="btn btn-success addMore">+</button>
        </div>
    <div class="row">
        <div class="col-md-10">
            <label class="bold">Findings</label>
            <input type="text" value="{{$findings['Findings1'] ?? ''}}" id="findings" name="findings[]" class="form-control findingsInput"  list="FindingsList">
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
        <div class="col-md-2">
            <label class="bold">Class</label>
            <input type="text" value="{{$Assesmentclass['Class1'] ?? ''}}" id="class" name="class[]" class="form-control classInput" readonly="readonly">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label class="bold">Assessment</label>
            <input type="text" id="assessment_1" name="Assessment[]" class="form-control assessmentInput" 
                list="AssessmentList" value="{{ $assessments['Assessment1'] ?? '' }}">
            <datalist id="AssessmentList">
                @foreach($Assesment as $data)
			<option value="||{{ $data->Code }}||{{ $data->Assesment }}" 
					data-code="{{ $data->Code }}" 
					data-assessment="{{ $data->Assesment }}" 
					data-findings="{{ $data->Findings }}" 
					data-class="{{ $data->Class }}" 
					data-recommendation="{{ $data->Recommendation }}" >
				 <div>
					<span class="code">
						<span class="name"> {{ $data->Findings }}  </span>
					</span>
					<span class="description"> {{ $data->Assesment }} </span>
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
     <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
        {!! $pdfviewer !!}
     </div>
 </form>
 <script>
 $(document).ready(function(e) {
 
 
 
	$('.medEval').height($(window).height()-120);

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
                                <div class="col-md-10">
                                    <label class="bold">Findings</label>
                                    <input type="text" value="${findings['Findings' + num] || ''}" id="findings" name="findings[]" class="form-control findingsInput">
                                </div>
                                <div class="col-md-2">
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
                                <div class="col-md-10">
                                    <label class="bold">Findings</label>
                                    <input type="text" id="findings" name="findings[]" class="form-control findingsInput">
                                </div>
                                <div class="col-md-2">
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