<?php

namespace App\Http\Controllers\RequisitionProcess;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class ChangeController extends Controller
{
	
	/**
	*
	* Created by Md. Masud Rana
	* Date : 10/06/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_return_order()
    {
        $selectedMenu   = 'Return Manage';               // Required Variable
        $pageTitle      = 'Return & Change';            // Page Slug Title

        $resultFO       = DB::table('tbl_return_distributor')
                        ->select('tbl_return_distributor.global_company_id','tbl_return_distributor.return_order_type','tbl_return_distributor.return_order_id','tbl_return_distributor.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_distributor.fo_id')                       
                        ->where('tbl_return_distributor.return_order_type', 'Confirmed')
                        ->where('tbl_return_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_distributor.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_return_distributor.fo_id')
                        ->orderBy('tbl_return_distributor.return_order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_return_distributor')
                        ->select('tbl_return_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_distributor.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_distributor.retailer_id')                  
                        ->where('tbl_return_distributor.return_order_type', 'Confirmed')
                        ->where('tbl_return_distributor.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_distributor.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_distributor.return_order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_return_distributor.return_order_id','DESC')                    
                        ->get();

        return view('sales/requisitionProcess/change/returnOrder', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    
	public function ssg_return_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_return_distributor')
                        ->select('tbl_return_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_distributor.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_distributor.retailer_id')                  
                        ->where('tbl_return_distributor.return_order_type', 'Confirmed')
                        ->where('tbl_return_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_distributor.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_distributor.return_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_distributor.return_order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_return_distributor')
                        ->select('tbl_return_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_distributor.retailer_id')
                        ->where('tbl_return_distributor.return_order_type', 'Confirmed')
                        ->where('tbl_return_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_distributor.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_distributor.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_distributor.return_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_distributor.return_order_id','DESC')                    
                        ->get();
        }
        
        return view('sales/requisitionProcess/change/returnOrdersList', compact('resultOrderList'));
    }
	
	
	public function return_change_products($return_order_id)
    {
		
		$selectedMenu   = 'Return Manage';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title
		
		if($return_order_id)
		{
			$resRetChangData = DB::table('tbl_return_distributor')
								->select('tbl_return_distributor.point_id', 'tbl_return_distributor.distributor_id', 'tbl_return_distributor.fo_id', 'tbl_return_distributor.route_id', 'tbl_return_distributor.retailer_id', 
										'tbl_return_distributor.total_return_value', 'tbl_return_distributor.total_change_value', 
								'tbl_retailer.name as retName', 'p1.name as retProdName', 'tbl_return_details_distributor.*')
								->join('tbl_return_details_distributor', 'tbl_return_details_distributor.return_order_id', '=', 'tbl_return_distributor.return_order_id')
								->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_distributor.retailer_id')
								->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details_distributor.return_product_id')
								//->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details.change_product_id')
								->where('tbl_return_distributor.return_order_id', $return_order_id)
								->get();

			if(sizeof($resRetChangData) >0 )
			{
				//echo '<pre/>'; print_r($resRetChangData); exit;
				
				$pointID = $resRetChangData[0]->point_id;
				$distributorID = $resRetChangData[0]->distributor_id;
				$routeid = $resRetChangData[0]->route_id;
				$retailderid = $resRetChangData[0]->retailer_id;
				$foID = $resRetChangData[0]->fo_id;
				
			}
			else
			{
				$pointID = '';
				$distributorID = '';
				$routeid = '';
				$retailderid = '';
				$foID = '';
			}

			$resultCategory = DB::table('tbl_product_category')
							->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
							->where('status', '0')
							->where('gid', Auth::user()->business_type_id)
							->where('global_company_id', Auth::user()->global_company_id)
							->get();
							
			//echo '<pre/>'; print_r($resultCategory); exit;				
							
		    return view('sales/requisitionProcess/change/categoryWiseReturnChange',compact('selectedMenu','pageTitle', 'resRetChangData','resultCategory','return_order_id','pointID','distributorID','routeid','retailderid','foID'));					
		
		}	else {

		    echo 'Return Order Id Failed';
		}		
				
		
        
    }


    public function ssg_confirm_return_change(Request $request)
    {

        $lastOrderId    = $request->get('return_order_id');

        $countRows = count($request->get('change_qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('change_qty')[$m]!='')
            {
                $mTotalPrice += $request->get('change_value')[$m];
                $mTotalQty += $request->get('change_qty')[$m];
            }
        }            

        DB::table('tbl_return_distributor')->where('return_order_id', $lastOrderId)
            ->where('fo_id', $request->get('fo_id'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'return_order_type'      => 'Delivered',
                'total_approved_qty'     => $mTotalQty,
                'total_approved_value'   => $mTotalPrice,
                'update_by'            	 => Auth::user()->id,
                'update_date'            => date('Y-m-d H:i:s')
            ]
        );

        
		for($m=0;$m<$countRows;$m++)
        {
            if($request->get('change_product_id')[$m] && $request->get('change_qty')[$m]!='')
            {
				/*
                $checkItemsExiting = DB::table('tbl_return_details')
                                ->where('return_order_id', $lastOrderId)
                                ->where('change_product_id',$request->get('change_product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                { */
			
                    $totalPrice = $request->get('change_value')[$m];                        

                    DB::table('tbl_return_details')
					->where('return_order_id', $lastOrderId)
					->where('change_product_id',$request->get('change_product_id')[$m])
					->update(
                        [
                            'approved_cat_id'    	 => $request->get('change_cat_id')[$m],
                            'approved_product_id'    => $request->get('change_product_id')[$m],
                            'approved_qty'       	 => $request->get('change_qty')[$m],
                            'approved_value'     	 => $totalPrice
                        ]
                    );
					
               //} 


            ///////////////////////////////// Md. Masud Rana Change Stock (Out) Inventory /////////////////////////////////////

                $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

                $inOut='2'; // stock-out operation

                $pointID = '';
                if(sizeof($division_point)>0)
                {
                    $pointID = $division_point->point_id;
                }

                $totalChangePrice = $request->get('change_value')[$m];
                //$totalPrice = $request->get('change_value')[$m];

                DB::table('distributor_inventory')->insert(
                    [
                        'point_id'           => $pointID,
                        'distributor_in_charge'    => Auth::user()->id,
                        'cat_id'             => $request->get('change_cat_id')[$m],
                        'product_id'         => $request->get('change_product_id')[$m],
                        'product_qty'        => $request->get('change_qty')[$m],
                        'product_value'      => $totalChangePrice,
                        'inventory_date'     => date('Y-m-d'),
                        'inventory_type'     => $inOut,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'created_by'         => Auth::user()->id
                    ]
                ); 
            

                $stockOutProduct = DB::table('distributor_stock')
                                ->select('distributor_id','point_id','cat_id','product_id','stock_qty')
                                ->where('point_id', $pointID)
                                ->where('cat_id', $request->get('change_cat_id')[$m])
                                ->where('product_id', $request->get('change_product_id')[$m])
                                ->first();

				
				$totalOutQty = $request->get('change_qty')[$m];
                if(sizeof($stockOutProduct)>0)
                {
                   
                        
                    $totalOutQty = $stockOutProduct->stock_qty - $request->get('change_qty')[$m];
                    //$totalQty = $stockProduct->stock_qty - $request->get('change_qty')[$m];
                    
                    
                    DB::table('distributor_stock')
                    ->where('point_id',$pointID)
                    ->where('cat_id',$request->get('change_cat_id')[$m])
                    //->where('cat_id',$checkItemsExiting->cat_id)
                    ->where('product_id',$request->get('change_product_id')[$m])
                    ->update(
                    [
                        'point_id'           => $pointID,
                        'cat_id'             => $request->get('change_cat_id')[$m],
                        'product_id'         => $request->get('change_product_id')[$m],
                        'stock_qty'          => $totalOutQty,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'updated_by'         => Auth::user()->id                   
                        
                    ]
                    );

                } else {  //save negative stock 
					
					DB::table('distributor_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('change_cat_id')[$m],
							'product_id'         => $request->get('change_product_id')[$m],
							'stock_qty'          => '-' . $totalOutQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id      
						]
					); 
				
				}

            // Depot Change Stock (Out) end of Md. Masud Rana
			
			
			////////////////////////// Md. Masud Rana Change Stock (In) Inventory ///////////////////////////////

                $inOut='1'; // stock-in operation

                $pointID = '';
                if(sizeof($division_point)>0)
                {
                    $pointID = $division_point->point_id;
                }

                $totalReturnPrice = $request->get('return_value')[$m];
                //$totalPrice = $request->get('return_value')[$m];

                DB::table('distributor_inventory')->insert(
                    [
                        'point_id'           => $pointID,
                        'distributor_in_charge'    => Auth::user()->id,
                        'cat_id'             => $request->get('return_cat_id')[$m],
                        'product_id'         => $request->get('return_product_id')[$m],
                        'product_qty'        => $request->get('return_qty')[$m],
                        'product_value'      => $totalReturnPrice,
                        'inventory_date'     => date('y-m-d'),
                        'inventory_type'     => $inOut,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'created_by'         => Auth::user()->id
                    ]
                ); 
            

                $stockInProduct = DB::table('distributor_stock')
                                ->select('distributor_id','point_id','cat_id','product_id','stock_qty')
                                ->where('point_id', $pointID)
                                ->where('cat_id', $request->get('return_cat_id')[$m])
                                ->where('product_id', $request->get('return_product_id')[$m])
                                ->first();

				
				$totalInQty = $request->get('return_qty')[$m];
                
				if(sizeof($stockInProduct)>0)
                {
                   
                    $totalInQty = $stockInProduct->stock_qty + $request->get('return_qty')[$m];
                    
                    DB::table('distributor_stock')
                    ->where('point_id',$pointID)
                    ->where('cat_id',$request->get('return_cat_id')[$m])
                    //->where('cat_id',$checkItemsExiting->cat_id)
                    ->where('product_id',$request->get('return_product_id')[$m])
                    ->update(
                    [
                        'point_id'           => $pointID,
                        'cat_id'             => $request->get('return_cat_id')[$m],
                        'product_id'         => $request->get('return_product_id')[$m],
                        'stock_qty'          => $totalInQty,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'updated_by'         => Auth::user()->id                   
                        
                    ]
                    );

                } else {  //save positive stock 
					
					DB::table('distributor_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('return_cat_id')[$m],
							'product_id'         => $request->get('return_product_id')[$m],
							'stock_qty'          => $totalInQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id      
						]
					); 
				
				}

            // Depot Stock (In) end of Md. Masud Rana
			


            } // main if closed


        } // main for closed
		
		
		return Redirect::to('/fo/returnorder')->with('success', 'Successfully Return & Change Done.'); 

       
    }





    public function ssg_delete_return_change(Request $request)
    {
        
		$orderid        = $request->get('orderID');
        $retailderid    = $request->get('retailderid');
        $routeid        = $request->get('routeid');

        //dd($request->all());

        $lastOrderId = DB::table('tbl_return')->select('return_order_type','fo_id','return_order_id','return_order_no')
                    ->where('return_order_id',$orderid)->first();

        $orderID     = $orderid;

		/*
        $items      = DB::table('tbl_order_wastage')
                    ->where('order_id',$orderID)                   
                    ->get();

        foreach ($items as $value) 
        {
            DB::table('tbl_order_wastage')->where('order_id', $value->order_id)->delete();
        }
		*/
        
        DB::table('tbl_visit_return')->where('order_no', $lastOrderId->return_order_no)->delete();

        DB::table('tbl_return')->where('return_order_id', $orderID)->delete();
       
        return 0;      
    }
	
	

/*
 	 
    public function ssg_return_order_edit($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Return Order';                    // Required Variable
        $pageTitle      = 'Return Order Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_return_details')
                        
						->select('tbl_return_details.return_cat_id','tbl_return_details.return_order_id','tbl_product_category.id AS catid',
						'tbl_product_category.name AS catname','tbl_return.return_order_id','tbl_return.fo_id','tbl_return.return_order_type',
						'tbl_return.return_order_no','tbl_return.retailer_id','tbl_return.global_company_id')
                        
						->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_return_details.return_cat_id')
                        ->join('tbl_return', 'tbl_return.return_order_id', '=', 'tbl_return_details.return_order_id')
                        
						->where('tbl_return.return_order_type','Confirmed')
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_return.fo_id',$foMainId)                        
                        ->where('tbl_return_details.return_order_id',$orderMainId)
                        
						->groupBy('tbl_return_details.return_cat_id')                        
                        
						->get();

        $resultInvoice  = DB::table('tbl_return')->select('tbl_return.global_company_id','tbl_return.return_order_id',
						'tbl_return.return_order_type','tbl_return.fo_id','tbl_return.retailer_id','return_order_no','return_order_date',
						'tbl_retailer.name','tbl_retailer.mobile')
						->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return.retailer_id')
                        ->where('tbl_return.return_order_type','Confirmed')
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_return.fo_id',$foMainId)                        
                        ->where('tbl_return.return_order_id',$orderMainId)
                        ->first();


        return view('sales.change.bucketEdit', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId'));
    }


    public function ssg_return_order_edit_submit(Request $request)
    {
        $lastOrderId    = $request->get('orderid');

        $countRows = count($request->get('qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $mTotalPrice += $request->get('qty')[$m] * $request->get('price')[$m];
                $mTotalQty += $request->get('qty')[$m];
            }
        }            

       DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', $request->get('foMainId'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_type'             => 'Delivered',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'update_date'            => date('Y-m-d H:i:s')
            ]
        );

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('tbl_order_details')
                                ->where('order_id', $lastOrderId)
                                ->where('product_id',$request->get('product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('tbl_order_details')->where('product_id',$request->get('product_id')[$m])->update(
                        [
                            'replace_delivered_qty'   => $request->get('replaceDelivery')[$m],
                            'delivered_qty'           => $request->get('qty')[$m],
                            'delivered_value'         => $totalPrice
                        ]
                    );
                } 


                /// Sharif Depot Stock Inventory

                $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

                $inOut='2';

                $pointID = '';
                if(sizeof($division_point)>0)
                {
                    $pointID = $division_point->point_id;
                }

                $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                DB::table('distributor_inventory')->insert(
                    [
                        'point_id'           => $pointID,
                        'depot_in_charge'    => Auth::user()->id,
                        'cat_id'             => $checkItemsExiting->cat_id,
                        'product_id'         => $request->get('product_id')[$m],
                        'product_qty'        => $request->get('qty')[$m],
                        'product_value'      => $totalPrice,
                        'inventory_date'     => date('y-m-d'),
                        'inventory_type'     => $inOut,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'created_by'         => Auth::user()->id
                    ]
                ); 
            

                $stockProduct = DB::table('distributor_stock')
                                ->select('distributor_id','point_id','cat_id','product_id','stock_qty')
                                ->where('point_id', $pointID)
                                ->where('cat_id', $checkItemsExiting->cat_id)
                                ->where('product_id', $request->get('product_id')[$m])
                                ->first();


                if(sizeof($stockProduct)>0)
                {
                   
                        
                    $totalQty = $stockProduct->stock_qty - $request->get('qty')[$m];
                    
                    
                    DB::table('distributor_stock')
                    ->where('point_id',$pointID)
                    //->where('cat_id',$request->get('category_id')[$m])
                    ->where('cat_id',$checkItemsExiting->cat_id)
                    ->where('product_id',$request->get('product_id')[$m])
                    ->update(
                    [
                        'point_id'           => $pointID,
                        'cat_id'             => $checkItemsExiting->cat_id,
                        'product_id'         => $request->get('product_id')[$m],
                        'stock_qty'          => $totalQty,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'updated_by'         => Auth::user()->id                   
                        
                    ]
                    );

                } else {  //save negative stock 
					
					DB::table('distributor_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $checkItemsExiting->cat_id,
							'product_id'         => $request->get('product_id')[$m],
							'stock_qty'          => '-' . $totalQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id      
						]
					); 
				
				}

            // Depot stock end of Sharif


            }


        }


        return Redirect::to('/returnorder')->with('success', 'Successfully Change Confirm Done.'); 
    }
	
	*/


    public function ssg_return_invoice_order($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order';                      // Required Variable
        $pageTitle      = 'Invoice Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.order_type','Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type','Confirmed')
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

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        
        return view('sales.change.returnInvoiceDetails', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }
}
