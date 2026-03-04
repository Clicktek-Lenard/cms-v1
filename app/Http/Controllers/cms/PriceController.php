<?php

namespace App\Http\Controllers\cms;

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
        $itemPrice =  \App\Price::where('IdPriceCode',$request->input('PriceCode'))
					->where('Items.Status',1)
					->leftJoin('Items','Prices.IdItem','=','Items.Id')
					->leftJoin('ItemCategories','Items.IdCategory','=','ItemCategories.Id')
					->get(array('Prices.IdItem','Prices.Amount','Items.Code','Items.Description','ItemCategories.Description as Category' ))
					->toArray();
		$itemSelected = \App\TransactionTemp::where('Date',date('Y-m-d'))
					->where('InputId',$user->Id)
					->where('IdDoctor',$request->input('DoctorName'))
					->where('IdCompany',$request->input('CompanyName'))
					->where('IdPriceCode',$request->input('PriceCode'))
					->get(array('IdItem'))
					->toArray();
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
