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
                        <th>Delivery QTY</th>
                        <th>Free QTY</th>
                        <th>Replace QTY</th>
                        <th>Total QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalFreeQty = 0;
                    $totalReplaceQty = 0;
                    $totalFinal = 0;
                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->delivered_qty;
                    $totalFreeQty += $orders->free_qty;
                    $totalReplaceQty += $orders->replace_delivered_qty;
                    $totalFinal += $orders->delivered_qty + $orders->free_qty + $orders->replace_delivered_qty;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->catname }}</th>
                        <th>{{ $orders->pname }}</th>
                        <th>{{ $orders->delivered_qty }}</th>                        
                        <th>@if($orders->free_qty!=null) {{ $orders->free_qty }} @else - @endif</th>                        
                        <th>@if($orders->replace_delivered_qty!=null) {{ $orders->replace_delivered_qty }} @else - @endif </th>
                        <th>{{ $orders->delivered_qty + $orders->free_qty + $orders->replace_delivered_qty }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ $totalFreeQty }}</th>
                        <th>{{ $totalReplaceQty }}</th>
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