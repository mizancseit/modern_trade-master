@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 style="padding-top: 30px;">
                            NEW WASTAGE REQUISITION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition / Wastage Requisition
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-3">                        
                        <div class="info-box-2 bg-red">
                            <div class="icon">
                                <i class="material-icons">shopping_cart</i>
                            </div>
                            <a href="{{ URL('/dist/was-req-bucket/'.$req_id) }}" style="text-decoration: none;" title="Click To Bucket Details">
                                <div class="content">
                                    <div class="text">REQUISITION LIST</div>
                                     <div class="number count-to" data-from="0" data-to="@if(sizeof($resultCart)>0) {{ $resultCart[0]->grand_total_value }} @else 0 @endif" data-speed="1000" data-fresh-interval="20">@if(sizeof($resultCart)>0) {{ $resultCart[0]->grand_total_value }} @else 0000.00 @endif</div>
								</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif 


            
            {{-- <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultReqList)>0) {{ $resultReqList[0]->display_name }} @else Depot in Charge @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="wastageRequisitionProducts()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->g_code.' : '.$categories->name }}</option>
                                    @endforeach                           
                                </select>
                               
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}

            <form action="{{ URL('/dist/was-req-add-to-product') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->
                <input type="hidden" id="point_id" name="point_id" value=" @if(sizeof($resultReqList)>0){{$resultReqList[0]->point_id}}@endif ">
                <input type="hidden" id="req_id" name="req_id" value="{{ $req_id }}">
             
                <div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">                
                <div class="row">
                    <div class="col-sm-10">
                        <h2>PRODUCTS</h2>
                    </div>

                    <div class="col-sm-2" style="text-align: right;">
                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                    </div>

                </div>                           
            </div>
            <div class="body">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Group</th>
                                <th>Product Name</th>
                                <th>Wastage Qty</th>
                                <th>Requisition Qty</th>
                                <th>Value</th>
                               
                            </tr>
                        </thead>
                        
                        <tbody>
                            
                            @if(sizeof($resultProduct) > 0)
                                @php
                                $serial = 1;
                                $totalwastageQty = 0;
                                $totalwastageValue = 0;
                                @endphp

                                @foreach($resultProduct as $products)
                                  
                                <tr>
                                    <th>{{ $serial }}</th>
                                    <input type="hidden" id="price{{$serial}}" name="price[]" value="{{ $products->price }}">
                                    <input type="hidden" id="category_id{{$serial}}" name="category_id[]" value="{{ $products->category_id }}">
                                    <input type="hidden" id="unit{{$serial}}" name="unit[]" value="{{ $products->unit }}">
                                    <input type="hidden" id="produuct_id{{$serial}}" name="produuct_id[]" value="{{ $products->id }}">
                                    <th>{{ $products->cname }}</th>
                                    <th>{{ $products->name }}</th>
                                    <th><input type="number" class="form-control" id="wastageQty{{$serial}}" name="wastageQty[]" maxlength="3" pattern="[1-9]" min="1" value="{{ $products->qty }}" style="width: 80px;" onkeyup="totalsWastagesFO();" readonly="">
                                    </th> 
                                    <th><input type="number" class="form-control" id="qty{{$serial}}" name="qty[]" maxlength="3" pattern="[1-9]" min="1" value="{{$products->qty}}" style="width: 80px;" onkeyup="wastageQty({{$serial}})" onmouseout="totalsFO();" readonly="">
                                    </th>
                                    <th><input type="number" class="form-control" id="value{{$serial}}" name="value[]" maxlength="10" value="{{ $products->qty*$products->price }}" style="width: 80px;" readonly=""></th>
                                                                      
                                </tr>
                                @php
                                $serial ++;

                                 $totalwastageQty +=$products->qty;
                                 $totalwastageValue +=$products->qty*$products->price;
                                @endphp

                                @endforeach
                           
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right; padding-top: 17px;" align="right">Total : </th>
                                <th><input type="text" class="form-control" id="totalWastage" name="totalWastage" readonly="" value="{{$totalwastageQty}}" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalQty" name="totalQty" readonly="" value="{{$totalwastageQty}}" style="width: 80px;"></th>
                                <th><input type="text" class="form-control" id="totalValue" name="totalValue" readonly="" value="{{$totalwastageValue}}" style="width: 80px;"></th>
                                
                               
                            </tr>
                        </tfoot>
                         @endif
                    </table>
                    <p></p>
                    <div class="row">
                        <div class="col-sm-10" style="text-align: right;">
                            &nbsp;
                        </div>

                        <div class="col-sm-2" style="text-align: right;">
                            <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                        </div>
                    </div>                    
            </div>
        </div>
    </div>
</div>
            </form> 

        </div>
    </section>
@endsection