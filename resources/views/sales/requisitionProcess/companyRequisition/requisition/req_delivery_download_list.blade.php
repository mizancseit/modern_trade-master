@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION DETAILS
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Requisition Deatils </a>
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

                @if(sizeof($resultReqPro)>0)
                <div class="header">
                    <h2>ALL REQUISITION PRODUCT</h2>                            
                </div>
                <div class="body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2">Distributor Name: &nbsp;{{ $resultReqPro[0]->distributor_in_charge }}</th>                  
                                <th colspan="2">Point Name: &nbsp;{{ $resultReqPro[0]->point_name }}</th>                  
                                <th colspan="2">Status: &nbsp;{{ $resultReqPro[0]->req_status }}</th>                  
                            </tr>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Product Name</th>
                                <th>Req Order Qty</th>
                                <th>Req Order Value</th>
                                <th>Req Wastage Qty</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                            $serial   = 1;
                            $count    = 1;
                            $subTotal = 0;
                            $totalQty = 0;
                            $totalWastage = 0;
                            @endphp
                            @foreach($resultReqPro as $items) 
                            @php                            
                            $subTotal += $items->req_value;
                            $totalQty += $items->req_qnty;
                            $totalWastage += $items->wastage_qty;
                            @endphp

                            <tr>
                                <th>{{ $serial }}</th>
                                <th>{{ $items->catname }}</th>
                                <th>{{ $items->proname }}</th>
                                <th style="text-align: right;">{{ $items->req_qnty }}</th>
                                <th style="text-align: right;">{{ $items->req_value }}</th>
                                <th style="text-align: right;">{{ $items->wastage_qty }}</th>
                            </tr>
                            @php
                            $serial ++;
                            @endphp

                        @endforeach

                        <tr>
                            <th style="text-align: right;" colspan="3">Total</th>                            
                            <th style="text-align: right;">{{ $totalQty }}</th>
                            <th style="text-align: right;">{{ $subTotal }}</th>
                            <th style="text-align: right;">{{ $totalWastage }}</th>
                        </tr>

                        <tr>
                            <th colspan="10">
                                <div class="col-sm-12" align="center">
                                    <form action="{{url('dist/export')}}" enctype="multipart/form-data">
                                        <input type="submit" name="download" value="DOWNLOAD SALES ORDER" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">

                                        <input type="hidden" name="requisition_id" value="{{ $reqid }}">
                                    </form>

                        </div>	
                        </th>
                        </tr>

                        </tbody>
                        <tfoot>
                        </table>
<p></p>

<input type="hidden" name="req_id" id="req_id" value="">

</div>
@else

<div class="header">
    <h2>Product EMPTY</h2>                            
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="7"></th>                                                         
        </tr>
    </thead>
    <tbody>                                        
        <tr>
            <th colspan="7" style="color: #000; text-align: center;" align="center">
                <h4>YOUR Requisition PRODUCT IS EMPTY.</h4> <p></p><p></p>

                <div class="col-sm-4" style="margin-right: 40px;"></div>


            </th>                  
        </tr>
        <tr>
            <th colspan="7"></th>                                                         
        </tr>
    </tbody>
</table>

@endif
</div>
</div>
</div>
<!-- #END# Basic Validation -->            
</div>
</section>

@endsection