@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-10">
                    <h2>
                        WASTAGE REQUISITION MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Wastage Requisition Manage 
                        </small>
                    </h2>
                </div>

                <div class="col-lg-2">
                    <div class="input-group">
                          @if($countReq == 0) 
                            <a href="{{ URL('/dist/was-req-add') }}">
                                <button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">New Requisition</button>
                            </a>
                         @endif
                    </div>
                      
                </div>

                </div>
            </div>
        </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>REQUISITION NEW LIST</h2>
                </div>
                
                <div class="body">                    
					<table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
								<th>Distributor Name</th>
								<th>Point Name</th>
                                <th>Requisition No</th>
                                <th>Requisition Date</th>
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
                                    <a href="{{ URL('/dist/was-req-bucket/'.$ReqRow->req_id) }}" title="Click To Edit Invoice" target="_blank">
                                        {{ $ReqRow->display_name }}
                                    </a>
                                </th>
                                <th>{{ $ReqRow->point_name }}</th>
                                <th>{{ $ReqRow->req_no }}</th>
                                <th>{{ $ReqRow->req_date }}</th>
								<th>{{ $ReqRow->req_status }}</th>			

								<th>
								
								<div class="icon">
									
									 <a href="{{ URL('/dist/was-req-send/'.$ReqRow->req_id) }}" title="Click To Send" onClick="return confirm('Are you sure to send?')">
                                       &nbsp;&nbsp; <i class="material-icons">send</i>
                                    </a>
								</div>
                                   
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