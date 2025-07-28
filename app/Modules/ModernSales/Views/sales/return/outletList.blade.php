<div class="card">
    <div class="header">
        <h2>
            Outlet List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Outlet Name</th>
                        <th>Return</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Outlet Name</th>
                        <th>Return</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultParty) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultParty as $retailers)

                     
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $retailers->name }}</th>
                     
                        <th><a href="{{ URL('/mts-return-process/'.$retailers->party_id.'/'.$customerID) }}"> Return Collect </a></th>
                        <th></th>
                       
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="4">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
