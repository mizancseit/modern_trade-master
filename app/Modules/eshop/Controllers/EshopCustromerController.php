<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class EshopCustromerController extends Controller
{
    /*public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
*/

   public function customer_list()
    {
        $selectedMenu   = 'E-Shop Customer';                      // Required Variable for menu
        $selectedSubMenu= 'E-Shop Customert';                    // Required Variable for submenu
        $pageTitle      = 'E-Shop Customer';  
        
        $customerResult = DB::table('eshop_customer_list') 
        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
        ->where('eshop_customer_define_executive.executive_id', Auth::user()->id)
        // ->where('eshop_customer_list.status',0)
        ->orderBy('eshop_customer_list.name','ASC')    
        ->get(); 
        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        $resultFo       = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        // print_r($customerResult);
        //exit();
        // Page Slug Title 
        return view('eshop::customer/customerList', compact('selectedMenu','selectedSubMenu','pageTitle', 'customerResult' , 'shopType' , 'resultFo' ));
    }

    public function  customer_create(Request $request){
        $customerId = DB::table('eshop_customer_list')->insertGetId(
            [
                'name'              => $request->get('customer_name'),
                'mobile'            => $request->get('mobile_no'),
                'address'           => $request->get('address'),
                'credit_limit'      => $request->get('credit_limit'),
                'shop_type'         => $request->get('shop_type'),
                'customer_code'     => $request->get('customer_code'),
                'sap_code'          => $request->get('sap_code'), 
                'global_company_id' => Auth::user()->global_company_id,
                'entry_by'          => Auth::user()->id,
                'entry_date'        => date('Y-m-d h:i:s')
                
            ]
        );

    DB::table('eshop_customer_define_executive')->insert(
            [
                'customer_id'      => $customerId, 
                'executive_id'      => $request->get('executive_id'), 
                'global_company_id' => Auth::user()->global_company_id,
                'entry_by'          => Auth::user()->id,
                'entry_date'        => date('Y-m-d h:i:s')
                
            ]
        );

     return Redirect::to('/e-shop-customer')->with('success', 'Customer successfully Added.');
    }   
    
    public function customer_edit(Request $request){

        $selectedMenu   = 'Customer List';                      // Required Variable for menu
        $selectedSubMenu= 'Customer List';                    // Required Variable for submenu
        $pageTitle      = 'Customer List';            // Page Slug Title

        
        $resultcus       = DB::table('eshop_customer_list')
        ->join('eshop_customer_define_executive', 'eshop_customer_define_executive.customer_id', '=', 'eshop_customer_list.customer_id')
        ->where('eshop_customer_list.customer_id',$request->get('customer_id'))
        ->orderBy('eshop_customer_list.name','ASC')                    
        ->first();


         $resultFo  = DB::table('users') 
        ->where('user_type_id',7)                   
        ->where('is_active',0)  
        ->orderBy('display_name','DESC')                  
        ->get();

        $shopType       = DB::table('mts_route')
        ->where('status',0)    
        ->get();

        return view('eshop::customer/customer_edit', compact('selectedMenu','selectedSubMenu','pageTitle','resultcus','shopType','resultFo'));
      
     }

     

     public function customer_edit_process(Request $request){

        DB::table('eshop_customer_list')->where('customer_id',$request->get('id'))->update(
                 [
                     'name'              => $request->get('customer_name'),
                     'mobile'            => $request->get('mobile_no'),
                     'address'           => $request->get('address'),
                     'credit_limit'      => $request->get('credit_limit'),
                     'shop_type'         => $request->get('shop_type'),
                     'customer_code'     => $request->get('customer_code'),
                     'sap_code'          => $request->get('sap_code'),  
                     'update_by'          => Auth::user()->id,
                     'update_date'        => date('Y-m-d h:i:s')
                     
                 ]
             );
 
         DB::table('eshop_customer_define_executive')->where('customer_id',$request->get('id'))->update(
                 [
                     'executive_id'      => $request->get('executive_id'),  
                     'update_by'          => Auth::user()->id,
                     'update_date'        => date('Y-m-d h:i:s')
                     
                 ]
             );
 
             return back()->with('success','Customer update successfully.'); 
       } 

    public function eshop_customer_active($id){

        DB::table('eshop_customer_list')->where('customer_id',$id)->update(
            [
                
                'status'          => 1,  
                'update_by'       => Auth::user()->id,
                'update_date'     => date('Y-m-d h:i:s') 
            ]
        );

        DB::table('eshop_customer_define_executive')->where('customer_id',$id)->update(
            [
                
                'status'          => 1,  
                'update_by'       => Auth::user()->id,
                'update_date'     => date('Y-m-d h:i:s') 
            ]
        );

         return back()->with('success','Customer Inactive successfully.'); 
    }

     public function eshop_customer_inactive($id){

        DB::table('eshop_customer_list')->where('customer_id',$id)->update(
            [
                
                'status'          => 0,  
                'update_by'       => Auth::user()->id,
                'update_date'     => date('Y-m-d h:i:s') 
            ]
        );

         DB::table('eshop_customer_define_executive')->where('customer_id',$id)->update(
            [
                
                'status'          => 0,  
                'update_by'       => Auth::user()->id,
                'update_date'     => date('Y-m-d h:i:s') 
            ]
        );

        
         return back()->with('success','Customer Active successfully.'); 
    }

     
}
