@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION Billed MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Billed 
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
                    <h2>REQUISITION Billed LIST</h2>   
								
                </div>
                
                <div class="body">
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Depot Name</th>
								<th>Point Name</th>
								<th>Req No </th>
                                <th>Date</th>
                                <th>Billed</th>
								 <th>Date</th>
                                <th>Status</th>
                                
								<!-- <th>Challan</th> -->
                               
							    
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultBilledList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($resultBilledList as $ReqRow)
                                           
                            <tr>
                                <th>{{ $serial }}</th>
                                
								<th>
                                    <a href="{{ URL('/reqOpenOrderList/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                     {{ $ReqRow->display_name }}
                                    </a>
                                </th>
								
								@php					
								$reultApp  = DB::select("SELECT * from users WHERE id = '".$ReqRow->approved_by."'");
								$reultDel  = DB::select("SELECT * from users WHERE id = '".$ReqRow->delivered_by."'");
                             @endphp
								
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $reultApp[0]->display_name }}</th>
                                <th>{{ $ReqRow->billed_date }}</th>
								<th>{{ strtoupper($ReqRow->req_status) }}</th>		

								<!-- 
								<th>
                                    <a href="{{ URL('/reqDeliveryChallan/'.$ReqRow->req_id) }}" target="_blank" title="Click To View Delivery Challan">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>								
								</th>
								-->
								
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