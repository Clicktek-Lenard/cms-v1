<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\cms\Transactions;
use App\Models\cms\Kiosk;
use App\Models\eros\ErosDB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		/*$companys = \App\Models\Company::where('Companies.Status', 1)
				   	->leftJoin('CompanyCategory', 'Companies.IdCategory', '=', 'CompanyCategories.Id')
				   	->get(array('Companies.Id','Companies.Code','Companies.Name','CompanyCategories.Description as Category'));
		$doctors = \App\Models\DoctorClinic::where('DoctorClinics.Status',1)
					->where('DoctorClinics.IdClinic',session('userClinic'))
					->where('Doctors.IdBU',session('userBU'))
					->leftJoin('Doctors','DoctorClinics.IdDoctor','=','Doctors.Id')
					->leftJoin('DoctorCategory','DoctorClinics.IdCategory','=','DoctorCategory.Id')
					->get(array('Doctors.Id','Doctors.FullName','DoctorCategory.Description as Category'));
		return view('cms/pages.queueCreateAdd', ['doctors' => $doctors, 'companys' => $companys]);*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
		$dataTime = date('Y-m-d H:i:s');
    	if( is_array($request->input('itemSelected')) )
		{
			foreach($request->input('itemSelected') as $item)
			{
				if( !empty($item['TransId']) )
				{
					\App\Models\Transactions::where('Id',$item['TransId'])
						->where('IdQueue',$request->input('QueueId'))
						->update([
							'Notes'		=> $item['Notes'],
						]);	
					\App\Models\TransactionLogs::where('IdTransaction',$item['TransId'])
						->lockForUpdate()
						->update([
							'Action'=> json_encode(
								array_merge(
									json_decode(\App\Models\TransactionLogs::where('IdTransaction',$item['TransId'])->get(['Action'])[0]->Action,true),
									[
										[
										'Id'		=>$dataTime,
										'Action'	=>'Update',
										'User'		=>session('username'),
										'Notes'		=> $item['Notes'],
										]
									]
								)
							)
						]);	
					
				}
				else
				{
					$amount = \App\Models\Price::getItemPrice($request->input('PriceCode'),$item['Id'])->get(array('Amount'))[0]->Amount;
					\App\Models\TransactionCounter::where('IdBU',session('userBU'))
					->lockForUpdate()
					->update(['Counter' => $counter = floatval(\App\Models\TransactionCounter::where('IdBU',session('userBU'))->lockForUpdate()->get(array('Counter'))[0]->Counter) + 1   ]);
					$transId = \App\Models\Transactions::insertGetId([
						'Code'		=> date('Y-md').'-'.$request->input('PatientCode').'-'.$counter,
						'IdQueue'	=> $request->input('QueueId'),
						'IdDoctor' 	=> $request->input('DoctorName'),
						'IdCompany'	=> $request->input('CompanyName'),
						'IdPriceCode'=> $request->input('PriceCode'),
						'IdItem'	=> $item['Id'],
						'Amount'	=> floatval($amount),
						'Status'	=> 100,
						'Notes'		=> $item['Notes'],
						'InputBy'	=> Auth::user()->Username,
					]);
					\App\Models\TransactionLogs::insert([
						'IdTransaction'	=> $transId,
						'Action' => json_encode(
							[
								[
								'Id'		=> $dataTime,
								'Action'	=> 'Create',
								'User'		=> Auth::user()->Username,
								'Code'		=> date('Y-md').'-'.$request->input('PatientCode').'-'.$counter,
								'IdQueue'	=> $request->input('QueueId'),
								'IdDoctor' 	=> $request->input('DoctorName'),
								'IdCompany'	=> $request->input('CompanyName'),
								'IdPriceCode'=> $request->input('PriceCode'),
								'IdItem'	=> $item['Id'],
								'Amount'	=> floatval($amount),
								'Status'	=> 100,
								'Notes'		=> $item['Notes'],
								]
							]
						)
					]);
					
					
					
					 
				}
				
			}
		}
		return \App\Models\Transactions::getTransactionByQueue($request->input('QueueId')); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
     	$companys = \App\Models\Company::where('Companies.Status', 1)
				   	->leftJoin('CompanyCategories', 'Companies.IdCategory', '=', 'CompanyCategories.Id')
				   	->get(array('Companies.Id','Companies.Code','Companies.Name','CompanyCategories.Description as Category'));
		$doctors = \App\Models\DoctorClinic::where('DoctorClinics.Status',1)
					->where('DoctorClinics.IdClinic',session('userClinic'))
					->where('Doctors.IdBU',session('userBU'))
					->leftJoin('Doctors','DoctorClinics.IdDoctor','=','Doctors.Id')
					->leftJoin('DoctorCategories','DoctorClinics.IdCategory','=','DoctorCategory.Id')
					->get(array('Doctors.Id','Doctors.FullName','DoctorCategory.Description as Category'));
		return view('cms/pages.queueCreateAdd', ['queueId' => $id,'doctors' => $doctors, 'companys' => $companys]);	
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	
	$datas = Transactions::getTransactionById($id);

	// not used here $EditOR = DB::connection('CMS')->table('PaymentHistory')->where('IdQueue', $id)->get();
	return view('cms/pages.queueTransactionRemove', ['datas' => $datas ]);	
	
   
	//ricky 07-13-23 to be check
	/*
	$user = Auth::user();
        $data = \App\Models\Transactions::where('Id',$id)
				->where('Status',100)
				->get(array('Id','IdQueue','IdItem','IdDoctor','IdPriceCode','IdCompany'));
				
		$companys = \App\Models\Company::where('Companies.Status', 1)
					->where('Companies.Id',$data[0]->IdCompany)
				   	->leftJoin('CompanyCategories', 'Companies.IdCategory', '=', 'CompanyCategories.Id')
				   	->get(array('Companies.Id','Companies.Code','Companies.Name','CompanyCategories.Description as Category'));
		$doctors = \App\Models\DoctorClinic::where('DoctorClinics.Status',1)
					->where('Doctors.Id',$data[0]->IdDoctor)
					->where('DoctorClinics.IdClinic',session('userClinic'))
					->where('Doctors.IdBU',session('userBU'))
					->leftJoin('Doctors','DoctorClinics.IdDoctor','=','Doctors.Id')
					->leftJoin('DoctorCategories','DoctorClinics.IdCategory','=','DoctorCategory.Id')
					->get(array('Doctors.Id','Doctors.FullName','DoctorCategory.Description as Category'));
		$packages = \App\Models\PriceCode::where('Status',1)
					->where('IdBU',session('userBU'))
					->where('IdCompany',$data[0]->IdCompany)
					->where('Id',$data[0]->IdPriceCode)
					->get(array('Id','Code','Description'));
		return view('cms/pages.queueCreateEdit', ['queueId' => $data[0]->IdQueue,'itemId' => $data[0]->IdItem ,'doctors' => $doctors, 'companys' => $companys, 'packages' => $packages ] );	
	*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
	//echo $request->input('reason');
	$idQueue = Transactions::getIdQueue($id);
	$return =  Transactions::postAsDeleted($request, $id);
	$trans = Transactions::getTransactionItemsByIdQueue($idQueue);
	$deptGroups = ErosDB::getItemMasterDeptGroup($trans);
	
	$packageItemCodes = DB::connection('Eros')
	->table('ItemMaster')
	->whereIn('Code', $trans)
	->where('Departmentgroup', 'Package')
	->pluck('Code')
	->merge(
		DB::connection('Eros')
			->table('ItemPrice')
			->whereIn('Code', $trans)
			->where('PriceGroup', 'Package')
			->pluck('Code')
		)
	->unique();

	if ($deptGroups->contains(fn($value) => strtolower($value) === 'package')) {
		$packageDeptGroups = ErosDB::getPackageDeptGroupMultiple($packageItemCodes);
		$deptGroups = $deptGroups->merge($packageDeptGroups);
	}

	$deptGroups = $deptGroups->reject(function ($value) {
		return $value === 'Package';
	});
	
	$deptGroupsArray = $deptGroups->map(function ($item) {
		if (is_string($item)) {
			return $item;
		}
		if (is_iterable($item)) {
			return collect($item)->map(function ($subItem) {
				return $subItem->DepartmentGroup ?? '';
			})->toArray();
		}
		if (is_object($item) && property_exists($item, 'DepartmentGroup')) {
			return $item->DepartmentGroup ?? '';
		}
		return '';
	})->flatten()->filter()->toArray();

	$deptGroupsArray = array_unique($deptGroupsArray);
	// FOR VITAL SIGNS
	if (($key = array_search('CONSULTATION', $deptGroupsArray)) !== false) {
		$deptGroupsArray[$key] = 'VITAL';
	}
	
	$deptGroupsString = implode(', ', $deptGroupsArray);
	// dd($deptGroupsString);
	Kiosk::where('IdQueueCMS', $idQueue)->update([
		'Station' => $deptGroupsString
	]);
	
	if($return == '1')
	{
		return 'Successfully Remove';
	}
	else
	{
		return 'Error found., pls contact you administrator.';
	}

	
	/* 2023-07-13 remove
		if( is_array($request->input('itemSelected')) )
		{
			\App\Models\Transactions::queueUpdate($request,$id);
		}
		else
		{
			\App\Models\Transactions::queueDelete($id);
		}
		return \App\Models\Transactions::getTransactionByQueue($request->input('QueueId'));
	*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
