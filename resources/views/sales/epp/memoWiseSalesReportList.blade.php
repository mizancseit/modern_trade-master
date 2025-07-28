
<div class="card">
                    
    <div class="body" id="printMe">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th style="font-size: 12px;">Channel</th>
                        <th style="font-size: 12px;">Division</th>
                        <th style="font-size: 12px;">Territory</th>
                        <th style="font-size: 12px;">Point area</th>
                        <th style="font-size: 12px;">SAP Code</th>
                        <th style="font-size: 12px;">DB/Depot Name</th>
                        <th style="font-size: 12px;">Retailer Name</th>
                        <th style="font-size: 12px;">Memo No</th>
                        <th style="font-size: 12px;">Memo Date</th>
                        <th style="font-size: 12px;">Memo Sales Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($memoResult)>0)
                        @foreach($memoResult as $memoResults)
                            <tr>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->business_type}}</th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->div_name }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->teriName }}</th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->point_name }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->sap_code }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->display_name }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->retailerName }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->order_no }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->order_date }} </th>
                                <th style="font-size: 12px; font-weight: normal;"> {{ $memoResults->total_value }} </th>
                            </tr>
                        @endforeach
                    @endif                        
                </tbody>
            </table>
        </div>
    </div>

    {{-- For Print --}}
    
    <!-- <div class="card">
        <div class="row" style="text-align: center; padding: 10px 10px; ">
            <div class="col-sm-12">
                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                    <i class="material-icons">print</i>
                    <span>PRINT...</span>
                </button>
            </div>
        </div>
    </div>  -->  

</div>