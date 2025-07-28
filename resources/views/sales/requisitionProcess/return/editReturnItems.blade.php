<!-- Default Size -->

<form action="{{ URL('/fo/return-edit-submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">Order Product Edit</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $resultPro->return_order_det_id }}">
            <input type="hidden" name="order_id" id="order_id" value="{{ $resultPro->return_order_id }}">
            <input type="hidden" name="pointID" id="pointID" value="{{ $pointid }}">
            <input type="hidden" name="retailderID" id="retailderID" value="{{ $retailderid }}">
            <input type="hidden" name="routeID" id="routeID" value="{{ $routeid }}">
            <input type="hidden" name="catID" id="catID" value="{{ $catid }}">

            <div class="modal-body">
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Product Name
                    </div>

                    <div class="col-sm-5" style="font-weight: bold;">
                        {{ $resultPro->ret_proName }}
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Return Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" name="items_return" id="items_return" class="form-control" value="{{ $resultPro->return_qty }}" maxlength="8" min="1" required="">
                    </div>
                </div>
                <p>&nbsp;</p>
				
				<div class="row">
                    <div class="col-sm-5">
                        Change Product Name
                    </div>

                    <div class="col-sm-5" style="font-weight: bold;">
                        {{ $resultPro->chng_proName }}
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Change Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" name="items_change" id="items_change" class="form-control" value="{{ $resultPro->change_qty }}" maxlength="8" min="1" required="">
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
