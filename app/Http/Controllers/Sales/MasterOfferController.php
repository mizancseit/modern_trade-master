<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class MasterOfferController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 02/02/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }


    // Bundle Offer Management

    public function ssg_bundle_offer()
    {

        $selectedMenu       = 'Master Offer';          // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer';        // Page Slug Title

        $resultBundleOffer  = DB::table('tbl_bundle_offer')
                            ->select('tbl_bundle_offer.*','tbl_business_type.business_type_id','tbl_business_type.business_type')
                            ->leftJoin('tbl_business_type','tbl_bundle_offer.iBusinessType','=','tbl_business_type.business_type_id')
                            ->orderBy('tbl_bundle_offer.iId','DESC')
                            ->get();

        return view('sales.offer.masterBundleOfferManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultBundleOffer'));
    }

    public function ssg_bundle_offer_add()
    {

        $selectedMenu       = 'Master Offer';          // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer';        // Page Slug Title

                            
        $resultDivision = DB::table('tbl_division')->get();
        $resultBusinessType = DB::table('tbl_business_type')
                            ->where('global_company_id',Auth::user()->global_company_id)
                            ->where('is_active',0)
                            ->get();

        return view('sales.offer.masterBundleOfferAdd', compact('selectedMenu','selectedSubMenu','pageTitle','resultDivision','resultBusinessType'));
    }


    public function ssg_bundle_category(Request $request)
    {
        if($request->get('businessType')==4)
        {
            $resultCategory = DB::table('tbl_product_category')
                            ->where('global_company_id',Auth::user()->global_company_id)
                            //->where('gid',$request->get('businessType'))
                            ->get();
        }
        else
        {
            $resultCategory = DB::table('tbl_product_category')
                            ->where('global_company_id',Auth::user()->global_company_id)
                            ->where('gid',$request->get('businessType'))
                            ->get();
        }        

        $serial       = 11;

        return view('sales.offer.allReplaceValue', compact('serial','resultCategory'));
    }

    public function ssg_bundle_division_wise_points(Request $request)
    {
        $resultPoints = DB::table('tbl_point')
                        ->select('tbl_point.business_type_id','tbl_point.point_id','tbl_point.point_name','tbl_point.point_division','tbl_division.div_id','tbl_division.div_name')
                         ->join('tbl_division', 'tbl_point.point_division', '=', 'tbl_division.div_id') 
                         ->where('tbl_point.business_type_id', $request->get('businessType'))                        
                         ->whereIn('tbl_point.point_division', $request->get('divisions'))
                         ->orderBy('tbl_division.div_id', 'ASC')
                         ->get();
       
        $serial       = 1;

        return view('sales.offer.allReplaceValue', compact('serial','resultPoints'));
    }

    public function ssg_bundle_point_wise_routes(Request $request)
    {
        $resultRoutes = DB::table('tbl_route')
                    ->select('tbl_route.route_id','tbl_route.rname','tbl_route.point_id','tbl_point.point_id','tbl_point.point_name','tbl_point.point_division')                        
                    ->join('tbl_point', 'tbl_route.point_id', '=', 'tbl_point.point_id')
                    ->join('tbl_division', 'tbl_point.point_division', '=', 'tbl_division.div_id')

                    ->whereIn('tbl_route.point_id', $request->get('points'))
                    ->orderBy('tbl_route.point_id', 'ASC')
                    ->get();
       
        $serial       = 2;

        return view('sales.offer.allReplaceValue', compact('serial','resultRoutes'));
    }

    public function ssg_bundle_offer_submit(Request $request)
    {

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        DB::table('tbl_bundle_offer')->insert(
            [
                'vOfferName'         => $request->get('offerName'),
                'dBeginDate'         => $fromdate,
                'dEndDate'           => $todate,
                'iOfferType'         => $request->get('offerTypes'),
                'iBusinessType'      => $request->get('offerBusinessTypes'),
                'iStatus'            => $request->get('offerStatus'),
                'created_at'         => date('Y-m-d H:i:s'),
                'global_company_id'  => Auth::user()->global_company_id,
                'vCreateUser'        => Auth::user()->id
            ]
        );

        $offerLastId = DB::table('tbl_bundle_offer')->orderBy('iId','DESC')->first(); 
        
        // for slab
        if( count($request->get('from_slab1')) >0 )
        {
            foreach ($request->get('from_slab1') as $key => $value) 
            {
                if($request->get('from_slab1')[$key]!='')
                {
                    DB::table('tbl_bundle_slab')->insert(
                        [
                            'iOfferId'         => $offerLastId->iId,
                            'iMinRange'        => $request->get('from_slab1')[$key],
                            'iMaxRange'        => $request->get('to_slab1')[$key],
                            'created_at'       => date('Y-m-d H:i:s'),
                            'vCreateUser'      => Auth::user()->id
                        ]
                    );
                }                   
            }
        }

        // for division
        if( count($request->get('divisions')) >0 )
        {
            foreach ($request->get('divisions') as $key => $value) 
            {
                DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId->iId,
                        'iDivId'           => $request->get('divisions')[$key],
                        'created_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            }
        }

        $arrayPoint = array();
        // for division wise points
        if( count($request->get('points')) >0 )
        {
            foreach ($request->get('points') as $key => $value) 
            {
               $convertPoints = explode('_',$request->get('pointsID')[$key]);
                
               DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId->iId,
                        'iDivId'           => $convertPoints[0],
                        'iPointId'         => $convertPoints[1],
                        'created_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            } 
        }
        else
        {
            $allPoint = DB::table('tbl_point')
            ->whereIn('point_division',$request->get('divisions'))
            ->get();

            foreach ($allPoint as $value)
            {
                $arrayPoint[] = $value->point_id;

                DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId->iId,
                        'iDivId'           => $value->point_division,
                        'iPointId'         => $value->point_id,
                        'created_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            }
        }
        

        // for division/points/route
        if( count($request->get('routes')) >0 )
        {
            foreach ($request->get('routes') as $key => $value) 
            {
               $convertRoutes = explode('_',$request->get('routesID')[$key]);
                
               DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId->iId,
                        'iDivId'           => $convertRoutes[0],
                        'iPointId'         => $convertRoutes[1],
                        'iRouteId'         => $convertRoutes[2],
                        'created_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            }
        }
        else
        {
            $allPoint = DB::table('tbl_route')
            ->select('tbl_route.*','tbl_point.point_id','tbl_point.point_division')
            ->leftJoin('tbl_point', 'tbl_route.point_id', '=', 'tbl_point.point_id')
            ->whereIn('tbl_route.point_id',$arrayPoint)
            ->get();

            foreach ($allPoint as $value)
            {
                DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId->iId,
                        'iDivId'           => $value->point_division,
                        'iPointId'         => $value->point_id,
                        'iRouteId'         => $value->route_id,
                        'created_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            } 
        } 

        // for category
        if( count($request->get('category')) >0 )
        {
            foreach ($request->get('category') as $key => $value) 
            {
               DB::table('tbl_bundle_category')->insert(
                    [
                        'offerId'         => $offerLastId->iId,
                        'categoryId'      => $request->get('category')[$key],
                        'created_at'      => date('Y-m-d H:i:s'),
                        'createUser'      => Auth::user()->id
                    ]
                );
            }
        }      

        return Redirect::to('/offers/bundle')->with('success', 'Successfully Added Bundle Offer.');
    }


    public function ssg_bundle_offer_edit($offerid)
    {
        $selectedMenu       = 'Master Offer';          // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer Update';        // Page Slug Title

        //dd($request->get('offerid'));

        $resultBundleOffer = DB::table('tbl_bundle_offer')
                    ->where('iId', $offerid)
                    ->where('global_company_id', Auth::user()->global_company_id)
                    ->first();

        $resultBundleSlab = DB::table('tbl_bundle_slab')
                    ->where('iOfferId', $offerid)                    
                    ->get();

        $resultDivision = DB::table('tbl_division')->get();

        $resultBusinessType = DB::table('tbl_business_type')
                            ->where('global_company_id',Auth::user()->global_company_id)
                            ->where('is_active',0)
                            ->get();

        $resultCategory = DB::table('tbl_product_category')
                            ->where('global_company_id',Auth::user()->global_company_id)
                            ->where('gid',$resultBundleOffer->iBusinessType)
                            ->get();

        //dd($resultBundleOffer->iBusinessType);

        return view('sales.offer.masterBundleOfferEdit', compact('selectedMenu','selectedSubMenu','pageTitle','serial','resultDivision','resultBundleOffer','resultBundleSlab','resultBusinessType','resultCategory'));
    }

    public function ssg_bundle_offer_update(Request $request)
    {

        $fromdate   = date('Y-m-d', strtotime($request->get('fromdate')));
        $todate     = date('Y-m-d', strtotime($request->get('todate')));

        DB::table('tbl_bundle_offer')->where('iId',$request->get('offerid'))->update(
            [
                'vOfferName'         => $request->get('offerName'),
                'dBeginDate'         => $fromdate,
                'dEndDate'           => $todate,
                'iOfferType'         => $request->get('offerTypes'),
                'iBusinessType'      => $request->get('businessType'),
                'iStatus'            => $request->get('offerStatus'),
                'updated_at'         => date('Y-m-d H:i:s'),
                'global_company_id'  => Auth::user()->global_company_id,
                'vCreateUser'        => Auth::user()->id
            ]
        );

        $offerLastId = $request->get('offerid'); 
        
        // for slab
        if( count($request->get('from_slab1')) >0 )
        {
            DB::table('tbl_bundle_slab')->where('iOfferId',$request->get('offerid'))->delete();

            foreach ($request->get('from_slab1') as $key => $value) 
            {
                if($request->get('from_slab1')[$key]!='')
                {
                    DB::table('tbl_bundle_slab')->insert(
                        [
                            'iOfferId'         => $offerLastId,
                            'iMinRange'        => $request->get('from_slab1')[$key],
                            'iMaxRange'        => $request->get('to_slab1')[$key],
                            'created_at'       => date('Y-m-d H:i:s'),
                            'vCreateUser'      => Auth::user()->id
                        ]
                    );
                }
            }
        }

        // for division
        if( count($request->get('divisions')) >0 )
        {
            DB::table('tbl_bundle_offer_scope')->where('iOfferId',$request->get('offerid'))->delete();

            foreach ($request->get('divisions') as $key => $value) 
            {
               DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId,
                        'iDivId'           => $request->get('divisions')[$key],
                        'updated_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            }
        }

        // for division wise points
        if( count($request->get('points')) >0 )
        {
           foreach ($request->get('points') as $key => $value) 
            {
               $convertPoints = explode('_',$request->get('pointsID')[$key]);
                
               DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId,
                        'iDivId'           => $convertPoints[0],
                        'iPointId'         => $convertPoints[1],
                        'updated_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            } 
        }
        

        // for division/points/route
        if( count($request->get('routes')) >0 )
        {
            foreach ($request->get('routes') as $key => $value) 
            {
               $convertRoutes = explode('_',$request->get('routesID')[$key]);
                
               DB::table('tbl_bundle_offer_scope')->insert(
                    [
                        'iOfferId'         => $offerLastId,
                        'iDivId'           => $convertRoutes[0],
                        'iPointId'         => $convertRoutes[1],
                        'iRouteId'         => $convertRoutes[2],
                        'updated_at'       => date('Y-m-d H:i:s'),
                        'vCreateUser'      => Auth::user()->id
                    ]
                );
            }
        }  

        // for category
        if( count($request->get('category')) >0 )
        {
            DB::table('tbl_bundle_category')->where('offerId',$request->get('offerid'))->delete();

            foreach ($request->get('category') as $key => $value) 
            {
               DB::table('tbl_bundle_category')->insert(
                    [
                        'offerId'         => $offerLastId,
                        'categoryId'      => $request->get('category')[$key],
                        'created_at'      => date('Y-m-d H:i:s'),
                        'createUser'      => Auth::user()->id
                    ]
                );
            }
        }         

        return Redirect::to('/offers/bundle')->with('success', 'Successfully Updated Bundle Offer.');
    }


    public function ssg_bundle_offer_active_inactive(Request $request)
    {
        DB::table('tbl_bundle_offer')->where('iId',$request->get('offerid'))->update(
            [
                'iStatus' => $request->get('offerStatus')
            ]

        );

        return 0;           
    }

    public function ssg_bundle_offer_delete(Request $request)
    {
        DB::table('tbl_bundle_offer')->where('iId',$request->get('offerid'))->delete();
        DB::table('tbl_bundle_slab')->where('iOfferId',$request->get('offerid'))->delete();
        DB::table('tbl_bundle_offer_scope')->where('iOfferId',$request->get('offerid'))->delete();
        DB::table('tbl_bundle_category')->where('offerId',$request->get('offerid'))->delete();

        return 0;           
    }


    // Bundle Offer Product Management 

     public function ssg_bundle_product()
    {

        $selectedMenu       = 'Master Offer';                  // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer Product';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer Product';        // Page Slug Title

        $resultBundleOffer = DB::table('tbl_bundle_product_details')
                            ->select('tbl_bundle_product_details.*','tbl_bundle_offer.vOfferName','tbl_bundle_offer.iId','tbl_bundle_offer.global_company_id','tbl_product_category.name as CatName','tbl_product.name as ProName','tbl_bundle_slab.iMinRange','tbl_bundle_slab.iMaxRange') 

                            ->join('tbl_bundle_offer', 'tbl_bundle_product_details.offerId', '=', 'tbl_bundle_offer.iId')

                            ->leftJoin('tbl_product_category', 'tbl_bundle_product_details.categoryId', '=', 'tbl_product_category.id')

                            ->leftJoin('tbl_product', 'tbl_bundle_product_details.giftName', '=', 'tbl_product.id')

                            ->leftJoin('tbl_bundle_slab', 'tbl_bundle_product_details.slabId', '=', 'tbl_bundle_slab.iId')

                            ->where('tbl_bundle_offer.global_company_id', Auth::user()->global_company_id)                            
                            ->orderBy('tbl_bundle_product_details.id', 'DESC')
                            ->groupBy('tbl_bundle_product_details.id')
                            ->get();

        // $resultBundleOffer  = $resultRoutes = DB::table('tbl_bundle_products')
        //                     ->select('tbl_bundle_products.*','tbl_bundle_offer.iId AS offerid','tbl_bundle_offer.vOfferName','tbl_bundle_offer.dBeginDate','tbl_bundle_offer.dEndDate','tbl_bundle_offer.iStatus','tbl_bundle_offer.iOfferType') 

        //                     ->join('tbl_bundle_offer', 'tbl_bundle_products.offerId', '=', 'tbl_bundle_offer.iId')
        //                     ->where('tbl_bundle_products.global_company_id', Auth::user()->global_company_id)
        //                     ->groupBy('tbl_bundle_products.offerId')
        //                     ->orderBy('tbl_bundle_offer.iId', 'DESC')
        //                     ->get();

        return view('sales/offer/masterBundleOfferProductManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultBundleOffer'));
    }

    public function ssg_bundle_product_add()
    {

        $selectedMenu       = 'Master Offer';                        // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer Product';               // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer Product New';          // Page Slug Title

                            
        $resultBundleOffer  = DB::table('tbl_bundle_offer')                            
                            ->where('iStatus','1')
                            ->where('iOfferType','3')
                            ->orderBy('iId','DESC')
                            ->get();

        return view('sales.offer.masterBundleOfferProAdd', compact('selectedMenu','selectedSubMenu','pageTitle','resultBundleOffer'));
    }

    public function ssg_bundle_offer_details(Request $request)
    {
        $resultBundleOfferDetails  = DB::table('tbl_bundle_offer')                            
                            ->where('iId', $request->get('offerid'))
                            ->first();

        $resultBundleOfferSlab  = DB::table('tbl_bundle_slab')->select('iId','iOfferId','iMinRange','iMaxRange','iStatus')                            
                            ->where('iOfferId',$request->get('offerid'))
                            ->get();
       
        $serial       = 3;

        return view('sales.offer.allReplaceValue', compact('serial','resultBundleOfferDetails','resultBundleOfferSlab'));
    }

    public function ssg_bundle_offer_types(Request $request)
    {
        $resultSSGProductCat  = DB::table('tbl_product_category')
                             ->orderBy('id','DESC')
                             ->get();

        if($request->get('typeid')==1)
        {
            $serial       = 5;
        }
        else
        {
            $serial       = 4;
        } 

        return view('sales.offer.allReplaceValue', compact('serial','resultSSGProductCat'));
    }

    public function ssg_bundle_offer_category_wise_pro(Request $request)
    {
        $resultSSGProduct  = DB::table('tbl_product')
                             ->where('category_id', $request->get('categoryid'))
                             ->orderBy('id','DESC')
                             ->get();

        $resultCategory  = DB::table('tbl_product_category')
                             ->where('id', $request->get('categoryid'))
                             ->first();

        $serial       = 6;        

        return view('sales.offer.allReplaceValue', compact('serial','resultCategory','resultSSGProduct'));
    }


    public function ssg_bundle_offer_pro_submit(Request $request)
    {
        $qtyStock = 0;
        if($request->get('type')==1) // for ssg product
        {
            foreach ($request->get('pqty') as $key => $value) 
            {
                if($value!=null)
                {
                    $qtyStock = $qtyStock + $value;
                }
            }            
        }
        else
        {
            foreach ($request->get('to_slab1') as $key => $value) 
            {
                $qtyStock = $qtyStock + $value;
            }
        }

        DB::table('tbl_bundle_products')->insert(
            [
                'offerId'            => $request->get('offerTypes'),
                'global_company_id'  => Auth::user()->global_company_id,
                'stockQty'           => $qtyStock,
                'productType'        => $request->get('type'),       
                'created_at'         => date('Y-m-d H:i:s'),
                'createUser'         => Auth::user()->id
            ]
        );

        $product = DB::table('tbl_bundle_products')
                    ->where('global_company_id',Auth::user()->global_company_id)
                    ->orderBy('id','DESC')->first();
        $lastID  = $product->id;

        if($request->get('type')==1) // for ssg product
        {
            foreach ($request->get('pqty') as $key => $value) 
            {
                if($value!=null)
                {
                   DB::table('tbl_bundle_product_details')->insert(
                        [
                            'offerId'          => $request->get('offerTypes'),
                            'slabId'           => $request->get('slabs'),
                            'productId'        => $lastID,                
                            'productType'      => $request->get('type'),      
                            'categoryId'       => $request->get('category'),        
                            'giftName'         => $request->get('pName')[$key],
                            'stockQty'         => $value
                        ]
                    ); 
                }                
            }            
        }
        else
        {
            foreach ($request->get('from_slab1') as $key => $value) 
            {
                if($value!=null)
                {
                    DB::table('tbl_bundle_product_details')->insert(
                        [
                            'offerId'          => $request->get('offerTypes'),
                            'slabId'           => $request->get('slabs'),
                            'productId'        => $lastID,
                            'productType'      => $request->get('type'),                
                            'giftName'         => $value,
                            'stockQty'         => $request->get('to_slab1')[$key]
                        ]
                    );
                }
            }
        }

         return Redirect::to('/offers/bundle-product-add')->with('success', 'Successfully Added Bundle Offer Product.');
        //return Redirect::to('/offers/bundle-offer-pro-edit/'.$lastID)->with('success', 'Successfully Added Bundle Offer Product. Add More');

    }



     public function ssg_bundle_offer_pro_edit($id)
    {

        $selectedMenu       = 'Master Offer';                  // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer Product';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer Product Update';        // Page Slug Title

        $resultBundleOffer  = DB::table('tbl_bundle_offer')                            
                            ->where('iStatus','1')
                            ->where('iOfferType','3')
                            ->orderBy('iId','DESC')
                            ->get();

        $resultBundleOfferEdit = $resultRoutes = DB::table('tbl_bundle_products')
                            ->select('tbl_bundle_products.*','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_offer.dBeginDate','tbl_bundle_offer.dEndDate','tbl_bundle_offer.iOfferType')
                            ->join('tbl_bundle_offer','tbl_bundle_products.offerId','=','tbl_bundle_offer.iId')                            
                            ->where('tbl_bundle_products.id', $id)                            
                            ->first();

        $resultBundleOfferSlab  = DB::table('tbl_bundle_slab')
                            ->select('iId','iOfferId','iMinRange','iMaxRange','iStatus')                            
                            ->where('iOfferId',$resultBundleOfferEdit->offerId)
                            ->get();

        //dd($resultBundleOfferSlab);

        $resultBundleOfferSlabOrProduct = DB::table('tbl_bundle_product_details')
                            ->select('productId','categoryId','giftName','stockQty','offerId')                               
                            ->where('productId', $id)
                            ->get();

        $resultBundleOfferSlabSelected = DB::table('tbl_bundle_product_details')
                            ->select('productId','slabId')                               
                            ->where('productId', $id)
                            ->groupBy('slabId')
                            ->first();

        $resultSSGProductCat  = DB::table('tbl_product_category')
                             ->orderBy('id','DESC')
                             ->get();
        

        $resultSSGProductCatSelected = DB::table('tbl_bundle_product_details')
                            ->select('productId','categoryId','slabId')                               
                            ->where('productId', $id)
                            ->groupBy('categoryId')
                            ->first();

        if(sizeof($resultSSGProductCatSelected)>0)
        {
            $resultCategory  = DB::table('tbl_product_category')
                             ->where('id', $resultSSGProductCatSelected->categoryId)
                             ->first();

            $resultSSGProduct = DB::table('tbl_product')
                            ->select('tbl_product.id','tbl_product.name','tbl_product.category_id','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.slabId')
                            ->leftJoin('tbl_bundle_product_details','tbl_product.id','=','tbl_bundle_product_details.giftName')                               
                            ->where('tbl_product.category_id', $resultSSGProductCatSelected->categoryId)
                            ->get();  
        }
        else
        {
            $resultCategory   = ''; 
            $resultSSGProduct = '';
        }

        return view('sales.offer.masterBundleOfferProEdit', compact('selectedMenu','selectedSubMenu','pageTitle','resultBundleOffer','resultBundleOfferEdit','resultBundleOfferSlab','resultBundleOfferSlabOrProduct','resultBundleOfferSlabSelected','resultSSGProductCat','resultSSGProductCatSelected','resultSSGProduct','resultCategory'));
    }


    public function ssg_bundle_offer_pro_update(Request $request)
    {

        //dd($request->get('to_slab1'));

        $qtyStock = 0;
        if($request->get('type')==1) // for ssg product
        {
            foreach ($request->get('pqty') as $key => $value) 
            {
                if($value!=null)
                {
                    $qtyStock = $qtyStock + $value;
                }
            }            
        }
        else
        {
            foreach ($request->get('to_slab1') as $key => $value) 
            {
                $qtyStock = $qtyStock + $value;
            }
        }

        DB::table('tbl_bundle_products')->insert(
            [
                'offerId'            => $request->get('offerTypes'),
                'global_company_id'  => Auth::user()->global_company_id,
                'stockQty'           => $qtyStock,
                'productType'        => $request->get('type'),       
                'updated_at'         => date('Y-m-d H:i:s'),
                'createUser'         => Auth::user()->id
            ]
        );

        $product = DB::table('tbl_bundle_products')
                    ->where('global_company_id',Auth::user()->global_company_id)
                    ->orderBy('id','DESC')->first();
        $lastID  = $product->id;

        //$lastID  = $request->get('offerProId');

        //$deletePro= DB::table('tbl_bundle_product_details')->where('productId', $lastID)->delete();

        if($request->get('type')==1) // for ssg product
        {
            foreach ($request->get('pqty') as $key => $value) 
            {
                if($value!=null)
                {
                   DB::table('tbl_bundle_product_details')->insert(
                        [
                            'offerId'          => $request->get('offerTypes'),
                            'slabId'           => $request->get('slabs'),
                            'productId'        => $lastID,
                            'productType'      => $request->get('type'),                
                            'categoryId'       => $request->get('category'),        
                            'giftName'         => $request->get('pName')[$key],
                            'stockQty'         => $value
                        ]
                    ); 
                }                
            }            
        }
        else
        {
            foreach ($request->get('from_slab1') as $key => $value) 
            {
                if($value!=null)
                {

                    DB::table('tbl_bundle_product_details')->insert(
                        [
                            'offerId'          => $request->get('offerTypes'),
                            'slabId'           => $request->get('slabs'),
                            'productId'        => $lastID,
                            'productType'      => $request->get('type'),                
                            'giftName'         => $value,
                            'stockQty'         => $request->get('to_slab1')[$key]
                        ]
                    );
                }
            }
        }

        return Redirect::to('/offers/bundle-offer-pro-edit/'.$lastID)->with('success', 'Successfully Added Bundle Offer Product. Add More');

        //return Redirect::to('/offers/bundle-product')->with('success', 'Successfully Updated Bundle Offer Product.');

    }

    public function ssg_bundle_product_delete(Request $request)
    {

       DB::table('tbl_bundle_products')->where('id', $request->get('offerid'))->delete();
       DB::table('tbl_bundle_product_details')->where('productId', $request->get('offerid'))->delete();

       return 0;
    }

    public function ssg_bundle_offer_pro_edit_new($id)
    {
        $selectedMenu       = 'Master Offer';                  // Required Variable Menu
        $selectedSubMenu    = 'Bundle Offer Product';         // Required Variable Sub Menu
        $pageTitle          = 'Bundle Offer Product Update'; // Page Slug Title

        $resultBundleOffer  = DB::table('tbl_bundle_offer')                            
                            ->where('iStatus','1')
                            ->where('iOfferType','3')
                            ->orderBy('iId','DESC')
                            ->get();

        //dd($id);

        $resultBundleOfferEdit = DB::table('tbl_bundle_product_details')
                            ->where('id', $id)
                            ->first();
        //dd($resultBundleOfferEdit);

        $resultBundleOfferSlab  = DB::table('tbl_bundle_slab')
                            ->select('iId','iOfferId','iMinRange','iMaxRange','iStatus')
                            ->where('iOfferId',$resultBundleOfferEdit->offerId)
                            ->get();


        $resultSSGProductCat  = DB::table('tbl_product_category')
                              ->orderBy('id','DESC')
                              ->get();

        // $resultBundleOfferEdit = $resultRoutes = DB::table('tbl_bundle_products')
        //                     ->select('tbl_bundle_products.*','tbl_bundle_offer.iId','tbl_bundle_offer.vOfferName','tbl_bundle_offer.dBeginDate','tbl_bundle_offer.dEndDate','tbl_bundle_offer.iOfferType')
        //                     ->join('tbl_bundle_offer','tbl_bundle_products.offerId','=','tbl_bundle_offer.iId')                            
        //                     ->where('tbl_bundle_products.id', $id)
        //                     ->first();

        // $resultBundleOfferSlab  = DB::table('tbl_bundle_slab')
        //                     ->select('iId','iOfferId','iMinRange','iMaxRange','iStatus')                            
        //                     ->where('iOfferId',$resultBundleOfferEdit->offerId)
        //                     ->get();

        // $resultBundleOfferSlabOrProduct = DB::table('tbl_bundle_product_details')
        //                     ->select('productId','categoryId','giftName','stockQty','offerId')                               
        //                     ->where('productId', $id)
        //                     ->get();

        // $resultBundleOfferSlabSelected = DB::table('tbl_bundle_product_details')
        //                     ->select('productId','slabId')                               
        //                     ->where('productId', $id)
        //                     ->groupBy('slabId')
        //                     ->first();

        // $resultSSGProductCat  = DB::table('tbl_product_category')
        //                      ->orderBy('id','DESC')
        //                      ->get();
        

        // $resultSSGProductCatSelected = DB::table('tbl_bundle_product_details')
        //                     ->select('productId','categoryId','slabId')                               
        //                     ->where('productId', $id)
        //                     ->groupBy('categoryId')
        //                     ->first();

        // if(sizeof($resultSSGProductCatSelected)>0)
        // {
        //     $resultCategory  = DB::table('tbl_product_category')
        //                      ->where('id', $resultSSGProductCatSelected->categoryId)
        //                      ->first();

        $resultSSGProduct = DB::table('tbl_product')
                        ->select('tbl_product.id','tbl_product.name','tbl_product.category_id','tbl_bundle_product_details.giftName','tbl_bundle_product_details.stockQty','tbl_bundle_product_details.slabId')
                        ->leftJoin('tbl_bundle_product_details','tbl_product.id','=','tbl_bundle_product_details.giftName')                               
                        ->where('tbl_product.category_id', $resultBundleOfferEdit->categoryId)
                        ->get();  
        // }
        // else
        // {
        //     $resultCategory   = ''; 
        //     $resultSSGProduct = '';
        // }

        return view('sales/offer/masterBundleOfferProEditNew', compact('selectedMenu','selectedSubMenu','pageTitle','resultBundleOffer','resultBundleOfferEdit','resultBundleOfferSlab','resultSSGProductCat','resultSSGProduct'));
    }

    public function ssg_bundle_offer_category_wise_pro_new(Request $request)
    {
        //dd($request->all());

        $resultSSGProduct  = DB::table('tbl_product')
                             ->where('category_id', $request->get('categoryid'))
                             ->orderBy('id','DESC')
                             ->get();

        $resultCategory  = DB::table('tbl_product_category')
                             ->where('id', $request->get('categoryid'))
                             ->first();

        $serial       = 20;        

        return view('sales/offer/allReplaceValue', compact('serial','resultCategory','resultSSGProduct'));
    }

    public function ssg_bundle_offer_pro_update_new(Request $request)
    {

        //dd($request->all());

        $qtyStock = 0;
        
        DB::table('tbl_bundle_products')->insert(
            [
                'offerId'            => $request->get('offerTypes'),
                'global_company_id'  => Auth::user()->global_company_id,
                'stockQty'           => $qtyStock,
                'productType'        => $request->get('type'),       
                'updated_at'         => date('Y-m-d H:i:s'),
                'createUser'         => Auth::user()->id
            ]
        );

        // $product = DB::table('tbl_bundle_products')
        //             ->where('global_company_id',Auth::user()->global_company_id)
        //             ->orderBy('id','DESC')->first();
        // $lastID  = $product->id;

        if($request->get('type')==1) // for ssg product
        {            
            DB::table('tbl_bundle_product_details')->where('id',$request->get('offerProId'))->update(
                [
                    'offerId'          => $request->get('offerTypes'),
                    'slabId'           => $request->get('slabs'),
                    'productId'        => $request->get('proID'),
                    'productType'      => $request->get('type'),                
                    'categoryId'       => $request->get('category'),        
                    'giftName'         => $request->get('products'),
                    'stockQty'         => $request->get('proQty')
                ]
            );   
        }
        else
        {           
            DB::table('tbl_bundle_product_details')->where('id',$request->get('offerProId'))->update(
                [
                    'offerId'          => $request->get('offerTypes'),
                    'slabId'           => $request->get('slabs'),
                    'productId'        => $request->get('proID'),
                    'productType'      => $request->get('type'),                
                    'giftName'         => $request->get('giftName'), 
                    'stockQty'         => $request->get('giftQty')
                ]
            );
        }

        return Redirect::to('/offers/bundle-product')->with('success', 'Successfully Update Bundle Offer Product.');

    }

    public function ssg_bundle_items_delete($id)
    {
       //dd($id);
       DB::table('tbl_bundle_product_details')->where('id', $id)->delete();
       
       return Redirect::to('/offers/bundle-product')->with('success','Successfully item delete.');
    }
}
