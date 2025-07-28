<form action="{{ URL('/offer/value/other-edit-process') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">  
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" style="color:white;" id="defaultModalLabel">VALUE WISE FREE PRODUCT UPDATE</h4>
            </div>

            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                        
                        <input type="hidden" name="primaryid" value="{{ $primaryid }}">
                        <input type="hidden" name="price" id="price" value="{{ $pro->depo }}">

                        <div class="row clearfix">
                            <div class="col-sm-12 col-md-12">
                                <label for="qty">Product Name:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Name"  value="{{$pro->name }}" name="proname" disabled="" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-sm-12 col-md-6">
                                <label for="qty">Free Qty:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Qty"  value="{{ $pqty }}" maxlength="3" pattern="[1-9]" min="1" name="freeqty" id="freeqty" onkeyup="priceChange()" required="" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6">
                                <label for="qty">Free Value:</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Qty"  value="{{ $pvalue }}" name="freeValue" id="freeValue" readonly="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</form>