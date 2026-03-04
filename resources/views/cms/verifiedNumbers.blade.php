<!--@extends('app')-->
@section('style')
<style>
.data-row{ color: #337AB7;text-decoration: none; cursor:pointer;}
.table-queue{ margin-top:-10px; margin-bottom:20px; z-index:0;}	
</style>
@endsection

@section('content')
<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
        <div class="col-menu-10">
            <div class="header-crumb">
                <ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/enrollment/cardverified') }}" class="waiting">Verification <span class="badge" style="top:-9px; position:relative;"></span></a></li>
                </ol>
            </div>
        </div>
    </div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
		<form id="formQueueCreate" class="form-horizontal" role="form" method="POST" action="{{ url(session('userBUCode').'/enrollment/cardverified') }}" autocomplete="off">
		        <input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="panel panel-primary">
					<div class="panel-heading" style="line-height:12px;">Info</div>
					<div class="panel-body">
                        @if(isset($alertMessage))
                            @if($alertMessage === "Card has been verified and updated successfully.")
                                <div class="alert alert-success" id="alertMessage">
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
                                }, 2000); 
                            </script>
                        @endif	
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                            <div class="row form-group row-md-flex-center">
                                <div class="col-sm-2 col-md-2 pad-0-md text-right-md">
                                    <input type="hidden" name="CardNumber" />
                                        <label class="bold ">Card Number<font style="color:red;">*</font></label>
                                </div>
                                    <div class="col-sm-6 col-md-6">
                                        <input type="text" class="form-control" name="VerifiedCardNumbers"  id="cardInput"  placeholder="################"  required="required">
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                    </div>
                            </div>
                        </div>
				    </div>
			    </div>
				<div class="panel panel-success">
				<div class="panel-heading" style="line-height:12px;">Verified Card Numbers</div>
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
                    <a href="{{ url(session('userBUCode').'/enrollment/cardverified/create') }}" class="waiting btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span>Verify</a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection 
@section('script')

<!-- Add Table column to display the Year,Batch and Month-->
<script>
$(document).ready(function(e)
{
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Verified Card Number</th>";
    $html += "<th>Year</th>";
    $html += "<th>Batch</th>";
    $html += "<th>Month</th>";
	$html += "<th>ICT Received</th>";
	$html += "<th>Date Received</th>";
   
	
	$html += "</tr>";
        $html +="</thead> <tbody>";
		var data = []; 
		var datas = {!! $data !!}; 
		if( typeof(datas.length) === 'undefined')
			data.push(datas);
		else
			data = datas;
		
		$html +="</tbody></table></div>";

        var preprocessedData = data.map(function(item) {
        // Clone the original item to avoid modifying the source data
        var newItem = Object.assign({}, item);

        // Reverse the conversion for Year, Batch, and Month
        newItem.Year = reverseYear(item.Year);
        newItem.Batch = reverseBatch(item.Batch);
        newItem.Month = reverseMonth(item.Month);

        return newItem;
     });
		$('.table-queue').append($html);
		
		var table = $('#QueueListTable').DataTable({
			data			: preprocessedData,
			autoWidth		: false,
			deferRender		: true,
			createdRow		: function ( row, data, index ) { $(row).attr('data-toggle-queueId', data.Id); },
			columns			: [
			{ "data": null },
			{ "data": "VerifiedCardNumbers", "render": function(data,type,row,meta) { return '<div class="wrap-row">' +data+'</div>'; } }, //.slice(0, 4) + '-' +data.slice(4, 8) + '-' + data.slice(8, 12) + '-' +data.slice(12,16) +
			{ "data": "Year"},
            { "data": "Batch" },
            { "data": "Month" },
            { "data": "ICTReceived", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
			{ "data": "DateReceived", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },],

			responsive		: { details: { type: 'column' } },
			columnDefs		: [
				{className: 'control', orderable: false, targets: 0, "width":"15px",defaultContent: ""},
				{ targets: 1, "width":"100px" },
				{ targets: 2, "width":"100px" },
				{ targets: 3, "width":"100px" },
                { targets: 4, "width":"100px" },
                { targets: 5, "width":"100px" },
                { targets: 6, "width":"100px" }					
			],
			order			: [ 1, 'asc' ],
			dom:            "frti",
			scrollY: $(document).height()-$('.navbar-fixed-top.crumb').height()-$('.navbar-fixed-bottom').height()-350,
			lengthMenu: [1000],
		});
		$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
		
		$('#QueueListTable').on('click','.data-row',function(e){
			waitingDialog.show('Loading...', {dialogSize: 'sm', progressType: 'success'});
			var id = $(this).closest('tr').data('toggle-queueid'); 
			var hyperlink = document.createElement('a');
			hyperlink.href = 'verifiedNumbers/'+id+'/edit';
			var mouseEvent = new MouseEvent('click', {
				view: window,
				bubbles: true,
				cancelable: true
			});
			
			hyperlink.dispatchEvent(mouseEvent);
			(window.URL || window.webkitURL).revokeObjectURL(hyperlink.href);
			
			e.preventDefault();
	
		});
		$('#cardInput').on('input', function() {
            var inputValue = $(this).val().replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

            if (inputValue.length > 16) {
                inputValue = inputValue.slice(0, 16);
            }

            $(this).val(inputValue);
        });

        $('#barcodeScanner').on('input', function() {
            var cardNumber = $(this).val();

            if (cardNumber) {
                $.ajax({
                    url: '/cardverified', 
                    method: 'POST',
                    data: { cardNumber: cardNumber },
                    success: function(data) {
                        if (data.valid) {
                           
                            $('#formQueueCreate').submit();
                        }
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }
        });

        $('.savebtn').on('click', function(e) {
            if (parent.required($('form'))) return false;
            e.preventDefault();

            $('#formQueueCreate').submit();
        });

        $('#formQueueCreate').on('keypress', function(event) {
            if (event.which === 13) {
                event.preventDefault(); 
                $(this).submit();
            }
        });
		window.onload = function() {
  			document.getElementById("cardInput").focus();
        }
        // Define a function to reverse convert Year from letters to numbers
        function reverseYear(letterYear) {
        
        const letterToDigit = {
            "A": "1",
            "B": "2",
            "C": "3",
            "D": "4",
            "E": "5",
            "F": "6",
            "G": "7",
            "H": "8",
            "I": "9",
            "J": "0"
        };

        const numericYear = letterYear.split('').map(letter => letterToDigit[letter]).join('');

        return numericYear;
    }

    // Define a function to reverse convert Batch from letters to numbers
    function reverseBatch(letterBatch) {
        const letterToDigit = {
            "A": "1",
            "B": "2",
            "C": "3",
            "D": "4",
            "E": "5",
            "F": "6",
            "G": "7",
            "H": "8",
            "I": "9",
            "J": "0"
        };

        const numericBatch = letterBatch.split('').map(letter => letterToDigit[letter]).join('');

        return numericBatch;
    }
    // Define a function to reverse convert Month from letters to their names
    function reverseMonth(letterMonth) {
        const monthData = {
            "AL": "01",
            "BK": "02",
            "CJ": "03",
            "DI": "04",
            "EH": "05",
            "FG": "06",
            "GF": "07",
            "HE": "08",
            "ID": "09",
            "JC": "10",
            "KB": "11",
            "LA": "12"
        };

        const originalMonth = monthData[letterMonth];

        return originalMonth;
    }
});
</script>
@endsection