<!-- Default Size -->

<form action="{{ URL('/eshop-replace-edit-submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">Replace Product Edit</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $resultPro->replace_det_id }}">
            <input type="hidden" name="replace_id" id="replace_id" value="{{ $resultPro->replace_id }}">


            <div class="modal-body"> 
				
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
                        Replace Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" pattern="[1-9]" min="1" name="items_qty" id="items_qty" class="form-control" value="{{ $resultPro->order_qty }}" maxlength="8">
                    </div>
                    <input type="hidden" name="items_price" id="items_price" class="form-control" value="{{ $resultPro->p_unit_price }}">
                </div> 
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</form>
