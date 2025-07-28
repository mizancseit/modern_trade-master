<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class EshopExecutiveReportController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

   public function eshop_order_report()
    {
        $selectedMenu   = 'Sales Report';                      // Required Variable for menu
        $selectedSubMenu= 'Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
         $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
  
        return view('eshop::sales/executiveReport/orderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function eshop_order_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/executiveReport/orderReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function eshop_order_details($orderMainId)
    {
        $selectedMenu   = 'Sales Report';                       // Required Variable for menu
        $selectedSubMenu= 'Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'eshop_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'eshop_order.route_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('eshop_order')
        ->select('eshop_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id',Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Confirmed')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        //dd($resultCartPro);

        $resultAllChalan  = DB::table('eshop_order_details')
        ->select('eshop_order_details.delivery_challan','eshop_order.delivery_date','eshop_order_details.order_id','eshop_order.order_id','eshop_order.order_status')

        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Confirmed')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.order_no','eshop_order.po_no','eshop_order.update_date','eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','eshop_order.party_id','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')

        ->leftjoin('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id',Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('eshop_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id)  
        ->first();
 
        $customerInfo = DB::table('eshop_order')
                        ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code')
                        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_order.order_id',$orderMainId)
                        ->first();
        

        return view('eshop::sales/executiveReport/orderDetailsReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultAllChalan', 'orderCommission','resultDistributorInfo','resultFoInfo','customerInfo'));

    }

    public function eshop_delivery_report()
    {
        $selectedMenu   = 'Sales Report';                      // Required Variable for menu
        $selectedSubMenu= 'Delivery Report';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.ack_status', 'Approved')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->where('eshop_order.ack_status', 'Approved')
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
  
        return view('eshop::sales/executiveReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function eshop_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/executiveReport/deliveryReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function eshop_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Sales Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'eshop_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'eshop_order.route_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('eshop_order')
        ->select('eshop_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id',Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Delivered')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('eshop_order_details')
        ->select('eshop_order_details.delivery_challan','eshop_order.delivery_date','eshop_order_details.order_id','eshop_order.order_id','eshop_order.order_status')

        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Delivered')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.order_no','eshop_order.po_no','eshop_order.update_date','eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','eshop_order.party_id','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address') 
        ->leftjoin('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id',Auth::user()->id)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('eshop_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id)                        
        // ->where('entry_by',Auth::user()->id)                        
        // ->where('party_id',$foMainId)
        ->first();

         $customerInfo = DB::table('eshop_order')
                        ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code')
                        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_order.order_id',$orderMainId)
                        ->first();

        

        return view('eshop::sales/executiveReport/deliveryDetailsReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultAllChalan', 'orderCommission','resultDistributorInfo','resultFoInfo','customerInfo'));

    }
}
