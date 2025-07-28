<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\Sales\RegistrationModel;

class RegistrationController extends Controller
{
    /**
    *
    * Create by Md. Masud Rana
    * Date : 04/12/2017
    *
    **/

    public function ssg_register()
    {  
        //echo Hash:: $2y$10$pVJ5ageXK3d7ya.ELwFN.uzpYwIi3ojhjTxHx2iMqP2yL4Dnh8bPu

        return view('registrationPage');
    }

    public function ssg_register_done(Request $request)
    {
        // validator for business email : unique 

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:45',
        ]);

        if ($validator->fails()) 
        {
            return redirect('/register')->withErrors($validator)->withInput($request->all);
        }

        $business                       = new RegistrationModel;
        $business->businessOwnerName    = $request->get('fullName');
        $business->businessName         = $request->get('businessName');     
        $business->email                = $request->get('email');     
        $business->password             = Hash::make($request->get('businessPassword'));
        $business->businessPhone        = $request->get('businessNumber');     
        $business->businessAgreeStatus  = $request->get('businessTerms');     
        $business->businessStatus       = '0';
        $business->save();

        return Redirect::to('/')->with('success', 'You have registered successfully. You can login now');
    }    
}
