<?php

namespace App\Http\Controllers\Sales;

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
	* Created by Zubair Mahmudul Huq
	* Date : 03/06/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_return_order()
    {
        $selectedMenu   = 'ReturnOnlyOrder';             // Required Variable
        $pageTitle      = 'ReturnOnlyOrder';            // Page Slug Title

        $resultFO       = DB::table('tbl_return_only')
                        ->select('tbl_return_only.global_company_id','tbl_return_only.return_only_order_type','tbl_return_only.return_only_order_id','tbl_return_only.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only.fo_id')                       
                        ->where('tbl_return_only.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_return_only.fo_id')
                        ->orderBy('tbl_return_only.return_only_order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_return_only')
                        ->select('tbl_return_only.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only.retailer_id')                  
                        ->where('tbl_return_only.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_only.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only.return_only_order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_return_only.return_only_order_id','DESC')                    
                        ->get();

        return view('sales.changeOnly.returnOrder', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    
	public function ssg_return_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_return_only')
                        ->select('tbl_return_only.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only.retailer_id')                  
                        ->where('tbl_return_only.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only.return_only_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_only.return_only_order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_return_only')
                        ->select('tbl_return_only.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return_only.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only.retailer_id')
                        ->where('tbl_return_only.return_only_order_type', 'Confirmed')
                        ->where('tbl_return_only.distributor_id', Auth::user()->id)
                        ->where('tbl_return_only.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return_only.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return_only.return_only_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return_only.return_only_order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.changeOnly.returnOrdersList', compact('resultOrderList'));
    }
	
	
	public function return_change_products($return_order_id)
    {
		
		$selectedMenu   = 'ReturnOnlyOrder';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title
		
		if($return_order_id)
		{
			$resRetChangData = DB::table('tbl_return_only')
								->select('tbl_return_only.point_id', 'tbl_return_only.distributor_id', 'tbl_return_only.fo_id', 'tbl_return_only.route_id', 'tbl_return_only.retailer_id', 
										'tbl_return_only.total_return_only_value', 'tbl_retailer.name as retName', 'p1.name as retProdName', 'tbl_return_details_only.*')
								->join('tbl_return_details_only', 'tbl_return_details_only.return_only_order_id', '=', 'tbl_return_only.return_only_order_id')
								->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return_only.retailer_id')
								->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details_only.return_only_product_id')
								//->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details.change_product_id')
								->where('tbl_return_only.return_only_order_id', $return_order_id)
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
							
		    return view('sales.changeOnly.categoryWiseReturnChange',compact('selectedMenu','pageTitle', 'resRetChangData','resultCategory','return_order_id','pointID','distributorID','routeid','retailderid','foID'));					
		
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

        DB::table('tbl_return_only')->where('return_only_order_id', $lastOrderId)
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

                    DB::table('tbl_return_details_only')
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


            ///////////////////////////////// Zubair Change Stock (IN) Inventory /////////////////////////////////////

                $division_point=DB::table('tbl_user_business_scope')
                ->select('point_id','division_id')
                ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                ->first();

                $inOut='1'; // stock-in operation

                $pointID = '';
                if(sizeof($division_point)>0)
                {
                    $pointID = $division_point->point_id;
                }

                $totalChangePrice = $request->get('approved_only_value')[$m];
                //$totalPrice = $request->get('change_value')[$m];

                DB::table('depot_inventory')->insert(
                    [
                        'point_id'           => $pointID,
                        'depot_in_charge'    => Auth::user()->id,
                        'cat_id'             => $request->get('approved_only_cat_id')[$m],
                        'product_id'         => $request->get('approved_only_product_id')[$m],
                        'product_qty'        => $request->get('approved_only_qty')[$m],
                        'product_value'      => $totalChangePrice,
                        'inventory_date'     => date('Y-m-d'),
                        'inventory_type'     => $inOut,
                        'transaction_type'   => 'return',
                        'global_company_id'  => Auth::user()->global_company_id,
                        'created_by'         => Auth::user()->id
                    ]
                ); 
            

                $stockOutProduct = DB::table('depot_stock')
                                ->select('depot_id','point_id','cat_id','product_id','stock_qty')
                                ->where('point_id', $pointID)
                                ->where('cat_id', $request->get('approved_only_cat_id')[$m])
                                ->where('product_id', $request->get('approved_only_product_id')[$m])
                                ->first();

				
				$totalOutQty = $request->get('approved_only_qty')[$m];
                if(sizeof($stockOutProduct)>0)
                {
                   
				    $totalOutQty = $stockOutProduct->stock_qty + $request->get('approved_only_qty')[$m];
                    
                    DB::table('depot_stock')
                    ->where('point_id',$pointID)
                    ->where('cat_id',$request->get('approved_only_cat_id')[$m])
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

                } else {  //save positive stock 
					
					DB::table('depot_stock')->insert(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('approved_only_cat_id')[$m],
							'product_id'         => $request->get('approved_only_product_id')[$m],
							'stock_qty'          => $totalOutQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id      
						]
					); 
				
				}

				// Depot Change Stock (IN) end of Zubair
	
            } // main if closed


        } // main for closed
		
		
		
		
		$retailer_info = array();
		$retailer_info['retailer_id'] = $request->get('retailer_id');
		$retailer_info['collection_amount'] = $mTotalPrice;
		
		$this->reatiler_credit_ledger($retailer_info);
		
		
		return Redirect::to('/return-only-order')->with('success', 'Successfully Return Done.'); 

       
    }
	
	
	private function reatiler_credit_ledger($retailer_info = array())
	{
		if(is_array($retailer_info))
		{
			
			$credit_ledger_Data = array();
			$credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
			$credit_ledger_Data['collection_id'] = 0;
			$credit_ledger_Data['trans_type'] = 'return_credited';
			$credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s');
			
			/////////////////////////////// Retailer Credit Balance ////////////////////////////////
			
			$retailerLedger = DB::select("SELECT * FROM retailer_credit_ledger WHERE retailer_id = '".$retailer_info['retailer_id']."'
							ORDER BY 1 DESC LIMIT 1");
							
			##opening balance
			if(sizeof($retailerLedger)>0)
			{
				$retOpeningBalance = $retailerLedger[0]->retailer_balance;
			} else {
				$retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$retailer_info['retailer_id']."'");
				$retOpeningBalance = $retailerData[0]->opening_balance;
			}
			
			$credit_ledger_Data['retailer_opening_balance'] = $retOpeningBalance;

			## invoice sales
			
			$retInVoiceSales = 0;
			/*
			if($retailer_info['invoice_no'] && $retailer_info['retailer_id'])
			{
				$retailerInvoice = DB::select("SELECT * FROM tbl_order WHERE retailer_id = '".$retailer_info['retailer_id']."'
				AND order_no = '".$retailer_info['invoice_no']."'");
				
				$retInVoiceSales = $retailerInvoice[0]->grand_total_value;
			}*/

			$credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;			
			
			##totalCollection
			$retCollect = $retailer_info['collection_amount'];
			$credit_ledger_Data['retailer_collection'] = $retCollect;
			
			
			##retailerBalance
			$remBalance = ($retOpeningBalance + $retInVoiceSales) - $retCollect;
			
			$credit_ledger_Data['retailer_balance'] = $remBalance;
			
			$credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
			$credit_ledger_Data['entry_by'] = Auth::user()->id;
			
			
			DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
			
			
		}	
	}

    
}
