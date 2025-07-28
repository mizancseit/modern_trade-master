<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class DistributorController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 24/12/2017
    * aa
    **/ 

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_order()
    {
        $selectedMenu   = 'Order';             // Required Variable
        $pageTitle      = 'Order';            // Page Slug Title

		
		$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')
									");
									
		$resultFO = DB::select("SELECT uds.* FROM users u JOIN tbl_user_business_scope ubs ON u.id = ubs.user_id 
								JOIN tbl_user_details uds ON uds.user_id = u.id 
								where ubs.point_id = '".$depotList[0]->point_id."' and u.user_type_id = 12");							
									
							
		
		/*
        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                       
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
		*/				

        $todate          = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
						->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
		
		//$resultFO = array();				

        return view('sales/distributor/order', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
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
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales/distributor/ordersList', compact('resultOrderList'));
    }


    public function ssg_order_edit($orderMainId,$foMainId,$orderPart)
    {
        //dd($orderMainId,$foMainId,$orderPart);

        $selectedMenu   = 'Order';                    // Required Variable
        $pageTitle      = 'Order Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname')
                        
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPart)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        //dd($resultCartPro);

         $orderTypePartial  = DB::table('tbl_order_details')
                        ->select('order_det_status','partial_order_id')                        
                        ->where('order_id',$orderMainId)
                        ->where('partial_order_id',$orderPart)                                           
                        ->first();

        //dd($orderTypePartial);

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.offer_type','tbl_order.order_status','users.id','users.display_name','tbl_order.global_company_id','tbl_order.order_id','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.point_id','tbl_order.route_id','tbl_order.order_no','tbl_order.order_date','tbl_order.grand_total_value','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')

        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
        ->where('tbl_order.order_type','Confirmed')
        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
        ->where('tbl_order.fo_id',$foMainId)                        
        ->where('tbl_order.order_id',$orderMainId)
        ->first();

        //dd($resultInvoice);


        // for offers

        // for FO Information

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        //dd($resultFoInfo);

        //$point_id       = $resultFoInfo->point_id;
        $division_id    = $resultFoInfo->division_id;

        $currentDay = date('Y-m-d');
		
						/************************* Start Bundle Offer ********************************/				
									
       
		
		// for offer related start

        $resultBundleOffers = DB::table('tbl_bundle_offer')
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->where('iBusinessType', Auth::user()->business_type_id)
                            ->where('iOfferType', 3)
                            ->orderBy('iId', 'DESC')
                            ->first();

        //dd($resultBundleOffers);

        $resultBundleOffersCategories = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_bundle_category.offerId','tbl_bundle_category.categoryId')
                            ->leftJoin('tbl_bundle_category','tbl_bundle_offer.iId','=','tbl_bundle_category.offerId')
                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                            ->get();

        $allowCategory = array();
        foreach ($resultBundleOffersCategories as $value) 
        {
            $allowCategory[] = $value->categoryId;
        }


        $resultBundleOffersGift = DB::table('tbl_order_gift')
                                //->where('retailer_id', $retailderid)
                                ->where('retailer_id', $resultInvoice->retailer_id)
                                ->where('global_company_id', Auth::user()->global_company_id)
                                //->where('distributor_id',Auth::user()->id)
                                ->where('orderid',$resultInvoice->order_id)
                                ->first();

        $offerid = '';
        if(sizeof($resultBundleOffersGift)>0)
        {
            $offerid = $resultBundleOffersGift->offerid;
        }

        $resultBundleOfferType = DB::table('tbl_bundle_products')->select('offerId','productType')
                                ->where('offerId', $offerid)                                
                                ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.offerId', $offerid)
                                //->where('og.retailer_id', $retailderid)
                                ->where('og.retailer_id', $resultInvoice->retailer_id)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                //->where('og.distributor_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            // $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
            //                     ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.offerid','og.proid','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')
                               
            //                     ->leftJoin('tbl_product','og.proid','=','tbl_product.id')

            //                     ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.giftName')

            //                     ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
            //                     ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

            //                     ->where('og.offerId', $offerid)
            //                     //->where('og.retailer_id', $retailderid)
            //                     ->where('og.retailer_id', $resultInvoice->retailer_id)
            //                     ->where('og.global_company_id', Auth::user()->global_company_id)
            //                     //->where('og.distributor_id',Auth::user()->id)
            //                     ->where('og.orderid',$resultInvoice->order_id)
            //                     ->first();

            $resultBundleSelectedGift = DB::table('tbl_order_gift')
                                    ->where('orderid',$resultInvoice->order_id)
                                    ->first();                                    

            $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->select('tbl_bundle_product_details.*','tbl_product.id','tbl_product.name','tbl_product.depo')
                                    ->leftJoin('tbl_product','tbl_bundle_product_details.giftName','=','tbl_product.id')
                                    ->where('offerId',$resultBundleSelectedGift->offerid)
                                    ->where('slabId',$resultBundleSelectedGift->slab_id)
                                    ->where('groupid',$resultBundleSelectedGift->groupid)
                                    ->get();


        }
		
		
        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->groupBy('order_id')                              
                        ->first();

        /////////////////// IF OFFER CHECKED ANY /////////////////////

        $regularOffersCheck = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $specialOffersCheck = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $bundleOffersCheck = DB::table('tbl_order_gift')
                        ->select('orderid')
                        ->where('orderid', $orderMainId)
                        ->groupBy('orderid')                              
                        ->count();


        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.free_id','osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)   
                        ->where('is_point_wise', 0)                       
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)   
                        ->where('is_point_wise', 1)                       
                        ->get();
		////////// Offer check /////////////

        $checkExclusiveAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->whereNull('status')  //added items
                    ->count();

        $checkExclusiveAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->where('status', 0)  //added items
                    ->count();


        $checkRegularAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->whereNull('status')  //added items
                    ->count();

        $checkRegularAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->where('status', 0)  //added items
                    ->count();


        $today = date('Y-m-d');            
        $resultBundle = DB::table('tbl_bundle_offer')
                        ->select('iId','vOfferName','dBeginDate','dEndDate')
                        ->where('iOfferType',3)
                        ->whereDate('dEndDate','>=',$today)
                        ->first();
                                    
        $offerId = '';
        if(sizeof($resultBundle)>0)
        {
            $offerId = $resultBundle->iId;
        }				
		

        $netAmount = $resultInvoice->grand_total_value + 0;       

       /// Exceptional products group

     $bundleCategoriesEx  = DB::table('tbl_bundle_except_category')
            ->select('categoryId')
            ->where('status',0)
            ->where('global_company_id',Auth::user()->global_company_id)
            ->get();

            $data = collect($bundleCategoriesEx)->map(function($x){ return (array) $x; })->toArray();


        $checkDelivery = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
        ->where('order_id', $orderMainId) 
        ->first();
        

        if(sizeof($checkDelivery)>0) // Delivery Value Available
        {
            $resultBundleVlue  = DB::table('tbl_order_details')
                ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
                ->where('order_id', $orderMainId)  
                ->whereNotIn('cat_id', $data) 
                ->first();
        }
        else
        {
            $resultBundleVlue  = DB::table('tbl_order_details')
                ->select('order_id','cat_id',DB::raw('SUM(p_total_price) AS totalValue'))
                ->where('order_id', $orderMainId)  
                ->whereNotIn('cat_id', $data) 
                ->first();
        }
        
        //dd($resultBundleVlue);

        $netBundleValue = 0;
        if(sizeof($resultBundleVlue)>0)
        {
            $netBundleValue = $resultBundleVlue->totalValue;
        }

        //dd($netBundleValue);         

        $resultOfferRange = DB::select("SELECT * FROM tbl_bundle_slab WHERE iOfferId='$offerId' AND '$netBundleValue' BETWEEN iMinRange AND iMaxRange");

        $offerRangeId=''; 
        if(sizeof($resultOfferRange)>0)
        {
            $offerRangeId = $resultOfferRange[0]->iId;
        }

        
/// end Exceptional products group

        /* $resultOfferRange = DB::select("SELECT * FROM tbl_bundle_slab WHERE iOfferId='$offerId' AND '$netAmount' BETWEEN iMinRange AND iMaxRange");

        $offerRangeId=''; 
        if(sizeof($resultOfferRange)>0)
        {
            $offerRangeId = $resultOfferRange[0]->iId;
        }*/

        $resultBundleAvailableItems = DB::table('tbl_bundle_product_details')
                        ->where('offerId', $offerId) 
                        ->where('slabId', $offerRangeId) 
                        ->count();

        $resultBundleAddedItems = DB::table('tbl_order_gift')
                        ->where('orderid', $resultInvoice->order_id)
                        ->count();
        
		


        return view('sales/distributor/bucketEdit', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffers','resultBundleOffersGift','specialOffers','regularOffersCheck','specialOffersCheck','bundleOffersCheck','commissionWiseItem','specialValueWise','orderTypePartial','pointValueWise','checkExclusiveAvailableItems','checkExclusiveAdded','checkRegularAvailableItems','checkRegularAdded','resultBundleAvailableItems','resultBundleAddedItems'));
    }


	public function ssg_product_price($product_id)
	{
			$prodPrice = DB::select("SELECT depo FROM tbl_product where id = '".$product_id."'");
			
			if(sizeof($prodPrice)>0)
			{
				return $prodPrice[0]->depo;
			} else {
				
				return 0;
			}
			
	
	}
    
    public function ssg_order_edit_submit(Request $request)
    { 
        //dd($request->all());

        if($request->get('statusProcess')==1)  // confirm order
        {
            $checkDeliveryStatus = DB::table('tbl_order_details')
            ->where('order_id', $request->get('orderid'))
            ->where('partial_order_id', $request->get('openOrderType'))
            ->where('order_det_type', 'Delivered')
            ->count();

            //dd($checkDeliveryStatus);

            if($checkDeliveryStatus==0)
            {

                $lastOrderId  = $request->get('orderid');
                $countRows    = count($request->get('qty'));

                // NEW OPTION VALUE WISE COMMISSION            

                $mTotalPrice=0;
                $mTotalQty=0;

                for($m=0;$m<$countRows;$m++)
                {
                    if($request->get('qty')[$m]!='')
                    {
                        $mTotalPrice += $request->get('qty')[$m] * $request->get('price')[$m];
                        $mTotalQty += $request->get('qty')[$m];
                    }
                }

                if($request->get('orderTypePartial')=='Closed')
                {
                    $previousBalanceRetailer = DB::table('tbl_retailer')
                                ->select('reminding_commission_balance')
                                ->where('retailer_id', $request->get('retailderid'))
                                ->first();

                    $previousBalance = 0;
                    if(sizeof($previousBalanceRetailer)>0)
                    {
                        $previousBalance = $previousBalanceRetailer->reminding_commission_balance;
                    }

                    $currentBalanceRetailer = $previousBalance + $request->get('totalFreeValueWiseCommissionBalance');             

                    $buyAmount = $request->get('totalFreeValue') - $request->get('totalFreeValueWiseCommissionBalance');

                    DB::table('tbl_retailer')->where('retailer_id', $request->get('retailderid'))->update(
                        [
                            'reminding_commission_balance' => $currentBalanceRetailer
                        ]
                    );

                    DB::table('tbl_retailer_commission_history')->insert(
                        [
                            'orderid'                   => $lastOrderId,
                            'retailerid'                => $request->get('retailderid'),
                            'distributorid'             => Auth::user()->id,
                            'foid'                      => $request->get('foMainId'),
                            'retailer_order_balance'    => $mTotalPrice,
                            'total_commission_amount'   => $request->get('totalFreeValue'),
                            'total_buy_commission_amount' => $buyAmount,
                            'balance_amount'            => $request->get('totalFreeValueWiseCommissionBalance'), 
                            'routeid'                   => $request->get('routeid'), 
                            'pointid'                   => $request->get('pointid'), 
                            'date'                      => date('Y-m-d')
                        ]
                    ); 
                }          


                if($request->get('orderTypePartial')=='Closed')
                {
                    $order_type = 'Delivered';
                }
                else
                {
                    $order_type = 'Confirmed';
                }

                DB::table('tbl_order')->where('order_id', $request->get('orderid'))

                    ->where('global_company_id', Auth::user()->global_company_id)->update(
                    [
                        'order_type'             => $order_type,
                        'total_delivery_qty'     => $mTotalQty,   
                        'total_delivery_value'   => $mTotalPrice, 
                        'grand_total_value'      => $mTotalPrice,  
                        'update_date'            => date('Y-m-d H:i:s')
                    ]
                );
                
                $srl_no = explode('_',$request->get('openOrderType'));
                //$chalan_no = 'CHN - ' . date('Ymd') . $srl_no[1];
                $autoAdd   = $request->get('orderid');
                $chalan_no = 'CHN-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;

                for($m=0;$m<$countRows;$m++)
                {
                    if($request->get('qty')[$m]!='' OR $request->get('replaceDelivery')[$m]!='')  /* bug resolve by zubair Dec_08_2018 */	
                    {
                        $checkItemsExiting = DB::table('tbl_order_details')
                                        ->where('order_id', $lastOrderId)
                                        ->where('product_id',$request->get('product_id')[$m])
                                        ->where('partial_order_id',$request->get('openOrderType'))
                                        ->first();

                        if(sizeof($checkItemsExiting)>0)
                        {
                            $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                            DB::table('tbl_order_details')
                            ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                            ->where('product_id',$request->get('product_id')[$m])
                            ->where('partial_order_id',$request->get('openOrderType'))
                            ->update(
                                [
                                    'replace_delivered_qty'   => $request->get('replaceDelivery')[$m],
                                    'delivered_qty'           => $request->get('qty')[$m],
                                    'delivered_value'         => $totalPrice,
                                    'is_delivered'            => 'YES',
                                    'delivered_date'          => date('Y-m-d H:i:s'),
                                    'delivery_challan'        => $chalan_no,
                                    'order_det_type'          => 'Delivered'
                                ]
                            );
                        } 
                    }
                } 

                $countFreeRows    = count($request->get('free_id'));
                $andCountFreeRows = count($request->get('and_free_id'));

                //  Sharif offer edit option
                if($request->get('offer_type') == 'exclusive')
                {
                    for($i=0;$i<$countFreeRows;$i++)
                    {
                        if($request->get('free_id')[$i]!='')
                        {
                          $totalFreePrice = $request->get('freeqty')[$i] * $request->get('free_value')[$i];
                          
						  //dd($request->get('total_free_value')[$i]);

                            DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                            ->where('free_id',$request->get('free_id')[$i])
                            ->where('product_id',$request->get('free_product_id')[$i])
                            ->update(
                                [
                                    'total_free_qty'              => $request->get('freeqty')[$i],
                                    'free_value'                  => $totalFreePrice,
                                    'free_delivery_qty'           => $request->get('total_free_qty')[$i],
                                    'free_delivery_value'         => ($request->get('total_free_qty')[$i] * $this->ssg_product_price($request->get('free_product_id')[$i])),
                                ]
                            );
                        }
                    }
                    // Special free and products
                    if($andCountFreeRows>0){
                        for($j=0;$j<$andCountFreeRows;$j++)
                        {
                            if($request->get('and_free_id')[$j]!='')
                            {
                             $totalFreePrice = $request->get('andFreeQty')[$j] * $request->get('and_free_value')[$j];
                              

                                DB::table('tbl_order_special_and_free_qty')
                                ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                                ->where('free_id',$request->get('and_free_id')[$j])
                                ->where('product_id',$request->get('and_free_pid')[$j])
                                ->update(
                                    [
                                        'total_free_qty'              => $request->get('andFreeQty')[$j],
                                        'free_value'                  => $totalFreePrice,
                                        'free_delivery_qty'           => $request->get('and_free_qty')[$j],
                                        'free_delivery_value'         => ($request->get('and_free_qty')[$j] * $this->ssg_product_price($request->get('and_free_pid')[$j]))
                                    ]
                                );
                            }
                        }
                    }
                }

                if($request->get('offer_type') == 'regular')
                {
                    for($i=0;$i<$countFreeRows;$i++)
                    {
                        if($request->get('free_id')[$i]!='')
                        {
                          $totalFreePrice = $request->get('freeqty')[$i] * $request->get('free_value')[$i];
                        
                          
                            DB::table('tbl_order_free_qty')
                            ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                            ->where('free_id',$request->get('free_id')[$i])
                            ->where('product_id',$request->get('free_product_id')[$i])
                            ->update(
                                [
                                    'total_free_qty'              => $request->get('freeqty')[$i],
                                    'free_value'                  => $totalFreePrice,
                                    'free_delivery_qty'           => $request->get('total_free_qty')[$i],
                                    'free_delivery_value'         => $request->get('free_value')[$i]
                                ]
                            );
                        }
                    }

                    // Regular free and products
                    if($andCountFreeRows>0){
                        for($j=0;$j<$andCountFreeRows;$j++)
                        {
                            if($request->get('and_free_id')[$j]!='')
                            {
                             $totalFreePrice = $request->get('andFreeQty')[$j] * $request->get('and_free_value')[$j];
                             

                                DB::table('tbl_order_regular_and_free_qty')
                                ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                                ->where('free_id',$request->get('and_free_id')[$j])
                                ->where('product_id',$request->get('and_free_pid')[$j])
                                ->update(
                                    [
                                        'total_free_qty'              => $request->get('andFreeQty')[$j],
                                        'free_value'                  => $totalFreePrice,
                                        'free_delivery_qty'           => $request->get('and_free_qty')[$j],
                                        'free_delivery_value'         => $request->get('and_free_value')[$j]
                                    ]
                                );
                            }
                        }
                    }
                }
            
                //zubair retailer balance call June-11-2018
                $orderData = DB::table('tbl_order')
                        ->select('tbl_order.*')
                        ->where('order_id', $request->get('orderid'))
                        ->first();
        
                if(sizeof($orderData)>0)
                {
                    $retailer_info = array();
                    $retailer_info['trans_type'] = 'sales'; 
                    $retailer_info['accounts_type'] = 'expense';
                    $retailer_info['retailer_id'] = $orderData->retailer_id;
                    $retailer_info['invoice_no'] = $orderData->order_no;
                    $retailer_info['point_id'] = $orderData->point_id;
					
					$retailer_info['order_id'] = $orderData->order_id;
					$retailer_info['partial_type'] = $request->get('openOrderType');
                   
					$this->reatiler_credit_ledger($retailer_info);
                }   

                $this->sync_offer_stock($request->get('orderid'), 'Delivered', $request->get('openOrderType'));
        
                if($request->get('offer_type') == 'exclusive')
                {
                    DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
                     DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
    				// clear regular  offer 	
                    DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
                    DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
    				 // clear bundle  offer 	
    			   DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
                    
                    $this->sync_offer_stock($request->get('orderid'),$request->get('offer_type'));
                }

                if($request->get('offer_type') == 'regular')
                {
                    DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
                     DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
    				// clear special  offer 
                    DB::table('tbl_order_special_free_qty')->where('status','!=',3)->where('order_id',$request->get('orderid'))->delete();
                    DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
    				 // clear bundle  offer 	
    			   DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
                    
                    $this->sync_offer_stock($request->get('orderid'), $request->get('offer_type'));
                }
                
            
                if($request->get('offer_type') != '' AND $request->get('offer_type') != 'regular' 
                    AND $request->get('offer_type') != 'exclusive')
                {

                  // clear regular offer
                    DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
                    DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                  // clear special offer    
                    DB::table('tbl_order_special_free_qty')->where('status','!=',3)->where('order_id',$request->get('orderid'))->delete();
    			  // clear specialand  offer 	
    			   DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                    
                    $this->sync_offer_stock($request->get('orderid'), 'bundle');
                }

                if($request->get('offer_type') == 'exclusive')
                {
                    DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->where('status',3)->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
                }

                return Redirect::to('/order')->with('success', 'Successfully Confirm Delivery Done.');
            }
            else
            {
                return Redirect::to('/order')->with('unsuccess', 'Sorry, this order already delivered.');
            }
        }
        else // Update Query Only.
        {

        $lastOrderId    = $request->get('orderid');

        $countRows = count($request->get('qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $mTotalPrice += $request->get('qty')[$m] * $request->get('price')[$m];
                $mTotalQty += $request->get('qty')[$m];
            }
        }            

       DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', $request->get('foMainId'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_type'             => 'Confirmed',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'grand_total_value'      => $mTotalPrice,
                'update_date'            => date('Y-m-d H:i:s')
            ]
        );

        
        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('tbl_order_details')
                                ->where('order_id', $lastOrderId)
                                ->where('product_id',$request->get('product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('tbl_order_details')
                    ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                    ->where('product_id',$request->get('product_id')[$m])
                    ->update(
                        [
                            'replace_delivered_qty'   => $request->get('replaceDelivery')[$m],
                            'delivered_qty'           => $request->get('qty')[$m],
                            'delivered_value'         => $totalPrice
                        ]
                    );
                } 

            }

        }

        //Sharif Offer Starts----------------->//
        //dd($request->get('offer_type'));

        

                $itemsDelete = DB::table('tbl_order_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();

                $order_sku = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','product_id')
                                    ->where('order_id', $lastOrderId)
                                    ->get();


                $special_sku = array();
                foreach($order_sku as $order_sku_id) {
                    $special_sku[]= $order_sku_id->product_id;
                }

                $checkRegularSkuProducts =  DB::table('tbl_regular_sku_products')
                                        ->select('slab','catid','sku_id','qty','value')
                                        ->whereIn('sku_id', $special_sku)
                                        ->groupBy('sku_id')
                                        ->get();
               

                $skuid = array();

                foreach($checkRegularSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }


                $offerGroupType = DB::table('tbl_order_details')
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('cat_id')
                                    ->get();


                $textGroup =0;
                if(sizeof($offerGroupType)>0)
                {
                    foreach ($offerGroupType as $value) 
                    {
                        if($value->offer_group_type==1)
                        {
                            // separate

                            $totalCatQty = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereNotIn('product_id', $skuid)
                                    ->where('cat_id', $value->cat_id)
                                    //->groupBy('cat_id')
                                    ->get();



                            $textGroup1 = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','offer_group_type')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('offer_group_type')
                                    ->first();

                            $textGroup = $textGroup1->offer_group_type;
                        }
                        else
                        {
                            //combine

                            $totalCatQty = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('cat_id')
                                    ->get();



                        }

                        foreach ($totalCatQty as $catQty) 
                        {
                           
                            $totalQty = $catQty->total;
                            $catid = $catQty->cat_id;
                            $orderNo = $order_no->order_no;
                            $autoOrderId = $order_no->auto_order_no;
                            $distributorID = $order_no->distributor_id;
                            $foID = $order_no->fo_id;
                            $pointID = $order_no->point_id;
                            $routeID = $order_no->route_id;
                            $retailderID = $order_no->retailer_id;
                            $total_odd=0;
                            $total_odd1=0;
                            $total_odd2 = 0;                    

                            $regularProducts =  DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab',$totalQty)
                                               ->where('status',0)
                                               ->get();

                 
                            if(sizeof($regularProducts) >0 )
                            {
                        
                                foreach($regularProducts as $regularProducts) {
                                
                                    $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularProducts->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularProducts->pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularProducts->qty,
                                            'free_value'           => $regularProducts->value,
                                            'total_free_value'     => $regularProducts->value * $regularProducts->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                    
                                    if($regularProducts->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularProducts->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularProducts->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularProducts->and_qty,
                                            'free_value'            => $regularProducts->and_value,
                                            'total_free_value'      => $regularProducts->value * $regularProducts->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                    }
                                
                                }
                            }
                            else
                            {
                                $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<$totalQty");


                                $maxSlab = DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab', $maxValue[0]->slab)
                                               ->where('status',0)
                                               ->get();

                                

                                 //dd($maxSlab);

                                if(sizeof($maxSlab) >0 )
                                {
                                    foreach($maxSlab as $maxSlab) {
                                    
                                        $mainQty = (int)($totalQty/$maxSlab->slab);
                                       //dd($mainQty);
                                        $total_odd = $totalQty - ($mainQty * $maxSlab->slab);
                                        $total_free = $maxSlab->qty * $mainQty;
                                        $total_value = $maxSlab->value * $total_free;
                                        $and_total_free = $maxSlab->and_qty * $mainQty;
                                        $and_total_value = $maxSlab->and_value * $and_total_free;
                                    

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $maxSlab->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $catid,
                                                'product_id'            => $maxSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $maxSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );
                                        
                                        if($maxSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'product_id'            => $maxSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $maxSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                                $lastSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd)");

                                
                                
                                if(sizeof($lastSlab) >0 )
                                {
                                     foreach($lastSlab as $lastSlab) {

                                        $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                        $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                        //dd($total_odd1);
                                        $total_free = $lastSlab->qty * $mainQty1;
                                        $total_value = $lastSlab->value * $total_free;
                                        $and_total_free = $lastSlab->and_qty * $mainQty1;
                                        $and_total_value = $lastSlab->and_value * $and_total_free;

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastSlab->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                               $lastOddSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd1)");
                                
                                if(sizeof($lastOddSlab) >0 )
                                {
                                  
                                   foreach($lastOddSlab as $lastOddSlab) {

                                        $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                        $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                        $total_free = $lastOddSlab->qty * $mainQty2;
                                        $total_value = $lastOddSlab->value * $total_free;
                                        $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;


                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastOddSlab->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }
                                
                                
                                $lastOddSlab2 =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab2) >0 )
                                {
                                  
                                   foreach($lastOddSlab2 as $lastOddSlab2) {

                                        $mainQty3 = (int)($total_odd1/$lastOddSlab2->slab);
                                        $total_free = $lastOddSlab2->qty * $mainQty3;
                                        $total_value = $lastOddSlab2->value * $total_free;
                                        $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab2->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab2->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab2->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab2->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastOddSlab2->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }
                            }            
                 
                            //Sharif Offer Ends-------------------->//  
                        }

                        if($textGroup==1) 
                        {     

                            foreach($checkRegularSkuProducts as $sku) {
                            
                                $totalSkuQty =  DB::table('tbl_order_details')
                                                ->where('order_id',$lastOrderId)
                                                ->where('product_id', $sku->sku_id)
                                                ->sum('delivered_qty');
                                                
                                $regularSku =  DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                ->where('sku_id',$sku->sku_id)
                                ->where('slab',$totalSkuQty)
                                ->where('status',0)                       
                                ->get();


                                if(sizeof($regularSku) >0 )
                                {

                                    foreach($regularSku as $regularSku) {
                                    
                                         $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularSku->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularSku->pid,
                                            'sku_id'                => $regularSku->sku_id,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularSku->qty,
                                            'free_value'            => $regularSku->value,
                                            'total_free_value'      => $regularSku->value * $regularSku->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        if($regularSku->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularSku->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'sku_id'                => $regularSku->sku_id,
                                            'product_id'            => $regularSku->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularSku->and_qty,
                                            'free_value'            => $regularSku->and_value,
                                            'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }


                                    }


                                }
                                else
                                {
                                    $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                                    $maxSlabSku = DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                                   ->where('catid',$catid)
                                                   ->where('sku_id',$sku->sku_id)
                                                   ->where('slab', $maxValueSku[0]->slab)
                                                   ->where('status',0)                       
                                                   ->get();

                                    if(sizeof($maxSlabSku) >0 )
                                    {

                                        foreach($maxSlabSku as $maxSlabSku) {

                                            $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                                           //dd($mainQty);
                                            $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                            $total_free = $maxSlabSku->qty * $mainQty;
                                            $total_value = $maxSlabSku->value * $total_free;
                                            $and_total_free = $maxSlabSku->and_qty * $mainQty;
                                            $and_total_value = $maxSlabSku->and_value * $and_total_free;
                                        

                                           $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $maxSlabSku->pid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $maxSlabSku->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($maxSlabSku->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $regularSku->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $catid,
                                                'sku_id'                => $regularSku->sku_id,
                                                'product_id'            => $regularSku->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $regularSku->and_qty,
                                                'free_value'            => $regularSku->and_value,
                                                'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }

                                        }
                                      
                                    }

                                    $lastSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                                    if(sizeof($lastSlabSku) >0 )
                                    {

                                         foreach($lastSlabSku as $lastSlab) {

                                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                            $total_free = $lastSlab->qty * $mainQty1;
                                            $total_value = $lastSlab->value * $total_free;

                                           $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastSlab->pid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );


                                            if($lastSlab->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'sku_id'                => $lastSlab->sku_id,
                                                'product_id'            => $lastSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $lastSlab->and_qty,
                                                'free_value'            => $lastSlab->and_value,
                                                'total_free_value'      => $lastSlab->and_qty * $lastSlab->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }

                                   $lastOddSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                                    if(sizeof($lastOddSlabSku) >0 )
                                    {
                                      
                                       foreach($lastOddSlabSku as $lastOddSlab) {

                                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                            $total_free = $lastOddSlab->qty * $mainQty2;
                                            $total_value = $lastOddSlab->value * $total_free;

                                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastOddSlab->pid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastOddSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastOddSlab->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'sku_id'                => $lastOddSlab->sku_id,
                                                'product_id'            => $lastOddSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $lastOddSlab->and_qty,
                                                'free_value'            => $lastOddSlab->and_value,
                                                'total_free_value'      => $lastOddSlab->and_qty * $lastOddSlab->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }
                                } // SKU else close
                            }


                              ///////////////// SPECIAL OFFER START //////////////////
                        }

                    }                    
                }                  
                                   

                
            // for FO Information

            $resultFoInfo   = DB::table('users')
                            ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                             ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                             ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                             ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                             ->where('tbl_user_type.user_type_id', 5)
                             ->where('users.id', Auth::user()->id)
                             ->where('users.is_active', 0) // 0 for active
                             ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                             ->first();

            //dd($resultFoInfo);
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
            $resultSpecialOffers = 0;
            $point = 0;
            $route = 0;

            $currentDay = date('Y-m-d');

            $resultSpecialOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='2'
             AND tbl_bundle_offer_scope.iDivId='$division_id' 
             AND '$currentDay' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultSpecialOffers)>0)
            {           

                $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                     AND iPointId='$point_id'
                     "); 


                $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                         AND iPointId='".$request->get('point_id')."' AND iRouteId = '".$request->get('route_id')."'
                         ");
            }

            if(sizeof($resultSpecialOffers)>0 || sizeof($point)>0 || sizeof($route)>0)
            {


                $specialDelete = DB::table('tbl_order_special_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $andspecialDelete = DB::table('tbl_order_special_and_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $commissionDelete = DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();

                $orderNo = $order_no->order_no;
                $autoOrderId = $order_no->auto_order_no;
                $distributorID = $order_no->distributor_id;
                $foID = $order_no->fo_id;
                $pointID = $order_no->point_id;
                $routeID = $order_no->route_id;
                $retailderID = $order_no->retailer_id;

                
                $skuid = array();
                $checkSkuProducts =  DB::table('tbl_special_sku_products')
                                            ->select('slab','catid','sku_id','qty','value')
                                            ->whereIn('sku_id', $special_sku)
                                            ->where('status',0) 
                                            ->groupBy('sku_id')
                                            ->get();
                

                foreach($checkSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }

                /*$totalQtyCat =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->whereNotIn('product_id', $skuid)
                                ->sum('order_qty');*/

                // $totalCat = DB::table('tbl_order_details')
                //                     ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                //                     ->where('order_id', $lastOrderId)
                //                     ->whereNotIn('product_id', $skuid)
                //                     ->groupBy('cat_id')
                //                     ->get();

                $offerGroupType = DB::table('tbl_order_details')
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('cat_id')
                                    ->get();

                $textGroup =0;
                if(sizeof($offerGroupType)>0)
                {
                    foreach ($offerGroupType as $value) 
                    {
                        if($value->offer_group_type==1)
                        {
                            // separate

                            $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereNotIn('product_id', $skuid)
                                    ->where('cat_id', $value->cat_id)
                                    //->groupBy('cat_id')
                                    ->get();



                            $textGroup1 = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','offer_group_type')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('offer_group_type')
                                    ->first();

                            $textGroup = $textGroup1->offer_group_type;
                        }
                        else
                        {
                            //combine

                            $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->get();

                        }


                        foreach ($totalCat as $catQty) 
                        {
                           
                            $totalQtyCat = $catQty->total;
                            $catid = $catQty->cat_id;
                            $total_odd=0;
                            $total_odd1=0;



                            $specialProducts =  DB::table('tbl_special_offer_product')->select('slab','catid', 'offerGroupId', 'pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                            ->where('catid',$catid)
                            ->where('slab',$totalQtyCat)
                            ->where('status',0)                       
                            ->get();

                           

                            if(sizeof($specialProducts) >0 )
                            {


                                foreach($specialProducts as $specialProducts) {

                                    $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialProducts->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $specialProducts->catid,
                                        'product_id'            => $specialProducts->pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialProducts->qty,
                                        'free_value'            => $specialProducts->value,
                                        'total_free_value'      => $specialProducts->value * $specialProducts->qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );

                                    if($specialProducts->and_qty>0){

                                      DB::table('tbl_order_special_and_free_qty')->insert(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $special_cat_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialProducts->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $specialProducts->catid,
                                        'product_id'            => $specialProducts->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialProducts->and_qty,
                                        'free_value'            => $specialProducts->and_value,
                                        'total_free_value'      => $specialProducts->and_value * $specialProducts->and_qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }
                            else
                            {

                                $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<$totalQtyCat AND status=0");
 

                                $maxSlab = DB::table('tbl_special_offer_product')->select('slab','catid','pid','offerGroupId','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab', $maxValue[0]->slab)
                                               ->where('status',0)                       
                                               ->get();
                                //dd($maxSlab);

                                if(sizeof($maxSlab) >0 )
                                {

                                    foreach($maxSlab as $maxSlab) {

                                        $mainQty = (int)($totalQtyCat/$maxSlab->slab);
                                       //dd($mainQty);
                                        $total_odd = $totalQtyCat - ($mainQty * $maxSlab->slab);
                                        $total_free = $maxSlab->qty * $mainQty;
                                        $total_value = $maxSlab->value * $total_free;
                                        $and_total_free = $maxSlab->and_qty * $mainQty;
                                        $and_total_value = $maxSlab->and_value * $and_total_free;

                                    

                                        $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $maxSlab->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $maxSlab->catid,
                                                'product_id'            => $maxSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $maxSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($maxSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $maxSlab->catid,
                                            'product_id'            => $maxSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $maxSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }

                                    }
                                  
                                }

                                $lastSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd  AND status=0)  AND status=0");


                                if(sizeof($lastSlab) >0 )
                                {

                                     foreach($lastSlab as $lastSlab) {

                                        $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                        $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                        //dd($total_odd1);
                                        $total_free = $lastSlab->qty * $mainQty1;
                                        $total_value = $lastSlab->value * $total_free;
                                        $and_total_free = $lastSlab->and_qty * $mainQty1;
                                        $and_total_value = $lastSlab->and_value * $and_total_free;

                                        $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $lastSlab->catid,
                                                'product_id'            => $lastSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $lastSlab->catid,
                                            'product_id'            => $lastSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                               $lastOddSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd1 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab) >0)
                                {
                                  
                                   foreach($lastOddSlab as $lastOddSlab) {

                                        $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                        $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                        $total_free = $lastOddSlab->qty * $mainQty2;
                                        $total_value = $lastOddSlab->value * $total_free;
                                        $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                         $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $lastOddSlab->catid,
                                                'product_id'            => $lastOddSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $lastOddSlab->catid,
                                            'product_id'            => $lastOddSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastOddSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                                $lastOddSlab2 =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab2) >0)
                                {
                                  
                                   foreach($lastOddSlab2 as $lastOddSlab2) {

                                        $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                        $total_free = $lastOddSlab2->qty * $mainQty3;
                                        $total_value = $lastOddSlab2->value * $total_free;
                                        $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                        $and_total_value = $lastOddSlab2->and_value * $and_total_free;


                                         $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $lastOddSlab2->catid,
                                                'product_id'            => $lastOddSlab2->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab2->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab2->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab2->slab,
                                            'slab_count'            => $mainQty3,
                                            'catid'                 => $lastOddSlab2->catid,
                                            'product_id'            => $lastOddSlab2->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastOddSlab2->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                            } //  cagegory else close
                        }

                        if($textGroup==1)  // for separate offer
                        {
                            foreach($checkSkuProducts as $sku) {
                        
                                $totalSkuQty =  DB::table('tbl_order_details')
                                                ->where('order_id',$lastOrderId)
                                                ->where('product_id', $sku->sku_id)
                                                ->sum('delivered_qty');
                                                
                                $specialSku =  DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                ->where('sku_id',$sku->sku_id)
                                ->where('slab',$totalSkuQty)
                                ->where('status',0)                       
                                ->get();


                                if(sizeof($specialSku) >0 )
                                {

                                    foreach($specialSku as $specialSku) {
                                    
                                     $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialSku->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $catid,
                                        'product_id'            => $specialSku->pid,
                                        'sku_id'                => $specialSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialSku->qty,
                                        'free_value'            => $specialSku->value,
                                        'total_free_value'      => $specialSku->value * $specialSku->qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );

                                        if($specialSku->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $sp_sku_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $specialSku->slab,
                                                'slab_count'            => 1,
                                                'catid'                 => $catid,
                                                'sku_id'                => $specialSku->sku_id,
                                                'product_id'            => $specialSku->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $specialSku->and_qty,
                                                'free_value'            => $specialSku->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }

                                }
                                else
                                {
                                    $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                                    $maxSlabSku = DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                                   ->where('catid',$catid)
                                                   ->where('sku_id',$sku->sku_id)
                                                   ->where('slab', $maxValueSku[0]->slab)
                                                   ->where('status',0)                       
                                                   ->get();

                                    if(sizeof($maxSlabSku) >0 )
                                    {

                                        foreach($maxSlabSku as $maxSlabSku) {

                                            $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                                           //dd($mainQty);
                                            $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                            $total_free = $maxSlabSku->qty * $mainQty;
                                            $total_value = $maxSlabSku->value * $total_free;
                                            $and_total_value =$maxSlabSku->and_qty * $maxSlabSku->and_value;

                                        

                                           $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $maxSlabSku->pid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $maxSlabSku->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($maxSlabSku->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'product_id'            => $maxSlabSku->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $maxSlabSku->and_qty,
                                                    'free_value'            => $maxSlabSku->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }

                                        }
                                      
                                    }

                                    $lastSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                                    if(sizeof($lastSlabSku) >0 )
                                    {

                                         foreach($lastSlabSku as $lastSlab) {

                                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                           
                                            $total_free = $lastSlab->qty * $mainQty1;
                                            $total_value = $lastSlab->value * $total_free;
                                            $and_total_value =$lastSlab->and_qty * $lastSlab->and_value;

                                            $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastSlab->pid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastSlab->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'product_id'            => $lastSlab->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $lastSlab->and_qty,
                                                    'free_value'            => $lastSlab->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }

                                   $lastOddSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                                    if(sizeof($lastOddSlabSku) >0 )
                                    {
                                    
                                       foreach($lastOddSlabSku as $lastOddSlab) {

                                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                            $total_free = $lastOddSlab->qty * $mainQty2;
                                            $total_value = $lastOddSlab->value * $total_free;

                                            $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastOddSlab->pid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastOddSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastOddSlab->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'product_id'            => $lastOddSlab->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $lastOddSlab->and_qty,
                                                    'free_value'            => $lastOddSlab->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }
                                } // SKU else close
                            } // foreach loop close
                        }   
                    }                    
                } 
                                   
               
                             


                // Special Value wise commission Start

                $totalCatValue = DB::table('tbl_order_details')
                                ->select('order_id','cat_id', DB::raw('SUM(delivered_value) AS totalValue'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('cat_id')
                                ->get();

                foreach ($totalCatValue as $totalCatValue) {
                       
                       $valuecatid = $totalCatValue->cat_id;
                       $catWiseValue = $totalCatValue->totalValue;


                       $checkGroupId = DB::select("SELECT * FROM tbl_special_value_wise_category
                                JOIN tbl_special_values_wise ON tbl_special_values_wise.id = tbl_special_value_wise_category.svwid
                                WHERE tbl_special_value_wise_category.categoryid=$valuecatid AND $catWiseValue BETWEEN min AND max");


                       if(sizeof($checkGroupId) >0 )
                        {
                           
                            DB::table('tbl_special_temp_commission')
                                        ->where('order_id', $lastOrderId)
                                        ->where('catid', $valuecatid)
                                        ->where('retailer_id', $retailderID)
                                        ->delete();

                            DB::table('tbl_special_temp_commission')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'group_id'              => $checkGroupId[0]->group_id,
                                'offer_id'              => $checkGroupId[0]->svwid,
                                'catid'                 => $valuecatid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'cat_value'             => $catWiseValue,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                           
                        }
                }

                

                $totalValue = DB::table('tbl_special_temp_commission')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {
                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_special_values_wise WHERE status = 1 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {
                       DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->where('group_id', $totalOfferValue->group_id)
                                ->where('retailer_id', $retailderID)
                                ->where('is_point_wise',0)
                                ->delete();

                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => $foID,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'is_point_wise'         => 0,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }

                // Special Value wise commission end

            }



            if(sizeof($resultSpecialOffers)>0 || sizeof($point)>0 || sizeof($route)>0)
            {
                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();


                $orderNo = $order_no->order_no;
                $autoOrderId = $order_no->auto_order_no;
                $requestDistributorId = $order_no->distributor_id;
                $foID = $order_no->fo_id;
                $requestPointId = $order_no->point_id;
                $requestRouteId = $order_no->route_id;
                $requestRetailerId = $order_no->retailer_id;


                $total_odd=0;
                $total_odd1=0;
                $total_odd2=0;

                
                $catid = array();

                $checkCat =  DB::table('tbl_offer_qty_commission_value')
                                            ->select('catid','slab','per_pcs','value','status')
                                            ->groupBy('catid')
                                            ->get();
                

                foreach($checkCat as $skua) {
                    $catid[]= $skua->catid;
                }

                $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereIn('cat_id', $catid)
                                    ->groupBy('cat_id')
                                    ->get();
                

                foreach ($totalCat as $catQty) 
                {
                    $catid=$catQty->cat_id;
                    
                    $itemsDelete = DB::table('tbl_order_free_qty_commission')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
           
                    $totalSpecialQty =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->sum('delivered_qty');
                   

                    if($totalSpecialQty>0 )
                    {
                        $specialProducts =  DB::table('tbl_offer_qty_commission_value')->select('catid','slab','per_pcs','value','status')
                        ->where('catid',$catid)
                        ->where('slab',$totalSpecialQty)
                        ->where('status',0)                       
                        ->get();

                        if(sizeof($specialProducts) >0 )
                        {
                            foreach($specialProducts as $specialProducts) {
                            
                             $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $specialProducts->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'free_value'            => $specialProducts->value,
                                'total_free_value'      => $specialProducts->value,
                                'distributor_id'        => $requestDistributorId,
                                'point_id'              => $requestPointId,
                                'route_id'              => $requestRouteId,
                                'retailer_id'           => $requestRetailerId,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                //'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                        else
                        {
                            $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<'".$totalSpecialQty."' AND status=0");


                            $maxSlab = DB::table('tbl_offer_qty_commission_value')->select('slab','catid','per_pcs','value')
                                           ->where('catid',$catid)
                                           ->where('slab', $maxValue[0]->slab)
                                           ->where('status',0)                       
                                           ->get();

                            if(sizeof($maxSlab) >0 )
                            {
                                foreach($maxSlab as $maxSlab) {

                                    $mainQty = (int)($totalSpecialQty/$maxSlab->slab);
                                    $total_odd = $totalSpecialQty - ($mainQty * $maxSlab->slab);
                                    //$total_free = $maxSlab->qty * $mainQty;
                                    $total_value = $maxSlab->value * $mainQty;

                                  $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'free_value'            => $maxSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                }
                              
                            }

                            $lastSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                            if(sizeof($lastSlab) >0 )
                            {
                                 foreach($lastSlab as $lastSlab) {

                                    $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                    $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                    //dd($total_odd1);
                                    //$total_free = $lastSlab->qty * $mainQty1;
                                    $total_value = $lastSlab->value * $mainQty1;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                }
                            }

                           $lastOddSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlab) >0 )
                            {
                              
                               foreach($lastOddSlab as $lastOddSlab) {

                                    $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                    $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                    //$total_free = $lastOddSlab->qty * $mainQty2;
                                    $total_value = $lastOddSlab->value * $mainQty2;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastOddSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                }
                            }

                            $lastOddSlab2 =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd2."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlab2) >0 )
                            {
                              
                               foreach($lastOddSlab2 as $lastOddSlab2) {

                                    $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                    //$total_free = $lastOddSlab2->qty * $mainQty3;
                                    $total_value = $lastOddSlab2->value * $mainQty3;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab2->slab,
                                            'slab_count'            => $mainQty3,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastOddSlab2->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                }
                            }


                        } 
                    }

                }
            } //  cagegory Qty wise commission else close

            $pointCheckOffer     = DB::table('tbl_point_wise_offer')
                        ->where('tbl_point_wise_offer.iPointId',$pointID)
                        ->where('dEndDate','>=',$currentDay)
                        ->where('tbl_point_wise_offer.iStatus',0)                      
                        ->first();

           if(sizeof($pointCheckOffer)>0)
           {

                $totalCatValue = DB::table('tbl_order_details')
                                ->select('order_id','cat_id', DB::raw('SUM(delivered_value) AS totalValue'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('cat_id')
                                ->get();

                foreach ($totalCatValue as $totalCatValue) {
                       
                       $valuecatid = $totalCatValue->cat_id;
                       $catWiseValue = $totalCatValue->totalValue;


                      /* $checkGroupId = DB::select("SELECT * FROM tbl_point_wise_value_category
                                JOIN tbl_point_wise_value ON tbl_point_wise_value.id = tbl_point_wise_value_category.point_value_id
                                WHERE tbl_point_wise_value_category.categoryid=$valuecatid AND $catWiseValue BETWEEN min AND max");*/

                       $checkGroupId     = DB::table('tbl_point_wise_value_category')
                                    ->join('tbl_point_wise_value', 'tbl_point_wise_value.id', '=', 'tbl_point_wise_value_category.point_value_id')
                                    ->where('tbl_point_wise_value.point_id',$pointID)
                                    ->where('tbl_point_wise_value_category.categoryid',$valuecatid)
                                    ->first();

                       if(sizeof($checkGroupId) >0 )
                        {
                           
                            DB::table('tbl_point_temp_commission_cat')
                                        ->where('order_id', $lastOrderId)
                                        ->where('catid', $valuecatid)
                                        ->where('retailer_id', $retailderID)
                                        ->delete();

                            DB::table('tbl_point_temp_commission_cat')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'group_id'              => $checkGroupId->group_id,
                                'offer_id'              => $pointCheckOffer->iId,
                                'catid'                 => $valuecatid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'cat_value'             => $catWiseValue,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                           
                        }
                }

                

                $totalValue = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {
                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_point_wise_value WHERE status = 0 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {

                        $checkSpecial = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id','catid')
                                    ->where('order_id', $lastOrderId)
                                    ->where('group_id', $totalOfferValue->group_id)
                                    ->get(); 

                       $sp_catid = array();

                        foreach($checkSpecial as $sku) {
                            $sp_catid[]= $sku->catid;
                        }

                        DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->whereIn('catid', $sp_catid)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                       DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->where('group_id', $totalOfferValue->group_id)
                                ->where('retailer_id', $retailderID)
                                ->where('is_point_wise',1)
                                ->delete();

                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => $foID,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'is_point_wise'         => 1,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }
            }
                // Special Value wise commission end
				
				
				/* Clear User Regular Offer Selection */
				$offer = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $request->get('orderid'))                                                
                        ->get();

				foreach ($offer as $value) {
					DB::table('tbl_order_free_qty')->where('order_id', $request->get('orderid'))
						//->where('fo_id', Auth::user()->id)                
						->where('global_company_id', Auth::user()->global_company_id)->update(
						[                    
							'status' => NULL
						]
					);  
				}
				
				/* Clear User Special Offer Selection */
				$spec_offer = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $request->get('orderid'))                                                
                        ->get();

				foreach ($spec_offer as $value) {
					DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))
						->where('global_company_id', Auth::user()->global_company_id)->update(
						[                    
							'status' => NULL
						]
					);  
				}
				

                DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->where('status',3)->update(
                    [
                        'delivery_date'            => date('Y-m-d H:i:s')
                    ]
                );
                
				
				/* Zubair Clear Bundle Offer Begin */ //added Dec_09_2018
				DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
				/* Zubair Clear Bundle Offer END */
		

            return Redirect::back()->with('success', 'Delivery Products Qty Update Successfully.');
       // return Redirect::to('/order')->with('success', 'Successfully Confirm Delivery Done.');  
        
		}
    }

    
    public function ssg_distributor_order_closed($order_id,$partialOrder)
    {
        
        DB::table('tbl_order_details')
                ->where('order_id', $order_id)
                ->update(
                    [
                        'order_det_type'            => 'Confirmed',
                        'order_det_status'          => 'Closed' 
                    ]
            );
            
        DB::table('tbl_order')->where('order_id', $order_id)->update(
            [
                'order_status'  => 'Closed',
                'order_type'  => 'Confirmed',
                'closing_date'  => date('Y-m-d')
            ]
        );
        
        $OrdData = DB::table('tbl_order')
                        ->where('order_id', $order_id)
                        ->first();
        
        session()->put('offersSelected','regular');
     
        return Redirect::to('/order-edit'. '/' . $order_id . '/' . $OrdData->fo_id . '/' . $partialOrder)->with('success', 'Successfully Order Closed');
    
    }
    
    // public function order_confirm_delivery(Request $request )
 //    {
          
 //           DB::table('tbl_order')->where('order_id', $request->get('orderid'))

 //                ->where('global_company_id', Auth::user()->global_company_id)->update(
 //                [
 //                    'order_type'             => 'Delivered',
 //                    'update_date'            => date('Y-m-d H:i:s')
 //                ]
 //            );
            
            
    //      //zubair retailer balance call June-11-2018
    //      $orderData = DB::table('tbl_order')
    //              ->select('tbl_order.*')
    //              ->where('order_id', $request->get('orderid'))
    //              ->first();
    
    //      if(sizeof($orderData)>0)
    //      {
    //          $retailer_info = array();
    //          $retailer_info['trans_type'] = 'sales'; 
    //          $retailer_info['accounts_type'] = 'expense';
    //          $retailer_info['retailer_id'] = $orderData->retailer_id;
    //          $retailer_info['invoice_no'] = $orderData->order_no;
                
    //          $this->reatiler_credit_ledger($retailer_info);
    //      }   

    //      $this->sync_offer_stock($request->get('orderid'), 'Delivered');
        
 //            if($request->get('offer_type') == 'exclusive')
 //            {

 //                DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
                
    //          $this->sync_offer_stock($request->get('orderid'),$request->get('offer_type'));
 //            }

 //            if($request->get('offer_type') == 'regular' OR $request->get('offer_type') == '1')
 //            {

 //                DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->delete();

 //                DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                
    //          $this->sync_offer_stock($request->get('orderid'), $request->get('offer_type'));
 //            }

 //           // return Redirect::to('/order')->with('success', 'Successfully Confirm Delivery Done.'); 
 //    }
    
    
	private function sync_offer_stock($order_id, $offer_type, $part_order_id = '')
    {
        //delivery Data
        if($order_id && $offer_type == 'Delivered')
        {
            $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

            $pointID = '';
            if(sizeof($division_point)>0)
            {
                $pointID = $division_point->point_id;
            }
            
            $resOrderData = DB::table('tbl_order_details')
                                ->select('tbl_order_details.*')
                                ->where('tbl_order_details.order_id', $order_id)
                                ->where('tbl_order_details.partial_order_id', $part_order_id)
                                ->get();
            
            if(sizeof($resOrderData)>0 && $pointID>0)
            {
                // split delivered and replace with head also - FEB-23-2019
				foreach($resOrderData as $resOrderRow)
                {
                    
				  if($resOrderRow->delivered_qty > 0)
				  {
					$totDeliveredQnty = $resOrderRow->delivered_qty;
                    $totDeliveredVal = $resOrderRow->delivered_value;
                    
                    $this->stock_out($pointID, $resOrderRow->cat_id, $resOrderRow->product_id, 
                    $totDeliveredQnty, $totDeliveredVal, 'regular', $order_id, $part_order_id);
					
				   if($resOrderRow->replace_delivered_qty > 0)
				   {
						$totReplaceQnty = $resOrderRow->replace_delivered_qty;
						$totReplaceVal = $resOrderRow->replace_delivered_qty * $resOrderRow->p_unit_price;
						
						$this->stock_out($pointID, $resOrderRow->cat_id, $resOrderRow->product_id, 
						$totReplaceQnty, $totReplaceVal, 'replace', $order_id, $part_order_id);  
				   }	

				  }	elseif($resOrderRow->replace_delivered_qty > 0) {
					  
					$totReplaceQnty = $resOrderRow->replace_delivered_qty;
                    $totReplaceVal = $resOrderRow->replace_delivered_qty * $resOrderRow->p_unit_price;
                    
                    $this->stock_out($pointID, $resOrderRow->cat_id, $resOrderRow->product_id, 
                    $totReplaceQnty, $totReplaceVal, 'replace', $order_id, $part_order_id);  
					  
				  }					  
					
                }
				
            }
            
            /* Free Commission Value Wise */
            
            //$AllStockOutProduct = array();
            
            $resExValWiseCommData = DB::table('tbl_order_special_free_qty')
                                    ->select('tbl_order_special_free_qty.*')
                                    ->where('tbl_order_special_free_qty.order_id', $order_id)
                                    ->where('tbl_order_special_free_qty.status', '3')
                                    ->get();
            
            
            if(sizeof($resExValWiseCommData)>0)
            {

                foreach($resExValWiseCommData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free', $order_id);
                }
            }
            
            
        }   
        
        
        // Exclusive Offer
        if($order_id && $offer_type == 'exclusive')
        {
        
            //$AllStockOutProduct = array();
            
            $resExOfferData = DB::table('tbl_order_special_free_qty')
                                ->select('tbl_order_special_free_qty.*')
                                ->where('tbl_order_special_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();
            
            
            if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free', $order_id);
                }
            }

          
            $resExOfferAndData = DB::table('tbl_order_special_and_free_qty')
                                ->select('tbl_order_special_and_free_qty.*')
                                ->join('tbl_order_special_free_qty','tbl_order_special_free_qty.free_id','=','tbl_order_special_and_free_qty.special_id')
                                ->where('tbl_order_special_and_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();

            if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                   $this->stock_out($resExOfferAndRow->point_id, $resExOfferAndRow->catid, $resExOfferAndRow->product_id, 
                    $resExOfferAndRow->total_free_qty, $resExOfferAndRow->total_free_value, 'free', $order_id);
                }
            }   
        
            
        } 
        
        // Regular Offer
        if($order_id && ($offer_type == 'regular') )
        {
            
            $resRegOfferData = DB::table('tbl_order_free_qty')
                                ->select('tbl_order_free_qty.*')
                                ->where('tbl_order_free_qty.order_id', $order_id)
                                ->where('tbl_order_free_qty.status', '0')
                                ->get();
            
            if(sizeof($resRegOfferData)>0)
            {
                foreach($resRegOfferData as $resRegOfferRow)
                {
                    $this->stock_out($resRegOfferRow->point_id, $resRegOfferRow->catid, $resRegOfferRow->product_id, 
                    $resRegOfferRow->total_free_qty, $resRegOfferRow->total_free_value, 'free', $order_id);
                }
            }
        }

        
        // Bundle Stock Out
        if($order_id && ($offer_type == 'bundle') )
        {
            
            $resBundleOfferData = DB::table('tbl_order_gift')
                                ->select('tbl_order_gift.*')
                                ->where('tbl_order_gift.orderid', $order_id)
                                ->get();
            
            if(sizeof($resBundleOfferData)>0)
            {
                
                $depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
                                        (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')
                                    ");
                
                if($depotList[0]->point_id)
                {
                    foreach($resBundleOfferData as $resBundleOfferRow)
                    {
                        $this->stock_out($depotList[0]->point_id, $resBundleOfferRow->cat_id, $resBundleOfferRow->proid, 
                        $resBundleOfferRow->free_qty, 0, 'free', $order_id);
                    }
                }
                
            }
        }

        
		// Exclusive Offer
        if($order_id && $offer_type == 'freepending')
        {
        
            //$AllStockOutProduct = array();
            
            $resExOfferData = DB::table('tbl_order_special_free_qty')
                                ->select('tbl_order_special_free_qty.*')
                                ->where('tbl_order_special_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.ref_id','>',0)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();
            
            
            if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->delivery_catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free', $order_id);
                }
            }

            /*if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $AllStockOutProduct['spec_free_qty'][] = $resExOfferRow;
                }
            }*/

            $resExOfferAndData = DB::table('tbl_order_special_and_free_qty')
                                ->select('tbl_order_special_and_free_qty.*')
                                ->join('tbl_order_special_free_qty','tbl_order_special_free_qty.free_id','=','tbl_order_special_and_free_qty.special_id')
                                ->where('tbl_order_special_and_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();

            if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                   $this->stock_out($resExOfferAndRow->point_id, $resExOfferAndRow->catid, $resExOfferAndRow->product_id, 
                    $resExOfferAndRow->total_free_qty, $resExOfferAndRow->total_free_value, 'free', $order_id);
                }
            }   

            
        }    
    
    }
	
	
	
    private function sync_offer_stock_old($order_id, $offer_type)
    {
        //delivery Data
        if($order_id && $offer_type == 'Delivered')
        {
            $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

            $pointID = '';
            if(sizeof($division_point)>0)
            {
                $pointID = $division_point->point_id;
            }
            
            $resOrderData = DB::table('tbl_order_details')
                                ->select('tbl_order_details.*')
                                ->where('tbl_order_details.order_id', $order_id)
                                ->get();
            
            if(sizeof($resOrderData)>0 && $pointID>0)
            {
                foreach($resOrderData as $resOrderRow)
                {
                    $totDeliveredQnty = $resOrderRow->delivered_qty + $resOrderRow->replace_delivered_qty;
                    $totDeliveredVal = $resOrderRow->delivered_value + 0;
                    
                    $this->stock_out($pointID, $resOrderRow->cat_id, $resOrderRow->product_id, 
                    $totDeliveredQnty, $totDeliveredVal, 'regular');
                }
            }
            
            /* Free Commission Value Wise */
            
            //$AllStockOutProduct = array();
            
            $resExValWiseCommData = DB::table('tbl_order_special_free_qty')
                                    ->select('tbl_order_special_free_qty.*')
                                    ->where('tbl_order_special_free_qty.order_id', $order_id)
                                    ->where('tbl_order_special_free_qty.status', '3')
                                    ->get();
            
            
            if(sizeof($resExValWiseCommData)>0)
            {

                foreach($resExValWiseCommData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free');
                }
            }
            
            
        }   
        
        
        // Exclusive Offer
        if($order_id && $offer_type == 'exclusive')
        {
        
            //$AllStockOutProduct = array();
            
            $resExOfferData = DB::table('tbl_order_special_free_qty')
                                ->select('tbl_order_special_free_qty.*')
                                ->where('tbl_order_special_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();
            
            
            if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free');
                }
            }

            /*if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $AllStockOutProduct['spec_free_qty'][] = $resExOfferRow;
                }
            }*/

            $resExOfferAndData = DB::table('tbl_order_special_and_free_qty')
                                ->select('tbl_order_special_and_free_qty.*')
                                ->join('tbl_order_special_free_qty','tbl_order_special_free_qty.free_id','=','tbl_order_special_and_free_qty.special_id')
                                ->where('tbl_order_special_and_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();

            if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                   $this->stock_out($resExOfferAndRow->point_id, $resExOfferAndRow->catid, $resExOfferAndRow->product_id, 
                    $resExOfferAndRow->total_free_qty, $resExOfferAndRow->total_free_value, 'free');
                }
            }   

            
            /*if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                    $AllStockOutProduct['spec_free_and_qty'][] = $resExOfferAndRow;
                }
            }   */
        
        
            ///////////////////////////////// Zubair Sync Stock Process /////////////////////////////////////

            /*if(sizeof($AllStockOutProduct['spec_free_qty'])>0)
            {   
                
                foreach($AllStockOutProduct['spec_free_qty'] as $rowStockOutProduct)
                {             

                    $this->stock_out($rowStockOutProduct->point_id, $rowStockOutProduct->catid, $rowStockOutProduct->product_id, 
                    $rowStockOutProduct->total_free_qty, $rowStockOutProduct->total_free_value, 'free');
            
                } // for closed
            
            }*/ // if closed
            
            /*if(sizeof($AllStockOutProduct['spec_free_and_qty'])>0)
            {   
                
                foreach($AllStockOutProduct['spec_free_and_qty'] as $rowStockOutAndProduct)
                {             

                    $this->stock_out($rowStockOutAndProduct->point_id, $rowStockOutAndProduct->catid, $rowStockOutAndProduct->product_id, 
                    $rowStockOutAndProduct->total_free_qty, $rowStockOutAndProduct->total_free_value, 'free');
            
                } // for closed
            
            }*/ // if closed
            
        } 
        
        // Regular Offer
        if($order_id && ($offer_type == 'regular') )
        {
            
            $resRegOfferData = DB::table('tbl_order_free_qty')
                                ->select('tbl_order_free_qty.*')
                                ->where('tbl_order_free_qty.order_id', $order_id)
                                ->where('tbl_order_free_qty.status', '0')
                                ->get();
            
            if(sizeof($resRegOfferData)>0)
            {
                foreach($resRegOfferData as $resRegOfferRow)
                {
                    $this->stock_out($resRegOfferRow->point_id, $resRegOfferRow->catid, $resRegOfferRow->product_id, 
                    $resRegOfferRow->total_free_qty, $resRegOfferRow->total_free_value, 'free');
                }
            }
        }

        
        // Bundle Stock Out
        if($order_id && ($offer_type == 'bundle') )
        {
            
            /*
            $resBundleOfferData = DB::table('tbl_order_gift')
                                ->select('tbl_bundle_product_details.*')
                                ->join('tbl_bundle_product_details', 'tbl_bundle_product_details.id', '=', 'tbl_order_gift.proid')
                                ->where('tbl_order_gift.orderid', $order_id)
                                ->get();
            */
            
            $resBundleOfferData = DB::table('tbl_order_gift')
                                ->select('tbl_order_gift.*')
                                ->where('tbl_order_gift.orderid', $order_id)
                                ->get();
            
            if(sizeof($resBundleOfferData)>0)
            {
                
                $depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
                                        (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')
                                    ");
                
                if($depotList[0]->point_id)
                {
                    foreach($resBundleOfferData as $resBundleOfferRow)
                    {
                        $this->stock_out($depotList[0]->point_id, $resBundleOfferRow->cat_id, $resBundleOfferRow->proid, 
                        $resBundleOfferRow->free_qty, 0, 'free');
                    }
                }
                
            }
        }

        // Exclusive Offer
        if($order_id && $offer_type == 'freepending')
        {
        
            //$AllStockOutProduct = array();
            
            $resExOfferData = DB::table('tbl_order_special_free_qty')
                                ->select('tbl_order_special_free_qty.*')
                                ->where('tbl_order_special_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.ref_id','>',0)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();
            
            
            if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $this->stock_out($resExOfferRow->point_id, $resExOfferRow->delivery_catid, $resExOfferRow->product_id, 
                    $resExOfferRow->total_free_qty, $resExOfferRow->total_free_value, 'free');
                }
            }

            /*if(sizeof($resExOfferData)>0)
            {

                foreach($resExOfferData as $resExOfferRow)
                {
                    $AllStockOutProduct['spec_free_qty'][] = $resExOfferRow;
                }
            }*/

            $resExOfferAndData = DB::table('tbl_order_special_and_free_qty')
                                ->select('tbl_order_special_and_free_qty.*')
                                ->join('tbl_order_special_free_qty','tbl_order_special_free_qty.free_id','=','tbl_order_special_and_free_qty.special_id')
                                ->where('tbl_order_special_and_free_qty.order_id', $order_id)
                                ->where('tbl_order_special_free_qty.status', '0')
                                ->get();

            if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                   $this->stock_out($resExOfferAndRow->point_id, $resExOfferAndRow->catid, $resExOfferAndRow->product_id, 
                    $resExOfferAndRow->total_free_qty, $resExOfferAndRow->total_free_value, 'free');
                }
            }   

            
            /*if(sizeof($resExOfferAndData)>0)
            {

                foreach($resExOfferAndData as $resExOfferAndRow)
                {
                    $AllStockOutProduct['spec_free_and_qty'][] = $resExOfferAndRow;
                }
            }   */
        
        
            ///////////////////////////////// Zubair Sync Stock Process /////////////////////////////////////

            /*if(sizeof($AllStockOutProduct['spec_free_qty'])>0)
            {   
                
                foreach($AllStockOutProduct['spec_free_qty'] as $rowStockOutProduct)
                {             

                    $this->stock_out($rowStockOutProduct->point_id, $rowStockOutProduct->catid, $rowStockOutProduct->product_id, 
                    $rowStockOutProduct->total_free_qty, $rowStockOutProduct->total_free_value, 'free');
            
                } // for closed
            
            }*/ // if closed
            
            /*if(sizeof($AllStockOutProduct['spec_free_and_qty'])>0)
            {   
                
                foreach($AllStockOutProduct['spec_free_and_qty'] as $rowStockOutAndProduct)
                {             

                    $this->stock_out($rowStockOutAndProduct->point_id, $rowStockOutAndProduct->catid, $rowStockOutAndProduct->product_id, 
                    $rowStockOutAndProduct->total_free_qty, $rowStockOutAndProduct->total_free_value, 'free');
            
                } // for closed
            
            }*/ // if closed
            
        }    
    
    }
    
	
	private function stock_out($pointID, $cat_id, $product_id, $prod_qnty, $prod_value, $trans_type, $reference_id, $partial_order_id = '')
    {
        //dd(session('isDepot'));
			
			$inOut='2'; // stock-out operation
		  
			DB::table('depot_inventory')->insert(
				[
					'point_id'           => $pointID,
					'depot_in_charge'    => Auth::user()->id,
					'reference_id'       => $reference_id,
					'partial_order_id'   => $partial_order_id,
					'cat_id'             => $cat_id,
					'product_id'         => $product_id,
					'product_qty'        => $prod_qnty,
					'product_value'      => $prod_value,
					'inventory_date'     => date('Y-m-d'),
					'inventory_type'     => $inOut,
					'transaction_type'   => $trans_type,
					'global_company_id'  => Auth::user()->global_company_id,
					'created_by'         => Auth::user()->id
				]
			); 
				

			$stockOutProduct = DB::table('depot_stock')
							->select('depot_id','point_id','cat_id','product_id','stock_qty')
							->where('point_id', $pointID)
							->where('cat_id', $cat_id)
							->where('product_id', $product_id)
							->first();

			$totalOutQty = $prod_qnty;
                
			if(sizeof($stockOutProduct)>0)
			{
					
				$totalOutQty = $stockOutProduct->stock_qty - $prod_qnty;
				
				
				DB::table('depot_stock')
				->where('point_id',$pointID)
				->where('cat_id',$cat_id)
				->where('product_id',$product_id)
				->update(
				[
					'point_id'           => $pointID,
					'cat_id'             => $cat_id,
					'product_id'         => $product_id,
					'stock_qty'          => $totalOutQty,
					'global_company_id'  => Auth::user()->global_company_id,
					'updated_by'         => Auth::user()->id                   
					
				]
				);

			} else {  //save negative stock 
				
				DB::table('depot_stock')->insert(
					[
						'point_id'           => $pointID,
						'cat_id'             => $cat_id,
						'product_id'         => $product_id,
						'stock_qty'          => '-' . $totalOutQty,
						'global_company_id'  => Auth::user()->global_company_id,
						'created_by'         => Auth::user()->id      
					]
				); 
			
			}
             
    }
	
	
    
    private function stock_out_old($pointID, $cat_id, $product_id, $prod_qnty, $prod_value, $trans_type)
    {
        //dd(session('isDepot'));

        if(session('isDepot')=='1')
        {

        $inOut='2'; // stock-out operation
      
        DB::table('depot_inventory')->insert(
            [
                'point_id'           => $pointID,
                'depot_in_charge'    => Auth::user()->id,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'product_qty'        => $prod_qnty,
                'product_value'      => $prod_value,
                'inventory_date'     => date('Y-m-d'),
                'inventory_type'     => $inOut,
                'transaction_type'   => $trans_type,
                'global_company_id'  => Auth::user()->global_company_id,
                'created_by'         => Auth::user()->id
            ]
        ); 
            

        $stockOutProduct = DB::table('depot_stock')
                        ->select('depot_id','point_id','cat_id','product_id','stock_qty')
                        ->where('point_id', $pointID)
                        ->where('cat_id', $cat_id)
                        ->where('product_id', $product_id)
                        ->first();

        $totalOutQty = $prod_qnty;
                
        if(sizeof($stockOutProduct)>0)
        {
                
            $totalOutQty = $stockOutProduct->stock_qty - $prod_qnty;
            
            
            DB::table('depot_stock')
            ->where('point_id',$pointID)
            ->where('cat_id',$cat_id)
            ->where('product_id',$product_id)
            ->update(
            [
                'point_id'           => $pointID,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'stock_qty'          => $totalOutQty,
                'global_company_id'  => Auth::user()->global_company_id,
                'updated_by'         => Auth::user()->id                   
                
            ]
            );

        } else {  //save negative stock 
            
            DB::table('depot_stock')->insert(
                [
                    'point_id'           => $pointID,
                    'cat_id'             => $cat_id,
                    'product_id'         => $product_id,
                    'stock_qty'          => '-' . $totalOutQty,
                    'global_company_id'  => Auth::user()->global_company_id,
                    'created_by'         => Auth::user()->id      
                ]
            ); 
        
        }

        }
        else
        {
            // for distributor

            $inOut='2'; // stock-out operation
      
        DB::table('distributor_inventory')->insert(
            [
                'point_id'           => $pointID,
                'distributor_in_charge' => Auth::user()->id,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'product_qty'        => $prod_qnty,
                'product_value'      => $prod_value,
                'inventory_date'     => date('Y-m-d'),
                'inventory_type'     => $inOut,
                'transaction_type'   => $trans_type,
                'global_company_id'  => Auth::user()->global_company_id,
                'created_by'         => Auth::user()->id
            ]
        ); 
            

        $stockOutProduct = DB::table('distributor_stock')
                        ->select('distributor_id','point_id','cat_id','product_id','stock_qty')
                        ->where('point_id', $pointID)
                        ->where('cat_id', $cat_id)
                        ->where('product_id', $product_id)
                        ->first();

        $totalOutQty = $prod_qnty;
                
        if(sizeof($stockOutProduct)>0)
        {
                
            $totalOutQty = $stockOutProduct->stock_qty - $prod_qnty;            
            
            DB::table('distributor_stock')
            ->where('point_id',$pointID)
            ->where('cat_id',$cat_id)
            ->where('product_id',$product_id)
            ->update(
            [
                'point_id'           => $pointID,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'stock_qty'          => $totalOutQty,
                'global_company_id'  => Auth::user()->global_company_id,
                'updated_by'         => Auth::user()->id                   
                
            ]
            );

        } else {  //save negative stock 
            
            DB::table('distributor_stock')->insert(
                [
                    'point_id'           => $pointID,
                    'cat_id'             => $cat_id,
                    'product_id'         => $product_id,
                    'stock_qty'          => '-' . $totalOutQty,
                    'global_company_id'  => Auth::user()->global_company_id,
                    'created_by'         => Auth::user()->id      
                ]
            ); 
        
        }
        }
                
                
    }


    /* Zubair Retailer Balance Start June-11-2018*/
    
    private function reatiler_credit_ledger($retailer_info = array())
    {
        if(is_array($retailer_info))
        {
            if($retailer_info['invoice_no']!='')
            {
              //$retCreditLedger = DB::table('retailer_credit_ledger')->where('retailer_invoice_no',$retailer_info['invoice_no'])->delete();  
            }
            
            
            $credit_ledger_Data = array();
            $credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
            $credit_ledger_Data['point_id'] = $retailer_info['point_id'];
            $credit_ledger_Data['collection_id'] = 0;
            $credit_ledger_Data['trans_type'] = $retailer_info['trans_type'];
            $credit_ledger_Data['accounts_type'] = $retailer_info['accounts_type'];
             $credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s');
            
			$credit_ledger_Data['order_id'] = $retailer_info['order_id'];
            $credit_ledger_Data['partial_order_id'] = $retailer_info['partial_type'];
			
			            
            /////////////////////////////// Retailer Credit Balance ////////////////////////////////
            
            $retailerLedger = DB::select("SELECT * FROM retailer_credit_ledger WHERE retailer_id = '".$retailer_info['retailer_id']."'
                            ORDER BY 1 DESC LIMIT 1");
                            
            ##opening balance
            if(sizeof($retailerLedger)>0)
            {
                $retOpeningBalance = $retailerLedger[0]->retailer_balance;
            } else {
                $retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$retailer_info['retailer_id']."'");
                $retOpeningBalance = $retailerData[0]->opening_balance;
            }
            
            $credit_ledger_Data['retailer_opening_balance'] = $retOpeningBalance;

            ## invoice No & sales
            $credit_ledger_Data['retailer_invoice_no'] = $retailer_info['invoice_no'];          
            

			/*            $rowRet = DB::select("SELECT grand_total_value FROM tbl_order WHERE order_no = '".$retailer_info['invoice_no']."'");
            $retInVoiceSales = $rowRet[0]->grand_total_value;

			*/
			
			$rowRet = DB::select("SELECT SUM(delivered_value) AS grand_total_value FROM tbl_order_details 
			WHERE order_id = '".$retailer_info['order_id']."' AND partial_order_id = '".$retailer_info['partial_type']."' ");
            $retInVoiceSales = $rowRet[0]->grand_total_value;            
            $credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;           
            
            ##totalCollection
            $retCollect = 0;
            $credit_ledger_Data['retailer_collection'] = $retCollect;
            
            
            ##retailerBalance
            $remBalance = ($retOpeningBalance + $retInVoiceSales) - $retCollect;
            
            $credit_ledger_Data['retailer_balance'] = $remBalance;
            
            $credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
            $credit_ledger_Data['entry_by'] = Auth::user()->id;
            
            
            DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
            
            
            /*
            
            $memoCommission  = 0;
            
            $reultProRate  = DB::select("SELECT * FROM tbl_commission 
            WHERE businessType='".Auth::user()->business_type_id."' 
            AND '".$retInVoiceSales."' BETWEEN minSlab AND maxSlab LIMIT 1");
            
            if(sizeof($reultProRate) > 0)
            {
            
                $memoCommission = $reultProRate[0]->rat;
                
                $CommissionEntry = array();
                
                $CommissionEntry['retailer_id'] = $retailer_info['retailer_id'];
                $CommissionEntry['point_id'] = $retailer_info['point_id'];
                
                $CommissionEntry['collection_id'] = 0;
                $CommissionEntry['retailer_invoice_no'] = 0;
                $CommissionEntry['retailer_invoice_sales'] = 0;
                
                $CommissionEntry['trans_type'] = 'memo_commission';
                $CommissionEntry['accounts_type'] = 'expense';
                $CommissionEntry['credit_ledger_date'] = date('Y-m-d H:i:s');
                
                $CommissionEntry['retailer_opening_balance'] = $remBalance;
                $CommissionEntry['memo_commission'] = $memoCommission;
                
                $memoCommissionValue = ($remBalance * $memoCommission) / 100;
                $CommissionEntry['memo_commission_value'] = $memoCommissionValue;
                
                $retailer_balance = $remBalance - $memoCommissionValue;
                $CommissionEntry['retailer_balance'] = $retailer_balance;
                
                DB::table('retailer_credit_ledger')->insert([$CommissionEntry]);
            }
            
            */
            
        }   
    }
    
    
    
    
    public function ssg_invoice_order($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order';                      // Required Variable
        $pageTitle      = 'Invoice Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id', 'tbl_order_details.delivered_date', 'tbl_order_details.delivery_challan', 'tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        //->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.order_no','tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_date','tbl_order.print_count','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        //->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type',
        'tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name',
        'tbl_user_details.cell_phone','users.id','users.display_name', 'users.sap_code')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

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

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                           
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.status',0)        
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)
                                ->where('og.status',0)                                
                                ->first();
        }

        $reultProRate ='';

         $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')
                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)
                        ->where('is_point_wise', 0)                           
                        ->get();
                        
        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)
                        ->where('is_point_wise', 1)                           
                        ->get();

        return view('sales/distributor/invoiceDetails', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift','reultProRate','commissionWiseItem','specialValueWise','pointValueWise'));
    }


    public function ssg_invoice_order_partial($orderMainId,$foMainId,$orderPartial)
    {
        $selectedMenu   = 'Order';                      // Required Variable
        $pageTitle      = 'Invoice Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_status','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        //->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPartial)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_date','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        //->where('tbl_order.order_type','Confirmed')
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

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

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

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                           
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.status',0)        
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)
                                ->where('og.status',0)                                
                                ->first();
        }

        $reultProRate ='';

         $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')
                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)                           
                        ->where('partial_order_id', $orderPartial)                           
                        ->get();

        return view('sales/distributor/invoiceDetailsPartial', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift','reultProRate','commissionWiseItem','specialValueWise'));
    }




    // NEW OPEN ORDER

    public function ssg_order_check($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order';                    // Required Variable
        $pageTitle      = 'Order Details';           // Page Slug Title

        $resultPartialOrder  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(p_grand_total) as orderPartialTotal"),'order_id','partial_order_id','order_det_status','order_det_type')
                        ->where('order_id',$orderMainId)                                          
                        //->where('order_det_type', 'Confirmed')
                        ->whereIn('order_det_type', array('Confirmed','Delivered'))
                        ->groupBy('partial_order_id')
                        ->orderBy('partial_order_id','ASC')                                         
                        ->get();


        // Total Partial Order Pending
        $totaPartialOrder = DB::table('tbl_order_details')
                        ->select('partial_order_id')
                        ->where('order_id',$orderMainId)
                        //->where('order_det_type','Confirmed')
                        ->where('order_det_status','!=', 'Closed')
                        ->groupBy('partial_order_id')
                        ->get()->count();

        // Total Partial Order Delivery
        $totaPartialOrderDelivered = DB::table('tbl_order_details')
                        ->select('partial_order_id')
                        ->where('order_id',$orderMainId)
                        ->where('order_det_type','Delivered')
                        ->where('order_det_status','!=', 'Closed')
                        ->groupBy('partial_order_id')
                        ->get()->count();

        //dd($totaPartialOrder);

        $foMainId   = $foMainId;

        return view('sales/distributor/bucketOrder', compact('selectedMenu','pageTitle','resultPartialOrder','foMainId','totaPartialOrder','totaPartialOrderDelivered'));
    }

    public function ssg_invoice_print(Request $request)
    {

        DB::table('tbl_order')->where('order_id', $request->get('invoiceid'))
        ->update(
            [
                'print_count' => 1
            ]
        );
    }


    // Free pending 

    public function ssg_free_pending()
    {
        $selectedMenu   = 'Free Pending';             // Required Variable
        $pageTitle      = 'Free';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                       
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $todate      = date('Y-m-d');
        $distributor = Auth::user()->id;

        $resultOrderList= DB::select("SELECT e.order_id,date(e.order_date) AS order_date,e.order_no,tbl_user_details.first_name,tbl_retailer.name AS rname,e.fo_id,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,sum(e.free_delivery_qty) AS delQty FROM (
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_free_qty WHERE date(order_date)='$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_and_free_qty WHERE date(order_date)='$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_free_qty WHERE date(order_date)='$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_regular_and_free_qty WHERE date(order_date)='$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            ) AS e 
            join tbl_user_details on tbl_user_details.user_id=e.fo_id
            join tbl_retailer on tbl_retailer.retailer_id=e.retailer_id
            group by e.order_id ORDER BY e.order_id");
       

        return view('sales/freePending/order', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_free_pending_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $distributor = Auth::user()->id;
        $foid = $request->get('fos');

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            
             $resultOrderList= DB::select("SELECT e.order_id,date(e.order_date) AS order_date,e.order_no,tbl_user_details.first_name,tbl_retailer.name AS rname,e.fo_id,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,sum(e.free_delivery_qty) AS delQty FROM (
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_and_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_regular_and_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor group by order_id  HAVING total_free_qty < free_delivery_qty
            ) AS e 
            join tbl_user_details on tbl_user_details.user_id=e.fo_id
            join tbl_retailer on tbl_retailer.retailer_id=e.retailer_id
            group by e.order_id ORDER BY e.order_id");


        }
        else
        {

             $resultOrderList= DB::select("SELECT e.order_id,date(e.order_date) AS order_date,e.order_no,tbl_user_details.first_name,tbl_retailer.name AS rname,e.fo_id,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,sum(e.free_delivery_qty) AS delQty FROM (
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor AND fo_id=$foid group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_special_and_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor AND fo_id=$foid group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor AND fo_id=$foid group by order_id  HAVING total_free_qty < free_delivery_qty
            UNION ALL
            SELECT order_id,order_date,order_no,fo_id,retailer_id,catid,sum(total_free_qty) AS total_free_qty,total_free_value,sum(free_delivery_qty) AS free_delivery_qty FROM tbl_order_regular_and_free_qty WHERE date(order_date) BETWEEN '$fromdate' AND '$todate' AND delivery_date !='' AND distributor_id= $distributor AND fo_id=$foid group by order_id  HAVING total_free_qty < free_delivery_qty
            ) AS e 
            join tbl_user_details on tbl_user_details.user_id=e.fo_id
            join tbl_retailer on tbl_retailer.retailer_id=e.retailer_id
            group by e.order_id ORDER BY e.order_id");
           
        }
        
        return view('sales/freePending/ordersList', compact('resultOrderList'));
    }

    public function ssg_free_pending_edit($orderMainId,$foMainId)
    {

        $selectedMenu   = 'Free Pending';                    // Required Variable
        $pageTitle      = 'Order Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname')
                        
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

       

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.offer_type','tbl_order.order_status','users.id','users.display_name','tbl_order.global_company_id','tbl_order.order_id','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.point_id','tbl_order.route_id','order_no','order_date','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')

        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
        ->where('tbl_order.order_type','Delivered')
        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
        ->where('tbl_order.fo_id',$foMainId)                        
        ->where('tbl_order.order_id',$orderMainId)
        ->first();


        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        
        

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->groupBy('order_id')                              
                        ->first();

        /////////////////// IF OFFER CHECKED ANY /////////////////////

        $regularOffersCheck = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $specialOffersCheck = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

       
        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.free_id','osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $resultCategory = DB::table('tbl_product_category')
                            ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                            ->where('status', '0')
                            ->where('gid', Auth::user()->business_type_id)
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->get();
        
        

        return view('sales/freePending/bucketEdit', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','specialOffers','regularOffersCheck','specialOffersCheck','resultCategory'));
    }

   
    public function ssg_free_pending_edit_submit(Request $request)
    { 
        //dd($request->all());

        if($request->get('statusProcess')==1)  // confirm order
        {
            $lastOrderId  = $request->get('orderid');

            $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();
            //dd($lastOrderId);


            $orderNo = $order_no->order_no;
            $autoOrderId = $order_no->auto_order_no;
            $distributorID = $order_no->distributor_id;
            $foID = $order_no->fo_id;
            $pointID = $order_no->point_id;
            $routeID = $order_no->route_id;
            $retailderID = $order_no->retailer_id;
            $orderDate = $order_no->order_date;

            $countFreeRows    = count($request->get('free_id'));
            $andCountFreeRows = count($request->get('and_free_id'));

            //  Sharif offer edit option
            if($request->get('offer_type') == 'exclusive')
            {
                for($i=0;$i<$countFreeRows;$i++)
                {
                    if($request->get('free_id')[$i]!='')
                    {
                      $totalFreePrice = $request->get('freeqty')[$i] * $request->get('change_prod_price')[$i];
                      
                        DB::table('tbl_order_special_free_qty')->insert(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => $orderDate,
                            'slab'                  => $request->get('slab')[$i],
                            'slab_count'            => 1,
                            'catid'                 => $request->get('catid')[$i],
                            'delivery_catid'        => $request->get('change_cat_id')[$i],
                            'product_id'            => $request->get('change_product_id')[$i],
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => $foID,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $request->get('freeqty')[$i],
                            'free_value'            => $totalFreePrice,
                            'total_free_value'      => $totalFreePrice,
                            'delivery_date'         => date('Y-m-d H:i:s'),
                            'ref_id'                => $request->get('free_id')[$i],
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost(),
                            'status'                => 0
                            ]
                            );

                    }
                }
            // Special free and products
                if($andCountFreeRows>0){
                    for($j=0;$j<$andCountFreeRows;$j++)
                    {
                        if($request->get('and_free_id')[$j]!='')
                        {
                         $totalFreePrice = $request->get('andFreeQty')[$j] * $request->get('and_free_value')[$j];
                          
                          DB::table('tbl_order_special_and_free_qty')->insert(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => $orderDate,
                            'slab'                  => $request->get('slab')[$j],
                            'slab_count'            => 1,
                            'catid'                 => $request->get('catid')[$i],
                            'product_id'            => $request->get('and_free_pid')[$j],
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => $foID,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $request->get('andFreeQty')[$j],
                            'free_value'            => $totalFreePrice,
                            'total_free_value'      => $totalFreePrice,
                            'delivery_date'         => date('Y-m-d H:i:s'),
                            'ref_id'                => $request->get('and_free_id')[$j],
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost()
                            ]
                            );

                        }
                    }
                }
            }


            if(sizeof($order_no)>0)
            {
                $retailer_info = array();
                $retailer_info['trans_type'] = 'free'; 
                $retailer_info['accounts_type'] = 'expense';
                $retailer_info['retailer_id'] = $retailderID;
                $retailer_info['order_id'] = $lastOrderId;
                $retailer_info['invoice_no'] = $orderNo;
                $retailer_info['point_id'] = $pointID;
                
                $this->reatiler_free_credit_ledger($retailer_info);
            }  

            $this->sync_offer_stock($request->get('orderid'), 'freepending');
        
           
            return Redirect::to('/free-pending')->with('success', 'Successfully Confirm Delivery Done.');
        }
    }


    private function reatiler_free_credit_ledger($retailer_info = array())
    {
        if(is_array($retailer_info))
        {
            $credit_ledger_Data = array();
            $credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
            $credit_ledger_Data['point_id'] = $retailer_info['point_id'];
            $credit_ledger_Data['collection_id'] = 0;
            $credit_ledger_Data['trans_type'] = $retailer_info['trans_type'];
            $credit_ledger_Data['accounts_type'] = $retailer_info['accounts_type'];
            $credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s');
            
            /////////////////////////////// Retailer Credit Balance ////////////////////////////////
            
            $retailerLedger = DB::select("SELECT * FROM retailer_credit_ledger WHERE retailer_id = '".$retailer_info['retailer_id']."'
                            ORDER BY 1 DESC LIMIT 1");
                            
            ##opening balance
            if(sizeof($retailerLedger)>0)
            {
                $retOpeningBalance = $retailerLedger[0]->retailer_balance;
            } else {
                $retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$retailer_info['retailer_id']."'");
                $retOpeningBalance = $retailerData[0]->opening_balance;
            }
            
            $credit_ledger_Data['retailer_opening_balance'] = $retOpeningBalance;

            ## invoice No & sales
            $credit_ledger_Data['retailer_invoice_no'] = $retailer_info['invoice_no'];          
            
          
            $rowRet = DB::select("SELECT e.order_id,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,sum(e.free_delivery_qty) AS delQty,sum(e.free_delivery_value) AS delValue FROM (
            SELECT order_id,sum(total_free_qty) AS total_free_qty, sum(free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_special_free_qty WHERE order_id='".$retailer_info['order_id']."'
            UNION ALL
            SELECT order_id,sum(total_free_qty) AS total_free_qty, sum(free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_special_and_free_qty WHERE order_id='".$retailer_info['order_id']."'
            UNION ALL
            SELECT order_id,sum(total_free_qty) AS total_free_qty, sum(free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_free_qty WHERE order_id='".$retailer_info['order_id']."'
            UNION ALL
            SELECT order_id,sum(total_free_qty) AS total_free_qty, sum(free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_regular_and_free_qty WHERE order_id='".$retailer_info['order_id']."'
            ) AS e 
            group by e.order_id");


            $retInVoiceSales = $rowRet[1]->value - $rowRet[1]->delValue;
            
            $credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;           
            
            ##totalCollection
            $retCollect = 0;
            $credit_ledger_Data['retailer_collection'] = $retCollect;
            
            
            ##retailerBalance
            $remBalance = ($retOpeningBalance + $retInVoiceSales) - $retCollect;
            
            $credit_ledger_Data['retailer_balance'] = $remBalance;
            
            $credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
            $credit_ledger_Data['entry_by'] = Auth::user()->id;
            
            
            DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
            
        }   
    }
	
	
	// Order delivery for Exception


    public function ssg_order_exceptional()
    {
        $selectedMenu   = 'Order';             // Required Variable
        $pageTitle      = 'Order';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                       
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $todate          = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales/distributorExceptional/order', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_order_list_exceptional(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
						
			 $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
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
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales/distributorExceptional/ordersList', compact('resultOrderList'));
    }


    public function ssg_order_edit_exceptional($orderMainId,$foMainId,$orderPart)
    {
        
        $selectedMenu   = 'Order';                    // Required Variable
        $pageTitle      = 'Order Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_det_status','tbl_product_category.id AS catid','tbl_product_category.name AS catname')
                        
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPart)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultCartOffer  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_det_status','tbl_product_category.id AS catid','tbl_product_category.name AS catname')
                        
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPart)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        //dd($resultCartPro);

         $orderTypePartial  = DB::table('tbl_order_details')
                        ->select('order_det_status','partial_order_id')                        
                        ->where('order_id',$orderMainId)
                        ->where('partial_order_id',$orderPart)        
                        ->first();

        //dd($orderTypePartial);

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.offer_type','tbl_order.order_status','users.id','users.display_name','tbl_order.global_company_id','tbl_order.order_id','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.point_id','tbl_order.route_id','order_no','order_date','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile','tbl_order.order_count','tbl_order.grand_total_value')
        ->join('users', 'users.id', '=', 'tbl_order.fo_id')
        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
        ->where('tbl_order.order_type','Confirmed')
        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
        ->where('tbl_order.fo_id',$foMainId)                        
        ->where('tbl_order.order_id',$orderMainId)
        ->first();

        //dd($resultInvoice);


        // for offers

        // for FO Information

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        //dd($resultFoInfo);

        //$point_id       = $resultFoInfo->point_id;
        $division_id    = $resultFoInfo->division_id;

        $currentDay = date('Y-m-d');
        
                        /************************* Start Bundle Offer ********************************/             
                                    
       
        
        // for offer related start

        $resultBundleOffers = DB::table('tbl_bundle_offer')
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->where('iBusinessType', Auth::user()->business_type_id)
                            ->where('iOfferType', 3)
                            ->orderBy('iId', 'DESC')
                            ->first();

        //dd($resultBundleOffers);

        $resultBundleOffersCategories = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_bundle_category.offerId','tbl_bundle_category.categoryId')
                            ->leftJoin('tbl_bundle_category','tbl_bundle_offer.iId','=','tbl_bundle_category.offerId')
                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                            ->get();

        $allowCategory = array();
        foreach ($resultBundleOffersCategories as $value) 
        {
            $allowCategory[] = $value->categoryId;
        }


        $resultBundleOffersGift = DB::table('tbl_order_gift')
                                //->where('retailer_id', $retailderid)
                                ->where('retailer_id', $resultInvoice->retailer_id)
                                ->where('global_company_id', Auth::user()->global_company_id)
                                //->where('distributor_id',Auth::user()->id)
                                ->where('orderid',$resultInvoice->order_id)
                                ->first();

        $offerid = '';
        if(sizeof($resultBundleOffersGift)>0)
        {
            $offerid = $resultBundleOffersGift->offerid;
        }

        $resultBundleOfferType = DB::table('tbl_bundle_products')->select('offerId','productType')
                                ->where('offerId', $offerid)                                
                                ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.offerId', $offerid)
                                //->where('og.retailer_id', $retailderid)
                                ->where('og.retailer_id', $resultInvoice->retailer_id)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                //->where('og.distributor_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            
            $resultBundleSelectedGift = DB::table('tbl_order_gift')
                                    ->where('orderid',$resultInvoice->order_id)
                                    ->first();                                    

            $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->select('tbl_bundle_product_details.*','tbl_product.id','tbl_product.name','tbl_product.depo')
                                    ->leftJoin('tbl_product','tbl_bundle_product_details.giftName','=','tbl_product.id')
                                    ->where('offerId',$resultBundleSelectedGift->offerid)
                                    ->where('slabId',$resultBundleSelectedGift->slab_id)
                                    ->where('groupid',$resultBundleSelectedGift->groupid)
                                    ->get();


        }
        

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->groupBy('order_id')                              
                        ->first();

        /////////////////// IF OFFER CHECKED ANY /////////////////////

        $regularOffersCheck = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $specialOffersCheck = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderMainId)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $bundleOffersCheck = DB::table('tbl_order_gift')
                        ->select('orderid')
                        ->where('status', 1) // 0 for added
                        ->where('orderid', $orderMainId)
                        ->groupBy('orderid')                              
                        ->count();


        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.free_id','osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)            
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)   
                        ->where('is_point_wise', 0)                       
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)   
                        ->where('is_point_wise', 1)                       
                        ->get();
                        
        ////////// Offer check /////////////

        $checkExclusiveAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->whereNull('status')  //added items
                    ->count();

        $checkExclusiveAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->where('status', 0)  //added items
                    ->count();


        $checkRegularAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->whereNull('status')  //added items
                    ->count();

        $checkRegularAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $orderMainId)
                    ->where('status', 0)  //added items
                    ->count();


        $today = date('Y-m-d');            
        $resultBundle = DB::table('tbl_bundle_offer')
                        ->select('iId','vOfferName','dBeginDate','dEndDate','iStatus')
                        ->where('iOfferType',3)
                        ->where('iStatus',1)
                        ->whereDate('dEndDate','>=',$today)
                        ->first();
                                    
        $offerId = '';
        if(sizeof($resultBundle)>0)
        {
            $offerId = $resultBundle->iId;
        }               
        

        $netAmount = $resultInvoice->grand_total_value + 0;       

         /// Exceptional products group

     $bundleCategoriesEx  = DB::table('tbl_bundle_except_category')
            ->select('categoryId')
            ->where('status',0)
            ->where('global_company_id',Auth::user()->global_company_id)
            ->get();

            $data = collect($bundleCategoriesEx)->map(function($x){ return (array) $x; })->toArray();


        $checkDelivery = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
        ->where('order_id', $orderMainId) 
        ->first();
        

        if(sizeof($checkDelivery)>0 && $checkDelivery->totalValue >0) // Delivery Value Available
        {
            $resultBundleVlue  = DB::table('tbl_order_details')
                ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
                ->where('order_id', $orderMainId)  
                ->whereNotIn('cat_id', $data) 
                ->first();
        }
        else
        {
            $resultBundleVlue  = DB::table('tbl_order_details')
                ->select('order_id','cat_id',DB::raw('SUM(p_total_price) AS totalValue'))
                ->where('order_id', $orderMainId)  
                ->whereNotIn('cat_id', $data) 
                ->first();
        }
        
        //dd($resultBundleVlue);

        $netBundleValue = 0;
        if(sizeof($resultBundleVlue)>0)
        {
            $netBundleValue = $resultBundleVlue->totalValue;
        }

        //dd($netBundleValue);         

        $resultOfferRange = DB::select("SELECT * FROM tbl_bundle_slab WHERE iOfferId='$offerId' AND '$netBundleValue' BETWEEN iMinRange AND iMaxRange");

        $offerRangeId=''; 
        if(sizeof($resultOfferRange)>0)
        {
            $offerRangeId = $resultOfferRange[0]->iId;
        }

        
/// end Exceptional products group
        $resultBundleAvailableItems = DB::table('tbl_bundle_product_details')
                        ->where('offerId', $offerId) 
                        ->where('slabId', $offerRangeId) 
                        ->count();

        $resultBundleAddedItems = DB::table('tbl_order_gift')
                        ->where('orderid', $orderMainId)
                        ->count();


        //dd($resultBundleAvailableItems,$resultBundleAddedItems,$resultBundleVlue);
        

        return view('sales/distributorExceptional/bucketEdit', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffers','resultBundleOffersGift','specialOffers','regularOffersCheck','specialOffersCheck','bundleOffersCheck','commissionWiseItem','specialValueWise','orderTypePartial','pointValueWise','checkExclusiveAvailableItems','checkExclusiveAdded','checkRegularAvailableItems','checkRegularAdded','resultBundleAvailableItems','resultBundleAddedItems'));
    }


    
    public function ssg_order_edit_submit_exceptional(Request $request)
    { 
        //dd($request->all());

        if($request->get('statusProcess')==1)  // confirm order
        {
            $checkDeliveryStatus = DB::table('tbl_order_details')
            ->where('order_id', $request->get('orderid'))
            ->where('partial_order_id', $request->get('openOrderType'))
            ->where('order_det_type', 'Delivered')
            ->count();

            //dd($checkDeliveryStatus);

            if($checkDeliveryStatus==0)
            {
                
            $lastOrderId  = $request->get('orderid');
            $countRows    = count($request->get('qty'));

            // NEW OPTION VALUE WISE COMMISSION       

            $mTotalPrice=0;
            $mTotalQty=0;

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $mTotalPrice += $request->get('qty')[$m] * $request->get('price')[$m];
                    $mTotalQty += $request->get('qty')[$m];
                }
            }

            if($request->get('orderTypePartial')=='Closed')
            {
                $previousBalanceRetailer = DB::table('tbl_retailer')
                            ->select('reminding_commission_balance')
                            ->where('retailer_id', $request->get('retailderid'))
                            ->first();

                $previousBalance = 0;
                if(sizeof($previousBalanceRetailer)>0)
                {
                    $previousBalance = $previousBalanceRetailer->reminding_commission_balance;
                }

                $currentBalanceRetailer = $previousBalance + $request->get('totalFreeValueWiseCommissionBalance');             

                $buyAmount = $request->get('totalFreeValue') - $request->get('totalFreeValueWiseCommissionBalance');

                DB::table('tbl_retailer')->where('retailer_id', $request->get('retailderid'))->update(
                    [
                        'reminding_commission_balance' => $currentBalanceRetailer
                    ]
                );

                DB::table('tbl_retailer_commission_history')->insert(
                    [
                        'orderid'                   => $lastOrderId,
                        'retailerid'                => $request->get('retailderid'),
                        'distributorid'             => Auth::user()->id,
                        'foid'                      => $request->get('foMainId'),
                        'retailer_order_balance'    => $mTotalPrice,
                        'total_commission_amount'   => $request->get('totalFreeValue'),
                        'total_buy_commission_amount' => $buyAmount,
                        'balance_amount'            => $request->get('totalFreeValueWiseCommissionBalance'), 
                        'routeid'                   => $request->get('routeid'), 
                        'pointid'                   => $request->get('pointid'), 
                        'date'                      => date('Y-m-d')
                    ]
                ); 
            }          


            if($request->get('orderTypePartial')=='Closed')
            {
                $order_type = 'Delivered';
            }
            else
            {
                $order_type = 'Confirmed';
            }

			
			
             DB::table('tbl_order')->where('order_id', $request->get('orderid'))

                    ->where('global_company_id', Auth::user()->global_company_id)->update(
                    [
                        'order_type'             => $order_type,
                        'total_delivery_qty'     => DB::raw("total_delivery_qty + $mTotalQty"), 	//$mTotalQty,   //bug resolved by zubair may-14-2019
                        'total_delivery_value'   => DB::raw("total_delivery_value + $mTotalPrice"), //$mTotalPrice, //bug resolved by zubair may-14-2019
                        'grand_total_value'      => DB::raw("grand_total_value + $mTotalPrice"), 	//$mTotalPrice, //bug resolved by zubair may-14-2019
                        'update_date'            => date('Y-m-d H:i:s')
                    ]
                );
			
			
			/*	
			DB::update("UPDATE tbl_order SET order_type= '".$order_type."',  
															   total_delivery_qty = total_delivery_qty + $mTotalQty, 
															   total_delivery_value = total_delivery_value + $mTotalPrice,
															   grand_total_value = grand_total_value + $mTotalPrice,
															   update_date = '".date('Y-m-d H:i:s')."'
															   Where order_id = ?", ["$request->get('orderid')"]);	
			*/												   
                
            $srl_no = explode('_',$request->get('openOrderType'));

            $autoAdd   = $request->get('orderid');
            $chalan_no = 'CHN-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;

            //$chalan_no = 'CHN-' . date('Ymd') . $srl_no[1];   

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='' OR $request->get('replaceDelivery')[$m]!='')  /* bug resolve by zubair Dec_08_2018 */   
                {
                    $checkItemsExiting = DB::table('tbl_order_details')
                                    ->where('order_id', $lastOrderId)
                                    ->where('product_id',$request->get('product_id')[$m])
                                    ->where('partial_order_id',$request->get('openOrderType'))
                                    ->first();

                    if(sizeof($checkItemsExiting)>0)
                    {
                        $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                        DB::table('tbl_order_details')
                        ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                        ->where('product_id',$request->get('product_id')[$m])
                        ->where('partial_order_id',$request->get('openOrderType'))
                        ->update(
                            [
                                'replace_delivered_qty'   => $request->get('replaceDelivery')[$m],
                                'delivered_qty'           => $request->get('qty')[$m],
                                'delivered_value'         => $totalPrice,
                                'is_delivered'            => 'YES',
                                'delivered_date'          => date('Y-m-d H:i:s'),
                                'delivery_challan'        => $chalan_no,
                                'order_det_type'          => 'Delivered'
                            ]
                        );
                    } 
                }
            } 

            $countFreeRows    = count($request->get('free_id'));
            $andCountFreeRows = count($request->get('and_free_id'));

            //  Sharif offer edit option
            if($request->get('offer_type') == 'exclusive')
            {
                for($i=0;$i<$countFreeRows;$i++)
                {
                    if($request->get('free_id')[$i]!='')
                    {
                      $totalFreePrice = $request->get('freeqty')[$i] * $request->get('free_value')[$i];
                      

                        DB::table('tbl_order_special_free_qty')
                        ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                        ->where('free_id',$request->get('free_id')[$i])
                        ->where('product_id',$request->get('free_product_id')[$i])
                        ->update(
                            [
                                'total_free_qty'              => $request->get('freeqty')[$i],
                                'free_value'                  => $totalFreePrice,
                                'free_delivery_qty'           => $request->get('total_free_qty')[$i],
                                'free_delivery_value'         => $request->get('total_free_value')[$i]
                            ]
                        );
                    }
                }
            // Special free and products
                if($andCountFreeRows>0){
                    for($j=0;$j<$andCountFreeRows;$j++)
                    {
                        if($request->get('and_free_id')[$j]!='')
                        {
                         $totalFreePrice = $request->get('andFreeQty')[$j] * $request->get('and_free_value')[$j];
                          

                            DB::table('tbl_order_special_and_free_qty')
                            ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                            ->where('free_id',$request->get('and_free_id')[$j])
                            ->where('product_id',$request->get('and_free_pid')[$j])
                            ->update(
                                [
                                    'total_free_qty'              => $request->get('andFreeQty')[$j],
                                    'free_value'                  => $totalFreePrice,
                                    'free_delivery_qty'           => $request->get('and_free_qty')[$j],
                                    'free_delivery_value'         => $request->get('and_free_value')[$j]
                                ]
                            );
                        }
                    }
                }
            }

            if($request->get('offer_type') == 'regular')
            {
                for($i=0;$i<$countFreeRows;$i++)
                {
                    if($request->get('free_id')[$i]!='')
                    {
                      $totalFreePrice = $request->get('freeqty')[$i] * $request->get('free_value')[$i];
                    
                      
                        DB::table('tbl_order_free_qty')
                        ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                        ->where('free_id',$request->get('free_id')[$i])
                        ->where('product_id',$request->get('free_product_id')[$i])
                        ->update(
						[
                                'total_free_qty'              => $request->get('freeqty')[$i],
                                'free_value'                  => $totalFreePrice,
                                'free_delivery_qty'           => $request->get('total_free_qty')[$i],
                                'free_delivery_value'         => $request->get('free_value')[$i]
                            ]
                        );
                    }
                }

                // Regular free and products
                if($andCountFreeRows>0){
                    for($j=0;$j<$andCountFreeRows;$j++)
                    {
                        if($request->get('and_free_id')[$j]!='')
                        {
                         $totalFreePrice = $request->get('andFreeQty')[$j] * $request->get('and_free_value')[$j];
                         

                            DB::table('tbl_order_regular_and_free_qty')
                            ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                            ->where('free_id',$request->get('and_free_id')[$j])
                            ->where('product_id',$request->get('and_free_pid')[$j])
                            ->update(
                                [
                                    'total_free_qty'              => $request->get('andFreeQty')[$j],
                                    'free_value'                  => $totalFreePrice,
                                    'free_delivery_qty'           => $request->get('and_free_qty')[$j],
                                    'free_delivery_value'         => $request->get('and_free_value')[$j]
                                ]
                            );
                        }
                    }
                }
            }
            
            //zubair retailer balance call June-11-2018
            $orderData = DB::table('tbl_order')
                    ->select('tbl_order.*')
                    ->where('order_id', $request->get('orderid'))
                    ->first();
    
            if(sizeof($orderData)>0)
            {
                $retailer_info = array();
                $retailer_info['trans_type'] = 'sales'; 
                $retailer_info['accounts_type'] = 'expense';
                $retailer_info['order_id'] = $request->get('orderid');
                $retailer_info['partial_order_id'] = $request->get('openOrderType');
                $retailer_info['retailer_id'] = $orderData->retailer_id;
                $retailer_info['invoice_no'] = $orderData->order_no;
                $retailer_info['point_id'] = $orderData->point_id;
				
				$retailer_info['order_id'] = $orderData->order_id;
				$retailer_info['partial_type'] = $request->get('openOrderType');
                
                $this->reatiler_credit_ledger($retailer_info);
            }   

            $this->sync_offer_stock($request->get('orderid'), 'Delivered', $request->get('openOrderType'));
        
            if($request->get('offer_type') == 'exclusive')
            {
                DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->update(
                    [
                        'delivery_date'            => date('Y-m-d H:i:s')
                    ]
                );
                 DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->update(
                    [
                        'delivery_date'            => date('Y-m-d H:i:s')
                    ]
                );
                // clear regular  offer     
                DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
                DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                 // clear bundle  offer     
               DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
                
                $this->sync_offer_stock($request->get('orderid'),$request->get('offer_type'));
            }

            if($request->get('offer_type') == 'regular')
            {
                DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->update(
                    [
                        'delivery_date'            => date('Y-m-d H:i:s')
                    ]
                );
                 DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->update(
                    [
                        'delivery_date'            => date('Y-m-d H:i:s')
                    ]
                );
                // clear special  offer 
                DB::table('tbl_order_special_free_qty')->where('status','!=',3)->where('order_id',$request->get('orderid'))->delete();
                DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                 // clear bundle  offer     
               DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
                
                $this->sync_offer_stock($request->get('orderid'), $request->get('offer_type'));
            }
            
            
            if($request->get('offer_type') != '' AND $request->get('offer_type') != 'regular' 
                AND $request->get('offer_type') != 'exclusive')
            {

              // clear regular offer
                DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
                DB::table('tbl_order_regular_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
              // clear special offer    
                DB::table('tbl_order_special_free_qty')->where('status','!=',3)->where('order_id',$request->get('orderid'))->delete();
              // clear specialand  offer    
               DB::table('tbl_order_special_and_free_qty')->where('order_id',$request->get('orderid'))->delete();
                
                $this->sync_offer_stock($request->get('orderid'), 'bundle');
            }

            DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->where('status',3)->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );

            return Redirect::to('/order-exceptional')->with('success', 'Successfully Confirm Delivery Done.');
            }
            else
            {
                return Redirect::to('/order-exceptional')->with('unsuccess', 'Sorry, this order already delivered.');
            }  
        }
        else
        {

        $lastOrderId    = $request->get('orderid');

        $countRows = count($request->get('qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $mTotalPrice += $request->get('qty')[$m] * $request->get('price')[$m];
                $mTotalQty += $request->get('qty')[$m];
            }
        }            

       DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', $request->get('foMainId'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_type'             => 'Confirmed',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'grand_total_value'      => $mTotalPrice,
                'update_date'            => date('Y-m-d H:i:s')
            ]
        );

        
        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('tbl_order_details')
                                ->where('order_id', $lastOrderId)
                                ->where('product_id',$request->get('product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('tbl_order_details')
                    ->where('order_id', $lastOrderId)  // bug fixing by zubair June-02-2018
                    ->where('product_id',$request->get('product_id')[$m])
                    ->update(
                        [
                            'replace_delivered_qty'   => $request->get('replaceDelivery')[$m],
                            'delivered_qty'           => $request->get('qty')[$m],
                            'delivered_value'         => $totalPrice
                        ]
                    );
                } 

            }

        }

        //Sharif Offer Starts----------------->//
        //dd($request->get('offer_type'));

        

                $itemsDelete = DB::table('tbl_order_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();

                $order_sku = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','product_id')
                                    ->where('order_id', $lastOrderId)
                                    ->get();


                $special_sku = array();
                foreach($order_sku as $order_sku_id) {
                    $special_sku[]= $order_sku_id->product_id;
                }

                $checkRegularSkuProducts =  DB::table('tbl_regular_sku_products')
                                        ->select('slab','catid','sku_id','qty','value')
                                        ->whereIn('sku_id', $special_sku)
                                        ->groupBy('sku_id')
                                        ->get();
               

                $skuid = array();

                foreach($checkRegularSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }


                $offerGroupType = DB::table('tbl_order_details')
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('cat_id')
                                    ->get();


                $textGroup =0;
                if(sizeof($offerGroupType)>0)
                {
                    foreach ($offerGroupType as $value) 
                    {
                        if($value->offer_group_type==1)
                        {
                            // separate

                            $totalCatQty = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereNotIn('product_id', $skuid)
                                    ->where('cat_id', $value->cat_id)
                                    //->groupBy('cat_id')
                                    ->get();



                            $textGroup1 = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','offer_group_type')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('offer_group_type')
                                    ->first();

                            $textGroup = $textGroup1->offer_group_type;
                        }
                        else
                        {
                            //combine

                            $totalCatQty = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('cat_id')
                                    ->get();



                        }

                        foreach ($totalCatQty as $catQty) 
                        {
                           
                            $totalQty = $catQty->total;
                            $catid = $catQty->cat_id;
                            $orderNo = $order_no->order_no;
                            $autoOrderId = $order_no->auto_order_no;
                            $distributorID = $order_no->distributor_id;
                            $foID = $order_no->fo_id;
                            $pointID = $order_no->point_id;
                            $routeID = $order_no->route_id;
                            $retailderID = $order_no->retailer_id;
                            $total_odd=0;
                            $total_odd1=0;
                            $total_odd2 = 0;                    

                            $regularProducts =  DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab',$totalQty)
                                               ->where('status',0)
                                               ->get();

                 
                            if(sizeof($regularProducts) >0 )
                            {
                        
                                foreach($regularProducts as $regularProducts) {
                                
                                    $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularProducts->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularProducts->pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularProducts->qty,
                                            'free_value'           => $regularProducts->value,
                                            'total_free_value'     => $regularProducts->value * $regularProducts->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                    
                                    if($regularProducts->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularProducts->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularProducts->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularProducts->and_qty,
                                            'free_value'            => $regularProducts->and_value,
                                            'total_free_value'      => $regularProducts->value * $regularProducts->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                    }
                                
                                }
                            }
                            else
                            {
                                $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<$totalQty");


                                $maxSlab = DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab', $maxValue[0]->slab)
                                               ->where('status',0)
                                               ->get();

                                

                                 //dd($maxSlab);

                                if(sizeof($maxSlab) >0 )
                                {
                                    foreach($maxSlab as $maxSlab) {
                                    
                                        $mainQty = (int)($totalQty/$maxSlab->slab);
                                       //dd($mainQty);
                                        $total_odd = $totalQty - ($mainQty * $maxSlab->slab);
                                        $total_free = $maxSlab->qty * $mainQty;
                                        $total_value = $maxSlab->value * $total_free;
                                        $and_total_free = $maxSlab->and_qty * $mainQty;
                                        $and_total_value = $maxSlab->and_value * $and_total_free;
                                    

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $maxSlab->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $catid,
                                                'product_id'            => $maxSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $maxSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );
                                        
                                        if($maxSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'product_id'            => $maxSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $maxSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                                $lastSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd)");

                                
                                
                                if(sizeof($lastSlab) >0 )
                                {
                                     foreach($lastSlab as $lastSlab) {

                                        $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                        $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                        //dd($total_odd1);
                                        $total_free = $lastSlab->qty * $mainQty1;
                                        $total_value = $lastSlab->value * $total_free;
                                        $and_total_free = $lastSlab->and_qty * $mainQty1;
                                        $and_total_value = $lastSlab->and_value * $and_total_free;

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastSlab->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                               $lastOddSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd1)");
                                
                                if(sizeof($lastOddSlab) >0 )
                                {
                                  
                                   foreach($lastOddSlab as $lastOddSlab) {

                                        $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                        $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                        $total_free = $lastOddSlab->qty * $mainQty2;
                                        $total_value = $lastOddSlab->value * $total_free;
                                        $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;


                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastOddSlab->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }
                                
                                
                                $lastOddSlab2 =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab2) >0 )
                                {
                                  
                                   foreach($lastOddSlab2 as $lastOddSlab2) {

                                        $mainQty3 = (int)($total_odd1/$lastOddSlab2->slab);
                                        $total_free = $lastOddSlab2->qty * $mainQty3;
                                        $total_value = $lastOddSlab2->value * $total_free;
                                        $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab2->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab2->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab2->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $catid,
                                                'product_id'            => $lastOddSlab2->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $and_total_free,
                                                'free_value'            => $lastOddSlab2->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }
                            }            
                 
                            //Sharif Offer Ends-------------------->//  
                        }

                        if($textGroup==1) 
                        {     

                            foreach($checkRegularSkuProducts as $sku) {
                            
                                $totalSkuQty =  DB::table('tbl_order_details')
                                                ->where('order_id',$lastOrderId)
                                                ->where('product_id', $sku->sku_id)
                                                ->sum('delivered_qty');
                                                
                                $regularSku =  DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                ->where('sku_id',$sku->sku_id)
                                ->where('slab',$totalSkuQty)
                                ->where('status',0)                       
                                ->get();


                                if(sizeof($regularSku) >0 )
                                {

                                    foreach($regularSku as $regularSku) {
                                    
                                         $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularSku->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'product_id'            => $regularSku->pid,
                                            'sku_id'                => $regularSku->sku_id,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularSku->qty,
                                            'free_value'            => $regularSku->value,
                                            'total_free_value'      => $regularSku->value * $regularSku->qty,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        if($regularSku->and_qty>0){

                                          DB::table('tbl_order_regular_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $regular_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $regularSku->slab,
                                            'slab_count'            => 1,
                                            'catid'                 => $catid,
                                            'sku_id'                => $regularSku->sku_id,
                                            'product_id'            => $regularSku->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $regularSku->and_qty,
                                            'free_value'            => $regularSku->and_value,
                                            'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }


                                    }


                                }
                                else
                                {
                                    $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                                    $maxSlabSku = DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                                   ->where('catid',$catid)
                                                   ->where('sku_id',$sku->sku_id)
                                                   ->where('slab', $maxValueSku[0]->slab)
                                                   ->where('status',0)                       
                                                   ->get();

                                    if(sizeof($maxSlabSku) >0 )
                                    {

                                        foreach($maxSlabSku as $maxSlabSku) {

                                            $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                                           //dd($mainQty);
                                            $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                            $total_free = $maxSlabSku->qty * $mainQty;
                                            $total_value = $maxSlabSku->value * $total_free;
                                            $and_total_free = $maxSlabSku->and_qty * $mainQty;
                                            $and_total_value = $maxSlabSku->and_value * $and_total_free;
                                        

                                           $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $maxSlabSku->pid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $maxSlabSku->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($maxSlabSku->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $regularSku->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $catid,
                                                'sku_id'                => $regularSku->sku_id,
                                                'product_id'            => $regularSku->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $regularSku->and_qty,
                                                'free_value'            => $regularSku->and_value,
                                                'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }

                                        }
                                      
                                    }

                                    $lastSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                                    if(sizeof($lastSlabSku) >0 )
                                    {

                                         foreach($lastSlabSku as $lastSlab) {

                                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                            $total_free = $lastSlab->qty * $mainQty1;
                                            $total_value = $lastSlab->value * $total_free;

                                           $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastSlab->pid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );


                                            if($lastSlab->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $catid,
                                                'sku_id'                => $lastSlab->sku_id,
                                                'product_id'            => $lastSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $lastSlab->and_qty,
                                                'free_value'            => $lastSlab->and_value,
                                                'total_free_value'      => $lastSlab->and_qty * $lastSlab->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }

                                   $lastOddSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                                    if(sizeof($lastOddSlabSku) >0 )
                                    {
                                      
                                       foreach($lastOddSlabSku as $lastOddSlab) {

                                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                            $total_free = $lastOddSlab->qty * $mainQty2;
                                            $total_value = $lastOddSlab->value * $total_free;

                                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastOddSlab->pid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastOddSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastOddSlab->and_qty>0){

                                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                                [
                                                'order_id'              => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $regular_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $catid,
                                                'sku_id'                => $lastOddSlab->sku_id,
                                                'product_id'            => $lastOddSlab->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $lastOddSlab->and_qty,
                                                'free_value'            => $lastOddSlab->and_value,
                                                'total_free_value'      => $lastOddSlab->and_qty * $lastOddSlab->and_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }
                                } // SKU else close
                            }


                              ///////////////// SPECIAL OFFER START //////////////////
                        }

                    }                    
                }                  
                                   

                
            // for FO Information

            $resultFoInfo   = DB::table('users')
                            ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                             ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                             ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                             ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                             ->where('tbl_user_type.user_type_id', 5)
                             ->where('users.id', Auth::user()->id)
                             ->where('users.is_active', 0) // 0 for active
                             ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                             ->first();

            //dd($resultFoInfo);
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
            $resultSpecialOffers = 0;
            $point = 0;
            $route = 0;

            $currentDay = date('Y-m-d');

            $resultSpecialOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='2'
             AND tbl_bundle_offer_scope.iDivId='$division_id' 
             AND '$currentDay' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultSpecialOffers)>0)
            {           

                $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                     AND iPointId='$point_id'
                     "); 


                $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                         AND iPointId='".$request->get('point_id')."' AND iRouteId = '".$request->get('route_id')."'
                         ");
            }

            if(sizeof($resultSpecialOffers)>0 || sizeof($point)>0 || sizeof($route)>0)
            {


                $specialDelete = DB::table('tbl_order_special_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $andspecialDelete = DB::table('tbl_order_special_and_free_qty')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $commissionDelete = DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->delete();

                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();

                $orderNo = $order_no->order_no;
                $autoOrderId = $order_no->auto_order_no;
                $distributorID = $order_no->distributor_id;
                $foID = $order_no->fo_id;
                $pointID = $order_no->point_id;
                $routeID = $order_no->route_id;
                $retailderID = $order_no->retailer_id;

                
                $skuid = array();
                $checkSkuProducts =  DB::table('tbl_special_sku_products')
                                            ->select('slab','catid','sku_id','qty','value')
                                            ->whereIn('sku_id', $special_sku)
                                            ->where('status',0) 
                                            ->groupBy('sku_id')
                                            ->get();
                

                foreach($checkSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }

                /*$totalQtyCat =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->whereNotIn('product_id', $skuid)
                                ->sum('order_qty');*/

                // $totalCat = DB::table('tbl_order_details')
                //                     ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                //                     ->where('order_id', $lastOrderId)
                //                     ->whereNotIn('product_id', $skuid)
                //                     ->groupBy('cat_id')
                //                     ->get();

                $offerGroupType = DB::table('tbl_order_details')
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('cat_id')
                                    ->get();

                $textGroup =0;
                if(sizeof($offerGroupType)>0)
                {
                    foreach ($offerGroupType as $value) 
                    {
                        if($value->offer_group_type==1)
                        {
                            // separate

                            $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereNotIn('product_id', $skuid)
                                    ->where('cat_id', $value->cat_id)
                                    //->groupBy('cat_id')
                                    ->get();



                            $textGroup1 = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','offer_group_type')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->groupBy('offer_group_type')
                                    ->first();

                            $textGroup = $textGroup1->offer_group_type;
                        }
                        else
                        {
                            //combine

                            $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $value->cat_id)
                                    ->get();

                        }


                        foreach ($totalCat as $catQty) 
                        {
                           
                            $totalQtyCat = $catQty->total;
                            $catid = $catQty->cat_id;
                            $total_odd=0;
                            $total_odd1=0;



                            $specialProducts =  DB::table('tbl_special_offer_product')->select('slab','catid', 'offerGroupId', 'pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                            ->where('catid',$catid)
                            ->where('slab',$totalQtyCat)
                            ->where('status',0)                       
                            ->get();

                           

                            if(sizeof($specialProducts) >0 )
                            {


                                foreach($specialProducts as $specialProducts) {

                                    $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialProducts->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $specialProducts->catid,
                                        'product_id'            => $specialProducts->pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialProducts->qty,
                                        'free_value'            => $specialProducts->value,
                                        'total_free_value'      => $specialProducts->value * $specialProducts->qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );

                                    if($specialProducts->and_qty>0){

                                      DB::table('tbl_order_special_and_free_qty')->insert(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $special_cat_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialProducts->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $specialProducts->catid,
                                        'product_id'            => $specialProducts->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialProducts->and_qty,
                                        'free_value'            => $specialProducts->and_value,
                                        'total_free_value'      => $specialProducts->and_value * $specialProducts->and_qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }
                            else
                            {

                                $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<$totalQtyCat AND status=0");
 

                                $maxSlab = DB::table('tbl_special_offer_product')->select('slab','catid','pid','offerGroupId','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                               ->where('catid',$catid)
                                               ->where('slab', $maxValue[0]->slab)
                                               ->where('status',0)                       
                                               ->get();
                                //dd($maxSlab);

                                if(sizeof($maxSlab) >0 )
                                {

                                    foreach($maxSlab as $maxSlab) {

                                        $mainQty = (int)($totalQtyCat/$maxSlab->slab);
                                       //dd($mainQty);
                                        $total_odd = $totalQtyCat - ($mainQty * $maxSlab->slab);
                                        $total_free = $maxSlab->qty * $mainQty;
                                        $total_value = $maxSlab->value * $total_free;
                                        $and_total_free = $maxSlab->and_qty * $mainQty;
                                        $and_total_value = $maxSlab->and_value * $and_total_free;

                                    

                                        $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $maxSlab->slab,
                                                'slab_count'            => $mainQty,
                                                'catid'                 => $maxSlab->catid,
                                                'product_id'            => $maxSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $maxSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($maxSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $maxSlab->catid,
                                            'product_id'            => $maxSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $maxSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }

                                    }
                                  
                                }

                                $lastSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd  AND status=0)  AND status=0");


                                if(sizeof($lastSlab) >0 )
                                {

                                     foreach($lastSlab as $lastSlab) {

                                        $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                        $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                        //dd($total_odd1);
                                        $total_free = $lastSlab->qty * $mainQty1;
                                        $total_value = $lastSlab->value * $total_free;
                                        $and_total_free = $lastSlab->and_qty * $mainQty1;
                                        $and_total_value = $lastSlab->and_value * $and_total_free;

                                        $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastSlab->slab,
                                                'slab_count'            => $mainQty1,
                                                'catid'                 => $lastSlab->catid,
                                                'product_id'            => $lastSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $lastSlab->catid,
                                            'product_id'            => $lastSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                               $lastOddSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd1 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab) >0)
                                {
                                  
                                   foreach($lastOddSlab as $lastOddSlab) {

                                        $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                        $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                        $total_free = $lastOddSlab->qty * $mainQty2;
                                        $total_value = $lastOddSlab->value * $total_free;
                                        $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                        $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                         $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab->slab,
                                                'slab_count'            => $mainQty2,
                                                'catid'                 => $lastOddSlab->catid,
                                                'product_id'            => $lastOddSlab->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $lastOddSlab->catid,
                                            'product_id'            => $lastOddSlab->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastOddSlab->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                                $lastOddSlab2 =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                                if(sizeof($lastOddSlab2) >0)
                                {
                                  
                                   foreach($lastOddSlab2 as $lastOddSlab2) {

                                        $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                        $total_free = $lastOddSlab2->qty * $mainQty3;
                                        $total_value = $lastOddSlab2->value * $total_free;
                                        $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                        $and_total_value = $lastOddSlab2->and_value * $and_total_free;


                                         $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $lastOddSlab2->slab,
                                                'slab_count'            => $mainQty3,
                                                'catid'                 => $lastOddSlab2->catid,
                                                'product_id'            => $lastOddSlab2->pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $total_free,
                                                'free_value'            => $lastOddSlab2->value,
                                                'total_free_value'      => $total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                        );

                                        if($lastOddSlab2->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                            'order_id'              => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $special_cat_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab2->slab,
                                            'slab_count'            => $mainQty3,
                                            'catid'                 => $lastOddSlab2->catid,
                                            'product_id'            => $lastOddSlab2->and_pid,
                                            'distributor_id'        => $distributorID,
                                            'point_id'              => $pointID,
                                            'route_id'              => $routeID,
                                            'retailer_id'           => $retailderID,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $and_total_free,
                                            'free_value'            => $lastOddSlab2->and_value,
                                            'total_free_value'      => $and_total_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }
                                }

                            } //  cagegory else close
                        }

                        if($textGroup==1)  // for separate offer
                        {
                            foreach($checkSkuProducts as $sku) {
                        
                                $totalSkuQty =  DB::table('tbl_order_details')
                                                ->where('order_id',$lastOrderId)
                                                ->where('product_id', $sku->sku_id)
                                                ->sum('delivered_qty');
                                                
                                $specialSku =  DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                ->where('sku_id',$sku->sku_id)
                                ->where('slab',$totalSkuQty)
                                ->where('status',0)                       
                                ->get();


                                if(sizeof($specialSku) >0 )
                                {

                                    foreach($specialSku as $specialSku) {
                                    
                                     $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialSku->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $catid,
                                        'product_id'            => $specialSku->pid,
                                        'sku_id'                => $specialSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => $foID,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialSku->qty,
                                        'free_value'            => $specialSku->value,
                                        'total_free_value'      => $specialSku->value * $specialSku->qty,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                        ]
                                        );

                                        if($specialSku->and_qty>0){

                                          DB::table('tbl_order_special_and_free_qty')->insert(
                                            [
                                                'order_id'             => $lastOrderId,
                                                'order_no'              => $orderNo,
                                                'special_id'            => $sp_sku_free_id,
                                                'auto_order_no'         => $autoOrderId,
                                                'order_date'            => date('Y-m-d h:i:s'),
                                                'slab'                  => $specialSku->slab,
                                                'slab_count'            => 1,
                                                'catid'                 => $catid,
                                                'sku_id'                => $specialSku->sku_id,
                                                'product_id'            => $specialSku->and_pid,
                                                'distributor_id'        => $distributorID,
                                                'point_id'              => $pointID,
                                                'route_id'              => $routeID,
                                                'retailer_id'           => $retailderID,
                                                'fo_id'                 => $foID,
                                                'global_company_id'     => Auth::user()->global_company_id,
                                                'total_free_qty'        => $specialSku->and_qty,
                                                'free_value'            => $specialSku->and_value,
                                                'total_free_value'      => $and_total_value,
                                                'created_by'            => Auth::user()->id,
                                                'ipaddress'             => request()->ip(),
                                                'hostname'              => $request->getHttpHost()
                                            ]
                                            );
                                        }
                                    }

                                }
                                else
                                {
                                    $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                                    $maxSlabSku = DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                                   ->where('catid',$catid)
                                                   ->where('sku_id',$sku->sku_id)
                                                   ->where('slab', $maxValueSku[0]->slab)
                                                   ->where('status',0)                       
                                                   ->get();

                                    if(sizeof($maxSlabSku) >0 )
                                    {

                                        foreach($maxSlabSku as $maxSlabSku) {

                                            $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                                           //dd($mainQty);
                                            $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                            $total_free = $maxSlabSku->qty * $mainQty;
                                            $total_value = $maxSlabSku->value * $total_free;
                                            $and_total_value =$maxSlabSku->and_qty * $maxSlabSku->and_value;

                                        

                                           $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $maxSlabSku->pid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $maxSlabSku->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($maxSlabSku->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $maxSlabSku->slab,
                                                    'slab_count'            => $mainQty,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $maxSlabSku->sku_id,
                                                    'product_id'            => $maxSlabSku->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $maxSlabSku->and_qty,
                                                    'free_value'            => $maxSlabSku->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }

                                        }
                                      
                                    }

                                    $lastSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                                    if(sizeof($lastSlabSku) >0 )
                                    {

                                         foreach($lastSlabSku as $lastSlab) {

                                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                           
                                            $total_free = $lastSlab->qty * $mainQty1;
                                            $total_value = $lastSlab->value * $total_free;
                                            $and_total_value =$lastSlab->and_qty * $lastSlab->and_value;

                                            $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastSlab->pid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastSlab->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastSlab->slab,
                                                    'slab_count'            => $mainQty1,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $lastSlab->sku_id,
                                                    'product_id'            => $lastSlab->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $lastSlab->and_qty,
                                                    'free_value'            => $lastSlab->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }

                                   $lastOddSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                                    if(sizeof($lastOddSlabSku) >0 )
                                    {
                                    
                                       foreach($lastOddSlabSku as $lastOddSlab) {

                                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                            $total_free = $lastOddSlab->qty * $mainQty2;
                                            $total_value = $lastOddSlab->value * $total_free;

                                            $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'product_id'            => $lastOddSlab->pid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $total_free,
                                                    'free_value'            => $lastOddSlab->value,
                                                    'total_free_value'      => $total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                            );

                                            if($lastOddSlab->and_qty>0){

                                              DB::table('tbl_order_special_and_free_qty')->insert(
                                                [
                                                    'order_id'             => $lastOrderId,
                                                    'order_no'              => $orderNo,
                                                    'special_id'            => $sp_sku_free_id,
                                                    'auto_order_no'         => $autoOrderId,
                                                    'order_date'            => date('Y-m-d h:i:s'),
                                                    'slab'                  => $lastOddSlab->slab,
                                                    'slab_count'            => $mainQty2,
                                                    'catid'                 => $catid,
                                                    'sku_id'                => $lastOddSlab->sku_id,
                                                    'product_id'            => $lastOddSlab->and_pid,
                                                    'distributor_id'        => $distributorID,
                                                    'point_id'              => $pointID,
                                                    'route_id'              => $routeID,
                                                    'retailer_id'           => $retailderID,
                                                    'fo_id'                 => $foID,
                                                    'global_company_id'     => Auth::user()->global_company_id,
                                                    'total_free_qty'        => $lastOddSlab->and_qty,
                                                    'free_value'            => $lastOddSlab->and_value,
                                                    'total_free_value'      => $and_total_value,
                                                    'created_by'            => Auth::user()->id,
                                                    'ipaddress'             => request()->ip(),
                                                    'hostname'              => $request->getHttpHost()
                                                ]
                                                );
                                            }
                                        }
                                    }
                                } // SKU else close
                            } // foreach loop close
                        }   
                    }                    
                } 
                                   
               
                             


                // Special Value wise commission Start

                $totalCatValue = DB::table('tbl_order_details')
                                ->select('order_id','cat_id', DB::raw('SUM(delivered_value) AS totalValue'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('cat_id')
                                ->get();

                foreach ($totalCatValue as $totalCatValue) {
                       
                       $valuecatid = $totalCatValue->cat_id;
                       $catWiseValue = $totalCatValue->totalValue;


                       $checkGroupId = DB::select("SELECT * FROM tbl_special_value_wise_category
                                JOIN tbl_special_values_wise ON tbl_special_values_wise.id = tbl_special_value_wise_category.svwid
                                WHERE tbl_special_value_wise_category.categoryid=$valuecatid AND $catWiseValue BETWEEN min AND max");


                       if(sizeof($checkGroupId) >0 )
                        {
                           
                            DB::table('tbl_special_temp_commission')
                                        ->where('order_id', $lastOrderId)
                                        ->where('catid', $valuecatid)
                                        ->where('retailer_id', $retailderID)
                                        ->delete();

                            DB::table('tbl_special_temp_commission')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'group_id'              => $checkGroupId[0]->group_id,
                                'offer_id'              => $checkGroupId[0]->svwid,
                                'catid'                 => $valuecatid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'cat_value'             => $catWiseValue,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                           
                        }
                }

                

                $totalValue = DB::table('tbl_special_temp_commission')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {
                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_special_values_wise WHERE status = 1 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {
                       DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->where('group_id', $totalOfferValue->group_id)
                                ->where('retailer_id', $retailderID)
                                ->where('is_point_wise',0)
                                ->delete();

                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => $foID,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'is_point_wise'         => 0,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }

                // Special Value wise commission end

            }



            if(sizeof($resultSpecialOffers)>0 || sizeof($point)>0 || sizeof($route)>0)
            {
                $order_no = DB::table('tbl_order')
                                ->where('order_id',$lastOrderId)
                                ->first();


                $orderNo = $order_no->order_no;
                $autoOrderId = $order_no->auto_order_no;
                $requestDistributorId = $order_no->distributor_id;
                $foID = $order_no->fo_id;
                $requestPointId = $order_no->point_id;
                $requestRouteId = $order_no->route_id;
                $requestRetailerId = $order_no->retailer_id;


                $total_odd=0;
                $total_odd1=0;
                $total_odd2=0;

                
                $catid = array();

                $checkCat =  DB::table('tbl_offer_qty_commission_value')
                                            ->select('catid','slab','per_pcs','value','status')
                                            ->groupBy('catid')
                                            ->get();
                

                foreach($checkCat as $skua) {
                    $catid[]= $skua->catid;
                }

                $totalCat = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id', DB::raw('SUM(delivered_qty) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->whereIn('cat_id', $catid)
                                    ->groupBy('cat_id')
                                    ->get();
                

                foreach ($totalCat as $catQty) 
                {
                    $catid=$catQty->cat_id;
                    
                    $itemsDelete = DB::table('tbl_order_free_qty_commission')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
           
                    $totalSpecialQty =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->sum('delivered_qty');
                   

                    if($totalSpecialQty>0 )
                    {
                        $specialProducts =  DB::table('tbl_offer_qty_commission_value')->select('catid','slab','per_pcs','value','status')
                        ->where('catid',$catid)
                        ->where('slab',$totalSpecialQty)
                        ->where('status',0)                       
                        ->get();

                        if(sizeof($specialProducts) >0 )
                        {
                            foreach($specialProducts as $specialProducts) {
                            
                             $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $specialProducts->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'free_value'            => $specialProducts->value,
                                'total_free_value'      => $specialProducts->value,
                                'distributor_id'        => $requestDistributorId,
                                'point_id'              => $requestPointId,
                                'route_id'              => $requestRouteId,
                                'retailer_id'           => $requestRetailerId,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                //'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                        else
                        {
                            $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<'".$totalSpecialQty."' AND status=0");


                            $maxSlab = DB::table('tbl_offer_qty_commission_value')->select('slab','catid','per_pcs','value')
                                           ->where('catid',$catid)
                                           ->where('slab', $maxValue[0]->slab)
                                           ->where('status',0)                       
                                           ->get();

                            if(sizeof($maxSlab) >0 )
                            {
                                foreach($maxSlab as $maxSlab) {

                                    $mainQty = (int)($totalSpecialQty/$maxSlab->slab);
                                    $total_odd = $totalSpecialQty - ($mainQty * $maxSlab->slab);
                                    //$total_free = $maxSlab->qty * $mainQty;
                                    $total_value = $maxSlab->value * $mainQty;

                                  $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlab->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'free_value'            => $maxSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                }
                              
                            }

                            $lastSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                            if(sizeof($lastSlab) >0 )
                            {
                                 foreach($lastSlab as $lastSlab) {

                                    $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                    $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                    //dd($total_odd1);
                                    //$total_free = $lastSlab->qty * $mainQty1;
                                    $total_value = $lastSlab->value * $mainQty1;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );
                                }
                            }

                           $lastOddSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlab) >0 )
                            {
                              
                               foreach($lastOddSlab as $lastOddSlab) {

                                    $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                    $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                    //$total_free = $lastOddSlab->qty * $mainQty2;
                                    $total_value = $lastOddSlab->value * $mainQty2;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastOddSlab->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                }
                            }

                            $lastOddSlab2 =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd2."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlab2) >0 )
                            {
                              
                               foreach($lastOddSlab2 as $lastOddSlab2) {

                                    $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                    //$total_free = $lastOddSlab2->qty * $mainQty3;
                                    $total_value = $lastOddSlab2->value * $mainQty3;

                                    $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab2->slab,
                                            'slab_count'            => $mainQty3,
                                            'catid'                 => $catid,
                                            'free_value'            => $lastOddSlab2->value,
                                            'total_free_value'      => $total_value,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => $foID,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                }
                            }


                        } 
                    }

                }
            } //  cagegory Qty wise commission else close

            $pointCheckOffer     = DB::table('tbl_point_wise_offer')
                        ->where('tbl_point_wise_offer.iPointId',$pointID)
                        ->where('dEndDate','>=',$currentDay)
                        ->where('tbl_point_wise_offer.iStatus',0)                      
                        ->first();

           if(sizeof($pointCheckOffer)>0)
           {

                $totalCatValue = DB::table('tbl_order_details')
                                ->select('order_id','cat_id', DB::raw('SUM(delivered_value) AS totalValue'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('cat_id')
                                ->get();

                foreach ($totalCatValue as $totalCatValue) {
                       
                       $valuecatid = $totalCatValue->cat_id;
                       $catWiseValue = $totalCatValue->totalValue;


                      /* $checkGroupId = DB::select("SELECT * FROM tbl_point_wise_value_category
                                JOIN tbl_point_wise_value ON tbl_point_wise_value.id = tbl_point_wise_value_category.point_value_id
                                WHERE tbl_point_wise_value_category.categoryid=$valuecatid AND $catWiseValue BETWEEN min AND max");*/

                       $checkGroupId     = DB::table('tbl_point_wise_value_category')
                                    ->join('tbl_point_wise_value', 'tbl_point_wise_value.id', '=', 'tbl_point_wise_value_category.point_value_id')
                                    ->where('tbl_point_wise_value.point_id',$pointID)
                                    ->where('tbl_point_wise_value_category.categoryid',$valuecatid)
                                    ->first();

                       if(sizeof($checkGroupId) >0 )
                        {
                           
                            DB::table('tbl_point_temp_commission_cat')
                                        ->where('order_id', $lastOrderId)
                                        ->where('catid', $valuecatid)
                                        ->where('retailer_id', $retailderID)
                                        ->delete();

                            DB::table('tbl_point_temp_commission_cat')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'group_id'              => $checkGroupId->group_id,
                                'offer_id'              => $pointCheckOffer->iId,
                                'catid'                 => $valuecatid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => $foID,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'cat_value'             => $catWiseValue,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                           
                        }
                }

                

                $totalValue = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {
                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_point_wise_value WHERE status = 0 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {

                        $checkSpecial = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id','catid')
                                    ->where('order_id', $lastOrderId)
                                    ->where('group_id', $totalOfferValue->group_id)
                                    ->get(); 

                       $sp_catid = array();

                        foreach($checkSpecial as $sku) {
                            $sp_catid[]= $sku->catid;
                        }

                        DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->whereIn('catid', $sp_catid)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                       DB::table('tbl_special_commission')
                                ->where('order_id', $lastOrderId)
                                ->where('group_id', $totalOfferValue->group_id)
                                ->where('retailer_id', $retailderID)
                                ->where('is_point_wise',1)
                                ->delete();

                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => $foID,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'is_point_wise'         => 1,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }
            }
                // Special Value wise commission end
                
                
                /* Clear User Regular Offer Selection */
                $offer = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $request->get('orderid'))                                                
                        ->get();

                foreach ($offer as $value) {
                    DB::table('tbl_order_free_qty')->where('order_id', $request->get('orderid'))
                        //->where('fo_id', Auth::user()->id)                
                        ->where('global_company_id', Auth::user()->global_company_id)->update(
                        [                    
                            'status' => NULL
                        ]
                    );  
                }
                
                /* Clear User Special Offer Selection */
                $spec_offer = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $request->get('orderid'))                                                
                        ->get();

                foreach ($spec_offer as $value) {
                    DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))
                        ->where('global_company_id', Auth::user()->global_company_id)->update(
                        [                    
                            'status' => NULL
                        ]
                    );  
                }
                
                    DB::table('tbl_order_special_free_qty')->where('order_id',$request->get('orderid'))->where('status',3)->update(
                        [
                            'delivery_date'            => date('Y-m-d H:i:s')
                        ]
                    );
               
                
                /* Zubair Clear Bundle Offer Begin */ //added Dec_09_2018
                DB::table('tbl_order_gift')->where('orderid',$request->get('orderid'))->delete();
                /* Zubair Clear Bundle Offer END */
        

            return Redirect::back()->with('success', 'Delivery Products Qty Update Successfully.');
       // return Redirect::to('/order')->with('success', 'Successfully Confirm Delivery Done.');  
        
        }
    }


     public function ssg_order_check_exceptional($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order';                    // Required Variable
        $pageTitle      = 'Order Details';           // Page Slug Title

        $resultPartialOrder  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(p_grand_total) as orderPartialTotal"),'order_id','partial_order_id','order_det_status','order_det_type')
                        ->where('order_id',$orderMainId)                                          
                        //->where('order_det_type', 'Confirmed')
                        ->whereIn('order_det_type', array('Confirmed','Delivered'))
                        ->groupBy('partial_order_id')
                        ->orderBy('order_det_id','ASC')                                         
                        ->get();


        // Total Partial Order Pending
        $totaPartialOrder = DB::table('tbl_order_details')
                        ->select('partial_order_id')
                        ->where('order_id',$orderMainId)
                        //->where('order_det_type','Confirmed')
                        ->where('order_det_status','!=', 'Closed')
                        ->groupBy('partial_order_id')
                        ->get()->count();

        // Total Partial Order Delivery
        $totaPartialOrderDelivered = DB::table('tbl_order_details')
                        ->select('partial_order_id')
                        ->where('order_id',$orderMainId)
                        ->where('order_det_type','Delivered')
                        ->where('order_det_status','!=', 'Closed')
                        ->groupBy('partial_order_id')
                        ->get()->count();

        //dd($totaPartialOrder);

        $foMainId   = $foMainId;

        return view('sales/distributorExceptional/bucketOrder', compact('selectedMenu','pageTitle','resultPartialOrder','foMainId','totaPartialOrder','totaPartialOrderDelivered'));
    }

    public function ssg_distributor_order_closed_exceptional($order_id,$partialOrder)
    {
        
        DB::table('tbl_order_details')
                ->where('order_id', $order_id)
                ->update(
                    [
                        'order_det_type'            => 'Confirmed',
                        'order_det_status'          => 'Closed' 
                    ]
            );
            
        DB::table('tbl_order')->where('order_id', $order_id)->update(
            [
                'order_status'  => 'Closed',
                'order_type'  => 'Confirmed',
                'closing_date'  => date('Y-m-d')
            ]
        );
        
        $OrdData = DB::table('tbl_order')
                        ->where('order_id', $order_id)
                        ->first();
        
        session()->put('offersSelected','regular');
     
        return Redirect::to('/order-edit-exceptional'. '/' . $order_id . '/' . $OrdData->fo_id . '/' . $partialOrder)->with('success', 'Successfully Order Closed');
    
    }


    public function ssg_invoice_order_partial_exceptional($orderMainId,$foMainId,$orderPartial)
    {
        $selectedMenu   = 'Order';                      // Required Variable
        $pageTitle      = 'Invoice Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.partial_order_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_order_details.order_det_status','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id','tbl_order.order_count')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        //->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPartial)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();



         $resultAllChalan  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.ordered_date','tbl_order_details.delivery_challan','tbl_order_details.delivered_date','tbl_order_details.order_id','tbl_order.order_id','tbl_order.order_type')
                        
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        //->where('tbl_order.order_det_type','Delivered')
                        ->where('tbl_order_details.order_det_type','Delivered')
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->where('tbl_order_details.partial_order_id',$orderPartial)
                        ->groupBy('tbl_order_details.delivery_challan')                        
                        ->get();
//dd($resultAllChalan);
        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_order.order_date','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        //->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_point.business_type_id','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name','users.sap_code')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone','display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('users', 'users.id', '=', 'tbl_user_details.user_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

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

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                           
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.status',0)        
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.status','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)
                                ->where('og.status',0)                                
                                ->first();
        }

        $reultProRate ='';

         $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')
                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)                           
                        ->where('partial_order_id', $orderPartial)                           
                        ->get();

        return view('sales/distributorExceptional/invoiceDetailsPartial', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift','reultProRate','commissionWiseItem','specialValueWise','resultAllChalan','orderPartial'));
    }




}

