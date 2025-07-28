<div id="showHiddenDiv">
    <div class="card"  id="printMe">
        
        <div class="body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="font-size: 11px;">Particulers</th>
                            <th style="font-size: 11px;">Today</th>
                            <th style="font-size: 11px;">Yesterday</th>
                            <th style="font-size: 11px;">Same day Last month</th>
                            <th style="font-size: 11px;">Current month</th>
                            <th style="font-size: 11px;">Last month</th>
                            {{-- <th style="font-size: 11px;">Total</th> --}}
                        </tr>
                    
                        <tr>
                            <th style="font-size: 11px;">Target</th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($currentMonthTarget/26,0) }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($currentMonthTarget/26,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($sameDayLastMonthTarget/26,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($currentMonthTarget,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($lastMonthTarget,0) }}</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ number_format($todayTarget +$yesterdayTarget +$currentMonthTarget +$lastMonthTarget,0) }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Sales (Primary)</th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($todayPrimarySales,0) }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($yesterdayPrimarySales,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($sameDayLastMonthPrimarySales,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($currentMonthPrimarySales,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($lastMonthPrimarySales,0) }}</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ $todayPrimarySales +$yesterdayPrimarySales +$sameDayLastMonthPrimarySales +$currentMonthPrimarySales +$lastMonthPrimarySales }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Collection</th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($todayCollection,0) }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($yesterdayCollection,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($sameDayLastMonthCollection,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($currentMonthCollection,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($lastMonthCollection,0) }}</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ $todayCollection +$yesterdayCollection +$sameDayLastMonthCollection+$currentMonthCollection +$lastMonthCollection }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Credit</th>
                            <th style="font-size: 11px; text-align: right;"> 
                            {{ number_format(($todaySecondarySales + $retailersOpeningBalance) - $todayCollection,0)  }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format(($yesterdaySecondarySales + $retailersOpeningBalance) - $yesterdayCollection,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format(($sameDayLastMonthSecondarySales + $retailersOpeningBalance) - $sameDayLastMonthCollection,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format(($currentMonthSecondarySales + $retailersOpeningBalance) - $currentMonthCollection,0) }} 
                            </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format(($lastMonthSecondarySales + $retailersOpeningBalance) - $lastMonthCollection,0) }}
                            </th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ ($todayPrimarySales - $todayCollection) + ($yesterdayPrimarySales - $yesterdayCollection ) + ($sameDayLastMonthPrimarySales - $sameDayLastMonthCollection) + ($currentMonthPrimarySales- $currentMonthCollection) +($lastMonthPrimarySales- $lastMonthCollection) }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Secondary Sales (IMS)</th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($todaySecondarySales,0) }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($yesterdaySecondarySales,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($sameDayLastMonthSecondarySales,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($currentMonthSecondarySales,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($lastMonthSecondarySales,0) }}</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ $todayPrimarySales +$yesterdayPrimarySales +$sameDayLastMonthPrimarySales +$currentMonthPrimarySales +$lastMonthPrimarySales }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Memo Qty</th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($todayMemo,0) }} </th>
                            <th style="font-size: 11px; text-align: right;"> {{ number_format($yesterdayMemo,0) }} </th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($sameDayLastMonthMemo,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($currentMonthMemo,0) }}</th>
                            <th style="font-size: 11px; text-align: right;">{{ number_format($lastMonthMemo,0) }}</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ $todayMemo +$yesterdayMemo +$sameDayLastMonthMemo +$currentMonthMemo +$lastMonthMemo }}</th> --}}
                        </tr>
                        <tr>
                            <th style="font-size: 11px;">Per Memo Ave.Value</th>
                            <th style="font-size: 11px; text-align: right;"> @if($todaySecondarySales > 0 && $todayMemo > 0) {{number_format($todaySecondarySales/$todayMemo) }} @else 0 @endif </th>
                            <th style="font-size: 11px; text-align: right;">  @if($yesterdaySecondarySales > 0 && $yesterdayMemo > 0) {{ number_format($yesterdaySecondarySales/$yesterdayMemo,0) }} @else 0 @endif</th>
                            <th style="font-size: 11px; text-align: right;"> @if($sameDayLastMonthSecondarySales > 0 && $sameDayLastMonthMemo > 0) {{ number_format($sameDayLastMonthSecondarySales/$sameDayLastMonthMemo,0) }} @else 0 @endif</th>
                            <th style="font-size: 11px; text-align: right;"> @if($currentMonthSecondarySales > 0 && $currentMonthMemo > 0) {{ number_format($currentMonthSecondarySales/$currentMonthMemo,0) }} @else 0 @endif</th>
                            <th style="font-size: 11px; text-align: right;"> @if($lastMonthSecondarySales > 0 && $lastMonthMemo > 0) {{ number_format($lastMonthSecondarySales/$lastMonthMemo,0) }} @else 0 @endif</th>
                            {{-- <th style="font-size: 11px; text-align: right;"> {{ round($todayMemoAve/26) + round($yesterdayMemoAve/26) + round($sameDayLastMonthMemoAve/26) + round($currentMonthMemoAve/26) +round($lastMonthMemoAve/26) }}</th> --}}
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