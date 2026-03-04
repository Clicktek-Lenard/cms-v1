<style>
@media (min-width: 767px){
	.m-top100 {
		margin-top: -100px !important;
	}
	.m-top170 {
		margin-top: -170px !important;
	}
	.m-top90 {
		margin-top: -90px !important;
	}
	.m-top20 {
		margin-top: -20px !important;
	}
}
.webcam { cursor:pointer; }
</style>
<form id="physicianEnrollmentModal" class="form-horizontal"  role="form" method="POST" action="{{ $postLink }}"  autocomplete="off">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">   

    <div class="modal-cms-header">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Last Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="lastname" placeholder="Last Name" required="required">
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">First Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="firstname" placeholder="First Name" required="required">
                </div>
            </div>			
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Middle Name<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="middlename" placeholder="Middle Name" >
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Display Name<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" style="text-transform: uppercase;" name="fullname" placeholder="Display Name" disabled >
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">PRC No.<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text"  maxlength="7" pattern="\d*" id= "prcId" class="form-control" name="prcno" placeholder="PRC No." required="required" >
                </div>
            </div>    
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Specialization<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <!-- <input type="text" class="form-control" style="text-transform: uppercase;" name="description" placeholder="Specialization"> -->
                    <select class="form-control" name="description" placeholder="DESCRIPTION" required="required">
                        <option value="">CHOOSE SPECIALIZATION</option>
                        <option value="ALLERGOLOGY">ALLERGOLOGY</option>
                        <option value="ANESTHESIOLOGY">ANESTHESIOLOGY</option>
                        <option value="CARDIOLOGY">CARDIOLOGY</option>
                        <option value="DENTAL MEDICINE">DENTAL MEDICINE</option>
                        <option value="DERMATOLOGY">DERMATOLOGY</option>
                        <option value="DIABETOLOGY">DIABETOLOGY</option>
                        <option value="ENDOCRINOLOGY">ENDOCRINOLOGY</option>
                        <option value="ENT">ENT</option>
                        <option value="FAMILY MEDICINE">FAMILY MEDICINE</option>
                        <option value="GASTROENTEROLOGY">GASTROENTEROLOGY</option>
                        <option value="GENERAL PRACTICE">GENERAL PRACTICE</option>
                        <option value="GENERAL SURGERY">GENERAL SURGERY</option>
                        <option value="INTERNAL MEDICINE">INTERNAL MEDICINE</option>
                        <option value="NEPHROLOGY">NEPHROLOGY</option>
                        <option value="NEUROLOGY">NEUROLOGY</option>
                        <option value="NUCLEAR MEDICINE">NUCLEAR MEDICINE</option>
                        <option value="OB-GYNECOLOGY">OB-GYNECOLOGY</option>
                        <option value="OB-SONOLOGY">OB-SONOLOGY</option>
                        <option value="OCCUPATIONAL MEDICINE">OCCUPATIONAL MEDICINE</option>
                        <option value="ONCO-SURGERY">ONCO-SURGERY</option>
                        <option value="OPHTHALMOLOGY">OPHTHALMOLOGY</option>
                        <option value="OPTOMETRY">OPTOMETRY</option>
                        <option value="ORTHOPEDICS">ORTHOPEDICS</option>
                        <option value="PATHOLOGY">PATHOLOGY</option>
                        <option value="PEDIATRICS">PEDIATRICS</option>
                        <option value="PLASTIC SURGERY">PLASTIC SURGERY</option>
                        <option value="PSYCHIATRY">PSYCHIATRY</option>
                        <option value="PULMONOLOGY">PULMONOLOGY</option>
                        <option value="RADIO-SONOLOGY">RADIO-SONOLOGY</option>
                        <option value="RADIOLOGY">RADIOLOGY</option>
                        <option value="REHABILITATION MEDICINE">REHABILITATION MEDICINE</option>
                        <option value="RHEUMATOLOGY">RHEUMATOLOGY</option>
                        <option value="THORACO-VASCULAR SURGERY">THORACO-VASCULAR SURGERY</option>
                        <option value="UROLOGY">UROLOGY</option>
                    </select>
                </div>
            </div>       
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 image_results" style="padding-left: 4px;">
            <div class="d-flex justify-content-between align-items-center">
                <input type="hidden" name="myimage" class="image-tag">
                <img src="{{ asset('images/RP_Prescription.png') }}" style="width: 430px; height: 330px; object-fit: contain;">
            </div>
            <button type="button" class="btn btn-primary scanbtn pull-right" style="margin-top: 10px;">Open Camera</button>
        </div>
    </div>
</form>
<div class="row">
    <div class="table-item col-md-12"></div>
</div>
<script src="{{ asset('/js/webcam.js') }}"></script>
<script>
$(document).ready(function(e)
{

    document.getElementById('prcId').addEventListener('blur', function (){ //added 121924 for limit prc input in 7 and starts input in zero
        var prcInput = this.value;

        if (prcInput){
            this.value = prcInput.padStart(7, '0');
        }
    });
    
	var physicianWebcam = new BootstrapDialog({
		message: function(dialog) {
			var $message = $('<div class="physicianWebcam-modal"><div style="text-align: center; color:blue;"><B>Loading...</B></div></div>');
			var pageToLoad = dialog.getData('pageToLoad');
			$message.load(pageToLoad);
			return $message;
		},
		size: BootstrapDialog.SIZE_WIDE,
		type: BootstrapDialog.TYPE_INFO,
		data: {
			'pageToLoad': "{{ '/webcam' }}"
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
			id: 'btnsave',
			cssClass: 'btn-primary actionbtn',
			label: 'Submit',
			action: function(modalRef) {
                var newImageUrl = $('input[name="image"]').val(); 
                $('input[name="myimage"]').val(newImageUrl); 
                $('div.image_results').attr('src', newImageUrl);
                $('div.image_results img').attr('src', newImageUrl);
                modalRef.close();
            }
		}]
	});

	$('.scanbtn').on('click', function(){
		physicianWebcam.setTitle("Camera");
		physicianWebcam.realize();
		physicianWebcam.open();
		//e.preventDefault();			
	});


	
});
</script>