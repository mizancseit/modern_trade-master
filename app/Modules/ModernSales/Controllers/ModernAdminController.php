<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
use Hash;
use Excel;
class ModernAdminController extends Controller
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
        //dd(Auth::user());


        if(Auth::user()->user_type_id == 3){ // for Executive 

        }else if(Auth::user()->user_type_id == 6){ // for Manager 
        }else{
            return Redirect::to('/modernSales'); 
        } 
        $selectedMenu     = 'Delivery';
        $subSelectedMenu  = 'Order Delivery'; 
        $pageTitle        = 'Order Delivery'; 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        // ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->select('mts_order.*','users.id','users.display_name','mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        // ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id') 
        ->join('users', 'users.id', '=', 'mts_order.fo_id')   
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id');
        if(Auth::user()->user_type_id == 3){ // for Executive
            $resultOrderList = $resultOrderList->where('mts_order.executive_id', Auth::user()->id)
            ->where('mts_order.ack_status', 'Pending');   
        }
        if(Auth::user()->user_type_id == 6){  // for Manager 
            $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'executive_approved');   
        }
        
        $resultOrderList = $resultOrderList->where('mts_order.order_status', 'Confirmed')
        
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

     /* $managementlist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.management_id')     
      ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
      ->groupBy('mts_role_hierarchy.management_id')
      ->get(); */

      $managementlist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
      ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
      ->groupBy('mts_role_hierarchy.executive_id')
      ->get();

      $officerlist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
      ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
      ->groupBy('mts_role_hierarchy.officer_id')
      ->get();

      //dd($managementlist);

        return view('ModernSales::sales/adminReport/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }
    

    public function mts_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate'))); 
        $executive_id    = $request->get('executive_id');
        $officer    = $request->get('fos');

        if($fromdate!='' && $todate!='' && $executive_id!='' && $officer=='')
        {
            $resultOrderList = DB::table('mts_order')
            // ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->select('mts_order.*','users.id','users.display_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            // ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')  
            ->join('users', 'users.id', '=', 'mts_order.fo_id')                                     
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')                 
            ->where('mts_order.executive_id', $executive_id);

            if(Auth::user()->user_type_id == 3){ // for Executive
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'Pending');   
            }
            if(Auth::user()->user_type_id == 6){  // for Manager 
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'executive_approved');   
            } 
            $resultOrderList = $resultOrderList->where('mts_order.order_status', 'Confirmed') 
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 
        } 
        else
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','users.id', 'users.id','users.display_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            // ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                    
            ->join('users', 'users.id', '=', 'mts_order.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id);                  
            if(Auth::user()->user_type_id == 3){ // for Executive
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'Pending');   
            }
            if(Auth::user()->user_type_id == 6){  // for Manager 
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'executive_approved');   
            } 
            $resultOrderList = $resultOrderList->where('mts_order.order_status', 'Confirmed') 
            
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate)) 
            ->where('mts_order.fo_id', $officer)
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/deliveryList', compact('resultOrderList'));
    }

    public function mts_remark(Request $request){
        DB::table('mts_order')->where('order_id',$request->get('order_id'))->update(
            [
                'remark'  => $request->get('remark')
            ]
        );
        return Redirect::to('/mts-approved')->with('success', 'remark has been added');
    }

    public function mtsDownloadCsv(Request $request){ 

        $fromdate   = date('Y-m-d', strtotime($request->get('fromDate')));
        $todate     = date('Y-m-d', strtotime($request->get('toDate'))); 
        $executive_id    = $request->get('executive_id');
        $officer    = $request->get('fos');  

        $resultOrderList = DB::table('mts_order')
            // ->select('mts_order.*','users.id', 'users.id','users.display_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')             
            ->select('mts_order.order_no as ORDER NO', 
                'users.display_name as Initiators Name', 
                'tbl_product_category.name as Product Group', 
                'tbl_product.sap_code as SAP Code', 
                'tbl_product.name as Product Name', 
                'mts_order_details.order_qty as QTY', 
                'tbl_product.mrp as MRP Price', 
                'mts_order_details.discount_rate as Discount Percentage', 
                DB::raw('tbl_product.mrp * mts_order_details.order_qty as `Total order`'), 
                'mts_order.po_no as Initiators Remarks')             
            ->join('users', 'users.id', '=', 'mts_order.fo_id')                    
            ->join('mts_order_details', 'mts_order_details.order_id', '=', 'mts_order.order_id')
            ->join('tbl_product', 'mts_order_details.product_id', '=', 'tbl_product.id')
            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')  
            
            ->where('mts_order.global_company_id', Auth::user()->global_company_id);                  
            if(Auth::user()->user_type_id == 3){ // for Executive
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'Pending');   
            }
            if(Auth::user()->user_type_id == 6){  // for Manager 
                $resultOrderList = $resultOrderList->where('mts_order.ack_status', 'executive_approved');   
            } 
            $resultOrderList = $resultOrderList->where('mts_order.order_status', 'Confirmed') 
            
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
            ->when($officer, function ($query, $officer) {
                return $query->where('mts_order.fo_id', $officer);
            })  
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 

        // dd($resultOrderList);
        $data = array();
		foreach ($resultOrderList as $items) {
			$data[] = (array)$items;  
		}

        Excel::create('Download_Sales_Order', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');

        return Redirect::to('/mts-approved')->with('success', 'remark has been added');
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

        if(Auth::user()->user_type_id == 3){ // for Executive
            $ack_status = 'executive_approved';
            $order_status = 'Rejected';

        }else if(Auth::user()->user_type_id == 6){ // for Manager 
            $ack_status = 'Approved';
            $order_status = 'Rejected';
        }else{
            return Redirect::to('/modernSales'); 
        } 
        if($status=='yes'){
            if(Auth::user()->user_type_id == 3){
                DB::table('mts_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                    [
                        'ack_status'  =>  $ack_status,
                        'executive_approved_date' => date('Y-m-d h:i:s')
                    ]
                );
            }else if(Auth::user()->user_type_id == 6){
                DB::table('mts_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                    [
                        'ack_status'  =>  $ack_status,
                        'manager_approved_date' => date('Y-m-d h:i:s'),
                    ]
                );
            }
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
 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
        ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
        ->where('mts_order.executive_id', Auth::user()->id)               
        ->where('mts_order.order_status', 'Delivered')
        ->where('mts_order.ack_status', 'Pending')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

         $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();

        return view('ModernSales::sales/adminReport/deliveryApproved', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }

    public function mts_delivery_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $executive  = $request->get('executive_id');
        $officer    = $request->get('fos'); 

        if($fromdate!='' && $todate!='' && $executive!='' && $officer=='')
        { 
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_order.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.executive_id', $executive)   
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
            ->where('mts_order.fo_id', $officer)
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
                        'invoice_no'            => $totalSales->order_no,
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
                        'invoice_no'            => $totalSales->order_no,
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
        ->where('mts_order.is_download', 'YES')
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
        ->where('mts_order.is_download', 'YES')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/adminReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','officerlist'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer_id');
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
            ->where('mts_order.executive_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $fo!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Delivered')
            ->where('mts_order.ack_status', 'Approved') 
            ->where('mts_order.executive_id', Auth::user()->id)
            ->where('mts_order.fo_id', $fo)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
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
            ->where('mts_order.executive_id', Auth::user()->id)
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

     public function ssg_report_sales_order()
    {
        $selectedMenu   = 'Sales Order Report';                      // Required Variable for menu
        $selectedSubMenu= 'Sales Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Sales Order Report';            // Page Slug Title
        //dd(Auth::user()->id);
 
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false; 
        if(Auth::user()->user_type_id ==7){ // Officer
            $officer = true;
        }else if(Auth::user()->user_type_id = 3){ // Executive
            $executive = true;
        }else if(Auth::user()->user_type_id = 6){ // Manager
            $manager = true;
        }else if(Auth::user()->user_type_id = 2){ // Billing
            $billing = true;
        } 
        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->when($officer, function ($query, $officer) {
                return $query->where('mts_order.order_status', 'Confirmed');
            }) 
            ->when($executive, function ($query, $executive) {
                return $query->whereIn('mts_order.ack_status', array('executive_approved','approved'));
            }) 
            ->when($manager, function ($query, $manager) {
                return $query->where('mts_order.order_status', 'Confirmed');
            }) 
            ->when($billing, function ($query, $billing) {
                return $query->where('mts_order.order_status', 'Confirmed');
            }) 
            ->where('mts_order.executive_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::sales/adminReport/salesOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','customer','resultOrderList','managementlist','officerlist'));
    }

    public function ssg_report_sales_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer_id');
        $fo         = $request->get('fos');  
        $officer = false;
        $executive = false;
        $manager = false;
        $billing = false;

        if(Auth::user()->user_type_id ==7){ // Officer
            $officer = true;
        }else if(Auth::user()->user_type_id = 3){ // Executive
            $executive = true;
        }else if(Auth::user()->user_type_id = 6){ // Manager
            $manager = true;
        }else if(Auth::user()->user_type_id = 2){ // Billing
            $billing = true;
        }
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->when($officer, function ($query, $officer) {
                return $query->where('mts_order.order_status', 'Confirmed')
                            ->where('mts_order.req_status','approved');
            }) 
            ->when($executive, function ($query, $executive) {
                return $query->whereIn('mts_order.ack_status', array('executive_approved','approved'));
            }) 
            ->when($manager, function ($query, $manager) {
                return $query->where('mts_order.order_status', 'Confirmed');
            }) 
            ->when($billing, function ($query, $billing) {
                return $query->where('mts_order.order_status', 'Confirmed');
            })
            ->when($fo, function ($query, $fo) {
                return $query->where('mts_order.fo_id', $fo);
            })
            ->when($customer, function ($query, $customer) {
                return $query->where('mts_order.customer_id', $customer);
            })
            ->where('mts_order.executive_id', Auth::user()->id)
            ->where('mts_order.global_company_id', Auth::user()->global_company_id) 
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 
        
        return view('ModernSales::sales/adminReport/salesOrderReportList', compact('resultOrderList'));
    }


    public function ssg_sales_order_details($orderMainId)
    {
        $selectedMenu   = 'Sales Order Report';                       // Required Variable for menu
        $selectedSubMenu= 'Sales Order Report';                    // Required Variable for submenu
        $pageTitle      = 'Sales Order Details';              // Page Slug Title

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

        return view('ModernSales::sales/adminReport/customer_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','executivelist','officerlist'));
        
    }


    public function customer_ledger_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $ledger_list = DB::table('mts_outlet_ledger')
        ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('customer_id', $request->get('customer_id'))
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

         $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.management_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.management_id')
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

        return view('ModernSales::sales/adminReport/customer_stock', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','managementlist','officerlist','executivelist'));
        
    }


    public function customer_stock_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

         $customer   = $request->get('customer_id');
         $fo         = $request->get('fos'); 

        if($fromdate!='' && $todate!='' && $fo=='' && $customer=='')
        {
            $stock_list = DB::table('mts_outlet_ledger')
            ->join('mts_customer_list', 'mts_outlet_ledger.customer_id', 'mts_customer_list.customer_id')
            ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', 'mts_customer_list.customer_id')
             ->join('mts_role_hierarchy', 'mts_role_hierarchy.officer_id', 'mts_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('mts_role_hierarchy.executive_id', Auth::user()->id)
            ->orderBy('mts_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }elseif ($fromdate!='' && $todate!='' && $fo!='' && $customer=='') {

          $stock_list = DB::table('mts_outlet_ledger')
            ->join('mts_customer_list', 'mts_outlet_ledger.customer_id', 'mts_customer_list.customer_id')
            ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', 'mts_customer_list.customer_id')
             ->join('mts_role_hierarchy', 'mts_role_hierarchy.officer_id', 'mts_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('mts_role_hierarchy.executive_id', Auth::user()->id)
             ->where('mts_role_hierarchy.officer_id', $fo)
            ->orderBy('mts_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }elseif ($fromdate!='' && $todate!='' && $fo!='' && $customer!='') {

           $stock_list = DB::table('mts_outlet_ledger')
            ->join('mts_customer_list', 'mts_outlet_ledger.customer_id', 'mts_customer_list.customer_id')
            ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', 'mts_customer_list.customer_id')
             ->join('mts_role_hierarchy', 'mts_role_hierarchy.officer_id', 'mts_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('mts_role_hierarchy.executive_id', Auth::user()->id)
              ->where('mts_role_hierarchy.officer_id', $fo)
               ->where('mts_customer_define_executive.customer_id', $customer)
            ->orderBy('mts_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }
        return view('ModernSales::sales/adminReport/customer_stock_list', compact('stock_list'));
    }

    public function mts_user_list()
    {

        $selectedMenu   = 'User List';                      // Required Variable for menu
        $selectedSubMenu= 'User List';                    // Required Variable for submenu
        $pageTitle      = 'User List';            // Page Slug Title

        $userType       = DB::table('tbl_user_type')
        ->orderBy('user_type','DESC')                    
        ->get();

        $designation       = DB::table('tbl_designation')
        ->orderBy('shot_code','ASC')                    
        ->get();

        $userList       = DB::table('users')
        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type')
        ->join('tbl_user_type', 'tbl_user_type.user_type_id', 'users.user_type_id')
        ->where('users.module_type',2)
        ->orderBy('display_name','ASC')                    
        ->get();

        return view('ModernSales::sales/form/addUser', compact('selectedMenu','selectedSubMenu','pageTitle','userList','userType','designation'));
        
    }

    public function mts_user_edit(Request $request)
    {

        $selectedMenu   = 'User List';                      // Required Variable for menu
        $selectedSubMenu= 'User List';                    // Required Variable for submenu
        $pageTitle      = 'User List';            // Page Slug Title

        $userType       = DB::table('tbl_user_type')
        ->orderBy('user_type','DESC')                    
        ->get();

        $designation       = DB::table('tbl_designation')
        ->orderBy('shot_code','ASC')                    
        ->get();

        $userList       = DB::table('users')
        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type')
        ->join('tbl_user_type', 'tbl_user_type.user_type_id', 'users.user_type_id')
        ->where('users.id',$request->get('user_id'))                    
        ->first();

        return view('ModernSales::sales/form/user_edit', compact('selectedMenu','selectedSubMenu','pageTitle','userList','userType','designation'));
        
    }

    public function mts_user_add_process(Request $request){ 
        $password = Hash::make($request->get('password'));
        $userId = DB::table('users')->insertGetId(
                [
                    'email'             => $request->get('email'),
                    'password'          => $password,
                    'display_name'      => $request->get('full_name'),
                    'employee_id'       => $request->get('emp_id'),
                    'designation'       => $request->get('designation'),
                    'user_type_id'      => $request->get('user_type'), 
                    'module_type'       => 2, 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')                    
                ]
            ); 
        $userId = DB::table('tbl_user_details')->insert(
                [
                    'user_id'           => $userId, 
                    'first_name'        => $request->get('full_name'),
                    'email'             => $request->get('email'),   
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')                    
                ]
            );  

         return Redirect::to('/mts-user-list')->with('success', 'User successfully Added.');
      }

    public function mts_user_edit_process(Request $request){ 

        if($request->get('password')!=''){
            $password = Hash::make($request->get('password'));
            $userId = DB::table('users')->where('id',$request->get('id'))->update(
                    [
                        'email'             => $request->get('email'),
                        'password'          => $password,
                        'display_name'      => $request->get('full_name'),
                        'employee_id'       => $request->get('emp_id'),
                        'designation'       => $request->get('designation'),
                        'user_type_id'      => $request->get('user_type'), 
                        'module_type'       => 2, 
                        'global_company_id' => Auth::user()->global_company_id,
                        'update_by'          => Auth::user()->id,
                        'update_date'        => date('Y-m-d h:i:s')
                        
                    ]
                );  
        }else{
            $userId = DB::table('users')->where('id',$request->get('id'))->update(
                    [
                        'email'             => $request->get('email'), 
                        'display_name'      => $request->get('full_name'),
                        'employee_id'       => $request->get('emp_id'),
                        'designation'       => $request->get('designation'),
                        'user_type_id'      => $request->get('user_type'), 
                        'module_type'       => 2, 
                        'global_company_id' => Auth::user()->global_company_id,
                        'update_by'          => Auth::user()->id,
                        'update_date'        => date('Y-m-d h:i:s')
                        
                    ]
                );
        }
         return Redirect::to('/mts-user-list')->with('success', 'User successfully Added.');
       
    }


    public function mts_user_active($id){

            DB::table('users')->where('id',$id)->update(
                [
                    
                    'is_active'          => 1,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','User Inactive successfully.'); 
        }

    public function mts_user_inactive($id){

        DB::table('users')->where('id',$id)->update(
            [
                
                'is_active'          => 0,  
                'update_by'       => Auth::user()->id,
                'update_date'     => date('Y-m-d h:i:s') 
            ]
        );
         return back()->with('success','User Active successfully.'); 
    }

    public function mts_user_delete($id){

        DB::table('users')->where('id',$id)->delete();
         return back()->with('success','User Delete successfully.'); 
    }

    public function mts_customer_delete($id){

        DB::table('mts_customer_define_executive')->where('customer_id',$id)->delete();
        DB::table('mts_customer_list')->where('customer_id',$id)->delete();


         return back()->with('success','Customer Delete successfully.'); 
    }

    public function mts_outlet_delete($id){

        DB::table('mts_party_list')->where('party_id',$id)->delete();
         return back()->with('success','Outlet Delete successfully.'); 
    }

    public function mts_product_delete($id){

        DB::table('tbl_product')->where('id',$id)->delete();
         return back()->with('success','Product Delete successfully.'); 
    }



    public function manager_list(Request $request){

      $management_id = $request->get('management_id');

      $managerlist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.manager_id')     
      ->where('mts_role_hierarchy.management_id',  $management_id)
      ->where('mts_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('mts_role_hierarchy.manager_id')
      ->get();

      //dd($managerlist);

      return view('ModernSales::sales/get_manager_list', compact('managerlist'));

    }

    public function executive_list(Request $request){

      $manager_id = $request->get('manager_id');

      $executivelist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
      ->where('mts_role_hierarchy.manager_id',$manager_id)
      ->where('mts_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('mts_role_hierarchy.executive_id')
      ->get();



       return view('ModernSales::sales/get_executive_list', compact('executivelist'));
    }

    public function officer_list(Request $request){

        $executive_id = $request->get('executive_id');

        $officerlist = DB::table('mts_role_hierarchy')
      ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
      ->where('mts_role_hierarchy.executive_id', $executive_id)
      ->where('mts_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('mts_role_hierarchy.officer_id')
      ->get();

      return view('ModernSales::sales/get_officer_list', compact('officerlist'));
    }

    public function mts_officer_customer_list(Request $request){

        $officer_id = $request->get('officer_id');

        $officerlist = DB::table('mts_customer_list')
      ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')     
      ->where('mts_customer_define_executive.executive_id', $officer_id)  
      ->get();

      return view('ModernSales::sales/get_officer_customer_list', compact('officerlist'));
    }


     public function customer_list(){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
        $resultcus       = DB::table('mts_customer_list')
        ->select('mts_customer_list.*','users.id','users.display_name')
        ->leftjoin('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id') 
        ->leftjoin('users', 'users.id', '=', 'mts_customer_define_executive.executive_id') 
        ->orderBy('mts_customer_list.name','ASC')                    
        ->get();

         $resultFo       = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        return view('ModernSales::sales/form/customer_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo'));

     }
    
      public function customer_create(Request $request){

        $customerId = DB::table('mts_customer_list')->insertGetId(
                [
                    'name'              => $request->get('customer_name'),
                    'mobile'            => $request->get('mobile_no'),
                    'address'           => $request->get('address'),
                    'credit_limit'      => $request->get('credit_limit'),
                    'shop_type'         => $request->get('shop_type'),
                    'customer_code'     => $request->get('customer_code'),
                    'sap_code'          => $request->get('sap_code'), 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

        DB::table('mts_customer_define_executive')->insert(
                [
                    'customer_id'      => $customerId, 
                    'executive_id'      => $request->get('executive_id'), 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

         return Redirect::to('/mts-customer-list')->with('success', 'Customer successfully Added.');
      }

    public function customer_edit(Request $request){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
        $resultcus       = DB::table('mts_customer_list')
        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
        ->where('mts_customer_list.customer_id',$request->get('customer_id'))
        ->orderBy('mts_customer_list.name','ASC')                    
        ->first();


         $resultFo  = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        return view('ModernSales::sales/form/customer_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo'));

    }


    public function modern_customer_edit_process(Request $request){

        DB::table('mts_customer_list')->where('customer_id',$request->get('id'))->update(
                [
                    'name'              => $request->get('customer_name'),
                    'mobile'            => $request->get('mobile_no'),
                    'address'           => $request->get('address'),
                    'credit_limit'      => $request->get('credit_limit'),
                    'shop_type'         => $request->get('shop_type'),
                    'customer_code'     => $request->get('customer_code'),
                    'sap_code'          => $request->get('sap_code'),  
                    'update_by'          => Auth::user()->id,
                    'update_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

        DB::table('mts_customer_define_executive')->where('customer_id',$request->get('id'))->update(
                [
                    'executive_id'      => $request->get('executive_id'),  
                    'update_by'          => Auth::user()->id,
                    'update_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

        return back()->with('success','Customer update successfully.'); 
    }


        public function mts_customer_active($id){

            DB::table('mts_customer_list')->where('customer_id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Customer Inactive successfully.'); 
        }

         public function mts_customer_inactive($id){
 
            DB::table('mts_customer_list')->where('customer_id',$id)->update(
                [
                    
                    'status'          => 0,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Customer Active successfully.'); 
        }


        // outlet process


 public function outlet_list(){

        $selectedMenu   = 'outlet List';                      // Required Variable for menu
        $selectedSubMenu= 'outlet List';                    // Required Variable for submenu
        $pageTitle      = 'outlet List';            // Page Slug Title

        
        $resultcus       = DB::table('mts_party_list')
        ->select('mts_party_list.party_id','mts_party_list.status','mts_party_list.name as pname','mts_party_list.mobile','mts_party_list.address','mts_customer_list.name as cname','mts_route.route_name','mts_route.route_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_party_list.customer_id')
        ->join('mts_route', 'mts_route.route_id', '=', 'mts_party_list.route_id')
        ->where('mts_customer_list.status',0)
        ->orderBy('mts_party_list.name','ASC')  
        ->get();

         $resultFo       = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        $resultCustomer = DB::table('mts_customer_list')
        ->where('status',0)
        ->orderBy('name','ASC')                    
        ->get();

        return view('ModernSales::sales/form/outlet_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));

     }

      public function mts_all_outlet_list(Request $request){

        $selectedMenu   = 'outlet List';                      // Required Variable for menu
        $selectedSubMenu= 'outlet List';                    // Required Variable for submenu
        $pageTitle      = 'outlet List';            // Page Slug Title
        $customer_id=$request->get('customer_id');
        $status=$request->get('status');


        if($customer_id!='' && $status=='')
        {
        $resultcus       = DB::table('mts_party_list')
        ->select('mts_party_list.party_id','mts_party_list.status','mts_party_list.name as pname','mts_party_list.mobile','mts_party_list.address','mts_customer_list.name as cname','mts_route.route_name','mts_route.route_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_party_list.customer_id')
        ->join('mts_route', 'mts_route.route_id', '=', 'mts_party_list.route_id')  
        ->where('mts_party_list.customer_id',$customer_id) 
        ->where('mts_customer_list.status',0)
        ->orderBy('mts_party_list.name','ASC') 
        ->get();
        }elseif($customer_id!='' && $status!='')
        {
        $resultcus       = DB::table('mts_party_list')
        ->select('mts_party_list.party_id','mts_party_list.status','mts_party_list.name as pname','mts_party_list.mobile','mts_party_list.address','mts_customer_list.name as cname','mts_route.route_name','mts_route.route_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_party_list.customer_id')
        ->join('mts_route', 'mts_route.route_id', '=', 'mts_party_list.route_id') 
        ->where('mts_customer_list.status',0)
        ->where('mts_party_list.customer_id',$customer_id)  
        ->where('mts_party_list.status',$status)
        ->orderBy('mts_party_list.name','ASC')  
        ->get();
        }elseif($customer_id=='' && $status!='')
        {
        $resultcus       = DB::table('mts_party_list')
        ->select('mts_party_list.party_id','mts_party_list.status','mts_party_list.name as pname','mts_party_list.mobile','mts_party_list.address','mts_customer_list.name as cname','mts_route.route_name','mts_route.route_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_party_list.customer_id')
        ->join('mts_route', 'mts_route.route_id', '=', 'mts_party_list.route_id')
        ->where('mts_customer_list.status',0)  
        ->where('mts_party_list.status',$status) 
        ->orderBy('mts_party_list.name','ASC') 
        ->get();
        }

         return view('ModernSales::sales/form/all_outlet_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));
    }

    
      public function outlet_create(Request $request){

        $customerId = DB::table('mts_party_list')->insert(
                [   
                    'customer_id'     => $request->get('customer_id'),
                    'name'              => $request->get('outlet_name'),
                    'mobile'            => $request->get('mobile_no'),
                    'address'           => $request->get('address'), 
                    'route_id'         => $request->get('shop_type'),
                    'sap_code'          => $request->get('sap_code'), 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

        

         return Redirect::to('/mts-outlet-list')->with('success', 'Outlet successfully Added.');
      }

      public function outlet_edit(Request $request){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
         $resultcus       = DB::table('mts_party_list')
        ->select('mts_party_list.party_id','mts_party_list.status','mts_party_list.name as pname','mts_party_list.mobile','mts_party_list.sap_code','mts_party_list.address','mts_party_list.customer_id','mts_customer_list.name as cname','mts_route.route_name','mts_route.route_id')
        ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_party_list.customer_id')
        ->join('mts_route', 'mts_route.route_id', '=', 'mts_party_list.route_id')  
        ->where('mts_party_list.party_id',$request->get('party_id'))
        //->where('mts_customer_list.status',0)
        ->first();

        //dd($resultcus);
 

        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        $resultCustomer = DB::table('mts_customer_list')
        ->where('status',0)                    
        ->get();

        return view('ModernSales::sales/form/outlet_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));

     }


     public function modern_outlet_edit_process(Request $request){

       DB::table('mts_party_list')->where('party_id',$request->get('id'))->update(
                [
                   'customer_id'     => $request->get('customer_id'),
                    'name'              => $request->get('outlet_name'),
                    'mobile'            => $request->get('mobile_no'),
                    'address'           => $request->get('address'), 
                    'route_id'         => $request->get('shop_type'),
                    'sap_code'          => $request->get('sap_code'), 
                    'global_company_id' => Auth::user()->global_company_id, 
                    'update_by'          => Auth::user()->id,
                    'update_date'        => date('Y-m-d h:i:s')
                    
                ]
            );
 

            return back()->with('success','Outlet update successfully.'); 
      }


        public function mts_outlet_active($id){

            DB::table('mts_party_list')->where('party_id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Outlet Inactive successfully.'); 
        }

         public function mts_outlet_inactive($id){
 
            DB::table('mts_party_list')->where('party_id',$id)->update(
                [
                    
                    'status'          => 0,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Outlet Active successfully.'); 
        }

    public function product_list(){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        
        $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')  
        ->orderBy('tbl_product.category_id','ASC')                    
        ->cursor();

        $resultCat  = DB::table('tbl_product_category')                  
        ->get();

        $cat  = DB::table('tbl_product_category')                  
        ->get();

         $resultChannel  = DB::table('tbl_business_type')                  
        ->get();
 

        return view('ModernSales::sales/form/product_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

    }

    public function mts_all_product_list(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        $channel=$request->get('channel');
        $category=$request->get('category');
        $status=$request->get('status');
        //dd($channel);

        if($channel!='' &&  $category=='' && $status=='')
        {
        $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')
        ->where('tbl_product_category.gid',$channel)  
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }elseif($channel!='' &&  $category!='' && $status==''){
            $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')
        ->where('tbl_product_category.gid',$channel)  
        ->where('tbl_product.category_id',$category) 
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }
        elseif($channel!='' &&  $category!='' && $status!=''){
            $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')
        ->where('tbl_product_category.gid',$channel)  
        ->where('tbl_product.category_id',$category)  
        ->where('tbl_product.status',$status)  
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }elseif($channel!='' &&  $category=='' && $status!=''){
            $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id') 
        ->where('tbl_product.status',$status) 
        ->where('tbl_product_category.gid',$channel)  
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }elseif($channel=='' &&  $category=='' && $status!=''){
            $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id') 
        ->where('tbl_product.status',$status)  
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }else{
             $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id') 
        ->orderBy('tbl_product.category_id','ASC')                    
        ->get();
        }
        return view('ModernSales::sales/form/all_product_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

     }
    
    public function product_create(Request $request){
        $this->validate($request, [
            'sap_code' => 'required|unique:tbl_product',
            'product_name' => 'required',
            'distributor_price' => 'required',
            'mrp_price' => 'required',
        ]);
 
        DB::table('tbl_product')->insert(
            [ 
                'category_id'   => $request->get('category'),
                'name'          => $request->get('product_name'),
                'companyid'     => $request->get('company_code'),
                'depo'          => $request->get('mrp_price'),
                'distri'        => $request->get('distributor_price'), // this is TP Price
                'mrp'           => $request->get('mrp_price'),
                'sap_code'      => $request->get('sap_code'),   
                'dateandtime'   => date('Y-m-d h:i:s')                
            ]
        ); 
        return Redirect::to('/mts-product-list')->with('success', 'Product successfully Added.');
    }

    public function modern_product_edit(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        
        $resultProduct       = DB::table('tbl_product')
        ->select('tbl_product.*','tbl_product_category.id as catid','tbl_product_category.gid','tbl_product_category.name as cname')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_product.category_id')
        ->where('tbl_product.id',$request->get('product_id'))             
        ->first();

        $resultCat  = DB::table('tbl_product_category')                  
        ->get();

         $resultChannel  = DB::table('tbl_business_type')                  
        ->get();
 

        return view('ModernSales::sales/form/product_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel'));

    }

    public function modern_product_edit_process(Request $request){
 
        DB::table('tbl_product')->where('id',$request->get('id'))->update(
                [ 
                    'category_id'            => $request->get('category'),
                    'name'           => $request->get('product_name'),
                    'companyid'      => $request->get('company_code'),
                    'depo'         => $request->get('mrp_price'),
                    'distri'     => $request->get('distributor_price'),
                    'mrp'     => $request->get('mrp_price'),
                    'sap_code'          => $request->get('sap_code'),   
                    'dateandtime'        => date('Y-m-d h:i:s')
                    
                ]
            ); 

        return back()->with('success','Product update successfully.'); 
    }

    public function mts_product_active($id){

        DB::table('tbl_product')->where('id',$id)->update(
            [
                
                'status'          => 1,  
                'user'       => Auth::user()->id,
                'dateandtime'     => date('Y-m-d h:i:s') 
            ]
        );
         return back()->with('success','product Inactive successfully.'); 
    }

    public function mts_product_inactive($id){

        DB::table('tbl_product')->where('id',$id)->update(
            [
                
                'status'          => 0,  
                'user'       => Auth::user()->id,
                'dateandtime'     => date('Y-m-d h:i:s') 
            ]
        );
         return back()->with('success','product Active successfully.'); 
    } 
    public function mts_category_list(Request $request){

        $cat  = DB::table('tbl_product_category')
        ->where('gid',$request->get('channel_id'))                  
        ->get();

        return view('ModernSales::sales/form/get_category_list', compact('cat'));
    }

    public function mts_bank_account_list()
    {

        $selectedMenu   = 'Bank Account List';                      // Required Variable for menu
        $selectedSubMenu= 'Bank Account List';                    // Required Variable for submenu
        $pageTitle      = 'Bank Account List';            // Page Slug Title
 

        $bankList = DB::table('tbl_master_bank')
                        ->orderBy('id','asc')
                        ->get();

        return view('ModernSales::sales/form/bank_account_add', compact('selectedMenu','selectedSubMenu','pageTitle','bankList'));
        
    }

    public function mts_bank_account_add_process(Request $request){
 
        DB::table('tbl_master_bank')->insert(
            [ 
                'accountname'    => $request->get('name'),
                'code'           => $request->get('account_no'),
                'bank_name'      => $request->get('bank_name'),
                'branchname'     => $request->get('branch_name'),
                'shortcode'      => $request->get('short_code'), 
                'user'           => Auth::user()->id 
                
            ]
        ); 

        return Redirect::to('/mts-bank-account-list')->with('success', 'Bank account successfully Added.');
    }

    public function mts_bank_account_edit(Request $request){
        $bank_id =  $request->get('bank_id'); 
         
        $bankInfo = DB::table('tbl_master_bank')
        ->where('id',$bank_id)
        ->first();


        return view('ModernSales::sales/form/bank_account_edit', compact('bankInfo'));

    }


    public function mts_bank_account_edit_process(Request $request){

        DB::table('tbl_master_bank')->where('id',$request->get('id'))->update(
            [ 
                'accountname'    => $request->get('name'),
                'code'           => $request->get('account_no'),
                'bank_name'      => $request->get('bank_name'),
                'branchname'     => $request->get('branch_name'),
                'shortcode'      => $request->get('short_code'), 
                'user'           => Auth::user()->id                 
            ]
        ); 

        // return Redirect::to('/mts-bank-account-list')->with('success', 'Bank account successfully update.');
 

        return back()->with('success','Bank update successfully.'); 
    }


    public function mts_bank_account_active($id,$type){

            if($type==0){

                DB::table('tbl_master_bank')->where('id',$id)->where('status',$type)->update(
                [
                    
                    'status'          => 1,  
                    'user'       => Auth::user()->id  
                ]
                );
                 return back()->with('success','Bank Account Inactive successfully.'); 
            }else{

                DB::table('tbl_master_bank')->where('id',$id)->where('status',$type)->update(
                [
                    
                    'status'          => 0,  
                    'user'       => Auth::user()->id  
                ]
                );
                 return back()->with('success','Bank Account Active successfully.');

            } 
        }

        public function mts_bank_account_delete($id){ 

                DB::table('tbl_master_bank')->where('id',$id)->delete();
            return back()->with('success','Bank Account Delete successfully.'); 
            
        }

        public function productUpload() {

            $selectedMenu   = 'Products upload';                      // Required Variable for menu
            $selectedSubMenu= 'Products upload';                    // Required Variable for submenu
            $pageTitle      = 'Products upload';            // Page Slug Title
      
        return view('ModernSales::sales/form/product_upload', compact('selectedMenu','selectedSubMenu','pageTitle'));
        
        }

        public function productsUpload(Request $request) {
            if($request->file('imported-file')){
                $path = $request->file('imported-file');
                $data = Excel::load($path, function($reader) {})->get();            
                if(!empty($data) && $data->count()){       
                    $data = $data->toArray(); 
                    //$businessType = $this->insertBusinessType($request,$data[0]);  
                    for($i=0;$i<count($data);$i++){                         
                        $product_category = DB::table('tbl_product_category')
                            ->where('g_name',$data[$i]['channel'])
                            ->where('g_code',$data[$i]['company_code'])
                            ->where('name',$data[$i]['category'])
                            ->first();
                        if(!$product_category){ 
                            $business_type = DB::table('tbl_business_type')
                            ->where('business_type','like',  '%' . $data[$i]['channel'] . '%') 
                            ->first();

                            if(!$business_type){ 
                                $this->insertBusinessType($request,$data[$i]);
                            }
                            $data[$i]['gid'] = $business_type->business_type_id;
                            $this->insertCategory($request,$data[$i]);
                        } 
                        $product_category = DB::table('tbl_product_category')
                            ->where('g_name',$data[$i]['channel'])
                            ->where('g_code',$data[$i]['company_code'])
                            ->where('name',$data[$i]['category'])
                            ->first();
                        $data[$i]['category_id'] = $product_category ? $product_category->id : '';
                        $this->updateProduts($request,$data[$i]);
                    }
        
                    if(!empty($insert)){ 
                        return back()->with('success','Target upload sucessfully Test.');
                   }
                }
            }
            return back()->with('error','Please Check your file, Something is wrong there.');
        } 

        public function insertCategory(Request $request, $data){
            if(DB::table('tbl_product_category')->where('name',$data['category'])->first()){
                return true;
            }else{
                return DB::table('tbl_product_category')->insert([ 
                    'gid'               => $data['gid'],
                    'g_name'            => $data['channel'],
                    'g_code'            => $data['company_code'],
                    'name'              => $data['category'],
                    'short_name'        => '', 
                    'global_company_id' => 1,
                    'status'            => 0,
                    'unit'              => '', 
                    'avg_price'         => '0.00',
                    'factor'            => 1,
                    'user'              => '',
                    'order_by'          => 0,  
                    'company_id'        => '',
                    'plant_code'        => '',  
                    'vat_percent'       => '0.80',
                    'order_by_la'       => '',
                    'top_group'         => '', 
                    'top_name'          => '',
                    'cat_id'            => '',
                    'offer_group'       => 0,
                    'LAF'               => '', 
                    'offer_type'        => 1,
                    'sync'              => 'Yes',
                    'modern_channel_id' => ''
                ]); 
            }
        }

        public function insertBusinessType(Request $request, $data){
            return DB::table('tbl_business_type')->insert([ 
                'business_type'     => $data['channel'],
                'global_company_id' => 1,
                'is_active'         => 0,
                'entry_by'          => Auth::user()->id 
            ]);
        }
        public function insertProduts(Request $request, $data){
            if(DB::table('tbl_product')->where('sap_code',$data['sap_code'])->first()){
                return true;
            }else{ 
                return DB::table('tbl_product')->insert([ 
                    'companyid'         => $data['company_code'],
                    'category_id'       => $data['category_id'],
                    'sap_code'          => $data['sap_code'],
                    'name'              => $data['product_name'],
                    'depo'              => $data['dp'],
                    'mrp'               => $data['mrp'],
                    'distri'            => $data['tp'],
                    'status'            => 0,
                    'stock_qty'         => 0,
                    'mkt_stock'         => 0,
                    'vat_percen'        => '0.95',
                    'order_status'      => 0,
                    'sync'              => 'Yes',
                ]);
            }
        }
        public function updateProduts(Request $request, $data){ 
            return DB::table('tbl_product')->where('sap_code',$data['sap_code'])->update([
                'name'              => $data['product_name'],
                'category_id'       => $data['category_id'],
                'depo'              => $data['dp'],
                'mrp'               => $data['mrp'],
                'distri'            => $data['tp'], 
            ]);
        }
        public function productsUpload2(Request $request) {
            if($request->file('imported-file')){
                $path = $request->file('imported-file'); 
                $data = Excel::load($path, function($reader) {})->get();            
                if(!empty($data) && $data->count()){       
                    $data = $data->toArray();   
                    for($i=0;$i<count($data);$i++){                       
                        $product_category = DB::table('tbl_product_category')
                            ->where('g_name',$data[$i]['channel']) 
                            ->where('name',$data[$i]['category'])
                            ->first(); 
                        if(!$product_category){ 
                            $business_type = DB::table('tbl_business_type')
                            ->where('business_type','like',  '%' . $data[$i]['channel'] . '%') 
                            ->first();

                            if(!$business_type){ 
                                $this->insertBusinessType($request,$data[$i]);
                            }
                            $data[$i]['gid'] = $business_type->business_type_id;
                            $insert = $this->insertCategory($request,$data[$i]);
                        } 
                        $product_category = DB::table('tbl_product_category')
                            ->where('g_name',$data[$i]['channel']) 
                            ->where('name',$data[$i]['category'])
                            ->first();  
                        $data[$i]['category_id'] = $product_category ? $product_category->id : '';
                        if(DB::table('tbl_product')->where('sap_code',$data[$i]['sap_code'])->first()){
                            $insert = $this->updateProduts($request,$data[$i]);
                        }else{
                            $insert = $this->insertProduts($request,$data[$i]);
                        }
                    } 
                    return back()->with('success','Product upload sucessfully.'); 
                }
            }
            return back()->with('error','Please Check your file, Something is wrong there.');
        } 

        public function productsUploadFormat(Request $request,$file_name){
            $file= public_path(). "/".$file_name; 
            return response()->download($file);
        }
} 
