<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ModernVisitController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

    public function mts_visit()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

         $routeResult = DB::table('mts_route')
                        ->where('status',0)
                        ->get();

         $customerResult = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('mts_customer_list.status',0)
                        ->orderBy('mts_customer_list.name','ASC')    
                        ->get();
      

        return view('ModernSales::sales/visitManage', compact('selectedMenu','pageTitle','routeResult','customerResult'));
    }

     public function mts_partyList(Request $request)
    {

        $customerID = $request->get('customer');

        $resultParty = DB::table('mts_party_list')
                        ->select('party_id','name','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customerID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      

        return view('ModernSales::sales/partyList', compact('resultParty','customerID'));
    }

    public function mts_order_process(Request $request, $partyid,$customer_id)
    {
        $order_id = $request->get('order_id') ?? NULL;
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

        

        $resultParty = DB::table('mts_party_list')
                        ->select('party_id','name','route_id','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customer_id)                       
                        ->where('party_id', $partyid)
                        ->where('status', 0)
                        ->first();
        
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
       

        $resultCart     = DB::table('mts_order')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_status', 'Ordered')
                        ->orderBy('order_id','DESC')
                        ->where('mts_order.order_id', $order_id)
                        // ->when($order_id, function($q, $order_id){
                        //     return $q->where('mts_order.order_id', $order_id);
                        // })                   
                        ->first();
        // $order_id = $resultCart ? $resultCart->order_id : '';
 
        return view('ModernSales::sales/categoryWithOrder', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart','order_id'));
    }

    public function mts_order_manage_process($orderid,$partyid,$customer_id)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

        

        $resultParty = DB::table('mts_party_list')
                        ->select('party_id','name','route_id','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customer_id)                       
                        ->where('party_id', $partyid)
                        ->where('status', 0)
                        ->first();
        
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get(); 
                        
            $resultCart  = DB::table('mts_order')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_id', $orderid)                      
                        ->first();   
        
        
        return view('ModernSales::sales/categoryWithOrderManage', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart','orderid'));
    }

    public function mts_category_products(Request $request)
    {
        $order_id = $request->get('order_id');
        $categoryID = $request->get('categories');
        $party_id     = $request->get('retailer_id');
 
        $orderProduct = DB::table('mts_order_details')
                        ->select('order_det_id','order_id','cat_id','product_id') 
                        ->where('cat_id', $categoryID) 
                        ->where('order_id', $order_id) 
                        ->get();

        $product_id = array();           
        foreach( $orderProduct as $row) {
            $product_id[]=$row->product_id;
        }
        // ->select('id','category_id','name','ims_stat','status','depo AS price','mrp','unit','sap_code')
        //above data is old change data 04/07/2022
        $resultProduct = DB::table('tbl_product')
                        ->select('tbl_product.id','tbl_product.category_id','tbl_product.name','ims_stat','status','mrp AS price','mrp','unit','tbl_product.sap_code','tbl_product_stock.stock_qty')
                        ->leftjoin('tbl_product_stock', 'tbl_product_stock.product_id', '=', 'tbl_product.id') 
                        ->where('ims_stat', '0') 
                        ->where('status', '0')                       
                        ->where('tbl_product.category_id', $categoryID)
                        ->whereNotIn('tbl_product.id', $product_id)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $currentDiscount = DB::table('mts_categroy_wise_commission')        
                        ->where('order_id', $order_id)
                        ->where('party_id', $party_id)
                        ->where('cat_id', $categoryID)
                        ->orderBy('id', 'desc') 
                        ->first();

        $lastDiscount = DB::table('mts_categroy_wise_commission')        
                        ->where('party_id', $party_id)
                        ->where('cat_id', $categoryID)
                        ->orderBy('id', 'desc') 
                        ->first(); 
        return view('ModernSales::sales/allProductList', compact('resultProduct','categoryID','party_id','lastDiscount','currentDiscount'));
    }


    public function mts_add_to_cart_products(Request $request)
    {
        
        $order_id = $request->get('order_id') ?? NULL;
        $customerResult = DB::table('mts_customer_list') 
                        ->where('customer_id',$request->get('customer_id'))
                        ->where('status',0)
                        ->first();
        // dd($request->toArray());
        if(sizeof($customerResult) > 0)
        {
            $sap_code = $customerResult->sap_code;
        }
        else
        {
            $sap_code = 0;
        }

        $autoOrder = DB::table('mts_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
    
        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_order_no + 1;
        }
        else
        {
            $autoOrderId = 1000;
        }    

        $currentYear    = substr(date("Y"), -2); // 2017 to 17
        $currentMonth   = date("m");            // 12
        $currentDay     = date("d");           // 14
        $retailerID     = $request->get('retailer_id');

        $orderNo        = 'SO'.'-'.$sap_code.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;


        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue');


        $countRows = count($request->get('qty'));

        $resultCart     = DB::table('mts_order')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->where('mts_order.order_id', $order_id)
                        // ->when($order_id, function($q, $order_id){
                        //     return $q->where('mts_order.order_id', $order_id);
                        // })
                        ->orderBy('order_id','DESC')                         
                        ->first();
        // dd($resultCart);



        $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();
 
        //dd($supervisorList);

        if(sizeof($resultCart)> 0) 
        { 

         $checkCommission     = DB::table('mts_categroy_wise_commission')
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete();
 
        }

        if(sizeof($resultCart)== 0) 
        {
           
            $lastorderid = DB::table('mts_order')->insertGetId(
                [
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'order_status'          => 'Ordered',
                    'total_order_qty'       => $totalQty,
                    'total_order_value'     => $totalValue,
                    'customer_id'           => $request->get('customer_id'),
                    'party_id'              => $request->get('retailer_id'),
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => Auth::user()->id,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            ); 

            $resultCart     = DB::table('mts_order')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID) 
                        ->where('mts_order.order_id', $lastorderid)                 
                        ->first();
           

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalBeforeDiscount = $request->get('qty')[$m] * $request->get('price')[$m];
                    $discountAmount = ($request->get('dis')[$m] / 100) * $totalBeforeDiscount;
                    $totalAfterDiscount = $totalBeforeDiscount - $discountAmount;

                    $totalPrice = $totalAfterDiscount;
                    // $totalPrice = $request->get('qty')[$m] * $request->get('value')[$m];


                    DB::table('mts_order_details')->insert(
                        [
                            'order_id'          => $lastorderid,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'discount'          => $request->get('dis')[$m],
                            'total_value'  => $request->get('value')[$m],
                            'p_unit_price'      => $request->get('price')[$m], 
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $retailerID,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')
                        ]
                    );

                }

            }
         }else{ 

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    // $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];
                    $totalBeforeDiscount = $request->get('qty')[$m] * $request->get('price')[$m];
                    $discountAmount = ($request->get('dis')[$m] / 100) * $totalBeforeDiscount;
                    $totalAfterDiscount = $totalBeforeDiscount - $discountAmount;
                    $totalPrice = $totalAfterDiscount;

                    DB::table('mts_order_details')->insert(
                        [
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'discount'          => $request->get('dis')[$m],
                            'total_value'       => $request->get('value')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $retailerID,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]
                    );

                }

            }


            $totalOrder = DB::table('mts_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->first();

            DB::table('mts_order')->where('order_id', $resultCart->order_id)                        
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$retailerID)->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );

           
        } 

        $totalCatValue = DB::table('mts_order_details') 
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('cat_id', $request->get('cat_id'))                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID) 
                        ->sum('order_total_value');
        //dd($totalCatValue);

            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalCatValue * $request->get('commission'))/100;
                
                DB::table('mts_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $resultCart->order_id,
                        'cat_id'                => $request->get('cat_id'),
                        'customer_id'           => $request->get('customer_id'),
                        'party_id'              => $request->get('retailer_id'),
                        'fo_id'                 => Auth::user()->id,
                        'order_value'           => $totalValue, 
                        'order_commission_value'=> $commissionValue,
                        'commission'            => $request->get('commission'),
                        'global_company_id'     => Auth::user()->global_company_id,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')
                    ]
                );
            }
           
            return Redirect::to('/mts-order-process/'.$request->get('retailer_id').'/'.$request->get('customer_id').'?order_id='. $resultCart->order_id)->with('success', 'Successfully Added Add To Cart.');
        

    }

    public function mts_manage_add_to_cart_products(Request $request)
    {
       
        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue'); 
        $order_id     = $request->get('order_id');
        $retailerID     = $request->get('retailer_id');
        $countRows = count($request->get('qty')); 

        $resultCart     = DB::table('mts_order')
                        ->where('order_id', $order_id)                        
                        // ->where('fo_id', Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('order_id','DESC')                         
                        ->first(); 

        // $supervisorList = DB::table('mts_role_hierarchy')
        //                 ->where('status',0)                        
        //                 ->where('officer_id',Auth::user()->id) 
        //                 ->first();
 
        //dd($supervisorList);

        if(sizeof($resultCart)> 0) 
        {

         /*$checkOrder     = DB::table('mts_order_details')
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete();*/ 

         $checkCommission     = DB::table('mts_categroy_wise_commission')
                        ->where('order_id', $resultCart->order_id)                        
                        // ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete();
 
        } 

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    DB::table('mts_order_details')->insert(
                        [
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $retailerID,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]
                    );

                }

            }


            $totalOrder = DB::table('mts_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->first();

             DB::table('mts_order')->where('order_id', $resultCart->order_id)                        
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$retailerID)->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            ); 


            $totalCatValue = DB::table('mts_order_details') 
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('cat_id', $request->get('cat_id'))                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID) 
                        ->sum('order_total_value');    

            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalCatValue * $request->get('commission'))/100;
                
                DB::table('mts_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $resultCart->order_id,
                        'cat_id'                => $request->get('cat_id'),
                        'customer_id'           => $request->get('customer_id'),
                        'party_id'              => $request->get('retailer_id'),
                        'fo_id'                 => Auth::user()->id,
                        'order_value'           => $totalValue, 
                        'order_commission_value'=> $commissionValue,
                        'commission'            => $request->get('commission'),
                        'global_company_id'     => Auth::user()->global_company_id,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')
                    ]
                );
            }
           
            return Redirect::to('/mts-order-manage-process/'.$request->get('order_id').'/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
        

    }

    public function mts_bucket(Request $request, $customer_id,$partyid)
    { 
        $order_id = $request->get('order_id');
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title  
       
        $resultCartPro  = DB::table('mts_order_details')
                        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status',
                        'mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order_details.order_date') 
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id') 
                        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id')
                        ->where('mts_order.order_status','Ordered')                        
                        ->where('mts_order.fo_id',Auth::user()->id)                        
                        ->where('mts_order.party_id',$partyid)
                        ->where('mts_order.order_id', $order_id)
                        // ->when($order_id, function($q, $order_id){
                        //     return $q->where('mts_order.order_id', $order_id);
                        // })
                        ->groupBy('mts_order_details.cat_id')                        
                        ->get();


            $resultInvoice  = DB::table('mts_order')->select('order_id','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->where('mts_order.order_id', $order_id)
                        // ->when($order_id, function($q, $order_id){
                        //     return $q->where('mts_order.order_id', $order_id);
                        // })
                        ->first();
            
            $resultFoInfo   = DB::table('mts_order')
                        ->select('mts_order.order_no','mts_order.order_date','users.id','users.email','users.display_name','mts_customer_list.name as cname','mts_party_list.name as partyName','mts_party_list.address','mts_customer_list.sap_code')
                         ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_order.customer_id')
                         ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
                         ->join('users', 'users.id', '=', 'mts_order.fo_id')
                         ->where('order_id', $resultInvoice->order_id)  
                         ->first(); 

            $orderCommission = DB::table('mts_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $resultInvoice->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

            $customerResult = DB::table('mts_customer_list')
                        ->where('customer_id',$customer_id)
                        ->where('status',0)
                        ->first();

            $closingResult = DB::table('mts_outlet_ledger')
                        ->where('customer_id',$customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();
            $customerCreditBalance = DB::table('customer_ledger_balance')
                        ->where('customer_id',$customer_id)
                        ->orderBy('id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

            //    $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        
           $creditSummery = $customerResult->credit_limit - ($customerCreditBalance ? $customerCreditBalance->last_balance : 0) - $resultInvoice->total_order_value;        
            // dd($resultInvoice);
            //echo $closingBalance;

        return view('ModernSales::sales/bucket',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery','resultFoInfo','customerCreditBalance','order_id'));
   

    }

    public function mts_items_edit(Request $request)
    {

        $partyid        = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $catid          = $request->get('catid');
        $orderid          = $request->get('orderid');

        $mts_order = DB::table('mts_order')->where('order_id', $orderid)->first();

        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }
        
        $resultPro  = DB::table('mts_order_details')
                        ->select('mts_order_details.order_det_id','mts_order_details.order_id','mts_order_details.product_id',
                        'mts_order_details.order_qty','mts_order_details.order_qty',
                        'mts_order_details.p_unit_price','tbl_product.name','tbl_product.sap_code')
                        ->join('tbl_product', 'tbl_product.id', '=', 'mts_order_details.product_id')
                        ->where('mts_order_details.order_det_id', $request->get('itemsid'))
                        ->first();

        $checkCommission     = DB::table('mts_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by', $supervisorList->officer_id)                        
                                ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)
                                ->first();

        //dd($checkCommission);

        $resultCatDefault  = DB::table('tbl_product_category')
                        ->select('id')                        
                        ->where('id', $request->get('catid'))
                        ->first();

        return view('ModernSales::sales/editItems', compact('resultPro','partyid','customer_id','catid','resultCatDefault','checkCommission'));
    }


    public function mts_items_edit_submit(Request $request)
    {

        $partyid    = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $catid      = $request->get('catid');
        $orderid    = $request->get('orderid');  

        // dd($request);

        $mts_order = DB::table('mts_order')->where('order_id', $orderid)->first();

        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }

        
        $price          = $request->get('items_qty') * $request->get('items_price');
        
        DB::table('mts_order_details')->where('order_det_id',$request->get('id'))->update(
            [
                'order_qty'         => $request->get('items_qty'), 
                'order_total_value' => $price, 
                'order_date'        => date('Y-m-d H:i:s') 
            ]
        ); 

        DB::table('mts_order_details')->where('order_id',$orderid)->where('cat_id',$catid)->update(
            [
                'discount_rate'         => $request->get('commission') 
            ]
        );     

        $totalOrder = DB::table('mts_order_details') 
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by', $supervisorList->officer_id )                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('mts_order')->where('order_id', $orderid)->where('fo_id', $supervisorList->officer_id )->where('entry_by', $supervisorList->officer_id )->update(
            [ 
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        //dd($orderid);
         $checkCommission     = DB::table('mts_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                // ->where('entry_by', $supervisorList->officer_id)                        
                                // ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)
                                ->delete();        
        // dd( $checkCommission );                
                                //->delete();  

        $totalOrder = DB::table('mts_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        // ->where('entry_by', $supervisorList->officer_id)                        
                        // ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        ->first();  

        $totalValue = $totalOrder->totalValue;
        if(sizeof($request->get('commission'))>0){
            $commissionValue = ($totalValue * $request->get('commission'))/100;
            
            DB::table('mts_categroy_wise_commission')->insert(
                [
                    'order_id'              => $orderid,
                    'cat_id'                => $catid,
                    'customer_id'           => $customer_id,
                    'party_id'              => $partyid,
                    'fo_id'                 => $supervisorList->officer_id,
                    'order_value'           => $totalValue, 
                    'order_commission_value'=> $commissionValue,
                    'commission'            => $request->get('commission'),
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => $supervisorList->officer_id,
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );
        }          

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');

    }


    public function mts_items_delete($orderid,$itemid,$customer_id,$partyid,$catid){

        $mts_order = DB::table('mts_order')->where('order_id', $orderid)->first();

        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }
        
        $checkCommission     = DB::table('mts_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by', $supervisorList->officer_id)                        
                                ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)                        
                                ->delete(); 
                                

        $totalOrder = DB::table('mts_order_details')
                        ->select('order_det_id','order_id','party_id','cat_id','product_id','discount_rate','entry_by',DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        // ->where('entry_by', $supervisorList->officer_id)                        
                        ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        // ->where('order_det_id','!=',$itemid)
                        ->first();
                        
       
        if(sizeof($totalOrder)>0){

            $totalValue = $totalOrder->totalValue;
            $discount = $totalOrder->discount_rate;
            $commissionValue = ($totalValue *  $discount)/100;
            
            DB::table('mts_categroy_wise_commission')->insert(
                [
                    'order_id'              => $orderid,
                    'cat_id'                => $catid,
                    'customer_id'           => $customer_id,
                    'party_id'              => $partyid,
                    'fo_id'                 => $supervisorList->officer_id,
                    'order_value'           => $totalValue, 
                    'order_commission_value'=> $commissionValue,
                    'commission'            => $discount,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => $supervisorList->officer_id,
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );
        } 

        $itemdelete = DB::table('mts_order_details')
                        ->where('order_id', $orderid)                        
                        // ->where('entry_by', $supervisorList->officer_id)                        
                        ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        ->where('order_det_id',$itemid) 
                        ->delete(); 
                        
        $totalOrder2 = DB::table('mts_order_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        // ->where('entry_by', '1501')                        
                        ->where('party_id',$partyid)
                        ->first();
                        // dd( $supervisorList->officer_id, $supervisorList, $totalOrder2, $itemdelete, $checkCommission,  $totalOrder  );

        DB::table('mts_order')->where('order_id', $orderid)->where('fo_id',  $supervisorList->officer_id)->where('entry_by', $supervisorList->officer_id)->update(
            [ 
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );
          

        return Redirect::back()->with('success', 'Successfully Delete Product.');
    }

    public function mts_confirm_order(Request $request)
    {

        $orderid    = $request->get('orderid');
        $partyid    = $request->get('partyid');
        $customer_id= $request->get('customer_id');
        $po_no      = $request->get('po_no');

        $mts_order = DB::table('mts_order')->where('order_id', $orderid)->first(); 
        $visit_order = DB::table('mts_visit_order')->where('order_id', $orderid)->where('visit',0)->where('non_visit',0)->first();

        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }

        $totalOrder = DB::table('mts_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)
                        ->first();
             

        DB::table('mts_order')->where('order_id', $orderid)->update(
            [
                'po_no'                 => $po_no, 
                'order_status'          => 'Confirmed',
                'ack_status'            => 'Pending',  
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        DB::table('mts_order_details')
                ->where('order_id', $orderid)
                ->update(
                    [
                        'order_det_status'          => 'Confirmed'
                    ]
            );   
        if(!$visit_order){
            DB::table('mts_visit_order')->insert(
                [
                    'date'                  => date('Y-m-d'),
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'officer_id'            => $supervisorList->officer_id,  
                    'customer_id'           => $customer_id,
                    'party_id'              => $partyid, 
                    'visit'                 => 0,    
                    'non_visit'             => 0,    
                    'order'                 => 1,    
                    'order_id'              => $orderid, 
                    'remarks'               => $request->get('remarks'),    
                    'created_by'            => Auth::user()->id,
                    'created_at'            => date('Y-m-d H:i:s'), 
                    'status'                => 0 
                ]
            );
        }


    return Redirect::to('/mts-visit')->with('success', 'Successfully Confirmed Order');

    }


    public function mts_delete_order($orderid,$partyid,$customer_id)
    {
        $checkCommission     = DB::table('mts_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$partyid)                     
                                ->delete(); 

        $totalOrder = DB::table('mts_order_details')
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->delete();

         $totalOrder = DB::table('mts_order')
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->delete();

        $totalOrder = DB::table('mts_visit_order')
                        ->where('order_id', $orderid)                        
                        ->where('party_id',$partyid)
                        ->delete();


    return Redirect::to('/mts-visit')->with('success', 'Successfully Delete Order');

    }



   public function mts_order_not_approve()
    {
        $selectedMenu   = 'Order Not approve';                      // Required Variable for menu
        $selectedSubMenu= 'Order Not approve';                    // Required Variable for submenu
        $pageTitle      = 'Order Not approve';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_order')
        ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_order.order_status', 'Ordered')
        ->where('mts_order.ack_status', 'Rejected')
        ->where('mts_order.global_company_id', Auth::user()->global_company_id)
        ->where('mts_order.fo_id', Auth::user()->id)
        ->groupBy('mts_order.fo_id')
        ->orderBy('mts_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
         $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->where('mts_order.order_status', 'Ordered')
            ->where('mts_order.ack_status', 'Rejected')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
  
        return view('ModernSales::sales/orderNotApprove', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_order_not_approve_list(Request $request)
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
            ->where('mts_order.order_status', 'Ordered')
            ->where('mts_order.ack_status', 'Rejected')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
            ->where('mts_order.order_status', 'Ordered')
            ->where('mts_order.ack_status', 'Rejected')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->where('mts_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/orderNotApproveList', compact('resultOrderList'));
    }

    public function mts_order_manage()
    {
        $selectedMenu   = 'Order Manage';            // Required Variable for menu
        $selectedSubMenu= 'Order Manage';            // Required Variable for submenu
        $pageTitle      = 'Order Manage';            // Page Slug Title
       
        if(Auth::user()->user_type_id ==3){
            $mts_hierarchy = DB::table('mts_role_hierarchy')
                ->where('executive_id', Auth::user()->id)->get();
            // $officer_ids = $mts_hierarchy->pluck('officer_id');
            $user_id = $mts_hierarchy->pluck('officer_id');
        }else{
            $user_id = array(Auth::user()->id);
        } 

        $resultFO       = DB::table('mts_order')
            ->select('mts_order.global_company_id','mts_order.order_status','mts_order.order_id','mts_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->whereIn('mts_order.order_status', ['Ordered','Confirmed'])
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereIn('mts_order.fo_id', $user_id)
            // ->groupBy('mts_order.fo_id')
            // ->orderBy('mts_order.order_id','DESC')                    
            ->get();


        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->whereIn('mts_order.order_status', ['Ordered','Confirmed'])
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereIn('mts_order.fo_id', $user_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            // ->orderBy('mts_order.order_id','DESC')                    
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
  
        return view('ModernSales::sales/orderManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }
    public function mts_order_manageList(Request $request)
    {
        $selectedMenu   = 'Order Manage';            // Required Variable for menu
        $selectedSubMenu= 'Order Manage';            // Required Variable for submenu
        $pageTitle      = 'Order Manage';            // Page Slug Title


        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate'))); 
        $executive_id    = $request->get('executive_id');
        $officer    = $request->get('fos');
       
        if(Auth::user()->user_type_id ==3){
            $mts_hierarchy = DB::table('mts_role_hierarchy')
                ->where('executive_id', Auth::user()->id)->get(); 
            $user_id = $mts_hierarchy->pluck('officer_id');
        }else{
            $user_id = array(Auth::user()->id);
        }  
        $resultOrderList = DB::table('mts_order')
            ->select('mts_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
            ->whereIn('mts_order.order_status', ['Ordered','Confirmed'])
            ->where('mts_order.ack_status', 'Pending')
            ->where('mts_order.global_company_id', Auth::user()->global_company_id)
            ->whereIn('mts_order.fo_id', $user_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_order.order_id','DESC')                    
            ->get(); 
  
        return view('ModernSales::sales/orderManageList', compact('selectedMenu','selectedSubMenu','pageTitle','resultOrderList'));
    } 
    public function mts_order_manage_delete(Request $request, $order_id)
    {         
        $mts_order = DB::table('mts_order')->where('order_id', $order_id)->first();
        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }
        DB::table('mts_order')->where('order_id', $order_id)->where('fo_id',$supervisorList->officer_id)->delete();
        DB::table('mts_order_details')->where('order_id', $order_id)->where('entry_by',$supervisorList->officer_id)->delete(); 
        DB::table('mts_categroy_wise_commission')->where('order_id', $order_id)->where('fo_id',$supervisorList->officer_id)->delete(); 

        return redirect()->back(); //->withErrors($validator)->withInput();
   
    }

     public function mts_bucket_manage($order_id,$customer_id,$partyid)
    { 
        $mts_order = DB::table('mts_order')->where('order_id', $order_id)->first();

        if(Auth::user()->user_type_id ==3){
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('officer_id', $mts_order->fo_id)
                ->where('executive_id', Auth::user()->id)
                ->first();  
        }else{
            $supervisorList = DB::table('mts_role_hierarchy')
                ->where('status',0)                        
                ->where('officer_id', Auth::user()->id) 
                ->first();
        }

        $user_id = $supervisorList->officer_id;

        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title 

       $resultFoInfo   = DB::table('mts_order')
                        ->select('mts_order.order_no','mts_order.order_date','users.id','users.email','users.display_name','mts_customer_list.name as cname','mts_party_list.name as partyName','mts_party_list.address','mts_customer_list.sap_code')
                         ->join('mts_customer_list', 'mts_customer_list.customer_id', '=', 'mts_order.customer_id')
                         ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_order.party_id')
                         ->join('users', 'users.id', '=', 'mts_order.fo_id')
                         ->where('mts_order.order_id', $order_id) 
                         ->first(); 

       
         $resultCartPro  = DB::table('mts_order_details')
                        ->select('mts_order_details.cat_id','mts_order_details.order_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','mts_order.order_id','mts_order.fo_id','mts_order.order_status',
                        'mts_order.order_no','mts_order.po_no','mts_order.party_id','mts_order_details.order_date') 
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_order_details.cat_id') 
                        ->join('mts_order', 'mts_order.order_id', '=', 'mts_order_details.order_id') 
                        ->where('mts_order.order_id', $order_id)                         
                        ->where('mts_order.fo_id', $supervisorList->officer_id)                        
                        ->where('mts_order.party_id',$partyid)
                        ->groupBy('mts_order_details.cat_id')                        
                        ->get();


            $resultInvoice  = DB::table('mts_order')->select('order_id','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_id', $order_id)                       
                        ->where('fo_id', $supervisorList->officer_id )                        
                        ->where('party_id',$partyid)
                        ->first();

            $orderCommission = DB::table('mts_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $order_id)                        
                        ->where('entry_by',$supervisorList->officer_id )                        
                        ->where('party_id', $partyid)
                        ->first();

            $customerResult = DB::table('mts_customer_list')
                        ->where('customer_id', $customer_id)
                        ->where('status',0)
                        ->first();

             $closingResult = DB::table('mts_outlet_ledger')
                        ->where('customer_id', $customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 
            // dd($customerResult,$resultInvoice );

           $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        

            //echo $closingBalance;


        return view('ModernSales::sales/bucketManage',compact('selectedMenu','pageTitle','order_id','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery','resultFoInfo','user_id'));
   

    }

    public function mts_order_visit($party_id,$customer_id)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('mts_visit_reason')
                        ->where('type', 1)                    
                        ->get();

         return view('ModernSales::sales/orderVisitOnly', compact('selectedMenu','pageTitle','party_id','customer_id','resultReason')); 
    }  


    public function mts_visit_process_submit(Request $request)
    {

        $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first(); 

        DB::table('mts_visit_order')->insert(
            [
                'date'                  => date('Y-m-d'),
                'management_id'         => $supervisorList->management_id,
                'manager_id'            => $supervisorList->manager_id,
                'executive_id'          => $supervisorList->executive_id,
                'officer_id'            => Auth::user()->id,  
                'customer_id'           => $request->get('customer_id'),
                'party_id'              => $request->get('party_id'),   
                'reasons_id'            => $request->get('reasons'), 
                'visit'                 => 1,    
                'non_visit'             => 0,    
                'order'                 => 0,    
                'order_id'              => 0, 
                'remarks'               => $request->get('remarks'),    
                'created_by'            => Auth::user()->id,
                'created_at'            => date('Y-m-d H:i:s'), 
                'status'                => 0 
            ]
        );

        return Redirect::to('/mts-visit')->with('success', 'Successfully Visit Done.'); 
    }

    public function mts_nonvisit($party_id,$customer_id)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('mts_visit_reason')
                        ->where('type', 2)                    
                        ->get();

         return view('ModernSales::sales/orderNonVisitOnly', compact('selectedMenu','pageTitle','party_id','customer_id','resultReason')); 
    }  


    public function mts_nonvisit_process_submit(Request $request)
    {

        $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first(); 

        DB::table('mts_visit_order')->insert(
            [
                'date'                  => date('Y-m-d'),
                'management_id'         => $supervisorList->management_id,
                'manager_id'            => $supervisorList->manager_id,
                'executive_id'          => $supervisorList->executive_id,
                'officer_id'            => Auth::user()->id,  
                'customer_id'           => $request->get('customer_id'),
                'party_id'              => $request->get('party_id'),   
                'reasons_id'            => $request->get('reasons'), 
                'visit'                 => 0,    
                'non_visit'             => 1,    
                'order'                 => 0,    
                'order_id'              => 0, 
                'remarks'               => $request->get('remarks'),    
                'created_by'            => Auth::user()->id,
                'created_at'            => date('Y-m-d H:i:s'), 
                'status'                => 0 
            ]
        );

        return Redirect::to('/mts-visit')->with('success', 'Successfully Visit Done.'); 
    }
   
}

