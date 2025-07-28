<?php

namespace App\Http\Controllers\distBilling;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
/* Load Model Reference Begin */
/* Load Model Reference End */
use Hash;

use DB;
use Auth;
use Session;

class DistriBilling extends Controller
{
	
	
    
	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
	   
    }
	
	public function paymentList()
	{
		$selectedMenu   = 'Payment Acknowledgement';         
		$pageTitle      = 'Payment Acknowledgement';   

		$paymentList=DB::select("select 
				tbl_point.point_id,tbl_point.point_name,tbl_division.div_id,tbl_division.div_name,
				tbl_point.point_division,distributor_accounts_payments.*
				from tbl_point, distributor_accounts_payments,tbl_division
				where tbl_point.point_id=distributor_accounts_payments.point_id
				and tbl_point.point_division=tbl_division.div_id and ack_status='NO' order by trans_date desc");    

		  return view('distriBilling.paymentList')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('paymentList',$paymentList);
			  
	}		
	
	
	public function ackDistriProcess(Request $req)
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
					$status=$ordackVal[$rowReqId];}
				else
				{
					$status='NO';
				}
				
				$remarks=$comment[$rowReqId];
				$date1=date('Y-m-d H:i:s');
				$trans_no=$trans_id[$rowReqId];
                    
					//dd($trans_no);
				$ack=DB::update("update distributor_accounts_payments set ack_status='$status',ack_by=$user_id,ack_date='$date1',ack_remarks='$remarks' where transaction_id=$trans_no");
			
			}
			
		}
		
		return Redirect::to('/distPaymentList')->with('success', 'Successfully Payment Acknowledged.');
	}

	
	public function ackList(Request $req)
	{

		$selectedMenu   = 'Acknowledgement List';         
		$pageTitle      = 'Acknowledgement List';   

		$ackList=DB::select("select * from distributor_accounts_payments 
					where ack_status='YES'
					order by ack_date DESC");
		
		return view('distriBilling.ackList')
			  ->with('selectedMenu',$selectedMenu) 
			  ->with('pageTitle',$pageTitle)
			  ->with('ackList',$ackList);

	}
	
	
	public function paymentAcknowledgeList()
	{
		$selectedMenu   = 'Payment Acknowledge List';         
		$pageTitle      = 'Payment Acknowledge List';   

		$paymentAckList=DB::select("select 
				tbl_point.point_id,tbl_point.point_name,tbl_division.div_id,tbl_division.div_name,
				tbl_point.point_division,distributor_accounts_payments.*
				from tbl_point, distributor_accounts_payments,tbl_division
				where tbl_point.point_id=distributor_accounts_payments.point_id
				and tbl_point.point_division=tbl_division.div_id and ack_status='YES' order by trans_date desc");    

		  return view('distriBilling.paymentVerify')
		  ->with('selectedMenu',$selectedMenu)
		  ->with('pageTitle',$pageTitle)
		  ->with('paymentAckList',$paymentAckList);
			  
	}	
	
	
	public function verifyDistriProcess(Request $req)
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
					$status=$ordackVal[$rowReqId];}
				else
				{
					$status='NO';
				}
				
				$remarks=$comment[$rowReqId];
				$date1=date('Y-m-d H:i:s');
				$trans_no=$trans_id[$rowReqId];
                    
					//dd($trans_no);
				$ack=DB::update("update distributor_accounts_payments set ack_status='".$status."',confirmed_by='".$user_id."',confirmed_date='".$date1."',verify_remarks='".$remarks."' 
				where transaction_id='".$trans_no."'");
			
			}
			
		}
		
		return Redirect::to('/verifiedList')->with('success', 'Successfully Payment Verified.');
	}
	
	
	public function verifiedList(Request $req)
	{

		$selectedMenu   = 'Verified List';         
		$pageTitle      = 'Verified List';   

		$verifiedList=DB::select("select * from distributor_accounts_payments 
					where ack_status='CONFIRMED'
					order by ack_date DESC");
		
		return view('distriBilling.verifiedList')
			  ->with('selectedMenu',$selectedMenu) 
			  ->with('pageTitle',$pageTitle)
			  ->with('verifiedList',$verifiedList);

	}
		

}  

