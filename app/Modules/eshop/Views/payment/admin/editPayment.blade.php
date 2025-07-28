<!-- Default Size -->

<form action="{{ URL('/app_admin_payment_edit_submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">Payment Edit</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{$outletPayment->transaction_id}}"> 

            <div class="modal-body"> 
                <div class="row clearfix"> 
                    <div class="col-md-12">  
                        <div class="col-md-8">
                            <label for="division">Customer:*</label>
                            <div class="form-group">
                                <div class="form-line">

                                <select class="form-control show-tick" name="customer_id" required="">
                                <option value=""> Select Customer</option>
                                @foreach($outletList as $rowOutlet)
                                <option value="{{ $rowOutlet->customer_id}}" @if ($rowOutlet->customer_id==$outletPayment->customer_id) {{ "selected" }} @endif>{{ $rowOutlet->name }} - {{$rowOutlet->sap_code}}</option>
                                @endforeach
                                </select>

                                </div>
                            </div> 
                        </div>
                        <div class="col-md-4">
                            <label for="division">Payment Type:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                <select class="form-control show-tick" id="payment_type" name="payment_type" onChange="distriBankDetails();" required="">
                                <option value="">Select Type</option>
                                <option value="CASH" @if ($outletPayment->payment_type=='CASH') {{ "selected" }} @endif>CASH</option>
                                <option value="CHEQUE" @if ($outletPayment->payment_type=='CHEQUE') {{ "selected" }} @endif>CHEQUE</option>
                                <option value="ON-LINE" @if ($outletPayment->payment_type=='ON-LINE') {{ "selected" }} @endif>ON-LINE</option>
                                <option value="PAY-ORDER" @if ($outletPayment->payment_type=='PAY-ORDER') {{ "selected" }} @endif>PAY-ORDER</option>
                                <option value="DD" @if ($outletPayment->payment_type=='DD') {{ "selected" }} @endif>DD</option>
                                <option value="TT" @if ($outletPayment->payment_type=='TT') {{ "selected" }} @endif>TT</option>
                                <option value="MR" @if ($outletPayment->payment_type=='MR') {{ "selected" }} @endif>MR</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="col-md-8">
                            <label for="division">Select Bank  A/C:*</label>

                            <div class="form-group">
                                <div class="form-line">
                                    <select id="bank_info" name="bank_info" class="form-control show-tick">
                                    <option value="">Select Bank</option>
                                    @foreach($bankList as $bankList)
                                    <option value="{{ $bankList->id}}" @if ($bankList->id==$outletPayment->bank_info_id) {{ "selected" }} @endif>{{ $bankList->bank_name }} - {{$bankList->code}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="division">Branch: </label>
                            <div class="form-group">
                                <div class="form-line">
                                <input type="text" class="form-control" placeholder="Branch Name" name="branch_name" value="{{$outletPayment->branch_name}}" />
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">  
                        
                        <div class="col-md-8"> 
                            <label for="ref_no">REF.NO</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="REF.NO (CHEQUE/DD/PAY-ORDER,TT NO) " name="ref_no" value="{{$outletPayment->ref_no}}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="division">Cheque Date:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" name="cheque_date" id="todate" class="form-control" value="{{$outletPayment->cheque_date}}" placeholder="Select To Date">
                                </div>
                            </div>
                        </div>
                    </div> 
                    
                    <div class="col-md-12">  
                        <div class="col-md-6"> 
                            <label for="division">Payment Amount:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Payment Amount" name="payment_amount" value="{{$outletPayment->payment_amount}}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="division">Adjust Amount:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Adjust Amount" name="adjust_amount" value="{{$outletPayment->adjust_amount}}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <label for="division">Remarks/Note:*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Remarks" id="remarks" name="payment_remarks" value="{{$outletPayment->payment_remarks}}" />
                            </div>
                        </div>  
                    </div> 

                </div>
            </div>  
            <div class="modal-footer">
                <button type="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</form>
