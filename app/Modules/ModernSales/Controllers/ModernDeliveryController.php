<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
class ModernDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Home';           // Required Variable
        $pageTitle      = 'Dashboard';     // Page Slug Title
        return view('ModernSales::modernDashboard', compact('selectedMenu','pageTitle'));
    }

    public function modern_delivery()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                       
        ->where('mts_order.order_status', 'Confirmed')
        ->where('mts_order.ack_status', 'Approved')
        ->where('mts_order.req_status', 'send')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')               
        ->where('mts_order.order_status', 'Confirmed')
        ->where('mts_order.ack_status', 'Approved')
        ->where('mts_order.req_status', 'send')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $customerResult = DB::table('mts_customer_list') 
        ->where('status',0)
        ->orderBy('name')
        ->get(); 
        return view('ModernSales::sales/deliveryOrder/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList','customerResult'));
    }

    public function ssg_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('customer_id')=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*', 'mts_customer_list.credit_limit', 'mts_customer_list.customer_code','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                  
            ->leftjoin('mts_categroy_wise_commission', 'mts_categroy_wise_commission.party_id', '=', 'mts_order.party_id')                  
            ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_order.customer_id')                  
            ->where('mts_order.order_status', 'Confirmed')
            ->where('mts_order.ack_status', 'Approved')
            ->where('mts_order.req_status', 'send')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->groupBy('mts_order.order_id')                    
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*', 'mts_customer_list.credit_limit', 'mts_customer_list.customer_code','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id') 
            ->leftjoin('mts_categroy_wise_commission', 'mts_categroy_wise_commission.party_id', '=', 'mts_order.party_id')                  
            ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_order.customer_id')                    
            ->where('mts_order.order_status', 'Confirmed')
            ->where('mts_order.ack_status', 'Approved')
            ->where('mts_order.req_status', 'send')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_order.customer_id', $request->get('customer_id'))
            ->groupBy('mts_order.order_id')  
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/deliveryOrder/deliveryList', compact('resultOrderList'));
    }


    public function req_acknowledge_new(Request $req)
    {
        if($req->isMethod('post'))
        {
            foreach($req->input('reqid') as $rowReqId)
            {
                $ordack = 'ordack' . $rowReqId;
                $ordackVal = $req->input($ordack);
                
                if($ordackVal == 'YES')
                {

                    $DepotReqUpd = DB::table('mts_order')->where('order_id',$rowReqId)->update(
                        [
                            'req_status'  => 'approved',
                            'approved_by'  => Auth::user()->id,
                            'approved_date'  => date('Y-m-d H:i:s')
                        ]
                    );
					
					$DepotReqDetUpd = DB::table('mts_order_details')->where('order_id',$rowReqId)->update(
    					[
    						'approved_qty'  => DB::raw("order_qty"),
    						'approved_value'  => DB::raw("order_total_value")
    					]
    				);
                    

                }
                
            }
            
            
        }
        
        return Redirect::to('/modern-reqAllAnalysisList/')->with('success', 'Successfully Acknowledge.');
    }


    public function ssg_order_edit($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Confirmed')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_order.fo_id',$foMainId)                        
        ->where('mts_order_details.order_id',$DeliveryMainId)
        ->groupBy('mts_order_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','users.display_name','mts_order.party_id','mts_order.order_no','mts_order.po_no','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.order_status','Confirmed')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_order.fo_id',$foMainId)                        
        ->where('mts_order.order_id',$DeliveryMainId)
        ->first();

       // dd($resultInvoice);


        
        $orderCommission = DB::table('mts_categroy_wise_commission') 
        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
        ->where('order_id', $resultInvoice->order_id)                        
        // ->where('entry_by',Auth::user()->id)                        
        // ->where('party_id',$foMainId)
        ->first();

        $resultFoInfo   = DB::table('users')
        ->select('users.id','users.email','users.display_name','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
        ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
        ->where('tbl_user_type.user_type_id', 5)
        ->where('users.id', Auth::user()->id)
        ->where('users.is_active', 0) 
        ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
        ->first();

        $customerInfo = DB::table('mts_order')
        ->select('mts_order.order_id','mts_order.order_status','mts_customer_list.name','mts_customer_list.sap_code','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_order.total_order_value')
        ->join('mts_customer_list', 'mts_order.customer_id', '=', 'mts_customer_list.customer_id') 
        ->where('mts_order.order_id',$DeliveryMainId)
        ->first();



        $customerResult = DB::table('mts_customer_list')
        ->where('customer_id',$customerInfo->customer_id)
        ->where('status',0)
        ->first();

        $closingResult = DB::table('mts_outlet_ledger')
        ->where('customer_id',$customerInfo->customer_id)
        ->orderBy('ledger_id','DESC')
        ->first();

        if(sizeof($closingResult)>0){
            $closingBalance = $closingResult->closing_balance;
        } else{
            $closingBalance = 0;
        } 

        $creditSummery = $customerResult->credit_limit - $closingBalance - $customerInfo->total_order_value; 


        return view('ModernSales::sales/deliveryOrder/DeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo','creditSummery'));
    }

    public function ssg_modern_edit_submit(Request $request)
    {
        DB::beginTransaction();

        $lastOrderId    = $request->get('orderid');

        $countRows = count($request->get('qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $mTotalPrice += $request->get('price')[$m];
                $mTotalQty += $request->get('qty')[$m];
            }
        }            

        $autoAdd  = $lastOrderId;
        $chalanNO = 'SO-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;
        //dd($chalanNO);

        DB::table('mts_order')->where('order_id', $lastOrderId)
        ->where('fo_id', $request->get('foMainId'))
        ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_status'           => 'Delivered',
                'ack_status'             => 'Pending',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'update_date'            => date('Y-m-d H:i:s'),
                'chalan_no'              => $chalanNO,
                'chalan_date'            => date('Y-m-d H:i:s'),
            ]
        );

        ///////////
        $checkOrdata = DB::table('mts_order')
        ->where('order_id', $lastOrderId)
        ->first();
        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('mts_order_details')
                ->where('order_id', $lastOrderId)
                ->where('product_id',$request->get('product_id')[$m])
                ->first();
                // dd($request->get('qty')[$m]);

                if(sizeof($checkItemsExiting)>0)
                {
                    //$totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('mts_order_details')->where('product_id',$request->get('product_id')[$m])->update(
                        [
                            'deliverey_qty'           => $request->get('qty')[$m],
                            'delivery_value'         => $request->get('price')[$m]
                        ]
                    );
                    
                }
            }
        }


        $orderDetails = DB::table('mts_order_details') 
        ->select('order_id','party_id', 'cat_id',DB::raw('SUM(delivery_value) AS delivery_value'),'entry_by')
        ->where('order_id', $lastOrderId)                        
        ->groupBy('cat_id')                        
        // ->where('entry_by',Auth::user()->id)                        
        // ->where('party_id',$foMainId)
        ->get();

        foreach ($orderDetails as $key => $orderDetail) {

            $orderCommission = DB::table('mts_categroy_wise_commission') 
            ->select('order_id','party_id', 'cat_id','commission','entry_by')
            ->where('order_id', $orderDetail->order_id)
            ->where('cat_id', $orderDetail->cat_id)
            ->first();

            $commissionValue = ($orderDetail->delivery_value * $orderCommission->commission)/100;
            DB::table('mts_categroy_wise_commission')
            ->where('order_id', $orderDetail->order_id)
            ->where('cat_id', $orderDetail->cat_id)
            ->update(
                [
                    'delivery_value'              => $orderDetail->delivery_value,
                    'delivery_commission_value'   => $commissionValue
                ]
            );

        }


        
        ///////////
        DB::commit();
        DB::rollBack();
        return Redirect::to('/modern-delivery')->with('success', 'Successfully Confirm Delivery Done.'); 

    }

    public function ssg_report_order_delivery()
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
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.order_status', 'Delivered')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();


        return view('ModernSales::sales.delivery_Report.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_order.fo_id', $request->get('fos'))
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales.delivery_Report.deliveryReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id',
            'mts_route.route_id','mts_route.route_name','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->leftjoin('users', 'mts_order.fo_id', '=', 'users.id')
        ->leftjoin('mts_route', 'mts_route.route_id', '=', 'mts_order.route_id')
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

        $resultInvoice  = DB::table('mts_order')->select('mts_order.order_no','mts_order.po_no','mts_order.update_date','mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','mts_order.party_id','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address','mts_order.delivery_date')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)       
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


        

        return view('ModernSales::sales/delivery_Report/deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission','customerInfo'));

    }


    ///////sazzad
    public function modern_delivery_analysis()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';
        
        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order.global_company_id', 'tbl_product.name as pname', DB::Raw('sum(mts_order_details.order_qty) as total_qty'), DB::Raw('sum(mts_order_details.approved_qty) as total_approved_qty'), 'tbl_product.sap_code' , 'tbl_product.id as pid')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.req_status','approved')
        ->groupBy('mts_order_details.cat_id')                        
        ->groupBy('mts_order_details.product_id')                        
        ->get();

        $customerResult = DB::table('mts_customer_list') 
        ->where('status',0)
        ->orderBy('name')
        ->get();

        $categoryResult = DB::table('tbl_product_category') 
        ->where('status',0)
        ->orderBy('name')
        ->get();

        //dd($categoryResult);


        return view('ModernSales::sales/deliveryOrder/delivery_analysis', compact('selectedMenu','pageTitle','resultCartPro','customerResult','categoryResult'));
    }

     public function modern_delivery_analysis_list(Request $request)
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('customer_id')=='')
        {
        
        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.customer_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order.global_company_id', 'tbl_product.name as pname', DB::Raw('sum(mts_order_details.order_qty) as total_qty'), DB::Raw('sum(mts_order_details.approved_qty) as total_approved_qty'), 'tbl_product.sap_code' , 'tbl_product.id as pid')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.req_status','approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->groupBy('mts_order_details.cat_id')                        
        ->groupBy('mts_order_details.product_id')                        
        ->get();
        }
        else{ 
            $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.customer_id','mts_order.party_id','mts_order.global_company_id', 'tbl_product.name as pname', DB::Raw('sum(mts_order_details.order_qty) as total_qty'), DB::Raw('sum(mts_order_details.approved_qty) as total_approved_qty'), 'tbl_product.sap_code' , 'tbl_product.id as pid')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.req_status','approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('mts_order.customer_id',$request->get('customer_id'))                        
        ->groupBy('mts_order_details.cat_id')                        
        ->groupBy('mts_order_details.product_id')                        
        ->get(); 

        }


        return view('ModernSales::sales/deliveryOrder/delivery_analysis_list', compact('selectedMenu','pageTitle','resultCartPro','customerResult'));
    }


    public function product_wise_analysis($pid)
    {

                // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';
        
        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_det_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order.global_company_id', 'tbl_product.name as pname', 'order_qty', 'tbl_product.sap_code' , 'tbl_product.id as pid', 'order_total_value', 'p_unit_price', 'mts_order_details.product_id',  'mts_party_list.name as party_name', 'mts_customer_list.name as cus_name')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_order.customer_id')
        ->where('tbl_product.id', $pid)
        ->where('mts_order.req_status','approved')
        ->groupBy('mts_order.party_id')
        ->get();
 

        return view('ModernSales::sales/deliveryOrder/product_wise_analysis', compact('selectedMenu','pageTitle','resultCartPro'));
    }

    public function ssg_modern_open_submit(Request $request)
    {
        DB::beginTransaction();

        $lastOrderId    = $request->get('orderid'); 
        $countRows = count($request->get('qty'));

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {

                DB::table('mts_order_details')->where('order_det_id',$request->get('order_det_id')[$m])->update(
                    [
                        'approved_qty'           => $request->get('qty')[$m],
                        'approved_value'         => $request->get('price')[$m]
                    ]
                );

            }
        }


        DB::commit();
        DB::rollBack();
        return Redirect::to('/modern-reqAllAnalysisList')->with('success', 'Successfully Confirm Delivery Done.'); 

    }


} 
