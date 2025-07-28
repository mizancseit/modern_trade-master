<div class="card">
    <div class="header">
        <h5>
            About result
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>CUSTOMER</th>
                        <th>STOCK</th>
                    </tr>
                </thead>
                
                <tbody> 
                    @php
                    $serial =1;
                    $closing_balance  = 0;

                    @endphp
                    {{-- @if(sizeof($stock_list))
                 
                    <tr>
                        <th>{{$stock_list->name }}</th>     
                        <th>{{ number_format($stock_list->closing_balance, 2) }}</th>  			
                    </tr>
                    @php
                    //$closing_balance = $orders->closing_balance;
                    $serial++;
                    @endphp

                    <tr>
                        <th style="text-align: right;">TOTAL BALANCE : </th>                        
                        <th>{{ number_format($stock_list->closing_balance,2) }}</th>
                    </tr>
                    @else
                    <tr>
                        <th colspan="2" style="text-align: center;">Data Not found</th>         
                    </tr>
                    @endif --}}

                    @if(sizeof($stock_list) > 0)   
                        @php
                        $serial =1;
                        $closing_balance  = 0;

                        @endphp

                        @foreach($stock_list as $stock_list)          
                         
                        <tr>
                           <th>{{$stock_list->name }}</th>     
                            <th>{{ number_format($stock_list->closing_balance, 2) }}</th>               
                        </tr> 
                        @php
                        $serial++;
                        @endphp
                        @endforeach

                        <tr>
                            <th style="text-align: right;">TOTAL BALANCE : </th>                        
                            <th>{{ number_format($closing_balance,2) }}</th>
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