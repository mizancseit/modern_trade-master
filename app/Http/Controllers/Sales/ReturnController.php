<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session; 

class ReturnController extends Controller
{
    /**
    *
    * Created by Zubair Mahmudul Huq
    * Date : 21/05/2018
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    
	public function returnproduct()
    {

        $selectedMenu   = 'Return';         // Required Variable
        $pageTitle      = 'Return';        // Page Slug Title

        $routeResult = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)  
                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)  
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

        $checkRoutes = DB::table('tbl_return')->select('route_id')
                        ->where('fo_id', Auth::user()->id)  
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->orderBy('return_order_id','DESC')
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

        return view('sales.return.returnManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','routeID'));
    }

    
	
	public function return_retailer(Request $request)
    {
        $routeID = $request->get('route');

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','status')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('rid', $routeID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();

        return view('sales.return.retailers', compact('resultRetailer','routeID'));
    }

    
	
	public function return_process($retailderid,$routeid)
    {
        $selectedMenu   = 'Return';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

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

        $resultCart     = DB::table('tbl_return')
                        ->where('return_order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();

        return view('sales/return/categoryWiseReturn', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart'));
    }

    
	
	// public function return_products(Request $request)
 //    {
 //        $categoryID = $request->get('categories');
 //        $array      = $request->get('categories');

 //        if($array==4)
 //        {
 //            $array = array('5');
 //        }
 //        $resultProductDropDown = DB::table('tbl_product')
 //                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
 //                        ->where('ims_stat', '0')                       
 //                        ->where('category_id', $array)
 //                        ->orderBy('id', 'ASC')
 //                        ->orderBy('status', 'ASC')
 //                        ->orderBy('name', 'ASC')
 //                        ->get();

 //        $resultProduct = DB::table('tbl_product')
 //                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
 //                        ->where('ims_stat', '0')                       
 //                        ->where('category_id', $categoryID)
 //                        ->orderBy('id', 'ASC')
 //                        ->orderBy('status', 'ASC')
 //                        ->orderBy('name', 'ASC')
 //                        ->get();
                        
 //        $pcategory=DB::table('tbl_product_category')->where('status',0)
 //                    ->where('gid', Auth::user()->business_type_id)
 //                    ->where('global_company_id', Auth::user()->global_company_id)
 //                    ->get();                

 //        return view('sales/return/returnProductsList', compact('resultProduct','categoryID','pcategory','resultProductDropDown'));
 //    }


    public function return_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $array      = $request->get('categories');
        $return_id = $request->get('return_id');

        // if($array==4)
        // {
        //     $array = array('4','5');
        // }
        // else if($array==11)
        // {
        //     $array = array('11','5');
        // }
        // else if($array==5)
        // {
        //     $array = array('5','11');
        // }

        $resultProductDropDown = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $array)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

                        //dd($resultProductDropDown);

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
                        
        $pcategory=DB::table('tbl_product_category')->where('status',0)
                    ->where('gid', Auth::user()->business_type_id)
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->get();                

        return view('sales/return/returnProductsList', compact('resultProduct','categoryID','pcategory','resultProductDropDown','return_id'));
    }
	
	
	public function get_product(Request $request)
    {
          $id=$request->input('id');
          $serial=$request->input('slID');

          $product=DB::table('tbl_product')
                     ->where('category_id',$id)
                     ->get();

        return view('Sales.return.getProduct' , compact('product','serial'));
        
    }
	
	public function get_product_price(Request $request)
    {
          $id=$request->input('id');

          $product=DB::table('tbl_product')
                     ->where('id',$id)
                     ->get();

        return $product[0]->depo;
        
    }

    
	public function return_add_to_cart_products(Request $request)
    {
        //dd($request->all());

        $autoOrder = DB::table('tbl_return')->select('return_auto_order_no')->orderBy('return_order_id','DESC')->first();

        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->return_auto_order_no + 1;
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

    // for total return qnty    
        $totalReturnQty   = 0;
        $countReturnRows = count($request->get('return_qty'));        
        for($m=0;$m<$countReturnRows;$m++)
        {
            $totalReturnQty   = $totalReturnQty + $request->get('return_qty')[$m];
        }

    // for total return value       
        $totalReturnValue   = 0;
        $countReturnValueRows = count($request->get('return_value'));        
        for($m=0;$m<$countReturnValueRows;$m++)
        {
            $totalReturnValue   = $totalReturnValue + $request->get('return_value')[$m];
        }   
        
    // for total change qty and change value
        $totalChangeValue   = 0; 
        $totalChangeQty     = 0;     
        $countChangeRows = count($request->get('change_qty')); 

        for($m1=0;$m1<$countChangeRows;$m1++)
        {
            $totalChangeValue += $request->get('change_value')[$m1];
            $totalChangeQty += $request->get('change_qty')[$m1];
        }

        if($this->return_change_validate($totalReturnValue,$totalChangeValue))
        {

            $resultCart     = DB::table('tbl_return')
                    ->where('return_order_type','Ordered')                        
                    ->where('fo_id',Auth::user()->id)                        
                    ->where('retailer_id',$retailerID)                        
                    ->first();

       //dd($resultCart);
        
            if(sizeof($resultCart)== 0)
            {
                DB::table('tbl_return')->insert(
                    [
                        'return_order_no'       => $orderNo,
                        'return_auto_order_no'  => $autoOrderId,
                        'return_order_date'     => date('Y-m-d h:i:s'),
                        'distributor_id'        => $request->get('distributor_id'),
                        'point_id'              => $request->get('point_id'),
                        'route_id'              => $request->get('route_id'),
                        'retailer_id'           => $request->get('retailer_id'),
                        'fo_id'                 => Auth::user()->id,
                        'total_return_qty'      => $totalReturnQty,
                        'total_return_value'    => $totalReturnValue,
                        'total_change_qty'      => $totalChangeQty,
                        'total_change_value'    => $totalChangeValue,
                        'entry_by'              => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost(),
                        'entry_date'            => date('Y-m-d h:i:s'),
                        'global_company_id'     => Auth::user()->global_company_id
                        //'is_active'             => 0  is_active 0 is edit option active and 1 is confirm
                    ]
                );

                $lastOrderId = DB::table('tbl_return')->latest('return_order_id')->first();

                for($m=0;$m<$countReturnRows;$m++)
                {
                    if($request->get('return_qty')[$m]!='')
                    {
                        
                        DB::table('tbl_return_details')->insert(
                            [
                                'return_order_id'          => $lastOrderId->return_order_id,
                                'return_cat_id'            => $request->get('category_id')[$m],
                                'return_product_id'        => $request->get('produuct_id')[$m],
                                'return_qty'               => $request->get('return_qty')[$m],
                                'return_value'             => $request->get('return_value')[$m],
                                'change_cat_id'            => $request->get('change_cat_id')[$m],
                                'change_product_id'        => $request->get('change_product_id')[$m],
                                'change_qty'               => $request->get('change_qty')[$m],
                                'change_value'             => $request->get('change_value')[$m]
                            ]
                        ); 
                    }
                }

                for($m=0;$m<$countChangeRows;$m++)
                {
                    if($request->get('change_qty')[$m]!='' && $request->get('return_qty')[$m]=='')
                    {                        
                        DB::table('tbl_return_details')->insert(
                        [
                        'return_order_id'          => $lastOrderId->return_order_id,
                        'return_cat_id'            => $request->get('category_id')[$m],
                        'return_product_id'        => $request->get('produuct_id')[$m],
                        'return_qty'               => 0,
                        'return_value'             => Null,
                        'change_cat_id'            => $request->get('change_cat_id')[$m],
                        'change_product_id'        => $request->get('change_product_id')[$m],
                        'change_qty'               => $request->get('change_qty')[$m],
                        'change_value'             => $request->get('change_value')[$m]
                        ]
                        ); 
                    }
                }


                return Redirect::to('/return-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Add To Cart.');
            }
            elseif(sizeof($resultCart) > 0)
            {
                $lastOrderId    = $resultCart->return_order_id;
                $oldGrandQty    = $resultCart->total_return_qty;
                $newGrandQty    = $oldGrandQty + $totalReturnQty;
                $returnQty      = $resultCart->total_return_qty + $totalReturnQty;
                $returnValue    = $resultCart->total_return_value + $totalReturnValue;
                $changeQty      = $resultCart->total_change_qty + $totalChangeQty;
                $changeValue    = $resultCart->total_change_value + $totalChangeValue;

                //dd($changeValue);
                DB::table('tbl_return')->where('return_order_id', $lastOrderId)
                    ->where('fo_id', Auth::user()->id)
                    ->where('return_order_type', 'Ordered')
                    ->where('global_company_id', Auth::user()->global_company_id)->update(
                    [
                        
                        'total_return_qty'      => $returnQty,
                        'total_return_value'    => $returnValue,
                        'total_change_qty'      => $changeQty,
                        'total_change_value'    => $changeValue,
                        'entry_by'              => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost(),
                        'entry_date'            => date('Y-m-d h:i:s')
                    ]
                ); 

                for($m=0;$m<$countReturnRows;$m++)
                {
                    if($request->get('return_qty')[$m]!='')
                    {
                        $checkItemsExiting = DB::table('tbl_return_details')
                                        ->select('tbl_return_details.*','tbl_return.return_order_id','tbl_return.return_order_type')
                                        ->join('tbl_return', 'tbl_return.return_order_id', '=', 'tbl_return_details.return_order_id')
                                        ->where('tbl_return.return_order_type', 'Ordered')
                                        ->where('tbl_return.return_order_id', $lastOrderId)
                                        ->where('tbl_return_details.return_product_id',$request->get('produuct_id')[$m])
                                        ->first();

                        if(sizeof($checkItemsExiting)>0)
                        {
                            $totRetQty          = $checkItemsExiting->return_qty    + $request->get('return_qty')[$m];
                            $totRetValue        = $checkItemsExiting->return_value  + $request->get('return_value')[$m];
                            $totChangeQty       = $checkItemsExiting->change_qty    + $request->get('change_qty')[$m];
                            $totChangeValue     = $checkItemsExiting->change_value  + $request->get('change_value')[$m];
                           

                            DB::table('tbl_return_details')->where('return_product_id',$request->get('produuct_id')[$m])->update(
                                [
                                    'return_qty'       => $totRetQty,
                                    'return_value'     => $totRetValue,
                                    'change_qty'       => $totChangeQty,
                                    'change_value'     => $totChangeValue
                                    
                                ]
                            );
                        }
                        else
                        {
                            DB::table('tbl_return_details')->insert(
                                [
                                    'return_order_id'          => $lastOrderId,
                                    'return_cat_id'            => $request->get('category_id')[$m],
                                    'return_product_id'        => $request->get('produuct_id')[$m],
                                    'return_qty'               => $request->get('return_qty')[$m],
                                    'return_value'             => $request->get('return_value')[$m],
                                    'change_cat_id'            => $request->get('category_id')[$m],
                                    'change_product_id'        => $request->get('change_product_id')[$m],
                                    'change_qty'               => $request->get('change_qty')[$m],
                                    'change_value'             => $request->get('change_value')[$m]
                                ]
                            );
                        }
                    }
                }

                for($m=0;$m<$countChangeRows;$m++)
                {
                    if($request->get('change_qty')[$m]!='' && $request->get('return_qty')[$m]=='')
                    {
                        $checkItemsExiting = DB::table('tbl_return_details')
                                        ->select('tbl_return_details.*','tbl_return.return_order_id','tbl_return.return_order_type')
                                        ->join('tbl_return', 'tbl_return.return_order_id', '=', 'tbl_return_details.return_order_id')
                                        ->where('tbl_return.return_order_type', 'Ordered')
                                        ->where('tbl_return.return_order_id', $lastOrderId)
                                        ->where('tbl_return_details.return_product_id',$request->get('produuct_id')[$m])
                                        ->first();

                        if(sizeof($checkItemsExiting)>0)
                        {
                            $totChangeQty       = $checkItemsExiting->change_qty    + $request->get('change_qty')[$m];
                            $totChangeValue     = $checkItemsExiting->change_value  + $request->get('change_value')[$m];                           

                            DB::table('tbl_return_details')->where('change_product_id',$request->get('produuct_id')[$m])->update(
                                [
                                    'change_qty'       => $totChangeQty,
                                    'change_value'     => $totChangeValue
                                ]
                            );
                        }
                        else
                        {
                            for($m=0;$m<$countChangeRows;$m++)
                            {
                                if($request->get('change_qty')[$m]!='' && $request->get('return_qty')[$m]=='')
                                {                        
                                    DB::table('tbl_return_details')->insert(
                                    [
                                    'return_order_id'          => $lastOrderId,
                                    'return_cat_id'            => $request->get('category_id')[$m],
                                    'return_product_id'        => $request->get('produuct_id')[$m],
                                    'return_qty'               => 0,
                                    'return_value'             => Null,
                                    'change_cat_id'            => $request->get('change_cat_id')[$m],
                                    'change_product_id'        => $request->get('change_product_id')[$m],
                                    'change_qty'               => $request->get('change_qty')[$m],
                                    'change_value'             => $request->get('change_value')[$m]
                                    ]
                                    ); 
                                }
                            }
                        }
                    }
                }


                return Redirect::to('/return-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Add To Cart.');
            }
        
        } else {

        
            return Redirect::to('/return-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('error', 'Change value must be greater than or equal.');
        
        }       
    }
    
    
    private function return_change_validate($totalReturnValue,$totalChangeValue)
    {
        
        $differenceAllowMax = 300;
        $differenceData = 0;
        
        if($totalReturnValue>0 && $totalChangeValue>0)
        {
           $differenceData   = $totalReturnValue - $totalChangeValue;
            if(abs($differenceData) <= $differenceAllowMax)
            {
                return true;
            } 
            else 
            {
                return false;
            }
        } 
        else 
        {
            return false;
        } 

        /*$differenceAllowMax = 300;
        $differenceData = 0;
        
        if($totalReturnValue >0 && $totalChangeValue >0)
        {
            $differenceData = $totalReturnValue - $totalChangeValue;
            if(abs($totalReturnValue) <= $totalChangeValue)
            {
                return true;
            }
            else 
            {
                return false;
            }

            
        } 
        else 
        {
            return false;
        } */      
    }


    public function return_bucket($pointid,$routeid,$retailderid)
    {
        $selectedMenu   = 'Return';           // Required Variable
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

        /*              
        $resultCartPro  = DB::table('tbl_return_details')
                        ->select('tbl_return_details.return_cat_id','tbl_return_details.return_order_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','tbl_return.return_order_id','tbl_return.fo_id',
                        'tbl_return.return_order_type','tbl_return.return_order_no','tbl_return.retailer_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_return_details.return_cat_id')
                        ->join('tbl_return', 'tbl_return.return_order_id', '=', 'tbl_return_details.return_order_id')

                        ->where('tbl_return.return_order_type','Ordered')                        
                        ->where('tbl_return.fo_id',Auth::user()->id)                        
                        ->where('tbl_return.retailer_id',$retailderid)
                        ->groupBy('tbl_return_details.return_cat_id')                        
                        ->get();
        */

        $resultCartPro  = DB::select(" select `tbl_return_details`.*, `c1`.`id` as `ret_catid`, `c1`.`name` as `ret_catname`,`c2`.`id` as `chan_catid`, `c2`.`name` as `chan_catname`,
                            `p1`.`id`, `p1`.`name` as `ret_cproname`, `p2`.`id`, `p2`.`name` as `chng_proname`, `tbl_return`.`return_order_id`, `tbl_return`.`fo_id`, 
                            `tbl_return`.`return_order_type`, `tbl_return`.`retailer_id` 
                            from `tbl_return_details` 
                            inner join tbl_product_category as c1 on `c1`.`id` = `tbl_return_details`.`return_cat_id`
                            inner join tbl_product_category as c2 on `c2`.`id` = `tbl_return_details`.`change_cat_id`

                            inner join `tbl_return` on `tbl_return`.`return_order_id` = `tbl_return_details`.`return_order_id` 
                            left join tbl_product as p1 on `p1`.`id` = `tbl_return_details`.`return_product_id` 
                            left join tbl_product as p2 on `p2`.`id` = `tbl_return_details`.`change_product_id` 
                            where `tbl_return`.`return_order_type` = 'Ordered' 
                            and `tbl_return`.`fo_id` = '".Auth::user()->id."' and `tbl_return`.`retailer_id` = '".$retailderid."'");
        
        $resultInvoice  = DB::table('tbl_return')
                        ->where('return_order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();


        return view('sales/return/returnBucket', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice'));
    }


    public function return_items_edit(Request $request)
    {

        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');

        $resultPro  = DB::table('tbl_return_details')
                        
						->select('tbl_return_details.*',
						'tbl_return_details.p_unit_price','p1.name as ret_proName', 'p2.name as chng_proName')
                        
						->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details.return_product_id')
						->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details.change_product_id')
                        
						->where('tbl_return_details.return_order_det_id', $request->get('id'))
                        ->first();
						

        return view('sales.return.editReturnItems', compact('resultPro','pointid','retailderid','routeid','catid'));
    }

    
	public function return_items_edit_submit(Request $request)
    {
            
			DB::table('tbl_return_details')->where('return_order_det_id',$request->get('id'))->update(
                [
                    
                    'return_qty'       => $request->get('items_return'),
                    'change_qty'       => $request->get('items_change')
                ]
            );

            $totalQty = DB::table('tbl_return_details')
                                    ->select('return_order_id', DB::raw('SUM(return_qty) AS returnQty'), 
									  DB::raw('SUM(change_qty) AS changeQty'))
                                    ->where('return_order_id', $request->get('order_id'))
                                    ->groupBy('return_order_id')
                                    ->first();

            DB::table('tbl_return')->where('return_order_id', $totalQty->return_order_id)
                ->where('fo_id', Auth::user()->id)
                ->where('return_order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_return_qty'      => $totalQty->returnQty,
                    'total_change_qty'      => $totalQty->changeQty,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            ); 
       
            return Redirect::back()->with('success', 'Successfully Updated Return Order Product.');
    }



    public function return_items_delete(Request $request)
    {
        $orderID     = $request->get('orderID');

        $id = $request->get('id');

        //dd($orderID);

        $itemsDelete = DB::table('tbl_return_details')->where('return_order_det_id',$id)->delete();


        $totalQty = DB::table('tbl_return_details')
                    ->select('return_order_id', DB::raw('SUM(return_qty) AS returnQty'))
                    ->where('return_order_id', $orderID)
                    ->groupBy('return_order_id')
                    ->first();
        //dd($totalQty);
                    
         DB::table('tbl_return')->where('return_order_id', $orderID)
                ->where('fo_id', Auth::user()->id)
                ->where('return_order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_return_qtyqty'   => $totalQty->returnQty,
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s')
                ]
            );

        
            return Redirect::back()->with('success', 'Successfully Deleted Return Product.');
             
    }



    public function ssg_confirm_return(Request $request)
    {

        $distributorID = $request->get('distributorID');
        $retailderid   = $request->get('retailderid');
        $routeid       = $request->get('routeid');
        $orderid       = $request->get('orderid');
        $pointid       = $request->get('pointid');

        //dd($request->all());
        DB::table('tbl_return')->where('return_order_id', $orderid)->where('fo_id', Auth::user()->id)->update(
            [
                'total_change_value'=> $request->get('changeValue'),
                'total_change_qty'  => $request->get('changeQty')
            ]
        );

        DB::table('tbl_visit_return')->insert(
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

        DB::table('tbl_return')->where('return_order_id', $orderid)->where('fo_id', Auth::user()->id)->update(
            [
                'return_order_type'             => 'Confirmed'
            ]
        );

        return Redirect::to('/returnproduct')->with('success', 'Successfully Confirmed Return.');
    }





  //   public function ssg_delete_return($retailderid,$routeid,$orderid)
  //   {
  //       //dd($retailderid,$routeid,$orderid);
		// $orderid        = $orderid;
  //       $retailderid    = $retailderid;
  //       $routeid        = $routeid;

  //       DB::table('tbl_return_details')->where('return_order_id', $orderid)->delete();

  //       DB::table('tbl_visit_return')->where('order_no', $orderid)->delete();

  //       DB::table('tbl_return')->where('return_order_id', $orderid)->delete();
       
  //       return Redirect::to('/return-process/'.$retailderid.'/'.$routeid)->with('success', 'Delete has been successfully.');
  //   }

    public function ssg_delete_return($retailderid,$routeid,$orderid)
    {
        //dd($retailderid,$routeid,$orderid);
        $orderid        = $orderid;
        $retailderid    = $retailderid;
        $routeid        = $routeid;

        DB::table('tbl_return_details')->where('return_order_id', $orderid)->delete();

        DB::table('tbl_visit_return')->where('order_no', $orderid)->delete();

        DB::table('tbl_return')->where('return_order_id', $orderid)->delete();
       
        return Redirect::to('/return-process/'.$retailderid.'/'.$routeid)->with('success', 'Delete has been successfully.');
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
