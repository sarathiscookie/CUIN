<?php

namespace App\Http\Controllers\Customerauth;

use App\Customer;

use Illuminate\Support\Facades\Session;
use Validator, DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */

    protected $redirectTo = '/profile';
    protected $guard = 'customer';

    /**
     * Customer login form
     * @return mixed
     */
    public function showLoginForm()
    {
        if (Auth::guard($this->guard)->check())
        {
            $hash = Auth::guard($this->guard)->user()->hash;
            return redirect('/'.$hash);
        }
        // redirect to previous url before login
        Session::forget('from');
        if(strstr(url()->previous(),'?pid')) {
            if (!session()->has('from')) {
                session()->put('from', url()->previous());
            }
        }

        return view('customer.auth.login');
    }

    /**
     * Customer Login
     * Handle an authentication attempt.
     * @return Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password, 'active' => 'yes'];

        if (Auth::guard($this->guard)->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect()->back()
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * intermediate route to handle hash and redirect to timeline
     * @return mixed
     */
    public function showProfile(Request $request)
    {
        if(session()->has('from')) {
            return redirect(session()->pull('from',$this->redirectTo));
        }
        $hash = Auth::guard($this->guard)->user()->hash;
        return redirect('/'.$hash);
    }


    /**
     * Customer logout
     * @return mixed
     */
    public function logout(){
        Auth::guard($this->guard)->logout();
        return redirect('/customer/login');
    }

}
