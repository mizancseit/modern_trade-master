@if($serialNo==1)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Top 10 Distributor</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($topDistributor) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($topDistributor as $orders)
                            @php
                            $totalValue += $orders->total_value1;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->display_name }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_value1 }}</th>
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="2" style="text-align: right;">Grand Total : </th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==2)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Business Wise Target Vs Achievement</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>Business Type</th>
                                <th>Target</th>
                                <th>Achievement</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($yearlyTarget) > 0)   
                            @php
                            $serial =1;
                            $totalTargetValue = 0;
                            $totalAchivementValue = 0;
                            $currentYear  = date('Y');
                            @endphp

                            @foreach($yearlyTarget as $orders)

                            @php                            
                            $totalTargetValue += $orders->totalTarget;
                            $totalAchivementValue = 0;
                            @endphp

                            <tr>
                                <th style="font-weight: normal;">{{ $orders->business_type }}</th>
                                <th style="font-weight: normal;">{{ number_format($orders->totalTarget,2) }}</th>
                                @php
                                $yearlyAchivement = DB::table('tbl_order')
                                        ->select(DB::raw("SUM(tbl_order.total_value) as totalAchivement"),'tbl_order.fo_id','tbl_order.total_value','users.id', 'users.business_type_id')

                                        ->leftJoin('users', 'tbl_order.fo_id', '=', 'users.id')

                                        ->where('tbl_order.global_company_id', Auth::user()->global_company_id)
                                        ->where('users.business_type_id', $orders->business_type_id)                       
                                        ->whereBetween(DB::raw("(DATE_FORMAT(tbl_order.order_date,'%Y'))"), array($currentYear, $currentYear))                       
                                        ->groupBy('users.business_type_id')
                                        ->orderBy('users.business_type_id','ASC')                    
                                        ->first();
                                @endphp
                                <th style="font-weight: normal;">
                                    @if(sizeof($yearlyAchivement)>0) {{ number_format($totalAchivementValue += $yearlyAchivement->totalAchivement,2) }} @else 0 @endif</th>
                            </tr>
                            @php
                            $serial++;
                            //$totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th style="text-align: left;">Total :</th>
                                <th style="text-align: left;">{{ number_format($totalTargetValue,2) }}</th>
                                <th style="text-align: left;">{{ number_format($totalAchivementValue,2) }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==3)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Top 10 FO</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($topFo) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($topFo as $orders)
                            @php
                            $totalValue += $orders->total_value1;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->display_name }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_value1 }}</th>
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="2" style="text-align: right;">Grand Total : </th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==4)
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">    
        <div class="modal-header" style="background-color: #A62B7F">
            <button type="button" class="close" style="opacity: 0px;">
                <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
            </button>
            <h4 class="modal-title" id="myModalLabel" >Business Wise Total Collection</h4>
        </div>
    
        <div class="modal-body" style="text-align: center;" id="printMe">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                    <thead>
                        <tr>
                            <th>Business Type</th>
                            <th>Collection Amount</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                    @if(sizeof($resultOrderList) > 0)   
                        @php
                        $serial =1;
                        $totalTargetValue = 0;
                        $totalAchivementValue = 0;
                        $currentYear  = date('Y');
                        @endphp

                        @foreach($resultOrderList as $orders)

                        @php                            
                        $totalTargetValue += $orders->totalSales;
                        $totalAchivementValue = 0;
                        @endphp

                        <tr>
                            <th style="font-weight: normal;">{{ $orders->business_type }}</th>
                            <th style="font-weight: normal;">{{ number_format($orders->totalSales,2) }}</th>
                            
                        </tr>
                        @php
                        $serial++;
                        @endphp
                        @endforeach

                        <tr>
                            <th style="text-align: left;">Total :</th>
                            <th style="text-align: left;">{{ number_format($totalTargetValue,2) }}</th>
                        </tr>

                    @else
                        <tr>
                            <th colspan="3">No record found.</th>
                        </tr>
                    @endif    
                        
                    </tbody>
                </table>                                  
            </div>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>

@elseif($serialNo==5)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Business Wise Yesterday Sales</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>Business Type</th>
                                <th>Sales Amount</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalTargetValue = 0;
                            $totalAchivementValue = 0;
                            $currentYear  = date('Y');
                            @endphp

                            @foreach($resultOrderList as $orders)

                            @php                            
                            $totalTargetValue += $orders->totalSales;
                            $totalAchivementValue = 0;
                            @endphp

                            <tr>
                                <th style="font-weight: normal;">{{ $orders->business_type }}</th>
                                <th style="font-weight: normal;">{{ number_format($orders->totalSales,2) }}</th>
                                
                            </tr>
                            @php
                            $serial++;
                            //$totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th style="text-align: left;">Total :</th>
                                <th style="text-align: left;">{{ number_format($totalTargetValue,2) }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==6)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Current Month Total Sales</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($topDistributor) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($topDistributor as $orders)
                            @php
                            $totalValue += $orders->total_value1;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->display_name }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_value1 }}</th>
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="2" style="text-align: right;">Grand Total : </th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==7)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Business Wise Till Date Total Sales</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>Business Type</th>
                                <th>Sales Amount</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalTargetValue = 0;
                            $totalAchivementValue = 0;
                            $currentYear  = date('Y');
                            @endphp

                            @foreach($resultOrderList as $orders)

                            @php                            
                            $totalTargetValue += $orders->totalSales;
                            $totalAchivementValue = 0;
                            @endphp

                            <tr>
                                <th style="font-weight: normal;">{{ $orders->business_type }}</th>
                                <th style="font-weight: normal;">{{ number_format($orders->totalSales,2) }}</th>
                                
                            </tr>
                            @php
                            $serial++;
                            //$totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th style="text-align: left;">Total :</th>
                                <th style="text-align: left;">{{ number_format($totalTargetValue,2) }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==8)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Business Wise Current Month Target</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>Business Type</th>
                                <th>Target</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($yearlyTarget) > 0)   
                            @php
                            $serial =1;
                            $totalTargetValue = 0;
                            $totalAchivementValue = 0;
                            $currentYear  = date('Y');
                            @endphp

                            @foreach($yearlyTarget as $orders)

                            @php                            
                            $totalTargetValue += $orders->totalTarget;
                            $totalAchivementValue = 0;
                            @endphp

                            <tr>
                                <th style="font-weight: normal;">{{ $orders->business_type }}</th>
                                <th style="font-weight: normal;">{{ number_format($orders->totalTarget,2) }}</th>
                                
                            </tr>
                            @php
                            $serial++;
                            //$totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th style="text-align: left;">Total :</th>
                                <th style="text-align: left;">{{ number_format($totalTargetValue,2) }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="3">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==9)
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Today's Attendance FO</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Distributor</th>
                                <th>Retailer</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($attendance) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($attendance as $attendances)
                                              
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $attendances->first_name }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $dname = DB::table('users')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('id', $attendances->distributor)
                                        ->first();
                                        
                                        if(sizeof($dname)>0)
                                        {
                                            echo $dname->display_name;
                                        }
                                    @endphp
                                </th>
                                <th style="font-weight: normal;">{{ $attendances->retailerName }}</th>
                                <th style="font-weight: normal;">{{ $attendances->location }}</th>
                                <th style="font-weight: normal;">{{ $attendances->date }}</th>
                                <th style="font-weight: normal;">{{ date('H:i', strtotime($attendances->entrydatetime)) }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $out = DB::table('ims_attendence')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        //->where('foid', Auth::user()->id)
                                        ->where('type', 3)
                                        ->where('date', $attendances->date)
                                        ->groupBy('date')
                                        ->max('entrydatetime');

                                        $outTime = '';
                                        if(sizeof($out)>0)
                                        {
                                            $outTime = date('H:i', strtotime($out));
                                        }
                                    @endphp
                                        
                                    {{ $outTime }}
                                </th>
                            </tr>
                            @php
                            $serial++;
                            $totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="8" style="text-align: left;">Total Row : {{ $totalValue }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="8">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serialNo==10)
<div class="card" id="printMe">
    <div class="header">
        <h5>
            TODAY'S ATTENDANCE{{-- About {{ sizeof($resultAttendanceList) }} results --}} 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>FO</th>
                        <th>DATE</th>                        
                        <th>LOCATION</th>                        
                        <th>IN TIME</th>                        
                        <th>OUT TIME</th>                        
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($resultAttendanceList) > 0)   
                    @php
                    $serial =1;                    
                    @endphp

                    @foreach($resultAttendanceList as $visits)                                       
                    <tr>
                        <th>{{ $serial }}</th> 
                        <th>{{ $visits->first_name }}</th>
                        <th>{{ date('d M Y', strtotime($visits->date)) }}</th>                                               
                        <th>{{ $visits->location }}</th>                                               
                        <th>{{ date('H:i', strtotime($visits->entrydatetime)) }}</th>
                        <th> 
                        @php
                        $out = DB::table('ims_attendence')
                        ->where('global_company_id', Auth::user()->global_company_id)
                        ->where('foid', Auth::user()->id)
                        ->where('type', 3)
                        ->where('date', $visits->date)
                        ->groupBy('date')
                        ->max('entrydatetime');

                        $outTime = '';
                        if(sizeof($out)>0)
                        {
                            $outTime = date('H:i', strtotime($out));
                        }
                        @endphp
                        
                        {{ $outTime }}</th>
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="6">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif



