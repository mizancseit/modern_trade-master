<!-- Default Size -->

<form action="{{ URL('/eshop-edit-submit') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="defaultModalLabel">Order Product Edit</h4>
            </div>
            <input type="hidden" name="id" id="id" value="{{ $resultPro->order_det_id }}">
             <input type="hidden" name="orderid" id="orderid" value="{{ $resultPro->order_id }}">
            <input type="hidden" name="partyid" id="partyid" value="{{ $partyid }}">
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer_id }}">
            <input type="hidden" name="catid" id="catID" value="{{ $catid }}">
            <input type="hidden" name="product_id" id="product_id" value="{{ $resultPro->product_id }}">


            <div class="modal-body"> 
				
                <div class="row">
                    <div class="col-sm-3">
                        Product Name
                    </div>

                    <div class="col-sm-8">
                        {{ $resultPro->name }}
                    </div>
                </div>
                <p></p>
                <div class="row">
                    <div class="col-sm-3">
                        Order Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="hidden" pattern="[1-9]" min="1" name="order_item_id" id="items_qty" class="form-control" value="{{ $resultPro->order_det_id }}" maxlength="8">

                        <input type="number" pattern="[1-9]" min="1" name="items_qty" id="items_qty" class="form-control" value="{{ $resultPro->order_qty }}" maxlength="8">
                         <input type="hidden" name="items_price" id="items_price" class="form-control" value="{{ $resultPro->p_unit_price }}">
                    </div>
                     <div class="col-sm-3">
                        <input type="number" pattern="[1-9]" min="1" name="commission" id="commission" class="form-control" placeholder="Discount %" value="@if(sizeof($resultPro)>0){{$resultPro->item_discount}}@endif" maxlength="8">
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
