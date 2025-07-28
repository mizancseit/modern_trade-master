@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
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
<form action="{{ URL('/eshop-reqAcknowledge') }}" method="POST">
    {{ csrf_field() }}    <!-- token -->

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Sales Order </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y')}}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y')}}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <select id="customer_id" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Select Customer --</option> 
                                @foreach($customerResult as $customer)
                                    <option value="{{ $customer->customer_id }}">{{ $customer->sap_code.' : '.$customer->name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allEshopOrder()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
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
                                        <th>CREDIT LIMIT</th>
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
                                            <a href="{{ URL('/eshop-orderDelivery-edit/'.$orders->order_id.'/'.$orders->fo_id) }}" title="Confirm Delivery" >
                                                {{ $orders->order_no }}
                                            </a>
                                        </th>
                                        <th>{{ $orders->order_date }}</th>
                                        <th>{{ $orders->first_name }}</th> 
                                        <th>{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                        <th>{{ $orders->credit_limit }}</th>                                          
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
                                        <th colspan="7">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    </form> 
</section>

@endsection