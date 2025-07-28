<div class="card"  id="printMe">
    <div class="header">
        <h5>
            About {{ sizeof($reports) }} results 
        </h5>
    </div>                  
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
               <thead>
                    <tr>
                        <th>SL</th>
                        <th>Retailer Name</th>
                        <th>Opening Balance</th>
                        <th>Sales (IMS Delivery)</th>
                        <th>Collection</th>
                        <th>Balance (TK)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($reports) > 0)
                        @php
                        $serial = 1;
                        $GrandtotSales = 0;
                        $GrandtotCollection = 0;
                        $GarndOpeningBalance = 0;
                        $GrandtotBalance = 0;
                        $balance = 0;
                        $depoMarketCredit = 0;
                        @endphp

                        @foreach($reports as $reportsData)
                        @php
                        
                        $rowCreditSummaryData = DB::select("SELECT SUM(retailer_invoice_sales) ret_wise_tot_sales, 
                        SUM(retailer_sales_return) ret_wise_sales_return,
                        SUM(retailer_collection) totCollection 
                        FROM  retailer_credit_ledger 
                        WHERE retailer_id = '".$reportsData->retailer_id."'
                        AND point_id = '".$reportsData->point_id."'");


                        
                        if(sizeof($retOpenTot)>0)
                        {
                            $totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
                        } 
                        else 
                        {
                            $totRetOpenBal = '0000';
                        }

                        if(sizeof($rowCreditSummaryData)>0)
                        {
                            $GrandtotSales += $rowCreditSummaryData[0]->ret_wise_tot_sales;

                            if($rowCreditSummaryData[0]->ret_wise_sales_return>0)
                            {
                                $GrandtotSales -= $rowCreditSummaryData[0]->ret_wise_sales_return;
                            }
                        } 

                        if(sizeof($rowCreditSummaryData)>0)
                        {
                            $GrandtotCollection += $rowCreditSummaryData[0]->totCollection;
                        }

                        @endphp
                        <tr>
                            <th style="font-weight: normal;">{{ $serial }}</th>
                            <th style="font-weight: normal;">{{ $reportsData->name }}</th>                            
                            <th style="font-weight: normal; text-align: right;">{{ $reportsData->opening_balance }}</th>
                            <th style="font-weight: normal; text-align: right;">
                                @if($rowCreditSummaryData[0]->ret_wise_tot_sales > 0)
                                    {{$rowCreditSummaryData[0]->ret_wise_tot_sales - $rowCreditSummaryData[0]->ret_wise_sales_return}}
                                @else
                                    {{ 0000.00}}
                                @endif
                            </th>
                           <th style="font-weight: normal; text-align: right;">
                                @if($rowCreditSummaryData[0]->totCollection > 0)
                                    {{$rowCreditSummaryData[0]->totCollection}}
                                @else
                                    {{ 0000.00}}
                                @endif
                            </th>
                            <th style="font-weight: normal; text-align: right;">
                                @php
                                $depoMarketCredit += ($reportsData->opening_balance + ($rowCreditSummaryData[0]->ret_wise_tot_sales - $rowCreditSummaryData[0]->ret_wise_sales_return)) - $rowCreditSummaryData[0]->totCollection
                                @endphp
                                {{ ($reportsData->opening_balance + ($rowCreditSummaryData[0]->ret_wise_tot_sales - $rowCreditSummaryData[0]->ret_wise_sales_return)) - $rowCreditSummaryData[0]->totCollection }}
                            </th>        
                        </tr>
                        @php
                        $serial++;
                        @endphp
                        @endforeach
                        <tr>                            
                            <th colspan="2" style="font-weight: normal; text-align: right;">Grand Total</th>
                            <th style="font-weight: normal; text-align: right;">{{ number_format($totRetOpenBal,0) }}</th>
                            <th style="font-weight: normal; text-align: right;">{{ number_format($GrandtotSales,0) }}</th>
                            <th style="font-weight: normal; text-align: right;">{{ number_format($GrandtotCollection,0) }}</th>
                            <th style="font-weight: normal; text-align: right;">{{ number_format($depoMarketCredit,0) }}</th>
                        </tr>
                    @else
                        <tr>
                            <th colspan="6">No record found.</th>
                        </tr>
                    @endif    
                        
                    </tbody>
            </table>
        </div>
    </div>

    {{-- For Print --}}
    @if(sizeof($reports) > 0)
    <div class="card">
        <div class="row" style="text-align: center; padding: 10px 10px; ">
            <div class="col-sm-12">
                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                    <i class="material-icons">print</i>
                    <span>PRINT...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>