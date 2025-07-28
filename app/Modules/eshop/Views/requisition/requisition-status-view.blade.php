@extends('eshop::masterPage')
@section('content')
<style type="text/css">
    .warning{
        background-color: #ffd90f !important ;
    }
</style>
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Delivery
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

    <form action="{{ URL('/eshop-not-approved-remarks-submit') }}" method="POST">
        {{ csrf_field() }}    <!-- token -->

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">                        

                    @if(sizeof($orders)>0)

                    <div class="header">
                        <div class="row">
                            <div class="col-sm-8">
                                <span> 
                                    PO No &nbsp;&nbsp;&nbsp;&nbsp;: {{ $orders->po_no }}<br />
                                    Customer Code :&nbsp; {{ $orders->customer->sap_code }}<br />

                                    Outlet Name  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;  {{ $orders->party->name }}  
                                    <br />

                                    Address  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; {{ $orders->customer->address }}  <br />
                                     
                                    Shiping Address&nbsp;:&nbsp; {{ $orders->party->address }}
                                     
                                </span>
                            </div>

                            <div class="col-sm-4">
                                @php
                                $user_details = App\Modules\eshop\Controllers\StaticController::whereRow('tbl_user_details','user_id',$orders->fo_id)
                                @endphp
                                <span>
                                    Sales Order No : {{ $orders->order_no }}<br />
                                    Collected By &nbsp; : {{$user_details->first_name}} {{$user_details->last_name}}<br />
                                    Invoice No : {{ $orders->order_no }}<br />
                                    Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ date('d M Y', strtotime($orders->order_date)) }}

                                </span>
                            </div>
                        </div>                                                           
                    </div>
                    @php
                    $eapproval_status = 0; 
                    @endphp
                    <div class="header">
                        <div class="row"> 

                            <div class="body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL  </th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>SAP code</th>
                                            <th>DP Price</th>
                                            <th>Unite Price</th> 
                                            <th>Order Qty</th>
                                            <th>Subtotal</th>   
                                            <th>Item Discount %</th>                  
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @if(sizeof($orders)>0)
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalPrice = 0;
                                        $item_discount = 0;
                                        $totalUnitPrice = 0;
                                        $totaldpPrice = 0;
                                        @endphp
                                        @foreach($orders->orderdetails as $itemsPro)
                                        @php  
                                        $category = App\Modules\eshop\Controllers\EshopRequisitionController::category($itemsPro->cat_id);                                  
                                        @endphp                                        
                                        <tr>
                                            <td></td>
                                            <td colspan="6"> {{ $category->name }}
                                                </td>
                                        </tr>  
                                        @php

                                        $subTotal += $itemsPro->order_total_value;
                                        $totalQty += $itemsPro->order_qty;
                                        $totalPrice += $itemsPro->order_total_value;
                                        $totaldpPrice += $itemsPro->distri;
                                        $totalUnitPrice += $itemsPro->p_unit_price;
                                        if($itemsPro->item_discount > 0){
                                            $item_discount += $itemsPro->order_total_value * $itemsPro->item_discount/100;
                                        }

                                        if( $itemsPro->p_unit_price < $itemsPro->distri ){
                                            $eapproval_status =  1 ;
                                            $warning = 1;
                                        }else{
                                            $warning = 0;
                                        }
                                             
                                        @endphp

                                        <tr  class="{{ $itemsPro->p_unit_price < $itemsPro->distri ? 'alert-amount' : '' }}" >
                                            <td>{{ $serial }}</td>
                                            <td></td>
                                            <td>{{ $itemsPro->proname }}</td>
                                            <td>{{ $itemsPro->sap_code }}</td>
                                            <td >{{ $itemsPro->distri }}</td>
                                            <td <?php if($warning == 1){ ?> class="warning" <?php } ?>>{{ $itemsPro->p_unit_price }}</td>
                                            <td style="text-align: center;">{{ number_format($itemsPro->order_qty,0) }}</td>
                                            <td style="text-align: center;">{{ number_format($itemsPro->order_total_value,2) }}</td>
                                            <td style="text-align: center;">{{ $itemsPro->item_discount }}</td> 
                                        </tr>
                                        @php
                                        $serial ++;
                                        @endphp                                           
                                    </tr> 
                                    @endforeach
                                    @endif

                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total</th>
                                        <th style="text-align: center;">{{ number_format($totaldpPrice,2) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalUnitPrice,2) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalQty,0) }}</th>
                                        <th style="text-align: center;">{{ number_format($totalPrice,2) }}</th>
                                        <th style="text-align: center;"> </th>
                                    </tr>  
                                </tbody>

                            </table> 
                        </div>

                        @endif
                    </div>
                </div>
            </div>

        </form>

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