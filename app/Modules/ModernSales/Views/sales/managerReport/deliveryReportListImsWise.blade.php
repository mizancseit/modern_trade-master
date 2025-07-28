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
                        <th>SL.</th> 
                        <th>Date</th> 
                        <th>Customer Code</th>   
                        <th>Customer Name</th>
                        <th>Material </th>   
                        <th>Description</th>   
                        <th>Qty</th>
                        <th>Value</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalOrderQty = 0;
                    $totalOrderValue = 0;
                    $totalDelivaryQty = 0;
                    $totalDeliveryValue = 0;
                    $totalOrderCommission = 0;
                    @endphp
                    @foreach($resultOrderList as $orders)        
                                 
                    <tr>
                        <th>{{ $serial }}</th>  
                        <th>{{ date('d-m-Y', strtotime($orders->order_date))}}</th>                        
                        <th>{{ $orders->customer_code }}</th> 
                        <th>{{ $orders->name }}</th> 
                        <th>{{ $orders->sap_code }}</th> 
                        <th>{{ $orders->product_name }}</th> 
                        <th>{{ $orders->total_order_qty }}</th>
                        <th>{{ number_format($orders->total_order_value, 2) }}</th> 
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach 

                    @else
                    <tr>
                        <th colspan="13">No record found.</th>
                    </tr>
                    @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
