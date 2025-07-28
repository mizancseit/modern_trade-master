<div class="card">
    <div class="header">
        <h2>
            Analysis List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>CAT NAME</th>
                        <th>SAP CODE</th>
                        <th>PRODUCT NAME</th>
                        <th>QTY</th>
                        <th>UPDATE QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if(sizeof($resultCartPro) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;

                    @endphp

                    @foreach($resultCartPro as $orders)
                    @php
                    $totalQty  += $orders->total_qty;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        
                        <th>{{ $orders->catname }}</th>
                        <th>
                            <a href="{{ URL('/product-wise-analysis/'.$orders->pid) }}" title="Confirm Delivery" target="_blank">
                                {{ $orders->sap_code }}
                            </a>
                        </th>
                        <th>{{ $orders->pname }}</th>                                           
                        <th style="text-align: right;">{{ $orders->total_qty }}</th>  
                        <th style="text-align: right;">{{ $orders->total_approved_qty }}</th>  

                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                        <th style="text-align: right;">{{ $totalQty }}</th>
                        <th style="text-align: right;">{{ $totalQty }}</th>
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