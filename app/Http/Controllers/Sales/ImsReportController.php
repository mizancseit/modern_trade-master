<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class ImsReportController extends Controller
{
    /**
    *
    * Created by Md. Masud Rana
    * Date : 4/09/2018
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

    public function ims_report()
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Report';                    // Required Variable for submenu
        $pageTitle      = 'Order Report';            // Page Slug Title

        $resultPoint    = DB::table('tbl_order')
                        ->select('tbl_order.point_id','tbl_point.point_id','tbl_point.point_name')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->groupBy('tbl_order.point_id')
                        ->orderBy('tbl_point.point_name','ASC')                    
                        ->get();

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','users.display_name')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Confirmed')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('users.display_name','ASC')                    
                        ->get();

        $date           = date('Y-m-d',strtotime('-1 Day'));

        $resultIms      = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        //->groupBy('tbl_order.order_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        //->where('tbl_order.order_type', 'Ordered')                   
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($date, $date))           
                        ->get();

            $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,e.slab FROM (
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_free_qty WHERE status=0 AND date(order_date)='$date'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_and_free_qty WHERE status=0 AND date(order_date)='$date'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_free_qty WHERE status=0 AND date(order_date)='$date'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_regular_and_free_qty WHERE status=0 AND date(order_date)='$date'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

        return view('sales/report/ims/ims_report', compact('selectedMenu','selectedSubMenu','pageTitle','resultPoint','resultFO','resultIms','freeSlab','date'));
    }

    public function get_point_fo_list(Request $request)
    {

          $point_id=$request->get('point_id');

          $point_fo_list=DB::table('users')
                     ->Join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                     ->where('tbl_user_business_scope.point_id',$point_id)
                     ->where('users.user_type_id',12)
                     ->get();           
        return view('sales/report/ims/get_point_fo_list' , compact('point_fo_list'));

    }

    public function ims_report_list(Request $request)
    {
        $fromdate     = date('Y-m-d', strtotime($request->get('fromdate')));
        $pointsID     = $request->get('pointsID');
        $foID         = $request->get('foID');
        $pointsID     = $request->get('pointsID');

        if($fromdate!='' && $pointsID=='' && $foID=='')
        {
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        //->groupBy('tbl_order.order_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        //->where('tbl_order.order_type', 'Ordered')                   
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
                        ->get();


            $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,e.slab FROM (
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_free_qty WHERE status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_and_free_qty WHERE  status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_free_qty WHERE status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_regular_and_free_qty WHERE status=0 AND date(order_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            // $resultIms      = DB::table('tbl_order_details')
            //             ->select(DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("SUM(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

            //             ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
            //             ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
            //             ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')

            //             ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
            //             ->groupBy('tbl_order_details.cat_id')
            //             ->orderBy('tbl_order_details.cat_id','ASC')
            //             ->where('tbl_order.order_type', 'Confirmed')                
            //             ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
            //             ->get();
        }
        else if($fromdate!='' && $pointsID!='' && $foID=='')
        {       
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->where('tbl_order.point_id', $pointsID)                  
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
                        ->get();

             $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,e.slab FROM (
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_free_qty WHERE point_id = $pointsID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_and_free_qty WHERE point_id = $pointsID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_free_qty WHERE point_id = $pointsID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_regular_and_free_qty WHERE point_id = $pointsID AND status=0 AND date(order_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            // $resultIms      = DB::table('tbl_order_details')
            //             ->select(DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("SUM(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

            //             ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
            //             ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
            //             ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')

            //             ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
            //             ->groupBy('tbl_order_details.cat_id')
            //             ->orderBy('tbl_order_details.cat_id','ASC')
            //             ->where('tbl_order.order_type', 'Confirmed')                   
            //             ->where('tbl_order.point_id', $pointsID)              
            //             ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
            //             ->get();
        }
        else if($fromdate!='' && $pointsID!='' && $foID!='')
        {
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->where('tbl_order.point_id', $pointsID)                  
                        ->where('tbl_order.fo_id', $foID)                  
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
                        ->get();

             $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,e.slab FROM (
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_special_and_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(order_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab FROM tbl_order_regular_and_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(order_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            // $resultIms      = DB::table('tbl_order_details')
            //             ->select(DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("SUM(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

            //             ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
            //             ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
            //             ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')

            //             ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
            //             ->groupBy('tbl_order_details.cat_id')
            //             ->orderBy('tbl_order_details.cat_id','ASC')
            //             ->where('tbl_order.order_type', 'Confirmed')                   
            //             ->where('tbl_order.point_id', $pointsID)                   
            //             ->where('tbl_order.fo_id', $foID)                   
            //             ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
            //             ->get();
        }
        else if($fromdate!='' && $pointsID=='' && $foID!='')
        {
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        //->where('tbl_order.point_id', $pointsID)                  
                        ->where('tbl_order.fo_id', $foID)                  
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
                        ->get();

            // $resultIms      = DB::table('tbl_order_details')
            //             ->select(DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("SUM(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

            //             ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
            //             ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
            //             ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')

            //             ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
            //             ->groupBy('tbl_order_details.cat_id')
            //             ->orderBy('tbl_order_details.cat_id','ASC')
            //             ->where('tbl_order.order_type', 'Confirmed')                   
            //             ->where('tbl_order.point_id', $pointsID)                   
            //             ->where('tbl_order.fo_id', $foID)                   
            //             ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
            //             ->get();
        }
        else
        {
            $date           = date('Y-m-d',strtotime('-1 Day'));

            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')

                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')              
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))           
                        ->get();

            // $resultIms      = DB::table('tbl_order_details')
            //                 ->select(DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("SUM(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.order_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

            //                 ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
            //                 ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
            //                 ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')

            //                 ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
            //                 ->groupBy('tbl_order_details.cat_id')
            //                 ->orderBy('tbl_order_details.cat_id','ASC')
            //                 ->where('tbl_order.order_type', 'Confirmed')                   
            //                 ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($date, $date))           
            //                 ->get();

        }
        
        return view('sales/report/ims/ims_report_view', compact('resultIms','freeSlab'));
    }




    /* 
       =====================================================================
       ============================ Order  =================================
       =====================================================================
    */    

    public function ims_delivery_report()
    {
        $selectedMenu   = 'Report-Delivery';                    // Required Variable for menu
        $selectedSubMenu= 'Report-Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Order Report';            // Page Slug Title

        $resultDivision = DB::table('tbl_division')->get();
        $resultPoint    = DB::table('tbl_order')
                        ->select('tbl_order.point_id','tbl_point.point_id','tbl_point.point_name')
                        ->Join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        //->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.total_delivery_qty','>', '0') // bug(Nawabpur) resolved by zubair May-13-2019
                        ->groupBy('tbl_order.point_id')
                        ->orderBy('tbl_point.point_name','ASC')                    
                        ->get();
//dd($resultPoint);
        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.fo_id','users.display_name')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                       // ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.total_delivery_qty','>', '0') // bug(Nawabpur) resolved by zubair May-13-2019
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('users.display_name','ASC')                    
                        ->get();

        $date           = date('Y-m-d');

        $resultIms      = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.update_date','tbl_order.point_id','tbl_point.business_type_id','tbl_order.fo_id','tbl_order.distributor_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->where('tbl_order_details.delivered_qty','>', '0') 
                        ->where('tbl_point.business_type_id','1')                  
                        //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($date, $date))  
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.delivered_date,'%Y-%m-%d'))"), array($date, $date))  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        //->groupBy('tbl_order.order_id')
                        ->orderBy('tbl_order.fo_id','ASC')         
                        ->get();

        //dd($resultIms);


        $resultOrderId = DB::table('tbl_order')
                        ->select('order_id','point_id','fo_id','total_delivery_qty','update_date')
                        ->where('total_delivery_qty','>','0')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($date, $date))
                        ->get();

           //dd($resultOrderId);
             $var = "";
             $comma="";           
            foreach ($resultOrderId as $resultid) {
                  //$orderid[] = $resultid->order_id;
                   $var .= $comma.$resultid->order_id;
                  $comma=",";  
             } 

            //dd($orderid);

        $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,tbl_point.business_type_id,users.display_name,e.slab,date(e.delivery_date) AS deliverydate FROM (
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_free_qty WHERE status=0 AND date(delivery_date)='$date'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_and_free_qty WHERE  status=0 AND date(delivery_date)='$date'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_free_qty WHERE status=0 AND date(delivery_date)='$date'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_regular_and_free_qty WHERE status=0 AND date(delivery_date)='$date'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        WHERE tbl_point.business_type_id='1'
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

        //dd($freeSlab);

       return view('sales/report/ims/ims_delivery_report', compact('selectedMenu','selectedSubMenu','pageTitle','resultPoint','resultFO','resultIms','freeSlab','resultDivision'));
    }

    public function ims_delivery_report_list(Request $request)
    {
        $fromdate      = date('Y-m-d', strtotime($request->get('fromdate')));
        $fromdate1     = strtotime('-2 day', strtotime($fromdate)); //strtotime('$fromdate - 2 Day'); 
        $fromdate2     = date('Y-m-d', $fromdate1);       
        //echo $fromdate2;
        //$pointsID     = $request->get('pointsID');
        $foID         = $request->get('foID');
        $pointsID     = $request->get('pointsID');
        $channel      = $request->get('channel');
        $divisions     = $request->get('divisions');
//dd($divisions);
        if($fromdate!='' && $channel !='' && $divisions=='' && $pointsID==''  && $foID=='')
        {


            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.update_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','tbl_point.business_type_id','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        //->where('tbl_order.order_type','Delivered')
                        ->where('tbl_order.total_delivery_qty','>', '0') // bug(Nawabpur) resolved by zubair May-13-2019
						->where('tbl_order_details.delivered_qty','>', '0')
                        ->where('tbl_point.business_type_id',$channel) 
                        //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))    
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.delivered_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))  // bug(Nawabpur) resolved by zubair May-13-2019   
                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->get();

            $resultOrderId = DB::table('tbl_order')
                        ->select('order_id','point_id','fo_id','total_delivery_qty','update_date')
                        ->where('total_delivery_qty','>','0')
                        //->where('order_type','Delivered') // bug(Nawabpur) resolved by zubair May-13-2019
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate)) 
                        ->groupBy('order_id')
                        ->get();

         
           //dd($resultOrderId);
             $var = "";
             $comma="";           
            foreach ($resultOrderId as $resultid) {
                  //$orderid[] = $resultid->order_id;
                   $var .= $comma.$resultid->order_id;
                  $comma=",";  
             } 

            $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,users.id,users.display_name,e.slab,date(e.delivery_date) AS deliverydate FROM (
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_and_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_regular_and_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        WHERE tbl_point.business_type_id='$channel'
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            $specialCommiQty = DB::select("SELECT cm.offer_id,sum(cm.total_free_value) as freevalue,cm.commission,cm.fo_id,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,users.id,users.display_name,tbl_order.update_date FROM tbl_special_commission as cm
                INNER JOIN tbl_order ON tbl_order.order_id=cm.order_id
                INNER JOIN tbl_point on tbl_point.point_id=cm.point_id
                INNER JOIN users on users.id=cm.fo_id
                WHERE tbl_point.business_type_id='$channel' AND date(tbl_order.update_date)='$fromdate' group by cm.offer_id,cm.fo_id ORDER BY cm.fo_id");
            
        }
        else if($fromdate!='' && $channel !='' && $divisions!='' && $pointsID==''  && $foID=='')
        {
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.update_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','tbl_point.business_type_id','tbl_point.point_division','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->where('tbl_order_details.delivered_qty','>', '0')
                         ->where('tbl_order.total_delivery_qty','>', '0') 
						//->where('tbl_order.order_type','Delivered')  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->where('tbl_point.business_type_id',$channel) 
                        ->where('tbl_point.point_division',$divisions) 
                        //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
						->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.delivered_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))  // bug(Nawabpur) resolved by zubair May-13-2019						
                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->get();

            $resultOrderId = DB::table('tbl_order')
                        ->select('order_id','point_id','fo_id','total_delivery_qty','update_date')
                        ->where('total_delivery_qty','>','0')
                        //->where('order_type','Delivered')  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
                        ->groupBy('order_id')
                        ->get();

         
           //dd($resultOrderId);
             $var = "";
             $comma="";           
            foreach ($resultOrderId as $resultid) {
                  //$orderid[] = $resultid->order_id;
                   $var .= $comma.$resultid->order_id;
                  $comma=",";  
             } 

            $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,tbl_point.point_division,users.id,users.display_name,e.slab,date(e.delivery_date) AS deliverydate FROM (
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_and_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_regular_and_free_qty WHERE status=0 AND date(delivery_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        WHERE tbl_point.business_type_id='$channel' AND tbl_point.point_division='$divisions'
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            $specialCommiQty = DB::select("SELECT cm.offer_id,sum(cm.total_free_value) as freevalue,cm.commission,cm.fo_id,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,tbl_point.point_division,users.id,users.display_name,tbl_order.update_date FROM tbl_special_commission as cm
                INNER JOIN tbl_order ON tbl_order.order_id=cm.order_id
                INNER JOIN tbl_point on tbl_point.point_id=cm.point_id
                INNER JOIN users on users.id=cm.fo_id
                WHERE tbl_point.business_type_id='$channel' AND tbl_point.point_division='$divisions' AND date(tbl_order.update_date)='$fromdate' group by cm.offer_id,cm.fo_id ORDER BY cm.fo_id");
            
        }
        else if($fromdate!='' && $channel!='' && $divisions!='' && $pointsID!='' && $foID=='')
        {       
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),
						DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),
						DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),
						DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),
						DB::raw("COUNT(tbl_order.order_id) as totalMemo"),
						'tbl_order_details.*','tbl_order.order_id','tbl_order.update_date','tbl_order.point_id','tbl_order.fo_id',
						'tbl_product_category.name','tbl_point.point_name','tbl_point.business_type_id','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->where('tbl_order.point_id', $pointsID)
                        //->where('tbl_order.order_type','Delivered')  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->where('tbl_order_details.delivered_qty','>', '0') 
                         ->where('tbl_point.business_type_id',$channel)                  
                        //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.delivered_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))  
                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->get();

            $resultOrderId = DB::table('tbl_order')
                        ->select('order_id','point_id','fo_id','update_date')
                        ->where('total_delivery_qty','>','0')
                        //->where('order_type','Delivered')  // bug(Nawabpur) resolved by zubair May-13-2019
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
                        ->where('tbl_order.point_id', $pointsID) 
                        ->groupBy('order_id')
                        ->get();

         
           //dd($resultOrderId);
             $var = "";
             $comma="";           
            foreach ($resultOrderId as $resultid) {
                  //$orderid[] = $resultid->order_id;
                   $var .= $comma.$resultid->order_id;
                  $comma=",";  
             } 

            $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,users.id,users.display_name,e.slab,date(e.delivery_date) AS deliverydate FROM (
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_free_qty WHERE point_id = $pointsID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_and_free_qty WHERE point_id = $pointsID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_free_qty WHERE point_id = $pointsID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_id,order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_regular_and_free_qty WHERE point_id = $pointsID AND status=0 AND date(delivery_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        WHERE tbl_point.business_type_id='$channel'
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            $specialCommiQty = DB::select("SELECT cm.offer_id,sum(cm.total_free_value) as freevalue,cm.commission,cm.fo_id,tbl_point.point_id,tbl_point.point_name,tbl_point.business_type_id,users.id,users.display_name,tbl_order.update_date FROM tbl_special_commission as cm
                INNER JOIN tbl_order ON tbl_order.order_id=cm.order_id
                INNER JOIN tbl_point on tbl_point.point_id=cm.point_id
                INNER JOIN users on users.id=cm.fo_id
                WHERE tbl_point.business_type_id='$channel' AND cm.point_id = $pointsID AND date(tbl_order.update_date)='$fromdate' group by cm.offer_id,cm.fo_id ORDER BY cm.fo_id");

        }
        else if($fromdate!='' && $channel!='' && $divisions!='' && $pointsID!='' && $foID!='')
        {
            $resultIms  = DB::table('tbl_order_details')
                        ->select(DB::raw("SUM(tbl_order_details.delivered_qty) as deliveryQty"),DB::raw("SUM(tbl_order_details.delivered_value) as deliveryValue"),DB::raw("SUM(tbl_order_details.order_qty) as orderQty"),DB::raw("SUM(tbl_order_details.p_total_price) as orderValue"),DB::raw("COUNT(tbl_order.order_id) as totalMemo"),'tbl_order_details.*','tbl_order.order_id','tbl_order.update_date','tbl_order.point_id','tbl_order.fo_id','tbl_product_category.name','tbl_point.point_name','users.display_name')

                        ->leftJoin('tbl_order', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->leftJoin('users', 'users.id', '=', 'tbl_order.fo_id')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->where('tbl_order.point_id', $pointsID)                  
                        ->where('tbl_order.fo_id', $foID)
                        //->where('tbl_order.order_type','Delivered') // bug(Nawabpur) resolved by zubair May-13-2019
                        ->where('tbl_order_details.delivered_qty','>', '0')                  
                        //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate)) 
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order_details.delivered_date,'%Y-%m-%d'))"), array($fromdate, $fromdate)) 
                        ->groupBy('tbl_order_details.cat_id')
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.fo_id','ASC')
                        ->get();


             $resultOrderId = DB::table('tbl_order')
                        ->select('order_id','point_id','fo_id','update_date')
                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($fromdate, $fromdate))
                        ->where('tbl_order.point_id', $pointsID) 
                        ->where('tbl_order.fo_id', $foID) 
                        //->where('order_type','Delivered')    // bug(Nawabpur) resolved by zubair May-13-2019
                        ->where('total_delivery_qty','>','0')
                        ->groupBy('order_id')
                        ->get();

         
           
             $var = "";
             $comma="";           
            foreach ($resultOrderId as $resultid) {
                  //$orderid[] = $resultid->order_id;
                   $var .= $comma.$resultid->order_id;
                  $comma=",";  
             } 
//dd($var);
             $freeSlab =  DB::select("SELECT date(e.order_date) AS date,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,e.slab,date(e.delivery_date) AS deliverydate FROM (
                        SELECT order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_special_and_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(delivery_date)='$fromdate'
                        UNION ALL
                        SELECT order_date,point_id,fo_id,slab,delivery_date FROM tbl_order_regular_and_free_qty WHERE point_id = $pointsID AND fo_id=$foID AND status=0 AND date(delivery_date)='$fromdate'
                        ) AS e 
                        join tbl_point on tbl_point.point_id=e.point_id
                        join users on users.id=e.fo_id
                        group by e.fo_id,e.slab ORDER BY e.fo_id,e.slab");

            // dd($freeSlab);

             $specialCommiQty = DB::select("SELECT cm.offer_id,sum(cm.total_free_value) as freevalue,cm.commission,cm.fo_id,tbl_point.point_id,tbl_point.point_name,users.id,users.display_name,tbl_order.update_date FROM tbl_special_commission as cm
                INNER JOIN tbl_order ON tbl_order.order_id=cm.order_id
                INNER JOIN tbl_point on tbl_point.point_id=cm.point_id
                INNER JOIN users on users.id=cm.fo_id
                WHERE cm.point_id = $pointsID AND cm.fo_id=$foID AND date(tbl_order.update_date)='$fromdate' group by cm.offer_id,cm.fo_id ORDER BY cm.fo_id");
        }
        
        
        return view('sales/report/ims/ims_report_delivery_view', compact('resultIms','freeSlab','specialCommiQty'));
    }

    /* 
       =====================================================================
       ============================ Order  =================================
       =====================================================================
    */    

    public function ims_distributor_requisition()
    {
        $selectedMenu   = 'Distributor Req';                    // Required Variable for menu
        $selectedSubMenu= 'Distributor Req';                    // Required Variable for submenu
        $pageTitle      = 'Distributor Report';            // Page Slug Title

        $date           = date('Y-m-d');
        
        $resultPoint    = DB::table('distributor_requisition')
                        ->select('distributor_requisition.point_id','tbl_point.point_id','tbl_point.point_name')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date)) 
                        ->groupBy('distributor_requisition.point_id')
                        ->orderBy('tbl_point.point_name','ASC')                    
                        ->get();

        $resultDist      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')                        
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date))           
                        ->get();

        

        $resultIms      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date))           
                        ->get();

        return view('sales/ims/imsDistributorRequisition', compact('selectedMenu','selectedSubMenu','pageTitle','resultPoint','resultDist','resultIms'));
    }

    public function ims_distributor_requisition_list(Request $request)
    {
        $fromdate     = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate       = date('Y-m-d', strtotime($request->get('todate')));
        $pointsID     = $request->get('pointsID');
        $distID       = $request->get('distID');
        //$pointsID     = $request->get('pointsID');

        if($distID!='' && $pointsID=='')
        {
            $resultIms      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        ->where('distributor_requisition.distributor_in_charge',$distID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID!='' && $pointsID!='')
        {
            $resultIms      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        ->where('distributor_requisition.distributor_in_charge',$distID)
                        ->where('distributor_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID=='' && $pointsID!='')
        {
            $resultIms      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        //->where('distributor_requisition.distributor_in_charge',$distID)
                        ->where('distributor_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID=='' && $pointsID=='')
        {
            $resultIms      = DB::table('distributor_requisition')
                        ->select('distributor_requisition.distributor_in_charge','distributor_requisition.sent_date','distributor_requisition.req_id','distributor_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'distributor_requisition.distributor_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'distributor_requisition.point_id')
                        //->where('distributor_requisition.distributor_in_charge',$distID)
                        //->where('distributor_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(distributor_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        
        
        return view('sales/ims/imsDistributorRequisitionView', compact('resultIms'));
    }
    
    
     public function ims_depo_requisition()
    {
        $selectedMenu   = 'Depo Req';                    // Required Variable for menu
        $selectedSubMenu= 'Depo Req';                    // Required Variable for submenu
        $pageTitle      = 'Depo Report';                // Page Slug Title

        $date           = date('Y-m-d');
        
        $resultPoint    = DB::table('depot_requisition')
                        ->select('depot_requisition.point_id','tbl_point.point_id','tbl_point.point_name')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date)) 
                        ->groupBy('depot_requisition.point_id')
                        ->orderBy('tbl_point.point_name','ASC')                    
                        ->get();

        $resultDist      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.point_id','users.id','users.display_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')                        
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date))           
                        ->get();

        

        $resultIms      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($date, $date))           
                        ->get();

        return view('sales/ims/imsDepotRequisition', compact('selectedMenu','selectedSubMenu','pageTitle','resultPoint','resultDist','resultIms'));
    }

    
    public function ims_depo_requisition_list(Request $request)
    {
        $fromdate     = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate       = date('Y-m-d', strtotime($request->get('todate')));
        $pointsID     = $request->get('pointsID');
        $distID       = $request->get('distID');
        //$pointsID     = $request->get('pointsID');

        if($distID!='' && $pointsID=='')
        {
            $resultIms      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.req_no', 'depot_requisition.req_date',
                        'depot_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        ->where('depot_requisition.depot_in_charge',$distID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID!='' && $pointsID!='')
        {
            $resultIms      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.req_no', 'depot_requisition.req_date',
                        'depot_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        ->where('depot_requisition.depot_in_charge',$distID)
                        ->where('depot_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID=='' && $pointsID!='')
        {
            $resultIms      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.req_no', 'depot_requisition.req_date',
                        'depot_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        //->where('distributor_requisition.depot_in_charge',$distID)
                        ->where('depot_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        else if($distID=='' && $pointsID=='')
        {
            $resultIms      = DB::table('depot_requisition')
                        ->select('depot_requisition.depot_in_charge','depot_requisition.sent_date','depot_requisition.req_id',
                        'depot_requisition.req_no', 'depot_requisition.req_date',
                        'depot_requisition.point_id','users.id','users.display_name','tbl_point.point_id','tbl_point.point_name')

                        ->leftJoin('users', 'users.id', '=', 'depot_requisition.depot_in_charge')
                        ->leftJoin('tbl_point', 'tbl_point.point_id', '=', 'depot_requisition.point_id')
                        //->where('depot_requisition.depot_in_charge',$distID)
                        //->where('depot_requisition.point_id',$pointsID)
                        ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.sent_date,'%Y-%m-%d'))"), array($fromdate, $todate))           
                        ->get();
        }
        
        
        return view('sales/ims/imsDepotRequisitionView', compact('resultIms'));
    }





























    public function ssg_order_details($orderMainId)
    {
        $selectedMenu   = 'Report';                    // Required Variable for menu
        $selectedSubMenu= 'Order';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';         // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $foMainId = Auth::user()->id;
        // for offers
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->whereNotNull('og.orderid')
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)                                
                                ->first();
        }

        return view('sales.report.fo.invoiceDetails', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift'));
    }



    /* 
       =====================================================================
       ============================ Delivery  ==============================
       =====================================================================
    */    

    public function ssg_report_fo_delivery()
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.deliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

// Sharif dashboard report
     public function dashboard_report_fo_delivery($startDate,$endDate)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Delivery Report';            // Page Slug Title

        

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();


        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.dashboardMonthAchivment', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList','startDate','endDate'));
    }

    
    public function ssg_report_fo_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        
        return view('sales.report.fo.deliveryReportList', compact('resultOrderList'));
    }


    public function ssg_report_fo_delivery_details($orderMainId)
    {
        $selectedMenu   = 'Report';                       // Required Variable for menu
        $selectedSubMenu= 'Delivery';                    // Required Variable for submenu
        $pageTitle      = 'Invoice Details';            // Page Slug Title        

        $resultCartPro  = DB::table('tbl_order_details')
                        ->select('tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.id AS catid','tbl_product_category.name AS catname','tbl_order.order_id','tbl_order.fo_id','tbl_order.order_type','tbl_order.order_no','tbl_order.retailer_id','tbl_order.global_company_id')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                        ->join('tbl_order', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                       
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order_details.order_id',$orderMainId)
                        ->groupBy('tbl_order_details.cat_id')                        
                        ->get();

        $resultInvoice  = DB::table('tbl_order')->select('tbl_order.auto_order_no','tbl_order.update_date','tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_order.retailer_id','tbl_order.order_no','tbl_order.order_date','tbl_retailer.name','tbl_retailer.mobile')

                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

         $resultDistributorInfo = DB::table('tbl_order')->select('tbl_order.global_company_id','tbl_order.order_id','tbl_order.order_type','tbl_order.fo_id','tbl_point.point_id','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.first_name','tbl_user_details.cell_phone','users.id','users.display_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                        ->join('users', 'tbl_order.distributor_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_point.point_id', '=', 'tbl_order.point_id')
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)                        
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        $resultFoInfo  = DB::table('tbl_order')->select('tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.cell_phone')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id',Auth::user()->id)                        
                        ->where('tbl_order.order_id',$orderMainId)
                        ->first();

        // for offers
        $foMainId = Auth::user()->id;
        $resultBundleOfferType = DB::table('tbl_order_gift')
                        ->select('tbl_order_gift.*','tbl_bundle_products.offerId','tbl_bundle_products.productType')
                        ->join('tbl_bundle_products', 'tbl_order_gift.offerid', '=', 'tbl_bundle_products.offerId')

                        ->where('tbl_order_gift.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order_gift.fo_id', $foMainId)
                        ->where('tbl_order_gift.orderid', $orderMainId)
                        ->first();

        $offerType = '';
        if(sizeof($resultBundleOfferType)>0)
        {
            $offerType = $resultBundleOfferType->productType;
        }

        $resultBundleOffersGift = array();
        if($offerType==2) // for offers gift
        {
            
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.productId','tbl_bundle_product_details.giftName','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')

                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')
                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->whereNotNull('og.orderid')
                                ->first();
        }
        elseif($offerType==1) // for SSG product
        {
            $resultBundleOffersGift = DB::table('tbl_order_gift AS og')
                                ->select('og.orderid','og.offerId','og.proid','og.retailer_id','og.global_company_id','og.fo_id','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_product_details.id','tbl_bundle_product_details.offerId','tbl_bundle_product_details.giftName','tbl_bundle_product_details.productId','tbl_product.id','tbl_product.name','tbl_bundle_products.id','tbl_bundle_products.productType')

                                ->leftJoin('tbl_product','og.proid','=','tbl_product.id')
                                ->leftJoin('tbl_bundle_product_details','og.proid','=','tbl_bundle_product_details.id')
                                ->leftJoin('tbl_bundle_products','tbl_bundle_product_details.productId','=','tbl_bundle_products.id')
                                ->leftJoin('tbl_bundle_offer','og.offerId','=','tbl_bundle_offer.iId')

                                ->where('og.orderid', $orderMainId)                                
                                ->where('og.global_company_id', Auth::user()->global_company_id)
                                ->where('og.fo_id',$foMainId)                                
                                ->first();
        }

        return view('sales.report.fo.invoiceDetailsDelivery', compact('selectedMenu','selectedSubMenu','pageTitle','resultCartPro','resultInvoice','orderMainId','foMainId','resultFoInfo','resultDistributorInfo','resultBundleOffersGift'));
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
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
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
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
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
                    ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                    ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
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
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ims_tbl_visit_order.foid', Auth::user()->id)
                        ->whereBetween('ims_tbl_visit_order.date', array($fromdate, $todate))
                        ->orderBy('ims_tbl_visit_order.id','DESC')                    
                        ->get();  
        }
        if($fromdate!='' && $todate!='' && $request->get('orderType')!='')
        {
            $resultVisitList = DB::table('ims_tbl_visit_order')
                        ->select('ims_tbl_visit_order.*','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ims_tbl_visit_order.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
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

        $resultRoute    = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.route_id','tbl_order.fo_id','tbl_route.rname','tbl_route.route_id')
                        
                        ->join('tbl_route', 'tbl_route.route_id', '=', 'tbl_order.route_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->orWhere('tbl_order.order_type', 'Confirmed')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.route_id')
                        ->orderBy('tbl_route.rname','ASC')                    
                        ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        return view('sales.report.fo.orderVsDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','resultOrderList'));
    }

    public function ssg_report_fo_order_vs_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('routes')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')                        
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                        ->select('tbl_order.*','tbl_retailer.name')                        
                        ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                        ->where('tbl_order.order_type', '!=','Ordered')
                        //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.route_id', $request->get('routes'))
                        ->orderBy('tbl_order.order_id','DESC')                    
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

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.categoryWiseOrderReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory','resultOrderList'));
    }

    public function ssg_report_fo_category_wise_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        if($fromdate!='' && $todate!='' && $request->get('category')=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->where('tbl_order.order_type', '!=','Ordered')
                            ->where('tbl_order_details.cat_id', $request->get('category'))
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
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

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', '!=', 'Ordered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        $todate     = date('Y-m-d');
        $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', Auth::user()->id)
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.fo.productWiseReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory','resultOrderList'));
    }

    public function ssg_report_fo_category_wise_product_list(Request $request)
    {

        $category   = $request->get('category');
        $serial     = 1;

        $resultProduct = DB::table('tbl_order')
                        ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product.name AS pname','tbl_product.id AS pid')

                        ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                      
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.fo_id', Auth::user()->id)
                        ->where('tbl_order.order_type', '!=', 'Ordered')
                        ->where('tbl_order_details.cat_id', $category)
                        ->groupBy('tbl_order_details.product_id')                    
                        ->orderBy('tbl_order_details.product_id','DESC')                    
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
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*',DB::raw('SUM(tbl_order_details.delivered_qty) as delivered_qty'),DB::raw('SUM(tbl_order_details.order_qty) as order_qty'),'tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')                            
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.fo_id', $fo)
                            //->whereBetween('tbl_order.order_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->groupBy('tbl_product.name')
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
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

        $resultFO       = DB::table('tbl_order')
                        ->select('tbl_order.global_company_id','tbl_order.order_type','tbl_order.order_id','tbl_order.fo_id','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name')
                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')
                        ->where('tbl_order.order_type', 'Delivered')
                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_order.distributor_id', Auth::user()->id)
                        ->groupBy('tbl_order.fo_id')
                        ->orderBy('tbl_order.order_id','DESC')                    
                        ->get();

        $resultCategory = DB::table('tbl_order')
                            ->select('tbl_order.order_id','tbl_order.global_company_id','tbl_order_details.cat_id','tbl_order_details.order_id','tbl_product_category.name AS catname','tbl_product_category.id AS catid')
                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->groupBy('tbl_order_details.cat_id')                    
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();

        return view('sales.report.distributor.skuWiseDeliveryReport', compact('selectedMenu','selectedSubMenu','pageTitle','resultFO','resultCategory'));
    }

    //MAUNG MGT FO REPORT

    public function fotopten()
    {
        $selectedMenu   = 'FO Top Ten Report';                       // Required Variable for menu
        $selectedSubMenu= 'FO Top Ten Report';                    // Required Variable for submenu
        $pageTitle      = 'FO Top Ten Report'; 
        $fotopten=DB::select("select *,sum(grand_total_value) total from tbl_order where order_type='Ordered' group by fo_id order by total desc limit 0,10 ");
//dd($fotopten);
       // echo"maung"; exit;
return view('sales.report.fo.topten')->with('selectedMenu',$selectedMenu)->with('selectedSubMenu', $selectedSubMenu)
                                     ->with('pageTitle',$pageTitle)->with('fotopten',$fotopten);
   }         // Page Slug Title


    public function ssg_report_sku_wise_delivery_list(Request $request)
    {
        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));
        $fo         = $request->get('fos');
        $category   = $request->get('category');
        $products   = $request->get('products');

        if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            ->where('tbl_order_details.cat_id', $category)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo!='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order.fo_id', $fo)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category=='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products=='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        }
        else if($fromdate!='' && $todate!='' && $fo=='' && $category!='' && $products!='')
        {
            $resultOrderList = DB::table('tbl_order')
                            ->select('tbl_order.*','tbl_order_details.*','tbl_product_category.name AS catname','tbl_product.name AS pname','tbl_product.id AS pid')

                            ->join('tbl_order_details', 'tbl_order_details.order_id', '=', 'tbl_order.order_id')
                            ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_order_details.cat_id')
                            ->join('tbl_product', 'tbl_product.id', '=', 'tbl_order_details.product_id')
                            ->where('tbl_order.order_type', 'Delivered')
                            ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                            ->where('tbl_order.distributor_id', Auth::user()->id)
                            ->where('tbl_order_details.cat_id', $category)
                            ->where('tbl_order_details.product_id', $products)
                            //->whereBetween('tbl_order.update_date', array($fromdate, $todate))
                            ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.update_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                            ->orderBy('tbl_order_details.cat_id','DESC')                    
                            ->get();
        } 
        
        return view('sales.report.distributor.skuWiseDeliveryReportList', compact('resultOrderList'));
    }
}
