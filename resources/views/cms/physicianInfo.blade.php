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
<form id="physicianTableModal" class="form-horizontal"  role="form" autocomplete="off">
    <input type="hidden" name="_selected" value="{{ $physicianDatas[0]->Id }}">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">   

    <div class="modal-cms-header">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-left: 4px;">
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Last Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required="required" value="{{ $physicianDatas[0]->LastName }}">
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">First Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required="required" value="{{ $physicianDatas[0]->FirstName }}">
                </div>
            </div>			
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Middle Name<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="middlename" placeholder="Middle Name" value="{{ $physicianDatas[0]->MiddleName }}" >
                </div>
            </div>					
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Display Name<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" name="fullname" placeholder="Display Name" required="required" value="{{ $physicianDatas[0]->FullName }}">
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">PRC No.<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <input type="text" class="form-control" id= "prcId" name="prcno" placeholder="PRC No." required="required" value="{{ $physicianDatas[0]->PRCNo }}">
                </div>
            </div>    
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Specialization<font style="color:red;"></font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <select class="form-control" name="description" placeholder="Description" required="required">
                        <option value="">CHOOSE SPECIALIZATION</option>
                        <option value="ALLERGOLOGY" {{ $physicianDatas[0]->Description == 'ALLERGOLOGY' ? 'selected' : '' }}>ALLERGOLOGY</option>
                        <option value="ANESTHESIOLOGY" {{ $physicianDatas[0]->Description == 'ANESTHESIOLOGY' ? 'selected' : '' }}>ANESTHESIOLOGY</option>
                        <option value="CARDIOLOGY" {{ $physicianDatas[0]->Description == 'CARDIOLOGY' ? 'selected' : '' }}>CARDIOLOGY</option>
                        <option value="DENTAL MEDICINE" {{ $physicianDatas[0]->Description == 'DENTAL MEDICINE' ? 'selected' : '' }}>DENTAL MEDICINE</option>
                        <option value="DERMATOLOGY" {{ $physicianDatas[0]->Description == 'DERMATOLOGY' ? 'selected' : '' }}>DERMATOLOGY</option>
                        <option value="DIABETOLOGY" {{ $physicianDatas[0]->Description == 'DIABETOLOGY' ? 'selected' : '' }}>DIABETOLOGY</option>
                        <option value="ENDOCRINOLOGY" {{ $physicianDatas[0]->Description == 'ENDOCRINOLOGY' ? 'selected' : '' }}>ENDOCRINOLOGY</option>
                        <option value="ENT" {{ $physicianDatas[0]->Description == 'ENT' ? 'selected' : '' }}>ENT</option>
                        <option value="FAMILY MEDICINE" {{ $physicianDatas[0]->Description == 'FAMILY MEDICINE' ? 'selected' : '' }}>FAMILY MEDICINE</option>
                        <option value="GASTROENTEROLOGY" {{ $physicianDatas[0]->Description == 'GASTROENTEROLOGY' ? 'selected' : '' }}>GASTROENTEROLOGY</option>
                        <option value="GENERAL PRACTICE" {{ $physicianDatas[0]->Description == 'GENERAL PRACTICE' ? 'selected' : '' }}>GENERAL PRACTICE</option>
                        <option value="GENERAL SURGERY" {{ $physicianDatas[0]->Description == 'GENERAL SURGERY' ? 'selected' : '' }}>GENERAL SURGERY</option>
                        <option value="INTERNAL MEDICINE" {{ $physicianDatas[0]->Description == 'INTERNAL MEDICINE' ? 'selected' : '' }}>INTERNAL MEDICINE</option>
                        <option value="NEPHROLOGY" {{ $physicianDatas[0]->Description == 'NEPHROLOGY' ? 'selected' : '' }}>NEPHROLOGY</option>
                        <option value="NEUROLOGY" {{ $physicianDatas[0]->Description == 'NEUROLOGY' ? 'selected' : '' }}>NEUROLOGY</option>
                        <option value="NUCLEAR MEDICINE" {{ $physicianDatas[0]->Description == 'NUCLEAR MEDICINE' ? 'selected' : '' }}>NUCLEAR MEDICINE</option>
                        <option value="OB-GYNECOLOGY" {{ $physicianDatas[0]->Description == 'OB-GYNECOLOGY' ? 'selected' : '' }}>OB-GYNECOLOGY</option>
                        <option value="OB-SONOLOGY" {{ $physicianDatas[0]->Description == 'OB-SONOLOGY' ? 'selected' : '' }}>OB-SONOLOGY</option>
                        <option value="OCCUPATIONAL MEDICINE" {{ $physicianDatas[0]->Description == 'OCCUPATIONAL MEDICINE' ? 'selected' : '' }}>OCCUPATIONAL MEDICINE</option>
                        <option value="ONCO-SURGERY" {{ $physicianDatas[0]->Description == 'ONCO-SURGERY' ? 'selected' : '' }}>ONCO-SURGERY</option>
                        <option value="OPHTHALMOLOGY" {{ $physicianDatas[0]->Description == 'OPHTHALMOLOGY' ? 'selected' : '' }}>OPHTHALMOLOGY</option>
                        <option value="OPTOMETRY" {{ $physicianDatas[0]->Description == 'OPTOMETRY' ? 'selected' : '' }}>OPTOMETRY</option>
                        <option value="PATHOLOGY" {{ $physicianDatas[0]->Description == 'PATHOLOGY' ? 'selected' : '' }}>PATHOLOGY</option>
                        <option value="ORTHOPEDICS" {{ $physicianDatas[0]->Description == 'ORTHOPEDICS' ? 'selected' : '' }}>ORTHOPEDICS</option>
                        <option value="PEDIATRICS" {{ $physicianDatas[0]->Description == 'PEDIATRICS' ? 'selected' : '' }}>PEDIATRICS</option>
                        <option value="PLASTIC SURGERY" {{ $physicianDatas[0]->Description == 'PLASTIC SURGERY' ? 'selected' : '' }}>PLASTIC SURGERY</option>
                        <option value="PSYCHIATRY" {{ $physicianDatas[0]->Description == 'PSYCHIATRY' ? 'selected' : '' }}>PSYCHIATRY</option>
                        <option value="PULMONOLOGY" {{ $physicianDatas[0]->Description == 'PULMONOLOGY' ? 'selected' : '' }}>PULMONOLOGY</option>
                        <option value="RADIO-SONOLOGY" {{ $physicianDatas[0]->Description == 'RADIO-SONOLOGY' ? 'selected' : '' }}>RADIO-SONOLOGY</option>
                        <option value="RADIOLOGY" {{ $physicianDatas[0]->Description == 'RADIOLOGY' ? 'selected' : '' }}>RADIOLOGY</option>
                        <option value="REHABILITATION MEDICINE" {{ $physicianDatas[0]->Description == 'REHABILITATION MEDICINE' ? 'selected' : '' }}>REHABILITATION MEDICINE</option>
                        <option value="RHEUMATOLOGY" {{ $physicianDatas[0]->Description == 'RHEUMATOLOGY' ? 'selected' : '' }}>RHEUMATOLOGY</option>
                        <option value="THORACO-VASCULAR SURGERY" {{ $physicianDatas[0]->Description == 'THORACO-VASCULAR SURGERY' ? 'selected' : '' }}>THORACO-VASCULAR SURGERY</option>
                        <option value="UROLOGY" {{ $physicianDatas[0]->Description == 'UROLOGY' ? 'selected' : '' }}>UROLOGY</option>
                    </select>
                </div>
            </div>
            <div class="row form-group row-md-flex-center">
                <div class="col-sm-3 col-md-3 pad-0-md text-right-md  ">
                    <label class="bold ">Status<font style="color:red;">*</font></label>
                </div>
                <div class="col-sm-12 col-md-10">
                    <select class="form-control" name="status" placeholder="Status" required="required" disabled >
                        <option value="Active" @if($physicianDatas[0]->Status == 'Active') selected @endif>Active</option>
                        <option value="Inactive" @if($physicianDatas[0]->Status == 'Inactive') selected @endif>Inactive</option>
                        <option value="RP - For Approval" @if($physicianDatas[0]->Status == 'RP - For Approval') selected @endif>RP - For Approval</option>
                        <option value="RP - For Revision" @if($physicianDatas[0]->Status == 'RP - For Revision') selected @endif>RP - For Revision</option>
                        <option value="RP - Leads" @if($physicianDatas[0]->Status == 'RP - Leads') selected @endif>RP - Leads</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 image_holder" style="padding-left: 4px;">
            <div class="d-flex justify-content-between align-items-center">
                
                <input type="hidden" name="myimage" class="image-tag" >

                @if(!empty($physicianDatas[0]->Prescription_Link) && file_exists(public_path('uploads/PhysicianPrescription/' . $physicianDatas[0]->Prescription_Link)))
                    <!-- Display the actual image if Prescription_Link exists and the file is present -->
                    <img src="{{ asset('uploads/PhysicianPrescription/' . $physicianDatas[0]->Prescription_Link) }}" alt="Captured Rx" class="img-fluid mt-2">
                @else
                    <img src="{{ asset('images/RP_Prescription.png') }}" style="width: 430px; height: 330px; object-fit: contain;">
                @endif
                <button type="button" class="btn btn-primary scanbtn pull-right" style="margin-top: 10px;">Open Camera</button>
            </div>
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
                $('div.image_holder img').attr('src', newImageUrl); 
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

    //added 01162025 for limit prc input in 7 and starts input in zero
    document.getElementById('prcId').addEventListener('blur', function (){ 
        var prcInput = this.value;

        if (prcInput){
            this.value = prcInput.padStart(7, '0');
        }
    });
	
});
</script>