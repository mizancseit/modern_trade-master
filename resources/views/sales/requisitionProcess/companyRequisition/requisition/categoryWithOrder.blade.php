@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 style="padding-top: 30px;">
                            NEW REQUISITION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition / New Requisition
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-3">                        
                        <div class="info-box-2 bg-red">
                            <div class="icon">
                                <i class="material-icons">shopping_cart</i>
                            </div>
                            <a href="{{ URL('/dist/req-bucket/'.$req_id) }}" style="text-decoration: none;" title="Click To Bucket Details">
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
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2>@if(sizeof($resultReqList)>0) {{ $resultReqList[0]->display_name }} @else Depot in Charge @endif </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST">
                                <select id="categories" class="form-control show-tick" onchange="allRequisitionProductsM()" data-live-search="true">
                                    <option value="">-- Please select category --</option>
                                    @foreach($resultCategory as $categories)
                                    <option value="{{ $categories->id }}">{{ $categories->g_code.' : '.$categories->name }}</option>
                                    @endforeach                           
                                </select>
                                {{-- <select class="form-control show-tick">
                                    <option value="">-- Please select subcategory--</option>             
                                </select> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ URL('/dist/req-add-to-product') }}" method="POST">
                {{ csrf_field() }}    <!-- token -->

                <input type="hidden" id="req_id" name="req_id" value="{{ $req_id }}">
             
                <div id="showHiddenDiv">                        
                    {{-- Here Product List --}}
                </div>
            </form> 

        </div>
    </section>
@endsection