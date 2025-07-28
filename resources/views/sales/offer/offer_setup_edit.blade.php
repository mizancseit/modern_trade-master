<!-- Default Size -->


<form action="{{ URL('offer/offer_setup_edit_process') }}" method="post" name="editForm">

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
                                                <div class="col-sm-12 col-md-12">
                                                    <div class="col-sm-4 col-md-4">
                                                        <label for="category">Product Category : *</label>
                                                    </div>
                                                    <div class="col-sm-8 col-md-8">
                                                        <div class="form-group">
                                                            <div class="form-line">

                                                                <select class="form-control show-tick" name="category" id="category" required="" onchange="getProductEdit(this.value)">
                                                                    <option value="">Select Category</option>
                                                                    @foreach($pcategory as $cname)
                                                                    <option value="{{ $cname->id }}" @if ($productById->gid == $cname->id) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                                                    @endforeach
                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>




                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-1</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s1" value="{{ $productById->s1 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p1" value="{{ $productById->p1 }}" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-2</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s2" value="{{ $productById->s2 }}" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p2" value="{{ $productById->p2 }}" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-3</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s3" value="{{ $productById->s3 }}" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p3" value="{{ $productById->p3 }}" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-4</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s4" value="{{ $productById->s4 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p4" value="{{ $productById->p4 }}" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-5</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s5" value="{{ $productById->s5 }}" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p5" value="{{ $productById->p5 }}" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>


                                            </div>
                                            <hr>
                                             <div class="row clearfix">
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-6</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s6" value="{{ $productById->s6 }}" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p6" value="{{ $productById->p6 }}"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-7</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s7" value="{{ $productById->s7 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p7" value="{{ $productById->p7 }}"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-8</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s8" value="{{ $productById->s8 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p8" value="{{ $productById->p8 }}"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-9</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s9" value="{{ $productById->s9 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p9" value="{{ $productById->p9 }}"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-10</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s10" value="{{ $productById->s10 }}"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p10" value="{{ $productById->p10 }}"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>

                                            </div>
                                    </div>
                                </div>
            <div class="modal-footer">
             <input type="hidden" id="id" name="id" value="{{ $productById->id }}">
             <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
             <button type="button" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
         </div>
       </div>
    </div>

</form>


