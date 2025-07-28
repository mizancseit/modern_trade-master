<div class="card">
    <div class="header">
        <h2>
            Depot Data
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th>
                       <!--  <th>Division</th> -->
                        <th>Depot Name</th>
                        <th>Visit</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($depotResult) > 0)   
                    @php
                    $i =1;
                    @endphp

                    @foreach($depotResult as $depotList) 
                    <tr>
                        <td>{{$i++ }}</td>
                        <td>{{$depotList->point_name }}</td>
                        <td>
                             <a href="{{ URL('/stock-process/'.$depotList->point_id.'/'.'1') }}"> IN </a> |
                             <a href="{{ URL('/stock-process/'.$depotList->point_id.'/'.'2') }}"> OUT </a>
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="3">No record found.</th>
                    </tr>
                    @endif     

                </tbody>
                <tfoot>
                    <tr>
                       <th>SL</th>
                        <th>Depot Name</th>
                        <th>Visit</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>