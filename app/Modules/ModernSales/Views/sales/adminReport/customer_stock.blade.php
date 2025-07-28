@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Stock 
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
                    <h2>CUSTOMER STOCK REPORT </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        
                        <div class="col-sm-2">
                          <div class="input-group">
                            <div class="form-line">
                              <input type="text" name="fromdate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="input-group">
                            <div class="form-line">
                              <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                            </div>
                          </div>
                        </div>
                         <div class="col-sm-3" id="officerDiv">
                                <select id="fos" name="fos" class="form-control show-tick" data-live-search="true" onchange="allCustomer(this.value)">
                                   <option value="">-- Select Officer--</option> 
                                      @foreach($officerlist as $row)
                                          <option value="{{ $row->id }}">{{ $row->email.' : '.$row->display_name }}</option>
                                      @endforeach 
                                                                                  
                                </select>
                            </div> 
                            <div class="col-sm-3" id="customerDiv">
                                <select id="customer_id" class="form-control show-tick" data-live-search="true" required="">
                                    <option value="">-- Select Customer--</option> 
                                                                                       
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="customer_stock_list()">Search</button>
                            </div>
                        </div>
                        <div class="row">  
                        <div class="col-sm-2">                        
                           <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">

                <div class="card">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>CUSTOMER</th>
                                        <th>STOCK</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultcus) > 0)   
                                    @php
                                    $serial =1;
                                    $closing_balance  = 0;

                                    @endphp

                                    @foreach($resultcus as $cus)          
                                    @php 
                                    $ledger_data = DB::table('mts_outlet_ledger')
                                    ->where('customer_id', $cus->customer_id)
                                    ->orderBy('ledger_id','DESC')                    
                                    ->first(); 
                                    if(sizeof($ledger_data) > 0): 
                                    $closing_balance +=$ledger_data->closing_balance;   
                                    @endphp
                                    <tr>
                                        <th>{{$cus->name }}</th>     
                                        <th>{{ number_format($ledger_data->closing_balance, 2) }}</th>               
                                    </tr>
                                    @endif 
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach

                                    <tr>
                                        <th style="text-align: right;">TOTAL BALANCE : </th>                        
                                        <th>{{ number_format($closing_balance,2) }}</th>
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
</section>

@endsection