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
                        <th>WASTAGE</th>
                        <th>DATE</th>                        
                        <th>CUSTOMER</th>
                        <th style="text-align: right;">WASTAGE QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;

                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $totalQty  += $orders->total_qty;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th> 
                        <th>
                            <a href="{{ URL('/report/wastage/distributor/invoice-details/'.$orders->order_id) }}" title="Invoice Details" target="_blank">
                                {{ $orders->order_no }}
                            </a>
                        </th>
                        <th>{{ $orders->order_date }}</th>                                                
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th style="text-align: right;">{{ $orders->total_qty }}</th> 
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="4" style="text-align: right;">Total : </th>                        
                        <th style="text-align: right;">{{ $totalQty }}</th>
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