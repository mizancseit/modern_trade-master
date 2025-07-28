<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class SalesAdminController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 30/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    /* 
       =====================================================================
       ============================ Order Report============================
       =====================================================================
    */    

    public function ssg_depot_report()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= '';                            // Required Variable for submenu
        $pageTitle      = 'Depot Operation Report';     // Page Slug Title

        $resultDepot    = DB::table('tbl_point')->select('point_name')
                        ->where('is_depot', 1)
                        ->where('point_status', 0)
                        ->orderBy('point_name')
                        ->get();

        return view('sales/report/salesAdmin/depotReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultDepot'));
    }

    public function ssg_depot_report_list(Request $request)
    {
        $fromdate   = ''; //date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($todate!='')
        {
            $resultDepot    = DB::table('tbl_point')->select('point_name','point_id')
                            ->where('is_depot', 1)
                            ->where('point_status', 0)
                            //->where('point_id', 267)
                            ->orderBy('point_name')
                            ->get();
        }        
        
        return view('sales/report/salesAdmin/depotReportViewList', compact('resultDepot','todate'));
    }





























    public function ssg_points_list(Request $request)
    {
        $serial = 3;
        $resultPoints = DB::table('tbl_point')->where('point_division',$request->get('divisions'))->get();
        return view('sales.report.distributor.allDropDown', compact('resultPoints','serial'));
    }

    public function ssg_fos_list(Request $request)
    {
        $serial = 4;

        $resultFos = DB::table('tbl_user_business_scope')
                    ->where('point_division',$request->get('division'))->get();

        return view('sales.report.report.distributor.allDropDown', compact('resultFos','serial'));
    }




    


    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Order';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';         // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        return view('sales.report.fo.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }



    /* 
       =====================================================================
       ============================ Delivery  ==============================
       =====================================================================
    */    

    public function ssg_report_fo_delivery()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        return view('sales.report.fo.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute'));
    }

    public function ssg_report_fo_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_report_fo_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';            // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        return view('sales.report.fo.invoiceDetailsDelivery', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }



    /* 
       =====================================================================
       ============================ Visit  =================================
       =====================================================================
    */    

    public function ssg_report_fo_visit()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Visit';                    // Required Variable for submenu
        $pageTitle      = 'Visit Report';            // Page Slug Title
        

        return view('sales.report.fo.visitReport', compact('selectedMenu','selectedSubMenu','pageTitle'));
    }

    public function ssg_report_fo_visit_list(Request $request)
    {
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('orderType')=='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();  
        }
        if($fromdate!='' && $todate!='' && $request->get('orderType')!='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->where('ims_tbl_visit_order.status', $request->get('orderType'))
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();   
        }       
        

        return view('sales.report.fo.visitReportList', compact('resultVisitList'));
    }


    /* 
       =====================================================================
       ========================= Order Vs Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_fo_order_vs_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'Oeder Vs Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Oeder Vs Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->orWhere('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        return view('sales.report.fo.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute'));
    }

    public function ssg_report_fo_order_vs_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.orderVsDeliveryReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Category Wise Order  ======================
       =====================================================================
    */    

    public function ssg_report_fo_category_wise_order()
    {
        $selectedMenu   = 'Report';                                  // Required Variable for menu
        $selectedSubMenu= 'Category Wise Order';                    // Required Variable for submenu
        $pageTitle      = 'Category Wise Order Report';            // Page Slug Title

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory'));
    }

    public function ssg_report_fo_category_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('category')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->where('tbl_order_details.cat_id', $request->get('category'))
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
            
        }
        
        return view('sales.report.fo.categoryWiseOrderReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Product Wise Order  =======================
       =====================================================================
    */    

    public function ssg_report_fo_product_wise()
    {
        $selectedMenu   = 'Report';                             // Required Variable for menu
        $selectedSubMenu= 'Product Wise';                      // Required Variable for submenu
        $pageTitle      = 'Product Wise Order Report';        // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', '!=', 'Ordered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.productWiseReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    public function ssg_report_fo_category_wise_product_list(Request $request)
    {

        $category   = $request->get('category');
        $serial     = 1;

        $resultProduct = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product.name AS pname','tbl_product.id AS pid')

                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                      
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order_details.cat_id', $category)
                        ->groupBy('tbl_order_details.product_id')                    
                        ->orderBy('tbl_order_details.product_id','DESC')                    
                        ->get();

        return view('sales.report.distributor.allDropDown', compact('resultProduct','serial'));

    }

    public function ssg_report_fo_product_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = Auth::user()->id;
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        
        return view('sales.report.fo.productWiseReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= SKU Wise Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_sku_wise_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'SKU Wise Delivery';                    // Required Variable for submenu
        $pageTitle      = 'SKU Wise Delivery Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    public function ssg_report_sku_wise_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }
}
