<form action="{{ URL('/mts-bank-account-edit-process') }}" method="POST">
    {{ csrf_field() }} <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" style="color:white;" id="defaultModalLabel">Edit Bank Account</h4>
            </div>
            <div class="modal-body">

                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-6">
                            <label for="name">A/C Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="hidden" name="id" value="{{$bankInfo->id }}" />
                                    <input required type="text" class="form-control" placeholder="A/C Name"
                                        name="name" id="name" value="{{$bankInfo->accountname }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="account_no">A/C No:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="A/C Number"
                                        name="account_no" value="{{$bankInfo->code }}" id="account_no" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-4 col-md-4">
                            <label for="division">Bank Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Bank Name"
                                        name="bank_name" value="{{$bankInfo->bank_name }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4">
                            <label for="division">Branch Name:</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Branch Name"
                                        name="branch_name"  value="{{$bankInfo->branchname }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 col-md-4">
                            <label for="division">Short Code:</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Short Code"
                                        name="short_code" value="{{$bankInfo->shortcode }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-link waves-effect">Save</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
