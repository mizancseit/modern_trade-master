<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use Auth;


class RedirectController extends Controller
{
	
	public function index()
	{
        // dd(Auth::user());
        if(!Auth::user()){
            return view('masterLogin');
        }
        // dd(Auth::user());
        if (Auth::user()->module_type==1) // if login
        {
        	return Redirect::to('/dashboard');
        }
        elseif(Auth::user()->module_type==2){
        	return Redirect::to('/modernSales');
        }
        elseif(Auth::user()->module_type==3){
            return Redirect::to('/eshop');
        }
        else
        {
        	return view('masterLogin');
        }

        return view('masterLogin');
    }
}
