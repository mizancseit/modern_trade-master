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
                        <th>Coustomer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th>
                        <th>Reference No</th>
                        <th>Payment Amount</th>
                        <th>Adjust Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Images</th>
                        <th>Ack Status</th>
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
                     <td>{{$RowoutletPayment->name }}</td>
                     <td>{{$RowoutletPayment->sap_code }}</td>
                     <td>{{$RowoutletPayment->payment_no }}</td>
                     <td>{{$RowoutletPayment->bank_name }}</td>
                     <td>{{$RowoutletPayment->code }}</td>
                     <td>{{$RowoutletPayment->branch_name }}</td>
                           <td>{{$RowoutletPayment->ref_no }}</td>
                     <td>{{$RowoutletPayment->payment_amount}}</td>
                     <td>{{$RowoutletPayment->adjust_amount}}</td>
                     <td>{{$RowoutletPayment->payment_type}}</td>
                     <td>{{$RowoutletPayment->trans_date}}</td>
                     <td>@if($RowoutletPayment->upload_image!='')<img src="uploads/modernPayment/{{ $RowoutletPayment->upload_image }}" style="width: 60px; height: 40px;">@endif</td>
                     <td>
                      <a href="{{URL('eshop_app_admin_payment')}}/{{$RowoutletPayment->transaction_id}}/APPROVE" class="btn btn-success">Approve</a>
                      <!-- <a href="{{URL('app_admin_payment')}}/{{$RowoutletPayment->transaction_id}}/NOT_APPROVE" class="btn btn-info">Not Approve</a> -->
                    </td>
                    <td>

                      <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editPayment('{{ $RowoutletPayment->transaction_id }}')" style="width: 70px;">

                      {{-- <a target="_blank" href="{{URL('eshop_app_admin_payment_edit')}}/{{$RowoutletPayment->transaction_id}}"><i class="material-icons">edit</i></a> --}}
                    </td>
                    
                </tr>
                
                
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                    <th colspan="14">No record found.</th>
                </tr>
                @endif     
                
            </tbody>
            <tfoot>
             <tr>
                <th>Sl</th>
                <th>Coustomer Name</th>
                <th>SAP Code</th>
                <th>Payment No</th>
                <th>Bank Name</th>
                <th>Bank A/C</th>
                <th>Branch Name</th>
                                <th>Reference No</th>
                <th>Payment Amount</th>
                <th>Adjust Amount</th>
                <th>Payment Type</th>
                <th>Payment Date</th>
                <th>Images</th>
                <th>Ack Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
        <tbody>
           
        </tbody>
    </table>
</div>
</div>
</div>
