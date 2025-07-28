<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class AdminController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 21/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_new_retails()
    {
        $selectedMenu   = 'Admin';         // Required Variable
        $pageTitle      = 'Admin';        // Page Slug Title

        $routeResult = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
                        ->where('tbl_user_business_scope.is_active', 0)                         
                        ->where('tbl_user_business_scope.global_company_id', Auth::user()->global_company_id)  
                        ->where('tbl_user_business_scope.is_active', 0) 
                        ->groupBy('tbl_route.route_id')                  
                        ->get();

        return view('sales/fo_admin/visitManage', compact('selectedMenu','pageTitle','routeResult','routeID'));
    }

    public function ssg_retailer_all(Request $request)
    {
        $routeID = $request->get('route');

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','rid','owner','mobile','serial','vAddress','status')
                        ->where('global_company_id', Auth::user()->global_company_id)                       
                        ->where('rid', $routeID)
                        ->where('status', 0)
                        ->orderBy('serial','ASC')                    
                        ->get();

        return view('sales/fo_admin/retailers', compact('resultRetailer','routeID'));
    }

    public function ssg_fo_admin($serialid,$retailerid,$routeid)
    {

        $selectedMenu       = 'Admin';               // Required Variable Menu
        $selectedSubMenu    = '';                   // Required Variable Sub Menu
        $pageTitle          = 'Admin Manage';      // Page Slug Title
        
        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id','tbl_division.div_id','tbl_division.div_name','tbl_point.point_id','tbl_point.point_name')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                         ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id = '';
        $division_id = '';

        if(sizeof($resultFoInfo)>0)
        {
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
        }
        

        $resultDis = DB::table('users')
                        ->select('users.id','users.sap_code','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor                         
                         ->where('tbl_user_business_scope.point_id', $point_id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $resultRoute = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', Auth::user()->id)                    
                        ->groupBy('route_id')                    
                        ->orderBy('rname')                    
                        ->get();

        $resultTerritory = DB::table('tbl_user_business_scope')
                        ->select('tbl_territory.id','tbl_territory.name')
                        ->join('tbl_territory', 'tbl_territory.id', '=', 'tbl_user_business_scope.territory_id')
                        ->where('tbl_user_business_scope.point_id', $point_id) 
                        ->first();

        $resultRetailer = DB::table('tbl_retailer')                        
                        ->where('user', Auth::user()->id)
                        ->orderBy('retailer_id','DESC')
                        ->get();

        return view('sales/fo_admin/adminManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultDis','resultRoute','resultTerritory','resultRetailer','resultFoInfo'));
    }

    public function ssg_fo_admin_add(Request $request)
    {
        //dd($request->all());        

        $allRouteWiseRetailers = DB::table('tbl_retailer')                        
                        ->where('rid', $request->get('routes'))
                        //->where('point_id', $request->get('point'))
                        ->where('status', 0)
                        ->where('retailer_id', '!=', $request->get('retailerid'))
                        ->where('serial', '>=', $request->get('serial'))
                        ->orderBy('name','ASC')                    
                        ->get();

        foreach ($allRouteWiseRetailers as $value) 
        {
            # code...
            DB::table('tbl_retailer')->where('retailer_id',$value->retailer_id)
            ->update(
                [
                    'serial' => $value->serial + 1
                ]
            );
        }

        //dd($allRouteWiseRetailers);


        DB::table('tbl_retailer')->insert(
            [
                'name'              => $request->get('retailerName'),
                'division'          => $request->get('division'),
                'territory'         => $request->get('territory'),
                'rid'               => $request->get('routes'),
                'point_id'          => $request->get('point'),
                'shop_type'         => $request->get('type'),
                'owner'             => $request->get('ownerName'),
                //'sap_code'          => $request->get('sap_code'),
                'mobile'            => '88'.$request->get('mobile'),
                'tnt'               => $request->get('tandt'),
                'email'             => $request->get('email'),
                'dateandtime'       => date('Y-m-d H:i:s', strtotime('+6 hour')),
                'user'              => Auth::user()->id,
                'vAddress'          => $request->get('address'),
                'global_company_id' => Auth::user()->global_company_id,       
                'after_retailers'   => $request->get('retailerid'),       
                'serial'            => $request->get('serial')+1,       
                'status'            => 1    // 1 for inactive & 0 for active
            ]
        );

        return Redirect::to('fo/new-retails')->with('success', Auth::user()->display_name.'  Successfully Retailer ADD.');        
    }

    public function ssg_fo_admin_active(Request $request)
    {
        $sqlRetailerUpdate = DB::table('tbl_retailer')->where('user',Auth::user()->id)->update(
            [
                'status' => $request->get('status')
            ]
        );

        return $sqlRetailerUpdate;        
    }



    // Activation Request

    public function ssg_fo_activation()
    {

        $selectedMenu       = 'Admin';                                       // Required Variable Menu
        $selectedSubMenu    = '';                                           // Required Variable Sub Menu
        $pageTitle          = 'Activation / Inactive Request Manage';      // Page Slug Title  

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id','tbl_division.div_id','tbl_division.div_name','tbl_point.point_id','tbl_point.point_name')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                         ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', Auth::user()->id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id = '';
        $division_id = '';

        if(sizeof($resultFoInfo)>0)
        {
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
        }      

        $resultRoute = DB::table('tbl_route')
                        ->select('route_id','rname')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('status', 0) // 0 for active
                        ->where('point_id', $point_id)
                        ->orderBy('rname','ASC')
                        ->get();

        $activation = DB::table('tbl_activation_retailers')
                        ->select('tbl_activation_retailers.*','tbl_retailer.retailer_id','tbl_retailer.name','tbl_route.route_id','tbl_route.rname')
                        ->join('tbl_route', 'tbl_activation_retailers.routeId', '=', 'tbl_route.route_id')
                        ->join('tbl_retailer', 'tbl_activation_retailers.retailerId', '=', 'tbl_retailer.retailer_id')
                        ->where('tbl_activation_retailers.global_company_id', Auth::user()->global_company_id)
                        ->where('tbl_activation_retailers.userId', Auth::user()->id)                        
                        ->orderBy('tbl_activation_retailers.id','DESC')
                        ->get();

        return view('sales.fo_admin.activationManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','activation'));
    }

    public function ssg_fo_activation_submit(Request $request)
    {
        DB::table('tbl_activation_retailers')->insert(
            [
                'routeId'               => $request->get('routes'),
                'retailerId'            => $request->get('retailer'),
                'status'                => $request->get('status'),
                'done'                  => '2',
                'userId'                => Auth::user()->id,                
                'global_company_id'     => Auth::user()->global_company_id,       
                'created_at'            => date('Y-m-d H:i:s')
            ]
        );

        return Redirect::back()->with('success', Auth::user()->display_name.'  Successfully Request Send.');        
    }



    // for admin
    public function ssg_fo_admin_activation()
    {

        $selectedMenu       = 'Request Manage';                                       // Required Variable Menu
        $selectedSubMenu    = '';                                           // Required Variable Sub Menu
        $pageTitle          = 'Activation / Inactive Request Manage';      // Page Slug Title  

        $activation = DB::table('tbl_activation_retailers')
                        ->select('tbl_activation_retailers.*','tbl_retailer.retailer_id','tbl_retailer.name','tbl_user_business_scope.point_id','tbl_route.route_id','tbl_route.rname','users.display_name','tbl_user_details.cell_phone','tbl_point.point_id','tbl_point.point_name')

                        ->join('users', 'tbl_activation_retailers.userId', '=', 'users.id')
                        ->join('tbl_user_details', 'tbl_activation_retailers.userId', '=', 'tbl_user_details.user_id')
                        ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                        ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')

                        ->join('tbl_route', 'tbl_activation_retailers.routeId', '=', 'tbl_route.route_id')
                        ->join('tbl_retailer', 'tbl_activation_retailers.retailerId', '=', 'tbl_retailer.retailer_id')
                        
                        ->where('tbl_activation_retailers.global_company_id', Auth::user()->global_company_id)                        
                        ->orderBy('tbl_activation_retailers.id','DESC')
                        ->get();

        return view('sales.fo_admin.activationAdminManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','activation'));
    }

    public function ssg_fo_admin_activation_done($id)
    {

        $selectedMenu       = 'Request Manage';                                       // Required Variable Menu
        $selectedSubMenu    = '';                                           // Required Variable Sub Menu
        $pageTitle          = 'Activation / Inactive Request Manage';      // Page Slug Title  


        $request = DB::table('tbl_activation_retailers')->where('id',$id)->first();

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id','tbl_division.div_id','tbl_division.div_name','tbl_point.point_id','tbl_point.point_name')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                         ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', $request->userId)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id       = '';
        $division_id    = '';

        if(sizeof($resultFoInfo)>0)
        {
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
        }      

        $resultRoute = DB::table('tbl_route')
                        ->select('route_id','rname')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('status', 0) // 0 for active
                        ->where('point_id', $point_id)
                        ->orderBy('rname','ASC')
                        ->get();

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('retailer_id','name','point_id')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        //->where('status', 0) // 0 for active
                        ->where('point_id', $point_id)
                        ->orderBy('name','ASC')
                        ->get();

        return view('sales.fo_admin.activationAdminEditManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultRoute','activation','request','resultRetailer'));
    }

    public function ssg_fo_admin_activation_submit(Request $request)
    {

        DB::table('tbl_activation_retailers')->where('id',$request->get('id'))->update(
            [
                'routeId'               => $request->get('routes'),
                'retailerId'            => $request->get('retailer'),
                'done'                  => $request->get('done'),
                'updated_at'            => date('Y-m-d H:i:s')
            ]
        );

        DB::table('tbl_retailer')->where('retailer_id',$request->get('retailer'))->update(
            [
                'status'                => $request->get('done')
            ]
        );

        return Redirect::to('/admin/activation')->with('success','Successfully Request Save.');        
    }




    // ADMIN 

    public function ssg_fo_admin_new_retailer()
    {

        $selectedMenu       = 'Request Manage';               // Required Variable Menu
        $selectedSubMenu    = '';                            // Required Variable Sub Menu
        $pageTitle          = 'New Retailer Manage';        // Page Slug Title
        

        $resultRetailer = DB::table('tbl_retailer')
                        ->select('tbl_retailer.*','users.id','users.display_name','tbl_point.point_name','tbl_route.route_id','tbl_route.rname','tbl_user_details.cell_phone') 
                        ->join('users', 'tbl_retailer.user', '=', 'users.id')
                        ->join('tbl_user_details', 'tbl_retailer.user', '=', 'tbl_user_details.user_id')
                        ->join('tbl_point', 'tbl_retailer.point_id', '=', 'tbl_point.point_id')
                        ->join('tbl_route', 'tbl_retailer.rid', '=', 'tbl_route.route_id')
                        ->whereNotNull('tbl_retailer.user')
                        ->where('tbl_retailer.status',1)
                        ->orderBy('tbl_retailer.retailer_id','DESC')
                        ->get();

        return view('sales/fo_admin/adminrNewRetailerManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultRetailer'));
    }

    public function ssg_fo_admin_retailer_done($id)
    {

        $selectedMenu       = 'Request Manage';               // Required Variable Menu
        $selectedSubMenu    = '';                            // Required Variable Sub Menu
        $pageTitle          = 'New Retailer Manage';        // Page Slug Title
        

        $resultRetailer = DB::table('tbl_retailer')
                        ->where('retailer_id',$id)
                        ->first();

        //dd($resultRetailer);

        $resultFoInfo   = DB::table('users')
                        ->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id','tbl_division.div_id','tbl_division.div_name','tbl_point.point_id','tbl_point.point_name')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->join('tbl_division', 'tbl_user_business_scope.division_id', '=', 'tbl_division.div_id')
                         ->join('tbl_point', 'tbl_user_business_scope.point_id', '=', 'tbl_point.point_id')
                         ->where('tbl_user_type.user_type_id', 12)
                         ->where('users.id', $resultRetailer->user)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $point_id = '';
        $division_id = '';

        if(sizeof($resultFoInfo)>0)
        {
            $point_id       = $resultFoInfo->point_id;
            $division_id    = $resultFoInfo->division_id;
        }
        

        $resultDis = DB::table('users')
                        ->select('users.id','users.sap_code','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id')
                         ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
                         ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                         ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
                         ->where('tbl_user_type.user_type_id', 5) // 5 for distributor                         
                         ->where('tbl_user_business_scope.point_id', $point_id)
                         ->where('users.is_active', 0) // 0 for active
                         ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
                         ->first();

        $resultRoute = DB::table('tbl_user_business_scope')
                        ->select('tbl_user_business_scope.point_id','tbl_user_business_scope.user_id','tbl_user_business_scope.territory_id','tbl_route.point_id','tbl_route.rname','tbl_route.route_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_user_business_scope.global_company_id')
                        ->join('tbl_route', 'tbl_route.point_id', '=', 'tbl_user_business_scope.point_id')
                        ->where('tbl_user_business_scope.user_id', $resultRetailer->user)                    
                        ->groupBy('tbl_route.route_id')                    
                        ->orderBy('tbl_route.rname')                    
                        ->get();

        $resultTerritory = DB::table('tbl_user_business_scope')
                        ->select('tbl_territory.id','tbl_territory.name')
                        ->join('tbl_territory', 'tbl_territory.id', '=', 'tbl_user_business_scope.territory_id')
                        ->where('tbl_user_business_scope.point_id', $point_id) 
                        ->first();

        return view('sales.fo_admin.adminrNewRetailerEditManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultDis','resultRoute','resultTerritory','resultRetailer','resultFoInfo'));
    }

    public function ssg_fo_admin_retailer_submit(Request $request)
    {
        //echo $request->get('sap_code');

        //dd($request->all());
        DB::table('tbl_retailer')->where('retailer_id',$request->get('retailerid'))->update(
            [
                'name'              => $request->get('retailerName'),
                'division'          => $request->get('division'),
                'territory'         => $request->get('territory'),
                'rid'               => $request->get('routes'),
                'point_id'          => $request->get('point'),
                'shop_type'         => $request->get('type'),
                'owner'             => $request->get('ownerName'),
                //'sap_code'          => $request->get('sap_code'),
                'mobile'            => '88'.$request->get('mobile'),
                'tnt'               => $request->get('tandt'),
                'email'             => $request->get('email'),
                'dateandtime'       => date('Y-m-d H:i:s', strtotime('+6 hour')),
                //'user'              => Auth::user()->id,
                'vAddress'          => $request->get('address'),
                //'global_company_id' => Auth::user()->global_company_id,       
                'status'            => 0    // 1 for inactive & 0 for active
            ]
        );

        return Redirect::to('admin/new-retailer')->with('success', Auth::user()->display_name.'  Successfully Retailer Active.');        
    }

    public function ssg_fo_admin_retailer_delete(Request $request)
    {

        $done = DB::table('tbl_retailer')->where('retailer_id',$request->get('id'))->delete();

        return 0;

        //return Redirect::to('admin/new-retailer')->with('success', Auth::user()->display_name.'  Successfully Retailer Delete.');        
    }

    public function retailer_req_delete($id)
    {

    $retailer_id  = $id;//$request->get('id');

    DB::table('tbl_retailer')->where('retailer_id',$retailer_id)->delete();

        return Redirect::back()->with('success','Successfully Deleted Retailer.');  
      
    }
}
