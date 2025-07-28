<!-- Default Size -->


<form action="{{ URL('offer/special_product_edit_process') }}" method="post" name="editForm">

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
                        <div class="col-sm-12 col-md-4">
                            <label for="ptype">Type :*</label>
                            <div class="form-group ">
                                <div class="form-line">

                                    <select class="form-control show-tick" id="ptype" name="ptype" required>
                                        <option value="">Select Type</option>
                                        <option value="1">Lighting</option>
                                        <option value="2">Accesories</option>
                                        <option value="3">FAN</option>
                                    </select>

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
                                        <option value="{{ $cname->id }}" @if ($productById->catid == $cname->id) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2">
                            <label for="category">SLAB :*</label>
                            <div class="form-group ">
                                <div class="form-line">
                                    <input type="text" class="form-control" placeholder="Slab" name="slab" value="{{ $productById->slab }}" required="" />
                                </div>
                            </div>
                        </div>


                    </div>
                   
                    <div class="row clearfix">
                        <div class="col-sm-12 col-md-4">
                            <label for="category">Offer Category :*</label>
                            <div class="form-group">
                                <div class="form-line">

                                    <select class="form-control show-tick" name="groupCat" id="groupCat" required="" onchange="getProductEdit(this.value)">
                                        <option value="">Select Category</option>
                                        @foreach($pcategory as $cname)
                                        <option value="{{ $cname->id }}" @if ($productById->offerGroupId == $cname->id) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label for="product">Product Name :*</label>
                            <div class="form-group">

                                <div class="form-line" id="productedit">
                                    <select class="form-control show-tick" name="product" required="">

                                     @foreach($product as $pname)
                                     <option value="{{ $pname->id }}" @if ($productById->pid == $pname->id) {{ "selected" }} @endif >{{ $pname->name }}</option>
                                     @endforeach                                
                                 </select>
                             </div>
                         </div>
                     </div>
                     <div class="col-sm-12 col-md-2">
                     <label for="qty">Qty :*</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Qty" id="qty" name="qty" value="{{ $productById->qty }}" required="" />
                            </div>
                        </div>
                    </div>
                 </div>
                 <div class="row clearfix">
                    <div class="col-sm-12 col-md-12">
                        <h2 align="center">AND</h2>
                    </div>
                </div>
                 <div class="row clearfix">
                    <div class="col-sm-12 col-md-4">
                     <label for="andCat">Offer Category : </label>
                         <div class="form-group">
                            <div class="form-line">

                                <select class="form-control show-tick" name="andCat" onchange="getSkuProduct(this.value)">
                                    <option value="">Select Category</option>
                                    @foreach($pcategory as $cname)
                                    <option value="{{ $cname->id }}" @if ($productById->and_pro_cat_id == $cname->id) {{ "selected" }} @endif >{{ $cname->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="sku">Product Name :</label>
                       <div class="form-group">

                            <div class="form-line" id="sku1">
                                <select class="form-control show-tick" name="sku">
                                    <option value="">Select Product</option>
                                    @foreach($sku as $skuname)
                                     <option value="{{ $skuname->id }}" @if ($productById->and_pid == $skuname->id) {{ "selected" }} @endif >{{ $skuname->name }}</option>
                                     @endforeach

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-2">
                     <label for="and_qty">Qty :</label>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" placeholder="Qty" id="and_qty" name="and_qty" value="{{ $productById->and_qty }}" />
                            </div>
                        </div>
                    </div>
                </div>
                 
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
<script type="text/javascript">
  document.forms['editForm'].elements['ptype'].value={{$productById->ptype}}
</script> 

