<div class="card">
                    <div class="header">
                        <h2>
                            Order List
                        </h2>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>ORDER NO</th>
                                        <th>COLLECT DATE</th>
                                        <th>EXECUTIVE</th>
                                        <th>CUSTOMER</th>
                                        <th>ORDER QTY</th>
                                        <th>ORDER VALUE</th> 
                                        <th>DELIVERY QTY</th>
                                        <th>DELIVERY VALUE</th>
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
                                    $totalQty  += $orders->total_order_qty;
                                    $totalValue += $orders->total_order_value;
                                    $totalDeliveryQty  += $orders->total_delivery_qty;
                                    $totalDeliveryValue += $orders->total_delivery_value;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>
                                            <a href="{{ URL('/mts-delivery-approved-view/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->order_no }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->first_name }}</th>                        
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                                        <th style="text-align: right;">{{ $orders->total_order_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_order_value,2) }}</th> 
                                        <th style="text-align: right;">{{ $orders->total_delivery_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_delivery_value,2) }}</th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                        <th style="text-align: right;">{{ $totalDeliveryQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDeliveryValue,2) }}</th>
                                       
                                    </tr>

                                @else
                                    <tr>
                                        <th colspan="9">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>