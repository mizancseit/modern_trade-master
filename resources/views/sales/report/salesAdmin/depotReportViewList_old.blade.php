<div id="showHiddenDiv">
    <div class="card">
        <div class="header">
            <h5>
                About {{ sizeof($resultDepot) }} results 
            </h5>
        </div>
        <div class="body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover js-basic-example dataTable">
                    <thead>
                        <tr style="font-size: 11px; font-weight: normal;">
                            <th>SL</th>
                            <th>Depot</th>
                            {{-- <th>Code</th> --}}
                            {{-- <th>Business Type</th> --}}
                            <th>Total sales {{ date("M'y", strtotime("-1 month")) }}</th>
                            <th>Target for {{ date("M'y") }}</th>
                            <th>As of Total Sales</th>
                            <th>Target Vs Sales</th>
                            <th>Total Collection</th>
                            <th> Last Day Sales </th>
                            <th> Last Day Collection </th>
                            <th>Current Total Stock Value</th>
                            <th>Cash in Hand</th>
                            <th>Advance Petty Cash</th>
                            <th>Current Market Credit</th>
                            {{-- <th> Total unsettled claim </th> --}}
                            <th>Total Current Asset</th>
                            <th>Credit Limit</th>
                            {{-- <th>October Closing Credit</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @if(sizeof($resultDepot)>0)
                        @php
                        $serial        = 1;
                        $startDate     = date('Y-m'.'-01');
                        $endDate       = date('Y-m'.'-31');
                        $yesterday     = date('Y-m-d', strtotime('-1 day'));
                        $currentYear   = date('Y');

                        $searchMonth   = date('m',strtotime($todate)); //current month
                        $currentMothStart = date('Y-'.$searchMonth.'-01');
                        $currentMothEnd = date('Y-'.$searchMonth.'-31');


                        //previous month

                        $previousMonth      = date("m", strtotime("-1 month")); //current month + 1 month add
                        $previousMonthStart = date('Y-'.$previousMonth.'-01');
                        $previousMonthEnd   = date('Y-'.$previousMonth.'-31');

                        //echo $previousMonth;
                        //echo $previousMonthStart.'--'.$previousMonthEnd;


                        $tosearchDate     = strtotime($todate);
                        $searchNextMonth  = date("m", strtotime("+1 month", $tosearchDate)); //current month + 1 month add
                        $searchNextMonthStart = date('Y-'.$searchNextMonth.'-01');
                        $searchNextMonthEnd   = date('Y-'.$searchNextMonth.'-31');

                        $advancePettyCash = 0;


                        $searchYesterday = date("Y-m-d", strtotime("-1 day", $tosearchDate)); //search day ( - 1 day ) add

                        //echo $searchYesterday;
                        @endphp
                        @foreach($resultDepot as $result)
                        @php
                        $depoTotSalesLastDay = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            ->where('order_type', 'Delivered')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($searchYesterday ,$searchYesterday))
                                            ->sum('total_delivery_value');

                        $depoTotSalesSearchMonth = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            ->where('order_type', 'Delivered')
                                            ->where('point_id', $result->point_id)

                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($previousMonthStart ,$previousMonthEnd))
                                            ->sum('total_delivery_value');

                        $depoTotSalesSearchAsOfTotalSales = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            ->where('order_type', 'Delivered')
                                            ->where('point_id', $result->point_id)

                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMothStart ,$todate))
                                            ->sum('total_delivery_value');

                        $totalCollection = DB::table('depot_collection')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($currentMothStart ,$currentMothEnd))
                                            ->sum('collection_amount');

                        $totalCollectionLastDay = DB::table('depot_requisition')
                                            ->select('depot_requisition.*','depot_req_details.req_id','depot_req_details.delevered_value')

                                            ->leftJoin('depot_req_details','depot_requisition.req_id','=','depot_req_details.req_id')
                                            ->where('depot_requisition.req_status', 'delivered')
                                            ->where('depot_requisition.point_id', $result->point_id)

                                            ->whereBetween(DB::raw("(DATE_FORMAT(depot_requisition.delivered_date,'%Y-%m-%d'))"), array($searchYesterday ,$searchYesterday))
                                            ->sum('depot_req_details.delevered_value');                        

                        $currentMonthTotalSales = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            ->where('order_type', 'Delivered')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMothStart ,$currentMothEnd))
                                            ->sum('total_delivery_value');

                        $currentMonthTotalPayment = DB::table('depot_accounts_payments')
                                            ->where('ack_status', 'CONFIRMED')
                                            ->where('point_id', $result->point_id)

                                            ->whereBetween(DB::raw("(DATE_FORMAT(confirmed_date,'%Y-%m-%d'))"), array($currentMothStart ,$currentMothEnd))
                                            ->sum('trans_amount');

                        //echo $currentMonthTotalPayment.'__'.$currentMonthTotalSales;



                        @endphp
                        <tr style="font-size: 11px; font-weight: normal;">
                            <th> {{ $serial }}</th>
                            <th> 
                                @php
                                    $point = explode(' ',$result->point_name);
                                    echo $result->point_name; //$point[0];
                                @endphp
                            </th>
                            {{-- <th>Code</th> --}}
                            {{-- <th>
                                @php
                                    $point = explode('-',$result->point_name);
                                    if($point[1]=='A')
                                    {
                                        echo 'Accessories';
                                    }
                                    else if($point[1]=='L')
                                    {
                                        echo 'Lightings';
                                    }
                                    else if($point[1]=='F')
                                    {
                                        echo 'Fan';
                                    }                                                
                                @endphp
                            </th> --}}
                            <th> {{ number_format($depoTotSalesSearchMonth,0) }} </th>
                            <th>
                                @php
                                $currentMonthTarget = DB::table('tbl_fo_target')
                                    ->select('tbl_fo_target.*','tbl_user_business_scope.user_id','tbl_user_business_scope.point_id')

                                    ->leftJoin('tbl_user_business_scope','tbl_fo_target.fo_id','=','tbl_user_business_scope.user_id')

                                    ->where('tbl_fo_target.global_company_id', Auth::user()->global_company_id)
                                    ->where('tbl_user_business_scope.point_id', $result->point_id)
                                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_fo_target.start_date,'%Y-%m-%d'))"), array($currentMothStart,$currentMothEnd))
                                    ->sum('tbl_fo_target.total_value');

                                echo $currentMonthTarget;
                                @endphp
                            </th>
                            <th> {{ number_format($depoTotSalesSearchAsOfTotalSales,0) }}</th>
                            <th>
                                @php
                                if($depoTotSalesSearchAsOfTotalSales >0 && $currentMonthTarget>0)
                                {
                                    $figureRoundSales = round($depoTotSalesSearchAsOfTotalSales);
                                    $figureRoundTarget = round($currentMonthTarget);

                                    echo $totalSalesVstotaltarget = round(($figureRoundSales / $figureRoundTarget));

                                    // as per total sales / total target
                                    
                                }
                                else
                                {
                                    echo '0';
                                }
                                    
                                @endphp
                                
                            </th>
                            <th> {{ number_format($totalCollection,0) }}</th>
                            <th> {{ number_format($depoTotSalesLastDay,0) }} </th>
                            <th> {{ number_format($totalCollectionLastDay,0) }}  </th>
                            <th>
                                <?php
                                $resStockSummaryTotal = 0; 
                                if (isset($resStockSummary[0]->totStockVal)) 
                                {   
                                    $resStockSummaryTotal = $resStockSummary[0]->totStockVal;
                                    echo number_format($resStockSummary[0]->totStockVal,0);
                                } 
                                else 
                                {
                                    echo 0;
                                }    
                                ?>
                            </th>
                            <th>
                                @php
                                    $depoOpenCashInHand = DB::select("SELECT opening_cash_in_hand FROM tbl_depot_summary WHERE point_id 
                                            in (SELECT point_id FROM tbl_user_business_scope 
                                            WHERE point_id = '".$result->point_id."')");                                  

                                    $depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection
                                                                WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
                                                                WHERE point_id = '".$result->point_id."')");

                                    $depoTotExpense = DB::select("SELECT SUM(c.trans_amount) tot_expense
                                                        FROM depot_cash_book c JOIN depot_accounts_head h ON c.perticular_head_id = h.accounts_head_id
                                                        WHERE h.accounts_head_type = 'expense' 
                                                        AND point_id in (SELECT point_id FROM tbl_user_business_scope 
                                                            WHERE point_id = '".$result->point_id."')");

                                    $retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer WHERE point_id 
                                        in (SELECT point_id FROM tbl_user_business_scope 
                                        WHERE point_id = '".$result->point_id."')");

                                    $resStockSummary = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.depo * ds.stock_qty) as totStockVal 
                                        FROM depot_stock  ds JOIN tbl_product p ON ds.product_id = p.id
                                        WHERE ds.point_id = '".$result->point_id."'");

                                
                                    $depoMarketCredit = 0;
                                    if(sizeof($retOpenTot)>0)
                                    {
                                        $totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
                                    } else {
                                        $totRetOpenBal = 0;
                                    }

                                    $depoTotSales = DB::select("SELECT SUM(total_delivery_value) tot_sales FROM  tbl_order 
                                        WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
                                        WHERE point_id = '".$result->point_id."')  
                                        AND order_type = 'Delivered'");
                                

                                    if(sizeof($depoTotSales)>0)
                                    {
                                        $tot_sales = $depoTotSales[0]->tot_sales;
                                    } else {
                                        $tot_sales = 0;
                                    }

                                    $depoCashInHand = 0;
                                    if(sizeof($depoOpenCashInHand)>0)
                                    {
                                        $opening_cash_in_hand = $depoOpenCashInHand[0]->opening_cash_in_hand;
                                    } else {
                                        $opening_cash_in_hand = 0;
                                    }
                                    
                                    if(sizeof($depoTotCollection)>0)
                                    {
                                        $tot_collection = $depoTotCollection[0]->tot_collection;
                                    } else {
                                        $tot_collection = 0;
                                    }
                                    
                                    if(sizeof($depoTotExpense)>0)
                                    {
                                        $tot_expense = $depoTotExpense[0]->tot_expense;
                                    } else {
                                        $tot_expense = 0;
                                    }

                                    echo $depoCashInHand = number_format(($opening_cash_in_hand + $tot_collection) - $tot_expense,0);
                                @endphp
                            </th>
                            <th> {{ 0 }}</th>
                            <th>
                                {{ $depoMarketCredit = number_format(($totRetOpenBal + $tot_sales) - $tot_collection,0) }}
                            </th>

                            <th>
                                
                                <?php                                                              

                                $currentMarketCredit = round(($totRetOpenBal + $tot_sales) - $tot_collection);
                                $currentCashInHand = round(($opening_cash_in_hand + $tot_collection) - $tot_expense,0);

                                $totalAsset = (round($resStockSummaryTotal) + round($currentMarketCredit) + round($currentCashInHand));

                                echo number_format($totalAsset,0);

                                ?>

                            </th>
                            
                            <th>
                                @php
                                if($result->point_id==48) // Cox-A
                                {
                                    echo '2,000,000';
                                }
                                elseif($result->point_id==23) // Cox-L
                                {
                                    echo '2,500,000';
                                }
                                elseif($result->point_id==523) // Dhanmondi-A
                                {
                                    echo '2,200,000';
                                }
                                elseif($result->point_id==519) // Dhanmondi-L
                                {
                                    echo '2,200,000';
                                }
                                elseif($result->point_id==511) // Gaibandha-A
                                {
                                    echo '0';
                                }
                                elseif($result->point_id==485) // Gaibandha-L
                                {
                                    echo '4,000,000';
                                }
                                elseif($result->point_id==506) // Jaldhaka-A
                                {
                                    echo '1,500,000';
                                }
                                elseif($result->point_id==494) // Jaldhaka-L
                                {
                                    echo '2,200,000';
                                }
                                elseif($result->point_id==28) // Kazirhat-A
                                {
                                    echo '2,500,000 ';
                                }
                                elseif($result->point_id==2) // Kazirhat-L
                                {
                                    echo '1,500,000';
                                }
                                elseif($result->point_id==501) // Kurigram-A
                                {
                                    echo '3,000,000';
                                }
                                elseif($result->point_id==480) // Kurigram-L
                                {
                                    echo '3,000,000';
                                }
                                elseif($result->point_id==522) // Nandan Kanan-A
                                {
                                    echo '5,000,000';
                                }
                                elseif($result->point_id==5) // Nandan Kanan-L
                                {
                                    echo '2,000,000';
                                }
                                elseif($result->point_id==462) // Naogaon-A
                                {
                                    echo '3,500,000';
                                }
                                elseif($result->point_id==267) // Nawabpur-A
                                {
                                    echo ' 25,000,000';
                                }
                                elseif($result->point_id==266) // Nawabpur-L
                                {
                                    echo ' 11,000,000';
                                }
                                elseif($result->point_id==500) // Rampura-A
                                {
                                    echo '5,200,000';
                                }
                                elseif($result->point_id==252) // Rampura-L
                                {
                                    echo '3,500,000';
                                }
                                elseif($result->point_id==84) // Sylhet-A
                                {
                                    echo '6,000,000';
                                }
                                elseif($result->point_id==56) // Sylhet-L
                                {
                                    echo ' 6,000,000';
                                }
                                else
                                {
                                    echo '0';
                                }                                
                                @endphp
                            </th>
                            
                        </tr>
                        @php
                        $serial ++;
                        @endphp
                        @endforeach
                        @endif
                    </tbody>                                
                    
                </table>
            </div>
        </div>
    </div>
</div>