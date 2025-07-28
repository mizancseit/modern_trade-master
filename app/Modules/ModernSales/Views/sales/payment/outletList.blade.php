<div class="card">
    <div class="row">
        <div class="col-sm-9" style="text-align: left;"></div>
        <div class="col-sm-2" style="text-align: right;">
            <br>
            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
            <input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
        </div>
         <div class="col-sm-1"></div>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>

                        <th>SL</th>
                        <th>Customer Name</th>
                        <th>Opening Balance</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>SL</th>
                        <th>Customer Name</th>
                        <th>Opening Balance</th>
                    </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultParty) > 0)   
                    @php
                    $serial =1;
                    @endphp

                    @foreach($resultParty as $retailers)

                     
                    <tr>
                        <th>{{ $serial }}</th>
                        <th>{{ $retailers->name }}</th>
                        @if($retailers->opening_balance!=0)
                         <th>{{$retailers->opening_balance}}</th>
                         @else
                         <th><input type="number" class="form-control" id="opening_balance{{$serial}}" name="opening_balance[]" maxlength="3" pattern="[1-9]" min="1" value="" style="width: 80px;"></th>
                         <input type="hidden" id="customer_id{{$serial}}" name="customer_id[]" value="{{  $retailers->customer_id }}">
                         <input type="hidden" id="sap_code{{$serial}}" name="sap_code[]" value="{{  $retailers->sap_code }}">  
                         @endif
                        
                       
                    </tr>
                    @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="3">No record found.</th>
                    </tr>
                @endif    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>