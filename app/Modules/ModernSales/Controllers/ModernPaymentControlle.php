<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
use Validator;
use Excel;
use Carbon\Carbon;
class ModernPaymentControlle extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function mts_opening_route()
    {

        $selectedMenu   = 'Visit';         // Required Variable
        $pageTitle      = 'Visit';        // Page Slug Title

         $routeResult = DB::table('mts_route')
                        ->where('status',0)
                        ->get();
      

        return view('ModernSales::sales/payment/routeManage', compact('selectedMenu','pageTitle','routeResult'));
    }

     public function mts_opening_outlet(Request $request)
    {

        $routeid = $request->get('route');


        $ledgerCheckParty = DB::table('mts_outlet_ledger')
                        ->groupBy('customer_id')                    
                        ->get();

        $customer_id = array();

         foreach($ledgerCheckParty as $checkParty) {
                    $customer_id[]= $checkParty->customer_id;
                }

        $resultParty = DB::table('mts_customer_list')
                        ->select('customer_id','name','route_id','owner','address','opening_balance','sap_code','status')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('route_id', $routeid)
                        ->whereNotIn('customer_id', $customer_id)
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
      //dd($customer_id);

        return view('ModernSales::sales/payment/outletList', compact('resultParty','routeid'));
    }


    public function mts_add_opening_balance(Request $request){

    	$countRows = count($request->get('opening_balance'));
    	

    	for($m=0;$m<$countRows;$m++)
            {
            	

                if($request->get('opening_balance')[$m]!='')
                {
                	
                    DB::table('mts_customer_list')->where('customer_id',$request->get('customer_id')[$m])->update(
                        [
                            'opening_balance'   => $request->get('opening_balance')[$m]
                        ]
                    );

                     DB::table('mts_outlet_ledger')->insert(
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

            return Redirect::to('/mts-opening-route')->with('success', 'Successfully Added Add To Cart.');
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

            $outletList = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('mts_customer_list.status',0)
                        ->orderBy('mts_customer_list.name','ASC')    
                        ->get();

        $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

        $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.trans_type','adjustment')
                       ->where('mts_outlet_payments.entry_by',Auth::user()->id)        
                        ->get();
 

        return view('ModernSales::payment/credit_adjustment')->with('outletList',$outletList)
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
  
            $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

            $outletPayment= DB::table('mts_outlet_payments')
                           ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                           ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                           ->where('mts_outlet_payments.trans_type','adjustment')
                           ->where('mts_outlet_payments.entry_by',Auth::user()->id) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

           // dd($outletPayment);
        }

        return view('ModernSales::payment/credit_adjustment_list')->with('outletList',$outletList)
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
            $payment_from   =   $req->input('payment_from');
            $customer_info  =   $req->input('party_id');
            $customer       = explode('-',$customer_info);
            $party_id       = $customer[0];
            $sap_code       = $customer[1];

            $autoNo = rand(100,1000);
           
            $payment_no = 'AE'.'-'.$sap_code.'-'.date('dmy').'-'.$autoNo;

            $supervisorList = DB::table('mts_role_hierarchy')
                        ->where('status',0)                        
                        ->where('officer_id',Auth::user()->id) 
                        ->first();
            
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
 

                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'management_id'          => $supervisorList->management_id,
                        'manager_id'             => $supervisorList->manager_id,
                        'executive_id'           => $supervisorList->executive_id,
                        'officer_id'             => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'payment_from'          => $payment_from,
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


                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'management_id'          => $supervisorList->management_id,
                        'manager_id'             => $supervisorList->manager_id,
                        'executive_id'           => $supervisorList->executive_id,
                        'officer_id'             => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'payment_from'          => $payment_from,
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

                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'            => $party_id,
                        'management_id'          => $supervisorList->management_id,
                        'manager_id'             => $supervisorList->manager_id,
                        'executive_id'           => $supervisorList->executive_id,
                        'officer_id'             => Auth::user()->id,
                        'payment_no'             => $payment_no,
                        'outlet_in_charge'       => $this->outlet_in_charge,
                        'payment_type'           => $payment_type,
                        'payment_from'          => $payment_from,
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
            

            return Redirect::to('/credit-adjustment')->with('success', 'Successfully Adjustment Added.');

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

        $outletList = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_define_executive.executive_id', Auth::user()->id)
                        ->where('mts_customer_list.status',0)
                        ->orderBy('mts_customer_list.name','ASC')    
                        ->get();

      $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();
        $ssgbankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

        $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.trans_type','payment')
                       ->where('mts_outlet_payments.entry_by',Auth::user()->id)
                       ->orderBy('mts_outlet_payments.transaction_id','DESC')        
                        ->get();

       // dd($outletPayment);
 

        return view('ModernSales::payment/outlet_credit')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('bankList',$bankList)
        ->with('ssgbankList',$ssgbankList)
        ->with('pageTitle',$pageTitle);

    }
    public function inventoryManagement()
    {
        $selectedMenu   = 'Inventory Management';         // Required Variable
        $pageTitle      = 'Inventory List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;  

        return view('ModernSales::payment/inventory_management')  
        ->with('selectedMenu',$selectedMenu) 
        ->with('pageTitle',$pageTitle);

    }
    public function inventoryManagementProcess(Request $request)
    {
        if($request->hasFile('imported-file')){

    		$path = $request->file('imported-file')->getRealPath();

    		$data = Excel::load($path, function($reader) {})->get();

    		if(!empty($data) && $data->count()){

    			$data = $data->toArray();  
                 
    			try {
                    DB::beginTransaction();
                    $insertDate = date('Y-m-d h:i:s');
                    foreach ($data as $key => $value) { 
                        $tblProductStock = DB::table('tbl_product_stock')
                            ->where('product_id', $value['product_id'])
                            ->where('sap_code', $value['sap_code'])
                            ->first();
                        if ($tblProductStock) {
                            $product_stock = [ 
                                'in_qty' => $value['qty'] + $tblProductStock->in_qty,
                                'stock_qty' => $value['qty'] + $tblProductStock->stock_qty, 
                                'created_by'   => Auth::user()->id,
                                'updated_at'   => $insertDate,
                                'updated_by'   => Auth::user()->id
                            ]; 
                            DB::table('tbl_product_stock')
                                ->where('product_id', $value['product_id'])
                                ->where('sap_code', $value['sap_code'])
                                ->update($product_stock);
                
                        } else {
                            $product_stock = [
                                'product_id' => $value['product_id'],  
                                'sap_code' => $value['sap_code'],
                                'in_qty' => $value['qty'],
                                'stock_qty' => $value['qty'],
                                'created_at'   => $insertDate,
                                'created_by'   => Auth::user()->id,
                                'updated_at'   => $insertDate,
                                'updated_by'   => Auth::user()->id
                            ]; 
                            DB::table('tbl_product_stock')->insert($product_stock);
                        } 
                        DB::table('tbl_upload_inventory')->insert(
                            [ 
                                'product_id'   => $value['product_id'],
                                'sap_code'     => $value['sap_code'],
                                'type'         => 'in',
                                'qty'          => $value['qty'],
                                'created_at'   => $insertDate,
                                'created_by'   => Auth::user()->id,
                                'updated_at'   => $insertDate,
                                'updated_by'   => Auth::user()->id
                            ]
                        );
                    } 
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    // Handle the exception, log, or throw further
                    // For example:
                    Log::error('Transaction failed: ' . $e->getMessage());
                    // throw $e; // rethrow the exception if needed
                }

				return back()->with('success','Inventory Upload Succesfull.');
				 
    		} else {
				
				return back()->with('error','Please Check your file, Something is wrong there.');	
				
			}


    	}

    }

    public function mtsInventoryReport(Request $request){
        $selectedMenu   = 'Inventory Report';         // Required Variable
        $pageTitle      = 'Inventory Report List';       // Required Variable 

        $tblProductStock = DB::table('tbl_product_stock') 
                            ->get();

        return view('ModernSales::payment/inventory_report')  
        ->with('selectedMenu',$selectedMenu) 
        ->with('stocks',$tblProductStock)
        ->with('pageTitle',$pageTitle);
    }
    public function inventoryManagementProcessww(Request $request)
    {
        if($request->hasFile('imported-file')){

    		$path = $request->file('imported-file')->getRealPath();

    		$data = Excel::load($path, function($reader) {})->get();

    		if(!empty($data) && $data->count()){

    			$data = $data->toArray();  
                
                $insertDate = date('Y-m-d h:i:s');
    			foreach ($data as $key => $value) { 
                    $tblProductStock = DB::table('tbl_product_stock')
                            ->where('product_id', $value['product_id'])
                            ->where('sap_code', $value['sap_code'])
                            ->first();
                    if($tblProductStock){
                        $product_stock = [ 
                            'in_qty' => $value['qty'] + $tblProductStock->in_qty,
                            'stock_qty' => $value['qty'] + $tblProductStock->stock_qty, 
                            'created_by'   => Auth::user()->id,
                            'updated_at'   => $insertDate,
                            'updated_by'   => Auth::user()->id
                        ]; 
                        DB::table('tbl_product_stock')
                            ->where('product_id', $value['product_id'])
                            ->where('sap_code', $value['sap_code'])
                            ->update($product_stock);

                    }else{
                        $product_stock = [
                            'product_id' => $value['product_id'],  
                            'sap_code' => $value['sap_code'],
                            'in_qty' => $value['qty'],
                            'stock_qty' => $value['qty'] + $tblProductStock->stock_qty,
                            'created_at'   => $insertDate,
                            'created_by'   => Auth::user()->id,
                            'updated_at'   => $insertDate,
                            'updated_by'   => Auth::user()->id
                        ]; 
                        DB::table('tbl_product_stock')->insert($product_stock);
                    } 
                    DB::table('tbl_upload_inventory')->insert(
                        [ 
                            'product_id'   => $value['product_id'],
                            'sap_code'     => $value['sap_code'],
                            'type'         => 'in',
                            'qty'          => $value['qty'],
                            'created_at'   => $insertDate,
                            'created_by'   => Auth::user()->id,
                            'updated_at'   => $insertDate,
                            'updated_by'   => Auth::user()->id
                        ]
                    );
                } 

				return back()->with('success','Inventory Upload Succesfull.');
				 
    		} else {
				
				return back()->with('error','Please Check your file, Something is wrong there.');	
				
			}


    	}

    }
    
    public function productDownload(Request $request){
        $tblProduct = DB::table('tbl_product')
                            ->select('id','sap_code','name') 
                            ->get();

        
        $data = array();
        foreach ($tblProduct as $items) {
            $data[] = [
                'product_id' => $items->id,
                'sap_code'   => $items->sap_code,
                'name'       => $items->name,
                'qty'        => 0
            ];
        } 

        // //$items = Item::all();
        Excel::create('product_list', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');
    }
    public function downloadInventoryReport(Request $request){

        $tblProduct = DB::table('tbl_product_stock')->get();        
        $data = array();
        foreach ($tblProduct as $items) {  
            $data[] = [ 
                'sap_code'   => $items->sap_code,
                'in_qty'   => $items->in_qty,
                'out_qty'   => $items->out_qty,
                'stock_qty'   => $items->stock_qty
            ];
        } 

        // //$items = Item::all();
        Excel::create('stock_report', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');
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
  
            $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.trans_type','payment')
                           ->where('mts_outlet_payments.entry_by',Auth::user()->id)
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                           ->orderBy('mts_outlet_payments.transaction_id','DESC')         
                            ->get();
        }

        return view('ModernSales::payment/outlet_credit_list')->with('outletList',$outletList)
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
            $payment_from   =   $req->input('payment_from');
            $customer_info  =   $req->input('party_id');
            $customer       = explode('-',$customer_info);
            $party_id       = $customer[0];
            $sap_code       = $customer[1];

           

            $autoNo = rand(100,1000);
           
            $payment_no = 'PE'.'-'.$sap_code.'-'.date('dmy').'-'.$autoNo;

            $supervisorList = DB::table('mts_role_hierarchy')
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

                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'management_id'         => $supervisorList->management_id,
                        'manager_id'            => $supervisorList->manager_id,
                        'executive_id'          => $supervisorList->executive_id,
                        'officer_id'            => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'payment_from'          => $payment_from,
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


                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'           => $party_id,
                        'management_id'         => $supervisorList->management_id,
                        'manager_id'            => $supervisorList->manager_id,
                        'executive_id'          => $supervisorList->executive_id,
                        'officer_id'            => Auth::user()->id,
                        'payment_no'            => $payment_no,
                        'outlet_in_charge'      => $this->outlet_in_charge,
                        'payment_type'          => $payment_type,
                        'payment_from'          => $payment_from,
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


                DB::table('mts_outlet_payments')->insert(
                    [
                        'customer_id'            => $party_id,
                        'management_id'          => $supervisorList->management_id,
                        'manager_id'             => $supervisorList->manager_id,
                        'executive_id'           => $supervisorList->executive_id,
                        'officer_id'             => Auth::user()->id,
                        'payment_no'             => $payment_no,
                        'outlet_in_charge'       => $this->outlet_in_charge,
                        'payment_type'           => $payment_type,
                        'payment_from'           => $payment_from,
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
            

            return Redirect::to('/outlet_payments')->with('success', 'Successfully Payment Added.');

        }

    }

    public function mts_outlet_payments_delete($paymentid,$type)
    {

        DB::table('mts_outlet_payments')
        ->where('ack_status', 'NO')                        
        ->where('entry_by',Auth::user()->id)                        
        ->where('transaction_id',$paymentid)                  
        ->delete(); 

        if($type==1){
             return Redirect::to('/outlet_payments')->with('success', 'Successfully Payment Delete!');
        }else{
             return Redirect::to('/credit-adjustment')->with('success', 'Successfully Adjustment Delete!');
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

        
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get(); 

        $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO')       
                        ->get();

        $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();


        return view('ModernSales::payment/admin/outlet_credit', compact('selectedMenu','pageTitle','managementlist','outletList','outletPayment','outlet_name','officerlist'));

       

    }
    

    public function admin_payments_con_list(Request $request)
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable 

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $executive  = $request->get('executive_id');
        $officer    = $request->get('fos');
        $customer_id    = $request->get('customer_id'); 
        $payment_type    = $request->get('payment_type'); 
        
        if($fromdate!='' && $todate!='' && $executive!='' && $officer=='' && $customer_id=='' && $payment_type=='') 
        {
 

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO') 
                       ->where('mts_outlet_payments.executive_id',$executive) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }elseif($fromdate!='' && $todate!='' && $executive!='' && $officer!='' && $customer_id=='' && $payment_type=='') 
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO') 
                       ->where('mts_outlet_payments.executive_id',$executive) 
                       ->where('mts_outlet_payments.officer_id',$officer) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();


        }elseif($fromdate!='' && $todate!='' && $executive!='' && $officer!='' && $customer_id!='' && $payment_type=='') 
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO') 
                       ->where('mts_outlet_payments.executive_id',$executive) 
                       ->where('mts_outlet_payments.officer_id',$officer) 
                       ->where('mts_outlet_payments.customer_id',$customer_id) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }elseif($fromdate!='' && $todate!='' && $executive!='' && $officer!='' && $customer_id!='' && $payment_type!='') 
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO') 
                       ->where('mts_outlet_payments.executive_id',$executive) 
                       ->where('mts_outlet_payments.officer_id',$officer) 
                       ->where('mts_outlet_payments.customer_id',$customer_id) 
                       ->where('mts_outlet_payments.trans_type',$payment_type) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }
        elseif($fromdate!='' && $todate!='' && $executive!='' && $officer=='' && $customer_id=='' && $payment_type!='') 
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('mts_outlet_payments.ack_status','NO') 
                       ->where('mts_outlet_payments.executive_id',$executive)  
                       ->where('mts_outlet_payments.trans_type',$payment_type) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }

        return view('ModernSales::payment/admin/outlet_credit_list',compact('outletList','outletPayment','pageTitle','outlet_name','customer','selectedMenu'));

    }


    public function mts_admin_payment()
    {
        $selectedMenu   = 'Customer payment';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        

        $customer = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

        $outletPayment= DB::table('mts_customer_list')
                       ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
                       ->where('mts_outlet_payments.executive_id',Auth::user()->id)
                       ->where('mts_outlet_payments.ack_status','NO')       
                        ->get();

        $managementlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.management_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.management_id')
          ->get();

          $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

          $officerlist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.officer_id')
          ->get();

        return view('ModernSales::payment/admin/payment_report',compact('customer','outletPayment','selectedMenu','pageTitle','managementlist','officerlist','executivelist'));

    }
    

    public function mts_admin_payment_list(Request $request)
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
            $outletPayment= DB::table('mts_customer_list')
               ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
               ->where('mts_outlet_payments.ack_status','APPROVE') 
               ->where('mts_outlet_payments.executive_id',Auth::user()->id)
               ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
               ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id=='' && $payment_type=='')
        { 
            $outletPayment= DB::table('mts_customer_list')
                           ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
                           ->where('mts_outlet_payments.ack_status','APPROVE') 
                           ->where('mts_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('mts_outlet_payments.officer_id',$officer) 
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                           ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id!='' && $payment_type=='')
        {
            $outletPayment = DB::table('mts_customer_list')
                           ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
                           ->where('mts_outlet_payments.ack_status','APPROVE') 
                           ->where('mts_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('mts_outlet_payments.officer_id',$officer)
                           ->where('mts_outlet_payments.customer_id',$customer_id)
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }
        elseif($fromdate!='' && $todate!='' && $officer!='' && $customer_id!='' && $payment_type!='')
        {


            $outletPayment= DB::table('mts_customer_list')
                           ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
                           ->where('mts_outlet_payments.ack_status','APPROVE') 
                           ->where('mts_outlet_payments.executive_id',Auth::user()->id) 
                           ->where('mts_outlet_payments.officer_id',$officer)
                           ->where('mts_outlet_payments.customer_id',$customer_id)
                           ->where('mts_outlet_payments.trans_type',$payment_type)
                           ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                            ->get();

        }

         elseif($fromdate!='' && $todate!='' && $officer=='' && $customer_id=='' && $payment_type!='')
        {


            $outletPayment= DB::table('mts_customer_list')
                   ->JOIN('mts_outlet_payments','mts_outlet_payments.customer_id','mts_customer_list.customer_id')
                   ->where('mts_outlet_payments.ack_status','APPROVE') 
                   ->where('mts_outlet_payments.executive_id',Auth::user()->id)  
                   ->where('mts_outlet_payments.trans_type',$payment_type)
                   ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))        
                    ->get();

        }

        return view('ModernSales::payment/admin/payment_report_list',compact('outletPayment','selectedMenu','pageTitle'));

    }

    public function app_admin_payment($id, $status)
    {
        if($status=='APPROVE')
        {
            DB::table('mts_outlet_payments')->where('transaction_id', $id)->update(
                [
                    'ack_by'           => Auth::user()->id,
                    'ack_date'           => date('Y-m-d h:i:s'),
                    'ack_status'           => 'APPROVE',
                ]
            );
        }elseif($status=='NOT_APPROVE'){
            DB::table('mts_outlet_payments')->where('transaction_id', $id)->update(
                [
                    'ack_by'           => Auth::user()->id,
                    'ack_date'           => date('Y-m-d h:i:s'),
                    'ack_status'           => 'NOT_APPROVE',
                ]
            );
        }else{
            return Redirect::to('/admin_payments_con')->with('error', 'Sorry Payment Not Added.');
        }

        return Redirect::to('/admin_payments_con')->with('success', 'Successfully Payment Added.');

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
 

        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();

        $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','APPROVE')
                       ->get();
 

        return view('ModernSales::payment/account/outlet_credit')->with('outletList',$outletList)
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
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        $payment_from   = $request->get('payment_from');  
        
        if($fromdate!='' && $todate!='')
        { 
            $outletPayment = DB::table('mts_outlet_payments')
                ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                ->where('mts_outlet_payments.ack_status','APPROVE')
                ->when($customer, function ($query, $customer){ 
                    $query->where('mts_outlet_payments.customer_id',$customer); 
                }) 
                ->when($payment_type, function ($query, $payment_type){ 
                    $query->where('mts_outlet_payments.payment_type',$payment_type); 
                }) 
                ->when($payment_from, function ($query, $payment_from){ 
                    $query->where('mts_outlet_payments.payment_from', $payment_from); 
                })
                ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                ->get(); 

        } 
        return view('ModernSales::payment/account/outlet_credit_list')
        ->with('outletPayment',$outletPayment) 
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    
    // Backup By Mazid 5-24-23
    public function accounts_payments_con_listBackup(Request $request)
    {
        $selectedMenu   = 'Outlet pay';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
         
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');

        
         
        
        if($fromdate!='' && $todate!='' && $customer=='' && $payment_type=='')
        {
 

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','APPROVE')
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','APPROVE')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','APPROVE')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','APPROVE')
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }

        return view('ModernSales::payment/account/outlet_credit_list')
        ->with('outletPayment',$outletPayment) 
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    

    public function accounts_payments_download(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('toDate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        $payment_from   = $request->get('payment_from');  
        
        $outletPayment= DB::table('mts_outlet_payments')
            ->select('mts_customer_list.name','mts_customer_list.sap_code','mts_outlet_payments.payment_no', 'mts_outlet_payments.payment_from', 'tbl_master_bank.bank_name','tbl_master_bank.code','mts_outlet_payments.branch_name','mts_outlet_payments.ref_no','mts_outlet_payments.trans_date','mts_outlet_payments.payment_amount','mts_outlet_payments.payment_type', 'mts_outlet_payments.ack_status','mts_outlet_payments.ack_remarks','mts_outlet_payments.ack_date')
            ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
            ->where('mts_outlet_payments.ack_status','APPROVE')
            ->when($customer, function ($query, $customer){ 
                $query->where('mts_outlet_payments.customer_id',$customer); 
            }) 
            ->when($payment_type, function ($query, $payment_type){ 
                $query->where('mts_outlet_payments.payment_type',$payment_type); 
            }) 
            ->when($payment_from, function ($query, $payment_from){ 
                $query->where('mts_outlet_payments.payment_from', $payment_from); 
            }) 
            ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))
            ->get(); 
        $data = array();
        foreach ($outletPayment as $items) {
            $data[] = (array)$items;  
        } 

        //$items = Item::all();
        Excel::create('PAYMENT_PENDING_LIST', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');

    }

    // Backup By Mazid 24-5-23
    public function accounts_payments_downloadBackup(Request $request)
    {
        $outletPayment= DB::table('mts_outlet_payments')
            ->select('mts_customer_list.name','mts_customer_list.sap_code','mts_outlet_payments.payment_no','tbl_master_bank.bank_name','tbl_master_bank.code','mts_outlet_payments.branch_name','mts_outlet_payments.ref_no','mts_outlet_payments.trans_date','mts_outlet_payments.payment_amount','mts_outlet_payments.payment_type', 'mts_outlet_payments.payment_from','mts_outlet_payments.ack_status','mts_outlet_payments.ack_remarks','mts_outlet_payments.ack_date')
            ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
            ->where('mts_outlet_payments.ack_status','APPROVE')
            ->get();


        $data = array();
        foreach ($outletPayment as $items) {
            $data[] = (array)$items;  
        } 

        //$items = Item::all();
        Excel::create('PAYMENT_PENDING_LIST', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');

    }

    public function accounts_payments_verify_download(Request $request) { 
        $fromdate   = $request->get('fromdate') ? date('Y-m-d', strtotime($request->get('fromdate'))) : NULL;
        $todate     = $request->get('todate') ? date('Y-m-d', strtotime($request->get('todate'))) : NULL;
        $customer   = $request->get('customer') ? $request->get('customer') : NULL;
        $payment_type   = $request->get('payment_type') ? $request->get('payment_type') : NULL; 
        $payment_from   = $request->get('payment_from') ? $request->get('payment_from') : NULL; 

 
        $outletPayment= DB::table('mts_outlet_payments')
            ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')  
            ->where('mts_outlet_payments.ack_status','YES')
            ->when($fromdate,function($q,$fromdate){
                return $q->where('mts_outlet_payments.trans_date', '>=',$fromdate.' 00:00:00');
            })
            ->when($todate,function($q,$todate){
                return $q->where('mts_outlet_payments.trans_date', '<=',$todate.' 23:59:59');
            }) 
            ->when($payment_type,function($q,$payment_type){
                return $q->where('mts_outlet_payments.payment_type',$payment_type);
            })     
            ->when($payment_from,function($q,$payment_from){
                return $q->where('mts_outlet_payments.payment_from',$payment_from);
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
                    'Payment From' => $items->payment_from,
                    'Bank Name' => $items->bank_name,
                    'Bank A/C' => $items->code ? 'CA-'.substr($items->code, -5) : '' ,
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
            $data = array( 'Sl','Payment Date','Customer Name','SAP Code','Payment No','Payment From','Bank Name','Bank A/C','Branch Name','Reference No','Payment Amount', 'Adjust Amount','Actual Amount','Bank Charge','Payment Type','Ack Remarks'); 
        }  
        Excel::create('accounts_payments_verify_download', function($excel) use($data) {
            $excel->sheet('ExportFile', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->export('csv');
    }
    public function accounts_payments_verify_download_back(Request $request)
    {
        $outletPayment= DB::table('mts_outlet_payments')
            ->select('mts_customer_list.name','mts_customer_list.sap_code','mts_outlet_payments.payment_no','tbl_master_bank.bank_name','tbl_master_bank.code','mts_outlet_payments.branch_name','mts_outlet_payments.ref_no','mts_outlet_payments.trans_date','mts_outlet_payments.payment_amount','mts_outlet_payments.payment_type','mts_outlet_payments.ack_status','mts_outlet_payments.ack_remarks','mts_outlet_payments.receive_date as ack_date')
           ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
           ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
           ->where('mts_outlet_payments.ack_status','YES')
           ->get();

        $data = array();
        foreach ($outletPayment as $items) {
            $data[] = (array)$items;  
        } 

        //$items = Item::all();
        Excel::create('PAYMENT_PENDING_LIST', function($excel) use($data) {
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
        $payment_from   = $request->get('payment_from') ? $request->get('payment_from') : NULL; 
 
        // $outletPayment = DB::table('mts_outlet_payments')
        //     ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
        //     ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
        //     // ->where('mts_outlet_payments.ack_status','CONFIRMED')
        //     ->when($fromdate,function($q,$fromdate){
        //         return $q->where('mts_outlet_payments.confirmed_date', '>=',$fromdate.' 00:00:00');
        //     })
        //     ->when($todate,function($q,$todate){
        //         return $q->where('mts_outlet_payments.confirmed_date', '<=',$todate.' 23:59:59');
        //     }) 
        //     ->when($payment_type,function($q,$payment_type){
        //         return $q->where('mts_outlet_payments.payment_type',$payment_type);
        //     })     
        //     // ->when($payment_from,function($q,$payment_from){
        //     //     return $q->where('mts_outlet_payments.payment_from',$payment_from);
        //     // })     
        //     ->get(); 

        $outletPayment= DB::table('mts_outlet_payments')
            ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')  

            ->when($fromdate,function($q,$fromdate){  
                return $q->whereDate("mts_outlet_payments.confirmed_date",'>=', $fromdate); 
            })  
            ->when($todate,function($q,$todate){  
                return $q->whereDate("mts_outlet_payments.confirmed_date",'<=', $todate); 
            })  
            // ->when(($this->user_type_id !=6),function($q,$customer){ 
            //     return $q->where('mts_outlet_payments.ack_status','CONFIRMED') ; 
            // })
            ->when(($customer),function($q,$customer){ 
                return $q->where('mts_outlet_payments.customer_id',$customer); 
            })
            ->when(($payment_type),function($q,$payment_type){ 
                return $q->where('mts_outlet_payments.payment_type',$payment_type); 
            })
            ->when(($payment_from),function($q,$payment_from){ 
                return $q->where('mts_outlet_payments.payment_from',$payment_from); 
            })
            ->get(); 
        $data = array(); 
        $serial = 0;
        // dd($outletPayment);
        foreach ($outletPayment as $key => $items) { 
            $data[] = array(
                'Sl' => $key+1,
                'Payment Date' =>$items->trans_date,
                'Verify Date' => $items->confirmed_date,
                'Customer Name' => $items->name,
                'SAP Code' => $items->sap_code,
                'Payment No' => $items->payment_no,
                'Payment From' => $items->payment_from,
                'Bank Name' => $items->bank_name,
                'Bank A/C' => $items->code ? 'CA-'.substr($items->code, -5) : '',
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
    public function account_payment_receive(Request $request)
    {

       // $id = $request->get('id');
        //$net_amount = $request->get('net_amount');
       // $bank_charge = $request->get('bank_charge');

        if($request->isMethod('post'))
        {
            //echo '<pre/>'; print_r($req->input('reqid')); exit;
            //dd($req->all());

             
            if(!empty($request->input('tran_id'))){
                foreach($request->input('tran_id') as $key => $trans_id)
                {
                    DB::table('mts_outlet_payments')->where('transaction_id', $trans_id)->update(
                        [
                            'net_amount'           => $request->net_amount[$key],
                            'bank_charge'          => $request->bank_charge[$key],
                            'receive_by'           => Auth::user()->id,
                            'receive_date'         => date('Y-m-d h:i:s'),
                            'ack_status'           => 'YES',
                            'ack_remarks'          => $request->ack_remarks[$key]
                        ]
                    );

                }
            }
        }  
 
        
        return Redirect::to('/accounts_payments_con')->with('success', 'Successfully Payment Added.');

    }

     public function accounts_payments_ack()
    {
        $selectedMenu   = 'Payments Ack';         // Required Variable
        $pageTitle      = 'Payment Ack List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;

        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

 
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
         $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->get();
 

        return view('ModernSales::payment/payment_ack')->with('outletList',$outletList)
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
        
        $outletList = DB::table('mts_customer_list')
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
 

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        

        return view('ModernSales::payment/payment_ack_list')->with('outletList',$outletList)
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

        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();        
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");        
        foreach ($user_business_type as $type)
        {
            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;
        }
 
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        $outletPayment= DB::table('mts_outlet_payments')
                        ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                        ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                        ->where('mts_outlet_payments.ack_status','YES')
                        ->get(); 

        return view('ModernSales::payment/payment_verify')->with('outletList',$outletList)
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
        $payment_from   = $request->get('payment_from');
        
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }  
        if($fromdate!='' && $todate!='' ) {
            $outletPayment= DB::table('mts_outlet_payments')
                        ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                        ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                        ->where('mts_outlet_payments.ack_status','YES')
                        ->when(($customer),function($q, $customer){ 
                            return $q->where('mts_outlet_payments.customer_id',$customer); 
                        })
                        ->when(($payment_type),function($q,$payment_type){ 
                            return $q->where('mts_outlet_payments.payment_type',$payment_type); 
                        })  
                        ->when(($payment_from),function($q, $payment_from){ 
                            return $q->where('mts_outlet_payments.payment_from', $payment_from); 
                        })  
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        } 
        return view('ModernSales::payment/payment_verify_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    public function accounts_payments_verify_list_Backup(Request $request)
    {
        $selectedMenu   = 'Payments Verify';         // Required Variable
        $pageTitle      = 'Payment Verify List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        $payment_from   = $request->get('payment_from');
        
        $outletList = DB::table('mts_customer_list')
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
 

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','YES')
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.trans_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        

        return view('ModernSales::payment/payment_verify_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }

    public function accounts_payments_undo($id)
    {
        DB::table('mts_outlet_payments')->where('transaction_id', $id)->update(
            [
                 
                'ack_status'           => 'APPROVE'
            ]
        );

        return Redirect::to('/accounts-payments-ack')->with('success', 'Successfully Payment Undo.');
    }

    public function accounts_payments_verify_process(Request $request)
    {
        
        if($request->isMethod('post'))
        { 
             
            if(!empty($request->input('tran_id'))){
                foreach($request->input('tran_id')  as $key => $trans_id)
                {

                    DB::table('mts_outlet_payments')->where('transaction_id', $trans_id)->update(
                        [
                             
                            'ack_status'           => 'CONFIRMED',
                            'confirmed_by'         => Auth::user()->id,
                            'confirmed_date'       => date('Y-m-d h:i:s'),
                            'ack_remarks'          => $request->ack_remarks[$key]

                        ]
                    );

                    $payments = DB::table('mts_outlet_payments')
                        ->join('mts_customer_list', 'mts_customer_list.customer_id', 'mts_outlet_payments.customer_id')
                        ->where('ack_status', 'CONFIRMED')
                        ->where('transaction_id', $trans_id)->first();

                        
                    if($payments->payment_amount>0){

                        $ledger = DB::table('mts_outlet_ledger')->where('customer_id', $payments->customer_id)->orderBy('ledger_id','DESC')->first();

                        
                        if(sizeof($ledger)){
                            $closing_balance = $ledger->closing_balance;
                        }else{
                            $closing_balance = 0;
                        }
                        DB::table('mts_outlet_ledger')->insert(
                            [
                                'ledger_date'           => date('Y-m-d h:i:s'),
                                'customer_id'           => $payments->customer_id,
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

                        $ledger = DB::table('mts_outlet_ledger')->where('customer_id', $payments->customer_id)->orderBy('ledger_id','DESC')->first();

                         
                        if(sizeof($ledger)){
                            $closing_balance = $ledger->closing_balance;
                        }else{
                            $closing_balance = 0;
                        }
                        DB::table('mts_outlet_ledger')->insert(
                            [
                                'ledger_date'           => date('Y-m-d h:i:s'),
                                'customer_id'           => $payments->customer_id,
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

        return Redirect::to('/accounts-payments-verify')->with('success', 'Successfully Payment Verify.');
    }

    public function accounts_payments_rece_report()
    {
        $selectedMenu   = 'Outlet report';      // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;

        if($this->user_type_id == 6){  
            $currentMonth = date('m');
        }else{
            $currentMonth = '';
        }
 
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();  
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        }

 
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
         $outletPayment= DB::table('mts_outlet_payments')
            ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
            ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')  
            ->when($currentMonth,function($q,$currentMonth){
                $date = Carbon::now();
                return $q->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.entry_date,'%Y-%m-%d'))"), array($date->startOfMonth()->format('Y-m-d'), $date->endOfMonth()->format('Y-m-d'))); 
            })  
            ->when(($this->user_type_id !=6),function($q,$currentMonth){ 
                return $q->where('mts_outlet_payments.ack_status','CONFIRMED') ; 
            })  
            
            ->orderBy('mts_outlet_payments.transaction_id','DESC')
            ->get(); 

        return view('ModernSales::payment/account/report/outlet_credit')->with('outletList',$outletList)
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
        $customer   = $request->get('customer') ? $request->get('customer') : '';
        $payment_type   = $request->get('payment_type'); 
        $payment_from   = $request->get('payment_from'); 
        
        $outletList = DB::table('mts_customer_list')
                            ->orderBy('customer_id','asc')
                            ->get();
        
        $user_business_type=DB::select("select * from users where id='".$this->outlet_in_charge."'");
        
        foreach ($user_business_type as $type)
        {

            $user_type=$type->business_type_id;
            $outlet_name=$type->display_name;

        } 

        $outletPayment= DB::table('mts_outlet_payments')
                        ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                        ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')  

                        ->when($fromdate,function($q,$fromdate){  
                            return $q->whereDate("mts_outlet_payments.confirmed_date",'>=', $fromdate); 
                        })  
                        ->when($todate,function($q,$todate){  
                            return $q->whereDate("mts_outlet_payments.confirmed_date",'<=', $todate); 
                        })  
                        // ->when(($this->user_type_id !=6),function($q,$customer){ 
                        //     return $q->where('mts_outlet_payments.ack_status','CONFIRMED') ; 
                        // })
                        ->when(($customer),function($q,$customer){ 
                            return $q->where('mts_outlet_payments.customer_id',$customer); 
                        })
                        ->when(($payment_type),function($q,$payment_type){ 
                            return $q->where('mts_outlet_payments.payment_type',$payment_type); 
                        })
                        ->when(($payment_from),function($q,$payment_from){ 
                            return $q->where('mts_outlet_payments.payment_from',$payment_from); 
                        })
                        ->get(); 

        return view('ModernSales::payment/account/report/outlet_credit_list')->with('outletList',$outletList)
        ->with('outletPayment',$outletPayment)
        ->with('user_business_type',$user_type)
        ->with('outlet_name',$outlet_name)
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle);

    }
    

    public function accounts_payments_rece_report_list2(Request $request)
    {
        $selectedMenu   = 'Outlet report';         // Required Variable
        $pageTitle      = 'Payment List';       // Required Variable
        
        
        $this->outlet_in_charge  = Auth::user()->id;
        $this->user_type_id     = Auth::user()->user_type_id;
         
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $customer   = $request->get('customer');
        $payment_type   = $request->get('payment_type');
        
        $outletList = DB::table('mts_customer_list')
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
 

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','CONFIRMED')
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.confirmed_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type=='')
        {

            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','CONFIRMED')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.confirmed_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get(); 


        }
        elseif($fromdate!='' && $todate!='' && $customer!='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','CONFIRMED')
                       ->where('mts_outlet_payments.customer_id',$customer)
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.confirmed_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        elseif($fromdate!='' && $todate!='' && $customer=='' && $payment_type!='')
        {
            $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id') 
                       ->where('mts_outlet_payments.ack_status','CONFIRMED')
                       ->where('mts_outlet_payments.payment_type',$payment_type)
                       ->whereBetween(DB::raw("(DATE_FORMAT(mts_outlet_payments.confirmed_date,'%Y-%m-%d'))"), array($fromdate, $todate))  
                        ->get();  

        }
        

        return view('ModernSales::payment/account/report/outlet_credit_list')->with('outletList',$outletList)
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

         $outletList = DB::table('mts_customer_list') 
                        ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
                        ->where('mts_customer_list.status',0)
                        ->orderBy('mts_customer_list.name','ASC')    
                        ->get();

        $bankList = DB::table('tbl_master_bank')
                        ->where('status',0)
                        ->orderBy('bank_name','asc')
                        ->get();

       $outletPayment= DB::table('mts_outlet_payments')
                       ->JOIN('mts_customer_list','mts_customer_list.customer_id','mts_outlet_payments.customer_id')
                       ->leftjoin('tbl_master_bank','tbl_master_bank.id','=','mts_outlet_payments.bank_info_id')
                       ->where('transaction_id', $request->get('paymentid'))->first();

       // dd($request->get('paymentid'));
        
        return view('ModernSales::payment/admin/editPayment')
        ->with('selectedMenu',$selectedMenu)
        ->with('pageTitle',$pageTitle)
        ->with('outletList',$outletList)
        ->with('bankList',$bankList)
        ->with('outletPayment',$outletPayment);
    }
    public function app_admin_payment_edit_submit(Request $request)
    { 

        DB::table('mts_outlet_payments')->where('transaction_id', $request->get('id'))->update(
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
        return Redirect::to('/admin_payments_con')->with('success', 'Successfully Payment Update.');

        
    }
    
} 
