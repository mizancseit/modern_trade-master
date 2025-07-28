<!-- Default Size -->

<form action="{{ URL('/dist/free-req-edit-submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">REQUISITION PRODUCT EDIT</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $depotResult->req_det_id }}">


            <div class="modal-body">
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Product Name
                    </div>

                    <div class="col-sm-5" style="font-weight: bold;">
                        {{ $depotResult->name }}
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Order Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" pattern="[1-9]" min="1" name="items_qty" id="items_qty" class="form-control" value="{{ $depotResult->req_qnty }}" maxlength="8" required="">
                    </div>
                    <input type="hidden" name="items_price" id="items_price" class="form-control" value="{{ $depotResult->depo }}">
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Free Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" pattern="[0-9]" min="0" name="wastage_qty" id="wastage_qty" class="form-control" value="{{ $depotResult->wastage_qty }}" maxlength="8">
                    </div>
                    <input type="hidden" name="items_price" id="items_price" class="form-control" value="{{ $depotResult->depo }}">
                </div>
               
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</form>
