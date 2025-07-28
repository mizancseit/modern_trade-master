@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        FO ORDER REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / FO Order Report
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
                    <h2> FO Order Report</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="" placeholder="Choose Date" readonly="">
                                </div>
                            </div>
                        </div>

                        

                       
                    

                        <div class="col-sm-3">
                            <select id="division" class="form-control show-tick" data-live-search="true">
                                <option value="">--- Division --</option> 
                                @foreach($division as $divisions)
                                    <option value="{{ $divisions->div_id }}">{{ $divisions->div_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                      
                       

                        <div class="col-sm-3">
                            <select id="Point" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Point --</option>             
                            </select>
                        </div>
                        </div>
                         <div class="row">

                        <div class="col-sm-3">
                            <select id="fos" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Fo --</option> 
                                @foreach($resultFO as $fos)
                                    <option value="{{ $fos->user_id }}">{{ $fos->user_id.' : '.$fos->first_name.''.$fos->middle_name.''.$fos->last_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>
                        
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">Search</button>
                        </div>
                    </div>                                
                </div>
            </div>
            <div id="showHiddenDiv">
                <div class="card"  id="printMe">
                    
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="font-size: 11px;">SL</th>
                                        <th style="font-size: 11px;">DIV</th>
                                        <th style="font-size: 11px;">POINT</th>
                                        <th style="font-size: 11px;">FO</th>
                                        <th style="font-size: 11px;">VISIT</th>
                                        <th style="font-size: 11px;">TOTAL MEMO</th>
                                        <th style="font-size: 11px;">TOTAL ORDER VALUE </th>
                                        <th style="font-size: 11px;">AVG PER MEMO</th>
                                       
                                    </tr>
                                
                                    <tr>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"</th>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"></th>
                                        <th style="font-size: 11px;"></th>
                                       
                                    </tr>
                                    
                                </thead>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- For Print --}}
                <div class="card">
                    <div class="row" style="text-align: center; padding: 10px 10px; ">
                        <div class="col-sm-12">
                            <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                <i class="material-icons">print</i>
                                <span>PRINT...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection