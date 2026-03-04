<form id="cancelTransaction" class="form-horizontal" role="form" method="POST" action="{{$postLink}}"  autocomplete="off">
    <input type="hidden" name="_selected" value="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Reason<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" class="form-control" name="reason" required="required" placeholder="Reason for amendment" >
    </div>
    <div class="col-sm-10 col-md-1">
    	<input type="text" class="form-control hidden" >
    </div>
</div>


<div class="modal-cms-header">
    <div class="col-menu-15 table-items">
    </div>
</div>
<script>
$(document).ready(function(e) {
    
});
    
</script>