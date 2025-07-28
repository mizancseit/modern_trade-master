    @extends('sales.masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            REGULAR OFFER PRODUCTS 
                            <small> 
                               <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Regular Products
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
                        Regular Offer Product Set Up
                    </h2>
                </div>

                <div class="body">

                  <button type="button"  id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Product</button>
                  <br>
                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('offer/regular_offer_product_save') }}" method="post">
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
                                         <div class="col-sm-12 col-md-4">
                                            <label for="category">Type :*</label>
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

                                        <div class="col-sm-12 col-md-6">
                                             <label for="category">CATEGORY :*</label>
                                             <div class="form-group">
                                                <div class="form-line">

                                                    <select class="form-control show-tick" name="category" required="">
                                                        <option value="">Select Category</option>
                                                        @foreach($pcategory as $cname)
                                                        <option value="{{ $cname->id }}">{{ $cname->name }}</option>
                                                        @endforeach
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
                                         <label for="category">Offer Category :*</label>
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
                                       <label for="product">Product Name :*</label>
                                       <div class="form-group">

                                        <div class="form-line" id="product">
                                            <select class="form-control show-tick" name="product" required="">
                                                <option value="">Select Product</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-2">
                                   <label for="qty">Qty :*</label>
                                   <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Qty" name="qty" required="" />
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-12 col-md-6">
                                   <label for="qty">Qty :*</label>
                                   <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Qty" name="qty" required="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <label for="value">Value :*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Value" name="value" required="" />
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
                <th>Offer No</th>
                <th>Category</th>
                <th>Slab</th>
                <th>Product name</th>
                <th>Qty</th>
                <th>Value</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(sizeof($resultRegularOffer) > 0)   
            @php
            $i =1;
            @endphp

            @foreach($resultRegularOffer as $regularProduct) 
            <tr>
                <td>{{$i++ }}</td>
                <td>{{$regularProduct->oid }}</td>
                <td>{{$regularProduct->cName}}</td>
                <td>{{$regularProduct->slab}}</td>
                <td>{{$regularProduct->pName}}</td>
                <td>{{$regularProduct->qty}}</td>
                <td>{{$regularProduct->value}}</td>
                <td>
                 
                    <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editRegularProduct('{{ $regularProduct->id }}','{{ $regularProduct->offerProId }}')" style="width: 70px;">
                    <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteRegularProduct('{{ $regularProduct->id}}')" style="width: 70px; margin-top: 0px;">
                </td>
            </tr>


                @endforeach
                @else
                <tr>
                    <th colspan="7">No record found.</th>
                </tr>
                @endif     

            </tbody>
            <tbody>
            <tfoot>
                <tr>
                    <th>SL</th>
                    <th>Offer No</th>
                    <th>Category</th>
                    <th>Slab</th>
                    <th>Product name</th>
                    <th>Qty</th>
                    <th>Value</th>
                    <th>Action</th>
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
