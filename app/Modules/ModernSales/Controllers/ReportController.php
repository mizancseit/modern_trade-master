<?php

namespace App\Modules\ModernSales\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;

use DB;
use Auth;
use Session;

class ReportController extends Controller
{
    /**
     * MST SALES REPORT
     */
    public function mtsSalesReport()
    {
        //EXECUTIVE LIST
        $executiveList = DB::table('mts_role_hierarchy')
            ->join('users', 'users.id', '=', 'mts_role_hierarchy.executive_id')
            ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
            ->groupBy('mts_role_hierarchy.executive_id')
            ->get();

        //OFFICER LIST
        $officerList = DB::table('mts_role_hierarchy')
            ->join('users', 'users.id', '=', 'mts_role_hierarchy.officer_id')
            ->where('mts_role_hierarchy.supervisor_id', Auth::user()->id)
            ->groupBy('mts_role_hierarchy.officer_id')
            ->get();

        //CUSTOMER LIST
        $customerList = DB::table('mts_customer_list')->where('status',0)->orderBy('name','ASC')->get();

        //PG LIST
        $pgList = array();

        return view('ModernSales::reports/mst_sales', compact('customerList','officerList','executiveList','pgList'));
    }

    /**
     * OFFICER PERFORMANCE REPORT
     */
    public function mstOfficerPerformanceReport()
    {
        return view('ModernSales::reports/mst_officer_performance');
    }

    /**
     * OPERATIONAL REPORT
     */
    public function mstOperationalReport()
    {
        return view('ModernSales::reports/mst_operational');
    }

    /**
     * CUSTOMER LEDGER REPORT
     */
    public function mstCustomerLedgerReport()
    {
        return view('ModernSales::reports/mst_customer_ledger');
    }

    /**
     * VISIT FREQUENCY REPORT
     */
    public function mstVisitFrequencyReport()
    {
        return view('ModernSales::reports/mst_visit_frequency');
    }
 }
