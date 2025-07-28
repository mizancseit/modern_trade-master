 
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        Fo Wise Report
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

            <div id="showHiddenDiv">
                <div class="card">                
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>FO Name</th>
                                        <th>Monthly Target</th>
                                        <th>Today Target</th>
                                        <th>Monthly Achievement</th>
                                        <th>Monthly Achievement (%)</th>
                                        <th>Today Achievement</th>
                                        <th>Today Achievement (%)</th>
                                        <th>Monthly Required Achievement (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @php
                                        $ftotalMonthlyTarget = 0;
                                        $ftotalTodayTarget = 0;
                                        $ftotalMonthlyAchievement = 0;
                                        $ftotalMonthlyAchievementPer = 0;
                                        $ftotalTodayAchievement = 0;
                                        $ftotalTodayTargetStrikeRate = 0;
                                        $ftotalTodayAchievementStrikeRate = 0;
                                        $ftotalMonthlyAchievementRequired = 0;
                                        $serial = 0;
                                    @endphp

                                    @foreach($territoryFO as $fos)
                                    @php
                                        $startDate     = date('Y-m'.'-01');
                                        $endDate       = date('Y-m'.'-31');
                                        $todayDate      = date('Y-m-d');                                        

                                        $monthlyTarget = DB::table('tbl_fo_target')->select('total_value','fo_id','start_date','end_date')
                                            ->where('employee_id', $fos->email)
                                            ->whereDate('start_date', '>=', $startDate)
                                            ->whereDate('end_date', '<=', $endDate)
                                            ->groupBy('fo_id')
                                            ->sum('total_value');

                                        $ftotalMonthlyTarget += $monthlyTarget;
										
										 $monthlyAchivement = DB::table('tbl_order')->select('order_type','global_company_id','fo_id','total_delivery_value','update_date')
                                        //->where('order_type', 'Delivered')
                                        ->where('total_delivery_qty','>',0)
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('fo_id', $fos->id)
                                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($startDate, $endDate))
                                        ->groupBy('fo_id')
                                        ->sum('total_delivery_value');

                                        $ftotalMonthlyAchievement += $monthlyAchivement;

                                        if($monthlyTarget >0)
                                        {
											
											$date1 = date("d");
											$dayAct = 26 - ($date1 - 1);
											
											$remtarget = $monthlyTarget - $monthlyAchivement;
											
											$todayTarget = ($remtarget/$dayAct);
											
											//$todayTarget = ($monthlyTarget/26);
                                  		
											$ftotalTodayTarget += $todayTarget;
                                        }
                                        else
                                        {
                                            $todayTarget = 0;
                                            $ftotalTodayTarget += $todayTarget;
                                        }

                                       

                                        //$ftotalMonthlyAchievementRequired += $monthlyTarget - $monthlyAchivement;

                                        $todayAchivement = DB::table('tbl_order')->select('order_type','global_company_id','fo_id','total_delivery_value','update_date')
                                        //->where('order_type', 'Delivered')
                                        ->where('total_delivery_qty','>',0)
                                        ->where('fo_id', $fos->id)
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->whereBetween(DB::raw("(DATE_FORMAT(update_date,'%Y-%m-%d'))"), array($todayDate, $todayDate))
                                        ->groupBy('fo_id')
                                        ->sum('total_delivery_value');

                                        $ftotalTodayAchievement += $todayAchivement;

                                    @endphp

                                        <tr>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ $fos->display_name }}</th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($monthlyTarget,2) }} </th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($todayTarget,2) }} </th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($monthlyAchivement,2) }} </th>
                                        <th style="font-weight: normal; font-size: 12px;"> 
                                            @if($monthlyAchivement>0 && $monthlyTarget>0)
                                                @php
                                                $ftotalMonthlyAchievementPer +=($monthlyAchivement * 100)/$monthlyTarget;
                                                @endphp

                                                 {{number_format(($monthlyAchivement * 100)/$monthlyTarget,0)}} %
                                            @else
                                                0 %
                                                @php
                                                //$ftotalMonthlyAchievementPer +=0;
                                                @endphp
                                            @endif
                                        </th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($todayAchivement,2) }} </th>
                                        <th style="font-weight: normal; font-size: 12px;">
                                            @if($todayAchivement>0 && $todayTarget>0)
                                                @php
                                                $ftotalTodayAchievementStrikeRate += ($todayAchivement * 100)/$todayTarget;
                                                @endphp

                                                {{number_format(($todayAchivement * 100)/$todayTarget,0)}} %
                                            @else
                                                0 %
                                                @php
                                                //$ftotalTodayAchievementStrikeRate += 0;
                                                @endphp
                                            @endif
                                        </th>
                                        <th style="font-weight: normal; font-size: 12px;"> 
                                            @if($monthlyTarget >0 && $monthlyAchivement >0)
                                                @php
                                                    $ftotalMonthlyAchievementRequired += (($monthlyTarget - $monthlyAchivement) / $monthlyTarget) *100;
                                                @endphp
                                                {{ number_format((($monthlyTarget - $monthlyAchivement) / $monthlyTarget) *100,0).'%' }}
                                            @else
                                                0%
                                            @endif
                                        <!-- {{ $monthlyTarget - $monthlyAchivement }} --></th>
                                    </tr>
                                    @php
                                    $serial ++;
                                    @endphp
                                    @endforeach
                                                             
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="font-weight: normal; font-size: 12px;"> TSM (Total)</th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($ftotalMonthlyTarget,2) }} </th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($ftotalTodayTarget,2) }}</th>
                                        <th style="font-weight: normal; font-size: 12px;"> {{ number_format($ftotalMonthlyAchievement,2) }}</th>
                                        <th style="font-weight: normal; font-size: 12px;">
                                            @php
                                                $re = $ftotalMonthlyAchievement/$ftotalMonthlyTarget;
                                            @endphp
                                            {{ number_format(($ftotalMonthlyAchievement/$ftotalMonthlyTarget) *100,2).'%' }}
                                        <!-- {{ number_format($ftotalMonthlyAchievementPer,0).' %' }} --></th>
                                        <th style="font-weight: normal; font-size: 12px;">{{ number_format($ftotalTodayAchievement,2) }}</th>
                                        <th style="font-weight: normal; font-size: 12px;">                                            
                                            {{ number_format(($ftotalTodayAchievement/$ftotalTodayTarget) *100,2).'%' }}

                                        </th>
                                        <th style="font-weight: normal; font-size: 12px;">
                                            {{ number_format((1-$re)*100,2).'%' }}

                                       <!--  {{ number_format($ftotalMonthlyAchievementRequired,0).' %' }} -->

                                    </th>
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