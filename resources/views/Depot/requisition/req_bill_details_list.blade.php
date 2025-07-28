@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Requisition Approved Deatils
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Req Approved List </a>
                            </small>
                        </h2>
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
                            <h2>ALL Approved PRODUCT</h2>                            
                        </div>
                        <div class="body">
                            <table class="table table-bordered">
                                    <thead>
										<tr>
                                            <th colspan="2">Depot Name: &nbsp;{{ $resultReqPro[0]->depot_in_charge }}</th>                  
                                            <th colspan="3">Point Name: &nbsp;{{ $resultReqPro[0]->point_name }}</th>                  
                                            <th colspan="2">Status: &nbsp; Approved</th>                  
                                        </tr>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
										    <th>Req Qty</th>
                                            <th>Req Value</th>
											<th>Apprvd Qty</th>
                                            <th>Apprvd Value</th>
								        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                        

                                       
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalValue = 0;
                                        $approvedQty = 0; 
                                        $approvedValue = 0;
                                        $billedQty = 0;
                                        $billedValue = 0; 
                                        @endphp
                                        @foreach($resultReqPro as $items)  

                                        @php
                                        $totalQty += $items->req_qnty;   
                                        $totalValue += $items->req_value;           
                                        $approvedQty += $items->approved_qnty; 
                                        $approvedValue += $items->approved_value;
                                        $billedQty += $items->billed_qnty;
                                        $billedValue += $items->billed_value;                    
                                        @endphp                                                              
                                        
										<tr>
                                            <th></th>
                                            <th colspan="9"></th>
                                        </tr>

                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->req_qnty }}</th>
												<th style="text-align: right;">{{ $items->req_value }}</th>
												<th style="text-align: right;">{{ $items->approved_qnty }}</th>
												<th style="text-align: right;">{{ $items->approved_value }}</th>
									        </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
                                        </tr>
                                        
                                        @endforeach
                                        
                                       <tr>
                                            <th colspan="3" style="text-align: right;">Grand Total : </th>
                                            
                                            <th style="text-align: right;">{{ $totalQty }}</th>
                                            <th style="text-align: right;">{{ $totalValue }}</th>
                                            <th style="text-align: right;">{{ $approvedQty }}</th>
                                            <th style="text-align: right;">{{ $approvedValue }}</th>
                                        </tr>
                                       
                                    </tbody>
                                    <tfoot>
                                       

                                   
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