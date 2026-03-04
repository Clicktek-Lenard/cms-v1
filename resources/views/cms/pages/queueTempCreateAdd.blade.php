<style>

.tooltip-inner {
    color: #red; 
    background-color: red; 
    border: none; 
	box-shadow: none;
}
.tooltip-arrow {
	display: none; 
}
 .border{
	border: 0; 
    height: 1px;
    background-color: #d6d4d4; 
 }
.tt-menu {
  max-height: 250px;
  overflow-y: auto;
  background-color: #f2f5fa; 
  width: 400px;
}
.table-dark-text {
    color: #000; /* Darker text color */
}
.disabled-cell {
	pointer-events: none; /* Prevent interaction */
}

</style>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="modal-cms-header">
<form id="outsidePhysician" class="form-horizontal"  role="form" method="POST" autocomplete="off">
	<div class="box-divider">
	<input type="hidden" name="IdPatient" value="{{$PatientId ?? ''}}"/>
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
	<label class="bold nodoctors"  name="physician" style="cursor:pointer;">Referring Physician<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
	<select name="modalDoctors" class="form-control rowview" id="physician" placeholder="Doctor's Name">
        	<option value=""></option>
            @foreach ($doctors as $doctor)
            <option value="{{ $doctor->Id }}">{{ $doctor->FullName }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row form-group row-md-flex-center" id="addPhysician" >
			<div class="col-sm-3 col-md-3 pad-0-md text-right-md addPhysician ">
				<label class="bold nodoctors " style="cursor:pointer;">Physician Search<font style="color:red;"></font></label>
			</div>
			<div class="col-sm-3 col-md-3">
				<input type="text"  id="prcNo" class="typeahead form-control" name="prcNo" placeholder="PRC NO."  maxlength="7" pattern="\d*" data-toggle="tooltip" title="Please enter only digits">
			</div>
			<div class="col-sm-3 col-md-3">
				<input type="text" class="typeahead form-control" name="lname" id="lname" placeholder="LAST NAME" data-toggle="tooltip" title="Required Field" >
			</div>
			<div class="col-sm-3 col-md-3">
				<input type="text" class="typeahead form-control" name="fname" id="fname" placeholder="FIRST NAME" data-toggle="tooltip" title="Required Field" >
			</div>
			<div class="col-sm-3 col-md-3">
				<input type="text" class="typeahead form-control" name="mname" id="mname" placeholder="MIDDLE NAME" data-toggle="tooltip" title="Required Field" >
			</div>			
			<div class="col-sm-2 col-md-2">
				<button class="doctorbtn btn btn-primary" name="doctorbtn" type="button">Search</button>
			</div>
		</div> 
	</div>
	<hr class="border">
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold companyDefault " style="cursor:pointer;">Company Name<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
        <select name="modalCompanys" class="form-control rowview" placeholder="Company Name">
            <option value=""></option>
            @foreach ($companys as $company)
            <option value="{{ $company->Id }}">{{ $company->Name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold transactionDefault " style="cursor:pointer;">Transaction Type<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
        <select name="modalTransactionType" class="form-control rowview" placeholder="Transaction Type">
            <option value=""></option>
            @foreach ($transactionType as $tType)
            <option value="{{ $tType->Code }}">{{ $tType->Description }}</option>
            @endforeach
        </select>
    </div>
</div>
</form>
</div>
<div class="row">
    <div class="table-items col-md-12"></div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('#select_all').get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

/* Create an array with the values of all the checkboxes in a column */
$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}
$('#physician').change(function() {
    var selectedPhysician = $(this).val();
	
    if (selectedPhysician === '8498') {
        $('#addPhysician').removeClass('hidden').show(); //displayed
    } else {
        $('#addPhysician').addClass('hidden').hide(); // hidden
    }
});

var physicianNameTT = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: "{{ url('cms/queue/api/getPhysicianName') }}/%QUERY",
    wildcard: '%QUERY',
    rateLimitWait: 1000
  }
});



$('input[name="prcNo"]').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 1  
	},
	{
		name: 'prcNo',
		display: 'PRCNo',
		source: physicianNameTT.ttAdapter(),
		limit: 1000,
		remote: {
			//url: '/Search?q=%QUERY',
			xhrSending: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				} 
				//add loading class to tt-hint element when request sending.
				$ttHint.addClass("loading");
			},
			xhrDone: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				}
				//remove loading class from tt-hint element when response arrived.
				$ttHint.removeClass("loading"); 
				
			}
		},
		
		templates: {
			empty: [
			  '<div class="empty-message">',
				'PRC not found...',
			  '</div>'
			].join('\n'),
			suggestion: function (data) {
				// console.log("Data from API:", data);
			return 	'<div class="man-section">' +
						'<div class="description-section">'+data.PRCNo+'</div>' +
						'<div class="description-section">'+data.FullName+'</div>' +
						'<div class="description-section">'+data.Description+'</div>' +
						'<div style="clear:both;"></div>' +
					'</div>';

		}
	}
})
.on('typeahead:selected', function (e, data) {
  $('input[name="prcNo"]').val(data.PRCNo);
  $('input[name="lname"]').val(data.LastName);
  $('input[name="fname"]').val(data.FirstName);
  $('input[name="mname"]').val(data.MiddleName);
});

$('input[name="lname"]').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 1  
	},
	{
		name: 'lname',
		display: 'LastName',
		source: physicianNameTT.ttAdapter(),
		limit: 1000,
		remote: {
			//url: '/Search?q=%QUERY',
			xhrSending: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				} 
				//add loading class to tt-hint element when request sending.
				$ttHint.addClass("loading");
			},
			xhrDone: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				}
				//remove loading class from tt-hint element when response arrived.
				$ttHint.removeClass("loading"); 
				
			}
		},
		
		templates: {
			empty: [
			  '<div class="empty-message">',
				'Last Name not found...',
			  '</div>'
			].join('\n'),
			suggestion: function (data) {
				// console.log("Data from API:", data);
			return 	'<div class="man-section">' +
						'<div class="description-section">'+data.PRCNo+'</div>' +
						'<div class="description-section">'+data.FullName+'</div>' +
						'<div class="description-section">'+data.Description+'</div>' +
						'<div style="clear:both;"></div>' +
					'</div>';

		}
	}
})
.on('typeahead:selected', function (e, data) {
  $('input[name="prcNo"]').val(data.PRCNo);
  $('input[name="lname"]').val(data.LastName);
  $('input[name="fname"]').val(data.FirstName);
  $('input[name="mname"]').val(data.MiddleName);
});

$('input[name="fname"]').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 1  
	},
	{
		name: 'fname',
		display: 'FirstName',
		source: physicianNameTT.ttAdapter(),
		limit: 1000,
		remote: {
			//url: '/Search?q=%QUERY',
			xhrSending: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				} 
				//add loading class to tt-hint element when request sending.
				$ttHint.addClass("loading");
			},
			xhrDone: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				}
				//remove loading class from tt-hint element when response arrived.
				$ttHint.removeClass("loading"); 
				
			}
		},
		
		templates: {
			empty: [
			  '<div class="empty-message">',
				'First Name not found...',
			  '</div>'
			].join('\n'),
			suggestion: function (data) {
				// console.log("Data from API:", data);
			return 	'<div class="man-section">' +
						'<div class="description-section">'+data.PRCNo+'</div>' +
						'<div class="description-section">'+data.FullName+'</div>' +
						'<div class="description-section">'+data.Description+'</div>' +
						'<div style="clear:both;"></div>' +
					'</div>';

		}
	}
})
.on('typeahead:selected', function (e, data) {
  $('input[name="prcNo"]').val(data.PRCNo);
  $('input[name="lname"]').val(data.LastName);
  $('input[name="fname"]').val(data.FirstName);
  $('input[name="mname"]').val(data.MiddleName);
});

$('input[name="mname"]').typeahead(
	{
		hint: true,
		highlight: true,
		minLength: 1  
	},
	{
		name: 'mname',
		display: 'MidlleName',
		source: physicianNameTT.ttAdapter(),
		limit: 1000,
		remote: {
			//url: '/Search?q=%QUERY',
			xhrSending: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				} 
				//add loading class to tt-hint element when request sending.
				$ttHint.addClass("loading");
			},
			xhrDone: function () {
				if (!$ttHint.length) {
					$ttHint = $(".tt-hint");
				}
				//remove loading class from tt-hint element when response arrived.
				$ttHint.removeClass("loading"); 
				
			}
		},
		
		templates: {
			empty: [
			  '<div class="empty-message">',
				'Middle Name not found...',
			  '</div>'
			].join('\n'),
			suggestion: function (data) {
				// console.log("Data from API:", data);
			return 	'<div class="man-section">' +
						'<div class="description-section">'+data.PRCNo+'</div>' +
						'<div class="description-section">'+data.FullName+'</div>' +
						'<div class="description-section">'+data.Description+'</div>' +
						'<div style="clear:both;"></div>' +
					'</div>';

		}
	}
})
.on('typeahead:selected', function (e, data) {
  $('input[name="prcNo"]').val(data.PRCNo);
  $('input[name="lname"]').val(data.LastName);
  $('input[name="fname"]').val(data.FirstName);
  $('input[name="mname"]').val(data.MiddleName);
});


$(document).ajaxSend(function(event, request, settings) { 
    // console.log("AJAX request sent:", settings.url);
    $('.tt-input').addClass('loading');
});
$(document).ajaxComplete(function(event, request, settings) {
    // console.log("AJAX request completed. Response:", request.responseText);
    $('.tt-input').removeClass('loading');
});

$('.doctorbtn').on('click', function() {
	if (parent.required($('#outsidePhysician'))) return false;

	var prcNo = $('#prcNo').val();
	var lname = $('#lname').val();
	var fname = $('#fname').val();
	var mname = $('#mname').val();

	$.ajax({
		url: '{{ route('outsidePhysiciansearch') }}',
		method: 'GET',
		data: {
			prcNo: prcNo,
			lname: lname,
			fname: fname,
			mname: mname,
			_token: '{{ csrf_token() }}' 
		},
		success: function(response) {
			var idPatient = $('input[name="IdPatient"]').val();		
			outsidePhysician.setTitle("Referring Physician");
			outsidePhysician.setType(BootstrapDialog.TYPE_INFO);
			outsidePhysician.setMessage(
                '<div class="form-group">'+'<input type="hidden" id="IdPatienttwo" value="'+idPatient+'"' + '</div>' +
                '<div class="table-responsive"><table id="QueueListTable" class="table table-striped table-dark-text"></table></div>'
            );
			outsidePhysician.realize();
			outsidePhysician.open();

			$('#QueueListTable').DataTable({
				data: response.physicianDatas, 
				columns: [
					{
						title: "", 
						data: null,
						orderable: false,
						render: function (data, type, row) {							
							return '<input type="checkbox" class="row-select" value="' + row.Id + '">';
						}
					},
					{ title: "Last Name", data: "LastName" },
					{ title: "First Name", data: "FirstName" },
					{ title: "Middle Name", data: "MiddleName" },
					{ title: "Specialization", data: "Description" },
					{ title: "PRC No", data: "PRCNo" },
					{ title: "Status", data: "Status" }
					
				],
				responsive: true,
				scrollY: "550px",
				scrollCollapse: false,
				paging: false,
				dom: '<"custom-filter"f>t<"bottom"ip>', 
			});
			
			$('#QueueListTable_wrapper .custom-filter input').addClass('hide'); 
			$('#QueueListTable_wrapper .custom-filter label').addClass('hide');  

			// Handle row selection
			$('#QueueListTable tbody').on('click', 'td', function() {
				var row = $(this).closest('tr');
				var isChecked = row.find('input.row-select').is(':checked');

				$('#QueueListTable tbody tr').removeClass('selected').find('input.row-select').prop('checked', false);

				if (!isChecked) {
					row.addClass('selected');
					row.find('input.row-select').prop('checked', true);
					disableOtherCells(row);

					// Update selectedId with the value of the selected row's Id
					selectedId = row.find('input.row-select').val();
				} else {
					row.removeClass('selected');
					row.find('input.row-select').prop('checked', false);
					enableAllCells();

					// Clear selectedId since nothing is selected
					selectedId = null;
				}
			});

			// Handle checkbox selection directly
			$('#QueueListTable tbody').on('click', 'input.row-select', function(e) {
				e.stopPropagation();
				var row = $(this).closest('tr');
				var isChecked = $(this).is(':checked');

				$('#QueueListTable tbody tr').removeClass('selected').find('input.row-select').prop('checked', false);

				if (isChecked) {
					row.addClass('selected');
					disableOtherCells(row);

					// Update selectedId with the value of the selected row's Id
					selectedId = $(this).val();
				} else {
					row.removeClass('selected');
					enableAllCells();

					// Clear selectedId since nothing is selected
					selectedId = null;
				}

				$(this).prop('checked', isChecked);
			});
		},
		error: function(xhr, status, error) {
			console.error('Search failed: ', error);
		}
	});
});


function disableOtherCells(selectedRow) {
	$('#QueueListTable tbody tr').not(selectedRow).each(function() {
		$(this).find('td').addClass('disabled-cell');
		$(this).find('input.row-select').prop('disabled', true);
	});
}

function enableAllCells() {
	$('#QueueListTable tbody tr').each(function() {
		$(this).find('td').removeClass('disabled-cell');
		$(this).find('input.row-select').prop('disabled', false);
	});
}


var selectedId = null;

var outsidePhysician = new BootstrapDialog({
    message: function(dialog) {
        var $message = $('<div class="outsidePhysician"><div style="text-align: center; color:blue;"><b>Loading...</b></div></div>');
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
        },
		{
            cssClass: 'btn-info actionbtn',
            label: 'Add Physician',
            action: function (modalRef) {    
				modalRef.close();
				physicianEnrollment.setTitle("Physician - Enrollment");
				physicianEnrollment.setType(BootstrapDialog.TYPE_SUCCESS);
				physicianEnrollment.setData("pageToLoad", "{{ '/cms/queue/pages/physicianEnrollment/create' }}");
				physicianEnrollment.realize();
				physicianEnrollment.open();
            }
        },
        {
            id: 'btnsave',
            cssClass: 'btn-success actionbtn',
            label: 'Open',
            action: function (modalRef) {

                var selectedRow = $('#QueueListTable').DataTable().rows('.selected').data();

                if (selectedRow.length > 0) {
                    var selectedData = selectedRow[0]; // Get the first selected row data
                    var selectedId = selectedData.Id; // Assuming Id is the field name
                    var status = selectedData.Status; // Assuming Status is the field name

                    console.log(selectedId, status, 'test');

					if (status === "RP - Leads" || status === "RP - For Approval" || status === "Active") {
                        alert('Done');
                        modalRef.close();
                        var $sdoctor = $('select[name="modalDoctors"]')[0].selectize;
                        
                        // Only set the value if selectedId is existed
                        if (selectedId) {
                            $sdoctor.setValue(selectedId);
                        }
					}	
					else {
                        modalRef.close();
                        physicianInfo.setTitle("Physician - Info");
                        physicianInfo.setType(BootstrapDialog.TYPE_SUCCESS);
                        physicianInfo.setData('pageToLoad', '/cms/queue/physicianInfo?id=' + selectedId);
                        physicianInfo.realize();
                        physicianInfo.open();
                    }
                } else {
                    alert("Please select a physician.");
                }
            }
        }
    ]
});


var physicianInfo = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="physicianTableModalInfo"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_INFO,
	data: {
		// pageToLoad: '/cms/queue/physicianTableList'
	},
	animate: false,
	closable: false,
	buttons: [
		{
			cssClass: 'btn-default modal-closebtn',
			label: 'Close',
			action: function (modalRef) {
				//$('.physicianTableModal').close();
				modalRef.close();
			
			}
		},
		{
			id: 'btnsave',
			cssClass: 'btn-success actionbtn',
			label: 'Save',
			action: function (modalRef){

				var selectedId = $('input[name="_selected"]').val();
				var csrfToken = $('meta[name="csrf-token"]').attr('content');

				var lastname = $('input[name="lastname"]').val();
				var firstname = $('input[name="firstname"]').val();
				var middlename = $('input[name="middlename"]').val();
				var fullname = $('input[name="fullname"]').val();
				var prcno = $('input[name="prcno"]').val();
				var description = $('select[name="description"]').val();
				var status = $('input[name="status"]').val();
				var myimage = $('input[name="myimage"]').val();
			
				if (!lastname || !firstname || !prcno || !myimage | myimage === 'no-image.jpg') {
					alert('Please fill out all required fields (Last Name, First Name, PRC No, Prescription).');
					return;
				}

				$.ajax({
					method: 'POST',
					url: '/cms/queue/physician-update?idQueue=' + $('input[name="_queueid"]').val(), 
					headers: {
						'X-CSRF-TOKEN': csrfToken,
					},					
					data: {selectedId: selectedId, 
							lastname: lastname, 
							firstname: firstname, 
							middlename: middlename, 
							fullname: fullname, prcno: prcno, 
							description: description, 
							status: status, 
							myimage: myimage 
						},
					success: function(response) {
						alert('Physician updated successfully');
						modalRef.close();
						
						if(myimage) {
							$('.image-tag').val(myimage);
						}
						var $sdoctor = $('select[name="modalDoctors"]')[0].selectize;
                			$sdoctor.setValue(selectedId);
						// $sdoctor = $idoctors[0].selectize;
                        // $sdoctor.setValue(selectedId);
						// $('select[name="modalDoctors"]').val(selectedId).trigger('change');
					},
					error: function(xhr) {
						alert('Error occurred: ');
					}
				});
			}
		}
	]
});

var physicianEnrollment = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="physicianEnrollmentModal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_INFO,
	data: {
		// pageToLoad: '/cms/queue/physicianTableList'
	},
	animate: false,
	closable: false,
	buttons: [
		{
			cssClass: 'btn-default modal-closebtn',
			label: 'Close',
			action: function (modalRef) {
				//$('.physicianTableModal').close();
				modalRef.close();
			
			}
		},
		{
			id: 'btnsave',
			cssClass: 'btn-success actionbtn',
			label: 'Save',
			action: function (modalRef){
				var csrfToken = $('meta[name="csrf-token"]').attr('content');

				var lastname = $('input[name="lastname"]').val();
				var firstname = $('input[name="firstname"]').val();
				var middlename = $('input[name="middlename"]').val();
				var fullname = $('input[name="fullname"]').val();
				var prcno = $('input[name="prcno"]').val();
				var description = $('select[name="description"]').val();
				var myimage = $('input[name="myimage"]').val();

				if (!lastname || !firstname || !prcno || !myimage | myimage === 'no-image.jpg') {
					alert('Please fill out all required fields (Last Name, First Name, PRC No, Prescription).');
					return;
				}

				$.ajax({
					method: 'POST',
					url: '{{ route('physicianinsert') }}',  
					headers: {
						'X-CSRF-TOKEN': csrfToken,
					},				
						
					data: { 
							lastname: lastname, 
							firstname: firstname, 
							middlename: middlename, 
							fullname: fullname, 
							prcno: prcno, 
							description: description,
							myimage: myimage
						},
						
						success: function(response) {
							modalRef.close();

							if (response.exists) {
								alert('Physician already exists!');
							} else {
								alert('New physician inserted successfully. ID: ' + response.id+ '-' + response.fullname);
							}

							var selectedId = response.id;
							var fullname = response.fullname; 
							if (myimage) {
								$('.image-tag').val(myimage);
							}

							var $sdoctor = $('select[name="modalDoctors"]')[0].selectize;
							$sdoctor.addOption({ value: selectedId, text: fullname }); 
							$sdoctor.setValue(selectedId); 
							$('select[name="modalDoctors"]').val(selectedId).trigger('change');
						},

					error: function(xhr) {
						alert('Error occurred: ');
					}
				});
			}
		}
	]
});


$(document).ready(function(e) {
	var itemSelected = [];
	var rows_selected = [];
	var mless = 300;
	if ($(window).width() < 767) mless = 130;
	
	
    $html = "<div class=\"table-responsive\"><table id=\"ItemListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
		$html += "<thead>";
		$html += "<tr>";
			$html += "<th></th>";
			$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"hidden\" disabled=\"disabled\"></th>";
			$html += "<th>Item Code</th>";
			$html += "<th>Description</th>";
			$html += "<th>Qty</th>";
			$html += "<th>Price</th>";
		$html += "</tr>";
	$html +="</thead><tbody>";
	var data = [];
	
			
	$html +="</tbody></table></div>";
	$('.table-items').append($html);
	
	var table = $('#ItemListTable').DataTable({
		data			: data,
		autoWidth		: false,
		deferRender	: false,
		createdRow		: function ( row, data, index ) {
			$(row).attr('data-toggle-subgroup', data.Group).attr('data-toggle-itemused', data.ItemUsed).attr('data-toggle-group', data.SubGroup).attr('data-toggle-IMAllowQty', data.IMAllowQty);
			 $(row).attr('id', data.IdItem);
			 if($.inArray(data.IdItem, rows_selected) !== -1){ 
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
			 
		},
		columns			: [
		{ "data": null },
		{ "data": "IdItem", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "Code", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "Description", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'&nbsp;<div class="text-right" style="color:red;">'+row['PDefault']+'</div></div>'; } },
		{ "data": null, "render": function(data, type, row, meta) { if (typeof data === 'object') {  var content = (data.propertyName || ''); } else {  var content = (data || ''); } if ((data.Group === 'CARD' && data.IMSubGroup !== "FREE")  || (data.IMAllowQty == "1" )) { content += '<input type="number" name="Qty" min="1" max="25" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" data-group="' + (data.Group || '') + '" placeholder="Qty" style="margin-left: 10px"class="form-control" value="1" required />'; }  return '<div class="wrap-row">' + content +'</div>'; }},
		{ "data": "Price", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "Code", targets: 2, "width":"30px" },
			{"data": "Description", targets: 3, "width":"300px" },
			{"data": "Price", targets: 4, "width":"80px" },
			{"data": null, targets: 5, "width":"10px" },
			{"data": "PriceGroup", targets: 6, "width":"1px"}

		],
		'rowCallback': function(row, data, dataIndex){
			var itemId = $(row).attr('id');
			 if($.inArray(itemId, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		  },
		order			: [[ 1, 'desc' ],[ 2, 'asc' ]],
		dom:            "frtiS",
		scrollY: $(window).height()-$('.modal-cms-header').height()-mless,
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	// Handle click on checkbox
	//for ToolTip prompt
	$('[data-toggle="tooltip"]').tooltip();
	//only digits can input 
	$('#prcNo').on('input', function() {
		this.value = this.value.replace(/[^0-9]/g, '');
	});	
	   $('#ItemListTable tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

      // Get row data
      var data = table.row($row).data();
	
      // Get row ID
      var rowId = data[0]; 

      // Determine whether row ID is in the list of selected row IDs 
      var index = $.inArray(rowId, rows_selected);

      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });
   
   
   // Handle click on table cells with checkboxes
   $('#ItemListTable').on('click', 'tbody td', function(e){
	 var rowData = table.row($(this).closest('tr')).data();
    
    // Check if rowData is not null and has the "Group" property
    if (rowData && rowData.Group) {
        console.log('data.Group: ', rowData.Group);
    }
    if ($(this).hasClass('control') || $(this).find('input').hasClass('item-notes') || $(this).find('input[name="Qty"]').length > 0 ) {
        return false;
    }

      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('#select_all').on('click', function(e){
      if(this.checked){
         $('#ItemListTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#ItemListTable tbody input[type="checkbox"]:checked').trigger('click');
      }
      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });
	
	
	var doctors = [];
	var datas = {!! $doctors !!};
	if( typeof(datas.length) === 'undefined')
		doctors.push(datas);
	else
		doctors = datas;
	$idoctors = $('select[name="modalDoctors"]').selectize({
		sortField: 'FullName',
		searchField: ['FullName','Category'],
		options : doctors,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.FullName) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Category) + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) {
			if (!value.length)
			{
				var itemTable = $('#ItemListTable').dataTable();
				itemTable.fnClearTable();
				return;
			}
			else
			{
				parent.getData(
					"{{ '/cms/queue/api/itemPrice' }}/0",
					{
						'IdCompany':$('select[name=modalCompanys]').val(),
						'IdDoctor':value,
						'IdPatient':$('input[name=IdPatient]').val(),
						'_token': $('input[name=_token]').val()
					},
					function(results){
						var itemTable = $('#ItemListTable').dataTable();
						rows_selected = [];
						$.each(results.selectedItemPrice,function(ikey,ival)
						{
							rows_selected.push(ival.IdItem);
						});
						itemTable.fnClearTable();
						
						if(!results.listItemPrice.length) return;
						itemTable.fnAddData(results.listItemPrice);
						itemTable.rows().invalidate().draw();
						
						// itemTable.rows.add( results.listItemPrice ).draw(false);
						
					}
				);
			}
		}
	});
	
	$('.nodoctors').on('click', function(e){
		//1699 No Physician = changed to 8498 Outside Physician as per request 3/15/23
		$sdoctor = $idoctors[0].selectize;
		$sdoctor.setValue("8498");
	});
	$('.nodoctors').click();
	
	var xhr;
	var company_s, $company_s;
	var package, $package;
	
	var companys = [];
	var datas = {!! $companys !!}; 
	
	if( typeof(datas.length) === 'undefined')
		companys.push(datas);
	else
		companys = datas;
	$company_s = $('select[name="modalCompanys"]').selectize({
		sortField: 'Name',
		searchField: ['ErosCode','Name'],
		options : companys,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Name) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.ErosCode) + '</span>' +
				'</div>';
			}
		},
		onChange: function(value) { 
			if (!value.length) 
			{
				var itemTable = $('#ItemListTable').dataTable();
				itemTable.fnClearTable();
				return;
			}
			else
			{
				parent.getData(
					"{{ '/cms/queue/api/itemPrice' }}/0",
					{
						'IdCompany':value,
						'IdDoctor':$('select[name=modalDoctors]').val(),
						'IdPatient':$('input[name=IdPatient]').val(),
						'_token': $('input[name=_token]').val()
					},
					function(results){
						var itemTable = $('#ItemListTable').dataTable();
						rows_selected = [];
						$.each(results.selectedItemPrice,function(ikey,ival)
						{	
							rows_selected.push(ival.IdItem);
						});
						itemTable.fnClearTable();
						
						if(!results.listItemPrice.length) return;
						itemTable.fnAddData(results.listItemPrice);
						// test if working
						var num_rows = itemTable.api().page.info().recordsTotal;
						itemTable.api().page( 'last' ).draw( true );
						itemTable.api().row( num_rows-1 ).scrollTo();
						itemTable.rows().invalidate().draw();
					}
				);
			}
		}
	});
	//companyDefault set on the login session 
	$('.companyDefault').on('click', function(e){
		//session('userClinicDefault')
		$scompany = $company_s[0].selectize;
		$scompany.setValue("{{session('userClinicDefault')}}");
	});
	
	$('.companyDefault').click();
	
	company_s = $company_s[0].selectize;
	//package  = $package[0].selectize;
	//package.disable();
	
	
	var transactionType = [];
	var datas = {!! $transactionType !!}; 
	
	if( typeof(datas.length) === 'undefined')
		transactionType.push(datas);
	else
		transactionType = datas;
	$transaction_t = $('select[name="modalTransactionType"]').selectize({  
		sortField: 'Id',
		searchField: ['Code','Description'],
		options : transactionType,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.Code) + '</span>' +
					'</span>' +
					'<span class="description">' + escape(item.Description) + '</span>' +
				'</div>';
			}
		}
		
	});
	transaction_t = $transaction_t[0].selectize;
	
	//companyDefault set on the login session 
	$('.transactionDefault').on('click', function(e){
		//session('userClinicDefault')
		transaction_t.setValue("Walk-In");
	});
	
	$('.transactionDefault').click();
	$.ajaxSetup({
	   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	});

	document.getElementById('prcNo').addEventListener('blur', function (){ //added 012025 for limit prc input in 7 and starts input in zero
        var prcInput = this.value;

        if (prcInput){
            this.value = prcInput.padStart(7, '0');
        }
    });	
});
</script>


