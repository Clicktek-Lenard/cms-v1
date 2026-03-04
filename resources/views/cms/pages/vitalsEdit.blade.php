<form id="vitalsEditModalForm" class="form-horizontal" role="form" method="POST" action="{{ $postLink }}"  autocomplete="off">
<input type="hidden" name="_method" value="PUT">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="modal-cms-header">
	<div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">BP</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="BP" value="" placeholder="Blood Pressure" >
        </div>
        <div class="col-sm-1 col-md-1 text-right ">
            <label class="bold ">Temp.</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="Temp" value="" placeholder="Temperature">
        </div>
    </div>
	<div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">Height</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="Height" value="" placeholder="Height" >
        </div>
        <div class="col-sm-1 col-md-1  text-right ">
            <label class="bold ">Weight</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="Weight" value="" placeholder="Weight">
        </div>
    </div>
	<div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">PRPM</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="PRPM" value="" placeholder="Pulse Rate Per Min." >
        </div>
        <div class="col-sm-1 col-md-1  text-right  ">
            <label class="bold ">RRPM</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="RRPM" value="" placeholder="Respiratory Rate Per Min.">
        </div>
    </div>
	<div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">BB</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="BB" value="" placeholder="Body Built" >
        </div>
        <div class="col-sm-1 col-md-1 text-right  ">
            <label class="bold ">BMI</label>
        </div>
        <div class="col-sm-4 col-md-4">
           <input type="text" class="form-control" name="BMI" value="" placeholder="Body Mass Index">
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">BSA</label>
        </div>
        <div class="col-sm-9 col-md-9">
           <input type="text" class="form-control" name="BSA" value="" placeholder="Body Surface Area" >
        </div>
    </div>
    <div class="row form-group row-md-flex-center">
        <div class="col-sm-2 col-md-2 text-right  ">
            <label class="bold ">Notes</label>
        </div>
        <div class="col-sm-9 col-md-9">
           <textarea class="form-control" name="Notes" placeholder="Notes"></textarea>
        </div>
    </div>
</div>
</form>
