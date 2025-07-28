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
                        <th>Product</th>
                        <th>Order QTY</th>
                        <th>Free QTY</th>
                        <th>Wastage QTY</th>
                        <th>Total QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalFreeQty = 0;
                    $totalWastageQty = 0;
                    $totalFinal = 0;
                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->order_qty;
                    $totalFreeQty += $orders->free_qty;
                    $totalWastageQty += $orders->wastage_qty;
                    $totalFinal += $orders->order_qty + $orders->wastage_qty + $orders->wastage_qty;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->catname }}</th>
                        <th>{{ $orders->pname }}</th>
                        <th>{{ $orders->order_qty }}</th>                        
                        <th>@if($orders->free_qty!=null) {{ $orders->free_qty }} @else - @endif</th>                        
                        <th>@if($orders->wastage_qty!=null) {{ $orders->wastage_qty }} @else - @endif </th>
                        <th>{{ $orders->order_qty + $orders->wastage_qty + $orders->wastage_qty }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ $totalFreeQty }}</th>
                        <th>{{ $totalWastageQty }}</th>
                        <th>{{ $totalFinal }}</th>
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