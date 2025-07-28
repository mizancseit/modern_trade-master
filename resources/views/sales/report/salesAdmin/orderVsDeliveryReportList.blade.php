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
                        <th>FO</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        <th>VALUE</th>
                        <th>DELIVERY QTY</th>
                        <th>DELIVERY VALUE</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;
                    $totalDeliveryQty = 0;
                    $totalDeliveryValue = 0;

                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->total_qty;
                    $totalValue += $orders->total_value;
                    $totalDeliveryQty  += $orders->total_delivery_qty;
                    $totalDeliveryValue += $orders->total_delivery_value;
                    @endphp                    
                    <tr>
                        <th>{{ $orders->order_no }}</th>
                        <th>{{ $orders->order_date }}</th>
                        <th>{{ $orders->first_name }}</th>                        
                        <th>{{ $orders->name }}</th>                        
                        <th>{{ $orders->total_qty }}</th>                        
                        <th>{{ number_format($orders->total_value,2) }}</th>
                        <th>{{ $orders->total_delivery_qty }}</th>                        
                        <th>{{ number_format($orders->total_delivery_value,2) }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ number_format($totalValue,2) }}</th>
                        <th>{{ $totalDeliveryQty }}</th>
                        <th>{{ number_format($totalDeliveryValue,2) }}</th>
                    </tr>

                @else
                    <tr>
                        <th colspan="8">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>