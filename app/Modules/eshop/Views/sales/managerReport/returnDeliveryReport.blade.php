@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Delivery 
                        </small>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('success'))
        <div class="alert alert-success">
        {{ Session::get('success') }}                        
        </div>
    @endif

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Return Order Report </h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromDate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>
							<div class="col-sm-2">
								<select id="executive_id" name="executive_id" class="form-control show-tick" onchange="allOfficer(this.value)"> 
									<option value="">-- Select Executive--</option>
									@foreach($executivelist as $row)
										<option value="{{ $row->id }}">{{$row->display_name }}</option>
									@endforeach                                                    
								</select>
							</div>
                            <div class="col-sm-3" id="officerDiv">
                                <select id="fos" name="fos" class="form-control show-tick" data-live-search="true" onchange="allCustomer(this.value)">
                                   <option value="">-- Select Officer--</option> 
                                      @foreach($officerlist as $row)
                                          <option value="{{ $row->id }}">{{ $row->email.' : '.$row->display_name }}</option>
                                      @endforeach 
                                                                                  
                                </select>
                            </div> 
                            <div class="col-sm-3" id="customerDiv">
                                <select id="customer_id" class="form-control show-tick" data-live-search="true" required="">
                                    <option value="">-- Select Customer--</option> 
                                                                                       
                                </select>
                            </div>
                            
                         </div>
                        <div class="row">
							<div class="col-sm-2">
								<button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="managerReturnDeliveryReport()">Search</button>
							</div>						
							<div class="col-sm-2">                        
							   <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
							</div>
						</div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                 
            </div>
            
        </div>
    </div>
</section>

@endsection