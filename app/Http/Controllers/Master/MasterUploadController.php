<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Sales\MasterUploadModel;
use App\Models\Sales\MasterStockUploadModel;
use App\Models\Sales\DistributorStockUploadModel;

use Hash;
use DB;
use Auth;
use Session;
use Excel;

class MasterUploadController extends Controller
{
    // --- Sharif start target file upload -- //


    public function fo_target_list()
    {
        $selectedMenu    = 'Targer file upload';                    // Required Variable for menu
        $selectedSubMenu = 'Targer file upload';           // Required Variable for menu
        $pageTitle       = 'Targer file upload'; // Page Slug Title

        
        $targetList  = DB::table('tbl_fo_target')
        ->select('tbl_fo_target.id AS id','tbl_fo_target.fo_id','tbl_fo_target.employee_id','users.display_name','tbl_product_category.name AS name','tbl_fo_target.cat_id','tbl_fo_target.qty','tbl_fo_target.avg_value','tbl_fo_target.total_value','tbl_fo_target.start_date','tbl_fo_target.end_date') 
        ->join('tbl_product_category', 'tbl_product_category.id', '=', 'tbl_fo_target.cat_id')
		->join('users', 'users.employee_id', '=', 'tbl_fo_target.employee_id')
        ->orderBy('tbl_fo_target.id','DESC')                    
        ->get();

        return view('Master.fo_target_upload' , compact('selectedMenu','selectedSubMenu','pageTitle','targetList'));  

        
    }

    public function targetUpload(Request $request)
    {
        if($request->file('imported-file'))
        {
            $path = $request->file('imported-file')->getRealPath();
            $data = Excel::load($path, function($reader) {})->get();

            /* if(!empty($data) && $data->count())
            {
                $data = $data->toArray();
                for($i=0;$i<count($data);$i++)
                {
                  $dataImported[] = $data[$i];
				}
			} */
			
			if(!empty($data) && $data->count()){

               
                      $data = $data->toArray();
                      for($i=0;$i<count($data);$i++)
                      {
                       $insert[] = ['fo_id' => $data[$i]['fo_id'],'employee_id'=> $data[$i]['employee_id'],'global_company_id' =>Auth::user()->global_company_id, 'cat_id' => $data[$i]['cat_id'], 'cat_name' => $data[$i]['cat_name'], 'qty' => $data[$i]['qty'], 'avg_value' => $data[$i]['avg_value'], 'total_value' => $data[$i]['total_value'], 'start_date' => $data[$i]['start_date'],'end_date' => $data[$i]['end_date'],'created_by' => Auth::user()->id];
					  
					  }
                 
                if(!empty($insert)){
				
                     MasterUploadModel::insert($insert);
					  DB::table('tbl_fo_target')
					  ->where('qty', 0)
					  ->delete();
					  
					  return back()->with('success','Target upload sucessfully.');
                }


            }

          
      }

      return back()->with('error','Please Check your file, Something is wrong there.');

  }

  public function fo_target_edit(Request $request)
  {

        $selectedMenu    = 'Targer file upload';                    // Required Variable for menu
        $selectedSubMenu = 'Targer file upload';           // Required Variable for menu
        $pageTitle       = 'Targer file upload'; // Page Slug Title
        $pcategory=DB::table('tbl_product_category')->get();

        $slID=$request->get('id');
        $targetList  = DB::table('tbl_fo_target')
        ->where('id',$slID)
        ->first();
        return view('Master.fo_target_edit',compact('selectedMenu','selectedSubMenu','pageTitle','targetList','pcategory')); 

    }

    public function fo_target_edit_process(Request $request){

        $target = MasterUploadModel::find($request->get('id'));
        $target->fo_id          = $request->fo_id;
        $target->cat_id         = $request->category;
        $target->qty            = $request->qty;
        $target->avg_value      = $request->avg_value;
        $target->total_value    = $request->avg_value * $request->qty;
        $target->start_date     = $request->start_date;
        $target->end_date       = $request->end_date; 
        $target->updated_by     = Auth::user()->id; 
        $target->save();
        return back()->with('success','Target upload sucessfully.');
       //return redirect('Master/fo_target_upload')->with('success','Target Update sucessfully.');
        
    }


    public function targetDelete(Request $request)
    {
      $target = MasterUploadModel::find($request->get('id'));
      $target->delete();

      //return back()->with('success','Target Delete sucessfully.');
      return redirect('Master/fo_target_upload')->with('success','Target Delete sucessfully.');
  }

    /*public function targetUpload(Request $request)
    {
        //dd($request->all());

        if($request->file('imported-file'))
        {
            //dd('path');
            $path = $request->file('imported-file')->getRealPath();


            $data = Excel::load($path, function($reader) {})->get();


            if(!empty($data) && $data->count()){


                foreach ($data->toArray() as $key => $value) {

                    //dd($value);
                    if(!empty($value)){


                        foreach ($value as $v) { 

                            $insert[] = ['fo_id' => $v['fo_id'], 'cat_id' => $v['cat_id'], 'cat_name' => $v['cat_name'], 'qty' => $v['qty'], 'avg_value' => $v['avg_value'], 'total_value' => $v['total_value'], 'start_date' => $v['start_date'],'end_date' => $v['end_date']];
                        }
                    }
                }

                 //print_r($insert);
                if(!empty($insert)){
                    MasterUploadModel::insert($insert);
                    return back()->with('success','Insert Record successfully.');
                     //return redirect('Master.fo_target_upload')->with('success','Target upload sucessfully.');
                }


            }


        }
        return back()->with('error','Please Check your file, Something is wrong there.');
   
    }*/
	
	
	 public function stockInventoryUpload(Request $request)
    {
        //dd($request->all());

        if($request->hasFile('imported-file'))
        {
           $pointID = $request->input('point_id');
           $inOut = $request->input('inOut');
           $path = $request->file('imported-file')->getRealPath();


            $data = Excel::load($path, function($reader) {})->get();


            if(!empty($data) && $data->count()){

               
				$data = $data->toArray();
				for($i=0;$i<count($data);$i++)
				{
				   $insert[] = ['point_id' => $pointID,'depot_in_charge' =>Auth::user()->id, 
				   
				   'cat_id' => $data[$i]['category_id'], 'product_id' => $data[$i]['id'], 
				   'product_qty' => $data[$i]['stock_qty'], 'inventory_date' => date('Y-m-d'), //$data[$i]['inventory_date'], 
				
					'inventory_type' => $inOut, 
				   'global_company_id' =>Auth::user()->global_company_id, 
				   'created_by' => Auth::user()->id];
			   
			   
							/* update stock begin */
			   
						$chkStcok = DB::select("SELECT * 
									FROM depot_stock 
									WHERE	point_id = '".$pointID."'
									AND 	product_id = '".$data[$i]['id']."'
									AND 	cat_id = '".$data[$i]['category_id']."'
								");
						
						
							//echo '<pre/>'; print_r($chkStcok); exit;				
				
						if(sizeof($chkStcok) > 0)
						{
							
							$DepotReqDetUpd = DB::update("UPDATE depot_stock SET stock_qty = stock_qty + $data[$i]['stock_qty'] 
															WHERE point_id = ? AND product_id = ? AND cat_id = ?",
															[$pointID, $data[$i]['id'], $data[$i]['category_id']]
														);	


						} else {
							
							DB::table('depot_stock')->insert(
								[
									'point_id'          	=> $pointID,
									'product_id'          	=> $data[$i]['id'],
									'cat_id'          		=> $data[$i]['category_id'],
									'stock_qty'      		=> $data[$i]['stock_qty'],
									'global_company_id'     => Auth::user()->global_company_id,
									'created_by'         	=> Auth::user()->id,
								  
								]
							); 

						}	
			   
							/* update stock end */
				}
                 
                if(!empty($insert)){
                    MasterStockUploadModel::insert($insert);
                     return back()->with('success','Inventory upload sucessfully.');
                }


            }


        }
        return back()->with('error','Please Check your file, Something is wrong there.');
   
    }


    ///////////////////////// MD. MASUD RANA | DISTRIBUTOR PHYSICAL INVENTORY UPLOAD ////////////////////////////

    public function stockPhysicalInventoryUpload(Request $request)
    {
    //dd($request->all());

      if($request->hasFile('imported-file'))
      {
        $pointID  = $request->input('point_id');
        $inOut    = $request->input('inOut');
        $path     = $request->file('imported-file')->getRealPath();

        $data     = Excel::load($path, function($reader) {})->get();

        if(!empty($data) && $data->count())
        {

          $data = $data->toArray();
          for($i=0;$i<count($data);$i++)
          {
            $insert[] = ['point_id' => $pointID,'distributor_in_charge' =>Auth::user()->id, 

            'cat_id' => $data[$i]['category_id'], 'product_id' => $data[$i]['id'], 
            'product_qty' => $data[$i]['stock_qty'], 'inventory_date' => date('Y-m-d'), //$data[$i]['inventory_date'], 

            'inventory_type'    => $inOut, 
            'global_company_id' =>Auth::user()->global_company_id, 
            'created_by'        => Auth::user()->id];

            /* update stock begin */

            $chkStcok = DB::select("SELECT * 
              FROM distributor_stock 
              WHERE distributor_id = '".Auth::user()->id."'
              AND point_id = '".$pointID."'
              AND product_id = '".$data[$i]['id']."'
              AND cat_id = '".$data[$i]['category_id']."'
              ");


            //echo '<pre/>'; print_r($chkStcok); exit;        

            if(sizeof($chkStcok) > 0)
            {
              $TotalQty = $chkStcok[0]->stock_qty + $data[$i]['stock_qty'];

              DB::table('distributor_stock')->where('point_id',$pointID)->where('product_id',$data[$i]['id'])->where('cat_id',$data[$i]['category_id'])->update(
                [
                  'stock_qty' => $TotalQty
                ]
              );
            } 
            else 
            {
              DB::table('distributor_stock')->insert(
                [
                  'point_id'            => $pointID,
                  'product_id'          => $data[$i]['id'],
                  'cat_id'              => $data[$i]['category_id'],
                  'stock_qty'           => $data[$i]['stock_qty'],
                  'global_company_id'   => Auth::user()->global_company_id,
                  'created_by'          => Auth::user()->id,
                  'distributor_id'      => Auth::user()->id
                ]
              );
            } 

            /* update stock end */
          }

        if(!empty($insert)){
          DistributorStockUploadModel::insert($insert);
          return back()->with('success','Physical Inventory Upload Sucessfully.');
        }
      }
    }
    return back()->with('error','Please Check your file, something is wrong there.');
}

}
