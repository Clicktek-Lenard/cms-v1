<!--@extends('app')-->
<link href="../../../css/datetpicker.ui.css" rel="stylesheet" type="text/css">

@section('style')
<style>
.data-row { color: #337AB7; text-decoration: none; cursor:pointer; }

.result-container, .result-container1 {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    height: 256.45px;
    background-color: #f5f5f5;
    overflow-y: auto;
}

.ip-result-container {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    height: 500px;
    background-color: #f5f5f5;
    overflow-y: auto;
}

.tab-pane {
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2 ease;
  position: absolute; /* Optional, to overlap panes */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.tab-pane.active {
  opacity: 1;
  visibility: visible;
  position: relative; /* Reset for active */
}

.ip-box {
    background-color: #d9edf7;     /* light blue */
    border: 1px solid #bce8f1;      /* matching border */
    border-radius: 8px;
    padding: 30px 20px;
    margin-top: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.ip-title {
    font-size: 28px;
    font-weight: bold;
    color:rgb(0, 0, 0);
    margin-bottom: 15px;
}

.ip-value {
    font-size: 46px;
    font-weight: 700;
    color: #333;
}

.swal-btn-custom2 {
    background-color: #28a745 !important;
    color: white !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    border-radius: 4px !important;
}

.swal-btn-custom2.cancel {
    background-color: #6c757d !important;
    color: white !important;
}


</style>
@endsection
<script src="{{ asset('/js/sweetalert2.all.min.js?0') }}"></script>  

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb">
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style="border-radius:0;">
                    <li class="active"><a href="{{ '/cms/services' }}" class="waiting">Services <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>

    

    <div class="body-content row">
        <div class="col-menu-15 table-queue">

        <div class="form-group d-flex" style="margin-top:20px;" id="tabButtons">
            <button type="button" class="btn btn-success util-btn mr-2 active" data-target="ping">Ping</button>
            <button type="button" class="btn btn-danger util-btn" data-target="ipchicken">Check Your IP</button>
            <button type="button" class="btn btn-info util-btn mr-2" data-target="jasper">Jasper Server</button>
            <button type="button" class="btn btn-primary util-btn mr-2" data-target="socket">Socket</button>
            <button type="button" class="btn btn-warning util-btn" data-target="sql">SQL</button>
            <button type="button" class="btn btn-purple util-btn" style="background-color: purple; color: white;" data-target="hl7">HL7</button>
        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="ping">
                <!-- PING PANEL -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-success" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Ping</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <!-- Branch Dropdown -->
                                <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;" disabled>
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->GatewayIP}}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <!-- Buttons  -->
                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsPing">
                                    <button type="button" class="btn btn-primary util-btn mr-2 active" data-target="central">Central</button>
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="dns">DNS</button>
                                    <button type="button" class="btn btn-info util-btn mr-2" data-target="gateway">Gateway</button>
                                    <button type="button" class="btn btn-warning util-btn mr-2" data-target="traceroute">Traceroute</button>
                                </div>

                                <!-- Results -->
                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container">
                                        <div id="resultsPing">
                                            <div id="cardNumbersPing"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnPing" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-space-shuttle"></i> Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="jasper">
                <!-- JASPER PANEL -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-info" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Jasper Server</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;" disabled>
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->Code }}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsJasper">
                                    <button type="button" class="btn btn-primary util-btn mr-2 active" data-target="status">Status</button>
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="start">Start</button>
                                    <button type="button" class="btn btn-danger util-btn mr-2" data-target="stop">Stop</button>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container1">
                                        <div id="resultsJasper">
                                            <div id="cardNumbersJasper"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnJasper" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-check-circle"></i> Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="socket">
                <!-- SOCKET PANEL -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-primary" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Socket</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;" disabled>
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->Code }}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsSocket">
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="run">Run</button>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container1">
                                        <div id="resultsSocket">
                                            <div id="cardNumbersSocket"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnSocket" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-check-circle"></i> Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="sql">
                <!-- SQL PANEL -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-warning" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">SQL</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;" disabled>
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->Code }}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsSql">
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="sqlStatus">Status</button>
                                    <button type="button" class="btn btn-info util-btn mr-2" data-target="sqlStart">Start</button>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container1">
                                        <div id="resultsSql">
                                            <div id="cardNumbersSql"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnSql" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-check-circle"></i> Go</button>

                                        <button id="skipbtnSql" type="button" class="btn btn-warning" style="margin-top: 15px; display:none;">
                                            <i class="fa fa-forward"></i> Skip
                                        </button>

                                        <button id="resetbtnSql" type="button" class="btn btn-danger" style="margin-top: 15px; display:none;">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="ipchicken">
                <!-- IP Chicken -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-danger" style="margin-top:20px;">
                        <div class="panel-heading" style="line-height:12px;">Check Your IP</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <!-- <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;">
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->Code }}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsIpChicken">
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="Status">Status</button>
                                </div> -->

                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container1">
                                        <div id="resultsIpChicken">
                                            <div id="cardNumbersIpChicken"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnIpChicken" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-check-circle"></i> Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="hl7">
                <!-- HL7 PANEL -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-primary" style="margin-top: 20px;  border-color: purple;">
                        <div class="panel-heading" style="line-height: 12px; background-color: purple;  border-color: purple;">HL7 Mounting</div>
                        <div class="panel-body">
                            <div class="table-queue">
                                <div class="row form-group row-md-flex-center" style="margin-left:20px; margin-bottom: 15px;">
                                    <label style="margin-right: 10px; font-weight: bold;">Branch:</label>
                                    @if(count($ClinicCode) != 1)
                                        <select class="form-control" name="Clinic" id="Clinic" style="width: 200px;" disabled>
                                            @foreach($Clinics as $clinic)
                                                <option value="{{ $clinic->Code }}" {{ session('userClinicCode') == $clinic->Code ? 'selected' : '' }}>
                                                    {{ strtoupper($clinic->Description) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="Clinic" value="{{ session('userClinicCode') }}">
                                    @else
                                        <input type="hidden" name="Clinic" value="{{ $clinicName[0]->Code }}">
                                        <input type="text" class="form-control" value="{{ $clinicName[0]->Description }}">
                                    @endif
                                </div>

                                <div class="form-group d-flex" style="margin-left:20px;" id="tabButtonsHl7">
                                    <button type="button" class="btn btn-success util-btn mr-2" data-target="run">Run</button>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="bold text-center">Result</label>
                                    <div class="mt-3 result-container1">
                                        <div id="resultsHl7">
                                            <div id="cardNumbersHl7"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button id="gobtnHl7" type="button" class="btn btn-success" style="margin-top: 15px;"><i class="fa fa-check-circle"></i> Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
        </div>
    </div>

    <div class="navbar-fixed-bottom">
        <div class="col-menu">
            <div class="btn-group col-xs-12" style="background-color:#8F8F8F;">
                <div class="col-xs-12 col-sm-10 col-sm-offset-2 col-md-8 col-md-offset-4 col-lg-6 col-lg-offset-6">
                    <button class="summarybtn btn btn-warning col-xs-4" style="border-radius:0px; line-height:29px; visibility:hidden;" type="button">Summary</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener("DOMContentLoaded", function () {
//  // ORIGINAL TAB SWITCHING
//     const tabButtons = document.querySelectorAll('#tabButtons button');
//     const tabContents = document.querySelectorAll('.tab-content .tab-pane');

//     tabButtons.forEach(button => {
//         button.addEventListener('click', function() {
//             const targetId = this.getAttribute('data-target');
//             const targetContent = document.getElementById(targetId);

//             tabButtons.forEach(btn => btn.classList.remove('active'));
//             tabContents.forEach(content => content.classList.remove('active'));

//             this.classList.add('active');
//             targetContent.classList.add('active');
//         });
//     });

    const tabButtons = document.querySelectorAll('#tabButtons button');
    const tabContents = document.querySelectorAll('.tab-content .tab-pane');
    const fadeDuration = 200;

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);

            const currentActiveBtn = document.querySelector('#tabButtons button.active');
            const currentActiveTab = document.querySelector('.tab-content .tab-pane.active');

            if (targetContent === currentActiveTab) return; // no change

            currentActiveBtn.classList.remove('active');
            this.classList.add('active');

            currentActiveTab.style.opacity = 0;

            setTimeout(() => {
                currentActiveTab.classList.remove('active');
                currentActiveTab.style.opacity = '';

                targetContent.classList.add('active');
                targetContent.style.opacity = 0;

                requestAnimationFrame(() => {
                    targetContent.style.opacity = 1;
                });
            }, fadeDuration);
        });
    });

    // Ping logic
    const gobtnPing = document.getElementById("gobtnPing");
    const buttonsPing = document.querySelectorAll("#tabButtonsPing button");
    const resultsPing = document.getElementById("cardNumbersPing");
    const clinicSelect = document.getElementById("Clinic");

    buttonsPing.forEach(btn => {
        btn.addEventListener("click", function () {
            buttonsPing.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    gobtnPing.addEventListener("click", function () {
        const activeBtn = document.querySelector("#tabButtonsPing .active");
        const target = activeBtn.getAttribute("data-target");
        let ip = "";
        let mode = "ping";

        if (target === "central") {
            ip = "10.10.250.22";
        }
        else if (target === "dns") {
            ip = "192.168.2.4";
        }
        else if (target === "gateway") {
            ip = clinicSelect ? clinicSelect.value : "";
        }
        else if (target === "traceroute") {
            ip = window.location.hostname;
            mode = "traceroute";
        }

        resultsPing.innerHTML = `<span class="text-muted">Pinging ${ip}...</span>`;

        fetch(`{{ route('services.ping') }}?ip=${ip}&mode=${mode}`)
            .then(response => response.text())
            .then(data => {
                resultsPing.innerHTML = `<pre>${data}</pre>`;
            })
            .catch(error => {
                resultsPing.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
            });
    });

    // Jasper logic
    const gobtnJasper = document.getElementById("gobtnJasper");
    const buttonsJasper = document.querySelectorAll("#tabButtonsJasper button");
    const resultsJasper = document.getElementById("cardNumbersJasper");

    buttonsJasper.forEach(btn => {
        btn.addEventListener("click", function () {
            buttonsJasper.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    gobtnJasper.addEventListener("click", function () {
        const activeBtn = document.querySelector("#tabButtonsJasper .active");
        const action = activeBtn.getAttribute("data-target");

        resultsJasper.innerHTML = `<span class="text-muted">Running "${action}"...</span>`;

        fetch(`{{ route('services.jasper') }}?action=${action}`)
            .then(response => response.text())
            .then(data => {
                resultsJasper.innerHTML = `<pre>${data}</pre>`;
            })
            .catch(error => {
                resultsJasper.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
            });
    });

    const gobtnSocket = document.getElementById("gobtnSocket");
    const buttonsSocket = document.querySelectorAll("#tabButtonsSocket button");
    const resultsSocket = document.getElementById("cardNumbersSocket");

    buttonsSocket.forEach(btn => {
        btn.addEventListener("click", function () {
            buttonsSocket.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    gobtnSocket.addEventListener("click", function () {
        const activeBtn = document.querySelector("#tabButtonsSocket .active");
        const target = activeBtn.getAttribute("data-target");

        if (target === "run") {
            resultsSocket.innerHTML = `<span class="text-muted">Running Socket...</span>`;

            fetch(`{{ route('services.socket') }}`)
                .then(response => response.text())
                .then(data => {
                    resultsSocket.innerHTML = `<pre>${data}</pre>`;
                })
                .catch(error => {
                    resultsSocket.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
                });
        }
    });

    gobtnIpChicken.addEventListener("click", function () {
    fetch("https://api.ipify.org?format=json")
        .then(response => response.json())
        .then(data => {
            document.getElementById("cardNumbersIpChicken").innerHTML = `
                <div class="ip-box text-center">
                    <div class="ip-title">Your IP Address:</div>
                    <div class="ip-value">${data.ip}</div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById("cardNumbersIpChicken").innerHTML =
                `<div class="alert alert-danger text-center">Failed to fetch IP: ${error}</div>`;
        });
    });

    const gobtnSql = document.getElementById("gobtnSql");
    const skipbtnSql = document.getElementById("skipbtnSql");
    const resetbtnSql = document.getElementById("resetbtnSql");
    const buttonsSql = document.querySelectorAll("#tabButtonsSql button");
    const resultsSql = document.getElementById("cardNumbersSql");

    buttonsSql.forEach(btn => {
        btn.addEventListener("click", function () {
            buttonsSql.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    let lastSqlErrno = null;

    gobtnSql.addEventListener("click", function () {
        const activeBtn = document.querySelector("#tabButtonsSql .active");
        const target = activeBtn.getAttribute("data-target");

        skipbtnSql.style.display = "none";
        resetbtnSql.style.display = "none";

        resultsSql.innerHTML = `<span class="text-muted">Running SQL Status...</span>`;

        fetch(`{{ route('services.sql') }}?action=${target}`)
            .then(response => response.text())
            .then(data => {
                resultsSql.innerHTML = `<pre>${data}</pre>`;
                // Extract Last_SQL_Errno using regex
                const match = data.match(/Last_SQL_Errno:\s*(\d+)/);
                 lastSqlErrno = match ? match[1] : null;
                const match2 = data.match(/Last_IO_Errno:\s*(\d+)/);
                 lastIOErrno = match2 ? match2[1] : null;

                console.log(lastSqlErrno);

                // Show Skip button only if Last_SQL_Errno = 1782
                if (lastSqlErrno === "1782") {
                    skipbtnSql.style.display = "inline-block";
                } else {
                    skipbtnSql.style.display = "none";
                }

                if (lastIOErrno === "2003" && "{{ Auth::user()->username }}" === "andreijames.pantia" || "{{ Auth::user()->username }}" === "jhoncarlos.drilon") {
                    resetbtnSql.style.display = "inline-block";
                } else {
                    resetbtnSql.style.display = "none";
                }


            })
            .catch(error => {
                resultsSql.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
            });
    });

    skipbtnSql.addEventListener("click", function () {
        const action = 'Skip';

        // First confirmation
        Swal.fire({
            title: "Are you sure?",
            text: "This will skip the error handling for this SQL issue.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, skip it",
            cancelButtonText: "Cancel",
            allowOutsideClick: false,
            buttonsStyling: true,
            customClass: {
                confirmButton: 'swal-btn-custom2',
                cancelButton: 'swal-btn-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {

                let countdown = 5;
                let timerInterval;

                // Countdown modal
                Swal.fire({
                    title: `Skipping in ${countdown} seconds`,
                    html: 'You can cancel before it executes.',
                    showCancelButton: true,
                    confirmButtonText: 'Skip Now',
                    cancelButtonText: 'Cancel',
                    allowOutsideClick: false,
                    buttonsStyling: true,
                    customClass: {
                        confirmButton: 'swal-btn-custom2',
                        cancelButton: 'swal-btn-cancel'
                    },
                    didOpen: () => {
                        const content = Swal.getHtmlContainer();
                        timerInterval = setInterval(() => {
                            countdown--;
                            if (content) {
                                content.innerHTML = `Skipping in <b>${countdown}</b> seconds.<br>You can cancel before it executes.`;
                            }

                            if (countdown <= 0) {
                                clearInterval(timerInterval);
                                Swal.close();
                            }
                        }, 1000);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then((finalResult) => {
                    if (finalResult.isConfirmed || countdown <= 0) {
                        resultsSql.innerHTML = `<span class="text-muted">Skipping Error...</span>`;

                        fetch(`{{ route('services.sql') }}?action=${action}&lastSqlErrno=${lastSqlErrno}`)
                            .then(response => response.text())
                            .then(data => {
                                resultsSql.innerHTML = `<pre>${data}</pre>`;
                            })
                            .catch(error => {
                                resultsSql.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
                            });
                    }
                });
            }
        });
    });

    resetbtnSql.addEventListener("click", function () {
        const action = 'Reset';

        const resultsText = resultsSql.innerText;
        let sourceLogFile = '';
        let readSourceLogPos = '';

        const logFileMatch = resultsText.match(/Source_Log_File:\s*(\S+)/);
        const logPosMatch = resultsText.match(/Read_Source_Log_Pos:\s*(\d+)/);

        if (logFileMatch) sourceLogFile = logFileMatch[1];
        if (logPosMatch) readSourceLogPos = logPosMatch[1];

        // First confirmation
        Swal.fire({
            title: "Are you sure?",
            text: "This will reset the SQL operation. Any changes may be lost.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, reset it",
            cancelButtonText: "Cancel",
            allowOutsideClick: false,
            buttonsStyling: true,
            customClass: {
                confirmButton: 'swal-btn-custom2',
                cancelButton: 'swal-btn-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {

                let countdown = 5;
                let timerInterval;

                // Countdown modal
                Swal.fire({
                    title: `Resetting in ${countdown} seconds`,
                    html: 'You can cancel before it executes.',
                    showCancelButton: true,
                    confirmButtonText: 'Reset Now',
                    cancelButtonText: 'Cancel',
                    allowOutsideClick: false,
                    buttonsStyling: true,
                    customClass: {
                        confirmButton: 'swal-btn-custom2',
                        cancelButton: 'swal-btn-cancel'
                    },
                    didOpen: () => {
                        const content = Swal.getHtmlContainer();
                        timerInterval = setInterval(() => {
                            countdown--;
                            if (content) {
                                content.innerHTML = `Resetting in <b>${countdown}</b> seconds.<br>You can cancel before it executes.`;
                            }

                            if (countdown <= 0) {
                                clearInterval(timerInterval);
                                Swal.close();
                            }
                        }, 1000);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                }).then((finalResult) => {
                    if (finalResult.isConfirmed || countdown <= 0) {
                        resultsSql.innerHTML = `<span class="text-muted">Resetting SQL...</span>`;

                        fetch(`{{ route('services.sql') }}?action=${action}&sourceLogFile=${encodeURIComponent(sourceLogFile)}&readSourceLogPos=${encodeURIComponent(readSourceLogPos)}&lastSqlErrno=${lastIOErrno}`)
                            .then(response => response.text())
                            .then(data => {
                                resultsSql.innerHTML = `<pre>${data}</pre>`;
                            })
                            .catch(error => {
                                resultsSql.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
                            });
                    }
                });
            }
        });
    });

    const gobtnHl7 = document.getElementById("gobtnHl7");
    const buttonsHl7 = document.querySelectorAll("#tabButtonsHl7 button");
    const resultsHl7 = document.getElementById("cardNumbersHl7");

    buttonsHl7.forEach(btn => {
        btn.addEventListener("click", function () {
            buttonsHl7.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
        });
    });

    gobtnHl7.addEventListener("click", function () {
        const activeBtn = document.querySelector("#tabButtonsHl7 .active");
        const target = activeBtn.getAttribute("data-target");

        if (target === "run") {
            resultsHl7.innerHTML = `<span class="text-muted">Running HL7 Mounting...</span>`;

            fetch(`{{ route('services.hl7') }}`)
                .then(response => response.text())
                .then(data => {
                    resultsHl7.innerHTML = `<pre>${data}</pre>`;
                })
                .catch(error => {
                    resultsHl7.innerHTML = `<span class="text-danger">Error: ${error}</span>`;
                });
        }
    });
});
</script>
@endsection
