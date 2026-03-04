<?php

namespace App\Models\hclab;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;



class HCLABPatientMaster extends Model
{
	function SyncToTUAZON()
	{
		echo 'Start';
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');

		$sqlInsert = "INSERT into cust_master (	DBCODE, DBNAME, DBCOMREGNO, DBCUSTYPE, DBCONTACT, DBGROUP ,DBTERM ,DBPRICEGRP ,DBFINCHG ,DBLIMIT ,DB_CURBAL ,DB_DEPOSIT ,DBADDR1 ,DBADDR2 ,DBADDR3 ,DBADDR4 ,DBPOSTCODE		
				,DBSTATECODE	,DBCOUNTRYCODE ,DBTEL1 ,DBTEL2 ,DBFAX ,DBHPHONE_NO ,DBEMAIL ,IC_NO ,OLD_IC_NO ,NAME_TITLE1 ,NAME_TITLE2 ,OTHER_NAME 
				,BIRTH_DATE ,STATE_OF_BIRTH	
				,SEX	,ETHNIC_GROUP ,RELIGION ,MARITAL_STATUS ,CITIZENSHIP ,MOTHER_MAIDEN_NAME ,BLOODGRP_ABO ,BLOODGRP_RH ,BLOODGRP_SUBGROUP ,MEDICAL_HISTORY ,FAMILY_HISTORY ,SOCIAL_LIFE ,ALLERGY ,ACTIVE_CODE		
				,REGISTRATION_DATE
				,REGISTRATION_TIME
				,LAST_ADMIT_DATE
				,LAST_VISIT_DATE
				,LAST_DISCHARGE_TYPE
				,LAST_DISCH_DATE
				,RHESUS ,GRAVIDA ,PARA ,SPOKEN_LANGUAGE1	,SPOKEN_LANGUAGE2 ,PATIENT_OCCUPATION_CODE ,SOCSO_NO ,COMMENT_1 ,COMMENT_2 ,MEDICAL_FOLDER ,FOLDER_ID ,FOLDER_LOCATION ,MERGED_DBCODE ,REC_FLAG ,PAYER_TYPE		
				,PAYER_CODE ,PCMT_CODE1 ,PCMT_CODE2	 ,PCMT_CODE3 ,PCMT_CODE4 ,PCMT_CODE5 ,PCMT_FTEXT ,DB_UPDATE_BY	
				,DB_UPDATE_ON) 
				VALUES (
					:DBCODE, :DBNAME, :DBCOMREGNO, :DBCUSTYPE, :DBCONTACT, :DBGROUP ,:DBTERM ,:DBPRICEGRP ,:DBFINCHG ,:DBLIMIT ,:DB_CURBAL ,:DB_DEPOSIT ,:DBADDR1 ,:DBADDR2 ,:DBADDR3 ,:DBADDR4 ,:DBPOSTCODE		
					,:DBSTATECODE	,:DBCOUNTRYCODE ,:DBTEL1 ,:DBTEL2 ,:DBFAX ,:DBHPHONE_NO ,:DBEMAIL ,:IC_NO ,:OLD_IC_NO ,:NAME_TITLE1 ,:NAME_TITLE2 ,:OTHER_NAME 
					,:BIRTH_DATE 
					,:STATE_OF_BIRTH	
					,:SEX ,:ETHNIC_GROUP ,:RELIGION ,:MARITAL_STATUS ,:CITIZENSHIP ,:MOTHER_MAIDEN_NAME ,:BLOODGRP_ABO ,:BLOODGRP_RH ,:BLOODGRP_SUBGROUP ,:MEDICAL_HISTORY ,:FAMILY_HISTORY ,:SOCIAL_LIFE ,:ALLERGY ,:ACTIVE_CODE		
					,:REGISTRATION_DATE
					,:REGISTRATION_TIME
					,:LAST_ADMIT_DATE
					,:LAST_VISIT_DATE
					,:LAST_DISCHARGE_TYPE
					,:LAST_DISCH_DATE
					,:RHESUS ,:GRAVIDA ,:PARA ,:SPOKEN_LANGUAGE1	,:SPOKEN_LANGUAGE2 ,:PATIENT_OCCUPATION_CODE ,:SOCSO_NO ,:COMMENT_1 ,:COMMENT_2 ,:MEDICAL_FOLDER ,:FOLDER_ID ,:FOLDER_LOCATION ,:MERGED_DBCODE ,:REC_FLAG ,:PAYER_TYPE		
					,:PAYER_CODE ,:PCMT_CODE1 ,:PCMT_CODE2	 ,:PCMT_CODE3 ,:PCMT_CODE4 ,:PCMT_CODE5 ,:PCMT_FTEXT ,:DB_UPDATE_BY	
					,:DB_UPDATE_ON
				)";
		
		$datas = DB::connection('hclab')->table('PatientMaster')->where('Status', 'LIKE', 'reUpdate')->get(array('*'));		
		$x = 1;
		foreach($datas as $data)
		{
			$x++;
			$DBCODE					= $data->DBCODE;
			$DBNAME					= $data->DBNAME;
			$DBCOMREGNO				= $data->DBCOMREGNO;
			$DBCUSTYPE				= $data->DBCUSTYPE;
			$DBCONTACT				= $data->DBCONTACT;
			$DBGROUP				= $data->DBGROUP;
			$DBTERM					= $data->DBTERM;
			$DBPRICEGRP				= $data->DBPRICEGRP;
			$DBFINCHG				= $data->DBFINCHG;
			$DBLIMIT					= $data->DBLIMIT;
			$DB_CURBAL				= $data->DB_CURBAL;
			$DB_DEPOSIT				= $data->DB_DEPOSIT;
			$DBADDR1				= $data->DBADDR1;
			$DBADDR2				= $data->DBADDR2;
			$DBADDR3				= $data->DBADDR3;
			$DBADDR4				= $data->DBADDR4;
			$DBPOSTCODE				= $data->DBPOSTCODE;
			$DBSTATECODE				= $data->DBSTATECODE;
			$DBCOUNTRYCODE			= $data->DBCOUNTRYCODE;
			$DBTEL1					= $data->DBTEL1;
			$DBTEL2					= $data->DBTEL2;
			$DBFAX					= $data->DBFAX;
			$DBHPHONE_NO			= $data->DBHPHONE_NO;
			$DBEMAIL					= $data->DBEMAIL;
			$IC_NO					= $data->IC_NO;
			$OLD_IC_NO				= $data->OLD_IC_NO;
			$NAME_TITLE1				= $data->NAME_TITLE1;
			$NAME_TITLE2				= $data->NAME_TITLE2;
			$OTHER_NAME				= $data->OTHER_NAME;
			$BIRTH_DATE				= (!empty($data->BIRTH_DATE))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->BIRTH_DATE)->format('d-M-Y'):'';
			$STATE_OF_BIRTH			= $data->STATE_OF_BIRTH;
			$SEX						= $data->SEX;
			$ETHNIC_GROUP			= $data->ETHNIC_GROUP;
			$RELIGION				= $data->RELIGION;
			$MARITAL_STATUS			= $data->MARITAL_STATUS;
			$CITIZENSHIP				= $data->CITIZENSHIP;
			$MOTHER_MAIDEN_NAME		= $data->MOTHER_MAIDEN_NAME;
			$BLOODGRP_ABO			= $data->BLOODGRP_ABO;
			$BLOODGRP_RH			= $data->BLOODGRP_RH;
			$BLOODGRP_SUBGROUP		= $data->BLOODGRP_SUBGROUP;
			$MEDICAL_HISTORY			= $data->MEDICAL_HISTORY;
			$FAMILY_HISTORY			= $data->FAMILY_HISTORY;
			$SOCIAL_LIFE				= $data->SOCIAL_LIFE;
			$ALLERGY					= $data->ALLERGY;
			$ACTIVE_CODE				= $data->ACTIVE_CODE;
			$REGISTRATION_DATE		= (!empty($data->REGISTRATION_DATE))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->REGISTRATION_DATE)->format('d-M-Y'):'';
			$REGISTRATION_TIME		= (!empty($data->REGISTRATION_TIME))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->REGISTRATION_TIME)->format('d-M-Y'):'';
			$LAST_ADMIT_DATE			= (!empty($data->LAST_ADMIT_DATE))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->LAST_ADMIT_DATE)->format('d-M-Y'):'';
			$LAST_VISIT_DATE			=  (!empty($data->LAST_VISIT_DATE))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->LAST_VISIT_DATE)->format('d-M-Y'):'';
			$LAST_DISCHARGE_TYPE		= $data->LAST_DISCHARGE_TYPE;
			$LAST_DISCH_DATE			= (!empty($data->LAST_DISCH_DATE))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->LAST_DISCH_DATE)->format('d-M-Y'):'';
			$RHESUS					= $data->RHESUS;
			$GRAVIDA					= $data->GRAVIDA;
			$PARA					= $data->PARA;
			$SPOKEN_LANGUAGE1		= $data->SPOKEN_LANGUAGE1;
			$SPOKEN_LANGUAGE2		= $data->SPOKEN_LANGUAGE2;
			$PATIENT_OCCUPATION_CODE	= $data->PATIENT_OCCUPATION_CODE;
			$SOCSO_NO				= $data->SOCSO_NO;
			$COMMENT_1				= $data->COMMENT_1;
			$COMMENT_2				= $data->COMMENT_2;
			$MEDICAL_FOLDER			= $data->MEDICAL_FOLDER;
			$FOLDER_ID				= $data->FOLDER_ID;
			$FOLDER_LOCATION			= $data->FOLDER_LOCATION;
			$MERGED_DBCODE			= $data->MERGED_DBCODE;
			$REC_FLAG				= $data->REC_FLAG;
			$PAYER_TYPE				= $data->PAYER_TYPE;
			$PAYER_CODE				= $data->PAYER_CODE;
			$PCMT_CODE1				= $data->PCMT_CODE1;
			$PCMT_CODE2				= $data->PCMT_CODE2;
			$PCMT_CODE3				= $data->PCMT_CODE3;
			$PCMT_CODE4				= $data->PCMT_CODE4;
			$PCMT_CODE5				= $data->PCMT_CODE5;
			$PCMT_FTEXT				= $data->PCMT_FTEXT;
			$DB_UPDATE_BY			= $data->DB_UPDATE_BY;
			$DB_UPDATE_ON			= (!empty($data->DB_UPDATE_ON))?\Carbon\Carbon::createFromFormat('Y-m-d', $data->DB_UPDATE_ON)->format('d-M-Y'):'';
			
			
			//////////////
			$sqlSelect = "SELECT  * FROM cust_master WHERE  DBCODE LIKE  '".$data->DBCODE."'  ";
			$itemSelect = oci_parse($conn, $sqlSelect);
			oci_execute($itemSelect);
			
			$withItem = '';
			while (oci_fetch($itemSelect)) {
				$withItem = oci_result($itemSelect, 'DBCODE');
			}
			
			if(empty($withItem))
			{
				echo "<br>";
				
				
				$compiled = oci_parse($conn, $sqlInsert);
				
				oci_bind_by_name($compiled, ":DBCODE", $DBCODE);
				oci_bind_by_name($compiled, ":DBNAME", $DBNAME);
				oci_bind_by_name($compiled, ":DBCOMREGNO", $DBCOMREGNO);
				oci_bind_by_name($compiled, ":DBCUSTYPE", $DBCUSTYPE);
				oci_bind_by_name($compiled, ":DBCONTACT", $DBCONTACT);
				oci_bind_by_name($compiled, ":DBGROUP", $DBGROUP);
				oci_bind_by_name($compiled, ":DBTERM", $DBTERM);
				oci_bind_by_name($compiled, ":DBPRICEGRP", $DBPRICEGRP);
				oci_bind_by_name($compiled, ":DBFINCHG", $DBFINCHG);
				oci_bind_by_name($compiled, ":DBLIMIT", $DBLIMIT);
				oci_bind_by_name($compiled, ":DB_CURBAL", $DB_CURBAL);
				oci_bind_by_name($compiled, ":DB_DEPOSIT", $DB_DEPOSIT);
				oci_bind_by_name($compiled, ":DBADDR1", $DBADDR1);
				oci_bind_by_name($compiled, ":DBADDR2", $DBADDR2);
				oci_bind_by_name($compiled, ":DBADDR3", $DBADDR3);
				oci_bind_by_name($compiled, ":DBADDR4", $DBADDR4);
				oci_bind_by_name($compiled, ":DBPOSTCODE", $DBPOSTCODE);
				oci_bind_by_name($compiled, ":DBSTATECODE", $DBSTATECODE);
				oci_bind_by_name($compiled, ":DBCOUNTRYCODE", $DBCOUNTRYCODE);
				oci_bind_by_name($compiled, ":DBTEL1", $DBTEL1);
				oci_bind_by_name($compiled, ":DBTEL2", $DBTEL2);
				oci_bind_by_name($compiled, ":DBFAX", $DBFAX);
				oci_bind_by_name($compiled, ":DBHPHONE_NO", $DBHPHONE_NO);
				oci_bind_by_name($compiled, ":DBEMAIL", $DBEMAIL);
				oci_bind_by_name($compiled, ":IC_NO", $IC_NO);
				oci_bind_by_name($compiled, ":OLD_IC_NO", $OLD_IC_NO);
				oci_bind_by_name($compiled, ":NAME_TITLE1", $NAME_TITLE1);
				oci_bind_by_name($compiled, ":NAME_TITLE2", $NAME_TITLE2);
				oci_bind_by_name($compiled, ":OTHER_NAME", $OTHER_NAME);
				oci_bind_by_name($compiled, ":BIRTH_DATE", $BIRTH_DATE);
				oci_bind_by_name($compiled, ":STATE_OF_BIRTH", $STATE_OF_BIRTH);
				oci_bind_by_name($compiled, ":SEX", $SEX);
				oci_bind_by_name($compiled, ":ETHNIC_GROUP", $ETHNIC_GROUP);
				oci_bind_by_name($compiled, ":RELIGION", $RELIGION);
				oci_bind_by_name($compiled, ":MARITAL_STATUS", $MARITAL_STATUS);
				oci_bind_by_name($compiled, ":CITIZENSHIP", $CITIZENSHIP);
				oci_bind_by_name($compiled, ":MOTHER_MAIDEN_NAME", $MOTHER_MAIDEN_NAME);
				oci_bind_by_name($compiled, ":BLOODGRP_ABO", $BLOODGRP_ABO);
				oci_bind_by_name($compiled, ":BLOODGRP_RH", $BLOODGRP_RH);
				oci_bind_by_name($compiled, ":BLOODGRP_SUBGROUP", $BLOODGRP_SUBGROUP);
				oci_bind_by_name($compiled, ":MEDICAL_HISTORY", $MEDICAL_HISTORY);
				oci_bind_by_name($compiled, ":FAMILY_HISTORY", $FAMILY_HISTORY);
				oci_bind_by_name($compiled, ":SOCIAL_LIFE", $SOCIAL_LIFE);
				oci_bind_by_name($compiled, ":ALLERGY", $ALLERGY);
				oci_bind_by_name($compiled, ":ACTIVE_CODE", $ACTIVE_CODE);
				oci_bind_by_name($compiled, ":REGISTRATION_DATE", $REGISTRATION_DATE);
				oci_bind_by_name($compiled, ":REGISTRATION_TIME", $REGISTRATION_TIME);
				oci_bind_by_name($compiled, ":LAST_ADMIT_DATE", $LAST_ADMIT_DATE);
				oci_bind_by_name($compiled, ":LAST_VISIT_DATE", $LAST_VISIT_DATE);
				oci_bind_by_name($compiled, ":LAST_DISCHARGE_TYPE", $LAST_DISCHARGE_TYPE);
				oci_bind_by_name($compiled, ":LAST_DISCH_DATE", $LAST_DISCH_DATE);
				oci_bind_by_name($compiled, ":RHESUS", $RHESUS);
				oci_bind_by_name($compiled, ":GRAVIDA", $GRAVIDA);
				oci_bind_by_name($compiled, ":PARA", $PARA);
				oci_bind_by_name($compiled, ":SPOKEN_LANGUAGE1", $SPOKEN_LANGUAGE1);
				oci_bind_by_name($compiled, ":SPOKEN_LANGUAGE2", $SPOKEN_LANGUAGE2);
				oci_bind_by_name($compiled, ":PATIENT_OCCUPATION_CODE", $PATIENT_OCCUPATION_CODE);
				oci_bind_by_name($compiled, ":SOCSO_NO", $SOCSO_NO);
				oci_bind_by_name($compiled, ":COMMENT_1", $COMMENT_1);
				oci_bind_by_name($compiled, ":COMMENT_2", $COMMENT_2);
				oci_bind_by_name($compiled, ":MEDICAL_FOLDER", $MEDICAL_FOLDER);
				oci_bind_by_name($compiled, ":FOLDER_ID", $FOLDER_ID);
				oci_bind_by_name($compiled, ":FOLDER_LOCATION", $FOLDER_LOCATION);
				oci_bind_by_name($compiled, ":MERGED_DBCODE", $MERGED_DBCODE);
				oci_bind_by_name($compiled, ":REC_FLAG", $REC_FLAG);
				oci_bind_by_name($compiled, ":PAYER_TYPE", $PAYER_TYPE);
				oci_bind_by_name($compiled, ":PAYER_CODE", $PAYER_CODE);
				oci_bind_by_name($compiled, ":PCMT_CODE1", $PCMT_CODE1);
				oci_bind_by_name($compiled, ":PCMT_CODE2", $PCMT_CODE2);
				oci_bind_by_name($compiled, ":PCMT_CODE3", $PCMT_CODE3);
				oci_bind_by_name($compiled, ":PCMT_CODE4", $PCMT_CODE4);
				oci_bind_by_name($compiled, ":PCMT_CODE5", $PCMT_CODE5);
				oci_bind_by_name($compiled, ":PCMT_FTEXT", $PCMT_FTEXT);
				oci_bind_by_name($compiled, ":DB_UPDATE_BY", $DB_UPDATE_BY);
				oci_bind_by_name($compiled, ":DB_UPDATE_ON", $DB_UPDATE_ON);
				
				$result = oci_execute($compiled);
				
				if (!$result){
				    $e = oci_error($result);  // For oci_execute errors pass the statement handle
				    print htmlentities($e['message']);
				    print "\n<pre>\n";
				    print htmlentities($e['sqltext']);
				    printf("\n%".($e['offset']+1)."s", "^");
				    print  "\n</pre>\n";
				}else{
					DB::connection('hclab')->table('PatientMaster')->where('Id',$data->Id)	->update(['Status'=>'Append']);
				}
				oci_close($conn);
			}
			else
			{
				$itemUpdate = oci_parse($conn, "UPDATE cust_master SET 
				
					DBNAME = '".$DBNAME."',
					DBCOMREGNO = '".$DBCOMREGNO."',
					DBCUSTYPE = '".$DBCUSTYPE."',
					DBCONTACT = '".$DBCONTACT."',
					DBGROUP = '".$DBGROUP."',
					DBTERM = '".$DBTERM."',
					DBPRICEGRP = '".$DBPRICEGRP."',
					DBFINCHG = '".$DBFINCHG."',
					DBLIMIT = '".$DBLIMIT."',
					DB_CURBAL = '".$DB_CURBAL."',
					DB_DEPOSIT = '".$DB_DEPOSIT."',
					DBADDR1 = '".$DBADDR1."',
					DBADDR2 = '".$DBADDR2."',
					DBADDR3 = '".$DBADDR3."',
					DBADDR4 = '".$DBADDR4."',
					DBPOSTCODE = '".$DBPOSTCODE."',
					DBSTATECODE = '".$DBSTATECODE."',
					DBCOUNTRYCODE = '".$DBCOUNTRYCODE."',
					DBTEL1 = '".$DBTEL1."',
					DBTEL2 = '".$DBTEL2."',
					DBFAX = '".$DBFAX."',
					DBHPHONE_NO = '".$DBHPHONE_NO."',
					DBEMAIL = '".$DBEMAIL."',
					IC_NO = '".$IC_NO."',
					OLD_IC_NO = '".$OLD_IC_NO."',
					NAME_TITLE1 = '".$NAME_TITLE1."',
					NAME_TITLE2 = '".$NAME_TITLE2."',
					OTHER_NAME = '".$OTHER_NAME."',
					BIRTH_DATE = '".$BIRTH_DATE."',
					STATE_OF_BIRTH = '".$STATE_OF_BIRTH."',
					SEX = '".$SEX."',
					ETHNIC_GROUP = '".$ETHNIC_GROUP."',
					RELIGION = '".$RELIGION."',
					MARITAL_STATUS = '".$MARITAL_STATUS."',
					CITIZENSHIP = '".$CITIZENSHIP."',
					MOTHER_MAIDEN_NAME = '".$MOTHER_MAIDEN_NAME."',
					BLOODGRP_ABO = '".$BLOODGRP_ABO."',
					BLOODGRP_RH = '".$BLOODGRP_RH."',
					BLOODGRP_SUBGROUP = '".$BLOODGRP_SUBGROUP."',
					MEDICAL_HISTORY = '".$MEDICAL_HISTORY."',
					FAMILY_HISTORY = '".$FAMILY_HISTORY."',
					SOCIAL_LIFE = '".$SOCIAL_LIFE."',
					ALLERGY = '".$ALLERGY."',
					ACTIVE_CODE = '".$ACTIVE_CODE."',
					REGISTRATION_DATE = '".$REGISTRATION_DATE."',
					REGISTRATION_TIME = '".$REGISTRATION_TIME."',
					LAST_ADMIT_DATE = '".$LAST_ADMIT_DATE."',
					LAST_VISIT_DATE = '".$LAST_VISIT_DATE."',
					LAST_DISCHARGE_TYPE = '".$LAST_DISCHARGE_TYPE."',
					LAST_DISCH_DATE = '".$LAST_DISCH_DATE."',
					RHESUS = '".$RHESUS."',
					GRAVIDA = '".$GRAVIDA."',
					PARA = '".$PARA."',
					SPOKEN_LANGUAGE1 = '".$SPOKEN_LANGUAGE1."',
					SPOKEN_LANGUAGE2 = '".$SPOKEN_LANGUAGE2."',
					PATIENT_OCCUPATION_CODE = '".$PATIENT_OCCUPATION_CODE."',
					SOCSO_NO = '".$SOCSO_NO."',
					COMMENT_1 = '".$COMMENT_1."',
					COMMENT_2 = '".$COMMENT_2."',
					MEDICAL_FOLDER = '".$MEDICAL_FOLDER."',
					FOLDER_ID = '".$FOLDER_ID."',
					FOLDER_LOCATION = '".$FOLDER_LOCATION."',
					MERGED_DBCODE = '".$MERGED_DBCODE."',
					REC_FLAG = '".$REC_FLAG."',
					PAYER_TYPE = '".$PAYER_TYPE."',
					PAYER_CODE = '".$PAYER_CODE."',
					PCMT_CODE1 = '".$PCMT_CODE1."',
					PCMT_CODE2 = '".$PCMT_CODE2."',
					PCMT_CODE3 = '".$PCMT_CODE3."',
					PCMT_CODE4 = '".$PCMT_CODE4."',
					PCMT_CODE5 = '".$PCMT_CODE5."',
					PCMT_FTEXT = '".$PCMT_FTEXT."',
					DB_UPDATE_BY = '".$DB_UPDATE_BY."',
					DB_UPDATE_ON = '".$DB_UPDATE_ON."'
				WHERE  DBCODE LIKE  '".$data->DBCODE."' ");
				$result = oci_execute($itemUpdate); 
				if (!$result){
				    $e = oci_error($result);  // For oci_execute errors pass the statement handle
				    print htmlentities($e['message']);
				    print "\n<pre>\n";
				    print htmlentities($e['sqltext']);
				    printf("\n%".($e['offset']+1)."s", "^");
				    print  "\n</pre>\n";
				}else{
					DB::connection('hclab')->table('PatientMaster')->where('Id',$data->Id)	->update(['Status'=>'Updated']);
				}
				oci_close($conn);
				
				
			}
			//die();
		}
		//DB::connection('Eros')->commit();  
		echo '<br>Todays, Total Patient Sync '.$x.'<br>End';
	
	
		
	
	}

	
	/////////// SMB HCLAB Connection
	function SMBSync()
	{
		$SMBcstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$SMBconn = oci_connect("hclab", "hclab", $SMBcstr, 'AL32UTF8');
		
		$today = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-y');
		$sql = "SELECT 	DBCODE, DBNAME, DBCOMREGNO, DBCUSTYPE, DBCONTACT, DBGROUP ,DBTERM ,DBPRICEGRP ,DBFINCHG ,DBLIMIT ,DB_CURBAL ,DB_DEPOSIT ,DBADDR1 ,DBADDR2 ,DBADDR3 ,DBADDR4 ,DBPOSTCODE		
				,DBSTATECODE	,DBCOUNTRYCODE ,DBTEL1 ,DBTEL2 ,DBFAX ,DBHPHONE_NO ,DBEMAIL ,IC_NO ,OLD_IC_NO ,NAME_TITLE1 ,NAME_TITLE2 ,OTHER_NAME ,TO_CHAR(BIRTH_DATE, 'yyyy-mm-dd') AS BIRTH_DATE ,STATE_OF_BIRTH	
				,SEX	,ETHNIC_GROUP ,RELIGION ,MARITAL_STATUS ,CITIZENSHIP ,MOTHER_MAIDEN_NAME ,BLOODGRP_ABO ,BLOODGRP_RH ,BLOODGRP_SUBGROUP ,MEDICAL_HISTORY ,FAMILY_HISTORY ,SOCIAL_LIFE ,ALLERGY ,ACTIVE_CODE		
				,TO_CHAR(REGISTRATION_DATE, 'yyyy-mm-dd') AS REGISTRATION_DATE
				,TO_CHAR(REGISTRATION_TIME, 'yyyy-mm-dd') AS REGISTRATION_TIME
				,TO_CHAR(LAST_ADMIT_DATE, 'yyyy-mm-dd') AS LAST_ADMIT_DATE
				,TO_CHAR(LAST_VISIT_DATE, 'yyyy-mm-dd') AS LAST_VISIT_DATE
				,LAST_DISCHARGE_TYPE
				,TO_CHAR(LAST_DISCH_DATE, 'yyyy-mm-dd') AS LAST_DISCH_DATE
				,RHESUS ,GRAVIDA ,PARA ,SPOKEN_LANGUAGE1	,SPOKEN_LANGUAGE2 ,PATIENT_OCCUPATION_CODE ,SOCSO_NO ,COMMENT_1 ,COMMENT_2 ,MEDICAL_FOLDER ,FOLDER_ID ,FOLDER_LOCATION ,MERGED_DBCODE ,REC_FLAG ,PAYER_TYPE		
				,PAYER_CODE ,PCMT_CODE1 ,PCMT_CODE2	 ,PCMT_CODE3 ,PCMT_CODE4 ,PCMT_CODE5 ,PCMT_FTEXT ,DB_UPDATE_BY	
				,TO_CHAR(DB_UPDATE_ON, 'yyyy-mm-dd') AS DB_UPDATE_ON
			FROM cust_master where  DB_UPDATE_ON LIKE '".strtoupper($today)."'  "; //DBCODE like 'SMB%' AND  DB_UPDATE_ON = '".$today."' 
		
		$stid = oci_parse($SMBconn, $sql);
		oci_execute($stid);
		echo "Start";
		$x = 1;
		while (oci_fetch($stid)) {
			
			//check if exsist 
			 $count  = DB::connection('hclab')->table('PatientMaster')->where('DBCODE', 'LIKE', oci_result($stid, 'DBCODE'))->get(array('Id'));
			
			$x ++;
			if(count($count) != 0)
			{
				//echo "<BR>".$count[0]->Id."<BR>";
				DB::connection('hclab')->table('PatientMaster')
				->where('Id',$count[0]->Id)
				->update([
					'DBNAME'			=> oci_result($stid, 'DBNAME')
					,'DBCOMREGNO'	=> oci_result($stid, 'DBCOMREGNO')
					,'DBCUSTYPE'		=> oci_result($stid, 'DBCUSTYPE')
					,'DBCONTACT'		=> oci_result($stid, 'DBCONTACT')
					,'DBGROUP'		=> oci_result($stid, 'DBGROUP')
					,'DBTERM'			=> oci_result($stid, 'DBTERM')
					,'DBPRICEGRP'		=> oci_result($stid, 'DBPRICEGRP')
					,'DBFINCHG'		=> oci_result($stid, 'DBFINCHG')
					,'DBLIMIT'			=> oci_result($stid, 'DBLIMIT')
					,'DB_CURBAL'		=> oci_result($stid, 'DB_CURBAL')
					,'DB_DEPOSIT'		=> oci_result($stid, 'DB_DEPOSIT')
					,'DBADDR1'		=> oci_result($stid, 'DBADDR1')
					,'DBADDR2'		=> oci_result($stid, 'DBADDR2')
					,'DBADDR3'		=> oci_result($stid, 'DBADDR3')
					,'DBADDR4'		=> oci_result($stid, 'DBADDR4')
					,'DBPOSTCODE'		=> oci_result($stid, 'DBPOSTCODE')
					,'DBSTATECODE'	=> oci_result($stid, 'DBSTATECODE')
					,'DBCOUNTRYCODE'	=> oci_result($stid, 'DBCOUNTRYCODE')
					,'DBTEL1'			=> oci_result($stid, 'DBTEL1')
					,'DBTEL2'			=> oci_result($stid, 'DBTEL2')
					,'DBFAX'			=> oci_result($stid, 'DBFAX')
					,'DBHPHONE_NO'	=> oci_result($stid, 'DBHPHONE_NO')
					,'DBEMAIL'			=> oci_result($stid, 'DBEMAIL')
					,'IC_NO'			=> trim('SMB-'.oci_result($stid, 'IC_NO'))
					,'OLD_IC_NO'		=> oci_result($stid, 'OLD_IC_NO')
					,'NAME_TITLE1'		=> oci_result($stid, 'NAME_TITLE1')
					,'NAME_TITLE2'		=> oci_result($stid, 'NAME_TITLE2')
					,'OTHER_NAME'		=> oci_result($stid, 'OTHER_NAME')
					,'BIRTH_DATE'		=> (!empty(oci_result($stid, 'BIRTH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'BIRTH_DATE'))):NULL
					,'STATE_OF_BIRTH'	=> oci_result($stid, 'STATE_OF_BIRTH')
					,'SEX'			=> oci_result($stid, 'SEX')
					,'ETHNIC_GROUP'	=> oci_result($stid, 'ETHNIC_GROUP')
					,'RELIGION'		=> oci_result($stid, 'RELIGION')
					,'MARITAL_STATUS'	=> oci_result($stid, 'MARITAL_STATUS')
					,'CITIZENSHIP'		=> oci_result($stid, 'CITIZENSHIP')
					,'MOTHER_MAIDEN_NAME'=> oci_result($stid, 'MOTHER_MAIDEN_NAME')
					,'BLOODGRP_ABO'	=> oci_result($stid, 'BLOODGRP_ABO')
					,'BLOODGRP_RH'	=> oci_result($stid, 'BLOODGRP_RH')
					,'BLOODGRP_SUBGROUP'=> oci_result($stid, 'BLOODGRP_SUBGROUP')
					,'MEDICAL_HISTORY'	=> oci_result($stid, 'MEDICAL_HISTORY')
					,'FAMILY_HISTORY'	=> oci_result($stid, 'FAMILY_HISTORY')
					,'SOCIAL_LIFE'		=> oci_result($stid, 'SOCIAL_LIFE')
					,'ALLERGY'			=> oci_result($stid, 'ALLERGY')
					,'ACTIVE_CODE'		=> oci_result($stid, 'ACTIVE_CODE')
					,'REGISTRATION_DATE'=> (!empty(oci_result($stid, 'REGISTRATION_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'REGISTRATION_DATE'))):NULL
					,'REGISTRATION_TIME'=> (!empty(oci_result($stid, 'REGISTRATION_TIME')))?date('Y-m-d H:i:s', strtotime(oci_result($stid, 'REGISTRATION_TIME'))):NULL
					,'LAST_ADMIT_DATE'	=> (!empty(oci_result($stid, 'LAST_ADMIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_ADMIT_DATE'))):NULL
					,'LAST_VISIT_DATE'	=>  (!empty(oci_result($stid, 'LAST_VISIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_VISIT_DATE'))):NULL
					,'LAST_DISCHARGE_TYPE'=> oci_result($stid, 'LAST_DISCHARGE_TYPE')
					,'LAST_DISCH_DATE'	=> (!empty(oci_result($stid, 'LAST_DISCH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_DISCH_DATE'))):NULL
					,'RHESUS'			=> oci_result($stid, 'RHESUS')
					,'GRAVIDA'		=> oci_result($stid, 'GRAVIDA')
					,'PARA'			=> oci_result($stid, 'PARA')
					,'SPOKEN_LANGUAGE1'=> oci_result($stid, 'SPOKEN_LANGUAGE1')
					,'SPOKEN_LANGUAGE2'=> oci_result($stid, 'SPOKEN_LANGUAGE2')
					,'PATIENT_OCCUPATION_CODE'=> oci_result($stid, 'PATIENT_OCCUPATION_CODE')
					,'SOCSO_NO'		=> oci_result($stid, 'SOCSO_NO')
					,'COMMENT_1'		=> oci_result($stid, 'COMMENT_1')
					,'COMMENT_2'		=> oci_result($stid, 'COMMENT_2')
					,'MEDICAL_FOLDER'	=> oci_result($stid, 'MEDICAL_FOLDER')
					,'FOLDER_ID'		=> oci_result($stid, 'FOLDER_ID')
					,'FOLDER_LOCATION'	=> oci_result($stid, 'FOLDER_LOCATION')
					,'MERGED_DBCODE'	=> oci_result($stid, 'MERGED_DBCODE')
					,'REC_FLAG'		=> oci_result($stid, 'REC_FLAG')
					,'PAYER_TYPE'		=> oci_result($stid, 'PAYER_TYPE')
					,'PAYER_CODE'		=> oci_result($stid, 'PAYER_CODE')
					,'PCMT_CODE1'		=> oci_result($stid, 'PCMT_CODE1')
					,'PCMT_CODE2'		=> oci_result($stid, 'PCMT_CODE2')
					,'PCMT_CODE3'		=> oci_result($stid, 'PCMT_CODE3')
					,'PCMT_CODE4'		=> oci_result($stid, 'PCMT_CODE4')
					,'PCMT_CODE5'		=> oci_result($stid, 'PCMT_CODE5')
					,'PCMT_FTEXT'		=> oci_result($stid, 'PCMT_FTEXT')
					,'DB_UPDATE_BY'	=> oci_result($stid, 'DB_UPDATE_BY')
					,'DB_UPDATE_ON'	=> (!empty(oci_result($stid, 'DB_UPDATE_ON')))?date('Y-m-d', strtotime(oci_result($stid, 'DB_UPDATE_ON'))):NULL
					,'Status'			=> 'reUpdate'
				    ]); 
			
			}
			else
			{
			
				$data = 
				[
				    [
					'DBCODE'			=> oci_result($stid, 'DBCODE')
					,'DBNAME'			=> oci_result($stid, 'DBNAME')
					,'DBCOMREGNO'	=> oci_result($stid, 'DBCOMREGNO')
					,'DBCUSTYPE'		=> oci_result($stid, 'DBCUSTYPE')
					,'DBCONTACT'		=> oci_result($stid, 'DBCONTACT')
					,'DBGROUP'		=> oci_result($stid, 'DBGROUP')
					,'DBTERM'			=> oci_result($stid, 'DBTERM')
					,'DBPRICEGRP'		=> oci_result($stid, 'DBPRICEGRP')
					,'DBFINCHG'		=> oci_result($stid, 'DBFINCHG')
					,'DBLIMIT'			=> oci_result($stid, 'DBLIMIT')
					,'DB_CURBAL'		=> oci_result($stid, 'DB_CURBAL')
					,'DB_DEPOSIT'		=> oci_result($stid, 'DB_DEPOSIT')
					,'DBADDR1'		=> oci_result($stid, 'DBADDR1')
					,'DBADDR2'		=> oci_result($stid, 'DBADDR2')
					,'DBADDR3'		=> oci_result($stid, 'DBADDR3')
					,'DBADDR4'		=> oci_result($stid, 'DBADDR4')
					,'DBPOSTCODE'		=> oci_result($stid, 'DBPOSTCODE')
					,'DBSTATECODE'	=> oci_result($stid, 'DBSTATECODE')
					,'DBCOUNTRYCODE'	=> oci_result($stid, 'DBCOUNTRYCODE')
					,'DBTEL1'			=> oci_result($stid, 'DBTEL1')
					,'DBTEL2'			=> oci_result($stid, 'DBTEL2')
					,'DBFAX'			=> oci_result($stid, 'DBFAX')
					,'DBHPHONE_NO'	=> oci_result($stid, 'DBHPHONE_NO')
					,'DBEMAIL'			=> oci_result($stid, 'DBEMAIL')
					,'IC_NO'			=> trim('SMB-'.oci_result($stid, 'IC_NO'))
					,'OLD_IC_NO'		=> oci_result($stid, 'OLD_IC_NO')
					,'NAME_TITLE1'		=> oci_result($stid, 'NAME_TITLE1')
					,'NAME_TITLE2'		=> oci_result($stid, 'NAME_TITLE2')
					,'OTHER_NAME'		=> oci_result($stid, 'OTHER_NAME')
					,'BIRTH_DATE'		=> (!empty(oci_result($stid, 'BIRTH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'BIRTH_DATE'))):NULL
					,'STATE_OF_BIRTH'	=> oci_result($stid, 'STATE_OF_BIRTH')
					,'SEX'			=> oci_result($stid, 'SEX')
					,'ETHNIC_GROUP'	=> oci_result($stid, 'ETHNIC_GROUP')
					,'RELIGION'		=> oci_result($stid, 'RELIGION')
					,'MARITAL_STATUS'	=> oci_result($stid, 'MARITAL_STATUS')
					,'CITIZENSHIP'		=> oci_result($stid, 'CITIZENSHIP')
					,'MOTHER_MAIDEN_NAME'=> oci_result($stid, 'MOTHER_MAIDEN_NAME')
					,'BLOODGRP_ABO'	=> oci_result($stid, 'BLOODGRP_ABO')
					,'BLOODGRP_RH'	=> oci_result($stid, 'BLOODGRP_RH')
					,'BLOODGRP_SUBGROUP'=> oci_result($stid, 'BLOODGRP_SUBGROUP')
					,'MEDICAL_HISTORY'	=> oci_result($stid, 'MEDICAL_HISTORY')
					,'FAMILY_HISTORY'	=> oci_result($stid, 'FAMILY_HISTORY')
					,'SOCIAL_LIFE'		=> oci_result($stid, 'SOCIAL_LIFE')
					,'ALLERGY'			=> oci_result($stid, 'ALLERGY')
					,'ACTIVE_CODE'		=> oci_result($stid, 'ACTIVE_CODE')
					,'REGISTRATION_DATE'=> (!empty(oci_result($stid, 'REGISTRATION_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'REGISTRATION_DATE'))):NULL
					,'REGISTRATION_TIME'=> (!empty(oci_result($stid, 'REGISTRATION_TIME')))?date('Y-m-d H:i:s', strtotime(oci_result($stid, 'REGISTRATION_TIME'))):NULL
					,'LAST_ADMIT_DATE'	=> (!empty(oci_result($stid, 'LAST_ADMIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_ADMIT_DATE'))):NULL
					,'LAST_VISIT_DATE'	=>  (!empty(oci_result($stid, 'LAST_VISIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_VISIT_DATE'))):NULL
					,'LAST_DISCHARGE_TYPE'=> oci_result($stid, 'LAST_DISCHARGE_TYPE')
					,'LAST_DISCH_DATE'	=> (!empty(oci_result($stid, 'LAST_DISCH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_DISCH_DATE'))):NULL
					,'RHESUS'			=> oci_result($stid, 'RHESUS')
					,'GRAVIDA'		=> oci_result($stid, 'GRAVIDA')
					,'PARA'			=> oci_result($stid, 'PARA')
					,'SPOKEN_LANGUAGE1'=> oci_result($stid, 'SPOKEN_LANGUAGE1')
					,'SPOKEN_LANGUAGE2'=> oci_result($stid, 'SPOKEN_LANGUAGE2')
					,'PATIENT_OCCUPATION_CODE'=> oci_result($stid, 'PATIENT_OCCUPATION_CODE')
					,'SOCSO_NO'		=> oci_result($stid, 'SOCSO_NO')
					,'COMMENT_1'		=> oci_result($stid, 'COMMENT_1')
					,'COMMENT_2'		=> oci_result($stid, 'COMMENT_2')
					,'MEDICAL_FOLDER'	=> oci_result($stid, 'MEDICAL_FOLDER')
					,'FOLDER_ID'		=> oci_result($stid, 'FOLDER_ID')
					,'FOLDER_LOCATION'	=> oci_result($stid, 'FOLDER_LOCATION')
					,'MERGED_DBCODE'	=> oci_result($stid, 'MERGED_DBCODE')
					,'REC_FLAG'		=> oci_result($stid, 'REC_FLAG')
					,'PAYER_TYPE'		=> oci_result($stid, 'PAYER_TYPE')
					,'PAYER_CODE'		=> oci_result($stid, 'PAYER_CODE')
					,'PCMT_CODE1'		=> oci_result($stid, 'PCMT_CODE1')
					,'PCMT_CODE2'		=> oci_result($stid, 'PCMT_CODE2')
					,'PCMT_CODE3'		=> oci_result($stid, 'PCMT_CODE3')
					,'PCMT_CODE4'		=> oci_result($stid, 'PCMT_CODE4')
					,'PCMT_CODE5'		=> oci_result($stid, 'PCMT_CODE5')
					,'PCMT_FTEXT'		=> oci_result($stid, 'PCMT_FTEXT')
					,'DB_UPDATE_BY'	=> oci_result($stid, 'DB_UPDATE_BY')
					,'DB_UPDATE_ON'	=> (!empty(oci_result($stid, 'DB_UPDATE_ON')))?date('Y-m-d', strtotime(oci_result($stid, 'DB_UPDATE_ON'))):NULL
				    ]
				];
				DB::connection('hclab')->table('PatientMaster')->insert($data);
			}
			
		}
		oci_close($SMBconn);
		echo "<br>Today, Total Patient  ".$x ." <br>Done";
	}
	
	/////////// CEB HCLAB Connection
	function CEBSync()
	{
		$CEBcstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$CEBconn = oci_connect("hclab", "hclab", $CEBcstr, 'AL32UTF8');
		
		$today = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-y');
		$sql = "SELECT 	DBCODE, DBNAME, DBCOMREGNO, DBCUSTYPE, DBCONTACT, DBGROUP ,DBTERM ,DBPRICEGRP ,DBFINCHG ,DBLIMIT ,DB_CURBAL ,DB_DEPOSIT ,DBADDR1 ,DBADDR2 ,DBADDR3 ,DBADDR4 ,DBPOSTCODE		
				,DBSTATECODE	,DBCOUNTRYCODE ,DBTEL1 ,DBTEL2 ,DBFAX ,DBHPHONE_NO ,DBEMAIL ,IC_NO ,OLD_IC_NO ,NAME_TITLE1 ,NAME_TITLE2 ,OTHER_NAME ,TO_CHAR(BIRTH_DATE, 'yyyy-mm-dd') AS BIRTH_DATE ,STATE_OF_BIRTH	
				,SEX	,ETHNIC_GROUP ,RELIGION ,MARITAL_STATUS ,CITIZENSHIP ,MOTHER_MAIDEN_NAME ,BLOODGRP_ABO ,BLOODGRP_RH ,BLOODGRP_SUBGROUP ,MEDICAL_HISTORY ,FAMILY_HISTORY ,SOCIAL_LIFE ,ALLERGY ,ACTIVE_CODE		
				,TO_CHAR(REGISTRATION_DATE, 'yyyy-mm-dd') AS REGISTRATION_DATE
				,TO_CHAR(REGISTRATION_TIME, 'yyyy-mm-dd') AS REGISTRATION_TIME
				,TO_CHAR(LAST_ADMIT_DATE, 'yyyy-mm-dd') AS LAST_ADMIT_DATE
				,TO_CHAR(LAST_VISIT_DATE, 'yyyy-mm-dd') AS LAST_VISIT_DATE
				,LAST_DISCHARGE_TYPE
				,TO_CHAR(LAST_DISCH_DATE, 'yyyy-mm-dd') AS LAST_DISCH_DATE
				,RHESUS ,GRAVIDA ,PARA ,SPOKEN_LANGUAGE1	,SPOKEN_LANGUAGE2 ,PATIENT_OCCUPATION_CODE ,SOCSO_NO ,COMMENT_1 ,COMMENT_2 ,MEDICAL_FOLDER ,FOLDER_ID ,FOLDER_LOCATION ,MERGED_DBCODE ,REC_FLAG ,PAYER_TYPE		
				,PAYER_CODE ,PCMT_CODE1 ,PCMT_CODE2	 ,PCMT_CODE3 ,PCMT_CODE4 ,PCMT_CODE5 ,PCMT_FTEXT ,DB_UPDATE_BY	
				,TO_CHAR(DB_UPDATE_ON, 'yyyy-mm-dd') AS DB_UPDATE_ON
			FROM cust_master where  DB_UPDATE_ON LIKE '".strtoupper($today)."'  "; 
		
		$stid = oci_parse($CEBconn, $sql);
		oci_execute($stid);
		echo "Start";
		$x = 1;
		while (oci_fetch($stid)) {
			
			//check if exsist 
			 $count  = DB::connection('hclab')->table('PatientMaster')->where('DBCODE', 'LIKE', oci_result($stid, 'DBCODE'))->get(array('Id'));
			
			$x ++;
			if(count($count) != 0)
			{
				//echo "<BR>".$count[0]->Id."<BR>";
				DB::connection('hclab')->table('PatientMaster')
				->where('Id',$count[0]->Id)
				->update([
					'DBNAME'			=> oci_result($stid, 'DBNAME')
					,'DBCOMREGNO'	=> oci_result($stid, 'DBCOMREGNO')
					,'DBCUSTYPE'		=> oci_result($stid, 'DBCUSTYPE')
					,'DBCONTACT'		=> oci_result($stid, 'DBCONTACT')
					,'DBGROUP'		=> oci_result($stid, 'DBGROUP')
					,'DBTERM'			=> oci_result($stid, 'DBTERM')
					,'DBPRICEGRP'		=> oci_result($stid, 'DBPRICEGRP')
					,'DBFINCHG'		=> oci_result($stid, 'DBFINCHG')
					,'DBLIMIT'			=> oci_result($stid, 'DBLIMIT')
					,'DB_CURBAL'		=> oci_result($stid, 'DB_CURBAL')
					,'DB_DEPOSIT'		=> oci_result($stid, 'DB_DEPOSIT')
					,'DBADDR1'		=> oci_result($stid, 'DBADDR1')
					,'DBADDR2'		=> oci_result($stid, 'DBADDR2')
					,'DBADDR3'		=> oci_result($stid, 'DBADDR3')
					,'DBADDR4'		=> oci_result($stid, 'DBADDR4')
					,'DBPOSTCODE'		=> oci_result($stid, 'DBPOSTCODE')
					,'DBSTATECODE'	=> oci_result($stid, 'DBSTATECODE')
					,'DBCOUNTRYCODE'	=> oci_result($stid, 'DBCOUNTRYCODE')
					,'DBTEL1'			=> oci_result($stid, 'DBTEL1')
					,'DBTEL2'			=> oci_result($stid, 'DBTEL2')
					,'DBFAX'			=> oci_result($stid, 'DBFAX')
					,'DBHPHONE_NO'	=> oci_result($stid, 'DBHPHONE_NO')
					,'DBEMAIL'			=> oci_result($stid, 'DBEMAIL')
					,'IC_NO'			=> trim('CEB-'.oci_result($stid, 'IC_NO'))
					,'OLD_IC_NO'		=> oci_result($stid, 'OLD_IC_NO')
					,'NAME_TITLE1'		=> oci_result($stid, 'NAME_TITLE1')
					,'NAME_TITLE2'		=> oci_result($stid, 'NAME_TITLE2')
					,'OTHER_NAME'		=> oci_result($stid, 'OTHER_NAME')
					,'BIRTH_DATE'		=> (!empty(oci_result($stid, 'BIRTH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'BIRTH_DATE'))):NULL
					,'STATE_OF_BIRTH'	=> oci_result($stid, 'STATE_OF_BIRTH')
					,'SEX'			=> oci_result($stid, 'SEX')
					,'ETHNIC_GROUP'	=> oci_result($stid, 'ETHNIC_GROUP')
					,'RELIGION'		=> oci_result($stid, 'RELIGION')
					,'MARITAL_STATUS'	=> oci_result($stid, 'MARITAL_STATUS')
					,'CITIZENSHIP'		=> oci_result($stid, 'CITIZENSHIP')
					,'MOTHER_MAIDEN_NAME'=> oci_result($stid, 'MOTHER_MAIDEN_NAME')
					,'BLOODGRP_ABO'	=> oci_result($stid, 'BLOODGRP_ABO')
					,'BLOODGRP_RH'	=> oci_result($stid, 'BLOODGRP_RH')
					,'BLOODGRP_SUBGROUP'=> oci_result($stid, 'BLOODGRP_SUBGROUP')
					,'MEDICAL_HISTORY'	=> oci_result($stid, 'MEDICAL_HISTORY')
					,'FAMILY_HISTORY'	=> oci_result($stid, 'FAMILY_HISTORY')
					,'SOCIAL_LIFE'		=> oci_result($stid, 'SOCIAL_LIFE')
					,'ALLERGY'			=> oci_result($stid, 'ALLERGY')
					,'ACTIVE_CODE'		=> oci_result($stid, 'ACTIVE_CODE')
					,'REGISTRATION_DATE'=> (!empty(oci_result($stid, 'REGISTRATION_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'REGISTRATION_DATE'))):NULL
					,'REGISTRATION_TIME'=> (!empty(oci_result($stid, 'REGISTRATION_TIME')))?date('Y-m-d H:i:s', strtotime(oci_result($stid, 'REGISTRATION_TIME'))):NULL
					,'LAST_ADMIT_DATE'	=> (!empty(oci_result($stid, 'LAST_ADMIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_ADMIT_DATE'))):NULL
					,'LAST_VISIT_DATE'	=>  (!empty(oci_result($stid, 'LAST_VISIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_VISIT_DATE'))):NULL
					,'LAST_DISCHARGE_TYPE'=> oci_result($stid, 'LAST_DISCHARGE_TYPE')
					,'LAST_DISCH_DATE'	=> (!empty(oci_result($stid, 'LAST_DISCH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_DISCH_DATE'))):NULL
					,'RHESUS'			=> oci_result($stid, 'RHESUS')
					,'GRAVIDA'		=> oci_result($stid, 'GRAVIDA')
					,'PARA'			=> oci_result($stid, 'PARA')
					,'SPOKEN_LANGUAGE1'=> oci_result($stid, 'SPOKEN_LANGUAGE1')
					,'SPOKEN_LANGUAGE2'=> oci_result($stid, 'SPOKEN_LANGUAGE2')
					,'PATIENT_OCCUPATION_CODE'=> oci_result($stid, 'PATIENT_OCCUPATION_CODE')
					,'SOCSO_NO'		=> oci_result($stid, 'SOCSO_NO')
					,'COMMENT_1'		=> oci_result($stid, 'COMMENT_1')
					,'COMMENT_2'		=> oci_result($stid, 'COMMENT_2')
					,'MEDICAL_FOLDER'	=> oci_result($stid, 'MEDICAL_FOLDER')
					,'FOLDER_ID'		=> oci_result($stid, 'FOLDER_ID')
					,'FOLDER_LOCATION'	=> oci_result($stid, 'FOLDER_LOCATION')
					,'MERGED_DBCODE'	=> oci_result($stid, 'MERGED_DBCODE')
					,'REC_FLAG'		=> oci_result($stid, 'REC_FLAG')
					,'PAYER_TYPE'		=> oci_result($stid, 'PAYER_TYPE')
					,'PAYER_CODE'		=> oci_result($stid, 'PAYER_CODE')
					,'PCMT_CODE1'		=> oci_result($stid, 'PCMT_CODE1')
					,'PCMT_CODE2'		=> oci_result($stid, 'PCMT_CODE2')
					,'PCMT_CODE3'		=> oci_result($stid, 'PCMT_CODE3')
					,'PCMT_CODE4'		=> oci_result($stid, 'PCMT_CODE4')
					,'PCMT_CODE5'		=> oci_result($stid, 'PCMT_CODE5')
					,'PCMT_FTEXT'		=> oci_result($stid, 'PCMT_FTEXT')
					,'DB_UPDATE_BY'	=> oci_result($stid, 'DB_UPDATE_BY')
					,'DB_UPDATE_ON'	=> (!empty(oci_result($stid, 'DB_UPDATE_ON')))?date('Y-m-d', strtotime(oci_result($stid, 'DB_UPDATE_ON'))):NULL
					,'Status'			=> 'reUpdate'
				    ]); 
			
			}
			else
			{
			
				$data = 
				[
				    [
					'DBCODE'			=> oci_result($stid, 'DBCODE')
					,'DBNAME'			=> oci_result($stid, 'DBNAME')
					,'DBCOMREGNO'	=> oci_result($stid, 'DBCOMREGNO')
					,'DBCUSTYPE'		=> oci_result($stid, 'DBCUSTYPE')
					,'DBCONTACT'		=> oci_result($stid, 'DBCONTACT')
					,'DBGROUP'		=> oci_result($stid, 'DBGROUP')
					,'DBTERM'			=> oci_result($stid, 'DBTERM')
					,'DBPRICEGRP'		=> oci_result($stid, 'DBPRICEGRP')
					,'DBFINCHG'		=> oci_result($stid, 'DBFINCHG')
					,'DBLIMIT'			=> oci_result($stid, 'DBLIMIT')
					,'DB_CURBAL'		=> oci_result($stid, 'DB_CURBAL')
					,'DB_DEPOSIT'		=> oci_result($stid, 'DB_DEPOSIT')
					,'DBADDR1'		=> oci_result($stid, 'DBADDR1')
					,'DBADDR2'		=> oci_result($stid, 'DBADDR2')
					,'DBADDR3'		=> oci_result($stid, 'DBADDR3')
					,'DBADDR4'		=> oci_result($stid, 'DBADDR4')
					,'DBPOSTCODE'		=> oci_result($stid, 'DBPOSTCODE')
					,'DBSTATECODE'	=> oci_result($stid, 'DBSTATECODE')
					,'DBCOUNTRYCODE'	=> oci_result($stid, 'DBCOUNTRYCODE')
					,'DBTEL1'			=> oci_result($stid, 'DBTEL1')
					,'DBTEL2'			=> oci_result($stid, 'DBTEL2')
					,'DBFAX'			=> oci_result($stid, 'DBFAX')
					,'DBHPHONE_NO'	=> oci_result($stid, 'DBHPHONE_NO')
					,'DBEMAIL'			=> oci_result($stid, 'DBEMAIL')
					,'IC_NO'			=> trim('CEB-'.oci_result($stid, 'IC_NO'))
					,'OLD_IC_NO'		=> oci_result($stid, 'OLD_IC_NO')
					,'NAME_TITLE1'		=> oci_result($stid, 'NAME_TITLE1')
					,'NAME_TITLE2'		=> oci_result($stid, 'NAME_TITLE2')
					,'OTHER_NAME'		=> oci_result($stid, 'OTHER_NAME')
					,'BIRTH_DATE'		=> (!empty(oci_result($stid, 'BIRTH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'BIRTH_DATE'))):NULL
					,'STATE_OF_BIRTH'	=> oci_result($stid, 'STATE_OF_BIRTH')
					,'SEX'			=> oci_result($stid, 'SEX')
					,'ETHNIC_GROUP'	=> oci_result($stid, 'ETHNIC_GROUP')
					,'RELIGION'		=> oci_result($stid, 'RELIGION')
					,'MARITAL_STATUS'	=> oci_result($stid, 'MARITAL_STATUS')
					,'CITIZENSHIP'		=> oci_result($stid, 'CITIZENSHIP')
					,'MOTHER_MAIDEN_NAME'=> oci_result($stid, 'MOTHER_MAIDEN_NAME')
					,'BLOODGRP_ABO'	=> oci_result($stid, 'BLOODGRP_ABO')
					,'BLOODGRP_RH'	=> oci_result($stid, 'BLOODGRP_RH')
					,'BLOODGRP_SUBGROUP'=> oci_result($stid, 'BLOODGRP_SUBGROUP')
					,'MEDICAL_HISTORY'	=> oci_result($stid, 'MEDICAL_HISTORY')
					,'FAMILY_HISTORY'	=> oci_result($stid, 'FAMILY_HISTORY')
					,'SOCIAL_LIFE'		=> oci_result($stid, 'SOCIAL_LIFE')
					,'ALLERGY'			=> oci_result($stid, 'ALLERGY')
					,'ACTIVE_CODE'		=> oci_result($stid, 'ACTIVE_CODE')
					,'REGISTRATION_DATE'=> (!empty(oci_result($stid, 'REGISTRATION_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'REGISTRATION_DATE'))):NULL
					,'REGISTRATION_TIME'=> (!empty(oci_result($stid, 'REGISTRATION_TIME')))?date('Y-m-d H:i:s', strtotime(oci_result($stid, 'REGISTRATION_TIME'))):NULL
					,'LAST_ADMIT_DATE'	=> (!empty(oci_result($stid, 'LAST_ADMIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_ADMIT_DATE'))):NULL
					,'LAST_VISIT_DATE'	=>  (!empty(oci_result($stid, 'LAST_VISIT_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_VISIT_DATE'))):NULL
					,'LAST_DISCHARGE_TYPE'=> oci_result($stid, 'LAST_DISCHARGE_TYPE')
					,'LAST_DISCH_DATE'	=> (!empty(oci_result($stid, 'LAST_DISCH_DATE')))?date('Y-m-d', strtotime(oci_result($stid, 'LAST_DISCH_DATE'))):NULL
					,'RHESUS'			=> oci_result($stid, 'RHESUS')
					,'GRAVIDA'		=> oci_result($stid, 'GRAVIDA')
					,'PARA'			=> oci_result($stid, 'PARA')
					,'SPOKEN_LANGUAGE1'=> oci_result($stid, 'SPOKEN_LANGUAGE1')
					,'SPOKEN_LANGUAGE2'=> oci_result($stid, 'SPOKEN_LANGUAGE2')
					,'PATIENT_OCCUPATION_CODE'=> oci_result($stid, 'PATIENT_OCCUPATION_CODE')
					,'SOCSO_NO'		=> oci_result($stid, 'SOCSO_NO')
					,'COMMENT_1'		=> oci_result($stid, 'COMMENT_1')
					,'COMMENT_2'		=> oci_result($stid, 'COMMENT_2')
					,'MEDICAL_FOLDER'	=> oci_result($stid, 'MEDICAL_FOLDER')
					,'FOLDER_ID'		=> oci_result($stid, 'FOLDER_ID')
					,'FOLDER_LOCATION'	=> oci_result($stid, 'FOLDER_LOCATION')
					,'MERGED_DBCODE'	=> oci_result($stid, 'MERGED_DBCODE')
					,'REC_FLAG'		=> oci_result($stid, 'REC_FLAG')
					,'PAYER_TYPE'		=> oci_result($stid, 'PAYER_TYPE')
					,'PAYER_CODE'		=> oci_result($stid, 'PAYER_CODE')
					,'PCMT_CODE1'		=> oci_result($stid, 'PCMT_CODE1')
					,'PCMT_CODE2'		=> oci_result($stid, 'PCMT_CODE2')
					,'PCMT_CODE3'		=> oci_result($stid, 'PCMT_CODE3')
					,'PCMT_CODE4'		=> oci_result($stid, 'PCMT_CODE4')
					,'PCMT_CODE5'		=> oci_result($stid, 'PCMT_CODE5')
					,'PCMT_FTEXT'		=> oci_result($stid, 'PCMT_FTEXT')
					,'DB_UPDATE_BY'	=> oci_result($stid, 'DB_UPDATE_BY')
					,'DB_UPDATE_ON'	=> (!empty(oci_result($stid, 'DB_UPDATE_ON')))?date('Y-m-d', strtotime(oci_result($stid, 'DB_UPDATE_ON'))):NULL
				    ]
				];
				DB::connection('hclab')->table('PatientMaster')->insert($data);
			}
			
		}
		oci_close($CEBconn);
		echo "<br>Today, Total Patient  ".$x ." <br>Done";
	}
	
	
	
}
