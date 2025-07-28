<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
use Excel;
class EshopDeliveryController extends Controller
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

    public function eshop_delivery()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                       
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.ack_status', 'Approved')
        ->where('eshop_order.req_status', 'send')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.credit_limit')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id')              
        ->where('eshop_order.order_status', 'Confirmed')
        ->where('eshop_order.ack_status', 'Approved')
        ->where('eshop_order.req_status', 'send')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

        $customerResult = DB::table('eshop_customer_list') 
        ->where('status',0)
        ->orderBy('name')
        ->get();

        return view('eshop::sales/deliveryOrder/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList','customerResult'));
    }

    public function ssg_delivery_list(Request $request)
    {
      
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer_id = $request->get('customer_id') ? $request->get('customer_id') : NULL;
        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.credit_limit')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id') 
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')   
            ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id') 
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.req_status', 'send')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->when($customer_id, function($query, $customer_id){
                $query->where('eshop_order.customer_id', $customer_id);
            })
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get(); 
        return view('eshop::sales/deliveryOrder/deliveryList', compact('resultOrderList'));
    }

    public function eshop_summary_report(Request $request){

        $selectedMenu   = 'Stock report sku wise';    // Required Variable for menu
        $selectedSubMenu= 'Summary';                  // Required Variable for submenu
        $pageTitle      = 'Summary Report';           // Page Slug Title 
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email' )
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get(); 
         
        $stocks2 = DB::table('eshop_product_stock')
            ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name')
            ->selectRaw("SUM(eshop_product_stock.qty) as sum_qty")
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')                    
            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order.order_id')     
            ->groupBy('eshop_product_stock.product_id')   
            ->groupBy('eshop_product_stock.status')             
            ->get(); 
        $stocks = DB::table('eshop_product_stock')
            ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name')
            ->selectRaw("SUM(IF(eshop_product_stock.status=0,qty,0)) AS outstock, SUM(IF(eshop_product_stock.status=1,qty,0)) as instock, SUM(IF(eshop_product_stock.status=2,qty,0)) as hold_stock")
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')      
            ->groupBy('eshop_product_stock.product_id')
                           
            ->get(); 

        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                    
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.req_status', 'send')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.customer_id', $request->get('customer_id'))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        $query = DB::table('eshop_order_details')
            ->select('eshop_order_details.*' ,'eshop_product.sap_code','eshop_product.stock_qty','eshop_product.name')
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')   
            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')              
            ->selectRaw("SUM(eshop_order_details.order_qty) as product_qty") 
            ->selectRaw("SUM(eshop_order_details.order_total_value) as order_total_value") 
            ->groupBy('eshop_order_details.product_id') 
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_product.stock_qty', '>',0);   
        $resultProductList = $query->get();
        return view('eshop::sales/delivery_Report/deliverySummary', compact('stocks','resultProductList','resultOrderList','pageTitle','selectedMenu','resultFO')); 
    }

  
    public function eshop_summary_report_ajax(Request $request){
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive_id  = $request->get('executive_id');  
        $stocks = DB::table('eshop_product_stock')
            ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name')
            ->selectRaw("SUM(IF(eshop_product_stock.status=0,qty,0)) AS outstock, SUM(IF(eshop_product_stock.status=1,qty,0)) as instock, SUM(IF(eshop_product_stock.status=2,qty,0)) as hold_stock")
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_product_stock.created_at,'%Y-%m-%d'))"), array($fromdate, $todate))      
            ->groupBy('eshop_product_stock.product_id')                           
            ->get();  
        $resultProductList = $stocks;
        return view('eshop::sales/delivery_Report/eshop_summary_report_ajax', compact('resultProductList','stocks'));
    }
    public function eshop_stock_report_download($sdate= NULL, $edate=NULL){
         
        $stocks = DB::table('eshop_product_stock')
            ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name')
            ->selectRaw("SUM(IF(eshop_product_stock.status=0,qty,0)) AS outstock, SUM(IF(eshop_product_stock.status=1,qty,0)) as instock, SUM(IF(eshop_product_stock.status=2,qty,0)) as hold_stock")
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')
            ->when($sdate, function($q, $sdate){
                $todate     = date('Y-m-d', strtotime($sdate)).' 00:00:00';
                return $q->where('eshop_product_stock.created_at','>=', $todate);
            })
            ->when($edate, function($q, $edate){
                $fromdate   = date('Y-m-d', strtotime($edate)).' 23:59:59';
                return $q->where("eshop_product_stock.created_at",'<=', $fromdate);
            })      
            ->groupBy('eshop_product_stock.product_id')                           
            ->get();
            // ->whereBetween(DB::raw("(DATE_FORMAT(eshop_product_stock.created_at,'%Y-%m-%d'))"), array($fromdate, $todate)) 
        $custom_array[] = array('Product Name','SAP Code','In-stock','Out-stock','Closing');

        $s = 1;
        foreach ($stocks as $stock) { 
            $custom_array[] = array(
                'Product Name'     => $stock->name,
                'SAP Code'  => $stock->sap_code,
                'In-stock'       => $stock->instock,
                'Out-stock'       => $stock->outstock,
                'Closing' => $stock->instock - $stock->outstock 
            );  
        } 
        Excel::create('sku-wise-stock-report',function($excel) use ($custom_array) {
            $excel->sheet('Sales_Order',function($sheet) use ($custom_array){
                return $sheet->fromArray($custom_array,null,'A1', false, false);
            });
        })->download('xlsx'); 
        return Redirect::back()->with('success', 'Successfully Approved!');  

    }
    public function eshop_sku_wise_stock(Request $request){
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive_id  = $request->get('executive_id'); 
        $query = DB::table('eshop_order_details')
        ->select('eshop_order_details.*' , 'eshop_product.sap_code', 'eshop_product.name')
        ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')   
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        // ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id')                 
        ->selectRaw("SUM(eshop_order_details.order_qty) as product_qty") 
        ->selectRaw("SUM(eshop_order_details.order_total_value) as order_total_value") 
        ->groupBy('eshop_order_details.product_id') 
        ->where('eshop_order.order_status', 'Delivered')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate));
        if($executive_id){
            $query->where('eshop_order.fo_id', $executive_id) ;
        }  
        $resultProductList = $query->get();
        //print_r($resultProductList);
        return view('eshop::sales/delivery_Report/eshop_summary_report_ajax', compact('resultProductList'));
    }
    public function eshop_customer_wise_summary(Request $request){

        $selectedMenu   = 'Report';                      // Required Variable for menu
        $selectedSubMenu= 'Summary';                    // Required Variable for submenu
        $pageTitle      = 'Summary Report';            // Page Slug Title 
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get(); 

        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                    
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.req_status', 'send')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.customer_id', $request->get('customer_id'))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        return view('eshop::sales/delivery_Report/deliveryCustomerSummary', compact('resultOrderList','pageTitle','selectedMenu','resultFO')); 
    }

  
    public function eshop_customer_wise_summary_ajax(Request $request){
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive_id  = $request->get('executive_id'); 
        $query = DB::table('eshop_order_details')
        ->select('eshop_order_details.*' , 'eshop_product.sap_code', 'eshop_product.name' ,'eshop_customer_list.name as customer_name','eshop_customer_list.sap_code as customer_sap_code' )
        ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')   
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id') 
        ->selectRaw("SUM(eshop_order_details.order_qty) as product_qty") 
        ->selectRaw("SUM(eshop_order_details.order_total_value) as order_total_value") 
        // ->groupBy('eshop_order_details.product_id') 
        ->groupBy('eshop_order.customer_id') 
        ->where('eshop_order.order_status', 'Confirmed')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate));
        if($executive_id){
            $query->where('eshop_order.fo_id', $executive_id) ;
        }  
        $resultProductList = $query->get();

        //print_r($resultProductList);          

        return view('eshop::sales/delivery_Report/eshop_customer_summary_report_ajax', compact('resultProductList'));
    }


    public function eshop_delivery_report(Request $request)
    {
      
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('customer_id')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                                   
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                  
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.req_status', 'send')
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
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.req_status', 'send')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('eshop_order.customer_id', $request->get('customer_id'))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/delivery_Report/deliveryList', compact('resultOrderList'));
    }



    public function req_acknowledge_new(Request $req)
    {
        if($req->isMethod('post'))
        { 
            foreach($req->input('reqid') as $rowReqId)
            {
                $ordack = 'ordack' . $rowReqId; 
                $ordackVal = $req->input($ordack); 
                
                if($ordackVal == 'YES'){
                    $DepotReqUpd = DB::table('eshop_order')->where('order_id',$rowReqId)->update([
                        'req_status'  => 'approved',
                        'approved_by'  => Auth::user()->id,
                        'approved_date'  => date('Y-m-d H:i:s'),
                        'approval_status' => 'Acknowledged',
                        'status'          => 3 // Acknowledged by biller
                    ]);

                    // Stock Track
                    $order_details = DB::table('eshop_order_details')->where('order_id',$rowReqId)->get();     
                     
                    foreach ($order_details as $key => $item) { 
                        // Hold stock query
                        $hold = DB::table('eshop_product_stock')
                            ->select('qty', DB::raw('SUM(qty) as stock'))
                            ->where('product_id', $item->product_id)
                            ->where('order_id', $rowReqId)
                            ->where('order_details_id', $item->order_det_id)  
                            ->where('status', 2)->first();
                        // in stock query
                        $stock = DB::table('eshop_product_stock')
                            ->select('qty', DB::raw('SUM(qty) as stock'))
                            ->where('product_id', $item->product_id)
                            ->where('order_id', $rowReqId)
                            ->where('order_details_id', $item->order_det_id)  
                            ->where('status', 1)->first(); 
                        // out stock
                        $out = DB::table('eshop_product_stock')
                            ->select('qty', DB::raw('SUM(qty) as stock'))
                            ->where('product_id', $item->product_id)
                            ->where('order_id', $rowReqId)
                            ->where('order_details_id', $item->order_det_id)  
                            ->where('status', 0)->first(); 
     
                        $hold_qty = $stock->stock - $hold->stock;
                        if($hold_qty > 0){
                            $stock_qty = $item->order_qty - $hold_qty;
                            if($stock_qty > 0){
                                $stock_qty = $stock_qty;
                            }else{
                                $stock_qty = 0;
                            }
                        }else{
                            $stock_qty = $item->order_qty;
                        }                        

                        DB::table('eshop_product_stock')->insert([
                            'product_id' =>  $item->product_id,
                            'order_id'  => $rowReqId,
                            'order_details_id' => $item->order_det_id, 
                            'type' => 'approved', 
                            'status' => 2,
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_by' => Auth::user()->id,
                            'qty' =>  $stock_qty, 
                        ]);
                    }

                    $DepotReqDetUpd = DB::table('eshop_order_details')->where('order_id',$rowReqId)->update([
                        'approved_qty'  => DB::raw("order_qty"),
                        'approved_value'  => DB::raw("order_total_value") 
                    ]);

                } else if($ordackVal == 'NO'){
                    $DepotReqUpd = DB::table('eshop_order')->where('order_id',$rowReqId)->update([
                        'ack_status'  => 'Pending',
                        'approval_status' => 'Rejected by biller',
                        'status'          => 1 // send requisition
                    ]);
                }   
                
            }
        } 
        
        return Redirect::to('/eshop-reqAllAnalysisList/')->with('success', 'Successfully Acknowledge.');
    }


    public function ssg_order_edit($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id')


        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')

        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')

        ->where('eshop_order.order_status','Confirmed')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                       
        ->where('eshop_order.fo_id',$foMainId)                        
        ->where('eshop_order_details.order_id',$DeliveryMainId)
        ->groupBy('eshop_product.category_id')                        
        ->get();

        //dd($resultCartPro);

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


        return view('eshop::sales/deliveryOrder/DeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo','creditSummery'));
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

        DB::table('eshop_order')->where('order_id', $lastOrderId)
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
        $checkOrdata = DB::table('eshop_order')
        ->where('order_id', $lastOrderId)
        ->first();
        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('eshop_order_details')
                ->where('order_id', $lastOrderId)
                ->where('product_id',$request->get('product_id')[$m])
                ->first();
                // dd($request->get('qty')[$m]);

                if(sizeof($checkItemsExiting)>0)
                {
                    //$totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('eshop_order_details')->where('product_id',$request->get('product_id')[$m])->update(
                        [
                            'deliverey_qty'           => $request->get('qty')[$m],
                            'delivery_value'         => $request->get('price')[$m]
                        ]
                    );
                    
                }
            }
        }


        $orderDetails = DB::table('eshop_order_details') 
        ->select('order_id','party_id', 'cat_id',DB::raw('SUM(delivery_value) AS delivery_value'),'entry_by')
        ->where('order_id', $lastOrderId)                        
        ->groupBy('cat_id')                        
        // ->where('entry_by',Auth::user()->id)                        
        // ->where('party_id',$foMainId)
        ->get();

        foreach ($orderDetails as $key => $orderDetail) {

            $orderCommission = DB::table('eshop_categroy_wise_commission') 
            ->select('order_id','party_id', 'cat_id','commission','entry_by')
            ->where('order_id', $orderDetail->order_id)
            ->where('cat_id', $orderDetail->cat_id)
            ->first();

            $commissionValue = ($orderDetail->delivery_value * $orderCommission->commission)/100;
            DB::table('eshop_categroy_wise_commission')
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
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.order_status', 'Delivered')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        return view('eshop::sales.delivery_Report.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')

            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')

            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
            ->where('eshop_order.fo_id', $request->get('fos'))
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales.delivery_Report.deliveryReportList', compact('resultOrderList'));
    }

    ///////////////////
    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id',
            'eshop_route.route_id','eshop_route.route_name','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')
        ->leftjoin('users', 'eshop_order.fo_id', '=', 'users.id')
        ->leftjoin('eshop_route', 'eshop_route.route_id', '=', 'eshop_order.route_id')
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

        // $resultCartPro  = DB::table('eshop_order_details')
        // ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id')
        // ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        // ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        // ->where('eshop_order_details.order_id',$orderMainId)
        // ->groupBy('eshop_order_details.cat_id')                        
        // ->get();

        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_product.sap_code','eshop_product.category_id','eshop_product.sap_code')

        ->leftjoin('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->leftjoin('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->leftjoin('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->where('eshop_order_details.order_id',$orderMainId)
        ->groupBy('eshop_order_details.cat_id')                        
        ->get();

        //dd($resultCartPro);

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

        $customerInfo = DB::table('eshop_order')
        ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code')
        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
        ->where('eshop_order.order_id',$orderMainId)
        ->first();


        //dd($resultDistributorInfo);


        

        return view('eshop::sales/delivery_Report/deliveryNewReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission','customerInfo'));

    }


    ///////sazzad
    public function eshop_delivery_analysis()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';
        
        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id', 'eshop_product.name as pname', 'eshop_product.id as product_id', 'eshop_product.stock_qty', DB::Raw('sum(eshop_order_details.order_qty) as total_qty'), DB::Raw('sum(eshop_order_details.approved_qty) as total_approved_qty'), 'eshop_product.sap_code' , 'eshop_product.id as pid')

        //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        //->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')
        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')

        ->where('eshop_order.req_status','approved') 
        ->groupBy('eshop_product.category_id')           
        ->groupBy('eshop_order_details.product_id')                        
        ->get();

        $customerResult = DB::table('eshop_customer_list') 
        ->where('status',0)
        ->orderBy('name')
        ->get();

        $categoryResult = DB::table('eshop_product_category') 
        ->where('status',0)
        ->orderBy('name')
        ->get();

        //dd($categoryResult);


        return view('eshop::sales/deliveryOrder/delivery_analysis', compact('selectedMenu','pageTitle','resultCartPro','customerResult','categoryResult'));
    }

    public function eshop_delivery_analysis_list(Request $request)
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('customer_id')=='')
        {
        
        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.customer_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id', 'eshop_product.name as pname', DB::Raw('sum(eshop_order_details.order_qty) as total_qty'), DB::Raw('sum(eshop_order_details.approved_qty) as total_approved_qty'), 'eshop_product.sap_code' , 'eshop_product.id as pid')

        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        // ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        // ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')

        ->where('eshop_order.req_status','approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->groupBy('eshop_order_details.cat_id')                        
        ->groupBy('eshop_order_details.product_id')                        
        ->get();
        }
        else{ 
            $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.customer_id','eshop_order.party_id','eshop_order.global_company_id', 'eshop_product.name as pname', DB::Raw('sum(eshop_order_details.order_qty) as total_qty'), DB::Raw('sum(eshop_order_details.approved_qty) as total_approved_qty'), 'eshop_product.sap_code' , 'eshop_product.id as pid')


        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

        // ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        // ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')

        ->where('eshop_order.req_status','approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('eshop_order.customer_id',$request->get('customer_id'))                        
        ->groupBy('eshop_order_details.cat_id')                        
        ->groupBy('eshop_order_details.product_id')                        
        ->get(); 

        }


        return view('eshop::sales/deliveryOrder/delivery_analysis_list', compact('selectedMenu','pageTitle','resultCartPro','customerResult'));
    }


    public function product_wise_analysis($pid)
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';
        
        $resultCartPro  = DB::table('eshop_order_details')
        ->select('eshop_order_details.cat_id','eshop_order_details.order_det_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id', 'eshop_product.name as pname', 'order_qty', 'eshop_product.sap_code' , 'eshop_product.id as pid', 'order_total_value', 'p_unit_price', 'eshop_order_details.product_id',  'eshop_party_list.name as party_name', 'eshop_customer_list.name as cus_name', 'eshop_product.stock_qty')
        
        // ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
        // ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')
        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id')

        ->where('eshop_product.id', $pid)
        ->where('eshop_order.req_status','approved') 
        ->get(); 

        return view('eshop::sales/deliveryOrder/product_wise_analysis', compact('selectedMenu','pageTitle','resultCartPro'));
    }

    public function ssg_eshop_open_submit(Request $request)
    {
        DB::beginTransaction(); 
        $lastOrderId    = $request->get('orderid'); 
        $countRows = count($request->get('qty'));

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {

                DB::table('eshop_order_details')->where('order_det_id',$request->get('order_det_id')[$m])->update(
                    [
                        'approved_qty'           => $request->get('qty')[$m],
                        'approved_value'         => $request->get('price')[$m]
                    ]
                );

                DB::table('eshop_product_stock')->where('order_details_id',$request->get('order_det_id')[$m])->update([
                    'qty' => $request->get('qty')[$m],
                    'status' => 2,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_by' => Auth::user()->id
                ]);

            }
        }


        DB::commit();
        DB::rollBack();
        return Redirect::to('/eshop-reqAllAnalysisList')->with('success', 'Successfully Confirm Delivery Done.'); 

    }


} 