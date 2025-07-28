<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-10">
                        <h2>RETAILERS</h2>
                    </div>

                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">UPDATE</button>
                    </div>

                </div>                           
            </div>
            <div class="body">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Retailer Name</th>
                                <th>Opening Balance</th>
                                <th>Update Balance</th>
                            </tr>
                        </thead>
                       

                        <tbody>
                            @if(sizeof($resultRetailer) > 0)
                                @php
                                $serial = 1;
                                @endphp

                                @foreach($resultRetailer as $rowRetailer)
                                <tr>
                                    <th>{{ $serial }}</th>
									 <input type="hidden" id="retailer_id{{$serial}}" name="retailer_id[]" value="{{ $rowRetailer->retailer_id }}">
                                    <th>{{ $rowRetailer->name }}</th>
									<th>{{ $rowRetailer->opening_balance }}</th>
                                    <th><input type="number" class="form-control" id="opening_balance{{$serial}}" name="opening_balance[]" pattern="[1-9]" value="" style="width: 140px;">
									
                                   
                                </tr>
                                @php
                                $serial ++;
                                @endphp

                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <p></p>
                    <div class="row">
                        <div class="col-sm-10" style="text-align: right;">
                            &nbsp;
                        </div>

                        <div class="col-sm-2" style="text-align: right;">
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">UPDATE</button>
                        </div>
                    </div>                    
            </div>
        </div>
    </div>
</div>