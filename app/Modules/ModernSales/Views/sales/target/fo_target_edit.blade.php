<!-- Default Size -->


<form action="{{ URL('modern-target-edit-process') }}" method="post" name="editForm">

    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Target Info</h4>
            </div>
            <div class="modal-body">

                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                        <div class="row clearfix">
                            <div class="col-sm-12 col-md-6">
                                 <label for="qty">Year :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        
                                        <select class="form-control show-tick" name="year" required="">
                                       
                                            <option value="2020" @if ($targetList->year == 2020) {{ "selected" }} @endif >2020</option>
                                            <option value="2019" @if ($targetList->year == 2019) {{ "selected" }} @endif >2019</option>
                                          
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                            <label for="value">Month :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                         <select class="form-control show-tick" name="month" required="">
                                        <option value="">Select Month</option>
                                        @foreach($monthList as $month)
                                        <option value="{{ $month->id }}" @if ($targetList->month == $month->id) {{ "selected" }} @endif >{{ $month->month }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                       <div class="row clearfix">
                        
                        <div class="col-sm-12 col-md-6">
                             <label for="category">Customer :*</label>
                             <div class="form-group">
                                <div class="form-line">
                                     <select class="form-control show-tick" name="customer_id" required="">
                                        <option value="">Select Customer</option>
                                        @foreach($customerList as $cname)
                                        <option value="{{ $cname->customer_code }}" @if ($targetList->customer_id == $cname->customer_code) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="value">Value :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Value" name="value" value="{{ $targetList->value }}" required="" />
                                    </div>
                                </div>
                            </div>
                       
                    </div> 
                
        </div>
    </div>
</div>

<div class="modal-footer">
 <input type="hidden" id="id" name="id" value="{{ $targetList->id }}">
 <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
 <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
</div>
</div>
</div>
</form>


