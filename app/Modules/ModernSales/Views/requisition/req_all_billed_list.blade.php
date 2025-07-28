@extends('ModernSales::masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION BILL MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Billed 
                        </small>
                    </h2>
                </div>
                <div class="col-lg-3">
                        <form action="{{url('modern-export-sales-order')}}" enctype="multipart/form-data">
                        <input type="submit" name="download" value="DOWNLOAD SALES ORDER" class="btn bg-red btn-block btn-sm waves-effect" style="width: 240px;">

                        <input type="hidden" name="requisition_id" value="">
                        </form>

                </div>
                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>REQUISITION BILLED LIST</h2>   
								
                </div>
                
                <div class="body">
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Customer Name</th>
								<th>Outlet Name</th>
								<th>Order No </th>
                                <th>Date</th>
                                <th>Billed</th>
								<th>Date</th>
                                <th>Status</th>
                                
								<!-- <th>Challan</th> -->
                               
							    
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
                                    <a href="{{ URL('/modernorder-details/'.$ReqRow->order_id) }}" title="Click To Details" target="_blank">
                                     {{ $ReqRow->name }}
                                    </a>
                                </th>
								
								@php					
								$reultApp  = DB::select("SELECT * from users WHERE id = '".$ReqRow->approved_by."'");
								$reultDel  = DB::select("SELECT * from users WHERE id = '".$ReqRow->billed_by."'");
                             @endphp
								
                                <th>{{ $ReqRow->partyName }}</th>
                                <th>{{ $ReqRow->order_no }}</th>
                                <th>{{ $ReqRow->order_date }}</th>
                                <th>{{ $reultApp[0]->display_name }}</th>
                                <th>{{ $ReqRow->billed_date }}</th>
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