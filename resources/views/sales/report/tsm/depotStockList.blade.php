
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        STOCK REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Depot / Stock 
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
                <div class="body">
                    <div class="row">

                    <form id="form_validation" method="POST">

                        <div class="col-sm-2">
                            <select id="channel" class="form-control show-tick" data-live-search="true">
                               <option value="{{ $channelName->business_type_id }}" selected="">{{ $channelName->business_type }}</option>
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <select id="divistion" class="form-control show-tick" onchange="tsmDivisionWisePoints()">
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
                        <div class="col-sm-3">
                            <select id="categories" class="form-control show-tick" data-live-search="true">
                                <option value="">-- Please select category --</option>
    							<option value="all"> All Stock </option>
                                @foreach($resultCategory as $categories)
                                <option value="{{ $categories->id }}">{{ $categories->g_code.' : '.$categories->name }}</option>
                                @endforeach                           
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="tsmStockProducts()">Search</button>
                        </div>

                        <div class="col-sm-8"></div>
                        <div class="col-sm-2">
                            <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
           

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            Products Stock 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                               
                                
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection