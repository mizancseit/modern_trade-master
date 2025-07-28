@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / All product
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

<form action="{{ URL('/orderDelivery-open-submit') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">                        

                @if(sizeof($resultCartPro)>0)

                <div class="header">

                </div>

                <div class="header">
                    <div class="row">
                     <div class="col-lg-3">
                        <h2>All Product</h2>  
                    </div>
                    

                </div> 

            </div>

            <div class="body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Customer</th>
                            <th>Party</th>
                            <th>Product Group</th>
                            <th>Product Name</th>
                            <th>Delivery</th>
                            <th>Value</th>
                            <th>R.Delivery</th>
                            <th>R.Value</th>                   
                        </tr>
                    </thead>

                    <tbody>

                        @if(sizeof($resultCartPro)>0)
                        @php
                        $serial   = 1;
                        $count    = 1;
                        $subTotal = 0;
                        $totalQty = 0;
                        $totalPrice = 0;
                        @endphp
                        @foreach($resultCartPro as $items)                                    


                        <tr>
                            <th>{{ $serial }}</th>
                            <th>{{ $items->cus_name }}</th>
                            <th>{{ $items->party_name }}</th>
                            <th>{{ $items->catname }}</th>
                            <th>{{ $items->pname }}</th>
                            <th style="text-align: center;">{{ $items->order_qty }}</th>
                            <th style="text-align: center;">{{ number_format($items->order_total_value,0) }}</th>

                            <input type="hidden" id="change_prod_price{{ $serial }}" value="{{ $items->p_unit_price }}" >
                            <th style="text-align: right;">

                                <input type="number" class="form-control" id="changeQty{{$serial}}" name="qty[]" value="{{ $items->order_qty }}" maxlength="3" style="width: 80px;" onkeyup="addChange({{$serial}})">
                            </th>
                            <th>
                                <input type="text" class="form-control" id="changeValue{{$serial}}" name="price[]" value="{{$items->order_total_value}}" maxlength="8" style="width: 80px;"  readonly="">
                            </th>

                            <input type="hidden" name="product_id[]" id="product_id{{ $serial }}" value="{{ $items->product_id }}">

                            <input type="hidden" name="order_det_id[]" id="order_det_id{{ $serial }}" value="{{ $items->order_det_id }}">

                        </tr>
                        @php
                        $serial ++;
                        @endphp                                           
                    </tr>

                    @endforeach
                    @endif

                </tbody>

            </table>
            <p></p>
            <div class="row" style="text-align: center;">
                <div class="col-sm-3">
                    <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Update</button>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>
</div>

</form>

<!-- #END# Basic Validation -->            
</div>
</section>

<div class="modal fade" id="showBundleProductCon" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div id="showBundleProductContent"></div>
</div>

<div class="modal fade" id="showBundleProductConMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #A62B7F">
                {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                <h4 class="modal-title" id="myModalLabel" >Success</h4>
            </div>
            
            <div class="modal-body" style="text-align: center;">
                {{-- <p><h4>Successfully added offer product</h4></p> --}}
                <p>Successfully added offer product</p>
                <p class="debug-url"></p>
            </div>

            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> --}}
            </div>
        </div>
    </div>
</div>
@endsection