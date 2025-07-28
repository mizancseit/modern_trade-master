<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($resultOrderList) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>ORDER</th>
                        <th>DELIVERY DATE</th>
                        <th>APPROVED DATE</th>
                        <th>FO</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        <th>VALUE</th>
                        <th>GRAND TOTAL</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;
                    $gTotalValue = 0;

                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->total_delivery_qty;
                    $totalValue += $orders->total_delivery_value;
                    @endphp                    
                    <tr>
                        <th>
                            <a href="{{ URL('/modernorder-details/'.$orders->order_id) }}" title="Show Details" target="_blank">
                                {{ $orders->order_no }}
                            </a></br>
							
                        </th>
                        <th>{{ $orders->update_date }}</th>
                        <th>{{ $orders->manager_approved_date }}</th>
                        <th>{{ $orders->display_name }}</th>                        
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th>{{ $orders->total_delivery_qty }}</th>                        
                        <th>{{ number_format($orders->total_delivery_value,2) }}</th> 		
                        @php
                            $orderCommission = DB::table('mts_categroy_wise_commission') 
                                ->select('order_id','party_id',DB::raw('SUM(order_commission_value) AS commission'),'entry_by')
                                ->where('order_id', $orders->order_id)  
                                ->first();
                            // $customerResult = DB::table('mts_customer_list')
                            //     ->where('customer_id',$orders->customer_id)
                            //     ->where('status',0)
                            //     ->first();

                            // $closingResult = DB::table('mts_outlet_ledger')
                            //     ->where('customer_id',$orders->customer_id)
                            //     ->orderBy('ledger_id','DESC')
                            //     ->first();

                            // if(sizeof($closingResult)>0){
                            //     $closingBalance = $closingResult->closing_balance;
                            // } else{
                            //     $closingBalance = 0;
                            // } 

                            // $creditSummery = $customerResult->credit_limit - $closingBalance - $orders->total_order_value;
                            if($orders->total_order_value > 0 && $orderCommission){
                                $grandTotalValue = $orders->total_order_value - $orderCommission->commission;
                            } else {
                                $grandTotalValue = $orders->total_order_value;
                            }
                            $gTotalValue += $grandTotalValue; 
                            @endphp    				
                         <th style="text-align: center;">  
                                {{ number_format($grandTotalValue,2) }} 
                        </th> 						
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th style="text-align: center;">{{ number_format($totalValue,2) }}</th>
                        <th style="text-align: center;">{{ number_format($gTotalValue,2) }}</th>
                    </tr>

                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>