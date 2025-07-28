<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class DistributorReportController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 24/12/2017
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    /* 
       =====================================================================
       ============================ Delivery  ==============================
       =====================================================================
    */    

    public function ssg_report_order_delivery()
    {
        $selectedMenu   = 'Report';                      // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();


        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                       // ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.total_delivery_qty', '>', 0)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();


        //dd($resultOrderList);

        return view('sales.report.distributor.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        //->where('tbl_order.order_type', 'Delivered')
						 ->where('tbl_order.total_delivery_qty', '>', 0)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        //->where('tbl_order.order_type', 'Delivered')
						 ->where('tbl_order.total_delivery_qty', '>', 0)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.distributor.deliveryReportList', compact('resultOrderList'));
    }
	
	


    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

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
                        //->where('tbl_order.order_type','Delivered')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultAllChalan  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.delivery_challan','tbl_order_details.delivered_date','tbl_order_details.order_id','tbl_order.order_id','tbl_order.order_type')
                        
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                       // ->where('tbl_order.order_type','Delivered')
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

        //return view('sales.report.distributor.deliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo'));
        
		return view('sales/report/distributor/deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan'));
    
	}
	
	public function ssg_rollback_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id',
		'tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
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
                        ->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
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

        //return view('sales.report.distributor.deliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo'));
        
		return view('sales/report/distributor/deliveryRollBackView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan'));
    
	}



    /* 
       =====================================================================
       ========================= Order Vs Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_order_vs_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'Oeder Vs Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Oeder Vs Delivery Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.distributor.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_report_order_vs_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.distributor.orderVsDeliveryReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Category Wise Order  ======================
       =====================================================================
    */    

    public function ssg_report_category_wise_order()
    {
        $selectedMenu   = 'Report';                                  // Required Variable for menu
        $selectedSubMenu= 'Category Wise Order';                    // Required Variable for submenu
        $pageTitle      = 'Category Wise Order Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        
        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_report_category_wise_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $request->get('fos'))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
            
        }
        
        return view('sales.report.distributor.categoryWiseOrderReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= SKU Wise Order  ===========================
       =====================================================================
    */    

    public function ssg_report_sku_wise_order()
    {
        $selectedMenu   = 'Report';                             // Required Variable for menu
        $selectedSubMenu= 'SKU Wise Order';                    // Required Variable for submenu
        $pageTitle      = 'SKU Wise Order Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

         $todate     = date('Y-m-d');
         //$todate     = '2018-06-27';
         $userid =  Auth::user()->id;

        /*$resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();*/

       $resultOrderList = DB::select("SELECT e.distributor_id,date(e.order_date),tbl_product_category.id as cid,tbl_product_category.name as cname,e.pid,tbl_product.name as pname,sum(e.orderQty) AS orderQty, sum(e.deliveryQty) AS deliveryQty, sum(e.spFreeQty + e.freeQty + e.spAndFreeQty) AS freeQty, sum(e.wastageQty + e.orderWastageQty) AS totalWastageQty, sum(e.deliveryOrderWastageQty + e.wastageDeliveryQty) AS totalDeliveryWastageQty
        FROM (
        SELECT tbl_order.distributor_id AS distributor_id, tbl_order.order_date AS order_date, tbl_order_details.cat_id,tbl_order_details.product_id as pid,tbl_order_details.order_qty AS orderQty, tbl_order_details.delivered_qty AS deliveryQty, 0 AS freeQty, 0 AS spFreeQty, 0 AS spAndFreeQty, tbl_order_details.wastage_qty AS orderWastageQty, tbl_order_details.replace_delivered_qty AS deliveryOrderWastageQty, 0 AS wastageQty, 0 AS wastageDeliveryQty FROM tbl_order_details
        INNER JOIN tbl_order ON tbl_order.order_id = tbl_order_details.order_id 
        WHERE tbl_order.distributor_id = '$userid'  AND date(tbl_order.order_date) = '$todate'
        UNION ALL
        SELECT tbl_order_free_qty.distributor_id AS distributor_id,tbl_order_free_qty.order_date AS order_date, tbl_order_free_qty.catid, tbl_order_free_qty.product_id as pid, 0 AS orderQty,0 AS deliveryQty, tbl_order_free_qty.total_free_qty AS freeQty, 0 AS spFreeQty, 0 AS spAndFreeQty, 0 AS orderWastageQty, 0 AS deliveryOrderWastageQty, 0 AS wastageQty, 0 AS wastageDeliveryQty FROM tbl_order_free_qty
         WHERE tbl_order_free_qty.distributor_id = '$userid'  AND date(tbl_order_free_qty.order_date) = '$todate'
        UNION ALL
        SELECT tbl_order_special_free_qty.distributor_id AS distributor_id,tbl_order_special_free_qty.order_date AS order_date, tbl_order_special_free_qty.catid, tbl_order_special_free_qty.product_id as pid, 0 AS orderQty,0 AS deliveryQty, 0 AS freeQty, tbl_order_special_free_qty.total_free_qty AS spFreeQty, 0 AS spAndFreeQty, 0 AS orderWastageQty, 0 AS deliveryOrderWastageQty, 0 AS wastageQty, 0 AS wastageDeliveryQty FROM tbl_order_special_free_qty
         WHERE tbl_order_special_free_qty.distributor_id = '$userid'  AND date(tbl_order_special_free_qty.order_date) = '$todate'
        UNION ALL
        SELECT tbl_order_special_and_free_qty.distributor_id AS distributor_id, tbl_order_special_and_free_qty.order_date AS order_date, tbl_order_special_and_free_qty.catid, tbl_order_special_and_free_qty.product_id as pid, 0 AS orderQty,0 AS deliveryQty, 0 AS freeQty, 0 AS spFreeQty, tbl_order_special_and_free_qty.total_free_qty AS spAndFreeQty, 0 AS orderWastageQty, 0 AS deliveryOrderWastageQty, 0 AS wastageQty, 0 AS wastageDeliveryQty FROM tbl_order_special_and_free_qty
        WHERE tbl_order_special_and_free_qty.distributor_id = '$userid'  AND date(tbl_order_special_and_free_qty.order_date) = '$todate'
        UNION ALL
        SELECT tbl_wastage.distributor_id AS distributor_id,tbl_wastage.order_date AS order_date, tbl_wastage_details.cat_id, tbl_wastage_details.product_id as pid, 0 AS orderQty,0 AS deliveryQty, 0 AS freeQty, 0 AS spFreeQty, 0 AS spAndFreeQty, 0 AS orderWastageQty, 0 AS deliveryOrderWastageQty, tbl_wastage_details.wastage_qty AS wastageQty, tbl_wastage_details.replace_delivered_qty AS wastageDeliveryQty FROM tbl_wastage_details
        INNER JOIN tbl_wastage ON tbl_wastage.order_id = tbl_wastage_details.order_id
        WHERE tbl_wastage.distributor_id = '$userid'  AND date(tbl_wastage.order_date) = '$todate'
        ) AS e
        join tbl_product on e.pid=tbl_product.id 
        join tbl_product_category on tbl_product_category.id=tbl_product.category_id 
        group by e.pid ORDER BY cname");

     // dd($resultOrderList);



        return view('sales.report.distributor.skuWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList'));
    }

    public function ssg_report_category_wise_product_list(Request $request)
    {

        $category   = $request->get('category');
        $serial     = 1;

        $resultProduct = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product.name AS pname','tbl_product.id AS pid')

                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order_details.cat_id', $category)
                        ->groupBy('tbl_order_details.product_id')                    
                        ->orderBy('tbl_order_details.product_id','DESC')                    
                        ->get();

        return view('sales.report.distributor.allDropDown', compact('resultProduct','serial'));

    }

    public function ssg_report_sku_wise_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');
        $userid     =  Auth::user()->id;
        $global_company_id = Auth::user()->global_company_id;

        $where="tbl_order.order_date BETWEEN '$fromdate' AND '$todate' AND tbl_order.distributor_id='$userid' AND tbl_order.global_company_id ='$global_company_id'";

        if($fo!='')
        {
        $where=$where ." AND tbl_order.fo_id='$fo'"; 
        }
        if($category!='')
        {
        $where=$where ." AND tbl_order_details.cat_id='$category'";  
        }
        if($products!='')
        {
        $where=$where ." AND tbl_order_details.product_id='$products'";  
        }




        if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseOrderReportList', compact('resultOrderList'));
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

        $todate     = date('Y-m-d');

        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList'));
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
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ============ Retailer Remaining Commission Report  ==================
       =====================================================================
    */    

    public function ssg_report_remaining_commission()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'Commission';                    // Required Variable for submenu
        $pageTitle      = 'Retailer Remaining Commission Report';            // Page Slug Title

        $resultRoutes    = DB::table('tbl_retailer_commission_history')
                        ->select('tbl_retailer_commission_history.routeid','tbl_route.route_id','tbl_route.rname')

                        ->leftJoin('tbl_route', 'tbl_route.route_id', '=', 'tbl_retailer_commission_history.routeid')

                        ->where('tbl_retailer_commission_history.distributorid', Auth::user()->id)
                        ->groupBy('tbl_retailer_commission_history.routeid')
                        ->orderBy('tbl_route.rname','DESC')                    
                        ->get();

        $date   = date('Y-m-d');

        $resultRetailers    = DB::table('tbl_retailer_commission_history')
                        ->select(DB::raw('SUM(total_commission_amount) AS totalBalance'),DB::raw('SUM(total_buy_commission_amount) AS totalBuyBalance'),'tbl_retailer_commission_history.balance_amount','tbl_retailer_commission_history.date','tbl_retailer_commission_history.routeid','tbl_retailer_commission_history.retailerid','tbl_route.route_id','tbl_route.rname','tbl_retailer.retailer_id','tbl_retailer.name')

                        ->leftJoin('tbl_route', 'tbl_route.route_id', '=', 'tbl_retailer_commission_history.routeid')
                        ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_retailer_commission_history.retailerid')

                        ->where('tbl_retailer_commission_history.distributorid', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_retailer_commission_history.date,'%Y-%m-%d'))"), array($date, $date))
                        ->groupBy('tbl_retailer_commission_history.retailerid')
                        ->orderBy('tbl_retailer.name','ASC')                    
                        ->get();
        
        return view('sales.report.distributor.retailerCommissionBalanceReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoutes','resultRetailers'));
    }

    public function ssg_report_remaining_commission_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $routeID    = $request->get('routeID');
        //$category   = $request->get('category');
        //$products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $routeID=='')
        {
            $resultRetailers    = DB::table('tbl_retailer_commission_history')
                        ->select(DB::raw('SUM(total_commission_amount) AS totalBalance'),DB::raw('SUM(total_buy_commission_amount) AS totalBuyBalance'),'tbl_retailer_commission_history.balance_amount','tbl_retailer_commission_history.date','tbl_retailer_commission_history.routeid','tbl_retailer_commission_history.retailerid','tbl_route.route_id','tbl_route.rname','tbl_retailer.retailer_id','tbl_retailer.name')

                        ->leftJoin('tbl_route', 'tbl_route.route_id', '=', 'tbl_retailer_commission_history.routeid')
                        ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_retailer_commission_history.retailerid')

                        ->where('tbl_retailer_commission_history.distributorid', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_retailer_commission_history.date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->groupBy('tbl_retailer_commission_history.retailerid')
                        ->orderBy('tbl_retailer.name','ASC')                    
                        ->get();
        }
        if($fromdate!='' && $todate!='' && $routeID!='')
        {
            $resultRetailers    = DB::table('tbl_retailer_commission_history')
                        ->select(DB::raw('SUM(total_commission_amount) AS totalBalance'),DB::raw('SUM(total_buy_commission_amount) AS totalBuyBalance'),'tbl_retailer_commission_history.balance_amount','tbl_retailer_commission_history.date','tbl_retailer_commission_history.routeid','tbl_retailer_commission_history.retailerid','tbl_route.route_id','tbl_route.rname','tbl_retailer.retailer_id','tbl_retailer.name')

                        ->leftJoin('tbl_route', 'tbl_route.route_id', '=', 'tbl_retailer_commission_history.routeid')
                        ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_retailer_commission_history.retailerid')

                        ->where('tbl_retailer_commission_history.distributorid', Auth::user()->id)
                        ->where('tbl_retailer_commission_history.routeid', $routeID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_retailer_commission_history.date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->groupBy('tbl_retailer_commission_history.retailerid')
                        ->orderBy('tbl_retailer.name','ASC')                    
                        ->get();
        }        
        
        return view('sales.report.distributor.retailerCommissionBalanceReportList', compact('resultRetailers'));
    }
	
	
	public function delivery_rollback(Request $request)
    {
		
		$order_id = $request->get('orderid');
		
		//echo $order_id; exit;
		
		$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')
									");
									
		$point_id = $depotList[0]->point_id;
		
		if($order_id && $point_id > 0)
		{
			/* Unit wise TEST 
			$this->prod_rollback($order_id,$point_id);
			$this->offer_rollback($order_id,$point_id);
			$this->credit_ledger_adjust($order_id);
			$this->order_ready_for_delivery($order_id);
			return Redirect::to('/order')->with('success', 'Successfully Order RollBack');
			*/
			
			
			if(	$this->prod_rollback($order_id,$point_id) && $this->offer_rollback($order_id,$point_id) 
				&& $this->credit_ledger_adjust($order_id)	)
			{
				if($this->order_ready_for_delivery($order_id))
				{
					return Redirect::to('/order')->with('success', 'Successfully Order RollBack');
				
				} else{
					
					return Redirect::to('/report/delivery')->with('danger', 'Order RollBack Failed');
					
				}
			
			} else {
				
				return Redirect::to('/report/delivery')->with('danger', 'Order RollBack Failed');
			}
			
			
			
			
		}
    
    }
	
	
	private function offer_rollback($order_id, $point_id)
	{
		if($order_id && $point_id)
		{
			/* Regular Offer Roll Back */
			$this->regular_offer_rollback($order_id, $point_id);
			$this->regular_and_offer_rollback($order_id, $point_id);
			
			/* Spec Offer Roll Back */
			$this->special_offer_rollback($order_id, $point_id);
			$this->special_and_offer_rollback($order_id, $point_id);
			
			/* Spec Value wise Offer Roll Back */
			$this->special_value_wise_offer_rollback($order_id, $point_id);
			
			/* Bundle Offer Roll Back */
			$this->bundle_offer_rollback($order_id, $point_id);
			
		  return true;
			
		}
		
		return false;
		
	}
	
	
	private function prod_rollback($order_id, $point_id)
	{
		/*
		$depotPayment=DB::select("UPDATE tbl_order_details od LEFT JOIN depot_stock stk  ON od.product_id = stk.product_id 
			SET stk.stock_qty = stk.stock_qty + od.delivered_qty
				WHERE stk.point_id = 462 AND od.order_id = 2403 ");
		*/

		$OrderDelvData = DB::select("SELECT od.order_id, od.point_id, od.distributor_id, odetails.*
									FROM tbl_order od JOIN tbl_order_details odetails ON od.order_id = odetails.order_id
									WHERE od.order_id = '".$order_id."' and od.order_type = 'Delivered'"); 
		
		if(sizeof($OrderDelvData)>0)
		{
			foreach($OrderDelvData as $RowOrderDelvData)
			{
				// normal delivery
				if ($RowOrderDelvData->delivered_qty > 0 )
				{
					$stock_affected = DB::update("UPDATE depot_stock stk  
											SET stk.stock_qty = stk.stock_qty + $RowOrderDelvData->delivered_qty
											WHERE stk.point_id = ? AND stk.product_id = ?", 
											["$point_id","$RowOrderDelvData->product_id"]);
											
					$insData = array();
					
					$insData['reference_id'] = $RowOrderDelvData->order_id;
					$insData['point_id'] = $point_id;
					$insData['cat_id'] = $RowOrderDelvData->cat_id;
					$insData['product_id'] = $RowOrderDelvData->product_id;
					$insData['product_qty'] = $RowOrderDelvData->delivered_qty;
					$insData['product_value'] = $RowOrderDelvData->delivered_value;
					$insData['inventory_type'] = 1; // stock-in
					$insData['transaction_type'] = 'regular_reverse';
					
					$this->write_log($insData);		

					reset($insData);
				}
				
				// wastage delivery
				if ($RowOrderDelvData->replace_delivered_qty > 0 )
				{
					$stock_affected = DB::update("UPDATE depot_stock stk  
											SET stk.stock_qty = stk.stock_qty + $RowOrderDelvData->replace_delivered_qty
											WHERE stk.point_id = ? AND stk.product_id = ?", 
											["$point_id","$RowOrderDelvData->product_id"]);
											
					$insData = array();
					
					$insData['reference_id'] = $RowOrderDelvData->order_id;
					$insData['point_id'] = $point_id;
					$insData['cat_id'] = $RowOrderDelvData->cat_id;
					$insData['product_id'] = $RowOrderDelvData->product_id;
					$insData['product_qty'] = $RowOrderDelvData->replace_delivered_qty;
					$insData['product_value'] = 0; //$RowOrderDelvData->delivered_value;
					$insData['inventory_type'] = 1; // stock-in
					$insData['transaction_type'] = 'wastage_reverse';
					
					$this->write_log($insData);		

					reset($insData);
				}
				
				
			}
			
			return true;
		}
		
		return false;
		
	}
	
	
	private function write_log($insData = array())
	{
		if( sizeof($insData)>0 )
		{
		
			DB::table('depot_inventory')->insert(
				[
					'reference_id'		 => $insData['reference_id'],
					'point_id'           => $insData['point_id'],
					'depot_in_charge'    => Auth::user()->id,
					'cat_id'             => $insData['cat_id'],
					'product_id'         => $insData['product_id'],
					'product_qty'        => $insData['product_qty'],
					'product_value'      => $insData['product_value'],
					'inventory_date'     => date('Y-m-d'),
					'inventory_type'     => $insData['inventory_type'],
					'transaction_type'   => $insData['transaction_type'],
					'global_company_id'  => Auth::user()->global_company_id,
					'created_by'         => Auth::user()->id
				]
			);
		}			
	
	}

	
	
	
	private function regular_offer_rollback($order_id, $point_id)
	{
							/* Regular Offer Roll Back step by step*/
		
		$regularOffer = DB::select("SELECT rf.* FROM tbl_order_free_qty rf WHERE rf.order_id = '".$order_id."' AND rf.status = '0'"); 
									
		if(sizeof($regularOffer)>0)
		{
			foreach($regularOffer as $RowRegularOffer)
			{
				
				$regular_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowRegularOffer->free_delivery_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowRegularOffer->product_id"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowRegularOffer->catid;
				$insData['product_id'] = $RowRegularOffer->product_id;
				$insData['product_qty'] = $RowRegularOffer->free_delivery_qty;
				$insData['product_value'] = $RowRegularOffer->free_delivery_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);

				/* offer update to originial state*/
				DB::table('tbl_order_free_qty')->where('order_id',$order_id)
											   ->where('product_id',$RowRegularOffer->product_id)
											   ->update(
														[
														 'free_delivery_qty' => 0,
														 'free_delivery_value' => 0,
														 'status' => NULL
														]
												); 
				
			}
			
			
		}	
		
		/* Roll Back at whole 
		$regular_stk_affcd = DB::update('UPDATE tbl_order_free_qty rf JOIN depot_stock stk  ON rf.product_id = stk.product_id 
										SET stk.stock_qty = stk.stock_qty + rf.free_delivery_qty
										WHERE stk.point_id = ? AND rf.order_id = ? and rf.status = 0', 
										["$point_id","$order_id"]);
										
		DB::table('tbl_order_free_qty')->where('order_id',$order_id)->delete(); 
		*/
	}
	
	
	private function regular_and_offer_rollback($order_id, $point_id)
	{
							/* Regular AND Offer Roll Back step by step*/
		
		$regularAndOffer = DB::select("SELECT randf.* FROM tbl_order_regular_and_free_qty randf WHERE randf.order_id = '".$order_id."' 
										AND randf.status = '0'"); 
									
		if(sizeof($regularAndOffer)>0)
		{
			foreach($regularAndOffer as $RowRegularAndOffer)
			{
				
				$regular_and_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowRegularAndOffer->free_delivery_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowRegularAndOffer->product_id"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowRegularAndOffer->catid;
				$insData['product_id'] = $RowRegularAndOffer->product_id;
				$insData['product_qty'] = $RowRegularAndOffer->free_delivery_qty;
				$insData['product_value'] = $RowRegularAndOffer->free_delivery_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);

				/* AND offer update to originial state*/
				DB::table('tbl_order_regular_and_free_qty')->where('order_id',$order_id)
											   ->where('product_id',$RowRegularAndOffer->product_id)
											   ->update(
														[
														 'free_delivery_qty' => 0,
														 'free_delivery_value' => 0,														
														 'status' => NULL
														]
												); 
				
			}
			
			
		}	
		
		/* Regular AND Offer Roll Back whole at a time
		$regular_and_stk_affcd = DB::update('UPDATE tbl_order_regular_and_free_qty randf JOIN depot_stock stk  
											ON randf.product_id = stk.product_id 
											SET stk.stock_qty = stk.stock_qty + randf.free_delivery_qty
											WHERE stk.point_id = ? AND randf.order_id = ? and randf.status = 0',
											["$point_id","$order_id"]);
											
		DB::table('tbl_order_regular_and_free_qty')->where('order_id',$order_id)->delete();		
		
		*/
		
	}
	
	private function special_offer_rollback($order_id, $point_id)
	{
							/* Special Offer Roll Back step by step*/
		
		$specialOffer = DB::select("SELECT sf.* FROM tbl_order_special_free_qty sf WHERE sf.order_id = '".$order_id."' 
										AND sf.status = '0'"); 
									
		if(sizeof($specialOffer)>0)
		{
			foreach($specialOffer as $RowSpecialOffer)
			{
				
				$sp_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowSpecialOffer->total_free_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowSpecialOffer->product_id"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowSpecialOffer->catid;
				$insData['product_id'] = $RowSpecialOffer->product_id;
				$insData['product_qty'] = $RowSpecialOffer->total_free_qty;
				$insData['product_value'] = $RowSpecialOffer->free_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);

				/* Spec offer update to originial state*/
				DB::table('tbl_order_special_free_qty')->where('order_id',$order_id)
											   ->where('product_id',$RowSpecialOffer->product_id)
											   ->update(
														[ 
														 'total_free_qty' => 0,
														 'free_value' => 0,														
														 'status' => NULL
														]
												); 
				
			}
			
			
		}	
									
		
		/* Special Offer Roll Back whole at a time
		$spcl_stk_affcd = DB::update('UPDATE tbl_order_special_free_qty spf JOIN depot_stock stk  ON spf.product_id = stk.product_id 
									SET stk.stock_qty = stk.stock_qty + spf.free_delivery_qty
									WHERE stk.point_id = ? AND spf.order_id = ? and spf.status = 0', 
									["$point_id","$order_id"]);	
									
		DB::table('tbl_order_special_free_qty')->where('order_id',$order_id)->delete();
		*/
		
	}
	
	
	private function special_value_wise_offer_rollback($order_id, $point_id)
	{
							/* Special Value Wise Offer Roll Back step by step*/
		
		$specialOffer = DB::select("SELECT sf.* FROM tbl_order_special_free_qty sf WHERE sf.order_id = '".$order_id."' 
										AND sf.status = '3'"); 
									
		if(sizeof($specialOffer)>0)
		{
			foreach($specialOffer as $RowSpecialOffer)
			{
				
				$sp_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowSpecialOffer->total_free_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowSpecialOffer->product_id"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowSpecialOffer->catid;
				$insData['product_id'] = $RowSpecialOffer->product_id;
				$insData['product_qty'] = $RowSpecialOffer->total_free_qty;
				$insData['product_value'] = $RowSpecialOffer->free_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);
				
			}
			
			//clear free pc
			DB::table('tbl_order_special_free_qty')->where('order_id',$order_id)->where('status',3)->delete();
			//clear free pc
			DB::table('tbl_retailer_commission_history')->where('orderid',$order_id)->delete();
			
			
		}	
		
		
	}
	
	
	private function special_and_offer_rollback($order_id, $point_id)
	{
							/* Special AND Offer Roll Back step by step*/
		
		$specialAndOffer = DB::select("SELECT sandf.* FROM tbl_order_special_and_free_qty sandf WHERE sandf.order_id = '".$order_id."' 
										AND sandf.status = '0'"); 
									
		if(sizeof($specialAndOffer)>0)
		{
			foreach($specialAndOffer as $RowSpecialAndOffer)
			{
				
				$sp_and_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowSpecialAndOffer->total_free_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowSpecialAndOffer->product_id"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowSpecialAndOffer->catid;
				$insData['product_id'] = $RowSpecialAndOffer->product_id;
				$insData['product_qty'] = $RowSpecialAndOffer->total_free_qty;
				$insData['product_value'] = $RowSpecialAndOffer->free_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);

				/* Spec AND offer update to originial state*/
				DB::table('tbl_order_special_and_free_qty')->where('order_id',$order_id)
											   ->where('product_id',$RowSpecialAndOffer->product_id)
											   ->update(
														[ 
														 'total_free_qty' => 0,
														 'free_value' => 0,														
														 'status' => NULL
														]
												); 
				
			}
			
			
		}	
									
		
		
		/* Special AND Offer Roll Back
		$spcl_and_stk_affcd = DB::update('UPDATE tbl_order_special_and_free_qty spandf JOIN depot_stock stk 
										ON spandf.product_id = stk.product_id 
										SET stk.stock_qty = stk.stock_qty + spandf.free_delivery_qty
										WHERE stk.point_id = ? AND spandf.order_id = ? and spandf.status = 0', 
										["$point_id","$order_id"]);	
										
		DB::table('tbl_order_special_and_free_qty')->where('order_id',$order_id)->delete();		
		
		*/
		
	}
	
	
	private function bundle_offer_rollback($order_id, $point_id)
	{
							/* Bundle Offer Roll Back step by step */
		
		$BundleOffer = DB::select("SELECT bndl.* FROM tbl_order_gift bndl WHERE bndl.orderid = '".$order_id."' 
										AND bndl.status = '1'"); 
									
		if(sizeof($BundleOffer)>0)
		{
			foreach($BundleOffer as $RowBundleOffer)
			{
				
				$regular_stk_affcd = DB::update("UPDATE depot_stock stk  
												SET stk.stock_qty = stk.stock_qty + $RowBundleOffer->free_qty
												WHERE stk.point_id = ?  AND stk.product_id = ?",  
												["$point_id","$RowBundleOffer->proid"]);
												
				
				/* Update Log */	
				$insData = array();
				$insData['reference_id'] = $order_id;
				$insData['point_id'] = $point_id;
				$insData['cat_id'] = $RowBundleOffer->cat_id;
				$insData['product_id'] = $RowBundleOffer->proid;
				$insData['product_qty'] = $RowBundleOffer->free_qty;
				$insData['product_value'] = 0; //$RowBundleOffer->free_delivery_value;
				$insData['inventory_type'] = 1; // stock-in
				$insData['transaction_type'] = 'free_reverse';
				
				$this->write_log($insData);		

				reset($insData);

				DB::table('tbl_order_gift')->where('orderid',$order_id)
										   ->where('og_id',$RowBundleOffer->og_id)
										   ->delete();
				
				/* Bundle offer update to originial state 
				DB::table('tbl_order_gift')->where('order_id',$order_id)
											   ->where('product_id',$RowBundleOffer->product_id)
											   ->update(
														['status' => NULL]
												); 
				*/								
				
			}
			
			
		}	
		
		/* Bundle Offer Roll Back whole at a time
		$bndl_stk_affcd = DB::update('UPDATE tbl_order_gift bf JOIN depot_stock stk  ON bf.proid = stk.product_id 
									SET stk.stock_qty = stk.stock_qty + bf.free_qty
									WHERE stk.point_id = ? AND bf.orderid = ? and bf.status = 1', ["$point_id","$order_id"]);
									
									
		DB::table('tbl_order_gift')->where('orderid',$order_id)->delete();		

		*/		
	
	}
	
	
	private function offer_rollback_whole_at_a_time($order_id, $point_id)
	{
		
									/* Regular Offer Roll Back*/
		$regular_stk_affcd = DB::update('UPDATE tbl_order_free_qty rf JOIN depot_stock stk  ON rf.product_id = stk.product_id 
										SET stk.stock_qty = stk.stock_qty + rf.free_delivery_qty
										WHERE stk.point_id = ? AND rf.order_id = ? and rf.status = 0', 
										["$point_id","$order_id"]);
										
		DB::table('tbl_order_free_qty')->where('order_id',$order_id)->delete();
       							
									
									/* Regular AND Offer Roll Back*/
		$regular_and_stk_affcd = DB::update('UPDATE tbl_order_regular_and_free_qty randf JOIN depot_stock stk  
											ON randf.product_id = stk.product_id 
											SET stk.stock_qty = stk.stock_qty + randf.free_delivery_qty
											WHERE stk.point_id = ? AND randf.order_id = ? and randf.status = 0',
											["$point_id","$order_id"]);
											
		DB::table('tbl_order_regular_and_free_qty')->where('order_id',$order_id)->delete();									
									
									
									/* Special Offer Roll Back*/
		$spcl_stk_affcd = DB::update('UPDATE tbl_order_special_free_qty spf JOIN depot_stock stk  ON spf.product_id = stk.product_id 
									SET stk.stock_qty = stk.stock_qty + spf.free_delivery_qty
									WHERE stk.point_id = ? AND spf.order_id = ? and spf.status = 0', 
									["$point_id","$order_id"]);	
									
		DB::table('tbl_order_special_free_qty')->where('order_id',$order_id)->delete();
        

									/* Special AND Offer Roll Back*/
		$spcl_and_stk_affcd = DB::update('UPDATE tbl_order_special_and_free_qty spandf JOIN depot_stock stk 
										ON spandf.product_id = stk.product_id 
										SET stk.stock_qty = stk.stock_qty + spandf.free_delivery_qty
										WHERE stk.point_id = ? AND spandf.order_id = ? and spandf.status = 0', 
										["$point_id","$order_id"]);	
										
		DB::table('tbl_order_special_and_free_qty')->where('order_id',$order_id)->delete();															

									/* Bundle Offer Roll Back*/
		$bndl_stk_affcd = DB::update('UPDATE tbl_order_gift bf JOIN depot_stock stk  ON bf.proid = stk.product_id 
									SET stk.stock_qty = stk.stock_qty + bf.free_qty
									WHERE stk.point_id = ? AND bf.orderid = ? and bf.status = 1', ["$point_id","$order_id"]);
									
									
		DB::table('tbl_order_gift')->where('orderid',$order_id)->delete();							

		return true;							
	
	
	}
	
	
	
	private function credit_ledger_adjust($order_id)
	{   
        if($order_id)
		{			 
		    $OrderInfo = DB::select("SELECT * FROM tbl_order WHERE order_id = '".$order_id."'");
           
			if(sizeof($OrderInfo)>0)
			{
				 $retailerLedger = DB::select("SELECT * FROM retailer_credit_ledger WHERE retailer_id = '".$OrderInfo[0]->retailer_id."'
                            ORDER BY 1 DESC LIMIT 1");
			
           
                            
				##opening balance
				if(sizeof($retailerLedger)>0)
				{
					$retOpeningBalance = $retailerLedger[0]->retailer_balance;
				} else {
					$retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$OrderInfo[0]->retailer_id."'");
					$retOpeningBalance = $retailerData[0]->opening_balance;
				}
				
				$LedAdjEntry = array();
				
				$LedAdjEntry['retailer_id'] = $OrderInfo[0]->retailer_id;
				$LedAdjEntry['point_id'] = $OrderInfo[0]->point_id;
				
				$LedAdjEntry['collection_id'] = 0;
				$LedAdjEntry['retailer_invoice_no'] = 0;
				$LedAdjEntry['retailer_invoice_sales'] = 0;
				$LedAdjEntry['memo_commission'] = 0;
				$LedAdjEntry['memo_commission_value'] = 0;
				
				$LedAdjEntry['sales_return_order_id'] = $order_id;
				$LedAdjEntry['retailer_sales_return'] = $OrderInfo[0]->grand_total_value;
				
				$LedAdjEntry['trans_type'] = 'return_sales';
				$LedAdjEntry['accounts_type'] = 'expense';
				$LedAdjEntry['credit_ledger_date'] = date('Y-m-d H:i:s');
				
				$LedAdjEntry['retailer_opening_balance'] = $retOpeningBalance;
			   
				
				$retailer_balance = $retOpeningBalance - $OrderInfo[0]->grand_total_value;
				$LedAdjEntry['retailer_balance'] = $retailer_balance;
				
				DB::table('retailer_credit_ledger')->insert([$LedAdjEntry]);
				
				
			} //order found
			
			return true;	
			
		 } // order id	
		 
		return false; 
      
	}
	
	private function order_ready_for_delivery($order_id)
	{
		if($order_id)
		{
			$ord_mstr_affcd = DB::update("UPDATE tbl_order SET order_type= 'Confirmed',  total_delivery_qty = 0, total_delivery_value = 0,
									  grand_total_value = 0,
									  update_date = '', is_reverse = 'YES', reverse_count = reverse_count + 1
									  Where order_id = ?", ["$order_id"]);
		
			$ord_det_affcd = DB::update("UPDATE tbl_order_details SET order_det_type = 'Confirmed',  
									delivered_qty = NULL, delivered_value = NULL, replace_delivered_qty = NULL,
									delivery_challan = '', delivered_date = '', is_delivered = 'NO' 
									Where order_id = ?", ["$order_id"]);
		  return true;
		
		} else { 
		
			return false;
		
		}
	}
	
	
}
