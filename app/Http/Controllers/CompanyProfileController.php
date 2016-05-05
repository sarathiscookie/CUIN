<?php

namespace App\Http\Controllers;

use App\Status;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CompanyProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App; 
class CompanyProfileController extends Controller
{
    /**
     * CompanyProfileController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show company user profile
     * @return mixed
     */
    public function showProfile()
    {
        $userLanguage = Auth::user()->language;
        $id = Auth::user()->id;
        $user = User::select('name', 'lastname', 'email', 'phone', 'street', 'postal', 'city', 'state', 'country')->find($id);
        $company = User::find($id)->userCompany->title;
        return view('companyProfile', ['user' => $user, 'company'=> $company, 'userLanguage' => $userLanguage]);
    }

    /**
     * update company user profile
     * @param CompanyProfileRequest $request
     * @return mixed
     */
    public function updateProfile(CompanyProfileRequest $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        $user->name     = $request->name;
        $user->lastname = $request->lastname;
        $user->email    = $request->email;
        $user->language = $request->language;
        $user->phone    = $request->phone;
        $user->street   = $request->street;
        $user->postal   = $request->postal;
        $user->city     = $request->city;
        $user->state    = $request->state;
        $user->country  = $request->country;
        $user->save();
        return redirect(url('/company/profile'))->with('status','Updated Successfully');
    }

    /**
     * show chnage password form
     * @return mixed
     */
    public function showSettings()
    {
        $listCompanyStatus             = Status::select('id', 'title', 'description', 'light', 'sort_id')
            ->where('company_id', session()->get('companyId'))
            ->orderBy('sort_id', 'asc')
            ->get();

        return view('profileSettings', compact('listCompanyStatus'));
    }

    /**
     * Update password
     * @param ChangePasswordRequest $request
     * @return mixed
     */

    public function updatePassword(ChangePasswordRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
        ]);
        $id = Auth::user()->id;
        $user = User::find($id);

        if(Hash::check($request->old_password, $user->password)){
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect(url('/company/profile/settings'))->with('status','Password Changed Successfully');
        }
        else{
            $validator->errors()->add('old_password', 'Old password is incorrect!');
            return redirect('/company/profile/settings')->withErrors($validator);
        }
    }
}
