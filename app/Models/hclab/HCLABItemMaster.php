<?php

namespace App\Models\hclab;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;



class HCLABItemMaster extends Model
{
	
	/////////// TUAZON HCLAB Connection
	function TuazonItemSync()
	{
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');
		
		$today = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-y');
		$sql = "SELECT 
				IMH_CODE
				,IMH_DESC
				,IMH_TYPE
				,IMH_PDGROUP
				,IMH_BILLCODE
				,IMH_DEPT_CODE
				,IMH_HOSTCODE
				,IMH_INSURANCE_CODE
				,IMH_STKUOM
				,IMH_STKITEM_FLAG
				,IMH_TAXABLE
				,IMH_YTD_SQTY
				,IMH_YTD_SAMT
				,IMH_STD_COST
				,IMH_CURR_P1
				,TO_CHAR(IMH_EFD_P1, 'yyyy-mm-dd') AS IMH_EFD_P1
				,IMH_PREV_P1
				,IMH_CURR_P2
				,TO_CHAR(IMH_EFD_P2, 'yyyy-mm-dd') AS IMH_EFD_P2
				,IMH_PREV_P2
				,IMH_CURR_P3
				,TO_CHAR(IMH_EFD_P3, 'yyyy-mm-dd') AS IMH_EFD_P3
				,IMH_PREV_P3
				,IMH_FIXED_PRICE
				,IMH_REC_FLAG
				,IMH_UPDATE_BY
				,TO_CHAR(IMH_UPDATE_ON, 'yyyy-mm-dd') AS IMH_UPDATE_ON
			FROM ITEM_MASTERH where  IMH_UPDATE_ON LIKE '".strtoupper($today)."'   "; //where  IMH_UPDATE_ON LIKE '".strtoupper($today)."'
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		echo "Start";
		$x = 1;
		while (oci_fetch($stid)) {
			
			//check if exsist 
			 $count  = DB::connection('hclab')->table('ItemMaster')->where('IMH_CODE', 'LIKE', oci_result($stid, 'IMH_CODE'))->get(array('Id'));
			
			$x ++;
			if(count($count) != 0)
			{
				//echo "<BR>".$count[0]->Id."<BR>";
				DB::connection('hclab')->table('ItemMaster')
				->where('Id',$count[0]->Id)
				->update([
					'IMH_DESC'			=> oci_result($stid, 'IMH_DESC')
					,'IMH_TYPE'			=> oci_result($stid, 'IMH_TYPE')
					,'IMH_PDGROUP'		=> oci_result($stid, 'IMH_PDGROUP')
					,'IMH_BILLCODE'		=> oci_result($stid, 'IMH_BILLCODE')
					,'IMH_DEPT_CODE'		=> oci_result($stid, 'IMH_DEPT_CODE')
					,'IMH_HOSTCODE'		=> oci_result($stid, 'IMH_HOSTCODE')
					,'IMH_INSURANCE_CODE'	=> oci_result($stid, 'IMH_INSURANCE_CODE')
					,'IMH_STKUOM'			=> oci_result($stid, 'IMH_STKUOM')
					,'IMH_STKITEM_FLAG'	=> oci_result($stid, 'IMH_STKITEM_FLAG')
					,'IMH_TAXABLE'			=> oci_result($stid, 'IMH_TAXABLE')
					,'IMH_YTD_SQTY'		=> oci_result($stid, 'IMH_YTD_SQTY')
					,'IMH_YTD_SAMT'		=> oci_result($stid, 'IMH_YTD_SAMT')
					,'IMH_STD_COST'		=> oci_result($stid, 'IMH_STD_COST')
					,'IMH_CURR_P1'			=> oci_result($stid, 'IMH_CURR_P1')
					,'IMH_EFD_P1'			=> oci_result($stid, 'IMH_EFD_P1')
					,'IMH_PREV_P1'			=> oci_result($stid, 'IMH_PREV_P1')
					,'IMH_CURR_P2'			=> oci_result($stid, 'IMH_CURR_P2')
					,'IMH_EFD_P2'			=> oci_result($stid, 'IMH_EFD_P2')
					,'IMH_PREV_P2'			=> oci_result($stid, 'IMH_PREV_P2')
					,'IMH_CURR_P3'			=> oci_result($stid, 'IMH_CURR_P3')
					,'IMH_EFD_P3'			=> oci_result($stid, 'IMH_EFD_P3')
					,'IMH_PREV_P3'			=> oci_result($stid, 'IMH_PREV_P3')
					,'IMH_FIXED_PRICE'		=> oci_result($stid, 'IMH_FIXED_PRICE')
					,'IMH_REC_FLAG'		=> oci_result($stid, 'IMH_REC_FLAG')
					,'IMH_UPDATE_BY'		=> oci_result($stid, 'IMH_UPDATE_BY')
					,'IMH_UPDATE_ON'		=> oci_result($stid, 'IMH_UPDATE_ON')
					,'DTStatus'			=> 'reUpdate'
					,'CEBStatus'			=> 'reUpdate'
					,'SMBStatus'			=> 'reUpdate'
				    ]); 
			
			}
			else
			{
			
				$data = 
				[
				    [
					'IMH_CODE'			=> oci_result($stid, 'IMH_CODE')
					,'IMH_DESC'			=> oci_result($stid, 'IMH_DESC')
					,'IMH_TYPE'			=> oci_result($stid, 'IMH_TYPE')
					,'IMH_PDGROUP'		=> oci_result($stid, 'IMH_PDGROUP')
					,'IMH_BILLCODE'		=> oci_result($stid, 'IMH_BILLCODE')
					,'IMH_DEPT_CODE'		=> oci_result($stid, 'IMH_DEPT_CODE')
					,'IMH_HOSTCODE'		=> oci_result($stid, 'IMH_HOSTCODE')
					,'IMH_INSURANCE_CODE'	=> oci_result($stid, 'IMH_INSURANCE_CODE')
					,'IMH_STKUOM'			=> oci_result($stid, 'IMH_STKUOM')
					,'IMH_STKITEM_FLAG'	=> oci_result($stid, 'IMH_STKITEM_FLAG')
					,'IMH_TAXABLE'			=> oci_result($stid, 'IMH_TAXABLE')
					,'IMH_YTD_SQTY'		=> oci_result($stid, 'IMH_YTD_SQTY')
					,'IMH_YTD_SAMT'		=> oci_result($stid, 'IMH_YTD_SAMT')
					,'IMH_STD_COST'		=> oci_result($stid, 'IMH_STD_COST')
					,'IMH_CURR_P1'			=> oci_result($stid, 'IMH_CURR_P1')
					,'IMH_EFD_P1'			=> oci_result($stid, 'IMH_EFD_P1')
					,'IMH_PREV_P1'			=> oci_result($stid, 'IMH_PREV_P1')
					,'IMH_CURR_P2'			=> oci_result($stid, 'IMH_CURR_P2')
					,'IMH_EFD_P2'			=> oci_result($stid, 'IMH_EFD_P2')
					,'IMH_PREV_P2'			=> oci_result($stid, 'IMH_PREV_P2')
					,'IMH_CURR_P3'			=> oci_result($stid, 'IMH_CURR_P3')
					,'IMH_EFD_P3'			=> oci_result($stid, 'IMH_EFD_P3')
					,'IMH_PREV_P3'			=> oci_result($stid, 'IMH_PREV_P3')
					,'IMH_FIXED_PRICE'		=> oci_result($stid, 'IMH_FIXED_PRICE')
					,'IMH_REC_FLAG'		=> oci_result($stid, 'IMH_REC_FLAG')
					,'IMH_UPDATE_BY'		=> oci_result($stid, 'IMH_UPDATE_BY')
					,'IMH_UPDATE_ON'		=> oci_result($stid, 'IMH_UPDATE_ON')
				    ]
				];
				DB::connection('hclab')->table('ItemMaster')->insert($data);
			}
			
			$this->TuazonItemDetailsSync(oci_result($stid, 'IMH_CODE'));
			
		}
		oci_close($conn);
		echo "<br>Total Count Package  ".$x ." <br>Done";
	}
	/////////// TUAZON Item Master Details  HCLAB Connection
	function TuazonItemDetailsSync($pcode = NULL)
	{
		if(empty($pcode)){ return "Missing Package Code!"; die(); }
	
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');
		
		$today = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-M-y');
		$sql = "SELECT 
				IMD_PKG_CODE
				,IMD_PKG_ITEM
				,IMD_PKG_FLAG1
				,IMD_PKG_FLAG2
				,IMD_DISP_SEQ
				,IMD_PKG_SFX
			FROM ITEM_MASTERD where  IMD_PKG_CODE like '".$pcode."'   "; //where  IMH_UPDATE_ON LIKE '".strtoupper($today)."'
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		echo "Start";
		$x = 1;
		while (oci_fetch($stid)) {
			
			//check if exsist 
			 $count  = DB::connection('hclab')->table('ItemMasterDetails')->where('IMD_PKG_CODE', 'LIKE', oci_result($stid, 'IMD_PKG_CODE')) ->get(array('Id'));
			
			$x ++;
			if(count($count) != 0) // delete 
			{
				//echo "<BR>".$count[0]->Id."<BR>";
				DB::connection('hclab')->table('ItemMasterDetails')
				->where('IMD_PKG_CODE', oci_result($stid, 'IMD_PKG_CODE') )
				->delete(); 
			
			}
			// add all items per package
			$data = 
			[
			    [
				'IMD_PKG_CODE'		=> oci_result($stid, 'IMD_PKG_CODE')
				,'IMD_PKG_ITEM'		=> oci_result($stid, 'IMD_PKG_ITEM')
				,'IMD_PKG_FLAG1'		=> oci_result($stid, 'IMD_PKG_FLAG1')
				,'IMD_PKG_FLAG2'		=> oci_result($stid, 'IMD_PKG_FLAG2')
				,'IMD_DISP_SEQ'		=> oci_result($stid, 'IMD_DISP_SEQ')
				,'IMD_PKG_SFX'			=> oci_result($stid, 'IMD_PKG_SFX')
			    ]
			];
			DB::connection('hclab')->table('ItemMasterDetails')->insert($data);
			
			
		}
		oci_close($conn);
		echo "<br>Total Count Package  ".$x ." <br>Done";
	}
	
	/////////// CEB HCLAB Connection
	function ItemMasterCEBSync()
	{
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');
		
		echo "Start";
		//select all reUpdate CEBStatus
		$datas = DB::connection('hclab')->table('ItemMaster')->where('CEBStatus' , 'LIKE', 'reUpdate')->get(array('*'));
		foreach($datas as $data)
		{
			echo "<br>".$data->Id;
			//search CEBU HCLAB if this item already exist
			$sql = "SELECT IMH_CODE FROM ITEM_MASTERH WHERE IMH_CODE LIKE '".$data->IMH_CODE."'  ";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			$withItem = '';
			while (oci_fetch($stid)) 
			{
				$withItem = oci_result($stid, 'IMH_CODE');
			}
			
			if(empty($withItem))
			{
				$InsertSQL = "INSERT INTO ITEM_MASTERH 
					(
						IMH_CODE
						,IMH_DESC
						,IMH_TYPE
						,IMH_PDGROUP
						,IMH_BILLCODE
						,IMH_DEPT_CODE
						,IMH_HOSTCODE
						,IMH_INSURANCE_CODE
						,IMH_STKUOM
						,IMH_STKITEM_FLAG
						,IMH_TAXABLE
						,IMH_YTD_SQTY
						,IMH_YTD_SAMT
						,IMH_STD_COST
						,IMH_CURR_P1
						,IMH_EFD_P1
						,IMH_PREV_P1
						,IMH_CURR_P2
						,IMH_EFD_P2
						,IMH_PREV_P2
						,IMH_CURR_P3
						,IMH_EFD_P3
						,IMH_PREV_P3
						,IMH_FIXED_PRICE
						,IMH_REC_FLAG
						,IMH_UPDATE_BY
						,IMH_UPDATE_ON
					) 
				VALUES 
					(
						:IMH_CODE
						,:IMH_DESC
						,:IMH_TYPE
						,:IMH_PDGROUP
						,:IMH_BILLCODE
						,:IMH_DEPT_CODE
						,:IMH_HOSTCODE
						,:IMH_INSURANCE_CODE
						,:IMH_STKUOM
						,:IMH_STKITEM_FLAG
						,:IMH_TAXABLE
						,:IMH_YTD_SQTY
						,:IMH_YTD_SAMT
						,:IMH_STD_COST
						,:IMH_CURR_P1
						,:IMH_EFD_P1
						,:IMH_PREV_P1
						,:IMH_CURR_P2
						,:IMH_EFD_P2
						,:IMH_PREV_P2
						,:IMH_CURR_P3
						,:IMH_EFD_P3
						,:IMH_PREV_P3
						,:IMH_FIXED_PRICE
						,:IMH_REC_FLAG
						,:IMH_UPDATE_BY
						,:IMH_UPDATE_ON
					)";
					
					$compiledMaster = oci_parse($conn, $InsertSQL);
					
					$IMH_CODE			= $data->IMH_CODE;
					$IMH_DESC			= $data->IMH_DESC;
					$IMH_TYPE			= $data->IMH_TYPE;
					$IMH_PDGROUP			= $data->IMH_PDGROUP;
					$IMH_BILLCODE		= $data->IMH_BILLCODE;
					$IMH_DEPT_CODE		= $data->IMH_DEPT_CODE;
					$IMH_HOSTCODE		= $data->IMH_HOSTCODE;
					$IMH_INSURANCE_CODE	= $data->IMH_INSURANCE_CODE;
					$IMH_STKUOM			= $data->IMH_STKUOM;
					$IMH_STKITEM_FLAG		= $data->IMH_STKITEM_FLAG;
					$IMH_TAXABLE			= $data->IMH_TAXABLE;
					$IMH_YTD_SQTY		= $data->IMH_YTD_SQTY;
					$IMH_YTD_SAMT		= $data->IMH_YTD_SAMT;
					$IMH_STD_COST		= $data->IMH_STD_COST;
					$IMH_CURR_P1			= $data->IMH_CURR_P1;
					$IMH_EFD_P1			=  \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P1)->format('d-M-y');
					$IMH_PREV_P1			= $data->IMH_PREV_P1;
					$IMH_CURR_P2			= $data->IMH_CURR_P2;
					$IMH_EFD_P2			= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P2)->format('d-M-y');
					$IMH_PREV_P2			= $data->IMH_PREV_P2;
					$IMH_CURR_P3			= $data->IMH_CURR_P3;
					$IMH_EFD_P3			= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P3)->format('d-M-y');
					$IMH_PREV_P3			= $data->IMH_PREV_P3;
					$IMH_FIXED_PRICE		= $data->IMH_FIXED_PRICE;
					$IMH_REC_FLAG		= $data->IMH_REC_FLAG;
					$IMH_UPDATE_BY		= $data->IMH_UPDATE_BY;
					$IMH_UPDATE_ON		= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_UPDATE_ON)->format('d-M-y');
				
					oci_bind_by_name($compiledMaster, ":IMH_CODE", $IMH_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_DESC", $IMH_DESC);
					oci_bind_by_name($compiledMaster, ":IMH_TYPE", $IMH_TYPE);
					oci_bind_by_name($compiledMaster, ":IMH_PDGROUP", $IMH_PDGROUP);
					oci_bind_by_name($compiledMaster, ":IMH_BILLCODE", $IMH_BILLCODE);
					oci_bind_by_name($compiledMaster, ":IMH_DEPT_CODE", $IMH_DEPT_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_HOSTCODE", $IMH_HOSTCODE);
					oci_bind_by_name($compiledMaster, ":IMH_INSURANCE_CODE", $IMH_INSURANCE_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_STKUOM", $IMH_STKUOM);
					oci_bind_by_name($compiledMaster, ":IMH_STKITEM_FLAG", $IMH_STKITEM_FLAG);
					oci_bind_by_name($compiledMaster, ":IMH_TAXABLE", $IMH_TAXABLE);
					oci_bind_by_name($compiledMaster, ":IMH_YTD_SQTY", $IMH_YTD_SQTY);
					oci_bind_by_name($compiledMaster, ":IMH_YTD_SAMT", $IMH_YTD_SAMT);
					oci_bind_by_name($compiledMaster, ":IMH_STD_COST", $IMH_STD_COST);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P1", $IMH_CURR_P1);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P1", $IMH_EFD_P1);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P1", $IMH_PREV_P1);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P2", $IMH_CURR_P2);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P2", $IMH_EFD_P2);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P2", $IMH_PREV_P2);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P3", $IMH_CURR_P3);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P3", $IMH_EFD_P3);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P3", $IMH_PREV_P3);
					oci_bind_by_name($compiledMaster, ":IMH_FIXED_PRICE", $IMH_FIXED_PRICE);
					oci_bind_by_name($compiledMaster, ":IMH_REC_FLAG", $IMH_REC_FLAG);
					oci_bind_by_name($compiledMaster, ":IMH_UPDATE_BY", $IMH_UPDATE_BY);
					oci_bind_by_name($compiledMaster, ":IMH_UPDATE_ON", $IMH_UPDATE_ON);
			
					$result = oci_execute($compiledMaster);
					if (!$result){
					    $e = oci_error($result);  // For oci_execute errors pass the statement handle
					    print htmlentities($e['message']);
					    print "\n<pre>\n";
					    print htmlentities($e['sqltext']);
					    printf("\n%".($e['offset']+1)."s", "^");
					    print  "\n</pre>\n";
					}else{
						$InsertDetailsSQL = "INSERT INTO ITEM_MASTERD (IMD_PKG_CODE, IMD_PKG_ITEM, IMD_PKG_FLAG1, IMD_PKG_FLAG2, IMD_DISP_SEQ, IMD_PKG_SFX) VALUES
						(:IMD_PKG_CODE, :IMD_PKG_ITEM, :IMD_PKG_FLAG1, :IMD_PKG_FLAG2, :IMD_DISP_SEQ, :IMD_PKG_SFX)";
						
						$itemDetailsDatas = DB::connection('hclab')->table('ItemMasterDetails')->where('IMD_PKG_CODE', $data->IMH_CODE)->get(array('*'));
						
						foreach($itemDetailsDatas as $itemD )
						{
							$IMD_PKG_CODE		= $itemD->IMD_PKG_CODE;
							$IMD_PKG_ITEM		= $itemD->IMD_PKG_ITEM;
							$IMD_PKG_FLAG1		= $itemD->IMD_PKG_FLAG1;
							$IMD_PKG_FLAG2		= $itemD->IMD_PKG_FLAG2;
							$IMD_DISP_SEQ		= $itemD->IMD_DISP_SEQ;
							$IMD_PKG_SFX			= $itemD->IMD_PKG_SFX;
							
							$compiledMasterD = oci_parse($conn, $InsertDetailsSQL);
							
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_CODE", $IMD_PKG_CODE);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_ITEM", $IMD_PKG_ITEM);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_FLAG1", $IMD_PKG_FLAG1);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_FLAG2", $IMD_PKG_FLAG2);
							oci_bind_by_name($compiledMasterD, ":IMD_DISP_SEQ", $IMD_DISP_SEQ);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_SFX", $IMD_PKG_SFX);
							
							
							$resultD = oci_execute($compiledMasterD);
							if (!$resultD){
							    $e = oci_error($resultD);  // For oci_execute errors pass the statement handle
							    print htmlentities($e['message']);
							    print "\n<pre>\n";
							    print htmlentities($e['sqltext']);
							    printf("\n%".($e['offset']+1)."s", "^");
							    print  "\n</pre>\n";
							}
							
						}
						DB::connection('hclab')->table('ItemMaster')->where('Id', $data->Id)->update(['CEBStatus' => 'APPEND']);
					}
			
			}else{ // exist Item Package Code
				DB::connection('hclab')->table('ItemMaster')->where('Id', $data->Id)->update(['CEBStatus' => 'EXIST']);
			}
		
		}
		
	}
	
	/////////// SMB HCLAB Connection
	function ItemMasterSMBSync()
	{
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("hclab", "hclab", $cstr, 'AL32UTF8');
		
		echo "Start";
		//select all reUpdate CEBStatus
		$datas = DB::connection('hclab')->table('ItemMaster')->where('SMBStatus' , 'LIKE', 'reUpdate')->get(array('*'));
		foreach($datas as $data)
		{
			echo "<br>".$data->Id;
			//search CEBU HCLAB if this item already exist
			$sql = "SELECT IMH_CODE FROM ITEM_MASTERH WHERE IMH_CODE LIKE '".$data->IMH_CODE."'  ";
			$stid = oci_parse($conn, $sql);
			oci_execute($stid);
			
			$withItem = '';
			while (oci_fetch($stid)) 
			{
				$withItem = oci_result($stid, 'IMH_CODE');
			}
			
			if(empty($withItem))
			{
				$InsertSQL = "INSERT INTO ITEM_MASTERH 
					(
						IMH_CODE
						,IMH_DESC
						,IMH_TYPE
						,IMH_PDGROUP
						,IMH_BILLCODE
						,IMH_DEPT_CODE
						,IMH_HOSTCODE
						,IMH_INSURANCE_CODE
						,IMH_STKUOM
						,IMH_STKITEM_FLAG
						,IMH_TAXABLE
						,IMH_YTD_SQTY
						,IMH_YTD_SAMT
						,IMH_STD_COST
						,IMH_CURR_P1
						,IMH_EFD_P1
						,IMH_PREV_P1
						,IMH_CURR_P2
						,IMH_EFD_P2
						,IMH_PREV_P2
						,IMH_CURR_P3
						,IMH_EFD_P3
						,IMH_PREV_P3
						,IMH_FIXED_PRICE
						,IMH_REC_FLAG
						,IMH_UPDATE_BY
						,IMH_UPDATE_ON
					) 
				VALUES 
					(
						:IMH_CODE
						,:IMH_DESC
						,:IMH_TYPE
						,:IMH_PDGROUP
						,:IMH_BILLCODE
						,:IMH_DEPT_CODE
						,:IMH_HOSTCODE
						,:IMH_INSURANCE_CODE
						,:IMH_STKUOM
						,:IMH_STKITEM_FLAG
						,:IMH_TAXABLE
						,:IMH_YTD_SQTY
						,:IMH_YTD_SAMT
						,:IMH_STD_COST
						,:IMH_CURR_P1
						,:IMH_EFD_P1
						,:IMH_PREV_P1
						,:IMH_CURR_P2
						,:IMH_EFD_P2
						,:IMH_PREV_P2
						,:IMH_CURR_P3
						,:IMH_EFD_P3
						,:IMH_PREV_P3
						,:IMH_FIXED_PRICE
						,:IMH_REC_FLAG
						,:IMH_UPDATE_BY
						,:IMH_UPDATE_ON
					)";
					
					$compiledMaster = oci_parse($conn, $InsertSQL);
					
					$IMH_CODE			= $data->IMH_CODE;
					$IMH_DESC			= $data->IMH_DESC;
					$IMH_TYPE			= $data->IMH_TYPE;
					$IMH_PDGROUP			= $data->IMH_PDGROUP;
					$IMH_BILLCODE		= $data->IMH_BILLCODE;
					$IMH_DEPT_CODE		= $data->IMH_DEPT_CODE;
					$IMH_HOSTCODE		= $data->IMH_HOSTCODE;
					$IMH_INSURANCE_CODE	= $data->IMH_INSURANCE_CODE;
					$IMH_STKUOM			= $data->IMH_STKUOM;
					$IMH_STKITEM_FLAG		= $data->IMH_STKITEM_FLAG;
					$IMH_TAXABLE			= $data->IMH_TAXABLE;
					$IMH_YTD_SQTY		= $data->IMH_YTD_SQTY;
					$IMH_YTD_SAMT		= $data->IMH_YTD_SAMT;
					$IMH_STD_COST		= $data->IMH_STD_COST;
					$IMH_CURR_P1			= $data->IMH_CURR_P1;
					$IMH_EFD_P1			=  \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P1)->format('d-M-y');
					$IMH_PREV_P1			= $data->IMH_PREV_P1;
					$IMH_CURR_P2			= $data->IMH_CURR_P2;
					$IMH_EFD_P2			= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P2)->format('d-M-y');
					$IMH_PREV_P2			= $data->IMH_PREV_P2;
					$IMH_CURR_P3			= $data->IMH_CURR_P3;
					$IMH_EFD_P3			= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_EFD_P3)->format('d-M-y');
					$IMH_PREV_P3			= $data->IMH_PREV_P3;
					$IMH_FIXED_PRICE		= $data->IMH_FIXED_PRICE;
					$IMH_REC_FLAG		= $data->IMH_REC_FLAG;
					$IMH_UPDATE_BY		= $data->IMH_UPDATE_BY;
					$IMH_UPDATE_ON		= \Carbon\Carbon::createFromFormat('Y-m-d', $data->IMH_UPDATE_ON)->format('d-M-y');
				
					oci_bind_by_name($compiledMaster, ":IMH_CODE", $IMH_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_DESC", $IMH_DESC);
					oci_bind_by_name($compiledMaster, ":IMH_TYPE", $IMH_TYPE);
					oci_bind_by_name($compiledMaster, ":IMH_PDGROUP", $IMH_PDGROUP);
					oci_bind_by_name($compiledMaster, ":IMH_BILLCODE", $IMH_BILLCODE);
					oci_bind_by_name($compiledMaster, ":IMH_DEPT_CODE", $IMH_DEPT_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_HOSTCODE", $IMH_HOSTCODE);
					oci_bind_by_name($compiledMaster, ":IMH_INSURANCE_CODE", $IMH_INSURANCE_CODE);
					oci_bind_by_name($compiledMaster, ":IMH_STKUOM", $IMH_STKUOM);
					oci_bind_by_name($compiledMaster, ":IMH_STKITEM_FLAG", $IMH_STKITEM_FLAG);
					oci_bind_by_name($compiledMaster, ":IMH_TAXABLE", $IMH_TAXABLE);
					oci_bind_by_name($compiledMaster, ":IMH_YTD_SQTY", $IMH_YTD_SQTY);
					oci_bind_by_name($compiledMaster, ":IMH_YTD_SAMT", $IMH_YTD_SAMT);
					oci_bind_by_name($compiledMaster, ":IMH_STD_COST", $IMH_STD_COST);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P1", $IMH_CURR_P1);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P1", $IMH_EFD_P1);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P1", $IMH_PREV_P1);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P2", $IMH_CURR_P2);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P2", $IMH_EFD_P2);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P2", $IMH_PREV_P2);
					oci_bind_by_name($compiledMaster, ":IMH_CURR_P3", $IMH_CURR_P3);
					oci_bind_by_name($compiledMaster, ":IMH_EFD_P3", $IMH_EFD_P3);
					oci_bind_by_name($compiledMaster, ":IMH_PREV_P3", $IMH_PREV_P3);
					oci_bind_by_name($compiledMaster, ":IMH_FIXED_PRICE", $IMH_FIXED_PRICE);
					oci_bind_by_name($compiledMaster, ":IMH_REC_FLAG", $IMH_REC_FLAG);
					oci_bind_by_name($compiledMaster, ":IMH_UPDATE_BY", $IMH_UPDATE_BY);
					oci_bind_by_name($compiledMaster, ":IMH_UPDATE_ON", $IMH_UPDATE_ON);
			
					$result = oci_execute($compiledMaster);
					if (!$result){
					    $e = oci_error($result);  // For oci_execute errors pass the statement handle
					    print htmlentities($e['message']);
					    print "\n<pre>\n";
					    print htmlentities($e['sqltext']);
					    printf("\n%".($e['offset']+1)."s", "^");
					    print  "\n</pre>\n";
					}else{
						$InsertDetailsSQL = "INSERT INTO ITEM_MASTERD (IMD_PKG_CODE, IMD_PKG_ITEM, IMD_PKG_FLAG1, IMD_PKG_FLAG2, IMD_DISP_SEQ, IMD_PKG_SFX) VALUES
						(:IMD_PKG_CODE, :IMD_PKG_ITEM, :IMD_PKG_FLAG1, :IMD_PKG_FLAG2, :IMD_DISP_SEQ, :IMD_PKG_SFX)";
						
						$itemDetailsDatas = DB::connection('hclab')->table('ItemMasterDetails')->where('IMD_PKG_CODE', $data->IMH_CODE)->get(array('*'));
						
						foreach($itemDetailsDatas as $itemD )
						{
							$IMD_PKG_CODE		= $itemD->IMD_PKG_CODE;
							$IMD_PKG_ITEM		= $itemD->IMD_PKG_ITEM;
							$IMD_PKG_FLAG1		= $itemD->IMD_PKG_FLAG1;
							$IMD_PKG_FLAG2		= $itemD->IMD_PKG_FLAG2;
							$IMD_DISP_SEQ		= $itemD->IMD_DISP_SEQ;
							$IMD_PKG_SFX			= $itemD->IMD_PKG_SFX;
							
							$compiledMasterD = oci_parse($conn, $InsertDetailsSQL);
							
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_CODE", $IMD_PKG_CODE);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_ITEM", $IMD_PKG_ITEM);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_FLAG1", $IMD_PKG_FLAG1);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_FLAG2", $IMD_PKG_FLAG2);
							oci_bind_by_name($compiledMasterD, ":IMD_DISP_SEQ", $IMD_DISP_SEQ);
							oci_bind_by_name($compiledMasterD, ":IMD_PKG_SFX", $IMD_PKG_SFX);
							
							
							$resultD = oci_execute($compiledMasterD);
							if (!$resultD){
							    $e = oci_error($resultD);  // For oci_execute errors pass the statement handle
							    print htmlentities($e['message']);
							    print "\n<pre>\n";
							    print htmlentities($e['sqltext']);
							    printf("\n%".($e['offset']+1)."s", "^");
							    print  "\n</pre>\n";
							}
							
						}
						DB::connection('hclab')->table('ItemMaster')->where('Id', $data->Id)->update(['SMBStatus' => 'APPEND']);
					}
			
			}else{ // exist Item Package Code
				DB::connection('hclab')->table('ItemMaster')->where('Id', $data->Id)->update(['SMBStatus' => 'EXIST']);
			}
		
		}
		
	}
	
	
	
}
