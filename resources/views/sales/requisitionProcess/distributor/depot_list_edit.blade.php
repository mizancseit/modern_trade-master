
   <form action="{{ URL('depot/depot_list_edit_process') }}" method="post" name="editForm">

    {{ csrf_field() }}    <!-- token -->

     <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Depot Info</h4>
            </div>
            <div class="modal-body">

                    <div class="row clearfix">
                     <div class="col-sm-12 col-md-12">
                       <div class="row clearfix">
                         <div class="col-sm-12 col-md-6">
                            <label for="depoName"> Depot Name * </label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Type Depot Name" name="depoName" id="depoName" value="{{ $depot->depot_name }}" required="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                         <label for="in_charge">In-Charge Name :*</label>
                         <div class="form-group">
                            <div class="form-line">
                                <select class="form-control show-tick" name="in_charge" required="">
                                    <option value="">Select In-Charge</option>
                                    @foreach($in_charge as $incharge)
                                        <option value="{{  $incharge->id }}" @if ($incharge->id == $depot->depot_in_charge) {{ "selected" }} @endif >{{ $incharge->display_name }}</option>
                                        @endforeach                                   
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12 col-md-6">
                        <label for="division">Division :*</label>
                        <div class="form-group ">
                            <div class="form-line">
                                <select class="form-control show-tick" name="division" required="">
                                    <option value="">Select Division</option>
                                    @foreach($division as $div)
                                    <option value="{{ $div->div_id }}" @if ($div->div_id == $depot->division) {{ "selected" }} @endif >{{ $div->div_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="location">Location :*</label>
                        <div class="form-group ">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Type Location" name="location" id="location" value="{{ $depot->depot_location }}" required="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row clearfix">
                   <div class="col-sm-12 col-md-6">
                    <label for="opening_balance">Opening Balance :*</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" placeholder="Type Opening Balance" name="opening_balance" id="opening_balance" value="{{ $depot->opening_balance }}" required="" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                   <label for="current_balance">Current Balance :*</label>
                   <div class="form-group">
                    <div class="form-line">
                        <input type="text" class="form-control" placeholder="Type Current Balance" name="current_balance" id="current_balance" value="{{ $depot->depot_current_balance }}" required="" />
                    </div>
                </div>
            </div>

        </div>
        <div class="row clearfix">
           <div class="col-sm-12 col-md-6">
            <label for="current_sales">Current Sales :*</label>
            <div class="form-group">
                <div class="form-line">
                    <input type="text" class="form-control" placeholder="Type Current Sales" name="current_sales" id="current_sales" value="{{ $depot->depot_current_sales }}" required="" />
                </div>
            </div>
        </div> 
        <div class="col-sm-12 col-md-6">
         <label for="market_credit">Market Credit :*</label>
         <div class="form-group">
            <div class="form-line">
                <input type="text" class="form-control" placeholder="Type Market Credit" name="market_credit" id="market_credit" value="{{ $depot->market_credit }}" required="" />
            </div>
        </div>
    </div>
</div>    
</div>
</div>
</div>
<div class="modal-footer">
    <input type="hidden" id="id" name="id" value="{{ $depot->depot_id }}">
   <button type="submit" name="submit" class="btn btn-link waves-effect">CHANGE</button>
   <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
</div>
</form>
