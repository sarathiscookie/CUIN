<?php

namespace App\Http\Controllers;

use App\Http\Requests;

use Illuminate\Http\Request;

use App\User;

use Auth, App;

use App\Subscription;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_id       = User::select('company_id')->where('id', Auth::user()->id)->first();
        $request->session()->put('companyId', $company_id->company_id);
        return view('home');
    }
}
