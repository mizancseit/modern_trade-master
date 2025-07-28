<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class tsmReportController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 10/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    /* 
       =====================================================================
       ============================ Order  =================================
       =====================================================================
    */    

    
    //MAUNG
    public function folist()
    {
      $selectedMenu   = 'FO List Report';                       // Required Variable for menu
      $selectedSubMenu= 'FO List Report';                    // Required Variable for submenu
      $pageTitle      = 'FO List Report'; 
      $fotopten=DB::select("select *,sum(grand_total_value) total from tbl_order where order_type='Ordered' group by fo_id order by total desc limit 0,10 ");
      //dd($fotopten);
      // echo"maung"; exit;

      //folist

      $user_id=Auth::user()->id;
      $user=DB::table('tbl_user_business_scope')
      ->where('user_id', $user_id)
      ->first();

      $folist= DB::select("select  tbl_user_business_scope.user_id,tbl_user_business_scope.user_id,tbl_user_business_scope.point_id,tbl_user_business_scope.territory_id,
      users.id,users.display_name,users.designation,users.user_type_id,users.email,users.sap_code,tbl_user_details.cell_phone,tbl_user_details.email 

      from tbl_user_business_scope,users,tbl_user_details
      where tbl_user_business_scope.user_id=users.id  and tbl_user_business_scope.user_id=tbl_user_details.user_id and tbl_user_business_scope.territory_id=
      $user->territory_id 
      and users.user_type_id=12");

      //dd($folist);
      //Folist                      
      return view('sales/report/tsm/folist')->with('selectedMenu',$selectedMenu)->with('selectedSubMenu', $selectedSubMenu)
      ->with('pageTitle',$pageTitle)->with('fotopten',$fotopten)->with('folist',$folist);
    } 


           // Page Slug Title

    //MAUNg


    public function tsmDist()
   {

 $selectedMenu   = 'Dist Report';                       // Required Variable for menu
 $selectedSubMenu= 'Dist Report';                    // Required Variable for submenu
 $pageTitle      = 'Dist Report'; 

  $user_id=Auth::user()->id;
             $user=DB::table('tbl_user_business_scope')
                                ->where('user_id', $user_id)
                                ->first();

$dist=DB::select("select  tbl_user_business_scope.user_id,users.email as sap,tbl_user_business_scope.user_id,tbl_user_business_scope.point_id,tbl_user_business_scope.territory_id,
        users.id,users.display_name,users.designation,users.user_type_id,users.email,users.sap_code,tbl_user_details.cell_phone,tbl_user_details.email 

            from tbl_user_business_scope,users,tbl_user_details
             where tbl_user_business_scope.user_id=users.id  and tbl_user_business_scope.user_id=tbl_user_details.user_id and tbl_user_business_scope.territory_id=
             $user->territory_id 
                    and users.user_type_id=5");
return view('sales.report.tsm.distlist')->with('selectedMenu',$selectedMenu)->with('selectedSubMenu', $selectedSubMenu)
                                     ->with('pageTitle',$pageTitle)->with('dist',$dist);

   }

   public function foaAttendance()
   {

     $selectedMenu   = 'FO Attendance';                       // Required Variable for menu
     $selectedSubMenu= 'FO Attendance';                    // Required Variable for submenu
     $pageTitle      = 'FO Attendance'; 

      $user_id=Auth::user()->id;
      $user = DB::table('tbl_user_business_scope')
            ->where('user_id', $user_id)
            ->first();

      $todate = date('Y-m-d');

      $attendance = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','users.display_name')

                        ->join('tbl_user_business_scope', 'ia.foid', '=', 'tbl_user_business_scope.user_id')
                        ->leftJoin('users', 'ia.foid', '=', 'users.id')

                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.type', 1)
                        ->where('tbl_user_business_scope.territory_id', $user->territory_id)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        //->groupBy('ia.foid')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

      //dd($attendance);

      $territoryFO = DB::table('users')
                    ->select('users.*','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.point_id','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')

                    ->leftJoin('tbl_user_business_scope','users.id', '=', 'tbl_user_business_scope.user_id')
                    ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')

                    ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
                    ->where('users.is_active', 0)
                    ->where('users.user_type_id', 12) // FO
                    ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO
                    ->orderBy('users.display_name')                    
                    ->get();

      //dd($territoryFO);


    //dd($foattend);
    return view('sales/report/tsm/foattendance',compact('selectedMenu','selectedSubMenu','pageTitle','attendance','territoryFO'));

   }


   public function foaAttendanceList(Request $request)
   {

      $user_id=Auth::user()->id;
      $user = DB::table('tbl_user_business_scope')
            ->where('user_id', $user_id)
            ->first();

      $todate = date('Y-m-d');

      $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
      $todate     = date('Y-m-d', strtotime($request->get('todate')));



      if($fromdate!='' && $todate!='' && $request->get('foID')=='')
      {

          $attendance = DB::table('ims_attendence AS ia')
            ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','users.display_name')

            ->join('tbl_user_business_scope', 'ia.foid', '=', 'tbl_user_business_scope.user_id')
            ->leftJoin('users', 'ia.foid', '=', 'users.id')

            ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
            ->where('ia.type', 1)
            ->where('tbl_user_business_scope.territory_id', $user->territory_id)
            ->whereBetween('ia.date', array($fromdate, $todate))
            ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
            //->groupBy('ia.foid')
            ->orderBy('ia.id','DESC')                    
            ->get();
      }
      else if($fromdate!='' && $todate!='' && $request->get('foID')!='')
      {

          $attendance = DB::table('ims_attendence AS ia')
            ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','users.display_name')

            ->join('tbl_user_business_scope', 'ia.foid', '=', 'tbl_user_business_scope.user_id')
            ->leftJoin('users', 'ia.foid', '=', 'users.id')

            ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
            ->where('ia.type', 1)
            ->where('tbl_user_business_scope.territory_id', $user->territory_id)
            ->where('ia.foid', $request->get('foID'))
            ->whereBetween('ia.date', array($fromdate, $todate))
            ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
            //->groupBy('ia.foid')
            ->orderBy('ia.id','DESC')                    
            ->get();
      }
      else
      {
          $attendance = DB::table('ims_attendence AS ia')
            ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','ia.retailerid','ia.distributor','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','users.display_name')

            ->join('tbl_user_business_scope', 'ia.foid', '=', 'tbl_user_business_scope.user_id')
            ->leftJoin('users', 'ia.foid', '=', 'users.id')

            ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
            ->where('ia.type', 1)
            ->where('tbl_user_business_scope.territory_id', $user->territory_id)
            ->whereBetween('ia.date', array($todate, $todate))
            ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
            //->groupBy('ia.foid')
            ->orderBy('ia.id','DESC')                    
            ->get();   

      }


      return view('sales/report/tsm/foattendance_search',compact('attendance'));

   }


   public function retailer()
   {

     $selectedMenu   = 'FO Attendance';                       // Required Variable for menu
     $selectedSubMenu= 'FO Attendance';                    // Required Variable for submenu
     $pageTitle      = 'FO Wise Retiler List';

     $user_id=Auth::user()->id;
     $user = DB::table('tbl_user_business_scope')
            ->where('user_id', $user_id)
            ->first();


      $territoryFO = DB::table('users')
                    ->select('users.*','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.point_id','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')

                    ->leftJoin('tbl_user_business_scope','users.id', '=', 'tbl_user_business_scope.user_id')
                    ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')

                    ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
                    ->where('users.is_active', 0)
                    ->where('users.user_type_id', 12) // FO
                    ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO

                    //->where('tbl_user_business_scope.territory_id', $user->territory_id)
                    ->orderBy('users.display_name')                    
                    ->get(); 

      // $territoryFO = DB::table('users')
      //               ->select('users.*','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id')

      //               ->leftJoin('tbl_user_business_scope','users.id', '=', 'tbl_user_business_scope.user_id')

      //               ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
      //               ->where('users.is_active', 0)
      //               ->where('users.user_type_id', 12) // FO
      //               ->where('tbl_user_business_scope.territory_id', $user->territory_id)
      //               ->orderBy('users.display_name')                    
      //               ->get();

      //dd($territoryFO);

    return view('sales/report/tsm/retailer',compact('selectedMenu','selectedSubMenu','pageTitle','territoryFO'));

   }


   public function retailerList(Request $request)
   {

      $user_id=Auth::user()->id;
      $user = DB::table('tbl_user_business_scope')
            ->where('user_id', $user_id)
            ->first();

      if($request->get('foID')!='')
      {
          $allFo = DB::table('users')
                  ->select('users.*','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id')
                  ->where('users.is_active', 0)
                  ->where('users.user_type_id', 12) // FO
                  ->leftJoin('tbl_user_business_scope','users.id','=','tbl_user_business_scope.user_id')
                  ->where('tbl_user_business_scope.territory_id', $user->territory_id)
                  ->where('users.id',$request->get('foID'))
                  ->get();
      }

      return view('sales/report/tsm/retailer_search',compact('allFo'));

   }


   public function foWiseReport()
   {

     $selectedMenu   = 'FO Wise Report';                       // Required Variable for menu
     $selectedSubMenu= 'FO Wise Report';                    // Required Variable for submenu
     $pageTitle      = 'FO Wise Report';

     $user_id=Auth::user()->id;
     $user = DB::table('tbl_user_business_scope')
            ->where('user_id', $user_id)
            ->first(); 

      // $territoryFO = DB::table('users')
      //               ->select('users.*','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id')

      //               ->leftJoin('tbl_user_business_scope','users.id', '=', 'tbl_user_business_scope.user_id')

      //               ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
      //               ->where('users.is_active', 0)
      //               ->where('users.user_type_id', 12) // FO
      //               ->where('tbl_user_business_scope.territory_id', $user->territory_id)
      //               ->orderBy('users.display_name')                    
      //               ->get();

       $territoryFO = DB::table('users')
                    ->select('users.*','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.point_id','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')

                    ->leftJoin('tbl_user_business_scope','users.id', '=', 'tbl_user_business_scope.user_id')
                    ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')

                    ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)
                    ->where('users.is_active', 0)
                    ->where('users.user_type_id', 12) // FO
                    ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO

                    //->where('tbl_user_business_scope.territory_id', $user->territory_id)
                    ->orderBy('users.display_name')                    
                    ->get();

      //dd($territoryFO);

    return view('sales/report/tsm/fo_wise_report',compact('selectedMenu','selectedSubMenu','pageTitle','territoryFO'));

   }



   public function db_wise_requisition()
   {

      $selectedMenu   = 'DB Wise Requisition Report';                       // Required Variable for menu
      $selectedSubMenu= 'DB Wise Requisition Report';                    // Required Variable for submenu
      $pageTitle      = 'DB Wise Requisition Report'; 

      $user_id=Auth::user()->id;
      // $user   = DB::table('tbl_user_business_scope')
      // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
      // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
      // ->where('tbl_user_business_scope.user_id', $user_id)
      // ->first();

      //dd($user);

      // $point = DB::table('tbl_point')
      // ->where('point_division', $user->div_id)
      // ->where('territory_id', $user->territory_id)
      // ->orderBy('point_name')                    
      // ->get();


      // Supervisor Table

      $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();

      return view('sales/report/tsm/db_wise_requisition',compact('selectedMenu','selectedSubMenu','pageTitle','user','point','channelName','divisionName'));

   }


   public function db_wise_requisition_list(Request $request)
   {

      $divistion  = $request->get('divistion');
      $points     = $request->get('points');

      if($divistion!='' && $points!='')
      {
        $reports = DB::table('depot_requisition')            
        ->where('point_id', $points)
        ->orderBy('req_id','DESC')                    
        ->get();
      }
      return view('sales/report/tsm/db_wise_requisition_list',compact('reports'));
   }


   public function retailer_ledger()
   {

      $selectedMenu   = 'Retailer Ledger Report';                       // Required Variable for menu
      $selectedSubMenu= 'Retailer Ledger Report';                    // Required Variable for submenu
      $pageTitle      = 'Retailer Ledger Report'; 

      $user_id=Auth::user()->id;
      // $user   = DB::table('tbl_user_business_scope')
      // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
      // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
      // ->where('tbl_user_business_scope.user_id', $user_id)
      // ->first();

      // $point = DB::table('tbl_point')
      // ->where('point_division', $user->div_id)
      // ->where('territory_id', $user->territory_id)
      // ->orderBy('point_name')                    
      // ->get();

      $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();

      return view('sales/report/tsm/retailer_ledger',compact('selectedMenu','selectedSubMenu','pageTitle','user','point','channelName','divisionName'));

   }


   public function retailer_ledger_list(Request $request)
   {
      $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
      $todate     = date('Y-m-d', strtotime($request->get('todate')));
      $divistion  = $request->get('divistion');
      $points     = $request->get('points');
      $route_id   = $request->get('route_id');


      if($points!='' && $route_id!='')
      {
          $reports = DB::table('tbl_retailer')
            ->select('rid','point_id','retailer_id','name','opening_balance')
            ->where('rid', $route_id)
            ->orderBy('name')                    
            ->get();

          $retOpenTot      = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer where rid = '".$route_id."'");       

          $depoTotSales    = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order
          where order_type = 'Delivered' and route_id = '".$route_id."'");

          $depoTotRetSales = DB::select("
            SELECT SUM(rled.retailer_sales_return) totSalesReturn
            FROM  retailer_credit_ledger rled JOIN tbl_retailer ret 
            ON rled.retailer_id = ret.retailer_id      
            where ret.rid = '".$route_id."'
          ");                       

          $depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection where route_id = '".$route_id."'");
      }
      else // Point wise
      {
          $reports = DB::table('tbl_retailer')
            ->select('rid','point_id','retailer_id','name','opening_balance')
            ->where('point_id', $points)
            ->orderBy('name')                    
            ->get();

          $retOpenTot      = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer where point_id = '".$points."'");       

          $depoTotSales    = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order
          where order_type = 'Delivered' and point_id = '".$points."'");

          $depoTotRetSales = DB::select("
            SELECT SUM(rled.retailer_sales_return) totSalesReturn
            FROM  retailer_credit_ledger rled JOIN tbl_retailer ret 
            ON rled.retailer_id = ret.retailer_id      
            where ret.point_id = '".$points."'
          ");                       

          $depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection where point_id = '".$points."'");
      }

      

      return view('sales/report/tsm/retailer_ledger_list',compact('reports','retOpenTot','depoTotSales','depoTotRetSales','depoTotCollection'));
   }


   public function daily_ims_report()
   {

      $selectedMenu   = 'Daily IMS Report';                       // Required Variable for menu
      $selectedSubMenu= 'Daily IMS Report';                    // Required Variable for submenu
      $pageTitle      = 'Daily IMS Report'; 

      $user_id=Auth::user()->id;
      // $user   = DB::table('tbl_user_business_scope')
      // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
      // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
      // ->where('tbl_user_business_scope.user_id', $user_id)
      // ->first();

      // $point = DB::table('tbl_point')
      // ->where('point_division', $user->div_id)
      // ->where('territory_id', $user->territory_id)
      // ->orderBy('point_name')                    
      // ->get();

      $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();

      return view('sales/report/tsm/daily_ims_report',compact('selectedMenu','selectedSubMenu','pageTitle','channelName','divisionName'));

   }


   public function daily_ims_report_list(Request $request)
   {
      $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
      $divistion  = $request->get('divistion');
      $points     = $request->get('points');
      $foID       = $request->get('foID');

      $currentMonthStart = date('Y-m'.'-01',strtotime($request->get('fromdate')));
      $currentMonthEnd2  = date('Y-m'.'-31',strtotime($request->get('fromdate')));
      $currentMonthEnd   = $fromdate;

      $sameDayLastMonth  = date('Y-m-d', strtotime('-1 Month', strtotime($fromdate)));

      $sameDayLastMonthStart  = date('Y-m'.'-01', strtotime('-1 Month', strtotime($fromdate)));
      // echo $sameDayLastMonth;
      // echo $sameDayLastMonthStart;
      // echo $currentMonthStart;
      // echo $fromdate;
      
      // exit();



      //$now = $currentMonthStart; // or your date as well
      //$your_date = strtotime($fromdate);
      //$datediff = $now - $your_date;
      $datetime1 = date_create($currentMonthStart); 
      $datetime2 = date_create($fromdate); 

      $interval = date_diff($datetime1, $datetime2);  
      $targetDays = $interval->format('%a'); 

      //echo round($datediff / (60 * 60 * 24));



      if($points!='' && $foID!='')
      {

          $allDepot = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')
        ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
        ->join('users','tbl_user_business_scope.user_id','=','users.id')
        ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')
        ->where('tbl_point.point_id', $points)
        ->where('users.user_type_id', 12) // FO
        ->where('users.id', $foID)
        ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO
        ->orderBy('tbl_point.point_name')
        ->groupBy('users.id')
        ->get();

          // $allDepot  = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email')
          // ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
          // ->join('users','tbl_user_business_scope.user_id','=','users.id')
          // ->where('tbl_point.point_id', $points)
          // ->where('users.id', $foID)
          // ->orderBy('tbl_point.point_name')
          // ->get();
      }
      else if($points!='' && $foID=='')
      {
        $allDepot = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')
        ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
        ->join('users','tbl_user_business_scope.user_id','=','users.id')
        ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')
        ->where('tbl_point.point_id', $points)
        ->where('users.user_type_id', 12) // FO
        ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO
        ->orderBy('tbl_point.point_name')
        ->groupBy('users.id')
        ->get();
      }      

      return view('sales/report/tsm/daily_ims_report_list',compact('allDepot','fromdate','points','foID','currentMonthStart','currentMonthEnd','sameDayLastMonth','sameDayLastMonthStart','currentMonthEnd2','targetDays'));
   }


   // Monthly IMS Status

   public function monthly_ims_status()
   {

      $selectedMenu   = 'Monthly IMS Status';                       // Required Variable for menu
      $selectedSubMenu= 'Monthly IMS Status';                    // Required Variable for submenu
      $pageTitle      = 'Monthly IMS Status'; 

      $user_id=Auth::user()->id;
      // $user   = DB::table('tbl_user_business_scope')
      // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
      // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
      // ->where('tbl_user_business_scope.user_id', $user_id)
      // ->first();

      // $point = DB::table('tbl_point')
      // ->where('point_division', $user->div_id)
      // ->where('territory_id', $user->territory_id)
      // ->orderBy('point_name')                    
      // ->get();

      $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();

      $MonthList = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July',
    '8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');

      return view('sales/report/tsm/monthly_ims_status',compact('selectedMenu','selectedSubMenu','pageTitle','user','point','MonthList','channelName','divisionName'));

   }


   public function monthly_ims_status_list(Request $request)
   {
      $monthStart = $request->get('monthStart');
      $monthEnd   = $request->get('monthEnd');
      $divistion  = $request->get('divistion');
      $points     = $request->get('points');
      $foID       = $request->get('foID');


      //dd($monthStart,$monthEnd);

      if($points!='' && $foID!='')
      {
          $allDepot = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')
          ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
          ->join('users','tbl_user_business_scope.user_id','=','users.id')
          ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')
          ->where('tbl_point.point_id', $points)
          ->where('users.user_type_id', 12) // FO
          ->where('users.id', $foID)
          ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO
          ->orderBy('tbl_point.point_name')
          ->groupBy('users.id')
          ->get();

          // $allDepot    = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email')
          // ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
          // ->join('users','tbl_user_business_scope.user_id','=','users.id')
          // ->where('tbl_point.point_id', $points)
          // ->where('users.id', $foID)
          // ->orderBy('tbl_point.point_name')
          // ->get();
      }
      else if($points!='' && $foID=='')
      {
        $allDepot = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email','tbl_supervisor.iSuperId','tbl_supervisor.iPointId')
          ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
          ->join('users','tbl_user_business_scope.user_id','=','users.id')
          ->join('tbl_supervisor','tbl_user_business_scope.point_id', '=', 'tbl_supervisor.iPointId')
          ->where('tbl_point.point_id', $points)
          ->where('users.user_type_id', 12) // FO
          ->where('tbl_supervisor.iSuperId', Auth::user()->id) // FO
          ->orderBy('tbl_point.point_name')
          ->groupBy('users.id')
          ->get();

        // $allDepot    = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name','users.email')
        // ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
        // ->join('users','tbl_user_business_scope.user_id','=','users.id')
        // ->where('tbl_point.point_id', $points)
        // ->orderBy('tbl_point.point_name')
        // ->get();
      }      

      return view('sales/report/tsm/monthly_ims_status_list',compact('allDepot','points','foID','monthStart','monthEnd'));
   }


   /* --------------TSM Stock -----------*/

   public function ssg_depot_stock_list()
    {

        $selectedMenu    = 'Depot';                   // Required Variable for menu
        $selectedSubMenu = 'Stock List';            // Required Variable for menu
        $pageTitle       = 'Stock List';           // Page Slug Title

         $resultDivision=DB::table('tbl_division')
        ->where('tbl_division.div_status', '=', '0')
        ->orderBy('tbl_division.div_name','ASC')  
        ->get();

        $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();

         $resultCategory = DB::table('tbl_product_category')
        ->select('id','gid','status','LAF','name','g_name','g_code','avg_price','global_company_id')
        ->where('gid', Auth::user()->business_type_id)
        ->where('status', '0')
        ->where('global_company_id', Auth::user()->global_company_id)
        ->get();

        return view('sales/report/tsm/depotStockList', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory','resultDivision','channelName','divisionName'));
    }


    public function ssg_stock_products(Request $request)
    {
        $catID = $request->get('categories');
        $pointID = $request->get('pointsID');

        if($catID!='all')
        {
            $stockResult = DB::select("SELECT ds.point_id, p.name, ds.stock_qty,p.depo, (ds.stock_qty * p.depo) as stock_value
            FROM depot_stock ds JOIN tbl_product p ON ds.product_id = p.id 
            WHERE ds.point_id = '".$pointID."' AND ds.cat_id = '".$catID."' ORDER BY p.name ASC");
            
        } 
        else 
        {
            
            $stockResult = DB::select("SELECT tbl_product.name, tbl_product.depo, sum(if(inventory_type=1,product_qty,0)) as InStock,sum(if(inventory_type=2,product_qty,0)) as OutStock, 
            (sum(if(inventory_type=1,product_qty,0))- sum(if(inventory_type=2,product_qty,0))) as stock_qty,
            sum(if(inventory_type=1,product_value,0)) as provInval,  sum(if(inventory_type=2,product_value,0)) as proOutVal,
            (sum(if(inventory_type=1,product_value,0))- sum(if(inventory_type=2,product_value,0)))  as pro_val
                        FROM depot_inventory JOIN tbl_product  ON depot_inventory.product_id = tbl_product.id
            WHERE point_id in ('".$pointID."') GROUP BY depot_inventory.product_id");            
        }
        
        return view('sales/report/tsm/allStockList', compact('stockResult','catID'));
    }


    /* 
       =====================================================================
       ========================= PG WISE REPORT  ========================
       =====================================================================
    */    

    public function pg_wise_report()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'PG Wise Report';                    // Required Variable for submenu
        $pageTitle      = 'PG Wise Report';            // Page Slug Title

        $user_id=Auth::user()->id;
        // $user   = DB::table('tbl_user_business_scope')
        // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
        // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
        // ->where('tbl_user_business_scope.user_id', $user_id)
        // ->first();

        // $point = DB::table('tbl_point')
        // ->where('point_division', $user->div_id)
        // ->where('territory_id', $user->territory_id)
        // ->orderBy('point_name')                    
        // ->get();

      $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
      ->where('business_type_id', Auth::user()->business_type_id)
      ->first();

      $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
      ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
      ->where('tbl_supervisor.iSuperId', Auth::user()->id)
      ->groupBy('tbl_supervisor.iDivId')
      ->get();
        

        $resultCategory = DB::table('tbl_product_category')->select('id','name','gid')
                            ->where('gid',Auth::user()->business_type_id)                                              
                            ->where('status',0)                                              
                            ->orderBy('name')                    
                            ->get();

        $todate     = date('Y-m-d');

        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales/report/tsm/skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList','user','point','channelName','divisionName'));
    }

    public function pg_wise_report_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $channel    = $request->get('channel');
        $divistion  = $request->get('divistion');
        $pointID    = $request->get('pointID');
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');

        //dd($fromdate,$todate,$fo,$category,$products,$divistion,$pointID);

        if($fromdate!='' && $todate!='' && $channel!='' && $divistion!='' && $pointID!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            //->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.point_id', $pointID)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.id')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        }

        else if($fromdate!='' && $todate!='' && $channel!='' && $divistion!='' && $pointID!='' && $fo!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            //->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.point_id', $pointID)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.id')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
                            
        }
        else if($fromdate!='' && $todate!='' && $channel!='' && $divistion!='' && $pointID!='' && $fo!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type','=','Delivered')
                            ->where('tbl_order.point_id', $pointID)                            
                            ->where('tbl_order.fo_id', $fo)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.id')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $channel!='' && $divistion!='' && $pointID!='' && $fo=='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            //->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.point_id', $pointID)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.id')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

            //dd($resultOrderList);
                            
        }
        
        
        return view('sales/report/tsm/skuWiseDeliveryReportList', compact('resultOrderList','pointID','fromdate','todate'));
    }



    public function monthly_fo_performance()
    {
        $selectedMenu   = 'Monthly FO Performance';         // Required Variable for menu
        $selectedSubMenu= 'Monthly FO Performance';                    // Required Variable for submenu
        $pageTitle      = 'Monthly FO Performance';       // Page Slug Title

        $user_id=Auth::user()->id;
        

        $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
        ->where('business_type_id', Auth::user()->business_type_id)
        ->first();

        $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
        ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
        ->where('tbl_supervisor.iSuperId', Auth::user()->id)
        ->groupBy('tbl_supervisor.iDivId')
        ->get();

        return view('sales/report/tsm/foPerformanceView', compact('selectedMenu','selectedSubMenu','pageTitle','channelName','divisionName'));
    }
    public function monthly_fo_performance_list(Request $request)
    {
        
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $channel    = $request->get('channel');
        $divisions  = $request->get('divisions');
        $pointsID   = $request->get('pointsID');
        $fo         = $request->get('fos');

        if($pointsID!='' && $fo!='')
        {
            $userslist = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name')
              ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
              ->join('users','tbl_user_business_scope.user_id','=','users.id')
              ->where('tbl_point.point_id', $pointsID)
              ->where('users.id', $fo)
              ->orderBy('tbl_point.point_name')
              ->get();
        }
        elseif($pointsID!='' && $fo=='')
        {
            $userslist = DB::table('tbl_point')->select('tbl_point.point_name','tbl_point.point_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','users.id','users.display_name')
              ->join('tbl_user_business_scope','tbl_point.point_id','=','tbl_user_business_scope.point_id')
              ->join('users','tbl_user_business_scope.user_id','=','users.id')
              ->where('tbl_point.point_id', $pointsID)
              ->orderBy('tbl_point.point_name')
              ->get();
        }

        return view('sales/report/tsm/foPerformanceList', compact('selectedMenu','selectedSubMenu','pageTitle','resultDivision','userslist','resultPoint','todate','fromdate'));
    }


    public function division_wise_points(Request $request)
    {
        //dd($request->get('divisions'));
        $resultPoints = DB::table('tbl_supervisor')
        ->select('tbl_supervisor.iDivId','tbl_supervisor.iSuperId','tbl_supervisor.iPointId','tbl_point.point_id','tbl_point.point_name')
        ->join('tbl_point','tbl_supervisor.iPointId','=','tbl_point.point_id')
        ->where('tbl_supervisor.iSuperId',Auth::user()->id)
        ->where('tbl_supervisor.iDivId',$request->get('divisions'))
        ->orderBy('tbl_point.point_name','ASC') 
        ->get();

        return view('sales/report/tsm/get_point_list', compact('resultPoints'));
    }

    public function point_wise_fos(Request $request)
    {

          $point_id=$request->get('point_id');

          $point_fo_list=DB::table('users')
                     ->Join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                     ->where('tbl_user_business_scope.point_id',$point_id)
                     ->where('users.user_type_id',12)
                     ->get();

        return view('sales/report/tsm/get_point_fo_list' , compact('point_fo_list'));
    }


    public function division_wise_points1(Request $request)
    {
        //dd($request->get('divisions'));
        $resultPoints = DB::table('tbl_supervisor')
        ->select('tbl_supervisor.iDivId','tbl_supervisor.iSuperId','tbl_supervisor.iPointId','tbl_point.point_id','tbl_point.point_name')
        ->join('tbl_point','tbl_supervisor.iPointId','=','tbl_point.point_id')
        ->where('tbl_supervisor.iSuperId',Auth::user()->id)
        ->where('tbl_supervisor.iDivId',$request->get('divisions'))
        ->orderBy('tbl_point.point_name','ASC') 
        ->get();

        return view('sales/report/tsm/get_point_list1', compact('resultPoints'));
    }

    public function point_wise_routes(Request $request)
    {

          $point_id=$request->get('point_id');

          $route_list=DB::table('tbl_route')
                     ->where('point_id',$point_id)
                     ->get();

        return view('sales/report/tsm/get_point_routes_list' , compact('route_list'));
    }


    
    // Fo  Daily performance report start

     public function fo_performance_report()
    {
        $selectedMenu   = 'FO Performance Report';         // Required Variable for menu
        $selectedSubMenu= '';                    // Required Variable for submenu
        $pageTitle      = 'FO Performance Report';       // Page Slug Title

        $resultDivision = DB::table('tbl_division')->get();

        $todate = date('Y-m-d');
       
        $resultPoint    = DB::table('tbl_point')
                          ->get();

        $userslist    = DB::table('users')
                        ->select('users.id','users.business_type_id','users.email','users.display_name','tbl_user_business_scope.point_id','tbl_point.point_name','tbl_user_business_scope.division_id','tbl_division.div_name')
                        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_division', 'tbl_division.div_id', '=', 'tbl_user_business_scope.division_id')
                        ->where('users.user_type_id','=',12)
                        ->where('users.is_active','=',0)
                        ->get();

          $channelName = DB::table('tbl_business_type')->select('business_type','business_type_id')     
          ->where('business_type_id', Auth::user()->business_type_id)
          ->first();

          $divisionName = DB::table('tbl_supervisor')->select('tbl_supervisor.iDivId','tbl_division.div_id','tbl_division.div_name')
          ->join('tbl_division','tbl_supervisor.iDivId','=','tbl_division.div_id')    
          ->where('tbl_supervisor.iSuperId', Auth::user()->id)
          ->groupBy('tbl_supervisor.iDivId')
          ->get();

          // $user_id=Auth::user()->id;
          // $user   = DB::table('tbl_user_business_scope')
          // ->select('tbl_user_business_scope.*','tbl_division.div_id','tbl_division.div_name')
          // ->join('tbl_division','tbl_user_business_scope.division_id','=','tbl_division.div_id')
          // ->where('tbl_user_business_scope.user_id', $user_id)
          // ->first();


        return view('sales/report/tsm/foPerformanceDailyView', compact('selectedMenu','selectedSubMenu','pageTitle','resultDivision','resultPoint','todate','userslist','channelName','divisionName'));
    }
     public function fo_performance_list(Request $request)
    {
        $selectedMenu   = 'FO Performance Report';         // Required Variable for menu
        $selectedSubMenu= '';                    // Required Variable for submenu
        $pageTitle      = 'FO Performance Report';       // Page Slug Title

        
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $channel    = $request->get('channel');
        $divisions  = $request->get('divisions');
        $pointsID   = $request->get('pointsID');
        $fo         = $request->get('fos');

        $resultDivision = DB::table('tbl_division')->get();
       
        $resultPoint    = DB::table('tbl_point')
                          ->get();
       
        if($todate!='' && $channel!='' && $divisions!='' && $pointsID!='')
        {

         $userslist    = DB::table('users')
                        ->select('users.id','users.business_type_id','users.email','users.display_name','tbl_user_business_scope.point_id','tbl_point.point_name','tbl_user_business_scope.division_id','tbl_division.div_name')
                        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_division', 'tbl_division.div_id', '=', 'tbl_user_business_scope.division_id')
                        ->where('users.business_type_id',$channel)
                        ->where('tbl_division.div_id',$divisions)
                         ->where('tbl_point.point_id',$pointsID)
                        ->where('users.user_type_id','=',12)
                        ->where('users.is_active','=',0)
                        ->orderBy('tbl_point.point_id','ASC')
                        ->get();

        }

        if($todate!='' && $channel!='' && $divisions!='' && $pointsID!='' && $fo!='')
        {

        $userslist    = DB::table('users')
                        ->select('users.id','users.business_type_id','users.email','users.display_name','tbl_user_business_scope.point_id','tbl_point.point_name','tbl_user_business_scope.division_id','tbl_division.div_name')
                        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_division', 'tbl_division.div_id', '=', 'tbl_user_business_scope.division_id')
                        ->where('users.business_type_id',$channel)
                        ->where('tbl_division.div_id',$divisions)
                        ->where('tbl_point.point_id',$pointsID)
                        ->where('users.id',$fo)
                        ->where('users.user_type_id','=',12)
                        ->where('users.is_active','=',0)
                        ->orderBy('tbl_point.point_id','ASC')
                        ->get();

        }
        return view('sales/report/tsm/foPerformanceDailyList', compact('selectedMenu','selectedSubMenu','pageTitle','resultDivision','userslist','resultPoint','todate'));
    }
}
