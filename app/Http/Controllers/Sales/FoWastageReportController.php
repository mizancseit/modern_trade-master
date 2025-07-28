<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class FoWastageReportController extends Controller
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

    public function wastage_report_fo_order()
    {
        $selectedMenu   = 'Wastage Report';                    // Required Variable for menu
        $selectedSubMenu= 'Wastage Reqisition';                    // Required Variable for submenu
        $pageTitle      = 'Wastage Reqisition';            // Page Slug Title

        $resultRoute    = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.route_id','tbl_wastage.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        return view('sales.report.wastage.fo.orderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

    public function wastage_report_fo_order_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.middle_name','tbl_wastage_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->where('tbl_wastage.route_id', $request->get('routes'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        }
        
        return view('sales.report.wastage.fo.orderReportList', compact('resultOrderList'));
    }


    public function wastage_order_details($orderMainId)
    {
        $selectedMenu   = 'Wastage Report';                    // Required Variable for menu
        $selectedSubMenu= 'Wastage Reqisition';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';         // Page Slug Title        

        $resultCartPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.order_no','tbl_wastage.retailer_id','tbl_wastage.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage_details.order_id',$orderMainId)
                        ->groupBy('tbl_wastage_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.auto_order_no','tbl_wastage.update_date','tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.fo_id','tbl_wastage.retailer_id','tbl_wastage.order_no','tbl_wastage.order_date','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.distributor_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_wastage.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.fo_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')
                       ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id',Auth::user()->id)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $foMainId = Auth::user()->id;
       
        

        return view('sales.report.wastage.fo.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }



    /* 
       =====================================================================
       ============================ Delivery  ==============================
       =====================================================================
    */    

    public function wastage_report_fo_delivery()
    {
        $selectedMenu   = 'Wastage Report';                    // Required Variable for menu
        $selectedSubMenu= 'Wastage Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Wastage Delivery';            // Page Slug Title


         $resultRoute    = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.route_id','tbl_wastage.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        return view('sales.report.wastage.fo.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }


    public function wastage_report_fo_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.middle_name','tbl_wastage_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->where('tbl_wastage.route_id', $request->get('routes'))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        }
        
        return view('sales.report.wastage.fo.deliveryReportList', compact('resultOrderList'));
    }


     public function wastage_report_fo_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Wastage Report';                    // Required Variable for menu
        $selectedSubMenu= 'Wastage Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';         // Page Slug Title        

        $resultCartPro  = DB::table('tbl_wastage_details')
                        ->select('tbl_wastage_details.cat_id','tbl_wastage_details.p_total_price','tbl_wastage_details.wastage_qty','tbl_wastage_details.delivery_cat_id','tbl_wastage_details.delivery_product_id','tbl_wastage_details.replace_delivered_qty','tbl_wastage_details.replace_delivered_value','tbl_wastage_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_wastage.order_id','tbl_wastage.distributor_id','tbl_wastage.fo_id','tbl_wastage.order_type','tbl_wastage.order_no','tbl_wastage.retailer_id','tbl_wastage.global_company_id','tbl_product.id','tbl_product.name AS proname')

                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')

                        ->join('tbl_wastage', 'tbl_wastage.order_id', '=', 'tbl_wastage_details.order_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage_details.order_id',$orderMainId)
                        //->groupBy('tbl_wastage_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.auto_order_no','tbl_wastage.chalan_no','tbl_wastage.chalan_date','tbl_wastage.update_date','tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.distributor_id','tbl_wastage.fo_id','tbl_wastage.retailer_id','tbl_wastage.order_no','tbl_wastage.order_date','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.distributor_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.global_company_id','tbl_wastage.order_id','tbl_wastage.order_type','tbl_wastage.distributor_id','tbl_wastage.fo_id','tbl_point.point_id','tbl_point.point_name', 'tbl_point.business_type_id', 'tbl_route.route_id','tbl_route.rname')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.distributor_id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_wastage.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_wastage')->select('tbl_user_details.first_name','tbl_user_details.cell_phone','tbl_wastage.distributor_id','tbl_wastage.fo_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                                      
                        ->where('tbl_wastage.order_id',$orderMainId)
                        ->first();

       $foMainId = $resultFoInfo->fo_id;
       
        

        return view('sales/report/wastage/fo/invoiceDetailsDelivery', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo'));
    }

// Sharif dashboard report
     public function dashboard_report_fo_delivery($startDate,$endDate)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title

        

        $resultRoute    = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.route_id','tbl_wastage.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();


        $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.middle_name','tbl_wastage_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.dashboardMonthAchivment', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList','startDate','endDate'));
    }

    
    


    


    /* 
       =====================================================================
       ============================ Attendance  ============================
       =====================================================================
    */    

    public function ssg_report_fo_attendance()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Attendance';               // Required Variable for submenu
        $pageTitle      = 'Attendance';              // Page Slug Title
        

        $todate         = date('Y-m-d');
        $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.global_company_id')

                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'ia.foid')
                        ->where('tbl_wastage_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

        return view('sales.report.fo.attendanceReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultAttendanceList'));
    }

    public function ssg_report_fo_attendance_list(Request $request)
    {
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='')
        {
            $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.global_company_id')

                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'ia.foid')
                        ->where('tbl_wastage_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($fromdate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        //->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();  
        }

        //dd($resultAttendanceList);
               

        return view('sales.report.fo.attendanceReportList', compact('resultAttendanceList'));
    }


    /* 
       =====================================================================
       ============================ Visit  =================================
       =====================================================================
    */    

    public function ssg_report_fo_visit()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Visit';                    // Required Variable for submenu
        $pageTitle      = 'Visit Report';            // Page Slug Title
        
        $todate     = date('Y-m-d');
        $resultVisitList = DB::table('ims_tbl_visit_order')
                    ->select('ims_tbl_visit_order.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.global_company_id')
                    ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'ims_tbl_visit_order.foid')
                    ->where('tbl_wastage_details.global_company_id', Auth::user()->global_company_id)
                    ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                    ->whereBetween('ims_tbl_visit_order.date', array($todate, $todate))
                    ->orderBy('ims_tbl_visit_order.id','DESC')                    
                    ->get();

        return view('sales.report.fo.visitReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultVisitList'));
    }

    public function ssg_report_fo_visit_list(Request $request)
    {
        
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('orderType')=='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.global_company_id')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_wastage_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();  
        }
        if($fromdate!='' && $todate!='' && $request->get('orderType')!='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.global_company_id')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_wastage_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->where('ims_tbl_visit_order.status', $request->get('orderType'))
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();   
        }       
        

        return view('sales.report.fo.visitReportList', compact('resultVisitList'));
    }


    /* 
       =====================================================================
       ========================= Order Vs Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_fo_order_vs_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'Oeder Vs Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Oeder Vs Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.route_id','tbl_wastage.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_wastage.route_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->orWhere('tbl_wastage.order_type', 'Confirmed')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', '!=','Ordered')                        
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

    public function ssg_report_fo_order_vs_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', '!=','Ordered')                        
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_wastage')
                        ->select('tbl_wastage.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_wastage.retailer_id')
                        ->where('tbl_wastage.order_type', '!=','Ordered')
                        //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->where('tbl_wastage.route_id', $request->get('routes'))
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.orderVsDeliveryReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Category Wise Order  ======================
       =====================================================================
    */    

    public function ssg_report_fo_category_wise_order()
    {
        $selectedMenu   = 'Report';                                  // Required Variable for menu
        $selectedSubMenu= 'Category Wise Order';                    // Required Variable for submenu
        $pageTitle      = 'Category Wise Order Report';            // Page Slug Title

        $resultCategory = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->where('tbl_wastage.order_type', '!=','Ordered')
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->where('tbl_wastage.order_type', '!=','Ordered')
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory','resultOrderList'));
    }

    public function ssg_report_fo_category_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('category')=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->where('tbl_wastage.order_type', '!=','Ordered')
                            //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->where('tbl_wastage.order_type', '!=','Ordered')
                            ->where('tbl_wastage_details.cat_id', $request->get('category'))
                            //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
            
        }
        
        return view('sales.report.fo.categoryWiseOrderReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= Product Wise Order  =======================
       =====================================================================
    */    

    public function ssg_report_fo_product_wise()
    {
        $selectedMenu   = 'Report';                             // Required Variable for menu
        $selectedSubMenu= 'Product Wise';                      // Required Variable for submenu
        $pageTitle      = 'Product Wise Order Report';        // Page Slug Title

        $resultFO       = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.middle_name','tbl_wastage_details.last_name')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->where('tbl_wastage.order_type', '!=', 'Ordered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.fo_id')
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_wastage')
                            ->select('tbl_wastage.order_id','tbl_wastage.global_company_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.order_type', '!=', 'Ordered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')                            
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.productWiseReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList'));
    }

    public function ssg_report_fo_category_wise_product_list(Request $request)
    {

        $category   = $request->get('category');
        $serial     = 1;

        $resultProduct = DB::table('tbl_wastage')
                        ->select('tbl_wastage.order_id','tbl_wastage.global_company_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product.name AS pname','tbl_product.id AS pid')

                        ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')                      
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.fo_id', Auth::user()->id)
                        ->where('tbl_wastage.order_type', '!=', 'Ordered')
                        ->where('tbl_wastage_details.cat_id', $category)
                        ->groupBy('tbl_wastage_details.product_id')                    
                        ->orderBy('tbl_wastage_details.product_id','DESC')                    
                        ->get();

        return view('sales.report.distributor.allDropDown', compact('resultProduct','serial'));

    }

    public function ssg_report_fo_product_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = Auth::user()->id;
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')                            
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', $fo)
                            ->where('tbl_wastage_details.cat_id', $category)
                            ->where('tbl_wastage_details.product_id', $products)
                            //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')                            
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', $fo)
                            ->where('tbl_wastage_details.cat_id', $category)
                            //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*',DB::raw('SUM(tbl_wastage_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_wastage_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')                            
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.fo_id', $fo)
                            //->whereBetween('tbl_wastage.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        
        return view('sales.report.fo.productWiseReportList', compact('resultOrderList'));
    }


    /* 
       =====================================================================
       ========================= SKU Wise Delivery  ========================
       =====================================================================
    */    

    public function ssg_report_sku_wise_delivery()
    {
        $selectedMenu   = 'Report';                                // Required Variable for menu
        $selectedSubMenu= 'SKU Wise Delivery';                    // Required Variable for submenu
        $pageTitle      = 'SKU Wise Delivery Report';            // Page Slug Title

        $resultFO       = DB::table('tbl_wastage')
                        ->select('tbl_wastage.global_company_id','tbl_wastage.order_type','tbl_wastage.order_id','tbl_wastage.fo_id','tbl_wastage_details.user_id','tbl_wastage_details.first_name','tbl_wastage_details.middle_name','tbl_wastage_details.last_name')
                        ->join('tbl_wastage_details', 'tbl_wastage_details.user_id', '=', 'tbl_wastage.fo_id')
                        ->where('tbl_wastage.order_type', 'Delivered')
                        ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_wastage.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_wastage.fo_id')
                        ->orderBy('tbl_wastage.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_wastage')
                            ->select('tbl_wastage.order_id','tbl_wastage.global_company_id','tbl_wastage_details.cat_id','tbl_wastage_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->groupBy('tbl_wastage_details.cat_id')                    
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    public function ssg_report_sku_wise_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->where('tbl_wastage.fo_id', $fo)
                            ->where('tbl_wastage_details.cat_id', $category)
                            ->where('tbl_wastage_details.product_id', $products)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->where('tbl_wastage.fo_id', $fo)
                            ->where('tbl_wastage_details.cat_id', $category)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->where('tbl_wastage.fo_id', $fo)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->where('tbl_wastage_details.cat_id', $category)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_wastage')
                            ->select('tbl_wastage.*','tbl_wastage_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_wastage_details', 'tbl_wastage_details.order_id', '=', 'tbl_wastage.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_wastage_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_wastage_details.product_id')
                            ->where('tbl_wastage.order_type', 'Delivered')
                            ->where('tbl_wastage.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_wastage.distributor_id', Auth::user()->id)
                            ->where('tbl_wastage_details.cat_id', $category)
                            ->where('tbl_wastage_details.product_id', $products)
                            //->whereBetween('tbl_wastage.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_wastage.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_wastage_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }
}
