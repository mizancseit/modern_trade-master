<!-- Default Size -->


<form action="{{ URL('fo_target_edit_process') }}" method="post" name="editForm">

    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Product Info</h4>
            </div>
            <div class="modal-body">

                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                       <div class="row clearfix">
                        <div class="col-sm-12 col-md-6">
                            <label for="offerid">FO ID :*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" class="form-control" id="fo_id" name="fo_id" value="{{ $targetList->fo_id }}" required="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                             <label for="category">CATEGORY :*</label>
                             <div class="form-group">
                                <div class="form-line">

                                    <select class="form-control show-tick" name="category" required="">
                                        <option value="">Select Category</option>
                                        @foreach($pcategory as $cname)
                                        <option value="{{ $cname->id }}" @if ($targetList->cat_id == $cname->id) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                       
                    </div>
                   
                 <div class="row clearfix">
                    <div class="col-sm-12 col-md-6">
                         <label for="qty">Qty :*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" id="qty" name="qty" value="{{ $targetList->qty }}" required="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                    <label for="value">Value :*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Value" name="avg_value" value="{{ $targetList->avg_value }}" required="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-sm-12 col-md-6">
                         <label for="qty">Start Date :*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $targetList->start_date }}" required="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                    <label for="value">End Date :*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="date" class="form-control" name="end_date" value="{{ $targetList->end_date }}" required="" />
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


