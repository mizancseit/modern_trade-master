<?php

namespace App\Http\Controllers\depotBilling;

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

class DepotBilling extends Controller
{
	
	
    
	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
	   
    }
	
    public function billingPaymentList(Request $req)
	{
		$selectedMenu   = 'Payment List';         
		$pageTitle      = 'Payment Acknowledgement';   

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$paymentList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank, mbk.shortcode, mbk.acctshortname, ap.* 
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='NO'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
				
     						

		return view('depotBilling/billingPaymentList')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('sel_fromPaymentDat',$sel_fromPaymentDat)
		  ->with('sel_toPaymentDate',$sel_toPaymentDate)
		  ->with('sel_payment_type',$sel_payment_type)
		  ->with('sel_sap_code',$sel_sap_code)
		  ->with('sel_ssgbank_name',$sel_ssgbank_name)
		  ->with('sel_business_type',$sel_business_type)
		  ->with('paymentList',$paymentList);
			  
	}

	public function depotPaymentList(Request $req)
	{
		$selectedMenu   = 'Depot Payment Acknowledgement';         
		$pageTitle      = 'Depot Payment Acknowledgement';   

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$paymentList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank, mbk.shortcode, mbk.acctshortname, ap.* 
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='NO'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
				
     						

		return view('depotBilling.paymentList')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('sel_fromPaymentDat',$sel_fromPaymentDat)
		  ->with('sel_toPaymentDate',$sel_toPaymentDate)
		  ->with('sel_payment_type',$sel_payment_type)
		  ->with('sel_sap_code',$sel_sap_code)
		  ->with('sel_ssgbank_name',$sel_ssgbank_name)
		  ->with('sel_business_type',$sel_business_type)
		  ->with('paymentList',$paymentList);
			  
	}
	
	public function ackDepotProcess(Request $req)
	{
		
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('amtack')); exit;

			//dd($req->all());
			
			foreach($req->input('reqid') as $rowReqId)
			{
				
				$ordackVal = $req->input('amtack');
				$comment=$req->input('ack_remarks');
				$trans_id=$req->input('tran_id');
				
				$inp_net_amount  = $req->input('net_amount');
				$inp_bank_charge = $req->input('bank_charge');
				
				$user_id=Auth::user()->id;
				if(isset($ordackVal[$rowReqId]))
				{
					$status=$ordackVal[$rowReqId];
					
					$remarks=$comment[$rowReqId];
					$date1=date('Y-m-d H:i:s');
					$trans_no=$trans_id[$rowReqId];
					
					$net_amount = $inp_net_amount[$rowReqId];
					$bank_charge = $inp_bank_charge[$rowReqId];
                    
					//dd($trans_no);
					$ack=DB::update("update depot_accounts_payments set ack_status='$status', ack_by=$user_id, ack_date='$date1',
								ack_remarks='$remarks', net_amount='$net_amount', bank_charge='$bank_charge' 
								where transaction_id=$trans_no");
				}
				
			
			}
			
		}
		
		return Redirect::to('/depotPaymentList')->with('success', 'Successfully Payment Acknowledged.');
	}

	
	public function ackDepotList(Request $req)
	{

		$selectedMenu   = 'Depot Acknowledgement List';         
		$pageTitle      = 'Depot Acknowledgement List';   

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$paymentList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank,  mbk.shortcode, mbk.acctshortname, ap.* 
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='YES'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
		
		/*
		echo "SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank, ap.* 
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='YES' 
								$whereCond";						
		exit;
		*/
		
     						

		return view('depotBilling.ackList')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('sel_fromPaymentDat',$sel_fromPaymentDat)
		  ->with('sel_toPaymentDate',$sel_toPaymentDate)
		  ->with('sel_payment_type',$sel_payment_type)
		  ->with('sel_sap_code',$sel_sap_code)
		  ->with('sel_ssgbank_name',$sel_ssgbank_name)
		  ->with('sel_business_type',$sel_business_type)
		  ->with('paymentList',$paymentList);		
		
		/*
		return view('depotBilling.ackList')
			  ->with('selectedMenu',$selectedMenu) 
			  ->with('pageTitle',$pageTitle)
			  ->with('ackList',$ackList);
			  
		*/	  

	}
	

	public function depo_payment_undo ($transaction_id)
	{
		$paymentUndo = DB::table('depot_accounts_payments')->where('transaction_id',$transaction_id)->update(
                    [
                        'ack_status' 			   =>'NO', 
                        'ack_by'				   =>'', 
                        'ack_date'				   =>'',
						'ack_remarks'			   =>'', 
						'net_amount' 			   =>'0', 
						'bank_charge'			   =>'0'
                    ]
                );
		if($paymentUndo){
		 return Redirect::to('/depotAckList')->with('success', 'Successfully undo payment.');
		}else{
			return Redirect::to('/depotAckList')->with('unsuccess', 'Unsuccessfully undo payment.');
		}
	}
	
	public function download_ackDepotList(Request $req)
	{

	
		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND ap.trans_date <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$paymentList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank,  mbk.shortcode, mbk.acctshortname, ap.* 
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='YES'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
		
		$data = array();
		foreach ($paymentList as $items) {
			
			$rowExcelHead['Date'] = date('d-m-Y',strtotime($items->trans_date));
			$rowExcelHead['Sap_Code'] = $items->sap_code;
			$rowExcelHead['Division'] = $items->div_name;
			$rowExcelHead['Point'] = $items->point_name;
			$rowExcelHead['Customar'] = $items->display_name;
			$rowExcelHead['Bank'] = $items->shortcode . '::' . $items->acctshortname . '::' . $items->ssg_bank;
			$rowExcelHead['Deposit_Branch'] =  $items->branch_name;
			$rowExcelHead['Ref_No'] = $items->ref_no;
			$rowExcelHead['Method'] = $items->payment_type;
			$rowExcelHead['Cheque_Date'] = $items->cheque_date;
			$rowExcelHead['Deposit_Amount'] = $items->trans_amount;
			$rowExcelHead['Remarks'] = $items->payment_remarks;
			$rowExcelHead['Status'] = $items->ack_status;
			$rowExcelHead['Actual_Amount'] = $items->net_amount;
			$rowExcelHead['Bank_Charge'] = $items->bank_charge;
			$rowExcelHead['Ack_Remarks'] = $items->ack_remarks;
			$rowExcelHead['Upadated_By'] = 'Finance Dept';
			
			$data[] = (array)$rowExcelHead;  
		} 
		
		Excel::create('Download_Payment_Acknowledge', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');

	}
	
	
	public function depotPaymentAcknowledgeList(Request $req)
	{
		
		$selectedMenu   = 'Depot Payment Acknowledge List';         
		$pageTitle      = 'Depot Payment Acknowledge List';   

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.ack_date,'%Y-%m-%d') >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.ack_date,'%Y-%m-%d') <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$paymentList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank,  mbk.shortcode, mbk.acctshortname, ap.*  
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='YES'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
				
     						

		return view('depotBilling.paymentVerify')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('sel_fromPaymentDat',$sel_fromPaymentDat)
		  ->with('sel_toPaymentDate',$sel_toPaymentDate)
		  ->with('sel_payment_type',$sel_payment_type)
		  ->with('sel_sap_code',$sel_sap_code)
		  ->with('sel_ssgbank_name',$sel_ssgbank_name)
		  ->with('sel_business_type',$sel_business_type)
		  ->with('paymentList',$paymentList);	
			  
	}	
	
	
	public function verifyDepotProcess(Request $req)
	{
		
		if($req->isMethod('post'))
		{
			//echo '<pre/>'; print_r($req->input('reqid')); exit;
			//dd($req->all());
	
			foreach($req->input('reqid') as $rowReqId)
			{
				
				$ordackVal = $req->input('amtack');
				$comment=$req->input('ack_remarks');
				$trans_id=$req->input('tran_id');
				
				$user_id=Auth::user()->id;
				if(isset($ordackVal[$rowReqId]))
				{
					$status=$ordackVal[$rowReqId];
					
					$remarks=$comment[$rowReqId];
					$date1=date('Y-m-d H:i:s');
					$trans_no=$trans_id[$rowReqId];
                    
					//dd($trans_no);
					$ack=DB::update("update depot_accounts_payments set ack_status='".$status."',confirmed_by='".$user_id."',confirmed_date='".$date1."',verify_remarks='".$remarks."' 
					where transaction_id='".$trans_no."'");
					
					
					$data_payment = DB::table('depot_accounts_payments')->where('transaction_id',$trans_no)->first();
					self::depot_cashbook_bank_deposit($data_payment->point_id, $trans_no, $data_payment->trans_amount, Auth::user()->id);
					
					
					//PartyLedger for Collection	
					$this->party_payment($trans_no);
				
				}
				
				
				
			
			}
			
		}
		
		return Redirect::to('/depotVerifiedList')->with('success', 'Successfully Payment Verified.');
	}
	
	
	public function depotVerifiedList(Request $req)
	{

		$selectedMenu   = 'Depot Verified List';         
		$pageTitle      = 'Depot Verified List';   

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.confirmed_date,'%Y-%m-%d') >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.confirmed_date,'%Y-%m-%d') <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$verifiedList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank,  mbk.shortcode, mbk.acctshortname, ap.*  
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='CONFIRMED'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
		
		
		return view('depotBilling.verifiedList')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('sel_fromPaymentDat',$sel_fromPaymentDat)
		  ->with('sel_toPaymentDate',$sel_toPaymentDate)
		  ->with('sel_payment_type',$sel_payment_type)
		  ->with('sel_sap_code',$sel_sap_code)
		  ->with('sel_ssgbank_name',$sel_ssgbank_name)
		  ->with('sel_business_type',$sel_business_type)
		  ->with('paymentList',$verifiedList);	
		
		/*
		return view('depotBilling.verifiedList')
			  ->with('selectedMenu',$selectedMenu) 
			  ->with('pageTitle',$pageTitle)
			  ->with('verifiedList',$verifiedList);
		*/	  

	}
	
	
	public function download_depotVerifiedList(Request $req)
	{

		$whereCond = '';
		$sel_fromPaymentDat = '';
		if ($req->get('fromPaymentDate') != '') 
		{
			$sel_fromPaymentDat = $req->get('fromPaymentDate');
			$fromPaymentDate = explode('-',$req->get('fromPaymentDate'));
			$fromDate = $fromPaymentDate[2] . '-' . $fromPaymentDate[1] . '-' . $fromPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.confirmed_date,'%Y-%m-%d') >= '".$fromDate."' ";
		}
		
		$sel_toPaymentDate = '';
		if ($req->get('toPaymentDate') != '') 
		{
			$sel_toPaymentDate = $req->get('toPaymentDate');
			$toPaymentDate = explode('-',$req->get('toPaymentDate'));
			$toDate = $toPaymentDate[2] . '-' . $toPaymentDate[1] . '-' . $toPaymentDate[0]; 
			$whereCond .= " AND date_format(ap.confirmed_date,'%Y-%m-%d') <= '".$toDate."' ";
		}
		
		$sel_payment_type = '';
		if ($req->get('payment_type') != '') 
		{
			$sel_payment_type = $req->get('payment_type');
			$whereCond .= " AND ap.payment_type = '".$req->get('payment_type')."' ";
		}
		
		/*
		if ($req->get('bank_name') != '') 
		{
			$whereCond .= " AND depot_accounts_payments.bank_name = '".$req->get('bank_name')."' ";
		}
		*/
		
		$sel_sap_code = '';
		if ($req->get('sap_code') != '') 
		{
			$sel_sap_code = $req->get('sap_code');
			$whereCond .= " AND u.sap_code = '".$req->get('sap_code')."'";
		}
		
		$sel_ssgbank_name = '';
		if ($req->get('ssgbank_name') != '') 
		{
			$sel_ssgbank_name = $req->get('ssgbank_name');
			$whereCond .= " AND ap.bank_info_id = '".$req->get('ssgbank_name')."'";
		}
		
		
		$sel_business_type = '';
		if ($req->get('business_type') != '') 
		{
			$sel_business_type = $req->get('business_type');
			$whereCond .= " AND p.business_type_id = '".$req->get('business_type')."' ";
		}
		
		//echo $whereCond; exit;
		
		$verifiedList=DB::select("SELECT p.point_id, p.point_name, d.div_id, d.div_name, p.point_division, u.sap_code, 
								u.display_name, mbk.bank_name as ssg_bank,  mbk.shortcode, mbk.acctshortname, ap.*  
								FROM tbl_point p JOIN  depot_accounts_payments ap ON p.point_id = ap.point_id 
								JOIN tbl_division d ON d.div_id =  p.point_division 
								JOIN tbl_user_business_scope ubs ON ubs.point_id = p.point_id
								LEFT JOIN tbl_master_bank mbk ON mbk.id = ap.bank_info_id
								JOIN users u ON u.id = ubs.user_id
								WHERE u.user_type_id = 5 AND ap.ack_status='CONFIRMED'
								AND ap.transaction_type = 'credit'								
								$whereCond
								ORDER BY ap.transaction_id DESC");   
		
		$data = array();
		foreach ($verifiedList as $items) {
			
			$rowExcelHead['Date'] = date('d-m-Y',strtotime($items->trans_date));
			$rowExcelHead['Sap_Code'] = $items->sap_code;
			$rowExcelHead['Division'] = $items->div_name;
			$rowExcelHead['Point'] = $items->point_name;
			$rowExcelHead['Customar'] = $items->display_name;
			$rowExcelHead['Bank'] = $items->shortcode . '::' . $items->acctshortname . '::' . $items->ssg_bank;
			$rowExcelHead['Deposit_Branch'] =  $items->branch_name;
			$rowExcelHead['Ref_No'] = $items->ref_no;
			$rowExcelHead['Method'] = $items->payment_type;
			$rowExcelHead['Cheque_Date'] = $items->cheque_date;
			$rowExcelHead['Deposit_Amount'] = $items->trans_amount;
			$rowExcelHead['Remarks'] = $items->payment_remarks;
			$rowExcelHead['Status'] = $items->ack_status;
			$rowExcelHead['Actual_Amount'] = $items->net_amount;
			$rowExcelHead['Bank_Charge'] = $items->bank_charge;
			$rowExcelHead['Ack_Remarks'] = $items->ack_remarks;
			$rowExcelHead['Upadated_By'] = 'Finance Dept';
			
			$data[] = (array)$rowExcelHead;  
		} 
		
		Excel::create('Download_Payment_verified', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');
		 

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
	
	
	private function party_payment($payment_id)
	{
		if($payment_id)
		{
			
			$partyPaymentData= DB::select("SELECT pamnts.*, u.sap_code, p.is_depot
										FROM depot_accounts_payments pamnts JOIN users u ON u.id = pamnts.depot_in_charge
										JOIN tbl_point p ON p.point_id = pamnts.point_id
										WHERE transaction_id = '".$payment_id."'");	
			
			if(sizeof($partyPaymentData)>0)
			{
				$partyLedgerData= DB::select("SELECT pldg.*
										FROM tbl_party_ledger pldg 
										WHERE 
										pldg.party_id = '".$partyPaymentData[0]->depot_in_charge."'
										AND pldg.point_id = '".$partyPaymentData[0]->point_id."'
										AND date_format(pldg.ledger_date_time,'%Y-%m-%d') = '".date('Y-m-d')."'");
										
										
				if(sizeof($partyLedgerData)>0) // Entry exist
				{
					
					$collectionTotal = $partyLedgerData[0]->party_collection_total + $partyPaymentData[0]->trans_amount;
					$closing_balance_total = $partyLedgerData[0]->closing_balance_total - $partyPaymentData[0]->trans_amount;
					$actual_closing_balance = $partyLedgerData[0]->actual_closing_balance - $partyPaymentData[0]->trans_amount;
					
					$partyLedgerUpd = DB::table('tbl_party_ledger')->where('ledger_id',$partyLedgerData[0]->ledger_id)->update(
						[
						   
							'ledger_date_time'         => date('Y-m-d'),
							'party_collection_total'   => $collectionTotal,
							'closing_balance_total'    => $closing_balance_total,		
							'actual_closing_balance'   => $actual_closing_balance,
							'today_collection_count'   => $partyLedgerData[0]->today_collection_count + 1,
						]
					); 
					
				} else { // New Ledger 

					$party_ledger_data = array();
					$party_ledger_data['ledger_date_time'] =  date('Y-m-d H:i:s');
					$party_ledger_data['party_id'] =  $partyPaymentData[0]->depot_in_charge;
					$party_ledger_data['party_sap_code'] =  $partyPaymentData[0]->sap_code;
					$party_ledger_data['point_id'] = $partyPaymentData[0]->point_id;
					$party_ledger_data['party_type'] =  ($partyPaymentData[0]->is_depot==1)?'depot':'dist';
					
					$party_ledger_data['party_opening_balance'] =  $this->GetPartyOpeningBalance($partyPaymentData[0]->depot_in_charge,
																								$partyPaymentData[0]->point_id);
					$party_ledger_data['party_collection_total'] 	=  $partyPaymentData[0]->trans_amount;
				
					$party_ledger_data['closing_balance_total'] =  $party_ledger_data['party_opening_balance'] 
																	- $party_ledger_data['party_collection_total'];
																	
					$party_ledger_data['actual_closing_balance'] =  $party_ledger_data['closing_balance_total'];
					
					$party_ledger_data['today_collection_count'] = 1;
			
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
		

}  

