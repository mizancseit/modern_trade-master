<!-- Default Size -->

<form action="{{ URL('/edit-submit') }}" method="post">
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
            <input type="hidden" name="skuID" id="skuID" value="{{ $resultPro->product_id }}">


            <div class="modal-body">
                <p></p>
                <div class="row">
                    <div class="col-sm-5">
                        Offer Group Type
                    </div>

                    @if(sizeof($resultCatDefault)>0)
                        @if($resultCatDefault->offer_type==2 || $resultCatDefault->offer_type==3)
                        <div class="col-sm-2">  
                                <input name="offer_group_type" type="radio" id="offer_group_separated" class="radio-col-red" value="1" data-toggle="modal" @if($resultPro->offer_group_type =='1') checked="" @endif>
                            <label for="offer_group_separated" style="font-size:16px;" title="Separated Offer">Separated</label>
                        </div>
                        @endif
                        @if($resultCatDefault->offer_type==1 || $resultCatDefault->offer_type==3)
                        <div class="col-sm-3"  style="text-align: center;">   
                                <input name="offer_group_type" type="radio" id="roffer_group_combine" class="radio-col-red" value="2" data-toggle="modal" @if($resultPro->offer_group_type =='2') checked="" @endif>
                            <label for="roffer_group_combine" style="font-size:16px;" title="Combine Offer">Combine</label>  
                        </div>
                        @endif
                    @endif
                    
                    <!-- <div class="col-sm-2">                                    
                                            
                            <input name="offer_group_type" type="radio" id="offer_group_separated" class="radio-col-red" value="1" data-toggle="modal" @if($resultPro->offer_group_type =='1') checked="" @endif>
                            <label for="offer_group_separated" style="font-size:16px;" title="Separated Offer">Separated</label>
                    </div>
                   

                    <div class="col-sm-3"  style="text-align: center;">   
                            <input name="offer_group_type" type="radio" id="roffer_group_combine" class="radio-col-red" value="2" data-toggle="modal" @if($resultPro->offer_group_type =='2') checked="" @endif>
                            <label for="roffer_group_combine" style="font-size:16px;" title="Combine Offer">Combine</label>  
                    </div> -->
                </div>
				
				
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
                        Order Qty
                    </div>

                    <div class="col-sm-5">
                        <input type="number" pattern="[1-9]" min="1" name="items_qty" id="items_qty" class="form-control" value="{{ $resultPro->order_qty }}" maxlength="8">
                    </div>
                    <input type="hidden" name="items_price" id="items_price" class="form-control" value="{{ $resultPro->p_unit_price }}">
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
