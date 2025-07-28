<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class VisitController extends Controller
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

    public function ssg_visit()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

		/*
        $routeResult = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                        ->where('tbl_user_business_scope.is_active', 0)                         
                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)  
                        ->where('tbl_user_business_scope.is_active', 0) 
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

        $checkRoutes = DB::table('tbl_order')->select('route_id')
                        ->where('fo_id', Auth::user()->id)  
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->orderBy('order_id','DESC')
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

        if($routeID!='')
        {
            $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','owner')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('rid', $routeID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
        }
        else
        {
            $resultRetailer = NULL;
        }
		*/
		
		$resultRetailer = array();
		$routeID = '';
		$resultPoint    = DB::select("SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."'");
		
		if(sizeof($resultPoint)>0)
		{
			
			$routeResult = DB::table('tbl_route')
                        ->select('*')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('point_id',$resultPoint[0]->point_id )
                        ->get();
		}
		
		

        return view('sales.visit.visitManage', compact('selectedMenu','pageTitle','routeResult','resultRetailer','routeID'));
    }

    public function ssg_retailer(Request $request)
    {
        $routeID = $request->get('route');

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','owner','vAddress','status')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('rid', $routeID)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();

        return view('sales.visit.retailers', compact('resultRetailer','routeID'));
    }

    public function ssg_order_process($retailderid,$routeid)
    {
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->where('tbl_user_business_scope.is_active', 0) 
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
                         ->where('tbl_user_business_scope.is_active', 0) 
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
        
        if(Auth::user()->business_type_id==4){
            $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->whereIN('gid', [1, 2])
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
        }
        
        elseif(Auth::user()->business_type_id==5){
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->whereIN('gid', [1, 3])
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
        }
        
        elseif(Auth::user()->business_type_id==6){
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->whereIN('gid', [2, 3])
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
        }
        
        elseif(Auth::user()->business_type_id==7){
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
        }
        
        else {
        $resultCategory = DB::table('tbl_product_category')
                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                        ->where('status', '0')
                        ->where('gid', Auth::user()->business_type_id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->get();
        }

        /*
        $resultCart     = DB::table('tbl_order')
                        ->where('order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first(); */
                        
        $resultCart     = DB::table('tbl_order')
                        //->where('order_status', '<>', 'Close_Req')
                        ->Where('order_status', '<>', 'Closed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();  

                        

        if(sizeof($resultCart) > 0 )
        {           
            //echo '<pre/>'; print_r($resultCart); exit;    
            //echo $resultCart->order_id; exit;
            $Bucket_1 = DB::select("SELECT SUM(p_total_price) as grand_total_value FROM tbl_order_details WHERE order_id = '".$resultCart->order_id."' and partial_order_id = 'part_1'");       
            $Bucket_2 = DB::select("SELECT SUM(p_total_price) as grand_total_value FROM tbl_order_details WHERE order_id = '".$resultCart->order_id."' and partial_order_id = 'part_2'");       
            $Bucket_3 = DB::select("SELECT SUM(p_total_price) as grand_total_value FROM tbl_order_details WHERE order_id = '".$resultCart->order_id."' and partial_order_id = 'part_3'");       
            $Bucket_4 = DB::select("SELECT SUM(p_total_price) as grand_total_value FROM tbl_order_details WHERE order_id = '".$resultCart->order_id."' and partial_order_id = 'part_4'");       
        
        //echo '<pre/>'; print_r($Bucket_1); exit;  
        } else {
            $Bucket_1 = array();
            $Bucket_2 = array();        
            $Bucket_3 = array();        
            $Bucket_4 = array();        
        }
        
        return view('sales.visit.categoryWithOrder', 
        compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart',
        'Bucket_1', 'Bucket_2', 'Bucket_3', 'Bucket_4'));
    }

    public function ssg_order_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $pointID        = $request->get('point_id');
        $retailerID     = $request->get('retailer_id');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

		/*
        $lastOrderid     = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.order_type','tbl_order.retailer_id','tbl_order.fo_id','tbl_order_details.cat_id')
                         ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')               
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailerID)
                        ->where('tbl_order_details.cat_id',$categoryID)
                        ->where('tbl_order.order_type', 'Ordered')
                        ->orderBy('tbl_order.order_id','DESC')                         
                        ->first();
		*/				
       //dd($retailerID);
	   
	   $lastOrderData	= DB::select("SELECT tbl_order.order_id , tbl_order.order_type, tbl_order.retailer_id, tbl_order.fo_id, tbl_order_details.product_id,
							tbl_order_details.cat_id, tbl_order_details.order_qty, tbl_order_details.p_total_price, tbl_order_details.p_total_price,
							tbl_order_details.wastage_qty
							FROM tbl_order JOIN tbl_order_details ON tbl_order.order_id = tbl_order_details.order_id
								WHERE  tbl_order.fo_id = '".Auth::user()->id."'
								and tbl_order.retailer_id = '".$retailerID."'
								and tbl_order_details.cat_id = '".$categoryID."'
								and (tbl_order_details.order_qty !='' OR tbl_order_details.wastage_qty !='')
								and tbl_order.order_type =  'Ordered'
								order by tbl_order.order_id DESC");	

        $offerTypeCategory = DB::table('tbl_product_category')
                        ->select('id','offer_type')
                        ->where('id', $categoryID)
                        ->first();

        return view('sales/visit/allOrderProductList', compact('resultProduct','categoryID','retailerID','pointID','lastOrderData','offerTypeCategory'));
    }

    public function ssg_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $retailerID     = $request->get('retailer_id');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $lastOrderid     = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.order_type','tbl_order.retailer_id','tbl_order.fo_id','tbl_order_details.cat_id')
                         ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')               
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailerID)
                        ->where('tbl_order_details.cat_id',$categoryID)
                        ->where('tbl_order.order_type', 'Ordered')
                        ->orderBy('tbl_order.order_id','DESC')                         
                        ->first();
       //dd($retailerID);

        $offerTypeCategory = DB::table('tbl_product_category')
                        ->select('id','offer_type')
                        ->where('id', $categoryID)
                        ->first();

        

        return view('sales/visit/allProductList', compact('resultProduct','categoryID','retailerID','lastOrderid','offerTypeCategory'));
    }

    public function ssg_order_manage_category_products(Request $request)
    {
        $categoryID = $request->get('categories');
        $retailerID     = $request->get('retailer_id');
        $order_id     = $request->get('order_id');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();
						
		/*
        $lastOrderid     = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.order_type','tbl_order.retailer_id','tbl_order.fo_id','tbl_order_details.cat_id')
                         ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')               
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailerID)
                        ->where('tbl_order_details.cat_id',$categoryID)
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.order_id', $order_id)
                        ->orderBy('tbl_order.order_id','DESC')                         
                        ->first();
		*/				
       //dd($retailerID);
	   
	    $lastOrderData	= DB::select("SELECT tbl_order.order_id , tbl_order.order_type, tbl_order.retailer_id, tbl_order.fo_id, tbl_order_details.product_id,
							tbl_order_details.cat_id, tbl_order_details.order_qty, tbl_order_details.p_total_price, tbl_order_details.p_total_price,
							tbl_order_details.wastage_qty
							FROM tbl_order JOIN tbl_order_details ON tbl_order.order_id = tbl_order_details.order_id
								WHERE  tbl_order.fo_id = '".Auth::user()->id."'
								and tbl_order.retailer_id = '".$retailerID."'
								and tbl_order_details.cat_id = '".$categoryID."'
								and (tbl_order_details.order_qty !='' OR tbl_order_details.wastage_qty !='')
								and tbl_order.order_type =  'Ordered'
								order by tbl_order.order_id DESC");	
	   

        $offerTypeCategory = DB::table('tbl_product_category')
                        ->select('id','offer_type')
                        ->where('id', $categoryID)
                        ->first();

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.division_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id','tbl_point.point_id','tbl_point.point_name','tbl_division.div_id','tbl_division.div_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                        ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')                         
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

        return view('sales/visit/allOrderProductList', compact('resultProduct','categoryID','retailerID','lastOrderid','lastOrderData','offerTypeCategory','pointID'));
    }
    
   public function ssg_add_to_cart_products(Request $request)
    {
        $autoOrder = DB::table('tbl_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();
    
        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_order_no + 1;
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


        $totalQty   = 0;
        $totalValue = 0;
        $offerType = $request->get('offer_group_type');


        $countRows = count($request->get('qty'));        

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('qty')[$m];
            $totalValue = $totalValue + $request->get('qty')[$m] * $request->get('price')[$m];
        } 


        $resultCart     = DB::table('tbl_order')
                        ->where('order_status', '<>', 'Closed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)
                        ->orderBy('order_id','DESC')                         
                        ->first();  

       //dd(sizeof($resultCart));
        $lastOrderId =0;
        if(sizeof($resultCart)== 0) 
        {
            
            DB::table('tbl_order')->insert(
                [
                    'order_no'              => $orderNo,
                    'auto_order_no'         => $autoOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'distributor_id'        => $request->get('distributor_id'),
                    'point_id'              => $request->get('point_id'),
                    'route_id'              => $request->get('route_id'),
                    'retailer_id'           => $request->get('retailer_id'),
                    'fo_id'                 => Auth::user()->id,
                    'total_qty'             => $totalQty,
                    'total_value'           => $totalValue,
                    'grand_total_value'     => $totalValue,
                    'order_count'           => 1,
                    'order_status'          => 'Close_Req',
                    'closing_req_date'      => date('Y-m-d H:i:s'),
                    'order_type'            => 'Ordered',
                    'entry_by'              => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'entry_date'            => date('Y-m-d h:i:s'),
                    'global_company_id'     => Auth::user()->global_company_id
                    //'is_active'             => 0  is_active 0 is edit option active and 1 is confirm
                ]
            );

                        
             $lastOrderId   = DB::table('tbl_order')
                        ->where('order_status','Close_Req')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)
                        ->orderBy('retailer_id','DESC')                        
                        ->first();
            
            $partial_id = 'part_' . 1;          

            $special_sku = array();
            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='' OR $request->get('wastageQty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    /* Only First Order */
                    DB::table('tbl_order_details')->insert(
                        [
                            'order_id'          => $lastOrderId->order_id,
                            'partial_order_id'  => $partial_id,
                            'is_ordered'        => 'YES',
                            'ordered_date'      => date('Y-m-d H:i:s'),
                            'order_det_status'  => 'Closed',
                            'order_det_type'    => 'Ordered',
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'wastage_qty'       => $request->get('wastageQty')[$m],
                            'p_unit_price'      => $request->get('price')[$m],
                            'p_total_price'     => $totalPrice,
                            'p_grand_total'     => $totalPrice,
                            'offer_group_type'  => $offerType
                        ]
                    );
                
                    $special_sku[]= $request->get('produuct_id')[$m];

                }

            }

            // for FO Information
            
            $this->pre_offer_entry( $lastOrderId->order_id, $orderNo, $autoOrderId, $request->get('distributor_id'), $request->get('retailer_id'), 
                                     $request->get('point_id'), $request->get('route_id'), $request->get('category_id')[0], 
                                     $special_sku, $offerType, $partial_id, $IsUpdate = false );

            return Redirect::to('/order-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Added Add To Cart.');
        }
        
        // if order already placed
        else
        {
            $lastOrderId = $resultCart->order_id;
            
            $order_date = date_create($resultCart->order_date);
            $order_date = date_format($order_date, 'Y-m-d'); 
            
            if($resultCart->pertial_order_track)
            {
              $update_date = date_create($resultCart->pertial_order_track);
              $update_date = date_format($update_date, 'Y-m-d');
            } else {
                $update_date  = '';
            }
            
          
            if( ( $order_date == date('Y-m-d') ) OR ( $update_date == date('Y-m-d') ) )
            {
                $PartialOrderId = 'part_' . $resultCart->order_count;
            } else {
                $PartialOrderId = 'part_' . ($resultCart->order_count + 1);
            }
             
            $oldGrandTotal  = $resultCart->grand_total_value;
            $newGrandTotal  = $oldGrandTotal + $totalValue;

            $oldGrandQty    = $resultCart->total_qty;
            $newGrandQty    = $oldGrandQty + $totalQty;
           
            
            if( ( $order_date == date('Y-m-d') ) OR ( $update_date == date('Y-m-d') ) )
            {
                $order_count = $resultCart->order_count;
            } else {
                $order_count = $resultCart->order_count + 1;
            }
            
            DB::table('tbl_order')->where('order_id', $lastOrderId)
                ->where('fo_id', Auth::user()->id)
                //->where('order_type', 'Ordered')
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [
                    
                    'total_qty'             => $newGrandQty,
                    'total_value'           => $newGrandTotal,
                    'grand_total_value'     => $newGrandTotal,
                    'order_count'           => $order_count,
                    'order_status'          => 'Close_Req',
                    'update_by'             => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost(),
                    'pertial_order_track'   => date('Y-m-d h:i:s')
                ]
            );
            

            $special_sku = array();

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='' OR $request->get('wastageQty')[$m]!='')
                {
                    $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];


                    $checkItemsExiting = DB::table('tbl_order_details')
                                    ->select('tbl_order_details.*','tbl_order.order_id','tbl_order.order_type')
                                    ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                    ->where('tbl_order_details.order_id', $lastOrderId)
                                    ->where('tbl_order_details.partial_order_id', $PartialOrderId)
                                    ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                                    ->first();      

                    if(sizeof($checkItemsExiting)>0)
                    {
                        $upMainQty = $request->get('qty')[$m];
                        $upMainWas = $request->get('wastageQty')[$m];
                        $upMainPri = $upMainQty * $request->get('price')[$m];

                     
                        
                        DB::table('tbl_order_details')
                        ->where('tbl_order_details.order_id', $lastOrderId)
                        ->where('tbl_order_details.partial_order_id', $PartialOrderId)
                        ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                        ->update(
                            [
                                'order_qty'         => $upMainQty,
                                'wastage_qty'       => $upMainWas,
                                'p_total_price'     => $upMainPri,
                                'p_grand_total'     => $upMainPri,
                                'order_update_date' => date('Y-m-d H:i:s'), 
                                'offer_group_type'  => $offerType
                            ]
                        );
                        
                    }
                    else
                    {
                      
                        
                        DB::table('tbl_order_details')->insert(
                            [
                                'order_id'          => $lastOrderId,
                                'partial_order_id'  => $PartialOrderId,
                                'order_det_status'  => 'Closed',
                                'is_ordered'        => 'YES',   
                                'ordered_date'      => date('Y-m-d H:i:s'), 
                                'order_det_type'    => 'Ordered',
                                'cat_id'            => $request->get('category_id')[$m],
                                'product_id'        => $request->get('produuct_id')[$m],
                                'order_qty'         => $request->get('qty')[$m],
                                'wastage_qty'       => $request->get('wastageQty')[$m],
                                'p_unit_price'      => $request->get('price')[$m],
                                'p_total_price'     => $totalPrice,
                                'p_grand_total'     => $totalPrice,
                                'offer_group_type'  => $offerType
                            ]
                        );
                        
                    }

                    $special_sku[] = $request->get('produuct_id')[$m];
                }
            }

            $this->pre_offer_entry( $lastOrderId, $orderNo, $autoOrderId, $request->get('distributor_id'), $request->get('retailer_id'), 
                                     $request->get('point_id'), $request->get('route_id'), $request->get('category_id')[0], 
                                     $special_sku, $offerType, $PartialOrderId, $IsUpdate = false);

            return Redirect::to('/order-process/'.$request->get('retailer_id').'/'.$request->get('route_id'))->with('success', 'Successfully Updated Add To Cart.');
        
        }       
    }

    
    public function pre_offer_entry( $lastOrderId = 0, $orderNo = 0, $autoOrderId = 0, $requestDistributorId = 0, $requestRetailerId = 0 , 
                                     $requestPointId = 0, $requestRouteId = 0, $requestCategoryID = 0, $special_sku = array(), 
                                     $offerType = 0, $partial_id = 0, $ISUpdate = false)
    {
        
        
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
            $resultSpecialOffers = 0;
            $point = 0;
            $route = 0;
            $businessType = Auth::user()->business_type_id;
            

            $currentDay = date('Y-m-d');

            // Regular offer criteria

            $resultRegularOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='1'
             AND tbl_bundle_offer.iBusinessType='".$businessType."'
             AND tbl_bundle_offer_scope.iDivId='".$division_id."' 
             AND '".$currentDay."' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");
             
            

            if(sizeof($resultRegularOffers)>0)
            {           

                $regularpoint = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                     AND iPointId='".$point_id."'
                     "); 
                     
                    

                $regularroute = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                         AND iPointId='".$requestPointId."' AND iRouteId = '".$requestRouteId."'
                         ");
                         
         
            }
            
            //echo '<pre/>'; print_r($regularpoint); exit

            // Special offer criteria

            $resultSpecialOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='2'
            AND tbl_bundle_offer.iBusinessType='".$businessType."'
             AND tbl_bundle_offer_scope.iDivId='".$division_id."' 
             AND '".$currentDay."' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultSpecialOffers)>0)
            {           

                $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                     AND iPointId='".$point_id."'
                     "); 


                $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                         AND iPointId='".$requestPointId."' AND iRouteId = '".$requestRouteId."'
                         ");
                         
                         
                
                
                
                         
            }
            
            //Sharif Regular Offer Starts----------------->//

            if(sizeof($resultRegularOffers)>0 AND sizeof($regularpoint)>0 AND sizeof($regularroute)>0)
            {
                
                $catid       =  $requestCategoryID;
                $lastOrderId =  $lastOrderId;
                $total_odd  =0;
                $total_odd1=0;
                $total_odd2=0;
                
                
                    $itemsDelete = DB::table('tbl_order_free_qty')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
           
                    $andItemsDelete = DB::table('tbl_order_regular_and_free_qty')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
                
                
                $checkRegularSkuProducts =  DB::table('tbl_regular_sku_products')
                                            ->select('slab','catid','sku_id','qty','value')
                                            ->where('catid',$catid)
                                            ->whereIn('sku_id',$special_sku)
                                            ->groupBy('sku_id')
                                            ->get();
                                         

                $skuid = array();

                foreach($checkRegularSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }

                if($offerType==1)
                {
                    $totalRegularQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->whereNotIn('product_id', $skuid)
                                    ->sum('order_qty');
                }else{
                    $totalRegularQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->sum('order_qty');
                }
                
                //echo $totalRegularQty; exit;

                if($totalRegularQty>0){
                    $regularProducts =  DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('slab',$totalRegularQty)
                                       ->where('status',0)
                                       ->get();

         
                    if(sizeof($regularProducts) >0 )
                    {
                        
                
                        foreach($regularProducts as $regularProducts) {
                        
                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularProducts->slab,
                                    'slab_count'            => 1,
                                    'catid'                 => $catid,
                                    'product_id'            => $regularProducts->pid,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularProducts->qty,
                                    'free_value'            => $regularProducts->value,
                                    'total_free_value'      => $regularProducts->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                ]
                            );
                            
                            if($regularProducts->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularProducts->slab,
                                    'slab_count'            => 1,
                                    'catid'                 => $catid,
                                    'product_id'            => $regularProducts->and_pid,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularProducts->and_qty,
                                    'free_value'            => $regularProducts->and_value,
                                    'total_free_value'      => $regularProducts->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                            }
                        
                        }
                    }
                    else
                    {
                        $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab<'".$totalRegularQty."'");


                        $maxSlab = DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('slab', $maxValue[0]->slab)
                                       ->where('status',0)
                                       ->get();

                        

                         //dd($maxSlab);

                        if(sizeof($maxSlab) >0 )
                        {
                            foreach($maxSlab as $maxSlab) {
                            
                                $mainQty = (int)($totalRegularQty/$maxSlab->slab);
                               //dd($mainQty);
                                $total_odd = $totalRegularQty - ($mainQty * $maxSlab->slab);
                                $total_free = $maxSlab->qty * $mainQty;
                                $total_value = $maxSlab->value * $total_free;
                                $and_total_free = $maxSlab->and_qty * $mainQty;
                                $and_total_value = $maxSlab->and_value * $and_total_free;
                            

                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlab->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlab->value,
                                        'total_free_value'      => $maxSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );
                                
                                if($maxSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id, 
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'slab_count'            => $mainQty,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->and_pid,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $maxSlab->and_value,
                                    'total_free_value'      => $maxSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                        $lastSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd."')");

                        
                        
                        if(sizeof($lastSlab) >0 )
                        {

                             foreach($lastSlab as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                //dd($total_odd1);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;
                                $and_total_free = $lastSlab->and_qty * $mainQty1;
                                $and_total_value = $lastSlab->and_value * $and_total_free;

                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $lastSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastSlab->and_value,
                                        'total_free_value'      => $lastSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd1."')");
                        
                        if(sizeof($lastOddSlab) >0 )
                        {
                          
                           foreach($lastOddSlab as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $total_free;
                                $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                $and_total_value = $lastOddSlab->and_value * $and_total_free;


                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $lastOddSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastOddSlab->and_value,
                                        'total_free_value'      => $lastOddSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }
                        
                        
                        $lastOddSlab2 =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd2."' AND status=0) AND status=0");


                        if(sizeof($lastOddSlab2) >0 )
                        {
                          
                           foreach($lastOddSlab2 as $lastOddSlab2) {

                                $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                $total_free = $lastOddSlab2->qty * $mainQty3;
                                $total_value = $lastOddSlab2->value * $total_free;
                                $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab2->slab,
                                        'slab_count'            => $mainQty3,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab2->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab2->value,
                                        'total_free_value'      => $lastOddSlab2->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab2->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab2->slab,
                                        'slab_count'            => $mainQty3,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab2->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastOddSlab2->and_value,
                                        'total_free_value'      => $lastOddSlab2->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                        
                    } 

                }   // Regular offer cat wise end
                ////// Start SKU Regular offer
                if($offerType==1)
                {
                    foreach($checkRegularSkuProducts as $sku) {
                    
                        $totalRegularSkuQty =  DB::table('tbl_order_details')
                                        ->where('order_id',$lastOrderId)
                                        ->where('cat_id',$catid)
                                        ->where('product_id', $sku->sku_id)
                                        ->sum('order_qty');
                                        
                        $regularSku =  DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                        ->where('catid',$catid)
                        ->where('sku_id',$sku->sku_id)
                        ->where('slab',$totalRegularSkuQty)
                        ->where('status',0)                       
                        ->get();


                        if(sizeof($regularSku) >0 )
                        {

                            foreach($regularSku as $regularSku) {
                            
                                 $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id, 
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularSku->slab,
                                    'slab_count'            => 1,
                                    'catid'                 => $catid,
                                    'product_id'            => $regularSku->pid,
                                    'sku_id'                => $regularSku->sku_id,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularSku->qty,
                                    'free_value'            => $regularSku->value,
                                    'total_free_value'      => $regularSku->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                if($regularSku->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id, 
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularSku->slab,
                                    'slab_count'            => 1,
                                    'catid'                 => $catid,
                                    'sku_id'                => $regularSku->sku_id,
                                    'product_id'            => $regularSku->and_pid,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularSku->and_qty,
                                    'free_value'            => $regularSku->and_value,
                                    'total_free_value'      => $regularSku->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }


                            }


                        }
                        else
                        {
                            $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<'".$totalRegularSkuQty."' AND status=0");


                            $maxSlabSku = DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                           ->where('catid',$catid)
                                           ->where('sku_id',$sku->sku_id)
                                           ->where('slab', $maxValueSku[0]->slab)
                                           ->where('status',0)                       
                                           ->get();

                            if(sizeof($maxSlabSku) >0 )
                            {

                                foreach($maxSlabSku as $maxSlabSku) {

                                    $mainQty = (int)($totalRegularSkuQty/$maxSlabSku->slab);
                                   //dd($mainQty);
                                    $total_odd = $totalRegularSkuQty - ($mainQty * $maxSlabSku->slab);
                                    $total_free = $maxSlabSku->qty * $mainQty;
                                    $total_value = $maxSlabSku->value * $total_free;
                                    $and_total_free = $maxSlabSku->and_qty * $mainQty;
                                    $and_total_value = $maxSlabSku->and_value * $and_total_free;
                                

                                   $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlabSku->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'product_id'            => $maxSlabSku->pid,
                                            'sku_id'                => $maxSlabSku->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $maxSlabSku->value,
                                            'total_free_value'      => $maxSlabSku->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                    if($maxSlabSku->and_qty>0){

                                      DB::table('tbl_order_regular_and_free_qty')->insert(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $regularSku->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'sku_id'                => $regularSku->sku_id,
                                        'product_id'            => $regularSku->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $regularSku->and_qty,
                                        'free_value'            => $regularSku->and_value,
                                        'total_free_value'      => $regularSku->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }

                                }
                              
                            }

                            $lastSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                            if(sizeof($lastSlabSku) >0 )
                            {

                                 foreach($lastSlabSku as $lastSlab) {

                                    $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                    $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                    $total_free = $lastSlab->qty * $mainQty1;
                                    $total_value = $lastSlab->value * $total_free;

                                   $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $catid,
                                            'product_id'            => $lastSlab->pid,
                                            'sku_id'                => $lastSlab->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $lastSlab->value,
                                            'total_free_value'      => $lastSlab->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );


                                    if($lastSlab->and_qty>0){

                                      DB::table('tbl_order_regular_and_free_qty')->insert(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'product_id'            => $lastSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastSlab->and_qty,
                                        'free_value'            => $lastSlab->and_value,
                                        'total_free_value'      => $lastSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }

                           $lastOddSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE  catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlabSku) >0 )
                            {
                              
                               foreach($lastOddSlabSku as $lastOddSlab) {

                                    $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                    $total_free = $lastOddSlab->qty * $mainQty2;
                                    $total_value = $lastOddSlab->value * $total_free;

                                    $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $catid,
                                            'product_id'            => $lastOddSlab->pid,
                                            'sku_id'                => $lastOddSlab->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $lastOddSlab->value,
                                            'total_free_value'      => $lastOddSlab->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                    if($lastOddSlab->and_qty>0){

                                      DB::table('tbl_order_regular_and_free_qty')->insert(
                                        [
                                        'order_id'              => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $regular_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'product_id'            => $lastOddSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastOddSlab->and_qty,
                                        'free_value'            => $lastOddSlab->and_value,
                                        'total_free_value'      => $lastOddSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }
                        } // SKU else close
                    } // foreach loop close
                }
                
                
                /* Zubair Clear Bundle Offer Begin */
                    DB::table('tbl_order_gift')->where('orderid',$lastOrderId)->delete();
                /* Zubair Clear Bundle Offer END */
                

            } //Sharif Regular Offer Ends-------------------->//


            ///////////////// SPECIAL OFFER START //////////////////

            if(sizeof($resultSpecialOffers)>0 AND sizeof($point)>0 AND sizeof($route)>0)
            {
                $catid=$requestCategoryID;
               // $lastOrderId = $lastOrderId->order_id;
                $total_odd=0;
                $total_odd1=0;
                $total_odd2=0;
                
                
                    $itemsDelete = DB::table('tbl_order_special_free_qty')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
           
                    $andItemsDelete = DB::table('tbl_order_special_and_free_qty')
                            ->where('order_id',$lastOrderId)
                            ->where('catid',$catid)
                            ->delete();
                

                $checkSkuProducts =  DB::table('tbl_special_sku_products')
                                        ->select('slab','catid','sku_id','qty','value')
                                        ->where('catid',$catid)
                                        ->where('status',0) 
                                        ->whereIn('sku_id',$special_sku)
                                        ->groupBy('sku_id')
                                        ->get();
               

                $skuid = array();

                foreach($checkSkuProducts as $skua) {
                    $skuid[]= $skua->sku_id;
                }

                if($offerType==1)
                {
                    $totalSpecialQty =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->whereNotIn('product_id', $skuid)
                                ->sum('order_qty');
                }else{
                    $totalSpecialQty =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->sum('order_qty');
                }

                //dd($totalSpecialQty);
                if($totalSpecialQty>0 )
                {
                    $specialProducts =  DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                    ->where('catid',$catid)
                    ->where('slab',$totalSpecialQty)
                    ->where('status',0)                       
                    ->get();

                    

                    if(sizeof($specialProducts) >0 )
                    {

                        foreach($specialProducts as $specialProducts) {
                        
                        

                         $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'partial_order_id'      => $partial_id, 
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialProducts->slab,
                            'slab_count'            => 1,
                            'catid'                 => $catid,
                            'product_id'            => $specialProducts->pid,
                            'distributor_id'        => $requestDistributorId,
                            'point_id'              => $requestPointId,
                            'route_id'              => $requestRouteId,
                            'retailer_id'           => $requestRetailerId,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $specialProducts->qty,
                            'free_value'            => $specialProducts->value,
                            'total_free_value'      => $specialProducts->value,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            //'hostname'              => $request->getHttpHost()
                            ]
                            );


                            if($specialProducts->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $special_cat_id,
                                'auto_order_no'         => $autoOrderId,
                                'partial_order_id'      => $partial_id, 
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $specialProducts->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'product_id'            => $specialProducts->and_pid,
                                'distributor_id'        => $requestDistributorId,
                                'point_id'              => $requestPointId,
                                'route_id'              => $requestRouteId,
                                'retailer_id'           => $requestRetailerId,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $specialProducts->and_qty,
                                'free_value'            => $specialProducts->and_value,
                                'total_free_value'      => $specialProducts->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                //'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }


                    }
                    else
                    {
                        $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid='".$catid."' AND slab<'".$totalSpecialQty."' AND status=0");


                        $maxSlab = DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('slab', $maxValue[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlab) >0 )
                        {

                            foreach($maxSlab as $maxSlab) {
                                

                                $mainQty = (int)($totalSpecialQty/$maxSlab->slab);
                              
                                $total_odd = $totalSpecialQty - ($mainQty * $maxSlab->slab);
                                $total_free = $maxSlab->qty * $mainQty;
                                $total_value = $maxSlab->value * $total_free;
                                $and_total_free = $maxSlab->and_qty * $mainQty;
                                $and_total_value = $maxSlab->and_value * $and_total_free;

                            

                              $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlab->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlab->value,
                                        'total_free_value'      => $maxSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($maxSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'partial_order_id'      => $partial_id, 
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'slab_count'            => $mainQty,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->and_pid,
                                    'distributor_id'        => $requestDistributorId,
                                    'point_id'              => $requestPointId,
                                    'route_id'              => $requestRouteId,
                                    'retailer_id'           => $requestRetailerId,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $maxSlab->and_value,
                                    'total_free_value'      => $maxSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }
                          
                        }

                        $lastSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                        if(sizeof($lastSlab) >0 )
                        {

                             foreach($lastSlab as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                //dd($total_odd1);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;
                                $and_total_free = $lastSlab->and_qty * $mainQty1;
                                $and_total_value = $lastSlab->and_value * $and_total_free;

                                $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $lastSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $special_cat_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastSlab->and_value,
                                        'total_free_value'      => $lastSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                        if(sizeof($lastOddSlab) >0 )
                        {
                          
                           foreach($lastOddSlab as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $total_free;
                                $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                                $and_total_value = $lastOddSlab->and_value * $and_total_free;


                                $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $lastOddSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $special_cat_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastOddSlab->and_value,
                                        'total_free_value'      => $lastOddSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                        $lastOddSlab2 =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid='".$catid."' AND slab<='".$total_odd2."' AND status=0) AND status=0");


                        if(sizeof($lastOddSlab2) >0 )
                        {
                          
                           foreach($lastOddSlab2 as $lastOddSlab2) {

                                $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                $total_free = $lastOddSlab2->qty * $mainQty3;
                                $total_value = $lastOddSlab2->value * $total_free;
                                $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                                $and_total_value = $lastOddSlab->and_value * $and_total_free;

                                $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab2->slab,
                                        'slab_count'            => $mainQty3,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab2->pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab2->value,
                                        'total_free_value'      => $lastOddSlab2->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab2->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $special_cat_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab2->slab,
                                        'slab_count'            => $mainQty3,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab2->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $and_total_free,
                                        'free_value'            => $lastOddSlab2->and_value,
                                        'total_free_value'      => $lastOddSlab2->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }


                    } //  cagegory else close
                }

                ////// Start SKU special offer
                if($offerType==1)
                {
                    foreach($checkSkuProducts as $sku) {
                    
                        $totalSpecialSkuQty =  DB::table('tbl_order_details')
                                        ->where('order_id',$lastOrderId)
                                        ->where('cat_id',$catid)
                                        ->where('product_id', $sku->sku_id)
                                        ->sum('order_qty');
                          //dd($totalSpecialSkuQty);              
                       
                         $specialSku =  DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                        ->where('catid',$catid)
                        ->where('sku_id',$sku->sku_id)
                        ->where('slab',$totalSpecialSkuQty)
                        ->where('status',0)                       
                        ->get();


                        if(sizeof($specialSku) >0 )
                        {

                            foreach($specialSku as $specialSku) {
                            
                            $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'partial_order_id'      => $partial_id, 
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $specialSku->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'product_id'            => $specialSku->pid,
                                'sku_id'                => $specialSku->sku_id,
                                'distributor_id'        => $requestDistributorId,
                                'point_id'              => $requestPointId,
                                'route_id'              => $requestRouteId,
                                'retailer_id'           => $requestRetailerId,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $specialSku->qty,
                                'free_value'            => $specialSku->value,
                                'total_free_value'      => $specialSku->value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                //'hostname'              => $request->getHttpHost()
                                ]
                                );

                                if($specialSku->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $specialSku->slab,
                                        'slab_count'            => 1,
                                        'catid'                 => $catid,
                                        'sku_id'                => $specialSku->sku_id,
                                        'product_id'            => $specialSku->and_pid,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $specialSku->and_qty,
                                        'free_value'            => $specialSku->and_value,
                                        'total_free_value'      => $specialSku->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }


                        }
                        else
                        {
                            $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<'".$totalSpecialSkuQty."' AND status=0");


                            $maxSlabSku = DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                           ->where('catid',$catid)
                                           ->where('sku_id',$sku->sku_id)
                                           ->where('slab', $maxValueSku[0]->slab)
                                           ->where('status',0)                       
                                           ->get();

                            if(sizeof($maxSlabSku) >0 )
                            {

                                foreach($maxSlabSku as $maxSlabSku) {

                                    $mainQty = (int)($totalSpecialSkuQty/$maxSlabSku->slab);
                                   //dd($mainQty);
                                    $total_odd = $totalSpecialSkuQty - ($mainQty * $maxSlabSku->slab);
                                    $total_free = $maxSlabSku->qty * $mainQty;
                                    $total_value = $maxSlabSku->value * $total_free;
                                    $and_total_free = $maxSlabSku->and_qty;
                                    $and_total_value =$maxSlabSku->and_qty * $maxSlabSku->and_value;
                                

                                   $sp_sku_free_id =  DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlabSku->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'product_id'            => $maxSlabSku->pid,
                                            'sku_id'                => $maxSlabSku->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $maxSlabSku->value,
                                            'total_free_value'      => $maxSlabSku->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                    if($maxSlabSku->and_qty>0){

                                      DB::table('tbl_order_special_and_free_qty')->insert(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $sp_sku_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $maxSlabSku->slab,
                                            'slab_count'            => $mainQty,
                                            'catid'                 => $catid,
                                            'sku_id'                => $maxSlabSku->sku_id,
                                            'product_id'            => $maxSlabSku->and_pid,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $maxSlabSku->and_qty,
                                            'free_value'            => $maxSlabSku->and_value,
                                            'total_free_value'      => $maxSlabSku->and_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }

                                }
                              
                            }

                            $lastSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                            if(sizeof($lastSlabSku) >0 )
                            {

                                 foreach($lastSlabSku as $lastSlab) {

                                    $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                    $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                    //dd($total_odd1);
                                    $total_free = $lastSlab->qty * $mainQty1;
                                    $total_value = $lastSlab->value * $total_free;
                                    $and_total_value = $lastSlab->and_value * $lastSlab->and_qty;

                                    $sp_sku_free_id =  DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $catid,
                                            'product_id'            => $lastSlab->pid,
                                            'sku_id'                => $lastSlab->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $lastSlab->value,
                                            'total_free_value'      => $lastSlab->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                    if($lastSlab->and_qty>0){

                                      DB::table('tbl_order_special_and_free_qty')->insert(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $sp_sku_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastSlab->slab,
                                            'slab_count'            => $mainQty1,
                                            'catid'                 => $catid,
                                            'sku_id'                => $lastSlab->sku_id,
                                            'product_id'            => $lastSlab->and_pid,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $lastSlab->and_qty,
                                            'free_value'            => $lastSlab->and_value,
                                            'total_free_value'      => $lastSlab->and_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }

                           $lastOddSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE  catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid='".$catid."' AND sku_id='".$sku->sku_id."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                            if(sizeof($lastOddSlabSku) >0 )
                            {
                              
                               foreach($lastOddSlabSku as $lastOddSlab) {

                                    $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                    $total_free = $lastOddSlab->qty * $mainQty2;
                                    $total_value = $lastOddSlab->value * $total_free;
                                    $and_total_value = $lastOddSlab->and_value * $lastOddSlab->and_qty;

                                    $sp_sku_free_id =  DB::table('tbl_order_special_free_qty')->insertGetId(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $catid,
                                            'product_id'            => $lastOddSlab->pid,
                                            'sku_id'                => $lastOddSlab->sku_id,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $total_free,
                                            'free_value'            => $lastOddSlab->value,
                                            'total_free_value'      => $lastOddSlab->value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                    );

                                    if($lastOddSlab->and_qty>0){

                                      DB::table('tbl_order_special_and_free_qty')->insert(
                                        [
                                            'order_id'             => $lastOrderId,
                                            'order_no'              => $orderNo,
                                            'special_id'            => $sp_sku_free_id,
                                            'auto_order_no'         => $autoOrderId,
                                            'partial_order_id'      => $partial_id, 
                                            'order_date'            => date('Y-m-d h:i:s'),
                                            'slab'                  => $lastOddSlab->slab,
                                            'slab_count'            => $mainQty2,
                                            'catid'                 => $catid,
                                            'sku_id'                => $lastOddSlab->sku_id,
                                            'product_id'            => $lastOddSlab->and_pid,
                                            'distributor_id'        => $requestDistributorId,
                                            'point_id'              => $requestPointId,
                                            'route_id'              => $requestRouteId,
                                            'retailer_id'           => $requestRetailerId,
                                            'fo_id'                 => Auth::user()->id,
                                            'global_company_id'     => Auth::user()->global_company_id,
                                            'total_free_qty'        => $lastOddSlab->and_qty,
                                            'free_value'            => $lastOddSlab->and_value,
                                            'total_free_value'      => $lastOddSlab->and_value,
                                            'created_by'            => Auth::user()->id,
                                            'ipaddress'             => request()->ip(),
                                            //'hostname'              => $request->getHttpHost()
                                        ]
                                        );
                                    }
                                }
                            }
                        } // SKU else close
                    } // foreach loop close
                    
                }   
                
                
                /* Zubair Clear Bundle Offer Begin */ //added Dec_09_2018
                        DB::table('tbl_order_gift')->where('orderid',$lastOrderId)->delete();
                /* Zubair Clear Bundle Offer END */
                
                // Special Value wise commission Start

                

                $totalCatValue =  DB::table('tbl_order_details')
                                    ->select('p_total_price')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->sum('p_total_price');

                $checkGroupId = DB::select("SELECT * FROM tbl_special_value_wise_category
                                JOIN tbl_special_values_wise ON tbl_special_values_wise.id = tbl_special_value_wise_category.svwid
                                WHERE tbl_special_value_wise_category.categoryid='".$catid."' AND '".$totalCatValue."' BETWEEN min AND max");

                if(sizeof($checkGroupId) >0 )
                {

                     DB::table('tbl_special_temp_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->where('retailer_id', $requestRetailerId)
                            ->delete();
                   
                 DB::table('tbl_special_temp_commission')->insert(
                    [
                    'order_id'              => $lastOrderId,
                    'group_id'              => $checkGroupId[0]->group_id,
                    'partial_order_id'      => $partial_id,
                    'offer_id'              => $checkGroupId[0]->svwid,
                    'catid'                 => $catid,
                    'distributor_id'        => $requestDistributorId,
                    'point_id'              => $requestPointId,
                    'route_id'              => $requestRouteId,
                    'retailer_id'           => $requestRetailerId,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'cat_value'             => $totalCatValue,
                    'created_by'            => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    //'hostname'              => $request->getHttpHost()
                    ]
                    );
                   
                }

                $totalValue = DB::table('tbl_special_temp_commission')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {
                    /*$orderCount = DB::table('tbl_special_commission')
                            ->where('order_id',$lastOrderId)
                            ->get()
                            ->count();

                    if($orderCount>0)
                        {
                            DB::table('tbl_special_commission')
                            ->where('order_id',$lastOrderId)
                            ->delete();
                        }*/

                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_special_values_wise WHERE status = 1 AND group_id='".$totalOfferValue->group_id."' AND '".$commissionValue."' BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {

                        DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('group_id', $totalOfferValue->group_id)
                            ->where('retailer_id', $requestRetailerId)
                            ->where('is_point_wise',0)
                            ->delete();
                       
                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'partial_order_id'      => $partial_id,
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $requestDistributorId,
                        'point_id'              => $requestPointId,
                        'route_id'              => $requestRouteId,
                        'retailer_id'           => $requestRetailerId,
                        'fo_id'                 => Auth::user()->id,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        //'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }

                // Special Value wise commission end


            } 

            if(sizeof($resultSpecialOffers)>0 AND sizeof($point)>0 AND sizeof($route)>0)
            {
                $catid=$requestCategoryID;
                $total_odd=0;
                $total_odd1=0;
                $total_odd2=0;
                
                
                $itemsDelete = DB::table('tbl_order_free_qty_commission')
                        ->where('order_id',$lastOrderId)
                        ->where('catid',$catid)
                        ->delete();
       
                $totalSpecialQty =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->sum('order_qty');
               

                if($totalSpecialQty>0 )
                {
                    $specialProducts =  DB::table('tbl_offer_qty_commission_value')->select('catid','slab','per_pcs','value','status')
                    ->where('catid',$catid)
                    ->where('slab',$totalSpecialQty)
                    ->where('status',0)                       
                    ->get();

                    if(sizeof($specialProducts) >0 )
                    {
                        foreach($specialProducts as $specialProducts) {
                        
                         $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'partial_order_id'      => $partial_id, 
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialProducts->slab,
                            'slab_count'            => 1,
                            'catid'                 => $catid,
                            'free_value'            => $specialProducts->value,
                            'total_free_value'      => $specialProducts->value,
                            'distributor_id'        => $requestDistributorId,
                            'point_id'              => $requestPointId,
                            'route_id'              => $requestRouteId,
                            'retailer_id'           => $requestRetailerId,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            //'hostname'              => $request->getHttpHost()
                            ]
                            );
                        }
                    }
                    else
                    {
                        $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<'".$totalSpecialQty."' AND status=0");


                        $maxSlab = DB::table('tbl_offer_qty_commission_value')->select('slab','catid','per_pcs','value')
                                       ->where('catid',$catid)
                                       ->where('slab', $maxValue[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlab) >0 )
                        {
                            foreach($maxSlab as $maxSlab) {

                                $mainQty = (int)($totalSpecialQty/$maxSlab->slab);
                                $total_odd = $totalSpecialQty - ($mainQty * $maxSlab->slab);
                                //$total_free = $maxSlab->qty * $mainQty;
                                $total_value = $maxSlab->value * $mainQty;

                              $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlab->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'free_value'            => $maxSlab->value,
                                        'total_free_value'      => $total_value,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );
                            }
                          
                        }

                        $lastSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd."'  AND status=0)  AND status=0");


                        if(sizeof($lastSlab) >0 )
                        {
                             foreach($lastSlab as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                //dd($total_odd1);
                                //$total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $mainQty1;

                                $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $total_value,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );
                            }
                        }

                       $lastOddSlab =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd1."' AND status=0) AND status=0");


                        if(sizeof($lastOddSlab) >0 )
                        {
                          
                           foreach($lastOddSlab as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                                //$total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $mainQty2;

                                $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $total_value,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                            }
                        }

                        $lastOddSlab2 =  DB::select("SELECT * FROM tbl_offer_qty_commission_value WHERE  catid='".$catid."' AND slab=(SELECT MAX(slab) AS slab  FROM tbl_offer_qty_commission_value WHERE catid='".$catid."' AND slab<='".$total_odd2."' AND status=0) AND status=0");


                        if(sizeof($lastOddSlab2) >0 )
                        {
                          
                           foreach($lastOddSlab2 as $lastOddSlab2) {

                                $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                                //$total_free = $lastOddSlab2->qty * $mainQty3;
                                $total_value = $lastOddSlab2->value * $mainQty3;

                                $special_cat_id = DB::table('tbl_order_free_qty_commission')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'partial_order_id'      => $partial_id, 
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab2->slab,
                                        'slab_count'            => $mainQty3,
                                        'catid'                 => $catid,
                                        'free_value'            => $lastOddSlab2->value,
                                        'total_free_value'      => $total_value,
                                        'distributor_id'        => $requestDistributorId,
                                        'point_id'              => $requestPointId,
                                        'route_id'              => $requestRouteId,
                                        'retailer_id'           => $requestRetailerId,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        //'hostname'              => $request->getHttpHost()
                                    ]
                                );

                            }
                        }


                    } 
                }
            } //  cagegory Qty wise commission else close

            $pointCheckOffer     = DB::table('tbl_point_wise_offer')
                        ->where('tbl_point_wise_offer.iBusinessType',$businessType)
                        ->where('tbl_point_wise_offer.iPointId',$requestPointId)
                        ->where('dEndDate','>=',$currentDay)
                        ->where('tbl_point_wise_offer.iStatus',0)                      
                        ->first();

           if(sizeof($pointCheckOffer)>0)
           {
                $totalCatValue =  DB::table('tbl_order_details')
                                    ->select('p_total_price')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->sum('p_total_price');

                $checkGroupId     = DB::table('tbl_point_wise_value_category')
                                    ->join('tbl_point_wise_value', 'tbl_point_wise_value.id', '=', 'tbl_point_wise_value_category.point_value_id')
                                    ->where('tbl_point_wise_value.point_id',$requestPointId)
                                    ->where('tbl_point_wise_value_category.categoryid',$catid)
                                    ->first();
               

                /*$checkGroupId = DB::select("SELECT * FROM tbl_point_wise_value_category
                            JOIN tbl_point_wise_value ON tbl_point_wise_value.id = tbl_point_wise_value_category.point_value_id
                                WHERE tbl_point_wise_value.point_id='".$requestPointId."' AND tbl_point_wise_value_category.categoryid='".$catid."' AND '".$totalCatValue."' BETWEEN min AND max");*/
                 //dd($checkGroupId);

                if(sizeof($checkGroupId) >0 )
                {
                    DB::table('tbl_point_temp_commission_cat')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->where('retailer_id', $requestRetailerId)
                            ->delete();
                   
                     DB::table('tbl_point_temp_commission_cat')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'group_id'              => $checkGroupId->group_id,
                        'partial_order_id'      => $partial_id,
                        'offer_id'              => $pointCheckOffer->iId,
                        'catid'                 => $catid,
                        'distributor_id'        => $requestDistributorId,
                        'point_id'              => $requestPointId,
                        'route_id'              => $requestRouteId,
                        'retailer_id'           => $requestRetailerId,
                        'fo_id'                 => Auth::user()->id,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'cat_value'             => $totalCatValue,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        //'hostname'              => $request->getHttpHost()
                        ]
                        );
                   
                }

                $totalValue = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                    ->where('order_id', $lastOrderId)
                                    ->groupBy('group_id')
                                    ->get(); 

                

                foreach ($totalValue as $totalOfferValue) 
                {

                    $commissionValue = $totalOfferValue->total;
                    $checkCatValue = DB::select("SELECT * FROM tbl_point_wise_value WHERE status = 0 AND group_id='".$totalOfferValue->group_id."' AND '".$commissionValue."' BETWEEN min AND max LIMIT 1");


                    if(sizeof($checkCatValue) >0 )
                    {

                        $checkSpecial = DB::table('tbl_point_temp_commission_cat')
                                    ->select('order_id','group_id','catid')
                                    ->where('order_id', $lastOrderId)
                                    ->where('group_id', $totalOfferValue->group_id)
                                    ->get(); 

                        //dd($checkSpecial);

                        $sp_catid = array();

                        foreach($checkSpecial as $sku) {
                            $sp_catid[]= $sku->catid;
                        }

                        DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->whereIn('catid', $sp_catid)
                            ->where('retailer_id', $requestRetailerId)
                            ->delete();

                        DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('group_id', $totalOfferValue->group_id)
                            ->where('retailer_id', $requestRetailerId)
                            ->where('is_point_wise',1)
                            ->delete();


                       
                     DB::table('tbl_special_commission')->insert(
                        [
                        'order_id'              => $lastOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'partial_order_id'      => $partial_id,
                        'offer_id'              => $checkCatValue[0]->id,
                        'group_id'              => $totalOfferValue->group_id,
                        'distributor_id'        => $requestDistributorId,
                        'point_id'              => $requestPointId,
                        'route_id'              => $requestRouteId,
                        'retailer_id'           => $requestRetailerId,
                        'fo_id'                 => Auth::user()->id,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'commission'            => $checkCatValue[0]->commission_rate,
                        'total_free_value'      => $commissionValue,
                        'is_point_wise'         => 1,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        //'hostname'              => $request->getHttpHost()
                        ]
                        );
                       
                    }

                }

                
            } // Point  wise Value commission end
        
    } // pre offer func closed
    
    
    public function ssg_bucket($pointid,$routeid,$retailderid,$partial_id)
    {

        //dd($pointid,$routeid,$retailderid,$partial_id);

        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title

        // for FO Information

        //dd($routeid,$retailderid);

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
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
                         //->where('tbl_user_business_scope.is_active', 0) 
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->first();

        //dd($resultDistributor);

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

        /*
        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_special_commission.catid AS valuecatid','tbl_special_commission.commission','tbl_special_commission.total_free_value')

                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')

                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->leftJoin('tbl_special_commission', 'tbl_order_details.order_id', '=', 'tbl_special_commission.order_id')

                        ->where('tbl_order.order_type','Ordered')                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();
        */
        
         $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type',
                        'tbl_order.order_no','tbl_order.retailer_id','tbl_special_commission.catid AS valuecatid',
                        'tbl_special_commission.commission','tbl_special_commission.total_free_value','tbl_order_details.ordered_date')

                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')

                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->leftJoin('tbl_special_commission', 'tbl_order_details.order_id', '=', 'tbl_special_commission.order_id')

                        ->where('tbl_order.order_status', '<>', 'Closed')                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->where('tbl_order_details.partial_order_id',$partial_id)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

       //dd($resultCartPro);
        
        /*
        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','auto_order_no')
                        ->where('order_type','Ordered')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();
        */              

                        
        
        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','auto_order_no','order_count','grand_total_value')
                        ->where('order_status', '<>', 'Closed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();

        //dd($resultInvoice);
                

        // for offer related start

        $resultBundleOffers = DB::table('tbl_bundle_offer')
                        ->select('tbl_bundle_offer.iOfferType','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_offer.iStatus','tbl_bundle_offer_scope.iOfferId','tbl_bundle_offer.global_company_id','tbl_bundle_offer.iBusinessType')
                        ->Join('tbl_bundle_offer_scope','tbl_bundle_offer_scope.iOfferId','=','tbl_bundle_offer.iId')
                        ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                        ->where('tbl_bundle_offer_scope.iPointId', $pointid)
                        ->where('tbl_bundle_offer.iOfferType', 3)
                        ->where('tbl_bundle_offer.iStatus', 1)
                        ->first();

        //dd($resultBundleOffers);

        $resultBundleOffersCategories = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_bundle_category.offerId','tbl_bundle_category.categoryId')
                            ->leftJoin('tbl_bundle_category','tbl_bundle_offer.iId','=','tbl_bundle_category.offerId')
                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                            ->get();

        $allowCategory = array();
        foreach ($resultBundleOffersCategories as $value) 
        {
            $allowCategory[] = $value->categoryId;
        }

        // if(sizeof($resultBundleOffers)>0)
        // {
        //     $currentDay = date('Y-m-d');
        //     $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //      FROM
        //      tbl_bundle_offer
        //      LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //      WHERE 
        //      tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");

        //     if(sizeof($resultBundleOffers)>0)
        //     {
        //         $resultBundleOffers  = DB::table('tbl_order_details')
        //                 ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
        //                 ->leftJoin('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
        //                 ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')

        //                 ->where('tbl_order.order_type','Ordered')                        
        //                 ->where('tbl_order.fo_id',Auth::user()->id)                        
        //                 ->where('tbl_order.retailer_id',$retailderid)
        //                 ->whereIn('tbl_product_category.id',$allowCategory)
        //                 ->groupBy('tbl_order_details.cat_id')                        
        //                 ->get();

        //         dd($resultBundleOffers);

        //         if(sizeof($resultBundleOffers)>0)
        //         {
        //             $currentDay = date('Y-m-d');
        //             $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //              FROM
        //              tbl_bundle_offer
        //              LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //              WHERE 
        //              tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");
        //         }
        //     }
        // }

        //dd($resultBundleOffers);

        $resultBundleOffersGift = DB::table('tbl_order_gift')
                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_gift.proid')
                                ->where('retailer_id', $retailderid)
                                ->where('global_company_id', Auth::user()->global_company_id)
                                ->where('fo_id',Auth::user()->id)
                                ->where('orderid',$resultInvoice->order_id)
                                ->first();

        //dd($resultInvoice->order_id);

        $offerid = '';
        if(sizeof($resultBundleOffersGift)>0)
        {
            $offerid = $resultBundleOffersGift->offerid;
        }

        $resultBundleOfferType = DB::table('tbl_bundle_products')->select('offerId','productType')
                                ->where('offerId', $offerid)                                
                                ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_products.id','tbl_bundle_products.productType','tbl_product.depo')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->join('tbl_product', 'tbl_product.id', '=', 'tbl_bundle_product_details.id')
                                ->where('og.offerId', $offerid)
                                ->where('og.retailer_id', $retailderid)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            // $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
            //                     ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.offerid','og.proid','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')
                               
            //                     ->leftJoin('tbl_product','og.proid','=','tbl_product.id')

            //                     ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.giftName')

            //                     ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
            //                     ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

            //                     ->where('og.offerId', $offerid)
            //                     ->where('og.retailer_id', $retailderid)
            //                     ->where('og.global_company_id', Auth::user()->global_company_id)
            //                     ->where('og.fo_id',Auth::user()->id)
            //                     ->where('og.orderid',$resultInvoice->order_id)
            //                     ->first();

            $resultBundleSelectedGift = DB::table('tbl_order_gift')
                                    ->where('orderid',$resultInvoice->order_id)
                                    ->first();                                    

            $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->select('tbl_bundle_product_details.*','tbl_product.id','tbl_product.name','tbl_product.depo')
                                    ->leftJoin('tbl_product','tbl_bundle_product_details.giftName','=','tbl_product.id')
                                    ->where('offerId',$resultBundleSelectedGift->offerid)
                                    ->where('slabId',$resultBundleSelectedGift->slab_id)
                                    ->where('groupid',$resultBundleSelectedGift->groupid)
                                    ->get();


        }

        $orderID = DB::table('tbl_order')                                
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->where('point_id', $pointid)
                    ->where('route_id', $routeid)
                    ->where('retailer_id', $retailderid)                                
                    ->where('fo_id',Auth::user()->id)
                    ->where('order_type', 'Ordered')                                
                    ->first();

        if(sizeof($orderID)>0)
        {
            $oid = $orderID->order_id;
        }
        else
        {
            $oid ='';
        }

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->groupBy('order_id')                              
                        ->first();

        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $resultInvoice->order_id)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('is_point_wise', 0)                           
                        ->get();
//dd($oid);
        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('is_point_wise', 1)                          
                        ->get();

    $prevoiusBalanceCommission = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id',$retailderid)
                    ->first();

                ////////// Offer check /////////////

        $checkExclusiveAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $resultInvoice->order_id)
                    ->whereNull('status')  //added items
                    ->count();

        $checkExclusiveAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $resultInvoice->order_id)
                    ->where('status', 0)  //added items
                    ->count();


        $checkRegularAvailableItems = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $resultInvoice->order_id)
                    ->whereNull('status')  //added items
                    ->count();

        $checkRegularAdded = DB::table('tbl_order_special_free_qty')
                    ->where('order_id', $resultInvoice->order_id)
                    ->where('status', 0)  //added items
                    ->count();


        $today = date('Y-m-d');            
        $resultBundle = DB::table('tbl_bundle_offer')
                        ->select('iId','vOfferName','dBeginDate','dEndDate')
                        ->where('iOfferType',3)
                        ->whereDate('dEndDate','>=',$today)
                        ->first();
                                    
        $offerId = '';
        if(sizeof($resultBundle)>0)
        {
            $offerId = $resultBundle->iId;
        }

        $netAmount = $resultInvoice->grand_total_value + 0;       

        $resultOfferRange = DB::select("SELECT * FROM tbl_bundle_slab WHERE iOfferId='$offerId' AND '$netAmount' BETWEEN iMinRange AND iMaxRange");

        $offerRangeId=''; 
        if(sizeof($resultOfferRange)>0)
        {
            $offerRangeId = $resultOfferRange[0]->iId;
        }

        $resultBundleAvailableItems = DB::table('tbl_bundle_product_details')
                        ->where('offerId', $offerId) 
                        ->where('slabId', $offerRangeId) 
                        ->count();

        $resultBundleAddedItems = DB::table('tbl_order_gift')
                        ->where('orderid', $resultInvoice->order_id)
                        ->count();


        return view('sales/visit/bucket',compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice','resultBundleOffers','resultBundleOffersGift','specialOffers','commissionWiseItem','specialValueWise','partial_id','prevoiusBalanceCommission','pointValueWise','checkExclusiveAvailableItems','checkExclusiveAdded','checkRegularAvailableItems','checkRegularAdded','resultBundleAvailableItems','resultBundleAddedItems'));
    }
    
    
    public function ssg_bucket_offer($pointid,$routeid,$retailderid,$partialOrder)
    {

        //dd($pointid,$routeid,$retailderid);
        $selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'Bucket';           // Page Slug Title

        // for FO Information

        //dd($routeid,$retailderid);

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_user_business_scope.is_active', 0) 
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
                         ->where('tbl_user_business_scope.is_active', 0) 
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

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid',
                        'tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id',
                        'tbl_order.order_status', 'tbl_order.order_type',
                        'tbl_order.order_no','tbl_order.retailer_id','tbl_special_commission.catid AS valuecatid',
                        'tbl_special_commission.commission','tbl_special_commission.total_free_value')

                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')

                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->leftJoin('tbl_special_commission', 'tbl_order_details.order_id', '=', 'tbl_special_commission.order_id')

                        ->where('tbl_order.order_status','Close_Req')                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

       //dd($resultCartPro); exit;

        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','auto_order_no')
                        ->where('order_status','Close_Req')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->first();


        // for offer related start

        $resultBundleOffers = DB::table('tbl_bundle_offer')
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->where('iBusinessType', Auth::user()->business_type_id)
                            ->where('iOfferType', 3)
                            ->orderBy('iId', 'DESC')
                            ->first();

        //dd($resultBundleOffers);

        $resultBundleOffersCategories = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_bundle_category.offerId','tbl_bundle_category.categoryId')
                            ->leftJoin('tbl_bundle_category','tbl_bundle_offer.iId','=','tbl_bundle_category.offerId')
                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                            ->get();

        $allowCategory = array();
        foreach ($resultBundleOffersCategories as $value) 
        {
            $allowCategory[] = $value->categoryId;
        }

        // if(sizeof($resultBundleOffers)>0)
        // {
        //     $currentDay = date('Y-m-d');
        //     $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //      FROM
        //      tbl_bundle_offer
        //      LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //      WHERE 
        //      tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");

        //     if(sizeof($resultBundleOffers)>0)
        //     {
        //         $resultBundleOffers  = DB::table('tbl_order_details')
        //                 ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
        //                 ->leftJoin('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
        //                 ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')

        //                 ->where('tbl_order.order_type','Ordered')                        
        //                 ->where('tbl_order.fo_id',Auth::user()->id)                        
        //                 ->where('tbl_order.retailer_id',$retailderid)
        //                 ->whereIn('tbl_product_category.id',$allowCategory)
        //                 ->groupBy('tbl_order_details.cat_id')                        
        //                 ->get();

        //         dd($resultBundleOffers);

        //         if(sizeof($resultBundleOffers)>0)
        //         {
        //             $currentDay = date('Y-m-d');
        //             $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //              FROM
        //              tbl_bundle_offer
        //              LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //              WHERE 
        //              tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");
        //         }
        //     }
        // }

        //dd($resultBundleOffers);

        $resultBundleOffersGift = DB::table('tbl_order_gift')
                                ->where('retailer_id', $retailderid)
                                ->where('global_company_id', Auth::user()->global_company_id)
                                ->where('fo_id',Auth::user()->id)
                                ->where('orderid',$resultInvoice->order_id)
                                ->first();

        $offerid = '';
        if(sizeof($resultBundleOffersGift)>0)
        {
            $offerid = $resultBundleOffersGift->offerid;
        }

        $resultBundleOfferType = DB::table('tbl_bundle_products')->select('offerId','productType')
                                ->where('offerId', $offerid)                                
                                ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.offerId', $offerid)
                                ->where('og.retailer_id', $retailderid)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.offerid','og.proid','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')
                               
                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.giftName')

                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.offerId', $offerid)
                                ->where('og.retailer_id', $retailderid)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();


        }

        $orderID = DB::table('tbl_order')                                
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->where('point_id', $pointid)
                    ->where('route_id', $routeid)
                    ->where('retailer_id', $retailderid)                                
                    ->where('fo_id',Auth::user()->id)
                    ->where('order_status', 'Close_Req')                                
                    ->first();

        //dd($orderID);

        if(sizeof($orderID)>0)
        {
            $oid = $orderID->order_id;
        }
        else
        {
            $oid ='';
        }

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $oid)
                        ->groupBy('order_id')                              
                        ->first();

        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.free_id','osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $oid)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $oid)   
                        ->where('is_point_wise', 0)                         
                        ->where('partial_order_id', $partialOrder)                           
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $oid)    
                        ->where('is_point_wise', 1)                        
                        ->where('partial_order_id', $partialOrder)                           
                        ->get();

        $prevoiusBalanceCommission = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id',$retailderid)
                    ->first();

        return view('sales/visit/bucket_offer', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice','resultBundleOffers','resultBundleOffersGift','specialOffers','commissionWiseItem','specialValueWise','prevoiusBalanceCommission','pointValueWise'));
    }


   public function ssg_bucket_bundle_gifts(Request $request)
    {
        //dd($request->all);

        $offerId = $request->get('offerMainId');
        $status  = $request->get('status');
        $netAmount = $request->get('netAmount');
        $orderid = $request->get('orderid');

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.division_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id','tbl_point.point_id','tbl_point.point_name','tbl_division.div_id','tbl_division.div_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                        ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')                         
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

    /// Exceptional products group

     /// Exceptional products group
        $partialOrderId = DB::table('tbl_order')
        ->where('order_id', $orderid) 
        ->first();

        if(sizeof($partialOrderId)>0){
            $partialId= 'part_'.$partialOrderId->order_count;
        }

     $bundleCategoriesEx  = DB::table('tbl_bundle_except_category')
            ->select('categoryId')
            ->where('status',0)
            ->where('global_company_id',Auth::user()->global_company_id)
            ->get();

            $data = collect($bundleCategoriesEx)->map(function($x){ return (array) $x; })->toArray();

        $checkDelivery = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
        ->where('order_id', $orderid) 
        ->whereNotIn('cat_id', $data)
        ->first();

        if(sizeof($checkDelivery)>0 && $checkDelivery->totalValue>0) {
            // for distributor
        $bundleDeliveryVlue  = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(delivered_value) AS totalValue'))
        ->where('order_id', $orderid)  
        ->whereNotIn('cat_id', $data) 
        ->first();

        $partialBundleVlue  = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(p_total_price) AS totalValue'))
        ->where('order_id', $orderid)  
        ->whereNotIn('cat_id', $data)
        ->where('partial_order_id', $partialId)
        ->first();

            $resultBundleVlue =$partialBundleVlue->totalValue + $bundleDeliveryVlue->totalValue;
//dd($partialBundleVlue->totalValue,$bundleDeliveryVlue->totalValue;);
        } 
        else
        {
        $orderBundleVlue  = DB::table('tbl_order_details')
        ->select('order_id','cat_id',DB::raw('SUM(p_total_price) AS totalValue'))
        ->where('order_id', $orderid)  
        ->whereNotIn('cat_id', $data) 
        ->first();

        $resultBundleVlue = $orderBundleVlue->totalValue;

        }
        
      
        $netBundleValue = 0;
        if(sizeof($resultBundleVlue)>0)
        {
            $netBundleValue = $resultBundleVlue;
        }

        $resultOffer = DB::table('tbl_bundle_offer')
                        ->select('tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_offer.iStatus','tbl_bundle_offer_scope.iOfferId')
                        ->Join('tbl_bundle_offer_scope','tbl_bundle_offer_scope.iOfferId','=','tbl_bundle_offer.iId')
                        ->where('tbl_bundle_offer.iId', $offerId)
                        ->where('tbl_bundle_offer.iStatus', 1)
                        ->where('tbl_bundle_offer_scope.iPointId', $pointID)
                        ->first();

        $offerRangeId=''; 
        if(sizeof($resultOffer)>0){
            $resultOfferRange = DB::select("SELECT * FROM tbl_bundle_slab WHERE iOfferId='$offerId' AND '$netBundleValue' BETWEEN iMinRange AND iMaxRange");

            if(sizeof($resultOfferRange)>0)
            {
                $offerRangeId = $resultOfferRange[0]->iId;
            }

        }
        //dd($offerId,$netAmount,$offerRangeId);

        return view('sales.offer.bundleGiftContent', compact('resultOffer','offerId','offerType','status','offerId','offerRangeId'));

    }

    public function ssg_bundle_gifts_added(Request $request)
    {

        //print_r($request->all());
        //exit();
        //dd($request->all());

        if($request->get('status')==0)
        {
            if(sizeof($request->get('giftid'))>0)
            {

                $slab_id=0;
                $groupid=0;
                $proType=0;

                foreach ($request->get('giftid') as $value) 
                {
                    
                   $proTypeKey = explode('_', $value);

                   $slab_id = $proTypeKey[2];
                   $groupid = $proTypeKey[0];
                   $proType = $proTypeKey[1];
                }

                // echo $slab_id.'<br />';
                // echo $groupid.'<br />';
                // echo $proType.'<br />';

                $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->where('offerId',$request->get('offerid'))
                                    ->where('slabId',$slab_id)
                                    ->where('groupid',$groupid)
                                    ->get();

                 DB::table('tbl_order_gift')
                ->where('fo_id',Auth::user()->id)
                ->where('global_company_id',Auth::user()->global_company_id)
                ->where('retailer_id',$request->get('retailderid'))
                ->where('offerid',$request->get('offerid'))
                ->where('orderid',$request->get('orderid'))
                ->delete();
                 
                foreach ($resultBundleOffersGift as $value) 
                {
                    DB::table('tbl_order_gift')->insert(
                        [
                            'global_company_id'     => Auth::user()->global_company_id,
                            'retailer_id'           => $request->get('retailderid'),    
                            'fo_id'                 => Auth::user()->id,    
                            'proid'                 => $value->giftName,
                            'proType'               => $proType, 
                            'offerid'               => $request->get('offerid'),
                            'free_qty'              => $value->stockQty,
                            'orderid'               => $request->get('orderid'),
                            'cat_id'                => $value->categoryId,
                            'slab_id'               => $slab_id,
                            'groupid'               => $groupid,
                            'status'                => 1
                        ]
                    );
                }

                $request->session()->put('offersSelected','bundle');
                return 1;                
            }
        }
        else if($request->get('status')==1 || $request->get('status')==2)
        {

            if(sizeof($request->get('giftid'))>0)
            {

                $slab_id=0;
                $groupid=0;
                $proType=0;

                foreach ($request->get('giftid') as $value) 
                {
                    
                   $proTypeKey = explode('_', $value);

                   $slab_id = $proTypeKey[2];
                   $groupid = $proTypeKey[0];
                   $proType = $proTypeKey[1];
                }

                // echo $slab_id.'<br />';
                // echo $groupid.'<br />';
                // echo $proType.'<br />';

                $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->where('offerId',$request->get('offerid'))
                                    ->where('slabId',$slab_id)
                                    ->where('groupid',$groupid)
                                    ->get();

                $fo = DB::table('tbl_order')
                                    ->select('order_id','fo_id')
                                    ->where('order_id',$request->get('orderid'))
                                    ->first();

                 DB::table('tbl_order_gift')
                //->where('fo_id',Auth::user()->id)
                ->where('global_company_id',Auth::user()->global_company_id)
                ->where('retailer_id',$request->get('retailderid'))
                ->where('offerid',$request->get('offerid'))
                ->where('orderid',$request->get('orderid'))
                ->delete();
                 
                foreach ($resultBundleOffersGift as $value) 
                {
                    DB::table('tbl_order_gift')->insert(
                        [
                            'global_company_id'     => Auth::user()->global_company_id,
                            'retailer_id'           => $request->get('retailderid'),    
                            'fo_id'                 => $fo->fo_id,    
                            'proid'                 => $value->giftName,
                            'proType'               => $proType, 
                            'offerid'               => $request->get('offerid'),
                            'free_qty'              => $value->stockQty,
                            'orderid'               => $request->get('orderid'),
                            'cat_id'                => $value->categoryId,
                            'slab_id'               => $slab_id,
                            'groupid'               => $groupid,
                            'status'                => 1
                        ]
                    );
                }

                $request->session()->put('offersSelected','bundle');
                return 1;                
            }
        }
        
        $request->session()->put('offersSelected','bundle');
        return 0;
    }

    public function ssg_confirm_order(Request $request)
    {

        //dd($request->all());
        $previousBalanceRetailer = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id', $request->get('retailderid'))
                    ->first();

        $previousBalance = 0;
        if(sizeof($previousBalanceRetailer)>0)
        {
            $previousBalance = $previousBalanceRetailer->reminding_commission_balance;
        }

        $currentBalanceRetailer = $previousBalance + $request->get('totalFreeValueWiseCommissionBalance');             

        $buyAmount = $request->get('totalFreeValue') - $request->get('totalFreeValueWiseCommissionBalance');

        DB::table('tbl_retailer')->where('retailer_id', $request->get('retailderid'))->update(
            [
                'reminding_commission_balance' => $request->get('totalFreeValueWiseCommissionBalance')
                ]
            );

            DB::table('tbl_retailer_commission_history')->insert(
                [
                    'orderid'                   => $request->get('orderid'),
                    'retailerid'                => $request->get('retailderid'),
                    'distributorid'             => Auth::user()->id,
                    'foid'                      => $request->get('foMainId'),
                    'retailer_order_balance'    => $request->get('netAmount'),
                    'total_commission_amount'   => $request->get('totalFreeValue'),
                    'total_buy_commission_amount' => $buyAmount,
                    'balance_amount'            => $request->get('totalFreeValueWiseCommissionBalance'), 
                    'routeid'                   => $request->get('routeid'), 
                    'pointid'                   => $request->get('pointid'), 
                    'date'                      => date('Y-m-d')
                ]
            );

           //echo $request->get('offer_type'); 
        DB::table('ims_tbl_visit_order')->insert(
            [
                'disid'                 => $request->get('distributorID'),                
                'retailerid'            => $request->get('retailderid'),    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $request->get('routeid'),  
                'visit'                 => 1,    
                'order'                 => 1,    
                'order_no'              => $request->get('orderid'), 
                'remarks'               => '',    
                'nonvisitedremarks'     => '',
                'entrydate'             => date('Y-m-d H:i:s'),
                'date'                  => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 3
            ]
        );

        DB::table('tbl_order')->where('order_id', $request->get('orderid'))->where('fo_id', Auth::user()->id)->update(
            [
                'order_status'  => 'Closed',
                'order_type'  => 'Confirmed',
                'total_discount_percentage' => $request->get('memo_commission_value'), 
                'total_discount_rate'       => $request->get('memo_commission_rat'),
                'order_date'                => date('Y-m-d H:i:s')
            ]
        );

        DB::table('tbl_order_details')
                ->where('order_id', $request->get('orderid'))
                ->update(
                    [
                        'order_det_type'            => 'Confirmed',
                        'order_det_status'          => 'Closed' 
                    ]
            );
        
       
        if($request->get('offer_type') == 'exclusive')
        {

            DB::table('tbl_order_free_qty')->where('order_id',$request->get('orderid'))->delete();
        }

        if($request->get('offer_type') == 'regular' OR $request->get('offer_type') == '1')
        {

            DB::table('tbl_order_special_free_qty')->where('status','!=',3)->where('order_id',$request->get('orderid'))->delete();
        }
        
       

        return Redirect::to('/visit')->with('success', 'Successfully Confirmed Order');
    }

    
    public function ssg_order_closed($order_id,$partialOrder)
    {
        
       //initiate closed request
        DB::table('tbl_order')->where('order_id', $order_id)->where('fo_id', Auth::user()->id)->update(
            [
                'order_status'  => 'Close_Req', //'Closed',
                'closing_req_date'  => date('Y-m-d')
            ]
        );
        
        $OrdData = DB::table('tbl_order')
                        ->where('order_id', $order_id)
                        ->first();
        
        //return Redirect::to('/visit')->with('success', 'Successfully Closed Order');
         session()->put('offersSelected','regular');

        return Redirect::to('/bucket_offer'. '/' . $OrdData->point_id . '/' . $OrdData->route_id . '/' . $OrdData->retailer_id. '/' . $partialOrder)->with('success', 'Successfully Initiate Close Request');
    
    }
    
    public function ssg_delete_order($orderID,$retailderid,$routeid)
    {
       
        DB::table('ims_tbl_visit_order')->where('order_no', $orderID)->delete();
        DB::table('tbl_order_details')->where('order_id', $orderID)->delete();
        DB::table('tbl_order_gift')->where('global_company_id', Auth::user()->global_company_id)
        ->where('orderid', $orderID)->delete();
        DB::table('tbl_order_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order_regular_and_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order_special_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();
        DB::table('tbl_order_special_and_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_special_commission')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_special_temp_commission')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order')->where('order_id', $orderID)->delete();


        return Redirect::to('/order-process/'.$retailderid.'/'.$routeid)->with('success', 'Successfully Deleted Order.');   
    }
    
    /*public function ssg_delete_order(Request $request)
    {
        //echo 'Hellow'; exit;
        $orderid        = $request->get('orderID');
        $retailderid    = $request->get('retailderid');
        $routeid        = $request->get('routeid');

        //dd($request->all());

        $lastOrderId = DB::table('tbl_order')->select('order_type','fo_id','order_id','order_no')
                    ->where('order_id',$orderid)->first();

        DB::table('tbl_order_details')->where('order_id', $orderid)->delete();
        
        DB::table('ims_tbl_visit_order')->where('order_no', $lastOrderId->order_no)->delete();
      
        DB::table('tbl_order')->where('order_id', $orderid)->delete();
       
        return 0;      
    }*/
    
    public function ssg_order_manage_closed($order_id,$partialOrder)
    {
        
        DB::table('tbl_order_details')
                ->where('order_id', $order_id)
                ->update(
                    [
                        'order_det_type'            => 'Confirmed',
                        'order_det_status'          => 'Closed' 
                    ]
            );
            
        DB::table('tbl_order')->where('order_id', $order_id)->where('fo_id', Auth::user()->id)->update(
            [
                'order_status'  => 'Closed',
                'order_type'  => 'Confirmed',
                'closing_date'  => date('Y-m-d')
            ]
        );
        
        $OrdData = DB::table('tbl_order')
                        ->where('order_id', $order_id)
                        ->first();
        
        session()->put('offersSelected','regular');
     
        return Redirect::to('/bucket-edit'. '/' . $order_id . '/'. $OrdData->point_id . '/' . $OrdData->route_id . '/' . $OrdData->retailer_id. '/' . $partialOrder)->with('success', 'Successfully Order Closed');
    
    }
    
    /*
    public function ssg_delete_order_old(Request $request)
    {
        $orderid        = $request->get('orderID');
        $retailderid    = $request->get('retailderid');
        $routeid        = $request->get('routeid');

        //dd($request->all());

        $lastOrderId = DB::table('tbl_order')->select('order_type','fo_id','order_id','order_no')
                    ->where('order_id',$orderid)->first();

        $orderID     = $orderid;

        $items      = DB::table('tbl_order_details')
                    ->where('order_id',$orderID)                   
                    ->get();

        foreach ($items as $value) 
        {
            DB::table('tbl_order_details')->where('order_id', $value->order_id)->delete();
        }
        
        DB::table('ims_tbl_visit_order')->where('order_no', $lastOrderId->order_no)->delete();
        DB::table('tbl_order_gift')->where('global_company_id', Auth::user()->global_company_id)
        ->where('orderid', $orderID)->delete();
        DB::table('tbl_order_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order_regular_and_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order_special_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();
        DB::table('tbl_order_special_and_free_qty')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_special_commission')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_special_temp_commission')->where('global_company_id', Auth::user()->global_company_id)
        ->where('order_id', $orderID)->delete();

        DB::table('tbl_order')->where('order_id', $orderID)->delete();
        
        //added by zubair retailer balance
        //DB::table('retailer_credit_ledger')->where('retailer_invoice_no', $lastOrderId->order_no)->delete();
       
        return 0;      
    }
    */

    public function ssg_items_edit(Request $request)
    {

        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');

        /*
        $resultPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.order_det_id','tbl_order_details.order_id','tbl_order_details.product_id','tbl_order_details.order_qty','tbl_order_details.order_qty','tbl_order_details.wastage_qty','tbl_order_details.p_unit_price','tbl_order_details.offer_group_type','tbl_product.name')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                        ->where('tbl_order_details.order_det_id', $request->get('id'))
                        ->first();
        */

        
        $resultPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.order_det_id','tbl_order_details.order_id','tbl_order_details.product_id',
                        'tbl_order_details.order_qty','tbl_order_details.order_qty','tbl_order_details.wastage_qty',
                        'tbl_order_details.p_unit_price','tbl_product.name','tbl_order_details.offer_group_type')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                        ->where('tbl_order_details.order_det_id', $request->get('id'))
                        ->first();

        $resultCatDefault  = DB::table('tbl_product_category')
                        ->select('id','offer_type')                        
                        ->where('id', $request->get('catID'))
                        ->first();

        return view('sales/visit/editItems', compact('resultPro','pointid','retailderid','routeid','catid','resultCatDefault'));
    }

    
    public function ssg_items_edit_submit(Request $request)
    {
        
        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');
        $orderID        = $request->get('order_id');
        $skuID          = $request->get('skuID');
        $offer_group_type = $request->get('offer_group_type');

        $price          = $request->get('items_qty') * $request->get('items_price');
        
        $SQLOrdMast = DB::select("SELECT ordM.order_id,  ordM.order_no,  ordM.auto_order_no, ordM.distributor_id
                     FROM tbl_order ordM WHERE ordM.order_id = '".$orderID."'");
                    
        $SQLOrdDet = DB::select("SELECT * FROM tbl_order_details WHERE order_det_id = '".$request->get('id')."'");          
        
        if(sizeof($SQLOrdDet)>0)
        {
            $special_sku[] = $SQLOrdDet[0]->product_id;
        }
                    
        
        DB::table('tbl_order_details')->where('order_det_id',$request->get('id'))->update(
            [
                'order_qty'         => $request->get('items_qty'),
                'wastage_qty'       => $request->get('items_wastage'),
                'p_total_price'     => $price,
                'p_grand_total'     => $price,
                'ordered_date'      => date('Y-m-d H:i:s'),
                'offer_group_type'  => $request->get('offer_group_type')
            ]
        );          

        $totalOrder = DB::table('tbl_order_details')
                    ->select('order_id', DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(p_grand_total) AS grandTotal'),
                                                                                DB::raw('SUM(p_total_price) AS totalValue'))
                    ->where('order_id', $orderID)
                    ->first();


        $orderUpdate= DB::table('tbl_order')->where('order_id', $orderID)->update(
                        [
                            'total_qty'         => $totalOrder->totalQty,                
                            'total_value'       => $totalOrder->totalValue,  
                            'grand_total_value' => $totalOrder->grandTotal
                        ]
                    );

        if(sizeof($SQLOrdMast)>0 && sizeof($SQLOrdDet)>0)
        {
            $this->pre_offer_entry( $orderID, $SQLOrdMast[0]->order_no, $SQLOrdMast[0]->auto_order_no, 
                                     $SQLOrdMast[0]->distributor_id, $retailderid, 
                                     $pointid, $routeid, $catid, $special_sku, $offer_group_type, 
                                     $SQLOrdDet[0]->partial_order_id, $IsUpdate = true);
        }   
                    

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');    
        
    }
    
    public function ssg_items_edit_submit_old(Request $request)
    {
        $pointid        = $request->get('pointID');
        $retailderid    = $request->get('retailderID');
        $routeid        = $request->get('routeID');
        $catid          = $request->get('catID');
        $orderID        = $request->get('order_id');
        $skuID          = $request->get('skuID');
        $offer_group_type = $request->get('offer_group_type');

        $price          = $request->get('items_qty') * $request->get('items_price');

        
        DB::table('tbl_order_details')->where('order_det_id',$request->get('id'))->update(
            [
                'order_qty'         => $request->get('items_qty'),
                'wastage_qty'       => $request->get('items_wastage'),
                'p_total_price'     => $price,
                'p_grand_total'     => $price
            ]
        );
        
        DB::table('tbl_order_details')->where('cat_id',$catid)->update(
            [
                'offer_group_type'         => $request->get('offer_group_type')
            ]
        ); 
        
        
        
        $totalOrder = DB::table('tbl_order_details')
                    ->select('order_id', DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(p_grand_total) AS grandTotal'), DB::raw('SUM(p_total_price) AS totalValue'))
                    ->where('order_id', $orderID)
                    ->first();


        $orderUpdate= DB::table('tbl_order')->where('order_id', $orderID)->update(
                        [
                            'total_qty'         => $totalOrder->totalQty,                
                            'total_value'       => $totalOrder->totalValue,  
                            'grand_total_value' => $totalOrder->grandTotal
                        ]
                    );
        

        // Special offer Start

        
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
        $resultSpecialOffers = 0;
        $point = 0;
        $route = 0;
        $businessType = Auth::user()->business_type_id;

        $currentDay = date('Y-m-d');


        $order_no = DB::table('tbl_order')
                            //->select('order_no','auto_order_no')
                            ->where('order_id',$request->get('order_id'))
                            ->first();
                           
            //dd($order_no);     
            
            //Sharif Offer Starts----------------->//

            $lastOrderId = $request->get('order_id');
            $orderNo = $order_no->order_no;
            $autoOrderId = $order_no->auto_order_no;
            $distributorID = $order_no->distributor_id;
            $pointID = $order_no->point_id;
            $routeID = $order_no->route_id;
            $retailderID = $order_no->retailer_id;
            $total_odd=0;
            $total_odd1=0;
            $total_odd2=0;

            $order_sku = DB::table('tbl_order_details')
                                    ->select('order_id','cat_id','product_id')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $catid)
                                    ->get();


            $special_sku = array();
            foreach($order_sku as $order_sku_id) {
                $special_sku[]= $order_sku_id->product_id;
            }

        // Regular offer criteria

            $resultRegularOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='1'
             AND tbl_bundle_offer.iBusinessType='$businessType'
             AND tbl_bundle_offer_scope.iDivId='$division_id' 
             AND '$currentDay' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultRegularOffers)>0)
            {           

                $regularpoint = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                     AND iPointId='$point_id'
                     "); 


                $regularroute = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                         AND iPointId='$point_id' AND iRouteId = '$routeid'
                         ");
            }

        $resultSpecialOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
         FROM
         tbl_bundle_offer
         INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
         WHERE 
         tbl_bundle_offer.iStatus='1'
         AND tbl_bundle_offer.iOfferType='2'
         AND tbl_bundle_offer.iBusinessType='$businessType'
         AND tbl_bundle_offer_scope.iDivId='$division_id' 
         AND '$currentDay' BETWEEN dBeginDate 
         AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
         ");

        if(sizeof($resultSpecialOffers)>0)
        {            

        $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
             FROM tbl_bundle_offer_scope 
             WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
             AND iPointId='$point_id'
             "); 


        $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                 FROM tbl_bundle_offer_scope 
                 WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                 AND iPointId='$point_id' AND iRouteId = '$routeid'
                 ");
        }

        

        if(sizeof($resultRegularOffers)>0 AND sizeof($regularpoint)>0 AND sizeof($regularroute)>0)
        {
            $itemsDelete = DB::table('tbl_order_free_qty')
                            ->where('catid',$catid)
                            ->where('point_id',$pointid)
                            ->where('route_id',$routeid)
                            ->where('retailer_id',$retailderid)
                            ->delete();
           
            $andItemsDelete = DB::table('tbl_order_regular_and_free_qty')
                            ->where('catid',$catid)
                            ->where('point_id',$pointid)
                            ->where('route_id',$routeid)
                            ->where('retailer_id',$retailderid)
                            ->delete();
            
            $checkRegularSkuProducts =  DB::table('tbl_regular_sku_products')
                                        ->select('slab','catid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                        ->where('catid',$catid)
                                        ->whereIn('sku_id',$special_sku)
                                        ->groupBy('sku_id')
                                        ->get();


            $skuid = array();

            foreach($checkRegularSkuProducts as $skua) {
                $skuid[]= $skua->sku_id;
            }

            if($offer_group_type == 1){

            $totalQty =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->whereNotIn('product_id', $skuid)
                            ->sum('order_qty');
            }else{

            $totalQty =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->sum('order_qty');

            }
            if($totalQty>0)
            {
                $regularProducts =  DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab',$totalQty)
                                   ->where('status',0)
                                   ->get();

               
     
                if(sizeof($regularProducts) >0 )
                {
            
                    foreach($regularProducts as $regularProducts) {
                    
                        $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                            [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularProducts->slab,
                                'catid'                 => $catid,
                                'product_id'            => $regularProducts->pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularProducts->qty,
                                'free_value'           => $regularProducts->value,
                                'total_free_value'     => $regularProducts->value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                            ]
                        );
                        
                        if($regularProducts->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularProducts->slab,
                                'catid'                 => $catid,
                                'product_id'            => $regularProducts->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularProducts->and_qty,
                                'free_value'            => $regularProducts->and_value,
                                'total_free_value'      => $regularProducts->value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                        }
                    
                    }
                }
                else
                {
                    
                    /*$maxSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product  WHERE slab<$totalQty)");*/

                    $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<$totalQty");


                    $maxSlab = DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab', $maxValue[0]->slab)
                                   ->where('status',0)
                                   ->get();

                    

                     //dd($maxSlab);

                    if(sizeof($maxSlab) >0 )
                    {
                        foreach($maxSlab as $maxSlab) {
                        
                            $mainQty = (int)($totalQty/$maxSlab->slab);
                           //dd($mainQty);
                            $total_odd = $totalQty - ($mainQty * $maxSlab->slab);
                            $total_free = $maxSlab->qty * $mainQty;
                            $total_value = $maxSlab->value * $total_free;
                            $and_total_free = $maxSlab->and_qty * $mainQty;
                            $and_total_value = $maxSlab->and_value * $and_total_free;
                        

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $maxSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );
                            
                            if($maxSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $maxSlab->slab,
                                'catid'                 => $catid,
                                'product_id'            => $maxSlab->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $and_total_free,
                                'free_value'            => $maxSlab->and_value,
                                'total_free_value'      => $and_total_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd)");

                    
                    
                    if(sizeof($lastSlab) >0 )
                    {

                         foreach($lastSlab as $lastSlab) {

                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                            //dd($total_odd1);
                            $total_free = $lastSlab->qty * $mainQty1;
                            $total_value = $lastSlab->value * $total_free;
                            $and_total_free = $lastSlab->and_qty * $mainQty1;
                            $and_total_value = $lastSlab->and_value * $and_total_free;

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastSlab->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                   $lastOddSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd1)");
                    
                    if(sizeof($lastOddSlab) >0 )
                    {
                      
                       foreach($lastOddSlab as $lastOddSlab) {

                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                            $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                            $total_free = $lastOddSlab->qty * $mainQty2;
                            $total_value = $lastOddSlab->value * $total_free;
                            $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;


                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }
                    
                    
                    $lastOddSlab2 =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                    if(sizeof($lastOddSlab2) >0 )
                    {
                      
                       foreach($lastOddSlab2 as $lastOddSlab2) {

                            $mainQty3 = (int)($total_odd1/$lastOddSlab2->slab);
                            $total_free = $lastOddSlab2->qty * $mainQty3;
                            $total_value = $lastOddSlab2->value * $total_free;
                            $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab2->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab2->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab2->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                }
            }

            ////// Start SKU Regular offer
            if($offer_group_type == 1){
                foreach($checkRegularSkuProducts as $sku) {
                
                    $totalSkuQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->where('product_id', $sku->sku_id)
                                    ->sum('order_qty');
                                    
                    $regularSku =  DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                    ->where('catid',$catid)
                    ->where('sku_id',$sku->sku_id)
                    ->where('slab',$totalSkuQty)
                    ->where('status',0)                       
                    ->get();


                    if(sizeof($regularSku) >0 )
                    {

                        foreach($regularSku as $regularSku) {
                        
                             $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularSku->slab,
                                'catid'                 => $catid,
                                'product_id'            => $regularSku->pid,
                                'sku_id'                => $regularSku->sku_id,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularSku->qty,
                                'free_value'            => $regularSku->value,
                                'total_free_value'      => $regularSku->value * $regularSku->qty,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            if($regularSku->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularSku->slab,
                                'catid'                 => $catid,
                                'sku_id'                => $regularSku->sku_id,
                                'product_id'            => $regularSku->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularSku->and_qty,
                                'free_value'            => $regularSku->and_value,
                                'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }


                        }


                    }
                    else
                    {
                        $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                        $maxSlabSku = DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('sku_id',$sku->sku_id)
                                       ->where('slab', $maxValueSku[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlabSku) >0 )
                        {

                            foreach($maxSlabSku as $maxSlabSku) {

                                $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                               //dd($mainQty);
                                $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                $total_free = $maxSlabSku->qty * $mainQty;
                                $total_value = $maxSlabSku->value * $total_free;
                                $and_total_free = $maxSlabSku->and_qty * $mainQty;
                                $and_total_value = $maxSlabSku->and_value * $and_total_free;
                            

                               $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlabSku->pid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlabSku->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($maxSlabSku->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularSku->slab,
                                    'catid'                 => $catid,
                                    'sku_id'                => $regularSku->sku_id,
                                    'product_id'            => $regularSku->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularSku->and_qty,
                                    'free_value'            => $regularSku->and_value,
                                    'total_free_value'      => $regularSku->and_qty * $regularSku->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }
                          
                        }

                        $lastSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                        if(sizeof($lastSlabSku) >0 )
                        {

                             foreach($lastSlabSku as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;

                               $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );


                                if($lastSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'catid'                 => $catid,
                                    'sku_id'                => $lastSlab->sku_id,
                                    'product_id'            => $lastSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $lastSlab->and_qty,
                                    'free_value'            => $lastSlab->and_value,
                                    'total_free_value'      => $lastSlab->and_qty * $lastSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                        if(sizeof($lastOddSlabSku) >0 )
                        {
                          
                           foreach($lastOddSlabSku as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $total_free;

                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'catid'                 => $catid,
                                    'sku_id'                => $lastOddSlab->sku_id,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $lastOddSlab->and_qty,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $lastOddSlab->and_qty * $lastOddSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }
                    } // SKU else close
                } // foreach loop close             
            }
        }
            //Sharif Regular Offer Ends-------------------->//

            // Start special offer

        if(sizeof($resultSpecialOffers)>0 AND sizeof($point)>0 AND sizeof($route)>0)
        {
            

            $specialDelete = DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            $andspecialDelete = DB::table('tbl_order_special_and_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            $commissionDelete = DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            
            $checkSkuProducts =  DB::table('tbl_special_sku_products')
                                    ->select('slab','catid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                    ->where('catid', $catid)
                                    ->where('status',0) 
                                    ->whereIn('sku_id',$special_sku)
                                    ->groupBy('sku_id')
                                    ->get();

             
            $skuid = array();
            foreach($checkSkuProducts as $skua) {
                $skuid[]= $skua->sku_id;
            }

            if($offer_group_type == 1){

                $totalQtyCat =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->whereNotIn('product_id', $skuid)
                            ->sum('order_qty');
            }else{

                $totalQtyCat =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->sum('order_qty');
            }

           

           if($totalQtyCat>0)
            {

                $specialProducts =  DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                ->where('catid',$catid)
                ->where('slab',$totalQtyCat)
                ->where('status',0)                       
                ->get();

                //dd($specialProducts);

                if(sizeof($specialProducts) >0 )
                {

                    foreach($specialProducts as $specialProducts) {
                    
                    

                     $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                        [
                        'order_id'              => $lastOrderId,
                        'order_no'              => $orderNo,
                        'auto_order_no'         => $autoOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'slab'                  => $specialProducts->slab,
                        'catid'                 => $catid,
                        'product_id'            => $specialProducts->pid,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => Auth::user()->id,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'total_free_qty'        => $specialProducts->qty,
                        'free_value'            => $specialProducts->value,
                        'total_free_value'      => $specialProducts->value,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );


                        if($specialProducts->and_qty>0){

                          DB::table('tbl_order_special_and_free_qty')->insert(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'special_id'            => $special_cat_id,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialProducts->slab,
                            'catid'                 => $catid,
                            'product_id'            => $specialProducts->and_pid,
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $specialProducts->and_qty,
                            'free_value'            => $specialProducts->and_value,
                            'total_free_value'      => $specialProducts->and_value,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost()
                            ]
                            );
                        }
                    }


                }
                else
                {
                    $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<$totalQtyCat AND status=0");


                    $maxSlab = DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab', $maxValue[0]->slab)
                                   ->where('status',0)                       
                                   ->get();

                    if(sizeof($maxSlab) >0 )
                    {

                        foreach($maxSlab as $maxSlab) {

                            $mainQty = (int)($totalQtyCat/$maxSlab->slab);
                          
                            $total_odd = $totalQtyCat - ($mainQty * $maxSlab->slab);
                            $total_free = $maxSlab->qty * $mainQty;
                            $total_value = $maxSlab->value * $total_free;
                            $and_total_free = $maxSlab->and_qty * $mainQty;
                            $and_total_value = $maxSlab->and_value * $and_total_free;

                        

                          $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $maxSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($maxSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $special_cat_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $maxSlab->slab,
                                'catid'                 => $catid,
                                'product_id'            => $maxSlab->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $and_total_free,
                                'free_value'            => $maxSlab->and_value,
                                'total_free_value'      => $and_total_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }

                        }
                      
                    }

                    $lastSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd  AND status=0)  AND status=0");


                    if(sizeof($lastSlab) >0 )
                    {

                         foreach($lastSlab as $lastSlab) {

                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                            //dd($total_odd1);
                            $total_free = $lastSlab->qty * $mainQty1;
                            $total_value = $lastSlab->value * $total_free;
                            $and_total_free = $lastSlab->and_qty * $mainQty1;
                            $and_total_value = $lastSlab->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastSlab->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                   $lastOddSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd1 AND status=0) AND status=0");


                    if(sizeof($lastOddSlab) >0 )
                    {
                      
                       foreach($lastOddSlab as $lastOddSlab) {

                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                            $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                            $total_free = $lastOddSlab->qty * $mainQty2;
                            $total_value = $lastOddSlab->value * $total_free;
                            $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastOddSlab2 =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");
                   //dd($lastOddSlab);

                    if(sizeof($lastOddSlab2) >0 )
                    {
                      
                       foreach($lastOddSlab2 as $lastOddSlab2) {

                            $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                           
                            $total_free = $lastOddSlab2->qty * $mainQty3;
                            $total_value = $lastOddSlab2->value * $total_free;
                            $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                            $and_total_value = $lastOddSlab2->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab2->value,
                                    'total_free_value'      => $total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab2->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab2->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                } 

            }//  cagegory else close


            ////// Start SKU special offer

            /*$skuSpecialProducts =  DB::table('tbl_special_sku_products')
                                    ->select('slab','catid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                    ->where('catid', $catid)
                                    ->where('sku_id',$special_sku)
                                    ->get();*/

            if($offer_group_type == 1){

                foreach($checkSkuProducts as $sku) {
                
                    $totalSkuQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->where('product_id', $sku->sku_id)
                                    ->sum('order_qty');
                                    
                     $specialSku =  DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                    ->where('catid',$catid)
                    ->where('sku_id',$sku->sku_id)
                    ->where('slab',$totalSkuQty)
                    ->where('status',0)                       
                    ->get();


                    if(sizeof($specialSku) >0 )
                    {

                        foreach($specialSku as $specialSku) {
                        
                         $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialSku->slab,
                            'catid'                 => $catid,
                            'product_id'            => $specialSku->pid,
                            'sku_id'                => $specialSku->sku_id,
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $specialSku->qty,
                            'free_value'            => $specialSku->value,
                            'total_free_value'      => $specialSku->value * $specialSku->qty,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost()
                            ]
                            );

                            if($specialSku->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $sp_sku_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $specialSku->slab,
                                    'catid'                 => $catid,
                                    'sku_id'                => $specialSku->sku_id,
                                    'product_id'            => $specialSku->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $specialSku->and_qty,
                                    'free_value'            => $specialSku->and_value,
                                    'total_free_value'      => $and_total_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }

                    }
                    else
                    {
                        $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                        $maxSlabSku = DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('sku_id',$sku->sku_id)
                                       ->where('slab', $maxValueSku[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlabSku) >0 )
                        {

                            foreach($maxSlabSku as $maxSlabSku) {

                                $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                               //dd($mainQty);
                                $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                $total_free = $maxSlabSku->qty * $mainQty;
                                $total_value = $maxSlabSku->value * $total_free;
                                $and_total_value =$maxSlabSku->and_qty * $maxSlabSku->and_value;

                            

                               $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlabSku->pid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlabSku->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($maxSlabSku->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'catid'                 => $catid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'product_id'            => $maxSlabSku->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $maxSlabSku->and_qty,
                                        'free_value'            => $maxSlabSku->and_value,
                                        'total_free_value'      => $and_total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }
                          
                        }

                        $lastSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                        if(sizeof($lastSlabSku) >0 )
                        {

                             foreach($lastSlabSku as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                //dd($total_odd1);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;
                                $and_total_value =$lastSlab->and_qty * $lastSlab->and_value;

                                $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'product_id'            => $lastSlab->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastSlab->and_qty,
                                        'free_value'            => $lastSlab->and_value,
                                        'total_free_value'      => $and_total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                        if(sizeof($lastOddSlabSku) >0 )
                        {
                          
                           foreach($lastOddSlabSku as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $total_free;

                                $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'product_id'            => $lastOddSlab->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastOddSlab->and_qty,
                                        'free_value'            => $lastOddSlab->and_value,
                                        'total_free_value'      => $and_total_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }
                    } // SKU else close
                } // foreach loop close
            }
            // Special Value wise commission Start

            $totalCatValue =  DB::table('tbl_order_details')
                                ->select('p_total_price')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->sum('p_total_price');


            $checkGroupId = DB::select("SELECT * FROM tbl_special_value_wise_category
                            JOIN tbl_special_values_wise ON tbl_special_values_wise.id = tbl_special_value_wise_category.svwid
                            WHERE tbl_special_value_wise_category.categoryid=$catid AND $totalCatValue BETWEEN min AND max");

            if(sizeof($checkGroupId) >0 )
            {
               
                DB::table('tbl_special_temp_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                DB::table('tbl_special_temp_commission')->insert(
                    [
                    'order_id'              => $lastOrderId,
                    'group_id'              => $checkGroupId[0]->group_id,
                    'offer_id'              => $checkGroupId[0]->svwid,
                    'catid'                 => $catid,
                    'distributor_id'        => $distributorID,
                    'point_id'              => $pointID,
                    'route_id'              => $routeID,
                    'retailer_id'           => $retailderID,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'cat_value'             => $totalCatValue,
                    'created_by'            => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost()
                    ]
                    );
               
            }

            $totalValue = DB::table('tbl_special_temp_commission')
                                ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('group_id')
                                ->get(); 

            

            foreach ($totalValue as $totalOfferValue) 
            {
                $commissionValue = $totalOfferValue->total;
                $checkCatValue = DB::select("SELECT * FROM tbl_special_values_wise WHERE status = 1 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                if(sizeof($checkCatValue) >0 )
                {
                   DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('group_id', $totalOfferValue->group_id)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                 DB::table('tbl_special_commission')->insert(
                    [
                    'order_id'              => $lastOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'offer_id'              => $checkCatValue[0]->id,
                    'group_id'              => $totalOfferValue->group_id,
                    'distributor_id'        => $distributorID,
                    'point_id'              => $pointID,
                    'route_id'              => $routeID,
                    'retailer_id'           => $retailderID,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'commission'            => $checkCatValue[0]->commission_rate,
                    'total_free_value'      => $commissionValue,
                    'created_by'            => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost()
                    ]
                    );
                   
                }

            }

            // Special Value wise commission end
        }      
                 

        return Redirect::back()->with('success', 'Successfully Updated Order Product.');
    }

    
    public function ssg_items_items_delete_old(Request $request)
    {
        $orderID     = $request->get('orderID');
        $itemQty     = $request->get('itemQty');
        $itemPrice   = $request->get('itemPrice');
        $catid       = $request->get('itemCat');
        
        $itemsOrders = DB::table('tbl_order')
                    ->select('order_id','total_qty','total_value','grand_total_value')
                    ->where('order_id',$orderID)
                    ->first();

        $minQty     = $itemsOrders->total_qty - $itemQty;
        $minMinitValue = $itemQty * $itemPrice;
        $minValue   = ($itemsOrders->total_value) - ($minMinitValue);

        $orderUpdate= DB::table('tbl_order')->where('order_id', $orderID)->update(
                        [
                            'total_qty'         => $minQty,                
                            'total_value'       => $minValue,
                            'grand_total_value' => $minValue
                        ]
                    );

      
                        /* Offer Re-Arrange for Item Delete */
        
        $SQLOrdMast = DB::select("SELECT * FROM tbl_order WHERE order_id = '".$orderID."'");
                     
        $SQLOrdDet = DB::select("SELECT * FROM tbl_order_details WHERE order_det_id = '".$request->get('id')."'");          
        
        
        if(sizeof($SQLOrdDet)>0)
        {
            $special_sku[] = $SQLOrdDet[0]->product_id;
            $offer_group_type = $SQLOrdDet[0]->offer_group_type;
        }

                

        if(sizeof($SQLOrdMast)>0 && sizeof($SQLOrdDet)>0)
        {
            $this->pre_offer_entry( $orderID, $SQLOrdMast[0]->order_no, $SQLOrdMast[0]->auto_order_no, 
                                     $SQLOrdMast[0]->distributor_id, $SQLOrdMast[0]->retailer_id, 
                                     $SQLOrdMast[0]->point_id, $SQLOrdMast[0]->route_id, 
                                     $catid, 
                                     $special_sku, 
                                     $offer_group_type, 
                                     $SQLOrdDet[0]->partial_order_id, 
                                     $IsUpdate = true);
        }   

        $itemsDelete = DB::table('tbl_order_details')->where('order_det_id',$request->get('id'))->delete();
        //Redirect to original location
        if ($itemsDelete) 
        {
            return Redirect::back()->with('success', 'Successfully Deleted Order Product.');
        }
        
    }
    
    public function ssg_items_items_delete(Request $request)
    {
        $orderID     = $request->get('orderID');
        $itemQty     = $request->get('itemQty');
        $itemPrice   = $request->get('itemPrice');
        $catid       = $request->get('itemCat');

        $resultFoInfo   = DB::table('users')
                            ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                             ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                             ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                             ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                             ->where('tbl_user_type.user_type_id', 12)
                             ->where('users.id', Auth::user()->id)
                             ->where('users.is_active', 0) // 0 for active
                             ->where('tbl_user_business_scope.is_active', 0) 
                             ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                             ->first();

            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
            $resultSpecialOffers = 0;
            $point = 0;
            $route = 0;
            $businessType = Auth::user()->business_type_id;

            $currentDay = date('Y-m-d');


            // Regular offer criteria

            $resultRegularOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='1'
             AND tbl_bundle_offer.iBusinessType='$businessType'
             AND tbl_bundle_offer_scope.iDivId='$division_id' 
             AND '$currentDay' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultRegularOffers)>0)
            {           

                $regularpoint = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                     AND iPointId='$point_id'
                     "); 


                $regularroute = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                         FROM tbl_bundle_offer_scope 
                         WHERE iOfferId='".$resultRegularOffers[0]->iId."' 
                         AND iPointId='".$request->get('point_id')."' AND iRouteId = '".$request->get('route_id')."'
                         ");
            }

            $resultSpecialOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
             FROM
             tbl_bundle_offer
             INNER JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
             WHERE 
             tbl_bundle_offer.iStatus='1'
             AND tbl_bundle_offer.iOfferType='2'
             AND tbl_bundle_offer.iBusinessType='$businessType'
             AND tbl_bundle_offer_scope.iDivId='$division_id' 
             AND '$currentDay' BETWEEN dBeginDate 
             AND dEndDate GROUP BY tbl_bundle_offer.iOfferType LIMIT 1
             ");

            if(sizeof($resultSpecialOffers)>0)
            {            

            $point = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                 FROM tbl_bundle_offer_scope 
                 WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                 AND iPointId='$point_id'
                 "); 


            $route = DB::select("SELECT iOfferId,iDivId,iPointId,iRouteId
                     FROM tbl_bundle_offer_scope 
                     WHERE iOfferId='".$resultSpecialOffers[0]->iId."' 
                     AND iPointId='".$request->get('point_id')."' AND iRouteId = '".$request->get('route_id')."'
                     ");
            }

            //Sharif Regular Offer Start-------------------->//
        
        
            $regularFreeItemDelete = DB::table('tbl_order_free_qty')
                                    ->where('order_id',$orderID)
                                    ->where('global_company_id',Auth::user()->global_company_id)
                                    ->where('catid', $catid)
                                    ->delete();

            $regularAndFreeItemDelete = DB::table('tbl_order_regular_and_free_qty')
                                    ->where('order_id',$orderID)
                                    ->where('global_company_id',Auth::user()->global_company_id)
                                    ->where('catid', $catid)
                                    ->delete();

           

            $order_no = DB::table('tbl_order')
                        ->where('order_id',$orderID)
                        ->first();
                           
            //dd($order_no);     
            
            //Sharif Offer Starts----------------->//

            $lastOrderId = $orderID;
            $orderNo = $order_no->order_no;
            $autoOrderId = $order_no->auto_order_no;
            $distributorID = $order_no->distributor_id;
            $pointID = $order_no->point_id;
            $routeID = $order_no->route_id;
            $retailderID = $order_no->retailer_id;
            $total_odd=0;
            $total_odd1=0;
            $total_odd2=0;

            $order_sku = DB::table('tbl_order_details')
                                    ->select('order_det_id','order_id','cat_id','product_id')
                                    ->where('order_id', $lastOrderId)
                                    ->where('cat_id', $catid)
                                    ->where('order_det_id','!=', $request->get('id'))
                                    ->get();


            $special_sku = array();
            foreach($order_sku as $order_sku_id) {
                $special_sku[]= $order_sku_id->product_id;
            }




            $checkRegularSkuProducts =  DB::table('tbl_regular_sku_products')
                                        ->select('slab','catid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                        ->where('catid',$catid)
                                        ->whereIn('sku_id',$special_sku)
                                        ->groupBy('sku_id')
                                        ->get();

           

            $skuid = array();

            foreach($checkRegularSkuProducts as $skua) {
                $skuid[]= $skua->sku_id;
            }

            $checkOfferType = DB::table('tbl_order_details')
                                    ->select('order_det_id','offer_group_type')
                                    ->where('order_det_id', $request->get('id'))
                                    ->first();

            //dd($checkOfferType->offer_group_type);
            if($checkOfferType->offer_group_type ==1){

                $totalQty =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->whereNotIn('product_id', $skuid)
                            ->where('order_det_id','!=', $request->get('id'))
                            ->sum('order_qty');
            }else{
                $totalQty =  DB::table('tbl_order_details')
                            ->where('order_id',$lastOrderId)
                            ->where('cat_id',$catid)
                            ->where('order_det_id','!=', $request->get('id'))
                            ->sum('order_qty');
            }

            if($totalQty>0)
            {
                $regularProducts =  DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab',$totalQty)
                                   ->where('status',0)
                                   ->get();

     
                if(sizeof($regularProducts) >0 )
                {
            
                    foreach($regularProducts as $regularProducts) 
                    {
                    
                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularProducts->slab,
                                'slab_count'            =>1,
                                'catid'                 => $catid,
                                'product_id'            => $regularProducts->pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularProducts->qty,
                                'free_value'           => $regularProducts->value,
                                'total_free_value'     => $regularProducts->value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                            );
                        
                        if($regularProducts->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularProducts->slab,
                                'slab_count'            =>1,
                                'catid'                 => $catid,
                                'product_id'            => $regularProducts->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularProducts->and_qty,
                                'free_value'            => $regularProducts->and_value,
                                'total_free_value'      => $regularProducts->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                        }
                    
                    }
                }
                else
                {
                    $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<$totalQty");


                    $maxSlab = DB::table('tbl_regular_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab', $maxValue[0]->slab)
                                   ->where('status',0)
                                   ->get();

                    
                    if(sizeof($maxSlab) >0 )
                    {
                        foreach($maxSlab as $maxSlab) {
                        
                            $mainQty = (int)($totalQty/$maxSlab->slab);
                           //dd($mainQty);
                            $total_odd = $totalQty - ($mainQty * $maxSlab->slab);
                            $total_free = $maxSlab->qty * $mainQty;
                            $total_value = $maxSlab->value * $total_free;
                            $and_total_free = $maxSlab->and_qty * $mainQty;
                            $and_total_value = $maxSlab->and_value * $and_total_free;
                        

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'slab_count'            => $mainQty,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $maxSlab->value,
                                    'total_free_value'      => $maxSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );
                            
                            if($maxSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $maxSlab->slab,
                                'slab_count'            => $mainQty,
                                'catid'                 => $catid,
                                'product_id'            => $maxSlab->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $and_total_free,
                                'free_value'            => $maxSlab->and_value,
                                'total_free_value'      => $maxSlab->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd)");

                    
                    
                    if(sizeof($lastSlab) >0 )
                    {

                        foreach($lastSlab as $lastSlab) 
                         {

                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                            //dd($total_odd1);
                            $total_free = $lastSlab->qty * $mainQty1;
                            $total_value = $lastSlab->value * $total_free;
                            $and_total_free = $lastSlab->and_qty * $mainQty1;
                            $and_total_value = $lastSlab->and_value * $and_total_free;

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'slab_count'            => $mainQty1,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastSlab->value,
                                    'total_free_value'      => $lastSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'slab_count'            => $mainQty1,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastSlab->and_value,
                                    'total_free_value'      => $lastSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastOddSlab =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd1)");
                    
                    if(sizeof($lastOddSlab) >0 )
                    {
                      
                       foreach($lastOddSlab as $lastOddSlab) {

                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                            $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                            $total_free = $lastOddSlab->qty * $mainQty2;
                            $total_value = $lastOddSlab->value * $total_free;
                            $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;


                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'slab_count'            => $mainQty2,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab->value,
                                    'total_free_value'      => $lastOddSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'slab_count'            => $mainQty2,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $lastOddSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastOddSlab2 =  DB::select("SELECT * FROM tbl_regular_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");


                    if(sizeof($lastOddSlab2) >0 )
                    {
                      
                       foreach($lastOddSlab2 as $lastOddSlab2) {

                            $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                            $total_free = $lastOddSlab2->qty * $mainQty3;
                            $total_value = $lastOddSlab2->value * $total_free;
                            $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;

                            $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'slab_count'            => $mainQty3,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab2->value,
                                    'total_free_value'      => $lastOddSlab2->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab2->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'slab_count'            => $mainQty3,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab2->and_value,
                                    'total_free_value'      => $lastOddSlab2->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }
                    

                }
            } 

            ////// Start SKU Regular offer
            if($checkOfferType->offer_group_type ==1)
            {
                foreach($checkRegularSkuProducts as $sku) {
                
                    $totalSkuQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->where('product_id', $sku->sku_id)
                                    ->where('order_det_id','!=', $request->get('id'))
                                    ->sum('order_qty');
                                    
                    $regularSku =  DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                    ->where('catid',$catid)
                    ->where('sku_id',$sku->sku_id)
                    ->where('slab',$totalSkuQty)
                    ->where('status',0)                       
                    ->get();


                    if(sizeof($regularSku) >0 )
                    {

                        foreach($regularSku as $regularSku) {
                        
                             $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularSku->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'product_id'            => $regularSku->pid,
                                'sku_id'                => $regularSku->sku_id,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularSku->qty,
                                'free_value'            => $regularSku->value,
                                'total_free_value'      => $regularSku->value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            if($regularSku->and_qty>0){

                              DB::table('tbl_order_regular_and_free_qty')->insert(
                                [
                                'order_id'              => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $regular_free_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $regularSku->slab,
                                'slab_count'            => 1,
                                'catid'                 => $catid,
                                'sku_id'                => $regularSku->sku_id,
                                'product_id'            => $regularSku->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $regularSku->and_qty,
                                'free_value'            => $regularSku->and_value,
                                'total_free_value'      => $regularSku->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }


                        }


                    }
                    else
                    {
                        $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                        $maxSlabSku = DB::table('tbl_regular_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('sku_id',$sku->sku_id)
                                       ->where('slab', $maxValueSku[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlabSku) >0 )
                        {

                            foreach($maxSlabSku as $maxSlabSku) {

                                $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                               //dd($mainQty);
                                $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                $total_free = $maxSlabSku->qty * $mainQty;
                                $total_value = $maxSlabSku->value * $total_free;
                                $and_total_free = $maxSlabSku->and_qty * $mainQty;
                                $and_total_value = $maxSlabSku->and_value * $and_total_free;
                            

                               $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlabSku->pid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlabSku->value,
                                        'total_free_value'      => $maxSlabSku->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($maxSlabSku->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $regularSku->slab,
                                    'slab_count'            => $mainQty,
                                    'catid'                 => $catid,
                                    'sku_id'                => $regularSku->sku_id,
                                    'product_id'            => $regularSku->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $regularSku->and_qty,
                                    'free_value'            => $regularSku->and_value,
                                    'total_free_value'      => $regularSku->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }
                          
                        }

                        $lastSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                        if(sizeof($lastSlabSku) >0 )
                        {

                             foreach($lastSlabSku as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;

                               $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $lastSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );


                                if($lastSlabSku->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlabSku->slab,
                                    'slab_count'            => $mainQty1,
                                    'catid'                 => $catid,
                                    'sku_id'                => $lastSlabSku->sku_id,
                                    'product_id'            => $lastSlabSku->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $lastSlabSku->and_qty,
                                    'free_value'            => $lastSlabSku->and_value,
                                    'total_free_value'      => $lastSlabSku->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlabSku =  DB::select("SELECT * FROM tbl_regular_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_regular_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                        if(sizeof($lastOddSlabSku) >0 )
                        {
                          
                           foreach($lastOddSlabSku as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value;

                                $regular_free_id = DB::table('tbl_order_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $lastOddSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_regular_and_free_qty')->insert(
                                    [
                                    'order_id'              => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $regular_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'slab_count'            => $mainQty2,
                                    'catid'                 => $catid,
                                    'sku_id'                => $lastOddSlab->sku_id,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $lastOddSlab->and_qty,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $lastOddSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }
                    } // SKU else close
                } // foreach loop close
            }                  
        
            //Sharif Regular Offer Ends-------------------->//

            // Special offer Start

            

        
            

            $specialDelete = DB::table('tbl_order_special_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            $andspecialDelete = DB::table('tbl_order_special_and_free_qty')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            $commissionDelete = DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->delete();

            
            $checkSkuProducts =  DB::table('tbl_special_sku_products')
                                    ->select('slab','catid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                    ->where('catid',$catid)
                                    ->where('status',0) 
                                    ->whereIn('sku_id',$special_sku)
                                    ->groupBy('sku_id')
                                    ->get();
            $skuid = array();
            foreach($checkSkuProducts as $skua) {
                $skuid[]= $skua->sku_id;
            }

            if($checkOfferType->offer_group_type ==1){

                $totalQtyCat =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->whereNotIn('product_id', $skuid)
                                ->where('order_det_id','!=', $request->get('id'))
                                ->sum('order_qty');

           }else{

                $totalQtyCat =  DB::table('tbl_order_details')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->where('order_det_id','!=', $request->get('id'))
                                ->sum('order_qty');
           }

           
           if($totalQtyCat>0)
            {
                $specialProducts =  DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                ->where('catid',$catid)
                ->where('slab',$totalQtyCat)
                ->where('status',0)                       
                ->get();

                //dd($specialProducts);

                if(sizeof($specialProducts) >0 )
                {

                    foreach($specialProducts as $specialProducts) {
                    
                     $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                        [
                        'order_id'              => $lastOrderId,
                        'order_no'              => $orderNo,
                        'auto_order_no'         => $autoOrderId,
                        'order_date'            => date('Y-m-d h:i:s'),
                        'slab'                  => $specialProducts->slab,
                        'slab_count'            => 1,
                        'catid'                 => $catid,
                        'product_id'            => $specialProducts->pid,
                        'distributor_id'        => $distributorID,
                        'point_id'              => $pointID,
                        'route_id'              => $routeID,
                        'retailer_id'           => $retailderID,
                        'fo_id'                 => Auth::user()->id,
                        'global_company_id'     => Auth::user()->global_company_id,
                        'total_free_qty'        => $specialProducts->qty,
                        'free_value'            => $specialProducts->value,
                        'total_free_value'      => $specialProducts->value,
                        'created_by'            => Auth::user()->id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost()
                        ]
                        );


                        if($specialProducts->and_qty>0){

                          DB::table('tbl_order_special_and_free_qty')->insert(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'special_id'            => $special_cat_id,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialProducts->slab,
                            'slab_count'            => 1,
                            'catid'                 => $catid,
                            'product_id'            => $specialProducts->and_pid,
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $specialProducts->and_qty,
                            'free_value'            => $specialProducts->and_value,
                            'total_free_value'      => $specialProducts->and_value,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost()
                            ]
                            );
                        }
                    }


                }
                else
                {
                    $maxValue =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<$totalQtyCat AND status=0");


                    $maxSlab = DB::table('tbl_special_offer_product')->select('slab','catid','pid','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                   ->where('catid',$catid)
                                   ->where('slab', $maxValue[0]->slab)
                                   ->where('status',0)                       
                                   ->get();

                    if(sizeof($maxSlab) >0 )
                    {

                        foreach($maxSlab as $maxSlab) {

                            $mainQty = (int)($totalQtyCat/$maxSlab->slab);
                          
                            $total_odd = $totalQtyCat - ($mainQty * $maxSlab->slab);
                            $total_free = $maxSlab->qty * $mainQty;
                            $total_value = $maxSlab->value * $total_free;
                            $and_total_free = $maxSlab->and_qty * $mainQty;
                            $and_total_value = $maxSlab->and_value * $and_total_free;

                        

                          $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $maxSlab->slab,
                                    'slab_count'            => $mainQty,
                                    'catid'                 => $catid,
                                    'product_id'            => $maxSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $maxSlab->value,
                                    'total_free_value'      => $maxSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($maxSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                'order_id'             => $lastOrderId,
                                'order_no'              => $orderNo,
                                'special_id'            => $special_cat_id,
                                'auto_order_no'         => $autoOrderId,
                                'order_date'            => date('Y-m-d h:i:s'),
                                'slab'                  => $maxSlab->slab,
                                'slab_count'            => $mainQty,
                                'catid'                 => $catid,
                                'product_id'            => $maxSlab->and_pid,
                                'distributor_id'        => $distributorID,
                                'point_id'              => $pointID,
                                'route_id'              => $routeID,
                                'retailer_id'           => $retailderID,
                                'fo_id'                 => Auth::user()->id,
                                'global_company_id'     => Auth::user()->global_company_id,
                                'total_free_qty'        => $and_total_free,
                                'free_value'            => $maxSlab->and_value,
                                'total_free_value'      => $maxSlab->and_value,
                                'created_by'            => Auth::user()->id,
                                'ipaddress'             => request()->ip(),
                                'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }

                        }
                      
                    }

                    $lastSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd  AND status=0)  AND status=0");


                    if(sizeof($lastSlab) >0 )
                    {

                         foreach($lastSlab as $lastSlab) {

                            $mainQty1 = (int)($total_odd/$lastSlab->slab);
                            $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                            //dd($total_odd1);
                            $total_free = $lastSlab->qty * $mainQty1;
                            $total_value = $lastSlab->value * $total_free;
                            $and_total_free = $lastSlab->and_qty * $mainQty1;
                            $and_total_value = $lastSlab->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'slab_count'            => $mainQty1,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastSlab->value,
                                    'total_free_value'      => $lastSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastSlab->slab,
                                    'slab_count'            => $mainQty1,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastSlab->and_value,
                                    'total_free_value'      => $lastSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                   $lastOddSlab =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd1 AND status=0) AND status=0");


                    if(sizeof($lastOddSlab) >0 )
                    {
                      
                       foreach($lastOddSlab as $lastOddSlab) {

                            $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                            $total_odd2 = $total_odd1 - ($mainQty2 * $lastOddSlab->slab);
                            $total_free = $lastOddSlab->qty * $mainQty2;
                            $total_value = $lastOddSlab->value * $total_free;
                            $and_total_free = $lastOddSlab->and_qty * $mainQty2;
                            $and_total_value = $lastOddSlab->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'slab_count'            => $mainQty2,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab->value,
                                    'total_free_value'      => $lastOddSlab->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab->slab,
                                    'slab_count'            => $mainQty2,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab->and_value,
                                    'total_free_value'      => $lastOddSlab->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                    $lastOddSlab2 =  DB::select("SELECT * FROM tbl_special_offer_product WHERE  catid=$catid AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_offer_product WHERE catid=$catid AND slab<=$total_odd2 AND status=0) AND status=0");
                   //dd($lastOddSlab);

                    if(sizeof($lastOddSlab2) >0 )
                    {
                      
                       foreach($lastOddSlab2 as $lastOddSlab2) {

                            $mainQty3 = (int)($total_odd2/$lastOddSlab2->slab);
                           
                            $total_free = $lastOddSlab2->qty * $mainQty3;
                            $total_value = $lastOddSlab2->value * $total_free;
                            $and_total_free = $lastOddSlab2->and_qty * $mainQty3;
                            $and_total_value = $lastOddSlab2->and_value * $and_total_free;

                            $special_cat_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'slab_count'            => $mainQty3,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $total_free,
                                    'free_value'            => $lastOddSlab2->value,
                                    'total_free_value'      => $lastOddSlab2->value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                            );

                            if($lastOddSlab2->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $special_cat_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $lastOddSlab2->slab,
                                    'slab_count'            => $mainQty3,
                                    'catid'                 => $catid,
                                    'product_id'            => $lastOddSlab2->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $and_total_free,
                                    'free_value'            => $lastOddSlab2->and_value,
                                    'total_free_value'      => $lastOddSlab2->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }
                    }

                } 

            }//  cagegory else close
            ////// Start SKU special offer
            if($checkOfferType->offer_group_type ==1){

                foreach($checkSkuProducts as $sku) {
                
                    $totalSkuQty =  DB::table('tbl_order_details')
                                    ->where('order_id',$lastOrderId)
                                    ->where('cat_id',$catid)
                                    ->where('product_id', $sku->sku_id)
                                    ->sum('order_qty');
                                    
                     $specialSku =  DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                    ->where('catid',$catid)
                    ->where('sku_id',$sku->sku_id)
                    ->where('slab',$totalSkuQty)
                    ->where('status',0)                       
                    ->get();


                    if(sizeof($specialSku) >0 )
                    {

                        foreach($specialSku as $specialSku) {
                        
                         $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                            [
                            'order_id'              => $lastOrderId,
                            'order_no'              => $orderNo,
                            'auto_order_no'         => $autoOrderId,
                            'order_date'            => date('Y-m-d h:i:s'),
                            'slab'                  => $specialSku->slab,
                            'slab_count'            => 1,
                            'catid'                 => $catid,
                            'product_id'            => $specialSku->pid,
                            'sku_id'                => $specialSku->sku_id,
                            'distributor_id'        => $distributorID,
                            'point_id'              => $pointID,
                            'route_id'              => $routeID,
                            'retailer_id'           => $retailderID,
                            'fo_id'                 => Auth::user()->id,
                            'global_company_id'     => Auth::user()->global_company_id,
                            'total_free_qty'        => $specialSku->qty,
                            'free_value'            => $specialSku->value,
                            'total_free_value'      => $specialSku->value,
                            'created_by'            => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost()
                            ]
                            );

                            if($specialSku->and_qty>0){

                              DB::table('tbl_order_special_and_free_qty')->insert(
                                [
                                    'order_id'             => $lastOrderId,
                                    'order_no'              => $orderNo,
                                    'special_id'            => $sp_sku_free_id,
                                    'auto_order_no'         => $autoOrderId,
                                    'order_date'            => date('Y-m-d h:i:s'),
                                    'slab'                  => $specialSku->slab,
                                    'slab_count'            => 1,
                                    'catid'                 => $catid,
                                    'sku_id'                => $specialSku->sku_id,
                                    'product_id'            => $specialSku->and_pid,
                                    'distributor_id'        => $distributorID,
                                    'point_id'              => $pointID,
                                    'route_id'              => $routeID,
                                    'retailer_id'           => $retailderID,
                                    'fo_id'                 => Auth::user()->id,
                                    'global_company_id'     => Auth::user()->global_company_id,
                                    'total_free_qty'        => $specialSku->and_qty,
                                    'free_value'            => $specialSku->and_value,
                                    'total_free_value'      => $specialSku->and_value,
                                    'created_by'            => Auth::user()->id,
                                    'ipaddress'             => request()->ip(),
                                    'hostname'              => $request->getHttpHost()
                                ]
                                );
                            }
                        }

                    }
                    else
                    {
                        $maxValueSku =  DB::select("SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<$totalSkuQty AND status=0");


                        $maxSlabSku = DB::table('tbl_special_sku_products')->select('slab','catid','pid','sku_id','qty','value','and_pro_cat_id','and_pid','and_value','and_qty')
                                       ->where('catid',$catid)
                                       ->where('sku_id',$sku->sku_id)
                                       ->where('slab', $maxValueSku[0]->slab)
                                       ->where('status',0)                       
                                       ->get();

                        if(sizeof($maxSlabSku) >0 )
                        {

                            foreach($maxSlabSku as $maxSlabSku) {

                                $mainQty = (int)($totalSkuQty/$maxSlabSku->slab);
                               //dd($mainQty);
                                $total_odd = $totalSkuQty - ($mainQty * $maxSlabSku->slab);
                                $total_free = $maxSlabSku->qty * $mainQty;
                                $total_value = $maxSlabSku->value * $total_free;
                                $and_total_value =$maxSlabSku->and_qty * $maxSlabSku->and_value;

                            

                               $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'product_id'            => $maxSlabSku->pid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $maxSlabSku->value,
                                        'total_free_value'      => $maxSlabSku->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($maxSlabSku->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $maxSlabSku->slab,
                                        'slab_count'            => $mainQty,
                                        'catid'                 => $catid,
                                        'sku_id'                => $maxSlabSku->sku_id,
                                        'product_id'            => $maxSlabSku->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $maxSlabSku->and_qty,
                                        'free_value'            => $maxSlabSku->and_value,
                                        'total_free_value'      => $maxSlabSku->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }

                            }
                          
                        }

                        $lastSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd  AND status=0)  AND status=0");


                        if(sizeof($lastSlabSku) >0 )
                        {

                             foreach($lastSlabSku as $lastSlab) {

                                $mainQty1 = (int)($total_odd/$lastSlab->slab);
                                $total_odd1 = $total_odd - ($mainQty1 * $lastSlab->slab);
                                //dd($total_odd1);
                                $total_free = $lastSlab->qty * $mainQty1;
                                $total_value = $lastSlab->value * $total_free;
                                $and_total_value =$lastSlab->and_qty * $lastSlab->and_value;

                                $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastSlab->pid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastSlab->value,
                                        'total_free_value'      => $lastSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastSlab->slab,
                                        'slab_count'            => $mainQty1,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastSlab->sku_id,
                                        'product_id'            => $lastSlab->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastSlab->and_qty,
                                        'free_value'            => $lastSlab->and_value,
                                        'total_free_value'      => $lastSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }

                       $lastOddSlabSku =  DB::select("SELECT * FROM tbl_special_sku_products WHERE  catid=$catid AND sku_id=$sku->sku_id AND slab=(SELECT MAX(slab) AS slab  FROM tbl_special_sku_products WHERE catid=$catid AND sku_id=$sku->sku_id AND slab<=$total_odd1 AND status=0) AND status=0");


                        if(sizeof($lastOddSlabSku) >0 )
                        {
                          
                           foreach($lastOddSlabSku as $lastOddSlab) {

                                $mainQty2 = (int)($total_odd1/$lastOddSlab->slab);
                                $total_free = $lastOddSlab->qty * $mainQty2;
                                $total_value = $lastOddSlab->value * $total_free;

                                $sp_sku_free_id = DB::table('tbl_order_special_free_qty')->insertGetId(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'product_id'            => $lastOddSlab->pid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $total_free,
                                        'free_value'            => $lastOddSlab->value,
                                        'total_free_value'      => $lastOddSlab->value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                );

                                if($lastOddSlab->and_qty>0){

                                  DB::table('tbl_order_special_and_free_qty')->insert(
                                    [
                                        'order_id'             => $lastOrderId,
                                        'order_no'              => $orderNo,
                                        'special_id'            => $sp_sku_free_id,
                                        'auto_order_no'         => $autoOrderId,
                                        'order_date'            => date('Y-m-d h:i:s'),
                                        'slab'                  => $lastOddSlab->slab,
                                        'slab_count'            => $mainQty2,
                                        'catid'                 => $catid,
                                        'sku_id'                => $lastOddSlab->sku_id,
                                        'product_id'            => $lastOddSlab->and_pid,
                                        'distributor_id'        => $distributorID,
                                        'point_id'              => $pointID,
                                        'route_id'              => $routeID,
                                        'retailer_id'           => $retailderID,
                                        'fo_id'                 => Auth::user()->id,
                                        'global_company_id'     => Auth::user()->global_company_id,
                                        'total_free_qty'        => $lastOddSlab->and_qty,
                                        'free_value'            => $lastOddSlab->and_value,
                                        'total_free_value'      => $lastOddSlab->and_value,
                                        'created_by'            => Auth::user()->id,
                                        'ipaddress'             => request()->ip(),
                                        'hostname'              => $request->getHttpHost()
                                    ]
                                    );
                                }
                            }
                        }
                    } // SKU else close
                } 
            } // foreach loop close
            // Special Value wise commission Start

           $totalCatValue =  DB::table('tbl_order_details')
                                ->select('p_total_price')
                                ->where('order_id',$lastOrderId)
                                ->where('cat_id',$catid)
                                ->where('order_det_id','!=', $request->get('id'))
                                ->sum('p_total_price');


            $checkGroupId = DB::select("SELECT * FROM tbl_special_value_wise_category
                            JOIN tbl_special_values_wise ON tbl_special_values_wise.id = tbl_special_value_wise_category.svwid
                            WHERE tbl_special_value_wise_category.categoryid=$catid AND $totalCatValue BETWEEN min AND max");

            if(sizeof($checkGroupId) >0 )
            {
               
                DB::table('tbl_special_temp_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('catid', $catid)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                DB::table('tbl_special_temp_commission')->insert(
                    [
                    'order_id'              => $lastOrderId,
                    'group_id'              => $checkGroupId[0]->group_id,
                    'offer_id'              => $checkGroupId[0]->svwid,
                    'catid'                 => $catid,
                    'distributor_id'        => $distributorID,
                    'point_id'              => $pointID,
                    'route_id'              => $routeID,
                    'retailer_id'           => $retailderID,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'cat_value'             => $totalCatValue,
                    'created_by'            => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost()
                    ]
                    );
               
            }

            $totalValue = DB::table('tbl_special_temp_commission')
                                ->select('order_id','group_id', DB::raw('SUM(cat_value) AS total'))
                                ->where('order_id', $lastOrderId)
                                ->groupBy('group_id')
                                ->get(); 

            

            foreach ($totalValue as $totalOfferValue) 
            {
                $commissionValue = $totalOfferValue->total;
                $checkCatValue = DB::select("SELECT * FROM tbl_special_values_wise WHERE status = 1 AND group_id=$totalOfferValue->group_id AND $commissionValue BETWEEN min AND max LIMIT 1");


                if(sizeof($checkCatValue) >0 )
                {
                   DB::table('tbl_special_commission')
                            ->where('order_id', $lastOrderId)
                            ->where('group_id', $totalOfferValue->group_id)
                            ->where('retailer_id', $retailderID)
                            ->delete();

                 DB::table('tbl_special_commission')->insert(
                    [
                    'order_id'              => $lastOrderId,
                    'order_date'            => date('Y-m-d h:i:s'),
                    'offer_id'              => $checkCatValue[0]->id,
                    'group_id'              => $totalOfferValue->group_id,
                    'distributor_id'        => $distributorID,
                    'point_id'              => $pointID,
                    'route_id'              => $routeID,
                    'retailer_id'           => $retailderID,
                    'fo_id'                 => Auth::user()->id,
                    'global_company_id'     => Auth::user()->global_company_id,
                    'commission'            => $checkCatValue[0]->commission_rate,
                    'total_free_value'      => $commissionValue,
                    'created_by'            => Auth::user()->id,
                    'ipaddress'             => request()->ip(),
                    'hostname'              => $request->getHttpHost()
                    ]
                    );
                   
                }

            }

            // Special Value wise commission end
         



        $itemsOrders = DB::table('tbl_order')
                    ->select('order_id','total_qty','total_value','grand_total_value')
                    ->where('order_id',$orderID)
                    ->first();

        $minQty     = $itemsOrders->total_qty - $itemQty;
        $minMinitValue = $itemQty * $itemPrice;
        $minValue   = ($itemsOrders->total_value) - ($minMinitValue);

        $orderUpdate= DB::table('tbl_order')->where('order_id', $orderID)->update(
                        [
                            'total_qty'         => $minQty,                
                            'total_value'       => $minValue,
                            'grand_total_value' => $minValue
                        ]
                    );

        $itemsDelete = DB::table('tbl_order_details')->where('order_det_id',$request->get('id'))->delete(); 

        if ($itemsDelete) 
        {
            return Redirect::back()->with('success', 'Successfully Deleted Order Product.');
        }


        //return 0;        
    }
    /* 
       =====================================================================
       ============================ Visit Manage ===========================
       =====================================================================
    */

    public function ssg_visit_process_order($retailerID,$routeID)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Visit';           // Page Slug Title

        $resultReason  = DB::table('ims_visit_reason')
                        ->where('type', 1)                    
                        ->get();

         return view('sales.visit.orderVisitOnly', compact('selectedMenu','pageTitle','retailerID','routeID','resultReason')); 
    }  


    public function ssg_visit_process_submit(Request $request)
    {

        //dd($request->all());

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->where('tbl_user_business_scope.is_active', 0) 
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
                         ->where('tbl_user_business_scope.is_active', 0) 
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

        DB::table('ims_tbl_visit_order')->insert(
            [
                'disid'                 => $distributorID,                
                'retailerid'            => $request->get('retailerID'),    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $request->get('routeID'),    
                'visit'                 => 1,    
                'order'                 => '',    
                'order_no'              => '', 
                'remarks'               => $request->get('remarks'),    
                'nonvisitedremarks'     => '',
                'entrydate'             => date('Y-m-d H:i:s'),
                'date'                  => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 2,
                'reasonid'              => $request->get('reasons')
            ]
        );

        return Redirect::to('/visit')->with('success', 'Successfully Visit Done.'); 
    }
   

    /* 
       =====================================================================
       ============================ Non-visit Manage =======================
       =====================================================================
    */

       public function ssg_nonvisit_process_order($retailerID,$routeID)
    {
        $selectedMenu   = 'Visit';                  // Required Variable
        $pageTitle      = 'Order Non-visit';           // Page Slug Title

        $resultReason  = DB::table('ims_visit_reason')
                        ->where('type', 2)                    
                        ->get();

         return view('sales.visit.orderNonVisitOnly', compact('selectedMenu','pageTitle','retailerID','routeID','resultReason')); 
    }

    public function ssg_nonvisit_process_submit(Request $request)
    {

        //dd($request->all());

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->where('tbl_user_business_scope.is_active', 0) 
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
                         ->where('tbl_user_business_scope.is_active', 0) 
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

        DB::table('ims_tbl_visit_order')->insert(
            [
                'disid'                 => $distributorID,                
                'retailerid'            => $request->get('retailerID'),    
                'foid'                  => Auth::user()->id,    
                'routeid'               => $request->get('routeID'),    
                'visit'                 => 1,    
                'order'                 => '',    
                'order_no'              => '', 
                'remarks'               => '',    
                'nonvisitedremarks'     => $request->get('remarks'),
                'entrydate'             => date('Y-m-d H:i:s'),
                'date'                  => date('Y-m-d H:i:s'),
                'user'                  => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'status'                => 1,
                'reasonid'              => $request->get('reasons')
            ]
        );

        return Redirect::to('/visit')->with('success', 'Successfully Non-visit Done.'); 
        //return Redirect::back()->with('success', 'Successfully Non-visit Done.'); 
    }


    /* 
       =====================================================================
       ============================ Order Manage ===========================
       =====================================================================
    */    

    
    public function ssg_order_manage()
    {
        $selectedMenu   = 'Order Manage';             // Required Variable
        $pageTitle      = 'Order Manage';            // Page Slug Title

        $fromdate = date('Y-m-d');
        $todate   = date('Y-m-d');
        /*
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        */      

        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*', 'tbl_order_details.*', DB::raw('SUM(tbl_order_details.order_qty) AS total_qty'),
                        DB::raw('SUM(tbl_order_details.p_grand_total) AS total_value'), 'tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name',
                        'tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order_details.order_det_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.ordered_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->groupBy('tbl_order.order_id')                    
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();        

        return view('sales.visit.orderManage', compact('selectedMenu','pageTitle','resultOrderList'));
    }
    

    public function ssg_invoice_details_order($orderMainId,$foMainId)
    {
        $selectedMenu   = 'Order Manage';                      // Required Variable
        $pageTitle      = 'Invoice Details';                  // Page Slug Title

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id', 'tbl_order_details.delivery_challan', 'tbl_order_details.delivered_date', 
                        'tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.order_date','tbl_order.print_count','tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_order.total_discount_percentage','tbl_order.total_discount_rate','tbl_retailer.name','tbl_retailer.owner','tbl_retailer.vAddress','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id',
        'tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone',
        'users.id','users.display_name' , 'users.sap_code')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',$foMainId)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->leftJoin('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        //->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($resultBundleOfferType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                //->whereNotNull('og.orderid')
                                ->where('og.orderid', $orderMainId)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)
                                //->where('og.orderid', $orderMainId)                              
                                ->first();
        }

        $reultProRate = '';

        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')

                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $orderMainId)                                              
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId) 
                        ->where('is_point_wise',0)                          
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $orderMainId)   
                        ->where('is_point_wise',1)                        
                        ->get();

        return view('sales/distributor/invoiceDetails', compact('selectedMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift','reultProRate','commissionWiseItem','specialValueWise','pointValueWise'));
    }

    public function ssg_order_edit_process($orderId,$retailderid,$routeid,$partial_order_id)
    {
        $selectedMenu   = 'Order Manage';             // Required Variable Menu
        $selectedSubMenu= '';                        // Required Variable Submenu
        $pageTitle      = 'Edit Order';             // Page Slug Title

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->where('tbl_user_business_scope.is_active', 0) 
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
                         ->where('tbl_user_business_scope.is_active', 0) 
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

        /*
        $resultCart     = DB::table('tbl_order')
                        ->where('order_type','Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)                        
                        ->first();
        */

        $prevoiusBalanceCommission = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id',$retailderid)
                    ->first();
    
        $resultCart =   DB::select("SELECT SUM(p_total_price) as grand_total_value,order_det_status FROM tbl_order_details 
        WHERE order_id = '".$orderId."' and partial_order_id = '".$partial_order_id."'");                       

        return view('sales/visit/categoryWithOrderEdit', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart','prevoiusBalanceCommission'));
    }

   
   
     public function ssg_add_to_edit_cart_products(Request $request)
    {
  
        $totalQty   = 0;
        $totalValue = 0;

        $countRows = count($request->get('qty'));        

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('qty')[$m];
            $totalValue = $totalValue + $request->get('qty')[$m] * $request->get('price')[$m];
        }

        $retailerID     = $request->get('retailer_id');
        $lastOrderId    = $request->get('order_id');
        $order_det_status    = $request->get('order_det_status');

        $resultCart     = DB::table('tbl_order')
                        ->where('order_id', $lastOrderId)
                        //->where('order_type','Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)                        
                        ->where('order_id', $lastOrderId)             
                        ->first();


        $orderNo  = $resultCart->order_no;
        $autoOrderId = $resultCart->auto_order_no;
        $offerType = $request->get('offer_group_type');
        //dd($offerType);

       
        
        $partial_order_id = 'part_'.$resultCart->order_count;

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                $checkItemsExiting = DB::table('tbl_order_details')
                                ->select('tbl_order_details.*','tbl_order.order_id','tbl_order.order_type')
                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                ->where('tbl_order_details.order_det_type', 'Confirmed')
                                ->where('tbl_order_details.order_id', $lastOrderId)
                                ->where('tbl_order_details.partial_order_id', $partial_order_id)
                                ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $upMainQty =  $request->get('qty')[$m];
                    $upMainWas =  $request->get('wastageQty')[$m];
                    $upMainPri = $upMainQty * $request->get('price')[$m];

                    DB::table('tbl_order_details')
                    ->where('tbl_order_details.order_id', $lastOrderId)
                    ->where('tbl_order_details.partial_order_id', $partial_order_id)
                    ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                    ->update(
                        [
                            'order_qty'         => $upMainQty,
                            'wastage_qty'       => $upMainWas,
                            'p_total_price'     => $upMainPri,
                            'p_grand_total'     => $upMainPri,
                            'offer_group_type'  => $offerType,
                            'order_det_status'  => $checkItemsExiting->order_det_status,
                            'order_update_date' => date('Y-m-d')
                        ]
                    );
                }
                else
                {
                    DB::table('tbl_order_details')->insert(
                        [
                            'order_id'          => $lastOrderId,
                            'partial_order_id'  => $partial_order_id,
                            'is_ordered'        => 'YES',
                            'ordered_date'      => date('Y-m-d H:i:s'),
                            'order_det_type'    => 'Confirmed',
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'wastage_qty'       => $request->get('wastageQty')[$m],                            
                            'p_unit_price'      => $request->get('price')[$m],
                            'p_total_price'     => $totalPrice,
                            'p_grand_total'     => $totalPrice,
                            'offer_group_type'  => $offerType,
                            'order_det_status' => $order_det_status
                        ]
                    );
                }

                $special_sku[]= $request->get('produuct_id')[$m];
            }
        }


        /*$oldGrandTotal  = $resultCart->grand_total_value;
        $newGrandTotal  = $oldGrandTotal + $totalValue;

        $oldGrandQty    = $resultCart->total_qty;
        $newGrandQty    = $oldGrandQty + $totalQty;

        DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', Auth::user()->id)
            //->where('order_type', 'Confirmed')
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'total_qty'             => $newGrandQty,
                'total_value'           => $newGrandTotal,
                'grand_total_value'     => $newGrandTotal,
                'update_by'             => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'update_date'           => date('Y-m-d h:i:s') // VVI sensative
            ]
        );*/


        $totalOrder = DB::table('tbl_order_details')
                    ->select('order_id', DB::raw('SUM(order_qty) AS totalQty'), DB::raw('SUM(p_grand_total) AS grandTotal'), DB::raw('SUM(p_total_price) AS totalValue'))
                    ->where('order_id', $lastOrderId)
                    ->first();


        $orderUpdate= DB::table('tbl_order')->where('order_id', $lastOrderId)->update(
                        [
                            'total_qty'             => $totalOrder->totalQty,                
                            'total_value'           => $totalOrder->totalValue,  
                            'grand_total_value'     => $totalOrder->grandTotal,
                            'update_by'             => Auth::user()->id,
                            'ipaddress'             => request()->ip(),
                            'hostname'              => $request->getHttpHost(),
                            'update_date'           => date('Y-m-d h:i:s') // VVI sensative
                        ]
                    );

        $this->pre_offer_entry( $lastOrderId, $orderNo, $autoOrderId, $request->get('distributor_id'), $request->get('retailer_id'), 
                                     $request->get('point_id'), $request->get('route_id'), $request->get('category_id')[0], 
                                     $special_sku, $offerType, $partial_order_id, $IsUpdate = false );

        return Redirect::back()->with('success', 'Successfully Updated Add To Cart.');        
    }
   
   /*
    public function ssg_add_to_edit_cart_products_old(Request $request)
    {
  
        $totalQty   = 0;
        $totalValue = 0;

        $countRows = count($request->get('qty'));        

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('qty')[$m];
            $totalValue = $totalValue + $request->get('qty')[$m] * $request->get('price')[$m];
        }

        $retailerID     = $request->get('retailer_id');
        $lastOrderId    = $request->get('order_id');

        $resultCart     = DB::table('tbl_order')
                        ->where('order_id', $lastOrderId)
                        ->where('order_type','Confirmed')                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailerID)                        
                        ->where('order_id', $lastOrderId)             
                        ->first();
        
        $oldGrandTotal  = $resultCart->grand_total_value;
        $newGrandTotal  = $oldGrandTotal + $totalValue;

        $oldGrandQty    = $resultCart->total_qty;
        $newGrandQty    = $oldGrandQty + $totalQty;

        DB::table('tbl_order')->where('order_id', $lastOrderId)
            ->where('fo_id', Auth::user()->id)
            ->where('order_type', 'Confirmed')
            ->where('global_company_id', Auth::user()->global_company_id)->update(
            [
                'total_qty'             => $newGrandQty,
                'total_value'           => $newGrandTotal,
                'grand_total_value'     => $newGrandTotal,
                'entry_by'              => Auth::user()->id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'entry_date'            => date('Y-m-d h:i:s')
            ]
        );

        for($m=0;$m<$countRows;$m++)
        {
            if($request->get('qty')[$m]!='')
            {
                $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                $checkItemsExiting = DB::table('tbl_order_details')
                                ->select('tbl_order_details.*','tbl_order.order_id','tbl_order.order_type')
                                ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                                ->where('tbl_order.order_type', 'Confirmed')
                                ->where('tbl_order_details.order_id', $lastOrderId)
                                ->where('tbl_order_details.product_id',$request->get('produuct_id')[$m])
                                ->first();

                if(sizeof($checkItemsExiting)>0)
                {
                    $upMainQty = $checkItemsExiting->order_qty + $request->get('qty')[$m];
                    $upMainWas = $checkItemsExiting->wastage_qty + $request->get('wastageQty')[$m];
                    $upMainPri = $upMainQty * $request->get('price')[$m];

                    DB::table('tbl_order_details')->where('product_id',$request->get('produuct_id')[$m])->update(
                        [
                            'order_qty'         => $upMainQty,
                            'wastage_qty'       => $upMainWas,
                            'p_total_price'     => $upMainPri,
                            'p_grand_total'     => $upMainPri
                        ]
                    );
                }
                else
                {
                    DB::table('tbl_order_details')->insert(
                        [
                            'order_id'          => $lastOrderId,
                            'cat_id'            => $request->get('category_id')[$m],
                            'product_id'        => $request->get('produuct_id')[$m],
                            'order_qty'         => $request->get('qty')[$m],
                            'wastage_qty'       => $request->get('wastageQty')[$m],                            
                            'p_unit_price'      => $request->get('price')[$m],
                            'p_total_price'     => $totalPrice,
                            'p_grand_total'     => $totalPrice
                        ]
                    );
                }
            }
        }

        return Redirect::back()->with('success', 'Successfully Updated Add To Cart.');        
    }
    */

    
    public function ssg_bucket_edit($orderid,$pointid,$routeid,$retailderid,$partial_id)
    {
        $selectedMenu   = 'Order Manage';           // Required Variable
        $pageTitle      = 'Bucket Edit';           // Page Slug Title
  
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

        //dd($resultDistributor);

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

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname',
                        'tbl_order.order_id','tbl_order.fo_id','tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_no',
                        'tbl_order.retailer_id','tbl_order.order_status')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')

                        ->where('tbl_order_details.order_det_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)            
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->where('tbl_order.order_id',$orderid)
                        ->where('tbl_order_details.partial_order_id',$partial_id)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','global_company_id','auto_order_no')
                        //->where('tbl_order_new.order_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->where('order_id',$orderid)
                        ->first();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id)                           
                        ->where('partial_order_id', $partial_id) 
                         ->where('is_point_wise', 0)                           
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id)                           
                        ->where('partial_order_id', $partial_id) 
                        ->where('is_point_wise', 1)                           
                        ->get();
        

        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')
                        ->select('osfq.free_id','osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $resultInvoice->order_id)
                        ->where('osfq.status', 3)                                              
                        ->get();



        $prevoiusBalanceCommission = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id',$retailderid)
                    ->first();

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->groupBy('order_id')                              
                        ->get();

        /////////////////// IF OFFER CHECKED ANY /////////////////////

        $regularOffersCheck = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $specialOffersCheck = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

                        /************************* Start Bundle Offer ********************************/             
                                    
       
        
        // for offer related start

        $resultBundleOffers = DB::table('tbl_bundle_offer')
                            ->where('global_company_id', Auth::user()->global_company_id)
                            ->where('iBusinessType', Auth::user()->business_type_id)
                            ->where('iOfferType', 3)
                            ->orderBy('iId', 'DESC')
                            ->first();

        //dd($resultBundleOffers);

        $resultBundleOffersCategories = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_bundle_category.offerId','tbl_bundle_category.categoryId')
                            ->leftJoin('tbl_bundle_category','tbl_bundle_offer.iId','=','tbl_bundle_category.offerId')
                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_bundle_offer.iBusinessType', Auth::user()->business_type_id)
                            ->get();

        $allowCategory = array();
        foreach ($resultBundleOffersCategories as $value) 
        {
            $allowCategory[] = $value->categoryId;
        }

        // if(sizeof($resultBundleOffers)>0)
        // {
        //     $currentDay = date('Y-m-d');
        //     $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //      FROM
        //      tbl_bundle_offer
        //      LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //      WHERE 
        //      tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");

        //     if(sizeof($resultBundleOffers)>0)
        //     {
        //         $resultBundleOffers  = DB::table('tbl_order_details')
        //                 ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
        //                 ->leftJoin('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
        //                 ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')

        //                 ->where('tbl_order.order_type','Ordered')                        
        //                 ->where('tbl_order.fo_id',Auth::user()->id)                        
        //                 ->where('tbl_order.retailer_id',$retailderid)
        //                 ->whereIn('tbl_product_category.id',$allowCategory)
        //                 ->groupBy('tbl_order_details.cat_id')                        
        //                 ->get();

        //         dd($resultBundleOffers);

        //         if(sizeof($resultBundleOffers)>0)
        //         {
        //             $currentDay = date('Y-m-d');
        //             $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
        //              FROM
        //              tbl_bundle_offer
        //              LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
        //              WHERE 
        //              tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");
        //         }
        //     }
        // }

        //dd($resultBundleOffers);

        $resultBundleOffersGift = DB::table('tbl_order_gift')
                                ->where('retailer_id', $retailderid)
                                ->where('global_company_id', Auth::user()->global_company_id)
                                ->where('fo_id',Auth::user()->id)
                                ->where('orderid',$resultInvoice->order_id)
                                ->first();

        $offerid = '';
        if(sizeof($resultBundleOffersGift)>0)
        {
            $offerid = $resultBundleOffersGift->offerid;
        }

        $resultBundleOfferType = DB::table('tbl_bundle_products')->select('offerId','productType')
                                ->where('offerId', $offerid)                                
                                ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.offerId', $offerid)
                                ->where('og.retailer_id', $retailderid)
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',Auth::user()->id)
                                ->where('og.orderid',$resultInvoice->order_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            // $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
            //                     ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.offerid','og.proid','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')
                               
            //                     ->leftJoin('tbl_product','og.proid','=','tbl_product.id')

            //                     ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.giftName')

            //                     ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
            //                     ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

            //                     ->where('og.offerId', $offerid)
            //                     ->where('og.retailer_id', $retailderid)
            //                     ->where('og.global_company_id', Auth::user()->global_company_id)
            //                     ->where('og.fo_id',Auth::user()->id)
            //                     ->where('og.orderid',$resultInvoice->order_id)
            //                     ->first();

            $resultBundleSelectedGift = DB::table('tbl_order_gift')
                                    ->where('orderid',$resultInvoice->order_id)
                                    ->first();                                    

            $resultBundleOffersGift = DB::table('tbl_bundle_product_details')
                                    ->select('tbl_bundle_product_details.*','tbl_product.id','tbl_product.name','tbl_product.depo')
                                    ->leftJoin('tbl_product','tbl_bundle_product_details.giftName','=','tbl_product.id')
                                    ->where('offerId',$resultBundleSelectedGift->offerid)
                                    ->where('slabId',$resultBundleSelectedGift->slab_id)
                                    ->where('groupid',$resultBundleSelectedGift->groupid)
                                    ->get();


        }

        
        
        /* End Bundle Offer */
        
        

        //dd($specialValueWise);

        //print_r($specialValueWise);
        //exit();

        //dd($specialValueWise);
                
        return view('sales/visit/bucketEdit', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID',
        'resultRetailer','resultCartPro','resultInvoice','resultBundleOffersGift','resultBundleOffers','specialOffers',
        'commissionWiseItem','specialValueWise','regularOffersCheck','specialOffersCheck','bundleOffersCheck','partial_id','prevoiusBalanceCommission','pointValueWise'));  
            
                        
    }                   
    
    public function ssg_bucket_edit_old($orderid,$pointid,$routeid,$retailderid,$part_id)
    {
        $selectedMenu   = 'Order Manage';           // Required Variable
        $pageTitle      = 'Bucket Edit';           // Page Slug Title
  
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

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')

                        ->where('tbl_order.order_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)            
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.retailer_id',$retailderid)
                        ->where('tbl_order.order_id',$orderid)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('order_id','order_type','fo_id','retailer_id','order_no','global_company_id','auto_order_no')
                        ->where('tbl_order.order_type','Confirmed')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->where('order_id',$orderid)
                        ->first();

        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', Auth::user()->id)
                        ->where('tbl_order_gift.retailer_id', $retailderid)
                        ->first();

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

        $currentDay = date('Y-m-d');

        $resultBundleOffers = DB::select("SELECT tbl_bundle_offer.*,tbl_bundle_offer_scope.iOfferId,tbl_bundle_offer_scope.iDivId,tbl_bundle_offer_scope.iPointId,tbl_bundle_offer_scope.iRouteId
         FROM
         tbl_bundle_offer
         LEFT JOIN tbl_bundle_offer_scope ON tbl_bundle_offer_scope.iOfferId=tbl_bundle_offer.iId
         WHERE 
         tbl_bundle_offer.iStatus='1' AND tbl_bundle_offer_scope.iDivId='$division_id' AND '$currentDay' BETWEEN dBeginDate AND dEndDate GROUP BY tbl_bundle_offer_scope.iDivId");

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        //dd($offerType);

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.fo_id', Auth::user()->id)                                
                                ->where('og.retailer_id', $retailderid)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.free_qty','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.fo_id', Auth::user()->id)                                
                                ->where('og.retailer_id', $retailderid)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->first();
        }

        //dd($resultInvoice->order_id);

        $specialOffers = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->groupBy('order_id')                              
                        ->get();




        $commissionWiseItem = DB::table('tbl_order_special_free_qty AS osfq')
                        ->select('osfq.order_id','osfq.product_id','osfq.total_free_qty','osfq.free_value','tbl_product.id','tbl_product.name','osfq.status')
                        ->leftJoin('tbl_product','osfq.product_id','=','tbl_product.id')
                        ->where('osfq.order_id', $resultInvoice->order_id)
                        ->where('osfq.status', 3)                                              
                        ->get();

        $specialValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id) 
                        ->where('is_point_wise', 0)                          
                        ->get();

        $pointValueWise = DB::table('tbl_special_commission')
                        ->where('order_id', $resultInvoice->order_id) 
                        ->where('is_point_wise', 1)                          
                        ->get();


        /////////////////// IF OFFER CHECKED ANY /////////////////////

        $regularOffersCheck = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $specialOffersCheck = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('order_id', $resultInvoice->order_id)
                        ->where('status', 0) // 0 for added
                        ->groupBy('order_id')                              
                        ->count();

        $bundleOffersCheck = DB::table('tbl_order_gift')
                        ->select('orderid')
                        ->where('orderid', $resultInvoice->order_id)
                        ->groupBy('orderid')                              
                        ->count();

        return view('sales.visit.bucketEdit', compact('selectedMenu','pageTitle','pointid','retailderid','routeid','distributorID','resultRetailer','resultCartPro','resultInvoice','resultBundleOffersGift','resultBundleOffers','specialOffers','commissionWiseItem','specialValueWise','regularOffersCheck','specialOffersCheck','bundleOffersCheck','pointValueWise'));
    }


    // Regular OFFER popup box

    public function ssg_show_regular_products(Request $request)
    {

        $orderid = $request->get('orderid');

        $regularOfferProducts = DB::table('tbl_order_free_qty')
                        ->select('tbl_order_free_qty.catid','tbl_order_free_qty.slab','tbl_product_category.name')
                        ->join('tbl_product_category','tbl_order_free_qty.catid','=','tbl_product_category.id')
                        ->where('tbl_order_free_qty.order_id', $orderid)
                        ->groupBy('tbl_order_free_qty.catid')
                        ->get();

        return view('sales.offer.regularGiftContent', compact('regularOfferProducts','orderid'));

    }

    public function ssg_show_regular_products_submit(Request $request)
    {
        $orderid        = $request->get('orderid');
        //$giftsProduct   = $request->get('giftsProduct');

        $offer = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderid)                                                
                        ->get();

        foreach ($offer as $value) {
            DB::table('tbl_order_free_qty')->where('order_id', $orderid)
                //->where('fo_id', Auth::user()->id)                
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [                    
                    'status' => 0
                ]
            );  
        }

        $offer = DB::table('tbl_order_special_free_qty')
                        ->select('order_id')
                        ->where('status','!=',3)
                        ->where('order_id', $orderid)                                                
                        ->get();

        foreach ($offer as $value) {
            DB::table('tbl_order_special_free_qty')->where('order_id', $orderid)
                //->where('fo_id', Auth::user()->id)                
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [                    
                    'status' => NULL
                ]
            );  
        }
       

        //$request->session()->put(['offersSelected', 'exclusive']);
        $request->session()->put('offersSelected','regular');

        return Redirect::back()->with('success', 'Successfully Add Regular Order Product.');
    }

    // SPECIAL OFFER 

    public function ssg_show_special_products(Request $request)
    {

        $orderid = $request->get('orderid');

        $specialOfferProducts = DB::table('tbl_order_special_free_qty')
                        ->select('tbl_order_special_free_qty.catid','tbl_order_special_free_qty.slab','tbl_product_category.name')
                        ->join('tbl_product_category','tbl_order_special_free_qty.catid','=','tbl_product_category.id')
                        ->where('tbl_order_special_free_qty.order_id', $orderid)
                        ->groupBy('tbl_order_special_free_qty.catid')
                        ->get();

        return view('sales.offer.specialGiftContent', compact('specialOfferProducts','orderid'));

    }

    public function ssg_show_special_products_submit(Request $request)
    {
        $orderid        = $request->get('orderid');
        $giftsProduct   = $request->get('giftsProduct');

        //dd($request->all());

        foreach ($giftsProduct as $value) 
        {
            $ex = explode('_', $value);

            DB::table('tbl_order_special_free_qty')->whereNotNull('slab')->where('order_id', $orderid)
                //->where('fo_id', Auth::user()->id)                
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [                    
                    'status' =>NULL
                ]
            );            
        }

        foreach ($giftsProduct as $value) 
        {
            $ex = explode('_', $value);
            
            DB::table('tbl_order_special_free_qty')->whereNotNull('slab')->where('order_id', $orderid)
                //->where('fo_id', Auth::user()->id)
                ->where('product_id', $ex[0])
                ->where('slab', $ex[1])
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [                    
                    'status' => 0
                ]
            );
        }


        //////////////////////// REGULAR OFFER NULL ////////////////////////////

        $offer = DB::table('tbl_order_free_qty')
                        ->select('order_id')
                        ->where('order_id', $orderid)                                                
                        ->get();

        foreach ($offer as $value) {
            DB::table('tbl_order_free_qty')->where('order_id', $orderid)
                //->where('fo_id', Auth::user()->id)                
                ->where('global_company_id', Auth::user()->global_company_id)->update(
                [                    
                    'status' => NULL
                ]
            );  
        }
        
        /* clear bundle offer */
         DB::table('tbl_order_gift')->where('orderid', $orderid)->delete();

        //$request->session()->put(['offersSelected', 'exclusive']);
        $request->session()->put('offersSelected','exclusive');

        return Redirect::back()->with('success', 'Successfully Add Special Order Product.');
    }
    
    /* Zubair Retailer Balance Start May-17-2018*/
    
    private function reatiler_credit_ledger($retailer_info = array())
    {
        if(is_array($retailer_info))
        {
            if($retailer_info['invoice_no']!='')
            {
              $retCreditLedger = DB::table('retailer_credit_ledger')->where('retailer_invoice_no',$retailer_info['invoice_no'])->delete();  
            }
            
            
            $credit_ledger_Data = array();
            $credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
            $credit_ledger_Data['collection_id'] = 0;
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
            
            $rowRet = DB::select("SELECT grand_total_value FROM tbl_order WHERE order_no = '".$retailer_info['invoice_no']."'");
            $retInVoiceSales = $rowRet[0]->grand_total_value;
            
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


    ///////////////////////////////// MASUD RANA ////////////////////////////////////


    public function ssg_order_valuewise_process($retailderid,$routeid,$orderid,$amount,$catid,$offerid,$pagestatus,$partialOrder)
    {
        if($pagestatus==1)
        {
            $selectedMenu   = 'Visit'; 
        }
        else if($pagestatus==2)
        {
            $selectedMenu   = 'Order Manage'; 
        }
        else if($pagestatus==3)
        {
            $selectedMenu = 'Order';
        }

        //$selectedMenu   = 'Visit';             // Required Variable
        $pageTitle      = 'New Order';        // Page Slug Title

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

        $resultCart     = DB::table('tbl_order')
                        //->where('fo_id',Auth::user()->id)                        
                        ->where('retailer_id',$retailderid)
                        ->where('order_id',$orderid)                       
                        ->first();

                        //dd($orderid,$retailderid);

        $valueSum = DB::table('tbl_order_special_free_qty')
                    ->where('status',3)
                    ->where('subcat_id',$catid)
                    ->where('order_id',$orderid)
                    ->sum('free_value');

        $prevoiusBalanceCommission = DB::table('tbl_retailer')
                    ->select('reminding_commission_balance')
                    ->where('retailer_id',$retailderid)
                    ->first();

        return view('sales/visit/categoryWithOrderValueWise', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart','valueSum','pagestatus','prevoiusBalanceCommission'));
    }

    public function ssg_add_to_cart_value_wise_products(Request $request)
    {

        //dd($request->all());

        $totalQty   = 0;
        $totalValue = 0;

        $countRows = count($request->get('qty'));  

        for($m=0;$m<$countRows;$m++)
        {
            $totalQty   = $totalQty + $request->get('qty')[$m];
            $totalValue = $totalValue + $request->get('qty')[$m] * $request->get('price')[$m];
        }

        if($request->get('free_amount') >= $totalValue)
        {
            $lessAmount = $request->get('free_amount') - $totalValue;

            for($m=0;$m<$countRows;$m++)
            {
                if($request->get('qty')[$m]!='')
                {

                    $alreadyHere = DB::table('tbl_order_special_free_qty')
                                ->where('order_id',$request->get('orderid'))
                                ->where('product_id',$request->get('produuct_id')[$m])
                                ->where('catid',$request->get('category_id')[$m])
                                ->where('fo_id',Auth::user()->id)
                                ->where('global_company_id',Auth::user()->global_company_id)
                                ->where('offer_id',$request->get('reference_offerid'))
                                ->where('status',3)
                                ->first();

                    if(sizeof($alreadyHere)>0)
                    {
                        $totalQty   = $alreadyHere->total_free_qty + $request->get('qty')[$m];
                        $totalPrice = $totalQty * $request->get('price')[$m];
                        

                        DB::table('tbl_order_special_free_qty')
                                ->where('order_id',$request->get('orderid'))
                                ->where('product_id',$request->get('produuct_id')[$m])
                                ->where('catid',$request->get('category_id')[$m])
                                ->where('fo_id',Auth::user()->id)
                                ->where('global_company_id',Auth::user()->global_company_id)
                                ->where('status',3)
                                ->where('offer_id',$request->get('reference_offerid'))
                                ->update(
                            [
                            
                                'total_free_qty'    => $totalQty,
                                'free_value'        => $totalPrice,                                
                                'created_at'        => date('Y-m-d h:i:s'),
                                'order_date'        => date('Y-m-d h:i:s')
                            ]
                        );
                    }
                    else
                    {
                        $totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

                        $rowUser = DB::select("Select user_type_id FROM users where id = '".Auth::user()->id."'");

                        DB::table('tbl_order_special_free_qty')->insert(
                            [
                                'order_id'          => $request->get('orderid'),
                                'catid'             => $request->get('category_id')[$m],
                                'subcat_id'         => $request->get('reference_catid'),
                                'product_id'        => $request->get('produuct_id')[$m],
                                'total_free_qty'    => $request->get('qty')[$m],
                                'free_value'        => $totalPrice,
                                'distributor_id'    => $request->get('distributor_id'),
                                'point_id'          => $request->get('point_id'),
                                'route_id'          => $request->get('route_id'),
                                'retailer_id'       => $request->get('retailer_id'),
                                'fo_id'             => ($rowUser[0]->user_type_id == 12)?Auth::user()->id:0,
                                'global_company_id' => Auth::user()->global_company_id,
                                'status'            => 3,
                                'offer_id'          => $request->get('reference_offerid'),
                                'created_at'        => date('Y-m-d h:i:s'),
                                'order_date'        => date('Y-m-d h:i:s')
                            ]
                        );
                    }
                }
            }

            return Redirect::to('/order-process-valuewise/'.$request->get('retailer_id').'/'.$request->get('route_id').'/'.$request->get('orderid').'/'.$lessAmount.'/'.$request->get('reference_catid').'/'.$request->get('reference_offerid').'/'.$request->get('reference_pagestatus').'/'.$request->get('partialOrder'))->with('success', 'Successfully Added Add To Cart.');
        }
        else
        {
            return Redirect::to('/order-process-valuewise/'.$request->get('retailer_id').'/'.$request->get('route_id').'/'.$request->get('orderid').'/'.$request->get('free_amount').'/'.$request->get('reference_catid').'/'.$request->get('reference_offerid').'/'.$request->get('reference_pagestatus').'/'.$request->get('partialOrder'))->with('warning', 'Sorry, not match amount');
        }        
    }


    //

    public function ssg_category_products_free(Request $request)
    {
        $categoryID     = $request->get('categories');
        $retailerID     = $request->get('retailer_id');

        $resultProduct = DB::table('tbl_product')
                        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
                        ->where('ims_stat', '0')                       
                        ->where('category_id', $categoryID)
                        ->orderBy('id', 'ASC')
                        ->orderBy('status', 'ASC')
                        ->orderBy('name', 'ASC')
                        ->get();

        $offerTypeCategory = DB::table('tbl_product_category')
                        ->select('id','offer_type')
                        ->where('id', $categoryID)
                        ->first();

        return view('sales/visit/allProductListFreeCommission', compact('resultProduct','categoryID','retailerID','offerTypeCategory'));
    }
    
}
