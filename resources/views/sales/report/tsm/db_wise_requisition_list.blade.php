<div class="card"  id="printMe">
    <div class="header">
        <h5>
            About {{ sizeof($reports) }} results 
        </h5>
    </div>                  
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
               <thead>
                    <tr>
                        <th>Req No</th>
                        <th>Req Date</th>
                        <th>Billed Status</th>
                        <th>Date</th>
                        <th>Factory Delivered Status</th>
                        <th>Date</th>
                        <th>Receiving Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($reports) > 0)
                        @foreach($reports as $reportsData)
                        <tr>
                            <th style="font-weight: normal;">{{ $reportsData->req_no }}</th>
                            <th style="font-weight: normal;">{{ date('d-m-Y',strtotime($reportsData->req_date)) }}</th>
                            <th style="font-weight: normal; text-align: center;">
                                
                                @if($reportsData->req_status=='billed' || $reportsData->req_status=='partial_delivered' || $reportsData->req_status=='delivered' || $reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    Yes
                                @endif
                            </th>
                            <th style="font-weight: normal;">
                                @if($reportsData->req_status=='billed' || $reportsData->req_status=='partial_delivered' || $reportsData->req_status=='delivered' || $reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    @if(!empty($reportsData->billed_date)) {{ date('d-m-Y',strtotime($reportsData->billed_date)) }} @endif 
                                @endif
                            </th>
                            <th style="font-weight: normal; text-align: center;">                                
                                @if($reportsData->req_status=='partial_delivered' || $reportsData->req_status=='delivered' || $reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    Yes
                                @endif
                            </th>
                            <th style="font-weight: normal;">
                                @if($reportsData->req_status=='partial_delivered' || $reportsData->req_status=='delivered' || $reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    {{ date('d-m-Y',strtotime($reportsData->delivered_date)) }}
                                @endif
                            </th>
                            <th style="font-weight: normal; text-align: center;">                                
                                @if($reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    Yes
                                @endif
                            </th>
                            <th style="font-weight: normal;">
                                @if($reportsData->req_status=='partial_received' || $reportsData->req_status=='received')
                                    {{ date('d-m-Y',strtotime($reportsData->received_date)) }}
                                @endif
                            </th>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="8">No record found.</th>
                        </tr>
                    @endif    
                        
                    </tbody>
            </table>
        </div>
    </div>

    {{-- For Print --}}
    @if(sizeof($reports) > 0)
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
    @endif

</div>