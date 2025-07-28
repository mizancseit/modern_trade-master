<?php

namespace App\Http\Controllers\Depot;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

//use App\Models\Sales\ProductsStockUploadModel;

use Hash;
use DB;
use Auth;
use Session;
use Excel;

class DepotClaim extends Controller
{
	/**
	*
	* Created by Zubair Mahmudul Huq
	* Date : 22/12/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

	
	
									/*  Delivery Claim Order Process */
	
	public function claim_order_list(Request $req)
	{
		$selectedMenu    = 'Claim Order';             // Required Variable
        $selectedSubMenu = 'Claim Order';             // Required Variable
        $pageTitle       = 'Claim Order';            // Page Slug Title
		
		$divList = DB::table('tbl_division')->orderBy('div_id','asc')->get();
		
		$whereCond = '';
		if ($req->get('fromReqDate') != '') 
		{
			$fromReqDate = explode('-',$req->get('fromReqDate'));
			$fromDate = $fromReqDate[2] . '-' . $fromReqDate[1] . '-' . $fromReqDate[0]; 
			$whereCond .= " AND req.req_date >= '".$fromDate."' ";
		}
		
		if ($req->get('toReqDate') != '') 
		{
			$toReqDate = explode('-',$req->get('toReqDate'));
			$toDate = $toReqDate[2] . '-' . $toReqDate[1] . '-' . $toReqDate[0]; 
			$whereCond .= " AND req.req_date <= '".$toDate."' ";
		}
		
		if ($req->get('div_id') != '') 
		{
			$whereCond .= " AND p.point_division = '".$req->get('div_id')."'";
		}
		
		if ($req->get('business_type') != '') 
		{
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."'";
		}
		
		if ($req->get('qnty_fillup') != '') 
		{
			$qnty_fillup = $req->get('qnty_fillup');
		}
		
					
		$resultClaimOrdList = DB::select("SELECT req.req_id, req.req_no, req.req_date, 
									p.point_id, p.point_name, p.point_division, p.business_type_id,
									prd.id as prod_id, prd.name as pro_name, prd.depo as depo_price, 
									pc.id as cat_id, pc.name as cat_name,
									u.display_name, ud.sap_code,
									rdet.req_qnty, rdet.delevered_qnty, rdet.received_qnty
									FROM depot_requisition req JOIN depot_req_details rdet ON req.req_id = rdet.req_id
									JOIN tbl_point p ON p.point_id = req.point_id
									JOIN users u ON u.id = req.req_by
									JOIN tbl_user_details ud ON ud.user_id = u.id
									JOIN tbl_product prd ON prd.id = rdet.product_id
									JOIN tbl_product_category pc ON pc.id = rdet.cat_id
									WHERE req.req_status = 'received' AND rdet.delevered_qnty > rdet.received_qnty
									AND req.req_by = '".Auth::user()->id."'
									$whereCond
									ORDER BY req.req_id");
		/*							
		echo "SELECT req.req_id, req.req_no, req.req_date, 
									p.point_id, p.point_name, p.point_division, p.business_type_id,
									prd.id as prod_id, prd.name as pro_name, prd.depo as depo_price, 
									pc.id as cat_id, pc.name as cat_name,
									u.display_name, ud.sap_code,
									rdet.req_qnty, rdet.delevered_qnty, rdet.received_qnty
									FROM depot_requisition req JOIN depot_req_details rdet ON req.req_id = rdet.req_id
									JOIN tbl_point p ON p.point_id = req.point_id
									JOIN users u ON u.id = req.req_by
									JOIN tbl_user_details ud ON ud.user_id = u.id
									JOIN tbl_product prd ON prd.id = rdet.product_id
									JOIN tbl_product_category pc ON pc.id = rdet.cat_id
									WHERE req.req_status = 'delivered' AND rdet.delevered_qnty > rdet.received_qnty
									AND req.req_by = '".Auth::user()->id."'
									$whereCond
									ORDER BY req.req_id";	exit;			*/			

        return view('Depot/claim_order/claim_order_list', compact('selectedMenu','selectedSubMenu','pageTitle',
		'resultClaimOrdList','divList','qnty_fillup'));			
		
	}
	
	
	private function chkProdClaimOrd(Request $request, $req_id)
	{
		if($req_id)
		{	
			foreach($request->input('cat_id') as $row_cat_ID => $cat_id)
			{
				$prod_id 	 = $request->input('prod_id')[$row_cat_ID];
				$claimord_qnty  = 'claimord_qnty_' . $req_id . '_' . $cat_id . '_' . $prod_id;
				if($request->input($claimord_qnty) > 0)
					return true;
			}
		}	
	}
	
	
	public function claim_order_process(Request $request)
	{
	   
        if($request->isMethod('post'))
        {
			
			$req_id_check = array();
			// master process
			foreach($request->input('req_id') as $rowID => $req_id)
			{
			
				if(!in_array($req_id, $req_id_check) and self::chkProdClaimOrd($request, $req_id))
				{
					$req_id_check[] = $req_id;
			
					// update prev main order
					$ReOrdInfo = DB::table('depot_requisition')->where('req_id',$req_id)->update(
								[
									'claim_order_status'        => 	'YES',
									'claim_order_count'         => 	DB::raw('claim_order_count+1')
								]
					); 
					
					
					$point_id = $request->input('point_id')[$rowID];
					$sap_code = $request->input('sap_code')[$rowID];
					$cat_id = $request->input('cat_id')[$rowID];
					$prod_id = $request->input('prod_id')[$rowID];
					$reDate = date('Y-m-d H:i:s'); 
					
					$LastReqId = DB::select("SELECT (MAX(req_id) + 1) as last_req_id FROM depot_requisition");		
					$req_no = $sap_code . date('dmY') . $LastReqId[0]->last_req_id;
					
					/*
					$depoRec = DB::select("SELECT u.id FROM tbl_user_business_scope bs JOIN users u ON u.id = bs.user_id
												WHERE u.user_type_id = 5 and bs.point_id = '".$point_id."' and bs.is_active =0
									");
					
					if(sizeof($depoRec)>0)
					{
					   $depot_in_charge = $req_by = $depoRec[0]->id;
					} else {
						$depot_in_charge = 0;
					}
					
					*/					
						
					$depot_in_charge = $req_by = Auth::user()->id;						
					
					//$req_no = 'req_001';
					
					$reord_id = DB::table('depot_requisition')->insertGetId(
									[ 'depot_in_charge' => $depot_in_charge,
									  'point_id' => $point_id,
									  'req_no' => $req_no,
									  'req_by' => $req_by,
									  'req_date' => $reDate,
									  'sent_by' => $req_by,
									  'sent_date' => $reDate,
									  
									  /*
									  'acknowledge_by' => $req_by,
									  'acknowledge_date' => $reDate,
									  'approved_by' => $req_by,
									  'approved_date' => $reDate,
									  'delivered_by' => $req_by,
									  'delivered_date' => $reDate,
									  */
									  
									  'req_status' => 'send',
									  'is_active' => 'YES',
									  'claim_order_reference' => $req_id, 	
									
									]
					);
					
						
					//foreach($request->input('cat_id') as $row_cat_ID => $cat_id)
					//foreach($request->input('prod_id') as $row_prod_ID => $prod_id)
					
					foreach($request->input('req_id') as $row_req_ID => $req_id)
					{
						
						if($req_id == $row_req_ID)
						{						  
							$claimord_qnty = ''; $cat_id = ''; $prod_id = '';
							$cat_id 	 = $request->input('cat_id')[$row_req_ID];
							$prod_id 	 = $request->input('prod_id')[$row_req_ID];
							$claimord_qnty  = 'claimord_qnty_' . $req_id . '_' . $cat_id . '_' . $prod_id;
							
							if($request->input($claimord_qnty) > 0)
							{
								$totalPrice = $request->input($claimord_qnty) * $request->input('depo_price')[$row_req_ID];

								DB::table('depot_req_details')->insert(
									[
										'req_id'          	=> $reord_id,
										'product_id'      	=> $prod_id,
										'cat_id'        	=> $cat_id,
										'req_qnty'         	=> $request->input($claimord_qnty),
										'req_value'       	=> $totalPrice,
										
										/*
										'approved_qnty'     => $request->input($reord_qnty),
										'approved_value'    => $totalPrice,
										'delevered_qnty'    => $request->input($reord_qnty),
										'delevered_value'   => $totalPrice,
										*/
									]
								); 
							}
						}
					}
				
				} // re-order check					
			
			} // loop closed		
  
            //return Redirect::to('/req-list_product/'.$request->get('req_id'))->with('success', 'Successfully Added.');
			
			return Redirect::back()->with('success', 'Successfully Order has been Claimed.');	
			
        }
		
	}
	
	
	
}
