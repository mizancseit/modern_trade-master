<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ModernReturnController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

    public function mts_return()
    {

        $selectedMenu   = 'Return';         // Required Variable
        $pageTitle      = 'Return';        // Page Slug Title

         $routeResult = DB::table('mts_route')
                        ->where('status',0)
                        ->get();

          $customerResult = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('mts_customer_list.status',0)
                        ->get();
      

        return view('ModernSales::sales/return/returnManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','customerResult'));
    }

     public function mts_return_outlet_list(Request $request)
    {

        $customerID = $request->get('customer');

        $resultParty = DB::table('mts_party_list')
                        ->select('party_id','name','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customerID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      

        return view('ModernSales::sales/return/outletList', compact('resultParty','customerID'));
    }

    public function mts_return_process($partyid,$customer_id)
    {
        $selectedMenu   = 'Return';             // Required Variable
        $pageTitle      = 'Return';        // Page Slug Title

        

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
       

        $resultCart     = DB::table('mts_return')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_status', 'Ordered')                       
                        ->first();  

        
        
        return view('ModernSales::sales/return/categoryWithOrder', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart'));
    }

    public function mts_return_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $party_id     = $request->get('retailer_id');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit','sap_code')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
         $lastDiscount = DB::table('mts_categroy_wise_commission')        
                        ->where('party_id', $party_id)
                        ->where('cat_id', $categoryID)
                        ->orderBy('id', 'desc') 
                        ->first();

        return view('ModernSales::sales/return/allProductList', compact('resultProduct','categoryID','party_id','lastDiscount'));
    }


     public function mts_add_return_products(Request $request)
    {
        
        $customerResult = DB::table('mts_customer_list') 
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

        $autoOrder = DB::table('mts_return')->select('auto_return_no')->orderBy('return_id','DESC')->first();
    
        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_return_no + 1;
        }
        else
        {
            $autoOrderId = 1000;
        }    

        $currentYear    = substr(date("Y"), -2); // 2017 to 17
        $currentMonth   = date("m");            // 12
        $currentDay     = date("d");           // 14
        $retailerID     = $request->get('retailer_id');

        $orderNo        = 'RO'.'-'.$sap_code.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;

        $discount   = $request->get('discount');
        $totaldiscount= ($request->get('totalValue') * $request->get('discount'))/100;
        //dd($discount);
        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue')-$totaldiscount;

        $countRows = count($request->get('qty'));

        $resultCart     = DB::table('mts_return')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('return_id','DESC')                         
                        ->first(); 

        $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();

        if(sizeof($resultCart)> 0) 
        {

         $checkOrder     = DB::table('mts_return_details')
                        ->where('return_id', $resultCart->return_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete(); 
        
        }

        if(sizeof($resultCart)== 0) 
        {
           
            DB::table('mts_return')->insert(
                [
                    'return_no'              => $orderNo,
                    'auto_return_no'         => $autoOrderId,
                    'return_date'            => date('Y-m-d h:i:s'),
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

           $resultCart     = DB::table('mts_return')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('return_id','DESC')                         
                        ->first(); 
           

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    DB::table('mts_return_details')->insert(
                        [
                            'return_id'          => $resultCart->return_id,
                            'return_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice-($totalPrice * $request->get('discount'))/100,
                            'order_det_status'  => 'Ordered',
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


                    DB::table('mts_return_details')->insert(
                        [
                            'return_id'          => $resultCart->return_id,
                            'return_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice-($totalPrice * $request->get('discount'))/100,
                            'order_det_status'  => 'Ordered',
                            'party_id'          => $retailerID,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]
                    );

                }

            }


            $totalOrder = DB::table('mts_return_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('return_id', $resultCart->return_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->first();

             DB::table('mts_return')->where('return_id', $resultCart->return_id)                        
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$retailerID)->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );

           
         }
            return Redirect::to('/mts-return-process/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
        

    }

    public function mts_return_bucket($customer_id,$partyid)
    {


        $selectedMenu   = 'return';             // Required Variable
        $pageTitle      = 'return';           // Page Slug Title 

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

       
         $resultCartPro  = DB::table('mts_return_details')
                        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status',
                        'mts_return.return_no','mts_return.po_no','mts_return.party_id','mts_return_details.return_date') 
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id') 
                        ->join('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        ->where('mts_return.order_status','Ordered')                        
                        ->where('mts_return.fo_id',Auth::user()->id)                        
                        ->where('mts_return.party_id',$partyid)
                        ->groupBy('mts_return_details.cat_id')                        
                        ->get();


            $resultInvoice  = DB::table('mts_return')->select('return_id','order_status','fo_id','party_id')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

           

        return view('ModernSales::sales/return/bucket',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice'));
   

    }
    
public function mts_return_items_edit(Request $request)
    {

        
        $resultPro  = DB::table('mts_return_details')
                        ->select('mts_return_details.return_det_id','mts_return_details.return_id','mts_return_details.product_id',
                        'mts_return_details.order_qty','mts_return_details.order_qty',
                        'mts_return_details.p_unit_price','tbl_product.name')
                        ->join('tbl_product', 'tbl_product.id', '=', 'mts_return_details.product_id')
                        ->where('mts_return_details.return_det_id', $request->get('itemsid'))
                        ->first();

        return view('ModernSales::sales/return/editItems', compact('resultPro'));
    }


    public function mts_return_edit_submit(Request $request)
    {
        $returnid    = $request->get('return_id');
        
        $price          = $request->get('items_qty') * $request->get('items_price');
        
        DB::table('mts_return_details')->where('return_det_id',$request->get('id'))->update(
            [
                'order_qty'         => $request->get('items_qty'), 
                'order_total_value' => $price, 
                'return_date'        => date('Y-m-d H:i:s') 
            ]
        );        
  
        $totalOrder = DB::table('mts_return_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('return_id', $returnid)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->first();

             DB::table('mts_return')->where('return_id', $returnid)                        
                ->where('entry_by',Auth::user()->id)                        
                ->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );
                    

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');

    }

    public function mts_return_items_delete($returnid,$itemid)
    {
       
        DB::table('mts_return_details')->where('return_det_id',$itemid)->delete();        
  
        $totalOrder = DB::table('mts_return_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('return_id', $returnid)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->first();

             DB::table('mts_return')->where('return_id', $returnid)                        
                ->where('entry_by',Auth::user()->id)                        
                ->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );
                    

        return Redirect::back()->with('success', 'Successfully Delete Product.');

    }

    public function mts_confirm_return(Request $request)
    {

        $orderid    = $request->get('orderid');
        $partyid    = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $po_no      = $request->get('po_no');

        //dd($po_no);

        $totalOrder = DB::table('mts_return_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('return_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('mts_return')->where('return_id', $orderid)->where('fo_id', Auth::user()->id)->where('entry_by',Auth::user()->id)->update(
            [
                'po_no'                 => $po_no, 
                'order_status'          => 'Confirmed',
                'ack_status'            => 'Pending',  
                'return_date'            => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        DB::table('mts_return_details')
                ->where('return_id', $orderid)
                ->update(
                    [
                        'order_det_status'          => 'Confirmed' 
                    ]
            );




        return Redirect::to('/mts-return')->with('success', 'Successfully Confirmed Order');

    }


    public function mts_delete_return($return_id,$partyid,$customer_id)
    {
        
        $totalOrder = DB::table('mts_return_details') 
                        ->where('return_id', $return_id)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->delete();  


         DB::table('mts_return')->where('return_id',$return_id)->delete(); 

        return Redirect::to('/mts-return')->with('success', 'Successfully Delete Return');

        //return Redirect::back()->with('success', 'Successfully Delete Advance return.');

    }


    // Advance return start for admin section

    public function mts_return_approved()
    {
        $selectedMenu       = 'return Approve';
        $subSelectedMenu    = 'Order Delivery'; 
        $pageTitle          = 'Order Delivery'; 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.executive_id', Auth::user()->id)               
        ->where('mts_return.order_status', 'Confirmed')
        ->where('mts_return.ack_status', 'Pending')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
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

        return view('ModernSales::sales/adminReport/return', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }

    public function mts_return_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive_id    = $request->get('executive_id');
        $officer    = $request->get('fos');

        if($fromdate!='' && $todate!='' && $executive_id!='' && $officer=='')
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.executive_id', $executive_id)                  
            ->where('mts_return.order_status', 'Confirmed')
            ->where('mts_return.ack_status', 'Pending')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')                  
            ->where('mts_return.order_status', 'Confirmed')
            ->where('mts_return.ack_status', 'Pending')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_return.executive_id', $executive_id)
            ->where('mts_return.fo_id', $request->get('fos'))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/returnList', compact('resultOrderList'));
    }


    public function mts_return_view($DeliveryMainId,$foMainId)
    {
         $selectedMenu    = 'return Approve';                 // Required Variable
        $pageTitle      = 'Return Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id','mts_return.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->join('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
        ->where('mts_return.order_status','Confirmed')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return_details.return_id',$DeliveryMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','users.display_name','mts_return.party_id','mts_return.return_no','mts_return.po_no','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.order_status','Confirmed')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$DeliveryMainId)
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

         $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.name','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$DeliveryMainId)
                        ->first();


        return view('ModernSales::sales/adminReport/returnEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo','customerInfo'));
    }


    public function mts_approved_return($orderid,$partyid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('mts_return')->where('return_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );
        }else{
            DB::table('mts_return')->where('return_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Rejected'
                ]
            );
        }

        DB::commit();
        DB::rollBack();
        return Redirect::to('/mts-return-approved')->with('success', 'Successfully Approved Order');

    }


    public function mts_return_delivery_approved()
    {
        $selectedMenu   = 'Return Delivery Approved';           // Required Variable for menu
        $selectedSubMenu= 'Return Delivery Approved';           // Required Variable for submenu
        $pageTitle      = 'Return Delivery Approved';            // Page Slug Title
         

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultReturnList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Pending')
        ->where('mts_return.executive_id', Auth::user()->id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
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

        return view('ModernSales::sales/adminReport/returnApprove', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultReturnList','managementlist','officerlist'));
    }

    public function mts_return_delivery_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate'))); 
        $executive  = $request->get('executive_id');
        $officer    = $request->get('fos'); 

        if($fromdate!='' && $todate!='' && $executive!='' && $officer=='')
        { 
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Pending') 
           ->where('mts_return.executive_id', Auth::user()->id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        else{
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Pending')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.executive_id', Auth::user()->id)
            ->where('mts_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }

         return view('ModernSales::sales/adminReport/returnApproveList', compact('selectedMenu','selectedSubMenu','pageTitle','resultReturnList'));
        
       
    }

     public function mts_return_delivery_approved_view($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Advance Return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'mts_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_return.route_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_return')
        ->select('mts_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        //dd($resultFoInfo);

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        //->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_return_details')
        ->select('mts_return_details.delivery_challan','mts_return.delivery_date','mts_return_details.return_id','mts_return.return_id','mts_return.order_status')

        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                       // ->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.return_no','mts_return.po_no','mts_return.update_date','mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','mts_return.party_id','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$orderMainId)
                        ->first();

        return view('ModernSales::sales/adminReport/returnApproveEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','orderMainId','foMainId','resultFoInfo','customerInfo'));
    }

    public function mts_return_delivery_approved_submit($orderid,$party_id,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){ 

            //dd($orderid.'-'.$party_id);

            DB::table('mts_return')->where('return_id', $orderid)->where('party_id', $party_id)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );

             DB::commit();
            DB::rollBack();
            return Redirect::to('/mts-return-delivery-approved')->with('success', 'Successfully Approved Order');
             
        }else{
            DB::table('mts-return-delivery-approved')->where('return_id', $orderid)->where('customer_id', $party_id)->update(
                [
                    'ack_status'  => 'Rejected'
                ]
            );

             DB::commit();
            DB::rollBack();
            return Redirect::to('/mts-return-delivery-approved')->with('success', 'Unsuccessfully Approved Order');
        }

       

    }

    // Advance return start for Billing section

    public function mts_return_delivery()
    {
        $selectedMenu       = 'return Delivery';
        $subSelectedMenu    = 'return Delivery'; 
        $pageTitle          = 'Order Delivery';

        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')                       
        ->where('mts_return.order_status', 'Confirmed')
        ->where('mts_return.ack_status', 'Pending')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')               
        ->where('mts_return.order_status', 'Confirmed')
        ->where('mts_return.ack_status', 'Approved')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        return view('ModernSales::sales/delivery_Report/return', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_return_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')                  
            ->where('mts_return.order_status', 'Confirmed')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')                  
            ->where('mts_return.order_status', 'Confirmed')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_return.fo_id', $request->get('fos'))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/delivery_Report/returnList', compact('resultOrderList'));
    }

    public function mts_return_delivery_edit($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'return Delivery';                   // Required Variable
        $pageTitle      = 'return Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id','mts_return.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->join('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
        ->where('mts_return.order_status','Confirmed')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return_details.return_id',$DeliveryMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','users.display_name','mts_return.party_id','mts_return.return_no','mts_return.po_no','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.order_status','Confirmed')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$DeliveryMainId)
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

        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$DeliveryMainId)
                        ->first();


        return view('ModernSales::sales/delivery_Report/returnDeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo','customerInfo'));
    }

    public function mts_return_delivery_edit_submit(Request $request)
    {
        DB::beginTransaction();

        $orderid    = $request->get('orderid'); 

        $autoAdd  = $orderid;
        $chalanNO = 'RO-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;
        //dd($chalanNO);

        DB::table('mts_return')->where('return_id', $orderid)
        ->where('fo_id', $request->get('foMainId'))
        ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_status'           => 'Delivered',
                'ack_status'             => 'Pending', 
                'update_date'            => date('Y-m-d H:i:s'),
                'chalan_no'              => $chalanNO,
                'chalan_date'            => date('Y-m-d H:i:s'),
            ]
        );




            $totalSales = DB::table('mts_return')
            ->join('mts_customer_list', 'mts_customer_list.customer_id', 'mts_return.customer_id')
            ->where('mts_return.return_id', $orderid)->where('mts_return.order_status', 'Delivered')->first();


            if(sizeof($totalSales)>0){
                $ledger = DB::table('mts_outlet_ledger')->where('customer_id', $totalSales->customer_id)->orderBy('ledger_id','DESC')->first();
                
                
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
                        'ref_id'                => $totalSales->return_id,
                        'trans_type'            => 'Return',
                        'party_sap_code'        => $totalSales->sap_code,
                        'opening_balance'       => $closing_balance,
                        'debit'                 => 0,
                        'credit'                => $totalSales->total_order_value,
                        'closing_balance'       => $closing_balance-$totalSales->total_order_value,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')

                    ]
                );
        }

        DB::commit();
        DB::rollBack();
        return Redirect::to('/mts-return-delivery')->with('success', 'Successfully Confirm Delivery Done.'); 

    }

    // Advance return Delivery report for admin part


    public function mts_admin_return_delivery_report()
    {
        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
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

        return view('ModernSales::sales/adminReport/returnDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','officerlist'));
    }

    public function mts_admin_return_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');



        if($fromdate!='' && $todate!='' && $officer=='' && $customer=='')
        { 
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved') 
            ->where('mts_return.executive_id', Auth::user()->id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer=='')
        {
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.executive_id', Auth::user()->id) 
            ->where('mts_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
       elseif($fromdate!='' && $todate!='' && $officer!='' && $customer!='')
        {
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.customer_id', $customer) 
            ->where('mts_return.executive_id', Auth::user()->id)
            ->where('mts_return.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }

         return view('ModernSales::sales/adminReport/returnDeliveryReportList', compact('selectedMenu','selectedSubMenu','pageTitle','resultReturnList'));
        
       
    }


    public function mts_admin_return_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'mts_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_return.route_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_return')
        ->select('mts_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        //->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_return_details')
        ->select('mts_return_details.delivery_challan','mts_return.delivery_date','mts_return_details.return_id','mts_return.return_id','mts_return.order_status')

        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                       // ->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.return_no','mts_return.po_no','mts_return.update_date','mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','mts_return.party_id','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$orderMainId)
                        ->first();
 

        

        return view('ModernSales::sales/adminReport/returnDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }


// Advance return Delivery report for Billing part


    public function mts_billing_return_delivery_report()
    {
        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Pending')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Pending')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

         $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.management_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.management_id')
          ->get();

        return view('ModernSales::sales/billingReport/returnDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist'));
    }

    public function mts_billing_return_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');



        if($fromdate!='' && $todate!='' && $customer=='')
        { 
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Pending')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
       
       elseif($fromdate!='' && $todate!='' && $customer!='')
        {
            $resultReturnList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Pending')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.customer_id', $customer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }

         return view('ModernSales::sales/billingReport/returnDeliveryReportList', compact('selectedMenu','selectedSubMenu','pageTitle','resultReturnList'));
        
       
    }


    public function mts_billing_return_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'mts_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_return.route_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_return')
        ->select('mts_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        //->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_return_details')
        ->select('mts_return_details.delivery_challan','mts_return.delivery_date','mts_return_details.return_id','mts_return.return_id','mts_return.order_status')

        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                       // ->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.return_no','mts_return.po_no','mts_return.update_date','mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','mts_return.party_id','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$orderMainId)
                        ->first();
 

        

        return view('ModernSales::sales/billingReport/returnDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }
    // Advance return Delivery report for Executive part


    public function mts_return_delivery_report()
    {
        $selectedMenu   = 'Advance return Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';             // Required Variable for submenu
        $pageTitle      = 'Advance return Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->where('mts_return.fo_id', Auth::user()->id)
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_return')
        ->select('mts_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.order_status', 'Delivered')
        ->where('mts_return.ack_status', 'Approved')
        ->where('mts_return.fo_id', Auth::user()->id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();


        return view('ModernSales::sales/executiveReport/returnDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList'));
    }

    public function mts_return_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        //$fo         = $request->get('fos');

        if($fromdate!='' && $todate!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.fo_id', Auth::user()->id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!='')
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Delivered')
            ->where('mts_return.ack_status', 'Approved')
            ->where('mts_return.fo_id', Auth::user()->id)
            ->where('mts_return.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_return.customer_id', $customer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        
        
        return view('ModernSales::sales/executiveReport/returnDeliveryReportList', compact('resultOrderList'));
    }


    public function mts_return_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance return Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance return Report';                // Required Variable for submenu
        $pageTitle      = 'Advance return Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_return')->select('mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'mts_return.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_return.route_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_return')
        ->select('mts_return.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_return.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_return.fo_id',$foMainId)                        
        ->where('mts_return.return_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_return_details')
        ->select('mts_return_details.cat_id','mts_return_details.return_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_return.return_id','mts_return.fo_id','mts_return.order_status','mts_return.return_no','mts_return.po_no','mts_return.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_return_details.cat_id')
        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                        //->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_return_details')
        ->select('mts_return_details.delivery_challan','mts_return.delivery_date','mts_return_details.return_id','mts_return.return_id','mts_return.order_status')

        ->leftjoin('mts_return', 'mts_return.return_id', '=', 'mts_return_details.return_id')
                       // ->where('mts_return.order_status','Delivered')
        ->where('mts_return_details.return_id',$orderMainId)
        ->groupBy('mts_return_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_return')->select('mts_return.return_no','mts_return.po_no','mts_return.update_date','mts_return.global_company_id','mts_return.return_id','mts_return.order_status','mts_return.fo_id','mts_return.party_id','mts_return.return_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_return.return_id',$orderMainId)
        ->first();
        
        $customerInfo = DB::table('mts_return')
                        ->select('mts_return.return_id','mts_return.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_return.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_return.return_id',$orderMainId)
                        ->first();

        

        return view('ModernSales::sales/executiveReport/returnDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultDistributorInfo','resultFoInfo','resultAllChalan','customerInfo'));

    }


    public function mts_return_not_approve()
    {
        $selectedMenu   = 'Return Not approve';                      // Required Variable for menu
        $selectedSubMenu= 'Return Not approve';                    // Required Variable for submenu
        $pageTitle      = 'Return Not approve';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_return')
        ->select('mts_return.global_company_id','mts_return.order_status','mts_return.return_id','mts_return.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_return.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_return.order_status', 'Ordered')
        ->where('mts_return.ack_status', 'Rejected')
        ->where('mts_return.global_company_id', Auth::user()->global_company_id)
        ->where('mts_return.fo_id', Auth::user()->id)
        ->groupBy('mts_return.fo_id')
        ->orderBy('mts_return.return_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
         $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Ordered')
            ->where('mts_return.ack_status', 'Rejected')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->where('mts_return.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
  
        return view('ModernSales::sales/return/returnNotApprove', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_return_not_approve_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Ordered')
            ->where('mts_return.ack_status', 'Rejected')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->where('mts_return.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_return')
            ->select('mts_return.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_return.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_return.party_id')
            ->where('mts_return.order_status', 'Ordered')
            ->where('mts_return.ack_status', 'Rejected')
            ->where('mts_return.global_company_id', Auth::user()->global_company_id)
            ->where('mts_return.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_return.return_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_return.return_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/return/returnNotApproveList', compact('resultOrderList'));
    }


}
