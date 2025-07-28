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
                        <th>DIV</th>
                        <th>ORDER NO</th>
                        <th>DATE</th>
                        <th>POINT</th>
                        <th>RETAILER</th>
                        <th>FO</th>
                        <th>ORDER QTY</th>
                        <th>ORDER VALUE</th>
                    </tr>
                </thead>
                
                <tbody style="font-size: 13px;">
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;
                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->total_qty;
                    $totalValue += $orders->total_value;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th> </th>
                        <th>{{ $orders->order_no }}</th>
                        <th>{{ $orders->order_date }}</th>
                        <th>{{ $orders->point_name }}</th>                        
                        <th>{{ $orders->first_name }}</th>                        
                        <th>{{ $orders->name }}</th>                        
                        <th>{{ $orders->total_qty }}</th>                        
                        <th>{{ number_format($orders->total_value,2) }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="7" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ number_format($totalValue,2) }}</th>
                    </tr>

                @else
                    <tr>
                        <th colspan="9">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>