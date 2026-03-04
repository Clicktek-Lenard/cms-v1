@section('style')
<style>
    #ItemListTable {
        table-layout: auto !important;
        width: 100% !important;
    }
</style>
@endsection
<form id="declineModal" class="form-horizontal" role="form" autocomplete="off">    <!--pcp v2-->
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="doctor_id" value="{{ $physicianDatas[0]->Id }}">
    <input type="hidden" name="doctor_status" value="{{ $physicianDatas[0]->Status }}">

<div class="row form-group row-md-flex-center">
    <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
        <label class="bold ">Physician Full Name<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-8 col-md-8">
    	<input type="text" class="form-control" name="names" value="{{ $physicianDatas[0]->FullName }}"  readonly>
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden" >
    </div>
</div>


<div class="modal-cms-header">
    <div class="col-menu-15 table-items">
    </div>
</div>
<script>
 $(document).ready(function(e) {
    const fieldNameMap = {
        "email": "Email Address",
        "nwdBranch[]": "Branch Origin",
        "schedule[]": "Schedule Day",
        "timestart[]": "Start Time",
        "timeend[]": "End Time",
        "firstengagement[]": "First Engagement",
        "lastengagement[]": "Last Engagement",
        "inputby[]": "Encoded By",
        "appointment[]": "By Appointment",
        "lastname": "Last Name",
        "firstname": "First Name",  
        "middlename": "Middle Name",
        "suffix": "Suffix",
        "dob": "Date of Birth",
        "specialty": "Specialty",
        "subSpecialty": "Sub Specialty",
        "prcno": "PRC No.",
        "validity": "PRC Validity",
        "mobile": "Mobile No.",
        "p_subgroup": "Position",
        "logged_in_user": "Updated By",
        "update_timeDate": "Date and Time",
        "applicationLetter": "ApplicationLetter",
        "curriculumVitae": "CV",
        "medicalSchoolDiploma": "School Diploma",
        "prcId": "PRC License",
        "residencySpecialtyCert": "Residency Certificate",
        "diplomateFellowCert": "Diplomate Certificate",
        "philHealth": "PhilHealth",
        "ptr": "PTR",
        "bir": "BIR",
        "MOA": "Signed MOA",
        "primaryCarePhysician": "Primary Care Physician",
        "specialistConsultant": "Specialist/Consultant/Reader",
        "visiting": "Visiting",
        "resigned": "Resigned",
        "regular": "Regular Physician",
        "reliever": "Reliever Physician",
    };

    var rawData = {!! json_encode($physicianDatas) !!};
    if (!Array.isArray(rawData)) rawData = [rawData];

   let flattenedRows = [];

    rawData.forEach(item => {
        if (!item.ApprovalLogs) return;

        let logs;
        try {
            logs = JSON.parse(item.ApprovalLogs);
        } catch (e) {
            logs = {};
        }

        for (let key in logs) {
            if (key === 'logged_in_user' || key === 'update_timeDate') continue;
            const normalizedKey = key.replace(/\[\d*\]/g, '[]');
            const fieldLabel = fieldNameMap[normalizedKey] || key;
            const log = logs[key];

            // Only show entries that were changed
            if (log.oldVal !== log.newVal) {
                flattenedRows.push({
                    field: fieldLabel,
                    oldVal: log.oldVal || '',
                    newVal: log.newVal || '',
                    date: log.updateTime || '',
                    updatedBy: log.updatedBy || ''
                });
            }
        }
    });

    var $dom = (flattenedRows.length >= 11) ? "frtiS" : "frti";

    let $html = `
        <div class="table-responsive">
            <table id="ItemListTable" class="table table-striped table-hover dt-responsive display" style="width:100%;" cellspacing="0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Input Name</th>
                        <th>Data From</th>
                        <th>Data To</th>
                        <th>Date and Time</th>
                        <th>Update By</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>`;
    
    $('.table-items').append($html);

    // Initialize DataTable
    var table = $('#ItemListTable').DataTable({
        data: flattenedRows,
        autoWidth: true,
        deferRender: true,
        columns: [
            { data: null, defaultContent: '' },
            { data: 'field' },
            { data: 'oldVal' },
            { data: 'newVal' },
            { data: 'date' },
            { data: 'updatedBy' }
        ],
        responsive: { details: { type: 'column' } },
        columnDefs: [
            { className: 'control', orderable: false, targets: 0, width: "10px" },
            { targets: 1, width: "150px" },
            { targets: 2, width: "150px" },
            { targets: 3, width: "150px" },
            { targets: 4, width: "150px" },
            { targets: 5, width: "150px" }
        ],
        order: [4, 'desc'],
        dom: $dom,
        scrollY: $(window).height() - 378
    });

    $('.dataTables_filter input').addClass('form-control search').attr({ 'type': 'text', 'placeholder': 'Search' });
});


</script>
    

