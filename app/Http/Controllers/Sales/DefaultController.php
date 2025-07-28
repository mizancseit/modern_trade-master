<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use Auth;

class DefaultController extends Controller
{
    
    public function default1()
    {
        if (Auth::user()) // if login
        {
            return Redirect::to('/dashboard');
        }
        else
        {
            return view('masterLogin');
        }

        return view('masterLogin');
    }

    

    public function new_order()
    {
    	$selectedMenu   = 'Visit';     // Required Variable

    	return view('sales/defaultCreate', compact('selectedMenu'));
    }    

    public function ssg_invoice()
    {
        $selectedMenu   = 'Visit';     // Required Variable

        return view('sales/defaultInvoice', compact('selectedMenu'));
    }

    public function ssg_forgot_password()
    {
        return view('forgotPasswordPage');
    }

    public function logout()
    {
    	return Redirect::to('/')->with('success', 'Successfully Logout.');;
    }
}
