<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class HierarchyController extends Controller
{

	public function create_supervisor(){ 
		
        $selectedMenu    = 'Supervisor';                    // Required Variable for menu
        $selectedSubMenu = 'Supervisor List';           // Required Variable for menu
        $pageTitle       = 'Supervisor List'; // Page Slug Title

        $user_type=DB::table('tbl_user_type')->get();

        $supervisorList  = DB::table('mts_role_hierarchy') 
        ->join('users', 'users.id', '=', 'mts_role_hierarchy.supervisor_id')
        ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'mts_role_hierarchy.supervisor_type')
        ->where('users.is_active', '=', '0')
        ->orderBy('mts_role_hierarchy.hierarchy_id','ASC')                    
        ->get();

        return view('eshop::sales/form/supervisor_list' , compact('selectedMenu','selectedSubMenu','pageTitle','user_type','supervisorList','division','channel'));  


     

	}

	public function get_type_user_list(){

	}


}

