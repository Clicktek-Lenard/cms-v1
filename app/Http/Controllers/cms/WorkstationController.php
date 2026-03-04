<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\cms\Counter;
use App\Models\cms\CardEnrollment;
use Carbon\Carbon;

class WorkstationController extends Controller
{
    public function index()
    {
        $counters = Counter::all();
        $users = CardEnrollment::getUsers();
    
        return view('cms.workstation', compact('counters', 'users'));
    }

    public function create()
    {
        $idbu = DB::connection('Eros')->table('BusinessUnits')->pluck('Code');

	    return view('cms.workstationCreate', compact('idbu'));
    }

    public function store(Request $request)
    {
        $counter = new Counter();

        $counter->StationNumber = $request->input('station');
        $counter->IPv4          = $request->input('ip');
        $counter->Department    = $request->input('department');
        $counter->Location      = $request->input('location') ?: '';
        $counter->IdBU          = $request->input('branch');
        $counter->InputBy       = Auth::user()->username;
        $counter->InputDate     = Carbon::now();

        $counter->save();

        return redirect()->route('workstation.index')->with('success', 'Counter created successfully.');
    }

    public function edit($id)
    {
        $counter = Counter::findOrFail($id);
        $idbu = DB::connection('Eros')->table('BusinessUnits')->pluck('Code');
    
        return view('cms.workstationEdit', ['counter' => $counter, 'idbu' => $idbu]);
    }

    public function update(Request $request, $id)
    {
        $counter = Counter::findOrFail($id);

        $counter->StationNumber = $request->input('station');
        $counter->IPv4          = $request->input('ip');
        $counter->Department    = $request->input('department');
        $counter->Location      = $request->input('location') ?: '';
        $counter->IdBU          = $request->input('branch');
        $counter->UpdateBy      = Auth::user()->username;
        $counter->UpdateDate    = Carbon::now();

        $counter->save();

        return redirect()->route('workstation.edit', ['workstation' => $id])->with('success', 'Counter information updated successfully.')->with('show_dialogue_box', true);;
    }
    
}
