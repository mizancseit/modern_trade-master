@extends('sales.masterPage')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>DASHBOARD</h2>
        </div>

        <div class="row clearfix">
            <!-- Radar Chart -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="display: none;">
                <div class="card">
                    <div class="header">
                        <h2>RADAR CHART</h2>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="body"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                        <canvas id="radar_chart" height="716" width="1432" style="display: block; width: 716px; height: 358px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- #END# Radar Chart -->

            <!-- Pie Chart -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>{{ date('Y')}} - Yearly Target Vs Achievement</h2>                            
                    </div>
                    <div class="body">
                        <iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;">

                        </iframe>
                        <canvas {{-- onclick="dashboardManagement(2)" --}} id="pie_chart" height="716" width="1432" style="display: block; width: 716px; height: 358px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- #END# Pie Chart -->

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2> {{ date('Y')}} - Month Wise Sale</h2>                            
                    </div>
                    <div class="body"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                        <canvas id="bar_chart" height="716" width="1432" style="display: block; width: 716px; height: 358px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="block-header">
            <h2>MONTHLY</h2>
        </div>
        <div class="row clearfix">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(2)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Target Vs Achievement</div>
                            <div class="number"> {{ number_format($yearlyTarget,0).'/'.number_format($currentMonthAchivement,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(8)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Target</div>
                            <div class="number"> {{ number_format($currentMonthTarget,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(6)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Credit</div>
                            <div class="number"> {{ number_format((($retailersOpeningBalance+$currentMonthAchivement)- $totalcollection),0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(4)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Collection</div>
                            <div class="number"> {{ number_format($totalcollection,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(7)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Till date total sales</div>
                            <div class="number"> {{ number_format($currentMonthAchivement,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(5)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Yesterday Sales</div>
                            <div class="number"> {{ number_format($yesterdayTotalSales,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>            
        </div>

        <div class="row clearfix">
            <!-- Line Chart -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="display: none;">
                <div class="card">
                    <div class="header">
                        <h2>LINE CHART</h2>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                    <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="body"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
                        <canvas id="line_chart" height="716" width="1432" style="display: block; width: 716px; height: 358px;"></canvas>
                    </div>
                </div>
            </div>
            <!-- #END# Line Chart -->
            <!-- Bar Chart -->

            <!-- #END# Bar Chart -->
        </div>

        <div class="block-header">
            <h2>DAILY</h2>
        </div>
        <div class="row clearfix">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(2)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Target Vs Achievement</div>
                            <div class="number"> {{ number_format(($currentMonthTarget/26),0).'/'.number_format($todayAchivement,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(8)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Target</div>
                            <div class="number"> {{ number_format(($currentMonthTarget/26),0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(6)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Credit</div>
                            <div class="number"> {{ number_format((($retailersOpeningBalance+$todayAchivement)- $todayCollection),0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(4)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Collection</div>
                            <div class="number"> {{ number_format($todayCollection,0) }} TK</div>
                        </div>                    
                    </div>
                </a>
            </div>
        </div>

        <div class="block-header">
            <h2>TOP</h2>
        </div>
        <div class="row clearfix">
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(1)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Top 10 Distributor</div>
                            <div class="number"> 10</div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(3)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Top 10 Fo</div>
                            <div class="number"> 10 </div>
                        </div>                    
                    </div>
                </a>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <a href="JavaScript:void()" {{-- onclick="dashboardManagement(3)" --}} title="Click To Details" style="cursor: pointer; text-decoration: none;">
                    <div class="info-box-4 hover-expand-effect">                    
                        <div class="content">                        
                            <div class="text">Top 10 Products</div>
                            <div class="number"> 10 </div>
                        </div>                    
                    </div>
                </a>
            </div>


        </div>
    </div>   
</section>

@endsection