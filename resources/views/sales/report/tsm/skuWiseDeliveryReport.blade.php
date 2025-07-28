@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PG WISE REPORT
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
                    <h2>PG WISE REPORT</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select From Date" readonly="">
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

                        <div class="col-sm-2">
                            <select name="channel" id="channel" class="form-control show-tick" data-live-search="true">
                               <option value="{{ $channelName->business_type_id }}" selected="">{{ $channelName->business_type }}</option>
                            </select>
                        </div>

                        <div class="col-sm-3">
                            <select id="divistion" name="divistion" class="form-control show-tick" onchange="tsmDivisionWisePoints()">
                                <option value=""> Select Division </option>
                                @foreach($divisionName as $divisions)
                                    <option value="{{ $divisions->iDivId }}">{{ $divisions->div_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-3" id="foPoints">
                            <select id="pointID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Select Point </option>
                            </select>
                        </div>                       
                    </div>                  


                    <div class="row">
                        <div class="col-sm-2" id="foDiv">
                            <select id="foID" name="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> Select FO </option>             
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select id="category" class="form-control show-tick" onchange="getRegularProduct(this.value)">
                                <option value="">------ Category ------</option> 
                                @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div id="product">
                                <select id="productList" class="form-control show-tick">
                                    <option value="">------ Product ------</option>   
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="pgWiseReport()">Search</button>
                        </div>
                        <div class="col-sm-2">
                            <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                        
                    </div>                                
                </div>




                

            </div>
            <div id="showHiddenDiv">
                
            </div>
        </div>
    </div>
</section>

@endsection