@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION RECEIVED
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Received 
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
                    <h2>REQUISITION RECEIVED LIST</h2>   
								
                </div>
                
                <div class="body">
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
						
						<tr>
                                <th colspan="5">Distributor Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->display_name; ?></th>
								<th colspan="5">Point Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->point_name; ?></th>
							</tr>	
						
						
                            <tr>
                                <th>SL</th>
							    <th>Req No</th>
                                <th>Req Date</th>
                            	<th>Received By</th>
                                <th>Received Date</th>
                                <th>Status</th>
                                <th>Challan</th>
                                
                               
							    
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
                                    <a href="{{ URL('/dist/reqReceivedDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                      {{ $ReqRow->req_no }}
                                    </a>
                                </th>
							  
							  @php					
								
								$reultRcvd  = DB::select("SELECT * from users WHERE id = '".$ReqRow->received_by."'");
								
                             @endphp
							  
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $reultRcvd[0]->display_name }}</th>
                                <th>{{ $ReqRow->received_date }}</th>
								<th>{{ $ReqRow->req_status }}</th>		
								
								<th>

								<a href="{{ URL('/dist/reqDeliveryChallan/'.$ReqRow->req_id) }}" target="_blank" title="Click To View Delivery Challan">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>	
								</th>		

								
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