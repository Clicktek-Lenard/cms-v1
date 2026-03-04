<?php

namespace App\Http\Controllers\cms\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		$user = Auth::user();
        /*$itemPrice =  \App\Models\Price::where('IdPriceCode',$request->input('PriceCode'))
					->where('Items.Status',1)
					->leftJoin('Item','Prices.IdItem','=','Items.Id')
					->leftJoin('ItemCategory','Items.IdCategory','=','ItemCategories.Id')
					->get(array('Prices.IdItem','Prices.Amount','Items.Code','Items.Description','ItemCategories.Description as Category' ))
					->toArray();*/
					
		$itemPrice =  \App\Models\Price::where(function($q) use ($request) {
			$q->where('IdPriceCode',$request->input('PriceCode'));
			$q->where('Items.Status',1);
			if( $request->input('ItemId') != 0)
			{
				$q->where('Items.Id', $request->input('ItemId'));	
			}
		})
		->leftJoin('Items','Prices.IdItem','=','Items.Id')
		->leftJoin('ItemCategories','Items.IdCategory','=','ItemCategories.Id')
		->get(array('Prices.IdItem','Prices.Amount','Items.Code','Items.Description','ItemCategories.Description as Category' ))
		->toArray();
		$itemSelected = array();			
		if( $request->input('Pages') == 'temp' )
		{
			$itemSelected = \App\Models\TransactionTemp::where(function($q) use ($request,$user) {
					if( $request->input('ItemId') != 0)
					{
						$q->where('IdItem', $request->input('ItemId'));	
					}
					$q->where('Date',date('Y-m-d'));
					$q->where('InputId',$user->Id);
					$q->where('IdDoctor',$request->input('DoctorName'));
					$q->where('IdCompany',$request->input('CompanyName'));
					$q->where('IdPriceCode',$request->input('PriceCode'));
				})
				->get(array('IdItem','Notes'))
				->toArray();
		}
		else
		{
			$itemSelected = \App\Models\Transactions::where(function($q) use ($request,$user) {
					if( $request->input('ItemId') != 0)
					{
						$q->where('IdItem', $request->input('ItemId'));	
					}
					if( $request->input('TransId') != 0 )
					{
						$q->where('Id', $request->input('TransId'));	
					}
					$q->where('IdDoctor',$request->input('DoctorName'));
					$q->where('IdCompany',$request->input('CompanyName'));
					$q->where('IdPriceCode',$request->input('PriceCode'));
					$q->where('IdQueue',$request->input('QueueId'));
				})
				->get(array('Id','IdItem','Notes'))
				->toArray();
		}
		return \Response::json(
			array(
				'itemPrice'=>$itemPrice,
				'itemSelected'=>$itemSelected
			));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
