<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class AttendenceController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 08/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_attendance()
    {

        $selectedMenu       = 'Attendance';          // Required Variable Menu
        $selectedSubMenu    = 'Attendance';         // Required Variable Sub Menu
        $pageTitle          = 'Attendance';        // Page Slug Title


        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id       = $resultFoInfo->point_id;
        $division_id    = $resultFoInfo->division_id;

        //dd($point_id);

        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','users.display_name','tbl_user_type.user_type','tbl_user_details.first_name','tbl_user_details.user_id')
                         ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor                         
                         ->where('tbl_user_business_scope.point_id',$point_id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                        ->get();

        $resultRoute = DB::table('tbl_route')
                        ->select('route_id','rname')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('status', 0) // 0 for active
                        ->where('point_id', $point_id)
                        ->orderBy('rname','ASC')
                        ->get();

        // $route_id = '';
        // if(sizeof($resultRouteOnly)>0)
        // {
        //     $route_id = $resultRouteOnly->route_id;
        // }

        // $resultRetailer = DB::table('tbl_retailer')
        //                 ->select('retailer_id','name','rid')
        //                 ->where('global_company_id', Auth::user()->global_company_id)                       
        //                 ->where('rid', $route_id)
        //                 ->orderBy('name','DESC')                    
        //                 ->get();

        $resultInOut    = DB::table('ims_attendence')
                        ->select('id','date','foid','type')                       
                        ->where('foid', Auth::user()->id)
                        ->where('type', 1)
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('date', date('Y-m-d'))
                        ->orderBy('id', 'DESC')
                        ->first();


        $todate         = date('Y-m-d');
        $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.division_id','tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id','tbl_point.point_id','tbl_point.point_name','tbl_division.div_id','tbl_division.div_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                        ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')                         
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                        ->first();

            if(sizeof($resultPoint) >0 )
            {
                //$pointID = $resultPoint->point_id;
                session()->put('pointName', $resultPoint->point_name);
                session()->put('divisionName', $resultPoint->div_name);
            }
            


        return view('sales/attendance/attendanceManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultDistributor','resultRetailer','resultInOut','resultRoute','resultAttendanceList'));
    }



    public function ssg_attendance_retailers(Request $request)
    {
        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','status')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('rid', $request->get('routesid'))
                        ->where('status', 0)
                        ->orderBy('name','ASC')                    
                        ->get();
        $serial = 5;

        return view('sales.report.distributor.allDropDown', compact('serial','resultRetailer'));
    }


    public function ssg_attendance_inout(Request $request)
    {
        
        if($request->get('inOutStatus')=='1')
        {
            DB::table('ims_attendence')->insert(
                [
                    'foid'              => Auth::user()->id,
                    'global_company_id' => Auth::user()->global_company_id,
                    'entrydatetime'     => date('Y-m-d H:i:s'),
                    'location'          => $request->get('location'),
                    'date'              => date('Y-m-d'),
                    'lat'               => $request->get('latitude'),
                    'lng'               => $request->get('longitude'),
                    'type'              => 1,
                    'customername'      => '',
                    'remarks'           => '',
                    'retailerid'        => $request->get('retailer'),
                    'routes'            => $request->get('routes'),
                    'distributor'       => $request->get('distributor')
                ]
            );
            
            return 3;
            
            //return Redirect::back()->with('success', Auth::user()->display_name.'  Successfully IN.');
        }
        else
        {
            DB::table('ims_attendence')->insert(
                [
                    'foid'              => Auth::user()->id,
                    'global_company_id' => Auth::user()->global_company_id,
                    'entrydatetime'     => date('Y-m-d H:i:s'),
                    'location'          => $request->get('location'),
                    'date'              => date('Y-m-d'),
                    'lat'               => $request->get('latitude'),
                    'lng'               => $request->get('longitude'),
                    'type'              => 3,
                    'customername'      => '',
                    'remarks'           => '',
                    'retailerid'        => $request->get('retailer'),
                    'routes'            => $request->get('routes'),
                    'distributor'       => $request->get('distributor')
                ]
            );

            return 3;

            //return Redirect::back()->with('success', Auth::user()->display_name.'  Successfully OUT.');
        }
    }

    public function ssg_attendance_list(Request $request)
    {
        $todate         = date('Y-m-d');
        $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.global_company_id')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', Auth::user()->global_company_id)
                        ->where('ia.foid', Auth::user()->id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();
        $serialNo = 10;

        return view('sales.mgt.allReplaceValue', compact('serialNo','resultAttendanceList'));
    }
}
