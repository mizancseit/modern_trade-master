<?php 

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Sales\ProductsStockUploadModel;

use Hash;
use DB;
use Auth;
use Session;
use Excel;

class PartialDelivery extends Controller
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

   
   public function partial_delivery_report(Request $request){

        $selectedMenu   = 'Stock report sku wise';    // Required Variable for menu
        $selectedSubMenu= 'Summary';                  // Required Variable for submenu
        $pageTitle      = 'Summary Report';           // Page Slug Title 
          
        $stocks1 = DB::table('eshop_product_stock')
            ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name')
            ->selectRaw("SUM(IF(eshop_product_stock.status=1,eshop_product_stock.qty,0)) AS instock, 
				SUM(IF(eshop_product_stock.status=2,eshop_product_stock.qty,0)) AS hold_stock")
            ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')     
            ->where('eshop_product_stock.type', '!=', 'out')
            ->groupBy('eshop_product_stock.order_id')                           
            ->get(); 
 

        $stocks = DB::select("SELECT * FROM (SELECT eshop_order.order_id, eshop_order.order_no, SUM(IF(eshop_product_stock.status=1,eshop_product_stock.qty,0)) AS instock, 
			SUM(IF(eshop_product_stock.status=2,eshop_product_stock.qty,0)) AS hold_stock   
			FROM  `eshop_product_stock` 
			INNER JOIN `eshop_order` ON `eshop_order`.`order_id` = `eshop_product_stock`.`order_id` 
			WHERE NOT `eshop_product_stock`.`type` = 'out'
			GROUP BY `eshop_product_stock`.`order_id`) AS asss
			WHERE asss.hold_stock>asss.instock");



       //      $stocks2 = DB::table("eshop_product_stock")
 						// ->select('eshop_product_stock.id', 'eshop_product_stock.product_id', 'eshop_product_stock.type', 'eshop_product_stock.qty', 'eshop_product_stock.status', 'eshop_product_stock.order_details_id','eshop_product_stock.created_at','eshop_product.sap_code','eshop_product.name',
 						// 	DB::raw("SUM(IF(eshop_product_stock.status=1,eshop_product_stock.qty,0)) AS instock, 
							// 	SUM(IF(eshop_product_stock.status=2,eshop_product_stock.qty,0)) AS hold_stock"), 
       //              	 ) 
 						// ->where(function($q){ 
 						// 	DB::raw("SUM(IF(eshop_product_stock.status=1,eshop_product_stock.qty,0)) AS instock, 
							// 	SUM(IF(eshop_product_stock.status=2,eshop_product_stock.qty,0)) AS hold_stock")
       //              	 ) 
 						// })
 						// ->groupBy('eshop_product_stock.order_id')
 						// ->join('eshop_product', 'eshop_product.id', '=', 'eshop_product_stock.product_id')   
       //    				->get();
   

        return view('eshop::partial-delivery/partial-delivery-report', compact('stocks','pageTitle','selectedMenu')); 
    }

    public function eshop_delivery_approved_view($DeliveryMainId){
            $selectedMenu   = 'Delivery Approved';          // Required Variable
            $pageTitle      = 'Delivery Details';           // Page Slug Title

            $resultCartPro  = DB::table('eshop_order_details')
            ->select('eshop_order_details.cat_id','eshop_order_details.order_id','eshop_product_category.id AS catid','eshop_product_category.name AS catname','eshop_order.order_id','eshop_order.fo_id','eshop_order.order_status','eshop_order.order_no','eshop_order.po_no','eshop_order.party_id','eshop_order.customer_id','eshop_order.global_company_id')

            //->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
            ->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
            ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')

            ->join('eshop_order', 'eshop_order.order_id', '=', 'eshop_order_details.order_id')
            
            ->where('eshop_order.global_company_id', Auth::user()->global_company_id)                       
            ->where('eshop_order_details.order_id',$DeliveryMainId)
            ->groupBy('eshop_order_details.cat_id')                        
            ->get();

            $resultInvoice  = DB::table('eshop_order')->select('eshop_order.global_company_id','eshop_order.order_id','eshop_order.order_status','eshop_order.fo_id','users.display_name','eshop_order.party_id','eshop_order.customer_id','eshop_order.order_no','eshop_order.po_no','eshop_order.order_date','eshop_party_list.name','eshop_party_list.mobile','eshop_party_list.address')
            ->join('users', 'users.id', '=', 'eshop_order.fo_id')
            ->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')                 
            ->where('eshop_order.order_id',$DeliveryMainId)
            ->first();

            // dd($resultInvoice);
            
            $orderCommission = DB::table('eshop_categroy_wise_commission') 
            ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
            ->where('order_id', $resultInvoice->order_id)                        
            // ->where('entry_by',Auth::user()->id)                        
            // ->where('party_id',$foMainId)
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

             $customerInfo = DB::table('eshop_order')
                            ->select('eshop_order.order_id','eshop_order.order_status','eshop_customer_list.name','eshop_customer_list.sap_code','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_order.total_order_value')
                            ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
                            ->where('eshop_order.order_id',$DeliveryMainId)
                            ->first();


            return view('eshop::partial-delivery/DeliveryApprovedEdit', compact('selectedMenu','pageTitle','resultCartPro','resultCategory','resultInvoice','DeliveryMainId','foMainId','resultFoInfo', 'orderCommission','customerInfo'));
    }
    public function partial_delivery_received(Request $request){
        DB::beginTransaction(); 
        $index_key = 0;
        if($request->status=='yes'){ 
            $eshop_order = DB::table('eshop_order')->where('eshop_order.order_id', $request->orderid)->where('eshop_order.customer_id', $request->customerid)
            ->join('eshop_order_details','eshop_order.order_id','=','eshop_order_details.order_id')
            ->get();              
            foreach ($request->qty as $key => $order) {
                $product = DB::table('eshop_product')->where('id',$key)->first();
                $sumQty = $product->stock_qty + $order;
                DB::table('eshop_product')->where('id',$key)->update(['stock_qty'=> $sumQty]);
                 
                DB::table('eshop_order_details')->where('order_det_id',$request->items_id[$index_key])->update([
                    'received_qty'=> $order
                ]);
             
                DB::table('eshop_product_stock')->insert([
                    'product_id' =>  $key,
                    'order_id'  => $request->orderid,
                    'order_details_id' => $request->items_id[$index_key], 
                    'type' => 'in',
                    'qty' => $order,
                    'status' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Auth::user()->id
                ]); 
                $index_key ++;
            }          

            $totalSales = DB::table('eshop_order')
            ->join('eshop_customer_list', 'eshop_customer_list.customer_id', 'eshop_order.customer_id')
            ->where('eshop_order.order_id', $request->orderid)->where('eshop_order.order_status', 'Delivered')->first();


            if(sizeof($totalSales)>0){
                $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $request->customerid)->orderBy('ledger_id','DESC')->first(); 
                // dd($ledger);
                if(sizeof($ledger)){
                    $closing_balance = $ledger->closing_balance;
                }else{
                    $closing_balance = 0;
                }
                DB::table('eshop_outlet_ledger')->insert([
                  'ledger_date'           => date('Y-m-d h:i:s'),
                  'outlet_id'             => $totalSales->party_id,
                  'customer_id'           => $totalSales->customer_id,
                  'ref_id'                => $totalSales->order_id,
                  'trans_type'            => 'sales',
                  'party_sap_code'        => $totalSales->sap_code,
                  'opening_balance'       => $closing_balance,
                  'debit'                 => $totalSales->total_delivery_value,
                  'credit'                => 0,
                  'closing_balance'       => $closing_balance+$totalSales->total_delivery_value,
                  'entry_by'              => Auth::user()->id,
                  'entry_date'            => date('Y-m-d h:i:s')
                ]);

                $salesCommission = DB::table('eshop_categroy_wise_commission')
                ->where('order_id', $request->orderid)->sum('delivery_commission_value');

                if(sizeof($salesCommission)>0){

                    $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $request->customerid)->orderBy('ledger_id','DESC')->first();
                    
                    // dd($ledger);
                    if(sizeof($ledger)){
                        $closing_balance = $ledger->closing_balance;
                    }else{
                        $closing_balance = 0;
                    }

                    DB::table('eshop_outlet_ledger')->insert([
                        'ledger_date'           => date('Y-m-d h:i:s'),
                        'outlet_id'             => $totalSales->party_id,
                        'customer_id'           => $totalSales->customer_id,
                        'ref_id'                => $totalSales->order_id,
                        'trans_type'            => 'sales_commission',
                        'party_sap_code'        => $totalSales->sap_code,
                        'opening_balance'       => $closing_balance,
                        'debit'                 => 0,
                        'credit'                => $salesCommission,
                        'closing_balance'       => $closing_balance-$salesCommission,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')

                    ]);
                }
            }
        }
        DB::commit();
        DB::rollBack();
        return Redirect::to('/eshop-partial-delivery-report')->with('success', 'Partial delivery received successfully!');
    }
}
