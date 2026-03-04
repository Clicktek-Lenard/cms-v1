<!--@extends('app')-->
@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/enrollment/cardregistration') }}" class="waiting">Card - Registration <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
			<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/enrollment/cardregistration') }}" autocomplete="off">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
					<div class="panel panel-primary">
						<div class="panel-heading" style="line-height:12px;">Info</div>
						<div class="panel-body">
							
	
							@if(isset($alertMessage))
								@if($alertMessage === "Data has been enrolled successfully...")
									<div class="alert alert-success" id="alertMessage">
										{{ $alertMessage }}
									</div>
								@elseif($alertMessage === "Card number already exists. Please choose a different Card number.")
									<div class="alert alert-warning" id="alertMessage">
										{{ $alertMessage }}
									</div>
								@else 
									<div class="alert alert-danger" id="alertMessage">
										{{ $alertMessage }}
									</div>
								@endif
								<script>           
									setTimeout(function() {                                    
										 var alert = document.getElementById('alertMessage');                                                    
										 if (alert) {                                      
											 alert.style.display = 'none';
										 }
									 }, 1000); 
								 </script>
							@endif
						
							<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
								<div class="row form-group row-md-flex-center">
									
									<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
										<label class="bold ">Released To<font style="color:red;">*</font></label>
									</div>
									<div class="col-sm-6 col-md-6">
										<input type="hidden" name="UserCode" />
										<select name="Users" class="form-control disabled" placeholder="Users" required="required">
											<option value=""></option>
											@foreach ($users as $user) 
											<option value="{{ $user->Code }}">{{ $user->Description }}</option>
											@endforeach
										</select>
									</div>
	
									<div class="col-sm-2 col-md-2 pad-0-md text-right-md">
									<input type="hidden" name="CardNumber" />
										<label class="bold ">Card Number<font style="color:red;">*</font></label>
									</div>
										<div class="col-sm-6 col-md-6">
											<input type="text" class="form-control" name="CardNumber"  id="barcodeScanner"  value= "" placeholder="################"  required="required">
										</div>
										
								</div>
							</div>
	
						</div>
					</div>
					<div class="panel panel-success">
					<div class="panel-heading" style="line-height:12px;">Registration</div>
					<div class="panel-body">
						<div class="row">
							<div class="table-queue">
							</div>
						</div>
					</div>
				  </div>
				</form>
			</div>   
	</div>
    <div class="navbar-fixed-bottom" >
        <div class="col-menu">
            <div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
            	<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                    <button class="savebtn saving btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-sm-offset-6 col-md-offset-6 col-lg-offset-6" style="border-radius:0px; line-height:29px;" type="button"></i> Save </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@section('script')
<script>

var addModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
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
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	},

	{
		id: 'btnSave',
		cssClass: 'btn-primary actionbtn',
		label: 'Received',
		action: function (modalRef){
		
			if( parent.required($('#patientAddModalForm')) ) return false;
	
			var form = $('#patientAddModalForm');
			parent.postData(form.attr('action'),form.serialize(),function($rowId){ 
				// addModal.setTitle("Receiver - View");
				// addModal.setData("pageToLoad", "{{ '/cms/enrollment/pages/enrollmentReceived' }}"+'/'+$rowId+'/edit');
				// addModal.realize();
				addModal.close();
	
			});
		}
	
		
	}]
});

$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Card Number</th>";
	$html += "<th>Date Enrolled</th>";
	$html += "<th>Released To</th>";
	$html += "<th>Date Released</th>";
	$html += "<th>Released By</th>";
	$html += "</tr>";
        $html +="</thead><tbody>";
		var data = []; 
		var datas = {!! $queue !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";
		$('.table-queue').append($html);
		
		var table = $('#QueueListTable').DataTable({
			data			: data,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
			columns			: [
			{ "data": null },
			{ "data": "CardNumber", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DateEnrolled", "render": function(data,type,row,meta) {return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "ReleaseTo", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DateRelease", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "ReleaseBy", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			],
			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"70px"  },
				{ targets: 2, "width":"120px" },
				{ targets: 3, "width":"100px" },
				{ targets: 4, "width":"100px" },
				{ targets: 5, "width":"120px" }
			],
			order			: [ 3, 'desc' ],
			dom:            "frtiS",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-350,
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});

		//  // Event listener for the search input
		// $('.dataTables_filter input').on('input', function() {
		// 	var inputValue = $(this).val();

		// 	// Check if the input is a valid 16-character alphanumeric value
		// 	if (/^[a-zA-Z0-9]{16}$/.test(inputValue)) {
		// 	// Open the modal
		// 	openModalWithBarcode(inputValue);
		// 	}
		// });

		//  // Helper function to open modal with a barcode value
		//  function openModalWithBarcode(barcode) {
		// 	var rowData = data.find(function(item) {
		// 		return item.CardNumber === barcode;
		// 	});

		// 	if (rowData) {
		// 	var rowId = rowData.Id;

		// 	// Set the title and pageToLoad for the modal
		// 	addModal.setTitle("Receiver - View");
		// 	addModal.setData("pageToLoad", "{{ '/cms/enrollment/pages/enrollmentReceived/'}}" + rowId + '/edit');

		// 	// Open the modal
		// 	addModal.realize();
		// 	addModal.open();
		// 	}
		// }

		$('#QueueListTable').on('click', '.data-row', function(e) {
			var rowData = table.row($(this).closest('tr')).data();
			var rowId = rowData.Id;  // Extract the TestCode property

			//testcode was pass correctly into url also the groupName = working
			addModal.setTitle("Receiver - View");
			addModal.setData("pageToLoad", "{{ '/cms/enrollment/pages/enrollmentReceived/'}}" + rowId + '/edit');
			addModal.realize();
			addModal.open();
			e.preventDefault();
		});
});

	var userSelect = $('select[name="Users"]').selectize({
			onChange: function(value) {
				if (!value.length) {
					$('input[name="UserCode"]').val('');
					return;	
				}
				$('input[name="UserCode"]').val($('select[name="Users"] option:selected').text());

				// Save the selected value to localStorage
				localStorage.setItem('selectedUser', value);
			}
		});

		// Load the selected value from localStorage and set it in the dropdown
		var selectedUser = localStorage.getItem('selectedUser');
		if (selectedUser) {
			userSelect[0].selectize.setValue(selectedUser);
		}

		// Bind 'Enter' key event to form submission
		$('#formQueueCreate').on('keypress', function(event) {
			if (event.which === 13) {
				event.preventDefault();  // Prevent default behavior of the Enter key
				$(this).submit();
			}
		});

		$('.savebtn').on('click', function(e) {
				if (parent.required($('form'))) return false;
				e.preventDefault();

				$('#formQueueCreate').submit();
			});

			window.onload = function() {
  			document.getElementById("barcodeScanner").focus();
			}

		$.ajaxSetup({
			headers: {
				'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			}
		});
</script>
@endsection