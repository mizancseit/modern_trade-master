@if($serial==1)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Total Requisition</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                {{-- <th style="text-align: center;">Print</th> --}}
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($distributorOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($distributorOrderList as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_no }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_date }}</th>
                                <th style="font-weight: normal;">{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_qty }}</th>                        
                                <th style="font-weight: normal;">{{ number_format($orders->total_value,2) }}</th>
                                {{-- <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th> --}}
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="6">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serial==2)

<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Today Requisition</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                {{-- <th style="text-align: center;">Print</th> --}}
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($distributorTodayOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($distributorTodayOrderList as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_no }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_date }}</th>
                                <th style="font-weight: normal;">{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_qty }}</th>                        
                                <th style="font-weight: normal;">{{ number_format($orders->total_value,2) }}</th>
                                {{-- <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th> --}}
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="6">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

    @elseif($serial==3)

<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Delivery Pending</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                {{-- <th style="text-align: center;">Print</th> --}}
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($distributorDeliveryPending) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($distributorDeliveryPending as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_no }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_date }}</th>
                                <th style="font-weight: normal;">{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_qty }}</th>                        
                                <th style="font-weight: normal;">{{ number_format($orders->total_value,2) }}</th>
                                {{-- <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th> --}}
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="6">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

    @elseif($serial==4)

<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Total Delivered</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                {{-- <th style="text-align: center;">Print</th> --}}
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($distributorDelivery) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($distributorDelivery as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_no }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_date }}</th>
                                <th style="font-weight: normal;">{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_qty }}</th>                        
                                <th style="font-weight: normal;">{{ number_format($orders->total_value,2) }}</th>
                                {{-- <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th> --}}
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="6">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@endif



