<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
class EshopSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Home';           // Required Variable
        $pageTitle      = 'Dashboard';     // Page Slug Title
        return view('eshop::modernDashboard', compact('selectedMenu','pageTitle'));
    }
    
} 