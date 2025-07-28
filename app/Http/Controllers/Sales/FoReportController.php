<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class FoReportController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 10/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    /* 
       =====================================================================
       ============================ Order  =================================
       =====================================================================
    */    

    public function ssg_report_fo_order()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Order';                    // Required Variable for submenu
        $pageTitle      = 'Order Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.orderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

    public function ssg_report_fo_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        }
        
        return view('sales.report.fo.orderReportList', compact('resultOrderList'));
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

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
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

        $foMainId = Auth::user()->id;
        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->whereNotNull('og.orderid')
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)                                
                                ->first();
        }

        return view('sales.report.fo.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift'));
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

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

// Sharif dashboard report
     public function dashboard_report_fo_delivery($startDate,$endDate)
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


        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.dashboardMonthAchivment', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList','startDate','endDate'));
    }

    
    public function ssg_report_fo_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id',
        'tbl_point.point_id', 'tbl_point.business_type_id', 'tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
        'users.id','users.display_name','users.sap_code')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        //->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('users', 'users.id', '=', 'tbl_user_details.user_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        //->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.free_qty','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.order_type','Delivered')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultAllChalan  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.delivery_challan','tbl_order_details.delivered_date','tbl_order_details.order_id','tbl_order.order_id','tbl_order.order_type')
                        
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.order_type','Delivered')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.delivery_challan')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.order_no','tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile','tbl_retailer.vAddress')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        //->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        //->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        
        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.status','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.status',0) // confirm
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            /*
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.status','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.status',0) // confirm                             
                                ->first();
            
            */
            
            
            $resultBundleSelectedGift = DB::table('tbl_order_gift')
                                    ->where('orderid',$orderMainId)
                                    ->first();    

                                    

            $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->select('tbl_bundle_product_details.*','tbl_product.id','tbl_product.name','tbl_product.depo')
                                    ->leftJoin('tbl_product','tbl_bundle_product_details.giftName','=','tbl_product.id')
                                    ->where('offerId',$resultBundleSelectedGift->offerid)
                                    ->where('slabId',$resultBundleSelectedGift->slab_id)
                                    ->where('groupid',$resultBundleSelectedGift->groupid)
                                    ->get();
                                
                                    
                                
            
                
            //echo '<pre/>';
            //print_r($resultBundleOffersGift); exit;       

        
            
            ///$resultBundleOffersGift = array();
            
            
        }
        
        
        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)                           
                        ->get();

        return view('sales/report/fo/invoiceDetailsDelivery', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan'));
    }



    /* 
       =====================================================================
       ============================ Attendance  ============================
       =====================================================================
    */    

    public function ssg_report_fo_attendance()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Attendance';               // Required Variable for submenu
        $pageTitle      = 'Attendance';              // Page Slug Title
        

        $todate         = date('Y-m-d');
        $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

        return view('sales.report.fo.attendanceReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultAttendanceList'));
    }

    public function ssg_report_fo_attendance_list(Request $request)
    {
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='')
        {
            $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($fromdate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        //->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();  
        }

        //dd($resultAttendanceList);
               

        return view('sales.report.fo.attendanceReportList', compact('resultAttendanceList'));
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
        
        $todate     = date('Y-m-d');
        $resultVisitList = DB::table('ims_tbl_visit_order')
                    ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                    ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                    ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                    ->whereBetween('ims_tbl_visit_order.date', array($todate, $todate))
                    ->orderBy('ims_tbl_visit_order.id','DESC')                    
                    ->get();

        return view('sales.report.fo.visitReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultVisitList'));
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

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
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
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory','resultOrderList'));
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
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.productWiseReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList'));
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
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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

    //MAUNG MGT FO REPORT

    public function fotopten()
    {
        $selectedMenu   = 'FO Top Ten Report';                       // Required Variable for menu
        $selectedSubMenu= 'FO Top Ten Report';                    // Required Variable for submenu
        $pageTitle      = 'FO Top Ten Report'; 
        $fotopten=DB::select("select *,sum(grand_total_value) total from tbl_order where order_type='Ordered' group by fo_id order by total desc limit 0,10 ");
//dd($fotopten);
       // echo"maung"; exit;
return view('sales.report.fo.topten')->with('selectedMenu',$selectedMenu)->with('selectedSubMenu', $selectedSubMenu)
                                     ->with('pageTitle',$pageTitle)->with('fotopten',$fotopten);
   }         // Page Slug Title

    //MAUNG MGT FO REPORT

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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }
}
