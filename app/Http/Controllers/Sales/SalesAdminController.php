<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class SalesAdminController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 30/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    /* 
       =====================================================================
       ============================ Order Report============================
       =====================================================================
    */    

    public function ssg_depot_report()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= '';                            // Required Variable for submenu
        $pageTitle      = 'Depot Operation Report';     // Page Slug Title

        $resultDepot    = DB::table('tbl_point')->select('point_name')
                        ->where('is_depot', 1)
                        ->where('point_status', 0)
                        ->orderBy('point_name')
                        ->get();

        return view('sales/report/salesAdmin/depotReportView', compact('selectedMenu','selectedSubMenu','pageTitle','resultDepot'));
    }

    
	public function ssg_depot_report_list(Request $request)
    {
        $fromdate   = ''; //date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($todate!='')
        {
            $resultDepot    = DB::table('tbl_point')->select('point_name','point_id')
                            ->where('is_depot', 1)
                            ->where('point_status', 0)
                            //->where('point_id', 267)
                            ->orderBy('point_name')
                            ->get();
        }        
        
        return view('sales/report/salesAdmin/depotReportViewList', compact('resultDepot','todate'));
    }

	
	public function retailer_sales_history(Request $req)
	{
		
		$selectedMenu   = 'Retailer Sales History';         	  // Required Variable
		$pageTitle      = 'Retailer Sales History Details';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$YearList = array('2020'=>'2020','2019'=>'2019','2018'=>'2018');

		dd($YearList);
		
		$MonthList = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July',
		'08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
		
		$pointList = DB::select("SELECT * FROM tbl_point WHERE is_depot = 1");
		
		
		$whereCond = '';
		$sel_year_id = '';
		if ($req->get('year_id') != '') 
		{
			$sel_year_id = $req->get('year_id');
			$whereCond .= " AND date_format(tbl_order.order_date,'%Y') = '".$req->get('year_id')."' ";
		}
		
		$sel_month_id = '';
		if ($req->get('month_id') != '') 
		{
			$sel_month_id = $req->get('month_id');
			$whereCond .= " AND date_format(tbl_order.order_date,'%m') = '".$req->get('month_id')."' ";
		}
		
		$sel_point_id = '';
		if ($req->get('point_id') != '') 
		{
			$sel_point_id = $req->get('point_id');
			$whereCond .= " AND tbl_order.point_id = '".$req->get('point_id')."' ";
		}
		
		$sel_route_id = '';
		if ($req->get('route_id') != '') 
		{
			$sel_route_id = $req->get('route_id');
			$whereCond .= " AND tbl_order.route_id = '".$req->get('route_id')."' ";
		}
		
		$sel_ratailer_id = '';
		if ($req->get('ratailer_id') != '') 
		{
			$sel_ratailer_id = $req->get('ratailer_id');
			$whereCond .= " AND tbl_order.retailer_id = '".$req->get('ratailer_id')."' ";
		}
			
		//echo $whereCond; exit;
			
		/*	
		$retSalesHist = DB::select("
						SELECT tbl_point.point_id, tbl_point.point_name, tbl_route.route_id, tbl_route.rname as route_name, tbl_retailer.retailer_id, tbl_retailer.name as retailer_name,
						users.display_name as fo_name, tbl_order.grand_total_value as total_sales, tbl_order.update_date as delivery_date
						FROM tbl_order JOIN tbl_point ON tbl_point.point_id = tbl_order.point_id
						JOIN tbl_route ON tbl_order.route_id = tbl_route.route_id
						JOIN tbl_retailer ON tbl_retailer.retailer_id = tbl_order.retailer_id
						JOIN users ON users.id = tbl_order.fo_id
						WHERE order_type = 'Delivered' 
						$whereCond 
						ORDER BY tbl_retailer.name 
						"); 
		*/				
		
		$retSalesHist = array();				
		if($whereCond != '')
		{
			$retSalesHist = DB::select("
						SELECT tbl_point.point_id, tbl_point.point_name, tbl_route.route_id, tbl_route.rname as route_name, 
						tbl_retailer.retailer_id, tbl_retailer.name as retailer_name, 
						users.display_name as fo_name, SUM(tbl_order.grand_total_value) as total_sales, tbl_order.update_date as delivery_date
						FROM tbl_order JOIN tbl_point ON tbl_point.point_id = tbl_order.point_id
						JOIN tbl_route ON tbl_order.route_id = tbl_route.route_id
						JOIN tbl_retailer ON tbl_retailer.retailer_id = tbl_order.retailer_id
						JOIN users ON users.id = tbl_order.fo_id
						WHERE order_type = 'Delivered' 
						AND tbl_retailer.retailer_id NOT IN (SELECT retailer_id FROM tbl_monthly_retailer_commission 
															 WHERE for_year = '".$sel_year_id."' AND for_month = '".$sel_month_id."')
						$whereCond
						GROUP BY tbl_order.retailer_id
						"); 
		}
		
						
						
		     
		return view('sales/report/salesAdmin/retailer_sales_history')
		  ->with('pointList',$pointList)
		  ->with('YearList',$YearList)
		  ->with('MonthList',$MonthList)
		  ->with('sel_year_id',$sel_year_id)
		  ->with('sel_month_id',$sel_month_id)
		  ->with('sel_point_id',$sel_point_id)
		  ->with('sel_route_id',$sel_route_id)
		  ->with('sel_ratailer_id',$sel_ratailer_id)
		  ->with('retSalesHist',$retSalesHist)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}
	
	
	public function get_route_list(Request $request)
    {
     	$point_ID = $request->input('id');

		
        $RouteList = DB::table('tbl_route')
						->where('point_id',$point_ID)
						->orderby('rname','asc')
						->get(); 
	
        return view('sales.report.salesAdmin.getRouteList' , compact('RouteList'));
    	
    }
	
	public function get_retaier_list(Request $request)
    {
     	$route_ID = $request->get('id');

		
        $RetList = DB::table('tbl_retailer')
						->where('rid',$route_ID)
						->orderby('name','asc')
						->get(); 
			 

        return view('sales.report.salesAdmin.getRetailerList' , compact('RetList'));
    	
    }
	
	
	public function sales_commission_process(Request $req) 
	{
		
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			//dd($req->all());
		
			$year_id = $req->input('inp_year_id');
			$month_id = $req->input('inp_month_id');
			
			$inpPointId = $req->input('inp_point_id');
			$inpRouteId = $req->input('inp_route_id');
			
			$inpRetCommCheck = $req->input('retCommCheck');
			$RetailerTotalSales = $req->input('retailer_total_sales');
			$salesPercen = $req->input('sales_com_perc');
			$salesComAmount = $req->input('sales_com_amount');
			$salesComNote = $req->input('commission_note');
			
			
			$commission_paid_by=Auth::user()->id;
			$paid_date=date('Y-m-d H:i:s');
			$is_commission_paid = 'YES';
			
			$file_info = array();
			$FileObj = $req->file('comm_approval_file'); 
			 
			/* 
			$file_info['file_path'] = $FileObj->getRealPath();
			$file_info['file_name'] = $FileObj->getClientOriginalName();
			$file_info['file_ext'] = $FileObj->getClientOriginalExtension();
			$file_info['file_size'] = $FileObj->getSize();
			$file_info['file_mime_type'] = $FileObj->getMimeType();
			$file_info['file_store_path'] = public_path() . '/' . 'sales_commission_approval' . '/';
			*/
			 
			//Commission Master
			$monthly_comm_data['commission_title'] = $req->input('commission_title');
			$monthly_comm_data['commission_desc'] =  $req->input('commission_desc');
			$monthly_comm_data['attached_file_name'] = $FileObj->getClientOriginalName();
			$monthly_comm_data['attached_file_ext'] =  $FileObj->getClientOriginalExtension();
			$monthly_comm_data['attached_file_size'] =  $FileObj->getSize();
			$monthly_comm_data['attached_file_mime_type'] =  $FileObj->getMimeType();
			$monthly_comm_data['attached_file_path'] =  public_path() . '/' . 'sales_commission_approval' . '/';
			$monthly_comm_data['commission_datetime'] =  $paid_date;
			$monthly_comm_data['commission_is_paid'] =  'YES';
			$monthly_comm_data['commission_paid_by'] =  $commission_paid_by;
			$monthly_comm_data['commission_paid_date'] =  $paid_date;
			
			$commission_id = DB::table('tbl_monthly_commission_master')->insertGetId($monthly_comm_data);
			 
			//Move Uploaded File
			$FileObj->move($monthly_comm_data['attached_file_path'],$FileObj->getClientOriginalName());
			  
			
			// Commission Details
			foreach($req->input('inp_retailer_id') as $rowRetId)
			{
				
				if(isset($inpRetCommCheck[$rowRetId]) && $inpRetCommCheck[$rowRetId] == 'YES')
				{
					
					$RetChkVal=$inpRetCommCheck[$rowRetId];
					
					$RetailerTotalSalesVal=$RetailerTotalSales[$rowRetId];
					
					$salesPercenVal=$salesPercen[$rowRetId];
					$salesComAmountVal=$salesComAmount[$rowRetId];
					$salesComNoteVal=$salesComNote[$rowRetId];
				
					$monthly_comm_detail['commission_id'] = $commission_id;
					$monthly_comm_detail['point_id'] = $inpPointId[$rowRetId];
					$monthly_comm_detail['route_id'] =  $inpRouteId[$rowRetId];
					$monthly_comm_detail['retailer_id'] =  $rowRetId;
					$monthly_comm_detail['for_year'] =  $year_id;
					$monthly_comm_detail['for_month'] =  $month_id;
					$monthly_comm_detail['total_sales'] =  $RetailerTotalSalesVal;
					$monthly_comm_detail['commission_percentage'] =  $salesPercenVal;
					$monthly_comm_detail['commission_amount'] =  $salesComAmountVal;
					$monthly_comm_detail['commission_note'] =  $salesComNoteVal;
					$monthly_comm_detail['commission_paid_by'] =  $commission_paid_by;
					$monthly_comm_detail['commission_paid_date'] =  $paid_date;
					$monthly_comm_detail['is_commission_paid'] =  $is_commission_paid;
					
					//echo '<pre/>'; print_r($monthly_comm_detail); exit;
					
					$monthly_commission_id = DB::table('tbl_monthly_retailer_commission')->insertGetId($monthly_comm_detail);
					
					if($monthly_commission_id>0)
					{
						$retailer_info = array();
						$retailer_info['point_id'] = $monthly_comm_detail['point_id'];
						$retailer_info['retailer_id'] = $monthly_comm_detail['retailer_id'];
						$retailer_info['monthly_commission_id'] = $monthly_commission_id;
						$retailer_info['monthly_commission_perc'] = $monthly_comm_detail['commission_percentage'];
						$retailer_info['monthly_commission_value'] = $monthly_comm_detail['commission_amount'];
						$retailer_info['trans_type'] = 'monthly_commission'; 
						$retailer_info['accounts_type'] = 'expense';
						
						$this->reatiler_credit_ledger($retailer_info);
					}
					
				}
			
			}
			
		}
		
		return Redirect::to('/PartySalesHistory')->with('success', 'Successfully Commission Paid.');
	}
	
	
	
	private function reatiler_credit_ledger($retailer_info = array())
    {
        if(is_array($retailer_info))
        {
            
            $credit_ledger_Data = array();
            $credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
            $credit_ledger_Data['point_id'] = $retailer_info['point_id'];
            $credit_ledger_Data['monthly_commission_id'] = $retailer_info['monthly_commission_id'];
            $credit_ledger_Data['monthly_commission_perc'] = $retailer_info['monthly_commission_perc'];
            $credit_ledger_Data['monthly_commission_value'] = $retailer_info['monthly_commission_value'];
            $credit_ledger_Data['trans_type'] = $retailer_info['trans_type'];
            $credit_ledger_Data['accounts_type'] = $retailer_info['accounts_type'];
            $credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s');
			
			$credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
			$credit_ledger_Data['entry_by'] = Auth::user()->id;
			
			$credit_ledger_Data['collection_id'] = 0;
			$credit_ledger_Data['retailer_invoice_no'] = 0;
			$credit_ledger_Data['retailer_invoice_sales'] = 0;
            
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
	 	
			$credit_ledger_Data['retailer_balance'] = $credit_ledger_Data['retailer_opening_balance'] - $credit_ledger_Data['monthly_commission_value'];
	
			DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
            
        }   
    }
	
	
	
	
	
	
	
	
	


    public function ssg_points_list(Request $request)
    {
        $serial = 3;
        $resultPoints = DB::table('tbl_point')->where('point_division',$request->get('divisions'))->get();
        return view('sales.report.distributor.allDropDown', compact('resultPoints','serial'));
    }

    public function ssg_fos_list(Request $request)
    {
        $serial = 4;

        $resultFos = DB::table('tbl_user_business_scope')
                    ->where('point_division',$request->get('division'))->get();

        return view('sales.report.report.distributor.allDropDown', compact('resultFos','serial'));
    }



    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Order';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';         // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        return view('sales.report.fo.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }



    /* 
       =====================================================================
       ============================ Delivery  ==============================
       =====================================================================
    */    

    public function ssg_report_fo_delivery()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        return view('sales.report.fo.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute'));
    }

    public function ssg_report_fo_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_report_fo_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';            // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        return view('sales.report.fo.invoiceDetailsDelivery', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }



    /* 
       =====================================================================
       ============================ Visit  =================================
       =====================================================================
    */    

    public function ssg_report_fo_visit()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Visit';                    // Required Variable for submenu
        $pageTitle      = 'Visit Report';            // Page Slug Title
        

        return view('sales.report.fo.visitReport', compact('selectedMenu','selectedSubMenu','pageTitle'));
    }

    public function ssg_report_fo_visit_list(Request $request)
    {
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('orderType')=='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();  
        }
        if($fromdate!='' && $todate!='' && $request->get('orderType')!='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->where('ims_tbl_visit_order.status', $request->get('orderType'))
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();   
        }       
        

        return view('sales.report.fo.visitReportList', compact('resultVisitList'));
    }


    /* 
       =====================================================================
       ========================= Order Vs Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_fo_order_vs_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'Oeder Vs Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Oeder Vs Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->orWhere('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        return view('sales.report.fo.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute'));
    }

    public function ssg_report_fo_order_vs_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')
                        ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.orderVsDeliveryReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Category Wise Order  ======================
       =====================================================================
    */    

    public function ssg_report_fo_category_wise_order()
    {
        $selectedMenu   = 'Report';                                  // Required Variable for menu
        $selectedSubMenu= 'Category Wise Order';                    // Required Variable for submenu
        $pageTitle      = 'Category Wise Order Report';            // Page Slug Title

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory'));
    }

    public function ssg_report_fo_category_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('category')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->where('tbl_order_details.cat_id', $request->get('category'))
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
            
        }
        
        return view('sales.report.fo.categoryWiseOrderReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Product Wise Order  =======================
       =====================================================================
    */    

    public function ssg_report_fo_product_wise()
    {
        $selectedMenu   = 'Report';                             // Required Variable for menu
        $selectedSubMenu= 'Product Wise';                      // Required Variable for submenu
        $pageTitle      = 'Product Wise Order Report';        // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', '!=', 'Ordered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.productWiseReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    public function ssg_report_fo_category_wise_product_list(Request $request)
    {

        $category   = $request->get('category');
        $serial     = 1;

        $resultProduct = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product.name AS pname','tbl_product.id AS pid')

                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                      
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order_details.cat_id', $category)
                        ->groupBy('tbl_order_details.product_id')                    
                        ->orderBy('tbl_order_details.product_id','DESC')                    
                        ->get();

        return view('sales.report.distributor.allDropDown', compact('resultProduct','serial'));

    }

    public function ssg_report_fo_product_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = Auth::user()->id;
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        
        return view('sales.report.fo.productWiseReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= SKU Wise Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_sku_wise_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'SKU Wise Delivery';                    // Required Variable for submenu
        $pageTitle      = 'SKU Wise Delivery Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    public function ssg_report_sku_wise_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }
	
	// Visit Frequency Report

	public function ssg_visit_frequency_report()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= '';                            // Required Variable for submenu
        $pageTitle      = 'Visit Frequency Report';     // Page Slug Title


        $pointList    = DB::table('tbl_point')->select('point_name','point_id')
                        ->where('is_depot', 1)
                        ->where('point_status', 0)
                        ->orderBy('point_name')
                        ->get();

        session()->put('leftMenuActive', 'Yes');
		
		$MonthList = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July',
		'08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');

        return view('sales/report/salesAdmin/visitFrequencyReportView', compact('selectedMenu','selectedSubMenu','pageTitle','pointList','MonthList'));
    }

    
	public function ssg_visit_frequency_report_list(Request $request)
    {
        $point = $request->get('point');
        $year  = $request->get('year');
        $month = $request->get('month');
        $route = $request->get('route');
        
        $allDepot    = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_retailer.retailer_id','tbl_retailer.name','tbl_retailer.rid')
        			    ->join('tbl_retailer','tbl_point.point_id','=','tbl_retailer.point_id')
                        ->where('tbl_point.point_id', $point)
                        ->where('tbl_retailer.rid', $route)
                        ->orderBy('tbl_point.point_name')
                        ->get();

        return view('sales/report/salesAdmin/visitFrequencyReportViewList', compact('allDepot','year','month'));
    }
	
	
	
	
}
