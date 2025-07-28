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
                            <th>Current Total Stock Qnty</th>
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
                        $serial1        = 0;
                        $startDate     = date('Y-m'.'-01');
                        $endDate       = date('Y-m'.'-31');
                        $yesterday     = date('Y-m-d', strtotime('-1 day'));
                        $currentYear   = date('Y');

                        $searchMonth   = date('m',strtotime($todate)); //current month
                        $currentMothStart = date('Y-'.$searchMonth.'-01');
                        $currentMothEnd = date('Y-'.$searchMonth.'-31');


                        //previous month

                        $previousMonth = date("m", strtotime("-1 month")); //current month + 1 month add
						$prevYear   = date("Y", strtotime("-1 year"));
                        
						//echo $prevYear;
						
						if($previousMonth != 12)
						{
						
							$previousMonthStart = date('Y-'.$previousMonth.'-01');
							$previousMonthEnd   = date('Y-'.$previousMonth.'-31');
						
						} else {
							
							$previousMonthStart = date($prevYear . '-' . $previousMonth.'-01');
							$previousMonthEnd   = date($prevYear . '-' . $previousMonth.'-31');
	
						}						
						
						

                        //echo $previousMonth;
                        //echo $previousMonthStart.'--'.$previousMonthEnd;

						//echo $todate;	

                        $tosearchDate     = strtotime($todate);
                        $searchNextMonth  = date("m", strtotime("+1 month", $tosearchDate)); //current month + 1 month add
                        $searchNextMonthStart = date('Y-'.$searchNextMonth.'-01');
                        $searchNextMonthEnd   = date('Y-'.$searchNextMonth.'-31');

                        $advancePettyCash = 0;


                        $searchYesterday = date("Y-m-d", strtotime("-1 day", $tosearchDate)); //search day ( - 1 day ) add

                        //echo $searchYesterday;
						
						
						
						$GarndTotdepoTotSalesPrevMonth  = 0;
						 $GarndTotcurrentMonthTarget = 0;
						
						 $GarndTotdepoTotSalesSearchAsOfTotalSales  = 0;
						 $GarndTottotalSalesVstotaltarget  = 0;
						 $GarndTottotalCollection  = 0;
						 $GarndTotdepoTotSalesLastDay  = 0;
						
						 $GarndTottotalCollectionLastDay  = 0;
						 $GarndTottotProdRemQnty  = 0;
						 $GarndTotStockBalance = 0;
						 $GarndTotdepoCashInHand = 0;
						
					
						
						 $GarndTotdepoMarketCredit = 0;
						 $GarndtotalAsset = 0;
						
						
						$StockQnty = 0;
						
                        
						@endphp
                        @foreach($resultDepot as $result)
                        @php
                        
						$depoTotSalesLastDay = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            //->where('order_type', 'Delivered')
                                            ->where('total_delivery_qty','>',0)
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($searchYesterday ,$searchYesterday))
                                            ->sum('total_delivery_value');

                        $depoTotSalesPrevMonth = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            //->where('order_type', 'Delivered')
                                            ->where('total_delivery_qty','>',0)
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($previousMonthStart ,$previousMonthEnd))
                                            ->sum('total_delivery_value');

                        $depoTotSalesSearchAsOfTotalSales = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            //->where('order_type', 'Delivered')
                                            ->where('total_delivery_qty','>',0)
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($currentMothStart ,$todate))
                                            ->sum('total_delivery_value');

                        $totalCollection = DB::table('depot_collection')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($currentMothStart ,$todate))
                                            ->sum('collection_amount');
											
						$totalCollectionLastDay = DB::table('depot_collection')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array($searchYesterday ,$searchYesterday))
                                            ->sum('collection_amount');		

						$resStockSummary = DB::table('depot_inventory')
                                    ->select('inventory_type', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
                                    ->where('point_id', $result->point_id)
									->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->groupBy('inventory_type')
                                    ->get();
									
							
						$resProductIN = DB::table('depot_inventory')
                                    ->select('product_id', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
                                    ->where('point_id', $result->point_id)
                                    ->where('inventory_type', 1)
									->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->groupBy('product_id')
                                    ->get();
						
						
						
						$TotProdIn = 0;
						$TotProdInPrice = 0;
						if(sizeof($resProductIN)>0)
						{
							foreach($resProductIN as $rowStockProdIN)
							{	
								$resProdPrice = DB::table('tbl_product')
                                    ->select('id', 'mrp', 'depo', 'distri')
                                    ->where('id', $rowStockProdIN->product_id)
								    ->first();	
							
								if(sizeof($resProdPrice)>0)
								{
									$TotProdIn += $rowStockProdIN->tot_qnty;
									$TotProdInPrice += $rowStockProdIN->tot_qnty * $resProdPrice->depo;
								}									
								
							}
						}

					
						
						$resProductOut = DB::table('depot_inventory')
                                    ->select('product_id', DB::raw('SUM(product_qty) AS tot_qnty'), DB::raw('SUM(product_value) AS tot_value'))
                                    ->where('point_id', $result->point_id)
                                    ->where('inventory_type', 2)
									->whereBetween(DB::raw("(DATE_FORMAT(inventory_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->groupBy('product_id')
                                    ->get();
									
						$TotProdOut = 0;
						$TotProdOutPrice = 0;
						if(sizeof($resProductOut)>0)
						{
							foreach($resProductOut as $rowStockProdOut)
							{	
								$resProdPrice = DB::table('tbl_product')
                                    ->select('id', 'mrp', 'depo', 'distri')
                                    ->where('id', $rowStockProdOut->product_id)
								    ->first();	
							
								if(sizeof($resProdPrice)>0)
								{
									$TotProdOut += $rowStockProdOut->tot_qnty;
									$TotProdOutPrice += $rowStockProdOut->tot_qnty * $resProdPrice->depo;
								}									
								
							}
						}
						

						
						//$TotProdInPrice = 50;
						//$TotProdOutPrice = 90;
						
						$StockBalance = $TotProdInPrice - $TotProdOutPrice;
						
						$StockQnty =  $TotProdIn - $TotProdOut;
									

						$depoTotExpense = array();			
						$tot_expense = 0;
						
						$depoTotExpense = DB::table('depot_cash_book AS c')
                                    ->select(DB::raw('SUM(c.trans_amount) AS tot_expense'))
									->join('depot_accounts_head AS h', 'c.perticular_head_id', '=', 'h.accounts_head_id')    
                                    ->where('c.point_id', $result->point_id)
                                    ->where('h.accounts_head_type', 'expense')
									->whereBetween(DB::raw("(DATE_FORMAT(c.trans_date,'%Y-%m-%d'))"), array('2000-01-01',$todate))
                                    ->get();
						
						if(sizeof($depoTotExpense)>0)
						{
							foreach($depoTotExpense as $rowDepoTotExpense)
							{
								$tot_expense = $rowDepoTotExpense->tot_expense;
							}
						}					
							
						
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
                            
							<th> {{ number_format($depoTotSalesPrevMonth,0) }} </th>
							
							<?php $GarndTotdepoTotSalesPrevMonth += $depoTotSalesPrevMonth; ?>
							
							
							
							
                           
						   <th>
                                @php
                                $currentMonthTarget = DB::table('tbl_fo_target')
                                    ->select('tbl_fo_target.*','tbl_user_business_scope.user_id','tbl_user_business_scope.point_id')
                                    ->leftJoin('tbl_user_business_scope','tbl_fo_target.fo_id','=','tbl_user_business_scope.user_id')
									->join('users','users.id','=','tbl_user_business_scope.user_id')
									->where('tbl_fo_target.global_company_id', Auth::user()->global_company_id)
                                    ->where('tbl_user_business_scope.point_id', $result->point_id)
                                    ->where('users.user_type_id', 12)
                                    ->whereDate('tbl_fo_target.start_date','>=',$currentMothStart)
                                    ->whereDate('tbl_fo_target.end_date','<=',$currentMothEnd)
                                    ->sum('tbl_fo_target.total_value');

                                echo number_format($currentMonthTarget,0);
								
								$GarndTotcurrentMonthTarget += $currentMonthTarget;
								
                                @endphp
                            </th>
                            
							<th> 
								{{ number_format($depoTotSalesSearchAsOfTotalSales,0) }} </th>
								
							<?php $GarndTotdepoTotSalesSearchAsOfTotalSales +=  $depoTotSalesSearchAsOfTotalSales; ?>

								
								
								
							
							
                            
							<th>
                                @php
								$totalSalesVstotaltarget = 0;
                                //echo $currentMonthTarget.'<br />'.round($depoTotSalesSearchAsOfTotalSales).'<br />';
								if(round($depoTotSalesSearchAsOfTotalSales)>0 && $currentMonthTarget>0)
                                {
                                    $figureRoundSales = round($depoTotSalesSearchAsOfTotalSales);
                                    $figureRoundTarget = round($currentMonthTarget);

                                    echo round(($figureRoundSales / $figureRoundTarget)*100).'%';
                                   
								    $totalSalesVstotaltarget = ($figureRoundSales / $figureRoundTarget)*100;

                                    // as per total sales / total target
                                    
                                }
                                else
                                {
                                    echo '0%';
                                }
								
									$GarndTottotalSalesVstotaltarget +=  $totalSalesVstotaltarget;
                                    
                                @endphp
                                
                            </th>
							
						
                            <th> {{ number_format($totalCollection,0) }} </th>
							
								<?php $GarndTottotalCollection += $totalCollection; ?>
							
							
							
							
							
                            
							<th> {{ number_format($depoTotSalesLastDay,0) }}  </th>
							
							<?php $GarndTotdepoTotSalesLastDay += $depoTotSalesLastDay; ?>
							
							
                            
							<th> {{ number_format($totalCollectionLastDay,0) }}  </th>
							
							<?php $GarndTottotalCollectionLastDay += $totalCollectionLastDay; ?>
                            
							<th>
							@php
									
								// depoTotSalesSearchAsOfTotalSales totalCollection tot_expense									
								
								$resStockSummaryTotal = 0; $totProdRemValue = 0;
                               
								$totProdInQnty =0; $totProdInValue = 0;
							    $totProdOutQnty =0; $totProdOutValue = 0;
							    $totProdRemQnty =0; $totProdRemValue = 0; 

					           /*
								if( sizeof($resStockSummary)>0 )
								{
									
									foreach($resStockSummary as $rowStockSummary)
									{
										
										$StockINfo['totProdQnty'][$rowStockSummary->inventory_type] = $rowStockSummary->tot_qnty;
										$StockINfo['totProdValue'][$rowStockSummary->inventory_type] = $rowStockSummary->tot_value;
										
									}	
										
										
										$totProdInQnty = $StockINfo['totProdQnty'][1];
										$totProdInValue = $StockINfo['totProdValue'][1];
										
										$totProdOutQnty = $StockINfo['totProdQnty'][2];
										$totProdOutValue = $StockINfo['totProdValue'][2];
										
										$totProdRemQnty = $totProdInQnty - $totProdOutQnty;
										$totProdRemValue = $totProdInValue - $totProdOutValue;
										
										//echo $totProdRemQnty;
										echo number_format($StockQnty,0);
										
										$GarndTottotProdRemQnty += $totProdRemQnty;
									
								
								} else {
									echo 0;
								}
								
								*/
								
								echo number_format($StockQnty,0);						
							 
								
                                 @endphp
                            </th>
							
							 <th>
							 
							 <?php
							 
								/*
								$resStockSummary = DB::select("SELECT SUM(ds.stock_qty) as totStock, SUM(p.depo * ds.stock_qty) as totStockVal 
																FROM depot_stock  ds JOIN tbl_product p ON ds.product_id = p.id
																WHERE ds.point_id = '".$result->point_id."' ");
								
								
								if(sizeof($resStockSummary)>0)
								{
									echo $resStockSummary[0]->totStockVal;
								} else {
									echo 0;
								}
								*/
								
								//echo $totProdRemValue;
								
								echo number_format($StockBalance,0);
								
								$GarndTotStockBalance += $StockBalance;
								
								?>
												 
							
                            </th>
							
							
                            <th>
                                @php
                                    
									$depoOpenCashInHand = DB::select("SELECT opening_cash_in_hand FROM tbl_depot_summary WHERE point_id 
                                            in (SELECT point_id FROM tbl_user_business_scope 
                                            WHERE point_id = '".$result->point_id."')");                                  

                        		
                                    $retOpenTot = DB::select("SELECT SUM(opening_balance) totRetOpenBal FROM tbl_retailer WHERE point_id 
                                        in (SELECT point_id FROM tbl_user_business_scope 
                                        WHERE point_id = '".$result->point_id."')");

                                  
                                    $depoMarketCredit = 0;
                                    if(sizeof($retOpenTot)>0)
                                    {
                                        $totRetOpenBal = $retOpenTot[0]->totRetOpenBal;
                                    } else {
                                        $totRetOpenBal = 0;
                                    }

                                    $depoCashInHand = 0;
                                    if(sizeof($depoOpenCashInHand)>0)
                                    {
                                        $opening_cash_in_hand = $depoOpenCashInHand[0]->opening_cash_in_hand;
                                    } else {
                                        $opening_cash_in_hand = 0;
                                    }
                                    
							/*	 
							$depoGrandTotSales = DB::table('tbl_order')
                                            ->where('global_company_id', Auth::user()->global_company_id)
                                            //->where('order_type', 'Delivered')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array('2000-01-01' ,$todate))
                                            //->sum('grand_total_value');
                                            ->sum('total_value');
							*/

							$SqlGrandTotSales = DB::select("SELECT SUM(retailer_invoice_sales) tot_sales
											FROM  retailer_credit_ledger WHERE point_id = $result->point_id");	

							if(sizeof($SqlGrandTotSales)>0)	
							{
								$depoGrandTotSales = $SqlGrandTotSales[0]->tot_sales;
							}						
											
							$totalGrandCollection = DB::table('depot_collection')
                                            ->where('point_id', $result->point_id)
                                            ->whereBetween(DB::raw("(DATE_FORMAT(collection_date,'%Y-%m-%d'))"), array('2000-01-01' ,$todate))
                                            ->sum('collection_amount');	
							
							
									
									echo $depoCashInHand = ($opening_cash_in_hand + $totalGrandCollection) - $tot_expense;
									
									$GarndTotdepoCashInHand  += $depoCashInHand;
                               
							   @endphp
                            </th>
                           
							<th> {{ 0 }}</th>
                            
							<th>
							
							  
							   {{ $depoMarketCredit = number_format(($totRetOpenBal + $depoGrandTotSales) - $totalGrandCollection,0) }}
							   
							   
							 <?php  $GarndTotdepoMarketCredit  += ($totRetOpenBal + $depoGrandTotSales) - $totalGrandCollection; ?>
							   
                            </th>

                            
							<th>
                                
                                <?php        


										
								

                                $currentMarketCredit = round(($totRetOpenBal + $depoGrandTotSales) - $totalGrandCollection);
                                $currentCashInHand = round(($opening_cash_in_hand + $totalGrandCollection) - $tot_expense,0);

                                //$totalAsset = (round($resStockSummaryTotal) + round($currentMarketCredit) + round($currentCashInHand));
                                $totalAsset = (round($StockBalance) + round($currentMarketCredit) + round($currentCashInHand));

                                echo number_format($totalAsset,0);
								
								$GarndtotalAsset += $totalAsset;

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
                                elseif($result->point_id==540) // Rampura Depot-L
                                {
                                    echo ' 3,500,000';
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
                        $serial1 ++;
                        @endphp
                        @endforeach
						
						<tr  style="font-size: 11px; font-weight: normal;">
						    <th colspan="2">Grand Total</th>
							
							<th>{{ number_format($GarndTotdepoTotSalesPrevMonth,0) }}</th>
							<th>{{ number_format($GarndTotcurrentMonthTarget,0) }}</th>							
							<th>{{ number_format($GarndTotdepoTotSalesSearchAsOfTotalSales,0) }}</th>
							<th>{{ number_format(($GarndTotdepoTotSalesSearchAsOfTotalSales/$GarndTotcurrentMonthTarget) *100,2).'%' }}</th>
							<th>{{ number_format($GarndTottotalCollection,0) }}</th>
							<th>{{ number_format($GarndTotdepoTotSalesLastDay,0) }}</th>
							
							<th>{{ number_format($GarndTottotalCollectionLastDay,0) }}</th>
							<th>{{ number_format($GarndTottotProdRemQnty,0) }}</th>
							<th>{{ number_format($GarndTotStockBalance,0) }}</th>
							<th>{{ number_format($GarndTotdepoCashInHand,0) }}</th>
							
							<th>0</th>
							
							<th>{{ $GarndTotdepoMarketCredit }}</th>
							<th>{{ $GarndtotalAsset }}</th>
							
							<th></th>
						
						</tr>
						
						
                        @endif
                    </tbody>                                
                    
                </table>
            </div>
        </div>
    </div>
</div>