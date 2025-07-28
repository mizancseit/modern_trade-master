<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class SystemController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 18/10/2018
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_all_order_manage()
    {
        $selectedMenu   = 'All Order Manage';             // Required Variable
        $pageTitle      = 'All Order Manage';            // Page Slug Title
      
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*', 'tbl_order_details.*', DB::raw('SUM(tbl_order_details.order_qty) AS total_qty'),
                        DB::raw('SUM(tbl_order_details.p_grand_total) AS total_value'), 'tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name',
                        'tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order_details.order_det_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_order.order_id')                    
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();        

        return view('sales/systemAdmin/orderManage', compact('selectedMenu','pageTitle','resultOrderList'));
    }


    public function ssg_all_invoice_details_order($orderMainId,$foMainId)
    {
        $selectedMenu   = 'All Order Manage';                      // Required Variable
        $pageTitle      = 'Invoice Details';                  // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->leftJoin('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        //->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($resultBundleOfferType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                //->whereNotNull('og.orderid')
                                ->where('og.orderid', $orderMainId)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)
                                //->where('og.orderid', $orderMainId)                              
                                ->first();
        }

        $reultProRate = '';

        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)                           
                        ->get();

        return view('sales/systemAdmin/invoiceDetails', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift','reultProRate','commissionWiseItem','specialValueWise'));
    }

    public function ssg_all_delete_order(Request $request)
    {

        $orderid        = $request->get('orderid');        

        $lastOrderId = DB::table('tbl_order')->select('order_type','fo_id','order_id','order_no')
                    ->where('order_id',$orderid)->first();

        DB::table('tbl_order_details')->where('order_id', $orderid)->delete();
        
        DB::table('ims_tbl_visit_order')->where('order_no', $lastOrderId->order_no)->delete();
      
        DB::table('tbl_order')->where('order_id', $orderid)->delete();

        // All free QTY Delete

        DB::table('tbl_special_commission')->where('order_id', $orderid)->delete();
        DB::table('tbl_order_gift')->where('orderid', $orderid)->delete();

        DB::table('tbl_order_regular_and_free_qty')->where('order_id', $orderid)->delete();
        DB::table('tbl_order_special_and_free_qty')->where('order_id', $orderid)->delete();
        DB::table('tbl_order_special_free_qty')->where('order_id', $orderid)->delete();
        
        return Redirect::back()->with('success','Successfully invoice delete');     
    }    
}
