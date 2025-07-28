<form action="{{ URL('/eshop-category-update') }}" method="POST">
    {{ csrf_field() }}    <!-- token --> 
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #A62B7F">
                 <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Category Edit</h4>
            </div>
            <div class="modal-body">

                <div class="row clearfix">
                    <div class="col-sm-12 col-md-12"> 
                        <div class="col-sm-6 col-md-6"> 
                            <label for="division">Channel:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                   <select id="channel" name="channel" class="form-control" data-live-search="true" required="">
                                        <option value="">-- Select Channel --</option> 
                                          @foreach($resultChannel as $row)
                                        <option value="{{ $row->business_type_id }}" @if ($category->gid == $row->business_type_id) {{ "selected" }} @endif >{{ $row->business_type }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Company Code:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Company Code" name="company_code"  
                                    value="{{$category->g_code}}" autocomplete="off"/>
                                </div>
                            </div>
                        </div> 
                        <div class="col-sm-6 col-md-6">
                            <label for="division">Category Name:*</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input required type="text" class="form-control" placeholder="Product Name" name="category_name" value="{{$category->name}}" autocomplete="off"/>
                                </div>
                            </div>
                        </div> 

                    </div> 
                    
                </div>
            </div>
    <div class="modal-footer">
        <input type="hidden" id="id" name="id" value="{{ $category->id }}">
        <button type="submit" name="submit" class="btn btn-link waves-effect">UPDATE</button>
        <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
    </div>
</form>