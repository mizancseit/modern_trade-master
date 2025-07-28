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
                        <th>Method</th>
                        
                        <th>Ack Status</th>
                        {{-- <th class="pull-right">Action</th> --}}
                    </tr>
                </thead>
                 <tbody>
             
             @if(sizeof($outletPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($outletPayment as $RowoutletPayment) 
                               
                        <tr>
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
                           <td>
                            <input type="hidden" size="5" name="pay_amount" id="pay_amount{{$RowoutletPayment->transaction_id}}" value="{{$RowoutletPayment->payment_amount+$RowoutletPayment->adjust_amount}}" />
                            <input type="text" size="5" name="net_amount" id="net_amount{{$RowoutletPayment->transaction_id}}" value="" onkeyup="getBankCharge({{$RowoutletPayment->transaction_id}})" /></td>
                          <td><input type="text" size="5" name="bank_charge" id="bank_charge{{$RowoutletPayment->transaction_id}}" value="" readonly="" /></td>
                           <td>{{$RowoutletPayment->payment_type}}</td>
                           
                           <td>
                            <input type="hidden" size="5" name="id" id="id" value="{{$RowoutletPayment->transaction_id}}" />
                            <button type="submit" class="btn bg-green btn-block btn-lg waves-effect">Receive</button>
                            {{-- <a href="{{URL('account_payment_receive')}}/{{$RowoutletPayment->transaction_id}}" class="btn btn-success">Receive</a> --}}
                        </td>

                        </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="15">No record found.</td>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
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
                        <th>Ack Status</th>
                       {{--  <th class="pull-right">Action</th> --}}
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
            