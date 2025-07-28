<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($allFo) }} results 
        </h5>
    </div>                  
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>FO Name</th>
                        <th>Retailer Name</th>
                    </tr>
                </thead>
                <tbody>
                     @if(sizeof($allFo) > 0)
                        @php
                        $serial =1;                        
                        @endphp

                        @foreach($allFo as $allFos)
                            <tr>
                                <td>{{ $serial }}</td>
                                <td colspan="2">{{ $allFos->display_name }}</td>
                            </tr>

                            @php
                                $retailer = DB::table('tbl_retailer')                                  
                                  ->where('point_id', $allFos->point_id)
                                  ->orderBy('name')
                                  ->get();
                            @endphp
                            @foreach($retailer as $retailers)
                                <tr>
                                    <td colspan="2"></td>
                                    <td>{{ $retailers->name }}</td>
                                </tr>
                            @endforeach
                        @php
                        $serial++;
                        
                        @endphp
                        @endforeach
                     @endif                        
                    </tbody>
            </table>
        </div>
    </div>

    {{-- For Print --}}
    @if(sizeof($allFo) > 0)
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