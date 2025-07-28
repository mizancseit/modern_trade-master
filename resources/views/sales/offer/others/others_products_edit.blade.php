<form action="{{ URL('/offer/other-edit-process') }}" method="post">
    {{ csrf_field() }}    <!-- token -->
    <div class="modal-dialog" role="document">  
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" style="color:white;" id="defaultModalLabel">UPDATE</h4>
            </div>

            <div class="modal-body">
                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                        <div class="row clearfix">

                            <input type="hidden" name="id" value="{{$productById->id}}">

                            <div class="col-sm-12 col-md-6">
                                <label for="category">TYPE :*</label>
                                <div class="form-group ">
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="ptype" required>
                                            <option value="1" @if($productById->business_type==1) selected="" @endif>Lighting</option>
                                            <option value="2" @if($productById->business_type==2) selected="" @endif>Accesories</option>
                                            <option value="3" @if($productById->business_type==3) selected="" @endif>FAN</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                <label for="category">OFFER GROUP ID :*</label>
                                <div class="form-group ">
                                    <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Offer ID" name="group_id" value="{{ $productById->group_id }}" required="" />
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12" style="margin-bottom: 0;">
                                <label for="category">CATEGORY :*</label>
                                <div class="form-line" style="height: 200px; overflow: scroll; overflow-x: hidden;">
                                    <div class="demo-checkbox">
                                        @foreach($pcategory as $cname)
                                            <input type="checkbox" id="md_checkbox_2{{ $cname->id }}" class="filled-in chk-col-red"  name="categorys[]" value="{{ $cname->id }}" @if($productById->id==$cname->svwid) checked="checked" @endif>

                                            <label for="md_checkbox_2{{ $cname->id }}">{{ $cname->name }}</label> <br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>











                            {{-- <div class="col-sm-12 col-md-6">
                                <label for="category">CATEGORY :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="category" required="">
                                            @foreach($pcategory as $cname)
                                            <option value="{{ $cname->id }}" @if($productById->categoryid==$cname->id) selected="" @endif>{{ $cname->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> --}}

                            

                        </div>
                        <div class="row clearfix">

                            <div class="col-sm-12 col-md-6">
                                <label for="qty">MIN SLAB :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Min Slab" name="min" value="{{$productById->min}}" required="" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6">
                                <label for="qty">MAX SLAB :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Max Slab" name="max" value="{{$productById->max}}" required="" />
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row clearfix">
                            <div class="col-sm-12 col-md-6">
                                <label for="qty">COMMISSION:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="number" class="form-control" placeholder="Commission"  value="{{$productById->commission_rate}}" name="commission" required="" />
                                    </div>
                                </div>
                            </div> 

                            <div class="col-sm-12 col-md-6">
                                <label for="category">STATUS :*</label>
                                <div class="form-group ">
                                    <div class="form-line">
                                        <select class="form-control show-tick" name="status" required>
                                            <option value="1" @if($productById->status==1) selected="" @endif>Active</option>
                                            <option value="2" @if($productById->status==2) selected="" @endif>Inactive</option>             
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</form>