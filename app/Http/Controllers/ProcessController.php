<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\ProcessRequest;

use App\Process;
use App\Status;

use Auth;
use Illuminate\Support\Facades\Mail;

class ProcessController extends Controller
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
        //return view('createProcess', ['statusIds' => $statusId]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id,Request $request)
    {
        $statusId   = Status::select('id', 'title')
            ->where('company_id', $request->session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();
        return view('createProcess', ['id' => $id, 'statusIds' => $statusId, 'mode'=>'new']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcessRequest $request)
    {
        $process                 = new Process;
        $process->company_id     = $request->session()->get('companyId');
        $process->customer_id    = $request->customersId;
        $process->status_id      = $request->statusId;
        $process->title          = $request->title;
        $process->description    = $request->description;
        $process->reference_id   = $request->referenceId;
        $process->save();

        if($process->id>0) {
            //$this->notifyProcess($process->id, $request);
        }

        if(isset($request->relist) && $request->relist==1)
            return redirect(url('/customers/list/process/'.$request->customersId))->with('status', true);
        else
            return redirect(url('customers/list/process/'.$process->id.'/entries/create'))->with('createProcessMessage', true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $listCustomersProcess    = Process::select('processes.id', 'processes.title', 'processes.description', 'processes.reference_id', 'statuses.light as statustitle')
            ->join('statuses', 'processes.status_id', '=', 'statuses.id')
            ->where('processes.customer_id', $id)
            ->where('processes.status', '<>', 'deleted')
            ->orderBy('processes.id', 'desc')
            ->get();
        return view('listCustomersProcess', ['customer_id'=>$id], compact('listCustomersProcess'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProcessesData()
    {
        $listProcesses    = Process::select('processes.id', 'processes.title', 'processes.description', 'processes.reference_id', 'statuses.light as statusLight', 'customers.name as customerName', 'customers.reference_id as customerReferenceId')
            ->join('statuses', 'processes.status_id', '=', 'statuses.id')
            ->join('customers', 'processes.customer_id', '=', 'customers.id')
            ->where('processes.company_id', session()->get('companyId'))
            ->where('customers.status', '<>', 'deleted')
            ->where('processes.status', '<>', 'deleted')
            ->orderBy('processes.id', 'desc')
            ->get();
        return view('listProcess', compact('listProcesses'));
    }

    /**
     * Notification email to Customer -on adding a new process
     * @param $id
     * @param $request
     */
    protected function notifyProcess($id, $request)
    {
        $newprocess = Process::select('title','customers.name', 'customers.email')
            ->join('customers','customers.id','=','processes.customer_id')
            ->where('processes.id',$id)
            ->first();
        try {
            Mail::send('emails.notifyProcess', ['process' => $newprocess], function ($message) use($newprocess) {
                $message->to($newprocess->email, $newprocess->name)->subject('Notification: A new process created');
            });
        } catch (Exception $e) {
            $request->session()->flash('error', 'Notification mail not send!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $process = Process::find($id);
        $statusId   = Status::select('id', 'title')
            ->where('company_id', $request->session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();
        $redirect ='';
        if(isset($request->r) && $request->r=='all')
            $redirect = '?r=all';

        return view('updateProcess', ['process'=>$process, 'statusIds' => $statusId, 'redirect'=>$redirect]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProcessRequest $request, $id)
    {
        $process = Process::find($id);
        $process->status_id      = $request->statusId;
        $process->title          = $request->title;
        $process->description    = $request->description;
        $process->reference_id   = $request->referenceId;
        $process->save();

        if(isset($request->r) && $request->r=='all')
            return redirect(url('/processes'))->with('updatemessage', true);

        return redirect(url('/customers/list/process/'.$process->customer_id))->with('updatemessage', true);
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
        Process::where('id',$id)->update(['status' => 'deleted']);
        return response()->json(['mes'=>'done']);
    }
}
