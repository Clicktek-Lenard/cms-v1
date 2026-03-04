<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TransactionTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$companys = \App\Models\Company::where('Companies.Status', 1)
				   	->leftJoin('CompanyCategories', 'Companies.IdCategory', '=', 'CompanyCategories.Id')
				   	->get(array('Companies.Id','Companies.Code','Companies.Name','CompanyCategories.Description as Category'));
		$doctors = \App\Models\DoctorClinic::where('DoctorClinics.Status',1)
					->where('DoctorClinics.IdClinic',session('userClinic'))
					->where('Doctors.IdBU',session('userBU'))
					->leftJoin('Doctors','DoctorClinics.IdDoctor','=','Doctors.Id')
					->leftJoin('DoctorCategories','DoctorClinics.IdCategory','=','DoctorCategories.Id')
					->get(array('Doctors.Id','Doctors.FullName','DoctorCategories.Description as Category'));
		return view('cms/pages.queueTempCreateAdd', ['doctors' => $doctors, 'companys' => $companys]);
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
        $user = Auth::user();
		// delete
		\App\Models\TransactionTemp::where(function($q) use ($request,$user)
		{
			if( $request->input('Id') != 0)
			{
				$q->where('Id', $request->input('Id'));	
			}
			else
			{
				$q->where('Date',date('Y-m-d'));
				$q->where('InputId',$user->Id);
				$q->where('IdDoctor',$request->input('DoctorName'));
				$q->where('IdCompany',$request->input('CompanyName'));
				$q->where('IdPriceCode',$request->input('PriceCode'));	
			}
		})
		->delete();
		// insert 
		if( is_array($request->input('itemSelected')) )
		{
			foreach($request->input('itemSelected') as $item)
			{
				\App\Models\TransactionTemp::insertGetId([
					'Date' 		=> date('Y-m-d'),
					'IdDoctor' 	=> $request->input('DoctorName'),
					'IdCompany'	=> $request->input('CompanyName'),
					'IdPriceCode'=> $request->input('PriceCode'),
					'IdItem'	=> $item['Id'],	
					'Notes'		=> $item['Notes'],
					'InputBy'	=> $user->Username,
					'InputId'	=> $user->Id
				]);
			}
		}
		return \App\Models\TransactionTemp::where('TransactionTemp.Date',date('Y-m-d'))
				->where('TransactionTemp.InputId',$user->Id)
				->leftJoin('Items','TransactionTemp.IdItem','=','Items.Id')
				->leftJoin('Doctors','TransactionTemp.IdDoctor','=','Doctors.Id')
				->leftJoin('Companies','TransactionTemp.IdCompany','=','Companies.Id')
				->leftJoin('PriceCode','TransactionTemp.IdPriceCode','=','PriceCode.Id')
				->get(array('TransactionTemp.Id','TransactionTemp.IdDoctor','TransactionTemp.IdCompany','TransactionTemp.IdPriceCode','PriceCode.Description as Package','Items.Description','Doctors.FullName as Doctor','Companies.Name as Company','TransactionTemp.Notes','TransactionTemp.Status','TransactionTemp.InputBy'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
     	return \App\Models\PriceCode::where('Status',1)
					->where('IdBU',session('userBU'))
					->where('IdCompany',$id)
					->get(array('Id','Code','Description'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
		$user = Auth::user();
        $data = \App\Models\TransactionTemp::where('Date',date('Y-m-d'))
				->where('InputId',$user->Id)
				->where('Id',$id)
				->get(array('Id','IdItem','IdDoctor','IdPriceCode','IdCompany'));
				
		$companys = \App\Models\Company::where('Companies.Status', 1)
					->where('Companies.Id',$data[0]->IdCompany)
				   	->leftJoin('CompanyCategories', 'Companies.IdCategory', '=', 'CompanyCategories.Id')
				   	->get(array('Companies.Id','Companies.Code','Companies.Name','CompanyCategories.Description as Category'));
		$doctors = \App\Models\DoctorClinic::where('DoctorClinics.Status',1)
					->where('Doctors.Id',$data[0]->IdDoctor)
					->where('DoctorClinics.IdClinic',session('userClinic'))
					->where('Doctors.IdBU',session('userBU'))
					->leftJoin('Doctors','DoctorClinics.IdDoctor','=','Doctors.Id')
					->leftJoin('DoctorCategories','DoctorClinics.IdCategory','=','DoctorCategories.Id')
					->get(array('Doctors.Id','Doctors.FullName','DoctorCategories.Description as Category'));
		$packages = \App\Models\PriceCode::where('Status',1)
					->where('IdBU',session('userBU'))
					->where('IdCompany',$data[0]->IdCompany)
					->where('Id',$data[0]->IdPriceCode)
					->get(array('Id','Code','Description'));
		
				
		return view('cms/pages.queueTempCreateEdit', ['itemId' => $data[0]->IdItem ,'doctors' => $doctors, 'companys' => $companys, 'packages' => $packages ] );					
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
        //
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
