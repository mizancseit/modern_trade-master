<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session; 

class WastageController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 10/12/2017
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function wastage()
    {

        $selectedMenu   = 'Requisition';         // Required Variable
        $pageTitle      = 'Wastage';        // Page Slug Title

        $routeResult = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)  
                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)  
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

        $checkRoutes = DB::table('tbl_order')->select('route_id')
                        ->where('fo_id', Auth::user()->id)  
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->orderBy('order_id','DESC')
                        ->first();

        $routeID = '';
        if(sizeof($checkRoutes)>0)
        {
            $routeID = $checkRoutes->route_id;
        }
        else
        {
            $checkRoutes = DB::table('ims_tbl_visit_order')->select('routeid')
                        ->where('foid', Auth::user()->id)
                        ->orderBy('date','DESC')
                        ->first();

            if(sizeof($checkRoutes)>0)
            {
                $routeID = $checkRoutes->routeid;
            }
        }

        //dd($routeID);

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid')
                        ->where('global_company_id', Auth::user()->global_company_id)                     
                        ->where('rid', $routeID)
                        ->orderBy('name','ASC')                    
                        ->get();           

        return view('sales.wastage.wastageManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','routeID'));
    }

    public function wastage_retailer(Request $request)
    {
        $routeID = $request->get('route');

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','status')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('rid', $routeID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();

        return view('sales.wastage.retailers', compact('resultRetailer','routeID'));
    }

    public function wastage_process($retailderid,$routeid)
    {
        $selectedMenu   = 'Requisition';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.is_active', 0) // 0 for active
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->first();

        if(sizeof($resultPoint) >0 )
        {
            $pointID = $resultPoint->point_id;
        }
        else
        {
            $pointID = '';
        }

        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointID)
                         ->where('tbl_user_business_scope.is_active', 0) // 0 for active
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid')                       
                        ->where('retailer_id', $retailderid)                    
                        ->first();

        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();

        $resultCart     = DB::table('tbl_wastage')
                        ->where('order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();

        return view('sales.wastage.categoryWiseWastage', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart'));
    }

    public function wastage_products(Request $request)
    {
        $categoryID = $request->get('categories');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        return view('sales.wastage.wastageProductsList', compact('resultProduct','categoryID'));
    }

    public function wastage_add_to_cart_products(Request $request)
    {
        $autoOrder = DB::table('tbl_wastage')->select('auto_order_no')->orderBy('order_id','DESC')->first();

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
        $currentDay     = date("d");           // 14
        $retailerID     = $request->get('retailer_id');

        $orderNo        = $retailerID.'-'.$currentYear.$currentMonth.$currentDay.$autoOrderId;


        $totalQty   = 0;
        $totalValue   = 0;

        $countRows = count($request->get('wastageQty'));        

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('wastageQty')[$m];
            $totalValue   = $totalValue + $request->get('value')[$m];
        } 

        $resultCart     = DB::table('tbl_wastage')
                        ->where('order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)                        
                        ->first();

       //dd($resultCart);

        if(sizeof($resultCart)== 0)
        {
            DB::table('tbl_wastage')->insert(
                [
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => date('Y-m-d H:i:s'),
                    'distributor_id'        => $request->get('distributor_id'),
                    'point_id'              => $request->get('point_id'),
                    'route_id'              => $request->get('route_id'),
                    'retailer_id'           => $request->get('retailer_id'),
                    'fo_id'                 => Auth::user()->id,
                    'total_qty'             => $totalQty,
                    'total_value'           => $totalValue,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s'),
                    'global_company_id'     => Auth::user()->global_company_id
                    //'is_active'             => 0  is_active 0 is edit option active and 1 is confirm
                ]
            );

            $lastOrderId = DB::table('tbl_wastage')->latest('order_id')->first();

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('wastageQty')[$m]!='')
                {
                    
                    DB::table('tbl_wastage_details')->insert(
                        [
                            'order_id'          => $lastOrderId->order_id,
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'wastage_qty'       => $request->get('wastageQty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'p_total_price'     => $request->get('value')[$m]
                        ]
                    ); 
                }
            }


            return Redirect::to('/wastage-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Added Add To Cart.');
        }
        elseif(sizeof($resultCart) > 0)
        {
            $lastOrderId    = $resultCart->order_id;

            $oldGrandQty    = $resultCart->total_qty;
            $newGrandQty    = $oldGrandQty + $totalQty;
            $newGrandValue    = $resultCart->total_value + $totalValue;

            DB::table('tbl_wastage')->where('order_id', $lastOrderId)
                ->where('fo_id', Auth::user()->id)
                ->where('order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_qty'             => $newGrandQty,
                    'total_value'         => $newGrandValue,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('wastageQty')[$m]!='')
                {
                    $checkItemsExiting = DB::table('tbl_wastage_details')
                                    ->select('tbl_wastage_details.*','tbl_wastage.order_id','tbl_wastage.order_type')
                                    ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                                    ->where('tbl_wastage.order_type', 'Ordered')
                                    ->where('tbl_wastage.order_id', $lastOrderId)
                                    ->where('tbl_wastage_details.product_id',$request->get('produuct_id')[$m])
                                    ->first();

                    if(sizeof($checkItemsExiting)>0)
                    {
                        $upMainWas = $checkItemsExiting->wastage_qty + $request->get('wastageQty')[$m];
                        $upMainValue = $checkItemsExiting->p_total_price + $request->get('value')[$m];
                       

                        DB::table('tbl_wastage_details')->where('product_id',$request->get('produuct_id')[$m])->update(
                            [
                                'wastage_qty'       => $upMainWas,
                                'p_unit_price'      => $request->get('price')[$m],
                                'p_total_price'     => $upMainValue
                            ]
                        );
                    }
                    else
                    {
                        DB::table('tbl_wastage_details')->insert(
                            [
                                'order_id'          => $lastOrderId,
                                'cat_id'            => $request->get('category_id')[$m],
                                'product_id'        => $request->get('produuct_id')[$m],
                                'wastage_qty'       => $request->get('wastageQty')[$m],
                                'p_total_price'     => $request->get('value')[$m]
                            ]
                        );
                    }
                }
            }

            return Redirect::to('/wastage-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Updated Add To Cart.');
        } 
    }




    public function wastage_bucket($pointid,$routeid,$retailderid)
    {
        $selectedMenu   = 'Requisition';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title

        // for FO Information

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id       = $resultFoInfo->point_id;
        $division_id    = $resultFoInfo->division_id;
  
        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointid)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid')                       
                        ->where('retailer_id', $retailderid)                    
                        ->first();

        $resultCartPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.order_no','tbl_wastage.retailer_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')

                        ->where('tbl_wastage.order_type','Ordered')                        
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage.retailer_id',$retailderid)
                        ->groupBy('tbl_wastage_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_wastage')->select('order_id','order_type','fo_id','retailer_id','order_no','auto_order_no')
                        ->where('order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();


        return view('sales.wastage.wastageBucket', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice'));
    }


    public function wastage_items_edit(Request $request)
    {

        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');

        $resultPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.order_det_id','tbl_wastage_details.order_id','tbl_wastage_details.product_id','tbl_wastage_details.wastage_qty','tbl_wastage_details.p_unit_price','tbl_product.name')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                        ->where('tbl_wastage_details.order_det_id', $request->get('id'))
                        ->first();

        return view('sales.wastage.editWastageItems', compact('resultPro','pointid','retailderid','routeid','catid'));
    }

    public function wastage_items_edit_submit(Request $request)
        {
            $pointid        = $request->get('pointID');
            $retailderid    = $request->get('retailderID');
            $routeid        = $request->get('routeID');
            $product_id     = $request->get('product_id');

            $productlist  = DB::table('tbl_product')
                        ->where('tbl_product.id',$product_id )
                        ->first();
            $totalPrice = $request->get('items_wastage') * $productlist->depo;

            DB::table('tbl_wastage_details')->where('order_det_id',$request->get('id'))->update(
                [
                    
                    'wastage_qty'       => $request->get('items_wastage'),
                    'p_unit_price'      => $productlist->depo,
                    'p_total_price'     =>$totalPrice
                ]
            );

            $totalQty = DB::table('tbl_wastage_details')
                                    ->select('order_id', DB::raw('SUM(wastage_qty) AS wastageQty'),DB::raw('SUM(p_total_price) AS wastageValue'))
                                    ->where('order_id', $request->get('order_id'))
                                    ->groupBy('order_id')
                                    ->first();

            DB::table('tbl_wastage')->where('order_id', $totalQty->order_id)
                ->where('fo_id', Auth::user()->id)
                ->where('order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_qty'             => $totalQty->wastageQty,
                    'total_value'           => $totalQty->wastageValue,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            ); 
       
            return Redirect::back()->with('success', 'Successfully Updated Order Product.');
        }



    public function wastage_items_delete(Request $request)
    {
        $orderID     = $request->get('orderID');

        $id = $request->get('id');

        //dd($id);

        $itemsDelete = DB::table('tbl_wastage_details')->where('order_det_id',$id)->delete();


        $totalQty = DB::table('tbl_wastage_details')
                     ->select('order_id', DB::raw('SUM(wastage_qty) AS wastageQty'),DB::raw('SUM(p_total_price) AS wastageValue'))
                    ->where('order_id', $orderID)
                    ->groupBy('order_id')
                    ->first();
        //dd($totalQty);
                    
         DB::table('tbl_wastage')->where('order_id', $orderID)
                ->where('fo_id', Auth::user()->id)
                ->where('order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_qty'             => $totalQty->wastageQty,
                    'total_value'           => $totalQty->wastageValue,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );

        
            return Redirect::back()->with('success', 'Successfully Deleted Wastage Product.');
             
    }



    public function ssg_confirm_wastage(Request $request, $orderpid,$orderid,$retailderid,$routeid,$pointid,$distributorID)
    {

        DB::table('tbl_visit_wastage')->insert(
            [
                'date'                  => date('Y-m-d H:i:s'),
                'disid'                 => $distributorID,                
                'retailerid'            => $retailderid,    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $routeid,    
                'visit'                 => 1,    
                'order'                 => 1,    
                'order_no'              => $orderid, 
                'remarks'               => '',    
                'nonvisitedremarks'     => '',
                'entrydate'             => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 3
            ]
        );

        DB::table('tbl_wastage')->where('order_id', $orderpid)->where('fo_id', Auth::user()->id)->update(
            [
                'order_type'             => 'Confirmed'
            ]
        );

        return Redirect::to('/wastage')->with('success', 'Successfully Confirmed Wastage.');
    }





    public function ssg_delete_wastage(Request $request,$orderid,$retailderid,$routeid)
    {
        $lastOrderId = DB::table('tbl_wastage')->select('order_type','fo_id','order_id','order_no')
                    ->where('order_id',$orderid)->first();

        $items      = DB::table('tbl_wastage_details')
                    ->where('order_id',$orderid)                   
                    ->get();

        foreach ($items as $value) 
        {
            DB::table('tbl_wastage_details')->where('order_id', $value->order_id)->delete();
        }
        
        DB::table('tbl_visit_wastage')->where('order_no', $lastOrderId->order_no)->delete();

        DB::table('tbl_wastage')->where('order_id', $orderid)->delete();
       
        return Redirect::to('/wastage-process/'.$retailderid.'/'.$routeid)->with('success', 'Delete has been Successfully.');
    }

    

    

    


    /* 
       =====================================================================
       ============================ Visit Manage ===========================
       =====================================================================
    */

    public function ssg_visit_process_order($retailerID,$routeID)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('ims_visit_reason')
                        ->where('type', 1)                    
                        ->get();

         return view('sales.visit.orderVisitOnly', compact('selectedMenu','pageTitle','retailerID','routeID','resultReason')); 
    }  


    public function ssg_visit_process_submit(Request $request)
    {

        //dd($request->all());

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->first();

        if(sizeof($resultPoint) >0 )
        {
            $pointID = $resultPoint->point_id;
        }
        else
        {
            $pointID = '';
        }

        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointID)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        DB::table('ims_tbl_visit_order')->insert(
            [
                'disid'                 => $distributorID,                
                'retailerid'            => $request->get('retailerID'),    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $request->get('routeID'),    
                'visit'                 => 1,    
                'order'                 => '',    
                'order_no'              => '', 
                'remarks'               => $request->get('remarks'),    
                'nonvisitedremarks'     => '',
                'entrydate'             => date('Y-m-d H:i:s'),
                'date'                  => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 2,
                'reasonid'              => $request->get('reasons')
            ]
        );

        return Redirect::to('/visit')->with('success', 'Successfully Visit Done.'); 
    }
   

    /* 
       =====================================================================
       ============================ Non-visit Manage =======================
       =====================================================================
    */

       public function ssg_nonvisit_process_order($retailerID,$routeID)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Non-visit';           // Page Slug Title

        $resultReason  = DB::table('ims_visit_reason')
                        ->where('type', 2)                    
                        ->get();

         return view('sales.visit.orderNonVisitOnly', compact('selectedMenu','pageTitle','retailerID','routeID','resultReason')); 
    }

    public function ssg_nonvisit_process_submit(Request $request)
    {

        //dd($request->all());

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->first();

        if(sizeof($resultPoint) >0 )
        {
            $pointID = $resultPoint->point_id;
        }
        else
        {
            $pointID = '';
        }

        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointID)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        DB::table('ims_tbl_visit_order')->insert(
            [
                'disid'                 => $distributorID,                
                'retailerid'            => $request->get('retailerID'),    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $request->get('routeID'),    
                'visit'                 => 1,    
                'order'                 => '',    
                'order_no'              => '', 
                'remarks'               => '',    
                'nonvisitedremarks'     => $request->get('remarks'),
                'entrydate'             => date('Y-m-d H:i:s'),
                'date'                  => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 1,
                'reasonid'              => $request->get('reasons')
            ]
        );

        return Redirect::to('/visit')->with('success', 'Successfully Non-visit Done.'); 
        //return Redirect::back()->with('success', 'Successfully Non-visit Done.'); 
    }


    /* 
       =====================================================================
       ============================ Order Manage ===========================
       =====================================================================
    */    

    public function ssg_order_manage()
    {
        $selectedMenu   = 'Order Manage';             // Required Variable
        $pageTitle      = 'Order Manage';            // Page Slug Title

        $fromdate = date('Y-m-d');
        $todate   = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.visit.orderManage', compact('selectedMenu','pageTitle','resultOrderList'));
    }

    public function ssg_invoice_details_order($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order Manage';                      // Required Variable
        $pageTitle      = 'Invoice Details';                  // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->whereNotNull('og.orderid')
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)                                
                                ->first();
        }

        return view('sales.distributor.invoiceDetails', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift'));
    }

    public function ssg_order_edit_process($orderId,$retailderid,$routeid)
    {
        $selectedMenu   = 'Order Manage';             // Required Variable Menu
        $selectedSubMenu= '';                        // Required Variable Submenu
        $pageTitle      = 'Edit Order';             // Page Slug Title

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->first();

        if(sizeof($resultPoint) >0 )
        {
            $pointID = $resultPoint->point_id;
        }
        else
        {
            $pointID = '';
        }

        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointID)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid')                       
                        ->where('retailer_id', $retailderid)                    
                        ->first();

        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();

        $resultCart     = DB::table('tbl_order')
                        ->where('order_type','Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();

        return view('sales.visit.categoryWithOrderEdit', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart'));
    }

    public function ssg_add_to_edit_cart_products(Request $request)
    {
  
        $totalQty   = 0;
        $totalValue = 0;

        $countRows = count($request->get('qty'));        

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('qty')[$m];
            $totalValue = $totalValue + $request->get('qty')[$m] * $request->get('price')[$m];
        }

        $retailerID     = $request->get('retailer_id');
        $lastOrderId    = $request->get('order_id');

        $resultCart     = DB::table('tbl_order')
                        ->where('order_id', $lastOrderId)
                        ->where('order_type','Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)                        
                        ->where('order_id', $lastOrderId)             
                        ->first();
        
        $oldGrandTotal  = $resultCart->grand_total_value;
        $newGrandTotal  = $oldGrandTotal + $totalValue;

        $oldGrandQty    = $resultCart->total_qty;
        $newGrandQty    = $oldGrandQty + $totalQty;

        DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', Auth::user()->id)
            ->where('order_type', 'Confirmed')
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'total_qty'             => $newGrandQty,
                'total_value'           => $newGrandTotal,
                'grand_total_value'     => $newGrandTotal,
                'entry_by'              => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                $checkItemsExiting = DB::table('tbl_order_details')
                                ->select('tbl_order_details.*','tbl_order.order_id','tbl_order.order_type')
                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                ->where('tbl_order.order_type', 'Confirmed')
                                ->where('tbl_order_details.order_id', $lastOrderId)
                                ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $upMainQty = $checkItemsExiting->order_qty + $request->get('qty')[$m];
                    $upMainWas = $checkItemsExiting->wastage_qty + $request->get('wastageQty')[$m];
                    $upMainPri = $upMainQty * $request->get('price')[$m];

                    DB::table('tbl_order_details')->where('product_id',$request->get('produuct_id')[$m])->update(
                        [
                            'order_qty'         => $upMainQty,
                            'wastage_qty'       => $upMainWas,
                            'p_total_price'     => $upMainPri,
                            'p_grand_total'     => $upMainPri
                        ]
                    );
                }
                else
                {
                    DB::table('tbl_order_details')->insert(
                        [
                            'order_id'          => $lastOrderId,
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'wastage_qty'       => $request->get('wastageQty')[$m],                            
                            'p_unit_price'      => $request->get('price')[$m],
                            'p_total_price'     => $totalPrice,
                            'p_grand_total'     => $totalPrice
                        ]
                    );
                }
            }
        }

        return Redirect::back()->with('success', 'Successfully Updated Add To Cart.');        
    }

    public function ssg_bucket_edit($pointid,$routeid,$retailderid)
    {
        $selectedMenu   = 'Order Manage';           // Required Variable
        $pageTitle      = 'Bucket Edit';           // Page Slug Title
  
        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                         ->where('tbl_user_business_scope.point_id', $pointid)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        if(sizeof($resultDistributor) >0 )
        {
            $distributorID = $resultDistributor->id;
        }
        else
        {
            $distributorID = '';
        }

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid')                       
                        ->where('retailer_id', $retailderid)                    
                        ->first();

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')

                        ->where('tbl_order.order_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)            
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','global_company_id','auto_order_no')
                        ->where('tbl_order.order_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', Auth::user()->id)
                        ->where('tbl_order_gift.retailer_id', $retailderid)
                        ->first();

        // for FO Information

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id       = $resultFoInfo->point_id;
        $division_id    = $resultFoInfo->division_id;

        $currentDay = date('Y-m-d');

        $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
         FROM
         tbl_bundle_offer
         LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
         WHERE 
         tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.fo_id', Auth::user()->id)                                
                                ->where('og.retailer_id', $retailderid)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.fo_id', Auth::user()->id)                                
                                ->where('og.retailer_id', $retailderid)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->first();
        }

        return view('sales.visit.bucketEdit', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice','resultBundleOffersGift','resultBundleOffers'));
    }
}
