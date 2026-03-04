<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentHistory extends Model
{
	public static function getTransactionByQueue($queueId = null)
	{
		if( $queueId == null) return 'Missing Id';
		return DB::connection('CMS')->table('PaymentHistory')
				->where('PaymentHistory.IdQueue',$queueId)
				->leftJoin('QueueStatus','PaymentHistory.Status','=','QueueStatus.Id')
				->leftJoin('Transactions','PaymentHistory.IdTransaction','=','Transactions.Id')
				->leftJoin('Eros.Company','CMS.PaymentHistory.BillTo','=','Eros.Company.Id')
				//->leftJoin('Companies','Transactions.IdCompany','=','Companies.Id')
				//->leftJoin('PriceCode','Transactions.IdPriceCode','=','PriceCode.Id')
				->get(array('Transactions.CodeItemPrice','Transactions.DescriptionItemPrice', 'Transactions.PriceGroupItemPrice',  'PaymentHistory.*', 'QueueStatus.Name as QueueStatus','Eros.Company.Name as CopanyBillTo'));	
	}

	public static function getTransactionByStatus($queueId, $providerType, $ops )
	{
		if( $queueId == null || $providerType == null) return 'Missing Id';
		return DB::connection('CMS')->table('PaymentHistory')
				->where('IdQueue',$queueId)
				->where('ProviderType', $ops , $providerType)
				->get(array('Status'));
	
	}

	public static function postUpdate($request,$id)
	{
	
	}
	

	
	
}
