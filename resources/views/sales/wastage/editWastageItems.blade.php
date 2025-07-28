<!-- Default Size -->

<form action="{{ URL('/wastage-items-edit-submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">Order Product Edit</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $resultPro->order_det_id }}">
            <input type="hidden" name="order_id" id="order_id" value="{{ $resultPro->order_id }}">
            <input type="hidden" name="pointID" id="pointID" value="{{ $pointid }}">
            <input type="hidden" name="retailderID" id="retailderID" value="{{ $retailderid }}">
            <input type="hidden" name="routeID" id="routeID" value="{{ $routeid }}">
            <input type="hidden" name="catID" id="catID" value="{{ $catid }}">
            <input type="hidden" name="product_id" id="product_id" value="{{ $resultPro->product_id }}">


            <div class="modal-body">
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Product Name
                    </div>

                    <div class="col-sm-5" style="font-weight: bold;">
                        {{ $resultPro->name }}
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Wastage Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" name="items_wastage" id="items_wastage" class="form-control" value="{{ $resultPro->wastage_qty }}" maxlength="8">
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
