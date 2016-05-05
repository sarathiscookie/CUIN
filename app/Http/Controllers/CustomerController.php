<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CustomerRequest;

use App\Customer;

use App\Status;

use Auth, App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
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
        return view('createCustomer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $secret   = $this->randomString(7);
        $password = Hash::make($secret);
        $customer                  = new Customer;
        $customer->company_id      = $request->session()->get('companyId');
        $customer->salutation      = $request->salutation;
        $customer->reference_id    = $request->referenceId;
        $customer->name            = $request->name;
        $customer->email           = $request->email;
        $customer->password        = $password;
        $customer->notice_internal = $request->noticeinternal;
        $customer->notice_external = $request->noticeexternal;
        $customer->hash            = md5($customer->email.$customer->created_at);
        $customer->save();

        if($customer->id>0){
            $this->sendActivationMail($customer->id, $secret, $request);
        }

        $statusId                  = Status::select('id', 'title')
            ->where('company_id', $request->session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();
        $request->session()->flash('successmessage', 'Task was successful!');
        return view('createProcess', ['id' => $customer->id, 'statusIds' => $statusId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $listCustomers             = Customer::select('id', 'name', 'email', 'reference_id')
            ->where('company_id', session()->get('companyId'))
            ->where('status', '<>', 'deleted')
            ->orderBy('id', 'desc')
            ->get();
        return view('listCustomers', compact('listCustomers'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('updateCustomer', ['customer'=>$customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::find($id);
        $email = $customer->email;
        $customer->salutation      = $request->salutation;
        $customer->reference_id    = $request->referenceId;
        $customer->name            = $request->name;
        $customer->email           = $request->email;
        $customer->notice_internal = $request->noticeinternal;
        $customer->notice_external = $request->noticeexternal;
        $customer->hash            = md5($customer->email.$customer->created_at);
        $customer->save();
        if($email!=trim($request->email)){
            $secret   = $this->randomString(7);
            $password = Hash::make($secret);
            Customer::where('id',$id)->update(['password'=>$password, 'active'=>'no']);
            $this->sendActivationMail($id, $secret, $request);
        }
        return redirect(url('/customers'))->with('updatemessage', true);
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
        Customer::where('id',$id)->update(['status' => 'deleted', 'active' => 'no']);
        return response()->json(['mes'=>'done']);
    }

    /**
     * Unique string generated for password hash
     * @param $length
     * @return string
     */
    protected  function randomString($length)
    {
        $salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $rand = '';
        $i = 0;
        while ($i < $length) { // Loop until you have met the length
            $num = rand() % strlen($salt);
            $tmp = substr($salt, $num, 1);
            $rand = $rand . $tmp;
            $i++;
        }
        return $rand; // Return the random string
    }

    /**
     * Customer activation - Email push
     * @param $id
     * @param $secret
     * @param $request
     */
    protected function sendActivationMail($id, $secret, $request)
    {
        $new_customer = Customer::select('hash','name', 'email')->find($id);
        try {
            Mail::send('emails.activation', ['password'=>$secret, 'customer'=>$new_customer], function ($message)  use ($new_customer) {
                $message->to($new_customer->email, $new_customer->name)->subject('Customer Account Activation!');
            });
        } catch (Exception $e) {
            $request->session()->flash('error', 'Activation mail not send!');
        }
    }
}
