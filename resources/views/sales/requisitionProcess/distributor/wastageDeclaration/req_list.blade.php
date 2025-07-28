@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-10">
                    <h2>
                        WASTAGE DECLARATION MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Wastage Declaration Manage 
                        </small>
                    </h2>
                </div>

                <div class="col-lg-2">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                {{-- <form action="{{ URL('/fo/req-add') }}" method="get"> --}}
                                    <a href="{{ URL('/dist/was-declaration-add') }}">
                                        <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">New Declaration</button>
                                    </a>
                                {{-- </form>  --}}
                            </div>
                        </div>
                    </div>  
                </div>

                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>DECLARATION NEW LIST</h2>
                </div>
                
                <div class="body">                    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Distributor Name</th>
								<th>Point Name</th>
                                <th>Declaration No</th>
                                <th>Declaration Date</th>
							    
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultReqList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($resultReqList as $ReqRow)
                                           
                            <tr>
                                <th>{{ $serial }}</th>
                                <th>
                                    <a href="{{ URL('/dist/was-declaration-bucket/'.$ReqRow->order_id) }}" title="Click To Edit Invoice" target="_blank">
                                        {{ $ReqRow->display_name }}
                                    </a>
                                </th>
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->order_no }}</th>
                                <th>{{ $ReqRow->entry_date }}</th>
								<th></th>
                            </tr>
							
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                        @else
                            <tr>
                                <th colspan="7">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>

            </div>
        </div>
    </div>
</section>

@endsection