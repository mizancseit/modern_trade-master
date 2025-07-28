<?php

namespace App\Http\Controllers\Depot; 

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
/* Load Model Reference Begin */
/* Load Model Reference End */
use Hash;
use DB;
use Auth;
use Session;
use Excel;

class DepotReport extends Controller
{
	
	private  $depot_in_charge;
	private  $user_type_id;
    
	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
	   
    }
	
	
	public function depot_ledger_details(Request $req)
	{
		$selectedMenu   = 'Depot Ledger';         		// Required Variable
		$pageTitle      = 'Depot Ledger';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$WhereCond = '';	
		$fromDate = '';
		if($req->get('fromDate')!='')
		{
				$fromDate = explode('-',$req->get('fromDate'));
				$fromDate = $fromDate[2] . '-' . $fromDate[1] . '-' . $fromDate[0]; 
			
			$WhereCond .= " AND date_format(pldg.ledger_date_time,'%Y-%m-%d') >= '".$fromDate."' ";	
		}	
		
		$todate = '';
		if($req->get('todate')!='')
		{
				$todate = explode('-',$req->get('todate'));
				$todate = $todate[2] . '-' . $todate[1] . '-' . $todate[0]; 
				
			$WhereCond .= " AND date_format(pldg.ledger_date_time,'%Y-%m-%d') <= '".$todate."' ";		
		}	
			
		$SqlQuery = "SELECT pldg.*, u.display_name, u.sap_code 
		FROM tbl_party_ledger pldg JOIN users u ON u.id = pldg.party_id
		WHERE pldg.party_id = '".$this->depot_in_charge."' $WhereCond order by 1 asc";	
			
			
		$DepotLedgerData = DB::select("$SqlQuery");
			
		
		return view('Depot.report.depot_ledger_summary')
		  ->with('DepotLedgerData',$DepotLedgerData)
		  ->with('fromdate',$fromDate)
		  ->with('todate',$todate)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
		
			
	}
	
							/* All Retailer ledger history */
							
	public function retailer_wise_credit_summary(Request $req)
	{
		$selectedMenu   = 'Retailer Credit Ledger';         		// Required Variable
		$pageTitle      = 'All Retailer Credit Ledger';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$retailerData = array();
		$retOpenTot = array(); 
		$depoTotSales = array(); 
		$depoTotRetSales = array();
		$depoTotCollection = array();
		
		//$depotList = DB::table('tbl_depot_setup')->orderBy('depot_id','asc')->get();
		
		if($this->user_type_id == 1)
		{
			$routeList = DB::table('tbl_route')->orderBy('tbl_route','asc')->get();
		
		} elseif($this->user_type_id == 5) {
			
			$routeList = DB::select("SELECT * FROM tbl_route WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
		}
		
		
		if ($req->get('route_id') && $req->get('route_id')!='all') 
		{
			
			$retailerData = DB::select("
									SELECT ret.point_id,ret.retailer_id, ret.name, ret.opening_balance
										FROM  tbl_retailer ret
										WHERE  ret.point_id in (SELECT point_id FROM tbl_user_business_scope 
																WHERE user_id = '".Auth::user()->id."')
											AND ret.rid = '".$req->get('route_id')."'					
								");
								
			
			$retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer 
								WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
													WHERE user_id = '".Auth::user()->id."')	
								AND rid = '".$req->get('route_id')."'");	

			//echo 	$retOpenTot[0]->totRetOpenBal; exit;				
							
			$depoTotSales = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order 
								WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
								WHERE user_id = '".Auth::user()->id."')  
								AND order_type = 'Delivered' and route_id = '".$req->get('route_id')."'");
			
			$depoTotRetSales = DB::select("
											SELECT SUM(rled.retailer_sales_return) totSalesReturn
												FROM  retailer_credit_ledger rled JOIN tbl_retailer ret 
												ON rled.retailer_id = ret.retailer_id
												WHERE  rled.point_id in (SELECT point_id FROM tbl_user_business_scope 
																		WHERE user_id = '".Auth::user()->id."')
												and ret.rid = '".$req->get('route_id')."'																		
												
											");  					
								
			//echo 	$depoTotSales[0]->tot_sales; exit;							
								
			$depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection
								WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
								WHERE user_id = '".Auth::user()->id."')
								and route_id = '".$req->get('route_id')."'");
			
			//echo 	$depoTotCollection[0]->tot_collection; exit;	
													
			/*
			$retailerData = DB::select("
							SELECT ret.retailer_id, ret.name, ret.opening_balance, SUM(rled.retailer_invoice_sales) totSales, SUM(rled.retailer_collection) totCollection 
							FROM tbl_retailer ret LEFT JOIN retailer_credit_ledger rled ON ret.retailer_id = rled.retailer_id
							AND ret.point_id = rled.point_id
							WHERE ret.rid = '".$req->get('route_id')."' 
							AND ret.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							GROUP BY ret.retailer_id
						"); 
			*/
			
			
		} elseif ($req->get('route_id') && $req->get('route_id')=='all')  {
			
			$retailerData = DB::select("SELECT ret.point_id,ret.retailer_id, ret.name, ret.opening_balance, point_id
										FROM  tbl_retailer ret
										WHERE  ret.point_id in (SELECT point_id FROM tbl_user_business_scope 
										WHERE user_id = '".Auth::user()->id."')
								"); 
								
			
			$retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer WHERE point_id 
								in (SELECT point_id FROM tbl_user_business_scope 
								WHERE user_id = '".Auth::user()->id."')");		
							
			$depoTotSales = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order 
								WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
								WHERE user_id = '".Auth::user()->id."')  
								AND order_type = 'Delivered'");
								
			$depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection
								WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
								WHERE user_id = '".Auth::user()->id."')");
								
			$depoTotRetSales = DB::select("
											SELECT SUM(rled.retailer_sales_return) totSalesReturn
												FROM  retailer_credit_ledger rled 
												WHERE  rled.point_id in (SELECT point_id FROM tbl_user_business_scope 
																		WHERE user_id = '".Auth::user()->id."') 
												
											");  							
															
						
						
						/*  SELECT ret.retailer_id, ret.name, ret.opening_balance, SUM(rled.retailer_invoice_sales) totSales, SUM(rled.retailer_collection) totCollection 
							FROM tbl_retailer ret JOIN retailer_credit_ledger rled ON ret.retailer_id = rled.retailer_id
							AND ret.point_id = rled.point_id 
							WHERE ret.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."') 
							AND trans_type = 'sales'
							GROUP BY ret.retailer_id*/
		}
		 
		//echo '<pre/>'; print_r($depotList); exit;
	

		return view('Depot.report.retailer_wise_credit_summary')
		  ->with('route_id',$req->get('route_id'))
		  ->with('routeList',$routeList)
		  ->with('retailerData',$retailerData)
		  ->with('retOpenTot',$retOpenTot)
		  ->with('depoTotSales',$depoTotSales)
		  ->with('depoTotRetSales',$depoTotRetSales)
		  ->with('depoTotCollection',$depoTotCollection)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}
	
	
	public function download_credit_summary(Request $req)
	{
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$retCreditSummaryData = array();
		
		//$depotList = DB::table('tbl_depot_setup')->orderBy('depot_id','asc')->get();
		
		if($this->user_type_id == 1)
		{
			$routeList = DB::table('tbl_route')->orderBy('tbl_route','asc')->get();
		
		} elseif($this->user_type_id == 5) {
			
			$routeList = DB::select("SELECT * FROM tbl_route WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
		}
		
		
		if ($req->get('route_id') && $req->get('route_id')!='all') 
		{
			
			
			$retCreditSummaryData = DB::select("
							SELECT ret.retailer_id, ret.name, ret.opening_balance, SUM(rled.retailer_invoice_sales) totSales, SUM(rled.retailer_collection) totCollection 
							FROM tbl_retailer ret LEFT JOIN retailer_credit_ledger rled ON ret.retailer_id = rled.retailer_id
							WHERE ret.rid = '".$req->get('route_id')."' 
							AND ret.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							GROUP BY ret.retailer_id
						"); 
			
		} elseif ($req->get('route_id') && $req->get('route_id')=='all')  {
			
			$retCreditSummaryData = DB::select("
							SELECT ret.retailer_id, ret.name, ret.opening_balance, SUM(rled.retailer_invoice_sales) totSales, 
							SUM(rled.retailer_collection) totCollection 
									FROM tbl_retailer ret LEFT JOIN retailer_credit_ledger rled ON ret.retailer_id = rled.retailer_id
									WHERE ret.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."') 
									GROUP BY ret.retailer_id
						"); 
		}
		 
		 
		
		$data = array();
		foreach ($retCreditSummaryData as $items) {
			$items->balance = ( $items->opening_balance + $items->totSales) - $items->totCollection;
			unset($items->retailer_id);			
			$data[] = (array)$items;  
		} 
		
		Excel::create('Download_Credit', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');
			
	}
	
	
	public function depot_expense_summary(Request $req)
	{
		$selectedMenu   = 'Depot Expense Summary';         		// Required Variable
		$pageTitle      = 'Depot Expense History';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$expenseList = DB::select("SELECT * FROM depot_accounts_head WHERE accounts_head_type = 'expense'");
		
		if ($req->get('accounts_head_id')) 
		{
			
			if ($req->get('fromExpenseDate') != '' && $req->get('toExpenseDate') != '' && $req->get('accounts_head_id') && $req->get('accounts_head_id')!='all') 
			{
				
				$fromExpenseDate = explode('-',$req->get('fromExpenseDate'));
				$toExpenseDate = explode('-',$req->get('toExpenseDate'));
				
				$fromDate = $fromExpenseDate[2] . '-' . $fromExpenseDate[1] . '-' . $fromExpenseDate[0]; 
				$toDate = $toExpenseDate[2] . '-' . $toExpenseDate[1] . '-' . $toExpenseDate[0]; 
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							AND ah.accounts_head_id = '".$req->get('accounts_head_id')."'
							AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' 
							AND '".$toDate."'";
			
			} elseif ($req->get('fromExpenseDate') != '' && $req->get('toExpenseDate') != '' && $req->get('accounts_head_id') && $req->get('accounts_head_id')=='all') {
				
				$fromExpenseDate = explode('-',$req->get('fromExpenseDate'));
				$toExpenseDate = explode('-',$req->get('toExpenseDate'));
				
				$fromDate = $fromExpenseDate[2] . '-' . $fromExpenseDate[1] . '-' . $fromExpenseDate[0]; 
				$toDate = $toExpenseDate[2] . '-' . $toExpenseDate[1] . '-' . $toExpenseDate[0]; 
				
				$ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' AND '".$toDate."'
								AND ah.accounts_head_type = 'expense'
								GROUP BY dc.perticular_head_id";
				
			} elseif($req->get('accounts_head_id')!='all') {
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."') 
							AND ah.accounts_head_id = '".$req->get('accounts_head_id')."'";
				
			
			} elseif($req->get('accounts_head_id')=='all') {
				
				 $ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								AND ah.accounts_head_type = 'expense'
								GROUP BY dc.perticular_head_id";
				
			}
			
			//echo $ExpenseSQL; exit;
			
			$depotExpenseData = DB::select("$ExpenseSQL"); 
			
		} else {
			
			$depotExpenseData = array();
		}
		 
		//echo '<pre/>'; print_r($depotList); exit;
		

		return view('Depot.report.depot_expense_summary')
		  ->with('fromExpenseDate', $req->get('fromExpenseDate'))
		  ->with('toExpenseDate', $req->get('toExpenseDate'))
		  ->with('accounts_head_id',$req->get('accounts_head_id'))
		  ->with('expenseList', $expenseList)
		  ->with('depotExpenseData', $depotExpenseData)
		  ->with('selectedMenu', $selectedMenu)
		  ->with('pageTitle', $pageTitle);
			
	}
	
	
	public function download_expense_summary(Request $req)
	{
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		//$expenseList = DB::select("SELECT * FROM depot_accounts_head WHERE accounts_head_type = 'expense'");
		
		if ($req->get('accounts_head_id')) 
		{
			
			if ($req->get('fromExpenseDate') != '' && $req->get('toExpenseDate') != '' && $req->get('accounts_head_id') && $req->get('accounts_head_id')!='all') 
			{
				
				$fromExpenseDate = explode('-',$req->get('fromExpenseDate'));
				$toExpenseDate = explode('-',$req->get('toExpenseDate'));
				
				$fromDate = $fromExpenseDate[2] . '-' . $fromExpenseDate[1] . '-' . $fromExpenseDate[0]; 
				$toDate = $toExpenseDate[2] . '-' . $toExpenseDate[1] . '-' . $toExpenseDate[0]; 
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							AND ah.accounts_head_id = '".$req->get('accounts_head_id')."'
							AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' 
							AND '".$toDate."'";
			
			} elseif ($req->get('fromExpenseDate') != '' && $req->get('toExpenseDate') != '' && $req->get('accounts_head_id') && $req->get('accounts_head_id')=='all') {
				
				$fromExpenseDate = explode('-',$req->get('fromExpenseDate'));
				$toExpenseDate = explode('-',$req->get('toExpenseDate'));
				
				$fromDate = $fromExpenseDate[2] . '-' . $fromExpenseDate[1] . '-' . $fromExpenseDate[0]; 
				$toDate = $toExpenseDate[2] . '-' . $toExpenseDate[1] . '-' . $toExpenseDate[0]; 
				
				$ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' AND '".$toDate."'
								AND ah.accounts_head_type = 'expense'
								GROUP BY dc.perticular_head_id";
				
			} elseif($req->get('accounts_head_id')!='all') {
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."') 
							AND ah.accounts_head_id = '".$req->get('accounts_head_id')."'";
				
			
			} elseif($req->get('accounts_head_id')=='all') {
				
				 $ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								AND ah.accounts_head_type = 'expense'
								GROUP BY dc.perticular_head_id";
				
			}
			
			//echo $ExpenseSQL; exit;
			
			$depotExpenseData = DB::select("$ExpenseSQL"); 
			
		} else {
			
			$depotExpenseData = array();
		}
		 
		//echo '<pre/>'; print_r($depotList); exit;
		
		$data = array();
		foreach ($depotExpenseData as $items) {
			//$items->balance = ( $items->opening_balance + $items->totSales) - $items->totCollection;
			//unset($items->retailer_id);			
			$data[] = (array)$items;  
		} 
		
		Excel::create('Download_Expense', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');
			
	}
	
	
	public function depot_cashbook_summary(Request $req)
	{
		$selectedMenu   = 'Depot Cashbook Summary';       // Required Variable
		$pageTitle      = 'Depot Cashbook Summary';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$depotCashbookData = array();
		
		//$expenseList = DB::select("SELECT * FROM depot_accounts_head WHERE accounts_head_type = 'expense'");
		
			
			if ($req->get('fromCashBookDate') != '' && $req->get('toCashBookDate') != '') 
			{
				
				$fromCashBookDate = explode('-',$req->get('fromCashBookDate'));
				$toCashBookDate = explode('-',$req->get('toCashBookDate'));
				
				$fromDate = $fromCashBookDate[2] . '-' . $fromCashBookDate[1] . '-' . $fromCashBookDate[0]; 
				$toDate = $toCashBookDate[2] . '-' . $toCashBookDate[1] . '-' . $toCashBookDate[0]; 
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.*
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' 
							AND '".$toDate."' ORDER BY cash_book_id ASC";
							
				$depotCashbookData = DB::select("$ExpenseSQL"); 			
			
			} else {
				
				
				$ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.* 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								ORDER BY cash_book_id ASC";
								
				$depotCashbookData = DB::select("$ExpenseSQL"); 				
				
			} 
			
			
			
	
		return view('Depot.report.depot_cashbook_summary')
		  ->with('fromCashBookDate', $req->get('fromCashBookDate'))
		  ->with('toCashBookDate', $req->get('toCashBookDate'))
		  ->with('depotCashbookData', $depotCashbookData)
		  ->with('selectedMenu', $selectedMenu)
		  ->with('pageTitle', $pageTitle);
			
	}
	
	
	public function download_cashbook_summary(Request $req)
	{
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$depotCashbookData = array();
		
		//$expenseList = DB::select("SELECT * FROM depot_accounts_head WHERE accounts_head_type = 'expense'");
		
			
			if ($req->get('fromCashBookDate') != '' && $req->get('toCashBookDate') != '') 
			{
				
				$fromCashBookDate = explode('-',$req->get('fromCashBookDate'));
				$toCashBookDate = explode('-',$req->get('toCashBookDate'));
				
				$fromDate = $fromCashBookDate[2] . '-' . $fromCashBookDate[1] . '-' . $fromCashBookDate[0]; 
				$toDate = $toCashBookDate[2] . '-' . $toCashBookDate[1] . '-' . $toCashBookDate[0]; 
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.*
							FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' 
							AND '".$toDate."' ORDER BY cash_book_id ASC";
							
				$depotCashbookData = DB::select("$ExpenseSQL"); 			
			
			} else {
				
				
				$ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.* 
								FROM depot_accounts_head ah JOIN depot_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								ORDER BY cash_book_id ASC";
								
				$depotCashbookData = DB::select("$ExpenseSQL"); 				
				
			} 
			
			
		$data = array();
		foreach ($depotCashbookData as $items) {
			$data[] = (array)$items;  
		} 
		
		Excel::create('Download_Cashbook', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');	
	
		
			
	}

	
	public function depot_fo_wise_sales(Request $req)
	{
		$selectedMenu   = 'FO Sales';      // Required Variable
		$pageTitle      = 'FO Sales';       // Required Variable
			
		
		 
		$this->depot_in_charge 	= Auth::user()->id;
		$this->user_type_id 	= Auth::user()->user_type_id;
		
		
		
		
		if($this->user_type_id == 5) {
			
			
			
			$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
									
			$routeList = DB::table('tbl_route')->where('point_id',$depotList[0]->point_id)->orderBy('rname','asc')->get();
			
			$retailerList = DB::table('tbl_retailer')->where('point_id',$depotList[0]->point_id)->orderBy('name','asc')->get();
			
			$foList = DB::select("SELECT u.id, u.display_name FROM users u JOIN tbl_user_type ut 
								ON u.user_type_id = ut.user_type_id
								JOIN tbl_user_details ud ON ud.user_id = u.id
								JOIN tbl_user_business_scope bs ON bs.user_id = u.id
								WHERE ut.user_type_id = '12' AND bs.point_id = '".$depotList[0]->point_id."'");

			
			
			if ($req->get('fromSalesDate') != '' && $req->get('toSalesDate') != '') 
			{
				
				
				
				$fromSalesDate = explode('-',$req->get('fromSalesDate'));
				$toSalesDate = explode('-',$req->get('toSalesDate'));
				
			
				
				$fromDate = $fromSalesDate[2] . '-' . $fromSalesDate[1] . '-' . $fromSalesDate[0]; 
				$toDate = $toSalesDate[2] . '-' . $toSalesDate[1] . '-' . $toSalesDate[0];
				
				
				
				$DynSQL =	"SELECT u.display_name, u.email, r.rname, ret.name, o.* 
							FROM tbl_order o JOIN tbl_order_details od ON o.order_id =  od.order_id
							JOIN users u on u.id = o.fo_id 
							JOIN tbl_route r ON r.route_id = o.route_id 
							JOIN tbl_retailer ret ON ret.retailer_id = o.retailer_id
							WHERE o.point_id = '".$depotList[0]->point_id."' 
							AND date_format(o.update_date,'%Y-%m-%d') 
							between '".$fromDate."' AND '".$toDate."'
							AND o.order_type = 'Delivered'
							";
							
							
				
										
				if($req->get('fo_id') != '')
				{
					$DynSQL .= "AND o.fo_id = '".$req->get('fo_id')."'";	
				}
				
				$DynSQL .= "GROUP BY order_id order by name";
			
				$depotSales=DB::select("$DynSQL");							
				
			} else {	
				
				
				$DynSQL =	"SELECT u.display_name, u.email, r.rname, ret.name, o.* 
							FROM tbl_order o JOIN tbl_order_details od ON o.order_id =  od.order_id
							JOIN users u on u.id = o.fo_id 
							JOIN tbl_route r ON r.route_id = o.route_id 
							JOIN tbl_retailer ret ON ret.retailer_id = o.retailer_id
							WHERE o.point_id = '".$depotList[0]->point_id."' 
							AND o.order_type = 'Delivered'
							";
				
										
				if($req->get('fo_id') != '')
				{
					$DynSQL .= "AND o.fo_id = '".$req->get('fo_id')."'";	
				}
				
				$DynSQL .= " GROUP BY order_id order by name";
				
					//echo $DynSql; exit;
			
				$depotSales = DB::select("$DynSQL");	
				
				//echo '<pre/>'; print_r($depotSales); exit;

			}							
		
		}		
			
		//echo '<pre/>'; print_r($foList); exit;

		return view('Depot.report.fo_sales_summary')->with('depotList',$depotList)
		  ->with('foList',$foList)
		  ->with('routeList',$routeList)
		  ->with('retailerList',$retailerList)
		  ->with('depotSales',$depotSales)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}						

}  // class end

