<?php 

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class DashboardController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 05/12/2017
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check
    }

    public function default_dashboard()
    {
    	$selectedMenu   = 'Home';           // Required Variable
        $pageTitle      = 'Dashboard';     // Page Slug Title

        if (session('userType')=='')
        {
        
            $commontSessionData = DB::table('users')
                        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                        ->where('users.email', Auth::user()->email)                    
                        ->first();

            //dd($commontSessionData);
            
            session()->put('businessName', $commontSessionData->global_company_name);
            session()->put('userType', $commontSessionData->user_type);
            session()->put('userFullName', $commontSessionData->display_name);
            session()->put('userTypeId', $commontSessionData->user_type_id);

            if(Auth::user()->user_type_id=='12' || Auth::user()->user_type_id=='5')
            {
                $commontSessionDataM = DB::table('users')
                        ->select('users.*','bscope.point_id','tbl_point.point_id','tbl_point.is_depot')

                        ->join('tbl_user_business_scope AS bscope', 'users.id', '=', 'bscope.user_id')

                        ->join('tbl_point', 'bscope.point_id', '=', 'tbl_point.point_id') 
                        ->where('users.email', Auth::user()->email)                    
                        ->first();

                //dd($commontSessionDataM);

                session()->put('isDepot', $commontSessionDataM->is_depot);

                if(sizeof($commontSessionDataM) >0 )
                {
                    $pointID = $commontSessionDataM->point_id;
                   
                    $exceptionPoint = DB::table('tbl_exception')->where('ex_point_id', $pointID)
                            ->first();
                    if(sizeof($exceptionPoint)>0)
                    {
                        session()->put('exceptionPoint', $exceptionPoint->ex_point_id);
                    }
                    else
                    {
                        session()->put('exceptionPoint', '');
                    }
                }
                else
                {
                   session()->put('exceptionPoint', '');
                }
            }

        }
        else if (session('userType')!='')
        {
            if (session('userTypeId')==12 && session('userPointId')=='') // for field officer (Fo)
            {
                $resultFo = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12) // 5 for distributor
                         ->where('users.id', Auth::user()->id)
                         //->where('tbl_user_business_scope.point_id', $pointID)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();
            }
        }

        $typesLayoutPath ='';
        $allCompack ='';

        $todayDate      = date('Y-m-d');
        $tomorrowDate   = date('Y-m-d');

        // for target
        $startDate     = date('Y-m'.'-01');
        $endDate       = date('Y-m'.'-31');

        if(Auth::user()->user_type_id==12) // Field Officer (FO)
        {
            $todate         = date('Y-m-d');
            $resultAttendanceList = DB::table('ims_attendence AS ia')
                            ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                            ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                            ->where('ia.foid', Auth::user()->id)
                            ->where('ia.type', 1)
                            ->whereBetween('ia.date', array($todate, $todate))
                            ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                            ->groupBy('ia.date')
                            ->orderBy('ia.id','DESC')                    
                            ->first();

            //dd($resultAttendanceList);

            if(sizeof($resultAttendanceList)==NUll)
            {
                return Redirect::to('/attendance'); 
            }
            // elseif(sizeof($resultAttendanceList)>0)
            // {
            //     return Redirect::to('/visit');
            // }
            

            $resultNewOrder = DB::table('tbl_order')
                        ->where('order_type', 'Confirmed')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todayDate, $todayDate))
                        ->orderBy('order_id','DESC')                    
                        ->count();
           // dd($resultNewOrder);

            $resultAttendance = DB::table('ims_attendence')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('foid', Auth::user()->id)
                        ->count();

            $attendanceSummery = DB::table('ims_attendence')                        
                        ->where('foid', Auth::user()->id) 
                        ->where('type', 1)                                            
                        ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($startDate, $endDate))->count();

            $todayResultVisit = DB::table('ims_tbl_visit_order')                        
                        ->where('foid', Auth::user()->id)                                      
                        ->where('status', 2)
                        ->whereBetween(DB::raw("(DATE_FORMAT(date,'%Y-%m-%d'))"), array($todayDate, $todayDate))
                        ->count();

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
                session()->put('pointName', $resultPoint->point_name);
                session()->put('divisionName', $resultPoint->div_name);
            }
            else
            {
                $pointID = '';
            }

            $resultRetailer = DB::table('tbl_retailer')                        
                        //->where('user', Auth::user()->id)
                        ->where('point_id', $pointID)
                        ->orderBy('retailer_id','DESC')
                        ->count();

            $monthlyTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                        ->where('employee_id', Auth::user()->employee_id)
                        ->whereDate('start_date', '>=', $startDate)
                        ->whereDate('end_date', '<=', $endDate)
                        ->groupBy('fo_id')
                        ->sum('total_value');

            $totalTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                        ->where('employee_id', Auth::user()->employee_id)
                        ->groupBy('fo_id')
                        ->sum('total_value');

                        //dd($monthlyTarget);

           $monthlyAchivement = DB::table('tbl_order')->select('global_company_id','fo_id','total_value','update_date')
                        ->where('fo_id', Auth::user()->id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereDate('update_date', '>=', $startDate)
                        ->whereDate('update_date', '<=', $endDate)
                        ->groupBy('fo_id')
                        ->sum('total_value');

            $totalAchivement = DB::table('tbl_order')->select('global_company_id','fo_id','total_value','update_date')
                        ->where('fo_id', Auth::user()->id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->groupBy('fo_id')
                        ->sum('total_value');
            

            if($monthlyTarget >0)
            {
                $todayTarget = ($monthlyTarget/26);
            }
            else
            {
                $todayTarget = 0;
            }

            $todayAchivement = DB::table('tbl_order')->select('global_company_id','fo_id','total_value','order_date')
                        ->where('fo_id', Auth::user()->id)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereDate('order_date', '=', $todayDate)
                        ->groupBy('fo_id')
                        ->sum('total_value');


            // CATEGORY WISE TARGET

            $glsTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                        ->where('employee_id', Auth::user()->employee_id)
                        ->whereDate('start_date', '>=', $startDate)
                        ->whereDate('end_date', '<=', $endDate)
                        ->where('cat_id', 1)
                        ->groupBy('fo_id')
                        ->sum('total_value');

            $glsAchievement =  DB::table('tbl_order')
                               ->select('tbl_order.global_company_id','tbl_order.fo_id','tbl_order.total_value','tbl_order.update_date','tbl_order_details.cat_id','tbl_order_details.delivered_value')
                               ->join('tbl_order_details', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                               ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                               ->where('tbl_order.fo_id', Auth::user()->id)
                               ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                               ->where('tbl_order_details.cat_id', 1)
                               ->sum('tbl_order_details.delivered_value'); 

            $cflTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                        ->where('employee_id', Auth::user()->employee_id)
                        ->whereDate('start_date', '>=', $startDate)
                        ->whereDate('end_date', '<=', $endDate)
                        ->where('cat_id', 3)
                        ->groupBy('fo_id')
                        ->sum('total_value');

            $cflAchievement =  DB::table('tbl_order')
                               ->select('tbl_order.global_company_id','tbl_order.fo_id','tbl_order.total_value','tbl_order.update_date','tbl_order_details.cat_id','tbl_order_details.delivered_value')
                               ->join('tbl_order_details', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                               ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                               ->where('tbl_order.fo_id', Auth::user()->id)
                               ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                               ->where('tbl_order_details.cat_id', 3)
                               ->sum('tbl_order_details.delivered_value');

            $ledTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                        ->where('employee_id', Auth::user()->employee_id)
                        ->whereDate('start_date', '>=', $startDate)
                        ->whereDate('end_date', '<=', $endDate)
                        //->whereIn('cat_id', 3)
                        ->whereIn('cat_id', [4, 5, 6, 7])
                        ->groupBy('fo_id')
                        ->sum('total_value');

            $ledAchievement =  DB::table('tbl_order')
                               ->select('tbl_order.global_company_id','tbl_order.fo_id','tbl_order.total_value','tbl_order.update_date','tbl_order_details.cat_id','tbl_order_details.delivered_value')
                               ->join('tbl_order_details', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                               ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                               ->where('tbl_order.fo_id', Auth::user()->id)
                               ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                               //->where('tbl_order_details.cat_id', 3)
                               ->whereIn('tbl_order_details.cat_id', [4, 5, 6, 7])
                               ->sum('tbl_order_details.delivered_value');      


            $allCompack = compact('selectedMenu','pageTitle','resultNewOrder','resultAttendance','attendanceSummery','resultRetailer','monthlyTarget','totalTarget','totalAchivement','monthlyAchivement','todayTarget','todayAchivement','startDate','endDate','todayResultVisit','glsTarget','glsAchievement','cflTarget','cflAchievement','ledTarget','ledAchievement');
            $typesLayoutPath = 'sales/masterDashboard';
        }
		elseif(Auth::user()->user_type_id==15) // Billing Dept
	    {
			//echo 'I am billing'; exit;
			
			$depoTodaySend = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(req_date) = CURDATE() and req_status = 'send'");

			$depoTodayAck = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(approved_date) = CURDATE()");

			$depoTodayApprvd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(approved_date) = CURDATE()");
									
			$depoTodayDownload = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(download_date) = CURDATE()");	

			$depoTodayDlvrd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE req_status = 'delivered' AND DATE(delivered_date) = CURDATE()");

			$depoTodayRcvd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(received_date) = CURDATE()");
									
			$depoTodayCnled = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE DATE(canceled_date) = CURDATE()");						

			$depoTotalStock = DB::select("SELECT SUM(stock_qty) totStock FROM tbl_product");								
			 
            $allCompack = compact('selectedMenu','pageTitle', 'depoTodaySend', 'depoTodayAck', 'depoTodayApprvd',
									'depoTodayDownload','depoTodayDlvrd','depoTodayRcvd', 'depoTodayCnled', 'depoTotalStock');
            
			$typesLayoutPath = 'Depot.depotBillingMasterDashboard';
        
		}
		
		elseif(Auth::user()->user_type_id==16) // Delivery Panel
        {
			
			//echo 'I am Delivery'; exit;
			
            $allCompack = compact('selectedMenu','pageTitle', 'resDepotSummary', 'resStockSummary', 'resultOrderDelivery','resultOrderPending','resultOrderPreviousRe','resultOrderTotalRe');
            
			$typesLayoutPath = 'Depot.depotDeliveryMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==5) // Distributor (D)
        {

            $resultOrderPreviousRe = DB::table('tbl_order')
                        ->select('tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                       ->whereDate('order_date', '=', $todayDate)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->count();

            $resultOrderTotalRe = DB::table('tbl_order')
                        ->select('tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)                        
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->count();

            $resultOrderDelivery = DB::table('tbl_order')
                        ->select('tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->count();

            $resultOrderPending = DB::table('tbl_order')
                        ->select('tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->count();
						
			/* Zubair */				
			$depoTodayReq = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(req_date) = CURDATE()");
									
			$depoTodaySend = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(sent_date) = CURDATE()");

			$depoTodayAck = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(acknowledge_date) = CURDATE()");

			$depoTodayApprvd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(approved_date) = CURDATE()");

			$depoTodayCancld = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(canceled_date) = CURDATE()");

			$depoTodayDelvrd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND req_status = 'delivered' AND DATE(delivered_date) = CURDATE()");

			$depoTodayRcvd = DB::select("SELECT req_status, COUNT(req_id) totCount
									FROM depot_requisition WHERE depot_in_charge =  '".Auth::user()->id."' 
									AND DATE(received_date) = CURDATE()");							
		
			/*
			$resStockSummary = DB::select("SELECT SUM(stock_qty) totStock FROM depot_stock 
			WHERE point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')"); */

			$PointInfo = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".Auth::user()->id."')
									");
			$totStockVal = 0;
			if(sizeof($PointInfo)>0)
			{
				
				/*  old 
				$resStockSummary = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.depo * ds.stock_qty) as totStockVal 
											FROM depot_stock  ds JOIN tbl_product p ON ds.product_id = p.id
											WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
											WHERE user_id = '".Auth::user()->id."')");
				*/							
				
				// new from 21-01-2019
				
				$sql_point = DB::select("SELECT point_id FROM tbl_user_business_scope 
											WHERE user_id = '".Auth::user()->id."'");
				

				
											
				$resStockSummary = DB::table('depot_inventory')
                                    ->select('inventory_type', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
                                    ->where('point_id', $sql_point[0]->point_id)
									//->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->groupBy('inventory_type')
                                    ->get();	

				$totProdInQnty =0; $totProdInValue = 0;
				$totProdOutQnty =0; $totProdOutValue = 0;
				$totStock =0; $totStockVal = 0; 
									
				if( sizeof($resStockSummary)>0 )
				{
									
					foreach($resStockSummary as $rowStockSummary)
					{
						
						$StockINfo['totProdQnty'][$rowStockSummary->inventory_type] = $rowStockSummary->tot_qnty;
						$StockINfo['totProdValue'][$rowStockSummary->inventory_type] = $rowStockSummary->tot_value;
						
					}	
					
					if(array_key_exists(1,$StockINfo['totProdQnty']))
					{
						$totProdInQnty  = $StockINfo['totProdQnty'][1];
						$totProdInValue = $StockINfo['totProdValue'][1];
					} else {
						$totProdInQnty  = 0;
						$totProdInValue = 0;	
					}

					
					if(array_key_exists(2,$StockINfo['totProdQnty']))
					{
						$totProdOutQnty  = $StockINfo['totProdQnty'][2];
						$totProdOutValue = $StockINfo['totProdValue'][2];
					} else {
						$totProdOutQnty  = 0;
						$totProdOutValue = 0;
					}
					
					
					$totStock    = $totProdInQnty - $totProdOutQnty;
					$totStockVal = $totProdInValue - $totProdOutValue;
									
								
				} else {
					
					$totStock = 0;
                   
					
				}
				
				
				$resProductIN = DB::table('depot_inventory')
                                    ->select('product_id', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
                                    ->where('point_id', $sql_point[0]->point_id)
                                    ->where('inventory_type', 1)
									//->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->groupBy('product_id')
                                    ->get();
						
				$TotProdInPrice = 0;
				if(sizeof($resProductIN)>0)
				{
					foreach($resProductIN as $rowStockProdIN)
					{	
						$resProdPrice = DB::table('tbl_product')
							->select('id', 'mrp', 'depo', 'distri')
							->where('id', $rowStockProdIN->product_id)
							->first();	
					
						if(sizeof($resProdPrice)>0)
						{
							$TotProdInPrice += $rowStockProdIN->tot_qnty * $resProdPrice->depo;
						}									
						
					}
				}

					
						
				$resProductOut = DB::table('depot_inventory')
							->select('product_id', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
							->where('point_id', $sql_point[0]->point_id)
							->where('inventory_type', 2)
							//->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
							->groupBy('product_id')
							->get();
							
				$TotProdOutPrice = 0;
				if(sizeof($resProductOut)>0)
				{
					foreach($resProductOut as $rowStockProdOut)
					{	
						$resProdPrice = DB::table('tbl_product')
							->select('id', 'mrp', 'depo', 'distri')
							->where('id', $rowStockProdOut->product_id)
							->first();	
					
						if(sizeof($resProdPrice)>0)
						{
							$TotProdOutPrice += $rowStockProdOut->tot_qnty * $resProdPrice->depo;
						}									
						
					}
				}
						

						
				//$TotProdInPrice = 50;
				//$TotProdOutPrice = 90;
				
				$StockBalance = $TotProdInPrice - $TotProdOutPrice;
				$totStockVal = $StockBalance;

									
											
				//echo 	'Hellow'; exit;						
			} else {								
				$resStockSummary = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.distri * ds.stock_qty) as totStockVal 
											FROM depot_stock  ds JOIN tbl_product p ON ds.product_id = p.id
											WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
											WHERE user_id = '".Auth::user()->id."')");	

											
			}		
										
			//Depot Balance Dashboard

			/*
			$depoTotSales = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order 
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')  
										AND order_type = 'Delivered'");
			*/							
				
			/*	
			$depoTotSales = DB::select("SELECT SUM(retailer_invoice_sales) tot_sales, SUM(retailer_sales_return) tot_sales_return 
										FROM  retailer_credit_ledger 
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");
			*/

			$depoTotSales = DB::select("SELECT SUM(retailer_invoice_sales) tot_sales
										FROM  retailer_credit_ledger 
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");
										
			
			$depoTotSalesReturn = DB::select("SELECT SUM(retailer_sales_return) tot_sales_return 
										FROM  retailer_credit_ledger 
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");							
										
			
			//$depoTotReturnChangeSales = array();
					
			/*			
			$depoTotReturnChangeSales = DB::select("SELECT SUM(retailer_invoice_sales) tot_RetChngsales FROM  retailer_credit_ledger 
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')  
										AND trans_type = 'return_debited'");	
			*/							
										
			$retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer WHERE point_id 
										in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");	

			$depoOpenCashInHand = DB::select("SELECT opening_cash_in_hand FROM tbl_depot_summary WHERE point_id 
										in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");									

			$depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection
										WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");

			$depoTotExpense = DB::select("SELECT SUM(c.trans_amount) tot_expense
									FROM depot_cash_book c JOIN depot_accounts_head h ON c.perticular_head_id = h.accounts_head_id
									WHERE h.accounts_head_type = 'expense' 
									AND point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')");		
			
			/*
			$totStockVal = 0;
			if(sizeof($resStockSummary)>0)
			{
				$totStockVal = $resStockSummary[0]->totStockVal;
			}
			*/			
				
			
											/* Depot Market Credit */
											
			$depoMarketCredit = 0;
			if(sizeof($retOpenTot)>0)
			{
				$totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
			} else {
				$totRetOpenBal = 0;
			}
			
			if(sizeof($depoTotSales)>0)
			{
				$tot_sales = $depoTotSales[0]->tot_sales;
				
				
				if($depoTotSalesReturn[0]->tot_sales_return>0)
				{
					$tot_sales -= $depoTotSalesReturn[0]->tot_sales_return;
				}
				
				
				
			} else {
				$tot_sales = 0;
			}
			
			/*
			if(sizeof($depoTotReturnChangeSales)>0)
			{
				$ret_sales = $depoTotReturnChangeSales[0]->tot_RetChngsales;
				//$tot_sales += $depoTotReturnChangeSales[0]->tot_RetChngsales;
			}
			*/
		
			
			if(sizeof($depoTotCollection)>0)
			{
				$tot_collection = $depoTotCollection[0]->tot_collection;
			} else {
				$tot_collection = 0;
			}
			
			//echo 'openBla = ' .  $totRetOpenBal . ' tot_sales = ' . $tot_sales . ' tot_collection = ' . $tot_collection . '<br/> <br/>'; exit;
			
			//echo 'openBla = ' .  $totRetOpenBal . ' act_sales = ' . $act_sales . ' ret_sales = ' . $ret_sales . ' tot_collection = ' . $tot_collection; 
			
			//exit;
			
			$depoMarketCredit = ($totRetOpenBal + $tot_sales) - $tot_collection;
			
			
											
											/* Depot Cash In Hand */
											
			$depoCashInHand = 0;
			if(sizeof($depoOpenCashInHand)>0)
			{
				$opening_cash_in_hand = $depoOpenCashInHand[0]->opening_cash_in_hand;
			} else {
				$opening_cash_in_hand = 0;
			}
			
			if(sizeof($depoTotCollection)>0)
			{
				$tot_collection = $depoTotCollection[0]->tot_collection;
			} else {
				$tot_collection = 0;
			}
			
			if(sizeof($depoTotExpense)>0)
			{
				$tot_expense = $depoTotExpense[0]->tot_expense;
			} else {
				$tot_expense = 0;
			}
			
			//echo  'open bal = ' . $opening_cash_in_hand . 'col = '. $tot_collection . 'expense ='  .$tot_expense; exit;
			
			$depoCashInHand = ($opening_cash_in_hand + $tot_collection) - $tot_expense;
			
			$depoBalance = $totStockVal + $depoMarketCredit + $depoCashInHand;					



            ///////////////////////////// MD. MASUD RANA ///////////////////////////

            $distTodayReq = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(req_date) = CURDATE()");
                                    
            $distTodaySend = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(sent_date) = CURDATE()");

            $distTodayAck = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(acknowledge_date) = CURDATE()");

            $distTodayApprvd = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(approved_date) = CURDATE()");

            $distTodayCancld = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(canceled_date) = CURDATE()");

            $distTodayDelvrd = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND req_status = 'delivered' AND DATE(delivered_date) = CURDATE()");

            $distTodayRcvd = DB::select("SELECT req_status, COUNT(req_id) totCount
                                    FROM distributor_requisition WHERE distributor_in_charge =  '".Auth::user()->id."' 
                                    AND DATE(received_date) = CURDATE()");                          
			
			/*
            $resStockSummaryDist = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.distri * ds.stock_qty) as totStockVal 
                                        FROM distributor_stock  ds JOIN tbl_product p ON ds.product_id = p.id
                                        WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
                                        WHERE user_id = '".Auth::user()->id."')");
			*/							


            // total wastage stock
            $totalWastageStock = DB::table('tbl_wastage')->select('total_delivery_qty')
                        ->where('distributor_id', Auth::user()->id)
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_qty');

            $totalWastageOrderStock = DB::table('tbl_order_details')
                        ->select('tbl_order_details.wastage_qty','tbl_order_details.order_id','tbl_order.order_id','tbl_order.distributor_id','tbl_order.order_type')
                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->where('tbl_order.order_type', 'Delivered')
                        ->sum('tbl_order_details.wastage_qty');

            $totalWastageBalance = $totalWastageStock + $totalWastageOrderStock;

            $totalOfferQty =0;
            $totalOfferQty1 =0;
            $totalOfferQty2 =0;
            $totalOfferQty3 =0;
            $totalOfferValue =0;
            $totalOfferValue1 =0;
            $totalOfferValue2 =0;
            $totalOfferValue3 =0;

            //$pDate = date('Y-m-d',strtotime('-2 Day'));
                $SpecialFreeQty = DB::table('tbl_order_special_free_qty')
                        ->select('tbl_order_special_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                        ->where('distributor_id', Auth::user()->id)
                        ->whereIn('status', array('0','3'))
                        ->first();

                if(sizeof($SpecialFreeQty)>0)
                {
                    $totalOfferQty = $SpecialFreeQty->freeQty;
                    $totalOfferValue = $SpecialFreeQty->freeValue;
                }

                $SpecialFreeAndQty = DB::table('tbl_order_special_and_free_qty')
                        ->select('tbl_order_special_and_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                        ->where('distributor_id', Auth::user()->id)
                        ->first();

                if(sizeof($SpecialFreeAndQty)>0)
                {
                    $totalOfferQty1 = $SpecialFreeAndQty->freeQty;
                    $totalOfferValue1 = $SpecialFreeAndQty->freeValue;
                }

                $RegularFreeAndQty = DB::table('tbl_order_regular_and_free_qty')
                        ->select('tbl_order_regular_and_free_qty.*',DB::raw("SUM(total_free_qty) as freeQty"),DB::raw("SUM(free_value) as freeValue"))
                        ->where('distributor_id', Auth::user()->id)
                        ->first();

                if(sizeof($RegularFreeAndQty)>0)
                {
                    $totalOfferQty2 = $RegularFreeAndQty->freeQty;
                    $totalOfferValue2 = $RegularFreeAndQty->freeValue;
                }

                $totalOfferQty3 += $totalOfferQty + $totalOfferQty1 + $totalOfferQty2;
                $totalOfferValue3 += $totalOfferValue + $totalOfferValue1 + $totalOfferValue2;
            
           
			  $allCompack = compact('selectedMenu','pageTitle', 'depoTodayReq', 'depoTodaySend', 'depoTodayAck', 'depoTodayApprvd',
								 'depoTodayCancld', 'depoTodayDelvrd', 'depoTodayRcvd',
			'resStockSummary', 'resultOrderDelivery','resultOrderPending','resultOrderPreviousRe','resultOrderTotalRe' , 'distTodayReq', 'distTodaySend', 'distTodayAck', 'distTodayApprvd',
                                 'distTodayCancld', 'distTodayDelvrd', 'distTodayRcvd',
            'resStockSummaryDist', 'depoMarketCredit', 'depoCashInHand', 'depoBalance','totalOfferQty3','totalOfferValue3','totalWastageBalance',
			'totStock','totStockVal');
			
            $typesLayoutPath = 'sales.distributor.distributorMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==1) // Super Admin (SP)
        {
            $activeUsers   = DB::table('users')->select('is_active')->where('is_active', 0)->where('user_type_id', 12)->count();
            $activeUsersDist   = DB::table('users')->select('is_active')->where('is_active', 0)->where('user_type_id', 5)->count();
            $inactiveUsers = DB::table('users')->select('is_active')->where('user_type_id', 12)->where('is_active', '!=', 0)->count();
            $activeProducts = DB::table('tbl_product')->select('status')->where('status', 0)->count();
            $activeOffers = DB::table('tbl_bundle_offer')->select('iStatus')->where('iStatus', 1)->count();

            $activeDivision   = DB::table('tbl_division')->select('div_status')->where('div_status', 0)->count();
            $activePoint      = DB::table('tbl_point')->select('point_status')->count();
            $activeRoute      = DB::table('tbl_route')->select('status')->where('status', 0)->count();
            $activeTerritory  = DB::table('tbl_territory')->count();

            //Maung
            $yesterdayTotalSales=DB::select("SELECT sum(grand_total_value) as delval from tbl_order  where DATE(order_date)=DATE(NOW() - INTERVAL 1 DAY)and order_type='Delivered'");
          //dd($yesterdayTotalSales);
            $thisMonthTotalSales=DB::select("SELECT SUM(grand_total_value) as totmonthSales FROM tbl_order WHERE MONTH(order_date) = MONTH(CURRENT_DATE())and order_type='Delivered'");
            $totalcollection=DB::select("SELECT SUM(grand_total_value) as totcollect FROM tbl_order where order_type='Ordered'");

            //Maung
            $allCompack = compact('selectedMenu','pageTitle','activeUsers','inactiveUsers','activeProducts','activeOffers','activeDivision','activePoint','activeRoute','','activeTerritory','activeUsersDist','yesterdayTotalSales','thisMonthTotalSales','totalcollection');

            $typesLayoutPath = 'sales.admin.adminMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==4) // Sales Coordinator (SC)
        {

             $activeUsers   = DB::table('users')->select('is_active')->where('is_active', 0)->where('user_type_id', 12)->where('global_company_id', Auth::user()->global_company_id)->count();
            $inactiveUsers = DB::table('users')->select('is_active')->where('is_active', '!=', 0)->where('user_type_id', 12)->where('global_company_id', Auth::user()->global_company_id)->count();
            $activeProducts = DB::table('tbl_product')->select('status')->where('status', 0)->count();
            $activeOffers = DB::table('tbl_bundle_offer')->select('iStatus')->where('iStatus', 1)->count();

            $activeDivision   = DB::table('tbl_division')->select('div_status')->where('div_status', 0)->count();
            $activePoint      = DB::table('tbl_point')->select('point_status')->where('global_company_id', Auth::user()->global_company_id)->count();
            $activeRoute      = DB::table('tbl_route')->select('status')->where('status', 0)->where('global_company_id', Auth::user()->global_company_id)->count();
            $activeTerritory  = DB::table('tbl_territory')->where('global_company_id', Auth::user()->global_company_id)->count();

            $allCompack = compact('selectedMenu','pageTitle','activeUsers','inactiveUsers','activeProducts','activeOffers','activeDivision','activePoint','activeRoute','','activeTerritory');
            $typesLayoutPath = 'sales.salesCoordinator.masterDashboard';
        }
        elseif(Auth::user()->user_type_id==2) // Sales Admin (SA)
        {
            $activeUsers   = DB::table('users')->select('is_active')->where('is_active', 0)->count();
            $inactiveUsers = DB::table('users')->select('is_active')->where('is_active', '!=', 0)->count();
            $activeProducts = DB::table('tbl_product')->select('status')->where('status', 0)->count();
            $activeOffers = DB::table('tbl_bundle_offer')->select('iStatus')->where('iStatus', 1)->count();
            $allCompack = compact('selectedMenu','pageTitle','activeUsers','inactiveUsers','activeProducts','activeOffers');
            $typesLayoutPath = 'sales.salesAdmin.masterDashboard';
        }
        elseif(Auth::user()->user_type_id==3) // Management ( MGT )
        {

            $startDate          = date('Y-m'.'-01');
            $endDate            = date('Y-m'.'-31');
            $currentYearStart   = date('Y'.'-01-01');
            $currentYearEnd     = date('Y'.'-12-31');

            //dd($currentYearStart,$currentYearEnd);
            
            $yearlyTarget  = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$currentYearStart)
                    ->whereDate('end_date','<=',$currentYearEnd)
                    ->sum('total_value');

            $currentMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$startDate)
                    ->whereDate('end_date','<=',$endDate)
                    ->sum('total_value');

            $totalAchivement = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)                        
                        ->where('order_type', 'Delivered')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentYearStart ,$currentYearEnd))
                        ->sum('total_delivery_value');

            $currentMonthAchivement = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)                        
                        ->where('order_type', 'Delivered')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($startDate ,$endDate))
                        ->sum('total_delivery_value');

            //dd($yearlyTarget,$totalAchivement);

            //dd($totalAchivement);

            $totalAchivementJan = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-01-01') ,date('Y'.'-01-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementFeb = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-02-01') ,date('Y'.'-02-29')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementMar = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-03-01') ,date('Y'.'-03-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementApr = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-04-01') ,date('Y'.'-04-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementMay = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-05-01') ,date('Y'.'-05-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementJun = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-06-01') ,date('Y'.'-06-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementJul = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-07-01') ,date('Y'.'-07-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementAug = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-08-01') ,date('Y'.'-08-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementSep = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-09-01') ,date('Y'.'-09-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');


            $totalAchivementOct = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-10-01') ,date('Y'.'-10-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');


            $totalAchivementNov = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-11-01') ,date('Y'.'-11-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            $totalAchivementDec = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y'.'-12-01') ,date('Y'.'-12-31')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');

            //dd($totalAchivementOct);





            $totalCreditCollection =  0; //DB::table('tbl_order')
            //             ->where('global_company_id', Auth::user()->global_company_id)
            //             ->where('order_type', 'Delivered')
            //             ->sum('total_delivery_value');

            $totalcollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($startDate ,$endDate))
                    ->sum('collection_amount');

            $yesterdayTotalSales = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('order_type', 'Delivered')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y-m-d', strtotime('-1 day')),date('Y-m-d', strtotime('-1 day'))))
                        ->sum('total_delivery_value');

            $thisMonthTotalSales = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('order_type', 'Delivered')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($startDate ,$endDate))
                        ->sum('total_delivery_value');

            $retailersOpeningBalance = DB::table('tbl_retailer')
                    ->where('global_company_id', Auth::user()->global_company_id)                    
                    ->sum('opening_balance');

            $todayCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array(date('Y-m-d'),date('Y-m-d')))
                    ->sum('collection_amount');

            $todayAchivement = DB::table('tbl_order')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array(date('Y-m-d') ,date('Y-m-d')))
                        ->where('order_type', 'Delivered')
                        ->sum('total_delivery_value');


            $allCompack = compact('selectedMenu','pageTitle','activeOffers','yearlyTarget','currentMonthTarget','currentMonthAchivement','totalAchivement','yesterdayTotalSales','thisMonthTotalSales','totalcollection','totalCreditCollection','startDate','endDate','totalAchivementJan','totalAchivementFeb','totalAchivementMar','totalAchivementApr','totalAchivementMay','totalAchivementJun','totalAchivementJul','totalAchivementAug','totalAchivementSep','totalAchivementOct','totalAchivementNov','totalAchivementDec','retailersOpeningBalance','todayCollection','todayAchivement');
             
            $typesLayoutPath = 'sales/mgt/mgtMasterDashboard';
        
        }
        elseif(Auth::user()->user_type_id==19) // System Admin ( SA )
        {
            
            $allCompack      = compact('selectedMenu','pageTitle');             
            $typesLayoutPath = 'sales/systemAdmin/systemMasterDashboard';
        
        }

        elseif(Auth::user()->user_type_id==10) // TSM
        {
                
            $user_id=Auth::user()->id;
            $user=DB::table('tbl_user_business_scope')
                ->where('user_id', $user_id)
                ->first();
//             $end_date     = date('Y-m-31');
//             $start_date     = date('Y-m-01');
          

//             $activeUsers   = DB::table('users')->select('is_active')->where('is_active', 0)->where('user_type_id', 12)->count();
//             $activeUsersDist   = DB::table('users')->select('is_active')->where('is_active', 0)->where('user_type_id', 5)->count();
//             $inactiveUsers = DB::table('users')->select('is_active')->where('user_type_id', 12)->where('is_active', '!=', 0)->count();
//             $activeProducts = DB::table('tbl_product')->select('status')->where('status', 0)->count();
//             $activeOffers = DB::table('tbl_bundle_offer')->select('iStatus')->where('iStatus', 1)->count();

//             $activeDivision   = DB::table('tbl_division')->select('div_status')->where('div_status', 0)->count();
//             $activePoint      = DB::table('tbl_point')->select('point_status')->count();
//             $activeRoute      = DB::table('tbl_route')->select('status')->where('status', 0)->count();
//             $activeTerritory  = DB::table('tbl_territory')->count();
//            //Maung
//             $yesterdayTotalSales=DB::select("SELECT sum(grand_total_value) as delval from tbl_order  where DATE(order_date)=DATE(NOW() - INTERVAL 1 DAY)and order_type='Delivered'");
//           //dd($yesterdayTotalSales);
//             $thisMonthTotalSales=DB::select("SELECT SUM(grand_total_value) as totmonthSales FROM tbl_order WHERE MONTH(order_date) = MONTH(CURRENT_DATE())and order_type='Delivered'");
//             $totalcollection=DB::select("SELECT SUM(grand_total_value) as totcollect FROM tbl_order where order_type='Ordered'");

//              $user_id=Auth::user()->id;
//              $user=DB::table('tbl_user_business_scope')
//                                 ->where('user_id', $user_id)
//                                 ->first();
// //dd($user);
//             //Maung
//             $totorder=DB::select("select  sum(tbl_order.grand_total_value)as totorder,tbl_user_business_scope.user_id,tbl_user_business_scope.user_id,tbl_user_business_scope.point_id,tbl_user_business_scope.territory_id,
//             users.id,users.display_name,users.designation,users.user_type_id,
//             tbl_order.*
//             from tbl_user_business_scope,users,tbl_order
//             where tbl_user_business_scope.user_id=users.id and tbl_user_business_scope.user_id=tbl_order.fo_id
//             and tbl_user_business_scope.territory_id=$user->territory_id 
//             and users.user_type_id=12 and tbl_order.order_type='Ordered'");

//             //dd($totorder);

//             $todayscollect=DB::select("select  sum(tbl_order.grand_total_value)as todaytotorder,tbl_user_business_scope.user_id,tbl_user_business_scope.user_id,tbl_user_business_scope.point_id,tbl_user_business_scope.territory_id,
//             users.id,users.display_name,users.designation,users.user_type_id,
//             tbl_order.*
//             from tbl_user_business_scope,users,tbl_order
//             where tbl_user_business_scope.user_id=users.id and tbl_user_business_scope.user_id=tbl_order.fo_id
//             and tbl_user_business_scope.territory_id=$user->territory_id 
//             and users.user_type_id=12 and tbl_order.order_type='Ordered' and DATE(order_date)=DATE(NOW())");

//             $yesterdaycollect=DB::select("select  sum(tbl_order.grand_total_value)as yesdaytotorder,tbl_user_business_scope.user_id,tbl_user_business_scope.user_id,tbl_user_business_scope.point_id,tbl_user_business_scope.territory_id,
//             users.id,users.display_name,users.designation,users.user_type_id,
//             tbl_order.*
//             from tbl_user_business_scope,users,tbl_order
//             where tbl_user_business_scope.user_id=users.id and tbl_user_business_scope.user_id=tbl_order.fo_id
//             and tbl_user_business_scope.territory_id=$user->territory_id 
//             and users.user_type_id=12 and tbl_order.order_type='Ordered' and DATE(order_date)=DATE(NOW() - INTERVAL 1 DAY)");

 
//            $monthlyAchieveTsm=DB::select("select sum(total_value) as monthtarget,total_value,fo_id,update_date
//                 from tbl_order
//                 where update_date between '$start_date' and '$end_date'
//                 and fo_id IN(select tbl_user_business_scope.user_id from tbl_user_business_scope, users
//                 where tbl_user_business_scope.user_id=users.id and tbl_user_business_scope.territory_id=27 and users.user_type_id=12)");
          
          
            // $allCompack = compact('selectedMenu','pageTitle','activeUsers','inactiveUsers','activeProducts','activeOffers','activeDivision','activePoint','activeRoute','','activeTerritory','activeUsersDist','yesterdayTotalSales','thisMonthTotalSales','totalcollection','user','totorder','todayscollect','yesterdaycollect','monthlyAchieveTsm');

            // $typesLayoutPath = 'sales/tsm/tsmMasterDashboard';

            $allCompack = compact('selectedMenu','pageTitle','user');

            $typesLayoutPath = 'sales/tsm/tsmMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==17) // IMS DEPARTMENT
        {
            $allCompack = compact('selectedMenu','pageTitle');

            $typesLayoutPath = 'sales/ims/imsMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==18) // Account Panel
        {
            
            //echo 'Accounts Panel'; exit;
            
            $allCompack = compact('selectedMenu','pageTitle', 'resDepotSummary', 'resStockSummary', 'resultOrderDelivery','resultOrderPending','resultOrderPreviousRe','resultOrderTotalRe');
            
            $typesLayoutPath = 'Depot.depotAccountsMasterDashboard';
        }
        elseif(Auth::user()->user_type_id==20) // EPP Panel
        {
            
            //echo 'EPP Panel'; exit;
            
            $allCompack = compact('selectedMenu','pageTitle');
            
            $typesLayoutPath = 'sales/epp/eppMasterDashboard';
        }


    	return view($typesLayoutPath, $allCompack);
    }

    public function default_profile()
    {
    	$selectedMenu   = 'Home';         // Required Variable
        $pageTitle      = 'Profile';     // Page Slug Title

        $resultProfile = DB::table('users')
                        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_user_business_scope.user_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')
                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                        ->where('users.email', Auth::user()->email)                    
                        ->first();

    	return view('sales/profile', compact('selectedMenu','pageTitle'));
    }


    public function ssg_fo_orders(Request $request)
    {
        $todate = date('Y-m-d');

        $startDate     = date('Y-m'.'-01');
        $endDate       = date('Y-m'.'-31');

        if($request->get('serialid')==1)
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

            $serial = '7';
            $compact = compact('serial','resultOrderList');
        }
        elseif($request->get('serialid')==2)
        {
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
            
            $resultRetailer = DB::table('tbl_retailer')                        
                        //->where('user', Auth::user()->id)
                        ->where('point_id', $pointID)
                        ->orderBy('retailer_id','DESC')
                        ->get();

            $serial = '8';

            $compact = compact('serial','resultRetailer');
        }
        elseif($request->get('serialid')==3)
        {
           
            $attendance = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id','tbl_user_details.global_company_id','tbl_retailer.name AS retailerName')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->leftJoin('tbl_retailer', 'ia.retailerid', '=', 'tbl_retailer.retailer_id')

                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)     
                        ->where('ia.type', 1)
                        ->whereBetween(DB::raw("(DATE_FORMAT(ia.date,'%Y-%m-%d'))"), array($startDate, $endDate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        //->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

            $serial = '9';

            $compact = compact('serial','attendance');
        }
        elseif($request->get('serialid')==4)
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                    ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id','tbl_retailer.name')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                    ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'ims_tbl_visit_order.retailerid')
                    ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                    ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                    ->where('ims_tbl_visit_order.status', 2)
                    ->whereBetween('ims_tbl_visit_order.date', array($todate, $todate))
                    ->orderBy('ims_tbl_visit_order.id','DESC')                    
                    ->get();

            $serial = '12';

            $compact = compact('serial','resultVisitList');
        }


        

        return view('sales/offer/allReplaceValue', $compact );
    }


    
// Start Dashboard for Distributor controller of Sharif


    public function ssg_distributor_orders(Request $request)
    {
        $todate = date('Y-m-d');

        if($request->get('serialid')==1)
        {
            $distributorOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '<>', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

            $serial = '1';
            $compact = compact('serial','distributorOrderList');
        }

        if($request->get('serialid')==2)
        {
            $distributorTodayOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->whereDate('order_date', '=', $todate)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

            $serial = '2';
            $compact = compact('serial','distributorTodayOrderList');
        }

        if($request->get('serialid')==3)
        {
            $distributorDeliveryPending = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

            $serial = '3';
            $compact = compact('serial','distributorDeliveryPending');
        }

        if($request->get('serialid')==4)
        {
            $distributorDelivery = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

            $serial = '4';
            $compact = compact('serial','distributorDelivery');
        }

        return view('sales/distributor/allDistributorValue', $compact );
    }

// End Dashboard for Distributor controller of Sharif


    public function ssg_management(Request $request)
    {
        $todate       = date('Y-m-d');
        $end_date     = date('Y-m-31');
        $start_date   = date('Y-m-01');

        $startDate     = date('Y-m'.'-01');
        $endDate       = date('Y-m'.'-31');

        $currentYear  = date('Y');

        if($request->get('serialid')==1)
        {

            //->whereBetween(DB::raw("(DATE_FORMAT(ia.date,'%Y-%m-%d'))"), array($startDate, $endDate))
            $topDistributor = DB::select("SELECT tbl_order.global_company_id,tbl_order.update_date,tbl_order.order_type, tbl_order.global_company_id, SUM(tbl_order.total_delivery_value) as total_value1, tbl_order.distributor_id, users.id, users.display_name FROM tbl_order INNER JOIN users ON tbl_order.distributor_id = users.id 
                WHERE tbl_order.order_type ='Delivered' 
                
                AND tbl_order.update_date BETWEEN '$start_date' AND '$end_date' 
                GROUP BY tbl_order.distributor_id
                ORDER BY MAX(tbl_order.total_delivery_value) DESC LIMIT 10");

            $serialNo = '1';

            $compact = compact('serialNo','topDistributor');
            
        }
        elseif($request->get('serialid')==2)
        {
            $yearlyTarget = DB::table('tbl_fo_target')
                        ->select(DB::raw("SUM(tbl_fo_target.total_value) as totalTarget"),'tbl_fo_target.employee_id','tbl_fo_target.global_company_id', 'tbl_fo_target.start_date','users.email', 'users.display_name', 'users.business_type_id','tbl_business_type.business_type_id','tbl_business_type.business_type')
                        
                        ->leftJoin('users', 'tbl_fo_target.employee_id', '=', 'users.email')
                        ->leftJoin('tbl_business_type', 'users.business_type_id', '=', 'tbl_business_type.business_type_id')

                        ->where('tbl_fo_target.global_company_id', Auth::user()->global_company_id)                        
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_fo_target.start_date,'%Y'))"), array($currentYear, $currentYear))                       
                        ->groupBy('users.business_type_id')
                        ->orderBy('users.business_type_id','ASC')                    
                        ->get();

            $serialNo = '2';

            $compact = compact('serialNo','yearlyTarget');
            
        }
        elseif($request->get('serialid')==3)
        {
            $topFo = DB::select("SELECT tbl_order.global_company_id,tbl_order.order_type, tbl_order.global_company_id, SUM(tbl_order.total_delivery_value) as total_value1, tbl_order.fo_id, users.id, users.display_name FROM tbl_order INNER JOIN users ON tbl_order.fo_id = users.id 
                WHERE tbl_order.order_type ='Delivered' 
                AND tbl_order.update_date BETWEEN '$start_date' AND '$end_date'
                GROUP BY tbl_order.distributor_id 
                ORDER BY MAX(tbl_order.total_delivery_value) DESC LIMIT 10");

            $serialNo = '3';

            $compact = compact('serialNo','topFo');
            
        }
        elseif($request->get('serialid')==4)
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select(DB::raw("SUM(tbl_order.total_delivery_value) as totalSales"),'tbl_order.*','users.id','users.business_type_id','tbl_business_type.business_type_id','tbl_business_type.business_type')

                            ->leftJoin('users', 'tbl_order.fo_id', '=', 'users.id')
                            ->leftJoin('tbl_business_type', 'users.business_type_id', '=', 'tbl_business_type.business_type_id')

                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                            ->groupBy('users.business_type_id')    
                            ->orderBy('users.business_type_id','ASC')
                            ->get();
            //dd($resultOrderList);

            $serialNo = '4';
            $compact  = compact('serialNo','resultOrderList');
            
        }
        elseif($request->get('serialid')==5)
        {
            $yesterday = date('Y-m-d',strtotime('-1 day'));
            //$todate   = date('Y-m'.'-31');

            $resultOrderList = DB::table('tbl_order')
                            ->select(DB::raw("SUM(tbl_order.total_value) as totalSales"),'tbl_order.*','users.id','users.business_type_id','tbl_business_type.business_type_id','tbl_business_type.business_type')

                            ->leftJoin('users', 'tbl_order.fo_id', '=', 'users.id')
                            ->leftJoin('tbl_business_type', 'users.business_type_id', '=', 'tbl_business_type.business_type_id')

                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($yesterday, $yesterday))
                            ->groupBy('users.business_type_id')    
                            ->orderBy('users.business_type_id','ASC')
                            ->get();

            $serialNo = '5';
            $compact  = compact('serialNo','resultOrderList');                      
        }
        elseif($request->get('serialid')==7)
        {
            $fromdate = date('Y-m'.'-01');
            $todate   = date('Y-m'.'-31');

            $resultOrderList = DB::table('tbl_order')
                            ->select(DB::raw("SUM(tbl_order.total_value) as totalSales"),'tbl_order.*','users.id','users.business_type_id','tbl_business_type.business_type_id','tbl_business_type.business_type')

                            ->leftJoin('users', 'tbl_order.fo_id', '=', 'users.id')
                            ->leftJoin('tbl_business_type', 'users.business_type_id', '=', 'tbl_business_type.business_type_id')

                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('users.business_type_id')    
                            ->orderBy('users.business_type_id','ASC')
                            ->get();

            $serialNo = '7';

            $compact = compact('serialNo','resultOrderList'); 
        }
        elseif($request->get('serialid')==8)
        {
            $yearlyTarget = DB::table('tbl_fo_target')
                        ->select(DB::raw("SUM(tbl_fo_target.total_value) as totalTarget"),'tbl_fo_target.employee_id','tbl_fo_target.global_company_id', 'tbl_fo_target.start_date','users.email', 'users.display_name', 'users.business_type_id','tbl_business_type.business_type_id','tbl_business_type.business_type')
                        
                        ->leftJoin('users', 'tbl_fo_target.employee_id', '=', 'users.email')
                        ->leftJoin('tbl_business_type', 'users.business_type_id', '=', 'tbl_business_type.business_type_id')

                        ->where('tbl_fo_target.global_company_id', Auth::user()->global_company_id)                        
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_fo_target.start_date,'%Y-%m-%d'))"), array($startDate, $endDate))                       
                        ->groupBy('users.business_type_id')
                        ->orderBy('users.business_type_id','ASC')                    
                        ->get();

            $serialNo = '8';

            $compact = compact('serialNo','yearlyTarget'); 
        }
        elseif($request->get('serialid')==9) // ATTENDANCE
        {
            $today = date('Y-m-d');

            $attendance = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id','tbl_user_details.global_company_id','tbl_retailer.name AS retailerName')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->leftJoin('tbl_retailer', 'ia.retailerid', '=', 'tbl_retailer.retailer_id')

                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        //->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

            $serialNo = '9';

            $compact = compact('serialNo','attendance'); 
        }

        return view('sales/mgt/allReplaceValue', $compact);        
    }

    public function ssg_master_logout()
    {
        Session::forget('userType'); // Removes a specific variable
    	Auth::logout(); // logout user
		return Redirect::to('/')->with('success', 'Your have successfully logged out!');
    }
}
