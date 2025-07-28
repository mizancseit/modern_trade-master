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
                                        <th>ORDER NO</th>
                                        <th>COLLECT DATE</th>
                                        <th>FO</th>
                                        <th>CUSTOMER</th>
                                        <th>QTY</th>
                                        <th>VALUE</th>
                                        <th>STATUS</th>
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
                                            <a href="{{ URL('/eshop-orderDelivery-edit/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" target="_blank">
                                                {{ $orders->order_no }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->first_name }}</th>                        
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>                        
                                        <th style="text-align: right;">{{ $orders->total_order_qty }}</th>                        
                                        <th style="text-align: right;">{{ number_format($orders->total_order_value,2) }}</th> 

                                        <th>
                                 
                                                <input name="ordack<?=$orders->order_id?>" type="radio" id="radio_yes<?=$serial?>" class="radio-col-red" value="YES">
                                                <label for="radio_yes<?=$serial?>"> YES </label>
                                                
                                                <input name="ordack<?=$orders->order_id?>" type="radio" id="radio_no<?=$serial?>" class="radio-col-red" value="NO">
                                                <label for="radio_no<?=$serial?>"> NO </label>
                                                
                                                
                                                <input type="hidden" name="reqid[]" value="<?=$orders->order_id?>"  />
                                                
                                                <input type="hidden" name="customer_id<?=$orders->order_id?>" value="<?=$orders->customer_id?>"  />
                                               
                                                <input type="hidden" name="trans_amount<?=$orders->order_id?>" value="<?=$orders->total_order_value?>"  />
                                                
                                          
                                               
                                            </th>
                                       
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th colspan="5" style="text-align: right;">Grand Total : </th>                        
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                        <th></th>
                                    </tr>
                                     <tr>
                                        <th colspan="8" >
                                            <div class="col-sm-12" align="center">
                                                <input  type="submit" name="ORDER_ACKNOWLEDGE" value="ACKNOWLEDGE" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
                                            </div>  
                                        </th>
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