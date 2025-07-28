@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        SKU WISE ORDER REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Category Wise Order 
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
                    <h2>SKU WISE ORDER REPORT</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <select id="fos" class="form-control show-tick" data-live-search="true">
                                <option value="">------ Fo ------</option> 
                                @foreach($resultFO as $fos)
                                    <option value="{{ $fos->user_id }}">{{ $fos->user_id.' : '.$fos->first_name.''.$fos->middle_name.''.$fos->last_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="category" class="form-control show-tick" data-live-search="true" onchange="categoryWiseProduct()">
                                <option value="">------ Category ------</option> 
                                @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->catid }}">{{ $categories->catname }}</option>
                                @endforeach                                                    
                            </select>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-sm-3">
                            <div id="productsall">
                                <select id="products" class="form-control show-tick" data-live-search="true">
                                    <option value="">------ Product ------</option>   
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allSkuWiseOrder()">Search</button>
                        </div>
                    </div>                                
                </div>

                

            </div>
            <div id="showHiddenDiv"></div>
        </div>
    </div>
</section>

@endsection