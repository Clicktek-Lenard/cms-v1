<style>
.dataTables_info{
	width: 100%;
}
.bg-dark {
    background-color: #343a40!important;
}
.bg-maroon {
    background-color: #800000!important
}
.text-light {
    color: #f8f9fa!important;
}
fieldset {
    display: block;
    margin-inline-start: 2px;
    margin-inline-end: 2px;
    padding-block-start: 0.35em;
    padding-inline-start: 0.75em;
    padding-inline-end: 0.75em;
    padding-block-end: 0.625em;
    min-inline-size: min-content;
    border-width: 2px;
    border-style: groove;
    border-color: #337AB7 !important;
    border-image: initial;
}
legend {
    font-size: 15px !important;
    display: block;
    padding-inline-start: 2px;
    padding-inline-end: 2px;
    width: fit-content !important;
    border-style: groove;
    border-color: initial;
    border-image: initial;
    border-color: #337AB7 !important;
    border-image: initial !important;
    border-bottom: 0px solid #e5e5e5 !important;
}
.right-div-scroll{
    overflow-y: scroll;
}

</style>
<div class="BillingModule">
	<!--LEFT-->
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
		<div class="panel panel-danger">
			<div class="panel-heading" style="line-height:12px;">List of Procedure</div>
			<div class="panel-body">
				<div class="row form-group row-md-flex-center">
					<div class=" col-sm-12 table-queue"></div>
				</div>
			</div>
		</div>
	</div>
	<!--RIGHT-->
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
		<div class="panel panel-warning">
			<div class="panel-heading" style="line-height:12px;">Cashiering</div>
			<div class="panel-body right-div-scroll">
				<div class="row form-group row-md-flex-center">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Discount:</legend>
								<div class="bg-maroon text-light text-right col-xs-1 col-sm-1 col-md-1 col-lg-1" style="padding: 6px 12px;" >Less:</div>
								<div class="bg-dark text-light text-right col-xs-2 col-sm-2 col-md-2 col-lg-2"  style="padding: 6px 12px;" >Type:</div>
								<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"  >
									<select name="modalDiscountType" class="form-control rowview" placeholder="Type of Discount" required="required" >
										<option value="" data-req="No" data-per="0" >None</option> 
										@foreach ($discountlist as $discount)
											<option value="{{ $discount->Id }}" data-req="{{ $discount->IdRequired }}" data-per="{{ $discount->Percentage }}">{{ $discount->Description }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
									<input type="text" class="form-control" name="discountID" placeholder="ID#" value="" disabled="disabled">
								</div>
								<div class="row form-group row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Discountable Amount:</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right"  > <span class="text-right" style="color:blue; font-size:20px;">&#8369;</span></div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="text" class="form-control text-right"  style="color:blue; font-size:20px;" name="discountAmount" placeholder="Amount" value="" disabled="disabled">
											<input type="text" class="hidden form-control text-right"  style="color:blue; font-size:20px;" name="IdiscountAmount" placeholder="Amount" value="" disabled="disabled">
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="text" class="form-control text-right" style="color:red; font-size:20px;" name="lessAmount" placeholder="0.00" value="0" disabled="disabled">
										</div>
									</div>
								</div>
								<div class="row form-group row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Loyalty Id / Points:</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"  >
											<input type="text" class="form-control" name="loyaltyId" placeholder="ID#" value="" disabled="disabled">
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="text" class="form-control" name="loyaltyPoints" placeholder="Points" value="" disabled="disabled">
										</div>
									</div>
								</div>
						</fieldset>
					</div>
				</div>
				<div class="row form-group row-md-flex-center">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Bill to:</legend>
								<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Provider:</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"  >
									<select name="modalProviderType" class="form-control rowview" placeholder="Type of Discount" required="required" >
										<option value="PATIENT">PATIENT</option> 
										<option value="HMO">HMO</option> 
										<option value="Corporate">CORPORATE</option> 
									</select>
								</div>
								<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"  >
									<input type="text" class="form-control" name="billTo" placeholder="Bill to" value="" disabled="disabled">
								</div>
							<div class="row  row-md-flex-center">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Coverage Type / Amount:</div>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"  >
										<select name="modalBillType" class="form-control rowview" placeholder="Bill Type" required="required" >
											<option value="FULL" >FULL</option> 
											<option value="PARTIALLY" >PARTIALLY </option> 
										</select>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"  >
										<input type="text" class="form-control text-right" style="color:blue; font-size:20px;" name="coPayAmount" placeholder="0.00" value="0" disabled="disabled">
									</div>
								</div>
							</div>
								<div class="row row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >HMO ID# / Card Name:</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"  >
											<input type="text" class="form-control" name="hmoId" placeholder="HMO ID#" value="" disabled="disabled">
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"  >
											<input type="text" class="form-control" name="cardName" placeholder="Card Name" value="" disabled="disabled">
										</div>
									</div>
								</div>
						</fieldset>
					</div>
				</div>
				<div class="row form-group row-md-flex-center">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Payment Type:</legend>
								<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Cash Pay:</div>
								<div class="text-right col-xs-5 col-sm-5 col-md-5 col-lg-5">
									<span  style="color:red; font-size:20px;">&#8369;</span>
								</div>
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  "  >
									<input type="text" class="form-control text-right" name="cashAmount" style="color:red; font-size:20px;" placeholder="Cash Amount" value="0">
								</div>
								<div class="row  row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Cheque Pay:</div>
										<div class="text-right col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<span  style="color:red; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="chequeAmount" style="color:red; font-size:20px;" placeholder="Cheque Amount" value="0">
										</div>
									</div>
								</div>
								<div class="row row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Cheque No. / Bank Name:</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="chequeNo"  placeholder="Cheque No." >
										</div>
										<div class="text-right col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<span  style="color:red; font-size:20px;"></span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<select name="modalBankName" class="form-control rowview" placeholder="Bank Name"  >
												<option value="">Select Bank Name</option> 
												<option value="BDO">BDO</option> 
												<option value="PNB">PNB</option> 
												<option value="CHINA BANK">CHINA BANK</option> 
											</select>
										</div>
									</div>
								</div>
								<div class="row row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Gcash Ref No. / Amount:</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="gcashRefNo"  placeholder="Gcash Ref. No." >
										</div>
										<div class="text-right col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<span  style="color:red; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="gcashAmount" style="color:red; font-size:20px;"  placeholder="Gcash Amount." value="0" >
										</div>
									</div>
								</div>
								<div class="row  row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Total Payment:</div>
										<div class="text-right col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<span  style="color:blue; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  "  >
											<input type="text" class="form-control text-right" name="totalAmount" style="color:blue; font-size:20px;" placeholder="Total Payment" value="0" disabled="disabled">
										</div>
									</div>
								</div>
								<div class="row  row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-maroon text-light text-right col-xs-1 col-sm-1 col-md-1 col-lg-1" style="padding: 6px 12px;" >O.R </div>
										<div class="bg-dark text-light text-right col-xs-2 col-sm-2 col-md-2 col-lg-2"  style="padding: 6px 12px;" >Balance:</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="ORnumber"  placeholder="O.R No." >
										</div>
										<div class="text-right col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<span  style="color:green; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  "  >
											<input type="text" class="form-control text-right" name="balanceAmount" style="background-color:#5cb85c; color:red; font-size:20px;" placeholder="Total Payment" value="0" disabled="disabled">
										</div>
									</div>
								</div>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
(function($) {
  $.fn.inputFilter = function(callback, errMsg) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
      if (callback(this.value)) {
        // Accepted value
        if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
          $(this).removeClass("input-error");
          this.setCustomValidity("");
        }
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        // Rejected value - restore the previous one
        $(this).addClass("input-error");
        this.setCustomValidity(errMsg);
        this.reportValidity();
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        // Rejected value - nothing to restore
        this.value = "";
      }
    });
  };
}(jQuery));

function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
  }
function selectedInfo( selectedName, datas )
{ 
	var selectedName = selectedName || "";
	var localdatas = datas || [];
	var iselect = "";
	var itemSelected = []; 
	var selected = itemSelected.length;
	var chargeToPatientAmount = 0;
	var totalAmount = 0; 
		var loadData = $('#QueueListTable').DataTable().rows().data();
		 loadData.each(function (value, index) {
			itemSelected.push(value.Id);
			totalAmount += parseInt(value.AmountItemPrice, 10);
			
		});
	//used selected item   //$('input[name="discountAmount"]').val(commaSeparateNumber(totalAmount));
	//used selected item  //$('input[name="IdiscountAmount"]').val(totalAmount);
	//used selected item  //$('input[name="balanceAmount"]').val(commaSeparateNumber(totalAmount));
	//---------$('input[name="cashAmount"]').val(totalAmount);
	//---------$('input[name="totalAmount"]').val(commaSeparateNumber(totalAmount));
	
	
	iselect = "</span><div class=\"text-right col-xs-12 col-sm-12 col-md-12\" ><b>Grand Total Amount :</b> <span class=\"Gtotal-amount row-discount\"><B><font style=\"color:blue; font-size:20px;\">&#8369; "+commaSeparateNumber(totalAmount)+"</font></B></span></div></div>";
	$('.selected_info').html("<a class=\"iselected\">"+selected+"</a> "+"row(s) selected"+"</span>");
	$('.Gtotal-amount').html(totalAmount);
	if( selectedName ) return iselect;
}

function reCalAmount()
{

	var percentage = $('select[name="modalDiscountType"]').find(':selected').data('per');
	var amountG =  $('input[name="IdiscountAmount"]').val();
	
	var lessAmount = (parseFloat(amountG) * parseFloat(percentage)/100);
	var DlessAmount = - (parseFloat(lessAmount));
	$('input[name="lessAmount"]').val(commaSeparateNumber(DlessAmount));
	var cashAmount = $('input[name="cashAmount"]').val();
	var chequeAmount = $('input[name="chequeAmount"]').val();
	var gcashAmount = $('input[name="gcashAmount"]').val();
	
	
	
	var balanceAmount = (parseFloat(amountG) - parseFloat(lessAmount) - parseFloat(cashAmount) - parseFloat(chequeAmount) - parseFloat(gcashAmount) );
	$('input[name="balanceAmount"]').val(balanceAmount);
	
	var totalAmount  =  (parseFloat(lessAmount) + parseFloat(cashAmount) + parseFloat(chequeAmount) + parseFloat(gcashAmount) );
	$('input[name="totalAmount"]').val(totalAmount);
}
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

$(document).ready(function(e) 
{
	$('.BillingModule').height($(window).height()-170);
	//$('.BillingModule').height(500);
	
	var rows_selected = [];
	
	$('.right-div-scroll').height($(window).height()-233)
	$html = "<div class=\"table-responsive\"><table id=\"QueueListTable\" class=\"table table-striped table-hover dt-responsive display \" style=\"width:100%;\" cellspacing=\"0\" >";
	$html += "<thead>";
          $html += "<tr>";
	$html += "<th></th>";
	$html += "<th class=\"text-center\"><input name=\"select_all\" id=\"select_all\" value=\"1\" type=\"checkbox\" ></th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Type</th>";
	$html += "<th>Amount</th>";
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
	$('.table-queue').append($html);
	
	var table = $('#QueueListTable').DataTable({
		data			: idata,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { 
			$(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-code', data.CodeItemPrice).attr('data-toggle-desc', data.DescriptionItemPrice).attr('data-toggle-itemtype', data.PriceGroupItemPrice).attr('data-toggle-amount', data.AmountItemPrice); 
			$(row).attr('id', data.Id);
			 if($.inArray(data.Id, rows_selected) !== -1){ 
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		},
		columns			: [
		{ "data": null },
		{ "data": "Id", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { return '<input type="checkbox" name="id[]" value="'+data+'">'; } },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PriceGroupItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
			{ "data": null,className: 'dt-body-center', 'searchable':false,  "orderable": true, targets: 1, "width":"10px"  },
			{"data": "CodeItemPrice", targets: 2, "width":"50px" },
			{"data": "DescriptionItemPrice", targets: 3, "width":"150px" },
			{"data": "DescriptionItemPrice", targets: 4, "width":"50px" },
			{"data": "AmountItemPrice", targets: 5, "width":"50px" }
		],
		'rowCallback': function(row, data, dataIndex){
			var itemId = $(row).attr('id');
			 if($.inArray(itemId, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		  },
		order	 : [ 3, 'asc' ],
		dom:     $dom,
		scrollY: $(window).height()-358,
		fnInfoCallback: function( settings, start, end, max, total, pre ) {	
			return "<div class=\" col-xs-12 col-sm-12 col-md-12 col-lg-12\"> <div  class=\" col-xs-6 col-sm-6 col-md-6\"  >"+"Got a total of"+" "+total+" "+"entries "+"</div><div  class=\" col-xs-6 col-sm-10 col-md-6\"  ><span class=\"selected_info\">"+selectedInfo(true,idata);
		}
	});
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
$('#QueueListTable tbody').on('click', 'input[type="checkbox"]', function(e){
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
      
      //cal total on checked item 
	var itemSelected = [];
	var DAmount = 0;
	var iAmount = 0;
	$("input:checked", $('#QueueListTable').dataTable().fnGetNodes()).each(function(){ 
		
		if( $(this).closest('tr').data('toggle-itemtype')  != "Package")
		{
			DAmount =  (parseFloat(DAmount) + parseFloat($(this).closest('tr').data('toggle-amount')) );
		}
		iAmount =  (parseFloat(iAmount) + parseFloat($(this).closest('tr').data('toggle-amount')) );
		
		itemSelected.push({"Id":$(this).val()});
	});
	//update view
	$('input[name="discountAmount"]').val(commaSeparateNumber(DAmount));
	$('input[name="IdiscountAmount"]').val(DAmount);
	$('input[name="balanceAmount"]').val(commaSeparateNumber(iAmount));
	
	
	
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#QueueListTable').on('click', 'tbody td', function(e){
	   if( $(this).hasClass('control') || $(this).find('input').hasClass('item-notes') ) return false;
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('#select_all').on('click', function(e){
      if(this.checked){
         $('#QueueListTable tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#QueueListTable tbody input[type="checkbox"]:checked').trigger('click');
      }
      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });
	


	$('select[name="modalDiscountType"]').on('change', function() {
		
		if(  $(this).find(':selected').data('req') == "Yes")
			$('input[name="discountID"]').attr('disabled', false).attr('required', true);
		else
			$('input[name="discountID"]').attr('disabled', true).attr('required', false).val('');
		
		if( $(this).find(':selected').data('per') != "0")
		{
			reCalAmount();
		}
		else
		{
			reCalAmount();
			$('input[name="lessAmount"]').val('0');
		}
	});
	
	$('input[name="cashAmount"]').on('input',function(e){
		reCalAmount();
	});
	$('input[name="chequeAmount"]').on('input',function(e){
		reCalAmount();
	});
	$('input[name="gcashAmount"]').on('input',function(e){
		reCalAmount();
	});
	
	
	$('select[name="modalBillType"]').selectize();
	$('select[name="modalProviderType"]').selectize();

	
	parent.waitingDialog.hide();
	$('input[name="cashAmount"]').inputFilter(function(value) {
	   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
	},"Only digits allowed");
	$('input[name="chequeAmount"]').inputFilter(function(value) {
	   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
	},"Only digits allowed");
	$('input[name="gcashAmount"]').inputFilter(function(value) {
	   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
	},"Only digits allowed");
	
	
	
});
</script>


