@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            FREE REQUISITION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/dist/free-req-manage') }}"> Requisition </a> / <a href="{!! URL::previous() !!}"> New Requisition </a> / Bucket
                            </small>
                        </h2>
                    </div>

                    <div class="col-lg-3">
                        {{-- <a href="{{ URL('/dist/free-req-list-product/'.$reqid) }}">
                            <button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD NEW PRODUCT</button>
                        </a>  --}}                       
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

                        @if(sizeof($resultReqPro)>0)
                        <div class="header">
                            <h2>ALL REQUISITION PRODUCT</h2>                            
                        </div>
                        <div class="body">
                            <table class="table table-bordered">
                                    <thead>
										<tr>
                                            <th colspan="3">Distributor Name: &nbsp;{{ $resultReqPro[0]->distributor_in_charge }}</th>                  
                                            <th colspan="4">Point Name: &nbsp;{{ $resultReqPro[0]->point_name }}</th>                  
                                        </tr>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Req Free Qty</th>
                                            <th>Req Free Value</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                        

                                       
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalWastage = 0;
                                        @endphp
                                        @foreach($resultReqPro as $items)
                                        @php
                                            $totalQty += $items->req_qnty;
                                            $subTotal += $items->req_value;
                                            $totalWastage += $items->wastage_qty;
                                        @endphp                                      
                                        
										{{-- <tr>
                                            <th></th>
                                            <th colspan="6"></th>
                                        </tr> --}}

                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->req_qnty }}</th>
												<th style="text-align: right;">{{ $items->req_value }}</th>
                                                
                                                {{-- <th>
                                                    <input type="hidden" name="edit" id="edit" value="{{ $items->id }}">
                                                <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editFreeProductsM('{{ $items->id }}')" style="width: 70px;">
                                                
                                                <input type="button" name="delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteReqProductsM('{{ $items->id }}')" style="width: 70px; margin-top: 0px;">
                                                </th>  --}}                                          
                                            </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                        @endforeach                                       
                                       
                                       
                                    </tbody>
                                    <tfoot>
                                        <th colspan="3" style="text-align: right;"> Total </th>
                                        <th style="text-align: right;">{{ $totalQty }}</th>
                                        <th style="text-align: right;">{{ $subTotal }}</th>
                                       {{--  <th style="text-align: right;"> </th> --}}
                                    </tfoot>
                                       

                                   
                                </table>
                                <p></p>
                               
                                <input type="hidden" name="req_id" id="req_id" value="">
                               
                        </div>
                        @else
                        
                        <div class="header">
                            <h2>Product EMPTY</h2>                            
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </thead>
                            <tbody>                                        
                                <tr>
                                    <th colspan="7" style="color: #000; text-align: center;" align="center">
                                    <h4>YOUR Requisition PRODUCT IS EMPTY.</h4> <p></p><p></p>

                                    <div class="col-sm-4" style="margin-right: 40px;"></div>
                                   
                                    
                                     </th>                  
                                </tr>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </tbody>
                        </table>
                            
                        @endif
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>

   

@endsection