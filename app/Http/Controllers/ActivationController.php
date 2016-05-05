<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;

class ActivationController extends Controller
{
    /**
     * On adding a new customer - send account Activation email
     * @param $hash
     * @return mixed
     */
    public function activateAccount($hash)
    {
        $customer = Customer::where('hash',$hash)->first();
        if(count($customer)>0)
        {
            if($customer->active=='yes'){
                return view('customer.auth.login',['email'=>$customer->email]);
            }
            else {
                Customer::where('id', $customer->id)->update(['active' => 'yes']);
                return view('customer.auth.login',['email'=>$customer->email, 'status' => 'Your account has been successfully Activated, proceed to Login']);
            }
        }
        return view('customer.auth.login');
    }
}
