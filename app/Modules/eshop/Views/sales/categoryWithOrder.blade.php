@extends('eshop::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            
            <div class="block-header">
                <div class="row">
                    
                    <div class="col-lg-9">
                        <h2 style="padding-bottom: 10px;">
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Sales Order </a> / New Order
                            </small>
                        </h2>
                    </div>
                    
                    <div class="col-lg-3">
                        <button type="button"  id="ref" class="btn btn-primary btn-md" data-toggle="modal" data-target="#defaultModal1"><i class="material-icons">chrome_reader_mode</i> CSV Upload</button> 
                        @if(sizeof($resultCart)>0)
                        <a href="{{ URL('/eshop-bucket/'.$customer_id.'/'.$partyid) }}" style="text-decoration: none;" title="Click To Bucket Details"><button class="btn btn-success btn-rounded btn-md"><i class="material-icons">shopping_cart</i> Cart <span class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif" data-speed="1000" data-fresh-interval="20"> @if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif </span></button> </a>
                        @else
                        <button class="btn btn-success btn-rounded btn-md"><i class="material-icons">shopping_cart</i> Cart <span class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif" data-speed="1000" data-fresh-interval="20"> @if(sizeof($resultCart)>0) {{ $resultCart->total_order_qty }} @else 0 @endif </span></button>
                        @endif
                        
                    </div>
                    
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif  

            @if(Session::has('failed'))
                <div class="alert alert-danger">
                {{ Session::get('failed') }}                        
                </div>
            @endif    
            <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
                <form action="{{ URL('/e-shop-csv-file') }}/{{$partyid}}/{{$customer_id}}" enctype="multipart/form-data"  method="POST">
                    {{ csrf_field() }}  
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #A62B7F">
                                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Upload csv file</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row clearfix">
                                    <!-- //'+$partyid+'/'+$customer_id -->
                                    <!-- <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-6 col-md-12">
                                            <label for="division">Customer *</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <select id="customer" class="form-control show-tick"  name="customer_id" >
                                                        <option value="">-- Please select Customer --</option>
                                                        @foreach($customerResult as $customer)
                                                        <option value="{{$customer->customer_id}}"> {{ $customer->name }} </option>
                                                        @endforeach                         
                                                    </select>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> -->
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-6" style="float: right; left: 20%;">
                                            <label for="division"></label> 
                                            <a href="/public/demo/downloadExcel/order_csv_demo.xlsx" download>Download CSV format</a>
                                        </div> 
                                    </div>
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-sm-6 col-md-12">
                                            <label for="division">CSV File *</label>
                                            <div class="form-group">
                                                <div class="form-line">
                                                    <input required type="file" class="form-control" placeholder="File" name="csvFile"  autocomplete="off"/>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit" class="btn btn-link waves-effect">Save</button>
                                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>           
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultParty)>0) {{ $resultParty->name }} @else STORE @endif </h2>                       
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allOrderProducts()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->name }}</option>
                                    @endforeach                           
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/eshop-add-to-cart') }}" method="POST">
                {{ csrf_field() }}  
                <input type="hidden" id="customer_id" name="customer_id" value="{{ $customer_id }}">
                <input type="hidden" id="party_id" name="party_id" value="{{ $partyid }}">
                <input type="hidden" id="retailer_id" name="retailer_id" value="{{ $partyid }}">
                <input type="hidden" id="cat_id" name="cat_id" value="">

                <div id="showHiddenDiv"> 
                </div>
            </form> 

        </div>
    </section>
@endsection