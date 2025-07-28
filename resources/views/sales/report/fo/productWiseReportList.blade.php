<div class="card" id="printMe">
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
                        <th>{{ $orders->pname }}</th>
                        <th>{{ $orders->order_qty }}</th>                        
                        <th>@if($orders->delivered_qty!=null) {{ $orders->delivered_qty }} @else - @endif</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ $totalDeliveryQty }}</th>                        
                    </tr>

                @else
                    <tr>
                        <th colspan="5">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- For Print --}}
@if(sizeof($resultOrderList) > 0)
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