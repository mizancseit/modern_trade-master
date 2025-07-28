<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect; 
use App\Modules\eshop\Models\Supervisor; 
use DB;
use Auth;
use Session;
use App\Modules\eshop\Models\Order;

class EshopRequisitionController extends Controller
{ 
    public function index()
    {
 
        $selectedMenu    = 'Requisition';        // Required Variable for menu
        $selectedSubMenu = 'Requisition List';   // Required Variable for menu
        $pageTitle       = 'Requisition List';   // Page Slug Title

        $user_type = DB::table('tbl_user_type')->get();
        $management = DB::table('users')->where('user_type_id','5')->get();
        $managers = DB::table('users')->where('user_type_id','6')->get();
        $executive = DB::table('users')->where('user_type_id','3')->get();
        $officer = DB::table('users')->where('user_type_id','7')->get();

        $orders = Order::where('stock_out',1)->get();  
        return view('eshop::requisition/requisition-status',compact('user_type','management','managers','executive','officer','selectedMenu','selectedSubMenu','pageTitle','orders'));
    } 
    public function details($id){
        $selectedMenu    = 'Requisition';        // Required Variable for menu
        $selectedSubMenu = 'Requisition List';   // Required Variable for menu
        $pageTitle       = 'Requisition List';   // Page Slug Title
        $user_type = DB::table('tbl_user_type')->get();
        $management = DB::table('users')->where('user_type_id','5')->get();
        $managers = DB::table('users')->where('user_type_id','6')->get();
        $executive = DB::table('users')->where('user_type_id','3')->get();
        $officer = DB::table('users')->where('user_type_id','7')->get();  
        $orders = Order::find($id);  
        //$orders = Order::find($id)->orderdetails;  
        //print_r($supervisor);
        return view('eshop::requisition/requisition-status-view',compact('user_type','management','managers','executive','officer','supervisor','orders','selectedMenu','selectedSubMenu','pageTitle')); 
    } 

    public static function category($id){ 
        return DB::table('eshop_product_category')->where('id',$id)->first();   
    }  
}
