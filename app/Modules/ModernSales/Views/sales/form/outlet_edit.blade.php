<form action="{{ URL('/modern-outlet-edit-process') }}" method="POST">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                     <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Outlet</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                       <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-6"> 
                                                 <label for="division">Customer:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select id="shop_type" name="customer_id" class="form-control" data-live-search="true">
                                                             
                                                              <option value="">-- Select Customer --</option> 
                                                             @foreach($resultCustomer as $cusList)
                                                            <option value="{{ $cusList->customer_id }}" @if ($resultcus->customer_id == $cusList->customer_id) {{ "selected" }} @endif>{{ $cusList->name }}</option>
                                                            @endforeach  
                                                        </select>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <label for="division">Outlet Name:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="Outlet Name" name="outlet_name" value="{{$resultcus->pname}}" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        </div>
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-sm-6 col-md-4">
                                                <label for="division">Mobile No:</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Mobile No" name="mobile_no" value="{{$resultcus->mobile}}" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                <label for="division">SAP code:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input required type="text" class="form-control" placeholder="SAP code" name="sap_code" value="{{$resultcus->sap_code}}" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4"> 
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
                                                        <input required type="text" class="form-control" placeholder="Address" name="address" value="{{$resultcus->address}}" autocomplete="off" />
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                        <div class="modal-footer">
                           <input type="hidden" id="id" name="id" value="{{ $resultcus->party_id }}">
                             <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
                             <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
