<div class="BillingHistory">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="panel panel-danger">
			<div class="panel-heading" style="line-height:12px;">History</div>
			<div class="panel-body">
				<div class="row form-group row-md-flex-center">
					<div class=" col-sm-12 table-summary"></div>
				</div>
			</div>
		</div>
	</div>

</div>

<script>
$(document).ready(function(e) 
{
	$html = "<div class=\"table-responsive\"><table id=\"SummaryListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Type</th>";
	$html += "<th>Status</th>";
	$html += "<th>Provider</th>";
	$html += "<th>Bill To</th>";
	$html += "<th>Co. Pay</th>";
	$html += "<th>Co. Pay Amount</th>";
	$html += "<th>Payment Type</th>";
	$html += "<th>Ref. No.</th>";
	
	$html += "<th>Bank Name</th>";
	$html += "<th>OR No.</th>";
	$html += "<th>Item Amount</th>";
	$html += "<th>Amount Pay</th>";
	$html += "<th>Balance</th>";
	$html += "<th>Remaining</th>";

	$html += "</tr>";
	$html +="</thead><tbody>";

	var idata = []; 
	var idatas = {!! $itemData !!}; 
	
	
	if( typeof(idatas.length) === 'undefined')
		idata.push(idatas);
	else
		idata = idatas;
	
	var $dom = (idata.length >= 11)?"frtiS":"frti";
	
	
	
	$html +="</tbody></table></div>";
	$('.table-summary').append($html);
	
	var table = $('#SummaryListTable').DataTable({
		data			: idata,
		autoWidth		: false,
		deferRender		: true,
		
		columns			: [
		{ "data": null },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PriceGroupItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "QueueStatus", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "ProviderType", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "CopanyBillTo", "render": function(data,type,row,meta) { var idata = (data == null)?'':data; return '<div class="wrap-row">'+idata+'</div>'; } },
		{ "data": "CoverageType", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "CoverageAmount", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PaymentType", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "RefNo", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "BankName", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "ORNum", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "ItemAmount", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ "data": "PayAmount", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "BalanceAmount", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ "data": "RemainingAmount", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } }
		],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
			{"data": "CodeItemPrice",orderable: false, targets: 1, "width":"50px" },
			{"data": "DescriptionItemPrice", orderable: false, targets: 2, "width":"250px" },
			{"data": "PriceGroupItemPrice", orderable: false, targets: 3, "width":"60px" },
			{"data": "QueueStatus", orderable: false, targets: 4, "width":"120px" },
			{"data": "ProviderType", orderable: false, targets: 5, "width":"100px" },
			{"data": "CopanyBillTo", orderable: false, targets: 6, "width":"200px" },
			{"data": "CoverageType", orderable: false, targets: 7, "width":"50px" },
			{"data": "CoverageAmount", orderable: false, targets: 8, "width":"80px" },
			{"data": "PaymentType", orderable: false, targets: 9, "width":"50px" },
			{"data": "RefNo", orderable: false, targets: 10, "width":"80px" },
			{"data": "ORNum", orderable: false, targets: 12, "width":"50px" },
			{"data": "ItemAmount", orderable: false, targets: 13, "width":"80px" },
			{"data": "PayAmount", orderable: false, targets: 11, "width":"80px" },
			{"data": "BalanceAmount", orderable: false, targets: 14, "width":"80px" },
			{"data": "RemainingAmount", orderable: false, targets: 15, "width":"80px" }
		
			
		],
		dom:     $dom,
		scrollY: $(window).height()-378
		
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('.BillingHistory').height($(window).height()-170);

});
</script>