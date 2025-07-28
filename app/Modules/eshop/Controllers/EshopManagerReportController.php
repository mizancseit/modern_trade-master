<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class EshopManagerReportController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
	*/
	
	public function ssg_manager_delivery()
    {
        $selectedMenu   = 'Report';                      // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.ack_status', 'Approved')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $customer = DB::table('eshop_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();
		   

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();


        return view('eshop::sales/managerReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','officerlist','executivelist'));
    }

    public function ssg_manager_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
		$executive_id   = $request->get('executive_id');
        $fo         = $request->get('fos'); 
		
		if($fromdate!='' && $todate!='' && $executive_id==''&& $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved') 
            ->where('eshop_order.manager_id', Auth::user()->id)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved') 
            ->where('eshop_order.manager_id', Auth::user()->id)
			->where('eshop_order.executive_id', $executive_id)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $fo!='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved') 
            ->where('eshop_order.manager_id', Auth::user()->id)
			->where('eshop_order.executive_id', $executive_id)
            ->where('eshop_order.fo_id', $fo)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $customer!='' && $fo!='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved') 
            ->where('eshop_order.manager_id', Auth::user()->id)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.customer_id', $customer)
			->where('eshop_order.executive_id', $executive_id)
            ->where('eshop_order.fo_id', $fo)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/managerReport/deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_manager_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'eshop_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'eshop_order.route_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('eshop_order')
        ->select('eshop_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                        //->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                        //->where('eshop_order.order_status','Delivered')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('eshop_order_details')
        ->select('eshop_order_details.delivery_challan','eshop_order.delivery_date','eshop_order_details.order_id','eshop_order.order_id','eshop_order.order_status')

        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                       // ->where('eshop_order.order_status','Delivered')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.order_no','eshop_order.po_no','eshop_order.update_date','eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','eshop_order.party_id','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->leftjoin('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)       
        ->where('eshop_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('eshop_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id) 
        ->first();

        

        return view('eshop::sales/managerReport/deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan', 'orderCommission'));

    }
	
	
	
	 // Advance Replace Delivery report for Manager part


    public function eshop_manager_replace_delivery_report()
    {
        $selectedMenu   = 'Advance Replace Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';             // Required Variable for submenu
        $pageTitle      = 'Advance Replace Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_replace')
        ->select('eshop_replace.global_company_id','eshop_replace.order_status','eshop_replace.replace_id','eshop_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_replace.order_status', 'Delivered')
        ->where('eshop_replace.ack_status', 'Approved')
        ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_replace.fo_id')
        ->orderBy('eshop_replace.replace_id','DESC')                    
        ->get();

        $customer = DB::table('eshop_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_replace')
        ->select('eshop_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_replace.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
        ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_replace.order_status', 'Delivered')
        ->where('eshop_replace.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_replace.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_replace.replace_id','DESC')                    
        ->get(); 

         $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();


        return view('eshop::sales/managerReport/replaceDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','executivelist','officerlist'));
    }

    public function eshop_manager_replace_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');
		$executive_id   = $request->get('executive_id'); 

		if($fromdate!='' && $todate!='' && $executive_id=='' && $officer=='' && $customer=='')
        { 
            $resultOrderList = DB::table('eshop_replace')
            ->select('eshop_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
            ->where('eshop_replace.order_status', 'Delivered')
            ->where('eshop_replace.ack_status', 'Approved') 
            ->where('eshop_replace.manager_id',  Auth::user()->id) 
            ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer=='' && $customer=='')
        { 
            $resultOrderList = DB::table('eshop_replace')
            ->select('eshop_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
            ->where('eshop_replace.order_status', 'Delivered')
            ->where('eshop_replace.ack_status', 'Approved') 
            ->where('eshop_replace.manager_id',  Auth::user()->id)
			->where('eshop_replace.executive_id', $executive_id)
            ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_replace')
            ->select('eshop_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
            ->where('eshop_replace.order_status', 'Delivered')
            ->where('eshop_replace.ack_status', 'Approved')
            ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)  
            ->where('eshop_replace.manager_id',  Auth::user()->id)
			->where('eshop_replace.executive_id', $executive_id)
            ->where('eshop_replace.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_replace.replace_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer!='')
        {
            $resultOrderList = DB::table('eshop_replace')
            ->select('eshop_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
            ->where('eshop_replace.order_status', 'Delivered')
            ->where('eshop_replace.ack_status', 'Approved')
            ->where('eshop_replace.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_replace.executive_id', $executive_id)
			->where('eshop_replace.customer_id', $customer)
            ->where('eshop_replace.fo_id', $officer) 
            ->where('eshop_replace.manager_id',  Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/managerReport/replaceDeliveryReportList', compact('resultOrderList'));
    }


    public function eshop_manager_replace_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Replace Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Replace Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_replace')->select('eshop_replace.global_company_id','eshop_replace.replace_id','eshop_replace.order_status','eshop_replace.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_replace.fo_id')
        ->leftjoin('users', 'eshop_replace.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'eshop_replace.route_id')
        ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_replace.replace_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('eshop_replace')
        ->select('eshop_replace.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_replace.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)
                        //->where('eshop_replace.fo_id',$foMainId)                        
        ->where('eshop_replace.replace_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('eshop_replace_details')
        ->select('eshop_replace_details.cat_id','eshop_replace_details.replace_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_replace.replace_id','eshop_replace.fo_id','eshop_replace.order_status','eshop_replace.replace_no','eshop_replace.po_no','eshop_replace.party_id')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_replace_details.cat_id')
        ->leftjoin('eshop_replace', 'eshop_replace.replace_id', '=', 'eshop_replace_details.replace_id')
                        //->where('eshop_replace.order_status','Delivered')
        ->where('eshop_replace_details.replace_id',$orderMainId)
        ->groupBy('eshop_replace_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('eshop_replace_details')
        ->select('eshop_replace_details.delivery_challan','eshop_replace.delivery_date','eshop_replace_details.replace_id','eshop_replace.replace_id','eshop_replace.order_status')

        ->leftjoin('eshop_replace', 'eshop_replace.replace_id', '=', 'eshop_replace_details.replace_id')
                       // ->where('eshop_replace.order_status','Delivered')
        ->where('eshop_replace_details.replace_id',$orderMainId)
        ->groupBy('eshop_replace_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('eshop_replace')->select('eshop_replace.replace_no','eshop_replace.po_no','eshop_replace.update_date','eshop_replace.global_company_id','eshop_replace.replace_id','eshop_replace.order_status','eshop_replace.fo_id','eshop_replace.party_id','eshop_replace.replace_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->leftjoin('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_replace.party_id')
        ->where('eshop_replace.global_company_id', Auth::user()->global_company_id)       
        ->where('eshop_replace.replace_id',$orderMainId)
        ->first();

         $customerInfo = DB::table('eshop_replace')
                        ->select('eshop_replace.replace_id','eshop_replace.order_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code')
                        ->join('eshop_customer_list', 'eshop_replace.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_replace.replace_id',$orderMainId)
                        ->first();

 

        

        return view('eshop::sales/managerReport/replaceDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }
	
	// Return Manager part 
	
	 public function eshop_manager_return_delivery_report()
    {
        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_return')
        ->select('eshop_return.global_company_id','eshop_return.order_status','eshop_return.return_id','eshop_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_return.order_status', 'Delivered')
        ->where('eshop_return.ack_status', 'Approved')
        ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_return.fo_id')
        ->orderBy('eshop_return.return_id','DESC')                    
        ->get();

        $customer = DB::table('eshop_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_return')
        ->select('eshop_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_return.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
        ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_return.order_status', 'Delivered')
        ->where('eshop_return.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_return.return_id','DESC')                    
        ->get();
 
          $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::sales/managerReport/returnDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','officerlist','executivelist'));
    }

    public function eshop_manager_return_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');
		$executive_id   = $request->get('executive_id'); 
		
		if($fromdate!='' && $todate!='' && $executive_id=='' && $officer=='' && $customer=='')
        { 
            $resultReturnList = DB::table('eshop_return')
            ->select('eshop_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
            ->where('eshop_return.order_status', 'Delivered')
            ->where('eshop_return.ack_status', 'Approved') 
            ->where('eshop_return.manager_id', Auth::user()->id)
            ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_return.return_id','DESC')                    
            ->get();
        }

        elseif($fromdate!='' && $todate!='' && $executive_id!='' &&  $officer=='' && $customer=='')
        { 
            $resultReturnList = DB::table('eshop_return')
            ->select('eshop_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
            ->where('eshop_return.order_status', 'Delivered')
            ->where('eshop_return.ack_status', 'Approved') 
            ->where('eshop_return.manager_id', Auth::user()->id)
			->where('eshop_return.executive_id', $executive_id)
            ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_return.return_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer=='')
        {
            $resultReturnList = DB::table('eshop_return')
            ->select('eshop_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
            ->where('eshop_return.order_status', 'Delivered')
            ->where('eshop_return.ack_status', 'Approved')
            ->where('eshop_return.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_return.manager_id', Auth::user()->id)
			->where('eshop_return.executive_id', $executive_id)			
            ->where('eshop_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_return.return_id','DESC')                    
            ->get();
        }
       elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer!='')
        {
            $resultReturnList = DB::table('eshop_return')
            ->select('eshop_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
            ->where('eshop_return.order_status', 'Delivered')
            ->where('eshop_return.ack_status', 'Approved')
            ->where('eshop_return.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_return.executive_id', $executive_id) 
			->where('eshop_return.customer_id', $customer) 
            ->where('eshop_return.manager_id', Auth::user()->id)
            ->where('eshop_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_return.return_id','DESC')                    
            ->get();
        }

         return view('eshop::sales/managerReport/returnDeliveryReportList', compact('selectedMenu','selectedSubMenu','pageTitle','resultReturnList'));
        
       
    }


    public function eshop_manager_return_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_return')->select('eshop_return.global_company_id','eshop_return.return_id','eshop_return.order_status','eshop_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_return.fo_id')
        ->leftjoin('users', 'eshop_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'eshop_return.route_id')
        ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('eshop_return')
        ->select('eshop_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('eshop_return.global_company_id', Auth::user()->global_company_id)
                        //->where('eshop_return.fo_id',$foMainId)                        
        ->where('eshop_return.return_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('eshop_return_details')
        ->select('eshop_return_details.cat_id','eshop_return_details.return_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_return.return_id','eshop_return.fo_id','eshop_return.order_status','eshop_return.return_no','eshop_return.po_no','eshop_return.party_id')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_return_details.cat_id')
        ->leftjoin('eshop_return', 'eshop_return.return_id', '=', 'eshop_return_details.return_id')
                        //->where('eshop_return.order_status','Delivered')
        ->where('eshop_return_details.return_id',$orderMainId)
        ->groupBy('eshop_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('eshop_return_details')
        ->select('eshop_return_details.delivery_challan','eshop_return.delivery_date','eshop_return_details.return_id','eshop_return.return_id','eshop_return.order_status')

        ->leftjoin('eshop_return', 'eshop_return.return_id', '=', 'eshop_return_details.return_id')
                       // ->where('eshop_return.order_status','Delivered')
        ->where('eshop_return_details.return_id',$orderMainId)
        ->groupBy('eshop_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('eshop_return')->select('eshop_return.return_no','eshop_return.po_no','eshop_return.update_date','eshop_return.global_company_id','eshop_return.return_id','eshop_return.order_status','eshop_return.fo_id','eshop_return.party_id','eshop_return.return_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->leftjoin('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_return.party_id')
        ->where('eshop_return.global_company_id', Auth::user()->global_company_id)       
        ->where('eshop_return.return_id',$orderMainId)
        ->first();

        $customerInfo = DB::table('eshop_return')
                        ->select('eshop_return.return_id','eshop_return.order_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code')
                        ->join('eshop_customer_list', 'eshop_return.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_return.return_id',$orderMainId)
                        ->first();
 

        

        return view('eshop::sales/managerReport/returnDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }


 }
