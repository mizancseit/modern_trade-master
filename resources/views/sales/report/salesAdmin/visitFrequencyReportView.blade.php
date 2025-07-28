@extends('sales.masterPage') 
@section('content')
<section class="content" id="contentReplace">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        VISIT FREQUENCY REPORT
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / VISIT FREQUENCY REPORT
                        </small>
                    </h2>
                </div>
                <div class="col-lg-3" style="text-align: right;">
                    <h2>                        
                        <small> 
                            <a href="JavaScript:void()" onclick="window.history.go(-1); return false;"> << BACK PREVIEW PAGE </a> | 
                            <a href="JavaScript:void()" onclick="activeMeu()" id="onlyMenu"> MENU SHOW </a>
                            <input type="hidden" id="onlyMenuValue" value="0">
                        </small>
                    </h2>
                </div>
            </div>
        </div>

        @if(Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}                        
        </div>
        @endif

        <div class="row clearfix">
            <div class="card">
                <div class="header">
                    <h2>
                        FILTER
                    </h2>
                </div>

                <div class="body">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-sm-4"> 
                                <select class="form-control show-tick" name="year" id="year" required="">
                                    <option value="">Select Year</option>
                                    @php
                                    $y= date('Y');
                                    for($i=2018;$i<=$y;$i++)
                                    {
                                    @endphp
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @php
                                    }
                                    @endphp
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="month" id="month" required="">
                                    <option value="">Select Month</option>
                                    @foreach($MonthList as $rowMonthKey => $rowMonth)
                                    <option value="{{ $rowMonthKey }}">{{ $rowMonth }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-2">
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="allVisitFrequencyReport()">Search</button>
                            </div>

                        </div>  

                        <div class="row">   
                            <div class="col-sm-4">
                                <select class="form-control show-tick" name="point" id="point" onchange="getRouteList(this.value)">
                                    <option value="">Select Point</option>
                                    @foreach($pointList as $rowPoint)
                                    <option value="{{ $rowPoint->point_id }}">{{ $rowPoint->point_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-4" id="div_route">
                                <select class="form-control show-tick" name="route" id="route">
                                    <option value="">Select Route</option>
                                </select>
                            </div>

                            <!-- <div class="col-sm-4" id="div_ratailer">
                                <select class="form-control show-tick" name="ratailer_id">
                                    <option value="">Select Retailer</option>
                                </select>
                            </div> -->

                            <div class="col-sm-2">
                                 <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                            </div>

                           

                        </div>
                    </div> 
                </form> 
            </div>  

            <div id="showHiddenDiv"> 

            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
    @endsection
