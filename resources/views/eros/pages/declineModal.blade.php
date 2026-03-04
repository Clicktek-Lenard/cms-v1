<form id="declineModal" class="form-horizontal" role="form" autocomplete="off">                   <!--pcp v2-->
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="doctor_id" value="{{ $doctorsDatas[0]->Id }}">
    <input type="hidden" name="doctor_status" value="{{ $doctorsDatas[0]->Status }}">

<div class="row form-group row-md-flex-center">
    <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
        <label class="bold ">Disapprove Reasons<font style="color:red;">*</font></label>
    </div>
    <div class="col-sm-10 col-md-10">
    	<input type="text" class="form-control" name="reasons" required="required" placeholder="Reason for Declining" >
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
    // $('input[name="reason"]').keypress(function(event) {
    //     if (event.which === 13) {             
    //         event.preventDefault();            
    //      $('#btnSave').click(); 
    //         console.log('Enter key pressed');
    //     }
    // });
    // $('input[name="reason"]').focus();
});
    
</script>