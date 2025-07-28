<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Session;
use DB;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $selectedMenu   = 'Category List';                      // Required Variable for menu
        $selectedSubMenu= 'Category List';                    // Required Variable for submenu
        $pageTitle      = 'Category List';            // Page Slug Title

        $resultProduct = DB::table('tbl_product_category')
        ->select('tbl_product_category.*')
        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'tbl_product_category.gid') 
        ->orderBy('tbl_product_category.id','DESC')                    
        ->get(); 
        $resultChannel  = DB::table('tbl_business_type')                  
        ->get();
 
        return view('ModernSales::category/index', compact('selectedMenu','pageTitle','resultProduct','resultChannel'));
    }
    

    public function create(Request $request){
        $resultChannel  = DB::table('tbl_business_type')->where('business_type_id',$request->get('channel'))->first();

        DB::table('tbl_product_category')->insert([ 
            'gid'               => $resultChannel->business_type_id,
            'g_name'            => $resultChannel->business_type,
            'g_code'            => $request->get('company_code'),
            'name'              => $request->get('category'),
            'short_name'        => '', 
            'global_company_id' => 1,
            'status'            => 0,
            'unit'              => '', 
            'avg_price'         => '0.00',
            'factor'            => 1,
            'user'              => '',
            'order_by'          => 0,  
            'company_id'        => '',
            'plant_code'        => '',  
            'vat_percent'       => '0.80',
            'order_by_la'       => '',
            'top_group'         => '', 
            'top_name'          => '',
            'cat_id'            => '',
            'offer_group'       => 0,
            'LAF'               => '', 
            'offer_type'        => 1,
            'sync'              => 'Yes',
            'modern_channel_id' => ''
        ]); 
        return back()->with('success','Category successfully Added');  
    }

    public function edit(Request $request){

        $selectedMenu   = 'Product List';                      // Required Variable for menu
        $selectedSubMenu= 'Product List';                    // Required Variable for submenu
        $pageTitle      = 'Product List';            // Page Slug Title

        
        $resultProduct = DB::table('tbl_product_category')
        ->select('tbl_product_category.*')
        ->where('id',$request->get('product_id'))
        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'tbl_product_category.gid') 
        ->orderBy('tbl_product_category.id','DESC')                    
        ->first(); 
        $resultChannel  = DB::table('tbl_business_type')                  
        ->get(); 
        return view('ModernSales::category/edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultProduct','resultCat','resultChannel'));

    }

    public function update(Request $request){
        $resultChannel  = DB::table('tbl_business_type')->where('business_type_id',$request->get('channel'))->first(); 
        DB::table('tbl_product_category')->where('id',$request->get('id'))->update([
            'gid'               => $resultChannel->business_type_id,
            'g_name'            => $resultChannel->business_type,
            'g_code'            => $request->get('company_code'),
            'name'              => $request->get('product_name'), 
            'status'            => $request->get('status'), 
        ]);
        return back()->with('success','Category successfully Added');
    }
} 
