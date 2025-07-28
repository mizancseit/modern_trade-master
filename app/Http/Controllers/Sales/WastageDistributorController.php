<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class WastageDistributorController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 24/12/2017
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_wastage()
    {
        $selectedMenu   = 'Wastage';             // Required Variable
        $subSelectedMenu  = 'Wastage Delivery'; 
        $pageTitle      = 'Wastage Delivery';            // Page Slug Title

        $resultFO       = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')                       
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.distributor_id', Auth::user()->id)
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_wastage.fo_id')
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')                  
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
                        
                        /*
                        echo '<pre/>';
                        echo print_r($resultOrderList); exit;*/

        return view('sales/distributor/wastage', compact('selectedMenu','pageTitle','resultFO','resultOrderList'));
    }

    public function ssg_wastage_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('fos')=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')                  
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')                  
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.distributor_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->where('tbl_wastage.fo_id', $request->get('fos'))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales/distributor/wastageList', compact('resultOrderList'));
    }


    public function ssg_wastage_edit($wastageMainId,$foMainId)
    {
        $selectedMenu   = 'Wastage';                   // Required Variable
        $pageTitle      = 'Wastage Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.order_no','tbl_wastage.retailer_id','tbl_wastage.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                        ->where('tbl_wastage.order_type','Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage_details.order_id',$wastageMainId)
                        ->groupBy('tbl_wastage_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_wastage')->select('tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.fo_id','users.display_name','tbl_wastage.retailer_id','order_no','order_date','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('users', 'users.id', '=', 'tbl_wastage.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type','Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage.order_id',$wastageMainId)
                        ->first();


        // for offers

        // for FO Information

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','users.display_name','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $resultCategory = DB::table('tbl_product_category')
                            ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                            ->where('status', '0')
                            ->where('gid', Auth::user()->business_type_id)
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->get();
        

        return view('sales.distributor.wastageBucketEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','wastageMainId','foMainId','resultFoInfo'));
    }


    public function ssg_wastage_edit_submit(Request $request)
    {
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
       $chalanNO = 'W-'.Auth::user()->sap_code.'-'.date('ymd').$autoAdd;
        //dd($chalanNO);

       DB::table('tbl_wastage')->where('order_id', $lastOrderId)
            ->where('fo_id', $request->get('foMainId'))
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'order_type'             => 'Delivered',
                'total_delivery_qty'     => $mTotalQty,
                'total_delivery_value'   => $mTotalPrice,
                'update_date'            => date('Y-m-d H:i:s'),
                'chalan_no'              => $chalanNO,
                'chalan_date'            => date('Y-m-d H:i:s'),
            ]
        );
        
        //get order Data
        $checkOrdata = DB::table('tbl_wastage')
                                ->where('order_id', $lastOrderId)
                                ->first();

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $checkItemsExiting = DB::table('tbl_wastage_details')
                                ->where('order_id', $lastOrderId)
                                ->where('product_id',$request->get('product_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    //$totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];                        

                    DB::table('tbl_wastage_details')->where('product_id',$request->get('product_id')[$m])->update(
                        [
                            'delivery_cat_id'                 => $request->get('change_cat_id')[$m],
                            'delivery_product_id'             => $request->get('change_product_id')[$m],
                            'replace_delivered_qty'           => $request->get('qty')[$m],
                            'replace_delivered_value'         => $request->get('price')[$m]
                        ]
                    );
                    
                    $this->stock_out($checkOrdata->point_id,            // no  
                                      $request->get('change_cat_id')[$m],       
                                      $request->get('change_product_id')[$m],  
                                      $request->get('qty')[$m], 0, 
                                      'wastage');
                }
            }
        }

        if(sizeof($checkOrdata)>0)
            {
                $retailer_info = array();
                $retailer_info['trans_type'] = 'wastage'; 
                $retailer_info['accounts_type'] = 'expense';
                $retailer_info['retailer_id'] = $checkOrdata->retailer_id;
                $retailer_info['order_id'] = $lastOrderId;
                $retailer_info['invoice_no'] = $checkOrdata->order_no;
                $retailer_info['point_id'] = $checkOrdata->point_id;
                
                $this->reatiler_wastage_credit_ledger($retailer_info);
            }  
        
        


      return Redirect::to('/wastage-delivery')->with('success', 'Successfully Confirm Delivery Done.'); 
    
    }
    
    
    private function stock_out($pointID, $cat_id, $product_id, $prod_qnty, $prod_value, $trans_type)
    {
        //dd(session('isDepot'));

       

        $inOut='2'; // stock-out operation
      
        DB::table('depot_inventory')->insert(
            [
                'point_id'           => $pointID,
                'depot_in_charge'    => Auth::user()->id,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'product_qty'        => $prod_qnty,
                'product_value'      => $prod_value,
                'inventory_date'     => date('Y-m-d'),
                'inventory_type'     => $inOut,
                'transaction_type'   => $trans_type,
                'global_company_id'  => Auth::user()->global_company_id,
                'created_by'         => Auth::user()->id
            ]
        ); 
            

        $stockOutProduct = DB::table('depot_stock')
                        ->select('depot_id','point_id','cat_id','product_id','stock_qty')
                        ->where('point_id', $pointID)
                        ->where('cat_id', $cat_id)
                        ->where('product_id', $product_id)
                        ->first();

        $totalOutQty = $prod_qnty;
                
        if(sizeof($stockOutProduct)>0)
        {
                
            $totalOutQty = $stockOutProduct->stock_qty - $prod_qnty;
            
            
            DB::table('depot_stock')
            ->where('point_id',$pointID)
            ->where('cat_id',$cat_id)
            ->where('product_id',$product_id)
            ->update(
            [
                'point_id'           => $pointID,
                'cat_id'             => $cat_id,
                'product_id'         => $product_id,
                'stock_qty'          => $totalOutQty,
                'global_company_id'  => Auth::user()->global_company_id,
                'updated_by'         => Auth::user()->id                   
                
            ]
            );

        } else {  //save negative stock 
            
            DB::table('depot_stock')->insert(
                [
                    'point_id'           => $pointID,
                    'cat_id'             => $cat_id,
                    'product_id'         => $product_id,
                    'stock_qty'          => '-' . $totalOutQty,
                    'global_company_id'  => Auth::user()->global_company_id,
                    'created_by'         => Auth::user()->id      
                ]
            ); 
        
        }

        
                
                
    }


    public function ssg_invoice_wastage($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Wastage';  
        $selectedSubMenu  = 'Wastage';                  // Required Variable
        $pageTitle      = 'Invoice Details';           // Page Slug Title

        $resultCartPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.order_no','tbl_wastage.retailer_id','tbl_wastage.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                        ->where('tbl_wastage.order_type','Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage_details.order_id',$orderMainId)
                        ->groupBy('tbl_wastage_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_wastage')->select('tbl_wastage.auto_order_no','tbl_wastage.update_date','tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.fo_id','tbl_wastage.retailer_id','tbl_wastage.order_no','tbl_wastage.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type','Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_wastage')->select('tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_wastage.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.order_type','Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_wastage')->select('tbl_wastage.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id',$foMainId)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        
        return view('sales.report.wastage.distributor.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }


    private function reatiler_wastage_credit_ledger($retailer_info = array())
    {
        if(is_array($retailer_info))
        {
            $credit_ledger_Data = array();
            $credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
            $credit_ledger_Data['point_id'] = $retailer_info['point_id'];
            $credit_ledger_Data['collection_id'] = 0;
            $credit_ledger_Data['trans_type'] = $retailer_info['trans_type'];
            $credit_ledger_Data['accounts_type'] = $retailer_info['accounts_type'];
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

            ## invoice No & sales
            $credit_ledger_Data['retailer_invoice_no'] = $retailer_info['invoice_no'];          
            
            /*$rowRet = DB::select("SELECT grand_total_value FROM tbl_order WHERE order_no = '".$retailer_info['invoice_no']."'");*/

            $rowRet = DB::select("SELECT total_value,total_delivery_value FROM tbl_wastage WHERE order_id = '".$retailer_info['order_id']."'");


            $retInVoiceSales = $rowRet[0]->total_delivery_value - $rowRet[0]->total_value;
            
            $credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;           
            
            ##totalCollection
            $retCollect = 0;
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
