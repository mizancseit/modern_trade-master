<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($ledger_list) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL NO.</th>
                        <th>REF NO.</th>
                        <th>DATE</th>
                        <th>TYPE</th>
                        <th>OPENING BALANCE</th>
                        <th>DEBIT</th>
                        <th>CREDIT</th>
                        <th>CLOSING BALANCE</th>
                    </tr>
                </thead>
                
                <tbody>
                @if(sizeof($ledger_list) > 0)   
                    @php
                    $serial =1;
                    $closing_balance  = 0;

                    @endphp

                    @foreach($ledger_list as $orders)                   
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $orders->invoice_no }}</th>
                        <th>{{ $orders->ledger_date }}</th>
                        <th>{{ $orders->trans_type }}</th>                                              
                        <th>{{ number_format($orders->opening_balance,2 ) }}</th>                        
                        <th>{{ number_format($orders->debit, 2) }}</th>                                              
                        <th>{{ number_format($orders->credit, 2) }}</th>                                              
                        <th>{{ number_format($orders->closing_balance, 2) }}</th> 				
                    </tr>
                    @php
                    $closing_balance = $orders->closing_balance;
                    $serial++;
                    @endphp
                    @endforeach

                    <tr>
                        <th colspan="7" style="text-align: right;">CLOSING BALANCE : </th>                        
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
