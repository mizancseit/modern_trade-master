@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2> 
                        Inventory Report  
                       <small> 
                           <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Inventory Report  
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
        
        <div id="showHiddenDiv">
            <div class="card">
                <div class="header"> 
                    <div class="row">
                                                                          
                        <div class="col-lg-8">
                            <h4>Inventory List</h4>
                        </div>
                        <div class="col-lg-4">
                            <a class="btn btn-warning" href="{{ URL('/mts-download-stock-report') }}">Download Stock Report</a>
                      </div>
                    </div>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th style="text-align: center">SL</th>
                                    <th style="text-align: center">SAP_CODE</th>
                                    <th style="text-align: center">In Stock</th>
                                    <th style="text-align: center">Out Stock</th>
                                    <th style="text-align: center">Current Stock</th> 
                                </tr>
                            </thead>                            
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @if (count($stocks) > 0) 
                                @foreach ($stocks as $stock )
                                @php
                                    $i++;
                                @endphp
                                <tr>
                                    <td style="text-align: center">{{$i}}</td>
                                    <td style="text-align: center">{{$stock->sap_code}}</td>
                                    <td style="text-align: center">{{$stock->in_qty}}</td>
                                    <td style="text-align: center">{{$stock->out_qty}}</td>
                                    <td style="text-align: center">{{$stock->stock_qty}}</td>
                                </tr>
                                @endforeach                                
                                @else
                                <tr>
                                    <th colspan="4" style="text-align: center">No record found.</th>
                                </tr>
                                @endif
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
<script type="text/javascript">
     
     
   </script>
   @endsection
