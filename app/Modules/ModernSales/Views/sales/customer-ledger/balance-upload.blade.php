@extends('ModernSales::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">
                        <h2>
                            <small>
                                <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Customer Last Balance
                            </small>
                        </h2>
                    </div>
                    <div class="col-lg-2">
                        <a href="{{ url('download-customers') }}"><button class="btn btn-primary btn-lg"> File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button" id="ref" class="btn btn-primary btn-lg" data-toggle="modal"
                            data-target="#defaultModal1">Balance Upload</button>
                    </div>
                </div>
            </div>
        </div>

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Customer Last Balance </h2>
                    </div>

                    <div class="body">
                        <div class="row">

                            <div class="col-sm-2">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" name="fromdate" id="fromdate" class="form-control"
                                            value="{{ date('d-m-Y') }}" placeholder="Select To Date" readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <div class="form-line">
                                        <input type="text" name="toDate" id="todate" class="form-control"
                                            value="{{ date('d-m-Y') }}" placeholder="Select To Date" readonly="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <button type="button" class="btn bg-pink btn-block btn-lg waves-effect"
                                    onclick="customerBalanceList()">Search</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <img src="{{ URL::asset('resources/sales/images/loading.gif') }}" id="loadingTimeMasud"
                                    style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">

                    <div class="body">
                        <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog"> 
                            <form action="{{ URL('/customer-last-balance-upload-process') }}" method="post"
                                enctype="multipart/form-data">
                                {{ csrf_field() }} <!-- token -->
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #A62B7F">
                                            <h4 class="modal-title" style="color:white;" id="defaultModalLabel">Customer Last Balance Upload</h4>
                                        </div>
                                        <div class="modal-body">

                                            <div class="row clearfix">
                                                <div class="col-sm-12 col-md-12">

                                                    <div class="col-sm-6 col-md-4">
                                                        <label for="qty">Last Balance Upload : *</label>

                                                    </div>
                                                    <div class="col-sm-6 col-md-8">
                                                        <div class="form-group ">
                                                            <input type="file" class="form-control" name="imported-file"
                                                                required="" />
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="submit"
                                                class="btn btn-link waves-effect">UPLOAD</button>
                                            <button type="button" class="btn btn-link waves-effect"
                                                data-dismiss="modal">CLOSE</button>
                                        </div>
                            </form>
                        </div>
                    </div>
                </div>
                <br>

                <div id="showHiddenDiv">

                    <div class="card">
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                    <thead>
                                        <tr>
                                            <th>SL</th> 
                                            <th>Customer Name</th>
                                            <th>Upload Date</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                        $i =1;  
                                        @endphp
                                        @if(sizeof($balance_list) > 0)   
                                        @foreach ($balance_list as $balance ) 
                                        <tr>
                                            <td>{{$i++ }}</td> 
                                            <td>{{$balance->name}}</td>
                                            <td>{{ date('d-m-Y',strtotime($balance->submitted_date))}}</td>
                                            <td>{{$balance->last_balance}}</td> 
                                        </tr> 
                                        @endforeach 
                                        @else
                                        <tr>
                                            <th colspan="9">No record found.</th>
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

        <script>
            function customerBalanceList() {
                //alert(id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var fromdate = document.getElementById('fromdate').value;
                var todate = document.getElementById('todate').value; 
                var customer_id = 0; //document.getElementById('customer_id').value; 

                document.getElementById('loadingTimeMasud').style.display = 'inline';
                $.ajax({
                        method: "get",
                        url: '{{url('/customer-last-balance-filter')}}', 
                        data: { 
                            customer_id: customer_id,
                            todate: todate,
                            fromdate: fromdate
                        }
                    })
                    .done(function(response) {
                        document.getElementById('loadingTimeMasud').style.display = 'none';
                        $('#showHiddenDiv').html(response);
                    });
            }
        </script>
    </section>
@endsection
