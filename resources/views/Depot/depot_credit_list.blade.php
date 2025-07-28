<div class="card">  
    <div class="header">
        <h5>
            About {{ sizeof($depotPayment) }} results 
        </h5>
    </div>

    <div class="body">
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Ack Status</th>
                        {{-- <th class="pull-right">Action</th> --}}
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotPayment) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($depotPayment as $RowDepotPayment) 
                               
                        <th>{{$serial}}</th>
                        <th>{{$RowDepotPayment->point_name }}</th>
                        <th>{{$RowDepotPayment->trans_amount}}</th>
                        <th>{{$RowDepotPayment->payment_type}}</th>
                        <th>{{$RowDepotPayment->trans_date}}</th>
                        <th>{{$RowDepotPayment->ack_status}}</th>
                        
                        @if($RowDepotPayment->ack_status=="NO")
                        
						<!--th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editDepotPayment('{{ $RowDepotPayment->transaction_id }}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteDepotPayment('{{ $RowDepotPayment->transaction_id}}')" style="width: 70px; margin-top: 0px;"></th-->
                        
                        @else
							
                       {{--  <th></th> --}}
                        
						@endif
						
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="6">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Depot</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
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
            