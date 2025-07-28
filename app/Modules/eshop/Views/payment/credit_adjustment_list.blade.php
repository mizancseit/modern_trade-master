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
                        <th>Customer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        {{-- <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th> --}}
                        <th>Adjustment Amount</th>
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
                       <td>{{$RowoutletPayment->name }}-{{$RowoutletPayment->transaction_id}}</td>
                       <td>{{$RowoutletPayment->sap_code }}</td>
                       <td>{{$RowoutletPayment->payment_no }}</td>
                       {{-- <td>{{$RowoutletPayment->bank_name }}</td>
                       <td>{{$RowoutletPayment->code }}</td>
                        <td>{{$RowoutletPayment->branch_name }}</td> --}}
                       <td>{{$RowoutletPayment->adjust_amount}}</td>
                       <td>{{$RowoutletPayment->payment_type}}</td>
                       <td>{{$RowoutletPayment->trans_date}}</td>
                        <td>@if($RowoutletPayment->upload_image!='')<img src="uploads/modernPayment/{{ $RowoutletPayment->upload_image }}" style="width: 60px; height: 40px;">@endif</td>
                        <th>{{$RowoutletPayment->ack_status}}</th>
                        
                        @if($RowoutletPayment->ack_status=="NO")
                        <td>
                            <!--input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editoutletPayment('{{ $RowoutletPayment->transaction_id }}')" style="width: 70px;"-->
                            
                            <a href=" {{ URL('/mts-outlet-payments-delete/'.$RowoutletPayment->transaction_id.'/'.'2') }}" onclick="return confirm('Are you sure you want to delete this item?')">
                                <button type="button" class="btn bg-red btn-block btn-lg waves-effect">Delete</button>
                            </a>
                        </td>
                        
                        @else
							
                        <th></th>
                        
						@endif
						
                    </tr>
                   
                
                @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="10">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Customer Name</th>
                        <th>SAP Code</th>
                        <th>Payment No</th>
                        {{-- <th>Bank Name</th>
                        <th>Bank A/C</th>
                        <th>Branch Name</th> --}}
                        <th>Adjustment Amount</th>
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
            