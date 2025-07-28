<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
class EshopManagmentController extends Controller
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
        return view('eshop::modernDashboard', compact('selectedMenu','pageTitle'));
    }

    public function eshop_approved()
    {
        // dd(Auth::user());
        $selectedMenu     = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle        = 'Order Delivery';

        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')               
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        return view('eshop::sales/adminReport/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function eshop_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                                   
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Pending')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                    
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Pending')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('eshop_order.fo_id', $request->get('fos'))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/adminReport/deliveryList', compact('resultOrderList'));
    }


    public function eshop_order_view($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Confirmed')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                       
        ->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order_details.order_id',$DeliveryMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.order_status','Confirmed')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                        
        ->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order.order_id',$DeliveryMainId)
        ->first();

       // dd($resultInvoice);


        
        $orderCommission = DB::table('eshop_categroy_wise_commission') 
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

           $customerInfo = DB::table('eshop_order')
                        ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.sap_code','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_order.total_order_value')
                        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_order.order_id',$DeliveryMainId)
                        ->first();



            $customerResult = DB::table('eshop_customer_list')
                        ->where('customer_id',$customerInfo->customer_id)
                        ->where('status',0)
                        ->first();

             $closingResult = DB::table('eshop_outlet_ledger')
                        ->where('customer_id',$customerInfo->customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

           $creditSummery = $customerResult->credit_limit - $closingBalance - $customerInfo->total_order_value; 


        return view('eshop::sales/adminReport/DeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo','creditSummery'));
    }


    public function eshop_approved_order($orderid,$partyid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('eshop_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );
             return Redirect::to('/mts-approved')->with('success', 'Successfully Approved Order');
        }else{
            DB::table('eshop_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/mts-approved')->with('success', 'Order has been Canceled');
        }

        DB::commit();
        DB::rollBack();
   

    }

    public function not_approved_remarks_submit(Request $request){

        $orderid = $request->get('orderid');
        $customer_id = $request->get('customer_id');
        $partyid = $request->get('party_id');
        $foMainId = $request->get('foMainId');
        $remarks = $request->get('remarks');
        $remarks_type = $request->get('remarks_type');

        if($remarks!=''){
            DB::table('eshop_remarks')->insert(
                [
                    'reference_id'          => $orderid,
                    'remarks'               => $remarks,
                    'remarks_type'          => $remarks_type,
                    'customar_id'           => $customer_id,
                    'party_id'              => $partyid,
                    'fo_id'                 => $foMainId,
                    'created_by'            => Auth::user()->id,
                    'created_at'            => date('Y-m-d h:i:s'),
                    'updated_by'            => Auth::user()->id
                   
                    
                ]
            );
        }
        if($remarks_type=='order'){
            DB::table('eshop_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/mts-approved')->with('success', 'Order has been Canceled');
        }elseif($remarks_type=='return'){
            DB::table('eshop_return')->where('return_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/mts-return-approved')->with('success', 'Order has been Canceled');

        }
        elseif($remarks_type=='replace'){
            DB::table('eshop_replace')->where('replace_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );
             return Redirect::to('/mts-replace-approved')->with('success', 'Order has been Canceled');

        }

            
    }


    // Delivery approved part


    public function eshop_delivery_approved()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery Approved';
        $subSelectedMenu = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                       
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')               
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        return view('eshop::sales/adminReport/deliveryApproved', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function eshop_delivery_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                                   
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Pending')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                    
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Pending')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('eshop_order.fo_id', $request->get('fos'))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/adminReport/deliveryApprovedList', compact('resultOrderList'));
    }


    public function eshop_delivery_approved_view($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery Approved';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.customer_id','eshop_order.global_company_id')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order.order_status','Delivered')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order_details.order_id',$DeliveryMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.customer_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.order_status','Delivered')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                        
        ->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order.order_id',$DeliveryMainId)
        ->first();

       // dd($resultInvoice);


        
        $orderCommission = DB::table('eshop_categroy_wise_commission') 
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

         $customerInfo = DB::table('eshop_order')
                        ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.sap_code','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_order.total_order_value')
                        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
                        ->where('eshop_order.order_id',$DeliveryMainId)
                        ->first();


        return view('eshop::sales/adminReport/DeliveryApprovedEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo'));
    }



    public function eshop_approved_delivery($orderid,$customerid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('eshop_order')->where('order_id', $orderid)->where('customer_id', $customerid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );

            $totalSales = DB::table('eshop_order')
            ->join('eshop_customer_list', 'eshop_customer_list.customer_id', 'eshop_order.customer_id')
            ->where('eshop_order.order_id', $orderid)->where('eshop_order.order_status', 'Delivered')->first();


            if(sizeof($totalSales)>0){
                $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $customerid)->orderBy('ledger_id','DESC')->first();
                
                // dd($ledger);
                if(sizeof($ledger)){
                    $closing_balance = $ledger->closing_balance;
                }else{
                    $closing_balance = 0;
                }
                DB::table('eshop_outlet_ledger')->insert(
                    [
                        'ledger_date'           => date('Y-m-d h:i:s'),
                        'outlet_id'             => $totalSales->party_id,
                        'customer_id'           => $totalSales->customer_id,
                        'ref_id'                => $totalSales->order_id,
                        'trans_type'            => 'sales',
                        'party_sap_code'        => $totalSales->sap_code,
                        'opening_balance'       => $closing_balance,
                        'debit'                 => $totalSales->total_delivery_value,
                        'credit'                => 0,
                        'closing_balance'       => $closing_balance+$totalSales->total_delivery_value,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')

                    ]
                );

                $salesCommission = DB::table('eshop_categroy_wise_commission')
                ->where('order_id', $orderid)->sum('delivery_commission_value');

                if(sizeof($salesCommission)>0){

                    $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $customerid)->orderBy('ledger_id','DESC')->first();
                    
                    // dd($ledger);
                    if(sizeof($ledger)){
                        $closing_balance = $ledger->closing_balance;
                    }else{
                        $closing_balance = 0;
                    }

                    DB::table('eshop_outlet_ledger')->insert(
                    [
                        'ledger_date'           => date('Y-m-d h:i:s'),
                        'outlet_id'             => $totalSales->party_id,
                        'customer_id'           => $totalSales->customer_id,
                        'ref_id'                => $totalSales->order_id,
                        'trans_type'            => 'sales_commission',
                        'party_sap_code'        => $totalSales->sap_code,
                        'opening_balance'       => $closing_balance,
                        'debit'                 => 0,
                        'credit'                => $salesCommission,
                        'closing_balance'       => $closing_balance-$salesCommission,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')

                    ]
                );
                }

            }
        }else{
            DB::table('eshop_order')->where('order_id', $orderid)->where('customer_id', $customerid)->update(
                [
                    'ack_status'  => 'Rejected'
                ]
            );
        }

        DB::commit();
        DB::rollBack();
    return Redirect::to('/mts-delivery-approved')->with('success', 'Successfully Approved Order');

    }



    public function ssg_report_order_delivery()
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


        return view('eshop::sales/adminReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $fo         = $request->get('fos');

        if($fromdate!='' && $todate!='' && $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!=''  && $fo=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.customer_id', $customer)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $fo!='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.customer_id', $customer)
            ->where('eshop_order.fo_id', $fo)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/adminReport/deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_order_details($orderMainId)
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

        

        return view('eshop::sales.adminReport.deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan', 'orderCommission'));

    }


    public function customer_ledger($value='')
    {

        $selectedMenu   = 'Ledger';                      // Required Variable for menu
        $selectedSubMenu= 'Ledger';                    // Required Variable for submenu
        $pageTitle      = 'Customer ledger';            // Page Slug Title

        $resultcus       = DB::table('eshop_customer_list')
        ->orderBy('name','DESC')                    
        ->get();

        return view('eshop::sales/adminReport/customer_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    public function customer_ledger_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $ledger_list = DB::table('eshop_outlet_ledger')
        ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('customer_id', $request->get('customerid'))
        ->orderBy('ledger_id','ASC')                    
        ->get();
        return view('eshop::sales/adminReport/customer_ledger_list', compact('ledger_list'));
    }

   
    public function customer_stock($value='')
    {

        $selectedMenu   = 'Stock';                      // Required Variable for menu
        $selectedSubMenu= 'Stock';                    // Required Variable for submenu
        $pageTitle      = 'Customer Stock';            // Page Slug Title

        $resultcus     = DB::table('eshop_customer_list')
        ->orderBy('name','ASC')                    
        ->get();

        return view('eshop::sales/adminReport/customer_stock', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    public function customer_stock_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $stock_list = DB::table('eshop_outlet_ledger')
        ->where('eshop_outlet_ledger.customer_id', $request->get('customerid'))
        ->join('eshop_customer_list', 'eshop_outlet_ledger.customer_id', 'eshop_customer_list.customer_id')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->orderBy('ledger_id','DESC')                    
        ->first();
        return view('eshop::sales/adminReport/customer_stock_list', compact('stock_list'));
    }

    public function addUser($value='')
    {

        $selectedMenu   = 'Stock';                      // Required Variable for menu
        $selectedSubMenu= 'Stock';                    // Required Variable for submenu
        $pageTitle      = 'Customer Stock';            // Page Slug Title

        $resultcus       = DB::table('eshop_customer_list')
        ->orderBy('name','DESC')                    
        ->get();

        return view('eshop::sales/form/addUser', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    
    

} 