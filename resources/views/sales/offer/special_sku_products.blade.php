    @extends('sales.masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            SPECIAL SKU OFFER PRODUCTS 
                            <small> 
                               <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Special SKU Products
                           </small>
                       </h2>
                   </div>

               </div>

           </div>

           @if(Session::has('success'))
           <div class="alert alert-success">
            {{ Session::get('success') }}                        
        </div>
        @endif


        <div class="row clearfix">


            <!-- #END# Exportable Table -->

            <div class="card">
                <div class="header">
                    <h2>
                        Special SKU Wise Product
                    </h2>
                </div>

                <div class="body">

                  <button type="button"  id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Offer Design</button>
                  <br>
                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('offer/special_sku_product_save') }}" method="post">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Product</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                     <div class="col-sm-12 col-md-12">
                                       <div class="row clearfix">
                                            <div class="col-sm-12 col-md-3">
                                                <label for="category">TYPE :*</label>
                                                <div class="form-group ">
                                                    <div class="form-line">

                                                        <select class="form-control show-tick" name="ptype" required>
                                                            <option value="">Select Type</option>
                                                            <option value="1">Lighting</option>
                                                            <option value="2">Accesories</option>
                                                            <option value="3">FAN</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-3">
                                             <label for="category">CATEGORY :*</label>
                                                 <div class="form-group">
                                                    <div class="form-line">

                                                        <select class="form-control show-tick" name="category" required="" onchange="getSkuProduct(this.value)">
                                                            <option value="">Select Category</option>
                                                            @foreach($pcategory as $cname)
                                                            <option value="{{ $cname->id }}">{{ $cname->name }}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-4">
                                           <label for="sku">SKU :*</label>
                                               <div class="form-group">

                                                    <div class="form-line" id="sku">
                                                        <select class="form-control show-tick" name="sku" required="">
                                                            <option value="">Select Product</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-sm-12 col-md-2">
                                                <label for="category">SLAB :*</label>
                                                <div class="form-group ">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Slab" name="slab" required="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                    <div class="row clearfix">
                                        <div class="col-sm-12 col-md-4">
                                         <label for="category">OFFER GROUP :*</label>
                                             <div class="form-group">
                                                <div class="form-line">

                                                    <select class="form-control show-tick" name="groupCat" required="" onchange="getRegularProduct(this.value)">
                                                        <option value="">Select Category</option>
                                                        @foreach($pcategory as $cname)
                                                        <option value="{{ $cname->id }}">{{ $cname->name }}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                       <label for="product">PRODUCTS NAME :*</label>
                                           <div class="form-group">

                                                <div class="form-line" id="product">
                                                    <select class="form-control show-tick" name="product" required="">
                                                        <option value="">Select Product</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-2">
                                               <label for="qty">QTY :*</label>
                                               <div class="form-group">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" placeholder="Qty" name="qty" required="" />
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
                                            <label for="andCat">OFFER GROUP :</label>
                                            <div class="form-group">
                                                <div class="form-line">

                                                    <select class="form-control show-tick" name="andCat" onchange="getAndProduct(this.value)">
                                                        <option value="">Select Category</option>
                                                        @foreach($pcategory as $cname)
                                                        <option value="{{ $cname->id }}">{{ $cname->name }}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <label for="andProducts">PRODUCTS NAME :</label>
                                            <div class="form-group">

                                                <div class="form-line" id="andProducts">
                                                    <select class="form-control show-tick" name="andProducts">
                                                        <option value="">Select Product</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-2">
                                            <label for="and_qty">QTY :</label>
                                            <div class="form-group">
                                                <div class="form-line" id="and_qty">
                                                    <input type="text" class="form-control" placeholder="Qty" name="and_qty" />
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
        </form>
    </div>
</div>
</div>
<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
        <thead>
            <tr>
                <th>SL</th>
                <th>CATEGORY</th>
                <th>SKU</th>
                <th>SLAB</th>
                <th>PRODUCTS NAME</th>
                <th>QTY</th>
                <th>AND PRODUCTS</th>
                <th>AND QTY</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            @if(sizeof($result_sku_products) > 0)   
            @php
            $i =1;
            @endphp

            @foreach($result_sku_products as $regularProduct)

            @php
            $sku_products_name  = DB::table('tbl_product')
                        ->select('id','name')
                        ->where('id', $regularProduct->sku_id)
                        ->first();

            $and_products_name  = DB::table('tbl_product')
            ->select('id','name')
            ->where('id', $regularProduct->and_pid)
            ->first();


            @endphp
            <tr>
                <td>{{$i++ }}</td>
                <td>{{$regularProduct->cName}}</td>
                <td> @if(sizeof($sku_products_name)>0) {{$sku_products_name->name}} @endif</td>
                <td>{{$regularProduct->slab}}</td>
                <td>{{$regularProduct->pName}}</td>
                <td>{{$regularProduct->qty}}</td>
                <td> @if(sizeof($and_products_name)>0) {{$and_products_name->name}} @endif</td>
                <td>{{$regularProduct->and_qty}}</td>
                
                <td>
                 
                    <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editSpecialSku('{{ $regularProduct->id }}','{{ $regularProduct->offerProId }}','{{ $regularProduct->catid }}','{{ $regularProduct->and_pro_cat_id }}','{{ $regularProduct->and_pid }}')" style="width: 70px;">
                    <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteSpecialSku('{{ $regularProduct->id}}')" style="width: 70px; margin-top: 0px;">
                </td>
            </tr>


                @endforeach
                @else
                <tr>
                    <th colspan="9">No record found.</th>
                </tr>
                @endif     

            </tbody>
            <tbody>
            <tfoot>
                <tr>
                    <th>SL</th>
                    <th>CATEGORY</th>
                    <th>SKU</th>
                    <th>SLAB</th>
                    <th>PRODUCTS NAME</th>
                    <th>QTY</th>
                    <th>AND PRODUCTS</th>
                    <th>AND QTY</th>
                    <th>ACTION</th>
                </tr>
            </tfoot>
            <tbody>

        </table>
    </div>
</div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
@endsection
