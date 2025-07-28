<?php 

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Sales\ProductsStockUploadModel;

use Hash;
use DB;
use Auth;
use Session;
use Excel;

class StaticController extends Controller
{ 
	public static function getQuery($table){
		return DB::table($table)->get();
	}
	public static function whereRow($table,$field,$value){
		return DB::table($table)->where($field,$value)->first();
	}
	public static function wheresRow($table,$field,$value){
		return DB::table($table)->where($field,$value)->get();
	}
	public static function whereRows($table,$field,$value){
		return DB::table($table)->where($field,$value)->get();
	}
	public static function wheresRows($table,$field,$value){
		return DB::table($table)->where($field,$value)->get();
	} 
}
