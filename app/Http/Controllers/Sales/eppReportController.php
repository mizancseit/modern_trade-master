<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class eppReportController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 23/12/2018
	*
	**/

  public function __construct()
  {
    $this->middleware('auth'); // for auth check       
  }

  public function memoWiseSalesReport()
  {
    $selectedMenu   = 'Memo wise sales report';                       // Required Variable for menu
    $selectedSubMenu= 'Memo wise sales report';                    // Required Variable for submenu
    $pageTitle      = 'Memo wise sales report';   


    return view('sales/epp/memoWiseSalesReport',compact('selectedMenu','selectedSubMenu','pageTitle'));
  }


  public function memoWiseSalesReportList(Request $request)
  {

    $fromdate= '';
    $todate='';
    if($request->get('fromdate')!='' && $request->get('todate')!='')
    {
      $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
      $todate     = date('Y-m-d', strtotime($request->get('todate')));
    }    
    
    $memo       = $request->get('memo');

    if($fromdate!='' && $todate!='' && $memo!='')
    {
        $memoResult = DB::table('tbl_order')
                    ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.sap_code','tbl_retailer.retailer_id','tbl_retailer.name as retailerName','users.display_name','tbl_point.point_name','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.division_id','tbl_territory.id','tbl_territory.name as teriName','tbl_division.div_id','tbl_division.div_name','tbl_business_type.business_type_id','tbl_business_type.business_type')

                    ->leftJoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                    ->leftJoin('users', 'tbl_order.distributor_id', '=', 'users.id')
                    ->leftJoin('tbl_user_business_scope', 'tbl_user_details.user_id', '=', 'tbl_user_business_scope.user_id')

                    ->leftJoin('tbl_point', 'tbl_order.point_id', '=', 'tbl_point.point_id')
                    ->leftJoin('tbl_territory', 'tbl_user_business_scope.territory_id', '=', 'tbl_territory.id')
                    ->leftJoin('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                    ->leftJoin('tbl_business_type', 'tbl_user_business_scope.global_company_id', '=', 'tbl_business_type.business_type_id')

                    ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                    ->where('tbl_order.order_type', 'Confirmed')
                    ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->where('tbl_order.order_no', $memo)
                    ->orderBy('tbl_business_type.business_type','ASC')
                    ->get();
    }
    else if($fromdate!='' && $todate!='' && $memo=='')
    {
        $memoResult = DB::table('tbl_order')
                    ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.sap_code','tbl_retailer.retailer_id','tbl_retailer.name as retailerName','users.display_name','tbl_point.point_name','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.division_id','tbl_territory.id','tbl_territory.name as teriName','tbl_division.div_id','tbl_division.div_name','tbl_business_type.business_type_id','tbl_business_type.business_type')

                    ->leftJoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                    ->leftJoin('users', 'tbl_order.distributor_id', '=', 'users.id')
                    ->leftJoin('tbl_user_business_scope', 'tbl_user_details.user_id', '=', 'tbl_user_business_scope.user_id')

                    ->leftJoin('tbl_point', 'tbl_order.point_id', '=', 'tbl_point.point_id')
                    ->leftJoin('tbl_territory', 'tbl_user_business_scope.territory_id', '=', 'tbl_territory.id')
                    ->leftJoin('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                    ->leftJoin('tbl_business_type', 'tbl_user_business_scope.global_company_id', '=', 'tbl_business_type.business_type_id')

                    ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                    ->where('tbl_order.order_type', 'Confirmed')
                    ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    //->where('tbl_order.order_no', $memo)
                    ->orderBy('tbl_business_type.business_type','ASC')
                    ->get();
    }
    else if($fromdate=='' && $todate=='' && $memo!='')
    {
        $memoResult = DB::table('tbl_order')
                    ->select('tbl_order.*','tbl_user_details.user_id','tbl_user_details.sap_code','tbl_retailer.retailer_id','tbl_retailer.name as retailerName','users.display_name','tbl_point.point_name','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_user_business_scope.global_company_id','tbl_user_business_scope.division_id','tbl_territory.id','tbl_territory.name as teriName','tbl_division.div_id','tbl_division.div_name','tbl_business_type.business_type_id','tbl_business_type.business_type')

                    ->leftJoin('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.distributor_id')
                    ->leftJoin('users', 'tbl_order.distributor_id', '=', 'users.id')
                    ->leftJoin('tbl_user_business_scope', 'tbl_user_details.user_id', '=', 'tbl_user_business_scope.user_id')

                    ->leftJoin('tbl_point', 'tbl_order.point_id', '=', 'tbl_point.point_id')
                    ->leftJoin('tbl_territory', 'tbl_user_business_scope.territory_id', '=', 'tbl_territory.id')
                    ->leftJoin('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                    ->leftJoin('tbl_business_type', 'tbl_user_business_scope.global_company_id', '=', 'tbl_business_type.business_type_id')

                    ->leftJoin('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                    ->where('tbl_order.order_type', 'Confirmed')
                    ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                    //->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($fromdate, $todate))
                    ->where('tbl_order.order_no', $memo)
                    ->orderBy('tbl_business_type.business_type','ASC')
                    ->get();
    }
    else
    {
      $memoResult = Null;
    }
    return view('sales/epp/memoWiseSalesReportList',compact('memoResult'));

  } 
}
