<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Hash;
use DB;
use Auth;
use Session;
use Excel;

class MasterUploadController extends Controller
{
    // --- Sharif start target file upload -- //


  public function modern_target_list()
  {
        $selectedMenu    = 'Targer file upload';            // Required Variable for menu
        $selectedSubMenu = 'Targer file upload';           // Required Variable for menu
        $pageTitle       = 'Targer file upload';          // Page Slug Title 

        $targetList  = DB::table('mts_target_upload') 
        ->select('mts_customer_list.name as cusname','users.display_name','mts_target_upload.*')
        ->join('mts_customer_list', 'mts_customer_list.sap_code', '=', 'mts_target_upload.customer_id')
        ->join('users', 'users.id', '=', 'mts_target_upload.officer_id') 
        ->where('mts_target_upload.month', date('m'))
        ->where('mts_target_upload.year', date('Y'))
        ->where('mts_target_upload.manager_id', Auth::user()->id) 
        ->orderBy('mts_target_upload.id','DESC')                    
        ->get();

         $executivelist = DB::table('mts_role_hierarchy')
          ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')     
          ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
          ->groupBy('mts_role_hierarchy.executive_id')
          ->get();

        $MonthList = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July',
          '8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');

        return view('ModernSales::sales/target/fo_target_upload' , compact('selectedMenu','selectedSubMenu','pageTitle','targetList','channelName','resultDivision','MonthList','executivelist'));
  }

  public function modern_target_search(Request $request)
    {
      $year = $request->get('year');
      $month = $request->get('month'); 
      $executive  = $request->get('executive_id');
      $officer    = $request->get('fos');

    

      if($year!='' && $month!='' && $executive=='' && $officer=='')
      { 

        $targetList = DB::table('mts_target_upload')  
        ->select('mts_customer_list.name as cusname','users.display_name','mts_target_upload.*')
        ->join('mts_customer_list', 'mts_customer_list.sap_code', '=', 'mts_target_upload.customer_id') 
        ->join('users', 'users.id', '=', 'mts_target_upload.officer_id') 
        ->where('mts_target_upload.manager_id', Auth::user()->id) 
        ->where('mts_target_upload.month',$month)
      ->where('mts_target_upload.year', $year)
      ->orderBy('mts_target_upload.id','DESC')                    
      ->get();

      }
          
      elseif($year!='' && $month!='' && $executive!='' && $officer=='')
      {
        $targetList = DB::table('mts_target_upload')  
        ->select('mts_customer_list.name as cusname','users.display_name','mts_target_upload.*')
        ->join('mts_customer_list', 'mts_customer_list.sap_code', '=', 'mts_target_upload.customer_id') 
        ->join('users', 'users.id', '=', 'mts_target_upload.officer_id') 
        ->where('mts_target_upload.month',$month)
        ->where('mts_target_upload.year', $year) 
        ->where('mts_target_upload.manager_id', Auth::user()->id) 
        ->where('mts_target_upload.executive_id', $executive) 
        ->orderBy('mts_target_upload.id','DESC')                    
        ->get();
      } 

      elseif($year!='' && $month!='' && $executive!='' && $officer!='')
      {
        $targetList = DB::table('mts_target_upload')  
        ->select('mts_customer_list.name as cusname','users.display_name','mts_target_upload.*')
        ->join('mts_customer_list', 'mts_customer_list.sap_code', '=', 'mts_target_upload.customer_id') 
        ->join('users', 'users.id', '=', 'mts_target_upload.officer_id') 
        ->where('mts_target_upload.month',$month)
        ->where('mts_target_upload.year', $year)
        ->where('mts_target_upload.manager_id', Auth::user()->id) 
        ->where('mts_target_upload.executive_id', $executive)
        ->where('mts_target_upload.officer_id', $officer) 
        ->orderBy('mts_target_upload.id','DESC')                    
        ->get();
      }      

      return view('ModernSales::sales/target/targetList' , compact('targetList'));
  }
 


  public function modernTargetUpload(Request $request)
    {
        if($request->file('imported-file'))
        {
          $path = $request->file('imported-file')->getRealPath();
          $data = Excel::load($path, function($reader) {})->get();
 
			
			if(!empty($data) && $data->count()){

       
        $data = $data->toArray();
        for($i=0;$i<count($data);$i++)
        {

          $cus_info = DB::table('mts_customer_list') 
          ->join('mts_customer_define_executive', 'mts_customer_define_executive.customer_id', '=', 'mts_customer_list.customer_id')
          ->join('mts_role_hierarchy', 'mts_role_hierarchy.officer_id', '=', 'mts_customer_define_executive.executive_id') 
          ->where('mts_customer_list.sap_code', $data[$i]['customer_id'])
          ->groupBy('mts_customer_list.sap_code')                    
          ->first(); 

          if (sizeof($cus_info)>0) {
            
           $insert = DB::table('mts_target_upload')->insert(
                [
                    'management_id'               => $cus_info->management_id,
                    'manager_id'                  => $cus_info->manager_id,
                    'executive_id'                => $cus_info->executive_id,
                    'officer_id'                  => $cus_info->officer_id,
                    'customer_id'                 => $data[$i]['customer_id'],
                    'year'                        => $data[$i]['year'],
                    'month'                       => $data[$i]['month'], 
                    'value'                       => $data[$i]['value'],
                    'created_by'                  => Auth::user()->id,
                    'created_at'                  => date('Y-m-d h:i:s')
                    
                ]
            );
          }
  
          
           

          // $insert[] = ['division_id'=> $data[$i]['division_id'], 'territory_id'=> $data[$i]['territory_id'], 'point_id'=> $data[$i]['point_id'], 'employee_id'=> $data[$i]['employee_id'],'global_company_id' =>Auth::user()->global_company_id, 'cat_id' => $data[$i]['cat_id'], 'cat_name' => $data[$i]['cat_name'], 'qty' => $data[$i]['qty'], 'avg_value' => $data[$i]['avg_value'], 'total_value' => $data[$i]['total_value'], 'start_date' => $sDate,'end_date' => $eDate,'created_by' => Auth::user()->id];
          
        }
        
        if(!empty($insert)){ 
         return back()->with('success','Target upload sucessfully Test.');
       }


     }

     
   }

   return back()->with('error','Please Check your file, Something is wrong there.');

 }

 public function modern_target_edit(Request $request)
 {

      $selectedMenu    = 'Targer file upload';                    // Required Variable for menu
      $selectedSubMenu = 'Targer file upload';           // Required Variable for menu
      $pageTitle       = 'Targer file upload'; // Page Slug Title
        
      $targetid=$request->get('targetid'); 

      $customerList  = DB::table('mts_customer_list')
        ->where('status',0)
      ->get();

      $monthList  = DB::table('mts_month') 
      ->where('status',0)
      ->get();

      $targetList  = DB::table('mts_target_upload')
      ->where('id',$targetid)
      ->first();

      // dd($targetList);
      return view('ModernSales::sales/target/fo_target_edit',compact('selectedMenu','selectedSubMenu','pageTitle','targetList','customerList','monthList')); 

    }

    public function modern_target_edit_process(Request $request){ 
      
      DB::table('mts_target_upload')->where('id',$request->get('id'))->update(
          [
              'year'              => $request->get('year'),
              'month'             => $request->get('month'),
              'customer_id'       => $request->get('customer_id'),
              'value'             => $request->get('value'), 
              'updated_by'        => Auth::user()->id 
          ]
      ); 
      return back()->with('success','Target upload sucessfully.');
      //return redirect('Master/fo_target_upload')->with('success','Target Update sucessfully.');
      
    }


    public function mts_target_delete($id)
    { 

      $checkOrder     = DB::table('mts_target_upload')                       
                      ->where('id',$id)                        
                      ->delete();

      return back()->with('success','Target Delete sucessfully.'); 
    }

    public function downloadCustomers(Request $request){

      $tblProduct = DB::table('mts_customer_list')->get();  
      $data = array();
      foreach ($tblProduct as $items) {  
          $data[] = [ 
              'customer_id'  => $items->customer_id,
              'name'         => $items->name,
              'last_balance' => 0
          ];
      }
      Excel::create('customer_list', function($excel) use($data) {
          $excel->sheet('ExportFile', function($sheet) use($data) {
              $sheet->fromArray($data);
          });
      })->export('csv');
    }

    public function customerBalanceUpload(Request $request){
      $selectedMenu    = 'Customer Last Balance Upload';            // Required Variable for menu
      $selectedSubMenu = 'Customer Last Balance Upload';           // Required Variable for menu
      $pageTitle       = 'Customer Last Balance Upload';          // Page Slug Title 
      $customer_id = $request->get('customer_id'); 
      $fromdate   = $request->get('fromdate') ? date('Y-m-d', strtotime($request->get('fromdate'))) : date('Y-m-d');
      $todate   = $request->get('todate') ? date('Y-m-d', strtotime($request->get('todate'))) : date('Y-m-d'); 
      $balance_list = DB::table('customer_ledger_balance')       
                    ->join('mts_customer_list', 'customer_ledger_balance.customer_id', '=', 'mts_customer_list.customer_id')                
                    ->when($customer_id, function($q, $customer_id){
                      return $q->where('customer_ledger_balance.customer_id', $customer_id);
                    })
                    ->whereBetween(DB::raw("(DATE_FORMAT(customer_ledger_balance.submitted_date,'%Y-%m-%d'))"), array($todate, $todate))     
                    ->get(); 
      return view('ModernSales::sales/customer-ledger/balance-upload' , compact('selectedMenu','selectedSubMenu','pageTitle','balance_list'));
    }

    public function customerBalanceUploadSubmit(Request $request){
      if($request->file('imported-file')) {
        $path = $request->file('imported-file')->getRealPath();
        $data = Excel::load($path, function($reader) {})->get(); 
      
        if(!empty($data) && $data->count()){       
          $data = $data->toArray(); 
          for($i=0; $i < count($data); $i++) {              
          $insert = DB::table('customer_ledger_balance')->insert([ 
                'customer_id'    => $data[$i]['customer_id'], 
                'last_balance'   => $data[$i]['last_balance'],
                'entry_by'       => Auth::user()->id,
                'submitted_date' => date('Y-m-d h:i:s')                
            ]);
          } 
        }          
        if(!empty($insert)){ 
          return back()->with('success','Target upload sucessfully Test.');
        }
      }
    }
    
    public function customerBalanceFilter(Request $request){
      $selectedMenu    = 'Customer Last Balance Upload';            // Required Variable for menu
      $selectedSubMenu = 'Customer Last Balance Upload';           // Required Variable for menu
      $pageTitle       = 'Customer Last Balance Upload';          // Page Slug Title  

      $customer_id = $request->get('customer_id'); 
      $fromdate   = $request->get('fromdate') ? date('Y-m-d', strtotime($request->get('fromdate'))) : date('Y-m-d');
      $todate   = $request->get('todate') ? date('Y-m-d', strtotime($request->get('todate'))) : date('Y-m-d'); 
      
      $balance_list = DB::table('customer_ledger_balance')       
                    ->join('mts_customer_list', 'customer_ledger_balance.customer_id', '=', 'mts_customer_list.customer_id')                
                    ->when($customer_id, function($q, $customer_id){
                      return $q->where('customer_ledger_balance.customer_id', $customer_id);
                    })
                    ->whereBetween(DB::raw("(DATE_FORMAT(customer_ledger_balance.submitted_date,'%Y-%m-%d'))"), array($todate, $todate))     
                    ->get();
      return view('ModernSales::sales/customer-ledger/balance-list' , compact('selectedMenu','selectedSubMenu','pageTitle','balance_list'));
    }

      
  }
