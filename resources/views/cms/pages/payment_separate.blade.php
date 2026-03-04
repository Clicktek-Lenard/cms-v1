
<style>
.form-control
{
    font-weight: bolder;
}

.cms-font
{
    font-weight: bolder;
}
	
.selectize {
    width: 75%;
}
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
.summary{ cursor:pointer; }
.selectize-dropdown .name {
    display: block;
    font-weight: bold;
    margin-bottom: 4px; /* Adjust spacing between elements */
}

.selectize-dropdown .description {
    display: block;
    font-size: smaller;
    color: #888; /* Replace with your desired light color */
}

</style>
<form id="BillingPost">
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
									<input type="hidden" name ="SiniorAge" value="{{$seniorAge}}">
									<input type="hidden" name="pwdId" value="">
									<select name="modalDiscountType" class="form-control rowview type" placeholder="Type of Discount" >
										<option value="" data-req="No" data-per="0" >None</option> 
										@foreach ($discountlist as $discount)
											<option value="{{ $discount->Id }}" data-req="{{ $discount->IdRequired }}" data-per="{{ $discount->Percentage }}">{{ $discount->Description }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
									<input type="text" class="form-control discountID" name="discountID" placeholder="ID#" value="" id="OSCA" disabled="disabled">
									<input type="hidden" value="{{ $PwdExpiredDate ? date('m/d/Y', strtotime($PwdExpiredDate)) : '' }}" name="OscaPwdDate">
								</div>


								<div class="row form-group row-md-flex-center OscaId hidden">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >OSCA/PWD ID:</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-5  ">
											<input type="text" class="form-control text-left hidden"  style="color:blue; font-size:20px;" name="IdiscountAmount" placeholder="Amount" value="" readonly="readonly">
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="" class="form-control datepickertwo" name="ExpiryDatePWD" id="oscaPwdDateExpire" placeholder="Date Expire" {{$PwdExpiredDate ? '': 'disabled' }}>
										</div>
									</div>
								</div>
								<div class="row form-group row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Discountable Amount:</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right"  > <span class="text-right" style="color:blue; font-size:20px;">&#8369;</span></div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="text" class="form-control text-right"  style="color:blue; font-size:20px;" name="discountAmount" placeholder="Amount" value="" disabled="disabled">
											<input type="text" class="hidden form-control text-right"  style="color:blue; font-size:20px;" name="IdiscountAmount" placeholder="Amount" value="" readonly="readonly">
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"  >
											<input type="text" class="form-control text-right" style="color:red; font-size:20px;" name="lessAmount" placeholder="0.00" value="0" readonly="readonly">
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
									<select name="billTo" class="form-control rowview" placeholder="Bill To" disabled="disabled"  >
									</select>
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
										<input type="text" class="form-control text-right" style="color:blue; font-size:20px;" name="coPayAmount" placeholder="0.00" value="0" readonly="readonly">
									</div>
								</div>
							</div>
								<div class="row row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >HMO ID# / Card Name:</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"  >
											<input type="text" class="form-control" name="hmoId" placeholder="HMO ID#" value="" readonly="readonly">
										</div>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"  >
											<input type="text" class="form-control" name="cardName" placeholder="Card Name" value="" readonly="readonly">
										</div>
									</div>
								</div>
						</fieldset>
					</div>
				</div>
				<div class="row form-group row-md-flex-center Agent hidden">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Agent Info:</legend>
								<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Name:</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<input type="hidden" name="_agent" id="agent" value="{{$Payment->AgentName ?? ''}}">
										<input type="hidden" name="Qid" id="" value="{{$QID}}">
										<select name="Agent" class="form-control " placeholder="Select Agent Name" id="Agent"@if($Payment && $Payment->AgentName) disabled @endif>
											<option value=""></option> 
											@foreach($agents as $agent)
											<option value="{{$agent->EmployeeID}}"  @if($Payment && $Payment->AgentName === $agent->EmployeeName) selected  @else '' @endif >{{$agent->EmployeeName}}</option> 
											@endforeach
										</select>
									</div>
						</fieldset>
					</div>
				</div>
				<div class="row form-group row-md-flex-center hcard hidden">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Health + Card Number:</legend>
								<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Card Number:</div>
									<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<input type="text" name="Hcard" id="Hcard" placeholder="Card Number" value="{{ $Card[0]->HCardNumber ?? '' }}" class="form-control Card" oninput="formatCardNumber(this)" {{ !empty($Card[0]->HCardNumber) ? 'disabled' : '' }}>
									</div>
						</fieldset>
					</div>
				</div>
				<div class="row form-group row-md-flex-center divPaymentType">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<fieldset>
							<legend>Payment Type:</legend>
								<div class="divSelect" >
									<div class="row  row-md-flex-center">
										<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
											<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Select Payment Mode:</div>
											<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9  "  >
												<select name="modalSelect" class="form-control rowview" placeholder="Select Payment Mode" multiple required="required"    >
													<option value="">Select Payment Mode</option> 
													<option value="Cash">Cash Pay</option> 
													<option value="GCash">G-Cash Pay</option> 
													<option value="Credit">Credit Card Pay</option> 
													<option value="Cheque">Cheque Pay</option> 
													<option value="Online">Online/Bank Transfer</option> 
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="divSelectAdd" >
								</div>
								<br>
								<br>
								<br>
								<div class="row  row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3"  style="padding: 6px 12px;" >Total Payment:</div>
										<div class="text-right col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<span  style="color:blue; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  "  >
											<input type="text" class="form-control text-right" name="totalAmount" style="color:blue; font-size:20px;" placeholder="Total Payment" value="0" readonly="readonly">
										</div>
									</div>
								</div>
								<div class="row  row-md-flex-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div class="bg-maroon text-light text-right col-xs-1 col-sm-1 col-md-1 col-lg-1" style="padding: 6px 12px;" >O.R </div>
										<div class="bg-dark text-light text-right col-xs-2 col-sm-2 col-md-2 col-lg-2"  style="padding: 6px 12px;" >Balance:</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 "  >
											<input type="text" class="form-control text-right" name="ORnumber"  placeholder="O.R No." required="required" >
										</div>
										<div class="text-right col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<span  style="color:green; font-size:20px;">&#8369;</span>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4  "  >
											<input type="text" class="form-control text-right hide" name="IbalanceAmount" value="0" disabled="disabled" >
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
</form>


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
	var balAmount = 0; 
		var loadData = $('#QueueListTable').DataTable().rows().data();
		 loadData.each(function (value, index) {
			itemSelected.push(value.Id);
			totalAmount += parseFloat(value.AmountItemPrice);
			balAmount += parseFloat(value.AmountRemaining);
		});
	//used selected item   //$('input[name="discountAmount"]').val(commaSeparateNumber(totalAmount));
	//used selected item  //$('input[name="IdiscountAmount"]').val(totalAmount);
	//used selected item  //$('input[name="balanceAmount"]').val(commaSeparateNumber(totalAmount));
	//---------$('input[name="cashAmount"]').val(totalAmount);
	//---------$('input[name="totalAmount"]').val(commaSeparateNumber(totalAmount));
	
	
	iselect = "<div class=\"text-right col-xs-6 col-sm-6 col-md-6\" ><b>Grand Total :</b> <span class=\"Gtotal-amount row-discount summary\"><B><font style=\"color:blue; font-size:20px;\">&#8369; "+commaSeparateNumber(totalAmount.toFixed(2))+"</font></B></span></div>" +
			"<div class=\"text-right col-xs-6 col-sm-6 col-md-6 text-right\" ><b>Balance Amount :</b> <span class=\"Balance-amount row-discount text-right\"><B><font style=\"color:blue; font-size:20px;\">&#8369; "+commaSeparateNumber(balAmount.toFixed(2))+"</font></B></span></div>"  +
			"</div></div>";
	//$('.selected_info').html("<a class=\"iselected\">"+selected+"</a> "+"row(s) selected"+"</span>");
	//$('.Gtotal-amount').html(totalAmount);
	if( selectedName ) return iselect;
}

function reCalAmount()
{

	var percentage = $('select[name="modalDiscountType"]').find(':selected').data('per');
	var amountG =  $('input[name="IdiscountAmount"]').val();
	var IbalanceAmount  = $('input[name="IbalanceAmount"]').val();
	var lessAmount = (parseFloat(amountG) * parseFloat(percentage)/100);
	var DlessAmount = - (parseFloat(lessAmount));
	$('input[name="lessAmount"]').val(commaSeparateNumber(DlessAmount));
	var cashAmount = ( typeof $('input[name="cashAmount"]').val() === "undefined" )? 0: $('input[name="cashAmount"]').val();
	var gcashAmount = ( typeof $('input[name="gcashAmount"]').val() === "undefined" )? 0: $('input[name="gcashAmount"]').val();
	var creditAmount = ( typeof $('input[name="creditAmount"]').val() === "undefined" )? 0: $('input[name="creditAmount"]').val();
	var chequeAmount = ( typeof $('input[name="chequeAmount"]').val() === "undefined" )? 0: $('input[name="chequeAmount"]').val();
	var onlineAmount = ( typeof $('input[name="onlineAmount"]').val() === "undefined" )? 0: $('input[name="onlineAmount"]').val();
	var coPayAmount = ( typeof $('input[name="coPayAmount"]').val() === "undefined" )? 0: $('input[name="coPayAmount"]').val();
	
	
	var balanceAmount = ( parseFloat(IbalanceAmount) -  parseFloat(lessAmount) - parseFloat(cashAmount) - parseFloat(gcashAmount) - parseFloat(creditAmount) - parseFloat(chequeAmount) - parseFloat(onlineAmount) - parseFloat(coPayAmount)  );
	$('input[name="balanceAmount"]').val(balanceAmount.toFixed(2));

	var totalAmount  =  (parseFloat(lessAmount) + parseFloat(cashAmount) + parseFloat(gcashAmount) + parseFloat(creditAmount) + parseFloat(chequeAmount) + parseFloat(onlineAmount) + parseFloat(coPayAmount)   );
	$('input[name="totalAmount"]').val(totalAmount.toFixed(2));

	
	var nCoPayAmount = ( parseFloat(totalAmount) -  parseFloat(lessAmount)  -  parseFloat(lessAmount));
	if( $('select[name=modalProviderType]').val() != 'PATIENT' &&  $('select[name="modalBillType"]').val() == 'FULL'     )
	{ 
		
		$('input[name="coPayAmount"]').val(nCoPayAmount.toFixed(2));
		
	}
	
	
	if (parseFloat(balanceAmount) <= 0  )  
		parent.$('#btnsave').removeClass('hide');
	else
		parent.$('#btnsave').addClass('hide');
	
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
function validateDiscount() {
	$('.Card').off('focusout').on('focusout', function (e) {
        var input = $(this);
        var Discount = input.val();
        var iReturn = "";

        if (Discount === '') {
            return iReturn;
        }

		$.ajax({
		async: false,
        type: "POST",
        url: '/cms/queue/discount',
        data: {
			'_token': '{{ csrf_token() }}',
            'Discount': Discount
        },
        success: function(data) {
			if (data.status === 'error') 
			{
				alert(data.message);
				input.val('');
				input.focus();
			}
        }
   
         });

     });
}

function checkedReCalAmount(table)
{ 
	var statustr = "";
	var DAmount = 0;
	var iAmount = 0;
	var itemSelected = [];
	var $table  = table.table().node();
	var tableCount = 0;
	
	$('tbody input[type="checkbox"]:checked', $table).each(function(){ 
		// console.log($(this).closest('tr').data('toggle-allowdiscount')  == '0');
		if( $(this).closest('tr').data('toggle-itemtype')  != "Package" && $(this).closest('tr').data('toggle-allowdiscount')  != '0')
		{
			if( ($(this).closest('tr').data('toggle-status') != "Fully Paid"  &&   parseFloat($(this).closest('tr').data('toggle-balance'))  > 0 ) )
			{
				DAmount =  (parseFloat(DAmount) + parseFloat($(this).closest('tr').data('toggle-balance')) );
			}
		}
		iAmount =  (parseFloat(iAmount) + parseFloat($(this).closest('tr').data('toggle-balance')) );
		
		if( iAmount != '0')
		{
			itemSelected.push({"Id":$(this).val()});
		}
		if( $(this).data('toggle-group')  == "CARD" &&  $(this).closest('tr').data('toggle-balance') != '0' )
		{
			  $('.Agent').removeClass('hidden');
			  $('.sCard').attr('required', true);
		}
		if ($(this).closest('tr').data('toggle-compasubgroup') == "CARD" && $(this).closest('tr').data('toggle-balance') != '0' && $(this).data('toggle-group')  != "CARD") {
			$('.hcard').removeClass('hidden');
			$('.Card').attr('required', true);
			validateDiscount();
		}
		
			
		if( $(this).data('toggle-group')  == "CARD" && parseFloat($(this).data('toggle-itemused')) != 0 && (parseFloat($(this).data('toggle-statustr')) == 201 || parseFloat($(this).data('toggle-statustr')) == 205  ) )
		{
			statustr = "iEnabled";
		}
		tableCount = parseFloat(tableCount) + 1; 

	});
	// $('.type').on('change', function () {
    // if ($(this).val() === '14') {
       
    // }
	// });
	if( parseFloat(tableCount) == 1 && statustr  == "iEnabled")
	{
		$('.divPaymentType').addClass('hide');
		$('select[name="modalSelect"]').attr('required', false);
		$('input[name="ORnumber"]').attr('required', false);
	}
	else
	{
		$('.divPaymentType').removeClass('hide');
		$('select[name="modalSelect"]').attr('required', true);
		$('input[name="ORnumber"]').attr('required', true);
	}

	//update view
	$('input[name="discountAmount"]').val(commaSeparateNumber(DAmount.toFixed(2)));
	$('input[name="IdiscountAmount"]').val(DAmount.toFixed(2));
	$('input[name="IbalanceAmount"]').val(iAmount.toFixed(2));
	$('input[name="balanceAmount"]').val(commaSeparateNumber(iAmount.toFixed(2)));
	
	if( $('select[name=modalProviderType]').val() != 'PATIENT' &&  $('select[name="modalBillType"]').val() == 'FULL' )
	{
		$('input[name="coPayAmount"]').val(iAmount).attr('readonly', true);
		$('.divPaymentType').addClass('hide');
		$('select[name="modalSelect"]').attr('required', false);
		$('input[name="ORnumber"]').attr('required', false);
	}
	else if( $('select[name=modalProviderType]').val() != 'PATIENT' &&  $('select[name="modalBillType"]').val() != 'FULL' )
	{
		$('input[name="coPayAmount"]').attr('readonly', false);
		$('.divPaymentType').removeClass('hide');
		$('select[name="modalSelect"]').attr('required', true);
		$('input[name="ORnumber"]').attr('required', true);
	}
	else if( $('select[name=modalProviderType]').val() == 'PATIENT' && statustr != "iEnabled")
	{
		$('input[name="coPayAmount"]').val(0).attr('readonly', true);
		$('select[name="modalSelect"]').attr('required', true);
		$('input[name="ORnumber"]').attr('required', true);
	}
	
	
	if (parseFloat(iAmount) <= 0 || statustr == "iEnabled" )  //|| ($('[data-toggle-group="CARD"][data-toggle-itemused !="0"] input[type="checkbox"]:checked').length > 0)
		parent.$('#btnsave').removeClass('hide');
	else
		parent.$('#btnsave').addClass('hide');
	
	
	 
	reCalAmount();

}

var summaryModal = new BootstrapDialog({
	message: function(dialog) {
		var $message = $('<div class="add-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
		var pageToLoad = dialog.getData('pageToLoad');
		$message.load(pageToLoad);
		return $message;
	},
	size: BootstrapDialog.SIZE_WIDE,
	type: BootstrapDialog.TYPE_WARNING,
	data: {
		'pageToLoad': "{{ '/cms/payment/pages/summary/'.$QID.'/edit?_ntoken='.csrf_token() }}"
	},
	animate: false,
	closable: false,
	buttons: [{
		cssClass: 'btn-default modal-closebtn',
		label: 'Close',
		action: function (modalRef) {
			modalRef.close();
		}
	}
	]
});

function formatCardNumber(input) {
  var inputValue = input.value.replace(/[^0-9a-zA-Z]/g, ''); // Remove characters other than numbers and letters

  if (inputValue.length > 16) {
    inputValue = inputValue.slice(0, 16); // Truncate to 16 characters
  }

  var formattedValue = '';
  for (var i = 0; i < inputValue.length; i += 4) {
    formattedValue += inputValue.slice(i, i + 4) + '-';
  }

  formattedValue = formattedValue.replace(/-$/, ''); // Remove the trailing hyphen

  input.value = formattedValue;
}

// Additional JavaScript to trigger formatCardNumber function on initial load
window.addEventListener('DOMContentLoaded', function () {
  var inputElement = document.getElementById('Hcard');
  if (inputElement) {
    formatCardNumber(inputElement);
  }
});


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
	$html += "<th>Company Name</th>";
	$html += "<th>Item Code</th>";
	$html += "<th>Doctor</th>";
	$html += "<th>Item Name</th>";
	$html += "<th>Type</th>";
	$html += "<th>Amount</th>";
	$html += "<th>Status</th>";
	$html += "<th>Balance</th>";
	$html += "</tr>";
	$html +="</thead><tbody>";

	var idata = []; 
	var idatas = {!! $itemData !!}; 
	
	
	if( typeof(idatas.length) === 'undefined')
		idata.push(idatas);
	else
		idata = idatas;
	
	var $dom = (idata.length >= 11)?"frtiS":"frti";
	
	
	console.log(idata);
	$html +="</tbody></table></div>";
	$('.table-queue').append($html);
	
	var table = $('#QueueListTable').DataTable({
		data			: idata,
		autoWidth		: false,
		deferRender		: true,
		createdRow		: function ( row, data, index ) { 
			$(row).attr('data-toggle-queueId', data.Id).attr('data-toggle-code', data.CodeItemPrice).attr('data-toggle-subgroup', data.SubGroup).attr('data-toggle-desc', data.DescriptionItemPrice).attr('data-toggle-itemtype', data.PriceGroupItemPrice).attr('data-toggle-amount', data.AmountItemPrice).attr('data-toggle-status', data.QueueStatus).attr('data-toggle-balance', data.AmountRemaining).attr('data-toggle-compasubgroup', data.CompaSubGroup).attr('data-toggle-companyname', data.Name).attr('data-toggle-allowdiscount', data.AllowDiscount); 
			$(row).attr('id', data.Id);
			 if($.inArray(data.Id, rows_selected) !== -1){ 
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		},
		
		columns			: [
		{ "data": null },
		{ "data": "Id", "orderDataType": "dom-checkbox" ,"render": function(data,type,row,meta) { if(row.Status == "201" || row.Status == "202"){ return '<input type="checkbox" data-toggle-statustr="'+row.Status+'"  data-toggle-itemused="'+row.ItemUsedItemPrice+'" data-toggle-group="'+row.GroupItemMaster+'" data-toggle-compasubgroup="'+row.CompaSubGroup+'" name="id[]" value="'+data+'">';}else{return "";} } },
		{ "data": "NameCompany", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "CodeItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "NameDoctor", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "DescriptionItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "PriceGroupItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "AmountItemPrice", "render": function(data,type,row,meta) { return '<div class="wrap-row text-right">'+data+'</div>'; } },
		{ "data": "QueueStatus", "render": function(data,type,row,meta) { return '<div class="wrap-row">'+data+'</div>'; } },
		{ "data": "AmountRemaining", "render": function(data,type,row,meta) { var nBalAmount  = (parseFloat(data) > 0)?'<div class="wrap-row text-right"><font style=\"color:blue;\">'+data+'</font></div>':'<div class="wrap-row text-right">'+data+'</div>'  ;return nBalAmount; } }],
		responsive		: { details: { type: 'column' } },
		columnDefs		: [
			{"data": null,className: 'control', orderable: false, targets: 0, "width":"10px",defaultContent: ""},
			{ data: null, targets: 1, orderable: false,"width":"10px" },
			{ data: null, targets: 2,className:'data-row', orderable: false,"width":"150px" },
			{ "data": "NameCompany",  targets: 3, orderable: false, "width":"300px" },
			{"data": "CodeItemPrice", targets: 4, orderable: false, "width":"50px" },
			{"data": "NameDoctor", targets: 5, orderable: false, "width":"300px" },
			{"data": "DescriptionItemPrice", targets: 6, orderable: false, "width":"60px" },
			{"data": "PriceGroupItemPrice", targets: 7, orderable: false, "width":"60px" },
			{"data": "AmountItemPrice", targets: 8, orderable: false, "width":"100px" },
			{"data": "QueueStatus", targets: 9,  orderable: false, "width":"120px" }
			//{"data": "AmountRemaining", targets: 10,  orderable: false, "width":"50px" }
		],
		'rowCallback': function(row, data, dataIndex){
			var itemId = $(row).attr('id');
			 if($.inArray(itemId, rows_selected) !== -1){
				$(row).find('input[type="checkbox"]').prop('checked', true);
				$(row).addClass('selected');
			 }
		  },
		order	 : [ 5, 'asc' ],
		dom:     $dom,
		scrollY: $(window).height()-378,
		fnInfoCallback: function( settings, start, end, max, total, pre ) {	
			return "<div class=\" col-xs-12 col-sm-12 col-md-12 col-lg-12\"> <div  class=\" col-xs-4 col-sm-4 col-md-4\"  >"+"Got a total of"+" "+total+" "+"entries "+"</div><div  class=\" col-xs-8 col-sm-8 col-md-8\"  >"+selectedInfo(true,idata);
		}
	});
	console.log('toggle-companyname');
	$('.dataTables_filter input').addClass('form-control search').attr({'type':'text','placeholder':'Search'});
	$('#QueueListTable').dataTable().rowGrouping({
            							iGroupingColumnIndex: 4,
										iExpandGroupOffset:-1
            							/*sGroupingColumnSortDirection: "asc",
            							iGroupingOrderByColumnIndex: 0*/
								});
		
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
	$('.Agent').addClass('hidden');
	//$('.sCard').attr('required', false);
	$('.hcard').addClass('hidden');
	$('.Card').attr('required', false);
      }
       // if(this.checked){
        // $row.addClass('selected');	
      //} else {
        // $row.removeClass('selected');
 	//	$('.hcard').addClass('hidden');
		
      //}
      
      //cal total on checked item 
	checkedReCalAmount(table);
	
	
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
	//$('#TransactionListTable').on('focusout','.card-number-input', function (e)

   //add 01/11/2024 
	var seniorAge = parseInt($('input[name="SiniorAge"]').val());	

    if (seniorAge < 60) {
        $('select[name="modalDiscountType"] option[value="6"]').hide(); 
    }
	// end added 01/11/2024

	$('select[name="modalDiscountType"]').on('change', function() {
		var expiryDateInput = $('input[name="ExpiryDatePWD"]');
		var pwd = $('input[name="pwdId"]').val($(this).val());
		console.log($('input[name="pwdId"]').val() == '8');
		if ($(this).find(':selected').data('req') == "Yes") {
			$('input[name="discountID"]').attr('disabled', false).attr('required', true);
			
			if ($(this).val() === '6') {
				$('input[name="discountID"]').val('{{$SeniorId}}');
				$('.OscaId').addClass('hidden');
			} else if ($(this).val() === '8') {
				$('input[name="discountID"]').val('{{$PWD}}');
				$('input[name="ExpiryDatePWD"]').val('{{$PwdExpiredDate}}');
				$('.OscaId').removeClass('hidden');
				
				$('#oscaPwdDateExpire').prop('required', true);
				$('#OSCA').on('input', function() {
					let oscaPwdInput = $(this);
					var value = oscaPwdInput.val();
					let oscaPwdDateExpire = $('#oscaPwdDateExpire');

					if (value === null || value === '') {
						oscaPwdDateExpire.prop('disabled', true);
						oscaPwdDateExpire.prop('required', false);
					}
					else
					{
						oscaPwdDateExpire.prop('disabled', false);
					}
				});
				var oscaPwdDate = $('input[name="OscaPwdDate"]').val();
				var oscaPwdId = $('input[name="discountID"]'); // Assuming this is the field for OscaPwdId
				var expirationDate = new Date(oscaPwdDate);
				var currentDate = new Date();
			
			if (currentDate > expirationDate) {
				BootstrapDialog.show({
					title: 'Expired OSCA/PWD ID',
					message: '<strong>OSCA/PWD Id</strong> is already <strong>Expired</strong>! <br> Please check validity and try again',
					size: BootstrapDialog.SIZE_WIDE,
					type: BootstrapDialog.TYPE_WARNING,
					closable: false,
					buttons: [{
						label: 'Close',
						action: function(dialog) {
							
							expiryDateInput.val('');
							dialog.close();
							setTimeout(function() {
							$('input[name="discountID"]').val('').focus();
							}, 100); 
						}
					}]
				});
			}
			} else {
				$('.OscaId').addClass('hidden');
				$('input[name="discountID"]').val('');
			}
		} else {
			// If "req" is not "Yes," hide the OscaId section
			$('.OscaId').addClass('hidden');
			$('input[name="discountID"]').attr('disabled', true).attr('required', false).val('');

			// Remove the 'required' attribute
			expiryDateInput.removeAttr('required');
		}

		if ($(this).find(':selected').data('per') != "0") {
			// reCalAmount();
			checkedReCalAmount(table);
		} else {
			// reCalAmount();
			checkedReCalAmount(table);
			$('input[name="lessAmount"]').val('0');
		}
	});
	function isLeapYear(year) {
    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
	}

	$('.datepickertwo').on('input', function() {
    let input = $(this);
    let value = input.val().replace(/\D/g, '').substring(0, 8);
	let currentYear = new Date().getFullYear();
		if (value.length === 8) {
			let month = parseInt(value.substring(0, 2));
			let day = parseInt(value.substring(2, 4));
			let year = parseInt(value.substring(4));

			month = Math.min(Math.max(month, 1), 12);
			day = Math.min(Math.max(day, 1), 31); // This allows 31 days for all months

			value = `${month.toString().padStart(2, '0')}/${day.toString().padStart(2, '0')}/${year}`;


		if (month === 2 && day === 29 && !isLeapYear(year)) {
            alert('Invalid date format: February 29 is only valid in a leap year!.');
			setTimeout(function () {
                input.val('');
            }, 0);
        }

		} else if (value.length > 2) {
			value = value.replace(/^(\d{2})(\d{0,2})/, '$1/$2');
		}
		input.val(value);
		
	});

    // $('#OSCA').on('input', function() {
    //     let oscaPwdInput = $(this);
    //     var value = oscaPwdInput.val();
    //     let oscaPwdDateExpire = $('#oscaPwdDateExpire');

    //     if (value === null || value === '') {
    //         oscaPwdDateExpire.prop('disabled', true);
    //         oscaPwdDateExpire.prop('required', false);
    //     } else {
    //        oscaPwdDateExpire.prop('disabled', false);
    //         oscaPwdDateExpire.prop('required', true);
    //     }
    // });



	$('.datepickertwo').datepicker({
    minDate: 0,
    dateFormat: 'mm/dd/yy',
	changeMonth: true, 
	changeYear: true, 
	yearRange: 'c-100:c+100',
    onClose: function(dateText, inst) {
        var selectedDate = $(this).datepicker('getDate');
        var currentDate = new Date();
		var input = $(this);
		var value = input.val();
        if (value && selectedDate < currentDate) {

            BootstrapDialog.show({
                title: 'Expired OSCA/PWD ID',
				message: '<strong>OSCA/PWD Id</strong> is already<strong> Expired</strong>! <br> Please Check validity and try again',
				size: BootstrapDialog.SIZE_WIDE,
				type: BootstrapDialog.TYPE_WARNING,
				closable: false,
                buttons: [{
                    label: 'Close',
                    action: function (dialog) {
                        input.val('');
                        dialog.close();
						input.focus();  
                    }
                }]
            });
			
        }
    }
	});
	
	var $modalBillType =  $('select[name="modalBillType"]').attr('disabled', true)
		.selectize({
			onChange: function(value) {
				if(value == 'PARTIALLY')
				{
					$('input[name="coPayAmount"]').attr('readonly', false);
				}
				else
				{
					$('input[name="coPayAmount"]').attr('readonly', true);
				}
				checkedReCalAmount(table);		
			}
		});
	
	
	
	
	var $billTo =  $('select[name="billTo"]').selectize();
	$('select[name="modalProviderType"]').selectize({
		onChange: function(value) {
			if(value == 'HMO' || value == 'Corporate')
				$('.divPaymentType').addClass('hide');
			else
				$('.divPaymentType').removeClass('hide');
			
			if(value == 'HMO')
			{
				var $billTo =  $('select[name="billTo"]').selectize();
				var control = $billTo[0].selectize;
				control.destroy();
				
				var $modalBillType =  $('select[name="modalBillType"]').selectize();
				var controlBillType = $modalBillType[0].selectize;
				controlBillType.destroy();
				$('select[name="modalBillType"]').attr('disabled', false).val('FULL').selectize({
					onChange: function(value) {
						if(value == 'PARTIALLY')
						{
							$('input[name="coPayAmount"]').attr('readonly', false);
						}
						else
						{
							$('input[name="coPayAmount"]').attr('readonly', true);
						}
						checkedReCalAmount(table);		
					}
				});
				
				
				
			
				$('select[name="billTo"]').attr('disabled', true).attr('required', false).val('');
				$('select[name="billTo"]').attr('disabled', false).attr('required', true).empty();
				parent.getData(
					"{{ '/cms/payment/pages/transactions/HMO' }}",
					{
						'_token': '{{ csrf_token() }}'
					},
					function(results){
						//control.clear();
						$('select[name="billTo"]').append($("<option></option>"));
						$.each(results, function(key,val){ 
							var selected =  false;
							$('select[name="billTo"]').append($("<option></option>").attr({"value":val.Id,"selected":selected}).text(val.Name)); 
							//control.addOption({value:val.Id,text:val.Name}); //option can be created manually or loaded using Ajax
							//control.addItem(val.Id);
						});
					var $billTo =  $('select[name="billTo"]').selectize();
					 //control.refreshState();
					}
				);
				
				$('input[name="hmoId"], input[name="cardName"]').attr('readonly', false).attr('required', true);
			}
			else if(value == 'Corporate')
			{
				var $modalBillType =  $('select[name="modalBillType"]').selectize();
				var controlBillType = $modalBillType[0].selectize;
				controlBillType.destroy();
				$('select[name="modalBillType"]').attr('disabled', false).val('FULL').selectize({
					onChange: function(value) {
						if(value == 'PARTIALLY')
						{
							$('input[name="coPayAmount"]').attr('readonly', false);
						}
						else
						{
							$('input[name="coPayAmount"]').attr('readonly', true);
						}
						checkedReCalAmount(table);		
					}
				});
			
			
			
				var $billTo =  $('select[name="billTo"]').selectize();
				var control = $billTo[0].selectize;
				control.destroy();
				//$('select[name="modalBillType"]').attr('disabled', false).val('FULL').selectize();
				$('select[name="billTo"]').attr('disabled', true).attr('required', false).val('');
				$('select[name="billTo"]').attr('disabled', false).attr('required', true).empty();
				parent.getData(
					"{{ '/cms/payment/pages/transactions/Corporate' }}",
					{
						'_token': '{{ csrf_token() }}'
					},
					function(results){
						
						//control.clear();
						$('select[name="billTo"]').append($("<option></option>"));
						$.each(results, function(key,val){ 
							var selected =  false;
							$('select[name="billTo"]').append($("<option></option>").attr({"value":val.Id,"selected":selected}).text(val.Name)); 
							//control.addOption({value:val.Id,text:val.Name}); //option can be created manually or loaded using Ajax
							//control.addItem(val.Id);
						});
					var $billTo =  $('select[name="billTo"]').selectize();
					 //control.refreshState();
					}
				);
				$('input[name="hmoId"], input[name="cardName"]').attr('readonly', true).attr('required', false);
			}
			else
			{
				var $modalBillType =  $('select[name="modalBillType"]').selectize();
				var controlBillType = $modalBillType[0].selectize;
				controlBillType.destroy();
				$('select[name="modalBillType"]').attr('disabled', true).val('FULL').selectize({
					onChange: function(value) {
						if(value == 'PARTIALLY')
						{
							$('input[name="coPayAmount"]').attr('readonly', false);
						}
						else
						{
							$('input[name="coPayAmount"]').attr('readonly', true);
						}
						checkedReCalAmount(table);		
					}
				});
			
			
				var $billTo =  $('select[name="billTo"]').selectize();
				var control = $billTo[0].selectize;
				control.destroy();
			
				$('select[name="billTo"]').attr('disabled', true).attr('required', false).val('');
				$('input[name="hmoId"], input[name="cardName"]').attr('readonly', true).attr('required', false);
			}
			checkedReCalAmount(table);
		}
	});
	
	
	
	//* Paymennt Type *//
	Selectize.define('select_remove_all_options', function(options) {
	    if (this.settings.mode === 'single') return;

	    var self = this;

	    self.setup = (function() {
		var original = self.setup;
		return function() {
		    original.apply(this, arguments);
		};
	    })();
	});
	

	//* Paymennt Type *//
	var modalSelectPaymentType = $('select[name="modalSelect"]').selectize({
		plugins: ['remove_button', 'select_remove_all_options']
		,onItemAdd: function (value) {
			modalSelectPaymentType[0].selectize.close();
			 if(value == 'Cash')
				$('input[name="cashAmount"]').focus();
			else if (value == 'GCash')
				$('input[name="gcashAmount"]').focus();
			else if (value == 'Credit')
				$('input[name="creditAmount"]').focus();
			else if(value == 'Cheque')
				$('input[name="chequeAmount"]').focus();
			else if(value == 'Online')
				$('input[name="onlineAmount"]').focus();
		}
	});

	$('.divSelectAdd').delegate('input[name="cashAmount"]', 'input', function(e){ 
		reCalAmount();
	});
	$('.divSelectAdd').delegate('input[name="gcashAmount"]', 'input', function(e){ 
		reCalAmount();
	});
	$('.divSelectAdd').delegate('input[name="creditAmount"]', 'input', function(e){ 
		reCalAmount();
	});
	$('.divSelectAdd').delegate('input[name="chequeAmount"]', 'input', function(e){ 
		reCalAmount();
	});
	$('.divSelectAdd').delegate('input[name="onlineAmount"]', 'input', function(e){ 
		reCalAmount();
	});

	
	
	$('.selectize-input').delegate('a', 'mousedown',  function(){
		 var value = $(this).closest('div').data('value');
		 if(value == 'Cash')
			$('.divCash').remove();
		else if (value == 'GCash')
			$('.divGcash').remove();
		else if (value == 'Credit')
			$('.divCredit').remove();
		else if(value == 'Cheque')
			$('.divCheque').remove();
		else if(value == 'Online')
			$('.divOnline').remove();	
		
		reCalAmount();
		event.preventDefault();
		event.stopPropagation();
		 
	});
	$('.selectize-dropdown-content').delegate('div[data-selectable]', 'mousedown', function(event) {
		    
		var value = $(this).data('value');
		if(value == 'Cash')
		{
			$iHtml  = 	"<div class=\"divCash\" >" +
						"<div class=\"row  row-md-flex-center\">" +
							"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
								"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Cash Pay:</div>" +
								"<div class=\"text-right col-xs-5 col-sm-5 col-md-5 col-lg-5\">" +
									"<span  style=\"color:red; font-size:20px;\">&#8369;</span>" +
								"</div>" +
								"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4 \" >" +
									"<input type=\"text\" class=\"form-control text-right cmsAmount\" name=\"cashAmount\" style=\"color:red; font-size:20px;\" placeholder=\"Cash Amount\" value=\"0\" required=\"required\">" +
								"</div>" +
							"</div>" +
						"</div>" +
					"</div>";
			$('.divSelectAdd').append($iHtml);
			$('input[name="cashAmount"]').inputFilter(function(value) {
			   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
			},"Only digits allowed");
			//$('input[name="cashAmount"]').on('input',function(e){
			//	reCalAmount();
			//});
		}
		else if (value == 'GCash')
		{
			$iHtml  =  "<div class=\"divGcash\" >" +
						"<div class=\"row row-md-flex-center\">" +
							"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
								"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Gcash Ref No. / Amount:</div>" +
								"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4\"  >" +
									"<input type=\"text\" class=\"form-control text-right\" name=\"gcashRefNo\"  placeholder=\"Gcash Ref. No.\"  required=\"required\">" +
								"</div>" +
								"<div class=\"text-right col-xs-1 col-sm-1 col-md-1 col-lg-1\">" +
									"<span  style=\"color:red; font-size:20px;\">&#8369;</span>" +
								"</div>" +
								"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4\"  >" +
									"<input type=\"text\" class=\"form-control text-right cmsAmount\" name=\"gcashAmount\" style=\"color:red; font-size:20px;\"  placeholder=\"Gcash Amount.\" value=\"0\"  required=\"required\">" +
								"</div>" +
							"</div>" +
						"</div>" +
					"</div>";
			$('.divSelectAdd').append($iHtml);
			$('input[name="gcashAmount"]').inputFilter(function(value) {
			   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
			},"Only digits allowed");
			//$('input[name="gcashAmount"]').on('input',function(e){
			//	reCalAmount();
			//});
		}
		else if(value == 'Credit')
		{
			
		
			$iHtml  = 	"<div class=\"divCredit\" >" +
						"<div class=\"row row-md-flex-center\">" +
							"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
								"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Card Bank Name:</div>" +
								"<div class=\"col-xs-9 col-sm-9 col-md-9 col-lg-9\"  >" +
									"<select name=\"modalCreditBank\" class=\"form-control rowview\" placeholder=\"Card Bank Name\"  required=\"required\" >" +
										"<option value=\"\">Select Card Bank Name</option> " +
									"</select>" +
								"</div>" +
							"</div>" +
						"</div>" +
						"<div class=\"row row-md-flex-center\">" +
							"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
								"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Ref. No. / Card Amount:</div>" +
								"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4 \" >" +
									"<input type=\"text\" class=\"form-control text-right\" name=\"creditRefNo\"  placeholder=\"Ref. No.\" required=\"required\" >" +
								"</div>" +
								"<div class=\"text-right col-xs-1 col-sm-1 col-md-1 col-lg-1\">" +
									"<span  style=\"color:red; font-size:20px;\"></span>" +
								"</div>" +
								"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4\"  >" +
									"<input type=\"text\" class=\"form-control text-right cmsAmount\" name=\"creditAmount\"  style=\"color:red; font-size:20px;\"   placeholder=\"Credit Amount Pay\" value=\"0\" required=\"required\" >" +
								"</div>" +
							"</div>" +
						"</div>" +
					"</div>";
			
			$('.divSelectAdd').append($iHtml);
			
			parent.getData(
				"{{ '/cms/payment/pages/bankname' }}",
				{
					'_token': '{{ csrf_token() }}'
				},
				function(results){
					
					//control.clear();
					$('select[name="modalCreditBank"]').append($("<option>Select Card Bank Name</option>"));
					$.each(results, function(key,val){ 
						var selected =  false;
						$('select[name="modalCreditBank"]').append($("<option></option>").attr({"value":val.Id,"selected":selected}).text(val.BankName)); 
						//control.addOption({value:val.Id,text:val.Name}); //option can be created manually or loaded using Ajax
						//control.addItem(val.Id);
					});
				var $modalCreditBank =  $('select[name="modalCreditBank"]').selectize();
				 //control.refreshState();
				}
			);
			
			
			
			$('input[name="creditAmount"]').inputFilter(function(value) {
			   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
			},"Only digits allowed");
			//$('input[name="creditAmount"]').on('input',function(e){
			//	reCalAmount();
			//});
		}
		else if(value == 'Cheque')
		{
			$iHtml  = 	"<div class=\"divCheque\" >" +
					"<div class=\"row row-md-flex-center\">" +
						"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
							"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Bank Name:</div>" +
							"<div class=\"col-xs-9 col-sm-9 col-md-9 col-lg-9\"  >" +
								"<select name=\"modalChequeBank\" class=\"form-control rowview\" placeholder=\"Bank Name\" required=\"required\"  >" +
									"<option value=\"\">Select Bank Name</option> " +
								"</select>" +
							"</div>" +
						"</div>" +
					"</div>" +
					"<div class=\"row row-md-flex-center\">" +
						"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
							"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Cheque No. / Cheque Amount:</div>" +
							"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4 \" >" +
								"<input type=\"text\" class=\"form-control text-right\" name=\"chequeRefNo\"  placeholder=\"Ref. No.\" required=\"required\" >" +
							"</div>" +
							"<div class=\"text-right col-xs-1 col-sm-1 col-md-1 col-lg-1\">" +
								"<span  style=\"color:red; font-size:20px;\"></span>" +
							"</div>" +
							"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4\"  >" +
								"<input type=\"text\" class=\"form-control text-right cmsAmount\" name=\"chequeAmount\"  style=\"color:red; font-size:20px;\"   placeholder=\"Cheque Amount Pay\" value=\"0\"  required=\"required\">" +
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>";
			
			$('.divSelectAdd').append($iHtml);
			parent.getData(
				"{{ '/cms/payment/pages/bankname' }}",
				{
					'_token': '{{ csrf_token() }}'
				},
				function(results){
					
					//control.clear();
					$('select[name="modalChequeBank"]').append($("<option>Select Card Bank Name</option>"));
					$.each(results, function(key,val){ 
						var selected =  false;
						$('select[name="modalChequeBank"]').append($("<option></option>").attr({"value":val.Id,"selected":selected}).text(val.BankName)); 
						//control.addOption({value:val.Id,text:val.Name}); //option can be created manually or loaded using Ajax
						//control.addItem(val.Id);
					});
				var $modalChequeBank =  $('select[name="modalChequeBank"]').selectize();
				 //control.refreshState();
				}
			);
			
			$('input[name="chequeAmount"]').inputFilter(function(value) {
			   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
			},"Only digits allowed");
			//$('input[name="chequeAmount"]').on('input',function(e){
			//	reCalAmount();
			//});
		}
		else if(value == 'Online')
		{
			$iHtml  = 	"<div class=\"divOnline\" >" +
					"<div class=\"row row-md-flex-center\">" +
						"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
							"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Bank Name:</div>" +
							"<div class=\"col-xs-9 col-sm-9 col-md-9 col-lg-9\"  >" +
								"<select name=\"modalOnlineBank\" class=\"form-control rowview\" placeholder=\"Bank Name\" required=\"required\"  >" +
									"<option value=\"\">Select Bank Name</option> " +
								"</select>" +
							"</div>" +
						"</div>" +
					"</div>" +
					"<div class=\"row row-md-flex-center\">" +
						"<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">" +
							"<div class=\"bg-dark text-light text-right col-xs-3 col-sm-3 col-md-3 col-lg-3\"  style=\"padding: 6px 12px;\" >Ref No. / Amount:</div>" +
							"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4 \" >" +
								"<input type=\"text\" class=\"form-control text-right\" name=\"onlineRefNo\"  placeholder=\"Ref. No.\" required=\"required\" >" +
							"</div>" +
							"<div class=\"text-right col-xs-1 col-sm-1 col-md-1 col-lg-1\">" +
								"<span  style=\"color:red; font-size:20px;\"></span>" +
							"</div>" +
							"<div class=\"col-xs-4 col-sm-4 col-md-4 col-lg-4\"  >" +
								"<input type=\"text\" class=\"form-control text-right cmsAmount\" name=\"onlineAmount\"  style=\"color:red; font-size:20px;\"   placeholder=\"Online Amount Pay\" value=\"0\"  required=\"required\">" +
							"</div>" +
						"</div>" +
					"</div>" +
				"</div>";
			
			$('.divSelectAdd').append($iHtml);
			parent.getData(
				"{{ '/cms/payment/pages/bankname' }}",
				{
					'_token': '{{ csrf_token() }}'
				},
				function(results){
					
					//control.clear();
					$('select[name="modalOnlineBank"]').append($("<option>Select Bank Name</option>"));
					$.each(results, function(key,val){ 
						var selected =  false;
						$('select[name="modalOnlineBank"]').append($("<option></option>").attr({"value":val.Id,"selected":selected}).text(val.BankName)); 
						//control.addOption({value:val.Id,text:val.Name}); //option can be created manually or loaded using Ajax
						//control.addItem(val.Id);
					});
				var $modalOnlineBank =  $('select[name="modalOnlineBank"]').selectize();
				 //control.refreshState();
				}
			);
			
			$('input[name="onlineAmount"]').inputFilter(function(value) {
			   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
			},"Only digits allowed");
			//$('input[name="chequeAmount"]').on('input',function(e){
			//	reCalAmount();
			//});
		}
		event.preventDefault();
		event.stopPropagation();
		
	});
	var cards = [];
	var datas = {!! $agents !!}; 
	
	if( typeof(datas.length) === 'undefined')
	cards.push(datas);
	else
	cards = datas;
	var agent = $('select[name="Agent"]').selectize({  
		sortField: 'EmployeeName',
		searchField: ['EmployeeID','EmployeeName'],
		options : cards,
		render: {
			option: function(item, escape) {
				return '<div>' +
					'<span class="code">' +
						'<span class="name">' + escape(item.EmployeeID) + '</span>' +
					'</span>' +
					'<span class="description">'+'<small>' + escape(item.EmployeeName) + '</small>'+'</span>' +
				'</div>';
			}
		},
		onChange: function(value){
			if(!value.length)
			{
			$('input[name=_agent]').val('');
			return;
			}
			$('input[name=_agent]').val($('select[name=Agent] option:selected').text());
			console.log("Selected Agent Value: " + value);
		}
	});
	
	var agents = agent[0].selectize;

	

	$('input[name="coPayAmount"]')
	.inputFilter(function(value) {
	   return /^[0-9]*\.?[ 0-9]*$/.test(value);    // Allow digits only, using a RegExp
	},"Only digits allowed")
	.on('input', function(){
		reCalAmount();
	});
	
	$('.summary').on('click',function(e){
		summaryModal.setTitle("Payment");
		summaryModal.setData("pageId",'id');
		summaryModal.realize();
		summaryModal.open();
		e.preventDefault();
	});
	
	

	
	parent.waitingDialog.hide();
	
	
	
	
	
	
});
</script>


