<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Excel; 
use DB;
use Auth;
use Session;

class EshopVisitController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

    public function eshop_visit()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

         $routeResult = DB::table('eshop_route')
                        ->where('status',0)
                        ->get();

         $customerResult = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();
      

        return view('eshop::sales/visitManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','customerResult'));
    }

     public function eshop_partyList(Request $request)
    {

        $customerID = $request->get('customer');

        $resultParty = DB::table('eshop_party_list')
                        ->select('party_id','name','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customerID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      

        return view('eshop::sales/partyList', compact('resultParty','customerID'));
    }

    public function eshop_order_process($partyid,$customer_id)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

        

        $resultParty = DB::table('eshop_party_list')
                        ->select('party_id','name','route_id','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customer_id)                       
                        ->where('party_id', $partyid)
                        ->where('status', 0)
                        ->first();
        
        $resultCategory = DB::table('eshop_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
       

        $resultCart     = DB::table('eshop_order')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_status', 'Ordered')                       
                        ->first();  

        $customerResult = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();

        
        
        return view('eshop::sales/categoryWithOrder', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart' ,'customerResult'));
    }

    public function eshop_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $party_id     = $request->get('retailer_id');

        $resultProduct = DB::table('eshop_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit','sap_code')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $lastDiscount = DB::table('eshop_categroy_wise_commission')        
                        ->where('party_id', $party_id)
                        ->where('cat_id', $categoryID)
                        ->orderBy('id', 'desc') 
                        ->first();

        return view('eshop::sales/allProductList', compact('resultProduct','categoryID','party_id','lastDiscount'));
    }


    public function eshop_add_to_cart_products(Request $request)
    {
        
        $customerResult = DB::table('eshop_customer_list') 
                        ->where('customer_id',$request->get('customer_id'))
                        ->where('status',0)
                        ->first();

        if(sizeof($customerResult) > 0){
            $sap_code = $customerResult->sap_code;
        }else{
            $sap_code = 0;
        }
        $party_id     = $request->get('party_id');
        $autoOrder    = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
        $autoOrderId  = $autoOrder ? $autoOrder->auto_order_no : rand(100000,999999);   
        $orderNo      = 'SO-'.rand(100000,999999).'-'.substr(date("Y"), -2).date("m-d").'-'.$autoOrderId;

        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue');


        $countRows = count($request->get('qty'));

        $resultCart = DB::table('eshop_order')
                    ->where('order_status', 'Ordered')                        
                    ->where('fo_id',Auth::user()->id)                        
                    ->where('party_id',$party_id)
                    ->orderBy('order_id','DESC')                         
                    ->first();  

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();  
        //dd($supervisorList);

        // if(sizeof($resultCart)> 0) {
        //     $checkOrder = DB::table('eshop_order_details')->where('order_id', $resultCart->order_id)->delete(); 
        //     $checkCommission     = DB::table('eshop_categroy_wise_commission')
        //                 ->where('order_id', $resultCart->order_id)                        
        //                 ->where('entry_by',Auth::user()->id)                        
        //                 ->where('party_id',$party_id)                        
        //                 ->where('cat_id',$request->get('cat_id'))                        
        //                 ->delete();
 
        // }

        if(sizeof($resultCart)== 0) { 
            $eshop_order_id = DB::table('eshop_order')->insertGetId([
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'order_status'          => 'Ordered',
                    'total_order_qty'       => $totalQty,
                    'total_order_value'     => $totalValue,
                    'customer_id'           => $request->get('customer_id'),
                    'party_id'              => $request->get('party_id'),
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => Auth::user()->id,
                    'entry_date'            => date('Y-m-d h:i:s')                    
                ]);   

            for($m=0;$m<$countRows;$m++) {
                if($request->get('qty')[$m]!='') {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];
                    $order_detail = DB::table('eshop_order_details')
                    ->where('product_id', $request->get('produuct_id')[$m])->where('order_id',$eshop_order_id)->first();
                    if($order_detail){
                        DB::table('eshop_order_details')->where('order_id', $eshop_order_id)
                        ->where('product_id', $request->get('produuct_id')[$m])->update([
                            'order_qty'         => $request->get('qty')[$m] + $order_detail->order_qty,
                            'order_total_value' => $totalPrice + $order_detail->order_total_value, 
                        ]);
                    }else{ 
                        DB::table('eshop_order_details')->insert([
                            'order_id'          => $eshop_order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'sap_code'          => $request->get('sap_code')[$m],
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'item_discount'     => $request->get('item_discount')[$m],
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $party_id,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')
                        ]);
                    }
                }
            }
            $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(item_discount) AS total_discount'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $eshop_order_id)
                        ->first();

            DB::table('eshop_order')->where('order_id', $eshop_order_id)->where('entry_by',Auth::user()->id)
                ->where('party_id',$party_id)->update([
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'total_discount'        => $totalOrder->total_discount,
                    'entry_date'            => date('Y-m-d h:i:s')                    
                ]);  
            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $eshop_order_id,
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
        }else{ 
            for($m=0;$m<$countRows;$m++){
                if($request->get('qty')[$m]!=''){
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m]; 
                    $order_detail = DB::table('eshop_order_details')
                    ->where('product_id', $request->get('produuct_id')[$m])->where('order_id',$resultCart->order_id)->first();
                    if($order_detail){
                        DB::table('eshop_order_details')->where('order_id', $resultCart->order_id)
                        ->where('product_id', $request->get('produuct_id')[$m])->update([
                            'order_qty'         => $request->get('qty')[$m] + $order_detail->order_qty,
                            'order_total_value' => $totalPrice + $order_detail->order_total_value, 
                        ]);
                    }else{ 
                        DB::table('eshop_order_details')->insert([
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'sap_code'          => $request->get('sap_code')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'item_discount'     => $request->get('item_discount')[$m],
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $party_id,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]);
                    }
                }
            } 
            $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'),  DB::raw('SUM(item_discount) AS total_discount'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$party_id)
                        ->first();

            DB::table('eshop_order')->where('order_id', $resultCart->order_id)  
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$party_id)->update([
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'total_discount'        => $totalOrder->total_discount,
                    'entry_date'            => date('Y-m-d h:i:s')
                ]); 
            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
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
        } 
        return Redirect::to('/eshop-order-process/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
        

    }
    public function eshop_add_to_cart_products_old(Request $request)
    {
        
        $customerResult = DB::table('eshop_customer_list') 
                        ->where('customer_id',$request->get('customer_id'))
                        ->where('status',0)
                        ->first();

        if(sizeof($customerResult) > 0)
        {
            $sap_code = $customerResult->sap_code;
        }
        else
        {
            $sap_code = 0;
        }

        $autoOrder = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
    
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

        $resultCart     = DB::table('eshop_order')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('order_id','DESC')                         
                        ->first(); 

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();
 
        //dd($supervisorList);

        if(sizeof($resultCart)> 0) 
        {

         $checkOrder     = DB::table('eshop_order_details')
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete(); 

         $checkCommission     = DB::table('eshop_categroy_wise_commission')
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete();
 
        }

        if(sizeof($resultCart)== 0) 
        {
           
            DB::table('eshop_order')->insert(
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

           $resultCart     = DB::table('eshop_order')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('order_id','DESC')                         
                        ->first(); 
           

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    DB::table('eshop_order_details')->insert(
                        [
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'sap_code'          => $request->get('sap_code')[$m],
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
         }else{ 

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    DB::table('eshop_order_details')->insert(
                        [
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'sap_code'          => $request->get('sap_code')[$m],
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
            $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->first();

             DB::table('eshop_order')->where('order_id', $resultCart->order_id)                        
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$retailerID)->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );

           
         }

            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
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
           
            return Redirect::to('/eshop-order-process/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
        

    }

    public function eshop_csv_file_to_cart_products($partyid, $customer_id , Request $request){  
        $file = Storage::disk('public')->put('csv', $request->file('csvFile'));
        $output_file =  Storage::disk('public')->path($file);
        $collection = Excel::load($output_file)->get();  
        $qty =  [] ;
        // $sap_codes =  array(1190121001, 1190121002 );
        $sap_codes = [] ;
        //$category_ids =  array(1, 1 );
        $category_ids = [];
        $produuct_ids = [];
        $price = [];
        $collection_total = count($collection);
        $retailerID = $partyid; 
        $totalQty = 0  ;
        $total_order_value =  0 ;
        $cat_id ='';
        $commission = 0;
        $getProducta = array();
        $countRowss = array();
        $resultCarts = array();
        $countRowsaa = array();
        $arrayInsertQuery = array();
        foreach ($collection as $key => $data) {
            
            if($data->discount_persentage !='' ){
                $commission =  $data->discount_persentage;  
            }else{
                $commission = 0;
            }
            $getProduct = DB::table('tbl_product')->where('sap_code', $data->sap_code)->first();  

            if(sizeof($getProduct)>0){ 
                $qty[] =  $data->qty ; 
                $sap_codes[] =  $data->sap_code;  
                $produuct_ids[] = $getProduct->id;    
                $price[] = $data->price;    
                $cat_id  = $getProduct->category_id; 
                $totalQty =  $totalQty +  $data->qty ; 
                $total_order_value = $total_order_value  + ($data->qty * $data->price); 
                if($cat_id){   
                    $getProducta[] = $getProduct;
                    $countRows = count($qty);  
                    $totalValue =  $data->totalvalue;   
                    $customerResult = DB::table('eshop_customer_list') 
                    ->where('customer_id', $customer_id)
                    ->where('status',0)
                    ->first();  
                    // print_r($customerResult); 
                    if(sizeof($customerResult) > 0) {
                        $sap_code = $customerResult->sap_code;
                    }else{
                        $sap_code = 0;
                    }
                    $autoOrder = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
                
                    if(sizeof($autoOrder) > 0){
                        $autoOrderId = $autoOrder->auto_order_no + 1;
                    } else{
                        $autoOrderId = 1000;
                    }    
                
                    $currentYear    = substr(date("Y"), -2); // 2017 to 17
                    $currentMonth   = date("m");            // 12
                    $currentDay     = date("d");           // 14
                    //$retailerID     = $request->get('retailer_id'); 
                    $orderNo        = 'SO'.'-'.$sap_code.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;
                       
                    $resultCart     = DB::table('eshop_order')
                            ->where('order_status', 'Ordered')                        
                            ->where('fo_id', Auth::user()->id)                        
                            ->where('party_id',$retailerID)
                            ->orderBy('order_id','DESC')                         
                            ->first();  
                    
                    $supervisorList  = DB::table('eshop_role_hierarchy')
                                ->where('status',0)                        
                                ->where('officer_id',Auth::user()->id) 
                                ->first(); 
                
                    // //dd($supervisorList);
                
                    // if(sizeof($resultCart)> 0) {                
                    //     $checkOrder     = DB::table('eshop_order_details')
                    //             ->where('order_id', $resultCart->order_id)                        
                    //             ->where('entry_by',Auth::user()->id)                        
                    //             ->where('party_id',$retailerID)                        
                    //             ->where('cat_id', $cat_id)                        
                    //             ->delete(); 
                
                    //     $checkCommission     = DB::table('eshop_categroy_wise_commission')
                    //             ->where('order_id', $resultCart->order_id)                        
                    //             ->where('entry_by',Auth::user()->id)                        
                    //             ->where('party_id',$retailerID)                        
                    //             ->where('cat_id',$cat_id)                        
                    //             ->delete();
                
                    // }
                
                    if(sizeof($resultCart)== 0) {
                        
                        DB::table('eshop_order')->insert([
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => date('Y-m-d h:i:s'),
                            'order_status'          => 'Ordered',
                            'total_order_qty'       => $totalQty,
                            'total_order_value'     => $total_order_value,
                            'customer_id'           => $customer_id,
                            'party_id'              => $retailerID,
                            'management_id'         => $supervisorList->management_id,
                            'manager_id'            => $supervisorList->manager_id,
                            'executive_id'          => $supervisorList->executive_id,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'entry_by'              => Auth::user()->id,
                            'entry_date'            => date('Y-m-d h:i:s')
                            
                        ]);
                
                        $resultCart     = DB::table('eshop_order')
                            ->where('order_status', 'Ordered')                        
                            ->where('fo_id',Auth::user()->id)                        
                            ->where('party_id',$retailerID)
                            ->orderBy('order_id','DESC')                         
                            ->first();   

                        $totalPrice = $data->qty * $data->price; 
                         
                        DB::table('eshop_order_details')->insert(
                            [
                                'order_id'          => $resultCart->order_id,
                                'order_date'        => date('Y-m-d H:i:s'),
                                'cat_id'            => $cat_id,
                                'product_id'        => $getProduct->id,
                                'sap_code'          => $getProduct->sap_code,
                                'order_qty'         => $data->qty,
                                'p_unit_price'      => $data->price,
                                'order_total_value' => $totalPrice,
                                'order_det_status'  => 'Ordered',
                                'discount_rate'     => $commission,
                                'party_id'          => $retailerID,
                                'entry_by'          => Auth::user()->id,
                                'entry_date'        => date('Y-m-d h:i:s')

                            ]);
                    }else{  
 
                        $totalPrice = $data->qty * $data->price; 
                        DB::table('eshop_order_details')->insert(
                            [
                                'order_id'          => $resultCart->order_id,
                                'order_date'        => date('Y-m-d H:i:s'),
                                'cat_id'            => $cat_id,
                                'product_id'        => $getProduct->id,
                                'sap_code'          => $getProduct->sap_code,
                                'order_qty'         => $data->qty,
                                'p_unit_price'      => $data->price,
                                'order_total_value' => $totalPrice,
                                'order_det_status'  => 'Ordered',
                                'discount_rate'     => $commission,
                                'party_id'          => $retailerID,
                                'entry_by'          => Auth::user()->id,
                                'entry_date'        => date('Y-m-d h:i:s')

                            ]);
                        $totalOrder = DB::table('eshop_order_details')
                                ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                                ->where('order_id', $resultCart->order_id)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$retailerID)
                                ->first();
            
                        DB::table('eshop_order')->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)->update(
                        [
                            'total_order_qty'       => $totalOrder->totalQty,
                            'total_order_value'     => $totalOrder->totalValue,
                            'entry_date'            => date('Y-m-d h:i:s')
                            
                        ]
                        ); 
            
                    }
                    //DB::table('eshop_order_details')->insert($arrayInsertQuery);
            
                    $checkDuplicat = DB::table('eshop_categroy_wise_commission')
                        ->where('order_id', $resultCart->order_id)                       
                        ->where('cat_id',$cat_id)->first(); 
                    if(!$checkDuplicat && $commission >0 ){ 
                        $commissionValue = ($total_order_value * $commission)/100; 
                        DB::table('eshop_categroy_wise_commission')->insert(
                            [
                                'order_id'              => $resultCart->order_id,
                                'cat_id'                => $cat_id,
                                'customer_id'           => $customer_id,
                                'party_id'              => $retailerID,
                                'fo_id'                 => Auth::user()->id,
                                'order_value'           => $total_order_value, 
                                'order_commission_value'=> $commissionValue,
                                'commission'            => $commission,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'entry_by'              => Auth::user()->id,
                                'entry_date'            => date('Y-m-d h:i:s')
                            ]
                        );  
                    }
                    $qty =  [];
                    $sap_codes =  [];
                    $category_ids =  [];
                    $produuct_ids =  [];
                }                 
            } 
        } 
        //print_r($getProducta);
        // echo "<pre>";
        // DB::table('eshop_order_details')->insert($arrayInsertQuery);
        // //print_r($arrayInsertQuery);
        // echo('</pre>');
        // exit();
        return Redirect::to('/eshop-order-process/'.$retailerID.'/'.$customer_id)->with('success', 'Successfully Added Add To Cart.'); 
    } 

    public function eshop_csv_file_to_cart_products_old($partyid, $customer_id , Request $request){  
        $file = Storage::disk('public')->put('csv', $request->file('csvFile'));
        $output_file =  Storage::disk('public')->path($file);
        $collection = Excel::load($output_file)->get();  
        $qty =  [] ;
        // $sap_codes =  array(1190121001, 1190121002 );
        $sap_codes = [] ;
        //$category_ids =  array(1, 1 );
        $category_ids = [];
        $produuct_ids = [];
        $price = [];
        $collection_total = count($collection);
        $retailerID = $partyid; 
        $totalQty = 0;
        $total_order_value =  0 ;
        $cat_id ='';
        $commission = 0;
        $getProducta = array();
        $countRowss = array();
        $resultCarts = array();
        $countRowsaa = array();
        $arrayInsertQuery = array(); 

        foreach ($collection as $key => $data) {
            
            if($data->discount_persentage !='' ){
                $commission =  $data->discount_persentage;  
            }else{
                $commission = 0;
            }
            if(!empty( $data->sap_code)){
                $sap_codeaas[] =  $data->sap_code;  
                $getProduct = DB::table('eshop_product')->where('sap_code', $data->sap_code)->first();   
                if(sizeof($getProduct)>0){ 
                    $qty[] =  $data->qty ; 
                    $sap_codes[] =  $data->sap_code;  
                    $produuct_ids[] = $getProduct->id;    
                    $price[] = $data->price;    
                    $cat_id  = $getProduct->category_id; 
                    $totalQty =  $totalQty + $data->qty; 
                    $total_order_value = $total_order_value  + ($data->qty * $data->price); 
                    // if($cat_id){   
                    //     $getProducta[] = $getProduct;
                    //     $countRows = count($qty);  
                    //     $totalValue =  $data->totalvalue;   
                    //     $customerResult = DB::table('eshop_customer_list') 
                    //     ->where('customer_id', $customer_id)
                    //     ->where('status',0)
                    //     ->first();  
                    //     // print_r($customerResult); 
                    //     if(sizeof($customerResult) > 0) {
                    //         $sap_code = $customerResult->sap_code;
                    //     }else{
                    //         $sap_code = 0;
                    //     }
                    //     $autoOrder = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
                    
                    //     if(sizeof($autoOrder) > 0){
                    //         $autoOrderId = $autoOrder->auto_order_no + 1;
                    //     } else{
                    //         $autoOrderId = 1000;
                    //     }    
                    
                    //     $currentYear    = substr(date("Y"), -2); // 2017 to 17
                    //     $currentMonth   = date("m");            // 12
                    //     $currentDay     = date("d");           // 14
                    //     //$retailerID     = $request->get('retailer_id'); 
                    //     $orderNo        = 'SO'.'-'.$sap_code.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;
                           
                    //     $resultCart     = DB::table('eshop_order')
                    //             ->where('order_status', 'Ordered')                        
                    //             ->where('fo_id', Auth::user()->id)                        
                    //             ->where('party_id',$retailerID)
                    //             ->orderBy('order_id','DESC')                         
                    //             ->first();  
                        
                    //     $supervisorList  = DB::table('eshop_role_hierarchy')
                    //                 ->where('status',0)                        
                    //                 ->where('officer_id',Auth::user()->id) 
                    //                 ->first(); 
                    
                    //     // //dd($supervisorList);
                    
                    //     // if(sizeof($resultCart)> 0) {                
                    //     //     $checkOrder     = DB::table('eshop_order_details')
                    //     //             ->where('order_id', $resultCart->order_id)                        
                    //     //             ->where('entry_by',Auth::user()->id)                        
                    //     //             ->where('party_id',$retailerID)                        
                    //     //             ->where('cat_id', $cat_id)                        
                    //     //             ->delete(); 
                    
                    //     //     $checkCommission     = DB::table('eshop_categroy_wise_commission')
                    //     //             ->where('order_id', $resultCart->order_id)                        
                    //     //             ->where('entry_by',Auth::user()->id)                        
                    //     //             ->where('party_id',$retailerID)                        
                    //     //             ->where('cat_id',$cat_id)                        
                    //     //             ->delete();
                    
                    //     // }
                    
                    //     if(sizeof($resultCart)== 0) {
                            
                    //         DB::table('eshop_order')->insert([
                    //             'order_no'              => $orderNo,
                    //             'auto_order_no'         => $autoOrderId,
                    //             'order_date'            => date('Y-m-d h:i:s'),
                    //             'order_status'          => 'Ordered',
                    //             'total_order_qty'       => $totalQty,
                    //             'total_order_value'     => $total_order_value,
                    //             'customer_id'           => $customer_id,
                    //             'party_id'              => $retailerID,
                    //             'management_id'         => $supervisorList->management_id,
                    //             'manager_id'            => $supervisorList->manager_id,
                    //             'executive_id'          => $supervisorList->executive_id,
                    //             'fo_id'                 => Auth::user()->id,
                    //             'global_company_id'     => Auth::user()->global_company_id,
                    //             'entry_by'              => Auth::user()->id,
                    //             'entry_date'            => date('Y-m-d h:i:s')
                                
                    //         ]);
                    
                    //         $resultCart     = DB::table('eshop_order')
                    //             ->where('order_status', 'Ordered')                        
                    //             ->where('fo_id',Auth::user()->id)                        
                    //             ->where('party_id',$retailerID)
                    //             ->orderBy('order_id','DESC')                         
                    //             ->first();   

                    //         $totalPrice = $data->qty * $data->price; 
                             
                    //         DB::table('eshop_order_details')->insert(
                    //             [
                    //                 'order_id'          => $resultCart->order_id,
                    //                 'order_date'        => date('Y-m-d H:i:s'),
                    //                 'cat_id'            => $cat_id,
                    //                 'product_id'        => $getProduct->id,
                    //                 'sap_code'          => $getProduct->sap_code,
                    //                 'order_qty'         => $data->qty,
                    //                 'p_unit_price'      => $data->price,
                    //                 'order_total_value' => $totalPrice,
                    //                 'order_det_status'  => 'Ordered',
                    //                 'discount_rate'     => $commission,
                    //                 'party_id'          => $retailerID,
                    //                 'entry_by'          => Auth::user()->id,
                    //                 'entry_date'        => date('Y-m-d h:i:s')

                    //             ]);
                    //     }else{  
     
                    //         $totalPrice = $data->qty * $data->price; 
                    //         DB::table('eshop_order_details')->insert(
                    //             [
                    //                 'order_id'          => $resultCart->order_id,
                    //                 'order_date'        => date('Y-m-d H:i:s'),
                    //                 'cat_id'            => $cat_id,
                    //                 'product_id'        => $getProduct->id,
                    //                 'sap_code'          => $getProduct->sap_code,
                    //                 'order_qty'         => $data->qty,
                    //                 'p_unit_price'      => $data->price,
                    //                 'order_total_value' => $totalPrice,
                    //                 'order_det_status'  => 'Ordered',
                    //                 'discount_rate'     => $commission,
                    //                 'party_id'          => $retailerID,
                    //                 'entry_by'          => Auth::user()->id,
                    //                 'entry_date'        => date('Y-m-d h:i:s')

                    //             ]);
                    //         $totalOrder = DB::table('eshop_order_details')
                    //                 ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                    //                 ->where('order_id', $resultCart->order_id)                        
                    //                 ->where('entry_by',Auth::user()->id)                        
                    //                 ->where('party_id',$retailerID)
                    //                 ->first();
                
                    //         DB::table('eshop_order')->where('order_id', $resultCart->order_id)                        
                    //         ->where('entry_by',Auth::user()->id)                        
                    //         ->where('party_id',$retailerID)->update(
                    //         [
                    //             'total_order_qty'       => $totalOrder->totalQty,
                    //             'total_order_value'     => $totalOrder->totalValue,
                    //             'entry_date'            => date('Y-m-d h:i:s')
                                
                    //         ]
                    //         ); 
                
                    //     }
                    //     //DB::table('eshop_order_details')->insert($arrayInsertQuery);
                
                    //     $checkDuplicat = DB::table('eshop_categroy_wise_commission')
                    //         ->where('order_id', $resultCart->order_id)                       
                    //         ->where('cat_id',$cat_id)->first(); 
                    //     if(!$checkDuplicat && $commission >0 ){ 
                    //         $commissionValue = ($total_order_value * $commission)/100; 
                    //         DB::table('eshop_categroy_wise_commission')->insert(
                    //             [
                    //                 'order_id'              => $resultCart->order_id,
                    //                 'cat_id'                => $cat_id,
                    //                 'customer_id'           => $customer_id,
                    //                 'party_id'              => $retailerID,
                    //                 'fo_id'                 => Auth::user()->id,
                    //                 'order_value'           => $total_order_value, 
                    //                 'order_commission_value'=> $commissionValue,
                    //                 'commission'            => $commission,
                    //                 'global_company_id'     => Auth::user()->global_company_id,
                    //                 'entry_by'              => Auth::user()->id,
                    //                 'entry_date'            => date('Y-m-d h:i:s')
                    //             ]
                    //         );  
                    //     }
                    //     $qty =  [];
                    //     $sap_codes =  [];
                    //     $category_ids =  [];
                    //     $produuct_ids =  [];
                    // }                 
                } 
            }
        }  
        //print_r($getProducta);
        // echo "<pre>";
         DB::table('eshop_order_details')->insert($arrayInsertQuery);
        // //print_r($arrayInsertQuery);
        // echo('</pre>'); 
        return Redirect::to('/eshop-order-process/'.$retailerID.'/'.$customer_id)->with('success', 'Successfully Added Add To Cart.'); 
     } 

    public function eshop_bucket($customer_id,$partyid)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title 

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company','tbl_global_company.global_company_id','=','users.global_company_id') 
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first(); 

        $resultInvoice  = DB::table('eshop_order')->select('order_id','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

        $getCats        = DB::table('eshop_order_details')
                        ->select('eshop_order_details.cat_id','eshop_product_category.name AS catname')  
                        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')  
                        ->where('eshop_order_details.order_id', $resultInvoice->order_id)  
                        ->groupBy('eshop_order_details.cat_id')                       
                        ->get(); 

        $orderCommission = DB::table('eshop_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $resultInvoice->order_id)    
                        ->first();

        $customerResult = DB::table('eshop_customer_list')
                    ->where('customer_id',$customer_id)
                    ->where('status',0)
                    ->first();

        $closingResult = DB::table('eshop_outlet_ledger')
                        ->where('customer_id',$customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

           $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        

            //echo $closingBalance;


        return view('eshop::sales/bucket',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery','getCats'));
   

    }
    public function eshop_bucket_old($customer_id,$partyid)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title 

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first(); 

       
         $resultCartPro  = DB::table('eshop_order_details')
                        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid',
                        'eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status',
                        'eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order_details.order_date') 
                        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id') 
                        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                        ->where('eshop_order.order_status','Ordered')                        
                        ->where('eshop_order.fo_id',Auth::user()->id)                        
                        ->where('eshop_order.party_id',$partyid)
                        ->groupBy('eshop_order_details.cat_id')                        
                        ->get();

          //  dd($resultCartPro);


            $resultInvoice  = DB::table('eshop_order')->select('order_id','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

            $orderCommission = DB::table('eshop_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $resultInvoice->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

            $customerResult = DB::table('eshop_customer_list')
                        ->where('customer_id',$customer_id)
                        ->where('status',0)
                        ->first();

             $closingResult = DB::table('eshop_outlet_ledger')
                        ->where('customer_id',$customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

           $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        

            //echo $closingBalance;


        return view('eshop::sales/bucket',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery'));
   

    }

    public function eshop_items_edit(Request $request)
    {

        $partyid        = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $catid          = $request->get('catid');
        $orderid          = $request->get('orderid');

        
        $resultPro  = DB::table('eshop_order_details')
                        ->select('eshop_order_details.order_det_id','eshop_order_details.order_id','eshop_order_details.product_id', 'eshop_order_details.order_qty','eshop_order_details.order_qty', 'eshop_order_details.p_unit_price', 'eshop_order_details.item_discount','eshop_product.name','eshop_product.sap_code')
                        ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')
                        ->where('eshop_order_details.order_det_id', $request->get('itemsid'))
                        ->first();

        $checkCommission     = DB::table('eshop_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)
                                ->first();

        //dd($checkCommission);

        $resultCatDefault  = DB::table('eshop_product_category')
                        ->select('id')                        
                        ->where('id', $request->get('catid'))
                        ->first();

        return view('eshop::sales/editItems', compact('resultPro','partyid','customer_id','catid','resultCatDefault','checkCommission'));
    }


    public function eshop_items_edit_submit(Request $request)
    {

        $partyid    = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $catid      = $request->get('catid');
        $orderid    = $request->get('orderid');
        $order_item_id  = $request->get('order_item_id');
        
        $price          = $request->get('items_qty') * $request->get('items_price');
        
        DB::table('eshop_order_details')->where('order_det_id',$request->get('id'))->update([
            'order_qty'         => $request->get('items_qty'), 
            'order_total_value' => $price, 
            'item_discount'     => $request->get('commission'),
            'order_date'        => date('Y-m-d H:i:s') 
        ]); 

        // DB::table('eshop_order_details')->where('order_id',$orderid)->where('cat_id',$catid)->update(
        //     [
        //         'discount_rate'         => $request->get('commission') 
        //     ]
        // );     

        $totalOrder = DB::table('eshop_order_details') 
                        ->select(DB::raw('SUM(item_discount) AS total_discount'), DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('eshop_order')->where('order_id', $orderid)->where('fo_id', Auth::user()->id)->where('entry_by',Auth::user()->id)->update(
            [ 
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'total_discount'        => $totalOrder->total_discount,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        //dd($orderid);
         $checkCommission     = DB::table('eshop_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)                        
                                ->delete();  

        $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        ->first();  

        $totalValue = $totalOrder->totalValue;
        if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $orderid,
                        'cat_id'                => $catid,
                        'customer_id'           => $customer_id,
                        'party_id'              => $partyid,
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

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');

    }


    public function eshop_items_delete($orderid,$itemid,$customer_id,$partyid,$catid){

        
        $checkCommission     = DB::table('eshop_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$partyid)                        
                                ->where('cat_id',$catid)                        
                                ->delete(); 

        $totalOrder = DB::table('eshop_order_details')
                        ->select('order_det_id','order_id','party_id','cat_id','product_id','discount_rate','entry_by',DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        ->where('order_det_id','!=',$itemid)
                        ->first();

       
        if(sizeof($totalOrder)>0){

            $totalValue = $totalOrder->totalValue;
            $discount = $totalOrder->discount_rate;
            $commissionValue = ($totalValue *  $discount)/100;
            
            DB::table('eshop_categroy_wise_commission')->insert(
                [
                    'order_id'              => $orderid,
                    'cat_id'                => $catid,
                    'customer_id'           => $customer_id,
                    'party_id'              => $partyid,
                    'fo_id'                 => Auth::user()->id,
                    'order_value'           => $totalValue, 
                    'order_commission_value'=> $commissionValue,
                    'commission'            => $discount,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => Auth::user()->id,
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );
        } 

        $itemdelete = DB::table('eshop_order_details')
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->where('cat_id',$catid)
                        ->where('order_det_id',$itemid)
                        ->delete(); 

        $totalOrder = DB::table('eshop_order_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('eshop_order')->where('order_id', $orderid)->where('fo_id', Auth::user()->id)->where('entry_by',Auth::user()->id)->update(
            [ 
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );
          

         return Redirect::back()->with('success', 'Successfully Delete Product.');
    }

    public function eshop_confirm_order(Request $request)
    {

        $orderid    = $request->get('orderid');
        $partyid    = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $po_no      = $request->get('po_no');

        //dd($po_no);

        $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(item_discount) AS totalDiscount'), DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('eshop_order')->where('order_id', $orderid)->where('fo_id', Auth::user()->id)->where('entry_by',Auth::user()->id)->update(
            [
                'po_no'                 => $po_no, 
                'order_status'          => 'Confirmed',
                'ack_status'            => 'Pending',  
                'order_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'total_discount'        => $totalOrder->totalDiscount,
                'entry_date'            => date('Y-m-d h:i:s'),
                'status'                => 1 // send requisition to executive panel
            ]
        );

        DB::table('eshop_order_details')
                ->where('order_id', $orderid)
                ->update(['order_det_status'  => 'Confirmed']);

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();

        DB::table('eshop_visit_order')->insert([
            'date'                  => date('Y-m-d'),
            'management_id'         => $supervisorList->management_id,
            'manager_id'            => $supervisorList->manager_id,
            'executive_id'          => $supervisorList->executive_id,
            'officer_id'            => Auth::user()->id,  
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
        ]);


    return Redirect::to('/eshop-visit')->with('success', 'Successfully Confirmed Order');

    }


    public function eshop_delete_order($orderid,$partyid,$customer_id)
    {
        $checkCommission     = DB::table('eshop_categroy_wise_commission')
                                ->where('order_id', $orderid)                        
                                ->where('entry_by',Auth::user()->id)                        
                                ->where('party_id',$partyid)                     
                                ->delete(); 

        $totalOrder = DB::table('eshop_order_details')
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->delete();

         $totalOrder = DB::table('eshop_order')
                        ->where('order_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->delete();

        $totalOrder = DB::table('eshop_visit_order')
                        ->where('order_id', $orderid)                        
                        ->where('party_id',$partyid)
                        ->delete();


    return Redirect::to('/eshop-visit')->with('success', 'Successfully Delete Order');

    }



   public function eshop_order_not_approve()
    {
        $selectedMenu   = 'Order Not approve';                      // Required Variable for menu
        $selectedSubMenu= 'Order Not approve';                    // Required Variable for submenu
        $pageTitle      = 'Order Not approve';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Ordered')
        ->where('eshop_order.ack_status', 'Rejected')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
         $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Ordered')
            ->where('eshop_order.ack_status', 'Rejected')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
  
        return view('eshop::sales/orderNotApprove', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function eshop_order_not_approve_list(Request $request)
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
            ->where('eshop_order.order_status', 'Ordered')
            ->where('eshop_order.ack_status', 'Rejected')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
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
            ->where('eshop_order.order_status', 'Ordered')
            ->where('eshop_order.ack_status', 'Rejected')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
        }
        
        return view('eshop::sales/orderNotApproveList', compact('resultOrderList'));
    }

    public function eshop_order_manage()
    {
        $selectedMenu   = 'Order Manage';                      // Required Variable for menu
        $selectedSubMenu= 'Order Manage';                    // Required Variable for submenu
        $pageTitle      = 'Order Manage';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('eshop_order')
        ->select('eshop_order.global_company_id','eshop_order.order_status','eshop_order.order_id','eshop_order.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'eshop_order.fo_id')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('eshop_order.order_status', 'Ordered')
        ->where('eshop_order.ack_status', 'Rejected')
        ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
        ->where('eshop_order.fo_id', Auth::user()->id)
        ->groupBy('eshop_order.fo_id')
        ->orderBy('eshop_order.order_id','DESC')                    
        ->get();


        $todate   = date('Y-m-d');
         $resultOrderList = DB::table('eshop_order')
            ->select('eshop_order.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
            ->where('eshop_order.order_status', 'Confirmed')
            ->where('eshop_order.ack_status', 'Pending')
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)
            ->where('eshop_order.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(eshop_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('eshop_order.order_id','DESC')                    
            ->get();
  
        return view('eshop::sales/orderManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

     public function eshop_bucket_manage($order_id,$customer_id,$partyid)
    {


        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title 

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first(); 

       
         $resultCartPro  = DB::table('eshop_order_details')
                        ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid', 'eshop_order_details.item_discount',
                        'eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status',
                        'eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order_details.order_date') 

                        //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id') 
                        ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
                        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
                        ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
                        ->where('eshop_order.order_status','Confirmed')
                        ->where('eshop_order.order_id', $order_id)                         
                        ->where('eshop_order.fo_id',Auth::user()->id)                        
                        ->where('eshop_order.party_id',$partyid)
                        ->groupBy('eshop_product.category_id')                        
                        ->get();


            $resultInvoice  = DB::table('eshop_order')->select('order_id','total_discount','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_id', $order_id) 
                        ->where('order_status', 'Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

            $orderCommission = DB::table('eshop_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

            $customerResult = DB::table('eshop_customer_list')
                        ->where('customer_id',$customer_id)
                        ->where('status',0)
                        ->first();

             $closingResult = DB::table('eshop_outlet_ledger')
                        ->where('customer_id',$customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

           $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        

            //echo $closingBalance;


        return view('eshop::sales/bucketManage',compact('selectedMenu','pageTitle','order_id','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery'));
   

    }

    public function eshop_order_visit($party_id,$customer_id)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('eshop_visit_reason')
                        ->where('type', 1)                    
                        ->get();

         return view('eshop::sales/orderVisitOnly', compact('selectedMenu','pageTitle','party_id','customer_id','resultReason')); 
    }  


    public function eshop_visit_process_submit(Request $request)
    {

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first(); 

        DB::table('eshop_visit_order')->insert(
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

        return Redirect::to('/eshop-visit')->with('success', 'Successfully Visit Done.'); 
    }

    public function eshop_nonvisit($party_id,$customer_id)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('eshop_visit_reason')
                        ->where('type', 2)                    
                        ->get();

         return view('eshop::sales/orderNonVisitOnly', compact('selectedMenu','pageTitle','party_id','customer_id','resultReason')); 
    }  


    public function eshop_nonvisit_process_submit(Request $request)
    {

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first(); 

        DB::table('eshop_visit_order')->insert(
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

        return Redirect::to('/eshop-visit')->with('success', 'Successfully Visit Done.'); 
    }
   
}

