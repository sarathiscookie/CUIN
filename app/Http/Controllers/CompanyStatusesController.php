<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CompanyStatusesRequest;

use App\Status;

use Auth, DB;

class CompanyStatusesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('createCompanyStatuses');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyStatusesRequest $request)
    {
        $sortIdMax = Status::select('sort_id')
            ->where('company_id', session()->get('companyId'))
            ->orderBy('sort_id', 'desc')
            ->first();
        $status    = new Status;
        DB::transaction(function() use ($sortIdMax, $status, $request) {
            $status->company_id     = $request->session()->get('companyId');
            $status->title          = $request->title;
            $status->description    = $request->description;
            $status->light          = $request->light;
            $status->sort_id        = $sortIdMax->sort_id+1;
            $status->save();
        });

        return redirect(url('/company/profile/settings#companyStatus'))->with('successmessage', true);
    }

    /**
     * Display the specified resource. - For Ajax only inside the Vue methods     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showListData()
    {
        $listCompanyStatus             = Status::select('id', 'title', 'description', 'light', 'sort_id')
            ->where('company_id', session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();
        return view('profileSettings', compact('listCompanyStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $companyStatus             = Status::select('title', 'description', 'light', 'sort_id')
            ->where('id', $id)
            ->first();
        return view('updateCompanyStatuses', ['companyStatus' => $companyStatus, 'id' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyStatusesRequest $request, $id)
    {
        $StatusUpdate              = Status::find($id);
        $StatusUpdate->title       = $request->title;
        $StatusUpdate->description = $request->description;
        $StatusUpdate->light       = $request->light;
        $StatusUpdate->save();
        return redirect(url('/company/profile/settings#companyStatus'))->with('updatemessage', true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSort($ID, $sortID)
    {
        $update_priority           = Status::where('id', $ID)
            ->update(['sort_id' => $sortID]);

        return response()->json(['update_priority' => $update_priority]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!$request->ajax()) {
            return response()->json(['mes'=>'bad request']);
        }
        $status = Status::find($id);
        $used   = $status->process->count();

        if($used==0) {
            Status::destroy($id);
            return response()->json(['mes'=>'done']);
        }
        elseif($used>0) {
            return response()->json(['mes'=>'Couldn\'t be deleted! Status is being used by processes']);
        }
        else {
            return response()->json(['mes'=>'invalid request']);
        }
    }
}
