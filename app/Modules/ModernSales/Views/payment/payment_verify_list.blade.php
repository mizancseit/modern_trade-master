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
                        <th>Payment From</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Actual Amount</th>
                        <th>Bank Charge</th>
                        <th>Payment Type</th>
                        <th> Verify
                            <a href="javascript:;" onclick="toggleCheckAll()">Check All</a>  
                        </th>
                        
                        <th>Ack Remarks</th>                       
                    </tr>
                </thead>
                 <tbody>
             
             @if(sizeof($outletPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($outletPayment as $key =>$RowoutletPayment) 
                               
                        <td>{{$serial}}</td>
                        <td>{{$RowoutletPayment->trans_date}}</td>
                        <td>{{$RowoutletPayment->name }}</td>
                       <td>{{$RowoutletPayment->sap_code }}</td>
                       <td>{{$RowoutletPayment->payment_no }}</td>
                       <td>{{$RowoutletPayment->payment_from }}</td>
                       <td>{{$RowoutletPayment->bank_name }}</td>
                       <td>{{$RowoutletPayment->code }}</td>
                       <td>{{$RowoutletPayment->branch_name }}</td>
                           <td>{{$RowoutletPayment->ref_no }}</td>
                        <td>{{$RowoutletPayment->payment_amount}}</td>
                        <td>{{$RowoutletPayment->adjust_amount}}</td>
                        <td>{{$RowoutletPayment->net_amount}}</td>
                        <td>{{$RowoutletPayment->bank_charge}}</td>
                        <td>{{$RowoutletPayment->payment_type}}</td>
                         <td> 
                             <input type="checkbox" id="tran_id{{$RowoutletPayment->transaction_id}}" name="tran_id[{{$key}}]" value="{{$RowoutletPayment->transaction_id}}" class="verify-checkbox filled-in" />
                            <label for="tran_id{{$RowoutletPayment->transaction_id}}" style="margin-bottom: 0px">Yes</label>
                          </td> 
                          <td>
                            <textarea name="ack_remarks[{{$key}}]">{{$RowoutletPayment->ack_remarks}}</textarea>
                          </td>
                        
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="17">No record found.</th>
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
                        <th>Payment From</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Actual Amount</th>
                        <th>Bank Charge</th>
                        <th>Payment Type</th>
                        <th>Verify</th>
                        <th>Ack Remarks</th>
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
