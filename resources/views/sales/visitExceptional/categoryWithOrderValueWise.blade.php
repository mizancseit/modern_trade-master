@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div @if($prevoiusBalanceCommission->reminding_commission_balance >0) class="col-lg-3" @else class="col-lg-5" @endif>
                        <h2 style="padding-top: 30px;">
                            COMMISSION NEW ORDER
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / New Order
                            </small>
                        </h2>
                    </div>

                    <div class="col-lg-3">                        
                        <div class="info-box-2 bg-red">
                            <div class="icon">
                                <i class="material-icons">shopping_cart</i>
                            </div>
                            @php
                            //echo $pagestatus.'Masud';
                            if($pagestatus==1)
                            {
                                $page = 'bucket-offer-exception/'.$pointID.'/'.$routeid.'/'.$retailderid.'/'.Request::segment(9).'/'.Request::segment(10);
                            }
                            else if($pagestatus==2)
                            {
                                $page = 'bucket-edit/'.$resultCart->order_id.'/'.$pointID.'/'.$routeid.'/'.$retailderid.'/'.Request::segment(5);
                            }
                            else if($pagestatus==3)
                            {
                                $page = 'order-edit/'.$resultCart->order_id.'/'.$resultCart->fo_id.'/'.Request::segment(9);
                            }
                            @endphp

                            <a href="{{ URL($page) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text">BUCKET AMOUNT</div>
                                    <div class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart->grand_total_value }} @else 0 @endif" data-speed="1000" data-fresh-interval="20">@if(sizeof($resultCart)>0) {{ $resultCart->grand_total_value }} @else 0 @endif</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    @if($prevoiusBalanceCommission->reminding_commission_balance >0)
                    <div class="col-lg-2">                        
                        <div class="info-box-2 bg-indigo">                            
                            <a href="{{ URL($page) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text"  style="text-align: center;">PRE  COMMISSION</div>
                                    <div class="number count-to" data-from="0" data-to="{{ Request::segment(5) }}" data-speed="1000" data-fresh-interval="20"> </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-lg-2">                        
                        <div class="info-box-2 bg-indigo">                            
                            <a href="{{ URL($page) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text"  style="text-align: center;">COMMISSION</div>
                                    <div class="number count-to" data-from="0" data-to="{{ Request::segment(5) }}" data-speed="1000" data-fresh-interval="20"> </div>
                                </div>
                            </a>
                        </div>
                    </div>                    

                    <div class="col-lg-2">                        
                        <div class="info-box-2 bg-red">                            
                            <a href="{{ URL($page) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text" style="text-align: center;">TOTAL COMM</div>
                                    <div class="number count-to" data-from="0" data-to="{{ $valueSum }}" data-speed="1000" data-fresh-interval="20"> </div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif 

            @if(Session::has('warning'))
                <div class="alert alert-warning">
                {{ Session::get('warning') }}                        
                </div>
            @endif           
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultRetailer)>0) {{ $resultRetailer->name }} @else STORE @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allProducts()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->name }}</option>
                                    @endforeach                           
                                </select>
                                {{-- <select class="form-control show-tick">
                                    <option value="">-- Please select subcategory--</option>             
                                </select> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/add-to-cart-value-wise-exception') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="distributor_id" name="distributor_id" value="{{ $distributorID }}">
                <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                <input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $retailderid }}">
                <input type="hidden" id="free_amount" name="free_amount" value="{{ Request::segment(5) }}">
                <input type="hidden" id="orderid" name="orderid" value="{{ Request::segment(4) }}">
                <input type="hidden" id="reference_catid" name="reference_catid" value="{{ Request::segment(6) }}">
                <input type="hidden" id="reference_offerid" name="reference_offerid" value="{{ Request::segment(7) }}">

                <input type="hidden" id="reference_pagestatus" name="reference_pagestatus" value="{{ Request::segment(8) }}">

                <input type="hidden" id="partialOrder" name="partialOrder" value="{{ Request::segment(9) }}">
                <input type="hidden" id="offerType" name="offerType" value="{{ Request::segment(10) }}">

                <div id="showHiddenDiv">                        
                    {{-- Here Product List --}}
                </div>
            </form> 

        </div>
    </section>
@endsection