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
                    <tr style="font-size: 11px;">
                        <th>SL</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th style="text-align: right;">Delivery QTY</th>
                        <th style="text-align: right;">Free QTY</th>
                        <th style="text-align: right;">Replace QTY</th>
                        <th style="text-align: right;">Total QTY</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultOrderList) > 0)   
                    @php
                    $serial =1;
                    $totalQty = 0;
                    $totalFreeQty = 0;
                    $totalReplaceQty = 0;
                    $totalFinal = 0;
                    @endphp

                    @foreach($resultOrderList as $orders)
                    @php
                    $freeQty = DB::select("SELECT e.point_id,e.product_id,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value,sum(e.free_delivery_qty) AS delQty,sum(e.free_delivery_value) AS delValue FROM (
                        SELECT point_id,product_id,sum(total_free_qty) AS total_free_qty, sum(total_free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_special_free_qty WHERE point_id=$pointID AND product_id='".$orders->pid."' AND date(delivery_date) BETWEEN '".$fromdate."' AND '".$todate."' group by product_id
                        UNION ALL
                        SELECT point_id,product_id,sum(total_free_qty) AS total_free_qty, sum(total_free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_special_and_free_qty WHERE point_id=$pointID AND product_id='".$orders->pid."' AND date(delivery_date) BETWEEN '".$fromdate."' AND '".$todate."' group by product_id
                        UNION ALL
                        SELECT point_id,product_id,sum(total_free_qty) AS total_free_qty, sum(total_free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_free_qty WHERE point_id=$pointID AND product_id='".$orders->pid."' AND date(delivery_date) BETWEEN '".$fromdate."' AND '".$todate."' group by product_id
                        UNION ALL
                        SELECT point_id,product_id,sum(total_free_qty) AS total_free_qty, sum(total_free_value) AS total_free_value,sum(free_delivery_qty) AS free_delivery_qty,sum(free_delivery_value) AS free_delivery_value FROM tbl_order_regular_and_free_qty WHERE point_id=$pointID AND product_id='".$orders->pid."' AND date(delivery_date) BETWEEN '".$fromdate."' AND '".$todate."' group by product_id
                        ) AS e 
                        group by e.product_id

                        ");



                    $totalQty  += $orders->delivered_qty;
                    $totalFreeQty += (count($freeQty)>0?$freeQty[0]->qty:0);
                    $totalReplaceQty += $orders->replace_delivered_qty;
                    $totalFinal += $orders->delivered_qty + (count($freeQty)>0?$freeQty[0]->qty:0) + $orders->replace_delivered_qty;
                    @endphp                    
                    <tr style="font-size: 11px;">
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->catname }}</th>
                        <th>{{ $orders->pname }}</th>
                        <th style="text-align: right;">{{ $orders->delivered_qty }}</th>                        
                        <th style="text-align: right;">@if(sizeof($freeQty)>0) {{$freeQty[0]->qty }} @else 0 @endif</th>                        
                        <th style="text-align: right;">@if($orders->replace_delivered_qty!=null) {{ $orders->replace_delivered_qty }} @else 0 @endif </th>
                        <th style="text-align: right;">{{ $orders->delivered_qty + (count($freeQty)>0?$freeQty[0]->qty:0) + $orders->replace_delivered_qty }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach

                    <tr style="font-size: 11px;">
                        <th colspan="3" style="text-align: right;">Total : </th>                        
                        <th style="text-align: right;">{{ $totalQty }}</th>
                        <th style="text-align: right;">{{ $totalFreeQty }}</th>
                        <th style="text-align: right;">{{ $totalReplaceQty }}</th>
                        <th style="text-align: right;">{{ $totalFinal }}</th>
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