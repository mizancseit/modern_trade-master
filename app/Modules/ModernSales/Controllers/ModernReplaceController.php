<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ModernReplaceController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

    public function mts_replace()
    {

        $selectedMenu   = 'Replace';         // Required Variable
        $pageTitle      = 'Replace';        // Page Slug Title

         $routeResult = DB::table('mts_route')
                        ->where('status',0)
                        ->get();

          $customerResult = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('mts_customer_list.status',0)
                        ->get();
      

        return view('ModernSales::sales/replace/replaceManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','customerResult'));
    }

     public function mts_replace_outlet_list(Request $request)
    {

        $customerID = $request->get('customer');

        $resultParty = DB::table('mts_party_list')
                        ->select('party_id','name','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customerID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      

        return view('ModernSales::sales/replace/outletList', compact('resultParty','customerID'));
    }

    public function mts_replace_process($partyid,$customer_id)
    {
        $selectedMenu   = 'Replace';             // Required Variable
        $pageTitle      = 'Replace';        // Page Slug Title

        

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
       

        $resultCart     = DB::table('mts_replace')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_status', 'Ordered')                       
                        ->first();  

        
        
        return view('ModernSales::sales/replace/categoryWithOrder', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart'));
    }

    public function mts_replace_category_products(Request $request)
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
        

        return view('ModernSales::sales/replace/allProductList', compact('resultProduct','categoryID','party_id'));
    }


     public function mts_add_replace_products(Request $request)
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

        $autoOrder = DB::table('mts_replace')->select('auto_replace_no')->orderBy('replace_id','DESC')->first();
    
        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_replace_no + 1;
        }
        else
        {
            $autoOrderId = 1000;
        }    

        $currentYear    = substr(date("Y"), -2); // 2017 to 17
        $currentMonth   = date("m");            // 12
        $currentDay     = date("d");           // 14
        $retailerID     = $request->get('retailer_id');

        $orderNo        = 'ARO'.'-'.$sap_code.'-'.$currentYear.$currentMonth.$currentDay.'-'.$autoOrderId;


        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue');


        $countRows = count($request->get('qty'));

        $resultCart     = DB::table('mts_replace')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('replace_id','DESC')                         
                        ->first(); 

         $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();

        if(sizeof($resultCart)> 0) 
        {

         $checkOrder     = DB::table('mts_replace_details')
                        ->where('replace_id', $resultCart->replace_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)                        
                        ->where('cat_id',$request->get('cat_id'))                        
                        ->delete(); 
        
        }

        if(sizeof($resultCart)== 0) 
        {
           
            DB::table('mts_replace')->insert(
                [
                    'replace_no'              => $orderNo,
                    'auto_replace_no'         => $autoOrderId,
                    'replace_date'            => date('Y-m-d h:i:s'),
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

           $resultCart     = DB::table('mts_replace')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->orderBy('replace_id','DESC')                         
                        ->first(); 
           

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    DB::table('mts_replace_details')->insert(
                        [
                            'replace_id'          => $resultCart->replace_id,
                            'replace_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
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


                    DB::table('mts_replace_details')->insert(
                        [
                            'replace_id'          => $resultCart->replace_id,
                            'replace_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'party_id'          => $retailerID,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]
                    );

                }

            }


            $totalOrder = DB::table('mts_replace_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('replace_id', $resultCart->replace_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$retailerID)
                        ->first();

             DB::table('mts_replace')->where('replace_id', $resultCart->replace_id)                        
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$retailerID)->update(
                [
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'entry_date'            => date('Y-m-d h:i:s')
                    
                ]
            );

           
         }
            return Redirect::to('/mts-replace-process/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
        

    }

    public function mts_replace_bucket($customer_id,$partyid)
    {


        $selectedMenu   = 'Replace';             // Required Variable
        $pageTitle      = 'Replace';           // Page Slug Title 

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

       
         $resultCartPro  = DB::table('mts_replace_details')
                        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status',
                        'mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id','mts_replace_details.replace_date') 
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id') 
                        ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                        ->where('mts_replace.order_status','Ordered')                        
                        ->where('mts_replace.fo_id',Auth::user()->id)                        
                        ->where('mts_replace.party_id',$partyid)
                        ->groupBy('mts_replace_details.cat_id')                        
                        ->get();


            $resultInvoice  = DB::table('mts_replace')->select('replace_id','order_status','fo_id','party_id')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

           

        return view('ModernSales::sales/replace/bucket',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice'));
   

    }

    public function mts_replace_items_edit(Request $request)
    {

        
        $resultPro  = DB::table('mts_replace_details')
                        ->select('mts_replace_details.replace_det_id','mts_replace_details.replace_id','mts_replace_details.product_id',
                        'mts_replace_details.order_qty','mts_replace_details.order_qty',
                        'mts_replace_details.p_unit_price','tbl_product.name','tbl_product.sap_code')
                        ->join('tbl_product', 'tbl_product.id', '=', 'mts_replace_details.product_id')
                        ->where('mts_replace_details.replace_det_id', $request->get('itemsid'))
                        ->first();

        return view('ModernSales::sales/replace/editItems', compact('resultPro'));
    }


    public function mts_replace_edit_submit(Request $request)
    {
        $replaceid    = $request->get('replace_id');
        
        $price          = $request->get('items_qty') * $request->get('items_price');
        
        DB::table('mts_replace_details')->where('replace_det_id',$request->get('id'))->update(
            [
                'order_qty'         => $request->get('items_qty'), 
                'order_total_value' => $price, 
                'replace_date'        => date('Y-m-d H:i:s') 
            ]
        );        
  
        $totalOrder = DB::table('mts_replace_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('replace_id', $replaceid)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->first();

             DB::table('mts_replace')->where('replace_id', $replaceid)                        
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

    public function mts_replace_items_delete($replaceid,$itemid)
    {
       
        DB::table('mts_replace_details')->where('replace_det_id',$itemid)->delete();        
  
        $totalOrder = DB::table('mts_replace_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('replace_id', $replaceid)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->first();

             DB::table('mts_replace')->where('replace_id', $replaceid)                        
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

    public function mts_confirm_replace(Request $request)
    {

        $orderid    = $request->get('orderid');
        $partyid    = $request->get('partyid');
        $customer_id    = $request->get('customer_id');
        $po_no      = $request->get('po_no');

        //dd($po_no);

        $totalOrder = DB::table('mts_replace_details')

                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('replace_id', $orderid)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();
             

        DB::table('mts_replace')->where('replace_id', $orderid)->where('fo_id', Auth::user()->id)->where('entry_by',Auth::user()->id)->update(
            [
                'po_no'                 => $po_no, 
                'order_status'          => 'Confirmed', 
                'ack_status'            => 'Pending', 
                'replace_date'          => date('Y-m-d H:i:s'),
                'total_order_qty'       => $totalOrder->totalQty,
                'total_order_value'     => $totalOrder->totalValue,
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        DB::table('mts_replace_details')
                ->where('replace_id', $orderid)
                ->update(
                    [
                        'order_det_status'          => 'Confirmed' 
                    ]
            );




        return Redirect::to('/mts-replace')->with('success', 'Successfully Confirmed Order');

    }

    public function mts_delete_replace($replace_id,$partyid,$customer_id)
    {
        
        $totalOrder = DB::table('mts_replace_details') 
                        ->where('replace_id', $replace_id)                        
                        ->where('entry_by',Auth::user()->id) 
                        ->delete();  


         DB::table('mts_replace')->where('replace_id',$replace_id)->delete(); 

        return Redirect::to('/mts-replace')->with('success', 'Successfully Delete Advance Replace');

        //return Redirect::back()->with('success', 'Successfully Delete Advance Replace.');

    }


    // Advance replace start for admin section

    public function mts_replace_approved()
    {
        $selectedMenu       = 'Replace Approve';
        $subSelectedMenu    = 'Order Delivery'; 
        $pageTitle          = 'Order Delivery'; 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.executive_id', Auth::user()->id)
        ->where('mts_replace.order_status', 'Confirmed')
        ->where('mts_replace.ack_status', 'Pending')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
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

        return view('ModernSales::sales/adminReport/replace', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }

    public function mts_replace_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive_id    = $request->get('executive_id');
        $officer    = $request->get('fos');

        if($fromdate!='' && $todate!='' && $executive_id!='' && $officer=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.executive_id', $executive_id)                 
            ->where('mts_replace.order_status', 'Confirmed')
            ->where('mts_replace.ack_status', 'Pending')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                  
            ->where('mts_replace.order_status', 'Confirmed')
            ->where('mts_replace.ack_status', 'Pending')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_replace.executive_id', $executive_id) 
            ->where('mts_replace.fo_id', $officer)
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/replaceList', compact('resultOrderList'));
    }


    public function mts_replace_view($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Delivery';                   // Required Variable
        $pageTitle      = 'Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id','mts_replace.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
        ->where('mts_replace.order_status','Confirmed')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace_details.replace_id',$DeliveryMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','users.display_name','mts_replace.party_id','mts_replace.replace_no','mts_replace.po_no','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.order_status','Confirmed')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$DeliveryMainId)
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

         $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.name','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$DeliveryMainId)
                        ->first();


        return view('ModernSales::sales/adminReport/replaceEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo','customerInfo'));
    }


    public function mts_approved_replace($orderid,$partyid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('mts_replace')->where('replace_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );
        }else{
            DB::table('mts_replace')->where('replace_id', $orderid)->where('party_id', $partyid)->update(
                [
                    'ack_status'  => 'Rejected'
                ]
            );
        }

        DB::commit();
        DB::rollBack();
        return Redirect::to('/mts-replace-approved')->with('success', 'Successfully Approved Order');

    }

    // Advance replace start for Billing section

    public function mts_replace_delivery()
    {
        $selectedMenu       = 'Replace Delivery';
        $subSelectedMenu    = 'Replace Delivery'; 
        $pageTitle          = 'Replace Delivery';

        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                       
        ->where('mts_replace.order_status', 'Confirmed')
        ->where('mts_replace.ack_status', 'Pending')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')               
        ->where('mts_replace.order_status', 'Confirmed')
        ->where('mts_replace.ack_status', 'Approved')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        return view('ModernSales::sales/delivery_Report/replace', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_replace_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                  
            ->where('mts_replace.order_status', 'Confirmed')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                  
            ->where('mts_replace.order_status', 'Confirmed')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_replace.fo_id', $request->get('fos'))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/delivery_Report/replaceList', compact('resultOrderList'));
    }

    public function mts_replace_delivery_edit($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Replace Delivery';                   // Required Variable
        $pageTitle      = 'Replace Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id','mts_replace.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
        ->where('mts_replace.order_status','Confirmed')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace_details.replace_id',$DeliveryMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','users.display_name','mts_replace.party_id','mts_replace.replace_no','mts_replace.po_no','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.order_status','Confirmed')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$DeliveryMainId)
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

        $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$DeliveryMainId)
                        ->first();


        return view('ModernSales::sales/delivery_Report/replaceDeliveryEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo','customerInfo'));
    }

    public function mts_replace_delivery_edit_submit(Request $request)
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
        $chalanNO = 'ARO-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;
        //dd($chalanNO);

        DB::table('mts_replace')->where('replace_id', $lastOrderId)
        ->where('fo_id', $request->get('foMainId'))
        ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_status'           => 'Delivered',
                'ack_status'             => 'Pending',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'delivery_date'          => date('Y-m-d H:i:s'),
                'update_date'            => date('Y-m-d H:i:s'),
                'chalan_no'              => $chalanNO,
                'chalan_date'            => date('Y-m-d H:i:s'),
            ]
        );

        ///////////
        $checkOrdata = DB::table('mts_replace')
        ->where('replace_id', $lastOrderId)
        ->first();

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('mts_replace_details')
                ->where('replace_id', $lastOrderId)
                ->where('product_id',$request->get('product_id')[$m])
                ->first(); 

                if(sizeof($checkItemsExiting)>0)
                {
                    DB::table('mts_replace_details')->where('replace_det_id',$request->get('replace_det_id')[$m])->update(
                        [
                            'replace_product_id'      => $request->get('change_product_id')[$m],
                            'deliverey_qty'           => $request->get('qty')[$m],
                            'delivery_value'          => $request->get('price')[$m],
                            'deliverey_date'          => date('Y-m-d H:i:s'),
                            'order_det_status'        => 'Delivered'
                        ]
                    );
                    
                }
            }
        }

        DB::commit();
        DB::rollBack();
        return Redirect::to('/mts-replace-delivery')->with('success', 'Successfully Confirm Delivery Done.'); 

    }

    // Advance Replace Delivery report for admin part


    public function mts_admin_replace_delivery_report()
    {
        $selectedMenu   = 'Advance Replace Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';             // Required Variable for submenu
        $pageTitle      = 'Advance Replace Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
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


        return view('ModernSales::sales/adminReport/replaceDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist','executivelist','officerlist'));
    }

    public function mts_admin_replace_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 
        $officer    = $request->get('fos');



        if($fromdate!='' && $todate!='' && $officer=='' && $customer=='')
        { 
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved') 
            ->where('mts_replace.executive_id',  Auth::user()->id)
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)  
            ->where('mts_replace.executive_id',  Auth::user()->id)
            ->where('mts_replace.fo_id', $officer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer!='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_replace.customer_id', $customer)
            ->where('mts_replace.fo_id', $officer) 
            ->where('mts_replace.executive_id',  Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/replaceDeliveryReportList', compact('resultOrderList'));
    }


    public function mts_admin_replace_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Replace Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Replace Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'mts_replace.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_replace.route_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_replace')
        ->select('mts_replace.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                        //->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_replace_details')
        ->select('mts_replace_details.delivery_challan','mts_replace.delivery_date','mts_replace_details.replace_id','mts_replace.replace_id','mts_replace.order_status')

        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                       // ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.replace_no','mts_replace.po_no','mts_replace.update_date','mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','mts_replace.party_id','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

         $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$orderMainId)
                        ->first();

 

        

        return view('ModernSales::sales/adminReport/replaceDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }

    // Advance Replace Delivery report for billing part


    public function mts_billing_replace_delivery_report()
    {
        $selectedMenu   = 'Advance Replace Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';             // Required Variable for submenu
        $pageTitle      = 'Advance Replace Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Pending')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Pending')
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

         $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.management_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.management_id')
          ->get();


        return view('ModernSales::sales/billingReport/replaceDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList','managementlist'));
    }

    public function mts_billing_replace_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer'); 



        if($fromdate!='' && $todate!='' && $customer=='')
        { 
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Pending') 
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();

            //dd($resultOrderList);
        }
        elseif($fromdate!='' && $todate!='' && $customer!='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Pending')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_replace.customer_id', $customer) 
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        
        return view('ModernSales::sales/billingReport/replaceDeliveryReportList', compact('resultOrderList'));
    }


    public function mts_billing_replace_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Replace Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Replace Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'mts_replace.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_replace.route_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_replace')
        ->select('mts_replace.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                        //->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_replace_details')
        ->select('mts_replace_details.delivery_challan','mts_replace.delivery_date','mts_replace_details.replace_id','mts_replace.replace_id','mts_replace.order_status')

        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                       // ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.replace_no','mts_replace.po_no','mts_replace.update_date','mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','mts_replace.party_id','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

         $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$orderMainId)
                        ->first();

 

        

        return view('ModernSales::sales/billingReport/replaceDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','customerInfo','resultDistributorInfo','resultFoInfo','resultAllChalan', 'orderCommission'));

    }

    // Advance Replace Delivery report for Executive part


    public function mts_replace_delivery_report()
    {
        $selectedMenu   = 'Advance Replace Report';                 // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';             // Required Variable for submenu
        $pageTitle      = 'Advance Replace Report';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->where('mts_replace.fo_id', Auth::user()->id)
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();

        $customer = DB::table('mts_customer_list')
                    ->where('status',0)
                    ->orderBy('name','ASC')    
                    ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Approved')
        ->where('mts_replace.fo_id', Auth::user()->id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();


        return view('ModernSales::sales/executiveReport/replaceDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','customer','resultOrderList'));
    }

    public function mts_replace_delivery_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        //$fo         = $request->get('fos');

        if($fromdate!='' && $todate!='' && $customer=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.fo_id', Auth::user()->id)
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        elseif($fromdate!='' && $todate!='' && $customer!='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Approved')
            ->where('mts_replace.fo_id', Auth::user()->id)
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id) 
            ->where('mts_replace.customer_id', $customer)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        
        return view('ModernSales::sales/executiveReport/replaceDeliveryReportList', compact('resultOrderList'));
    }


    public function mts_replace_delivery_report_details($orderMainId)
    {
        $selectedMenu   = 'Advance Replace Report';           // Required Variable for menu
        $selectedSubMenu= 'Advance Replace Report';                // Required Variable for submenu
        $pageTitle      = 'Advance Replace Details';              // Page Slug Title

        $resultDistributorInfo = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id',
            'tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
            'users.id','users.display_name','users.sap_code')

        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'mts_replace.fo_id', '=', 'users.id')
        ->leftjoin('tbl_route', 'tbl_route.route_id', '=', 'mts_replace.route_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        // dd($resultDistributorInfo);
        $resultFoInfo  = DB::table('mts_replace')
        ->select('mts_replace.fo_id','users.display_name','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')
        ->leftjoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')
        ->leftjoin('users', 'users.id', '=', 'tbl_user_details.user_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
                        //->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id')
        ->leftjoin('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                        //->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        $resultAllChalan  = DB::table('mts_replace_details')
        ->select('mts_replace_details.delivery_challan','mts_replace.delivery_date','mts_replace_details.replace_id','mts_replace.replace_id','mts_replace.order_status')

        ->leftjoin('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
                       // ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace_details.replace_id',$orderMainId)
        ->groupBy('mts_replace_details.delivery_challan')                        
        ->get();

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.replace_no','mts_replace.po_no','mts_replace.update_date','mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','mts_replace.party_id','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->leftjoin('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)       
        ->where('mts_replace.replace_id',$orderMainId)
        ->first();
 

        

        return view('ModernSales::sales/executiveReport/replaceDeliveryReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultDistributorInfo','resultFoInfo','resultAllChalan'));

    }

    // Delivery approved part


    public function mts_replace_delivery_approved()
    {
        // dd(Auth::user());
        $selectedMenu   = 'Replace Delivery Approved';
        $subSelectedMenu = 'Replace Delivery'; 
        $pageTitle      = 'Replace Delivery'; 

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('mts_replace')
        ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')  
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
         ->where('mts_replace.executive_id', Auth::user()->id)               
        ->where('mts_replace.order_status', 'Delivered')
        ->where('mts_replace.ack_status', 'Pending')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($todate, $todate))
        ->orderBy('mts_replace.replace_id','DESC')                    
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

        return view('ModernSales::sales/adminReport/replaceApprove', compact('selectedMenu','pageTitle','resultFO','resultOrderList','managementlist','officerlist'));
    }

    public function mts_replace_delivery_approved_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive  = $request->get('executive_id');
        $officer    = $request->get('fos'); 

        if($fromdate!='' && $todate!='' && $executive!='' && $officer=='')
        { 
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                                   
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                  
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Pending')
            ->where('mts_replace.executive_id', $executive)
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name', 'mts_party_list.name', 'mts_party_list.mobile','mts_party_list.address')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'mts_replace.fo_id')                    
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')                  
            ->where('mts_replace.order_status', 'Delivered')
            ->where('mts_replace.ack_status', 'Pending')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->where('mts_replace.fo_id', $officer)
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/adminReport/replaceApproveList', compact('resultOrderList'));
    }


    public function mts_replace_delivery_approved_view($DeliveryMainId,$foMainId)
    {
        $selectedMenu   = 'Replace Delivery Approved';                   // Required Variable
        $pageTitle      = 'Replace Delivery Details';           // Page Slug Title

        $resultCartPro  = DB::table('mts_replace_details')
        ->select('mts_replace_details.cat_id','mts_replace_details.replace_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','mts_replace.replace_id','mts_replace.fo_id','mts_replace.order_status','mts_replace.replace_no','mts_replace.po_no','mts_replace.party_id','mts_replace.global_company_id')
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'mts_replace_details.cat_id')
        ->join('mts_replace', 'mts_replace.replace_id', '=', 'mts_replace_details.replace_id')
        ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                       
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace_details.replace_id',$DeliveryMainId)
        ->groupBy('mts_replace_details.cat_id')                        
        ->get();

        //dd( $resultCartPro);

        $resultInvoice  = DB::table('mts_replace')->select('mts_replace.global_company_id','mts_replace.replace_id','mts_replace.order_status','mts_replace.fo_id','users.display_name','mts_replace.party_id','mts_replace.replace_no','mts_replace.po_no','mts_replace.replace_date','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
        ->where('mts_replace.order_status','Delivered')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)                        
        ->where('mts_replace.fo_id',$foMainId)                        
        ->where('mts_replace.replace_id',$DeliveryMainId)
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

        $customerInfo = DB::table('mts_replace')
                        ->select('mts_replace.replace_id','mts_replace.order_status','mts_customer_list.name','mts_customer_list.customer_id','mts_customer_list.address','mts_customer_list.route_id','mts_customer_list.sap_code')
                        ->join('mts_customer_list', 'mts_replace.customer_id', '=', 'mts_customer_list.customer_id') 
                        ->where('mts_replace.replace_id',$DeliveryMainId)
                        ->first();

        return view('ModernSales::sales/adminReport/replaceApproveEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo','customerInfo'));
    }



    public function mts_replace_delivery_approved_submit($orderid,$customerid,$status)
    {
        DB::beginTransaction();

        if($status=='yes'){

            DB::table('mts_replace')->where('replace_id', $orderid)->where('customer_id', $customerid)->update(
                [
                    'ack_status'  => 'Approved'
                ]
            );

            $totalSales = DB::table('mts_replace')
            ->join('mts_customer_list', 'mts_customer_list.customer_id', 'mts_replace.customer_id')
            ->where('replace_id', $orderid)
            ->where('order_status', 'Delivered')
            ->first();


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
                        'ref_id'                => $totalSales->replace_id,
                        'trans_type'            => 'sales',
                        'party_sap_code'        => $totalSales->sap_code,
                        'opening_balance'       => $closing_balance,
                        'debit'                 => 0,
                        'credit'                => $totalSales->total_delivery_value,
                        'closing_balance'       => $closing_balance+$totalSales->total_delivery_value,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')

                    ]
                );

            }
        }else{
            DB::table('mts-replace-delivery-approved')->where('replace_id', $orderid)->where('customer_id', $customerid)->update(
                [
                    'ack_status'  => 'Rejected'
                ]
            );
        }

        DB::commit();
        DB::rollBack();
        return Redirect::to('/mts-replace-delivery-approved')->with('success', 'Successfully Approved Order');

    }


    public function mts_replace_not_approve()
    {
        $selectedMenu   = 'Replace Not approve';              // Required Variable for menu
        $selectedSubMenu= 'Replace Not approve';             // Required Variable for submenu
        $pageTitle      = 'Replace Not approve';            // Page Slug Title
        //dd(Auth::user()->id);
        $resultFO       = DB::table('mts_replace')
        ->select('mts_replace.global_company_id','mts_replace.order_status','mts_replace.replace_id','mts_replace.fo_id','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
        ->join('users', 'users.id', '=', 'mts_replace.fo_id')
        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
        ->where('mts_replace.order_status', 'Ordered')
        ->where('mts_replace.ack_status', 'Rejected')
        ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
        ->where('mts_replace.fo_id', Auth::user()->id)
        ->groupBy('mts_replace.fo_id')
        ->orderBy('mts_replace.replace_id','DESC')                    
        ->get();


        $todate     = date('Y-m-d');
         $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Ordered')
            ->where('mts_replace.ack_status', 'Rejected')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->where('mts_replace.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($todate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
  
        return view('ModernSales::sales/replace/replaceNotApprove', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function mts_replace_not_approve_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Ordered')
            ->where('mts_replace.ack_status', 'Rejected')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->where('mts_replace.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        else
        {
            $resultOrderList = DB::table('mts_replace')
            ->select('mts_replace.*','tbl_user_details.user_id','users.display_name','users.email','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','mts_party_list.name','mts_party_list.mobile','mts_party_list.address')
            ->join('users', 'users.id', '=', 'mts_replace.fo_id')
            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
            ->join('mts_party_list', 'mts_party_list.party_id', '=', 'mts_replace.party_id')
            ->where('mts_replace.order_status', 'Ordered')
            ->where('mts_replace.ack_status', 'Rejected')
            ->where('mts_replace.global_company_id', Auth::user()->global_company_id)
            ->where('mts_replace.fo_id', Auth::user()->id)
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_replace.replace_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->orderBy('mts_replace.replace_id','DESC')                    
            ->get();
        }
        
        return view('ModernSales::sales/replace/replaceNotApproveList', compact('resultOrderList'));
    }


}
