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
	public function eshop_api_add_to_cart_products(Request $request)
    { 

        // dd($request->all());

        // echo '<prev />';

        // exit();
        $json = json_decode(json_encode($request->all()),true);




        // user informaion here
        $po_no         		= $json['invoice_info']['po_no'];
        $order_date     	= $json['invoice_info']['order_date']; 
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
                    'po_no'					=>  $po_no,
                    'total_order_qty'       => $totalQty,
                    'total_order_value'     => $totalValue,
                    'customer_id'           => 1,
                    'party_id'              => 1,
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => $supervisorList->officer_id,
                    //'global_company_id'     => Auth::user()->global_company_id,
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
               // $totalPrice = $orders['order_qty'] * $orders['per_unit_price'];

                 DB::table('eshop_order_details')->insert(
                        [
                            'order_id'          => $lastOrderId->order_id,
                            'order_date'        => $order_date,
                            'sap_code'          => $orders['sap_code'],
                            'cat_id'            => $orders['cat_id'],
                            'product_id'        => $orders['product_id'],
                            'order_qty'         => $orders['order_qty'],
                            'p_unit_price'      => $orders['per_unit_price'],
                            'order_total_value' => $orders['order_value'],
                            'order_det_status'  => 'Confirmed',
                            //'discount_rate'     => $orders['commission'],
                            'party_id'          => $retailerID, 
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]
                    ); 
            }
        }
 		
 		DB::commit();  
			return response()->json(['status' => '1','message' => 'Successfully Order Place']);
		}catch(\Exception $e){
		            DB::rollback();
		            dd($e);
		            return response()->json(['status' => '0','message' => 'Synced Not Successfully']);
		 	}
        
        
    }


	 

}