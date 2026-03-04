<?php



namespace App\Http\Controllers\hclab;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\hclab\HclabDB;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

class HclabBCKController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

   
    public function index()
    {
	
	$pdfData = HclabDB::getPDFData();
        return view('hclab.pdfList', ['pdfData' => json_encode($pdfData)]);
    }
    
    public function show($id)
    {

	$adapter = new \League\Flysystem\AwsS3V3\AwsS3V3Adapter(
		new \Aws\S3\S3Client([
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            'region' => 'ap-southeast-1',
            'version' => 'latest',

        ]),
		'nwdi-pdf-bucket',
		'',
		new \League\Flysystem\AwsS3V3\PortableVisibilityConverter(
			\League\Flysystem\Visibility::PRIVATE
		)
	);
	
	$s3Fs = new \League\Flysystem\Filesystem($adapter);
    
	
	$pdfData = HclabDB::getPDFData($id);
    
	
	$fileName = $pdfData[0]->trans_no . '_' . date('Ymd', strtotime($pdfData[0]->order_date)) . '_' . $pdfData[0]->patient_id . '.pdf';	
	$fullPath =   date('Y-m-d', strtotime($pdfData[0]->order_date)) . '/' . $fileName;
	
	if (!$s3Fs->fileExists($fullPath)) {
		echo '<div style="background-color: #10069f; padding: 20px 30px; margin: 200px auto; width: 400px;"><p style="color: #fff; margin: auto; font-family: Roboto, sans-serif; text-align: center; font-weight: bold;">Test still in process..</p></div>';
		exit;
	}
	
	  $disposition =  'inline';

	    header('Content-Type: application/pdf');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-disposition: ' . $disposition . '; filename="' . $fileName . '"');
	    echo $s3Fs->read($fullPath);	
	    exit;
    
	
    }
    
    // public function HclabORDH()
    // {
	// $datas = DB::connection('oraPRODh')->table('ORD_HDR')->offset(200000)->limit(10000)->get(array('*'));
	// echo "Processing";
	// foreach($datas as $data)
	// {
	// 	echo " -> ";
	// 	echo DB::connection('hclab')->table('OrderHDR')->insertGetId([
	// 	'OH_TNO'			=> $data->oh_tno
	// 	,'OH_TRX_DT'		=> $data->oh_trx_dt
	// 	,'OH_TYPE'		=> $data->oh_type
	// 	,'OH_ONO'			=> $data->oh_ono
	// 	,'OH_SNO'			=> $data->oh_sno
	// 	,'OH_PID'			=> $data->oh_pid
	// 	,'OH_APID'		=> $data->oh_apid
	// 	,'OH_PTYPE'		=> $data->oh_ptype
	// 	,'OH_FIRST_NAME'	=> $data->oh_first_name
	// 	,'OH_LAST_NAME'	=> $data->oh_last_name
	// 	,'OH_PATADDR1'	=> $data->oh_pataddr1
	// 	,'OH_PATADDR2'	=> $data->oh_pataddr2
	// 	,'OH_PATADDR3'	=> $data->oh_pataddr3
	// 	,'OH_PATADDR4'	=> $data->oh_pataddr4
	// 	,'OH_ETHNIC_CODE'	=> $data->oh_ethnic_code
	// 	,'OH_SEX'			=> $data->oh_sex
	// 	,'OH_BOD'			=> $data->oh_bod
	// 	,'OH_AGE_YY'		=> $data->oh_age_yy
	// 	,'OH_AGE_MM'		=> $data->oh_age_mm
	// 	,'OH_AGE_DD'		=> $data->oh_age_dd
	// 	,'OH_VISITNO'		=> $data->oh_visitno
	// 	,'OH_DCODE'		=> $data->oh_dcode
	// 	,'OH_DNAME'		=> $data->oh_dname
	// 	,'OH_CLINIC_CODE'	=> $data->oh_clinic_code
	// 	,'OH_FT_CODE'		=> $data->oh_ft_code
	// 	,'OH_PRI'			=> $data->oh_pri
	// 	,'OH_DIAG1'		=> $data->oh_diag1
	// 	,'OH_DIAG2'		=> $data->oh_diag2
	// 	,'OH_PDGRP'		=> $data->oh_pdgrp
	// 	,'OH_ADMS'		=> $data->oh_adms
	// 	,'OH_WARD_CODE'	=> $data->oh_ward_code
	// 	,'OH_PCMT'		=> $data->oh_pcmt
	// 	,'OH_LS_CODE'		=> $data->oh_ls_code
	// 	,'OH_WS_CODE'		=> $data->oh_ws_code
	// 	,'OH_ORD_TYPE'	=> $data->oh_ord_type
	// 	,'OH_ORD_STATUS'	=> $data->oh_ord_status
	// 	,'OH_COMPLETED_DT'=> $data->oh_completed_dt
	// 	,'OH_POLL_FLAG'	=> $data->oh_poll_flag
	// 	,'OH_PRINT_FLAG'	=> $data->oh_print_flag
	// 	,'OH_LAST_PRINT'	=> $data->oh_last_print
	// 	,'OH_GPH_FLAG'	=> $data->oh_gph_flag
	// 	,'OH_REC_FLAG'	=> $data->oh_rec_flag
	// 	,'OH_ARCHIVE_FLAG'	=> $data->oh_archive_flag
	// 	,'OH_INVOICE_FLAG'	=> $data->oh_invoice_flag
	// 	,'OH_FLAG1'		=> $data->oh_flag1
	// 	,'OH_FLAG2'		=> $data->oh_flag2
	// 	,'OH_UPDATE_BY'	=> $data->oh_update_by	
	// 	,'OH_UPDATE_ON'	=> $data->oh_update_on	
	// 	,'OH_LOCK_FLAG'	=> $data->oh_lock_flag	
	// 	,'OH_LOCK_TIME'	=> $data->oh_lock_time	
	// 	,'OH_RACKNO'		=> $data->oh_rackno	
	// 	,'OH_TUBENO'		=> $data->oh_tubeno	
	// 	,'OH_PRINT_COPIES'	=> $data->oh_print_copies	
	// 	,'OH_FAX_FLAG'		=> $data->oh_fax_flag	
	// 	,'OH_EMAIL_FLAG'	=> $data->oh_email_flag	
	// 	,'OH_PCMT2'		=> $data->oh_pcmt2	
	// 	,'OH_PCMT3'		=> $data->oh_pcmt3	
	// 	,'OH_PCMT4'		=> $data->oh_pcmt4	
	// 	,'OH_PCMT5'		=> $data->oh_pcmt5	
	// 	,'OH_SCMT1'		=> $data->oh_scmt1	
	// 	,'OH_SCMT2'		=> $data->oh_scmt2	
	// 	,'OH_OCMT'		=> $data->oh_ocmt	
	// 	,'OH_WAM_FLAG1'	=> $data->oh_wam_flag1	
	// 	,'OH_WAM_FLAG2'	=> $data->oh_wam_flag2	
	// 	,'OH_WAM_FLAG3'	=> $data->oh_wam_flag3	
	// 	,'OH_SPT_CODE'	=> $data->oh_spt_code	
		
		
	// ]);
	
	// }
	// echo " Done";
    // }
    
    //  public function HclabORDD()
    //  {
	// $i = 0;
	// for ($x = 0; $x <= 500; $x++) {
     
	
	//  $datas = DB::connection('oraPRODh')->table('ORD_DTL')->offset($i)->limit(10000)->get(array('*'));
	//  //print_r($datas);
	//  //die();
	//  echo "Processing";
	//  foreach($datas as $data)
	//  {
	//  	echo " -> ";
	//  	echo DB::connection('hclab')->table('OrderDTL')->insertGetId([
	//  	'OD_TNO'			=> $data->od_tno
	//  	,'OD_TESTCODE'	=> $data->od_testcode
	//  	,'OD_ITEM_TYPE'	=> $data->od_item_type
	//  	,'OD_ITEM_PARENT'	=> $data->od_item_parent
	//  	,'OD_ORDER_TI'	=> $data->od_order_ti
	//  	,'OD_PKG_ORDER'	=> $data->od_pkg_order
	//  	,'OD_SEQ_NO'		=> $data->od_seq_no
	//  	,'OD_TR_VAL'		=> $data->od_tr_val
	//  	,'OD_TR_UNIT'		=> $data->od_tr_unit
	//  	,'OD_TR_FLAG'		=> $data->od_tr_flag
	//  	,'OD_TR_RANGE'	=> $data->od_tr_range
	//  	,'OD_NORMAL_LOLIMIT'=> $data->od_normal_lolimit
	//  	,'OD_NORMAL_UPLIMIT'=> $data->od_normal_uplimit
	//  	,'OD_PANIC_LOLIMIT'	=> $data->od_panic_lolimit
	//  	,'OD_PANIC_UPLIMIT'	=> $data->od_panic_uplimit
	//  	,'OD_TR_COMMENT'	=> $data->od_tr_comment
	//  	,'OD_01_VAL'		=> $data->od_01_val
	//  	,'OD_01_ANZ'		=> $data->od_01_anz
	//  	,'OD_02_VAL'		=> $data->od_02_val
	//  	,'OD_02_ANZ'		=> $data->od_02_anz
	//  	,'OD_03_VAL'		=> $data->od_03_val
	//  	,'OD_03_ANZ'		=> $data->od_03_anz
	//  	,'OD_TEST_GRP'	=> $data->od_test_grp
	//  	,'OD_SPL_TYPE'		=> $data->od_spl_type
	//  	,'OD_DATA_TYPE'	=> $data->od_data_type
	//  	,'OD_ORDER_ITEM'	=> $data->od_order_item
	//  	,'OD_ANZ_ORDER'	=> $data->od_anz_order
	//  	,'OD_WC_CODE'	=> $data->od_wc_code
	//  	,'OD_LOGNO'		=> $data->od_logno
	//  	,'OD_RESULT_SRC'	=> $data->od_result_src
	// 	,'OD_ACTION_FLAG'	=> $data->od_action_flag
	//  	,'OD_EDIT_FLAG'	=> $data->od_edit_flag
	//  	,'OD_EDIT_LEVEL'	=> $data->od_edit_level
	//  	,'OD_STATUS'		=> $data->od_status
	//  	,'OD_VALIDATE_BY'	=> $data->od_validate_by
	//  	,'OD_VALIDATE_ON'	=> $data->od_validate_on
	//  	,'OD_RELEASE_ON'	=> $data->od_release_on
	//  	,'OD_ANZ_ID'		=> $data->od_anz_id
	//  	,'OD_ANZ_COMMENT'	=> $data->od_anz_comment
	//  	,'OD_ANZ_RACKNO'	=> $data->od_anz_rackno
	//  	,'OD_ANZ_TUBENO'	=> $data->od_anz_tubeno
	//  	,'OD_MRR_DESC'	=> $data->od_mrr_desc
	//  	,'OD_ATTACHED_CMT'=> $data->od_attached_cmt
	//  	,'OD_CTL_FLAG1'	=> $data->od_ctl_flag1
	//  	,'OD_CTL_FLAG2'	=> $data->od_ctl_flag2
	//  	,'OD_TI_STATUS1'	=> $data->od_ti_status1
	//  	,'OD_TI_STATUS2'	=> $data->od_ti_status2	
	//  	,'OD_ANALY_SECT'	=> $data->od_analy_sect	
	//  	,'OD_TID'			=> $data->od_tid	
	//  	,'OD_SERVICE_TYPE'	=> $data->od_service_type	
	//  	,'OD_HIS_PKG_ORDER'=> $data->od_his_pkg_order	
	//  	,'OD_RE_USED'		=> $data->od_re_used	
	//  	,'OD_UPDATE_BY'	=> $data->od_update_by	
	//  	,'OD_UPDATE_ON'	=> $data->od_update_on	
	//  ]);
	
	//  }
	 
	//  echo " Done";
	 
	//  $i = $i + 10000;
	// }
    // }

// 	public function HclabTestI()
// 	{
   
// 		$datas = DB::connection('oraPRODh')->table('TEST_ITEM')->offset(0)->limit(10000)->get(array('*'));
// 		// print_r($datas);
// 		// die();
// 		echo "Processing";

// 		foreach($datas as $data)
// 		{
// 			echo " -> ";
// 			echo DB::connection('hclab')->table('TestItem')->insertGetId([
// 			'TI_CODE'			=> $data->ti_code
// 			,'TI_NAME'	=> $data->ti_name
// 			,'TI_OTHER_NAME'	=> $data->ti_other_name
// 			,'TI_PRINT_NAME'	=> $data->ti_print_name
// 			,'TI_LONG_NAME'	=> $data->ti_long_name
// 			,'TI_TEST_GRP'	=> $data->ti_test_grp
// 			,'TI_CATEGORY'		=> $data->ti_category
// 			,'TI_UNIT'		=> $data->ti_unit
// 			,'TI_DATA_TYPE'		=> $data->ti_data_type
// 			,'TI_FORMULA_CODE'		=> $data->ti_formula_code
// 			,'TI_SPL_TYPE'	=> $data->ti_spl_type
// 			,'TI_SPL_TYPE2'=> $data->ti_spl_type2
// 			,'TI_REC_FLAG'=> $data->ti_rec_flag
// 			,'TI_DELTA_CHECK'	=> $data->ti_delta_check
// 			,'TI_SI_UNIT'	=> $data->ti_si_unit
// 			,'TI_SI_FACTOR'	=> $data->ti_si_factor
// 			,'TI_LEVEL'		=> $data->ti_level
// 			,'TI_ORDER_ENABLE'		=> $data->ti_order_enable
// 			,'TI_OUTPUT_MASK'		=> $data->ti_output_mask
// 			,'TI_ANZ_ORDER'		=> $data->ti_anz_order
// 			,'TI_TRIGGER_FORMULA'		=> $data->ti_trigger_formula
// 			,'TI_TRIGGER_ITEM1'		=> $data->ti_trigger_item1
// 			,'TI_TRIGGER_ITEM2'	=> $data->ti_trigger_item2
// 			,'TI_TRIGGER_ITEM3'		=> $data->ti_trigger_item3
// 			,'TI_TRIGGER_ITEM4'	=> $data->ti_trigger_item4
// 			,'TI_TRIGGER_ITEM5'	=> $data->ti_trigger_item5
// 			,'TI_DISP_SEQ'	=> $data->ti_disp_seq
// 			,'TI_LINK_ITEM'	=> $data->ti_link_item
// 			,'TI_LINK_NO'		=> $data->ti_link_no
// 			,'TI_ITPV_FLAG'	=> $data->ti_itpv_flag
// 		,'TI_MRF_FLAG'	=> $data->ti_mrf_flag
// 			,'TI_URGENT_TAT'	=> $data->ti_urgent_tat
// 			,'TI_STAT_TAT'	=> $data->ti_stat_tat
// 			,'TI_ROUTINE_TAT'		=> $data->ti_routine_tat
// 			,'TI_TM_CODE'	=> $data->ti_tm_code
// 			,'TI_TH_CODE'	=> $data->ti_th_code
// 			,'TI_TH_SEQ'	=> $data->ti_th_seq
// 			,'TI_MB_TEST_GRP'		=> $data->ti_mb_test_grp
// 			,'TI_MB_PC_TYPE'	=> $data->ti_mb_pc_type
// 			,'TI_BB_PC_TYPE'	=> $data->ti_bb_pc_type
// 			,'TI_CTL_FLAG'	=> $data->ti_ctl_flag
// 			,'TI_FTR_TPL'	=> $data->ti_ftr_tpl
// 			,'TI_TRIGGER_FR'=> $data->ti_trigger_fr
// 			,'TI_ATTACHED_CMT'	=> $data->ti_attached_cmt
// 			,'TI_DFLT_VALUE'	=> $data->ti_dflt_value
// 			,'TI_REORDER_CTL'	=> $data->ti_reorder_ctl
// 			,'TI_LABNO_SUFFIX'	=> $data->ti_labno_suffix	
// 			,'TI_QC_VALIDITY'	=> $data->ti_qc_validity	
// 			,'TI_MB_NOGROWTH_BR'			=> $data->ti_mb_nogrowth_br	
// 			,'TI_MB_NOGROWTH_DAY'	=> $data->ti_mb_nogrowth_day	
// 			,'TI_MB_NOGROWTH_CODE'=> $data->ti_mb_nogrowth_code	
// 			,'TI_MB_PRENOGROWTH_DAY'		=> $data->ti_mb_prenogrowth_day	
// 			,'TI_MB_PRENOGROWTH_CODE'	=> $data->ti_mb_prenogrowth_code	
// 			,'TI_TB_CULTURE_FLAG'	=> $data->ti_tb_culture_flag	
// 			,'TI_TB_NEGSTAIN_CODE'	=> $data->ti_tb_negstain_code
// 			,'TI_TB_MEDIA1'	=> $data->ti_tb_media1
// 			,'TI_TB_MEDIA1_CUTOFF'	=> $data->ti_tb_media1_cutoff
// 			,'TI_TB_MEDIA1_NOGROWTH_CODE'	=> $data->ti_tb_media1_nogrowth_code
// 			,'TI_TB_MEDIA2'	=> $data->ti_tb_media2
// 			,'TI_TB_MEDIA2_CUTOFF'	=> $data->ti_tb_media2_cutoff
// 			,'TI_TB_MEDIA2_NOGROWTH_CODE'	=> $data->ti_tb_media2_nogrowth_code
// 			,'TI_TB_MEDIA3'	=> $data->ti_tb_media3
// 			,'TI_TB_MEDIA3_CUTOFF'	=> $data->ti_tb_media3_cutoff
// 			,'TI_TB_MEDIA3_NOGROWTH_CODE'	=> $data->ti_tb_media3_nogrowth_code
// 			,'TI_ORDER_RETENTION'	=> $data->ti_order_retention
// 			,'TI_MIN_SPL_VOL'	=> $data->ti_min_spl_vol
// 			,'TI_OPT_SPL_VOL'	=> $data->ti_opt_spl_vol
// 			,'TI_USE_PRIMARY_TUBE'	=> $data->ti_use_primary_tube
// 			,'TI_JV_CODE'	=> $data->ti_jv_code
// 			,'TI_CONFIDENTIAL_FLAG'	=> $data->ti_confidential_flag
// 			,'TI_MOLECULAR_FLAG'	=> $data->ti_molecular_flag
// 			,'TI_UPDATE_BY'	=> $data->ti_update_by
// 			,'TI_UPDATE_ON'	=> $data->ti_update_on
// 		]);
	
// 		}
	
// 	echo " Done";

//    }

}



