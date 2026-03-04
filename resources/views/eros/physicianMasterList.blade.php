<!--@extends('app')-->													 <!--pcp v2-->
<style>
.dataTables_wrapper .dataTables_info { padding-top: 0 !important; }

.column-search {
    color: #000 !important;
    background-color: #fff !important;
    border: 1px solid #ccc;
    padding: 4px 6px;
    font-size: 13px;
}

.truncate-col {
    white-space: normal !important;
    overflow-wrap: break-word;
    word-break: break-word;
    max-width: 160px; 
    min-height: 34px; 
}

#QueueListTable td {
    vertical-align: top;
}
.fullname-search {
	width: 100%;
	box-sizing: border-box;
}

</style>

@section('content')

	<div class="container-fluid">
		<div class="navbar-fixed-top crumb" >
			<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
					<li class="active"><a href="{{ url(session('userBUCode').'/cmsphysician/doctorsmodule') }}" class="waiting">Physician - EROS <span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
			</div>
		</div>
		<div class="body-content row">
			<div class="col-menu-15 table-queue"></div>
			<form id="downloadDoctorMasterList" class="form-horizontal" role="form" method="POST" action="{{ '/cmsphysician/doctorsmodule' }}" autocomplete="off">
		</div>
		<div class="navbar-fixed-bottom" >
			<div class="col-menu">
				<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
					<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
					    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
						@if(strpos(session('userRole'), '"ldap_role":"[PHYSICIAN-APPROVER]"') !== false)
					    	<a class=" btndownload btn btn-success col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Download</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection	
@section('script')	
<script>
$(document).ready(function (e) {
	let $html = `
    <div class="table-responsive">
        <table id="QueueListTable" class="table table-striped table-hover" >
            <thead>
                <tr>
                    <th></th>
                    <th>Full Name</th>
                    <th>PRC No.</th>
                    <th>Specialization</th>
                    <th>Position</th>
                    <th>Position Status</th>
                    <th>Branch Duty</th>
                    <th>Schedule Day</th>
                    <th>Schedule Start</th>
                    <th>Schedule End</th>
                </tr>
                <tr>
                   <th></th>
					<th><input type="text" placeholder="Search Full Name" class="column-search fullname-search" /></th>
					<th><input type="text" placeholder="Search PRC No." class="column-search" /></th>
					<th><input type="text" placeholder="Search Specialization" class="column-search" /></th>
					<th><input type="text" placeholder="Search Position" class="column-search" /></th>
					<th><input type="text" placeholder="Search Status" class="column-search" /></th>
					<th><input class="hidden"/></th> 
					<th><input class="hidden"/></th> 
					<th><input class="hidden"/></th> 
					<th><input class="hidden"/></th> 
                </tr>
            </thead>
        </table>
    </div>`;

	$('.table-queue').append($html);
	
	let data = [];
	let datas = {!! json_encode($physicianData) !!};
	if (typeof datas.length === 'undefined') data.push(datas);
	else data = datas;

	let table = $('#QueueListTable').DataTable({
		data: data,
		autoWidth: true,
		deferRender: true,
		createdRow: function (row, data, index) {
			$(row).attr('data-toggle-queueId', data.Id);
		},
		columns: [
			{ data: null },
			{ data: "FullName" },
			{ data: "PRCNo" },
			{ data: "Description" },
			{
				data: null,
				render: function (data, type, row) {
					let label = '';
					if (row.Specialist === 'Yes') label = 'SPECIALIST';
					else if (row.PCP === 'Yes') label = 'PRIMARY CARE PHYSICIAN';
					else if (row.ResignDoctor === 'Yes') label = 'RESIGNED';
					return label;
				}
			},
			{
				data: null,
				render: function (data, type, row) {
					let label = '';
					if (row.Specialist === 'Yes' && row.Visiting === 'Yes') label = 'VISITING';
					else if (row.PCP === 'Yes') {
						const statuses = [];
						if (row.Regular === 'Yes') statuses.push('REGULAR');
						if (row.Reliever === 'Yes') statuses.push('RELIEVER');
						label = statuses.join(' / ');
			}
					return label;
				}
			},
			{
				data: "NWDBranch",
				render: function (data) {
					try { return JSON.parse(data).join(' / '); }
					catch { return data; }
			}
			},
			{
				data: "Schedule",
				render: function (data) {
					try { return JSON.parse(data).join(' / '); }
					catch { return data; }
			}
			},
			{
				data: "TimeStart",
				render: function (data) {
					try { return JSON.parse(data).join(' / '); }
					catch { return data; }
			}
			},
			{
				data: "TimeEnd",
				render: function (data) {
					try { return JSON.parse(data).join(' / '); }
					catch { return data; }
				}
			},
		],
		responsive: false,
		columnDefs: [
			{ targets: 0, orderable: false, defaultContent:"", width: "2%" },
			{ targets: 1, width: "13%", className: 'data-row', orderable: false },
			{ targets: 2, width: "10%", orderable: false },
			{ targets: 3, width: "10%", orderable: false },
			{ targets: 4, width: "10%", orderable: false },
			{ targets: 5, width: "10%", orderable: false },
			{ targets: 6, width: "15%", className: 'truncate-col', orderable: false },
			{ targets: 7, width: "15%", className: 'truncate-col', orderable: false },
			{ targets: 8, width: "15%", className: 'truncate-col', orderable: false },
			{ targets: 9, width: "15%", className: 'truncate-col', orderable: false }
		],
		order: [1, 'asc'],
		dom: "frtiS",
		scrollX: true,
		scrollY: $(document).height() - $('.navbar-fixed-top.crumb').height() - $('.navbar-fixed-bottom').height() - 170,
		initComplete: function () {
            console.log('DataTable initialization complete');
            this.api().columns().every(function () {
                var column = this;
                var columnIndex = column.index();

                $('input', column.header()).on('keyup change', function () {
                    let value = this.value;
					if (columnIndex === 1 && value.length > 0) {
						value = '^' + value; 
					}
					if (column.search() !== value) {
						column.search(value, true, false).draw(); 
					}
                });
            });
        },
        drawCallback: function() {
            console.log('Table redrawn');
        }		
	});
	
	$('#QueueListTable thead input').on('keyup', function() {
		console.log('Keyup event fired on:', $(this).attr('placeholder'));
	});
	$('#QueueListTable thead input').on('click', function (e) {
		e.stopPropagation();
	});

	$('#QueueListTable_info').addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12');
	$('#QueueListTable_filter label').addClass('hide');
	$('#QueueListTable_filter input').addClass('hide');

	$('#QueueListTable').on('click', '.data-row', function (e) {
		waitingDialog.show('Loading...', { dialogSize: 'sm', progressType: 'success' });
		var id = $(this).closest('tr').data('toggle-queueid'); 
		var hyperlink = document.createElement('a');
		hyperlink.href = 'physician/' + id + '/edit';
		var mouseEvent = new MouseEvent('click', {
			view: window,
			bubbles: true,
			cancelable: true
		});
		
		hyperlink.dispatchEvent(mouseEvent);
		(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
		
		e.preventDefault();		
	});
	
	$('.btndownload').on('click', function (e) {
		if (parent.required($('form'))) return false;
		e.preventDefault();
		$('#downloadDoctorMasterList').submit();
	});
	
});

</script>
@endsection



