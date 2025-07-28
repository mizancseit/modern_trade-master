@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 style="padding-top: 30px;">
                            EDIT ORDER
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/order-manage') }}"> Order Manage </a> / Edit Order
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-3">
                        
                            <div class="info-box-2 bg-red">
                                <div class="icon">
                                    <i class="material-icons">shopping_cart</i>
                                </div>
                                <a href="{{ URL('/bucket-edit/'.Request::segment(2).'/'.$pointID.'/'.$routeid.'/'.$retailderid.'/'.Request::segment(5)) }}" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text">BUCKET AMOUNT</div>
                                    <div class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart[0]->grand_total_value }} @else 0 @endif" data-speed="1000" data-fresh-interval="20">@if(sizeof($resultCart)>0) {{ $resultCart[0]->grand_total_value }} @else 0 @endif</div>
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
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultRetailer)>0) {{ $resultRetailer->name }} @else STORE @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allOrderManageProducts()" data-live-search="true">
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

            <form action="{{ URL('/add-to-edit-cart') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="distributor_id" name="distributor_id" value="{{ $distributorID }}">
                <input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
                <input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $retailderid }}">
                <input type="hidden" id="order_id" name="order_id" value="{{ Request::segment(2) }}">
                <input type="hidden" id="order_det_status" name="order_det_status" value="@if(sizeof($resultCart)>0) {{ $resultCart[0]->order_det_status }} @else '' @endif">

                <div id="showHiddenDiv">                        
                    {{-- Here Product List --}}
                </div>
            </form> 

        </div>
    </section>
@endsection