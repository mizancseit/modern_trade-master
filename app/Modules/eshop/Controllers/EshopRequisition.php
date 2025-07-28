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

class EshopRequisition extends Controller
{
	/**
	*
	* Created Abdul Mazid
	* Date : 03-18-2021
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function customer_list()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

        $routeResult = DB::table('eshop_route')
                        ->where('status',0)
                        ->get();

        $customerResult = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();
      

        return view('eshop::eshop.requisition.outlet-list', compact('selectedMenu','pageTitle','routeResult','resultRetailer','customerResult'));
    }

    public function eshop_party_list(Request $request){

        $customerID = $request->get('customer');

        $resultParty = DB::table('eshop_party_list')
                        ->select('party_id','name','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customerID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();      

        return view('eshop::eshop.requisition.party-list', compact('resultParty','customerID'));
    }
    public function eshop_order_process($partyid,$customer_id)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title 

        $resultParty = DB::table('eshop_party_list')
                        ->select('party_id','name','route_id','customer_id','owner','address','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('customer_id', $customer_id)                       
                        ->where('party_id', $partyid)
                        ->where('status', 0)
                        ->first();
        
        $resultCategory = DB::table('eshop_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
       

        $resultCart     = DB::table('eshop_order')                     
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid) 
                        ->Where('order_status', 'Ordered')                       
                        ->first();  

        $customerResult = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();

        
        
        return view('eshop::eshop.requisition.category-with-order', 
        compact('selectedMenu','pageTitle','resultParty','resultCategory','partyid','customer_id','resultCart' ,'customerResult'));
    }

    public function eshop_category_products(Request $request)  {
        $categoryID = $request->get('categories');
        $party_id     = $request->get('retailer_id');

        $resultProduct = DB::table('eshop_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit','sap_code')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $lastDiscount = DB::table('eshop_categroy_wise_commission')        
                        ->where('party_id', $party_id)
                        ->where('cat_id', $categoryID)
                        ->orderBy('id', 'desc') 
                        ->first();

        return view('eshop::eshop.requisition.cat-wise-products', compact('resultProduct','categoryID','party_id','lastDiscount'));
    }

    public function eshop_add_to_cart_products(Request $request)
    {
        
        $customerResult = DB::table('eshop_customer_list') 
                        ->where('customer_id',$request->get('customer_id'))
                        ->where('status',0)
                        ->first();

        if(sizeof($customerResult) > 0){
            $sap_code = $customerResult->sap_code;
        }else{
            $sap_code = 0;
        }
        $party_id     = $request->get('party_id');
        $autoOrder    = DB::table('eshop_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
        $autoOrderId  = $autoOrder ? $autoOrder->auto_order_no : rand(100000,999999);   
        $orderNo      = 'SO-'.rand(100000,999999).'-'.substr(date("Y"), -2).date("m-d").'-'.$autoOrderId;

        $totalQty   = $request->get('totalQty');
        $totalValue = $request->get('totalValue');


        $countRows = count($request->get('qty'));

        $resultCart = DB::table('eshop_order')
                    ->where('order_status', 'Ordered')                        
                    ->where('fo_id',Auth::user()->id)                        
                    ->where('party_id',$party_id)
                    ->orderBy('order_id','DESC')                         
                    ->first();  

        $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();   

        if(sizeof($resultCart)== 0) { 
            $eshop_order_id = DB::table('eshop_order')->insertGetId([
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'order_status'          => 'Ordered',
                    'total_order_qty'       => $totalQty,
                    'total_order_value'     => $totalValue,
                    'customer_id'           => $request->get('customer_id'),
                    'party_id'              => $request->get('party_id'),
                    'management_id'         => $supervisorList->management_id,
                    'manager_id'            => $supervisorList->manager_id,
                    'executive_id'          => $supervisorList->executive_id,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'entry_by'              => Auth::user()->id,
                    'entry_date'            => date('Y-m-d h:i:s')                    
                ]);   

            for($m=0;$m<$countRows;$m++) {
                if($request->get('qty')[$m]!='') {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];
                    $order_detail = DB::table('eshop_order_details')
                    ->where('product_id', $request->get('produuct_id')[$m])->where('order_id',$eshop_order_id)->first();
                    if($order_detail){
                        DB::table('eshop_order_details')->where('order_id', $eshop_order_id)
                        ->where('product_id', $request->get('produuct_id')[$m])->update([
                            'order_qty'         => $request->get('qty')[$m] + $order_detail->order_qty,
                            'order_total_value' => $totalPrice + $order_detail->order_total_value, 
                        ]);
                    }else{ 
                        DB::table('eshop_order_details')->insert([
                            'order_id'          => $eshop_order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'sap_code'          => $request->get('sap_code')[$m],
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'item_discount'     => $request->get('item_discount')[$m],
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $party_id,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')
                        ]);
                    }
                }
            }
            $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(item_discount) AS total_discount'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $eshop_order_id)
                        ->first();

            DB::table('eshop_order')->where('order_id', $eshop_order_id)->where('entry_by',Auth::user()->id)
                ->where('party_id',$party_id)->update([
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'total_discount'        => $totalOrder->total_discount,
                    'entry_date'            => date('Y-m-d h:i:s')                    
                ]);  
            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $eshop_order_id,
                        'cat_id'                => $request->get('cat_id'),
                        'customer_id'           => $request->get('customer_id'),
                        'party_id'              => $request->get('retailer_id'),
                        'fo_id'                 => Auth::user()->id,
                        'order_value'           => $totalValue, 
                        'order_commission_value'=> $commissionValue,
                        'commission'            => $request->get('commission'),
                        'global_company_id'     => Auth::user()->global_company_id,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')
                    ]
                );
            }
        }else{ 
            for($m=0;$m<$countRows;$m++){
                if($request->get('qty')[$m]!=''){
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m]; 
                    $order_detail = DB::table('eshop_order_details')
                    ->where('product_id', $request->get('produuct_id')[$m])->where('order_id',$resultCart->order_id)->first();
                    if($order_detail){
                        DB::table('eshop_order_details')->where('order_id', $resultCart->order_id)
                        ->where('product_id', $request->get('produuct_id')[$m])->update([
                            'order_qty'         => $request->get('qty')[$m] + $order_detail->order_qty,
                            'order_total_value' => $totalPrice + $order_detail->order_total_value, 
                        ]);
                    }else{ 
                        DB::table('eshop_order_details')->insert([
                            'order_id'          => $resultCart->order_id,
                            'order_date'        => date('Y-m-d H:i:s'),
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'sap_code'          => $request->get('sap_code')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'order_total_value' => $totalPrice,
                            'order_det_status'  => 'Ordered',
                            'item_discount'     => $request->get('item_discount')[$m],
                            'discount_rate'     => $request->get('commission'),
                            'party_id'          => $party_id,
                            'entry_by'          => Auth::user()->id,
                            'entry_date'        => date('Y-m-d h:i:s')

                        ]);
                    }
                }
            } 
            $totalOrder = DB::table('eshop_order_details')
                        ->select(DB::raw('SUM(order_qty) AS totalQty'),  DB::raw('SUM(item_discount) AS total_discount'), DB::raw('SUM(order_total_value) AS totalValue'))
                        ->where('order_id', $resultCart->order_id)                        
                        ->where('entry_by',Auth::user()->id)                        
                        ->where('party_id',$party_id)
                        ->first();

            DB::table('eshop_order')->where('order_id', $resultCart->order_id)  
                ->where('entry_by',Auth::user()->id)                        
                ->where('party_id',$party_id)->update([
                    'total_order_qty'       => $totalOrder->totalQty,
                    'total_order_value'     => $totalOrder->totalValue,
                    'total_discount'        => $totalOrder->total_discount,
                    'entry_date'            => date('Y-m-d h:i:s')
                ]); 
            if(sizeof($request->get('commission'))>0){
                $commissionValue = ($totalValue * $request->get('commission'))/100;
                
                DB::table('eshop_categroy_wise_commission')->insert(
                    [
                        'order_id'              => $resultCart->order_id,
                        'cat_id'                => $request->get('cat_id'),
                        'customer_id'           => $request->get('customer_id'),
                        'party_id'              => $request->get('retailer_id'),
                        'fo_id'                 => Auth::user()->id,
                        'order_value'           => $totalValue, 
                        'order_commission_value'=> $commissionValue,
                        'commission'            => $request->get('commission'),
                        'global_company_id'     => Auth::user()->global_company_id,
                        'entry_by'              => Auth::user()->id,
                        'entry_date'            => date('Y-m-d h:i:s')
                    ]
                );
            }      
        } 
        return Redirect::to('/eshop-requisition-process/'.$request->get('retailer_id').'/'.$request->get('customer_id'))->with('success', 'Successfully Added Add To Cart.');
    }


    public function carts($customer_id,$partyid)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title 

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company','tbl_global_company.global_company_id','=','users.global_company_id') 
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first(); 

        $resultInvoice  = DB::table('eshop_order')->select('order_id','po_no','order_status','fo_id','party_id','total_order_value')
                        ->where('order_status', 'Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('party_id',$partyid)
                        ->first();

        $getCats        = DB::table('eshop_order_details')
                        ->select('eshop_order_details.cat_id','eshop_product_category.name AS catname')  
                        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')  
                        ->where('eshop_order_details.order_id', $resultInvoice->order_id)  
                        ->groupBy('eshop_order_details.cat_id')                       
                        ->get(); 

        $orderCommission = DB::table('eshop_categroy_wise_commission') 
                        ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                        ->where('order_id', $resultInvoice->order_id)    
                        ->first();

        $customerResult = DB::table('eshop_customer_list')
                    ->where('customer_id',$customer_id)
                    ->where('status',0)
                    ->first();

        $closingResult = DB::table('eshop_outlet_ledger')
                        ->where('customer_id',$customer_id)
                        ->orderBy('ledger_id','DESC')
                        ->first();

            if(sizeof($closingResult)>0){
                $closingBalance = $closingResult->closing_balance;
            } else{
                $closingBalance = 0;
            } 

           $creditSummery = $customerResult->credit_limit - $closingBalance - $resultInvoice->total_order_value;        

            //echo $closingBalance;


        return view('eshop::eshop.requisition.carts',compact('selectedMenu','pageTitle','partyid','customer_id','resultCartPro','resultInvoice','orderCommission','creditSummery','getCats'));
   

    }

   

    public function req_manage()
    {
        $selectedMenu    = 'Requisition Manage';             // Required Variable
        $selectedSubMenu = 'Requisition Manage';             // Required Variable
        $pageTitle       = 'Requisition Manage';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'requisition'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList'));
    }
	
	
	public function req_add()
    {
        $selectedMenu    = 'Requisition Manage';             // Required Variable
        $selectedSubMenu = 'Requisition Manage';             // Required Variable
        $pageTitle       = 'Requisition Manage';            // Page Slug Title
				

		$reqAddList = DB::select("SELECT u.display_name, u.id, ud.sap_code, p.point_id, p.point_name
								FROM users u JOIN tbl_user_business_scope bs ON  u.id = bs.user_id 
								JOIN tbl_user_details ud ON ud.user_id = u.id
								JOIN tbl_point p ON p.point_id = bs.point_id
								WHERE u.id='".Auth::user()->id."'");

		$LastReqId = DB::select("SELECT (MAX(req_id) + 1) as last_req_id FROM depot_requisition");						

        return view('eshop::requisition/req_add', compact('selectedMenu','selectedSubMenu','pageTitle','reqAddList','LastReqId'));
    }
	
	
	public function req_process(Request $req)
    {
      
		if ($req->isMethod('post')) 
		{
			$selectedMenu    = 'Requisition Manage';             // Required Variable
			$selectedSubMenu = 'Requisition Manage';             // Required Variable
			$pageTitle       = 'Requisition Manage';            // Page Slug Title
			
			$point_id 		= 	$req->input('point_id');
			$req_no 		= 	$req->input('req_no');
			//$reDate		= 	$req->input('req_date');
			//$reDate 		= date('Y-m-d H:i:s');
			
			$reDate = date('Y-m-d H:i:s', strtotime($req->input('req_date'))); //mm-dd-yyyy
			 
			$depot_in_charge =	$req_by = Auth::user()->id;
			
			
			
			$point=DB::insert('insert into depot_requisition(depot_in_charge, point_id, req_no, req_by, 
			req_date, req_status, is_active) 
			values (?,?,?,?,?,?,?)', [$depot_in_charge, $point_id, $req_no, $req_by, $reDate, 'requisition', 'YES']);
			
			
			
			$req_id = DB::getPdo()->lastInsertId(); //mysql_insert_id();
					 
			return Redirect::to('/req-list_product/'.$req_id)->with('success', 'Successfully Requisition Added.');
			   
		}
    }
	
	
	public function req_list_product($req_id)
	{
		$selectedMenu    = 'Requisition Manage';             // Required Variable
		$selectedSubMenu = 'Requisition Manage';             // Required Variable
		$pageTitle       = 'Requisition Manage';            // Page Slug Title
			
			
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_id = '".$req_id."'
										AND dr.req_status = 'requisition'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
										
		//echo '<pre/>'; print_r($resultReqList); exit;								
										
		$resultCategory = DB::table('eshop_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
						
		 $resultCart     = DB::select("SELECT SUM(req_value) as grand_total_value FROM depot_req_details WHERE req_id = '".$req_id."'");
									
						
		return view('eshop::requisition/categoryWithOrder', compact('selectedMenu','selectedSubMenu','pageTitle',
																	'resultCategory','resultReqList', 'resultCart', 'req_id'));				
		
	}
	
	
	
	
	public function req_add_product(Request $request)
	{
	   
        if($request->isMethod('post'))
        {
			$countRows = count($request->get('qty'));
			
            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                    DB::table('depot_req_details')->insert(
                        [
                            'req_id'          	=> $request->get('req_id'),
                            'product_id'      	=> $request->get('produuct_id')[$m],
                            'cat_id'        	=> $request->get('category_id')[$m],
                            'req_qnty'         	=> $request->get('qty')[$m],
                            'req_value'       	=> $totalPrice,
                        ]
                    ); 
                }
            }
  

            //return Redirect::to('/req-bucket/'.$request->get('req_id'))->with('success', 'Successfully Added.');
            return Redirect::to('/req-list_product/'.$request->get('req_id'))->with('success', 'Successfully Added.');
        }
		
	}
	
	public function req_category_products(Request $request)
    {
        $categoryID = $request->get('categories');


        if(session('isDepot')==1) // Depot
        { 
        	$resultProduct = DB::table('eshop_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
        }
        else // Distributor
        {
        	$resultProduct = DB::table('eshop_product')
                        ->select('id','category_id','name','ims_stat','status','distri AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
        }

        return view('eshop::requisition/allProductList', compact('resultProduct','categoryID'));
    }
	
	
	
	public function req_bucket($reqid)
    {
		$selectedMenu    = 'Requisition Manage';             // Required Variable
		$selectedSubMenu = 'Requisition Manage';             // Required Variable
		$pageTitle       = 'Requisition Manage';            // Page Slug Title

		$resultReqPro = DB::select("SELECT rdet.req_det_id as id, u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'requisition'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		
        return view('eshop::requisition/bucket', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
    }
	
	
	public function req_send($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'send',
							'sent_by'  => Auth::user()->id,
							'sent_date'  => Date('Y-m-d H:i:s')
							
						]
					); 
		}
		
		return Redirect::to('/req-send_list/')->with('success', 'Successfully Send.');
	}
	
	
	public function req_send_list()
	{
		$selectedMenu    = 'Requisition Send';             // Required Variable
        $selectedSubMenu = 'Requisition Send';             // Required Variable
        $pageTitle       = 'Requisition Send';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'send'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_send_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_acknowledge_list()
	{
		$selectedMenu    = 'Requisition Acknowledge';             // Required Variable
        $selectedSubMenu = 'Requisition Acknowledge';             // Required Variable
        $pageTitle       = 'Requisition Acknowledge';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'acknowledge'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_acknowledge_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_pending_list(Request $req)
	{
		$selectedMenu    = 'Requisition Pending';             // Required Variable
        $selectedSubMenu = 'Requisition Pending';             // Required Variable
        $pageTitle       = 'Requisition Pending';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		$custList = DB::table('users')
                        ->select('users.*')
						->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('users.user_type_id', '5')
                        ->where('users.is_active', 0) 
						->orderBy('users.display_name','ASC')
                        ->get();
		
		$whereCond = '';
		
		$sel_from_ReqDate = '';
		if ($req->get('fromReqDate') != '') 
		{
			$sel_from_ReqDate = $req->get('fromReqDate');
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND dr.req_date >= '".$fromDate."' ";
		}
		
		$sel_to_ReqDate = '';
		if ($req->get('toReqDate') != '') 
		{
			$sel_to_ReqDate = $req->get('toReqDate');
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND dr.req_date <= '".$toDate."' ";
		}
		
		$sel_div_id = '';
		if ($req->get('div_id') != '') 
		{
			$sel_div_id = $req->get('div_id');
			$whereCond .= " AND d.div_id = '".$req->get('div_id')."'";
		}
		
		$sel_cust_id = '';
		if ($req->get('cust_id') != '') 
		{
			$sel_cust_id = $req->get('cust_id');
			$whereCond .= " AND u.id = '".$req->get('cust_id')."'";
		}
		
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		$sel_req_status = '';
		if ($req->get('req_status') != '') 
		{
			$sel_req_status = $req->get('req_status');
			$whereCond .= " AND dr.req_status = '".$req->get('req_status')."'";
		}
						
		$resultReqList = DB::select("SELECT u.display_name, u.sap_code, p.point_id, p.point_name, p.business_type_id,
									dr.*, d.div_id,
									SUM(rdet.req_qnty) as totQnty, SUM(rdet.req_value) as totVal
									FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
									JOIN tbl_point p ON p.point_id = dr.point_id 
									JOIN tbl_division d ON d.div_id = p.point_division
									JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
									WHERE dr.req_status = 'send'
									AND dr.is_active = 'YES' 
									$whereCond
										GROUP BY dr.req_id
									ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_pending_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList','divList',
		'sel_from_ReqDate', 'sel_to_ReqDate', 'sel_div_id', 'sel_cust_id', 'sel_business_type', 'sel_req_status', 'custList'));			
		
	}
	
	
	/* Requisition Approved */
	
	public function req_acknowledge(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			
			foreach($req->input('reqid') as $rowReqId)
			{
				$ordack = 'ordack' . $rowReqId;
				$ordackVal = $req->input($ordack);
				
				//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
				
				if($ordackVal == 'YES')
				{
					//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
						
					$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$rowReqId)->update(
						[
							'req_status'  => 'acknowledge',
							'acknowledge_by'  => Auth::user()->id,
							'acknowledge_date'  => date('Y-m-d H:i:s')
						]
					);
					
					$point_id = $req->input('point_id' . $rowReqId);
					$depot_in_charge = $req->input('depot_in_charge' . $rowReqId);
					$trans_amount = $req->input('trans_amount' . $rowReqId);
					
					// cash block
						
						$cash_block = array();
						$cash_block['point_id'] = $point_id;
						$cash_block['depot_in_charge'] =  $depot_in_charge;
						$cash_block['transaction_type'] =  'debit';
						$cash_block['payment_type'] =  'CASH';
						$cash_block['trans_amount'] =  $trans_amount;
						$cash_block['trans_date'] =  date('Y-m-d H:i:s');
						$cash_block['entry_by'] =  Auth::user()->id;
						$cash_block['entry_date'] =  date('Y-m-d H:i:s');
						
						DB::table('depot_accounts_payments')->insert([$cash_block]);
						
					$point_id = '';
					$depot_in_charge = '';
					$trans_amount = '';	
				
				}
				
			}
			
			
		}
		
		return Redirect::to('/reqAllAnalysisList/')->with('success', 'Successfully Acknowledge.');
	}
	
	
	public function req_acknowledge_new(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			
			foreach($req->input('reqid') as $rowReqId)
			{
				$ordack = 'ordack' . $rowReqId;
				$ordackVal = $req->input($ordack);
				
				//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
				
				if($ordackVal == 'YES')
				{
					//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
						
					$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$rowReqId)->update(
						[
							'req_status'  => 'approved',
							'approved_by'  => Auth::user()->id,
							'approved_date'  => date('Y-m-d H:i:s')
						]
					);
					
					
					$DepotReqDetUpd = DB::table('depot_req_details')->where('req_id',$rowReqId)->update(
						[
							'approved_qnty'  => DB::raw("req_qnty"),
							'approved_value'  => DB::raw("req_value")
						]
					);
					
					
					$point_id = $req->input('point_id' . $rowReqId);
					$depot_in_charge = $req->input('depot_in_charge' . $rowReqId);
					$trans_amount = $req->input('trans_amount' . $rowReqId);
					
					// cash block
						
						$cash_block = array();
						$cash_block['point_id'] = $point_id;
						$cash_block['depot_in_charge'] =  $depot_in_charge;
						$cash_block['transaction_type'] =  'debit';
						$cash_block['payment_type'] =  'CASH';
						$cash_block['trans_amount'] =  $trans_amount;
						$cash_block['trans_date'] =  date('Y-m-d H:i:s');
						$cash_block['entry_by'] =  Auth::user()->id;
						$cash_block['entry_date'] =  date('Y-m-d H:i:s');
						
						DB::table('depot_accounts_payments')->insert([$cash_block]);
						
					$point_id = '';
					$depot_in_charge = '';
					$trans_amount = '';	
				
				}
				
			}
			
			
		}
		
		return Redirect::to('/reqAllAnalysisList/')->with('success', 'Successfully Acknowledge.');
	}
	
	
	public function req_analysis_list(Request $req)
	{
		$selectedMenu    = 'Requisition Analysis';             // Required Variable
        $selectedSubMenu = 'Requisition Analysis';             // Required Variable
        $pageTitle       = 'Requisition Analysis';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		$custList = DB::table('users')
                        ->select('users.*')
						->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('users.user_type_id', '5')
                        ->where('users.is_active', 0) 
						->orderBy('users.display_name','ASC')
                        ->get();
						
		$whereCond = '';
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND p.point_division = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('cust_id') != '') 
		{
			$whereCond .= " AND u.id = '".$req->get('cust_id')."'";
		}				
						
		$resultReqList = DB::select("SELECT u.id as user_id, u.display_name, p.point_name, p.point_division, p.business_type_id, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'acknowledge'
										AND dr.is_active = 'YES'
										$whereCond
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_analysis_list', compact('selectedMenu','approved','pageTitle','resultReqList','divList','custList'));			
		
	}
	
	
	public function req_analysis_list_new(Request $req)
	{
		$selectedMenu    = 'Requisition Analysis';             // Required Variable
        $selectedSubMenu = 'Requisition Analysis';             // Required Variable
        $pageTitle       = 'Requisition Analysis';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		$catList = DB::table('eshop_product_category')->orderBy('id','asc')->get();
		$prodList = DB::table('eshop_product')->orderBy('id','asc')->get();
		$custList = DB::table('users')
                        ->select('users.*')
						->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('users.user_type_id', '5')
                        ->where('users.is_active', 0) 
						->orderBy('users.display_name','ASC')
                        ->get();
						
		$whereCond = '';
		$sel_div_id = '';
		if ($req->get('div_id') != '') 
		{
			$sel_div_id = $req->get('div_id');
			$whereCond .= " AND p.point_division = '".$req->get('div_id')."'";
		}
		
		$sel_cat_id = '';
		if ($req->get('cat_id') != '') 
		{
			$sel_cat_id = $req->get('cat_id');
			$whereCond .= " AND pcat.id = '".$req->get('cat_id')."'";
		}
		
		$sel_prod_id = '';
		if ($req->get('prod_id') != '') 
		{
			$sel_prod_id = $req->get('prod_id');
			$whereCond .= " AND prd.id = '".$req->get('prod_id')."'";
		}
		
		$sel_cust_id = '';
		if ($req->get('cust_id') != '') 
		{
			$sel_cust_id = $req->get('cust_id');
			$whereCond .= " AND u.id = '".$req->get('cust_id')."'";
		}

		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}		
						
		$resultAnalysisList = DB::select("SELECT drd.order_id, drd.order_det_id, pcat.id as cat_id, pcat.name as pro_cat_name, 
								prd.id as prod_id, prd.name as prod_name, prd.sap_code, p.point_division,
								SUM(prd.stock_qty) as ssg_stk, SUM(drd.order_qty) as req_qnty, 
								SUM(drd.approved_qty) as update_qnty, SUM(drd.order_qty - prd.stock_qty) as adjust_qty
								FROM eshop_order dr JOIN eshop_order_details drd ON dr.order_id = drd.order_id
								left JOIN users u ON u.id = dr.customer_id
								left JOIN eshop_product prd ON prd.id = drd.product_id 
								left JOIN eshop_product_category pcat ON pcat.id = drd.cat_id 
								left JOIN tbl_point p ON p.point_id = dr.point_id
								left JOIN tbl_division d ON d.div_id = p.point_division
								WHERE dr.req_status = 'approved'
								AND dr.is_active = 'YES'
								$whereCond
								GROUP BY prd.id
								ORDER BY pcat.id");
								
	
        return view('eshop::requisition/analysis/req_analysis_list', 
		compact('selectedMenu','approved','pageTitle','resultAnalysisList','divList','custList','catList','prodList','sel_div_id',
		'sel_cat_id','sel_prod_id','sel_cust_id','sel_business_type'));			
		
	}
	
	
	/* Requisition Approved */
	
	public function req_order_analysis($reqid)
	{
		$selectedMenu    = 'Home';             		// Required Variable
		$selectedSubMenu = 'Home';             		// Required Variable
		$pageTitle       = 'Requisition Details';   // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, pr.stock_qty, pr.depo as price, rdet.req_det_id, rdet.req_qnty, rdet.req_value, dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_order_analysis', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
	}
	
	
	public function req_order_analysis_new($prodid)
	{
		$selectedMenu    = 'Home';             		// Required Variable
		$selectedSubMenu = 'Home';             		// Required Variable
		$pageTitle       = 'Requisition Details';   // Page Slug Title

		$isDepot = DB::select("SELECT u.display_name as depot_in_charge, p.point_id,p.is_depot, p.point_name, pc.name AS catname, 
						pr.id AS prod_id, pr.name proname, pr.stock_qty, pr.depo as price, 
						rdet.req_det_id, rdet.req_qnty, rdet.req_value, dr.req_status,
						rdet.approved_qnty, rdet.approved_value
						FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id
						JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
						JOIN tbl_point p ON p.point_id = dr.point_id 
						JOIN eshop_product_category pc ON pc.id = rdet.cat_id
						JOIN eshop_product pr ON pr.id = rdet.product_id
						WHERE dr.req_status = 'approved'
						AND dr.is_active = 'YES'
						AND pr.id = '".$prodid."'
						ORDER BY dr.req_id DESC");

		if(sizeof($isDepot)>0)
		{
			//echo $isDepot[0]->is_depot;

			if($isDepot[0]->is_depot==1)
			{
				$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_id, p.point_name, pc.name AS catname, 
						pr.id AS prod_id, pr.name proname, pr.stock_qty, pr.depo as price, 
						rdet.req_det_id, rdet.req_qnty, rdet.req_value, dr.req_status,
						rdet.approved_qnty, rdet.approved_value
						FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id
						JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
						JOIN tbl_point p ON p.point_id = dr.point_id 
						JOIN eshop_product_category pc ON pc.id = rdet.cat_id
						JOIN eshop_product pr ON pr.id = rdet.product_id
						WHERE dr.req_status = 'approved'
						AND dr.is_active = 'YES'
						AND pr.id = '".$prodid."'
						ORDER BY dr.req_id DESC");	
			}
			else
			{
				$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_id, p.point_name, pc.name AS catname, 
							pr.id AS prod_id, pr.name proname, pr.stock_qty, pr.distri as price, 
							rdet.req_det_id, rdet.req_qnty, rdet.req_value, dr.req_status,
							rdet.approved_qnty, rdet.approved_value
							FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id
							JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
							JOIN tbl_point p ON p.point_id = dr.point_id 
							JOIN eshop_product_category pc ON pc.id = rdet.cat_id
							JOIN eshop_product pr ON pr.id = rdet.product_id
							WHERE dr.req_status = 'approved'
							AND dr.is_active = 'YES'
							AND pr.id = '".$prodid."'
							ORDER BY dr.req_id DESC");
			}
		}
						
		
        return view('eshop::requisition/analysis/req_order_analysis', compact('selectedMenu','pageTitle','resultReqPro'));
	}
	
	
	
	/* Requisition Approved New */
	
	public function req_approved(Request $req)
	{
		if($req->isMethod('post'))
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req->input('req_id'))->update(
						[
							'req_status'  => 'approved',
							'approved_by'  => Auth::user()->id,
							'approved_date'  => date('Y-m-d H:i:s')
						]
					);

			//echo '<pre/>'; print_r($req->input('req_det_id'));  exit;	
			
			foreach($req->input('req_det_id') as $reqDetId)
			{
				
				$aprvd_qnty = 'approved_qnty_' . $reqDetId;
				$apprvd_value = 'approved_value_' . $reqDetId;
				
				//echo $req->input($aprvd_qnty); exit;
				
				$act_aprvd_qnty = $req->input($aprvd_qnty);
				$act_apprvd_value = $req->input($apprvd_value);
			    
				$DepotReqDetUpd = DB::update("UPDATE depot_req_details SET approved_qnty = $act_aprvd_qnty,
																		   approved_value = $act_apprvd_value 
												WHERE req_det_id = ?", [$reqDetId]);	
			}
			
				
		}
		
		return Redirect::to('/reqAllApprovedList/')->with('success', 'Successfully Approved.');
	}
	
	
	public function req_approved_new(Request $req)
	{
		if($req->isMethod('post'))
		{
			/*
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req->input('req_id'))->update(
						[
							'req_status'  => 'approved',
							'approved_by'  => Auth::user()->id,
							'approved_date'  => date('Y-m-d H:i:s')
						]
					);
			*/		

			//echo '<pre/>'; print_r($req->input('req_det_id'));  exit;	
			
			foreach($req->input('req_det_id') as $reqDetId)
			{
				
				$aprvd_qnty = 'approved_qnty_' . $reqDetId;
				$apprvd_value = 'approved_value_' . $reqDetId;
				
				//echo $req->input($aprvd_qnty); exit;
				
				$act_aprvd_qnty = $req->input($aprvd_qnty);
				$act_apprvd_value = $req->input($apprvd_value);
			    
				$DepotReqDetUpd = DB::update("UPDATE depot_req_details SET approved_qnty = $act_aprvd_qnty,
																		   approved_value = $act_apprvd_value 
												WHERE req_det_id = ?", [$reqDetId]);	
			}
			
				
		}
		
		//return Redirect::to('/reqAllApprovedList/')->with('success', 'Successfully Approved.');
		
		return Redirect::to('/reqAllAnalysisList/')->with('success', 'Successfully Updated.');
	}
	
	
	
	/* Requisition Approved old
	public function req_approved($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'approved',
							'approved_by'  => Auth::user()->id,
							'approved_date'  => date('Y-m-d H:i:s')
						]
					);

			$DepotReqDetUpd = DB::update('UPDATE depot_req_details SET approved_qnty = req_qnty, approved_value = req_value 
										WHERE req_id = ?', [$req_id]);		
		}
		
		return Redirect::to('/reqAllApprovedList/')->with('success', 'Successfully Approved.');
	}
	*/
	
	
	
	
	
	public function req_approved_list()
	{
		$selectedMenu    = 'Requisition Approved';             // Required Variable
        $selectedSubMenu = 'Requisition Approved';             // Required Variable
        $pageTitle       = 'Requisition Approved';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'approved'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_approved_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	
	public function req_all_approved_list()
	{
		$selectedMenu    = 'Requisition Approved';             // Required Variable
        $selectedSubMenu = 'Requisition Approved';             // Required Variable
        $pageTitle       = 'Requisition Approved';            // Page Slug Title
						
		/*$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'approved'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");*/

		$resultReqList = DB::table('eshop_order')
        ->select('eshop_order.order_id','eshop_order.order_no','eshop_order.order_date','eshop_order.order_status','eshop_order.approved_by','eshop_order.approved_date','eshop_order.req_status','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code','eshop_party_list.party_id','eshop_party_list.name as partyName')
        ->join('eshop_party_list', 'eshop_order.party_id', '=', 'eshop_party_list.party_id')
        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
        ->where('eshop_order.req_status','approved')
        ->orderBY('eshop_order.order_id','DESC')
        ->get();
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/req_all_approved_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	
	public function req_open_order_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, rdet.approved_qnty, rdet.approved_value,
								 rdet.billed_qnty, rdet.billed_value,
								dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_delivery_download_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function req_bill_details_list($reqid)
    {
		$selectedMenu    = 'Home';             			// Required Variable
		$selectedSubMenu = 'Home';             			// Required Variable
		$pageTitle       = 'Requisition Details';       // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, 
								rdet.approved_qnty, rdet.approved_value, rdet.billed_qnty, rdet.billed_value,
								dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'approved'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_bill_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	/* Requisition Billed Process*/
	
	public function req_billed_process($req_id)
	{
		if($req_id){ 
					
			$DepotReqDetUpd = DB::update('UPDATE eshop_order_details SET deliverey_qty = approved_qty, delivery_value = approved_value WHERE order_id = ?', [$req_id]);				
										
					
			$orderDetai = DB::table('eshop_order_details') 
			        ->select(DB::raw('SUM(deliverey_qty) AS deliverey_qty'), DB::raw('SUM(delivery_value) AS delivery_value'))
			        ->where('order_id', $req_id)
			        ->first();
			$DepotReqUpd = DB::table('eshop_order')->where('order_id',$req_id)->update([
				'req_status'  => 'billed',
				'billed_by'  => Auth::user()->id,
				'billed_date'  => date('Y-m-d H:i:s'),
				'total_delivery_qty' => $orderDetai->deliverey_qty,
				'total_delivery_value' => $orderDetai->delivery_value,
				'approval_status'   => 'Ready for Stock',
				'status'			=> 4 //ready for stock
			]); 

			$orderDetails = DB::table('eshop_order_details') 
			        ->select('order_id','party_id', 'cat_id', DB::raw('SUM(delivery_value) AS delivery_value'),'entry_by')
			        ->where('order_id', $req_id)                        
			        ->groupBy('cat_id') 
			        ->get();


	        foreach ($orderDetails as $key => $orderDetail) {

	            $orderCommission = DB::table('eshop_categroy_wise_commission') 
	            ->select('order_id','party_id', 'cat_id','commission','entry_by')
	            ->where('order_id', $orderDetail->order_id)
	            ->where('cat_id', $orderDetail->cat_id)
	            ->first();

	            if(sizeof($orderCommission)>0){
	            	$commissionValue = ($orderDetail->delivery_value * $orderCommission->commission)/100;
	            }
	            else
	            {
	            	$commissionValue = ($orderDetail->delivery_value)/100;
	            }
	            
	            DB::table('eshop_categroy_wise_commission')
	            ->where('order_id', $orderDetail->order_id)
	            ->where('cat_id', $orderDetail->cat_id)
	            ->update(
	                [
	                    'delivery_value'              => $orderDetail->delivery_value,
	                    'delivery_commission_value'   => $commissionValue
	                ]
	            );

	        }       
	        ///////////
	        DB::commit();
			DB::rollBack();									
										
		}
		return Redirect::to('/eshop-reqAllApprovedList/')->with('success', 'Successfully Billed.');
		//return Redirect::to('/modern-reqAllBilledList/')->with('success', 'Successfully Billed.');
	}
	
	
	public function req_factory_wise_split($req_id)
	{
		if($req_id)
		{
			$reqAllFactory = DB::select("SELECT c.id as ori_factory_id, c.sap_code, c.name as ori_factory_name, c.short_code, 
										dr.* 
										FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
										JOIN eshop_product p ON rdet.product_id = p.id 
										JOIN tbl_company c ON c.sap_code = p.companyid
										WHERE rdet.req_id = '".$req_id."'  GROUP BY p.companyid");	
										
									
										
			if(sizeof($reqAllFactory)>0)
			{
				// update master requisition
				$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req_id)->update(
						[
							'child_factory_count'  => sizeof($reqAllFactory)
						]
					);	
				

				foreach($reqAllFactory as $rowAllFact)
				{
					$newReqId = $this->new_factory_master($req_id,$rowAllFact);
					
					if($newReqId)
					{
						$this->new_factory_details($req_id, $newReqId, $rowAllFact->sap_code);
					}
					
				}
		
			}	
							
		}
		
		return Redirect::to('/reqAllBilledList/')->with('success', 'Successfully Billed & Split to Factory wise.');
	}
	
	
	private function new_factory_master($req_id, $fact_master_info) 
	{
		
		if($req_id && is_object($fact_master_info))
		{
			
			$newReqId = DB::table('depot_requisition')->insertGetId(
									[ 
									  'depot_in_charge' => $fact_master_info->depot_in_charge,
									  'point_id' => $fact_master_info->point_id,
									  
									  'req_no' => $fact_master_info->req_no .  '_' . $fact_master_info->short_code,
									  'req_by' => $fact_master_info->req_by,
									  'req_date' => $fact_master_info->req_date,
									  
									  'sent_by' => $fact_master_info->sent_by,
									  'sent_date' => $fact_master_info->sent_date,
									  
									  'approved_by' => $fact_master_info->approved_by,
									  'approved_date' => $fact_master_info->approved_date,
									  
									  'billed_by' => $fact_master_info->billed_by,
									  'billed_date' => $fact_master_info->billed_date,
									  
									  'req_status' => 'billed',
									  'is_active' => 'YES',
									  
									  'parent_req_id' => $fact_master_info->req_id,
									  'parent_req_no' => $fact_master_info->req_no,

									  'factory_id' => $fact_master_info->ori_factory_id,
									  'factory_sap_code' => $fact_master_info->sap_code,

									  'factory_name' => $fact_master_info->ori_factory_name, 	
									  'factory_total_item' => 0,									  
									
									]
					);
					
			
			return $newReqId;
			
		}	
	}
	
	
	private function new_factory_details($req_id, $newReqId, $fact_code)
	{
		if($req_id && $newReqId && $fact_code)
		{
			
			$reqFactItem = DB::select("SELECT  rdet.*  
										FROM depot_req_details rdet JOIN eshop_product p ON p.id = rdet.product_id
										JOIN tbl_company c ON c.sap_code = p.companyid
										WHERE rdet.req_id = '".$req_id."' and c.sap_code = '".$fact_code."'");	
			
			if(sizeof($reqFactItem)>0)
			{
				foreach($reqFactItem as $rowFactItem)
				{
					$req_item_data = array();
					$req_item_data['req_id'] = $newReqId; 
					$req_item_data['product_id'] =  $rowFactItem->product_id;
					$req_item_data['cat_id'] =  $rowFactItem->cat_id;
					$req_item_data['req_qnty'] =  $rowFactItem->req_qnty;
					$req_item_data['req_value'] = $rowFactItem->req_value;
					$req_item_data['approved_qnty'] =  $rowFactItem->approved_qnty;
					$req_item_data['approved_value'] =  $rowFactItem->approved_value;
					$req_item_data['billed_qnty'] =  $rowFactItem->billed_qnty;
					$req_item_data['billed_value'] =  $rowFactItem->billed_value;
			
					DB::table('depot_req_details')->insert([$req_item_data]);
				}
				
			}
		}	
			
	}
	
	
	
	private function party_lifting($req_id)
	{
		if($req_id)
		{
			
			$partyLiftingData= DB::select("SELECT  dr.*, u.sap_code, p.is_depot, SUM(rdet.billed_value) as Lifting
										FROM depot_requisition dr JOIN depot_req_details rdet ON dr.req_id = rdet.req_id
										JOIN users u ON u.id = dr.depot_in_charge
										JOIN tbl_point p on p.point_id = dr.point_id
										WHERE dr.req_id = '".$req_id."'");	
			
			if(sizeof($partyLiftingData)>0)
			{
				$partyLedgerData= DB::select("SELECT pldg.*
										FROM tbl_party_ledger pldg 
										WHERE 
										pldg.party_id = '".$partyLiftingData[0]->depot_in_charge."'
										AND pldg.point_id = '".$partyLiftingData[0]->point_id."'
										AND date_format(pldg.ledger_date_time,'%Y-%m-%d') = '".date('Y-m-d')."'");
										
										
				if(sizeof($partyLedgerData)>0) // Entry exist
				{
					
					$liftingToatl = $partyLedgerData[0]->party_lifting_total + $partyLiftingData[0]->Lifting;
					$closing_balance_total = $partyLedgerData[0]->closing_balance_total + $partyLiftingData[0]->Lifting;
					$actual_closing_balance = $partyLedgerData[0]->actual_closing_balance + $partyLiftingData[0]->Lifting;
					
					$partyLedgerUpd = DB::table('tbl_party_ledger')->where('ledger_id',$partyLedgerData[0]->ledger_id)->update(
						[
						   
							'ledger_date_time'      => 	date('Y-m-d'),
							'party_lifting_total'   => 	$liftingToatl,
							'closing_balance_total' =>  $closing_balance_total,		
							'actual_closing_balance' => $actual_closing_balance,
							'today_lifting_count'    => $partyLedgerData[0]->today_lifting_count + 1,
						]
					); 
					
				} else { // New Ledger 

					$party_ledger_data = array();
					$party_ledger_data['ledger_date_time'] =  date('Y-m-d H:i:s');
					$party_ledger_data['party_id'] =  $partyLiftingData[0]->depot_in_charge;
					$party_ledger_data['party_sap_code'] =  $partyLiftingData[0]->sap_code;
					$party_ledger_data['point_id'] = $partyLiftingData[0]->point_id;
					$party_ledger_data['party_type'] =  ($partyLiftingData[0]->is_depot==1)?'depot':'dist';
					
					$party_ledger_data['party_opening_balance'] =  $this->GetPartyOpeningBalance($partyLiftingData[0]->depot_in_charge,
																								$partyLiftingData[0]->point_id);
					$party_ledger_data['party_lifting_total'] 	=  $partyLiftingData[0]->Lifting;
				
					$party_ledger_data['closing_balance_total'] =  $party_ledger_data['party_opening_balance'] 
																	+ $party_ledger_data['party_lifting_total'];
					$party_ledger_data['actual_closing_balance'] =  $party_ledger_data['closing_balance_total'];
					
					$party_ledger_data['today_lifting_count'] = 1;
			
					DB::table('tbl_party_ledger')->insert([$party_ledger_data]);
				}				
										
										
			}
			
		}	
			
	}
	
	
	private function GetPartyOpeningBalance($party_id, $point_id)
	{
		$party_opening_balance = 0;
		
		if($party_id && $point_id)
		{
			$partyLedgerData= DB::select("SELECT  pldg.actual_closing_balance
										FROM tbl_party_ledger pldg 
										WHERE 
										pldg.party_id = '".$party_id."'
										AND 
										pldg.point_id = '".$point_id."'
										ORDER BY ledger_id DESC LIMIT 1
										");	


			if(sizeof($partyLedgerData))
			{
				$party_opening_balance = $partyLedgerData[0]->actual_closing_balance;
			} 
										
		}
		
		return $party_opening_balance;
		
	}
	
	
	/* Requisition Billed List*/
	
	public function req_billed_list()
	{
		$selectedMenu    = 'Requisition Billed List';             // Required Variable
        $selectedSubMenu = 'Requisition Billed List';             // Required Variable
        $pageTitle       = 'Requisition Billed List';            // Page Slug Title
						
		$resultBilledList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'billed'
										AND dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultBilledList); exit;								

        return view('eshop::requisition/req_billed_list', compact('selectedMenu','approved','pageTitle','resultBilledList'));			
		
	}
	
	public function req_all_billed_list()
	{
		$selectedMenu    = 'Requisition All Billed';             // Required Variable
        $selectedSubMenu = 'Requisition All Billed';             // Required Variable
        $pageTitle       = 'Requisition All Billed';            // Page Slug Title
						
		/*$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'billed'
										AND dr.is_active = 'YES'
										AND dr.is_download = 'NO'
										AND dr.parent_req_id = 0
										ORDER BY dr.req_id desc");*/

		$resultReqList = DB::table('eshop_order')
        ->select('eshop_order.order_id','eshop_order.order_no','eshop_order.order_date','eshop_order.order_status','eshop_order.approved_by','eshop_order.approved_date','eshop_order.req_status','eshop_order.is_download','eshop_order.billed_by','eshop_order.billed_date','eshop_customer_list.name','eshop_customer_list.customer_id','eshop_customer_list.address','eshop_customer_list.route_id','eshop_customer_list.sap_code','eshop_party_list.party_id','eshop_party_list.name as partyName')
        ->join('eshop_party_list', 'eshop_order.party_id', '=', 'eshop_party_list.party_id')
        ->join('eshop_customer_list', 'eshop_order.customer_id', '=', 'eshop_customer_list.customer_id') 
        ->where('eshop_order.req_status','billed')
        ->where('eshop_order.is_download','NO')
        ->orderBY('eshop_order.order_id','DESC')
        ->get();
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/req_all_billed_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
												/* Factory Delivery Part*/
												
	
	public function req_ready_for_delivery()
	{
		$selectedMenu    = 'Ready For Delivery';             // Required Variable
        $selectedSubMenu = 'Ready For Delivery';             // Required Variable
        $pageTitle       = 'Ready For Delivery';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE (dr.req_status = 'billed' OR dr.req_status = 'partial_received')
										AND dr.factory_sap_code = '".Auth::user()->sap_code."'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/factory.req_ready_delivery_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_delivery_item_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

								
		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, u1.display_name as delivered_by,  
								u2.display_name as approved_by, u3.display_name as billed_by,  ud.cell_phone as delMobNo,
								p.point_name, pc.name AS catname, pc.id as cat_id,
								pr.id pro_id, pr.name proname, pr.depo price,  SUM(rdet.billed_qnty) as billed_qnty,
								SUM(rdet.billed_value) as billed_value, 
								rdet.req_det_id, SUM(rdet.delevered_qnty) as delevered_qnty, SUM(rdet.delevered_value) as delevered_value, 	
								(SUM(rdet.billed_qnty) - SUM(rdet.delevered_qnty)) as rem_delvd_qnty,  
								(SUM(rdet.billed_value) - SUM(rdet.delevered_value)) as rem_delvd_value,
								dr.req_status, dr.req_no, dr.req_date,  
								dr.approved_date, dr.billed_date, dr.delivered_date, dr.partial_delivery_count
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
							LEFT JOIN users u1 ON u1.id = dr.delivered_by
							LEFT JOIN users u2 ON u2.id = dr.approved_by
							LEFT JOIN users u3 ON u3.id = dr.billed_by
							LEFT JOIN tbl_user_details ud ON ud.user_id = u3.id
								WHERE dr.req_id = '".$reqid."'
								AND (dr.req_status = 'billed' OR dr.req_status = 'partial_received')
								AND dr.is_active = 'YES'
								GROUP BY pr.id
								ORDER BY dr.req_id DESC");							
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/factory.req_delivery_item_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	
	/* Requisition Delivery */
	
	
		//3rd version
	public function req_deliver_process(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			
			$reqid = $req->input('reqid');
			$delivery_chalan_no = $req->input('delivery_chalan_no');
			$partial_count = $req->input('partial_delivery_count') + 1;
			
			if($req->input('partial_delivery_count') == 0)
			{	
				
				foreach($req->input('req_det_id') as $req_det_id)
				{
					$delevered_qnty = "delevered_qnty_" . $req_det_id;
					$delevered_value = "delevered_value_" . $req_det_id;
					
					$delvd_qnty = $req->input("$delevered_qnty");
					$delvd_value = $req->input("$delevered_value");
					
					$DepotReqDetUpd = DB::update("UPDATE depot_req_details SET delivery_chalan_no = '".$delivery_chalan_no."', 
																			   delevered_qnty = '".$delvd_qnty."',
																			   delevered_value = '".$delvd_value."',
																			   item_delivered_date = '".date('Y-m-d H:i:s')."',
																			   partial_track = '".$partial_count."'
											WHERE req_det_id = ?", [$req_det_id]);	
				
				}		 
		
			} elseif($req->input('partial_delivery_count') > 0) {
				
				foreach($req->input('req_det_id') as $req_det_id)
				{
					
					$pro_id = "pro_id_" . $req_det_id;
					$cat_id = "cat_id_" . $req_det_id;
					
					$product_id = $req->input("$pro_id");
					$catagory_id = $req->input("$cat_id");
					
					$delevered_qnty = "delevered_qnty_" . $req_det_id;
					$delevered_value = "delevered_value_" . $req_det_id;
					
					$delvd_qnty = $req->input("$delevered_qnty");
					$delvd_value = $req->input("$delevered_value");
					
								
					$req_det_data['req_id'] = $reqid;   
					$req_det_data['product_id'] =  $product_id;
					$req_det_data['cat_id'] =   $catagory_id;
					$req_det_data['req_qnty'] =  0;
					$req_det_data['req_value'] =  0;
					$req_det_data['approved_qnty'] =  0;
					$req_det_data['approved_value'] =  0;
					$req_det_data['delivery_chalan_no'] =  $delivery_chalan_no;
					$req_det_data['delevered_qnty'] =  $delvd_qnty;
					$req_det_data['delevered_value'] =  $delvd_value;
					$req_det_data['received_qnty'] =  0;
					$req_det_data['received_value'] =  0;
					$req_det_data['item_delivered_date'] =  date('Y-m-d H:i:s');
					$req_det_data['partial_track'] =  $partial_count;
					
					DB::table('depot_req_details')->insert([$req_det_data]);
				
				}
				
			}
			
			// master update and stcok as well
			
			$ReqStatusQuery = DB::select("SELECT product_id, SUM(billed_qnty), SUM(delevered_qnty)  FROM depot_req_details 
											WHERE req_id = '".$reqid."' 
											GROUP BY product_id 
											HAVING SUM(billed_qnty) > SUM(delevered_qnty)");
		
			if(sizeof($ReqStatusQuery)>0)
			{
				$req_status = 'partial_delivered';
				$URL = '/reqAllDeliveredList/';
				$msg = 'Pertial Delivered Successfully';
			} else {
				$req_status = 'delivered';
				$URL = '/reqAllDeliveredList/';
				$msg = 'Full Delivered Successfully';
			}
				
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$reqid)->update(
					[
						'req_status'  => $req_status,
						'delivered_by'  => Auth::user()->id,
						'delivered_date'  => date('Y-m-d H:i:s'),
						'partial_delivery_count'  => $partial_count
					]
				); 
					
			
			//$this->req_receive_stock($reqid, $partial_count);		
			
			
		}
		
		return Redirect::to("$URL")->with('success', "$msg.");
	}
	
	
	
	
	public function req_all_delivered_list()
	{
		$selectedMenu    = 'Requisition All Delivered';             // Required Variable
        $selectedSubMenu = 'Requisition All Delivered';             // Required Variable
        $pageTitle       = 'Requisition All Delivered';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'delivered' OR dr.req_status = 'partial_delivered'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/factory.req_all_delivered_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Canceled */
	public function req_canceled($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'canceled',
							'canceled_by'  => Auth::user()->id,
							'canceled_date'  => date('Y-m-d H:i:s')
						]
					); 
		}
		
		return Redirect::to('/reqAllCanceledList/')->with('success', 'Successfully Canceled.');
	}
	
	
	public function req_canceled_list()
	{
		$selectedMenu    = 'Requisition Canceled';             // Required Variable
        $selectedSubMenu = 'Requisition Canceled';             // Required Variable
        $pageTitle       = 'Requisition Canceled';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'canceled'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_canceled_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_all_canceled_list()
	{
		$selectedMenu    = 'Requisition Canceled';             // Required Variable
        $selectedSubMenu = 'Requisition Canceled';             // Required Variable
        $pageTitle       = 'Requisition Canceled';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'canceled'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/req_all_canceled_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Received By Depot/Distributor */
	//1st version
	/*
	public function req_receive($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'received',
							'received_by'  => Auth::user()->id,
							'received_date'  => date('Y-m-d H:i:s')
						]
					); 
					
			$DepotReqDetUpd = DB::update('UPDATE depot_req_details SET received_qnty = delevered_qnty, received_value = delevered_value 
										WHERE req_id = ?', [$req_id]);	


			$this->req_receive_stock($req_id);								
		}
		
		return Redirect::to('/reqReceivedList/')->with('success', 'Successfully Received.');
	}
	*/
	
	/* //2nd version
	public function req_receive(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$req->input('reqid'))->update(
						[
							'req_status'  => 'received',
							'received_by'  => Auth::user()->id,
							'received_note'  => $req->input('received_note'),
							'grn_no'  => $req->input('grn_no'),
							'received_date'  => date('Y-m-d',strtotime($req->input('received_date')))
						]
					); 
					
			$DepotReqDetUpd = DB::update('UPDATE depot_req_details SET received_qnty = delevered_qnty, received_value = delevered_value 
										WHERE req_id = ?', [$req->input('reqid')]);	


			$this->req_receive_stock($req->input('reqid'));								
		}
		
		return Redirect::to('/reqReceivedList/')->with('success', 'Successfully Received.');
	}
	*/
	
									
									/*  Depot Received Begin */
	
	public function req_delivered_list()
	{
		$selectedMenu    = 'Requisition Delivered';             // Required Variable
        $selectedSubMenu = 'Requisition Delivered';             // Required Variable
        $pageTitle       = 'Requisition Delivered';            // Page Slug Title
						
		//echo Auth::user()->id; exit;
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND (dr.req_status = 'partial_delivered' OR dr.req_status = 'delivered')
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_delivered_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}								
	
	
	
	
	//3rd version
	public function req_receive(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$msg = 'Operation Ignored';
			$reqid = $req->input('reqid');
			$partial_delivery_count = $req->input('partial_delivery_count');
					
			foreach($req->input('req_det_id') as $req_det_id)
			{
				$received_qnty = "received_qnty_" . $req_det_id;
				$recvd_value = "received_value_" . $req_det_id;
				
				$recvd_qnty = $req->input("$received_qnty");
				$recvd_value = $req->input("$recvd_value");
				
				$DepotReqDetUpd = DB::update("UPDATE depot_req_details SET received_qnty = '".$recvd_qnty."', 
																		   received_value = '".$recvd_value."',
																		   item_received_date = '".date('Y-m-d H:i:s')."'
										WHERE req_det_id = ? AND partial_track = ?", [$req_det_id,$partial_delivery_count]);	
			
			}


			$RecvdStatusQuery = DB::select("SELECT product_id, SUM(billed_qnty), SUM(received_qnty)  FROM depot_req_details 
											WHERE req_id = '".$reqid."' 
											GROUP BY product_id 
											HAVING SUM(billed_qnty) > SUM(received_qnty)");
		
			if(sizeof($RecvdStatusQuery)>0)
			{
				$req_status = 'partial_received';
				$received_note  = 'partial receuved';
				$grn_no  = '';
				$URL = '/reqReceivedList/';
				$msg = 'Pertial Received Successfully';
			} else {
				$req_status = 'received';
				$received_note  = $req->input('received_note');
				$grn_no  = $req->input('grn_no');
				$URL = '/reqReceivedList/';
				$msg = 'Full Received Successfully';
			}	
			
			
			$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$reqid)->update(
						[
							'req_status'  => $req_status,
							'partial_delivery_count'  => $partial_delivery_count,
							'received_by'  => Auth::user()->id,
							'received_note'  => $received_note,
							'grn_no'  => $grn_no,
							'received_date'  => date('Y-m-d',strtotime($req->input('received_date')))
						]
			); 
		
			$this->req_receive_stock($reqid, $partial_delivery_count);								
		}
		
		return Redirect::to("$URL")->with('success', "$msg.");
	}
	
	
	// Stock Operation
	private function req_receive_stock($req_id, $partial_track)
	{
		
		if($req_id && $partial_track)
		{
			
			$resultReqData = DB::select("SELECT dr.*,  rdet.product_id, rdet.cat_id, rdet.received_qnty, rdet.received_value
								FROM depot_requisition dr JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								WHERE dr.req_id = '".$req_id."'
								AND dr.partial_delivery_count = '".$partial_track."'
								AND (dr.req_status = 'received' OR dr.req_status = 'partial_received')
								AND dr.is_active = 'YES'
								AND rdet.partial_track = '".$partial_track."'  
								ORDER BY dr.req_id DESC");				
					
			foreach($resultReqData as $RowReqData)
			{
				DB::table('depot_inventory')->insert(
                        [
                            'req_id'          		=> $RowReqData->req_id,
                            'req_no'          		=> $RowReqData->req_no,
                            'point_id'          	=> $RowReqData->point_id,
                            'depot_in_charge'      	=> $RowReqData->depot_in_charge,
                            'cat_id'        		=> $RowReqData->cat_id,
                            'product_id'         	=> $RowReqData->product_id,
                            'product_qty'       	=> $RowReqData->received_qnty,
                            'product_value'       	=> $RowReqData->received_value,
                            'inventory_date'       	=> date('Y-m-d'),
                            'inventory_type'       	=> 1, 			// stock-in
                            'global_company_id'     => Auth::user()->global_company_id,
                            'created_by'       		=> Auth::user()->id,
                        ]
                ); 
				 
				$SSG_STOCK = DB::update("UPDATE eshop_product SET stock_qty = stock_qty - $RowReqData->received_qnty,
													mkt_stock = mkt_stock - $RowReqData->received_qnty
													WHERE id = ? AND category_id = ?",
													[$RowReqData->product_id, $RowReqData->cat_id]
												);	 
				 
				$chkStcok = DB::select("SELECT * 
									FROM depot_stock 
									WHERE	point_id = '".$RowReqData->point_id."'
									AND 	product_id = '".$RowReqData->product_id."'
									AND 	cat_id = '".$RowReqData->cat_id."'
								");
								
				
				//echo '<pre/>'; print_r($chkStcok); exit;				
								
				
				if(sizeof($chkStcok) > 0)
				{
					
					$DepotReqDetUpd = DB::update("UPDATE depot_stock SET stock_qty = stock_qty + $RowReqData->received_qnty 
													WHERE point_id = ? AND product_id = ? AND cat_id = ?",
													[$RowReqData->point_id, $RowReqData->product_id, $RowReqData->cat_id]
												);	


				} 
				else 
				{
					
					DB::table('depot_stock')->insert(
                        [
                            'point_id'          	=> $RowReqData->point_id,
                            'product_id'          	=> $RowReqData->product_id,
                            'cat_id'          		=> $RowReqData->cat_id,
                            'stock_qty'      		=> $RowReqData->received_qnty,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'created_by'         	=> Auth::user()->id,
                          
                        ]
					);
				}
			}		
		} 
	}
	
	
	public function req_received_list()
	{
		$selectedMenu    = 'Requisition Received';             // Required Variable
        $selectedSubMenu = 'Requisition Received';             // Required Variable
        $pageTitle       = 'Requisition Received';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.depot_in_charge = '".Auth::user()->id."'
										AND (dr.req_status = 'partial_received' OR dr.req_status = 'received')
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('eshop::requisition/req_received_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_all_received_list()
	{
		$selectedMenu    = 'Requisition Received';             // Required Variable
        $selectedSubMenu = 'Requisition Received';             // Required Variable
        $pageTitle       = 'Requisition Received';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'received'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('eshop::requisition/factory.req_all_received_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	public function req_approved_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, rdet.approved_qnty, rdet.approved_value, dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'approved'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_approved_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	public function req_delivered_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, 
								rdet.approved_qnty, rdet.approved_value, rdet.delevered_qnty, rdet.delevered_value,
								dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND (dr.req_status = 'delivered' OR dr.req_status = 'partial_delivered')
								AND dr.partial_delivery_count = rdet.partial_track 
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/factory.req_delivered_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function req_received_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, rdet.approved_qnty, rdet.approved_value,
								rdet.received_qnty, rdet.received_value, dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND (dr.req_status = 'partial_received' OR dr.req_status = 'received')
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_received_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	public function req_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, dr.req_status
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function req_delivery_challan($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, u1.display_name as delivered_by,  
								u2.display_name as approved_by, u3.display_name as received_by,  ud.cell_phone as delMobNo,
								p.point_name, pc.name AS catname, 
								pr.name proname, rdet.delevered_qnty, rdet.delevered_value, 
								dr.req_status, dr.req_no, dr.req_date, dr.delivered_date, 
								dr.approved_date, dr.received_date 
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
							LEFT JOIN users u1 ON u1.id = dr.delivered_by
							LEFT JOIN users u2 ON u2.id = dr.approved_by
							LEFT JOIN users u3 ON u3.id = dr.received_by
							LEFT JOIN tbl_user_details ud ON ud.user_id = u3.id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_delivery_challan', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function req_delivery_received_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as depot_in_charge, u1.display_name as delivered_by,  
								u2.display_name as approved_by, u3.display_name as received_by,  ud.cell_phone as delMobNo,
								p.point_name, pc.name AS catname, 
								pr.name proname, pr.depo price, rdet.req_det_id, rdet.delivery_chalan_no, rdet.delevered_qnty, rdet.delevered_value, 
								dr.req_status, dr.req_no, dr.req_date, dr.delivered_date, 
								dr.approved_date, dr.received_date, dr.partial_delivery_count 
								FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
								JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN eshop_product_category pc ON pc.id = rdet.cat_id
								JOIN eshop_product pr ON pr.id = rdet.product_id
								
							LEFT JOIN users u1 ON u1.id = dr.delivered_by
							LEFT JOIN users u2 ON u2.id = dr.approved_by
							LEFT JOIN users u3 ON u3.id = dr.received_by
							LEFT JOIN tbl_user_details ud ON ud.user_id = u3.id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status in ('partial_delivered','delivered') 
								AND dr.partial_delivery_count = rdet.partial_track
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");							
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('eshop::requisition/req_delivery_receive_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	
	
	
	public function stock_export(Request $Request) 
	{

		$catID = $Request->get('cat_id');

        $point=DB::table('tbl_user_business_scope')
        ->select('point_id','division_id')
        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
        ->first();

        $pointID = '';
        if(sizeof($point)>0)
        {
            $pointID = $point->point_id;
        }
	
		
		if($catID!='all')
		{
			$stockResult = DB::select("SELECT p.name as ProductName, ds.stock_qty as Stock, (ds.stock_qty * p.depo) as Value
			FROM depot_stock ds JOIN eshop_product p ON ds.product_id = p.id 
			WHERE ds.point_id = '".$pointID."' AND ds.cat_id = '".$catID."' ORDER BY p.name ASC");
		
		} else {
			
			$stockResult = DB::select("SELECT p.name as ProductName, ds.stock_qty as Stock, (ds.stock_qty * p.depo) as Value
			FROM depot_stock ds JOIN eshop_product p ON ds.product_id = p.id 
			WHERE ds.point_id = '".$pointID."' ORDER BY p.name ASC");
			
		}
		

		$data = array();
		foreach ($stockResult as $items) {
			$data[] = (array)$items;  
		}

		//$items = Item::all();
		Excel::create('Download_Stock', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');

	}
	
	
	/* Sharif Begin */
	
	public function sales_order_export(Request $Request) 
	{

		$reqID=$Request->get('requisition_id');

		$items  = DB::table('depot_requisition')
		->select('users.display_name as Depot Name','tbl_point.point_name AS Point Name','eshop_product.companyid AS Company Code',
		'eshop_product.sap_code AS Material Code','eshop_product_category.name AS Category','eshop_product.name AS Material Name',
		'depot_req_details.req_qnty AS Qty','depot_req_details.req_value AS Value',
		'depot_req_details.billed_qnty AS BilledQty','depot_req_details.billed_value AS BilledValue')
		->join('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
		->join('depot_req_details', 'depot_req_details.req_id', '=', 'depot_requisition.req_id')
		->join('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
		->join('eshop_product_category', 'eshop_product_category.id', '=', 'depot_req_details.cat_id')
		->join('eshop_product', 'eshop_product.id', '=', 'depot_req_details.product_id')
		->where('depot_requisition.req_id', $reqID)
		->orderBy('depot_requisition.req_id','DESC')                    
		->get();

		$data = array();
		foreach ($items as $items) {
			$data[] = (array)$items;  
		}

//$items = Item::all();
		Excel::create('Download_Sales_ORDER', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');

	}
	
	
	public function upload_customar_balance()
	{
        $selectedMenu    = 'Upload Balance';          	// Required Variable for menu
        $selectedSubMenu = 'Customar Balance Upload';   // Required Variable for menu
        $pageTitle       = 'Customar Balance Upload'; 	// Page Slug Title

        
        $CustBalanceList  = DB::table('tbl_party_sap_balance')
        ->select('*') 
        ->orderBy('tbl_party_sap_balance.balance_id','DESC')                    
        ->get();
		

        return view('Depot/cust_balance_list' , compact('selectedMenu','selectedSubMenu','pageTitle','CustBalanceList'));  
      
    }
	
	
	public function custBalanceUploadProcess(Request $request)
    {

    	if($request->hasFile('imported-file'))
    	{

    		$path = $request->file('imported-file')->getRealPath();


    		$data = Excel::load($path, function($reader) {})->get();


    		if(!empty($data) && $data->count())
			{


    			$data = $data->toArray();
				
				/*
				echo '<pre/>';
				print_r($data); exit;
				*/
				
    			for($i=0;$i<count($data);$i++)
    			{
    				//$date = date('Y-m-d', strtotime($data[$i]['date'])); // sap_date

    				$insert[] = ['cust_code' => $data[$i]['cust.code'], 'cust_balance' => $data[$i]['balance']];
    			
					$this->sap_adjustment($data[$i]['cust.code'], $data[$i]['balance']);
				
				}
				
				/*
				echo '<pre/>';
				print_r($insert); exit;
				*/
				
				return back()->with('success','SAP Adjustment Succesfull.');
				 
    		} else {
				
				return back()->with('error','Please Check your file, Something is wrong there.');	
				
			}


    	}
	
    }
	
	
	private function sap_adjustment($party_code,$sap_closing_balance)
	{
		if($party_code && $sap_closing_balance)
		{
			
			$partyData= DB::select("SELECT u.*, p.point_id, p.is_depot FROM tbl_user_business_scope ubs 
										JOIN tbl_point p ON ubs.point_id = p.point_id
										JOIN users u ON u.id = ubs.user_id  
										WHERE u.sap_code = '".$party_code."' 
										AND u.user_type_id = 5
										AND ubs.is_active = 0
										");	
			
			if(sizeof($partyData)>0)
			{
				$partyLedgerData= DB::select("SELECT pldg.*
										FROM tbl_party_ledger pldg 
										WHERE 
										pldg.party_id = '".$partyData[0]->id."'
										AND pldg.point_id = '".$partyData[0]->point_id."'
										AND date_format(pldg.ledger_date_time,'%Y-%m-%d') = '".date('Y-m-d')."'");
										
										
				if(sizeof($partyLedgerData)>0) // Entry exist
				{
					
					$party_adjustment = $sap_closing_balance - $partyLedgerData[0]->closing_balance_total;
					$actual_closing_balance = $partyLedgerData[0]->closing_balance_total + $party_adjustment;
					
					$partyLedgerUpd = DB::table('tbl_party_ledger')->where('ledger_id',$partyLedgerData[0]->ledger_id)->update(
						[
						   
							'ledger_date_time'      	=> date('Y-m-d'),
							'sap_closing_balance'   	=> $sap_closing_balance,
							'party_adjustment' 			=> $party_adjustment,		
							'adjustment_type' 			=> ($party_adjustment>1)?'UP':'DOWN',
							'actual_closing_balance'    => $actual_closing_balance,
							'today_sap_adjust_count'    => $partyLedgerData[0]->today_sap_adjust_count + 1,
						]
					); 
					
				} else { // New Ledger 

					$party_ledger_data = array();
					
					$party_ledger_data['ledger_date_time'] =  date('Y-m-d H:i:s');
					
					$party_ledger_data['party_id'] =  $partyData[0]->id;
					$party_ledger_data['party_sap_code'] =  $partyData[0]->sap_code;
					$party_ledger_data['point_id'] = $partyData[0]->point_id;
					$party_ledger_data['party_type'] =  ($partyData[0]->is_depot==1)?'depot':'dist';
					
					$party_ledger_data['party_opening_balance'] =  $this->GetPartyOpeningBalance($partyData[0]->id,
																								$partyData[0]->point_id);
																								
					$party_ledger_data['closing_balance_total'] 	=  $party_ledger_data['party_opening_balance'];
					
					$party_ledger_data['sap_closing_balance'] 	=  $sap_closing_balance;
				
					$party_ledger_data['party_adjustment'] =  $sap_closing_balance - $party_ledger_data['closing_balance_total'];  // 0 because new entry 
					
					$party_ledger_data['adjustment_type'] =  ($sap_closing_balance>1)?'UP':'DOWN';
					
					$party_ledger_data['actual_closing_balance'] =  $party_ledger_data['closing_balance_total'] + $party_ledger_data['party_adjustment'];
					
					$party_ledger_data['today_sap_adjust_count'] = 1;
			
					DB::table('tbl_party_ledger')->insert([$party_ledger_data]);
				}				
										
										
			}
			
		}	
			
	}


	public function upload_stock_list()
	{
        $selectedMenu    = 'Upload Stock';                    // Required Variable for menu
        $selectedSubMenu = 'Products Stock List';           // Required Variable for menu
        $pageTitle       = 'Products Stock List'; // Page Slug Title

        
        $stockList  = DB::table('tbl_sap_stock')
        ->select('dDate','company_code','company_name','plant','material_no','material_desc','stock_qty') 
        ->where('tbl_sap_stock.is_active',1)
        ->orderBy('tbl_sap_stock.iId','DESC')                    
        ->get();
		

        return view('Depot/depot_stock_list' , compact('selectedMenu','selectedSubMenu','pageTitle','stockList'));  

        
    }

    
	public function stockProductsUpload(Request $request)
    {

    	if($request->hasFile('imported-file'))
    	{

    		$path = $request->file('imported-file')->getRealPath();


    		$data = Excel::load($path, function($reader) {})->get();


    		if(!empty($data) && $data->count()){


    			$data = $data->toArray();
				
    			for($i=0;$i<count($data);$i++)
    			{
    				$date = date('Y-m-d', strtotime($data[$i]['date'])); // sap_date

    				$insert[] = ['dDate' => $date,'company_code' =>$data[$i]['comp._code'], 'company_name' => $data[$i]['comp._name'], 'plant' => $data[$i]['plant'], 'material_no' => $data[$i]['material_no'], 'material_desc' => $data[$i]['material_description'], 'stock_qty' => $data[$i]['stock'],'global_company_id' =>Auth::user()->global_company_id,'created_by' => Auth::user()->id];
    			}

    			if(!empty($insert)){
					
					//DB::table('tbl_sap_stock')->delete();

    				DB::table('tbl_sap_stock')->where('is_active',1)->update(
						[
							'is_active'  => 2,
							'updated_at'  => date('Y-m-d H:i:s')
						]
					); 

    				ProductsStockUploadModel::insert($insert);
				
					DB::select("UPDATE eshop_product JOIN tbl_sap_stock ON tbl_sap_stock.material_no = eshop_product.sap_code
					SET eshop_product.stock_qty = tbl_sap_stock.stock_qty WHERE tbl_sap_stock.is_active=1");
					
				  return back()->with('success','Products Stock upload sucessfully.');
				
				}
    				
				 
    		}


    	}
		
    	return back()->with('error','Please Check your file, Something is wrong there.');

    }
	
	public function depot_items_edit(Request $request)
    {
        
        $depotResult  = DB::table('depot_req_details')
                        ->select('depot_req_details.req_det_id','depot_req_details.product_id','depot_req_details.req_qnty','eshop_product.depo','eshop_product.name')
                        ->join('eshop_product', 'eshop_product.id', '=', 'depot_req_details.product_id')
                        ->where('depot_req_details.req_det_id', $request->get('id'))
                        ->first();


        return view('eshop::requisition/editReqItems', compact('depotResult'));
    }

    public function depot_items_edit_submit(Request $request)
    {
        
        $price          = $request->get('items_qty') * $request->get('items_price');

        DB::table('depot_req_details')->where('req_det_id',$request->get('id'))->update(
            [
                'req_qnty'         => $request->get('items_qty'),
                'req_value'        => $price
            ]
        ); 

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');
    }


    public function depot_req_items_delete(Request $request)
    {
       $id = $request->get('id');

        $itemsDelete = DB::table('depot_req_details')->where('req_det_id',$id)->delete();
        
       	return Redirect::back()->with('success', 'Successfully Delete Requisition Product.');
             
    }
	
	
	
	public function pending_order_summary(Request $req)
	{
		$selectedMenu    = 'Pending Order Summary';             // Required Variable
        $selectedSubMenu = 'Pending Order Summary';             // Required Variable
        $pageTitle       = 'Pending Order Summary';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		$catList = DB::table('eshop_product_category')->orderBy('id','asc')->get();
		$prodList = DB::table('eshop_product')->orderBy('id','asc')->get();
		$custList = DB::table('users')
                        ->select('users.*')
						->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('users.user_type_id', '5')
                        ->where('users.is_active', 0) 
						->orderBy('users.display_name','ASC')
                        ->get();
						
						 
		
		$whereCond = '';
		$sel_from_ReqDate = '';
		if ($req->get('fromReqDate') != '') 
		{
			$sel_from_ReqDate = $req->get('fromReqDate');
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND req.req_date >= '".$fromDate."' ";
		}
		
		$sel_to_ReqDate = '';
		if ($req->get('toReqDate') != '') 
		{
			$sel_to_ReqDate = $req->get('toReqDate');
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND req.req_date <= '".$toDate."' ";
		}
		
		$sel_div_id = '';
		if ($req->get('div_id') != '') 
		{
			$sel_div_id = $req->get('div_id');
			$whereCond .= " AND p.point_division = '".$req->get('div_id')."'";
		}
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		$sel_cat_id = '';
		if ($req->get('cat_id') != '') 
		{
			$sel_cat_id = $req->get('cat_id');
			$whereCond .= " AND pc.id = '".$req->get('cat_id')."'";
		}
		
		$sel_prod_id = '';
		if ($req->get('prod_id') != '') 
		{
			$sel_prod_id = $req->get('prod_id');
			$whereCond .= " AND prd.id = '".$req->get('prod_id')."'";
		}
		
		$sel_cust_id = '';
		if ($req->get('cust_id') != '') 
		{
			$sel_cust_id = $req->get('cust_id');
			$whereCond .= " AND u.id = '".$req->get('cust_id')."'";
		}
		
		
		
		$sel_qnty_fillup = '';
		if ($req->get('qnty_fillup') != '') 
		{
			$sel_qnty_fillup = $req->get('qnty_fillup');
			$qnty_fillup = $req->get('qnty_fillup');
		}
		
					
		$resultPenOrdSummary = DB::select("SELECT req.req_id, req.req_no, req.req_date, 
									prd.id as prod_id, prd.name as pro_name, prd.depo as depo_price, 
									prd.sap_code as sap_code, prd.stock_qty as stock_qty, 
									prd.depo as depo_price, prd.distri as distri_price,
									pc.id as cat_id, pc.name as cat_name,
									u.display_name, ud.sap_code,
									SUM(rdet.req_qnty) AS req_qnty, SUM(rdet.approved_qnty) AS approved_qnty
									FROM depot_requisition req JOIN depot_req_details rdet ON req.req_id = rdet.req_id
									JOIN tbl_point p ON p.point_id = req.point_id
									JOIN users u ON u.id = req.req_by
									JOIN tbl_user_details ud ON ud.user_id = u.id
									JOIN eshop_product prd ON prd.id = rdet.product_id
									JOIN eshop_product_category pc ON pc.id = rdet.cat_id
									WHERE rdet.req_qnty > rdet.approved_qnty
									$whereCond
									GROUP BY prd.id
									ORDER BY prd.id");
									
									//
									//p.point_id, p.point_name, p.point_division, p.business_type_id,
									
		  							

        return view('Depot/pending_order/pending_order_summary', compact('selectedMenu','selectedSubMenu','pageTitle',
		'resultPenOrdSummary','divList','qnty_fillup', 'sel_from_ReqDate', 'sel_to_ReqDate', 'sel_div_id', 
		'sel_business_type', 'sel_qnty_fillup', 'sel_cat_id', 'sel_prod_id', 'sel_cust_id', 'catList', 'prodList', 'custList'));			
		
	}
	
	
									/*  Delivery Pending Order Process */
	
	public function pending_order_list(Request $req)
	{
		$selectedMenu    = 'Pending Order';             // Required Variable
        $selectedSubMenu = 'Pending Order';             // Required Variable
        $pageTitle       = 'Pending Order';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		$catList = DB::table('eshop_product_category')->orderBy('id','asc')->get();
		$prodList = DB::table('eshop_product')->orderBy('id','asc')->get();
		$custList = DB::table('users')
                        ->select('users.*')
						->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->where('users.user_type_id', '5')
                        ->where('users.is_active', 0) 
						->orderBy('users.display_name','ASC')
                        ->get();
						
						 
		
		$whereCond = '';
		$sel_from_ReqDate = '';
		if ($req->get('fromReqDate') != '') 
		{
			$sel_from_ReqDate = $req->get('fromReqDate');
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND req.req_date >= '".$fromDate."' ";
		}
		
		$sel_to_ReqDate = '';
		if ($req->get('toReqDate') != '') 
		{
			$sel_to_ReqDate = $req->get('toReqDate');
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND req.req_date <= '".$toDate."' ";
		}
		
		$sel_div_id = '';
		if ($req->get('div_id') != '') 
		{
			$sel_div_id = $req->get('div_id');
			$whereCond .= " AND p.point_division = '".$req->get('div_id')."'";
		}
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		$sel_cat_id = '';
		if ($req->get('cat_id') != '') 
		{
			$sel_cat_id = $req->get('cat_id');
			$whereCond .= " AND pc.id = '".$req->get('cat_id')."'";
		}
		
		$sel_prod_id = '';
		if ($req->get('prod_id') != '') 
		{
			$sel_prod_id = $req->get('prod_id');
			$whereCond .= " AND prd.id = '".$req->get('prod_id')."'";
		}
		
		$sel_cust_id = '';
		if ($req->get('cust_id') != '') 
		{
			$sel_cust_id = $req->get('cust_id');
			$whereCond .= " AND u.id = '".$req->get('cust_id')."'";
		}
		
		
		
		$sel_qnty_fillup = '';
		if ($req->get('qnty_fillup') != '') 
		{
			$sel_qnty_fillup = $req->get('qnty_fillup');
			$qnty_fillup = $req->get('qnty_fillup');
		}
		
					
		$resultPenOrdList = DB::select("SELECT req.req_id, req.req_no, req.req_date, 
									p.point_id, p.point_name, p.point_division, p.business_type_id,
									prd.id as prod_id, prd.name as pro_name, prd.depo as depo_price, 
									pc.id as cat_id, pc.name as cat_name,
									u.display_name, ud.sap_code,
									rdet.req_qnty, rdet.approved_qnty AS delevered_qnty
									FROM depot_requisition req JOIN depot_req_details rdet ON req.req_id = rdet.req_id
									JOIN tbl_point p ON p.point_id = req.point_id
									JOIN users u ON u.id = req.req_by
									JOIN tbl_user_details ud ON ud.user_id = u.id
									JOIN eshop_product prd ON prd.id = rdet.product_id
									JOIN eshop_product_category pc ON pc.id = rdet.cat_id
									WHERE rdet.req_qnty > rdet.approved_qnty
									$whereCond
									ORDER BY req.req_id");
									
		  							

        return view('Depot/pending_order/pending_order_list', compact('selectedMenu','selectedSubMenu','pageTitle',
		'resultPenOrdList','divList','qnty_fillup', 'sel_from_ReqDate', 'sel_to_ReqDate', 'sel_div_id', 
		'sel_business_type', 'sel_qnty_fillup', 'sel_cat_id', 'sel_prod_id', 'sel_cust_id', 'catList', 'prodList', 'custList'));			
		
	}
	
	
	private function chkProdReOrd(Request $request, $req_id)
	{
		if($req_id)
		{	
			foreach($request->input('cat_id') as $row_cat_ID => $cat_id)
			{
				$prod_id 	 = $request->input('prod_id')[$row_cat_ID];
				$reord_qnty  = 'reord_qnty_' . $req_id . '_' . $cat_id . '_' . $prod_id;
				if($request->input($reord_qnty) > 0)
					return true;
			}
		}	
	}
	
	
	public function pending_order_process(Request $request)
	{
	   
        if($request->isMethod('post'))
        {
			
			$req_id_check = array();
			// master process
			foreach($request->input('req_id') as $rowID => $req_id)
			{
			
				if(!in_array($req_id, $req_id_check) and self::chkProdReOrd($request, $req_id))
				{
					$req_id_check[] = $req_id;
			
					// update prev main order
					$ReOrdInfo = DB::table('depot_requisition')->where('req_id',$req_id)->update(
								[
									'reorder_status'        => 	'YES',
									'reorder_count'         => 	DB::raw('reorder_count+1')
								]
					); 
					
					
					$point_id = $request->input('point_id')[$rowID];
					$sap_code = $request->input('sap_code')[$rowID];
					$cat_id = $request->input('cat_id')[$rowID];
					$prod_id = $request->input('prod_id')[$rowID];
					$reDate = date('Y-m-d H:i:s'); 
					
					$LastReqId = DB::select("SELECT (MAX(req_id) + 1) as last_req_id FROM depot_requisition");		
					$req_no = $sap_code . date('dmY') . $LastReqId[0]->last_req_id;
					
					$depoRec = DB::select("SELECT u.id FROM tbl_user_business_scope bs JOIN users u ON u.id = bs.user_id
												WHERE u.user_type_id = 5 and bs.point_id = '".$point_id."' and bs.is_active =0
									");
					
					if(sizeof($depoRec)>0)
					{
					   $depot_in_charge = $req_by = $depoRec[0]->id;
					} else {
						$depot_in_charge = 0;
					}						
									
					
					//$req_no = 'req_001';
					
					$reord_id = DB::table('depot_requisition')->insertGetId(
									[ 'depot_in_charge' => $depot_in_charge,
									  'point_id' => $point_id,
									  'req_no' => $req_no,
									  'req_by' => $req_by,
									  'req_date' => $reDate,
									  'sent_by' => $req_by,
									  'sent_date' => $reDate,
									  'acknowledge_by' => $req_by,
									  'acknowledge_date' => $reDate,
									  'approved_by' => $req_by,
									  'approved_date' => $reDate,
									  //'delivered_by' => $req_by,
									  //'delivered_date' => $reDate,
									  'req_status' => 'approved',
									  'is_active' => 'YES',
									  're_order_reference' => $req_id, 	
									
									]
					);
					
						
					//foreach($request->input('cat_id') as $row_cat_ID => $cat_id)
					foreach($request->input('req_id') as $row_req_ID => $req_id_again)
					{
						if($req_id == $req_id_again)
						{
							$reord_qnty = ''; $cat_id = ''; $prod_id = '';
							$cat_id 	 = $request->input('cat_id')[$row_req_ID];
							$prod_id 	 = $request->input('prod_id')[$row_req_ID];
							$reord_qnty  = 'reord_qnty_' . $req_id . '_' . $cat_id . '_' . $prod_id;
							
							if($request->input($reord_qnty) > 0)
							{
								$totalPrice = $request->input($reord_qnty) * $request->input('depo_price')[$row_req_ID];

								DB::table('depot_req_details')->insert(
									[
										'req_id'          	=> $reord_id,
										'product_id'      	=> $prod_id,
										'cat_id'        	=> $cat_id,
										'req_qnty'         	=> $request->input($reord_qnty),
										'req_value'       	=> $totalPrice,
										'approved_qnty'     => $request->input($reord_qnty),
										'approved_value'    => $totalPrice,
										//'delevered_qnty'    => $request->input($reord_qnty),
										//'delevered_value'   => $totalPrice,
									]
								); 
							}
						}
					}
				
				} // re-order check					
			
			} // loop closed		
  
            //return Redirect::to('/req-list_product/'.$request->get('req_id'))->with('success', 'Successfully Added.');
			
			return Redirect::back()->with('success', 'Successfully Pending Order has been Submitted.');	
			
        }
		
	}
	
	
	/*  Order Active and INactive list */
	
	public function req_active_list(Request $req)
	{
		$selectedMenu    = 'Requisition Active';             // Required Variable
        $selectedSubMenu = 'Requisition Active';             // Required Variable
        $pageTitle       = 'Requisition Active';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		
		$whereCond = '';
		if ($req->get('fromReqDate') != '') 
		{
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND dr.req_date >= '".$fromDate."' ";
		}
		
		if ($req->get('toReqDate') != '') 
		{
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND dr.req_date <= '".$toDate."' ";
		}
		
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND d.div_id = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('req_status') != '') 
		{
			$whereCond .= " AND dr.req_status = '".$req->get('req_status')."'";
		}
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_id, p.point_name, p.business_type_id,
									dr.*, d.div_id,
									SUM(rdet.req_qnty) as totQnty, SUM(rdet.req_value) as totVal
									FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
									JOIN tbl_point p ON p.point_id = dr.point_id 
									JOIN tbl_division d ON d.div_id = p.point_division
									JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
									WHERE dr.is_active = 'YES' AND dr.req_status = 'send'
									$whereCond
										GROUP BY dr.req_id
									ORDER BY dr.req_id desc");

        return view('eshop::requisition/active_inactive/req_active_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList','divList'));			
		
	}
	
	
	/* Requisition Approved */
	
	public function req_inactive_process(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			
			foreach($req->input('reqid') as $rowReqId)
			{
				$ordack = 'ordack' . $rowReqId;
				$ordackVal = $req->input($ordack);
				
				//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
				
				if($ordackVal == 'YES')
				{
					//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
						
					$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$rowReqId)->update(
						[
							'is_active'  => 'NO',
							'active_by'  => Auth::user()->id,
							'active_date'  => date('Y-m-d H:i:s')
						]
					);
					
					
					/*
					$point_id = $req->input('point_id' . $rowReqId);
					$depot_in_charge = $req->input('depot_in_charge' . $rowReqId);
					$trans_amount = $req->input('trans_amount' . $rowReqId);
					
					// cash block
						
						$cash_block = array();
						$cash_block['point_id'] = $point_id;
						$cash_block['depot_in_charge'] =  $depot_in_charge;
						$cash_block['transaction_type'] =  'debit';
						$cash_block['payment_type'] =  'CASH';
						$cash_block['trans_amount'] =  $trans_amount;
						$cash_block['trans_date'] =  date('Y-m-d H:i:s');
						$cash_block['entry_by'] =  Auth::user()->id;
						$cash_block['entry_date'] =  date('Y-m-d H:i:s');
						
						DB::table('depot_accounts_payments')->insert([$cash_block]);
					*/	
						
					$point_id = '';
					$depot_in_charge = '';
					$trans_amount = '';	
				
				}
				
			}
			
			
			
			
			
			
		}
		
		return Redirect::to('/reqAllActiveList/')->with('success', 'Successfully Order InActive.');
	}
	
	
	public function req_inactive_list(Request $req)
	{
		$selectedMenu    = 'Requisition IN Active';             // Required Variable
        $selectedSubMenu = 'Requisition IN Active';             // Required Variable
        $pageTitle       = 'Requisition IN Active';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		
		$whereCond = '';
		if ($req->get('fromReqDate') != '') 
		{
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND dr.req_date >= '".$fromDate."' ";
		}
		
		if ($req->get('toReqDate') != '') 
		{
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND dr.req_date <= '".$toDate."' ";
		}
		
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND d.div_id = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('req_status') != '') 
		{
			$whereCond .= " AND dr.req_status = '".$req->get('req_status')."'";
		}
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_id, p.point_name, p.business_type_id,
									dr.*, d.div_id,
									SUM(rdet.req_qnty) as totQnty, SUM(rdet.req_value) as totVal
									FROM depot_requisition dr JOIN users u ON dr.depot_in_charge = u.id 
									JOIN tbl_point p ON p.point_id = dr.point_id 
									JOIN tbl_division d ON d.div_id = p.point_division
									JOIN depot_req_details rdet ON rdet.req_id = dr.req_id
									WHERE dr.is_active = 'NO' AND dr.req_status = 'send'
									$whereCond
										GROUP BY dr.req_id
									ORDER BY dr.req_id desc");

        return view('eshop::requisition/active_inactive/req_in_active_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList','divList'));			
		
	}
	
	
	public function req_active_process(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			
			foreach($req->input('reqid') as $rowReqId)
			{
				$ordack = 'ordack' . $rowReqId;
				$ordackVal = $req->input($ordack);
				
				//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
				
				if($ordackVal == 'YES')
				{
					//echo 'name = ' . $ordack . 'val = ' . $ordackVal; exit;
						
					$DepotReqUpd = DB::table('depot_requisition')->where('req_id',$rowReqId)->update(
						[
							'is_active'  => 'YES',
							'active_by'  => Auth::user()->id,
							'active_date'  => date('Y-m-d H:i:s')
						]
					);
					
					
					/*
					$point_id = $req->input('point_id' . $rowReqId);
					$depot_in_charge = $req->input('depot_in_charge' . $rowReqId);
					$trans_amount = $req->input('trans_amount' . $rowReqId);
					
					// cash block
						
						$cash_block = array();
						$cash_block['point_id'] = $point_id;
						$cash_block['depot_in_charge'] =  $depot_in_charge;
						$cash_block['transaction_type'] =  'debit';
						$cash_block['payment_type'] =  'CASH';
						$cash_block['trans_amount'] =  $trans_amount;
						$cash_block['trans_date'] =  date('Y-m-d H:i:s');
						$cash_block['entry_by'] =  Auth::user()->id;
						$cash_block['entry_date'] =  date('Y-m-d H:i:s');
						
						DB::table('depot_accounts_payments')->insert([$cash_block]);
					*/	
						
					$point_id = '';
					$depot_in_charge = '';
					$trans_amount = '';	
				
				}
				
			}
			
			
			
			
			
			
		}
		
		return Redirect::to('/reqAllInActiveList/')->with('success', 'Successfully Order Active.');
	}
	
	
	/* Customar Active/In-Active */
	
	public function cust_active_list(Request $req)
	{
		$selectedMenu    = 'Cuatomar Active';             // Required Variable
        $selectedSubMenu = 'Cuatomar Active';             // Required Variable
        $pageTitle       = 'Cuatomar Active';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		
		$whereCond = '';
		if ($req->get('fromReqDate') != '') 
		{
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND dr.req_date >= '".$fromDate."' ";
		}
		
		if ($req->get('toReqDate') != '') 
		{
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND dr.req_date <= '".$toDate."' ";
		}
		
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND d.div_id = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('req_status') != '') 
		{
			$whereCond .= " AND dr.req_status = '".$req->get('req_status')."'";
		}
						
		$resultCustList = DB::select("SELECT u.id as cust_id, u.sap_code, u.display_name, ud.current_address, ud.cell_phone, 
									d.div_id, d.div_name, p.point_id, p.point_name, u.business_type_id
									FROM users u JOIN tbl_user_details ud ON u.id = ud.user_id 
									JOIN tbl_user_business_scope ubs ON ubs.user_id = u.id
									JOIN tbl_point p ON p.point_id = ubs.point_id
									JOIN tbl_division d ON d.div_id = p.point_division
									WHERE u.user_type_id = 5
									AND ubs.is_active = 0
									AND u.is_active = 0");

        return view('eshop::requisition/active_inactive/cust_active_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultCustList','divList'));			
		
	}
	
	
	public function cust_inactive_process(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('custid')); exit;
			
			foreach($req->input('custid') as $rowCustId)
			{
				$custack = 'custack' . $rowCustId;
				$custackVal = $req->input($custack);
				
				//echo 'name = ' . $custack . 'val = ' . $custackVal; exit;
				
				if($custackVal == 'YES')
				{
					//echo 'name = ' . $custack . 'val = ' . $custackVal . 'cust_id = ' . $rowCustId; exit;
						
					$CustUpd = DB::table('users')->where('id',$rowCustId)->update(
						[
							'is_active'  => '2',
							'update_by'  => Auth::user()->id,
							'update_date'  => date('Y-m-d H:i:s')
						]
					);
					
					$custack = '';
					$custackVal = '';
				}
				
			}
			
		}
		
		return Redirect::to('/custAllActiveList/')->with('success', 'Successfully Customar InActive.');
	}
	
	
	public function cust_inactive_list(Request $req)
	{
		$selectedMenu    = 'Cuatomar IN Active';             // Required Variable
        $selectedSubMenu = 'Cuatomar IN Active';             // Required Variable
        $pageTitle       = 'Cuatomar IN Active';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		
		$whereCond = '';
		if ($req->get('fromReqDate') != '') 
		{
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND dr.req_date >= '".$fromDate."' ";
		}
		
		if ($req->get('toReqDate') != '') 
		{
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND dr.req_date <= '".$toDate."' ";
		}
		
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND d.div_id = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('req_status') != '') 
		{
			$whereCond .= " AND dr.req_status = '".$req->get('req_status')."'";
		}
						
		$resultCustList = DB::select("SELECT u.id as cust_id, u.sap_code, u.display_name, ud.current_address, ud.cell_phone, 
									d.div_id, d.div_name, p.point_id, p.point_name, u.business_type_id
									FROM users u JOIN tbl_user_details ud ON u.id = ud.user_id 
									JOIN tbl_user_business_scope ubs ON ubs.user_id = u.id
									JOIN tbl_point p ON p.point_id = ubs.point_id
									JOIN tbl_division d ON d.div_id = p.point_division
									WHERE u.user_type_id = 5
									AND ubs.is_active = 0
									AND u.is_active = 2");

        return view('eshop::requisition/active_inactive/cust_in_active_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultCustList','divList'));			
		
	}
	
	
	public function cust_active_process(Request $req)
	{
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('custid')); exit;
			
			foreach($req->input('custid') as $rowCustId)
			{
				$custack = 'custack' . $rowCustId;
				$custackVal = $req->input($custack);
				
				//echo 'name = ' . $custack . 'val = ' . $custackVal; exit;
				
				if($custackVal == 'YES')
				{
					//echo 'name = ' . $custack . 'val = ' . $custackVal . 'cust_id = ' . $rowCustId; exit;
						
					$CustUpd = DB::table('users')->where('id',$rowCustId)->update(
						[
							'is_active'  => '0',
							'update_by'  => Auth::user()->id,
							'update_date'  => date('Y-m-d H:i:s')
						]
					);
					
					$custack = '';
					$custackVal = '';
				}
				
			}
			
		}
		
		return Redirect::to('/custAllInActiveList/')->with('success', 'Successfully Customar IN-Active.');
	}



	// Export Excel File.

	public function export(Request $Request) 
	{

		$reqID=$Request->get('requisition_id');

		$items  = DB::table('depot_requisition')
		->select('users.display_name as Depot Name','depot_requisition.req_no as req_no','users.sap_code as sap_code','tbl_point.point_name AS Point Name','eshop_product.companyid AS CompanyCode',
		'eshop_product.sap_code AS MaterialCode','eshop_product_category.name AS Category','eshop_product.name AS Material Name',
		'depot_req_details.req_qnty AS Qty','depot_req_details.req_value AS Value',
		'depot_req_details.approved_qnty AS AppQty','depot_req_details.approved_value AS AppValue')
		->join('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
		->join('depot_req_details', 'depot_req_details.req_id', '=', 'depot_requisition.req_id')
		->join('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
		->join('eshop_product_category', 'eshop_product_category.id', '=', 'depot_req_details.cat_id')
		->join('eshop_product', 'eshop_product.id', '=', 'depot_req_details.product_id')
		->where('depot_requisition.req_id', $reqID)
		->orderBy('depot_requisition.req_id','DESC')                    
		->get()->toArray();

		$custom_array[] = array('SO Ref No','Company Code','SO Type','Dis.Cha','Sold To Party
','Ship To Party','Ref PO No','Ref PO Date','req del date','Order Note','Debit/Credit Note','Order Reason','Currency','Material Code','Qty','Plant Code','Storage Loc','Batch','Item Category','ZASV','ZEXP','ZFRH','ZTHP','ZSCR','ZRMS','ZAST');

		$s = 1;
		foreach ($items as $value) 
		{
			$custom_array[] = array(
				'SO Ref No' 	=> $value->req_no,
				'Company Code' 	=> $value->CompanyCode,
				'SO Type' 	    => 'ZTRD',
				'Dis.Cha' 	    => '10',
				'Sold To Party' => $value->sap_code,
				'Ship To Party' => '',
				'Ref PO No' 	=> '',
				'Ref PO Date' 	=> '',
				'req del date' 	=> '',
				'Order Note' 	=> '',
				'Debit/Credit Note'=> '',
				'Order Reason' 	=> '',
				'Currency' 	    => '',
				'Material Code' => $value->MaterialCode,
				'Qty'           => $value->AppQty,
				'Plant Code'    => $value->CompanyCode+$s,
				'Storage Loc'   => '',
				'Batch'         => '',
				'Item Category' => '',
				'ZASV' => '',
				'ZEXP' => '',
				'ZFRH' => '',
				'ZTHP' => '',
				'ZSCR' => '',
				'ZRMS' => '',
				'ZAST' => ''
			);
			
		//$s++;
		}

		Excel::create('Upload_Sales_Order_For_SAP',function($excel) use ($custom_array) {
			$excel->sheet('Sales_Order',function($sheet) use ($custom_array){
				$sheet->fromArray($custom_array,null,'A1', false,false);
			});
		})->download('xlsx');
		
		
		
		
		
	}


	public function export_sale_order(Request $Request) 
	{

		$items  = DB::table('eshop_order')
		->select('eshop_order.order_id','eshop_order.order_no as order_no','eshop_party_list.name as partyName','eshop_customer_list.sap_code as sap_code','eshop_customer_list.name AS customerName','eshop_product.companyid AS CompanyCode',
		'eshop_product.sap_code AS MaterialCode','eshop_product_category.name AS Category','eshop_product.name AS Material Name',
		'eshop_order_details.order_qty AS Qty','eshop_order_details.order_total_value AS Value',
		'eshop_order_details.approved_qty AS AppQty','eshop_order_details.approved_value AS AppValue')
		->join('eshop_party_list', 'eshop_party_list.party_id', '=', 'eshop_order.party_id')
		->join('eshop_order_details', 'eshop_order_details.order_id', '=', 'eshop_order.order_id')
		->join('eshop_customer_list', 'eshop_customer_list.customer_id', '=', 'eshop_order.customer_id')

		->join('eshop_product', 'eshop_product.sap_code', '=', 'eshop_order_details.sap_code')
        ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_product.category_id')
        
		// ->join('eshop_product_category', 'eshop_product_category.id', '=', 'eshop_order_details.cat_id')
		// ->join('eshop_product', 'eshop_product.id', '=', 'eshop_order_details.product_id')

		->where('eshop_order.req_status', 'billed') 
		->where('eshop_order.is_download', 'NO') 
		->orderBy('eshop_order.order_id','DESC')                    
		->get()
		->toArray();

		//dd($items);

		//

		$custom_array[] = array('SO Ref No','Company Code','SO Type','Dis.Cha','Sold To Party', 'Ship To Party','Ref PO No','Ref PO Date','req del date','Order Note','Debit/Credit Note','Order Reason','Currency','Material Code','Qty','Plant Code','Storage Loc','Batch','Item Category','ZASV','ZEXP','ZFRH','ZTHP','ZSCR','ZRMS','ZAST');

		$s = 1;
		foreach ($items as $value) 
		{
			
			if($value->AppQty>0){
				$custom_array[] = array(
					'SO Ref No' 	=> $value->order_no,
					'Company Code' 	=> '1600',
					'SO Type' 	    => 'ZCOR',
					'Dis.Cha' 	    => '20',
					'Sold To Party' => $value->sap_code,
					'Ship To Party' => '',
					'Ref PO No' 	=> '',
					'Ref PO Date' 	=> '',
					'req del date' 	=> '',
					'Order Note' 	=> '',
					'Debit/Credit Note'=> '',
					'Order Reason' 	=> '',
					'Currency' 	    => '',
					'Material Code' => $value->MaterialCode,
					'Qty'           => $value->AppQty,
					'Plant Code'    => '1611',
					'Storage Loc'   => '',
					'Batch'         => '',
					'Item Category' => '',
					'ZASV' => '',
					'ZEXP' => '',
					'ZFRH' => '',
					'ZTHP' => '',
					'ZSCR' => '',
					'ZRMS' => '',
					'ZAST' => ''
				);

				$totalQty = DB::table('eshop_order_details')
                     ->select('order_id', DB::raw('SUM(order_qty) AS orderQty'),DB::raw('SUM(order_total_value) AS orderValue'), DB::raw('SUM(deliverey_qty) AS delivereyQty'),DB::raw('SUM(delivery_value) AS deliveryValue'))
                    ->where('order_id', $value->order_id)
                    ->groupBy('order_id')
                    ->first();

				
				$ReqDownUpd = DB::table('eshop_order')->where('order_id',$value->order_id)->where('status',4)->update(
						[
							'total_order_qty'  		=> $totalQty->orderQty,
							'total_order_value'  	=> $totalQty->orderValue,
							'total_delivery_qty'  	=> $totalQty->delivereyQty,
							'total_delivery_value'  => $totalQty->deliveryValue,
							'delivery_date'  		=> date('Y-m-d H:i:s'),
							'order_status'  		=> 'Delivered',
							'ack_status'  			=> 'Pending',
							'approval_status'       => 'Downloaded for SAP',
							'status'                => 5 //download for sap
							///'is_download'  			=> 'YES',
							//'download_by'  			=> Auth::user()->id,
							//'download_date'  		=> date('Y-m-d H:i:s')
						]
					);	
				
				
				
			}
		//$s++;
		} 
		Excel::create('Upload_Sales_Order_For_SAP',function($excel) use ($custom_array) {
			$excel->sheet('Sales_Order',function($sheet) use ($custom_array){
				return $sheet->fromArray($custom_array,null,'A1', false, false);
			});
		})->download('xlsx'); 
		return Redirect::back()->with('success', 'Successfully Approved!');  
		//return Redirect::to('/eshop-reqAllBilledList/')->with('success', 'Successfully Added Add To Cart.'); 
		
	}

	public function orderCconfirmDownload(){  
		$ReqDownUpd = DB::table('eshop_order')->where('req_status','billed')->where('is_download','NO')->where('status',5)->update([
			'is_download'  			=> 'YES',
			'download_by'  			=> Auth::user()->id,
			'download_date'  		=> date('Y-m-d H:i:s'),
			'approval_status'		=> 'download confirmed by biller',
			'status'				=> 6 //confirm download
		]);
		return Redirect::to('/eshop-reqAllBilledList/')->with('success', 'Download has been successfully');
	}
}
