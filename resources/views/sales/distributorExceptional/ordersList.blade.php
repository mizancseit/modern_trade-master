<div class="card">
    <div class="header">
        <h2>
            ORDER LIST
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>ORDER</th>
                        <th>ORDER DATE</th>
                        <th>FO</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        <th>VALUE</th>
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
                    $totalQty  += $orders->total_qty;
                    $totalValue += $orders->total_value;
					
					
                    @endphp                    
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>
			   <a href="{{ URL('/order-check-exceptional/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->order_no }}
                            </a>
                        </th>
                        <th>{{ $orders->order_date }}</th>
                        <th>{{ $orders->first_name }}</th>                        
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th style="text-align: right;">{{ $orders->total_qty }}</th>                        
                        <th style="text-align: right;">{{ number_format($orders->total_value,2) }}</th> 
                      <!--  <th>
                             <a href="{{ URL('/invoice/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Invoice Details" target="_blank">
                                <img src="{{URL::asset('resources/sales/images/icon/ic_details.png')}}" alt="Invoice Details">
                            </a> 
                        </th>  -->
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                        <th style="text-align: right;">{{ $totalQty }}</th>
                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
						
                      
                    </tr>

                @else
                    <tr>
                        <th colspan="8">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>