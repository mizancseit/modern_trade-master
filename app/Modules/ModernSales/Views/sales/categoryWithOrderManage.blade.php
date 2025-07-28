@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            
            <div class="block-header">
                <div class="row">
                    
                    <div class="col-lg-10">
                        <h2 style="padding-bottom: 10px;">
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Sales Order </a> / New Order
                            </small>
                        </h2>
                    </div>
                    
                    <div class="col-lg-2">
                        @if(sizeof($resultCart)>0)
                        <a href="{{ URL('/mts-bucket-manage/'.$orderid.'/'.$customer_id.'/'.$partyid) }}" style="text-decoration: none;" title="Click To Bucket Details"><button class="btn btn-success btn-rounded btn-md"><i class="material-icons">shopping_cart</i> Cart <span class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif" data-speed="1000" data-fresh-interval="20"> @if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif </span></button> </a>
                        @else
                        <button class="btn btn-success btn-rounded btn-md"><i class="material-icons">shopping_cart</i> Cart <span class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif" data-speed="1000" data-fresh-interval="20"> @if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif </span></button>
                        @endif
                    </div>
                    
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif  

            @if(Session::has('failed'))
                <div class="alert alert-danger">
                {{ Session::get('failed') }}                        
                </div>
            @endif              
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultParty)>0) {{ $resultParty->name }} @else STORE @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allOrderProducts()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->name }}</option>
                                    @endforeach                           
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/mts-manage-add-to-cart') }}" method="POST">
                {{ csrf_field() }}  
                <input type="hidden" id="customer_id" name="customer_id" value="{{ $customer_id }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $partyid }}">
                <input type="hidden" id="cat_id" name="cat_id" value="">
                <input type="hidden" id="order_id" name="order_id" value="{{ $orderid }}">

                <div id="showHiddenDiv"> 
                </div>
            </form> 

        </div>
    </section>
@endsection
