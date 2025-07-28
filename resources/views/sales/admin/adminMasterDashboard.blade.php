@extends('sales.masterPage')

@section('content')

<section class="content">

@if ( Auth::user()->id != '1032') 

    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Division</div>
                            <div class="number">{{$activeDivision}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Territory</div>
                            <div class="number">{{$activeTerritory}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Point</div>
                            <div class="number">{{$activePoint}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Route</div>
                            <div class="number">{{$activeRoute}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Active Users FO</div>
                            <div class="number">{{$activeUsers}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Inactive Users FO</div>
                            <div class="number">{{$inactiveUsers}}</div>
                        </div>
                    </div>
                </div>

                 <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Active Users Distributor</div>
                            <div class="number">{{$activeUsersDist}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Products</div>
                            <div class="number">{{$activeProducts}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Active Offers</div>
                            <div class="number">{{$activeOffers}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Yesterday Total Sales</div>
                            <div class="number">@foreach($yesterdayTotalSales as $totsales){{$totsales->delval}}@endforeach</div>
                        </div>
                    </div>
            </div>  

             <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Current Month Total Sales</div>
                            <div class="number">@foreach($thisMonthTotalSales as $totMonthsales){{$totMonthsales->totmonthSales}}@endforeach</div>
                        </div>
                    </div>
                </div>
                   <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="body bg-pink">

                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                               <li>
                                  
                                    <a href="{{url('/report/fo/topten') }}"  style="color: #fff;"><span class="pull-right"><b>
                                       

                                    </b></span>TOP 10 FO LIST</a>
                                </li>
                             
                            </ul>
                        </div>
                    </div>
                </div>
                 <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Top 10 Distributor List</div>
                            <div class="number"></div>
                        </div>
                    </div>
                </div> 
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Order Collection</div>
                            <div class="number">@foreach($totalcollection as $totcollection){{$totcollection->totcollect}}@endforeach</div>
                        </div>
                    </div>
                </div>

               <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Today's total sales</div>
                            <div class="number"></div>
                        </div>
                    </div>
                </div> -->     
    </div>
	
@endif	
</section>

@endsection