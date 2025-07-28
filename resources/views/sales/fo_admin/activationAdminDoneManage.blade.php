 <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    
                    <div class="card">                        

                        <form action="{{ URL('/activation-submit') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                            <div class="body">                                 

                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="routes" name="routes" class="form-control show-tick" data-live-search="true" onchange="routeWiseRetailers(this.value)" required="">
                                        <option value="">-- Select Route --</option>
                                        @foreach($resultRoute as $routes)
                                            <option value="{{ $routes->route_id }}"> {{ $routes->rname }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group" id="showHiddenDiv">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="retailer" name="retailer" class="form-control show-tick" data-live-search="true" required="">
                                        <option value="">-- Select Retailer --</option>
                                        {{-- @foreach($resultRetailer as $retailer)
                                            <option value="{{ $retailer->retailer_id }}"> {{ $retailer->name }} </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <p></p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">business</i>
                                    </span>

                                    <select id="status" name="status" class="form-control show-tick" required="">
                                        <option value="">-- Select Status --</option>                                        
                                            <option value="0">Activation Request</option>
                                            <option value="1">Inactive Request</option>
                                    </select>
                                </div>
                               

                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">                                        
                                        <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Request Send</button>                                        
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>