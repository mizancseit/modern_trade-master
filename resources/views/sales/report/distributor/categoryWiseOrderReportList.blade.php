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
                        <th>SL</th>
                        <th>Category</th>
                        <th>Order QTY</th>
                        <th>Delivery QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalDeliveryQty = 0;
                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->order_qty;
                    $totalDeliveryQty += $orders->delivered_qty;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->catname }}</th>
                        <th>{{ $orders->order_qty }}</th>                        
                        <th>{{ $orders->delivered_qty }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="2" style="text-align: right;">Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ $totalDeliveryQty }}</th>
                    </tr>

                @else
                    <tr>
                        <th colspan="4">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>