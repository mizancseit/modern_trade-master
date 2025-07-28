@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION APPROVED MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Appproved 
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
                    <h2>REQUISITION APPROVED LIST</h2>   
								
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
                                <th>Aprvd By</th>
                                <th>Aprvd Date</th>
                                <th>Status</th>
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
							
						
							 @php					
								$reultUser  = DB::select("SELECT * from users WHERE id = '".$ReqRow->approved_by."'");
                             @endphp                  							
								
                                           
                            <tr>
                                <th>{{ $serial }}</th>
                            	
								
								<th>
                                    <a href="{{ URL('/reqBillDetails/'.$ReqRow->req_id) }}" title="Click To Details" target="_blank">
                                      {{ $ReqRow->display_name }}
                                    </a>
                                </th>
								
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
                                <th>{{ $reultUser[0]->display_name }}</th>
                                <th>{{ $ReqRow->approved_date }}</th>
								<th>{{ $ReqRow->req_status }}</th>			
       
							<th style="text-align: center;"> 
                                    <a href="{{ URL('/reqBilled/'.$ReqRow->req_id) }}" title="Click To Deliver" onClick="return confirm('Are you sure to Billed?')">
                                        Ready for Billed
                                    </a>   
									
                                </th>
							    
								
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