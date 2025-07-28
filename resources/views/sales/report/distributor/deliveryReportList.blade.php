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
                        <th>FO</th>
                        <th>CUSTOMER</th>
                        <th>QTY</th>
                        <th>VALUE</th>
						<th>OPTION</th>
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
                            <a href="{{ URL('/report/order-details/'.$orders->order_id) }}" title="Show Details" target="_blank">
                                {{ $orders->order_no }}
                            </a></br>
							
                        </th>
                        <th>{{ $orders->update_date }}</th>
                        <th>{{ $orders->display_name }}</th>                        
                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                        <th>{{ $orders->total_delivery_qty }}</th>                        
                        <th>{{ number_format($orders->total_delivery_value,2) }}</th>
						
						
						<th>
					
						<?php 
	
						$diff = '';
						$pointNotIN = array(266,267);
						$date1 = new DateTime($orders->update_date);
						$date2 = new DateTime(date('Y-m-d H:i:s'));
						$diff = $date2->diff($date1);
						//echo ($diff->h + ($diff->days*24)); exit;
						
						if( ($diff->h + ($diff->days*24)) <= 48 && !in_array($orders->point_id,$pointNotIN)) {
													
										?>
							
							<a href="{{ URL('/report/order-rollback-details/'.$orders->order_id) }}" title="Show Details" target="_blank">
                              &nbsp;&nbsp;&nbsp;<button type="button" class="btn btn-outline-danger">ROLL Back</button>
                            </a> 
							
						<?php } ?>	
							
							
						</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                        <th>{{ $totalQty }}</th>
                        <th>{{ number_format($totalValue,2) }}</th>
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