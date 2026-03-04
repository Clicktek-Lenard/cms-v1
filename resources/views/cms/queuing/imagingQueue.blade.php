<!--@extends('app')-->
@section('style')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.js"></script>

<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}

.rectangle {
  height: 37px; /* Set the height as per your preference */
  background-color: #007bff; /* Background color for the rectangle */
  color: white; /* Text color inside the rectangle */
  text-align: center; /* Center-align text horizontally */
  line-height: 37px; /* Vertically center-align text by matching it to the height of the rectangle */
  border-radius: 5px; /* Optional border radius for rounded corners */
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-weight: bold;
}

.rectangle.on-hold {
    background-color: gray; /* Gray background color when on hold */
}

.badge-notify{
   background:red;
   position:relative;
   top: -18px;
   left: -27px;
}

.bordered-icon {
    display: inline-block;
    padding: 5px; /* Adjust the padding as needed */
    border: 1px solid transparent; /* Adjust the border color and style as needed */
    border-radius: 5px; /* Adjust the border radius as needed */
    margin: 2px; /* Adjust the margin as needed */
}

.numofcall{
	background-color: #5bc0de;
}

.pause-button-circle {
    width: 25px;
    height: 25px;
    border: 2px solid black; /* Default border color */
    border-radius: 50%;
    background-color: transparent; /* Default transparent background */
    color: black; /* Default icon color */
    padding-top: 1px;
    cursor: pointer;
}

.pause-button-circle:hover {
    border-color: red; /* Red border color on hover */
}

.pause-button-circle:hover i {
    color: red; /* Red icon color on hover */
}

.pause-button-circle i {
    font-size: 12px;
    line-height: -5px;
}

/* New class for play icon state */
.pause-button-circle.play-icon:hover {
    border-color: green; /* Green border color on hover when play icon */
}

.pause-button-circle.play-icon:hover i {
    color: green; /* Green icon color on hover when play icon */
}

.container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 35px; /* Set the width of the container */
  height: 100%;
  position: relative; /* Ensure the container is a positioning context for its children */
}

.circle {
  position: absolute; /* Use absolute positioning for animation */
  width: 17px; /* Size of the tiktok element */
  height: 17px; /* Size of the tiktok element */
  background: #10069F;
  border-radius: 50%;
  animation: leftToRight2 0.8s ease-in-out infinite;
  mix-blend-mode: darken;
  transform: scale(1);
}

.circle.red {
  background: #E10600;
  animation: rightToLeft2 0.8s ease-in-out infinite;
}

@keyframes leftToRight2 {
  0% {
    left: 0;
  }
  25% {
    transform: scale(1.2);
  }
  50% {
    left: 17px; /* Move within the container width */
  }
  75% {
    transform: scale(0.8);
  }
  100% {
    left: 0;
  }
}

@keyframes rightToLeft2 {
  0% {
    right: 0;
  }
  25% {
    transform: scale(0.8);
  }
  50% {
    right: 17px; /* Move within the container width */
  }
  75% {
    transform: scale(1.2);
  }
  100% {
    right: 0;
  }
}

.badge-gold {
    background: linear-gradient(145deg, #f9d835, #f9d835);
    color: #fff;
    border: 1px solid #e5a427;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.8);
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    /* animation: goldSparkle 1.5s infinite alternate; */
}

.badge-procedures {
    display: inline-block;
    background: linear-gradient(145deg, #28a745, #218838);
    color: #fff; 
    border: 1px solid #1c7430;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); Text shadow
    margin-right: 5px;
}

</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/kiosk/imagingqueue') }}" class="waiting"> Imaging Queue <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
        <audio id="announcement-audio" src="{{ url('mp3/ANNOUNCEMENT.mp3') }}"></audio>
    	<div class="col-menu-15 table-queue">
		</div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection 
@section('script')
<script>
    var imagingDepartment = "";
    
    var myURL = window.location.hostname;
    const socket = io.connect(myURL+':3001');

$(document).ready(function (e) {
    $html = "<div class=\"table-responsive\"><table id=\"QueueListTable\"  data-queue-type=\"IMAGING\"class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
    $html += "<thead>";
    $html += "<tr>";
    $html += "<th></th>";
    $html += "<th>Name</th>";
    $html += "<th>Queue No.</th>";
    $html += "<th>Procedures</th>";
    $html += "<th style=\"text-align: center;\">Priority</th>"; 
    $html += "<th style=\"text-align: center;\">Action</th>";
    $html += "<th style=\"text-align: center;\">Current Room</th>";
    $html += "<th>System Date</th>";
    var datas = {!! $queue !!};
    if (typeof (datas.length) === 'undefined')
        data.push(datas);
    else
        data = datas;

    $html += "</tbody></table></div>";
    $('.table-queue').append($html);

    var table = $('#QueueListTable').DataTable({
        ajax: {
            url: '{{ route('getqueueimagingdata') }}',
            data: function(d) {
                    // Assuming 'subgroupList' is the list of subgroups you passed to the view
                    d.stations = @json($subgroupList);  // Send the subgroups to the server
                },
                dataSrc: '' // Adjust if your JSON response is wrapped in an object with a 'data' key
            },
        autoWidth: true,
        deferRender: true,
        createdRow: function (row, data, index) { $(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-idPatient', data.IdPatient).attr('data-toggle-idQueueCMS', data.IdQueueCMS); },
        columns: [
            { "data": null },
            { "data": "FullName", "render": function (data, type, row, meta) { return '<div class="wrap-row" style="line-height: 37px;">' + data + '</div>'; } },
            { 
                "data": "QRCode",
                "render": function(data, type, row, meta) {
                    // var IdPatient = row.IdPatient;
                    // console.log(IdPatient);
                    var currentOnHold = row.OnHold || "";
                    var branch = row.IdBU; // Get the DTU value from the "IdBU" column
                    var priorityValue = row.Priority === 1 ? 1 : (row.Priority === 0 ? 2 : (row.Priority === 2 ? 2 : (row.Priority === 3 ? 1 : (row.Priority === 4 ? 0 : 99))));
                    var priority = row.Priority === 1 ? 'P' : (row.Priority === 0 ? 'R' : (row.Priority === 2 ? 'R' : (row.Priority === 3 ? 'P' : (row.Priority === 4 ? 'V' : ''))));
                    var queueNo = branch + '-' + priority + '-' + data; // Combine branch and QRCode
                    
                    var className = 'rectangle text-center'; // Default class
                    if (row.Priority === 4) {
                        className += ' badge-gold'; // Add gold class if priority is 4
                    }

                    if (currentOnHold.includes('IMAGING')) {
                        return '<div class="rectangle text-center on-hold" data-priority="' + priorityValue + '">' + queueNo + '</div>';
                    } else {
                        return '<div class="' + className + '" data-priority="' + priorityValue + '">' + queueNo + '</div>';
                    }
                },
                "type": "numeric", // SORTING
            },    
            {
                "data": "Station",
                "width": "10%",
                "render": function(data, type, row, meta) {
                    var stationData = data; // data will hold the "Station" value here

                    // If the data is not 0, proceed with the subgroups logic
                    var subgroups = stationData.split(',').map(function(item) {
                        return item.trim();
                    });

                    var validSubgroups = subgroups.filter(function(subgroup) {
                        return @json($subgroupList).includes(subgroup); // Ensure $subgroupList is available for filtering
                    });

                    // For each valid subgroup, generate the necessary HTML structure
                    var subgroupList = validSubgroups.map(function(subgroup) {
                    var badgeHtml = '<div class="badge badge-procedures flex text-center">' + subgroup + '</div>';
                    return '<div class="wrap-row text-center" style="margin-top: 5px;" value="' + subgroup + '">' +
                                '<div class="text-center">' +
                                    badgeHtml +
                                    '<span style="display: none;">' + subgroup + '</span>' +
                                '</div>' +
                            '</div>';
                    }).join(''); // Join the items without space to keep them vertically aligned

                    return subgroupList;
                }
            },
            { 
                "data": "Priority",  
                "render": function(data, type, row, meta) { 
                    var badgeHtml = '';
                    var filter ='';

                    if (data === 0) {
                        badgeHtml = '<div class="badge badge-pill badge-warning" style="background-color: red;" title="Regular">' +
                                        '<span class="tooltip-text">R</span>' +
                                    '</div>';
                        filter = '0';
                    } else if (data === 1) {
                        badgeHtml = '<div class="badge badge-pill badge-warning" style="background-color: orange;" title="Priority">' +
                                        '<span class="tooltip-text">P</span>' +
                                    '</div>';
                        filter = '1';
                    } else if (data === 2) {
                        badgeHtml = '<div class="badge badge-pill badge-warning" style="background-color: red;" title="Regular">' +
                                        '<span class="tooltip-text">R</span>' +
                                    '</div>' + 
                                    '&nbsp<div class="badge badge-pill badge-warning" style="background-color: green;" title="Assistance">' +
                                        '<span class="tooltip-text">A</span>' +
                                    '</div>';
                        filter = '0';
                    } else if (data === 3) {
                        badgeHtml = '<div class="badge badge-pill badge-warning" style="background-color: orange;" title="Priority">' +
                                        '<span class="tooltip-text">P</span>' +
                                    '</div>' +
                                    '&nbsp<div class="badge badge-pill badge-warning" style="background-color: green;" title="Assistance">' +
                                        '<span class="tooltip-text">A</span>' +
                                    '</div>';
                        filter = '1';
                      }  else if (data === 4) {
                        badgeHtml = '<div class="badge badge-gold" title="VIP">' +
                                        '<span class="tooltip-text">VIP</span>' +
                                    '</div>';
                        }


                    return '<div class="wrap-row text-center" style="margin-top: 5px;" value="'+filter+'">' +
                        '<div class="text-center">' + 
                            badgeHtml + 
                            '<span style="display: none;">' + filter + '</span>' +
                        '</div>' +
                    '</div>'; 
                }, 
                "type": "numeric", // SORTING
            },
            {
                "data": "lastClick",
                "width": "20%",
                "render": function(data, type, row, meta) {
                    var IdPatient = row.IdPatient;
                    var numOfCall = row.numOfCall;
                    var status = row.Status;
                    var currentUsername = "{{ Auth::user()->username }}";
                    var idQueueCMS = row.IdQueueCMS;
                    var isOnHold = row.OnHold && row.OnHold.includes("IMAGING"); // Check if OnHold contains "LABORATORY"

                    var selectedFilter = $('#imagingFilter').val(); 

                    // Check if lastClick has a value
                    if (data !== null && data !== undefined && data !== "") {
                        var buttonsHtml = '';
                        var id = row.Id;
                        buttonsHtml += '<button class="btn btn-success call-button fixed-width-button" value="' + data + '"';
                        if (selectedFilter === "" || status === "in_progress" && currentUsername !== data || isOnHold) {
                            buttonsHtml += ' disabled';
                        }
                        buttonsHtml += '><i class="fa fa-phone"></i> Call</button>';

                        if (status === "in_progress") {
                            if (currentUsername === data) {
                                buttonsHtml += '<button class="btn btn-danger in-button" style="background-color: #03a1fc; margin-left: 3px;" data-room="Reception"';
                                if (selectedFilter === "" || isOnHold) {
                                    buttonsHtml += ' disabled';
                                }
                                buttonsHtml += '>In</button>';
                            } else {
                                buttonsHtml += '<button class="btn btn-danger in-button" style="background-color: #03a1fc; margin-left: 3px;" data-room="Reception" disabled>In</button>';
                            }
                        } else {
                            buttonsHtml += '<button class="btn btn-danger in-button" style="margin-left: 3px;" data-room="Reception"';
                            if (selectedFilter === "" || isOnHold) {
                                buttonsHtml += ' disabled';
                            }
                            buttonsHtml += '>In</button>';
                        }

                        buttonsHtml += '<div class="numofcall wrap-row text-center bordered-icon" style="margin-left: 5px;" readonly>';
                        buttonsHtml += '<i style="color:white; font-size: 15px;" class="fa fa-bullhorn"></i> <span class="numOfCallValue" style="color:white;">' + numOfCall + '</span>';
                        buttonsHtml += '</div>';

                        // Add PLAY/PAUSE button icon div
                        if (isOnHold) {
                            buttonsHtml += '<div class="pause-button-circle wrap-row text-center bordered-icon" style="margin-left: 5px;">';
                            buttonsHtml += '<i class="fa fa-play"></i>';
                            buttonsHtml += '</div>';
                        } else {
                            buttonsHtml += '<div class="pause-button-circle wrap-row text-center bordered-icon" style="margin-left: 5px;">';
                            buttonsHtml += '<i class="fa fa-pause"></i>';
                            buttonsHtml += '</div>';
                        }

                        buttonsHtml += '<div class="container wrap-row text-center hidden">';
                        buttonsHtml += '<div class="circle"></div>';
                        buttonsHtml += '<div class="circle red"></div>';
                        buttonsHtml += '</div>';

                        // Display different statuses
                        if (status === 'next_room') {
                            return '<div class="wrap-row text-center" style="position: relative;">' + buttonsHtml + '</div>';
                        } else if (isOnHold) {
                            return '<div class="wrap-row text-center" style="position: relative;">' + buttonsHtml +
                                '<span class="badge badge-notify" style="position: absolute; top: 0; left: 38%; transform: translate(-50%, -50%); display: none;">' + data + '</span>' +
                                '</div>';
                        } else if (row.CurrentRoom === 'Resume Queue' && status === 'resume_queue') {
                            return '<div class="wrap-row text-center" style="position: relative;">' + buttonsHtml +
                                '<span class="badge badge-notify" style="position: absolute; top: 0; left: 38%; transform: translate(-50%, -50%); display: none;">' + data + '</span>' +
                                '</div>';
                        } else {
                            return '<div class="wrap-row text-center" style="position: relative;">' + buttonsHtml +
                                '<span class="badge badge-notify" style="position: absolute; top: 0; left: 38%; transform: translate(-50%, -50%); display: block;">' + data + '</span>' +
                                '</div>';
                        }
                    } else {
                        // If lastClick doesn't have a value, return buttons with an empty notification badge
                        var buttonsHtml = '';
                        var id = row.Id;
                        buttonsHtml += '<button class="btn btn-success call-button fixed-width-button"';
                        if (selectedFilter === "" || isOnHold) {
                            buttonsHtml += ' disabled';
                        }
                        buttonsHtml += '><i class="fa fa-phone"></i> Call</button>';

                        if (status === "in_progress" || currentUsername === data) {
                            buttonsHtml += '<button class="btn btn-danger in-button" style="background-color: #03a1fc; margin-left: 3px;" data-room="Reception"';
                            if ((status === "in_progress" && currentUsername !== data) || isOnHold) {
                                buttonsHtml += ' disabled';
                            }
                            buttonsHtml += '>In</button>';
                        } else {
                            buttonsHtml += '<button class="btn btn-danger in-button" style="margin-left: 3px;" data-room="Reception"';
                            if (selectedFilter === "" || isOnHold) {
                                buttonsHtml += ' disabled';
                            }
                            buttonsHtml += '>In</button>';
                        }

                        buttonsHtml += '<div class="numofcall wrap-row text-center bordered-icon" style="margin-left: 5px;" readonly>';
                        buttonsHtml += '<i style="color:white; font-size: 15px;" class="fa fa-bullhorn"></i> <span class="numOfCallValue" style="color:white;">' + numOfCall + '</span>';
                        buttonsHtml += '</div>';

                        // Add PLAY/PAUSE button icon div
                        if (isOnHold) {
                            buttonsHtml += '<div class="pause-button-circle wrap-row text-center bordered-icon" style="margin-left: 5px;">';
                            buttonsHtml += '<i class="fa fa-play"></i>';
                            buttonsHtml += '</div>';
                        } else {
                            buttonsHtml += '<div class="pause-button-circle wrap-row text-center bordered-icon" style="margin-left: 5px;">';
                            buttonsHtml += '<i class="fa fa-pause"></i>';
                            buttonsHtml += '</div>';
                        }

                        buttonsHtml += '<div class="container wrap-row text-center hidden">';
                        buttonsHtml += '<div class="circle"></div>';
                        buttonsHtml += '<div class="circle red"></div>';
                        buttonsHtml += '</div>';

                        return '<div class="wrap-row text-center" style="position: relative;">' + buttonsHtml +
                            '<span class="badge badge-notify" style="position: absolute; top: 0; left: 38%; transform: translate(-50%, -50%); display: none;"></span>' +
                            '</div>';
                    }
                }
            },
            { "data": "CurrentRoom", "render": function (data, type, row, meta) { return '<div class="wrap-row currentroom text-center">' + data + '</div>'; } },
            { "data": "SysDateTime", "render": function (data, type, row, meta) { return '<div class="wrap-row text-center">' + data + '</div>'; } }

        ],
        responsive: { details: { type: 'column' } },
        columnDefs: [
            {className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
            { targets: 1, "width":"100px",className: 'data-row', autoWidth: false, orderable: false  },
            { targets: 2, "width":"100px", autoWidth: false, orderable: false },
            { targets: 3, "width":"10px", autoWidth: false, orderable: false  },
            { targets: 4, "width":"25px", autoWidth: false, orderable: false  },

            { targets: 5, "width":"100px", autoWidth: false, orderable: false  },
            { targets: 6, "width":"25px", autoWidth: false, orderable: false  },
            { targets: 7, "width":"25px", autoWidth: false, orderable: false,  },
            {
                targets: 8, 
                visible: false, 
                orderable: true, 
                render: function(data, type, row, meta) {
                    var data = (row.OnHold && row.OnHold.includes('IMAGING')) ? '1' : '0'; 
                    return '<div class="wrap-row text-center hidden">' + data + '</div>';
                }
            }

        ],
        order: [[8, 'asc'],[2, 'asc']],
        dom: "frtiS",
        scrollY: $(document).height() - $('.navbar-fixed-top.crumb').height() - $('.navbar-fixed-bottom').height() - 160,
    });

    // FETCCH DATA EVERY 5 SECONDS
    setInterval(function() {
        table.ajax.reload(null, false); // false to keep the current pagination
    }, 10000);
    
    var $searchContainer = $('.dataTables_filter');
    $searchContainer.addClass('form-inline'); // Ensure inline display

    var subgroupList = @json($subgroupList);

    var imagingFilter = $('<select id="imagingFilter" class="form-control ml-2"></select>')
        .css('margin-right', '10px')
        .append('<option value="">ALL</option>') // Default ALL option
        .on('change', function() {
            imagingDepartment  = $(this).val();
            localStorage.setItem('imagingFilter', imagingDepartment ); 
            applyFilter(imagingDepartment ); 
            table.rows().invalidate().draw(); // If using DataTables, this will refresh the table
            // Now update the button states based on the selected filter
            $('#imagingFilter').val(imagingDepartment);
        });

    $.each(subgroupList, function(index, value) {
        imagingFilter.append($('<option></option>')
            .attr('value', value)
            .text(value));
    });

    imagingFilter.prependTo($searchContainer);

    var selectedFilter = localStorage.getItem('imagingFilter');
    if (selectedFilter) {
        imagingDepartment = selectedFilter;
        applyFilter(selectedFilter);
        $('#imagingFilter').val(selectedFilter);
    }

    // Function to apply filter
    function applyFilter(value) {
        var column = table.column(3);
        column.search(value, true, false).draw();
    }

    $('.dataTables_filter input').addClass('form-control mr-2').attr('placeholder', 'Search');
 
    var priorityFilter = $('<select id="priorityFilter" class="form-control ml-2"><option value="">ALL</option><option value="0">REGULAR</option><option value="1">PRIORITY</option></select>')
        .css('margin-right', '10px')
        .prependTo($searchContainer)
        .on('change', function() {
            var val = $(this).val();
            localStorage.setItem('priorityFilter', val); // Store selected value in localStorage
            applyPriorityFilter(val); // Apply the filter immediately
            table.rows().invalidate().draw();
        });

    var selectedPriorityFilter = localStorage.getItem('priorityFilter');
    if (selectedPriorityFilter !== null) {
        priorityFilter.val(selectedPriorityFilter); // Set the selected value
        applyPriorityFilter(selectedPriorityFilter); // Apply the filter
    }

    // Function to apply priority filter
    function applyPriorityFilter(value) {
        var columnData = table.column(4).data().toArray();
        table.column(4).search(value).draw();
    }


});

var authenticatedUsername = "{{ Auth::user()->username }}";
var counter = "{{ $counter->StationNumber }}";
// var department = "{{ $counter->Location }}";

$(document).ready(function () {
    // Emit the authenticated username to the server after connecting
    socket.on('connect', function () {
        socket.emit('username', authenticatedUsername);
    });
    
    // Function to speak the text
    function speakText(text) {
        // Play the announcement audio
        const audio = document.getElementById('announcement-audio');
        audio.play();

        // Add a delay before speaking the text with the prefix
        audio.onended = function() {
            const prefix = 'CALLING PATIENT: ';
            const suffix = 'please proceed to';
            const station = '{{ $counter -> Department}}';
            const counter = 'Counter Number {{ $counter -> StationNumber}}';
            const fullText = prefix + text + suffix + station + counter;
            const utterance = new SpeechSynthesisUtterance(fullText);
            window.speechSynthesis.speak(utterance);
        };
    }

    var button;
    // Add click event listener to the document
    $(document).on('click', '.call-button', function () {
        button = $(this);
        var queueID = button.closest('tr').data('toggle-queueid');
        var queueNo = button.closest('tr').find('.rectangle').text(); // Get the queue number
        // var extractedQueueNo = queueNo.substring(4);
        var IdPatient = button.closest('tr').data('toggle-idpatient');
        var $badge = $('.badge-notify', $(this).closest('.wrap-row'));
        var receptionId = authenticatedUsername;
        $badge.text(receptionId).show();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        button.prop('disabled', true);
        // Your click event logic goes here
        console.log( authenticatedUsername + ' called patient ' + queueID);
        setTimeout(function () {
            button.prop('disabled', false);
        }, 10000); 
        // speakText(queueNo); // Using queueNo as the text to speak
        $.ajax({
            type: 'POST',
            url: '{{ route('updatestatus') }}',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: { queueID: queueID, status: 'waiting', counter: counter, calledTo: imagingDepartment },
            success: function (response) {
                console.log('Status updated successfully');
                //FOR INCREMENTING NUMBER OF CALLS CLIENT SIDE
                socket.emit('numOfCallUpdated', { queueID: queueID });

                var $numOfCallElement = $('[data-toggle-queueid="' + queueID + '"] .numOfCallValue');
                var currentCount = parseInt($numOfCallElement.text());
                $numOfCallElement.text(currentCount + 1); // Increment the count locally
            },
            error: function (error) {
                console.error('Error updating status:', error);
            }
        });

        $.ajax({
            type: 'POST',
            url: '{{ route('actionlog') }}',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            data: { actionBy : authenticatedUsername, room : imagingDepartment, action : 'CALL', queueno : queueNo.substring(6), idpatient : IdPatient, kioskid : queueID},
            success: function (response) {

            },
            error: function (error) {
                console.error('Error updating status:', error);
            }
        });

        socket.emit('queueDisplay', {
            counter: counter,
            department: imagingDepartment,
            queueNo: queueNo.substring(4)
        });
        
        socket.emit('call', {
            queueID: queueID
        });
    });

    // FOR INCREMENTING REAL TIME ON CONNECTED CLIENTS
    socket.on('numOfCallUpdated', function (data) {
        var queueID = data.queueID;
        var $numOfCallElement = $('[data-toggle-queueid="' + queueID + '"] .numOfCallValue');
        var currentCount = parseInt($numOfCallElement.text());
        $numOfCallElement.text(currentCount + 1); // Update the count
    });


    // Handle the broadcast event on the client side
    socket.on('call', function (data) {
        button = $(this);
        var queueNo = button.closest('tr').find('.rectangle').text(); // Get the queue number

        console.log(data.username + ' called patient ' + data.queueID);
        var queueId = data.queueID;
        var $callButton = $('tr[data-toggle-queueid="' + queueId + '"] .call-button');
        $callButton.prop('disabled', true);
        // speakText(queueNo);
        var badge = $('tr[data-toggle-queueid="' + queueId + '"] .badge-notify');
        var receptionId = data.username;
        badge.text(receptionId).show();

        setTimeout(function () {
            $callButton.prop('disabled', false);
        }, 10000); 
    });

    // FOR CURRENT ROOM REAL TIME UPDATE ON CONNECTED CLIENTS
    socket.on('currentRoom', function (data) {
        var queueID = data.queueID;
        var department = data.department;
        var action = data.action;

        // Get the queue type of the current page
        var currentQueueType = $('#QueueListTable').data('queue-type');

        // If the queue type does not match the department, exit
        if (currentQueueType !== department) {
            return;
        }

        var $currentRow = $('[data-toggle-queueid="' + queueID + '"]');
        var $currentIcon = $currentRow.find('.fa');
        var $buttonCircle = $currentRow.find('.pause-button-circle');
        var $rectangle = $currentRow.find('.rectangle');
        var callButton = $currentRow.find('.call-button');
        var inButton = $currentRow.find('.in-button');
        var badge = $currentRow.find('.badge-notify');

        if (action === 'HOLD') {
            console.log('Pausing ' + currentQueueType);
            $currentIcon.removeClass('fa-pause').addClass('fa-play');
            $buttonCircle.addClass('play-icon');
            $rectangle.addClass('on-hold');
            callButton.prop('disabled', true);
            inButton.prop('disabled', true);
            badge.hide();
        } else if (action === 'RESUME'){
            console.log('Resuming ' + currentQueueType);
            $currentIcon.removeClass('fa-play').addClass('fa-pause');
            $buttonCircle.removeClass('play-icon');
            $rectangle.removeClass('on-hold');
            callButton.prop('disabled', false);
            inButton.prop('disabled', false);
        }
    });

    // Add click event listener to the document
    $(document).on('click', '.in-button', function () {
        var button = $(this);
        var queueID = button.closest('tr').data('toggle-queueid');
        var IdPatient = button.closest('tr').data('toggle-idpatient');
        var queueNo = button.closest('tr').find('.rectangle').text();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var idQueueCMS = button.closest('tr').data('toggle-idqueuecms');
        
        // Show the waiting dialog immediately
        waitingDialog.show();
        
        // Short timeout to ensure the dialog shows up
        setTimeout(function() {
            // Disable the button and change its background color
            button.prop('disabled', true);
            button.closest('tr').find('.call-button').prop('disabled', true);
            button.css('background-color', '#03a1fc');

            // Update UI
            var $currentRow = $('[data-toggle-queueid="' + queueID + '"]');
            $currentRow.find('.call-button, .in-button, .numofcall, .pause-button-circle, .badge-notify').addClass('hidden');
            $currentRow.find('.container').removeClass('hidden');

            var deferreds = [];

            // Emit socket event for 'in'
            socket.emit('in', { queueID: queueID });

            // Update status
            deferreds.push($.ajax({
                type: 'POST',
                url: '{{ route('updatestatus') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { queueID: queueID, status: 'in_progress' },
                success: function (response) {
                    console.log('Status updated successfully');
                },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            }));

            // Update current room
            deferreds.push($.ajax({
                type: 'POST',
                url: '{{ route('updatecurrentroom') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { queueID: queueID, roomName: imagingDepartment },
                success: function (response) {
                    console.log('Status updated successfully');
                    socket.emit('currentRoom', { queueID: queueID, department: imagingDepartment });
                    var $currentRoomElement = $('[data-toggle-queueid="' + queueID + '"] .currentroom');
                    $currentRoomElement.text(imagingDepartment);
                },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            }));

            // Log action
            deferreds.push($.ajax({
                type: 'POST',
                url: '{{ route('actionlog') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: {
                    actionBy: authenticatedUsername,
                    room: imagingDepartment,
                    action: 'IN',
                    queueno: queueNo.substring(6),
                    idpatient: IdPatient,
                    // erosidpatient: erosid,
                    kioskid: queueID
                },
                success: function (response) {
                    // No specific action on success
                },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            }));

            // Emit socket event for 'queueDisplayExit'
            socket.emit('queueDisplayExit', {
                counter: counter,
                department: imagingDepartment,
                queueNo: queueNo.substring(4)
            });

            // Wait for all AJAX requests to complete before redirecting
            $.when.apply($, deferreds).done(function () {
                window.location.href = 'imagingqueue/' + idQueueCMS +'/edit?department=' + encodeURIComponent(imagingDepartment);
            }).fail(function (error) {
                console.error('An error occurred:', error);
            });

        }, 100); // Adjust timeout as needed
    });

    // Handle the broadcast event on the client side
    socket.on('in', function (data) {
        console.log('Patient ' + data.queueID + ' entered room ');
        var queueId = data.queueID;
        var $inButton = $('tr[data-toggle-queueid="' + queueId + '"] .in-button');
        var $callButton = $('tr[data-toggle-queueid="' + queueId + '"] .call-button');
        
        $inButton.css('background-color', '#03a1fc');
        $inButton.prop('disabled', true);
        $callButton.prop('disabled', true);
    });

    $(document).on('click', '.pause-button-circle', function () {
        var icon = $(this).find('i');
        var button = $(this);
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var queueNo = button.closest('tr').find('.rectangle').text();
        var IdPatient = button.closest('tr').data('toggle-idpatient');
        var queueID = button.closest('tr').data('toggle-queueid');
        var buttonCircle = $('[data-toggle-queueid="' + queueID + '"] .pause-button-circle');
        var badge = $('[data-toggle-queueid="' + queueID + '"] .badge-notify');
        var rectangle = $('[data-toggle-queueid="' + queueID + '"] .rectangle');
        var callButton = $('[data-toggle-queueid="' + queueID + '"] .call-button');
        var inButton = $('[data-toggle-queueid="' + queueID + '"] .in-button');

        var isHolding = icon.hasClass('fa-pause');

        if (isHolding) {
            // Change to "ON HOLD"
            icon.removeClass('fa-pause').addClass('fa-play');
            buttonCircle.addClass('play-icon');
            rectangle.addClass('on-hold');
            badge.hide();
            callButton.prop('disabled', true);
            inButton.prop('disabled', true);
            console.log('CHECK 1 (HOLDING)');

            $.ajax({
                type: 'POST',
                url: '{{ route('holdreceivingrooms') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { queueID: queueID, roomName: 'IMAGING', action: 'hold' },
                success: function (response) {
                    console.log('Status updated successfully');
                    socket.emit('currentRoom', { queueID: queueID, department: 'IMAGING', onHold: 'IMAGING', action: 'HOLD' });
                },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route('actionlog') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { actionBy: authenticatedUsername, room: imagingDepartment, action: 'HOLD QUEUE', queueno: queueNo.substring(6), idpatient: IdPatient, kioskid: queueID },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            });

        } else {
            // Change to "RESUME" state only if LABORATORY is not in OnHold
            $.ajax({
                type: 'POST',
                url: '{{ route('holdreceivingrooms') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { queueID: queueID, roomName: 'IMAGING', action: 'resume' },
                success: function (response) {
                    console.log('Status updated successfully', response);

                    var onHold = response.onHold || ''; // Default to empty string if NULL or undefined
                    console.log('ONHOLDDDDD', onHold);
                    if (!onHold.includes('IMAGING')) {
                        icon.removeClass('fa-play').addClass('fa-pause');
                        buttonCircle.removeClass('play-icon');
                        rectangle.removeClass('on-hold');
                        callButton.prop('disabled', false);
                        inButton.prop('disabled', false);
                        console.log('CHECK 2 (RESUMED)');
                        
                        socket.emit('currentRoom', { queueID: queueID, department: 'IMAGING', onHold: onHold, action: 'RESUME' });
                    }
                },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route('actionlog') }}',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                data: { actionBy: authenticatedUsername, room: imagingDepartment, action: 'RESUME QUEUE', queueno: queueNo.substring(6), idpatient: IdPatient, kioskid: queueID },
                error: function (error) {
                    console.error('Error updating status:', error);
                }
            });
        }
    });


});



// $(document).ready(function (e) {
//     // Emit the authenticated username to the server after connecting
//     socket.on('connect', function () {
//         var authenticatedUsername = "{{ Auth::user()->username }}";
//         socket.emit('username', authenticatedUsername);
//     });

//     $('.call-button').click(function () {
//         var button = $(this);
//         var kioskId = button.closest('tr').data('toggle-queueid');
//         console.log("ID", kioskId);

//         socket.emit('buttonClicked', kioskId);

//         button.prop('disabled', true);

//         setTimeout(function () {
//             button.prop('disabled', false);
//         }, 10000); 
//     });

//     // Handle the broadcast event on the client side
//     socket.on('broadcastButtonClicked', function (kioskId) {
//         console.log(`Button clicked for kiosk ${kioskId} on another client`);

//     });


// });

</script>
@endsection