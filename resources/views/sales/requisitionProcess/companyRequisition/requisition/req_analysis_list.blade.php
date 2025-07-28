@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION ANALYSIS
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Analysis 
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
                    <h2>REQUISITION ACKNOWLEDGE LIST</h2> 
								
                </div>
                
                <div class="body">
				    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
						
						<tr>
                                <th colspan="3">Distributor Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->display_name; ?></th>
								<th colspan="3">Point Name :&nbsp; <?php if(sizeof($resultReqList)) echo $resultReqList[0]->point_name; ?></th>
							</tr>
						
                            <tr>
                                <th>SL</th>
							    <th>Requisition No</th>
                                <th>Requisition Date</th>
                                <th>Acknowledge By</th>
                                <th>Acknowledge Date</th>
                                <th>Requisition Status</th>
                                <th>Action</th>
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
                                    <a href="{{ URL('/dist/reqDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                       {{ $ReqRow->req_no }}
                                    </a>
                                </th>
								
								@php					
								
								$reultAck  = DB::select("SELECT * from users WHERE id = '".$ReqRow->acknowledge_by."'");
                             @endphp
								
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $reultAck[0]->display_name }}</th>
                                <th>{{ $ReqRow->acknowledge_date }}</th>
								<th>{{ $ReqRow->req_status }}</th>
								<th> 
									<a href="{{ URL('/dist/reqOrderAnalysis/'.$ReqRow->req_id) }}" target="_blank" title="Click To Analysis">
                                        Analysis
                                    </a> || 
									
									<a href="{{ URL('/dist/reqCanceled/'.$ReqRow->req_id) }}" target="_blank" title="Click To Cancel">
                                        Cancel
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