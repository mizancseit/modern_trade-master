<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

class UtilityController extends Controller
{
	/**
	*
	* Created by Md. Masud Rana
	* Date : 10/01/2018
	*
	**/

	public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

    public function ssg_utility()
    {

        $selectedMenu       = 'Utility';             // Required Variable Menu
        $selectedSubMenu    = '';                   // Required Variable Sub Menu
        $pageTitle          = 'Utility Manage';    // Page Slug Title

        $resultUtility = DB::table('ims_suggestion')
                        ->select('ims_suggestion.*','ims_visit_reason.reason','ims_visit_reason.id AS rid')
                        ->leftjoin('ims_visit_reason','ims_visit_reason.id', '=', 'ims_suggestion.reasonid')
                        ->where('ims_suggestion.user', Auth::user()->id)
                        ->orderBy('ims_suggestion.id', 'DESC')
                        ->get();        

        return view('sales.utility.utilityManage', compact('selectedMenu','selectedSubMenu','pageTitle','resultUtility'));
    }


    public function ssg_utility_type(Request $request)
    {
        $type = '';
        if($request->get('reason')=='PROBLEM')
        {
            $type = 2;
        }
        elseif($request->get('reason')=='SERVICE')
        {
            $type = 3;
        }

        $resultReason = DB::table('ims_visit_reason')->where('type', $type)->get();
        $serial       = 2;

        return view('sales.report.distributor.allDropDown', compact('resultReason','serial'));
    }

    public function ssg_utility_add(Request $request)
    {
        DB::table('ims_suggestion')->insert(
            [
                'reasonid'      => $request->get('reason'),
                'type'          => $request->get('type'),
                'remarks'       => $request->get('suggestion'),
                'user'          => Auth::user()->id,
                'date'          => date('Y-m-d'),
                'entrydate'     => date('Y-m-d H:i:s', strtotime('+6 hour'))               
            ]
        );

        return Redirect::back()->with('success', Auth::user()->display_name.'  Successfully Utility ADD.');        
    }
}
