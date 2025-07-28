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
                        <th>EXECUTIVE</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        {{-- <th>VALUE</th> --}}
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
                    $totalQty  += $orders->total_delivery_qty;
                    $totalValue += $orders->total_delivery_value;
                    @endphp                    
                    <tr>
                        <th>
                            <a href="{{ URL('/mts-manager-replace-delivery-report-details/'.$orders->replace_id) }}" title="Show Details" target="_blank">
                                {{ $orders->replace_no }}
                            </a></br>
							
                        </th>
                        <th>{{ $orders->update_date }}</th>
                        <th>{{ $orders->display_name }}</th>                        
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th>{{ $orders->total_delivery_qty }}</th>                        
                       {{--  <th>{{ number_format($orders->total_delivery_value,2) }}</th> --}}
						
						
						
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        {{-- <th>{{ number_format($totalValue,2) }}</th> --}}
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