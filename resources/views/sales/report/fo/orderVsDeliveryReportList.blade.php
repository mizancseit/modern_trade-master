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
                        <th>ORDER</th>
                        <th>DATE</th>                        
                        <th>CUSTOMER</th>
                        <th style="text-align: right;">QTY</th>
                        <th style="text-align: right;">VALUE</th>
                        <th style="text-align: right;">DELIVERY QTY</th>
                        <th style="text-align: right;">VALUE</th>
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
                        <th>{{ $orders->name }}</th>                        
                        <th style="text-align: right;">{{ $orders->total_qty }}</th>                        
                        <th style="text-align: right;">{{ number_format($orders->total_value,2) }}</th>
                        <th style="text-align: right;">@if($orders->total_delivery_qty!=null){{ $orders->total_delivery_qty }} @else 0 @endif</th>                        
                        <th style="text-align: right;">{{ number_format($orders->total_delivery_value,2) }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Total : </th>                        
                        <th style="text-align: right;">{{ $totalQty }}</th>
                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                        <th style="text-align: right;">{{ $totalDeliveryQty }}</th>
                        <th style="text-align: right;">{{ number_format($totalDeliveryValue,2) }}</th>
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

{{-- For Print --}}
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