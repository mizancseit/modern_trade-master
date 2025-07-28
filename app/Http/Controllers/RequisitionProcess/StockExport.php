<?php

namespace App\Http\Controllers\RequisitionProcess;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Excel;

class StockExport extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 12/06/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
   

    public function stock_export(Request $Request) 
	{

		$catID = $Request->get('cat_id');

        $point=DB::table('tbl_user_business_scope')
		        ->select('point_id','division_id')
		        ->where('tbl_user_business_scope.user_id', Auth::user()->id)
		        ->first();

        $pointID = '';
        if(sizeof($point)>0)
        {
            $pointID = $point->point_id;
        }
	
		$stockResult = DB::select("SELECT p.name as ProductName, ds.stock_qty as Stock, (ds.stock_qty * p.depo) as Value
		FROM distributor_stock ds JOIN tbl_product p ON ds.product_id = p.id 
		WHERE ds.point_id = '".$pointID."' AND ds.cat_id = '".$catID."' ORDER BY p.name ASC");

		$data = array();
		foreach ($stockResult as $items) {
			$data[] = (array)$items;  
		}

		Excel::create('Distributor_Stock', function($excel) use($data) {
			$excel->sheet('ExportFile', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->export('xls');
	}	
}
