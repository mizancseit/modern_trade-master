@extends('eshop::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Partial delivery report 
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
                    <h2>Partial delivery report</h2>                            
                </div>             
                <!-- <div class="body">                    
                    <div class="row">                        
                        <div class="col-sm-2">
                          <div class="input-group">
                            <div class="form-line">
                              <input type="text" name="fromdate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
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
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="sqwisestock()">Search</button>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{url('eshop-stock-report-download/')}}" class="btn bg-pink btn-block btn-lg waves-effect" id="download-link">Download Report</a>
                            </div>
                        </div>
                        <div class="row">  
                        <div class="col-sm-2">                        
                           <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
                        </div>
                    </div>                                  
                </div> -->
            </div>

            <div id="showHiddenDiv">
                <div class="card"> 
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr> 
                                        <th>Order No</th>
                                        <th>Approved Qty</th>
                                        <th>Delivery qty</th>   
                                    </tr>
                                </thead>
                                <tbody> 
                                    @if(sizeof($stocks) > 0)   
                                        @php
                                        $serial =1;
                                        $totalQty = 0;
                                        $totalValue = 0;
                                        $totalinstock= 0;
                                        $totaloutstock=0;

                                        @endphp

                                        @foreach($stocks as $stock)
                                        @php
                                        $totalinstock += $stock->instock;
                                        $totaloutstock += $stock->hold_stock; 
                                        @endphp  
                                        @if( $stock->instock > 0)                  
                                        <tr>                     
                                            <td><a href="{{ URL('/eshop-partial-delivery/'.$stock->order_id) }}"> {{ $stock->order_no }} </a></td>  
                                            <td>{{ $stock->hold_stock }}  </td> 
                                            <td>{{ $stock->instock }} </td>         
                                             
                                        </tr>
                                        @endif
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
            
        </div>
    </div>
</section>
<script type="text/javascript"> 
    function sqwisestock(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                var downloadlink = '{{url('eshop-stock-report-download/')}}/'+fromdate+'/'+todate
                document.getElementById('loadingTimeMasud').style.display='inline';
                //jQuery('#download-link').val(downloadlink);
                document.getElementById("download-link").href= downloadlink; 
                //document.getElementById('download-link').value = downloadlink;

                $.ajax({
                    method: "POST",
                    url: '{{url('/eshop_summary_report_ajax')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            }         
        }
</script> 
@endsection