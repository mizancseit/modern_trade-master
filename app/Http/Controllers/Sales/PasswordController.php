<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class PasswordController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 02/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_change_password()
    {
        $selectedMenu   = 'Password';                      // Required Variable for menu        
        $pageTitle      = 'Change Password';              // Page Slug Title
        
        return view('sales.password.changePassword', compact('selectedMenu','pageTitle'));
    }

    public function ssg_check_password(Request $request)
    {

        $resultUser  = DB::table('users')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('id', Auth::user()->id)
                        ->first();

        if( ! Hash::check($request->get('oldPassword') , $resultUser->password) )
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }

    public function ssg_change_password_submit(Request $request)
    {

        $newPassword = Hash::make($request->get('newPassword'));

        $result = DB::table('users')->where('id', Auth::user()->id)
        ->update(
            [
                'password' => $newPassword
            ]
        );

        return $result;
    }
}
