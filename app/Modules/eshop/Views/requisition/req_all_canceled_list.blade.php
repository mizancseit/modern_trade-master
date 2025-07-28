@extends('eshop::masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION Canceled MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Canceled 
                        </small>
                    </h2>
                </div>
                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>REQUISITION CANCELED LIST</h2>   
								
                </div>
                
                <div class="body">
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Depot Name</th>
								<th>Point Name</th>
                                <th>Req No</th>
                                <th>Req Date</th>
                                <th>Canceled By</th>
                                <th>Canceled Date</th>
                                <th>Status</th>
                               
							    
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
                                    <a href="{{ URL('/reqDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                      {{ $ReqRow->display_name }}
                                    </a>
                                </th>
								
								@php					
								$reultUser  = DB::select("SELECT * from users WHERE id = '".$ReqRow->canceled_by."'");
                             @endphp
								
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $reultUser[0]->display_name }}</th>
                                <th>{{ $ReqRow->canceled_date }}</th>
                                <th>{{ $ReqRow->req_status }}</th>			

								
                            </tr>
							
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                        @else
                            <tr>
                                <th colspan="8">No record found.</th>
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