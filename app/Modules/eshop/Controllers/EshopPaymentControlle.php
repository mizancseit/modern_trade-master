<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
use Validator;
use Excel;
class EshopPaymentControlle extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function eshop_opening_route()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

         $routeResult = DB::table('eshop_route')
                        ->where('status',0)
                        ->get();
      

        return view('eshop::sales/payment/routeManage', compact('selectedMenu','pageTitle','routeResult'));
    }

     public function eshop_opening_outlet(Request $request)
    {

        $routeid = $request->get('route');


        $ledgerCheckParty = DB::table('eshop_outlet_ledger')
                        ->groupBy('customer_id')                    
                        ->get();

        $customer_id = array();

         foreach($ledgerCheckParty as $checkParty) {
                    $customer_id[]= $checkParty->customer_id;
                }

        $resultParty = DB::table('eshop_customer_list')
                        ->select('customer_id','name','route_id','owner','address','opening_balance','sap_code','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('route_id', $routeid)
                        ->whereNotIn('customer_id', $customer_id)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      //dd($customer_id);

        return view('eshop::sales/payment/outletList', compact('resultParty','routeid'));
    }


    public function eshop_add_opening_balance(Request $request){

    	$countRows = count($request->get('opening_balance'));
    	

    	for($m=0;$m<$countRows;$m++)
            {
            	

                if($request->get('opening_balance')[$m]!='')
                {
                	
                    DB::table('eshop_customer_list')->where('customer_id',$request->get('customer_id')[$m])->update(
                        [
                            'opening_balance'   => $request->get('opening_balance')[$m]
                        ]
                    );

                     DB::table('eshop_outlet_ledger')->insert(
                        [
                            'ledger_date'        	=> date('Y-m-d h:i:s'),
                            'customer_id'          	=> $request->get('customer_id')[$m],
                            'party_sap_code'     	=> $request->get('sap_code')[$m],
                            'opening_balance'     	=> $request->get('opening_balance')[$m],
                            'debit'     			=> $request->get('opening_balance')[$m],
                            'credit'     			=> 0,
                            'closing_balance'     	=> $request->get('opening_balance')[$m],
                            'trans_type'            => 'opening',
                            'entry_by'           	=> Auth::user()->id,
                            'entry_date'         	=> date('Y-m-d h:i:s')

                        ]
                    );

                }

            }

            return Redirect::to('/eshop-opening-route')->with('success', 'Successfully Added Add To Cart.');
    }


    public function credit_adjustment()
    {
        $selectedMenu   = 'Credit Adjustment';         // Required Variable
        $pageTitle      = 'Adjustment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

            $outletList = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();

        $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

        $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('eshop_outlet_payments.trans_type','adjustment')
                       ->where('eshop_outlet_payments.entry_by',Auth::user()->id)        
                        ->get();
 

        return view('eshop::payment/credit_adjustment')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle)
        ->with('bankList',$bankList);

    }

    public function credit_adjustment_list(Request $request)
    {
       $selectedMenu   = 'Credit Adjustment';         // Required Variable
        $pageTitle      = 'Adjustment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }
        
        if($fromdate!='' && $todate!='')
        {
  
            $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

            $outletPayment= DB::table('eshop_outlet_payments')
                           ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                           ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                           ->where('eshop_outlet_payments.trans_type','adjustment')
                           ->where('eshop_outlet_payments.entry_by',Auth::user()->id) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

           // dd($outletPayment);
        }

        return view('eshop::payment/credit_adjustment_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function credit_adjustment_process(Request $req)
    {
        if ($req->isMethod('post')) 
        {
            $selectedMenu   = 'Outlet pay';         // Required Variable
            $pageTitle      = 'Payment List';       // Required Variable 
            $payment_type   =   $req->input('payment_type');
            $customer_info  =   $req->input('party_id');
            $customer       = explode('-',$customer_info);
            $party_id       = $customer[0];
            $sap_code       = $customer[1];

            $autoNo = rand(100,1000);
           
            $payment_no = 'AE'.'-'.$sap_code.'-'.date('dmy').'-'.$autoNo;

            //dd($payment_no);
            
            if($payment_type == 'CHEQUE' || $payment_type=='ON-LINE')
            {

                if($payment_type=="CHEQUE")
                {   $bank_info  =   $req->input('bank_name');
                    $bank_info_list   = explode('-',$bank_info);
                    $bank_id       = $bank_info_list[0];
                    $bank_name       = $bank_info_list[1];
                    $bank_ac_no       = $bank_info_list[2];
                    //$bank_name      =   $req->input('bank_name');
                    $branch_name    =   $req->input('branch_name');
                    $cheque_date    =   date_create($req->input('cheque_date'));
                    $cheque_date    =   date_format($cheque_date,"Y-m-d");
                }

                if($payment_type=="ON-LINE")
                {   
                    $bank_name      =   $req->input('ssgbank_name'); 
                    $bank_id       = ''; 
                    $bank_ac_no       = '';
                    $branch_name    =   '';
                    $cheque_date    =   '';
                    $cheque_date    =   '';
                }
                


            } else {

                $bank_name      =   '';
                $branch_name    =   '';
                $acc_no         =   '';
                $cheque_no      =   '';
                $cheque_date    =   '';
                $bank_id       = ''; 
                $bank_ac_no       = '';
            }
            
            $payment_remarks    =   $req->input('payment_remarks');
            $trans_amount       =   $req->input('trans_amount');
            $adjust_amount       =   $req->input('adjust_amount');
            

            $trans_date     =   date('Y-m-d h:i:s'); // system date
            
            
            $ref_no =   $req->input('ref_no');

            $user_id        =   Auth::user()->id;
            $entryDate = date('Y-m-d h:i:s');

            $this->user_type_id     = Auth::user()->user_type_id;


            $this->outlet_in_charge  = Auth::user()->id;

            // image upload 

                $main_image_file = '';
                if($req->file('user_photo')!='')
                {
                    $validator = Validator::make($req->all(), [
                    'user_photo' => 'required|max:10000|mimes:jpg,jpeg,png,gif',
                    ]);

                    if ($validator->fails()) {
                        return Redirect::back()
                                    ->withErrors($validator)
                                    ->withInput($req->all);
                    }

                    $photo = $req->file('user_photo');
                    $fath1 = 'uploads/modernPayment';
                    $main_image_file = 'payment'.'_'.$party_id.'_'.time().'.'.$photo->getClientOriginalExtension();
                    $success = $photo->move($fath1, $main_image_file);
                } 

            if($payment_type=="CHEQUE")
            {
 

                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'bank_info_id'          => $bank_id,
                        'bank_name'             => $bank_name,
                        'acc_no'                => $bank_ac_no,
                        'branch_name'           => $branch_name,
                        'cheque_date'           => $cheque_date,
                        'ref_no'                => $ref_no,
                        'payment_amount'        => $trans_amount,
                        'adjust_amount'         => $adjust_amount,
                        'trans_type'            => 'adjustment',
                        'trans_date'            => $trans_date,
                        'upload_image'          => $main_image_file,
                        'entry_by'              => $this->outlet_in_charge,
                        'entry_date'            => $entryDate,
                        'payment_remarks'       => $payment_remarks,
                    ]
                );

            } else if($payment_type=="ON-LINE"){


                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'bank_info_id'          => $bank_name,
                        'ref_no'                => $ref_no,
                        'payment_amount'        => $trans_amount,
                        'adjust_amount'         => $adjust_amount,
                        'trans_type'             => 'adjustment',
                        'trans_date'            => $trans_date,
                        'upload_image'          => $main_image_file,
                        'entry_by'              => $this->outlet_in_charge,
                        'entry_date'            => $entryDate,
                        'payment_remarks'       => $payment_remarks,
                    ]
                );



            } else {

                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'            => $party_id,
                        'payment_no'             => $payment_no,
                        'outlet_in_charge'       => $this->outlet_in_charge,
                        'payment_type'           => $payment_type,
                        'ref_no'                 => $ref_no,
                        'payment_amount'         => $trans_amount,
                        'adjust_amount'          => $adjust_amount,
                        'trans_type'             => 'adjustment',
                        'trans_date'             => $trans_date,
                        'upload_image'           => $main_image_file,
                        'entry_by'               => $this->outlet_in_charge,
                        'entry_date'             => $entryDate,
                        'payment_remarks'        => $payment_remarks,
                    ]
                );



            }
            

            return Redirect::to('/eshop-credit-adjustment')->with('success', 'Successfully Adjustment Added.');

        }

    }

    // Md. Sazzadul islam 
    public function outlet_payments()
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

        $outletList = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();

      $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();
        $ssgbankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

        $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('eshop_outlet_payments.trans_type','payment')
                       ->where('eshop_outlet_payments.entry_by',Auth::user()->id)
                       ->orderBy('eshop_outlet_payments.transaction_id','DESC')        
                        ->get();

       // dd($outletPayment);
 

        return view('eshop::payment/outlet_credit')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('bankList',$bankList)
        ->with('ssgbankList',$ssgbankList)
        ->with('pageTitle',$pageTitle);

    }

    public function outlet_payment_list(Request $request)
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

       
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }
        
        if($fromdate!='' && $todate!='')
        {
  
            $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('eshop_outlet_payments.trans_type','payment')
                           ->where('eshop_outlet_payments.entry_by',Auth::user()->id)
                           ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                           ->orderBy('eshop_outlet_payments.transaction_id','DESC')         
                            ->get();
        }

        return view('eshop::payment/outlet_credit_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function outlet_paymnet_process(Request $req)
    {
        if ($req->isMethod('post')) 
        {
            $selectedMenu   = 'Outlet pay';         // Required Variable
            $pageTitle      = 'Payment List';       // Required Variable 
            $payment_type   =   $req->input('payment_type');
            $customer_info  =   $req->input('party_id');
            $outlet_id      =   $req->input('outlet_id');
            $customer       = explode('-',$customer_info);
            $party_id       = $customer[0];
            $sap_code       = $customer[1];

           

            $autoNo = rand(100,1000);
           
            $payment_no = 'PE'.'-'.$sap_code.'-'.date('dmy').'-'.$autoNo;

            $supervisorList = DB::table('eshop_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();
            
            if($payment_type == 'CHEQUE' || $payment_type=='ON-LINE' || $payment_type=="PAY-ORDER" || $payment_type=="DD" || $payment_type=="TT")
            {

                if($payment_type=="CHEQUE" || $payment_type=="PAY-ORDER" || $payment_type=="DD" || $payment_type=="TT")
                {   

                    $bank_info  =   $req->input('bank_name');
                    $bank_info_list   = explode('-',$bank_info);
                    $bank_id       = $bank_info_list[0];
                    $bank_name       = $bank_info_list[1];
                    $bank_ac_no       = $bank_info_list[2];
                    //$bank_name      =   $req->input('bank_name');
                    $branch_name    =   $req->input('branch_name');
                    $cheque_date    =   date_create($req->input('cheque_date'));
                    $cheque_date    =   date_format($cheque_date,"Y-m-d");
                }

                if($payment_type=="ON-LINE")
                {   
                    $bank_name      =   $req->input('ssgbank_name');
                    $branch_name    =   '';
                    $cheque_date    =   '';
                    $cheque_date    =   '';
                    $bank_ac_no     = '';
                }
                


            } else {
                $bank_ac_no     = '';
                $bank_name      =   '';
                $branch_name    =   '';
                $acc_no         =   '';
                $cheque_no      =   '';
                $cheque_date    =   '';
            }
            
            $payment_remarks    =   $req->input('payment_remarks');
            $trans_amount       =   $req->input('trans_amount');
            $adjust_amount       =   $req->input('adjust_amount'); 
            $trans_date     =   date('Y-m-d h:i:s'); // system date 
            $ref_no =   $req->input('ref_no');

            $user_id        =   Auth::user()->id;
            $entryDate = date('Y-m-d h:i:s');

            $this->user_type_id     = Auth::user()->user_type_id;


            $this->outlet_in_charge  = Auth::user()->id;

            if($payment_type=="CHEQUE" || $payment_type=="PAY-ORDER" || $payment_type=="DD" || $payment_type=="TT")
            {
                // image upload 

                $main_image_file = '';
                if($req->file('user_photo')!='')
                {
                    $validator = Validator::make($req->all(), [
                    'user_photo' => 'required|max:10000|mimes:jpg,jpeg,png,gif',
                    ]);

                    if ($validator->fails()) {
                        return Redirect::back()
                                    ->withErrors($validator)
                                    ->withInput($req->all);
                    }

                    $photo = $req->file('user_photo');
                    $fath1 = 'uploads/modernPayment';
                    $main_image_file = 'payment'.'_'.$party_id.'_'.time().'.'.$photo->getClientOriginalExtension();
                    $success = $photo->move($fath1, $main_image_file);
                } 

                //dd($req->file('user_photo'));

                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'outlet_id'             => $outlet_id,
                        'management_id'         => $supervisorList->management_id,
                        'manager_id'            => $supervisorList->manager_id,
                        'executive_id'          => $supervisorList->executive_id,
                        'officer_id'            => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'bank_info_id'          => $bank_id,
                        'bank_name'             => $bank_name,
                        'acc_no'                => $bank_ac_no,
                        'branch_name'           => $branch_name,
                        'cheque_date'           => $cheque_date,
                        'ref_no'                => $ref_no,
                        'payment_amount'        => $trans_amount,
                        'adjust_amount'         => $adjust_amount,
                        'trans_date'            => $trans_date,
                        'upload_image'          => $main_image_file,
                        'entry_by'              => $this->outlet_in_charge,
                        'entry_date'            => $entryDate,
                        'payment_remarks'       => $payment_remarks,
                    ]
                );

            } else if($payment_type=="ON-LINE"){


                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'outlet_id'             => $outlet_id,
                        'management_id'         => $supervisorList->management_id,
                        'manager_id'            => $supervisorList->manager_id,
                        'executive_id'          => $supervisorList->executive_id,
                        'officer_id'            => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'bank_info_id'          => $bank_name,
                        'ref_no'                => $ref_no,
                        'payment_amount'        => $trans_amount,
                        'adjust_amount'         => $adjust_amount,
                        'trans_date'            => $trans_date,
                        'entry_by'              => $this->outlet_in_charge,
                        'entry_date'            => $entryDate,
                        'payment_remarks'       => $payment_remarks,
                    ]
                );



            } else {


                DB::table('eshop_outlet_payments')->insert(
                    [
                        'customer_id'            => $party_id,
                        'outlet_id'              => $outlet_id,
                        'management_id'          => $supervisorList->management_id,
                        'manager_id'             => $supervisorList->manager_id,
                        'executive_id'           => $supervisorList->executive_id,
                        'officer_id'             => Auth::user()->id,
                        'payment_no'             => $payment_no,
                        'outlet_in_charge'       => $this->outlet_in_charge,
                        'payment_type'           => $payment_type,
                        'ref_no'                 => $ref_no,
                        'payment_amount'         => $trans_amount,
                        'adjust_amount'          => $adjust_amount,
                        'trans_date'             => $trans_date,
                        'entry_by'               => $this->outlet_in_charge,
                        'entry_date'             => $entryDate,
                        'payment_remarks'        => $payment_remarks,
                    ]
                );



            }
            

            return Redirect::to('/eshop_outlet_payments')->with('success', 'Successfully Payment Added.');

        }

    }

    public function eshop_outlet_payments_delete($paymentid,$type)
    {

        DB::table('eshop_outlet_payments')
        ->where('ack_status', 'NO')                        
        ->where('entry_by',Auth::user()->id)                        
        ->where('transaction_id',$paymentid)                  
        ->delete(); 

        if($type==1){
             return Redirect::to('/eshop_outlet_payments')->with('success', 'Successfully Payment Delete!');
        }else{
             return Redirect::to('/eshop-credit-adjustment')->with('success', 'Successfully Adjustment Delete!');
        }

       
    }

    public function admin_payments_con()
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

        
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get(); 

        $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('eshop_outlet_payments.ack_status','NO')       
                        ->get();

        $managementlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();


        return view('eshop::payment/admin/outlet_credit', compact('selectedMenu','pageTitle','managementlist','outletList','outletPayment','outlet_name','officerlist'));

       

    }
    

    public function admin_payments_con_list(Request $request)
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable 

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive  = $request->get('executive_id');
        $officer    = $request->get('fos');
        $customer_id   = $request->get('customer_id') ? $request->get('customer_id') : NULL; 
        $payment_type  = $request->get('payment_type') ? $request->get('payment_type') : NULL; 

        $outletPayment= DB::table('eshop_outlet_payments')
                        ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                        ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                        ->where('eshop_outlet_payments.ack_status','NO') 
                        ->when($executive, function ($query, $executive) {
                            return $query->where('eshop_outlet_payments.executive_id', $executive);
                        })
                        ->when($officer, function ($query, $officer) {
                            return $query->where('eshop_outlet_payments.officer_id', $officer);
                        }) 
                        ->when($customer_id, function ($query, $customer_id) {
                            return $query->where('eshop_outlet_payments.customer_id', $customer_id);
                        }) 
                        ->when($payment_type, function ($query, $payment_type) {
                            return $query->where('eshop_outlet_payments.trans_type', $payment_type);
                        }) 
                        ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                        ->get(); 

        return view('eshop::payment/admin/outlet_credit_list',compact('outletList','outletPayment','pageTitle','outlet_name','customer','selectedMenu'));

    }


    public function eshop_admin_payment()
    {
        $selectedMenu   = 'Customer payment';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        

        $customer = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

        $outletPayment= DB::table('eshop_customer_list')
                       ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
                       ->where('eshop_outlet_payments.executive_id',Auth::user()->id)
                       ->where('eshop_outlet_payments.ack_status','NO')       
                        ->get();

        $managementlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.management_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.management_id')
          ->get();

          $executivelist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.executive_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('eshop_role_hierarchy')
          ->join('users', 'users.id', '=', 'eshop_role_hierarchy.officer_id')     
          ->where('eshop_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('eshop_role_hierarchy.officer_id')
          ->get();

        return view('eshop::payment/admin/payment_report',compact('customer','outletPayment','selectedMenu','pageTitle','managementlist','officerlist','executivelist'));

    }
    

    public function eshop_admin_payment_list(Request $request)
    {
        $selectedMenu   = 'Customer payment';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $fromdate       = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate         = date('Y-m-d', strtotime($request->get('todate'))); 
        $officer    = $request->get('fos');
        $customer_id    = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        
        if($fromdate!='' && $todate!='' && $officer=='' && $customer_id=='' && $payment_type=='')
        { 
            $outletPayment= DB::table('eshop_customer_list')
               ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
               ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
               ->where('eshop_outlet_payments.executive_id',Auth::user()->id)
               ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
               ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id=='' && $payment_type=='')
        { 
            $outletPayment= DB::table('eshop_customer_list')
                           ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
                           ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
                           ->where('eshop_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('eshop_outlet_payments.officer_id',$officer) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                           ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id!='' && $payment_type=='')
        {
            $outletPayment = DB::table('eshop_customer_list')
                           ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
                           ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
                           ->where('eshop_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('eshop_outlet_payments.officer_id',$officer)
                           ->where('eshop_outlet_payments.customer_id',$customer_id)
                           ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id!='' && $payment_type!='')
        {


            $outletPayment= DB::table('eshop_customer_list')
                           ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
                           ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
                           ->where('eshop_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('eshop_outlet_payments.officer_id',$officer)
                           ->where('eshop_outlet_payments.customer_id',$customer_id)
                           ->where('eshop_outlet_payments.trans_type',$payment_type)
                           ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }

         elseif($fromdate!='' && $todate!='' && $officer=='' && $customer_id=='' && $payment_type!='')
        {


            $outletPayment= DB::table('eshop_customer_list')
                   ->JOIN('eshop_outlet_payments','eshop_outlet_payments.customer_id','eshop_customer_list.customer_id')
                   ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
                   ->where('eshop_outlet_payments.executive_id',Auth::user()->id)  
                   ->where('eshop_outlet_payments.trans_type',$payment_type)
                   ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                    ->get();

        }


        return view('eshop::payment/admin/payment_report_list',compact('outletPayment','selectedMenu','pageTitle'));

    }

    public function app_admin_payment($id, $status)
    {
        if($status=='APPROVE')
        {
            DB::table('eshop_outlet_payments')->where('transaction_id', $id)->update(
                [
                    'ack_by'           => Auth::user()->id,
                    'ack_date'           => date('Y-m-d'),
                    'ack_status'           => 'APPROVE',
                ]
            );
        }elseif($status=='NOT_APPROVE'){
            DB::table('eshop_outlet_payments')->where('transaction_id', $id)->update(
                [
                    'ack_by'           => Auth::user()->id,
                    'ack_date'           => date('Y-m-d'),
                    'ack_status'           => 'NOT_APPROVE',
                ]
            );
        }else{
            return Redirect::to('/eshop_admin_payments_con')->with('error', 'Sorry Payment Not Added.');
        }

        return Redirect::to('/eshop_admin_payments_con')->with('success', 'Successfully Payment Added.');

    }

    public function accounts_payments_con()
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }
 

        $outletList = DB::table('eshop_customer_list')
                        ->orderBy('customer_id','asc')
                        ->get();

        $outletPayment = DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','APPROVE')
                       ->get();
 

        return view('eshop::payment/account/outlet_credit')->with('outletList',$outletList)
            ->with('outletPayment',$outletPayment)
            ->with('user_business_type',$user_type)
            ->with('outlet_name',$outlet_name)
            ->with('selectedMenu',$selectedMenu)
            ->with('pageTitle',$pageTitle);

    }
    

    public function accounts_payments_con_list(Request $request)
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');

        //dd($customer);

        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }
        
        if($fromdate!='' && $todate!='' && $customer=='' && $payment_type=='')
        {
 

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','APPROVE')
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','APPROVE')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','APPROVE')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','APPROVE')
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }

        return view('eshop::payment/account/outlet_credit_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    public function account_payment_receive(Request $request)
    {

        $id = $request->get('id');
        $net_amount = $request->get('net_amount');
        $bank_charge = $request->get('bank_charge');

        //dd($id);
        DB::table('eshop_outlet_payments')->where('transaction_id', $id)->update(
            [
                'net_amount'           => $net_amount,
                'bank_charge'          => $bank_charge,
                'receive_by'           => Auth::user()->id,
                'receive_date'         => date('Y-m-d'),
                'ack_status'           => 'YES'
            ]
        );
 
        
        return Redirect::to('/eshop_accounts_payments_con')->with('success', 'Successfully Payment Added.');

    }

     public function accounts_payments_ack()
    {
        $selectedMenu   = 'Payments Ack';         // Required Variable
        $pageTitle      = 'Payment Ack List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;

        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

 
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
         $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->get();
 

        return view('eshop::payment/payment_ack')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function accounts_payments_ack_list(Request $request)
    {
        $selectedMenu   = 'Payments Ack';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

        if($fromdate!='' && $todate!='' && $customer=='' && $payment_type=='')
        {
 

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        

        return view('eshop::payment/payment_ack_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function accounts_payments_verify()
    {
        $selectedMenu   = 'Payments Verify';         // Required Variable
        $pageTitle      = 'Payment Verify List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;

        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

 
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
         $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->get();
 

        return view('eshop::payment/payment_verify')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function accounts_payments_verify_list(Request $request)
    {
        $selectedMenu   = 'Payments Verify';         // Required Variable
        $pageTitle      = 'Payment Verify List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

        if($fromdate!='' && $todate!='' && $customer=='' && $payment_type=='')
        {
 

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.customer_id',$customer)
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','YES')
                       ->where('eshop_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(eshop_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        

        return view('eshop::payment/payment_verify_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function accounts_payments_undo($id)
    {
        DB::table('eshop_outlet_payments')->where('transaction_id', $id)->update(
            [
                 
                'ack_status'           => 'APPROVE'
            ]
        );

        return Redirect::to('/eshop-accounts-payments-ack')->with('success', 'Successfully Payment Undo.');
    }

    public function accounts_payments_verify_process(Request $request)
    {
        
        if($request->isMethod('post'))
        {
            //echo '<pre/>'; print_r($req->input('reqid')); exit;
            //dd($req->all());
            
            foreach($request->input('tran_id') as $rowReqId)
            {

                $verify = $request->input('verify'); 
                $trans_id=$request->input('tran_id'); 
                $user_id=Auth::user()->id;
                if(isset($verify[$rowReqId]))
                {
                    DB::table('eshop_outlet_payments')->where('transaction_id', $trans_id)->update(
                        [
                             
                            'ack_status'           => 'CONFIRMED'
                        ]
                    );

                    $payments = DB::table('eshop_outlet_payments')
                        ->join('eshop_customer_list', 'eshop_customer_list.customer_id', 'eshop_outlet_payments.customer_id')
                        ->where('ack_status', 'CONFIRMED')
                        ->where('transaction_id', $trans_id)->first();

                        
                        if($payments->payment_amount>0){

                            $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $payments->customer_id)->orderBy('ledger_id','DESC')->first();

                            
                            if(sizeof($ledger)){
                                $closing_balance = $ledger->closing_balance;
                            }else{
                                $closing_balance = 0;
                            }
                            DB::table('eshop_outlet_ledger')->insert(
                                [
                                    'ledger_date'           => date('Y-m-d h:i:s'),
                                    'customer_id'           => $payments->customer_id,
                                    'outlet_id'             => $payments->outlet_id,
                                    'party_sap_code'        => $payments->sap_code,
                                    'trans_type'            => 'payment',
                                    'opening_balance'       => $closing_balance,
                                    'debit'                 => 0,
                                    'credit'                => $payments->payment_amount,
                                    'closing_balance'       => $closing_balance-$payments->payment_amount,
                                    'entry_by'              => Auth::user()->id,
                                    'entry_date'            => date('Y-m-d h:i:s')

                                ]
                            );
                        }

                        if($payments->adjust_amount>0){

                            $ledger = DB::table('eshop_outlet_ledger')->where('customer_id', $payments->customer_id)->orderBy('ledger_id','DESC')->first();

                             
                            if(sizeof($ledger)){
                                $closing_balance = $ledger->closing_balance;
                            }else{
                                $closing_balance = 0;
                            }
                            DB::table('eshop_outlet_ledger')->insert(
                                [
                                    'ledger_date'           => date('Y-m-d h:i:s'),
                                    'customer_id'           => $payments->customer_id,
                                    'outlet_id'             => $payments->outlet_id,
                                    'party_sap_code'        => $payments->sap_code,
                                    'trans_type'            => 'adjustment',
                                    'opening_balance'       => $closing_balance,
                                    'debit'                 => 0,
                                    'credit'                => $payments->adjust_amount,
                                    'closing_balance'       => $closing_balance-$payments->adjust_amount,
                                    'entry_by'              => Auth::user()->id,
                                    'entry_date'            => date('Y-m-d h:i:s'),
                                    'is_adjustment'         => 1

                                ]
                            );
                        }

                    

                }

            }

        } 

        return Redirect::to('/eshop-accounts-payments-verify')->with('success', 'Successfully Payment Verify.');
    }

    public function accounts_payments_rece_report()
    {
        $selectedMenu   = 'Outlet report';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;

        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

 
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
         $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
                       ->where('eshop_outlet_payments.ack_status','CONFIRMED')
                       ->get();

        


        return view('eshop::payment/account/report/outlet_credit')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    

    public function accounts_payments_rece_report_list(Request $request)
    {
        $selectedMenu   = 'Outlet report';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        
        $outletList = DB::table('eshop_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        } 
        $outletPayment= DB::table('eshop_outlet_payments')
            ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
            ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
            ->when($payment_type,function($q,$payment_type){
                return $q->where('eshop_outlet_payments.payment_type',$payment_type);
            })
            ->when($customer,function($q,$customer){
                return $q->where('eshop_outlet_payments.customer_id',$customer);
            })
            ->when($fromdate,function($q,$fromdate){
                return $q->where('eshop_outlet_payments.trans_date', '>=',$fromdate.' 00:00:00');
            })
            ->when($todate,function($q,$todate){
                return $q->where('eshop_outlet_payments.trans_date', '<=',$todate.' 23:59:59');
            }) 
            ->get();   

        return view('eshop::payment/account/report/outlet_credit_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function app_admin_payment_edit(Request $request)
    {
 
        $selectedMenu   = 'Outlet report';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable

         $outletList = DB::table('eshop_customer_list') 
                        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
                        ->where('eshop_customer_list.status',0)
                        ->orderBy('eshop_customer_list.name','ASC')    
                        ->get();

        $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

       $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('transaction_id', $request->get('paymentid'))->first();

       // dd($request->get('paymentid'));
        
        return view('eshop::payment/admin/editPayment')
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle)
        ->with('outletList',$outletList)
        ->with('bankList',$bankList)
        ->with('outletPayment',$outletPayment);
    }
    public function app_admin_payment_edit_submit(Request $request)
    { 

        DB::table('eshop_outlet_payments')->where('transaction_id', $request->get('id'))->update(
            [
                'customer_id'               => $request->customer_id,
                'payment_type'              => $request->payment_type,
                'bank_info_id'              => $request->bank_info,
                'branch_name'               => $request->branch_name,
                'ref_no'                    => $request->ref_no,
                'cheque_date'               => $request->cheque_date, 
                'payment_amount'            => $request->payment_amount,
                'adjust_amount'             => $request->adjust_amount,
                'payment_remarks'           => $request->payment_remarks,
                'update_by'                 => Auth::user()->id,
                'update_date'               => date('Y-m-d')
            ]
        );
        return Redirect::to('/eshop_admin_payments_con')->with('success', 'Successfully Payment Update.');

        
    }

    public function eshop_money_receipt($transaction_id)
    {
        $outletPayment= DB::table('eshop_outlet_payments')
                       ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')
                       ->where('transaction_id',$transaction_id)
                       ->first();

         return view('eshop::payment/money_receipt',compact('outletPayment'));
    }

    public function accounts_payments_verify_download(Request $request) {


        $fromdate   = $request->get('fromdate') ? date('Y-m-d', strtotime($request->get('fromdate'))) : NULL;
        $todate     = $request->get('todate') ? date('Y-m-d', strtotime($request->get('todate'))) : NULL;
        $customer   = $request->get('customer') ? $request->get('customer') : NULL;
        $payment_type   = $request->get('payment_type') ? $request->get('payment_type') : NULL; 

 
        $outletPayment= DB::table('eshop_outlet_payments')
            ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id')  
            ->where('eshop_outlet_payments.ack_status','YES')
            ->when($fromdate,function($q,$fromdate){
                return $q->where('eshop_outlet_payments.trans_date', '>=',$fromdate.' 00:00:00');
            })
            ->when($todate,function($q,$todate){
                return $q->where('eshop_outlet_payments.trans_date', '<=',$todate.' 23:59:59');
            }) 
            ->when($payment_type,function($q,$payment_type){
                return $q->where('eshop_outlet_payments.payment_type',$payment_type);
            })     
            ->get();  

        $data = array(); 
        $serial = 0;
        if(sizeof($outletPayment) > 0){
            foreach ($outletPayment as $key => $items) { 
                $data[] = array(
                    'Sl' => $key+1,
                    'Payment Date' => $items->trans_date,
                    'Customer Name' => $items->name,
                    'SAP Code' => $items->sap_code,
                    'Payment No' => $items->payment_no,
                    'Bank Name' => $items->bank_name,
                    'Bank A/C' => $items->code,
                    'Branch Name' => $items->branch_name,
                    'Reference No' => $items->ref_no,
                    'Payment Amount' => $items->payment_amount,
                    'Adjust Amount' => $items->adjust_amount,
                    'Actual Amount' => $items->net_amount,
                    'Bank Charge' => $items->bank_charge,
                    'Payment Type' => $items->payment_type, 
                    'Ack Remarks'  => $items->ack_remarks, 
                );  
            }
        }else{
            $data = array( 'Sl','Payment Date','Customer Name','SAP Code','Payment No','Bank Name','Bank A/C','Branch Name','Reference No','Payment Amount', 'Adjust Amount','Actual Amount','Bank Charge','Payment Type','Ack Remarks'); 
        }  
        Excel::create('accounts_payments_verify_download', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');
    }

    public function accounts_payments_rece_report_download(Request $request)
    {
        $fromdate   = $request->get('fromdate') ? date('Y-m-d', strtotime($request->get('fromdate'))) : NULL;
        $todate     = $request->get('todate') ? date('Y-m-d', strtotime($request->get('todate'))) : NULL;
        $customer   = $request->get('customer') ? $request->get('customer') : NULL;
        $payment_type   = $request->get('payment_type') ? $request->get('payment_type') : NULL; 

        $outletPayment= DB::table('eshop_outlet_payments')
            ->JOIN('eshop_customer_list','eshop_customer_list.customer_id','eshop_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','eshop_outlet_payments.bank_info_id') 
            ->where('eshop_outlet_payments.ack_status','CONFIRMED') 
            ->when($payment_type,function($q,$payment_type){
                return $q->where('eshop_outlet_payments.payment_type',$payment_type);
            })
            ->when($customer,function($q,$customer){
                return $q->where('eshop_outlet_payments.customer_id',$customer);
            })
            ->when($fromdate,function($q,$fromdate){
                return $q->where('eshop_outlet_payments.trans_date', '>=',$fromdate.' 00:00:00');
            })
            ->when($todate,function($q,$todate){
                return $q->where('eshop_outlet_payments.trans_date', '<=',$todate.' 23:59:59');
            }) 
            ->get();  

        $data = array(); 
        $serial = 0;
        foreach ($outletPayment as $key => $items) { 
            $data[] = array(
                'Sl' => $key+1,
                'Payment Date' =>$items->trans_date,
                'Verify Date' => $items->confirmed_date,
                'Customer Name' => $items->name,
                'SAP Code' => $items->sap_code,
                'Payment No' => $items->payment_no,
                'Bank Name' => $items->bank_name,
                'Bank A/C' => $items->code,
                'Branch Name' => $items->branch_name ,
                'Reference No' => $items->ref_no ,
                'Payment Amount' => $items->payment_amount,
                'Adjust Amount' => $items->adjust_amount,
                'Actual Amount' => $items->net_amount,
                'Bank Charge' => $items->bank_charge,
                'Payment Type' => $items->payment_type,
                'Remarks' => $items->ack_remarks
            );  
        }   
        Excel::create('accounts_payments_rece_report', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv'); 
    } 
    
} 