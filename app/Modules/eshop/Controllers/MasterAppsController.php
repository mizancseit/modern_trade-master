<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class MasterAppsController extends Controller
{
    public function eshop_api_add_to_cart_products(Request $request){          
        $json = json_decode(json_encode($request->all()),true);    
        $orders = array(); 
        $data = array();
        $items = array();
        $customer = array(); 
        foreach ($json as $key => $value) { 
            foreach ($value as $key2 => $value2) {  
                if (filter_var($key2, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "valid email format ". $key2 .'<br>'; 
                    $customer = $value2;
                }else{
                    $items[] = $value2;
                }  
            } 
            //$api_items = array('order_id' => $key,'order_items' => $items,'customer' => $customer, 'status' => 0);
            
            $eshop_api_orders_temp = DB::table('eshop_api_orders_temp')->where('order_id',$key)->first();
            if(!$eshop_api_orders_temp){
                DB::table('eshop_api_orders_temp')->insert([
                    'order_id' => $key,
                    'order_items' => json_encode($items),
                    'customer' => json_encode($customer),
                    'status' => 0,
                    'created_at' => date('Y-m-d h:i:s')                    
                ]);
            }
            $items = array();
            $this->eshop_api_add_to_cart_orders();
        }  
    }
    public function eshop_api_add_to_cart_orders(){
        $ordersItems = array();
        $eshop_api_orders_temp = DB::table('eshop_api_orders_temp')->where('status',0)->get(); 
        $i=0; 
        foreach ($eshop_api_orders_temp as $key => $value) {  
            $autoOrder    = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
            $autoOrderId  = $autoOrder ? $autoOrder->auto_order_no : rand(100000,999999);   
            $orderNo      = 'SO-'.rand(100000,999999).'-'.substr(date("Y"), -2).date("m-d").'-'.$autoOrderId;
            $total_order_qty   = 0;
            $total_order_value = 0; 
            $customerInfo = json_decode($value->customer);
            $party    = DB::table('eshop_party_list')->where('email',$customerInfo->billing_email)->first();  
            if(!$party){
                $party_id = DB::table('eshop_party_list')->insertGetId([
                    'name' => $customerInfo->billing_first_name .' '.$customerInfo->billing_last_name,
                    'owner' => '',
                    'mobile' => $customerInfo->billing_phone,
                    'email' => $customerInfo->billing_email,
                    'address' => $customerInfo->billing_address_1.' '.$customerInfo->billing_address_2.' '.$customerInfo->billing_city.' '.$customerInfo->billing_state.' '.$customerInfo->billing_postcode,
                    'opening_balance' => 0,
                    'reminding_commission_balance' => 0,
                    'division' => '', 
                    'customer_id' => 1,
                    'route_id' => 1,
                    'shop_type' => 1,
                    'sap_code' => '',
                    'global_company_id' => 1, 
                    'approval' => 0,
                    'status' => 0,
                    'entry_date' => date('Y-m-d H:i:s')]
                );
            }else{
                $party_id = $party->party_id;
            }
            $supervisorList = DB::table('eshop_role_hierarchy')->where('status',0)->first();
            $order_id = DB::table('eshop_order')->insertGetId(
                [
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId+1,
                    'order_date'            => date('Y-m-d H:i:s'),
                    'order_status'          => 'Confirmed',
                    'po_no'                 => $value->order_id, 
                    'customer_id'           => 1,
                    'party_id'              => $party_id,
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => $supervisorList->officer_id,
                    'global_company_id'     => 1, 
                    'entry_date'            => date('Y-m-d h:i:s')                
                ]
            );
            $order_items = json_decode($value->order_items);
            foreach ($order_items as $key => $item) {  
                $product = DB::table('eshop_product')->where('sap_code',$item->sap_code)->first();  
                if($product){
                    $total_order_qty +=$item->order_qty;
                    $total_order_value +=$item->per_unit_price; 
                    $ordersItems[] = array(
                        'order_id'          => $order_id,
                        'order_date'        => date('Y-m-d H:i:s'),
                        'sap_code'          => $product->sap_code,
                        'cat_id'            => $product->category_id,
                        'product_id'        => $product->id,
                        'order_qty'         => $item->order_qty,
                        'p_unit_price'      => $item->per_unit_price,
                        'order_total_value' => $item->order_value,  
                        'order_det_status'  => 'Confirmed', 
                        'party_id'          => $party_id, 
                        'entry_date'        => date('Y-m-d h:i:s')
                    ); 
                }
            }
            DB::table('eshop_order')
                ->where('order_id', $order_id)
                ->update(['total_order_qty' => $total_order_qty,'total_order_value' => $total_order_value]);
            DB::table('eshop_order_details')->insert($ordersItems); 
            $ordersItems = array(); 
        } 
        DB::table('eshop_api_orders_temp')->where('status',0)->update(['status' => 1]); 
    }
    public function eshop_api_add_to_cart_products_old(Request $request)
    { 
 
       
        $json = json_decode(json_encode($request->all()),true); 

        // user informaion here
        $po_no              = $json['invoice_info']['po_no'];
        $order_date         = $json['invoice_info']['order_date']; 
        $retailerID         = 10101; 

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        //->where('officer_id',Auth::user()->id) 
                        ->first();
        // order

        $autoOrder = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();

        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_order_no + 1;
        }
        else
        {
            $autoOrderId = 10000;
        }    

        $currentYear    = substr(date("Y"), -2); // 2017 to 17
        $currentMonth   = date("m");            // 12
        $currentDay     = date("d");    

        $orderNo        = $retailerID.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;


        $totalQty   = 0;
        $totalValue = 0; 

        foreach ($json['order'] as $orders)
        {

            if($orders['order_qty']!='')
            {
                $totalQty   = $totalQty + $orders['order_qty'];
                $totalValue = $totalValue + $orders['order_value'];
            }            
        }

        try{
        
            DB::beginTransaction(); 

        // order insert here
        
        DB::table('eshop_order')->insert(
                [
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => $order_date,
                    'order_status'          => 'Confirmed',
                    'po_no'                 =>  $po_no,
                    'total_order_qty'       => $totalQty,
                    'total_order_value'     => $totalValue,
                    'customer_id'           => 1,
                    'party_id'              => 1,
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => $supervisorList->officer_id,
                    'global_company_id'     => 1,
                    //'entry_by'              => Auth::user()->id,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );

        // order last id here
        $lastOrderId = DB::table('eshop_order')->latest('order_id')->first(); // order id

        foreach ($json['order'] as $orders)
        {
            if($orders['order_qty']!='')
            {
                DB::table('eshop_order_details_temp')->insert(
                    [
                        'order_id'          => $lastOrderId->order_id,
                        'order_date'        => $json['invoice_info']['order_date'],
                        'sap_code'          => $orders['sap_code'],
                        'cat_id'            => $orders['cat_id'],
                        'product_id'        => $orders['product_id'],
                        'order_qty'         => $orders['order_qty'],
                        'p_unit_price'      => $orders['per_unit_price'],
                        'order_total_value' => $orders['order_value'],
                        'entry_date'        => date('Y-m-d h:i:s')
                    ]
                );
            }
        }

        $tempPro = DB::table('eshop_order_details_temp')
        ->select('eshop_order_details_temp.*', DB::raw('SUM(order_qty) AS order_qty'), DB::raw('SUM(order_total_value) AS order_value'))
        ->groupBy('product_id')
        ->get();

        //dd($tempPro);

        foreach ($tempPro as $key => $orders) {
            
            DB::table('eshop_order_details')->insert(
                [
                    'order_id'          => $lastOrderId->order_id,
                    'order_date'        => $order_date,
                    'sap_code'          => $orders->sap_code,
                    'cat_id'            => $orders->cat_id,
                    'product_id'        => $orders->product_id,
                    'order_qty'         => $orders->order_qty,
                    'p_unit_price'      => $orders->p_unit_price,
                    'order_total_value' => $orders->order_value,
                    'order_det_status'  => 'Confirmed',
                    //'discount_rate'     => $orders['commission'],
                    'party_id'          => $retailerID, 
                    'entry_date'        => date('Y-m-d h:i:s')

                ]
            );
        }

        DB::table('eshop_order_details_temp')->truncate();
         

        // foreach ($json['order'] as $orders)
        // {
        //     if($orders['order_qty']!='')
        //     {
        //        // $totalPrice = $orders['order_qty'] * $orders['per_unit_price'];

        //          DB::table('eshop_order_details')->insert(
        //                 [
        //                     'order_id'          => $lastOrderId->order_id,
        //                     'order_date'        => $order_date,
        //                     'sap_code'          => $orders['sap_code'],
        //                     'cat_id'            => $orders['cat_id'],
        //                     'product_id'        => $orders['product_id'],
        //                     'order_qty'         => $orders['order_qty'],
        //                     'p_unit_price'      => $orders['per_unit_price'],
        //                     'order_total_value' => $orders['order_value'],
        //                     'order_det_status'  => 'Confirmed',
        //                     //'discount_rate'     => $orders['commission'],
        //                     'party_id'          => $retailerID, 
        //                     'entry_date'        => date('Y-m-d h:i:s')

        //                 ]
        //             ); 
        //     }
        // }
        
        DB::commit();  
            return response()->json(['status' => '1','message' => 'Successfully Order Place']);
        }catch(\Exception $e){
                    DB::rollback();
                    dd($e);
                    return response()->json(['status' => '0','message' => 'Synced Not Successfully']);
            }
        
        
    }


     

}