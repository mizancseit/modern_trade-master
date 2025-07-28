@extends('sales.masterPage')

@section('content')

<section class="content">
    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">

                    <div class="info-box bg-pink hover-expand-effect">
                        <a href="JavaScript:void()" onclick="dashboardFoOrders(1)" title="Click To Details">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>

                        <div class="content">
                            <div class="text">ORDERS</div>
                            <div class="number count-to" data-from="0" data-to="{{$resultNewOrder}}" data-speed="15" data-fresh-interval="{{$resultNewOrder}}"></div>
                        </div>
                        </a>
                    </div>
                </div>

                {{-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">help</i>
                        </div>
                        <div class="content">
                            <div class="text" style="font-size: 11px;">ATTENDANCE</div>
                            <div class="number count-to" data-from="0" data-to="{{$resultAttendance}}" data-speed="1000" data-fresh-interval="{{$resultAttendance}}"></div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                        <a href="JavaScript:void()" onclick="dashboardFoOrders(2)" title="Click To Details">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">TOTAL RETAILERS</div>
                                <div class="number count-to" data-from="0" data-to="{{$resultRetailer}}" data-speed="1000" data-fresh-interval="20">{{$resultRetailer}}</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <a href="JavaScript:void()" onclick="dashboardFoOrders(2)" title="Click To Details">
                            <div class="icon">
                                <i class="material-icons">forum</i>
                            </div>
                            <div class="content">
                                <div class="text">TOTAL RETAILERS</div>
                                <div class="number count-to" data-from="0" data-to="{{$resultRetailer}}" data-speed="1000" data-fresh-interval="{{$resultRetailer}}"></div>
                            </div>
                        </a>
                    </div>
                </div> -->

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <a href="JavaScript:void()" onclick="dashboardFoOrders(4)" title="Click To Details">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">TODAY VISITS</div>
                                <div class="number count-to" data-from="0" data-to="{{$todayResultVisit}}" data-speed="1000" data-fresh-interval="{{$todayResultVisit}}"></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-green hover-expand-effect">
                        <a href="JavaScript:void()" onclick="dashboardFoOrders(3)" title="Click To Details">
                            <div class="icon">
                                <i class="material-icons">playlist_add_check</i>
                            </div>
                            <div class="content">
                                <div class="text">ATTENDANCE</div>
                                <div class="number count-to" data-from="0" data-to="{{$attendanceSummery}}" data-speed="1000" data-fresh-interval="{{$attendanceSummery}}"></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">                 

                    <div class="card">                       
                        <div class="body bg-teal">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                <li> <b> TODAY </b> </li>
                                <li>
                                    Target
                                    <span class="pull-right"><b>
                                        @if($todayTarget>0)
                                            {{ number_format($todayTarget,0) }}
                                        @else
                                            0
                                        @endif
                                    </b></span>
                                </li>
                                <li>
                                    Achievement
                                    <span class="pull-right"><b>
                                        @if($todayAchivement>0)
                                            {{ number_format($todayAchivement,0) }}
                                        @else
                                            0
                                        @endif

                                    </b></span>
                                </li>
                                <li>
                                    Strike Rate
                                    <span class="pull-right"><b>
                                        @if($todayAchivement>0 && $todayTarget>0)
                                            {{number_format(($todayAchivement * 100)/$todayTarget,0)}} %
                                        @else
                                            0 %
                                        @endif
                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="body bg-teal">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                <li> <b> MONTHLY </b> </li>
                                <li>
                                    Target
                                    <span class="pull-right"><b>
                                        @if($monthlyTarget>0)
                                            {{ number_format($monthlyTarget,0) }}
                                        @else
                                            {{ $monthlyTarget }}
                                        @endif
                                    </b></span>
                                </li>
                                <li>
                                    Achievement
                                    <a href="{{url('/report/fo/delivery/'.$startDate.'/'.$endDate) }}"  style="color: #fff;"><span class="pull-right"><b>
                                        @if($monthlyAchivement>0)
                                            {{ $monthlyAchivement }}
                                        @else
                                            0
                                        @endif

                                    </b></span></a>
                                </li>
                                <li>
                                    Strike Rate
                                    <span class="pull-right"><b>
                                        @if($monthlyAchivement>0 && $monthlyTarget>0)
                                            {{number_format(($monthlyAchivement * 100)/$monthlyTarget,0)}} %
                                        @else
                                            0 %
                                        @endif

                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>                

                <div class="block-header" style="padding-left: 15px;">
                    <h2>CATEGORY WISE TARGET VS ACHIEVEMENT</h2>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="body bg-cyan">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                <li> <b> GLS </b> </li>
                                <li>
                                    Target
                                    <span class="pull-right"><b>
                                        @if($glsTarget>0)
                                            {{ number_format($glsTarget,0) }}
                                        @else
                                            0
                                        @endif
                                    </b></span>
                                </li>
                                <li>
                                    Achievement
                                    <span class="pull-right"><b>
                                        @if($glsAchievement>0)
                                            {{ number_format($glsAchievement,0) }}
                                        @else
                                            0
                                        @endif

                                    </b></span>
                                </li>
                                <li>
                                    Strike Rate
                                    <span class="pull-right"><b>
                                        @if($glsAchievement>0 && $glsTarget>0)
                                            {{number_format(($glsAchievement * 100)/$glsTarget,0)}} %
                                        @else
                                            0 %
                                        @endif
                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="body bg-cyan">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                <li> <b> CFL </b> </li>
                                <li>
                                    Target
                                    <span class="pull-right"><b>
                                        @if($cflTarget>0)
                                            {{ number_format($cflTarget,0) }}
                                        @else
                                            0
                                        @endif
                                    </b></span>
                                </li>
                                <li>
                                    Achievement
                                    <span class="pull-right"><b>
                                        @if($cflAchievement>0)
                                            {{ number_format($cflAchievement,0) }}
                                        @else
                                            0
                                        @endif

                                    </b></span>
                                </li>
                                <li>
                                    Strike Rate
                                    <span class="pull-right"><b>
                                        @if($cflAchievement>0 && $cflTarget>0)
                                            {{number_format(($cflAchievement * 100)/$cflTarget,0)}} %
                                        @else
                                            0 %
                                        @endif
                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="body bg-cyan">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                <li> <b> LED </b> </li>
                                <li>
                                    Target
                                    <span class="pull-right"><b>
                                        @if($ledTarget>0)
                                            {{ number_format($ledTarget,0) }}
                                        @else
                                            0
                                        @endif
                                    </b></span>
                                </li>
                                <li>
                                    Achievement
                                    <span class="pull-right"><b>
                                        @if($ledAchievement>0)
                                            {{ number_format($ledAchievement,0) }}
                                        @else
                                            0
                                        @endif

                                    </b></span>
                                </li>
                                <li>
                                    Strike Rate
                                    <span class="pull-right"><b>
                                        @if($ledAchievement>0 && $ledTarget>0)
                                            {{number_format(($ledAchievement * 100)/$ledTarget,0)}} %
                                        @else
                                            0 %
                                        @endif
                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="card">
                        <div class="body bg-pink">
                            <ul class="dashboard-stat-list" style="margin-top:0px;">
                                
                                <li>
                                    TOTAL TARGET
                                    <span class="pull-right"><b>
                                        @if($totalTarget>0)
                                            {{ $totalTarget }}
                                        @else
                                            0
                                        @endif

                                    </b></span>
                                </li>
                                <li>
                                    TILL DATE ACHIEVEMENT
                                     <span class="pull-right"><b>
                                        @if($totalAchivement>0)
                                            {{ number_format($totalAchivement,0) }}
                                        @else
                                            0
                                        @endif

                                     </b></span>
                                </li>
                                <li>
                                    STRIKE RATE
                                    <span class="pull-right"><b>
                                        @if($totalAchivement>0 && $totalTarget>0)
                                            {{number_format(($totalAchivement * 100)/$totalTarget,0)}} %
                                        @else
                                            0 %
                                        @endif

                                    </b></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div> -->

            </div>
            
            

        
    </div>
</section>

@endsection