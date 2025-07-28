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
                        <th>Verify Date</th>
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
                        <th>Ack Remarks</th>
                        <th>Status</th>
                        <th>Remarks/Note</th> 
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
                        <td>{{$RowoutletPayment->confirmed_date}}</td>
                        <td>{{$RowoutletPayment->name }}</td>
                       <td>{{$RowoutletPayment->sap_code }}</td>
                       <td>{{$RowoutletPayment->payment_no }}</td>
                       <td>{{$RowoutletPayment->payment_from }}</td>
                       <td>{{$RowoutletPayment->bank_name }}</td>
                       <td>{{$RowoutletPayment->code ? 'CA-'.substr($RowoutletPayment->code, -5) : '' }}</td>
                       {{-- <td>{{$RowoutletPayment->code }}</td> --}}
                       <td>{{$RowoutletPayment->branch_name }}</td> 
                        <td>{{$RowoutletPayment->ref_no }}</td>
                        <td>{{$RowoutletPayment->payment_amount}}</td>
                        <td>{{$RowoutletPayment->adjust_amount}}</td>
                        <td>{{$RowoutletPayment->net_amount}}</td>
                        <td>{{$RowoutletPayment->bank_charge}}</td>
                        <td>{{$RowoutletPayment->payment_type}}</td>
                        <td>{{$RowoutletPayment->ack_remarks}}</td>
                        <td>{!!statusWiseData($RowoutletPayment->ack_status)!!}</td>
                        <td>{{$RowoutletPayment->payment_remarks}}</td>
                        
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
                        <th>Verify Date</th>
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
                        <th>Ack Remarks</th>
                        <th>Status</th>
                        <th>Remarks/Note</th> 
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
    function statusWiseData($data){
        switch ($data){
            case 'NOT_APPROVE':
                $approve_status_name = '<span style="background-color:#ffc107" class="badge bg-warning">'.$data.'</span>';
                break;
            case 'APPROVE':
                $approve_status_name = '<span style="background-color:#17a2b8" class="badge bg-success">'.$data.'</span>';
                break;
            case 'CONFIRMED':
                $approve_status_name = '<span style="background-color:#28a745" class="badge bg-success">'.$data.'</span>';
                break;
            case 'YES':
                $approve_status_name = '<span style="background-color:#28a745" class="badge bg-success">'.$data.'</span>';
                break;
            case 'NOT_CONFIRMED':
                $approve_status_name = '<span style="background-color:#dc3545" class="badge bg-danger">'.$data.'</span>';
                break;
            case 'NO':
                $approve_status_name = '<span style="background-color:#dc3545" class="badge bg-danger">'.$data.'</span>';
                break;
            default:
                $approve_status_name = '<span style="background-color:#17a2b8" class="badge bg-default">'.$data.'</span>';
                break;
        }
        return $approve_status_name;
    }
?>   
