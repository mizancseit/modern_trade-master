<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($resultProductList) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr> 
                        <th>Customer name</th>
                        <th>SAP Code</th>  
                        <th>QTY</th>
                        <th>VALUE</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultProductList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalValue = 0;

                    @endphp

                    @foreach($resultProductList as $item)
                    @php
                    $totalQty += $item->product_qty;
                    $totalValue += $item->order_total_value;
                    @endphp                    
                    <tr> 
                        <th>{{ $item->customer_name }} </th>
                        <th>{{ $item->customer_sap_code }}</th>                        
                        <th>{{ $item->product_qty }}</th>                               
                        <th>{{ $item->order_total_value }}</th>    
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach 

                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif    
                    <tr>
                        <th colspan="2">Total.</th>
                        <th>{{$totalQty}}</th>
                        <th>{{$totalValue}}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>