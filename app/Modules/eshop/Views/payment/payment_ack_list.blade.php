<div class="card">  
    <div class="header">
        <h5>
            About {{ sizeof($outletPayment) }} results 
        </h5>
    </div>

    <div class="body">
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Payment Date</th>
                        <th>Customer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Actual Amount</th>
                        <th>Bank Charge</th>
                        <th>Payment Type</th>
                         <th>Action</th>
                        
                    </tr>
                </thead>
                 <tbody>
             
             @if(sizeof($outletPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($outletPayment as $RowoutletPayment) 
                               
                        <td>{{$serial}}</td>
                        <td>{{$RowoutletPayment->trans_date}}</td>
                        <td>{{$RowoutletPayment->name }}</td>
                       <td>{{$RowoutletPayment->sap_code }}</td>
                       <td>{{$RowoutletPayment->payment_no }}</td>
                       <td>{{$RowoutletPayment->bank_name }}</td>
                       <td>{{$RowoutletPayment->code }}</td>
                       <td>{{$RowoutletPayment->branch_name }}</td> 
                           <td>{{$RowoutletPayment->ref_no }}</td>
                        <td>{{$RowoutletPayment->payment_amount}}</td>
                        <td>{{$RowoutletPayment->adjust_amount}}</td>
                        <td>{{$RowoutletPayment->net_amount}}</td>
                        <td>{{$RowoutletPayment->bank_charge}}</td>
                        <td>{{$RowoutletPayment->payment_type}}</td>
                        <td><a href="{{ URL('/eshop-accounts-payments-undo/'.$RowoutletPayment->transaction_id) }}">
                              <input type="button" class="btn bg-red btn-block btn-sm waves-effect" value="Undo" style="width: 70px;">
                            </a>
                          </td>
                        
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="15">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Payment Date</th>
                        <th>Coustomer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Actual Amount</th>
                        <th>Bank Charge</th>
                        <th>Payment Type</th>
                         <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
            