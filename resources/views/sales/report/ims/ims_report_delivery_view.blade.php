<div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultIms) }} results 
                        </h5>
                    </div>                  
                    <div class="body">
                        <div class="table-responsive">
                        @if(sizeof($resultIms)>0)
                           
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Group</th>
                                        <th style="text-align: right;">D.Qty</th>
                                        <th style="text-align: right;">D.Value</th>
                                       {{--  <th style="text-align: right;">Offer Qty</th>
                                        <th style="text-align: right;">Offer Value</th> --}}
                                        <!-- <th style="text-align: right;">Order Qty</th>
                                        <th style="text-align: right;">Order Value</th> -->
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

                                $pDate = date('Y-m-d',strtotime('-2 Day'));
                               



                                @endphp    
                                    <tr style="font-size: 13px;">
                                        <td>{{ $s }}</td>
                                       <!-- <td> {{ date('d-m-Y',strtotime($orders->update_date)) }}</td> -->
                                        <td> {{ date('d-m-Y',strtotime($orders->delivered_date)) }}</td>
                                        <td>{{ $orders->point_name }}</td>                        
                                        <td>{{ $orders->display_name }}</td>
                                        <td>{{ $orders->name }}</td>
                                        <td style="text-align: right;">{{ $orders->deliveryQty }}</td>
                                        <td style="text-align: right;">{{ $orders->deliveryValue }}</td>
                                        <td style="text-align: right;">{{ $orders->totalMemo }}</td>
                                    </tr>
                                @php
                                $s++;
                                @endphp                                   
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right;">Total</th>                                    
                                        <th style="text-align: right;">{{ $totalDQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalDValue,2) }}</th>
                                        {{-- <th style="text-align: right;"><!-- {{ $totalOfferQty3 }} --></th>
                                        <th style="text-align: right;"><!-- {{ number_format($totalOfferValue3,2) }} --></th> --}}
                                        <!-- <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                        <th style="text-align: right;">{{ $totalMemo }}</th>  -->
										<th></th>   
										
                                    </tr>
                                </tfoot>
                            </table>

                            <!---- offer show start -->
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th colspan="9" bgcolor="#F4E7EA"><b>Exclusive Offer</b></th>
                                    </tr>
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Slab</th>
                                        <th>Group</th>
                                        <th>Slab Qty</th>
                                        <th>Free Qty</th>
                                        <th>Free Value</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                               @php 
                               $i=1;
                               $totalFreeQty = 0;
                               $totalFreeValue = 0;
                               @endphp
                                @foreach($freeSlab as $freeSlab)
                                
                                    <tr style="font-size: 13px;">
                                        <th>{{$i++}}</th>
                                        <th> {{ date('d-m-Y',strtotime($freeSlab->deliverydate)) }}</th>
                                        <th>{{ $freeSlab->point_name }}</th>                        
                                        <th>{{ $freeSlab->display_name }}</th>
                                        <th colspan="5">{{ $freeSlab->slab }}</th>
                                    </tr>

                                    @php

                                    $freeQty =  DB::select("SELECT date(e.order_date) AS date,e.fo_id,e.slab,sum(e.slab_count) AS slab_qty,e.catid,tbl_product_category.name,sum(e.total_free_qty) AS qty,sum(e.free_value) AS value,date(e.delivery_date) AS deliverydate FROM (
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,free_value,delivery_date FROM tbl_order_special_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,free_value,delivery_date FROM tbl_order_special_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,free_value,delivery_date FROM tbl_order_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            UNION ALL
                                            SELECT order_date,fo_id,slab,slab_count,catid,total_free_qty,free_value,delivery_date FROM tbl_order_regular_and_free_qty WHERE slab!='' AND slab=$freeSlab->slab AND fo_id=$freeSlab->id AND date(delivery_date)='$freeSlab->deliverydate'
                                            ) AS e 
                                            join tbl_product_category on tbl_product_category.id=e.catid
                                            group by e.slab,e.catid ORDER BY e.slab");

                                    @endphp

                                    @foreach($freeQty as $freeQty)

                                        <tr style="font-size: 13px;">
                                            <td colspan="5"></td>
                                            <td>{{ $freeQty->name }}</td>
                                            <td>{{ $freeQty->slab_qty }}</td>                        
                                            <td>{{ $freeQty->qty }}</td>
                                            <td>{{ $freeQty->value }}</td>
                                        </tr>

                                    @endforeach

                                @php
                                $s++;

                                $totalFreeQty += $freeQty->qty;
                                $totalFreeValue += $freeQty->value;
                                @endphp                                   
                                @endforeach
                                 <tr>
                                            <th colspan="7"  style="text-align: right;">Total</th>
                                            <th>{{ $totalFreeQty }}</th>
                                            <th>{{ number_format($totalFreeValue,2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="9" bgcolor="#F4E7EA"><b>Commission Offer</b></th>
                                        </tr>
                                <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Point</th>                        
                                        <th>FO</th>
                                        <th>Commission</th>
                                        <th>Value</th>
                                        <th>Group</th>
                                        <th>Free Qty</th>
                                        <th>Free Value</th>
                                    </tr>
                                    @php

                                    $specialFreeQty = 0;
                                    $specialFreeValue = 0;

                                    @endphp

                                @foreach($specialCommiQty as $specialCommiQty)
                                
                                    <tr style="font-size: 13px;">
                                        <th>{{$i++}}</th>
                                        <th> {{ date('d-m-Y',strtotime($specialCommiQty->update_date)) }}</th>
                                        <th>{{ $specialCommiQty->point_name }}</th>                        
                                        <th>{{ $specialCommiQty->display_name }}</th>
                                        <th>{{ $specialCommiQty->commission }} %</th>
                                        <th  colspan="4">{{ ($specialCommiQty->freevalue * $specialCommiQty->commission)/100 }}</th>
                                        
                                    </tr>

                                    @php

                                    $deliDate =  date('Y-m-d',strtotime($specialCommiQty->update_date));

                                       $valueWiseGift  = DB::table('tbl_order_special_free_qty AS fq')
                                                    ->select('fq.order_id','fq.catid','fq.product_id','fq.total_free_qty','fq.free_value','fq.total_free_value','fq.free_delivery_qty','fq.free_id','fq.offer_id','tbl_product.id','tbl_product.name AS proname','tbl_product.mrp','tbl_product.depo')
                                                    ->join('tbl_product', 'tbl_product.id', '=', 'fq.product_id')
                                                    ->where('fq.status','3')      
                                                    ->where('fq.offer_id',$specialCommiQty->offer_id)
                                                    ->where('fq.fo_id', $specialCommiQty->fo_id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(fq.delivery_date,'%Y-%m-%d'))"), array($deliDate, $deliDate))
                                                    ->get();
                                    @endphp

                                    @foreach($valueWiseGift as $valueWiseGift)

                                        <tr style="font-size: 13px;">
                                            <td colspan="6"></td>
                                            <td>{{ $valueWiseGift->proname }}</td>
                                            <td>{{ $valueWiseGift->total_free_qty }}</td>
                                            <td>{{ number_format($valueWiseGift->total_free_qty * $valueWiseGift->depo,2)  }}</td>
                                        </tr>
                                @php
                               
                                $specialFreeQty += $valueWiseGift->total_free_qty;
                                $specialFreeValue += $valueWiseGift->total_free_qty * $valueWiseGift->depo;
                                @endphp
                                @endforeach

                                 @endforeach

                                 <tr>
                                            <th colspan="7"  style="text-align: right;">Total</th>
                                            <th>{{ $specialFreeQty }}</th>
                                            <th>{{ number_format($specialFreeValue,2) }}</th>
                                        </tr>
                                 
                                </tbody>
                                
                            </table>
                        @else
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>Data no available</th>
                                        </tr>
                                    </thead>
                            </table>
                        @endif
                           

                                <!---- offer show end -->
                        </div>
                    </div>
                </div>