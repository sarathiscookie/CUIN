<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Company;
use App\Status;
use Illuminate\Support\Facades\Validator; 
use  DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'company' => 'required|max:100',
            'name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:users|unique:customers',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $company          = new Company;
        $user             = new User;
        $company->title   = $data['company'];
        $creditCardToken  = $data['stripeToken'];
        DB::transaction(function() use ($company, $user, $data, $creditCardToken) {
            $company->save();
            $user->company_id = $company->id;
            $user->name       = $data['name'];
            $user->lastname   = $data['lastname'];
            $user->email      = $data['email'];
            $user->password   = bcrypt($data['password']);
            $user->language   = $data['language'];
            $user->phone      = $data['phone'];
            $user->street     = $data['street'];
            $user->postal     = $data['postal'];
            $user->city       = $data['city'];
            $user->state      = $data['state'];
            $user->country    = $data['country'];
            $user->save();

            if($data['plan'] == 'medium'){
                $user->newSubscription('main', $data['plan'])
                    ->trialDays(1)
                    ->create($creditCardToken, [
                        'email' => $data['email']
                    ]);
            }
            else{
                $user->newSubscription('main', $data['plan'])->create($creditCardToken, [
                    'email' => $data['email']
                ]);
            }
        });

        Status::insert([
            ['company_id' => $user->company_id, 'title' => 'Created', 'description' => NULL, 'light' => '1', 'sort_id' => '1'],
            ['company_id' => $user->company_id, 'title' => 'In the work', 'description' => NULL, 'light' => '2', 'sort_id' => '2'],
            ['company_id' => $user->company_id, 'title' => 'Done', 'description' => NULL, 'light' => '3', 'sort_id' => '3']
        ]);
        
        return $user;
    }
}
