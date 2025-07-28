<?php

namespace App\Http\Controllers\Sales;

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
	* Created by Zubair Mahmudul Huq
	* Date : 30/05/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_return_order()
    {
        $selectedMenu   = 'ReturnOrder';             // Required Variable
        $pageTitle      = 'ReturnOrder';            // Page Slug Title

        $resultFO       = DB::table('tbl_return')
                        ->select('tbl_return.global_company_id','tbl_return.return_order_type','tbl_return.return_order_id','tbl_return.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return.fo_id')                       
                        ->where('tbl_return.return_order_type', 'Confirmed')
                        ->where('tbl_return.distributor_id', Auth::user()->id)
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_return.fo_id')
                        ->orderBy('tbl_return.return_order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_return')
                        ->select('tbl_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return.retailer_id')                  
                        ->where('tbl_return.return_order_type', 'Confirmed')
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return.return_order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_return.return_order_id','DESC')                    
                        ->get();

        return view('sales.change.returnOrder', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    
	public function ssg_return_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_return')
                        ->select('tbl_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return.retailer_id')                  
                        ->where('tbl_return.return_order_type', 'Confirmed')
                        ->where('tbl_return.distributor_id', Auth::user()->id)
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return.return_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return.return_order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_return')
                        ->select('tbl_return.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_return.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return.retailer_id')
                        ->where('tbl_return.return_order_type', 'Confirmed')
                        ->where('tbl_return.distributor_id', Auth::user()->id)
                        ->where('tbl_return.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_return.fo_id', $request->get('fos'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_return.return_order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_return.return_order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.change.returnOrdersList', compact('resultOrderList'));
    }
	
	
	public function return_change_products($return_order_id)
    {
		
		$selectedMenu   = 'ReturnOrder';      // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title
		
		if($return_order_id)
		{
			$resRetChangData = DB::table('tbl_return')
								->select('tbl_return.point_id', 'tbl_return.distributor_id', 'tbl_return.fo_id', 'tbl_return.route_id', 'tbl_return.retailer_id', 
										'tbl_return.total_return_value', 'tbl_return.total_change_value', 
								'tbl_retailer.name as retName', 'p1.name as retProdName', 'tbl_return_details.*')
								->join('tbl_return_details', 'tbl_return_details.return_order_id', '=', 'tbl_return.return_order_id')
								->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_return.retailer_id')
								->join('tbl_product as p1', 'p1.id', '=', 'tbl_return_details.return_product_id')
								//->join('tbl_product as p2', 'p2.id', '=', 'tbl_return_details.change_product_id')
								->where('tbl_return.return_order_id', $return_order_id)
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
							
		    return view('sales.change.categoryWiseReturnChange',compact('selectedMenu','pageTitle', 'resRetChangData','resultCategory','return_order_id','pointID','distributorID','routeid','retailderid','foID'));					
		
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


		if($this->return_change_validate($lastOrderId,$mTotalPrice))
		{

			

			DB::table('tbl_return')->where('return_order_id', $lastOrderId)
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


				///////////////////////////////// Zubair Change Stock (Out) Inventory /////////////////////////////////////

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

					DB::table('depot_inventory')->insert(
						[
							'point_id'           => $pointID,
							'depot_in_charge'    => Auth::user()->id,
							'cat_id'             => $request->get('change_cat_id')[$m],
							'product_id'         => $request->get('change_product_id')[$m],
							'product_qty'        => $request->get('change_qty')[$m],
							'product_value'      => $totalChangePrice,
							'inventory_date'     => date('Y-m-d'),
							'transaction_type'   => 'change',
							'inventory_type'     => $inOut,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id
						]
					); 
				

					$stockOutProduct = DB::table('depot_stock')
									->select('depot_id','point_id','cat_id','product_id','stock_qty')
									->where('point_id', $pointID)
									->where('cat_id', $request->get('change_cat_id')[$m])
									->where('product_id', $request->get('change_product_id')[$m])
									->first();

					
					$totalOutQty = $request->get('change_qty')[$m];
					if(sizeof($stockOutProduct)>0)
					{
					   
							
						$totalOutQty = $stockOutProduct->stock_qty - $request->get('change_qty')[$m];
						//$totalQty = $stockProduct->stock_qty - $request->get('change_qty')[$m];
						
						
						DB::table('depot_stock')
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
						
						DB::table('depot_stock')->insert(
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

				// Depot Change Stock (Out) end of Zubair
				
				
				////////////////////////// Zubair Change Stock (In) Inventory ///////////////////////////////

					$inOut='1'; // stock-in operation

					$pointID = '';
					if(sizeof($division_point)>0)
					{
						$pointID = $division_point->point_id;
					}

					$totalReturnPrice = $request->get('return_value')[$m];
					//$totalPrice = $request->get('return_value')[$m];

					DB::table('depot_inventory')->insert(
						[
							'point_id'           => $pointID,
							'depot_in_charge'    => Auth::user()->id,
							'cat_id'             => $request->get('return_cat_id')[$m],
							'product_id'         => $request->get('return_product_id')[$m],
							'product_qty'        => $request->get('return_qty')[$m],
							'product_value'      => $totalReturnPrice,
							'inventory_date'     => date('y-m-d'),
							'inventory_type'     => $inOut,
							'transaction_type'   => 'return',
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id
						]
					); 
				

					$stockInProduct = DB::table('depot_stock')
									->select('depot_id','point_id','cat_id','product_id','stock_qty')
									->where('point_id', $pointID)
									->where('cat_id', $request->get('return_cat_id')[$m])
									->where('product_id', $request->get('return_product_id')[$m])
									->first();

					
					$totalInQty = $request->get('return_qty')[$m];
					
					if(sizeof($stockInProduct)>0)
					{
					   
						$totalInQty = $stockInProduct->stock_qty + $request->get('return_qty')[$m];
						
						DB::table('depot_stock')
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
						
						DB::table('depot_stock')->insert(
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

				// Depot Stock (In) end of Zubair
				




				} // main if closed


			} // main for closed
			
			
			$resRetChangData = DB::table('tbl_return')
									->select('tbl_return.*')
									->where('tbl_return.return_order_id', $lastOrderId)
									->get();
			
			if(sizeof($resRetChangData) >0 )
			{
				if($resRetChangData[0]->total_approved_value > $resRetChangData[0]->total_return_value)
				{
					$remCreditValue = 0;
					$remCreditValue = ($resRetChangData[0]->total_approved_value - $resRetChangData[0]->total_return_value);
					$retailer_info = array();
					$retailer_info['trans_type'] = 'return_debited';
					$retailer_info['accounts_type'] = 'expense';
					$retailer_info['retailer_id'] = $request->get('retailer_id');
					$retailer_info['sales_amount'] = $remCreditValue;
					$retailer_info['point_id'] = $resRetChangData[0]->point_id;
					$retailer_info['retailer_invoice_no'] = $resRetChangData[0]->return_order_id;
					$this->reatiler_credit_ledger($retailer_info);
				} 
              /*
				else {
					$remCreditValue = 0;
					$remCreditValue = ($resRetChangData[0]->total_return_value - $resRetChangData[0]->total_approved_value);
					$retailer_info = array();
					$retailer_info['trans_type'] = 'return_credited';
					$retailer_info['accounts_type'] = 'income';
					$retailer_info['retailer_id'] = $request->get('retailer_id');
					$retailer_info['collection_amount'] = $remCreditValue;
					$this->reatiler_credit_ledger($retailer_info);
				}*/
			}
			
			
			return Redirect::to('/returnorder')->with('success', 'Successfully Return & Change Done.'); 
		
			
		
		} // validation if closed 
		else {
			
			return Redirect::to('/change-category-products/'.$lastOrderId)->with('failure', 'Return & Change value must be less or equal 100 taka.'); 
		} 
       
    }
	
	
	private function return_change_validate($return_order_ir,$total_approved_value)
	{
		$differenceAllowMax = 100;
		$differenceData = 0;
		
		$retTotData = DB::table('tbl_return')
							->select('total_return_value')
							->where('return_order_id', $return_order_ir)
							->first();
							
		if(sizeof($retTotData)>0)
		{
			if($retTotData->total_return_value>0)
			{
				$differenceData = $retTotData->total_return_value - $total_approved_value;
				$totalReturnValue = $retTotData->total_return_value;

				
				if(abs($totalReturnValue) <= $total_approved_value)
				{
					return true;
				} else {
					return false;
				}
			
			} else {
				
			   return false;	
			}
			  
		} else {

			return false;
		}		
		
		
	}		
	
	private function reatiler_credit_ledger($retailer_info = array())
	{
		if(is_array($retailer_info))
		{
			
			$credit_ledger_Data = array();
			$credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
			$credit_ledger_Data['collection_id'] = 0;
			$credit_ledger_Data['trans_type'] = $retailer_info['trans_type'];
			$credit_ledger_Data['accounts_type'] = $retailer_info['accounts_type'];
			$credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s');
			$credit_ledger_Data['point_id'] = $retailer_info['point_id'];
			$credit_ledger_Data['retailer_invoice_no'] = $retailer_info['retailer_invoice_no'];

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
	
			
			##retailerBalance
								############//bug fix by zubair mahmudul huq 2018-10-june #################
			
			if($credit_ledger_Data['trans_type'] == 'return_credited')
			{
				$credit_ledger_Data['retailer_collection'] = $retailer_info['collection_amount'];
				$remBalance = $retOpeningBalance - $retailer_info['collection_amount'];
				
			
			} elseif($credit_ledger_Data['trans_type'] == 'return_debited') {
			
			    $credit_ledger_Data['retailer_invoice_sales'] = $retailer_info['sales_amount'];
				$remBalance = $retOpeningBalance + $retailer_info['sales_amount'];
			}	
			
			//$remBalance = ($retOpeningBalance + $retInVoiceSales) + $retCollect;  //bug fix
			
			$credit_ledger_Data['retailer_balance'] = $remBalance;
			
			$credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
			$credit_ledger_Data['entry_by'] = Auth::user()->id;
			
			
			DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
			
			
		}	
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
