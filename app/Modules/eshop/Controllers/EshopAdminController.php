<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
use App\Modules\eshop\Models\Order;
use App\Modules\eshop\Models\OrderDetails;
use Illuminate\Support\Facades\Validator;
use App\Myclass\PHPMailer;
use App\Myclass\SMTP; 

class EshopAdminController extends Controller
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

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')               
            ->where('eshop_order.executive_id', Auth::user()->id) 
            ->where('eshop_order.status', 2)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get(); 

        $managementlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

        $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();  

        return view('eshop::sales/adminReport/delivery', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }

    public function eshop_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate'))); 
        $executive_id = $request->get('executive_id') ? $request->get('executive_id') : NULL;
        $officer    = $request->get('fos') ? $request->get('fos') : NULL;
 
        $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                                   
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')  
            ->when($executive_id, function($query, $executive_id){
                return $query->where('eshop_order.executive_id', $executive_id);
            }) 
            ->when($officer, function($query, $officer){
                return $query->where('eshop_order.fo_id', $officer);
            })   
            ->where('eshop_order.status', 2) 
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();  
        return view('eshop::sales/adminReport/deliveryList', compact('resultOrderList'));
    }


    public function eshop_order_view($DeliveryMainId,$foMainId)
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
            ->groupBy('eshop_order_details.cat_id')                        
            ->get(); 

        $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_order.total_discount','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status','Confirmed')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                        
            ->where('eshop_order.fo_id',$foMainId)                        
            ->where('eshop_order.order_id',$DeliveryMainId)
            ->first(); 
        
        $orderCommission = DB::table('eshop_categroy_wise_commission') 
            ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
            ->where('order_id', $resultInvoice->order_id)
            ->groupBy('order_id')  
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
        } else {
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
                    'approval_status' => 'Approved', 
                    'status'          => 5 // all items approved requisition  
                ]
            );
            DB::table('eshop_order_details')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [ 
                    'order_status' => 5, // all items approved requisition  
                ]
            );
            return Redirect::to('/eshop-approved')->with('success', 'Successfully Approved Order');
        }else{
            DB::table('eshop_order')->where('order_id', $orderid)->where('party_id', $partyid)->update(
                [     
                    'approval_status' => 'Order',
                    'status'          => 2,
                ]
            );

            return Redirect::to('/eshop-approved')->with('success', 'Order has been Canceled');
        }

        DB::commit();
        DB::rollBack(); 
    }
    public function eshop_approved_order_by_mail($orderid,$partyid,$status)
    { 
        if($status=='yes'){

            DB::table('eshop_order')->where('order_id', $orderid)->update(
                [
                    'ack_status'  => 'Approved',
                    'approval_status' => 'Approved'
                ]
            );
             
        }else{
            DB::table('eshop_order')->where('order_id', $orderid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );
        }
 		return Redirect::to('/eshop-approved-success')->with('success', 'Order has been approved');
    }
    public function eshop_approved_success()
    { 
        $message ="Order has been approved";
        return view('eshop::sales/adminReport/approved-success', compact('message'));
    }

    public function eshop_email_approval(Request $request, $DeliveryMainId,$foMainId ,$partyid = null )
    {

        //$foMainId =  1508 ;
        // $DeliveryMainId  =  13 ;
        ///$partyid =  1 ; 
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title 
        $resultCartPro  = DB::table('eshop_order_details')
            ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id','eshop_order_details.sap_code')

            ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
            ->where('eshop_order.order_status','Confirmed')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                       
            ->where('eshop_order.fo_id',$foMainId)                        
            ->where('eshop_order_details.order_id',$DeliveryMainId)
            ->groupBy('eshop_order_details.cat_id')                        
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
            ->groupBy('order_id')  
            ->first();

   

       // dd($orderCommission);

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
        $serial   = 1;
        $count    = 1;
        $subTotal = 0;
        $totalQty = 0;
        $totalPrice = 0;  

        $remarks = $request->get('remarks');  

        $link = url('/eshop-approved-order-mail/'.$DeliveryMainId.'/'.$partyid.'/'.'yes');
        $html = view('eshop::sales/adminReport/mailtemplate', compact('resultCartPro','link','serial','count','subTotal','totalQty','totalPrice','foMainId','DeliveryMainId','orderCommission','resultInvoice','customerInfo','remarks'));
        // $env_approval_email = env('APPROVAL_EMAIL');
        $to = env('APPROVAL_EMAIL'); //"abdul.mazid@ssgbd.com";
        // $to = 'kazi.rahaduzzaman@ssgbd.com';
        $subject = "Product Approval"; 
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
 
        // More headers
        $headers .= 'From: <info@ssgbd.com>' . "\r\n"; 
        // DB::table('eshop_order')->where('order_id',$DeliveryMainId)->update([
        //     'status' => 4,
        //     'updated_at' => date('Y-m-d'),
        //     'updated_by' => Auth::user()->id,
        //     'approval_status'=>'Waiting for mail approval']);
        //mail($to,$subject,$html,$headers);  
        //return $html;

        $receiver_name = env('APP_NAME');  
        $receiver_address = 'abdul.mazid@ssgbd.com'; //env('APPROVAL_EMAIL');
        $sender_address = 'info@ssgbd.com';
        $sender_name = 'Mazid'; 

        $phpMail = new PHPMailer();

        $phpMail->AddAddress($receiver_address, $receiver_name);
        $phpMail->AddReplyTo($receiver_address, $receiver_name);
        $message = view('eshop::sales/adminReport/mailtemplate', compact('resultCartPro','link','serial','count','subTotal','totalQty','totalPrice','foMainId','DeliveryMainId','orderCommission','resultInvoice','customerInfo','remarks'));
        
        $phpMail->FromName = $sender_name; 
        $phpMail->From = $sender_address;

        $phpMail->Sender = $sender_address;
        $phpMail->IsHTML(true);
        $phpMail->Host = env('MAIL_HOST'); //"ssgbd.com"; //your hostname such as ssgbd.com or ip
        $phpMail->IsSMTP();
        $phpMail->Mailer  =  env('MAIL_DRIVER');  // "smtp";
        $phpMail->Subject="Product Approval";
        $phpMail->Body=$message;            
        $phpMail->SMTPAuth=false;
        $phpMail->SMTPAutoTLS = false; 
        $phpMail->Port = env('MAIL_PORT'); ;  
        $phpMail->Send();
        //$phpMail->ClearAddresses() = env('APPROVAL_EMAIL');
        //$phpMail->ClearAttachments();  
        return Redirect::to('/eshop-approved')->with('success', 'Your mail has been successfully submitted');
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

             return Redirect::to('/eshop-approved')->with('success', 'Order has been Canceled');
        }elseif($remarks_type=='return'){
            DB::table('eshop_return')->where('return_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );

             return Redirect::to('/eshop-return-approved')->with('success', 'Order has been Canceled');

        }
        elseif($remarks_type=='replace'){
            DB::table('eshop_replace')->where('replace_id', $orderid)->where('party_id', $partyid)->update(
                [    
                    'order_status'  => 'Ordered',
                    'ack_status'    => 'Rejected'
                ]
            );
             return Redirect::to('/eshop-replace-approved')->with('success', 'Order has been Canceled');

        }

            
    }


    // Delivery approved part


    public function eshop_delivery_approved()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Delivery Approved';
        $subSelectedMenu = 'Order Delivery'; 
        $pageTitle      = 'Order Delivery';
 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('eshop_order')
        ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
        ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
        ->where('eshop_order.executive_id', Auth::user()->id)               
        ->where('eshop_order.order_status', 'Delivered')
        ->where('eshop_order.ack_status', 'Pending')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();

         $managementlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::sales/adminReport/deliveryApproved', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
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

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();


        return view('eshop::sales/adminReport/deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','officerlist'));
    }

    public function ssg_report_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer_id');
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
            ->where('eshop_order.executive_id', Auth::user()->id)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $fo!='' && $customer=='')
        {
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved') 
            ->where('eshop_order.executive_id', Auth::user()->id)
            ->where('eshop_order.fo_id', $fo)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
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
            ->where('eshop_order.executive_id', Auth::user()->id)
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

          $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::sales/adminReport/customer_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','executivelist','officerlist'));
        
    }


    public function customer_ledger_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $ledger_list = DB::table('eshop_outlet_ledger')
        ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('customer_id', $request->get('customer_id'))
        ->orderBy('ledger_id','ASC')                    
        ->get();
        return view('eshop::sales/adminReport/customer_ledger_list', compact('ledger_list'));
    }


    public function outlate_ledger($value='')
    {

        $selectedMenu   = 'Outlet Ledger';            // Required Variable for menu
        $selectedSubMenu= 'Outlet Ledger';            // Required Variable for submenu
        $pageTitle      = 'Outlet Ledger';            // Page Slug Title

        $resultcus       = DB::table('eshop_customer_list')
        ->orderBy('name','DESC')                    
        ->get(); 

        $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

        $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::sales/adminReport/outlet_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','executivelist','officerlist'));
        
    }


    public function outlate_ledger_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        $ledger_list = DB::table('eshop_outlet_ledger')
        ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
        ->where('customer_id', $request->get('customer_id'))
        ->where('outlet_id', $request->get('outlet_id'))
        ->orderBy('ledger_id','ASC')                    
        ->get();
        return view('eshop::sales/adminReport/outlet_ledger_list', compact('ledger_list'));
    }

   
    public function customer_stock($value='')
    {

        $selectedMenu   = 'Stock';                      // Required Variable for menu
        $selectedSubMenu= 'Stock';                    // Required Variable for submenu
        $pageTitle      = 'Customer Stock';            // Page Slug Title

        $resultcus     = DB::table('eshop_customer_list')
            ->orderBy('name','ASC')                    
            ->get();
        $party_list     = DB::table('eshop_party_list')
            ->orderBy('name','ASC')                    
            ->get(); 
        // print_r($party_list);
        // exit();
        $managementlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.management_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.management_id')
          ->get();

          $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::sales/adminReport/customer_stock', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','managementlist','officerlist','executivelist','party_list'));
        
    }


    public function customer_stock_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

         $customer   = $request->get('customer_id');
         $fo         = $request->get('fos'); 

        if($fromdate!='' && $todate!='' && $fo=='' && $customer=='')
        {
            $stock_list = DB::table('eshop_outlet_ledger')
            ->join('eshop_customer_list', 'eshop_outlet_ledger.customer_id', 'eshop_customer_list.customer_id')
            ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', 'eshop_customer_list.customer_id')
             ->join('eshop_role_hierarchy', 'eshop_role_hierarchy.officer_id', 'eshop_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('eshop_role_hierarchy.executive_id', Auth::user()->id)
            ->orderBy('eshop_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }elseif ($fromdate!='' && $todate!='' && $fo!='' && $customer=='') {

          $stock_list = DB::table('eshop_outlet_ledger')
            ->join('eshop_customer_list', 'eshop_outlet_ledger.customer_id', 'eshop_customer_list.customer_id')
            ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', 'eshop_customer_list.customer_id')
             ->join('eshop_role_hierarchy', 'eshop_role_hierarchy.officer_id', 'eshop_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('eshop_role_hierarchy.executive_id', Auth::user()->id)
             ->where('eshop_role_hierarchy.officer_id', $fo)
            ->orderBy('eshop_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }elseif ($fromdate!='' && $todate!='' && $fo!='' && $customer!='') {

           $stock_list = DB::table('eshop_outlet_ledger')
            ->join('eshop_customer_list', 'eshop_outlet_ledger.customer_id', 'eshop_customer_list.customer_id')
            ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', 'eshop_customer_list.customer_id')
             ->join('eshop_role_hierarchy', 'eshop_role_hierarchy.officer_id', 'eshop_customer_define_executive.executive_id')
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_ledger.ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
             ->where('eshop_role_hierarchy.executive_id', Auth::user()->id)
              ->where('eshop_role_hierarchy.officer_id', $fo)
               ->where('eshop_customer_define_executive.customer_id', $customer)
            ->orderBy('eshop_outlet_ledger.ledger_id','DESC')                    
            ->get();
        }
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

    public function manager_list(Request $request){

      $management_id = $request->get('management_id');

      $managerlist = DB::table('eshop_role_hierarchy')
      ->join('users', 'users.id', '=', 'eshop_role_hierarchy.manager_id')     
      ->where('eshop_role_hierarchy.management_id',  $management_id)
      ->where('eshop_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('eshop_role_hierarchy.manager_id')
      ->get();

      //dd($managerlist);

      return view('eshop::sales/get_manager_list', compact('managerlist'));

    }

    public function executive_list(Request $request){

      $manager_id = $request->get('manager_id');

      $executivelist = DB::table('eshop_role_hierarchy')
      ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
      ->where('eshop_role_hierarchy.manager_id',$manager_id)
      ->where('eshop_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('eshop_role_hierarchy.executive_id')
      ->get();



       return view('eshop::sales/get_executive_list', compact('executivelist'));
    }

    public function officer_list(Request $request){

        $executive_id = $request->get('executive_id');

        $officerlist = DB::table('eshop_role_hierarchy')
      ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
      ->where('eshop_role_hierarchy.executive_id', $executive_id)
      ->where('eshop_role_hierarchy.supervisor_id',  Auth::user()->id)
      ->groupBy('eshop_role_hierarchy.officer_id')
      ->get();

      return view('eshop::sales/get_officer_list', compact('officerlist'));
    }

    public function eshop_officer_customer_list(Request $request){

        $officer_id = $request->get('officer_id');

        $officerlist = DB::table('eshop_customer_list')
          ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')     
          ->where('eshop_customer_define_executive.executive_id', $officer_id)  
          ->get();

      return view('eshop::sales/get_officer_customer_list', compact('officerlist'));
    }
    public function eshop_customer_outlet_list(Request $request){

        $customer_id = $request->get('customer_id');

        $officerlist = DB::table('eshop_party_list')     
          ->where('eshop_party_list.customer_id', $customer_id)  
          ->get();

      return view('eshop::sales/get_customer_outlet_list', compact('officerlist'));
    }


    public function eshop_customer_list(){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
        $resultcus       = DB::table('eshop_customer_list')
        ->orderBy('name','ASC')                    
        ->get();

         $resultFo       = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)
        ->where('module_type',3)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('eshop_route')
        ->where('status',0)    
        ->get();

        return view('eshop::sales/form/customer_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo'));

     }
    
      public function eshop_customer_create(Request $request){

        $customerId = DB::table('eshop_customer_list')->insertGetId(
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

        DB::table('eshop_customer_define_executive')->insert(
                [
                    'customer_id'      => $customerId, 
                    'executive_id'      => $request->get('executive_id'), 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

         return Redirect::to('/eshop-customer-list')->with('success', 'Customer successfully Added.');
      }

      public function eshop_customer_edit(Request $request){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
        $resultcus       = DB::table('eshop_customer_list')
        ->where('customer_id',$request->get('customer_id'))
        ->orderBy('name','ASC')                    
        ->first();


         $resultFo  = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)
        ->where('module_type',3)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('eshop_route')
        ->where('status',0)    
        ->get();

        return view('eshop::sales/form/customer_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo'));

     }


     public function eshop_customer_edit_process(Request $request){

       DB::table('eshop_customer_list')->where('customer_id',$request->get('id'))->update(
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

        DB::table('eshop_customer_define_executive')->where('customer_id',$request->get('id'))->update(
                [
                    'executive_id'      => $request->get('executive_id'),  
                    'update_by'          => Auth::user()->id,
                    'update_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

            return back()->with('success','Customer update successfully.'); 
      }


        public function eshop_customer_active($id){

            DB::table('eshop_customer_list')->where('customer_id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Customer Inactive successfully.'); 
        }

         public function eshop_customer_inactive($id){
 
            DB::table('eshop_customer_list')->where('customer_id',$id)->update(
                [
                    
                    'status'          => 0,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Customer Active successfully.'); 
        }


        // outlet process


    public function eshop_outlet_list(){

        $selectedMenu   = 'outlet List';                      // Required Variable for menu
        $selectedSubMenu= 'outlet List';                    // Required Variable for submenu
        $pageTitle      = 'outlet List';            // Page Slug Title

        
        $resultcus       = DB::table('eshop_party_list')
        ->select('eshop_party_list.party_id','eshop_party_list.status','eshop_party_list.name as pname','eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.name as cname','eshop_route.route_name','eshop_route.route_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_party_list.customer_id')
        ->join('eshop_route', 'eshop_route.route_id', '=', 'eshop_party_list.route_id')
        ->orderBy('eshop_party_list.name','ASC')  
        ->get();

         $resultFo       = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('eshop_route')
        ->where('status',0)    
        ->get();

        $resultCustomer = DB::table('eshop_customer_list')
        ->where('status',0)
        ->orderBy('name','ASC')                    
        ->get();

        return view('eshop::sales/form/outlet_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));

     }

      public function eshop_all_outlet_list(Request $request){

        $selectedMenu   = 'outlet List';                      // Required Variable for menu
        $selectedSubMenu= 'outlet List';                    // Required Variable for submenu
        $pageTitle      = 'outlet List';            // Page Slug Title
        $customer_id=$request->get('customer_id');
        $status=$request->get('status');


        if($customer_id!='' && $status=='')
        {
        $resultcus       = DB::table('eshop_party_list')
        ->select('eshop_party_list.party_id','eshop_party_list.status','eshop_party_list.name as pname','eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.name as cname','eshop_route.route_name','eshop_route.route_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_party_list.customer_id')
        ->join('eshop_route', 'eshop_route.route_id', '=', 'eshop_party_list.route_id')  
        ->where('eshop_party_list.customer_id',$customer_id) 
        ->orderBy('eshop_party_list.name','ASC') 
        ->get();
        }elseif($customer_id!='' && $status!='')
        {
        $resultcus       = DB::table('eshop_party_list')
        ->select('eshop_party_list.party_id','eshop_party_list.status','eshop_party_list.name as pname','eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.name as cname','eshop_route.route_name','eshop_route.route_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_party_list.customer_id')
        ->join('eshop_route', 'eshop_route.route_id', '=', 'eshop_party_list.route_id') 

        ->where('eshop_party_list.customer_id',$customer_id)  
        ->where('eshop_party_list.status',$status)
        ->orderBy('eshop_party_list.name','ASC')  
        ->get();
        }elseif($customer_id=='' && $status!='')
        {
        $resultcus       = DB::table('eshop_party_list')
        ->select('eshop_party_list.party_id','eshop_party_list.status','eshop_party_list.name as pname','eshop_party_list.mobile','eshop_party_list.address','eshop_customer_list.name as cname','eshop_route.route_name','eshop_route.route_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_party_list.customer_id')
        ->join('eshop_route', 'eshop_route.route_id', '=', 'eshop_party_list.route_id')  
        ->where('eshop_party_list.status',$status) 
        ->orderBy('eshop_party_list.name','ASC') 
        ->get();
        }

         return view('eshop::sales/form/all_outlet_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));
    }

    
    public function eshop_outlet_create(Request $request){

        $customerId = DB::table('eshop_party_list')->insert(
                [   
                    'customer_id'       => $request->get('customer_id'),
                    'name'              => $request->get('outlet_name'),
                    'mobile'            => $request->get('mobile_no'),
                    'address'           => $request->get('address'), 
                    'route_id'          => $request->get('shop_type'),
                    'sap_code'          => $request->get('sap_code'), 
                    'global_company_id' => Auth::user()->global_company_id,
                    'entry_by'          => Auth::user()->id,
                    'entry_date'        => date('Y-m-d h:i:s')
                    
                ]
            );

        

         return Redirect::to('/eshop-outlet-list')->with('success', 'Outlet successfully Added.');
      }

      public function eshop_outlet_edit(Request $request){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
         $resultcus       = DB::table('eshop_party_list')
        ->select('eshop_party_list.party_id','eshop_party_list.status','eshop_party_list.name as pname','eshop_party_list.mobile','eshop_party_list.sap_code','eshop_party_list.address','eshop_customer_list.customer_id','eshop_customer_list.name as cname','eshop_route.route_name','eshop_route.route_id')
        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_party_list.customer_id')
        ->join('eshop_route', 'eshop_route.route_id', '=', 'eshop_party_list.route_id')  
        ->where('eshop_party_list.party_id',$request->get('party_id'))
        ->first();
 

        $shopType       = DB::table('eshop_route')
        ->where('status',0)    
        ->get();

        $resultCustomer = DB::table('eshop_customer_list')
        ->where('status',0)                    
        ->get();

        return view('eshop::sales/form/outlet_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo','resultCustomer'));

     }


     public function eshop_outlet_edit_process(Request $request){

       DB::table('eshop_party_list')->where('party_id',$request->get('id'))->update(
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


        public function eshop_outlet_active($id){

            DB::table('eshop_party_list')->where('party_id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Outlet Inactive successfully.'); 
        }

         public function eshop_outlet_inactive($id){
 
            DB::table('eshop_party_list')->where('party_id',$id)->update(
                [
                    
                    'status'          => 0,  
                    'update_by'       => Auth::user()->id,
                    'update_date'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','Outlet Active successfully.'); 
        }

      public function eshop_product_list(){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        
        $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')  
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();

        $resultCat  = DB::table('eshop_product_category')                  
        ->get();

        $cat  = DB::table('eshop_product_category')                  
        ->get();

         $resultChannel  = DB::table('tbl_business_type')                  
        ->get();
 

        return view('eshop::sales/form/product_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

     }

     public function eshop_all_product_list(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        $channel=$request->get('channel');
        $category=$request->get('category');
        $status=$request->get('status');
        //dd($channel);

        if($channel!='' &&  $category=='' && $status=='')
        {
        $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->where('eshop_product_category.gid',$channel)  
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }elseif($channel!='' &&  $category!='' && $status==''){
            $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->where('eshop_product_category.gid',$channel)  
        ->where('eshop_product.category_id',$category) 
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }
        elseif($channel!='' &&  $category!='' && $status!=''){
            $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->where('eshop_product_category.gid',$channel)  
        ->where('eshop_product.category_id',$category)  
        ->where('eshop_product.status',$status)  
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }elseif($channel!='' &&  $category=='' && $status!=''){
            $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id') 
        ->where('eshop_product.status',$status) 
        ->where('eshop_product_category.gid',$channel)  
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }elseif($channel=='' &&  $category=='' && $status!=''){
            $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id') 
        ->where('eshop_product.status',$status)  
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }else{
             $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id') 
        ->orderBy('eshop_product.category_id','ASC')                    
        ->get();
        }
        return view('eshop::sales/form/all_product_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

     }
       
    public function eshop_product_create(Request $request){

        DB::table('eshop_product')->insert(
            [ 
                'category_id'            => $request->get('category'),
                'name'           => $request->get('product_name'),
                'companyid'      => $request->get('company_code'),
                'depo'         => $request->get('depot_price'),
                'distri'     => $request->get('distributor_price'),
                'mrp'     => $request->get('mrp_price'),
                'sap_code'          => $request->get('sap_code'),   
                'dateandtime'        => date('Y-m-d h:i:s')
                
            ]
        ); 

        return Redirect::to('/eshop-product-list')->with('success', 'Product successfully Added.');
    }

    public function eshop_product_edit(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        
        $resultProduct       = DB::table('eshop_product')
        ->select('eshop_product.*','eshop_product_category.id as catid','eshop_product_category.gid','eshop_product_category.name as cname')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        ->where('eshop_product.id',$request->get('product_id'))             
        ->first();

        $resultCat  = DB::table('eshop_product_category')                  
        ->get();

         $resultChannel  = DB::table('tbl_business_type')                  
        ->get();
 

        return view('eshop::sales/form/product_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel'));

    }

    public function eshop_product_edit_process(Request $request){
 
        DB::table('eshop_product')->where('id',$request->get('id'))->update(
            [ 
                'category_id'            => $request->get('category'),
                'name'           => $request->get('product_name'),
                'companyid'      => $request->get('company_code'),
                'depo'         => $request->get('depot_price'),
                'distri'     => $request->get('distributor_price'),
                'mrp'       => $request->get('mrp_price'),
                'sap_code'          => $request->get('sap_code'),   
                'dateandtime'        => date('Y-m-d h:i:s')                
            ]
        ); 

        return back()->with('success','Product update successfully.'); 
    }

    public function eshop_product_active($id){

            DB::table('eshop_product')->where('id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'user'       => Auth::user()->id,
                    'dateandtime'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','product Inactive successfully.'); 
    }

    public function eshop_product_inactive($id){
 
            DB::table('eshop_product')->where('id',$id)->update(
                [
                    
                    'status'          => 0,  
                    'user'       => Auth::user()->id,
                    'dateandtime'     => date('Y-m-d h:i:s') 
                ]
            );
             return back()->with('success','product Active successfully.'); 
        }


        public function eshop_category_list(Request $request){

            $cat  = DB::table('eshop_product_category')
            ->where('gid',$request->get('channel_id'))                  
            ->get();

            return view('eshop::sales/form/get_category_list', compact('cat'));
        }

        public function eshop_approved_delivery(Request $request){
            DB::beginTransaction(); 
            $index_key = 0;
            if($request->status=='yes'){ 
                $eshop_order = DB::table('eshop_order')->where('eshop_order.order_id', $request->orderid)->where('eshop_order.customer_id', $request->customerid)
                ->join('eshop_order_details','eshop_order.order_id','=','eshop_order_details.order_id')
                ->get(); 


                DB::table('eshop_order')->where('order_id', $request->orderid)->where('customer_id', $request->customerid)
                    ->update([
                        'ack_status'  => 'Approved',
                        'approval_status' => 'In stock',
                        'status'          => 7 //In stock,
                    ]);
                
                foreach ($request->qty as $key => $order) {
                    $product = DB::table('eshop_product')->where('id',$key)->first();
                    $sumQty = $product->stock_qty + $order;
                    DB::table('eshop_product')->where('id',$key)->update(['stock_qty'=> $sumQty]);
                     
                    DB::table('eshop_order_details')->where('order_det_id',$request->items_id[$index_key])->update([
                        'received_qty'=> $order
                    ]);
                 
                    DB::table('eshop_product_stock')->insert([
                        'product_id' =>  $key,
                        'order_id'  => $request->orderid,
                        'order_details_id' => $request->items_id[$index_key], 
                        'type' => 'in',
                        'qty' => $order,
                        'status' => 1,
                        'created_at' => date('Y-m-d h:i:s'),
                        'created_by' => Auth::user()->id
                    ]); 
                    $index_key ++;
                }

                // foreach ($eshop_order as $key => $order) {
                //     $product = DB::table('eshop_product')->where('id',$order->product_id)->first();
                //     $sumQty = $product->stock_qty + $order->deliverey_qty;
                //     DB::table('eshop_product')->where('id',$order->product_id)->update(['stock_qty'=> $sumQty]);
                
                //     DB::table('eshop_product_stock')->insert([
                //         'product_id' =>  $order->product_id,
                //         'order_id'  => $orderid,
                //         'order_details_id' => $order->order_det_id, 
                //         'type' => 'in',
                //         'qty' => $order->deliverey_qty,
                //         'status' => 1,
                //         'created_at' => date('Y-m-d h:i:s'),
                //         'created_by' => Auth::user()->id
                //     ]); 
                // }               

                $totalSales = DB::table('eshop_order')
                ->join('eshop_customer_list', 'eshop_customer_list.customer_id', 'eshop_order.customer_id')
                ->where('eshop_order.order_id', $request->orderid)->where('eshop_order.order_status', 'Delivered')->first();


                if(sizeof($totalSales)>0){
                    $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $request->customerid)->orderBy('ledger_id','DESC')->first(); 
                    // dd($ledger);
                    if(sizeof($ledger)){
                        $closing_balance = $ledger->closing_balance;
                    }else{
                        $closing_balance = 0;
                    }
                    DB::table('eshop_outlet_ledger')->insert([
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
                    ]);

                    $salesCommission = DB::table('eshop_categroy_wise_commission')
                    ->where('order_id', $request->orderid)->sum('delivery_commission_value');

                    if(sizeof($salesCommission)>0){

                        $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $request->customerid)->orderBy('ledger_id','DESC')->first();
                        
                        // dd($ledger);
                        if(sizeof($ledger)){
                            $closing_balance = $ledger->closing_balance;
                        }else{
                            $closing_balance = 0;
                        }

                        DB::table('eshop_outlet_ledger')->insert([
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

                        ]);
                    }
                }
            }
            DB::commit();
            DB::rollBack();
            return Redirect::to('/eshop-delivery-approved')->with('success', 'Successfully Approved Order');

        }

        public function eshop_approved_delivery2($orderid,$customerid,$status){
            DB::beginTransaction(); 
            $index_key = 0;
            if($status=='yes'){ 
                $eshop_order = DB::table('eshop_order')->where('eshop_order.order_id', $orderid)->where('eshop_order.customer_id', $customerid)
                ->join('eshop_order_details','eshop_order.order_id','=','eshop_order_details.order_id')
                ->get(); 


                DB::table('eshop_order')->where('order_id', $orderid)->where('customer_id', $customerid)
                    ->update([
                        'ack_status'  => 'Approved',
                        'approval_status' => 'In stock',
                        'status'          => 7 //In stock,
                    ]); 

                foreach ($eshop_order as $key => $order) {
                    $product = DB::table('eshop_product')->where('id',$order->product_id)->first();
                    $sumQty = $product->stock_qty + $order->deliverey_qty;
                    DB::table('eshop_product')->where('id',$order->product_id)->update(['stock_qty'=> $sumQty]);
                
                    DB::table('eshop_product_stock')->insert([
                        'product_id' =>  $order->product_id,
                        'order_id'  => $orderid,
                        'order_details_id' => $order->order_det_id, 
                        'type' => 'in',
                        'qty' => $order->deliverey_qty,
                        'status' => 1,
                        'created_at' => date('Y-m-d h:i:s'),
                        'created_by' => Auth::user()->id
                    ]); 
                }               

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
                    DB::table('eshop_outlet_ledger')->insert([
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
                    ]);

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

                        DB::table('eshop_outlet_ledger')->insert([
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

                        ]);
                    }
                }
            }
            DB::commit();
            DB::rollBack();
            return Redirect::to('/eshop-delivery-approved')->with('success', 'Successfully Approved Order');

        }


        public function eshop_delivery_approved_list(Request $request) {
            $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
            $todate     = date('Y-m-d', strtotime($request->get('todate')));

            $executive  = $request->get('executive_id') ? $request->get('executive_id') : NULL;
            $officer    = $request->get('fos') ? $request->get('fos') : NULL; 

            $resultOrderList = DB::table('eshop_order')
                ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
                ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                                   
                ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
 
                ->when($officer, function($query, $officer){
                    return $query->where('eshop_order.fo_id', $officer);
                })

                ->when($executive, function($q, $executive){
                    return $q->where('eshop_order.executive_id', $executive);
                })
                   
                ->where('eshop_order.order_status', 'Delivered')
                ->where('eshop_order.ack_status', 'Pending')
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                ->orderBy('eshop_order.order_id','DESC')                    
                ->get();
        
            return view('eshop::sales/adminReport/deliveryApprovedList', compact('resultOrderList'));
        }
     

        public function eshop_delivery_approved_view($DeliveryMainId,$foMainId)
        {
            $selectedMenu   = 'Delivery Approved';                   // Required Variable
            $pageTitle      = 'Delivery Details';           // Page Slug Title

            $resultCartPro  = DB::table('eshop_order_details')
            ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.customer_id','eshop_order.global_company_id')

            //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
            ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

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

        public function eshop_make_invoice(){
            // dd(Auth::user());
            $selectedMenu   = 'Make Invoice';
            $subSelectedMenu = 'Make Invoice'; 
            $pageTitle      = 'Make Invoice';
     

            $todate     = date('Y-m-d');
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.executive_id', Auth::user()->id)               
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.is_active', '0') 
            ->where('eshop_order.stock_out', 1)
            ->where('eshop_order.customer_id', 1)

            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get(); 
            $managementlist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.executive_id')
              ->get();

            $officerlist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.officer_id')
              ->get();

            return view('eshop::sales/reports/make-invoice', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
        }

        public function eshop_make_invoice_list(Request $request)
        {
            $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
            $todate     = date('Y-m-d', strtotime($request->get('todate')));

            $executive  = $request->get('executive_id') ? $request->get('executive_id') : NULL;
            $officer    = $request->get('fos') ? $request->get('fos') : NULL; 
            if($fromdate && $todate){
                $today = date('Y-m-d'); 
            }

            $resultOrderList = DB::table('eshop_order')
                ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
                ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                               
                ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
                ->when($executive, function($query, $executive){
                    $query->where('eshop_order.executive_id', $executive);
                }) 
                ->when($officer, function($query, $officer){
                    $query->where('eshop_order.fo_id', $officer);
                }) 
                ->where('eshop_order.order_status', 'Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.is_active', '0')
                ->where('eshop_order.stock_out', 1)
                ->where('eshop_order.customer_id', 1)
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                ->orderBy('eshop_order.order_id','DESC')                    
                ->get();         
            return view('eshop::sales/reports/make-invoice-list', compact('resultOrderList'));
        }

        public function eshop_make_invoice_view($DeliveryMainId,$foMainId) { 
            $selectedMenu   = 'Make Invoice';
            $subSelectedMenu = 'Make Invoice'; 
            $pageTitle      = 'Make Invoice';         // Page Slug Title


            // $invice = DB::table('eshop_order')
            //     ->where('eshop_order.order_status','Delivered')
            //     ->where('eshop_order.ack_status', 'Approved')
            //     ->where('eshop_order.is_active', '0')  
            //     ->where('eshop_order.stock_out', 1)                     
            //     ->where('eshop_order.order_id',$DeliveryMainId)
            //     ->first(); 
            $invice = Order::where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.is_active', '0')  
                ->where('eshop_order.stock_out', 1)                     
                ->where('eshop_order.order_id',$DeliveryMainId)
                ->first(); 
            $itemsGroup = DB::table('eshop_order_details')
                ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname')
                ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')
                ->get();  
            
            $invoiceItems = DB::table('eshop_order_details')
                ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')
                ->get(); 


            $resultCartPro  = DB::table('eshop_order_details')
                ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.customer_id','eshop_order.global_company_id') 
                ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

                ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                ->where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                ->where('eshop_order.fo_id',$foMainId)                        
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')                        
                ->get();  

            $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.customer_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
                ->join('users', 'users.id', '=', 'eshop_order.fo_id')
                ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
                ->where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.stock_out', 1)
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
                ->where('eshop_order.customer_id', 1)                  
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


            return view('eshop::sales/reports/make-invoice-view', compact('itemsGroup','invice','selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo')); 
        }
 
        public function eshop_invoiced(){
            // dd(Auth::user());
            $selectedMenu   = 'Invoice List';
            $subSelectedMenu = 'Invoice List'; 
            $pageTitle      = 'Invoice List';
     

            $todate     = date('Y-m-d');
            $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')  
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.executive_id', Auth::user()->id)               
            ->where('eshop_order.order_status', 'Delivered')
            ->where('eshop_order.ack_status', 'Approved')
            ->where('eshop_order.is_active', '0') 
            ->where('eshop_order.stock_out', 0)
            ->where('eshop_order.customer_id', 1)

            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get(); 
            $managementlist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.executive_id')
              ->get();

            $officerlist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.officer_id')
              ->get();

            return view('eshop::sales/reports/invoiced', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
        }

        public function eshop_invoiced_list(Request $request)
        {
            $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
            $todate     = date('Y-m-d', strtotime($request->get('todate')));

            $executive  = $request->get('executive_id') ? $request->get('executive_id') : NULL;
            $officer    = $request->get('fos') ? $request->get('fos') : NULL; 
            if($fromdate && $todate){
                $today = date('Y-m-d'); 
            }

            $resultOrderList = DB::table('eshop_order')
                ->select('eshop_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'eshop_party_list.name', 'eshop_party_list.mobile','eshop_party_list.address')
                ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'eshop_order.fo_id')                               
                ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
                ->when($executive, function($query, $executive){
                    $query->where('eshop_order.executive_id', $executive);
                }) 
                ->when($officer, function($query, $officer){
                    $query->where('eshop_order.fo_id', $officer);
                }) 
                ->where('eshop_order.order_status', 'Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.is_active', '0')
                ->where('eshop_order.stock_out', 0)
                ->where('eshop_order.customer_id', 1)
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                ->orderBy('eshop_order.order_id','DESC')                    
                ->get();         
            return view('eshop::sales/reports/eshop-invoiced-list', compact('resultOrderList'));
        }

        public function eshop_invoiced_view($DeliveryMainId,$foMainId) { 
            $selectedMenu   = 'Invoice list';      // Required Variable
            $pageTitle      = 'Invoice';           // Page Slug Title

            $invice = Order::where('order_status','Delivered')
                ->where('ack_status', 'Approved')
                ->where('is_active', '0')  
                ->where('stock_out', 0)                     
                ->where('order_id',$DeliveryMainId)
                ->first(); 

            $itemsGroup = DB::table('eshop_order_details')
                ->select( 'eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname')
                ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')
                ->get();   
            $OrderDetails = OrderDetails::where('order_id',$DeliveryMainId);
            // dd($OrderDetails);
            return view('eshop::sales/reports/make-invoice-view', compact('itemsGroup','invice','selectedMenu','pageTitle', 'resultCategory','foMainId')); 
        }
        public function eshop_invoiced_view11($DeliveryMainId,$foMainId) { 
            $selectedMenu   = 'Invoice list';                   // Required Variable
            $pageTitle      = 'Invoice';           // Page Slug Title


            $invice = DB::table('eshop_order')
                ->where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.is_active', '0')  
                ->where('eshop_order.stock_out', 0)                     
                ->where('eshop_order.order_id',$DeliveryMainId)
                ->first(); 
            $itemsGroup = DB::table('eshop_order_details')
                ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname')
                ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')
                ->get();  
            
            $invoiceItems = DB::table('eshop_order_details')
                ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')
                ->get(); 


            $resultCartPro  = DB::table('eshop_order_details')
                ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.customer_id','eshop_order.global_company_id') 
                ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

                ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                ->where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
                ->where('eshop_order.fo_id',$foMainId)                        
                ->where('eshop_order_details.order_id',$DeliveryMainId)
                ->groupBy('eshop_order_details.cat_id')                        
                ->get();  

            $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.customer_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address','eshop_order.stock_out')
                ->join('users', 'users.id', '=', 'eshop_order.fo_id')
                ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
                ->where('eshop_order.order_status','Delivered')
                ->where('eshop_order.ack_status', 'Approved')
                ->where('eshop_order.stock_out', 0)
                ->where('eshop_order.global_company_id', Auth::user()->global_company_id) 
                ->where('eshop_order.customer_id', 1)                  
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


            return view('eshop::sales/reports/make-invoice-view', compact('itemsGroup','invice','selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo')); 
        }
        public function outlet_ledger($value='') {

            $selectedMenu   = 'Outlet Ledger';                      // Required Variable for menu
            $selectedSubMenu= 'Outlet Ledger';                    // Required Variable for submenu
            $pageTitle      = 'Outlet ledger';            // Page Slug Title

            $resultcus       = DB::table('eshop_customer_list')
            ->orderBy('name','DESC')                    
            ->get(); 

            $executivelist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.executive_id')
              ->get();

            $officerlist = DB::table('eshop_role_hierarchy')
              ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
              ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
              ->groupBy('eshop_role_hierarchy.officer_id')
              ->get();

            return view('eshop::sales/adminReport/customer_ledger', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','executivelist','officerlist'));
            
        }


        public function outlet_ledger_list(Request $request)
        {
            $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
            $todate     = date('Y-m-d', strtotime($request->get('todate')));

            $ledger_list = DB::table('eshop_outlet_ledger')
            ->whereBetween(DB::raw("(DATE_FORMAT(ledger_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('customer_id', $request->get('customer_id'))
            ->orderBy('ledger_id','ASC')                    
            ->get();
            return view('eshop::sales/adminReport/customer_ledger_list', compact('ledger_list'));
        }

        public function eshop_make_invoice_stock_out(Request $request){ 
            $index_key = 0;
            foreach ($request->out_qty as $key => $item) { 
                $product = DB::table('eshop_product')->where('id', $key)->first(); 
                $qty = $product->stock_qty - $item;
                DB::table('eshop_product')->where('id', $key)->update([
                    'stock_qty'  =>  $qty 
                ]); 
                DB::table('eshop_product_stock')->insert([
                    'product_id' =>  $key,
                    'order_id'  => $request->orderid,
                    'order_details_id' => $request->item_id[ $index_key], 
                    'type' => 'out',
                    'qty' => $item,
                    'status' => 0,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Auth::user()->id
                ]);
                DB::table('eshop_order_details')->where('order_det_id',$request->item_id[ $index_key])->update([
                    'deliverey_qty'  => $item,
                    'delivery_value' => $item * $request->change_prod_price[ $index_key]
                ]); 
                
                $index_key ++;
            }
            // item_id
            DB::table('eshop_order')->where('order_id',  $request->orderid)->update([
                'stock_out'  => 0,
                'status'     => 8 // stock out / final invoice
            ]); 
            return Redirect::to('/eshop-order-details/'.$request->orderid)->with('success', 'Successfully Approved Order and stock out');   
        }
        public function eshop_make_invoice_stock_out2( $id){ 

            $reultPro  = DB::table('eshop_order_details')
                ->select('eshop_order_details.order_det_id','eshop_order_details.cat_id','eshop_order_details.order_id','eshop_order_details.order_qty','eshop_order_details.product_id') 
                ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id') 
                ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code') 
                ->where('eshop_order_details.order_id',$id)   
                ->get(); 
            foreach ($reultPro as $key => $item) { 
                $product = DB::table('eshop_product')->where('id', $item->product_id)->first(); 
                $qty = $product->stock_qty - $item->order_qty;
                DB::table('eshop_product_stock')->insert([
                    'product_id' =>  $item->product_id,
                    'order_id'  => $id,
                    'order_details_id' => $item->order_det_id, 
                    'type' => 'out',
                    'qty' => $qty,
                    'status' => 0,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Auth::user()->id
                ]);

                DB::table('eshop_product')->where('id', $key)->update([
                    'stock_qty'  =>  $qty 
                ]);
            } 
            DB::table('eshop_order')->where('order_id',  $id)->update([
                'stock_out'  => 0
            ]); 
            return Redirect::to('/eshop-make-invoice')->with('success', 'Successfully Approved Order and stock out');   
        }

    public function eshop_categories_list(){

        $selectedMenu   = 'Category List';                      // Required Variable for menu
        $selectedSubMenu= 'Category List';                    // Required Variable for submenu
        $pageTitle      = 'Category List';            // Page Slug Title 
        $resultProduct       = DB::table('eshop_product_category')
        ->select('eshop_product_category.*','business_type','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'eshop_product_category.gid')  
        ->orderBy('eshop_product_category.id','ASC')                    
        ->get();

        $resultCat  = DB::table('eshop_product_category')                  
        ->get();

        $cat  = DB::table('eshop_product_category')                  
        ->get();

        $resultChannel  = DB::table('tbl_business_type')             
        ->get();

        return view('eshop::sales/form/category_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

    } 


    public function eshop_category_create(Request $request){

        // 'gid' // business_type_id /chenel 
        // 'g_name' // business_type name
 

        $validator = $this->validate($request,[
            'name' => 'required|unique:eshop_product_category',
            'channel' => 'required', 
            'company_code' =>  'required'
        ]);

 
        $business_type = DB::table('tbl_business_type')->where('business_type_id', $request->get('channel'))->first(); 
        DB::table('eshop_product_category')->insert(
            [ 
                'gid'               => $request->get('channel'),  // business_type_id
                'g_name'            => $business_type->business_type,
                'g_code'            => $request->get('company_code'),
                'name'              => $request->get('name'),
                'status'            => 0,
                'global_company_id' => 1,
                'factor'            => 1,
                'modern_channel_id' => 1,
                'sync'              => 'Yes',
                'date_time'         => date('Y-m-d h:i:s')                
            ]
        ); 

        return Redirect::to('/eshop-categories-list')
        ->withErrors($validator)
        ->with('success', 'Category successfully Added.');
    }

    public function eshop_all_category_list(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        $channel=$request->get('channel'); 
        $status=$request->get('status');

        $resultProduct       = DB::table('eshop_product_category')
        ->select('eshop_product_category.*','business_type','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'eshop_product_category.gid')  
        ->orderBy('eshop_product_category.id','ASC')       
        ->when($channel, function($query, $channel){
            $query->where('eshop_product_category.gid',$channel);
        }) 
        ->when($status, function($query, $status){
            $query->where('eshop_product_category.status', $status);
        })             
        ->get(); 
        return view('eshop::sales/form/all_category_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel','cat'));

    }
    public function eshop_category_edit(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title
        
        $category       = DB::table('eshop_product_category')
        ->select('eshop_product_category.*','business_type','eshop_product_category.id as catid','eshop_product_category.name as cname')
        ->where('eshop_product_category.id', $request->get('cat_id'))
        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'eshop_product_category.gid')  
        ->orderBy('eshop_product_category.id','ASC')                    
        ->first();

        $resultCat  = DB::table('eshop_product_category')                  
        ->get();

        $resultChannel  = DB::table('tbl_business_type')                  
        ->get(); 

        return view('eshop::sales/form/category_edit', compact('selectedMenu','selectedSubMenu','pageTitle','category','resultChannel'));

    }

    public function eshop_category_update(Request $request){

        $business_type = DB::table('tbl_business_type')->where('business_type_id', $request->get('channel'))->first();  
        DB::table('eshop_product_category')->where('id',$request->get('id'))->update(
            [ 
                'gid'               => $request->get('channel'),  // business_type_id
                'g_name'            => $business_type->business_type,
                'g_code'            => $request->get('company_code'),
                'name'              => $request->get('category_name')              
            ]
        ); 

        return back()->with('success','Product update successfully.'); 
    }
    public function eshop_category_active($id){

            DB::table('eshop_product_category')->where('id',$id)->update(
                [
                    
                    'status'          => 1,  
                    'user'            => Auth::user()->id, 
                ]
            );
             return back()->with('success','Category Inactive successfully.'); 
    }

    public function eshop_category_inactive($id){
 
        DB::table('eshop_product_category')->where('id',$id)->update(
            [
                
                'status'          => 0,  
                'user'       => Auth::user()->id, 
            ]
        );
         return back()->with('success','Category Active successfully.'); 
    }
    public function eshop_for_approved_sales_order(){
        $selectedMenu    = 'for-approved-sales-order';        // Required Variable for menu
        $selectedSubMenu = 'For-approved-sales-order';   // Required Variable for menu
        $pageTitle       = 'For Approved Sales Order';   // Page Slug Title

        $user_type = DB::table('tbl_user_type')->get();
        $management = DB::table('users')->where('user_type_id','5')->get();
        $managers = DB::table('users')->where('user_type_id','6')->get();
        $executive = DB::table('users')->where('user_type_id','3')->get();
        $officer = DB::table('users')->where('user_type_id','7')->get();
  
        $eshop_order = OrderDetails::select('order_id')->where('eshop_order_details.order_status',4)->get(); 
        foreach ($eshop_order as $key => $value) {
            $order_id[] = $value->order_id;
        } 
        if($eshop_order){
            $orders = Order::whereIn('eshop_order.order_id',$order_id)->get();
        } else {
            $orders = Order::where('eshop_order.status',4)->get();
        } 

        return view('eshop::requisition/eshop-for-approved-sales-order',compact('user_type','management','managers','executive','officer','selectedMenu','selectedSubMenu','pageTitle','orders')); 
    }
    public function eshop_for_approved_sales_order_view($DeliveryMainId)
    {
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('eshop_order_details')
            ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.global_company_id')

            ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
            ->where('eshop_order_details.order_status',4)
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                    
            ->where('eshop_order.order_id',$DeliveryMainId)
            ->where('eshop_order_details.order_id',$DeliveryMainId)
            ->groupBy('eshop_order_details.cat_id')                        
            ->get(); 
        $eshop_order = OrderDetails::where('eshop_order_details.order_status',4)->first();   
        if($eshop_order){
            $resultInvoice  = Order::where('eshop_order.order_id',$eshop_order->order_id)->first();
        } else {
            $resultInvoice  = Order::where('eshop_order.status',4)->first();
        } 
        
        $orderCommission = DB::table('eshop_categroy_wise_commission') 
            ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
            ->where('order_id', $resultInvoice->order_id)
            ->groupBy('order_id')  
            ->first();  

        $customerResult = DB::table('eshop_customer_list')
            ->where('customer_id',$resultInvoice->customer_id)
            ->where('status',0)
            ->first();

        $closingResult = DB::table('eshop_outlet_ledger')
            ->where('customer_id',$resultInvoice->customer_id)
            ->orderBy('ledger_id','DESC')
            ->first();

        if(sizeof($closingResult)>0){
            $closingBalance = $closingResult->closing_balance;
        } else {
            $closingBalance = 0;
        } 

        $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value; 

        return view('eshop::sales/adminReport/eshop_for_approved_sales_order_view', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId',  'orderCommission', 'creditSummery'));
    }

    public function eshop_for_approved_sales_order_update(request $request){ 
        if(count($request->approved) > 0){

            DB::table('eshop_order_details') 
            ->where('order_id',$request->orderid)
            ->update([ 
            'order_status' => 5             
            ]); 

            DB::table('eshop_order') 
            ->where('order_id',$request->orderid)
            ->update([ 
            'status' => 5             
            ]); 
            foreach ($request->approved as $key => $value) {
                if($value == 0){
                    DB::table('eshop_order_details')
                        ->where('order_det_id',$key)
                        ->where('order_id',$request->orderid)
                        ->update([ 
                        'order_status' => 4             
                        ]); 
                } 
            } 
        }
        return Redirect::to('/eshop-for-approved-sales-order')->with('success', 'Successfully Approved');  
        return redirect()->back();
    }
} 
