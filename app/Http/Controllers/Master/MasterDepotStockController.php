<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Sales\DepotSetupModel;

use DB;
use Auth;
use Session;

class MasterDepotStockController extends Controller
{

 public function __construct()
 {
       $this->middleware('auth'); // for auth check       
   }


   public function depot_setup_list()
   {
        $selectedMenu    = 'Depot';                    // Required Variable for menu
        $selectedSubMenu = 'Depot Setup';           // Required Variable for menu
        $pageTitle       = 'Depot Setup'; // Page Slug Title

        $in_charge=DB::table('users')
        ->where('users.user_type_id', '=', '2')
        ->orderBy('users.display_name','ASC')  
        ->get();
        $division=DB::table('tbl_division')
        ->where('tbl_division.div_status', '=', '0')
        ->orderBy('tbl_division.div_name','ASC')  
        ->get();

        $depotSetup  = DB::table('tbl_depot_setup')
        ->select('tbl_depot_setup.depot_id','tbl_depot_setup.depot_name','tbl_division.div_name','users.display_name','tbl_depot_setup.depot_location','tbl_depot_setup.depot_current_balance','tbl_depot_setup.depot_current_sales','tbl_depot_setup.market_credit','tbl_depot_setup.opening_balance')
        ->leftjoin('users', 'users.id', '=', 'tbl_depot_setup.depot_in_charge')
        ->join('tbl_division', 'tbl_division.div_id', '=', 'tbl_depot_setup.division')
        ->where('tbl_depot_setup.depot_status', '=', 'active')
        ->orderBy('tbl_depot_setup.depot_name','ASC')                    
        ->get();

        return view('sales.depot.depot_list' , compact('selectedMenu','selectedSubMenu','pageTitle','depotSetup','in_charge','division'));  


    }

    public function depot_setup_save(Request $request){

      $depot = new DepotSetupModel();
      $depot->depot_name = $request->depoName;
      $depot->depot_in_charge = $request->in_charge;
      $depot->division = $request->division;
      $depot->depot_location = $request->location;
      $depot->global_company_id = Auth::user()->global_company_id;
      $depot->opening_balance = $request->opening_balance;
      $depot->depot_current_balance = $request->current_balance;
      $depot->depot_current_sales = $request->current_sales;
      $depot->market_credit = $request->market_credit;
      $depot->created_by = Auth::user()->id; 
      $depot->save();

      return redirect('/depot/depot_list')->with('success','Depot add sucessfully.');

  }



  public function depotListEdit(Request $request)
  {

  	 	$selectedMenu    = 'Depot';                   // Required Variable for menu
        $selectedSubMenu = 'Depot Setup';            // Required Variable for menu
        $pageTitle       = 'Depot Setup';           // Page Slug Title

        $in_charge=DB::table('users')
        ->where('users.user_type_id', '=', '2')
        ->orderBy('users.display_name','ASC')  
        ->get();

        $division=DB::table('tbl_division')
        ->where('tbl_division.div_status', '=', '0')
        ->orderBy('tbl_division.div_name','ASC')  
        ->get();

        
        $slID=$request->get('id');
        $depot = DB::table('tbl_depot_setup')
        ->where('depot_id',$slID)
        ->first();

        return view('sales/depot/depot_list_edit',compact('selectedMenu','selectedSubMenu','pageTitle','depot','in_charge','division')); 
    }



    public function depot_list_edit_process(Request $request){

     DB::table('tbl_depot_setup')->where('depot_id',$request->get('id'))->update(
        [
          'depot_name'        => $request->depoName,
          'depot_in_charge'   => $request->in_charge,
          'division'          => $request->division,
          'depot_location'    => $request->location,
          'global_company_id'        => Auth::user()->global_company_id,
          'opening_balance'   => $request->opening_balance,
          'depot_current_balance' => $request->current_balance,
          'depot_current_sales' => $request->current_sales,
          'market_credit'     => $request->market_credit,
          'created_by'        => Auth::user()->id
      ]
    );

     return redirect('/depot/depot_list')->with('success','Depot Update sucessfully.');

 }


 public function deleteDepotList(Request $request)
 {


    DB::table('tbl_depot_setup')->where('depot_id',$request->get('id'))->update(
        [
            'depot_status'          =>'inactive',                   
            'updated_by'            => Auth::user()->id                    
            
        ]
    );


    return redirect('/depot/depot_list')->with('success','Depot Delete sucessfully.');
}



public function ssg_depot()
{

        $selectedMenu    = 'Depot Inventory List';                   // Required Variable for menu
        $selectedSubMenu = 'Inventory List';            // Required Variable for menu
        $pageTitle       = 'Depot Inventory';           // Page Slug Title


        $division=DB::table('tbl_division')
        ->where('tbl_division.div_status', '=', '0')
        ->orderBy('tbl_division.div_name','ASC')  
        ->get();

        $selectDivision=DB::table('tbl_division')
        ->select('div_id')
        ->where('tbl_division.div_status', '=', '0')
        ->orderBy('tbl_division.div_name','ASC')  
        ->first();


        //dd($selectDivision);

        $divisionID = '';
        if(sizeof($selectDivision)>0)
        {
            $divisionID = $selectDivision->div_id;
        }

        $depotResult = DB::table('tbl_point')
        ->select('point_id','point_name','point_division')
        ->where('global_company_id', Auth::user()->global_company_id)                     
        ->where('point_division', $divisionID)
        ->orderBy('point_name','ASC')                    
        ->get();  
        


        return view('sales.depot.depotManage', compact('selectedMenu','selectedSubMenu','pageTitle','division','depotResult'));
    }


    public function ssg_depot_list(Request $request)
    {

       $divisionID = $request->get('div_id');

        $depotResult = DB::table('tbl_point')
        ->select('point_id','point_name','point_division')
        ->join('tbl_division', 'tbl_division.div_id', '=', 'tbl_point.point_division')
       // ->where('global_company_id', Auth::user()->global_company_id)                     
        ->where('point_division', $divisionID)
        ->orderBy('point_name','ASC')                    
        ->get(); 

        return view('sales.depot.depot_div_list', compact('depotResult'));
    }


    public function ssg_stock_process($pointID,$inOut)
    {
		$selectedMenu    = 'Depot Inventory List';       // Required Variable for menu
        $selectedSubMenu = 'Inventory List';            // Required Variable for menu
        $pageTitle       = 'Depot Inventory';           // Page Slug Title


        $resultCategory = DB::table('tbl_product_category')
        ->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
        ->where('status', '0')
        //->where('gid', Auth::user()->business_type_id)
        //->where('global_company_id', Auth::user()->global_company_id)
        ->get();


        return view('sales.depot.category_wise_stock', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory','pointID','inOut'));
    }


    public function ssg_category_products(Request $request)
    {
        $categoryID = $request->get('categories');

        $resultProduct = DB::table('tbl_product')
        ->select('id','category_id','name','ims_stat','status','depo AS price','unit')
        ->where('ims_stat', '0')                       
        ->where('category_id', $categoryID)
        ->orderBy('id', 'ASC')
        ->orderBy('status', 'ASC')
        ->orderBy('name', 'ASC')
        ->get();

        return view('sales.depot.allProductList', compact('resultProduct','categoryID'));
    }


    public function products_add_to_inventory(Request $request)
    {
		
		$pointID = $request->input('point_id');
		$inOut = $request->input('inOut');
		$countRows = count($request->get('qty')); 

		$resultPoint = DB::select(" SELECT * FROM tbl_point WHERE point_id = '".$pointID."' ");
		
		
			for($m=0;$m<$countRows;$m++)
			{
				if($request->get('qty')[$m]!='')
				{
					$totalPrice = $request->get('qty')[$m] * $request->get('price')[$m];

					DB::table('depot_inventory')->insert(
						[
							'point_id'           => $pointID,
							'depot_in_charge'    => Auth::user()->id,
							'cat_id'             => $request->get('category_id')[$m],
							'product_id'         => $request->get('produuct_id')[$m],
							'product_qty'        => $request->get('qty')[$m],
							'product_value'      => $totalPrice,
							'inventory_date'     => date('y-m-d'),
							'inventory_type'     => $inOut,
							'transaction_type'   => 'regular',
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id
						]
					); 
				

					$stockProduct = DB::table('depot_stock')
									->select('point_id','cat_id','product_id','stock_qty')
									->where('point_id', $pointID)
									->where('cat_id', $request->get('category_id')[$m])
									->where('product_id', $request->get('produuct_id')[$m])
									->first();


					if(sizeof($stockProduct)>0)
					{
						//if($stockProduct->stock_qty>0) // May-19 for negative trabsaction
						//{
							if($inOut==1)
							{
								$totalQty = ($request->get('qty')[$m]) + $stockProduct->stock_qty;
							}else
							{
								$totalQty = $stockProduct->stock_qty - $request->get('qty')[$m];
							}
						
						/*
						}
						else
						{
							$totalQty = 0;
						} */

						
						
						DB::table('depot_stock')
						->where('point_id',$pointID)
						->where('cat_id',$request->get('category_id')[$m])
						->where('product_id',$request->get('produuct_id')[$m])
						->update(
						[
							'point_id'           => $pointID,
							'cat_id'             => $request->get('category_id')[$m],
							'product_id'         => $request->get('produuct_id')[$m],
							'stock_qty'          => $totalQty,
							'global_company_id'  => Auth::user()->global_company_id,
							'created_by'         => Auth::user()->id                   
							
						]
						);

					}else{

						DB::table('depot_stock')->insert(
							[
								'point_id'           => $pointID,
								'cat_id'             => $request->get('category_id')[$m],
								'product_id'         => $request->get('produuct_id')[$m],
								'stock_qty'          => $request->get('qty')[$m],
								'global_company_id'  => Auth::user()->global_company_id,
								'created_by'         => Auth::user()->id
							]
						); 

					}
				}

			}
		
		

		return back()->with('success','Stock upload sucessfully.');

	}


  public function ssg_depot_distributor()
    {

        $selectedMenu    = 'Depot';                   // Required Variable for menu
        $selectedSubMenu = 'Inventory';            // Required Variable for menu
        $pageTitle       = 'Depot Inventory';           // Page Slug Title



        $division_point=DB::table('tbl_user_business_scope')
        ->select('point_id','division_id')
        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
        ->first();

        $pointID = '';
        if(sizeof($division_point)>0)
        {
            $pointID = $division_point->point_id;
        }

        $depotResult = DB::table('tbl_point')
        ->select('point_id','point_name','point_division')                    
        ->where('point_id', $pointID)
        ->orderBy('point_name','ASC')                    
        ->get();            

        return view('sales.depot.depotDistributorManage', compact('selectedMenu','selectedSubMenu','pageTitle','division_point','depotResult'));
    }



    public function ssg_depot_stock_list()
    {

        $selectedMenu    = 'Depot';                   // Required Variable for menu
        $selectedSubMenu = 'Stock List';            // Required Variable for menu
        $pageTitle       = 'Stock List';           // Page Slug Title


         $resultCategory = DB::table('tbl_product_category')
        ->select('id','gid','status','LAF','name','g_name','g_code','avg_price','global_company_id')
        ->where('gid', Auth::user()->business_type_id)
        ->where('status', '0')
        ->where('global_company_id', Auth::user()->global_company_id)
        ->get();

        return view('sales.depot.depotStockList', compact('selectedMenu','selectedSubMenu','pageTitle','resultCategory'));
    }


    public function ssg_stock_products(Request $request)
    {
        $catID = $request->get('categories');

        $point=DB::table('tbl_user_business_scope')
        ->select('point_id','division_id')
        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
        ->first();

        $pointID = '';
        if(sizeof($point)>0)
        {
            $pointID = $point->point_id;
        }

        /*
        $stockResult = DB::table('depot_stock')
        ->select('depot_stock.point_id','tbl_product.name','depot_stock.stock_qty', 
        'depot_stock.stock_qty * tbl_product.depo stock_value')
        ->join('tbl_product', 'tbl_product.id', '=', 'depot_stock.product_id')
        ->where('point_id', $pointID)                 
        ->where('depot_stock.cat_id', $catID)
        ->orderBy('tbl_product.name','ASC')                    
        ->get(); */
        
        if($catID!='all')
        {
            $stockResult = DB::select("SELECT ds.point_id, p.name, ds.stock_qty,p.depo, (ds.stock_qty * p.depo) as stock_value
            FROM depot_stock ds JOIN tbl_product p ON ds.product_id = p.id 
            WHERE ds.point_id = '".$pointID."' AND ds.cat_id = '".$catID."' ORDER BY p.name ASC");
            
        } else {
            
            /*
            $stockResult = DB::select("SELECT ds.point_id, p.name, ds.stock_qty, (ds.stock_qty * p.depo) as stock_value
            FROM depot_stock ds JOIN tbl_product p ON ds.product_id = p.id 
            WHERE ds.point_id = '".$pointID."' ORDER BY p.name ASC");
            */
            
            $stockResult = DB::select("SELECT tbl_product.name, tbl_product.depo, sum(if(inventory_type=1,product_qty,0)) as InStock,sum(if(inventory_type=2,product_qty,0)) as OutStock, 
            (sum(if(inventory_type=1,product_qty,0))- sum(if(inventory_type=2,product_qty,0))) as stock_qty,
            sum(if(inventory_type=1,product_value,0)) as provInval,  sum(if(inventory_type=2,product_value,0)) as proOutVal,
            (sum(if(inventory_type=1,product_value,0))- sum(if(inventory_type=2,product_value,0)))  as pro_val
                        FROM depot_inventory JOIN tbl_product  ON depot_inventory.product_id = tbl_product.id
            WHERE point_id in ('".$pointID."') GROUP BY depot_inventory.product_id"); 
            
        }
        
        
        //dd($stockResult);

        return view('sales.depot.allStockList', compact('stockResult','catID'));
    }

}
