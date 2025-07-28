<div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultIms) }} results 
                        </h5>
                    </div>                  
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Group</th>
                                        <!-- <th style="text-align: right;">Delivery Qty</th>
                                        <th style="text-align: right;">Delivery Value</th> -->
                                        <th style="text-align: right;">Order Qty</th>
                                        <th style="text-align: right;">Order Value</th>
                                        <th style="text-align: right;">Memo</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @php
                                $s=1;
                                $totalMemo =0;
                                $totalQty =0;
                                $totalValue =0;
                                $totalDQty =0;
                                $totalDValue =0;
                                @endphp
                                @foreach($resultIms as $orders)
                                @php                                
                                $totalMemo += $orders->totalMemo;
                                $totalQty +=$orders->orderQty;
                                $totalValue +=$orders->orderValue;

                                $totalDQty +=$orders->deliveryQty;
                                $totalDValue +=$orders->deliveryValue;
                                @endphp    
                                    <tr style="font-size: 13px;">
                                        <th>{{ $s }}</th>
                                        <th> {{ date('d-m-Y',strtotime($orders->order_date)) }}</th>
                                        <th>{{ $orders->point_name }}</th>                        
                                        <th>{{ $orders->display_name }}</th>
                                        <th>{{ $orders->name }}</th>
                                        <!-- <th style="text-align: right;">{{ $orders->deliveryQty }}</th>
                                        <th style="text-align: right;">{{ $orders->deliveryValue }}</th> -->

                                        <th style="text-align: right;">{{ $orders->orderQty }}</th>
                                        <th style="text-align: right;">{{ $orders->orderValue }}</th>
                                        <th style="text-align: right;">{{ $orders->totalMemo }}</th>
                                    </tr>
                                @php
                                $s++;
                                @endphp                                   
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right;">Total</th>                                    
                                        <!-- <th style="text-align: right;">{{ $totalDQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDValue,2) }}</th> -->
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                        <th style="text-align: right;">{{ $totalMemo }}</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <!---- offer show start -->
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Slab</th>
                                        <th>Group</th>
                                        <th>Free Qty</th>
                                        <th>Free Value</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                               
                                @foreach($freeSlab as $freeSlab)
                                
                                    <tr style="font-size: 13px;">
                                        <th> {{ date('d-m-Y',strtotime($freeSlab->date)) }}</th>
                                        <th>{{ $freeSlab->point_name }}</th>                        
                                        <th>{{ $freeSlab->display_name }}</th>
                                        <th colspan="4">{{ $freeSlab->slab }}</th>
                                    </tr>

                                    @php

                                    $freeQty =  DB::select("SELECT date(e.order_date) AS date,e.fo_id,e.slab,e.catid,tbl_product_category.name,sum(e.total_free_qty) AS qty,sum(e.total_free_value) AS value FROM (
                                            SELECT order_date,fo_id,slab,catid,total_free_qty,total_free_value FROM tbl_order_special_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(order_date)='$freeSlab->date'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,catid,total_free_qty,total_free_value FROM tbl_order_special_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(order_date)='$freeSlab->date'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,catid,total_free_qty,total_free_value FROM tbl_order_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(order_date)='$freeSlab->date'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,catid,total_free_qty,total_free_value FROM tbl_order_regular_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(order_date)='$freeSlab->date'
                                            ) AS e 
                                            join tbl_product_category on tbl_product_category.id=e.catid
                                            group by e.slab,e.catid ORDER BY e.slab");

                                    @endphp

                                    @foreach($freeQty as $freeQty)

                                        <tr style="font-size: 13px;">
                                            <th colspan="4"></th>
                                            <th>{{ $freeQty->name }}</th>                        
                                            <th>{{ $freeQty->qty }}</th>
                                            <th>{{ $freeQty->value }}</th>
                                        </tr>

                                    @endforeach
                                @php
                                $s++;
                                @endphp                                   
                                @endforeach
                                </tbody>
                                
                            </table>

                                <!---- offer show end -->
                                
                        </div>
                    </div>
                </div>
            </div>