<?php

namespace App\Http\Controllers\RequisitionProcess;

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
    * Created by Md. Masud Rana
    * Date : 07/06/2018
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    
	public function returnproduct()
    {

        $selectedMenu   = 'Requisition';           // Required Variable
        $pageTitle      = 'Return Change';        // Page Slug Title

        $routeResult = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)  
                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)  
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

        $checkRoutes = DB::table('tbl_return_distributor')->select('route_id')
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

        return view('sales/requisitionProcess/return/returnManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','routeID'));
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

        return view('sales/requisitionProcess/return/retailers', compact('resultRetailer','routeID'));
    }

    
	
	public function return_process($retailderid,$routeid)
    {
        $selectedMenu   = 'Requisition';             // Required Variable
        $pageTitle      = 'New Order';              // Page Slug Title

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

        $resultCart     = DB::table('tbl_return_distributor')
                        ->where('return_order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();

        return view('sales/requisitionProcess/return/categoryWiseReturn', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart'));
    }

    
	
	public function return_products(Request $request)
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
						
		$pcategory=DB::table('tbl_product_category')->get();				

        return view('sales/requisitionProcess/return/returnProductsList', compact('resultProduct','categoryID','pcategory'));
    }
	
	
	public function get_product(Request $request)
    {
          $id=$request->input('id');
          $serial=$request->input('slID');

          $product=DB::table('tbl_product')
                     ->where('category_id',$id)
                     ->get();

        return view('sales/requisitionProcess/return/getProduct' , compact('product','serial'));
        
    }
	
	public function get_product_price(Request $request)
    {
          $id=$request->input('id');

          $product=DB::table('tbl_product')
                     ->where('id',$id)
                     ->get();

        return $product[0]->realtimeprice;
        
    }

    
	public function return_add_to_cart_products(Request $request)
    {
        
		$autoOrder = DB::table('tbl_return_distributor')->select('return_auto_order_no')->orderBy('return_order_id','DESC')->first();

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
		
	// for total change qnty
		$totalChangeQty   = 0;	
        $countChangeRows = count($request->get('change_qty'));        
		for($m=0;$m<$countChangeRows;$m++)
        {
            $totalChangeQty   = $totalChangeQty + $request->get('change_qty')[$m];
        } 
		
	// for total change value
		$totalChangeValue   = 0;	
        $countChangeValueRows = count($request->get('change_value'));        
		for($m=0;$m<$countChangeValueRows;$m++)
        {
            $totalChangeValue   = $totalChangeValue + $request->get('change_value')[$m];
        } 	

        $resultCart     = DB::table('tbl_return_distributor')
                        ->where('return_order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)                        
                        ->first();

       //dd($resultCart);

        if(sizeof($resultCart)== 0)
        {
            DB::table('tbl_return_distributor')->insert(
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

            $lastOrderId = DB::table('tbl_return_distributor')->latest('return_order_id')->first();

            for($m=0;$m<$countReturnRows;$m++)
            {
                if($request->get('return_qty')[$m]!='')
                {
                    
                    DB::table('tbl_return_details_distributor')->insert(
                        [
                            'return_order_id'          => $lastOrderId->return_order_id,
                            'return_cat_id'            => $request->get('category_id')[$m],
                            'return_product_id'        => $request->get('produuct_id')[$m],
                            'return_qty'       		   => $request->get('return_qty')[$m],
                            'return_value'       	   => $request->get('return_value')[$m],
							'change_cat_id'            => $request->get('change_cat_id')[$m],
                            'change_product_id'        => $request->get('change_product_id')[$m],
                            'change_qty'       	   	   => $request->get('change_qty')[$m],
                            'change_value'       	   => $request->get('change_value')[$m]
                        ]
                    ); 
                }
            }


            return Redirect::to('/fo/return-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Added Add To Cart.');
        }
        elseif(sizeof($resultCart) > 0)
        {
            $lastOrderId            = $resultCart->return_order_id;

            // for total QTY and VALUE calculation.
            $oldGrandQty            = $resultCart->total_return_qty;
            $oldGrandChangeQty      = $resultCart->total_change_qty;

            $newGrandQty            = $oldGrandQty + $totalReturnQty;
            $newGrandChangeQty      = $oldGrandChangeQty + $totalChangeQty;

            $totRetValue ='';
            $totChangeValue ='';
            for($m=0;$m<$countReturnRows;$m++)
            {
                if($request->get('return_qty')[$m]!='')
                {
                    $totRetValue          = $request->get('return_value')[$m] * $request->get('return_qty')[$m];                        
                    $totChangeValue       = $request->get('change_value')[$m] * $request->get('change_qty')[$m];
                }
            }

            $totRetValueFinal = $totRetValue +$resultCart->total_return_value;
            $totChangeValueFinal = $totChangeValue +$resultCart->total_change_value;

            // for total QTY and VALUE calculation end
                              

            DB::table('tbl_return_distributor')->where('return_order_id', $lastOrderId)
                ->where('fo_id', Auth::user()->id)
                ->where('return_order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_return_qty'      => $newGrandQty,
                    'total_change_qty'      => $newGrandChangeQty,
                    'total_return_value'    => $totRetValueFinal,
                    'total_change_value'    => $totChangeValueFinal,
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
                    $checkItemsExiting = DB::table('tbl_return_details_distributor')
                                    ->select('tbl_return_details_distributor.*','tbl_return_distributor.return_order_id','tbl_return_distributor.return_order_type')

                                    ->join('tbl_return_distributor', 'tbl_return_distributor.return_order_id', '=', 'tbl_return_details_distributor.return_order_id')

                                    ->where('tbl_return_distributor.return_order_type', 'Ordered')
                                    ->where('tbl_return_distributor.return_order_id', $lastOrderId)
                                    ->where('tbl_return_details_distributor.return_product_id',$request->get('produuct_id')[$m])
                                    ->first();

                    if(sizeof($checkItemsExiting)>0)
                    {
                        $totRetQty 			= $checkItemsExiting->return_qty 	+ $request->get('return_qty')[$m];
                        $totRetValue 		= $checkItemsExiting->return_value 	+ $request->get('return_value')[$m];
                        $totChangeQty 		= $checkItemsExiting->change_qty 	+ $request->get('change_qty')[$m];
                        $totChangeValue 	= $checkItemsExiting->change_value 	+ $request->get('change_value')[$m];                       

                        DB::table('tbl_return_details_distributor')->where('return_product_id',$request->get('produuct_id')[$m])->update(
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
                        DB::table('tbl_return_details_distributor')->insert(
                            [
                                'return_order_id'          => $lastOrderId,
                                'return_cat_id'            => $request->get('category_id')[$m],
                                'return_product_id'        => $request->get('produuct_id')[$m],
                                'return_qty'       		   => $request->get('return_qty')[$m],
                                'return_value'       	   => $request->get('return_value')[$m],
								'change_cat_id'            => $request->get('change_cat_id')[$m],
								'change_product_id'        => $request->get('change_product_id')[$m],
                                'change_qty'       		   => $request->get('change_qty')[$m],
                                'change_value'       	   => $request->get('change_value')[$m]
                            ]
                        );
                    }
                }
            }

            return Redirect::to('/fo/return-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Updated Add To Cart.');
        } 
    }




    public function return_bucket($pointid,$routeid,$retailderid)
    {
        $selectedMenu   = 'Requisition';                    // Required Variable
        $pageTitle      = 'Return & Change Bucket';        // Page Slug Title

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

		$resultCartPro  = DB::select(" select `tbl_return_details_distributor`.*, `c1`.`id` as `ret_catid`, `c1`.`name` as `ret_catname`, `c2`.`id` as `chng_catid`, `c2`.`name` as `chng_catname`,
							`p1`.`id`, `p1`.`name` as `ret_cproname`, `p2`.`id`, `p2`.`name` as `chng_proname`, `tbl_return_distributor`.`return_order_id`, `tbl_return_distributor`.`fo_id`, 
							`tbl_return_distributor`.`return_order_type`, `tbl_return_distributor`.`retailer_id`
							from `tbl_return_details_distributor` inner join tbl_product_category as c1 on `c1`.`id` = `tbl_return_details_distributor`.`return_cat_id`
							inner join tbl_product_category as c2 on `c2`.`id` = `tbl_return_details_distributor`.`change_cat_id`
							inner join `tbl_return_distributor` on `tbl_return_distributor`.`return_order_id` = `tbl_return_details_distributor`.`return_order_id`
							inner join tbl_product as p1 on `p1`.`id` = `tbl_return_details_distributor`.`return_product_id` 
							inner join tbl_product as p2 on `p2`.`id` = `tbl_return_details_distributor`.`change_product_id` 
							where `tbl_return_distributor`.`return_order_type` = 'Ordered' 
							and `tbl_return_distributor`.`fo_id` = '".Auth::user()->id."' and `tbl_return_distributor`.`retailer_id` = '".$retailderid."'");
		
        $resultInvoice  = DB::table('tbl_return_distributor')->select('return_order_id','return_order_type','fo_id','retailer_id','return_order_no','return_auto_order_no')
                        ->where('return_order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();


        return view('sales/requisitionProcess/return/returnBucket', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice'));
    }


    public function return_items_edit(Request $request)
    {

        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');

        $resultPro  = DB::table('tbl_return_details_distributor')
                        
						->select('tbl_return_details_distributor.*',
						'tbl_return_details_distributor.p_unit_price','p1.name as ret_proName', 'p2.name as chng_proName')
                        
						->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details_distributor.return_product_id')
						->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details_distributor.change_product_id')
                        
						->where('tbl_return_details_distributor.return_order_det_id', $request->get('id'))
                        ->first();
						

        return view('sales/requisitionProcess/return/editReturnItems', compact('resultPro','pointid','retailderid','routeid','catid'));
    }

    
	public function return_items_edit_submit(Request $request)
    {

            
		DB::table('tbl_return_details_distributor')->where('return_order_det_id',$request->get('id'))->update(
            [
                
                'return_qty'       => $request->get('items_return'),
                'change_qty'       => $request->get('items_change')
            ]
        );

        $totalQty = DB::table('tbl_return_details_distributor')
                                ->select('return_order_id', DB::raw('SUM(return_qty) AS returnQty'), 
								  DB::raw('SUM(change_qty) AS changeQty'))
                                ->where('return_order_id', $request->get('order_id'))
                                ->groupBy('return_order_id')
                                ->first();

        DB::table('tbl_return_distributor')->where('return_order_id', $totalQty->return_order_id)
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

        $itemsDelete = DB::table('tbl_return_details_distributor')->where('return_order_det_id',$id)->delete();

        $totalQty = DB::table('tbl_return_details_distributor')
                    ->select('return_order_id', DB::raw('SUM(return_qty) AS returnQty'))
                    ->where('return_order_id', $orderID)
                    ->groupBy('return_order_id')
                    ->first();
                    
         DB::table('tbl_return_distributor')->where('return_order_id', $orderID)
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



    public function ssg_confirm_return(Request $request, $orderpid,$orderid,$retailderid,$routeid,$pointid,$distributorID)
    {

        DB::table('tbl_visit_return_distributor')->insert(
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

        DB::table('tbl_return_distributor')->where('return_order_id', $orderpid)->where('fo_id', Auth::user()->id)->update(
            [
                'return_order_type'             => 'Confirmed'
            ]
        );

        return Redirect::to('/fo/returnproduct')->with('success', 'Successfully Confirmed Return.');
    }


    public function ssg_delete_return(Request $request)
    {
        
		$orderid        = $request->get('orderID');
        $retailderid    = $request->get('retailderid');
        $routeid        = $request->get('routeid');

        $lastOrderId = DB::table('tbl_return_distributor')->select('return_order_type','fo_id','return_order_id','return_order_no')
                    ->where('return_order_id',$orderid)->first();

        $orderID     = $orderid;
        
        DB::table('tbl_visit_return_distributor')->where('order_no', $lastOrderId->return_order_no)->delete();

        DB::table('tbl_return_distributor')->where('return_order_id', $orderID)->delete();
       
        return 0;      
    }
}
