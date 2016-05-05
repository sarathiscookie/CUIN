<?php

namespace App\Http\Controllers\Customerauth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;


class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/customer/login';
    protected $linkRequestView = 'customer.auth.passwords.email';
    protected $resetView = 'customer.auth.passwords.reset';
    protected $guard = 'customer';
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        config(['auth.defaults.passwords' => 'customers']);
        $this->middleware('guest');
    }
}
