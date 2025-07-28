<?php

namespace App\Http\Controllers\RequisitionProcess;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class ChangeOnlyController extends Controller
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
        $selectedMenu   = 'Return Manage';             // Required Variable
        $pageTitle      = 'Return Order';            // Page Slug Title

        $resultFO       = DB::table('tbl_return_only_distributor')
                        ->select('tbl_return_only_distributor.global_company_id','tbl_return_only_distributor.return_only_order_type','tbl_return_only_distributor.return_only_order_id','tbl_return_only_distributor.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only_distributor.fo_id')                       
                        ->where('tbl_return_only_distributor.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only_distributor.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_return_only_distributor.fo_id')
                        ->orderBy('tbl_return_only_distributor.return_only_order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_return_only_distributor')
                        ->select('tbl_return_only_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only_distributor.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only_distributor.retailer_id')                  
                        ->where('tbl_return_only_distributor.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only_distributor.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_only_distributor.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only_distributor.return_only_order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_return_only_distributor.return_only_order_id','DESC')                    
                        ->get();

        return view('sales/requisitionProcess/changeOnly/returnOrder', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    
	public function ssg_return_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_return_only_distributor')
                        ->select('tbl_return_only_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only_distributor.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only_distributor.retailer_id')                  
                        ->where('tbl_return_only_distributor.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only_distributor.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only_distributor.return_only_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_only_distributor.return_only_order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_return_only_distributor')
                        ->select('tbl_return_only_distributor.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only_distributor.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only_distributor.retailer_id')
                        ->where('tbl_return_only_distributor.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only_distributor.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only_distributor.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_only_distributor.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only_distributor.return_only_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_only_distributor.return_only_order_id','DESC')                    
                        ->get();
        }
        
        return view('sales/requisitionProcess/changeOnly/returnOrdersList', compact('resultOrderList'));
    }
	
	
	public function return_change_products($return_order_id)
    {
		
		$selectedMenu   = 'Return Manage';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title
		
		if($return_order_id)
		{
			$resRetChangData = DB::table('tbl_return_only_distributor')
								->select('tbl_return_only_distributor.point_id', 'tbl_return_only_distributor.distributor_id', 'tbl_return_only_distributor.fo_id', 'tbl_return_only_distributor.route_id', 'tbl_return_only_distributor.retailer_id', 
										'tbl_return_only_distributor.total_return_only_value', 'tbl_retailer.name as retName', 'p1.name as retProdName', 'tbl_return_details_only_distributor.*')

								->join('tbl_return_details_only_distributor', 'tbl_return_details_only_distributor.return_only_order_id', '=', 'tbl_return_only_distributor.return_only_order_id')
								->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only_distributor.retailer_id')
								->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details_only_distributor.return_only_product_id')
								//->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details.change_product_id')
								->where('tbl_return_only_distributor.return_only_order_id', $return_order_id)
								->get();

                                //dd($resRetChangData);

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
							
		    return view('sales/requisitionProcess/changeOnly/categoryWiseReturnChange',compact('selectedMenu','pageTitle', 'resRetChangData','resultCategory','return_order_id','pointID','distributorID','routeid','retailderid','foID'));					
		
		}	else {

		    echo 'Return Order Id Failed';
		}		
				
		
        
    }


    public function ssg_confirm_return_change(Request $request)
    {

        $lastOrderId    = $request->get('return_order_id');

        $countRows = count($request->get('approved_only_qty'));

        $mTotalPrice=0;
        $mTotalQty=0;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('approved_only_qty')[$m]!='')
            {
                $mTotalPrice += $request->get('approved_only_value')[$m];
                $mTotalQty += $request->get('approved_only_qty')[$m];
            }
        }            

        DB::table('tbl_return_only_distributor')->where('return_only_order_id', $lastOrderId)
            ->where('fo_id', $request->get('fo_id'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'return_only_order_type'      => 'Delivered',
                'total_approved_only_qty'     => $mTotalQty,
                'total_approved_only_value'   => $mTotalPrice,
                'update_by'            	 	  => Auth::user()->id,
                'update_date'            	  => date('Y-m-d H:i:s')
            ]
        );

        
		for($m=0;$m<$countRows;$m++)
        {
            if($request->get('approved_only_product_id')[$m] && $request->get('approved_only_value')[$m]!='')
            {
				/*
                $checkItemsExiting = DB::table('tbl_return_details')
                                ->where('return_order_id', $lastOrderId)
                                ->where('change_product_id',$request->get('change_product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                { */
			
                    $totalPrice = $request->get('approved_only_value')[$m];                        

                    DB::table('tbl_return_details_only_distributor')
					->where('return_only_order_id', $lastOrderId)
					->where('return_only_product_id',$request->get('return_product_id')[$m])
					->update(
                        [
                            'approved_only_cat_id'    	 => $request->get('approved_only_cat_id')[$m],
                            'approved_only_product_id'    => $request->get('approved_only_product_id')[$m],
                            'approved_only_qty'       	 => $request->get('approved_only_qty')[$m],
                            'approved_only_value'     	 => $totalPrice
                        ]
                    );
					
               //} 


            ///////////////////////////////// Md. Masud Rana Change Stock (Out) Inventory /////////////////////////////////////

                $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

                $inOut='2'; // stock-in operation

                $pointID = '';
                if(sizeof($division_point)>0)
                {
                    $pointID = $division_point->point_id;
                }

                $totalChangePrice = $request->get('approved_only_value')[$m];
                //$totalPrice = $request->get('change_value')[$m];

                DB::table('distributor_inventory')->insert(
                    [
                        'point_id'           => $pointID,
                        'distributor_in_charge'    => Auth::user()->id,
                        'cat_id'             => $request->get('approved_only_cat_id')[$m],
                        'product_id'         => $request->get('approved_only_product_id')[$m],
                        'product_qty'        => $request->get('approved_only_qty')[$m],
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
                                ->where('cat_id', $request->get('approved_only_cat_id')[$m])
                                ->where('product_id', $request->get('approved_only_product_id')[$m])
                                ->first();

				
				$totalOutQty = $request->get('approved_only_qty')[$m];
                if(sizeof($stockOutProduct)>0)
                {
                   
                        
                    $totalOutQty = $stockOutProduct->stock_qty - $request->get('approved_only_qty')[$m];
                    //$totalQty = $stockProduct->stock_qty - $request->get('change_qty')[$m];
                    
                    
                    DB::table('distributor_stock')
                    ->where('point_id',$pointID)
                    ->where('cat_id',$request->get('approved_only_cat_id')[$m])
                    //->where('cat_id',$checkItemsExiting->cat_id)
                    ->where('product_id',$request->get('approved_only_product_id')[$m])
                    ->update(
                    [
                        'point_id'           => $pointID,
                        'cat_id'             => $request->get('approved_only_cat_id')[$m],
                        'product_id'         => $request->get('approved_only_product_id')[$m],
                        'stock_qty'          => $totalOutQty,
                        'global_company_id'  => Auth::user()->global_company_id,
                        'updated_by'         => Auth::user()->id                   
                        
                    ]
                    );

                } else {  //save negative stock 
					
					DB::table('distributor_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('approved_only_cat_id')[$m],
							'product_id'         => $request->get('approved_only_product_id')[$m],
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
                        'distributor_in_charge'=> Auth::user()->id,
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

                } else {  //save negative stock 
					
					DB::table('distributor_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('return_cat_id')[$m],
							'product_id'         => $request->get('return_product_id')[$m],
							'stock_qty'          => '-' . $totalInQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id      
						]
					); 				
				}

            // Depot Stock (In) end of Md. Masud Ranad
            } // main if closed


        } // main for closed
		
		
		return Redirect::to('/fo/return-only-order')->with('success', 'Successfully Return Done.'); 

       
    }

    
}
