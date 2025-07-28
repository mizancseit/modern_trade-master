
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        ORDER LIST
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Order 
                        </small>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success">
        {{ Session::get('success') }}                        
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>ORDER STATUS</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-12">
                                <div class="row clearfix">

                                @if(sizeof($resultPartialOrder)>0)
                                    @php
                                    $orderSerial =0;
                                    $orderPending =0;
                                    @endphp
                                    @foreach($resultPartialOrder as $partialOrder)
                                    @php
                                    $orderSerial ++;

                                    if($partialOrder->order_det_type=='Confirmed')
                                    {
                                        $url = URL('/order-edit/'.$partialOrder->order_id.'/'.$foMainId.'/'.$partialOrder->partial_order_id);
                                    }
                                    else if($partialOrder->order_det_type=='Delivered')
                                    {
                                        $url = URL('/invoice-partial/'.$partialOrder->order_id.'/'.$foMainId.'/'.$partialOrder->partial_order_id);
                                    }
                                    elseif($partialOrder->order_det_type=='Ordered')
                                    {
                                        $url = URL('/order-edit/'.$partialOrder->order_id.'/'.$foMainId.'/'.$partialOrder->partial_order_id);
                                    }
                                    else
                                    {
                                        $url = '';
                                    }
                                    @endphp

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <div @if($partialOrder->order_det_type=='Confirmed' || $partialOrder->order_det_type=='Ordered') class="info-box bg-pink" @elseif($partialOrder->order_det_type=='Delivered') class="info-box-2 bg-teal" @endif>

                                                @if($partialOrder->order_det_status=='Partial' && $totaPartialOrder!=$totaPartialOrderDelivered)

                                                <a href="{{ $url }}" title="Click To Details">

                                                @elseif($partialOrder->order_det_status=='Closed' && $totaPartialOrder==$totaPartialOrderDelivered)

                                                <a href="{{ $url }}" title="Click To Details">
                                                 @elseif($partialOrder->order_det_status=='Partial' && $partialOrder->order_det_type=='Delivered')

                                                <a href="{{ $url }}" title="Click To Details">

                                                @endif

                                                    <div class="content">
                                                        <div class="text">ORDERS - {{ $orderSerial }} @if($partialOrder->order_det_type=='Confirmed') ( Pending ) @elseif($partialOrder->order_det_type=='Delivered') ( Delivered ) @endif 
                                                        @if($partialOrder->order_det_status=='Closed')( Order Closed )@endif
                                                        </div>
                                                        <div class="number count-to" data-from="{{ $partialOrder->orderPartialTotal }}" data-to="{{ $partialOrder->orderPartialTotal }}" data-speed="15" data-fresh-interval="{{ $partialOrder->orderPartialTotal }}">{{ $partialOrder->orderPartialTotal }}</div>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>
                                    
                                    @endforeach
                                @else
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        Sorry, no order now.
                                    </div>
                                    
                                @endif
                                </div>
                        </div>
                    </div>                                  
                </div>
            </div>
        </div>
    </div>
</section>

@endsection