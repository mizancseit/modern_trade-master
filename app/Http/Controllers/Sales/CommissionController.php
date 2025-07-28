<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class CommissionController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 08/04/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_admin_commission()
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission Manage';        // Page Slug Title

        $commission =  DB::table('tbl_commission')
                        ->select('tbl_commission.*','tbl_business_type.business_type')

                        ->leftJoin('tbl_business_type','tbl_commission.businessType','=','tbl_business_type.business_type_id')
                        ->where('tbl_commission.userId', Auth::user()->id)  
                        ->where('tbl_commission.global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('tbl_commission.id','DESC')                  
                        ->get();          

        return view('sales.commission.commissionManage', compact('selectedMenu','pageTitle','commission'));
    }

    public function ssg_admin_commission_add()
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission ADD';        // Page Slug Title

        $businessType   =  DB::table('tbl_business_type')
                        ->where('is_active', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('business_type_id','DESC')                  
                        ->get(); 

        return view('sales.commission.addCommission', compact('selectedMenu','pageTitle','businessType'));
    }

    public function ssg_admin_commission_submit(Request $request)
    {
        DB::table('tbl_commission')->insert(
            [
                'minSlab'               => $request->get('minSlab'),
                'maxSlab'               => $request->get('maxSlab'),
                'rat'                   => $request->get('rat'),
                'businessType'          => $request->get('businessType'),
                'userId'                => Auth::user()->id,                    
                'status'                => $request->get('status'),
                'created_at'            => date('Y-m-d h:i:s'),
                'global_company_id'     => Auth::user()->global_company_id
            ]
        );

        return Redirect::to('/admin/commission')->with('success', 'Successfully Added Commission');
    }

    public function ssg_admin_commission_edit($id)
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission Edit';        // Page Slug Title

        $businessType   = DB::table('tbl_business_type')
                        ->where('is_active', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('business_type_id','DESC')                  
                        ->get();

        $edit           = DB::table('tbl_commission')
                        ->where('id', $id)  
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->first();

        return view('sales.commission.editCommission', compact('selectedMenu','pageTitle','businessType','edit'));
    }

    public function ssg_admin_commission_update(Request $request)
    {
        DB::table('tbl_commission')->where('id',$request->get('id'))->update(
            [
                'minSlab'               => $request->get('minSlab'),
                'maxSlab'               => $request->get('maxSlab'),
                'rat'                   => $request->get('rat'),
                'businessType'          => $request->get('businessType'),
                'userId'                => Auth::user()->id,                    
                'status'                => $request->get('status'),
                'updated_at'            => date('Y-m-d h:i:s'),
                'global_company_id'     => Auth::user()->global_company_id
            ]
        );

        return Redirect::to('/admin/commission')->with('success', 'Successfully Updated Commission');
    }



    // Except Category Commission

    public function ssg_admin_except_commission()
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission Except Category Manage';        // Page Slug Title

        $commission =  DB::table('tbl_except_category_commission')
                        ->select('tbl_except_category_commission.*','tbl_business_type.business_type','tbl_product_category.name')

                        ->leftJoin('tbl_business_type','tbl_except_category_commission.businessType','=','tbl_business_type.business_type_id')
                        ->leftJoin('tbl_product_category','tbl_except_category_commission.categoryId','=','tbl_product_category.id')
                        ->where('tbl_except_category_commission.userId', Auth::user()->id)  
                        ->where('tbl_except_category_commission.global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('tbl_except_category_commission.id','DESC')                  
                        ->get();

        return view('sales.commission.exceptCommissionManage', compact('selectedMenu','pageTitle','commission'));
    }

    public function ssg_admin_except_commission_add()
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission Except Category ADD';        // Page Slug Title

        $businessType   =  DB::table('tbl_business_type')
                        ->where('is_active', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('business_type_id','DESC')                  
                        ->get();

        $category   =  DB::table('tbl_product_category')
                        ->where('status', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('name','ASC')                  
                        ->get(); 

        return view('sales.commission.addExceptCommission', compact('selectedMenu','pageTitle','businessType','category'));
    }

    public function ssg_admin_except_commission_submit(Request $request)
    {
        DB::table('tbl_except_category_commission')->insert(
            [
                'categoryId'            => $request->get('categoryId'),
                'businessType'          => $request->get('businessType'),
                'userId'                => Auth::user()->id,                    
                'status'                => $request->get('status'),
                'created_at'            => date('Y-m-d h:i:s'),
                'global_company_id'     => Auth::user()->global_company_id
            ]
        );

        return Redirect::to('/admin/except-commission')->with('success', 'Successfully Added Commission');
    }

    public function ssg_admin_except_commission_edit($id)
    {

        $selectedMenu   = 'Commission Manage';         // Required Variable
        $pageTitle      = 'Commission Edit';        // Page Slug Title

        $businessType   = DB::table('tbl_business_type')
                        ->where('is_active', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('business_type_id','DESC')                  
                        ->get();

        $category   =  DB::table('tbl_product_category')
                        ->where('status', 0)  
                        ->where('global_company_id', Auth::user()->global_company_id)  
                        ->orderBy('name','ASC')                  
                        ->get(); 

        $edit           = DB::table('tbl_except_category_commission')
                        ->where('id', $id)  
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->first();

        return view('sales.commission.editExceptCommission', compact('selectedMenu','pageTitle','businessType','category','edit'));
    }

    public function ssg_admin_except_commission_update(Request $request)
    {
        DB::table('tbl_except_category_commission')->where('id',$request->get('id'))->update(
            [
                'categoryId'            => $request->get('categoryId'),
                'businessType'          => $request->get('businessType'),
                'userId'                => Auth::user()->id,                    
                'status'                => $request->get('status'),
                'created_at'            => date('Y-m-d h:i:s'),
                'global_company_id'     => Auth::user()->global_company_id
            ]
        );

        return Redirect::to('/admin/except-commission')->with('success', 'Successfully Updated Commission');
    }
}
