<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Input;

use App\Http\Requests\SubscriptionRequest;

use App\Subscription;

use Auth;

class SubscriptionController extends Controller
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
    public function cardUpdatePage()
    {
        return view('subscriptionCardUpdate');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cardUpdate(SubscriptionRequest $request)
    {
        $user  = Auth::user();
        $token = $request->stripeToken;
        $user->updateCard($token);
        return redirect(url('/home'))->with('cardUpdatedNotice', 'Card details updated');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function planUpdatePage()
    {
        return view('subscriptionPlanUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function planUpdate(SubscriptionRequest $request)
    {
        $user            = Auth::user();

        $user->subscription('main')->swap($request->plan);

        return redirect(url('/home'))->with('updatedPlanNotice', 'Subscription has updated. Thanks');
    }

    /**
     * Cancel subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancelPlan()
    {
        $user            = Auth::user();

        $user->subscription('main')->cancel();

        return redirect(url('/home'))->with('cancelPlanNotice', 'Subscription has cancelled');
    }

    /**
     * Cancel subscription plan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resumePlan()
    {
        $user            = Auth::user();

        $user->subscription('main')->resume();

        return redirect(url('/home'));
    }


    /**
     * Download invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($invoice)
    {
        $user            = Auth::user();
        return  $user->downloadInvoice($invoice, [
            'vendor'  => 'Service Hoster',
            'product' => 'Subscription',
        ]);
    }


}
