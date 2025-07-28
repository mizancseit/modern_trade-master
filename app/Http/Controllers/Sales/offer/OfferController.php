<?php

namespace App\Http\Controllers\Sales\Offer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Sales\RegularOfferProductModel;
use App\Models\Sales\SpecialOfferProductModel;
use App\Models\Sales\SpecialOfferSkuModel;
use App\Models\Sales\OfferSetupModel;
use App\Models\Sales\OfferValueWiseSetupModel;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class OfferController extends Controller
{
    //
    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }


    public function regular_offer_products()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Regular Offer';           // Required Variable for menu
        $pageTitle       = 'Regular Offer Products'; // Page Slug Title

        $pcategory=DB::table('tbl_product_category')->get();

        $resultRegularOffer  = DB::table('tbl_regular_offer_product')
                        ->select('tbl_regular_offer_product.id','tbl_regular_offer_product.oid','tbl_product.name AS pName','tbl_regular_offer_product.qty','tbl_regular_offer_product.and_pid','tbl_regular_offer_product.and_qty','tbl_regular_offer_product.and_pro_cat_id','tbl_regular_offer_product.slab','tbl_regular_offer_product.value','tbl_product_category.g_name AS type','tbl_regular_offer_product.catid AS catid','tbl_regular_offer_product.offerGroupId AS offerProId','tbl_product_category.name AS cName')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_regular_offer_product.pid')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_regular_offer_product.catid')
                        ->where('tbl_regular_offer_product.status', '=', '0')
                        ->orderBy('tbl_product_category.id','ASC')                    
                        ->get();

         return view('sales.offer.regular_offer_products' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','resultRegularOffer'));  

       
    }


    public function get_product(Request $request)
    {
          $id=$request->input('id');

          $product=DB::table('tbl_product')
                     ->where('category_id',$id)
                     ->get();

        return view('Master/getProduct' , compact('product'));
        
    }

    public function regular_offer_product_save(Request $request){

        $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

      
        if($request->get('sku') != '' )
        {
        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('sku'))
                     ->first();
            $andvalue = $request->get('and_qty') * $andProductValue->depo;
        } else {

           $andvalue = 0;
        }

        $product = new RegularOfferProductModel();
        $product->slab = $request->get('slab');
        $product->catid = $request->get('category');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid = $request->get('product');
        $product->qty = $request->get('qty');
        $product->value = $value;
        $product->ptype = $request->get('ptype');

        if($request->get('andCat')!='')
        {
            $product->and_pro_cat_id = $request->get('andCat');
            $product->and_pid = $request->get('sku');
            $product->and_qty = $request->get('and_qty');
            $product->and_value = $andvalue;
        } 
        
        $product->created_user = Auth::user()->id; 
        $product->save();

        return redirect('/offer/regular_offer_products')->with('success','Products add sucessfully.');
        
    }

    

    public function regular_products_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Regular Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit';        // Page Slug Title
        
        $catid = $request->get('offerGroupId');
        $sku_cat_id = $request->get('and_cat');
        $pcategory=DB::table('tbl_product_category')->get();

        $product=DB::table('tbl_product')
                     ->where('category_id', $catid)
                     ->get();

        $sku=DB::table('tbl_product')
                     ->where('category_id', $sku_cat_id)
                     ->get();

        
        $slID=$request->get('id');
        $productById = DB::table('tbl_regular_offer_product')
                            ->where('id',$slID)
                            ->first();
        return view('sales/offer/regular_products_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory','product','sku')); 
    }

    



    public function regular_product_edit_process(Request $request){

         $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('sku'))
                     ->first();
      
        if(sizeof($andProductValue)>0 )
        {
            $andvalue = $request->get('and_qty') * $andProductValue->depo;

        } else {

           $andvalue = 0;
        }

        $product = RegularOfferProductModel::find($request->get('id'));
        $product->slab = $request->get('slab');
        $product->catid = $request->get('category');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid   = $request->get('product');
        $product->qty   = $request->get('qty');
        $product->value = $value;
        $product->ptype = $request->get('ptype');
            $product->and_pro_cat_id = $request->get('andCat');
            $product->and_pid = $request->get('sku');
            $product->and_qty = $request->get('and_qty');
            $product->and_value = $andvalue;
       
        $product->updated_user = Auth::user()->id; 
        $product->save();
       

        return redirect('/offer/regular_offer_products')->with('success','Products Update sucessfully.');
        
    }

    public function deleteRegularProduct(Request $request)
    {
      $product = RegularOfferProductModel::find($request->get('id'));
      $product->delete();

      return redirect('/offer/regular_offer_products')->with('success','Products Delete sucessfully.');
    }

// regular SKU offer Start

     public function regular_sku_products()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Regular sku';           // Required Variable for menu
        $pageTitle       = 'Regular SKU Products'; // Page Slug Title

        $pcategory=DB::table('tbl_product_category')->get();

        $result_sku_products  = DB::table('tbl_regular_sku_products')
                        ->select('tbl_regular_sku_products.id','tbl_product.name AS pName','tbl_regular_sku_products.slab','tbl_regular_sku_products.qty','tbl_regular_sku_products.and_pro_cat_id','tbl_regular_sku_products.and_pid','tbl_regular_sku_products.and_qty','tbl_product_category.g_name AS type','tbl_regular_sku_products.catid AS catid','tbl_regular_sku_products.sku_id AS sku_id','tbl_regular_sku_products.offerGroupId AS offerProId','tbl_product_category.name AS cName')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_regular_sku_products.pid')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_regular_sku_products.catid')
                        ->where('tbl_regular_sku_products.status', '=', '0')
                        ->orderBy('tbl_product_category.id','ASC')                    
                        ->get();

         return view('sales.offer.regular_sku_products' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','result_sku_products'));  

       
    }

    public function regular_sku_product_save(Request $request){

        $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

      
        if($request->get('andProducts') != '' )
        {
        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('andProducts'))
                     ->first();
            $andvalue = $request->get('and_qty') * $andProductValue->depo;
        } else {

           $andvalue = 0;
        }

        DB::table('tbl_regular_sku_products')->insert(
              [
              'ptype'              => $request->get('ptype'),
              'slab'               => $request->get('slab'),
              'catid'              => $request->get('category'),
              'sku_id'             => $request->get('sku'),
              'offerGroupId'       => $request->get('groupCat'),
              'pid'                => $request->get('product'),
              'qty'                => $request->get('qty'),
              'value'              => $value,
              'and_pro_cat_id'     => $request->get('andCat'),
              'and_pid'            => $request->get('andProducts'),
              'and_qty'            => $request->get('and_qty'),
              'and_value'          => $andvalue,
              'created_user'       => Auth::user()->id
              ]
            );

        return redirect('/offer/regular_sku_products')->with('success','Products add sucessfully.');
        
    }

    public function regular_sku_products_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Regular sku Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit';        // Page Slug Title
        $catid = $request->get('offerGroupId');
        $sku_cat_id = $request->get('catid');
        $pcategory=DB::table('tbl_product_category')->get();

        $product=DB::table('tbl_product')
                     ->where('category_id', $catid)
                     ->get();
        $sku=DB::table('tbl_product')
                     ->where('category_id', $sku_cat_id)
                     ->get();
         $andcat=DB::table('tbl_product_category')
                     ->where('id', $request->get('andCat'))
                     ->get();

        $andProduct=DB::table('tbl_product')
                     ->where('category_id', $request->get('andCat'))
                     ->get();

        
        
        $slID=$request->get('id');

        $productById = DB::table('tbl_regular_sku_products')
                            ->where('id',$slID)
                            ->first();

        return view('sales/offer/regular_sku_products_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory','product','sku','andcat','andProduct')); 
    }


    public function regular_sku_product_edit_process(Request $request){

       $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

      
        if($request->get('andProducts') != '' )
        {
        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('andProducts'))
                     ->first();

            $andvalue = $request->get('and_qty') * $andProductValue->depo;


        } else {

           $andvalue = 0;
        }

        DB::table('tbl_regular_sku_products')->where('id', $request->get('id'))->update(
              [
              'ptype'              => $request->get('ptype'),
              'slab'               => $request->get('slab'),
              'catid'              => $request->get('category'),
              'sku_id'             => $request->get('sku'),
              'offerGroupId'       => $request->get('groupCat'),
              'pid'                => $request->get('product'),
              'qty'                => $request->get('qty'),
              'value'              => $value,
              'and_pro_cat_id'     => $request->get('andCat'),
              'and_pid'            => $request->get('andProducts'),
              'and_qty'            => $request->get('and_qty'),
              'and_value'          => $andvalue,
              'created_user'       => Auth::user()->id
              ]
            );

        return redirect('/offer/regular_sku_products')->with('success','Products Update sucessfully.');
        
    }

    public function regular_sku_product_delete(Request $request)
    {
      
       DB::table('tbl_regular_sku_products')->where('id',$request->get('id'))->delete();

      return redirect('/offer/regular_sku_products')->with('success','SKU Delete sucessfully.');
    }
    // Regular offer end

// Special Category offer Start

     public function special_offer_products()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Special Offer';           // Required Variable for menu
        $pageTitle       = 'Special Offer Products'; // Page Slug Title

        $pcategory=DB::table('tbl_product_category')->get();

        $resultSpecialproducts  = DB::table('tbl_special_offer_product')
                        ->select('tbl_special_offer_product.id','tbl_special_offer_product.oid','tbl_product.name AS pName','tbl_special_offer_product.slab','tbl_special_offer_product.qty','tbl_special_offer_product.and_qty','tbl_special_offer_product.and_pro_cat_id','tbl_special_offer_product.and_pid','tbl_product_category.g_name AS type','tbl_special_offer_product.catid AS catid','tbl_special_offer_product.offerGroupId AS offerProId','tbl_product_category.name AS cName')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_special_offer_product.pid')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_special_offer_product.catid')
                        ->where('tbl_special_offer_product.status', '=', '0')
                        ->orderBy('tbl_product_category.id','ASC')                    
                        ->get();

         return view('sales.offer.special_offer_products' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','resultSpecialproducts'));  

       
    }

    public function special_offer_product_save(Request $request){

        //dd($request->all());

        $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

      
        if($request->get('sku') != '' )
        {
        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('sku'))
                     ->first();
            $andvalue = $request->get('and_qty') * $andProductValue->depo;
        } else {

           $andvalue = 0;
        }

       
        $product = new SpecialOfferProductModel();
        $product->ptype = $request->get('ptype');
        $product->slab = $request->get('slab');
        $product->catid = $request->get('category');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid = $request->get('product');
        $product->qty = $request->get('qty');
        $product->value = $value;

		if($request->get('andCat')!='')
		{
			$product->and_pro_cat_id = $request->get('andCat');
			$product->and_pid = $request->get('sku');
			$product->and_qty = $request->get('and_qty');
			$product->and_value = $andvalue;
		} 
		
		
        $product->created_user = Auth::user()->id; 
        $product->save();

        return redirect('/offer/special_offer_products')->with('success','Products add sucessfully.');
        
    }

    public function special_products_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Special Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit';        // Page Slug Title
        $catid = $request->get('offerGroupId');
        $sku_cat_id = $request->get('and_cat');
        $pcategory=DB::table('tbl_product_category')->get();

        $product=DB::table('tbl_product')
                     ->where('category_id', $catid)
                     ->get();

        $sku=DB::table('tbl_product')
                     ->where('category_id', $sku_cat_id)
                     ->get();
       
        $slID=$request->get('id');
        $productById = DB::table('tbl_special_offer_product')
                            ->where('id',$slID)
                            ->first();
        return view('sales/offer/special_products_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory','product','sku')); 
    }

    public function special_product_edit_process(Request $request){

        $product = SpecialOfferProductModel::find($request->get('id'));
        $product->oid   = $request->get('offerid');
        $product->ptype = $request->get('ptype');
        $product->slab  = $request->get('slab');
        $product->catid = $request->get('category');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid   = $request->get('product');
        $product->qty   = $request->get('qty');
        $product->value = $request->get('value');
        $product->and_pro_cat_id = $request->get('andCat');
        $product->and_pid   = $request->get('sku');
        $product->and_qty   = $request->get('and_qty');
        $product->and_value = $request->get('value');
        $product->updated_user = Auth::user()->id; 
        $product->save();

        return redirect('/offer/special_offer_products')->with('success','Products Update sucessfully.');
        
    }


    public function specialProductDelete(Request $request)
    {
      $offersetup = SpecialOfferProductModel::find($request->get('id'));
      $offersetup->delete();

      return redirect('/offer/special_offer_products')->with('success','Products Delete sucessfully.');
    }

// Special Category offer end

// Special SKU offer Start

     public function special_sku_products()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Special sku';           // Required Variable for menu
        $pageTitle       = 'Special SKU Products'; // Page Slug Title

        $pcategory=DB::table('tbl_product_category')->get();

        $result_sku_products  = DB::table('tbl_special_sku_products')
                        ->select('tbl_special_sku_products.id','tbl_product.name AS pName','tbl_special_sku_products.slab','tbl_special_sku_products.qty','tbl_special_sku_products.and_pro_cat_id','tbl_special_sku_products.and_pid','tbl_special_sku_products.and_qty','tbl_product_category.g_name AS type','tbl_special_sku_products.catid AS catid','tbl_special_sku_products.sku_id AS sku_id','tbl_special_sku_products.offerGroupId AS offerProId','tbl_product_category.name AS cName')
                        ->join('tbl_product', 'tbl_product.id', '=', 'tbl_special_sku_products.pid')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_special_sku_products.catid')
                        ->where('tbl_special_sku_products.status', '=', '0')
                        ->orderBy('tbl_product_category.id','ASC')                    
                        ->get();

         return view('sales.offer.special_sku_products' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','result_sku_products'));  

       
    }

    public function get_special_offer_sku(Request $request)
    {
          $id=$request->input('id');

          $product=DB::table('tbl_product')
                     ->where('category_id',$id)
                     ->get();

        return view('Master.getOfferSku' , compact('product'));
        
    }

    public function special_sku_and_products(Request $request)
    {
          $id=$request->get('id');

          $product=DB::table('tbl_product')
                     ->where('category_id',$id)
                     ->get();

           
        return view('Master.getSkuAndProducts' , compact('product'));
        
    }

    public function special_sku_product_save(Request $request){

        $productValue=DB::table('tbl_product')
                     ->where('id', $request->get('product'))
                     ->first();
       
        $value = $request->get('qty') * $productValue->depo;

      
        if($request->get('andProducts') != '' )
        {
        $andProductValue=DB::table('tbl_product')
                     ->where('id', $request->get('andProducts'))
                     ->first();
            $andvalue = $request->get('and_qty') * $andProductValue->depo;
        } else {

           $andvalue = 0;
        }

        $product = new SpecialOfferSkuModel();
        $product->ptype = $request->get('ptype');
        $product->slab = $request->get('slab');
        $product->catid = $request->get('category');
        $product->sku_id = $request->get('sku');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid = $request->get('product');
        $product->qty = $request->get('qty');
        $product->value = $value;
        if($request->get('andCat')!='')
        {
            $product->and_pro_cat_id = $request->get('andCat');
            $product->and_pid = $request->get('andProducts');
            $product->and_qty = $request->get('and_qty');
            $product->and_value = $andvalue;
        } 


        $product->created_user = Auth::user()->id; 
        $product->save();

        return redirect('/offer/special_sku_products')->with('success','Products add sucessfully.');
        
    }

    public function special_sku_products_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Special Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit';        // Page Slug Title
        $catid = $request->get('offerGroupId');
        $sku_cat_id = $request->get('catid');
        $pcategory=DB::table('tbl_product_category')->get();

        $product=DB::table('tbl_product')
                     ->where('category_id', $catid)
                     ->get();
        $sku=DB::table('tbl_product')
                     ->where('category_id', $sku_cat_id)
                     ->get();
         $andcat=DB::table('tbl_product_category')
                     ->where('id', $request->get('andCat'))
                     ->get();

        $andProduct=DB::table('tbl_product')
                     ->where('category_id', $request->get('andCat'))
                     ->get();

        
        
        $slID=$request->get('id');

        $productById = DB::table('tbl_special_sku_products')
                            ->where('id',$slID)
                            ->first();

        return view('sales/offer/special_sku_products_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory','product','sku','andcat','andProduct')); 
    }

    public function special_sku_product_edit_process(Request $request){

        $product = SpecialOfferSkuModel::find($request->get('id'));

        $product->ptype = $request->get('ptype');
        $product->slab  = $request->get('slab');
        $product->catid = $request->get('category');
        $product->sku_id = $request->get('sku');
        $product->offerGroupId = $request->get('groupCat');
        $product->pid   = $request->get('product');
        $product->qty   = $request->get('qty');
        $product->value = $request->get('value');
        $product->and_pro_cat_id = $request->get('andCat');
        $product->and_pid   = $request->get('andProducts');
        $product->and_qty   = $request->get('and_qty');
        $product->updated_user = Auth::user()->id; 
        $product->save();

        return redirect('/offer/special_sku_products')->with('success','Products Update sucessfully.');
        
    }


    public function special_sku_product_Delete(Request $request)
    {
      $offersetup = SpecialOfferSkuModel::find($request->get('id'));
      $offersetup->delete();

      return redirect('/offer/special_sku_products')->with('success','SKU Delete sucessfully.');
    }

// Special SKU offer end

    public function regular_special_offer_setup()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Offer Setup';           // Required Variable for menu
        $pageTitle       = 'Offer Setup'; // Page Slug Title

        $setupProduct=DB::table('tbl_regular_special_offer')
                        ->select('gid')
                        ->get();

        $setupProduct = collect($setupProduct)->map(function($x){ return (array) $x; })->toArray();               

        //dd($setupProduct);
        $pcategory=DB::table('tbl_product_category')
                        ->where('status', '=', '0')
                        ->whereNotIn('id',$setupProduct)
                        ->get();          

         

        $resultRegularOffer  = DB::table('tbl_regular_special_offer AS rs')
                        ->select('rs.id','rs.gid AS catid','tbl_product_category.name AS cName','rs.s1','rs.p1','rs.s2','rs.p2','rs.s3','rs.p3','rs.s4','rs.p4','rs.s5','rs.p5','rs.s6','rs.p6','rs.s7','rs.p7','rs.s8','rs.p8','rs.s9','rs.p9','rs.s10','rs.p10')
                        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'rs.gid')
                        ->where('rs.status', '=', '0')
                        ->orderBy('tbl_product_category.id','ASC')                    
                        ->get();

         return view('sales.offer.offer_setup' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','resultRegularOffer'));  

       
    }

     

    public function offer_setup_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Regular Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit'; 
        $pcategory=DB::table('tbl_product_category')->get();
       
        $slID=$request->get('id');
        $productById = DB::table('tbl_regular_special_offer')
                            ->where('id',$slID)
                            ->first();
        return view('sales/offer/offer_setup_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory')); 
    }



    public function offerSetupDelete(Request $request)
    {
      $offersetup = OfferSetupModel::find($request->get('id'));
      $offersetup->delete();

      return redirect('/offer/regular_special_offer_setup')->with('success','Products Delete sucessfully.');
    }




    ///////////////////////////// OTHERS OFFER BY MASUD ///////////////////////////

    public function ssg_others()
    {
        $selectedMenu    = 'Master Offer';                    // Required Variable for menu
        $selectedSubMenu = 'Others';           // Required Variable for menu
        $pageTitle       = 'Others Products'; // Page Slug Title

        $pcategory=DB::table('tbl_product_category')->get();

        $resultRegularOffer  = DB::table('tbl_special_values_wise')
                        ->select('tbl_special_values_wise.*','tbl_business_type.business_type')
                        
                        ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'tbl_special_values_wise.business_type')
                        ->orderBy('tbl_special_values_wise.id','DESC')                    
                        ->get();

         return view('sales/offer/others/others_products' , compact('selectedMenu','selectedSubMenu','pageTitle','pcategory','resultRegularOffer'));
    }

    public function ssg_others_save(Request $request)
    {

        $offersetup = new OfferValueWiseSetupModel();
        $offersetup->group_id           = $request->group_id;
        $offersetup->min                = $request->min;
        $offersetup->max                = $request->max;
        $offersetup->commission_rate    = $request->commission;
        $offersetup->global_company_id  = Auth::user()->global_company_id; 
        $offersetup->business_type      = $request->ptype;
        $offersetup->status             = $request->status;
        $offersetup->save();

        $lastID = OfferValueWiseSetupModel::orderBy('id','DESC')->first();

        //dd($lastID->id);

        if( count($request->get('categorys')) >0 )
        {
            foreach ($request->get('categorys') as $key => $value) 
            {
               DB::table('tbl_special_value_wise_category')->insert(
                    [
                        'svwid'            => $lastID->id,
                        'categoryid'       => $request->get('categorys')[$key]
                    ]
                );
            }
        }

        return redirect('/offer/other-products')->with('success','Value Wise Offer Setup sucessfully.');        
    }

    public function ssg_others_edit(Request $request)
    {

        $selectedMenu    = 'Master Offer';                  // Required Variable for menu
        $selectedSubMenu = 'Special Offer';         // Required Variable for menu
        $pageTitle       = 'Products Edit';        // Page Slug Title

        $pcategory      = DB::table('tbl_product_category')
                          ->select('tbl_product_category.*','tbl_special_value_wise_category.categoryid','tbl_special_value_wise_category.svwid')
                          
                          ->leftJoin('tbl_special_value_wise_category', 'tbl_product_category.id', '=', 'tbl_special_value_wise_category.categoryid')
                          ->groupBy('tbl_product_category.id')
                          ->get();

        $productById    = OfferValueWiseSetupModel::where('id',$request->get('id'))->first(); 

        //dd($productById);       

        return view('sales/offer/others/others_products_edit',compact('selectedMenu','selectedSubMenu','pageTitle','productById','pcategory','product')); 
    }

    public function ssg_others_edit_process(Request $request)
    {

        $offersetup = OfferValueWiseSetupModel::find($request->get('id'));
        $offersetup->group_id           = $request->group_id;
        $offersetup->min                = $request->min;
        $offersetup->max                = $request->max;
        $offersetup->commission_rate    = $request->commission;
        $offersetup->global_company_id  = Auth::user()->global_company_id; 
        $offersetup->business_type      = $request->ptype;
        $offersetup->status             = $request->status;
        $offersetup->save();

        DB::table('tbl_special_value_wise_category')->where('svwid',$request->get('id'))->delete();

        if( count($request->get('categorys')) >0 )
        {
            foreach ($request->get('categorys') as $key => $value) 
            {
               DB::table('tbl_special_value_wise_category')->insert(
                    [
                        'svwid'            => $request->get('id'),
                        'categoryid'       => $request->get('categorys')[$key]
                    ]
                );
            }
        }

        return redirect('/offer/other-products')->with('success','Value Wise Offer Setup Update Sucessfully.');        
    }

    public function ssg_others_delete(Request $request)
    {
      $offersetup = OfferValueWiseSetupModel::find($request->get('id'));
      $offersetup->delete();

      DB::table('tbl_special_value_wise_category')->where('svwid',$request->get('id'))->delete();

      return redirect('/offer/other-products')->with('success','Products Delete sucessfully.');
    }


    //VALUE WISE COMMISSION NEW OPTIONS

    public function ssg_others_value_edit(Request $request)
    {
        //dd($request->all());

        $pro      = DB::table('tbl_product')
                    ->where('id',$request->get('pid'))
                    ->first();

        $pqty     = $request->get('pqty');
        $pvalue   = $request->get('pvalue');
        $primaryid= $request->get('primaryid');

        return view('sales/offer/valueWiseCommission/products_edit',compact('pro','pqty','primaryid','pvalue')); 
    }

    public function ssg_others_value_edit_process(Request $request)
    {
        //dd($request->all());

        DB::table('tbl_order_special_free_qty')->where('free_id',$request->get('primaryid'))
            ->update(
            [
                'total_free_qty'   => $request->get('freeqty'),
                'free_value'       => $request->get('freeValue'),
                'total_free_value' => $request->get('freeValue')
            ]
        );
            
        return redirect()->back()->with('success','Sucessfully Free Value wise commission qty add.');
    }

    public function ssg_others_value_delete(Request $request)
    { 

      DB::table('tbl_order_special_free_qty')->where('free_id',$request->get('freeid'))->delete();
      return 1;
    }

}
