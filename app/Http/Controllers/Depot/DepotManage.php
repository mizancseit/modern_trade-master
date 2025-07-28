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

class DepotManage extends Controller
{
	
	private  $depot_in_charge;
	private  $user_type_id;
    
	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
	   
    }
	
		
											/* Depot Payments Begin*/
	
	public function depot_payments()
	{
		$selectedMenu   = 'Depot';         // Required Variable
		$pageTitle      = 'Payment List';       // Required Variable
		
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id 	= Auth::user()->user_type_id;
		
		
		$user_business_type=DB::select("select * from users where id='".$this->depot_in_charge."'");
		
		foreach ($user_business_type as $type)
        {
       	
			$user_type=$type->business_type_id;
			$depot_name=$type->display_name;
       	
       	}
		
		//echo '<pre/>'; print_r(Auth::user()); exit;
		
			/*		
		$depotPayment=DB::select("SELECT ds.depot_name, ap.* FROM tbl_depot_setup ds 
									JOIN depot_accounts_payments ap
								ON ds.depot_id = ap.depot_id WHERE transaction_type = 'credit'");
								*/
								
		if($this->user_type_id == 1)
		{
			$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
			
			$depotPayment=DB::select("SELECT p.point_name, ap.* FROM tbl_point p 
										JOIN depot_accounts_payments ap
										ON p.point_id = ap.point_id WHERE transaction_type = 'credit'");
		
		} elseif($this->user_type_id == 5) {
			
			$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
			
			$depotPayment=DB::select("SELECT p.point_name, ap.* FROM users u 
									JOIN  tbl_user_business_scope bs ON u.id = bs.user_id 
									JOIN depot_accounts_payments ap ON ap.point_id = bs.point_id
									JOIN tbl_point p ON p.point_id = ap.point_id
									WHERE u.id = '".$this->depot_in_charge."' AND transaction_type = 'credit'
									ORDER BY ap.transaction_id DESC");

		}		
														
		 
		 //dd($division);
		 
		//$depotList = DB::table('tbl_depot_setup')->orderBy('depot_id','asc')->get();
		
		//echo '<pre/>'; print_r($depotList); exit;
		

		return view('Depot.depot_credit')->with('depotList',$depotList)
		  ->with('depotPayment',$depotPayment)
		  ->with('user_business_type',$user_type)
		  ->with('depot_name',$depot_name)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}

	public function depot_payment_list(Request $request)
	{
		$selectedMenu   = 'Depot';         // Required Variable
		$pageTitle      = 'Payment List';       // Required Variable
		
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id 	= Auth::user()->user_type_id;
		
		$fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        
		
		$user_business_type=DB::select("select * from users where id='".$this->depot_in_charge."'");
		
		foreach ($user_business_type as $type)
        {
       	
			$user_type=$type->business_type_id;
			$depot_name=$type->display_name;
       	
       	}
		
		if($fromdate!='' && $todate!='')
        {
								
			if($this->user_type_id == 1)
			{
				$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
				
				$depotPayment=DB::select("SELECT p.point_name, ap.* FROM tbl_point p 
											JOIN depot_accounts_payments ap
											ON p.point_id = ap.point_id WHERE transaction_type = 'credit' AND trans_date Between '$fromdate' AND '$todate'");
			
			} elseif($this->user_type_id == 5) {
				
				$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
											(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
										");
				
				$depotPayment=DB::select("SELECT p.point_name, ap.* FROM users u 
										JOIN  tbl_user_business_scope bs ON u.id = bs.user_id 
										JOIN depot_accounts_payments ap ON ap.point_id = bs.point_id
										JOIN tbl_point p ON p.point_id = ap.point_id
										WHERE u.id = '".$this->depot_in_charge."' AND transaction_type = 'credit' AND trans_date Between '$fromdate' AND '$todate'
										ORDER BY ap.transaction_id DESC");

			}		
														
		 }

		return view('Depot/depot_credit_list')->with('depotList',$depotList)
		  ->with('depotPayment',$depotPayment)
		  ->with('user_business_type',$user_type)
		  ->with('depot_name',$depot_name)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}
	
	
	public function depot_transaction_history(Request $req)
	{
		$selectedMenu   = 'Depot Transaction';         // Required Variable
		$pageTitle      = 'Depot Transaction History';       // Required Variable
		
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
		

		return view('Depot.depot_transaction_history')
		  ->with('depotList',$depotList)
		  ->with('depotPayment',$depotPayment)
		  ->with('creditData',$creditData)
		  ->with('debitData',$debitData)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}

	
	
	public function depot_paymnet_process(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Depot';         // Required Variable
			$pageTitle      = 'Payment List';       // Required Variable
				  
			//$depot_id 		= 	$req->input('depot_id');
			
			$point_id 		= 	$req->input('point_id');
			$payment_type	= 	$req->input('payment_type');
			
			
			if($payment_type == 'CHEQUE' || $payment_type=='ON-LINE')
			{

				if($payment_type=="CHEQUE")
				{	
					$bank_name		= 	$req->input('bank_name');
					$branch_name	= 	$req->input('branch_name');
					$cheque_date	=	date_create($req->input('cheque_date'));
					$cheque_date 	= 	date_format($cheque_date,"Y-m-d");
			    }

			    if($payment_type=="ON-LINE")
				{	
					$bank_name		= 	$req->input('ssgbank_name');
					$branch_name	= 	'';
					$cheque_date	=	'';
					$cheque_date 	= 	'';
				}
				
				
				
				//$acc_no			= 	$req->input('acc_no');
				//$cheque_no		= 	$req->input('cheque_no');
				
				
			
			} else {
			
				$bank_name		= 	'';
				$branch_name	= 	'';
				$acc_no			= 	'';
				$cheque_no		= 	'';
				$cheque_date	= 	'';
			}
			
			$payment_remarks	= 	$req->input('payment_remarks');
			$trans_amount	    =	$req->input('trans_amount');
			
			/*
			$trans_date		=	date_create($req->input('trans_date'));
			$trans_date 	= 	date_format($trans_date,"Y-m-d");
			*/
			
			$trans_date		=	date('Y-m-d'); // system date
			
			
			$ref_no	=	$req->input('ref_no');
			 
			$user_id		=	Auth::user()->id;
			$entryDate = date('Y-m-d H:i:s');
			
			
		    //////////////////////////////  new payment entry //////////////////////////////////////
			/*
			$point=DB::insert('insert into depot_accounts_payments(depot_id, transaction_type, payment_type, 
			trans_amount, trans_date, entry_by, entry_date) 
			values (?,?,?,?,?,?,?)', [$depot_id, 'credit', $payment_type, $trans_amount, $trans_date, 
			$user_id, $entryDate]);
			*/
			
			
			$this->user_type_id 	= Auth::user()->user_type_id;
		
			if($this->user_type_id == 1) { // admin -login
				
				$userList = DB::select("SELECT * FROM users WHERE id in 
											(SELECT user_id FROM tbl_user_business_scope WHERE point_id = '".$point_id."')
										");
										
				$local_depot_in_charge   = $userList[0]->id;
				
				$point=DB::insert('insert into depot_accounts_payments(point_id, depot_in_charge, transaction_type, payment_type, 
				bank_name, branch_name, cheque_date, ref_no, trans_amount, trans_date, entry_by, entry_date, payment_remarks) 
				values (?,?,?,?,?,?,?,?,?,?,?,?,?)', [$point_id, $local_depot_in_charge, 'credit', $payment_type, $bank_name,
				$branch_name, $cheque_date, $ref_no, $trans_amount, $trans_date, $user_id, $entryDate, $payment_remarks]);
				
				//$payment_id = DB::getPdo()->lastInsertId();
				
				/* $data_payment = DB::table('depot_accounts_payments')->orderBy('transaction_id', 'desc')->first();
				
				self::depot_cashbook_bank_deposit($point_id, $data_payment->transaction_id, $trans_amount, $local_depot_in_charge); */
				
			} elseif($this->user_type_id == 5) {   //depot login
				
				$this->depot_in_charge 	= Auth::user()->id;
				
				if($payment_type=="CHEQUE")
				{
					$point=DB::insert('insert into depot_accounts_payments(point_id, depot_in_charge, transaction_type, payment_type, 
					bank_name,	branch_name, cheque_date, ref_no, trans_amount, trans_date, entry_by, entry_date, payment_remarks) 
					values (?,?,?,?,?,?,?,?,?,?,?,?,?)', [$point_id, $this->depot_in_charge, 'credit', $payment_type, $bank_name,
					$branch_name, $cheque_date, $ref_no, $trans_amount, $trans_date, $this->depot_in_charge, $entryDate, 
					$payment_remarks]);
				
				} else if($payment_type=="ON-LINE"){
					
					$point=DB::insert('insert into depot_accounts_payments(point_id, depot_in_charge, transaction_type, payment_type, 
					bank_info_id, ref_no, trans_amount, trans_date, entry_by, entry_date, payment_remarks) 
					values (?,?,?,?,?,?,?,?,?,?,?)', [$point_id, $this->depot_in_charge, 'credit', $payment_type,  $bank_name,
					$ref_no, $trans_amount, $trans_date, $this->depot_in_charge, $entryDate, $payment_remarks]);
				
				} else {
					
					$point=DB::insert('insert into depot_accounts_payments(point_id, depot_in_charge, transaction_type, payment_type, 
					ref_no, trans_amount, trans_date, entry_by, entry_date, payment_remarks) 
					values (?,?,?,?,?,?,?,?,?,?)', [$point_id, $this->depot_in_charge, 'credit', $payment_type, 
					$ref_no, $trans_amount, $trans_date, $this->depot_in_charge, $entryDate, $payment_remarks]);
					
				}
				
				
				
				
				//$payment_id = DB::getPdo()->lastInsertId();
				
				/* $data_payment = DB::table('depot_accounts_payments')->orderBy('transaction_id', 'desc')->first();
				
				self::depot_cashbook_bank_deposit($point_id, $data_payment->transaction_id, $trans_amount, $this->depot_in_charge); */
				
			}
			
			
			//sync depot balance as well
			//self::depot_balance_update($depot_id);
					 
			return Redirect::to('/newDepotPayment')->with('success', 'Successfully Payment Added.');
			   
		}
	
	}


	
	public function depot_payment_edit(Request $req)
	{
		
		$selectedMenu   = 'Depot';         		// Required Variable
		$pageTitle      = 'Payment List';       // Required Variable
		
		if($req->input('id'))
		{	
			/*
			$depotPayment=DB::select("SELECT ds.depot_name, ap.* FROM tbl_depot_setup ds 
										JOIN depot_accounts_payments ap
									ON ds.depot_id = ap.depot_id WHERE transaction_id = '".$req->input('id')."'");
			*/
			
			$depotPayment=DB::select("SELECT p.point_name, ap.* FROM tbl_point p 
										JOIN depot_accounts_payments ap
									ON p.point_id = ap.point_id 
									WHERE ap.transaction_id = '".$req->input('id')."'");
			
			 
			 //dd($division);
			 
			 $this->depot_in_charge 	= Auth::user()->id;
			 $this->user_type_id 	= Auth::user()->user_type_id;
			 
			 
			 if($this->user_type_id == 1){
				 
				$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
				 
			 } elseif($this->user_type_id == 5){
				 
				$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
				 
			 }
			
			
			$user_business_type=DB::select("select * from users where id='".$this->depot_in_charge."'");
		
			foreach ($user_business_type as $type)
			{
				$user_type=$type->business_type_id;
			}
			
			//echo '<pre/>'; print_r($depotList); exit;
			

			return view('Depot.depot_credit_edit')->with('depotList',$depotList)
			  ->with('depotPayment',$depotPayment)
			  ->with('user_business_type',$user_type)
			  ->with('selectedMenu',$selectedMenu)
			  ->with('pageTitle',$pageTitle); 
			  
		}	  
			  

	}

	
	
	public function depot_payment_edit_process(Request $req)
	{
		
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Depot';         		// Required Variable
			$pageTitle      = 'Payment List';       // Required Variable
				  
			//$depot_id 		= 	$req->input('depot_id');
			
			$point_id 		= 	$req->input('point_id');
			$payment_type	= 	$req->input('payment_type');
			
			if($payment_type == 'CHEQUE' || $payment_type == 'ON-LINE' )
			{
				
				if($payment_type=="CHEQUE")
				{	
					$bank_name		= 	$req->input('bank_name');
					$branch_name	= 	$req->input('branch_name');
					$cheque_date	= 	$req->input('cheque_date');
					
					
				}

			    if($payment_type=="ON-LINE")
				{	
					$bank_name		= 	$req->input('ssgbank_name');
				}
				
				
			
			} else {
				
				$bank_name		= 	'';
				$branch_name	= 	'';
				$acc_no			= 	'';
				$cheque_no		= 	'';
				$cheque_date	= 	'';
				
			}
			
			
			$trans_date	=	date('Y-m-d'); // system date
				
			$ref_no				=	$req->input('ref_no');
			$payment_remarks	=	$req->input('payment_remarks');
			$trans_amount		=	$req->input('trans_amount');
			
			//$trans_date			=	$req->input('trans_date');
			
			$trans_date		=	date('Y-m-d');
			 
			$user_id		=	Auth::user()->id;
			
			$updateDate 	= date('Y-m-d H:i:s');
	
			if($payment_type=="CHEQUE")
			{
	
			$DepotPaymentUpd = DB::table('depot_accounts_payments')->where('transaction_id',$req->get('id'))->update(
						[
						   
							//'depot_id'             	=> 	$depot_id,
							'point_id'             	=> 	$point_id,
							'payment_type'         	=> 	$payment_type,
							'payment_remarks'       => 	$payment_remarks,
							
							'bank_name'         	=> 	$bank_name,
							'branch_name'         	=> 	$branch_name,
							'cheque_date'         	=> 	$cheque_date,
							
							'ref_no'         		=> 	$ref_no,
							'trans_amount'         	=>	$trans_amount,
							'trans_date'		 	=> 	$trans_date,
							'update_by'		 		=> 	$user_id,
							'update_date'           => 	$updateDate
						   
						]
					);
			
			} elseif ($payment_type=="ON-LINE") {
				
				$DepotPaymentUpd = DB::table('depot_accounts_payments')->where('transaction_id',$req->get('id'))->update(
						[
						   
							//'depot_id'             	=> 	$depot_id,
							'point_id'             	=> 	$point_id,
							'payment_type'         	=> 	$payment_type,
							'payment_remarks'       => 	$payment_remarks,
						
							'bank_info_id'			=>  $bank_name,
							
							'ref_no'         		=> 	$ref_no,
							'trans_amount'         	=>	$trans_amount,
							'trans_date'		 	=> 	$trans_date,
							'update_by'		 		=> 	$user_id,
							'update_date'           => 	$updateDate
						   
						]
					); 
				

			}	else {
				
				$DepotPaymentUpd = DB::table('depot_accounts_payments')->where('transaction_id',$req->get('id'))->update(
						[
						   
							//'depot_id'             	=> 	$depot_id,
							'point_id'             	=> 	$point_id,
							'payment_type'         	=> 	$payment_type,
							'payment_remarks'       => 	$payment_remarks,
							
							'bank_name'         	=> 	'',
							'branch_name'         	=> 	'',
							'cheque_date'         	=> 	'',
							
							'bank_info_id'			=>  0,
							
							'ref_no'         		=> 	$ref_no,
							'trans_amount'         	=>	$trans_amount,
							'trans_date'		 	=> 	$trans_date,
							'update_by'		 		=> 	$user_id,
							'update_date'           => 	$updateDate
						   
						]
					); 

			}			
					
					
					
			//sync balance as well		
			//self::depot_balance_update($depot_id);		
			
			/*self::depot_cashbook_bank_deposit($point_id, $req->get('id'), $trans_amount, $user_id);*/		
			  
			return Redirect::to('/newDepotPayment')->with('success', 'Successfully Updated Payment Info.');
		}

	}
	
	
	/*
	private function depot_balance_update($point_id)
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
	*/


	
	public function depot_payment_delete(Request $request)
    {
        $id  = $request->get('id');
        
		$depotData=DB::select("SELECT point_id FROM depot_accounts_payments
									WHERE transaction_id = '".$request->get('id')."'
									");
		
	    $paymentDelete = DB::table('depot_accounts_payments')->where('transaction_id',$request->get('id'))->delete();           
        
		
		if ($paymentDelete) 
        {
			$paymentDelete = DB::table('depot_cash_book')->where('payment_id',$request->get('id'))->delete();   
			//self::depot_balance_update($depotData[0]->point_id);
            //return 0;
        }
		
        
		return Redirect::to('/newDepotPayment')->with('success', 'Payment Info Deleted Successfully.');
      
    }
	
	
										/* Depot Collection Begin*/
	
	public function depot_collection(Request $req)
	{
		$selectedMenu   = 'Depot Collection';      // Required Variable
		$pageTitle      = 'Collection List';       // Required Variable
			
		 
		$this->depot_in_charge 	= Auth::user()->id;
		$this->user_type_id 	= Auth::user()->user_type_id;
			
       	if($this->user_type_id == 1) {
			
			$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
			
			$routeList = DB::table('tbl_route')->orderBy('rname','asc')->get();
			
			$retailerList = DB::table('tbl_retailer')->orderBy('name','asc')->get();
			
			$foList = DB::select("SELECT u.id, u.display_name FROM users u JOIN tbl_user_type ut 
								ON u.user_type_id = ut.user_type_id
								JOIN tbl_user_details ud ON ud.user_id = u.id
								WHERE ut.user_type_id = '12'");
			
			$depotCollection=DB::select("SELECT p.point_name, u.display_name as fo_name, rt.name as retailer_name, 
										rt.vAddress as retailer_Address, dc.* 
										FROM tbl_point p 
										JOIN depot_collection dc ON p.point_id = dc.point_id
										JOIN tbl_retailer rt ON rt.retailer_id = dc.retailer_id
										JOIN users u ON u.id = dc.collect_by");
										
		
		} elseif($this->user_type_id == 5) {
			
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

			
			
			if ($req->get('fromCollectionDate') != '' && $req->get('toCollectionDate') != '') 
			{
				
				$fromCollectionDate = explode('-',$req->get('fromCollectionDate'));
				$toCollectionDate = explode('-',$req->get('toCollectionDate'));
				
				$fromDate = $fromCollectionDate[2] . '-' . $fromCollectionDate[1] . '-' . $fromCollectionDate[0]; 
				$toDate = $toCollectionDate[2] . '-' . $toCollectionDate[1] . '-' . $toCollectionDate[0];
				
				$DynSQL = "SELECT p.point_name, u.display_name as fo_name, rt.name as retailer_name, 
										rt.vAddress as retailer_Address, dc.* 
										FROM tbl_point p 
										JOIN depot_collection dc ON p.point_id = dc.point_id
										JOIN tbl_retailer rt ON rt.retailer_id = dc.retailer_id
										JOIN users u ON u.id = dc.collect_by
										WHERE dc.depot_in_charge = '".$this->depot_in_charge."'
										AND date_format(dc.collection_date,'%Y-%m-%d') between '".$fromDate."' 
										AND '".$toDate."'";
				
										
				if($req->get('fo_id') != '')
				{
					$DynSQL .= "AND dc.collect_by = '".$req->get('fo_id')."'";	
				}
			
				$depotCollection=DB::select("$DynSQL");							
				
			} else {	
			
				$Today = date('Y-m-d');
				//$toDate = date('Y-m-d');
				
				$DynSQL = "SELECT p.point_name, u.display_name as fo_name, rt.name as retailer_name, 
										rt.vAddress as retailer_Address, dc.* 
										FROM tbl_point p 
										JOIN depot_collection dc ON p.point_id = dc.point_id
										JOIN tbl_retailer rt ON rt.retailer_id = dc.retailer_id
										JOIN users u ON u.id = dc.collect_by
										WHERE dc.depot_in_charge = '".$this->depot_in_charge."'
										AND date_format(dc.collection_date,'%Y-%m-%d') = '".$Today."' ";
										
				if($req->get('fo_id') != '')
				{
					$DynSQL .= "AND dc.collect_by = '".$req->get('fo_id')."'";	
				}			
			
				   $depotCollection=DB::select("$DynSQL");	

			}							
		
		}		
			
		//echo '<pre/>'; print_r($foList); exit;

		return view('Depot/depot_collection')->with('depotList',$depotList)
		  ->with('foList',$foList)
		  ->with('routeList',$routeList)
		  ->with('retailerList',$retailerList)
		  ->with('depotCollection',$depotCollection)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}
	
	
	public function depot_collection_process(Request $req)
	{
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Depot';         		// Required Variable
			$pageTitle      = 'Payment List';       // Required Variable
				  
			//$depot_id 				= 	$req->input('depot_id');
			$point_id 				= 	$req->input('point_id');
			
			$route_id				= 	$req->input('route_id');
			
			$retailer_id			= 	$req->input('retailer_id');
			$collect_by				=	$req->input('fo_id');
			$collection_amount		=	$req->input('collection_amount');
			
			$collection_date		=	date_create($req->input('collection_date'));
			$collection_date 		= 	date_format($collection_date,"Y-m-d");
			
			//$invoice_no 			= 	$req->input('invoice_no');
			$collection_note 		= 	$req->input('collection_note');
			$reference_no 			= 	$req->input('reference_no');
			
			//$commission_type		=	$req->input('commission_type');
			//$commission_amount		=	$req->input('commission_amount');
			 
			$entry_by				=	Auth::user()->id;
			
			$entry_date 			= date('Y-m-d H:i:s');
			
			
			
			
		    //////////////////////////////  new collection entry //////////////////////////////////////
			/*
			$point=DB::insert('insert into depot_collection(depot_id, collect_by, retailer_id, 
			collection_amount, commission_amount, commission_type, collection_date, entry_by, entry_date) 
			values (?,?,?,?,?,?,?,?,?)', [$depot_id, $collect_by, $retailer_id, $collection_amount, 
			$commission_amount, $commission_type, $collection_date, $entry_by, $entry_date]);
			*/
			
			
			$this->user_type_id 	= Auth::user()->user_type_id;
			
			if($this->user_type_id == 1) {
				
				$userList = DB::select("SELECT * FROM users WHERE id in 
										(SELECT user_id FROM tbl_user_business_scope WHERE point_id = '".$point_id."')
									");
									
				$local_depot_in_charge   = $userList[0]->id;
				
				$point=DB::insert('insert into depot_collection(point_id, route_id, depot_in_charge, collect_by, retailer_id, 
				collection_amount, reference_no, collection_note, collection_date, entry_by, entry_date) 
				values (?,?,?,?,?,?,?,?,?,?,?)', [$point_id, $route_id, $local_depot_in_charge, $collect_by, $retailer_id, $collection_amount, 
				$reference_no, $collection_note, $collection_date, $entry_by, $entry_date]);
				
				//sync depot cashbook
				$data_collection = DB::table('depot_collection')->orderBy('collection_id', 'desc')->first();
				self::depot_cashbook($point_id, $data_collection->collection_id, $collection_amount, $local_depot_in_charge, $mode = 'insert');
				
			} elseif($this->user_type_id == 5) {
				
				$this->depot_in_charge 	= Auth::user()->id;
				
				$point=DB::insert('insert into depot_collection(point_id, route_id, depot_in_charge, collect_by, retailer_id, 
				collection_amount, reference_no, collection_note, collection_date, entry_by, entry_date) 
				values (?,?,?,?,?,?,?,?,?,?,?)', [$point_id, $route_id, $this->depot_in_charge, $collect_by, $retailer_id, $collection_amount, 
				$reference_no, $collection_note, $collection_date, $entry_by, $entry_date]);
				
				//sync depot cashbook
				$data_collection = DB::table('depot_collection')->orderBy('collection_id', 'desc')->first();
				self::depot_cashbook($point_id, $data_collection->collection_id, $collection_amount, $this->depot_in_charge, $mode = 'insert');
				
			}
			
			//$collection_id = DB::getPdo()->lastInsertId();
			
			$retailer_info = array('retailer_id'=>$retailer_id, 'point_id'=>$point_id, 
								'collection_id'=>$data_collection->collection_id, 
								'collection_amount'=>$collection_amount,
								'collection_date' => $collection_date, 'mode'=>'add');
			
			//sync retailer credit laser as well
			self::reatiler_credit_ledger($retailer_info);
			
					 
			return Redirect::to('/DepotCollection')->with('success', 'Successfully Collection Added.');
			   
		}
	}
	
	
	public function depot_collection_edit(Request $req)
	{
		
		$selectedMenu   = 'Depot Collection';      // Required Variable
		$pageTitle      = 'Collection List';       // Required Variable
			
		$depotCollection=DB::select("SELECT p.point_name, u.display_name as fo_name, rt.name as retailer_name, 
										rt.vAddress as retailer_Address, dc.* 
										FROM tbl_point p 
										JOIN depot_collection dc ON p.point_id = dc.point_id
										JOIN tbl_retailer rt ON rt.retailer_id = dc.retailer_id
										JOIN users u ON u.id = dc.collect_by
										WHERE dc.collection_id = '".$req->get('id')."'");
		 
		 //dd($depotCollection);
		
		$this->depot_in_charge 	= Auth::user()->id;
		$this->user_type_id 	= Auth::user()->user_type_id; 
		 
		if($this->user_type_id == 1) {
			
			$depotList = DB::table('tbl_point')->orderBy('point_id','asc')->get();
			
			
			$foList = DB::select("SELECT u.id, u.display_name FROM users u JOIN tbl_user_type ut 
								ON u.user_type_id = ut.user_type_id
								JOIN tbl_user_details ud ON ud.user_id = u.id
								WHERE ut.user_type_id = '12'");
								
			$retailerList = DB::table('tbl_retailer')->orderBy('name','asc')->get();
			
		} elseif($this->user_type_id == 5) {
			
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
			/*
			$depotList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									"); */
		}
		
		
		
								
		
		//echo '<pre/>'; print_r($foList); exit;
		

		return view('Depot.depot_collection_edit')->with('depotList',$depotList)
		  ->with('foList',$foList)
		  ->with('routeList',$routeList)
		  ->with('retailerList',$retailerList)
		  ->with('depotCollection',$depotCollection)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);  
			  

	}

	
	
	public function collection_edit_process(Request $req)
	{
		
		if ($req->isMethod('post')) 
		{
			$selectedMenu   = 'Depot';         		// Required Variable
			$pageTitle      = 'Payment List';       // Required Variable
				  
			//$depot_id 			= 	$req->input('depot_id');
			$point_id 				= 	$req->input('point_id');
			$route_id				= 	$req->input('route_id');
			$retailer_id			= 	$req->input('retailer_id');
			$collect_by				=	$req->input('fo_id');
			$collection_amount		=	$req->input('collection_amount');
			$collection_date		=	date_create($req->input('collection_date'));
			$collection_date 		= 	date_format($collection_date,"Y-m-d");
			
			
			//$invoice_no 			= 	$req->input('invoice_no');
			$collection_note 			= 	$req->input('collection_note');
			$reference_no 			= 	$req->input('reference_no');
			
			//$commission_type		=	$req->input('commission_type');
			//$commission_amount		=	$req->input('commission_amount');
			 
			$update_by				=	Auth::user()->id;
			
			$update_date 			= 	date('Y-m-d H:i:s');
	

			$DepotPaymentUpd = DB::table('depot_collection')->where('collection_id',$req->input('id'))->update(
						[
						   
							//'point_id'            => 	$point_id,
							'route_id'            => 	$route_id,
							'collect_by'         	=> 	$collect_by,
							'retailer_id'         =>	$retailer_id,
							'collection_amount'		=> 	$collection_amount,
							//'commission_amount'	=> 	$commission_amount,
							//'commission_type'     => 	$commission_type,
							'collection_date'       => 	$collection_date,
							//'invoice_no'       	=> 	$invoice_no,
							'collection_note'       => 	$collection_note,
							'reference_no'       	=> 	$reference_no,
							
							'update_by'           	=> 	$update_by,
							'update_date'           => 	$update_date
						   
						]
					); 
			
			
			$retailer_info = array('retailer_id'=>$retailer_id, 'point_id'=>$point_id, 'collection_id'=>$req->input('id'), 
			'collection_amount'=>$collection_amount, 'collection_date' => $collection_date, 'mode' => 'edit');
			
			self::reatiler_credit_ledger($retailer_info);  
			
			//sync depot cashbook
			self::depot_cashbook($point_id, $req->input('id'), $collection_amount, $update_by, $mode = 'edit');
			  
			return Redirect::to('/DepotCollection')->with('success', 'Successfully Updated Collection Info.');
		}

	}
	
	
	public function depot_collection_delete(Request $request)
    {
        $collection_id  = $request->get('id');
		
		$retailerInfo = DB::select("SELECT * FROM depot_collection 
									WHERE collection_id = '".$collection_id."'
									");
		
		if(sizeof($retailerInfo)>0)
		{
		
			$retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$retailerInfo[0]->retailer_id."'");
			
			if(sizeof($retailerData)>0)
			{
				if($retailerData[0]->opening_balance > 0)
				{
					$retOpeningBalance = $retailerData[0]->opening_balance; 	
				} else {
					$retOpeningBalance = 0;
				}
			} else {
				$retOpeningBalance = 0;
			}
				
				
			// collection delete
			$depotCollection = DB::table('depot_collection')->where('collection_id',$collection_id)->delete();           
			
			//sync delete on others
			if ($depotCollection) 
			{
				DB::table('depot_cash_book')->where('collection_id', $collection_id)->delete();
				DB::table('retailer_credit_ledger')->where('collection_id', $collection_id)->delete(); 	
				
				$this->retailer_ledger_adjust($retailerInfo[0]->retailer_id, $retOpeningBalance);

				return 0;
			}
		
		} // main if closed
        
      return Redirect::to('/DepotCollection')->with('success', 'Collection Info Deleted.');
      
    }
	
	
	private function depot_cashbook_bank_deposit($point_id, $payment_id, $cash_amount, $depot_in_charge)
	{
		
		if($cash_amount> 0 && $point_id && $payment_id)
		{
			
		   // clear cash book	
			DB::table('depot_cash_book')->where('payment_id',$payment_id)->delete();  
		
			$cash_book_data = array();
			
			$cash_book_data['perticular_head_id'] = 13;   // 13 ref to Bank Deposit & Expense
			$cash_book_data['point_id'] =  $point_id;
			$cash_book_data['depot_in_charge'] =  $depot_in_charge;
			$cash_book_data['trans_amount'] =  $cash_amount;
			$cash_book_data['trans_description'] =  'Bank Deposit';
			$cash_book_data['trans_type'] =  'debited';
			$cash_book_data['trans_date'] =  date('Y-m-d H:i:s');
			$cash_book_data['payment_id'] =  $payment_id;
			$cash_book_data['entry_by'] =  Auth::user()->id;
			$cash_book_data['entry_date'] =  date('Y-m-d H:i:s');
			
			DB::table('depot_cash_book')->insert([$cash_book_data]);
		}  
	
	}
	
	
	private function depot_cashbook($point_id, $collection_id, $cash_amount, $depot_in_charge, $mode='insert')
	{
		
		if($cash_amount> 0)
		{
			
		   // clear cash book	
			//DB::table('depot_cash_book')->where('collection_id',$collection_id)->delete();  
		
			$cash_book_data = array();
			$cash_book_data['trans_amount'] =  $cash_amount;
			$cash_book_data['trans_description'] =  'Cash Collection';
			$cash_book_data['trans_type'] =  'credited';
			$cash_book_data['trans_date'] =  date('Y-m-d H:i:s');
			
			
			if($mode == 'insert' && $point_id && $collection_id)
			{
				$cash_book_data['perticular_head_id'] = 1;   // 1 ref to collection and income
				$cash_book_data['collection_id'] =  $collection_id;
				$cash_book_data['point_id'] =  $point_id;
				$cash_book_data['depot_in_charge'] =  $depot_in_charge; //Auth::user()->id;
				
				$cash_book_data['entry_by'] =  Auth::user()->id;
				$cash_book_data['entry_date'] =  date('Y-m-d H:i:s');
		
				DB::table('depot_cash_book')->insert([$cash_book_data]);
			}
			
		    
			if($mode == 'edit' && $collection_id)
		    {
				$cash_book_data['update_by'] =  Auth::user()->id;
				$cash_book_data['update_date'] =  date('Y-m-d H:i:s');
				
				$DepotCashBooktUpd = DB::table('depot_cash_book')->where('collection_id',$collection_id)->update(
					[ 
					    'trans_amount' => $cash_amount,
						'trans_description' =>	'Cash Collection',
						'trans_type' => 'credited',
						'trans_date' => date('Y-m-d H:i:s'),
						'perticular_head_id' => 1,
						'collection_id' => $collection_id,
						'point_id' =>  $point_id,
				        'depot_in_charge' =>  $depot_in_charge,
						'update_by' =>  Auth::user()->id,
						'update_date' =>  date('Y-m-d H:i:s')						
					]
				); 
		    }
			
		}  
	
	}
	
	
	private function reatiler_credit_ledger($retailer_info = array())
	{
		if(is_array($retailer_info))
		{
			if($retailer_info['mode']=='edit')
			{
			  //$retCreditLedger = DB::table('retailer_credit_ledger')->where('collection_id',$retailer_info['collection_id'])->delete();  
			}
			
			
			$credit_ledger_Data = array();
			$credit_ledger_Data['trans_type'] = 'collection';
			$credit_ledger_Data['accounts_type'] = 'income';
			$credit_ledger_Data['credit_ledger_date'] = date('Y-m-d H:i:s',strtotime($retailer_info['collection_date']));
			
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

			## invoice sales
			
			$retInVoiceSales = 0;
			/*
			if($retailer_info['invoice_no'] && $retailer_info['retailer_id'])
			{
				$retailerInvoice = DB::select("SELECT * FROM tbl_order WHERE retailer_id = '".$retailer_info['retailer_id']."'
				AND order_no = '".$retailer_info['invoice_no']."'");
				
				$retInVoiceSales = $retailerInvoice[0]->grand_total_value;
			}*/

			$credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;			
			
			##totalCollection
			$retCollect = $retailer_info['collection_amount'];
			$credit_ledger_Data['retailer_collection'] = $retCollect;
			
			
			##retailerBalance
			$remBalance = ($retOpeningBalance + $retInVoiceSales) - $retCollect;
			
			$credit_ledger_Data['retailer_balance'] = $remBalance;
			
			
			if($retailer_info['mode'] == 'add')
			{
				$credit_ledger_Data['collection_id'] = $retailer_info['collection_id'];
				$credit_ledger_Data['point_id'] = $retailer_info['point_id'];
				$credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
				
				$credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
				$credit_ledger_Data['entry_by'] = Auth::user()->id;
				
				DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
			
			} elseif($retailer_info['mode'] == 'edit') {
				
				$DepotCreditUpd = DB::table('retailer_credit_ledger')->where('collection_id',$retailer_info['collection_id'])->update(
						[
						   'retailer_collection' => $retCollect,
						   'update_by' => Auth::user()->id,
						   'update_date' => date('Y-m-d H:i:s'),
						]
					);
					
				$retailerData = DB::select("SELECT opening_balance FROM tbl_retailer WHERE retailer_id = '".$retailer_info['retailer_id']."'");
				$retEditOpenBalance = $retailerData[0]->opening_balance;	

				$this->retailer_ledger_adjust($retailer_info['retailer_id'], $retEditOpenBalance);		
			
			}
			
		}	
	}
	
	
	/*
	private function reatiler_credit_ledger($retailer_info = array())
	{
		if(is_array($retailer_info))
		{
			if($retailer_info['mode']=='edit')
			{
			  $retCreditLedger = DB::table('retailer_credit_ledger')->where('collection_id',$retailer_info['collection_id'])->delete();  
			}
			
			
			$credit_ledger_Data = array();
			$credit_ledger_Data['retailer_id'] = $retailer_info['retailer_id'];
			$credit_ledger_Data['collection_id'] = $retailer_info['collection_id'];
			
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

			## invoice sales
			$retInVoiceSales = 0;
			if($retailer_info['invoice_no'] && $retailer_info['retailer_id'])
			{
				$retailerInvoice = DB::select("SELECT * FROM tbl_order WHERE retailer_id = '".$retailer_info['retailer_id']."'
				AND order_no = '".$retailer_info['invoice_no']."'");
				
				$retInVoiceSales = $retailerInvoice[0]->grand_total_value;
			} 

			$credit_ledger_Data['retailer_invoice_sales'] = $retInVoiceSales;			
			
			##totalCollection
			$retCollect = $retailer_info['collection_amount'];
			$credit_ledger_Data['retailer_collection'] = $retCollect;
			
			
			##retailerBalance
			$remBalance = ($retOpeningBalance + $retInVoiceSales) - $retCollect;
			
			$credit_ledger_Data['retailer_balance'] = $remBalance;
			
			$credit_ledger_Data['entry_date'] = date('Y-m-d H:i:s');
			$credit_ledger_Data['entry_by'] = Auth::user()->id;
			
			
			DB::table('retailer_credit_ledger')->insert([$credit_ledger_Data]);
			
			
		}	
	}
	*/
	
	
							/* Depot Opening Balance Star */
							
	public function depo_opening_cashinhand()
    {
        $selectedMenu    = 'Depo Cashinhand';                   	// Required Variable for menu
        $selectedSubMenu = 'Cashinhand';            					// Required Variable for menu
        $pageTitle       = 'Cashinhand List';           				// Page Slug Title

		$this->depot_in_charge 	= Auth::user()->id;
		$this->user_type_id 	= Auth::user()->user_type_id; 
		
		$depotList =  DB::select("SELECT p.point_id, p.point_name, ds.opening_cash_in_hand FROM tbl_point p LEFT JOIN tbl_depot_summary ds 
							ON p.point_id = ds.point_id
							WHERE p.point_id in (SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
							");
		
		//echo '<pre/>'; print_r($resultRoute); exit;
        return view('Depot.cashinhand.depo_cash_inhand', compact('selectedMenu','selectedSubMenu','pageTitle','depotList'));
    }

	
	public function depo_opening_cashinhand_update(Request $request)
    {
		
		if($request->get('opening_cash_in_hand')!= '')
		{
			
			$depoSummData = DB::table('tbl_depot_summary')
                            ->select('point_id')
                            ->where('point_id', $request->get('point_id'))
                            ->first();
			
			
			if(sizeof($depoSummData)>0)
			{
				$DepoCashInHandUpd = DB::table('tbl_depot_summary')->where('point_id',$request->get('point_id'))->update(
					[
						'opening_cash_in_hand'  => $request->get('opening_cash_in_hand')
					]
				);
			
			} else {

					DB::table('tbl_depot_summary')->insert(
                    [
                        'point_id'           	=> $request->get('point_id'),
                        'depot_in_charge'       =>  Auth::user()->id,
                        'opening_cash_in_hand'  => $request->get('opening_cash_in_hand')
                    ]
                ); 
			}			
		
		}	   
	
		return back()->with('success','Depo Cash In Hand Update.');
	}	
							
	
	public function route_wise_retailer_list()
    {
        $selectedMenu    = 'Retailer Balance';                   // Required Variable for menu
        $selectedSubMenu = 'Balance';            				// Required Variable for menu
        $pageTitle       = 'Balance List';           		// Page Slug Title

		$this->depot_in_charge 	= Auth::user()->id; 
		$this->user_type_id 	= Auth::user()->user_type_id; 
		
		$pointList = DB::select("SELECT * FROM tbl_point WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
									
        $resultRoute = DB::table('tbl_route')
        ->select('route_id','rname')
        ->where('status', '0')
		->where('point_id', $pointList[0]->point_id)
        ->where('global_company_id', Auth::user()->global_company_id)
        ->get();
		
		//echo '<pre/>'; print_r($resultRoute); exit;


        return view('Depot.opening_balance.route_wise_retailer_list', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute'));
    }


	
	public function retailer_balance_update(Request $request)
    {
		
		$retailer_id='9901'; 
		
		$opening_balance='0';
		
		$this->retailer_ledger_adjust($retailer_id, $opening_balance);
		
		
		/*
		foreach($request->get('retailer_id') as $retKey => $retailerId) 
		{
			
			if($request->get('opening_balance')[$retKey] != '')
			{
					$RetBalanceUpd = DB::table('tbl_retailer')->where('retailer_id',$retailerId)->update(
						[
							'opening_balance'  => $request->get('opening_balance')[$retKey]
						]
					); 
					
					//$this->retailer_ledger_adjust($retailerId, $request->get('opening_balance')[$retKey]);	
			}

			if($request->get('opening_balance_accessories')[$retKey] != '')
			{
					$RetBalanceUpd = DB::table('tbl_retailer')->where('retailer_id',$retailerId)->update(
						[
							'opening_balance_accessories'  => $request->get('opening_balance_accessories')[$retKey]
						]
					); 
					
				//$this->retailer_ledger_adjust($retailerId, $request->get('opening_balance_accessories')[$retKey]);	
			}
			
					
		}
		
		*/
	
		return back()->with('success','Retailer Balance Update.');
	}

	
	public function getRetailerByRouteId(Request $request)
    {
        $routeID = $request->get('route');
		
		//dd($routeID);

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('tbl_retailer.retailer_id','tbl_retailer.name','tbl_retailer.rid','tbl_retailer.vAddress','tbl_retailer.status','tbl_retailer.opening_balance','tbl_point.business_type_id','tbl_retailer.opening_balance_accessories')
						->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_retailer.point_id')
                        ->where('tbl_retailer.status', '0')                       
                        ->where('tbl_retailer.rid', $routeID)
                        ->orderBy('tbl_retailer.name', 'ASC')
                        ->get();
		//dd($resultRetailer);
						
        return view('Depot.opening_balance.allRetailerList', compact('resultRetailer','routeID'));
    }

	
	public function get_invoice(Request $request)
    {
     	$retailer_id = $request->input('id');

		/*
        $RetInvoice=DB::table('tbl_order')
                     ->where('retailer_id',$retailer_id)
                     ->where('order_date', '>=', 'NOW() - INTERVAL 7 DAY')
                     ->where('order_date', '<', 'NOW()')
                     ->get(); */
		
		$RetInvoice=DB::select("SELECT * FROM tbl_order WHERE retailer_id = '".$retailer_id."'
								AND order_date >= NOW() - INTERVAL 7 DAY 
								AND order_date < NOW()");			 

        return view('Depot.getInvoice' , compact('RetInvoice'));
    	
    }


	public function money_recipt($collection_id)
    {
     	$selectedMenu    = 'Retailer Balance';                   // Required Variable for menu
        $selectedSubMenu = 'Balance';            				// Required Variable for menu
        $pageTitle       = 'Balance List';  

		$ReciptData = 'ReciptData';		
		
		$CollectionData = DB::select("SELECT rt.name, rt.vAddress, rcd.retailer_opening_balance, rcd.retailer_invoice_sales, 
									rcd.retailer_collection, rcd.retailer_balance, dc.*  
							FROM depot_collection dc 
							LEFT JOIN retailer_credit_ledger rcd ON dc.collection_id = rcd.collection_id
							JOIN tbl_retailer rt ON rt.retailer_id = dc.retailer_id
							WHERE dc.collection_id = '".$collection_id."'");
		
        return view('Depot.collection_recipt', compact('ReciptData','CollectionData','selectedMenu','selectedSubMenu','pageTitle'));
    	
    }


							/* Retailer/Party Wise Laser */
							
	public function retailer_laser_history(Request $req)
	{
		$selectedMenu   = 'Retailer Laser';         // Required Variable
		$pageTitle      = 'Retailer Transaction History';       // Required Variable
		
		$this->depot_in_charge 	= Auth::user()->id;
	    $this->user_type_id  	= Auth::user()->user_type_id;
		
		//$depotList = DB::table('tbl_depot_setup')->orderBy('depot_id','asc')->get();
		
		if($this->user_type_id == 1)
		{
			$routeList = DB::table('tbl_route')->orderBy('tbl_route','asc')->get();
		
		} elseif($this->user_type_id == 5) {
			
			$routeList = DB::select("SELECT * FROM tbl_route WHERE point_id in 
										(SELECT point_id FROM tbl_user_business_scope WHERE user_id = '".$this->depot_in_charge."')
									");
		}
		
		
		if ($req->get('retailer_id')) 
		{
			
			$retOpeningBalance = DB::select("SELECT opening_balance FROM tbl_retailer 
										WHERE retailer_id = '".$req->get('retailer_id')."'"); 
										
			$retCommisionVal = DB::select("SELECT SUM(memo_commission_value) tot_memo_com FROM retailer_credit_ledger 
											 WHERE retailer_id = '".$req->get('retailer_id')."'"); 							
										
			$retLaserData = DB::select("SELECT ret.name as RetName, crd.* FROM retailer_credit_ledger crd 
										JOIN tbl_retailer ret ON crd.retailer_id = ret.retailer_id
										WHERE crd.retailer_id = '".$req->get('retailer_id')."' ORDER BY crd.ledger_id ASC"); 
			
			$retGrandTotData = DB::select("SELECT SUM(retailer_invoice_sales) totSales, SUM(retailer_collection) totCollection,
											SUM(retailer_sales_return) totSalesReturn
										FROM retailer_credit_ledger WHERE retailer_id = '".$req->get('retailer_id')."'"); 
			
		} else {
		 
			$retLaserData = array();
			$retGrandTotData = array();
			$retOpeningBalance = array();
			$retCommisionVal = array();
		}
		 
		//echo '<pre/>'; print_r($depotList); exit;
		

		return view('Depot.retailer_laser_history')
		  ->with('routeList',$routeList)
		  ->with('retGrandTotData',$retGrandTotData)
		  ->with('retOpeningBalance',$retOpeningBalance)
		  ->with('retLaserData',$retLaserData)
		  ->with('retCommisionVal',$retCommisionVal)
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle);
			
	}

	
	public function get_retaier_list(Request $request)
    {
     	$route_ID = $request->input('id');

		
        $RetList = DB::table('tbl_retailer')
						->where('rid',$route_ID)
						->orderby('name','asc')
						->get(); 
		
		/*
		$RetInvoice=DB::select("SELECT * FROM tbl_order WHERE retailer_id = '".$retailer_id."'
								AND order_date >= NOW() - INTERVAL 7 DAY 
								AND order_date < NOW()"); */			 

        return view('Depot.getRetailer' , compact('RetList'));
    	
    }

	
	/* point wise adjust */
	public function point_wise_retailer_all_ledger_adjust($point_id = 0)
	{
		
		if($point_id == 0)
			$point_id = 266; //nawabpur
		
		
		if($point_id>0)
		{
			$retData = DB::select("SELECT rcd.retailer_id, ret.name, ret.opening_balance
								FROM tbl_retailer ret JOIN retailer_credit_ledger rcd ON ret.retailer_id = rcd.retailer_id
								WHERE ret.point_id = '".$point_id."' and rcd.point_id = '".$point_id."' GROUP BY rcd.retailer_id");

			if(sizeof($retData)>0)
			{
				foreach($retData as $rowRetData)
				{
				  if($rowRetData->retailer_id > 0)
				  {
					$this->retailer_ledger_adjust($rowRetData->retailer_id, $rowRetData->opening_balance);
				  }			  

				}
			}
			
		}
		
	}
	
	// Balance Adjust July-25-2018
	private function retailer_ledger_adjust($retailer_id=0, $opening_balance=0)
	{
		
		//echo '= ' . $retailer_id . $opening_balance. exit;
		
		
		//if($retailer_id>0 && $opening_balance>0)
		if($retailer_id>0)
		{
			$retLaserData = DB::select("SELECT * FROM retailer_credit_ledger WHERE retailer_id = '".$retailer_id."' order by ledger_id ASC"); 
			
			if(sizeof($retLaserData)>0)
			{
				$curLedgerId = 0;  $retailer_opening_balance = 0; $retailer_adj_opening_balance = 0;  
				$retailer_balance = 0; $retailer_adj_balance = 0; 
				
				foreach($retLaserData as $rowLaserData)
				{
					$curLedgerId = $rowLaserData->ledger_id;
					//$retailer_opening_balance = $rowLaserData->retailer_opening_balance;
					//$retailer_balance = $rowLaserData->retailer_balance;
					
					/*
					if($retailer_adj_opening_balance == 0 && $opening_balance != 0)
					{
						$retailer_adj_opening_balance = $opening_balance;
					
					} elseif($retailer_adj_opening_balance != 0) {
						$retailer_adj_opening_balance = $retailer_adj_balance;
					
					} */ 
						
					if($retailer_adj_opening_balance == 0 && $opening_balance != 0)
					{
						$retailer_adj_opening_balance = $opening_balance;
					
					} elseif($retailer_adj_balance != 0) {  // Last Balance 0 faulty Feb-20-2019
						$retailer_adj_opening_balance = $retailer_adj_balance;
					
					} 	
						
					
					if($rowLaserData->retailer_invoice_sales > 0 ) //&& $rowLaserData->retailer_invoice_sales>0)
					{
						$retailer_adj_balance = $retailer_adj_opening_balance + $rowLaserData->retailer_invoice_sales;
					
					} elseif($rowLaserData->retailer_collection>0) {  //&& $rowLaserData->retailer_collection>0) {
					
						$retailer_adj_balance = $retailer_adj_opening_balance - $rowLaserData->retailer_collection;
						
					}   elseif($rowLaserData->retailer_sales_return>0)  { //&& $rowLaserData->retailer_sales_return>0) {
					
						$retailer_adj_balance = $retailer_adj_opening_balance - $rowLaserData->retailer_sales_return;
					}	
						
					/*	
					
					} elseif($rowLaserData->memo_commission_value>0 ) { //&& $rowLaserData->memo_commission_value>0) {
					
						$retailer_adj_balance = $retailer_adj_opening_balance - $rowLaserData->memo_commission_value;
				
					}  elseif($rowLaserData->monthly_commission_value>0) { //&& $rowLaserData->monthly_commission_value>0) {
					
						$retailer_adj_balance = $retailer_adj_opening_balance - $rowLaserData->monthly_commission_value;
					
					}
					*/
					
					$RetAdjBalanceUpd = DB::table('retailer_credit_ledger')->where('ledger_id',$curLedgerId)->update(
						[
							'retailer_opening_balance'  => $retailer_adj_opening_balance,
							'retailer_balance'  => $retailer_adj_balance,
							'update_by'  => Auth::user()->id,
							'update_date'  => date('Y-m-d H:i:s')
						]
					); 
					
					
					
				}
			}
			
		}
		
		
	}
							

}  // class end

