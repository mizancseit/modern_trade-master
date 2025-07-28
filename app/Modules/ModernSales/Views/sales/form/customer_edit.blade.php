<!-- Default Size -->


<form action="{{ URL('modern-customer-edit-process') }}" method="post" name="editForm">

    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Customer Info</h4>
            </div>
            <div class="modal-body"> 
                <div class="row clearfix">
                   <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Customer Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Customer Name" name="customer_name" value="{{ $resultcus->name }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Mobile No: </label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Mobile No" name="mobile_no" value="{{ $resultcus->mobile }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="col-sm-6 col-md-4">
                            <label for="division">Customer code:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Customer code" name="customer_code" value="{{ $resultcus->customer_code }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label for="division">SAP code:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="SAP code" name="sap_code" value="{{ $resultcus->sap_code }}" autocomplete="off"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4"> 
                            <label for="division">Credit limit:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Credit limit" name="credit_limit" value="{{ $resultcus->credit_limit }}"autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">  
                        <div class="col-sm-6 col-md-6"> 
                            <label for="division">Define Officer:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                   <select id="executive_id" name="executive_id" class="form-control" data-live-search="true">
                                        <option value="">-- Select Officer --</option> 
                                         @foreach($resultFo as $resultFo)
                                        <option value="{{ $resultFo->id }}" @if ($resultcus->executive_id == $resultFo->id) {{ "selected" }} @endif>{{ $resultFo->display_name }}</option>
                                        @endforeach  
                                       
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6"> 
                             <label for="division">Shop Type:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select id="shop_type" name="shop_type" class="form-control" data-live-search="true">
                                        <option value="">-- Shop Type --</option> 
                                         @foreach($shopType as $shopType)
                                        <option value="{{ $shopType->route_id }}" @if ($resultcus->route_id == $shopType->route_id) {{ "selected" }} @endif>{{ $shopType->route_name }}</option>
                                        @endforeach  
 
                                    </select>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12"> 
                            <label for="division">Address:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Address" name="address" value="{{ $resultcus->address }}" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                </div>
            </div>

<div class="modal-footer">
 <input type="hidden" id="id" name="id" value="{{ $resultcus->customer_id }}">
 <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
 <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
</div>
</div>
</div>
</form>



