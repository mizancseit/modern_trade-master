<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($resultRetailers) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Retailer</th>
                        <th>Route</th>
                        <th>Remaining Commission Balance</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultRetailers) > 0)   
                    @php
                    $serial =1;
                    $totalBalance = 0;                                    
                    @endphp

                    @foreach($resultRetailers as $orders)
                    @php

                    $totalBalance  += $orders->totalBalance - $orders->totalBuyBalance;
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->name }}</th>
                        <th>{{ $orders->rname }}</th>
                        <th>{{ $orders->totalBalance - $orders->totalBuyBalance }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="3" style="text-align: right;">Total : </th>
                        <th>{{ $totalBalance }}</th>
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