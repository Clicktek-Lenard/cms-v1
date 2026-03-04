<style>
    .modal-cms-header {
        display: flex;
        align-items: center; 
        margin-bottom: 15px; 
    }
    .form-control {
        width: 100%; 
    } 
    .text-right-md {
        text-align: right; 
    }
    
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .modal-cms-header p {
        color: red;  /* Color of the text */
        animation: blink 1s infinite;  /* Blink every 1 second */
    }
</style>
<!-- test -->

<form id="physicianReasonModal" class="form-horizontal" role="form" autocomplete="off">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="_selected" value="{{ $physicianViewDatas[0]->Id }}">
        
    <div class="modal-cms-header">
        <div class="col-sm-2 col-md-2 pad-0-md text-right-md  ">
            <label class="bold ">Reasons<font style="color:red;">*</font></label>
        </div> 
        <div class="col-sm-12 col-md-10">
            <select id="group" class="form-control" name="reason[]" multiple placeholder="Reason">
                <option value="WRONG NAME">WRONG NAME</option>
                <option value="WRONG PRC NO.">WRONG PRC NO.</option>
                <option value="WRONG SPECIALIZATION">WRONG SPECIALIZATION</option>
                <option value="WRONG PRESCRIPTION">WRONG PRESCRIPTION</option>
            </select>
            <p>Note: Hold down (Ctrl) key to choose multiple options for the Reason.</p>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    
});
</script>
