@extends('eshop::masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION MONITOR
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Monitor 
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
                    <h2>REQUISITION Waiting LIST</h2>   
								
                </div>
                
                <div class="body">
						
					
					<div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
								<form action="{{ URL('/req-manage') }}" method="get">
									<div class="form-line">
									   <button type="submit" name="submit" class="btn btn-link waves-effect">Requisition List</button>
									</div>
								</form>	
                            </div>
                        </div>
					</div>	
                    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
							
							<tr>
                                <th colspan='2'>Depo Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->display_name; ?></th>
								<th colspan='2'>Point Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->point_name; ?></th>
							</tr>	
						
                            <tr>
                                <th>SL</th>
							    <th>Requisition No</th>
                                <th>Requisition Date</th>
                                <th>Total Req Order</th>
                                <th>Total Req Value</th>
                                <th>Requisition Status</th>
                               
							    
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
                                        {{ $ReqRow->req_no }}
                                    </a>
                                </th>
                               
                               <?php 
							   
							    $ReqTot = DB::select("SELECT SUM(req_qnty) as sum_req_qnty, SUM(req_value) as sum_req_value  
								FROM depot_req_details WHERE req_id = '".$ReqRow->req_id."'");
							   ?>							   
							   
								<th>{{ $ReqRow->req_date }}</th>								
                                <th>{{ $ReqTot[0]->sum_req_qnty }}</th>                                
								<th>{{ $ReqTot[0]->sum_req_value }}</th>
								<th> Sent </th>
                            </tr>
							
                            @php
                            $totalQty +=$ReqTot[0]->sum_req_qnty;
                            $totalValue +=$ReqTot[0]->sum_req_value;
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="3" style="text-align: right;"> Total : </th>
                                <th> {{ $totalQty }} </th>
                                <th> {{ $totalValue }} </th>
                                <th>  </th>
                            </tr>

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