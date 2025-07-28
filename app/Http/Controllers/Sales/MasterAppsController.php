<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class MasterAppsController extends Controller
{

    public function ssg_apps_api_login(Request $request)
    {

        //dd($request->all());
        // apps/api/login?appsusername=1731&appspassword=12345&appsrememberme=0

        $username   = $request->get('appsusername');
        $password   = $request->get('appspassword'); 
        $remember   = $request->get('appsrememberme');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id')
                    ->where('email', $username)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                
                $checkAccessFO = DB::table('users')
                    ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_business_scope.user_id','tbl_user_business_scope.point_id')
                    ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                    ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                    ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                    ->where('users.email', $username)
                    ->where('users.is_active', 0)
                    ->first();

                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                    {
                        $resultDistributor = DB::table('users')
                        ->select('users.id','users.email','users.display_name','tbl_user_type.user_type','tbl_user_details.first_name','tbl_user_details.user_id','tbl_user_details.sap_code','tbl_user_business_scope.territory_id','tbl_user_business_scope.division_id','tbl_territory.id','tbl_territory.name','tbl_division.div_id','tbl_division.div_name','tbl_point.point_id','tbl_point.point_name')
                         ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')

                         ->leftJoin('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                         ->leftJoin('tbl_territory', 'tbl_user_business_scope.territory_id', '=', 'tbl_territory.id')
                         ->leftJoin('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')


                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor                         
                         ->where('tbl_user_business_scope.point_id',$checkAccessFO->point_id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', $checkAccessFO->global_company_id)
                        ->first();
						
						//dd($resultDistributor);
						
                        return response(
                            [
                                'status'    => '1',
                                'message'   => 'Successfully Login',
                                'info'      => [
                                    'user_id'           => $checkAccessFO->id,
                                    'user_name'         => $checkAccessFO->display_name,
                                    'user_designation'  => 'Field Officer',
                                    'user_type_id'      => $checkAccessFO->user_type_id,
                                    'user_point_id'     => $checkAccessFO->point_id,
                                    'user_point_name'   => $resultDistributor->point_name,
                                    'global_id'         => $checkAccessFO->global_company_id,
                                    'distributor_id'    => $resultDistributor->id,
                                    'distributor_name'  => $resultDistributor->display_name,
                                    'distributor_sap_code' => $resultDistributor->sap_code,
                                    'territory_id'      => $resultDistributor->territory_id,
                                    'territory_name'    => $resultDistributor->name,
                                    'division_id'      => $resultDistributor->div_id,
                                    'division_name'    => $resultDistributor->div_name
                                ]
                            ]
                        );
						
						//dd(response);
						
						
                    } 
                else
                {
                   return response(['status' => '0','message' => 'Invalid Username & Password']);
                }
            }
        }
        else
        {
           return response(['status' => '0','message' => 'Invalid Username & Password']);
        }
    }


    public function ssg_apps_api_dashboard(Request $request)
    {

        // apps/api/dashboard?appsuser_id=2637&appsglobal_id=1

        $user_id      = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');


        $todayDate      = date('Y-m-d');
        $tomorrowDate   = date('Y-m-d', strtotime($todayDate . "-1 days"));

        // for target
        $startDate     = date('Y-m'.'-01');
        $endDate       = date('Y-m'.'-31');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {

                $resultNewOrder = DB::table('tbl_order')
                            ->select('tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name')

                            ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')                    
                            ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')                  
                            ->where('tbl_order.order_type', 'Confirmed')
                            ->where('tbl_order.global_company_id', $global_id)
                            ->where('tbl_order.fo_id', $user_id)
                            ->whereBetween('tbl_order.order_date', array($tomorrowDate, $todayDate))
                            ->orderBy('tbl_order.order_id','DESC')                    
                            ->count();

                $resultAttendance = DB::table('ims_attendence')
                            ->where('global_company_id', $global_id)
                            ->where('foid', $user_id)                                      
                            ->count();

                $resultVisit = DB::table('ims_tbl_visit_order')                        
                            ->where('foid', $user_id)                                      
                            ->where('status', 2)                                      
                            ->count();

                $resultRetailer = DB::table('tbl_retailer')                        
                            ->where('user', $user_id)
                            ->orderBy('retailer_id','DESC')
                            ->count();

                $monthlyTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                            ->where('fo_id', $user_id)
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate)
                            ->groupBy('fo_id')
                            ->sum('total_value');

                $monthlyAchivement = DB::table('tbl_order')->select('global_company_id','fo_id','total_value','update_date')
                            ->where('fo_id', $user_id)
                            ->where('global_company_id', $global_id)
                            ->whereDate('update_date', '>=', $startDate)
                            ->whereDate('update_date', '<=', $endDate)
                            ->groupBy('fo_id')
                            ->sum('total_value');

                $todayTarget = number_format(($monthlyTarget/26),0);

                $todayAchivement = DB::table('tbl_order')->select('global_company_id','fo_id','total_value','update_date')
                            ->where('fo_id', $user_id)
                            ->where('global_company_id', $global_id)
                            ->whereDate('update_date', '=', $todayDate)
                            ->groupBy('fo_id')
                            ->sum('total_value');

                return response(
                    [
                        'status'    => '1',
                        'message'   => 'Successfully Dashboard',
                        'dashboard' => [
                            'new_order'         => $resultNewOrder,
                            'attendance'        => $resultAttendance,
                            'visit'             => $resultVisit,
                            'retailer'          => $resultRetailer,
                            'monthly_target'    => $monthlyTarget,
                            'monthly_achivement'=> $monthlyAchivement,
                            'today_target'      => $todayTarget,
                            'today_achivement'  => $todayAchivement
                        ]
                    ]
                );
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Dashboard']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Dashboard']);
        }
    }


    public function ssg_apps_api_route(Request $request)
    {

        // apps/api/route?appsuser_id=2637&appsglobal_id=1

        $user_id     = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {

                $routes = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', $user_id)  
                        ->where('tbl_user_business_scope.global_company_id', $global_id)  
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

                return response()->json(['route' => $routes], 201);
                
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Route']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Route']);
        }
    }


    public function ssg_apps_api_attendance(Request $request)
    {

        // apps/api/attendance?appsUser_id=2637&appsGlobal_id=1&appsLag=1111.00&appsLog=1111.00&appsLocation=Uttora&appsType=1

        $user_id     = $request->get('appsUser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsGlobal_id');
        $lag         = $request->get('appsLag'); 
        $log         = $request->get('appsLog');
        $location    = $request->get('appsLocation');
        $type        = $request->get('appsType'); // for in and out status

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {

                if($user_id!='' && $global_id!='' && $lag!='' && $lag!='' && $location!='' && $type!='') // Field Officer (FO)
                {
                    DB::table('ims_attendence')->insert(
                        [
                            'foid'              => $user_id,
                            'global_company_id' => $global_id,
                            'entrydatetime'     => date('Y-m-d H:i:s'),
                            'location'          => $location,
                            'date'              => date('Y-m-d'),
                            'lat'               => $lag,
                            'lng'               => $log,
                            'type'              => $type,
                            'customername'      => Null,
                            'remarks'           => Null,
                            'retailerid'        => Null,
                            'distributor'       => Null
                        ]
                    );

                    return response(['status' => '1','message' => 'Success']);
                }
                else
                {
                    return response(['status' => '0','message' => 'Invalid']);
                } 
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid']);
        }
    }


    public function ssg_apps_api_order_process(Request $request)
    {

        $username   = $request->get('appsusername');
        $password   = $request->get('appspassword'); 
        $remember   = $request->get('appsrememberme');

        $retailderid  = $request->get('retailerID'); 
        $routeid     = $request->get('routeID');

        //dd($request->all());


        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id')
                    ->where('email', $username)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                
                $checkAccessFO = DB::table('users')
                    ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_business_scope.user_id','tbl_user_business_scope.point_id')
                    ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                    ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                    ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                    ->where('users.email', $username)
                    ->where('users.is_active', 0)
                    ->first();

                if (sizeof($checkAccessFO) > 0 ) 
                {  
                    if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                    {

                        $selectedMenu   = 'Visit';             // Required Variable
                        $pageTitle      = 'New Order';        // Page Slug Title


                        if (session('userType')=='')
                        {
                            $commontSessionData = DB::table('users')
                                        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                                        ->where('users.email', Auth::user()->email)                    
                                        ->first();
                            
                            session()->put('businessName', $commontSessionData->global_company_name);
                            session()->put('userType', $commontSessionData->user_type);
                            session()->put('userFullName', $commontSessionData->display_name);
                            session()->put('userTypeId', $commontSessionData->user_type_id);

                        }
                        else if (session('userType')!='')
                        {
                            if (session('userTypeId')==12 && session('userPointId')=='') // for field officer (Fo)
                            {
                                $resultFo = DB::table('users')
                                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
                                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                         ->where('tbl_user_type.user_type_id', 12) // 5 for distributor
                                         ->where('users.id', Auth::user()->id)
                                         //->where('tbl_user_business_scope.point_id', $pointID)
                                         ->where('users.is_active', 0) // 0 for active
                                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                                         ->first();

                                session()->put('userPointId', $resultFo->point_id);
                                session()->put('userDivisionId', $resultFo->division_id);
                            }
                        }

                        $user_id     = $checkAccessFO->id;
                        $global_id   = $checkAccessFO->global_company_id;
                        $businessType= $checkAccessFO->business_type_id;

                        //dd($checkAccessFO);

                        $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', $user_id)                    
                        ->where('tbl_global_company.global_company_id', $global_id)                    
                        ->first();

                        if(sizeof($resultPoint) >0 )
                        {
                            $pointID = $resultPoint->point_id;
                        }
                        else
                        {
                            $pointID = '';
                        }

                        $resultDistributor = DB::table('users')
                                        ->select('users.id','users.email','tbl_user_type.user_type')
                                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                                         ->where('tbl_user_business_scope.point_id', $pointID)
                                         ->where('users.is_active', 0) // 0 for active
                                         ->where('tbl_global_company.global_company_id', $global_id)
                                        ->first();

                        if(sizeof($resultDistributor) >0 )
                        {
                            $distributorID = $resultDistributor->id;
                        }
                        else
                        {
                            $distributorID = '';
                        }

                        $resultRetailer = DB::table('tbl_retailer')
                                        ->select('retailer_id','name','rid')                       
                                        ->where('retailer_id', $retailderid)                    
                                        ->first();

                        $resultCategory = DB::table('tbl_product_category')
                                        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
                                        ->where('status', '0')
                                        ->where('gid', $businessType)
                                        ->where('global_company_id', $global_id)
                                        ->get();

                        $resultCart     = DB::table('tbl_order')
                                        ->where('order_type','Ordered')                        
                                        ->where('fo_id',$user_id)                        
                                        ->where('retailer_id',$retailderid)                        
                                        ->first();

                        return view('masterApps.categoryWithOrder', compact('selectedMenu','pageTitle','resultRetailer','resultCategory','retailderid','routeid','pointID','distributorID','resultCart'));
                    }
                }
            }
        }
        
        
    }



    // ORDER VISIT

    public function ssg_apps_api_visit(Request $request)
    {

        $user_id     = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');
        $retailerID  = $request->get('retailerID');
        $routeID     = $request->get('routeID');
        $type        = $request->get('type');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                $resultReason  = DB::table('ims_visit_reason')->select('id','reason')
                        ->where('type', $type)                    
                        ->get();

                return response()->json(['visitReason' => $resultReason], 201);                
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Reason']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Reason']);
        }
    }

    // ORDER VISIT SUBMIT

    public function ssg_apps_api_visit_submit(Request $request)
    {

        $user_id     = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');
        $retailerID  = $request->get('retailerID');
        $routeID     = $request->get('routeID');
        $reasonID    = $request->get('reasonID');
        $remark      = $request->get('remark');
        $type        = $request->get('type');
        $lat         = $request->get('lat');
        $lon         = $request->get('lon');
        $location    = $request->get('location');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                $resultPoint    = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_route.point_id')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', $user_id)                    
                        ->first();

                if(sizeof($resultPoint) >0 )
                {
                    $pointID = $resultPoint->point_id;
                }
                else
                {
                    $pointID = '';
                }

                $resultDistributor = DB::table('users')
                                ->select('users.id','users.email','tbl_user_type.user_type')
                                 ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                                 ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                                 ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                 ->where('tbl_user_type.user_type_id', 5) // 5 for distributor
                                 ->where('tbl_user_business_scope.point_id', $pointID)
                                 ->where('users.is_active', 0) // 0 for active
                                 ->where('tbl_global_company.global_company_id', $global_id)
                                ->first();

                if(sizeof($resultDistributor) >0 )
                {
                    $distributorID = $resultDistributor->id;
                }
                else
                {
                    $distributorID = '';
                }

                DB::table('ims_tbl_visit_order')->insert(
                    [
                        'disid'                 => $distributorID,                
                        'retailerid'            => $retailerID,    
                        'foid'                  => $user_id,    
                        'routeid'               => $routeID,    
                        'visit'                 => 1,    
                        'order'                 => '',    
                        'order_no'              => '', 
                        'remarks'               => $remark,    
                        'nonvisitedremarks'     => '',
                        'entrydate'             => date('Y-m-d H:i:s'),
                        'date'                  => date('Y-m-d H:i:s'),
                        'user'                  => $user_id,
                        'ipaddress'             => request()->ip(),
                        'hostname'              => $request->getHttpHost(),
                        'status'                => $type,
                        'lat'                   => $lat,
                        'lon'                   => $lon,
                        'location'              => $location,
                        'reasonid'              => $reasonID
                    ]
                );

                return response(['status' => '1','message' => 'Success']); 
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Reason']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Reason']);
        }
    }


    //ATTENDANCE LIST OPTION

    public function ssg_apps_api_attendance_list(Request $request)
    {

        $user_id     = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                $todate         = date('Y-m-d');
                $resultAttendanceList = DB::table('ims_attendence AS ia')
                        ->select('ia.foid','ia.date','ia.entrydatetime','ia.type','ia.location','tbl_user_details.first_name')

                        ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'ia.foid')
                        ->where('tbl_user_details.global_company_id', $global_id)
                        ->where('ia.foid', $user_id)
                        ->where('ia.type', 1)
                        ->whereBetween('ia.date', array($todate, $todate))
                        ->whereRaw('entrydatetime = (select min(`entrydatetime`))')
                        ->groupBy('ia.date')
                        ->orderBy('ia.id','DESC')                    
                        ->get();

                return response()->json(['resultAttendanceList' => $resultAttendanceList], 201); 
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Reason']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Reason']);
        }
    }


    //ORDER MANAGEMENT

    public function ssg_apps_api_confirm_order(Request $request)
    {   
        echo $request->all();
        //return response(['status' => '0','message' => 'Invalid Reason']);
        //echo $request->all();
    }

    //ORDER MANAGEMENT

    public function ssg_apps_api_order_confirm(Request $request)
    { 

        // print_r($request);

        // echo '<prev />';

        // exit();
        $json = json_decode(json_encode($request->all()),true);




        // user informaion here
        $invoice_id         = $json['userinfo']['invoice_id'];
        $distributor_id     = $json['userinfo']['distributor_id'];
        $route_id           = $json['userinfo']['route_id'];
        $retailer_id        = $json['userinfo']['retailer_id'];
        $point_id           = $json['userinfo']['point_id'];
        $user_id            = $json['userinfo']['user_id'];
        $global_company_id  = $json['userinfo']['global_company_id'];

        // discount 
        $discount_amount    = $json['discount']['discount_amount'];
        $discount_rate      = $json['discount']['discount_rate'];

        // order

        $autoOrder = DB::table('tbl_order')->select('auto_order_no')->orderBy('order_id','DESC')->first();

        if(sizeof($autoOrder) > 0)
        {
            $autoOrderId = $autoOrder->auto_order_no + 1;
        }
        else
        {
            $autoOrderId = 10000;
        }    

        $currentYear    = substr(date("Y"), -2); // 2017 to 17
        $currentMonth   = date("m");            // 12
        $currentDay     = date("d");           // 14
        $retailerID     = $retailer_id;

        $orderNo        = $retailerID.'-'.$currentYear.$currentMonth.$currentDay.$autoOrderId;


        $totalQty   = 0;
        $totalValue = 0;

        foreach ($json['order'] as $orders)
        {
            if($orders['product_qty']!='')
            {
                $totalQty   = $totalQty + $orders['product_qty'];
                $totalValue = $totalValue + $orders['product_qty'] * $orders['price'];
            }            
        }

        // order insert here
        
        DB::table('tbl_order')->insert(
            [
                'order_no'              => $orderNo,
                
                'auto_order_no'         => $autoOrderId,
                'order_date'            => date('Y-m-d h:i:s'),
                'distributor_id'        => $distributor_id,
                'point_id'              => $point_id,
                'route_id'              => $route_id,
                'retailer_id'           => $retailer_id,
                'fo_id'                 => $user_id,
                'total_qty'             => $totalQty,
                'total_value'           => $totalValue,
                'grand_total_value'     => $totalValue,
                'entry_by'              => $user_id,
                'ipaddress'             => request()->ip(),
                'hostname'              => $request->getHttpHost(),
                'entry_date'            => date('Y-m-d h:i:s'),
                'global_company_id'     => $global_company_id,
                'total_discount_rate'   => $discount_amount,
                'total_discount_percentage'=> $discount_rate
                //'is_active'             => 0  is_active 0 is edit option active and 1 is confirm
            ]
        );

        // order last id here
        $lastOrderId = DB::table('tbl_order')->latest('order_id')->first(); // order id

        // order details here
        $partial_id = 'part_' . 1;
        foreach ($json['order'] as $orders)
        {
            if($orders['product_qty']!='')
            {
                $totalPrice = $orders['product_qty'] * $orders['price'];

                DB::table('tbl_order_details')->insert(
                    [
                        'order_id'          => $lastOrderId->order_id,
                        'partial_order_id'  => $partial_id,
                        'cat_id'            => $orders['category_id'],
                        'product_id'        => $orders['product_id'],
                        'order_qty'         => $orders['product_qty'],
                        'wastage_qty'       => $orders['wastage_qty'],
                        'p_unit_price'      => $orders['price'],
                        'p_total_price'     => $totalPrice,
                        'p_grand_total'     => $totalPrice
                    ]
                ); 
            }
        }

        foreach ($json['reguler_offer'] as $offers)
        {
            // DB::table('tbl_regular_offer_product')->insert(
            //     [
            //         'order_id'          => $lastOrderId->order_id,
            //         'cat_id'            => $orders['category_id'],
            //         'product_id'        => $orders['product_id'],
            //         'order_qty'         => $orders['product_qty'],
            //         'wastage_qty'       => $orders['wastage_qty'],
            //         'p_unit_price'      => $orders['price'],
            //         'p_total_price'     => $totalPrice,
            //         'p_grand_total'     => $totalPrice
            //     ]
            // );       
        }

        foreach ($json['exclusive_offer'] as $offers)
        {
                   
        }

        foreach ($json['bundle_offer'] as $offers)
        {
                   
        }

        return response(['status' => '1','message' => 'Successfully Order Place']);
    }


    //////////////////////////////// REAL-TIME UPDATE API ////////////////////////////
    
    // POINT LIST

    public function ssg_apps_api_point_list(Request $request)
    {

        // All Points

        $pointList = DB::table('tbl_point')
                    ->select('point_id','point_name','point_division','territory_id','territory_name','global_company_id','business_type_id','is_depot','point_status')
                    
                    ->orderBy('point_name','ASC')                    
                    ->get();

        return response()->json(['point_list' => $pointList], 201);
    }

    // ROUTE LIST

    public function ssg_apps_api_route_list(Request $request)
    {
        $routeList = DB::table('tbl_route')
                    ->select('route_id','did','rname AS route_name','point_id','global_company_id','status')
                    
                    ->orderBy('rname','ASC')                    
                    ->get();

        return response()->json(['route_list' => $routeList], 201);
    }

    // RETAILER LIST

    public function ssg_apps_api_retailer_list(Request $request)
    {
        $retailerList = DB::table('tbl_retailer')
                    ->select('retailer_id','name AS retailer_name','division','territory','point_id','rid','global_company_id','owner','mobile','email','vAddress','reminding_commission_balance','status')
                    
                    ->orderBy('name','ASC')                                   
                    ->get();

        return response()->json(['retailer_list' => $retailerList], 201);
    }

    // DISTRIBUTOR LIST

    public function ssg_apps_api_distributor_list(Request $request)
    {
        $distributorList = DB::table('users')
                    ->select('id','email','password','display_name','user_type_id','business_type_id','global_company_id','sap_code','is_active')
                    
                    ->where('user_type_id',12)                                   
                    ->orderBy('email','ASC')                                   
                    ->get();

        return response()->json(['distributor_list' => $distributorList], 201);
    }

    // FO LIST

    public function ssg_apps_api_fo_list(Request $request)
    {
        $foList = DB::table('users')
                    ->select('id','email','password','display_name','user_type_id','business_type_id','global_company_id','sap_code','is_active')
                    
                    ->where('user_type_id',5)                                   
                    ->orderBy('email','ASC')                                   
                    ->get();

        return response()->json(['fo_list' => $foList], 201);
    }

    // CATEGORY LIST

    public function ssg_apps_api_category_list(Request $request)
    {
        $categoryList = DB::table('tbl_product_category')
                    ->select('id','name AS category_name','g_name','g_code')
                    ->orderBy('name','ASC')                                   
                    ->get();

        return response()->json(['category_list' => $categoryList], 201);
    }

    // PRODUCT LIST

    public function ssg_apps_api_product_list(Request $request)
    {
        $productList = DB::table('tbl_product')
                    ->select('id','name AS product_name','mrp','depo','distri','realtimeprice','status')
                                  
                    ->orderBy('name','ASC')                                   
                    ->get()->take(10);

        return response()->json(['product_list' => $productList], 201);
    }



    public function ssg_tabs_login($pointid,$routeid,$retailderid,$partialOrder,$username,$password)
    {

        //'.$pointid.'/'.$routeid.'/'.$retailderid.'/'.$partialOrder

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id')
                    ->where('email', $username)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                
                $checkAccessFO = DB::table('users')
                    ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_business_scope.user_id')
                    ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                    ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                    ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                    ->where('users.email', $username)
                    ->where('users.is_active', 0)
                    ->where('tbl_user_business_scope.is_active', 0)
                    ->first();

                if (sizeof($checkAccessFO) > 0) 
                {  
                    //dd($checkAccessFO);
                    if (Auth::attempt(['email' => $username, 'password' => $password])) 
                    {
                        if (session('userType')=='')
                        {                        
                            $commontSessionData = DB::table('users')
                                        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                                        ->where('users.email', Auth::user()->email)                    
                                        ->first();
                            
                            session()->put('businessName', $commontSessionData->global_company_name);
                            session()->put('userType', $commontSessionData->user_type);
                            session()->put('userFullName', $commontSessionData->display_name);
                            session()->put('userTypeId', $commontSessionData->user_type_id);

                            if(Auth::user()->user_type_id=='12' || Auth::user()->user_type_id=='5')
                            {
                                $commontSessionDataM = DB::table('users')
                                        ->select('users.*','bscope.point_id','tbl_point.point_id','tbl_point.is_depot')

                                        ->join('tbl_user_business_scope AS bscope', 'users.id', '=', 'bscope.user_id')

                                        ->join('tbl_point', 'bscope.point_id', '=', 'tbl_point.point_id') 
                                        ->where('users.email', Auth::user()->email)                    
                                        ->first();

                                //dd($commontSessionDataM);

                                session()->put('isDepot', $commontSessionDataM->is_depot);
                            }

                        }
                        
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
                            session()->put('pointName', $resultPoint->point_name);
                            session()->put('divisionName', $resultPoint->div_name);
                        }

                        return Redirect::to('/bucket/'.$pointid.'/'.$routeid.'/'.$retailderid.'/'.$partialOrder);
                    } 
                    else
                    {
                       return response(['status' => '0','message' => 'Invalid username or password.']);
                    }
                }
                else
                {
                   return response(['status' => '0','message' => 'Invalid username or password.']);
                }
            }
        }
    }



    // ORDER MANAGE REDIRECT

    public function ssg_tabs_login_order_manage($orderId,$retailderid,$routeid,$partial_order_id,$username,$password)
    {

        //'.$pointid.'/'.$routeid.'/'.$retailderid.'/'.$partialOrder

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id')
                    ->where('email', $username)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                
                $checkAccessFO = DB::table('users')
                    ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_business_scope.user_id')
                    ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                    ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                    ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                    ->where('users.email', $username)
                    ->where('users.is_active', 0)
                    ->where('tbl_user_business_scope.is_active', 0)
                    ->first();

                if (sizeof($checkAccessFO) > 0) 
                {  
                    //dd($checkAccessFO);
                    if (Auth::attempt(['email' => $username, 'password' => $password])) 
                    {   
                        if (session('userType')=='')
                        {
                        
                            $commontSessionData = DB::table('users')
                                        ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                                        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                                        ->where('users.email', Auth::user()->email)                    
                                        ->first();
                            
                            session()->put('businessName', $commontSessionData->global_company_name);
                            session()->put('userType', $commontSessionData->user_type);
                            session()->put('userFullName', $commontSessionData->display_name);
                            session()->put('userTypeId', $commontSessionData->user_type_id);

                            if(Auth::user()->user_type_id=='12' || Auth::user()->user_type_id=='5')
                            {
                                $commontSessionDataM = DB::table('users')
                                        ->select('users.*','bscope.point_id','tbl_point.point_id','tbl_point.is_depot')

                                        ->join('tbl_user_business_scope AS bscope', 'users.id', '=', 'bscope.user_id')

                                        ->join('tbl_point', 'bscope.point_id', '=', 'tbl_point.point_id') 
                                        ->where('users.email', Auth::user()->email)                    
                                        ->first();

                                //dd($commontSessionDataM);

                                session()->put('isDepot', $commontSessionDataM->is_depot);
                            }

                        }

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
                            session()->put('pointName', $resultPoint->point_name);
                            session()->put('divisionName', $resultPoint->div_name);
                        }
                        
                        return Redirect::to('/invoice-edit/'.$orderId.'/'.$retailderid.'/'.$routeid.'/'.$partial_order_id);
                    } 
                    else
                    {
                       return response(['status' => '0','message' => 'Invalid username or password.']);
                    }
                }
                else
                {
                   return response(['status' => '0','message' => 'Invalid username.']);
                }
            }
        }
    }


    // ORDER MANAGE LIST

    public function ssg_apps_api_order_manage_list(Request $request)
    {

        $user_id     = $request->get('appsuser_id'); // fo email = 1731 and primaryid = 2637
        $global_id   = $request->get('appsglobal_id');

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id','global_company_id','business_type_id')
                    ->where('id', $user_id)
                    ->where('global_company_id', $global_id)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                $fromdate = date('Y-m-d');
                $todate   = date('Y-m-d');
                $resultOrderList = DB::table('tbl_order')
                                ->select('tbl_order.order_id','tbl_order.order_no','tbl_order.order_date','tbl_order.total_qty','tbl_order.grand_total_value','tbl_user_details.user_id','tbl_user_details.first_name','tbl_user_details.middle_name','tbl_user_details.last_name','tbl_retailer.name','tbl_retailer.mobile')
                                ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'tbl_order.fo_id')

                                ->join('tbl_retailer', 'tbl_retailer.retailer_id', '=', 'tbl_order.retailer_id')
                                ->where('tbl_order.order_type', 'Confirmed')
                                ->where('tbl_order.global_company_id', $global_id)
                                ->where('tbl_order.fo_id', $user_id)
                                ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                                ->orderBy('tbl_order.order_id','DESC')                    
                                ->get();

                return response()->json(['order_manage_list' => $resultOrderList], 201); 
            }
            else
            {
                return response(['status' => '0','message' => 'Invalid Order Manage List']);
            }
        }
        else
        {
            return response(['status' => '0','message' => 'Invalid Order Manage List']);
        }
    }

    // new retailer

    public function ssg_new_retailer_submit(Request $request)
    {
        $json = json_decode(json_encode($request->all()),true);

        //dd($request->all());
        DB::table('tbl_retailer')->insert(
            [
                'name'              => $json['retailer_info']['retailerName'],
                'division'          => $json['retailer_info']['division'],
                'territory'         => $json['retailer_info']['territory'],
                'rid'               => $json['retailer_info']['routeid'],
                'point_id'          => $json['retailer_info']['point_id'],
                'shop_type'         => $json['retailer_info']['shop_type'],
                'owner'             => $json['retailer_info']['owner'],
                'mobile'            => $json['retailer_info']['mobile'],
                'tnt'               => $json['retailer_info']['tnt'],
                'email'             => $json['retailer_info']['email'],
                'dateandtime'       => date('Y-m-d H:i:s', strtotime('+6 hour')),
                'user'              => $json['retailer_info']['userid'],
                'vAddress'          => $json['retailer_info']['retailerAddress'],
                'global_company_id' => $json['retailer_info']['global_company_id'],       
                'status'            => 1    // 1 for inactive & 0 for active
            ]
        );

        return response(['status' => '1','message' => 'Successfully added']);        
    }

    // new retailer-active-inactive-submit

    public function ssg_retailer_active_inactive_submit(Request $request)
    {
        $json = json_decode(json_encode($request->all()),true);
      
        //dd($request->all());
        DB::table('tbl_activation_retailers')->insert(
            [
                'routeId'               => $json['retailer_info']['routeid'],
                'retailerId'            => $json['retailer_info']['retailerid'],
                'status'                => $json['retailer_info']['status'],
                'done'                  => '2',
                'userId'                => $json['retailer_info']['userid'],          
                'global_company_id'     => $json['retailer_info']['global_company_id'],   
                'created_at'            => date('Y-m-d H:i:s')
            ]
        );

        return response(['status' => '1','message' => 'Successfully Request Send']);        
    }
}
