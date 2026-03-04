<form id="queueTransactionRemoveModalForm" class="form-horizontal" role="form" method="POST" action=""  autocomplete="off">
<input type="hidden" name="_selected" value="">
<input type="hidden" name="_method" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="TransactionRemove">
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Item Code<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="ItemCode" value="{{ $datas[0]->CodeItemPrice }}"  readonly="readonly" >
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Item Name<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="ItemName" value="{{ $datas[0]->DescriptionItemPrice }}"  readonly="readonly" >
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Item Status</label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="ItemStatus" value="{{ $datas[0]->QueueStatus }}"  readonly="readonly" >
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Input By</label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="text-transform: capitalize;" class="form-control" name="ItemInputBy" value="{{ $datas[0]->InputBy }}"  readonly="readonly" >
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold ">Reason<font style="color:red;">*</font></label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="text-transform: capitalize;" class="form-control" placeholder="Reason" name="modalReason" value="" required="required" >
		</div>
	</div>
	<div class="row form-group row-md-flex-center">
		<div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
			<label class="bold "><font style="color:red;">NOTE</font></label>
		</div>
		<div class="col-sm-10 col-md-10">
			<input type="text" style="color:red;" class="form-control" disabled="disabled" value="Re-Billing required for any removed / deleted procedure." >
		</div>
	</div>
</div>
</form>

<script>
$(document).ready(function(e) 
{
	
	

});
</script>