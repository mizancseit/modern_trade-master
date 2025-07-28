@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 style="padding-top: 30px;">
                            NEW ORDER
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Visit </a> / New Order
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-3">
                        <a href="{{ URL('/bucket') }}">
                            <div class="info-box-2 bg-red">
                                <div class="icon">
                                    <i class="material-icons">shopping_cart</i>
                                </div>
                                <div class="content">
                                    <div class="text">NEW ORDERS</div>
                                    <div class="number count-to" data-from="0" data-to="20000" data-speed="1000" data-fresh-interval="20">20000</div>
                                </div>
                            </div>
                        </a>
                        
                    </div>
                </div>
            </div>
            
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>STORE</h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select class="form-control show-tick">
                                    <option value="">-- Please select category --</option>
                                    <option value="10">GLS Bulb</option>
                                    <option value="20">Tube</option>                                    
                                </select>
                                <select class="form-control show-tick">
                                    <option value="">-- Please select subcategory--</option>             
                                </select>
                                <p style="padding-bottom: 10px;"></p>
                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">
                                        <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">SEARCH</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>STORE PRODUCT</h2>                            
                        </div>
                        <div class="body">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Name</th>
                                            <th>Order Qty</th>
                                            <th>Value</th>
                                            <th>Wastage Qty</th>                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Name</th>
                                            <th>Order Qty</th>
                                            <th>Value</th>
                                            <th>Wastage Qty</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                            <th>1</th>
                                            <th>200W CLEAR B-22</th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required>
                                            </th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required></th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required></th>                                            
                                        </tr>
                                        <tr>
                                            <th>2</th>
                                            <th>200W CLEAR E-27</th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required>
                                            </th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required></th>
                                            <th><input type="number" class="form-control" name="name" maxlength="3" required></th>                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <p></p>
                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-2">
                                        <button type="button" class="btn bg-pink btn-block btn-lg waves-effect">ADD CART</button>
                                    </div>
                                </div>

                                
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection