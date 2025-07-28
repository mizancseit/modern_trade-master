<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ModernExecutiveReportController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

   public function mts_order_report()
    {
        $selectedMenu   = 'Sales Report';                      // Required Variable for menu
        $selectedSubMenu= 'Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Report';            // Page Slug Title  
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false;

        if(Auth::user()->user_type_id ==7){ //
            $officer = true;
        }else if(Auth::user()->user_type_id = 3){
            $executive = true;
        }else if(Auth::user()->user_type_id = 6){
            $manager = true;
        }else if(Auth::user()->user_type_id = 2){
            $billing = true;
        } 
        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->when($officer, function ($query, $officer) {
                return $query->where('mts_order.order_status', 'Confirmed')
                    ->where('mts_order.is_download','NO');
            }) 
            ->when($executive, function ($query, $executive) { 
            }) 
            ->when($manager, function ($query, $manager) { 
            }) 
            ->when($billing, function ($query, $billing) { 
            }) 
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
  
        return view('ModernSales::sales/executiveReport/orderReport', compact('selectedMenu','selectedSubMenu','pageTitle', 'resultOrderList'));
    }

    public function mts_order_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false;

        if(Auth::user()->user_type_id ==7){ //
            $officer = true;
        }else if(Auth::user()->user_type_id = 3){
            $executive = true;
        }else if(Auth::user()->user_type_id = 6){
            $manager = true;
        }else if(Auth::user()->user_type_id = 2){
            $billing = true;
        } 
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->when($officer, function ($query, $officer) {
                return $query->where('mts_order.order_status', 'Confirmed')
                    ->where('mts_order.is_download','NO');
            }) 
            ->when($executive, function ($query, $executive) { 
            }) 
            ->when($manager, function ($query, $manager) { 
            }) 
            ->when($billing, function ($query, $billing) { 
            }) 
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 
        
        return view('ModernSales::sales/executiveReport/orderReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function mts_order_details($orderMainId)
    {
        $selectedMenu   = 'Sales Report';                       // Required Variable for menu
        $selectedSubMenu= 'Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'mts_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_order.route_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id', Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_order')
        ->select('mts_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id',Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Confirmed')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_order_details')
        ->select('mts_order_details.delivery_challan','mts_order.delivery_date','mts_order_details.order_id','mts_order.order_id','mts_order.order_status')

        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Confirmed')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_order')->select('mts_order.order_no','mts_order.po_no','mts_order.update_date','mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','mts_order.party_id','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')

        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id',Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('mts_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id)  
        ->first();
 
        $customerInfo = DB::table('mts_order')
                        ->select('mts_order.order_id','mts_order.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_order.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_order.order_id',$orderMainId)
                        ->first();
        

        return view('ModernSales::sales/executiveReport/orderDetailsReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultAllChalan', 'orderCommission','resultDistributorInfo','resultFoInfo','customerInfo'));

    }

    public function mts_delivery_report()
    {
        $selectedMenu   = 'Sales Report';                      // Required Variable for menu
        $selectedSubMenu= 'Delivery Report';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title
        

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->where('mts_order.is_download', 'YES')
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
  
        return view('ModernSales::sales/executiveReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultOrderList'));
    }

    public function mts_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
 
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.is_download', 'YES')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        
        return view('ModernSales::sales/executiveReport/deliveryReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function mts_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Sales Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'mts_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_order.route_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id', Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_order')
        ->select('mts_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id',Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Delivered')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_order_details')
        ->select('mts_order_details.delivery_challan','mts_order.delivery_date','mts_order_details.order_id','mts_order.order_id','mts_order.order_status')

        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Delivered')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_order')->select('mts_order.order_no','mts_order.po_no','mts_order.update_date','mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','mts_order.party_id','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address') 
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id',Auth::user()->id)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('mts_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id)                        
        // ->where('entry_by',Auth::user()->id)                        
        // ->where('party_id',$foMainId)
        ->first();

         $customerInfo = DB::table('mts_order')
                        ->select('mts_order.order_id','mts_order.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_order.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_order.order_id',$orderMainId)
                        ->first();

        

        return view('ModernSales::sales/executiveReport/deliveryDetailsReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultAllChalan', 'orderCommission','resultDistributorInfo','resultFoInfo','customerInfo'));

    }
}

