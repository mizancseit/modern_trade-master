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
                                        <th>Order No</th>
                                        <th>COLLECT DATE</th>
                                        <th>INITIATOR</th>
                                        <th>CUSTOMER</th>
                                        <th>QTY</th>
                                        <th>VALUE</th> 
                                        <th>REMARKS</th> 
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
                                    $totalQty  += $orders->total_order_qty;
                                    $totalValue += $orders->total_order_value;
                                    @endphp                    
                                    <tr>
                                        <th>{{ $serial }}</th>
                                        <th>
                                            <a href="{{ URL('/mts-order-view/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->order_no }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->display_name }}</th>                        
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                                        <th style="text-align: right;">{{ $orders->total_order_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_order_value,2) }}</th> 
                                         
                                        <th>  {{ $orders->po_no }} </th>  
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