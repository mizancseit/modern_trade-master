<div class="card"> 
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>Product Name </th>
                        <th>SAP Code</th> 
                        <th>instock</th>
                        <th>outstock</th> 
                        <th>Closing</th> 
                    </tr>
                </thead>
                <tbody> 
                    @if(sizeof($stocks) > 0)   
                        @php
                        $serial =1;
                        $totalQty = 0;
                        $totalValue = 0;
                        $totalinstock= 0;
                        $totaloutstock=0;

                        @endphp

                        @foreach($stocks as $stock)
                        @php
                        $totalinstock += $stock->instock;
                        $totaloutstock += $stock->outstock;

                        @endphp                    
                        <tr>
                            <td>{{ $stock->name }} </td>
                            <td>{{ $stock->sap_code }} </td>                        
                            <td>{{ $stock->instock }} </td>         
                            <td>{{ $stock->outstock }} </td> 
                            <td>{{ $stock->instock - $stock->outstock }} </td> 
                        </tr>
                        @php
                        $serial++;
                        @endphp
                        @endforeach 

                        <tr>
                            <th colspan="2" style="text-align:right;">Total Qty</th>                        
                            <th>{{ $totalinstock}}</th> 
                            <th>{{ $totaloutstock}}</th>  
                            <th>{{ $totalinstock - $totaloutstock}}</th>          
                        </tr>

                    @else
                        <tr>
                            <th colspan="7">No record found.</th>
                        </tr>
                    @endif    
                        
                    </tbody>
            </table>
        </div>
    </div>
</div>