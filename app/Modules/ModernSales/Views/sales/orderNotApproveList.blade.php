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
                        <th>ORDER DATE</th>
                        <th>FO</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        <th>VALUE</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;

                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->total_order_qty;
                    $totalValue += $orders->total_order_value;
                    @endphp                    
                    <tr>
                        <th>
                            <a href="{{ URL('/mts-bucket/'.$orders->customer_id.'/'.$orders->party_id.'?order_id='. $orders->order_id) }}" title="Show Details" target="_blank">
                                {{ $orders->order_no }}
                            </a></br>
							
                        </th>
                        <th>{{ $orders->update_date }}</th>
                        <th>{{ $orders->display_name }}</th>                        
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th>{{ $orders->total_order_qty }}</th>                        
                        <th>{{ number_format($orders->total_order_value,2) }}</th>
						
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ number_format($totalValue,2) }}</th>
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