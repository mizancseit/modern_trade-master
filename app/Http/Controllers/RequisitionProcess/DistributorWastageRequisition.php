<?php

namespace App\Http\Controllers\RequisitionProcess;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Sales\ProductsStockUploadModel;

use DB;
use Auth;
use Session;
use Excel;

class DistributorWastageRequisition extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 12/06/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }


    // Wastage Declaration start


    public function was_declaration_manage()
    {
        $selectedMenu    = 'Wastage Declaration';             // Required Variable
        $selectedSubMenu = 'Requisition';             // Required Variable
        $pageTitle       = 'Requisition';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM tbl_wastage dr 
										JOIN users u ON dr.distributor_id = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_id = '".Auth::user()->id."'
										AND dr.order_type = 'Declaration'
										AND dr.is_active = 0
										ORDER BY dr.order_id desc");

        return view('sales/requisitionProcess/distributor/wastageDeclaration/req_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList'));
    }

    public function was_declaration_add()
    {
        $selectedMenu    = 'Wastage Declaration';             // Required Variable
        $selectedSubMenu = 'Requisition';             // Required Variable
        $pageTitle       = 'Requisition Manage';            // Page Slug Title
				

		$reqAddList = DB::select("SELECT u.display_name, u.id, ud.sap_code, p.point_id, p.point_name
								FROM users u JOIN tbl_user_business_scope bs ON  u.id = bs.user_id 
								JOIN tbl_user_details ud ON ud.user_id = u.id
								JOIN tbl_point p ON p.point_id = bs.point_id
								WHERE u.id='".Auth::user()->id."'");

		$LastReqId = DB::select("SELECT (MAX(order_id) + 1) as last_req_id FROM tbl_wastage");						

        return view('sales/requisitionProcess/distributor/wastageDeclaration/req_add', compact('selectedMenu','selectedSubMenu','pageTitle','reqAddList','LastReqId'));
    }


    public function was_declaration_process(Request $req)
    {
      
		if ($req->isMethod('post')) 
		{
			$selectedMenu    = 'Wastage Declaration';              // Required Variable
			$selectedSubMenu = 'Requisition';             // Required Variable
			$pageTitle       = 'Requisition Manage';     // Page Slug Title
			
			$point_id 		= 	$req->input('point_id');
			$order_no 		= 	$req->input('req_no');
			//$reDate		= 	$req->input('req_date');
			//$reDate 		= date('Y-m-d H:i:s');
			
			$reDate = date('Y-m-d H:i:s', strtotime($req->input('req_date'))); //mm-dd-yyyy
			 
			$distributor_id =	$req_by = Auth::user()->id;
			
			$req_id = DB::table('tbl_wastage')->insertGetId(
                            [
                            'order_no'			=>$order_no, 
                            'order_type'		=>'Declaration',
                            'order_date'		=>$reDate, 
                            'chalan_date'		=>$reDate,
                            'distributor_id'	=>$distributor_id, 
                            'point_id'			=>$point_id, 
                            'entry_by'			=>$req_by,
                            'entry_date'		=>$reDate, 
                            'is_active'			=>0	
                            ]
                            );
					 
			return Redirect::to('/dist/was-declaration-list-product/'.$req_id)->with('success', 'Successfully Declaration Added.');
			   
		}
    }

    public function was_declaration_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $point_id = $request->get('point_id');



        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $lastReq = DB::table('tbl_wastage')
        			->where('point_id', $point_id)
                    ->orderBy('order_id', 'desc')
                    ->first();


        //dd($lastReq);

        return view('sales/requisitionProcess/distributor/wastageDeclaration/allProductList', compact('resultProduct','categoryID','lastReq'));
    }

    public function was_declaration_list_product($req_id)
	{
		$selectedMenu    = 'Wastage Declaration';              // Required Variable
		$selectedSubMenu = 'Requisition';             // Required Variable
		$pageTitle       = 'Requisition Manage';     // Page Slug Title
			
			
		$resultReqList = DB::select("SELECT u.display_name,p.point_id, p.point_name, dr.* 
										FROM tbl_wastage dr 
										JOIN users u ON dr.distributor_id = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.order_id = '".$req_id."'
										AND dr.order_type = 'Declaration'
										AND dr.is_active = 0
										ORDER BY dr.order_id desc");
										
		
		$resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
						
		$resultCart     = DB::select("SELECT SUM(wastage_qty) as grand_total_value FROM tbl_wastage_details WHERE order_id = '".$req_id."'");
									
					


		return view('sales/requisitionProcess/distributor/wastageDeclaration/categoryWithOrder', compact('selectedMenu','selectedSubMenu','pageTitle',
																	'resultCategory','resultReqList', 'resultCart', 'req_id'));				
		
	}


    public function was_declaration_bucket($reqid)
    {
		$selectedMenu    = 'Wastage Declaration';             // Required Variable
		$selectedSubMenu = 'Requisition';             // Required Variable
		$pageTitle       = 'Requisition Manage';            // Page Slug Title


		//dd($reqid);
		$resultReqPro = DB::select("SELECT rdet.order_det_id as id, rdet.wastage_qty, u.display_name as distributor_name, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.replace_delivered_qty, rdet.p_total_price
								FROM tbl_wastage dr JOIN users u ON dr.distributor_id = u.id 
								JOIN tbl_wastage_details rdet ON rdet.order_id = dr.order_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.order_id = '".$reqid."'
								AND dr.order_type = 'Declaration'
								AND dr.is_active = '0'
								ORDER BY dr.order_id DESC");
		
        return view('sales/requisitionProcess/distributor/wastageDeclaration/bucket', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
    }

    public function was_declaration_add_product(Request $request)
	{
	   
        if($request->isMethod('post'))
        {
			$countRows = count($request->get('qty'));
			$pointID   = $request->get('point_id');
			$reference_id = $request->get('req_id');
			
            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice 	= $request->get('qty')[$m] * $request->get('price')[$m];

                    DB::table('tbl_wastage_details')->insert(
                        [
                            'order_id'          	=> $request->get('req_id'),
                            'cat_id'        		=> $request->get('category_id')[$m],
                            'product_id'      		=> $request->get('produuct_id')[$m],
                            'wastage_qty' 			=> $request->get('qty')[$m],
                            'p_unit_price'       	=> $request->get('price')[$m],
                            'p_total_price'       	=> $totalPrice,
                        ]
                    );


                DB::table('depot_inventory')->insert(
                [
                    'point_id'           => $pointID,
                    'depot_in_charge' 	 => Auth::user()->id,
                    'reference_id'		 => $reference_id,
                    'cat_id'             => $request->get('category_id')[$m],
                    'product_id'         => $request->get('produuct_id')[$m],
                    'product_qty'        => $request->get('qty')[$m],
                    'product_value'      => $totalPrice,
                    'inventory_date'     => date('y-m-d'),
                    'inventory_type'     => 2,
                    'transaction_type'   => 'wastage_declaration',
                    'global_company_id'  => Auth::user()->global_company_id,
                    'created_by'         => Auth::user()->id
                ]
            	); 
        

		        $stockProduct = DB::table('depot_stock')
		                        ->select('point_id','cat_id','product_id','stock_qty')
		                        ->where('point_id', $pointID)
		                        ->where('cat_id', $request->get('category_id')[$m])
		                        ->where('product_id', $request->get('produuct_id')[$m])
		                        ->first();


	            if(sizeof($stockProduct)>0)
	            {
	                
	                $totalQty = $stockProduct->stock_qty - $request->get('qty')[$m];
	                   
	                
	                DB::table('depot_stock')
	                ->where('point_id',$pointID)
	                ->where('cat_id',$request->get('category_id')[$m])
	                ->where('product_id',$request->get('produuct_id')[$m])
	                ->update(
	                [
	                    'point_id'           => $pointID,
	                    'cat_id'             => $request->get('category_id')[$m],
	                    'product_id'         => $request->get('produuct_id')[$m],
	                    'stock_qty'          => $totalQty,
	                    'global_company_id'  => Auth::user()->global_company_id,
	                    'created_by'         => Auth::user()->id                   
	                    
	                ]
	                );

	            }else{

	                DB::table('depot_stock')->insert(
	                    [
	                        'point_id'           => $pointID,
	                        'cat_id'             => $request->get('category_id')[$m],
	                        'product_id'         => $request->get('produuct_id')[$m],
	                        'stock_qty'          => $request->get('qty')[$m],
	                        'global_company_id'  => Auth::user()->global_company_id,
	                        'created_by'         => Auth::user()->id
	                    ]
	                ); 

	            }

              }
            }
  
            return Redirect::to('/dist/was-declaration-list-product/'.$request->get('req_id'))->with('success', 'Successfully Added.');
        }
		
	}

    public function was_req_manage()
    {
        $selectedMenu    = 'Wastage Requisition';             // Required Variable
        $selectedSubMenu = 'Requisition';             // Required Variable
        $pageTitle       = 'Requisition';            // Page Slug Title
						
		
//dd($countReq);

		$ParentList	= DB::select("SELECT * FROM dist_wastage_requisition WHERE req_id not in (
						SELECT dist.req_id FROM dist_wastage_requisition dist JOIN dist_wastage_req_details disDet ON dist.req_id = disDet.req_id)
					   AND distributor_in_charge = '".Auth::user()->id."'
					");
					
					
		if(sizeof($ParentList)>0)
		{
			foreach($ParentList as $rowParentList)
			{
				DB::table('dist_wastage_requisition')
        			->where('req_id', $rowParentList->req_id)
        	        ->delete();
			}
		}	
		
		$countReq = DB::table('dist_wastage_requisition')
        			->where('distributor_in_charge', Auth::user()->id)
        			->where('req_status','=','requisition')
                    ->count('req_id');
	
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'requisition'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");



        return view('sales/requisitionProcess/distributor/wastageRequisition/req_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList','countReq'));
    }
	
	
	public function was_req_add()
    {
        $selectedMenu    = 'Wastage Requisition';             // Required Variable
        $selectedSubMenu = 'Requisition';             // Required Variable
        $pageTitle       = 'Requisition Manage';            // Page Slug Title
				

		$reqAddList = DB::select("SELECT u.display_name, u.id,u.email, ud.sap_code, p.point_id, p.point_name
								FROM users u JOIN tbl_user_business_scope bs ON  u.id = bs.user_id 
								JOIN tbl_user_details ud ON ud.user_id = u.id
								JOIN tbl_point p ON p.point_id = bs.point_id
								WHERE u.id='".Auth::user()->id."'");

		$LastReqId = DB::select("SELECT (MAX(req_id) + 1) as last_req_id FROM dist_wastage_requisition");						

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_add', compact('selectedMenu','selectedSubMenu','pageTitle','reqAddList','LastReqId'));
    }
	
	
	public function was_req_process(Request $req)
    {
      
		if ($req->isMethod('post')) 
		{
			$selectedMenu    = 'Wastage Requisition';              // Required Variable
			$selectedSubMenu = 'Requisition';             // Required Variable
			$pageTitle       = 'Requisition Manage';     // Page Slug Title
			
			$point_id 		= 	$req->input('point_id');
			$req_no 		= 	$req->input('req_no');
			//$reDate		= 	$req->input('req_date');
			//$reDate 		= date('Y-m-d H:i:s');
			
			$reDate = date('Y-m-d H:i:s', strtotime($req->input('req_date'))); //mm-dd-yyyy
			 
			$distributor_in_charge =	$req_by = Auth::user()->id;
			
			
			
			$point=DB::insert('insert into dist_wastage_requisition(distributor_in_charge, point_id, req_no, req_by, 
			req_date, req_status, is_active) 
			values (?,?,?,?,?,?,?)', [$distributor_in_charge, $point_id, $req_no, $req_by, $reDate, 'requisition', 'YES']);
			
			$req_id = DB::getPdo()->lastInsertId(); //mysql_insert_id();
					 
			return Redirect::to('/dist/was-req-list-product/'.$req_id)->with('success', 'Successfully Requisition Added.');
			   
		}
    }
	
	
	public function was_req_list_product($req_id)
	{
		$selectedMenu    = 'Wastage Requisition';              // Required Variable
		$selectedSubMenu = 'Requisition';             // Required Variable
		$pageTitle       = 'Requisition Manage';     // Page Slug Title
			
		$distributor_id = Auth::user()->id;
			
		$resultReqList = DB::select("SELECT u.display_name,p.point_id, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_id = '".$req_id."'
										AND dr.req_status = 'requisition'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
										
		//echo '<pre/>'; print_r($resultReqList); exit;								
										
		$resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
						
		$resultCart     = DB::select("SELECT SUM(req_value) as grand_total_value FROM dist_wastage_req_details WHERE req_id = '".$req_id."'");


		/*$resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', 1)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();*/

           $lastReq = DB::table('dist_wastage_requisition')
        			->where('distributor_in_charge', $distributor_id)
                    ->orderBy('req_id', 'desc')
                    ->skip(1)
                    ->take(1)
                    ->first();

            if(sizeof($lastReq)>0){
            //$date = date('Y-m-d H:i:s',strtotime($lastReq->req_date));
            $date = $lastReq->req_date;
            }

         if(sizeof($lastReq)>0){
         $resultProduct     = DB::select("SELECT tbl_product.category_id,tbl_product_category.name as cname,tbl_product.name,tbl_product.depo as price,tbl_product.unit,e.product_id as id,sum(e.wastage_qty) AS qty,sum(e.wastage_value) AS wastageValue,sum(e.replace_delivered_qty) AS delQty FROM (
			SELECT tbl_order.order_date,tbl_order.point_id,tbl_order.distributor_id,tbl_order_details.product_id,tbl_order_details.wastage_qty,tbl_order_details.wastage_value,tbl_order_details.replace_delivered_qty
			FROM tbl_order 
			INNER JOIN tbl_order_details ON tbl_order_details.order_id = tbl_order.order_id
			WHERE tbl_order.distributor_id =  $distributor_id AND tbl_order_details.replace_delivered_qty>0 AND tbl_order.order_type = 'Delivered' AND date_format(tbl_order.update_date,'%Y-%m-%d %H:%i:%s')>'".$date."'
			UNION ALL
			SELECT tbl_wastage.order_date,tbl_wastage.point_id,tbl_wastage.distributor_id,tbl_wastage_details.product_id,tbl_wastage_details.wastage_qty,tbl_wastage_details.p_total_price as wastage_value,tbl_wastage_details.replace_delivered_qty
			FROM tbl_wastage
			INNER JOIN tbl_wastage_details ON tbl_wastage_details.order_id = tbl_wastage.order_id
			WHERE tbl_wastage.distributor_id =  $distributor_id AND date_format(tbl_wastage.chalan_date,'%Y-%m-%d %H:%i:%s')>'".$date."'
				AND (tbl_wastage.order_type = 'Delivered' OR tbl_wastage.order_type = 'Declaration')
			) AS e
			INNER JOIN tbl_product ON tbl_product.id = e.product_id
			INNER JOIN tbl_product_category ON tbl_product_category.id = tbl_product.category_id
			group by e.product_id");         
       }
       else{
       	$resultProduct     = DB::select("SELECT tbl_product.category_id,tbl_product_category.name as cname,tbl_product.name,tbl_product.depo as price,tbl_product.unit,e.product_id as id,sum(e.wastage_qty) AS qty,sum(e.wastage_value) AS wastageValue,sum(e.replace_delivered_qty) AS delQty FROM (
			SELECT tbl_order.order_date,tbl_order.point_id,tbl_order.distributor_id,tbl_order_details.product_id,tbl_order_details.wastage_qty,tbl_order_details.wastage_value,tbl_order_details.replace_delivered_qty
			FROM tbl_order 
			INNER JOIN tbl_order_details ON tbl_order_details.order_id = tbl_order.order_id
			WHERE tbl_order.distributor_id =  $distributor_id AND tbl_order_details.replace_delivered_qty>0 AND tbl_order.order_type = 'Delivered'
			UNION ALL
			SELECT tbl_wastage.order_date,tbl_wastage.point_id,tbl_wastage.distributor_id,tbl_wastage_details.product_id,tbl_wastage_details.wastage_qty,tbl_wastage_details.p_total_price as wastage_value,tbl_wastage_details.replace_delivered_qty
			FROM tbl_wastage
			INNER JOIN tbl_wastage_details ON tbl_wastage_details.order_id = tbl_wastage.order_id
			WHERE tbl_wastage.distributor_id =  $distributor_id 
				AND (tbl_wastage.order_type = 'Delivered' OR tbl_wastage.order_type = 'Declaration')
			) AS e
			INNER JOIN tbl_product ON tbl_product.id = e.product_id
			INNER JOIN tbl_product_category ON tbl_product_category.id = tbl_product.category_id
			group by e.product_id"); 
       }

        
									
						
		return view('sales/requisitionProcess/distributor/wastageRequisition/categoryWithOrder', compact('selectedMenu','selectedSubMenu','pageTitle',
																	'resultCategory','resultReqList', 'resultCart', 'req_id','resultProduct','lastReq'));				
		
	}
	
	
	
	
	public function was_req_add_product(Request $request)
	{
	   
        if($request->isMethod('post'))
        {
			$countRows = count($request->get('qty'));
			
            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {
                    $totalPrice 	= $request->get('qty')[$m] * $request->get('price')[$m];
                    $totalPrice2 	= $request->get('wastageQty')[$m] * $request->get('price')[$m];

                    DB::table('dist_wastage_req_details')->insert(
                        [
                            'req_id'          	=> $request->get('req_id'),
                            'product_id'      	=> $request->get('produuct_id')[$m],
                            'cat_id'        	=> $request->get('category_id')[$m],
                            'req_qnty'         	=> $request->get('qty')[$m],
                            'req_value'       	=> $totalPrice,
                            'wastage_qty'       => $request->get('wastageQty')[$m],
                            'wastage_value'     => $totalPrice2,
                        ]
                    ); 
                }
            }
  

            //return Redirect::to('/req-bucket/'.$request->get('req_id'))->with('success', 'Successfully Added.');
            return Redirect::to('/dist/was-req-manage/')->with('success', 'Successfully Added.');
        }
		
	}
	
	public function was_req_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $point_id = $request->get('point_id');


        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
                        
        // if(session('isDepot')==1) // Depot
        // { 
        // 	$resultProduct = DB::table('tbl_product')
        //                 ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
        //                 ->where('ims_stat', '0')                       
        //                 ->where('category_id', $categoryID)
        //                 ->orderBy('id', 'ASC')
        //                 ->orderBy('status', 'ASC')
        //                 ->orderBy('name', 'ASC')
        //                 ->get();
        // }
        // else // Distributor
        // {
        // 	$resultProduct = DB::table('tbl_product')
        //                 ->select('id','category_id','name','ims_stat','status','distri AS price','unit')
        //                 ->where('ims_stat', '0')                       
        //                 ->where('category_id', $categoryID)
        //                 ->orderBy('id', 'ASC')
        //                 ->orderBy('status', 'ASC')
        //                 ->orderBy('name', 'ASC')
        //                 ->get();
        // }
       

        $lastReq = DB::table('dist_wastage_requisition')
        			->where('point_id', $point_id)
                    ->orderBy('req_id', 'desc')
                    ->skip(1)
                    ->take(1)
                    ->first();


        //dd($lastReq);

        return view('sales/requisitionProcess/distributor/wastageRequisition/allProductList', compact('resultProduct','categoryID','lastReq'));
    }
	
	
	
	public function was_req_bucket($reqid)
    {
		$selectedMenu    = 'Wastage Requisition';             // Required Variable
		$selectedSubMenu = 'Requisition';             // Required Variable
		$pageTitle       = 'Requisition Manage';            // Page Slug Title

		$resultReqPro = DB::select("SELECT rdet.req_det_id as id, rdet.wastage_qty, u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value,rdet.req_id
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'requisition'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");
		
        return view('sales/requisitionProcess/distributor/wastageRequisition/bucket', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
    }
	
	
	public function was_req_send($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req_id)->update(
				[
					'req_status'	=> 'send',
					'sent_by'		=> Auth::user()->id,
					'sent_date'		=> Date('Y-m-d H:i:s')							
				]
			); 
		}

		return Redirect::to('/dist/was-req-send-list/')->with('success', 'Successfully Send.');
	}
	
	
	public function was_req_send_list()
	{
		$selectedMenu    = 'Wastage Requisition Send';             // Required Variable
        $selectedSubMenu = 'Requisition Send';             // Required Variable
        $pageTitle       = 'Requisition Send';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'send'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_send_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_acknowledge_list()
	{
		$selectedMenu    = 'Requisition Acknowledge';             // Required Variable
        $selectedSubMenu = 'Requisition Acknowledge';             // Required Variable
        $pageTitle       = 'Requisition Acknowledge';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'acknowledge'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('Depot.requisition.req_acknowledge_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function was_req_pending_list()
	{
		$selectedMenu    = 'Wastage Requisition Pending';             // Required Variable
        $selectedSubMenu = 'Requisition Pending';             // Required Variable
        $pageTitle       = 'Requisition Pending';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.*, 
									SUM(rdet.req_qnty) as totQnty, SUM(rdet.req_value) as totVal, ds.depot_current_balance
									FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
									JOIN tbl_point p ON p.point_id = dr.point_id 
									LEFT JOIN tbl_depot_summary ds ON ds.point_id = dr.point_id
									JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
									WHERE dr.req_status = 'send'
									AND dr.is_active = 'YES'
										GROUP BY dr.req_id
									ORDER BY dr.req_id desc");

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_pending_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Approved */
	
	public function was_req_acknowledge(Request $req)
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
						
					$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$rowReqId)->update(
						[
							'req_status'  => 'acknowledge',
							'acknowledge_by'  => Auth::user()->id,
							'acknowledge_date'  => date('Y-m-d H:i:s')
						]
					);
				
				}
				
			}
			
		}
		
		return Redirect::to('/dist/was-req-analysis-list/')->with('success', 'Successfully Acknowledge.');
	}
	
	
	public function was_req_analysis_list()
	{
		$selectedMenu    = 'Wastage Requisition Analysis';             // Required Variable
        $selectedSubMenu = 'Requisition Analysis';             // Required Variable
        $pageTitle       = 'Requisition Analysis';            // Page Slug Title
		
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'acknowledge' OR dr.req_status = 'canceled'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
										
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_analysis_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Approved */
	
	public function was_req_order_analysis($reqid)
	{
		$selectedMenu    = 'Home';             		// Required Variable
		$selectedSubMenu = 'Home';             		// Required Variable
		$pageTitle       = 'Requisition Details';   // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.wastage_qty, pr.stock_qty, pr.depo as price, rdet.req_det_id, rdet.req_qnty, rdet.req_value, dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_order_analysis', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
	}
	
	
	
	/* Requisition Approved New */
	
	public function was_req_approved(Request $req)
	{
		if($req->isMethod('post'))
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req->input('req_id'))->update(
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
			    
				$DepotReqDetUpd = DB::update("UPDATE dist_wastage_req_details SET approved_qnty = $act_aprvd_qnty,
																		   approved_value = $act_apprvd_value 
												WHERE req_det_id = ?", [$reqDetId]);	
			}
			
				
		}
		
		return Redirect::to('/dist/was-req-all-approved-list/')->with('success', 'Successfully Approved.');
	}
	
	
	
	/* Requisition Approved old
	public function req_approved($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'approved',
							'approved_by'  => Auth::user()->id,
							'approved_date'  => date('Y-m-d H:i:s')
						]
					);

			$DepotReqDetUpd = DB::update('UPDATE dist_wastage_req_details SET approved_qnty = req_qnty, approved_value = req_value 
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
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'approved'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('Depot.requisition.req_approved_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function was_req_all_approved_list()
	{
		$selectedMenu    = 'Wastage Requisition Approved';             // Required Variable
        $selectedSubMenu = 'Requisition Approved';             // Required Variable
        $pageTitle       = 'Requisition Approved';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'approved' OR dr.req_status = 'delivered' OR dr.req_status = 'received'   
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_all_approved_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function was_req_open_order_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value,rdet.delevered_qnty,rdet.delevered_value, dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_delivery_download_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	/* Requisition Delivery */
	
	public function was_req_deliver($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'delivered',
							'delivered_by'  => Auth::user()->id,
							'delivered_date'  => date('Y-m-d H:i:s')
						]
					); 
					
			$DepotReqDetUpd = DB::update('UPDATE dist_wastage_req_details SET delevered_qnty = approved_qnty, delevered_value = approved_value 
										WHERE req_id = ?', [$req_id]);				
		}
		
		return Redirect::to('/dist/was-req-all-approved-list/')->with('success', 'Successfully Delivered.');
	}
	
	public function was_req_delivered_list()
	{
		$selectedMenu    = 'Wastage Requisition Delivered';             // Required Variable
        $selectedSubMenu = 'Requisition Delivered';             // Required Variable
        $pageTitle       = 'Requisition Delivered';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'delivered' OR dr.req_status = 'received'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('sales/requisitionProcess/distributor/wastageRequisition/req_delivered_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_all_delivered_list()
	{
		$selectedMenu    = 'Requisition All Delivered';             // Required Variable
        $selectedSubMenu = 'Requisition All Delivered';             // Required Variable
        $pageTitle       = 'Requisition All Delivered';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'delivered'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('Depot.requisition.req_all_delivered_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Canceled */
	public function was_req_canceled($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'canceled',
							'canceled_by'  => Auth::user()->id,
							'canceled_date'  => date('Y-m-d H:i:s')
						]
					); 
		}
		
		return Redirect::to('/dist/was-req-analysis-list')->with('success', 'Successfully Canceled.');
	}
	
	
	public function was_req_canceled_list()
	{
		$selectedMenu    = 'Requisition Canceled';             // Required Variable
        $selectedSubMenu = 'Requisition Canceled';             // Required Variable
        $pageTitle       = 'Requisition Canceled';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'canceled'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('Depot.requisition.req_canceled_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_all_canceled_list()
	{
		$selectedMenu    = 'Requisition Canceled';             // Required Variable
        $selectedSubMenu = 'Requisition Canceled';             // Required Variable
        $pageTitle       = 'Requisition Canceled';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'canceled'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('Depot.requisition.req_all_canceled_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	/* Requisition Received By Depot/Distributor */
	//1st version
	/*
	public function req_receive($req_id)
	{
		if($req_id)
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req_id)->update(
						[
							'req_status'  => 'received',
							'received_by'  => Auth::user()->id,
							'received_date'  => date('Y-m-d H:i:s')
						]
					); 
					
			$DepotReqDetUpd = DB::update('UPDATE dist_wastage_req_details SET received_qnty = delevered_qnty, received_value = delevered_value 
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
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req->input('reqid'))->update(
						[
							'req_status'  => 'received',
							'received_by'  => Auth::user()->id,
							'received_note'  => $req->input('received_note'),
							'grn_no'  => $req->input('grn_no'),
							'received_date'  => date('Y-m-d',strtotime($req->input('received_date')))
						]
					); 
					
			$DepotReqDetUpd = DB::update('UPDATE dist_wastage_req_details SET received_qnty = delevered_qnty, received_value = delevered_value 
										WHERE req_id = ?', [$req->input('reqid')]);	


			$this->req_receive_stock($req->input('reqid'));								
		}
		
		return Redirect::to('/reqReceivedList/')->with('success', 'Successfully Received.');
	}
	*/
	
	
	//3rd version
	public function was_req_receive(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$DepotReqUpd = DB::table('dist_wastage_requisition')->where('req_id',$req->input('reqid'))->update(
						[
							'req_status'  => 'received',
							'received_by'  => Auth::user()->id,
							'received_note'  => $req->input('received_note'),
							'grn_no'  => $req->input('grn_no'),
							'received_date'  => date('Y-m-d',strtotime($req->input('received_date')))
						]
					); 
					
			foreach($req->input('req_det_id') as $req_det_id)
			{
				$received_qnty = "received_qnty_" . $req_det_id;
				$recvd_value = "received_value_" . $req_det_id;
				
				$recvd_qnty = $req->input("$received_qnty");
				$recvd_value = $req->input("$recvd_value");
				
				$DepotReqDetUpd = DB::update("UPDATE dist_wastage_req_details SET received_qnty = '".$recvd_qnty."', 
																		   received_value = '".$recvd_value."'
										WHERE req_det_id = ?", [$req_det_id]);	
			
			}

			$isDepot = DB::table('dist_wastage_requisition')
						->join('tbl_point', 'tbl_point.point_id', '=', 'dist_wastage_requisition.point_id')
                        ->where('dist_wastage_requisition.req_id', $req->input('reqid'))
                        ->first();

            if(sizeof($isDepot)>0){	 
				if($isDepot->is_depot ==1){
			    	$this->req_receive_stock($req->input('reqid'));	
				}else{

					$this->dist_req_receive_stock($req->input('reqid'));	
				}
			}							
		}
		
		return Redirect::to('/dist/was-req-delivered-list/')->with('success', 'Successfully Received.');
	}
	
	
	
	public function req_received_list()
	{
		$selectedMenu    = 'Requisition Received';             // Required Variable
        $selectedSubMenu = 'Requisition Received';             // Required Variable
        $pageTitle       = 'Requisition Received';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.distributor_in_charge = '".Auth::user()->id."'
										AND dr.req_status = 'received'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");

        return view('Depot.requisition.req_received_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	
	public function req_all_received_list()
	{
		$selectedMenu    = 'Requisition Received';             // Required Variable
        $selectedSubMenu = 'Requisition Received';             // Required Variable
        $pageTitle       = 'Requisition Received';            // Page Slug Title
						
		$resultReqList = DB::select("SELECT u.display_name, p.point_name, dr.* 
										FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
										JOIN tbl_point p ON p.point_id = dr.point_id 
										WHERE dr.req_status = 'received'
										AND dr.is_active = 'YES'
										ORDER BY dr.req_id desc");
		
		//echo '<pre/>'; print_r($resultReqList); exit;								

        return view('Depot.requisition.req_all_received_list', compact('selectedMenu','approved','pageTitle','resultReqList'));			
		
	}
	
	public function req_approved_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, rdet.approved_qnty, rdet.approved_value, dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'approved'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('Depot.requisition.req_approved_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	public function was_req_delivered_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, 
								rdet.approved_qnty, rdet.approved_value, rdet.delevered_qnty, rdet.delevered_value,
								dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_delivered_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function req_received_details_list($reqid)
    {
		$selectedMenu    = 'Home';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, rdet.approved_qnty, rdet.approved_value,
								rdet.received_qnty, rdet.received_value, dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.req_status = 'received'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('Depot.requisition.req_received_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	public function was_req_details_list($reqid)
    {
		$selectedMenu    = 'Wastage Requisition Analysis';             // Required Variable
		$selectedSubMenu = 'Requisition Send';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.req_qnty, rdet.req_value, dr.req_status
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_details_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function was_req_delivery_challan($reqid)
    {
		$selectedMenu    = 'Wastage Requisition Delivered';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, u1.display_name as delivered_by,  
								u2.display_name as approved_by, u3.display_name as received_by,  ud.cell_phone as delMobNo,
								p.point_name, pc.name AS catname, 
								pr.name proname, rdet.delevered_qnty, rdet.delevered_value, 
								dr.req_status, dr.req_no, dr.req_date, dr.delivered_date, 
								dr.approved_date, dr.received_date 
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
							LEFT JOIN users u1 ON u1.id = dr.delivered_by
							LEFT JOIN users u2 ON u2.id = dr.approved_by
							LEFT JOIN users u3 ON u3.id = dr.received_by
							LEFT JOIN tbl_user_details ud ON ud.user_id = u3.id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_delivery_challan', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	
	public function was_req_delivery_received_list($reqid)
    {
		$selectedMenu    = 'Wastage Requisition Delivered';             // Required Variable
		$selectedSubMenu = 'Home';             // Required Variable
		$pageTitle       = 'Requisition Details';            // Page Slug Title

		/*
		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, p.point_name, pc.name AS catname, 
								pr.name proname, rdet.delevered_qnty, rdet.delevered_value, 
								dr.req_status, dr.req_no, dr.req_date , dr.delivered_date 
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");	
		*/
								
								
		$resultReqPro = DB::select("SELECT u.display_name as distributor_in_charge, u1.display_name as delivered_by,  
								u2.display_name as approved_by, u3.display_name as received_by,  ud.cell_phone as delMobNo,
								p.point_name, pc.name AS catname, 
								pr.name proname, pr.realtimeprice price, rdet.req_det_id,rdet.req_qnty,rdet.req_value, rdet.delevered_qnty, rdet.delevered_value, 
								dr.req_status, dr.req_no, dr.req_date, dr.delivered_date, 
								dr.approved_date, dr.received_date 
								FROM dist_wastage_requisition dr JOIN users u ON dr.distributor_in_charge = u.id 
								JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								JOIN tbl_point p ON p.point_id = dr.point_id 
								JOIN tbl_product_category pc ON pc.id = rdet.cat_id
								JOIN tbl_product pr ON pr.id = rdet.product_id
							LEFT JOIN users u1 ON u1.id = dr.delivered_by
							LEFT JOIN users u2 ON u2.id = dr.approved_by
							LEFT JOIN users u3 ON u3.id = dr.received_by
							LEFT JOIN tbl_user_details ud ON ud.user_id = u3.id
								WHERE dr.req_id = '".$reqid."'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");							
								
		//echo  '<pre/>'; print_r($resultReqPro); exit;						
								
		 
        return view('sales/requisitionProcess/distributor/wastageRequisition/req_delivery_receive_list', compact('selectedMenu','pageTitle','resultReqPro','reqid'));
        
		
    }
	
	// Stock receive for depot

	private function req_receive_stock($req_id)
	{
		
		if($req_id)
		{
			
			$resultReqData = DB::select("SELECT dr.*,  rdet.product_id, rdet.cat_id, rdet.received_qnty, rdet.received_value
								FROM dist_wastage_requisition dr JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								WHERE dr.req_id = '".$req_id."'
								AND dr.req_status = 'received'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");
				
					
			foreach($resultReqData as $RowReqData)
			{
				 DB::table('depot_inventory')->insert(
                        [
                            'req_id'          		=> $RowReqData->req_id,
                            'req_no'          		=> $RowReqData->req_no,
                            'point_id'          	=> $RowReqData->point_id,
                            'depot_in_charge'      	=> $RowReqData->distributor_in_charge,
                            'cat_id'        		=> $RowReqData->cat_id,
                            'product_id'         	=> $RowReqData->product_id,
                            'product_qty'       	=> $RowReqData->received_qnty,
                            'product_value'       	=> $RowReqData->received_value,
                            'inventory_date'       	=> date('Y-m-d'),
                            'inventory_type'       	=> 1, 			// stock-in
                            'transaction_type'		=> 'wastage',
                            'global_company_id'     => Auth::user()->global_company_id,
                            'created_by'       		=> Auth::user()->id,
                        ]
                 ); 
				 
				$SSG_STOCK = DB::update("UPDATE tbl_product SET stock_qty = stock_qty - $RowReqData->received_qnty,
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


				} else {
					
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

	// Stock receive for distributor

	private function dist_req_receive_stock($req_id)
	{
		
		if($req_id)
		{
			
			$resultReqData = DB::select("SELECT dr.*,  rdet.product_id, rdet.cat_id, rdet.received_qnty, rdet.received_value
								FROM dist_wastage_requisition dr JOIN dist_wastage_req_details rdet ON rdet.req_id = dr.req_id
								WHERE dr.req_id = '".$req_id."'
								AND dr.req_status = 'received'
								AND dr.is_active = 'YES'
								ORDER BY dr.req_id DESC");
				
					
			foreach($resultReqData as $RowReqData)
			{
				 DB::table('distributor_inventory')->insert(
                        [
                            'req_id'          		=> $RowReqData->req_id,
                            'req_no'          		=> $RowReqData->req_no,
                            'point_id'          	=> $RowReqData->point_id,
                            'distributor_in_charge' => $RowReqData->distributor_in_charge,
                            'cat_id'        		=> $RowReqData->cat_id,
                            'product_id'         	=> $RowReqData->product_id,
                            'product_qty'       	=> $RowReqData->received_qnty,
                            'product_value'       	=> $RowReqData->received_value,
                            'inventory_date'       	=> date('Y-m-d'),
                            'inventory_type'       	=> 1, 			// stock-in
                            'transaction_type'		=> 'wastage',
                            'global_company_id'     => Auth::user()->global_company_id,
                            'created_by'       		=> Auth::user()->id,
                        ]
                 ); 
				 
				$SSG_STOCK = DB::update("UPDATE tbl_product SET stock_qty = stock_qty - $RowReqData->received_qnty,
													mkt_stock = mkt_stock - $RowReqData->received_qnty
													WHERE id = ? AND category_id = ?",
													[$RowReqData->product_id, $RowReqData->cat_id]
												);	 
				 
				$chkStcok = DB::select("SELECT * 
									FROM distributor_stock 
									WHERE	point_id = '".$RowReqData->point_id."'
									AND 	product_id = '".$RowReqData->product_id."'
									AND 	cat_id = '".$RowReqData->cat_id."'
								");
								
				
				//echo '<pre/>'; print_r($chkStcok); exit;				
								
				
				if(sizeof($chkStcok) > 0)
				{
					
					$DepotReqDetUpd = DB::update("UPDATE distributor_stock SET stock_qty = stock_qty + $RowReqData->received_qnty 
													WHERE point_id = ? AND product_id = ? AND cat_id = ?",
													[$RowReqData->point_id, $RowReqData->product_id, $RowReqData->cat_id]
												);	


				} else {
					
					DB::table('distributor_stock')->insert(
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
	
		$stockResult = DB::select("SELECT p.name as ProductName, ds.stock_qty as Stock, (ds.stock_qty * p.depo) as Value
		FROM depot_stock ds JOIN tbl_product p ON ds.product_id = p.id 
		WHERE ds.point_id = '".$pointID."' AND ds.cat_id = '".$catID."' ORDER BY p.name ASC");

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
	
	public function export(Request $Request) 
	{

		$reqID=$Request->get('requisition_id');

		$items  = DB::table('dist_wastage_requisition')
		->select('users.display_name as Depot Name','tbl_point.point_name AS Point Name','tbl_product.companyid AS Company Code','tbl_product.sap_code AS Material Code','tbl_product_category.name AS Category','tbl_product.name AS Material Name','dist_wastage_req_details.req_qnty AS Qty','dist_wastage_req_details.req_value AS Value')
		->join('users', 'users.id', '=', 'dist_wastage_requisition.distributor_in_charge')
		->join('dist_wastage_req_details', 'dist_wastage_req_details.req_id', '=', 'dist_wastage_requisition.req_id')
		->join('tbl_point', 'tbl_point.point_id', '=', 'dist_wastage_requisition.point_id')
		->join('tbl_product_category', 'tbl_product_category.id', '=', 'dist_wastage_req_details.cat_id')
		->join('tbl_product', 'tbl_product.id', '=', 'dist_wastage_req_details.product_id')
		->where('dist_wastage_requisition.req_id', $reqID)
		->orderBy('dist_wastage_requisition.req_id','DESC')                    
		->get();

		$data = array();
		foreach ($items as $items) {
			$data[] = (array)$items;  
		}

//$items = Item::all();
		Excel::create('Download_For_SAP', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');

	}


	public function upload_stock_list()
	{
        $selectedMenu    = 'Upload Stock';                    // Required Variable for menu
        $selectedSubMenu = 'Products Stock List';           // Required Variable for menu
        $pageTitle       = 'Products Stock List'; // Page Slug Title

        
        $stockList  = DB::table('tbl_sap_stock')
        ->select('dDate','company_code','company_name','plant','material_no','material_desc','stock_qty') 
        ->orderBy('tbl_sap_stock.iId','DESC')                    
        ->get();
		
		//echo '<pre/>'; print_r($stockList); exit;

        return view('Depot.depot_stock_list' , compact('selectedMenu','selectedSubMenu','pageTitle','stockList'));  

        
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
    				$date = date('Y-m-d', strtotime($data[$i]['date']));

    				$insert[] = ['dDate' => $date,'company_code' =>$data[$i]['comp_code'], 'company_name' => $data[$i]['comp_name'], 'plant' => $data[$i]['plant'], 'material_no' => $data[$i]['material_no'], 'material_desc' => $data[$i]['material_desc'], 'stock_qty' => $data[$i]['stock'],'global_company_id' =>Auth::user()->global_company_id,'created_by' => Auth::user()->id];
    			}

    			if(!empty($insert)){
    				ProductsStockUploadModel::insert($insert);


    				$items  = DB::table('tbl_sap_stock')
    				->select('stock_qty','material_no')
    				->get();

    				foreach ($items as $items) {
    					$qty = $items->stock_qty;
    					$material_no = $items->material_no;

    					$productQty = DB::table('tbl_product')
    					->select('stock_qty','sap_code')
    					->where('sap_code', $material_no)
    					->first();

    					$totalQty=0;
    					if($productQty->sap_code){
    						$totalQty = $productQty->stock_qty + $items->stock_qty;
    					}
    					DB::table('tbl_product')
    					->where('sap_code',$productQty->sap_code)
    					->update(
    						[
    							'stock_qty'          => $totalQty                  

    						]
    					);

    				}
    				return back()->with('success','Products Stock upload sucessfully.');
    			}




    		}




    	}
    	return back()->with('error','Please Check your file, Something is wrong there.');

    }
	
	public function depot_items_edit(Request $request)
    {
        
        $depotResult  = DB::table('dist_wastage_req_details')
                        ->select('dist_wastage_req_details.req_det_id','dist_wastage_req_details.wastage_qty','dist_wastage_req_details.product_id','dist_wastage_req_details.req_qnty','tbl_product.depo','tbl_product.name')
                        ->join('tbl_product', 'tbl_product.id', '=', 'dist_wastage_req_details.product_id')
                        ->where('dist_wastage_req_details.req_det_id', $request->get('id'))
                        ->first();


        return view('sales/requisitionProcess/companyRequisition/requisition/editReqItems', compact('depotResult'));
    }

    public function depot_items_edit_submit(Request $request)
    {
        
        $price          = $request->get('items_qty') * $request->get('items_price');
        $price2         = $request->get('wastage_qty') * $request->get('items_price');

        DB::table('dist_wastage_req_details')->where('req_det_id',$request->get('id'))->update(
            [
                'req_qnty'         => $request->get('items_qty'),
                'req_value'        => $price,
                'wastage_qty'      => $request->get('wastage_qty'),
                'wastage_value'    => $price2
            ]
        ); 

        return Redirect::back()->with('success', 'Successfully Updated Requisition Product.');
    }


    public function depot_req_items_delete(Request $request)
    {
        DB::table('dist_wastage_req_details')
        ->where('req_id',$request->get('wastageId'))
        ->where('req_det_id',$request->get('id'))
        ->delete();
        
       	return Redirect::back()->with('success', 'Successfully Delete Requisition Product.');             
    }	
}
