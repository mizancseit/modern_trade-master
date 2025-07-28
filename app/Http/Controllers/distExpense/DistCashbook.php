<?php

namespace App\Http\Controllers\distExpense;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
/* Load Model Reference Begin */
/* Load Model Reference End */
use Hash;

use DB;
use Auth;
use Session;

class DistCashbook extends Controller
{
	
	private  $depot_in_charge;
	private  $user_type_id;
    
	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
	   
    }
	
		
											/* Distributor Payments Begin*/
	
	public function dist_cashbook()
	{
		$selectedMenu   = 'Dist Expense';         		// Required Variable
		$pageTitle      = 'Income/Expense List';       // Required Variable
		
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id 	= Auth::user()->user_type_id;
		
		
		//echo '<pre/>'; print_r(Auth::user()); exit;
		
		
		$ExPenseHead = DB::select("SELECT * FROM dist_accounts_head WHERE accounts_head_type = 'expense'");
									
								
		if($this->user_type_id == 1)
		{
			$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
			
			$depotCashbook=DB::select("SELECT p.point_name, cb.*, ahd.* FROM tbl_point p 
										JOIN dist_cash_book cb
										JOIN dist_accounts_head ahd ON ahd.accounts_head_id = cb.perticular_head_id
										ON p.point_id = cb.point_id WHERE cb.trans_type = 'debited'
										");
		
		} elseif($this->user_type_id == 5) {
			
			$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
			
			$depotCashbook=DB::select("SELECT p.point_name, cb.*, ahd.* FROM users u 
									JOIN  tbl_user_business_scope bs ON u.id = bs.user_id 
									JOIN dist_cash_book cb ON cb.point_id = bs.point_id
									JOIN tbl_point p ON p.point_id = cb.point_id
									JOIN dist_accounts_head ahd ON ahd.accounts_head_id = cb.perticular_head_id
									WHERE u.id = '".$this->depot_in_charge."' AND trans_type = 'debited'");

		}		
		 

		return view('distExpense.cashbook.dist_cashbook')->with('depotList',$depotList)
		  ->with('ExPenseHead',$ExPenseHead)
		  ->with('depotCashbook',$depotCashbook)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}
	
	
	public function dist_cashbook_history(Request $req)
	{
		$selectedMenu   = 'Dist Expense Summary';         	// Required Variable
		$pageTitle      = 'Dist Cashbook History';       	// Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		//$depotList = DB::table('tbl_depot_setup')->orderBy('depot_id','asc')->get();
		
		if($this->user_type_id == 1)
		{
			$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
		
		} elseif($this->user_type_id == 5) {
			
			$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
		}
		
		
		if ($req->get('point_id')) 
		{
			$sqlDate = '';
			if($req->get('fromDate')!='')
			{
				$sqlDate = " AND date_format(ap.trans_date,'%d-%m-%Y') >= '".$req->get('fromDate')."'";
			}
			
			if($req->get('toDate')!='')
			{
				$sqlDate .= " AND date_format(ap.trans_date,'%d-%m-%Y') <= '".$req->get('toDate')."'";
			}
			
			//echo $sqlDate; exit;
			/*
			$depotPayment=DB::select("SELECT ds.depot_name, ds.opening_balance, ap.* FROM tbl_depot_setup ds 
										LEFT JOIN depot_accounts_payments ap
									ON ds.depot_id = ap.depot_id 
									WHERE ds.depot_id = '".$req->get('depot_id')."'
									$sqlDate
									order by transaction_id asc, depot_id asc");
			*/
									
			$depotPayment=DB::select("SELECT p.point_name, ds.opening_balance, ap.* 
									FROM tbl_depot_summary ds 
										LEFT JOIN depot_accounts_payments ap
											ON ds.point_id = ap.point_id 
										JOIN tbl_point p ON p.point_id = ds.point_id
									WHERE p.point_id = '".$req->get('point_id')."'
									order by transaction_id asc, ds.point_id asc");						
						
			/*			
			$creditData = DB::select("SELECT SUM(ap.trans_amount) as total_credited FROM tbl_depot_setup ds 
							LEFT JOIN depot_accounts_payments ap	ON ds.depot_id = ap.depot_id 
							WHERE ds.depot_id = '".$req->get('depot_id')."' AND ap.transaction_type = 'credit'
							$sqlDate");
			*/				
							
			$creditData = DB::select("SELECT SUM(ap.trans_amount) as total_credited FROM tbl_point p 
							LEFT JOIN depot_accounts_payments ap	ON p.point_id = ap.point_id 
							WHERE p.point_id = '".$req->get('point_id')."' AND ap.transaction_type = 'credit'
							");				
			/*
			$debitData = DB::select("SELECT SUM(ap.trans_amount) total_debited FROM tbl_depot_setup ds 
							LEFT JOIN depot_accounts_payments ap
							ON ds.depot_id = ap.depot_id 
							WHERE ds.depot_id = '".$req->get('depot_id')."' AND ap.transaction_type = 'debit'
							$sqlDate");	
			*/
			
			$debitData = DB::select("SELECT SUM(ap.trans_amount) as total_debited FROM tbl_point p 
							LEFT JOIN depot_accounts_payments ap	ON p.point_id = ap.point_id 
							WHERE p.point_id = '".$req->get('point_id')."' AND ap.transaction_type = 'debit'
							");

			
		} else {
		 
			$depotPayment = array();
			$creditData = array();
			$debitData = array();
		}
		 
		//echo '<pre/>'; print_r($depotList); exit;
		

		return view('distExpense.dist_cashbook_history')
		  ->with('depotList',$depotList)
		  ->with('depotPayment',$depotPayment)
		  ->with('creditData',$creditData)
		  ->with('debitData',$debitData)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}

	
	public function dist_cashbook_process(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Dist Expense';         		// Required Variable
			$pageTitle      = 'Income/Expense List';       // Required Variable
				  
			//$depot_id 		= 	$req->input('depot_id');
			
			$point_id 					= 	$req->input('point_id');
			$perticular_head_id 		= 	$req->input('perticular_head_id');
			$trans_amount				=	$req->input('trans_amount');
			$trans_description			=	$req->input('trans_description');
			$trans_date					=	date_create($req->input('trans_date'));
			$trans_date 				= 	date_format($trans_date,"Y-m-d");
			 
			$user_id		=	Auth::user()->id;
			$entryDate = date('Y-m-d H:i:s');
			 
			
			$this->user_type_id 	= Auth::user()->user_type_id;
		
			if($this->user_type_id == 1) { // admin -login
				
				$userList = DB::select("SELECT * FROM users WHERE id in 
											(SELECT user_id FROM tbl_user_business_scope WHERE point_id = '".$point_id."')
										");
										
				$local_depot_in_charge   = $userList[0]->id;
				
				$point=DB::insert('insert into dist_cash_book(point_id, depot_in_charge, perticular_head_id, trans_type,  
				trans_amount, trans_date, trans_description, entry_by, entry_date) 
				values (?,?,?,?,?,?,?,?,?)', [$point_id, $local_depot_in_charge, $perticular_head_id, 'debited', $trans_amount, $trans_date, 
				$trans_description, $user_id, $entryDate]);
				
			} elseif($this->user_type_id == 5) {   //depot login
				
				$this->depot_in_charge 	= Auth::user()->id;
				
				$point=DB::insert('insert into dist_cash_book(point_id, depot_in_charge, perticular_head_id, trans_type, 
				trans_amount, trans_date, trans_description, entry_by, entry_date) 
				values (?,?,?,?,?,?,?,?,?)', [$point_id, $this->depot_in_charge, $perticular_head_id, 'debited', $trans_amount, $trans_date, 
				$trans_description, $this->depot_in_charge, $entryDate]);
				
			}
					 
			return Redirect::to('/newDistCashBook')->with('success', 'Successfully Payment Added.');
			   
		}
	}


	
	public function dist_cashbook_edit(Request $req)
	{
		
		$selectedMenu   = 'Dist Expense';         		// Required Variable
		$pageTitle      = 'Income/Expense List';       // Required Variable
		
		if($req->input('id'))
		{	
			
			$ExPenseHead = DB::select("SELECT * FROM dist_accounts_head WHERE accounts_head_type = 'expense'");
			
			$depotCashbook=DB::select("SELECT p.point_name, cb.* FROM tbl_point p 
										JOIN dist_cash_book cb
									ON p.point_id = cb.point_id 
									WHERE cb.cash_book_id = '".$req->input('id')."'");
			
			 
			 //dd($division);
			 
			 $this->depot_in_charge = Auth::user()->id;
			 $this->user_type_id 	= Auth::user()->user_type_id;
			 
			 
			 if($this->user_type_id == 1){
				 
				$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
				 
			 } elseif($this->user_type_id == 5){
				 
				$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
				 
			 }
			
		
			return view('distExpense.cashbook.dist_cashbook_edit')->with('depotList',$depotList)
			  ->with('depotCashbook',$depotCashbook)
			  ->with('ExPenseHead',$ExPenseHead)
			  ->with('selectedMenu',$selectedMenu)
			  ->with('pageTitle',$pageTitle); 
			  
		}	  
			  

	}

	
	
	public function dist_cashbook_edit_process(Request $req)
	{
		
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Dist Expense';         		// Required Variable
			$pageTitle      = 'Income/Expense List';       // Required Variable
				  
			//$depot_id 		= 	$req->input('depot_id');
			
			$point_id 				= 	$req->input('point_id');
			$perticular_head_id 	= 	$req->input('perticular_head_id');
			$trans_amount			=	$req->input('trans_amount');
			$trans_description		=	$req->input('trans_description');
			$trans_date				=	date_create($req->input('trans_date'));
			$trans_date 			= 	date_format($trans_date,"Y-m-d");
			 
			$user_id				=	Auth::user()->id;
			$updateDate 			= 	date('Y-m-d H:i:s');
	

			$DepotPaymentUpd = DB::table('dist_cash_book')->where('cash_book_id',$req->get('id'))->update(
						[
						   
							//'depot_id'             	=> 	$depot_id,
							'point_id'             	=> 	$point_id,
							'perticular_head_id'   	=> 	$perticular_head_id,
							'trans_amount'         	=>	$trans_amount,
							'trans_date'		 	=> 	$trans_date,
							'trans_description'		=> 	$trans_description,
							'update_by'		 		=> 	$user_id,
							'update_date'           => 	$updateDate
						   
						]
			); 
					
			//sync balance as well		
			//self::depot_balance_update($depot_id);		
			
			//self::depot_balance_update($point_id);		
			  
			return Redirect::to('/newDistCashBook')->with('success', 'Successfully Updated Payment Info.');
		}

	}
	
	private function dist_balance_update($point_id)
	{
		if($point_id)
		{
			/////////////////////////////// depot balance update ////////////////////////////////
			$depotPayment=DB::select("SELECT p.point_name, ds.opening_balance, ap.* 
									FROM tbl_depot_summary ds 
										LEFT JOIN depot_accounts_payments ap
											ON ds.point_id = ap.point_id 
										JOIN tbl_point p ON p.point_id = ds.point_id
									WHERE p.point_id = '".$point_id."'
									order by transaction_id asc, ds.point_id asc");
						
						
			$creditData = DB::select("SELECT SUM(ap.trans_amount) as total_credited FROM tbl_point p 
							LEFT JOIN depot_accounts_payments ap	ON p.point_id = ap.point_id 
							WHERE p.point_id = '".$point_id."' AND ap.transaction_type = 'credit'
							");

			$debitData = DB::select("SELECT SUM(ap.trans_amount) as total_debited FROM tbl_point p 
							LEFT JOIN depot_accounts_payments ap	ON p.point_id = ap.point_id 
							WHERE p.point_id = '".$point_id."' AND ap.transaction_type = 'debit'
							");
							
			$current_Balance = ($depotPayment[0]->opening_balance + ($creditData[0]->total_credited - $debitData[0]->total_debited));
			
			$DepotPaymentUpd = DB::table('tbl_depot_summary')->where('point_id',$point_id)->update(
						[
							'depot_current_balance'  => $current_Balance
						]
			);
		}	
	}


	
	public function dist_cashbook_delete(Request $request)
    {
        $id  = $request->get('id');
        
	    $paymentDelete = DB::table('dist_cash_book')->where('cash_book_id',$request->get('id'))->delete();           
        
		/*
		if ($paymentDelete) 
        {
			//self::depot_balance_update($depotData[0]->depot_id);
			self::depot_balance_update($depotData[0]->point_id);
            return 0;
        }
		*/
        
		return Redirect::to('/newDistCashBook')->with('success', 'Payment Info Deleted Successfully.');
      
    }
							

	public function dist_expense_summary(Request $req)
	{
		$selectedMenu   = 'Dist Expense Summary';         		// Required Variable
		$pageTitle      = 'Dist Expense Summary';       		// Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		$expenseList = DB::select("SELECT * FROM dist_accounts_head WHERE accounts_head_type = 'expense'");
		
		if ($req->get('accounts_head_id')) 
		{
			
			if ($req->get('fromExpenseDate') != '' && $req->get('toExpenseDate') != '' && $req->get('accounts_head_id') && $req->get('accounts_head_id')!='all') 
			{
				
				$fromExpenseDate = explode('-',$req->get('fromExpenseDate'));
				$toExpenseDate = explode('-',$req->get('toExpenseDate'));
				
				$fromDate = $fromExpenseDate[2] . '-' . $fromExpenseDate[1] . '-' . $fromExpenseDate[0]; 
				$toDate = $toExpenseDate[2] . '-' . $toExpenseDate[1] . '-' . $toExpenseDate[0]; 
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM dist_accounts_head ah JOIN dist_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
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
								FROM dist_accounts_head ah JOIN dist_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
								JOIN tbl_point p ON p.point_id = dc.point_id 
								WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
								AND date_format(dc.trans_date,'%Y-%m-%d') between '".$fromDate."' AND '".$toDate."'
								AND ah.accounts_head_type = 'expense'
								GROUP BY dc.perticular_head_id";
				
			} elseif($req->get('accounts_head_id')!='all') {
				
				$ExpenseSQL = "SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
							FROM dist_accounts_head ah JOIN dist_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id
							JOIN tbl_point p ON p.point_id = dc.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."') 
							AND ah.accounts_head_id = '".$req->get('accounts_head_id')."'";
				
			
			} elseif($req->get('accounts_head_id')=='all') {
				
				 $ExpenseSQL = "
								SELECT p.point_name, ah.accounts_head_name, dc.trans_type, SUM(trans_amount) amount 
								FROM dist_accounts_head ah JOIN dist_cash_book dc ON ah.accounts_head_id = dc.perticular_head_id 
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
		

		return view('distExpense.cashbook.dist_expense_summary')
		  ->with('fromExpenseDate', $req->get('fromExpenseDate'))
		  ->with('toExpenseDate', $req->get('toExpenseDate'))
		  ->with('accounts_head_id',$req->get('accounts_head_id'))
		  ->with('expenseList', $expenseList)
		  ->with('depotExpenseData', $depotExpenseData)
		  ->with('selectedMenu', $selectedMenu)
		  ->with('pageTitle', $pageTitle);
			
	}
	
}  // class end

