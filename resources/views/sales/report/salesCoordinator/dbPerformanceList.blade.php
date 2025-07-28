<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($userslist) }} results 
        </h5>
    </div>
    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>DATE</th>
                                        <th>CHANNEL</th>
                                        <th>DIV</th>
                                        <th>POINT</th>
                                        <th>NAME</th>
                                        <th>SAP.ID</th>
                                        <th>O.QTY</th>
                                        <th>O.VALUE</th>
                                        <th>DEL.QTY</th>
                                        <th>DEL.VALUE</th>
                                        <th>Total Collection</th>
                                        <th>Current Stock Value</th>
                                        <th>Current Cash in Hand</th>
                                        <th>Current Market Credit</th>
                                    </tr>
                                </thead>
                                
                                <tbody style="font-size: 13px;">
                                @if(sizeof($userslist) > 0)   
                                    @php
                                    $serial =1;
                                    $totalQty = 0;
                                    $totalValue = 0;
                                    $totalDelQty = 0;  
                                    $totalDelValue = 0;
                                    $totalCollectionValue = 0; 
                                    $totalStockValue = 0;                                    
                                    $totalCash = 0; 
                                    $totalMarketCredit = 0; 

                                    @endphp

                                    @foreach($userslist as $users)
                                    @php

                                    $orders = DB::table('tbl_order')
                                                    ->select('distributor_id',DB::raw('SUM(tbl_order.total_qty) as total_qty'),DB::raw('SUM(tbl_order.total_value) as total_value'))
                                                    ->where('order_type','Confirmed')
                                                    ->where('distributor_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y-%m-%d'))"), array($todate, $todate))
                                                    ->first();

                                    $totalDalivery = DB::table('tbl_order')
                                                    ->select('distributor_id',DB::raw('SUM(total_delivery_qty) as total_delivery_qty'),DB::raw('SUM(total_delivery_value) as total_delivery_value'))
                                                    ->where('order_type','Delivered')
                                                    ->where('distributor_id',$users->id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($todate, $todate))
                                                    ->first();

                                    $totalCollection = DB::table('depot_collection')
                                            ->where('point_id', $users->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($todate ,$todate))
                                            ->sum('collection_amount');

                                    $resStockSummary = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.depo * ds.stock_qty) as totStockVal 
                                        FROM depot_stock  ds JOIN tbl_product p ON ds.product_id = p.id
                                        WHERE ds.point_id = '".$users->point_id."'");
                                   

                                    if(sizeof($orders)>0){

                                        $totalQty  += $orders->total_qty;
                                        $totalValue += $orders->total_value;
                                    } 
                                       
                                    if(sizeof($totalDalivery)>0){
                                                
                                        $totalDelQty  += $totalDalivery->total_delivery_qty;
                                        $totalDelValue += $totalDalivery->total_delivery_value;
                                     } 

                                     $totalCollectionValue +=$totalCollection;
                                     $totalStockValue += $resStockSummary[0]->totStockVal;        
      

                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                         <th>{{ $todate }}</th>
                                        <th>
                                            @if($users->business_type_id==1) {{'LIGHTING'}}
                                            @elseif($users->business_type_id==2) {{'ACCESSORISE'}}
                                            @else {{'FAN'}}
                                            @endif
                                        </th>
                                        <th>{{ $users->div_name }} </th>
                                       
                                        <th>{{ $users->point_name }}</th> 
                                                            
                                        <th>{{ $users->display_name }}</th>
                                        <th>{{ $users->email }}</th> 
                                        <th>@if(sizeof($orders->total_qty)>0){{ $orders->total_qty }} @else {{'0'}} @endif</th>                        
                                        <th>@if(sizeof($orders->total_qty)>0){{ number_format($orders->total_value,2) }} @else {{'0'}} @endif</th>
                                        <th>@if(sizeof($totalDalivery->total_delivery_qty)>0){{ $totalDalivery->total_delivery_qty }} @else {{'0'}} @endif</th>                        
                                        <th>@if(sizeof($totalDalivery->total_delivery_qty)>0){{ number_format($totalDalivery->total_delivery_value,2) }} @else {{ '0' }} @endif</th>
                                        <th> {{ number_format($totalCollection,0) }}</th>
                                        <th>
                                            @if (sizeof($resStockSummary[0]->totStockVal)>0) {{
                                                number_format($resStockSummary[0]->totStockVal,0)
                                            }}
                                            @else {{ '0' }}
                                            @endif
                                        </th>
                                        <th>
                                @php
                                    $depoOpenCashInHand = DB::select("SELECT opening_cash_in_hand FROM tbl_depot_summary WHERE point_id 
                                            in (SELECT point_id FROM tbl_user_business_scope 
                                            WHERE point_id = '".$users->point_id."')");                                  

                                    $depoTotCollection = DB::select("SELECT SUM(collection_amount) tot_collection FROM  depot_collection
                                                                WHERE point_id in (SELECT point_id FROM tbl_user_business_scope 
                                                                WHERE point_id = '".$users->point_id."')");

                                    $depoTotExpense = DB::select("SELECT SUM(c.trans_amount) tot_expense
                                                        FROM depot_cash_book c JOIN depot_accounts_head h ON c.perticular_head_id = h.accounts_head_id
                                                        WHERE h.accounts_head_type = 'expense' 
                                                        AND point_id in (SELECT point_id FROM tbl_user_business_scope 
                                                            WHERE point_id = '".$users->point_id."')");

                                    $retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer WHERE point_id 
                                        in (SELECT point_id FROM tbl_user_business_scope 
                                        WHERE point_id = '".$users->point_id."')");
                                
                                    $depoMarketCredit = 0;
                                    if(sizeof($retOpenTot)>0)
                                    {
                                        $totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
                                    } else {
                                        $totRetOpenBal = 0;
                                    }

                                    $depoTotSales = DB::select("SELECT SUM(retailer_invoice_sales) as tot_sales FROM  retailer_credit_ledger 
                                        WHERE point_id = '".$users->point_id."'");
                                

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

                                   $cashInHand=$opening_cash_in_hand + $tot_collection - $tot_expense;
                                @endphp
                            </th>
                            <th>
                                {{ 
                                    $depoMarketCredit = number_format(($totRetOpenBal + $tot_sales) - $tot_collection,0) }}
                            </th>
                                     
                                    </tr>
                                    @php

                                    $serial++;
                                    $marketCredit = $totRetOpenBal + $tot_sales - $tot_collection;
                                    $totalMarketCredit +=$marketCredit;
                                    $totalCash +=$cashInHand;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="7" style="text-align: right;">Grand Total : </th>
                                        <th>{{ $totalQty }}</th>
                                        <th>{{ number_format($totalValue,2) }}</th>
                                        <th>{{ $totalDelQty }}</th>
                                        <th>{{ number_format($totalDelValue,2) }}</th>
                                        <th>{{ $totalCollectionValue }}</th>
                                        <th>{{ $totalStockValue }}</th>
                                        <th>{{$totalCash}}</th>
                                        <th>{{$totalMarketCredit}}</th>
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="15">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
</div>