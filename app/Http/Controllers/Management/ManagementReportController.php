<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class ManagementReportController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 1/04/2018
    *
    **/

    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_management()
    {
        $selectedMenu   = 'Management Report';                     // Required Variable for menu
        $selectedSubMenu= 'Management Report';                    // Required Variable for submenu
        $pageTitle      = 'Management Report';                   // Page Slug Title

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        //dd($resultFO);

        $businessType   = DB::table('tbl_business_type')->where('global_company_id', Auth::user()->global_company_id)->get();
        $division       = DB::table('tbl_division')->get();
        //$division       = DB::table('tbl_division')->get();


        $today             = date('Y-m-d');
        $yesterday         = date('Y-m-d',strtotime('-1 Day'));
        $sameDayLastMonth  = date('Y-m-d',strtotime('-1 Month'));
        $currentMonthStart = date('Y-m'.'-01');
        $currentMonthEnd   = date('Y-m'.'-31');

        $lastMonthStart = date('Y-m'.'-01',strtotime('-1 Month'));
        $lastMonthEnd   = date('Y-m'.'-31',strtotime('-1 Month'));

        //All Target

        $todayTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('total_value');

        $yesterdayTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('total_value');

        $sameDayLastMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$lastMonthStart)
                    ->whereDate('end_date','<=',$lastMonthEnd)
                    ->sum('total_value');

        /*$currentMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('total_value');*/

         $currentMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$currentMonthStart)
                    ->whereDate('end_date','<=',$currentMonthEnd)
                    ->sum('total_value');

        $lastMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$lastMonthStart)
                    ->whereDate('end_date','<=',$lastMonthEnd)
                    ->sum('total_value');


        //All Sales (Primary)

        $todayPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($today ,$today))
                        ->sum('drd.received_value');

        $yesterdayPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                        ->sum('drd.received_value');

        $sameDayLastMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                        ->sum('drd.received_value');

        $currentMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                        ->sum('drd.received_value');

        $lastMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                        ->sum('drd.received_value');


        //All Collection

        $todayCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('collection_amount');

        $yesterdayCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('collection_amount');

        $sameDayLastMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->sum('collection_amount');

        $currentMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('collection_amount');

        $lastMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->sum('collection_amount');

        //All Memo QTY

        $todayMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->count('order_id');

        $yesterdayMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->count('order_id');

        $sameDayLastMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->count('order_id');

        $currentMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->count('order_id');

        $lastMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->count('order_id');


        //All Memo Ave

        $todayMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->avg('total_value');

        $yesterdayMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->avg('total_value');

        $sameDayLastMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->avg('total_value');

        $currentMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->avg('total_value');

        $lastMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->avg('total_value');


        // All Credit

        $retailersOpeningBalance = DB::table('tbl_retailer')
                    ->where('global_company_id', Auth::user()->global_company_id)                    
                    ->sum('opening_balance');



        //All Secondary Sales

        $todaySecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('total_delivery_value');

        $yesterdaySecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('total_delivery_value');

        $sameDayLastMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->sum('total_delivery_value');

        $currentMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('total_delivery_value');

        $lastMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->sum('total_delivery_value');

        return view('sales/mgt/mgtReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','businessType','division','todayTarget','yesterdayTarget','sameDayLastMonthTarget','currentMonthTarget','lastMonthTarget','todayPrimarySales','yesterdayPrimarySales','sameDayLastMonthPrimarySales','currentMonthPrimarySales','lastMonthPrimarySales','todayCollection','yesterdayCollection','sameDayLastMonthCollection','currentMonthCollection','lastMonthCollection','todayMemo','yesterdayMemo','sameDayLastMonthMemo','currentMonthMemo','lastMonthMemo','todayMemoAve','yesterdayMemoAve','sameDayLastMonthMemoAve','currentMonthMemoAve','lastMonthMemoAve', 'todaySecondarySales','yesterdaySecondarySales','sameDayLastMonthSecondarySales','currentMonthSecondarySales','lastMonthSecondarySales','retailersOpeningBalance'));
    }

    
    public function filtering(Request $request)
    {

        $today             = date('Y-m-d',strtotime($request->get('todate'))); 
                            //date('Y-m-d');

        $yesterday         = date('Y-m-d', strtotime('-1 Day', strtotime($today)));
                            //date('Y-m-d',strtotime('-1 Day'));

        $sameDayLastMonth  = date('Y-m-d', strtotime('-1 Month', strtotime($today))); 
                            //date('Y-m-d',strtotime('-1 Month'));

        $currentMonthStart = date('Y-m'.'-01',strtotime($request->get('todate'))); 
                            //date('Y-m'.'-01');

        $currentMonthEnd   = $today; //date('Y-m'.'-31');

        $lastMonthStart = date('Y-m'.'-01', strtotime('-1 Month', strtotime($today))); 
                          //date('Y-m'.'-01',strtotime('-1 Month'));

        $lastMonthEnd   = date('Y-m'.'-31', strtotime('-1 Month', strtotime($today))); 
                          //date('Y-m'.'-31',strtotime('-1 Month'));


        // echo 'yesterday : '.$yesterday.'<br />';
        // echo 'sameDayLastMonth : '.$sameDayLastMonth.'<br />';

        // echo 'currentMonthStart : '.$currentMonthStart.'<br />';
        // echo 'currentMonthEnd : '.$currentMonthEnd.'<br />';
        // echo 'lastMonthStart : '.$lastMonthStart.'<br />';
        // echo 'lastMonthEnd : '.$lastMonthEnd.'<br />';
        // exit();

        //All Target

        $todayTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('total_value');

        $yesterdayTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('total_value');

        $sameDayLastMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$lastMonthStart)
                    ->whereDate('end_date','<=',$lastMonthEnd)
                    ->sum('total_value');

        /*$currentMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(start_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('total_value');*/

         $currentMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$currentMonthStart)
                    ->whereDate('end_date','<=',$currentMonthEnd)
                    ->sum('total_value');

        $lastMonthTarget = DB::table('tbl_fo_target')
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->whereDate('start_date','>=',$lastMonthStart)
                    ->whereDate('end_date','<=',$lastMonthEnd)
                    ->sum('total_value');


        //All Sales (Primary)

        $todayPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($today ,$today))
                        ->sum('drd.received_value');

        $yesterdayPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                        ->sum('drd.received_value');

        $sameDayLastMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                        ->sum('drd.received_value');

        $currentMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                        ->sum('drd.received_value');

        $lastMonthPrimarySales = DB::table('depot_req_details AS drd')
                        ->select('drd.req_id','drd.received_value','drd.received_qnty','depot_requisition.received_date','depot_requisition.global_company_id')
                        ->leftJoin('depot_requisition','drd.req_id','=','depot_requisition.req_id')
                        ->where('depot_requisition.global_company_id', Auth::user()->global_company_id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.received_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                        ->sum('drd.received_value');


        //All Collection

        $todayCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('collection_amount');

        $yesterdayCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('collection_amount');

        $sameDayLastMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->sum('collection_amount');

        $currentMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('collection_amount');

        $lastMonthCollection = DB::table('depot_collection')
                    ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->sum('collection_amount');

        //All Memo QTY

        $todayMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->count('order_id');

        $yesterdayMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->count('order_id');

        $sameDayLastMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->count('order_id');

        $currentMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->count('order_id');

        $lastMonthMemo = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->count('order_id');


        //All Memo Ave

        $todayMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->avg('total_value');

        $yesterdayMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->avg('total_value');

        $sameDayLastMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->avg('total_value');

        $currentMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->avg('total_value');

        $lastMonthMemoAve = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->avg('total_value');


        // All Credit

        $retailersOpeningBalance = DB::table('tbl_retailer')
                    ->where('global_company_id', Auth::user()->global_company_id)                    
                    ->sum('opening_balance');

        

        //All Secondary Sales

        $todaySecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($today ,$today))
                    ->sum('total_delivery_value');

        $yesterdaySecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($yesterday ,$yesterday))
                    ->sum('total_delivery_value');

        $sameDayLastMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($sameDayLastMonth ,$sameDayLastMonth))
                    ->sum('total_delivery_value');

        $currentMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMonthStart ,$currentMonthEnd))
                    ->sum('total_delivery_value');

        $lastMonthSecondarySales = DB::table('tbl_order')
                    ->whereIn('order_type', array('Delivered'))
                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($lastMonthStart ,$lastMonthEnd))
                    ->sum('total_delivery_value');

        return view('sales/mgt/filtering', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','businessType','division','todayTarget','yesterdayTarget','sameDayLastMonthTarget','currentMonthTarget','lastMonthTarget','todayPrimarySales','yesterdayPrimarySales','sameDayLastMonthPrimarySales','currentMonthPrimarySales','lastMonthPrimarySales','todayCollection','yesterdayCollection','sameDayLastMonthCollection','currentMonthCollection','lastMonthCollection','todayMemo','yesterdayMemo','sameDayLastMonthMemo','currentMonthMemo','lastMonthMemo','todayMemoAve','yesterdayMemoAve','sameDayLastMonthMemoAve','currentMonthMemoAve','lastMonthMemoAve', 'todaySecondarySales','yesterdaySecondarySales','sameDayLastMonthSecondarySales','currentMonthSecondarySales','lastMonthSecondarySales','retailersOpeningBalance'));
    }
}
