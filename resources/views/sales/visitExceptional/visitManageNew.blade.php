@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            VISIT MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Visit
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-2">
                        {{-- <div class="preloader">
                            <div class="spinner-layer pl-black">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
                
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            
            <!-- Exportable Table -->
            <div class="row clearfix">
              
                       
                        
                          @foreach($resultOrderList as $orders)
                           <div class="col-lg-3">
                            <div class="info-box-2 bg-red">
                                <div class="icon">
                                    <i class="material-icons">shopping_cart</i>
                                </div>
                                <a href="{{ URL('/invoice-edit-exception/'.$orders->order_id.'/'.$orders->retailer_id.'/'.$orders->route_id.'/'.$orders->partial_order_id.'/'.$orders->offer_type) }}" title="Click To Edit Invoice">
                                <div class="content">
                                    <div class="text">BUCKET AMOUNT</div>
                                    <div class="number count-to" data-from="0" data-to="{{$orders->total_value}}" data-speed="1000" data-fresh-interval="20">{{$orders->total_value}}</div>
                                </div>
                                </a>
                            </div>
                          </div>
                        @endforeach
                        
                      
            </div>
        </div>
    </section>
@endsection