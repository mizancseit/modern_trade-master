
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        DISTRIBUTOR REQUISITION REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
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
                    <h2>DISTRIBUTOR REQUISITION REPORT</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <select id="distID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Distributor </option> 
                                @foreach($resultDist as $points)
                                    <option value="{{ $points->id }}">{{ $points->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="pointsID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Point </option> 
                                @foreach($resultPoint as $points)
                                    <option value="{{ $points->point_id }}">{{ $points->point_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        {{-- <div class="col-sm-3">
                            <select id="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> FO </option> 
                                @foreach($resultFO as $fos)
                                    <option value="{{ $fos->fo_id }}">{{ $fos->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div> --}}

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allDistReqIms()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            About {{ sizeof($resultIms) }} results 
                        </h5>
                    </div>                  
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Distributor</th>
                                        <th>Point</th>
                                        <th>Date & Category</th> 
                                        <th style="text-align: right;">Requisition Qty</th>
                                        <th style="text-align: right;">Requisition Value</th>
                                    </tr>
                                </thead>
                                
                                <tbody>

                                @php
                                $s=1;
                                $totalMemo =0;
                                $totalQty =0;
                                $totalValue =0;
                                @endphp

                                @foreach($resultIms as $orders)
                               
                                    <tr>
                                        <th>{{ $s }}</th>
                                        <th>{{ $orders->display_name }}</th>
                                        <th>{{ $orders->point_name }}</th>
                                        <th colspan="2"> {{ date('d-m-Y',strtotime($orders->sent_date)) }}</th>
                                    </tr>

                                    @php
                                    $categories      = DB::table('distributor_req_details')
                                    ->select(DB::raw("SUM(distributor_req_details.req_qnty) as orderQty"),DB::raw("SUM(distributor_req_details.req_value) as orderValue"),'distributor_req_details.req_id','distributor_req_details.cat_id','tbl_product_category.id','tbl_product_category.name')

                                    ->leftJoin('tbl_product_category', 'tbl_product_category.id', '=', 'distributor_req_details.cat_id')
                                    ->where('distributor_req_details.req_id',$orders->req_id)           
                                    ->groupBy('distributor_req_details.cat_id')           
                                    ->get();

                                    foreach ($categories as $key) {
                                    
                                     $totalQty +=$key->orderQty;
                                     $totalValue +=$key->orderValue;
                                    $s++;
                                    @endphp
                                        <tr>
                                            <th colspan="3"></th>
                                            <th style="text-align: right;">{{ $key->name }}</th>
                                            <th style="text-align: right;">{{ $key->orderQty }}</th>
                                            <th style="text-align: right;">{{ $key->orderValue }}</th>
                                        </tr>
                                    @php
                                    }
                                    @endphp
                                @php
                                $s++;
                                @endphp                                   
                                @endforeach
                                </tbody>
                                
                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align: right;">Total</th>
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ number_format($totalValue,2) }}</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection