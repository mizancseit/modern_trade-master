<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
class ModernManagmentController extends Controller
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

    public function mts_approved()
    {
        // dd(Auth::user());
        $selectedMenu     = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle        = 'Order Delivery';

        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.order_status', 'Confirmed')
        ->where('mts_order.ack_status', 'Pending')
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
        ->where('mts_order.ack_status', 'Pending')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        return view('ModernSales::sales/adminReport/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                  
            ->where('mts_order.order_status', 'Confirmed')
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                  
            ->where('mts_order.order_status', 'Confirmed')
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_order.fo_id', $request->get('fos'))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/deliveryList', compact('resultOrderList'));
    }


    public function mts_order_view($DeliveryMainId,$foMainId)
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


        return view('ModernSales::sales/adminReport/DeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo','creditSummery'));
    }


    public function mts_approved_order($orderid,$partyid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('mts_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );
             return Redirect::to('/mts-approved')->with('success', 'Successfully Approved Order');
        }else{
            DB::table('mts_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
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
            DB::table('mts_remarks')->insert(
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
            DB::table('mts_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/mts-approved')->with('success', 'Order has been Canceled');
        }elseif($remarks_type=='return'){
            DB::table('mts_return')->where('return_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/mts-return-approved')->with('success', 'Order has been Canceled');

        }
        elseif($remarks_type=='replace'){
            DB::table('mts_replace')->where('replace_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );
             return Redirect::to('/mts-replace-approved')->with('success', 'Order has been Canceled');

        }

            
    }


    // Delivery approved part


    public function mts_delivery_approved()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery Approved';
        $subSelectedMenu = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                       
        ->where('mts_order.order_status', 'Confirmed')
        ->where('mts_order.ack_status', 'Pending')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')               
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Pending')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

        return view('ModernSales::sales/adminReport/deliveryApproved', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_delivery_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                  
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                  
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_order.fo_id', $request->get('fos'))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/deliveryApprovedList', compact('resultOrderList'));
    }


    public function mts_delivery_approved_view($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery Approved';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_order_details')
        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status','mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order.customer_id','mts_order.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id')
        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
        ->where('mts_order.order_status','Delivered')
        ->where('mts_order.ack_status', 'Pending')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id',$foMainId)                        
        ->where('mts_order_details.order_id',$DeliveryMainId)
        ->groupBy('mts_order_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_order')->select('mts_order.global_company_id','mts_order.order_id','mts_order.order_status','mts_order.fo_id','users.display_name','mts_order.party_id','mts_order.customer_id','mts_order.order_no','mts_order.po_no','mts_order.order_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.order_status','Delivered')
        ->where('mts_order.ack_status', 'Pending')
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


        return view('ModernSales::sales/adminReport/DeliveryApprovedEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo'));
    }



    public function mts_approved_delivery($orderid,$customerid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('mts_order')->where('order_id', $orderid)->where('customer_id', $customerid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );

            $totalSales = DB::table('mts_order')
            ->join('mts_customer_list', 'mts_customer_list.customer_id', 'mts_order.customer_id')
            ->where('mts_order.order_id', $orderid)->where('mts_order.order_status', 'Delivered')->first();


            if(sizeof($totalSales)>0){
                $ledger = DB::table('mts_outlet_ledger')->where('customer_id', $customerid)->orderBy('ledger_id','DESC')->first();
                
                // dd($ledger);
                if(sizeof($ledger)){
                    $closing_balance = $ledger->closing_balance;
                }else{
                    $closing_balance = 0;
                }
                DB::table('mts_outlet_ledger')->insert(
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

                $salesCommission = DB::table('mts_categroy_wise_commission')
                ->where('order_id', $orderid)->sum('delivery_commission_value');

                if(sizeof($salesCommission)>0){

                    $ledger = DB::table('mts_outlet_ledger')->where('customer_id', $customerid)->orderBy('ledger_id','DESC')->first();
                    
                    // dd($ledger);
                    if(sizeof($ledger)){
                        $closing_balance = $ledger->closing_balance;
                    }else{
                        $closing_balance = 0;
                    }

                    DB::table('mts_outlet_ledger')->insert(
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
            DB::table('mts_order')->where('order_id', $orderid)->where('customer_id', $customerid)->update(
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
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();


        return view('ModernSales::sales/adminReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $fo         = $request->get('fos');

        if($fromdate!='' && $todate!='' && $fo=='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!=''  && $fo=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_order.customer_id', $customer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $fo!='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_order.customer_id', $customer)
            ->where('mts_order.fo_id', $fo)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_order_details($orderMainId)
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

        

        return view('ModernSales::sales.adminReport.deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultBundleOffersGift','resultDistributorInfo','resultFoInfo','commissionWiseItem','specialValueWise','resultAllChalan', 'orderCommission'));

    }


    public function customer_ledger($value='')
    {

        $selectedMenu   = 'Ledger';                      // Required Variable for menu
        $selectedSubMenu= 'Ledger';                    // Required Variable for submenu
        $pageTitle      = 'Customer ledger';            // Page Slug Title

        $resultcus       = DB::table('mts_customer_list')
        ->orderBy('name','DESC')                    
        ->get();

        return view('ModernSales::sales/adminReport/customer_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    public function customer_ledger_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $ledger_list = DB::table('mts_outlet_ledger')
        ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('customer_id', $request->get('customerid'))
        ->orderBy('ledger_id','ASC')                    
        ->get();
        return view('ModernSales::sales/adminReport/customer_ledger_list', compact('ledger_list'));
    }

   
    public function customer_stock($value='')
    {

        $selectedMenu   = 'Stock';                      // Required Variable for menu
        $selectedSubMenu= 'Stock';                    // Required Variable for submenu
        $pageTitle      = 'Customer Stock';            // Page Slug Title

        $resultcus     = DB::table('mts_customer_list')
        ->orderBy('name','ASC')                    
        ->get();

        return view('ModernSales::sales/adminReport/customer_stock', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    public function customer_stock_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $stock_list = DB::table('mts_outlet_ledger')
        ->where('mts_outlet_ledger.customer_id', $request->get('customerid'))
        ->join('mts_customer_list', 'mts_outlet_ledger.customer_id', 'mts_customer_list.customer_id')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->orderBy('ledger_id','DESC')                    
        ->first();
        return view('ModernSales::sales/adminReport/customer_stock_list', compact('stock_list'));
    }

    public function addUser($value='')
    {

        $selectedMenu   = 'Stock';                      // Required Variable for menu
        $selectedSubMenu= 'Stock';                    // Required Variable for submenu
        $pageTitle      = 'Customer Stock';            // Page Slug Title

        $resultcus       = DB::table('mts_customer_list')
        ->orderBy('name','DESC')                    
        ->get();

        return view('ModernSales::sales/form/addUser', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus'));
        
    }


    
    

} 