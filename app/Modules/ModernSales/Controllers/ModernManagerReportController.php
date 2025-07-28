<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ModernManagerReportController extends Controller
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
        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();
		   

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/managerReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','officerlist','executivelist'));
    }

     public function ssg_manager_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer') ? $request->get('customer') : NULL; 
        $executive_id= $request->get('executive_id') ? $request->get('executive_id') : NULL;
        $fo         = $request->get('fos') ? $request->get('fos') : NULL; 
        
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false;  
        if(Auth::user()->user_type_id ==7){ // Officer
            $officer = true;
        }else if(Auth::user()->user_type_id == 3){ // Executive
            $executive = true;
        }else if(Auth::user()->user_type_id == 6){ // Manager
            $manager = true;
        }else if(Auth::user()->user_type_id == 2){ // Billing
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
                return $query->whereIn('mts_order.ack_status', array('executive_approved','approved'))
                    ->where('mts_order.is_download','NO') 
                    ->where('mts_order.order_status', 'Confirmed');
            }) 
            ->when($manager, function ($query, $manager) {
                return $query->whereIn('mts_order.order_status', array('Delivered','Confirmed'));
            }) 
            ->when($billing, function ($query, $billing) {
                return $query->where('mts_order.order_status', 'Confirmed') 
                ->where('mts_order.ack_status', 'Approved');
            })

            ->where('mts_order.manager_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
           
            ->when($fo, function ($query, $fo) {
                return $query->where('mts_order.fo_id', $fo);
            }) 
            ->when($customer, function ($query, $customer) {
                return $query->where('mts_order.customer_id', $customer);
            }) 
            ->when($executive_id, function ($query, $executive_id) {
                return $query->where('mts_order.executive_id', $executive_id);
            })  
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 

        return view('ModernSales::sales/managerReport/deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_manager_delivery_ims_wise()
    {
        $selectedMenu   = 'Party & Item Wise';                      // Required Variable for menu
        $selectedSubMenu= 'Party & Item Wise';                    // Required Variable for submenu
        $pageTitle      = 'Party & Item Wise';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();
           

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/managerReport/deliveryReportImsWise', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','officerlist','executivelist'));
    }

     public function ssg_manager_delivery_list_ims_wise(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer') ? $request->get('customer') : NULL; 
        $executive_id= $request->get('executive_id') ? $request->get('executive_id') : NULL;
        $fo         = $request->get('fos') ? $request->get('fos') : NULL; 
        
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false;  
        
        if(Auth::user()->user_type_id ==7){ // Officer
            $officer = true;
        }else if(Auth::user()->user_type_id == 3){ // Executive
            $executive = true;
        }else if(Auth::user()->user_type_id == 6){ // Manager
            $manager = true;
        }else if(Auth::user()->user_type_id == 2){ // Billing
            $billing = true;
        }   
 

            $resultOrderList = DB::table('mts_order')
            ->select(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d')) as order_date "), 'mts_customer_list.sap_code as customer_code', 'mts_customer_list.name', 'tbl_product.sap_code', 'tbl_product.name as product_name',
            DB::raw('sum(mts_order_details.order_qty) as total_order_qty'),
            DB::raw('sum(mts_order_details.order_total_value) as total_order_value') )
            ->join('mts_order_details','mts_order_details.order_id','=','mts_order.order_id')
            ->join('mts_customer_list','mts_customer_list.customer_id','=','mts_order.customer_id')
            ->join('tbl_product','tbl_product.id','=','mts_order_details.product_id') 
            ->whereIn('mts_order.order_status',['Delivered', 'Confirmed'])
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate)) 
            
            //->where('mts_order.manager_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
           
            ->when($fo, function ($query, $fo) {
                return $query->where('mts_order.fo_id', $fo);
            }) 
            ->when($customer, function ($query, $customer) {
                return $query->where('mts_order.customer_id', $customer);
            }) 
            ->when($executive_id, function ($query, $executive_id) {
                return $query->where('mts_order.executive_id', $executive_id);
            })  
            ->groupBy('mts_customer_list.customer_id')
            ->groupBy('mts_order_details.product_id')
            ->groupBy(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"))
            ->orderBy('mts_order.order_date','DESC')
            ->get();


            //return response()->json($resultOrderList);
        return view('ModernSales::sales/managerReport/deliveryReportListImsWise', compact('resultOrderList'));
    } 

    public function ssg_manager_delivery_list_backup(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
		$executive_id   = $request->get('executive_id');
        $fo         = $request->get('fos'); 
		
		if($fromdate!='' && $todate!='' && $executive_id==''&& $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved') 
            ->where('mts_order.manager_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved') 
            ->where('mts_order.manager_id', Auth::user()->id)
			->where('mts_order.executive_id', $executive_id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $fo!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved') 
            ->where('mts_order.manager_id', Auth::user()->id)
			->where('mts_order.executive_id', $executive_id)
            ->where('mts_order.fo_id', $fo)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $customer!='' && $fo!='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved') 
            ->where('mts_order.manager_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_order.customer_id', $customer)
			->where('mts_order.executive_id', $executive_id)
            ->where('mts_order.fo_id', $fo)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/managerReport/deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_manager_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'mts_order.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_order.route_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_order')
        ->select('mts_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_order.fo_id',$foMainId)                        
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                        //->where('mts_order.order_status','Delivered')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_order_details')
        ->select('mts_order_details.delivery_challan','mts_order.delivery_date','mts_order_details.order_id','mts_order.order_id','mts_order.order_status')

        ->leftjoin('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                       // ->where('mts_order.order_status','Delivered')
        ->where('mts_order_details.order_id',$orderMainId)
        ->groupBy('mts_order_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_order')->select('mts_order.order_no','mts_order.po_no','mts_order.update_date','mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','mts_order.party_id','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_order.order_id',$orderMainId)
        ->first();

        $orderCommission = DB::table('mts_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'), DB::raw('SUM(delivery_commission_value) AS delivery_commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id) 
        ->first();

        

        return view('ModernSales::sales/managerReport/deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan', 'orderCommission'));

    }
	
	
	
	 // Advance Replace Delivery report for Manager part


    public function mts_manager_replace_delivery_report()
    {
        $selectedMenu   = 'Advance Replace Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';             // Required Variable for submenu
        $pageTitle      = 'Advance Replace Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get(); 

         $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/managerReport/replaceDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','executivelist','officerlist'));
    }

    public function mts_manager_replace_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');
		$executive_id   = $request->get('executive_id'); 

		if($fromdate!='' && $todate!='' && $executive_id=='' && $officer=='' && $customer=='')
        { 
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved') 
            ->where('mts_replace.manager_id',  Auth::user()->id) 
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer=='' && $customer=='')
        { 
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved') 
            ->where('mts_replace.manager_id',  Auth::user()->id)
			->where('mts_replace.executive_id', $executive_id)
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)  
            ->where('mts_replace.manager_id',  Auth::user()->id)
			->where('mts_replace.executive_id', $executive_id)
            ->where('mts_replace.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer!='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_replace.executive_id', $executive_id)
			->where('mts_replace.customer_id', $customer)
            ->where('mts_replace.fo_id', $officer) 
            ->where('mts_replace.manager_id',  Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/managerReport/replaceDeliveryReportList', compact('resultOrderList'));
    }


    public function mts_manager_replace_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Replace Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Replace Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'mts_replace.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_replace.route_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_replace')
        ->select('mts_replace.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                        //->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_replace_details')
        ->select('mts_replace_details.delivery_challan','mts_replace.delivery_date','mts_replace_details.replace_id','mts_replace.replace_id','mts_replace.order_status')

        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                       // ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.replace_no','mts_replace.po_no','mts_replace.update_date','mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','mts_replace.party_id','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

         $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$orderMainId)
                        ->first();

 

        

        return view('ModernSales::sales/managerReport/replaceDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }
	
	// Return Manager part 
	
	 public function mts_manager_return_delivery_report()
    {
        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();
 
          $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();

        return view('ModernSales::sales/managerReport/returnDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','officerlist','executivelist'));
    }

    public function mts_manager_return_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');
		$executive_id   = $request->get('executive_id'); 

        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';
		
		if($fromdate!='' && $todate!='' && $executive_id=='' && $officer=='' && $customer=='')
        { 
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved') 
            ->where('mts_return.manager_id', Auth::user()->id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }

        elseif($fromdate!='' && $todate!='' && $executive_id!='' &&  $officer=='' && $customer=='')
        { 
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved') 
            ->where('mts_return.manager_id', Auth::user()->id)
			->where('mts_return.executive_id', $executive_id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer=='')
        {
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.manager_id', Auth::user()->id)
			->where('mts_return.executive_id', $executive_id)			
            ->where('mts_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
       elseif($fromdate!='' && $todate!='' && $executive_id!='' && $officer!='' && $customer!='')
        {
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.executive_id', $executive_id) 
			->where('mts_return.customer_id', $customer) 
            ->where('mts_return.manager_id', Auth::user()->id)
            ->where('mts_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }

         return view('ModernSales::sales/managerReport/returnDeliveryReportList', compact('selectedMenu','selectedSubMenu','pageTitle','resultReturnList'));
        
       
    }


    public function mts_manager_return_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'mts_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_return.route_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_return')
        ->select('mts_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        //->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_return_details')
        ->select('mts_return_details.delivery_challan','mts_return.delivery_date','mts_return_details.return_id','mts_return.return_id','mts_return.order_status')

        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                       // ->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.return_no','mts_return.po_no','mts_return.update_date','mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','mts_return.party_id','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$orderMainId)
                        ->first();
 

        

        return view('ModernSales::sales/managerReport/returnDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }


    public function achievement_sales_report()
    {
        $selectedMenu   = 'Report';                      // Required Variable for menu
        $selectedSubMenu= 'Achievement Report';                    // Required Variable for submenu
        $pageTitle      = 'Achievement Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();
           

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/managerReport/achievementSalesReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','officerlist','executivelist'));
    }

    public function achievement_sales_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer') ? $request->get('customer') : NULL; 
        $executive_id= $request->get('executive_id') ? $request->get('executive_id') : NULL;
        $fo         = $request->get('fos') ? $request->get('fos') : NULL; 

        $resultOrderList = DB::table('mts_customer_list') 
        ->select('mts_customer_list.customer_id','mts_customer_list.name','mts_customer_list.sap_code','mts_role_hierarchy.management_id','mts_role_hierarchy.manager_id','mts_role_hierarchy.executive_id','mts_role_hierarchy.officer_id')
        ->Join('mts_customer_define_executive','mts_customer_list.customer_id','=','mts_customer_define_executive.customer_id')
        ->Join('mts_role_hierarchy','mts_customer_define_executive.executive_id','=','mts_role_hierarchy.officer_id')
        ->where('mts_customer_list.status',0)
        ->when($fo, function ($query, $fo) {
                return $query->where('mts_role_hierarchy.officer_id', $fo);
            }) 
            ->when($customer, function ($query, $customer) {
                return $query->where('mts_customer_list.customer_id', $customer);
            }) 
            ->when($executive_id, function ($query, $executive_id) {
                return $query->where('mts_role_hierarchy.executive_id', $executive_id);
            })
        ->groupBy('mts_customer_list.customer_id')
        ->get();  

        return view('ModernSales::sales/managerReport/achievementSalesReportList', compact('resultOrderList','fromdate','todate'));
    }


 }

